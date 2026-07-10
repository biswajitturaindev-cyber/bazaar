<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class BusinessCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = BusinessCategory::latest()->paginate(10);

        return view('admin.business-category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.business-category.create');
    }

    /**
     * Store a newly created resource in storage.
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
            $imageName = time().'.'.$file->extension();

            $manager = new ImageManager(new Driver());

            $image = $manager->read($file->getRealPath())
                ->cover(300, 300);

            Storage::disk('public')->put(
                'business_category/'.$imageName,
                (string) $image->encode()
            );
        }

        BusinessCategory::create([
            'name' => $data['name'],
            'status' => $data['status'],
            'image' => $imageName
        ]);

        return redirect()->route('business-categories.index')
            ->with('success', 'Category Created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = BusinessCategory::findOrFail($id);
        return view('admin.business-category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = BusinessCategory::findOrFail($id);

        $data = $request->validate([
            'name'   => 'required|unique:business_categories,name,'.$id,
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'status' => 'required|in:0,1'
        ]);

        $imageName = $category->image;

        if ($request->hasFile('image')) {

            if ($category->image && Storage::disk('public')->exists('business_category/'.$category->image)) {
                Storage::disk('public')->delete('business_category/'.$category->image);
            }

            $file = $request->file('image');
            $imageName = time().'.'.$file->extension();

            $manager = new ImageManager(new Driver());

            $image = $manager->read($file->getRealPath())
                ->cover(300, 300);

            Storage::disk('public')->put(
                'business_category/'.$imageName,
                (string) $image->encode()
            );
        }

        $category->update([
            'name' => $data['name'],
            'status' => $data['status'],
            'image' => $imageName
        ]);

        return redirect()->route('business-categories.index')
            ->with('success', 'Category Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = BusinessCategory::findOrFail($id);

        if ($category->image && Storage::disk('public')->exists('business_category/'.$category->image)) {
            Storage::disk('public')->delete('business_category/'.$category->image);
        }

        $category->delete();

        return redirect()->back()->with('success', 'Deleted Successfully');
    }
}
