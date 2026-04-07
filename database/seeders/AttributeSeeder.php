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
        $attributes = [
            'Size',
            'Spice Level',
            'Sweetness',
            'Temperature',
        ];

        foreach ($attributes as $attr) {
            Attribute::create([
                'name' => $attr,
                'status' => 1,
            ]);
        }
    }
}
