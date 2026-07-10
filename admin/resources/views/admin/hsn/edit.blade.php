@extends('admin.layouts.master')

@section('title')
    Edit HSN
@endsection

@section('breadcrumb')
    Edit HSN
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Edit HSN</h2>

            <a href="{{ route('hsns.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back
            </a>
        </div>

        {{-- Form --}}
        <form action="{{ route('hsns.update', $hsn->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- HSN Code --}}
                <div>
                    <label class="block mb-2 font-medium">HSN Code</label>

                    <input type="text"
                           name="hsn_code"
                           maxlength="20"
                           value="{{ old('hsn_code', $hsn->hsn_code) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    @error('hsn_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block mb-2 font-medium">Description</label>

                    <input type="text"
                           name="description"
                           value="{{ old('description', $hsn->description) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CGST --}}
                <div>
                    <label class="block mb-2 font-medium">CGST (%)</label>

                    <input type="number" step="0.01"
                           name="cgst"
                           value="{{ old('cgst', $hsn->cgst) }}"
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
                           value="{{ old('sgst', $hsn->sgst) }}"
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
                           value="{{ old('igst', $hsn->igst) }}"
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

                        <option value="1" {{ old('status', $hsn->status) == 1 ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0" {{ old('status', $hsn->status) == 0 ? 'selected' : '' }}>
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
                    Update HSN
                </button>
            </div>

        </form>

    </div>
</div>

{{-- Auto IGST Script --}}
<script>
    document.querySelector('[name="cgst"]').addEventListener('input', calcIgst);
    document.querySelector('[name="sgst"]').addEventListener('input', calcIgst);

    function calcIgst() {
        let cgst = parseFloat(document.querySelector('[name="cgst"]').value) || 0;
        let sgst = parseFloat(document.querySelector('[name="sgst"]').value) || 0;

        document.querySelector('[name="igst"]').value = cgst + sgst;
    }
</script>

@endsection
