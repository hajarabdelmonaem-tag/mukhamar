<?php

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);

    $this->product = Product::factory()->create(['price' => 100]);
});

it('builds the cart pricing summary from the configured settings', function (): void {
    Setting::query()->create(['key' => 'tax_rate', 'value' => 12]);
    Setting::query()->create(['key' => 'shipping_fee', 'value' => 45]);
    Setting::query()->create(['key' => 'free_shipping_threshold', 'value' => 500]);
    Setting::query()->create(['key' => 'currency', 'value' => 'OMR']);

    $this->postJson('/api/v1/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 2,
    ])->assertOk();

    $this->getJson('/api/v1/cart')
        ->assertOk()
        ->assertJsonPath('data.summary.subtotal', 200)
        ->assertJsonPath('data.summary.shipping', 45)
        ->assertJsonPath('data.summary.tax_rate', 0.12)
        ->assertJsonPath('data.summary.tax', 24)
        ->assertJsonPath('data.summary.total', 269)
        ->assertJsonPath('data.summary.shipping_free_threshold', 500)
        ->assertJsonPath('data.summary.remaining_for_free_shipping', 300)
        ->assertJsonPath('data.summary.currency', 'OMR');
});

it('applies free shipping once the subtotal reaches the configured threshold', function (): void {
    Setting::query()->create(['key' => 'tax_rate', 'value' => 12]);
    Setting::query()->create(['key' => 'shipping_fee', 'value' => 45]);
    Setting::query()->create(['key' => 'free_shipping_threshold', 'value' => 200]);

    $this->postJson('/api/v1/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 2,
    ])->assertOk();

    $this->getJson('/api/v1/cart')
        ->assertOk()
        ->assertJsonPath('data.summary.shipping', 0)
        ->assertJsonPath('data.summary.total', 224);
});

it('calculates order totals from the configured settings on checkout', function (): void {
    Setting::query()->create(['key' => 'tax_rate', 'value' => 10]);
    Setting::query()->create(['key' => 'shipping_fee', 'value' => 20]);
    Setting::query()->create(['key' => 'free_shipping_threshold', 'value' => 1000]);
    Setting::query()->create(['key' => 'currency', 'value' => 'JPY']);

    $address = Address::factory()->for($this->user)->create();

    $this->postJson('/api/v1/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 3,
    ])->assertOk();

    $this->postJson('/api/v1/orders', [
        'address_id' => $address->id,
        'payment_method' => 'VISA',
    ])->assertCreated();

    $this->assertDatabaseHas('orders', [
        'user_id' => $this->user->id,
        'subtotal' => 300,
        'shipping' => 20,
        'discount' => 0,
        'tax' => 30,
        'total' => 350,
    ]);

    expect(Order::query()->count())->toBe(1);
});
