@extends('layout.app')

@section('title', 'Purchase List')

@section('content')
    <style>
        #DataTables_Table_0_info {
            float: left;
        }

        .table-scroll-top {
            display: none;
        }

        .input-groupicon.me-2.dateFilterclass {
            width: 105px !important;
        }

        .form-control {
            color: #595b5d !important;
        }

        .form-group {
            margin-bottom: 8px !important;
        }

        /* Status Badge Styles */
        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 500;
            text-transform: capitalize;
            display: inline-block;
            color: white !important;
            min-width: 80px;
            text-align: center;
        }

        .status-pending {
            background-color: #ea5455 !important;
        }

        .status-completed {
            background-color: #28c76f !important;
        }

        .order-mobile-summary {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 2px;
        }

        .order-mobile-link {
            color: inherit;
            text-decoration: none;
        }

        .order-mobile-vendor {
            display: none;
            font-size: 12px;
            color: #595b5d;
            line-height: 1.3;
            word-break: break-word;
            text-transform: capitalize;
        }

        /* Vendor Name column - word wrap */
        .datanew td:nth-child(4) {
            white-space: normal !important;
            word-wrap: break-word;
            max-width: 200px;
            min-width: 120px;
        }

        .datanew th:nth-child(4) {
            white-space: normal;
            word-wrap: break-word;
        }

        /* Desktop view - show header buttons, hide filter row buttons */
        @media screen and (min-width: 768px) {
            .desktop-export-buttons {
                display: flex !important;
            }

            .mobile-export-buttons {
                display: none !important;
            }
        }

        /* Mobile view - hide header buttons, show filter row buttons */
        @media screen and (max-width: 767px) {
            .desktop-export-buttons {
                display: none !important;
            }

            .mobile-export-buttons {
                display: flex !important;
            }
        }

        /* Extra small devices (phones, less than 576px) */
        @media screen and (max-width: 575.98px) {
            .search-set {
                width: 100% !important;
                margin: 0 0 10px 0 !important;
            }

            .search-input {
                width: 100%;
                margin-top: 10px;
            }

            .input-groupicon {
                width: 100% !important;
                margin: 0 0 10px 0 !important;
            }

            .input-groupicon select,
            .input-groupicon input {
                width: 100% !important;
                font-size: 14px;
            }

            .dateFilterclass {
                width: 100% !important;
            }

            .table-responsive {
                display: block !important;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                width: 100% !important;
            }

            .datanew {
                width: 100% !important;
                table-layout: fixed;
            }

            .datanew th:nth-child(1),
            .datanew td:nth-child(1) {
                width: 70% !important;
                text-align: left;
            }

            .datanew th:nth-child(2),
            .datanew td:nth-child(2) {
                width: 30% !important;
                text-align: center;
                display: table-cell !important;
            }

            .mobile-order-card {
                display: none;
            }

            .order-mobile-vendor {
                display: block;
            }

            #filter_inputs {
                display: none;
            }

            .datanew {
                font-size: 11px;
            }

            .datanew th,
            .datanew td {
                padding: 6px 3px;
            }

            .datanew th:nth-child(3),
            .datanew td:nth-child(3),
            .datanew th:nth-child(4),
            .datanew td:nth-child(4),
            .datanew th:nth-child(5),
            .datanew td:nth-child(5),
            .datanew th:nth-child(6),
            .datanew td:nth-child(6),
            .datanew th:nth-child(7),
            .datanew td:nth-child(7),
            .datanew th:nth-child(8),
            .datanew td:nth-child(8),
            .datanew th:nth-child(9),
            .datanew td:nth-child(9) {
                display: none;
            }

            .datanew th:nth-child(2),
            .datanew td:nth-child(2) {
                text-align: center;
                display: table-cell !important;
            }

            .filter-row .col-md-2 {
                /* flex: 0 0 calc(50% - 5px);
                max-width: calc(50% - 5px); */
                padding: 0;
            }

            .filter-row .export-buttons-row {
                flex: 0 0 100%;
                max-width: 100%;
                margin-top: 5px;
            }

            .filter-row .export-buttons-row .d-flex {
                justify-content: space-between !important;
                gap: 8px;
            }

            .filter-row .export-buttons-row button {
                flex: 1 1 0;
                min-width: 0;
                font-size: 11px;
                padding: 6px 8px;
            }

            .filter-total-box {
                justify-content: center !important;
                width: 100% !important;
                margin: 0 0 10px 0 !important;
            }
        }

        @media only screen
        and (width: 375px)
        and (height: 667px) {
            .filter-row .select2-container--default .select2-selection--single {
                        
                            width: 154px !important;
            }

        }

        @media only screen
        and (width: 414px)
        and (height: 896px) {
            .filter-row .select2-container--default .select2-selection--single {
                        
                            width: 174px !important;
            }
        }

        @media only screen
and (width: 390px)
and (height: 844px) {
    .filter-row .select2-container--default .select2-selection--single {
                        
                            width: 162px !important;
            }
}



        /* Small devices (landscape phones, 576px and up) */
        @media screen and (min-width: 576px) and (max-width: 767.98px) {
            .search-set {
                width: 100% !important;
                margin: 0 0 10px 0 !important;
            }

            .input-groupicon {
                flex: 0 0 calc(50% - 5px);
                margin: 0 0 10px 0 !important;
            }

            .input-groupicon select,
            .input-groupicon input {
                width: 100% !important;
                font-size: 14px;
            }

            .dateFilterclass {
                width: 100% !important;
            }

            .table-responsive {
                display: block !important;
                overflow-x: auto;
                width: 100% !important;
            }

            .datanew {
                width: 100% !important;
                table-layout: fixed;
            }

            .order-mobile-vendor {
                display: block;
            }

            .datanew th:nth-child(1),
            .datanew td:nth-child(1) {
                width: 50% !important;
            }

            .datanew th:nth-child(2),
            .datanew td:nth-child(2) {
                width: 20% !important;
                text-align: center;
                display: table-cell !important;
            }

            .datanew th:nth-child(3),
            .datanew td:nth-child(3) {
                width: 30% !important;
            }

            .mobile-order-card {
                display: none;
            }

            #filter_inputs {
                display: none;
            }

            .datanew {
                font-size: 12px;
            }

            .datanew th,
            .datanew td {
                padding: 8px 4px;
            }

            .datanew th:nth-child(3),
            .datanew td:nth-child(3),
            .datanew th:nth-child(4),
            .datanew td:nth-child(4),
            .datanew th:nth-child(5),
            .datanew td:nth-child(5),
            .datanew th:nth-child(6),
            .datanew td:nth-child(6),
            .datanew th:nth-child(7),
            .datanew td:nth-child(7),
            .datanew th:nth-child(8),
            .datanew td:nth-child(8),
            .datanew th:nth-child(9),
            .datanew td:nth-child(9) {
                display: none;
            }

            .datanew th:nth-child(2),
            .datanew td:nth-child(2) {
                text-align: center;
                display: table-cell !important;
            }

            .filter-row .export-buttons-row {
                margin-top: 10px;
            }
        }

        /* Medium devices (tablets, 768px and up to 991px) */
        @media screen and (min-width: 768px) and (max-width: 991.98px) {
            .table-responsive {
                display: block !important;
                overflow-x: auto;
                width: 100% !important;
            }

            .mobile-order-card {
                display: none;
            }

            .datanew {
                font-size: 13px;
                width: 100% !important;
                table-layout: auto;
            }

            .datanew th,
            .datanew td {
                padding: 8px 6px;
            }

            .datanew th:nth-child(2),
            .datanew td:nth-child(2) {
                display: none !important;
            }

            .order-details-row {
                display: none !important;
            }
        }

        /* Large devices (desktops, 992px and up) */
        @media screen and (min-width: 992px) {
            .table-responsive {
                display: block !important;
                overflow-x: auto;
                width: 100% !important;
            }

            .mobile-order-card {
                display: none;
            }

            .datanew {
                font-size: 14px;
                width: 100% !important;
            }

            .datanew th,
            .datanew td {
                padding: 12px 10px;
            }

            .datanew th:nth-child(2),
            .datanew td:nth-child(2) {
                display: none !important;
            }

            .order-details-row {
                display: none !important;
            }
        }

        /* Expandable row details */
        .order-details-row {
            display: none;
        }

        .order-details-row.show {
            display: table-row;
        }

        .order-details-content {
            padding: 15px;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        .order-details-list {
            margin-bottom: 15px;
        }

        .order-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .order-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 14px;
        }

        .order-detail-value-simple {
            color: #1b2850;
            font-size: 14px;
            text-align: right;
        }

        .mobile-action-buttons-simple {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-start;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }

        .btn-icon-mobile,
        button.btn-icon-mobile {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #1b2850;
            background: transparent;
            transition: all 0.3s;
            border: 2px solid #1b2850;
            cursor: pointer;
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        button.btn-icon-mobile {
            border: 2px solid #1b2850;
            background: transparent;
        }

        .btn-icon-mobile:hover {
            background: #1b2850;
            color: white;
            transform: scale(1.1);
        }

        .btn-icon-mobile i {
            font-size: 16px;
        }

        .btn-icon-mobile.btn-history i {
            font-size: 18px;
        }

        .mobile-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            text-transform: capitalize;
        }

        .mobile-toggle-btn-table {
            background: #ff9f43;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .mobile-toggle-btn-table:hover {
            background: #ff8c2e;
        }

        .mobile-toggle-btn-table.minus {
            background: #dc3545;
        }

        .mobile-toggle-btn-table.minus:hover {
            background: #c82333;
        }

        @media screen and (min-width: 992px) {
            .order-details-content {
                padding: 20px;
            }

            .order-detail-label-simple,
            .order-detail-value-simple {
                font-size: 15px;
            }

            .btn-icon-mobile {
                width: 42px;
                height: 42px;
            }

            .btn-icon-mobile i {
                font-size: 17px;
            }
        }

        @media screen and (max-width: 768px) {
            .table-scroll-top {
                display: block;
            }
        }

        /* FILTER UI SAME SIZE DESIGN */
        .total-box {
            color: #1b2850;
            border: 1px solid #0d1b3e;
            border-radius: 5px;
            padding: 4px 8px;
            font-size: 14px;
            font-weight: bold;
            height: 32px;
            display: flex;
            align-items: center;
        }

        #filtered-total {
            color: #ff9f43;
            margin-left: 5px;
        }

        .filter-row input.form-control-sm,
        .filter-row select.form-control-sm {
            height: 32px !important;
            border-radius: 5px;
            font-size: 14px;
        }

        .filter-row .select2-container--default .select2-selection--single {
            height: 32px !important;
            border-radius: 5px !important;
            padding: 2px 8px;
            /* width: 154px; */
        }

        .filter-row .select2-selection__rendered {
            line-height: 28px !important;
        }

        .filter-row .select2-selection__arrow {
            height: 30px !important;
        }

        .filter-row .mb-1 {
            margin-bottom: 4px !important;
        }

        @media screen and (min-width: 768px) and (max-width: 1024px) {
            .content,
            .card,
            .card-body,
            .table-top {
                max-width: 100%;
                overflow-x: hidden;
            }

            .table-top .filter-row {
                display: flex;
                flex-wrap: wrap;
                width: auto !important;
                margin-left: 0;
                margin-right: 0;
            }

            .table-top .filter-row>div.col-md-2,
            .table-top .filter-row>div.col-md-2.col-12,
            .table-top .filter-row>div.col-md-2.col-6 {
                flex: 0 0 50% !important;
                max-width: 50% !important;
                width: 50% !important;
                padding-left: 5px;
                padding-right: 5px;
                margin-bottom: 8px;
            }

            .table-top .filter-row .search-set,
            .table-top .filter-row .search-input,
            .table-top .filter-row .mb-1,
            .table-top .filter-row .form-control,
            .table-top .filter-row select,
            .table-top .filter-row input,
            .table-top .filter-row .input-groupicon,
            .table-top .filter-row .dateFilterclass {
                width: 100% !important;
                max-width: 100% !important;
            }

            .table-top .filter-row #filter-date {
                min-width: 100% !important;
            }

            .table-top .filter-row .export-buttons-row {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                width: 100% !important;
                padding-left: 5px;
                padding-right: 5px;
            }

            .table-responsive {
                width: 100% !important;
                max-width: 100%;
                overflow-x: auto !important;
                overflow-y: hidden;
            }
        }

        /* MOBILE HEADER */
        @media (max-width: 767px) {
            .page-header {
                display: flex !important;
                flex-direction: row !important;
                /* justify-content: center !important; */
                align-items: center !important;
                flex-wrap: nowrap !important;
                gap: 20px;
                white-space: nowrap;
            }
            .page-header .page-title {
                width: auto !important;
                margin: 0 !important;
            }

            .page-header .page-title h4 {
                font-size: 16px;
                margin: 0;
                white-space: nowrap;
            }

            .page-header .header-actions {
                display: flex !important;
                flex-wrap: nowrap !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 6px;
            }

            .page-header .btn {
                padding: 5px 8px;
                font-size: 12px;
                white-space: nowrap;
            }

            .page-header .btn i,
            .page-header .btn img {
                margin-right: 3px;
            }

            .filter-row .col-6 {
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }

            .filter-row .export-buttons-row {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                padding: 0px;
            }

            #filter-date,
            .datetimepicker,
            input.datetimepicker {
                width: 99% !important;
                min-width: 92% !important;
                display: block !important;
                box-sizing: border-box !important;
            }

            #filter-date+span,
            .input-group,
            .input-groupicon {
                width: 100% !important;
                max-width: 100% !important;
            }
        }

        /* Search input styling */
        .search-input {
            position: relative;
            display: flex;
            align-items: center;
        }

        .btn-searchset {
            position: absolute;
            left: 10px;
            z-index: 10;
            padding: 0;
            top: 4px !important;
        }

        .search-input input {
            padding-left: 35px !important;
            border-radius: 5px;
        }

        /* Custom Pagination Styling */
        .pagination .page-item .page-link {
            background-color: #5d6d7e;
            color: #fff;
            border: none;
              margin: 0 3px;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: bold;
        }

        .pagination .page-item.active .page-link {
            background-color: #ff9f43 !important;
            color: #fff;
        }

        .pagination .page-item .page-link:hover {
            background-color: #4a5766;
            color: #fff;
        }

        .pagination .page-item.active .page-link:hover {
            background-color: #e68a35 !important;
        }

        /* Previous and Next buttons */
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            background-color: #fff;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }

        .pagination .page-item:first-child .page-link:hover,
        .pagination .page-item:last-child .page-link:hover {
            background-color: #f8f9fa;
            color: #495057;
            border-color: #dee2e6;
        }

        .pagination .page-item.disabled .page-link {
            background-color: #fff !important;
            color: #dee2e6 !important;
            border: 1px solid #dee2e6 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        .summary-badges-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 12px;
        }

        .summary-badge-box {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 34px;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid;
            font-size: 14px;
            font-weight: 600;
            background: #fff;
        }

        .summary-badge-box.pending {
            color: #ea5455;
            border-color: #f5c2c7;
            background: #fff5f5;
        }

        .summary-badge-box.paid {
            color: #28c76f;
            border-color: #b7ebcd;
            background: #effcf4;
        }

        .filter-total-box {
            justify-content: flex-start !important;
            min-width: 165px;
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
        }

        .filter-total-box span {
            display: inline-block;
            flex: 1 1 auto;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: bottom;
        }

        .purchase-action-dropdown {
            display: inline-flex;
            justify-content: center;
            width: 100%;
        }

        .purchase-action-dropdown .action-menu-toggle {
            align-items: center;
            background: #fff;
            border: 1px solid #d7dde8;
            border-radius: 4px;
            color: #1b2850;
            display: inline-flex;
            height: 28px;
            justify-content: center;
            padding: 0;
            width: 34px;
        }

        .purchase-action-dropdown .action-menu-toggle:hover,
        .purchase-action-dropdown .action-menu-toggle:focus {
            background: #f8fafc;
            border-color: #b8c2d2;
            color: #1b2850;
        }

        .purchase-action-menu {
            border: 1px solid #e8ebed;
            border-radius: 6px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
            max-width: 168px;
            min-width: 168px;
            padding: 6px;
            width: 168px;
            z-index: 999999999999;
        }

        .purchase-action-menu-floating {
            position: fixed !important;
            z-index: 999999999999 !important;
            bottom: auto !important;
            right: auto !important;
            height: auto !important;
            min-height: 0 !important;
            margin: 0 !important;
        }

        .purchase-action-menu .dropdown-item {
            align-items: center;
            border-radius: 4px;
            color: #344054;
            display: flex;
            font-size: 12px;
            gap: 8px;
            padding: 7px 8px;
            width: 100%;
        }

        .purchase-action-menu .dropdown-item i {
            color: #667085;
            font-size: 13px;
            text-align: center;
            width: 15px;
        }

        .purchase-action-menu .dropdown-item.text-danger,
        .purchase-action-menu .dropdown-item.text-danger i {
            color: #ea5455 !important;
        }

        .purchase-action-menu .dropdown-item:hover {
            background: #f4f6f8;
            color: #1b2850;
        }

        .datanew td:last-child {
            text-align: center;
            white-space: nowrap;
        }

        @media screen and (max-width: 575.98px) {
            .summary-badges-row {
                width: 100% !important;
                margin-top: 10px;
                /* padding: 0 8px; */
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .summary-badge-box {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
                justify-content: space-between;
            }
        }

        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        .download-loader-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            z-index: 1060;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .download-loader-overlay.d-none {
            display: none !important;
        }

        .download-loader-box {
            width: min(460px, 100%);
            background: #fff;
            border-radius: 8px;
            padding: 24px 20px;
            text-align: center;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
        }

        .download-loader-box h4 {
            margin: 0 0 18px 0;
            font-size: 34px;
            color: #2c3e50;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .download-loader-box h4 {
                font-size: 28px;
            }

            /* Mobile pagination adjustments */
            .pagination-controls {
                flex-direction: column !important;
                gap: 15px !important;
                align-items: center !important;
            }

            .pagination {
                display: block !important;
                list-style: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .pagination .page-item {
                display: inline-block !important;
                margin: 2px !important;
            }

            .pagination .page-item .page-link {
                /* padding: 8px 12px !important;
                font-size: 14px !important; */
                margin: 0 !important;
                display: inline-block !important;
            }

            .pagination .page-item:first-child .page-link,
            .pagination .page-item:last-child .page-link {
                /* padding: 10px 16px !important;
                font-size: 14px !important; */
                margin: 0 !important;
                display: inline-block !important;
            }

            .pagination .page-item:first-child .page-link:hover,
            .pagination .page-item:last-child .page-link:hover {
                background-color: #f8f9fa !important;
                color: #495057 !important;
            }

        /* Ensure Previous and Next buttons display properly */
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
                position: relative !important;
                z-index: 10 !important;
                font-weight: bold !important;
                min-width: 80px !important;
                text-align: center !important;
            }

        .pagination .page-item:not(:first-child):not(:last-child) .page-link {
                position: relative !important;
                z-index: 3 !important;
            }

        /* Ensure Previous and Next buttons are always visible */
        .pagination .page-item:first-child,
        .pagination .page-item:last-child {
                display: inline-block !important;
                visibility: visible !important;
            }
        }
    </style>
    @if (session('error'))
        <div class="alert alert-danger" id="error-message">
            {{ session('error') }}
        </div>

        <style>
            .fade-out {
                opacity: 1;
                transition: opacity 0.5s ease-out;
            }

            .fade-out.hidden {
                opacity: 0;
            }
        </style>

        <script>
            setTimeout(function() {
                let alert = document.getElementById('error-message');
                if (alert) {
                    alert.classList.add('hidden'); // Triggers the fade-out transition
                    // Remove the element from DOM after fadeout (optional)
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500); // match the CSS transition duration (0.5s)
                }
            }, 4000);
        </script>
    @endif
    <div class="content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div class="page-title">
                <h4>Purchases List</h4>
            </div>

            <div class="header-actions d-flex align-items-center gap-2">
                @if (app('hasPermission')(3, 'add'))
                    <a href="{{ route('purchase.import') }}" class="btn btn-sm btn-added">
                        <i class="fas fa-file-import me-1"></i> Import
                    </a>
                    <a href="{{ route('purchase.add') }}" class="btn btn-sm btn-added">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img">
                        New Purchases
                    </a>
                @endif
                {{-- <button id="exportAllChallan" class="btn btn-sm btn-success desk-res">
                    <i class="fas fa-file-excel"></i> Excel
                </button>

                <button id="exportPdf" class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> PDF
                </button> --}}
                <!-- Desktop Export Buttons (visible only on desktop) -->
                <div class="desktop-export-buttons d-flex gap-2">
                    <button id="exportAllChallanDesktop" class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                    <button id="exportPdfDesktop" class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                </div>
            </div>

        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-top mb-3">
                    <div class="row w-100 align-items-center filter-row">
                        <!-- Search -->
                        <div class="col-md-2 col-12 mb-1 mb-md-0 filter-field filter-search">
                            <div class="search-set w-100">
                                <div class="search-path"></div>
                                <div class="search-input d-flex align-items-center">
                                    <a class="btn btn-searchset">
                                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                            alt="img">
                                    </a>
                                    <input type="text" id="search-input" class="form-control" placeholder="Search..."
                                        style="height: 30px">
                                </div>
                            </div>
                        </div>

                        <!-- Total Filter -->
                        <div class="col-md-2 col-12 filter-field">
                            @if (in_array(auth()->user()->role, ['admin', 'sub-admin']))
                                <div class="mb-1 d-flex align-items-center filter-total-box"
                                    style="color: #1b2850; border: 1px solid #ced4da; border-radius: 4px; padding: 0 8px; font-size: 14px; font-weight: bold; height: 31px; background: #fff;">
                                    Total: <span style="color: #ff9f43" class="ms-1" id="filtered-total">₹0.00</span>
                                </div>
                            @endif
                        </div>


                        <div class="col-md-2 col-6 filter-field">
                            <div class="mb-1 custom-select2">
                                <!-- <label for="filter-month" class="form-label">Month</label> -->
                                <select id="filter-month" class="form-control form-control-sm ">
                                    <option value="all">All Months</option>
                                    <option value="01">January</option>
                                    <option value="02">February</option>
                                    <option value="03">March</option>
                                    <option value="04">April</option>
                                    <option value="05">May</option>
                                    <option value="06">June</option>
                                    <option value="07">July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2 col-6 filter-field">
                            <div class="mb-1 custom-select2">
                                <!-- <label for="filter-year" class="form-label">Year</label> -->
                                <select id="filter-year" class="form-control form-control-sm">
                                    <option value="all">All Year</option>
                                </select>
                            </div>
                        </div>

                        <!-- Financial Year Filter -->
                        <div class="col-md-2 col-6 filter-field {{ ($financialYearEnabled ?? true) ? '' : 'd-none' }}"
                            id="purchase-financial-year-filter">
                            <div class="mb-1">
                                <select id="filter-financial-year" class="form-control form-control-sm">
                                    <option value="all">All Financial Years</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2 col-6 filter-field">
                            <div class="mb-1" style="width: 100%;">
                                <!-- <div class="form-group mb-0"> -->
                                <!-- <label for="filter-date" class="form-label">Date</label> -->
                                <input type="text" id="filter-date" placeholder="Date"
                                    class="datetimepicker form-control form-control-sm w-100">
                            </div>
                        </div>
                        <div class="col-md-2 col-6 filter-field">
                            <div class="mb-1 custom-select2">
                                <!-- <div class="form-group mb-0"> -->
                                <!-- <label for="filter-date" class="form-label">Date</label> -->
                                <select id="filter-customer" class="form-control form-control-sm">
                                    <option value="">-- Select Vendor --</option>
                                </select>
                            </div>
                        </div>
                        <!-- Mobile Export Buttons Row (visible only on mobile) -->
                        <div class="col-12 export-buttons-row mobile-export-buttons">
                            <div class="d-flex justify-content-center align-items-center w-100">
                                <button id="exportAllChallanMobile" class="btn btn-sm btn-success flex-grow-1">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                                <button id="exportPdfMobile" class="btn btn-sm btn-danger flex-grow-1">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                @if (in_array(auth()->user()->role, ['admin', 'sub-admin']))
                    <div class="summary-badges-row">
                        <div class="summary-badge-box pending">
                            <span>Total Pending:</span>
                            <span id="filtered-pending-total">₹0.00</span>
                        </div>
                        <div class="summary-badge-box paid">
                            <span>Total Paid:</span>
                            <span id="filtered-paid-total">₹0.00</span>
                        </div>
                    </div>
                @endif


                <!-- Filter Inputs Card -->
                <div class="card" id="filter_inputs">
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <input type="text" placeholder="Enter Name" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <input type="text" placeholder="Enter Reference No" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <select class="form-select">
                                        <option>Completed</option>
                                        <option>Paid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group d-flex justify-content-end">
                                    <a class="btn btn-filters ms-auto">
                                        <img src="admin/assets/img/icons/search-whites.svg" alt="img">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                {{-- <div class="table-scroll-top"
                    style="overflow-x: auto; overflow-y: hidden; height: 20px; margin-bottom: 5px;">
                    <div style="height: 1px;"></div> <!-- Adjust width to match your table width -->
                </div> --}}

                <div class="table-responsive mt-3" style="overflow-x: auto;">
                    <table class="table datanew" id="order-table" style="max-width: 2000px;">
                        <thead>
                            <tr>
                                <th>Bill Number</th>
                                <th class="text-center">Details</th>
                                <th>Date</th>
                                <th>Vendor Name</th>
                                <th>Grand Total</th>
                                {{-- <th>Purchase Status</th> --}}
                                <th>Payment Status</th>
                                {{-- <th>Return Status</th> --}}
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- JS will populate this -->
                        </tbody>

                    </table>
                </div>

                <!-- Pagination Controls -->
                <div
                    class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                        <select id="per-page-select" class="form-select form-select-sm"
                            style="width: auto; border: 1px solid #ddd;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="ms-3" style="font-size: 14px; color: #555;">
                            <span id="pagination-from">0</span> - <span id="pagination-to">0</span> of <span
                                id="pagination-total">0</span> items
                        </span>
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0" id="pagination-numbers">
                            <!-- Page numbers will be populated by JS -->
                        </ul>
                    </nav>
                </div>

                <!-- Mobile Order Cards -->
                <div class="mobile-order-card mt-3" id="mobile-order-container">
                    <!-- JS will populate this -->
                </div>


            </div>
        </div>

    </div>
    <form id="makePaymentForm">
        <div class="modal fade" id="makePaymentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">

                        <div class="text-end mb-3">
                            <button type="button" class="btn btn-sm btn-cancel text-white" id="viewHistoryBtn">View
                                Payment History</button>
                        </div>

                        <!-- ✅ Payment History Container -->
                        <div id="paymentHistoryBox" class="border p-2 rounded bg-white d-none mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Payment History</h6>

                                <!-- 👇 Visible Close Button -->
                                <button type="button" class="btn btn-sm btn-danger" id="closeHistoryBtn">
                                    &times; <!-- This renders an “×” symbol -->
                                </button>
                            </div>

                            <ul id="paymentHistoryList" class="list-unstyled mb-0"
                                style="max-height: 200px; overflow-y: auto;">
                                <!-- Populated via JavaScript -->
                            </ul>
                        </div>



                        <div class="border p-2 rounded bg-light">
                            <strong>Total Amount:</strong> ₹<span id="emiTotal"></span><br>
                            <strong>Remaining Amount:</strong> ₹<span id="remainingAmountDisplay">0.00</span><br>
                            <strong>Return Amount:</strong> ₹<span id="returnAmountDisplay">0.00</span>
                        </div>

                        <!-- ✅ View Payment History Button -->



                        <br>

                        <!-- Payment Method -->
                        <div class="mb-3" id="paymentMethodDiv">
                            <label for="paymentMethodSelect" class="form-label">Select Payment Method</label>
                            <select class="form-select" id="paymentMethodSelect" name="payment_method">
                                <option value="" selected disabled>Select</option>
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                                <option value="cash_online">Cash + Online</option>

                            </select>
                            <div class="text-danger" id="paymentMethodError"></div>
                        </div>

                        <div class="mb-3 d-none" id="cashOnlineTypeDiv">
                            <label for="cashOnlineTypeSelect" class="form-label">Select Cash + Online Type</label>
                            <select class="form-select" id="cashOnlineTypeSelect" name="cash_online_type">
                                <option value="" selected disabled>Select</option>
                                <option value="cash_online_fully">Cash + Online Fully</option>
                                <option value="cash_online_partially">Cash + Online Partially</option>
                            </select>
                            <div class="text-danger" id="cashOnlineTypeError"></div>
                        </div>

                        <!-- Fully Cash + Online -->
                        <div class="mb-3 d-none" id="fullyCashOnlineFields">
                            <label>Cash Amount</label>
                            <input type="text" id="cashOnlineFullAmount" name="fully_cash_amount"
                                class="form-control">
                            <div class="text-danger" id="cashOnlineFullAmountError"></div>
                            <label class="mt-2">Online Amount</label>
                            <input type="text" id="upiOnlineFullAmount" name="full_online_amount"
                                class="form-control" readonly>
                            <div class="text-danger" id="upiOnlineFullAmountError"></div>
                        </div>

                        <!-- Partial Cash + Online -->
                        <div class="mb-3 d-none" id="partialCashOnlineFields">
                            <label>Cash Amount</label>
                            <input type="text" id="cashOnlinePartialAmount" name="cash_amount" class="form-control">
                            <div class="text-danger" id="cashOnlinePartialAmountError"></div>
                            <label class="mt-2">Online Amount</label>
                            <input type="text" id="upiOnlinePartialAmount" name="online_amount" class="form-control">
                            <div class="text-danger" id="upiOnlinePartialAmountError"></div>
                            <label class="mt-2">Remaining Amount</label>
                            <input type="text" id="remainingCashOnlineAmount" name="remaining_amount"
                                class="form-control" readonly>
                        </div>







                        <div class="mb-3 d-none" id="onlineTypeDiv">
                            <label for="onlineTypeSelect" class="form-label">Select Online Type</label>
                            <select class="form-select" id="onlineTypeSelect" name="online_type">
                                <option value="" selected disabled>Select</option>
                                <option value="online_fully">Online Fully</option>
                                <option value="online_partially">Online Partially</option>
                            </select>
                            <div class="text-danger" id="onlineTypeError"></div>
                        </div>

                        <!-- Paid Type Dropdown (Hidden by default) -->
                        <div class="mb-3 d-none" id="paidTypeDiv">
                            <label for="paidTypeSelect" class="form-label">Paid Type</label>
                            <select class="form-select" id="paidTypeSelect" name="paid_type">
                                <option value="" selected disabled>Select</option>
                                <option value="cash_partially">Cash Partially</option>
                                <option value="cash_fully">Cash Fully</option>
                            </select>
                            <div class="text-danger" id="paidTypeError"></div>
                        </div>

                        <!-- UPI Amount Input -->
                        <div class="mb-3 d-none" id="upiAmountDiv">
                            <label for="upiAmountInput" class="form-label">Online Amount</label>
                            <input type="text" class="form-control" id="upiAmountInput" name="upi_online_amount"
                                readonly>
                            <div class="text-danger" id="upiAmountError"></div>
                        </div>



                        <!-- Partially Paid Fields -->
                        <div class="mb-3 d-none" id="partialPaidFields">
                            <label for="partialAmount" class="form-label">Enter Amount</label>
                            <input type="text" class="form-control mb-2" id="partialAmount" name="amount">
                            <div class="text-danger" id="partialAmountError"></div>

                            <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                                <div style="flex: 1;">
                                    <label for="pendingAmount" class="form-label">Pending Amount</label>
                                    <input type="text" class="form-control" id="pendingAmount"
                                        name="cash_pending_amount" readonly>
                                </div>

                            </div>
                        </div>

                        <div class="mb-3 d-none" id="bank_container">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="bank_id" class="form-label mb-0">Select Bank</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="openAddBankModal">
                                    Add Bank
                                </button>
                            </div>
                            <select name="bank_id" id="bank_id" class="form-select">
                                <option value="">Select Bank</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger" id="bankError"></div>
                        </div>

                        <!-- Fully Paid Fields -->
                        <div class="mb-3 d-none" id="fullyPaidFields">
                            <label class="form-label">Cash Amount</label>
                            <input type="text" class="form-control" id="cashAmount" name="cashAmount">
                            <div class="text-danger" id="cashAmountError"></div>
                        </div>

                        <!-- Remarks -->
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="2"
                                placeholder="Enter remarks..."></textarea>
                            <div class="text-danger" id="remarksError"></div>
                        </div>

                        <!-- Cleaned Hidden Inputs (no duplicate name attributes) -->
                        <input type="hidden" id="paymentJobCardId" name="purchase_id">
                        <input type="hidden" id="remainingAmountHidden" name="remaining_amount">
                        <input type="hidden" id="paymentMethodHidden" name="payment_type">

                        <div class="text-end">
                            <button type="submit" class="btn btn-submit text-white"
                                style="background-color: #ff9f43;">Submit Payment</button>
                            <button type="button" class="btn btn-secondary btn-cancel"
                                data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Global Payment History Modal -->
    <div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-labelledby="paymentHistoryLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentHistoryLabel">Payment History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <ul id="globalPaymentHistoryList" class="list-group"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addBankModal" tabindex="-1" aria-labelledby="addBankModalLabel" aria-hidden="true">
        <div class="modal-dialog">
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
                                <input type="number" class="form-control" id="add_opening_balance" name="opening_balance"
                                    min="0" step="0.01" value="0">
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
                        <button type="submit" class="btn btn-submit text-white" id="saveBankBtn"
                            style="background-color: #ff9f43;">Save Bank</button>
                        <button type="button" class="btn btn-secondary btn-cancel"
                            data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="downloadLoaderOverlay" class="download-loader-overlay d-none" aria-live="polite" aria-busy="true">
        <div class="download-loader-box">
            <h4 id="downloadLoaderText">Generating PDF...</h4>
            <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // Global variables
        var purchaseTable;
        let currentPage = 1;
        let lastPage = 1;
        let perPage = 10;
        let searchQuery = '';
        let isLoading = false;
        let currentRequest = null;
        let debounceTimer = null;

        function formatCurrency(amount) {
            return parseFloat(amount || 0).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function parseCurrency(value) {

            if (!value) return 0;

            // remove ₹ symbol and commas
            value = value.toString().replace(/[₹,\s]/g, '');

            let number = parseFloat(value);

            return isNaN(number) ? 0 : number;
        }


        // Helper function to build expandable row content
        function buildPurchaseExpandableRowContent(purchase, currencySymbol, currencyPosition) {
            const amount = formatCurrency(purchase.grand_total || 0);
            const displayAmount = currencyPosition === 'right' ?
                amount + currencySymbol : currencySymbol + amount;

            let actionBtns = '';

            // Make Payment button (orange button) - only if there's remaining amount
            if (parseFloat(purchase.remaining_amount || 0) > 0) {
                actionBtns += `<button type="button" class="btn btn-sm btn-primary me-3 make-payment-btn"
                    data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                    data-id="${purchase.id}"
                    data-amount="${purchase.remaining_amount}"
                    data-method="${purchase.payment_mode || ''}"
                    data-total-amount="${purchase.grand_total || 0}"
                    data-remaining-amount="${purchase.remaining_amount}"
                    style="background-color: #ff9f43; border-color: #ff9f43; color: white;">
                    Make Payment
                </button>`;
            }

            // Edit icon button
            @if (app('hasPermission')(3, 'edit'))
                if (parseFloat(purchase.total_return || 0) === 0) {
                    actionBtns += `<a class="btn-icon-mobile btn-edit" href="/edit-purchase/${purchase.id}" title="Edit Purchase">
                        <i class="fas fa-edit"></i>
                    </a>`;
                }
            @endif

            // History icon button
            actionBtns += `<button class="btn-icon-mobile btn-history open-history" data-id="${purchase.id}" title="Payment History">
                <i class="fas fa-history"></i>
            </button>`;

            // View icon button
            @if (app('hasPermission')(3, 'view'))
                actionBtns += `<a class="btn-icon-mobile btn-view" href="/print-purchase/${purchase.id}" title="View Purchase">
                    <i class="fas fa-eye"></i>
                </a>`;
            @endif

            // Print icon button
            @if (app('hasPermission')(3, 'view'))
                actionBtns += `<a class="btn-icon-mobile btn-print" href="javascript:void(0);" onclick="window.open('/purchase/invoice/pdf/' + ${purchase.id});" title="Print Invoice">
                    <i class="fas fa-print"></i>
                </a>`;
            @endif

            // Delete icon button
            @if (app('hasPermission')(3, 'delete'))
                actionBtns += `<a class="btn-icon-mobile btn-delete delete-order" href="javascript:void(0);" data-id="${purchase.id}" title="Delete">
                    <i class="fas fa-trash"></i>
                </a>`;
            @endif

            return `
                <td colspan="8" class="order-details-content">
                    <div class="order-details-list">
                        <div class="order-detail-row-simple">
                            <span class="order-detail-label-simple">Date:</span>
                            <span class="order-detail-value-simple">${purchase.purchase_date || purchase.purxhase_date || purchase.created_at || 'N/A'}</span>
                        </div>
                        <div class="order-detail-row-simple">
                            <span class="order-detail-label-simple">Vendor Name:</span>
                            <span class="order-detail-value-simple">${purchase.vendor_name || 'N/A'}</span>
                        </div>

                        <div class="order-detail-row-simple">
                            <span class="order-detail-label-simple">Payment Status:</span>
                            <span class="order-detail-value-simple">
                                ${parseFloat(purchase.extra_paid || 0) > 0 ?
                                    `<span class="mobile-badge bg-lightred">Extra Paid: ${currencySymbol}${formatCurrency(purchase.extra_paid)}</span>` :
                                    `<span class="mobile-badge bg-lightgreen">${purchase.payment_status || 'N/A'}</span>`}
                            </span>
                        </div>

                        <div class="order-detail-row-simple">
                            <span class="order-detail-label-simple">Grand Total:</span>
                            <span class="order-detail-value-simple" style="font-weight: bold; color: #ff9f43;">${displayAmount}</span>
                        </div>

                        ${parseFloat(purchase.remaining_amount || 0) > 0 ? `
                                                            <div class="order-detail-row-simple">
                                                                <span class="order-detail-label-simple">Remaining:</span>
                                                                <span class="order-detail-value-simple" style="color: #dc3545; font-weight: bold;">
                                                                    ${currencySymbol}${formatCurrency(purchase.remaining_amount || 0)}
                                                                </span>
                                                            </div>
                                                            ` : ''}
                        ${parseFloat(purchase.extra_paid || 0) > 0 ? `
                                                            <div class="order-detail-row-simple">
                                                                <span class="order-detail-label-simple">Extra Paid:</span>
                                                                <span class="order-detail-value-simple" style="color: #28c76f; font-weight: bold;">
                                                                    ${currencySymbol}${parseFloat(purchase.extra_paid || 0).toFixed(2)}
                                                                </span>
                                                            </div>
                                                            ` : ''}
                    </div>
                    <div class="mobile-action-buttons-simple">
                        ${actionBtns}
                    </div>
                </td>
            `;
        }

        // Toggle function for table rows - must be global
        window.togglePurchaseRowDetails = function(purchaseId) {
            // Find the button that was clicked
            const btn = $(`.mobile-toggle-btn-table[data-purchase-id="${purchaseId}"]`);
            if (btn.length === 0) {
                // console.error('Toggle button not found for purchase:', purchaseId);
                return;
            }

            const row = btn.closest('tr');
            let detailsRow = row.next(`tr.order-details-row[data-purchase-id="${purchaseId}"]`);
            const icon = btn.find('.toggle-icon');

            // If expandable row doesn't exist, create it
            if (detailsRow.length === 0) {
                const purchaseData = window.purchaseDataMap && window.purchaseDataMap[purchaseId];
                if (purchaseData) {
                    detailsRow = $('<tr>')
                        .addClass('order-details-row')
                        .attr('data-purchase-id', purchaseId)
                        .html(buildPurchaseExpandableRowContent(purchaseData, purchaseData.currencySymbol, purchaseData
                            .currencyPosition));
                    row.after(detailsRow);
                } else {
                    // console.error('Purchase data not found for purchase:', purchaseId);
                    return;
                }
            }

            if (detailsRow.hasClass('show')) {
                detailsRow.removeClass('show');
                btn.removeClass('minus');
                icon.text('+');
            } else {
                detailsRow.addClass('show');
                btn.addClass('minus');
                icon.text('−');
            }
        }

        // Function to add expandable rows - must be global
        window.addPurchaseExpandableRows = function(dt) {
            if (!dt) return;

            const currentWidth = $(window).width();
            const isMobileOrTablet = currentWidth <= 1024;

            if (!isMobileOrTablet) {
                // Remove expandable rows on desktop
                $('tr.order-details-row').remove();
                return;
            }

            dt.rows().every(function() {
                const row = this.node();
                const toggleBtn = $(row).find('.mobile-toggle-btn-table');
                if (toggleBtn.length > 0) {
                    const purchaseId = toggleBtn.data('purchase-id');
                    const purchaseData = window.purchaseDataMap && window.purchaseDataMap[purchaseId];
                    if (purchaseData && !$(row).next('tr.order-details-row[data-purchase-id="' + purchaseId +
                            '"]').length) {
                        const expandableRow = $('<tr>')
                            .addClass('order-details-row')
                            .attr('data-purchase-id', purchaseId)
                            .html(buildPurchaseExpandableRowContent(purchaseData, purchaseData.currencySymbol,
                                purchaseData.currencyPosition));
                        $(row).after(expandableRow);
                    }
                }
            });
        };

        // Function to calculate total for visible rows - must be global
        function calculatePurchaseFilteredTotal() {
            if (window.purchaseSummaryTotals) {
                updatePurchaseSummaryTotals(
                    window.purchaseSummaryTotals.total_amount || 0,
                    window.purchaseSummaryTotals.total_pending_amount || 0,
                    window.purchaseSummaryTotals.total_paid_amount || 0,
                    window.purchaseSummaryTotals.currency_symbol || '₹',
                    window.purchaseSummaryTotals.currency_position || 'left'
                );
                return;
            }

            if (!purchaseTable) {
                purchaseTable = $('.datanew').DataTable();
            }

            let total = 0;

            // Find the Grand Total column index by header name
            let totalColumnIndex = -1;
            purchaseTable.columns().every(function() {
                const header = $(this.header());
                if (header.text().trim() === 'Grand Total') {
                    totalColumnIndex = this.index();
                    return false; // break
                }
            });

            // If column not found by name, use index 4 as fallback
            if (totalColumnIndex === -1) {
                totalColumnIndex = 4;
            }

            purchaseTable.rows({
                filter: 'applied'
            }).every(function() {
                const row = this.data();
                if (row[totalColumnIndex]) {
                    const amountText = row[totalColumnIndex];
                    const rawAmount = parseFloat(amountText.replace(/[^0-9.-]+/g, '')) || 0;
                    total += rawAmount;
                }
            });

            const currencySymbol = "₹";
            $('#filtered-total').text(`${currencySymbol}${formatCurrency(total.toFixed(2))}`);
        }

        function formatPurchaseSummaryAmount(amount, currencySymbol, currencyPosition) {
            const numericAmount = parseFloat(amount || 0);
            const formattedAmount = formatCurrency(numericAmount.toFixed(2));

            return currencyPosition === 'right' ?
                `${formattedAmount}${currencySymbol}` :
                `${currencySymbol}${formattedAmount}`;
        }

        function updatePurchaseSummaryTotals(totalAmount, totalPendingAmount, totalPaidAmount, currencySymbol,
            currencyPosition) {
            $('#filtered-total').text(formatPurchaseSummaryAmount(totalAmount, currencySymbol,
                currencyPosition));
            $('#filtered-pending-total').text(formatPurchaseSummaryAmount(totalPendingAmount,
                currencySymbol, currencyPosition));
            $('#filtered-paid-total').text(formatPurchaseSummaryAmount(totalPaidAmount, currencySymbol,
                currencyPosition));
        }

        const purchaseCalendarYears = @json($years ?? []);

        function buildFinancialYearOptions(yearValues) {
            const fyStartYears = new Set();
            (Array.isArray(yearValues) ? yearValues : []).forEach((yearVal) => {
                const year = parseInt(yearVal, 10);
                if (!Number.isNaN(year)) {
                    fyStartYears.add(year - 1);
                    fyStartYears.add(year);
                }
            });

            const currentYear = new Date().getFullYear();
            fyStartYears.add(currentYear - 1);
            fyStartYears.add(currentYear);

            return Array.from(fyStartYears)
                .sort((a, b) => b - a)
                .map((startYear) => `${startYear}-${startYear + 1}`);
        }

        function populatePurchaseFinancialYears() {
            const options = buildFinancialYearOptions(purchaseCalendarYears);
            const $financialYear = $("#filter-financial-year");
            $financialYear.empty().append('<option value="all">All Financial Years</option>');
            options.forEach((financialYear) => {
                $financialYear.append(`<option value="${financialYear}">${financialYear}</option>`);
            });
        }

        $(document).on('focus',
            '#partialAmount, #cashAmount, #cashOnlineFullAmount, #cashOnlinePartialAmount, #upiOnlinePartialAmount',
            function() {
                let val = parseCurrency($(this).val());
                $(this).val(val > 0 ? val : '');
            });

        // $(document).on('blur',
        //     '#partialAmount, #cashAmount, #cashOnlineFullAmount, #cashOnlinePartialAmount, #upiOnlinePartialAmount',
        //     function() {
        //         let val = parseCurrency($(this).val());
        //         $(this).val(formatCurrency(val));
        //     });

        $(document).ready(function() {
            // Initialize table after data is loaded
           const yearsToRender = Array.isArray(purchaseCalendarYears) && purchaseCalendarYears.length ?
                purchaseCalendarYears :
                [new Date().getFullYear(), new Date().getFullYear() - 1, new Date().getFullYear() - 2, new Date()
                    .getFullYear() - 3
                ];
            yearsToRender.forEach((yearVal) => {
                const year = parseInt(yearVal, 10);
                if (!Number.isNaN(year)) {
                    $("#filter-year").append(`<option value="${year}">${year}</option>`);
                }
            });
            populatePurchaseFinancialYears();
            let isFinancialYearEnabled = false;

            function togglePurchaseFinancialYearFilter(enabled) {
                isFinancialYearEnabled = Boolean(Number(enabled));
                const $financialYearWrapper = $("#purchase-financial-year-filter");
                if (isFinancialYearEnabled) {
                    $financialYearWrapper.removeClass('d-none');
                } else {
                    $financialYearWrapper.addClass('d-none');
                    $("#filter-financial-year").val('all');
                }
            }

            $("#filter-month, #filter-year, #filter-financial-year").select2({
                minimumResultsForSearch: -1,
                width: '100%'
            });

            // Vendor filter: search by name OR phone, show "Name - Phone" in list, show only name when selected
            $("#filter-customer").select2({
                width: '100%',
                placeholder: '-- Select Vendor --',
                allowClear: true,
                matcher: function(params, data) {
                    if (!params.term || params.term.trim() === '') return data;
                    var term = params.term.trim().toLowerCase();
                    var text = (data.text || '').toLowerCase();
                    var phone = $(data.element).data('phone') ? String($(data.element).data('phone')).toLowerCase() : '';
                    if (text.indexOf(term) > -1 || phone.indexOf(term) > -1) return data;
                    return null;
                },
                templateResult: function(data) {
                    return data.text; // show "Name - Phone" in dropdown list
                },
                templateSelection: function(data) {
                    if (!data.id) return data.text;
                    // Show only vendor name (strip " - phone") in the selection box
                    var text = data.text || '';
                    var dashIndex = text.lastIndexOf(' - ');
                    return dashIndex > -1 ? text.substring(0, dashIndex) : text;
                }
            });

            // On dropdown change → filter table
            $("#filter-customer").on("change", function() {
                let selectedCustomer = $(this).val();
                if (purchaseTable) {
                    if (selectedCustomer) {
                        purchaseTable.column(3) // column index for vendor name (after Details column)
                            .search("^" + selectedCustomer + "$", true, false) // exact match
                            .draw();
                    } else {
                        purchaseTable.column(3).search("").draw(); // clear filter
                    }
                    setTimeout(() => calculatePurchaseFilteredTotal(), 100);
                }
            });


            // Month short names
            const monthNames = {
                "01": "Jan",
                "02": "Feb",
                "03": "Mar",
                "04": "Apr",
                "05": "May",
                "06": "Jun",
                "07": "Jul",
                "08": "Aug",
                "09": "Sep",
                "10": "Oct",
                "11": "Nov",
                "12": "Dec"
            };

            // Apply month/year filter whenever dropdown changes
            $("#filter-month, #filter-year").on("change", function() {
                applyMonthYearFilter();
                $(this).trigger("change.select2");
            });

            // function applyMonthYearFilter() {
            //     const selectedMonth = $("#filter-month").val();
            //     const selectedYear = $("#filter-year").val();

            //     let regex = "";
            //     if (selectedMonth && selectedYear) {
            //         regex = `${monthNames[selectedMonth]}.*${selectedYear}`;
            //     } else if (selectedMonth) {
            //         regex = `${monthNames[selectedMonth]}`;
            //     } else if (selectedYear) {
            //         regex = `${selectedYear}`;
            //     }

            //     console.log("Applied Filter Regex:", regex);

            //     // Use the DataTable instance
            //     if (purchaseTable) {
            //         purchaseTable.column(2).search(regex, true, false).draw(); // Date column is now index 2
            //         setTimeout(() => calculatePurchaseFilteredTotal(), 100);
            //     }
            // }
            function applyMonthYearFilter() {

                const selectedMonth = $("#filter-month").val();
                const selectedYear = $("#filter-year").val();

                let regex = "";

                // If both are ALL → show all
                if (selectedMonth === "all" && selectedYear === "all") {
                    regex = "";
                }

                // Only year selected
                else if (selectedMonth === "all" && selectedYear !== "all") {
                    regex = selectedYear;
                }

                // Only month selected
                else if (selectedMonth !== "all" && selectedYear === "all") {
                    regex = `${monthNames[selectedMonth]}`;
                }

                // Both specific
                else if (selectedMonth && selectedYear) {
                    regex = `${monthNames[selectedMonth]}.*${selectedYear}`;
                }

                if (purchaseTable) {
                    purchaseTable.column(2).search(regex, true, false).draw();
                    setTimeout(() => calculatePurchaseFilteredTotal(), 100);
                }
            }

            // alert("Purchase List Page Loaded");
            const authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            const addBankModalElement = document.getElementById('addBankModal');
            const addBankModal = addBankModalElement ? new bootstrap.Modal(addBankModalElement) : null;
            // console.log("Selected Sub Admin ID:", selectedSubAdminId);

            function formatDate(dateString) {
                let date = new Date(dateString.replace(' ', 'T')); // fix parsing issue

                let day = String(date.getDate()).padStart(2, '0');
                let month = String(date.getMonth() + 1).padStart(2, '0');
                let year = date.getFullYear();

                return `${day}-${month}-${year}`;
            }

            function resetAddBankForm() {
                $('#addBankForm')[0].reset();
                $('#add_opening_balance').val('0');
                $('#add_bank_status').val('1');
                $('#addBankForm .text-danger').text('');
            }

            function upsertBankOption(bank) {
                if (!bank || !bank.id) {
                    return;
                }

                const bankId = String(bank.id);
                const bankName = bank.bank_name || 'Unnamed Bank';
                const existingOption = $('#bank_id option[value="' + bankId + '"]');

                if (existingOption.length) {
                    existingOption.text(bankName);
                } else {
                    $('#bank_id').append(new Option(bankName, bankId));
                }

                $('#bank_id').val(bankId).trigger('change');
            }


            $(document).on('click', '.make-payment-btn', function() {
                let jobCardId = $(this).data('id');
                let totalAmount = $(this).data('total-amount');
                let remainingAmount = $(this).data('remaining-amount');
                let returnAmount = $(this).data('return-amount') || 0;
                let method = $(this).data('method') || '';

                // ✅ Fill modal hidden inputs + text spans
                $('#paymentJobCardId').val(jobCardId);
                $('#emiTotal').text(parseFloat(totalAmount).toFixed(2));
                $('#remainingAmountDisplay').text(parseFloat(remainingAmount).toFixed(2));
                $('#returnAmountDisplay').text(parseFloat(returnAmount).toFixed(2));
                $('#remainingAmountHidden').val(remainingAmount);
                $('#paymentMethodHidden').val(method);

                // ✅ Reset payment method dropdown to default
                $('#paymentMethodSelect').val('');

                // ✅ Hide history box initially
                $('#paymentHistoryBox').addClass('d-none');
                $('#paymentHistoryList').html('');

                // ✅ Bind View History button
                $('#viewHistoryBtn').off('click').on('click', function() {
                    $.ajax({
                        url: '/api/purchase/payment-history/' + jobCardId,
                        method: 'GET',
                        headers: {
                            "Authorization": "Bearer " + authToken
                        },
                        success: function(response) {
                            const history = response.data;

                            if (!history || history.length === 0) {
                                $('#paymentHistoryList').html(
                                    '<li>No payment history found.</li>');
                            } else {
                                let historyHtml = '';
                                history.forEach(function(payment) {
                                    historyHtml += `
                                                                            <li class="mb-2">
                                                                                <strong>Amount:</strong> ₹${formatCurrency(payment.payment_amount)}<br>
                                                                                <strong>Date:</strong> ${formatDate(payment.payment_date)}<br>
                                                                                <strong>Method:</strong> ${payment.payment_method.charAt(0).toUpperCase() + payment.payment_method.slice(1)}<br>
                                                                                <strong>Payment Type:</strong> ${payment.payment_type ? payment.payment_type.charAt(0).toUpperCase() + payment.payment_type.slice(1) : 'N/A'}<br>
                                                                                <strong>Remark:</strong> ${payment.remarks || 'N/A'}<br>
                                                                                ${payment.payment_type === 'emi' ? `<strong>EMI Months:</strong> ${payment.emi_month || 0}<br>` : ''}
                                                                            </li>
                                                                            <hr class="my-1"/>
                                                                        `;
                                });
                                $('#paymentHistoryList').html(historyHtml);
                            }

                            $('#paymentHistoryBox').removeClass('d-none');
                        },
                        error: function() {
                            $('#paymentHistoryList').html(
                                '<li class="text-danger">Failed to load payment history.</li>'
                            );
                            $('#paymentHistoryBox').removeClass('d-none');
                        }
                    });
                });

                // ✅ Close history
                $('#closeHistoryBtn').off('click').on('click', function() {
                    $('#paymentHistoryBox').addClass('d-none');
                });
            });

            $('#paymentMethodSelect').on('change', function() {
                let method = $(this).val();

                // Hide all optional sections first
                $('#cashOnlineTypeDiv, #fullyCashOnlineFields, #partialCashOnlineFields, #onlineTypeDiv, #paidTypeDiv, #upiAmountDiv, #partialPaidFields, #fullyPaidFields, #bank_container')
                    .addClass('d-none');
                $('#bank_id').val('');

                if (method === 'cash') {
                    $('#paidTypeDiv').removeClass('d-none'); // Show paid type options

                } else if (method === 'online') {
                    $('#onlineTypeDiv').removeClass('d-none'); // Show online type dropdown
                    $('#bank_container').removeClass('d-none');

                } else if (method === 'cash_online') {
                    $('#cashOnlineTypeDiv').removeClass('d-none'); // Show Cash + Online type dropdown
                    $('#bank_container').removeClass('d-none');
                }
            });

            $('#bank_id').on('change', function() {
                if ($(this).val()) {
                    $("#bankError").text("");
                }
            });

            $('#openAddBankModal').on('click', function() {
                resetAddBankForm();
                if (addBankModal) {
                    addBankModal.show();
                }
            });

            $('#addBankModal').on('hidden.bs.modal', function() {
                if ($('#makePaymentModal').hasClass('show')) {
                    $('body').addClass('modal-open');
                }
            });

            $('#addBankForm').on('submit', function(e) {
                e.preventDefault();

                $('#addBankForm .text-danger').text('');

                const formData = new FormData(this);
                if (selectedSubAdminId) {
                    formData.append('selectedSubAdminId', selectedSubAdminId);
                }

                const saveButton = $('#saveBankBtn');
                saveButton.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: '/api/banks',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        upsertBankOption(response.data || null);
                        $("#bankError").text("");

                        if (addBankModal) {
                            addBankModal.hide();
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'Bank added successfully.',
                            confirmButtonText: 'OK'
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
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Failed to add bank.',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    complete: function() {
                        saveButton.prop('disabled', false).text('Save Bank');
                    }
                });
            });

            $('#paidTypeSelect').on('change', function() {
                let type = $(this).val();
                let remaining = parseFloat($('#remainingAmountHidden').val()) || 0;

                // Hide both sections
                $('#partialPaidFields, #fullyPaidFields').addClass('d-none');

                // Disable all inputs first
                $('#partialPaidFields input, #fullyPaidFields input').prop('disabled', true);

                if (type === 'cash_partially') {
                    // Show partial fields
                    $('#partialPaidFields').removeClass('d-none');
                    $('#partialPaidFields input').prop('disabled', false);

                    // Clear & reset values
                    $('#partialAmount').val('');
                    $('#pendingAmount').val(parseFloat(remaining).toFixed(2));

                    // Remove any full cash amount
                    $('#cashAmount').val('').prop('readonly', false).prop('disabled', true);

                    // Live calculation for pending
                    $('#partialAmount').off('input').on('input', function() {
                        let entered = parseCurrency($(this).val()) || 0;
                        let newPending = Math.max(remaining - entered, 0);
                        $('#pendingAmount').val(newPending.toFixed(2));
                    });

                } else if (type === 'cash_fully') {
                    // Show fully fields
                    $('#fullyPaidFields').removeClass('d-none');
                    $('#fullyPaidFields input').prop('disabled', false);

                    // Fill full amount & disable editing
                    $('#cashAmount').val(parseFloat(remaining).toFixed(2)).prop('readonly', true);

                    // Reset partial fields
                    $('#partialAmount, #pendingAmount').val('');
                    $('#partialPaidFields input').prop('disabled', true);
                }
            });


            // ✅ Handle Online Type (when Payment Method = Online)
            $('#onlineTypeSelect').on('change', function() {
                let type = $(this).val();
                let remaining = parseFloat($('#remainingAmountHidden').val()) || 0;

                // Hide both sections
                $('#upiAmountDiv, #partialPaidFields').addClass('d-none');
                // Disable all inputs first
                $('#upiAmountDiv input, #partialPaidFields input').prop('disabled', true);

                if (type === 'online_partially') {
                    // Show partial fields
                    $('#partialPaidFields').removeClass('d-none');
                    $('#partialPaidFields input').prop('disabled', false);

                    // Reset values
                    $('#partialAmount').val('');
                    $('#pendingAmount').val(parseFloat(remaining).toFixed(2));

                    // Clear and disable UPI field
                    $('#upiAmountInput').val('').prop('readonly', false).prop('disabled', true);

                    // Live pending update
                    $('#partialAmount').off('input').on('input', function() {
                        let entered = parseFloat($(this).val()) || 0;
                        let newPending = Math.max(remaining - entered, 0);
                        $('#pendingAmount').val(newPending.toFixed(2));
                    });

                } else if (type === 'online_fully') {
                    // Show fully online section
                    $('#upiAmountDiv').removeClass('d-none');
                    $('#upiAmountDiv input').prop('disabled', false);

                    // Fill with remaining and lock editing
                    $('#upiAmountInput').val(parseFloat(remaining)).prop('readonly', true);

                    // Reset partial section
                    $('#partialAmount, #pendingAmount').val('');
                    $('#partialPaidFields input').prop('disabled', true);
                }
            });

            // ✅ Handle Cash + Online Type
            $('#cashOnlineTypeSelect').on('change', function() {
                let type = $(this).val();
                let remaining = parseFloat($('#remainingAmountHidden').val()) || 0;

                // Hide both sections
                $('#fullyCashOnlineFields, #partialCashOnlineFields').addClass('d-none');
                // Disable all inputs first
                $('#fullyCashOnlineFields input, #partialCashOnlineFields input').prop('disabled', true);

                if (type === 'cash_online_fully') {
                    // Show fully section
                    $('#fullyCashOnlineFields').removeClass('d-none');
                    $('#fullyCashOnlineFields input').prop('disabled', false);

                    // Reset values
                    $('#cashOnlineFullAmount').val('');
                    $('#upiOnlineFullAmount').val(parseFloat(remaining).toFixed(2));

                    // Live adjustment of online amount
                    $('#cashOnlineFullAmount').off('input').on('input', function() {
                        let cash = parseCurrency($(this).val()) || 0;
                        let online = Math.max(remaining - cash, 0);
                        $('#upiOnlineFullAmount').val(formatCurrency(online));
                    });

                    // Disable partial fields
                    $('#partialCashOnlineFields input').prop('disabled', true);

                } else if (type === 'cash_online_partially') {
                    // Show partial section
                    $('#partialCashOnlineFields').removeClass('d-none');
                    $('#partialCashOnlineFields input').prop('disabled', false);

                    // Reset values
                    $('#cashOnlinePartialAmount, #upiOnlinePartialAmount').val('');
                    $('#remainingCashOnlineAmount').val(parseFloat(remaining).toFixed(2));

                    // Live update on cash input
                    $('#cashOnlinePartialAmount').off('input').on('input', function() {
                        let cash = parseCurrency($(this).val()) || 0;
                        let online = parseCurrency($('#upiOnlinePartialAmount').val()) || 0;
                        let newRemaining = Math.max(remaining - cash - online, 0);
                        $('#remainingCashOnlineAmount').val(newRemaining.toFixed(2));
                    });

                    // Live update on online input
                    $('#upiOnlinePartialAmount').off('input').on('input', function() {
                        let online = parseCurrency($(this).val()) || 0;
                        let cash = parseCurrency($('#cashOnlinePartialAmount').val()) || 0;
                        let newRemaining = Math.max(remaining - cash - online, 0);
                        $('#remainingCashOnlineAmount').val(newRemaining.toFixed(2));
                    });

                    // Disable fully fields
                    $('#fullyCashOnlineFields input').prop('disabled', true);
                }
            });

            $('#makePaymentForm').on('submit', function(e) {
                e.preventDefault();


                let isValid = true;


                // Clear all previous errors
                $('.text-danger').text('');

                // Get values
                let paymentMethod = $('#paymentMethodSelect').val();
                let paymentTypeemionly = $('#paymentType').val();
                let paidType = $('#paidTypeSelect').val();
                let onlineType = $('#onlineTypeSelect').val();
                let cashOnlineType = $('#cashOnlineTypeSelect').val();
                let pendingAmount = $('#pendingAmount').val();
                let remarks = $('#remarks').val();

                // console.log(pendingAmount);
                // console.log(onlineType);

                // return false;
                // EMI Validation



                // Payment Method validation


                if (!paymentMethod) {
                    isValid = false;
                    // console.log("Validation failed: Payment method not selected");
                    $('#paymentMethodError').text("Please select a payment method.");
                    return false;
                } else {
                    // console.log("Payment method selected:", paymentMethod);
                }


                // Cash Payment Validation
                if (paymentMethod === 'cash') {
                    // console.log("Cash payment selected");

                    if (!paidType) {
                        isValid = false;
                        // console.log("Validation failed: Paid type not selected");
                        $('#paidTypeError').text("Please select paid type.");
                        return false;
                    } else {
                        // console.log("Paid type selected:", paidType);
                    }

                    if (paidType === 'cash_partially') {
                        let partialAmount = parseCurrency($('#partialAmount').val()) || 0;
                        let remainingAmount = parseCurrency($('#remainingAmountHidden').val()) || 0;

                        // console.log("Cash partially selected, entered amount:", partialAmount, "Remaining:",
                        //     remainingAmount);

                        if (!partialAmount || isNaN(partialAmount) || partialAmount <= 0) {
                            isValid = false;
                            $('#partialAmountError').text("Enter a valid positive partial cash amount.");
                            return false;
                        }

                        if (partialAmount > remainingAmount) {
                            isValid = false;
                            $('#partialAmountError').text(
                                "Partial cash amount cannot exceed remaining amount (" + remainingAmount
                                .toFixed(2) + ")."
                            );
                            return false;
                        }

                        if (partialAmount < 0) {
                            isValid = false;
                            $('#partialAmountError').text("Amount cannot be negative.");
                            return false;
                        }

                        // console.log("Partial cash amount valid");
                    } else {
                        let cashAmount = parseCurrency($('#cashAmount').val()) || 0;
                        // console.log("Cash fully selected, amount:", cashAmount);
                        if (!cashAmount || cashAmount <= 0) {
                            isValid = false;
                            // console.log("Validation failed: Invalid full cash amount");
                            $('#cashAmountError').text("Enter a valid cash amount.");
                            return false;
                        } else {
                            // console.log("Full cash amount valid");
                        }
                    }
                }

                // Online Payment Validation
                if (paymentMethod === 'online') {
                    // console.log("Online payment selected");

                    if (!onlineType) {
                        isValid = false;
                        // console.log("Validation failed: Online type not selected");
                        $('#onlineTypeError').text("Please select online type.");
                        return false;
                    } else {
                        // console.log("Online type selected:", onlineType);
                    }

                    if (!$("#bank_id").val()) {
                        isValid = false;
                        $("#bankError").text("Please select a bank");
                        return false;
                    }

                    let onlineAmount = parseCurrency($('#partialAmount').val()) || parseCurrency($(
                        '#upiAmountInput').val()) || 0;
                    let remainingAmount = parseCurrency($('#remainingAmountHidden').val()) || 0;

                    // console.log("Online amount entered:", onlineAmount, "Remaining:", remainingAmount);

                    // ✅ Check 1: Must be a valid positive number
                    if (!onlineAmount || isNaN(onlineAmount) || onlineAmount <= 0) {
                        isValid = false;
                        if (onlineType === 'online_partially') {
                            // console.log("Validation failed: Invalid partial online amount");
                            $('#partialAmountError').text("Enter a valid positive online partial amount.");
                        } else {
                            // console.log("Validation failed: Invalid full online amount");
                            $('#upiAmountError').text("Enter a valid positive online amount.");
                        }
                        return false;
                    }

                    // ✅ Check 2: Cannot exceed remaining
                    if (onlineType === 'online_partially' && onlineAmount > remainingAmount) {
                        // console.log(onlineType);
                        isValid = false;
                        // console.log("Validation failed: Partial online amount exceeds remaining");
                        $('#partialAmountError').text(
                            "Partial online amount cannot exceed remaining amount (" + remainingAmount
                            .toFixed(2) + ")."
                        );
                        return false;
                    }

                    // ✅ Check 3: Cannot be negative
                    if (onlineAmount < 0) {
                        isValid = false;
                        // console.log("Validation failed: Negative online amount");
                        $('#partialAmountError').text("Amount cannot be negative.");
                        return false;
                    }

                    // console.log("Online amount valid");
                }


                // Cash + Online Validation
                if (paymentMethod === 'cash_online') {
                    // console.log("Cash + Online payment selected");

                    if (!cashOnlineType) {
                        isValid = false;
                        // console.log("Validation failed: Cash + Online type not selected");
                        $('#cashOnlineTypeError').text("Please select Cash + Online type.");
                        return false;
                    } else {
                        // console.log("Cash + Online type selected:", cashOnlineType);
                    }

                    if (!$("#bank_id").val()) {
                        isValid = false;
                        $("#bankError").text("Please select a bank");
                        return false;
                    }

                    if (cashOnlineType === 'cash_online_fully') {
                        let cashAmt = parseCurrency($('#cashOnlineFullAmount').val()) || 0;
                        let onlineAmt = parseCurrency($('#upiOnlineFullAmount').val()) || 0;
                        // console.log("Cash+Online fully amounts:", cashAmt, onlineAmt);

                        if (!cashAmt || cashAmt <= 0 || !onlineAmt || onlineAmt <=
                            0) {
                            isValid = false;
                            // console.log("Validation failed: Invalid fully cash + online amounts");
                            $('#cashOnlineFullAmountError').text("Enter a valid cash amount.");
                            $('#upiOnlineFullAmountError').text("Enter a valid online amount.");
                            return false;
                        } else {
                            // console.log("Fully cash + online amounts valid");
                        }
                    }


                    if (cashOnlineType === 'cash_online_partially') {
                        // let cashAmt = parseCurrency($('#cashOnlinePartialAmount').val()) || 0;
                        // let onlineAmt = parseCurrency($('#upiOnlinePartialAmount').val()) || 0;

                        // Clean pending amount
                        // let rawPending = $('#remainingCashOnlineAmount').val() || "0";
                        // rawPending = rawPending.replace(/[₹,]/g, '').trim();
                        // let pendingAmt = parseCurrency(rawPending) || 0;
                        let cashAmt = parseCurrency($('#cashOnlinePartialAmount').val()) || 0;
                        let onlineAmt = parseCurrency($('#upiOnlinePartialAmount').val()) || 0;

                        // ✅ Get original remaining (correct source)
                        let originalRemaining = parseFloat($('#remainingAmountHidden').val()) || 0;

                        // ✅ VALIDATION
                        if ((cashAmt + onlineAmt) > originalRemaining) {
                            isValid = false;

                            $('#cashOnlinePartialAmountError').text(
                                "Total payment cannot exceed pending amount (" + originalRemaining.toFixed(2) + ")."
                            );

                            $('#upiOnlinePartialAmountError').text(
                                "Total payment cannot exceed pending amount (" + originalRemaining.toFixed(2) + ")."
                            );

                            return false;
                        }

                        // console.log("Cash+Online partially amounts:", cashAmt, onlineAmt, "Pending:",
                        //     pendingAmt);

                        // ✅ Check for invalid or negative input
                        if ((cashAmt <= 0 && onlineAmt <= 0)) {
                            isValid = false;
                            // console.log("Validation failed: Invalid partially cash + online amounts");
                            $('#cashOnlinePartialAmountError').text("Enter at least one valid amount.");
                            $('#upiOnlinePartialAmountError').text("Enter at least one valid amount.");
                            return false;
                        }

                        if (cashAmt < 0 || onlineAmt < 0) {
                            isValid = false;
                            // console.log("Validation failed: Negative amounts are not allowed");
                            $('#cashOnlinePartialAmountError').text("Negative amount not allowed.");
                            $('#upiOnlinePartialAmountError').text("Negative amount not allowed.");
                            return false;
                        }

                        // ✅ Total should not exceed pending amount
                        let total = cashAmt + onlineAmt;
                        // console.log("Total payment:", total, "Pending amount:", pendingAmt);

                        if (total > originalRemaining) {
                            isValid = false;
                            // console.log("Validation failed: Total exceeds pending amount");
                            $('#cashOnlinePartialAmountError').text(
                                "Total payment cannot exceed pending amount (" + originalRemaining .toFixed(2) +
                                ").");
                            $('#upiOnlinePartialAmountError').text(
                                "Total payment cannot exceed pending amount (" + originalRemaining .toFixed(2) +
                                ").");
                            return false;
                        }

                        // console.log("Partially cash + online amounts valid");
                    }

                }


                // console.log('done pay');


                if (isValid) {
                    // this.submit(); // submit the form
                }
                let selectedPaymentType = $('#paymentType').val();
                $('#paymentMethodHidden').val(selectedPaymentType);

                if ($("#paymentMethodDiv").hasClass("d-none")) {
                    $("#newEmiHidden").prop("disabled", true).val("");
                }



                let formElement = $(this)[0];
                let formData = new FormData(formElement);

                let submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true).text('Processing...');
                if (paymentMethodSelect === 'emi') {
                    let emiTotal = $('#emiTotalCalculated').val();
                    formData.append('emi_paid_value', emiTotal);
                }

                if (selectedPaymentType === 'emi') {
                    let emi_val = $('#emiTotalCalculated').val();
                    formData.append('amount', emi_val);
                }

                $.ajax({
                    url: "{{ route('make-payment.purchase') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        $('#makePaymentModal').modal('hide');
                        submitButton.prop('disabled', false).text('Submit Payment');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Payment submitted successfully.',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        submitButton.prop('disabled', false).text('Submit Payment');

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMsg = '';
                            $.each(errors, function(key, value) {
                                errorMsg += value + '\n';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: errorMsg
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Please try again.'
                            });
                        }
                    }
                });
            });

            // Reset Make Payment Modal when closed
            $('#makePaymentModal').on('hidden.bs.modal', function() {

                // Reset form
                $('#makePaymentForm')[0].reset();

                // Hide all dynamic sections
                $('#cashOnlineTypeDiv, #fullyCashOnlineFields, #partialCashOnlineFields, #onlineTypeDiv, #paidTypeDiv, #upiAmountDiv, #partialPaidFields, #fullyPaidFields, #bank_container')
                    .addClass('d-none');

                // Clear error messages
                $('.text-danger').text('');

                // Clear payment history
                $('#paymentHistoryBox').addClass('d-none');
                $('#paymentHistoryList').html('');

                // Clear input values
                $('#cashOnlineFullAmount, #upiOnlineFullAmount, #cashOnlinePartialAmount, #upiOnlinePartialAmount, #remainingCashOnlineAmount')
                    .val('');
                $('#partialAmount, #pendingAmount, #cashAmount, #upiAmountInput').val('');

                // Reset dropdowns
                $('#paymentMethodSelect').val('').trigger('change');
                $('#cashOnlineTypeSelect').val('');
                $('#onlineTypeSelect').val('');
                $('#paidTypeSelect').val('');
                $('#bank_id').val('');

                // Reset display values
                $('#emiTotal').text('0.00');
                $('#remainingAmountDisplay').text('0.00');
                $('#returnAmountDisplay').text('0.00');

                // Reset hidden fields
                $('#paymentJobCardId').val('');
                $('#remainingAmountHidden').val('');
                $('#paymentMethodHidden').val('');
            });

            $(function() {

                function formatDate(dateStr) {
                    if (!dateStr) return 'N/A';
                    const d = new Date(dateStr);
                    const day = String(d.getDate()).padStart(2, '0');
                    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct",
                        "Nov", "Dec"
                    ];
                    const month = months[d.getMonth()];
                    const year = d.getFullYear();
                    let hours = d.getHours();
                    const minutes = String(d.getMinutes()).padStart(2, '0');
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12 || 12;
                    return `${day}-${month}-${year}`;
                }

                function badge(text, okClass, warnClass) {
                    const positiveStatuses = ['paid', 'completed', 'delivered', 'success'];
                    const isPositive = positiveStatuses.includes(String(text).toLowerCase());
                    const cls = isPositive ? okClass : warnClass;
                    return `<span class="badges ${cls}" style="text-transform:capitalize;">${text}</span>`;
                }

                function actionLinks(o) {


                    let menuItems = '';

                    // Show "Make Payment" button only if remaining amount > 0
                    if (parseFloat(o.remaining_amount) > 0) {

                        menuItems += `<button type="button" class="dropdown-item make-payment-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#makePaymentModal"
                            data-id="${o.id}"
                            data-amount="${o.remaining_amount}"
                            data-method="${o.payment_mode || ''}"
                            data-total-amount="${o.grand_total || 0}"
                            data-remaining-amount="${o.remaining_amount}"
                            data-return-amount="${o.total_return || 0}">
                            <i class="fas fa-money-bill"></i><span>Pay</span>
                        </button>`;
                    }
                    // console.log(o);

                    // Other actions (always visible)
                    menuItems += `
                        <button type="button" class="dropdown-item open-history" data-id="${o.id}">
                            <i class="fas fa-history"></i><span>History</span>
                        </button>
                        <a class="dropdown-item" href="/print-purchase/${o.id}">
                            <i class="fas fa-eye"></i><span>View</span>
                        </a>`;

                    @if (app('hasPermission')(3, 'edit'))
                        if (parseFloat(o.total_return || 0) === 0) {
                            menuItems += `<a class="dropdown-item" href="/edit-purchase/${o.id}">
                                <i class="fas fa-edit"></i><span>Edit</span>
                            </a>`;
                        }
                    @endif


                    @if (app('hasPermission')(3, 'view'))
                        menuItems += `<a class="dropdown-item" href="javascript:void(0);" onclick="window.open('/purchase/invoice/pdf/' + ${o.id});">
                            <i class="fas fa-print"></i><span>Print</span>
                        </a>`;
                    @endif

                    @if (app('hasPermission')(3, 'delete'))
                        menuItems += `<a class="dropdown-item text-danger confirm-text delete-order" data-id="${o.id}" href="javascript:void(0);">
                            <i class="fas fa-trash"></i><span>Delete</span>
                        </a>`;
                    @endif

                    return `<div class="dropdown purchase-action-dropdown">
                        <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end purchase-action-menu">
                            ${menuItems || '<span class="dropdown-item text-muted">No actions</span>'}
                        </div>
                    </div>`;
                }

                function positionPurchaseActionMenu(dropdown) {
                    const $dropdown = $(dropdown);
                    const $menu = $dropdown.data('floating-menu') || $dropdown.find('.purchase-action-menu');
                    const button = $dropdown.find('.action-menu-toggle')[0];

                    if (!$menu.length || !button) {
                        return;
                    }

                    const rect = button.getBoundingClientRect();
                    const menuEl = $menu[0];
                    const menuWidth = 168;
                    const menuHeight = menuEl.offsetHeight || 220;
                    const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
                    const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
                    const gap = 4;

                    let left = rect.right - menuWidth;
                    left = Math.max(8, Math.min(left, viewportWidth - menuWidth - 8));

                    let top = rect.bottom + gap;
                    if (top + menuHeight > viewportHeight - 8) {
                        top = Math.max(8, rect.top - menuHeight - gap);
                    }

                    menuEl.style.setProperty('left', `${left}px`, 'important');
                    menuEl.style.setProperty('position', 'fixed', 'important');
                    menuEl.style.setProperty('top', `${top}px`, 'important');
                    menuEl.style.setProperty('transform', 'none', 'important');
                    menuEl.style.setProperty('width', `${menuWidth}px`, 'important');
                    menuEl.style.setProperty('min-width', `${menuWidth}px`, 'important');
                    menuEl.style.setProperty('max-width', `${menuWidth}px`, 'important');
                }

                $(document).on('show.bs.dropdown', '.purchase-action-dropdown', function() {
                    const $dropdown = $(this);
                    const $menu = $dropdown.find('.purchase-action-menu');

                    if (!$menu.length) {
                        return;
                    }

                    $dropdown.data('floating-menu', $menu);
                    $dropdown.data('menu-parent', $menu.parent());
                    $dropdown.data('menu-next', $menu.next());
                    $menu.addClass('purchase-action-menu-floating').appendTo('body');
                });

                $(document).on('shown.bs.dropdown', '.purchase-action-dropdown', function() {
                    const dropdown = this;
                    positionPurchaseActionMenu(dropdown);
                    requestAnimationFrame(function() {
                        positionPurchaseActionMenu(dropdown);
                    });
                    setTimeout(function() {
                        positionPurchaseActionMenu(dropdown);
                    }, 50);
                });

                $(document).on('hidden.bs.dropdown', '.purchase-action-dropdown', function() {
                    const $dropdown = $(this);
                    const $menu = $dropdown.data('floating-menu');
                    const $parent = $dropdown.data('menu-parent');
                    const $next = $dropdown.data('menu-next');

                    if ($menu && $menu.length && $parent && $parent.length) {
                        $menu.removeClass('purchase-action-menu-floating').removeAttr('style');
                        if ($next && $next.length && $.contains($parent[0], $next[0])) {
                            $menu.insertBefore($next);
                        } else {
                            $parent.append($menu);
                        }
                    }

                    $dropdown.removeData('floating-menu menu-parent menu-next');
                });

                $(window).on('scroll resize', function() {
                    $('.purchase-action-dropdown.show').each(function() {
                        positionPurchaseActionMenu(this);
                    });
                });

                // function loadPurchases() {
                //     const rawDate = $('#filter-date').val();
                //     const [day, month, year] = rawDate ? rawDate.split('-') : [null, null, null];
                //     const formattedDate = rawDate ? `${year}-${month}-${day}` : '';

                //     const filters = {
                //         date: formattedDate,
                //         selectedSubAdminId: selectedSubAdminId || null,
                //         vendor_name: $('#filter_inputs input[placeholder="Enter Name"]').val().trim(),
                //         reference_no: $('#filter_inputs input[placeholder="Enter Reference No"]').val()
                //             .trim(),
                //         status: $('#filter_inputs select').val()
                //     };

                //     $.ajax({
                //         url: "/api/purchase_list",
                //         type: "GET",
                //         dataType: "json",
                //         data: filters,
                //         headers: {
                //             "Authorization": "Bearer " + authToken
                //         },
                //         success: res => {
                //             const rows = [];

                //             if (res.success && Array.isArray(res.data)) {
                //                 const currencySymbol = res.currency_symbol || '₹';
                //                 const currencyPosition = res.currency_position || 'left';

                //                 // Store purchase data for expandable rows
                //                 if (!window.purchaseDataMap) {
                //                     window.purchaseDataMap = {};
                //                 }

                //                 res.data.forEach(o => {
                //                     const amount = formatCurrency(o.grand_total || 0);
                //                     const formattedAmount = currencyPosition ===
                //                         'right' ?
                //                         `${amount}${currencySymbol}` :
                //                         `${currencySymbol}${amount}`;

                //                     // Store purchase data for expandable row
                //                     const purchaseData = {
                //                         ...o,
                //                         invoice_date: formatDate(o.invoice_date || o
                //                             .date || o.created_at),
                //                         displayAmount: formattedAmount,
                //                         currencySymbol: currencySymbol,
                //                         currencyPosition: currencyPosition
                //                     };
                //                     window.purchaseDataMap[o.id] = purchaseData;

                //                     rows.push([
                //                         `<a href="/print-purchase/${o.id}" style="text-decoration: none;">${o.invoice_number || ''}</a>`,
                //                         `<button class="mobile-toggle-btn-table" onclick="togglePurchaseRowDetails('${o.id}')" data-purchase-id="${o.id}">
            //                             <span class="toggle-icon">+</span>
            //                         </button>`,
                //                         formatDate(o.invoice_date || o.date || o
                //                             .created_at),
                //                         `<span style="text-transform:capitalize;">${o.vendor_name || ''}</span>`,
                //                         formattedAmount,
                //                         badge((o.purchase_status || o.status ||
                //                                 ''), 'bg-lightgreen',
                //                             'bg-lightred'),
                //                         (parseFloat(o.extra_paid || 0) > 0) ?
                //                         `<span class="badges bg-lightred" style="text-transform:capitalize;">Extra Paid: ${currencySymbol}${formatCurrency(o.extra_paid)}</span>` :
                //                         badge(o.payment_status, 'bg-lightgreen',
                //                             'bg-lightred'),
                //                         parseFloat(o.total_return || 0) > 0 ?
                //                         `<span class="status-badge status-pending">Returned</span>` :
                //                         `<span class="status-badge status-completed">No return</span>`,
                //                         actionLinks(o)
                //                     ]);
                //                 });

                //                 const $tbl = $('#order-table');

                //                 function updateTotal(dt) {
                //                     setTimeout(() => calculatePurchaseFilteredTotal(), 100);
                //                 }

                //                 if ($.fn.DataTable.isDataTable($tbl)) {
                //                     const dt = $tbl.DataTable();
                //                     dt.clear().rows.add(rows).draw();
                //                     dt.off('draw').on('draw', () => {
                //                         updateTotal(dt);
                //                         if (window.addPurchaseExpandableRows) {
                //                             window.addPurchaseExpandableRows(dt);
                //                         }
                //                     });
                //                     updateTotal(dt);
                //                     setTimeout(() => {
                //                         if (window.addPurchaseExpandableRows) {
                //                             window.addPurchaseExpandableRows(dt);
                //                         }
                //                     }, 100);
                //                 } else {
                //                     const dt = $tbl.DataTable({
                //                         data: rows,
                //                         responsive: true,
                //                         autoWidth: false,
                //                         pageLength: 10,
                //                         ordering: true,
                //                         searching: true,
                //                         language: {
                //                             searchPlaceholder: "Search purchases...",
                //                             search: ""
                //                         }
                //                     });
                //                     dt.on('draw', () => {
                //                         updateTotal(dt);
                //                         if (window.addPurchaseExpandableRows) {
                //                             window.addPurchaseExpandableRows(dt);
                //                         }
                //                     });
                //                     updateTotal(dt);
                //                     setTimeout(() => {
                //                         if (window.addPurchaseExpandableRows) {
                //                             window.addPurchaseExpandableRows(dt);
                //                         }
                //                     }, 100);
                //                 }

                //                 // Update global purchaseTable reference
                //                 purchaseTable = $tbl.DataTable();

                //                 // Populate customer dropdown after first draw
                //                 purchaseTable.one('draw', function() {
                //                     let customers = new Set();
                //                     purchaseTable.column(3, {
                //                         search: 'applied'
                //                     }).data().each(function(d) {
                //                         let name = $('<div>').html(d).text()
                //                             .trim();
                //                         customers.add(name);
                //                     });

                //                     let $filter = $("#filter-customer");
                //                     $filter.empty().append(
                //                         '<option value="">-- Select Vendor --</option>'
                //                     );
                //                     customers.forEach(function(name) {
                //                         $filter.append('<option value="' +
                //                             name + '">' + name + '</option>'
                //                         );
                //                     });
                //                     $filter.trigger('change');
                //                 });

                //                 // Scroll sync
                //                 //    const topScroll = document.querySelector('.table-scroll-top');
                //                 //    const tableResponsive = document.querySelector(
                //                 //       '.table-responsive');
                //                 //    const table = document.getElementById('order-table');
                //                 //     topScroll.querySelector('div').style.width = table.scrollWidth +
                //                 //       'px';
                //                 //
                //                 //  topScroll.onscroll = () => {
                //                 //       tableResponsive.scrollLeft = topScroll.scrollLeft;
                //                 //    };
                //                 //    tableResponsive.onscroll = () => {
                //                 //      topScroll.scrollLeft = tableResponsive.scrollLeft;
                //                 //  };
                //             }
                //         },
                //         error: xhr => console.error('purchase_list error:', xhr.responseText)
                //     });
                // }// Pagination state
                // let currentPage = 1;
                // let lastPage = 1;
                // let perPage = 10;
                // let searchQuery = '';

                function loadPurchases(page = 1) {
                    // ✅ Prevent concurrent requests
                    if (isLoading) {
                        if (currentRequest) {
                            currentRequest.abort();
                        }
                    }

                    isLoading = true;

                    const rawDate = $('#filter-date').val();
                    let formattedDate = '';
                    if (rawDate) {
                        const parts = rawDate.split(/[-\/]/);
                        if (parts.length === 3) {
                            if (parts[0].length <= 2 && parts[2].length === 4) {
                                // dd-mm-yyyy → yyyy-mm-dd
                                formattedDate =
                                    `${parts[2]}-${parts[1].padStart(2,'0')}-${parts[0].padStart(2,'0')}`;
                            } else {
                                formattedDate = rawDate;
                            }
                        }
                    }
                    const selectedMonth = $('#filter-month').val();
                    const selectedYear = $('#filter-year').val();
                    const selectedFinancialYear = $('#filter-financial-year').val();

                    const filters = {
                        page: page,
                        per_page: perPage,
                        date: formattedDate,
                        month: (selectedMonth && selectedMonth !== 'all') ? selectedMonth : '',
                        year: (selectedYear && selectedYear !== 'all') ? selectedYear : '',
                        financial_year: (selectedFinancialYear && selectedFinancialYear !== 'all') ?
                            selectedFinancialYear : '',
                        selectedSubAdminId: selectedSubAdminId || null,
                        search: searchQuery || ''
                    };

                    currentRequest = $.ajax({
                        url: "/api/purchase_list",
                        type: "GET",
                        dataType: "json",
                        data: filters,
                        headers: {
                            "Authorization": "Bearer " + authToken
                        },
                        success: function(res) {
                            isLoading = false;
                            currentRequest = null;

                            const rows = [];

                            if (res.success && Array.isArray(res.data)) {
                                     togglePurchaseFinancialYearFilter(res.financial_year_enabled);
                                const currencySymbol = res.currency_symbol || '₹';
                                const currencyPosition = res.currency_position || 'left';
                                const pagination = res.pagination;

                                updatePurchaseSummaryTotals(
                                    res.total_amount || 0,
                                    res.total_pending_amount || 0,
                                    res.total_paid_amount || 0,
                                    currencySymbol,
                                    currencyPosition
                                );
                                window.purchaseSummaryTotals = {
                                    total_amount: res.total_amount || 0,
                                    total_pending_amount: res.total_pending_amount || 0,
                                    total_paid_amount: res.total_paid_amount || 0,
                                    currency_symbol: currencySymbol,
                                    currency_position: currencyPosition
                                };

                                if (pagination) {
                                    currentPage = pagination.current_page;
                                    lastPage = pagination.last_page;
                                    updatePaginationUI(pagination);
                                }

                                if (!window.purchaseDataMap) {
                                    window.purchaseDataMap = {};
                                }

                                res.data.forEach(function(o) {
                                    const displayTotal = o.grand_total || 0;
                                    const amount = formatCurrency(displayTotal);
                                    const formattedAmount = currencyPosition ===
                                        'right' ?
                                        `${amount}${currencySymbol}` :
                                        `${currencySymbol}${amount}`;

                                    const purchaseData = {
                                        ...o,
                                        invoice_date: formatDate(o.invoice_date || o
                                            .date || o.created_at),
                                        displayAmount: formattedAmount,
                                        currencySymbol: currencySymbol,
                                        currencyPosition: currencyPosition
                                    };
                                    window.purchaseDataMap[o.id] = purchaseData;

                                    rows.push([
                                        `<div class="order-mobile-summary">
                                            <span class="order-mobile-vendor">${o.vendor_name || 'N/A'}</span>
                                            <a href="/print-purchase/${o.id}" class="order-mobile-link">${o.bill_no || o.invoice_number || ''}</a>
                                        </div>`,
                                        `<button class="mobile-toggle-btn-table" onclick="togglePurchaseRowDetails('${o.id}')" data-purchase-id="${o.id}">
                            <span class="toggle-icon">+</span>
                        </button>`,
                                        formatDate(o.invoice_date || o.date || o
                                            .created_at),
                                        `<span style="text-transform:capitalize;">${o.vendor_name || ''}</span>`,
                                        formattedAmount,
                                        (parseFloat(o.extra_paid || 0) > 0) ?
                                        `<span class="badges bg-lightred" style="text-transform:capitalize;">Extra Paid: ${currencySymbol}${formatCurrency(o.extra_paid)}</span>` :
                                        badge(o.payment_status, 'bg-lightgreen',
                                            'bg-lightred'),
                                        actionLinks(o)
                                    ]);
                                });

                                const $tbl = $('#order-table');

                                if ($.fn.DataTable.isDataTable($tbl)) {
                                    const dt = $tbl.DataTable();
                                    dt.clear().rows.add(rows).draw();
                                } else {
                                    const dt = $tbl.DataTable({
                                        data: rows,
                                        responsive: true,
                                        autoWidth: false,
                                        // ✅ Disable built-in pagination/search since we handle it server-side
                                        paging: false,
                                        ordering: true,
                                        searching: false,
                                        info: false,
                                        language: {
                                            searchPlaceholder: "Search purchases...",
                                            search: ""
                                        }
                                    });
                                    dt.on('draw', function() {
                                        setTimeout(function() {
                                            calculatePurchaseFilteredTotal();
                                        }, 100);
                                        if (window.addPurchaseExpandableRows) {
                                            window.addPurchaseExpandableRows(dt);
                                        }
                                    });
                                }

                                purchaseTable = $tbl.DataTable();
                                setTimeout(function() {
                                    calculatePurchaseFilteredTotal();
                                }, 100);

                                // Populate vendor dropdown from current page results
                                let $filter = $("#filter-customer");
                                let currentVal = $filter.val();
                                $filter.empty().append(
                                    '<option value="">-- Select Vendor --</option>');
                                // Use a map keyed by vendor_name to store name+phone
                                let vendorMap = new Map();
                                res.data.forEach(function(o) {
                                    if (o.vendor_name && !vendorMap.has(o.vendor_name)) {
                                        vendorMap.set(o.vendor_name, o.vendor_phone || '');
                                    }
                                });
                                vendorMap.forEach(function(phone, name) {
                                    let displayText = phone ? name + ' - ' + phone : name;
                                    let $opt = $('<option>')
                                        .val(name)
                                        .text(displayText)
                                        .attr('data-phone', phone);
                                    $filter.append($opt);
                                });
                                if (currentVal) $filter.val(currentVal);
                                $filter.trigger('change.select2');
                            } else {
                                updatePurchaseSummaryTotals(0, 0, 0, '₹', 'left');
                                window.purchaseSummaryTotals = {
                                    total_amount: 0,
                                    total_pending_amount: 0,
                                    total_paid_amount: 0,
                                    currency_symbol: '₹',
                                    currency_position: 'left'
                                };

                                if ($.fn.DataTable.isDataTable('#order-table')) {
                                    $('#order-table').DataTable().clear().draw();
                                }
                            }
                        },
                        error: function(xhr) {
                            isLoading = false;
                            currentRequest = null;
                            if (xhr.statusText !== 'abort') {
                                console.error('purchase_list error:', xhr.responseText);
                            }
                        }
                    });
                }

                // ============================================================
                // 3. REPLACE updatePaginationUI function:
                // ============================================================
                function updatePaginationUI(pagination) {
                    const currentPageNumber = parseInt(pagination.current_page || 1, 10);
                    const lastPageNumber = parseInt(pagination.last_page || 1, 10);
                    const totalItems = parseInt(pagination.total || 0, 10);
                    const from = pagination.from ?? (totalItems ? ((currentPageNumber - 1) * pagination.per_page) + 1 : 0);
                    const to = pagination.to ?? Math.min(currentPageNumber * pagination.per_page, totalItems);

                    $('#pagination-from').text(from);
                    $('#pagination-to').text(to);
                    $('#pagination-total').text(totalItems);

                    let paginationHtml = '';

                    paginationHtml += `
                        <li class="page-item ${currentPageNumber === 1 ? 'disabled' : ''}">
                            <a class="page-link" href="javascript:void(0);" data-page="${currentPageNumber - 1}">Previous</a>
                        </li>
                    `;

                    const pages = [];
                    const windowSize = 2;

                    for (let i = 1; i <= lastPageNumber; i++) {
                        if (
                            i === 1 ||
                            i === lastPageNumber ||
                            (i >= currentPageNumber - windowSize && i <= currentPageNumber + windowSize)
                        ) {
                            pages.push(i);
                        }
                    }

                    let previousPageNumber = 0;
                    pages.forEach(function(pageNumber) {
                        if (previousPageNumber && pageNumber - previousPageNumber > 1) {
                            const jumpPage = previousPageNumber + 1;
                            paginationHtml += `
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0);" data-page="${jumpPage}">...</a>
                                </li>
                            `;
                        }

                        paginationHtml += `
                            <li class="page-item ${pageNumber === currentPageNumber ? 'active' : ''}">
                                <a class="page-link" href="javascript:void(0);" data-page="${pageNumber}">${pageNumber}</a>
                            </li>
                        `;

                        previousPageNumber = pageNumber;
                    });

                    paginationHtml += `
                        <li class="page-item ${currentPageNumber === lastPageNumber || lastPageNumber === 0 ? 'disabled' : ''}">
                            <a class="page-link" href="javascript:void(0);" data-page="${currentPageNumber + 1}">Next</a>
                        </li>
                    `;

                    $('#pagination-numbers').html(paginationHtml);
                    $('.pagination-controls').show();
                }

                // ============================================================
                // 4. REPLACE all event bindings (put these ONCE inside $(document).ready):
                // ============================================================

                // ✅ Pagination click with ellipsis support - single binding with .off() first
                $(document).off('click', '#pagination-numbers .page-link')
                    .on('click', '#pagination-numbers .page-link', function(e) {
                        e.preventDefault();
                        let page = parseInt($(this).data('page'), 10);

                        // Regular page navigation
                        if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                            loadPurchases(page);
                        }
                    });

                // ✅ Per-page selector
                $('#per-page-select').off('change').on('change', function() {
                    perPage = parseInt($(this).val());
                    loadPurchases(1);
                });

                // ✅ Search input with debounce (500ms) - ONLY fire after user stops typing
                $('#search-input').off('keyup input').on('keyup input', function() {
                    clearTimeout(debounceTimer);
                    const val = $(this).val();
                    debounceTimer = setTimeout(function() {
                        if (val !== searchQuery) {
                            searchQuery = val;
                            loadPurchases(1);
                        }
                    }, 500); // ✅ Wait 500ms after user stops typing
                });

                // ✅ Month/Year filter - single binding
                $('#filter-month, #filter-year').off('change.purchase').on('change.purchase', function() {
                    // Clear date filter when month/year is used
                    loadPurchases(1);
                });

                 $('#filter-financial-year').off('change.purchase').on('change.purchase', function() {
                    if (!isFinancialYearEnabled) {
                        return;
                    }
                    loadPurchases(1);
                });

                // ✅ Date filter - use change event, not dp.change (to avoid double firing)
                $('#filter-date').off('change.purchase').on('change.purchase', function() {
                    // Clear month/year when date is used
                    loadPurchases(1);
                });

                // ✅ Vendor/Customer filter
                $('#filter-customer').off('change.purchase').on('change.purchase', function() {
                    loadPurchases(1);
                });

                // ✅ Filter button
                $('.btn-filters').off('click.purchase').on('click.purchase', function(e) {
                    e.preventDefault();
                    loadPurchases(1);
                });

                // ✅ Initial load - called ONCE
                loadPurchases(1);


                $('#filter-date').off('change.purchase dp.change').on('change.purchase dp.change',
                    function() {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(function() {
                            loadPurchases(1);
                        }, 300);
                    });
            });

    $(document).on('click', '.delete-order', function() {
            // var vendorId = $(this).data('id');
    let orderId = $(this).data("id");

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ff9f43",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/purchase_delete`,
                        type: 'POST',
                        headers: {
                            "Authorization": "Bearer " + authToken,
                        },
                        data: {
                            id: orderId,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#ff9f43",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                     window.location.reload();// refresh current page
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                    confirmButtonColor: "#ff9f43",
                                    confirmButtonText: "OK"
                                });
                            }
                        },
                        error: function(xhr) {
                            let message = "Something went wrong!";
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                message = xhr.responseJSON.error;
                            }
                            Swal.fire({
                                title: "Error!",
                                text: message,
                                icon: "error",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            });
                        }
                    });
                }
            });
        });




            // Handle delete action
//           $(document).on("click", ".delete-order", function() {
//     let orderId = $(this).data("id");

//     Swal.fire({
//         title: "Are you sure?",
//         text: "You won't be able to revert this!",
//         icon: "warning",
//         showCancelButton: true,
//         confirmButtonColor: "#ff9f43",
//         cancelButtonColor: "#6c757d",
//         confirmButtonText: "Yes, delete it!"
//     }).then((result) => {
//         if (result.isConfirmed) {
//             $.ajax({
//                 url: "/api/purchase_delete",
//                 type: "POST",
//                 data: {
//                     id: orderId,
//                     _token: "{{ csrf_token() }}"
//                 },
//                 headers: {
//                     "Authorization": "Bearer " + authToken,
//                 },
//                 success: function(response) {

//                     if (response && (response.success === true || response.success == 1)) {

//                         Swal.fire({
//                             title: "Deleted!",
//                             text: response.message || "Record deleted successfully",
//                             icon: "success",
//                             confirmButtonColor: "#ff9f43"
//                         }).then(() => {
//                             window.location.reload(true); // ✅ FORCE reload
//                         });

//                     } else {
//                         Swal.fire({
//                             title: "Error!",
//                             text: response.message,
//                             icon: "error",
//                             confirmButtonColor: "#ff9f43"
//                         });
//                     }
//                 },
//                 error: function(xhr) {
//                     let errorMessage = "Something went wrong. Try again.";
//                     if (xhr.responseJSON && xhr.responseJSON.message) {
//                         errorMessage = xhr.responseJSON.message;
//                     }

//                     Swal.fire({
//                         title: "Error!",
//                         text: errorMessage,
//                         icon: "error",
//                         confirmButtonColor: "#ff9f43"
//                     });
//                 }
//             });
//         }
//     });
// });   // Global history modal open
            $(document).on('click', '.open-history', function() {
                const jobCardId = $(this).data('id');
                $('#globalPaymentHistoryList').html('<li class="list-group-item">Loading...</li>');
                $.ajax({
                    url: '/api/purchase/payment-history/' + jobCardId,
                    method: 'GET',
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {

                        const history = response.data || [];
                        const summary = response.summary || {};
                        let html = '';

                        if (history.length === 0) {
                            html = `<li class="list-group-item">No payment history found.</li>`;
                        } else {
                            html = history.map(p => `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>${formatDate(p.payment_date) || p.created_at}</span>
                <span>
                    <strong>₹${formatCurrency(p.payment_amount)}</strong>
                    (${p.payment_method? p.payment_method.charAt(0).toUpperCase() + p.payment_method.slice(1) : '-'})
                </span>
            </li>
        `).join('');
                        }

                        // 🔹 Summary section (same as sales)
                        html += `
        <li class="list-group-item mt-2 bg-light">
            <strong>Purchase Total:</strong>
            ₹${formatCurrency(summary.order_total)}
        </li>

        <li class="list-group-item bg-light">
            <strong>Total Paid:</strong>
            ₹${formatCurrency(summary.total_paid)}
        </li>

        <li class="list-group-item bg-light">
            <strong>Total Return:</strong>
            ₹${formatCurrency(summary.total_return)}
        </li>
    `;

                        if (parseFloat(summary.extra_paid) > 0) {
                            html += `
            <li class="list-group-item bg-warning">
                <strong>Extra Paid:</strong>
                ₹${formatCurrency(summary.extra_paid)}
                <span class="text-danger">(Advance / Refund)</span>
            </li>
        `;
                        } else {
                            html += `
            <li class="list-group-item bg-light">
                <strong>Remaining:</strong>
                ₹${formatCurrency(summary.remaining)}
            </li>
        `;
                        }

                        $('#globalPaymentHistoryList').html(html);
                        $('#globalPaymentHistoryList .list-group-item').each(function(index) {
                            if (!history[index]) return;
                            const payment = history[index];
                            if (index >= history.length) return;
                            if ($(this).find('.purchase-payment-actions').length) return;

                            const actionHtml = `
                                <div class="purchase-payment-actions d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-warning purchase-payment-edit-btn"
                                        data-payment-id="${payment.id}"
                                        data-payment-date="${payment.payment_date || ''}"
                                        data-payment-amount="${payment.payment_amount || ''}"
                                        data-payment-method="${payment.payment_method || ''}"
                                        data-payment-type="${payment.payment_type || ''}"
                                        data-payment-remarks="${payment.remarks || ''}"
                                        title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger purchase-payment-delete-btn"
                                        data-payment-id="${payment.id}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>`;
                            $(this).find('.d-flex.justify-content-between, .d-flex.justify-content-between.align-items-center, .d-flex.justify-content-between.align-items-start').append(actionHtml);
                        });

                        new bootstrap.Modal(
                            document.getElementById('paymentHistoryModal')
                        ).show();
                    },
                    error: function() {
                        $('#globalPaymentHistoryList').html(
                            '<li class="list-group-item text-danger">Failed to load payment history.</li>'
                        );
                        const modal = new bootstrap.Modal(document.getElementById(
                            'paymentHistoryModal'));
                        modal.show();
                    }
                });
            });

            $(document).on('click', '.purchase-payment-edit-btn', function() {
                Swal.fire({
                    icon: 'info',
                    title: 'Edit payment',
                    text: 'Purchase payment edit is not wired yet.'
                });
            });

            $(document).on('click', '.purchase-payment-delete-btn', function() {
                Swal.fire({
                    icon: 'info',
                    title: 'Delete payment',
                    text: 'Purchase payment delete is not wired yet.'
                });
            });

            // Function to force CSS media query recalculation
            function forceCSSRecalculation() {
                // Create a temporary element to force reflow
                const temp = document.createElement('div');
                temp.style.width = '1px';
                temp.style.height = '1px';
                temp.style.position = 'absolute';
                temp.style.visibility = 'hidden';
                document.body.appendChild(temp);
                void temp.offsetWidth;
                void temp.offsetHeight;
                document.body.removeChild(temp);

                // Force reflow on viewport
                void window.innerWidth;
                void window.innerHeight;

                // Force reflow on document
                void document.documentElement.offsetWidth;
                void document.documentElement.offsetHeight;
            }

            // Resize handler for responsive behavior
            let resizeTimer;
            let lastWidth = $(window).width();

            function handlePurchaseResize() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const currentWidth = $(window).width();

                    // Always process resize to ensure CSS updates
                    lastWidth = currentWidth;

                    // Force CSS media query recalculation first
                    forceCSSRecalculation();

                    // Force CSS recalculation by triggering multiple reflows
                    const table = document.getElementById('order-table');
                    const tableResponsive = document.querySelector('.table-responsive');
                    const tableTop = document.querySelector('.table-top');
                    const cardBody = document.querySelector('.card-body');
                    const card = document.querySelector('.card');

                    // Method 1: Force reflow on all key elements
                    [table, tableResponsive, tableTop, cardBody, card].forEach(function(el) {
                        if (el) {
                            void el.offsetHeight;
                            void el.offsetWidth;
                            // Force style recalculation
                            el.style.display = 'none';
                            void el.offsetHeight;
                            el.style.display = '';
                        }
                    });

                    // Method 2: Force CSS media query recalculation
                    if (document.body) {
                        const originalDisplay = document.body.style.display;
                        document.body.style.display = 'none';
                        void document.body.offsetHeight;
                        document.body.style.display = originalDisplay;
                    }

                    // Method 3: Force table and DataTables recalculation
                    if (purchaseTable && table) {
                        // Remove all existing expandable rows first
                        $('tr.order-details-row').remove();

                        // Force DataTables to recalculate
                        try {
                            // Multiple adjustments to ensure columns update
                            purchaseTable.columns.adjust();
                            void table.offsetHeight;

                            purchaseTable.draw(false);
                            void table.offsetHeight;

                            // Additional adjustment after draw
                            setTimeout(function() {
                                purchaseTable.columns.adjust();
                                purchaseTable.draw(false);

                                // Force one more reflow
                                void table.offsetHeight;

                                // Re-add expandable rows if needed
                                setTimeout(function() {
                                    if (window.addPurchaseExpandableRows) {
                                        window.addPurchaseExpandableRows(purchaseTable);
                                    }
                                    calculatePurchaseFilteredTotal();

                                    // Final CSS recalculation
                                    forceCSSRecalculation();
                                    void table.offsetHeight;
                                }, 100);
                            }, 100);
                        } catch (e) {
                            // console.error('DataTables adjustment error:', e);
                            // Fallback: just redraw
                            purchaseTable.draw(false);
                            setTimeout(function() {
                                if (window.addPurchaseExpandableRows) {
                                    window.addPurchaseExpandableRows(purchaseTable);
                                }
                                calculatePurchaseFilteredTotal();
                                forceCSSRecalculation();
                            }, 150);
                        }
                    } else {
                        // Even if no table, force CSS recalculation
                        forceCSSRecalculation();
                    }
                }, 50);
            }

            // Window resize handler with throttling - multiple listeners for reliability
            $(window).off('resize.purchase').on('resize.purchase', handlePurchaseResize);

            // Also add native window resize listener (remove old one if exists, then add new)
            if (window.purchaseResizeHandler) {
                window.removeEventListener('resize', window.purchaseResizeHandler);
            }
            window.purchaseResizeHandler = handlePurchaseResize;
            window.addEventListener('resize', window.purchaseResizeHandler, {
                passive: true
            });

            // Orientation change handler
            $(window).off('orientationchange.purchase').on('orientationchange.purchase', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handlePurchaseResize();
                }, 300);
            });

            // Also add native orientation change listener
            window.addEventListener('orientationchange', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handlePurchaseResize();
                }, 500);
            });

            // MatchMedia listeners for all breakpoint changes
            const queries = [
                window.matchMedia('(max-width: 575.98px)'),
                window.matchMedia('(min-width: 576px) and (max-width: 767.98px)'),
                window.matchMedia('(min-width: 768px) and (max-width: 1024px)'),
                window.matchMedia('(min-width: 1025px)')
            ];

            queries.forEach(function(query) {
                // Modern browsers
                if (query.addEventListener) {
                    query.addEventListener('change', function() {
                        setTimeout(handlePurchaseResize, 100);
                    });
                }
                // Legacy browsers
                else if (query.addListener) {
                    query.addListener(function() {
                        setTimeout(handlePurchaseResize, 100);
                    });
                }
            });

            // Initial width set
            lastWidth = $(window).width();

            // Call on initial load to ensure correct state
            $(window).on('load', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handlePurchaseResize();
                }, 500);
            });

            // Also call after a short delay to ensure DOM is ready
            setTimeout(function() {
                if (purchaseTable) {
                    handlePurchaseResize();
                }
            }, 1000);

            // Make handler globally accessible for debugging
            window.handlePurchaseResize = handlePurchaseResize;

        });
        // const topScroll = document.querySelector('.table-scroll-top');
        // const tableResponsive = document.querySelector('.table-responsive');
        // const table = document.getElementById('order-table');

        // // Set top scrollbar inner div width to match table scroll width
        // function updateTopScrollbarWidth() {
        //     const topInnerDiv = topScroll.querySelector('div');
        //     topInnerDiv.style.width = table.scrollWidth + 'px';
        // }

        // // Update on load and window resize
        // window.addEventListener('load', updateTopScrollbarWidth);
        // window.addEventListener('resize', updateTopScrollbarWidth);

        // // Sync scrolling
        // topScroll.addEventListener('scroll', function() {
        //     tableResponsive.scrollLeft = topScroll.scrollLeft;
        // });
        // tableResponsive.addEventListener('scroll', function() {
        //     topScroll.scrollLeft = tableResponsive.scrollLeft;
        // });
        // $('#exportAllChallan').click(function() {
        //     let selectedYear = $('#filter-year').val() || '';
        //     let selectedMonth = $('#filter-month').val() || '';
        //     let selectedDate = $('#filter-date').val() || '';
        //     let selectedVendorId = $('#filter-customer').val() || '';
        //     const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
        //     let authToken = localStorage.getItem("authToken");

        //     let url =
        //         `/api/export-purchase?year=${selectedYear}&month=${selectedMonth}&date=${selectedDate}&customer_id=${selectedVendorId}&selectedSubAdminId=${selectedSubAdminId}`;

        //     $.ajax({
        //         url: url,
        //         method: "GET",
        //         headers: {
        //             "Authorization": "Bearer " + authToken
        //         },
        //         success: function(response) {
        //             if (response.status && response.file_url) {
        //                 // Trigger download
        //                 const link = document.createElement('a');
        //                 link.href = response.file_url;
        //                 link.download = response.file_name; // optional
        //                 document.body.appendChild(link);
        //                 link.click();
        //                 document.body.removeChild(link);
        //             } else {
        //                 Swal.fire({
        //                     icon: "warning",
        //                     title: "No Data Found",
        //                     text: response.message ||
        //                         "No purchase data available for selected filters."
        //                 });
        //             }
        //         },
        //         error: function(xhr) {
        //             // console.error("Export failed:", xhr.responseText);
        //             alert("Export failed. Please try again.");
        //         }
        //     });
        // });

        // $('#exportPdf').click(function() {
        //     let selectedYear = $('#filter-year').val() || '';
        //     let selectedMonth = $('#filter-month').val() || '';
        //     let selectedDate = $('#filter-date').val() || '';
        //     let selectedVendorId = $('#filter-customer').val() || '';
        //     const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
        //     let authToken = localStorage.getItem("authToken");

        //     $.ajax({
        //         url: `/api/export-purchase-pdf`,
        //         method: "GET",
        //         headers: {
        //             "Authorization": "Bearer " + authToken
        //         },
        //         data: {
        //             year: selectedYear,
        //             month: selectedMonth,
        //             date: selectedDate,
        //             customer_id: selectedVendorId,
        //             selectedSubAdminId: selectedSubAdminId
        //         },
        //         success: function(response) {
        //             if (response.status && response.file_url) {
        //                 // Download the PDF
        //                 let link = document.createElement('a');
        //                 link.href = response.file_url;
        //                 link.download = response.file_name || 'Purchases.pdf';
        //                 document.body.appendChild(link);
        //                 link.click();
        //                 document.body.removeChild(link);
        //             } else {
        //                 Swal.fire({
        //                     icon: "warning",
        //                     title: "No Data Found",
        //                     text: response.message ||
        //                         "No purchase data available for selected filters."
        //                 });
        //             }
        //         },
        //         error: function(xhr) {
        //             // console.error(xhr);
        //             alert("Failed to generate PDF. Please try again.");
        //         }
        //     });
        // });
        $(document).ready(function() {
            const $downloadLoader = $("#downloadLoaderOverlay");
            const $downloadLoaderText = $("#downloadLoaderText");
            const $downloadButtons = $(
                "#exportAllChallanDesktop, #exportAllChallanMobile, #exportPdfDesktop, #exportPdfMobile"
            );

            function toggleDownloadLoader(isLoading, message) {
                if (isLoading) {
                    $downloadLoaderText.text(message || "Generating report...");
                    $downloadLoader.removeClass("d-none");
                    $downloadButtons.prop("disabled", true).addClass("disabled").attr("aria-disabled", "true");
                } else {
                    $downloadLoader.addClass("d-none");
                    $downloadButtons.prop("disabled", false).removeClass("disabled").removeAttr(
                        "aria-disabled");
                }
            }

            // Function to handle Excel export
            function handleExcelExport() {
                let selectedYear = $('#filter-year').val() || '';
                let selectedMonth = $('#filter-month').val() || '';
                let selectedDate = $('#filter-date').val() || '';
                let selectedVendorId = $('#filter-customer').val() || '';
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                let authToken = localStorage.getItem("authToken");

                let url =
                    `/api/export-purchase?year=${selectedYear}&month=${selectedMonth}&date=${selectedDate}&customer_id=${selectedVendorId}&selectedSubAdminId=${selectedSubAdminId}`;

                $.ajax({
                    url: url,
                    method: "GET",
                    beforeSend: function() {
                        toggleDownloadLoader(true, "Generating Excel...");
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        if (response.status && response.file_url) {
                            const link = document.createElement('a');
                            link.href = response.file_url;
                            link.download = response.file_name;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        } else {
                            Swal.fire({
                                icon: "warning",
                                title: "No Data Found",
                                text: response.message ||
                                    "No purchase data available for selected filters."
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: "error",
                            title: "Export Failed",
                            text: "Failed to export data. Please try again."
                        });
                    },
                    complete: function() {
                        toggleDownloadLoader(false);
                    }
                });
            }

            // Function to handle PDF export
            function handlePdfExport() {
                let selectedYear = $('#filter-year').val() || '';
                let selectedMonth = $('#filter-month').val() || '';
                let selectedDate = $('#filter-date').val() || '';
                let selectedVendorId = $('#filter-customer').val() || '';
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                let authToken = localStorage.getItem("authToken");

                $.ajax({
                    url: `/api/export-purchase-pdf`,
                    method: "GET",
                    beforeSend: function() {
                        toggleDownloadLoader(true, "Generating PDF...");
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    data: {
                        year: selectedYear,
                        month: selectedMonth,
                        date: selectedDate,
                        customer_id: selectedVendorId,
                        selectedSubAdminId: selectedSubAdminId
                    },
                    success: function(response) {
                        if (response.status && response.file_url) {
                            let link = document.createElement('a');
                            link.href = response.file_url;
                            link.download = response.file_name || 'Purchases.pdf';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        } else {
                            Swal.fire({
                                icon: "warning",
                                title: "No Data Found",
                                text: response.message ||
                                    "No purchase data available for selected filters."
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: "error",
                            title: "Export Failed",
                            text: "Failed to generate PDF. Please try again."
                        });
                    },
                    complete: function() {
                        toggleDownloadLoader(false);
                    }
                });
            }

            // Bind desktop export buttons
            $('#exportAllChallanDesktop, #exportAllChallanMobile').off('click').on('click', function(e) {
                e.preventDefault();
                handleExcelExport();
            });

            // Bind PDF export buttons
            $('#exportPdfDesktop, #exportPdfMobile').off('click').on('click', function(e) {
                e.preventDefault();
                handlePdfExport();
            });
        });
    </script>
@endpush
