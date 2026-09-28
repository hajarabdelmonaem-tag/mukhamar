<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convert the existing title/description values into translations.
     */
    public function up(): void
    {
        DB::table('banners')->get()->each(function (object $banner): void {
            $title = is_array($banner->title) ? $banner->title : ['en' => $banner->title, 'ar' => $banner->title];
            $description = is_array($banner->description)
                ? $banner->description
                : ($banner->description !== null ? ['en' => $banner->description, 'ar' => $banner->description] : null);

            DB::table('banners')->where('id', $banner->id)->update([
                'title' => json_encode($title, JSON_UNESCAPED_UNICODE),
                'description' => $description !== null ? json_encode($description, JSON_UNESCAPED_UNICODE) : null,
            ]);
        });

        Schema::table('banners', function (Blueprint $table): void {
            $table->json('title')->nullable()->change();
            $table->json('description')->nullable()->change();
        });
    }

    /**
     * Flatten the translations back into plain text.
     */
    public function down(): void
    {
        $fallback = config('app.fallback_locale', 'en');

        DB::table('banners')->get()->each(function (object $banner) use ($fallback): void {
            $title = json_decode($banner->title ?? '', true);
            $description = json_decode($banner->description ?? '', true);

            DB::table('banners')->where('id', $banner->id)->update([
                'title' => is_array($title) ? ($title[$fallback] ?? array_values($title)[0]) : $banner->title,
                'description' => is_array($description) ? ($description[$fallback] ?? array_values($description)[0]) : $banner->description,
            ]);
        });

        Schema::table('banners', function (Blueprint $table): void {
            $table->string('title')->nullable()->change();
            $table->text('description')->nullable()->change();
        });
    }
};
