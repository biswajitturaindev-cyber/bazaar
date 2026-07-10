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

                {{-- <a href="{{ route('users.create') }}"
                class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                + Add User
            </a> --}}
            </div>

            {{-- Flash Messages --}}
            <div class="px-5 pt-3">
                @if (session('success'))
                    <div class="bg-green-100 text-green-700 px-3 py-2 rounded mb-2">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
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
                            <th class="px-3 py-2">VendorID</th>
                            <th class="px-3 py-2">Name</th>
                            <th class="px-3 py-2">Email</th>
                            <th class="px-3 py-2">Mobile</th>
                            <th class="px-3 py-2">Gender</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">KYC</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @foreach ($users as $user)
                            <tr class="border-l border-r">

                                {{-- Serial --}}
                                <td class="px-3 py-2">
                                    {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                </td>

                                {{-- Vendor ID --}}
                                <td class="px-3 py-2 font-medium">
                                    <a href=""> {{ $user->vendor_id }} </a>
                                </td>

                                {{-- Name --}}
                                <td class="px-3 py-2 font-medium">
                                    {{ $user->name }}
                                </td>

                                {{-- Email --}}
                                <td class="px-3 py-2">
                                    {{ $user->email }}
                                </td>

                                {{-- Mobile --}}
                                <td class="px-3 py-2">
                                    {{ $user->mobile }}
                                </td>

                                {{-- Gender --}}
                                <td class="px-3 py-2">
                                    {{ $user->gender == 1 ? 'Male' : ($user->gender == 2 ? 'Female' : ($user->gender == 3 ? 'Others' : '-')) }}
                                </td>

                                {{-- Status --}}
                                <td class="px-3 py-2">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded
                                    {{ $user->status ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $user->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                {{-- KYC --}}
                                <td class="px-3 py-2">
                                    @php
                                        switch ($user->kyc_status) {
                                            case 1:
                                                $class = 'bg-green-100 text-green-700';
                                                $label = 'Approved';
                                                break;
                                            case 2:
                                                $class = 'bg-blue-100 text-blue-700';
                                                $label = 'Pending';
                                                break;
                                            case 3:
                                                $class = 'bg-red-100 text-red-700';
                                                $label = 'Cancelled';
                                                break;
                                            default:
                                                $class = 'bg-gray-100 text-gray-700';
                                                $label = 'Not Submitted';
                                        }
                                    @endphp

                                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $class }}">
                                        {{ $label }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-3 py-2 flex gap-2">

                                    {{-- Edit --}}
                                    <a href="{{ route('users.edit', $user->id) }}"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                        Edit
                                    </a>

                                    {{-- View Products --}}
                                    @if ($user->business?->id)
                                        <a href="{{ route('vendors.products.index', $user->business->id) }}"
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded flex items-center gap-1" title="Show products">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 8h14l-1 12H6L5 8zm4-3a3 3 0 016 0" />
                                            </svg>

                                        </a>
                                    @endif

                                    {{-- Delete --}}
                                    {{-- Uncomment if needed --}}
                                    {{--
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this user?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                        Delete
                                    </button>
                                </form>
                                --}}

                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $users->links() }}
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
