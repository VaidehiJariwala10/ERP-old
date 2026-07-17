@extends('layout.app')

@section('title', 'Return Sale')

@section('content')
    <style>
        .d-none {
            display: none !important;
        }

        .img-flag {
            vertical-align: middle;
        }

        @media screen and (max-width: 768px) {
            .form-group {
                margin-bottom: 15px !important
            }
        }

        .table-responsive table td:nth-child(2),
        .table-responsive table th:nth-child(2) {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal !important;
            max-width: 250px;
            min-width: 150px;
        }

        .table-responsive table td:nth-child(2) a {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal !important;
            display: inline-block;
            max-width: 100%;
        }

        .mobile-detail-row .mobile-detail-value {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal !important;
        }

        .mobile-order-item .productimgname span {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal !important;
            display: inline-block;
            max-width: calc(100% - 50px);
        }

        .productimgname {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .productimgname a {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 0;
        }

        .productimgname a span {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal !important;
            flex: 1;
        }

        .productimgname img {
            flex-shrink: 0;
        }

        .table-responsive table td {
            white-space: nowrap;
        }

        .table-responsive table td:nth-child(2) {
            white-space: normal !important;
        }

        @media screen and (max-width: 1024px) {
            .table-responsive {
                overflow-x: visible;
            }

            .table-responsive table thead {
                display: none;
            }

            .table-responsive table,
            .table-responsive table tbody,
            .table-responsive table tr,
            .table-responsive table td {
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

            #product-table-body tr[data-product-id] > td {
                border: 0;
                padding: 0;
            }

            #product-table-body tr[data-product-id] > td:first-child {
                display: none;
            }

            #product-table-body tr[data-product-id] > td:nth-child(2) {
                /* display: grid; */
                grid-template-columns: 56px minmax(0, 1fr);
                gap: 12px;
                align-items: start;
                margin-bottom: 12px;
            }

            #product-table-body tr[data-product-id] > td:nth-child(2) a {
                width: 100%;
            }

            #product-table-body tr[data-product-id] > td:nth-child(2) img {
                width: 56px !important;
                height: 56px !important;
                object-fit: cover;
                border-radius: 10px;
                margin-right: 0 !important;
            }

            #product-table-body tr[data-product-id] > td:nth-child(2) span {
                display: block;
                font-weight: 600;
                line-height: 1.35;
                word-break: break-word;
            }

            #product-table-body tr[data-product-id] > td:not(:first-child):not(:nth-child(2)):not(:last-child) {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
                padding: 8px 0;
            }

            #product-table-body tr[data-product-id] > td:not(:first-child):not(:nth-child(2)):not(:last-child)::before {
                content: attr(data-label);
                flex: 0 0 110px;
                color: #6b7280;
                font-size: 12px;
                font-weight: 600;
                line-height: 1.4;
            }

            #product-table-body tr[data-product-id] > td:last-child {
                display: none;
            }

            #product-table-body tr[data-product-id] input.form-control {
                width: 120px !important;
                max-width: 100%;
                margin-left: auto;
                text-align: right;
            }

            #product-table-body tr[data-product-id] .discount-column,
            #product-table-body tr[data-product-id] .item-gst {
                /* text-align: right; */
                word-break: break-word;
                white-space: normal !important;
            }

            #product-table-body tr[data-product-id] > td[data-label="GST"] {
                align-items: flex-start;
            }

            #product-table-body tr[data-product-id] > td[data-label="GST"] .item-gst {
                flex: 1 1 auto;
                width: auto;
                margin-left: auto;
                text-align: right;
                display: block;
            }

            #product-table-body tr[data-product-id] > td[data-label="GST"] .item-gst > div {
                display: block;
                width: auto;
            }

            #product-table-body tr[data-product-id] > td[data-label="GST"] .item-gst small {
                display: block;
                line-height: 1.4;
                white-space: normal !important;
            }
        }
    </style>

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Create Return Sale</h4>
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(2, 'add') && app('hasPermission')(2, 'edit'))
                    <a href="{{ route('salesreturn.list') }}" class="btn btn-added">Back</a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Order Number</label>
                            <div class="row">
                                <div class="col-lg-12 col-sm-12 col-12">
                                    <select name="user_id" class="form-control select2-invoices">
                                        <option value="">Select Order Number</option>
                                        @foreach ($invoiceNumbers as $invoiceNumber)
                                            <option value="{{ $invoiceNumber }}">{{ $invoiceNumber }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Customer Name</label>
                            <div class="input-groupicon">
                                <input type="tel" id="customer_phone" class="form-control"
                                    placeholder="Customer Name" name="customer_phone"
                                    value="{{ $sales->user->phone ?? '' }}" readonly>
                                <span class="error_customerphone"></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-sm-6 col-12 d-none">
                        <div class="form-group">
                            <label>Product Name</label>
                            <div class="input-groupicon">
                                <div class="addonset">
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/scanner.svg' }}" alt="img">
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
                                    <th>Sold Qty</th>
                                    <th>Already Returned</th>
                                    <th>Return Qty</th>
                                    <th>Price</th>
                                    <th>Discount Amt</th>
                                    <th class="gst-column" style="display: none;">GST</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="product-table-body"></tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="row justify-content-end">
                        <div class="col-lg-6">
                            <div class="total-order w-100 max-widthauto m-auto mb-4">
                                <ul>
                                    <li class="order-subtotal">
                                        <h4>Subtotal</h4>
                                        <h5>
                                            @if ($currencyPosition == 'left')
                                                {{ $currencySymbol }} <span id="subtotal">0.00</span>
                                            @else
                                                <span id="subtotal">0.00</span> {{ $currencySymbol }}
                                            @endif
                                        </h5>
                                    </li>

                                    <li class="discount">
                                        <h4>Discount</h4>
                                        <h5 id="discount">0.00</h5>
                                    </li>

                                    <li class="after-discount">
                                        <h4>After Discount</h4>
                                        <h5>
                                            @if ($currencyPosition == 'left')
                                                {{ $currencySymbol }} <span id="after-discount">0.00</span>
                                            @else
                                                <span id="after-discount">0.00</span> {{ $currencySymbol }}
                                            @endif
                                        </h5>
                                    </li>

                                    {{-- Shipping row: dynamically added/removed by JS --}}

                                    {{-- GST row: dynamically added/removed by JS --}}

                                    <li class="total">
                                        <h4>Total</h4>
                                        <h5>
                                            @if ($currencyPosition == 'left')
                                                {{ $currencySymbol }} <span id="grand-total">0.00</span>
                                            @else
                                                <span id="grand-total">0.00</span> {{ $currencySymbol }}
                                            @endif
                                        </h5>
                                    </li>

                                    <li style="border-top:1px dashed #ddd; padding-top:10px;">
                                        <h4 style="color:#2E7D32;">Paid Amount</h4>
                                        <h5 id="paidAmountText" style="color:#2E7D32;font-weight:600;">
                                            @if ($currencyPosition == 'left')
                                                {{ $currencySymbol }}0.00
                                            @else
                                                0.00{{ $currencySymbol }}
                                            @endif
                                        </h5>
                                    </li>

                                    <li>
                                        <h4 style="color:#C62828;">Pending Amount</h4>
                                        <h5 id="pendingAmountText" style="color:#C62828;font-weight:600;">
                                            @if ($currencyPosition == 'left')
                                                {{ $currencySymbol }}0.00
                                            @else
                                                0.00{{ $currencySymbol }}
                                            @endif
                                        </h5>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-submit me-2">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true" id="submit-spinner"></span>
                            <span id="submit-text">Update Order</span>
                        </button>
                        <a href="{{ route('sales.list') }}" class="btn btn-cancel">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
$(document).ready(function () {

    // ─── Global Variables ────────────────────────────────────────────────────────
    const currencySymbol   = @json($currencySymbol);
    const currencyPosition = @json($currencyPosition);
    const authToken        = localStorage.getItem("authToken");

    let originalPaidAmount  = 0;
    let originalTotalAmount = 0;
    let shippingCharge      = 0;   // ← Set when invoice is loaded

    // ─── Helpers ─────────────────────────────────────────────────────────────────
    function formatCurrency(amount) {
        let num       = parseFloat(amount || 0);
        let formatted = num.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return currencyPosition === 'left'
            ? `${currencySymbol}${formatted}`
            : `${formatted}${currencySymbol}`;
    }

    function formatNumber(amount) {
        const num = parseFloat(amount || 0);

        return num.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function normalizeGstDetails(rawValue) {
        if (!rawValue) {
            return [];
        }

        let parsedValue = rawValue;

        if (typeof parsedValue === 'string') {
            try {
                parsedValue = JSON.parse(parsedValue);
            } catch (error) {
                return [];
            }
        }

        if (typeof parsedValue === 'string') {
            try {
                parsedValue = JSON.parse(parsedValue);
            } catch (error) {
                return [];
            }
        }

        if (Array.isArray(parsedValue)) {
            return parsedValue;
        }

        if (parsedValue && typeof parsedValue === 'object') {
            return Object.values(parsedValue);
        }

        return [];
    }

    /**
     * Returns true only when EVERY row's (returnQty + alreadyReturned) === originalQty.
     * This is the condition that unlocks shipping refund.
     */
    function areAllItemsFullyReturned() {
        let allFull = true;
        const epsilon = 0.0001;

        $('#product-table-body tr').each(function () {
            const $row          = $(this);
            const returnQty     = parseFloat($row.find('.quantity-input').val()) || 0;
            const originalQty   = parseFloat($row.data('original-qty'))          || 0;
            const availableQty  = parseFloat($row.data('available-qty'))         || 0;
            const alreadyReturned = originalQty - availableQty;

            if ((returnQty + alreadyReturned) < (originalQty - epsilon)) {
                allFull = false;
                return false; // break $.each
            }
        });

        return allFull;
    }

    // ─── Calculate Totals ────────────────────────────────────────────────────────
    function calculateTotals() {
        let subtotal            = 0;
        let totalDiscountAmount = 0;
        let totalGST            = 0;

        $('#product-table-body tr').each(function () {
            const $row                    = $(this);
            const quantity                = parseFloat($row.find('.quantity-input').val())                      || 0;
            const originalQty             = parseFloat($row.data('original-qty'))                               || 1;
            const price                   = parseFloat($row.find('.quantity-input').data('price'))              || 0;
            const originalDiscountAmount  = parseFloat($row.data('original-discount-amount'))                   || 0;
            const originalDiscountPct     = parseFloat($row.data('original-discount-percentage'))               || 0;
            const gstTotalPerItem         = parseFloat($row.find('.quantity-input').data('gst-total'))          || 0;

            // Pro-rate discount & GST
            const proRatedDiscount = (originalDiscountAmount / originalQty) * quantity;
            const proRatedGST      = (gstTotalPerItem         / originalQty) * quantity;

            const lineTotal = price * quantity;
            subtotal            += lineTotal;
            totalDiscountAmount += proRatedDiscount;
            totalGST            += proRatedGST;

            // ── Update Discount column (index 6) ──
            const $discountCol = $row.find('td:eq(6)');
            if (proRatedDiscount > 0) {
                $discountCol.html(
                    originalDiscountPct > 0
                        ? `${formatCurrency(proRatedDiscount)} (${originalDiscountPct.toFixed(2)}%)`
                        : formatCurrency(proRatedDiscount)
                );
            } else {
                $discountCol.html(formatCurrency('0.00'));
            }

            // ── Update GST column (index 7) ──
            const gstDetails = normalizeGstDetails($row.data('gst-details'));

            let gstHtml = '';
            if (gstDetails.length) {
                $.each(gstDetails, function (key, gst) {
                    const name          = gst.tax_name || key;
                    const rate          = gst.tax_rate  || 0;
                    const amount        = parseFloat(gst.tax_amount || 0);
                    const proRatedAmt   = (amount / originalQty) * quantity;
                    gstHtml += `<div><small>${name}(${rate}%): ${formatCurrency(proRatedAmt)}</small></div>`;
                });
            } else {
                gstHtml = proRatedGST > 0
                    ? formatCurrency(proRatedGST)
                    : formatCurrency('0.00');
            }
            $row.find('.item-gst').html(gstHtml);

            // ── Update Subtotal column (index 8) ──
            $row.find('td:eq(8)').text(formatCurrency(lineTotal));
        });

        // ── Summary panel ──────────────────────────────────────────────────────
        $('#subtotal').text(formatNumber(subtotal));

        // Discount display
        let discountText = '';
        if (totalDiscountAmount > 0) {
            discountText = formatCurrency(totalDiscountAmount);
        } else {
            discountText = formatCurrency('0.00');
        }
        $('#discount').text(discountText);

        const afterDiscount = subtotal - totalDiscountAmount;
        $('#after-discount').text(formatNumber(afterDiscount));

        // Remove dynamic rows before re-inserting
        $('.total-order ul').find('li.tax-row, li.shipping-row, li.round-off-row').remove();

        // GST row (inserted after after-discount)
        if (totalGST > 0) {
            $('.total-order ul .after-discount').after(
                `<li class="tax-row"><h4>Total GST</h4><h5>${formatCurrency(totalGST)}</h5></li>`
            );
        }

        // ── Shipping: only add when ALL items are fully returned ──────────────
        const shippingAmount = areAllItemsFullyReturned() ? shippingCharge : 0;

        if (shippingAmount > 0) {
            const shippingRow = `<li class="shipping-row"><h4>Shipping</h4><h5>${formatCurrency(shippingAmount)}</h5></li>`;
            // Insert after GST row if it exists, otherwise after after-discount
            if (totalGST > 0) {
                $('.total-order ul .tax-row').after(shippingRow);
            } else {
                $('.total-order ul .after-discount').after(shippingRow);
            }
        }

        // Round Off + Grand Total
        const rawGrandTotal = afterDiscount + totalGST + shippingAmount;
        const roundedGrandTotal = Math.round(rawGrandTotal);
        const roundOffAmount = roundedGrandTotal - rawGrandTotal;

        const roundOffRow = `<li class="round-off-row d-none"><h4>Round Off</h4><h5>${formatCurrency(roundOffAmount)}</h5></li>`;

        if (shippingAmount > 0) {
            $('.total-order ul .shipping-row').after(roundOffRow);
        } else if (totalGST > 0) {
            $('.total-order ul .tax-row').after(roundOffRow);
        } else {
            $('.total-order ul .after-discount').after(roundOffRow);
        }

        $('#grand-total').text(formatNumber(roundedGrandTotal));

        // Pending Amount
        const newPending = Math.max(0, (originalTotalAmount - roundedGrandTotal) - originalPaidAmount);
        $('#pendingAmountText').text(formatCurrency(newPending));
        $('#paidAmountText').text(formatCurrency(originalPaidAmount));
    }

    // ─── Select2 Init ────────────────────────────────────────────────────────────
    $('.select2-invoices').select2({
        placeholder: 'Select Order Number',
        allowClear:  true,
        width:       '100%'
    });

    // ─── Invoice Change → Load Products (SINGLE handler) ────────────────────────
    $('select[name="user_id"]').on('change', function () {
        const invoiceNumber     = $(this).val();
        const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

        if (!invoiceNumber) {
            // Reset everything when cleared
            $('#customer_phone').val('');
            $('#product-table-body').empty();
            shippingCharge      = 0;
            originalPaidAmount  = 0;
            originalTotalAmount = 0;
            calculateTotals();
            return;
        }

        $.ajax({
            url:  `/api/getSaleDetails/${invoiceNumber}`,
            type: 'GET',
            headers: { "Authorization": "Bearer " + authToken },
            data: { selectedSubAdminId: selectedSubAdminId },

            success: function (data) {
                if (data.error) { alert(data.error); return; }

                // ── Store order-level values ──────────────────────────────────
                shippingCharge      = parseFloat(data.order.shipping)          || 0;
                originalPaidAmount  = parseFloat(data.order.paid_amount)       || 0;
                originalTotalAmount = parseFloat(data.order.total_amount)      || 0;

                $('#customer_phone').val(data.order.user_name);
                $('#paidAmountText').text(formatCurrency(originalPaidAmount));
                $('#pendingAmountText').text(formatCurrency(parseFloat(data.order.remaining_amount) || 0));

                // ── Build product rows ────────────────────────────────────────
                const $tableBody = $('#product-table-body').empty();
                let hasGst = false;

                $.each(data.items, function (index, item) {
                    const price            = parseFloat(item.price)                  || 0;
                    const gstDetails       = normalizeGstDetails(item.product_gst_details);
                    const gstTotal         = parseFloat(item.product_gst_total)      || 0;
                    const soldQuantity     = parseFloat(item.sold_quantity)           || 0;
                    const availableQty     = parseFloat(item.quantity)               || 0;   // returnable
                    const returnedQuantity = parseFloat(item.returned_quantity)       || 0;

                    let discountAmount     = parseFloat(item.discount_amount)        || 0;
                    let discountPct        = parseFloat(item.discount_percentage)    || 0;

                    // Cross-calculate if one is missing
                    if (discountPct === 0 && discountAmount > 0 && price > 0) {
                        discountPct = (discountAmount / price) * 100;
                    }
                    if (discountAmount === 0 && discountPct > 0 && price > 0) {
                        discountAmount = (price * discountPct) / 100;
                    }

                    // Discount display for initial load (qty = 0, so show 0.00)
                    const discountDisplay = formatCurrency('0.00');

                    // GST column
                    if (gstTotal > 0 || gstDetails.length) {
                        hasGst = true;
                    }

                    let gstHtml = '';
                    if (gstDetails.length) {
                        $.each(gstDetails, function (key, gst) {
                            gstHtml += `<div><small>${gst.tax_name || key}(${gst.tax_rate || 0}%): ${formatCurrency('0.00')}</small></div>`;
                        });
                    } else {
                        gstHtml = formatCurrency('0.00');
                    }

                    const row = `
                        <tr data-product-id="${item.id}"
                            data-gst-details='${JSON.stringify(gstDetails)}'
                            data-gst-total="${gstTotal}"
                            data-original-qty="${soldQuantity}"
                            data-available-qty="${availableQty}"
                            data-original-discount-amount="${discountAmount}"
                            data-original-discount-percentage="${discountPct}">
                            <td>${index + 1}</td>
                            <td data-label="Product Name" style="width:100%;">
                                <a href="/product-view/${item.product_id}" style="display:flex;align-items:center;text-decoration:none;color:inherit;">
                                    <img src="${item.product_image
                                        ? '{{ env('ImagePath') }}/storage/' + item.product_image
                                        : '{{ env('ImagePath') }}/admin/assets/img/product/noimage.png'}"
                                        alt="${item.product_name}" width="40" height="40"
                                        style="object-fit:cover;border-radius:4px;margin-right:10px;flex-shrink:0;">
                                    <span style="flex:1;">${item.product_name || 'N/A'}</span>
                                </a>
                            </td>
                            <td data-label="Sold Qty">${parseFloat(soldQuantity).toFixed(2)}</td>
                            <td data-label="Already Returned">${parseFloat(returnedQuantity).toFixed(2)}</td>
                            <td data-label="Return Qty">
                                <input type="text" class="form-control quantity-input"
                                    value="0.00" step="0.01" min="0" max="${availableQty}"
                                    data-price="${price}"
                                    data-gst-total="${gstTotal}"
                                    data-original-discount-amount="${discountAmount}"
                                    data-original-discount-percentage="${discountPct}"
                                    data-original-qty="${soldQuantity}"
                                    style="width:80px;">
                            </td>
                            <td data-label="Price">${formatCurrency(price)}</td>
                            <td data-label="Discount Amt" class="discount-column">${discountDisplay}</td>
                            <td data-label="GST" class="item-gst gst-column">${gstHtml}</td>
                            <td data-label="Subtotal">${formatCurrency(0)}</td>
                            <td data-label="Action"></td>
                        </tr>`;
                    $tableBody.append(row);
                });

                // Show/hide GST column header + cells
                if (hasGst) {
                    $('.gst-column').show();
                } else {
                    $('.gst-column').hide();
                }

                calculateTotals();
            },

            error: function () {
                alert('Something went wrong while loading order details!');
            }
        });
    });

    // ─── Quantity Input Change ───────────────────────────────────────────────────
    $(document).on('input', '.quantity-input', function () {
        // Clamp value between 0 and max
        const max = parseFloat($(this).attr('max')) || 0;
        const rawValue = $(this).val().replace(/[^0-9.]/g, '');
        const parts = rawValue.split('.');
        const normalizedValue = parts.length > 2
            ? `${parts[0]}.${parts.slice(1).join('')}`
            : rawValue;

        let val = parseFloat(normalizedValue);

        if (normalizedValue !== $(this).val()) {
            $(this).val(normalizedValue);
        }

        if (isNaN(val)) {
            val = 0;
        }

        if (val < 0) {
            val = 0;
        }

        if (val > max) {
            val = max;
        }

        if (normalizedValue !== '' && !normalizedValue.endsWith('.')) {
            $(this).val(val.toString());
        }

        calculateTotals();
    });

    // ─── Submit Handler ──────────────────────────────────────────────────────────
    $(document).on('click', '.btn-submit', function (e) {
        e.preventDefault();

        const $btn     = $(this);
        const $spinner = $('#submit-spinner');
        const $text    = $('#submit-text');

        const allFullyReturned = areAllItemsFullyReturned();

        const formData = {
            products:    [],
            discount:    0,   // No additional discount input in this form
            shipping:    allFullyReturned ? shippingCharge : 0,
            grand_total: $('#grand-total').text().replace(/[^\d.]/g, '').trim()
        };

        $('#product-table-body tr').each(function () {
            const $row                   = $(this);
            const productId              = $row.data('product-id');
            const quantity               = parseFloat($row.find('.quantity-input').val()) || 0;
            const originalQty            = parseFloat($row.data('original-qty'))           || 1;
            const price                  = parseFloat($row.find('.quantity-input').data('price')) || 0;
            const originalDiscountAmount = parseFloat($row.data('original-discount-amount'))  || 0;
            const discountPct            = parseFloat($row.data('original-discount-percentage')) || 0;
            const gstTotalPerItem        = parseFloat($row.find('.quantity-input').data('gst-total')) || 0;

            const proRatedDiscount = (originalDiscountAmount / originalQty) * quantity;
            const proRatedGST      = (gstTotalPerItem         / originalQty) * quantity;

            const gstDetails = normalizeGstDetails($row.data('gst-details'));

            let proRatedGstDetails = [];
            if (gstDetails.length) {
                $.each(gstDetails, function (key, gst) {
                    const amount      = parseFloat(gst.tax_amount || 0);
                    const proRatedAmt = (amount / originalQty) * quantity;
                    proRatedGstDetails.push({
                        tax_name:   gst.tax_name  || key,
                        tax_rate:   gst.tax_rate  || 0,
                        tax_amount: proRatedAmt.toFixed(2)
                    });
                });
            }

            if (productId && quantity > 0) {
                formData.products.push({
                    order_item_id:       productId,
                    quantity:            quantity,
                    price:               price,
                    subtotal:            (price * quantity).toFixed(2),
                    discount_amount:     proRatedDiscount.toFixed(2),
                    discount_percentage: discountPct,
                    product_gst_details: proRatedGstDetails,
                    product_gst_total:   proRatedGST.toFixed(2)
                });
            }
        });

        if (formData.products.length === 0) {
            Swal.fire({
                title: 'Error',
                text:  'Please enter a return quantity greater than 0 for at least one product.',
                icon:  'error',
                confirmButtonText:  'OK',
                confirmButtonColor: '#ffa957'
            });
            return;
        }

        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        $text.text('Updating...');

        $.ajax({
            url:  '/api/return_sale',
            type: 'POST',
            headers: { "Authorization": "Bearer " + authToken },
            data: formData,

            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success',
                        text:  response.message,
                        icon:  'success',
                        confirmButtonText:  'OK',
                        confirmButtonColor: '#ffa957'
                    }).then(result => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('salesreturn.list') }}";
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text:  response.message || 'Something went wrong.',
                        icon:  'error',
                        confirmButtonText:  'OK',
                        confirmButtonColor: '#ffa957'
                    });
                    $btn.prop('disabled', false);
                    $spinner.addClass('d-none');
                    $text.text('Update Order');
                }
            },

            error: function () {
                Swal.fire({
                    title: 'Error',
                    text:  'An error occurred while updating the order.',
                    icon:  'error',
                    confirmButtonText:  'OK',
                    confirmButtonColor: '#ffa957'
                });
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $text.text('Update Order');
            }
        });
    });

}); // end document.ready
</script>
@endpush
