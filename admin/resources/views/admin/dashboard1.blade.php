@extends('admin.layouts.master')

@section('title')
Member Dashboard
@endsection

@section('breadcrumb')
Dashboard
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-2 lg:gap-10 md:gap-6 gap-4">
    <div class="flex flex-col gap-y-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 lg:gap-6 gap-3">
            <a href="{{ route('admin.member.list') }}" 
               class="bg-white bg-[url({{ asset('admin_assets/images/card-bg.png') }})] bg-contain bg-no-repeat bg-right rounded-xl shadow-[0px_6px_16px_rgba(0,0,0,0.05)] p-6 flex justify-between items-center">
        
                <div>
                    <p class="text-gray-500 text-md font-medium">Total Joining Count</p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-2">{{ $users->totalUsers }}</h2>
        
                    <p class="text-sm text-gray-500 mt-2">
                        Total Active Account By 
                        <span class="text-green-500 font-semibold">
                            ₹{{ $packages->totalGold + $packages->totalVipGold + $packages->totalDream }}
                        </span>
                    </p>
                </div>
        
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-indigo-500 text-white">
                    <i class="ri-group-line text-2xl"></i>
                </div>
            </a>
        
            <a href="{{ route('admin.today.join.member.list') }}" 
               class="bg-white bg-[url({{ asset('admin_assets/images/card-bg.png') }})] bg-contain bg-no-repeat bg-right rounded-xl shadow-[0px_6px_16px_rgba(0,0,0,0.05)] p-6 flex justify-between items-center">
        
                <div>
                    <p class="text-gray-500 text-md font-medium">Today Joining Count</p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-2">{{ $users->todayUsers }}</h2>
        
                    <p class="text-sm text-gray-500 mt-2">
                        Today Active Account By 
                        <span class="text-blue-500 font-semibold">
                            ₹{{ $packages->todayGold + $packages->todayVipGold + $packages->todayDream }}
                        </span>
                    </p>
                </div>
        
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-500 text-white">
                    <i class="ri-group-line text-2xl"></i>
                </div>
            </a>
        </div>
    </div>
    <div class="bg-white bg-[url({{ asset('admin_assets/images/card-bg.png') }})] bg-contain bg-no-repeat bg-right rounded-xl shadow-[0px_6px_16px_rgba(0,0,0,0.05)] p-4 flex justify-between items-start">
        <table class="w-full text-sm text-left border border-gray-200 rounded-lg overflow-hidden border-collapse">
            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="px-2 py-1">Package</th>
                    <th class="px-2 py-1 text-center">Today</th>
                    <th class="px-2 py-1 text-center">Total</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 border">

                <tr>
                    <td class="px-2 py-1 border border-gray-200 font-medium text-yellow-600">Gold</td>
                    <td class="px-2 py-1 border border-gray-200 text-center">₹{{ $packages->todayGold }}</td>
                    <td class="px-2 py-1 border border-gray-200 text-center">₹{{ $packages->totalGold }}</td>
                </tr>

                <tr>
                    <td class="px-2 py-1 border border-gray-200 font-medium text-orange-500">VIP Gold</td>
                    <td class="px-2 py-1 border border-gray-200 text-center">₹{{ $packages->todayVipGold }}</td>
                    <td class="px-2 py-1 border border-gray-200 text-center">₹{{ $packages->totalVipGold }}</td>
                </tr>

                <tr>
                    <td class="px-2 py-1 border border-gray-200 font-medium text-purple-600">Dream</td>
                    <td class="px-2 py-1 border border-gray-200 text-center">₹{{ $packages->todayDream }}</td>
                    <td class="px-2 py-1 border border-gray-200 text-center">₹{{ $packages->totalDream }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection