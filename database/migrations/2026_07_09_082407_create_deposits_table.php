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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')
                ->constrained('businesses')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('company_account_id')->nullable();

            $table->decimal('amount', 12, 2);

            $table->string('transaction_id')->nullable();
            $table->string('ref_id')->nullable();

            // 1=UPI, 2=Bank, 3=Wallet/Other
            $table->tinyInteger('payment_method')->nullable();

            $table->string('payment_proof')->nullable();

            // 0=Pending,1=Approved,2=Rejected
            $table->tinyInteger('status')->default(0);

            $table->text('admin_note')->nullable();
            $table->string('user_note')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
