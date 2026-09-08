<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application settings with social links, terms and policy.
     */
    public function run(): void
    {
        $settings = [
            'socials' => [
                ['name' => ['en' => 'Facebook', 'ar' => 'فيسبوك'], 'icon' => 'socials/facebook.png', 'link' => 'https://facebook.com/mukhamar'],
                ['name' => ['en' => 'Instagram', 'ar' => 'انستغرام'], 'icon' => 'socials/instagram.png', 'link' => 'https://instagram.com/mukhamar'],
                ['name' => ['en' => 'TikTok', 'ar' => 'تيك توك'], 'icon' => 'socials/tiktok.png', 'link' => 'https://tiktok.com/@mukhamar'],
                ['name' => ['en' => 'X', 'ar' => 'إكس'], 'icon' => 'socials/x.png', 'link' => 'https://x.com/mukhamar'],
            ],
            'terms' => [
                'title' => ['en' => 'Terms & Conditions', 'ar' => 'الشروط والأحكام'],
                'sections' => [
                    [
                        'heading' => ['en' => 'Acceptance of Terms', 'ar' => 'قبول الشروط'],
                        'body' => [
                            'en' => 'By accessing or using our application, you agree to be bound by these terms and conditions.',
                            'ar' => 'باستخدامك لتطبيقنا فإنك توافق على الالتزام بهذه الشروط والأحكام.',
                        ],
                    ],
                    [
                        'heading' => ['en' => 'Products & Orders', 'ar' => 'المنتجات والطلبات'],
                        'body' => [
                            'en' => 'All orders are subject to availability and confirmation. We reserve the right to refuse or cancel any order.',
                            'ar' => 'جميع الطلبات تخضع لتوافر المنتجات وتأكيدها. نحتفظ بحق رفض أو إلغاء أي طلب.',
                        ],
                    ],
                ],
            ],
            'policy' => [
                'title' => ['en' => 'Privacy Policy', 'ar' => 'سياسة الخصوصية'],
                'sections' => [
                    [
                        'heading' => ['en' => 'Information We Collect', 'ar' => 'المعلومات التي نجمعها'],
                        'body' => [
                            'en' => 'We collect personal information you provide directly, such as your name, phone number and order details.',
                            'ar' => 'نجمع المعلومات الشخصية التي تقدمها مباشرة، مثل اسمك ورقم هاتفك وتفاصيل طلباتك.',
                        ],
                    ],
                    [
                        'heading' => ['en' => 'How We Use Your Information', 'ar' => 'كيف نستخدم معلوماتك'],
                        'body' => [
                            'en' => 'Your information is used to process orders, improve our services, and communicate important updates.',
                            'ar' => 'نستخدم معلوماتك لمعالجة الطلبات وتحسين خدماتنا والتواصل معك بشأن التحديثات المهمة.',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
