@extends('admin.layouts.master')

@section('title')
    Edit Banner
@endsection

@section('breadcrumb')
    Edit Banner
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Edit Banner</h2>

            <a href="{{ route('banners.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back
            </a>
        </div>

        {{-- Form --}}
        <form action="{{ route('banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Banner Type --}}
                <div>
                    <label class="block mb-2 font-medium">Banner Type</label>

                    <select name="banner_type"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        <option value="">Select Banner Type</option>

                        <option value="promotional_banner"
                            {{ old('banner_type', $banner->banner_type) == 'promotional_banner' ? 'selected' : '' }}>
                            Promotional Banner
                        </option>

                        <option value="advertisement_banner"
                            {{ old('banner_type', $banner->banner_type) == 'advertisement_banner' ? 'selected' : '' }}>
                            Advertisement Banner
                        </option>

                    </select>

                    @error('banner_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Title --}}
                <div>
                    <label class="block mb-2 font-medium">Title</label>

                    <input type="text"
                        name="title"
                        value="{{ old('title', $banner->title) }}"
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
                        value="{{ old('sort_order', $banner->sort_order) }}"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    @error('sort_order')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Current Image --}}
                <div>
                    <label class="block mb-2 font-medium">Current Image</label>

                    @if ($banner->image)
                        <img src="{{ asset('storage/' . $banner->image) }}"
                            class="w-40 h-24 rounded border object-cover mb-3">
                    @else
                        <img src="{{ asset('images/no-image.png') }}"
                            class="w-40 h-24 rounded border object-cover mb-3">
                    @endif

                    <input type="file"
                        name="image"
                        accept="image/*"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    <small class="text-gray-500">Leave empty to keep the existing image.</small>

                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block mb-2 font-medium">Status</label>

                    <select name="status"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        <option value="1" {{ old('status', $banner->status) == 1 ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0" {{ old('status', $banner->status) == 0 ? 'selected' : '' }}>
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
                    Update Banner
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
