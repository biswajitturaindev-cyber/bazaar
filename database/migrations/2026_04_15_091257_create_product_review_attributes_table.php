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
        Schema::create('product_review_attributes', function (Blueprint $table) {
            $table->id();

            // FK: product_reviews
            $table->foreignId('product_review_id')
                  ->constrained('product_reviews')
                  ->cascadeOnDelete();

            // FK: attributes
            $table->foreignId('attribute_id')
                  ->constrained('attributes')
                  ->cascadeOnDelete();

            // FK: attribute_values (IMPORTANT FIX)
            $table->foreignId('attribute_value_id')
                  ->nullable()
                  ->constrained('attribute_values')
                  ->nullOnDelete();

            // Variant snapshot
            $table->integer('stock')->nullable();
            $table->decimal('price', 10, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_review_attributes');
    }
};
