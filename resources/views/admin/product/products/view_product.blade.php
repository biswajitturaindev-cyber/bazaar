@extends('admin.layouts.master')

@section('title')
Product Details
@endsection

@section('breadcrumb')
Product
@endsection

@section('content')

<div class="bg-white shadow-xl rounded-xl">

    <!-- Header -->
    <div class="flex justify-between items-center border-b px-6 py-4">
        <h2 class="text-xl font-semibold text-gray-800">Product Details</h2>

        <div class="space-x-2">

            <a href="{{ route('admin.product.list') }}"
            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            Back
            </a>
        </div>
    </div>

    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-10">

        <!-- Product Images -->
        <div>

            @php
                $images = json_decode($product->image);
            @endphp

            <!-- Main Image -->
            <div class="border rounded-lg p-3 mb-4">
                @if($images && count($images)>0)
                <img id="mainImage"
                src="{{ asset('uploads/products/'.$images[0]) }}"
                class="w-full h-96 object-cover rounded-lg">
                @else
                <div class="h-96 flex items-center justify-center bg-gray-100">
                    No Image
                </div>
                @endif
            </div>

            <!-- Thumbnails -->
            <div class="flex gap-3">

                @if($images)

                @foreach($images as $img)

                <img src="{{ asset('uploads/products/'.$img) }}"
                class="w-20 h-20 object-cover rounded cursor-pointer border hover:border-blue-500"
                onclick="changeImage(this)">

                @endforeach

                @endif

            </div>

        </div>

        <!-- Product Information -->
        <div class="space-y-5">

            <div>
                <h3 class="text-2xl font-bold text-gray-800">
                    {{ $product->name }}
                </h3>
                <p class="text-gray-500 mt-1">HSN Code: {{ $product->sku }}</p>
            </div>

            <!-- Price -->
            <div class="text-3xl font-bold text-green-600">
                ₹ {{ number_format($product->price,2) }}
            </div>

            <!-- Status -->
            <div>
                @if($product->status == 1)
                <span class="px-3 py-1 text-sm font-semibold text-green-800 bg-green-100 rounded-full">
                    Active
                </span>
                @else
                <span class="px-3 py-1 text-sm font-semibold text-red-800 bg-red-100 rounded-full">
                    Inactive
                </span>
                @endif
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-4 pt-4 border-t">

                <div>
                    <p class="text-gray-500 text-sm">Category</p>
                    <p class="font-semibold">
                        {{ $product->category->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-sm">Subcategory</p>
                    <p class="font-semibold">
                        {{ $product->subcategory->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-sm">Stock</p>
                    <p class="font-semibold">{{ $product->stock }}</p>
                </div>

                <div>
                    <p class="text-gray-500 text-sm">PV</p>
                    <p class="font-semibold">{{ $product->prod_pv }}</p>
                </div>

                <div>
                    <p class="text-gray-500 text-sm">BV</p>
                    <p class="font-semibold">{{ $product->prod_bv }}</p>
                </div>

                <div>
                    <p class="text-gray-500 text-sm">Created</p>
                    <p class="font-semibold">
                        {{ $product->created_at->format('d M Y') }}
                    </p>
                </div>

            </div>

            <!-- Description -->
            <div class="pt-5 border-t">
                <h4 class="font-semibold mb-2">Description</h4>

                <div class="text-gray-600 leading-relaxed">
                    {!! $product->description !!}
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Image Change Script -->
<script>

function changeImage(element)
{
    document.getElementById("mainImage").src = element.src;
}

</script>

@endsection