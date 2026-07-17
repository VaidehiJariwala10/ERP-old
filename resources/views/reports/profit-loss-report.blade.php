@extends('layout.app')

@section('title', 'Profit Loss Report')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
@php
    $showProfitLossChart = \App\Services\StaffDepartmentScope::canShowReportChart(\App\Services\StaffDepartmentScope::REPORT_PROFIT_LOSS);
@endphp
<style>
    .summary-row {
        row-gap: 8px;
    }

    .dash-count {
        padding: 8px !important;
        border-radius: 8px;
        min-height: 70px;
        height: 100%;
    }

    .dash-count .card-body {
        padding: 10px !important;
    }

    .filter-card .card-body {
        padding: 1rem;
    }

    .filter-card .form-label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        line-height: 1.3;
    }

    .filter-card .select2-container {
        width: 100% !important;
    }

    .filter-card .select2-container .select2-selection--single {
        height: 38px;
        display: flex;
        align-items: center;
        border: 1px solid #ced4da;
    }

    .filter-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        padding-right: 28px;
        font-size: 14px;
    }

    .filter-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    @media (max-width: 576px) {
        .product-name {
            font-size: 12px;
        }
    }

    .product-name {
        line-height: 1.4;
        margin-bottom: 5px;
    }

    .summary-card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .summary-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        font-size: 18px;
        flex-shrink: 0;
    }

    .summary-value {
        margin: 0;
        line-height: 1.2;
    }

    @media (max-width: 576px) {
        .content > .card > .card-body {
            padding: 12px;
        }

        .summary-row {
            margin-left: -4px !important;
            margin-right: -4px !important;
        }

        .summary-row > [class*="col-"] {
            padding-left: 4px !important;
            padding-right: 4px !important;
        }

        .dash-count {
            min-height: 94px;
        }

        .dash-count .card-body {
            padding: 10px 8px !important;
        }

        .summary-card-body {
            align-items: flex-start;
            gap: 8px;
        }

        .summary-card-body h6 {
            font-size: 12px !important;
            line-height: 1.35;
            margin-bottom: 6px !important;
        }

        .summary-icon {
            width: 32px;
            height: 32px;
            font-size: 14px;
            margin-top: 2px;
        }

        .summary-value {
            font-size: 1.2rem;
            word-break: break-word;
        }

        .filter-card .card-body {
            padding: 14px 10px;
        }

        .filter-card .row {
            margin-left: 0;
            margin-right: 0;
            justify-content: center;
        }

        .filter-card .row > [class*="col-"] {
            padding-left: 5px;
            padding-right: 5px;
            margin-bottom: 12px !important;
        }

        .filter-card .form-label {
            font-size: 13px;
            min-height: 34px;
            margin-bottom: 5px;
            padding-left: 2px;
            padding-right: 2px;
            text-align: left;
        }

        .filter-card .form-control,
        .filter-card .select2-container {
            margin-left: auto;
            margin-right: auto;
        }

        .filter-card .select2-container .select2-selection--single {
            width: 100%;
        }

        #product_legend {
            margin-left: 12px !important;
            margin-right: 12px !important;
        }

        .card-header h5 {
            font-size: 18px;
            line-height: 1.4;
        }

        .card-header .btn-group {
            width: 100%;
            margin-top: 8px;
        }

        .card-header .btn-group .btn {
            width: 100%;
        }
    }

    @media (min-width: 768px) and (max-width: 912px) {
        .report-filter-col {
            flex: 0 0 50% !important;
            max-width: 50% !important;
            padding-left: 15px !important;
            padding-right: 15px !important;
            margin-bottom: 20px !important;
        }
        .report-filter-col .form-label {
            margin-bottom: 8px !important;
            font-size: 15px !important;
        }
    }

    @media (min-width: 913px) and (max-width: 1280px) {
        .report-filter-col {
            flex: 0 0 25% !important;
            max-width: 25% !important;
            margin-bottom: 10px !important;
        }

        .summary-card-body {
            padding: 10px 8px !important;
            gap: 8px !important;
        }

        .summary-card-body h6 {
            font-size: 11px !important;
            margin-bottom: 5px !important;
        }

        .summary-value {
            font-size: 1.15rem !important;
        }

        .summary-icon {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }

        .filter-card .form-label {
            font-size: 13px !important;
            margin-bottom: 4px !important;
        }
    }
</style>
    <div class="content">
        <div class="card">
            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row mb-2 mx-n1 summary-row">
                    <div class="col-6 col-md-3 mb-2 px-1">
                        <a href="{{ route('sales.list') }}">
                        <div class="card shadow-sm border-0 dash-count das2 p-0 mb-0">
                            <div class="card-body summary-card-body">
                                <div>
                                    <h6 class="text-uppercase mb-2" style="font-size: 14px; opacity: 0.9;">Total Sales</h6>
                                    <h3 class="summary-value">&#8377;<span id="total_sales">0.00</span></h3>
                                </div>
                                <span class="summary-icon" aria-hidden="true">
                                    <i class="fa fa-shopping-cart"></i>
                                </span>
                            </div>
                        </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3 mb-2 px-1">
                        <a href="{{ route('purchase.lists') }}">
                        <div class="card shadow-sm border-0 dash-count das3 p-0 mb-0">
                            <div class="card-body summary-card-body">
                                <div>
                                    <h6 class="text-uppercase mb-2" style="font-size: 14px; opacity: 0.9;">Total Purchase</h6>
                                    <h3 class="summary-value">&#8377;<span id="total_purchase">0.00</span></h3>
                                </div>
                                <span class="summary-icon" aria-hidden="true">
                                    <i class="fa fa-shopping-bag"></i>
                                </span>
                            </div>
                        </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3 mb-2 px-1">
                        <div class="card shadow-sm border-0 dash-count p-0 mb-0">
                            <div class="card-body summary-card-body">
                                <div>
                                    <h6 class="text-uppercase mb-2" style="font-size: 14px; opacity: 0.9;">Total Loss</h6>
                                    <h3 class="summary-value">&#8377;<span id="total_loss_amount">0.00</span></h3>
                                </div>
                                <span class="summary-icon" aria-hidden="true">
                                    <i class="fa fa-arrow-down"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2 px-1">
                        <div class="card shadow-sm border-0 dash-count das1 p-0 mb-0">
                            <div class="card-body summary-card-body">
                                <div>
                                    <h6 class="text-uppercase mb-2" style="font-size: 14px; opacity: 0.9;">Total Profit</h6>
                                    <h3 class="summary-value">&#8377;<span id="total_profit_amount">0.00</span></h3>
                                </div>
                                <span class="summary-icon" aria-hidden="true">
                                    <i class="fa fa-arrow-up"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($showProfitLossChart)
                <!-- Chart Section -->
                <div class="card shadow-sm mb-4 filter-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fa fa-chart-line text-primary mr-2"></i>
                            Product-wise Profit & Loss Report
                        </h5>
                        <div class="btn-group">
                            <a href="{{ route('profit-loss-report.pdf') }}" id="pdf_export_btn"
                                class="btn btn-sm btn-outline-danger">
                                <i class="fa fa-file-pdf mr-1"></i> PDF Export
                            </a>
                        </div>

                    </div>
                    <!-- Product Color Legend -->
                    <div id="product_legend" class="mt-2 ms-4">
                        <h6 class="mb-1"><strong>Product Colors:</strong></h6>
                        <div id="legend_items" class="row" style="gap: 10px;">
                            <!-- Legend items will be populated here -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="height: 350px;">
                            <canvas id="profitLossChart"></canvas>
                        </div>
                        <div id="no_data_alert" class="alert alert-info mt-3 d-none">
                            No data found for the selected filters.
                        </div>


                    </div>
                </div>
                @endif

                <!-- Filter Section -->
                <div class="card shadow-sm mb-4 filter-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 col-6 mb-3 report-filter-col">
                                <label class="form-label font-weight-bold"><strong>Time Period</strong></label>
                                <select class="form-control" id="time_period">
                                    <option value="all_time">All Time</option>
                                    <option value="this_week">This Week</option>
                                    <option value="this_month">This Month</option>
                                    <option value="last_6_months">Last 6 Months</option>
                                    <option value="this_year">This Year</option>
                                    <option value="previous_year">Previous Year</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-6 mb-3 report-filter-col">
                                <label class="form-label font-weight-bold"><strong>Product</strong></label>
                                <select class="form-control" id="product_id">
                                    <option value="all" selected>All Products</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-6 mb-3 report-filter-col">
                                <label class="form-label font-weight-bold"><strong>Vendor</strong></label>
                                <select class="form-control" id="vendor_id">
                                    <option value="all" selected>All Vendors</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" data-phone="{{ $vendor->phone ?? '' }}">{{ $vendor->name }}{{ $vendor->phone ? ' - ' . $vendor->phone : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-6 mb-3 report-filter-col">
                                <label class="form-label font-weight-bold"><strong>Customer</strong></label>
                                <select class="form-control" id="customer_id">
                                    <option value="all" selected>All Customers</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" data-phone="{{ $customer->phone ?? '' }}">{{ $customer->name }}{{ $customer->phone ? ' - ' . $customer->phone : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-6 mb-3 report-filter-col">
                                <label class="form-label font-weight-bold"><strong>Select Year</strong></label>
                                <select class="form-control" id="year">
                                    <option value="all" selected>All Years</option>
                                    @for ($i = date('Y'); $i >= date('Y') - 10; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2 col-6 mb-3 report-filter-col">
                                <label class="form-label font-weight-bold"><strong>Select Month</strong></label>
                                <select class="form-control" id="month">
                                    <option value="all" selected>All Months</option>
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Filter button removed: auto-filtering enabled -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection


@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        function formatCurrency(amount) {
            return parseFloat(amount).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
        const rupeeSymbol = '\u20B9';
        let profitLossChart = null;

        $(document).ready(function() {
            let isSyncingPartyFilters = false;

            // Initialize Select2 on all filter selects
            $('#time_period, #product_id, #year, #month').select2({
                placeholder: 'Select an option',
                width: '100%'
            });

            // Vendor and Customer: phone-aware matcher
            function phoneNameMatcher(params, data) {
                if ($.trim(params.term) === '') return data;
                if (typeof data.text === 'undefined') return null;
                var term      = params.term.toLowerCase();
                var text      = (data.text || '').toLowerCase();
                var phone     = ($(data.element).data('phone') || '').toString().toLowerCase();
                var normTerm  = term.replace(/[^0-9a-z]/g, '');
                var normPhone = phone.replace(/[^0-9]/g, '');
                if (text.indexOf(term) > -1 || phone.indexOf(term) > -1) return data;
                if (normTerm && normPhone.indexOf(normTerm) > -1) return data;
                return null;
            }

            $('#vendor_id').select2({
                placeholder: 'Select an option',
                width: '100%',
                matcher: phoneNameMatcher
            });

            $('#customer_id').select2({
                placeholder: 'Select an option',
                width: '100%',
                matcher: phoneNameMatcher
            });

            loadData();

            $('#vendor_id').on('change', function() {
                if (isSyncingPartyFilters) {
                    return;
                }

                const vendorId = $(this).val();
                if (vendorId !== 'all' && $('#customer_id').val() !== 'all') {
                    isSyncingPartyFilters = true;
                    $('#customer_id').val('all').trigger('change.select2');
                    isSyncingPartyFilters = false;
                }
            });

            $('#customer_id').on('change', function() {
                if (isSyncingPartyFilters) {
                    return;
                }

                const customerId = $(this).val();
                if (customerId !== 'all' && $('#vendor_id').val() !== 'all') {
                    isSyncingPartyFilters = true;
                    $('#vendor_id').val('all').trigger('change.select2');
                    isSyncingPartyFilters = false;
                }
            });

            // Auto-filter when any filter input changes
            $('#time_period, #product_id, #vendor_id, #customer_id, #year, #month').on('change', function() {
                loadData();
            });

            // Handle PDF link update with filters
            $('#pdf_export_btn').click(function(e) {
                e.preventDefault();
                let baseUrl = "{{ route('profit-loss-report.pdf') }}";
                let productId = $('#product_id').val();
                let vendorId = $('#vendor_id').val();
                let customerId = $('#customer_id').val();
                let year = $('#year').val();
                let month = $('#month').val();

                let params = $.param({
                    product_id: productId === 'all' ? '' : productId,
                    vendor_id: vendorId === 'all' ? '' : vendorId,
                    customer_id: customerId === 'all' ? '' : customerId,
                    year: year === 'all' ? '' : year,
                    month: month === 'all' ? '' : month,
                    time_period: $('#time_period').val()
                });
                window.open(baseUrl + '?' + params, '_blank');
            });
        });

        function loadData() {
            let productId = $('#product_id').val();
            let vendorId = $('#vendor_id').val();
            let customerId = $('#customer_id').val();
            let year = $('#year').val();
            let month = $('#month').val();

            const params = {
                product_id: productId === 'all' ? '' : productId,
                vendor_id: vendorId === 'all' ? '' : vendorId,
                customer_id: customerId === 'all' ? '' : customerId,
                year: year === 'all' ? '' : year,
                month: month === 'all' ? '' : month,
                time_period: $('#time_period').val()
            };
            console.log('Requesting profit-loss data with params:', params);
            $.ajax({
                url: "{{ route('profit-loss-report.data') }}",
                type: "GET",
                data: params,
                success: function(res) {
                    console.log('Profit-loss response:', res);

                    // Normalize data
                    const breakdown = Array.isArray(res.product_breakdown) ? res.product_breakdown : [];
                    window.currentBreakdownData = breakdown;

                    const totalSales = parseFloat(res.total_sales || 0);
                    const totalPurchase = parseFloat(res.total_purchase || 0);
                    const totalProfit = parseFloat(res.total_profit_amount || 0);
                    const totalLoss = parseFloat(res.total_loss_amount || 0);

                    $('#total_sales').text(formatCurrency(totalSales));
                    $('#total_purchase').text(formatCurrency(totalPurchase));
                    $('#total_profit_amount').text(formatCurrency(totalProfit));
                    $('#total_loss_amount').text(formatCurrency(totalLoss));

                    // Determine if there's any meaningful data to show
                    const hasData = (breakdown.length > 0) || totalSales > 0 || totalPurchase > 0 ||
                        totalProfit > 0 || totalLoss > 0;

                    if (!hasData) {
                        $('#no_data_alert').removeClass('d-none');
                    } else {
                        $('#no_data_alert').addClass('d-none');
                    }

                    // Update chart with product-wise data (clears if empty)
                    updateChart();
                },
                error: function(xhr) {
                    console.error('Profit-loss request failed:', xhr);
                    // Show generic no-data/error message
                    $('#no_data_alert').removeClass('d-none').text('No data found for the selected filters.');

                    // Reset summary cards and chart
                    $('#total_sales').text(formatCurrency(0));
                    $('#total_purchase').text(formatCurrency(0));
                    $('#total_profit_amount').text(formatCurrency(0));
                    $('#total_loss_amount').text(formatCurrency(0));
                    window.currentBreakdownData = [];
                    updateChart();
                }
            });
        }

        function updateChart() {
            const chartEl = document.getElementById('profitLossChart');
            if (!chartEl) {
                return;
            }

            if (profitLossChart) {
                profitLossChart.destroy();
            }

            const ctx = document.getElementById('profitLossChart').getContext('2d');

            // Dynamic color palette for products
            const colorPalette = [
                '#FFD700', // Yellow
                '#FF8C00', // Orange
                '#4169E1', // Blue
                '#8B4513', // Brown
                '#FF1493', // Deep Pink
                '#32CD32', // Lime Green
                '#DC143C', // Crimson
                '#FF69B4', // Hot Pink
                '#00CED1', // Dark Turquoise
                '#9370DB', // Medium Purple
                '#3CB371', // Medium Sea Green
                '#FF6347', // Tomato
                '#20B2AA', // Light Sea Green
                '#DEB887', // Burlywood
                '#48D1CC' // Medium Turquoise
            ];

            // Create a map to store product names and their assigned colors
            if (!window.productColorMap) {
                window.productColorMap = {};
            }

            let colorIndex = Object.keys(window.productColorMap).length;

            // Prepare product-wise data from breakdown
            const productLabels = [];
            const profitLossData = [];
            const pointColors = [];

            if (window.currentBreakdownData && window.currentBreakdownData.length > 0) {
                window.currentBreakdownData.forEach(item => {
                    // productLabels.push(item.name);
                    // productLabels.push(item.name.match(/.{1,12}/g));
                    productLabels.push(
                        item.name.length > 10 
                            ? item.name.substring(0, 10) + '...' 
                            : item.name
                    );
                    profitLossData.push(parseFloat(item.profit));

                    // Assign color if product not yet mapped, otherwise use existing color
                    if (!window.productColorMap[item.name]) {
                        window.productColorMap[item.name] = colorPalette[colorIndex % colorPalette.length];
                        colorIndex++;
                    }
                    pointColors.push(window.productColorMap[item.name]);
                });
            }

            profitLossChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: productLabels,
                    datasets: [{
                        label: 'Profit / Loss',
                        data: profitLossData,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        pointBackgroundColor: pointColors,
                        pointBorderColor: pointColors,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        fill: false,
                        tension: 0.4,
                        cubicInterpolationMode: 'monotone'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Products'
                            },
                            ticks: {
                            maxRotation: 45,   // rotate labels
                            minRotation: 45,
                            autoSkip: true,    // skip extra labels
                            maxTicksLimit: 5,  // control how many show
                            font: {
                                size: 10       // smaller text
                            }
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: `Profit / Loss Amount (${rupeeSymbol})`
                            },
                            beginAtZero: false,
                            grid: {
                                color: function(context) {
                                    if (context.tick.value === 0) {
                                        return '#000000';
                                    }
                                    return '#e9ecef';
                                },
                                lineWidth: function(context) {
                                    if (context.tick.value === 0) {
                                        return 2;
                                    }
                                    return 1;
                                }
                            },
                            ticks: {
                                callback: function(value) {
                                    return rupeeSymbol + formatCurrency(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed.y;
                                    const label = value >= 0 ? 'Profit: ' + rupeeSymbol + formatCurrency(value) : 'Loss: ' + rupeeSymbol + formatCurrency(Math.abs(value));
                                    return label;
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });

            // Generate product color legend
            let legendHtml = '';
            if (window.currentBreakdownData && window.currentBreakdownData.length > 0) {
                window.currentBreakdownData.forEach(item => {
                    const color = window.productColorMap[item.name] || '#999999';
                    legendHtml += `
                <div class="col-auto mb-2">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; background-color: ${color}; border: 2px solid #333; border-radius: 3px;"></div>
                        <span style="font-size: 14px;">${item.name}</span>
                    </div>
                </div>
            `;
                });
            }
            $('#legend_items').html(legendHtml);
        }
    </script>
@endpush
