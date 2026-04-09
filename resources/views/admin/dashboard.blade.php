@extends('admin.layouts.master')

@section('title')
Member Dashboard
@endsection

@section('breadcrumb')
Dashboard
@endsection

@section('content')

<div class="flex pt-4 pb-6 items-center justify-between">
    <h1 class="text-2xl font-semibold text-orange-500">Dashboard</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <div class="bg-white p-6 rounded-xl shadow">
        <p>Total Joining Count</p>
        <h2 class="text-2xl font-bold">289</h2>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <p>Total Amount</p>
        <h2 class="text-2xl font-bold">3,19,535.00</h2>
    </div>

</div>


@endsection