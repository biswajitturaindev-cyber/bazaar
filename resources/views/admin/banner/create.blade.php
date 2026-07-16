@extends('admin.layouts.master')

@section('title')
    Add Banner
@endsection

@section('breadcrumb')
    Banner
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Add Banner</h2>

            <a href="{{ route('banners.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back
            </a>
        </div>

        {{-- Form --}}
        <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Title --}}
                <div>
                    <label class="block mb-2 font-medium">Title</label>

                    <input type="text"
                        name="title"
                        value="{{ old('title') }}"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                        placeholder="Enter Banner Title">

                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sort Order --}}
                <div>
                    <label class="block mb-2 font-medium">Sort Order</label>

                    <input type="number"
                        name="sort_order"
                        min="1"
                        value="{{ old('sort_order', 1) }}"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    @error('sort_order')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Image --}}
                <div>
                    <label class="block mb-2 font-medium">Banner Image</label>

                    <input type="file"
                        name="image"
                        accept="image/*"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block mb-2 font-medium">Status</label>

                    <select name="status"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>
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
                    Save Banner
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
