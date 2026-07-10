<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BusinessAgreement;

class BusinessAgreementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BusinessAgreement::updateOrCreate(
            ['business_id' => 1],
            [
                'agree_terms' => 1,
                'confirm_info' => 1,
            ]
        );
    }
}
