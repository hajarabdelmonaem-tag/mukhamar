<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's product categories.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => ['en' => 'Women Perfumes', 'ar' => 'عطور نسائية'],
                'slug' => 'women-perfumes',
                'description' => ['en' => 'An elegant collection of Oriental and Western women\'s perfumes', 'ar' => 'تشكيلة راقية من العطور النسائية الشرقية والغربية'],
                'type' => 'perfume',
                'sort_order' => 1,
            ],
            [
                'name' => ['en' => 'Men Perfumes', 'ar' => 'عطور رجالية'],
                'slug' => 'men-perfumes',
                'description' => ['en' => 'Luxurious perfumes for men combining authenticity and elegance', 'ar' => 'عطور فاخرة للرجال تجمع بين الأصالة والفخامة'],
                'type' => 'perfume',
                'sort_order' => 2,
            ],
            [
                'name' => ['en' => 'Oud Oil', 'ar' => 'دهن العود'],
                'slug' => 'oud-oil',
                'description' => ['en' => 'Authentic oud oil from the finest Cambodian and Moroccan sources', 'ar' => 'دهن عود أصلي من أجود المصادر الكمبودية والمروكي'],
                'type' => 'dehen',
                'sort_order' => 3,
            ],
            [
                'name' => ['en' => 'Incense and Burners', 'ar' => 'بخور ومباخر'],
                'slug' => 'incense',
                'description' => ['en' => 'Luxurious incense that fills the place with heritage fragrance', 'ar' => 'بخور فاخر يملأ المكان وعبق الأصالة'],
                'type' => 'incense',
                'sort_order' => 4,
            ],
            [
                'name' => ['en' => 'Home Fragrances', 'ar' => 'عطور منزلية'],
                'slug' => 'home-fragrances',
                'description' => ['en' => 'Fragrance diffusers and home air fresheners', 'ar' => 'موزعات عطور ومنعشات أجواء للمنزل'],
                'type' => 'home',
                'sort_order' => 5,
            ],
            [
                'name' => ['en' => 'Gifts', 'ar' => 'هدايا'],
                'slug' => 'gifts',
                'description' => ['en' => 'Luxurious gift boxes to impress your loved ones', 'ar' => 'علب هدايا فاخرة لإبهار من تحب'],
                'type' => 'gifts',
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
