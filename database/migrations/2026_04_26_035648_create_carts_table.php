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
            $table->unsignedBigInteger('business_id');
            $table->unsignedTinyInteger('business_category_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('product_name')->nullable();
            $table->string('attribute_hash')->default('');
            $table->timestamps();
            $table->unique([
                'user_id',
                'business_id',
                'business_category_id',
                'product_id',
                'attribute_hash'
            ], 'cart_unique_item');
            $table->index('user_id');
            $table->index('business_id');
            $table->index([
                'business_id',
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
