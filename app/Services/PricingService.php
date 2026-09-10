<?php

namespace App\Services;

use App\Models\Coupon;

class PricingService
{
    public function __construct(private SettingsService $settings) {}

    /**
     * Calculate coupon discount for the given subtotal.
     */
    public function discount(float $subtotal, ?Coupon $coupon): float
    {
        if (! $coupon || ! $coupon->isValid($subtotal)) {
            return 0.0;
        }

        return $coupon->discount_type === 'percentage'
            ? round($subtotal * ($coupon->discount_value / 100), 2)
            : min((float) $coupon->discount_value, $subtotal);
    }

    /**
     * Build the full pricing summary from the settings.
     *
     * @return array<string, mixed>
     */
    public function summary(float $subtotal, ?Coupon $coupon = null): array
    {
        $subtotal = round($subtotal, 2);
        $discount = round($this->discount($subtotal, $coupon), 2);
        $taxable = round($subtotal - $discount, 2);

        $threshold = $this->settings->freeShippingThreshold();
        $shipping = $taxable > 0 && $taxable >= $threshold ? 0.0 : $this->settings->shippingFee();

        $taxRate = $this->settings->taxRate() / 100;
        $tax = $taxable > 0 ? round($taxable * $taxRate, 2) : 0.0;
        $total = round($taxable + $shipping + $tax, 2);

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'taxable' => $taxable,
            'shipping' => round($shipping, 2),
            'shipping_free_threshold' => $threshold,
            'remaining_for_free_shipping' => $subtotal < $threshold ? round($threshold - $subtotal, 2) : 0,
            'tax_rate' => round($this->settings->taxRate() / 100, 4),
            'tax' => $tax,
            'total' => $total,
            'currency' => $this->settings->currency(),
        ];
    }
}
