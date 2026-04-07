<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Business;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class UserController extends Controller
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

    /**
     * Remove the specified resource from storage.
     */
    // public function checkGst(Request $request)
    // {
    //     try {
    //         // Validate
    //         $request->validate([
    //             'user_id' => 'required|integer'
    //         ]);

    //         // Directly check GST existence (optimized query)
    //         $exists = Business::where('user_id', $request->user_id)
    //             ->whereNotNull('gst_number')
    //             ->where('gst_number', '!=', '')
    //             ->exists();

    //         return response()->json([
    //             'status' => true,
    //             'exists' => $exists,
    //             'message' => $exists
    //                 ? 'GST already registered'
    //                 : 'GST not registered'
    //         ]);

    //     } catch (\Illuminate\Validation\ValidationException $e) {

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation error',
    //             'errors' => $e->errors()
    //         ], 422);

    //     } catch (\Exception $e) {

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Update the specified resource in storage.
     */
    public function checkGst(Request $request)
    {
        try {

            // Decode user_id FIRST
            $decoded = Hashids::decode($request->user_id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid user ID'
                ], 400);
            }

            // Replace with real ID
            $request->merge([
                'user_id' => $decoded[0]
            ]);

            // Now validate
            $request->validate([
                'user_id' => 'required|integer|exists:users,id'
            ]);

            // Query
            $exists = Business::where('user_id', $request->user_id)
                ->whereNotNull('gst_number')
                ->where('gst_number', '!=', '')
                ->exists();

            return response()->json([
                'status' => true,
                'exists' => $exists,
                'message' => $exists
                    ? 'GST already registered'
                    : 'GST not registered'
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
     * Get Category Dropdown as per Businss Category
     */
    public function CategoryDropdown(Request $request)
    {
        try {
            // Decode ID
            $decoded = Hashids::decode($request->business_category_id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid business category ID'
                ], 400);
            }

            $businessCategoryId = $decoded[0];

            // Validation
            $request->merge(['business_category_id' => $businessCategoryId]);

            $request->validate([
                'business_category_id' => 'required|exists:business_categories,id'
            ]);

            // Fetch categories
            $categories = BusinessCategory::findOrFail($businessCategoryId)
                ->categories()
                ->select('categories.id', 'categories.name')
                ->distinct()
                ->get();

            // RETURN WITH RESOURCE
            return response()->json([
                'status' => true,
                'data' => CategoryResource::collection($categories)
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


}
