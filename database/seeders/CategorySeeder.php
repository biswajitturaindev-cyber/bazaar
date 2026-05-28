<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\SubCategoryItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Men's Fashion
        |--------------------------------------------------------------------------
        */

        $mensFashion = Category::create([
            'name' => "Men's Fashion",
            'description' => 'Men fashion category',
            'status' => 1,
        ]);

        $mensSubCategories = [

            'T-Shirts' => [
                'Round Neck T-Shirts',
                'V-Neck T-Shirts',
                'Polo T-Shirts',
                'Oversized T-Shirts',
                'Graphic T-Shirts',
                'Printed T-Shirts',
                'Plain T-Shirts',
                'Full Sleeve T-Shirts',
                'Sleeveless T-Shirts',
            ],

            'Shirts' => [
                'Casual Shirts',
                'Formal Shirts',
                'Denim Shirts',
                'Linen Shirts',
                'Checked Shirts',
                'Printed Shirts',
                'Slim Fit Shirts',
                'Oversized Shirts',
            ],

            'Jackets & Outerwear' => [
                'Bomber Jackets',
                'Denim Jackets',
                'Leather Jackets',
                'Blazers',
                'Hoodies',
                'Sweatshirts',
                'Winter Coats',
            ],

            'Jeans' => [
                'Skinny Jeans',
                'Slim Fit Jeans',
                'Regular Fit Jeans',
                'Relaxed Fit Jeans',
                'Distressed Jeans',
            ],

            'Trousers' => [
                'Formal Trousers',
                'Chinos',
                'Cargo Pants',
                'Joggers',
                'Track Pants',
            ],

            'Shorts' => [
                'Denim Shorts',
                'Sports Shorts',
                'Cotton Shorts',
                'Cargo Shorts',
            ],

            'Kurtas' => [
                'Cotton Kurtas',
                'Printed Kurtas',
                'Pathani Kurtas',
                'Embroidered Kurtas',
            ],

            'Sherwanis' => [
                'Wedding Sherwanis',
                'Indo-Western Sherwanis',
                'Designer Sherwanis',
            ],

            'Ethnic Bottoms' => [
                'Pajama',
                'Dhoti',
                'Churidar',
            ],

            'Innerwear' => [
                'Vests',
                'Briefs',
                'Boxers',
                'Trunks',
            ],

            'Nightwear' => [
                'Night Suits',
                'Lounge Pants',
                'Robes',
            ],

        ];

        foreach ($mensSubCategories as $subCategoryName => $items) {

            $subCategory = SubCategory::create([
                'category_id' => $mensFashion->id,
                'name' => $subCategoryName,
                'description' => $subCategoryName,
                'status' => 1,
            ]);

            foreach ($items as $item) {

                SubCategoryItem::create([
                    'category_id' => $mensFashion->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $item,
                    'description' => $item,
                    'status' => 1,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Women's Fashion
        |--------------------------------------------------------------------------
        */

        $womensFashion = Category::create([
            'name' => "Women's Fashion",
            'description' => 'Women fashion category',
            'status' => 1,
        ]);

        $womenSubCategories = [

            'Casual Dresses' => [
                'Maxi Dresses',
                'Mini Dresses',
                'Midi Dresses',
                'Bodycon Dresses',
            ],

            'Party Dresses' => [
                'Sequin Dresses',
                'Cocktail Dresses',
                'Evening Gowns',
            ],

            'Tops' => [
                'Crop Tops',
                'Peplum Tops',
                'Tank Tops',
                'Tunics',
                'T-Shirts',
                'Graphic T-Shirts',
                'Oversized T-Shirts',
                'Printed T-Shirts',
            ],

            'Jeans' => [
                'Skinny Jeans',
                'Mom Jeans',
                'Bootcut Jeans',
                'Boyfriend Jeans',
            ],

            'Pants & Trousers' => [
                'Palazzo Pants',
                'Culottes',
                'Jeggings',
                'Leggings',
            ],

            'Skirts & Shorts' => [
                'Mini Skirts',
                'Pencil Skirts',
                'Denim Shorts',
            ],

            'Sarees' => [
                'Silk Sarees',
                'Cotton Sarees',
                'Banarasi Sarees',
                'Designer Sarees',
                'Bridal Sarees',
            ],

            'Salwar Suits' => [
                'Anarkali Suits',
                'Straight Cut Suits',
                'Patiala Suits',
                'Palazzo Suits',
            ],

            'Kurtis' => [
                'A-Line Kurtis',
                'Printed Kurtis',
                'Embroidered Kurtis',
                'High-Low Kurtis',
            ],

            'Lehenga' => [
                'Bridal Lehenga',
                'Party Wear Lehenga',
                'Designer Lehenga',
            ],

            'Lingerie' => [
                'Bras',
                'Panties',
                'Shapewear',
                'Camisoles',
            ],

            'Sleepwear' => [
                'Night Suits',
                'Satin Nightwear',
                'Robes',
            ],

        ];

        foreach ($womenSubCategories as $subCategoryName => $items) {

            $subCategory = SubCategory::create([
                'category_id' => $womensFashion->id,
                'name' => $subCategoryName,
                'description' => $subCategoryName,
                'status' => 1,
            ]);

            foreach ($items as $item) {

                SubCategoryItem::create([
                    'category_id' => $womensFashion->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $item,
                    'description' => $item,
                    'status' => 1,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Kids Fashion
        |--------------------------------------------------------------------------
        */

        $kidsFashion = Category::create([
            'name' => "Kids Fashion",
            'description' => 'Kids fashion category',
            'status' => 1,
        ]);

        $kidsSubCategories = [

            'Boys Fashion' => [
                'T-Shirts',
                'Shirts',
                'Hoodies',
                'Jeans',
                'Shorts',
                'Joggers',
                'Kurta Sets',
                'Sherwani Sets',
            ],

            'Girls Fashion' => [
                'Party Dresses',
                'Frocks',
                'Maxi Dresses',
                'Tops',
                'T-Shirts',
                'Tunics',
                'Bottomwear',
                'Leggings',
                'Skirts',
                'Jeans',
                'Ethnic Wear',
                'Lehenga Choli',
                'Kurti Sets',
                'Gowns',
            ],

            'Baby Fashion' => [
                'Baby Clothing',
                'Rompers',
                'Bodysuits',
                'Sleepsuits',
                'Baby Sets',
                'Baby Accessories',
                'Bibs',
                'Caps',
                'Mittens',
                'Baby Shoes',
            ],

        ];

        foreach ($kidsSubCategories as $subCategoryName => $items) {

            $subCategory = SubCategory::create([
                'category_id' => $kidsFashion->id,
                'name' => $subCategoryName,
                'description' => $subCategoryName,
                'status' => 1,
            ]);

            foreach ($items as $item) {

                SubCategoryItem::create([
                    'category_id' => $kidsFashion->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $item,
                    'description' => $item,
                    'status' => 1,
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Automobile Category
        |--------------------------------------------------------------------------
        */

        $data = [

            'Hatchback' => [
                'Compact Hatchback',
                'Premium Hatchback',
                'Electric Hatchback',
            ],

            'Sedan' => [
                'Compact Sedan',
                'Mid-Size Sedan',
                'Premium Sedan',
                'Luxury Sedan',
                'Sports Sedan',
            ],

            'SUV' => [
                'Compact SUV',
                'Mid-Size SUV',
                'Full-Size SUV',
                'Off-Road SUV',
                'Luxury SUV',
                'Electric SUV',
                'Urban SUV',
                'Mini SUV',
                '4x4 SUV',
                'Adventure SUV',
            ],

            'MUV' => [
                'Family MUV',
                'Premium MUV',
                'Luxury MUV',
            ],

            'Coupe' => [
                'Sports Coupe',
                'Luxury Coupe',
                'Performance Coupe',
            ],

            'Convertible' => [
                'Soft Top Convertible',
                'Hard Top Convertible',
                'Luxury Convertible',
            ],

            'Wagon' => [
                'Estate Wagon',
                'Luxury Wagon',
                'Cross Wagon',
            ],

            'Pickup Truck' => [
                'Lifestyle Pickup',
                'Commercial Pickup',
                'Off-Road Pickup',
            ],

            'Sports Car' => [
                'Supercar',
                'Hypercar',
                'Muscle Car',
                'Track Car',
            ],

            'Luxury Car' => [
                'Luxury Sedan',
                'Luxury SUV',
                'Executive Car',
                'Ultra Luxury Car',
            ],

            'Electric Car' => [
                'Electric Hatchback',
                'Electric Sedan',
                'Electric SUV',
                'Luxury EV',
                'Compact Electric SUV',
                'Premium Electric SUV',
            ],

            'Hybrid Car' => [
                'Mild Hybrid',
                'Strong Hybrid',
                'Plug-in Hybrid',
            ],

            'CNG Cars' => [
                'Factory Fitted CNG',
                'Aftermarket CNG',
            ],

        ];

        foreach ($data as $categoryName => $subCategories) {

            /*
            |--------------------------------------------------------------------------
            | Category
            |--------------------------------------------------------------------------
            */
            $category = Category::create([
                'name' => $categoryName,
                'description' => $categoryName,
                'status' => 1,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Sub Categories
            |--------------------------------------------------------------------------
            */
            foreach ($subCategories as $subCategoryName) {

                $subCategory = SubCategory::create([
                    'category_id' => $category->id,
                    'name' => $subCategoryName,
                    'description' => $subCategoryName,
                    'status' => 1,
                ]);

                /*
                |--------------------------------------------------------------------------
                | SUV Sub Sub Categories
                |--------------------------------------------------------------------------
                */
                if ($categoryName == 'SUV') {

                    if ($subCategoryName == 'Compact SUV') {

                        $compactItems = [
                            'Urban SUV',
                            'Mini SUV',
                        ];

                        foreach ($compactItems as $item) {

                            SubCategoryItem::create([
                                'category_id' => $category->id,
                                'sub_category_id' => $subCategory->id,
                                'name' => $item,
                                'description' => $item,
                                'status' => 1,
                            ]);
                        }
                    }

                    if ($subCategoryName == 'Off-Road SUV') {

                        $offRoadItems = [
                            '4x4 SUV',
                            'Adventure SUV',
                        ];

                        foreach ($offRoadItems as $item) {

                            SubCategoryItem::create([
                                'category_id' => $category->id,
                                'sub_category_id' => $subCategory->id,
                                'name' => $item,
                                'description' => $item,
                                'status' => 1,
                            ]);
                        }
                    }
                }
            }
        }


    }
}
