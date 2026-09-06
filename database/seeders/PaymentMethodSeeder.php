<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Seed the payment methods supported by the application.
     */
    public function run(): void
    {
        $methods = [
            [
                'code' => 'VISA',
                'name' => ['en' => 'Visa', 'ar' => 'فيزا'],
                'sort_order' => 1,
            ],
            [
                'code' => 'MADA',
                'name' => ['en' => 'Mada', 'ar' => 'مدى'],
                'sort_order' => 2,
            ],
            [
                'code' => 'Apple Pay',
                'name' => ['en' => 'Apple Pay', 'ar' => 'أبل باي'],
                'sort_order' => 3,
            ],
            [
                'code' => 'card',
                'name' => ['en' => 'Card', 'ar' => 'بطاقة ائتمان'],
                'sort_order' => 4,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::query()->updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}
