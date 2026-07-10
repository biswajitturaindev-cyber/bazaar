<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\SalesReportResource;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SalesReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $request->validate([
                'business_id' => 'required',
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

            /*
            |--------------------------------------------------------------------------
            | Date Filter
            |--------------------------------------------------------------------------
            */

            switch ($request->type) {

                case 'today':

                    $query->whereDate('created_at', Carbon::today());

                    break;

                case 'month':

                    $query->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year);

                    break;

                case 'fy':

                    [$startYear, $endYear] = explode('-', $request->financial_year);

                    $query->whereBetween('created_at', [
                        Carbon::create($startYear, 4, 1)->startOfDay(),
                        Carbon::create('20' . $endYear, 3, 31)->endOfDay(),
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

            /*
            |--------------------------------------------------------------------------
            | Load Commission & Vendor Commission
            |--------------------------------------------------------------------------
            */

            $modelMap = config('product.model_map');

            foreach ($orders as $order) {

                $productModel = $modelMap[$order->business_category_id] ?? null;

                if (!$productModel || $order->items->isEmpty()) {
                    continue;
                }

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

            /*
            |--------------------------------------------------------------------------
            | Group By Member
            |--------------------------------------------------------------------------
            */

            $salesReport = $orders->groupBy('user_id');

            return response()->json([
                'success' => true,
                'message' => 'Sales Report',
                'data' => SalesReportResource::collection($salesReport),
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            Log::error('Sales Report Error', [
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
     * Display the order details for a specific member.
     */
    public function orderDetails(Request $request, $memberId)
    {
        try {

            $request->validate([
                'business_id' => 'required',
                'type' => 'required|in:today,month,fy,custom',
                'financial_year' => 'required_if:type,fy',
                'from_date' => 'required_if:type,custom|date',
                'to_date' => 'required_if:type,custom|date|after_or_equal:from_date',
            ]);

            $businessId = decodeIdOrFail(
                $request->business_id,
                'Invalid business ID'
            );

            // $userId = decodeIdOrFail(
            //     $memberId,
            //     'Invalid Member ID'
            // );

            $userId = $memberId;


            $query = Order::with([
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
            ])
            ->where('business_id', $businessId)
            ->where('user_id', $userId)
            ->where('order_status', 1);

            switch ($request->type) {

                case 'today':

                    $query->whereDate('created_at', today());

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

            /*
            |--------------------------------------------------------------------------
            | Load Commission & Vendor Commission
            |--------------------------------------------------------------------------
            */

            $modelMap = config('product.model_map');

            foreach ($orders as $order) {

                $productModel = $modelMap[$order->business_category_id] ?? null;

                if (!$productModel || $order->items->isEmpty()) {
                    continue;
                }

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
                'message' => 'Order Details',
                'data' => OrderResource::collection($orders),
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            Log::error('Sales Order Details Error', [
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

}
