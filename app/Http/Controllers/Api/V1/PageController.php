<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    /**
     * Get the terms and conditions from settings.
     */
    public function terms(): JsonResponse
    {
        return $this->show('terms');
    }

    /**
     * Get the privacy policy from settings.
     */
    public function policy(): JsonResponse
    {
        return $this->show('policy');
    }

    /**
     * Return a single static page by settings key.
     */
    private function show(string $key): JsonResponse
    {
        $page = Setting::query()->where('key', $key)->value('value');

        if (! $page) {
            return response()->json([
                'data' => null,
            ], 404);
        }

        return response()->json([
            'data' => [
                'title' => $this->localize($page['title'] ?? []),
                'sections' => collect($page['sections'] ?? [])->map(fn (array $section): array => [
                    'heading' => $this->localize($section['heading'] ?? []),
                    'body' => $this->localize($section['body'] ?? []),
                ])->all(),
            ],
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
