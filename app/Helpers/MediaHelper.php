<?php

namespace App\Helpers;

class MediaHelper
{
    /**
     * Build the full public URL for a stored media path.
     *
     * Absolute URLs are returned untouched, so external sources such as a CDN
     * keep working, while relative paths are resolved against the public disk.
     */
    public static function toUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : asset('storage/'.$path);
    }
}
