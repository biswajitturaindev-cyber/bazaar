<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

class VendorProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($vendor)
    {
        try {
            $vendorId = $vendor;

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
                $data = $model::where('business_id', $vendorId)
                    ->latest()
                    ->get();

                $products = $products->merge($data);
            }

            $products = $products->sortByDesc('created_at')->values();

            $activeCount   = $products->where('status', 1)->count();
            $inactiveCount = $products->where('status', 0)->count();
            $pendingCount  = $products->where('status', 2)->count();

            return view('admin.vendor-products.index', compact(
                'products',
                'vendorId',
                'activeCount',
                'inactiveCount',
                'pendingCount'
            ));

        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
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
