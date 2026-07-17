<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommissionSettlementOrder;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class CommissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'business_id' => 'required|string',
        ]);

        $decoded = Hashids::decode($request->business_id);

        if (empty($decoded)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid business ID.',
            ], 422);
        }

        $businessId = $decoded[0];

        $query = CommissionSettlementOrder::where('business_id', $businessId)
            ->where('status', CommissionSettlementOrder::STATUS_PENDING);

        $commissionAmount = (clone $query)->sum('commission_amount');
        $platformCharge = (clone $query)->sum('platform_charge');
        $settlementOrderAmount = (clone $query)->sum('settlement_order_amount');
        $pendingOrders = (clone $query)->count();

        $settlementAmount = 5.00; // Fixed settlement fee
        $totalPayable = $settlementOrderAmount + $settlementAmount;

        $data = [
            'commission_amount'      => (float) $commissionAmount,
            'platform_charge'        => (float) $platformCharge,
            'settlement_order_amount'=> (float) $settlementOrderAmount,
            'settlement_amount'      => (float) $settlementAmount,
            'total_payable'          => (float) $totalPayable,
            'pending_orders'         => $pendingOrders,
        ];

        return response()->json([
            'status'  => true,
            'message' => 'Payable commission fetched successfully.',
            'data'    => $data,
        ]);
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
