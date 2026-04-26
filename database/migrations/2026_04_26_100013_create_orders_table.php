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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_no')->unique();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->decimal('sub_total', 10, 2);
            $table->decimal('handling_charge', 10, 2)->default(0);
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);

            $table->integer('total_items');

            $table->enum('status', ['pending', 'processing', 'delivered', 'cancelled'])
                ->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
