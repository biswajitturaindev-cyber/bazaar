@extends('admin.layouts.master')

@section('title')
    Attribute List
@endsection

@section('breadcrumb')
    Attributes
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            {{-- Header --}}
            <div class="flex justify-between items-center px-5 py-3 border-b">
                <h2 class="text-lg font-semibold">Attribute List</h2>

                <a href="{{ route('attributes.create') }}"
                    class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                    + Add Attribute
                </a>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto p-5">
                <table class="w-full text-sm text-left" id="example">
                    <thead class="bg-gray-100">
                        <tr class="border">
                            <th class="px-3 py-2">Sl.No</th>
                            <th class="px-3 py-2">Attribute Name</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @foreach ($attributes as $attribute)
                            <tr class="border-l border-r">

                                {{-- Serial --}}
                                <td class="px-3 py-2">
                                    {{ ($attributes->currentPage() - 1) * $attributes->perPage() + $loop->iteration }}
                                </td>

                                {{-- Name --}}
                                <td class="px-3 py-2 font-medium">
                                    {{ $attribute->name ?? '-' }}
                                </td>


                                {{-- Status --}}
                                <td class="px-3 py-2">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded
                                    {{ $attribute->status == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $attribute->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-3 py-2 flex gap-2">

                                    {{-- Edit --}}
                                    <a href="{{ route('attributes.edit', $attribute->id) }}"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                        Edit
                                    </a>

                                    {{-- Delete --}}
                                    {{-- <form action="{{ route('attributes.destroy', $attribute->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this attribute?')">
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
                    {{ $attributes->links() }}
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
