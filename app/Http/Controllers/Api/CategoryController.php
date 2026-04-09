<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Http\Resources\CategoryResource;
use Vinkla\Hashids\Facades\Hashids;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $categories = $query->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Category list',
            'data' => CategoryResource::collection($categories)
        ]);
    }

    /**
     * POST /api/categories
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable',
            'status' => 'required|in:0,1',
            'commission' => 'nullable|numeric|min:0',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp'
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $imageName = time() . '.' . $file->extension();

            $manager = new ImageManager(new Driver());

            $image = $manager->read($file->getRealPath())
                ->cover(300, 300);

            Storage::disk('public')->put(
                'category/' . $imageName,
                (string) $image->encodeByExtension($file->extension())
            );
        }

        $category = Category::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'commission' => $data['commission'] ?? 0,
            'image' => $imageName
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category created',
            'data' => new CategoryResource($category)
        ], 201);
    }

    /**
     * GET /api/categories/{id}
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * PUT /api/categories/{id}
     */
    public function update(Request $request, string $id)
    {
        // Decode and overwrite $id
        $decoded = Hashids::decode($id);

        if (empty($decoded)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid ID'
            ], 400);
        }

        $id = $decoded[0]; // important
        $category = Category::findOrFail($id);


        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable',
            'status' => 'required|in:0,1',
            'commission' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp'
        ]);

        $imageName = $category->image;

        if ($request->hasFile('image')) {

            if ($category->image && Storage::disk('public')->exists('category/' . $category->image)) {
                Storage::disk('public')->delete('category/' . $category->image);
            }

            $file = $request->file('image');
            $imageName = time() . '.' . $file->extension();

            $manager = new ImageManager(new Driver());

            $image = $manager->read($file->getRealPath())
                ->cover(300, 300);

            Storage::disk('public')->put(
                'category/' . $imageName,
                (string) $image->encodeByExtension($file->extension())
            );

            $data['image'] = $imageName;
        }

        $category->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * DELETE /api/categories/{id}
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        if ($category->image && Storage::disk('public')->exists('category/' . $category->image)) {
            Storage::disk('public')->delete('category/' . $category->image);
        }

        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully'
        ]);
    }

    /**
     * GET /api/categories-dropdown
     */
    public function dropdown()
    {
        $categories = Category::where('status', 1)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $categories
        ]);
    }
}
