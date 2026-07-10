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
        try {

            $businessCategoryId = request()->filled('business_category_id')
                ? decodeIdOrFail(request()->business_category_id)
                : null;

            $search = request()->get('search');
            $perPage = request()->get('per_page', 10);

            $products = MasterProduct::with([
                    'category',
                    'subCategory',
                    'subSubCategory',
                    'hsn',
                    'primaryImage',
                    'images'
                ])
                ->when($businessCategoryId, function ($q) use ($businessCategoryId) {
                    $q->join(
                            'business_category_mappings',
                            'business_category_mappings.category_id',
                            '=',
                            'master_products.category_id'
                        )
                        ->where(
                            'business_category_mappings.business_category_id',
                            $businessCategoryId
                        )
                        ->select('master_products.*')
                        ->distinct();
                })
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('master_products.name', 'like', "%{$search}%")
                            ->orWhere('master_products.description', 'like', "%{$search}%")
                            ->orWhereHas('category', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('subCategory', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('subSubCategory', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                    });
                })
                ->latest('master_products.created_at')
                ->paginate($perPage);

            return MasterProductResource::collection($products);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch products.',
                'error' => $e->getMessage(),
            ], 500);
        }
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
