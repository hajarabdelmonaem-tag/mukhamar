<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Seed sample orders for demo users.
     */
    public function run(): void
    {
        // The flagship demo order from the "Order Details" mockup.
        $demoOrder = Order::factory()->delivered()->create([
            'order_no' => 'MKR-0982',
            'user_id' => User::where('email', 'n.alabdullah@example.com')->value('id'),
            'payment_method' => 'VISA',
            'payment_status' => 'paid',
            'subtotal' => 1100,
            'shipping' => 0,
            'discount' => 0,
            'tax' => 165,
            'total' => 1265,
            'placed_at' => now()->parse('2023-10-15 10:30:00'),
        ]);

        $products = Product::whereIn('slug', ['luxury-oud', 'damask-rose-dehen'])->get();
        foreach ($products as $product) {
            $variant = $product->variants()->where('price_adjustment', 0)->first();
            $demoOrder->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'product_name' => $product->name,
                'variant_name' => $variant->name,
                'quantity' => 1,
                'unit_price' => $product->price,
                'total' => $product->price,
            ]);
        }

        // A few extra orders per seeded customer.
        $customers = User::whereNotNull('email')
            ->where('email', 'not like', '%@mukhamar.sa')
            ->limit(6)
            ->get();

        foreach ($customers as $user) {
            Order::factory()
                ->has(OrderItem::factory()->count(mt_rand(1, 3)), 'items')
                ->create(['user_id' => $user->id]);
        }
    }
}
