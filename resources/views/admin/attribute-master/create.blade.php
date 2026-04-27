@extends('admin.layouts.master')

@section('title')
    Add Attribute Master
@endsection

@section('breadcrumb')
    Attribute Master
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Add Attribute Master</h2>

            <a href="{{ route('attribute-master.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back
            </a>
        </div>

        {{-- Form --}}
        <form action="{{ route('attribute-master.store') }}" method="POST">
            @csrf

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Business Category --}}
                <div>
                    <label class="block mb-2 font-medium">Category</label>

                    <select name="business_category_id" id="categorySelect"
                        class="w-full border rounded-lg px-3 py-2">

                        <option value="">-- Select Category --</option>

                        @foreach ($categories as $category)
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
                    <label class="block mb-2 font-medium">Sub Category</label>

                    <select name="business_sub_category_id" id="subCategorySelect"
                        class="w-full border rounded-lg px-3 py-2">

                        <option value="">-- Select Sub Category --</option>

                        @foreach ($subCategories as $sub)
                            <option value="{{ $sub->id }}"
                                data-category="{{ $sub->business_category_id }}"
                                {{ old('business_sub_category_id') == $sub->id ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('business_sub_category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div class="md:col-span-2">
                    <label class="block mb-2 font-medium">Attribute Master Name</label>

                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border rounded-lg px-3 py-2"
                        placeholder="Enter attribute name (e.g. Size, Color)">

                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Submit --}}
            <div class="p-5 border-t flex justify-end">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Save Attribute Master
                </button>
            </div>

        </form>

    </div>
</div>

{{-- JS (Dependent Dropdown) --}}
<script>
document.addEventListener("DOMContentLoaded", function() {

    let category = document.getElementById('categorySelect');
    let subCategory = document.getElementById('subCategorySelect');

    category.addEventListener('change', function() {
        let selected = this.value;

        Array.from(subCategory.options).forEach(option => {
            if (!option.value) return;

            if (option.getAttribute('data-category') == selected) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });

        subCategory.value = '';
    });

});
</script>

@endsection
