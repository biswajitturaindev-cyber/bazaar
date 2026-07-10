@extends('admin.layouts.master')

@section('title')
    Vendor Products
@endsection

@section('breadcrumb')
    Vendor Products
@endsection

@section('content')

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">

        <div class="bg-white rounded-xl shadow-[0px_4px_16px_rgba(0,0,0,0.06)] p-5 flex items-center gap-4 border border-gray-100 hover:shadow-[0px_6px_20px_rgba(0,0,0,0.10)] transition-shadow duration-200">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgba(242,101,34,0.10);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="#f26522" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Products</p>
                <h2 class="text-2xl font-bold text-gray-800 leading-tight">{{ $products->count() }}</h2>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-[0px_4px_16px_rgba(0,0,0,0.06)] p-5 flex items-center gap-4 border border-gray-100 hover:shadow-[0px_6px_20px_rgba(0,0,0,0.10)] transition-shadow duration-200">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgba(34,197,94,0.10);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Active Products</p>
                <h2 class="text-2xl font-bold text-gray-800 leading-tight">{{ $activeCount ?? 289 }}</h2>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-[0px_4px_16px_rgba(0,0,0,0.06)] p-5 flex items-center gap-4 border border-gray-100 hover:shadow-[0px_6px_20px_rgba(0,0,0,0.10)] transition-shadow duration-200">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgba(239,68,68,0.10);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Inactive Products</p>
                <h2 class="text-2xl font-bold text-gray-800 leading-tight">{{ $inactiveCount ?? 100 }}</h2>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-[0px_4px_16px_rgba(0,0,0,0.06)] p-5 flex items-center gap-4 border border-gray-100 hover:shadow-[0px_6px_20px_rgba(0,0,0,0.10)] transition-shadow duration-200">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgba(245,158,11,0.10);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="#d97706" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Pending Review</p>
                <h2 class="text-2xl font-bold text-gray-800 leading-tight">{{ $pendingCount ?? 24 }}</h2>
            </div>
        </div>

    </div>

    {{-- Main Table Card --}}
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl border border-gray-100">

        {{-- Card Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-800">All Vendor Products</h3>
                <p class="text-xs text-gray-400 mt-0.5">Manage and monitor all listed products</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                {{-- Export Button --}}
                <button type="button"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:border-orange-400 hover:text-orange-500 transition-colors duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </button>
                {{-- Add Product Button --}}
                {{-- <a href="{{ route('admin.products.create') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white rounded-lg transition-all duration-150 hover:opacity-90 hover:shadow-md"
                    style="background-color: #f26522;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Product
                </a> --}}
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('success') || session('error'))
            <div class="px-6 pt-4">
                @if (session('success'))
                    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-2.5 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-2.5 rounded-lg mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        @endif

        {{-- Table --}}
        <div class="overflow-x-auto p-5">
            <table id="example" class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 rounded-tl-lg">#</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Product Name</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Category</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Status</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 rounded-tr-lg">Created At</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $index => $product)
                        <tr class="hover:bg-orange-50/40 transition-colors duration-100 group">

                            <td class="px-4 py-3 text-gray-400 text-xs font-medium">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    {{-- Product thumbnail placeholder --}}
                                    <div class="w-9 h-9 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center flex-shrink-0 text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-gray-800 text-sm">{{ $product->name ?? 'N/A' }}</span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                    style="background: rgba(242,101,34,0.10); color: #f26522;">
                                    {{ class_basename($product) }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                @php $isActive = ($product->status ?? 1) === 'active'; @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $isActive ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    {{ $isActive ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $product->created_at?->format('d M Y') }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-16 text-gray-400">
                                <div class="flex flex-col items-center gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-400">No Products Found</p>
                                    <p class="text-xs text-gray-300">Products added by this vendor will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- Scripts --}}
    @push('scripts')
        <link href="{{ asset('admin_assets/datatables/dataTables.dataTables.css') }}" rel="stylesheet">
        <script src="{{ asset('admin_assets/datatables/dataTables.js') }}"></script>

        <style>
            /* Override DataTables to match Reshera style */
            #example_wrapper .dataTables_length select,
            #example_wrapper .dataTables_filter input {
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 5px 10px;
                font-size: 13px;
                outline: none;
            }
            #example_wrapper .dataTables_filter input:focus {
                border-color: #f26522;
                box-shadow: 0 0 0 2px rgba(242,101,34,0.10);
            }
            #example_wrapper .dataTables_info,
            #example_wrapper .dataTables_length {
                font-size: 12px;
                color: #9ca3af;
            }
            #example_wrapper .dataTables_paginate .paginate_button {
                border-radius: 7px !important;
                padding: 4px 10px !important;
                font-size: 12px !important;
                font-weight: 600 !important;
                margin: 0 2px !important;
                border: 1px solid #e5e7eb !important;
                color: #6b7280 !important;
            }
            #example_wrapper .dataTables_paginate .paginate_button:hover {
                background: #fff7f4 !important;
                border-color: #f26522 !important;
                color: #f26522 !important;
            }
            #example_wrapper .dataTables_paginate .paginate_button.current,
            #example_wrapper .dataTables_paginate .paginate_button.current:hover {
                background: #f26522 !important;
                border-color: #f26522 !important;
                color: #fff !important;
            }
            #example_wrapper .dataTables_paginate .paginate_button.disabled {
                opacity: 0.4 !important;
            }
        </style>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                $('#example').DataTable({
                    paging: true,
                    searching: true,
                    info: true,
                    pagingType: "simple_numbers",
                    language: {
                        search: "",
                        searchPlaceholder: "Search products…",
                        lengthMenu: "_MENU_ entries per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        emptyTable: "No products found",
                        zeroRecords: "No matching products found"
                    },
                    columnDefs: [
                        { orderable: false, targets: [] }
                    ]
                });
            });
        </script>
    @endpush

@endsection
