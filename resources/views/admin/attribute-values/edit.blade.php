@extends('admin.layouts.master')

@section('title')
    Edit Attribute Value
@endsection

@section('breadcrumb')
    Edit Attribute Value
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Edit Attribute Value</h2>

            <a href="{{ route('attribute-values.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back
            </a>
        </div>

        {{-- Form --}}
        <form action="{{ route('attribute-values.update', $value->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Attribute --}}
                <div>
                    <label class="block mb-2 font-medium">Attribute</label>

                    <select name="attribute_id" id="attributeSelect"
                        class="w-full border rounded-lg px-3 py-2">

                        <option value="">-- Select Attribute --</option>

                        @foreach ($attributes as $attribute)
                            <option value="{{ $attribute->id }}"
                                data-name="{{ strtolower($attribute->name) }}"
                                {{ old('attribute_id', $value->attribute_id) == $attribute->id ? 'selected' : '' }}>
                                {{ $attribute->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('attribute_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Value --}}
                <div>
                    <label class="block mb-2 font-medium">Value</label>

                    <input type="text"
                        name="value"
                        id="valueInput"
                        value="{{ old('value', $value->value) }}"
                        class="w-full border rounded-lg px-3 py-2"
                        placeholder="Enter value">

                    @error('value')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Color Section --}}
                <div id="colorSection" class="hidden">
                    <label class="block mb-2 font-medium">Color Picker</label>

                    <div class="flex gap-3 items-center">

                        {{-- Picker --}}
                        <input type="color"
                            id="colorPicker"
                            class="w-16 h-10 border rounded cursor-pointer">

                        {{-- HEX --}}
                        <input type="text"
                            name="color_code"
                            id="colorCode"
                            value="{{ old('color_code', $value->color_code ?? '#000000') }}"
                            class="w-full border rounded-lg px-3 py-2"
                            placeholder="#000000">

                        {{-- Preview --}}
                        {{-- <div id="colorPreview"
                            class="w-10 h-10 rounded border"></div> --}}

                    </div>

                    @error('color_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block mb-2 font-medium">Status</label>

                    <select name="status"
                        class="w-full border rounded-lg px-3 py-2">

                        <option value="1" {{ old('status', $value->status) == 1 ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0" {{ old('status', $value->status) == 0 ? 'selected' : '' }}>
                            Inactive
                        </option>

                    </select>

                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Submit --}}
            <div class="p-5 border-t flex justify-end">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Update Attribute Value
                </button>
            </div>

        </form>

    </div>
</div>

{{-- JS --}}
<script>
document.addEventListener("DOMContentLoaded", function () {

    let attributeSelect = document.getElementById('attributeSelect');
    let colorSection = document.getElementById('colorSection');

    let picker = document.getElementById('colorPicker');
    let code = document.getElementById('colorCode');
    let preview = document.getElementById('colorPreview');

    function toggleColorField() {
        let selected = attributeSelect.options[attributeSelect.selectedIndex];
        let name = selected.getAttribute('data-name');

        if (name && name.includes('color')) {
            colorSection.classList.remove('hidden');
        } else {
            colorSection.classList.add('hidden');
        }
    }

    // Picker → input
    picker.addEventListener('input', function () {
        code.value = picker.value;
        preview.style.background = picker.value;
    });

    // Input → picker
    code.addEventListener('input', function () {
        let val = code.value;

        if (/^#([0-9A-F]{3}){1,2}$/i.test(val)) {
            picker.value = val;
            preview.style.background = val;
        }
    });

    // Change attribute
    attributeSelect.addEventListener('change', toggleColorField);

    // Initial load
    toggleColorField();

    let initialColor = code.value || '#000000';
    picker.value = initialColor;
    preview.style.background = initialColor;
});
</script>

@endsection
