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
        Schema::create('product_construction_hardware', function (Blueprint $table) {
            $table->id();

            // Business
            $table->foreignId('business_id')
                ->nullable()
                ->constrained('businesses')
                ->nullOnDelete();

            $table->foreignId('business_category_id')
                ->nullable()
                ->constrained('business_categories')
                ->nullOnDelete();

            $table->foreignId('business_sub_category_id')
                ->nullable()
                ->constrained('business_sub_categories')
                ->nullOnDelete();


            // Product Category
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->foreignId('sub_category_id')
                ->nullable()
                ->constrained('sub_categories')
                ->nullOnDelete();

            $table->foreignId('sub_sub_category_id')
                ->nullable()
                ->constrained('sub_category_items')
                ->nullOnDelete();

            // Product
            $table->foreignId('hsn_id')
                ->nullable()
                ->constrained('hsns')
                ->nullOnDelete();

            // Basic Info
            $table->string('name');
            //$table->string('image')->nullable();
            $table->longText('description')->nullable();

            // Pricing
            // $table->decimal('mrp', 10, 2)->nullable();
            // $table->decimal('cost_price', 10, 2)->nullable();
            // $table->decimal('selling_price', 10, 2)->nullable();
            // $table->decimal('discount', 10, 2)->nullable();
            // $table->decimal('final_price', 10, 2)->nullable();

            // Dates
            // $table->date('manufacture_date')->nullable();
            // $table->date('expiry_date')->nullable();

            // Status
            $table->tinyInteger('status')->default(2)->comment('0=Inactive,1=Active,2=Unapproved');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_construction_hardware');
    }
};
