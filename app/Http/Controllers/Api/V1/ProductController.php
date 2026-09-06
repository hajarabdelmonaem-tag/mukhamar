<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreReviewRequest;
use App\Http\Resources\Api\V1\ProductResource;
use App\Http\Resources\Api\V1\ReviewResource;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * List products with filtering, search, and sorting.
     */
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with(['category', 'images', 'variants'])
            ->where('is_active', true)
            ->when($request->category, fn ($query, $id) => $query->where('category_id', $id))
            ->when($request->search, fn ($query, $q) => $query->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            }))
            ->when($request->min_price, fn ($query, $min) => $query->where('price', '>=', $min))
            ->when($request->max_price, fn ($query, $max) => $query->where('price', '<=', $max))
            ->when($request->featured, fn ($query) => $query->where('is_featured', true))
            ->when($request->sort, function ($query, $sort) {
                match ($sort) {
                    'price_asc' => $query->orderBy('price'),
                    'price_desc' => $query->orderByDesc('price'),
                    'rating' => $query->orderByDesc('rating'),
                    'newest' => $query->latest(),
                    default => $query->latest(),
                };
            })
            ->paginate($request->integer('per_page', 12));

        return response()->json([
            'data' => ProductResource::collection($products),
            'meta' => self::paginationMeta($products),
        ]);
    }

    /**
     * Return the featured product carousel.
     */
    public function featured(): JsonResponse
    {
        $products = Product::query()
            ->with(['category', 'images', 'variants'])
            ->where('is_featured', true)
            ->where('is_active', true)
            ->take(10)
            ->get();

        return response()->json([
            'data' => ProductResource::collection($products),
        ]);
    }

    /**
     * Show a single product's full details.
     */
    public function show(Product $product): JsonResponse
    {
        $product->load([
            'category',
            'images',
            'variants',
            'reviews' => fn ($query) => $query->where('is_approved', true)->with('user'),
        ]);

        if (request()->user()) {
            $product->load([
                'wishlistedBy' => fn ($query) => $query->where('user_id', request()->user()->id),
            ]);
        }

        return response()->json([
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Submit a review for a product.
     */
    public function storeReview(StoreReviewRequest $request, Product $product): JsonResponse
    {
        $review = ProductReview::query()->updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
            ],
            [
                'rating' => $request->rating,
                'title' => $request->title,
                'body' => $request->body,
                'is_approved' => false,
            ]
        );

        self::recalculateProductRating($product);

        return response()->json([
            'message' => __('api.product.review_submitted'),
            'data' => new ReviewResource($review),
        ], 201);
    }

    /**
     * Recompute a product's average rating and review count.
     */
    private static function recalculateProductRating(Product $product): void
    {
        $aggregate = ProductReview::query()
            ->where('product_id', $product->id)
            ->where('is_approved', true)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();

        $product->forceFill([
            'rating' => round((float) ($aggregate->avg_rating ?? 0), 1),
            'reviews_count' => (int) ($aggregate->total ?? 0),
        ])->save();
    }

    /**
     * Extract pagination metadata from a length-aware paginator.
     *
     * @return array<string, mixed>
     */
    private static function paginationMeta(mixed $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }
}
