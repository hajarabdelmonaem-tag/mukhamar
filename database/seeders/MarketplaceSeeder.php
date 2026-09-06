<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class MarketplaceSeeder extends Seeder
{
    /**
     * Seed wishlist, reviews, notifications, and contact messages.
     */
    public function run(): void
    {
        $customers = User::whereNot('email', 'like', '%@mukhamar.sa')->limit(5)->get();
        $products = Product::query()->get();
        $totalProducts = $products->count();

        foreach ($customers as $index => $user) {
            // Each customer wishes a couple of products.
            $wished = $products->random(min(2, $totalProducts));
            foreach ($wished as $product) {
                Wishlist::firstOrCreate([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ]);
            }

            // Each customer reviews one or two approved products.
            $reviewed = $products->random(min(2, $totalProducts));
            foreach ($reviewed as $product) {
                ProductReview::firstOrCreate(
                    ['user_id' => $user->id, 'product_id' => $product->id],
                    [
                        'rating' => mt_rand(4, 5),
                        'title' => ['رائحة فاخرة', 'جودة ممتازة', 'تثبيت يدوم طويلاً'][mt_rand(0, 2)],
                        'body' => 'تقييم سريع بعد الاستخدام: الجودة ممتازة والرائحة راقية جداً وتدوم لوقت طويل.',
                        'is_approved' => true,
                    ]
                );
            }

            // A couple of notifications per user.
            Notification::query()->create([
                'user_id' => $user->id,
                'title' => 'عرض خاص لك',
                'body' => 'خصم 20% على العود الملكي لفترة محدودة.',
                'type' => 'offer',
                'action_url' => '/products/royal-oud',
                'read_at' => mt_rand(0, 1) ? now()->subHours(mt_rand(1, 12)) : null,
            ]);

            Notification::query()->create([
                'user_id' => $user->id,
                'title' => 'مرحباً بك في مخمر',
                'body' => 'استكشف تشكيلتنا الفاخرة من العطور الشرقية.',
                'type' => 'welcome',
                'action_url' => '/',
                'read_at' => null,
            ]);
        }

        // A few incoming contact messages.
        for ($i = 0; $i < 4; $i++) {
            ContactMessage::factory()->create();
        }
    }
}
