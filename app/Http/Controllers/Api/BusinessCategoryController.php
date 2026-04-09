<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Http\Resources\BusinessCategoryResource;
use Vinkla\Hashids\Facades\Hashids;

class BusinessCategoryController extends Controller
{
    /**
     * GET /api/business-categories
     */
    public function index()
    {
        $categories = BusinessCategory::latest()->paginate(10);
        return BusinessCategoryResource::collection($categories);
    }

    /**
     * POST /api/business-categories
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => 'required|unique:business_categories,name',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'status' => 'required|in:0,1'
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $imageName = time() . '.' . $file->extension();

            $manager = new ImageManager(new Driver());

            $image = $manager->read($file->getRealPath())
                ->cover(300, 300);

            Storage::disk('public')->put(
                'business_category/' . $imageName,
                (string) $image->encodeByExtension($file->extension())
            );
        }

        $category = BusinessCategory::create([
            'name' => $data['name'],
            'status' => $data['status'],
            'image' => $imageName
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category Created Successfully',
            'data' => new BusinessCategoryResource($category)
        ], 201);
    }

    /**
     * GET /api/business-categories/{id}
     */
    public function show($id)
    {
        try {

            // Decode ID
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category ID'
                ], 400);
            }

            $id = $decoded[0]; // overwrite

            $category = BusinessCategory::findOrFail($id);

            return new BusinessCategoryResource($category);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Category not found'
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
     * PUT /api/business-categories/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            // Decode ID
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category ID'
                ], 400);
            }

            $id = $decoded[0]; // overwrite

            $category = BusinessCategory::findOrFail($id);

            // Validation (use decoded ID)
            $data = $request->validate([
                'name'   => 'required|unique:business_categories,name,' . $id,
                'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp',
                'status' => 'required|in:0,1'
            ]);

            $imageName = $category->image;

            if ($request->hasFile('image')) {

                if ($category->image && Storage::disk('public')->exists('business_category/' . $category->image)) {
                    Storage::disk('public')->delete('business_category/' . $category->image);
                }

                $file = $request->file('image');
                $imageName = time() . '.' . $file->extension();

                $manager = new ImageManager(new Driver());

                $image = $manager->read($file->getRealPath())
                    ->cover(300, 300);

                Storage::disk('public')->put(
                    'business_category/' . $imageName,
                    (string) $image->encodeByExtension($file->extension())
                );
            }

            $category->update([
                'name'   => $data['name'],
                'status' => $data['status'],
                'image'  => $imageName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category Updated Successfully',
                'data' => new BusinessCategoryResource($category)
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
     * DELETE /api/business-categories/{id}
     */
    public function destroy($id)
    {
        try {
            // Decode ID
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category ID'
                ], 400);
            }

            $id = $decoded[0]; // overwrite

            $category = BusinessCategory::findOrFail($id);

            // Delete image if exists
            if ($category->image && Storage::disk('public')->exists('business_category/' . $category->image)) {
                Storage::disk('public')->delete('business_category/' . $category->image);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category Deleted Successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() // remove in production
            ], 500);
        }
    }
}
