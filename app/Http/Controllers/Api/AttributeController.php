<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Http\Resources\AttributeResource;
use Exception;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Attribute::with('values')->latest()->get();

            return response()->json([
                'status' => true,
                'message' => 'Attribute list fetched successfully',
                'data' => AttributeResource::collection($data)
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
                'name' => 'required|unique:attributes,name',
                'status' => 'required|in:0,1',
            ]);

            $attribute = Attribute::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Attribute created',
                'data' => new AttributeResource($attribute)
            ], 201);

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

            // Fetch attribute with values
            $attribute = Attribute::with('values')->findOrFail($id);

            return response()->json([
                'status'  => true,
                'message' => 'Attribute fetched successfully',
                'data'    => new AttributeResource($attribute)
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Attribute not found',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 404);
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

            $attribute = Attribute::findOrFail($id);

            $data = $request->validate([
                'name'   => 'required|unique:attributes,name,' . $id,
                'status' => 'required|in:0,1',
            ]);

            $attribute->update($data);

            return response()->json([
                'status'  => true,
                'message' => 'Attribute updated successfully',
                'data'    => new AttributeResource($attribute)
            ], 200);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Attribute not found'
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

            $attribute = Attribute::findOrFail($id);
            $attribute->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Attribute deleted successfully'
            ], 200);

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
}
