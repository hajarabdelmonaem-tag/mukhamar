<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(15);

        return view('admin.products.index', [
            'title' => __('admin.products.title'),
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', [
            'title' => __('admin.products.add'),
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'usage_instructions' => ['nullable', 'array'],
            'usage_instructions.en' => ['nullable', 'string'],
            'usage_instructions.ar' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'top_notes' => ['nullable', 'array'],
            'top_notes.en' => ['nullable', 'string'],
            'top_notes.ar' => ['nullable', 'string'],
            'heart_notes' => ['nullable', 'array'],
            'heart_notes.en' => ['nullable', 'string'],
            'heart_notes.ar' => ['nullable', 'string'],
            'base_notes' => ['nullable', 'array'],
            'base_notes.en' => ['nullable', 'string'],
            'base_notes.ar' => ['nullable', 'string'],
            'badges' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'variants' => ['nullable', 'array'],
            'variants.*.name.en' => ['required_with:variants', 'string', 'max:255'],
            'variants.*.name.ar' => ['required_with:variants', 'string', 'max:255'],
            'variants.*.unit.en' => ['nullable', 'string', 'max:50'],
            'variants.*.unit.ar' => ['nullable', 'string', 'max:50'],
            'variants.*.price_adjustment' => ['nullable', 'numeric'],
            'variants.*.sku' => ['nullable', 'string', 'max:255', 'unique:product_variants,sku'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.is_default' => ['boolean'],
            'variants.*.is_active' => ['boolean'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']['en']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        $product = Product::create($data);

        $this->saveImages($product, $request);
        $this->syncVariants($product, $request);

        return redirect()->route('admin.products.index')
            ->with('success', __('admin.products.created'));
    }

    /**
     * Show the form for editing a product.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', [
            'title' => __('admin.products.edit'),
            'product' => $product->load('variants'),
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,'.$product->id],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'usage_instructions' => ['nullable', 'array'],
            'usage_instructions.en' => ['nullable', 'string'],
            'usage_instructions.ar' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'top_notes' => ['nullable', 'array'],
            'top_notes.en' => ['nullable', 'string'],
            'top_notes.ar' => ['nullable', 'string'],
            'heart_notes' => ['nullable', 'array'],
            'heart_notes.en' => ['nullable', 'string'],
            'heart_notes.ar' => ['nullable', 'string'],
            'base_notes' => ['nullable', 'array'],
            'base_notes.en' => ['nullable', 'string'],
            'base_notes.ar' => ['nullable', 'string'],
            'badges' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variants.*.name.en' => ['required_with:variants', 'string', 'max:255'],
            'variants.*.name.ar' => ['required_with:variants', 'string', 'max:255'],
            'variants.*.unit.en' => ['nullable', 'string', 'max:50'],
            'variants.*.unit.ar' => ['nullable', 'string', 'max:50'],
            'variants.*.price_adjustment' => ['nullable', 'numeric'],
            'variants.*.sku' => ['nullable', 'string', 'max:255', 'unique:product_variants,sku,variants.*.id'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.is_default' => ['boolean'],
            'variants.*.is_active' => ['boolean'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']['en']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        $product->update($data);

        $this->saveImages($product, $request);
        $this->syncVariants($product, $request);

        return redirect()->route('admin.products.index')
            ->with('success', __('admin.products.updated'));
    }

    /**
     * Store any newly uploaded images, keeping the first one as primary when none exists.
     */
    private function saveImages(Product $product, Request $request): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $makePrimary = $product->images()->count() === 0;

        foreach ($request->file('images') as $file) {
            $product->images()->create([
                'path' => $file->store('products', 'public'),
                'alt' => ['en' => $product->getTranslation('name', 'en'), 'ar' => $product->getTranslation('name', 'ar')],
                'is_primary' => $makePrimary,
            ]);

            $makePrimary = false;
        }
    }

    /**
     * Sync variants submitted from the product form.
     */
    private function syncVariants(Product $product, Request $request): void
    {
        $submittedIds = [];

        foreach ($request->input('variants', []) as $row) {
            if (empty($row['name']['en'] ?? null)) {
                continue;
            }

            $submittedIds[] = $row['id'] ?? null;

            $variantData = [
                'product_id' => $product->id,
                'name' => ['en' => $row['name']['en'], 'ar' => $row['name']['ar'] ?? ''],
                'unit' => [
                    'en' => $row['unit']['en'] ?? null,
                    'ar' => $row['unit']['ar'] ?? null,
                ],
                'price_adjustment' => $row['price_adjustment'] ?? 0,
                'sku' => $row['sku'] ?? null,
                'stock' => $row['stock'] ?? 0,
                'is_default' => (bool) ($row['is_default'] ?? false),
                'is_active' => (bool) ($row['is_active'] ?? true),
            ];

            if (! empty($row['id']) && ProductVariant::where('id', $row['id'])->where('product_id', $product->id)->exists()) {
                ProductVariant::where('id', $row['id'])->update($variantData);
            } else {
                ProductVariant::create($variantData);
            }
        }

        ProductVariant::where('product_id', $product->id)
            ->whereNotIn('id', array_filter($submittedIds))
            ->delete();
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'variants', 'images', 'reviews' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('admin.products.show', [
            'title' => $product->getTranslation('name', app()->getLocale()),
            'product' => $product,
        ]);
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', __('admin.products.deleted'));
    }
}
