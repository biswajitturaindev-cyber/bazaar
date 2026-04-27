<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttributeValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        AttributeValue::insert([
            // Shirt Color (attribute_id = 1)
            [
                'attribute_id' => 1,
                'value' => 'BLACK',
                'color_code' => '#000000',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'attribute_id' => 1,
                'value' => 'LAVENDER',
                'color_code' => '#E6E6FA',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'attribute_id' => 1,
                'value' => 'NAVY',
                'color_code' => '#000080',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'attribute_id' => 1,
                'value' => 'WHITE',
                'color_code' => '#FFFFFF',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Shirt Size (attribute_id = 2)
            [
                'attribute_id' => 2,
                'value' => 'S',
                'color_code' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'attribute_id' => 2,
                'value' => 'M',
                'color_code' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'attribute_id' => 2,
                'value' => 'L',
                'color_code' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'attribute_id' => 2,
                'value' => 'XL',
                'color_code' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'attribute_id' => 2,
                'value' => 'XXL',
                'color_code' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

    }
}
