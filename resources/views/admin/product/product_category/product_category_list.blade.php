@extends('admin.layouts.master')

@section('title')
    Product Category List
@endsection

@section('breadcrumb')
    Products
@endsection

@section('content')
    <!-- Row -->
    <div class="grid grid-cols-1 lg:gap-16 md:gap-10">
        <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

            <div class="flex justify-between items-center px-5 py-3 border-b">
                <h2 class="text-lg font-semibold">Product Category List</h2>

                <a href="{{ route('admin.product.category') }}"
                    class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                    + Add Category
                </a>
            </div>

            <div class="overflow-x-auto p-5">
                <table class="w-full text-sm text-left" id="example">
                    <thead class="bg-gray-100">
                        <tr class="border">
                            <th class="px-3 py-2">Sl.No</th>
                            <th class="px-3 py-2">Category Name</th>
                            <th class="px-3 py-2">Category Image</th>
                            <th class="px-3 py-2">Description</th>
                            <th class="px-3 py-2">Commission</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($data as $category)
                            <tr class="border-l border-r">
                                <td class="px-3 py-2">{{ $loop->iteration }}</td>
                                <td class="px-3 py-2">
                                    {{ $category->name ?: '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    @if($category->image)
                                        <img src="{{ asset('storage/category/'.$category->image) }}"
                                            alt="Category Image"
                                            width="50" height="50"
                                            style="object-fit:cover;">
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    {{ $category->description ?: '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $category->commission ?: '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded
                                {{ $category->status == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $category->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td class="px-3 py-2  gap-2">
                                    <a href="{{ route('admin.product.category.edit', $category->id) }}"
                                        class="bg-blue-500 text-white px-2 py-1 rounded">
                                        Edit
                                    </a>

                                    {{-- <form action="{{ route('admin.product.category.delete', $category->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">
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
