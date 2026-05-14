<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorBannerResource;
use App\Models\VendorBanner;
use Illuminate\Http\Request;

class VendorBannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            // Decode business_id
            $businessId = decodeIdOrFail(
                $request->business_id,
                'Invalid business ID'
            );

            $request->merge([
                'business_id' => $businessId
            ]);

            // Validation
            $data = $request->validate([
                'business_id' => 'required|exists:businesses,id',
                'banner_type' => 'required|in:main_banner,promotional_banner',
                'title' => 'nullable|string|max:255',
                'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
                'status' => 'nullable|boolean',
                'sort_order' => 'nullable|integer',
            ]);

            // Upload image
            if ($request->hasFile('image')) {

                $path = $request->file('image')
                    ->store('vendor_banners', 'public');

                $data['image'] = 'storage/' . $path;
            }

            // Default values
            $data['status'] = $data['status'] ?? true;
            $data['sort_order'] = $data['sort_order'] ?? 0;

            // Save
            $banner = VendorBanner::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Vendor banner created successfully',
                'data' => new VendorBannerResource($banner)
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Failed to create vendor banner',
                'error' => $e->getMessage()
            ], 500);
        }
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
