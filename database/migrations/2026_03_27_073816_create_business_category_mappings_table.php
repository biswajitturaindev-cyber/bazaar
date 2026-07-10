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
        Schema::create('business_category_mappings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_category_id')
                ->constrained('business_categories')
                ->cascadeOnDelete();

            $table->foreignId('business_sub_category_id')
                ->constrained('business_sub_categories')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->boolean('status')->default(1)->comment('1=Active, 0=Inactive');

            $table->timestamps();

            // Prevent duplicate mapping
            $table->unique([
                'business_category_id',
                'business_sub_category_id',
                'category_id'
            ], 'unique_business_mapping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_category_mappings');
    }
};
