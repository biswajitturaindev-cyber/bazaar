<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttributeValue;
use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Http\Resources\AttributeValueResource;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AttributeValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = AttributeValue::with('attribute')->latest()->get();

            return response()->json([
                'status' => true,
                'message' => 'Attribute values fetched successfully',
                'data' => AttributeValueResource::collection($data)
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() // remove in production
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
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
            $attribute = Attribute::findOrFail($data['attribute_id']);

            if (!str_contains(strtolower($attribute->name), 'color')) {
                $data['color_code'] = null;
            }

            $value = AttributeValue::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Created',
                'data' => new AttributeValueResource($value)
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Attribute Not Found'
            ], 404);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() // remove in production
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $value = AttributeValue::with('attribute')->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Attribute value fetched successfully',
                'data' => new AttributeValueResource($value)
            ], 200);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Attribute Value Not Found'
            ], 404);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() // remove in production
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $value = AttributeValue::findOrFail($id);

            $data = $request->validate([
                'attribute_id' => 'required|exists:attributes,id',
                'value' => 'required|string|max:255',
                'color_code' => 'nullable|string',
                'status' => 'required|in:0,1',
            ]);

            // normalize value (avoid case duplicates)
            $data['value'] = strtolower(trim($data['value']));

            // duplicate check (excluding current record)
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

            // ensure attribute exists
            $attribute = Attribute::findOrFail($data['attribute_id']);

            // remove color_code if not a color attribute
            if (!str_contains(strtolower($attribute->name), 'color')) {
                $data['color_code'] = null;
            }

            $value->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Updated',
                'data' => new AttributeValueResource($value)
            ], 200);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Attribute Value or Attribute Not Found'
            ], 404);

        } catch (ValidationException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() // remove in production
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $value = AttributeValue::findOrFail($id);
            $value->delete();

            return response()->json([
                'status' => true,
                'message' => 'Attribute Value Deleted Successfully'
            ], 200);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Attribute Value Not Found'
            ], 404);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() // remove in production
            ], 500);
        }
    }
}
