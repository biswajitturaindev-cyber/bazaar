<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use App\Http\Resources\ProductReviewResource;
use App\Models\EducationStationary;
use App\Models\ProductAgriculture;
use App\Models\ProductAutomobile;
use App\Models\ProductConstructionHardware;
use App\Models\ProductFashionLifestyle;
use App\Models\ProductFoodBeverages;
use App\Models\ProductHealth;
use App\Models\ProductHomeLiving;
use App\Models\ProductRetail;
use App\Models\ProductReviewAttribute;
use App\Models\ProductSports;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
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

            // Step 1: Reviews (KEEP FILTER if needed)
            $reviewQuery = ProductReview::with('productAttributes')
                ->where('status', 2)
                ->latest();

            if ($request->filled('business_id')) {
                try {
                    $decodedBusinessId = decodeIdOrFail($request->business_id);
                    $reviewQuery->where('business_id', $decodedBusinessId);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid business_id'
                    ], 400);
                }
            }

            $reviews = $reviewQuery->get();

            // Step 2: Get Products (IMPORTANT FIX - independent)
            $productsCollection = collect();

            foreach ($tableMap as $categoryId => $modelClass) {

                $query = $modelClass::with('attributes');

                // filter by business if given
                if (!empty($decodedBusinessId)) {
                    $query->where('business_id', $decodedBusinessId);
                }

                $items = $query->get()->map(function ($item) {

                    if (!$item->relationLoaded('attributes') || $item->attributes === null) {
                        $item->setRelation('attributes', collect());
                    }

                    $item->type = 'approve';
                    return $item;
                });

                $productsCollection = $productsCollection->concat($items);
            }

            // Step 3: Reviews type
            $reviews = $reviews->map(function ($review) {
                $review->type = 'unapprove';
                return $review;
            });

            // Step 4: Merge
            $finalData = $reviews->concat($productsCollection)->values();

            // Step 5: Pagination
            $perPage = 10;
            $currentPage = request()->get('page', 1);

            $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
                $finalData->forPage($currentPage, $perPage),
                $finalData->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url()]
            );

            return response()->json([
                'success' => true,
                'message' => 'Product + Review list fetched successfully',
                'data' => ProductReviewResource::collection($paginatedData->values()),
                'meta' => [
                    'current_page' => $paginatedData->currentPage(),
                    'last_page' => $paginatedData->lastPage(),
                    'per_page' => $paginatedData->perPage(),
                    'total' => $paginatedData->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required',
                'name' => 'required|string|max:255',
                'mrp' => 'nullable|numeric',
                'selling_price' => 'nullable|numeric',
                'cost_price' => 'nullable|numeric',
                'final_price' => 'nullable|numeric',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

                // attributes
                'attribute_id' => 'nullable|array',
                'attribute_id.*' => 'nullable',
                'attribute_value_id' => 'nullable|array',
                'stock' => 'nullable|array',
                'price' => 'nullable|array',
            ]);

            // Decode Hashids BEFORE saving
            $validated['business_id'] = $request->business_id
                ? decodeIdOrFail($request->business_id)
                : null;

            $validated['business_category_id'] = $request->business_category_id
                ? decodeIdOrFail($request->business_category_id)
                : null;

            $validated['business_sub_category_id'] = $request->business_sub_category_id
                ? decodeIdOrFail($request->business_sub_category_id)
                : null;

            $validated['category_id'] = decodeIdOrFail($request->category_id);

            $validated['sub_category_id'] = $request->sub_category_id
                ? decodeIdOrFail($request->sub_category_id)
                : null;

            $validated['sub_sub_category_id'] = $request->sub_sub_category_id
                ? decodeIdOrFail($request->sub_sub_category_id)
                : null;

            $validated['hsn_id'] = $request->hsn_id
                ? decodeIdOrFail($request->hsn_id)
                : null;

            // Image Upload
            if ($request->hasFile('image')) {

                $manager = new ImageManager(new Driver());

                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.webp';

                // Read image
                $image = $manager->read($file);

                // Resize (600x600)
                $resized = clone $image;
                $resized->cover(600, 600);

                // Save
                Storage::disk('public')->put(
                    "review_products/{$filename}",
                    (string) $resized->toWebp(80)
                );

                $validated['image'] = "review_products/{$filename}";
            }

            // Other fields
            $validated += $request->only([
                'sku',
                'description',
                'discount',
                'manufacture_date',
                'expiry_date',
                'status'
            ]);

            $product = ProductReview::create($validated);

            // =========================
            // MULTIPLE ATTRIBUTES SAVE
            // =========================
            if ($request->has('attribute_id')) {

                $attributes = [];

                foreach ($request->attribute_id as $index => $attrId) {

                    if (!$attrId) continue;

                    $attributes[] = [
                        'product_review_id' => $product->id,
                        'attribute_id' => decodeIdOrFail($attrId),
                        'attribute_value_id' => isset($request->attribute_value_id[$index])
                            ? decodeIdOrFail($request->attribute_value_id[$index])
                            : null,
                        'stock' => $request->stock[$index] ?? null,
                        'price' => $request->price[$index] ?? null,
                        'created_at' => now(),
                        //'updated_at' => now(),
                    ];
                }

                ProductReviewAttribute::insert($attributes);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => new ProductReviewResource(
                    $product->load('productAttributes') // auto include attributes
                )
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Decode Hashid
            $decodedId = decodeIdOrFail($id);

            // Load with nested relations
            $product = ProductReview::with([
                'productAttributes.attribute',
                'productAttributes.attributeValue',
                'businessCategory',
                'businessSubCategory',
                'category',
                'subCategory',
                'subSubCategory',
                'hsn'
            ])->findOrFail($decodedId);

            return response()->json([
                'success' => true,
                'data' => new ProductReviewResource($product)
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Decode ID
            $decodedId = decodeIdOrFail($id);

            $product = ProductReview::findOrFail($decodedId);

            // Validation
            $validated = $request->validate([
                'category_id' => 'required',
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

                'attribute_id' => 'nullable|array',
                'attribute_value_id' => 'nullable|array',
                'stock' => 'nullable|array',
                'price' => 'nullable|array',
            ]);

            // Decode Hashids (loop)
            foreach ([
                'business_id',
                'business_category_id',
                'business_sub_category_id',
                'category_id',
                'sub_category_id',
                'sub_sub_category_id',
                'hsn_id'
            ] as $field) {
                if ($request->$field) {
                    $validated[$field] = decodeIdOrFail($request->$field);
                }
            }

            // =========================
            // Image Upload (Resize + Replace)
            // =========================
            if ($request->hasFile('image')) {

                // delete old
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                $manager = new ImageManager(new Driver());
                $file = $request->file('image');

                $filename = time() . '_' . uniqid() . '.webp';

                $image = $manager->read($file);

                $resized = clone $image;
                $resized->cover(600, 600);

                Storage::disk('public')->put(
                    "review_products/{$filename}",
                    (string) $resized->toWebp(80)
                );

                $validated['image'] = "review_products/{$filename}";
            }

            // Other fields
            $validated += $request->only([
                'sku',
                'description',
                'mrp',
                'cost_price',
                'selling_price',
                'discount',
                'final_price',
                'manufacture_date',
                'expiry_date',
                'status'
            ]);

            // Update product
            $product->update($validated);

            // =========================
            // UPDATE ATTRIBUTES (delete + insert)
            // =========================
            if ($request->has('attribute_id')) {

                // delete old
                $product->productAttributes()->delete();

                $attributes = [];

                foreach ($request->attribute_id as $index => $attrId) {

                    if (!$attrId) continue;

                    $attributes[] = [
                        'product_review_id' => $product->id,
                        'attribute_id' => decodeIdOrFail($attrId),
                        'attribute_value_id' => isset($request->attribute_value_id[$index])
                            ? decodeIdOrFail($request->attribute_value_id[$index])
                            : null,
                        'stock' => $request->stock[$index] ?? null,
                        'price' => $request->price[$index] ?? null,
                        'updated_at' => now(),
                    ];
                }

                ProductReviewAttribute::insert($attributes);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => new ProductReviewResource(
                    $product->load('productAttributes')
                )
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);

        } catch (\Illuminate\Database\QueryException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid reference data'
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Decode Hashid
            $decodedId = decodeIdOrFail($id);

            $product = ProductReview::findOrFail($decodedId);

            // =========================
            // Delete Attributes
            // =========================
            $product->productAttributes()->delete();

            // =========================
            // Delete Image
            // =========================
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            // =========================
            // Delete Product
            // =========================
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
