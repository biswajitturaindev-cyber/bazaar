<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessCategoryMapping;
use App\Http\Resources\BusinessCategoryMappingResource;
use Illuminate\Validation\Rule;

class BusinessCategoryMappingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = BusinessCategoryMapping::with([
                'businessCategory',
                'businessSubCategory',
                'category'
            ])->latest()->paginate(10);

            return BusinessCategoryMappingResource::collection($data);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch mappings',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'business_category_id' => 'required|exists:business_categories,id',
            'business_sub_category_id' => [
                'required',
                Rule::exists('business_sub_categories', 'id')
                    ->where('business_category_id', $request->business_category_id),
            ],

            // single value
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:0,1',
        ]);

        // Check duplicate
        $exists = BusinessCategoryMapping::where([
            'business_category_id' => $data['business_category_id'],
            'business_sub_category_id' => $data['business_sub_category_id'],
            'category_id' => $data['category_id'],
        ])->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Mapping already exists'
            ]);
        }

        // Create
        $mapping = BusinessCategoryMapping::create($data);
        return response()->json([
            'status' => true,
            'message' => 'Mapping created successfully',
            'data' => new BusinessCategoryMappingResource($mapping)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mapping = BusinessCategoryMapping::with([
            'businessCategory',
            'businessSubCategory',
            'category'
        ])->findOrFail($id);

        return new BusinessCategoryMappingResource($mapping);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $mapping = BusinessCategoryMapping::findOrFail($id);

        $data = $request->validate([
            'business_category_id' => 'required|exists:business_categories,id',
            'business_sub_category_id' => 'required|exists:business_sub_categories,id',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:0,1',
        ]);

        $mapping->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Mapping updated successfully',
            'data' => new BusinessCategoryMappingResource($mapping)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $mapping = BusinessCategoryMapping::findOrFail($id);
            $mapping->delete();

            return response()->json([
                'status' => true,
                'message' => 'Mapping deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Mapping not found'
            ], 404);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() // optional (remove in production)
            ], 500);
        }
    }
}
