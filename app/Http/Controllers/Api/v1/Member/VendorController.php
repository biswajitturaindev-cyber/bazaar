<?php

namespace App\Http\Controllers\Api\v1\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessResource;
use App\Models\Business;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Business::with([
            'category',
            'subCategory',
            //'address',
            //'contact',
            //'agreement',
            //'bankDetail',
            'kycDetail',
            'user',
            //'operationalDetail'
        ]);

        // Filter by category
        if ($request->filled('business_category_id')) {

            $decoded = decodeIdOrFail($request->business_category_id, 'Invalid category ID');

            $query->where('business_category_id', $decoded);
        }

        // Filter by subcategory
        if ($request->filled('business_sub_category_id')) {

            $decoded = decodeIdOrFail($request->business_sub_category_id, 'Invalid subcategory ID');

            $query->where('business_sub_category_id', $decoded);
        }

        $businesses = $query->latest()->paginate($request->per_page ?? 10);

        return BusinessResource::collection($businesses);
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
