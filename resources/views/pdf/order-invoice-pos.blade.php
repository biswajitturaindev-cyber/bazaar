<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice Details</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }

        .container {
            width: 100%;
        }

        .header-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .row {
            width: 100%;
            clear: both;
        }

        .left {
            float: left;
        }

        .right {
            float: right;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .divider {
            border-top: 1px dashed #cfcfcf;
            margin: 15px 0;
        }

        .customer-section {
            text-align: center;
            margin: 20px 0;
        }

        .customer-section p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            text-align: left;
            padding: 8px 0;
            font-size: 13px;
        }

        .items-table td {
            padding: 12px 0;
            vertical-align: top;
        }

        .item-name {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .sub-code {
            color: #888;
            font-size: 11px;
        }

        .qty {
            text-align: center;
        }

        .amount {
            text-align: right;
        }

        .amount strong {
            display: block;
        }

        .summary-table td {
            padding: 6px 0;
            font-size: 14px;
        }

        .summary-table .grand {
            font-size: 18px;
            font-weight: bold;
        }

        .tax-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .tax-table td {
            padding: 6px 0;
            font-size: 14px;
        }

        .tax-total {
            border-top: 2px solid #bbb;
            padding-top: 10px !important;
            font-weight: bold;
            font-size: 16px;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
        }

        .footer-company {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .footer p {
            margin: 3px 0;
            color: #444;
        }

        .clearfix {
            clear: both;
        }
    </style>
</head>

<body>

<div class="container">

    <!-- TITLE -->
    <div class="header-title">
        Invoice Details
    </div>

    <!-- HEADER -->
    <div class="row">
        <div class="left">
            Date & Time :
            {{ date('Y-m-d H:i', strtotime($order->placed_at)) }}
        </div>

        <div class="right">
            POS :
            {{ $order->pos_no ?? '231' }}
        </div>
    </div>

    <div class="clearfix"></div>

    <div style="margin-top:10px;">
        <strong>
            Bill :
            {{ $order->invoice_no }}
        </strong>
    </div>

    <div style="margin-top:8px;">
        Cashier :
        {{ $order->cashier_name ?? 'Admin' }}
    </div>

    <div class="divider"></div>

    <!-- CUSTOMER -->
    <div class="customer-section">

        <p>
            <strong>Customer Name :</strong>
            {{ $customer->name ?? '' }}
        </p>

        <p>
            <strong>Customer ID :</strong>
            {{ $customer->customer_code ?? '' }}
        </p>

        <p>
            <strong>Email :</strong>
            {{ $customer->email ?? '' }}
        </p>

        <p>
            <strong>Mobile no :</strong>
            {{ $customer->phone ?? '' }}
        </p>

    </div>

    <div class="divider"></div>

    <!-- PRODUCT HEADER -->
    <table class="items-table">

        <thead>

        <tr>
            <th width="55%">
                Description<br>
                SAQ Code<br>
                HSN Code
            </th>

            <th width="10%" class="text-center">
                QTY
            </th>

            <th width="17%" class="text-right">
                Unit Amt
            </th>

            <th width="18%" class="text-right">
                Total Amt
            </th>
        </tr>

        </thead>

        <tbody>

        @php
            $totalQty = 0;
        @endphp

        @foreach($order->items as $item)

            @php
                $totalQty += $item->quantity;
            @endphp

            <tr>

                <td>

                    <div class="item-name">
                        {{ $item->product_name }}
                    </div>

                    <div class="sub-code">
                        {{ $item->saq_code ?? $item->sku }}
                    </div>

                    <div class="sub-code">
                        {{ $item->hsn_code ?? '' }}
                    </div>

                </td>

                <td class="qty">
                    {{ $item->quantity }}
                </td>

                <td class="amount">
                    ₹{{ number_format($item->final_price,2) }}
                </td>

                <td class="amount">
                    <strong>
                        ₹{{ number_format($item->subtotal,2) }}
                    </strong>
                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

    <div class="divider"></div>

    <!-- SUMMARY -->

    <table class="summary-table">

        <tr>

            <td>
                <strong>
                    Total QTY :
                    {{ $totalQty }}
                </strong>
            </td>

            <td class="text-right grand">
                Grand Total :
                ₹{{ number_format($order->grand_total,2) }}
            </td>

        </tr>

        <tr>

            <td>
                <strong>Net Payable</strong>
            </td>

            <td class="text-right">
                <strong>
                    ₹{{ number_format($order->grand_total,2) }}
                </strong>
            </td>

        </tr>

        <tr>

            <td>
                Change Amount
            </td>

            <td class="text-right">
                ₹0.00
            </td>

        </tr>

    </table>

    <div class="divider"></div>

    <!-- TAX INFORMATION -->

    <div class="tax-title">
        Tax Information
    </div>

    <table class="tax-table">

        <tr>
            <td>Taxable Amount</td>
            <td class="text-right">
                ₹{{ number_format($order->taxable_amount ?? ($order->grand_total - $order->tax_amount),2) }}
            </td>
        </tr>

        <tr>
            <td>CGST (9.00%)</td>
            <td class="text-right">
                ₹{{ number_format(($order->tax_amount ?? 0)/2,2) }}
            </td>
        </tr>

        <tr>
            <td>SGST (9.00%)</td>
            <td class="text-right">
                ₹{{ number_format(($order->tax_amount ?? 0)/2,2) }}
            </td>
        </tr>

        <tr>
            <td colspan="2"></td>
        </tr>

        <tr>
            <td class="tax-total">
                Total Tax
            </td>

            <td class="text-right tax-total">
                ₹{{ number_format($order->tax_amount ?? 0,2) }}
            </td>
        </tr>

    </table>

    <div class="divider"></div>

    <!-- FOOTER -->

    <div class="footer">

        <div class="footer-company">
            {{ $business->business_name }}
        </div>

        <p>
            {{ $business->address_line_1 }}
            {{ $business->address_line_2 }}
        </p>

        <p>
            {{ $business->city }},
            {{ $business->state }}
            {{ $business->pincode }}
        </p>

        <p>
            GSTIN :
            {{ $business->gst_no ?? '' }}
        </p>

        <p>
            CIN :
            {{ $business->cin_no ?? '' }}
        </p>

        <p>
            Phone :
            {{ $business->phone }}
        </p>

        <p>
            Email :
            {{ $business->email }}
        </p>

    </div>

</div>

</body>
</html>
