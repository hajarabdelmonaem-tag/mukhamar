<?php

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('stores the device token on the user when registering', function (): void {
    $this->postJson('/api/v1/auth/register', [
        'name' => 'سارة أحمد',
        'email' => 'sara@example.com',
        'phone' => '+966 50 555 5555',
        'password' => 'password',
        'password_confirmation' => 'password',
        'fcm_token' => 'device-token',
        'accept_terms' => true,
    ])->assertCreated();

    expect(User::query()->where('email', 'sara@example.com')->value('fcm_token'))
        ->toBe('device-token');
});

it('moves a device token to the account that registers with it last', function (): void {
    $previous = User::factory()->create(['fcm_token' => 'device-token']);

    $this->postJson('/api/v1/auth/register', [
        'name' => 'سارة أحمد',
        'email' => 'sara@example.com',
        'phone' => '+966 50 555 5555',
        'password' => 'password',
        'password_confirmation' => 'password',
        'fcm_token' => 'device-token',
        'accept_terms' => true,
    ])->assertCreated();

    $current = User::query()->where('email', 'sara@example.com')->firstOrFail();

    expect($previous->fresh()->fcm_token)->toBeNull()
        ->and($current->fcm_token)->toBe('device-token')
        ->and(User::query()->where('fcm_token', 'device-token')->count())->toBe(1);
});

it('stores the device token on the user when verifying the otp', function (): void {
    $user = User::factory()->create(['phone_confirmation_code' => '1234']);

    $this->postJson('/api/v1/auth/verify-otp', [
        'phone' => $user->phone,
        'otp' => '1234',
        'fcm_token' => 'device-token',
    ])->assertOk();

    expect($user->fresh()->fcm_token)->toBe('device-token');
});

it('claims the device token on login', function (): void {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $this->postJson('/api/v1/auth/login', [
        'phone' => $user->phone,
        'password' => 'secret123',
        'fcm_token' => 'device-token',
    ])->assertOk();

    expect($user->fresh()->fcm_token)->toBe('device-token');
});

it('leaves the stored device token alone when the request carries none', function (): void {
    $user = User::factory()->create([
        'password' => bcrypt('secret123'),
        'fcm_token' => 'device-token',
    ]);

    $this->postJson('/api/v1/auth/login', [
        'phone' => $user->phone,
        'password' => 'secret123',
    ])->assertOk();

    expect($user->fresh()->fcm_token)->toBe('device-token');
});

it('guards the device token with a unique index', function (): void {
    $indexes = collect(Schema::getIndexes('users'))
        ->filter(fn (array $index) => $index['unique'] && $index['columns'] === ['fcm_token']);

    expect($indexes)->toHaveCount(1);
});

it('releases duplicate device tokens when the unique index is applied', function (): void {
    Schema::table('users', function (Blueprint $table) {
        $table->dropUnique(['fcm_token']);
    });

    $stale = User::factory()->create(['fcm_token' => 'shared-token']);
    $latest = User::factory()->create(['fcm_token' => 'shared-token']);
    $blank = User::factory()->create(['fcm_token' => '']);

    $latest->forceFill(['updated_at' => now()->addMinute()])->save();

    $migration = require base_path('database/migrations/2026_10_01_072025_make_users_fcm_token_unique.php');
    $migration->up();

    expect($latest->fresh()->fcm_token)->toBe('shared-token')
        ->and($stale->fresh()->fcm_token)->toBeNull()
        ->and($blank->fresh()->fcm_token)->toBeNull();
});
