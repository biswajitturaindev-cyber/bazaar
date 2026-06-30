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
        Schema::create('store_operational_timings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_operational_detail_id')
                ->constrained('store_operational_details')
                ->cascadeOnDelete();

            $table->time('opening_time');
            $table->time('closing_time');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_operational_timings');
    }
};
