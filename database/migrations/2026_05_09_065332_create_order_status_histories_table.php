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
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->tinyInteger('status')->default(0)->comment(
                '0=Pending, 1=Confirmed, 2=Processing, 3=Shipped, 4=Delivered, 5=Cancelled'
            );

            $table->string('tracking_id')->nullable();

            $table->unsignedBigInteger('delivery_partner_id')->nullable();
            $table->string('delivery_partner_name')->nullable();
            // Example: Delhivery, BlueDart, DTDC, Xpressbees

            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('changed_by')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};
