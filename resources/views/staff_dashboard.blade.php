@extends('layout.app')

@section('title', 'Staff Dashboard')

@section('content')
@php
    $canViewFollowUps = app('hasPermission')(30, 'view') || app('hasPermission')(30, 'add');
    $canViewMeetings = app('hasPermission')(31, 'view') || app('hasPermission')(31, 'add');
@endphp
<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.25; }
    }
    .staff-stat-card {
        border-radius: 16px;
        padding: 24px 20px;
        background-color: #ffffff;
        color: #1b2850;
        position: relative;
        overflow: hidden;
        min-height: 110px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        border: 1px solid #e9edf5;
        margin-bottom: 20px;
    }

    .staff-stat-card .stat-number {
        font-size: 2.2rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 6px;
    }

    .staff-stat-card .stat-label {
        font-size: 1rem;
        font-weight: 600;
        opacity: .95;
        margin-bottom: 2px;
    }

    .staff-stat-card .stat-sub {
        font-size: .78rem;
        color: #64748b;
    }

    .staff-stat-card .stat-icon {
        position: absolute;
        right: 18px;
        bottom: 16px;
        font-size: 1.8rem;
        opacity: 0.15;
    }

    .card-attendance .stat-icon {
        color: #ff6b35;
    }

    .card-leave .stat-icon {
        color: #e74c3c;
    }

    .card-followup .stat-icon {
        color: #3498db;
    }

    .card-meeting .stat-icon {
        color: #2ecc71;
    }

    .staff-panel {
        border: 1px solid #e9edf5;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        height: 100%;
    }

    .staff-panel .card-body {
        padding: 18px;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1b2850;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid #edf1f7;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .staff-table-wrap {
        overflow-x: auto;
    }

    .staff-delivery-scroll {
        max-height: 285px;
        overflow-y: auto;
        overflow-x: auto;
        padding-right: 4px;
    }

    .staff-delivery-scroll .staff-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .staff-delivery-scroll .staff-table td {
        height: 47px;
    }

    .staff-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 620px;
    }

    .staff-table thead th {
        background: #fff;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        padding: 12px 10px;
        border: 0;
        border-bottom: 1px solid #e9edf5;
    }

    .staff-table td {
        font-size: 13px;
        color: #475569;
        vertical-align: middle;
        padding: 14px 10px;
        border: 0;
        border-bottom: 1px solid #eef2f7;
        white-space: nowrap;
    }

    .staff-table .wrap-cell {
        white-space: normal !important;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .staff-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .staff-table tbody tr:hover {
        background: #fafcff;
    }

    .staff-empty-state {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: #64748b;
        text-align: center;
    }

    .badge-pending {
        background: #fff3cd;
        color: #856404;
    }

    .badge-completed {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-rescheduled,
    .badge-scheduled {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-high {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-medium {
        background: #fff3cd;
        color: #856404;
    }

    .badge-low {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge,
    .priority-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .mobile-toggle-btn,
    .mobile-detail-row {
        display: none;
    }

    .today-box {
        border: 1px solid #e9edf5;
        border-radius: 14px;
        background: #fff;
        padding: 16px;
        height: 100%;
    }

    .today-box-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1b2850;
        margin-bottom: 14px;
    }

    .today-check-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px;
        background: #f8fafc;
    }

    .check-pill {
        display: inline-block;
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
    }

    .check-pill.in {
        background: #16a34a;
    }

    .check-pill.out {
        background: #0ea5e9;
    }

    .hours-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
        color: #334155;
    }

    .hours-time {
        font-size: 12px;
        font-weight: 700;
        color: #fff;
        border-radius: 4px;
        padding: 4px 8px;
    }

    .hours-time.worked {
        background: #0891b2;
    }

    .hours-time.remaining {
        background: #ef4444;
    }

    .hours-bar {
        height: 8px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
        margin-bottom: 14px;
    }

    .hours-bar > span {
        display: block;
        height: 100%;
    }

    .hours-bar .worked-fill {
        background: #06b6d4;
    }

    .hours-bar .remaining-fill {
        background: #f87171;
    }

    .status-chip {
        display: inline-block;
        background: #16a34a;
        color: #fff;
        border-radius: 4px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 700;
    }

    @media (max-width: 575px) {
        .staff-stat-card .stat-number {
            font-size: 1.7rem;
        }

        .staff-stat-card {
            min-height: 95px;
            padding: 18px 16px;
        }

        .staff-panel .card-body {
            padding: 14px;
        }

        .staff-table {
            min-width: 100%;
        }

        .staff-table thead th:not(:first-child):not(:last-child),
        .staff-row-summary td:not(:first-child):not(:last-child) {
            display: none;
        }

        .staff-table thead th:first-child {
            width: 44px;
            padding: 12px 8px;
        }

        .staff-table thead th:last-child,
        .staff-row-summary td:last-child {
            display: table-cell;
            width: 52px;
            text-align: right;
            padding: 10px 8px;
        }

        .staff-row-summary td {
            padding: 12px 8px;
            white-space: normal;
        }

        .staff-row-summary td:first-child {
            width: 44px;
            color: #0f172a;
            font-weight: 600;
        }

        .mobile-toggle-btn {
            width: 28px;
            height: 28px;
            border: 0;
            border-radius: 50%;
            background: #ff9f43;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            line-height: 1;
            padding: 0;
            box-shadow: 0 6px 12px rgba(239, 68, 68, 0.18);
        }

        .mobile-toggle-btn .toggle-minus {
            display: none;
        }

        .mobile-toggle-btn[aria-expanded="true"] .toggle-plus {
            display: none;
        }

        .mobile-toggle-btn[aria-expanded="true"] .toggle-minus {
            display: inline;
        }

        .mobile-detail-row.is-open {
            display: table-row;
        }

        .mobile-detail-row td {
            padding: 0 0 12px;
            border-bottom: 1px solid #eef2f7;
        }

        .mobile-detail-item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 12px;
            border-bottom: 1px solid #eef2f7;
            font-size: 12px;
        }

        .mobile-detail-item:last-child {
            border-bottom: 0;
        }

        .mobile-detail-label {
            color: #0f172a;
            font-weight: 600;
        }

        .mobile-detail-value {
            color: #475569;
            text-align: right;
            word-break: break-word;
        }
    }

    /* Wrap product names in tables */
    .dataview .product-link {
        display: flex;
        align-items: flex-start;
        max-width: 250px;
        text-decoration: none;
        color: inherit;
    }

    .dataview .product-name {
        flex: 1;
        min-width: 70px;
        white-space: normal !important;
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
        line-height: 1.35;
    }

    .mobile-product-info {
        display: flex;
        align-items: flex-start;
        justify-content: flex-end;
        gap: 8px;
        width: 100%;
    }

    .mobile-product-info .product-name {
        text-align: right;
    }
</style>


<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Welcome, {{ ucwords($user->name) }} 👋</h4>
        </div>
    </div>

    @if (isset($isAccountingStaff) && $isAccountingStaff)
        <div class="row dashboard-widgets-row mb-4">
            <div class="col-lg-4 col-sm-6 col-12 mb-3">
                <a href="{{ route('sales.list') }}">
                    <div class="dash-widget dash1">
                        <div class="dash-widgetimg">
                            <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash2.svg' }}" alt="img"></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5>
                                @if ($currencyPosition === 'left')
                                    {{ $currencySymbol }}
                                @endif
                                <span class="counters" data-count="{{ $totalSalesAmount }}">
                                    {{ number_format($totalSalesAmount, 2) }}</span>
                                @if ($currencyPosition === 'right')
                                    {{ $currencySymbol }}
                                @endif
                            </h5>
                            <h6>Total Sales Amount</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-sm-6 col-12 mb-3">
                <a href="{{ route('purchase.lists') }}">
                    <div class="dash-widget">
                        <div class="dash-widgetimg">
                            <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash1.svg' }}" alt="img"></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5>
                                @if ($currencyPosition === 'left')
                                    {{ $currencySymbol }}
                                @endif
                                <span class="counters" data-count="{{ $totalPurchaseAmount }}">
                                    {{ number_format($totalPurchaseAmount, 2) }}
                                </span>
                                @if ($currencyPosition === 'right')
                                    {{ $currencySymbol }}
                                @endif
                            </h5>
                            <h6>Total Purchase Amount</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-sm-6 col-12 mb-3">
                <a href="{{ route('expense.list') }}">
                    <div class="dash-widget dash2">
                        <div class="dash-widgetimg">
                            <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash3.svg' }}" alt="img"></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5>
                                @if ($currencyPosition === 'left')
                                    {{ $currencySymbol }}
                                @endif
                                <span class="counters" data-count="{{ $totalExpenseAmount }}">
                                    {{ number_format($totalExpenseAmount, 2) }}</span>
                                @if ($currencyPosition === 'right')
                                    {{ $currencySymbol }}
                                @endif
                            </h5>
                            <h6>Total Expense Amount</h6>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Latest Sales</h4>
                            <a href="{{ route('sales.list') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="table-responsive dataview">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th class="details-column d-md-none">Details</th>
                                        <th>Product Name</th>
                                        <th>Grand Total</th>
                                        <th>Sale Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($latestSales as $index => $item)
                                        <tr>
                                            <td>
                                                <div>
                                                    <div class="d-md-none text-muted large mt-1 ms-2">
                                                        {{ $item->customer_name ?? 'N/A' }}
                                                    </div>
                                                    <div style="display: flex; align-items: center;">
                                                        <a href="/sales-details/{{ $item->order_id }}" class="d-flex align-items-center text-decoration-none">
                                                            <span class="order-id">{{ $item->order_number ?? 'N/A' }}</span>
                                                        </a>
                                                    </div>
                                                    <!-- Collapsible Details for Mobile -->
                                                    <div class="collapse mobile-details-collapse d-md-none" id="sales-details-{{ $item->order_id ?? $index }}">
                                                        <div class="mobile-details-row">
                                                            <span class="mobile-details-label">Product:</span>
                                                            <span class="mobile-details-value">
                                                                @php
                                                                    $images = json_decode($item->images, true);
                                                                    $imagePath = !empty($images) && isset($images[0])
                                                                        ? env('ImagePath') . 'storage/' . $images[0]
                                                                        : env('ImagePath') . '/admin/assets/img/product/noimage.png';
                                                                @endphp
                                                                <div class="mobile-product-info">
                                                                    <span class="product-name">{{ $item->product_name ?? 'N/A' }}</span>
                                                                    <img src="{{ asset($imagePath) }}" alt="Product" style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px;">
                                                                </div>
                                                            </span>
                                                        </div>
                                                        <div class="mobile-details-row">
                                                            <span class="mobile-details-label">Grand Total:</span>
                                                            <span class="mobile-details-value">
                                                                @if ($currencyPosition === 'left'){{ $currencySymbol }}@endif
                                                                {{ number_format($item->total_amount ?? 0, 2) }}
                                                                @if ($currencyPosition === 'right'){{ $currencySymbol }}@endif
                                                            </span>
                                                        </div>
                                                        <div class="mobile-details-row">
                                                            <span class="mobile-details-label">Sale Date:</span>
                                                            <span class="mobile-details-value">
                                                                {{ \Carbon\Carbon::parse($item->order_date)->format('d F Y ') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="details-column d-md-none">
                                                <a href="#sales-details-{{ $item->order_id ?? $index }}" class="toggle-details" data-bs-toggle="collapse">
                                                    <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                </a>
                                            </td>
                                            <td class="productimgname d-none d-md-table-cell">
                                                <a href="/product-view/{{ $item->product_id }}" class="product-link">
                                                    <img src="{{ asset($imagePath) }}" alt="Product Image" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px; flex-shrink: 0;">
                                                    <span class="product-name">{{ $item->product_name ?? 'N/A' }}</span>
                                                </a>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                @if ($currencyPosition === 'left'){{ $currencySymbol }}@endif
                                                {{ number_format($item->total_amount ?? 0, 2) }}
                                                @if ($currencyPosition === 'right'){{ $currencySymbol }}@endif
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                {{ \Carbon\Carbon::parse($item->order_date)->format('d F Y') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No sales records found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Latest Purchases</h4>
                            <a href="{{ route('purchase.lists') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="table-responsive dataview">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Bill No</th>
                                        <th class="details-column d-md-none">Details</th>
                                        <th>Product Name</th>
                                        <th>Grand Total</th>
                                        <th>Purchase Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($latestPurchases as $index => $item)
                                        <tr>
                                            <td>
                                                <div>
                                                    <div class="d-md-none text-muted large mt-1 ms-2">
                                                        {{ $item->vendor_name ?? 'N/A' }}
                                                    </div>
                                                    <div style="display: flex; align-items: center;">
                                                        <a href="/print-purchase/{{ $item->invoice_id }}" class="d-flex align-items-center text-decoration-none">
                                                            <span class="order-id">{{ $item->bill_no ?? 'N/A' }}</span>
                                                        </a>
                                                    </div>
                                                    <!-- Collapsible Details for Mobile -->
                                                    <div class="collapse mobile-details-collapse d-md-none" id="purchase-details-{{ $item->invoice_id ?? $index }}">
                                                        <div class="mobile-details-row">
                                                            <span class="mobile-details-label">Product:</span>
                                                            <span class="mobile-details-value">
                                                                @php
                                                                    $images = json_decode($item->images, true);
                                                                    $imagePath = !empty($images[0])
                                                                        ? env('ImagePath') . 'storage/' . $images[0]
                                                                        : env('ImagePath') . 'admin/assets/img/product/noimage.png';
                                                                @endphp
                                                                <div class="mobile-product-info">
                                                                    <span class="product-name">{{ $item->product_name ?? 'N/A' }}</span>
                                                                    <img src="{{ asset($imagePath) }}" alt="Product" style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px;">
                                                                </div>
                                                            </span>
                                                        </div>
                                                        <div class="mobile-details-row">
                                                            <span class="mobile-details-label">Grand Total:</span>
                                                            <span class="mobile-details-value">
                                                                @if ($currencyPosition === 'left'){{ $currencySymbol }}@endif
                                                                {{ number_format($item->grand_total ?? 0, 2) }}
                                                                @if ($currencyPosition === 'right'){{ $currencySymbol }}@endif
                                                            </span>
                                                        </div>
                                                        <div class="mobile-details-row">
                                                            <span class="mobile-details-label">Purchase Date:</span>
                                                            <span class="mobile-details-value">
                                                                {{ \Carbon\Carbon::parse($item->purchase_date)->format('d F Y') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="details-column d-md-none">
                                                <a href="#purchase-details-{{ $item->invoice_id ?? $index }}" class="toggle-details" data-bs-toggle="collapse">
                                                    <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                </a>
                                            </td>
                                            <td class="productimgname d-none d-md-table-cell">
                                                <a href="/product-view/{{ $item->product_id }}" class="product-link">
                                                    <img src="{{ asset($imagePath) }}" alt="Product Image" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px; flex-shrink: 0;">
                                                    <span class="product-name">{{ $item->product_name ?? 'N/A' }}</span>
                                                </a>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                @if ($currencyPosition === 'left'){{ $currencySymbol }}@endif
                                                {{ number_format($item->grand_total ?? 0, 2) }}
                                                @if ($currencyPosition === 'right'){{ $currencySymbol }}@endif
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                {{ \Carbon\Carbon::parse($item->purchase_date)->format('d F Y') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No purchase records found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-6 col-md-3">
            <div class="staff-stat-card card-attendance">
                <div>
                    <div class="stat-number">
                        {{ fmod((float) $totalPresent, 1.0) === 0.0 ? number_format($totalPresent, 0) : number_format($totalPresent, 1) }}
                    </div>
                    <div class="stat-label">Total Attendance</div>
                    <div class="stat-sub">{{ $monthName }}</div>
                </div>
                <i class="fas fa-calendar-check stat-icon"></i>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="staff-stat-card card-leave">
                <div>
                    <div class="stat-number">{{ $totalLeave }}</div>
                    <div class="stat-label">Total Leave</div>
                    <div class="stat-sub">{{ $monthName }}</div>
                </div>
                <i class="fas fa-user-times stat-icon"></i>
            </div>
        </div>

        @if ($canViewFollowUps)
            <div class="col-6 col-md-3">
                <div class="staff-stat-card card-followup">
                    <div>
                        <div class="stat-number">{{ $totalFollowUps }}</div>
                        <div class="stat-label">Total Follow-ups</div>
                        <div class="stat-sub">Assigned to me</div>
                    </div>
                    <i class="fas fa-phone-alt stat-icon"></i>
                </div>
            </div>
        @endif

        @if ($canViewMeetings)
            <div class="col-6 col-md-3">
                <div class="staff-stat-card card-meeting">
                    <div>
                        <div class="stat-number">{{ $totalMeetings }}</div>
                        <div class="stat-label">Total Meetings</div>
                        <div class="stat-sub">Assigned to me</div>
                    </div>
                    <i class="fas fa-handshake stat-icon"></i>
                </div>
            </div>
        @endif
    </div>

    @php
        $workedParts = array_map('intval', explode(':', $hoursWorkedFormatted ?? '00:00:00'));
        $workedSecondsValue = (($workedParts[0] ?? 0) * 3600) + (($workedParts[1] ?? 0) * 60) + ($workedParts[2] ?? 0);
        $standardSeconds = 8 * 3600;
        $workedPercent = min(100, round(($workedSecondsValue / $standardSeconds) * 100, 2));
        $remainingPercent = max(0, 100 - $workedPercent);
        $workStatusClass = $todayStatus === 'Checked In' ? 'success' : ($todayStatus === 'Checked Out' ? 'primary' : 'secondary');
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="today-box">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="today-box-title mb-0">Today's Check-In / Check-Out</div>
                </div>

                @if(isset($todaySessions) && $todaySessions->isNotEmpty())
                    @foreach($todaySessions as $session)
                        @php
                            $inTime = $session->check_in_time ? \Carbon\Carbon::parse($todayStr . ' ' . $session->check_in_time)->format('h:i A') : 'Pending';
                            $outTime = $session->check_out_time ? \Carbon\Carbon::parse($todayStr . ' ' . $session->check_out_time)->format('h:i A') : 'Working...';
                        @endphp
                        <div class="today-check-card mt-2">
                            <div class="text-muted mb-2">{{ \Carbon\Carbon::parse($todayStr)->format('d M Y') }}</div>
                            <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                                <span class="check-pill in">
                                    Check-in: {{ $inTime }}
                                </span>
                                <span class="check-pill out">
                                    Check-out: {{ $outTime }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="today-check-card mt-2" style="border: 1px dashed #e2e8f0; text-align: center; padding: 24px 16px;">
                        <i class="fas fa-fingerprint" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                        <div style="color: #94a3b8; font-size: 13px; font-weight: 500;">No check-in recorded for today</div>
                        <div style="color: #cbd5e1; font-size: 12px; margin-top: 4px;">
                            {{ \Carbon\Carbon::parse($todayStr)->format('d M Y') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="today-box">
                <div class="today-box-title">Today's Hours</div>

                <div class="hours-line">
                    <span>Hours Worked</span>
                    <span id="hoursWorkedBadge" class="hours-time worked">{{ $hoursWorkedFormatted }}</span>
                </div>
                <div class="hours-bar">
                    <span id="workedProgressBar" class="worked-fill" style="width: {{ $workedPercent }}%;"></span>
                </div>

                <div class="hours-line">
                    <span>Remaining Hours</span>
                    <span id="remainingHoursBadge" class="hours-time remaining">{{ $remainingHoursFormatted }}</span>
                </div>
                <div class="hours-bar">
                    <span id="remainingProgressBar" class="remaining-fill" style="width: {{ $remainingPercent }}%;"></span>
                </div>

                <div class="hours-line mt-3 mb-0">
                    <span>Status</span>
                    <span class="status-chip bg-{{ $workStatusClass }}">{{ $todayStatus }}</span>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script>
    /* ── Staff Dashboard: Today's Hours Live Timer ──────────────────────────
       Timer base = totalWorkedSeconds calculated by PHP server from DB records.
       Client clock drift/timezone issues are eliminated by only tracking
       elapsed time since page load.
    ──────────────────────────────────────────────────────────────────────── */
    (function () {
        const isCheckedIn      = {{ $todayStatus === 'Checked In' ? 'true' : 'false' }};
        const standardSec      = {{ (int) $standardSeconds }};
        const initialWorkedSec = {{ (int) $totalWorkedSeconds }};
        const checkInUnix      = {{ $todayCheckInUnix ?? 'null' }};

        // No check-in today → nothing to animate
        if (!checkInUnix) {
            console.log('[StaffDashboard] No check-in today — timer not started.');
            return;
        }

        const workedBadge    = document.getElementById('hoursWorkedBadge');
        const remainingBadge = document.getElementById('remainingHoursBadge');
        const workedBar      = document.getElementById('workedProgressBar');
        const remainingBar   = document.getElementById('remainingProgressBar');
        if (!workedBadge || !remainingBadge) return;

        function pad(n) { return String(n).padStart(2, '0'); }
        function secsToHMS(s) {
            s = Math.max(0, Math.floor(s));
            const h = Math.floor(s / 3600);
            const m = Math.floor((s % 3600) / 60);
            const sec = s % 60;
            return pad(h) + ':' + pad(m) + ':' + pad(sec);
        }

        function updateUI(workedSecs) {
            const remaining = Math.max(0, standardSec - workedSecs);
            const workedPct = Math.min(100, (workedSecs / standardSec) * 100);
            const remainPct = Math.min(100, (remaining  / standardSec) * 100);
            workedBadge.textContent    = secsToHMS(workedSecs);
            remainingBadge.textContent = secsToHMS(remaining);
            if (workedBar)    workedBar.style.width    = workedPct + '%';
            if (remainingBar) remainingBar.style.width  = remainPct + '%';
        }

        if (!isCheckedIn) {
            // ── CHECKED OUT: final static value — no timer ───────────────────
            console.log('[StaffDashboard] CHECKED OUT — final worked seconds:', initialWorkedSec, '=', secsToHMS(initialWorkedSec));
            updateUI(initialWorkedSec);
        } else {
            // ── STILL WORKING: live timer ────────────────────────────────────
            // Elapsed = time passed since the page was opened
            const pageLoadTime = Date.now();
            function tick() {
                const elapsedSinceLoad = Math.floor((Date.now() - pageLoadTime) / 1000);
                const currentWorked = initialWorkedSec + elapsedSinceLoad;
                updateUI(currentWorked);
            }
            tick();                    // run immediately on page load
            setInterval(tick, 1000);   // then update every second
            console.log('[StaffDashboard] LIVE TIMER started. Base Server Seconds:', initialWorkedSec);
        }
    })();
    </script>
    @endpush

    <div class="row g-4 mb-4">
        @if ($canViewFollowUps)
            <div class="col-12 col-xl-6">
                <div class="card staff-panel">
                    <div class="card-body">
                        <div class="section-title">
                            <i class="fas fa-phone-alt text-primary"></i> My Follow-ups
                        </div>

                    @if ($recentFollowUps->isEmpty())
                        <div class="staff-empty-state">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No follow-ups assigned to you yet.
                        </div>
                    @else
                        <div class="staff-table-wrap">
                            <table class="table staff-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer Name</th>
                                        <th>Purpose</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Date &amp; Time</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentFollowUps as $i => $fu)
                                        <tr class="staff-row-summary">
                                            <td>{{ $i + 1 }}</td>
                                            <td class="wrap-cell">{{ $fu->subject_name ?? '—' }}</td>
                                            <td class="wrap-cell">{{ $fu->purpose }}</td>
                                            <td>
                                                <span class="priority-badge badge-{{ strtolower($fu->priority) }}">
                                                    {{ $fu->priority }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge badge-{{ strtolower($fu->status) }}">
                                                    {{ $fu->status }}
                                                </span>
                                            </td>
                                            <td class="wrap-cell">{{ $fu->follow_up_datetime ? $fu->follow_up_datetime->format('d M Y, h:i A') : '—' }}</td>
                                            <td>
                                                <button type="button" class="mobile-toggle-btn" aria-expanded="false">
                                                    <span class="toggle-plus">+</span>
                                                    <span class="toggle-minus">-</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="mobile-detail-row">
                                            <td colspan="7">
                                                <div class="mobile-detail-card">
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Customer Name</span>
                                                        <span class="mobile-detail-value">{{ $fu->subject_name ?? '—' }}</span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Purpose</span>
                                                        <span class="mobile-detail-value">{{ $fu->purpose }}</span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Priority</span>
                                                        <span class="mobile-detail-value">
                                                            <span class="priority-badge badge-{{ strtolower($fu->priority) }}">
                                                                {{ $fu->priority }}
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Status</span>
                                                        <span class="mobile-detail-value">
                                                            <span class="status-badge badge-{{ strtolower($fu->status) }}">
                                                                {{ $fu->status }}
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Date &amp; Time</span>
                                                        <span class="mobile-detail-value">{{ $fu->follow_up_datetime ? $fu->follow_up_datetime->format('d M Y, h:i A') : '—' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($totalFollowUps > 10)
                            <div class="text-end mt-3">
                                <a href="{{ route('followup.list') }}" class="btn btn-sm btn-outline-primary">
                                    View All Follow-ups <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        @endif
                    @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($canViewMeetings)
            <div class="col-12 col-xl-6">
                <div class="card staff-panel">
                    <div class="card-body">
                        <div class="section-title">
                            <i class="fas fa-handshake text-success"></i> My Meetings
                        </div>

                    @if ($recentMeetings->isEmpty())
                        <div class="staff-empty-state">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No meetings assigned to you yet.
                        </div>
                    @else
                        <div class="staff-table-wrap">
                            <table class="table staff-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Customer Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Scheduled On</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentMeetings as $i => $meeting)
                                        <tr class="staff-row-summary">
                                            <td>{{ $i + 1 }}</td>
                                            <td class="wrap-cell">{{ $meeting->meeting_title }}</td>
                                            <td class="wrap-cell">{{ $meeting->customer->name ?? '—' }}</td>
                                            <td class="wrap-cell">{{ $meeting->meeting_type }}</td>
                                            <td>
                                                <span class="status-badge badge-{{ strtolower($meeting->status) }}">
                                                    {{ $meeting->status }}
                                                </span>
                                            </td>
                                            <td>{{ $meeting->scheduled_on ? $meeting->scheduled_on->format('d M Y, h:i A') : '—' }}</td>
                                            <td>
                                                <button type="button" class="mobile-toggle-btn" aria-expanded="false">
                                                    <span class="toggle-plus">+</span>
                                                    <span class="toggle-minus">-</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="mobile-detail-row">
                                            <td colspan="7">
                                                <div class="mobile-detail-card">
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Title</span>
                                                        <span class="mobile-detail-value">{{ $meeting->meeting_title }}</span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Customer Name</span>
                                                        <span class="mobile-detail-value">{{ $meeting->customer->name ?? '—' }}</span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Type</span>
                                                        <span class="mobile-detail-value">{{ $meeting->meeting_type }}</span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Status</span>
                                                        <span class="mobile-detail-value">
                                                            <span class="status-badge badge-{{ strtolower($meeting->status) }}">
                                                                {{ $meeting->status }}
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Scheduled On</span>
                                                        <span class="mobile-detail-value">{{ $meeting->scheduled_on ? $meeting->scheduled_on->format('d M Y, h:i A') : '—' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($totalMeetings > 10)
                            <div class="text-end mt-3">
                                <a href="{{ route('meeting.list') }}" class="btn btn-sm btn-outline-success">
                                    View All Meetings <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        @endif
                    @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if (!empty($showProductsDelivery))
        <div class="row mt-2">
            <div class="col-12">
                <div class="card staff-panel">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="section-title mb-0">
                                <i class="fas fa-truck text-warning"></i> Products Delivery
                            </div>
                            <a href="{{ route('sales.list') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>

                        <div class="table-responsive staff-delivery-scroll">
                            <table class="table staff-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Order#</th>
                                        <th>Delivered By</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $deliveryStatuses = [
                                            'pending' => 'Pending',
                                            'delivered' => 'Delivered',
                                            'partially_delivered' => 'Partially Delivered',
                                            'cancelled' => 'Cancelled',
                                        ];
                                        $deliveryStatusMap = [
                                            'pending' => 'pending',
                                            'delivered' => 'delivered',
                                            'partial' => 'partially_delivered',
                                            'partially_delivered' => 'partially_delivered',
                                            'cancelled' => 'cancelled',
                                        ];
                                        $deliveries = $dashboardDeliveries ?? collect();
                                    @endphp

                                    @forelse ($deliveries as $delivery)
                                        <tr>
                                            <td>
                                                <a href="{{ route('sales.delivery', $delivery->order_id) }}">
                                                    #{{ $delivery->order->order_number ?? $delivery->order_id }}
                                                </a>
                                            </td>
                                            <td>{{ $delivery->deliveredBy->name ?? $delivery->delivered_by ?? '--' }}</td>
                                            <td>
                                                @php
                                                    $deliveryStatusKey = strtolower(str_replace([' ', '-'], '_', trim((string) $delivery->status)));
                                                    $currentDeliveryStatus = $deliveryStatusMap[$deliveryStatusKey] ?? 'pending';
                                                @endphp
                                                <select
                                                    class="form-control form-control-sm pending-delivery-status"
                                                    @if ($delivery->has_delivery_record)
                                                        data-url="{{ route('sales.delivery.status.update', $delivery->id) }}"
                                                    @else
                                                        data-url="{{ route('sales.order.delivery.status.update', $delivery->order_id) }}"
                                                    @endif
                                                    data-current-status="{{ $currentDeliveryStatus }}"
                                                >
                                                    @foreach ($deliveryStatuses as $statusValue => $statusLabel)
                                                        <option value="{{ $statusValue }}" @selected($currentDeliveryStatus === $statusValue)>{{ $statusLabel }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No deliveries found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.mobile-toggle-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const summaryRow = button.closest('tr');
                const detailRow = summaryRow ? summaryRow.nextElementSibling : null;

                if (!detailRow || !detailRow.classList.contains('mobile-detail-row')) {
                    return;
                }

                const isOpen = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                detailRow.classList.toggle('is-open', !isOpen);
            });
        });
    });

    document.addEventListener('change', function (event) {
        const select = event.target.closest('.pending-delivery-status');

        if (!select) {
            return;
        }

        const previousStatus = select.dataset.currentStatus;
        const newStatus = select.value;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const url = select.dataset.url;

        if (!url) {
            select.value = previousStatus;
            alert('Delivery status update URL is missing.');
            return;
        }

        select.disabled = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ status: newStatus }),
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    if (!response.ok) {
                        throw data;
                    }

                    return data;
                });
            })
            .then(function (response) {
                if (response.status) {
                    select.dataset.currentStatus = newStatus;
                    return;
                }

                select.value = previousStatus;
                alert(response.message || 'Unable to update delivery status.');
            })
            .catch(function (error) {
                select.value = previousStatus;
                alert(error?.message || 'Unable to update delivery status.');
            })
            .finally(function () {
                select.disabled = false;
            });
    });
</script>
@endsection
