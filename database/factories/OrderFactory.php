<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 200, 3000);
        $discount = fake()->boolean(30) ? fake()->randomFloat(2, 0, 150) : 0;
        $shipping = fake()->boolean(70) ? 0 : 30;
        $tax = round(($subtotal - $discount) * 0.15, 2);
        $total = round($subtotal - $discount + $shipping + $tax, 2);

        return [
            'order_no' => 'MKR-'.fake()->unique()->numberBetween(1000, 9999),
            'user_id' => User::factory(),
            'address_id' => Address::factory(),
            'coupon_id' => null,
            'status' => fake()->randomElement([
                Order::STATUS_PENDING,
                Order::STATUS_PROCESSING,
                Order::STATUS_IN_TRANSIT,
                Order::STATUS_DELIVERED,
                Order::STATUS_CANCELLED,
            ]),
            'payment_method' => fake()->randomElement(['VISA', 'MADA', 'Apple Pay']),
            'payment_status' => fake()->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
            'placed_at' => now()->subDays(fake()->numberBetween(1, 60)),
        ];
    }

    /**
     * Indicate the order has been delivered.
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_DELIVERED,
            'confirmed_at' => now()->subDays(6),
            'shipped_at' => now()->subDays(3),
            'delivered_at' => now()->subDay(),
        ]);
    }
}
