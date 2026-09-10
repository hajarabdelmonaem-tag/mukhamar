<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The translatable variant text columns converted to JSON.
     *
     * @var array<int, string>
     */
    private const TEXT_FIELDS = ['name', 'unit'];

    /**
     * Convert the existing variant values into translations.
     */
    public function up(): void
    {
        DB::table('product_variants')->get()->each(function (object $variant): void {
            $updates = [];

            foreach (self::TEXT_FIELDS as $field) {
                $value = $variant->{$field};

                $updates[$field] = $value !== null
                    ? json_encode(is_array($value) ? $value : ['en' => $value, 'ar' => $value], JSON_UNESCAPED_UNICODE)
                    : null;
            }

            DB::table('product_variants')->where('id', $variant->id)->update($updates);
        });

        Schema::table('product_variants', function (Blueprint $table): void {
            $table->json('name')->change();
            $table->json('unit')->nullable()->change();
        });
    }

    /**
     * Flatten the translations back into plain text.
     */
    public function down(): void
    {
        $fallback = config('app.fallback_locale', 'en');

        DB::table('product_variants')->get()->each(function (object $variant) use ($fallback): void {
            $updates = [];

            foreach (self::TEXT_FIELDS as $field) {
                $decoded = json_decode($variant->{$field} ?? '', true);

                $updates[$field] = is_array($decoded)
                    ? ($decoded[$fallback] ?? array_values($decoded)[0])
                    : $variant->{$field};
            }

            DB::table('product_variants')->where('id', $variant->id)->update($updates);
        });

        Schema::table('product_variants', function (Blueprint $table): void {
            $table->string('name')->change();
            $table->string('unit')->nullable()->change();
        });
    }
};
