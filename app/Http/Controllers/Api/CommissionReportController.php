<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CommissionReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        public function index(Request $request)
    {
        try {

            $request->validate([
                'type' => 'required|in:today,month,fy,custom',
                'financial_year' => 'required_if:type,fy',
                'from_date' => 'required_if:type,custom|date',
                'to_date' => 'required_if:type,custom|date|after_or_equal:from_date',
            ]);

            $orders = Order::with([
                'items' => function ($query) {
                    $query->where('status', 'confirmed');
                }
            ]);

            switch ($request->type) {

                case 'today':

                    $orders->whereDate('created_at', Carbon::today());

                    break;

                case 'month':

                    $orders->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year);

                    break;

                case 'fy':

                    [$startYear, $endYear] = explode('-', $request->financial_year);

                    $startDate = Carbon::create($startYear, 4, 1)->startOfDay();

                    $endDate = Carbon::create('20' . $endYear, 3, 31)->endOfDay();

                    $orders->whereBetween('created_at', [
                        $startDate,
                        $endDate
                    ]);

                    break;

                case 'custom':

                    $orders->whereBetween('created_at', [
                        Carbon::parse($request->from_date)->startOfDay(),
                        Carbon::parse($request->to_date)->endOfDay()
                    ]);

                    break;
            }

            $orders = $orders->latest()->get();

            $report = [];

            $slNo = 1;

            $grandTotalItems = 0;
            $grandOrderAmount = 0;
            $grandCommissionPercent = 0;
            $grandCommissionAmount = 0;

            foreach ($orders as $order) {

                $items = $order->items;

                if ($items->isEmpty()) {
                    continue;
                }

                // Total Confirmed Items
                $totalItems = $items->count();

                // Sum of confirmed order item subtotal
                $orderAmount = $items->sum('subtotal');

                // Sum of product commission %
                $commissionPercent = $items->sum('product_commission');

                // Commission Amount
                $commissionAmount = 0;

                foreach ($items as $item) {

                    $commissionAmount +=
                        ($item->subtotal * $item->product_commission) / 100;
                }

                // Grand Totals
                $grandTotalItems += $totalItems;
                $grandOrderAmount += $orderAmount;
                $grandCommissionPercent += $commissionPercent;
                $grandCommissionAmount += $commissionAmount;

                $report[] = [

                    'sl_no' => $slNo++,

                    'invoice_no' => $order->invoice_no,

                    'total_items' => $totalItems,

                    'order_amount' => number_format($orderAmount, 2, '.', ''),

                    'commission_percent' => number_format($commissionPercent, 2, '.', ''),

                    'commission_amount' => number_format($commissionAmount, 2, '.', ''),
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Commission Report',
                'data' => $report,
                'summary' => [
                    'total_orders' => count($report),
                    'total_items' => $grandTotalItems,
                    'total_order_amount' => number_format($grandOrderAmount, 2, '.', ''),
                    'total_commission_percent' => number_format($grandCommissionPercent, 2, '.', ''),
                    'total_commission_amount' => number_format($grandCommissionAmount, 2, '.', ''),
                ]
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            Log::error('Commission Report Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(), // Remove in production
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
