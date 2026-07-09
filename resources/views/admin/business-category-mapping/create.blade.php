@extends('admin.layouts.master')

@section('title')
    Add Business Category Mapping
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('breadcrumb')
    Business Category Mapping
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            {{-- Header --}}
            <div class="flex justify-between items-center p-5 border-b">
                <h2 class="text-lg font-semibold">Add Business Category Mapping</h2>

                <a href="{{ route('business-category-mapping.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Back
                </a>
            </div>

            {{-- Error (duplicate etc) --}}
            @if ($errors->has('error'))
                <div class="mx-5 mt-3 p-3 bg-red-100 text-red-700 rounded">
                    {{ $errors->first('error') }}
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('business-category-mapping.store') }}" method="POST">
                @csrf

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Business Category --}}
                    <div>
                        <label class="block mb-2 font-medium">Business Category</label>

                        <select name="business_category_id" id="business_category"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                            <option value="">-- Select Category --</option>

                            @foreach ($businessCategories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('business_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('business_category_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sub Category --}}
                    <div>
                        <label class="block mb-2 font-medium">Business Sub Category</label>

                        <select name="business_sub_category_id" id="sub_category"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                            <option value="">-- Select Sub Category --</option>
                        </select>

                        @error('business_sub_category_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Product Category --}}
                    <div>
                        <label class="block mb-2 font-medium">Product Category</label>

                        <select name="category_id" id="product_category"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                            <option value="">-- Select Product Category --</option>

                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('category_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block mb-2 font-medium">Status</label>

                        <select name="status" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                            <option value="1" selected>Active</option>
                            <option value="0" >Inactive</option>
                        </select>

                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Submit --}}
                <div class="p-5 border-t flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Save Mapping
                    </button>
                </div>

            </form>

        </div>
    </div>
@endsection
@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- AJAX --}}
    <script>
       $(document).ready(function () {

    // Initialize Select2
    $('#business_category, #sub_category, #product_category').select2({
        width: '100%',
        placeholder: 'Select',
        allowClear: true
    });

    $('#business_category').on('change', function () {

        let categoryId = $(this).val();

        // Reset subcategory
        $('#sub_category')
            .html('<option value="">-- Select Sub Category --</option>')
            .trigger('change');

        if (!categoryId) {
            return;
        }

        let url = `{{ route('get.subcategories', ':id') }}`.replace(':id', categoryId);

        fetch(url)
            .then(res => res.json())
            .then(data => {

                let options = '<option value="">-- Select Sub Category --</option>';

                data.forEach(function (item) {
                    options += `<option value="${item.id}">
                                    ${item.name}
                                </option>`;
                });

                $('#sub_category')
                    .html(options)
                    .val("{{ old('business_sub_category_id', $business->business_sub_category_id ?? '') }}")
                    .trigger('change'); // Refresh Select2
            });
    });

    // Auto load subcategories on edit page
    @if(old('business_category_id', $business->business_category_id ?? false))
        $('#business_category').trigger('change');
    @endif

});
    </script>
@endpush
