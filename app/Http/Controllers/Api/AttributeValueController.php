<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttributeValue;
use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Http\Resources\AttributeValueResource;

class AttributeValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = AttributeValue::with('attribute')->latest()->get();

        return response()->json([
            'status' => true,
            'data' => AttributeValueResource::collection($data)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $data = $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string|max:255',
            'color_code' => 'nullable|string',
            'status' => 'required|in:0,1',
        ]);

        // duplicate check
        $exists = AttributeValue::where('attribute_id', $data['attribute_id'])
            ->where('value', $data['value'])
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Value already exists'
            ], 422);
        }

        // remove color if not color attribute
        $attribute = Attribute::find($data['attribute_id']);
        if (!str_contains(strtolower($attribute->name), 'color')) {
            $data['color_code'] = null;
        }

        $value = AttributeValue::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Created',
            'data' => new AttributeValueResource($value)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $value = AttributeValue::with('attribute')->findOrFail($id);
        return new AttributeValueResource($value);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $value = AttributeValue::findOrFail($id);

        $data = $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string|max:255',
            'color_code' => 'nullable|string',
            'status' => 'required|in:0,1',
        ]);

        $exists = AttributeValue::where('attribute_id', $data['attribute_id'])
            ->where('value', $data['value'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Value already exists'
            ], 422);
        }

        $attribute = Attribute::find($data['attribute_id']);
        if (!str_contains(strtolower($attribute->name), 'color')) {
            $data['color_code'] = null;
        }

        $value->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Updated',
            'data' => new AttributeValueResource($value)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $value = AttributeValue::findOrFail($id);
        $value->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted'
        ]);
    }
}
