<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Business;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Business::updateOrCreate(
            ['id' => 1],
            [
                'user_id' => 1,
                'sponsor_id' => 3,
                'business_type' => 'service',
                'business_name' => 'Ishita Fashion',
                'business_category_id' => 1,
                'business_sub_category_id' => 2,
                'years_in_business' => 2,

                'gst_number' => null,
                'pan_number' => null,
                'fssai_license' => null,
                'registration_number' => null,

                'shop_status' => 'open',
                'working_days' => null,
            ]
        );
    }
}
