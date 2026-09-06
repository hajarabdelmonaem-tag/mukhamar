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
        $locale = $request->header('X-Locale') ?? $request->query('lang');

        if (! $locale) {
            $locale = $request->getPreferredLanguage(self::SUPPORTED_LOCALES);
        }

        if ($locale && in_array($locale, self::SUPPORTED_LOCALES, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
