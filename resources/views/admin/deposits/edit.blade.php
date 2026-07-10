@extends('admin.layouts.master')

@section('title')
    Edit Deposit
@endsection

@section('breadcrumb')
    Edit Deposit
@endsection

@section('content')
<div class="grid grid-cols-1 lg:gap-16 md:gap-10">
    <div class="bg-white shadow-[0px_6px_16px_rgba(0,0,0,0.05)] rounded-xl">

        {{-- Header --}}
        <div class="flex justify-between items-center p-5 border-b">
            <h2 class="text-lg font-semibold">Edit Deposit</h2>

            <a href="{{ route('deposits.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back
            </a>
        </div>

        <form action="{{ route('deposits.update', $deposit->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Username --}}
                <div>
                    <label class="block mb-2 font-medium">Username</label>
                    <input type="text"
                        value="{{ $deposit->business->user->vendor_id }}"
                        class="w-full border rounded-lg px-3 py-2 bg-gray-100"
                        readonly>
                </div>

                {{-- Name --}}
                <div>
                    <label class="block mb-2 font-medium">Name</label>
                    <input type="text"
                        value="{{ $deposit->business->user->name }}"
                        class="w-full border rounded-lg px-3 py-2 bg-gray-100"
                        readonly>
                </div>

                {{-- Amount --}}
                <div>
                    <label class="block mb-2 font-medium">Amount</label>
                    <input type="text"
                        value="₹ {{ number_format($deposit->amount,2) }}"
                        class="w-full border rounded-lg px-3 py-2 bg-gray-100"
                        readonly>
                </div>

                {{-- Payment Method --}}
                <div>
                    <label class="block mb-2 font-medium">Payment Method</label>
                    <input type="text"
                        value="{{ $deposit->payment_method_label  }}"
                        class="w-full border rounded-lg px-3 py-2 bg-gray-100"
                        readonly>
                </div>

                {{-- UTR --}}
                <div>
                    <label class="block mb-2 font-medium">UTR / Ref ID</label>
                    <input type="text"
                        value="{{ $deposit->ref_id }}"
                        class="w-full border rounded-lg px-3 py-2 bg-gray-100"
                        readonly>
                </div>

                {{-- Transaction ID --}}
                <div>
                    <label class="block mb-2 font-medium">Transaction ID</label>
                    <input type="text"
                        name="transaction_id"
                        value="{{ old('transaction_id', $deposit->transaction_id) }}"
                        class="w-full border rounded-lg px-3 py-2">

                    @error('transaction_id')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Payment Proof --}}
                <div class="md:col-span-2">
                    <label class="block mb-2 font-medium">Payment Proof</label>

                    @if($deposit->payment_proof)
                        <img src="{{ asset('storage/'.$deposit->payment_proof) }}"
                            class="w-72 rounded border">

                        <a href="{{ asset('storage/'.$deposit->payment_proof) }}"
                            target="_blank"
                            class="inline-block mt-3 bg-blue-600 text-white px-4 py-2 rounded">
                            View Full Image
                        </a>
                    @endif
                </div>

                {{-- Status --}}
                <div>
                    <label class="block mb-2 font-medium">Status</label>

                    <select name="status"
                        class="w-full border rounded-lg px-3 py-2">

                        <option value="0"
                            {{ old('status',$deposit->status)==0 ? 'selected':'' }}>
                            Pending
                        </option>

                        <option value="1"
                            {{ old('status',$deposit->status)==1 ? 'selected':'' }}>
                            Approved
                        </option>

                        <option value="2"
                            {{ old('status',$deposit->status)==2 ? 'selected':'' }}>
                            Rejected
                        </option>

                    </select>
                </div>

                {{-- Remarks --}}
                <div>
                    <label class="block mb-2 font-medium">Remarks</label>

                    <textarea name="remarks"
                        rows="4"
                        class="w-full border rounded-lg px-3 py-2">{{ old('remarks',$deposit->admin_note) }}</textarea>
                </div>

            </div>

            <div class="p-5 border-t flex justify-end">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Update Deposit
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
