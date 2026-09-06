<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'discount_type' => $this->discount_type,
            'discount_value' => (float) $this->discount_value,
            'min_subtotal' => $this->min_subtotal !== null ? (float) $this->min_subtotal : null,
            'expires_at' => $this->expires_at?->toISOString(),
        ];
    }
}
