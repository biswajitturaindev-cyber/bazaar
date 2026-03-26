<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\MasterProducts;
use App\Models\Hsn;
use App\Models\SubCategoryItem;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class ProductController extends Controller
{
    public function productCategoryList(Request $request)
    {
        $data = Category::all();
        return view('admin.product.product_category.product_category_list',compact('data'));
    }

    public function productCategory(Request $request)
    {
        return view('admin.product.product_category.product_category');
    }

    public function checkCategory(Request $request)
    {
        $exists = Category::where('name', $request->category_name)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    public function productCategoryStore(Request $request)
    {
        // Validation
        $data = $request->validate([
            'category_name' => 'required|string|min:3|max:100',
            'description' => 'nullable|string|max:200',
            'status' => 'required|in:0,1',
            'commission' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp'
        ]);

        $imageName = null;

        // Image Upload
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $imageName = time().'.'.$file->extension();

            $manager = new ImageManager(new Driver());

            $image = $manager->read($file->getRealPath())
                ->cover(300, 300);

            Storage::disk('public')->put(
                'category/'.$imageName,
                (string) $image->encode()
            );
        }

        // Save
        $category = Category::create([
            'name' => $data['category_name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'commission' => $data['commission'] ?? 0,
            'image' => $imageName
        ]);

        // Response
        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
        ], 201);
    }


    public function productCategoryEdit($id)
    {
        $category = Category::findOrFail($id);

        return view(
            'admin.product.product_category.product_category_edit',
            compact('category')
        );
    }

    public function productCategoryUpdate(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        // Validation
        $data = $request->validate([
            'category_name' => 'required|string|min:3|max:100|unique:categories,name,' . $id,
            'description' => 'nullable|string|max:200',
            'status' => 'required|in:0,1',
            'commission' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp'
        ]);

        // Image Update
        if ($request->hasFile('image')) {
            try {
                // delete old image
                if ($category->image && Storage::disk('public')->exists('category/'.$category->image)) {
                    Storage::disk('public')->delete('category/'.$category->image);
                }

                $file = $request->file('image');
                $imageName = time().'.'.$file->extension();

                $manager = new ImageManager(new Driver());

                $image = $manager->read($file->getRealPath())
                    ->cover(300, 300);

                Storage::disk('public')->put(
                    'category/'.$imageName,
                    (string) $image->toJpeg(90)
                );

                $category->image = $imageName;

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image upload failed'
                ], 500);
            }
        }

        // Update Data
        $category->update([
            'name' => $data['category_name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'commission' => $data['commission'] ?? 0
        ]);

        // Response
        return response()->json([
                'success' => true,
                'message' => 'Category updated successfully'
        ]);
    }

    public function productCategoryDelete($id)
    {
        $category = Category::findOrFail($id);

        // Check if sub category exists
        $hasSubCategory = \DB::table('sub_categories')
            ->where('category_id', $id)
            ->exists();

        if ($hasSubCategory) {

            return redirect()->back()->with('error', 'Cannot delete category. Sub category exists.');
        }

        $category->delete();

        return redirect()->route('admin.product.category.list')
            ->with('success', 'Category deleted successfully');
    }

    public function productSubCategoryList()
    {
        $subcategories = SubCategory::with('category')->get();

        return view('admin.product.product_sub_category.sub_category_list', compact('subcategories'));
    }

    public function productSubCategory(Request $request)
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.product.product_sub_category.sub_category',compact('categories'));
    }

    public function productSubCategoryStore(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => [
                'required',
                'min:3',
                'max:50',
                'regex:/^[A-Za-z0-9 .]+$/',
                'unique:sub_categories,name'
            ],
            'description' => [
                'nullable',
                'min:10',
                'max:200',
                'regex:/^[A-Za-z0-9 .]+$/'
            ],
        ]);

        SubCategory::create([
            'category_id' => $request->category_id,
            //'parent_id' => $request->parent_id ?? 0,
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return redirect()->route('admin.product.sub.category.list')
                ->with('success','Sub Category Added Successfully');
    }

    public function checkName(Request $request)
    {
        $exists = SubCategory::where('name', $request->name)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    public function productSubCategoryEdit($id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $categories = Category::all();

        return view(
            'admin.product.product_sub_category.sub_category_edit',
            compact('subcategory','categories')
        );
    }

    public function productSubCategoryUpdate(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => [
                'required',
                'min:3',
                'max:50',
                'regex:/^[A-Za-z0-9 .]+$/',
                'unique:sub_categories,name,' . $id
            ],
            'description' => [
                'nullable',
                'min:10',
                'max:200',
                'regex:/^[A-Za-z0-9 .]+$/'
            ],
        ]);

        $subcategory = SubCategory::findOrFail($id);

        $subcategory->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sub Category updated successfully'
        ]);
    }

    public function productSubCategoryDelete($id)
    {
        $subCategory = SubCategory::findOrFail($id);

        if ($subCategory->products()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete sub category. Products exist.');
        }

        $subCategory->delete();

        return redirect()->route('admin.product.sub.category.list')
            ->with('success', 'Sub Category deleted successfully');
    }

    /* Sub Category Item Section Start */

    public function productSubCategoryItemList()
    {
        $subcategoriitems = SubCategoryItem::with(['category', 'subCategory'])->get();

        return view('admin.product.product_sub_category_item.sub_category_item_list', compact('subcategoriitems'));
    }

    public function productSubCategoryItem(Request $request)
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.product.product_sub_category_item.sub_category_item',compact('categories'));
    }


    public function productSubCategoryItemStore(Request $request)
    {
        // Validation
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',

            'name' => 'required|string|min:3|max:100|unique:sub_category_items,name',
            'description' => 'nullable|string|max:200',

            'status' => 'required|in:0,1',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp'
        ]);

        $imageName = null;

        // Image Upload
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $imageName = time().'.'.$file->extension();

                $manager = new ImageManager(new Driver());

                $image = $manager->read($file->getRealPath())
                    ->cover(300, 300);

                Storage::disk('public')->put(
                    'subcategoryitem/'.$imageName,
                    (string) $image->toJpeg(90)
                );

            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Image upload failed']);
            }
        }

        // Save
        SubCategoryItem::create([
            'category_id' => $data['category_id'],
            'sub_category_id' => $data['sub_category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'image' => $imageName
        ]);

        // Redirect
        return redirect()
            ->route('admin.product.sub.category.item.list')
            ->with('success', 'Sub Category Item Added Successfully');
    }

    public function checksubcatitemName(Request $request)
    {
        $exists = SubCategory::where('name', $request->name)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    public function productSubCategoryItemEdit($id)
    {
        $item = SubCategoryItem::findOrFail($id);

        $categories = Category::all();

        // load subcategories based on selected category
        $subcategories = SubCategory::where('category_id', $item->category_id)->get();

        return view(
            'admin.product.product_sub_category_item.sub_category_item_edit',
            compact('item', 'categories', 'subcategories')
        );
    }

    public function productSubCategoryItemUpdate(Request $request, $id)
    {
        $item = SubCategoryItem::findOrFail($id);

        // Validation
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',

            'name' => 'required|string|min:3|max:100|unique:sub_category_items,name,' . $id,
            'description' => 'nullable|string|max:200',

            'status' => 'required|in:0,1',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp'
        ]);

        // Image Update
        if ($request->hasFile('image')) {
            try {
                // delete old image
                if ($item->image && Storage::disk('public')->exists('subcategory/'.$item->image)) {
                    Storage::disk('public')->delete('subcategory/'.$item->image);
                }

                $file = $request->file('image');
                $imageName = time().'.'.$file->extension();

                $manager = new ImageManager(new Driver());

                $image = $manager->read($file->getRealPath())
                    ->cover(300, 300);

                Storage::disk('public')->put(
                    'subcategoryitem/'.$imageName,
                    (string) $image->toJpeg(90)
                );

                $item->image = $imageName;

            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Image upload failed']);
            }
        }

        // Update Data
        $item->update([
            'category_id' => $data['category_id'],
            'sub_category_id' => $data['sub_category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status']
        ]);

        // Redirect (better than JSON for form submit)
        return response()->json([
            'success' => true,
            'message' => 'Sub Category Item updated successfully'
        ]);

    }

    public function productSubCategoryItemDelete($id)
    {
        $subCategory = SubCategory::findOrFail($id);

        if ($subCategory->products()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete sub category. Products exist.');
        }

        $subCategory->delete();

        return redirect()->route('admin.product.sub.category.item.list')
            ->with('success', 'Sub Category deleted successfully');
    }



    /* Sub Category Item Section End*/

    public function productList(Request $request)
    {
        $products = MasterProducts::with(['category','subcategory'])->latest()->get();
        return view('admin.product.products.product_list',compact('products'));
    }

    public function productView(Request $request)
    {
        $categories = Category::all();
        $subcategories = SubCategory::all();

        $hsnList = Hsn::where('isActive', 1)
                ->orderBy('hsnCode')
                ->select('id','hsnCode','description','iGst')
                ->get();

        return view('admin.product.products.product', compact('categories','subcategories','hsnList'));
    }

    public function getSubCategories($category_id)
    {
        $subcategories = SubCategory::where('category_id', $category_id)->get();
        return response()->json($subcategories);
    }

    public function getsubcategorieslist($category_id)
    {
        $subCategories = SubCategory::where('category_id', $category_id)
            ->where('status', 1)
            ->get();

        return response()->json($subCategories);
    }


    public function loadProductList(Request $request)
    {
        $query = MasterProducts::select('id', 'name', 'price');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('subcategory')) {
            $query->where('sub_category_id', $request->subcategory);
        }

        $products = $query->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Products fetched successfully',
            'data' => $products->items(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'total' => $products->total(),
        ]);
    }

    public function productAdd(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'name' => 'required|min:3',
            'price' => 'required|numeric',
            'prod_bv' => 'required|numeric',
            'prod_pv' => 'required|numeric',
            'hsn_code' => 'required',
            'stock' => 'required|numeric',
            'description' => 'required'
        ]);

        try {

            $product = new MasterProducts();

            $product->category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id;
            $product->name = $request->name;
            $product->sku = $request->hsn_code;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->prod_bv = $request->prod_bv;
            $product->prod_pv = $request->prod_pv;
            $product->stock = $request->stock;
            $product->status = $request->status;

            /* Upload Images */

            $images = [];

            if($request->hasFile('images')){

                foreach($request->file('images') as $image){

                    $name = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();

                    $image->move(public_path('uploads/products'),$name);

                    $images[] = $name;
                }

            }

            $product->image = json_encode($images);

            $product->save();

            return response()->json([
                'status' => true,
                'message' => 'Product added successfully'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        }
    }

    public function viewProductDetails($id)
    {
        $id = decrypt($id);
        $product = MasterProducts::with(['category','subcategory'])->findOrFail($id);

        return view('admin.product.products.view_product', compact('product'));
    }

    public function productEdit($id)
    {
        $id = decrypt($id);

        $product = MasterProducts::findOrFail($id);

        $categories = Category::all();
        $subcategories = SubCategory::where(
            'category_id',
            $product->category_id
        )->get();

        $hsnList = Hsn::all();

        return view(
            'admin.product.products.edit_product',
            compact(
                'product',
                'categories',
                'subcategories',
                'hsnList'
            )
        );
    }

    public function productUpdate(Request $request, $id)
    {
        $id = decrypt($id);

        $request->validate([
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'name' => 'required|min:3',
            'price' => 'required|numeric',
            'prod_bv' => 'required|numeric',
            'prod_pv' => 'required|numeric',
            'hsn_code' => 'required',
            'stock' => 'required|numeric',
            'description' => 'required'
        ]);

        try {

            $product = MasterProducts::findOrFail($id);

            $product->category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id;
            $product->name = $request->name;
            $product->sku = $request->hsn_code;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->prod_bv = $request->prod_bv;
            $product->prod_pv = $request->prod_pv;
            $product->stock = $request->stock;
            $product->status = $request->status;

            $images = json_decode($product->image, true) ?? [];

            /*
            |--------------------------------------------------------------------------
            | DELETE IMAGES (Temporary UI delete -> Final delete on update)
            |--------------------------------------------------------------------------
            */

            if ($request->deleted_images) {

                $deletedImages = json_decode($request->deleted_images, true);

                foreach ($deletedImages as $img) {

                    $path = public_path('uploads/products/' . $img);

                    if (file_exists($path)) {
                        unlink($path);
                    }

                    $key = array_search($img, $images);

                    if ($key !== false) {
                        unset($images[$key]);
                    }
                }

                $images = array_values($images);
            }

            /*
            |--------------------------------------------------------------------------
            | ADD NEW IMAGES
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('images')) {

                foreach ($request->file('images') as $image) {

                    $name = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                    $image->move(public_path('uploads/products'), $name);

                    $images[] = $name;
                }
            }

            $product->image = json_encode($images);

            $product->save();

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        }
    }


    public function productdelete($id)
    {
        $id = decrypt($id);

        $product = MasterProducts::findOrFail($id);

        // delete images
        $images = json_decode($product->image);

        if ($images) {
            foreach ($images as $img) {

                $path = public_path('uploads/products/'.$img);

                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }

        $product->delete();

        return redirect()->back()->with('success','Product deleted successfully');
    }



























}
