<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['perfume', 'oud', 'incense', 'home', 'gifts', 'dehen']);

        return [
            'parent_id' => null,
            'name' => ['en' => fake()->unique()->words(2, true), 'ar' => fake()->unique()->words(2, true)],
            'slug' => fake()->unique()->slug(2),
            'description' => ['en' => fake()->sentence(), 'ar' => fake()->sentence()],
            'image' => 'categories/'.fake()->uuid().'.jpg',
            'type' => $type,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }

    /**
     * Indicate the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
