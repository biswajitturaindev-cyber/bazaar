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
            $products = ProductReview::with([
                'category',
                'subCategory',
                'subSubCategory',
                'hsn',
                'productAttributes'
            ])->latest()->get();

            return view('admin.product-reviews.index', compact('products'));

        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
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
                'status' => 'required|in:1,2',
            ]);

            $product = ProductReview::with('productAttributes')->findOrFail($id);

            // FIX: before transaction
            if ($product->status == 1) {
                return back()->with('error', 'Product already approved.');
            }

            DB::beginTransaction();

            $product->status = $request->status;
            $product->save();

            if ($request->status == 1) {

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
                    throw new \Exception('Invalid business category mapping');
                }

                $modelClass = $tableMap[$categoryId];

                // Prevent duplicate SKU
                if ($modelClass::where('sku', $product->sku)->exists()) {
                    throw new \Exception('Product with same SKU already exists.');
                }

                // Create Product
                $newProduct = $modelClass::create([
                    'business_id' => $product->business_id,
                    'business_category_id' => $product->business_category_id,
                    'business_sub_category_id' => $product->business_sub_category_id,
                    'category_id' => $product->category_id,
                    'sub_category_id' => $product->sub_category_id,
                    'sub_sub_category_id' => $product->sub_sub_category_id,
                    'sku' => $product->sku,
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
                    'status' => 1,
                ]);

                // BEST: Use relationship (no manual product_type)
                foreach ($product->productAttributes as $attr) {
                    $newProduct->attributes()->create([
                        'attribute_id' => $attr->attribute_id,
                        'attribute_value_id' => $attr->attribute_value_id,
                        'stock' => $attr->stock,
                        'price' => $attr->price,
                    ]);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Status updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Error: ' . $e->getMessage());
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
}
