<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1a1a2e;
            background: #fff;
            padding: 36px;
        }

        /* ── TOP BAR ── */
        .top-bar {
            background: #1a1a2e;
            margin: -36px -36px 0 -36px;
            padding: 10px 36px;
            display: table;
            width: calc(100% + 72px);
        }
        .top-bar-inner {
            display: table-row;
        }
        .top-bar .brand {
            display: table-cell;
            color: #fff;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            vertical-align: middle;
        }
        .top-bar .inv-badge {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }
        .top-bar .inv-badge span {
            display: inline-block;
            background: #f97316;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 3px;
            text-transform: uppercase;
            padding: 4px 14px;
        }

        /* ── HERO HEADER ── */
        .hero {
            margin: 0 -36px;
            padding: 28px 36px 24px;
            background: #f8f8fb;
            border-bottom: 3px solid #f97316;
            display: table;
            width: calc(100% + 72px);
        }
        .hero-row {
            display: table-row;
        }
        .hero-left {
            display: table-cell;
            vertical-align: middle;
            width: 55%;
        }
        .hero-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 45%;
        }

        .logo-wrap {
            display: table;
        }
        .logo-wrap img {
            display: table-cell;
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e2e2f0;
            vertical-align: middle;
            margin-right: 14px;
        }
        .logo-text {
            display: table-cell;
            vertical-align: middle;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #1a1a2e;
            letter-spacing: 0.5px;
        }
        .company-meta {
            font-size: 10.5px;
            color: #666;
            margin-top: 3px;
            line-height: 1.6;
        }
        .gst-pill {
            display: inline-block;
            background: #1a1a2e;
            color: #f97316;
            font-size: 9.5px;
            font-weight: bold;
            letter-spacing: 1px;
            padding: 2px 8px;
            border-radius: 3px;
            margin-top: 5px;
        }

        /* invoice meta right side */
        .inv-meta table {
            border-collapse: collapse;
            margin-left: auto;
        }
        .inv-meta td {
            padding: 3px 0 3px 18px;
            font-size: 11px;
            color: #444;
            border: none;
        }
        .inv-meta td:first-child {
            color: #999;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            text-align: right;
        }
        .inv-meta td:last-child {
            font-weight: bold;
            color: #1a1a2e;
            text-align: right;
        }
        .inv-date-big {
            font-size: 22px;
            font-weight: bold;
            color: #f97316;
            margin-bottom: 6px;
        }

        /* ── ADDRESS STRIP ── */
        .addr-strip {
            display: table;
            width: 100%;
            margin-top: 22px;
            border-collapse: separate;
            border-spacing: 12px 0;
        }
        .addr-strip-row { display: table-row; }
        .addr-box {
            display: table-cell;
            width: 50%;
            border: 1px solid #e2e2f0;
            border-top: 3px solid #1a1a2e;
            padding: 14px 16px;
            vertical-align: top;
            background: #fafafa;
        }
        .addr-box:first-child { margin-right: 6px; }
        .addr-label {
            font-size: 9px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #f97316;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .addr-text {
            font-size: 11.5px;
            color: #333;
            line-height: 1.65;
        }
        .addr-pin {
            display: inline-block;
            margin-top: 5px;
            background: #1a1a2e;
            color: #fff;
            font-size: 9px;
            padding: 2px 8px;
            border-radius: 2px;
            letter-spacing: 0.5px;
        }

        /* ── PRODUCTS TABLE ── */
        .section-label {
            font-size: 9px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #999;
            margin: 24px 0 8px;
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
        }
        .product-table thead tr {
            background: #1a1a2e;
            color: #fff;
        }
        .product-table thead th {
            padding: 10px 12px;
            text-align: left;
            font-size: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: bold;
            border: none;
        }
        .product-table thead th.right { text-align: right; }

        .product-table tbody tr {
            border-bottom: 1px solid #eef0f6;
        }
        .product-table tbody tr:nth-child(even) {
            background: #f8f8fb;
        }
        .product-table tbody td {
            padding: 10px 12px;
            border: none;
            vertical-align: middle;
            font-size: 11.5px;
            color: #333;
        }
        .product-table tbody td.right { text-align: right; }
        .product-table tbody td.mono {
            font-size: 10.5px;
            color: #888;
            font-family: monospace;
        }

        .row-num {
            width: 24px;
            height: 24px;
            background: #f0f0f8;
            border-radius: 50%;
            display: inline-block;
            text-align: center;
            line-height: 24px;
            font-size: 10px;
            color: #555;
            font-weight: bold;
        }

        .product-thumb {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e2e2f0;
        }
        .no-img {
            width: 48px;
            height: 48px;
            border-radius: 6px;
            background: #eef0f6;
            display: inline-block;
        }

        .product-name {
            font-weight: bold;
            color: #1a1a2e;
            font-size: 12px;
        }

        /* ── TOTALS ── */
        .totals-wrap {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .totals-spacer { display: table-cell; width: 55%; }
        .totals-cell { display: table-cell; width: 45%; vertical-align: top; }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 14px;
            font-size: 11.5px;
            border: none;
            border-bottom: 1px solid #f0f0f8;
        }
        .totals-table td:first-child { color: #777; }
        .totals-table td:last-child { text-align: right; font-weight: bold; color: #1a1a2e; }
        .totals-table .discount td:last-child { color: #16a34a; }

        .grand-row {
            background: #1a1a2e;
        }
        .grand-row td {
            padding: 12px 14px !important;
            font-size: 13px !important;
            font-weight: bold !important;
            color: #fff !important;
            border-bottom: none !important;
        }
        .grand-row td:last-child {
            color: #f97316 !important;
            font-size: 15px !important;
        }

        /* ── FOOTER ── */
        .footer {
            margin: 36px -36px -36px;
            background: #f8f8fb;
            border-top: 1px solid #e2e2f0;
            padding: 16px 36px;
            display: table;
            width: calc(100% + 72px);
        }
        .footer-inner { display: table-row; }
        .footer-left {
            display: table-cell;
            vertical-align: middle;
            font-size: 11px;
            color: #888;
        }
        .footer-right {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
            font-size: 10px;
            color: #bbb;
            letter-spacing: 0.5px;
        }
        .thank-you {
            font-size: 13px;
            font-weight: bold;
            color: #1a1a2e;
        }
        .orange { color: #f97316; }
    </style>
</head>
<body>

{{-- TOP BAR --}}
<div class="top-bar">
    <div class="top-bar-inner">
        <div class="brand">{{ $business->business_name ?? 'Your Business' }}</div>
        <div class="inv-badge"><span>TAX INVOICE</span></div>
    </div>
</div>

{{-- HERO HEADER --}}
<div class="hero">
    <div class="hero-row">

        {{-- Company Info --}}
        <div class="hero-left">
            @php
                $shopPhoto = null;
                if (!empty($kycDetail?->shop_photo)) {
                    $path = storage_path('app/public/' . $kycDetail->shop_photo);
                    if (file_exists($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $shopPhoto = 'data:image/' . strtolower($type) . ';base64,' . base64_encode($data);
                    }
                }
            @endphp

            <div class="logo-wrap">
                @if($shopPhoto)
                    <img src="{{ $shopPhoto }}" alt="Logo">
                @endif
                <div class="logo-text">
                    <div class="company-name">{{ $business->business_name ?? 'Company Name' }}</div>
                    <div class="company-meta">
                        {{ implode(', ', array_filter([
                            $businessAddress->address_line_1 ?? null,
                            $businessAddress->city ?? null,
                            $businessAddress->state ?? null,
                            $businessAddress->pincode ?? null,
                        ])) }}<br>
                        Ph: {{ $businessContact->contact_number ?? '' }}
                    </div>
                    @if(!empty($business->gst_number))
                        <div class="gst-pill">GST: {{ $business->gst_number }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Invoice Meta --}}
        <div class="hero-right">
            <div class="inv-date-big">{{ date('d M Y', strtotime($order->placed_at)) }}</div>
            <div class="inv-meta">
                <table>
                    <tr>
                        <td>Invoice No</td>
                        <td>{{ $order->invoice_no }}</td>
                    </tr>
                    <tr>
                        <td>Order No</td>
                        <td>{{ $order->order_no }}</td>
                    </tr>
                    <tr>
                        <td>Payment</td>
                        <td>{{ $order->payment_method_text }}</td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- ADDRESS STRIP --}}
<div class="addr-strip">
    <div class="addr-strip-row">
        <div class="addr-box">
            <div class="addr-label">Billing Address</div>
            <div class="addr-text">{{ $address->billing_address }}</div>
            <div class="addr-pin">PIN {{ $address->billing_pincode }}</div>
        </div>
        <div class="addr-box">
            <div class="addr-label">Shipping Address</div>
            <div class="addr-text">{{ $address->shipping_address }}</div>
            <div class="addr-pin">PIN {{ $address->shipping_pincode }}</div>
        </div>
    </div>
</div>

{{-- PRODUCTS --}}
<div class="section-label">Order Items</div>
<table class="product-table">
    <thead>
        <tr>
            <th style="width:36px;">#</th>
            <th style="width:60px;">Image</th>
            <th>Product</th>
            <th>SKU</th>
            <th style="width:40px;">Qty</th>
            <th class="right" style="width:80px;">Price</th>
            <th class="right" style="width:90px;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $key => $item)
            @php
                $snapshot = is_array($item->product_snapshot)
                    ? $item->product_snapshot
                    : json_decode($item->product_snapshot, true);

                $productImage = null;
                if (!empty($snapshot['image'])) {
                    $path = public_path('storage/' . $snapshot['image']);
                    if (file_exists($path)) {
                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                        $productImage = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($path));
                    }
                }
            @endphp
            <tr>
                <td><span class="row-num">{{ $key + 1 }}</span></td>
                <td>
                    @if($productImage)
                        <img src="{{ $productImage }}" class="product-thumb" alt="">
                    @else
                        <span class="no-img"></span>
                    @endif
                </td>
                <td><span class="product-name">{{ $item->product_name }}</span></td>
                <td class="mono">{{ $item->sku }}</td>
                <td>{{ $item->quantity }}</td>
                <td class="right">₹{{ number_format($item->final_price, 2) }}</td>
                <td class="right">₹{{ number_format($item->subtotal, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- TOTALS --}}
<div class="totals-wrap">
    <div class="totals-spacer"></div>
    <div class="totals-cell">
        <table class="totals-table">
            <tr>
                <td>Items Total</td>
                <td>₹{{ number_format($order->items_total, 2) }}</td>
            </tr>
            <tr class="discount">
                <td>Discount</td>
                <td>− ₹{{ number_format($order->discount_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Platform Charge</td>
                <td>₹{{ number_format($order->platform_charge, 2) }}</td>
            </tr>
            <tr>
                <td>Delivery Charge</td>
                <td>₹{{ number_format($order->delivery_charge, 2) }}</td>
            </tr>
            <tr>
                <td>Tax (GST)</td>
                <td>₹{{ number_format($order->tax_amount, 2) }}</td>
            </tr>
            <tr class="grand-row">
                <td>Grand Total</td>
                <td>₹{{ number_format($order->grand_total, 2) }}</td>
            </tr>
        </table>
    </div>
</div>

{{-- FOOTER --}}
<div class="footer">
    <div class="footer-inner">
        <div class="footer-left">
            <div class="thank-you">Thank you for your order <span class="orange">♥</span></div>
            <div style="margin-top:3px;">For support, contact us at {{ $businessContact->contact_number ?? '' }}</div>
        </div>
        <div class="footer-right">
            This is a computer-generated invoice<br>and does not require a signature.
        </div>
    </div>
</div>

</body>
</html>
