<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
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

}
