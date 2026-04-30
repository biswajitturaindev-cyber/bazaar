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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();

            // Category decides table
            $table->foreignId('business_category_id')
                ->constrained('business_categories')
                ->cascadeOnDelete();

            // Product ID inside that category table
            $table->unsignedBigInteger('product_id');

            // Variant (MAIN LINK)
            $table->unsignedBigInteger('product_variant_id');

            $table->string('image_large');
            $table->string('image_medium');
            $table->string('image_small');

            $table->timestamps();

            // FIX: short index name
            $table->index(
                ['business_category_id', 'product_id', 'product_variant_id'],
                'pi_bcat_pid_vid_idx'
            );

            // optional but recommended
            $table->index('product_variant_id', 'pi_vid_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
