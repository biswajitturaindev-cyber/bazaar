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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_type');

            $table->string('sku')->nullable()->unique()->index();
            $table->string('barcode')->nullable()->unique()->index();

            $table->decimal('mrp', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();
            
            $table->decimal('commission', 10, 2)->nullable();
            $table->decimal('vendor_commission', 10, 2)->nullable();

            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();

            $table->boolean('is_primary')->default(false);

            $table->tinyInteger('variant_status')
                ->default(1)
                ->comment('1 = Active, 0 = Inactive');

            $table->string('batch_no')->nullable();
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable();

            $table->tinyInteger('vendor_commission_approval_status')
                ->default(0)
                ->comment('0=Waiting for Approval, 1=Approved, 2=Rejected');

            $table->timestamps();

            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
