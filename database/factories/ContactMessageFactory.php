<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->boolean(60) ? fake()->numerify('+966 5# ### ####') : null,
            'subject' => fake()->boolean(70) ? fake()->sentence() : null,
            'message' => fake()->paragraph(),
            'is_read' => fake()->boolean(25),
        ];
    }
}
