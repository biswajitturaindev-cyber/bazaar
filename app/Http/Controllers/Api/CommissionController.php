<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommissionSettlementOrder;
use App\Models\CommissionSettlementTransaction;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $request->validate([
            'business_id'             => 'required|string',
            'amount'                  => 'required|numeric|min:1',
            'payment_mode'            => 'required|in:wallet,bank_transfer,upi,cash',
            'payment_transaction_no'  => 'nullable|string|max:255',
            'payment_reference_no'    => 'nullable|string|max:255',
            'remarks'                 => 'nullable|string|max:500',
        ]);

        $decoded = Hashids::decode($request->business_id);

        if (empty($decoded)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid business ID.',
            ], 422);
        }

        $businessId = $decoded[0];

        DB::beginTransaction();

        try {

            $orders = CommissionSettlementOrder::where('business_id', $businessId)
                ->where('status', CommissionSettlementOrder::STATUS_PENDING)
                ->lockForUpdate()
                ->get();

            if ($orders->isEmpty()) {

                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => 'No payable commission found.',
                ], 422);
            }

            $payableCommission = $orders->sum('commission_amount');
            $payableAmount = $orders->sum('settlement_order_amount');

            // Vendor paid amount
            $paidAmount = $request->amount;

            if ($paidAmount < $payableAmount) {

                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => 'Paid amount cannot be less than payable amount.',
                ], 422);
            }

            $extraAmount = $paidAmount - $payableAmount;

            // Generate transaction number
            $transactionNo = 'CST' . now()->format('YmdHis') . rand(1000, 9999);

            // Create settlement transaction
            $transaction = CommissionSettlementTransaction::create([
                'business_id'            => $businessId,
                'transaction_no'         => $transactionNo,
                'payable_commission'     => $payableCommission,
                'settlement_amount'      => $paidAmount,
                'payment_mode'           => $request->payment_mode,
                'payment_transaction_no' => $request->payment_transaction_no,
                'payment_reference_no'   => $request->payment_reference_no,
                'remarks'                => $request->remarks,
                'status'                 => 'pending',
            ]);

            // Update settlement orders
            CommissionSettlementOrder::whereIn('id', $orders->pluck('id'))
                ->update([
                    'settlement_transaction_id' => $transaction->id,
                    'status' => CommissionSettlementOrder::STATUS_PROCESSING,
                ]);

            // Save excess amount as Deposit
            if ($extraAmount > 0) {
                $paymentMethod = match ($request->payment_mode) {
                    'upi'             => 1,
                    'bank_transfer'   => 2,
                    'wallet', 'cash'  => 3,
                    default           => null,
                };

                Deposit::create([
                    'business_id'    => $businessId,
                    'amount'         => $extraAmount,
                    'transaction_id' => $request->payment_transaction_no,
                    'ref_id'         => $transactionNo,
                    'payment_method' => $paymentMethod,
                    'status'         => 0, // Pending
                    'user_note'      => 'Extra amount received during commission settlement.',
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Commission settlement request submitted successfully.',
                'data' => [
                    'transaction_id'       => Hashids::encode($transaction->id),
                    'transaction_no'       => $transactionNo,
                    'payable_commission'   => number_format($payableCommission, 2, '.', ''),
                    'payable_amount'       => number_format($payableAmount, 2, '.', ''),
                    'paid_amount'          => number_format($paidAmount, 2, '.', ''),
                    'extra_deposit_amount' => number_format($extraAmount, 2, '.', ''),
                    'status'               => $transaction->status,
                ]
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
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
