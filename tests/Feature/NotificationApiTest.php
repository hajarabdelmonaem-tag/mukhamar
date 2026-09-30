<?php

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create(['is_active' => true]);
    $this->actingAs($this->user, 'sanctum');
});

it('marks the returned notifications as read', function (): void {
    $notifications = Notification::factory()->count(2)->create([
        'user_id' => $this->user->id,
        'read_at' => null,
    ]);

    $response = $this->getJson('/api/v1/notifications');

    $response->assertOk()
        ->assertJsonPath('data.0.is_read', true)
        ->assertJsonPath('data.1.is_read', true)
        ->assertJsonCount(2, 'data');

    foreach ($notifications as $notification) {
        expect($notification->fresh()->read_at)->not->toBeNull();
    }
});

it('keeps the original read date of already read notifications', function (): void {
    $readAt = now()->subDay();
    $notification = Notification::factory()->create([
        'user_id' => $this->user->id,
        'read_at' => $readAt,
    ]);

    $response = $this->getJson('/api/v1/notifications');

    expect($notification->fresh()->read_at->timestamp)->toBe($readAt->timestamp);

    $response->assertOk()
        ->assertJsonPath('data.0.is_read', true)
        ->assertJsonPath('data.0.read_at', $notification->fresh()->read_at->toISOString());
});

it('marks unread notifications beyond the returned page as read', function (): void {
    Notification::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'read_at' => null,
    ]);

    $this->getJson('/api/v1/notifications?per_page=2')
        ->assertOk()
        ->assertJsonPath('unread_count', 3);

    expect(Notification::whereNull('read_at')->where('user_id', $this->user->id)->count())->toBe(0);
});

it('returns the unread notification count without marking anything as read', function (): void {
    Notification::factory()->count(2)->create([
        'user_id' => $this->user->id,
        'read_at' => null,
    ]);
    Notification::factory()->create(['user_id' => $this->user->id, 'read_at' => now()]);

    $this->getJson('/api/v1/notifications/unread-count')
        ->assertOk()
        ->assertJsonPath('data.unread_count', 2);

    expect(Notification::whereNull('read_at')->where('user_id', $this->user->id)->count())->toBe(2);

    $this->getJson('/api/v1/notifications/unread-count')
        ->assertOk()
        ->assertJsonPath('data.unread_count', 2);
});

it('does not count another user unread notifications', function (): void {
    Notification::factory()->count(3)->create([
        'user_id' => User::factory()->create()->id,
        'read_at' => null,
    ]);

    $this->getJson('/api/v1/notifications/unread-count')
        ->assertOk()
        ->assertJsonPath('data.unread_count', 0);
});

it('requires authentication to read the unread count', function (): void {
    auth('sanctum')->forgetUser();

    $this->getJson('/api/v1/notifications/unread-count')->assertUnauthorized();
});

it('marks a notification as read', function (): void {
    $notification = Notification::factory()->create([
        'user_id' => $this->user->id,
        'read_at' => null,
    ]);

    $this->postJson("/api/v1/notifications/{$notification->id}/read")
        ->assertOk()
        ->assertJsonPath('data.id', $notification->id)
        ->assertJsonPath('data.is_read', true);

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('keeps the original read timestamp when marking an already read notification', function (): void {
    $readAt = now()->subDay();
    $notification = Notification::factory()->create([
        'user_id' => $this->user->id,
        'read_at' => $readAt,
    ]);

    $this->postJson("/api/v1/notifications/{$notification->id}/read")
        ->assertOk()
        ->assertJsonPath('data.is_read', true);

    expect($notification->fresh()->read_at->timestamp)->toBe($readAt->timestamp);
});

it('marks every unread notification of the user as read', function (): void {
    Notification::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'read_at' => null,
    ]);

    $this->postJson('/api/v1/notifications/read-all')
        ->assertOk()
        ->assertJsonPath('message', __('api.notification.all_read'));

    expect(Notification::whereNull('read_at')->where('user_id', $this->user->id)->count())->toBe(0);
});

it('does not mark another user notification as read', function (): void {
    $notification = Notification::factory()->create([
        'user_id' => User::factory()->create()->id,
        'read_at' => null,
    ]);

    $this->postJson("/api/v1/notifications/{$notification->id}/read")
        ->assertForbidden();

    expect($notification->fresh()->read_at)->toBeNull();
});

it('reports the unread count from before the notifications were marked as read', function (): void {
    Notification::factory()->count(2)->create([
        'user_id' => $this->user->id,
        'read_at' => null,
    ]);
    Notification::factory()->create(['user_id' => $this->user->id, 'read_at' => now()]);

    $this->getJson('/api/v1/notifications')
        ->assertOk()
        ->assertJsonPath('unread_count', 2);

    $this->getJson('/api/v1/notifications')
        ->assertOk()
        ->assertJsonPath('unread_count', 0);
});
