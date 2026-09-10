<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The translatable product text columns (string/text) converted to JSON.
     *
     * @var array<int, string>
     */
    private const TEXT_FIELDS = ['name', 'description', 'usage_instructions', 'top_notes', 'heart_notes', 'base_notes'];

    /**
     * Convert the existing text values into translations.
     */
    public function up(): void
    {
        DB::table('products')->get()->each(function (object $product): void {
            $translations = [];

            foreach (self::TEXT_FIELDS as $field) {
                $value = $product->{$field};

                if ($value === null) {
                    $translations[$field] = null;

                    continue;
                }

                $translations[$field] = is_array($value)
                    ? $value
                    : ['en' => $value, 'ar' => $value];
            }

            DB::table('products')->where('id', $product->id)->update([
                'name' => json_encode($translations['name'], JSON_UNESCAPED_UNICODE),
                'description' => $translations['description'] !== null
                    ? json_encode($translations['description'], JSON_UNESCAPED_UNICODE)
                    : null,
                'usage_instructions' => $translations['usage_instructions'] !== null
                    ? json_encode($translations['usage_instructions'], JSON_UNESCAPED_UNICODE)
                    : null,
                'top_notes' => $translations['top_notes'] !== null
                    ? json_encode($translations['top_notes'], JSON_UNESCAPED_UNICODE)
                    : null,
                'heart_notes' => $translations['heart_notes'] !== null
                    ? json_encode($translations['heart_notes'], JSON_UNESCAPED_UNICODE)
                    : null,
                'base_notes' => $translations['base_notes'] !== null
                    ? json_encode($translations['base_notes'], JSON_UNESCAPED_UNICODE)
                    : null,
            ]);
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->json('name')->change();
            $table->json('description')->nullable()->change();
            $table->json('usage_instructions')->nullable()->change();
            $table->json('top_notes')->nullable()->change();
            $table->json('heart_notes')->nullable()->change();
            $table->json('base_notes')->nullable()->change();
        });
    }

    /**
     * Flatten the translations back into plain text.
     */
    public function down(): void
    {
        $fallback = config('app.fallback_locale', 'en');

        DB::table('products')->get()->each(function (object $product) use ($fallback): void {
            $updates = [];

            foreach (self::TEXT_FIELDS as $field) {
                $decoded = json_decode($product->{$field} ?? '', true);

                $updates[$field] = is_array($decoded)
                    ? ($decoded[$fallback] ?? array_values($decoded)[0])
                    : $product->{$field};
            }

            DB::table('products')->where('id', $product->id)->update($updates);
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->string('name')->change();
            $table->text('description')->nullable()->change();
            $table->text('usage_instructions')->nullable()->change();
            $table->string('top_notes')->nullable()->change();
            $table->string('heart_notes')->nullable()->change();
            $table->string('base_notes')->nullable()->change();
        });
    }
};
