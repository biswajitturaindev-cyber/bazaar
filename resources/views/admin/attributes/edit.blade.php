@extends('admin.layouts.master')

@section('title')
    Edit Attribute
@endsection

@section('breadcrumb')
    Edit Attribute
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Edit Attribute</h2>

            <a href="{{ route('attributes.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg">
                Back
            </a>
        </div>

        {{-- Form --}}
        <form action="{{ route('attributes.update', $attribute->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Category --}}
                <div>
                    <label class="block mb-2 font-medium">Category</label>
                    <select name="category_id" id="category_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('category_id', $attribute->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Sub Category --}}
                <div>
                    <label class="block mb-2 font-medium">Sub Category</label>
                    <select name="sub_category_id" id="sub_category_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select Sub Category</option>
                        @foreach($subCategories as $sub)
                            <option value="{{ $sub->id }}"
                                {{ old('sub_category_id', $attribute->sub_category_id) == $sub->id ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Attribute Master --}}
                <div>
                    <label class="block mb-2 font-medium">Attribute Master</label>
                    <select name="attribute_master_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select Attribute Master</option>
                        @foreach($attributeMasters as $master)
                            <option value="{{ $master->id }}"
                                {{ old('attribute_master_id', $attribute->attribute_master_id) == $master->id ? 'selected' : '' }}>
                                {{ $master->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Type --}}
                <div>
                    <label class="block mb-2 font-medium">Type</label>
                    <select name="type" class="w-full border rounded-lg px-3 py-2">
                        <option value="text" {{ old('type', $attribute->type) == 'text' ? 'selected' : '' }}>Text</option>
                        <option value="color" {{ old('type', $attribute->type) == 'color' ? 'selected' : '' }}>Color</option>
                    </select>
                </div>

                {{-- Attribute Name --}}
                <div>
                    <label class="block mb-2 font-medium">Attribute Name</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $attribute->name) }}"
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block mb-2 font-medium">Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2">
                        <option value="1" {{ old('status', $attribute->status) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $attribute->status) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

            </div>

            {{-- Submit --}}
            <div class="p-5 border-t flex justify-end">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg">
                    Update Attribute
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
