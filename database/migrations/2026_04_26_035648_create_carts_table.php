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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->unsignedBigInteger('product_id');
            $table->string('product_type'); // Model name

            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->decimal('total', 10, 2)->nullable();

            // Snapshot
            $table->string('product_name')->nullable();
            $table->string('image')->nullable();

            // VERY IMPORTANT
            $table->string('attribute_hash')->nullable();

            $table->timestamps();

            // Correct unique constraint
            $table->unique([
                'user_id',
                'product_id',
                'product_type',
                'attribute_hash'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
