@extends('admin.layouts.master')

@section('title')
    Vendor Commission Payment Pending List
@endsection

@section('breadcrumb')
    Vendor Commission Payment Pending List
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            {{-- Header --}}
            <div class="flex justify-between items-center px-5 py-3 border-b">
                <h2 class="text-lg font-semibold">
                    Vendor Commission Payment Pending List
                </h2>

                {{-- Add button here if needed --}}
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

                <table id="example" class="table table-bordered">
                    <thead>
                        <tr>
                            {{-- <th>#</th> --}}
                            <th>Transaction No</th>
                            <th>Business</th>
                            <th>Commission</th>
                            <th>Settlement Amount</th>
                            <th>Payment Mode</th>
                            <th>Status</th>
                            <th>Date</th>
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        $(function () {

            $('#example').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,

                ajax: "{{ route('vendor-commissions.payment-pending-list') }}",

                columns: [
                        // {data: null, orderable: false, searchable: false},
                        {data: 0},
                        {data: 1},
                        {data: 2},
                        {data: 3},
                        {data: 4},
                        {data: 5}, // Status Dropdown
                        {data: 6}, // Date
                ],

                drawCallback: function () {

                    let api = this.api();

                    api.column(1, {page: 'current'}).nodes().each(function (cell, i) {
                        cell.innerHTML = api.page.info().start + i + 1;
                    });

                }
            });

        });

        $(document).on('change', '.change-status', function () {

            let select = $(this);
            let id = select.data('id');
            let status = select.val();
            let oldStatus = select.data('old-status');

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to update the status?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Update'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('vendor-commissions.update-status') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                            status: status
                        },
                        success: function (response) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            select.attr('data-old-status', status);
                        },
                        error: function () {

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong.'
                            });

                            select.val(oldStatus);
                        }
                    });

                } else {

                    select.val(oldStatus);

                }

            });

        });

        </script>

    @endpush
@endsection
