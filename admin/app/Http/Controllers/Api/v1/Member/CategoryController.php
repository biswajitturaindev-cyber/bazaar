<?php

namespace App\Http\Controllers\Api\v1\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Category::query();

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $businessCategoryId = $request->filled('business_category_id')
            ? decodeIdOrFail($request->business_category_id, 'Invalid business category ID')
            : null;

        $businessSubCategoryId = $request->filled('business_sub_category_id')
            ? decodeIdOrFail($request->business_sub_category_id, 'Invalid business sub category ID')
            : null;

        if ($businessCategoryId || $businessSubCategoryId) {
            $query->whereHas('mappings', function ($q) use ($businessCategoryId, $businessSubCategoryId) {

                if ($businessCategoryId) {
                    $q->where('business_category_id', $businessCategoryId);
                }

                if ($businessSubCategoryId) {
                    $q->where('business_sub_category_id', $businessSubCategoryId);
                }
            });
        }

        // Get ALL data
        $categories = $query->latest()->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
