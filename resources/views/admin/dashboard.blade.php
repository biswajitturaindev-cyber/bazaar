@extends('admin.layouts.master')

@section('title')
Sales Dashboard
@endsection

@section('breadcrumb')
Dashboard
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Vendor Count Details-->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-12 h-12 rounded-xl bg-teal-600 flex items-center justify-center">
                <i class="ri-group-line text-xl text-white"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">
                    Vendor Summary
                </h3>
                <p class="text-sm text-gray-500">
                    Overall Vendor Statistics
                </p>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
            <a href="#" class="flex justify-between items-center p-4 rounded-xl bg-slate-50 border border-slate-300 hover:bg-slate-200 transition">
                <div class="flex items-center gap-2">
                    <i class="ri-team-line text-slate-600"></i>
                    <span class="font-medium text-gray-700">
                        Total Vendors
                    </span>
                </div>
                <span class="text-2xl font-bold text-indslateigo-700">150</span>
            </a>
            <a href="#" class="flex justify-between items-center p-4 rounded-xl bg-blue-50 border border-blue-300 hover:bg-blue-200 transition">
                <div class="flex items-center gap-2">
                    <i class="ri-user-add-line text-blue-600"></i>
                    <span class="font-medium text-blue-700">
                        Today's Joining
                    </span>
                </div>
                <span class="text-2xl font-bold text-blue-700">2</span>
            </a>
            <a href="#" class="flex justify-between items-center p-4 rounded-xl bg-green-50 border border-green-300 hover:bg-green-200 transition">
                <div class="flex items-center gap-2">
                    <i class="ri-team-line text-green-600"></i>
                    <span class="font-medium text-gray-700">
                        Active/Open
                    </span>
                </div>
                <span class="text-2xl font-bold text-green-700">148</span>
            </a>
            <a href="#" class="flex justify-between items-center p-4 rounded-xl bg-red-50 border border-red-300 hover:bg-red-200 transition">
                <div class="flex items-center gap-2">
                    <i class="ri-team-line text-red-600"></i>
                    <span class="font-medium text-red-700">
                        Block/Close
                    </span>
                </div>
                <span class="text-2xl font-bold text-red-700">2</span>
            </a>

        </div>
        
    </div>
    <!-- Commision Details -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-12 h-12 rounded-xl bg-teal-600 flex items-center justify-center">
                <i class="ri-cash-line text-2xl text-white"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">
                    Commision Summary
                </h3>
                <p class="text-sm text-gray-500">
                    Overall Vendor Commision Statistics
                </p>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
            <a href="#" class="flex justify-between items-center p-4 rounded-xl bg-slate-50 border border-slate-300 hover:bg-slate-200 transition">
                <div class="flex items-center gap-2">
                    <i class="ri-cash-line text-slate-600"></i>
                    <span class="font-medium text-gray-700">
                        Total Earning
                    </span>
                </div>
                <span class="text-2xl font-bold text-indslateigo-700">₹1150.00</span>
            </a>
            <a href="#" class="flex justify-between items-center p-4 rounded-xl bg-green-50 border border-green-300 hover:bg-green-200 transition">
                <div class="flex items-center gap-2">
                    <i class="ri-user-add-line text-green-600"></i>
                    <span class="font-medium text-green-700">
                        Today's Earning
                    </span>
                </div>
                <span class="text-2xl font-bold text-green-700">₹20.25</span>
            </a>
            <a href="#" class="flex justify-between items-center p-4 rounded-xl bg-blue-50 border border-blue-300 hover:bg-blue-200 transition">
                <div class="flex items-center gap-2">
                    <i class="ri-cash-line text-blue-600"></i>
                    <span class="font-medium text-blue-700">
                        Credit Amount
                    </span>
                </div>
                <span class="text-2xl font-bold text-blue-700">₹820.00</span>
            </a>
            <a href="#" class="flex justify-between items-center p-4 rounded-xl bg-orange-50 border border-orange-300 hover:bg-orange-200 transition">
                <div class="flex items-center gap-2">
                    <i class="ri-team-line text-orange-600"></i>
                    <span class="font-medium text-orange-700">
                        Balance Amount
                    </span>
                </div>
                <span class="text-2xl font-bold text-orange-700">₹330.00</span>
            </a>

        </div>
        
    </div>
    <!-- Vendor Details -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-12 h-12 rounded-xl bg-teal-600 flex items-center justify-center">
                <i class="ri-group-line text-xl text-white"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">
                    Vendor Details
                </h3>
                <p class="text-sm text-gray-500">
                    Overall Vendor Details Summary
                </p>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-3">
            <a href="#" class="flex justify-between items-center p-4 rounded-xl bg-slate-50 border border-slate-300 hover:bg-slate-200 transition">
                <div class="flex items-center gap-2">
                    <i class="ri-team-line text-slate-600"></i>
                    <span class="font-medium text-gray-700">
                        Cat & Sub Category wise
                    </span>
                </div>
                <span class="text-2xl font-bold text-indslateigo-700">150</span>
            </a>
            <a href="#" class="flex justify-between items-center p-4 rounded-xl bg-blue-50 border border-blue-300 hover:bg-blue-200 transition">
                <div class="flex items-center gap-2">
                    <i class="ri-pencil-line text-blue-600"></i>
                    <span class="font-medium text-blue-700">
                        Pending KYC & Profile
                    </span>
                </div>
                <span class="text-2xl font-bold text-blue-700">8</span>
            </a>
            <a href="#" class="flex justify-between items-center p-4 rounded-xl bg-purple-50 border border-purple-300 hover:bg-purple-200 transition">
                <div class="flex items-center gap-2">
                    <i class="ri-cash-line text-purple-600"></i>
                    <span class="font-medium text-purple-700">
                        Total Wallet Credit Balance
                    </span>
                </div>
                <span class="text-2xl font-bold text-purple-700">₹148.00</span>
            </a>

        </div>
    </div>
    <!-- Invoice Details -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-12 h-12 rounded-xl bg-teal-600 flex items-center justify-center">
                <i class="ri-receipt-line text-xl text-white"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">
                    Invoice Details
                </h3>
                <p class="text-sm text-gray-500">
                    Overall Invoice Details Summary
                </p>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-3">
            <a href="#" class="flex justify-between items-center p-4 rounded-xl bg-slate-50 border border-slate-300 hover:bg-slate-200 transition">
                <div class="flex items-center gap-2">
                    <i class="ri-receipt-line text-slate-600"></i>
                    <span class="font-medium text-gray-700">
                        Total no of invoice
                    </span>
                </div>
                <span class="text-2xl font-bold text-indslateigo-700">150</span>
            </a>
            <a href="#" class="flex justify-between items-center p-4 rounded-xl bg-blue-50 border border-blue-300 hover:bg-blue-200 transition">
                <div class="flex items-center gap-2">
                    <i class="ri-receipt-line text-blue-600"></i>
                    <span class="font-medium text-blue-700">
                        Today no of invoice
                    </span>
                </div>
                <span class="text-2xl font-bold text-blue-700">8</span>
            </a>
            <a href="#" class="flex justify-between items-center p-4 rounded-xl bg-purple-50 border border-purple-300 hover:bg-purple-200 transition">
                <div class="flex items-center gap-2">
                    <i class="ri-receipt-line text-purple-600"></i>
                    <span class="font-medium text-purple-700">
                        Pending confirmation Invoice
                    </span>
                </div>
                <span class="text-2xl font-bold text-purple-700">10</span>
            </a>

        </div>
    </div>
</div>


@endsection