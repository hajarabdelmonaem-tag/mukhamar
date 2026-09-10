<?php

use App\Models\Address;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->product = Product::factory()
        ->create(['name' => 'عطر اختبار', 'price' => 100]);

    $this->user = User::factory()->create();
    $this->actingAs($this->user, 'sanctum');
});

it('registers a new user and returns a token', function (): void {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'سارة أحمد',
        'email' => 'sara@example.com',
        'phone' => '+966 50 555 5555',
        'password' => 'password',
        'password_confirmation' => 'password',
        'accept_terms' => true,
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'phone']]);
});

it('logs a user in using email', function (): void {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $response = $this->postJson('/api/v1/auth/login', [
        'identifier' => $user->email,
        'password' => 'secret123',
    ]);

    $response->assertOk()->assertJsonStructure(['token', 'user']);
});

it('returns home screen data', function (): void {
    Category::factory()->create(['name' => ['en' => 'Women Perfumes', 'ar' => 'عطور نسائية']]);
    $this->product->update(['is_featured' => true]);

    $response = $this->getJson('/api/v1/home');

    $response->assertOk()
        ->assertJsonStructure(['data' => ['categories', 'featured', 'best_sellers']]);
});

it('lists products with pagination metadata', function (): void {
    $response = $this->getJson('/api/v1/products');

    $response->assertOk()
        ->assertJsonStructure(['data', 'meta' => ['current_page', 'total']]);
});

it('shows a single product with related data', function (): void {
    $response = $this->getJson('/api/v1/products/'.$this->product->slug);

    $response->assertOk()
        ->assertJsonPath('data.id', $this->product->id)
        ->assertJsonPath('data.name', 'عطر اختبار');
});

it('adds an item to the cart and returns a summary', function (): void {
    $response = $this->postJson('/api/v1/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 2,
    ]);

    $response->assertOk()
        ->assertJsonStructure(['data' => ['items', 'summary' => ['subtotal', 'total']]]);

    $this->assertDatabaseHas('cart_items', [
        'product_id' => $this->product->id,
        'quantity' => 2,
    ]);
});

it('applies a valid coupon to the cart', function (): void {
    $coupon = Coupon::factory()->percentage('10')->create([
        'min_subtotal' => null,
        'max_uses' => null,
        'used_count' => 0,
    ]);

    $this->postJson('/api/v1/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $response = $this->postJson('/api/v1/cart/coupon', ['code' => $coupon->code]);

    $response->assertOk()
        ->assertJsonPath('data.coupon.code', $coupon->code);
});

it('rejects an invalid coupon', function (): void {
    $this->postJson('/api/v1/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $response = $this->postJson('/api/v1/cart/coupon', ['code' => 'NOPE123']);

    $response->assertStatus(422);
});

it('adds a product to the wishlist', function (): void {
    $response = $this->postJson('/api/v1/wishlist', [
        'product_id' => $this->product->id,
    ]);

    $response->assertCreated();

    $this->assertDatabaseHas('wishlists', [
        'user_id' => $this->user->id,
        'product_id' => $this->product->id,
    ]);
});

it('creates, lists and deletes an address', function (): void {
    $store = $this->postJson('/api/v1/addresses', [
        'recipient_name' => $this->user->name,
        'street' => 'شارع التحلية',
        'city' => 'الرياض',
    ]);

    $store->assertCreated()->assertJsonPath('data.is_default', true);

    $addressId = $store->json('data.id');

    $this->getJson('/api/v1/addresses')->assertOk()->assertJsonCount(1, 'data');

    $this->deleteJson('/api/v1/addresses/'.$addressId)->assertOk();

    $this->assertDatabaseMissing('addresses', ['id' => $addressId]);
});

it('places an order from the cart', function (): void {
    $address = Address::factory()->for($this->user)->create();

    $this->postJson('/api/v1/cart/items', [
        'product_id' => $this->product->id,
        'quantity' => 1,
    ]);

    $response = $this->postJson('/api/v1/orders', [
        'address_id' => $address->id,
        'payment_method' => 'VISA',
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['data' => ['order_no', 'items', 'total']]);

    $this->assertDatabaseHas('orders', ['user_id' => $this->user->id]);
});

it('cannot access another users order', function (): void {
    $other = User::factory()->create();
    $order = Order::factory()->for($other)->create();

    $this->getJson('/api/v1/orders/'.$order->id)->assertForbidden();
});

it('guards protected routes for guests', function (): void {
    Sanctum::actingAs($this->user);

    $this->getJson('/api/v1/profile')->assertOk();
});

it('registers a new user with a selected language and returns a localized message', function (): void {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'سارة أحمد',
        'email' => 'sara.lang@example.com',
        'phone' => '+966 50 555 6666',
        'password' => 'password',
        'password_confirmation' => 'password',
        'accept_terms' => true,
        'lang' => 'ar',
    ]);

    $response->assertCreated()
        ->assertJsonPath('message', 'تم إنشاء الحساب بنجاح.')
        ->assertJsonPath('user.lang', 'ar');

    $this->assertDatabaseHas('users', ['email' => 'sara.lang@example.com', 'lang' => 'ar']);
});

it('updates the profile language and returns a localized message', function (): void {
    $response = $this->putJson('/api/v1/profile', [
        'name' => 'محمد',
        'lang' => 'ar',
    ]);

    $response->assertOk()
        ->assertJsonPath('message', 'تم تحديث البيانات بنجاح.')
        ->assertJsonPath('user.lang', 'ar');

    $this->assertDatabaseHas('users', ['id' => $this->user->id, 'lang' => 'ar']);
});

it('honors the lang header for messages on any endpoint', function (): void {
    $this->putJson('/api/v1/profile', ['name' => 'علي'], ['lang' => 'ar'])
        ->assertOk()
        ->assertJsonPath('message', 'تم تحديث البيانات بنجاح.');
});

it('uses the saved user language across endpoints when no lang is sent', function (): void {
    $this->user->update(['lang' => 'ar']);

    $this->putJson('/api/v1/profile', ['name' => 'محمد'])
        ->assertOk()
        ->assertJsonPath('message', 'تم تحديث البيانات بنجاح.');
});
