<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Hsn;
use App\Models\MasterProduct;
use App\Models\MasterProductImage;
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
        $products = MasterProduct::with([
            'category',
            'subCategory',
            'subSubCategory',
            'hsn',
            'primaryImage'
        ])
        ->latest()
        ->get();

        return view(
            'admin.master_products.index',
            compact('products')
        );
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
            'category_id'          => 'required',
            'sub_category_id'      => 'nullable',
            'sub_sub_category_id'  => 'nullable',
            'hsn_id'               => 'required',
            'name'                 => 'required|string|max:255',

            'product_price'        => 'required|numeric',
            'selling_price'        => 'required|numeric',
            'commission'           => 'nullable|numeric',
            'description'          => 'nullable|string',

            // Multiple Images
            'images'               => 'nullable|array|max:4',
            'images.*'             => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            // Primary Image
            'primary_image'        => 'nullable|integer',
        ]);

        $data = $request->except([
            'images',
            'primary_image'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create Product
        |--------------------------------------------------------------------------
        */
        $product = MasterProduct::create($data);

        /*
        |--------------------------------------------------------------------------
        | Upload Multiple Images
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('images')) {

            $manager = new ImageManager(new Driver());

            // Selected Primary Image Index
            $primaryIndex = $request->primary_image ?? 0;

            foreach ($request->file('images') as $index => $file) {

                /*
                |--------------------------------------------------------------------------
                | Always Save as WEBP
                |--------------------------------------------------------------------------
                */
                $imageName = uniqid() . '.webp';

                /*
                |--------------------------------------------------------------------------
                | Resize + Convert WEBP
                |--------------------------------------------------------------------------
                */
                $image = $manager->read($file->getRealPath())
                    ->cover(300, 300)
                    ->toWebp(80);

                /*
                |--------------------------------------------------------------------------
                | Save Path
                |--------------------------------------------------------------------------
                */
                $path = 'master_products/' . $imageName;

                /*
                |--------------------------------------------------------------------------
                | Store Image
                |--------------------------------------------------------------------------
                */
                Storage::disk('public')->put(
                    $path,
                    (string) $image
                );

                /*
                |--------------------------------------------------------------------------
                | Save Image DB
                |--------------------------------------------------------------------------
                */
                MasterProductImage::create([
                    'master_product_id' => $product->id,
                    'image'             => $path,
                    'is_primary'        => ($primaryIndex == $index),
                ]);
            }
        }

        return redirect()
            ->route('master-products.index')
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
        $product = MasterProduct::with([
            'images',
            'primaryImage'
        ])->findOrFail($id);

        return view('admin.master_products.edit', [
            'product'           => $product,
            'categories'        => Category::all(),
            'subCategories'     => SubCategory::all(),
            'subSubCategories'  => SubCategoryItem::all(),
            'hsns'              => Hsn::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = MasterProduct::with('images')->findOrFail($id);

        $request->validate([
            'category_id'          => 'required',
            'sub_category_id'      => 'nullable',
            'sub_sub_category_id'  => 'nullable',
            'hsn_id'               => 'required',
            'name'                 => 'required|string|max:255',
            'product_price'        => 'required|numeric',
            'selling_price'        => 'required|numeric',
            'commission'           => 'nullable|numeric',
            'description'          => 'nullable|string',

            // Multiple Images
            'images'               => 'nullable|array|max:4',
            'images.*'             => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            // Primary Image
            'primary_image'        => 'nullable|integer',
        ]);

        $data = $request->except([
            'images',
            'primary_image',
            'deleted_images'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update Product
        |--------------------------------------------------------------------------
        */
        $product->update($data);

        /*
        |--------------------------------------------------------------------------
        | Delete Removed Images
        |--------------------------------------------------------------------------
        */
        if ($request->filled('deleted_images')) {

            $images = MasterProductImage::whereIn(
                'id',
                $request->deleted_images
            )->get();

            foreach ($images as $image) {

                if (
                    $image->image &&
                    Storage::disk('public')->exists($image->image)
                ) {
                    Storage::disk('public')->delete($image->image);
                }

                $image->delete();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Upload New Images (Convert to WEBP)
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('images')) {

            $manager = new ImageManager(new Driver());

            foreach ($request->file('images') as $file) {

                // Always WEBP
                $imageName = uniqid() . '.webp';

                // Resize + Convert WEBP
                $image = $manager->read($file->getRealPath())
                    ->cover(300, 300)
                    ->toWebp(80);

                // Save path
                $path = 'master_products/' . $imageName;

                // Store image
                Storage::disk('public')->put(
                    $path,
                    (string) $image
                );

                // Save DB
                MasterProductImage::create([
                    'master_product_id' => $product->id,
                    'image'             => $path,
                    'is_primary'        => false,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Update Primary Image
        |--------------------------------------------------------------------------
        */
        MasterProductImage::where(
            'master_product_id',
            $product->id
        )->update([
            'is_primary' => false
        ]);

        $allImages = MasterProductImage::where(
            'master_product_id',
            $product->id
        )->get();

        $primaryIndex = $request->primary_image ?? 0;

        if (isset($allImages[$primaryIndex])) {

            $allImages[$primaryIndex]->update([
                'is_primary' => true
            ]);
        }

        return redirect()
            ->route('master-products.index')
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
