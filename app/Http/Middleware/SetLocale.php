<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['en', 'ar'];

    /**
     * Resolve the request locale and set it for the current request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('X-Locale')
            ?? $request->header('lang')
            ?? $request->input('lang');

        if (! $locale && $request->hasSession()) {
            $locale = $request->session()->get('locale');
        }

        if (! $locale && $request->is('api/*')) {
            $locale = $request->user()?->lang ?? $request->user('sanctum')?->lang;
        }

        if (! $locale) {
            $locale = $request->getPreferredLanguage(self::SUPPORTED_LOCALES);
        }

        if ($locale && in_array($locale, self::SUPPORTED_LOCALES, true)) {
            app()->setLocale($locale);

            if ($request->hasSession()) {
                $request->session()->put('locale', $locale);
            }
        }

        return $next($request);
    }
}
