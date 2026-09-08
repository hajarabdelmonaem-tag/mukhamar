<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SocialController extends Controller
{
    /**
     * List the active social links from settings.
     */
    public function index(): JsonResponse
    {
        $socials = Setting::query()->where('key', 'socials')->value('value') ?? [];

        $localized = array_map(function (array $social): array {
            return [
                'name' => $this->localize($social['name'] ?? []),
                'icon' => $social['icon'] ?? null,
                'link' => $social['link'] ?? null,
            ];
        }, $socials);

        return response()->json([
            'data' => $localized,
        ]);
    }

    /**
     * Pick the translated value for the current locale.
     *
     * @param  array<string, string>  $value
     */
    private function localize(array $value): ?string
    {
        $locale = app()->getLocale();

        return $value[$locale] ?? $value['en'] ?? null;
    }
}
