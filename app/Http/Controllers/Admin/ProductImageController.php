<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Delete a product image and reassign the primary flag if needed.
     */
    public function destroy(ProductImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->path);

        $wasPrimary = $image->is_primary;
        $product = $image->product;

        $image->delete();

        if ($wasPrimary) {
            $replacement = $product->images()->orderBy('sort_order')->orderBy('id')->first();
            $replacement?->update(['is_primary' => true]);
        }

        return redirect()->route('admin.products.edit', $product)
            ->with('success', __('admin.products.image_deleted'));
    }

    /**
     * Mark an image as the product's primary image.
     */
    public function setPrimary(ProductImage $image): RedirectResponse
    {
        $product = $image->product;

        $product->images()->update(['is_primary' => false, 'sort_order' => 0]);
        $image->update(['is_primary' => true, 'sort_order' => 0]);

        return redirect()->route('admin.products.edit', $product)
            ->with('success', __('admin.products.primary_image_updated'));
    }
}
