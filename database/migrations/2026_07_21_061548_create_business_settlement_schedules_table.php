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
        Schema::create('business_settlement_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'daily',
                'weekly',
                'monthly',
            ]);

            $table->unsignedTinyInteger('day')->nullable()->comment(
                'Daily: NULL, Weekly: 1-7 (Mon-Sun), Monthly: 1-31'
            );

            $table->timestamps();

            $table->index(['business_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_settlement_schedules');
    }
};
