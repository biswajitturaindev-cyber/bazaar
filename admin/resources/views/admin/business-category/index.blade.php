@extends('admin.layouts.master')

@section('title')
    Business Category List
@endsection

@section('breadcrumb')
    Business Category
@endsection

@section('content')
    <!-- Row -->
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            <div class="flex justify-between items-center px-5 py-3 border-b">
                <h2 class="text-lg font-semibold">Business Category Category List</h2>

                <a href="{{ route('business-categories.create') }}"
                    class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                    + Add Business Category
                </a>
            </div>

            <div class="overflow-x-auto p-5">
                <table class="w-full text-sm text-left" id="example">
                    <thead class="bg-gray-100">
                        <tr class="border">
                            <th class="px-3 py-2">Sl.No</th>
                            <th class="px-3 py-2">Category Name</th>
                            <th class="px-3 py-2">Category Image</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($categories as $businessCategory)
                            <tr class="border-l border-r">

                                {{-- Serial --}}
                                <td class="px-3 py-2">
                                    {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                                </td>

                                {{-- Name --}}
                                <td class="px-3 py-2">
                                    {{ $businessCategory->name ?? '-' }}
                                </td>

                                {{-- Image --}}
                                <td class="px-3 py-2">
                                    @if ($businessCategory->image)
                                        <a href="{{ asset('storage/business_category/' . $businessCategory->image) }}" data-fancybox="category-gallery">
                                            <img src="{{ asset('storage/business_category/' . $businessCategory->image) }}"
                                                width="50" height="50"
                                                class="rounded cursor-pointer"
                                                style="object-fit:cover;">
                                        </a>
                                    @else
                                        <span class="text-gray-400">No Image</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-3 py-2">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded
                                        {{ $businessCategory->status == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $businessCategory->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-3 py-2 gap-2">

                                    {{-- Edit --}}
                                    <a href="{{ route('business-categories.edit', $businessCategory->id) }}"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                        Edit
                                    </a>

                                    {{-- Delete --}}
                                    {{-- <form action="{{ route('business-categories.destroy', $businessCategory->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this business category?')">
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
            </div>

        </div>
    </div>



    @push('scripts')
        <link href="{{ asset('admin_assets/datatables/dataTables.dataTables.css') }}" type="text/css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
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

            document.addEventListener("DOMContentLoaded", function () {
                Fancybox.bind("[data-fancybox='gallery']", {
                    Toolbar: {
                        display: ["close"]
                    }
                });
            });
        </script>
    @endpush
@endsection
