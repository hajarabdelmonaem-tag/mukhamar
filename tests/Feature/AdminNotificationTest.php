<?php

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;
use NotificationChannels\Fcm\FcmChannel;

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

it('queues one notification per active user instead of pushing inside the request', function (): void {
    Queue::fake();

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

    $fcmJobs = Queue::pushed(SendQueuedNotifications::class)
        ->filter(fn (SendQueuedNotifications $job) => $job->channels === [FcmChannel::class]);

    expect($fcmJobs)->toHaveCount(4);
});

it('pushes to a device token only once when several users have claimed it', function (): void {
    Queue::fake();

    $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
    $first = User::factory()->create(['is_active' => true]);
    $second = User::factory()->create(['is_active' => true]);

    $first->claimFcmToken('device-token');
    $second->claimFcmToken('device-token');

    $this->actingAs($admin)
        ->post(route('admin.notifications.store'), [
            'user_id' => null,
            'title' => 'Flash sale',
            'body' => 'Up to 50% off',
        ])
        ->assertRedirect(route('admin.notifications.index'));

    expect($first->fresh()->fcm_token)->toBeNull()
        ->and($second->fresh()->fcm_token)->toBe('device-token');

    $tokens = Queue::pushed(SendQueuedNotifications::class)
        ->filter(fn (SendQueuedNotifications $job) => $job->channels === [FcmChannel::class])
        ->flatMap(fn (SendQueuedNotifications $job) => $job->notifiables
            ->map(fn (User $user) => $user->routeNotificationForFcm())
            ->filter()
            ->all())
        ->all();

    expect($tokens)->toBe(['device-token']);
});
