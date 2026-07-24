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


                    @php
                        $schedules = optional($user->business)->settlementSchedules ?? collect();
                    @endphp

                    <div class="md:col-span-2">
                        <label class="block mb-2 font-medium">Commission Settlement Schedule</label>

                        <div id="schedule-wrapper">

                            @forelse($schedules as $schedule)
                                <div class="schedule-row grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">

                                    <select name="settlement_type[]" class="settlement-type border rounded-lg px-3 py-2">

                                        <option value="">Select Type</option>

                                        <option value="daily" {{ $schedule->type == 'daily' ? 'selected' : '' }}>
                                            Daily
                                        </option>

                                        <option value="weekly" {{ $schedule->type == 'weekly' ? 'selected' : '' }}>
                                            Weekly
                                        </option>

                                        <option value="monthly" {{ $schedule->type == 'monthly' ? 'selected' : '' }}>
                                            Monthly
                                        </option>

                                    </select>

                                    <select name="settlement_day[]" class="settlement-day border rounded-lg px-3 py-2"
                                        data-selected="{{ $schedule->day }}">
                                    </select>

                                    <button type="button" class="remove-row bg-red-500 text-white rounded px-4">
                                        Remove
                                    </button>

                                </div>
                            @empty

                                <div class="schedule-row grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">

                                    <select name="settlement_type[]" class="settlement-type border rounded-lg px-3 py-2">

                                        <option value="">Select Type</option>
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>

                                    </select>

                                    <select name="settlement_day[]"
                                        class="settlement-day border rounded-lg px-3 py-2"></select>

                                    <button type="button" class="remove-row bg-red-500 text-white rounded px-4">
                                        Remove
                                    </button>

                                </div>
                            @endforelse

                        </div>

                        <button type="button" id="add-schedule" class="bg-blue-600 text-white px-4 py-2 rounded">
                            + Add Schedule
                        </button>
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
        const weekDays = [
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
        ];

        function fillDays(typeSelect, daySelect) {

            let selected = daySelect.dataset.selected ?? '';

            daySelect.innerHTML = '';

            if (typeSelect.value === 'daily') {

                daySelect.innerHTML = '<option value="">Every Day</option>';
                daySelect.disabled = true;

            } else if (typeSelect.value === 'weekly') {

                daySelect.disabled = false;

                weekDays.forEach((day, index) => {

                    daySelect.innerHTML +=
                        `<option value="${index+1}" ${selected==index+1?'selected':''}>${day}</option>`;

                });

            } else if (typeSelect.value === 'monthly') {

                daySelect.disabled = false;

                for (let i = 1; i <= 31; i++) {

                    daySelect.innerHTML +=
                        `<option value="${i}" ${selected==i?'selected':''}>${i}</option>`;

                }

            } else {

                daySelect.innerHTML = '<option value="">Select Day</option>';

            }
        }

        document.querySelectorAll('.schedule-row').forEach(row => {

            fillDays(
                row.querySelector('.settlement-type'),
                row.querySelector('.settlement-day')
            );

        });

        document.addEventListener('change', function(e) {

            if (e.target.classList.contains('settlement-type')) {

                fillDays(
                    e.target,
                    e.target.closest('.schedule-row').querySelector('.settlement-day')
                );

            }

        });

        document.getElementById('add-schedule').addEventListener('click', function() {

            let row = document.querySelector('.schedule-row').cloneNode(true);

            row.querySelector('.settlement-type').value = '';
            row.querySelector('.settlement-day').innerHTML = '';

            document.getElementById('schedule-wrapper').appendChild(row);

        });

        document.addEventListener('click', function(e) {

            if (e.target.classList.contains('remove-row')) {

                if (document.querySelectorAll('.schedule-row').length > 1) {

                    e.target.closest('.schedule-row').remove();

                }

            }

        });
    </script>
@endsection
