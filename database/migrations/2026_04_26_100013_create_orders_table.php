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
            $table->string('order_no')->unique();
            $table->string('invoice_no')->nullable();


            $table->foreignId('business_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('business_category_id')
                ->nullable()
                ->constrained('business_categories')
                ->nullOnDelete();

            $table->unsignedBigInteger('user_id');

            $table->integer('total_items')->default(0);
            $table->decimal('items_total', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('platform_charge', 12, 2)->default(0);
            $table->decimal('delivery_charge', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);


            $table->decimal('loyalty_used', 12, 2)->default(0);
            $table->decimal('loyalty_earned', 12, 2)->default(0);
            $table->decimal('wallet_used', 12, 2)->default(0);
            $table->decimal('online_paid', 12, 2)->default(0);

            $table->tinyInteger('payment_status')->default(0)->comment('0=Pending, 1=Paid, 2=Failed, 3=Refunded');
            $table->tinyInteger('payment_method')->default(1)->comment('0=Wallet, 1=Online, 2=COD');

            $table->tinyInteger('order_status')->default(0)->comment(
                '0=Pending, 1=Confirmed, 2=Processing, 3=Shipped, 4=Delivered, 5=Cancelled, 6=Partial Cancelled'
            );

            $table->text('notes')->nullable();

            /*
            |--------------------------------------------------------------------------
            | GST Invoice Details
            |--------------------------------------------------------------------------
            */
            $table->boolean('is_gst_bill')->default(false);
            $table->string('gst_name')->nullable();
            $table->string('gst_number', 15)->nullable();
            $table->text('gst_address')->nullable();


            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->tinyInteger('refund_status')->default(0)->comment(
                '0=No Refund, 1=Partial Refund, 2=Full Refund'
            );
            $table->foreignId('cancel_reason_id')->nullable()->constrained('redemption_cancel_reasons')->nullOnDelete();
            $table->text('cancel_note')->nullable();
            $table->timestamp('cancelled_at')->nullable();


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
