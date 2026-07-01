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
        $attributesFoodBeverages = [
            'Restaurant' => ['Plate', 'Half Plate', 'Full Plate', 'Bowl', 'Small Bowl', 'Big Bowl', 'Portion', 'Serving', 'Thali', 'Combo', 'Slice', 'Piece', 'Box', 'Packet', 'Container', 'Wrap', 'Gram', 'Kilogram', 'Milliliter', 'Liter', 'Cup', 'Glass', 'Bottle', 'Can', 'Jar', 'Tray', 'Dozen', 'Pair', 'Platter', 'Buffet', 'Skewer', 'Stick'],
            'Fast Food' => ['Burger', 'Meal', 'Bucket', 'Meal Box', 'Slice', 'Wrap', 'Roll', 'Plate', 'Half Plate', 'Full Plate', 'Bowl', 'Portion', 'Serving', 'Combo', 'Piece', 'Box', 'Packet', 'Container', 'Gram', 'Kilogram', 'Milliliter', 'Liter', 'Cup', 'Glass', 'Bottle', 'Can', 'Tray', 'Dozen', 'Pair'],
            'Cafe' => ['Cup', 'Mug', 'Glass', 'Shot', 'Pitcher', 'Small', 'Medium', 'Large', 'Regular', 'Tall', 'Venti', 'Grande', 'Ounce', 'Milliliter', 'Liter', 'Piece', 'Slice', 'Portion', 'Plate', 'Combo', 'Gram', 'Kilogram', 'Bottle', 'Box', 'Carafe', 'Flask', 'Sachet', 'Pack'],
            'Tea House' => ['Cup', 'Mug', 'Glass', 'Shot', 'Pitcher', 'Small', 'Medium', 'Large', 'Regular', 'Pot', 'Kettle', 'Ounce', 'Milliliter', 'Liter', 'Piece', 'Slice', 'Portion', 'Plate', 'Combo', 'Gram', 'Kilogram', 'Bottle', 'Box', 'Sachet', 'Bag', 'Pack', 'Tin'],
            'Sweet Shop' => ['Piece', 'Gram', 'Kilogram', '250g Box', '500g Box', '1kg Box', 'Gift Box', 'Assorted Box', 'Bowl', 'Pack', 'Combo Box', 'Family Pack', 'Premium Box', 'Cup', 'Glass', 'Bottle', 'Tin', 'Platter', 'Tray'],
            'Cake Shop & Bakery' => ['Piece', 'Slice', 'Whole', 'Kilogram', 'Gram', 'Half Slice', 'Portion', 'Pound', 'Ounce', 'Pack', 'Dozen', 'Tray', 'Box', 'Combo Pack', 'Gift Box', 'Tier', 'Pastry Box', 'Cupcake Box', 'Tin'],
            'Ice Cream Parlour' => ['Scoop', 'Single Scoop', 'Double Scoop', 'Triple Scoop', 'Cup', 'Cone', 'Waffle Cone', 'Stick', 'Slice', 'Sundae Cup', 'Tub', 'Milliliter', 'Liter', 'Gram', 'Kilogram', 'Family Pack', 'Party Pack', 'Pint', 'Quart', 'Gallon', 'Brick', 'Box'],
            'Egg Shop' => ['Piece', 'Nos', 'Dozen', 'Half Dozen', 'Tray (30 pcs)', 'Box (6 pcs)', 'Box (12 pcs)', 'Carton', 'Crate', 'Pack', 'Pair'],
            'Raw Meat (Chicken / Fish / Mutton)' => ['Kilogram', 'Gram', 'Pound', 'Ounce', 'Piece', 'Leg Piece', 'Breast Piece', 'Curry Cut', 'Whole', 'Half', 'Slice', 'Steak Cut', 'Boneless', 'With Bone', 'Fillet', 'Mince', 'Packet', 'Bag', 'Box', 'Vacuum Pack', 'Chop', 'Rack', 'Carcass', 'Portion'],
            'Fruits' => ['Kilogram', 'Gram', 'Pound', 'Ounce', 'Piece', 'Nos', 'Dozen', 'Half Dozen', 'Bunch', 'Box', 'Carton', 'Crate', 'Slice', 'Half', 'Quarter', 'Bowl', 'Glass', 'Cup', 'Bottle', 'Basket', 'Punnet', 'Net', 'Bag', 'Tray', 'Pack'],
            'Fresh Vegetables' => ['Kilogram', 'Gram', 'Pound', 'Ounce', 'Piece', 'Nos', 'Bunch', 'Bundle', 'Sack', 'Bag', 'Crate', 'Half', 'Quarter', 'Slice', 'Pack', 'Net', 'Basket', 'Head', 'Clove', 'Stalk', 'Stem'],
            'Groceries' => ['Gram', 'Kilogram', 'Milligram', 'Milliliter', 'Liter', 'Packet', 'Pack', 'Pouch', 'Box', 'Jar', 'Bottle', 'Can', 'Bag', 'Sack', 'Carton', 'Crate', 'Combo Pack', 'Family Pack', 'Value Pack', 'Piece', 'Nos', 'Dozen', 'Tray', 'Tin', 'Roll', 'Tube', 'Bar', 'Sachet'],
            'Departmental Store' => ['Gram', 'Kilogram', 'Milliliter', 'Liter', 'Piece', 'Nos', 'Box', 'Packet', 'Pack', 'Bottle', 'Jar', 'Can', 'Pouch', 'Tube', 'Carton', 'Case', 'Sack', 'Crate', 'Pump', 'Refill Pack', 'Dozen', 'Tray', 'Bunch', 'Slice', 'Combo Pack', 'Family Pack', 'Value Pack', 'Roll', 'Bar', 'Tin', 'Pair', 'Set', 'Bundle', 'Pallet'],
            'Dry Fruits' => ['Gram', 'Kilogram', 'Pound', 'Ounce', 'Pack', 'Packet', 'Box', 'Jar', 'Pouch', 'Gift Box', 'Combo Pack', 'Assorted Box', 'Premium Pack', 'Bag', 'Sack', 'Carton', 'Assorted Pack', 'Family Pack', 'Container', 'Tin', 'Can', 'Vacuum Pack', 'Tray'],
            'Home Made Food Products' => ['Gram', 'Kilogram', 'Milliliter', 'Liter', 'Piece', 'Box', 'Jar', 'Bottle', 'Pouch', 'Packet', 'Pack', 'Dozen', 'Combo Pack', 'Gift Box', 'Assorted Pack', 'Family Pack', 'Bag', 'Container', 'Plate', 'Bowl', 'Portion', 'Tiffin', 'Dabba', 'Thali'],
            'Factory Made Food Products' => ['Gram', 'Kilogram', 'Milliliter', 'Liter', 'Piece', 'Box', 'Jar', 'Bottle', 'Pouch', 'Packet', 'Pack', 'Dozen', 'Combo Pack', 'Gift Box', 'Assorted Pack', 'Family Pack', 'Bag', 'Container', 'Tin', 'Can', 'Carton', 'Case', 'Pallet', 'Sachet', 'Tube', 'Roll', 'Bar'],
        ];

        foreach ($attributesFoodBeverages as $subCategoryName => $names) {

            $subCategory = BusinessSubCategory::where('business_category_id', 1)
                ->where('name', $subCategoryName)
                ->first();

            if (!$subCategory) {
                continue;
            }

            foreach ($names as $name) {
                AttributeMaster::create([
                    'business_category_id'     => $subCategory->business_category_id,
                    'business_sub_category_id' => $subCategory->id,
                    'name'                     => $name,
                ]);
            }
        }


        $attributesConstructionHardware = [
                'Hardware Shop' => ['Piece', 'Nos', 'Pair', 'Set', 'Gram', 'Kilogram', 'Quintal', 'Millimeter', 'Inch', 'Feet', 'Meter', 'Packet', 'Box', 'Bundle', 'Carton', 'Kit', 'sq.ft', 'sq.m', 'Milliliter', 'Liter'],
                'Builders' => ['Piece', 'Nos', 'Pair', 'Set', 'Gram', 'Kilogram', 'Quintal', 'Millimeter', 'Inch', 'Feet', 'Meter', 'Packet', 'Box', 'Bundle', 'Carton', 'Kit', 'sq.ft', 'sq.m', 'Milliliter', 'Liter'],
                'Marbel Shop' => ['sq.ft', 'sq.m', 'Box', 'Carton', 'Piece', 'Slab', 'Pallet', 'Truck Load'],
                'Electric Material' => ['Piece', 'Nos', 'Set', 'Meter', 'Feet', 'Roll', 'Coil', 'Gram', 'Kilogram', 'Milliliter', 'Liter', 'Packet', 'Box', 'Carton', 'Kit', 'Watt', 'Volt', 'Ampere'],
                'Ply Shop' => ['sq.ft', 'sq.m', 'Sheet', 'Board', 'Millimeter', 'Feet', 'Bundle', 'Pack', 'Lot', 'Roll'],
                'Home Paint' => ['Milliliter', 'Liter', 'Can', 'Bucket', 'Drum', 'Bottle', 'sq.ft', 'sq.m', 'Piece', 'Set'],
        ];
        foreach ($attributesConstructionHardware as $subCategoryName => $names) {

            $subCategory = BusinessSubCategory::where('business_category_id', 2)
                ->where('name', $subCategoryName)
                ->first();

            if (!$subCategory) {
                continue;
            }

            foreach ($names as $name) {
                AttributeMaster::create([
                    'business_category_id'     => $subCategory->business_category_id,
                    'business_sub_category_id' => $subCategory->id,
                    'name'                     => $name,
                ]);
            }
        }


        $attributesHomeLiving = [
                'Furniture' => ['Piece', 'Set', 'Combo', 'Feet', 'Inch', 'Meter', 'Box', 'Carton', 'Color', 'Wood', 'Metal', 'Finish', 'Design', 'Centimeter', 'Millimeter', 'Yard', 'Kilogram', 'Pound', 'Pair'],
                'Home Decoration' => ['Piece', 'Set', 'Combo Pack', 'Feet', 'Inch', 'Meter', 'Box', 'Carton', 'Color', 'Wood', 'Metal', 'Finish', 'Design', 'Centimeter', 'Millimeter', 'Yard', 'Pair', 'Gram', 'Ounce', 'Bundle', 'Roll'],
                'Home Interior' => ['sq.ft', 'sq.m', 'Running Feet', 'Meter', 'Piece', 'Set', 'Sheet', 'Board', 'Liter', 'Milliliter', 'Package', 'Project', 'Lot', 'Centimeter', 'Millimeter', 'Yard', 'Gallon', 'Pint', 'Quart', 'Cubic Meter', 'Cubic Feet', 'Roll', 'Bundle', 'Tile', 'Slab'],
        ];
        foreach ($attributesHomeLiving as $subCategoryName => $names) {

            $subCategory = BusinessSubCategory::where('business_category_id', 3)
                ->where('name', $subCategoryName)
                ->first();

            if (!$subCategory) {
                continue;
            }

            foreach ($names as $name) {
                AttributeMaster::create([
                    'business_category_id'     => $subCategory->business_category_id,
                    'business_sub_category_id' => $subCategory->id,
                    'name'                     => $name,
                ]);
            }
        }


        $attributesFashionLifestyle = [
                'Fashion' => ['Piece', 'Set', 'Pair', 'Size', 'Color', 'Material', 'Combo Pack', 'Family Pack', 'Meter', 'Yard', 'Dozen', 'Bundle', 'Pack', 'Roll'],
                'Shoes' => ['Pair', 'Piece', 'Set', 'Size', 'Color', 'Material', 'Combo Pack', 'Box', 'Carton', 'Dozen', 'UK Size', 'US Size'],
                'Watches' => ['Piece', 'Set', 'Combo Pack', 'Dial', 'Color', 'Strap', 'Pack', 'Box', 'Gift Box', 'Pair', 'Carton', 'Case', 'Display Box'],
                'Bags & Luggage' => ['Piece', 'Liter', 'Size', 'Color', 'Material', 'Type', 'Set', 'Combo Pack', 'Box', 'Carton', 'Pair', 'Pack', 'Bag', 'Sack'],
                'Boutiques' => ['Piece', 'Set', 'Meter', 'Yard', 'Size', 'Color', 'Material', 'Combo Pack', 'Design', 'Box', 'Cover', 'Bag', 'Roll', 'Bundle', 'Spool', 'Dozen'],
                'Cosmetics & Imitation' => ['Piece', 'Milliliter', 'Gram', 'Shade', 'Color', 'Skin Type', 'Pack', 'Combo Pack', 'Kit', 'Box', 'Pair', 'Set', 'Bottle', 'Tube', 'Jar', 'Palette', 'Sachet', 'Pump', 'Spray', 'Ounce'],
                'Jewellery' => ['Gram', 'Milligram', 'Piece', 'Pair', 'Purity', 'Carat', 'Point', 'Box', 'Set', 'Color', 'Clarity', 'Cut', 'Origin', 'Ounce', 'Troy Ounce', 'Karat', 'String', 'Strand'],
        ];
        foreach ($attributesFashionLifestyle as $subCategoryName => $names) {

            $subCategory = BusinessSubCategory::where('business_category_id', 4)
                ->where('name', $subCategoryName)
                ->first();

            if (!$subCategory) {
                continue;
            }

            foreach ($names as $name) {
                AttributeMaster::create([
                    'business_category_id'     => $subCategory->business_category_id,
                    'business_sub_category_id' => $subCategory->id,
                    'name'                     => $name,
                ]);
            }
        }

        $attributesAutomobile = [
                'Cars' => ['Piece', 'Brand', 'Model', 'Variant', 'Transmission', 'Year', 'Set', 'Pair', 'Liter', 'Milliliter', 'Box', 'Kit', 'Ton', 'Kilogram', 'Pound', 'Gallon', 'Quart', 'Ounce', 'Inch', 'Foot', 'Meter'],
                'Bike' => ['Piece', 'Set', 'Pair', 'Liter', 'Milliliter', 'Box', 'Kit', 'Gallon', 'Quart', 'Ton', 'Kilogram', 'Pound', 'Ounce', 'Inch', 'Foot', 'Meter'],
                'Battery' => ['Piece', 'Volt', 'Ampere', 'Ampere-hour (Ah)', 'Watt', 'Kilowatt', 'Box', 'Pallet'],
                'Tyres' => ['Piece', 'Set', 'Pair', 'Inch', 'Millimeter', 'Pound', 'Kilogram', 'Pallet'],
                'Car Decor Items' => ['Piece', 'Set', 'Pair', 'Box', 'Kit', 'Pack', 'Bundle', 'Roll', 'Meter', 'Centimeter', 'Yard', 'Ounce', 'Gram'],
        ];
        foreach ($attributesAutomobile as $subCategoryName => $names) {

            $subCategory = BusinessSubCategory::where('business_category_id', 5)
                ->where('name', $subCategoryName)
                ->first();

            if (!$subCategory) {
                continue;
            }

            foreach ($names as $name) {
                AttributeMaster::create([
                    'business_category_id'     => $subCategory->business_category_id,
                    'business_sub_category_id' => $subCategory->id,
                    'name'                     => $name,
                ]);
            }
        }







    }
}
