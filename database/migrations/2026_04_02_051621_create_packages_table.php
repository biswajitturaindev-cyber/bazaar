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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->integer('stars')->index();
            $table->decimal('price', 10, 2)->default(0)->index();

            // Subscription validity
            $table->integer('duration')->default(30);
            $table->enum('duration_type', ['day', 'month', 'year'])->default('day');

            $table->integer('product_limit')->nullable();

            // Description
            $table->text('description')->nullable();

            $table->boolean('status')->default(1)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
