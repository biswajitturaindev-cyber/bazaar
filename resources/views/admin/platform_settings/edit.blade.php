@extends('admin.layouts.master')

@section('title')
    Platform Settings
@endsection

@section('breadcrumb')
    Platform Settings
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            {{-- Header --}}
            <div class="flex justify-between items-center p-5 border-b">
                <h2 class="text-lg font-semibold">Platform Settings</h2>
            </div>

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mx-5 mt-5 p-4 mb-4 text-green-800 bg-green-100 border border-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="mx-5 mt-5 p-4 mb-4 text-red-800 bg-red-100 border border-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="mx-5 mt-5 p-4 mb-4 text-red-800 bg-red-100 border border-red-300 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('platform-settings.update') }}" method="POST">
                @csrf
                @method('PUT')


                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Platform Fee --}}
                    <div>
                        <label class="block mb-2 font-medium text-gray-700">
                            Platform Fee (₹)
                        </label>

                        <input type="number" name="platform_fee"
                            class="w-full border rounded-lg px-3 py-2 @error('platform_fee') border-red-500 @enderror"
                            step="0.01" min="0" value="{{ old('platform_fee', $setting->platform_fee) }}"
                            placeholder="Enter Platform Fee">

                        @error('platform_fee')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Settlement Fee --}}
                    <div>
                        <label class="block mb-2 font-medium text-gray-700">
                            Settlement Fee (₹)
                        </label>

                        <input type="number" name="settlement_fee"
                            class="w-full border rounded-lg px-3 py-2 @error('settlement_fee') border-red-500 @enderror"
                            step="0.01" min="0" value="{{ old('settlement_fee', $setting->settlement_fee) }}"
                            placeholder="Enter Settlement Fee">

                        @error('settlement_fee')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block mb-2 font-medium text-gray-700">
                            Status
                        </label>

                        <select name="status"
                            class="w-full border rounded-lg px-3 py-2 @error('status') border-red-500 @enderror">
                            <option value="1" {{ old('status', $setting->status) == 1 ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="0" {{ old('status', $setting->status) == 0 ? 'selected' : '' }}>
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
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Update Settings
                    </button>
                </div>

            </form>


            {{-- Update Business Fees --}}
            <div class="border-t p-5">

                <div class="flex justify-between items-center mb-5">
                    <div>
                        <h2 class="text-lg font-semibold">
                            Update Existing Businesses
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Select the businesses that should receive the latest Platform Fee and Settlement Fee.
                        </p>
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="select_all">
                        <span>Select All</span>
                    </label>
                </div>

                <form action="{{ route('platform-settings.update-businesses') }}" method="POST">
                    @csrf

                    <div class="overflow-x-auto">

                        <table class="min-w-full border border-gray-200">

                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-3 py-2 w-16 text-center">
                                        Select
                                    </th>
                                    <th class="border px-3 py-2 text-left">
                                        Business Name
                                    </th>
                                    <th class="border px-3 py-2 text-center">
                                        Current Platform Fee
                                    </th>
                                    <th class="border px-3 py-2 text-center">
                                        Current Settlement Fee
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($businesses as $business)
                                    <tr>
                                        <td class="border px-3 py-2 text-center">
                                            <input type="checkbox" class="business_checkbox" name="businesses[]"
                                                value="{{ $business->id }}">
                                        </td>

                                        <td class="border px-3 py-2">
                                            {{ $business->business_name }}
                                        </td>

                                        <td class="border px-3 py-2 text-center">
                                            ₹{{ number_format($business->platform_charge, 2) }}
                                        </td>

                                        <td class="border px-3 py-2 text-center">
                                            ₹{{ number_format($business->commission_settlement_fee, 2) }}
                                        </td>
                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            No businesses found.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    <div class="flex justify-end mt-5">
                        <button type="submit"
                            onclick="return confirm('Are you sure you want to update the selected businesses with the latest Platform Fee and Settlement Fee?')"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">

                            Update Selected Businesses

                        </button>
                    </div>

                </form>

            </div>




        </div>
    </div>

<script>
    $('#select_all').on('change', function () {
        $('.business_checkbox').prop('checked', $(this).is(':checked'));
    });
</script>



@endsection
