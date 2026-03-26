<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubCategoryItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Http\Resources\SubCategoryItemResource;

class SubCategoryItemController extends Controller
{
    /**
     * GET /api/sub-category-items
     */
    public function index(Request $request)
    {
        $query = SubCategoryItem::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->sub_category_id) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Sub Category Item list',
            'data' => SubCategoryItemResource::collection($items)
        ]);
    }

    /**
     * POST /api/sub-category-items
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'sub_category_id'  => 'required|exists:sub_categories,id',
            'name'             => 'required|string|max:255|unique:sub_category_items,name',
            'description'      => 'nullable',
            'status'           => 'required|in:0,1',
            'image'            => 'required|image|mimes:jpg,jpeg,png,webp'
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $imageName = time() . '.' . $file->extension();

            $manager = new ImageManager(new Driver());

            $image = $manager->read($file->getRealPath())
                ->cover(300, 300);

            Storage::disk('public')->put(
                'subcategoryitem/' . $imageName,
                (string) $image->encodeByExtension($file->extension())
            );
        }

        $item = SubCategoryItem::create([
            'category_id'     => $data['category_id'],
            'sub_category_id' => $data['sub_category_id'],
            'name'            => $data['name'],
            'description'     => $data['description'] ?? null,
            'status'          => $data['status'],
            'image'           => $imageName
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Sub Category Item created',
            'data' => new SubCategoryItemResource($item)
        ], 201);
    }

    /**
     * GET /api/sub-category-items/{id}
     */
    public function show(string $id)
    {
        $item = SubCategoryItem::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => new SubCategoryItemResource($item)
        ]);
    }

    /**
     * PUT /api/sub-category-items/{id}
     */
    public function update(Request $request, string $id)
    {
        $item = SubCategoryItem::findOrFail($id);

        $data = $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'sub_category_id'  => 'required|exists:sub_categories,id',

            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sub_category_items')
                    ->where(fn ($q) => $q->where('sub_category_id', $request->sub_category_id))
                    ->ignore($id),
            ],

            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
            'status'      => 'required|in:0,1',
        ]);

        $imageName = $item->image;

        if ($request->hasFile('image')) {

            if ($item->image && Storage::disk('public')->exists('subcategoryitem/' . $item->image)) {
                Storage::disk('public')->delete('subcategoryitem/' . $item->image);
            }

            $file = $request->file('image');
            $imageName = time() . '.' . $file->extension();

            $manager = new ImageManager(new Driver());

            $image = $manager->read($file->getRealPath())
                ->cover(300, 300);

            Storage::disk('public')->put(
                'subcategoryitem/' . $imageName,
                (string) $image->encodeByExtension($file->extension())
            );

            $data['image'] = $imageName;
        }

        $item->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Sub Category Item updated successfully',
            'data' => new SubCategoryItemResource($item)
        ]);
    }

    /**
     * DELETE /api/sub-category-items/{id}
     */
    public function destroy(string $id)
    {
        $item = SubCategoryItem::findOrFail($id);

        if ($item->image && Storage::disk('public')->exists('subcategoryitem/' . $item->image)) {
            Storage::disk('public')->delete('subcategoryitem/' . $item->image);
        }

        $item->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item deleted successfully'
        ]);
    }
}
