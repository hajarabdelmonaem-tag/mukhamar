<?php

namespace App\Http\Controllers\Api\V1;

use Anthropic\Core\Exceptions\AnthropicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SendChatMessageRequest;
use App\Http\Resources\Api\V1\ChatMessageResource;
use App\Models\ChatConversation;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(private ChatbotService $chatbot) {}

    /**
     * Send a customer message to the assistant and return its reply.
     * Works for guests and, with a bearer token, for logged-in customers.
     * The reply (and the product data it uses) follows the language the
     * message is written in, falling back to the app locale.
     */
    public function send(SendChatMessageRequest $request): JsonResponse
    {
        if (! config('services.chatbot.enabled')) {
            return response()->json(['message' => __('api.chatbot.disabled')], 503);
        }

        app()->setLocale(ChatbotService::languageOf($request->message) ?? app()->getLocale());

        $user = $request->user('sanctum');

        $conversation = $request->conversation_id
            ? ChatConversation::query()->where('uuid', $request->conversation_id)->first()
            : null;

        abort_if($conversation && ! $conversation->isAccessibleBy($user), 403);

        if ($this->chatbot->hasReachedTurnLimit($conversation)) {
            return response()->json(['message' => __('api.chatbot.turn_limit')], 422);
        }

        try {
            $result = $this->chatbot->reply($request->message, $conversation, $user);
        } catch (AnthropicException $exception) {
            report($exception);

            return response()->json(['message' => __('api.chatbot.unavailable')], 503);
        }

        return response()->json([
            'data' => [
                'conversation_id' => $result['conversation']->uuid,
                'reply' => new ChatMessageResource($result['message']),
            ],
        ]);
    }

    /**
     * Show the visible messages of a conversation.
     */
    public function show(Request $request, ChatConversation $conversation): JsonResponse
    {
        abort_unless($conversation->isAccessibleBy($request->user('sanctum')), 403);

        return response()->json([
            'data' => [
                'conversation_id' => $conversation->uuid,
                'messages' => ChatMessageResource::collection($conversation->visibleMessages()->get()),
            ],
        ]);
    }
}
