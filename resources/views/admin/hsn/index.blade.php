@extends('admin.layouts.master')

@section('title')
    HSN List
@endsection

@section('breadcrumb')
    HSN
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center px-5 py-3 border-b">
            <h2 class="text-lg font-semibold">HSN List</h2>

            <a href="{{ route('hsns.create') }}"
                class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                + Add HSN
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
                        <th class="px-3 py-2">HSN Code</th>
                        <th class="px-3 py-2">Description</th>
                        <th class="px-3 py-2">CGST</th>
                        <th class="px-3 py-2">SGST</th>
                        <th class="px-3 py-2">IGST</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @foreach ($hsns as $hsn)
                        <tr class="border-l border-r">

                            {{-- Serial --}}
                            <td class="px-3 py-2">
                                {{ ($hsns->currentPage() - 1) * $hsns->perPage() + $loop->iteration }}
                            </td>

                            {{-- HSN Code --}}
                            <td class="px-3 py-2 font-medium">
                                {{ $hsn->hsn_code }}
                            </td>

                            {{-- Description --}}
                            <td class="px-3 py-2">
                                {{ $hsn->description ?? '-' }}
                            </td>

                            {{-- GST --}}
                            <td class="px-3 py-2">{{ $hsn->cgst }}%</td>
                            <td class="px-3 py-2">{{ $hsn->sgst }}%</td>
                            <td class="px-3 py-2">{{ $hsn->igst }}%</td>

                            {{-- Status --}}
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded
                                    {{ $hsn->status ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $hsn->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-3 py-2 flex gap-2">

                                {{-- Edit --}}
                                <a href="{{ route('hsns.edit', $hsn->id) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                    Edit
                                </a>

                                {{-- Delete --}}
                                {{-- <form action="{{ route('hsns.destroy', $hsn->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this HSN?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                        Delete
                                    </button>
                                </form> --}}

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $hsns->links() }}
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
