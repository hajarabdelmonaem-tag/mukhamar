<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $items = $this->whenLoaded('items');
        $itemsLoaded = $items instanceof Collection;
        $subtotal = $itemsLoaded
            ? $items->sum(fn ($item) => $item->lineTotal())
            : (float) ($this->subtotal ?? 0);

        $discount = 0.0;
        if ($this->relationLoaded('coupon') && $this->coupon) {
            $coupon = $this->coupon;
            $discount = $coupon->discount_type === 'percentage'
                ? round($subtotal * ($coupon->discount_value / 100), 2)
                : min((float) $coupon->discount_value, $subtotal);
        }

        $shipping = $subtotal > 0 && $subtotal >= 300 ? 0 : 30;
        $tax = round(($subtotal - $discount) * 0.15, 2);
        $total = round($subtotal - $discount + $shipping + $tax, 2);

        return [
            'id' => $this->id,
            'items_count' => $itemsLoaded ? $items->sum('quantity') : 0,
            'items' => CartItemResource::collection($items),
            'coupon' => new CouponResource($this->whenLoaded('coupon')),
            'summary' => [
                'subtotal' => round($subtotal, 2),
                'discount' => round($discount, 2),
                'shipping' => $shipping,
                'shipping_free_threshold' => 300,
                'remaining_for_free_shipping' => $subtotal < 300 ? round(300 - $subtotal, 2) : 0,
                'tax_rate' => 0.15,
                'tax' => $tax,
                'total' => $total,
            ],
        ];
    }
}
