<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convert the existing name/description values into translations.
     */
    public function up(): void
    {
        $fallback = config('app.fallback_locale', 'en');

        DB::table('categories')->get()->each(function (object $category): void {
            $name = is_string($category->name) ? ['en' => $category->name, 'ar' => $category->name] : $category->name;
            $description = $category->description !== null && is_string($category->description)
                ? ['en' => $category->description, 'ar' => $category->description]
                : $category->description;

            DB::table('categories')->where('id', $category->id)->update([
                'name' => json_encode($name, JSON_UNESCAPED_UNICODE),
                'description' => $description !== null ? json_encode($description, JSON_UNESCAPED_UNICODE) : null,
            ]);
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->json('name')->change();
            $table->json('description')->nullable()->change();
        });
    }

    /**
     * Flatten the translations back into plain text.
     */
    public function down(): void
    {
        $fallback = config('app.fallback_locale', 'en');

        DB::table('categories')->get()->each(function (object $category) use ($fallback): void {
            $name = json_decode($category->name ?? '', true);
            $description = json_decode($category->description ?? '', true);

            DB::table('categories')->where('id', $category->id)->update([
                'name' => is_array($name) ? ($name[$fallback] ?? array_values($name)[0]) : $category->name,
                'description' => is_array($description) ? ($description[$fallback] ?? array_values($description)[0]) : $category->description,
            ]);
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->string('name')->change();
            $table->text('description')->nullable()->change();
        });
    }
};
