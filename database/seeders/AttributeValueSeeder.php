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
        $data = [
            'Size' => ['Half', 'Full'],
            'Spice Level' => ['Mild', 'Medium', 'Spicy'],
            'Sweetness' => ['Low', 'Normal', 'High'],
            'Temperature' => ['Hot', 'Cold'],
        ];

        foreach ($data as $attributeName => $values) {

            $attribute = Attribute::where('name', $attributeName)->first();

            if ($attribute) {
                foreach ($values as $val) {
                    AttributeValue::create([
                        'attribute_id' => $attribute->id,
                        'value' => $val,
                        'status' => 1,
                    ]);
                }
            }
        }
    }
}
