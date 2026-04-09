<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hsn;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use App\Http\Resources\HsnResource;

class HsnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Hsn::latest()->get();

            return response()->json([
                'status' => true,
                'message' => 'HSN list fetched successfully',
                'data' => HsnResource::collection($data)
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
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
                'hsn_code' => 'required|string|max:20|unique:hsns,hsn_code,NULL,id,deleted_at,NULL',
                'description' => 'nullable|string',
                'cgst' => 'required|numeric|min:0',
                'sgst' => 'required|numeric|min:0',
                'igst' => 'required|numeric|min:0',
                'status' => 'required|boolean',
            ]);

            $hsn = Hsn::create($data);

            return response()->json([
                'status' => true,
                'message' => 'HSN created successfully',
                'data' => new HsnResource($hsn)
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
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Hsn $hsn)
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'HSN fetched successfully',
                'data' => new HsnResource($hsn)
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hsn $hsn)
    {
        try {
            $data = $request->validate([
                'hsn_code' => 'required|string|max:20|unique:hsns,hsn_code,' . $hsn->id . ',id,deleted_at,NULL',
                'description' => 'nullable|string',
                'cgst' => 'required|numeric|min:0',
                'sgst' => 'required|numeric|min:0',
                'igst' => 'required|numeric|min:0',
                'status' => 'required|boolean',
            ]);

            $hsn->update($data);

            return response()->json([
                'status' => true,
                'message' => 'HSN updated successfully',
                'data' => new HsnResource($hsn)
            ], 200);

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
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hsn $hsn)
    {
        try {
            $hsn->delete();

            return response()->json([
                'status' => true,
                'message' => 'HSN deleted successfully'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * HSN Dropdown
     */
    public function dropdown(Request $request)
    {
        try {
            $query = Hsn::where('status', 1);

            // Search filter
            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('hsn_code', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }

            $hsns = $query->select('id', 'hsn_code', 'description', 'cgst', 'sgst', 'igst', 'status')
                ->orderBy('hsn_code', 'asc')
                ->limit(50)
                ->get();

            // USE RESOURCE HERE
            return response()->json([
                'status' => true,
                'message' => 'HSN list fetched successfully',
                'data' => HsnResource::collection($hsns)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
