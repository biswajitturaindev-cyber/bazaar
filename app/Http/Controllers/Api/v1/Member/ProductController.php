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
            // Vendor from auth (no need to pass)
            //$vendorId = auth()->user()->id;

            // Decode filters
            $businessCategoryId = $request->filled('business_category_id')
                ? decodeIdOrFail($request->business_category_id, 'Invalid business category ID')
                : null;

            $businessSubCategoryId = $request->filled('business_sub_category_id')
                ? decodeIdOrFail($request->business_sub_category_id, 'Invalid business sub category ID')
                : null;

            $categoryId = $request->filled('category_id')
                ? decodeIdOrFail($request->category_id, 'Invalid category ID')
                : null;

            $tableMap = [
                ProductFoodBeverages::class,
                ProductConstructionHardware::class,
                ProductHomeLiving::class,
                ProductFashionLifestyle::class,
                ProductAutomobile::class,
                EducationStationary::class,
                ProductAgriculture::class,
                ProductRetail::class,
                ProductHealth::class,
                ProductSports::class,
            ];

            $products = collect();

            foreach ($tableMap as $model) {

                $query = $model::query();

                // Vendor filter
                //$query->where('business_id', $vendorId);

                // Apply filters
                if ($businessCategoryId) {
                    $query->where('business_category_id', $businessCategoryId);
                }

                if ($businessSubCategoryId) {
                    $query->where('business_sub_category_id', $businessSubCategoryId);
                }

                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }

                $products = $products->merge($query->with('attributes')->get());
            }

            // Sort latest
            $products = $products->sortByDesc('created_at')->values();

            return MemberProductResource::collection($products);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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
}
