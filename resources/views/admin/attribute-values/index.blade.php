@extends('admin.layouts.master')

@section('title')
    Attribute Values List
@endsection

@section('breadcrumb')
    Attribute Values
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            {{-- Header --}}
            <div class="flex justify-between items-center px-5 py-3 border-b">
                <h2 class="text-lg font-semibold">Attribute Values List</h2>

                <a href="{{ route('attribute-values.create') }}"
                    class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                    + Add Attribute Value
                </a>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto p-5">
                <table class="w-full text-sm text-left" id="example">
                    <thead class="bg-gray-100">
                        <tr class="border">
                            <th class="px-3 py-2">Sl.No</th>
                            <th class="px-3 py-2">Attribute</th>
                            <th class="px-3 py-2">Value</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @foreach ($values as $value)
                            <tr class="border-l border-r">

                                {{-- Serial --}}
                                <td class="px-3 py-2">
                                    {{ ($values->currentPage() - 1) * $values->perPage() + $loop->iteration }}
                                </td>

                                {{-- Attribute --}}
                                <td class="px-3 py-2">
                                    {{ $value->attribute?->name ?? '-' }}
                                </td>

                                {{-- Value + Color --}}
                                <td class="px-3 py-2 font-medium">
                                    <div class="flex items-center gap-2">

                                        {{-- Color Preview --}}
                                        @if ($value->color_code)
                                            <span class="w-5 h-5 rounded border"
                                                style="background-color: {{ $value->color_code }}">
                                            </span>
                                        @endif

                                        {{-- Text --}}
                                        <span>{{ $value->value ?? '-' }}</span>

                                        {{-- HEX --}}
                                        @if ($value->color_code)
                                            <span class="text-xs text-gray-500">
                                                ({{ $value->color_code }})
                                            </span>
                                        @endif

                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-3 py-2">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded
                                        {{ $value->status == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $value->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-3 py-2 flex gap-2">

                                    {{-- Edit --}}
                                    <a href="{{ route('attribute-values.edit', $value->id) }}"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                        Edit
                                    </a>

                                    {{-- Delete --}}
                                    {{-- <form action="{{ route('attribute-values.destroy', $value->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-red-500 px-2 py-1 rounded text-white">Delete</button>
                                    </form> --}}

                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $values->links() }}
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
