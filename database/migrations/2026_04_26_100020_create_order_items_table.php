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
        Schema::create('order_items', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Order
            |--------------------------------------------------------------------------
            */
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('product_id');

            /*
            |--------------------------------------------------------------------------
            | Product Variant
            |--------------------------------------------------------------------------
            */
            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Product Snapshot
            |--------------------------------------------------------------------------
            */
            $table->string('product_name');

            $table->string('sku')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            */
            $table->smallInteger('quantity')
                ->default(1);

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */
            $table->decimal('mrp', 12, 2)
                ->default(0);

            $table->decimal('selling_price', 12, 2)
                ->default(0);

            $table->decimal('discount_amount', 12, 2)
                ->default(0);

            $table->decimal('final_price', 12, 2)
                ->default(0);

            $table->decimal('subtotal', 12, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Loyalty
            |--------------------------------------------------------------------------
            */
            $table->decimal('loyalty_points', 12, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Product Snapshot JSON
            |--------------------------------------------------------------------------
            */
            $table->json('product_snapshot')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */
            $table->index('product_id');

            $table->index('product_variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
