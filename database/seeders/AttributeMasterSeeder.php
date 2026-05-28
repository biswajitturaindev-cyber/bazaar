<?php

namespace Database\Seeders;

use App\Models\AttributeMaster;
use App\Models\BusinessCategory;
use App\Models\BusinessSubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         /*
        |--------------------------------------------------------------------------
        | Disable Foreign Key Checks
        |--------------------------------------------------------------------------
        */
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        AttributeMaster::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /*
        |--------------------------------------------------------------------------
        | Business Category
        |--------------------------------------------------------------------------
        */
        $businessCategory = BusinessCategory::where(
            'name',
            'Fashion & Lifestyle'
        )->first();

        if (!$businessCategory) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Required Sub Categories
        |--------------------------------------------------------------------------
        */
        $requiredSubCategories = [

            'Fashion',
            'Shoe',
            'Watches',
            'Bag & Luggage',
            'Boutiques',
            'Jewellery',
            'Cosmetics & Imitation',

        ];

        /*
        |--------------------------------------------------------------------------
        | Create Missing Business Sub Categories
        |--------------------------------------------------------------------------
        */
        foreach ($requiredSubCategories as $subCategoryName) {

            BusinessSubCategory::updateOrCreate(

                [
                    'business_category_id' => $businessCategory->id,
                    'name' => $subCategoryName,
                ],

                [
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Attribute Master Data
        |--------------------------------------------------------------------------
        */
        $data = [

            /*
            |--------------------------------------------------------------------------
            | Fashion
            |--------------------------------------------------------------------------
            */
            'Fashion' => [

                'Piece',
                'Set',
                'Pair',

                'Size',
                'Color',
                'Material',

                'Combo Pack',
                'Family Pack',

                'Meter',
                'Yard',
                'Dozen',
                'Bundle',
                'Pack',
                'Roll',
            ],

            /*
            |--------------------------------------------------------------------------
            | Shoe
            |--------------------------------------------------------------------------
            */
            'Shoe' => [

                'Pair',
                'Piece',
                'Set',

                'Size',
                'Color',
                'Material',

                'Combo Pack',

                'Box',
                'Carton',
                'Dozen',

                'UK Size',
                'US Size',
            ],

            /*
            |--------------------------------------------------------------------------
            | Watches
            |--------------------------------------------------------------------------
            */
            'Watches' => [

                'Piece',
                'Set',
                'Combo Pack',

                'Dial',
                'Color',
                'Strap',

                'Pack',
                'Box',
                'Gift Box',

                'Pair',
                'Carton',
                'Case',
                'Display Box',
            ],

            /*
            |--------------------------------------------------------------------------
            | Bag & Luggage
            |--------------------------------------------------------------------------
            */
            'Bag & Luggage' => [

                'Piece',
                'Liter',

                'Size',
                'Color',
                'Material',
                'Type',

                'Set',
                'Combo Pack',

                'Box',
                'Carton',
                'Pair',
                'Pack',
                'Bag',
                'Sack',
            ],

            /*
            |--------------------------------------------------------------------------
            | Boutiques
            |--------------------------------------------------------------------------
            */
            'Boutiques' => [

                'Piece',
                'Set',
                'Meter',
                'Yard',

                'Size',
                'Color',
                'Material',
                'Design',

                'Combo Pack',

                'Box',
                'Cover',
                'Bag',
                'Roll',
                'Bundle',
                'Spool',
                'Dozen',
            ],

            /*
            |--------------------------------------------------------------------------
            | Jewellery
            |--------------------------------------------------------------------------
            */
            'Jewellery' => [

                'Gram',
                'Milligram',
                'Piece',
                'Pair',

                'Purity',

                'Carat',
                'Point',

                'Box',
                'Set',

                'Color',
                'Clarity',
                'Cut',
                'Origin',

                'Ounce',
                'Troy Ounce',
                'Karat',

                'String',
                'Strand',
            ],

            /*
            |--------------------------------------------------------------------------
            | Cosmetics & Imitation
            |--------------------------------------------------------------------------
            */
            'Cosmetics & Imitation' => [

                'Piece',
                'Milliliter',
                'Gram',

                'Shade',
                'Color',
                'Skin Type',

                'Pack',
                'Combo Pack',
                'Kit',

                'Box',
                'Pair',
                'Set',

                'Bottle',
                'Tube',
                'Jar',
                'Palette',
                'Sachet',
                'Pump',
                'Spray',
                'Ounce',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Insert Attribute Masters
        |--------------------------------------------------------------------------
        */
        foreach ($data as $subCategoryName => $attributes) {

            $subCategory = BusinessSubCategory::where(
                'business_category_id',
                $businessCategory->id
            )->where(
                'name',
                $subCategoryName
            )->first();

            if (!$subCategory) {
                continue;
            }

            foreach ($attributes as $attributeName) {

                AttributeMaster::updateOrCreate(

                    [
                        'business_category_id' => $businessCategory->id,
                        'business_sub_category_id' => $subCategory->id,
                        'name' => $attributeName,
                    ],

                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
