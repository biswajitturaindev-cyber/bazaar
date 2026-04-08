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
        Schema::create('product_agricultures', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
        
            $table->foreignId('business_sub_category_id')->nullable()->constrained('business_sub_categories')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->nullOnDelete();
        
            $table->string('sku')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('mrp', 10, 2)->comment('Maximum Retail Price');
            $table->decimal('cost_price', 10, 2)->nullable()->comment('Vendor cost');
            $table->decimal('selling_price', 10, 2)->nullable()->comment('Final selling price');
            $table->decimal('discount', 5, 2)->default(0)->comment('Discount %');
        
            $table->integer('stock')->default(0);
        
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable();
        
            $table->boolean('status')->default(2)->comment('0=Inactive,1=Active,2=Unapproved');
        
            $table->index(['business_id', 'category_id']);
            $table->index(['status']);
            $table->index(['name']);
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_agricultures');
    }
};
