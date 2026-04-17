@extends('admin.layouts.master')

@section('title')
    KYC List
@endsection

@section('breadcrumb')
    KYC
@endsection

@section('content')

<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center px-5 py-3 border-b">
            <h2 class="text-lg font-semibold">KYC List</h2>

            {{-- <a href="{{ route('users.create') }}"
                class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                + Add User
            </a> --}}
        </div>

        {{-- Flash Messages --}}
        <div class="px-5 pt-3">
            @if(session('success'))
                <div class="bg-green-100 text-green-700 px-3 py-2 rounded mb-2">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 text-red-700 px-3 py-2 rounded mb-2">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto p-5">
        @php
        $statusMap = [
            1 => ['bg-green-100 text-green-700', 'Approved'],
            2 => ['bg-red-100 text-red-700', 'Rejected'],
            0 => ['bg-yellow-100 text-yellow-700', 'Pending'],
        ];
        @endphp

        <table class="w-full text-sm text-left" id="example">
            <thead class="bg-gray-100">
                <tr class="border">
                    <th class="px-3 py-2">Sl.No</th>
                    <th class="px-3 py-2">Business ID</th>
                    <th class="px-3 py-2">Owner Photo</th>
                    <th class="px-3 py-2">Shop Photo</th>
                    <th class="px-3 py-2">PAN</th>
                    <th class="px-3 py-2">GST</th>
                    <th class="px-3 py-2">Trade</th>
                    <th class="px-3 py-2">FSSAI</th>
                    <th class="px-3 py-2">Address</th>
                    <th class="px-3 py-2">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach ($kycs as $kyc)
                    <tr class="border-l border-r">

                        {{-- Serial --}}
                        <td class="px-3 py-2">
                            {{ ($kycs->currentPage() - 1) * $kycs->perPage() + $loop->iteration }}
                        </td>

                        {{-- Business --}}
                        <td class="px-3 py-2 font-medium">
                            {{ $kyc->business_id }}
                        </td>

                        {{-- Owner Photo --}}
                        <td class="px-3 py-2">
                            <img src="{{ $kyc->owner_photo ? asset('storage/' . $kyc->owner_photo) : asset('images/no-image.png') }}"
                                class="w-10 h-10 rounded border object-cover">

                            @php [$c,$t] = $statusMap[$kyc->owner_photo_status] ?? $statusMap[0]; @endphp
                            <div class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $c }}">{{ $t }}</span>
                            </div>
                        </td>

                        {{-- Shop Photo --}}
                        <td class="px-3 py-2">
                            <img src="{{ $kyc->shop_photo ? asset('storage/' . $kyc->shop_photo) : asset('images/no-image.png') }}"
                                class="w-10 h-10 rounded border object-cover">

                            @php [$c,$t] = $statusMap[$kyc->shop_photo_status] ?? $statusMap[0]; @endphp
                            <div class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $c }}">{{ $t }}</span>
                            </div>
                        </td>

                        {{-- PAN --}}
                        <td class="px-3 py-2">

                            <img src="{{ $kyc->pan_card ? asset('storage/' . $kyc->pan_card) : asset('images/no-image.png') }}"
                                class="w-10 h-10 rounded border object-cover">

                            @php [$c,$t] = $statusMap[$kyc->pan_card_status] ?? $statusMap[0]; @endphp
                            <div class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $c }}">{{ $t }}</span>
                            </div>
                        </td>

                        {{-- GST --}}
                        <td class="px-3 py-2">
                            <img src="{{ $kyc->gst_certificate ? asset('storage/' . $kyc->gst_certificate) : asset('images/no-image.png') }}"
                                class="w-10 h-10 rounded border object-cover">

                            @php [$c,$t] = $statusMap[$kyc->gst_certificate_status] ?? $statusMap[0]; @endphp
                            <div class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $c }}">{{ $t }}</span>
                            </div>
                        </td>

                        {{-- Trade --}}
                        <td class="px-3 py-2">
                            <img src="{{ $kyc->trade_license ? asset('storage/' . $kyc->trade_license) : asset('images/no-image.png') }}"
                                class="w-10 h-10 rounded border object-cover">

                            @php [$c,$t] = $statusMap[$kyc->trade_license_status] ?? $statusMap[0]; @endphp
                            <div class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $c }}">{{ $t }}</span>
                            </div>
                        </td>

                        {{-- FSSAI --}}
                        <td class="px-3 py-2">
                            <img src="{{ $kyc->fssai_license ? asset('storage/' . $kyc->fssai_license) : asset('images/no-image.png') }}"
                                class="w-10 h-10 rounded border object-cover">

                            @php [$c,$t] = $statusMap[$kyc->fssai_license_status] ?? $statusMap[0]; @endphp
                            <div class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $c }}">{{ $t }}</span>
                            </div>
                        </td>

                        {{-- Address --}}
                        <td class="px-3 py-2">
                            <img src="{{ $kyc->address_proof ? asset('storage/' . $kyc->address_proof) : asset('images/no-image.png') }}"
                                class="w-10 h-10 rounded border object-cover">

                            @php [$c,$t] = $statusMap[$kyc->address_proof_status] ?? $statusMap[0]; @endphp
                            <div class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $c }}">{{ $t }}</span>
                            </div>
                        </td>

                        {{-- Action --}}
                        <td class="px-3 py-2 flex gap-2">
                            <a href="{{ route('kyc-details.edit', $kyc->id) }}"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                Edit
                            </a>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $kycs->links() }}
            </div>

        </div>

    </div>
</div>

{{-- Scripts --}}
@push('scripts')
<link href="{{ asset('admin_assets/datatables/dataTables.dataTables.css') }}" rel="stylesheet">
<script src="{{ asset('admin_assets/datatables/dataTables.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('#example').DataTable({
            paging: true,
            searching: true,
            info: true,
            pagingType: "simple_numbers"
        });
    });
</script>
@endpush
@endsection
