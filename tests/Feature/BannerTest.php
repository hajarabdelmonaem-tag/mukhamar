<?php

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns banner titles and descriptions in the requested locale', function (): void {
    Banner::factory()->create([
        'title' => ['en' => 'Winter Sale', 'ar' => 'تخفيضات الشتاء'],
        'description' => ['en' => 'Up to 30% off', 'ar' => 'خصم يصل إلى 30%'],
    ]);

    $this->getJson('/api/v1/home')
        ->assertOk()
        ->assertJsonPath('data.banners.0.title', 'Winter Sale')
        ->assertJsonPath('data.banners.0.description', 'Up to 30% off');

    $this->getJson('/api/v1/home', ['X-Locale' => 'ar'])
        ->assertOk()
        ->assertJsonPath('data.banners.0.title', 'تخفيضات الشتاء')
        ->assertJsonPath('data.banners.0.description', 'خصم يصل إلى 30%');
});

it('returns the banner image with a complete url', function (): void {
    Banner::factory()->create(['image' => 'banners/promo.jpg']);

    $this->getJson('/api/v1/home')
        ->assertOk()
        ->assertJsonPath('data.banners.0.image', 'banners/promo.jpg')
        ->assertJsonPath('data.banners.0.image_url', asset('storage/banners/promo.jpg'));
});

it('stores banner titles and descriptions in both locales', function (): void {
    $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

    $this->actingAs($admin)
        ->post(route('admin.banners.store'), [
            'title' => ['en' => 'Winter Sale', 'ar' => 'تخفيضات الشتاء'],
            'description' => ['en' => 'Up to 30% off', 'ar' => 'خصم يصل إلى 30%'],
        ])
        ->assertRedirect(route('admin.banners.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('banners', [
        'title' => json_encode(['en' => 'Winter Sale', 'ar' => 'تخفيضات الشتاء'], JSON_UNESCAPED_UNICODE),
        'description' => json_encode(['en' => 'Up to 30% off', 'ar' => 'خصم يصل إلى 30%'], JSON_UNESCAPED_UNICODE),
    ]);
});

it('requires the english and arabic banner titles', function (): void {
    $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

    $this->actingAs($admin)
        ->post(route('admin.banners.store'), ['title' => ['en' => 'Winter Sale']])
        ->assertSessionHasErrors('title.ar');
});
