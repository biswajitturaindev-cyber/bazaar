<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterProduct;
use Illuminate\Http\Request;
use App\Http\Resources\MasterProductResource;
use Illuminate\Support\Facades\Storage;

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
            'hsn'
        ])->latest()->get();

        return MasterProductResource::collection($products);
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

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = uniqid() . '.' . $file->extension();
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($file->getRealPath())->cover(300, 300);
            $path = 'master_products/' . $imageName;
            Storage::disk('public')->put($path, (string) $image->encode());
            $data['image'] = $path;
        }

        $product = MasterProduct::create($data);

        return new MasterProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = MasterProduct::with([
            'category',
            'subCategory',
            'subSubCategory',
            'hsn'
        ])->findOrFail($id);

        return new MasterProductResource($product);
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

        if ($request->hasFile('image')) {

            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $file = $request->file('image');
            $imageName = uniqid() . '.' . $file->extension();
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($file->getRealPath())->cover(300, 300);
            $path = 'master_products/' . $imageName;
            Storage::disk('public')->put($path, (string) $image->encode());

            $data['image'] = $path;
        }

        $product->update($data);

        return new MasterProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = MasterProduct::findOrFail($id);

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}
