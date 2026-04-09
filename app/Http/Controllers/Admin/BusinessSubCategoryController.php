<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessSubCategory;
use App\Models\BusinessCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BusinessSubCategoryController extends Controller
{
    // List
    public function index(Request $request)
    {
        $query = BusinessSubCategory::with('category');

        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->business_category_id) {
            $query->where('business_category_id', $request->business_category_id);
        }

        $subcategories = $query->latest()->paginate(10);

        return view('admin.business-sub-category.index', compact('subcategories'));
    }

    // Create Form
    public function create()
    {
        $categories = BusinessCategory::where('status', 1)->get();
        return view('admin.business-sub-category.create', compact('categories'));
    }

    // Store
    public function store(Request $request)
    {
        $data = $request->validate([
            'business_category_id' => 'required|exists:business_categories,id',
            'name' => 'required|string|max:255|unique:business_sub_categories,name,NULL,id,business_category_id,' . $request->business_category_id,
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
                (string) $image->encode()
            );
        }

        BusinessSubCategory::create([
            'business_category_id' => $data['business_category_id'],
            'name' => $data['name'],
            'status' => $data['status'],
            'commission'=> $data['commission'] ?? 0,
            'image' => $imageName
        ]);

        return redirect()->route('business-sub-categories.index')
            ->with('success', 'Sub Category Created');
    }

    // Edit Form
    public function edit($id)
    {
        $subCategory = BusinessSubCategory::findOrFail($id);
        $categories = BusinessCategory::where('status', 1)->get();

        return view('admin.business-sub-category.edit', compact('subCategory', 'categories'));
    }

    // Update
    public function update(Request $request, string $id)
    {
        $subcategory = BusinessSubCategory::findOrFail($id);

        $data = $request->validate([
            'business_category_id' => 'required|exists:business_categories,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('business_sub_categories')
                    ->where(fn ($q) => $q->where('business_category_id', $request->business_category_id))
                    ->ignore($id),
            ],
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

        return redirect()->route('business-sub-categories.index')
            ->with('success', 'Sub Category Updated');
    }

    // Delete
    public function destroy(string $id)
    {
        $subcategory = BusinessSubCategory::findOrFail($id);

        if ($subcategory->image && Storage::disk('public')->exists('business_sub_category/'.$subcategory->image)) {
            Storage::disk('public')->delete('business_sub_category/'.$subcategory->image);
        }

        $subcategory->delete();

        return redirect()->back()->with('success', 'Deleted Successfully');
    }
}
