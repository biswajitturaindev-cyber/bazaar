<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Hsn;
use App\Models\MasterProduct;
use App\Models\SubCategory;
use App\Models\SubCategoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class MasterProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = MasterProduct::with(['category','subCategory','subSubCategory','hsn'])->latest()->get();
        return view('admin.master_products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.master_products.create', [
            'categories' => Category::all(),
            'subCategories' => SubCategory::all(),
            'subSubCategories' => SubCategoryItem::all(),
            'hsns' => Hsn::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'sub_category_id' => 'nullable',
            'sub_sub_category_id' => 'nullable',
            'hsn_id' => 'required',
            'name' => 'required|string|max:255',
            'product_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image',
        ]);

        $data = $request->except('image');

        // Image upload with Intervention
        if ($request->hasFile('image')) {

            $file = $request->file('image');

            // Better unique name
            $imageName = uniqid() . '.' . $file->extension();

            $manager = new ImageManager(new Driver());

            $image = $manager->read($file->getRealPath())
                ->cover(300, 300); // resize

            Storage::disk('public')->put(
                'master_products/' . $imageName,
                (string) $image->encode()
            );

            // full path
            $path = 'master_products/' . $imageName;

            // Save only filename in DB
            $data['image'] = $path;
        }

        MasterProduct::create($data);

        return redirect()->route('master-products.index')
            ->with('success', 'Product created successfully');
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
    public function edit(string $id)
    {
        $product = MasterProduct::findOrFail($id);

        return view('admin.master_products.edit', [
            'product' => $product,
            'categories' => Category::all(),
            'subCategories' => SubCategory::all(),
            'subSubCategories' => SubCategoryItem::all(),
            'hsns' => Hsn::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = MasterProduct::findOrFail($id);

        $request->validate([
            'category_id' => 'required',
            'sub_category_id' => 'nullable',
            'sub_sub_category_id' => 'nullable',
            'hsn_id' => 'required',
            'name' => 'required|string|max:255',
            'product_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image',
        ]);

        $data = $request->except('image');

        // Image upload with resize
        if ($request->hasFile('image')) {

            // delete old image (IMPORTANT)
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $file = $request->file('image');

            $imageName = uniqid() . '.' . $file->extension();

            $manager = new ImageManager(new Driver());

            $image = $manager->read($file->getRealPath())
                ->cover(300, 300);

            // full path
            $path = 'master_products/' . $imageName;

            Storage::disk('public')->put(
                $path,
                (string) $image->encode()
            );

            // save path in DB
            $data['image'] = $path;
        }

        $product->update($data);

        return redirect()->route('master-products.index')
            ->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = MasterProduct::findOrFail($id);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return back()->with('success', 'Product deleted successfully');
    }

    public function getSubCategories($category_id)
    {
        $subCategories = SubCategory::where('category_id', $category_id)->get();
        return response()->json($subCategories);
    }

    public function getSubSubCategories($sub_category_id)
    {
        $subSubCategories = SubCategoryItem::where('sub_category_id', $sub_category_id)->get();
        return response()->json($subSubCategories);
    }
}
