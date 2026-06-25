<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <title>Invoice</title>

    <style>

        body{
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color:#333;
            margin:0;
            padding:20px;
        }

        .container{
            width:100%;
        }

        .header-table{
            width:100%;
            margin-bottom:25px;
        }

        .header-table td{
            vertical-align:top;
            border:none;
        }

        .company-logo{
            width:90px;
            height:90px;
            object-fit:contain;
            margin-bottom:10px;
        }

        .invoice-title{
            font-size:28px;
            font-weight:bold;
            margin-bottom:10px;
        }

        .company-name{
            font-size:22px;
            font-weight:bold;
            margin-bottom:8px;
        }

        .address-table{
            width:100%;
            margin-top:20px;
            margin-bottom:20px;
        }

        .address-table td{
            border:1px solid #ddd;
            padding:15px;
            vertical-align:top;
        }

        .product-table{
            width:100%;
            border-collapse:collapse;
            margin-top:10px;
        }

        .product-table th{
            background:#f5f5f5;
            border:1px solid #ddd;
            padding:12px;
            text-align:left;
        }

        .product-table td{
            border:1px solid #ddd;
            padding:10px;
            vertical-align:middle;
        }

        .product-image{
            width:60px;
            height:60px;
            object-fit:cover;
        }

        .totals-table{
            width:40%;
            margin-top:20px;
            margin-left:auto;
            border-collapse:collapse;
        }

        .totals-table td,
        .totals-table th{
            border:1px solid #ddd;
            padding:10px;
        }

        .text-right{
            text-align:right;
        }

        .footer{
            margin-top:50px;
            text-align:center;
            font-size:11px;
            color:#777;
        }

        h1,h2,h3,h4,p{
            margin:0;
            padding:0;
        }

    </style>

</head>

<body>

<div class="container">

    <!-- HEADER -->
    <table class="header-table">

        <tr>

            <!-- COMPANY -->
            <td width="60%">

                @if(!empty($business->profile_image))

                    <img
                        src="{{ public_path('storage/'.$business->profile_image) }}"
                        class="company-logo"
                    >

                @endif

                <div class="company-name">
                    {{ $business->business_name ?? 'Company Name' }}
                </div>

                <p>
                    {{ $business->address_line_1 ?? '' }}
                </p>

                <p>
                    {{ $business->address_line_2 ?? '' }}
                </p>

                <p>
                    {{ $business->city ?? '' }}
                    {{ $business->state ?? '' }}
                    {{ $business->pincode ?? '' }}
                </p>

                <p>
                    Phone:
                    {{ $business->phone ?? '' }}
                </p>

                <p>
                    Email:
                    {{ $business->email ?? '' }}
                </p>

            </td>

            <!-- INVOICE -->
            <td width="40%" align="right">

                <div class="invoice-title">
                    INVOICE
                </div>

                <p>
                    <strong>Invoice No:</strong>
                    {{ $order->invoice_no }}
                </p>

                <p>
                    <strong>Order No:</strong>
                    {{ $order->order_no }}
                </p>

                <p>
                    <strong>Date:</strong>
                    {{ date('d M Y', strtotime($order->placed_at)) }}
                </p>

                <p>
                    <strong>Payment:</strong>
                    {{ $order->payment_method_text }}
                </p>

            </td>

        </tr>

    </table>

    <!-- ADDRESS -->
    <table class="address-table">

        <tr>

            <td width="50%">

                <h4>Billing Address</h4>

                <br>

                <p>
                    {{ $address->billing_address }}
                </p>

                <p>
                    Pincode:
                    {{ $address->billing_pincode }}
                </p>

            </td>

            <td width="50%">

                <h4>Shipping Address</h4>

                <br>

                <p>
                    {{ $address->shipping_address }}
                </p>

                <p>
                    Pincode:
                    {{ $address->shipping_pincode }}
                </p>

            </td>

        </tr>

    </table>

    <!-- PRODUCTS -->
    <table class="product-table">

        <thead>

        <tr>

            <th>#</th>

            <th>Image</th>

            <th>Product</th>

            <th>SKU</th>

            <th>Qty</th>

            <th>Price</th>

            <th>Subtotal</th>

        </tr>

        </thead>

        <tbody>

        @foreach($order->items as $key => $item)

            @php

                $snapshot = is_array($item->product_snapshot)
                    ? $item->product_snapshot
                    : json_decode($item->product_snapshot, true);

            @endphp

            <tr>

                <td>
                    {{ $key + 1 }}
                </td>

                <td>

                    @php

                    $productImage = null;

                    if(!empty($snapshot['image'])){

                        $path = public_path('storage/'.$snapshot['image']);

                        if(file_exists($path)){

                            $ext = pathinfo($path, PATHINFO_EXTENSION);

                            $productImage = 'data:image/'.$ext.';base64,'.base64_encode(file_get_contents($path));
                        }

                    }

                    @endphp

                    @if($productImage)
                        <img src="{{ $productImage }}" class="product-image">
                    @endif

                </td>

                <td>
                    {{ $item->product_name }}
                </td>

                <td>
                    {{ $item->sku }}
                </td>

                <td>
                    {{ $item->quantity }}
                </td>

                <td>
                    ₹{{ number_format($item->final_price,2) }}
                </td>

                <td>
                    ₹{{ number_format($item->subtotal,2) }}
                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

    @php
        $itemsTotal = $order->items->sum('subtotal');
        $taxTotal = $order->items->sum('tax_amount'); // or gst_amount if that's your column

        $grandTotal = $itemsTotal
            - $order->discount_amount
            + $order->platform_charge
            + $order->delivery_charge
            + $taxTotal;
    @endphp

    <!-- TOTALS -->
    <table class="totals-table">

        <tr>

            <td>
                Items Total
            </td>

            <td class="text-right">
                ₹{{ number_format($order->items_total,2) }}
            </td>

        </tr>

        <tr>

            <td>
                Discount
            </td>

            <td class="text-right">
                ₹{{ number_format($order->discount_amount,2) }}
            </td>

        </tr>

        <tr>

            <td>
                Platform Charge
            </td>

            <td class="text-right">
                ₹{{ number_format($order->platform_charge,2) }}
            </td>

        </tr>

        <tr>

            <td>
                Delivery Charge
            </td>

            <td class="text-right">
                ₹{{ number_format($order->delivery_charge,2) }}
            </td>

        </tr>

        <tr>

            <td>
                Tax
            </td>

            <td class="text-right">
                ₹{{ number_format($order->tax_amount,2) }}
            </td>

        </tr>

        <tr>

            <th>
                Grand Total
            </th>

            <th class="text-right">
                ₹{{ number_format($order->grand_total,2) }}
            </th>

        </tr>

    </table>

    <!-- FOOTER -->
    <div class="footer">

        <p>
            Thank you for shopping with us.
        </p>

    </div>

</div>

</body>
</html>
