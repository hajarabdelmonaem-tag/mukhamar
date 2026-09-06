<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement([
                'تحديث الطلب',
                'عرض خاص لك',
                'تقييمك يهمنا',
            ]),
            'body' => fake()->sentence(8),
            'type' => fake()->randomElement(['order', 'offer', 'review']),
            'action_url' => fake()->boolean(50) ? '/orders' : null,
            'read_at' => fake()->boolean(40) ? now()->subHours(fake()->numberBetween(1, 24)) : null,
        ];
    }

    /**
     * Indicate the notification has been read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => now(),
        ]);
    }
}
