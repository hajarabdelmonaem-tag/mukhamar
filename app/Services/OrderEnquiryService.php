<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class OrderEnquiryService
{
    /** Shipping flat fee in SAR. */
    private const SHIPPING_FEE = 30;

    /** Free shipping subtotal threshold in SAR. */
    private const FREE_SHIPPING_THRESHOLD = 300;

    /** VAT rate applied after discount. */
    private const TAX_RATE = 0.15;

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

        $discount = 0.0;
        if ($cart->coupon && $cart->coupon->isValid($subtotal)) {
            $discount = $cart->coupon->discount_type === 'percentage'
                ? round($subtotal * ($cart->coupon->discount_value / 100), 2)
                : min((float) $cart->coupon->discount_value, $subtotal);
        }

        $taxable = round($subtotal - $discount, 2);
        $shipping = $subtotal >= self::FREE_SHIPPING_THRESHOLD ? 0.0 : self::SHIPPING_FEE;
        $tax = round($taxable * self::TAX_RATE, 2);
        $total = round($taxable + $shipping + $tax, 2);

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
            'summary' => [
                'subtotal' => round($subtotal, 2),
                'discount' => round($discount, 2),
                'taxable' => $taxable,
                'shipping' => $shipping,
                'shipping_free_threshold' => self::FREE_SHIPPING_THRESHOLD,
                'remaining_for_free_shipping' => $subtotal < self::FREE_SHIPPING_THRESHOLD
                    ? round(self::FREE_SHIPPING_THRESHOLD - $subtotal, 2)
                    : 0,
                'tax_rate' => self::TAX_RATE,
                'tax' => $tax,
                'total' => round($total, 2),
            ],
        ];
    }
}
