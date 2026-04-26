<?php

namespace App\Http\Controllers\Api\v1\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAttribute;
use App\Models\Cart;
use Illuminate\Support\Str;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    /**
     *  Place Order (Cart → Order)
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'user_id' => 'required'
            ]);

            $userId = decodeIdOrFail($request->user_id, 'Invalid user ID');
            $cartItems = Cart::with('attributes')
                ->where('user_id', $userId)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            // Calculate totals
            $subTotal = $cartItems->sum('total');
            $handlingCharge = 5;
            $deliveryCharge = $subTotal >= 100 ? 0 : 30;
            $grandTotal = $subTotal + $handlingCharge + $deliveryCharge;

            // Generate order number
            $lastOrder = Order::latest()->first();
            $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
            $orderNo = 'ORD-' . now()->format('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Create order
            $order = Order::create([
                'order_no' => $orderNo,
                'user_id' => $userId,
                'sub_total' => $subTotal,
                'handling_charge' => $handlingCharge,
                'delivery_charge' => $deliveryCharge,
                'grand_total' => $grandTotal,
                'total_items' => $cartItems->count(),
                'status' => 'pending'
            ]);

            // Move cart → order_items
            foreach ($cartItems as $cart) {

                $orderItem = $order->items()->create([
                    'product_id'   => $cart->product_id,
                    'product_type' => $cart->product_type,
                    'product_name' => $cart->product_name,
                    'image'        => $cart->image,
                    'quantity'     => $cart->quantity,
                    'price'        => $cart->price,
                    'total'        => $cart->total,
                ]);

                // Move attributes
                foreach ($cart->attributes as $attr) {
                    $orderItem->attributes()->create([
                        'attribute_name'  => $attr->attribute_name,
                        'attribute_value' => $attr->attribute_value,
                        'price'           => $attr->price,
                    ]);
                }
            }

            // Clear cart
            Cart::where('user_id', $userId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data'    => new OrderResource($order->load('items.attributes'))
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }

    /**
     * Order List
     */
    public function index(Request $request)
    {
        $userId = decodeIdOrFail($request->user_id, 'Invalid user ID');

        $orders = Order::with('items.attributes')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders)
        ]);
    }

    /**
     * Order Details
     */
    public function show($id)
    {
        $orderId = decodeIdOrFail($id, 'Invalid order ID');

        $order = Order::with('items.attributes')->findOrFail($orderId);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order)
        ]);
    }

    /**
     * Update Order Status (Admin / optional)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,delivered,cancelled'
        ]);

        $orderId = decodeIdOrFail($id, 'Invalid order ID');

        $order = Order::findOrFail($orderId);

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated',
            'data' => new OrderResource($order)
        ]);
    }
}
