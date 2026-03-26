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
        Schema::create('hsn', function (Blueprint $table) {
            $table->id();
            $table->string('hsnCode', 20);
            $table->string('description')->nullable();
            $table->decimal('cGst', 5, 2)->default(0);
            $table->decimal('sGst', 5, 2)->default(0);
            $table->decimal('iGst', 5, 2)->default(0);
            $table->boolean('isActive')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hsn');
    }
};
