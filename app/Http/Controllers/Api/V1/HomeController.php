<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\HomeResource;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * Return the aggregated data powering the home screen.
     */
    public function index(): JsonResponse
    {
        $banners = Banner::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        $home = [
            'banners' => $banners->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'image' => $banner->image,
                ];
            }),
            'categories' => Category::query()
                ->withCount('products')
                ->where('parent_id', null)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit(5)
                ->get(),
            'featured' => Product::query()
                ->with(['category', 'images', 'variants'])
                ->where('is_featured', true)
                ->where('is_active', true)
                ->latest()
                ->take(8)
                ->get(),
            'best_sellers' => Product::query()
                ->with(['category', 'images', 'variants'])
                ->where('is_active', true)
                ->where('reviews_count', '>', 0)
                ->orderByDesc('reviews_count')
                ->take(8)
                ->get(),
        ];

        return response()->json([
            'data' => new HomeResource($home),
        ]);
    }
}
