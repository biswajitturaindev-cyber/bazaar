<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\BusinessContact;
use Illuminate\Database\Seeder;

class BusinessContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BusinessContact::updateOrCreate(
            ['business_id' => 1],
            [
                'contact_person_name' => 'Ishita Giri',
                'contact_number' => '9876543210',
            ]
        );
    }
}
