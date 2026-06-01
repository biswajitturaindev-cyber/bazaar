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
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{

    /**
     * Order List
     */
    public function index(Request $request)
    {
        $userId = $request->user_id;

        $orders = Order::with([
            'business',
            'businessCategory',

            'items',
            'items.attributes',

            'addresses',
            'addresses.billingCity',
            'addresses.billingState',
            'addresses.shippingCity',
            'addresses.shippingState',

            'statusHistories',
        ])
        ->where('user_id', $userId)
        ->latest()
        ->get();

        return response()->json([
            'success' => true,
            'data'    => OrderResource::collection($orders)
        ]);
    }

    /**
     *  Place Order (Cart → Order)
     */
    // public function store(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Validate Request
    //         |--------------------------------------------------------------------------
    //         */
    //         $request->validate([
    //             'user_id' => 'required|integer',

    //             // Billing Address
    //             'billing_address' => 'required|string',
    //             'billing_city_id' => 'nullable|integer',
    //             'billing_state_id' => 'nullable|integer',
    //             'billing_pincode' => 'nullable|string|max:20',

    //             // Shipping Address
    //             'shipping_address_id' => 'nullable|integer',
    //             'shipping_address' => 'required|string',
    //             'shipping_city_id' => 'nullable|integer',
    //             'shipping_state_id' => 'nullable|integer',
    //             'shipping_pincode' => 'nullable|string|max:20',
    //         ]);

    //         $userId = (int) $request->user_id;

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Get Cart Items
    //         |--------------------------------------------------------------------------
    //         */
    //         $cartItems = Cart::with([
    //                 'productVariant',
    //                 'productVariant.stocks',
    //                 'cartAttributes',
    //                 'cartAttributes.attribute',
    //                 'cartAttributes.attributeValue',
    //             ])
    //             ->where('user_id', $userId)
    //             ->get();

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Empty Cart Check
    //         |--------------------------------------------------------------------------
    //         */
    //         if ($cartItems->isEmpty()) {

    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Cart is empty',
    //             ], 422);
    //         }

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Calculate Totals
    //         |--------------------------------------------------------------------------
    //         */
    //         $itemsTotal = 0;

    //         $totalItems = 0;

    //         foreach ($cartItems as $cart) {

    //             $price = $cart->productVariant->final_price ?? 0;

    //             $itemsTotal += (
    //                 $price * $cart->quantity
    //             );

    //             $totalItems += $cart->quantity;
    //         }

    //         $discountAmount = 0;
    //         $platformCharge = 5;
    //         $deliveryCharge = $itemsTotal >= 100 ? 0 : 30;
    //         $taxAmount = 0;

    //         $grandTotal = (
    //             $itemsTotal
    //             + $platformCharge
    //             + $deliveryCharge
    //             + $taxAmount
    //         ) - $discountAmount;

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Generate Order Number
    //         |--------------------------------------------------------------------------
    //         */
    //         $lastOrder = Order::latest()->first();

    //         $nextId = $lastOrder ? ($lastOrder->id + 1) : 1;
    //         $orderNo = 'ORD-'. now()->format('Ymd'). '-'. str_pad($nextId,4,'0',STR_PAD_LEFT);
    //         $invoiceNo =  'INV-'. now()->format('Ymd'). '-'. str_pad($nextId,4,'0',STR_PAD_LEFT);
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Create Order
    //         |--------------------------------------------------------------------------
    //         */
    //         $firstCartItem = $cartItems->first();

    //         $modelMap = config('product.model_map');

    //         $modelClass = $modelMap[
    //             $firstCartItem->business_category_id
    //         ] ?? null;

    //         if (!$modelClass) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Invalid business category'
    //             ], 422);
    //         }

    //         $product = $modelClass::find(
    //             $firstCartItem->product_id
    //         );

    //         if (!$product) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Product not found'
    //             ], 404);
    //         }


    //         $order = Order::create([
    //             'order_no' => $orderNo,
    //             'invoice_no' => $invoiceNo,
    //             'user_id' => $userId,
    //             'business_id' => $product->business_id,
    //             'business_category_id' => $cartItems->first()->business_category_id,
    //             'total_items' => $totalItems,
    //             'items_total' => $itemsTotal,
    //             'discount_amount' => $discountAmount,
    //             'platform_charge' => $platformCharge,
    //             'delivery_charge' => $deliveryCharge,
    //             'tax_amount' => $taxAmount,
    //             'grand_total' => $grandTotal,
    //             'payment_status' => Order::PAYMENT_PENDING,
    //             'payment_method' => Order::METHOD_COD,
    //             'order_status' => Order::STATUS_PENDING,
    //             'placed_at' => now(),
    //         ]);

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Create Order Items
    //         |--------------------------------------------------------------------------
    //         */
    //         foreach ($cartItems as $cart) {

    //             /*
    //             |--------------------------------------------------------------------------
    //             | Product Variant Check
    //             |--------------------------------------------------------------------------
    //             */
    //             if (!$cart->productVariant) {

    //                 throw new \Exception(
    //                     $cart->product_name
    //                     . ' variant not found'
    //                 );
    //             }

    //             /*
    //             |--------------------------------------------------------------------------
    //             | Stock Check
    //             |--------------------------------------------------------------------------
    //             */
    //             $availableStock = $cart->productVariant
    //                 ->stocks
    //                 ->sum('stock');

    //             if ($cart->quantity > $availableStock) {

    //                 throw new \Exception(
    //                     $cart->product_name
    //                     . ' stock unavailable'
    //                 );
    //             }

    //             /*
    //             |--------------------------------------------------------------------------
    //             | Variant Price
    //             |--------------------------------------------------------------------------
    //             */
    //             $price = $cart->productVariant
    //                 ->final_price ?? 0;

    //             /*
    //             |--------------------------------------------------------------------------
    //             | Create Order Item
    //             |--------------------------------------------------------------------------
    //             */
    //             $productImage = ProductImage::where(
    //                 'product_id',
    //                 $cart->product_id
    //             )
    //             ->where(
    //                 'business_category_id',
    //                 $cart->business_category_id
    //             )
    //             ->first();


    //             $orderItem = $order->items()->create([

    //                 'product_id' => $cart->product_id,
    //                 'product_variant_id' => $cart->product_variant_id,
    //                 'product_name' => $cart->product_name,
    //                 'sku' => $cart->productVariant->sku ?? null,
    //                 'quantity' => $cart->quantity,
    //                 'mrp' => $cart->productVariant->mrp ?? 0,
    //                 'selling_price' => $cart->productVariant->selling_price ?? 0,
    //                 'discount_amount' => $cart->productVariant->discount ?? 0,
    //                 'final_price' => $price,
    //                 'subtotal' => (
    //                     $price * $cart->quantity
    //                 ),
    //                 'loyalty_points' => 0,
    //                 'product_snapshot' => [
    //                     'product_name' => $cart->product_name,
    //                     'sku' => $cart->productVariant->sku ?? null,
    //                     'mrp' => $cart->productVariant->mrp ?? 0,
    //                     'selling_price' => $cart->productVariant->selling_price ?? 0,
    //                     'final_price' => $price,
    //                     'image' => $productImage?->image_medium,
    //                 ],
    //             ]);

    //             /*
    //             |--------------------------------------------------------------------------
    //             | Create Order Item Attributes
    //             |--------------------------------------------------------------------------
    //             */
    //             foreach ($cart->cartAttributes as $attribute) {

    //                 $orderItem->attributes()->create([
    //                     'attribute_id' => $attribute->attribute_id,
    //                     'attribute_value_id' => $attribute->attribute_value_id,
    //                     'attribute_name' => $attribute->attribute_name,
    //                     'attribute_value' => $attribute->attribute_value,
    //                 ]);
    //             }

    //             /*
    //             |--------------------------------------------------------------------------
    //             | Deduct Stock
    //             |--------------------------------------------------------------------------
    //             */
    //             $remainingQty = $cart->quantity;

    //             foreach (
    //                 $cart->productVariant->stocks
    //                 as $stock
    //             ) {

    //                 if ($remainingQty <= 0) {
    //                     break;
    //                 }

    //                 if ($stock->stock <= 0) {
    //                     continue;
    //                 }

    //                 $deductQty = min(
    //                     $stock->stock,
    //                     $remainingQty
    //                 );

    //                 $stock->decrement(
    //                     'stock',
    //                     $deductQty
    //                 );

    //                 $remainingQty -= $deductQty;
    //             }
    //         }

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Create Initial Status History
    //         |--------------------------------------------------------------------------
    //         */
    //         $order->statusHistories()->create([
    //             'status' => Order::STATUS_PENDING,
    //             'remarks' => 'Order placed successfully',
    //             //'changed_by' => $userId,
    //         ]);

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Clear Cart
    //         |--------------------------------------------------------------------------
    //         */
    //         Cart::where(
    //             'user_id',
    //             $userId
    //         )->delete();

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Commit Transaction
    //         |--------------------------------------------------------------------------
    //         */
    //         DB::commit();

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Load Relationships
    //         |--------------------------------------------------------------------------
    //         */
    //         $order->load([
    //             'items',
    //             'items.attributes',
    //             'addresses',
    //             'payments',
    //             'statusHistories',
    //         ]);

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Success Response
    //         |--------------------------------------------------------------------------
    //         */
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Order placed successfully',
    //             //'data' => new OrderResource($order),
    //         ]);

    //     } catch (\Illuminate\Validation\ValidationException $e) {

    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->validator
    //                 ->errors()
    //                 ->first(),

    //             'errors' => $e->errors(),
    //         ], 422);

    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage(),
    //             'line' => $e->getLine(),
    //         ], 500);
    //     }
    // }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $request->validate([

                'user_id' => 'required|integer',

                // Billing Address
                'billing_address' => 'required|string',
                'billing_city_id' => 'nullable|integer',
                'billing_state_id' => 'nullable|integer',
                'billing_pincode' => 'nullable|string|max:20',

                // Shipping Address
                'shipping_address_id' => 'nullable|integer',
                'shipping_address' => 'required|string',
                'shipping_city_id' => 'nullable|integer',
                'shipping_state_id' => 'nullable|integer',
                'shipping_pincode' => 'nullable|string|max:20',

                // Charges
                'platformCharge' => 'nullable|numeric|min:0',
                'deliveryCharge' => 'nullable|numeric|min:0',
                'taxAmount' => 'nullable|numeric|min:0',
                'discountAmount' => 'nullable|numeric|min:0',
                'itemsTotal' => 'nullable|numeric|min:0',
                'grandTotal' => 'nullable|numeric|min:0',

                // Payment
                'payment_method' => 'nullable|in:WALLET,ONLINE,COD',

                // Loyalty
                'loyalty_points' => 'nullable|numeric|min:0',

                // Notes
                'notes' => 'nullable|string',
            ]);

            $userId = (int) $request->user_id;

            $cartItems = Cart::with([
                    'productVariant',
                    'productVariant.stocks',
                    'cartAttributes',
                    'cartAttributes.attribute',
                    'cartAttributes.attributeValue',
                ])
                ->where('user_id', $userId)
                ->get();

            if ($cartItems->isEmpty()) {

                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty',
                ], 422);
            }

            $calculatedItemsTotal = 0;

            $totalItems = 0;

            foreach ($cartItems as $cart) {

                $price = $cart->productVariant->final_price ?? 0;

                $calculatedItemsTotal += (
                    $price * $cart->quantity
                );

                $totalItems += $cart->quantity;
            }

            $itemsTotal = $request->itemsTotal
                ?? $calculatedItemsTotal;

            $discountAmount = $request->discountAmount ?? 0;

            $platformCharge = $request->platformCharge ?? 0;

            $deliveryCharge = $request->deliveryCharge ?? 0;

            $taxAmount = $request->taxAmount ?? 0;

            $grandTotal = $request->grandTotal ?? (
                $itemsTotal
                + $platformCharge
                + $deliveryCharge
                + $taxAmount
                - $discountAmount
            );

            $loyaltyPoints = $request->loyalty_points ?? 0;

            $paymentMethodMap = [
                'WALLET' => Order::METHOD_WALLET,
                'ONLINE' => Order::METHOD_ONLINE,
                'COD' => Order::METHOD_COD,
            ];

            $paymentMethod = $paymentMethodMap[
                strtoupper($request->payment_method ?? 'COD')
            ] ?? Order::METHOD_COD;


            $lastOrder = Order::latest()->first();
            $nextId = $lastOrder
                ? ($lastOrder->id + 1)
                : 1;
            $orderNo = 'ORD-'
                . now()->format('Ymd')
                . '-'
                . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $invoiceNo = 'INV-'
                . now()->format('Ymd')
                . '-'
                . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $firstCartItem = $cartItems->first();

            $modelMap = config('product.model_map');

            $modelClass = $modelMap[
                $firstCartItem->business_category_id
            ] ?? null;

            if (!$modelClass) {

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid business category'
                ], 422);
            }

            $product = $modelClass::find(
                $firstCartItem->product_id
            );

            if (!$product) {

                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $order = Order::create([
                'order_no' => $orderNo,
                'invoice_no' => $invoiceNo,
                'business_id' => $product->business_id,
                'business_category_id' => $cartItems
                    ->first()
                    ->business_category_id,
                'user_id' => $userId,
                'total_items' => $totalItems,
                'items_total' => $itemsTotal,
                'discount_amount' => $discountAmount,
                'platform_charge' => $platformCharge,
                'delivery_charge' => $deliveryCharge,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'loyalty_used' => $loyaltyPoints,
                'loyalty_earned' => 0,
                'wallet_used' => $paymentMethod == Order::METHOD_WALLET
                    ? $grandTotal
                    : 0,
                'online_paid' => $paymentMethod == Order::METHOD_ONLINE
                    ? $grandTotal
                    : 0,
                'payment_status' => Order::PAYMENT_PENDING,
                'payment_method' => $paymentMethod,
                'order_status' => Order::STATUS_PENDING,
                'notes' => $request->notes,
                'placed_at' => now(),
            ]);

            $order->addresses()->create([

                // Billing
                'billing_address' => $request->billing_address,
                'billing_city_id' => $request->billing_city_id,
                'billing_state_id' => $request->billing_state_id,
                'billing_pincode' => $request->billing_pincode,

                // Shipping
                'shipping_address_id' => $request->shipping_address_id,
                'shipping_address' => $request->shipping_address,
                'shipping_city_id' => $request->shipping_city_id,
                'shipping_state_id' => $request->shipping_state_id,
                'shipping_pincode' => $request->shipping_pincode,
            ]);

            foreach ($cartItems as $cart) {

                if (!$cart->productVariant) {

                    throw new \Exception(
                        $cart->product_name
                        . ' variant not found'
                    );
                }

                $availableStock = $cart->productVariant
                    ->stocks
                    ->sum('stock');

                if ($cart->quantity > $availableStock) {

                    throw new \Exception(
                        $cart->product_name
                        . ' stock unavailable'
                    );
                }

                $price = $cart->productVariant
                    ->final_price ?? 0;

                $productImage = ProductImage::where(
                    'product_id',
                    $cart->product_id
                )
                ->where(
                    'business_category_id',
                    $cart->business_category_id
                )
                ->first();


                $orderItem = $order->items()->create([

                    'product_id' => $cart->product_id,
                    'product_variant_id' => $cart->product_variant_id,
                    'product_name' => $cart->product_name,
                    'sku' => $cart->productVariant->sku ?? null,
                    'quantity' => $cart->quantity,
                    'modified_quantity' => $cart->quantity,
                    'mrp' => $cart->productVariant->mrp ?? 0,
                    'selling_price' => $cart->productVariant->selling_price ?? 0,
                    'discount_amount' => $cart->productVariant->discount ?? 0,
                    'final_price' => $price,
                    'subtotal' => (
                        $price * $cart->quantity
                    ),
                    'loyalty_points' => $loyaltyPoints,
                    'product_snapshot' => [
                        'product_name' => $cart->product_name,
                        'sku' => $cart->productVariant->sku ?? null,
                        'mrp' => $cart->productVariant->mrp ?? 0,
                        'selling_price' => $cart->productVariant->selling_price ?? 0,
                        'final_price' => $price,
                        'image' => $productImage?->image_medium,
                    ],
                ]);

                foreach ($cart->cartAttributes as $attribute) {
                    $orderItem->attributes()->create([
                        'attribute_id' => $attribute->attribute_id,
                        'attribute_value_id' => $attribute->attribute_value_id,
                        'attribute_name' => $attribute->attribute_name,
                        'attribute_value' => $attribute->attribute_value,
                    ]);
                }

                $remainingQty = $cart->quantity;
                foreach (
                    $cart->productVariant->stocks
                    as $stock
                ) {
                    if ($remainingQty <= 0) {
                        break;
                    }
                    if ($stock->stock <= 0) {
                        continue;
                    }
                    $deductQty = min(
                        $stock->stock,
                        $remainingQty
                    );
                    $stock->decrement(
                        'stock',
                        $deductQty
                    );
                    $remainingQty -= $deductQty;
                }
            }

            $order->statusHistories()->create([
                'status' => Order::STATUS_PENDING,
                'remarks' => 'Order placed successfully',
            ]);

            Cart::where(
                'user_id',
                $userId
            )->delete();


            DB::commit();

            $order->load([
                'items',
                'items.attributes',
                'addresses',
                'payments',
                'statusHistories',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Success Response
            |--------------------------------------------------------------------------
            */
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => $order,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->validator
                    ->errors()
                    ->first(),
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),

            ], 500);
        }
    }

    /**
     * Order Details
     */
    public function show($id)
    {
        $orderId = decodeIdOrFail(
            $id,
            'Invalid order ID'
        );

        $order = Order::with([

            // Business
            'business',
            'businessCategory',

            // Items
            'items',
            'items.attributes',

            // Address
            'addresses',
            'addresses.billingCity',
            'addresses.billingState',
            'addresses.shippingCity',
            'addresses.shippingState',

            // Status History
            'statusHistories',

        ])->findOrFail($orderId);

        return response()->json([
            'success' => true,
            'data'    => new OrderResource($order)
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
                'items',
                'addresses',
                'business',
            ])->findOrFail($orderId);

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
            return $pdf->stream(
                $order->invoice_no . '.pdf'
            );

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
