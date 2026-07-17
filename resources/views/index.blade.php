@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $role = auth()->user()->role ?? '';
    @endphp
    {{-- <style>
        .color_box {
            display: block;
            width: 100%;
            text-decoration: none;
            color: inherit;
        }

        /* Dashboard widget styles remain the same */
        @media screen and (min-width: 768px) and (max-width: 1400px) {
            .dash-count .dash-imgs svg {
                width: 40px !important;
                height: 36px !important;
            }

            .dash-widgetcontent h6 {
                font-size: 10px !important;
            }

            .dash-counts h5 {
                font-size: 10.5px !important;
            }

            .dash-count {
                min-height: 90px !important;
                margin: 0 0 15px !important;
                padding: 17px;
            }
        }

        @media screen and (max-width: 767px) {
            .dash-widget {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 15px !important;
                text-align: center;
                padding: 12px 20px !important;
            }

            .col-4 {
                display: flex;
                justify-content: center;
            }

            .dash-widgetcontent {
                margin-left: 0 !important;
                width: 68px !important;
            }

            .dash-widgetcontent h5 {
                margin-top: .5rem !important;
                font-size: 13px !important;
            }

            .dash-widgetcontent h6 {
                font-size: 10px !important;
            }

            .dash-count {
                min-height: 90px !important;
                margin: 0 0 15px !important;
                padding: 15px;
            }

            .dash-counts h5 {
                font-size: 10.5px !important;
            }

            .dash-count .dash-imgs svg {
                width: 37px !important;
                height: 37px !important;
            }

            .dash-count h4 {
                font-size: 19px !important;
            }

            .table-responsive {
                font-size: 12px !important;
            }

            /* NEW: Mobile table styles for sales/purchases */
            .dataview table thead th:nth-child(n+3),
            .dataview table tbody td:nth-child(n+3) {
                display: none !important;
            }

            .dataview table thead th.details-column,
            .dataview table tbody td:nth-child(2) {
                display: table-cell !important;
                text-align: center;
                vertical-align: middle;
                width: 50px;
            }

            .dataview .toggle-details i {
                font-size: 18px;
                transition: transform 0.3s ease;
            }

            /* Order ID column styling */
            .dataview table tbody td:first-child {
                display: flex !important;
                align-items: center !important;
                max-width: calc(100vw - 100px) !important;
            }

            .dataview .order-id {
                display: inline-block !important;
                max-width: calc(100% - 50px) !important;
                margin-left: 8px !important;
                font-size: 14px !important;
                word-break: break-word !important;
            }
        }

        /* Desktop: hide details toggle column */
        @media (min-width: 769px) {

            .dataview table thead th.details-column,
            .dataview table tbody td:nth-child(2) {
                display: none !important;
            }
        }

        /* Mobile collapse styles */
        .mobile-details-collapse {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-top: 10px;
            padding: 12px;
            background-color: #f8f9fa;
        }

        .mobile-details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #dee2e6;
        }

        .mobile-details-label {
            font-weight: 600;
            color: #495057;
        }

    .mobile-details-value {
        color: #212529;
        text-align: right;
    }

    .dataview .product-link {
        display: flex;
        align-items: flex-start;
        max-width: 250px;
        text-decoration: none;
        color: inherit;
    }

    .dataview .product-name {
        flex: 1;
        min-width: 0;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
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
    </style> --}}
    <style>
    .color_box {
        display: block;
        width: 100%;
        text-decoration: none;
        color: inherit;
    }

    /* Dashboard widget styles */
    .dash-widget {
        transition: all 0.3s ease;
        border-radius: 8px !important;
    }

    .dash-widget:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* Tablet view (768px - 1400px) */
    @media screen and (min-width: 768px) and (max-width: 1400px) {
        .dash-widget {
            padding: 15px !important;
        }

        .dash-widgetimg span img {
            width: 40px !important;
            height: 36px !important;
        }

        .dash-widgetcontent h6 {
            font-size: 10px !important;
            line-height: 1.2 !important;
        }

        .dash-widgetcontent h5 {
            font-size: 10.5px !important;
            margin-bottom: 5px !important;
        }

        .dash-widget {
            min-height: 90px !important;
            margin: 0 0 15px !important;
        }

        /* Ensure proper alignment in tablet */
        .col-lg-4.col-sm-6.col-4 {
            padding-left: 7.5px !important;
            padding-right: 7.5px !important;
        }
    }

    /* Mobile view (max-width: 767px) */
    @media screen and (max-width: 767px) {

         .dash-count {
                min-height: 90px !important;
                margin: 0 0 15px !important;
                padding: 15px;
            }

            .dash-counts h5 {
                font-size: 10.5px !important;
            }

            .dash-count .dash-imgs svg {
                width: 37px !important;
                height: 37px !important;
            }

            .dash-count h4 {
                font-size: 19px !important;
            }

        /* Dashboard widgets container */
        .dashboard-widgets-row {
            margin-left: -5px !important;
            margin-right: -5px !important;
            display: flex !important;
            flex-wrap: wrap !important;
        }

        /* First row (Purchase, Expense, Sales) - 3 in one row */
        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:first-child,
        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(2),
        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(3) {
            width: 33.333% !important; /* Equal width for 3 widgets */
            padding-left: 5px !important;
            padding-right: 5px !important;
            margin-bottom: 10px !important;
            float: left !important;
        }

        /* Remaining widgets (Invoice counts, Vendors, Customers) - 2 per row */
        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(4),
        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(5),
        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(6),
        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(7) {
            width: 50% !important;
            padding-left: 5px !important;
            padding-right: 5px !important;
            margin-bottom: 10px !important;
            float: left !important;
        }

        /* Clear floats */
        .dashboard-widgets-row::after {
            content: "";
            display: table;
            clear: both;
        }

        /* Dashboard widget styling */
        .dash-widget {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
            padding: 12px 5px !important;
            margin-bottom: 0 !important;
            height: 110px !important; /* Slightly shorter for 3 in a row */
            justify-content: center !important;
        }

        .dash-widgetimg {
            margin-bottom: 6px !important;
        }

        .dash-widgetimg span img {
            width: 30px !important;
            height: 30px !important;
        }

        .dash-widgetcontent {
            margin-left: 0 !important;
            width: 100% !important;
            padding: 0 3px !important;
        }

        .dash-widgetcontent h5 {
            margin-top: 0.2rem !important;
            margin-bottom: 0.2rem !important;
            font-size: 12px !important;
            line-height: 1.2 !important;
            word-break: break-word !important;
        }

        .dash-widgetcontent h6 {
            font-size: 10px !important;
            line-height: 1.2 !important;
            margin-bottom: 0 !important;
            color: #6c757d !important;
        }

        /* Currency symbol and amount styling */
        .dash-widgetcontent h5 .counters {
            font-size: 12px !important;
            font-weight: 600 !important;
        }

        /* Adjust for first row (financial amounts) */
        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:first-child .dash-widgetcontent h5,
        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(2) .dash-widgetcontent h5,
        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(3) .dash-widgetcontent h5 {
            font-size: 11px !important; /* Slightly smaller for amounts */
        }

        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:first-child .dash-widgetcontent h6,
        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(2) .dash-widgetcontent h6,
        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(3) .dash-widgetcontent h6 {
            font-size: 9px !important; /* Slightly smaller labels */
        }

        /* Adjust for count widgets (4th onwards) */
        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(n+4) .dash-widgetcontent h5 {
            font-size: 14px !important; /* Larger for counts */
        }

        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(n+4) .dash-widgetcontent h6 {
            font-size: 11px !important;
        }

        .dashboard-widgets-row > .col-lg-4.col-sm-6.col-4:nth-child(n+4) .dash-widget {
            height: 100px !important; /* Slightly shorter for count widgets */
        }

        /* Table responsive styles */
        .table-responsive {
            font-size: 12px !important;
        }

        /* Mobile table styles for sales/purchases */
        .dataview table thead th:nth-child(n+3),
        .dataview table tbody td:nth-child(n+3) {
            display: none !important;
        }

        .dataview table thead th.details-column,
        .dataview table tbody td:nth-child(2) {
            display: table-cell !important;
            text-align: center;
            vertical-align: top !important;
            width: 42px;
            padding-top: 14px !important;
            padding-left: 0 !important;
            padding-right: 12px !important;
        }

        .dataview table tbody td.details-column {
            vertical-align: top !important;
        }

        .dataview .toggle-details {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .dataview .toggle-details i {
            font-size: 18px;
            transition: transform 0.3s ease;
        }

        /* Order ID column styling */
        .dataview table tbody td:first-child {
            display: flex !important;
            align-items: flex-start !important;
            max-width: calc(100vw - 100px) !important;
        }

        .dataview table tbody td:first-child > div {
            width: 100%;
        }

        .dataview .order-id {
            display: inline-block !important;
            max-width: calc(100% - 50px) !important;
            margin-left: 8px !important;
            font-size: 14px !important;
            word-break: break-word !important;
        }

        .dataview .mobile-details-collapse {
            margin-right: -50px;
            padding: 10px 10px 6px;
        }

        .dataview .mobile-details-row {
            display: grid;
            grid-template-columns: minmax(78px, auto) minmax(0, 1fr);
            align-items: start;
            column-gap: 10px;
            row-gap: 6px;
            margin-bottom: 6px;
            padding-bottom: 6px;
        }

        .dataview .mobile-details-label {
            margin: 0;
            white-space: nowrap;
        }

        .dataview .mobile-details-value {
            display: block;
            width: 100%;
            min-width: 0;
            text-align: left;
        }

        .dataview .mobile-product-info {
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
        }

        .dataview .mobile-product-info .product-name {
            flex: 1;
            min-width: 0;
            text-align: left;
            padding-right: 8px;
        }
    }

    /* Desktop: hide details toggle column */
    @media (min-width: 768px) {
        .dataview table thead th.details-column,
        .dataview table tbody td:nth-child(2) {
            display: none !important;
        }

        /* Desktop widget spacing */
        .col-lg-4.col-sm-6.col-4 {
            /* margin-bottom: 25px !important; */
        }
    }

    @media screen and (min-width: 768px) and (max-width: 1180px) {
        .content > .dashboard-widgets-row:first-child > div.col-lg-4.col-sm-6.col-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding-left: 8px !important;
            padding-right: 8px !important;
            margin-bottom: 12px !important;
        }

        .content > .dashboard-widgets-row:first-child > div.col-lg-4.col-sm-6.col-4 .dash-widget {
            min-height: 86px;
            margin: 0 !important;
        }

        .content > .dashboard-widgets-row:first-child > div.d-flex {
            flex: 0 0 50%;
            max-width: 50%;
            padding-left: 8px !important;
            padding-right: 8px !important;
            margin-bottom: 12px !important;
        }

        .content > .dashboard-widgets-row:first-child > div.d-flex .color_box {
            width: 100%;
            height: 100%;
        }

        .content > .dashboard-widgets-row:first-child > div.d-flex .dash-count {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 86px;
            height: 100%;
            margin: 0 !important;
            padding: 14px 16px;
        }

        .content > .dashboard-widgets-row:first-child > div.d-flex .dash-counts h4 {
            margin-bottom: 4px;
        }

        .content > .dashboard-widgets-row:first-child > div.d-flex .dash-counts h5 {
            margin-bottom: 0;
            line-height: 1.25;
        }
    }

    /* ── iPad Mini / Air (768px – 1024px): charts side-by-side col-6 ── */
    @media screen and (min-width: 768px) and (max-width: 1024px) {
        /* CRM: col-lg-5 + col-lg-7 → 50% each */
        .col-lg-7.col-sm-12.col-12.d-flex,
        .col-lg-5.col-sm-12.col-12.d-flex {
            flex: 0 0 50% !important;
            max-width: 50% !important;
        }

        /* ERP Sales & Purchases charts */
        div.col-sm-12.col-12.d-flex:has(#saleschart),
        div.col-sm-12.col-12.d-flex:has(#purchasechart),
        /* HR charts */
        div.col-sm-12.col-12.d-flex:has(#hrAttendanceChart),
        div.col-sm-12.col-12.d-flex:has(#hrSalaryChart),
        /* CRM charts */
        div.col-sm-12.col-12.d-flex:has(#crmPipelineChart),
        div.col-sm-12.col-12.d-flex:has(#crmActivityChart) {
            flex: 0 0 50% !important;
            max-width: 50% !important;
        }

        /* Keep the Sales / Purchases header compact on iPad widths */
        .card-header.d-flex.justify-content-between.align-items-center {
            gap: 8px;
        }

        .card-header.d-flex.justify-content-between.align-items-center .card-title {
            flex: 1 1 auto;
            min-width: 0;
        }

        .chart-select-container {
            flex: 0 0 112px;
            max-width: 112px;
        }

        .chart-select-container .select2-container {
            width: 100% !important;
            min-width: 0 !important;
        }

        .chart-select-container .select2-container--default .select2-selection--single {
            width: 100% !important;
        }

        .chart-select-container .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-size: 12px !important;
            padding-left: 8px !important;
            padding-right: 20px !important;
        }
    }

    /* Mobile collapse styles */
    /* .mobile-details-collapse {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-top: 10px;
        padding: 12px;
        background-color: #f8f9fa;
    } */

    .mobile-details-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #dee2e6;
    }

    .mobile-details-label {
        font-weight: 600;
        color: #495057;
    }

    .mobile-details-value {
        color: #212529;
        text-align: right;
    }

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
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
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

    /* Style for chart select2 dropdowns */
    .chart-select-container .select2-container--default .select2-selection--single {
        height: 31px !important;
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
    }

    .chart-select-container .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 29px !important;
        font-size: 13px !important;
        padding-left: 10px !important;
        padding-right: 25px !important;
    }

    .chart-select-container .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 29px !important;
        width: 10px !important;
    }

    /* Clearfix for mobile grid */
    @media screen and (max-width: 767px) {
        .dashboard-widgets-row::after {
            content: "";
            display: table;
            clear: both;
        }
    }

    .crm-section,
    .hr-section {
        position: relative;
        z-index: 1;
    }

    .crm-section::before,
    .hr-section::before {
        bottom: -10px;
        content: "";
        left: -4px;
        pointer-events: none;
        position: absolute;
        right: -4px;
        top: -10px;
        z-index: -1;
    }

    .crm-section::before {
        background: #f3f8ff;
    }

    .hr-section::before {
        background: #f6fbf6;
    }

    .crm-section .card {
        border: 1px solid #dbeafe;
        box-shadow: none;
    }

    .hr-section .card {
        border: 1px solid #d9f0df;
        box-shadow: none;
    }

    .pending-delivery-scroll {
        max-height: 250px;
        overflow-x: auto;
        overflow-y: auto;
    }

    .custom-table-scroll {
        max-height: 250px;
        overflow-y: auto;
        overflow-x: auto;
    }

    .custom-table-scroll thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #fff;
    }

    .custom-table-scroll::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .custom-table-scroll::-webkit-scrollbar-track {
        background: #f1f1f1; 
        border-radius: 10px;
    }

    .custom-table-scroll::-webkit-scrollbar-thumb {
        background: #ff9f43; 
        border-radius: 10px;
    }

    .custom-table-scroll::-webkit-scrollbar-thumb:hover {
        background: #e68a35; 
    }

    .pending-delivery-scroll table {
        min-width: 0;
        table-layout: fixed;
        width: 100%;
    }

    .pending-delivery-scroll thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #fff;
    }

    .pending-delivery-scroll td,
    .pending-delivery-scroll th {
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pending-delivery-scroll th:nth-child(1),
    .pending-delivery-scroll td:nth-child(1) {
        width: 38%;
    }

    .pending-delivery-scroll th:nth-child(2),
    .pending-delivery-scroll td:nth-child(2) {
        width: 26%;
    }

    .pending-delivery-scroll th:nth-child(3),
    .pending-delivery-scroll td:nth-child(3) {
        width: 36%;
        overflow: visible;
    }

    .pending-delivery-status {
        max-width: 100%;
        min-width: 0;
        width: 100%;
        height: 30px;
        border: 1px solid #d7dde8;
        border-radius: 6px;
        background-color: #fff;
        color: #1f2937;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 20px 4px 8px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .pending-delivery-status:focus {
        border-color: #ff9f43;
        box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.16);
        outline: none;
    }

    .pending-delivery-status.is-saving {
        opacity: 0.7;
    }

    .crm-metric {
        min-height: 118px;
    }

    .hr-metric {
        min-height: 118px;
        height: 80%;
    }

    .crm-metric-label,
    .hr-metric-label {
        color: #667085;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .crm-metric-value,
    .hr-metric-value {
        color: #1f2937;
        font-size: 28px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 8px;
    }

    .crm-metric-note,
    .hr-metric-note {
        color: #667085;
        font-size: 12px;
        line-height: 1.35;
        margin: 0;
    }

    .crm-mini-badge,
    .hr-mini-badge {
        border-radius: 4px;
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 7px;
        max-width: 100%;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .crm-section .table,
    .hr-section .table {
        table-layout: fixed;
        width: 100%;
    }

    .crm-section .table th,
    .crm-section .table td,
    .hr-section .table th,
    .hr-section .table td {
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
        vertical-align: top;
    }

    .crm-section .table a,
    .crm-section .table .text-muted,
    .crm-section .table .mobile-details-label,
    .crm-section .table .mobile-details-value,
    .hr-section .table a,
    .hr-section .table .text-muted,
    .hr-section .table .mobile-details-label,
    .hr-section .table .mobile-details-value {
        max-width: 100%;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .crm-section .table .details-column,
    .hr-section .table .details-column {
        width: 42px;
        word-break: normal;
        overflow-wrap: normal;
    }

    .crm-chart,
    .hr-chart {
        min-height: 260px;
        width: 100%;
    }

    .crm-section:first-of-type,
    .hr-section:first-of-type {
        margin-top: 12px;
        padding-top: 18px;
    }

    .crm-section:first-of-type::before,
    .hr-section:first-of-type::before {
        border-radius: 8px 8px 0 0;
        top: 0;
    }

    .crm-section:nth-last-of-type(1)::before,
    .hr-section:nth-last-of-type(1)::before {
        border-radius: 0 0 8px 8px;
    }

    .pulse-zone {
        /* border-radius: 8px; */
        /* margin: 18px 0; */
        /* margin: 0px 0; */
        /* padding: 0px 14px 14px; */
    }

    /* .pulse-crm {
        background: #f3f8ff;
        padding: 12px 15px;
    } */

    /* .pulse-hr {
        background: #f6fbf6;
        padding: 12px 15px;
    } */

    /* .pulse-zone .row {
        margin-left: 0px;
        margin-right: 0px;
    } */

    .pulse-zone .crm-section,
    .pulse-zone .hr-section {
        padding-left: 15px;
        padding-right: 7px;
    }

    .pulse-zone .crm-section::before,
    .pulse-zone .hr-section::before {
        display: none;
    }

</style>
    <div class="content">
        <div class="row dashboard-widgets-row">

            @if ($showErpDashboard && in_array($role, ['inventory-manager', 'admin', 'sub-admin']) && $showErpTotalSales)
                <div class="col-lg-4 col-sm-6 col-4">
                    <a href="{{ route('sales.list') }}">
                        <div class="dash-widget dash1">
                            <div class="dash-widgetimg">
                                <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash2.svg' }}"
                                        alt="img"></span>
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
            @endif
             @if ($showErpDashboard && in_array($role, ['inventory-manager', 'admin', 'sub-admin']) && $showErpTotalPurchase)
                <div class="col-lg-4 col-sm-6 col-4">
                    <a href="{{ route('purchase.lists') }}">
                        <div class="dash-widget">
                            <div class="dash-widgetimg">
                                <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash1.svg' }}"
                                        alt="img"></span>
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
            @endif
            @if ($showErpDashboard && in_array($role, ['inventory-manager', 'admin', 'sub-admin']) && $showErpTotalExpense)
                <div class="col-lg-4 col-sm-6 col-4">
                    <a href="{{ route('expense.list') }}">
                        <div class="dash-widget dash2">
                            <div class="dash-widgetimg">
                                <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash3.svg' }}"
                                        alt="img"></span>
                            </div>
                            <div class="dash-widgetcontent">
                                <h5>
                                    @if ($currencyPosition === 'left')
                                        {{ $currencySymbol }}
                                    @endif
                                    <span class="counters"
                                        data-count="{{ $totalExpenseAmount }}">{{ number_format($totalExpenseAmount, 2) }}</span>
                                    @if ($currencyPosition === 'right')
                                        {{ $currencySymbol }}
                                    @endif
                                </h5>
                                <h6>Total Expense Amount</h6>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if (in_array($role, ['sales-manager']))
                <div @class([
                    'col-lg-3 col-sm-6 col-6 d-flex' => in_array($role, [
                        'inventory-manager',
                        'admin',
                        'sub-admin',
                    ]),
                    'col-lg-4 col-sm-6 col-6 d-flex' => $role === 'sales-manager',
                ])>
                    <a href="{{ route('sales.list') }}" class="color_box box4">
                        <div class="dash-count das1">
                            <div class="dash-counts">
                                <h4>
                                    @if ($currencyPosition === 'left')
                                        {{ $currencySymbol }}
                                    @endif
                                    <span class="counters"
                                        data-count="{{ $totalSalesAmount }}">{{ number_format($totalSalesAmount, 2) }}</span>
                                    @if ($currencyPosition === 'right')
                                        {{ $currencySymbol }}
                                    @endif
                                </h4>
                                <h5>Total Sales Amount</h5>
                            </div>
                            <div class="dash-imgs">
                                <i data-feather="user-check"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if ($showHrDashboardSection && in_array($role, ['inventory-manager', 'admin', 'sub-admin']) && ($showHrStaffStrength || $showHrActiveStaff || $showHrMonthlyAttendance || $showHrPersonalProgress || $showHrAttendancePattern || $showHrSalaryPayrollTrend || $showHrPayrollSnapshot || $showHrAttendanceWatch || $showHrPayrollStatus))
                <div class="col-12 hr-section mt-0 pt-0">
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                        <div>
                            <h4 class="mb-1">HR Workforce Overview</h4>
                            {{-- <p class="text-muted mb-0">Staff strength, attendance performance, payroll progress, and advance exposure.</p> --}}
                        </div>
                        <a href="{{ route('staff.list') }}" class="btn btn-sm btn-primary">View Staff</a>
                    </div>
                </div>

                @if ($showHrStaffStrength)
                <div class="col-lg-3 col-sm-6 col-6 d-flex hr-section">
                    <a href="{{ route('staff.list') }}" class="color_box w-100">
                    <div class="card flex-fill hr-metric" style="cursor:pointer;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="hr-metric-label">Staff</div>
                            <div class="hr-metric-value">{{ number_format($totalStaff, 0) }}</div>
                            <p class="hr-metric-note">{{ number_format($activeStaff, 0) }} active, {{ number_format($newStaffThisMonth, 0) }} joined this month</p>
                        </div>
                    </div>
                    </a>
                </div>
                @endif

                @if ($showHrActiveStaff)
                <div class="col-lg-3 col-sm-6 col-6 d-flex hr-section">
                    <a href="{{ route('attendence.summary') }}" class="color_box w-100">
                    <div class="card flex-fill hr-metric" style="cursor:pointer;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="hr-metric-label">Today Attendance</div>
                            <div class="hr-metric-value">{{ number_format($todayPresentStaff, 0) }}</div>
                            <p class="hr-metric-note">{{ number_format($todayAbsentStaff, 0) }} absent, {{ number_format($todayUnmarkedStaff, 0) }} unmarked</p>
                        </div>
                    </div>
                    </a>
                </div>
                @endif

                @if ($showHrMonthlyAttendance)
                <div class="col-lg-3 col-sm-6 col-6 d-flex hr-section">
                    <a href="{{ route('attendence.summary') }}" class="color_box w-100">
                    <div class="card flex-fill hr-metric" style="cursor:pointer;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="hr-metric-label">Monthly Attendance</div>
                            <div class="hr-metric-value">{{ $monthlyAttendanceRate }}%</div>
                            <p class="hr-metric-note">Based on marked present, half-day, and absent entries</p>
                        </div>
                    </div>
                    </a>
                </div>
                @endif

                @if ($showHrPersonalProgress)
                <div class="col-lg-3 col-sm-6 col-6 d-flex hr-section">
                    <a href="{{ route('payroll.list') }}" class="color_box w-100">
                    <div class="card flex-fill hr-metric" style="cursor:pointer;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="hr-metric-label">Payroll Progress</div>
                            <div class="hr-metric-value">{{ number_format($salaryPaidCount, 0) }}/{{ number_format($totalStaff, 0) }}</div>
                            <p class="hr-metric-note">{{ number_format($salaryPendingCount, 0) }} pending this month</p>
                        </div>
                    </div>
                    </a>
                </div>
                @endif



                @if ($showHrPayrollSnapshot)
                <div class="col-lg-4 col-sm-12 col-12 d-flex hr-section">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">Payroll</h4>
                                <a href="{{ route('payroll.list') }}" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="table-responsive dataview custom-table-scroll">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Metric</th>
                                            <th class="details-column">Details</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div>
                                                    Paid this month
                                                    <div class="collapse mobile-details-collapse d-md-none"
                                                        id="payroll-snapshot-paid">
                                                        <div class="mobile-details-row">
                                                            <span class="mobile-details-label">Value:</span>
                                                            <span class="mobile-details-value">
                                                                @if ($currencyPosition === 'left'){{ $currencySymbol }}@endif
                                                                {{ number_format($salaryPaidAmount, 2) }}
                                                                @if ($currencyPosition === 'right'){{ $currencySymbol }}@endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="details-column">
                                                <a href="#payroll-snapshot-paid" class="toggle-details" data-bs-toggle="collapse">
                                                    <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                </a>
                                            </td>
                                            <td class="text-end d-none d-md-table-cell">
                                                @if ($currencyPosition === 'left'){{ $currencySymbol }}@endif
                                                {{ number_format($salaryPaidAmount, 2) }}
                                                @if ($currencyPosition === 'right'){{ $currencySymbol }}@endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div>
                                                    Outstanding advances
                                                    <div class="collapse mobile-details-collapse d-md-none"
                                                        id="payroll-snapshot-advances">
                                                        <div class="mobile-details-row">
                                                            <span class="mobile-details-label">Value:</span>
                                                            <span class="mobile-details-value">
                                                                @if ($currencyPosition === 'left'){{ $currencySymbol }}@endif
                                                                {{ number_format($advanceOutstanding, 2) }}
                                                                @if ($currencyPosition === 'right'){{ $currencySymbol }}@endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="details-column">
                                                <a href="#payroll-snapshot-advances" class="toggle-details" data-bs-toggle="collapse">
                                                    <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                </a>
                                            </td>
                                            <td class="text-end d-none d-md-table-cell">
                                                @if ($currencyPosition === 'left'){{ $currencySymbol }}@endif
                                                {{ number_format($advanceOutstanding, 2) }}
                                                @if ($currencyPosition === 'right'){{ $currencySymbol }}@endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div>
                                                    Salary pending staff
                                                    <div class="collapse mobile-details-collapse d-md-none"
                                                        id="payroll-snapshot-pending">
                                                        <div class="mobile-details-row">
                                                            <span class="mobile-details-label">Value:</span>
                                                            <span class="mobile-details-value">{{ number_format($salaryPendingCount, 0) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="details-column">
                                                <a href="#payroll-snapshot-pending" class="toggle-details" data-bs-toggle="collapse">
                                                    <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                </a>
                                            </td>
                                            <td class="text-end d-none d-md-table-cell">{{ number_format($salaryPendingCount, 0) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if ($showHrAttendanceWatch)
                <div class="col-lg-4 col-sm-12 col-12 d-flex hr-section">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">Attendance</h4>
                                <a href="{{ route('attendence.calendar') }}" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="table-responsive dataview custom-table-scroll">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Staff</th>
                                            <th class="details-column">Details</th>
                                            <th>Present</th>
                                            <th>Absent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($attendanceWatch as $index => $staffRow)
                                            <tr>
                                                <td>
                                                    <div>
                                                        {{ $staffRow->name }}
                                                        <div class="collapse mobile-details-collapse d-md-none"
                                                            id="attendance-details-{{ $staffRow->id ?? $index }}">
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Present:</span>
                                                                <span class="mobile-details-value">{{ number_format($staffRow->present_days, 0) }}</span>
                                                            </div>
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Absent:</span>
                                                                <span class="mobile-details-value">
                                                                    <span class="hr-mini-badge bg-light text-dark">{{ number_format($staffRow->absent_days, 0) }}</span>
                                                                    @if ($staffRow->half_days > 0)
                                                                        <div class="text-muted small">{{ number_format($staffRow->half_days, 0) }} half-day</div>
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="details-column">
                                                    <a href="#attendance-details-{{ $staffRow->id ?? $index }}" class="toggle-details" data-bs-toggle="collapse">
                                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                    </a>
                                                </td>
                                                <td class="d-none d-md-table-cell">{{ number_format($staffRow->present_days, 0) }}</td>
                                                <td class="d-none d-md-table-cell">
                                                    <span class="hr-mini-badge bg-light text-dark">{{ number_format($staffRow->absent_days, 0) }}</span>
                                                    @if ($staffRow->half_days > 0)
                                                        <div class="text-muted small">{{ number_format($staffRow->half_days, 0) }} half-day</div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No attendance data found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if ($showHrPayrollStatus)
                <div class="col-lg-4 col-sm-12 col-12 d-flex hr-section">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3 ">
                            <h4 class="card-title mb-0">Payroll Status</h4>
                              <a href="{{ route('payroll.list') }}  " class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="table-responsive dataview custom-table-scroll">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Staff</th>
                                            <th class="details-column">Details</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($payrollStatusRows as $index => $payrollRow)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <a href="{{ route('staff.view', $payrollRow['id']) }}">{{ $payrollRow['name'] }}</a>
                                                        <div class="text-muted small">{{ $payrollRow['phone'] ?? 'N/A' }}</div>
                                                        <div class="collapse mobile-details-collapse d-md-none"
                                                            id="payroll-status-details-{{ $payrollRow['id'] ?? $index }}">
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Status:</span>
                                                                <span class="mobile-details-value">
                                                                    <span class="hr-mini-badge bg-light text-dark">{{ $payrollRow['status'] }}</span>
                                                                </span>
                                                            </div>
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Amount:</span>
                                                                <span class="mobile-details-value">
                                                                    @if ($currencyPosition === 'left'){{ $currencySymbol }}@endif
                                                                    {{ number_format($payrollRow['amount'], 2) }}
                                                                    @if ($currencyPosition === 'right'){{ $currencySymbol }}@endif
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="details-column">
                                                    <a href="#payroll-status-details-{{ $payrollRow['id'] ?? $index }}" class="toggle-details" data-bs-toggle="collapse">
                                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                    </a>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <span class="hr-mini-badge bg-light text-dark">{{ $payrollRow['status'] }}</span>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    @if ($currencyPosition === 'left'){{ $currencySymbol }}@endif
                                                    {{ number_format($payrollRow['amount'], 2) }}
                                                    @if ($currencyPosition === 'right'){{ $currencySymbol }}@endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No staff records found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                   @if ($showHrAttendancePattern)
                <div class="col-lg-7 col-sm-12 col-12 d-flex hr-section">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <div class="card-title mb-0">7 Day Attendance</div>
                        </div>
                        <div class="card-body">
                            <div id="hrAttendanceChart" class="hr-chart"></div>
                        </div>
                    </div>
                </div>
                @endif

                @if ($showHrSalaryPayrollTrend)
                <div class="col-lg-5 col-sm-12 col-12 d-flex hr-section">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <div class="card-title mb-0">Salary Payout</div>
                        </div>
                        <div class="card-body">
                            <div id="hrSalaryChart" class="hr-chart"></div>
                        </div>
                    </div>
                </div>
                @endif
            @endif

            @if ($showErpDashboard && in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']) && $showErpSalesInvoiceCount)
                <div @class([
                    'col-lg-3 col-sm-6 col-6 d-flex' => in_array($role, [
                        'inventory-manager',
                        'admin',
                        'sub-admin',
                    ]),
                    'col-lg-4 col-sm-6 col-6 d-flex' => $role === 'sales-manager',
                ])>
                    <a href="{{ route('sales.list') }}" class="color_box box1">
                        <div class="dash-count das3">
                            <div class="dash-counts">
                                <h4>{{ number_format($salesInvoiceCount, 0) }}</h4>
                                <h5>Sales Invoice</h5>
                            </div>
                            <div class="dash-imgs">
                                <i data-feather="file"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if (in_array($role, ['purchase-manager']))
                <div @class([
                    'col-lg-3 col-sm-6 col-6 d-flex' => in_array($role, [
                        'inventory-manager',
                        'admin',
                        'sub-admin',
                    ]),
                    'col-lg-4 col-sm-6 col-6 d-flex' => $role === 'purchase-manager',
                ])>
                    <a href="{{ route('purchase.lists') }}" class="color_box box1">
                        <div class="dash-count das3">
                            <div class="dash-counts">
                                <h4>
                                    @if ($currencyPosition === 'left')
                                        {{ $currencySymbol }}
                                    @endif
                                    <span class="counters" data-count="{{ $totalPurchaseAmount }}">
                                        {{ number_format($totalPurchaseAmount, 2) }}
                                    </span>
                                    @if ($currencyPosition === 'right')
                                        {{ $currencySymbol }}
                                    @endif
                                </h4>
                                <h5>Total Purchase Amount</h5>
                            </div>
                            <div class="dash-imgs">
                                <i data-feather="file"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if ($showErpDashboard && in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin']) && $showErpPurchaseInvoiceCount)
                <div @class([
                    'col-lg-3 col-sm-6 col-6 d-flex' => in_array($role, [
                        'inventory-manager',
                        'admin',
                        'sub-admin',
                    ]),
                    'col-lg-4 col-sm-6 col-6 d-flex' => $role === 'purchase-manager',
                ])>
                    <a href="{{ route('purchase.lists') }}" class="color_box box2">
                        <div class="dash-count das2">
                            <div class="dash-counts">
                                <h4>{{ number_format($purchaseInvoiceCount, 0) }}</h4>
                                <h5>Purchase Invoice</h5>
                            </div>
                            <div class="dash-imgs">
                                <i data-feather="file-text"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if ($showErpDashboard && in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']) && $showErpCustomersCount)
                <div @class([
                    'col-lg-3 col-sm-6 col-6 d-flex' => in_array($role, [
                        'inventory-manager',
                        'admin',
                        'sub-admin',
                    ]),
                    'col-lg-4 col-sm-6 col-6 d-flex' => $role === 'sales-manager',
                ])>
                    <a href="{{ route('customer.list') }}" class="color_box box3">
                        <div class="dash-count">
                            <div class="dash-counts">
                                <h4>{{ number_format($customerCount, 0) }}</h4>
                                <h5>Customers</h5>
                            </div>
                            <div class="dash-imgs">
                                <i data-feather="user"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if ($showErpDashboard && in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin']) && $showErpVendorsCount)
                <div @class([
                    'col-lg-3 col-sm-6 col-6 d-flex' => in_array($role, [
                        'inventory-manager',
                        'admin',
                        'sub-admin',
                    ]),
                    'col-lg-4 col-sm-6 col-6 d-flex' => $role === 'purchase-manager',
                ])>
                    <a href="{{ route('vendor.list') }}" class="color_box box4">
                        <div class="dash-count das1">
                            <div class="dash-counts">
                                <h4>{{ number_format($vendorCount, 0) }}</h4>
                                <h5>Vendors</h5>
                            </div>
                            <div class="dash-imgs">
                                <i data-feather="user-check"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

        </div>

        @php
            use Carbon\Carbon;
            $currentYear = Carbon::now()->year;
            $previousYear = $currentYear - 1;
            $showErpSalesChartCard = $showErpDashboard && in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']) && $showErpSalesChart;
            $showErpPurchaseChartCard = $showErpDashboard && in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin']) && $showErpPurchaseChart;
            $showErpProductsDeliveryCard = $showErpDashboard && in_array($role, ['inventory-manager','sales-manager','purchase-manager','admin','sub-admin']) && $showErpProductsDelivery;
            $erpMainSectionCount = collect([
                $showErpSalesChartCard,
                $showErpPurchaseChartCard,
                $showErpProductsDeliveryCard,
            ])->filter()->count();
            $erpMainSectionClass = $erpMainSectionCount === 1 ? 'col-lg-12' : ($erpMainSectionCount === 2 ? 'col-lg-6' : 'col-lg-4');
        @endphp

        <div class="row">

              @if ($showErpDashboard && in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']) && $showErpRecentSales)
                <div class="col-md-6">
                    <div class="card">
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
                                            <th class="details-column">Details</th>
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
                                                            <a href="/sales-details/{{ $item->order_id }}"
                                                                class="d-flex align-items-center text-decoration-none">
                                                                <span
                                                                    class="order-id">{{ $item->order_number ?? 'N/A' }}</span>
                                                            </a>
                                                        </div>


                                                        <!-- Collapsible Details for Mobile -->
                                                        <div class="collapse mobile-details-collapse d-md-none"
                                                            id="sales-details-{{ $item->order_id ?? $index }}">
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Product:</span>
                                                                <span class="mobile-details-value">
                                                                    @php
                                                                        $images = json_decode($item->images, true);
                                                                        $imagePath =
                                                                            !empty($images) && isset($images[0])
                                                                                ? env('ImagePath') .
                                                                                    'storage/' .
                                                                                    $images[0]
                                                                                : env('ImagePath') .
                                                                                    '/admin/assets/img/product/noimage.png';
                                                                    @endphp
                                                                    <div class="mobile-product-info">
                                                                        <span class="product-name">{{ $item->product_name ?? 'N/A' }}</span>
                                                                        <img src="{{ asset($imagePath) }}" alt="Product"
                                                                            style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px;">
                                                                    </div>
                                                                </span>
                                                            </div>
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Grand Total:</span>
                                                                <span class="mobile-details-value">
                                                                    @if ($currencyPosition === 'left')
                                                                        {{ $currencySymbol }}
                                                                    @endif
                                                                    {{ number_format($item->total_amount ?? 0, 2) }}
                                                                    @if ($currencyPosition === 'right')
                                                                        {{ $currencySymbol }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Sale Date:</span>
                                                                <span class="mobile-details-value">
                                                                    {{ Carbon::parse($item->order_date)->format('d F Y ') }}
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="details-column">
                                                    <a href="#sales-details-{{ $item->order_id ?? $index }}"
                                                        class="toggle-details" data-bs-toggle="collapse">
                                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                    </a>
                                                </td>

                                                <td class="productimgname d-none d-md-table-cell">
                                                    <a href="/product-view/{{ $item->product_id }}"
                                                        class="product-link">
                                                        <img src="{{ asset($imagePath) }}" alt="Product Image"
                                                            style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px; flex-shrink: 0;">
                                                        <span class="product-name">
                                                            {{ $item->product_name ?? 'N/A' }}
                                                        </span>
                                                    </a>
                                                </td>

                                                <td class="d-none d-md-table-cell">
                                                    @if ($currencyPosition === 'left')
                                                        {{ $currencySymbol }}
                                                    @endif
                                                    {{ number_format($item->total_amount ?? 0, 2) }}
                                                    @if ($currencyPosition === 'right')
                                                        {{ $currencySymbol }}
                                                    @endif
                                                </td>

                                                <td class="d-none d-md-table-cell">
                                                    {{ Carbon::parse($item->order_date)->format('d F Y') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No sales records found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Latest Purchases Table - UPDATED -->
            @if ($showErpDashboard && in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin']) && $showErpRecentPurchases)
                <div class="col-md-6">
                    <div class="card mb-0">
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
                                            <th class="details-column">Details</th>
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
                                                         <div class="d-md-none text-muted large  mt-1 ms-2">
                                                            {{ $item->vendor_name ?? 'N/A' }}
                                                        </div>
                                                        <div style="display: flex; align-items: center;">
                                                            <a href="/print-purchase/{{ $item->invoice_id }}"
                                                                class="d-flex align-items-center text-decoration-none">
                                                                <span
                                                                    class="order-id">{{ $item->bill_no ?? 'N/A' }}</span>
                                                            </a>
                                                        </div>


                                                        <!-- Collapsible Details for Mobile -->
                                                        <div class="collapse mobile-details-collapse d-md-none"
                                                            id="purchase-details-{{ $item->invoice_id ?? $index }}">
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Product:</span>
                                                                <span class="mobile-details-value">
                                                                    @php
                                                                        $images = json_decode($item->images, true);
                                                                        $imagePath = !empty($images[0])
                                                                            ? env('ImagePath') . 'storage/' . $images[0]
                                                                            : env('ImagePath') .
                                                                                'admin/assets/img/product/noimage.png';
                                                                    @endphp
                                                                    <div class="mobile-product-info">
                                                                        <span class="product-name">{{ $item->product_name ?? 'N/A' }}</span>
                                                                        <img src="{{ asset($imagePath) }}" alt="Product"
                                                                            style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px;">
                                                                    </div>
                                                                </span>
                                                            </div>
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Grand Total:</span>
                                                                <span class="mobile-details-value">
                                                                    @if ($currencyPosition === 'left')
                                                                        {{ $currencySymbol }}
                                                                    @endif
                                                                    {{ number_format($item->grand_total ?? 0, 2) }}
                                                                    @if ($currencyPosition === 'right')
                                                                        {{ $currencySymbol }}
                                                                    @endif
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

                                                <td class="details-column">
                                                    <a href="#purchase-details-{{ $item->invoice_id ?? $index }}"
                                                        class="toggle-details" data-bs-toggle="collapse">
                                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                    </a>
                                                </td>

                                                <td class="productimgname d-none d-md-table-cell">
                                                    <a href="/product-view/{{ $item->product_id }}"
                                                        class="product-link">
                                                        <img src="{{ asset($imagePath) }}" alt="Product Image"
                                                            style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px; flex-shrink: 0;">
                                                        <span class="product-name">
                                                            {{ $item->product_name ?? 'N/A' }}
                                                        </span>
                                                    </a>
                                                </td>

                                                <td class="d-none d-md-table-cell">
                                                    @if ($currencyPosition === 'left')
                                                        {{ $currencySymbol }}
                                                    @endif
                                                    {{ number_format($item->grand_total ?? 0, 2) }}
                                                    @if ($currencyPosition === 'right')
                                                        {{ $currencySymbol }}
                                                    @endif
                                                </td>

                                                <td class="d-none d-md-table-cell">
                                                    {{ \Carbon\Carbon::parse($item->purchase_date)->format('d F Y') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No purchase records
                                                    found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($showErpSalesChartCard)
                <div class="{{ $erpMainSectionClass }} col-sm-12 col-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title mb-0">Sales</div>
                            <div class="chart-select-container">
                                <select class="form-control form-control-sm chart-select2" id="salesYearSelect" style="width: 130px;">
                                    <option value="month">This month ({{ date('F') }})</option>
                                    <option value="{{ $previousYear }}">Previous year ({{ $previousYear }})</option>
                                    <option value="{{ $currentYear }}" selected>This year ({{ $currentYear }})</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body chart-set">
                            <div class="h-250" style="width: 100%;" id="saleschart"></div>
                        </div>
                    </div>
                </div>
            @endif
            @if ($showErpPurchaseChartCard)
                <div class="{{ $erpMainSectionClass }} col-sm-12 col-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title mb-0">Purchases</div>
                            <div class="chart-select-container">
                                <select class="form-control form-control-sm chart-select2" id="purchaseYearSelect" style="width: 130px;">
                                    <option value="month">This month ({{ date('F') }})</option>
                                    <option value="{{ $previousYear }}">Previous year ({{ $previousYear }})</option>
                                    <option value="{{ $currentYear }}" selected>This year ({{ $currentYear }})</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body chart-set">
                            <div class="h-250" style="width: 100%;" id="purchasechart"></div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($showErpProductsDeliveryCard)
                <div class="{{ $erpMainSectionClass }} col-sm-12 col-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title mb-0">Products Delivery</div>
                            <a href="{{ route('sales.list') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body p-2">
                            <div class="table-responsive pending-delivery-scroll">
                                <table class="table table-sm mb-0">
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
                                                    <a href="{{ route('sales.delivery', $delivery->order_id) }}">#{{ $delivery->order->order_number ?? $delivery->order_id }}</a>
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
            @endif




            @if ($showCrmDashboardSection && in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']) && ($showCrmLeadPipeline || $showCrmConversion || $showCrmFollowupLoad || $showCrmMeetingMomentum || $showCrmLeadStatusMix || $showCrmActivityTrend || $showCrmPipelineQuality || $showCrmRecentLeads || $showCrmNext7Days))
                <div class="col-12 crm-section mt-0 pt-0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="mb-1">CRM Pipeline Overview</h4>
                            {{-- <p class="text-muted mb-0">Lead movement, customer conversion, and pending relationship work.</p> --}}
                        </div>
                        <a href="{{ route('lead.list') }}" class="btn btn-sm btn-primary">View Leads</a>
                    </div>
                </div>

                @if ($showCrmLeadPipeline)
                <div class="col-lg-3 col-sm-6 col-6 d-flex crm-section">
                    <a href="{{ route('lead.list') }}" class="color_box w-100">
                    <div class="card flex-fill crm-metric" style="cursor:pointer;">
                        <div class="card-body">
                            <div class="crm-metric-label">Lead</div>
                            <div class="crm-metric-value">{{ number_format($totalLeads, 0) }}</div>
                            <p class="crm-metric-note">{{ number_format($newLeadsThisMonth, 0) }} new this month</p>
                        </div>
                    </div>
                    </a>
                </div>
                @endif

                @if ($showCrmConversion)
                <div class="col-lg-3 col-sm-6 col-6 d-flex crm-section">
                    <a href="{{ route('lead.list') }}" class="color_box w-100">
                    <div class="card flex-fill crm-metric" style="cursor:pointer;">
                        <div class="card-body">
                            <div class="crm-metric-label">Conversion</div>
                            <div class="crm-metric-value">{{ $crmConversionRate }}%</div>
                            <p class="crm-metric-note">{{ number_format($convertedLeadCount, 0) }} leads became customers</p>
                        </div>
                    </div>
                    </a>
                </div>
                @endif

                @if ($showCrmFollowupLoad)
                <div class="col-lg-3 col-sm-6 col-6 d-flex crm-section">
                    <a href="{{ route('followup.list') }}" class="color_box w-100">
                    <div class="card flex-fill crm-metric" style="cursor:pointer;">
                        <div class="card-body">
                            <div class="crm-metric-label">Follow-up</div>
                            <div class="crm-metric-value">{{ number_format($pendingFollowUps, 0) }}</div>
                            <p class="crm-metric-note">{{ number_format($overdueFollowUps, 0) }} overdue and need action</p>
                        </div>
                    </div>
                    </a>
                </div>
                @endif

                @if ($showCrmMeetingMomentum)
                <div class="col-lg-3 col-sm-6 col-6 d-flex crm-section">
                    <a href="{{ route('meeting.list') }}" class="color_box w-100">
                    <div class="card flex-fill crm-metric" style="cursor:pointer;">
                        <div class="card-body">
                            <div class="crm-metric-label">Meeting</div>
                            <div class="crm-metric-value">{{ number_format($meetingsThisWeek, 0) }}</div>
                            <p class="crm-metric-note">{{ number_format($completedMeetingsThisMonth, 0) }} completed this month</p>
                        </div>
                    </div>
                    </a>
                </div>
                @endif


                @if ($showCrmPipelineQuality)
                <div class="col-lg-4 col-sm-12 col-12 d-flex crm-section">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">Pipeline Quality</h4>
                                <a href="{{ route('lead.list') }}" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="table-responsive dataview custom-table-scroll">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th class="details-column">Details</th>
                                            <th>Leads</th>
                                            <th>Share</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($leadStatusTable as $index => $statusRow)
                                            <tr>
                                                <td>
                                                    <div>
                                                        {{ $statusRow['status'] }}
                                                        <div class="collapse mobile-details-collapse d-md-none"
                                                            id="pipeline-details-{{ $index }}">
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Leads:</span>
                                                                <span class="mobile-details-value">{{ number_format($statusRow['total'], 0) }}</span>
                                                            </div>
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Share:</span>
                                                                <span class="mobile-details-value">{{ $statusRow['share'] }}%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="details-column">
                                                    <a href="#pipeline-details-{{ $index }}" class="toggle-details" data-bs-toggle="collapse">
                                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                    </a>
                                                </td>
                                                <td class="d-none d-md-table-cell">{{ number_format($statusRow['total'], 0) }}</td>
                                                <td class="d-none d-md-table-cell">{{ $statusRow['share'] }}%</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No lead data found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if ($showCrmRecentLeads)
                <div class="col-lg-4 col-sm-12 col-12 d-flex crm-section">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">Recent Leads</h4>
                                <a href="{{ route('lead.list') }}" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="table-responsive dataview custom-table-scroll">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Lead</th>
                                            <th class="details-column">Details</th>
                                            <th>Status</th>
                                            <th>Owner</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($latestLeads as $index => $lead)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <a href="{{ route('lead.view', $lead->id) }}">{{ $lead->name }}</a>
                                                        <div class="text-muted small">{{ $lead->company_name ?? $lead->phone }}</div>
                                                        <div class="collapse mobile-details-collapse d-md-none"
                                                            id="lead-details-{{ $lead->id ?? $index }}">
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Status:</span>
                                                                <span class="mobile-details-value">
                                                                    <span class="crm-mini-badge bg-light text-dark">{{ $lead->lead_status }}</span>
                                                                </span>
                                                            </div>
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Owner:</span>
                                                                <span class="mobile-details-value">{{ $lead->assignedUser->name ?? 'Unassigned' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="details-column">
                                                    <a href="#lead-details-{{ $lead->id ?? $index }}" class="toggle-details" data-bs-toggle="collapse">
                                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                    </a>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <span class="crm-mini-badge bg-light text-dark">{{ $lead->lead_status }}</span>
                                                </td>
                                                <td class="d-none d-md-table-cell">{{ $lead->assignedUser->name ?? 'Unassigned' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No recent leads found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if ($showCrmNext7Days)
                <div class="col-lg-4 col-sm-12 col-12 d-flex crm-section">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">Next 7 Days</h4>
                                <a href="{{ route('followup.list') }}" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="table-responsive dataview custom-table-scroll">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Work</th>
                                            <th class="details-column">Details</th>
                                            <th>Party</th>
                                            <th>Due</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($upcomingCrmActions as $index => $action)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <span class="crm-mini-badge bg-light text-dark">{{ $action['type'] }}</span>
                                                        <div class="mt-1">{{ $action['title'] }}</div>
                                                        <div class="collapse mobile-details-collapse d-md-none"
                                                            id="crm-action-details-{{ $index }}">
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Party:</span>
                                                                <span class="mobile-details-value">
                                                                    {{ $action['party'] }}
                                                                    <div class="text-muted small">{{ $action['owner'] }}</div>
                                                                </span>
                                                            </div>
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Due:</span>
                                                                <span class="mobile-details-value">{{ $action['date'] ? $action['date']->format('d M, h:i A') : 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="details-column">
                                                    <a href="#crm-action-details-{{ $index }}" class="toggle-details" data-bs-toggle="collapse">
                                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                    </a>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    {{ $action['party'] }}
                                                    <div class="text-muted small">{{ $action['owner'] }}</div>
                                                </td>
                                                <td class="d-none d-md-table-cell">{{ $action['date'] ? $action['date']->format('d M, h:i A') : 'N/A' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No upcoming CRM work</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif


                @if ($showCrmLeadStatusMix)
                <div class="col-lg-5 col-sm-12 col-12 d-flex crm-section">
                    <div class="card flex-fill">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title mb-0">Lead Status Mix</div>
                            <a href="{{ route('lead.list') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <div id="crmPipelineChart" class="crm-chart"></div>
                        </div>
                    </div>
                </div>
                @endif

                @if ($showCrmActivityTrend)
                <div class="col-lg-7 col-sm-12 col-12 d-flex crm-section">
                    <div class="card flex-fill">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title mb-0">CRM Activity Trend</div>
                            <a href="{{ route('lead.list') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <div id="crmActivityChart" class="crm-chart"></div>
                        </div>
                    </div>
                </div>
                @endif

            @endif

        </div>
    </div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Toggle details icon animation
            $(document).on('click', '.toggle-details', function() {
                let icon = $(this).find('i');
                if (icon.hasClass('fa-plus-circle')) {
                    icon.removeClass('fa-plus-circle')
                        .addClass('fa-minus-circle')
                        .css('color', 'red');
                } else {
                    icon.removeClass('fa-minus-circle')
                        .addClass('fa-plus-circle')
                        .css('color', '#ff9f43');
                }
            });
        });
    </script>
    <script>
        const currentYear = {{ $currentYear }};
        const previousYear = {{ $previousYear }};
        const salesThisMonth = @json($salesChartThisMonth);
        const purchaseThisMonth = @json($purchaseChartThisMonth);

        const salesChartData = {
            [currentYear]: @json($salesChartthisyear),
            [previousYear]: @json($salesChartpreviousyear),
            'thisMonth': salesThisMonth
        };

        const purchaseChartData = {
            [currentYear]: @json($purchaseChartthisyear),
            [previousYear]: @json($purchaseChartpreviousyear),
            'thisMonth': purchaseThisMonth
        };

        const leadStatusLabels = @json($leadStatusLabels ?? []);
        const leadStatusCounts = @json($leadStatusCounts ?? []);
        const crmMonthlyLabels = @json($crmMonthlyLabels ?? []);
        const crmMonthlyLeads = @json($crmMonthlyLeads ?? []);
        const crmMonthlyFollowUps = @json($crmMonthlyFollowUps ?? []);
        const crmMonthlyMeetings = @json($crmMonthlyMeetings ?? []);
        const hrAttendanceLabels = @json($hrAttendanceLabels ?? []);
        const hrAttendancePresent = @json($hrAttendancePresent ?? []);
        const hrAttendanceAbsent = @json($hrAttendanceAbsent ?? []);
        const hrAttendanceHalfDay = @json($hrAttendanceHalfDay ?? []);
        const hrSalaryLabels = @json($hrSalaryLabels ?? []);
        const hrSalaryPaidAmounts = @json($hrSalaryPaidAmounts ?? []);
        const hrSalaryPaidCounts = @json($hrSalaryPaidCounts ?? []);

        // ✅ Updated chart options
        const options = {
            grid: {
                borderWidth: 1,
                borderColor: 'rgba(67, 87, 133, .09)',
                hoverable: true
            },
            xaxis: {
                ticks: [], // dynamically set below
                tickColor: 'rgba(67, 87, 133, .09)',
                font: {
                    color: '#8e9cad',
                    size: 9
                },
                autoscaleMargin: 0.02
            },
            yaxis: {
                tickColor: 'rgba(67, 87, 133, .09)',
                font: {
                    color: '#8e9cad',
                    size: 10
                },
                tickFormatter: function(val, axis) {
                    return val.toLocaleString();
                }
            },
            legend: {
                show: true,
                position: "nw"
            },
            tooltip: true,
            tooltipOpts: {
                content: function(label, xval, yval, flotItem) {
                    return label + ": " + yval.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },
                shifts: {
                    x: 10,
                    y: 20
                },
                defaultTheme: false
            }
        };

        // ✅ Fixed: Tick generator for day labels 1–31
        function getDayTicks(length) {
            return Array.from({
                length
            }, (_, i) => [i, (i + 1).toString()]);
        }

        // Optional: Only show every 2nd or 5th tick (use if labels are still crowded)
        // function getDayTicks(length) {
        //     return Array.from({ length }, (_, i) => [i, (i + 1).toString()])
        //         .filter(([, label]) => parseInt(label) % 2 === 1); // odd days only
        // }

        function renderChart(containerId, label, data, barColor, isMonthly = true) {
            const series = data.map((val, idx) => [idx, val]);

            options.xaxis.ticks = isMonthly ? [
                    [0, 'Jan'],
                    [1, 'Feb'],
                    [2, 'Mar'],
                    [3, 'Apr'],
                    [4, 'May'],
                    [5, 'Jun'],
                    [6, 'Jul'],
                    [7, 'Aug'],
                    [8, 'Sep'],
                    [9, 'Oct'],
                    [10, 'Nov'],
                    [11, 'Dec']
                ] :
                getDayTicks(data.length); // ✅ this ensures 1–31 labels

            $.plot(containerId, [{
                label,
                data: series,
                bars: {
                    show: true,
                    barWidth: 0.3, // ✅ adjusted for tight spacing
                    align: "center",
                    fillColor: barColor
                },
                color: barColor
            }], options);
        }

        // $(document).ready(function () {
        //     renderChart('#saleschart', 'Sales', salesChartData[currentYear], '#44c4fa');
        //     renderChart('#purchasechart', 'Purchases', purchaseChartData[currentYear], '#fa6c7c');

        //     $('#salesYearSelect').on('change', function () {
        //         const year = $(this).val();
        //         if (year === 'month') {
        //             renderChart('#saleschart', 'Sales (This Month)', salesChartData['thisMonth'], '#44c4fa', false);
        //         } else {
        //             renderChart('#saleschart', 'Sales', salesChartData[year], '#44c4fa');
        //         }
        //     });

        //     $('#purchaseYearSelect').on('change', function () {
        //         const year = $(this).val();
        //         if (year === 'month') {
        //             renderChart('#purchasechart', 'Purchases (This Month)', purchaseChartData['thisMonth'], '#fa6c7c', false);
        //         } else {
        //             renderChart('#purchasechart', 'Purchases', purchaseChartData[year], '#fa6c7c');
        //         }
        //     });
        // });

        $(document).ready(function() {
            $('.chart-select2').select2({
                minimumResultsForSearch: -1,
                width: '100%'
            });

            const $misplacedHrSections = $('.dashboard-widgets-row > .hr-section');
            if ($misplacedHrSections.length) {
                const $lastCrmSection = $('.crm-section').last();
                const $latestSalesSection = $('.card-title')
                    .filter(function() {
                        return $(this).text().trim() === 'Latest Sales';
                    })
                    .closest('.col-md-6');

                if ($lastCrmSection.length) {
                    $misplacedHrSections.insertAfter($lastCrmSection);
                } else if ($latestSalesSection.length) {
                    $misplacedHrSections.insertBefore($latestSalesSection);
                }
            }

            if ($('.crm-section').length && !$('.crm-zone').length) {
                $('.crm-section').wrapAll('<div class="col-12 pulse-zone crm-zone"><div class="row pulse-crm"></div></div>');
            }

            if ($('.hr-section').not('.dashboard-widgets-row > .hr-section').length && !$('.hr-zone').length) {
                $('.hr-section').not('.dashboard-widgets-row > .hr-section')
                    .wrapAll('<div class="col-12 pulse-zone hr-zone my-3"><div class="row pulse-hr"></div></div>');
            }

            @if ($showCrmDashboardSection && in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']))
                if (document.querySelector('#crmPipelineChart')) {
                    new ApexCharts(document.querySelector('#crmPipelineChart'), {
                        chart: {
                            type: 'donut',
                            height: 260,
                            toolbar: {
                                show: false
                            }
                        },
                        labels: leadStatusLabels,
                        series: leadStatusCounts,
                        colors: ['#4361ee', '#10b981', '#ff9f43', '#ef4444', '#6b7280', '#06b6d4'],
                        legend: {
                            position: 'bottom',
                            fontSize: '12px'
                        },
                        dataLabels: {
                            enabled: false
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '68%'
                                }
                            }
                        },
                        noData: {
                            text: 'No lead data'
                        }
                    }).render();
                }

                if (document.querySelector('#crmActivityChart')) {
                    new ApexCharts(document.querySelector('#crmActivityChart'), {
                        chart: {
                            type: 'line',
                            height: 260,
                            toolbar: {
                                show: false
                            },
                            zoom: {
                                enabled: false
                            }
                        },
                        series: [{
                            name: 'Leads',
                            data: crmMonthlyLeads
                        }, {
                            name: 'Follow-ups',
                            data: crmMonthlyFollowUps
                        }, {
                            name: 'Meetings',
                            data: crmMonthlyMeetings
                        }],
                        colors: ['#4361ee', '#ff9f43', '#10b981'],
                        stroke: {
                            width: 3,
                            curve: 'smooth'
                        },
                        markers: {
                            size: 4
                        },
                        xaxis: {
                            categories: crmMonthlyLabels,
                            labels: {
                                style: {
                                    fontSize: '12px'
                                }
                            }
                        },
                        yaxis: {
                            min: 0,
                            forceNiceScale: true,
                            labels: {
                                formatter: function(value) {
                                    return Math.round(value);
                                }
                            }
                        },
                        grid: {
                            borderColor: '#edf0f5'
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right'
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return Math.round(value);
                                }
                            }
                        },
                        noData: {
                            text: 'No CRM activity'
                        }
                    }).render();
                }
            @endif

            @if ($showHrDashboardSection && in_array($role, ['inventory-manager', 'admin', 'sub-admin']))
                if (document.querySelector('#hrAttendanceChart')) {
                    new ApexCharts(document.querySelector('#hrAttendanceChart'), {
                        chart: {
                            type: 'bar',
                            height: 260,
                            stacked: true,
                            toolbar: {
                                show: false
                            }
                        },
                        series: [{
                            name: 'Present',
                            data: hrAttendancePresent
                        }, {
                            name: 'Half-day',
                            data: hrAttendanceHalfDay
                        }, {
                            name: 'Absent',
                            data: hrAttendanceAbsent
                        }],
                        colors: ['#10b981', '#ff9f43', '#ef4444'],
                        plotOptions: {
                            bar: {
                                columnWidth: '42%',
                                borderRadius: 3
                            }
                        },
                        xaxis: {
                            categories: hrAttendanceLabels
                        },
                        yaxis: {
                            min: 0,
                            forceNiceScale: true,
                            labels: {
                                formatter: function(value) {
                                    return Math.round(value);
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right'
                        },
                        grid: {
                            borderColor: '#edf0f5'
                        },
                        noData: {
                            text: 'No attendance data'
                        }
                    }).render();
                }

                if (document.querySelector('#hrSalaryChart')) {
                    new ApexCharts(document.querySelector('#hrSalaryChart'), {
                        chart: {
                            height: 260,
                            type: 'line',
                            toolbar: {
                                show: false
                            },
                            zoom: {
                                enabled: false
                            }
                        },
                        series: [{
                            name: 'Payout',
                            type: 'column',
                            data: hrSalaryPaidAmounts
                        }, {
                            name: 'Paid Staff',
                            type: 'line',
                            data: hrSalaryPaidCounts
                        }],
                        colors: ['#4361ee', '#10b981'],
                        stroke: {
                            width: [0, 3],
                            curve: 'smooth'
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '45%',
                                borderRadius: 3
                            }
                        },
                        xaxis: {
                            categories: hrSalaryLabels
                        },
                        yaxis: [{
                            min: 0,
                            labels: {
                                formatter: function(value) {
                                    return Math.round(value).toLocaleString();
                                }
                            }
                        }, {
                            opposite: true,
                            min: 0,
                            forceNiceScale: true,
                            labels: {
                                formatter: function(value) {
                                    return Math.round(value);
                                }
                            }
                        }],
                        grid: {
                            borderColor: '#edf0f5'
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right'
                        },
                        noData: {
                            text: 'No salary data'
                        }
                    }).render();
                }
            @endif

            @if (in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']))
                renderChart('#saleschart', 'Sales', salesChartData[currentYear], '#44c4fa');
                $('#salesYearSelect').on('change', function() {
                    const year = $(this).val();
                    if (year === 'month') {
                        renderChart('#saleschart', 'Sales (This Month)', salesChartData['thisMonth'],
                            '#44c4fa', false);
                    } else {
                        renderChart('#saleschart', 'Sales', salesChartData[year], '#44c4fa');
                    }
                });
            @endif

            @if (in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin']))
                renderChart('#purchasechart', 'Purchases', purchaseChartData[currentYear], '#fa6c7c');
                $('#purchaseYearSelect').on('change', function() {
                    const year = $(this).val();
                    if (year === 'month') {
                        renderChart('#purchasechart', 'Purchases (This Month)', purchaseChartData[
                            'thisMonth'], '#fa6c7c', false);
                    } else {
                        renderChart('#purchasechart', 'Purchases', purchaseChartData[year], '#fa6c7c');
                    }
                });
            @endif

        });
    </script>
    <script>
        $(document).on('change', '.pending-delivery-status', function() {
            const $select = $(this);
            const previousStatus = $select.data('current-status');
            const newStatus = $select.val();

            $select.addClass('is-saving').prop('disabled', true);

            $.ajax({
                url: $select.data('url'),
                method: 'POST',
                data: {
                    status: newStatus
                },
                success: function(response) {
                    if (response.status) {
                        $select.data('current-status', newStatus);
                    } else {
                        $select.val(previousStatus);
                        alert(response.message || 'Unable to update delivery status.');
                    }
                },
                error: function(xhr) {
                    $select.val(previousStatus);
                    alert(xhr.responseJSON?.message || 'Unable to update delivery status.');
                },
                complete: function() {
                    $select.removeClass('is-saving').prop('disabled', false);
                }
            });
        });

        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            let url = "{{ url('/api/dashboard-api') }}";

            if (selectedSubAdminId) {
                url += `?selectedSubAdminId=${selectedSubAdminId}`;
            }
            $.ajax({
                url: url,
                method: "GET",
                headers: {
                    "Authorization": "Bearer " + authToken,
                },
                dataType: "json",
                success: function(response) {
                    // console.log("Branch:", response.branch_id);
                    if (response.status) {
                        // console.log(response.data);

                        // Example: Update totals
                        $('#totalPurchase').text(parseFloat(response.data.totals.purchase).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $('#totalSales').text(parseFloat(response.data.totals.sales).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $('#totalExpense').text(parseFloat(response.data.totals.expense).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    }
                },
                error: function(xhr) {
                    // console.error(xhr.responseText);
                }
            });
        });
    </script>
@endpush
