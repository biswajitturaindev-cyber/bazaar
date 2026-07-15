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
                        <tr>
                            <th>Sl.No</th>
                            <th>VendorID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>KYC</th>
                            <th>Admin Shop</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>

            </div>

        </div>
    </div>

    {{-- Scripts --}}
    @push('scripts')
        <link href="{{ asset('admin_assets/datatables/dataTables.dataTables.css') }}" rel="stylesheet">
        <script src="{{ asset('admin_assets/datatables/dataTables.js') }}"></script>

        <script>
            $(function() {

                let table = $('#example').DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 10,

                    ajax: "{{ route('users.index') }}",

                    columns: [{
                            data: null,
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 1
                        },
                        {
                            data: 2
                        },
                        {
                            data: 3
                        },
                        {
                            data: 4
                        },
                        {
                            data: 5
                        },
                        {
                            data: 6,
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 7,
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 8,
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 9,
                            orderable: false,
                            searchable: false
                        }
                    ],

                    columnDefs: [{
                        targets: [6, 7, 8, 9],
                        render: function(data) {
                            return data;
                        }
                    }],

                    drawCallback: function() {

                        let api = this.api();

                        api.column(0, {
                            page: 'current'
                        }).nodes().each(function(cell, i) {
                            cell.innerHTML = api.page.info().start + i + 1;
                        });

                    }

                });

            });
        </script>
    @endpush
@endsection
