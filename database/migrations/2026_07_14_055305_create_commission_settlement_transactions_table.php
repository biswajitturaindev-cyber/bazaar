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
        Schema::create('commission_settlement_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')
                ->constrained('businesses')
                ->cascadeOnDelete();

            $table->string('transaction_no')->unique();

            // Commission details
            $table->decimal('payable_commission', 12, 2)->default(0);
            $table->decimal('settlement_amount', 12, 2);

            // Payment details
            $table->enum('payment_mode', [
                'wallet',
                'bank_transfer',
                'upi',
                'cash'
            ]);

            $table->text('remarks')->nullable();

            // Request status
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'paid'
            ])->default('pending');

            // Admin action
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->text('admin_remarks')->nullable();

            // Payment reference
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_settlement_transactions');
    }
};
