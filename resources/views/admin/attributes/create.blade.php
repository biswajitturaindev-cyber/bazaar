@extends('admin.layouts.master')

@section('title')
    Add Attribute
@endsection

@section('breadcrumb')
    Attributes
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            {{-- Header --}}
            <div class="flex justify-between items-center p-5 border-b">
                <h2 class="text-lg font-semibold">Add Attribute</h2>

                <a href="{{ route('attributes.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Back
                </a>
            </div>

            {{-- Form --}}
            <form action="{{ route('attributes.store') }}" method="POST">
                @csrf

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Category --}}
                    <div>
                        <label class="block mb-2 font-medium">Category</label>

                        <select name="category_id" id="category_id" class="w-full border rounded-lg px-3 py-2">
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('category_id')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sub Category --}}
                    <div>
                        <label class="block mb-2 font-medium">Sub Category</label>

                        <select name="sub_category_id" id="sub_category_id" class="w-full border rounded-lg px-3 py-2">
                            <option value="">Select Sub Category</option>
                        </select>

                        @error('sub_category_id')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Attribute Master --}}
                    <div>
                        <label class="block mb-2 font-medium">
                            Attribute Master <span class="text-red-500">*</span>
                        </label>

                        <select name="attribute_master_id"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200 @error('attribute_master_id') border-red-500 @enderror"
                            required>

                            <option value="">Select Attribute Master</option>

                            @foreach ($attributeMasters as $attributeMaster)
                                <option value="{{ $attributeMaster->id }}"
                                    {{ old('attribute_master_id') == $attributeMaster->id ? 'selected' : '' }}>
                                    {{ $attributeMaster->name }}
                                </option>
                            @endforeach

                        </select>

                        @error('attribute_master_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block mb-2 font-medium">Type</label>

                        <select name="type" class="w-full border rounded-lg px-3 py-2">
                            <option value="text">Text</option>
                            <option value="color">Color</option>
                        </select>

                        @error('type')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Attribute Name --}}
                    <div>
                        <label class="block mb-2 font-medium">
                            Attribute Name <span class="text-red-500">*</span>
                        </label>

                        <input type="text" name="name"
                            value="{{ old('name', isset($attribute) ? $attribute->name : '') }}" maxlength="50" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200 @error('name') border-red-500 @enderror"
                            placeholder="Enter Attribute Name (e.g. Size, Color)">

                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Status --}}
                    <div>
                        <label class="block mb-2 font-medium">Status</label>

                        <select name="status" class="w-full border rounded-lg px-3 py-2">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>

                        @error('status')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Submit --}}
                <div class="p-5 border-t flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Save Attribute
                    </button>
                </div>

            </form>

        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {

            // Category → SubCategory
            $('#category_id').on('change', function() {
                let categoryId = $(this).val();

                $('#sub_category_id').html('<option>Loading...</option>');

                if (categoryId) {
                    $.ajax({
                        url: '/admin/product-get-subcategories/' + categoryId,
                        type: 'GET',
                        success: function(data) {
                            let options = '<option value="">Select Sub Category</option>';

                            $.each(data, function(key, value) {
                                options +=
                                    `<option value="${value.id}">${value.name}</option>`;
                            });

                            $('#sub_category_id').html(options);
                        }
                    });
                }
            });

        });
    </script>
@endpush
