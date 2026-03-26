<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use Illuminate\Validation\Rule;
use App\Http\Resources\SubCategoryResource;

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
    }

    /**
     * GET /api/sub-categories/{id}
     */
    public function show(string $id)
    {
        $sub = SubCategory::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => new SubCategoryResource($sub)
        ]);
    }

    /**
     * PUT /api/sub-categories/{id}
     */
    public function update(Request $request, string $id)
    {
        $sub = SubCategory::findOrFail($id);

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
    public function dropdown()
    {
        $subcategories = SubCategory::where('status', 1)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $subcategories
        ]);
    }
}
