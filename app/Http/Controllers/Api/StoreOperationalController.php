<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreOperationalResource;
use App\Models\StoreOperationalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

class StoreOperationalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $store = StoreOperationalDetail::first();

        if (!$store) {
            return response()->json([
                'status' => false,
                'message' => 'Store not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => new StoreOperationalResource($store)
        ]);
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
        DB::beginTransaction();

        try {
            // Decode business_id FIRST
            $decoded = Hashids::decode($request->business_id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid business ID'
                ], 400);
            }

            $request->merge([
                'business_id' => $decoded[0] // replace with real ID
            ]);

            // Validation
            $validated = $request->validate([
                'business_id' => 'required|exists:businesses,id',

                'opening_time' => 'required',
                'closing_time' => 'required|after:opening_time',

                'delivery_type' => 'required|in:self,platform,both',

                'delivery_radius' => 'nullable|numeric|min:0',

                'serviceable_pincode' => 'required|array',
                'serviceable_pincode.*' => 'required',

                'status' => 'required|boolean',
            ]);

            // Convert pincode array  string
            $validated['serviceable_pincode'] = implode(',', $validated['serviceable_pincode']);

            // Save / Update using business_id
            $store = StoreOperationalDetail::updateOrCreate(
                ['business_id' => $validated['business_id']],
                $validated
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Saved successfully',
                'data' => new StoreOperationalResource($store)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            DB::rollBack();

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
    public function show(string $id)
    {
        // Decode and replace $id itself
        $decoded = Hashids::decode($id);

        if (empty($decoded)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid ID'
            ], 400);
        }

        $id = $decoded[0]; // overwrite $id


        $store = StoreOperationalDetail::find($id);

        if (!$store) {
            return response()->json([
                'status' => false,
                'message' => 'Not found'
            ], 404);
        }

        return new StoreOperationalResource($store);
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
        DB::beginTransaction();

        try {

            // Decode and overwrite $id
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid ID'
                ], 400);
            }

            $id = $decoded[0]; // important

            // Find record
            $store = StoreOperationalDetail::find($id);

            if (!$store) {
                return response()->json([
                    'status' => false,
                    'message' => 'Not found'
                ], 404);
            }

            // Validation
            $validated = $request->validate([
                'opening_time' => 'required',
                'closing_time' => 'required|after:opening_time',

                'delivery_type' => 'required|in:self,platform,both',

                'delivery_radius' => 'nullable|numeric|min:0',

                'serviceable_pincode' => 'required',
                'status' => 'required|boolean',
            ]);

            // Handle pincode (array OR string)
            $pincode = $request->serviceable_pincode;

            if (!is_array($pincode)) {
                $pincode = explode(',', $pincode);
            }

            $validated['serviceable_pincode'] = implode(',', $pincode);

            // Update
            $store->update($validated);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Updated successfully',
                'data' => new StoreOperationalResource($store)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            DB::rollBack();

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
    public function destroy(string $id)
    {
        $store = StoreOperationalDetail::find($id);

        if (!$store) {
            return response()->json([
                'status' => false,
                'message' => 'Not found'
            ], 404);
        }

        $store->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully'
        ]);
    }
}
