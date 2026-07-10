<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BusinessAddress;

class BusinessAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BusinessAddress::updateOrCreate(
            [
                'business_id' => 1,
            ],
            [
                'address_line_1' => 'Manik Tala',
                'address_line_2' => null,
                'city' => 732,
                'state' => 28,
                'pincode' => '721415',
                'landmark' => 'Maniktala Bus Stand',
                'google_map_location' => null,
                'latitude' => null,
                'longitude' => null,
            ]
        );
    }
}
