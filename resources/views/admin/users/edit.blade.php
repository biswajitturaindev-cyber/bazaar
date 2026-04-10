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
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Name --}}
                <div>
                    <label class="block mb-2 font-medium">Name</label>
                    <input type="text" name="name"
                        value="{{ old('name', $user->name) }}"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block mb-2 font-medium">Email</label>
                    <input type="email" name="email"
                        value="{{ old('email', $user->email) }}"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200" readonly style="blur">

                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mobile --}}
                <div>
                    <label class="block mb-2 font-medium">Mobile</label>
                    <input type="text" name="mobile"
                        value="{{ old('mobile', $user->mobile) }}"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    @error('mobile')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block mb-2 font-medium">Gender</label>
                    <select name="gender"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        <option value="">Select Gender</option>

                        <option value="1" {{ old('gender', $user->gender) == 1 ? 'selected' : '' }}>
                            Male
                        </option>

                        <option value="2" {{ old('gender', $user->gender) == 2 ? 'selected' : '' }}>
                            Female
                        </option>

                        <option value="3" {{ old('gender', $user->gender) == 3 ? 'selected' : '' }}>
                            Others
                        </option>

                    </select>

                    @error('gender')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DOB --}}
                <div>
                    <label class="block mb-2 font-medium">Date of Birth</label>
                    <input type="date" name="dob"
                        value="{{ old('dob', $user->dob) }}"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    @error('dob')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block mb-2 font-medium">Status</label>
                    <select name="status"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>
                            Inactive
                        </option>

                    </select>

                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- KYC Status --}}
                <div>
                    <label class="block mb-2 font-medium">KYC Status</label>

                    <select name="kyc_status"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        <option value="0" {{ old('kyc_status', $user->kyc_status) == 0 ? 'selected' : '' }}>
                            Not Submitted
                        </option>

                        <option value="2" {{ old('kyc_status', $user->kyc_status) == 2 ? 'selected' : '' }}>
                            Pending
                        </option>

                        <option value="1" {{ old('kyc_status', $user->kyc_status) == 1 ? 'selected' : '' }}>
                            Approved
                        </option>

                        <option value="3" {{ old('kyc_status', $user->kyc_status) == 3 ? 'selected' : '' }}>
                            Cancelled
                        </option>

                    </select>

                    @error('kyc_status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Submit --}}
            <div class="p-5 border-t flex justify-end">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Update User
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
