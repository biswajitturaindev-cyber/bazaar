@extends('admin.layouts.master')

@section('title')
    Banner List
@endsection

@section('breadcrumb')
    Banner
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            {{-- Header --}}
            <div class="flex justify-between items-center px-5 py-3 border-b">
                <h2 class="text-lg font-semibold">Banner List</h2>

                <a href="{{ route('banners.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                    + Add Banner
                </a>
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
                            <th>Title</th>
                            <th>Banner Type</th>
                            <th>Image</th>
                            <th>Sort Order</th>
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(function() {

                let table = $('#example').DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 10,

                    ajax: "{{ route('banners.index') }}",

                    columns: [{
                            data: null,
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 1 // Title
                        },
                        {
                            data: 2 // Banner Type
                        },
                        {
                            data: 3, // Image
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 4 // Sort Order
                        },
                        {
                            data: 5, // Status
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 6, // Action
                            orderable: false,
                            searchable: false
                        }
                    ],
                    columnDefs: [{
                        targets: [3, 5, 6],
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

            $(document).on('click', '.delete-btn', function() {

                let url = $(this).data('url');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {

                    if (result.isConfirmed) {

                        let form = $('<form>', {
                            method: 'POST',
                            action: url
                        });

                        form.append('@csrf');
                        form.append('<input type="hidden" name="_method" value="DELETE">');

                        $('body').append(form);
                        form.submit();
                    }
                });

            });
        </script>
    @endpush
@endsection
