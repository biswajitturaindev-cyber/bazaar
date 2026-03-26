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
        Schema::create('business_sub_categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_category_id')
                ->constrained('business_categories')
                ->cascadeOnDelete()
                ->index();

            $table->string('name')->index();

            $table->string('image')->nullable();

            $table->boolean('status')->default(1)->index();

            // Composite index (VERY IMPORTANT)
            $table->index(['business_category_id', 'name']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_sub_categories');
    }
};
