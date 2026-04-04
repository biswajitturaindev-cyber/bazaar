<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessCategoryMapping;
use App\Http\Resources\BusinessCategoryMappingResource;
use Illuminate\Validation\Rule;
use Vinkla\Hashids\Facades\Hashids;

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
        try {

            // Decode business_category_id
            $decodedBusinessCategory = Hashids::decode($request->business_category_id);
            if (empty($decodedBusinessCategory)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid business category ID'
                ], 400);
            }

            // Decode business_sub_category_id
            $decodedBusinessSubCategory = Hashids::decode($request->business_sub_category_id);
            if (empty($decodedBusinessSubCategory)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid business sub category ID'
                ], 400);
            }

            // Decode category_id
            $decodedCategory = Hashids::decode($request->category_id);
            if (empty($decodedCategory)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid category ID'
                ], 400);
            }

            // Replace with real IDs
            $request->merge([
                'business_category_id' => $decodedBusinessCategory[0],
                'business_sub_category_id' => $decodedBusinessSubCategory[0],
                'category_id' => $decodedCategory[0],
            ]);

            // Validation
            $data = $request->validate([
                'business_category_id' => 'required|exists:business_categories,id',
                'business_sub_category_id' => [
                    'required',
                    Rule::exists('business_sub_categories', 'id')
                        ->where('business_category_id', $request->business_category_id),
                ],
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

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            // Decode ID
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid mapping ID'
                ], 400);
            }

            $id = $decoded[0]; // overwrite

            $mapping = BusinessCategoryMapping::with([
                'businessCategory',
                'businessSubCategory',
                'category'
            ])->findOrFail($id);

            return new BusinessCategoryMappingResource($mapping);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Mapping not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            // Decode mapping ID
            $decodedId = Hashids::decode($id);

            if (empty($decodedId)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid mapping ID'
                ], 400);
            }

            $id = $decodedId[0]; // overwrite

            $mapping = BusinessCategoryMapping::findOrFail($id);

            // Decode business_category_id
            $decodedBusinessCategory = Hashids::decode($request->business_category_id);
            if (empty($decodedBusinessCategory)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid business category ID'
                ], 400);
            }

            // Decode business_sub_category_id
            $decodedBusinessSub = Hashids::decode($request->business_sub_category_id);
            if (empty($decodedBusinessSub)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid business sub category ID'
                ], 400);
            }

            // Decode category_id
            $decodedCategory = Hashids::decode($request->category_id);
            if (empty($decodedCategory)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid category ID'
                ], 400);
            }

            // Replace with real IDs
            $request->merge([
                'business_category_id' => $decodedBusinessCategory[0],
                'business_sub_category_id' => $decodedBusinessSub[0],
                'category_id' => $decodedCategory[0],
            ]);

            // Validation
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

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Failed to update mapping',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            // Decode ID
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid mapping ID'
                ], 400);
            }

            $id = $decoded[0]; // overwrite

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
                'error' => $e->getMessage() // remove in production
            ], 500);
        }
    }
}
