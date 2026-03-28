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
                'attributeValues.attribute',
                'attributeValues.value'
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

            // 1. VALIDATION
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

                // attributes array
                'attributes' => 'nullable|array',
                'attributes.*.attribute_id' => 'required|exists:attributes,id',
                'attributes.*.attribute_value_id' => 'required|exists:attribute_values,id',

                // images
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10000000',
            ]);

            // 2. CREATE PRODUCT
            $product = Product::create($data);

            // 3. SAVE ATTRIBUTES
            if ($request->has('attributes')) {
                foreach ($request->attributes as $attr) {
                    ProductAttributeValue::create([
                        'product_id' => $product->id,
                        'attribute_id' => $attr['attribute_id'],
                        'attribute_value_id' => $attr['attribute_value_id'],
                    ]);
                }
            }

            // 4. SAVE IMAGES (RESIZE + OPTIMIZE)
            if ($request->hasFile('images')) {

                $manager = new ImageManager(new Driver());

                foreach ($request->file('images') as $file) {

                    // unique image name
                    $imageName = time() . '_' . uniqid() . '.' . $file->extension();

                    // resize (crop to square 300x300)
                    $image = $manager->read($file->getRealPath())
                        ->cover(300, 300);

                    // save to storage
                    Storage::disk('public')->put(
                        'products/' . $imageName,
                        (string) $image->encodeByExtension($file->extension())
                    );

                    // save in DB
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => 'products/' . $imageName,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'data' => new ProductResource($product->load('attributes', 'images'))
            ], 201);

        } catch (ValidationException $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);

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
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        try {
            $product->load([
                'category',
                'subCategory',
                'subSubCategory',
                'hsn',
                'images',
                'attributeValues.attribute',
                'attributeValues.value'
            ]);

            return response()->json([
                'status' => true,
                'data' => new ProductResource($product)
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
            $product->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Updated',
                'data' => new ProductResource($product)
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
