<?php

namespace Database\Factories;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $text = fake()->sentence();

        return [
            'chat_conversation_id' => ChatConversation::factory(),
            'role' => ChatMessage::ROLE_USER,
            'content' => [['type' => 'text', 'text' => $text]],
            'text' => $text,
        ];
    }

    /**
     * A reply written by the assistant.
     */
    public function assistant(): static
    {
        return $this->state(fn (): array => ['role' => ChatMessage::ROLE_ASSISTANT]);
    }
}
