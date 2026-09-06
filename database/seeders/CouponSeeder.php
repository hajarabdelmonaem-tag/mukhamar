<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Seed demo discount coupons used on the cart/checkout screens.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME15',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'min_subtotal' => 200,
                'max_uses' => 1000,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
            ],
            [
                'code' => 'WINTER30',
                'discount_type' => 'percentage',
                'discount_value' => 30,
                'min_subtotal' => 500,
                'max_uses' => 500,
                'starts_at' => now()->subDays(10),
                'expires_at' => now()->addDays(30),
            ],
            [
                'code' => 'SAVE50',
                'discount_type' => 'fixed',
                'discount_value' => 50,
                'min_subtotal' => 300,
                'max_uses' => 300,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(2),
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::query()->updateOrCreate(
                ['code' => $coupon['code']],
                $coupon
            );
        }
    }
}
