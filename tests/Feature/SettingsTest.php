<?php

use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns the configured currency in product responses', function (): void {
    Setting::query()->create(['key' => 'currency', 'value' => 'OMR']);
    Product::factory()->create(['price' => 100]);

    $this->getJson('/api/v1/products')
        ->assertOk()
        ->assertJsonPath('data.0.currency', 'OMR');
});

it('falls back to the default currency when no setting is saved', function (): void {
    Product::factory()->create(['price' => 100]);

    $this->getJson('/api/v1/products')
        ->assertOk()
        ->assertJsonPath('data.0.currency', 'SAR');
});

it('seeds default values for all settings', function (): void {
    $this->seed(SettingSeeder::class);

    $settings = Setting::pluck('value', 'key')->all();

    expect($settings['site_name'])->toBe('Mukhamar')
        ->and($settings['site_email'])->toBe('info@mukhamar.com')
        ->and($settings['site_phone'])->toBe('+966 50 000 0000')
        ->and($settings['site_logo'])->toBe('')
        ->and($settings['currency'])->toBe('SAR')
        ->and($settings['tax_rate'])->toBe(15)
        ->and($settings['shipping_fee'])->toBe(30)
        ->and($settings['free_shipping_threshold'])->toBe(300)
        ->and($settings['maintenance_mode'])->toBe('false')
        ->and($settings['socials'])->toBeArray()
        ->and($settings['terms_conditions']['sections'])->toBeArray()
        ->and($settings['privacy_policy']['sections'])->toBeArray();
});

it('returns policy and terms as structured title and sections', function (): void {
    Setting::query()->create(['key' => 'privacy_policy', 'value' => [
        'title' => ['en' => 'Privacy Policy', 'ar' => 'سياسة الخصوصية'],
        'sections' => [
            [
                'heading' => ['en' => 'Data', 'ar' => 'البيانات'],
                'body' => ['en' => '<p>We keep your data <strong>safe</strong>.</p>', 'ar' => '<p>نحافظ على بياناتك.</p>'],
            ],
        ],
    ]]);

    Setting::query()->create(['key' => 'terms_conditions', 'value' => [
        'title' => ['en' => 'Terms', 'ar' => 'الشروط'],
        'sections' => [
            [
                'heading' => ['en' => 'Payment', 'ar' => 'الدفع'],
                'body' => ['en' => 'Pay on delivery.', 'ar' => 'الدفع عند الاستلام.'],
            ],
        ],
    ]]);

    $this->getJson('/api/v1/policy')
        ->assertOk()
        ->assertJsonPath('data.title', 'Privacy Policy')
        ->assertJsonPath('data.sections.0.heading', 'Data')
        ->assertJsonPath('data.sections.0.body', '<p>We keep your data <strong>safe</strong>.</p>');

    $this->getJson('/api/v1/terms', ['Accept-Language' => 'ar'])
        ->assertOk()
        ->assertJsonPath('data.title', 'الشروط')
        ->assertJsonPath('data.sections.0.body', 'الدفع عند الاستلام.');
});

it('falls back to the legacy terms and policy settings keys', function (): void {
    Setting::query()->create(['key' => 'terms', 'value' => [
        'title' => ['en' => 'Legacy Terms', 'ar' => 'الشروط القديمة'],
        'sections' => [
            ['heading' => ['en' => 'Intro', 'ar' => 'مقدمة'], 'body' => ['en' => 'Hello', 'ar' => 'مرحبا']],
        ],
    ]]);

    $this->getJson('/api/v1/terms')
        ->assertOk()
        ->assertJsonPath('data.title', 'Legacy Terms')
        ->assertJsonPath('data.sections.0.heading', 'Intro');
});

it('stores legal documents from the settings page as normalised title and sections', function (): void {
    $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

    $this->actingAs($admin)
        ->put(route('admin.settings.update'), [
            'privacy_policy' => [
                'title' => ['en' => 'Privacy Policy', 'ar' => 'سياسة الخصوصية'],
                'sections' => [
                    [
                        'heading' => ['en' => '', 'ar' => ''],
                        'body' => ['en' => '<p>&nbsp;</p>', 'ar' => ''],
                    ],
                    [
                        'heading' => ['en' => 'Data', 'ar' => 'البيانات'],
                        'body' => [
                            'en' => '<p style="font-family:Arial">Styled body</p>',
                            'ar' => '<p>محتوى عربي</p>',
                        ],
                    ],
                ],
            ],
            'terms_conditions' => [
                'title' => ['en' => 'Terms', 'ar' => 'الشروط'],
                'sections' => [],
            ],
        ])
        ->assertRedirect(route('admin.settings.index'))
        ->assertSessionHas('success', __('admin.settings.saved'));

    $privacy = Setting::query()->where('key', 'privacy_policy')->value('value');

    expect($privacy['title']['en'])->toBe('Privacy Policy')
        ->and($privacy['sections'])->toHaveCount(1)
        ->and($privacy['sections'][0]['heading']['en'])->toBe('Data')
        ->and($privacy['sections'][0]['body']['en'])->toBe('<p style="font-family:Arial">Styled body</p>');

    $terms = Setting::query()->where('key', 'terms_conditions')->value('value');

    expect($terms['sections'])->toBe([]);
});

it('renders the legal document editors on the settings page', function (): void {
    Setting::query()->create(['key' => 'privacy_policy', 'value' => [
        'title' => ['en' => 'Privacy Policy', 'ar' => 'سياسة الخصوصية'],
        'sections' => [
            ['heading' => ['en' => 'Data', 'ar' => 'البيانات'], 'body' => ['en' => '<p>Body</p>', 'ar' => '<p>جسم</p>']],
        ],
    ]]);
    $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

    $this->actingAs($admin)
        ->get(route('admin.settings.index'))
        ->assertOk()
        ->assertSee("settingsEditor('privacy_policy'", false)
        ->assertSee('data-ck', false)
        ->assertSee('ckeditor.js', false)
        ->assertSee('Body')
        ->assertSee("x-data=\"settingsEditor('privacy_policy', {&quot;title&quot;", false)
        ->assertDontSee("x-data=\"settingsEditor('privacy_policy', {\"title\"", false);
});

it('flashes a localized success message when saving settings', function (): void {
    $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

    $this->actingAs($admin)
        ->put(route('admin.settings.update'), ['site_name' => 'Mukhamar'])
        ->assertSessionHas('success', 'Settings saved successfully.');

    $this->actingAs($admin)
        ->put(route('admin.settings.update'), ['site_name' => 'مخمر'], ['X-Locale' => 'ar'])
        ->assertSessionHas('success', 'تم حفظ الإعدادات بنجاح.');
});
