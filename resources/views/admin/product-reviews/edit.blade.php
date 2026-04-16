@extends('admin.layouts.master')

@section('title')
    Edit Product Review
@endsection

@section('breadcrumb')
    Edit Product Review
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow rounded-xl">

        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Edit Product Review</h2>

            <a href="{{ route('master-products.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg">
                Back
            </a>
        </div>

        <form action="{{ route('master-products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Business Category --}}
                <div>
                    <label>Business Category</label>
                    <select name="business_category_id" id="business_category_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select</option>
                        @foreach($businessCategories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ $product->business_category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Business Sub Category --}}
                <div>
                    <label>Business Sub Category</label>
                    <select name="business_sub_category_id" id="business_sub_category_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select</option>
                        @foreach($businessSubCategories as $sub)
                            <option value="{{ $sub->id }}"
                                {{ $product->business_sub_category_id == $sub->id ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Category --}}
                <div>
                    <label>Category</label>
                    <select name="category_id" id="category_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Sub Category --}}
                <div>
                    <label>Sub Category</label>
                    <select name="sub_category_id" id="sub_category_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select</option>
                        @foreach($subCategories as $sub)
                            <option value="{{ $sub->id }}"
                                {{ $product->sub_category_id == $sub->id ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Sub Sub Category --}}
                <div>
                    <label>Sub Sub Category</label>
                    <select name="sub_sub_category_id" id="sub_sub_category_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select</option>
                        @foreach($subSubCategories as $subSub)
                            <option value="{{ $subSub->id }}"
                                {{ $product->sub_sub_category_id == $subSub->id ? 'selected' : '' }}>
                                {{ $subSub->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Product Name --}}
                <div>
                    <label>Product Name</label>
                    <input type="text" name="name" value="{{ $product->name }}"
                        class="w-full border rounded-lg px-3 py-2">
                </div>

                {{-- MRP --}}
                <div>
                    <label>MRP</label>
                    <input type="number" step="0.01" name="mrp"
                        value="{{ $product->mrp }}"
                        class="w-full border rounded-lg px-3 py-2">
                </div>

                {{-- Selling Price --}}
                <div>
                    <label>Selling Price</label>
                    <input type="number" step="0.01" name="selling_price"
                        value="{{ $product->selling_price }}"
                        class="w-full border rounded-lg px-3 py-2">
                </div>

                {{-- HSN --}}
                <div>
                    <label>HSN</label>
                    <select name="hsn_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select</option>
                        @foreach($hsns as $hsn)
                            <option value="{{ $hsn->id }}"
                                {{ $product->hsn_id == $hsn->id ? 'selected' : '' }}>
                                {{ $hsn->hsn_code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Image --}}
                <div>
                    <label>Image</label>
                    <input type="file" name="image" class="w-full border rounded-lg px-3 py-2">

                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}"
                             class="mt-2 rounded"
                             style="width:80px;height:80px;">
                    @endif
                </div>

                {{-- Status --}}
                <div>
                    <label>Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2">
                        <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Approved</option>
                        <option value="2" {{ $product->status == 2 ? 'selected' : '' }}>Unapproved</option>
                    </select>
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label>Description</label>
                    <textarea name="description" class="w-full border rounded-lg px-3 py-2">{{ $product->description }}</textarea>
                </div>

            </div>

            <div class="p-5 border-t text-right">
                <button class="bg-blue-600 text-white px-6 py-2 rounded-lg">
                    Update Product
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
