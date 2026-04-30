<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductImage;
use App\Http\Resources\ProductResource;
use App\Http\Resources\VariantResource;
use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\ProductVariantMeta;
use App\Models\ProductVendorStock;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Vinkla\Hashids\Facades\Hashids;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Product::with([
                'category',
                'subCategory',
                'subSubCategory',
                'hsn',
                'images',
                'attributes.attribute',
                'attributes.value'
            ])->latest()->get();

            return response()->json([
                'status' => true,
                'data' => ProductResource::collection($data)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         // Validation
    //         $rules = [
    //             'business_id' => 'required',
    //             'category_id' => 'required',
    //             'sub_category_id' => 'nullable',
    //             'sub_sub_category_id' => 'nullable',

    //             'name' => 'required|string',
    //             'description' => 'nullable|string',
    //             'status' => 'required|integer',

    //             // Variants
    //             'variants' => 'required|array|min:1',

    //             'variants.*.sku' => 'required|string|distinct|unique:product_variants,sku',
    //             'variants.*.barcode' => 'nullable|string|distinct|unique:product_variants,barcode',

    //             'variants.*.mrp' => 'required|numeric',
    //             'variants.*.cost_price' => 'nullable|numeric',
    //             'variants.*.selling_price' => 'nullable|numeric',
    //             'variants.*.discount' => 'nullable|numeric',

    //             'variants.*.stock' => 'nullable|integer',
    //             'variants.*.manufacture_date' => 'nullable|date',
    //             'variants.*.expiry_date' => 'nullable|date',

    //             // Images
    //             'variants.*.images' => 'nullable|array',
    //             'variants.*.images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10000',

    //             // Attributes
    //             'variants.*.attributes' => 'nullable|array',
    //             'variants.*.attributes.*.attribute_id' => 'required|exists:attributes,id',
    //             'variants.*.attributes.*.attribute_value_id' => 'required|exists:attribute_values,id',

    //             // Meta
    //             'variants.*.meta_title' => 'nullable|string',
    //             'variants.*.meta_keyword' => 'nullable|string',
    //             'variants.*.meta_description' => 'nullable|string',
    //         ];

    //         $data = $request->validate($rules);

    //         // Decode IDs
    //         $data['business_id'] = decodeIdOrFail($data['business_id'], 'Invalid Business ID');
    //         $data['category_id'] = decodeIdOrFail($data['category_id'], 'Invalid Category ID');

    //         if (!empty($data['sub_category_id'])) {
    //             $data['sub_category_id'] = decodeIdOrFail($data['sub_category_id'], 'Invalid Sub Category ID');
    //         }

    //         // Business
    //         $business = Business::findOrFail($data['business_id']);

    //         // Table map
    //         $tableMap = config('product.table_map');
    //         $categoryId = $business->business_category_id;

    //         if (!isset($tableMap[$categoryId])) {
    //             throw new \Exception('Invalid business category mapping');
    //         }

    //         $tableName = $tableMap[$categoryId];

    //         // 1. Create Product
    //         $productId = DB::table($tableName)->insertGetId([
    //             'business_id' => $business->id,
    //             'business_category_id' => $business->business_category_id,
    //             'business_sub_category_id' => $business->business_sub_category_id,
    //             'category_id' => $data['category_id'],
    //             'sub_category_id' => $data['sub_category_id'] ?? null,
    //             'sub_sub_category_id' => $data['sub_sub_category_id'] ?? null,

    //             'name' => $data['name'],
    //             'description' => $data['description'] ?? null,
    //             'status' => $data['status'],

    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);

    //         $variantIds = [];

    //         // 2. Variants Loop
    //         foreach ($data['variants'] as $index => $variantData) {

    //             // Variant
    //             $variant = ProductVariant::create([
    //                 'product_id' => $productId,
    //                 'product_type' => $categoryId,

    //                 'sku' => $variantData['sku'],
    //                 'barcode' => $variantData['barcode'] ?? null,

    //                 'price' => $variantData['selling_price'] ?? null,
    //                 'mrp' => $variantData['mrp'],
    //                 'cost_price' => $variantData['cost_price'] ?? null,
    //                 'selling_price' => $variantData['selling_price'] ?? null,
    //                 'discount' => $variantData['discount'] ?? 0,
    //                 'final_price' => ($variantData['selling_price'] ?? $variantData['mrp']) - ($variantData['discount'] ?? 0),

    //                 'manufacture_date' => $variantData['manufacture_date'] ?? null,
    //                 'expiry_date' => $variantData['expiry_date'] ?? null,
    //             ]);

    //             // Stock
    //             ProductVendorStock::create([
    //                 'product_variant_id' => $variant->id,
    //                 'business_id' => $business->id,
    //                 'stock' => $variantData['stock'] ?? 0,
    //             ]);

    //             // META (ADDED HERE)
    //             if (
    //                 !empty($variantData['meta_title']) ||
    //                 !empty($variantData['meta_keyword']) ||
    //                 !empty($variantData['meta_description'])
    //             ) {
    //                 ProductVariantMeta::updateOrCreate(
    //                     ['product_variant_id' => $variant->id],
    //                     [
    //                         'meta_title' => $variantData['meta_title'] ?? null,
    //                         'meta_keyword' => $variantData['meta_keyword'] ?? null,
    //                         'meta_description' => $variantData['meta_description'] ?? null,
    //                     ]
    //                 );
    //             }

    //             // Attributes
    //             if (!empty($variantData['attributes'])) {
    //                 foreach ($variantData['attributes'] as $attr) {
    //                     DB::table('product_attribute_values')->insert([
    //                         'product_id' => $variant->id,
    //                         'attribute_id' => $attr['attribute_id'],
    //                         'attribute_value_id' => $attr['attribute_value_id'],
    //                         'created_at' => now(),
    //                         'updated_at' => now(),
    //                     ]);
    //                 }
    //             }

    //             // Images
    //             if ($request->hasFile("variants.$index.images")) {

    //                 $manager = new ImageManager(new Driver());

    //                 foreach ($request->file("variants.$index.images") as $file) {

    //                     $filename = time() . '_' . uniqid();

    //                     $large = $manager->read($file)->cover(600, 600);
    //                     Storage::disk('public')->put(
    //                         "products/large/{$filename}.webp",
    //                         compressToTargetSize($large, 30)
    //                     );

    //                     $medium = $manager->read($file)->cover(150, 150);
    //                     Storage::disk('public')->put(
    //                         "products/medium/{$filename}.webp",
    //                         compressToTargetSize($medium, 25)
    //                     );

    //                     $small = $manager->read($file)->cover(40, 40);
    //                     Storage::disk('public')->put(
    //                         "products/small/{$filename}.webp",
    //                         compressToTargetSize($small, 15)
    //                     );

    //                     ProductImage::create([
    //                         'business_category_id' => $business->business_category_id,
    //                         'product_id' => $productId,
    //                         'product_variant_id' => $variant->id,

    //                         'image_large' => "products/large/{$filename}.webp",
    //                         'image_medium' => "products/medium/{$filename}.webp",
    //                         'image_small' => "products/small/{$filename}.webp",
    //                     ]);
    //                 }
    //             }

    //             $variantIds[] = $variant->id;
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Product created successfully',
    //             'data' => [
    //                 'product_id' => Hashids::encode($productId),
    //                 'variant_ids' => array_map(fn($id) => Hashids::encode($id), $variantIds),
    //                 'table' => $tableName
    //             ]
    //         ], 201);

    //     } catch (\Illuminate\Validation\ValidationException $e) {

    //         DB::rollBack();

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation error',
    //             'errors' => $e->errors()
    //         ], 422);

    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong',
    //             'error' => config('app.debug') ? $e->getMessage() : null
    //         ], 500);
    //     }
    // }


    public function store(Request $request)
    {
        //DB::beginTransaction();

        try {

            // ✅ VALIDATION RULES (UPDATED FOR ENCODED IDS)
            $rules = [
                'business_id' => 'required',
                'category_id' => 'required',
                'sub_category_id' => 'nullable',
                'sub_sub_category_id' => 'nullable',

                'name' => 'required|string',
                'description' => 'nullable|string',
                'status' => 'required|integer',

                'hsn_id' => 'nullable',

                // Variants
                'variants' => 'required|array|min:1',

                'variants.*.sku' => 'required|string|distinct|unique:product_variants,sku',
                'variants.*.barcode' => 'nullable|string|distinct|unique:product_variants,barcode',

                'variants.*.mrp' => 'required|numeric',
                'variants.*.cost_price' => 'nullable|numeric',
                'variants.*.selling_price' => 'nullable|numeric',
                'variants.*.discount' => 'nullable|numeric',

                'variants.*.stock' => 'nullable|integer',
                'variants.*.manufacture_date' => 'nullable|date',
                'variants.*.expiry_date' => 'nullable|date',

                // Images
                'variants.*.images' => 'nullable|array',
                'variants.*.images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10000',

                // ✅ ATTRIBUTES (NO EXISTS HERE)
                'variants.*.attributes' => 'nullable|array',
                'variants.*.attributes.*.attribute_id' => 'required_with:variants.*.attributes',
                'variants.*.attributes.*.attribute_value_id' => 'required_with:variants.*.attributes',

                // Meta
                'variants.*.meta_title' => 'nullable|string',
                'variants.*.meta_keyword' => 'nullable|string',
                'variants.*.meta_description' => 'nullable|string',
            ];

            $data = $request->validate($rules);

            // ✅ DECODE MAIN IDS
            $data['business_id'] = decodeIdOrFail($data['business_id'], 'Invalid Business ID');
            $data['category_id'] = decodeIdOrFail($data['category_id'], 'Invalid Category ID');

            if (!empty($data['hsn_id'])) {
                $data['hsn_id'] = decodeIdOrFail($data['hsn_id'], 'Invalid HSN ID');
            }


            if (!empty($data['sub_category_id'])) {
                $data['sub_category_id'] = decodeIdOrFail($data['sub_category_id'], 'Invalid Sub Category ID');
            }

            if (!empty($data['sub_sub_category_id'])) {
                $data['sub_sub_category_id'] = decodeIdOrFail($data['sub_sub_category_id'], 'Invalid Sub Sub Category ID');
            }

            // Business
            $business = Business::findOrFail($data['business_id']);

            // Table mapping
            $tableMap = config('product.table_map');
            $categoryId = $business->business_category_id;

            if (!isset($tableMap[$categoryId])) {
                throw new \Exception('Invalid business category mapping');
            }

            $tableName = $tableMap[$categoryId];
            //DB::enableQueryLog();
            // ✅ CREATE PRODUCT
            $productId = DB::table($tableName)->insertGetId([
                'business_id' => $business->id,
                'business_category_id' => $business->business_category_id,
                'business_sub_category_id' => $business->business_sub_category_id,
                'category_id' => $data['category_id'],
                'sub_category_id' => $data['sub_category_id'] ?? null,
                'sub_sub_category_id' => $data['sub_sub_category_id'] ?? null,
                'hsn_id' =>$data['hsn_id'] ?? null,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            //dd(DB::getQueryLog(), $productId);
            $variantIds = [];

            // ✅ VARIANTS LOOP
            foreach ($data['variants'] as $index => $variantData) {

                $variant = ProductVariant::create([
                    'product_id' => $productId,
                    'product_type' => $categoryId,

                    'sku' => $variantData['sku'],
                    'barcode' => $variantData['barcode'] ?? null,

                    'mrp' => $variantData['mrp'],
                    'cost_price' => $variantData['cost_price'] ?? null,
                    'selling_price' => $variantData['selling_price'] ?? null,
                    'discount' => $variantData['discount'] ?? 0,
                    'final_price' => ($variantData['selling_price'] ?? $variantData['mrp']) - ($variantData['discount'] ?? 0),

                    'manufacture_date' => $variantData['manufacture_date'] ?? null,
                    'expiry_date' => $variantData['expiry_date'] ?? null,
                ]);

                // Stock
                ProductVendorStock::create([
                    'product_variant_id' => $variant->id,
                    'business_id' => $business->id,
                    'stock' => $variantData['stock'] ?? 0,
                ]);

                // META
                if (
                    !empty($variantData['meta_title']) ||
                    !empty($variantData['meta_keyword']) ||
                    !empty($variantData['meta_description'])
                ) {
                    ProductVariantMeta::updateOrCreate(
                        ['product_variant_id' => $variant->id],
                        [
                            'meta_title' => $variantData['meta_title'] ?? null,
                            'meta_keyword' => $variantData['meta_keyword'] ?? null,
                            'meta_description' => $variantData['meta_description'] ?? null,
                        ]
                    );
                }

                // ✅ ATTRIBUTES WITH DECODE
                if (!empty($variantData['attributes']) && is_array($variantData['attributes'])) {

                    $insertData = [];

                    foreach ($variantData['attributes'] as $attr) {

                        if (!empty($attr['attribute_id']) && !empty($attr['attribute_value_id'])) {

                            $attributeId = decodeIdOrFail($attr['attribute_id'], 'Invalid Attribute ID');
                            $attributeValueId = decodeIdOrFail($attr['attribute_value_id'], 'Invalid Attribute Value ID');

                            // Optional validation
                            if (!DB::table('attributes')->where('id', $attributeId)->exists()) {
                                throw new \Exception('Attribute not found');
                            }

                            if (!DB::table('attribute_values')->where('id', $attributeValueId)->exists()) {
                                throw new \Exception('Attribute value not found');
                            }

                            $insertData[] = [
                                'product_variant_id' => $variant->id, // ✅ FIXED
                                'attribute_id' => $attributeId,
                                'attribute_value_id' => $attributeValueId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    // ✅ Bulk insert (faster)
                    if (!empty($insertData)) {
                        DB::table('product_attribute_relations')->insert($insertData);
                    }
                }

                // ✅ IMAGES
                if ($request->hasFile("variants.$index.images")) {

                    $manager = new ImageManager(new Driver());

                    foreach ($request->file("variants.$index.images") as $file) {

                        $filename = time() . '_' . uniqid();

                        $large = $manager->read($file)->cover(600, 600);
                        Storage::disk('public')->put(
                            "products/large/{$filename}.webp",
                            compressToTargetSize($large, 30)
                        );

                        $medium = $manager->read($file)->cover(150, 150);
                        Storage::disk('public')->put(
                            "products/medium/{$filename}.webp",
                            compressToTargetSize($medium, 25)
                        );

                        $small = $manager->read($file)->cover(40, 40);
                        Storage::disk('public')->put(
                            "products/small/{$filename}.webp",
                            compressToTargetSize($small, 15)
                        );

                        ProductImage::create([
                            'business_category_id' => $business->business_category_id,
                            'product_id' => $productId,
                            'product_variant_id' => $variant->id,
                            'image_large' => "products/large/{$filename}.webp",
                            'image_medium' => "products/medium/{$filename}.webp",
                            'image_small' => "products/small/{$filename}.webp",
                        ]);
                    }
                }

                $variantIds[] = $variant->id;
            }
            $variants = ProductVariant::with([
                'attributes.attribute',
                'attributes.attributeValue',
                'images',
                'meta',
                'stocks'
            ])->whereIn('id', $variantIds)->get();

             $product = DB::table($tableName)->where('id', $productId)->first();

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'data' => [
                    'product_id' => Hashids::encode($productId),
                    'name' => $product->name,
                    'description' => $product->description,
                    'status' => $product->status,
                    'status_label' => config('product.status')[$product->status] ?? 'Unknown',
                    'variants' => VariantResource::collection($variants),
                    'table' => $tableName
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {

            //DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            // DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {

            $decodedUser = Hashids::decode($id);

            if (empty($decodedUser)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid User ID'
                ], 400);
            }

            $id = $decodedUser[0];


            $product = Product::with([
                'category',
                'subCategory',
                'subSubCategory',
                'hsn',
                'images',
                'attributes.attribute',
                'attributes.value'
            ])->findOrFail($id);

            return response()->json([
                'status' => true,
                'data' => new ProductResource($product)
            ], 200);
        } catch (ModelNotFoundException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        try {

            // Decode product ID
            $id = decodeIdOrFail($id);

            $product = Product::findOrFail($id);

            // Decode user_id (if coming encoded)
            $userId = decodeIdOrFail($request->user_id ?? null, 'Invalid User ID');

            // GST check
            $hasGst = Business::where('user_id', $userId)
                ->whereNotNull('gst_number')
                ->where('gst_number', '!=', '')
                ->exists();

            // Validation
            $rules = [
                'category_id' => 'sometimes',
                'sub_category_id' => 'nullable',
                'sub_sub_category_id' => 'nullable',

                'name' => 'sometimes|string',
                'description' => 'nullable|string',

                'mrp' => 'sometimes|numeric',
                'selling_price' => 'nullable|numeric',
                'discount' => 'nullable|numeric',

                'status' => 'sometimes|boolean',

                'attributes' => 'nullable|array',
                'attributes.*.attribute_id' => 'required',
                'attributes.*.attribute_value_id' => 'required',

                'images' => 'nullable|array',
                'images.*' => 'file|image|mimes:jpg,jpeg,png,webp|max:10000',
            ];

            if ($hasGst) {
                $rules['hsn_id'] = 'required';
                $rules['gst_percent'] = 'required';
            }

            $data = $request->validate($rules);

            // Decode IDs
            if (isset($data['category_id'])) {
                $data['category_id'] = decodeIdOrFail($data['category_id'], 'Invalid Category ID');
            }

            if (!empty($data['sub_category_id'])) {
                $data['sub_category_id'] = decodeIdOrFail($data['sub_category_id']);
            }

            if (!empty($data['sub_sub_category_id'])) {
                $data['sub_sub_category_id'] = decodeIdOrFail($data['sub_sub_category_id']);
            }

            if (!empty($data['hsn_id'])) {
                $data['hsn_id'] = decodeIdOrFail($data['hsn_id']);
            }

            // UPDATE PRODUCT
            $product->update($data);

            // UPDATE ATTRIBUTES
            if ($request->has('attributes')) {

                $product->attributes()->delete();

                foreach ($request->attributes as $attr) {

                    $attributeId = decodeIdOrFail($attr['attribute_id']);
                    $valueId = decodeIdOrFail($attr['attribute_value_id']);

                    ProductAttributeValue::create([
                        'product_id' => $product->id,
                        'attribute_id' => $attributeId,
                        'attribute_value_id' => $valueId,
                    ]);
                }
            }

            // IMAGES
            if ($request->hasFile('images')) {

                $manager = new ImageManager(new Driver());

                foreach ($request->file('images') as $file) {

                    if (!$file || !$file->isValid()) continue;

                    $filename = time() . '_' . uniqid();

                    Storage::disk('public')->put(
                        "products/large/{$filename}.webp",
                        compressToTargetSize($manager->read($file)->cover(600, 600), 30)
                    );

                    Storage::disk('public')->put(
                        "products/medium/{$filename}.webp",
                        compressToTargetSize($manager->read($file)->cover(150, 150), 25)
                    );

                    Storage::disk('public')->put(
                        "products/small/{$filename}.webp",
                        compressToTargetSize($manager->read($file)->cover(40, 40), 15)
                    );

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_large' => "products/large/{$filename}.webp",
                        'image_medium' => "products/medium/{$filename}.webp",
                        'image_small' => "products/small/{$filename}.webp",
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
                'data' => new ProductResource(
                    $product->load('images', 'attributes.attribute', 'attributes.value')
                )
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $product->delete();

            return response()->json([
                'status' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteProductImage($id)
    {
        try {
            // Find image
            $image = ProductImage::find($id);

            if (!$image) {
                return response()->json([
                    'status' => false,
                    'message' => 'Image not found'
                ], 404);
            }

            // Delete files from storage
            if ($image->image_large && File::exists(public_path($image->image_large))) {
                File::delete(public_path($image->image_large));
            }

            if ($image->image_medium && File::exists(public_path($image->image_medium))) {
                File::delete(public_path($image->image_medium));
            }

            if ($image->image_small && File::exists(public_path($image->image_small))) {
                File::delete(public_path($image->image_small));
            }

            // Delete DB record
            $image->delete();

            return response()->json([
                'status' => true,
                'message' => 'Image deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
