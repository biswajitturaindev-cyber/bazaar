<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use App\Models\BusinessSubCategory;
use App\Models\Category;
use App\Models\Hsn;
use App\Models\ProductReview;
use App\Models\SubCategory;
use App\Models\SubCategoryItem;
use Illuminate\Http\Request;

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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
