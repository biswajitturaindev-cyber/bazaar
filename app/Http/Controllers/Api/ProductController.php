<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductImage;
use App\Http\Resources\ProductResource;
use App\Http\Resources\VariantResource;
use App\Models\AttributeValue;
use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\Category;
use App\Models\ProductFashionLifestyle;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $modelMap = config('product.model_map');
            $allProducts = collect();

            $perPage = request()->get('per_page', 10);
            $page = request()->get('page', 1);

            $businessId = decodeIdOrFail(request()->business_id);

            foreach ($modelMap as $type => $modelClass) {

                $products = $modelClass::query()
                    ->select([
                        'id',
                        'name',
                        'category_id',
                        'sub_category_id',
                        'sub_sub_category_id',
                        'hsn_id',
                        'batch_no',
                        'status',
                        'created_at'
                    ])
                    ->with([
                        'category:id,name',
                        'subCategory:id,name',
                        'subSubCategory:id,name',
                        'hsn:id,hsn_code,igst',

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
                                'mrp',
                                'cost_price',
                                'is_primary',
                                'manufacture_date',
                                'expiry_date',
                                'short_description',
                                'long_description'
                            ])
                            ->with([
                                'meta:id,product_variant_id,meta_title,meta_keyword,meta_description',

                                'attributes' => function ($attr) {
                                    $attr->select([
                                        'id',
                                        'product_variant_id',
                                        'attribute_master_id',
                                        'attribute_value_id'
                                    ])
                                    ->with([
                                        'attributeMaster:id,name',
                                        'attributeValue:id,value,color_code',
                                    ]);
                                },

                                'images' => function ($img) {
                                    $img->select([
                                        'id',
                                        'product_variant_id',
                                        'image_large'
                                    ])->limit(1);
                                }
                            ]);
                        }
                    ])
                    ->when($businessId, function ($q) use ($businessId) {
                        $q->where('business_id', $businessId);
                    })
                    ->latest()
                    ->get()
                    ->map(function ($item) use ($type) {
                        $item->product_type = $type;
                        return $item;
                    });

                $allProducts = $allProducts->concat($products);
            }

            $allProducts = $allProducts->sortByDesc('created_at')->values();

            $total = $allProducts->count();

            $paginated = $allProducts
                ->slice(($page - 1) * $perPage, $perPage)
                ->values();

            return response()->json([
                'status' => true,
                'message' => 'Product list fetched successfully',
                'data' => ProductResource::collection($paginated),
                'meta' => [
                    'current_page' => (int) $page,
                    'per_page' => (int) $perPage,
                    'total' => $total,
                    'last_page' => (int) ceil($total / $perPage),
                ]
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // VALIDATION RULES (UPDATED FOR ENCODED IDS)
            $rules = [
                'business_id' => 'required',
                'category_id' => 'required',
                'sub_category_id' => 'nullable',
                'sub_sub_category_id' => 'nullable',

                'name' => 'required|string',
                'status' => 'required|integer',

                'hsn_id' => 'nullable',
                'has_variant' => 'nullable|boolean',

                'commission' => 'nullable|numeric',
                'vendor_commission' => 'nullable|numeric',
                'batch_no' => 'nullable',
                'vendor_commission_approval_status' => 'nullable|integer|in:0,1,2',

                // Variants
                'variants' => 'required|array|min:1',

                'variants.*.sku' => 'nullable|string',
                'variants.*.barcode' => 'nullable|string',

                'variants.*.mrp' => 'required|numeric',
                'variants.*.cost_price' => 'nullable|numeric',
                'variants.*.selling_price' => 'nullable|numeric',
                'variants.*.discount' => 'nullable|numeric',
                'variants.*.final_price' => 'nullable|numeric',

                'variants.*.stock' => 'nullable|integer',
                'variants.*.variant_status' => 'nullable|integer',

                'variants.*.manufacture_date' => 'nullable|date',
                'variants.*.expiry_date' => 'nullable|date',

                'variants.*.short_description' => 'nullable|string|max:1000',
                'variants.*.long_description' => 'nullable|string',

                'variants.*.is_primary' => 'nullable|boolean',

                // Images
                'variants.*.images' => 'nullable|array',
                'variants.*.images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10000',

                'variants.*.attributes' => [
                    Rule::requiredIf(fn () => $request->boolean('has_variant')),
                    'array',
                ],

                'variants.*.attributes.*.attribute_master_id' => [
                    Rule::requiredIf(fn () => $request->boolean('has_variant')),
                ],

                'variants.*.attributes.*.attribute_value_id' => [
                    Rule::requiredIf(fn () => $request->boolean('has_variant')),
                ],


                // Meta
                'variants.*.meta_title' => 'nullable|string',
                'variants.*.meta_keyword' => 'nullable|string',
                'variants.*.meta_description' => 'nullable|string',

            ];

            $data = $request->validate($rules);

            // DECODE MAIN IDS
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

            // CREATE PRODUCT
            $productId = DB::table($tableName)->insertGetId([
                'business_id' => $business->id,
                'business_category_id' => $business->business_category_id,
                'business_sub_category_id' => $business->business_sub_category_id,
                'category_id' => $data['category_id'],
                'sub_category_id' => $data['sub_category_id'] ?? null,
                'sub_sub_category_id' => $data['sub_sub_category_id'] ?? null,
                'hsn_id' =>$data['hsn_id'] ?? null,
                'name' => $data['name'],
                'has_variant' => $data['has_variant'] ?? 0,
                'status' => $data['status'],
                'commission' => $data['commission'] ?? null,
                'vendor_commission' => $data['vendor_commission'] ?? null,
                'vendor_commission_approval_status' => $data['vendor_commission_approval_status'] ?? 0,
                'batch_no' => $data['batch_no'] ?? null,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
            //dd(DB::getQueryLog(), $productId);
            $variantIds = [];

            // VARIANTS LOOP
            foreach ($data['variants'] as $index => $variantData) {

                $variant = ProductVariant::create([
                    'product_id' => $productId,
                    'product_type' => $categoryId,

                    'sku' => $variantData['sku'] ?? null,
                    'barcode' => $variantData['barcode'] ?? null,

                    'mrp' => $variantData['mrp'],
                    'cost_price' => $variantData['cost_price'] ?? null,
                    'selling_price' => $variantData['selling_price'] ?? null,
                    'discount' => $variantData['discount'] ?? 0,
                    'final_price' => $variantData['final_price'] ?? null,



                    'short_description' => $variantData['short_description'] ?? null,
                    'long_description' => $variantData['long_description'] ?? null,

                    'variant_status' => $variantData['variant_status'] ?? null,


                    'manufacture_date' => $variantData['manufacture_date'] ?? null,
                    'expiry_date' => $variantData['expiry_date'] ?? null,

                    'is_primary' => !empty($variantData['is_primary']) ? true : false,
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

                // ATTRIBUTES WITH DECODE
                if (
                    $request->boolean('has_variant') &&
                    !empty($variantData['attributes']) &&
                    is_array($variantData['attributes'])
                ) {

                    $insertData = [];

                    foreach ($variantData['attributes'] as $attr) {

                        if (
                            !empty($attr['attribute_master_id']) &&
                            !empty($attr['attribute_value_id'])
                        ) {

                            $attributeMasterId = decodeIdOrFail(
                                $attr['attribute_master_id'],
                                'Invalid Attribute Master ID'
                            );

                            $attributeValueId = decodeIdOrFail(
                                $attr['attribute_value_id'],
                                'Invalid Attribute Value ID'
                            );

                            if (
                                !DB::table('attribute_masters')
                                    ->where('id', $attributeMasterId)
                                    ->exists()
                            ) {
                                throw new \Exception('Attribute master not found');
                            }

                            if (
                                !DB::table('attribute_values')
                                    ->where('id', $attributeValueId)
                                    ->where('attribute_master_id', $attributeMasterId)
                                    ->exists()
                            ) {
                                throw new \Exception(
                                    'Selected attribute value does not belong to the selected attribute'
                                );
                            }

                            $insertData[] = [
                                'product_variant_id' => $variant->id,
                                'attribute_master_id' => $attributeMasterId,
                                'attribute_value_id' => $attributeValueId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if (!empty($insertData)) {
                        DB::table('product_attribute_relations')->insert($insertData);
                    }
                }

                // IMAGES
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
                'stocks.business'
            ])->whereIn('id', $variantIds)->get();

             $product = DB::table($tableName)->where('id', $productId)->first();

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'data' => [
                    'product_id' => Hashids::encode($productId),
                    'name' => $product->name,
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
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid Product ID'
                ], 400);
            }

            $productId = $decoded[0];

            $businessId = null;

            if ($request->filled('business_id')) {

                $decodedBusiness = Hashids::decode(
                    $request->business_id
                );

                if (empty($decodedBusiness)) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Invalid Business ID'
                    ], 400);
                }

                $businessId = $decodedBusiness[0];

            }

            $businessCategoryId = null;

            if ($request->filled('business_category_id')) {

                $decodedBusinessCategory = Hashids::decode(
                    $request->business_category_id
                );

                if (empty($decodedBusinessCategory)) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Invalid Business Category ID'
                    ], 400);
                }

                $businessCategoryId = $decodedBusinessCategory[0];
            }

            $modelMap = config('product.model_map');

            $product = null;

            foreach ($modelMap as $type => $modelClass) {

                $query = $modelClass::query()
                    ->select([
                        'id',
                        'business_id',
                        'business_category_id',
                        'name',
                        'category_id',
                        'sub_category_id',
                        'sub_sub_category_id',
                        'hsn_id',
                        'commission',
                        'vendor_commission',
                        'vendor_commission_approval_status',
                        'batch_no',
                        'status',
                        'created_at'
                    ])

                    ->where('id', $productId)

                    ->when($businessId, function ($q) use ($businessId) {
                        $q->where('business_id', $businessId);
                    })

                    ->when($businessCategoryId, function ($q) use ($businessCategoryId) {
                        $q->where('business_category_id', $businessCategoryId);
                    })

                    ->with([

                        'category:id,name',

                        'subCategory:id,name',

                        'subSubCategory:id,name',

                        'hsn:id,hsn_code,igst',

                        'variants' => function ($q) use ($productId) {

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
                                'is_primary',
                                'variant_status',
                                'manufacture_date',
                                'expiry_date'
                            ])

                            ->where('product_id', $productId)

                            ->with([

                                'meta:id,product_variant_id,meta_title,meta_keyword,meta_description',

                                'attributes' => function ($attr) {
                                    $attr->select([
                                        'id',
                                        'product_variant_id',
                                        'attribute_master_id',
                                        'attribute_value_id'
                                    ])->with([
                                        'attributeMaster:id,name',
                                        'attributeValue:id,value,color_code'
                                    ]);
                                },

                                'images' => function ($img) {

                                    $img->select([
                                        'id',
                                        'product_variant_id',
                                        'image_medium'
                                    ]);
                                },

                                'stocks'
                            ]);
                        }
                    ]);

                $product = $query->first();

                if ($product) {
                    $product->product_type = $type;
                    break;
                }
            }

            if (!$product) {

                return response()->json([
                    'status'  => false,
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data'   => new ProductResource($product)
            ], 200);

        } catch (\Exception $e) {

            Log::error('Product Show Error: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Update the specified resource in storage.
     */

    // public function update(Request $request, string $id)
    // {
    //     //dd($request->all());
    //     DB::beginTransaction();

    //     try {

    //         // Decode Product ID
    //         $productId = decodeIdOrFail($id, 'Invalid Product ID');

    //         // Validation
    //         $rules = [
    //             'business_id' => 'required',
    //             'category_id' => 'required',
    //             'sub_category_id' => 'nullable',
    //             'sub_sub_category_id' => 'nullable',

    //             'name' => 'required|string',
    //             'status' => 'required|integer',

    //             'hsn_id' => 'nullable',
    //             'has_variant' => 'nullable|boolean',

    //             'commission' => 'nullable|numeric',
    //             'vendor_commission' => 'nullable|numeric',
    //             'batch_no' => 'nullable',
    //             'vendor_commission_approval_status' => 'nullable|integer|in:0,1,2',

    //             'variants' => 'required|array|min:1',

    //             // Existing Variant ID (for update)
    //             'variants.*.variant_id' => 'nullable',

    //             'variants.*.sku' => 'nullable|string',
    //             'variants.*.barcode' => 'nullable|string',

    //             'variants.*.mrp' => 'required|numeric',
    //             'variants.*.cost_price' => 'nullable|numeric',
    //             'variants.*.selling_price' => 'nullable|numeric',
    //             'variants.*.discount' => 'nullable|numeric',
    //             'variants.*.final_price' => 'nullable|numeric',

    //             'variants.*.stock' => 'nullable|integer',
    //             'variants.*.variant_status' => 'nullable|integer',

    //             'variants.*.manufacture_date' => 'nullable|date',
    //             'variants.*.expiry_date' => 'nullable|date',

    //             'variants.*.short_description' => 'nullable|string|max:1000',
    //             'variants.*.long_description' => 'nullable|string',

    //             'variants.*.is_primary' => 'nullable|boolean',

    //             'variants.*.images' => 'nullable|array',
    //             'variants.*.images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10000',

    //             'variants.*.attributes' => [
    //                 Rule::requiredIf(fn () => $request->boolean('has_variant')),
    //                 'array',
    //             ],

    //             'variants.*.attributes.*.attribute_master_id' => [
    //                 Rule::requiredIf(fn () => $request->boolean('has_variant')),
    //             ],

    //             'variants.*.attributes.*.attribute_value_id' => [
    //                 Rule::requiredIf(fn () => $request->boolean('has_variant')),
    //             ],

    //             'variants.*.meta_title' => 'nullable|string',
    //             'variants.*.meta_keyword' => 'nullable|string',
    //             'variants.*.meta_description' => 'nullable|string',
    //         ];

    //         $data = $request->validate($rules);

    //         // Decode IDs
    //         $data['business_id'] = decodeIdOrFail($data['business_id']);
    //         $data['category_id'] = decodeIdOrFail($data['category_id']);

    //         if (!empty($data['sub_category_id'])) {
    //             $data['sub_category_id'] = decodeIdOrFail($data['sub_category_id']);
    //         }

    //         if (!empty($data['sub_sub_category_id'])) {
    //             $data['sub_sub_category_id'] = decodeIdOrFail($data['sub_sub_category_id']);
    //         }

    //         if (!empty($data['hsn_id'])) {
    //             $data['hsn_id'] = decodeIdOrFail($data['hsn_id']);
    //         }

    //         $business = Business::findOrFail($data['business_id']);

    //         $tableMap = config('product.table_map');
    //         $tableName = $tableMap[$business->business_category_id] ?? null;

    //         if (!$tableName) {
    //             throw new \Exception('Invalid business category mapping');
    //         }

    //         // Verify Product Exists
    //         $product = DB::table($tableName)
    //             ->where('id', $productId)
    //             ->first();

    //         if (!$product) {
    //             throw new \Exception('Product not found');
    //         }

    //         // Update Product
    //         DB::table($tableName)
    //             ->where('id', $productId)
    //             ->update([
    //                 'category_id' => $data['category_id'],
    //                 'sub_category_id' => $data['sub_category_id'] ?? null,
    //                 'sub_sub_category_id' => $data['sub_sub_category_id'] ?? null,
    //                 'hsn_id' => $data['hsn_id'] ?? null,
    //                 'name' => $data['name'],
    //                 'has_variant' => $data['has_variant'] ?? 0,
    //                 'status' => $data['status'],
    //                 'commission' => $data['commission'] ?? null,
    //                 'vendor_commission' => $data['vendor_commission'] ?? null,
    //                 'vendor_commission_approval_status' => $data['vendor_commission_approval_status'] ?? 0,
    //                 'batch_no' => $data['batch_no'] ?? null,
    //                 'updated_at' => now(),
    //             ]);

    //         $variantIds = [];
    //         $existingVariantIds = [];

    //         foreach ($data['variants'] as $index => $variantData) {

    //             $variantId = null;

    //             if (!empty($variantData['variant_id'])) {
    //                 $variantId = decodeIdOrFail(
    //                     $variantData['variant_id'],
    //                     'Invalid Variant ID'
    //                 );

    //                 $variant = ProductVariant::findOrFail($variantId);

    //                 $variant->update([
    //                     'sku' => $variantData['sku'] ?? null,
    //                     'barcode' => $variantData['barcode'] ?? null,
    //                     'mrp' => $variantData['mrp'],
    //                     'cost_price' => $variantData['cost_price'] ?? null,
    //                     'selling_price' => $variantData['selling_price'] ?? null,
    //                     'discount' => $variantData['discount'] ?? 0,
    //                     'final_price' => $variantData['final_price'] ?? null,
    //                     'short_description' => $variantData['short_description'] ?? null,
    //                     'long_description' => $variantData['long_description'] ?? null,
    //                     'variant_status' => $variantData['variant_status'] ?? null,
    //                     'manufacture_date' => $variantData['manufacture_date'] ?? null,
    //                     'expiry_date' => $variantData['expiry_date'] ?? null,
    //                     'is_primary' => !empty($variantData['is_primary']),
    //                 ]);
    //             } else {

    //                 $variant = ProductVariant::create([
    //                     'product_id' => $productId,
    //                     'product_type' => $business->business_category_id,

    //                     'sku' => $variantData['sku'] ?? null,
    //                     'barcode' => $variantData['barcode'] ?? null,
    //                     'mrp' => $variantData['mrp'],
    //                     'cost_price' => $variantData['cost_price'] ?? null,
    //                     'selling_price' => $variantData['selling_price'] ?? null,
    //                     'discount' => $variantData['discount'] ?? 0,
    //                     'final_price' => $variantData['final_price'] ?? null,
    //                     'short_description' => $variantData['short_description'] ?? null,
    //                     'long_description' => $variantData['long_description'] ?? null,
    //                     'variant_status' => $variantData['variant_status'] ?? null,
    //                     'manufacture_date' => $variantData['manufacture_date'] ?? null,
    //                     'expiry_date' => $variantData['expiry_date'] ?? null,
    //                     'is_primary' => !empty($variantData['is_primary']),
    //                 ]);
    //             }

    //             $existingVariantIds[] = $variant->id;

    //             // Stock Update
    //             ProductVendorStock::updateOrCreate(
    //                 [
    //                     'product_variant_id' => $variant->id,
    //                     'business_id' => $business->id,
    //                 ],
    //                 [
    //                     'stock' => $variantData['stock'] ?? 0,
    //                 ]
    //             );

    //             // Meta Update
    //             ProductVariantMeta::updateOrCreate(
    //                 ['product_variant_id' => $variant->id],
    //                 [
    //                     'meta_title' => $variantData['meta_title'] ?? null,
    //                     'meta_keyword' => $variantData['meta_keyword'] ?? null,
    //                     'meta_description' => $variantData['meta_description'] ?? null,
    //                 ]
    //             );

    //             // Attributes
    //             DB::table('product_attribute_relations')
    //                 ->where('product_variant_id', $variant->id)
    //                 ->delete();

    //             if (!empty($variantData['attributes'])) {

    //                 $insertData = [];

    //                 foreach ($variantData['attributes'] as $attr) {

    //                     $insertData[] = [
    //                         'product_variant_id' => $variant->id,
    //                         'attribute_master_id' => decodeIdOrFail($attr['attribute_master_id']),
    //                         'attribute_value_id' => decodeIdOrFail($attr['attribute_value_id']),
    //                         'created_at' => now(),
    //                         'updated_at' => now(),
    //                     ];
    //                 }

    //                 if ($insertData) {
    //                     DB::table('product_attribute_relations')->insert($insertData);
    //                 }
    //             }

    //             $variantIds[] = $variant->id;

    //             $files = collect($request->file("variants.$index.images", []))
    //                 ->filter(fn ($file) => $file && $file->isValid());

    //             if ($files->isNotEmpty()) {

    //                 // Delete old images
    //                 $oldImages = ProductImage::where(
    //                     'product_variant_id',
    //                     $variant->id
    //                 )->get();

    //                 foreach ($oldImages as $oldImage) {

    //                     Storage::disk('public')->delete([
    //                         $oldImage->image_large,
    //                         $oldImage->image_medium,
    //                         $oldImage->image_small,
    //                     ]);

    //                     $oldImage->delete();
    //                 }

    //                 $manager = new ImageManager(new Driver());

    //                 foreach ($files as $file) {

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



    //         }

    //         // Delete removed variants
    //         ProductVariant::where('product_id', $productId)
    //             ->whereNotIn('id', $existingVariantIds)
    //             ->delete();

    //         DB::commit();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Product updated successfully'
    //         ]);

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
    //             'error' => $e->getMessage(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //         ], 500);
    //     }
    // }


    // public function update(Request $request, string $id)
    // {
    //     DB::beginTransaction();

    //     try {

    //         // Decode Product ID
    //         $productId = decodeIdOrFail($id, 'Invalid Product ID');

    //         // Validation
    //         $rules = [
    //             'business_id' => 'required',
    //             'category_id' => 'required',
    //             'sub_category_id' => 'nullable',
    //             'sub_sub_category_id' => 'nullable',

    //             'name' => 'required|string',
    //             'status' => 'required|integer',

    //             'hsn_id' => 'nullable',
    //             'has_variant' => 'nullable|boolean',

    //             'commission' => 'nullable|numeric',
    //             'vendor_commission' => 'nullable|numeric',
    //             'batch_no' => 'nullable',
    //             'vendor_commission_approval_status' => 'nullable|integer|in:0,1,2',

    //             'variants' => 'required|array|min:1',

    //             'variants.*.variant_id' => 'nullable',

    //             'variants.*.sku' => 'nullable|string',
    //             'variants.*.barcode' => 'nullable|string',

    //             'variants.*.mrp' => 'required|numeric',
    //             'variants.*.cost_price' => 'nullable|numeric',
    //             'variants.*.selling_price' => 'nullable|numeric',
    //             'variants.*.discount' => 'nullable|numeric',
    //             'variants.*.final_price' => 'nullable|numeric',

    //             'variants.*.stock' => 'nullable|integer',
    //             'variants.*.variant_status' => 'nullable|integer',

    //             'variants.*.manufacture_date' => 'nullable|date',
    //             'variants.*.expiry_date' => 'nullable|date',

    //             'variants.*.short_description' => 'nullable|string|max:1000',
    //             'variants.*.long_description' => 'nullable|string',

    //             'variants.*.is_primary' => 'nullable|boolean',

    //             'variants.*.images' => 'nullable|array',
    //             'variants.*.images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10000',

    //             'variants.*.attributes' => [
    //                 Rule::requiredIf(fn () => $request->boolean('has_variant')),
    //                 'array',
    //             ],

    //             'variants.*.attributes.*.attribute_master_id' => [
    //                 Rule::requiredIf(fn () => $request->boolean('has_variant')),
    //             ],

    //             'variants.*.attributes.*.attribute_value_id' => [
    //                 Rule::requiredIf(fn () => $request->boolean('has_variant')),
    //             ],

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

    //         if (!empty($data['sub_sub_category_id'])) {
    //             $data['sub_sub_category_id'] = decodeIdOrFail($data['sub_sub_category_id'], 'Invalid Sub Sub Category ID');
    //         }

    //         if (!empty($data['hsn_id'])) {
    //             $data['hsn_id'] = decodeIdOrFail($data['hsn_id'], 'Invalid HSN ID');
    //         }

    //         $business = Business::findOrFail($data['business_id']);

    //         $tableMap = config('product.table_map');
    //         $categoryId = $business->business_category_id;
    //         $tableName = $tableMap[$categoryId] ?? null;

    //         if (!$tableName) {
    //             throw new \Exception('Invalid business category mapping');
    //         }

    //         // Verify Product Exists
    //         $product = DB::table($tableName)
    //             ->where('id', $productId)
    //             ->first();

    //         if (!$product) {
    //             throw new \Exception('Product not found');
    //         }

    //         // Update Product
    //         DB::table($tableName)
    //             ->where('id', $productId)
    //             ->update([
    //                 'category_id' => $data['category_id'],
    //                 'sub_category_id' => $data['sub_category_id'] ?? null,
    //                 'sub_sub_category_id' => $data['sub_sub_category_id'] ?? null,
    //                 'hsn_id' => $data['hsn_id'] ?? null,
    //                 'name' => $data['name'],
    //                 'has_variant' => $data['has_variant'] ?? 0,
    //                 'status' => $data['status'],
    //                 'commission' => $data['commission'] ?? null,
    //                 'vendor_commission' => $data['vendor_commission'] ?? null,
    //                 'vendor_commission_approval_status' => $data['vendor_commission_approval_status'] ?? 0,
    //                 'batch_no' => $data['batch_no'] ?? null,
    //                 'updated_at' => now(),
    //             ]);

    //         $existingVariantIds = [];

    //         foreach ($data['variants'] as $index => $variantData) {

    //             if (!empty($variantData['variant_id'])) {

    //                 $variantId = decodeIdOrFail($variantData['variant_id'], 'Invalid Variant ID');

    //                 $variant = ProductVariant::findOrFail($variantId);

    //                 $variant->update([
    //                     'sku' => $variantData['sku'] ?? null,
    //                     'barcode' => $variantData['barcode'] ?? null,
    //                     'mrp' => $variantData['mrp'],
    //                     'cost_price' => $variantData['cost_price'] ?? null,
    //                     'selling_price' => $variantData['selling_price'] ?? null,
    //                     'discount' => $variantData['discount'] ?? 0,
    //                     'final_price' => $variantData['final_price'] ?? null,
    //                     'short_description' => $variantData['short_description'] ?? null,
    //                     'long_description' => $variantData['long_description'] ?? null,
    //                     'variant_status' => $variantData['variant_status'] ?? null,
    //                     'manufacture_date' => $variantData['manufacture_date'] ?? null,
    //                     'expiry_date' => $variantData['expiry_date'] ?? null,
    //                     'is_primary' => !empty($variantData['is_primary']),
    //                 ]);

    //             } else {

    //                 $variant = ProductVariant::create([
    //                     'product_id' => $productId,
    //                     'product_type' => $categoryId,

    //                     'sku' => $variantData['sku'] ?? null,
    //                     'barcode' => $variantData['barcode'] ?? null,
    //                     'mrp' => $variantData['mrp'],
    //                     'cost_price' => $variantData['cost_price'] ?? null,
    //                     'selling_price' => $variantData['selling_price'] ?? null,
    //                     'discount' => $variantData['discount'] ?? 0,
    //                     'final_price' => $variantData['final_price'] ?? null,
    //                     'short_description' => $variantData['short_description'] ?? null,
    //                     'long_description' => $variantData['long_description'] ?? null,
    //                     'variant_status' => $variantData['variant_status'] ?? null,
    //                     'manufacture_date' => $variantData['manufacture_date'] ?? null,
    //                     'expiry_date' => $variantData['expiry_date'] ?? null,
    //                     'is_primary' => !empty($variantData['is_primary']),
    //                 ]);
    //             }

    //             $existingVariantIds[] = $variant->id;

    //             // Stock Update
    //             ProductVendorStock::updateOrCreate(
    //                 [
    //                     'product_variant_id' => $variant->id,
    //                     'business_id' => $business->id,
    //                 ],
    //                 [
    //                     'stock' => $variantData['stock'] ?? 0,
    //                 ]
    //             );

    //             // Meta Update — only save if at least one meta field is present (matches store)
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

    //             // Attributes — delete old, re-insert with existence checks (matches store)
    //             DB::table('product_attribute_relations')
    //                 ->where('product_variant_id', $variant->id)
    //                 ->delete();

    //             if (
    //                 $request->boolean('has_variant') &&
    //                 !empty($variantData['attributes']) &&
    //                 is_array($variantData['attributes'])
    //             ) {
    //                 $insertData = [];

    //                 foreach ($variantData['attributes'] as $attr) {

    //                     if (
    //                         !empty($attr['attribute_master_id']) &&
    //                         !empty($attr['attribute_value_id'])
    //                     ) {
    //                         $attributeMasterId = decodeIdOrFail(
    //                             $attr['attribute_master_id'],
    //                             'Invalid Attribute Master ID'
    //                         );

    //                         $attributeValueId = decodeIdOrFail(
    //                             $attr['attribute_value_id'],
    //                             'Invalid Attribute Value ID'
    //                         );

    //                         if (
    //                             !DB::table('attribute_masters')
    //                                 ->where('id', $attributeMasterId)
    //                                 ->exists()
    //                         ) {
    //                             throw new \Exception('Attribute master not found');
    //                         }

    //                         if (
    //                             !DB::table('attribute_values')
    //                                 ->where('id', $attributeValueId)
    //                                 ->where('attribute_master_id', $attributeMasterId)
    //                                 ->exists()
    //                         ) {
    //                             throw new \Exception(
    //                                 'Selected attribute value does not belong to the selected attribute'
    //                             );
    //                         }

    //                         $insertData[] = [
    //                             'product_variant_id' => $variant->id,
    //                             'attribute_master_id' => $attributeMasterId,
    //                             'attribute_value_id' => $attributeValueId,
    //                             'created_at' => now(),
    //                             'updated_at' => now(),
    //                         ];
    //                     }
    //                 }

    //                 if (!empty($insertData)) {
    //                     DB::table('product_attribute_relations')->insert($insertData);
    //                 }
    //             }

    //             // Images — same pattern as store: hasFile check + per-index dot notation
    //             if ($request->hasFile("variants.$index.images")) {

    //                 // Delete old images first
    //                 $oldImages = ProductImage::where('product_variant_id', $variant->id)->get();

    //                 foreach ($oldImages as $oldImage) {
    //                     Storage::disk('public')->delete([
    //                         $oldImage->image_large,
    //                         $oldImage->image_medium,
    //                         $oldImage->image_small,
    //                     ]);
    //                     $oldImage->delete();
    //                 }

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
    //         }

    //         // Delete variants that were removed from the request
    //         ProductVariant::where('product_id', $productId)
    //             ->whereNotIn('id', $existingVariantIds)
    //             ->delete();

    //         DB::commit();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Product updated successfully'
    //         ]);

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
    //             'error' => $e->getMessage(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //         ], 500);
    //     }
    // }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        try {

            // Decode Product ID
            $productId = decodeIdOrFail($id, 'Invalid Product ID');

            // Validation
            $rules = [
                'business_id' => 'required',
                'category_id' => 'required',
                'sub_category_id' => 'nullable',
                'sub_sub_category_id' => 'nullable',

                'name' => 'required|string',
                'status' => 'required|integer',

                'hsn_id' => 'nullable',
                'has_variant' => 'nullable|boolean',

                'commission' => 'nullable|numeric',
                'vendor_commission' => 'nullable|numeric',
                'batch_no' => 'nullable',
                'vendor_commission_approval_status' => 'nullable|integer|in:0,1,2',

                'variants' => 'required|array|min:1',

                'variants.*.variant_id' => 'nullable',

                'variants.*.sku' => 'nullable|string',
                'variants.*.barcode' => 'nullable|string',

                'variants.*.mrp' => 'required|numeric',
                'variants.*.cost_price' => 'nullable|numeric',
                'variants.*.selling_price' => 'nullable|numeric',
                'variants.*.discount' => 'nullable|numeric',
                'variants.*.final_price' => 'nullable|numeric',

                'variants.*.stock' => 'nullable|integer',
                'variants.*.variant_status' => 'nullable|integer',

                'variants.*.manufacture_date' => 'nullable|date',
                'variants.*.expiry_date' => 'nullable|date',

                'variants.*.short_description' => 'nullable|string|max:1000',
                'variants.*.long_description' => 'nullable|string',

                'variants.*.is_primary' => 'nullable|boolean',

                'variants.*.images' => 'nullable|array',
                'variants.*.images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10000',

                'variants.*.attributes' => [
                    Rule::requiredIf(fn () => $request->boolean('has_variant')),
                    'array',
                ],

                'variants.*.attributes.*.attribute_master_id' => [
                    Rule::requiredIf(fn () => $request->boolean('has_variant')),
                ],

                'variants.*.attributes.*.attribute_value_id' => [
                    Rule::requiredIf(fn () => $request->boolean('has_variant')),
                ],

                'variants.*.meta_title' => 'nullable|string',
                'variants.*.meta_keyword' => 'nullable|string',
                'variants.*.meta_description' => 'nullable|string',
            ];

            $data = $request->validate($rules);

            // Decode IDs
            $data['business_id'] = decodeIdOrFail($data['business_id'], 'Invalid Business ID');
            $data['category_id'] = decodeIdOrFail($data['category_id'], 'Invalid Category ID');

            if (!empty($data['sub_category_id'])) {
                $data['sub_category_id'] = decodeIdOrFail($data['sub_category_id'], 'Invalid Sub Category ID');
            }

            if (!empty($data['sub_sub_category_id'])) {
                $data['sub_sub_category_id'] = decodeIdOrFail($data['sub_sub_category_id'], 'Invalid Sub Sub Category ID');
            }

            if (!empty($data['hsn_id'])) {
                $data['hsn_id'] = decodeIdOrFail($data['hsn_id'], 'Invalid HSN ID');
            }

            $business = Business::findOrFail($data['business_id']);

            $tableMap = config('product.table_map');
            $categoryId = $business->business_category_id;
            $tableName = $tableMap[$categoryId] ?? null;

            if (!$tableName) {
                throw new \Exception('Invalid business category mapping');
            }

            // Verify Product Exists
            $product = DB::table($tableName)->where('id', $productId)->first();

            if (!$product) {
                throw new \Exception('Product not found');
            }

            // Update Product
            DB::table($tableName)
                ->where('id', $productId)
                ->update([
                    'category_id' => $data['category_id'],
                    'sub_category_id' => $data['sub_category_id'] ?? null,
                    'sub_sub_category_id' => $data['sub_sub_category_id'] ?? null,
                    'hsn_id' => $data['hsn_id'] ?? null,
                    'name' => $data['name'],
                    'has_variant' => $data['has_variant'] ?? 0,
                    'status' => $data['status'],
                    'commission' => $data['commission'] ?? null,
                    'vendor_commission' => $data['vendor_commission'] ?? null,
                    'vendor_commission_approval_status' => $data['vendor_commission_approval_status'] ?? 0,
                    'batch_no' => $data['batch_no'] ?? null,
                    'updated_at' => now(),
                ]);

            // ---------------------------------------------------------------
            // Pre-load existing variants ordered by ID so we can match them
            // positionally when no variant_id is sent in the request.
            // e.g. request variant[0] (no variant_id) → maps to DB variant id=1
            //      request variant[1] (no variant_id) → maps to DB variant id=2
            // ---------------------------------------------------------------
            $existingVariants = ProductVariant::where('product_id', $productId)
                ->orderBy('id')
                ->get()
                ->values(); // re-index to 0,1,2...

            $existingVariantIds = [];

            foreach ($data['variants'] as $index => $variantData) {

                if (!empty($variantData['variant_id'])) {

                    // -----------------------------------------------------------
                    // UPDATE path — variant_id explicitly provided
                    // -----------------------------------------------------------
                    $variantId = decodeIdOrFail($variantData['variant_id'], 'Invalid Variant ID');
                    $variant = ProductVariant::findOrFail($variantId);

                    $variant->update([
                        'sku' => $variantData['sku'] ?? null,
                        'barcode' => $variantData['barcode'] ?? null,
                        'mrp' => $variantData['mrp'],
                        'cost_price' => $variantData['cost_price'] ?? null,
                        'selling_price' => $variantData['selling_price'] ?? null,
                        'discount' => $variantData['discount'] ?? 0,
                        'final_price' => $variantData['final_price'] ?? null,
                        'short_description' => $variantData['short_description'] ?? null,
                        'long_description' => $variantData['long_description'] ?? null,
                        'variant_status' => $variantData['variant_status'] ?? null,
                        'manufacture_date' => $variantData['manufacture_date'] ?? null,
                        'expiry_date' => $variantData['expiry_date'] ?? null,
                        'is_primary' => !empty($variantData['is_primary']),
                    ]);

                } else {

                    // -----------------------------------------------------------
                    // NO variant_id sent — match by position in the existing list.
                    // If a matching existing variant is found at this index, UPDATE
                    // it in place (avoids creating a new row and keeps images
                    // correctly associated).
                    // If no existing variant at this index, CREATE a new one.
                    // -----------------------------------------------------------
                    $matchedExisting = $existingVariants->get($index); // null-safe

                    if ($matchedExisting) {

                        $variant = $matchedExisting;

                        $variant->update([
                            'sku' => $variantData['sku'] ?? null,
                            'barcode' => $variantData['barcode'] ?? null,
                            'mrp' => $variantData['mrp'],
                            'cost_price' => $variantData['cost_price'] ?? null,
                            'selling_price' => $variantData['selling_price'] ?? null,
                            'discount' => $variantData['discount'] ?? 0,
                            'final_price' => $variantData['final_price'] ?? null,
                            'short_description' => $variantData['short_description'] ?? null,
                            'long_description' => $variantData['long_description'] ?? null,
                            'variant_status' => $variantData['variant_status'] ?? null,
                            'manufacture_date' => $variantData['manufacture_date'] ?? null,
                            'expiry_date' => $variantData['expiry_date'] ?? null,
                            'is_primary' => !empty($variantData['is_primary']),
                        ]);

                    } else {

                        // Genuinely new variant (more variants sent than exist in DB)
                        $variant = ProductVariant::create([
                            'product_id' => $productId,
                            'product_type' => $categoryId,

                            'sku' => $variantData['sku'] ?? null,
                            'barcode' => $variantData['barcode'] ?? null,
                            'mrp' => $variantData['mrp'],
                            'cost_price' => $variantData['cost_price'] ?? null,
                            'selling_price' => $variantData['selling_price'] ?? null,
                            'discount' => $variantData['discount'] ?? 0,
                            'final_price' => $variantData['final_price'] ?? null,
                            'short_description' => $variantData['short_description'] ?? null,
                            'long_description' => $variantData['long_description'] ?? null,
                            'variant_status' => $variantData['variant_status'] ?? null,
                            'manufacture_date' => $variantData['manufacture_date'] ?? null,
                            'expiry_date' => $variantData['expiry_date'] ?? null,
                            'is_primary' => !empty($variantData['is_primary']),
                        ]);
                    }
                }

                $existingVariantIds[] = $variant->id;

                // Stock Update
                ProductVendorStock::updateOrCreate(
                    [
                        'product_variant_id' => $variant->id,
                        'business_id' => $business->id,
                    ],
                    [
                        'stock' => $variantData['stock'] ?? 0,
                    ]
                );

                // Meta — only save if at least one field is present
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

                // Attributes — delete old, re-insert with existence checks
                DB::table('product_attribute_relations')
                    ->where('product_variant_id', $variant->id)
                    ->delete();

                if (
                    $request->boolean('has_variant') &&
                    !empty($variantData['attributes']) &&
                    is_array($variantData['attributes'])
                ) {
                    $insertData = [];

                    foreach ($variantData['attributes'] as $attr) {

                        if (
                            !empty($attr['attribute_master_id']) &&
                            !empty($attr['attribute_value_id'])
                        ) {
                            $attributeMasterId = decodeIdOrFail(
                                $attr['attribute_master_id'],
                                'Invalid Attribute Master ID'
                            );

                            $attributeValueId = decodeIdOrFail(
                                $attr['attribute_value_id'],
                                'Invalid Attribute Value ID'
                            );

                            if (
                                !DB::table('attribute_masters')
                                    ->where('id', $attributeMasterId)
                                    ->exists()
                            ) {
                                throw new \Exception('Attribute master not found');
                            }

                            if (
                                !DB::table('attribute_values')
                                    ->where('id', $attributeValueId)
                                    ->where('attribute_master_id', $attributeMasterId)
                                    ->exists()
                            ) {
                                throw new \Exception(
                                    'Selected attribute value does not belong to the selected attribute'
                                );
                            }

                            $insertData[] = [
                                'product_variant_id' => $variant->id,
                                'attribute_master_id' => $attributeMasterId,
                                'attribute_value_id' => $attributeValueId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if (!empty($insertData)) {
                        DB::table('product_attribute_relations')->insert($insertData);
                    }
                }

                // Images
                // $variant->id is always correct here:
                // - update path: same variant, images already on it
                // - positional match path: same variant, images already on it
                // - new variant path: genuinely new, no old images to worry about
                if ($request->hasFile("variants.$index.images")) {

                    // Delete old images for this specific variant
                    $oldImages = ProductImage::where('product_variant_id', $variant->id)->get();

                    foreach ($oldImages as $oldImage) {
                        Storage::disk('public')->delete([
                            $oldImage->image_large,
                            $oldImage->image_medium,
                            $oldImage->image_small,
                        ]);
                        $oldImage->delete();
                    }

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
            }

            // Delete variants removed from the request + their images from storage
            $removedVariants = ProductVariant::where('product_id', $productId)
                ->whereNotIn('id', $existingVariantIds)
                ->get();

            foreach ($removedVariants as $removedVariant) {

                $orphanImages = ProductImage::where('product_variant_id', $removedVariant->id)->get();

                foreach ($orphanImages as $orphanImage) {
                    Storage::disk('public')->delete([
                        $orphanImage->image_large,
                        $orphanImage->image_medium,
                        $orphanImage->image_small,
                    ]);
                    $orphanImage->delete();
                }

                $removedVariant->delete();
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully'
            ]);

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
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
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

            // =============================
            // DECODE ID
            // =============================
            $imageId = decodeIdOrFail($id);

            // =============================
            // FIND IMAGE
            // =============================
            $image = ProductImage::find($imageId);

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

    /**
     * Update the specified resource in storage.
     */
    public function updateVariantStatus(Request $request, $variant_id)
    {
        $request->validate([
            'variant_status' => 'required|in:0,1',
            'stock' => 'nullable|numeric|min:0'
        ]);

        try {

            // Decode Variant ID
            $decode_variant_id = decodeIdOrFail($variant_id);

            // Find Variant
            $variant = ProductVariant::findOrFail($decode_variant_id);

            // Update Variant Status
            $variant->variant_status = $request->variant_status;
            $variant->save();

            // Update Vendor Stock
            ProductVendorStock::where('product_variant_id', $variant->id)
                ->update([
                    'stock' => $request->variant_status == 0
                        ? 0
                        : ($request->stock ?? 100)
                ]);

            return response()->json([
                'status' => true,
                'message' => 'Variant status updated successfully',
                'data' => [
                    'variant_id' => $variant->id,
                    'variant_status' => $variant->variant_status,
                    'stock' => $request->variant_status == 0
                        ? 0
                        : ($request->stock ?? 100)
                ]
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }


    public function checkAttributeExists($categoryId)
    {
        try {

            $decodedCategoryId = decodeIdOrFail($categoryId);

            $exists = AttributeValue::where('category_id', $decodedCategoryId)
                ->exists();

            return response()->json([
                'success' => true,
                'attribute_exists' => $exists,
            ], 200);

        } catch (\Exception $e) {

            \Log::error('Check Attribute Exists Error', [
                'category_id' => $categoryId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
