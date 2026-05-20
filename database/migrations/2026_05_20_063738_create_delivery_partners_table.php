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
        Schema::create('delivery_partners', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('code')->unique()->nullable();
            // Example: DELHIVERY, DTDC, BLUEDART

            $table->string('website')->nullable();

            $table->string('tracking_url')->nullable();
            // Example:
            // https://www.delhivery.com/track/package/{{tracking_id}}

            $table->string('contact_no')->nullable();

            $table->tinyInteger('status')->default(1)->comment(
                '0=Inactive, 1=Active'
            );

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_partners');
    }
};
