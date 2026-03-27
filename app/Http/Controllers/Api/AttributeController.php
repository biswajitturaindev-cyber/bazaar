<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Http\Resources\AttributeResource;
class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Attribute::with('values')->latest()->get();
        return response()->json([
            'status' => true,
            'data' => AttributeResource::collection($data)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:attributes,name',
            'status' => 'required|in:0,1',
        ]);

        $attribute = Attribute::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Attribute created',
            'data' => new AttributeResource($attribute)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $attribute = Attribute::with('values')->findOrFail($id);
        return new AttributeResource($attribute);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $attribute = Attribute::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|unique:attributes,name,' . $id,
            'status' => 'required|in:0,1',
        ]);

        $attribute->update($data);
        return response()->json([
            'status' => true,
            'message' => 'Updated',
            'data' => new AttributeResource($attribute)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $attribute = Attribute::findOrFail($id);
        $attribute->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted'
        ]);
    }
}
