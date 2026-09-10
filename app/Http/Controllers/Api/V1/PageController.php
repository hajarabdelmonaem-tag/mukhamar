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
        return $this->show('terms_conditions');
    }

    /**
     * Get the privacy policy from settings.
     */
    public function policy(): JsonResponse
    {
        return $this->show('privacy_policy');
    }

    /**
     * Return a single static page by settings key.
     */
    private function show(string $key): JsonResponse
    {
        $document = Setting::query()->where('key', $key)->value('value')
            ?? Setting::query()->where('key', $this->legacyKey($key))->value('value');

        if (! $document) {
            return response()->json([
                'data' => null,
            ], 404);
        }

        return response()->json([
            'data' => [
                'title' => $this->localize($document['title'] ?? []),
                'sections' => collect($document['sections'] ?? [])->map(fn (array $section): array => [
                    'heading' => $this->localize($section['heading'] ?? []),
                    'body' => $this->localize($section['body'] ?? []),
                ])->values()->all(),
            ],
        ]);
    }

    /**
     * The legacy settings key that stored this page before the refactor.
     */
    private function legacyKey(string $key): ?string
    {
        return match ($key) {
            'terms_conditions' => 'terms',
            'privacy_policy' => 'policy',
            default => null,
        };
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
