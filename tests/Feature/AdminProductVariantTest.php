<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
    $this->category = Category::factory()->create();
});

/**
 * @return array<string, mixed>
 */
function productPayload(array $overrides = []): array
{
    return array_merge([
        'category_id' => test()->category->id,
        'name' => ['en' => 'Oud Noir', 'ar' => 'عود نوير'],
        'description' => ['en' => 'Deep oud', 'ar' => 'عود عميق'],
        'price' => 250,
        'is_featured' => '1',
        'is_active' => '1',
    ], $overrides);
}

it('stores submitted variants with both locales', function (): void {
    $this->actingAs($this->admin)
        ->post(route('admin.products.store'), productPayload([
            'variants' => [
                [
                    'name' => ['en' => '50ml', 'ar' => '٥٠ مل'],
                    'unit' => ['en' => 'ml', 'ar' => 'مل'],
                    'price_adjustment' => 0,
                    'sku' => 'OUD-50',
                    'stock' => 10,
                    'is_default' => '1',
                    'is_active' => '1',
                ],
                [
                    'name' => ['en' => '100ml', 'ar' => '١٠٠ مل'],
                    'unit' => ['en' => 'ml', 'ar' => 'مل'],
                    'price_adjustment' => 120,
                    'sku' => '',
                    'stock' => 4,
                ],
            ],
        ]))
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHasNoErrors();

    $product = Product::firstOrFail();

    expect($product->variants)->toHaveCount(2);

    $fifty = $product->variants->firstWhere('sku', 'OUD-50');
    expect($fifty->getTranslation('name', 'en'))->toBe('50ml')
        ->and($fifty->getTranslation('name', 'ar'))->toBe('٥٠ مل')
        ->and($fifty->getTranslation('unit', 'en'))->toBe('ml')
        ->and($fifty->is_default)->toBeTrue()
        ->and($fifty->is_active)->toBeTrue()
        ->and($fifty->stock)->toBe(10);

    $hundred = $product->variants->firstWhere('sku', null);
    expect($hundred->getTranslation('name', 'en'))->toBe('100ml')
        ->and($hundred->getTranslation('unit', 'ar'))->toBe('مل')
        ->and((float) $hundred->price_adjustment)->toBe(120.0)
        ->and($hundred->stock)->toBe(4);
});

it('stores unchecked variant flags as disabled', function (): void {
    $this->actingAs($this->admin)
        ->post(route('admin.products.store'), productPayload([
            'variants' => [
                [
                    'name' => ['en' => '50ml', 'ar' => '٥٠ مل'],
                    'unit' => ['en' => '', 'ar' => ''],
                    'price_adjustment' => 0,
                    'sku' => '',
                    'stock' => 0,
                ],
            ],
        ]))
        ->assertSessionHasNoErrors();

    $variant = ProductVariant::firstOrFail();

    expect($variant->is_default)->toBeFalse()
        ->and($variant->is_active)->toBeFalse();
});

it('updates existing variants instead of duplicating them', function (): void {
    $product = Product::factory()->create(['category_id' => $this->category->id]);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'name' => ['en' => '30ml', 'ar' => '٣٠ مل'],
        'sku' => 'OUD-30',
    ]);

    $this->actingAs($this->admin)
        ->put(route('admin.products.update', $product), productPayload([
            'variants' => [
                [
                    'id' => $variant->id,
                    'name' => ['en' => '75ml', 'ar' => '٧٥ مل'],
                    'unit' => ['en' => 'ml', 'ar' => 'مل'],
                    'price_adjustment' => 50,
                    'sku' => 'OUD-30',
                    'stock' => 7,
                    'is_default' => '1',
                    'is_active' => '1',
                ],
            ],
        ]))
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHasNoErrors();

    expect($product->variants()->count())->toBe(1);

    $product->variants()->first()->refresh();

    expect($variant->fresh()->getTranslation('name', 'en'))->toBe('75ml')
        ->and($variant->fresh()->getTranslation('name', 'ar'))->toBe('٧٥ مل')
        ->and($variant->fresh()->getTranslation('unit', 'en'))->toBe('ml')
        ->and($variant->fresh()->stock)->toBe(7)
        ->and($variant->fresh()->is_default)->toBeTrue();
});

it('keeps only one default variant per product', function (): void {
    $this->actingAs($this->admin)
        ->post(route('admin.products.store'), productPayload([
            'variants' => [
                ['name' => ['en' => '30ml', 'ar' => '٣٠ مل'], 'is_default' => '1'],
                ['name' => ['en' => '50ml', 'ar' => '٥٠ مل'], 'is_default' => '1'],
            ],
        ]))
        ->assertSessionHasNoErrors();

    expect(ProductVariant::where('is_default', true)->count())->toBe(1);
});

it('deletes variants removed from the form', function (): void {
    $product = Product::factory()->create(['category_id' => $this->category->id]);
    $kept = ProductVariant::factory()->create(['product_id' => $product->id, 'sku' => 'KEEP-1']);
    $removed = ProductVariant::factory()->create(['product_id' => $product->id, 'sku' => 'DROP-1']);

    $this->actingAs($this->admin)
        ->put(route('admin.products.update', $product), productPayload([
            'variants' => [
                [
                    'id' => $kept->id,
                    'name' => ['en' => '30ml', 'ar' => '٣٠ مل'],
                    'sku' => 'KEEP-1',
                    'stock' => 3,
                ],
            ],
        ]))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('product_variants', ['id' => $kept->id]);
    $this->assertDatabaseMissing('product_variants', ['id' => $removed->id]);
});

it('rejects a sku already used by another variant', function (): void {
    ProductVariant::factory()->create(['sku' => 'TAKEN-1']);

    $this->actingAs($this->admin)
        ->post(route('admin.products.store'), productPayload([
            'variants' => [
                ['name' => ['en' => '30ml', 'ar' => '٣٠ مل'], 'sku' => 'TAKEN-1'],
            ],
        ]))
        ->assertSessionHasErrors('variants.0.sku');
});

it('does not attach another product variant to this product', function (): void {
    $product = Product::factory()->create(['category_id' => $this->category->id]);
    $otherProduct = Product::factory()->create(['category_id' => $this->category->id]);
    $foreignVariant = ProductVariant::factory()->create(['product_id' => $otherProduct->id]);

    $this->actingAs($this->admin)
        ->put(route('admin.products.update', $product), productPayload([
            'variants' => [
                [
                    'id' => $foreignVariant->id,
                    'name' => ['en' => '99ml', 'ar' => '٩٩ مل'],
                    'sku' => 'NEW-1',
                ],
            ],
        ]))
        ->assertSessionHasNoErrors();

    expect($foreignVariant->fresh()->product_id)->toBe($otherProduct->id);
    expect($product->variants()->where('sku', 'NEW-1')->exists())->toBeTrue();
});
