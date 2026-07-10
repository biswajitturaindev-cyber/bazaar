@extends('admin.layouts.master')

@section('title')
    Business Category Mapping List
@endsection

@section('breadcrumb')
    Business Category Mapping
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center px-5 py-3 border-b">
            <h2 class="text-lg font-semibold">Business Category Mapping List</h2>

            <a href="{{ route('business-category-mapping.create') }}"
                class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                + Add Mapping
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto p-5">
            <table class="w-full text-sm text-left" id="example">
                <thead class="bg-gray-100">
                    <tr class="border">
                        <th class="px-3 py-2">Sl.No</th>
                        <th class="px-3 py-2">Business Category</th>
                        <th class="px-3 py-2">Sub Category</th>
                        <th class="px-3 py-2">Product Category</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @foreach ($mappings as $mapping)
                        <tr class="border-l border-r">

                            {{-- Serial --}}
                            <td class="px-3 py-2">
                                {{ ($mappings->currentPage() - 1) * $mappings->perPage() + $loop->iteration }}
                            </td>

                            {{-- Business Category --}}
                            <td class="px-3 py-2">
                                {{ $mapping->businessCategory?->name ?? '-' }}
                            </td>

                            {{-- Sub Category --}}
                            <td class="px-3 py-2">
                                {{ $mapping->businessSubCategory?->name ?? '-' }}
                            </td>

                            {{-- Product Category --}}
                            <td class="px-3 py-2">
                                {{ $mapping->category?->name ?? '-' }}
                            </td>

                            {{-- Status --}}
                            <td class="px-3 py-2">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded
                                    {{ $mapping->status == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $mapping->status == 1 ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-3 py-2 flex gap-2">

                                {{-- Edit --}}
                                <a href="{{ route('business-category-mapping.edit', $mapping->id) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                    Edit
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('business-category-mapping.destroy', $mapping->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                        Delete
                                    </button>
                                </form>

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $mappings->links() }}
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
