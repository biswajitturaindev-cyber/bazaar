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
        Schema::create('store_operational_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')
                ->constrained('businesses')
                ->cascadeOnDelete();

            // Store Timing
            $table->time('opening_time');
            $table->time('closing_time');

            // Delivery Type
            $table->enum('delivery_type', ['self', 'platform', 'both']);

            // Service Area
            $table->decimal('delivery_radius', 5, 2)->nullable(); // KM
            $table->text('serviceable_pincode'); // comma separated OR JSON

            // Optional fields
            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_operational_details');
    }
};
