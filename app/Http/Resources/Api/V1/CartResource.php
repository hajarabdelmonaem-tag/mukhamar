<?php

namespace App\Http\Resources\Api\V1;

use App\Services\PricingService;
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

        $summary = app(PricingService::class)->summary($subtotal, $this->coupon ?? null);

        return [
            'id' => $this->id,
            'items_count' => $itemsLoaded ? $items->sum('quantity') : 0,
            'items' => CartItemResource::collection($items),
            'coupon' => new CouponResource($this->whenLoaded('coupon')),
            'summary' => $summary,
        ];
    }
}
