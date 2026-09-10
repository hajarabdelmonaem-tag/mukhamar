<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Show the settings form.
     */
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();

        return view('admin.settings.index', [
            'title' => __('admin.settings.title'),
            'settings' => $settings,
            'documents' => [
                'privacy_policy' => $this->normalizeDocumentForView(
                    $settings['privacy_policy'] ?? null,
                    $settings['policy'] ?? null
                ),
                'terms_conditions' => $this->normalizeDocumentForView(
                    $settings['terms_conditions'] ?? null,
                    $settings['terms'] ?? null
                ),
            ],
        ]);
    }

    /**
     * Normalize a stored legal document for the editor view, falling back to
     * the legacy key and/or the old single-body shape.
     *
     * @return array<string, mixed>
     */
    private function normalizeDocumentForView(mixed $document, mixed $legacy = null): array
    {
        $document = $document ?: $legacy;

        if (! is_array($document)) {
            return ['title' => ['en' => '', 'ar' => ''], 'sections' => []];
        }

        if (! isset($document['title']) && (isset($document['en']) || isset($document['ar']))) {
            $document = [
                'title' => ['en' => '', 'ar' => ''],
                'sections' => [[
                    'heading' => ['en' => '', 'ar' => ''],
                    'body' => [
                        'en' => $document['en'] ?? '',
                        'ar' => $document['ar'] ?? '',
                    ],
                ]],
            ];
        }

        return $this->normalizeDocument($document);
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => ['nullable', 'string', 'max:255'],
            'site_email' => ['nullable', 'email', 'max:255'],
            'site_phone' => ['nullable', 'string', 'max:50'],
            'site_logo' => ['nullable', 'string', 'max:2048'],
            'currency' => ['nullable', 'string', 'max:10'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'shipping_fee' => ['nullable', 'numeric', 'min:0'],
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0'],
            'maintenance_mode' => ['boolean'],
            'privacy_policy' => ['nullable', 'array'],
            'privacy_policy.title' => ['nullable', 'array'],
            'privacy_policy.title.en' => ['nullable', 'string'],
            'privacy_policy.title.ar' => ['nullable', 'string'],
            'privacy_policy.sections' => ['nullable', 'array'],
            'privacy_policy.sections.*.heading' => ['nullable', 'array'],
            'privacy_policy.sections.*.heading.en' => ['nullable', 'string'],
            'privacy_policy.sections.*.heading.ar' => ['nullable', 'string'],
            'privacy_policy.sections.*.body' => ['nullable', 'array'],
            'privacy_policy.sections.*.body.en' => ['nullable', 'string'],
            'privacy_policy.sections.*.body.ar' => ['nullable', 'string'],
            'terms_conditions' => ['nullable', 'array'],
            'terms_conditions.title' => ['nullable', 'array'],
            'terms_conditions.title.en' => ['nullable', 'string'],
            'terms_conditions.title.ar' => ['nullable', 'string'],
            'terms_conditions.sections' => ['nullable', 'array'],
            'terms_conditions.sections.*.heading' => ['nullable', 'array'],
            'terms_conditions.sections.*.heading.en' => ['nullable', 'string'],
            'terms_conditions.sections.*.heading.ar' => ['nullable', 'string'],
            'terms_conditions.sections.*.body' => ['nullable', 'array'],
            'terms_conditions.sections.*.body.en' => ['nullable', 'string'],
            'terms_conditions.sections.*.body.ar' => ['nullable', 'string'],
        ]);

        $data = $request->except(['_token', '_method']);

        foreach (['privacy_policy', 'terms_conditions'] as $documentKey) {
            if (isset($data[$documentKey])) {
                $data[$documentKey] = $this->normalizeDocument($data[$documentKey]);
            }
        }

        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', __('admin.settings.saved'));
    }

    /**
     * Normalize the submitted legal document into the title + sections shape.
     *
     * @param  array<string, mixed>  $document
     * @return array<string, mixed>
     */
    private function normalizeDocument(array $document): array
    {
        return [
            'title' => [
                'en' => $document['title']['en'] ?? '',
                'ar' => $document['title']['ar'] ?? '',
            ],
            'sections' => collect($document['sections'] ?? [])
                ->reject(fn (array $section): bool => $this->isEmptySection($section))
                ->map(fn (array $section): array => [
                    'heading' => [
                        'en' => $section['heading']['en'] ?? '',
                        'ar' => $section['heading']['ar'] ?? '',
                    ],
                    'body' => [
                        'en' => $section['body']['en'] ?? '',
                        'ar' => $section['body']['ar'] ?? '',
                    ],
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * Determine whether a submitted section carries no content at all.
     *
     * @param  array<string, mixed>  $section
     */
    private function isEmptySection(array $section): bool
    {
        return $this->isBlank($section['heading']['en'] ?? '')
            && $this->isBlank($section['heading']['ar'] ?? '')
            && $this->isBlank($section['body']['en'] ?? '')
            && $this->isBlank($section['body']['ar'] ?? '');
    }

    /**
     * Determine whether rich text carries no visible content.
     */
    private function isBlank(string $value): bool
    {
        $stripped = preg_replace('/<[^>]*>/', '', $value) ?? '';

        return preg_replace('/[\s\x{00A0}\x{200B}\x{200E}\x{200F}]+/u', '', html_entity_decode($stripped, ENT_QUOTES)) === '';
    }
}
