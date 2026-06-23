@extends('admin.layouts.master')

@section('title')
    Vendor Commission Report
@endsection

@section('breadcrumb')
    Vendor Commission Report
@endsection

@section('content')
    <div class="grid grid-cols-1">

        <div class="bg-white rounded-xl shadow-md overflow-hidden">

            {{-- Header --}}
            <div class="border-b px-6 py-4">
                <h2 class="text-2xl font-bold text-gray-800">
                    Vendor Commission Report
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Track vendor sales, invoices and commission earnings
                </p>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-6">

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                    <p class="text-sm text-gray-500">
                        Total Vendors
                    </p>

                    <h3 class="text-3xl font-bold text-blue-600 mt-2">
                        {{ $vendorCommissions->count() }}
                    </h3>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                    <p class="text-sm text-gray-500">
                        Total Sale
                    </p>

                    <h3 class="text-3xl font-bold text-green-600 mt-2">
                        ₹ {{ number_format($vendorCommissions->sum('total_sale'), 2) }}
                    </h3>
                </div>

                <div class="bg-orange-50 border border-orange-200 rounded-xl p-5">
                    <p class="text-sm text-gray-500">
                        Total Commission
                    </p>

                    <h3 class="text-3xl font-bold text-orange-600 mt-2">
                        ₹ {{ number_format($vendorCommissions->sum('commission_amount'), 2) }}
                    </h3>
                </div>

            </div>

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('vendor-commissions.index') }}">

                <div class="mx-6 mb-6 bg-gray-50 border rounded-xl p-5">

                    <h3 class="font-semibold text-lg mb-4">
                        Filter Report
                    </h3>

                    {{-- Top Filters --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        {{-- Today --}}
                        <div class="filter-card cursor-pointer border rounded-xl p-5 text-center transition-all duration-200
                        {{ request('filter_type') == 'today' ? 'border-blue-500 bg-blue-50' : '' }}"
                            data-filter="today">

                            <i class="ri-calendar-check-line text-4xl text-blue-600"></i>

                            <h4 class="font-semibold mt-3">
                                Today
                            </h4>

                            <p class="text-xs text-gray-500 mt-1">
                                Today's Commission
                            </p>

                        </div>

                        {{-- Current Month --}}
                        <div class="filter-card cursor-pointer border rounded-xl p-5 text-center transition-all duration-200
                        {{ request('filter_type') == 'month' ? 'border-green-500 bg-green-50' : '' }}"
                            data-filter="month">

                            <i class="ri-calendar-2-line text-4xl text-green-600"></i>

                            <h4 class="font-semibold mt-3">
                                Current Month
                            </h4>

                            <p class="text-xs text-gray-500 mt-1">
                                Monthly Commission
                            </p>

                        </div>

                        {{-- Financial Year --}}
                        <div class="border rounded-xl p-5 bg-white">

                            <h4 class="font-semibold mb-3">
                                Financial Year
                            </h4>

                            <select name="financial_year" class="w-full border rounded-lg px-3 py-2">

                                <option value="">
                                    Select FY
                                </option>

                                @for ($year = date('Y'); $year >= 2020; $year--)
                                    <option value="{{ $year }}-{{ $year + 1 }}"
                                        {{ request('financial_year') == $year . '-' . ($year + 1) ? 'selected' : '' }}>
                                        {{ $year }}-{{ substr($year + 1, -2) }}
                                    </option>
                                @endfor

                            </select>

                        </div>

                    </div>

                    {{-- Full Width Date Range --}}
                    <div class="mt-4 border rounded-xl p-5 bg-white">

                        <h4 class="font-semibold mb-4">
                            Custom Date Range
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-1">
                                    From Date
                                </label>

                                <input type="date" name="from_date" value="{{ request('from_date') }}"
                                    class="w-full border rounded-lg px-3 py-2">
                            </div>

                            <div class="text-center font-semibold text-gray-500">
                                TO
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-1">
                                    To Date
                                </label>

                                <input type="date" name="to_date" value="{{ request('to_date') }}"
                                    class="w-full border rounded-lg px-3 py-2">
                            </div>

                        </div>

                    </div>

                    <input type="hidden" name="filter_type" id="filter_type" value="{{ request('filter_type') }}">

                    <div class="flex justify-end gap-3 mt-5">

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                            <i class="ri-search-line mr-1"></i>
                            Search
                        </button>

                        <a href="{{ route('vendor-commissions.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                            Reset
                        </a>

                    </div>

                </div>

            </form>

            {{-- Table --}}
            <div class="px-6 pb-6 overflow-x-auto">

                <table class="w-full text-sm" id="example">

                    <thead class="bg-slate-800 text-white">

                        <tr>

                            <th class="p-3 text-left">
                                #
                            </th>

                            <th class="p-3 text-left">
                                Vendor Name
                            </th>

                            <th class="p-3 text-center">
                                No. Of Invoice
                            </th>

                            <th class="p-3 text-right">
                                Total Sale
                            </th>

                            {{-- <th class="p-3 text-center">
                            Commission %
                        </th> --}}

                            <th class="p-3 text-right">
                                Commission Amount
                            </th>

                            <th class="p-3 text-center">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($vendorCommissions as $key => $row)
                            <tr class="border-b hover:bg-gray-50">

                                <td class="p-3">
                                    {{ $key + 1 }}
                                </td>

                                <td class="p-3 font-medium">
                                    {{ $row->business_name }} ({{ $row->vendor_id }})
                                </td>

                                <td class="p-3 text-center">
                                    {{ $row->invoice_count }}
                                </td>

                                <td class="p-3 text-right font-semibold text-green-600">
                                    ₹ {{ number_format($row->total_sale, 2) }}
                                </td>

                                {{-- <td class="p-3 text-center">
                                {{ number_format($row->commission_percentage, 2) }}%
                            </td> --}}

                                <td class="p-3 text-right font-semibold text-orange-600">
                                    ₹ {{ number_format($row->commission_amount, 2) }}
                                </td>

                                <td class="p-3 text-center">

                                    <button type="button"
                                        class="viewInvoiceBtn bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-full p-2"
                                        data-business-id="{{ $row->business_id }}">

                                        <i class="ri-eye-line text-lg"></i>

                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center py-8 text-gray-500">
                                    No Records Found
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <?php /* ?> ?> ?> ?>
    <!-- Invoice Modal -->
    <div id="invoiceModal" class="fixed inset-0 z-50 hidden">

        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50"></div>

        <!-- Modal -->
        <div class="relative flex items-center justify-center min-h-screen p-4">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl overflow-hidden">

                <!-- Header -->
                <div class="bg-slate-800 text-white px-6 py-4">

                    <div class="flex justify-between items-center">

                        <div>
                            <h3 class="text-xl font-bold">
                                Vendor Invoice Details
                            </h3>

                            <p class="text-slate-300 text-sm">
                                Invoice wise commission report
                            </p>
                        </div>

                        <button id="closeModal" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20">

                            <i class="ri-close-line text-xl"></i>

                        </button>

                    </div>

                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 bg-gray-50">

                    <div class="bg-white border rounded-xl p-4">
                        <p class="text-sm text-gray-500">
                            Total Invoices
                        </p>

                        <h4 id="modalTotalInvoices" class="text-2xl font-bold text-blue-600">
                            0
                        </h4>
                    </div>

                    <div class="bg-white border rounded-xl p-4">
                        <p class="text-sm text-gray-500">
                            Total Sale
                        </p>

                        <h4 id="modalTotalSale" class="text-2xl font-bold text-green-600">
                            ₹ 0.00
                        </h4>
                    </div>

                    <div class="bg-white border rounded-xl p-4">
                        <p class="text-sm text-gray-500">
                            Total Commission
                        </p>

                        <h4 id="modalTotalCommission" class="text-2xl font-bold text-orange-600">
                            ₹ 0.00
                        </h4>
                    </div>

                </div>

                <!-- Table -->
                <div class="p-6">

                    <div class="overflow-x-auto max-h-[500px]">

                        <table class="w-full">

                            <thead class="sticky top-0 bg-slate-700 text-white">

                                <tr>

                                    <th class="p-3 text-left">
                                        #
                                    </th>

                                    <th class="p-3 text-left">
                                        Invoice No
                                    </th>

                                    <th class="p-3 text-left">
                                        Order No
                                    </th>

                                    <th class="p-3 text-left">
                                        Date
                                    </th>

                                    <th class="p-3 text-right">
                                        Sale Amount
                                    </th>

                                    <th class="p-3 text-center">
                                        Commission %
                                    </th>

                                    <th class="p-3 text-right">
                                        Commission Amount
                                    </th>

                                </tr>

                            </thead>

                            <tbody id="invoiceTableBody">

                                <tr>
                                    <td colspan="7" class="text-center py-10 text-gray-500">
                                        No Data Found
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>
    <?php */ ?>

    <!-- Invoice Modal -->
    <div id="invoiceModal" class="fixed inset-0 z-50 hidden">

        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60"></div>

        <!-- Modal -->
        <div class="relative flex items-center justify-center min-h-screen p-4">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-7xl overflow-hidden">

                <!-- Header -->
                <div class="bg-slate-800 text-white px-6 py-4">

                    <div class="flex justify-between items-center">

                        <div>
                            <h3 class="text-xl font-bold">
                                Vendor Invoice Details
                            </h3>

                            <p class="text-slate-300 text-sm">
                                Invoice wise commission report
                            </p>
                        </div>

                        <button id="closeModal" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 transition">

                            <i class="ri-close-line text-xl"></i>

                        </button>

                    </div>

                </div>

                <!-- Summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 bg-gray-50">

                    <div class="bg-white border rounded-xl p-4 shadow-sm">
                        <p class="text-sm text-gray-500">
                            Total Invoices
                        </p>

                        <h4 id="modalTotalInvoices" class="text-3xl font-bold text-blue-600">
                            0
                        </h4>
                    </div>

                    <div class="bg-white border rounded-xl p-4 shadow-sm">
                        <p class="text-sm text-gray-500">
                            Total Sale
                        </p>

                        <h4 id="modalTotalSale" class="text-3xl font-bold text-green-600">
                            ₹ 0.00
                        </h4>
                    </div>

                    <div class="bg-white border rounded-xl p-4 shadow-sm">
                        <p class="text-sm text-gray-500">
                            Total Commission
                        </p>

                        <h4 id="modalTotalCommission" class="text-3xl font-bold text-orange-600">
                            ₹ 0.00
                        </h4>
                    </div>

                </div>

                <!-- Table Area -->
                <div class="p-6">

                    <div class="overflow-auto max-h-[600px]">

                        <table class="w-full border-collapse">

                            <thead class="sticky top-0 bg-slate-700 text-white z-10">

                                <tr>

                                    <th class="p-3 text-left">#</th>

                                    <th class="p-3 text-left">
                                        Invoice No
                                    </th>

                                    <th class="p-3 text-left">
                                        Order No
                                    </th>

                                    <th class="p-3 text-left">
                                        Total Items
                                    </th>

                                    <th class="p-3 text-left">
                                        Date
                                    </th>

                                    <th class="p-3 text-right">
                                        Sale Amount
                                    </th>

                                    <th class="p-3 text-right">
                                        Commission
                                    </th>

                                    <th class="p-3 text-center">
                                        Details
                                    </th>

                                </tr>

                            </thead>

                            <tbody id="invoiceTableBody">

                                <tr>

                                    <td colspan="8" class="text-center py-10 text-gray-500">

                                        No Data Found

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.filter-card').forEach(card => {

            card.addEventListener('click', function() {

                document.querySelectorAll('.filter-card').forEach(c => {
                    c.classList.remove(
                        'border-blue-500',
                        'bg-blue-50',
                        'border-green-500',
                        'bg-green-50'
                    );
                });

                if (this.dataset.filter === 'today') {
                    this.classList.add('border-blue-500', 'bg-blue-50');
                }

                if (this.dataset.filter === 'month') {
                    this.classList.add('border-green-500', 'bg-green-50');
                }

                document.getElementById('filter_type').value = this.dataset.filter;

                // Clear Financial Year
                document.querySelector('select[name="financial_year"]').value = '';

                // Clear Date Range
                document.querySelector('input[name="from_date"]').value = '';
                document.querySelector('input[name="to_date"]').value = '';
            });

        });

        // Financial Year Change
        document.querySelector('select[name="financial_year"]').addEventListener('change', function() {

            if (this.value !== '') {

                document.getElementById('filter_type').value = '';

                document.querySelector('input[name="from_date"]').value = '';
                document.querySelector('input[name="to_date"]').value = '';

                document.querySelectorAll('.filter-card').forEach(c => {
                    c.classList.remove(
                        'border-blue-500',
                        'bg-blue-50',
                        'border-green-500',
                        'bg-green-50'
                    );
                });
            }

        });

        // Date Range Change
        document.querySelectorAll('input[name="from_date"], input[name="to_date"]').forEach(input => {

            input.addEventListener('change', function() {

                document.getElementById('filter_type').value = '';

                document.querySelector('select[name="financial_year"]').value = '';

                document.querySelectorAll('.filter-card').forEach(c => {
                    c.classList.remove(
                        'border-blue-500',
                        'bg-blue-50',
                        'border-green-500',
                        'bg-green-50'
                    );
                });

            });

        });

        $(document).on('click', '.viewInvoiceBtn', function() {

            let businessId = $(this).data('business-id');

            $('#invoiceTableBody').html(`
                <tr>
                    <td colspan="8" class="text-center py-5">
                        Loading...
                    </td>
                </tr>
            `);

            $('#invoiceModal').removeClass('hidden');

            $.ajax({
                url: '/admin/vendor-commissions/invoices/' + businessId,
                type: 'GET',
                data: {
                    filter_type: $('#filter_type').val(),
                    financial_year: $('select[name="financial_year"]').val(),
                    from_date: $('input[name="from_date"]').val(),
                    to_date: $('input[name="to_date"]').val()
                },

                success: function(response) {
                    let html = '';
                    // Summary
                    $('#modalTotalInvoices').text(
                        response.summary.invoice_count
                    );

                    $('#modalTotalSale').text(
                        '₹ ' +
                        Number(response.summary.total_sale).toLocaleString(
                            'en-IN', {
                                minimumFractionDigits: 2
                            }
                        )
                    );

                    $('#modalTotalCommission').text(
                        '₹ ' +
                        Number(response.summary.total_commission).toLocaleString(
                            'en-IN', {
                                minimumFractionDigits: 2
                            }
                        )
                    );

                    if (response.data.length > 0) {

                        response.data.forEach(function(order, index) {

                            html += `

                            <tr class="border-b hover:bg-gray-50">

                                <td class="p-3">
                                    ${index + 1}
                                </td>

                                <td class="p-3 font-semibold">
                                    ${order.invoice_no ?? '-'}
                                </td>

                                <td class="p-3">
                                    ${order.order_no ?? '-'}
                                </td>

                                <td class="p-3">
                                    ${order.total_items ?? ''}
                                </td>

                                <td class="p-3">
                                    ${order.created_at}
                                </td>

                                <td class="p-3 text-right text-green-600 font-semibold">
                                    ₹ ${parseFloat(order.grand_total || 0).toFixed(2)}
                                </td>

                                <td class="p-3 text-right text-orange-600 font-semibold">
                                    ₹ ${parseFloat(order.commission_amount || 0).toFixed(2)}
                                </td>

                                <td class="p-3 text-center">

                                    <button
                                        type="button"
                                        class="toggleItems bg-blue-100 hover:bg-blue-200 text-blue-600 px-3 py-1 rounded-lg"
                                        data-target="items_${order.id}">

                                        <i class="ri-eye-line"></i>

                                    </button>

                                </td>

                            </tr>

                            <tr id="items_${order.id}" class="hidden bg-slate-50">

                                <td colspan="8">

                                    <div class="p-4">

                                        <table class="w-full border">

                                            <thead class="bg-slate-200">

                                                <tr>

                                                    <th class="p-2 text-left">
                                                        Product
                                                    </th>

                                                    <th class="p-2 text-center">
                                                        Qty
                                                    </th>

                                                    <th class="p-2 text-right">
                                                        Price
                                                    </th>

                                                    <th class="p-2 text-right">
                                                        Subtotal
                                                    </th>

                                                    <th class="p-2 text-center">
                                                        Commission %
                                                    </th>

                                                    <th class="p-2 text-right">
                                                        Commission Amount
                                                    </th>

                                                </tr>

                                            </thead>

                                            <tbody>
                            `;

                            if (order.items && order.items.length > 0) {

                                order.items.forEach(function(item) {

                                    html += `

                                    <tr>

                                        <td class="p-2">
                                            ${item.product_name ?? '-'}
                                        </td>

                                        <td class="p-2 text-center">
                                            ${item.quantity ?? 0}
                                        </td>

                                        <td class="p-2 text-right">
                                            ₹ ${parseFloat(item.selling_price || 0).toFixed(2)}
                                        </td>

                                        <td class="p-2 text-right">
                                            ₹ ${parseFloat(item.subtotal || 0).toFixed(2)}
                                        </td>

                                        <td class="p-2 text-center">
                                            ${parseFloat(item.commission_percent || 0).toFixed(2)}%
                                        </td>

                                        <td class="p-2 text-right text-orange-600">
                                            ₹ ${parseFloat(item.commission_amount || 0).toFixed(2)}
                                        </td>

                                    </tr>

                                    `;
                                });

                            } else {

                                html += `
                                    <tr>
                                        <td colspan="6"
                                            class="text-center py-3 text-gray-500">
                                            No Products Found
                                        </td>
                                    </tr>
                                `;
                            }

                            html += `

                                            </tbody>

                                        </table>

                                    </div>

                                </td>

                            </tr>
                            `;
                        });

                    } else {

                        html = `
                            <tr>
                                <td colspan="8"
                                    class="text-center py-8 text-gray-500">
                                    No Invoice Found
                                </td>
                            </tr>
                        `;
                    }

                    $('#invoiceTableBody').html(html);

                },

                error: function() {

                    $('#invoiceTableBody').html(`
                        <tr>
                            <td colspan="8"
                                class="text-center py-8 text-red-500">
                                Failed to load invoices.
                            </td>
                        </tr>
                    `);

                }

            });

        });

        // Expand / Collapse Product Details
        $(document).on('click', '.toggleItems', function() {

            let target = $(this).data('target');

            $('#' + target).toggleClass('hidden');

        });

        // Close Modal
        $(document).on('click', '#closeModal', function() {

            $('#invoiceModal').addClass('hidden');

        });

        // Close Modal on Backdrop Click
        $('#invoiceModal').on('click', function(e) {

            if (e.target === this) {
                $('#invoiceModal').addClass('hidden');
            }

        });
    </script>
@endpush
