<?php

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('sends a notification to a single user via the GeneralNotification class', function (): void {
    $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($admin)
        ->post(route('admin.notifications.store'), [
            'user_id' => $user->id,
            'title' => 'Welcome',
            'body' => 'Hello there',
        ])
        ->assertRedirect(route('admin.notifications.index'));

    $this->assertDatabaseHas('user_notifications', [
        'user_id' => $user->id,
        'title' => 'Welcome',
        'body' => 'Hello there',
    ]);

    expect(Notification::count())->toBe(1);
});

it('sends a notification to all active users via the GeneralNotification class', function (): void {
    $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
    User::factory()->count(3)->create(['is_active' => true]);
    User::factory()->count(2)->create(['is_active' => false]);

    $this->actingAs($admin)
        ->post(route('admin.notifications.store'), [
            'user_id' => null,
            'title' => 'Flash sale',
            'body' => 'Up to 50% off',
        ])
        ->assertRedirect(route('admin.notifications.index'));

    expect(Notification::count())->toBe(4);

    $this->assertDatabaseCount('user_notifications', 4);
});
