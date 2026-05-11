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
        Schema::create('order_addresses', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Order
            |--------------------------------------------------------------------------
            */
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Address Type
            |--------------------------------------------------------------------------
            */
            $table->enum('type', [
                'Billing',
                'Shipping',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Customer Details
            |--------------------------------------------------------------------------
            */
            $table->string('full_name');

            $table->string('phone');

            $table->string('email')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */
            $table->text('address_line_1');

            $table->text('address_line_2')
                ->nullable();

            $table->string('landmark')
                ->nullable();

            $table->string('city');

            $table->string('state');

            $table->string('country');

            $table->string('postal_code');

            /*
            |--------------------------------------------------------------------------
            | Geo Location
            |--------------------------------------------------------------------------
            */
            $table->string('latitude')
                ->nullable();

            $table->string('longitude')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Default Address
            |--------------------------------------------------------------------------
            */
            $table->boolean('is_default')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Extra
            |--------------------------------------------------------------------------
            */
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
    }
};
