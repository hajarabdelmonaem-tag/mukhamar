<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('returns the complete path of the uploaded icon', function (): void {
    Setting::query()->create(['key' => 'socials', 'value' => [
        ['id' => 'social-1', 'name' => ['en' => 'Facebook', 'ar' => 'فيسبوك'], 'icon' => 'socials/facebook.png', 'link' => 'https://facebook.com/mukhamar'],
    ]]);

    $this->getJson('/api/v1/socials')
        ->assertOk()
        ->assertJsonPath('data.0.icon', asset('storage/socials/facebook.png'));
});

it('keeps an absolute icon url untouched', function (): void {
    Setting::query()->create(['key' => 'socials', 'value' => [
        ['id' => 'social-1', 'name' => ['en' => 'Facebook', 'ar' => 'فيسبوك'], 'icon' => 'https://cdn.mukhamar.com/facebook.png', 'link' => 'https://facebook.com/mukhamar'],
    ]]);

    $this->getJson('/api/v1/socials')
        ->assertOk()
        ->assertJsonPath('data.0.icon', 'https://cdn.mukhamar.com/facebook.png');
});

it('returns a null icon when no image was uploaded', function (): void {
    Setting::query()->create(['key' => 'socials', 'value' => [
        ['id' => 'social-1', 'name' => ['en' => 'Facebook', 'ar' => 'فيسبوك'], 'icon' => null, 'link' => 'https://facebook.com/mukhamar'],
    ]]);

    $this->getJson('/api/v1/socials')
        ->assertOk()
        ->assertJsonPath('data.0.icon', null);
});

it('returns the social link names in the requested locale', function (): void {
    Setting::query()->create(['key' => 'socials', 'value' => [
        ['id' => 'social-1', 'name' => ['en' => 'Facebook', 'ar' => 'فيسبوك'], 'icon' => null, 'link' => 'https://facebook.com/mukhamar'],
    ]]);

    $this->getJson('/api/v1/socials')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Facebook');

    $this->getJson('/api/v1/socials', ['X-Locale' => 'ar'])
        ->assertOk()
        ->assertJsonPath('data.0.name', 'فيسبوك');
});

it('stores the uploaded icon and returns its complete path', function (): void {
    Storage::fake('public');
    $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

    $this->actingAs($admin)
        ->post(route('admin.socials.store'), [
            'name' => ['en' => 'Facebook', 'ar' => 'فيسبوك'],
            'link' => 'https://facebook.com/mukhamar',
            'icon' => UploadedFile::fake()->create('facebook.png', 100, 'image/png'),
        ])
        ->assertRedirect(route('admin.socials.index'))
        ->assertSessionHasNoErrors();

    $icon = Setting::query()->where('key', 'socials')->value('value')[0]['icon'];

    Storage::disk('public')->assertExists($icon);

    $this->getJson('/api/v1/socials')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Facebook')
        ->assertJsonPath('data.0.icon', asset('storage/'.$icon))
        ->assertJsonPath('data.0.link', 'https://facebook.com/mukhamar');
});

it('rejects a social link icon that is not an image', function (): void {
    $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

    $this->actingAs($admin)
        ->post(route('admin.socials.store'), [
            'name' => ['en' => 'Facebook', 'ar' => 'فيسبوك'],
            'link' => 'https://facebook.com/mukhamar',
            'icon' => UploadedFile::fake()->create('facebook.txt', 10, 'text/plain'),
        ])
        ->assertSessionHasErrors('icon');

    expect(Setting::query()->where('key', 'socials')->exists())->toBeFalse();
});

it('keeps the uploaded icon when updating without a new image', function (): void {
    Setting::query()->create(['key' => 'socials', 'value' => [
        ['id' => 'social-1', 'name' => ['en' => 'Facebook', 'ar' => 'فيسبوك'], 'icon' => 'socials/facebook.png', 'link' => 'https://facebook.com/mukhamar'],
    ]]);
    $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

    $this->actingAs($admin)
        ->put(route('admin.socials.update', 'social-1'), [
            'name' => ['en' => 'Facebook', 'ar' => 'فيسبوك'],
            'link' => 'https://facebook.com/new',
        ])
        ->assertRedirect(route('admin.socials.index'))
        ->assertSessionHasNoErrors();

    $social = Setting::query()->where('key', 'socials')->value('value')[0];

    expect($social['icon'])->toBe('socials/facebook.png')
        ->and($social['link'])->toBe('https://facebook.com/new');
});

it('deletes the replaced icon image', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('socials/facebook.png', 'old image');

    Setting::query()->create(['key' => 'socials', 'value' => [
        ['id' => 'social-1', 'name' => ['en' => 'Facebook', 'ar' => 'فيسبوك'], 'icon' => 'socials/facebook.png', 'link' => 'https://facebook.com/mukhamar'],
    ]]);
    $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

    $this->actingAs($admin)
        ->put(route('admin.socials.update', 'social-1'), [
            'name' => ['en' => 'Facebook', 'ar' => 'فيسبوك'],
            'link' => 'https://facebook.com/mukhamar',
            'icon' => UploadedFile::fake()->create('facebook.png', 100, 'image/png'),
        ])
        ->assertRedirect(route('admin.socials.index'))
        ->assertSessionHasNoErrors();

    $icon = Setting::query()->where('key', 'socials')->value('value')[0]['icon'];

    expect($icon)->not->toBe('socials/facebook.png');

    Storage::disk('public')->assertExists($icon);
    Storage::disk('public')->assertMissing('socials/facebook.png');
});

it('deletes the uploaded icon image when the social link is removed', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('socials/facebook.png', 'old image');

    Setting::query()->create(['key' => 'socials', 'value' => [
        ['id' => 'social-1', 'name' => ['en' => 'Facebook', 'ar' => 'فيسبوك'], 'icon' => 'socials/facebook.png', 'link' => 'https://facebook.com/mukhamar'],
    ]]);
    $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

    $this->actingAs($admin)
        ->delete(route('admin.socials.destroy', 'social-1'))
        ->assertRedirect(route('admin.socials.index'));

    expect(Setting::query()->where('key', 'socials')->value('value'))->toBe([]);

    Storage::disk('public')->assertMissing('socials/facebook.png');
});
