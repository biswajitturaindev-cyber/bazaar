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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Order Info
            |--------------------------------------------------------------------------
            */
            $table->string('order_no')->unique();

            $table->unsignedBigInteger('user_id');

            /*
            |--------------------------------------------------------------------------
            | Quantity & Amount
            |--------------------------------------------------------------------------
            */
            $table->integer('total_items')->default(0);
            $table->decimal('items_total', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('platform_charge', 12, 2)->default(0);
            $table->decimal('delivery_charge', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Loyalty & Wallet
            |--------------------------------------------------------------------------
            */
            $table->decimal('loyalty_used', 12, 2)->default(0);
            $table->decimal('loyalty_earned', 12, 2)->default(0);
            $table->decimal('wallet_used', 12, 2)->default(0);
            $table->decimal('online_paid', 12, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Payment
            |--------------------------------------------------------------------------
            */
            $table->enum('payment_status', [
                'Pending',
                'Paid',
                'Failed',
                'Refunded',
            ])->default('Pending');

            $table->enum('payment_method', [
                'Wallet',
                'Online',
                'COD',
                'Mixed',
            ])->default('Online');

            /*
            |--------------------------------------------------------------------------
            | Order Status
            |--------------------------------------------------------------------------
            */
            $table->enum('order_status', [
                'Pending',
                'Confirmed',
                'Processing',
                'Shipped',
                'Delivered',
                'Cancelled',
            ])->default('Pending');

            /*
            |--------------------------------------------------------------------------
            | Extra
            |--------------------------------------------------------------------------
            */
            $table->text('notes')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
