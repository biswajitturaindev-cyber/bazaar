<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice Details</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f5f5f5;
    font-family: Arial, sans-serif;
}

.invoice-wrapper{
    max-width:700px;
    margin:auto;
    background:#fff;
    min-height:100vh;
}

.page-header{
    padding:20px;
    display:flex;
    align-items:center;
    gap:15px;
}

.back-btn{
    width:48px;
    height:48px;
    border-radius:50%;
    background:#FBE8DD;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#F28C45;
    font-size:24px;
}

.profile-section{
    text-align:center;
    padding:10px 20px 30px;
}

.profile-section img{
    width:180px;
    height:120px;
    object-fit:cover;
}

.shop-name{
    font-size:32px;
    font-weight:700;
    margin-top:10px;
}

.profile-info{
    font-size:18px;
    margin-top:10px;
}

.dashed{
    border-top:2px dashed #ddd;
}

.invoice-info{
    padding:25px;
}

.invoice-info p{
    margin:0;
    font-size:18px;
    font-weight:600;
}

.customer-row{
    padding:20px 25px;
    font-size:18px;
    font-weight:600;
}

.table-area{
    padding:0 25px;
}

.table thead th{
    font-size:18px;
    font-weight:700;
    padding:18px 0;
}

.table tbody td{
    font-size:18px;
    padding:20px 0;
}

.summary{
    padding:25px;
}

.summary .left{
    font-size:18px;
    font-weight:600;
}

.summary .right{
    text-align:right;
    font-size:18px;
    font-weight:600;
}

.payment-area{
    padding:25px;
    font-size:18px;
    font-weight:700;
}

.footer-text{
    text-align:center;
    padding:20px;
    font-size:18px;
    font-weight:700;
}
</style>
</head>
<body>

<!DOCTYPE html>

<html>
<head>
<meta charset="utf-8">

<style>
body{
    font-family: DejaVu Sans, sans-serif;
    margin:0;
    padding:0;
    background:#fff;
}

.invoice-wrapper{
    width:100%;
}

.profile-section{
    text-align:center;
    padding:15px 20px 25px;
}

.profile-section img{
    width:150px;
    height:100px;
}

.shop-name{
    font-size:28px;
    font-weight:bold;
    margin-top:10px;
}

.profile-info{
    font-size:14px;
    margin-top:6px;
}

.dashed{
    border-top:1px dashed #999;
}

.section{
    padding:15px 20px;
}

.table-full{
    width:100%;
    border-collapse:collapse;
}

.table-full td{
    padding:4px 0;
}

.items-table{
    width:100%;
    border-collapse:collapse;
}

.items-table th{
    padding:10px 5px;
    border-bottom:1px solid #ddd;
    font-size:14px;
}

.items-table td{
    padding:10px 5px;
    font-size:14px;
}

.text-right{
    text-align:right;
}

.text-center{
    text-align:center;
}

.footer{
    text-align:center;
    padding:20px;
    font-size:18px;
    font-weight:bold;
}
</style>

</head>
<body>

<div class="invoice-wrapper">
<div class="profile-section">

    @if(!empty($logo))
        <img src="{{ $logo }}" width="120" alt="Logo">
    @endif

    <div class="shop-name">
        {{ $business->business_name }}
    </div>

    <div class="profile-info">
        <strong>Phone Number:</strong>
        {{ $business->user->mobile ?? '-' }}
    </div>

    <div class="profile-info">
        <strong>Address:</strong>
        {{ $business->address->address_line_1 ?? '-' }}, {{ $business->address->address_line_2 ?? '-' }}, {{ $business->address->pincode ?? '-' }}
    </div>

     <div class="profile-info">
        <strong>GST Number:</strong>
        {{ $business->gst_number ?? '-' }}
    </div>


</div>

<div class="dashed"></div>

<div class="section">

    <table class="table-full">
        <tr>
            <td>
                <strong>Invoice No :</strong>
                {{ $order->invoice_no }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>Date :</strong>
                {{ $order->created_at->format('d M Y') }}
            </td>

            <td class="text-right">
                <strong>Time :</strong>
                {{ $order->created_at->format('h:i A') }}
            </td>
        </tr>
    </table>

</div>

<div class="dashed"></div>

<div class="section">
    <strong>Customer :</strong>
     {{ $address->phone ?? '-' }}
</div>

<div class="dashed"></div>

<div class="section">

    <table class="items-table">

        <thead>
            <tr>
                <th align="left">Item</th>
                <th align="center">Qty</th>
                <th align="right">Price</th>
                <th align="right">Amount</th>
            </tr>
        </thead>

        <tbody>

        @foreach($order->items as $item)

            <tr>
                <td>{{ $item->product_name }}</td>

                <td class="text-center">
                    {{ $item->quantity }}
                </td>

                <td class="text-right">
                    ₹{{ number_format($item->final_price,2) }}
                </td>

                <td class="text-right">
                    ₹{{ number_format($item->subtotal,2) }}
                </td>
            </tr>

        @endforeach

        </tbody>

    </table>

</div>

<div class="dashed"></div>

<div class="section">

    <table class="table-full">

        <tr>
            <td>
                <strong>Total Quantity :</strong>
                {{ $order->items->sum('quantity') }}
            </td>

            <td class="text-right">
                <strong>Sub Total :</strong>
                ₹{{ number_format($order->items->sum('subtotal'),2) }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>Total Discount :</strong>
            </td>

            <td class="text-right">
                ₹{{ number_format($order->discount_amount,2) }}
            </td>
        </tr>

    </table>

</div>

<div class="dashed"></div>

<div class="section">

    <table class="table-full">
        <tr>
            <td>
                <strong>Payment :</strong>
                {{ match($order->payment_method) {
                    0 => 'WALLET',
                    1 => 'ONLINE',
                    2 => 'COD',
                    default => 'CASH'
                } }}
            </td>

            <td class="text-right">
                <strong>Total Amount :</strong>
                ₹{{ number_format($order->grand_total,2) }}
            </td>
        </tr>
    </table>

</div>

<div class="footer">
    Thank You & Visit Again
</div>


</div>

</body>
</html>


</body>
</html>
