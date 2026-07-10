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
        Schema::create('vendor_packages', function (Blueprint $table) {
            $table->id();

            // No extra index()
            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('package_id')
                ->constrained()
                ->restrictOnDelete();

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->timestamps();
            $table->softDeletes(); // soft delete

            // Composite index
            $table->index(['user_id', 'package_id']);

            // Unique constraint
            $table->unique(['user_id', 'package_id', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_packages');
    }
};
