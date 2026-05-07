<?php

namespace App\Http\Controllers\Api\v1\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberProductResource;
use App\Models\EducationStationary;
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

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        try {

            $modelMap = config('product.model_map');
            $allProducts = collect();

            // Pagination
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            // Filters
            $businessCategoryId = $request->filled('business_category_id')
                ? decodeIdOrFail($request->business_category_id, 'Invalid business category ID')
                : null;

            $businessSubCategoryId = $request->filled('business_sub_category_id')
                ? decodeIdOrFail($request->business_sub_category_id, 'Invalid business sub category ID')
                : null;

            $categoryId = $request->filled('category_id')
                ? decodeIdOrFail($request->category_id, 'Invalid category ID')
                : null;

            foreach ($modelMap as $type => $modelClass) {

                $query = $modelClass::query()
                    ->select([
                        'id',
                        'name',
                        'business_id',
                        'business_category_id',
                        'business_sub_category_id',
                        'category_id',
                        'sub_category_id',
                        'sub_sub_category_id',
                        'hsn_id',
                        'status',
                        'created_at'
                    ])
                ->where('status', 1); // Only active products
                // Filters
                if ($businessCategoryId) {
                    $query->where('business_category_id', $businessCategoryId);
                }

                if ($businessSubCategoryId) {
                    $query->where('business_sub_category_id', $businessSubCategoryId);
                }

                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }

                // Eager loading
                $products = $query->with([
                    'business:id,business_name',
                    'category:id,name',
                    'subCategory:id,name',
                    'subSubCategory:id,name',
                    'hsn:id,hsn_code',

                    'primaryVariant' => function ($q) {
                        $q->select([
                            'id',
                            'sku',
                            'barcode',
                            'discount',
                            'final_price',
                            'product_id',
                            'product_type',
                            'selling_price',
                            'cost_price',
                            'mrp',
                            'is_primary'
                        ])
                        ->where('is_primary', 1)
                        ->with([
                            'meta:id,product_variant_id,meta_title,meta_keyword,meta_description',

                            'attributes' => function ($attr) {
                                $attr->select([
                                    'id',
                                    'product_variant_id',
                                    'attribute_id',
                                    'attribute_value_id'
                                ])->with([
                                    'attribute:id,name',
                                    'attributeValue:id,value'
                                ]);
                            },

                            'images' => function ($img) {
                                $img->select([
                                    'id',
                                    'product_variant_id',
                                    'image_medium'
                                ])->limit(1);
                            }
                        ]);
                    }
                ])
                ->latest()
                ->get()
                ->map(function ($item) use ($type) {
                    $item->product_type = $type;
                    return $item;
                });

                $allProducts = $allProducts->concat($products);
            }

            // Sorting
            $allProducts = $allProducts->sortByDesc('created_at')->values();

            // Pagination
            $total = $allProducts->count();

            $paginated = $allProducts
                ->slice(($page - 1) * $perPage, $perPage)
                ->values();

            return response()->json([
                'status'  => true,
                'message' => 'Product list fetched successfully',
                'data'    => MemberProductResource::collection($paginated),
                'meta'    => [
                    'current_page' => (int)$page,
                    'per_page'     => (int)$perPage,
                    'total'        => $total,
                    'last_page'    => (int) ceil($total / $perPage),
                ]
            ], 200);

        } catch (\Exception $e) {

            \Log::error('Member Product Index Error: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * productDetails
     */
    public function productDetails(Request $request)
    {
        try {

            // Validation
            $request->validate([
                'product_id'  => 'required',
                'business_id' => 'required',
            ]);

            // Decode IDs
            $productId = decodeIdOrFail(
                $request->product_id,
                'Invalid product ID'
            );

            $businessId = decodeIdOrFail(
                $request->business_id,
                'Invalid business ID'
            );

            $modelMap = config('product.model_map');

            $product = null;

            foreach ($modelMap as $type => $modelClass) {

                $product = $modelClass::query()

                    ->where('id', $productId)
                    ->where('business_id', $businessId)
                    ->where('status', 1)

                    ->with([

                        // Business
                        'business:id,business_name',

                        // Categories
                        'category:id,name',
                        'subCategory:id,name',
                        'subSubCategory:id,name',

                        // HSN
                        'hsn:id,hsn_code',

                        // ALL Variants for details page
                        'variants' => function ($q) {

                            $q->select([
                                'id',
                                'product_id',
                                'product_type',
                                'sku',
                                'barcode',
                                'mrp',
                                'cost_price',
                                'selling_price',
                                'discount',
                                'final_price',
                                'short_description',
                                'long_description',
                                'manufacture_date',
                                'expiry_date',
                                'is_primary',
                            ])

                            ->with([

                                // SEO Meta
                                'meta:id,product_variant_id,meta_title,meta_keyword,meta_description',

                                // Attributes
                                'attributes' => function ($attr) {

                                    $attr->select([
                                        'id',
                                        'product_variant_id',
                                        'attribute_id',
                                        'attribute_value_id'
                                    ])

                                    ->with([
                                        'attribute:id,name',
                                        'attributeValue:id,value'
                                    ]);
                                },

                                // Images
                                'images:id,product_variant_id,image_large,image_medium,image_small'
                            ]);
                        }
                    ])

                    ->first();

                if ($product) {

                    $product->product_type = $type;

                    break;
                }
            }

            // Product not found
            if (!$product) {

                return response()->json([
                    'status'  => false,
                    'message' => 'Product not found',
                    'data'    => null,
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Product details fetched successfully',
                'data'    => new MemberProductResource($product),
            ], 200);

        } catch (\Exception $e) {

            \Log::error(
                'Member Product Details Error: ' . $e->getMessage()
            );

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
