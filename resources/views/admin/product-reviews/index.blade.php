@extends('admin.layouts.master')

@section('title')
    Product Review Master List
@endsection

@section('breadcrumb')
    Product Review Master List
@endsection

<style>
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 9999px;
    }

    .status-badge.pending {
        background-color: #fef3c7;
        color: #b45309;
    }

    .status-badge.approved {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-badge.rejected {
        background-color: #fee2e2;
        color: #991b1b;
    }
</style>
@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            <div class="flex justify-between items-center px-5 py-3 border-b">
                <h2 class="text-lg font-semibold">Product Review Master List</h2>
            </div>

            <div class="overflow-x-auto p-5">
                <table class="w-full text-sm text-left" id="example">

                    <thead class="bg-gray-100">
                        <tr class="border">

                            <th class="px-3 py-2">Sl.No</th>

                            <th class="px-3 py-4">Vendor Name</th>

                            <th class="px-3 py-4">Category</th>

                            <th class="px-3 py-4">Product Name</th>

                            <th class="px-3 py-2">HSN</th>

                            <th class="px-3 py-2">MRP</th>

                            <th class="px-3 py-2">Final Price</th>

                            <th class="px-3 py-2">Commission</th>

                            <th class="px-3 py-2">Vendor Commission</th>
                            <th class="px-3 py-2">Vendor Commission Approval Status</th>

                            <th class="px-3 py-2 text-center">Image</th>

                            <th class="px-3 py-2 text-center">Status</th>

                            <th class="px-3 py-2 text-center">Action</th>

                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        @forelse ($products as $product)
                            @php

                                $variant = $product->primaryVariant;

                                $image = null;

                                if ($variant && $variant->images && $variant->images->count() > 0) {
                                    $image = $variant->images->first();
                                }

                                // STATUS
                                $statusClass = match ($product->status) {
                                    1 => 'bg-green-100 text-green-700',
                                    0 => 'bg-red-100 text-red-700',
                                    default => 'bg-yellow-100 text-yellow-700',
                                };

                                $statusLabel = match ($product->status) {
                                    1 => 'Active',
                                    0 => 'Inactive',
                                    default => 'Unapproved',
                                };

                            @endphp

                            <tr class="border-l border-r hover:bg-gray-50 transition">

                                {{-- SERIAL --}}
                                <td class="px-3 py-2">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- Business --}}
                                <td class="px-3 py-4">
                                    <a href="" class="text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $product->business?->business_name ?? '-' }}
                                    </a>
                                </td>

                                {{-- CATEGORY --}}
                                <td class="px-3 py-4">
                                    {{ $product->category?->name ?? '-' }}
                                </td>


                                {{-- PRODUCT NAME --}}
                                <td class="px-3 py-4 font-medium">
                                    {{ $product->name ?? '-' }}
                                </td>


                                {{-- HSN --}}
                                <td class="px-3 py-2">
                                    {{ $product->hsn?->hsn_code ?? '-' }}
                                </td>

                                {{-- MRP --}}
                                <td class="px-3 py-2">
                                    ₹{{ !empty($variant?->mrp) ? number_format($variant->mrp, 2) : '-' }}
                                </td>

                                {{-- SELLING PRICE --}}
                                <td class="px-3 py-2 text-green-600 font-semibold">
                                    ₹{{ !empty($variant?->final_price) ? number_format($variant->final_price, 2) : '-' }}
                                </td>

                                {{-- Comm --}}
                                <td class="px-3 py-2">
                                    {{ $product->commission ?? '-' }}
                                </td>

                                {{-- VENDOR COMMISSION --}}
                                <td class="px-3 py-2">
                                    {{ $product->vendor_commission ?? '-' }}
                                </td>

                                {{-- VENDOR COMMISSION APPROVAL STATUS --}}
                                <td class="px-3 py-2">
                                    @if($product->vendor_commission_approval_status == 0)
                                        <span class="status-badge pending">Waiting for Approval</span>

                                    @elseif($product->vendor_commission_approval_status == 1)
                                        <span class="status-badge approved">Approved</span>

                                    @elseif($product->vendor_commission_approval_status == 2)
                                        <span class="status-badge rejected">Rejected</span>
                                    @endif
                                </td>

                                {{-- IMAGE --}}
                                <td class="px-3 py-2 text-center">

                                    @if ($image && !empty($image->image_medium))
                                        <a href="{{ asset('storage/' . $image->image_medium) }}" data-fancybox="products"
                                            data-caption="{{ $product->name }}">

                                            <img src="{{ asset('storage/' . $image->image_medium) }}" width="50"
                                                height="50" class="rounded border mx-auto cursor-pointer"
                                                style="object-fit: cover;">
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">
                                            No Image
                                        </span>
                                    @endif

                                </td>

                                {{-- STATUS --}}
                                <td class="px-3 py-2 text-center">

                                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>

                                </td>

                                {{-- ACTION --}}
                                <td class="px-3 py-2">

                                    <div class="flex items-center justify-center gap-2">

                                        {{-- VIEW PRODUCT DETAILS --}}
                                        <a href="{{ route('product-reviews.show', $product->id) }}"
                                            class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded text-xs">
                                            View Details
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="11" class="text-center py-6 text-gray-500">
                                    No products found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

    @push('scripts')
        <link href="{{ asset('admin_assets/datatables/dataTables.dataTables.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
        <script src="{{ asset('admin_assets/datatables/dataTables.js') }}"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {

                let table = $('#example').DataTable({
                    paging: true,
                    searching: true,
                    info: true,
                    pageLength: 10,

                    language: {
                        emptyTable: "No products found"
                    },

                    columnDefs: [{
                        targets: 0,
                        orderable: false,
                        searchable: false
                    }]
                });

                table.on('draw.dt', function () {

                    let PageInfo = table.page.info();

                    table.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                        cell.innerHTML = i + 1 + PageInfo.start;
                    });

                    Fancybox.bind("[data-fancybox='products']");
                });

            });
        </script>
    @endpush
@endsection
