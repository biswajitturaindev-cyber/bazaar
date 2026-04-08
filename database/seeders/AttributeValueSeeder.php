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
            // Size Values
            [
                'attribute_id' => 1,
                'value' => 'Small',
                'color_code' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attribute_id' => 1,
                'value' => 'Medium',
                'color_code' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attribute_id' => 1,
                'value' => 'Large',
                'color_code' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Color Values
            [
                'attribute_id' => 2,
                'value' => 'Red',
                'color_code' => '#FF0000',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attribute_id' => 2,
                'value' => 'Blue',
                'color_code' => '#0000FF',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attribute_id' => 2,
                'value' => 'Green',
                'color_code' => '#00FF00',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
