@extends('admin.layouts.master')

@section('title')
    Add Product Sub Sub Category
@endsection

@section('breadcrumb')
    Category / Add Sub Sub Category
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            <div class="flex justify-between items-center p-5 border-b">
                <h2 class="text-lg font-semibold">Add Product Sub Sub Category</h2>

                <a href="{{ route('admin.product.sub.category.item.list') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    Back
                </a>
            </div>

            <div class="p-5">

                {{-- @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}

                <form action="{{ route('admin.product.sub.category.item.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Category --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Category</label>

                            <select name="category_id" id="category_id"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">

                                <option value="">Select Category</option>

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach

                            </select>

                            @error('category_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Sub Category --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Sub Category</label>

                            <select name="sub_category_id" id="sub_category_id"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">

                                <option value="">Select Sub Category</option>

                            </select>

                            @error('sub_category_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>


                        {{-- Sub Sub Category Name --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Sub Sub Category Name</label>

                            <input type="text" id="subCategoryItemName" name="name"
                                value="@error('name'){{ '' }}@else{{ old('name') }}@enderror"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter sub category name">

                            <p id="nameError" class="text-red-500 text-sm mt-1"></p>

                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-2 font-medium">Image</label>
                            <input type="file" name="image" id="image"
                                class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                            <p id="image_error" class="text-red-500 text-sm mt-1 hidden"></p>
                            <p id="image_success" class="text-green-600 text-sm mt-1 hidden"></p>
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">

                            <label class="block mb-2 text-sm font-medium">Description</label>

                            <textarea name="description" rows="4"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter description">
                                @error('description')
{{ '' }}@else{{ old('description') }}
@enderror
                            </textarea>

                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror

                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium">Status</label>
                            <select name="status"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1" {{ old('status', 1) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                    </div>

                    {{-- Submit Button --}}
                    <div class="mt-6">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Save Sub Sub Category
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $('#subCategoryItemName').on('keyup', function() {

                let name = $(this).val();

                if (name.length > 0) {

                    $.ajax({
                        url: "{{ route('admin.product.sub.category.item.check.name') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            name: name
                        },
                        success: function(response) {

                            if (response.exists) {
                                $('#nameError').text('Sub category already exists');
                            } else {
                                $('#nameError').text('');
                            }

                        }
                    });

                } else {
                    $('#nameError').text('');
                }

            });

            $('#category_id').change(function() {

                let category_id = $(this).val();

                $.ajax({
                    url: 'getsubcategorieslist/' + category_id,
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

            });

        });
    </script>
@endsection
