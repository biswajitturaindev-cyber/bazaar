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
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $columns = [
                0 => 'id',
                1 => 'category_id',
                2 => 'name',
                3 => 'product_price',
                4 => 'selling_price',
                5 => 'commission',
                6 => 'status',
            ];

            $totalData = MasterProduct::count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')] ?? 'id';
            $dir = $request->input('order.0.dir', 'desc');

            $query = MasterProduct::with([
                'category',
                'subCategory',
                'subSubCategory',
                'primaryImage'
            ]);

            // Search
            if ($search = $request->input('search.value')) {

                $query->where(function ($q) use ($search) {

                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('product_price', 'like', "%{$search}%")
                        ->orWhere('selling_price', 'like', "%{$search}%")
                        ->orWhere('commission', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($cat) use ($search) {
                            $cat->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('subCategory', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('subSubCategory', function ($subSub) use ($search) {
                            $subSub->where('name', 'like', "%{$search}%");
                        });

                });

            }

            $totalFiltered = $query->count();

            $products = $query
                ->orderBy($order, $dir)
                ->offset($start)
                ->limit($limit)
                ->get();

            $data = [];

            foreach ($products as $product) {

                $hierarchy = $product->category->name ?? '-';

                if ($product->subCategory) {
                    $hierarchy .= ' → ' . $product->subCategory->name;
                }

                if ($product->subSubCategory) {
                    $hierarchy .= ' → ' . $product->subSubCategory->name;
                }

                $image = $product->primaryImage
                    ? '<img src="' . $product->primaryImage->image_url . '" width="50" height="50" class="rounded border" style="object-fit:cover;">'
                    : '<span class="text-gray-400">No Image</span>';

                $status = $product->status == 1
                    ? '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700">Active</span>'
                    : '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">Inactive</span>';

                $action = '
                    <div class="flex gap-2">
                        <a href="' . route('master-products.edit', $product->id) . '"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                            Edit
                        </a>

                        <form action="' . route('master-products.destroy', $product->id) . '"
                            method="POST"
                            onsubmit="return confirm(\'Delete this product?\')"
                            style="display:inline-block;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                Delete
                            </button>
                        </form>
                    </div>';

                $data[] = [
                    '',
                    $hierarchy,
                    $product->name,
                    $product->product_price,
                    $product->selling_price,
                    $product->commission,
                    $image,
                    $status,
                    $action,
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data,
            ]);
        }

        return view('admin.master_products.index');
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
