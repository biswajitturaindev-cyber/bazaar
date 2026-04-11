@extends('admin.layouts.master')

@section('title')
    Edit Master Product
@endsection

@section('breadcrumb')
    Edit Master Product
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow rounded-xl">

        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Edit Master Product</h2>

            <a href="{{ route('master-products.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg">
                Back
            </a>
        </div>

        <form action="{{ route('master-products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Category -->
                <div>
                    <label>Category</label>
                    <select name="category_id" id="category_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sub Category -->
                <div>
                    <label>Sub Category</label>
                    <select name="sub_category_id" id="sub_category_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select Sub Category</option>
                        @foreach($subCategories->where('category_id', $product->category_id) as $sub)
                            <option value="{{ $sub->id }}"
                                {{ $product->sub_category_id == $sub->id ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sub Sub Category -->
                <div>
                    <label>Sub Sub Category</label>
                    <select name="sub_sub_category_id" id="sub_sub_category_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select Sub Sub Category</option>
                        @foreach($subSubCategories->where('sub_category_id', $product->sub_category_id) as $subSub)
                            <option value="{{ $subSub->id }}"
                                {{ $product->sub_sub_category_id == $subSub->id ? 'selected' : '' }}>
                                {{ $subSub->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Product Name -->
                <div>
                    <label>Product Name</label>
                    <input type="text" name="name" value="{{ $product->name }}"
                        class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Product Price -->
                <div>
                    <label>Product Price</label>
                    <input type="number" step="0.01" name="product_price"
                        value="{{ $product->product_price }}"
                        class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Selling Price -->
                <div>
                    <label>Selling Price</label>
                    <input type="number" step="0.01" name="selling_price"
                        value="{{ $product->selling_price }}"
                        class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- HSN -->
                <div>
                    <label>HSN</label>
                    <select name="hsn_id" class="w-full border rounded-lg px-3 py-2">
                        @foreach($hsns as $hsn)
                            <option value="{{ $hsn->id }}"
                                {{ $product->hsn_id == $hsn->id ? 'selected' : '' }}>
                                {{ $hsn->hsn_code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Image -->
                <div>
                    <label>Image</label>
                    <input type="file" name="image" class="w-full border rounded-lg px-3 py-2">

                    <img src="{{ asset('storage/' . $product->image) }}"
                        style="width:80px;height:80px;margin-top:10px;">
                </div>

                <!-- Status -->
                <div>
                    <label>Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2">
                        <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Description -->
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

@push('scripts')
<script>
$(document).ready(function () {

    // Category change
    $('#category_id').change(function () {
        let id = $(this).val();

        $('#sub_category_id').html('<option>Loading...</option>');
        $('#sub_sub_category_id').html('<option>Select Sub Sub Category</option>');

        $.get('/admin/product-get-subcategories/' + id, function (data) {
            let options = '<option value="">Select Sub Category</option>';

            data.forEach(function (item) {
                options += `<option value="${item.id}">${item.name}</option>`;
            });

            $('#sub_category_id').html(options);
        });
    });

    // Subcategory change
    $('#sub_category_id').change(function () {
        let id = $(this).val();

        $('#sub_sub_category_id').html('<option>Loading...</option>');

        $.get('/admin/product-get-sub-subcategories/' + id, function (data) {
            let options = '<option value="">Select Sub Sub Category</option>';

            data.forEach(function (item) {
                options += `<option value="${item.id}">${item.name}</option>`;
            });

            $('#sub_sub_category_id').html(options);
        });
    });

});
</script>
@endpush
