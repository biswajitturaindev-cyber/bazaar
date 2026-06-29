@extends('admin.layouts.master')

@section('title')
    Orders List
@endsection

@section('breadcrumb')
    Orders
@endsection

@section('content')
    <style>
        :root {
            --navy: #1a1a2e;
            --navy-2: #16213e;
            --orange: #f97316;
            --orange-light: #fff7ed;
            --surface: #f4f6fb;
            --white: #ffffff;
            --border: #e4e8f0;
            --text: #1a1a2e;
            --muted: #6b7280;
            --green: #16a34a;
            --green-bg: #dcfce7;
            --yellow: #ca8a04;
            --yellow-bg: #fef9c3;
            --red: #dc2626;
            --red-bg: #fee2e2;
            --blue: #2563eb;
            --blue-bg: #dbeafe;
            --gray-bg: #f1f5f9;
        }

        /* ── Page ── */
        .orders-page {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--surface);
            min-height: 100vh;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 22px 28px 18px;
        }

        .page-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: -0.3px;
        }

        .page-title span {
            color: var(--orange);
        }

        .page-subtitle {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }

        /* ── Flash ── */
        .flash {
            margin: 0 28px 14px;
            padding: 11px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .flash-success {
            background: var(--green-bg);
            color: var(--green);
            border-left: 3px solid var(--green);
        }

        .flash-error {
            background: var(--red-bg);
            color: var(--red);
            border-left: 3px solid var(--red);
        }

        /* ── Filter bar ── */
        .filter-bar {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin: 0 28px 20px;
            padding: 16px 20px;
        }

        .filter-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 12px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 2fr 1.5fr 1fr 1fr 1fr 1fr auto;
            gap: 10px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .filter-group label {
            font-size: 10px;
            font-weight: 600;
            color: var(--muted);
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .filter-input {
            width: 100%;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 12.5px;
            color: var(--text);
            background: var(--surface);
            outline: none;
            transition: border-color .15s;
            font-family: inherit;
        }

        .filter-input:focus {
            border-color: var(--orange);
            background: var(--white);
        }

        .filter-input::placeholder {
            color: #b0b8c8;
        }

        .btn-search {
            background: var(--navy);
            color: var(--white);
            border: none;
            border-radius: 8px;
            padding: 9px 20px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            font-family: inherit;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background .15s;
        }

        .btn-search:hover {
            background: var(--navy-2);
        }

        /* ── Card ── */
        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin: 0 28px 28px;
            overflow: hidden;
        }

        .table-wrap {
            overflow-x: auto;
        }

        /* ── Table ── */
        table.orders-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .orders-table thead {
            background: var(--navy);
        }

        .orders-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .65);
            white-space: nowrap;
            border: none;
        }

        .orders-table thead th.right {
            text-align: right;
        }

        .orders-table thead th.center {
            text-align: center;
        }

        .orders-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .1s;
        }

        .orders-table tbody tr:last-child {
            border-bottom: none;
        }

        .orders-table tbody tr:hover {
            background: #f8faff;
        }

        .orders-table tbody td {
            padding: 13px 16px;
            color: var(--text);
            vertical-align: middle;
            border: none;
        }

        .orders-table tbody td.right {
            text-align: right;
        }

        .orders-table tbody td.center {
            text-align: center;
        }

        .sl-num {
            font-size: 11px;
            color: var(--muted);
            font-weight: 500;
        }

        .order-no {
            font-weight: 700;
            color: var(--navy);
            font-size: 13px;
        }

        .invoice-no {
            font-size: 11px;
            color: var(--muted);
            font-family: 'Courier New', monospace;
            margin-top: 2px;
        }

        .biz-name {
            font-weight: 600;
            color: var(--navy);
        }

        .items-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: var(--gray-bg);
            border-radius: 50%;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--navy);
        }

        .total-amt {
            font-weight: 700;
            color: var(--navy);
            font-size: 13.5px;
        }

        .total-amt .rupee {
            font-size: 11px;
            color: var(--muted);
            font-weight: 500;
            margin-right: 1px;
        }

        .date-val {
            font-size: 12px;
            color: var(--muted);
        }

        /* ── Status badges ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .3px;
            white-space: nowrap;
        }

        .badge::before {
            content: '';
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .badge-delivered {
            background: var(--green-bg);
            color: var(--green);
        }

        .badge-delivered::before {
            background: var(--green);
        }

        .badge-pending {
            background: var(--yellow-bg);
            color: var(--yellow);
        }

        .badge-pending::before {
            background: var(--yellow);
        }

        .badge-cancelled {
            background: var(--red-bg);
            color: var(--red);
        }

        .badge-cancelled::before {
            background: var(--red);
        }

        .badge-processing {
            background: var(--blue-bg);
            color: var(--blue);
        }

        .badge-processing::before {
            background: var(--blue);
        }

        .badge-shipped {
            background: var(--orange-light);
            color: var(--orange);
        }

        .badge-shipped::before {
            background: var(--orange);
        }

        .badge-default {
            background: var(--gray-bg);
            color: var(--muted);
        }

        .badge-default::before {
            background: var(--muted);
        }

        /* ── Action buttons ── */
        .action-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            justify-content: center;
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 10px;
            border-radius: 7px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-family: inherit;
            text-decoration: none;
            transition: opacity .15s, transform .1s;
            white-space: nowrap;
        }

        .btn-icon:hover {
            opacity: .85;
            transform: translateY(-1px);
        }

        .btn-icon svg {
            width: 13px;
            height: 13px;
            flex-shrink: 0;
        }

        .btn-view {
            background: var(--navy);
            color: #fff;
        }

        .btn-download {
            background: var(--orange-light);
            color: var(--orange);
            border: 1px solid #fed7aa;
        }

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }

        .empty-state svg {
            width: 48px;
            height: 48px;
            margin: 0 auto 12px;
            color: #d1d5db;
            display: block;
        }

        .empty-state p {
            font-size: 14px;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 4px;
        }

        .empty-state span {
            font-size: 12px;
        }

        /* ── Pagination ── */
        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            background: var(--surface);
        }

        .pagi-info {
            font-size: 12px;
            color: var(--muted);
        }

        /* ══════════════════════════════════════
           MODAL
        ══════════════════════════════════════ */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(10, 10, 30, .55);
            backdrop-filter: blur(3px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
        }

        .modal-backdrop.open {
            opacity: 1;
            pointer-events: all;
        }

        .modal {
            background: var(--white);
            border-radius: 16px;
            width: 100%;
            max-width: 780px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 24px 60px rgba(10, 10, 30, .2);
            transform: translateY(20px) scale(.98);
            transition: transform .22s ease;
            overflow: hidden;
        }

        .modal-backdrop.open .modal {
            transform: translateY(0) scale(1);
        }

        /* Modal header */
        .modal-head {
            background: var(--navy);
            padding: 18px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .modal-head-left {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .modal-head-title {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
        }

        .modal-head-sub {
            font-size: 11px;
            color: rgba(255, 255, 255, .5);
            font-family: monospace;
        }

        .modal-close {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255, 255, 255, .1);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            transition: background .15s;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, .2);
        }

        .modal-close svg {
            width: 16px;
            height: 16px;
        }

        /* Modal body — scrollable */
        .modal-body {
            overflow-y: auto;
            padding: 22px;
            flex: 1;
        }

        /* Info grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 22px;
        }

        .info-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
        }

        .info-box-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 5px;
        }

        .info-box-value {
            font-size: 13px;
            font-weight: 700;
            color: var(--navy);
        }

        .info-box-value.orange {
            color: var(--orange);
            font-size: 15px;
        }

        /* Address row */
        .addr-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 22px;
        }

        .addr-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-top: 3px solid var(--navy);
            border-radius: 10px;
            padding: 14px;
        }

        .addr-card-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--orange);
            margin-bottom: 8px;
        }

        .addr-card-text {
            font-size: 12px;
            color: #444;
            line-height: 1.65;
        }

        /* Section heading */
        .section-heading {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 10px;
        }

        /* Items table inside modal */
        .modal-items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .modal-items-table thead tr {
            background: var(--navy);
        }

        .modal-items-table thead th {
            padding: 9px 12px;
            color: rgba(255, 255, 255, .65);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-align: left;
            border: none;
        }

        .modal-items-table thead th.right {
            text-align: right;
        }

        .modal-items-table tbody tr {
            border-bottom: 1px solid var(--border);
        }

        .modal-items-table tbody tr:last-child {
            border-bottom: none;
        }

        .modal-items-table tbody td {
            padding: 10px 12px;
            border: none;
            vertical-align: middle;
        }

        .modal-items-table tbody td.right {
            text-align: right;
            font-weight: 600;
        }

        .modal-item-name {
            font-weight: 600;
            color: var(--navy);
        }

        .modal-item-sku {
            font-size: 10.5px;
            color: var(--muted);
            font-family: monospace;
        }

        /* Totals inside modal */
        .modal-totals {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
        }

        .modal-totals-inner {
            width: 280px;
        }

        .modal-totals-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            border-bottom: 1px solid var(--border);
            font-size: 12.5px;
        }

        .modal-totals-row:last-child {
            border-bottom: none;
        }

        .modal-totals-row .lbl {
            color: var(--muted);
        }

        .modal-totals-row .val {
            font-weight: 700;
            color: var(--navy);
        }

        .modal-totals-row .val.green {
            color: var(--green);
        }

        .modal-totals-grand {
            background: var(--navy);
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            padding: 10px 14px;
            margin-top: 8px;
        }

        .modal-totals-grand .lbl {
            color: rgba(255, 255, 255, .7);
            font-size: 12px;
            font-weight: 600;
        }

        .modal-totals-grand .val {
            color: var(--orange);
            font-size: 15px;
            font-weight: 700;
        }

        /* Modal footer */
        .modal-foot {
            flex-shrink: 0;
            padding: 14px 22px;
            border-top: 1px solid var(--border);
            background: var(--surface);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-foot-meta {
            font-size: 11px;
            color: var(--muted);
        }

        .btn-dl {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: var(--orange);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
            transition: background .15s;
        }

        .btn-dl:hover {
            background: #ea6c0a;
        }

        .btn-dl svg {
            width: 15px;
            height: 15px;
        }

        /* Loading spinner */
        .modal-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            gap: 14px;
            color: var(--muted);
            font-size: 13px;
        }

        .spinner {
            width: 36px;
            height: 36px;
            border: 3px solid var(--border);
            border-top-color: var(--orange);
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <div class="orders-page">

        {{-- Page Header --}}
        <div class="page-header">
            <div>
                <div class="page-title">Orders <span>List</span></div>
                <div class="page-subtitle">Manage and track all customer orders</div>
            </div>
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div class="flash flash-success">
                <svg viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;flex-shrink:0">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="flash flash-error">
                <svg viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;flex-shrink:0">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET">
            <div class="filter-bar">
                <div class="filter-label">Filter Orders</div>
                <div class="filter-grid">
                    <div class="filter-group">
                        <label>Search</label>
                        <input type="text" name="search" class="filter-input" placeholder="Order no, invoice, product…"
                            value="{{ request('search') }}">
                    </div>
                    <div class="filter-group">
                        <label>Business</label>
                        <select name="business_id" class="filter-input">
                            <option value="">All Businesses</option>
                            @foreach ($businesses as $business)
                                <option value="{{ $business->id }}"
                                    {{ request('business_id') == $business->id ? 'selected' : '' }}>
                                    {{ $business->business_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Month</label>
                        <select name="month" class="filter-input">
                            <option value="">Any</option>
                            @foreach (range(1, 12) as $month)
                                <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Year</label>
                        <select name="year" class="filter-input">
                            <option value="">Any</option>
                            @for ($year = date('Y'); $year >= 2023; $year--)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>From Date</label>
                        <input type="date" name="from_date" class="filter-input" value="{{ request('from_date') }}">
                    </div>
                    <div class="filter-group">
                        <label>To Date</label>
                        <input type="date" name="to_date" class="filter-input" value="{{ request('to_date') }}">
                    </div>
                    <button type="submit" class="btn-search">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                        Search
                    </button>
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="card">
            <div class="table-wrap">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order Details</th>
                            <th>Business</th>
                            <th>Items</th>
                            <th class="right">Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            @php
                                $status = strtolower($order->status);
                                $badgeClass = match ($status) {
                                    'delivered' => 'badge-delivered',
                                    'pending' => 'badge-pending',
                                    'cancelled' => 'badge-cancelled',
                                    'processing' => 'badge-processing',
                                    'shipped' => 'badge-shipped',
                                    default => 'badge-default',
                                };
                            @endphp
                            <tr>
                                <td><span
                                        class="sl-num">{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</span>
                                </td>
                                <td>
                                    <div class="order-no">{{ $order->order_no }}</div>
                                    <div class="invoice-no">{{ $order->invoice_no }}</div>
                                </td>
                                <td><span class="biz-name">{{ $order->business->business_name ?? '—' }}</span></td>
                                <td><span class="items-count">{{ $order->items->count() }}</span></td>
                                <td class="right">
                                    <span class="total-amt"><span
                                            class="rupee">₹</span>{{ number_format($order->grand_total, 2) }}</span>
                                </td>
                                <td><span class="badge {{ $badgeClass }}">{{ ucfirst($order->status) }}</span></td>
                                <td><span
                                        class="date-val">{{ \Carbon\Carbon::parse($order->placed_at)->format('d M Y') }}</span>
                                </td>
                                <td class="center">
                                    <div class="action-wrap">
                                        {{-- View --}}
                                        <button class="btn-icon btn-view"
                                            onclick="openOrderModal('{{ route('admin.orders.show', $order->id) }}')"
                                            title="View Details">
                                            <svg viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd"
                                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            View
                                        </button>
                                        {{-- Download Invoice --}}
                                        <a href="{{ route('admin.orders.invoice', $order->id) }}"
                                            class="btn-icon btn-download" title="Download Invoice" target="_blank">
                                            <svg viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Invoice
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p>No orders found</p>
                                        <span>Try adjusting your filters or date range</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="pagination-wrap">
                    <div class="pagi-info">Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of
                        {{ $orders->total() }} orders</div>
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- ═══════════════════════════════════════
     ORDER DETAIL MODAL
═══════════════════════════════════════ --}}
    <div class="modal-backdrop" id="orderModal" onclick="handleBackdropClick(event)">
        <div class="modal" id="modalBox">

            <div class="modal-head">
                <div class="modal-head-left">
                    <div class="modal-head-title" id="modalTitle">Order Details</div>
                    <div class="modal-head-sub" id="modalSub"></div>
                </div>
                <button class="modal-close" onclick="closeOrderModal()">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div class="modal-body" id="modalBody">
                <div class="modal-loading">
                    <div class="spinner"></div>
                    <span>Loading order details…</span>
                </div>
            </div>

            <div class="modal-foot" id="modalFoot" style="display:none;">
                <div class="modal-foot-meta" id="modalFootMeta"></div>
                <a href="#" id="modalInvoiceBtn" class="btn-dl" target="_blank">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Download Invoice
                </a>
            </div>

        </div>
    </div>

    <script>
        function openOrderModal(url) {
            const modal = document.getElementById('orderModal');
            const body = document.getElementById('modalBody');
            const foot = document.getElementById('modalFoot');
            const title = document.getElementById('modalTitle');
            const sub = document.getElementById('modalSub');

            // Reset state
            title.textContent = 'Order Details';
            sub.textContent = '';
            foot.style.display = 'none';
            body.innerHTML = `
            <div class="modal-loading">
                <div class="spinner"></div>
                <span>Loading order details…</span>
            </div>`;

            modal.classList.add('open');
            document.body.style.overflow = 'hidden';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => renderModal(data))
                .catch(() => {
                    body.innerHTML = `
                    <div class="modal-loading" style="color:#dc2626;">
                        <svg viewBox="0 0 20 20" fill="currentColor" style="width:36px;height:36px;color:#fca5a5"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>Failed to load order. Please try again.</span>
                    </div>`;
                });
        }

        function renderModal(d) {
            const order = d.order;
            const business = d.business || {};
            const items = d.items || [];
            const address = d.customer_address || {};

            document.getElementById('modalTitle').textContent = 'Order #' + order.order_no;
            document.getElementById('modalSub').textContent = order.invoice_no;

            // Status badge colour
            const statusMap = {
                delivered: 'badge-delivered',
                pending: 'badge-pending',
                cancelled: 'badge-cancelled',
                processing: 'badge-processing',
                shipped: 'badge-shipped'
            };
            const badgeClass = statusMap[(order.status || '').toLowerCase()] || 'badge-default';

            // Items rows
            const itemRows = items.map((item, i) => `
            <tr>
                <td>${i+1}</td>
                <td>
                    <div class="modal-item-name">${escHtml(item.product_name)}</div>
                    <div class="modal-item-sku">${escHtml(item.sku || '')}</div>
                </td>
                <td>${item.quantity}</td>
                <td class="right">₹${fmt(item.final_price)}</td>
                <td class="right">₹${fmt(item.subtotal)}</td>
            </tr>`).join('');

            document.getElementById('modalBody').innerHTML = `

    <div class="info-grid">

        <div class="info-box">
            <div class="info-box-label">Order No</div>
            <div class="info-box-value">${escHtml(order.order_no)}</div>
        </div>

        <div class="info-box">
            <div class="info-box-label">Invoice No</div>
            <div class="info-box-value">${escHtml(order.invoice_no)}</div>
        </div>

        <div class="info-box">
            <div class="info-box-label">Payment</div>
            <div class="info-box-value">
                ${escHtml(order.payment_method_text || '—')}
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-label">Status</div>
            <div class="info-box-value">
                <span class="badge ${badgeClass}">
                    ${cap(order.status)}
                </span>
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-label">Grand Total</div>
            <div class="info-box-value orange">
                ₹${fmt(order.grand_total)}
            </div>
        </div>

    </div>

    <div class="section-heading">
        Business Information
    </div>

    <div class="info-grid">

        <div class="info-box">
            <div class="info-box-label">Business</div>
            <div class="info-box-value">
                ${escHtml(business.business_name || '-')}
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-label">GST No</div>
            <div class="info-box-value">
                ${escHtml(business.gst_number || '-')}
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-label">Contact Person</div>
            <div class="info-box-value">
                ${escHtml(business.contact?.contact_person_name || '-')}
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-label">Contact Number</div>
            <div class="info-box-value">
                ${escHtml(business.contact?.contact_number || '-')}
            </div>
        </div>

    </div>

    <div class="addr-row">

        <div class="addr-card">

            <div class="addr-card-label">
                Billing Address
            </div>

            <div class="addr-card-text">

                ${escHtml(address.billing_address || '-')}

                <br>

                <strong>PIN :</strong>

                ${escHtml(address.billing_pincode || '')}

            </div>

        </div>

        <div class="addr-card">

            <div class="addr-card-label">
                Shipping Address
            </div>

            <div class="addr-card-text">

                ${escHtml(address.shipping_address || '-')}

                <br>

                <strong>PIN :</strong>

                ${escHtml(address.shipping_pincode || '')}

            </div>

        </div>

    </div>

    <div class="section-heading">
        Order Items
    </div>

    <table class="modal-items-table">

        <thead>

            <tr>

                <th>#</th>

                <th>Product</th>

                <th>Qty</th>

                <th class="right">
                    Price
                </th>

                <th class="right">
                    Product Commission
                </th>

                <th class="right">
                    Vendor Commission
                </th>

                <th class="right">
                    Subtotal
                </th>

            </tr>

        </thead>

        <tbody>

            ${itemRows}

        </tbody>

    </table>

    <div class="modal-totals">

        <div class="modal-totals-inner">

            <div class="modal-totals-row">
                <span class="lbl">Items Total</span>
                <span class="val">
                    ₹${fmt(order.items_total)}
                </span>
            </div>

            <div class="modal-totals-row">
                <span class="lbl">Discount</span>
                <span class="val green">
                    - ₹${fmt(order.discount_amount)}
                </span>
            </div>

            <div class="modal-totals-row">
                <span class="lbl">Platform Charge</span>
                <span class="val">
                    ₹${fmt(order.platform_charge)}
                </span>
            </div>

            <div class="modal-totals-row">
                <span class="lbl">Delivery Charge</span>
                <span class="val">
                    ₹${fmt(order.delivery_charge)}
                </span>
            </div>

            <div class="modal-totals-row">
                <span class="lbl">GST</span>
                <span class="val">
                    ₹${fmt(order.tax_amount)}
                </span>
            </div>

            <div class="modal-totals-grand">

                <span class="lbl">
                    Grand Total
                </span>

                <span class="val">
                    ₹${fmt(order.grand_total)}
                </span>

            </div>

        </div>

    </div>
`;

            // Footer
            const foot = document.getElementById('modalFoot');
            foot.style.display = 'flex';
            document.getElementById('modalFootMeta').textContent =
                'Placed on ' + (order.placed_at_formatted || order.placed_at);
            document.getElementById('modalInvoiceBtn').href = d.invoice_url;
        }

        function closeOrderModal() {
            document.getElementById('orderModal').classList.remove('open');
            document.body.style.overflow = '';
        }

        function handleBackdropClick(e) {
            if (e.target === document.getElementById('orderModal')) closeOrderModal();
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeOrderModal();
        });

        function fmt(n) {
            return parseFloat(n || 0).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function cap(s) {
            return s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
        }

        function escHtml(s) {
            const d = document.createElement('div');
            d.textContent = String(s);
            return d.innerHTML;
        }
    </script>
@endsection
