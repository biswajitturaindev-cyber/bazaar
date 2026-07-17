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
        Schema::create('commission_settlement_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('business_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('settlement_transaction_id')
                ->nullable()
                ->constrained('commission_settlement_transactions')
                ->nullOnDelete();

            // Original order amount
            $table->decimal('order_amount', 12, 2)->default(0);

            // Fixed platform charge per order
            $table->decimal('platform_charge', 12, 2)
                ->default(0.00)
                ->comment('Platform charge deducted from each order');

            // Commission for this order
            $table->decimal('commission_amount', 12, 2)->default(0);

            // Commission amount plus platform charge
            $table->decimal('settlement_order_amount', 12, 2)
                ->default(0)
                ->comment('Commission amount plus platform charge');

            $table->enum('status', [
                'pending',
                'processing',
                'paid',
                'cancelled',
            ])->default('pending');

            $table->timestamp('settled_at')->nullable();

            $table->timestamps();

            $table->index(['business_id', 'status']);
            $table->index('settlement_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_settlement_orders');
    }
};
