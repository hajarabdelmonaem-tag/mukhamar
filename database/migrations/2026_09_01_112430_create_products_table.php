<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('usage_instructions')->nullable();
            $table->decimal('price', 10, 2)->unsigned();
            $table->decimal('old_price', 10, 2)->unsigned()->nullable();
            $table->decimal('rating', 3, 1)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->string('top_notes')->nullable();
            $table->string('heart_notes')->nullable();
            $table->string('base_notes')->nullable();
            $table->json('badges')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
