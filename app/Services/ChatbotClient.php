<?php

namespace App\Services;

/**
 * A language model backend that the chatbot sends conversation turns to.
 */
interface ChatbotClient
{
    /**
     * Send the conversation and return the model's next turn.
     *
     * @param  list<array{type: string, text: string}>  $system
     * @param  list<array<string, mixed>>  $tools
     * @param  list<array{role: string, content: string|list<array<string, mixed>>}>  $messages
     * @return array{content: list<array<string, mixed>>, stop_reason: ?string, input_tokens: int, output_tokens: int}
     */
    public function send(array $system, array $tools, array $messages, bool $allowTools = true): array;
}
