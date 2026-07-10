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

        /*
        |--------------------------------------------------------------------------
        | Mobiles & Tablets
        |--------------------------------------------------------------------------
        */

        $mobilesTablets = Category::create([
            'name' => "Mobiles & Tablets",
            'description' => 'Mobiles & Tablets category',
            'status' => 1,
        ]);

        $mobilesTabletsSubCategories = [

            'Smartphones' => [
                'Android Smartphones',
                'iPhones',
                '5G Smartphones',
                'Gaming Smartphones',
                'Budget Smartphones',
                'Flagship Smartphones',
            ],

            'Tablets' => [
                'Android Tablets',
                'iPads',
                'Kids Tablets',
                'Graphic Tablets',
            ],

            'Mobile Accessories' => [
                'Chargers',
                'Power Banks',
                'Phone Cases',
                'Screen Protectors',
                'Mobile Holders',
            ],

        ];

        foreach ($mobilesTabletsSubCategories as $subCategoryName => $items) {

            $subCategory = SubCategory::create([
                'category_id' => $mobilesTablets->id,
                'name' => $subCategoryName,
                'description' => $subCategoryName,
                'status' => 1,
            ]);

            foreach ($items as $item) {

                SubCategoryItem::create([
                    'category_id' => $mobilesTablets->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $item,
                    'description' => $item,
                    'status' => 1,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Computers & Laptops
        |--------------------------------------------------------------------------
        */

        $computersLaptops = Category::create([
            'name' => "Computers & Laptops",
            'description' => 'Computers & Laptops category',
            'status' => 1,
        ]);

        $computersLaptopsSubCategories = [

            'Laptops' => [
                'Gaming Laptops',
                'Business Laptops',
                'Ultrabooks',
                '2-in-1 Laptops',
                'Student Laptops',
            ],

            'Desktop Computers' => [
                'Gaming PCs',
                'All-in-One PCs',
                'Mini PCs',
                'Workstations',
            ],

            'Computer Accessories' => [
                'Keyboards',
                'Mouse',
                'Webcams',
                'Laptop Bags',
                'Cooling Pads',
            ],

        ];

        foreach ($computersLaptopsSubCategories as $subCategoryName => $items) {

            $subCategory = SubCategory::create([
                'category_id' => $computersLaptops->id,
                'name' => $subCategoryName,
                'description' => $subCategoryName,
                'status' => 1,
            ]);

            foreach ($items as $item) {

                SubCategoryItem::create([
                    'category_id' => $computersLaptops->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $item,
                    'description' => $item,
                    'status' => 1,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TV & Entertainment
        |--------------------------------------------------------------------------
        */

        $tvEntertainment = Category::create([
            'name' => "TV & Entertainment",
            'description' => 'TV & Entertainment category',
            'status' => 1,
        ]);

        $tvEntertainmentSubCategories = [

            'Televisions' => [
                'Smart TVs',
                'LED TVs',
                'QLED TVs',
                'OLED TVs',
                'Android TVs',
            ],

            'Home Entertainment' => [
                'Soundbars',
                'Home Theater Systems',
                'Projectors',
                'Streaming Devices',
            ],

        ];

        foreach ($tvEntertainmentSubCategories as $subCategoryName => $items) {

            $subCategory = SubCategory::create([
                'category_id' => $tvEntertainment->id,
                'name' => $subCategoryName,
                'description' => $subCategoryName,
                'status' => 1,
            ]);

            foreach ($items as $item) {

                SubCategoryItem::create([
                    'category_id' => $tvEntertainment->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $item,
                    'description' => $item,
                    'status' => 1,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Audio Devices
        |--------------------------------------------------------------------------
        */

        $audioDevices = Category::create([
            'name' => "Audio Devices",
            'description' => 'Audio Devices category',
            'status' => 1,
        ]);

        $audioDevicesSubCategories = [

            'Headphones & Earphones' => [
                'Wireless Earbuds',
                'Bluetooth Headphones',
                'Gaming Headsets',
                'Neckbands',
                'Wired Earphones',
            ],

            'Speakers' => [
                'Bluetooth Speakers',
                'Portable Speakers',
                'Smart Speakers',
                'Party Speakers',
            ],

        ];

        foreach ($audioDevicesSubCategories as $subCategoryName => $items) {

            $subCategory = SubCategory::create([
                'category_id' => $audioDevices->id,
                'name' => $subCategoryName,
                'description' => $subCategoryName,
                'status' => 1,
            ]);

            foreach ($items as $item) {

                SubCategoryItem::create([
                    'category_id' => $audioDevices->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $item,
                    'description' => $item,
                    'status' => 1,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Cameras & Photography
        |--------------------------------------------------------------------------
        */

        $camerasPhotography = Category::create([
            'name' => "Cameras & Photography",
            'description' => 'Cameras & Photography category',
            'status' => 1,
        ]);

        $camerasPhotographySubCategories = [

            'Cameras' => [
                'DSLR Cameras',
                'Mirrorless Cameras',
                'Action Cameras',
                'Instant Cameras',
            ],

            'Camera Accessories' => [
                'Lenses',
                'Tripods',
                'Camera Bags',
                'Gimbals',
                'Memory Cards',
            ],

        ];

        foreach ($camerasPhotographySubCategories as $subCategoryName => $items) {

            $subCategory = SubCategory::create([
                'category_id' => $camerasPhotography->id,
                'name' => $subCategoryName,
                'description' => $subCategoryName,
                'status' => 1,
            ]);

            foreach ($items as $item) {

                SubCategoryItem::create([
                    'category_id' => $camerasPhotography->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $item,
                    'description' => $item,
                    'status' => 1,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Gaming
        |--------------------------------------------------------------------------
        */

        $gaming = Category::create([
            'name' => "Gaming",
            'description' => 'Gaming category',
            'status' => 1,
        ]);

        $gamingSubCategories = [

            'Gaming Consoles' => [
                'PlayStation',
                'Xbox',
                'Nintendo Switch',
                'Handheld Consoles',
            ],

            'Gaming Accessories' => [
                'Controllers',
                'Gaming Keyboards',
                'Gaming Mouse',
                'VR Headsets',
            ],

        ];

        foreach ($gamingSubCategories as $subCategoryName => $items) {

            $subCategory = SubCategory::create([
                'category_id' => $gaming->id,
                'name' => $subCategoryName,
                'description' => $subCategoryName,
                'status' => 1,
            ]);

            foreach ($items as $item) {

                SubCategoryItem::create([
                    'category_id' => $gaming->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $item,
                    'description' => $item,
                    'status' => 1,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Home Appliances
        |--------------------------------------------------------------------------
        */

        $homeAppliances = Category::create([
            'name' => "Home Appliances",
            'description' => 'Home Appliances category',
            'status' => 1,
        ]);

        $homeAppliancesSubCategories = [

            'Kitchen Appliances' => [
                'Microwave Ovens',
                'Air Fryers',
                'Mixer Grinders',
                'Electric Kettles',
                'Induction Cooktops',
            ],

            'Cleaning Appliances' => [
                'Vacuum Cleaners',
                'Robot Vacuum Cleaners',
                'Steam Cleaners',
            ],

            'Cooling Appliances' => [
                'Air Conditioners',
                'Air Coolers',
                'Fans',
            ],

        ];

        foreach ($homeAppliancesSubCategories as $subCategoryName => $items) {

            $subCategory = SubCategory::create([
                'category_id' => $homeAppliances->id,
                'name' => $subCategoryName,
                'description' => $subCategoryName,
                'status' => 1,
            ]);

            foreach ($items as $item) {

                SubCategoryItem::create([
                    'category_id' => $homeAppliances->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $item,
                    'description' => $item,
                    'status' => 1,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Wearables
        |--------------------------------------------------------------------------
        */

        $wearables = Category::create([
            'name' => "Wearables",
            'description' => 'Wearables category',
            'status' => 1,
        ]);

        $wearablesSubCategories = [

            'Smartwatches' => [
                'Fitness Smartwatches',
                'Premium Smartwatches',
                'Kids Smartwatches',
                'Sports Smartwatches',
            ],

            'Fitness Devices' => [
                'Fitness Bands',
                'Smart Rings',
                'Heart Rate Monitors',
            ],

        ];

        foreach ($wearablesSubCategories as $subCategoryName => $items) {

            $subCategory = SubCategory::create([
                'category_id' => $wearables->id,
                'name' => $subCategoryName,
                'description' => $subCategoryName,
                'status' => 1,
            ]);

            foreach ($items as $item) {

                SubCategoryItem::create([
                    'category_id' => $wearables->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $item,
                    'description' => $item,
                    'status' => 1,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Networking & Smart Home
        |--------------------------------------------------------------------------
        */

        $networkingSmartHome = Category::create([
            'name' => "Networking & Smart Home",
            'description' => 'Networking & Smart Home category',
            'status' => 1,
        ]);

        $networkingSmartHomeSubCategories = [

            'Networking Devices' => [
                'WiFi Routers',
                'Mesh WiFi Systems',
                'Range Extenders',
                'Network Switches',
            ],

            'Smart Home Devices' => [
                'Smart Bulbs',
                'Smart Plugs',
                'Smart Door Locks',
                'Video Doorbells',
                'Smart Security Cameras',
            ],

        ];

        foreach ($networkingSmartHomeSubCategories as $subCategoryName => $items) {

            $subCategory = SubCategory::create([
                'category_id' => $networkingSmartHome->id,
                'name' => $subCategoryName,
                'description' => $subCategoryName,
                'status' => 1,
            ]);

            foreach ($items as $item) {

                SubCategoryItem::create([
                    'category_id' => $networkingSmartHome->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $item,
                    'description' => $item,
                    'status' => 1,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Office Electronics
        |--------------------------------------------------------------------------
        */

        $officeElectronics = Category::create([
            'name' => "Office Electronics",
            'description' => 'Office Electronics category',
            'status' => 1,
        ]);

        $officeElectronicsSubCategories = [

            'Printers' => [
                'Inkjet Printers',
                'Laser Printers',
                'All-in-One Printers',
            ],

            'Scanners' => [
                'Document Scanners',
                'Photo Scanners',
            ],

            'Projectors' => [
                'Business Projectors',
                'Home Projectors',
                'Portable Projectors',
            ],

        ];

        foreach ($officeElectronicsSubCategories as $subCategoryName => $items) {

            $subCategory = SubCategory::create([
                'category_id' => $officeElectronics->id,
                'name' => $subCategoryName,
                'description' => $subCategoryName,
                'status' => 1,
            ]);

            foreach ($items as $item) {

                SubCategoryItem::create([
                    'category_id' => $officeElectronics->id,
                    'sub_category_id' => $subCategory->id,
                    'name' => $item,
                    'description' => $item,
                    'status' => 1,
                ]);
            }
        }

    }
}
