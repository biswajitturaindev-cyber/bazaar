@extends('admin.layouts.master')

@section('title')
    Vendor Commission List
@endsection

@section('breadcrumb')
    Vendor Commission
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        <div class="flex justify-between items-center px-5 py-3 border-b">
            <h2 class="text-lg font-semibold">Vendor Commission List</h2>

            <form method="GET" action="{{ route('vendor-commissions.index') }}">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

                    {{-- Today --}}
                    <div class="cursor-pointer border rounded-xl p-5 hover:border-blue-500 hover:bg-blue-50 filter-card {{ request('filter_type') == 'today' ? 'border-blue-500 bg-blue-50' : '' }}"
                        data-filter="today">
                        <h3 class="font-semibold text-lg">Today</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            View Today's Commission
                        </p>
                    </div>

                    {{-- Current Month --}}
                    <div class="cursor-pointer border rounded-xl p-5 hover:border-blue-500 hover:bg-blue-50 filter-card {{ request('filter_type') == 'month' ? 'border-blue-500 bg-blue-50' : '' }}"
                        data-filter="month">
                        <h3 class="font-semibold text-lg">Current Month</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            View Monthly Commission
                        </p>
                    </div>

                    {{-- Financial Year --}}
                    <div class="border rounded-xl p-5">
                        <h3 class="font-semibold text-lg mb-3">
                            Financial Year
                        </h3>

                        <select name="financial_year"
                            class="border rounded-lg px-3 py-2 w-full">
                            <option value="">Select FY</option>

                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}-{{ $year + 1 }}"
                                    {{ request('financial_year') == ($year.'-'.($year+1)) ? 'selected' : '' }}>
                                    {{ $year }}-{{ substr($year + 1, -2) }}
                                </option>
                            @endfor

                        </select>
                    </div>

                    {{-- Custom Date Range --}}
                    <div class="border rounded-xl p-5">
                        <h3 class="font-semibold text-lg mb-3">
                            Custom Date Range
                        </h3>

                        <div class="space-y-2">
                            <input type="date"
                                name="from_date"
                                value="{{ request('from_date') }}"
                                class="border rounded-lg px-3 py-2 w-full">

                            <input type="date"
                                name="to_date"
                                value="{{ request('to_date') }}"
                                class="border rounded-lg px-3 py-2 w-full">
                        </div>
                    </div>

                </div>

                <input type="hidden"
                    name="filter_type"
                    id="filter_type"
                    value="{{ request('filter_type') }}">

                <div class="flex justify-end gap-2">

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                        Search
                    </button>

                    <a href="{{ route('vendor-commissions.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg">
                        Reset
                    </a>

                </div>

            </form>
        </div>

        <div class="overflow-x-auto p-5">
            <table class="w-full text-sm text-left" id="example">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2">Sl.No</th>
                        <th class="px-3 py-2">Vendor</th>
                        <th class="px-3 py-2">Commission (%)</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Action</th>
                    </tr>
                </thead>

                <tbody>
                    {{-- @foreach($vendorCommissions as $commission)
                        <tr>
                            <td class="px-3 py-2">
                                {{ ($vendorCommissions->currentPage() - 1) * $vendorCommissions->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-3 py-2">
                                {{ $commission->vendor->name ?? '-' }}
                            </td>

                            <td class="px-3 py-2">
                                {{ $commission->commission_percentage }}%
                            </td>

                            <td class="px-3 py-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded
                                    {{ $commission->status ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $commission->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            <td class="px-3 py-2 flex gap-2">
                                <a href="{{ route('vendor-commissions.edit', $commission->id) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach --}}
                </tbody>
            </table>

            <div class="mt-4">
                {{-- {{ $vendorCommissions->links() }} --}}
            </div>
        </div>

    </div>
</div>
@endsection
@push('scripts')
<script>
    document.querySelectorAll('.filter-card').forEach(card => {

        card.addEventListener('click', function() {

            document.querySelectorAll('.filter-card').forEach(c => {
                c.classList.remove('border-blue-500', 'bg-blue-50');
            });

            this.classList.add('border-blue-500', 'bg-blue-50');

            document.getElementById('filter_type').value =
                this.dataset.filter;
        });

    });
</script>
@endpush
