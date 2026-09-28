<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => ['en' => fake()->sentence(4), 'ar' => fake()->sentence(4)],
            'description' => ['en' => fake()->paragraph(), 'ar' => fake()->paragraph()],
            'image' => 'banners/'.fake()->uuid().'.jpg',
            'link' => null,
            'position' => fake()->randomElement(['home', 'shop']),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }

    /**
     * Indicate the banner is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
