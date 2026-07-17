
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice PDF</title>
    <style>
        @page {
            size: A4;
            margin: 2mm;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            /* Set base font size here */
            margin: 0;
            padding: 0;
            background: white;
        }

        .pdf-wrapper {
            margin-top: 3mm;
            /* ensures top border is visible */
        }

        .card-body {
            width: 95%;
            /* min-height: auto; */
            min-height: 283mm;
            padding: 3mm;
            margin: auto;
            box-sizing: border-box;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid black; /* thicker border */
            font-size: 12px;
            position: relative;
        }

        table,
        table td,
        table th {
            font-size: inherit;
            /* Make sure tables inherit the font size */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* margin-bottom: 20px; */
        }

        .header-table {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            margin-bottom: 30px;
        }

        .header-table td {
            padding: 10px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
            padding: 4px 8px;
        }

        .table-bordered thead tr {
            background-color: #e9ecf0ff;
            color: #333;
        }

        .table-bordered tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table-bordered tfoot tr {
            /* font-weight: bold; */
            /* background-color: #e9ecef; */
        }

        .text-end {
            /* text-align: right; */
        }

        .text-center {
            text-align: center;
        }

        .mb-0 {
            /* margin-bottom: 0; */
        }

        h3,
        h4 {
            margin: 0 0 10px 0;
            color: #343a40;
        }

    .qr-details-box {
    text-align: center;
    vertical-align: middle;

}

.qr-details-box img {
    display: block;
    margin: auto 10px ; /* auto handles left & right center */
}

        .invoice-title {
            /* text-transform: uppercase; */
            /* letter-spacing: 2px; */
            /* border-bottom: 2px solid #e5e7ebff; */
            /* display: inline-block; */
            /* padding-bottom: 5px; */
            /* margin-bottom: 20px; */
        }

        .logo-container {
            /* position: relative; */
            /* min-height: 50px; */
            /* margin-bottom: 10px; */
        }

        /* .logo-container .qr-code {
            height: 60px;
            position: absolute;
            top: 0;
            left: 0;
        } */

        /* .logo-container .company-logo {
            height: 50px;
            position: absolute;
            top: 0;
            left: 0;
        } */

        /* .logo-container .company-details {
            text-align: center;
        } */


        /* .signature-section img {
            height: 50px;
            margin-top: 5px;
        } */

        /* .signature-section {
            margin-top: 50px;
            text-align: right;
        } */
         /* ===== PRODUCT NAME WRAP FIX ===== */

         


        @php
            $totalthing = count($orderItems);
            $footerBottom = ($totalthing > 5) ? 60 : 45;
        @endphp

        .footer-section {
            position: absolute;
            bottom: 20px;
            left: 0;
            width: 100%;
        }

        .invoice-label-nowrap {
            white-space: nowrap;
            word-break: keep-all;
            width: 58px;
        }

        .emi-pdf-box {
            margin-top: 8px;
            border: 1px solid #1b2850;
            background: #f7f9fc;
            page-break-inside: avoid;
        }

        .emi-pdf-blank-space {
            width: 100%;
            margin: 8px 0 8px 0;
        }

        .emi-pdf-title {
            padding: 5px 7px;
            background: #1b2850;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .emi-pdf-body {
            padding: 6px 7px;
            font-size: 9px;
            color: #222;
        }

        .emi-pdf-layout {
            width: 100%;
            border-collapse: collapse;
        }

        .emi-pdf-layout td {
            vertical-align: top;
            border: none;
            padding: 0;
        }

        .emi-pdf-summary-cell {
            width: 34%;
            padding-right: 8px !important;
        }

        .emi-pdf-schedule-cell {
            width: 66%;
        }

        .emi-pdf-formula {
            font-size: 11px;
            font-weight: bold;
            color: #1b2850;
            margin-bottom: 4px;
        }

        .emi-pdf-meta {
            font-size: 8.5px;
            color: #444;
            line-height: 1.45;
        }

        .emi-pdf-note {
            margin-top: 5px;
            padding: 4px 5px;
            background: #fff8e9;
            border: 1px solid #ffd89a;
            color: #5d4300;
            font-size: 8px;
            line-height: 1.35;
        }

        .emi-schedule-table {
            margin-top: 0;
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }

        .emi-schedule-table th {
            background: #e8edf6;
            color: #1b2850;
            border: 1px solid #cbd3df;
            padding: 3px;
            text-align: left;
        }

        .emi-schedule-table td {
            border: 1px solid #d7dde6;
            padding: 3px;
        }

        .emi-status-paid {
            color: #2E7D32;
            font-weight: bold;
        }

        .emi-status-pending {
            color: #C62828;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="card-body">
        <table style="width:100%; margin-bottom: 10px; border-collapse: collapse;">
            <tr>
                <td style="width: 150px; vertical-align: top;">
                    @php
                        $logoBase64 = local_image_to_base64(
                            $setting->logo ?? null,
                            'admin/assets/img/logo-image.jpg'
                        );
                    @endphp
                    @if ($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Company Logo"
                            style="height: 60px; width: auto;">
                    @endif
                </td>
                <td style="vertical-align: middle; padding-left: 15px; text-align: right; word-wrap: break-word; white-space: normal; max-width: 300px;">
                    <h3 style="margin: 0; text-transform: uppercase;">{{ $setting->name ?? '' }}</h3>
                    <small style="text-transform: uppercase; display: block;">
                        {{ $setting->address ?? '' }}<br>
                        Phone: {{ $setting->phone ?? '' }} |
                        Email: <span style="text-transform: none;">{{ $setting->email ?? '' }}</span>
                    </small>
                </td>
            </tr>
        </table>

        <hr style="height: 2px; background-color: #d7cdcd; border: none; margin-top: 0; margin-bottom: 12px;">




        <table style="width:100%; border-collapse: collapse; font-size: 10px; margin-bottom: 6px; table-layout: fixed;">
            <tr>
                <td
                    style="width:33%; position: relative; padding: 5px 8px; vertical-align: top; background-color: #eaedf0; border-right: 1px solid #ff9f43;">
                    <strong style="text-transform: uppercase; display: block; margin-bottom: 6px;">Customer
                        Details:</strong>
                    <table style="width:100%; border-collapse: collapse; font-size: 10px; color: inherit;">
                        <tr>
                            <td style="padding: 0 0 4px 0;">Name :</td>
                            <td style="text-align: right; padding: 0 0 4px 0;">
                                {{ $customer['name'] ?? 'walk-in-customer' }}
                            </td>
                        </tr>
                        @if (!empty($customer['company_name']))
                            <tr>
                                <td style="padding: 0 0 4px 0;">Company Name :</td>
                                <td style="text-align: right; padding: 0 0 4px 0;">{{ $customer['company_name'] }}</td>
                            </tr>
                        @endif
                        @if (!empty($customer['phone']))
                            <tr>
                                <td style="padding: 0 0 4px 0;">Phone :</td>
                                <td style="text-align: right; padding: 0 0 4px 0;">{{ $customer['phone'] }}</td>
                            </tr>
                        @endif
                        @if (!empty($customer['email']))
                            <tr>
                                <td style="padding: 0 0 4px 0;">Email :</td>
                                <td style="text-align: right; padding: 0 0 4px 0;">{{ $customer['email'] }}</td>
                            </tr>
                        @endif
                        @if (!empty($customer['address']))
                            <tr>
                                <td class="invoice-label-nowrap" style="padding: 0 0 4px 0; vertical-align: top;">Address :</td>
                                <td style="text-align: right; padding: 0 0 4px 0; word-wrap: break-word; white-space: normal;">{{ $customer['address'] }}</td>
                            </tr>
                        @endif
                        @if (!empty($customer['delivery_address']))
                            <tr>
                                <td class="invoice-label-nowrap" style="padding: 0 0 4px 0; vertical-align: top;">Delivery Address :</td>
                                <td style="text-align: right; padding: 0 0 4px 0; word-wrap: break-word; white-space: normal;">{{ $customer['delivery_address'] }}</td>
                            </tr>
                        @endif
                        @if (!empty($customer['gst_number']))
                            <tr>
                                <td style="padding: 0 0 4px 0;">GST :</td>
                                <td style="text-align: right; padding: 0 0 4px 0;">{{ $customer['gst_number'] }}</td>
                            </tr>
                        @endif
                        @if (!empty($customer['pan_number']))
                            <tr>
                                <td style="padding: 0 0 4px 0;">PAN :</td>
                                <td style="text-align: right; padding: 0 0 4px 0;">{{ $customer['pan_number'] }}</td>
                            </tr>
                        @endif
                    </table>

                </td>

                <!-- Vehicle Details -->
                <td
                    style="width:33%; position: relative; padding: 8px 12px; vertical-align: top; background-color: #eaedf0; border-right: 1px solid #ff9f43;">
                    <strong style="text-transform: uppercase; display: block;margin-bottom: 2px">Company
                        Details:</strong>
                    <table style="width:100%; border-collapse: collapse; font-size: 10px; color: inherit;">
                        @if (!empty($setting->name))
                            <tr>
                                <td style="padding: 0 0 2px 0;">Name :</td>
                                <td style="text-align: right; padding: 0 0 2px 0;">{{ $setting->name }}</td>
                            </tr>
                        @endif

                        @if (!empty($setting->email))
                            <tr>
                                <td style="padding: 0 0 2px 0;">Email :</td>
                                <td style="text-align: right; padding: 0 0 2px 0;">{{ $setting->email }}</td>
                            </tr>
                        @endif

                        @if (!empty($setting->phone))
                            <tr>
                                <td style="padding: 0 0 2px 0;">Phone :</td>
                                <td style="text-align: right; padding: 0 0 2px 0;">{{ $setting->phone }}</td>
                            </tr>
                        @endif

                        @if (!empty($setting->address))
                            <tr>
                                <td class="invoice-label-nowrap" style="padding: 0 0 2px 0; vertical-align: top;">Address :</td>
                                <td style="text-align: right; padding: 0 0 2px 0; word-wrap: break-word; white-space: normal;">{{ $setting->address }}</td>
                            </tr>
                        @endif

                        @if (!empty($setting->gst_num))
                            <tr>
                                <td style="padding: 0 0 2px 0;">GST :</td>
                                <td style="text-align: right; padding: 0 0 2px 0;">{{ $setting->gst_num }}</td>
                            </tr>
                        @endif
                    </table>

                </td>
                @php
                    $isQuotation      = ($sales->quotation_status ?? '') === 'quotation';
                    $isAdvanceReceipt = ($sales->quotation_status ?? '') === 'advance_receipt';
                    $paymentMethodRaw = strtolower(trim((string) ($sales->payment_method ?? '')));
                    $paymentMethodLabels = [
                        'cash' => 'Cash',
                        'online' => 'Online',
                        'cash+online' => 'Cash+Online',
                        'cash_online' => 'Cash+Online',
                        'cash + online' => 'Cash+Online',
                        'cash+bank' => 'Cash+Online',
                        'cash_bank' => 'Cash+Online',
                        'cash + bank' => 'Cash+Online',
                        'emi' => 'EMI',
                    ];
                    $paymentMethodLabel = $paymentMethodLabels[$paymentMethodRaw]
                        ?? ($paymentMethodRaw !== '' ? ucwords(str_replace(['_', '-'], ' ', $paymentMethodRaw)) : '-');
                @endphp


                <!-- Invoice Details -->
                <td style="width:34%; border: 0px solid #dee2e6; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">

                    <strong style="text-transform: uppercase; display: block; margin-bottom: 2px;">
                        @if($isQuotation)
                            Quotation Info:
                        @elseif($isAdvanceReceipt)
                            Advance Receipt Info:
                        @else
                            Order Details:
                        @endif
                    </strong>

                    <table style="width:100%; border-collapse: collapse; font-size: 10px; color: inherit;">

                        {{-- Number --}}
                        <tr>
                            <td style="padding: 0 0 2px 0;">
                                @if($isQuotation)
                                    Quotation Number :
                                @elseif($isAdvanceReceipt)
                                    Advance Receipt No :
                                @else
                                    Order Number :
                                @endif
                            </td>
                            <td style="text-align: right; padding: 0 0 2px 0;">
                                {{ $sales->order_number ?? '-' }}
                            </td>
                        </tr>

                        {{-- Date --}}
                        <tr>
                            <td style="padding: 0 0 2px 0;">
                                @if($isQuotation)
                                    Quotation Date :
                                @elseif($isAdvanceReceipt)
                                    Receipt Date :
                                @else
                                    Order Date :
                                @endif
                            </td>
                            <td style="text-align: right; padding: 0 0 2px 0;">
                                {{ !empty($sales->created_at) ? date('d-M-Y', strtotime($sales->created_at)) : '-' }}
                            </td>
                        </tr>

                        {{-- Sales Person --}}
                        <tr>
                            <td style="padding: 0 0 2px 0;">Sales Person :</td>
                            <td style="text-align: right; padding: 0 0 2px 0;">
                                {{ $sales->staff->name ?? $sales->creator->name ?? '-' }}
                            </td>
                        </tr>

                        {{-- Payment Method --}}
                        <tr>
                            <td style="padding: 0 0 2px 0;">Payment Method :</td>
                            <td style="text-align: right; padding: 0 0 2px 0;">
                                {{ $paymentMethodLabel }}
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>

        @php
            $hasGst = false;
            $hasDiscount = false;

            foreach ($orderItems as $item) {
                if (
                    (!empty($item->product_gst_total) && $item->product_gst_total > 0) ||
                    (!empty($item->product_gst_details) && is_array($item->product_gst_details))
                ) {
                    $hasGst = true;
                }

                if ((float) ($item->discount_amount ?? 0) > 0) {
                    $hasDiscount = true;
                }
            }
        @endphp
        <div class="text-center">
            <h4 style="text-transform: uppercase;">
                @if($isQuotation)
                    Quotation
                @elseif($isAdvanceReceipt)
                    Advance Receipt
                @else
                    Sale
                @endif
            </h4>
        </div>

        <table class="table-bordered"
            style="width: 100%; border-collapse: collapse; font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 5px 0 5px 0;">
            <thead>
                <tr style="background-color:#FF9F43; color:#fff;font-size:10px;">
                    <th style="width:10%; padding: 3px; text-align:center;">Sr No</th>
                    <th style="padding: 3px;width:20%;  text-align:left;">Product Name</th>
                    <th style="padding: 3px; text-align:left;">Unit</th>
                    <th style="width:8%; padding: 3px; text-align:center;">Qty</th>
                    <th style="padding: 3px; text-align:center;">Price</th>
                    @if($hasDiscount)
                        <th style="padding: 3px; text-align:center;width:10%;">Disc Amt</th>
                    @endif

                    @if($hasGst)
                        <th style="width:20%; padding: 3px; text-align:center;">Product Taxes</th>
                        <th style="width:20%; text-align:center;">Tax Amount</th>
                     @endif
                    <th style="width:35%; text-align:center;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orderItems as $item)
                    @php
                        $itemQty = (float) ($item->quantity ?? 0);
                        $productGstTotal = (float) ($item->product_gst_total ?? 0);
                        // price is now BASE price — display it directly (no GST subtraction)
                        $displayUnitPrice = (float) ($item->price ?? 0);
                    @endphp
                    <tr style="font-size:10px;">
                        <td style="text-align:center; padding:8px;">{{ $loop->iteration }}</td>
                       <td style="padding:8px; text-align:left;">
                        @php
                            $images = json_decode($item->product->images ?? '[]');
                            $firstImage = !empty($images) ? $images[0] : null;
                            $base64 = null;

                            if ($firstImage) {
                                $imagePath = storage_path('app/public/' . $firstImage);
                            } else {
                                $imagePath = public_path('/admin/assets/img/product/noimage.png');
                            }

                            if (file_exists($imagePath)) {
                                $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                                $data = file_get_contents($imagePath);
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            }
                        @endphp

                        <table style="border-collapse: collapse; width:100%; table-layout: fixed;">
                            <colgroup>
                                <col style="width: 30px;">
                                <col style="width: 70px;">
                            </colgroup>
                            <tr>
                                <td style="padding: 0; width: 20%; vertical-align: middle; border: none;">
                                    <img src="{{ $base64 }}" alt="img"
                                        style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px; display: block;">
                                </td>
                                <td style="width: 50px;
                                    border: none;
                                    padding: 0 0 0 6px;
                                    vertical-align: middle;
                                    word-wrap: break-word;
                                    word-break: break-word;
                                    white-space: normal;
                                    overflow-wrap: break-word;
                                    width: 70%;
                                ">
                                    {{ ucfirst($item->product->name ?? 'Product') }}
                                    @if(!empty($item->imei_no))
                                        <br><span style="font-size: 8px; color: #555;">IMEI: {{ $item->imei_no }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                          <td class="product-name">
                            {{ ucfirst($item->product->unit->unit_name ?? 'N/A') }}
                        </td>
                         <td style="padding:3px; text-align:center;">
                            {{ $item->quantity }}
                        </td>
                        <td style="padding:3px; text-align:center;">
                            @if ($setting->currency_position === 'right')
                                {{ number_format($displayUnitPrice, 2) }}{{ $setting->currency_symbol }}
                            @else
                                {{ $setting->currency_symbol }}{{ number_format($displayUnitPrice, 2) }}
                            @endif
                        </td>
                    @php
                        $discountAmount = (float)($item->discount_amount ?? 0);
                        $discountPercentage = (float)($item->discount_percentage ?? 0);
                    @endphp

                    @if($hasDiscount)
                        <td style="padding:3px; text-align:center;">
                            {{-- Discount Amount --}}
                            {{ $setting->currency_symbol }}{{ number_format($discountAmount, 2) }}

                            {{-- Show percentage only if applied --}}
                            @if($discountPercentage > 0)
                                <small>({{ rtrim(rtrim(number_format($discountPercentage,2), '0'), '.') }}%)</small>
                            @endif
                        </td>
                    @endif
                    @php
                        // ✅ GST total per product (initialised from DB as fallback)
                        $productGstTotal = (float) ($productGstTotal ?? 0);

                        // ✅ price IS exclusive base price; lineTotal = baseUnitPrice × qty
                        $lineTotal = (float) ($item->price ?? 0) * (float) ($item->quantity ?? 0);

                        $rowGstDetails = $item->product_gst_details;
                        if (is_string($rowGstDetails)) {
                            $rowGstDetails = json_decode($rowGstDetails, true);
                            if (is_string($rowGstDetails)) {
                                $rowGstDetails = json_decode($rowGstDetails, true);
                            }
                        }
                        if (is_array($rowGstDetails) && isset($rowGstDetails['tax_name'])) {
                            $rowGstDetails = [$rowGstDetails];
                        }

                        // ✅ Recalculate GST using EXCLUSIVE calculation
                        // Step 1: Sum ALL tax rates (e.g. CGST 9% + SGST 9% = 18%)
                        // Step 2: Total GST = lineTotal * (totalRate / 100)
                        // Step 3: Split proportionally per sub-tax
                        $rowTotalGstRate = 0;
                        if (is_array($rowGstDetails) && !empty($rowGstDetails)) {
                            foreach ($rowGstDetails as $gstRow) {
                                $rowTotalGstRate += (float)($gstRow['tax_rate'] ?? 0);
                            }
                        }
                        if ($rowTotalGstRate > 0 && is_array($rowGstDetails) && !empty($rowGstDetails)) {
                            $rowLineGstTotal = round($lineTotal * ($rowTotalGstRate / 100), 2);
                            $productGstTotal = $rowLineGstTotal;
                            // Pre-compute per-sub-tax GST for display (proportional split)
                            $rowTaxPortions  = [];
                            foreach ($rowGstDetails as $gstRow) {
                                $rate = (float)($gstRow['tax_rate'] ?? 0);
                                $rowTaxPortions[] = [
                                    'tax_name' => $gstRow['tax_name'] ?? 'GST',
                                    'tax_rate' => $rate,
                                    'gst_amt'  => round(($rowLineGstTotal * $rate) / $rowTotalGstRate, 2),
                                ];
                            }
                        } else {
                            $rowLineGstTotal = 0;
                            $productGstTotal = 0;
                            $rowTaxPortions  = [];
                        }
                        // Row final total = lineTotal + GST - discountAmount
                        $rowFinalTotal = $lineTotal + $rowLineGstTotal - $discountAmount;
                    @endphp
                         @if($hasGst)
                        <!-- Product Taxes -->
                       <td style="padding:3px; text-align:center; font-size:10px;">
                            @if(!empty($rowTaxPortions))
                                @foreach($rowTaxPortions as $taxPortion)
                                    <div>
                                        {{ $taxPortion['tax_name'] }} ({{ $taxPortion['tax_rate'] }}%)
                                        : {{ $setting->currency_symbol }}{{ number_format($taxPortion['gst_amt'], 2) }}
                                    </div>
                                @endforeach
                            @else
                                N/A
                            @endif
                        </td>


                    <!-- ✅ TAX AMOUNT COLUMN -->
                    <td style="padding:3px; text-align:right; font-weight:bold;">
                        {{ $setting->currency_symbol }}{{ number_format($productGstTotal, 2) }}
                    </td>
                     @endif
                    <!-- ✅ TOTAL -->
                    <td style="padding:3px; text-align:center;">
                        {{ $setting->currency_symbol }}{{ number_format($rowFinalTotal, 2) }}
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:8px;">No product data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>


        @php
            $isEmiSale = strtolower((string) ($sales->payment_method ?? '')) === 'emi';
            $emiTenureRaw = (string) ($sales->emi_tenure ?? '');
            $emiTenureMonths = (int) preg_replace('/[^0-9]/', '', $emiTenureRaw);
            $emiMonthlyAmount = (float) ($sales->emi_monthly_amount ?? 0);
            $emiLoanAmount = (float) ($sales->emi_loan_amount ?? 0);
            $emiDownPayment = (float) ($sales->emi_down_payment ?? 0);
            $emiInterestRate = (float) ($sales->emi_interest_rate ?? 0);
            $emiInstallmentTotal = $emiTenureMonths > 0 ? $emiMonthlyAmount * $emiTenureMonths : 0;
            $paidEmiPaymentsByMonth = [];

            foreach (($emiPayments ?? collect()) as $emiPayment) {
                $emiMonthNo = (int) ($emiPayment->emi_month ?? 0);
                if ($emiMonthNo > 0) {
                    $paidEmiPaymentsByMonth[$emiMonthNo] = $emiPayment;
                }
            }
        @endphp

        @if($isEmiSale && ($emiTenureMonths > 0 || $emiMonthlyAmount > 0))
            <div class="emi-pdf-blank-space">
                <div class="emi-pdf-box">
                    <div class="emi-pdf-title">EMI Distribution</div>
                    <div class="emi-pdf-body">
                        <table class="emi-pdf-layout">
                            <tr>
                                <td class="emi-pdf-summary-cell">
                                    <div class="emi-pdf-formula">
                                        @if($emiMonthlyAmount > 0 && $emiTenureMonths > 0)
                                            {{ formatCurrency($emiMonthlyAmount, $setting) }} x {{ $emiTenureMonths }} Months =
                                            {{ formatCurrency($emiInstallmentTotal, $setting) }}
                                        @elseif($emiMonthlyAmount > 0)
                                            Monthly EMI: {{ formatCurrency($emiMonthlyAmount, $setting) }}
                                        @else
                                            EMI plan selected
                                        @endif
                                    </div>

                                    <div class="emi-pdf-meta">
                                        @if($emiDownPayment > 0)
                                            Down Payment: {{ formatCurrency($emiDownPayment, $setting) }}<br>
                                        @endif
                                        @if($emiLoanAmount > 0)
                                            Loan Amount: {{ formatCurrency($emiLoanAmount, $setting) }}<br>
                                        @endif
                                        @if($emiInterestRate > 0)
                                            Interest: {{ number_format($emiInterestRate, 2) }}%<br>
                                        @endif
                                        @if(!empty($emiTenureRaw))
                                            Tenure: {{ strtolower($emiTenureRaw) === 'custom' ? 'Custom' : $emiTenureMonths . ' Months' }}
                                        @endif
                                    </div>

                                    <div class="emi-pdf-note">
                                        Note: EMI distribution is shown month-wise. Paid months are marked as Paid and remaining months are pending as per the selected EMI tenure.
                                    </div>
                                </td>
                                <td class="emi-pdf-schedule-cell">
                                    @if($emiTenureMonths > 0)
                                        <table class="emi-schedule-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 26%;">Month</th>
                                                    <th style="width: 28%;">Amount</th>
                                                    <th style="width: 22%;">Status</th>
                                                    <th style="width: 24%;">Paid Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for($month = 1; $month <= $emiTenureMonths; $month++)
                                                    @php
                                                        $paidPayment = $paidEmiPaymentsByMonth[$month] ?? null;
                                                        $isPaidEmiMonth = !empty($paidPayment);
                                                        $monthLabel = $month === 1 ? '1st Month' : ($month === 2 ? '2nd Month' : ($month === 3 ? '3rd Month' : $month . 'th Month'));
                                                        $emiPaidDate = '-';
                                                        if ($isPaidEmiMonth) {
                                                            $rawEmiPaidDate = $paidPayment->payment_date ?? $paidPayment->created_at ?? null;
                                                            $emiPaidDate = $rawEmiPaidDate ? \Carbon\Carbon::parse($rawEmiPaidDate)->format('d-m-Y') : '-';
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $monthLabel }}</td>
                                                        <td style="text-align: right;">{{ formatCurrency($emiMonthlyAmount, $setting) }}</td>
                                                        <td class="{{ $isPaidEmiMonth ? 'emi-status-paid' : 'emi-status-pending' }}">
                                                            {{ $isPaidEmiMonth ? 'Paid' : 'Pending' }}
                                                        </td>
                                                        <td>{{ $emiPaidDate ?: '-' }}</td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        @endif

                {{-- ✅ Show only when quotation AND labour items exist --}}
                @if( isset($labourItems) && $labourItems->isNotEmpty())

                <div class="text-center">
                    <h4 style="text-transform: uppercase;">Labour Items</h4>
                </div>

                <table class="table-bordered"
                    style="width: 100%; border-collapse: collapse; font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 10px 0 7px 0;">

                    <thead>
                        <tr style="background-color:#ff9f43; color:#fff;font-size:10px;">
                            <th style="width:10%; padding:3px; text-align:center;">Sr No</th>
                            <th style="padding:3px; text-align:left;">Labour Name</th>
                            <th style="width:8%; padding:3px; text-align:center;">Qty</th>
                            <th style="padding:3px; text-align:center;">Price</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($labourItems as $labour)
                        <tr style="font-size:10px;">
                            <td style="text-align:center;">{{ $loop->iteration }}</td>

                            <td>
                                {{ $labour->labourItem->item_name ?? 'Labour' }}
                            </td>

                            <td style="text-align:center;">
                                {{ $labour->qty ?? 0 }}
                            </td>

                            <td style="text-align:center;">
                                {{ $setting->currency_symbol }}
                                {{ number_format($labour->price ?? 0, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>

                @endif


            {{-- GST Details --}}
            {{-- @if ($sales->gst_option == 'with_gst' && !empty($taxDetails1))
                @foreach ($taxDetails1 as $tax)
                    <tr>
                        <td
                            style="padding: 6px 12px; text-align: left; background-color: #ff9f43; color:#fff; font-weight:bold; border: 1px solid #e0e0e0;">
                            {{ $tax['name'] }} ({{ $tax['rate'] }}%)
                        </td>
                        <td style="padding: 6px 12px; text-align: right; border: 1px solid #e0e0e0;">
                            {{ $tax['formatted_amount'] }}
                        </td>
                    </tr>
                @endforeach
            @endif --}}


        @php
            function convertNumberToWords($number)
            {
                $hyphen = '-';
                $conjunction = ' and ';
                $negative = 'negative ';
                $dictionary = [
                    0 => 'zero',
                    1 => 'one',
                    2 => 'two',
                    3 => 'three',
                    4 => 'four',
                    5 => 'five',
                    6 => 'six',
                    7 => 'seven',
                    8 => 'eight',
                    9 => 'nine',
                    10 => 'ten',
                    11 => 'eleven',
                    12 => 'twelve',
                    13 => 'thirteen',
                    14 => 'fourteen',
                    15 => 'fifteen',
                    16 => 'sixteen',
                    17 => 'seventeen',
                    18 => 'eighteen',
                    19 => 'nineteen',
                    20 => 'twenty',
                    30 => 'thirty',
                    40 => 'forty',
                    50 => 'fifty',
                    60 => 'sixty',
                    70 => 'seventy',
                    80 => 'eighty',
                    90 => 'ninety',
                ];

                if (!is_numeric($number)) {
                    return false;
                }

                if ($number < 0) {
                    return $negative . convertNumberToWords(abs($number));
                }

                $string = '';
                if ($number < 21) {
                    $string = $dictionary[$number];
                } elseif ($number < 100) {
                    $tens = ((int) ($number / 10)) * 10;
                    $units = $number % 10;
                    $string = $dictionary[$tens];
                    if ($units) {
                        $string .= $hyphen . $dictionary[$units];
                    }
                } elseif ($number < 1000) {
                    $hundreds = (int) ($number / 100);
                    $remainder = $number % 100;
                    $string = $dictionary[$hundreds] . ' hundred';
                    if ($remainder) {
                        $string .= $conjunction . convertNumberToWords($remainder);
                    }
                } else {
                    $baseUnits = [10000000 => 'crore', 100000 => 'lakh', 1000 => 'thousand'];
                    foreach ($baseUnits as $divisor => $label) {
                        if ($number >= $divisor) {
                            $units = (int) ($number / $divisor);
                            $remainder = $number % $divisor;
                            $string = convertNumberToWords($units) . ' ' . $label;
                            if ($remainder) {
                                $string .= $conjunction . convertNumberToWords($remainder);
                            }
                            break;
                        }
                    }
                }
                return $string;
            }
            @endphp


         @php
            $totalthing = count($orderItems);
        @endphp

        @if($totalthing > 5 && $totalthing <= 15)
            <div style="page-break-before: always;"></div>
        @endif
        @if($totalthing > 15)
            <div style="page-break-before: auto;"></div>
        @endif


        <div class="footer-section">
            <!-- Bank Details + Totals: table layout -->
            <table style="width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 12px; color: #000;">
                <tr>
                    <!-- Bank Details -->
                    <td
                        style="width:40%; border: 1px solid #ff9f43; padding: 5px 8px; vertical-align: top; background-color: #eaedf0;">
                        <strong style="display: block; margin-bottom: 6px; text-transform: uppercase;">Bank
                            Details:</strong>
                        <table style="width:100%; border-collapse: collapse; font-size: 10px; color: inherit;">
                            <tr>
                                <td style="padding: 0 0 3px 0;">Bank Name :</td>
                                <td style="text-align: right; padding: 0 0 2px 0;">
                                    {{ $setting->bank_name ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0 0 3px 0;">Branch :</td>
                                <td style="text-align: right; padding: 0 0 3px 0;">{{ $setting->branch ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0 0 3px 0;">A/C No :</td>
                                <td style="text-align: right; padding: 0 0 3px 0;">{{ $setting->ac_no ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0;">IFSC Code :</td>
                                <td style="text-align: right; padding: 0;">
                                    {{ $setting->ifsc_code ?? 'N/A' }}
                                </td>
                            </tr>
                        </table>
                          @php
                            $subTotal = 0;
                            $totalDiscount = 0;
                            $totalGstAmount = 0;

                            foreach ($orderItems as $item) {
                                $lineTotal = $item->price * $item->quantity;
                                $subTotal += $lineTotal;
                                $totalDiscount += (float)($item->discount_amount ?? 0);

                                // Calculate GST using EXCLUSIVE calculation
                                $gstDetails = $item->product_gst_details;
                                if (is_string($gstDetails)) {
                                    $gstDetails = json_decode($gstDetails, true);
                                    if (is_string($gstDetails)) $gstDetails = json_decode($gstDetails, true);
                                }
                                if (is_array($gstDetails) && isset($gstDetails['tax_name'])) {
                                    $gstDetails = [$gstDetails];
                                }
                                if (is_array($gstDetails) && !empty($gstDetails)) {
                                    $itemTotalGstRate = 0;
                                    foreach ($gstDetails as $gRow) {
                                        $itemTotalGstRate += (float)($gRow['tax_rate'] ?? 0);
                                    }
                                    if ($itemTotalGstRate > 0) {
                                        $totalGstAmount += round($lineTotal * ($itemTotalGstRate / 100), 2);
                                    }
                                } else {
                                    $totalGstAmount += (float)($item->product_gst_total ?? 0);
                                }
                            }

                            // base subtotal is just subTotal in exclusive mode
                            $baseSubtotal = $subTotal;

                            // Labour total
                            $labourTotal = 0;
                            if(!empty($labourItems)){
                                foreach ($labourItems as $labour) {
                                    $labourTotal += ($labour->price * $labour->qty);
                                }
                            }

                            $shippingCharge = (float)($sales->shipping ?? 0);

                            // Calculate after discount (on base subtotal)
                            $afterDiscount = $subTotal - $totalDiscount;

                            // ✅ Always recalculate Grand Total from components (prices are exclusive, so ADD GST)
                            $grandTotal = $subTotal + $totalGstAmount - $totalDiscount + $labourTotal + $shippingCharge;

                            // Calculate RETURN AMOUNT
                            $totalReturnAmount = 0;
                            $allItemsFullyReturned = false;

                            if (isset($returns) && $returns->isNotEmpty()) {
                                foreach ($returns as $ret) {
                                    if (!empty($ret->items)) {
                                        foreach ($ret->items as $retItem) {
                                            $lineRetTotal = (float)($retItem->price ?? 0) * (float)($retItem->quantity ?? 0);
                                            $discountRetAmt = (float)($retItem->discount_amount ?? 0);
                                            $gstRetAmt = (float)($retItem->product_gst_total ?? 0);
                                            $totalReturnAmount += ($lineRetTotal - $discountRetAmt + $gstRetAmt);
                                        }
                                    } else {
                                        $totalReturnAmount += (float)($ret->total_amount ?? 0);
                                    }
                                }

                                // Check if all items are fully returned
                                $orderItemsQuantities = [];
                                foreach ($orderItems as $item) {
                                    $orderItemsQuantities[$item->id] = $item->quantity;
                                }

                                $returnedQuantities = [];
                                foreach ($returns as $ret) {
                                    foreach ($ret->items as $retItem) {
                                        if (!isset($returnedQuantities[$retItem->order_item_id])) {
                                            $returnedQuantities[$retItem->order_item_id] = 0;
                                        }
                                        $returnedQuantities[$retItem->order_item_id] += $retItem->quantity;
                                    }
                                }

                                $allItemsFullyReturned = true;
                                foreach ($orderItemsQuantities as $orderItemId => $originalQty) {
                                    $returnedQty = $returnedQuantities[$orderItemId] ?? 0;
                                    if ($returnedQty < $originalQty) {
                                        $allItemsFullyReturned = false;
                                        break;
                                    }
                                }
                            }

                            // Keep return amount aligned with invoice screen:
                            // Add shipping to return amount only when fully returned
                            $totalReturnWithShipping = $allItemsFullyReturned
                                ? $totalReturnAmount + $shippingCharge
                                : $totalReturnAmount;

                            // Calculate PAID AMOUNT
                            $paidAmount = (float)($paidAmount ?? 0);

                            // Calculate PENDING AMOUNT = Grand Total - Return Amount - Paid Amount
                            $pendingAmount = max(0, $grandTotal - $totalReturnWithShipping - $paidAmount);

                            // Calculate EXTRA PAID (if any)
                            $extraPaid = max(0, $paidAmount - ($grandTotal - $totalReturnWithShipping));

                            // Amount in words
                            $amountInWords = ucwords(convertNumberToWords((int)$grandTotal)) . ' Rupees';

                            // Format currency function
                            function formatCurrency($amount, $setting) {
                                $num = (float)$amount;
                                $explode = explode(".", number_format($num, 2, '.', ''));
                                $whole = $explode[0];
                                $decimal = $explode[1];

                                $lastThree = substr($whole, -3);
                                $restUnits = substr($whole, 0, -3);
                                if ($restUnits != '') {
                                    $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
                                    $whole = $restUnits . "," . $lastThree;
                                }
                                $formatted = $whole . "." . $decimal;
                                if ($setting->currency_position === 'right') {
                                    return $formatted . $setting->currency_symbol;
                                } else {
                                    return $setting->currency_symbol . $formatted;
                                }
                            }

                        @endphp

                    <!-- QR Code -->
                    <td class="qr-details-box"
                        style="width: 20%; border: 1px solid #ff9f43; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
                        <table style="width: 100%; font-size: 12px; border-collapse: collapse; color: inherit;">
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">
                                    @if (isset($setting->qr_code) && file_exists(storage_path('app/public/' . $setting->qr_code)))
                                        @php
                                            $imageData = base64_encode(
                                                file_get_contents(storage_path('app/public/' . $setting->qr_code)),
                                            );
                                            $mimeType = mime_content_type(
                                                storage_path('app/public/' . $setting->qr_code),
                                            );
                                        @endphp
                                        <img src="data:{{ $mimeType }};base64,{{ $imageData }}"
                                            style="width:80px; height:80px;">
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>

                    <!-- Totals -->
                <td style="width: 40%; border: 1px solid #22b428; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
                    <strong style="display: block; margin-bottom: 2px; text-transform: uppercase;">Totals:</strong>
                    <table style="width: 100%; font-size: 10px; border-collapse: collapse; color: inherit;">
                        <tr>
                            <td style="padding: 0 0 3px 0;">Sub Total:</td>
                            <td style="text-align: right; padding: 0 0 3px 0;">
                                {{ formatCurrency($baseSubtotal, $setting) }}
                            </td>
                        </tr>

                        @if($hasGst || $totalGstAmount > 0)
                        <tr>
                            <td style="padding: 0 0 3px 0;">Total GST :</td>
                            <td style="text-align: right; padding: 0 0 3px 0;">
                                {{ formatCurrency($totalGstAmount, $setting) }}
                            </td>
                        </tr>
                        @endif

                        @if($totalDiscount > 0)
                            <tr>
                                <td style="padding: 0 0 3px 0;">Discount Amount:</td>
                                <td style="text-align: right; padding: 0 0 3px 0;">
                                    -{{ formatCurrency($totalDiscount, $setting) }}
                                </td>
                            </tr>
                        @endif


                        @if($shippingCharge > 0)
                            <tr>
                                <td style="padding: 0 0 3px 0;">Shipping Charge :</td>
                                <td style="text-align: right; padding: 0 0 3px 0;">
                                    {{ formatCurrency($shippingCharge, $setting) }}
                                </td>
                            </tr>
                        @endif

                        @if($labourTotal > 0)
                            <tr>
                                <td style="padding: 0 0 3px 0;">Labour Charge :</td>
                                <td style="text-align: right; padding: 0 0 3px 0;">
                                    {{ formatCurrency($labourTotal, $setting) }}
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td style="padding: 0 0 3px 0; font-weight:bold;">Grand Total :</td>
                            <td style="text-align: right; padding: 0 0 3px 0; font-weight:bold;">
                                {{ formatCurrency(round($grandTotal), $setting) }}
                            </td>
                        </tr>

                        @if($totalReturnAmount > 0)
                        <tr>
                            <td style="padding: 0 0 3px 0; color:#ea5455; font-weight:bold;">
                                Return Amount :
                            </td>
                            <td style="text-align: right; padding: 0 0 3px 0; color:#ea5455; font-weight:bold;">
                                {{ formatCurrency($totalReturnWithShipping, $setting) }}
                            </td>
                        </tr>
                        @endif

                        @if(isset($allPayments) && count($allPayments) > 0)
                            @foreach($allPayments as $index => $payment)
                            <tr>
                                <td style="padding: 0px 0 3px 0; color:#2E7D32; font-weight:bold;">
                                    @if($index === 0 && count($allPayments) > 1)
                                        Advance Paid :
                                    @else
                                        Paid Amount :
                                    @endif
                                </td>
                                <td style="text-align: right; padding: 0px 0 3px 0; color:#2E7D32; font-weight:bold;">
                                    {{ formatCurrency($payment->payment_amount, $setting) }}
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td style="padding: 0px 0 3px 0; color:#2E7D32; font-weight:bold;">
                                    Paid Amount :
                                </td>
                                <td style="text-align: right; padding: 0px 0 3px 0; color:#2E7D32; font-weight:bold;">
                                    {{ formatCurrency($paidAmount, $setting) }}
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td style="padding: 0 0 3px 0; color:#C62828; font-weight:bold;">
                                Pending Amount :
                            </td>
                            <td style="text-align: right; padding: 0 0 3px 0; color:#C62828; font-weight:bold;">
                                {{ formatCurrency($pendingAmount, $setting) }}
                            </td>
                        </tr>

                        @if(!empty($extraPaid) && $extraPaid > 0)
                        <tr>
                            <td style="padding: 0 0 3px 0; color:#d81414; font-weight:bold;">
                                Extra Paid :
                            </td>
                            <td style="text-align: right; padding: 0 0 3px 0; color:#d81414; font-weight:bold;">
                                {{ formatCurrency($extraPaid, $setting) }}
                            </td>
                        </tr>
                        @endif
                    </table>
                </td>

                </tr>
            </table>

                <table style="width: 100%; border-collapse: collapse; font-size: 12px; color: #000;">
                    <tr>
                        <td style="width: 60%; border: 2px solid #dee2e6; padding: 8px 12px; vertical-align: top; font-size: 11px;">
                            <strong>{{ $amountInWords }} Only</strong>
                        </td>
                        <td style="width: 40%; border: 2px solid #dee2e6; padding: 8px 12px; text-align: right; vertical-align: top; background-color:#FF9F43; color:#fff;">
                            <strong>Grand Total : {{ formatCurrency(round($grandTotal), $setting) }}</strong>
                        </td>
                    </tr>
                </table>

            <table style="width: 100%; border-collapse: collapse; font-size: 12px; color: #000;">
                <tr>
                    <!-- Remarks -->
                    <td style="width: 50%; border: 2px solid #dee2e6; padding: 8px 12px; vertical-align: top;">
                        <strong>Remarks :</strong><br>
                        <span style="white-space: pre-line;">{{ !empty($sales->remarks) ? $sales->remarks : 'N/A' }}</span>
                    </td>

                    <!-- Authorized Signatory -->
                    <td
                        style="width: 50%; border: 2px solid #dee2e6; padding: 8px 12px; text-align: right; vertical-align: top;">
                        <p style="margin: 0;">For, {{ $setting->name ?? ' Auto Care' }}</p>
                        <div style="height: 42px;"></div>
                        <p style="margin: 0;">(Authorized Signatory)</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>

</html>
