<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessResource;
use App\Models\Business;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
        // Decode ID using helper
        $businessId = decodeIdOrFail($id, 'Invalid business ID');

        $business = Business::with([
            'category',
            'subCategory',
            'address.latitude,longitude',
            'contact',
            'agreement',
            'bankDetail',
            'kycDetail',
            'user.name,email,vendor_id',
            'operationalDetail'
        ])->find($businessId);

        if (!$business) {
            return response()->json([
                'status' => false,
                'message' => 'Business not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => new BusinessResource($business)
        ]);
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
