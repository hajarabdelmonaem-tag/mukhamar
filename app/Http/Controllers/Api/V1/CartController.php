<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AddCartItemRequest;
use App\Http\Requests\Api\V1\ApplyCouponRequest;
use App\Http\Requests\Api\V1\UpdateCartItemRequest;
use App\Http\Resources\Api\V1\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    /**
     * Show the authenticated user's cart with a full pricing summary.
     */
    public function show(Request $request): JsonResponse
    {
        $cart = $this->resolveCart($request);

        return $this->cartResponse($cart);
    }

    /**
     * Add an item to the cart.
     */
    public function addItem(AddCartItemRequest $request): JsonResponse
    {
        $cart = $this->resolveCart($request);

        /** @var ProductVariant|null $variant */
        $variant = ProductVariant::query()
            ->where('product_id', $request->product_id)
            ->when($request->product_variant_id, fn ($query, $id) => $query->where('id', $id))
            ->where('is_active', true)
            ->first();

        $item = $cart->items()->updateOrCreate(
            [
                'product_id' => $request->product_id,
                'product_variant_id' => $variant?->id,
            ],
            [
                'quantity' => $request->quantity,
            ]
        );

        return $this->cartResponse($cart, __('api.cart.item_added'));
    }

    /**
     * Update the quantity of a cart item.
     */
    public function updateItem(UpdateCartItemRequest $request, CartItem $item): JsonResponse
    {
        $this->authorizeCartItem($request, $item);

        $item->update(['quantity' => $request->quantity]);

        return $this->cartResponse($item->cart, __('api.cart.quantity_updated'));
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(Request $request, CartItem $item): JsonResponse
    {
        $this->authorizeCartItem($request, $item);

        $cart = $item->cart;
        $item->delete();

        return $this->cartResponse($cart, __('api.cart.item_removed'));
    }

    /**
     * Apply a coupon code to the cart.
     */
    public function applyCoupon(ApplyCouponRequest $request): JsonResponse
    {
        $cart = $this->resolveCart($request);

        $subtotal = $cart->items()->get()->sum(fn ($item) => $item->lineTotal());

        $coupon = Coupon::whereRaw('LOWER(code) = ?', [strtolower($request->code)])->first();

        if (! $coupon || ! $coupon->isValid((float) $subtotal)) {
            throw ValidationException::withMessages([
                'code' => [__('api.cart.coupon_invalid')],
            ]);
        }

        $cart->update(['coupon_id' => $coupon->id]);

        return $this->cartResponse($cart, __('api.cart.coupon_applied'));
    }

    /**
     * Remove the applied coupon from the cart.
     */
    public function removeCoupon(Request $request): JsonResponse
    {
        $cart = $this->resolveCart($request);
        $cart->update(['coupon_id' => null]);

        return $this->cartResponse($cart, __('api.cart.coupon_removed'));
    }

    /**
     * Resolve the authenticated user's cart, creating one if needed.
     */
    private function resolveCart(Request $request): Cart
    {
        return Cart::query()->firstOrCreate(['user_id' => $request->user()->id]);
    }

    /**
     * Ensure the cart item belongs to the authenticated user.
     */
    private function authorizeCartItem(Request $request, CartItem $item): void
    {
        abort_unless($item->cart->user_id === $request->user()->id, 403);
    }

    /**
     * Build a consistent cart JSON response.
     */
    private function cartResponse(Cart $cart, ?string $message = null): JsonResponse
    {
        $cart->load(['items.product.category', 'items.product.images', 'items.product.variants', 'items.variant', 'coupon']);

        $payload = [
            'data' => new CartResource($cart),
        ];

        if ($message) {
            $payload['message'] = $message;
        }

        return response()->json($payload);
    }
}
