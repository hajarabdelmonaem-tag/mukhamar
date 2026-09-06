<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's product catalog.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'مخمر عود كلاسيك',
                'slug' => 'classic-oud-mukhamar',
                'category' => 'oud-oil',
                'price' => 249,
                'old_price' => null,
                'rating' => 4.9,
                'reviews_count' => 120,
                'top_notes' => 'زعفران',
                'heart_notes' => 'ورد طائفي',
                'base_notes' => 'عود، مسك',
                'badges' => null,
                'is_featured' => true,
                'variants' => [
                    ['name' => '50ml', 'unit' => 'مل', 'price_adjustment' => 0, 'is_default' => true],
                    ['name' => '100ml', 'unit' => 'مل', 'price_adjustment' => 120, 'is_default' => false],
                ],
            ],
            [
                'name' => 'ليالي نجد',
                'slug' => 'najd-nights',
                'category' => 'women-perfumes',
                'price' => 320,
                'old_price' => 400,
                'rating' => 4.8,
                'reviews_count' => 85,
                'top_notes' => 'باتشولي',
                'heart_notes' => 'ورد طائفي',
                'base_notes' => 'مسك',
                'badges' => ['خصم'],
                'is_featured' => true,
                'variants' => [
                    ['name' => '50ml', 'unit' => 'مل', 'price_adjustment' => 0, 'is_default' => true],
                    ['name' => '100ml', 'unit' => 'مل', 'price_adjustment' => 140, 'is_default' => false],
                ],
            ],
            [
                'name' => 'بخور ملكي فاخر',
                'slug' => 'royal-luxury-incense',
                'category' => 'incense',
                'price' => 185,
                'old_price' => null,
                'rating' => 4.7,
                'reviews_count' => 210,
                'top_notes' => 'عود كمبودي',
                'heart_notes' => 'عنبر',
                'base_notes' => 'مسك',
                'badges' => ['الأكثر مبيعاً'],
                'is_featured' => true,
                'variants' => [
                    ['name' => '12g', 'unit' => 'جرام', 'price_adjustment' => 0, 'is_default' => true],
                ],
            ],
            [
                'name' => 'عود ملكي',
                'slug' => 'royal-oud',
                'category' => 'men-perfumes',
                'price' => 550,
                'old_price' => null,
                'rating' => 4.9,
                'reviews_count' => 320,
                'top_notes' => 'بنزوين',
                'heart_notes' => 'ورد طائفي',
                'base_notes' => 'عود مروكي',
                'badges' => null,
                'is_featured' => false,
                'variants' => [
                    ['name' => '50ml', 'unit' => 'مل', 'price_adjustment' => 0, 'is_default' => true],
                    ['name' => '100ml', 'unit' => 'مل', 'price_adjustment' => 250, 'is_default' => false],
                ],
            ],
            [
                'name' => 'همس الحرير',
                'slug' => 'silk-whisper',
                'category' => 'women-perfumes',
                'price' => 420,
                'old_price' => null,
                'rating' => 4.8,
                'reviews_count' => 75,
                'top_notes' => 'فريزيا',
                'heart_notes' => 'ياسمين',
                'base_notes' => 'مسك ناعم',
                'badges' => ['جديد'],
                'is_featured' => true,
                'variants' => [
                    ['name' => '50ml', 'unit' => 'مل', 'price_adjustment' => 0, 'is_default' => true],
                    ['name' => '100ml', 'unit' => 'مل', 'price_adjustment' => 180, 'is_default' => false],
                ],
            ],
            [
                'name' => 'ليل الصحراء',
                'slug' => 'desert-night',
                'category' => 'men-perfumes',
                'price' => 450,
                'old_price' => 600,
                'rating' => 4.6,
                'reviews_count' => 140,
                'top_notes' => 'هيل',
                'heart_notes' => 'باتشولي',
                'base_notes' => 'عنبر، خشب الصندل',
                'badges' => ['خصم'],
                'is_featured' => false,
                'variants' => [
                    ['name' => '50ml', 'unit' => 'مل', 'price_adjustment' => 0, 'is_default' => true],
                    ['name' => '100ml', 'unit' => 'مل', 'price_adjustment' => 200, 'is_default' => false],
                ],
            ],
            [
                'name' => 'دهن عود كمبودي معتق',
                'slug' => 'aged-cambodian-oud',
                'category' => 'oud-oil',
                'price' => 1200,
                'old_price' => null,
                'rating' => 5.0,
                'reviews_count' => 45,
                'top_notes' => 'زعفران',
                'heart_notes' => 'ورد',
                'base_notes' => 'عود كمبودي معتق',
                'badges' => null,
                'is_featured' => true,
                'variants' => [
                    ['name' => 'ربع تولة', 'unit' => 'تولة', 'price_adjustment' => 0, 'is_default' => true],
                    ['name' => 'تولة كاملة', 'unit' => 'تولة', 'price_adjustment' => 800, 'is_default' => false],
                ],
            ],
            [
                'name' => 'بخور سكون',
                'slug' => 'sukoon-incense',
                'category' => 'incense',
                'price' => 129,
                'old_price' => null,
                'rating' => 4.5,
                'reviews_count' => 96,
                'top_notes' => 'عود',
                'heart_notes' => 'رمان',
                'base_notes' => 'مسك',
                'badges' => null,
                'is_featured' => false,
                'variants' => [
                    ['name' => '8g', 'unit' => 'جرام', 'price_adjustment' => 0, 'is_default' => true],
                ],
            ],
            [
                'name' => 'معمول دوسري فاخر',
                'slug' => 'luxury-dosari-mamool',
                'category' => 'incense',
                'price' => 220,
                'old_price' => null,
                'rating' => 4.7,
                'reviews_count' => 120,
                'top_notes' => 'عود',
                'heart_notes' => 'ورد',
                'base_notes' => 'عنبر',
                'badges' => null,
                'is_featured' => false,
                'variants' => [
                    ['name' => '12g', 'unit' => 'جرام', 'price_adjustment' => 0, 'is_default' => true],
                ],
            ],
            [
                'name' => 'بخور المجلس',
                'slug' => 'majlis-incense',
                'category' => 'incense',
                'price' => 280,
                'old_price' => null,
                'rating' => 4.8,
                'reviews_count' => 200,
                'top_notes' => 'عود مروكي',
                'heart_notes' => 'خطمي',
                'base_notes' => 'مسك',
                'badges' => null,
                'is_featured' => false,
                'variants' => [
                    ['name' => '16g', 'unit' => 'جرام', 'price_adjustment' => 0, 'is_default' => true],
                ],
            ],
            [
                'name' => 'موزع عطر عنبر',
                'slug' => 'amber-diffuser',
                'category' => 'home-fragrances',
                'price' => 390,
                'old_price' => null,
                'rating' => 4.6,
                'reviews_count' => 60,
                'top_notes' => 'برغموت',
                'heart_notes' => 'عنبر',
                'base_notes' => 'فانيليا',
                'badges' => null,
                'is_featured' => false,
                'variants' => [
                    ['name' => '200ml', 'unit' => 'مل', 'price_adjustment' => 0, 'is_default' => true],
                ],
            ],
            [
                'name' => 'دهن الورد الدمشقي',
                'slug' => 'damask-rose-dehen',
                'category' => 'oud-oil',
                'price' => 450,
                'old_price' => null,
                'rating' => 4.9,
                'reviews_count' => 88,
                'top_notes' => 'ورد دمشقي',
                'heart_notes' => 'صندل',
                'base_notes' => 'مسك',
                'badges' => null,
                'is_featured' => false,
                'variants' => [
                    ['name' => 'ربع تولة', 'unit' => 'تولة', 'price_adjustment' => 0, 'is_default' => true],
                    ['name' => 'تولة كاملة', 'unit' => 'تولة', 'price_adjustment' => 350, 'is_default' => false],
                ],
            ],
            [
                'name' => 'مسك الحرير',
                'slug' => 'silk-musk',
                'category' => 'women-perfumes',
                'price' => 380,
                'old_price' => null,
                'rating' => 4.8,
                'reviews_count' => 150,
                'top_notes' => 'يلنغ',
                'heart_notes' => 'ياسمين',
                'base_notes' => 'مسك أبيض',
                'badges' => null,
                'is_featured' => false,
                'variants' => [
                    ['name' => '50ml', 'unit' => 'مل', 'price_adjustment' => 0, 'is_default' => true],
                    ['name' => '100ml', 'unit' => 'مل', 'price_adjustment' => 170, 'is_default' => false],
                ],
            ],
            [
                'name' => 'العود الفاخر',
                'slug' => 'luxury-oud',
                'category' => 'men-perfumes',
                'price' => 650,
                'old_price' => null,
                'rating' => 4.9,
                'reviews_count' => 270,
                'top_notes' => 'زعفران',
                'heart_notes' => 'ورد',
                'base_notes' => 'عود، عنبر',
                'badges' => ['الأكثر مبيعاً'],
                'is_featured' => true,
                'variants' => [
                    ['name' => '50ml', 'unit' => 'مل', 'price_adjustment' => 0, 'is_default' => true],
                    ['name' => '100ml', 'unit' => 'مل', 'price_adjustment' => 300, 'is_default' => false],
                ],
            ],
        ];

        foreach ($products as $data) {
            $category = Category::where('slug', $data['category'])->firstOrFail();
            $variants = $data['variants'];
            unset($data['category'], $data['variants']);

            $data['category_id'] = $category->id;

            /** @var Product $product */
            $product = Product::query()->updateOrCreate(['slug' => $data['slug']], $data);

            foreach ($variants as $variant) {
                $product->variants()->updateOrCreate(
                    ['name' => $variant['name']],
                    [...$variant, 'sku' => strtoupper(str_replace('-', '', $data['slug'])).mt_rand(100, 999)]
                );
            }

            $product->images()->create([
                'path' => 'products/'.$data['slug'].'.jpg',
                'alt' => $data['name'],
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }
    }
}
