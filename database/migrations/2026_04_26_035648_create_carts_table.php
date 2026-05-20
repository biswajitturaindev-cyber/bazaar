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
        Schema::create('carts', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('user_id');

            /*
            |--------------------------------------------------------------------------
            | Product Source
            |--------------------------------------------------------------------------
            | Determines which model/table to use
            */

            $table->unsignedTinyInteger('business_category_id');

            /*
            |--------------------------------------------------------------------------
            | Product ID
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('product_id');

            $table->unsignedBigInteger('product_variant_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('quantity')
                ->default(1);

            /*
            |--------------------------------------------------------------------------
            | Snapshot Data (Optional)
            |--------------------------------------------------------------------------
            | Used for faster cart display
            */

            $table->string('product_name')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Variant Combination Hash
            |--------------------------------------------------------------------------
            | Example:
            | color:red-size:m
            | or md5 hash
            */

            $table->string('attribute_hash')
                ->default('');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Cart Rows
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'user_id',
                'business_category_id',
                'product_id',
                'attribute_hash'
            ], 'cart_unique_item');

            /*
            |--------------------------------------------------------------------------
            | Performance Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('user_id');

            $table->index([
                'business_category_id',
                'product_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
