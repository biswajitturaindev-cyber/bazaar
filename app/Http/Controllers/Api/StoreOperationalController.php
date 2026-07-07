<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreOperationalResource;
use App\Models\Business;
use App\Models\StoreOperationalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

class StoreOperationalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $decoded = Hashids::decode($request->business_id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid business ID'
                ], 400);
            }

            $store = StoreOperationalDetail::with('timings')
                ->where('business_id', $decoded[0])
                ->first();

            if (!$store) {
                return response()->json([
                    'status' => false,
                    'message' => 'Store not found'
                ], 404);
            }

            $business = Business::find($store->business_id);

            $data = (new StoreOperationalResource($store))->toArray($request);

            $data['shop_status'] = $business?->shop_status;
            $data['working_days'] = $business?->working_days;

            return response()->json([
                'status' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
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

            // Decode business_id
            $decoded = Hashids::decode($request->business_id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid business ID'
                ], 400);
            }

            $request->merge([
                'business_id' => $decoded[0]
            ]);

            /*
            |--------------------------------------------------------------------------
            | Normalize Serviceable Pincode
            |--------------------------------------------------------------------------
            | Supports:
            | serviceable_pincode[] = 721447
            | serviceable_pincode[] = 721448
            |
            | OR
            |
            | serviceable_pincode = "721447,721448"
            |--------------------------------------------------------------------------
            */

            $pincodes = $request->serviceable_pincode;

            if (!empty($pincodes)) {

                if (!is_array($pincodes)) {

                    $pincodes = explode(',', $pincodes);

                } else {

                    $pincodes = collect($pincodes)
                        ->flatMap(fn($item) => explode(',', $item))
                        ->map(fn($item) => trim($item))
                        ->filter()
                        ->values()
                        ->toArray();
                }

                $request->merge([
                    'serviceable_pincode' => $pincodes
                ]);
            }

            // Validation
            $validated = $request->validate([
                'business_id' => 'required|exists:businesses,id',

                'delivery_type' => 'required|in:self,platform,both',
                'delivery_radius' => 'nullable|numeric|min:0',

                'serviceable_pincode' => 'nullable|array',
                'serviceable_pincode.*' => 'digits:6',

                'status' => 'required|boolean',

                'timings' => 'required|array|min:1',
                'timings.*.opening_time' => 'required|date_format:H:i',
                'timings.*.closing_time' => 'required|date_format:H:i',

                'shop_status' => 'required|in:open,closed',
                'working_days' => 'required|array',
                'working_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            ]);

            // Validate each timing
            foreach ($validated['timings'] as $index => $timing) {

                if (strtotime($timing['closing_time']) <= strtotime($timing['opening_time'])) {

                    return response()->json([
                        'status' => false,
                        'message' => 'Validation error',
                        'errors' => [
                            "timings.$index.closing_time" => [
                                'Closing time must be after opening time.'
                            ]
                        ]
                    ], 422);
                }
            }

            // Convert pincode array to comma-separated string
            $validated['serviceable_pincode'] = !empty($validated['serviceable_pincode'])
                ? implode(',', $validated['serviceable_pincode'])
                : null;

            // Create or Update Store
            $store = StoreOperationalDetail::updateOrCreate(
                [
                    'business_id' => $validated['business_id']
                ],
                [
                    'delivery_type' => $validated['delivery_type'],
                    'delivery_radius' => $validated['delivery_radius'] ?? null,
                    'serviceable_pincode' => $validated['serviceable_pincode'],
                    'status' => $validated['status'],
                ]
            );

            // Remove old timings
            $store->timings()->delete();

            // Save new timings
            foreach ($validated['timings'] as $timing) {

                $store->timings()->create([
                    'opening_time' => $timing['opening_time'],
                    'closing_time' => $timing['closing_time'],
                ]);
            }


            $business = Business::find($validated['business_id']);
            if ($business) {

                $updateData = [];

                if ($request->filled('shop_status')) {
                    $updateData['shop_status'] = $request->shop_status;
                }

                if ($request->has('working_days')) {
                    $updateData['working_days'] = $request->working_days; // if cast to array
                }

                if (!empty($updateData)) {
                    $business->update($updateData);
                }
            }


            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Saved successfully',
                'data' => new StoreOperationalResource(
                    $store->load('timings')
                )
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

            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid ID'
                ], 400);
            }

            $id = $decoded[0];


            // Decode business_id
            $decodedbusinessid = Hashids::decode($request->business_id);

            if (empty($decodedbusinessid)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid business ID'
                ], 400);
            }

            $request->merge([
                'business_id' => $decodedbusinessid[0]
            ]);



            $store = StoreOperationalDetail::with('timings')->find($id);

            if (!$store) {
                return response()->json([
                    'status' => false,
                    'message' => 'Store not found'
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | Normalize Serviceable Pincode
            |--------------------------------------------------------------------------
            */

            $pincodes = $request->serviceable_pincode;

            if (!empty($pincodes)) {

                if (!is_array($pincodes)) {

                    $pincodes = explode(',', $pincodes);

                } else {

                    $pincodes = collect($pincodes)
                        ->flatMap(fn($item) => explode(',', $item))
                        ->map(fn($item) => trim($item))
                        ->filter()
                        ->values()
                        ->toArray();
                }

                $request->merge([
                    'serviceable_pincode' => $pincodes
                ]);
            }

            $validated = $request->validate([
                'business_id' => 'required|exists:businesses,id',

                'delivery_type' => 'required|in:self,platform,both',
                'delivery_radius' => 'nullable|numeric|min:0',

                'serviceable_pincode' => 'nullable|array',
                'serviceable_pincode.*' => 'digits:6',

                'status' => 'required|boolean',

                'timings' => 'required|array|min:1',
                'timings.*.opening_time' => 'required|date_format:H:i',
                'timings.*.closing_time' => 'required|date_format:H:i',


                'shop_status' => 'required|in:open,closed',
                'working_days' => 'required|array',
                'working_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            ]);

            // Validate timings
            foreach ($validated['timings'] as $index => $timing) {

                if (strtotime($timing['closing_time']) <= strtotime($timing['opening_time'])) {

                    return response()->json([
                        'status' => false,
                        'message' => 'Validation error',
                        'errors' => [
                            "timings.$index.closing_time" => [
                                'Closing time must be after opening time.'
                            ]
                        ]
                    ], 422);
                }
            }

            // Convert pincode array to string
            $validated['serviceable_pincode'] = !empty($validated['serviceable_pincode'])
                ? implode(',', $validated['serviceable_pincode'])
                : null;

            // Update store
            $store->update([
                'delivery_type' => $validated['delivery_type'],
                'delivery_radius' => $validated['delivery_radius'] ?? null,
                'serviceable_pincode' => $validated['serviceable_pincode'],
                'status' => $validated['status'],
            ]);

            // Replace timings
            $store->timings()->delete();

            foreach ($validated['timings'] as $timing) {

                $store->timings()->create([
                    'opening_time' => $timing['opening_time'],
                    'closing_time' => $timing['closing_time'],
                ]);
            }

            $business = Business::find($validated['business_id']);
            if ($business) {

                $updateData = [];

                if ($request->filled('shop_status')) {
                    $updateData['shop_status'] = $request->shop_status;
                }

                if ($request->has('working_days')) {
                    $updateData['working_days'] = $request->working_days; // if cast to array
                }

                if (!empty($updateData)) {
                    $business->update($updateData);
                }
            }


            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Updated successfully',
                'data' => new StoreOperationalResource(
                    $store->load('timings')
                )
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
