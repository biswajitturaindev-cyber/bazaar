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
        Schema::create('product_category_attribute_values', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation (important)
            $table->morphs('product'); // creates product_id + product_type

            // Attribute relations
            $table->foreignId('attribute_id')
                ->constrained('attributes')
                ->cascadeOnDelete();

            $table->foreignId('attribute_value_id')
                ->constrained('attribute_values')
                ->cascadeOnDelete();

            // Extra fields
            $table->integer('stock')->default(0);
            $table->decimal('price', 10, 2)->nullable();

            $table->timestamps();

            // Prevent duplicate combinations
            $table->unique([
                'product_id',
                'product_type',
                'attribute_id',
                'attribute_value_id'
            ], 'prod_attr_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_category_attribute_values');
    }
};
