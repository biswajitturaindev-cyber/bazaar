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
        Schema::create('order_items', function (Blueprint $table) {

            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('product_id');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('product_name');
            $table->string('sku')->nullable();
            $table->smallInteger('quantity')->default(1);
            $table->decimal('mrp', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('final_price', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('loyalty_points', 12, 2)->default(0);
            $table->json('product_snapshot')->nullable();
            $table->enum('status', [
                'pending',
                'confirmed',
                'cancelled',
                'shipped',
                'delivered'
            ])->default('pending');

            $table->foreignId('cancel_reason_id')->nullable()->constrained('redemption_cancel_reasons')->nullOnDelete();
            $table->text('cancel_note')->nullable();
            $table->enum('cancelled_by', [
                'admin',
                'vendor',
                'customer',
                'system',
            ])->nullable();

            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index('product_id');
            $table->index('product_variant_id');
            $table->index('status');
            $table->index('cancel_reason_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
