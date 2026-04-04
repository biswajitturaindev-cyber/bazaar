<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use Illuminate\Validation\Rule;
use App\Http\Resources\SubCategoryResource;
use Vinkla\Hashids\Facades\Hashids;

class SubCategoryController extends Controller
{
    /**
     * GET /api/sub-categories
     */
    public function index(Request $request)
    {
        $query = SubCategory::query();

        // Filter by category
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $subcategories = $query->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'SubCategory list',
            'data' => SubCategoryResource::collection($subcategories)
        ]);
    }

    /**
     * POST /api/sub-categories
     */
    public function store(Request $request)
    {
        try {

            // Decode category_id first
            $decoded = Hashids::decode($request->category_id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid category ID'
                ], 400);
            }

            // Replace with real ID
            $request->merge([
                'category_id' => $decoded[0]
            ]);

            // Now validation works
            $data = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255|unique:sub_categories,name',
                'description' => 'nullable|string',
                'status' => 'required|in:0,1'
            ]);

            $sub = SubCategory::create($data);

            return response()->json([
                'status' => true,
                'message' => 'SubCategory created',
                'data' => new SubCategoryResource($sub)
            ], 201);

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
     * GET /api/sub-categories/{id}
     */
    public function show(string $id)
    {
        try {

            // Decode and overwrite $id
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid ID'
                ], 400);
            }

            $id = $decoded[0]; // important

            $sub = SubCategory::findOrFail($id);

            return response()->json([
                'status' => true,
                'data' => new SubCategoryResource($sub)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'SubCategory not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * PUT /api/sub-categories/{id}
     */
    public function update(Request $request, string $id)
    {
        try {

            // Decode ID (SubCategory)
            $decodedId = Hashids::decode($id);

            if (empty($decodedId)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid SubCategory ID'
                ], 400);
            }

            $id = $decodedId[0]; // overwrite

            $sub = SubCategory::findOrFail($id);

            // Decode category_id
            $decodedCategory = Hashids::decode($request->category_id);

            if (empty($decodedCategory)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid category ID'
                ], 400);
            }

            $request->merge([
                'category_id' => $decodedCategory[0]
            ]);

            // Validation (now works)
            $data = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('sub_categories', 'name')->ignore($id)
                ],
                'description' => 'nullable|string',
                'status' => 'required|in:0,1'
            ]);

            $sub->update($data);

            return response()->json([
                'status' => true,
                'message' => 'SubCategory updated',
                'data' => new SubCategoryResource($sub)
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Failed to update SubCategory',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/sub-categories/{id}
     */
    public function destroy(string $id)
    {
        SubCategory::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'SubCategory deleted'
        ]);
    }

    /**
     * GET /api/sub-categories-dropdown
     */
    public function dropdown($category_id = null)
    {
        try {

            $query = SubCategory::where('status', 1);

            // Decode if category_id is passed
            if (!is_null($category_id)) {

                $decoded = Hashids::decode($category_id);

                if (empty($decoded)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid category ID'
                    ], 400);
                }

                $category_id = $decoded[0]; // overwrite

                $query->where('category_id', $category_id);
            }

            $subcategories = $query->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $subcategories
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch subcategories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
