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
        Schema::create('cart_attributes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();

            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained('attribute_values')->cascadeOnDelete();

            $table->decimal('price', 10, 2)->nullable();

            // Snapshot
            $table->string('attribute_name')->nullable();
            $table->string('attribute_value')->nullable();

            $table->timestamps();

            // Prevent duplicate attribute in same cart
            $table->unique(['cart_id', 'attribute_id']);

            // Add index for performance
            $table->index(['attribute_id', 'attribute_value_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_attributes');
    }
};
