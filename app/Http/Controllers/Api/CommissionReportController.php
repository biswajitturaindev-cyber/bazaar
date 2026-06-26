<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommissionReportResource;
use App\Http\Resources\OrderResource;
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

            $businessId = decodeIdOrFail(
                $request->business_id,
                'Invalid business ID'
            );

            $query = Order::with([
                'items' => function ($query) {
                    $query->where('status', 'confirmed');
                }
            ])
            ->where('business_id', $businessId)
            ->where('order_status', 1);

            switch ($request->type) {

                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;

                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;

                case 'fy':

                    [$startYear, $endYear] = explode('-', $request->financial_year);

                    $query->whereBetween('created_at', [
                        Carbon::create($startYear, 4, 1)->startOfDay(),
                        Carbon::create('20'.$endYear, 3, 31)->endOfDay(),
                    ]);

                    break;

                case 'custom':

                    $query->whereBetween('created_at', [
                        Carbon::parse($request->from_date)->startOfDay(),
                        Carbon::parse($request->to_date)->endOfDay(),
                    ]);

                    break;
            }

            $orders = $query->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'Commission Report',
                'data' => CommissionReportResource::collection($orders),

                'summary' => [
                    'total_orders' => $orders->count(),
                    'total_items' => $orders->sum(fn($order) => $order->items->count()),
                    'total_order_amount' => number_format(
                        $orders->sum(fn($order) => $order->items->sum('subtotal')),
                        2,
                        '.',
                        ''
                    ),
                    'total_commission_percent' => number_format(
                        $orders->sum(fn($order) => $order->items->sum('product_commission')),
                        2,
                        '.',
                        ''
                    ),
                    'total_commission_amount' => number_format(
                        $orders->sum(function ($order) {
                            return $order->items->sum(function ($item) {
                                return ($item->subtotal * $item->product_commission) / 100;
                            });
                        }),
                        2,
                        '.',
                        ''
                    ),
                ]
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
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
                'success' => false,
                'message' => 'Something went wrong.',
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

    /**
     * Display the invoice details for a specific order.
     */
    public function invoiceDetails($id)
    {
        try {

            $orderId = decodeIdOrFail(
                $id,
                'Invalid order ID'
            );

            $order = Order::with([
                'business',
                'businessCategory',

                'items' => function ($query) {
                    $query->where('status', 'confirmed');
                },
                
                'items.attributes',
                'items.cancelReason',
                'items.variant.images',

                'addresses',
                'statusHistories',
            ])->findOrFail($orderId);

            /*
            |--------------------------------------------------------------------------
            | Load Commission & Vendor Commission
            |--------------------------------------------------------------------------
            */

            $modelMap = config('product.model_map');

            $productModel = $modelMap[$order->business_category_id] ?? null;

            if ($productModel && $order->items->isNotEmpty()) {

                $products = $productModel::select(
                        'id',
                        'commission',
                        'vendor_commission'
                    )
                    ->whereIn(
                        'id',
                        $order->items->pluck('product_id')->unique()
                    )
                    ->get()
                    ->keyBy('id');

                foreach ($order->items as $item) {

                    $product = $products->get($item->product_id);

                    $item->commission = $product->commission ?? 0;
                    $item->vendor_commission = $product->vendor_commission ?? 0;
                }
            }

            return response()->json([
                'success' => true,
                'data' => new OrderResource($order),
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
