<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessCategoryMapping;
use App\Models\BusinessCategory;
use App\Models\BusinessSubCategory;
use App\Models\Category;

class BusinessCategoryMappingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mappings = BusinessCategoryMapping::with([
            'businessCategory',
            'businessSubCategory',
            'category'
        ])->latest()->paginate(10);

        return view('admin.business-category-mapping.index', compact('mappings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $businessCategories = BusinessCategory::all();
        $subCategories = BusinessSubCategory::all();
        $categories = Category::all();

        return view('admin.business-category-mapping.create', compact(
            'businessCategories',
            'subCategories',
            'categories'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'business_category_id' => 'required|exists:business_categories,id',
            'business_sub_category_id' => 'required|exists:business_sub_categories,id',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:0,1',
        ]);

        // Prevent duplicate
        $exists = BusinessCategoryMapping::where($data)->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'Mapping already exists']);
        }

        BusinessCategoryMapping::create($data);

        return redirect()->route('business-category-mapping.index')
            ->with('success', 'Mapping created successfully');
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
        $mapping = BusinessCategoryMapping::findOrFail($id);

        $businessCategories = BusinessCategory::all();
        $categories = Category::all();

        return view('admin.business-category-mapping.edit', compact(
            'mapping',
            'businessCategories',
            'categories'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $mapping = BusinessCategoryMapping::findOrFail($id);

        $data = $request->validate([
            'business_category_id' => 'required|exists:business_categories,id',
            'business_sub_category_id' => 'required|exists:business_sub_categories,id',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:0,1',
        ]);

        // Prevent duplicate except current
        $exists = BusinessCategoryMapping::where($data)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'Mapping already exists']);
        }

        $mapping->update($data);

        return redirect()->route('business-category-mapping.index')
            ->with('success', 'Mapping updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mapping = BusinessCategoryMapping::findOrFail($id);
        $mapping->delete();

        return back()->with('success', 'Mapping deleted successfully');
    }

    public function getSubCategories($id)
    {
        $subCategories = BusinessSubCategory::where('business_category_id', $id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($subCategories);
    }
}
