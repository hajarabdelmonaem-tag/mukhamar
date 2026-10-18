<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Runs one customer message through the model and its tools, then stores
 * the whole exchange. History is only ever appended to, never edited, so
 * earlier turns (including thinking blocks) are sent back exactly as the
 * model produced them.
 */
class ChatbotService
{
    public function __construct(
        private ChatbotClient $client,
        private ChatbotToolsService $tools,
    ) {}

    /**
     * Whether the conversation has used up its allowed customer messages.
     */
    public function hasReachedTurnLimit(?ChatConversation $conversation): bool
    {
        return $conversation !== null
            && $conversation->visibleMessages()->where('role', ChatMessage::ROLE_USER)->count()
                >= config('services.chatbot.max_turns');
    }

    /**
     * Answer a customer message, creating the conversation when needed.
     *
     * @return array{conversation: ChatConversation, message: ChatMessage}
     */
    public function reply(string $text, ?ChatConversation $conversation, ?User $user): array
    {
        $conversation ??= new ChatConversation([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user?->id,
            'locale' => app()->getLocale(),
        ]);

        $history = $conversation->exists
            ? $conversation->messages->map(fn (ChatMessage $message): array => [
                'role' => $message->role,
                'content' => $message->content,
            ])->all()
            : [];

        $newTurns = [[
            'role' => ChatMessage::ROLE_USER,
            'content' => [['type' => 'text', 'text' => $text]],
            'text' => $text,
        ]];

        $system = $this->systemPrompt($user);
        $definitions = $this->tools->definitions();
        $maxRounds = (int) config('services.chatbot.max_tool_rounds');

        for ($round = 0; ; $round++) {
            $response = $this->client->send(
                $system,
                $definitions,
                [...$history, ...array_map(self::forModel(...), $newTurns)],
                allowTools: $round < $maxRounds,
            );

            $assistantTurn = [
                'role' => ChatMessage::ROLE_ASSISTANT,
                'content' => $response['content'],
                'text' => self::textOf($response['content']),
                'input_tokens' => $response['input_tokens'],
                'output_tokens' => $response['output_tokens'],
            ];

            $toolsExhausted = $response['stop_reason'] === 'tool_use' && $round >= $maxRounds;

            if ($toolsExhausted || ! self::isUsable($response)) {
                return [
                    'conversation' => $conversation,
                    'message' => new ChatMessage([
                        'role' => ChatMessage::ROLE_ASSISTANT,
                        'text' => __('api.chatbot.cannot_help'),
                    ]),
                ];
            }

            $newTurns[] = $assistantTurn;

            if ($response['stop_reason'] !== 'tool_use') {
                break;
            }

            $newTurns[] = [
                'role' => ChatMessage::ROLE_USER,
                'content' => $this->runTools($response['content'], $user),
                'text' => null,
            ];
        }

        $reply = $this->store($conversation, $user, $newTurns);

        return ['conversation' => $conversation, 'message' => $reply];
    }

    /**
     * Run every tool call in a turn and build the matching tool results.
     *
     * @param  list<array<string, mixed>>  $content
     * @return list<array<string, mixed>>
     */
    private function runTools(array $content, ?User $user): array
    {
        $results = [];

        foreach ($content as $block) {
            if (($block['type'] ?? null) !== 'tool_use') {
                continue;
            }

            try {
                $result = $this->tools->execute($block['name'], (array) ($block['input'] ?? []), $user);
            } catch (\Throwable $exception) {
                report($exception);
                $result = ['error' => 'The tool failed. Apologise and offer to contact the support team.'];
            }

            $results[] = [
                'type' => 'tool_result',
                'tool_use_id' => $block['id'],
                'content' => (string) json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ...(isset($result['error']) ? ['is_error' => true] : []),
            ];
        }

        return $results;
    }

    /**
     * Save the new turns and return the final assistant reply.
     *
     * @param  list<array<string, mixed>>  $turns
     */
    private function store(ChatConversation $conversation, ?User $user, array $turns): ChatMessage
    {
        return DB::transaction(function () use ($conversation, $user, $turns): ChatMessage {
            $conversation->fill([
                'user_id' => $conversation->user_id ?? $user?->id,
                'last_message_at' => now(),
            ])->save();

            $reply = null;

            foreach ($turns as $turn) {
                $reply = $conversation->messages()->create($turn);
            }

            return $reply;
        });
    }

    /**
     * The system prompt: a fixed block first (cacheable), then a short
     * block describing the current customer.
     *
     * @return list<array{type: string, text: string}>
     */
    private function systemPrompt(?User $user): array
    {
        $store = config('services.chatbot.store_name');

        $instructions = <<<PROMPT
        You are the customer assistant for {$store}, an online perfume store in Saudi Arabia, chatting with customers inside the store's mobile app.

        You help customers find perfumes (including best sellers and products on sale), explain products (fragrance notes, sizes, usage, customer reviews), check coupon codes they give you, share the store's contact details, and answer questions about shipping, tax, payment methods and store policies. For logged-in customers you can also look up their orders, cart, wishlist, saved addresses, notifications and account details.

        How to answer:
        - Get every fact about products, prices, sizes, stock, orders, shipping, tax, payments and policies from the tools. Never guess or invent any of these. If the tools don't return something, say you don't have that information and offer to pass the question to the support team.
        - When recommending perfumes, search with short keywords (a note like oud, rose or musk, or a style) and suggest at most three products, with their price and why they fit.
        - Reply in the language the customer writes in (Arabic or English). Use clear, warm, simple Arabic for Arabic speakers.
        - Keep replies short enough for a phone screen. Use plain text with short lines; no markdown tables or headings.
        - Order, cart, wishlist, address, notification and account tools only work for logged-in customers. If a guest asks about these, ask them to log in to the app first.
        - Only check coupon codes the customer typed. Never reveal or suggest coupon codes on your own.
        - Before using contact_support, confirm with the customer what will be sent. For guests, also ask for their name and phone number.
        - You cannot place orders, change carts, cancel orders or issue refunds. For those, explain how to do it in the app or offer to contact the support team.
        - Only help with topics related to the store and its products. Politely decline anything else.
        PROMPT;

        $customer = $user
            ? 'The customer is logged in. Their name is "'.mb_substr((string) $user->name, 0, 50).'".'
            : 'The customer is a guest (not logged in).';

        return [
            ['type' => 'text', 'text' => $instructions],
            ['type' => 'text', 'text' => $customer.' App language: '.app()->getLocale().'.'],
        ];
    }

    /**
     * Strip storage-only fields from a turn before sending it to the model.
     *
     * @param  array<string, mixed>  $turn
     * @return array{role: string, content: list<array<string, mixed>>}
     */
    private static function forModel(array $turn): array
    {
        return ['role' => $turn['role'], 'content' => $turn['content']];
    }

    /**
     * Whether a model turn can be kept in the conversation history.
     *
     * A refusal, an empty turn, or a tool call cut off by the token limit
     * would leave the history invalid, so such exchanges are not stored.
     *
     * @param  array{content: list<array<string, mixed>>, stop_reason: ?string}  $response
     */
    private static function isUsable(array $response): bool
    {
        if ($response['stop_reason'] === 'refusal' || $response['content'] === []) {
            return false;
        }

        $lastBlock = end($response['content']);

        if (($lastBlock['type'] ?? null) === 'tool_use' && $response['stop_reason'] !== 'tool_use') {
            return false;
        }

        return $response['stop_reason'] === 'tool_use' || self::textOf($response['content']) !== null;
    }

    /**
     * Join the text blocks of a turn, or null when it has none.
     *
     * @param  list<array<string, mixed>>  $content
     */
    private static function textOf(array $content): ?string
    {
        $text = trim(collect($content)
            ->where('type', 'text')
            ->pluck('text')
            ->implode("\n"));

        return $text === '' ? null : $text;
    }
}
