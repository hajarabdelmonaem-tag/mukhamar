<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
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
            'name' => $this->name,
            'unit' => $this->unit,
            'price' => round((float) $this->product->price + (float) $this->price_adjustment, 2),
            'price_adjustment' => (float) $this->price_adjustment,
            'sku' => $this->sku,
            'stock' => $this->stock,
            'in_stock' => $this->stock > 0,
            'is_default' => $this->is_default,
        ];
    }
}
