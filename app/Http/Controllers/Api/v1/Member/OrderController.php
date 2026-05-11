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

        /*
        |--------------------------------------------------------------------------
        | Validate Request
        |--------------------------------------------------------------------------
        */
        $request->validate([
            'user_id' => 'required',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Decode User ID
        |--------------------------------------------------------------------------
        */
        $userId = decodeIdOrFail(
            $request->user_id,
            'Invalid user ID'
        );

        /*
        |--------------------------------------------------------------------------
        | Get Cart Items
        |--------------------------------------------------------------------------
        */
        $cartItems = Cart::with([
                'productVariant',
                'productVariant.stocks',
                'cartAttributes',
                'cartAttributes.attribute',
                'cartAttributes.attributeValue',
            ])
            ->where('user_id', $userId)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Empty Cart Check
        |--------------------------------------------------------------------------
        */
        if ($cartItems->isEmpty()) {

            return response()->json([
                'success' => false,
                'message' => 'Cart is empty',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate Totals
        |--------------------------------------------------------------------------
        */
        $itemsTotal = 0;
        $totalItems = 0;

        foreach ($cartItems as $cart) {

            $price = $cart->productVariant->final_price ?? 0;

            $itemsTotal += ($price * $cart->quantity);

            $totalItems += $cart->quantity;
        }

        $discountAmount = 0;

        $platformCharge = 5;

        $deliveryCharge = $itemsTotal >= 100
            ? 0
            : 30;

        $taxAmount = 0;

        $grandTotal = (
            $itemsTotal
            + $platformCharge
            + $deliveryCharge
            + $taxAmount
        ) - $discountAmount;

        /*
        |--------------------------------------------------------------------------
        | Generate Order Number
        |--------------------------------------------------------------------------
        */
        $lastOrder = Order::latest()->first();

        $nextId = $lastOrder
            ? $lastOrder->id + 1
            : 1;

        $orderNo = 'ORD-'
            . now()->format('Ymd')
            . '-'
            . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        /*
        |--------------------------------------------------------------------------
        | Create Order
        |--------------------------------------------------------------------------
        */
        $order = Order::create([

            'order_no' => $orderNo,

            'user_id' => $userId,

            'total_items' => $totalItems,

            'items_total' => $itemsTotal,

            'discount_amount' => $discountAmount,

            'platform_charge' => $platformCharge,

            'delivery_charge' => $deliveryCharge,

            'tax_amount' => $taxAmount,

            'grand_total' => $grandTotal,

            'payment_status' => 'Pending',

            'payment_method' => 'COD',

            'order_status' => 'Pending',

            'placed_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create Order Items
        |--------------------------------------------------------------------------
        */
        foreach ($cartItems as $cart) {

            /*
            |--------------------------------------------------------------------------
            | Stock Check
            |--------------------------------------------------------------------------
            */
            $availableStock = $cart->productVariant
                ? $cart->productVariant->stocks->sum('stock')
                : 0;

            if ($cart->quantity > $availableStock) {

                throw new \Exception(
                    $cart->product_name
                    . ' stock unavailable'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Variant Price
            |--------------------------------------------------------------------------
            */
            $price = $cart->productVariant->final_price ?? 0;

            /*
            |--------------------------------------------------------------------------
            | Create Order Item
            |--------------------------------------------------------------------------
            */
            $orderItem = $order->items()->create([

                'business_id' => $cart->business_id,

                'business_category_id' => $cart->business_category_id,

                'product_id' => $cart->product_id,

                'product_variant_id' => $cart->product_variant_id,

                'product_name' => $cart->product_name,

                'sku' => $cart->productVariant->sku ?? null,

                'quantity' => $cart->quantity,

                'mrp' => $cart->productVariant->mrp ?? 0,

                'selling_price' => $cart->productVariant->selling_price ?? 0,

                'discount_amount' => $cart->productVariant->discount ?? 0,

                'final_price' => $price,

                'subtotal' => ($price * $cart->quantity),

                'loyalty_points' => 0,

                /*
                |--------------------------------------------------------------------------
                | Product Snapshot
                |--------------------------------------------------------------------------
                */
                'product_snapshot' => [
                    'product_name' => $cart->product_name,
                    'sku' => $cart->productVariant->sku ?? null,
                    'mrp' => $cart->productVariant->mrp ?? 0,
                    'selling_price' => $cart->productVariant->selling_price ?? 0,
                    'final_price' => $price,
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Order Item Attributes
            |--------------------------------------------------------------------------
            */
            foreach ($cart->cartAttributes as $attribute) {

                $orderItem->attributes()->create([
                    'attribute_id' => $attribute->attribute_id,
                    'attribute_value_id' => $attribute->attribute_value_id,
                    'attribute_name' => $attribute->attribute->name ?? null,
                    'attribute_value' => $attribute->attributeValue->value ?? null,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Deduct Stock
            |--------------------------------------------------------------------------
            */
            foreach ($cart->productVariant->stocks as $stock) {

                if ($stock->stock >= $cart->quantity) {

                    $stock->decrement(
                        'stock',
                        $cart->quantity
                    );

                    break;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Clear Cart
        |--------------------------------------------------------------------------
        */
        Cart::where('user_id', $userId)->delete();

        /*
        |--------------------------------------------------------------------------
        | Load Relationships
        |--------------------------------------------------------------------------
        */
        $order->load([
            'items',
            'items.attributes',
            'addresses',
            'payments',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Success Response
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'data' => new OrderResource($order),
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {

        return response()->json([
            'success' => false,
            'message' => $e->validator->errors()->first(),
            'errors' => $e->errors(),
        ], 422);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
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
