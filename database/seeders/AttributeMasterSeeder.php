<?php

namespace Database\Seeders;

use App\Models\AttributeMaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttributeMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AttributeMaster::insert([
            [
                'id' => 1,
                'business_category_id' => 1,
                'business_sub_category_id' => 1,
                'name' => 'Size',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'business_category_id' => 1,
                'business_sub_category_id' => 1,
                'name' => 'Color',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
