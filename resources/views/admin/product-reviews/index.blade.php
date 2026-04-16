@extends('admin.layouts.master')

@section('title')
    Product Review Master List
@endsection

@section('breadcrumb')
    Product Review Master List
@endsection

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
                        <th class="px-3 py-2">Category</th>
                        <th class="px-3 py-2">Sub Category</th>
                        <th class="px-3 py-2">Sub Sub Category</th>
                        <th class="px-3 py-2">Product Name</th>
                        <th class="px-3 py-2">HSN</th>
                        <th class="px-3 py-2">MRP</th>
                        <th class="px-3 py-2">Selling Price</th>
                        <th class="px-3 py-2">Description</th>
                        <th class="px-3 py-2">Image</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @foreach ($products as $product)
                        <tr class="border-l border-r">

                            {{-- Serial --}}
                            <td class="px-3 py-2"></td>

                            {{-- Category --}}
                            <td class="px-3 py-2">
                                {{ $product->category?->name ?? '-' }}
                            </td>

                            {{-- Sub Category --}}
                            <td class="px-3 py-2">
                                {{ $product->subCategory?->name ?? '-' }}
                            </td>

                            {{-- Sub Sub Category --}}
                            <td class="px-3 py-2">
                                {{ $product->subSubCategory?->name ?? '-' }}
                            </td>

                            {{-- Product Name --}}
                            <td class="px-3 py-2 font-medium">
                                {{ $product->name }}
                            </td>

                            {{-- HSN --}}
                            <td class="px-3 py-2">
                                {{ $product->hsn?->hsn_code ?? '-' }}
                            </td>

                            {{-- Prices --}}
                            <td class="px-3 py-2">
                                ₹{{ $product->mrp ?? '-' }}
                            </td>

                            <td class="px-3 py-2 text-green-600 font-semibold">
                                ₹{{ $product->selling_price ?? '-' }}
                            </td>

                            {{-- Description --}}
                            <td class="px-3 py-2">
                                {{ \Str::limit($product->description, 50) }}
                            </td>

                            {{-- Image (Fancybox Enabled) --}}
                            <td class="px-3 py-2">
                                @if ($product->image)
                                    <a href="{{ asset('storage/' . $product->image) }}" data-fancybox="products"
                                    data-caption="{{ $product->name }}">
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            width="50" height="50"
                                            class="rounded cursor-pointer border"
                                            style="object-fit:cover;">
                                    </a>
                                @else
                                    <span class="text-gray-400">No Image</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded
                                    {{ $product->status == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $product->status == 1 ? 'Approved' : 'Unapproved' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-3 py-2 flex gap-2">

                                <a href="{{ route('product-reviews.edit', $product->id) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                    Edit
                                </a>

                                <form action="{{ route('product-reviews.destroy', $product->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                        Delete
                                    </button>
                                </form>

                            </td>

                        </tr>
                    @endforeach
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
    document.addEventListener("DOMContentLoaded", function() {

        let table = $('#example').DataTable({
            paging: true,
            searching: true,
            info: true,
            pageLength: 10
        });

        // Serial number fix
        table.on('draw.dt', function () {
            let PageInfo = table.page.info();

            table.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });

            // Re-bind Fancybox after redraw
            Fancybox.bind("[data-fancybox='products']");
        });

    });
</script>
@endpush

@endsection
