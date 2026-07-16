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

                // Unique settlement request number
                $table->string('transaction_no')->unique();

                // Commission
                $table->decimal('payable_commission', 12, 2)->default(0);
                $table->decimal('settlement_amount', 12, 2);

                // Payment
                $table->enum('payment_mode', [
                    'wallet',
                    'bank_transfer',
                    'upi',
                    'cash',
                ]);

                // User payment details
                $table->string('payment_transaction_no')->nullable();
                $table->string('payment_reference_no')->nullable();
                $table->string('payment_slip')->nullable(); // store file path

                $table->text('remarks')->nullable();

                // Settlement Status
                $table->enum('status', [
                    'pending',
                    'approved',
                    'rejected',
                    'paid',
                ])->default('pending');

                // Admin Action
                $table->foreignId('approved_by')
                    ->nullable()
                    ->constrained('admins')
                    ->nullOnDelete();

                $table->timestamp('approved_at')->nullable();
                $table->text('admin_remarks')->nullable();

                // Final payment details (when admin pays vendor)
                $table->string('settlement_reference_no')->nullable();
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
