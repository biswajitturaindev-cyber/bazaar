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
                        <th class="px-3 py-2">Category</th>
                        <th class="px-3 py-2">Sub Category</th>
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($masters as $master)
                        <tr class="border">

                            {{-- Serial --}}
                            <td class="px-3 py-2">
                                {{ ($masters->currentPage() - 1) * $masters->perPage() + $loop->iteration }}
                            </td>

                            {{-- Category --}}
                            <td class="px-3 py-2">
                                {{ $master->category?->name ?? '-' }}
                            </td>

                            {{-- Sub Category --}}
                            <td class="px-3 py-2">
                                {{ $master->subCategory?->name ?? '-' }}
                            </td>

                            {{-- Name --}}
                            <td class="px-3 py-2 font-medium">
                                {{ $master->name }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-3 py-2 flex gap-2">

                                <a href="{{ route('attribute-master.edit', $master->id) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                    Edit
                                </a>

                                <form action="{{ route('attribute-master.destroy', $master->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                        Delete
                                    </button>
                                </form>

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">
                                No Attribute Master found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $masters->links() }}
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
