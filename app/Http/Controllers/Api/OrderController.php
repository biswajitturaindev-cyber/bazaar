<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
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

            /*
            |--------------------------------------------------------------------------
            | Update Order
            |--------------------------------------------------------------------------
            */

            $order->update([
                'order_status' => $data['order_status']
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Status History
            |--------------------------------------------------------------------------
            */

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

        try {

            $orderItemId = decodeIdOrFail(
                $request->order_item_id,
                'Invalid order item ID'
            );

            $CancelReasonId = decodeIdOrFail(
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

            $orderItem->update([
                'status'           => 'cancelled',
                'cancel_reason_id' => $CancelReasonId,
                'cancel_note'      => $request->cancel_note,
                'cancelled_by'     => 'vendor',
                'cancelled_at'     => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order item cancelled successfully',
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel item',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


}
