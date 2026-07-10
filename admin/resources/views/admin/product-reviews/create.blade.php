@extends('admin.layouts.master')

@section('title')
    Add Master Product
@endsection

@section('breadcrumb')
    Master Product
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Add Master Product</h2>

            <a href="{{ route('master-products.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back
            </a>
        </div>

        <form action="{{ route('master-products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Category -->
                <div>
                    <label class="block mb-2 font-medium">Category</label>
                    <select name="category_id" id="category_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sub Category -->
                <div>
                    <label class="block mb-2 font-medium">Sub Category</label>
                    <select name="sub_category_id" id="sub_category_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select Sub Category</option>
                    </select>

                    @error('sub_category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sub Sub Category -->
                <div>
                    <label class="block mb-2 font-medium">Sub Sub Category</label>
                    <select name="sub_sub_category_id" id="sub_sub_category_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select Sub Sub Category</option>
                    </select>

                    @error('sub_sub_category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Name -->
                <div>
                    <label class="block mb-2 font-medium">Product Name</label>
                    <input type="text" name="name" maxlength="100" value="{{ old('name') }}"
                        class="w-full border rounded-lg px-3 py-2"
                        placeholder="Enter Product Name">

                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Product Price -->
                <div>
                    <label class="block mb-2 font-medium">Product Price</label>
                    <input type="number" step="0.01" name="product_price" value="{{ old('product_price') }}"
                        class="w-full border rounded-lg px-3 py-2"
                        placeholder="Enter Product Price">

                    @error('product_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Selling Price -->
                <div>
                    <label class="block mb-2 font-medium">Selling Price</label>
                    <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price') }}"
                        class="w-full border rounded-lg px-3 py-2"
                        placeholder="Enter Selling Price">

                    @error('selling_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block mb-2 font-medium">Description</label>
                    <textarea name="description" rows="4"
                        class="w-full border rounded-lg px-3 py-2"
                        placeholder="Enter Description">{{ old('description') }}</textarea>

                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


                <!-- HSN -->
                <div>
                    <label class="block mb-2 font-medium">HSN</label>
                    <select name="hsn_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select HSN</option>
                        @foreach($hsns as $hsn)
                            <option value="{{ $hsn->id }}" {{ old('hsn_id') == $hsn->id ? 'selected' : '' }}>
                                {{ $hsn->hsn_code }}
                            </option>
                        @endforeach
                    </select>

                    @error('hsn_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image -->
                <div>
                    <label class="block mb-2 font-medium">Image</label>
                    <input type="file" name="image"
                        class="w-full border rounded-lg px-3 py-2">

                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block mb-2 font-medium">Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2">
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>

                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Submit -->
            <div class="p-5 border-t flex justify-end">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Save Product
                </button>
            </div>

        </form>

    </div>
</div>


@endsection
@push('scripts')
<script>
$(document).ready(function () {

    // Category → SubCategory
    $('#category_id').on('change', function () {
        let categoryId = $(this).val();

        $('#sub_category_id').html('<option>Loading...</option>');
        $('#sub_sub_category_id').html('<option>Select Sub Sub Category</option>');

        if (categoryId) {
            $.ajax({
                url: '/admin/product-get-subcategories/' + categoryId,
                type: 'GET',
                success: function (data) {
                    let options = '<option value="">Select Sub Category</option>';

                    $.each(data, function (key, value) {
                        options += `<option value="${value.id}">${value.name}</option>`;
                    });

                    $('#sub_category_id').html(options);
                }
            });
        }
    });

    // SubCategory → SubSubCategory
    $('#sub_category_id').on('change', function () {
        let subCategoryId = $(this).val();

        $('#sub_sub_category_id').html('<option>Loading...</option>');

        if (subCategoryId) {
            $.ajax({
                url: '/admin/product-get-sub-subcategories/' + subCategoryId,
                type: 'GET',
                success: function (data) {
                    let options = '<option value="">Select Sub Sub Category</option>';

                    $.each(data, function (key, value) {
                        options += `<option value="${value.id}">${value.name}</option>`;
                    });

                    $('#sub_sub_category_id').html(options);
                }
            });
        }
    });

});
</script>
@endpush
