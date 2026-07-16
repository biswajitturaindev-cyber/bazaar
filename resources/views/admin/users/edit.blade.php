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

                <a href="{{ route('users.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
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
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block mb-2 font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200" readonly
                            style="blur">

                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Mobile --}}
                    <div>
                        <label class="block mb-2 font-medium">Mobile</label>
                        <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        @error('mobile')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label class="block mb-2 font-medium">Gender</label>
                        <select name="gender" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

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
                        <input type="date" name="dob" value="{{ old('dob', $user->dob) }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                        @error('dob')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block mb-2 font-medium">Status</label>
                        <select name="status" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

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

                        <select name="kyc_status" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

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

                    <div>
                        <label class="block mb-2 font-medium">Package</label>


                        <select name="package_id" class="w-full border rounded-lg px-3 py-2">
                            <option value="">Select Package</option>

                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}"
                                    {{ old('package_id', optional($user->latestSubscription)->package_id) == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }}
                                    ({{ $package->duration }} {{ ucfirst($package->duration_type) }})
                                    - ₹{{ $package->price }}
                                </option>
                            @endforeach
                        </select>
                        @error('package_id')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Admin Shop Status --}}
                    <div>
                        <label class="block mb-2 font-medium">Admin Shop Status</label>

                        <select name="admin_shop_status"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                            <option value="open"
                                {{ old('admin_shop_status', optional($user->business)->admin_shop_status) == 'open' ? 'selected' : '' }}>
                                Open
                            </option>

                            <option value="closed"
                                {{ old('admin_shop_status', optional($user->business)->admin_shop_status) == 'closed' ? 'selected' : '' }}>
                                Closed
                            </option>

                        </select>
                    </div>

                    {{-- Vendor Shop Status --}}
                    <div>
                        <label class="block mb-2 font-medium">Vendor Shop Status</label>

                        <input type="text" value="{{ ucfirst(optional($user->business)->shop_status ?? '-') }}"
                            class="w-full border rounded-lg px-3 py-2 bg-gray-100" readonly>
                    </div>

                    {{-- Working Days --}}
                    <div class="md:col-span-2">
                        <label class="block mb-2 font-medium">Working Days</label>

                        @php
                            $workingDays = optional($user->business)->working_days ?? [];
                            if (is_string($workingDays)) {
                                $workingDays = json_decode($workingDays, true) ?? [];
                            }

                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        @endphp

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach ($days as $day)
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" disabled {{ in_array($day, $workingDays) ? 'checked' : '' }}>
                                    <span>{{ ucfirst($day) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>


                    <div class="mt-4">
                        <label class="block mb-2 font-medium">Commission Settlement Type</label>

                        <select id="commission_settlement_type" name="commission_settlement_type"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                            <option value="">Select Type</option>
                            <option value="daily"
                                {{ old('commission_settlement_type', optional($user->business)->commission_settlement_type) == 'daily' ? 'selected' : '' }}>
                                Daily
                            </option>
                            <option value="weekly"
                                {{ old('commission_settlement_type', optional($user->business)->commission_settlement_type) == 'weekly' ? 'selected' : '' }}>
                                Weekly
                            </option>
                            <option value="monthly"
                                {{ old('commission_settlement_type', optional($user->business)->commission_settlement_type) == 'monthly' ? 'selected' : '' }}>
                                Monthly
                            </option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <label class="block mb-2 font-medium">Commission Settlement Day</label>

                        <select id="commission_settlement_day" name="commission_settlement_day"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                        </select>

                        @error('commission_settlement_day')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>



                </div>

                {{-- Submit --}}
                <div class="p-5 border-t flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Update User
                    </button>
                </div>

            </form>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const typeSelect = document.getElementById('commission_settlement_type');
            const daySelect = document.getElementById('commission_settlement_day');

            const selectedDay =
                "{{ old('commission_settlement_day', optional($user->business)->commission_settlement_day) }}";

            const weekDays = [{
                    value: 1,
                    text: 'Monday'
                },
                {
                    value: 2,
                    text: 'Tuesday'
                },
                {
                    value: 3,
                    text: 'Wednesday'
                },
                {
                    value: 4,
                    text: 'Thursday'
                },
                {
                    value: 5,
                    text: 'Friday'
                },
                {
                    value: 6,
                    text: 'Saturday'
                },
                {
                    value: 7,
                    text: 'Sunday'
                },
            ];

            function populateDays(type) {

                daySelect.innerHTML = '<option value="">Select Day</option>';
                daySelect.disabled = false;

                if (type === 'weekly') {

                    weekDays.forEach(day => {
                        const option = document.createElement('option');
                        option.value = day.value;
                        option.textContent = day.text;

                        if (parseInt(selectedDay) === day.value) {
                            option.selected = true;
                        }

                        daySelect.appendChild(option);
                    });

                } else if (type === 'biweekly') {

                    for (let i = 1; i <= 14; i++) {
                        const option = document.createElement('option');
                        option.value = i;
                        option.textContent = `Day ${i}`;

                        if (parseInt(selectedDay) === i) {
                            option.selected = true;
                        }

                        daySelect.appendChild(option);
                    }

                } else if (type === 'monthly') {

                    for (let i = 1; i <= 31; i++) {
                        const option = document.createElement('option');
                        option.value = i;
                        option.textContent = i;

                        if (parseInt(selectedDay) === i) {
                            option.selected = true;
                        }

                        daySelect.appendChild(option);
                    }

                } else if (type === 'daily') {

                    daySelect.innerHTML = '<option value="">Every Day</option>';
                    daySelect.disabled = true;
                }
            }

            populateDays(typeSelect.value);

            typeSelect.addEventListener('change', function() {
                populateDays(this.value);
            });

        });
    </script>
@endsection
