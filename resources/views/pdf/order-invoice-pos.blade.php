<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice Details</title>
<style>
    *{
    box-sizing:border-box;
}

body{
    font-family: DejaVu Sans, sans-serif;
    font-size:13px;
    color:#222;
    margin:0;
    padding:25px;
    line-height:1.5;
}

.container{
    width:100%;
}

/* =========================
   HEADER
========================= */

.header-title{
    text-align:center;
    font-size:28px;
    font-weight:bold;
    margin-bottom:25px;
}

.row{
    width:100%;
}

.left{
    float:left;
}

.right{
    float:right;
}

.clearfix{
    clear:both;
}

.text-right{
    text-align:right;
}

.text-center{
    text-align:center;
}

/* =========================
   DIVIDER
========================= */

.divider{
    border-top:1px dashed #d9d9d9;
    margin:18px 0;
}

/* =========================
   CUSTOMER
========================= */

.customer-section{
    text-align:center;
    margin:18px 0;
}

.customer-section p{
    margin:4px 0;
    font-size:14px;
}

.customer-section strong{
    font-weight:bold;
}

/* =========================
   TABLE
========================= */

table{
    width:100%;
    border-collapse:collapse;
}

.items-table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
}

.items-table th{
    font-size:16px;
    font-weight:bold;
    text-align:left;
    vertical-align:top;
    padding-bottom:18px;
}

.items-table td{
    vertical-align:top;
    padding:12px 0;
}

/* =========================
   COLUMN WIDTHS
========================= */

.desc-col{
    width:65%;
}

.qty-col{
    width:10%;
    text-align:center;
}

.amount-col{
    width:25%;
    text-align:right;
}

/* =========================
   PRODUCT
========================= */

.product-row{
    page-break-inside:avoid;
}

.product-name{
    font-size:16px;
    font-weight:bold;
    color:#222;
    margin-bottom:4px;
}

.product-code{
    font-size:12px;
    color:#999;
    line-height:18px;
}

.qty-column{
    text-align:center;
    font-size:15px;
    padding-top:10px;
}

.amount-column{
    text-align:right;
    white-space:nowrap;
}

.unit-price{
    font-size:15px;
    color:#444;
    margin-bottom:4px;
}

.total-price{
    font-size:18px;
    font-weight:bold;
    color:#222;
}

/* =========================
   SUMMARY
========================= */

.summary-table{
    margin-top:10px;
}

.summary-table td{
    padding:8px 0;
    font-size:15px;
}

.grand{
    font-size:18px;
    font-weight:bold;
}

.net-payable{
    font-size:18px;
    font-weight:bold;
}

/* =========================
   TAX
========================= */

.tax-title{
    font-size:20px;
    font-weight:bold;
    margin-bottom:12px;
}

.tax-table td{
    padding:6px 0;
    font-size:15px;
}

.tax-total{
    border-top:2px solid #c9c9c9;
    padding-top:10px !important;
    font-size:18px;
    font-weight:bold;
}

/* =========================
   FOOTER
========================= */

.footer{
    text-align:center;
    margin-top:25px;
}

.footer-company{
    font-size:22px;
    font-weight:bold;
    margin-bottom:10px;
}

.footer p{
    margin:4px 0;
    color:#444;
    font-size:13px;
}

/* =========================
   DOMPDF FIX
========================= */

.page-break{
    page-break-after:always;
}

.no-break{
    page-break-inside:avoid;
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

            <th class="desc-col">
                Description<br>
                SAQ Code<br>
                HSN Code
            </th>

            <th class="qty-col">
                QTY
            </th>

            <th class="amount-col">
                Unit Amt<br>
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

            <tr class="product-row">

                <td class="desc-col">

                    <div class="product-name">
                        {{ $item->product_name }}
                    </div>

                    <div class="product-code">
                        {{ $item->sku }}
                    </div>

                    <div class="product-code">
                        {{ $item->hsn_code }}
                    </div>

                </td>

                <td class="qty-column">
                    {{ $item->quantity }}
                </td>

                <td class="amount-column">

                    <div class="unit-price">
                        ₹{{ number_format($item->final_price,2) }}
                    </div>

                    <div class="total-price">
                        ₹{{ number_format($item->subtotal,2) }}
                    </div>

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
