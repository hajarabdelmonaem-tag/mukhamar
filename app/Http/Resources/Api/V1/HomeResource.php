<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
{
    /**
     * Transform the home aggregate resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $featured = $this->resource['featured'] ?? collect();
        $categories = $this->resource['categories'] ?? collect();
        $bestSellers = $this->resource['best_sellers'] ?? collect();

        return [
            'banners' => $this->resource['banners'] ?? [],
            'categories' => CategoryResource::collection($categories),
            'featured' => ProductResource::collection($featured),
            'best_sellers' => ProductResource::collection($bestSellers),
        ];
    }
}
