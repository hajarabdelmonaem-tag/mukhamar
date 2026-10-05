<?php

namespace App\Http\Resources\Api\V1;

use App\Helpers\MediaHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->localizedName(),
            'icon' => $this->iconUrl(),
            'link' => $this->resource['link'] ?? null,
        ];
    }

    /**
     * The complete public URL of the uploaded icon image.
     */
    private function iconUrl(): ?string
    {
        return MediaHelper::toUrl($this->resource['icon'] ?? null);
    }

    /**
     * Pick the translated name for the current locale.
     *
     * @param  array<string, string>  $name
     */
    private function localizedName(): ?string
    {
        $name = $this->resource['name'] ?? [];
        $locale = app()->getLocale();

        return $name[$locale] ?? $name['en'] ?? null;
    }
}
