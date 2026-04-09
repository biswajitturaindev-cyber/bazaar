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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // Users
            $table->foreignId('user_id')->index()->constrained()->restrictOnDelete();

            // Category
            $table->foreignId('category_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('sub_category_id')->nullable()->index()->constrained('categories')->nullOnDelete();
            $table->foreignId('sub_sub_category_id')->nullable()->index()->constrained('categories')->nullOnDelete();

            // Basic Info
            $table->string('name')->index(); // searchable
            $table->text('description')->nullable();

            // HSN
            $table->foreignId('hsn_id')->nullable()->index()->constrained('hsns')->nullOnDelete();
            $table->decimal('gst_percent', 5, 2)->default(0)->index();

            // Pricing
            $table->decimal('mrp', 10, 2);
            $table->decimal('selling_price', 10, 2)->nullable()->index();
            $table->decimal('discount', 10, 2)->default(0);

            // Status
            $table->boolean('status')->default(true)->index();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
