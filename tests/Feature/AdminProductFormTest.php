<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
    $this->category = Category::factory()->create();
});

it('reacts to badge clicks on the create form', function (): void {
    $response = $this->actingAs($this->admin)->get(route('admin.products.create'));

    $response->assertOk();

    $html = $response->getContent();

    foreach (['new', 'sale', 'bestseller', 'hot'] as $badge) {
        expect($html)->toContain('x-data="{ checked: false }"');
    }

    expect(substr_count($html, 'x-model="checked"'))->toBe(4)
        ->and($html)->toContain(":class=\"checked ? 'border-brand-500 bg-brand-500'");
});

it('marks already selected badges as checked on the create form', function (): void {
    $this->actingAs($this->admin)
        ->from(route('admin.products.create'))
        ->post(route('admin.products.store'), [
            'category_id' => $this->category->id,
            'name' => ['en' => 'Oud'],
            'price' => 100,
            'badges' => ['new', 'bestseller'],
        ])
        ->assertSessionHasErrors('name.ar');

    $html = $this->actingAs($this->admin)->get(route('admin.products.create'))->getContent();

    expect($html)->toContain('value="new" checked x-model="checked"')
        ->and($html)->toContain('value="bestseller" checked x-model="checked"');
});

it('keeps the featured and active alpine state in sync with old input on the create form', function (): void {
    $this->actingAs($this->admin)
        ->from(route('admin.products.create'))
        ->post(route('admin.products.store'), [
            'category_id' => $this->category->id,
            'name' => ['en' => 'Oud'],
            'price' => 100,
            'is_featured' => '1',
            'is_active' => '0',
        ])
        ->assertSessionHasErrors('name.ar');

    $html = $this->actingAs($this->admin)->get(route('admin.products.create'))->getContent();

    expect($html)->toContain('x-data="{ isFeatured: true }"')
        ->and($html)->toContain('x-data="{ isActive: false }"')
        ->and($html)->toContain('name="is_featured" value="1" checked')
        ->and($html)->not->toContain('name="is_active" value="1" checked');
});

it('marks persisted badges as checked on the edit form', function (): void {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'badges' => ['sale', 'hot'],
    ]);

    $html = $this->actingAs($this->admin)->get(route('admin.products.edit', $product))->getContent();

    expect($html)->toContain('value="sale" checked x-model="checked"')
        ->and($html)->toContain('value="hot" checked x-model="checked"')
        ->and(substr_count($html, 'x-model="checked"'))->toBe(4);
});

it('keeps the active alpine state in sync when the product is inactive', function (): void {
    $product = Product::factory()->inactive()->create(['category_id' => $this->category->id]);

    $html = $this->actingAs($this->admin)->get(route('admin.products.edit', $product))->getContent();

    expect($html)->toContain('x-data="{ isActive: false }"')
        ->and($html)->not->toContain('name="is_active" value="1" checked');
});
