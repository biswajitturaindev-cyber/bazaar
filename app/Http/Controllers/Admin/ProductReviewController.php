<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use App\Models\BusinessSubCategory;
use App\Models\Category;
use App\Models\EducationStationary;
use App\Models\Hsn;
use App\Models\ProductAgriculture;
use App\Models\ProductAttributeValue;
use App\Models\ProductAutomobile;
use App\Models\ProductBusinessCategoryAttributeValue;
use App\Models\ProductConstructionHardware;
use App\Models\ProductFashionLifestyle;
use App\Models\ProductFoodBeverages;
use App\Models\ProductHealth;
use App\Models\ProductHomeLiving;
use App\Models\ProductRetail;
use App\Models\ProductReview;
use App\Models\ProductSports;
use App\Models\SubCategory;
use App\Models\SubCategoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductReviewController extends Controller
{
    /**
     * Display a listing of the resource.
    */
    public function index()
    {
        try {

            $modelMap = config('product.model_map');

            $allProducts = collect();

            foreach ($modelMap as $type => $modelClass) {

                $products = $modelClass::query()
                    ->select([
                        'id',
                        'name',
                        'business_id',
                        'category_id',
                        'sub_category_id',
                        'sub_sub_category_id',
                        'hsn_id',
                        'commission',
                        'vendor_commission',
                        'vendor_commission_approval_status',
                        'status',
                        'created_at'
                    ])
                    ->where('status', 2)
                    ->orWhere('vendor_commission_approval_status', 0) // Only pending commission approvals
                    ->with([
                        'business:id,business_name',
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
                                        'attribute:id,name',
                                        'attributeValue:id,value'
                                    ]);
                                },

                                'images' => function ($img) {

                                    $img->select([
                                        'id',
                                        'product_variant_id',
                                        'image_medium'
                                    ])
                                    ->orderBy('id')
                                    ->limit(1);
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

                $allProducts = $allProducts->merge($products);
            }

            $products = $allProducts
                ->sortByDesc('created_at')
                ->values();



            return view(
                'admin.product-reviews.index',
                compact('products')
            );

        } catch (\Exception $e) {

            \Log::error(
                'Product Index Error',
                [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ]
            );

            return back()->with(
                'error',
                'Something went wrong. Please try again.'
            );
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
    // public function show($id)
    // {

    //     try {

    //         $modelMap = config('product.model_map');

    //         $product = null;

    //         foreach ($modelMap as $type => $modelClass) {

    //             $product = $modelClass::with([

    //                 'category',
    //                 'subCategory',
    //                 'subSubCategory',
    //                 'hsn',

    //                 'variants.meta',

    //                 'variants.attributes.attribute',
    //                 'variants.attributes.attributeValue',

    //                 'variants.images',

    //                 'variants.stocks.business'

    //             ])->find($id);

    //             if ($product) {
    //                 break;
    //             }
    //         }

    //         if (!$product) {

    //             return redirect()
    //                 ->back()
    //                 ->with('error', 'Product not found');
    //         }

    //         return view(
    //             'admin.product-reviews.show',
    //             compact('product')
    //         );

    //     } catch (\Exception $e) {

    //         \Log::error(
    //             'Product Show Error: ' . $e->getMessage()
    //         );

    //         return redirect()
    //             ->back()
    //             ->with(
    //                 'error',
    //                 'Something went wrong'
    //             );
    //     }
    // }

    public function show(Request $request, $id)
    {
        try {

            $businessId = $request->business_id;

            $modelMap = config('product.model_map');

            $product = null;

            foreach ($modelMap as $type => $modelClass) {

                $product = $modelClass::with([
                    'category',
                    'subCategory',
                    'subSubCategory',
                    'hsn',
                    'variants.meta',
                    'variants.attributes.attribute',
                    'variants.attributes.attributeValue',
                    'variants.images',
                    'variants.stocks.business'
                ])
                ->where('business_id', $businessId) // optional filter
                ->find($id);

                if ($product) {
                    break;
                }
            }

            if (!$product) {

                return redirect()
                    ->back()
                    ->with('error', 'Product not found');
            }

            return view(
                'admin.product-reviews.show',
                compact('product', 'businessId')
            );

        } catch (\Exception $e) {

            \Log::error(
                'Product Show Error: ' . $e->getMessage()
            );

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Something went wrong'
                );
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $product = ProductReview::with([
                'businessCategory',
                'businessSubCategory',
                'category',
                'subCategory',
                'subSubCategory',
                'hsn',
                'productAttributes'
            ])->findOrFail($id);

            $businessCategories = BusinessCategory::all();

            $businessSubCategories = BusinessSubCategory::where(
                'business_category_id',
                $product->business_category_id
            )->get();

            $categories = Category::get();

            $subCategories = SubCategory::where(
                'category_id',
                $product->category_id
            )->get();

            $subSubCategories = SubCategoryItem::where(
                'sub_category_id',
                $product->sub_category_id
            )->get();

            $hsns = Hsn::all();

            return view('admin.product-reviews.edit', compact(
                'product',
                'businessCategories',
                'businessSubCategories',
                'categories',
                'subCategories',
                'subSubCategories',
                'hsns'
            ));

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $request->validate([
                'status' => 'required|integer|in:0,1,2',
                'commission' => 'nullable|numeric|min:0|max:100',
                'vendor_commission' => 'nullable|numeric|min:0|max:100',
                'vendor_commission_approval_status' => 'required|integer|in:0,1,2',
            ]);

            $product = ProductReview::with('productAttributes')->findOrFail($id);

            $oldStatus = $product->status;

            DB::beginTransaction();



            // Update review product
            $product->update([
                'status' => $request->status,
                'commission' => $request->commission ?? 0,
                'vendor_commission' => $request->vendor_commission ?? 0,
                'vendor_commission_approval_status' => $request->vendor_commission_approval_status,
            ]);

            // Approve Product
            if ($oldStatus != 1 && $request->status == 1) {

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

                $categoryId = $product->business_category_id;

                if (!isset($tableMap[$categoryId])) {
                    throw new \Exception('Invalid business category mapping.');
                }

                $modelClass = $tableMap[$categoryId];

                $sku = $product->sku
                    ? strtolower(trim($product->sku))
                    : null;

                try {

                    $newProduct = $modelClass::create([
                        'business_id' => $product->business_id,
                        'business_category_id' => $product->business_category_id,
                        'business_sub_category_id' => $product->business_sub_category_id,

                        'category_id' => $product->category_id,
                        'sub_category_id' => $product->sub_category_id,
                        'sub_sub_category_id' => $product->sub_sub_category_id,

                        'sku' => $sku,

                        'hsn_id' => $product->hsn_id,

                        'name' => $product->name,
                        'image' => $product->image,
                        'description' => $product->description,

                        'mrp' => $product->mrp,
                        'cost_price' => $product->cost_price,
                        'selling_price' => $product->selling_price,
                        'discount' => $product->discount,
                        'final_price' => $product->final_price,

                        'manufacture_date' => $product->manufacture_date,
                        'expiry_date' => $product->expiry_date,

                        'commission' => $product->commission,
                        'vendor_commission' => $product->vendor_commission,
                        'vendor_commission_approval_status' => $product->vendor_commission_approval_status,

                        'status' => 1,
                    ]);

                } catch (\Illuminate\Database\QueryException $e) {

                    if ($e->getCode() == 23000) {
                        throw new \Exception(
                            'Duplicate product: same combination already exists.'
                        );
                    }

                    throw $e;
                }

                // Copy Product Attributes
                foreach ($product->productAttributes as $attr) {

                    $newProduct->attributes()->create([
                        'attribute_id' => $attr->attribute_id,
                        'attribute_value_id' => $attr->attribute_value_id,
                        'stock' => $attr->stock,
                        'price' => $attr->price,
                    ]);
                }

                // Remove review record after successful approval
                $product->delete();
            }

            DB::commit();

            $message = match ((int) $request->status) {
                1 => 'Product approved successfully.',
                0 => 'Product rejected successfully.',
                2 => 'Product marked as pending successfully.',
                default => 'Product updated successfully.',
            };

            return redirect('admin/product-reviews')
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {

            DB::rollBack();

            \Log::error('Product Review Update Error', [
                'product_id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = ProductReview::findOrFail($id);
            $product->delete();

            return redirect()->back()->with('success', 'Product deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $type, $id)
    {
        try {

            $request->validate([
                'status' => 'required|integer|in:0,1,2',
                'commission' => 'nullable|numeric|min:0|max:100',
                'vendor_commission' => 'nullable|numeric|min:0|max:100',
                'vendor_commission_approval_status' => 'required|integer|in:0,1,2',
            ]);

            $modelMap = config('product.model_map');

            if (!isset($modelMap[$type])) {

                return back()->with(
                    'error',
                    'Invalid product type.'
                );
            }

            // Product cannot be approved unless commission is approved
            if (
                $request->status == 1 &&
                $request->vendor_commission_approval_status != 1
            ) {
                return back()->with(
                    'error',
                    'Please approve Vendor Commission first.'
                );
            }

            $modelClass = $modelMap[$type];
            $product = $modelClass::findOrFail($id);
            $product->status = $request->status;
            $product->commission = $request->commission;
            $product->vendor_commission = $request->vendor_commission;
            $product->vendor_commission_approval_status = $request->vendor_commission_approval_status;

            $product->save();

            return back()->with(
                'success',
                'Product status updated successfully.'
            );

        } catch (\Exception $e) {

            \Log::error(
                'Product Status Update Error: ' . $e->getMessage()
            );

            return back()->with(
                'error',
                'Something went wrong.'
            );
        }
    }
}
