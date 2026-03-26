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
        Schema::create('business_addresses', function (Blueprint $table) {
            $table->id();

            // Relation with business (auto indexed)
            $table->foreignId('business_id')
                ->constrained('businesses')
                ->cascadeOnDelete();

            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();

            // Indexing for search/filter
            $table->string('city')->nullable()->index();
            $table->string('state')->nullable()->index();
            $table->string('pincode')->nullable()->index();

            $table->string('landmark')->nullable();
            $table->text('google_map_location')->nullable();

            $table->timestamps();

            // Composite index for location queries
            $table->index(['city', 'state']);

            // Optional (ONLY if one business = one address)
             $table->unique('business_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_addresses');
    }
};
