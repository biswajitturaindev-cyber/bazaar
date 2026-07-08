<?php

namespace App\Http\Controllers\Api\v1\Member;

use App\Http\Controllers\Controller;
use App\Models\VendorBanner;
use Illuminate\Http\Request;
use App\Http\Resources\VendorBannerResource;

class VendorBannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        try {

            // Decode business id
            $businessId = decodeIdOrFail(
                $request->business_id,
                'Invalid business ID'
            );

            // Get banners
            $banners = VendorBanner::where('business_id', $businessId)
                ->where('status', 1)
                ->when($request->banner_type, function ($query) use ($request) {
                    $query->where('banner_type', $request->banner_type);
                })
                ->orderBy('sort_order', 'asc')
                ->latest()
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Vendor banner list fetched successfully',
                'data' => VendorBannerResource::collection($banners)
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
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
