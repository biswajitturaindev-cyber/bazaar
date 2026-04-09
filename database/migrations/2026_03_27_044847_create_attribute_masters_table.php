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
        Schema::create('attribute_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_category_id')->nullable()->constrained('business_categories')->restrictOnDelete();
            $table->foreignId('business_sub_category_id')->nullable()->constrained('business_sub_categories')->restrictOnDelete();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attribute_masters', function (Blueprint $table) {

            $table->dropForeign(['business_category_id']);
            $table->dropForeign(['business_sub_category_id']);

            $table->dropColumn(['business_category_id', 'business_sub_category_id']);
        });
    }
};
