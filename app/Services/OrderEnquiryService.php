<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class OrderEnquiryService
{
    public function __construct(private PricingService $pricing) {}

    /**
     * Calculate the shipping cost and all financial data of the products in
     * the cart for the given delivery address.
     *
     * @return array<string, mixed>
     */
    public function calculate(User $user, int $addressId): array
    {
        $address = Address::query()
            ->where('id', $addressId)
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
            throw ValidationException::withMessages([
                'address_id' => [__('api.order.cart_empty')],
            ]);
        }

        $subtotal = $cart->items->sum(fn ($item) => $item->lineTotal());

        $summary = $this->pricing->summary($subtotal, $cart->coupon);

        return [
            'address_id' => $address->id,
            'address' => [
                'label' => $address->label,
                'city' => $address->city,
                'district' => $address->district,
                'street' => $address->street,
            ],
            'items' => $cart->items->map(function ($item): array {
                $unitPrice = (float) $item->product->price
                    + (float) ($item->variant->price_adjustment ?? 0);

                return [
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product->name,
                    'variant_name' => $item->variant?->name,
                    'quantity' => $item->quantity,
                    'unit_price' => round($unitPrice, 2),
                    'line_total' => $item->lineTotal(),
                ];
            })->values()->all(),
            'summary' => $summary,
        ];
    }
}
