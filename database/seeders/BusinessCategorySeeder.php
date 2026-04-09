<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = [
            'Food & Beverages' => [
                'Restaurant',
                'Fast Food',
                'Cafe',
                'Tea House',
                'Sweet Shop',
                'Cake Shop & Bakery',
                'Ice Cream Parlour',
                'Egg Shop',
                'Raw Meat (Chicken / Fish / Mutton)',
                'Fruits',
                'Fresh Vegetables',
                'Groceries',
                'Departmental Store',
                'Dry Fruits',
                'Home Made Food Products',
                'Factory Made Food Products',
            ],

            'Construction & Hardware' => [
                'Hardware Shop',
                'Builders',
                'Marble & Tiles',
                'Electric Materials',
                'Ply Shop',
                'Home Paint',
            ],

            'Home & Living' => [
                'Furniture',
                'Home Decoration',
                'Home Interior',
            ],

            'Fashion & Lifestyle' => [
                'Fashion (Male / Female)',
                'Winter Wear',
                'Shoes',
                'Watches',
                'Bags & Luggage',
                'Boutiques',
                'Cosmetics & Imitation Jewellery',
                'Jewellery Shop',
            ],

            'Automobile' => [
                'Pre-Owned Cars & Bikes',
                'Battery',
                'Tyres',
                'Car Decor Items',
            ],

            'Education & Stationery' => [
                'Book / Pen / Pencil',
                'Stationery Goods',
            ],

            'Agriculture & Nature' => [
                'Agriculture',
                'Nursery (Plants / Flowers)',
                'Nursery (Fish)',
                'Flower Shop',
            ],

            'Retail & General' => [
                'Gift Shop',
                'Printing Press',
                'Sculptor Making',
                'Agarbatti Sticks',
                'Dashakarma (Puja Items)',
            ],

            'Health & Medical' => [
                'Medicine',
            ],

            'Sports & Others' => [
                'Sports',
            ],
        ];

        foreach ($data as $category => $subCategories) {

            // Insert Category
            $categoryId = DB::table('business_categories')->insertGetId([
                'name' => $category,
                'image' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert Subcategories
            foreach ($subCategories as $sub) {
                DB::table('business_sub_categories')->insert([
                    'business_category_id' => $categoryId,
                    'name' => $sub,
                    'image' => null,
                    'commission' => 0,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
