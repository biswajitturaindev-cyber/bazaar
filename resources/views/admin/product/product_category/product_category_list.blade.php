@extends('admin.layouts.master')

@section('title')
    Product Category List
@endsection

@section('breadcrumb')
    Products
@endsection

@section('content')
    <!-- Row -->
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            <div class="flex justify-between items-center px-5 py-3 border-b">
                <h2 class="text-lg font-semibold">Product Category List</h2>

                <a href="{{ route('admin.product.category') }}"
                    class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                    + Add Category
                </a>
            </div>

            <div class="overflow-x-auto p-5">
                <table class="w-full text-sm text-left" id="example">
                    <thead class="bg-gray-100">
                        <tr class="border">
                            <th class="px-3 py-2">Sl.No</th>
                            <th class="px-3 py-2">Category Name</th>
                            <th class="px-3 py-2">Category Image</th>
                            <th class="px-3 py-2">Description</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>
    </div>



    @push('scripts')
        <link href="{{ asset('admin_assets/datatables/dataTables.dataTables.css') }}" type="text/css" rel="stylesheet">
        <script src="{{ asset('admin_assets/datatables/dataTables.js') }}" type="text/javascript"></script>
        <script src="{{ asset('admin_assets/js/script.js') }}"></script>

        <script>
        $(function () {

            let table = $('#example').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,

                ajax: "{{ route('admin.product.category.list') }}", // Update with your route

                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false
                    },
                    { data: 1 },
                    {
                        data: 2,
                        orderable: false,
                        searchable: false
                    },
                    { data: 3 },
                    {
                        data: 4,
                        orderable: false
                    },
                    {
                        data: 5,
                        orderable: false,
                        searchable: false
                    }
                ],

                columnDefs: [
                    {
                        targets: [2,4,5],
                        render: function (data) {
                            return data;
                        }
                    }
                ],

                drawCallback: function () {

                    let api = this.api();

                    api.column(0, {page:'current'}).nodes().each(function(cell, i){
                        cell.innerHTML = api.page.info().start + i + 1;
                    });

                }
            });

        });
        </script>
    @endpush
@endsection
