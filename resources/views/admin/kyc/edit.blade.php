@extends('admin.layouts.master')

@section('title')
    Edit User
@endsection

@section('breadcrumb')
    Edit User
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Edit User</h2>

            <a href="{{ route('users.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back
            </a>
        </div>

        {{-- Form --}}
        <form action="{{ route('kyc-details.update', $kyc->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Owner Photo --}}
                <div>
                    <label class="block mb-2 font-medium">Owner Photo</label>

                    <img src="{{ asset($kyc->owner_photo) }}" class="w-24 h-24 rounded border mb-2">

                    <select name="owner_photo_status"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        <option value="0" {{ old('owner_photo_status', $kyc->owner_photo_status) == 0 ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ old('owner_photo_status', $kyc->owner_photo_status) == 1 ? 'selected' : '' }}>Approved</option>
                        <option value="2" {{ old('owner_photo_status', $kyc->owner_photo_status) == 2 ? 'selected' : '' }}>Rejected</option>
                    </select>

                    @error('owner_photo_status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Shop Photo --}}
                <div>
                    <label class="block mb-2 font-medium">Shop Photo</label>

                    <img src="{{ asset($kyc->shop_photo) }}" class="w-24 h-24 rounded border mb-2">

                    <select name="shop_photo_status"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        <option value="0" {{ old('shop_photo_status', $kyc->shop_photo_status) == 0 ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ old('shop_photo_status', $kyc->shop_photo_status) == 1 ? 'selected' : '' }}>Approved</option>
                        <option value="2" {{ old('shop_photo_status', $kyc->shop_photo_status) == 2 ? 'selected' : '' }}>Rejected</option>
                    </select>

                    @error('shop_photo_status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- PAN Card --}}
                <div>
                    <label class="block mb-2 font-medium">PAN Card</label>

                    <a href="{{ asset($kyc->pan_card) }}" target="_blank" class="text-blue-600 underline mb-2 block">View File</a>

                    <select name="pan_card_status"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        <option value="0" {{ old('pan_card_status', $kyc->pan_card_status) == 0 ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ old('pan_card_status', $kyc->pan_card_status) == 1 ? 'selected' : '' }}>Approved</option>
                        <option value="2" {{ old('pan_card_status', $kyc->pan_card_status) == 2 ? 'selected' : '' }}>Rejected</option>
                    </select>

                    @error('pan_card_status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- GST --}}
                <div>
                    <label class="block mb-2 font-medium">GST Certificate</label>

                    @if($kyc->gst_certificate)
                        <a href="{{ asset($kyc->gst_certificate) }}" target="_blank" class="text-blue-600 underline mb-2 block">View File</a>
                    @endif

                    <select name="gst_certificate_status"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        <option value="0" {{ old('gst_certificate_status', $kyc->gst_certificate_status) == 0 ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ old('gst_certificate_status', $kyc->gst_certificate_status) == 1 ? 'selected' : '' }}>Approved</option>
                        <option value="2" {{ old('gst_certificate_status', $kyc->gst_certificate_status) == 2 ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                {{-- Trade License --}}
                <div>
                    <label class="block mb-2 font-medium">Trade License</label>

                    @if($kyc->trade_license)
                        <a href="{{ asset($kyc->trade_license) }}" target="_blank" class="text-blue-600 underline mb-2 block">View File</a>
                    @endif

                    <select name="trade_license_status"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        <option value="0">Pending</option>
                        <option value="1">Approved</option>
                        <option value="2">Rejected</option>
                    </select>
                </div>

                {{-- FSSAI --}}
                <div>
                    <label class="block mb-2 font-medium">FSSAI License</label>

                    @if($kyc->fssai_license)
                        <a href="{{ asset($kyc->fssai_license) }}" target="_blank" class="text-blue-600 underline mb-2 block">View File</a>
                    @endif

                    <select name="fssai_license_status"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        <option value="0">Pending</option>
                        <option value="1">Approved</option>
                        <option value="2">Rejected</option>
                    </select>
                </div>

                {{-- Address Proof --}}
                <div>
                    <label class="block mb-2 font-medium">Address Proof</label>

                    @if($kyc->address_proof)
                        <a href="{{ asset($kyc->address_proof) }}" target="_blank" class="text-blue-600 underline mb-2 block">View File</a>
                    @endif

                    <select name="address_proof_status"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        <option value="0">Pending</option>
                        <option value="1">Approved</option>
                        <option value="2">Rejected</option>
                    </select>
                </div>

            </div>

            {{-- Submit --}}
            <div class="p-5 border-t flex justify-end">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Update KYC
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
