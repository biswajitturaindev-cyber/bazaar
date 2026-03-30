<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductImage;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;


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
    public function store(Request $request)
    {

        DB::beginTransaction();

        try {
            // VALIDATION
            $data = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'sub_category_id' => 'nullable|exists:sub_categories,id',
                'sub_sub_category_id' => 'nullable|exists:sub_category_items,id',

                'name' => 'required|string',
                'description' => 'nullable|string',

                'hsn_id' => 'nullable|exists:hsns,id',
                'gst_percent' => 'required|numeric',

                'mrp' => 'required|numeric',
                'selling_price' => 'nullable|numeric',
                'discount' => 'nullable|numeric',

                'status' => 'required|boolean',

                // attributes
                'attributes' => 'nullable|array',
                'attributes.*.attribute_id' => 'required|exists:attributes,id',
                'attributes.*.attribute_value_id' => 'required|exists:attribute_values,id',

                // images
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10000',
            ]);

            // CREATE PRODUCT
            $product = Product::create($data);

            // SAVE ATTRIBUTES
            if (!empty($request['attributes'])) {
                foreach ($request['attributes'] as $attr) {
                    ProductAttributeValue::create([
                        'product_id' => $product->id,
                        'attribute_id' => $attr['attribute_id'],
                        'attribute_value_id' => $attr['attribute_value_id'],
                    ]);
                }
            }

            // MULTIPLE IMAGE UPLOAD
            if ($request->hasFile('images')) {

                $manager = new ImageManager(new Driver());

                foreach ($request->file('images') as $file) {

                    $filename = time() . '_' . uniqid();

                    // LARGE (600x600)
                    $large = $manager->read($file)->cover(600, 600);
                    $largeWebp = compressToTargetSize($large, 30);

                    Storage::disk('public')->put(
                        "products/large/{$filename}.webp",
                        $largeWebp
                    );

                    // MEDIUM (300x300)
                    $medium = $manager->read($file)->cover(150, 150);
                    $mediumWebp = compressToTargetSize($medium, 25);

                    Storage::disk('public')->put(
                        "products/medium/{$filename}.webp",
                        $mediumWebp
                    );

                    // SMALL 40x40
                    $small = $manager->read($file)->cover(40, 40);
                    $smallWebp = compressToTargetSize($small, 15);

                    Storage::disk('public')->put(
                        "products/small/{$filename}.webp",
                        $smallWebp
                    );

                    // SAVE DB
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
                'message' => 'Product created successfully',
                'data' => new ProductResource($product->load('images','attributes.attribute','attributes.value'))
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {

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
    public function update(Request $request, Product $product)
    {
        DB::beginTransaction();

        try {
            // VALIDATION
            $data = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'sub_category_id' => 'nullable|exists:sub_categories,id',
                'sub_sub_category_id' => 'nullable|exists:sub_category_items,id',

                'name' => 'required|string',
                'description' => 'nullable|string',

                'hsn_id' => 'nullable|exists:hsns,id',
                'gst_percent' => 'required|numeric',

                'mrp' => 'required|numeric',
                'selling_price' => 'nullable|numeric',
                'discount' => 'nullable|numeric',

                'status' => 'required|boolean',

                // attributes
                'attributes' => 'nullable|array',
                'attributes.*.attribute_id' => 'required|exists:attributes,id',
                'attributes.*.attribute_value_id' => 'required|exists:attribute_values,id',

                // images (new uploads)
                'images' => 'nullable|array',
                'images.*' => 'file|image|mimes:jpg,jpeg,png,webp|max:10000',
            ]);

            // ===========================
            // UPDATE PRODUCT
            // ===========================
            $product->update($data);

            // ===========================
            // UPDATE ATTRIBUTES
            // (Delete old → Insert new)
            // ===========================
            if ($request->has('attributes')) {

                // delete old
                $product->attributes()->delete();

                foreach ($request->attributes as $attr) {

                    if (
                        empty($attr['attribute_id']) ||
                        empty($attr['attribute_value_id'])
                    ) continue;

                    ProductAttributeValue::create([
                        'product_id' => $product->id,
                        'attribute_id' => $attr['attribute_id'],
                        'attribute_value_id' => $attr['attribute_value_id'],
                    ]);
                }
            }

            // ===========================
            // ADD NEW IMAGES (optional)
            // ===========================
            if ($request->hasFile('images')) {

                $manager = new ImageManager(new Driver());

                foreach ($request->file('images') as $file) {

                    if (!$file || !$file->isValid()) continue;

                    $filename = time() . '_' . uniqid();

                    // LARGE
                    $large = $manager->read($file)->cover(600, 600);
                    $largeWebp = compressToTargetSize($large, 30);

                    Storage::disk('public')->put(
                        "products/large/{$filename}.webp",
                        $largeWebp
                    );

                    // MEDIUM
                    $medium = $manager->read($file)->cover(150, 150);
                    $mediumWebp = compressToTargetSize($medium, 25);

                    Storage::disk('public')->put(
                        "products/medium/{$filename}.webp",
                        $mediumWebp
                    );

                    // SMALL
                    $small = $manager->read($file)->cover(40, 40);
                    $smallWebp = compressToTargetSize($small, 15);

                    Storage::disk('public')->put(
                        "products/small/{$filename}.webp",
                        $smallWebp
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
                    $product->load([
                        'images',
                        'attributes.attribute',
                        'attributes.value'
                    ])
                )
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();

            return response()->json([
                'status' => true,
                'message' => 'Deleted'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
