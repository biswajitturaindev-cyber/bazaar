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
        Schema::create('business_agreements', function (Blueprint $table) {
            $table->id();

            // Relation with business
            $table->foreignId('business_id')
                ->constrained('businesses')
                ->cascadeOnDelete();

            $table->boolean('agree_terms')->default(false)->index();
            $table->boolean('confirm_info')->default(false)->index();

            $table->timestamps();

            $table->unique('business_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_agreements');
    }
};
