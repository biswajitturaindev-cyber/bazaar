@extends('admin.layouts.master')

@section('title')
Product List
@endsection

@section('breadcrumb')
Products
@endsection

@section('content')

<!-- Row -->
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        <div class="flex justify-between items-center px-5 py-3 border-b">
            <h2 class="text-lg font-semibold">Product List</h2>

            <a href="{{route('admin.product.view')}}"
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
                        <th class="px-3 py-2">Product</th>
                        <th class="px-3 py-2">Image</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @foreach($products as $key => $product)

                    <tr class="border-l border-r">
                        <td class="px-3 py-2">{{ $key+1 }}</td>
                        <td class="px-3 py-2">
                            {{ $product->category->name ?? '-' }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $product->subcategory->name ?? '-' }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $product->name }}
                        </td>
                       <td class="px-3 py-2">
    @php
        $images = json_decode($product->image);
    @endphp

    @if($images && count($images) > 0)
        @foreach($images as $img)
            <img src="{{ asset('uploads/products/'.$img) }}"
                 class="w-10 inline-block cursor-pointer rounded"
                 onclick="showImage('{{ asset('uploads/products/'.$img) }}')"
                 onerror="this.onerror=null;this.src='{{ asset('uploads/products/no-images.png') }}';">
        @endforeach
    @else
        <img src="{{ asset('uploads/products/no-images.png') }}"
             class="w-10 inline-block rounded">
    @endif
</td>

                        <td class="px-3 py-2">
                            @if($product->status == 1)
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                    Active
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <td class="px-3 py-2 space-x-2">
                            <!--<a href="#" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">View</a>-->
                            <a href="{{ route('products.view', encrypt($product->id)) }}" 
                               class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                               View
                            </a>
                            <a href="{{ route('admin.product.edit', encrypt($product->id)) }}" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Edit</a>
                            <form action="{{ route('admin.product.delete', encrypt($product->id)) }}" 
                                  method="POST" 
                                  style="display:inline-block;"
                                  onsubmit="return confirm('Are you sure you want to delete this product?')">
                                @csrf
                                @method('DELETE')
                            
                                <button type="submit" 
                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
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
<div id="imageModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50">

    <button onclick="closeModal()" class="absolute top-5 right-8 text-white text-4xl font-bold">
        &times;
    </button>

    <img id="modalImage" class="max-w-[80%] max-h-[80%] rounded-lg shadow-lg">

</div>


@push('scripts')
<link href="{{ asset('admin_assets/datatables/dataTables.dataTables.css') }}" type="text/css" rel="stylesheet">
<script src="{{ asset('admin_assets/datatables/dataTables.js') }}" type="text/javascript"></script>
<script src="{{ asset('admin_assets/js/script.js') }}"></script>
@if(session('success'))
<div id="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 flex justify-between items-center">
    
    <span>{{ session('success') }}</span>

    <button onclick="document.getElementById('successMessage').remove()" 
        class="text-green-700 font-bold text-lg leading-none">
        &times;
    </button>

</div>
@endif
<script>
    document.addEventListener("DOMContentLoaded", function () {
        $('#example').DataTable({
            paging: true,
            searching: true,
            info: true,
            pagingType: "simple_numbers"
        });
    });

    function showImage(src) {
        const modal = document.getElementById("imageModal");
        const modalImage = document.getElementById("modalImage");

        modalImage.src = src;
        modal.classList.remove("hidden");
        modal.classList.add("flex");
    }

    function closeModal() {
        const modal = document.getElementById("imageModal");

        modal.classList.remove("flex");
        modal.classList.add("hidden");
    }
</script>

@endpush
@endsection