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
use Vinkla\Hashids\Facades\Hashids;

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
                'attribute_id' => 'required',
                'value'        => 'required|string|max:255',
                'color_code'   => 'nullable|string',
                'status'       => 'required|in:0,1',
            ]);

            // Decode attribute_id
            $decoded = Hashids::decode($data['attribute_id']);

            if (empty($decoded)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid Attribute ID'
                ], 400);
            }

            $data['attribute_id'] = $decoded[0];

            // Check attribute exists
            $attribute = Attribute::findOrFail($data['attribute_id']);

            // Duplicate check
            $exists = AttributeValue::where('attribute_id', $data['attribute_id'])
                ->where('value', $data['value'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Value already exists'
                ], 422);
            }

            // Remove color_code if not color attribute
            if (!str_contains(strtolower($attribute->name), 'color')) {
                $data['color_code'] = null;
            }

            $value = AttributeValue::create($data);

            return response()->json([
                'status'  => true,
                'message' => 'Attribute value created successfully',
                'data'    => new AttributeValueResource($value)
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Attribute not found'
            ], 404);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            // Decode ID
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid ID'
                ], 400);
            }

            $id = $decoded[0]; // overwrite ID

            $value = AttributeValue::with('attribute')->findOrFail($id);

            return response()->json([
                'status'  => true,
                'message' => 'Attribute value fetched successfully',
                'data'    => new AttributeValueResource($value)
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Attribute value not found'
            ], 404);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            // Decode ID
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid ID'
                ], 400);
            }

            $id = $decoded[0]; // overwrite ID

            $value = AttributeValue::findOrFail($id);

            $data = $request->validate([
                'attribute_id' => 'required',
                'value'        => 'required|string|max:255',
                'color_code'   => 'nullable|string',
                'status'       => 'required|in:0,1',
            ]);

            // Decode attribute_id
            $decodedAttr = Hashids::decode($data['attribute_id']);

            if (empty($decodedAttr)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid Attribute ID'
                ], 400);
            }

            $data['attribute_id'] = $decodedAttr[0];

            // Normalize value (avoid case duplicates)
            $data['value'] = strtolower(trim($data['value']));

            // Duplicate check (excluding current record)
            $exists = AttributeValue::where('attribute_id', $data['attribute_id'])
                ->where('value', $data['value'])
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Value already exists'
                ], 422);
            }

            // Ensure attribute exists
            $attribute = Attribute::findOrFail($data['attribute_id']);

            // Remove color_code if not color attribute
            if (!str_contains(strtolower($attribute->name), 'color')) {
                $data['color_code'] = null;
            }

            $value->update($data);

            return response()->json([
                'status'  => true,
                'message' => 'Attribute value updated successfully',
                'data'    => new AttributeValueResource($value)
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Attribute value or attribute not found'
            ], 404);

        } catch (ValidationException $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            // Decode ID
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid ID'
                ], 400);
            }

            $id = $decoded[0]; // overwrite ID

            $value = AttributeValue::findOrFail($id);
            $value->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Attribute value deleted successfully'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Attribute value not found'
            ], 404);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
