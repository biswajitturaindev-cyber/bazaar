@extends('admin.layouts.master')

@section('title')
    Attribute Master List
@endsection

@section('breadcrumb')
    Attribute Master
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow rounded-xl">

            {{-- Header --}}
            <div class="flex justify-between items-center px-5 py-3 border-b">
                <h2 class="text-lg font-semibold">Attribute Master List</h2>

                <a href="{{ route('attribute-master.create') }}"
                    class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                    + Add Attribute Master
                </a>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto p-5">
                <table class="w-full text-sm text-left" id="example">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2">#</th>
                            <th class="px-3 py-2">Business Category</th>
                            <th class="px-3 py-2">Business Sub Category</th>
                            <th class="px-3 py-2">Name</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>
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
                    pageLength: 100,

                    ajax: "{{ route('attribute-master.index') }}",

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
                            data: 4,
                            orderable: false,
                            searchable: false
                        }
                    ],

                    columnDefs: [{
                        targets: [4],
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
