@extends('admin.layouts.master')

@section('title')
    Package List
@endsection

@section('breadcrumb')
    Package Master
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center px-5 py-3 border-b">
            <h2 class="text-lg font-semibold">Package List</h2>

            <a href="{{ route('packages.create') }}"
                class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                + Add Package
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
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Stars</th>
                        <th class="px-3 py-2">Price (₹)</th>
                        <th class="px-3 py-2">Product Limit</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @foreach ($packages as $package)
                        <tr class="border-l border-r">

                            {{-- Serial --}}
                            <td class="px-3 py-2">
                                {{ ($packages->currentPage() - 1) * $packages->perPage() + $loop->iteration }}
                            </td>

                            {{-- Name --}}
                            <td class="px-3 py-2 font-medium">
                                {{ $package->name }}
                            </td>

                            {{-- Stars --}}
                            <td class="px-3 py-2">
                                ⭐ {{ $package->stars }}
                            </td>

                            {{-- Price --}}
                            <td class="px-3 py-2">
                                ₹ {{ number_format($package->price, 2) }}
                            </td>

                            {{-- Product Limit --}}
                            <td class="px-3 py-2">
                                {{ $package->product_limit ?? 'Unlimited' }}
                            </td>

                            {{-- Status --}}
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded
                                    {{ $package->status ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $package->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-3 py-2 flex gap-2">

                                {{-- Edit --}}
                                <a href="{{ route('packages.edit', $package->id) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                    Edit
                                </a>

                                {{-- Delete --}}
                                <button onclick="deletePackage({{ $package->id }})"
                                    class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                    Delete
                                </button>

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $packages->links() }}
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

    function deletePackage(id) {
        if (confirm('Delete this package?')) {
            fetch(`/admin/packages/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    }
</script>
@endpush
@endsection
