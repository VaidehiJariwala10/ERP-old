<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Delivery Challan PDF</title>
    <style>
        @page {
            size: A4;
            margin: 2mm;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background: white;
            color: #333;
        }

        .card-body {
            margin-top: 3mm;
            width: 95%;
            min-height: 283mm;
            padding: 3mm;
            margin: auto;
            box-sizing: border-box;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid black;
            font-size: 12px;
            position: relative;
        }

        table,
        table td,
        table th {
            font-size: inherit;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
            padding: 4px 8px;
        }

        .table-bordered thead tr {
            background-color: #ff9f43;
            color: #fff;
            font-size: 10px;
        }

        .table-bordered tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .text-center { text-align: center; }
        .small { font-size: 10px; }
        .invoice-label-nowrap { white-space: nowrap; word-break: keep-all; }

        .footer-section {
            position: static;
            width: 100%;
            margin-top: 12px;
        }
    </style>
</head>

<body>

    <div class="card-body">
        <table style="width:100%; margin-bottom: 10px; border-collapse: collapse;">
            <tr>
                <td style="width: 150px; vertical-align: top;">
                    @if (isset($setting->logo) && file_exists(storage_path('app/public/' . $setting->logo)))
                        @php
                            $logoPath = storage_path('app/public/' . $setting->logo);
                            $logoData = base64_encode(file_get_contents($logoPath));
                            $logoMime = mime_content_type($logoPath);
                        @endphp
                        <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Company Logo"
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
                <td style="width:33%; position: relative; padding: 5px 8px; vertical-align: top; background-color: #eaedf0; border-right: 1px solid #ff9f43;">
                    <strong style="text-transform: uppercase; display: block; margin-bottom: 6px;">Customer Details:</strong>
                    <table style="width:100%; border-collapse: collapse; font-size: 10px; color: inherit;">
                        <tr>
                            <td style="padding: 0 0 4px 0;">Name :</td>
                            <td style="text-align: right; padding: 0 0 4px 0;">
                                {{ $order->customer_name ?? ($order->user?->name ?? 'walk-in-customer') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 4px 0;">Phone :</td>
                            <td style="text-align: right; padding: 0 0 4px 0;">{{ $order->customer_phone ?? ($order->user?->phone ?? '--') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 4px 0;">Email :</td>
                            <td style="text-align: right; padding: 0 0 4px 0;">{{ $order->customer_email ?? ($order->user?->email ?? '--') }}</td>
                        </tr>
                        @php
                            $customerAddress = trim(implode(', ', array_filter([
                                $order->customer_address ?? '',
                                $order->customer_city ?? '',
                                $order->customer_country ?? '',
                            ])));
                        @endphp
                        @if (!empty($customerAddress))
                            <tr>
                                <td class="invoice-label-nowrap" style="padding: 0 0 4px 0; vertical-align: top;">Address :</td>
                                <td style="text-align: right; padding: 0 0 4px 0; word-wrap: break-word; white-space: normal;">{{ $customerAddress }}</td>
                            </tr>
                        @endif
                    </table>

                </td>

                <td style="width:33%; position: relative; padding: 8px 12px; vertical-align: top; background-color: #eaedf0; border-right: 1px solid #ff9f43;">
                    <strong style="text-transform: uppercase; display: block;margin-bottom: 2px">Company Details:</strong>
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
                    </table>

                </td>

                <td style="width:34%; border: 0px solid #dee2e6; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">

                    <strong style="text-transform: uppercase; display: block; margin-bottom: 2px;">Delivery Details:</strong>

                    <table style="width:100%; border-collapse: collapse; font-size: 10px; color: inherit;">
                        <tr>
                            <td style="padding: 0 0 2px 0;">Challan Number :</td>
                            <td style="text-align: right; padding: 0 0 2px 0;">{{ $challan_number ?? ('DC-' . ($order->id ?? '-')) }}</td>
                        </tr>

                        <tr>
                            <td style="padding: 0 0 2px 0;">Date :</td>
                            @php
                                $firstDelivery = (isset($deliveries) && $deliveries->count()) ? $deliveries->first() : null;
                                if ($firstDelivery && !empty($firstDelivery->created_at)) {
                                    try {
                                        $challan_date = \Carbon\Carbon::parse($firstDelivery->created_at)->format('d-M-Y');
                                    } catch (\Throwable $e) {
                                        $challan_date = date('d-M-Y');
                                    }
                                } else {
                                    $challan_date = date('d-M-Y');
                                }
                            @endphp
                            <td style="text-align: right; padding: 0 0 2px 0;">{{ $challan_date }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 2px 0;">Order Number :</td>
                            <td style="text-align: right; padding: 0 0 2px 0;">{{ $order->order_number ?? $order->id }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 2px 0;">Order Date :</td>
                            <td style="text-align: right; padding: 0 0 2px 0;">{{ !empty($order->created_at) ? optional($order->created_at)->format('d-M-Y') : '--' }}</td>
                        </tr>
                        @php
                            $deliveredByName = null;
                            if (isset($deliveries) && $deliveries->count()) {
                                $deliveredByName = optional($deliveries->first()->deliveredBy)->name;
                            }
                        @endphp
                        @if (!empty($deliveredByName))
                            <tr>
                                <td style="padding: 0 0 2px 0;">Delivered By :</td>
                                <td style="text-align: right; padding: 0 0 2px 0;">{{ $deliveredByName }}</td>
                            </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>

        <div class="text-center">
            <h4 style="text-transform: uppercase;">Delivery Challan</h4>
        </div>

        <table class="table-bordered" style="width: 100%; border-collapse: collapse; font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 5px 0 5px 0;">
            <thead>
                <tr style="background-color:#ff9f43; color:#fff;font-size:10px;">
                    <th style="width:8%; padding: 3px; text-align:center;">Sr No</th>
                    <th style="padding: 3px;width:40%;  text-align:left;">Product Name</th>
                    <th style="padding: 3px; text-align:center; width:12%;">Unit</th>
                    <th style="padding: 3px; text-align:center; width:12%;">Ordered Qty</th>
                    <th style="padding: 3px; text-align:center; width:12%;">Delivered Qty</th>
                    <th style="padding: 3px; text-align:center; width:16%;">Pending Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveries as $item)
                    @php
                        $product = $item->orderItem->product ?? $item->product ?? null;
                        $unitName = $product->unit->unit_name ?? 'N/A';
                        $orderedQty = $item->ordered_quantity ?? ($item->orderItem->quantity ?? 0);
                        $deliveredQty = $item->delivered_quantity ?? 0;
                        $pendingQty = max(0, $orderedQty - $deliveredQty);
                    @endphp
                    <tr style="font-size:10px;">
                        <td style="text-align:center; padding:8px;">{{ $loop->iteration }}</td>
                        <td style="padding:8px; text-align:left;">{{ ucfirst($product->name ?? 'Product') }}</td>
                        <td class="product-name" style="text-align:center;">{{ $unitName }}</td>
                        <td style="padding:3px; text-align:center;">{{ number_format($orderedQty, 2) }}</td>
                        <td style="padding:3px; text-align:center;">{{ number_format($deliveredQty, 2) }}</td>
                        <td style="padding:3px; text-align:center;">{{ number_format($pendingQty, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:8px;">No delivery data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @php
            $deliveryNotes = collect($deliveries ?? [])
                ->pluck('notes')
                ->filter()
                ->unique()
                ->implode("\n");
            $remarks = trim($deliveryNotes) !== '' ? $deliveryNotes : ($order->remarks ?? $order->notes ?? '');
        @endphp

        <div class="footer-section">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px; color: #000; margin-top: 12px;">
                <tr>
                    <td style="width: 60%; border: 2px solid #dee2e6; padding: 8px 12px; vertical-align: top; font-size: 11px;">
                        <strong>Remarks :</strong>
                        <div style="white-space: pre-line;">{{ !empty($remarks) ? $remarks : 'N/A' }}</div>
                    </td>
                    <td style="width: 40%; border: 2px solid #dee2e6; padding: 8px 12px; text-align: right; vertical-align: top; background-color:#ff9f43; color:#fff;">
                        <strong>Prepared By : {{ auth()->user()->name ?? '' }}</strong>
                    </td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse; font-size: 12px; color: #000; margin-top: 24px;">
                <tr>
                    <td style="width: 50%; border: 2px solid #dee2e6; padding: 8px 12px; vertical-align: bottom; height: 70px;">
                        <strong>Received By (Customer) :</strong>
                        <div style="margin-top: 40px; border-top: 1px solid #999; padding-top: 4px;">Signature &amp; Date</div>
                    </td>
                    <td style="width: 50%; border: 2px solid #dee2e6; padding: 8px 12px; vertical-align: bottom; height: 70px;">
                        <strong>For, {{ $setting->name ?? 'Company' }}</strong>
                        <div style="margin-top: 40px; border-top: 1px solid #999; padding-top: 4px;">Authorized Signatory</div>
                    </td>
                </tr>
            </table>
        </div>

        <div style="margin-top:12px; width:100%;">
            <div style="border:2px solid #e0e0e0; padding:14px; text-align:center;">
                <div style="color:#6f42c1; font-weight:700; font-size:16px; margin-bottom:8px;">Goods Delivered Successfully</div>
                <div style="color:#666; font-size:12px; line-height:1.4;">
                    Dear {{ $order->customer_name ?? ($order->user?->name ?? 'Customer') }}, please verify the items listed above upon receipt. For any delivery-related queries, contact {{ $setting->name ?? 'us' }} at {{ $setting->phone ?? '' }}.
                </div>
            </div>
        </div>
    </div>

</body>

</html>
