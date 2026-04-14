<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeMaster;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attributes = Attribute::with([
            'category',
            'subCategory',
            'attributeMaster'
        ])->latest()->paginate(10);
        return view('admin.attributes.index', compact('attributes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $categories = Category::where('status', 1)->get();
            $subCategories = SubCategory::where('status', 1)->get();
            $attributeMasters = AttributeMaster::get();

            return view('admin.attributes.create', compact(
                'categories',
                'subCategories',
                'attributeMasters'
            ));
        } catch (\Exception $e) {

            \Log::error('Attribute Create Error: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|integer',
            'sub_category_id' => 'required|integer',
            'attribute_master_id' => 'required|integer',
            'type' => 'required|in:text,color',
            'name' => 'required|unique:attributes,name',
            'status' => 'required|in:0,1',
        ]);

        Attribute::create($data);

        return redirect()->route('attributes.index')
            ->with('success', 'Attribute created successfully');
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
        $attribute = Attribute::findOrFail($id);

        $categories = Category::where('status', 1)->get();
        $subCategories = SubCategory::where('category_id', $attribute->category_id)
            ->where('status', 1)
            ->get();
        $attributeMasters = AttributeMaster::get();

        return view('admin.attributes.edit', compact(
            'attribute',
            'categories',
            'subCategories',
            'attributeMasters'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $attribute = Attribute::findOrFail($id);

        $data = $request->validate([
            'category_id' => 'required|integer',
            'sub_category_id' => 'required|integer',
            'attribute_master_id' => 'required|integer',
            'type' => 'required|in:text,color',
            'name' => 'required|unique:attributes,name,' . $id,
            'status' => 'required|in:0,1',
        ]);

        $attribute->update($data);

        return redirect()->route('attributes.index')
            ->with('success', 'Attribute updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $attribute = Attribute::findOrFail($id);
        $attribute->delete();

        return back()->with('success', 'Deleted successfully');
    }

    /**
     * Get subcategory list by category id
     */
    public function getSubCategories($id)
    {
        return SubCategory::where('category_id', $id)
            ->where('status', 1)
            ->get();
    }

}
