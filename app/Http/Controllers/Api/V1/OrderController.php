<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * List the authenticated user's orders.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with(['items', 'address'])
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Checkout: create an order from the user's cart.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = $request->user();

        $address = Address::query()
            ->where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->first();

        if (! $address) {
            throw ValidationException::withMessages([
                'address_id' => [__('api.order.address_invalid')],
            ]);
        }

        $cart = Cart::query()
            ->where('user_id', $user->id)
            ->with(['items.product', 'items.variant', 'coupon'])
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json([
                'message' => __('api.order.cart_empty'),
            ], 422);
        }

        $order = DB::transaction(function () use ($request, $user, $cart, $address) {
            $subtotal = $cart->items->sum(fn ($item) => $item->lineTotal());

            $discount = 0.0;
            if ($cart->coupon && $cart->coupon->isValid($subtotal)) {
                $discount = $cart->coupon->discount_type === 'percentage'
                    ? round($subtotal * ($cart->coupon->discount_value / 100), 2)
                    : min((float) $cart->coupon->discount_value, $subtotal);

                $cart->coupon->increment('used_count');
            }

            $shipping = $subtotal >= 300 ? 0 : 30;
            $tax = round(($subtotal - $discount) * 0.15, 2);
            $total = round($subtotal - $discount + $shipping + $tax, 2);

            $order = Order::query()->create([
                'order_no' => 'MKR-'.mt_rand(1000, 9999),
                'user_id' => $user->id,
                'address_id' => $address->id,
                'coupon_id' => $cart->coupon_id,
                'status' => Order::STATUS_PENDING,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'notes' => $request->notes,
                'placed_at' => now(),
            ]);

            foreach ($cart->items as $item) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product->name,
                    'variant_name' => $item->variant?->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->lineTotal() / $item->quantity,
                    'total' => $item->lineTotal(),
                ]);
            }

            $cart->items()->delete();
            $cart->update(['coupon_id' => null]);

            return $order->load(['items', 'address']);
        });

        return response()->json([
            'message' => __('api.order.created'),
            'data' => new OrderResource($order),
        ], 201);
    }

    /**
     * Show a single order belonging to the authenticated user.
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrder($request, $order);

        $order->load(['items.product', 'items.variant', 'address']);

        return response()->json([
            'data' => new OrderResource($order),
        ]);
    }

    /**
     * Return the invoice data for an order.
     */
    public function invoice(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrder($request, $order);

        $order->load(['items', 'address', 'user']);

        return response()->json([
            'data' => [
                'order' => new OrderResource($order),
                'billing' => [
                    'user' => [
                        'name' => $order->user->name,
                        'email' => $order->user->email,
                        'phone' => $order->user->phone,
                    ],
                    'address' => [
                        'recipient_name' => $order->address?->recipient_name,
                        'full_address' => $order->address
                            ? implode('، ', array_filter([
                                $order->address->street,
                                $order->address->district,
                                $order->address->city,
                            ]))
                            : null,
                        'phone' => $order->address?->phone,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Ensure the order belongs to the authenticated user.
     */
    private function authorizeOrder(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()->id, 403);
    }
}
