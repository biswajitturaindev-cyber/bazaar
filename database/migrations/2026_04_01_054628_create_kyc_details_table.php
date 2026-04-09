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
        Schema::create('kyc_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')
                ->constrained('businesses')
                ->cascadeOnDelete();

            $table->string('owner_photo');
            $table->tinyInteger('owner_photo_status')->default(0)->comment('0=Pending,1=Approved,2=Rejected');

            $table->string('shop_photo');
            $table->tinyInteger('shop_photo_status')->default(0)->comment('0=Pending,1=Approved,2=Rejected');

            $table->string('pan_card');
            $table->tinyInteger('pan_card_status')->default(0)->comment('0=Pending,1=Approved,2=Rejected');

            $table->string('gst_certificate')->nullable();
            $table->tinyInteger('gst_certificate_status')->default(0)->comment('0=Pending,1=Approved,2=Rejected');

            $table->string('trade_license')->nullable();
            $table->tinyInteger('trade_license_status')->default(0)->comment('0=Pending,1=Approved,2=Rejected');

            $table->string('fssai_license')->nullable();
            $table->tinyInteger('fssai_license_status')->default(0)->comment('0=Pending,1=Approved,2=Rejected');

            $table->string('address_proof')->nullable();
            $table->tinyInteger('address_proof_status')->default(0)->comment('0=Pending,1=Approved,2=Rejected');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_details');
    }
};
