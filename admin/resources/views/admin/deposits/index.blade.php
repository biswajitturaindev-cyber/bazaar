@extends('admin.layouts.master')

@section('title')
    Deposit List
@endsection

{{-- @section('breadcrumb')
    Deposits
@endsection --}}

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            {{-- Header --}}
            <div class="flex justify-between items-center px-5 py-3 border-b">
                <h2 class="text-lg font-semibold">Deposit List</h2>

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
                            <th class="px-3 py-2">Request Date</th>
                            <th class="px-3 py-2">Approved Date</th>
                            <th class="px-3 py-2">Username</th>
                            <th class="px-3 py-2">Name</th>
                            <th class="px-3 py-2">Amount</th>
                            <th class="px-3 py-2">Payment Method</th>
                            <th class="px-3 py-2">UTR / Ref ID</th>
                            <th class="px-3 py-2">Transaction ID</th>
                            <th class="px-3 py-2">Payment Proof</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($deposits as $deposit)
                            <tr class="border">

                                <td class="px-3 py-2">
                                    {{ ($deposits->currentPage() - 1) * $deposits->perPage() + $loop->iteration }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $deposit->created_at->format('d M Y, h:i A') }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $deposit->approved_at ? $deposit->approved_at->format('d M Y, h:i A') : '-' }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $deposit->business->user->vendor_id }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $deposit->business->user->name }}
                                </td>

                                <td class="px-3 py-2 font-semibold text-green-600">
                                    ₹ {{ number_format($deposit->amount, 2) }}
                                </td>

                                <td class="px-3 py-2">
                                    @switch($deposit->payment_method)
                                        @case(1)
                                            <span class="px-2 py-1 rounded bg-blue-100 text-blue-700">UPI</span>
                                        @break

                                        @case(2)
                                            <span class="px-2 py-1 rounded bg-green-100 text-green-700">Bank</span>
                                        @break

                                        @default
                                            <span class="px-2 py-1 rounded bg-gray-100 text-gray-700">Wallet</span>
                                    @endswitch
                                </td>

                                <td class="px-3 py-2">
                                    {{ $deposit->ref_id ?? '-' }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $deposit->transaction_id ?? '-' }}
                                </td>

                                <td class="px-3 py-2">
                                    @if ($deposit->payment_proof)
                                        <button type="button"
                                            onclick="showProof('{{ asset('storage/' . $deposit->payment_proof) }}')"
                                            class="bg-indigo-500 hover:bg-indigo-600 text-white text-xs px-3 py-1 rounded">
                                            View Proof
                                        </button>
                                    @else
                                        -
                                    @endif
                                </td>



                                <td class="px-3 py-2">
                                    @if ($deposit->status == 0)
                                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded">Pending</span>
                                    @elseif($deposit->status == 1)
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded">Approved</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded">Rejected</span>
                                    @endif
                                </td>

                                <td class="px-3 py-2">
                                    <a href="{{ route('deposits.edit', $deposit->id) }}"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </div>
    </div>

    <div id="proofModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">

        <div class="bg-white rounded-lg w-11/12 md:w-3/4 lg:w-1/2 p-4 relative">

            <button onclick="closeProof()" class="absolute right-3 top-3 text-2xl">
                &times;
            </button>

            <h3 class="font-semibold mb-3">Payment Proof</h3>

            <img id="proofImage" src="" class="w-full max-h-[80vh] object-contain">

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

            function showProof(image) {
                document.getElementById('proofImage').src = image;
                document.getElementById('proofModal').classList.remove('hidden');
                document.getElementById('proofModal').classList.add('flex');
            }

            function closeProof() {
                document.getElementById('proofModal').classList.add('hidden');
                document.getElementById('proofModal').classList.remove('flex');
            }
        </script>
    @endpush
@endsection
