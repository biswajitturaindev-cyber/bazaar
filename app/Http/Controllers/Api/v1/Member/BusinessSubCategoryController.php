<?php

namespace App\Http\Controllers\Api\v1\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessSubCategoryResource;
use App\Models\BusinessSubCategory;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class BusinessSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BusinessSubCategory::with('category:id,name');
    
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
    
        if ($request->filled('business_category_id')) {
    
            $decoded = Hashids::decode($request->business_category_id);
    
            if (empty($decoded)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid business category ID'
                ], 400);
            }
    
            $categoryId = $decoded[0];
    
            $query->where('business_category_id', $categoryId);
        }
    
        $subcategories = $query->latest()->paginate($request->per_page ?? 10);
    
        return BusinessSubCategoryResource::collection($subcategories);
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
