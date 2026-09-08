<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            IntroSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            CouponSeeder::class,
            UserSeeder::class,
            OrderSeeder::class,
            MarketplaceSeeder::class,
            PaymentMethodSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
