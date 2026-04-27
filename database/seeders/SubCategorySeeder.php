<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sub_categories')->insert([
            [
                'category_id' => 1,
                'name' => 'Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1,
                'name' => 'Non Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => 2,
                'name' => 'Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2,
                'name' => 'Non Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => 3,
                'name' => 'Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 3,
                'name' => 'Non Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => 4,
                'name' => 'Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 4,
                'name' => 'Non Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => 5,
                'name' => 'Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 5,
                'name' => 'Non Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => 6,
                'name' => 'Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 6,
                'name' => 'Non Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => 7,
                'name' => 'Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 7,
                'name' => 'Non Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => 8,
                'name' => 'Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 8,
                'name' => 'Non Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => 9,
                'name' => 'Veg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 10,
                'name' => 'Shirts',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 10,
                'name' => 'pants',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 10,
                'name' => 'skirts',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 10,
                'name' => 'dresses',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 10,
                'name' => 'suits',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 10,
                'name' => 'underwear',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 10,
                'name' => 'socks',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 10,
                'name' => 'pyjamas',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 10,
                'name' => 'jackets',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 10,
                'name' => 'swimwear',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],



        ]);
    }
}
