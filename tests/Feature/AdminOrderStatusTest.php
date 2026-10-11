<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
    $this->order = Order::factory()->create(['status' => Order::STATUS_PENDING]);
});

it('accepts confirmed as a status and stamps the confirmed date', function (): void {
    $this->actingAs($this->admin)
        ->from(route('admin.orders.show', $this->order))
        ->put(route('admin.orders.update-status', $this->order), [
            'status' => Order::STATUS_CONFIRMED,
        ])
        ->assertRedirect(route('admin.orders.show', $this->order));

    $this->order->refresh();

    expect($this->order->status)->toBe(Order::STATUS_CONFIRMED)
        ->and($this->order->confirmed_at)->not->toBeNull();
});

it('stamps the shipped date when moving to in transit', function (): void {
    $this->actingAs($this->admin)
        ->from(route('admin.orders.show', $this->order))
        ->put(route('admin.orders.update-status', $this->order), [
            'status' => Order::STATUS_IN_TRANSIT,
        ]);

    $this->order->refresh();

    expect($this->order->status)->toBe(Order::STATUS_IN_TRANSIT)
        ->and($this->order->shipped_at)->not->toBeNull();
});

it('stamps the delivered date when moving to delivered', function (): void {
    $this->actingAs($this->admin)
        ->from(route('admin.orders.show', $this->order))
        ->put(route('admin.orders.update-status', $this->order), [
            'status' => Order::STATUS_DELIVERED,
        ]);

    $this->order->refresh();

    expect($this->order->status)->toBe(Order::STATUS_DELIVERED)
        ->and($this->order->delivered_at)->not->toBeNull();
});

it('stamps the cancelled date when the order is cancelled', function (): void {
    $this->actingAs($this->admin)
        ->from(route('admin.orders.show', $this->order))
        ->put(route('admin.orders.update-status', $this->order), [
            'status' => Order::STATUS_CANCELLED,
        ]);

    $this->order->refresh();

    expect($this->order->status)->toBe(Order::STATUS_CANCELLED)
        ->and($this->order->cancelled_at)->not->toBeNull();
});

it('keeps the original confirmed date when the status is set again', function (): void {
    $confirmedAt = now()->subDays(5);

    $this->order->update([
        'status' => Order::STATUS_CONFIRMED,
        'confirmed_at' => $confirmedAt,
    ]);

    $this->actingAs($this->admin)
        ->from(route('admin.orders.show', $this->order))
        ->put(route('admin.orders.update-status', $this->order), [
            'status' => Order::STATUS_CONFIRMED,
        ]);

    expect($this->order->refresh()->confirmed_at->format('Y-m-d H:i:s'))
        ->toBe($confirmedAt->format('Y-m-d H:i:s'));
});

it('rejects an unsupported status', function (): void {
    $this->actingAs($this->admin)
        ->from(route('admin.orders.show', $this->order))
        ->put(route('admin.orders.update-status', $this->order), [
            'status' => 'refunded',
        ])
        ->assertSessionHasErrors('status');

    expect($this->order->refresh()->status)->toBe(Order::STATUS_PENDING);
});
