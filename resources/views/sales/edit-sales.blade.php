@extends('layout.app')

@section('title', ($sales->quotation_status ?? '') === 'advance_receipt' ? 'Edit Advance Receipt' : (($sales->quotation_status ?? '') === 'quotation' ? 'Edit Quotation' : 'Edit Sales'))

@section('content')
    <style>
        .d-none {
            display: none !important;
        }

        /* Fix for labour items select */
        .select2-labour+.select2-container,
        .product-select+.select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding-top: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .img-flag {
            vertical-align: middle;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 29px !important;
        }

        .gst-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 8px;
        }

        .gst-badge.with {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            width: fit-content;
        }

        .gst-badge.without {
            background: #f8f9fa;
            color: #6c757d;
            border: 1px solid #e9ecef;
            width: fit-content;
        }

        .product-gst-details {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 8px;
            margin-top: 5px;
            font-size: 12px;
        }

        .product-gst-details small {
            display: block;
            line-height: 1.4;
        }

        .bank-label-row {
            display: flex;
            align-items: center;
            /* justify-content: space-between; */
            gap: 8px;
            margin-bottom: 8px;
        }

        .bank-add-btn {
            border: 1px solid #ff9f43;
            background: #fff7ed;
            color: #ff9f43;
            border-radius: 4px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.2;
        }

        .bank-add-btn:hover {
            background: #ff9f43;
            color: #fff;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1px;
        }

        .page-header .page-title {
            display: flex;
            align-items: center;
        }

        .page-header .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 0;
        }

        .gst-header {
            display: flex;
            align-items: center;
            margin-bottom: 0 !important;
        }

        .gst-header .d-flex {
            display: flex;
            align-items: center;

        }

        .custom-radio-label {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 0;
        }

        .form-check-input {
            margin-top: 0;
        }

        .pos-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: 1px solid #ff9f43;
            background: #ff9f43;
            color: #fff;
            border-radius: 4px;
            padding: 3px 8px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
            height: 28px;
            margin-left: 6px;
        }

        .pos-back-btn:hover {
            background: #ff9f43;
            color: #fff;
        }

        .pos-back-btn i {
            font-size: 12px;
            line-height: 1;
        }

        @media (max-width: 767.98px) {
            .page-header {
                flex-wrap: wrap;
                align-items: flex-start;
                gap: 10px;
            }

            .page-header .form-check {
                margin-left: 0 !important;
            }

            .gst-header {
                width: 100%;
                flex-wrap: wrap;
                gap: 10px;
            }

            .gst-header .d-flex {
                flex-wrap: wrap;
                row-gap: 8px;
            }

            .custom-radio-label {
                white-space: nowrap;
            }

            .pos-back-btn {
                margin-left: auto;
                min-width: 70px;
                justify-content: center;
            }

            #paid_type_col,
            #bank_container,
            #cash_amount_col,
            #online_amount_col,
            #pending_amount_col {
                flex: 0 0 50%;
                max-width: 50%;
            }

            #payment_method_col {
                flex: 0 0 100%;
                max-width: 100%;
            }

            #payment_details_row > .col-lg-12 > .row {
                row-gap: 0;
            }

            #bank_container .bank-label-row {
                flex-wrap: nowrap;
                justify-content: space-between;
                align-items: center;
                gap: 6px;
            }

            #bank_container .bank-add-btn {
                padding: 3px 8px;
                font-size: 11px;
                white-space: nowrap;
            }
        }

        @media (max-width: 991.98px) {
            .table-responsive {
                overflow-x: visible;
            }

            .table-responsive .table thead {
                display: none;
            }

            .table-responsive .table,
            .table-responsive .table tbody,
            .table-responsive .table tr,
            .table-responsive .table td {
                display: block;
                width: 100%;
            }

            #product-table-body {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            #product-table-body tr[data-product-id] {
                position: relative;
                padding: 14px 14px 12px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
            }

            #product-table-body tr[data-product-id]>td {
                border: 0;
                padding: 0;
            }

            #product-table-body tr[data-product-id]>td:first-child {
                display: none;
            }

            #product-table-body tr[data-product-id]>td:nth-child(2) {
                display: grid;
                grid-template-columns: 56px minmax(0, 1fr);
                gap: 12px;
                align-items: start;
                padding-right: 36px;
                margin-bottom: 12px;
            }

            #product-table-body tr[data-product-id]>td:nth-child(2) .product-img {
                display: block;
                width: 56px;
                height: 56px;
                border-radius: 10px;
                overflow: hidden;
                background: #f8fafc;
            }

            #product-table-body tr[data-product-id]>td:nth-child(2) .product-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            #product-table-body tr[data-product-id]>td:nth-child(2)>a:last-of-type {
                display: block;
                color: #111827;
                font-weight: 600;
                line-height: 1.35;
                margin-bottom: 4px;
                word-break: break-word;
            }

            #product-table-body tr[data-product-id]>td:nth-child(2) .gst-badge {
                margin-left: 0;
                margin-top: 2px;
            }

            #product-table-body tr[data-product-id]>td:not(:first-child):not(:nth-child(2)):not(:last-child) {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
                padding: 8px 0;
            }

            #product-table-body tr[data-product-id]>td:not(:first-child):not(:nth-child(2)):not(:last-child)::before {
                content: attr(data-label);
                flex: 0 0 96px;
                color: #6b7280;
                font-size: 12px;
                font-weight: 600;
                line-height: 1.4;
            }

            #product-table-body tr[data-product-id]>td[data-label="GST Details"] {
                align-items: flex-start;
            }

            #product-table-body tr[data-product-id]>td[data-label="GST Details"] .product-gst-details,
            #product-table-body tr[data-product-id]>td[data-label="GST Details"] .text-muted {
                flex: 1 1 auto;
                text-align: right;
            }

            #product-table-body tr[data-product-id]>td[data-label="Total"] .total-amount {
                flex: 1 1 auto;
                text-align: right;
            }

            #product-table-body tr[data-product-id] input.form-control {
                width: 140px !important;
                max-width: 100%;
                margin-left: auto;
                text-align: right;
            }

            #product-table-body tr[data-product-id]>td:last-child {
                position: absolute;
                top: 12px;
                right: 12px;
                width: auto;
                display: block;
            }

            #product-table-body tr[data-product-id]>td:last-child .delete-set {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 26px;
                height: 26px;
            }
        }

        /* Fix for Labour Items on iPad Pro/Tablets */
        @media (min-width: 992px) and (max-width: 1199px) {
            .labour-item-row .col-lg-5 {
                flex: 0 0 33.333333% !important;
                max-width: 33.333333% !important;
            }

            .labour-item-row .col-lg-3 {
                flex: 0 0 25% !important;
                max-width: 25% !important;
            }

            .labour-item-row .col-lg-1 {
                flex: 0 0 16.666667% !important;
                max-width: 16.666667% !important;
            }

            .labour-item-row .btn {
                padding: 6px 10px !important;
                height: 38px !important;
                width: 100% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .labour-item-row .select2-container {
                width: 100% !important;
            }
        }

        /* Allow word wrap in Select2 multiple choices */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            white-space: normal !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
        }

        /* Allow word wrap in Select2 dropdown options */
        .select2-results__option {
            white-space: normal !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
        }

        /* Allow word wrap in product name table cells */
        #product-table-body td:nth-child(2) {
            white-space: normal !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            max-width: 300px;
        }
        #product-table-body td:nth-child(2) a {
            white-space: normal !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
        }
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>{{ ($sales->quotation_status ?? '') === 'advance_receipt' ? 'Edit Advance Receipt' : (($sales->quotation_status ?? '') === 'quotation' ? 'Edit Quotation' : 'Edit Sales') }}</h4>
            </div>
            @php
                $user = auth()->user();
            @endphp
            @php
                $subAdminId = session('selectedSubAdminId');
                $role = Auth::user()->role;
            @endphp
            @if ($role == 'admin' || $role == 'staff' || $role == 'sub-admin')
                <div class="form-check ms-3">
                    <input class="form-check-input me-2" type="checkbox" id="quotationToggle" value="quotation"
                        {{ ($sales->quotation_status ?? 'sales') === 'quotation' ? 'checked' : '' }}>
                    <label class="form-check-label" for="quotationToggle">Quotation</label>
                </div>
            @endif
            <div class="gst-header mb-4" style="display: flex; align-items: center; justify-content: space-between;">
                <div class="d-flex">
                    <label class="custom-radio-label" style="margin-right: 1rem;">
                        <input type="radio" name="gst_option" id="without_gst" value="without_gst" {{ ($sales->gst_option ?? 'without_gst') === 'without_gst' ? 'checked' : '' }} />
                        Without GST
                    </label>

                    <label class="custom-radio-label">
                        <input type="radio" name="gst_option" id="with_gst" value="with_gst" {{ ($sales->gst_option ?? 'without_gst') === 'with_gst' ? 'checked' : '' }} />
                        With GST
                    </label>
                </div>
                <a href="{{ route('sales.list') }}" class="pos-back-btn">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>




        <div class="card">
            <div class="card-body">
                <div class="row">
                    @php
                        $activePayments = $sales->payments->filter(function ($payment) {
                            return (int) ($payment->isDeleted ?? 0) === 0;
                        });

                        $prefillOrderTotal = max(0, (float) ($sales->total_amount ?? 0));
                        $prefillRemainingAmount = max(0, (float) ($sales->remaining_amount ?? 0));
                        $prefillTotalPaidFromPayments = (float) $activePayments->sum(function ($payment) {
                            return (float) ($payment->payment_amount ?? 0);
                        });
                        $prefillTotalPaid = $prefillTotalPaidFromPayments > 0
                            ? min($prefillOrderTotal, $prefillTotalPaidFromPayments)
                            : max(0, $prefillOrderTotal - $prefillRemainingAmount);
                        $prefillCashAmount = (float) $activePayments->sum(function ($payment) {
                            return (float) ($payment->cash_amount ?? 0);
                        });
                        $prefillOnlineAmount = (float) $activePayments->sum(function ($payment) {
                            $method = strtolower((string) ($payment->payment_method ?? ''));
                            if ($method === 'cash') {
                                return 0;
                            }

                            $upiAmount = (float) ($payment->upi_amount ?? 0);
                            return $upiAmount > 0 ? $upiAmount : (float) ($payment->payment_amount ?? 0);
                        });
                        $prefillBankId = optional($activePayments->first(function ($payment) {
                            return !empty($payment->bank_id);
                        }))->bank_id;

                        $orderPaymentMethodRaw = strtolower((string) ($sales->payment_method ?? 'pending'));
                        $isEmiOrder = $orderPaymentMethodRaw === 'emi';

                        $prefillPaymentMethod = match ($orderPaymentMethodRaw) {
                            'cash_online', 'cash+bank', 'cash_bank', 'cash + bank', 'cash + online' => 'cash+online',
                            'debit card', 'upi', 'debit', 'scan' => 'online',
                            'emi' => 'emi',
                            default => $orderPaymentMethodRaw,
                        };

                        if (!$isEmiOrder) {
                            if ($prefillCashAmount > 0 && $prefillOnlineAmount > 0) {
                                $prefillPaymentMethod = 'cash+online';
                            } elseif ($prefillOnlineAmount > 0 || !empty($prefillBankId)) {
                                $prefillPaymentMethod = 'online';
                            } elseif ($prefillCashAmount > 0) {
                                $prefillPaymentMethod = 'cash';
                            } elseif ($prefillTotalPaid <= 0) {
                                $prefillPaymentMethod = 'pending';
                            }
                        }

                        if ($prefillPaymentMethod === 'cash' && $prefillCashAmount <= 0) {
                            $prefillCashAmount = $prefillTotalPaid;
                        }

                        if (in_array($prefillPaymentMethod, ['online', 'debit', 'scan'], true) && $prefillOnlineAmount <= 0) {
                            $prefillOnlineAmount = $prefillTotalPaid;
                        }

                        if ($prefillPaymentMethod === 'cash+online' && $prefillCashAmount <= 0 && $prefillOnlineAmount <= 0) {
                            $prefillOnlineAmount = $prefillTotalPaid;
                        }

                        $prefillPendingAmount = $prefillRemainingAmount > 0
                            ? min($prefillOrderTotal, $prefillRemainingAmount)
                            : max(0, $prefillOrderTotal - $prefillTotalPaid);
                        $prefillPaidType = $prefillPendingAmount > 0 ? '' : 'full';
                        $prefillPaymentStatus = $prefillPendingAmount <= 0 && $prefillTotalPaid > 0
                            ? 'completed'
                            : ($prefillTotalPaid > 0 ? 'partially' : 'pending');

                        if ($prefillPendingAmount > 0 && !$isEmiOrder) {
                            $prefillPaymentMethod = 'pending';
                        }

                        $displayCashAmount = $prefillPendingAmount > 0 ? 0 : $prefillCashAmount;
                        $displayOnlineAmount = $prefillPendingAmount > 0 ? 0 : $prefillOnlineAmount;

                        $storedEmiTenure = (string) ($sales->emi_tenure ?? '');
                        $presetEmiTenures = ['3', '6', '9', '12'];
                        $isCustomEmiTenure = $storedEmiTenure !== '' && !in_array($storedEmiTenure, $presetEmiTenures, true);
                        $displayEmiTenure = $isCustomEmiTenure ? 'custom' : $storedEmiTenure;
                        $displayEmiCustomTenure = $isCustomEmiTenure ? $storedEmiTenure : '';
                    @endphp
                    <div class="col-lg-3 col-sm-6 col-6">
                        <input type="hidden" name="update_selse_id" id="update_selse_id" value="{{ $update_id }}">
                        <div class="form-group">
                            <label class="d-flex align-items-center justify-content-between">
                                <span>Customer</span>
                                <span style="display:inline-flex;align-items:center;gap:5px;">
                                    <button type="button" id="openQuickAddCustomerBtn"
                                        title="Add New Customer"
                                        style="background:#ff9f43;border:none;color:#fff;border-radius:50%;width:22px;height:22px;display:inline-flex;align-items:center;justify-content:center;padding:0;font-size:16px;line-height:1;cursor:pointer;">
                                        +
                                    </button>
                                    <button type="button" id="openEditCustomerBtn"
                                        title="Edit Selected Customer"
                                        style="background:#1b2850;border:none;color:#fff;border-radius:50%;width:22px;height:22px;display:none;align-items:center;justify-content:center;padding:0;font-size:12px;line-height:1;cursor:pointer;">
                                        <i class="fas fa-pencil-alt" style="font-size:10px;"></i>
                                    </button>
                                </span>
                            </label>
                            <select name="customer_id" id="customer_id" class="form-control select2">
                                <option value="">Select Customer</option>
                                @foreach ($usernames as $user)
                                    <option value="{{ $user->id }}" data-phone="{{ $user->phone }}"
                                        {{ $sales->user_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}{{ $user->phone ? ' - ' . $user->phone : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Order Number</label>
                            <div class="input-groupicon">
                                <input type="text" id="order_number" class="form-control" placeholder="Order number"
                                    name="order_number" value="{{ $sales->order_number ?? '' }}">
                                <span class="text-danger" id="order_number_error" style="display:none;"></span>
                            </div>
                        </div>
                    </div> <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Customer Number</label>
                            <div class="input-groupicon">
                                <input type="tel" id="customer_phone" class="form-control" placeholder="Customer number"
                                    name="customer_phone" value="{{ $sales->user->phone ?? '' }}" readonly>
                                <span class="error_customerphone"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Order Date</label>
                            <div class="input-groupicon">
                                <input type="hidden" id="order_date" name="order_date" value="{{ \Carbon\Carbon::parse($sales->created_at)->format('Y-m-d') }}">
                                <input type="text" class="datetimepicker form-control" id="order_date_display"
                                    value="{{ \Carbon\Carbon::parse($sales->created_at)->format('d/m/Y') }}" required>
                                <a class="addonset">
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/calendars.svg' }}" alt="img">
                                </a>
                            </div>
                        </div>
                    </div>

                    @if (isset($staffList) && $staffList->count() > 0)
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Assign Staff</label>
                                <select id="assigned_staff_id" name="assigned_staff_id"
                                    class="form-control select2">
                                    <option value="">Select Staff (Optional)</option>
                                    @foreach ($staffList as $staff)
                                        <option value="{{ $staff->id }}"
                                            {{ (int) ($sales->staff_id ?? 0) === (int) $staff->id ? 'selected' : '' }}>
                                            {{ $staff->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    {{-- Order Type dropdown (visible to all roles) --}}
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Order Type</label>
                            <select id="order_type" name="order_type" class="form-control">
                                <option value="self_pickup"
                                    {{ ($sales->order_type ?? 'self_pickup') === 'self_pickup' ? 'selected' : '' }}>
                                    Self Pickup
                                </option>
                                <option value="delivery"
                                    {{ ($sales->order_type ?? '') === 'delivery' ? 'selected' : '' }}>
                                    Delivery
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12 col-sm-12 col-12">
                        <div class="form-group">
                            <label>Product Name</label>
                            <div class="input-groupicon">
                                <select name="product_id[]" class="form-control product-select" multiple="multiple" style="width: 100%;">
                                    @php
                                        $user = auth()->user();
                                        $branchIdToUse = $user->role === 'staff' ? $user->branch_id : $user->id;
                                        $settings = \DB::table('settings')->where('branch_id', $branchIdToUse)->first();
                                        $currencySymbol = $settings->currency_symbol ?? '₹';
                                        $currencyPosition = $settings->currency_position ?? 'left';
                                        $selectedProductIds = old(
                                            'product_id',
                                            $sales->order_items->pluck('product_id')->toArray(),
                                        );
                                        $selectedProductIds = array_map('strval', $selectedProductIds);
                                    @endphp

                                    @foreach ($products as $product)
                                        @php
                                            $images = json_decode($product->images ?? '', true);
                                            $imageUrl =
                                                !empty($images) && isset($images[0])
                                                    ? env('ImagePath') . 'storage/' . $images[0]
                                                    : env('ImagePath') . '/admin/assets/img/product/noimage.png';
                                            $priceFormatted = number_format($product->price, 2);
                                            $displayPrice =
                                                $currencyPosition === 'right'
                                                    ? $priceFormatted . ' ' . $currencySymbol
                                                    : $currencySymbol . $priceFormatted;

                                            // Sales orders are saved with inclusive GST enabled.
                                            $gstOption = 'with_gst';
                                            $gstDetails = null;
                                            if ($product->product_gst) {
                                                try {
                                                    $gstDetails = json_decode($product->product_gst, true);
                                                } catch (\Exception $e) {
                                                    $gstDetails = null;
                                                }
                                            }
                                        @endphp

                                     <option value="{{ $product->id }}" data-image="{{ $imageUrl }}"
    data-price="{{ $product->price }}" data-name="{{ $product->name }}"
    data-category="{{ strtolower($product->category->name ?? '') }}"
    data-unit="{{ $product->unit->unit_name ?? 'N/A' }}"
    data-gst-option="{{ $gstOption }}"
    data-product-gst="{{ $product->product_gst ?? '[]' }}"
    data-discount="{{ $product->discount ?? 0 }}"
    data-stock="{{ $product->quantity ?? 999999 }}"
    {{ in_array((string) $product->id, $selectedProductIds) ? 'selected' : '' }}>
                                            {{ $product->name }} - {{ $displayPrice }}
                                            @if ($gstOption === 'with_gst')
                                                (With GST)
                                            @else
                                                (Without GST)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div class="addonset">
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/scanner.svg' }}"
                                        alt="img">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="table-responsive mb-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>IMEI No</th>
                                    <th>Unit</th>
                                    <th>QTY</th>
                                    <th>Price</th>
                                    <th>Discount %</th>
                                    <th>GST Details</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="product-table-body">
                                @php
                                    $currencySymbol = $settings->currency_symbol ?? '₹';
                                    $currencyPosition = $settings->currency_position ?? 'left';
                                @endphp

                                @forelse ($sales->order_items as $index => $item)
                                    @php
                                        $product = $item->product;
                                        $gstOption = $product->gst_option ?? 'with_gst';

                                        // Get GST details from order_item, not from product
                                        $gstDetails = null;
                                        $productGstTotal = $item->product_gst_total ?? 0;

                                        if (!empty($item->product_gst_details)) {
                                            if (is_array($item->product_gst_details)) {
                                                $gstDetails = $item->product_gst_details;
                                            } else {
                                                try {
                                                    $gstDetails = json_decode($item->product_gst_details, true);
                                                    if (is_string($gstDetails)) {
                                                        $gstDetails = json_decode($gstDetails, true);
                                                    }
                                                } catch (\Exception $e) {
                                                    $gstDetails = null;
                                                }
                                            }
                                        }

                                        if (is_array($gstDetails) && isset($gstDetails['tax_name'])) {
                                            $gstDetails = [$gstDetails];
                                        }

                                        // Fallback to product GST if order_item doesn't have details
                                        if (empty($gstDetails) && !empty($product->product_gst) && $gstOption === 'with_gst') {
                                            if (is_array($product->product_gst)) {
                                                $gstDetails = $product->product_gst;
                                            } else {
                                                try {
                                                    $gstDetails = json_decode($product->product_gst, true);
                                                } catch (\Exception $e) {
                                                    $gstDetails = null;
                                                }
                                            }
                                        }

                                        // Only apply default 18% if the product is supposed to have GST but data is missing
                                        if ($gstOption === 'with_gst' && (empty($gstDetails) || !is_array($gstDetails))) {
                                            $gstDetails = [
                                                [
                                                    'tax_name' => 'GST',
                                                    'tax_rate' => 18,
                                                ],
                                            ];
                                        }

                                        // Calculate base total
                                        $baseTotal = $item->price * $item->quantity;
                                        $finalTotal = $item->total_amount;

                                        // Prepare GST data for data attribute
                                        $gstDataForAttribute = '[]';
                                        if (!empty($gstDetails) && is_array($gstDetails)) {
                                            $gstDataForAttribute = json_encode($gstDetails);
                                        }
                                    @endphp

                                    <tr data-product-id="{{ $item->product_id }}" data-gst-option="{{ $gstOption }}"
    data-product-gst="{{ $gstDataForAttribute }}"
    data-stock="{{ $item->product->quantity ?? 999999 }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td class="">
                                            @php
                                                $images = json_decode($item->product->images ?? '', true);
                                            @endphp
                                            <a class="product-img">
                                                @if (!empty($images) && isset($images[0]))
                                                    <img src="{{ env('ImagePath') . 'storage/' . $images[0] }}"
                                                        alt="product" width="40">
                                                @else
                                                    <img src="{{ env('ImagePath') . '/admin/assets/img/product/noimage.png' }}"
                                                        alt="No image" width="40">
                                                @endif
                                            </a>
                                            <a href="javascript:void(0);">{{ $item->product->name ?? 'N/A' }}</a>
                                            <span class="gst-badge {{ $gstOption === 'with_gst' ? 'with' : 'without' }}">
                                                {{ $gstOption === 'with_gst' ? 'With GST' : 'Without GST' }}
                                            </span>
                                        </td>
                                        <td data-label="IMEI">
                                            @php
                                                $catName = strtolower($item->product->category->name ?? '');
                                            @endphp
                                            @if (strpos($catName, 'mobile') !== false)
                                                <div class="serial-no-container" data-product-id="{{ $item->product_id }}" data-current-imei="{{ $item->imei_no }}">
                                                    <select class="form-control edit-imei-select" name="imei_no[{{ $item->product_id }}]" style="width: 150px;">
                                                        @if($item->imei_no)
                                                            <option value="{{ $item->imei_no }}">{{ $item->imei_no }}</option>
                                                        @else
                                                            <option value="">Select IMEI</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            @endif
                                        </td>
                                        <td data-label="Unit">
                                            {{ $item->product->unit->unit_name ?? 'N/A' }}
                                        </td>
                                        <td data-label="QTY">
                                            <input type="text" name="quantities[{{ $item->product_id }}]"
                                                class="form-control quantity-input"
                                                value="{{ number_format($item->quantity, 2, '.', '') }}" step="1"
                                                min="0" style="width: 80px;">
                                        </td>
                                        <td data-label="Price">
                                            <input type="text" name="prices[{{ $item->product_id }}]"
                                                class="form-control price-input"
                                                value="{{ number_format($item->price, 2, '.', '') }}" min="0"
                                                step="0.01" style="width: 90px;">
                                        </td>
                                        <td data-label="Discount %">
                                            <input type="text" name="discounts[{{ $item->product_id }}]"
                                                class="form-control discount-input"
                                                value="{{ number_format($item->discount_percentage ?? 0, 2, '.', '') }}"
                                                min="0" max="100" step="0.01" style="width: 80px;">
                                        </td>
                                        <td class="gst-details-cell" data-label="GST Details">
                                            @if ($gstDetails && is_array($gstDetails))
                                                <div class="product-gst-details">
                                                    @foreach ($gstDetails as $tax)
                                                        <small>
                                                            {{ $tax['tax_name'] ?? 'GST' }}: {{ $tax['tax_rate'] ?? 0 }}%
                                                            @if (isset($tax['tax_amount']))
                                                                ({{ number_format($tax['tax_amount'], 2) }})
                                                            @endif
                                                        </small>
                                                    @endforeach
                                                    @if ($productGstTotal > 0)
                                                        <small style="font-weight: bold; color: #333;">
                                                            GST Total:
                                                            {{ $currencyPosition === 'right' ? number_format($productGstTotal, 2) . $currencySymbol : $currencySymbol . number_format($productGstTotal, 2) }}
                                                        </small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">No GST</span>
                                            @endif
                                        </td>
                                        <td data-label="Total">
                                            <span class="total-amount">
                                                <div style="color:#ff9f43;">
                                                    <strong>Sub Total:</strong>
                                                    @if ($currencyPosition === 'right')
                                                        {{ number_format($baseTotal, 2) }}{{ $currencySymbol }}
                                                    @else
                                                        {{ $currencySymbol }}{{ number_format($baseTotal, 2) }}
                                                    @endif
                                                </div>

                                                @if ($gstOption === 'with_gst')
                                                    <div style="color:#007bff;">
                                                        <strong>GST Included:</strong>
                                                        @if ($currencyPosition === 'right')
                                                            {{ number_format($baseTotal + $productGstTotal, 2) }}{{ $currencySymbol }}
                                                        @else
                                                            {{ $currencySymbol }}{{ number_format($baseTotal + $productGstTotal, 2) }}
                                                        @endif
                                                    </div>
                                                @endif

                                                @php
                                                    $discountAmt =
                                                        $item->price *
                                                        $item->quantity *
                                                        (($item->discount_percentage ?? 0) / 100);
                                                @endphp

                                                @if ($discountAmt > 0)
                                                    <div style="color:red;">
                                                        <strong>Discount:</strong> -
                                                        @if ($currencyPosition === 'right')
                                                            {{ number_format($discountAmt, 2) }}{{ $currencySymbol }}
                                                        @else
                                                            {{ $currencySymbol }}{{ number_format($discountAmt, 2) }}
                                                        @endif
                                                    </div>
                                                @endif

                                                <div
                                                    style="font-weight:bold; margin-top:4px; border-top:1px solid #ddd; padding-top:3px;color:green;">
                                                    Final Total:
                                                    @if ($currencyPosition === 'right')
                                                        {{ number_format($item->total_amount, 2) }}{{ $currencySymbol }}
                                                    @else
                                                        {{ $currencySymbol }}{{ number_format($item->total_amount, 2) }}
                                                    @endif
                                                </div>
                                            </span>
                                        </td>
                                        <td data-label="Action">
                                            <a href="javascript:void(0);" class="delete-set">
                                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}"
                                                    alt="svg">
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="no-products-row">
                                        <td colspan="7" class="text-center">No products selected</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>



                <div class="row">

                    <div class="col-lg-3 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Shipping</label>
                            <input type="number" class="form-control" name="shipping" id="shipping-input"
                                value="{{ $sales->shipping ?? 0 }}" min="0" step="0.01">
                            <div id="shipping-error" class="text-danger mt-1" style="display:none;"></div>
                        </div>
                    </div>

                    @if ((bool) ($setting->tds_apply ?? false))
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>TDS Percentage (%)</label>
                                <input type="number" class="form-control" name="tds_percentage"
                                    id="tds-percentage-input"
                                    value="{{ number_format((float) ($sales->tds_percentage ?? 0), 2, '.', '') }}"
                                    min="0" max="100" step="0.01">
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>TDS Amount</label>
                                <input type="number" class="form-control" name="tds_amount" id="tds-amount-input"
                                    value="{{ number_format((float) ($sales->tds_amount ?? 0), 2, '.', '') }}"
                                    min="0" step="0.01" readonly>
                            </div>
                        </div>
                    @endif

                    <div class="col-lg-3 col-sm-6 col-6" id="payment_method_col">
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select class="select form-control" name="payment_method" id="payment_method">
                                <option value="pending" {{ $prefillPaymentMethod === 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>
                                <option value="cash" {{ $prefillPaymentMethod === 'cash' ? 'selected' : '' }}>Cash
                                </option>
                                <option value="online" {{ in_array($prefillPaymentMethod, ['online', 'debit', 'scan'], true) ? 'selected' : '' }}>Online
                                </option>
                                <option value="cash+online" {{ $prefillPaymentMethod === 'cash+online' ? 'selected' : '' }}>
                                    Cash+Online
                                </option>
                                <option value="emi" {{ $prefillPaymentMethod === 'emi' ? 'selected' : '' }}>EMI
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-6 d-none" id="payment_status_col">
                        <div class="form-group">
                            <label>Payment Status</label>
                            <select class="select form-control" name="status" id="payment_status">
                                <option value="pending" {{ $prefillPaymentStatus === 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>
                                <option value="partially" {{ $prefillPaymentStatus === 'partially' ? 'selected' : '' }}>
                                    Partially Paid
                                </option>
                                <option value="completed" {{ $prefillPaymentStatus === 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="row d-none" id="emi_details_row">
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Down Payment (Optional)</label>
                            <input type="text" class="form-control" name="emi_down_payment" id="emi_down_payment"
                                value="{{ old('emi_down_payment', $sales->emi_down_payment ?? 0) }}">
                            <small class="text-danger d-none" id="emi_down_payment_error"></small>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Loan Amount</label>
                            <input type="text" class="form-control" name="emi_loan_amount" id="emi_loan_amount"
                                value="{{ old('emi_loan_amount', $sales->emi_loan_amount ?? 0) }}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>EMI Tenure</label>
                            <select class="select form-control" name="emi_tenure" id="emi_tenure">
                                <option value="">Select Tenure</option>
                                <option value="3" {{ $displayEmiTenure === '3' ? 'selected' : '' }}>3 Months</option>
                                <option value="6" {{ $displayEmiTenure === '6' ? 'selected' : '' }}>6 Months</option>
                                <option value="9" {{ $displayEmiTenure === '9' ? 'selected' : '' }}>9 Months</option>
                                <option value="12" {{ $displayEmiTenure === '12' ? 'selected' : '' }}>12 Months</option>
                                <option value="custom" {{ $displayEmiTenure === 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                            <small class="text-danger d-none" id="emi_tenure_error"></small>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6 d-none" id="emi_custom_tenure_col">
                        <div class="form-group">
                            <label>Custom Tenure (Months)</label>
                            <input type="number" class="form-control" name="emi_custom_tenure" id="emi_custom_tenure"
                                min="1" max="120" step="1"
                                value="{{ old('emi_custom_tenure', $displayEmiCustomTenure) }}">
                            <small class="text-danger d-none" id="emi_custom_tenure_error"></small>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Interest Rate (%) <small class="text-muted">Optional</small></label>
                            <input type="text" class="form-control" name="emi_interest_rate" id="emi_interest_rate"
                                value="{{ old('emi_interest_rate', $sales->emi_interest_rate ?? 0) }}">
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Monthly EMI</label>
                            <input type="text" class="form-control" name="emi_monthly_amount" id="emi_monthly_amount"
                                value="{{ old('emi_monthly_amount', $sales->emi_monthly_amount ?? 0) }}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Aadhar Number</label>
                            <input type="text" class="form-control" name="emi_aadhar_number" id="emi_aadhar_number"
                                value="{{ old('emi_aadhar_number', $sales->emi_aadhar_number ?? '') }}">
                            <small class="text-danger d-none" id="emi_aadhar_error"></small>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label>DO ID <small class="text-muted"></small></label>
                            <input type="text" class="form-control" name="emi_do_id" id="emi_do_id"
                                value="{{ old('emi_do_id', $sales->emi_do_id ?? '') }}" placeholder="DO ID">
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>PAN Number <small class="text-muted">Optional</small></label>
                            <input type="text" class="form-control" name="emi_pan_number" id="emi_pan_number"
                                value="{{ old('emi_pan_number', $sales->emi_pan_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Guarantor Name <small class="text-muted">Optional</small></label>
                            <input type="text" class="form-control" name="emi_guarantor_name" id="emi_guarantor_name"
                                value="{{ old('emi_guarantor_name', $sales->emi_guarantor_name ?? '') }}">
                        </div>
                    </div>
                </div>

                <div class="row" id="payment_details_row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-6 " id="paid_type_col">
                                <div class="form-group">
                                    <label>Paid Type</label>
                                    <select class="select form-control" name="paid_type" id="paid_type">
                                        <option value="" {{ $prefillPaidType === '' ? 'selected' : '' }}>Select Paid Type</option>
                                        <option value="full" {{ $prefillPaidType === 'full' ? 'selected' : '' }}>Fully</option>
                                        <option value="partial" {{ $prefillPaidType === 'partial' ? 'selected' : '' }}>Partially</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6 " id="bank_container">
                            <div class="form-group">
                                <div class="bank-label-row">
                                    <label for="bank_id" class="mb-0">Select Bank <small class="text-muted">Optional</small></label>
                                    <button type="button" class="bank-add-btn" id="openAddBankModal">Add Bank</button>
                                </div>
                                    <select class="form-control" id="bank_id" name="bank_id" style="width: 100%;">
                                        <option value="">Select Bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}" {{ (int) $prefillBankId === (int) $bank->id ? 'selected' : '' }}>
                                                {{ $bank->bank_name }}{{ $bank->account_number ? ' (' . $bank->account_number . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger d-none" id="bank_id_error"></small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6 " id="cash_amount_col">
                                <div class="form-group">
                                    <label id="cash_amount_label" for="cash_amount">Cash Amount</label>
                                    <input type="text" class="form-control" id="cash_amount" name="cash_amount"
                                        data-prefill="{{ number_format($prefillCashAmount, 2, '.', '') }}"
                                        value="{{ number_format($displayCashAmount, 2, '.', '') }}">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6 " id="online_amount_col">
                                <div class="form-group">
                                    <label id="online_amount_label" for="online_amount">Bank Amount</label>
                                    <input type="text" class="form-control" id="online_amount" name="online_amount"
                                        data-prefill="{{ number_format($prefillOnlineAmount, 2, '.', '') }}"
                                        value="{{ number_format($displayOnlineAmount, 2, '.', '') }}">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6 " id="pending_amount_col">
                                <div class="form-group">
                                    <label for="pending_amount">Pending Amount</label>
                                    <input type="text" class="form-control" id="pending_amount" name="pending_amount"
                                        data-prefill="{{ number_format($prefillPendingAmount, 2, '.', '') }}"
                                        data-paid-total="{{ number_format($prefillTotalPaid, 2, '.', '') }}"
                                        value="{{ number_format($prefillPendingAmount, 2, '.', '') }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row ">
                    <div class="col-lg-6 mb-3">
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="6" placeholder="Enter any remarks">{{ old('remarks', $sales->remarks ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-6 justify-content-end">
                            @php
                                // 1. Calculate Product Subtotal (Gross)
                                $productsSubtotal = $sales->order_items->sum(function ($item) {
                                    return $item->price * $item->quantity;
                                });

                                $totalProductGst = $sales->order_items->sum(function ($item) {
                                    return (float) ($item->product_gst_total ?? 0);
                                });

                                $productsBaseSubtotal = $totalProductGst > 0
                                    ? max(0, $productsSubtotal - $totalProductGst)
                                    : $productsSubtotal;

                                // Total product-level discounts
                                $totalProductDiscounts = $sales->order_items->sum(function ($item) {
                                    return $item->price * $item->quantity * (($item->discount_percentage ?? 0) / 100);
                                });

                                // Products Net Subtotal (after product discounts but before order discount)
                                $productsNetSubtotal = $productsSubtotal - $totalProductDiscounts;

                                // 2. Order Discount is now removed
                                $discountPercent = 0;
                                $discountAmount = 0;

                                // 3. After Discount (All Product discounts)
                                $productsAfterDiscount = $productsNetSubtotal;

                                // 4. Calculate Labour Subtotal
                                $labourSubtotal = 0;
                                if (isset($sales) && $sales->labour_items) {
                                    $labourSubtotal = $sales->labour_items->sum(function ($item) {
                                        return $item->qty * $item->price;
                                    });
                                }

                                // 5. Calculate Shipping
                                $shippingCost = $sales->shipping ?? 0;

                                // 5.1 Calculate TDS
                                $isTdsEnabled = (bool) ($setting->tds_apply ?? false);
                                $tdsPercentage = $isTdsEnabled ? (float) ($sales->tds_percentage ?? 0) : 0;
                                $storedTdsAmount = (float) ($sales->tds_amount ?? 0);

                                // 6. Calculate Taxes on (Products After Discount) only
                                $taxRates = $TaxRate;
                                $totalTaxAmount = 0;
                                $taxDetails = [];

                                foreach ($taxRates as $tax) {
                                    $taxAmount = ($productsAfterDiscount * $tax->tax_rate) / 100;
                                    $taxDetails[] = [
                                        'name' => $tax->tax_name,
                                        'rate' => $tax->tax_rate,
                                        'amount' => $taxAmount,
                                    ];
                                    $totalTaxAmount += $taxAmount;
                                }

                                // 7. Grand Total
                                $preTdsTotal =
                                    $productsAfterDiscount + $labourSubtotal + $shippingCost + $totalTaxAmount;
                                $tdsAmount = $isTdsEnabled
                                    ? ($storedTdsAmount > 0
                                        ? $storedTdsAmount
                                        : ($preTdsTotal * $tdsPercentage) / 100)
                                    : 0;
                                $grandTotal = $preTdsTotal - $tdsAmount;
                                $roundedGrandTotal = round($grandTotal);
                                $roundOffAmount = $roundedGrandTotal - $grandTotal;
                            @endphp

                            <!-- Labour Items Section -->
                            @if ($role == 'admin' || $role == 'staff' || $role == 'sub-admin')
                                <div class="col-lg-12 mb-3">
                                    <div class="select-split">
                                        <div class="select-group w-100">
                                            <hr>
                                            <h5 style=" font-weight: 600; font-size: 19px; ">Labour Items</h5>
                                            <div id="labour-items-container">
                                                @if (isset($sales->labour_items) && $sales->labour_items->count() > 0)
                                                    @foreach ($sales->labour_items as $index => $item)
                                                        <div class="row mb-2 labour-item-row">
                                                            <div class="col-lg-5 col-sm-4 col-4">
                                                                <select name="labour_item_id[]"
                                                                    class="form-control select2 select2-labour">
                                                                    <option value="">Select Labour Item</option>
                                                                    @foreach ($labourItems as $lItem)
                                                                        <option value="{{ $lItem->id }}"
                                                                            data-price="{{ $lItem->price }}"
                                                                            {{ $item->labour_item_id == $lItem->id ? 'selected' : '' }}>
                                                                            {{ $lItem->item_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-3 col-sm-3 col-3">
                                                                <input type="text" name="labour_qty[]"
                                                                    class="form-control labour-qty" placeholder="Qty"
                                                                    value="{{ $item->qty }}" min="0">
                                                            </div>
                                                            <div class="col-lg-3 col-sm-3 col-3">
                                                                <input type="text" name="labour_price[]"
                                                                    class="form-control labour-price" placeholder="Price"
                                                                    value="{{ $item->price }}" min="0">
                                                            </div>
                                                            <div class="col-lg-1 col-sm-2 col-2">
                                                                @if ($loop->last)
                                                                    <button type="button"
                                                                        class="btn btn-success add-labour-item">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                @else
                                                                    <button type="button"
                                                                        class="btn btn-danger remove-labour-item">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="row mb-2 labour-item-row">
                                                        <div class="col-lg-5 col-sm-4 col-4">
                                                            <select name="labour_item_id[]"
                                                                class="form-control select2 select2-labour">
                                                                <option value="">Select Labour Item</option>
                                                                @foreach ($labourItems as $lItem)
                                                                    <option value="{{ $lItem->id }}"
                                                                        data-price="{{ $lItem->price }}">
                                                                        {{ $lItem->item_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-3 col-sm-3 col-3">
                                                            <input type="number" name="labour_qty[]"
                                                                class="form-control labour-qty" placeholder="Qty"
                                                                value="1" min="0">
                                                        </div>
                                                        <div class="col-lg-3 col-sm-3 col-3">
                                                            <input type="number" name="labour_price[]"
                                                                class="form-control labour-price" placeholder="Price"
                                                                value="0" min="0">
                                                        </div>
                                                        <div class="col-lg-1 col-sm-2 col-2">
                                                            <button type="button"
                                                                class="btn btn-success add-labour-item">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <!-- End Labour Items Section -->

                            <div class="total-order w-100 max-widthauto m-auto mb-4">
                                <ul>
                                    <li class="subtotal">
                                        <h4>Total (Products)</h4>
                                        <h5>
                                            @if ($setting->currency_position === 'right')
                                                <span
                                                    id="subtotal-display">{{ number_format($productsBaseSubtotal, 2) }}</span>{{ $setting->currency_symbol ?? '₹' }}
                                            @else
                                                {{ $setting->currency_symbol ?? '₹' }}<span
                                                    id="subtotal-display">{{ number_format($productsBaseSubtotal, 2) }}</span>
                                            @endif
                                        </h5>
                                    </li>

                                    <li class="total-gst" style="display: none;">
                                        <h4>Total GST</h4>
                                        <h5>
                                            @if ($setting->currency_position === 'right')
                                                <span
                                                    id="total-gst-amount">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                            @else
                                                {{ $setting->currency_symbol ?? '₹' }}<span
                                                    id="total-gst-amount">0.00</span>
                                            @endif
                                        </h5>
                                    </li>
                                    <li class="product-discount"
                                        @if ($totalProductDiscounts <= 0) style="display:none;" @endif>
                                        <h4>Discounts</h4>
                                        <h5>
                                            @if ($setting->currency_position === 'right')
                                                <span
                                                    id="product-discount-total-display">{{ number_format($totalProductDiscounts, 2) }}</span>{{ $setting->currency_symbol ?? '₹' }}
                                            @else
                                                {{ $setting->currency_symbol ?? '₹' }}<span
                                                    id="product-discount-total-display">{{ number_format($totalProductDiscounts, 2) }}</span>
                                            @endif
                                        </h5>
                                    </li>

                                    <!-- <li class="discount">
                                                    <h4>Discount</h4>
                                                    <h5>
                                                        <span
                                                            id="discount-percent">{{ number_format($discountPercent, 2) }}</span>%
                                                        (
                                                        @if ($setting->currency_position === 'right')
                                                        <span
                                                                                                                    id="discount-amount">{{ number_format($discountAmount, 2) }}</span>{{ $setting->currency_symbol ?? '₹' }}
                                                    @else
                                                        {{ $setting->currency_symbol ?? '₹' }}<span
                                                                                                                    id="discount-amount">{{ number_format($discountAmount, 2) }}</span>
                                                        @endif
                                                                                                            )
                                                    </h5>
                                                </li> -->

                                    <li class="after-discount"
                                        @if ($totalProductDiscounts <= 0) style="display:none;" @endif>
                                        <h4>Sub Total</h4>
                                        <h5 id="after-discount-display">
                                            {{-- @if ($setting->currency_position === 'right')
                                                0.00{{ $setting->currency_symbol ?? '₹' }} +
                                                0.00{{ $setting->currency_symbol ?? '₹' }} -
                                                0.00{{ $setting->currency_symbol ?? '₹' }} =
                                                0.00{{ $setting->currency_symbol ?? '₹' }}
                                            @else
                                                {{ $setting->currency_symbol ?? '₹' }}0.00 +
                                                {{ $setting->currency_symbol ?? '₹' }}0.00 -
                                                {{ $setting->currency_symbol ?? '₹' }}0.00 =
                                                {{ $setting->currency_symbol ?? '₹' }}0.00
                                            @endif --}}
                                        </h5>
                                    </li>



                                    {{-- <div class="tax-section">
                                        @foreach ($TaxRate as $tax)
                                            <li>
                                                <h4>{{ $tax->tax_name }}</h4>
                                                <h5>{{ number_format($tax->tax_rate, 2) }}% (
                                                    @if ($setting->currency_position === 'right')
                                                        <span class="tax-amount"
                                                            data-rate="{{ $tax->tax_rate }}">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                                    @else
                                                        {{ $setting->currency_symbol ?? '₹' }}<span class="tax-amount"
                                                            data-rate="{{ $tax->tax_rate }}">0.00</span>
                                                    @endif
                                                    )
                                                </h5>
                                            </li>
                                        @endforeach
                                    </div> --}}
                                    <li class="labour-cost"
                                        @if ($labourSubtotal <= 0) style="display:none;" @endif>
                                        <h4>Labour Cost</h4>
                                        <h5>
                                            @if ($setting->currency_position === 'right')
                                                <span
                                                    id="labour-cost-display">{{ number_format($labourSubtotal, 2) }}</span>{{ $setting->currency_symbol ?? '₹' }}
                                            @else
                                                {{ $setting->currency_symbol ?? '₹' }}<span
                                                    id="labour-cost-display">{{ number_format($labourSubtotal, 2) }}</span>
                                            @endif
                                        </h5>
                                    </li>

                                    <li class="shipping-cost"
                                        @if ($shippingCost <= 0) style="display:none;" @endif>
                                        <h4>Shipping Cost</h4>
                                        <h5>
                                            @if ($setting->currency_position === 'right')
                                                <span
                                                    id="shipping-cost-display">{{ number_format($shippingCost, 2) }}</span>{{ $setting->currency_symbol ?? '₹' }}
                                            @else
                                                {{ $setting->currency_symbol ?? '₹' }}<span
                                                    id="shipping-cost-display">{{ number_format($shippingCost, 2) }}</span>
                                            @endif
                                        </h5>
                                    </li>

                                    <li class="tds-summary"
                                        @if (!$isTdsEnabled) style="display:none;" @endif>
                                        <h4>TDS (<span
                                                id="tds-percentage-display">{{ number_format($tdsPercentage, 2) }}</span>%)
                                        </h4>
                                        <h5>
                                            @if ($setting->currency_position === 'right')
                                                <span
                                                    id="tds-amount-display">-{{ number_format(abs($tdsAmount), 2) }}</span>{{ $setting->currency_symbol ?? 'â‚¹' }}
                                            @else
                                                {{ $setting->currency_symbol ?? 'â‚¹' }}<span
                                                    id="tds-amount-display">-{{ number_format(abs($tdsAmount), 2) }}</span>
                                            @endif
                                        </h5>
                                    </li>



                                    <li class="round-off d-none">
                                        <h4>Round Off</h4>
                                        <h5>
                                            @if ($setting->currency_position === 'right')
                                                <span
                                                    id="round-off-display">{{ number_format($roundOffAmount, 2) }}</span>{{ $setting->currency_symbol ?? 'â‚¹' }}
                                            @else
                                                {{ $setting->currency_symbol ?? 'â‚¹' }}<span
                                                    id="round-off-display">{{ number_format($roundOffAmount, 2) }}</span>
                                            @endif
                                        </h5>
                                    </li>
                                    <li class="total">
                                        <h4>Grand Total</h4>
                                        <h5>
                                            @if ($setting->currency_position === 'right')
                                                <span id="grand-total">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                            @else
                                                {{ $setting->currency_symbol ?? '₹' }}<span id="grand-total">0.00</span>
                                            @endif
                                        </h5>
                                    </li>
                                    <li class="advance-paid-row" style="display: {{ ($sales->quotation_status ?? 'sales') === 'advance_receipt' ? 'flex' : 'none' }}; border-top: 1px solid #e9ecef; padding-top: 10px; margin-top: 10px; align-items: center; justify-content: space-between;">
                                        <h4 style="font-weight: 500; font-size: 14px; color: #5B6670;">Advance Paid</h4>
                                        <h5 style="font-weight: 600; font-size: 14px; color: #5B6670;">
                                            @if ($setting->currency_position === 'right')
                                                <span id="advance-paid-display">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                            @else
                                                {{ $setting->currency_symbol ?? '₹' }}<span id="advance-paid-display">0.00</span>
                                            @endif
                                        </h5>
                                    </li>
                                    <li class="remaining-balance-row total" style="display: {{ ($sales->quotation_status ?? 'sales') === 'advance_receipt' ? 'flex' : 'none' }}; border-top: none;">
                                        <h4>Remaining Balance</h4>
                                        <h5>
                                            @if ($setting->currency_position === 'right')
                                                <span id="remaining-balance-display">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                            @else
                                                {{ $setting->currency_symbol ?? '₹' }}<span id="remaining-balance-display">0.00</span>
                                            @endif
                                        </h5>
                                    </li>
                                </ul>
                            </div>

                            {{-- <div class="row justify-content-end">
                    <div class="col-lg-6">
                        <div class="total-order w-100 max-widthauto m-auto mb-4">
                            <ul>
                                <li class="subtotal">
                                    <h4>Subtotal</h4>
                                    <h5>
                                        @if ($setting->currency_position === 'right')
                                            <span id="subtotal-display">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                        @else
                                            {{ $setting->currency_symbol ?? '₹' }}<span id="subtotal-display">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li class="discount">
                                    <h4>Discount</h4>
                                    <h5>
                                        <span id="discount-percent">0.00</span>%
                                        (
                                        @if ($setting->currency_position === 'right')
                                            <span id="discount-amount">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                        @else
                                            {{ $setting->currency_symbol ?? '₹' }}<span id="discount-amount">0.00</span>
                                        @endif
                                        )
                                    </h5>
                                </li>

                                <li class="after-discount">
                                    <h4>After Discount</h4>
                                    <h5>
                                        @if ($setting->currency_position === 'right')
                                            <span
                                                id="after-discount-display">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                        @else
                                            {{ $setting->currency_symbol ?? '₹' }}<span
                                                id="after-discount-display">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li class="total-gst" style="display: none;">
                                    <h4>Total GST</h4>
                                    <h5>
                                        @if ($setting->currency_position === 'right')
                                            <span id="total-gst-amount">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                        @else
                                            {{ $setting->currency_symbol ?? '₹' }}<span id="total-gst-amount">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li class="total">
                                    <h4>Grand Total</h4>
                                    <h5>
                                        @if ($setting->currency_position === 'right')
                                            <span id="grand-total">0.00</span>{{ $setting->currency_symbol ?? '₹' }}
                                        @else
                                            {{ $setting->currency_symbol ?? '₹' }}<span id="grand-total">0.00</span>
                                        @endif
                                    </h5>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div> --}}

                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-submit me-2" id="update-order-btn">
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true" id="btn-loader"></span>
                                    <span id="btn-text">Update Order</span>
                                </button>
                                <a href="{{ route('sales.list') }}" class="btn btn-cancel">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ===== QUICK ADD CUSTOMER MODAL ===== --}}
                <div class="modal fade" id="quickAddCustomerModal" tabindex="-1" aria-labelledby="quickAddCustomerModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form id="quickAddCustomerForm" enctype="multipart/form-data">
                                <input type="hidden" id="qac_edit_id" value="">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="quickAddCustomerModalLabel">Add New Customer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                            <label>Customer Name <span class="text-danger">*</span></label>
                                            <input type="text" name="qac_customer_name" id="qac_customer_name" class="form-control" maxlength="80" placeholder="Customer Name">
                                            <div class="text-danger small qac-error" id="qac_error_customer_name"></div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                            <label>Company Name</label>
                                            <input type="text" name="qac_company_name" id="qac_company_name" class="form-control" maxlength="80" placeholder="Company Name">
                                            <div class="text-danger small qac-error" id="qac_error_company_name"></div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                            <label>Phone <span class="text-danger">*</span></label>
                                            <input type="text" name="qac_phone" id="qac_phone" class="form-control" maxlength="10" placeholder="10-digit phone">
                                            <div class="text-danger small qac-error" id="qac_error_phone"></div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                            <label>Alternate Phone</label>
                                            <input type="text" name="qac_alternate_phone" id="qac_alternate_phone" class="form-control" maxlength="10" placeholder="10-digit phone (optional)">
                                            <div class="text-danger small qac-error" id="qac_error_alternate_phone"></div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                            <label>Email</label>
                                            <input type="email" name="qac_email" id="qac_email" class="form-control" placeholder="Email">
                                            <div class="text-danger small qac-error" id="qac_error_email"></div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                            <label>GST Number</label>
                                            <div style="position:relative;">
                                                <input type="text" name="qac_gst_number" id="qac_gst_number" class="form-control" maxlength="15" placeholder="GST Number (auto-fill)" autocomplete="off">
                                                <span id="qac-gst-loader" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);display:none;color:#ff9f43;"><i class="fas fa-spinner fa-spin"></i></span>
                                            </div>
                                            <div class="text-danger small qac-error" id="qac_error_gst_number"></div>
                                            <div id="qac-gst-msg" style="font-size:12px;margin-top:3px;min-height:16px;"></div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                            <label>PAN Number</label>
                                            <input type="text" name="qac_pan_number" id="qac_pan_number" class="form-control" maxlength="10" placeholder="PAN Number">
                                            <div class="text-danger small qac-error" id="qac_error_pan_number"></div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                            <label>State Code</label>
                                            <input type="text" name="qac_state_code" id="qac_state_code" class="form-control" placeholder="e.g. 27">
                                            <div class="text-danger small qac-error" id="qac_error_state_code"></div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                            <label>State Name</label>
                                            <input type="text" name="qac_state_name" id="qac_state_name" class="form-control" placeholder="Auto-filled from state code" readonly>
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                            <label>Country</label>
                                            <input type="text" name="qac_country" id="qac_country" class="form-control" placeholder="Country">
                                        </div>
                                        <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                            <label>City</label>
                                            <input type="text" name="qac_city" id="qac_city" class="form-control" placeholder="City">
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12 mb-3">
                                            <label>Address</label>
                                            <textarea name="qac_address" id="qac_address" class="form-control" rows="2" placeholder="Address"></textarea>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12 mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="mb-0">Delivery Address</label>
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" id="qac_use_same_address">
                                                    <label class="form-check-label small mb-0" style="text-transform: none; font-weight: normal;" for="qac_use_same_address">Use Address</label>
                                                </div>
                                            </div>
                                            <textarea name="qac_delivery_address" id="qac_delivery_address" class="form-control" rows="2" placeholder="Delivery Address"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn text-white" id="qacSaveBtn" style="background-color:#ff9f43;">
                                        <span class="spinner-border spinner-border-sm d-none" id="qacBtnSpinner" role="status" aria-hidden="true"></span>
                                        <span id="qacSaveBtnText">Save Customer</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-cancel" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- ===== END QUICK ADD CUSTOMER MODAL ===== --}}

                <div class="modal fade" id="addBankModal" tabindex="-1" aria-labelledby="addBankModalLabel"
                    aria-hidden="true">
                        <div class="modal-content">
                            <form id="addBankForm">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addBankModalLabel">Add Bank</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="add_bank_name" class="form-label">Bank Name</label>
                                            <input type="text" class="form-control" id="add_bank_name" name="bank_name">
                                            <div class="text-danger small" id="addBankNameError"></div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="add_account_number" class="form-label">Account Number</label>
                                            <input type="text" class="form-control" id="add_account_number" name="account_number">
                                            <div class="text-danger small" id="addAccountNumberError"></div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="add_ifsc_code" class="form-label">IFSC Code</label>
                                            <input type="text" class="form-control" id="add_ifsc_code" name="ifsc_code">
                                            <div class="text-danger small" id="addIfscCodeError"></div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="add_branch_name" class="form-label">Branch Name</label>
                                            <input type="text" class="form-control" id="add_branch_name" name="branch_name">
                                            <div class="text-danger small" id="addBranchNameError"></div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="add_opening_balance" class="form-label">Opening Balance</label>
                                            <input type="number" class="form-control" id="add_opening_balance"
                                                name="opening_balance" min="0" step="0.01" value="0">
                                            <div class="text-danger small" id="addOpeningBalanceError"></div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="add_bank_status" class="form-label">Status</label>
                                            <select class="form-select" id="add_bank_status" name="status">
                                                <option value="1" selected>Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                            <div class="text-danger small" id="addBankStatusError"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn text-white" id="saveBankBtn"
                                        style="background-color: #ff9f43;">Save Bank</button>
                                    <button type="button" class="btn btn-secondary btn-cancel"
                                        data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endsection

            @push('js')
                <script>
                    $(document).ready(function() {
                        const currencySymbol = '{{ $setting->currency_symbol ?? '₹' }}';
                        const currencyPosition = '{{ $setting->currency_position ?? 'left' }}';
                        const isTdsEnabled = @json((bool) ($setting->tds_apply ?? false));
                        const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                        const addBankModalElement = document.getElementById('addBankModal');
                        const addBankModal = addBankModalElement && typeof bootstrap !== 'undefined' ?
                            new bootstrap.Modal(addBankModalElement) :
                            null;

                        let _isSyncingPayment = false;

                        function formatNumber(amount, decimals = 2) {
                            return parseFloat(amount).toLocaleString('en-US', {
                                minimumFractionDigits: decimals,
                                maximumFractionDigits: decimals
                            });
                        }

                        function formatCurrency(amount) {
                            const formatted = formatNumber(amount);
                            return currencyPosition === 'right' ?
                                formatted + currencySymbol :
                                currencySymbol + formatted;
                        }

                        function parseMoney(value) {
                            const normalized = String(value ?? '')
                                .replace(/,/g, '')
                                .replace(/[^0-9.-]/g, '')
                                .trim();
                            const parsed = parseFloat(normalized);
                            return Number.isFinite(parsed) ? parsed : 0;
                        }

                        function getGrandTotalValue() {
                            return parseMoney($('#grand-total').first().text());
                        }

                        function normalizePaymentMethod(value) {
                            const normalized = String(value || '').toLowerCase().trim();

                            if (['cash+online', 'cash_online', 'cash + online', 'cash+bank', 'cash_bank', 'cash + bank']
                                .includes(normalized)) {
                                return 'cash+online';
                            }

                            if (['online', 'debit', 'debit card', 'scan', 'upi'].includes(normalized)) {
                                return 'online';
                            }

                            if (normalized === 'cash') {
                                return 'cash';
                            }

                            if (normalized === 'emi') {
                                return 'emi';
                            }

                            return 'pending';
                        }

                        function setSelect2Value(selector, value) {
                            const $element = $(selector);
                            if (!$element.length) {
                                return;
                            }

                            $element.find('option').prop('selected', false);
                            $element.find(`option[value="${value}"]`).prop('selected', true);
                            $element.val(value);
                            if ($element.hasClass('select2-hidden-accessible')) {
                                $element.trigger('change.select2');
                                const selectedText = $element.find('option:selected').text();
                                $element.next('.select2-container').find('.select2-selection__rendered').text(selectedText);
                            }
                        }

                        function syncPaidTypeWithStatus() {
                            const status = $('#payment_status').val();
                            if (status === 'pending') {
                                setSelect2Value('#paid_type', '');
                            } else if (status === 'partially') {
                                setSelect2Value('#paid_type', 'partial');
                            } else if (status === 'completed') {
                                setSelect2Value('#paid_type', 'full');
                            }
                        }

                        function syncStatusWithPayment(pendingAmount, paidAmount, grandTotal) {
                            let status = 'pending';

                            if (pendingAmount <= 0 && paidAmount > 0) {
                                status = 'completed';
                            } else if (paidAmount > 0 && paidAmount < grandTotal) {
                                status = 'partially';
                            }

                            setSelect2Value('#payment_status', status);
                        }

                        // function formatPaymentAmountInput(value) {
                        //     const amount = parseMoney(value);
                        //     return amount > 0 ? amount.toFixed(2) : '';
                        // }
                        function formatPaymentAmountInput(value) {
    const amount = parseMoney(value);
    return amount > 0 ? amount.toFixed(2) : '';
}

// Format cash/online fields to 2 decimal places only on blur
$(document).on('blur', '#cash_amount, #online_amount', function() {
    const val = parseMoney($(this).val());
    if (val > 0) {
        $(this).val(val.toFixed(2));
    }
});
$(document).on('blur', '#tds-percentage-input', function() {
    const val = Math.max(0, Math.min(100, parseFloat($(this).val()) || 0));
    $(this).val(val.toFixed(2));
});

                        function normalizeDiscountInputValue(value) {
                            const normalized = String(value ?? '')
                                .replace(/[^0-9.]/g, '')
                                .trim();

                            if (normalized === '') {
                                return '';
                            }

                            const parts = normalized.split('.');
                            const sanitized = parts.length > 2
                                ? `${parts[0]}.${parts.slice(1).join('')}`
                                : normalized;
                            const parsed = parseFloat(sanitized);

                            if (!Number.isFinite(parsed)) {
                                return '';
                            }

                            return Math.max(0, Math.min(100, parsed)).toFixed(2);
                        }

                        function togglePaymentInputLayout() {
                            const isQuotation = $('#quotationToggle').is(':checked');
                            const initialQuotationStatus = @json($sales->quotation_status ?? 'sales');
                            const isAdvanceReceipt = initialQuotationStatus === 'advance_receipt';
                            
                            const method = normalizePaymentMethod($('#payment_method').val());
                            const $paymentColumns = $('#payment_details_row > .col-lg-12 > .row').children();
                            const shouldShowPaymentRow = !isQuotation && method !== 'pending';

                            $('#payment_method_col').toggle(!isQuotation);
                            $('#payment_status_col').show();
                            $('#payment_details_row').toggle(shouldShowPaymentRow);

                            const showEmi = shouldShowPaymentRow && method === 'emi';
                            if (showEmi) {
                                $('#emi_details_row').removeClass('d-none').show();
                            } else {
                                $('#emi_details_row').addClass('d-none').hide();
                            }

                            if (isAdvanceReceipt) {
                                $('#paid_type').val('partial');
                            }

                            $('#paid_type_col').toggle(shouldShowPaymentRow && method !== 'emi' && !isAdvanceReceipt);
                            $('#bank_container').toggle(shouldShowPaymentRow && (method === 'online' || method === 'cash+online' || method === 'emi'));
                            $('#cash_amount_col').toggle(shouldShowPaymentRow && (method === 'cash' || method === 'cash+online'));
                            $('#online_amount_col').toggle(shouldShowPaymentRow && (method === 'online' || method === 'cash+online'));
                            $('#pending_amount_col').toggle(shouldShowPaymentRow && method !== 'emi');

                            const showCustomTenure = showEmi && $('#emi_tenure').val() === 'custom';
                            if (showCustomTenure) {
                                $('#emi_custom_tenure_col').removeClass('d-none').show();
                            } else {
                                $('#emi_custom_tenure_col').addClass('d-none').hide();
                            }

                            if (isAdvanceReceipt) {
                                $('#cash_amount_label').text('Advance Amount Paid');
                                $('#online_amount_label').text('Advance Amount Paid');
                            } else {
                                $('#cash_amount_label').text(method === 'cash' ? 'Payment Amount' : 'Cash Amount');
                                $('#online_amount_label').text(method === 'online' ? 'Payment Amount' : 'Bank Amount');
                            }

                            $('#bank_id').prop('disabled', isQuotation || !(method === 'online' || method === 'cash+online' || method === 'emi'));
                            $('#cash_amount').prop('disabled', isQuotation || !(method === 'cash' || method === 'cash+online'));
                            $('#online_amount').prop('disabled', isQuotation || !(method === 'online' || method === 'cash+online'));
                            $('#emi_down_payment, #emi_loan_amount, #emi_tenure, #emi_interest_rate, #emi_monthly_amount, #emi_aadhar_number, #emi_do_id, #emi_pan_number, #emi_guarantor_name')
                                .prop('disabled', isQuotation || method !== 'emi');
                            $('#pending_amount').prop('disabled', true);
                            $('#paid_type').prop('disabled', !shouldShowPaymentRow);

                            if (method === 'emi') {
                                $('#payment_status_col').show();
                                $('#emi_down_payment, #emi_loan_amount, #emi_tenure, #emi_interest_rate, #emi_monthly_amount, #emi_aadhar_number, #emi_do_id, #emi_pan_number, #emi_guarantor_name')
                                    .prop('disabled', false);
                                $('#bank_id').prop('disabled', false);
                                $('#emi_guarantor_name').closest('.col-lg-3').after($('#bank_container'));
                            } else {
                                $('#emi_down_payment, #emi_loan_amount, #emi_monthly_amount, #emi_aadhar_number, #emi_do_id, #emi_pan_number, #emi_guarantor_name')
                                    .val('');
                                $('#emi_tenure').val('');
                                $('#emi_interest_rate').val('0');
                                $('#bank_id').val('').trigger('change');
                                $('#paid_type_col').after($('#bank_container'));
                            }

                            $paymentColumns.removeClass('col-lg-3 col-lg-4 col-lg-6 col-lg-12 d-none');

                            const visibleColumns = $paymentColumns.filter(':visible');
                            let paymentColumnClass = 'col-lg-3';

                            if (visibleColumns.length === 1) {
                                paymentColumnClass = 'col-lg-12';
                            } else if (visibleColumns.length === 2) {
                                paymentColumnClass = 'col-lg-6';
                            } else if (visibleColumns.length === 3) {
                                paymentColumnClass = 'col-lg-4';
                            }

                            visibleColumns.addClass(paymentColumnClass);
                        }

                        /**
                         * Recalculate EMI loan amount and monthly EMI live.
                         * Mirrors the POS EMI calculation logic.
                         * Runs immediately on any change to down payment, tenure, or interest rate.
                         */
                        function recalculateEmiFields() {
                            const grandTotal  = getGrandTotalValue();
                            const downPayment = Math.max(0, Math.min(parseMoney($('#emi_down_payment').val()), grandTotal));
                            const loanAmount  = Math.max(0, grandTotal - downPayment);
                            const tenure = $('#emi_tenure').val();
                            const isCustom = tenure === 'custom';
                            const months = isCustom
                                ? parseInt($('#emi_custom_tenure').val() || '0', 10)
                                : parseInt(tenure || '0', 10);
                            const annualRate = Math.max(0, parseMoney($('#emi_interest_rate').val()));

                            // Inline validation
                            $('#emi_custom_tenure_error').addClass('d-none').text('');
                            $('#emi_down_payment_error').addClass('d-none').text('');
                            if (isCustom) {
                                const rawCustom = ($('#emi_custom_tenure').val() || '').trim();
                                if (rawCustom !== '' && (months <= 0 || !Number.isInteger(months))) {
                                    $('#emi_custom_tenure_error').removeClass('d-none').text('Please enter a valid positive whole number.');
                                }
                            }
                            if (downPayment > 0 && downPayment >= grandTotal && grandTotal > 0) {
                                $('#emi_down_payment_error').removeClass('d-none').text('Down payment must be less than the grand total.');
                            }

                            let monthlyEmi = 0;
                            if (loanAmount > 0 && months > 0) {
                                if (annualRate === 0) {
                                    // Zero interest: simple division
                                    monthlyEmi = loanAmount / months;
                                } else {
                                    // Standard amortization: EMI = P×R×(1+R)^N / [(1+R)^N - 1]
                                    const R = annualRate / 12 / 100;
                                    const pow = Math.pow(1 + R, months);
                                    monthlyEmi = (loanAmount * R * pow) / (pow - 1);
                                }
                            } else if (loanAmount > 0) {
                                monthlyEmi = loanAmount; // no tenure set yet
                            }

                            $('#emi_loan_amount').val(loanAmount > 0 ? loanAmount.toFixed(2) : '');
                            $('#emi_monthly_amount').val(monthlyEmi > 0 ? monthlyEmi.toFixed(2) : '');
                        }

                        let _emiPageInitDone = false;

                        function calculatePaymentBreakdown() {
                            if (_isSyncingPayment) return;
                            _isSyncingPayment = true;
                            try {
                                const isQuotation = $('#quotationToggle').is(':checked');
                                const method = normalizePaymentMethod($('#payment_method').val());
                                const paidType = $('#paid_type').val();
                                const grandTotal = getGrandTotalValue();
                                const historicalPaidAmount = Math.min(
                                    grandTotal,
                                    parseMoney($('#pending_amount').attr('data-paid-total'))
                                );
                                const outstandingAmount = Math.max(0, grandTotal - historicalPaidAmount);
                                let cashAmount = parseMoney($('#cash_amount').val());
                                let onlineAmount = parseMoney($('#online_amount').val());
                                let additionalPaidAmount = 0;
                                let paidAmount = historicalPaidAmount;
                                let pendingAmount = outstandingAmount;

                                if (isQuotation) {
                                    cashAmount = 0;
                                    onlineAmount = 0;
                                    pendingAmount = grandTotal;
                                    paidAmount = 0;
                                } else if (method === 'pending' || !paidType) {
                                    cashAmount = 0;
                                    onlineAmount = 0;
                                    pendingAmount = outstandingAmount;
                                } else if (method === 'cash') {
                                    onlineAmount = 0;
                                    additionalPaidAmount = paidType === 'full'
                                        ? outstandingAmount
                                        : Math.min(cashAmount, outstandingAmount);
                                    cashAmount = additionalPaidAmount;
                                    pendingAmount = Math.max(outstandingAmount - additionalPaidAmount, 0);
                                    paidAmount = Math.min(historicalPaidAmount + additionalPaidAmount, grandTotal);
                                } else if (method === 'online') {
                                    cashAmount = 0;
                                    additionalPaidAmount = paidType === 'full'
                                        ? outstandingAmount
                                        : Math.min(onlineAmount, outstandingAmount);
                                    onlineAmount = additionalPaidAmount;
                                    pendingAmount = Math.max(outstandingAmount - additionalPaidAmount, 0);
                                    paidAmount = Math.min(historicalPaidAmount + additionalPaidAmount, grandTotal);
                                } else if (method === 'cash+online') {
                                    cashAmount = Math.min(cashAmount, outstandingAmount);

                                    if (paidType === 'full') {
                                        additionalPaidAmount = outstandingAmount;
                                        onlineAmount = Math.max(outstandingAmount - cashAmount, 0);
                                    } else {
                                        onlineAmount = Math.min(onlineAmount, Math.max(outstandingAmount - cashAmount, 0));
                                        additionalPaidAmount = Math.min(cashAmount + onlineAmount, outstandingAmount);
                                    }

                                    pendingAmount = Math.max(outstandingAmount - additionalPaidAmount, 0);
                                    paidAmount = Math.min(historicalPaidAmount + additionalPaidAmount, grandTotal);
                                } else if (method === 'emi') {
                                    const downPayment = Math.min(parseMoney($('#emi_down_payment').val()), outstandingAmount);
                                    const loanAmount = Math.max(grandTotal - downPayment, 0);
                                    const tenure = $('#emi_tenure').val();
                                    const months = tenure === 'custom'
                                        ? parseInt($('#emi_custom_tenure').val() || '0', 10)
                                        : parseInt(tenure || '0', 10);
                                    const annualRate = parseMoney($('#emi_interest_rate').val());
                                    let monthlyEmi = loanAmount;

                                    if (loanAmount > 0 && months > 0) {
                                        if (annualRate === 0) {
                                            monthlyEmi = loanAmount / months;
                                        } else {
                                            const R = annualRate / 12 / 100;
                                            const pow = Math.pow(1 + R, months);
                                            monthlyEmi = (loanAmount * R * pow) / (pow - 1);
                                        }
                                    } else if (loanAmount > 0) {
                                        monthlyEmi = loanAmount;
                                    }

                                    // On first page load, keep pre-filled DB values; only recalculate on user interaction
                                    if (_emiPageInitDone) {
                                        $('#emi_down_payment').val(formatPaymentAmountInput(downPayment));
                                        $('#emi_loan_amount').val(formatPaymentAmountInput(loanAmount));
                                        $('#emi_monthly_amount').val(formatPaymentAmountInput(monthlyEmi));
                                    }
                                    pendingAmount = Math.max(outstandingAmount - downPayment, 0);
                                    paidAmount = Math.min(historicalPaidAmount + downPayment, grandTotal);
                                }

                                $('#cash_amount').val(formatPaymentAmountInput(cashAmount));
                                $('#online_amount').val(formatPaymentAmountInput(onlineAmount));
                                $('#pending_amount').val(pendingAmount.toFixed(2));
                                
                                const initialQuotationStatus = @json($sales->quotation_status ?? 'sales');
                                if (initialQuotationStatus === 'advance_receipt') {
                                    $('.advance-paid-row, .remaining-balance-row').css('display', 'flex');
                                    $('#advance-paid-display').text(formatNumber(paidAmount, 2));
                                    $('#remaining-balance-display').text(formatNumber(pendingAmount, 2));
                                } else {
                                    $('.advance-paid-row, .remaining-balance-row').hide();
                                }

                                syncStatusWithPayment(pendingAmount, paidAmount, grandTotal);
                                togglePaymentInputLayout();
                            } finally {
                                _isSyncingPayment = false;
                            }
                        }

                        function resetAddBankForm() {
                            const form = document.getElementById('addBankForm');
                            if (form) {
                                form.reset();
                            }

                            $('#add_opening_balance').val('0');
                            $('#add_bank_status').val('1');
                            $('#addBankForm .text-danger').text('');
                        }

                        function validateAddBankForm() {
                            const formValues = {
                                bank_name: $('#add_bank_name').val().trim(),
                                account_number: $('#add_account_number').val().trim(),
                                ifsc_code: $('#add_ifsc_code').val().trim(),
                                branch_name: $('#add_branch_name').val().trim(),
                                opening_balance: $('#add_opening_balance').val().trim(),
                                status: $('#add_bank_status').val()
                            };
                            let hasError = false;

                            $('#addBankForm .text-danger').text('');

                            if (!formValues.bank_name) {
                                $('#addBankNameError').text('Bank name is required.');
                                hasError = true;
                            }

                            if (!formValues.account_number) {
                                $('#addAccountNumberError').text('Account number is required.');
                                hasError = true;
                            }

                            if (!formValues.ifsc_code) {
                                $('#addIfscCodeError').text('IFSC code is required.');
                                hasError = true;
                            }

                            if (!formValues.branch_name) {
                                $('#addBranchNameError').text('Branch name is required.');
                                hasError = true;
                            }

                            if (formValues.opening_balance === '') {
                                $('#addOpeningBalanceError').text('Opening balance is required.');
                                hasError = true;
                            } else if (parseMoney(formValues.opening_balance) < 0) {
                                $('#addOpeningBalanceError').text('Opening balance must be 0 or more.');
                                hasError = true;
                            }

                            if (!['0', '1'].includes(formValues.status)) {
                                $('#addBankStatusError').text('Status is required.');
                                hasError = true;
                            }

                            return !hasError;
                        }

                        function upsertBankOption(bank) {
                            if (!bank || !bank.id) {
                                return;
                            }

                            const bankId = String(bank.id);
                            const accountNumber = bank.account_number ? ` (${bank.account_number})` : '';
                            const label = `${bank.bank_name || 'Unnamed Bank'}${accountNumber}`;
                            const $bankSelect = $('#bank_id');
                            let $option = $bankSelect.find(`option[value="${bankId}"]`);

                            if ($option.length) {
                                $option.text(label);
                            } else {
                                $option = $('<option></option>').val(bankId).text(label);
                                $bankSelect.append($option);
                            }

                            setSelect2Value('#bank_id', bankId);
                        }

                        function getOptionData($option, key, fallback = null) {
                            if (!$option || !$option.length) {
                                return fallback;
                            }

                            const dataValue = $option.data(key);
                            if (dataValue !== undefined && dataValue !== null && dataValue !== '') {
                                return dataValue;
                            }

                            const attrValue = $option.attr(`data-${key}`);
                            return attrValue !== undefined && attrValue !== null && attrValue !== '' ? attrValue :
                                fallback;
                        }

                        function normalizeProductPrice(price) {
                            const normalizedPrice = parseFloat(price);
                            return Number.isFinite(normalizedPrice) ? normalizedPrice : null;
                        }

                        function fetchProductDetails(productId) {
                            return $.ajax({
                                url: `/api/getProductById/${productId}`,
                                type: 'GET',
                                headers: {
                                    "Authorization": "Bearer " + localStorage.getItem("authToken"),
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                        }

                        // Initialize Select2 with images and GST info
                        function formatProduct(product) {
                            if (!product.id) return product.text;

                            const $option = $(product.element);
                            const image = $option.data('image');
                            const name = $option.data('name');
                            const gstOption = $option.data('gst-option');
                            const productGst = $option.data('product-gst');

                            let gstBadge = '';
                            let gstDetails = '';

                            if (productGst) {
                                try {
                                    const gstData = JSON.parse(productGst);
                                    if (Array.isArray(gstData) && gstData.length > 0) {
                                        const totalRate = gstData.reduce((sum, tax) => sum + parseFloat(tax.tax_rate || 0), 0);
                                        gstBadge = `<span class="badge bg-success ms-2">GST: ${totalRate}%</span>`;
                                        gstDetails = `<div class="small text-muted">`;
                                        gstData.forEach(tax => {
                                            gstDetails += `${tax.tax_name || 'GST'}: ${tax.tax_rate}%<br>`;
                                        });
                                        gstDetails += `</div>`;
                                    }
                                } catch (e) {
                                    gstBadge = `<span class="badge bg-success ms-2">With GST</span>`;
                                }
                            } else {
                                gstBadge = `<span class="badge bg-success ms-2">GST: 18%</span>`;
                                gstDetails = `<div class="small text-muted">GST: 18%</div>`;
                            }

                            return $(
                                `<span>
                <img src="${image}" class="img-flag" style="width: 20px; margin-right: 10px;" />
                ${name}
                ${gstBadge}
                ${gstDetails}
            </span>`
                            );
                        }

                        $('.product-select').select2({
                            templateResult: formatProduct,
                            templateSelection: formatProduct,
                            closeOnSelect: false,
                            width: '100%'
                        });

                        $('.select2-labour').select2({
                            width: '100%'
                        });

                        // Initialize Customer Select2 — searches by name AND phone number
                        $('#customer_id').select2({
                            placeholder: 'Select Customer',
                            allowClear: true,
                            width: '100%',
                            matcher: function (params, data) {
                                if ($.trim(params.term) === '') return data;
                                if (typeof data.text === 'undefined') return null;

                                var term      = params.term.toLowerCase();
                                var text      = (data.text || '').toLowerCase();
                                var phone     = ($(data.element).data('phone') || '').toString().toLowerCase();
                                var normTerm  = term.replace(/[^0-9a-z]/g, '');
                                var normPhone = phone.replace(/[^0-9]/g, '');

                                if (text.indexOf(term) > -1)  return data;
                                if (phone.indexOf(term) > -1) return data;
                                if (normTerm && normPhone.indexOf(normTerm) > -1) return data;
                                return null;
                            }
                        });

                        // Auto-fill phone when customer is selected
                        $('#customer_id').on('change', function() {
                            var phone = $(this).find(':selected').data('phone');
                            $('#customer_phone').val(phone || '');
                        });

                        if ($.fn.select2) {
                            $('#payment_method, #payment_status, #paid_type, #bank_id').select2({
                                width: '100%'
                            });
                        }

                        function populateImeiSelects() {
                            $('.serial-no-container:not(.loaded)').each(function() {
                                const container = $(this);
                                const select = container.find('.edit-imei-select');
                                const productId = container.data('product-id');
                                const currentImei = container.attr('data-current-imei') || '';
                                container.addClass('loaded');

                                $.ajax({
                                    url: '/api/get-available-serials/' + productId,
                                    type: 'GET',
                                    headers: {
                                        "Authorization": "Bearer " + localStorage.getItem("authToken"),
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function(response) {
                                        if (response && response.status) {
                                            const serials = response.serials || [];
                                            let optionsHtml = '';
                                            if (!currentImei) {
                                                optionsHtml += '<option value="">Select IMEI</option>';
                                            } else if (!serials.includes(currentImei)) {
                                                // If current IMEI is not in the available list (because it's already bought in this order)
                                                optionsHtml += `<option value="${currentImei}" selected>${currentImei}</option>`;
                                            }
                                            
                                            serials.forEach(serial => {
                                                const isSelected = (serial === currentImei) ? 'selected' : '';
                                                optionsHtml += `<option value="${serial}" ${isSelected}>${serial}</option>`;
                                            });

                                            select.html(optionsHtml);
                                            if ($.fn.select2) {
                                                select.select2({ width: '150px' });
                                            }
                                        }
                                    },
                                    error: function(err) {
                                        console.error('Failed to fetch available serials', err);
                                    }
                                });
                            });
                        }

                        // Call once on page load for existing items
                        populateImeiSelects();

                        // Add product to table
                        function addProductToTable(productId, productName, productImage, productPrice, gstOption, productGst,
    existingGstDetails = null, unit = 'N/A', discount = 0, stock = 999999, category = '') {
                            const normalizedInitialPrice = normalizeProductPrice(productPrice);
                            const resolvedPrice = normalizedInitialPrice !== null ? normalizedInitialPrice : 0;
                            const rowCount = $('#product-table-body tr[data-product-id]').length + 1;
                            let gstBadge = '';
                            let gstDetailsHtml = '';
                            let gstDetails = [];

                            // Use existing GST details if available (from order_items), otherwise use product GST
                            if (existingGstDetails) {
                                gstDetails = existingGstDetails;
                            } else if (gstOption === 'with_gst' && productGst) {
                                if (typeof productGst === 'string') {
                                    try {
                                        gstDetails = JSON.parse(productGst);
                                    } catch (e) {
                                        gstDetails = [];
                                    }
                                } else if (Array.isArray(productGst)) {
                                    gstDetails = productGst;
                                } else {
                                    gstDetails = [];
                                }
                            }

                            if (!Array.isArray(gstDetails) || gstDetails.length === 0) {
                                gstDetails = [{
                                    tax_name: 'GST',
                                    tax_rate: 18
                                }];
                            }

                            const totalRate = gstDetails.reduce((sum, tax) => sum + parseFloat(tax.tax_rate || 0), 0);
                            gstBadge = `<span class="gst-badge with">With GST (${totalRate}%)</span>`;

                            gstDetailsHtml = '<div class="product-gst-details">';
                            gstDetails.forEach(tax => {
                                gstDetailsHtml += `<small>${tax.tax_name || 'GST'}: ${tax.tax_rate}%</small>`;
                            });
                            gstDetailsHtml += '</div>';

                            const newRow = `
<tr data-product-id="${productId}" data-gst-option="with_gst" data-product-gst='${JSON.stringify(gstDetails).replace(/'/g, "&#39;")}' data-stock="${stock || 999999}">
                <td>${rowCount}</td>
                <td class="">
                    <a class="product-img">
                        <img src="${productImage}" alt="product" width="40">
                    </a>
                    <a href="javascript:void(0);">${productName}</a>
                    ${gstBadge}
                </td>
                <td data-label="IMEI">
                    ${category.toLowerCase().includes('mobile') ? `
                        <div class="serial-no-container" data-product-id="${productId}" data-current-imei="">
                            <select class="form-control edit-imei-select" name="imei_no[${productId}]" style="width: 150px;">
                                <option value="">Select IMEI</option>
                            </select>
                        </div>
                    ` : ''}
                </td>
                <td data-label="Unit">${unit}</td>
                <td data-label="QTY">
                    <input type="text"
                           name="quantities[${productId}]"
                           class="form-control quantity-input"
                           value="1"
                           step="1"
                           min="0"
                           style="width: 80px;">
                </td>
                <td data-label="Price">
                    <input type="text"
                           name="prices[${productId}]"
                           class="form-control price-input"
                           value="${resolvedPrice.toFixed(2)}"
                           step="0.01"
                           min="0"
                           style="width: 90px;">
                </td>
                <td data-label="Discount %">
                    <input type="text"
                           name="discounts[${productId}]"
                           class="form-control discount-input"
                           value="${discount}"
                           step="0.01"
                           min="0"
                           max="100"
                           style="width: 80px;">
                </td>
                <td class="gst-details-cell" data-label="GST Details">
                    ${gstDetailsHtml || '<span class="text-muted">No GST</span>'}
                </td>
                <td data-label="Total">
                    <span class="total-amount">${formatNumber(resolvedPrice)}</span>
                </td>
                <td data-label="Action">
                    <a href="javascript:void(0);" class="delete-set">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}" alt="svg">
                    </a>
                </td>
            </tr>
        `;

                            $('#product-table-body #no-products-row').remove();
                            $('#product-table-body').append(newRow);
                            populateImeiSelects();

                            // Add event listeners
                            $('#product-table-body tr[data-product-id="' + productId + '"] .quantity-input').on('input', calculateAllTotals);
                            $('#product-table-body tr[data-product-id="' + productId + '"] .price-input').on('input', calculateAllTotals);
                            $('#product-table-body tr[data-product-id="' + productId + '"] .discount-input').on('input', calculateAllTotals);
                            $('#product-table-body tr[data-product-id="' + productId + '"] .delete-set').on('click', function() {
                                $(this).closest('tr').remove();
                                updateProductSelection();
                                toggleNoProductsMessage();
                                calculateAllTotals();
                            });
                        }

                        // Update product selection in dropdown
                        function updateProductSelection() {
                            const selectedProducts = $('#product-table-body tr[data-product-id]').map(function() {
                                return $(this).attr('data-product-id') || '';
                            }).get();
                            $('.product-select').val(selectedProducts).trigger('change');
                        }

                        // Toggle "no products" message
                        function toggleNoProductsMessage() {
                            if ($('#product-table-body tr[data-product-id]').length === 0) {
                                $('#product-table-body #no-products-row').remove(); // ensure no duplicates
                                $('#product-table-body').append(
                                    '<tr id="no-products-row"><td colspan="10" class="text-center">No products selected</td></tr>'
                                );
                            } else {
                                $('#product-table-body #no-products-row').remove();
                            }
                        }

                        // Calculate all totals including product-wise GST
                        function calculateAllTotals() {
                            let grossSubtotal = 0;
                            let totalPerItemDiscount = 0;
                            let netSubtotal = 0;
                            let totalGst = 0;
                            const gstOption = $('input[name="gst_option"]:checked').val();
                            const hasGlobalGst = gstOption === 'with_gst';

                            // Calculate for each product
                            $('#product-table-body tr[data-product-id]').each(function() {
                                const $row = $(this);
                                const quantity = parseFloat($row.find('.quantity-input').val()) || 0;
                                const price = parseFloat($row.find('.price-input').val()) || 0;
                                const discountPercent = Math.max(0, Math.min(100, parseFloat($row.find('.discount-input').val()) || 0));
                                const productGstOption = $row.data('gst-option');
                                let productGstData = $row.data('product-gst') || [];

                                // If productGstData is a string, parse it
                                if (typeof productGstData === 'string' && productGstData.trim() !== '') {
                                    try {
                                        productGstData = JSON.parse(productGstData);
                                    } catch (e) {
                                        // console.error('Error parsing GST data', e);
                                        productGstData = [];
                                    }
                                }

                                if (!Array.isArray(productGstData)) {
                                    productGstData = [];
                                }

                                // ── GST-INCLUSIVE PRICING ──────────────────────────────────────
                                // The product price ALREADY includes GST (same as POS behaviour).
                                // e.g. price ₹100, GST 18% → GST portion = ₹18, base = ₹82,
                                //      final total = ₹100  (NOT ₹118).
                                // ──────────────────────────────────────────────────────────────

                                // Total line amount (GST-exclusive base price × qty)
                                const subTotal = quantity * price;

                                // GST is calculated on SubTotal
                                let productGstAmount = 0;
                                let baseAmount = subTotal;
                                let totalGstRate = 0;
                                if (hasGlobalGst && productGstOption === 'with_gst' && Array.isArray(productGstData) &&
                                    productGstData.length > 0) {
                                    totalGstRate = productGstData.reduce((sum, tax) => sum + (parseFloat(tax.tax_rate || 0)), 0);
                                    productGstAmount = subTotal * (totalGstRate / 100);
                                    totalGst += productGstAmount;
                                }

                                // Inclusive line total = Base + GST
                                const inclusiveLineTotal = subTotal + productGstAmount;

                                // Discount is applied on the inclusive total
                                const itemDiscountAmount = inclusiveLineTotal * (discountPercent / 100);

                                // Final total that the customer pays
                                const rowFinalTotal = inclusiveLineTotal - itemDiscountAmount;

                                grossSubtotal        += inclusiveLineTotal;
                                totalPerItemDiscount += itemDiscountAmount;
                                netSubtotal          += rowFinalTotal;

                                if (hasGlobalGst && productGstOption === 'with_gst' && productGstAmount > 0) {
                                    // Update GST details cell
                                    const $gstCell = $row.find('.gst-details-cell');
                                    let gstHtml = '<div class="product-gst-details">';
                                    productGstData.forEach(tax => {
                                        const taxRate = parseFloat(tax.tax_rate || 0);
                                        const taxAmount = totalGstRate > 0 ? productGstAmount * taxRate / totalGstRate : 0;
                                        gstHtml +=
                                            `<small>${tax.tax_name || 'GST'}: ${taxRate}% (${formatNumber(taxAmount)})</small>`;
                                    });
                                    gstHtml +=
                                        `<small style="font-weight: bold;">Total GST: ${formatNumber(productGstAmount)}</small>`;
                                    gstHtml += '</div>';
                                    $gstCell.html(gstHtml);

                                    // Row total display — matches POS style
                                    let rowTotalHtml = `
                                        <div style="color:#ff9f43;">
                                            <strong>Sub Total:</strong> ${formatCurrency(baseAmount)}
                                        </div>
                                        <div style="color:#007bff;">
                                            <strong>GST Inc:</strong> ${formatCurrency(productGstAmount)}
                                        </div>`;

                                    if (itemDiscountAmount > 0) {
                                        rowTotalHtml += `
                                        <div style="color:red;">
                                            <strong>Discount:</strong> -${formatCurrency(itemDiscountAmount)}
                                        </div>`;
                                    }

                                    rowTotalHtml += `
                                        <div style="font-weight:bold; margin-top:4px; border-top:1px solid #ddd; padding-top:3px;color:green;">
                                            Final Total: ${formatCurrency(rowFinalTotal)}
                                        </div>`;

                                    $row.find('.total-amount').html(rowTotalHtml);

                                } else {
                                    // No GST product
                                    const $gstCell = $row.find('.gst-details-cell');
                                    $gstCell.html('<span class="text-muted">No GST</span>');

                                    let rowTotalHtml = `
                                        <div style="color:#ff9f43;">
                                            <strong>Sub Total:</strong> ${formatCurrency(rowFinalTotal)}
                                        </div>`;

                                    if (itemDiscountAmount > 0) {
                                        rowTotalHtml += `
                                        <div style="color:red;">
                                            <strong>Discount:</strong> -${formatCurrency(itemDiscountAmount)}
                                        </div>`;
                                    }

                                    rowTotalHtml += `
                                        <div style="font-weight:bold; margin-top:4px; border-top:1px solid #ddd; padding-top:3px;color:green;">
                                            Final Total: ${formatCurrency(rowFinalTotal)}
                                        </div>`;

                                    $row.find('.total-amount').html(rowTotalHtml);
                                }
                            });

                            // Summary: productDisplaySubtotal shows the GST-exclusive base amount
                            // (inclusive price minus GST portion) so users see the "base" cost
                            const productDisplaySubtotal = grossSubtotal - totalGst;
                            $('#subtotal-display').text(formatNumber(productDisplaySubtotal));

                            // Total Discounts
                            $('#product-discount-total-display').text(formatNumber(totalPerItemDiscount));
                            const hasDiscounts = totalPerItemDiscount > 0;
                            $('.product-discount, .after-discount').toggle(hasDiscounts);

                            // After discount — sum of inclusive prices minus discounts
                            const afterDiscount = grossSubtotal - totalPerItemDiscount;
                            const subtotalFormulaText =
                                `${formatCurrency(afterDiscount)}`;
                            $('#after-discount-display').text(subtotalFormulaText);

                            // Calculate labour cost
                            let labourSubtotal = 0;
                            $('.labour-item-row').each(function() {
                                const qty = parseFloat($(this).find('.labour-qty').val()) || 0;
                                const price = parseFloat($(this).find('.labour-price').val()) || 0;
                                labourSubtotal += qty * price;
                            });
                            $('#labour-cost-display').text(formatNumber(labourSubtotal));
                            $('.labour-cost').toggle(labourSubtotal > 0);

                            // Shipping cost
                            const shippingCost = parseFloat($('#shipping-input').val()) || 0;
                            $('#shipping-cost-display').text(formatNumber(shippingCost));
                            $('.shipping-cost').toggle(shippingCost > 0);

                            // TDS (applies only when enabled in settings)
                            const tdsPercentageInput = isTdsEnabled ? (parseFloat($('#tds-percentage-input').val()) || 0) : 0;
                            const tdsPercentage = Math.max(0, Math.min(100, tdsPercentageInput));

                            // Grand Total: GST is ALREADY inside afterDiscount (inclusive pricing),
                            // so we do NOT add totalGst again. Labour & shipping are added on top.
                            const preTdsGrandTotal = afterDiscount + labourSubtotal + shippingCost;
                            const tdsAmount = isTdsEnabled ? (preTdsGrandTotal * tdsPercentage) / 100 : 0;

                            if (isTdsEnabled) {
                                $('#tds-percentage-display').text(formatNumber(tdsPercentage));
                                $('#tds-amount-display').text(`-${formatNumber(tdsAmount)}`);
                                $('#tds-amount-input').val(tdsAmount.toFixed(2));
                                $('.tds-summary').show();
                            } else {
                                $('.tds-summary').hide();
                            }

                            // Show/hide GST total row (informational only)
                            const $gstTotalLi = $('.total-gst');
                            if (hasGlobalGst && totalGst > 0) {
                                $gstTotalLi.show();
                                $('#total-gst-amount').text(formatNumber(totalGst));
                            } else {
                                $gstTotalLi.hide();
                            }

                            // Grand Total (inclusive prices + labour + shipping - TDS)
                            const grandTotal = preTdsGrandTotal - tdsAmount;
                            const roundedGrandTotal = Math.round(grandTotal);
                            const roundOffAmount = roundedGrandTotal - grandTotal;
                            $('#round-off-display').text(formatNumber(roundOffAmount));
                            $('#grand-total').text(formatNumber(roundedGrandTotal, 0));
                            calculatePaymentBreakdown();
                        }

                        // Bind events for recalculation
                        // $(document).on('input',
                        //     '.quantity-input, .price-input, .discount-input, #shipping-input, #tds-percentage-input, .labour-qty, .labour-price',
                        //     calculateAllTotals);

// $(document).on('input',
//     '.price-input, .discount-input, #shipping-input, #tds-percentage-input, .labour-qty, .labour-price',
//     calculateAllTotals);
$(document).on('input',
    '.price-input, .discount-input, #shipping-input, .labour-qty, .labour-price',
    calculateAllTotals);

// TDS: only recalculate on blur to avoid overwriting while typing
$(document).on('blur', '#tds-percentage-input', calculateAllTotals);

// While typing TDS: only update the display live, don't reformat the field
$(document).on('input', '#tds-percentage-input', function() {
    const rawVal = parseFloat($(this).val()) || 0;
    const tdsPercentage = Math.max(0, Math.min(100, rawVal));
    const grandTotalBeforeTds = parseFloat($('#grand-total').text().replace(/,/g, '')) || 0;
    const tdsAmount = (grandTotalBeforeTds * tdsPercentage) / 100;
    $('#tds-percentage-display').text(tdsPercentage.toFixed(2));
    $('#tds-amount-display').text(`-${tdsAmount.toFixed(2)}`);
    $('#tds-amount-input').val(tdsAmount.toFixed(2));
});

$(document).on('input', '.discount-input', function() {
    const normalizedDiscount = normalizeDiscountInputValue($(this).val());
    $(this).val(normalizedDiscount);
});

// Quantity input: validate stock then recalculate
$(document).on('input', '.quantity-input', function() {
    const $input = $(this);
    const $row = $input.closest('tr');
    const stock = parseFloat($row.data('stock'));
    const enteredQty = parseFloat($input.val()) || 0;
    const productName = $row.find('td:nth-child(2) a:last-of-type').text().trim() ||
                        $row.find('td:nth-child(2) a').last().text().trim() || 'this product';

    // Only validate if stock is a real finite number (not 999999 placeholder)
    if (isFinite(stock) && stock < 999999 && enteredQty > stock) {
        Swal.fire({
            title: 'Stock Quantity Exceeded',
            text: `Only ${stock.toFixed(2)} quantity are available for '${productName}'.`,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ff9f43'
        }).then(() => {
            $input.val(stock.toFixed(2));
            calculateAllTotals();
        });
        return; // Don't recalculate until user dismisses
    }

    if (enteredQty < 0) {
        $input.val(0);
    }

    calculateAllTotals();
});
                        $(document).on('change', 'input[name="gst_option"], .select2-labour', calculateAllTotals);

                        $(document).on('change', '.select2-labour', function() {
                            const price = $(this).find(':selected').data('price') || 0;
                            $(this).closest('.labour-item-row').find('.labour-price').val(price);
                            calculateAllTotals();
                        });

                        // Labour items dynamic row handling
                        $(document).on('click', '.add-labour-item', function() {
                            const newRow = `
                                <div class="row mb-2 labour-item-row">
                                    <div class="col-lg-5 col-sm-4 col-4">
                                        <select name="labour_item_id[]" class="form-control select2-labour-new">
                                            <option value="">Select Labour Item</option>
                                            @foreach ($labourItems as $lItem)
                                                <option value="{{ $lItem->id }}" data-price="{{ $lItem->price }}">{{ $lItem->item_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-sm-3 col-3">
                                        <input type="number" name="labour_qty[]" class="form-control labour-qty" placeholder="Qty" value="1" min="0">
                                    </div>
                                    <div class="col-lg-3 col-sm-3 col-3">
                                        <input type="number" name="labour_price[]" class="form-control labour-price" placeholder="Price" value="0" min="0">
                                    </div>
                                    <div class="col-lg-1 col-sm-2 col-2">
                                        <button type="button" class="btn btn-success add-labour-item">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            `;

                            // Change current plus button to minus
                            $(this).removeClass('btn-success add-labour-item').addClass('btn-danger remove-labour-item')
                                .html('<i class="fas fa-times"></i>');

                            $('#labour-items-container').append(newRow);

                            // Initialize select2 for the new row
                            $('.select2-labour-new').select2({
                                width: '100%'
                            }).removeClass('select2-labour-new').addClass('select2-labour');
                        });

                        $(document).on('click', '.remove-labour-item', function() {
                            $(this).closest('.labour-item-row').remove();
                            calculateAllTotals();
                        });

                        // Handle Quotation Toggle
                        function togglePaymentFields() {
                            togglePaymentInputLayout();
                            calculatePaymentBreakdown();
                        }

                        function clearEmiValidation() {
                            $('#emi_tenure, #emi_aadhar_number, #emi_custom_tenure, #bank_id').removeClass('is-invalid');
                            $('#emi_tenure_error, #emi_aadhar_error, #emi_custom_tenure_error, #bank_id_error, #emi_down_payment_error').addClass('d-none').text('');
                        }

                        function validateEmiFields() {
                            clearEmiValidation();

                            if (normalizePaymentMethod($('#payment_method').val()) !== 'emi') {
                                return true;
                            }

                            let valid = true;
                            const tenure = $('#emi_tenure').val();
                            const customMonths = parseInt($('#emi_custom_tenure').val() || '0', 10);
                            const aadhar = ($('#emi_aadhar_number').val() || '').trim();
                            const bankId = $('#bank_id').val();

                            if (!tenure) {
                                $('#emi_tenure').addClass('is-invalid');
                                $('#emi_tenure_error').removeClass('d-none').text('EMI tenure is required.');
                                valid = false;
                            } else if (tenure === 'custom' && (!Number.isFinite(customMonths) || customMonths <= 0)) {
                                $('#emi_custom_tenure').addClass('is-invalid');
                                $('#emi_custom_tenure_error').removeClass('d-none').text('Enter a valid number of months.');
                                valid = false;
                            }


                            return valid;
                        }

                        $('#quotationToggle').on('change', togglePaymentFields);
                        togglePaymentFields(); // Initial call

                        $('#payment_method').on('change', function() {
                            if (_isSyncingPayment) return;
                            setSelect2Value('#paid_type', '');
                            $('#cash_amount').val('');
                            $('#online_amount').val('');
                            $('#pending_amount').val(parseMoney($('#pending_amount').data('prefill')).toFixed(2));
                            if (normalizePaymentMethod($(this).val()) !== 'emi') {
                                $('#emi_down_payment, #emi_loan_amount, #emi_monthly_amount, #emi_aadhar_number, #emi_do_id, #emi_pan_number, #emi_guarantor_name')
                                    .val('');
                                $('#emi_tenure').val('');
                                $('#emi_interest_rate').val('0');
                                $('#bank_id').val('').trigger('change');
                            }
                            clearEmiValidation();
                            calculatePaymentBreakdown();
                        });

                        $('#payment_method').on('select2:select select2:clear', function() {
                            $(this).trigger('change');
                        });

                        $('#emi_down_payment, #emi_interest_rate, #emi_tenure, #emi_custom_tenure').on('input change', function() {
                            clearEmiValidation();
                            if ($(this).is('#emi_tenure')) {
                                togglePaymentInputLayout();
                            }
                            // Always recalculate EMI fields live on any user interaction
                            recalculateEmiFields();
                            calculatePaymentBreakdown();
                        });

                        $('#payment_status').on('change', function() {
                            if (_isSyncingPayment) return;
                            const status = $(this).val();

                            if (status === 'pending') {
                                setSelect2Value('#payment_method', 'pending');
                            } else {
                                syncPaidTypeWithStatus();

                                if (normalizePaymentMethod($('#payment_method').val()) === 'pending') {
                                    setSelect2Value('#payment_method', 'cash');
                                }
                            }

                            calculatePaymentBreakdown();
                        });

                        $('#paid_type').on('change', function() {
                            if (_isSyncingPayment) return;
                            calculatePaymentBreakdown();
                        });
                        // $('#cash_amount, #online_amount').on('input', calculatePaymentBreakdown);

                        // Recalculate pending/status on blur only (so typing isn't interrupted)
$('#cash_amount, #online_amount').on('blur', calculatePaymentBreakdown);

// While typing: only update pending amount display live, don't reformat the field
$('#cash_amount, #online_amount').on('input', function() {
    const method = normalizePaymentMethod($('#payment_method').val());
    const grandTotal = getGrandTotalValue();
    const historicalPaidAmount = Math.min(grandTotal, parseMoney($('#pending_amount').data('paid-total')));
    const outstandingAmount = Math.max(0, grandTotal - historicalPaidAmount);

    let cashAmount = parseMoney($('#cash_amount').val());
    let onlineAmount = parseMoney($('#online_amount').val());
    let additionalPaid = 0;

    if (method === 'cash') {
        additionalPaid = Math.min(cashAmount, outstandingAmount);
    } else if (method === 'online') {
        additionalPaid = Math.min(onlineAmount, outstandingAmount);
    } else if (method === 'cash+online') {
        cashAmount = Math.min(cashAmount, outstandingAmount);
        onlineAmount = Math.min(onlineAmount, Math.max(outstandingAmount - cashAmount, 0));
        additionalPaid = Math.min(cashAmount + onlineAmount, outstandingAmount);
    }

    const pendingAmount = Math.max(outstandingAmount - additionalPaid, 0);
    $('#pending_amount').val(pendingAmount.toFixed(2));
});

                        $('#openAddBankModal').on('click', function() {
                            resetAddBankForm();
                            if (addBankModal) {
                                addBankModal.show();
                            }
                        });

                        $('#addBankForm').on('submit', function(e) {
                            e.preventDefault();

                            if (!validateAddBankForm()) {
                                return;
                            }

                            const authToken = localStorage.getItem("authToken");
                            const formData = new FormData(this);
                            if (selectedSubAdminId) {
                                formData.append('selectedSubAdminId', selectedSubAdminId);
                            }
                            formData.set('bank_name', $('#add_bank_name').val().trim());
                            formData.set('account_number', $('#add_account_number').val().trim());
                            formData.set('ifsc_code', $('#add_ifsc_code').val().trim().toUpperCase());
                            formData.set('branch_name', $('#add_branch_name').val().trim());
                            formData.set('opening_balance', parseMoney($('#add_opening_balance').val()).toFixed(2));

                            const $saveButton = $('#saveBankBtn');
                            $saveButton.prop('disabled', true).text('Saving...');

                            $.ajax({
                                url: '/api/banks',
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                    "Authorization": "Bearer " + authToken
                                },
                                success: function(response) {
                                    upsertBankOption(response.data || null);
                                    if (addBankModal) {
                                        addBankModal.hide();
                                    }

                                    Swal.fire({
                                        title: "Success",
                                        text: response.message || "Bank added successfully.",
                                        icon: "success",
                                        confirmButtonText: "OK",
                                        confirmButtonColor: "#ff9f43"
                                    });
                                },
                                error: function(xhr) {
                                    const errors = xhr.responseJSON?.errors || {};
                                    $('#addBankNameError').text(errors.bank_name ? errors.bank_name[0] : '');
                                    $('#addAccountNumberError').text(errors.account_number ? errors.account_number[0] : '');
                                    $('#addIfscCodeError').text(errors.ifsc_code ? errors.ifsc_code[0] : '');
                                    $('#addBranchNameError').text(errors.branch_name ? errors.branch_name[0] : '');
                                    $('#addOpeningBalanceError').text(errors.opening_balance ? errors.opening_balance[0] : '');
                                    $('#addBankStatusError').text(errors.status ? errors.status[0] : '');

                                    if (!Object.keys(errors).length) {
                                        Swal.fire({
                                            title: "Error",
                                            text: xhr.responseJSON?.message || "Failed to add bank.",
                                            icon: "error",
                                            confirmButtonText: "OK",
                                            confirmButtonColor: "#ff9f43"
                                        });
                                    }
                                },
                                complete: function() {
                                    $saveButton.prop('disabled', false).text('Save Bank');
                                }
                            });
                        });

                        // Handle product selection changes
                        $('.product-select').on('change', function() {
                            const selectedProducts = $(this).val() || [];
                            const existingProducts = $('#product-table-body tr[data-product-id]').map(function() {
                                return $(this).attr('data-product-id') || '';
                            }).get();
                            const addProductPromises = [];

                            // Add new products
                            selectedProducts.forEach(productId => {
                                if (!existingProducts.includes(productId)) {
                                    const option = $(this).find('option[value="' + productId + '"]');
                                    const optionName = getOptionData(option, 'name', option.text().trim());
                                    const optionImage = getOptionData(option, 'image', '');
                                    const optionGstOption = getOptionData(option, 'gst-option', 'without_gst');
                                    const optionProductGst = getOptionData(option, 'product-gst', '[]');
                                    const optionUnit = getOptionData(option, 'unit', 'N/A');
                                    const optionCategory = getOptionData(option, 'category', '');
                                    const optionDiscount = getOptionData(option, 'discount', 0);
                                    const optionPrice = normalizeProductPrice(getOptionData(option, 'price'));

                                    if (optionPrice !== null) {
                                     const optionStock = parseFloat(getOptionData(option, 'stock', 999999)) || 999999;
addProductToTable(
    productId,
    optionName,
    optionImage,
    optionPrice,
    optionGstOption,
    optionProductGst,
    null,
    optionUnit,
    optionDiscount,
    optionStock,
    optionCategory
);
                                        return;
                                    }

                                    const fallbackRequest = fetchProductDetails(productId)
                                        .done(function(response) {
                                            const product = response.product || {};
                                            const fallbackPrice = normalizeProductPrice(product.price);
                                            const fallbackUnit = product.unit && product.unit.unit_name ?
                                                product.unit
                                                .unit_name : 'N/A';

                                         addProductToTable(
    productId,
    optionName || product.name || option.text().trim(),
    optionImage || product.image || '',
    fallbackPrice !== null ? fallbackPrice : 0,
    optionGstOption || product.gst_option || 'without_gst',
    optionProductGst || product.product_gst || '[]',
    null,
    optionUnit || fallbackUnit,
    optionDiscount,
    parseFloat(product.quantity ?? product.stock ?? 999999) || 999999,
    optionCategory || (product.category ? product.category.name : '')
);
                                        })
                                         .fail(function() {
                                           addProductToTable(
    productId,
    optionName,
    optionImage,
    0,
    optionGstOption,
    optionProductGst,
    null,
    optionUnit,
    optionDiscount,
    parseFloat(getOptionData(option, 'stock', 999999)) || 999999,
    optionCategory
);
                                        });

                                    addProductPromises.push(fallbackRequest);
                                }
                            });

                            // Remove unselected products
                            $('#product-table-body tr[data-product-id]').each(function() {
                                const rowProductId = $(this).attr('data-product-id') || '';
                                if (!selectedProducts.includes(rowProductId)) {
                                    $(this).remove();
                                }
                            });

                            toggleNoProductsMessage();
                            if (addProductPromises.length) {
                                $.when.apply($, addProductPromises).always(calculateAllTotals);
                            } else {
                                calculateAllTotals();
                            }
                        });

                        // Initialize existing rows with their GST details from order_items
                        $('#product-table-body tr[data-product-id]').each(function() {
                            const $row = $(this);
                            const productId = $row.attr('data-product-id') || '';

                            // Get GST details from the existing data attributes
                            let productGstData = $row.data('product-gst');

                            if (typeof productGstData === 'string' && productGstData.trim() !== '') {
                                try {
                                    const parsedGst = JSON.parse(productGstData);
                                    $row.data('product-gst', parsedGst);
                                } catch (e) {
                                    // console.error('Error parsing GST data:', e);
                                    $row.data('product-gst', []);
                                }
                            } else if (!productGstData) {
                                $row.data('product-gst', []);
                            }

                            // Add event listeners for existing rows
                            $row.find('.quantity-input').on('input', calculateAllTotals);
                            $row.find('.price-input').on('input', calculateAllTotals);
                            $row.find('.discount-input').on('input', calculateAllTotals);
                            $row.find('.delete-set').on('click', function() {
                                $(this).closest('tr').remove();
                                updateProductSelection();
                                toggleNoProductsMessage();
                                calculateAllTotals();
                            });
                        });



                        const initialQuotationStatus = @json($sales->quotation_status ?? 'sales');

                        function resetUpdateButtonState() {
                            const $btn = $('#update-order-btn');
                            const $loader = $('#btn-loader');
                            const $btnText = $('#btn-text');

                            $btn.prop('disabled', false);
                            $loader.addClass('d-none');
                            $btnText.text('Update Order');
                        }

                        function clearOrderNumberError() {
                            $('#order_number_error').hide().text('');
                        }

                        function showOrderNumberError(message) {
                            $('#order_number_error').text(message).show();
                            $('#order_number').focus();
                        }

                        function handleOrderNumberValidationError(xhr) {
                            const orderNumberError = xhr?.responseJSON?.errors?.order_number?.[0];

                            if (orderNumberError) {
                                showOrderNumberError(orderNumberError);
                                return true;
                            }

                            return false;
                        }

                        function submitOrderUpdate(formData, authToken, successMessage = "Order updated successfully!") {
                            $.ajax({
                                url: `/api/update_sale`,
                                type: "POST",
                                headers: {
                                    "Authorization": "Bearer " + authToken,
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                },
                                data: formData,
                                success: function(response) {
                                    if (response.success) {
                                        const displayMessage = (response.message && response.message.trim())
                                            ? response.message
                                            : successMessage;
                                        const displayTitle = formData.quotation_status === 'advance_receipt'
                                            ? 'Advance Receipt Updated!'
                                            : (formData.quotation_status === 'quotation'
                                                ? 'Quotation Updated!'
                                                : 'Order Updated!');
                                        Swal.fire({
                                            title: displayTitle,
                                            text: displayMessage,
                                            icon: "success",
                                            confirmButtonText: "OK",
                                            confirmButtonColor: "#ff9f43"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "{{ route('sales.list') }}";
                                            }
                                        });
                                    } else {
                                        resetUpdateButtonState();

                                        Swal.fire({
                                            title: "Error",
                                            text: response.message,
                                            icon: "error",
                                            confirmButtonText: "OK",
                                            confirmButtonColor: "#ff9f43"
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    resetUpdateButtonState();

                                    if (handleOrderNumberValidationError(xhr)) {
                                        return;
                                    }

                                    let message = 'An error occurred while updating the order';
                                    try {
                                        const res = xhr.responseJSON;
                                        if (res.message) {
                                            message = res.message;
                                        } else if (res.errors) {
                                            message = Object.values(res.errors).join('<br>');
                                        }
                                    } catch (e) {
                                        // console.error('Failed to parse error message:', e);
                                    }

                                    Swal.fire({
                                        title: "Error",
                                        html: message,
                                        icon: "error",
                                        confirmButtonText: "OK",
                                        confirmButtonColor: "#ff9f43"
                                    });
                                }
                            });
                        }

                        function convertQuotationToSale(orderId, authToken) {
                            $.ajax({
                                url: `/api/convert-quotation-to-sale/${orderId}`,
                                type: "POST",
                                headers: {
                                    "Authorization": "Bearer " + authToken,
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    if (response.status === true) {
                                        Swal.fire({
                                            title: "Success!",
                                            text: "Quotation converted to sale and order updated successfully!",
                                            icon: "success",
                                            confirmButtonText: "OK",
                                            confirmButtonColor: "#ff9f43"
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "{{ route('sales.list') }}";
                                            }
                                        });
                                    } else {
                                        resetUpdateButtonState();

                                        Swal.fire({
                                            title: "Error",
                                            text: response.message || "Failed to convert quotation.",
                                            icon: "error",
                                            confirmButtonText: "OK",
                                            confirmButtonColor: "#ff9f43"
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    resetUpdateButtonState();

                                    let message = 'Failed to convert quotation.';
                                    try {
                                        const res = xhr.responseJSON;
                                        if (res.message) {
                                            message = res.message;
                                        } else if (res.error) {
                                            message = res.error;
                                        }
                                    } catch (e) {
                                        // console.error('Failed to parse error message:', e);
                                    }

                                    Swal.fire({
                                        title: "Error",
                                        text: message,
                                        icon: "error",
                                        confirmButtonText: "OK",
                                        confirmButtonColor: "#ff9f43"
                                    });
                                }
                            });
                        }

                        function updateQuotationBeforeConvert(formData, authToken) {
                            const updateData = {
                                ...formData,
                                quotation_status: initialQuotationStatus
                            };

                            $.ajax({
                                url: `/api/update_sale`,
                                type: "POST",
                                headers: {
                                    "Authorization": "Bearer " + authToken,
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                },
                                data: updateData,
                                success: function(response) {
                                    if (response.success) {
                                        convertQuotationToSale(formData.update_id, authToken);
                                    } else {
                                        resetUpdateButtonState();

                                        Swal.fire({
                                            title: "Error",
                                            text: response.message,
                                            icon: "error",
                                            confirmButtonText: "OK",
                                            confirmButtonColor: "#ff9f43"
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    resetUpdateButtonState();

                                    if (handleOrderNumberValidationError(xhr)) {
                                        return;
                                    }

                                    let message = 'An error occurred while updating the quotation';
                                    try {
                                        const res = xhr.responseJSON;
                                        if (res.message) {
                                            message = res.message;
                                        } else if (res.errors) {
                                            message = Object.values(res.errors).join('<br>');
                                        }
                                    } catch (e) {
                                        // console.error('Failed to parse error message:', e);
                                    }

                                    Swal.fire({
                                        title: "Error",
                                        html: message,
                                        icon: "error",
                                        confirmButtonText: "OK",
                                        confirmButtonColor: "#ff9f43"
                                    });
                                }
                            });
                        }

                        // Form submission handler
                        $(document).on("click", '#update-order-btn', function(e) {
                            var authToken = localStorage.getItem("authToken");
                            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                            e.preventDefault();
                            clearOrderNumberError();

                            // Collect all form data
                            const formData = {
                                update_id: $('#update_selse_id').val(),
                                customer_id: $('#customer_id').val(),
                                order_number: $('#order_number').val(),
                                order_date: $('input[name="order_date"]').val(),
                                product_ids: [],
                                quantities: {},
                                prices: {},
                                discounts: {},
                                imei_no: {},
                                discount: 0,
                                grand_total: $('#grand-total').text().replace(/[^0-9.]/g, ''),
                                gst_option: $('input[name="gst_option"]:checked').val(),
                                selectedSubAdminId: selectedSubAdminId || null,
                                remarks: $('#remarks').val(),
                                payment_method: $('#payment_method').val(),
                                status: $('#payment_status').val(),
                                paid_type: $('#paid_type').val(),
                                bank_id: $('#bank_id').val(),
                                cash_amount: $('#cash_amount').val() || 0,
                                online_amount: $('#online_amount').val() || 0,
                                pending_amount: $('#pending_amount').val() || 0,
                                assigned_staff_id: $('#assigned_staff_id').val() || null,
                                order_type: $('#order_type').val() || 'self_pickup',
                                emi_down_payment: $('#emi_down_payment').val() || 0,
                                emi_loan_amount: $('#emi_loan_amount').val() || 0,
                                emi_interest_rate: $('#emi_interest_rate').val() || 0,
                                emi_tenure: $('#emi_tenure').val() || '',
                                emi_custom_tenure: $('#emi_custom_tenure').val() || '',
                                emi_monthly_amount: $('#emi_monthly_amount').val() || 0,
                                emi_aadhar_number: $('#emi_aadhar_number').val() || '',
                                emi_do_id: $('#emi_do_id').val() || '',
                                emi_pan_number: $('#emi_pan_number').val() || '',
                                emi_guarantor_name: $('#emi_guarantor_name').val() || '',
                                payment_amount: Math.max(
                                    0,
                                    Math.max(
                                        0,
                                        getGrandTotalValue() - parseMoney($('#pending_amount').data('paid-total'))
                                    ) - parseMoney($('#pending_amount').val() || 0)
                                ).toFixed(2),
                                shipping: $('#shipping-input').val(),
                                tds_percentage: $('#tds-percentage-input').val() || 0,
                                tds_amount: $('#tds-amount-input').val() || 0,
                                quotation_status: $('#quotationToggle').is(':checked') ? 'quotation' : 'sales',
                                labour_item_ids: [],
                                labour_qtys: [],
                                labour_prices: []
                            };

                            // Collect labour items
                            $('.labour-item-row').each(function() {
                                const itemId = $(this).find('select[name="labour_item_id[]"]').val();
                                if (itemId) {
                                    formData.labour_item_ids.push(itemId);
                                    formData.labour_qtys.push($(this).find('.labour-qty').val());
                                    formData.labour_prices.push($(this).find('.labour-price').val());
                                }
                            });

                            // Collect product rows. Rows with qty 0 are removed from the update payload.
                            $('#product-table-body tr[data-product-id]').each(function() {
                                const $row = $(this);
                                const productId = $row.attr('data-product-id') || '';
                                const quantity = parseFloat($row.find('.quantity-input').val()) || 0;

                                if (quantity <= 0) {
                                    return;
                                }

                                formData.product_ids.push(productId.toString());
                                formData.quantities[productId] = $row.find('.quantity-input').val();
                                formData.prices[productId] = $row.find('.price-input').val();
                                formData.discounts[productId] = $row.find('.discount-input').val();
                                
                                const imeiVal = $row.find('.edit-imei-select').val();
                                if (imeiVal) {
                                    formData.imei_no[productId] = imeiVal;
                                }
                            });

                            // Validate
                            if (!formData.customer_id) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a customer",
                                    icon: "error",
                                    confirmButtonText: "OK",
                                    confirmButtonColor: "#ff9f43"
                                });
                                return;
                            }

                            if (!formData.product_ids || formData.product_ids.length === 0) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select at least one product",
                                    icon: "error",
                                    confirmButtonText: "OK",
                                    confirmButtonColor: "#ff9f43"
                                });
                                return;
                            }

                            const normalizedPaymentMethod = normalizePaymentMethod(formData.payment_method);
                            const paymentAmount = parseMoney(formData.payment_amount);

                            if (formData.quotation_status !== 'quotation' && ['online', 'cash+online'].includes(normalizedPaymentMethod) && !formData.bank_id) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a bank for bank payment.",
                                    icon: "error",
                                    confirmButtonText: "OK",
                                    confirmButtonColor: "#ff9f43"
                                });
                                return;
                            }

                            if (!validateEmiFields()) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please complete the EMI required fields.",
                                    icon: "error",
                                    confirmButtonText: "OK",
                                    confirmButtonColor: "#ff9f43"
                                });
                                return;
                            }

                            const outstandingAmountVal = Math.max(0, getGrandTotalValue() - parseMoney($('#pending_amount').data('paid-total')));
                            if (formData.quotation_status !== 'quotation' && normalizedPaymentMethod !== 'pending' && normalizedPaymentMethod !== 'emi' && paymentAmount <= 0 && outstandingAmountVal > 0) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please enter a valid payment amount.",
                                    icon: "error",
                                    confirmButtonText: "OK",
                                    confirmButtonColor: "#ff9f43"
                                });
                                return;
                            }

                            // Show loader and disable button
                            const $btn = $('#update-order-btn');
                            const $loader = $('#btn-loader');
                            const $btnText = $('#btn-text');

                            $btn.prop('disabled', true);
                            $loader.removeClass('d-none');
                            $btnText.text('Updating...');

                            const isConvertingToSale = initialQuotationStatus === 'quotation' &&
                                formData.quotation_status === 'sales';

                            if (isConvertingToSale) {
                                Swal.fire({
                                    title: "Convert To Sale?",
                                    text: "This quotation will be converted to a sale. Do you want to continue?",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonText: "Yes, Convert",
                                    cancelButtonText: "Cancel",
                                    confirmButtonColor: "#ff9f43",
                                    cancelButtonColor: "#6c757d"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        updateQuotationBeforeConvert(formData, authToken);
                                    } else {
                                        resetUpdateButtonState();
                                    }
                                });
                                return;
                            }

                            submitOrderUpdate(formData, authToken,
                                formData.quotation_status === 'advance_receipt'
                                    ? 'Advance Receipt updated successfully!'
                                    : (formData.quotation_status === 'quotation'
                                        ? 'Quotation updated successfully!'
                                        : 'Order updated successfully!')
                            );
                        });

                        // Initial calculation
                        calculateAllTotals();

                        // Initialize customer phone on page load
                        const selectedCustomer = $('#customer_id').find(':selected');
                        if (selectedCustomer.length) {
                            $('#customer_phone').val(selectedCustomer.data('phone') || '');
                        }

                        $('#order_number').on('input', function() {
                            clearOrderNumberError();
                        });

                        calculatePaymentBreakdown();

                        // Mark init as done — EMI fields will recalculate on user interaction from here on
                        _emiPageInitDone = true;

                        // If payment method is EMI, recalculate fields now so loan/monthly are always consistent
                        if (normalizePaymentMethod($('#payment_method').val()) === 'emi') {
                            recalculateEmiFields();
                        }

                        // Initialize Order Date Picker
                        const $orderDateDisplay = $('#order_date_display');
                        if ($orderDateDisplay.length && typeof $orderDateDisplay.datetimepicker === 'function') {
                            $orderDateDisplay.datetimepicker({
                                format: 'DD/MM/YYYY',
                                useCurrent: true,
                                showTodayButton: true,
                                icons: {
                                    date: 'fa fa-calendar',
                                    previous: 'fa fa-chevron-left',
                                    next: 'fa fa-chevron-right',
                                    today: 'fa fa-crosshairs',
                                    clear: 'fa fa-trash',
                                    close: 'fa fa-times'
                                }
                            });

                            $orderDateDisplay.on('dp.change', function(e) {
                                if (e.date) {
                                    $('#order_date').val(e.date.format('YYYY-MM-DD'));
                                }
                            });
                        }
                    });
                </script>

                {{-- ===== QUICK ADD CUSTOMER MODAL JS ===== --}}
                <script>
                (function () {
                    var authToken = localStorage.getItem('authToken');

                    var qacModalEl = document.getElementById('quickAddCustomerModal');
                    var qacModal   = qacModalEl && typeof bootstrap !== 'undefined'
                        ? new bootstrap.Modal(qacModalEl)
                        : null;

                    var stateCodeToName = {
                        "01":"Jammu and Kashmir","02":"Himachal Pradesh","03":"Punjab",
                        "04":"Chandigarh","05":"Uttarakhand","06":"Haryana","07":"Delhi",
                        "08":"Rajasthan","09":"Uttar Pradesh","10":"Bihar","11":"Sikkim",
                        "12":"Arunachal Pradesh","13":"Nagaland","14":"Manipur",
                        "15":"Mizoram","16":"Tripura","17":"Meghalaya","18":"Assam",
                        "19":"West Bengal","20":"Jharkhand","21":"Odisha","22":"Chhattisgarh",
                        "23":"Madhya Pradesh","24":"Gujarat","27":"Maharashtra",
                        "29":"Karnataka","33":"Tamil Nadu","36":"Telangana"
                    };

                    var stateNameToCode = {
                        "Jammu and Kashmir":"01","Himachal Pradesh":"02","Punjab":"03",
                        "Chandigarh":"04","Uttarakhand":"05","Haryana":"06","Delhi":"07",
                        "Rajasthan":"08","Uttar Pradesh":"09","Bihar":"10","Sikkim":"11",
                        "Arunachal Pradesh":"12","Nagaland":"13","Manipur":"14",
                        "Mizoram":"15","Tripura":"16","Meghalaya":"17","Assam":"18",
                        "West Bengal":"19","Jharkhand":"20","Odisha":"21","Chhattisgarh":"22",
                        "Madhya Pradesh":"23","Gujarat":"24","Maharashtra":"27",
                        "Karnataka":"29","Tamil Nadu":"33","Telangana":"36"
                    };

                    /* ── Helpers ── */
                    function resetQacForm() {
                        $('#quickAddCustomerForm')[0].reset();
                        $('#qac_edit_id').val('');
                        $('.qac-error').html('');
                        $('#qac-gst-msg').html('');
                        $('#qac-gst-loader').hide();
                        $('#qac_use_same_address').prop('checked', false);
                    }

                    function setQacMode(mode) {
                        var isEdit = mode === 'edit';
                        $('#quickAddCustomerModalLabel').text(isEdit ? 'Edit Customer' : 'Add New Customer');
                        $('#qacSaveBtnText').text(isEdit ? 'Update Customer' : 'Save Customer');
                    }

                    function fillQacForm(c) {
                        $('#qac_customer_name').val(c.name         || '');
                        $('#qac_company_name').val(c.company_name  || '');
                        $('#qac_phone').val(c.phone                || '');
                        $('#qac_alternate_phone').val(c.alternate_phone || '');
                        $('#qac_email').val(c.email                || '');
                        $('#qac_gst_number').val(c.gst_number      || '');
                        $('#qac_pan_number').val(c.pan_number      || '');
                        $('#qac_state_code').val(c.state_code      || '');
                        $('#qac_state_name').val(c.state_name      || stateCodeToName[c.state_code] || '');
                        $('#qac_country').val(c.country || (c.details && c.details.country) || '');
                        $('#qac_city').val(c.city       || (c.details && c.details.city)    || '');
                        $('#qac_address').val(c.address || (c.details && c.details.address) || '');
                        $('#qac_delivery_address').val(c.delivery_address || (c.details && c.details.delivery_address) || '');

                        var addressVal = $('#qac_address').val();
                        var deliveryAddressVal = $('#qac_delivery_address').val();
                        if (addressVal && addressVal === deliveryAddressVal) {
                            $('#qac_use_same_address').prop('checked', true);
                        } else {
                            $('#qac_use_same_address').prop('checked', false);
                        }
                    }

                    /* ── Show/hide edit pencil ── */
                    function syncEditBtn() {
                        var val = $('#customer_id').val();
                        var isReal = val && /^\d+$/.test(String(val));
                        $('#openEditCustomerBtn').css('display', isReal ? 'inline-flex' : 'none');
                    }
                    syncEditBtn();
                    $(document).on('change', '#customer_id', syncEditBtn);

                    /* ── Open ADD modal ── */
                    $(document).on('click', '#openQuickAddCustomerBtn', function () {
                        resetQacForm();
                        setQacMode('add');
                        if (qacModal) qacModal.show();
                    });

                    /* ── Open EDIT modal ── */
                    $(document).on('click', '#openEditCustomerBtn', function () {
                        var customerId = $('#customer_id').val();
                        if (!customerId || !/^\d+$/.test(String(customerId))) return;

                        resetQacForm();
                        setQacMode('edit');
                        $('#qac_edit_id').val(customerId);

                        if (qacModal) qacModal.show();
                        $('#qacSaveBtn').prop('disabled', true);
                        $('#qacBtnSpinner').removeClass('d-none');

                        $.ajax({
                            url: '/api/getCustomer/' + customerId,
                            method: 'GET',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Authorization': 'Bearer ' + authToken
                            },
                            success: function (res) {
                                if (res.status && res.customer) {
                                    var c = res.customer;
                                    if (c.details) {
                                        c.country = c.country || c.details.country;
                                        c.city    = c.city    || c.details.city;
                                        c.address = c.address || c.details.address;
                                        c.delivery_address = c.delivery_address || c.details.delivery_address;
                                    }
                                    fillQacForm(c);
                                } else {
                                    if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: 'Could not load customer details.' });
                                    if (qacModal) qacModal.hide();
                                }
                            },
                            error: function () {
                                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to fetch customer details.' });
                                if (qacModal) qacModal.hide();
                            },
                            complete: function () {
                                $('#qacSaveBtn').prop('disabled', false);
                                $('#qacBtnSpinner').addClass('d-none');
                            }
                        });
                    });

                    /* ── State code → name ── */
                    $(document).on('input', '#qac_state_code', function () {
                        var raw = this.value.replace(/[^0-9]/g, '').substring(0, 3);
                        this.value = raw;
                        $('#qac_state_name').val(stateCodeToName[raw] || '');
                    });

                    /* ── GST lookup ── */
                    $(document).on('input', '#qac_gst_number', function () {
                        var raw = $(this).val().toUpperCase().replace(/[^0-9A-Z]/g, '').substring(0, 15);
                        $(this).val(raw);
                        $('#qac-gst-msg').html('');
                        if (raw.length < 15) return;

                        var $loader = $('#qac-gst-loader');
                        var $msg    = $('#qac-gst-msg');
                        $loader.show();
                        $(this).prop('readonly', true);
                        $msg.html('<span style="color:#1B2850;"><i class="fas fa-spinner fa-spin"></i> Fetching GST details...</span>');

                        $.ajax({
                            url: '/api/fetch-gst-details',
                            method: 'POST',
                            dataType: 'json',
                            data: { gst_number: raw },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Authorization': 'Bearer ' + authToken
                            },
                            success: function (res) {
                                $msg.html('');
                                if (res.error) { $msg.html('<span class="text-danger">GST details not found.</span>'); return; }
                                var stateName = res.state || '';
                                var stateCode = stateNameToCode[stateName] || '';
                                if (stateCode)           $('#qac_state_code').val(stateCode);
                                if (stateName)           $('#qac_state_name').val(stateName);
                                if (res.legal_name)      $('#qac_customer_name').val(res.legal_name);
                                if (res.company_name)    $('#qac_company_name').val(res.company_name);
                                if (res.primary_address) $('#qac_address').val(res.primary_address);
                                if (res.city)            $('#qac_city').val(res.city);
                                if (res.country)         $('#qac_country').val(res.country);
                                if (raw.length >= 12)    $('#qac_pan_number').val(raw.substring(2, 12));
                                $msg.html('<span style="color:#28a745;"><i class="fas fa-check-circle"></i> Details fetched successfully.</span>');
                            },
                            error: function () {
                                $('#qac-gst-msg').html('<span class="text-danger">Failed to fetch GST details.</span>');
                            },
                            complete: function () {
                                $loader.hide();
                                $('#qac_gst_number').prop('readonly', false);
                            }
                        });
                    });

                    /* ── Phone digits only ── */
                    $(document).on('input', '#qac_phone, #qac_alternate_phone', function () {
                        this.value = this.value.replace(/\D/g, '').substring(0, 10);
                    });

                    /* ── Form submit (Add or Edit) ── */
                    $(document).on('submit', '#quickAddCustomerForm', function (e) {
                        e.preventDefault();
                        $('.qac-error').html('');

                        var editId = $('#qac_edit_id').val();
                        var isEdit = !!editId;

                        var $btn     = $('#qacSaveBtn');
                        var $spinner = $('#qacBtnSpinner');
                        $btn.prop('disabled', true);
                        $spinner.removeClass('d-none');

                        var formData = new FormData();
                        formData.append('customer_name', $('#qac_customer_name').val().trim());
                        formData.append('company_name',  $('#qac_company_name').val().trim());
                        formData.append('phone',         $('#qac_phone').val().trim());
                        formData.append('alternate_phone', $('#qac_alternate_phone').val().trim());
                        formData.append('email',         $('#qac_email').val().trim());
                        formData.append('gst_number',    $('#qac_gst_number').val().trim());
                        formData.append('pan_number',    $('#qac_pan_number').val().trim());
                        formData.append('state_code',    $('#qac_state_code').val().trim());
                        formData.append('state_name',    $('#qac_state_name').val().trim());
                        formData.append('country',       $('#qac_country').val().trim());
                        formData.append('city',          $('#qac_city').val().trim());
                        formData.append('address',       $('#qac_address').val().trim());
                        formData.append('delivery_address', $('#qac_delivery_address').val().trim());

                        var sid = localStorage.getItem('selectedSubAdminId');
                        if (sid && sid !== 'null' && sid !== 'undefined') {
                            formData.append('selectedSubAdminId', sid);
                        }

                        var apiUrl = isEdit
                            ? '/api/updateCustomer/' + editId
                            : '/api/createCustomer';

                        $.ajax({
                            url: apiUrl,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Authorization': 'Bearer ' + authToken
                            },
                            success: function (response) {
                                if (response.status) {
                                    var name    = $('#qac_customer_name').val().trim();
                                    var company = $('#qac_company_name').val().trim();
                                    var phone   = $('#qac_phone').val().trim();
                                    var label   = (company || name) + (phone ? ' - ' + phone : '');

                                    var $select = $('#customer_id');

                                    if (isEdit) {
                                        // Update the <option> text + data in DOM
                                        var $existing = $select.find('option[value="' + editId + '"]');
                                        if ($existing.length) {
                                            $existing.text(label).attr('data-phone', phone);
                                        } else {
                                            $select.append(
                                                $('<option>', { value: editId, text: label }).attr('data-phone', phone)
                                            );
                                        }

                                        // Destroy & reinit select2 so it picks up new text
                                        if ($select.data('select2')) {
                                            $select.select2('destroy');
                                        }
                                        $select.select2({
                                            placeholder: 'Select Customer',
                                            allowClear: true,
                                            width: '100%',
                                            matcher: function (params, data) {
                                                if ($.trim(params.term) === '') return data;
                                                if (typeof data.text === 'undefined') return null;
                                                var term      = params.term.toLowerCase();
                                                var text      = (data.text || '').toLowerCase();
                                                var ph        = ($(data.element).data('phone') || '').toString().toLowerCase();
                                                var normTerm  = term.replace(/[^0-9a-z]/g, '');
                                                var normPhone = ph.replace(/[^0-9]/g, '');
                                                if (text.indexOf(term) > -1)  return data;
                                                if (ph.indexOf(term) > -1)    return data;
                                                if (normTerm && normPhone.indexOf(normTerm) > -1) return data;
                                                return null;
                                            }
                                        });
                                        $select.val(editId).trigger('change');

                                    } else {
                                        var realId = response.customer_id || response.id;
                                        $select.find('option[value="' + realId + '"]').remove();
                                        var $opt = $('<option>', { value: realId, text: label }).attr('data-phone', phone);
                                        $select.append($opt);
                                        $select.val(realId).trigger('change');
                                        if ($select.data('select2')) $select.trigger('change.select2');
                                    }

                                    $('#customer_phone').val(phone);

                                    if (qacModal) qacModal.hide();
                                    syncEditBtn();

                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: isEdit ? 'Customer Updated' : 'Customer Added',
                                            text: name + (isEdit ? ' updated' : ' added') + ' successfully.',
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                    }
                                } else {
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Operation failed.' });
                                    }
                                }
                            },
                            error: function (xhr) {
                                if (xhr.status === 422) {
                                    var errors = (xhr.responseJSON || {}).errors || {};
                                    $.each(errors, function (key, msgs) {
                                        $('#qac_error_' + key).html(msgs[0]);
                                    });
                                } else {
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({ icon: 'error', title: 'Error', text: (xhr.responseJSON || {}).message || 'Something went wrong.' });
                                    }
                                }
                            },
                            complete: function () {
                                $btn.prop('disabled', false);
                                $spinner.addClass('d-none');
                            }
                        });
                    });

                    // Sync QAC Address to Delivery Address
                    $('#qac_use_same_address').on('change', function() {
                        if ($(this).is(':checked')) {
                            $('#qac_delivery_address').val($('#qac_address').val());
                        }
                    });

                    $('#qac_address').on('input', function() {
                        if ($('#qac_use_same_address').is(':checked')) {
                            $('#qac_delivery_address').val($(this).val());
                        }
                    });

                    $('#qac_delivery_address').on('input', function() {
                        if ($('#qac_use_same_address').is(':checked') && $(this).val() !== $('#qac_address').val()) {
                            $('#qac_use_same_address').prop('checked', false);
                        }
                    });
                })();
                </script>
                {{-- ===== END QUICK ADD CUSTOMER MODAL JS ===== --}}
            @endpush
