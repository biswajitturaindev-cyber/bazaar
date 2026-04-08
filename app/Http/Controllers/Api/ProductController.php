<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductImage;
use App\Http\Resources\ProductResource;
use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\Category;
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

    //         // Decode user_id first
    //         $decodedUser = Hashids::decode($request->user_id);

    //         if (empty($decodedUser)) {
    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'Invalid User ID'
    //             ], 400);
    //         }

    //         $userId = $decodedUser[0];

    //         // STEP 1: Check GST existence
    //         $hasGst = Business::where('user_id', $userId)
    //             ->whereNotNull('gst_number')
    //             ->where('gst_number', '!=', '')
    //             ->exists();

    //         // STEP 2: Validation rules
    //         $rules = [
    //             'user_id' => 'required',

    //             'category_id' => 'required',
    //             'sub_category_id' => 'nullable',
    //             'sub_sub_category_id' => 'nullable',

    //             'name' => 'required|string',
    //             'description' => 'nullable|string',

    //             'mrp' => 'required|numeric',
    //             'selling_price' => 'nullable|numeric',
    //             'discount' => 'nullable|numeric',

    //             'status' => 'required|boolean',

    //             // attributes
    //             'attributes' => 'nullable|array',
    //             'attributes.*.attribute_id' => 'required',
    //             'attributes.*.attribute_value_id' => 'required',

    //             // images
    //             'images' => 'nullable|array',
    //             'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10000',
    //         ];

    //         // Conditional GST validation
    //         if ($hasGst) {
    //             $rules['hsn_id'] = 'required';
    //             $rules['gst_percent'] = 'required';
    //         }

    //         $data = $request->validate($rules);

    //         // Decode all IDs
    //         $data['user_id'] = $userId;
    //         $data['category_id'] = decodeIdOrFail($data['category_id'], 'Invalid Category ID');

    //         if (!empty($data['sub_category_id'])) {
    //             $data['sub_category_id'] = decodeIdOrFail($data['sub_category_id'], 'Invalid Sub Category ID');
    //         }


    //         if (!empty($data['sub_sub_category_id'])) {
    //             $data['sub_sub_category_id'] = decodeIdOrFail($data['sub_sub_category_id'], 'Invalid Sub Sub Category ID');
    //         }

    //         if (!empty($data['hsn_id'])) {
    //             $data['hsn_id'] = decodeIdOrFail($data['hsn_id'], 'Invalid HSN ID');
    //         }

    //         // CREATE PRODUCT
    //         $product = Product::create($data);

    //         // SAVE ATTRIBUTES
    //         if (!empty($request['attributes'])) {
    //             foreach ($request['attributes'] as $attr) {

    //                 $attributeId = decodeIdOrFail($attr['attribute_id'], 'Invalid Attribute ID');
    //                 $valueId = decodeIdOrFail($attr['attribute_value_id'], 'Invalid Attribute Value ID');

    //                 ProductAttributeValue::create([
    //                     'product_id' => $product->id,
    //                     'attribute_id' => $attributeId,
    //                     'attribute_value_id' => $valueId,
    //                 ]);
    //             }
    //         }

    //         // IMAGE UPLOAD
    //         if ($request->hasFile('images')) {

    //             $manager = new ImageManager(new Driver());

    //             foreach ($request->file('images') as $file) {

    //                 $filename = time() . '_' . uniqid();

    //                 // LARGE
    //                 $large = $manager->read($file)->cover(600, 600);
    //                 Storage::disk('public')->put(
    //                     "products/large/{$filename}.webp",
    //                     compressToTargetSize($large, 30)
    //                 );

    //                 // MEDIUM
    //                 $medium = $manager->read($file)->cover(150, 150);
    //                 Storage::disk('public')->put(
    //                     "products/medium/{$filename}.webp",
    //                     compressToTargetSize($medium, 25)
    //                 );

    //                 // SMALL
    //                 $small = $manager->read($file)->cover(40, 40);
    //                 Storage::disk('public')->put(
    //                     "products/small/{$filename}.webp",
    //                     compressToTargetSize($small, 15)
    //                 );

    //                 ProductImage::create([
    //                     'product_id' => $product->id,
    //                     'image_large' => "products/large/{$filename}.webp",
    //                     'image_medium' => "products/medium/{$filename}.webp",
    //                     'image_small' => "products/small/{$filename}.webp",
    //                 ]);
    //             }
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Product created successfully',
    //             'data'    => new ProductResource(
    //                 $product->load('images','attributes.attribute','attributes.value')
    //             )
    //         ], 201);

    //     } catch (\Illuminate\Validation\ValidationException $e) {

    //         DB::rollBack();

    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Validation error',
    //             'errors'  => $e->errors()
    //         ], 422);

    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Something went wrong',
    //             'error'   => config('app.debug') ? $e->getMessage() : null
    //         ], 500);
    //     }
    // }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validation
            $rules = [
                'business_id' => 'required',
                'category_id' => 'required',
                'sub_category_id' => 'nullable',

                'sku' => 'required|string',
                'name' => 'required|string',
                'description' => 'nullable|string',

                'mrp' => 'required|numeric',
                'cost_price' => 'nullable|numeric',
                'selling_price' => 'nullable|numeric',
                'discount' => 'nullable|numeric',

                'stock' => 'nullable|integer',
                'manufacture_date' => 'nullable|date',
                'expiry_date' => 'nullable|date',

                'status' => 'required|integer',

                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10000',
            ];

            $data = $request->validate($rules);

            // Decode IDs
            $data['business_id'] = decodeIdOrFail($data['business_id'], 'Invalid Business ID');
            $data['category_id'] = decodeIdOrFail($data['category_id'], 'Invalid Category ID');

            if (!empty($data['sub_category_id'])) {
                $data['sub_category_id'] = decodeIdOrFail($data['sub_category_id'], 'Invalid Sub Category ID');
            }

            // Get Business (Secure)
            $business = Business::findOrFail($data['business_id']);

            $tableMap = [
                1 => 'product_food_beverages',
                2 => 'product_construction_hardware',
                3 => 'product_home_livings',
                4 => 'product_fashion_lifestyles',
                5 => 'product_automobiles',
                //6 => 'product_education_stationery',
                7 => 'product_agricultures',
                8 => 'product_retail_general',
                9 => 'product_healths',
                //10 => 'product_sports_others',
            ];

            $categoryId = $business->business_category_id;

            if (!isset($tableMap[$categoryId])) {
                throw new \Exception('Invalid business category mapping');
            }

            $tableName = $tableMap[$categoryId];

            // Insert Product
            $productId = DB::table($tableName)->insertGetId([
                'business_id' => $business->id,
                'business_sub_category_id' => $business->business_sub_category_id,
                'category_id' => $data['category_id'],
                'sub_category_id' => $data['sub_category_id'] ?? null,
                'sku' => $data['sku'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'mrp' => $data['mrp'],
                'cost_price' => $data['cost_price'] ?? null,
                'selling_price' => $data['selling_price'] ?? null,
                'discount' => $data['discount'] ?? 0,
                'stock' => $data['stock'] ?? 0,
                'manufacture_date' => $data['manufacture_date'] ?? null,
                'expiry_date' => $data['expiry_date'] ?? null,
                'status' => $data['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Image Upload
            if ($request->hasFile('images')) {

                $manager = new ImageManager(new Driver());

                foreach ($request->file('images') as $file) {

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

                        'image_large' => "products/large/{$filename}.webp",
                        'image_medium' => "products/medium/{$filename}.webp",
                        'image_small' => "products/small/{$filename}.webp",
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'data' => [
                    'product_id' => Hashids::encode($productId),
                    'table' => $tableName
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            DB::rollBack();

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
                    $product->load('images','attributes.attribute','attributes.value')
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
