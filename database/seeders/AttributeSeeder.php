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
                'attribute_master_id' => 1, // Size
                'category_id' => 1,
                'sub_category_id' => 1,
                'type' => 'text',
                'name' => 'Size',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'attribute_master_id' => 2, // Color
                'category_id' => 1,
                'sub_category_id' => 1,
                'type' => 'color',
                'name' => 'Color',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
