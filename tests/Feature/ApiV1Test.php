<?php

use App\Models\Address;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Intro;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

it('returns only products with the bestseller badge in home best sellers', function (): void {
    $this->product->update(['badges' => null]);
    $bestSeller = Product::factory()->create(['badges' => ['new', 'bestseller']]);
    Product::factory()->create(['badges' => ['sale']]);

    $response = $this->getJson('/api/v1/home');

    $response->assertOk()
        ->assertJsonCount(1, 'data.best_sellers')
        ->assertJsonPath('data.best_sellers.0.id', $bestSeller->id);
});

it('returns the full intro image url', function (): void {
    Intro::create([
        'title' => ['en' => 'Free shipping', 'ar' => 'شحن مجاني'],
        'image' => 'intros/shipping.jpg',
        'is_active' => true,
    ]);

    $this->getJson('/api/v1/intros')
        ->assertOk()
        ->assertJsonPath('data.0.image', asset('storage/intros/shipping.jpg'));
});

it('returns a null intro image when none is set', function (): void {
    Intro::create([
        'title' => ['en' => 'Free shipping', 'ar' => 'شحن مجاني'],
        'image' => null,
        'is_active' => true,
    ]);

    $this->getJson('/api/v1/intros')
        ->assertOk()
        ->assertJsonPath('data.0.image', null);
});

it('returns the full category image url', function (): void {
    $category = Category::factory()->create(['image' => 'categories/women.jpg']);

    $response = $this->getJson('/api/v1/categories')->assertOk();

    $position = collect($response->json('data'))->search(fn (array $item): bool => $item['id'] === $category->id);

    $response->assertJsonPath("data.$position.image", asset('storage/categories/women.jpg'));
});

it('returns a null category image when none is set', function (): void {
    $category = Category::factory()->create(['image' => null]);

    $response = $this->getJson('/api/v1/categories')->assertOk();

    $position = collect($response->json('data'))->search(fn (array $item): bool => $item['id'] === $category->id);

    $response->assertJsonPath("data.$position.image", null);
});

it('returns the full product image url', function (): void {
    ProductImage::factory()->for($this->product)->primary()->create(['path' => 'products/oud.jpg']);

    $this->getJson('/api/v1/products?category='.$this->product->category_id)
        ->assertOk()
        ->assertJsonPath('data.0.images.0.path', asset('storage/products/oud.jpg'))
        ->assertJsonPath('data.0.images.0.url', asset('storage/products/oud.jpg'))
        ->assertJsonPath('data.0.primary_image.url', asset('storage/products/oud.jpg'));
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

it('returns the full avatar url in the profile resource', function (): void {
    $this->user->update(['avatar' => 'avatars/avatar.jpg']);

    $this->getJson('/api/v1/profile')
        ->assertOk()
        ->assertJsonPath('user.avatar', asset('storage/avatars/avatar.jpg'));
});

it('keeps an already absolute avatar url untouched', function (): void {
    $this->user->update(['avatar' => 'https://cdn.example.com/avatar.jpg']);

    $this->getJson('/api/v1/profile')
        ->assertOk()
        ->assertJsonPath('user.avatar', 'https://cdn.example.com/avatar.jpg');
});

it('returns a null avatar when the user has none', function (): void {
    $this->user->update(['avatar' => null]);

    $this->getJson('/api/v1/profile')
        ->assertOk()
        ->assertJsonPath('user.avatar', null);
});

it('stores an uploaded avatar and returns its full url when registering', function (): void {
    Storage::fake('public');

    $response = $this->post('/api/v1/auth/register', [
        'name' => 'سارة أحمد',
        'email' => 'sara.avatar@example.com',
        'phone' => '+966 50 555 7777',
        'password' => 'password',
        'password_confirmation' => 'password',
        'accept_terms' => true,
        'avatar' => UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg'),
    ]);

    $response->assertCreated();

    $avatar = $response->json('user.avatar');

    Storage::disk('public')->assertExists(Str::after($avatar, 'storage/'));
    $this->assertStringStartsWith(asset('storage/avatars/'), $avatar);

    $this->assertDatabaseHas('users', ['email' => 'sara.avatar@example.com']);
});

it('rejects a register avatar that is not an image', function (): void {
    Storage::fake('public');

    $this->post('/api/v1/auth/register', [
        'name' => 'سارة أحمد',
        'email' => 'sara.notimage@example.com',
        'phone' => '+966 50 555 8888',
        'password' => 'password',
        'password_confirmation' => 'password',
        'accept_terms' => true,
        'avatar' => UploadedFile::fake()->create('avatar.txt', 10, 'text/plain'),
    ])->assertStatus(422)->assertJsonValidationErrors('avatar');

    $this->assertDatabaseMissing('users', ['email' => 'sara.notimage@example.com']);
});

it('returns a null avatar when registering without one', function (): void {
    $this->postJson('/api/v1/auth/register', [
        'name' => 'سارة أحمد',
        'email' => 'sara.noavatar@example.com',
        'phone' => '+966 50 555 9999',
        'password' => 'password',
        'password_confirmation' => 'password',
        'accept_terms' => true,
    ])->assertCreated()
        ->assertJsonPath('user.avatar', null);
});

it('stores an uploaded avatar and returns its full url when updating the profile', function (): void {
    Storage::fake('public');

    $response = $this->put('/api/v1/profile', [
        'name' => 'سارة',
        'avatar' => UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg'),
    ]);

    $response->assertOk();

    $avatar = $response->json('user.avatar');

    Storage::disk('public')->assertExists(Str::after($avatar, 'storage/'));
    $this->assertStringStartsWith(asset('storage/avatars/'), $avatar);
    $this->assertDatabaseHas('users', ['id' => $this->user->id]);
});

it('deletes the previous avatar when a new one is uploaded', function (): void {
    Storage::fake('public');
    $this->user->update(['avatar' => 'avatars/old-avatar.jpg']);
    Storage::disk('public')->put('avatars/old-avatar.jpg', 'old');

    $this->put('/api/v1/profile', [
        'avatar' => UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg'),
    ])->assertOk();

    Storage::disk('public')->assertMissing('avatars/old-avatar.jpg');
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
