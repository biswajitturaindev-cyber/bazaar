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
        Schema::create('order_item_attributes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('attribute_master_id')
                ->nullable()
                ->constrained('attribute_masters')
                ->nullOnDelete();

            $table->foreignId('attribute_value_id')
                ->nullable()
                ->constrained('attribute_values')
                ->nullOnDelete();

            $table->string('attribute_name')->nullable();

            $table->string('attribute_value')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_attributes');
    }
};
