@extends('admin.layouts.master')

@section('title')
    Edit Package
@endsection

@section('breadcrumb')
    Edit Package
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Edit Package</h2>

            <a href="{{ route('packages.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back
            </a>
        </div>

        {{-- Form --}}
        <form action="{{ route('packages.update', $package->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Package Name --}}
                <div>
                    <label class="block mb-2 font-medium">Package Name</label>

                    <input type="text"
                           name="name"
                           value="{{ old('name', $package->name) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stars --}}
                <div>
                    <label class="block mb-2 font-medium">Stars</label>

                    <input type="number"
                           name="stars"
                           min="0" max="5"
                           value="{{ old('stars', $package->stars) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    @error('stars')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Price --}}
                <div>
                    <label class="block mb-2 font-medium">Price (₹)</label>

                    <input type="number" step="0.01"
                           name="price"
                           value="{{ old('price', $package->price) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Product Limit --}}
                <div>
                    <label class="block mb-2 font-medium">Product Limit</label>

                    <input type="number"
                           name="product_limit"
                           value="{{ old('product_limit', $package->product_limit) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                           placeholder="Leave empty for unlimited">

                    @error('product_limit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block mb-2 font-medium">Status</label>

                    <select name="status"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        <option value="1" {{ old('status', $package->status) == 1 ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0" {{ old('status', $package->status) == 0 ? 'selected' : '' }}>
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
                    Update Package
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
