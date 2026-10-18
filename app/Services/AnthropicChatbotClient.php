<?php

namespace App\Services;

use Anthropic\Client;

/**
 * Sends chatbot turns to Claude through the official Anthropic SDK.
 */
class AnthropicChatbotClient implements ChatbotClient
{
    public function __construct(private Client $client) {}

    /**
     * Send the conversation and return the model's next turn.
     *
     * @param  list<array{type: string, text: string}>  $system
     * @param  list<array<string, mixed>>  $tools
     * @param  list<array{role: string, content: string|list<array<string, mixed>>}>  $messages
     * @return array{content: list<array<string, mixed>>, stop_reason: ?string, input_tokens: int, output_tokens: int}
     */
    public function send(array $system, array $tools, array $messages, bool $allowTools = true): array
    {
        $response = $this->client->beta->messages->create(
            maxTokens: config('services.anthropic.max_tokens'),
            messages: array_map(self::prepareMessage(...), $messages),
            model: config('services.anthropic.model'),
            cacheControl: ['type' => 'ephemeral'],
            fallbacks: 'default',
            outputConfig: ['effort' => config('services.anthropic.effort')],
            system: $system,
            toolChoice: $allowTools ? ['type' => 'auto'] : ['type' => 'none'],
            tools: $tools,
            betas: ['server-side-fallback-2026-07-01'],
        );

        return [
            'content' => array_map(
                fn ($block): array => array_filter(
                    json_decode((string) json_encode($block), true),
                    fn ($value): bool => $value !== null,
                ),
                $response->content,
            ),
            'stop_reason' => $response->stopReason,
            'input_tokens' => $response->usage->inputTokens
                + (int) $response->usage->cacheReadInputTokens
                + (int) $response->usage->cacheCreationInputTokens,
            'output_tokens' => $response->usage->outputTokens,
        ];
    }

    /**
     * Restore JSON objects that became empty PHP arrays after storage
     * (an empty tool input must be sent as {} rather than []).
     *
     * @param  array{role: string, content: string|list<array<string, mixed>>}  $message
     * @return array{role: string, content: string|list<array<string, mixed>>}
     */
    private static function prepareMessage(array $message): array
    {
        if (is_array($message['content'])) {
            $message['content'] = array_map(function (array $block): array {
                if (($block['type'] ?? null) === 'tool_use' && empty($block['input'])) {
                    $block['input'] = new \stdClass;
                }

                return $block;
            }, $message['content']);
        }

        return $message;
    }
}
