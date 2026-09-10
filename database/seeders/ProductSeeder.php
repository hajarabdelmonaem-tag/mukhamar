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
                'name' => ['en' => 'Classic Oud Mukhamar', 'ar' => 'مخمر عود كلاسيك'],
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
                'name' => ['en' => 'Najd Nights', 'ar' => 'ليالي نجد'],
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
                'name' => ['en' => 'Royal Luxury Incense', 'ar' => 'بخور ملكي فاخر'],
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
                'name' => ['en' => 'Royal Oud', 'ar' => 'عود ملكي'],
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
                'name' => ['en' => 'Silk Whisper', 'ar' => 'همس الحرير'],
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
                'name' => ['en' => 'Desert Night', 'ar' => 'ليل الصحراء'],
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
                'name' => ['en' => 'Aged Cambodian Oud', 'ar' => 'دهن عود كمبودي معتق'],
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
                'name' => ['en' => 'Sukoon Incense', 'ar' => 'بخور سكون'],
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
                'name' => ['en' => 'Luxury Dosari Mamool', 'ar' => 'معمول دوسري فاخر'],
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
                'name' => ['en' => 'Majlis Incense', 'ar' => 'بخور المجلس'],
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
                'name' => ['en' => 'Amber Diffuser', 'ar' => 'موزع عطر عنبر'],
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
                'name' => ['en' => 'Damask Rose Dehen', 'ar' => 'دهن الورد الدمشقي'],
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
                'name' => ['en' => 'Silk Musk', 'ar' => 'مسك الحرير'],
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
                'name' => ['en' => 'Luxury Oud', 'ar' => 'العود الفاخر'],
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
            $data['top_notes'] = self::notes($data['top_notes'] ?? null);
            $data['heart_notes'] = self::notes($data['heart_notes'] ?? null);
            $data['base_notes'] = self::notes($data['base_notes'] ?? null);

            /** @var Product $product */
            $product = Product::query()->updateOrCreate(['slug' => $data['slug']], $data);

            foreach ($variants as $variant) {
                $variantData = [
                    'name' => self::variantName($variant['name']),
                    'unit' => isset($variant['unit']) ? self::variantUnit($variant['unit']) : null,
                    'price_adjustment' => $variant['price_adjustment'],
                    'is_default' => $variant['is_default'],
                    'sku' => strtoupper(str_replace('-', '', $data['slug'])).mt_rand(100, 999),
                ];

                $existing = $product->variants()->where('name->en', $variant['name'])->first();

                if ($existing) {
                    $existing->update($variantData);
                } else {
                    $product->variants()->create($variantData);
                }
            }

            $product->images()->create([
                'path' => 'products/'.$data['slug'].'.jpg',
                'alt' => $data['name'],
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }
    }

    /**
     * Bilingual translation for a fragrance note string.
     */
    private static function notes(?string $value): ?array
    {
        if ($value === null) {
            return null;
        }

        $map = [
            'زعفران' => 'Saffron',
            'ورد طائفي' => 'Taif Rose',
            'عود' => 'Oud',
            'مسك' => 'Musk',
            'باتشولي' => 'Patchouli',
            'عنبر' => 'Amber',
            'عود كمبودي' => 'Cambodian Oud',
            'بنزوين' => 'Benzoin',
            'عود مروكي' => 'Moroccan Oud',
            'فريزيا' => 'Freesia',
            'ياسمين' => 'Jasmine',
            'مسك ناعم' => 'Soft Musk',
            'هيل' => 'Cardamom',
            'خشب الصندل' => 'Sandalwood',
            'ورد' => 'Rose',
            'رمان' => 'Pomegranate',
            'خطمي' => 'Mallow',
            'برغموت' => 'Bergamot',
            'فانيليا' => 'Vanilla',
            'ورد دمشقي' => 'Damask Rose',
            'صندل' => 'Sandalwood',
            'يلنغ' => 'Ylang Ylang',
            'مسك أبيض' => 'White Musk',
        ];

        $english = collect(explode('،', $value))
            ->map(fn (string $note) => $map[trim($note)] ?? trim($note))
            ->implode(', ');

        return ['en' => $english, 'ar' => $value];
    }

    /**
     * Bilingual translation for a variant name.
     *
     * @return array{en: string, ar: string}
     */
    private static function variantName(string $name): array
    {
        return match ($name) {
            '50ml' => ['en' => '50ml', 'ar' => '50 مل'],
            '100ml' => ['en' => '100ml', 'ar' => '100 مل'],
            '200ml' => ['en' => '200ml', 'ar' => '200 مل'],
            '12g' => ['en' => '12g', 'ar' => '12 جرام'],
            '8g' => ['en' => '8g', 'ar' => '8 جرام'],
            '16g' => ['en' => '16g', 'ar' => '16 جرام'],
            'ربع تولة' => ['en' => 'Quarter Tola', 'ar' => 'ربع تولة'],
            'تولة كاملة' => ['en' => 'Full Tola', 'ar' => 'تولة كاملة'],
            default => ['en' => $name, 'ar' => $name],
        };
    }

    /**
     * Bilingual translation for a variant unit.
     *
     * @return array{en: string, ar: string}
     */
    private static function variantUnit(string $unit): array
    {
        return match ($unit) {
            'مل' => ['en' => 'ml', 'ar' => 'مل'],
            'جرام' => ['en' => 'g', 'ar' => 'جرام'],
            'تولة' => ['en' => 'tola', 'ar' => 'تولة'],
            default => ['en' => $unit, 'ar' => $unit],
        };
    }
}
