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
        Schema::create('order_addresses', function (Blueprint $table) {

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
            | Billing Address
            |--------------------------------------------------------------------------
            */
            $table->text('billing_address');
            $table->unsignedBigInteger('billing_city_id')->nullable();
            $table->unsignedBigInteger('billing_state_id')->nullable();
            $table->string('billing_pincode', 20)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Shipping Address
            |--------------------------------------------------------------------------
            */
            $table->text('shipping_address');
            $table->unsignedBigInteger('shipping_city_id')->nullable();
            $table->unsignedBigInteger('shipping_state_id')->nullable();
            $table->string('shipping_pincode', 20)->nullable();


            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
    }
};
