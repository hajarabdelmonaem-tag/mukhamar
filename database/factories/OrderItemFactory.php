<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);
        $quantity = fake()->numberBetween(1, 3);
        $unitPrice = round($product->price + $variant->price_adjustment, 2);

        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_name' => $variant->name,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => round($unitPrice * $quantity, 2),
        ];
    }
}
