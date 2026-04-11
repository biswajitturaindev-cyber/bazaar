@extends('admin.layouts.master')

@section('title')
    Master Product List
@endsection

@section('breadcrumb')
    Master Product
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        <div class="flex justify-between items-center px-5 py-3 border-b">
            <h2 class="text-lg font-semibold">Master Product List</h2>

            <a href="{{ route('master-products.create') }}"
                class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                + Add Product
            </a>
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
                        <th>Product Price</th>
                        <th>Selling Price</th>
                        <th>Description</th>
                        <th class="px-3 py-2">Image</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @foreach ($products as $product)
                        <tr class="border-l border-r">

                            {{-- Serial --}}
                            <td class="px-3 py-2">
                                {{ $loop->iteration }}
                            </td>

                            {{-- Category --}}
                            <td class="px-3 py-2">
                                {{ $product->category->name ?? '-' }}
                            </td>

                            {{-- Sub Category --}}
                            <td class="px-3 py-2">
                                {{ $product->subCategory->name ?? '-' }}
                            </td>

                            {{-- Sub Sub Category --}}
                            <td class="px-3 py-2">
                                {{ $product->subSubCategory->name ?? '-' }}
                            </td>

                            {{-- Product Name --}}
                            <td class="px-3 py-2">
                                {{ $product->name }}
                            </td>

                            {{-- HSN --}}
                            <td class="px-3 py-2">
                                {{ $product->hsn->hsn_code ?? '-' }}
                            </td>

                            <td class="px-3 py-2">{{ $product->product_price }}</td>
                            <td class="px-3 py-2">{{ $product->selling_price }}</td>
                            <td class="px-3 py-2">{{ \Str::limit($product->description, 50) }}</td>


                            {{-- Image --}}
                            <td class="px-3 py-2">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                         width="50" height="50"
                                         class="rounded"
                                         style="object-fit:cover;">
                                @else
                                    <span class="text-gray-400">No Image</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded
                                    {{ $product->status == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $product->status == 1 ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-3 py-2 flex gap-2">

                                {{-- Edit --}}
                                <a href="{{ route('master-products.edit', $product->id) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                    Edit
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('master-products.destroy', $product->id) }}" method="POST"
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
<script src="{{ asset('admin_assets/datatables/dataTables.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('#example').DataTable({
            paging: true,
            searching: true,
            info: true,
            pagingType: "simple_numbers"
        });
    });
</script>
@endpush

@endsection
