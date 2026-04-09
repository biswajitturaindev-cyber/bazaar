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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->unsignedBigInteger('sponsor_id')->nullable()->index();

            $table->string('business_name')->index();

            $table->foreignId('business_category_id')
                  ->constrained('business_categories')
                  ->cascadeOnDelete();

            $table->foreignId('business_sub_category_id')
                  ->constrained('business_sub_categories')
                  ->cascadeOnDelete();

            $table->integer('years_in_business')->nullable();
            $table->string('gst_number')->nullable()->index();
            $table->string('pan_number')->nullable()->index();
            $table->string('fssai_license')->nullable();
            $table->string('registration_number')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
