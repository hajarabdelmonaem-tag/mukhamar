<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 120, 1500);
        $hasOldPrice = fake()->boolean(25);

        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->words(3, true),
            'slug' => fake()->unique()->slug(3),
            'description' => fake()->paragraph(),
            'usage_instructions' => fake()->sentence(),
            'price' => $price,
            'old_price' => $hasOldPrice ? round($price * 1.25, 2) : null,
            'rating' => fake()->randomFloat(1, 3.5, 5),
            'reviews_count' => fake()->numberBetween(0, 450),
            'top_notes' => fake()->randomElement(['زعفران', 'برغموت', 'ليمون', 'هيل']),
            'heart_notes' => fake()->randomElement(['ورد طائفي', 'جاسمين', 'باتشولي']),
            'base_notes' => fake()->randomElement(['عود', 'مسك', 'عنبر']),
            'badges' => fake()->boolean(25) ? [fake()->randomElement(['جديد', 'خصم', 'الأكثر مبيعاً'])] : null,
            'is_featured' => fake()->boolean(20),
            'is_active' => true,
        ];
    }

    /**
     * Indicate the product is featured on the home page.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
