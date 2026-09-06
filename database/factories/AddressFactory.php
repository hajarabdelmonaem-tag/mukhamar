<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
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
            'label' => fake()->randomElement(['home', 'work', 'other']),
            'recipient_name' => fake()->name(),
            'street' => fake()->streetName(),
            'district' => fake()->randomElement(['حي الملقا', 'العليا', 'الرياض']),
            'city' => 'الرياض',
            'building' => fake()->randomElement(['مبنى 45', 'برج الفيصلية', 'شقة 12']),
            'postal_code' => fake()->numerify('#####'),
            'phone' => fake()->numerify('+966 5# ### ####'),
            'is_default' => false,
        ];
    }

    /**
     * Mark the address as the default shipping address.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
