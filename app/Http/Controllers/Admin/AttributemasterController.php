<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttributeMaster;
use App\Models\BusinessCategory;
use App\Models\BusinessSubCategory;
use Illuminate\Http\Request;

class AttributemasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $masters = AttributeMaster::with(['category', 'subCategory'])
            ->latest()
            ->paginate(10);

        return view('admin.attribute-master.index', compact('masters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = BusinessCategory::where('status', 1)->get();
        $subCategories = BusinessSubCategory::where('status', 1)->get();

        return view('admin.attribute-master.create', compact(
            'categories',
            'subCategories'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'business_category_id' => 'required|integer',
            'business_sub_category_id' => 'required|integer',
            'name' => 'required|string|max:255|unique:attribute_masters,name',
        ]);

        AttributeMaster::create($data);

        return redirect()->route('attribute-master.index')
            ->with('success', 'Attribute Master created successfully');
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
        $master = AttributeMaster::findOrFail($id);
        $categories = BusinessCategory::where('status', 1)->get();

        $subCategories = BusinessSubCategory::where('business_category_id', $master->business_category_id)
            ->where('status', 1)
            ->get();

        return view('admin.attribute-master.edit', compact(
            'master',
            'categories',
            'subCategories'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $master = AttributeMaster::findOrFail($id);

        $data = $request->validate([
            'business_category_id' => 'required|integer',
            'business_sub_category_id' => 'required|integer',
            'name' => 'required|string|max:255|unique:attribute_masters,name,' . $id,
        ]);

        $master->update($data);

        return redirect()->route('attribute-master.index')
            ->with('success', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $master = AttributeMaster::findOrFail($id);
        $master->delete();

        return back()->with('success', 'Deleted successfully');
    }
}
