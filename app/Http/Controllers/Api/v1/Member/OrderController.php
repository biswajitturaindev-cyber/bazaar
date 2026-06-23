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
use Illuminate\Support\Facades\Storage;

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

                'is_gst_bill' => 'nullable|boolean',

                'gst_name'    => 'nullable|string|max:255',
                'gst_number'  => 'nullable|string|max:15',
                'gst_address' => 'nullable|string|max:500',
            ]);

            $userId = (int) $request->user_id;

            $cartItems = Cart::with([
                'productVariant',
                'productVariant.stocks',
                'cartAttributes',
                'cartAttributes.attributeMaster',
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

            $itemsTotal = $request->itemsTotal ?? $calculatedItemsTotal;

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
            $orderNo = 'OD/' . now()->format('ymdHis');


            $nextId = (Order::max('id') ?? 0) + 1;

            $currentYear = now()->year;

            if (now()->month >= 4) {
                $fyStart = substr($currentYear, -2);
                $fyEnd = substr($currentYear + 1, -2);
            } else {
                $fyStart = substr($currentYear - 1, -2);
                $fyEnd = substr($currentYear, -2);
            }

            $invoiceNo = sprintf(
                'INV/%s-%s/%s/%04d',
                $fyStart,
                $fyEnd,
                now()->format('m'),
                $nextId
            );

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
                'business_category_id' => $cartItems->first()->business_category_id,
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

                // GST Details
                'is_gst_bill' => $request->boolean('is_gst_bill'),
                'gst_name' => $request->gst_name,
                'gst_number' => strtoupper($request->gst_number ?? ''),
                'gst_address' => $request->gst_address,

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


                $product = $cart->product();

                $hsnCode = null;
                $commission = null;

                if ($product) {
                    $hsnCode = $product->hsn_id ?? null;
                    $commission = $product->commission ?? null;
                }

                $orderItem = $order->items()->create([

                    'product_id' => $cart->product_id,
                    'product_commission' => $commission,
                    'product_variant_id' => $cart->product_variant_id,
                    'product_name' => $cart->product_name,
                    'sku' => $cart->productVariant->sku ?? null,
                    'hsn_code' => $hsnCode,
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
                        'attribute_master_id' => $attribute->attribute_master_id,
                        'attribute_value_id' => $attribute->attribute_value_id,
                        'attribute_name' => $attribute->attribute_master_name,
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
            'business.kycDetail',
            'businessCategory',

            // Items
            'items',
            'items.attributes',
            'items.variant.images',

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

            $orderId = decodeIdOrFail($encoded_id);

            $order = Order::with([
                'items',
                'addresses',
                'business',
            ])->findOrFail($orderId);

            $address = $order->addresses->first();
            $business = $order->business;

            $pdf = Pdf::loadView(
                'pdf.order-invoice-pos',
                compact(
                    'order',
                    'address',
                    'business'
                )
            );

            $pdf->setPaper('A5', 'portrait');

            $fileName = $order->invoice_no . '.pdf';
            $path = 'invoices/' . $fileName;

            // Create directory if not exists
            if (!Storage::disk('public')->exists('invoices')) {
                Storage::disk('public')->makeDirectory('invoices');
            }

            Storage::disk('public')->put(
                $path,
                $pdf->output()
            );

            return response()->json([
                'success' => true,
                'message' => 'Invoice generated successfully',
                'invoice_no' => $order->invoice_no,
                'invoice_url' => asset('storage/' . $path),
            ]);

        } catch (\Throwable $e) {

            \Log::error('Invoice Generation Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to generate invoice',
            ], 500);
        }
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
                'cancelled_by'     => 'customer',
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
}
