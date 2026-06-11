<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeMasterResource;
use App\Models\AttributeMaster;
use Illuminate\Http\Request;

class AttributeMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $query = AttributeMaster::query();

            if ($request->filled('business_category_id')) {

                $businessCategoryId = decodeIdOrFail(
                    $request->business_category_id,
                    'Business Category'
                );

                $query->where('business_category_id', $businessCategoryId);
            }

            if ($request->filled('business_sub_category_id')) {

                $businessSubCategoryId = decodeIdOrFail(
                    $request->business_sub_category_id,
                    'Business Sub Category'
                );

                $query->where('business_sub_category_id', $businessSubCategoryId);
            }

            $attributes = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Attribute master list fetched successfully.',
                'data' => AttributeMasterResource::collection($attributes),
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
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
