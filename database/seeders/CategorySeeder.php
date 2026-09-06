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
                'name' => 'عطور نسائية',
                'slug' => 'women-perfumes',
                'description' => 'تشكيلة راقية من العطور النسائية الشرقية والغربية',
                'type' => 'perfume',
                'sort_order' => 1,
            ],
            [
                'name' => 'عطور رجالية',
                'slug' => 'men-perfumes',
                'description' => 'عطور فاخرة للرجال تجمع بين الأصالة والفخامة',
                'type' => 'perfume',
                'sort_order' => 2,
            ],
            [
                'name' => 'دهن العود',
                'slug' => 'oud-oil',
                'description' => 'دهن عود أصلي من أجود المصادر الكمبودية والمروكي',
                'type' => 'dehen',
                'sort_order' => 3,
            ],
            [
                'name' => 'بخور ومباخر',
                'slug' => 'incense',
                'description' => 'بخور فاخر يملأ المكان وعبق الأصالة',
                'type' => 'incense',
                'sort_order' => 4,
            ],
            [
                'name' => 'عطور منزلية',
                'slug' => 'home-fragrances',
                'description' => 'موزعات عطور ومنعشات أجواء للمنزل',
                'type' => 'home',
                'sort_order' => 5,
            ],
            [
                'name' => 'هدايا',
                'slug' => 'gifts',
                'description' => 'علب هدايا فاخرة لإبهار من تحب',
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
