@extends('admin.layouts.master')

@section('title')
    Master Product List
@endsection

@section('breadcrumb')
    Master Product
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            <div class="flex justify-between items-center px-5 py-3 border-b">
                <h2 class="text-lg font-semibold">Master Product List</h2>

                <a href="{{ route('master-products.create') }}"
                    class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                    + Add Product
                </a>
            </div>

            <div class="overflow-x-auto p-5">
                <table class="w-full text-sm text-left" id="example">
                    <thead class="bg-gray-100">
                        <tr>
                            <th>Sl.No</th>
                            <th>Category Hierarchy</th>
                            <th>Product Name</th>
                            <th>Product Price</th>
                            <th>Selling Price</th>
                            <th>Commission</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>

    @push('scripts')
        <link href="{{ asset('admin_assets/datatables/dataTables.dataTables.css') }}" rel="stylesheet">
        <script src="{{ asset('admin_assets/datatables/dataTables.js') }}"></script>

        <script>
            $(function() {

                let table = $('#example').DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 10,

                    ajax: "{{ route('master-products.index') }}",

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
                            orderable: false
                        },
                        {
                            data: 8,
                            orderable: false,
                            searchable: false
                        }
                    ],

                    columnDefs: [{
                        targets: [6, 7, 8],
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
