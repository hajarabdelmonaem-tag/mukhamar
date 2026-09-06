<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('??????-##')),
            'discount_type' => fake()->randomElement(['percentage', 'fixed']),
            'discount_value' => fake()->randomElement([10, 15, 20, 25, 50, 100]),
            'min_subtotal' => fake()->boolean(60) ? 200 : null,
            'max_uses' => fake()->boolean(50) ? fake()->numberBetween(50, 500) : null,
            'used_count' => fake()->numberBetween(0, 100),
            'starts_at' => now()->subDays(fake()->numberBetween(1, 20)),
            'expires_at' => now()->addDays(fake()->numberBetween(1, 90)),
            'is_active' => true,
        ];
    }

    /**
     * Indicate the coupon is a percentage discount.
     */
    public function percentage(string $value = '15'): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_type' => 'percentage',
            'discount_value' => $value,
        ]);
    }

    /**
     * Indicate the coupon is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }
}
