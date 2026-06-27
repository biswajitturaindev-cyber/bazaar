<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\MemberLoyaltyWallet;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\VendorLoyaltyWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     try {

    //         $query = Order::with([
    //             'business',
    //             'businessCategory',

    //             'items',
    //             'items.attributes',
    //             'items.cancelReason',

    //             'addresses',
    //             'statusHistories',
    //         ]);

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Filter By Business
    //         |--------------------------------------------------------------------------
    //         */

    //         if ($request->filled('business_id')) {

    //             $decoded = decodeIdOrFail(
    //                 $request->business_id,
    //                 'Invalid business ID'
    //             );

    //             $query->where(
    //                 'business_id',
    //                 $decoded
    //             );
    //         }

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Orders
    //         |--------------------------------------------------------------------------
    //         */

    //         $orders = $query
    //             ->latest()
    //             ->get();

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Load Commission & Vendor Commission
    //         |--------------------------------------------------------------------------
    //         */

    //         $modelMap = config('product.model_map');

    //         foreach ($orders as $order) {

    //             $productModel = $modelMap[$order->business_category_id] ?? null;

    //             if (!$productModel || $order->items->isEmpty()) {
    //                 continue;
    //             }

    //             $products = $productModel::select(
    //                     'id',
    //                     'commission',
    //                     'vendor_commission'
    //                 )
    //                 ->whereIn(
    //                     'id',
    //                     $order->items->pluck('product_id')->unique()
    //                 )
    //                 ->get()
    //                 ->keyBy('id');

    //             foreach ($order->items as $item) {

    //                 $product = $products->get($item->product_id);

    //                 $item->commission = $product->commission ?? 0;
    //                 $item->vendor_commission = $product->vendor_commission ?? 0;
    //             }
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'data' => OrderResource::collection($orders)
    //         ]);

    //     } catch (\Exception $e) {

    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function index(Request $request)
    {
        try {

            $query = Order::with([
                'business',
                'businessCategory',
                'items',
                'items.attributes',
                'items.cancelReason',
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

                $query->where('business_id', $decoded);
            }

            /*
            |--------------------------------------------------------------------------
            | Search
            |--------------------------------------------------------------------------
            */

            if ($request->filled('search')) {

                $search = trim($request->search);

                $query->where(function ($q) use ($search) {

                    $q->where('order_no', 'like', "%{$search}%")
                    ->orWhere('invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('items', function ($itemQuery) use ($search) {
                        $itemQuery->where('product_name', 'like', "%{$search}%")
                                    ->orWhere('sku', 'like', "%{$search}%");
                    });

                });
            }

            /*
            |--------------------------------------------------------------------------
            | Pagination
            |--------------------------------------------------------------------------
            */

            $perPage = (int) $request->input('per_page', 10);

            $orders = $query
                ->latest()
                ->paginate($perPage);

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
                'message' => 'Orders fetched successfully.',
                'data' => OrderResource::collection($orders),

                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page'     => $orders->perPage(),
                    'total'        => $orders->total(),
                    'last_page'    => $orders->lastPage(),
                    'from'         => $orders->firstItem(),
                    'to'           => $orders->lastItem(),
                    'has_more'     => $orders->hasMorePages(),
                ]
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
        try {
            $orderId = decodeIdOrFail(
                $id,
                'Invalid order ID'
            );

            $data = $request->validate([
                'order_status' => 'required|in:0,1,2,3,4,5',
                'remarks' => 'nullable|string',
                'tracking_id' => 'nullable|string',
                'delivery_partner_id' => 'nullable|integer',
                'delivery_partner_name' => 'nullable|string',
            ]);

            $order = Order::findOrFail($orderId);

            $order->update([
                'order_status' => $data['order_status']
            ]);

            // If order confirmed, confirm all pending items
            if ((int) $data['order_status'] === 1) {

                $order->items()
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'confirmed'
                    ]);


                /*
                |--------------------------------------------------------------------------
                | Member Loyalty Wallet
                |--------------------------------------------------------------------------
                */

                if ($order->user_id) {

                    $lastWallet = MemberLoyaltyWallet::where('member_id', $order->user_id)
                        ->latest('id')
                        ->first();

                    $opening = $lastWallet?->closing_points ?? 0;

                   $points = $order->items()
                            ->where('status', 'confirmed')
                            ->sum('subtotal');

                    if (!MemberLoyaltyWallet::where('order_id', $order->id)->exists()) {

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
                }

                /*
                |--------------------------------------------------------------------------
                | Vendor Loyalty Wallet
                |--------------------------------------------------------------------------
                */

                if ($order->business_id) {

                    if (!VendorLoyaltyWallet::where('order_id', $order->id)->exists()) {

                        $lastWallet = VendorLoyaltyWallet::where('business_id', $order->business_id)
                            ->latest('id')
                            ->first();

                        $opening = $lastWallet?->closing_points ?? 0;

                        $vendorPoints = $order->items()
                            ->where('status', OrderItem::STATUS_CONFIRMED)
                            ->sum('subtotal');

                        $closing = $opening + $vendorPoints;

                        VendorLoyaltyWallet::create([
                            'business_id'      => $order->business_id,
                            'order_id'         => $order->id,
                            'transaction_no'   => 'VLW-' . now()->format('YmdHis') . '-' . $order->id,
                            'transaction_type' => 'credit',
                            'source'           => 'order',
                            'points'           => $vendorPoints,
                            'opening_points'   => $opening,
                            'closing_points'   => $closing,
                            'remarks'          => 'Vendor commission for Order ' . $order->order_no,
                            'status'           => 'approved',
                        ]);
                    }
                }
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $data['order_status'],
                'tracking_id' => $data['tracking_id'] ?? null,
                'delivery_partner_id' => $data['delivery_partner_id'] ?? null,
                'delivery_partner_name' => $data['delivery_partner_name'] ?? null,
                'remarks' => $data['remarks'] ?? null,
            ]);

            $order->load([
                'business',
                'businessCategory',
                'items',
                'items.attributes',
                'addresses',
                'statusHistories',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => new OrderResource($order),
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order',
                'error' => $e->getMessage()

            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function modifyItemQuantity(Request $request)
    {
        $request->validate([
            'order_item_id'     => 'required',
            'modified_quantity' => 'required|integer|min:1',
        ]);

        $orderItemId = decodeIdOrFail(
            $request->order_item_id,
            'Invalid order item ID'
        );

        $orderItem = OrderItem::findOrFail($orderItemId);

        // Create log
        // OrderItemQuantityLog::create([
        //     'order_item_id'   => $orderItem->id,
        //     'order_id'        => $orderItem->order_id,
        //     'old_quantity'    => $orderItem->modified_quantity ?? $orderItem->quantity,
        //     'new_quantity'    => $request->modified_quantity,
        //     'updated_by_type' => 'vendor',
        //     'updated_by_id'   => auth()->id(),
        //     'remarks'         => 'Quantity modified by vendor',
        // ]);

        // Update quantity
        $orderItem->update([
            'modified_quantity' => $request->modified_quantity,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Quantity updated successfully',
            'data'    => [
                'order_item_id'     => $request->order_item_id,
                'modified_quantity' => $request->modified_quantity,
            ]
        ]);
    }

    /**
     * Cancel Item
     */
    public function cancelItem(Request $request)
    {
        $request->validate([
            'order_item_id'    => 'required',
            'cancel_reason_id' => 'required',
            'cancel_note'      => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {

            $orderItemId = decodeIdOrFail(
                $request->order_item_id,
                'Invalid order item ID'
            );

            $cancelReasonId = decodeIdOrFail(
                $request->cancel_reason_id,
                'Invalid cancel reason ID'
            );

            $orderItem = OrderItem::findOrFail($orderItemId);

            if ($orderItem->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Item already cancelled',
                ], 422);
            }

            // Cancel current item
            $orderItem->update([
                'status'           => 'cancelled',
                'cancel_reason_id' => $cancelReasonId,
                'cancel_note'      => $request->cancel_note,
                'cancelled_by'     => 'vendor',
                'cancelled_at'     => now(),
            ]);

            // Check if any active items remain
            $activeItemsCount = OrderItem::where('order_id', $orderItem->order_id)
                ->where('status', '!=', 'cancelled')
                ->count();

            // If all items cancelled, cancel the order
            if ($activeItemsCount === 0) {

                Order::where('id', $orderItem->order_id)
                    ->update([
                        'order_status'     => 5, // Cancelled
                        'cancel_reason_id' => $cancelReasonId,
                        'cancel_note'      => $request->cancel_note,
                        'cancelled_at'     => now(),
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order item cancelled successfully',
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel item',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function invoice($encoded_id)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Decode Order ID
            |--------------------------------------------------------------------------
            */
            $orderId = decodeIdOrFail($encoded_id);

            /*
            |--------------------------------------------------------------------------
            | Get Order
            |--------------------------------------------------------------------------
            */
            $order = Order::with([
                'items' => function ($query) {
                    $query->where('status', '!=', 'cancelled');
                },
                'addresses',
                'business',
            ])->findOrFail($orderId);

            /*
            |--------------------------------------------------------------------------
            | Get Product Model
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
                    ->whereIn('id', $order->items->pluck('product_id')->unique())
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
            | Get Address & Business
            |--------------------------------------------------------------------------
            */
            $address = $order->addresses->first();
            $business = $order->business;
            /*
            |--------------------------------------------------------------------------
            | Generate PDF
            |--------------------------------------------------------------------------
            */
            $pdf = Pdf::loadView(
                'pdf.order-invoice',
                compact(
                    'order',
                    'address',
                    'business'
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Stream PDF
            |--------------------------------------------------------------------------
            */
            $filename = str_replace(['/', '\\'], '-', $order->invoice_no);

            return $pdf->stream($filename . '.pdf');

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
