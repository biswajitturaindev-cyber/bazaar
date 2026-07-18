<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionSettlementOrder;
use App\Models\CommissionSettlementTransaction;
use App\Models\MemberLoyaltyWallet;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\VendorLoyaltyWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorCommissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vendorCommissions = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('businesses', 'businesses.id', '=', 'orders.business_id')
            ->leftJoin('users', 'users.id', '=', 'businesses.user_id');

        /*
        |--------------------------------------------------------------------------
        | Apply Only One Filter
        |--------------------------------------------------------------------------
        */

        // Today
        if ($request->filter_type == 'today') {

            $vendorCommissions->whereDate('orders.created_at', today());

        }
        // Current Month
        elseif ($request->filter_type == 'month') {

            $vendorCommissions->whereMonth('orders.created_at', now()->month)
                ->whereYear('orders.created_at', now()->year);

        }
        // Financial Year
        elseif ($request->filled('financial_year')) {

            [$startYear, $endYear] = explode('-', $request->financial_year);

            $vendorCommissions->whereBetween('orders.created_at', [
                $startYear . '-04-01 00:00:00',
                $endYear . '-03-31 23:59:59',
            ]);

        }
        // Date Range
        elseif (
            $request->filled('from_date')
            && $request->filled('to_date')
        ) {

            $vendorCommissions->whereBetween('orders.created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59',
            ]);

        }
        $vendorCommissions = $vendorCommissions
            ->where('orders.order_status', '=', 1);

        $vendorCommissions = $vendorCommissions
            ->select(
                'businesses.id as business_id',
                'businesses.business_name',
                'users.vendor_id',
                'users.name as vendor_name',

                DB::raw('COUNT(DISTINCT orders.id) as invoice_count'),

                DB::raw('SUM(order_items.subtotal) as total_sale'),

                DB::raw('AVG(order_items.product_commission) as commission_percentage'),

                DB::raw('SUM(
                    (order_items.subtotal * order_items.product_commission) / 100
                ) as commission_amount')
            )
            ->groupBy(
                'businesses.id',
                'businesses.business_name',
                'users.vendor_id',
                'users.name'
            )
            ->orderBy('businesses.business_name')
            ->get();

        return view(
            'admin.vendor-commissions.index',
            compact('vendorCommissions')
        );
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

    /*
    public function invoiceList(Request $request, $businessId)
    {
        $query = Order::query()
            ->where('business_id', $businessId);

        // Today
        if ($request->filter_type == 'today') {

            $query->whereDate('created_at', today());

        }
        // Current Month
        elseif ($request->filter_type == 'month') {

            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);

        }
        // Financial Year
        elseif ($request->filled('financial_year')) {

            [$startYear, $endYear] = explode('-', $request->financial_year);

            $query->whereBetween('created_at', [
                $startYear . '-04-01 00:00:00',
                $endYear . '-03-31 23:59:59',
            ]);

        }
        // Date Range
        elseif ($request->filled('from_date') && $request->filled('to_date')) {

            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59',
            ]);
        }

        $invoices = $query
            ->with(['items'])
            ->latest()
            ->get()
            ->map(function ($order) {

                $commissionAmount = $order->items->sum(function ($item) {
                    return ($item->subtotal * $item->product_commission) / 100;
                });

                $avgCommission = $order->items->avg('product_commission');

                return [
                    'id' => $order->id,
                    'invoice_no' => $order->invoice_no,
                    'order_no' => $order->order_no,
                    'grand_total' => $order->grand_total,
                    'created_at' => $order->created_at->format('d M Y h:i A'),
                    'product_commission' => round($avgCommission, 2),
                    'commission_amount' => round($commissionAmount, 2),
                ];
            });

        return response()->json([
            'summary' => [
                'invoice_count' => $invoices->count(),
                'total_sale' => $invoices->sum('grand_total'),
                'total_commission' => $invoices->sum('commission_amount'),
            ],
            'data' => $invoices
        ]);
    }
    */

    public function invoiceList(Request $request, $businessId)
    {
        $query = Order::query()
            ->where('business_id', $businessId)
            ->with([
                'items',
                'user',
                'addresses'
            ]);

        // Today
        if ($request->filter_type == 'today') {

            $query->whereDate('created_at', today());

        }
        // Current Month
        elseif ($request->filter_type == 'month') {

            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);

        }
        // Financial Year
        elseif ($request->filled('financial_year')) {

            [$startYear, $endYear] = explode('-', $request->financial_year);

            $query->whereBetween('created_at', [
                $startYear . '-04-01 00:00:00',
                $endYear . '-03-31 23:59:59',
            ]);

        }
        // Date Range
        elseif (
            $request->filled('from_date') &&
            $request->filled('to_date')
        ) {

            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59',
            ]);
        }

        $orders = $query
            ->latest()
            ->get();

        $invoiceData = $orders->map(function ($order) {

            $commissionAmount = $order->items->sum(function ($item) {
                return ($item->subtotal * $item->product_commission) / 100;
            });

            $avgCommission = $order->items->avg('product_commission');

            return [

                'id' => $order->id,

                'invoice_no' => $order->invoice_no,

                'order_no' => $order->order_no,

                'created_at' => $order->created_at->format('d M Y h:i A'),


                'grand_total' => (float) $order->grand_total,

                'payment_status' => $order->payment_status,

                'order_status' => $order->order_status,

                'product_commission' => round($avgCommission, 2),

                'commission_amount' => round($commissionAmount, 2),

                'total_items' => $order->items->count(),

                'items' => $order->items->map(function ($item) {

                    return [
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'selling_price' => (float) $item->selling_price,
                        'subtotal' => (float) $item->subtotal,
                        'commission_percent' => (float) $item->product_commission,
                        'commission_amount' => round(
                            ($item->subtotal * $item->product_commission) / 100,
                            2
                        ),

                    ];

                })->values(),

            ];

        });

        return response()->json([

            'summary' => [

                'invoice_count' => $invoiceData->count(),

                'total_sale' => $invoiceData->sum('grand_total'),

                'total_commission' => $invoiceData->sum('commission_amount'),

            ],

            'data' => $invoiceData,

        ]);
    }

    public function paymentPendingList(Request $request)
    {
        if ($request->ajax()) {

            $transactions = CommissionSettlementTransaction::with('business')
                ->where('status', 'pending')
                ->latest()
                ->get();

            $data = [];

            foreach ($transactions as $key => $transaction) {

                    $data[] = [
                        // '',
                        $transaction->transaction_no,
                        optional($transaction->business)->business_name,
                        number_format($transaction->payable_commission, 2),
                        number_format($transaction->settlement_amount, 2),
                        ucfirst(str_replace('_', ' ', $transaction->payment_mode)),
                        '
                        <select class="form-control form-control-sm change-status"
                            data-id="'.$transaction->id.'"
                            data-old-status="'.$transaction->status.'">
                            <option value="pending" '.($transaction->status == 'pending' ? 'selected' : '').'>Pending</option>
                            <option value="approved" '.($transaction->status == 'approved' ? 'selected' : '').'>Approved</option>
                            <option value="rejected" '.($transaction->status == 'rejected' ? 'selected' : '').'>Rejected</option>
                            <option value="paid" '.($transaction->status == 'paid' ? 'selected' : '').'>Paid</option>
                        </select>
                        ',
                        $transaction->created_at->format('d-m-Y h:i A'),
                    ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => count($data),
                'recordsFiltered' => count($data),
                'data' => $data,
            ]);
        }

        return view('admin.vendor-commissions.payment_pending_list');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:commission_settlement_transactions,id',
            'status' => 'required|in:pending,approved,rejected,paid',
        ]);

        DB::beginTransaction();

        try {

            $transaction = CommissionSettlementTransaction::findOrFail($request->id);

            $transaction->update([
                'status' => $request->status,
            ]);

            // Credit loyalty only when approved
            if ($request->status === CommissionSettlementTransaction::STATUS_APPROVED) {

                $settlementOrders = CommissionSettlementOrder::where(
                    'settlement_transaction_id',
                    $transaction->id
                )->get();

                foreach ($settlementOrders as $settlementOrder) {

                    $order = Order::with('items')->find($settlementOrder->order_id);

                    if (!$order) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Member Loyalty Wallet
                    |--------------------------------------------------------------------------
                    */
                    if ($order->user_id && !MemberLoyaltyWallet::where('order_id', $order->id)->exists()) {

                        $lastWallet = MemberLoyaltyWallet::where('member_id', $order->user_id)
                            ->latest('id')
                            ->first();

                        $opening = $lastWallet?->closing_points ?? 0;

                        $points = $order->items()
                            ->where('status', OrderItem::STATUS_CONFIRMED)
                            ->sum('subtotal');

                        MemberLoyaltyWallet::create([
                            'member_id'        => $order->user_id,
                            'order_id'         => $order->id,
                            'transaction_no'   => 'MLW-' . now()->format('YmdHis') . '-' . $order->id,
                            'transaction_type' => 'credit',
                            'source'           => 'order',
                            'points'           => $points,
                            'opening_points'   => $opening,
                            'closing_points'   => $opening + $points,
                            'remarks'          => 'Loyalty points for Order ' . $order->order_no,
                            'status'           => 'approved',
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Vendor Loyalty Wallet
                    |--------------------------------------------------------------------------
                    */
                    if ($order->business_id && !VendorLoyaltyWallet::where('order_id', $order->id)->exists()) {

                        $lastWallet = VendorLoyaltyWallet::where('business_id', $order->business_id)
                            ->latest('id')
                            ->first();

                        $opening = $lastWallet?->closing_points ?? 0;

                        $vendorPoints = $order->items()
                            ->where('status', OrderItem::STATUS_CONFIRMED)
                            ->sum('subtotal');

                        VendorLoyaltyWallet::create([
                            'business_id'      => $order->business_id,
                            'order_id'         => $order->id,
                            'order_type'       => 'member_order',
                            'transaction_no'   => 'VLW-' . now()->format('YmdHis') . '-' . $order->id,
                            'transaction_type' => 'credit',
                            'source'           => 'order',
                            'points'           => $vendorPoints,
                            'opening_points'   => $opening,
                            'closing_points'   => $opening + $vendorPoints,
                            'remarks'          => 'Vendor commission for Order ' . $order->order_no,
                            'status'           => 'approved',
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Status updated successfully.',
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


}
