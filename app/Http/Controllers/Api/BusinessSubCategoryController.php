<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessSubCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Http\Resources\BusinessSubCategoryResource;
use Vinkla\Hashids\Facades\Hashids;

class BusinessSubCategoryController extends Controller
{
    /**
     * GET /api/business-sub-categories
     */
    public function index(Request $request)
    {
        $query = BusinessSubCategory::with('category');

        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter
        if ($request->business_category_id) {
            $query->where('business_category_id', $request->business_category_id);
        }

        $subcategories = $query->latest()->paginate(10);

        return BusinessSubCategoryResource::collection($subcategories);
    }

    /**
     * POST /api/business-sub-categories
     */
    public function store(Request $request)
    {
        try {
            // Decode business_category_id
            $decoded = Hashids::decode($request->business_category_id);

            if (empty($decoded)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid business category ID'
                ], 400);
            }

            // Replace with real ID
            $request->merge([
                'business_category_id' => $decoded[0]
            ]);

            // Validation (now works)
            $data = $request->validate([
                'business_category_id' => 'required|exists:business_categories,id',
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('business_sub_categories')
                        ->where(fn ($q) => $q->where('business_category_id', $request->business_category_id)),
                ],
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
                'commission' => 'nullable|numeric|min:0',
                'status' => 'required|in:0,1',
            ]);

            $imageName = null;

            if ($request->hasFile('image')) {

                $file = $request->file('image');
                $imageName = time() . '.' . $file->extension();

                $manager = new ImageManager(new Driver());

                $image = $manager->read($file->getRealPath())
                    ->cover(300, 300);

                Storage::disk('public')->put(
                    'business_sub_category/' . $imageName,
                    (string) $image->encodeByExtension($file->extension())
                );
            }

            $subcategory = BusinessSubCategory::create([
                'business_category_id' => $data['business_category_id'],
                'name' => $data['name'],
                'status' => $data['status'],
                'commission' => $data['commission'] ?? 0,
                'image' => $imageName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sub Category Created Successfully',
                'data' => new BusinessSubCategoryResource($subcategory->load('category'))
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/business-sub-categories/{id}
     */
    public function show($id)
    {
        try {

            // Decode ID
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid sub category ID'
                ], 400);
            }

            $id = $decoded[0]; // overwrite

            $subcategory = BusinessSubCategory::with('category')->findOrFail($id);

            return new BusinessSubCategoryResource($subcategory);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Sub Category not found'
            ], 404);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT /api/business-sub-categories/{id}
     */
    public function update(Request $request, $id)
    {
        try {

            // Decode subcategory ID
            $decodedId = Hashids::decode($id);

            if (empty($decodedId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid sub category ID'
                ], 400);
            }

            $id = $decodedId[0]; // overwrite

            $subcategory = BusinessSubCategory::findOrFail($id);

            // Decode business_category_id
            $decodedCategory = Hashids::decode($request->business_category_id);

            if (empty($decodedCategory)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid business category ID'
                ], 400);
            }

            // Replace with real ID
            $request->merge([
                'business_category_id' => $decodedCategory[0]
            ]);

            // Validation
            $data = $request->validate([
                'business_category_id' => 'required|exists:business_categories,id',
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
                'commission' => 'nullable|numeric|min:0',
                'status' => 'required|in:0,1',
            ]);

            $imageName = $subcategory->image;

            if ($request->hasFile('image')) {

                if ($subcategory->image && Storage::disk('public')->exists('business_sub_category/'.$subcategory->image)) {
                    Storage::disk('public')->delete('business_sub_category/'.$subcategory->image);
                }

                $file = $request->file('image');
                $imageName = time() . '.' . $file->extension();

                $manager = new ImageManager(new Driver());

                $image = $manager->read($file->getRealPath())
                    ->cover(300, 300);

                Storage::disk('public')->put(
                    'business_sub_category/' . $imageName,
                    (string) $image->encodeByExtension($file->extension())
                );
            }

            $subcategory->update([
                'business_category_id' => $data['business_category_id'],
                'name' => $data['name'],
                'status' => $data['status'],
                'commission'=> $data['commission'] ?? 0,
                'image' => $imageName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sub Category Updated Successfully',
                'data' => new BusinessSubCategoryResource($subcategory->load('category'))
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/business-sub-categories/{id}
     */
    public function destroy($id)
    {
        try {

            // Decode ID
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid sub category ID'
                ], 400);
            }

            $id = $decoded[0]; // overwrite

            $subcategory = BusinessSubCategory::findOrFail($id);

            // 🗑 Delete image if exists
            if ($subcategory->image && Storage::disk('public')->exists('business_sub_category/'.$subcategory->image)) {
                Storage::disk('public')->delete('business_sub_category/'.$subcategory->image);
            }

            $subcategory->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sub Category Deleted Successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Sub Category not found'
            ], 404);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
