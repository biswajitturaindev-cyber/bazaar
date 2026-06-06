@extends('admin.layouts.master')

@section('title')
    Add Attribute Value
@endsection

@section('breadcrumb')
    Attribute Values
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            {{-- Header --}}
            <div class="flex justify-between items-center p-5 border-b">
                <h2 class="text-lg font-semibold">Add Attribute Value</h2>

                <a href="{{ route('attribute-values.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Back
                </a>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('attribute-values.store') }}" method="POST">
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
                        <label class="block mb-2 font-medium">Attribute Master</label>

                        <select id="attribute_master_id" name="attribute_master_id" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                            <option value="">Select Attribute Master</option>
                        </select>

                        @error('attribute_master_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Value --}}
                    <div>
                        <label class="block mb-2 font-medium">Value</label>

                        <input type="text" name="value" id="valueInput" value="{{ old('value') }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                            placeholder="Enter value (e.g. M, Red)">

                        @error('value')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Color Section --}}
                    <div id="colorSection" class="hidden">
                        <label class="block mb-2 font-medium">Color Picker</label>

                        <div class="flex gap-3 items-center">

                            {{-- Color Picker --}}
                            <input type="color" id="colorPicker" class="w-16 h-10 border rounded cursor-pointer">

                            {{-- HEX Input --}}
                            <input type="text" name="color_code" id="colorCode" maxlength="7"
                                value="{{ old('color_code', '#000000') }}"
                                class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                                placeholder="#000000">

                            {{-- Preview --}}
                            {{-- <div id="colorPreview" class="w-10 h-10 rounded border"></div> --}}

                        </div>

                        @error('color_code')
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
                        Save Attribute Value
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- JS --}}
    <script>
        $(document).ready(function () {

            $('#category_id').on('change', function () {

                let categoryId = $(this).val();

                $('#sub_category_id').html('<option value="">Loading...</option>');

                if (categoryId) {

                    $.ajax({
                        url: "{{ route('attributevalues.getSubCategories') }}",
                        type: "POST",
                        data: {
                            category_id: categoryId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {

                            let options =
                                '<option value="">Select Sub Category</option>';

                            $.each(response, function (key, value) {
                                options +=
                                    `<option value="${value.id}">${value.name}</option>`;
                            });

                            $('#sub_category_id').html(options);
                        }
                    });

                } else {
                    $('#sub_category_id').html(
                        '<option value="">Select Sub Category</option>'
                    );
                }

            });


            $('#sub_category_id').on('change', function () {

                let subCategoryId = $(this).val();
                let categoryId = $('#category_id').val();

                $('#attribute_master_id').html(
                    '<option value="">Loading...</option>'
                );

                $.ajax({
                    url: "{{ route('attributevalues.getAttributeMaster') }}",
                    type: "POST",
                    data: {
                        category_id: categoryId,
                        sub_category_id: subCategoryId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {

                        let options =
                            '<option value="">Select Attribute Master</option>';

                        $.each(response, function (key, value) {
                            options +=
                                `<option value="${value.id}">${value.name}</option>`;
                        });

                        $('#attribute_master_id').html(options);
                    }
                });

            });

        });





    document.addEventListener("DOMContentLoaded", function() {

        let attributeSelect = document.getElementById('attribute_master_id');
        let colorSection = document.getElementById('colorSection');

        let picker = document.getElementById('colorPicker');
        let code = document.getElementById('colorCode');
        let preview = document.getElementById('colorPreview');

        function toggleColorField() {

            if (!attributeSelect || attributeSelect.selectedIndex < 0) {
                colorSection.classList.add('hidden');
                return;
            }

            let selected = attributeSelect.options[attributeSelect.selectedIndex];

            // Try data-name first, fallback to option text
            let name = (
                selected.getAttribute('data-name') ||
                selected.textContent ||
                ''
            ).toLowerCase();

            if (name.includes('color')) {
                colorSection.classList.remove('hidden');
            } else {
                colorSection.classList.add('hidden');
            }
        }

        // Picker → Input
        if (picker && code) {
            picker.addEventListener('input', function() {
                code.value = picker.value;

                if (preview) {
                    preview.style.background = picker.value;
                }
            });
        }

        // Input → Picker
        if (code) {
            code.addEventListener('input', function() {

                let val = code.value;

                if (/^#([0-9A-F]{3}){1,2}$/i.test(val)) {

                    if (picker) {
                        picker.value = val;
                    }

                    if (preview) {
                        preview.style.background = val;
                    }
                }
            });
        }

        // Attribute Change
        if (attributeSelect) {
            attributeSelect.addEventListener('change', toggleColorField);
        }

        // Initial Load
        toggleColorField();

        if (preview && code) {
            preview.style.background = code.value || '#000000';
        }
    });
    </script>
@endsection
