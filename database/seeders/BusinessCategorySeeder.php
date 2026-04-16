<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BusinessCategorySeeder extends Seeder
{
    public function run(): void
    {
        $manager = new ImageManager(new Driver());

        $data = [
            'Food & Beverages' => [
                'image' => 'Vegetable Shop.webp',
                'subs' => [
                    ['name' => 'Restaurant', 'image' => 'food & beverages/Restuarant.webp'],
                    ['name' => 'Fast Food', 'image' => 'food & beverages/Fast Food.webp'],
                    ['name' => 'Cafe', 'image' => 'food & beverages/Cafe.webp'],
                    ['name' => 'Tea House', 'image' => 'food & beverages/Tea House.webp'],
                    ['name' => 'Sweet Shop', 'image' => 'food & beverages/Sweet Shop.webp'],
                    ['name' => 'Cake Shop & Bakery', 'image' => 'food & beverages/Cake Shop.webp'],
                    ['name' => 'Ice Cream Parlour', 'image' => 'food & beverages/Icecream parlour.webp'],
                    ['name' => 'Egg Shop', 'image' => 'food & beverages/Egg Shop.webp'],
                    ['name' => 'Raw Meat (Chicken / Fish / Mutton)', 'image' => 'food & beverages/Meat Shop.webp'],
                    ['name' => 'Fruits', 'image' => 'food & beverages/Fruit Shop.webp'],
                    ['name' => 'Fresh Vegetables', 'image' => 'food & beverages/Vegetable Shop.webp'],
                    ['name' => 'Groceries', 'image' => 'food & beverages/Grocery.webp'],
                    ['name' => 'Departmental Store', 'image' => 'food & beverages/Departmental Store.webp'],
                    ['name' => 'Dry Fruits', 'image' => 'food & beverages/Dry Fruits.webp'],
                    ['name' => 'Home Made Food Products', 'image' => 'food & beverages/Home Made Food.webp'],
                    ['name' => 'Factory Made Food Products', 'image' => 'food & beverages/Factory Made Food.webp'],
                ],
            ],

            'Construction & Hardware' => [
                'image' => 'Ply Shop.webp',
                'subs' => [
                    ['name' => 'Hardware Shop', 'image' => 'construction/Hardware Shop.webp'],
                    ['name' => 'Builders', 'image' => 'construction/Builders.webp'],
                    ['name' => 'Marble & Tiles', 'image' => 'construction/Marbel Shop.webp'],
                    ['name' => 'Electric Materials', 'image' => 'construction/Electric Material.webp'],
                    ['name' => 'Ply Shop', 'image' => 'construction/Ply Shop.webp'],
                    ['name' => 'Home Paint', 'image' => 'construction/Home Paint.webp'],
                ],
            ],

            'Home & Living' => [
                'image' => 'Furniture.webp',
                'subs' => [
                    ['name' => 'Furniture', 'image' => 'home living/Furniture.webp'],
                    ['name' => 'Home Decoration', 'image' => 'home living/Home Decoration.webp'],
                    ['name' => 'Home Interior', 'image' => 'home living/Home Interior.webp'],
                ],
            ],

            'Fashion & Lifestyle' => [
                'image' => 'Watches.webp',
                'subs' => [
                    ['name' => 'Fashion (Male / Female)', 'image' => 'fashion & lifestyle/Fashion.webp'],
                    ['name' => 'Winter Wear', 'image' => 'fashion & lifestyle/Fashion.webp'],
                    ['name' => 'Shoes', 'image' => 'fashion & lifestyle/Shoe.webp'],
                    ['name' => 'Watches', 'image' => 'fashion & lifestyle/Watches.webp'],
                    ['name' => 'Bags & Luggage', 'image' => 'fashion & lifestyle/Bag & Luggages.webp'],
                    ['name' => 'Boutiques', 'image' => 'fashion & lifestyle/Boutiques.webp'],
                    ['name' => 'Cosmetics & Imitation Jewellery', 'image' => 'fashion & lifestyle/Cosmetics & Imitation.webp'],
                    ['name' => 'Jewellery Shop', 'image' => 'fashion & lifestyle/Jewellery.webp'],
                ],
            ],

            'Automobile' => [
                'image' => 'Car accessories.webp',
                'subs' => [
                    ['name' => 'Pre-Owned Cars & Bikes', 'image' => 'automobile/Car.webp'],
                    ['name' => 'Battery', 'image' => 'automobile/Battery.webp'],
                    ['name' => 'Tyres', 'image' => 'automobile/Tyre.webp'],
                    ['name' => 'Car Decor Items', 'image' => 'automobile/Car accessories.webp'],
                ],
            ],

            'Education & Stationery' => [
                'image' => 'Pen.webp',
                'subs' => [
                    ['name' => 'Book / Pen / Pencil', 'image' => 'Education/Pen.webp'],
                    ['name' => 'Stationery Goods', 'image' => 'Education/Stationery Goods.webp'],
                ],
            ],

            'Agriculture & Nature' => [
                'image' => 'Agriculture.webp',
                'subs' => [
                    ['name' => 'Agriculture', 'image' => 'agriculture/Agriculture.webp'],
                    ['name' => 'Nursery (Plants / Flowers)', 'image' => 'agriculture/Nursery Plant.webp'],
                    ['name' => 'Nursery (Fish)', 'image' => 'agriculture/Nursery Fish.webp'],
                    ['name' => 'Flower Shop', 'image' => 'agriculture/Flower.webp'],
                ],
            ],

            'Retail & General' => [
                'image' => 'Sculptor Making.webp',
                'subs' => [
                    ['name' => 'Gift Shop', 'image' => 'agriculture/Gift Shop.webp'],
                    ['name' => 'Printing Press', 'image' => 'agriculture/Printing Press.webp'],
                    ['name' => 'Sculptor Making', 'image' => 'agriculture/Sculptor Making.webp'],
                    ['name' => 'Agarbatti Sticks', 'image' => 'agriculture/Agarbatti Sticks.webp'],
                    ['name' => 'Dashakarma (Puja Items)', 'image' => 'agriculture/dashakarma-bhandar-puja-items-seller-behala-kolkata-puja-item-dealers-vf2rg5zxxu.webp'],
                ],
            ],

            'Health & Medical' => [
                'image' => 'Medicine.webp',
                'subs' => [
                    ['name' => 'Medicine', 'image' => 'medical/Medicine.webp'],
                ],
            ],

            'Sports & Others' => [
                'image' => 'Sports.webp',
                'subs' => [
                    ['name' => 'Sports', 'image' => 'Sport/Sports.webp'],
                ],
            ],
        ];

        foreach ($data as $category => $details) {

            $fileName = $details['image'];

            // Source path (public folder)
            $sourcePath = public_path('assets/images/business_categories/' . $fileName);

            // Destination path (storage)
            $destinationPath = 'business_category/' . $fileName;

            if (File::exists($sourcePath)) {

                // Optional: prevent overwrite
                if (!Storage::disk('public')->exists($destinationPath)) {

                    // Resize image (300x300)
                    $image = $manager->read($sourcePath)
                        ->cover(399, 399);

                    // Save resized image to storage
                    Storage::disk('public')->put(
                        $destinationPath,
                        //(string) $image->encodeByExtension(pathinfo($fileName, PATHINFO_EXTENSION))
                        compressToTargetSize($image, 100)
                    );
                }
            }

            // Path to store in DB
            $imagePath =  $fileName;

            // Insert category
            $categoryId = DB::table('business_categories')->insertGetId([
                'name' => $category,
                'image' => $imagePath,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

           foreach ($details['subs'] as $sub) {

                $subName = $sub['name'];
                $subImageFile = $sub['image'] ?? null;

                $subImagePath = null;

                if ($subImageFile) {

                    // Convert to webp filename
                    $subFileName = pathinfo($subImageFile, PATHINFO_FILENAME) . '.webp';

                    // Source (public)
                    $subSourcePath = public_path('assets/images/business_sub_categories/' . $subImageFile);

                    // Destination (storage)
                    $subDestinationPath = 'business_sub_category/' . $subFileName;

                    if (File::exists($subSourcePath)) {

                        if (!Storage::disk('public')->exists($subDestinationPath)) {

                            $subImage = $manager->read($subSourcePath)
                                ->cover(399, 399);

                            Storage::disk('public')->put(
                                $subDestinationPath,
                                compressToTargetSize($subImage, 100)
                            );
                        }

                        // Save path for DB
                        $subImagePath =  $subFileName;
                    }
                }

                DB::table('business_sub_categories')->insert([
                    'business_category_id' => $categoryId,
                    'name' => $subName,
                    'image' => $subImagePath,
                    'commission' => 0,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }



        }
    }
}
