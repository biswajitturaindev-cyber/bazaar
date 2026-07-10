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
        Schema::create('vendor_loyalty_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')
                ->constrained('businesses')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('order_id')->nullable();
            // Add order type
            $table->enum('order_type', [
                'vendor_order',
                'member_order',
            ])->nullable();

            $table->string('transaction_no')->unique();
            $table->enum('transaction_type', [
                'credit',
                'debit'
            ]);

            $table->enum('source', [
                'order',
                'redeem',
                'bonus',
                'adjustment',
                'refund',
            ]);

            $table->decimal('points', 12, 2);
            $table->decimal('opening_points', 12, 2)->default(0);
            $table->decimal('closing_points', 12, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->enum('status', [
                'pending',
                'approved',
                'cancelled',
            ])->default('approved');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('business_id');
            $table->index('order_id');
            $table->index('transaction_no');
            $table->index('status');
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_loyalty_wallets');
    }
};
