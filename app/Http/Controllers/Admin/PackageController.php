<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::latest()->paginate(10);

        return view('admin.packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.packages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'stars' => 'required|integer|min:0|max:5',
            'price' => 'required|numeric|min:0',
            'product_limit' => 'nullable|integer|min:1',
            'status' => 'required|boolean',
        ]);

        Package::create($data);

        return redirect()->route('packages.index')
            ->with('success', 'Package created successfully');
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
    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'stars' => 'required|integer|min:0|max:5',
            'price' => 'required|numeric|min:0',
            'product_limit' => 'nullable|integer|min:1',
            'status' => 'required|boolean',
        ]);

        $package->update($data);

        return redirect()->route('packages.index')
            ->with('success', 'Package updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        try {
            $package->delete();

            return response()->json([
                'status' => true,
                'message' => 'Package deleted successfully'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Cannot delete. Package already assigned to vendors.'
            ]);
        }
    }
}
