<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * List all active product categories.
     */
    public function index(Request $request): JsonResponse
    {
        $categories = Category::query()
            ->withCount('products')
            ->where('is_active', true)
            ->when($request->filled('parent_id'), fn ($query, $value) => $query->where('parent_id', $value), fn ($query) => $query->whereNull('parent_id'))
            ->when($request->type, fn ($query, $type) => $query->where('type', $type))
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }

    /**
     * List the products belonging to a category.
     */
    public function products(Request $request, Category $category): JsonResponse
    {
        $products = $category->products()
            ->with(['category', 'images', 'variants'])
            ->where('is_active', true)
            ->when($request->q, fn ($query, $q) => $query->where('name', 'like', "%{$q}%"))
            ->when($request->sort, function ($query, $sort) {
                match ($sort) {
                    'price_asc' => $query->orderBy('price'),
                    'price_desc' => $query->orderByDesc('price'),
                    'rating' => $query->orderByDesc('rating'),
                    default => $query->latest(),
                };
            })
            ->paginate($request->integer('per_page', 12));

        return response()->json([
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }
}
