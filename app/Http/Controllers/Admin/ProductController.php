<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
        $data = $request->validate($this->rules($request));

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']['en']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        DB::transaction(function () use ($data, $request): void {
            $product = Product::create($data);

            $this->saveImages($product, $request);
            $this->syncVariants($product, $request->input('variants', []));
        });

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
        $data = $request->validate($this->rules($request, $product));

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']['en']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        DB::transaction(function () use ($data, $product, $request): void {
            $product->update($data);

            $this->saveImages($product, $request);
            $this->syncVariants($product, $request->input('variants', []));
        });

        return redirect()->route('admin.products.index')
            ->with('success', __('admin.products.updated'));
    }

    /**
     * The validation rules shared by the store and update actions.
     *
     * @return array<string, mixed>
     */
    private function rules(Request $request, ?Product $product = null): array
    {
        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($product?->id),
            ],
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
            'variants.*.price_adjustment' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.is_default' => ['boolean'],
            'variants.*.is_active' => ['boolean'],
        ];

        $ownedVariantIds = $product?->variants()->pluck('id')->map(fn ($id) => (int) $id)->all() ?? [];

        foreach ($request->input('variants', []) as $index => $variant) {
            $rules["variants.$index.sku"] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('product_variants', 'sku')->ignore(
                    in_array((int) ($variant['id'] ?? 0), $ownedVariantIds, true) ? (int) $variant['id'] : null,
                ),
            ];
        }

        return $rules;
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
     *
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function syncVariants(Product $product, array $rows): void
    {
        $persisted = $product->variants()->get()->keyBy('id');
        $keptIds = [];
        $hasDefault = false;

        foreach ($rows as $row) {
            if (blank($row['name']['en'] ?? null)) {
                continue;
            }

            $variantId = (int) ($row['id'] ?? 0);
            $variant = $persisted->has($variantId)
                ? $persisted->get($variantId)
                : new ProductVariant(['product_id' => $product->id]);

            $isDefault = ! $hasDefault && (bool) ($row['is_default'] ?? false);
            $hasDefault = $hasDefault || $isDefault;

            $variant->fill([
                'name' => ['en' => $row['name']['en'], 'ar' => $row['name']['ar'] ?? ''],
                'unit' => [
                    'en' => $row['unit']['en'] ?? null,
                    'ar' => $row['unit']['ar'] ?? null,
                ],
                'price_adjustment' => $row['price_adjustment'] ?? 0,
                'sku' => $row['sku'] ?? null,
                'stock' => $row['stock'] ?? 0,
                'is_default' => $isDefault,
                'is_active' => (bool) ($row['is_active'] ?? false),
            ]);
            $variant->save();

            $keptIds[] = $variant->id;
        }

        $product->variants()->whereNotIn('id', $keptIds)->delete();
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
