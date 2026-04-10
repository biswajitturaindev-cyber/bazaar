@extends('admin.layouts.master')

@section('title')
    Add HSN
@endsection

@section('breadcrumb')
    HSN
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Add HSN</h2>

            <a href="{{ route('hsns.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back
            </a>
        </div>

        {{-- Form --}}
        <form action="{{ route('hsns.store') }}" method="POST">
            @csrf

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- HSN Code --}}
                <div>
                    <label class="block mb-2 font-medium">HSN Code</label>

                    <input type="text"
                           name="hsn_code"
                           maxlength="20"
                           value="{{ old('hsn_code') }}"
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                           placeholder="Enter HSN Code (e.g. 7113)">

                    @error('hsn_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block mb-2 font-medium">Description</label>

                    <input type="text"
                           name="description"
                           value="{{ old('description') }}"
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                           placeholder="Enter Description">

                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CGST --}}
                <div>
                    <label class="block mb-2 font-medium">CGST (%)</label>

                    <input type="number" step="0.01"
                           name="cgst"
                           value="{{ old('cgst') }}"
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    @error('cgst')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SGST --}}
                <div>
                    <label class="block mb-2 font-medium">SGST (%)</label>

                    <input type="number" step="0.01"
                           name="sgst"
                           value="{{ old('sgst') }}"
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    @error('sgst')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- IGST --}}
                <div>
                    <label class="block mb-2 font-medium">IGST (%)</label>

                    <input type="number" step="0.01"
                           name="igst"
                           value="{{ old('igst') }}"
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    @error('igst')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block mb-2 font-medium">Status</label>

                    <select name="status"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

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
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Save HSN
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
