<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;

class ChatConversationController extends Controller
{
    /**
     * Display a listing of chatbot conversations.
     */
    public function index()
    {
        $conversations = ChatConversation::query()
            ->with('user')
            ->withCount(['messages as customer_messages_count' => fn ($query) => $query
                ->where('role', ChatMessage::ROLE_USER)
                ->whereNotNull('text')])
            ->withSum('messages', 'input_tokens')
            ->withSum('messages', 'output_tokens')
            ->latest('last_message_at')
            ->paginate(15);

        return view('admin.chat_conversations.index', [
            'title' => __('admin.chat_conversations.title'),
            'conversations' => $conversations,
        ]);
    }

    /**
     * Display the transcript of a conversation.
     */
    public function show(ChatConversation $conversation)
    {
        $conversation->load('user');

        return view('admin.chat_conversations.show', [
            'title' => __('admin.chat_conversations.title'),
            'conversation' => $conversation,
            'messages' => $conversation->visibleMessages()->get(),
            'inputTokens' => (int) $conversation->messages()->sum('input_tokens'),
            'outputTokens' => (int) $conversation->messages()->sum('output_tokens'),
        ]);
    }

    /**
     * Remove the specified conversation.
     */
    public function destroy(ChatConversation $conversation)
    {
        $conversation->delete();

        return redirect()->route('admin.chat-conversations.index')
            ->with('success', __('admin.chat_conversations.deleted'));
    }
}
