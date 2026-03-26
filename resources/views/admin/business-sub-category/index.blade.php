@extends('admin.layouts.master')

@section('title')
    Business Sub Category List
@endsection

@section('breadcrumb')
    Business Sub Category
@endsection

@section('content')
    <!-- Row -->
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            <div class="flex justify-between items-center px-5 py-3 border-b">
                <h2 class="text-lg font-semibold">Business Sub Category List</h2>

                <a href="{{ route('business-sub-categories.create') }}"
                    class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                    + Add Business Sub Category
                </a>
            </div>

            <div class="overflow-x-auto p-5">
                <table class="w-full text-sm text-left" id="example">
                    <thead class="bg-gray-100">
                        <tr class="border">
                            <th class="px-3 py-2">Sl.No</th>
                            <th class="px-3 py-2">Category Name</th>
                            <th class="px-3 py-2">Sub Category Name</th>
                            <th class="px-3 py-2">Sub Category Image</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($subcategories as $subCategory)
                            <tr class="border-l border-r">

                                {{-- Serial --}}
                                <td class="px-3 py-2">
                                    {{ ($subcategories->currentPage() - 1) * $subcategories->perPage() + $loop->iteration }}
                                </td>

                                {{-- Category Name --}}
                                <td class="px-3 py-2">
                                    {{ $subCategory->category?->name ?? '-' }}
                                </td>

                                {{-- Name --}}
                                <td class="px-3 py-2">
                                    {{ $subCategory->name ?? '-' }}
                                </td>

                                {{-- Image --}}
                                <td class="px-3 py-2">
                                    @if ($subCategory->image)
                                        <img src="{{ asset('storage/business_sub_category/' . $subCategory->image) }}"
                                            width="50" height="50" class="rounded" style="object-fit:cover;">
                                    @else
                                        <span class="text-gray-400">No Image</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-3 py-2">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded
                                        {{ $subCategory->status == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $subCategory->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-3 py-2 gap-2">
                                    <a href="{{ route('business-sub-categories.edit', $subCategory->id) }}"
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



    @push('scripts')
        <link href="{{ asset('admin_assets/datatables/dataTables.dataTables.css') }}" type="text/css" rel="stylesheet">
        <script src="{{ asset('admin_assets/datatables/dataTables.js') }}" type="text/javascript"></script>
        <script src="{{ asset('admin_assets/js/script.js') }}"></script>

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
