<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => ['en' => fake()->randomElement(['50ml', '100ml', '200ml', 'Full Tola', 'Quarter Tola', '12g']), 'ar' => fake()->randomElement(['50مل', '100مل', '200مل', 'تولة كاملة', 'ربع تولة', '12جرام'])],
            'unit' => ['en' => fake()->randomElement(['ml', 'g', 'tola']), 'ar' => fake()->randomElement(['مل', 'جرام', 'تولة'])],
            'price_adjustment' => fake()->randomElement([0, 50, 150, 300]),
            'sku' => fake()->unique()->bothify('SKU-####-????'),
            'stock' => fake()->numberBetween(0, 200),
            'is_default' => fake()->boolean(40),
            'is_active' => true,
        ];
    }

    /**
     * Indicate the variant is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }
}
