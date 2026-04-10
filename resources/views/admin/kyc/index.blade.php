@extends('admin.layouts.master')

@section('title')
    Users List
@endsection

@section('breadcrumb')
    Users
@endsection

@section('content')

<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center px-5 py-3 border-b">
            <h2 class="text-lg font-semibold">Users List</h2>

            <a href="{{ route('users.create') }}"
                class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                + Add User
            </a>
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

                            {{-- Helper --}}
                            @php
                                function kycStatus($status) {
                                    switch ($status) {
                                        case 1: return ['bg-green-100 text-green-700', 'Approved'];
                                        case 2: return ['bg-red-100 text-red-700', 'Rejected'];
                                        default: return ['bg-yellow-100 text-yellow-700', 'Pending'];
                                    }
                                }
                            @endphp

                            {{-- Owner Photo --}}
                            <td class="px-3 py-2">
                                <img src="{{ asset($kyc->owner_photo) }}" class="w-10 h-10 rounded border">
                                @php [$c,$t] = kycStatus($kyc->owner_photo_status); @endphp
                                <div class="mt-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $c }}">{{ $t }}</span>
                                </div>
                            </td>

                            {{-- Shop Photo --}}
                            <td class="px-3 py-2">
                                <img src="{{ asset($kyc->shop_photo) }}" class="w-10 h-10 rounded border">
                                @php [$c,$t] = kycStatus($kyc->shop_photo_status); @endphp
                                <div class="mt-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $c }}">{{ $t }}</span>
                                </div>
                            </td>

                            {{-- PAN --}}
                            <td class="px-3 py-2">
                                <a href="{{ asset($kyc->pan_card) }}" target="_blank" class="text-blue-600">View</a>
                                @php [$c,$t] = kycStatus($kyc->pan_card_status); @endphp
                                <div class="mt-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $c }}">{{ $t }}</span>
                                </div>
                            </td>

                            {{-- GST --}}
                            <td class="px-3 py-2">
                                @if($kyc->gst_certificate)
                                    <a href="{{ asset($kyc->gst_certificate) }}" target="_blank" class="text-blue-600">View</a>
                                @endif
                                @php [$c,$t] = kycStatus($kyc->gst_certificate_status); @endphp
                                <div class="mt-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $c }}">{{ $t }}</span>
                                </div>
                            </td>

                            {{-- Trade --}}
                            <td class="px-3 py-2">
                                @if($kyc->trade_license)
                                    <a href="{{ asset($kyc->trade_license) }}" target="_blank" class="text-blue-600">View</a>
                                @endif
                                @php [$c,$t] = kycStatus($kyc->trade_license_status); @endphp
                                <div class="mt-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $c }}">{{ $t }}</span>
                                </div>
                            </td>

                            {{-- FSSAI --}}
                            <td class="px-3 py-2">
                                @if($kyc->fssai_license)
                                    <a href="{{ asset($kyc->fssai_license) }}" target="_blank" class="text-blue-600">View</a>
                                @endif
                                @php [$c,$t] = kycStatus($kyc->fssai_license_status); @endphp
                                <div class="mt-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $c }}">{{ $t }}</span>
                                </div>
                            </td>

                            {{-- Address --}}
                            <td class="px-3 py-2">
                                @if($kyc->address_proof)
                                    <a href="{{ asset($kyc->address_proof) }}" target="_blank" class="text-blue-600">View</a>
                                @endif
                                @php [$c,$t] = kycStatus($kyc->address_proof_status); @endphp
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
