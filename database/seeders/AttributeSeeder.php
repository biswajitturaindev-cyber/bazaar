<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Attribute::insert([
            [
                'id' => 1,
                'attribute_master_id' => 1, // Color
                'category_id' => 10,
                'sub_category_id' => 18,
                'type' => 'text',
                'name' => 'Shirt Color',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'attribute_master_id' => 2, // Size
                'category_id' => 10,
                'sub_category_id' => 18,
                'type' => 'text',
                'name' => 'Shirt Size',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
