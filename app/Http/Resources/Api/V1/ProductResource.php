<?php

namespace App\Http\Resources\Api\V1;

use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'usage_instructions' => $this->usage_instructions,
            'price' => (float) $this->price,
            'old_price' => $this->old_price !== null ? (float) $this->old_price : null,
            'currency' => app(SettingsService::class)->currency()?? 'SAR',
            'has_discount' => $this->old_price !== null,
            'discount_percentage' => $this->discountPercentage(),
            'rating' => (float) $this->rating,
            'reviews_count' => $this->reviews_count,
            'fragrance_pyramid' => [
                'top' => $this->top_notes,
                'heart' => $this->heart_notes,
                'base' => $this->base_notes,
            ],
            'badges' => $this->badges,
            'is_featured' => $this->is_featured,
            'is_wishlisted' => $request->user() ? $this->relationLoaded('wishlistedBy')
                ? $this->wishlistedBy->contains(fn ($user) => $user->id === $request->user()->id)
                : false
                : false,
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'primary_image' => $this->when($this->relationLoaded('images'), function () {
                $primary = $this->images->firstWhere('is_primary', true) ?? $this->images->first();

                return $primary ? new ProductImageResource($primary) : null;
            }),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            // 'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }

    /**
     * Compute the discount percentage off the old price.
     */
    private function discountPercentage(): ?int
    {
        if ($this->old_price === null || $this->old_price <= 0) {
            return null;
        }

        return (int) round((($this->old_price - $this->price) / $this->old_price) * 100);
    }
}
