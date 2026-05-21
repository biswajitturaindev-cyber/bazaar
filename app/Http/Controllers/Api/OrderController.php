<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $query = Order::with([
                'business',
                'businessCategory',

                'items',
                'items.attributes',

                'addresses',

                'statusHistories',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Filter By Business
            |--------------------------------------------------------------------------
            */

            if ($request->filled('business_id')) {

                $decoded = decodeIdOrFail(
                    $request->business_id,
                    'Invalid business ID'
                );

                $query->where(
                    'business_id',
                    $decoded
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Orders
            |--------------------------------------------------------------------------
            */

            $orders = $query
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'data' => OrderResource::collection($orders)
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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
