<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Business;
use App\Http\Requests\Admin\OrderIndexRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(OrderIndexRequest $request)
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
            | Business Filter
            |--------------------------------------------------------------------------
            */

            if ($request->filled('business_id')) {

                $businessId = decodeIdOrFail($request->business_id);

                $query->where('business_id', $businessId);
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
            | Month / Year
            |--------------------------------------------------------------------------
            */

            if ($request->filled('month')) {
                $query->whereMonth('placed_at', $request->integer('month'));
            }

            if ($request->filled('year')) {
                $query->whereYear('placed_at', $request->integer('year'));
            }

            /*
            |--------------------------------------------------------------------------
            | Date Range
            |--------------------------------------------------------------------------
            */

            if ($request->filled('from_date') && $request->filled('to_date')) {

                $query->whereBetween('placed_at', [
                    Carbon::parse($request->from_date)->startOfDay(),
                    Carbon::parse($request->to_date)->endOfDay(),
                ]);

            } elseif ($request->filled('from_date')) {

                $query->whereDate('placed_at', '>=', $request->from_date);

            } elseif ($request->filled('to_date')) {

                $query->whereDate('placed_at', '<=', $request->to_date);

            }

            /*
            |--------------------------------------------------------------------------
            | Paginate
            |--------------------------------------------------------------------------
            */

            $orders = $query
                ->latest('placed_at')
                ->paginate($request->integer('per_page', 40))
                ->appends($request->query());

            $businesses = Business::orderBy('business_name')->get();

            return view('admin.orders.index', compact('orders', 'businesses'));

        } catch (\Throwable $e) {

            return back()->with('error', $e->getMessage());

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
    public function show(string $id): JsonResponse
    {
        try {

            $order = Order::with([
                'business',
                'business.address',
                'business.contact',
                'business.kycDetail',
                'items',
                'items.attributes',
                'addresses',
            ])->findOrFail($id);

            $business = $order->business;
            $businessAddress = $business?->address?->first();
            $businessContact = $business?->contact?->first();
            $kycDetail = $order->kycDetail;
            $address = $order->addresses->first();

            return response()->json([

                'success' => true,

                'order' => [
                    'id'                    => $order->id,
                    'order_no'              => $order->order_no,
                    'invoice_no'            => $order->invoice_no,
                    'status'                => $order->status,
                    'payment_method'        => $order->payment_method,
                    'payment_method_text'   => $order->payment_method_text,
                    'items_total'           => $order->items_total,
                    'discount_amount'       => $order->discount_amount,
                    'platform_charge'       => $order->platform_charge,
                    'delivery_charge'       => $order->delivery_charge,
                    'tax_amount'            => $order->tax_amount,
                    'grand_total'           => $order->grand_total,
                    'placed_at'             => $order->placed_at,
                    'placed_at_formatted'   => Carbon::parse($order->placed_at)->format('d M Y'),
                ],

                'business' => [
                    'business_name' => $business?->business_name,
                    'gst_number'    => $business?->gst_number,

                    'address' => [
                        'address_line_1' => $businessAddress?->address_line_1,
                        'address_line_2' => $businessAddress?->address_line_2,
                        'city'           => $businessAddress?->city,
                        'state'          => $businessAddress?->state,
                        'pincode'        => $businessAddress?->pincode,
                        'landmark'       => $businessAddress?->landmark,
                    ],

                    'contact' => [
                        'contact_person_name' => $businessContact?->contact_person_name,
                        'contact_number'      => $businessContact?->contact_number,
                    ],

                    'shop_photo' => $kycDetail?->shop_photo,
                ],

                'items' => $order->items->map(function ($item) {

                    return [
                        'product_name'       => $item->product_name,
                        'sku'                => $item->sku,
                        'quantity'           => $item->quantity,
                        'final_price'        => $item->final_price,
                        'subtotal'           => $item->subtotal,
                        'product_commission' => $item->product_commission,
                        'vendor_commission'  => $item->vendor_commission,
                        'attributes'         => $item->attributes,
                    ];

                }),

                'customer_address' => $address ? [
                    'billing_address'  => $address->billing_address,
                    'billing_pincode'  => $address->billing_pincode,
                    'shipping_address' => $address->shipping_address,
                    'shipping_pincode' => $address->shipping_pincode,
                ] : null,

                'invoice_url' => route('admin.orders.invoice', $order->id),

            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order.',
                'error'   => $e->getMessage(),
            ], 500);

        }
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

    // ─────────────────────────────────────────────
    // GET /admin/orders/{id}/invoice  →  PDF download
    // ─────────────────────────────────────────────
    public function invoice(string $id)
    {
        try {

            $order = Order::with([
                'business',
                'business.address',
                'business.contact',
                'business.kycDetail',
                'items',
                'addresses',
            ])->findOrFail($id);

            $business = $order->business;
            $businessAddress = $business?->address;
            $businessContact = $business?->contact;
            $kycDetail = $business?->kycDetail;

            $pdf = Pdf::loadView('admin.orders.invoice', compact(
                'order',
                'business',
                'businessAddress',
                'businessContact',
                'kycDetail',
                'address',
            ))->setPaper('a4', 'portrait');

            return $pdf->download("invoice-{$order->invoice_no}.pdf");

        } catch (\Throwable $e) {

            return back()->with('error', $e->getMessage());

        }
    }
}
