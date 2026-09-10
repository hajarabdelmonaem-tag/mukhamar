<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convert the image alt text into translations.
     */
    public function up(): void
    {
        DB::table('product_images')->get()->each(function (object $image): void {
            $alt = $image->alt;

            DB::table('product_images')->where('id', $image->id)->update([
                'alt' => $alt !== null
                    ? json_encode(is_array($alt) ? $alt : ['en' => $alt, 'ar' => $alt], JSON_UNESCAPED_UNICODE)
                    : null,
            ]);
        });

        Schema::table('product_images', function (Blueprint $table): void {
            $table->json('alt')->nullable()->change();
        });
    }

    /**
     * Flatten the alt translations back into plain text.
     */
    public function down(): void
    {
        $fallback = config('app.fallback_locale', 'en');

        DB::table('product_images')->get()->each(function (object $image) use ($fallback): void {
            $decoded = json_decode($image->alt ?? '', true);

            DB::table('product_images')->where('id', $image->id)->update([
                'alt' => is_array($decoded) ? ($decoded[$fallback] ?? array_values($decoded)[0]) : $image->alt,
            ]);
        });

        Schema::table('product_images', function (Blueprint $table): void {
            $table->string('alt')->nullable()->change();
        });
    }
};
