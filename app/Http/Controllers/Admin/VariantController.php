<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    /**
     * Display a listing of variants.
     */
    public function index()
    {
        $variants = ProductVariant::with('product')->latest()->paginate(15);

        return view('admin.variants.index', [
            'title' => __('admin.variants.title'),
            'variants' => $variants,
        ]);
    }

    /**
     * Show the form for creating a new variant.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();

        return view('admin.variants.create', [
            'title' => __('admin.variants.add'),
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created variant.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'array'],
            'unit.en' => ['nullable', 'string', 'max:50'],
            'unit.ar' => ['nullable', 'string', 'max:50'],
            'price_adjustment' => ['nullable', 'numeric'],
            'sku' => ['nullable', 'string', 'max:255'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $data['is_default'] = $request->boolean('is_default');
        $data['is_active'] = $request->boolean('is_active');

        ProductVariant::create($data);

        return redirect()->route('admin.variants.index')
            ->with('success', __('admin.variants.created'));
    }

    /**
     * Show the form for editing a variant.
     */
    public function edit(ProductVariant $variant)
    {
        $products = Product::orderBy('name')->get();

        return view('admin.variants.edit', [
            'title' => __('admin.variants.edit'),
            'variant' => $variant,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified variant.
     */
    public function update(Request $request, ProductVariant $variant)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'array'],
            'unit.en' => ['nullable', 'string', 'max:50'],
            'unit.ar' => ['nullable', 'string', 'max:50'],
            'price_adjustment' => ['nullable', 'numeric'],
            'sku' => ['nullable', 'string', 'max:255'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $data['is_default'] = $request->boolean('is_default');
        $data['is_active'] = $request->boolean('is_active');

        $variant->update($data);

        return redirect()->route('admin.variants.index')
            ->with('success', __('admin.variants.updated'));
    }

    /**
     * Remove the specified variant.
     */
    public function destroy(ProductVariant $variant)
    {
        $variant->delete();

        return redirect()->route('admin.variants.index')
            ->with('success', __('admin.variants.deleted'));
    }
}
