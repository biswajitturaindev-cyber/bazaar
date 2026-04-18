<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EducationStationary;
use App\Models\MasterProduct;
use App\Models\ProductAgriculture;
use App\Models\ProductAutomobile;
use App\Models\ProductConstructionHardware;
use App\Models\ProductFashionLifestyle;
use App\Models\ProductFoodBeverages;
use App\Models\ProductHealth;
use App\Models\ProductHomeLiving;
use App\Models\ProductRetail;
use App\Models\ProductSports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorProductController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'business_id' => 'required',
                'business_category_id' => 'required',
                'business_sub_category_id' => 'nullable',
                'master_product_id' => 'required',
                'mrp' => 'nullable|numeric',
                'selling_price' => 'required|numeric'
            ]);

            DB::beginTransaction();

            // Decode all IDs
            $businessId = decodeIdOrFail($request->business_id);
            $categoryId = decodeIdOrFail($request->business_category_id);

            $subCategoryId = $request->business_sub_category_id
                ? decodeIdOrFail($request->business_sub_category_id)
                : null;

            $masterProductId = decodeIdOrFail($request->master_product_id);

            // Table mapping
            $tableMap = [
                1  => ProductFoodBeverages::class,
                2  => ProductConstructionHardware::class,
                3  => ProductHomeLiving::class,
                4  => ProductFashionLifestyle::class,
                5  => ProductAutomobile::class,
                6  => EducationStationary::class,
                7  => ProductAgriculture::class,
                8  => ProductRetail::class,
                9  => ProductHealth::class,
                10 => ProductSports::class,
            ];

            // Check category mapping
            if (!isset($tableMap[$categoryId])) {
                throw new \Exception('Invalid business category mapping');
            }

            $modelClass = $tableMap[$categoryId];

            // Get master product
            $master = MasterProduct::findOrFail($masterProductId);

            // Prevent duplicate (vendor-wise)
            if ($modelClass::where('business_id', $businessId)
                ->where('category_id', $master->category_id)
                ->where('sub_category_id', $master->sub_category_id)
                ->where('sub_sub_category_id', $master->sub_sub_category_id)
                ->where('name', $master->name)
                ->exists()) {

                throw new \Exception('Product already exists for this vendor');
            }

            // Create Product
            $newProduct = $modelClass::create([
                'business_id' => $businessId,
                'business_category_id' => $categoryId,
                'business_sub_category_id' => $subCategoryId,

                'category_id' => $master->category_id,
                'sub_category_id' => $master->sub_category_id,
                'sub_sub_category_id' => $master->sub_sub_category_id,

                'sku' => null,
                'hsn_id' => $master->hsn_id,

                'name' => $master->name,
                'image' => $master->image,
                'description' => $master->description,

                // Pricing
                'mrp' => $request->mrp ?? $master->product_price,
                'cost_price' => $master->product_price,
                'selling_price' => $request->selling_price,

                'discount' => 0,
                'final_price' => $request->selling_price,

                'manufacture_date' => null,
                'expiry_date' => null,

                'status' => 1,
            ]);

            // Optional: copy attributes if exist
            if (method_exists($master, 'attributes')) {
                foreach ($master->attributes ?? [] as $attr) {
                    $newProduct->attributes()->create([
                        'attribute_id' => $attr->attribute_id,
                        'attribute_value_id' => $attr->attribute_value_id,
                        'stock' => $attr->stock ?? 0,
                        'price' => $attr->price ?? 0,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product added successfully',
                //'data' => $newProduct
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
