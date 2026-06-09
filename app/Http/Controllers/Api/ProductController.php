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

            // FILTERS
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
                        'status',
                        'created_at'
                    ])
                    ->with([
                        'category:id,name',
                        'subCategory:id,name',
                        'subSubCategory:id,name',
                        'hsn:id,hsn_code,igst',

                        // optimized variant loading
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
                                'batch_no',
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
                                        'attribute_id',
                                        'attribute_value_id'
                                    ])->with([
                                        'attribute:id,name',
                                        'attributeValue:id,value,color_code'
                                    ]);
                                },

                                // ONLY ONE IMAGE
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

            // global sort
            $allProducts = $allProducts->sortByDesc('created_at')->values();

            // manual pagination (lightweight)
            $total = $allProducts->count();

            $paginated = $allProducts
                ->slice(($page - 1) * $perPage, $perPage)
                ->values();

            return response()->json([
                'status'  => true,
                'message' => 'Product list fetched successfully',
                'data'    => ProductResource::collection($paginated),
                'meta'    => [
                    'current_page' => (int)$page,
                    'per_page'     => (int)$perPage,
                    'total'        => $total,
                    'last_page'    => (int) ceil($total / $perPage),
                ]
            ], 200);

        } catch (Exception $e) {

            Log::error('Product Index Error: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //DB::beginTransaction();

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

                // Variants
                'variants' => 'required|array|min:1',

                'variants.*.sku' => 'required|string|distinct|unique:product_variants,sku',
                'variants.*.barcode' => 'nullable|string|distinct|unique:product_variants,barcode',

                'variants.*.mrp' => 'required|numeric',
                'variants.*.cost_price' => 'nullable|numeric',
                'variants.*.selling_price' => 'nullable|numeric',
                'variants.*.discount' => 'nullable|numeric',
                'variants.*.final_price' => 'nullable|numeric',

                'variants.*.commission' => 'nullable|numeric',
                'variants.*.vendor_commission' => 'nullable|numeric',


                'variants.*.stock' => 'nullable|integer',
                'variants.*.variant_status' => 'nullable|integer',
                'variants.*.batch_no' => 'nullable',
                'variants.*.manufacture_date' => 'nullable|date',
                'variants.*.expiry_date' => 'nullable|date',

                'variants.*.short_description' => 'nullable|string|max:1000',
                'variants.*.long_description' => 'nullable|string',

                'variants.*.is_primary' => 'nullable|boolean',

                // Images
                'variants.*.images' => 'nullable|array',
                'variants.*.images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10000',

                // ATTRIBUTES (NO EXISTS HERE)
                // 'variants.*.attributes' => 'nullable|array',
                // 'variants.*.attributes.*.attribute_id' => 'required_with:variants.*.attributes',
                // 'variants.*.attributes.*.attribute_value_id' => 'required_with:variants.*.attributes',

                'variants.*.attributes' => [
                    Rule::requiredIf(fn () => $request->boolean('has_variant')),
                    'array',
                ],

                'variants.*.attributes.*.attribute_id' => [
                    Rule::requiredIf(fn () => $request->boolean('has_variant')),
                ],

                'variants.*.attributes.*.attribute_value_id' => [
                    Rule::requiredIf(fn () => $request->boolean('has_variant')),
                ],


                // Meta
                'variants.*.meta_title' => 'nullable|string',
                'variants.*.meta_keyword' => 'nullable|string',
                'variants.*.meta_description' => 'nullable|string',

                // Vendor commission approval status
                'variants.*.vendor_commission_approval_status' => 'nullable|integer|in:0,1,2',

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

                    'sku' => $variantData['sku'],
                    'barcode' => $variantData['barcode'] ?? null,

                    'mrp' => $variantData['mrp'],
                    'cost_price' => $variantData['cost_price'] ?? null,
                    'selling_price' => $variantData['selling_price'] ?? null,
                    'discount' => $variantData['discount'] ?? 0,
                    'final_price' => $variantData['final_price'] ?? null,

                    'commission' => $variantData['commission'] ?? null,
                    'vendor_commission' => $variantData['vendor_commission'] ?? null,
                    'vendor_commission_approval_status' => $variantData['vendor_commission_approval_status'] ?? 0,

                    'short_description' => $variantData['short_description'] ?? null,
                    'long_description' => $variantData['long_description'] ?? null,

                    'variant_status' => $variantData['variant_status'] ?? null,

                    'batch_no' => $variantData['batch_no'] ?? null,
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
                /*
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
                                'product_variant_id' => $variant->id,
                                'attribute_id' => $attributeId,
                                'attribute_value_id' => $attributeValueId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    // Bulk insert (faster)
                    if (!empty($insertData)) {
                        DB::table('product_attribute_relations')->insert($insertData);
                    }
                }*/

                if (
                    $request->boolean('has_variant') &&
                    !empty($variantData['attributes']) &&
                    is_array($variantData['attributes'])
                ) {

                    $insertData = [];

                    foreach ($variantData['attributes'] as $attr) {

                        if (!empty($attr['attribute_id']) && !empty($attr['attribute_value_id'])) {

                            $attributeId = decodeIdOrFail(
                                $attr['attribute_id'],
                                'Invalid Attribute ID'
                            );

                            $attributeValueId = decodeIdOrFail(
                                $attr['attribute_value_id'],
                                'Invalid Attribute Value ID'
                            );

                            if (!DB::table('attributes')->where('id', $attributeId)->exists()) {
                                throw new \Exception('Attribute not found');
                            }

                            if (
                                !DB::table('attribute_values')
                                    ->where('id', $attributeValueId)
                                    ->where('attribute_id', $attributeId)
                                    ->exists()
                            ) {
                                throw new \Exception(
                                    'Selected attribute value does not belong to the selected attribute'
                                );
                            }

                            $insertData[] = [
                                'product_variant_id' => $variant->id,
                                'attribute_id' => $attributeId,
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
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Decode Product ID
            |--------------------------------------------------------------------------
            */
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid Product ID'
                ], 400);
            }

            $productId = $decoded[0];

            /*
            |--------------------------------------------------------------------------
            | Decode Business ID
            |--------------------------------------------------------------------------
            */
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

            /*
            |--------------------------------------------------------------------------
            | Decode Business Category ID
            |--------------------------------------------------------------------------
            */
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

            /*
            |--------------------------------------------------------------------------
            | Model Map
            |--------------------------------------------------------------------------
            */
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
                        'status',
                        'created_at'
                    ])

                    /*
                    |--------------------------------------------------------------------------
                    | Product ID + Business ID Uniqueness
                    |--------------------------------------------------------------------------
                    */
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

                        /*
                        |--------------------------------------------------------------------------
                        | Variants
                        |--------------------------------------------------------------------------
                        */
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
                                'commission',
                                'vendor_commission',
                                'vendor_commission_approval_status',
                                'discount',
                                'final_price',
                                'short_description',
                                'long_description',
                                'is_primary',
                                'variant_status',
                                'batch_no',
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
                                        'attribute_id',
                                        'attribute_value_id'
                                    ])->with([

                                        'attribute:id,name',

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

            /*
            |--------------------------------------------------------------------------
            | Product Not Found
            |--------------------------------------------------------------------------
            */
            if (!$product) {

                return response()->json([
                    'status'  => false,
                    'message' => 'Product not found'
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | Success Response
            |--------------------------------------------------------------------------
            */
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
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        try {

            // =============================
            // VALIDATION
            // =============================
            $rules = [
                'business_id' => 'required',
                'category_id' => 'required',

                'name' => 'required|string',
                'status' => 'required|integer',

                'variants' => 'required|array|min:1',

                'variants.*.id' => 'nullable',
                'variants.*.sku' => 'required|string|distinct',
                'variants.*.barcode' => 'nullable|string',

                'variants.*.mrp' => 'required|numeric',
                'variants.*.cost_price' => 'nullable|numeric',
                'variants.*.selling_price' => 'nullable|numeric',
                'variants.*.discount' => 'nullable|numeric',
                'variants.*.final_price' => 'nullable|numeric',
                'variants.*.stock' => 'nullable|integer',
                'variants.*.manufacture_date' => 'nullable|date',
                'variants.*.variant_status' => 'nullable|integer',
                'variants.*.expiry_date' => 'nullable|date',

                'variants.*.short_description' => 'nullable|string|max:1000',
                'variants.*.long_description' => 'nullable|string',

                'variants.*.is_primary' => 'nullable|boolean',

                // IMAGES
                'variants.*.images' => 'nullable|array',
                'variants.*.images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',

                // ATTRIBUTES
                'variants.*.attributes' => 'nullable|array',
                'variants.*.attributes.*.attribute_id' => 'required_with:variants.*.attributes',
                'variants.*.attributes.*.attribute_value_id' => 'required_with:variants.*.attributes',

                // META
                'variants.*.meta_title' => 'nullable|string',
                'variants.*.meta_keyword' => 'nullable|string',
                'variants.*.meta_description' => 'nullable|string',
            ];

            $data = $request->validate($rules);

            // =============================
            // PRE-CHECK SKU
            // =============================
            // foreach ($data['variants'] as $index => $variantData) {

            //     $sku = $variantData['sku'];

            //     $query = ProductVariant::where('sku', $sku);

            //     if (!empty($variantData['id'])) {
            //         $variantId = decodeIdOrFail($variantData['id']);
            //         $query->where('id', '!=', $variantId);
            //     }

            //     if ($query->exists()) {
            //         throw ValidationException::withMessages([
            //             "variants.$index.sku" => ["SKU '{$sku}' already exists"]
            //         ]);
            //     }
            // }

            $productId = decodeIdOrFail($id);  // <-- move this up from DECODE IDS

            foreach ($data['variants'] as $index => $variantData) {

                $sku = $variantData['sku'];

                $query = ProductVariant::where('sku', $sku);

                if (!empty($variantData['id'])) {
                    // Exclude this specific variant by its own ID
                    $variantId = decodeIdOrFail($variantData['id']);
                    $query->where('id', '!=', $variantId);
                } else {
                    // No ID sent — exclude SKUs already belonging to THIS product
                    // (they'll be matched by SKU and updated later, not treated as duplicates)
                    $query->where('product_id', '!=', $productId);
                }

                if ($query->exists()) {
                    throw ValidationException::withMessages([
                        "variants.$index.sku" => ["SKU '{$sku}' already exists"]
                    ]);
                }
            }
            // =============================
            // DECODE IDS
            // =============================
            $businessId = decodeIdOrFail($data['business_id']);

            $business = Business::findOrFail($businessId);

            $tableMap = config('product.table_map');
            $categoryId = $business->business_category_id;

            if (!isset($tableMap[$categoryId])) {
                throw new \Exception('Invalid business category mapping');
            }

            $tableName = $tableMap[$categoryId];

            // =============================
            // CHECK PRODUCT
            // =============================
            $product = DB::table($tableName)->where('id', $productId)->first();

            if (!$product) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Product not found'
                ], 404);
            }

            // =============================
            // UPDATE PRODUCT
            // =============================
            DB::table($tableName)->where('id', $productId)->update([
                'name' => $data['name'],
                'status' => $data['status'],
                'updated_at' => now(),
            ]);

            // =============================
            // EXISTING VARIANTS
            // =============================
            $existingVariantIds = ProductVariant::where('product_id', $productId)
                ->pluck('id')
                ->toArray();

            $incomingVariantIds = [];

            foreach ($data['variants'] as $index => $variantData) {

                $sku = $variantData['sku'];

                $manufactureDate = !empty($variantData['manufacture_date'])
                    ? \Carbon\Carbon::parse($variantData['manufacture_date'])->format('Y-m-d')
                    : null;

                $expiryDate = !empty($variantData['expiry_date'])
                    ? \Carbon\Carbon::parse($variantData['expiry_date'])->format('Y-m-d')
                    : null;

                // =============================
                // CHECK SKU BELONGS TO OTHER PRODUCT
                // =============================
                $existingVariantOtherProduct = ProductVariant::where('sku', $sku)
                    ->where('product_id', '!=', $productId)
                    ->first();

                if ($existingVariantOtherProduct) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "variants.$index.sku" => ["SKU '{$sku}' already exists for another product"]
                    ]);
                }

                // =============================
                // FIND VARIANT IN SAME PRODUCT
                // =============================
                $variant = ProductVariant::where('product_id', $productId)
                    ->where('sku', $sku)
                    ->first();

                // =============================
                // UPDATE (if exists)
                // =============================
                if ($variant) {

                    $variant->update([
                        'barcode' => $variantData['barcode'] ?? null,
                        'mrp' => $variantData['mrp'],
                        'cost_price' => $variantData['cost_price'] ?? null,
                        'selling_price' => $variantData['selling_price'] ?? null,
                        'discount' => $variantData['discount'] ?? 0,
                        'final_price' => $variantData['final_price'],
                        'short_description' => $variantData['short_description'] ?? null,
                        'long_description' => $variantData['long_description'] ?? null,
                        'manufacture_date' => $manufactureDate,
                        'expiry_date' => $expiryDate,
                        'is_primary' => !empty($variantData['is_primary']) ? true : false,
                    ]);
                }

                // =============================
                // CREATE (if not exists)
                // =============================
                else {

                    $variant = ProductVariant::create([
                        'product_id' => $productId,
                        'product_type' => $categoryId,
                        'sku' => $sku,
                        'barcode' => $variantData['barcode'] ?? null,
                        'mrp' => $variantData['mrp'],
                        'cost_price' => $variantData['cost_price'] ?? null,
                        'selling_price' => $variantData['selling_price'] ?? null,
                        'discount' => $variantData['discount'] ?? 0,
                        'final_price' => $variantData['final_price'],
                        'short_description' => $variantData['short_description'] ?? null,
                        'long_description' => $variantData['long_description'] ?? null,
                        'variant_status' => $variantData['variant_status'] ?? 0,
                        'manufacture_date' => $manufactureDate,
                        'expiry_date' => $expiryDate,
                        'is_primary' => !empty($variantData['is_primary']) ? true : false,
                    ]);
                }

                $incomingVariantIds[] = $variant->id;

                // =============================
                // STOCK
                // =============================
                ProductVendorStock::updateOrCreate(
                    [
                        'product_variant_id' => $variant->id,
                        'business_id' => $business->id,
                    ],
                    [
                        'stock' => $variantData['stock'] ?? 0,
                    ]
                );

                // =============================
                // META
                // =============================
                ProductVariantMeta::updateOrCreate(
                    ['product_variant_id' => $variant->id],
                    [
                        'meta_title' => $variantData['meta_title'] ?? null,
                        'meta_keyword' => $variantData['meta_keyword'] ?? null,
                        'meta_description' => $variantData['meta_description'] ?? null,
                    ]
                );

                // =============================
                // ATTRIBUTES
                // =============================
                DB::table('product_attribute_relations')
                    ->where('product_variant_id', $variant->id)
                    ->delete();

                if (!empty($variantData['attributes'])) {

                    $insertData = [];

                    foreach ($variantData['attributes'] as $attr) {
                        $insertData[] = [
                            'product_variant_id' => $variant->id,
                            'attribute_id' => decodeIdOrFail($attr['attribute_id']),
                            'attribute_value_id' => decodeIdOrFail($attr['attribute_value_id']),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    DB::table('product_attribute_relations')->insert($insertData);
                }

                // =============================
                // IMAGES
                // =============================
                if ($request->hasFile("variants.$index.images")) {

                    $manager = new ImageManager(new Driver());

                    foreach ($request->file("variants.$index.images") as $file) {

                        $filename = time() . '_' . uniqid();

                        // LARGE
                        $large = $manager->read($file)->cover(600, 600);

                        Storage::disk('public')->put(
                            "products/large/{$filename}.webp",
                            compressToTargetSize($large, 30)
                        );

                        // MEDIUM
                        $medium = $manager->read($file)->cover(150, 150);

                        Storage::disk('public')->put(
                            "products/medium/{$filename}.webp",
                            compressToTargetSize($medium, 25)
                        );

                        // SMALL
                        $small = $manager->read($file)->cover(40, 40);

                        Storage::disk('public')->put(
                            "products/small/{$filename}.webp",
                            compressToTargetSize($small, 15)
                        );

                        // SAVE IMAGE
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

            // =============================
            // DELETE REMOVED VARIANTS
            // =============================
            $toDelete = array_diff($existingVariantIds, $incomingVariantIds);

            if (!empty($toDelete)) {
                ProductVariant::whereIn('id', $toDelete)->delete();
            }

            DB::commit();

            // FETCH UPDATED PRODUCT
            $modelMap = config('product.model_map');

            $modelClass = $modelMap[$categoryId];

            $product = $modelClass::with([
                'variants.attributes.attribute',
                'variants.attributes.attributeValue',
                'variants.images',
                'variants.meta',
                'variants.stocks.business'
            ])->find($productId);

            return response()->json([
                'status' => true,
                'data'   => new ProductResource($product)
            ], 200);

        } catch (ValidationException $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Product Update Error: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => config('app.debug') ? $e->getMessage() : null
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
