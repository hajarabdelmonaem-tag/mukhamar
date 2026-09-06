<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreWishlistRequest;
use App\Http\Resources\Api\V1\WishlistResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * List the authenticated user's wishlist.
     */
    public function index(Request $request): JsonResponse
    {
        $wishlist = Wishlist::query()
            ->with(['product' => fn ($query) => $query->with(['category', 'images', 'variants'])])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => WishlistResource::collection($wishlist),
        ]);
    }

    /**
     * Add a product to the authenticated user's wishlist.
     */
    public function store(StoreWishlistRequest $request): JsonResponse
    {
        $wishlist = Wishlist::query()->firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
        ]);

        return response()->json([
            'message' => __('api.wishlist.added'),
            'data' => new WishlistResource($wishlist),
        ], 201);
    }

    /**
     * Remove a product from the authenticated user's wishlist.
     */
    public function destroy(Request $request, Product $product): JsonResponse
    {
        $deleted = Wishlist::query()
            ->where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->delete();

        if (! $deleted) {
            return response()->json([
                'message' => __('api.wishlist.not_found'),
            ], 404);
        }

        return response()->json([
            'message' => __('api.wishlist.removed'),
        ]);
    }
}
