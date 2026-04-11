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
        Schema::create('master_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('sub_category_id')->nullable()->index()->constrained('sub_categories')->nullOnDelete();
            $table->foreignId('sub_sub_category_id')->nullable()->index()->constrained('sub_category_items')->nullOnDelete();
            $table->foreignId('hsn_id')->constrained('hsns')->cascadeOnDelete();

            $table->string('name');
            $table->string('image')->nullable();
            $table->decimal('product_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('status')->default(1);

            $table->timestamps();
            $table->softDeletes(); // adds deleted_at column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_products');
    }
};
