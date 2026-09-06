<?php

namespace Database\Seeders;

use App\Models\Intro;
use Illuminate\Database\Seeder;

class IntroSeeder extends Seeder
{
    /**
     * Seed the application's intro slides.
     */
    public function run(): void
    {
        $intros = [
            [
                'image' => 'intros/winter-collection.jpg',
                'is_active' => true,
                'sort_order' => 1,
                'title' => [
                    'ar' => 'مجموعة الشتاء',
                    'en' => 'Winter Collection',
                ],
                'description' => [
                    'ar' => 'اكتشف عطورنا الحصرية لهذا الموسم',
                    'en' => 'Discover our exclusive fragrances for this season',
                ],
            ],
            [
                'image' => 'intros/oud-premium.jpg',
                'is_active' => true,
                'sort_order' => 2,
                'title' => [
                    'ar' => 'عود فاخر',
                    'en' => 'Premium Oud',
                ],
                'description' => [
                    'ar' => 'دهن عود أصيل من أجود المصادر',
                    'en' => 'Authentic oud oil from the finest sources',
                ],
            ],
            [
                'image' => 'intros/new-arrivals.jpg',
                'is_active' => true,
                'sort_order' => 3,
                'title' => [
                    'ar' => 'وصل حديثاً',
                    'en' => 'New Arrivals',
                ],
                'description' => [
                    'ar' => 'تشكيلة جديدة من العطور الشرقية والغربية',
                    'en' => 'New collection of Eastern and Western fragrances',
                ],
            ],
        ];

        foreach ($intros as $intro) {
            Intro::query()->updateOrCreate(
                ['sort_order' => $intro['sort_order']],
                $intro
            );
        }
    }
}
