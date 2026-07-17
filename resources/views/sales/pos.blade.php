@extends('layout.app')

@section('title', 'Add Sale')

@section('content')
    <style>
        .setvaluecash ul li a {
            border: 1px solid #e9ecef;
            color: #000;
            font-size: 12px;
            font-weight: 600;
            min-height: 59px;
            border-radius: 5px;
            padding: 8px 7px;
        }

        .disabled-product {
            opacity: 0.6;
            pointer-events: none;
            /* disables clicking inside */
        }

        .paymentmethod.active {
            /* background-color: #1b2850; */
            color: white;
            /* border-radius: 5px; */
        }

        .paymentmethod.active:hover {
            /* background-color: white; */
            color: white;
            /* Dark text for contrast */
            /* border: 1px solid #1b2850; */
            /* Optional: border to define shape */
        }

        .tabs_wrapper ul.tabs {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            padding: 0;
            margin: 0;
            list-style: none;
            scrollbar-width: thin;
            width: auto;
            /* scrollbar-color: #ff9f43 #f1f1f1; */
        }

        .tabs_wrapper ul.tabs::-webkit-scrollbar {
            display: none;
        }

        .tabs_wrapper ul.tabs li {
            flex: 0 0 auto;
            cursor: pointer;
            white-space: nowrap;
            width: auto;
            /* ðŸ‘ˆ Prevent tab name from wrapping */
            padding: 0;
        }

        .product-details {
            background: #fff;
            padding: 10px 16px;
            box-shadow: none;
            border: none !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: 0.3s ease;
            min-width: max-content;
            /* ðŸ‘ˆ Ensure box width fits content */
        }

        .product-details h6 {
            font-size: 14px;
            color: #000;
            margin: 0;
            text-align: center;
            white-space: nowrap;
            /* ðŸ‘ˆ Prevent text wrap */
            overflow: hidden;
            text-overflow: ellipsis;
            /* ðŸ‘ˆ Optional: show ... if too long */
            max-width: 100%;
            /* ðŸ‘ˆ Avoid overflow outside parent */
        }

        .tabs_wrapper ul.tabs li.active .product-details {
            background: transparent;
            /* border-bottom: 2px solid #ff9f43 !important; */
        }

        .tabs_wrapper ul.tabs li.active .product-details h6 {
            color: #ff9f43;
        }

        .payment_panel {
            position: fixed;
            bottom: 0;
            background-color: white;
            width: 38%;
        }

        .page-wrapper .content {
            padding-bottom: 220px !important;
        }

        .body_space {
            /*margin-bottom: 2rem;*/
        }

        .paymentmethod.active {
            background-color: #1b2850;
            color: #ffffff;
        }

        .paymentmethod.active svg {
            fill: #ffffff;
        }

        .productsetimgin {
            height: 100px;
            object-fit: contain;
            margin: 1rem auto 0;
            display: block;
        }

        .productsetbtn button {
            display: none;
        }

        .search_custom_product {
            width: 100%;
        }

        .scanner-search-wrap {
            width: 100%;
        }

        .scanner-search-wrap .header-search {
            margin-bottom: 4px !important;
        }

        .mobile-scanner-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            line-height: 1.2;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            padding: 3px 8px;
            background: #ffffff;
            color: #6b7280;
            min-height: 22px;
        }

        .mobile-scanner-status::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.8;
        }

        .mobile-scanner-status.is-connected {
            color: #166534;
            border-color: #86efac;
            background: #f0fdf4;
        }

        .mobile-scanner-status.is-disconnected {
            color: #6b7280;
            border-color: #d1d5db;
            background: #ffffff;
        }

        .mobile-scanner-status.is-checking {
            color: #475569;
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .payment_panel {
            z-index: 999;
        }

        .price {
            display: flex;
            flex-direction: column;
            width: 180px;
            font-size: 12px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .sub-total span {
            color: #ff9f43;
        }

        .gst-inc span {
            color: #007bff;
        }

        .discount span {
            color: red;
        }

        .final-total {
            font-weight: bold;
            margin-top: 4px;
            padding-top: 3px;
            border-top: 1px solid #ddd;
        }

        .final-total span {
            color: green;
        }

        .responsive-mobile-view-1 {
            display: none !important;
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
            height: 30px;
        }

        .pos-back-btn:hover {
            background: #ff9f43;
            color: #fff;
        }

        .pos-back-btn i {
            font-size: 12px;
            line-height: 1;
        }

        .pos-top-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            width: 100%;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .pos-top-controls-left {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .pos-gst-options {
            display: inline-flex;
            align-items: center;
            gap: 14px;
        }

        .pos-gst-options .custom-radio-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            white-space: nowrap;
            font-size: 14px;
        }

        .pos-quotation-toggle {
            margin: 0;
            padding-left: 0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .pos-quotation-toggle .form-check-input {
            margin: 0;
            margin-left: 0;
            float: none;
        }

        .pos-quotation-toggle .form-check-label {
            margin: 0;
            font-weight: 500;
        }
        .card .card-body {
            padding: 20px;
        }
        .tabs_container {
            max-height: calc(100vh - 230px);
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 5px;
        }

        .tabs_container::-webkit-scrollbar {
            width: 6px;
        }

        .tabs_container::-webkit-scrollbar-track {
            background: #f1f1f1; 
            border-radius: 4px;
        }

        .tabs_container::-webkit-scrollbar-thumb {
            background: #c1c1c1; 
            border-radius: 4px;
        }

        .tabs_container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8; 
        }

        .tabs_container.card .card-body {
            padding-bottom: 180px;
        }

        .card-order .card-body {
            padding-bottom: 20px;
        }

        /* ── Payment Panel Drag Handle ── */
        .payment-panel-handle {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 6px 12px 4px;
            cursor: grab;
            background: linear-gradient(to bottom, #f0f2f5, #e8eaf0);
            border-radius: 12px 12px 0 0;
            border-bottom: 1px solid #d8dce6;
            user-select: none;
            position: relative;
        }
        .payment-panel-handle:active { cursor: grabbing; }
        .handle-pill {
            width: 40px;
            height: 4px;
            background: #b0b8cc;
            border-radius: 99px;
            flex-shrink: 0;
        }
        .handle-hint {
            font-size: 10px;
            color: #8892a4;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }
        .panel-min-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #e2e6ed;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #555;
            transition: background 0.2s;
            padding: 0;
        }
        .panel-min-btn:hover { background: #cdd3dd; }
        #paymentPanelBody {
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        #paymentPanelBody.panel-collapsed {
            max-height: 0 !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background: #ff9f43;
    color: #fff !important;
}

        @media screen and (max-width: 992px) {
            .pos-top-controls {
                align-items: flex-start;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 992px) {
            .tabs_container .row {
                --bs-gutter-x: 12px;
                --bs-gutter-y: 12px;
            }

            .tabs_container .row > .col-lg-3.d-flex.position-relative {
                padding-left: calc(var(--bs-gutter-x) * 0.5);
                padding-right: calc(var(--bs-gutter-x) * 0.5);
            }

            .tabs_container .productset {
                width: 100%;
                min-height: 100%;
            }

            .payment_panel {
                position: fixed;
                left: 16px;
                right: 16px;
                bottom: 60px;
                width: auto;
                margin-left: 0;
                border-radius: 14px 14px 0 0;
                background-color: white;
                overflow: hidden;
                box-sizing: border-box;
            }

            .card-order .card-body {
                padding-bottom: 20px;
            }
        }

        @media screen and (min-width: 993px) and (max-width: 1199px) {
            .tabs_container .row > .col-lg-3.d-flex.position-relative {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }

            .tabs_container .productset {
                min-height: 100%;
            }

            .payment_panel {
                position: fixed;
                right: 16px;
                left: auto;
                bottom: 220px;
                width: min(360px, calc(50vw - 32px));
                margin-left: 0;
                border-radius: 14px;
                background-color: white;
                overflow: visible;
                box-sizing: border-box;
                z-index: 999;
            }

            .payment_panel .setvaluecash {
                display: block !important;
                margin: 0 0 12px;
            }

            .payment_panel .setvaluecash ul {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin: 0;
                padding: 0;
            }

            .payment_panel .setvaluecash ul li {
                width: calc(33.33% - 8px);
                margin: 0;
                list-style: none;
            }

            .payment_panel .setvaluecash ul li a {
                min-height: 38px;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 4px;
                padding: 4px 8px;
                text-align: center;
                font-size: 11px;
            }

            .payment_panel .setvaluecash ul li a i {
                font-size: 14px !important;
                margin-bottom: 0 !important;
            }

            .payment_panel .btn-totallabel {
                margin: 0;
            }

            .card-order .card-body {
                padding-bottom: 20px;
            }
        }

        @media screen and (device-width: 1024px) and (device-height: 1366px) and (orientation: portrait) {
            .tabs_container .row {
                --bs-gutter-x: 12px;
                --bs-gutter-y: 12px;
            }

            .tabs_container .row > .col-lg-3.d-flex.position-relative {
                flex: 0 0 50%;
                max-width: 50%;
            }

            /* iPad Pro portrait: payment panel sits at the bottom of the right column,
               not floating over the totals */
            .payment_panel {
                position: static !important;
                width: 100% !important;
                bottom: auto !important;
                right: auto !important;
                left: auto !important;
                border-radius: 0 !important;
                margin-top: 12px;
            }

            .card-order .card-body {
                padding-bottom: 20px !important;
            }
        }

        /* iPad Pro landscape (1366x1024) */
        @media screen and (device-width: 1366px) and (device-height: 1024px) and (orientation: landscape) {
            .payment_panel {
                position: static !important;
                width: 100% !important;
                bottom: auto !important;
                right: auto !important;
                left: auto !important;
                border-radius: 0 !important;
                margin-top: 12px;
            }

            .card-order .card-body {
                padding-bottom: 20px !important;
            }
        }

        /* iPad Pro viewport-based fix (1024px wide, portrait) — covers modern browsers
           where device-width queries may not fire */
        @media screen and (min-width: 1024px) and (max-width: 1024px) {
            .payment_panel {
                position: static !important;
                width: 100% !important;
                bottom: auto !important;
                right: auto !important;
                left: auto !important;
                border-radius: 8px !important;
                margin-top: 12px;
                box-shadow: 0 -2px 8px rgba(0,0,0,0.06);
            }

            .card-order .card-body {
                padding-bottom: 20px !important;
            }
        }

        @media screen and (max-width: 767px) {
            .page-wrapper .content {
                padding-bottom: 260px !important;
            }

            .payment_panel {
                position: fixed;
                left: 0px;
                right: 0px;
                bottom: 50px;
                background-color: white;
                width: auto;
                margin-left: 0;
                border-radius: 14px 14px 0 0;
                /* box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12); */
                overflow: hidden;
            }
            .gst-info {
                margin: 4px 0 !important;
                padding: 3px 8px !important;
                background: #f8f9fa !important;
                border-radius: 4px !important;
                border-left: 3px solid #4caf50 !important;
                width:80% !important;
            }
            .product-lists .gst-no-message {
            margin: 4px 0 !important;
            padding: 6px 8px !important;
            background: #fff8e1 !important;
            border-left: 3px solid #ff9f43 !important;
            border-radius: 4px !important;
            color: #640a06 !important;
            font-size: 12px !important;
            line-height: 1.4 !important;
            width: 80% !important;
        }

            .responsive-mobile-view {
                display: none !important;
            }

            .responsive-mobile-view-1 {
                display: block !important;
            }


            .productset {
                display: flex;
            }

            .productset .productsetimg {

                width: 30%;
            }

            .productsetimgin {
                object-fit: cover;
                height: 90px;
                margin: 0;
                max-width: 110px;
            }

            .productsetcontent {
                text-align: left !important;
            }



            .productsetbtn {
                position: absolute;
                right: 10px;
                top: 43px;
            }

            .productsetbtn button {
                display: block;
                width: 100%;
                height: 33px;
                background-color: #1b2850;
                color: white !important;
                border: none;
                border-radius: 5px;
            }

            .productsetbtn button:active {
                color: white;
            }

            .product-lists {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                grid-template-areas:
                    "details delete"
                    "discount discount"
                    "price price";
                align-items: flex-start;
                position: relative;
                overflow: hidden;
                gap: 10px;
                padding: 10px 42px 10px 0;
            }

            .product-lists>li:first-child {
                grid-area: details;
                min-width: 0;
            }

            .product-lists>li:nth-child(2) {
                grid-area: discount;
                width: 100%;
            }

            .product-lists>li.price {
                grid-area: price;
                width: 100%;
                margin-top: 0;
                padding: 8px 10px;
                border-radius: 10px;
                background: #f8f9fa;
            }

            .product-lists>li.delete-col {
                grid-area: delete;
                width: 32px;
                margin-left: 0;
                text-align: right;
                flex-shrink: 0;
                position: absolute;
                top: 110px;
                right: 0px;
                z-index: 2;
            }

            .productimg {
                display: flex;
                align-items: flex-start;
                gap: 5px;
            }

            .productimgs {
                flex: 0 0 72px;
            }

            .productimgs img {
                width: 72px;
                height: 72px;
                object-fit: cover;
                border-radius: 10px;
            }

            .productcontet {
                flex: 1 1 auto;
                min-width: 0;
            }

            .productcontet h4 {
                max-width: none;
                margin-bottom: 8px;
            }

            .increment-decrement {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }

            .increment-decrement .input-groups {
                width: 60%;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
            }
            .increment-decrement .input-groups input[type=button] {
                width: 75px;
                height: 25px;
            }

            .increment-decrement .quantity-field {
                flex: 1 1 auto;
                width: 100% !important;
                min-width: 0;
            }

            .increment-decrement>div:last-child {
                order: 0;
                margin: 0 !important;
                width: 100%;
                padding-left: 40px;
                padding-right: 46px;
            }

            .increment-decrement .product-price-input {
                width: 100%;
            }

            .price {
                width: 100%;
            }

            .price-row {
                margin-bottom: 4px;
            }

            .product-table {
                max-height: none;
                overflow: visible;
                -ms-overflow-style: none;
                scrollbar-width: none;
                padding-bottom: 8px;
            }

            .product-table::-webkit-scrollbar {
                display: none;
            }

            /* .body_space_two {
                                margin-bottom: 4rem;
                            } */

            a.confirm-text.remove-item {
                /* font-size: 116px; */
                margin: 6px;
            }


            .mobile-top-options {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                background: #ffffff;
                padding: 10px 15px;
                z-index: 9999;
                border-bottom: 1px solid #eee;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            /* Give space below so content not hide */
            .card-order .card-body {
                margin-top: 10px;
                padding: 12px;
            }

            /* Make radio inline properly */
            .mobile-top-options label {
                font-size: 13px;
                font-weight: 600;
                margin-right: 10px;
            }

            .mobile-top-options input[type="radio"],
            .mobile-top-options input[type="checkbox"] {
                margin-right: 4px;
            }

            .totalitem {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                flex-wrap: wrap;
                margin-bottom: 10px;
            }

            .product-discount-box {
                width: 100%;
                gap: 10px;
                margin-top: 0;
            }

            .discount-field {
                flex: 1 1 0;
            }

            .product-discount-percentage,
            .product-discount-amount {
                width: 100%;
                min-width: 145px;
            }

            .price-row {
                gap: 10px;
            }

            .price-row span:last-child {
                text-align: right;
            }

            .setvalue {
                padding: 0 0 16px 0 !important;
            }

            .setvalue ul li {
                padding: 8px 0;
            }

            .setvaluecash ul {
                display: flex;
                flex-wrap: nowrap;
                gap: 4px;
                margin: 0;
                padding: 0 10px 10px;
                overflow-x: auto;
            }

            .setvaluecash ul::-webkit-scrollbar {
                display: none;
            }

            .setvaluecash ul li {
                margin: 0;
                flex: 1;
                width: auto !important;
            }

            .setvaluecash ul li a {
                min-height: 40px;
                height: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 2px;
                text-align: center;
                padding: 4px 6px;
                font-size: 10px;
            }

            .setvaluecash ul li a i {
                font-size: 14px !important;
                margin-bottom: 0 !important;
            }

            #posPaidTypeBox,
            #cashOnlineBox,
            #bankSelectionBox {
                padding: 0 12px 12px;
            }

            .btn-totallabel {
                margin: 0;
            }

        }

        .gst-info {
            margin: 4px 0;
            padding: 3px 8px;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #4caf50;
            width:135px;
        }

        .product-lists .gst-info small {
            display: block;
            line-height: 1.3;
        }

        .product-lists .gst-no-message {
            margin: 4px 0;
            padding: 6px 8px;
            background: #fff8e1;
            border-left: 3px solid #ff9f43;
            border-radius: 4px;
            color: #640a06;
            font-size: 12px;
            line-height: 1.4;
            width: 135px;
        }

        .productset .productsetimg .gst-hover-badge {
            position: absolute;
            color: #fff;
            font-size: 10px;
            padding: 5px;
            border-radius: 5px;
            top: 55px;
            right: 20px;
            transform: translatey(-100px);
            transition: all .5s;
            z-index: 11;
            font-weight: 600;
        }

        .productset:hover .productsetimg .gst-hover-badge {
            transform: translatey(0);
        }

        .gst-hover-badge.with-gst {
            background: #4caf50;
        }

        .gst-hover-badge.no-gst {
            background: #9e9e9e;
        }

        .product-discount-box {
            display: flex;
            gap: 12px;
            align-items: flex-end;
            margin-top: 6px;
            /* padding-right: 20px; */
        }

        .discount-field {
            display: flex;
            flex-direction: column;
        }

        .discount-field label {
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 3px;
            color: #444;
        }

        .product-discount-percentage,
        .product-discount-amount {
            width: 50px;
            height: 30px;
            padding: 4px 6px;
            font-size: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #f9f9f9;
            text-align: center;
        }

        .product-discount-percentage:focus,
        .product-discount-amount:focus {
            border-color: #ff9f43;
            background: #fff;
            box-shadow: 0 0 3px rgba(255, 159, 67, 0.3);
        }

        .product-price-input {
            width: 90px;
            height: 34px;
            padding: 4px 8px;
            font-size: 14px;
            font-weight: 700;
            border: 1px solid #7a7979;
            border-radius: 5px;
            background: #fff;
            text-align: center;
        }

        .product-price-input:focus {
            outline: none;
            border-color: #ff9f43;
            box-shadow: 0 0 0 2px rgba(255, 159, 67, 0.15);
        }

        .product-lists {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 4px 10px 4px 0;
            border-bottom: 1px solid #eee;
            /* width: 600px; */
            gap: 5px;
        }

        .product-lists>li {
            list-style: none;
        }

        .product-lists li:last-child {
            width: 40px;
            text-align: center;
            flex-shrink: 0;
        }

        .remove-item img {
            width: 25px;
            height: 25px;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .remove-item img:hover {
            transform: scale(1.1);
            opacity: 0.7;
        }

        .setvalue ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .product-table {
            max-height: 400px;
            overflow: auto;
        }

        .product-table::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .product-table::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .product-table::-webkit-scrollbar-thumb {
            background: #FF9F43;
            border-radius: 4px;
        }

        .product-table::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .setvalue ul li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            border-bottom: 1px dashed #eee;
        }

        .setvalue ul li:last-child {
            border-bottom: none;
        }

        .setvalue h5 {
            font-size: 14px;
            font-weight: 500;
            margin: 0;
            color: #555;
        }

        .setvalue h6 {
            font-size: 15px;
            font-weight: 600;
            margin: 0;
            color: #222;
        }

        .setvalue {
            padding: 0 0 40px 0 !important;
        }

        .setvalue .total-value h6 {
            font-size: 18px;
            font-weight: 700;
            color: #28a745;
        }

        /* Fix customer row layout */
        .row.select-group.w-100 {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            gap: 15px;
            /* space between boxes */
            margin: 0;
        }

        /* Ensure both columns use equal width */
        .row.select-group .col-md-6 {
            flex: 1;
            max-width: 50%;
            padding: 0;
        }

        /* Remove unnecessary nested width conflicts */
        .select-split,
        .select-group {
            width: 100%;
        }

        /* Improve input/select appearance */
        #customer_name,
        #customer_phone,
        #order_date,
        #order_date_display {
            width: 100%;
        }

        .pos-order-date-group .input-groupicon {
            position: relative;
        }

        .pos-order-date-group .addonset {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 2;
        }

        .pos-order-date-group .addonset img {
            width: 16px;
            height: 16px;
        }

        #order_date_display {
            padding-right: 38px;
            background-color: #fff;
            cursor: pointer;
        }

        /* Fix for labour items select */
        .select2-labour+.select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding-top: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 29px !important;
        }

        #qr-reader {
            min-height: 300px;
            width: 100%;
            background: #f5f5f5;
            /* optional: shows a background while loading */
        }

        .productcontet h4 {
            white-space: normal !important;
            word-break: break-word;
            overflow-wrap: break-word;
            font-size: 13px;
            line-height: 1.3;
            max-width: 150px;
        }
        /* ===== POS PAYMENT PANEL FIELDS FIX ===== */
#posPaidTypeBox,
#cashOnlineBox,
#bankSelectionBox,
#emiBox {
    width: 100%;
    box-sizing: border-box;
}

#posPaidTypeBox .form-group,
#cashOnlineBox .form-group,
#bankSelectionBox .form-group,
#emiBox .form-group {
    display: flex;
    flex-direction: column;
    width: 100%;
    margin-bottom: 10px;
}

#posPaidTypeBox .form-group label,
#cashOnlineBox .form-group label,
#bankSelectionBox .form-group label,
#emiBox .form-group label {
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #444;
    width: 100%;
}

#posPaidTypeBox .form-group select,
#posPaidTypeBox .form-group input,
#cashOnlineBox .form-group input,
#bankSelectionBox .form-group select,
#emiBox .form-group input,
#emiBox .form-group select {
    width: 100% !important;
    box-sizing: border-box;
    height: 36px;
    font-size: 13px;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 4px 10px;
    background: #fff;
}

#posPaidAmountFields {
    width: 100%;
}

#cashOnlineBox .row,
#bankSelectionBox .row,
#emiBox .row {
    margin-left: 10px;
    margin-right: 5px;
}

#cashOnlineBox .row > [class*="col-"],
#bankSelectionBox .row > [class*="col-"],
#emiBox .row > [class*="col-"] {
    padding-left: 6px;
    padding-right: 6px;
}

#emiBox {
    max-height: 190px;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 8px 6px 0 0;
}

#emiBox::-webkit-scrollbar {
    width: 5px;
}

#emiBox::-webkit-scrollbar-thumb {
    background: #d7dde8;
    border-radius: 10px;
}

.bank-label-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 4px;
}

.bank-add-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    border: 1px solid #ff9f43;
    background: #fff7ed;
    color: #ff9f43;
    border-radius: 4px;
    padding: 3px 10px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.2;
    text-decoration: none;
}

.bank-add-btn:hover {
    color: #fff;
    background: #ff9f43;
}

#posPaidAmountFields .form-group {
    display: flex;
    flex-direction: column;
    width: 100%;
    margin-bottom: 10px;
}

#posPaidAmountFields .form-group label {
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #444;
}

#posPaidAmountFields .form-group input {
    width: 100% !important;
    box-sizing: border-box;
    height: 36px;
    font-size: 13px;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 4px 10px;
    background: #fff;
}

#posPendingAmount {
    background: #f8f9fa !important;
    color: #555;
}

/* Readonly/computed fields */
#posPaidAmount[readonly],
#posCashOnlineOnlineAmount[readonly] {
    background: #f8f9fa !important;
    color: #555;
    cursor: not-allowed;
}
/* ===== POS PAYMENT PANEL - TWO COLUMN LAYOUT FOR FIELDS ===== */

/* Make the payment panel containers use grid layout */
.payment_panel #posPaidTypeBox,
.payment_panel #cashOnlineBox,
.payment_panel #bankSelectionBox,
.payment_panel #emiBox {
    display: block;
    width: 100%;
}

/* Apply grid to form groups inside payment boxes */
.payment_panel #posPaidTypeBox .form-group,
.payment_panel #cashOnlineBox .form-group,
.payment_panel #bankSelectionBox .form-group,
.payment_panel #emiBox .form-group {
    display: block;
    width: 100%;
    margin-bottom: 12px;
}

/* For the Paid Type Box - keep select full width, but amount fields in 2 columns */
#posPaidTypeBox #posPaidAmountFields {
    display: grid;
    /* grid-template-columns: repeat(2, 1fr); */
    gap: 12px;
    margin-top: 8px;
}

#posPaidTypeBox #posPaidAmountFields .form-group {
    margin-bottom: 0;
}

#posPaidTypeBox #posPaidAmountFields .form-group:first-child {
    grid-column: 1 / 2;
}

#posPaidTypeBox #posPaidAmountFields .form-group:last-child {
    grid-column: 2 / 3;
}

/* For Cash+Online box - cash and online amounts in 2 columns */
/* #cashOnlineBox {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-top: 10px;
} */

#cashOnlineBox .form-group {
    margin-bottom: 0;
}

/* For Bank Selection box - keep full width (but can be full width) */
#bankSelectionBox .form-group {
    width: 100%;
}

/* Make all inputs inside payment panel have consistent styling */
.payment_panel input,
.payment_panel select {
    width: 100% !important;
    box-sizing: border-box;
}

.payment-detail-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 8px;
}

.payment-detail-cancel {
    border: 1px solid #dc3545;
    background: #fff;
    color: #dc3545;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1;
    padding: 8px 12px;
}

.payment-detail-cancel:hover {
    background: #dc3545;
    color: #fff;
}

/* Responsive adjustments for mobile */
@media screen and (max-width: 767px) {
    /* On mobile, stack them vertically */
    #posPaidTypeBox #posPaidAmountFields {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    #cashOnlineBox {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .bank-label-row {
        align-items: flex-start;
        /* flex-direction: column; */
        gap: 6px;
    }

    #posPaidTypeBox #posPaidAmountFields .form-group:first-child,
    #posPaidTypeBox #posPaidAmountFields .form-group:last-child {
        grid-column: auto;
    }

    /* Mobile validation message styling */
    .error_total {
        display: block !important;
        width: 100% !important;
        min-height: auto !important;
        height: auto !important;
        /* padding: 12px !important; */
        margin-bottom: 15px !important;
        margin-top: 10px !important;
        box-sizing: border-box !important;
        line-height: 1.4 !important;
        white-space: normal !important;
        word-wrap: break-word !important;
    }
}

/* For tablet devices */
@media screen and (min-width: 768px) and (max-width: 1024px) {
    #posPaidTypeBox #posPaidAmountFields {
        /* grid-template-columns: repeat(2, 1fr); */
        gap: 15px;
    }

    #cashOnlineBox {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .pos-customer-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .pos-customer-row > .pos-customer-field {
        width: 100%;
        max-width: none;
        padding: 0;
        flex: none;
    }

    .pos-customer-row > .pos-customer-field--date {
        grid-column: 1 / -1;
    }
}
    </style>
    <div class="content">
        @php
            $isQuotationMode      = request('sale_type') === 'quotation';
            $isAdvanceReceiptMode  = request('sale_type') === 'advance_receipt';
            $showNewBillModal      = request('new_bill') == 1 && !request()->has('sale_type');
        @endphp
        <input type="hidden" id="quotation_status" name="quotation_status"
            value="{{ $isQuotationMode ? 'quotation' : ($isAdvanceReceiptMode ? 'advance_receipt' : 'sales') }}">

        <div class="page-header">
            <div class="page-title">
                <h4 id="posPageTitle">{{ $isQuotationMode ? 'Add Quotation' : ($isAdvanceReceiptMode ? 'Add Advance Receipt' : 'Add Sale') }}</h4>
            </div>
            <div class="page-btn">
                 <a href="{{ route('sales.list') }}" class="pos-back-btn">
                                <i class="fa-solid fa-arrow-left"></i>
                                Back
                            </a>
            </div>
        </div>

        @if ($showNewBillModal)
            <div class="modal fade" id="newBillTypeModal" tabindex="-1" aria-labelledby="newBillTypeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-body text-center py-4 px-3">
                            <h3 id="newBillTypeModalLabel" class="mb-3" style="font-size: 18px; font-weight: 700;">
                                Create New Bill
                            </h3>
                            <p class="mb-4" style="font-size: 14px; color: #555;">
                                Choose bill type for this new entry.
                            </p>
                            <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                                <a href="{{ route('sales.add', ['sale_type' => 'sales']) }}"
                                    class="btn px-4 py-2 text-white"
                                    style="background-color:#22c55e; border-color:#22c55e;">Sales</a>
                                <a href="{{ route('sales.add', ['sale_type' => 'quotation']) }}"
                                    class="btn px-4 py-2 text-white"
                                    style="background-color:#ff9f43; border-color:#ff9f43;">Quotation</a>
                                <a href="{{ route('sales.add', ['sale_type' => 'advance_receipt']) }}"
                                    class="btn px-4 py-2 text-white"
                                    style="background-color:#7367f0; border-color:#7367f0;">Advance Receipt</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- IMEI Modal --}}
        <div class="modal fade" id="imeiModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Select IMEI Number</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6 id="imeiProductName" class="mb-3"></h6>
                        <select id="imeiNumberInput" class="form-control">
                            <option value="">Select IMEI number</option>
                        </select>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-warning text-white" id="confirmImeiBtn">Add</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="addBankForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addBankModalLabel">Add Bank</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close">x</button>
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
                                    <input type="text" class="form-control" id="add_account_number"
                                        name="account_number">
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
                            <button type="submit" class="btn btn-submit text-white" id="saveBankBtn"
                                style="background-color: #ff9f43;">Save Bank</button>
                            <button type="button" class="btn btn-secondary btn-cancel"
                                data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3  responsive-mobile-view-1">

            <!-- Left Side -->

            <!-- Center / Right Controls -->
            {{-- <div class="d-flex align-items-center gap-3"> --}}

            <div class=" d-flex justify-content-between ">

                <div class="form-check m-0">
                    <input class="form-check-input quotationToggle" type="checkbox" id="quotationToggle1" value="quotation"
                        {{ $isQuotationMode ? 'checked' : '' }}>
                    <label class="form-check-label" for="quotationToggle1">Quotation</label>
                </div>
                <div class="form-check m-0 ms-3">
                    <input class="form-check-input advanceReceiptToggle" type="checkbox" id="advanceReceiptToggle" value="advance_receipt" {{ $isAdvanceReceiptMode ? 'checked' : '' }}>
                    <label class="form-check-label" for="advanceReceiptToggle" style="color:#000;">Advance Receipt</label>
                </div>
                <div class="d-flex gap-3">
    <label class="custom-radio-label m-0">
        <input type="radio" name="gst_option_mobile" id="without_gst_mobile" value="without" checked>
        Without GST
    </label>
    <label class="custom-radio-label m-0">
        <input type="radio" name="gst_option_mobile" id="with_gst_mobile" value="with">
        With GST
    </label>
</div>

            </div>

        </div>

        <div class="row">
            <div class="col-lg-6 col-sm-12 tabs_wrapper">


                @php
                    $hasProducts = false; // start as false
                @endphp

                <ul class="tabs border-0 mb-4">
                    @foreach ($categories as $cat)
                        @php
                            $availableProducts = $cat->products;
                        @endphp

                        @if ($availableProducts->count() > 0)
                            @php
                                $hasProducts = true; // at least one product exists
                            @endphp
                            <li id="{{ $cat->id }}">
                                <div class="product-details">
                                    <h6 style="text-transform: capitalize;">{{ $cat->name }}</h6>
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>

                @if (!$hasProducts)
                    <div class="product-details text-center">
                        <h6>No data available</h6>
                        <a href="{{ url('add-product') }}" class="btn btn-primary mt-2">
                            + Add Product
                        </a>
                    </div>
                @endif
                <div class="tabs_container">

                </div>
            </div>
            <div class="col-lg-6 col-sm-12 ">

                <div class="card card-order">
                    <div class="card-body body_space">

                        @php
                            $user = auth()->user();
                        @endphp
                        @php
                            $subAdminId = session('selectedSubAdminId');
                            // Ensure $role is set - use from controller if available, otherwise get from auth
                            if (empty($role ?? '')) {
                                $role = Auth::user()->role ?? '';
                            }
                        @endphp
                        <div class="pos-top-controls responsive-mobile-view">
                            <div class="pos-top-controls-left">
                                @if ($role == 'admin' || $role == 'staff' || $role == 'sub-admin')
                                    <div class="form-check pos-quotation-toggle">
                                        <input class="form-check-input quotationToggle" type="checkbox" id="quotationToggle2"
                                            value="quotation" {{ $isQuotationMode ? 'checked' : '' }}>
                                        <label class="form-check-label" for="quotationToggle2">Quotation</label>
                                    </div>
                                @endif
                                <div class="form-check pos-quotation-toggle ms-3 me-3">
                                    <input class="form-check-input advanceReceiptToggle" type="checkbox" id="advanceReceiptToggle2" value="advance_receipt" {{ $isAdvanceReceiptMode ? 'checked' : '' }}>
                                    <label class="form-check-label" for="advanceReceiptToggle2" style="color:#000;">Advance Receipt</label>
                                </div>
                                <div class="pos-gst-options">
                                    <label class="custom-radio-label">
                                        <input type="radio" name="gst_option" id="without_gst" value="without" checked />
                                        Without GST
                                    </label>

                                    <label class="custom-radio-label">
                                        <input type="radio" name="gst_option" id="with_gst" value="with" />
                                        With GST
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="row">

                            <div class="col-lg-12">
                                <div class="scanner-search-wrap me-3 mb-3 search_custom_product">
                                    <div class="header-search d-flex align-items-center position-relative">
                                        <!-- Scanner Button (New) -->
                                        <button type="button" id="scanBarcodeBtn" class="btn btn-sm"
                                            style="background: #f0f0f0; border: 1px solid #ccc; border-radius: 4px; padding: 6px 8px; margin-right: 8px; color: #333; display: inline-flex; align-items: center; justify-content: center;min-width: 40px;height: 40px;"
                                            title="Scan Barcode">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                fill="currentColor" class="bi bi-qr-code-scan" viewBox="0 0 16 16">
                                                <path
                                                    d="M0 .5A.5.5 0 0 1 .5 0h3a.5.5 0 0 1 0 1H1v2.5a.5.5 0 0 1-1 0zm12 0a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0V1h-2.5a.5.5 0 0 1-.5-.5M.5 12a.5.5 0 0 1 .5.5V15h2.5a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H15v-2.5a.5.5 0 0 1 .5-.5M4 4h1v1H4z" />
                                                <path d="M7 2H2v5h5zM3 3h3v3H3zm2 8H4v1h1z" />
                                                <path d="M7 9H2v5h5zm-4 1h3v3H3zm8-6h1v1h-1z" />
                                                <path
                                                    d="M9 2h5v5H9zm1 1v3h3V3zM8 8v2h1v1H8v1h2v-2h1v2h1v-1h2v-1h-3V8zm2 2H9V9h1zm4 2h-1v1h-2v1h3zm-4 2v-1H8v1z" />
                                                <path d="M12 9h2V8h-2z" />
                                            </svg>
                                        </button>
                                        <!-- Search Icon and Input (existing) -->
                                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/search.svg' }}"
                                            alt="Search"
                                            style="position: absolute; left: 55px; width: 18px; height: 18px; z-index: 10; opacity: 0.6;">
                                        <input type="text" id="customerSearch1"
                                            class="form-control form-control-sm rounded px-3 ps-5"
                                            placeholder="Search..." autocomplete="off"
                                            style="height: 38px; font-size: 14px; padding-left: 42px;">
                                        <!-- Search Results (existing) -->
                                        <div id="searchResults1"
                                            class="list-group bg-white position-absolute rounded shadow mt-1 w-100"
                                            style="z-index: 1050; max-height: 300px; overflow-y: auto; display: none; top: 100%; left: 0;">
                                        </div>
                                    </div>
                                    <!-- <div id="mobileScannerStatus" class="mobile-scanner-status is-checking">
                                        Mobile scanner: checking...
                                    </div> -->
                                </div>

                                <!-- Barcode Scanner Modal -->
                                <div class="modal fade" id="barcodeScannerModal" tabindex="-1"
                                    aria-labelledby="barcodeScannerModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="barcodeScannerModalLabel">Scan Barcode</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">x</button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="qr-reader" style="width:100%; min-height:300px;"></div>
                                                <div id="scan-message" class="text-center mt-2 small text-muted">
                                                    Initializing camera...</div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <style>
                                    .pos-gst-input-wrap { position: relative; }
                                    .pos-gst-loader {
                                        position: absolute; right: 10px; top: 50%;
                                        transform: translateY(-50%); display: none;
                                        color: #ff9f43; pointer-events: none; z-index: 5;
                                    }
                                    #pos_gst_number.loading { padding-right: 36px; }
                                    .pos-gst-msg { font-size: 12px; margin-top: 3px; }

                                    .customer-section-title {
                                        font-size: 16px; font-weight: 600; color: #333; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;
                                    }
                                    .customer-details-box {
                                        border: 1px solid #28c76f; border-radius: 12px; padding: 20px 20px 5px 20px; background: #fff; margin-bottom: 20px;
                                    }
                                    .customer-details-box label {
                                        display: flex; align-items: center; font-weight: 500; color: #5e5873; margin-bottom: 8px;
                                    }
                                    .customer-details-box .form-control {
                                        background-color: #f8f9fa !important; border: 1px solid #e0e0e0; border-radius: 8px; color:#333;
                                    }
                                    /* Fix select2 height and background */
                                    .customer-details-box .select2-container .select2-selection--single {
                                        background-color: #f8f9fa !important; border: 1px solid #e0e0e0; border-radius: 8px; height: 40px;
                                    }
                                    .customer-details-box .select2-container--default .select2-selection--single .select2-selection__rendered {
                                        line-height: 40px; padding-left: 12px;
                                    }
                                    .customer-details-box .select2-container--default .select2-selection--single .select2-selection__arrow {
                                        height: 38px;
                                    }
                                </style>

                                <div class="customer-section-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-user-friends" style="color: #28c76f;"></i> Customer details
                                    </div>
                                    <div class="customer-action-btns" style="display:flex; gap:8px;">
                                        <button type="button" id="openQuickAddCustomerBtn" class="btn btn-outline-secondary btn-sm" style="border-radius: 6px; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background: #fff; border-color: #d8d6de; box-shadow: 0 2px 4px rgba(0,0,0,0.05);" title="Add New Customer">
                                            <i class="fas fa-plus" style="color: #28c76f;"></i>
                                        </button>
                                        <button type="button" id="openEditCustomerBtn" class="btn btn-outline-secondary btn-sm" style="border-radius: 6px; width: 30px; height: 30px; display: none; align-items: center; justify-content: center; background: #fff; border-color: #d8d6de; box-shadow: 0 2px 4px rgba(0,0,0,0.05);" title="Edit Selected Customer">
                                            <i class="fas fa-pencil-alt" style="color: #ff9f43;"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="customer-details-box">
                                    <div class="row w-105 pos-customer-row">
                                        <!-- Customer Name -->
                                        <div class="col-md-4 col-12 pos-customer-field">
                                            <div class="select-split select-group w-100">
                                                <div class="select-group w-100">
                                                    <label><i class="far fa-user-circle" style="color: #28c76f; margin-right: 5px;"></i> Customer name</label>
                                                    <select id="customer_name" name="customer_name" style="z-index:1;"
                                                        class="form-control select2">
                                                        <option value="">Select Customer</option>
                                                        @foreach ($customers as $username)
                                                            <option value="{{ $username->id }}"
                                                                data-phone="{{ $username->phone ?? '' }}"
                                                                data-gst="{{ $username->gst_number ?? '' }}">
                                                                {{ $username->company_name ?: $username->name }}{{ $username->phone ? ' - ' . $username->phone : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="error_customername text-danger"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Customer Phone -->
                                        <div class="col-md-4 col-12 pos-customer-field">
                                            <div class="select-split">
                                                <div class="select-group w-100">
                                                    <label><i class="fas fa-phone-alt" style="color: #28c76f; margin-right: 5px;"></i> Customer phone</label>
                                                    <input type="tel" id="customer_phone" class="form-control"
                                                        placeholder="Customer number" name="customer_phone">
                                                    <span class="error_customerphone text-danger"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Customer GST Number -->
                                        <div class="col-md-4 col-12 pos-customer-field" id="customer_gst_field">
                                            <div class="select-split">
                                                <div class="select-group w-100">
                                                    <label><i class="fas fa-file-invoice" style="color: #28c76f; margin-right: 5px;"></i> Customer GST no</label>
                                                    <div class="pos-gst-input-wrap">
                                                        <input type="text" id="pos_gst_number" class="form-control"
                                                            placeholder="Enter GST to auto-fill" name="customer_gst_number"
                                                            maxlength="15" autocomplete="off">
                                                        <span class="pos-gst-loader" id="pos-gst-loader">
                                                            <i class="fas fa-spinner fa-spin"></i>
                                                        </span>
                                                    </div>
                                                    <div class="pos-gst-msg" id="pos-gst-msg"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row w-105 pos-order-row">
                                    <div class="col-md-4 col-6 pos-customer-field pos-customer-field--date">
                                        <div class="select-split pos-order-date-group">
                                            <div class="select-group w-100">
                                                <label>Order Date</label>
                                                <input type="hidden" id="order_date" name="order_date"
                                                    value="{{ now()->format('Y-m-d') }}">
                                                <div class="input-groupicon">
                                                    <input type="text" id="order_date_display" class="form-control"
                                                        value="{{ now()->format('d/m/Y') }}" autocomplete="off">
                                                    <a class="addonset">
                                                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/calendars.svg' }}"
                                                            alt="Calendar">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if (isset($staffList) && $staffList->count() > 0)
                                        <div class="col-md-4 col-6 pos-customer-field">
                                            <div class="select-split">
                                                <div class="select-group w-100">
                                                    <label>Assign Staff</label>
                                                    <select id="assigned_staff_id" name="assigned_staff_id"
                                                        class="form-control select2">
                                                        <option value="">Select Staff (Optional)</option>
                                                        @foreach ($staffList as $staff)
                                                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Order Type dropdown (visible to all roles) --}}
                                    <div class="col-md-4 col-6 pos-customer-field">
                                        <div class="select-split">
                                            <div class="select-group w-100">
                                                <label>Order Type</label>
                                                <select id="order_type" name="order_type" class="form-control">
                                                    <option value="self_pickup" selected>Self Pickup</option>
                                                    <option value="delivery">Delivery</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            /* Sync native <select> change → fill phone only (GST is already in pos_gst_number) */
                            document.getElementById('customer_name').addEventListener('change', function() {
                                var phone = this.options[this.selectedIndex].getAttribute('data-phone') || '';
                                document.getElementById('customer_phone').value = phone;
                            });
                        </script>
                        <div class="split-card">

                        </div>
                        <div class="pt-0">
                            <div class="totalitem">
                                <h4>Total items : 0</h4>
                                <a href="javascript:void(0);" class="clear_items">Clear all</a>
                            </div>
                            <div class="product-table">

                            </div>
                        </div>
                        <div class="split-card">
                        </div>
                        <div class="pt-0 pb-2 body_space_two select-group w-100">
                            @if ($role == 'admin' || $role == 'staff' || $role == 'sub-admin')
                                <!-- Labour Items Section -->
                                <div class="col-lg-12 mb-3">
                                    <div class="select-split">
                                        <div class="select-group w-100">
                                            <hr>
                                            <h5 style=" font-weight: 400; font-size: 16px; ">Labour Items</h5>
                                            <div id="labour-items-container">
                                                <!-- Labour items will be added here dynamically -->
                                            </div>
                                            <!-- <button type="button" class="btn btn-primary mt-2" id="add-labour-item">
                                                                                                                                            <i class="fas fa-plus"></i> Add Labour Item
                                                                                                                                        </button> -->
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Labour Items Section -->
                            @endif
                            <div class="row">

                            </div>
                            <div class="row ">
                                <div class="col-lg-12">
                                    <div class="select-split ">
                                        <div class="select-group w-100">
                                            <label for="shipping">Shipping Cost</label>
                                            <input type="number" class="form-control" placeholder="Shipping Cost."
                                                name="shipping" id="shipping" min="0" step="0.01">
                                            <span class="error_shipping"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ((bool) ($setting->tds_apply ?? false))
                                <div class="row mt-2">
                                    <div class="col-lg-6 col-6">
                                        <div class="select-group w-100">
                                            <label for="tds_percentage">TDS Percentage (%)</label>
                                            <input type="text" class="form-control" name="tds_percentage"
                                                id="tds_percentage" >
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-6">
                                        <div class="select-group w-100 ">
                                            <label for="tds_amount">TDS Amount</label>
                                            <input type="text" class="form-control" name="tds_amount" id="tds_amount"
                                                min="0"  value="0.00" readonly>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-12 ">
                            <div class="select-split ">
                                <div class="select-group w-100">
                                    <label for="remarks">Remarks (Optional)</label>
                                    <textarea class="form-control" name="remarks" id="remarks" rows="4" cols="50"
                                        placeholder="Enter remarks"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="setvalue">
                            <ul>
                                <li>

                                    <h5>Total (Product)</h5>
                                    <h6 style="color: green;">
                                        @if ($currency_position === 'right')
                                            <span class="subtotal-value">0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span class="subtotal-value">0.00</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="subtotal" value="" class="tax-hidden">
                                </li>

                                <li class="gst-summary-row" style="display: none;">
                                    <h5>Total GST Amount</h5>
                                    <h6 class="gst-total-amount">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="discount-summary-row" style="display: none;">
                                    <h5>Discount Amount</h5>
                                    <h6 class="discount-amount">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="discount_amount" value="0.00" class="tax-hidden">
                                </li>

                                <li class="subtotal-summary-row" style="display: none;">
                                    <h5>SubTotal</h5>
                                    <h6 class="price-after-discount">
                                        {{-- @if ($currency_position === 'right')
                                            0.00{{ $currency_symbol }} + 0.00{{ $currency_symbol }} - 0.00{{ $currency_symbol }} = 0.00{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}0.00 + {{ $currency_symbol }}0.00 - {{ $currency_symbol }}0.00 = {{ $currency_symbol }}0.00
                                        @endif --}}
                                    </h6>
                                    <input type="hidden" name="price_after_discount" value="0.00" class="tax-hidden">
                                </li>

                                <li class="shipping-summary-row" style="display: none;">
                                    <h5>Shipping Cost</h5>
                                    <h6 class="shipping-cost-summary">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="labour-summary-row" style="display: none;">
                                    <h5>Labour Charge</h5>
                                    <h6 class="labour-total-amount">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="tds-summary-row" style="display: none;">
                                    <h5>TDS (<span class="tds-percentage-summary">0.00</span>%)</h5>
                                    <h6 class="tds-amount-summary">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                {{-- @foreach ($taxRates as $tax)
                                        <li class="taxList">
                                            <h5>{{ $tax->tax_name }} ({{ $tax->tax_rate }}%) Tax</h5>
                                            <h6 class="tax-value" data-rate="{{ $tax->tax_rate }}"
                                                data-symbol="{{ $currency_symbol }}"
                                                data-position="{{ $currency_position }}">
                                                @if ($currency_position === 'right')
                                                    <span>0.00</span>{{ $currency_symbol }}
                                                @else
                                                    {{ $currency_symbol }}<span>0.00</span>
                                                @endif
                                            </h6>
                                        </li>
                                    @endforeach --}}




                                <li class="round-off-row d-none">
                                    <h5>Round Off</h5>
                                    <h6 class="round-off-amount">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="round_off" value="0.00" class="tax-hidden">
                                </li>

                                <li class="advance-paid-summary-row" style="display: none;">
                                    <h5>Advance Paid</h5>
                                    <h6 class="advance-paid-amount-summary" style="color: red;">
                                        @if ($currency_position === 'right')
                                            <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="total-value">
                                    <h5>Total</h5>
                                    <h6>
                                        @if ($currency_position === 'right')
                                            <span class="total-amount">0</span>{{ $currency_symbol }}
                                        @else
                                            {{ $currency_symbol }}<span class="total-amount">0</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="total" value="" class="tax-hidden">
                                </li>

                                <li class="error-message-container">
                                    <span class="error_total" style="color:red; display:none;"></span>
                                </li>
                            </ul>
                        </div>
                        <div class="payment_panel" id="paymentPanel">
                            <!-- ⬆ Drag handle — grab to move the panel up/down -->
                            <div id="paymentPanelHandle" class="payment-panel-handle" title="Drag to move this panel">
                                <span class="handle-pill"></span>
                                <span class="handle-hint">⬆ drag to reveal products ⬇</span>
                                <button type="button" id="paymentPanelMinBtn" class="panel-min-btn d-none" title="Minimise / Restore">
                                    <svg id="panelMinIcon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>
                                </button>
                            </div>
                            <div id="paymentPanelBody">
                            <div class="setvaluecash mt-3" id="paymentSection">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" class="paymentmethod">
                                            <input type="radio" name="payment_method" value="pending" hidden>
                                            <i class="fas fa-history" style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                            Pay Later
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="paymentmethod">
                                            <input type="radio" name="payment_method" value="cash" hidden>
                                            <i class="fas fa-money-bill-wave" style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                            Cash
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="paymentmethod">
                                            <input type="radio" name="payment_method" value="debit card" hidden>
                                            <i class="fas fa-credit-card" style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                            Debit
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="paymentmethod">
                                            <input type="radio" name="payment_method" value="cash+online" hidden>
                                            <i class="fas fa-money-check-alt" style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                            Cash+Online
                                        </a>
                                    </li>

                                    <li>
                                        <a href="javascript:void(0);" class="paymentmethod">
                                            <input type="radio" name="payment_method" value="scan" hidden>
                                            <i class="fas fa-qrcode" style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                            Scan
                                        </a>
                                    </li>
                                    <li id="emiPaymentOption" style="display: {{ $isAdvanceReceiptMode ? 'none' : 'block' }};">
                                        <a href="javascript:void(0);" class="paymentmethod">
                                            <input type="radio" name="payment_method" value="emi" hidden>
                                            <i class="fas fa-calendar-alt" style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                            EMI
                                        </a>
                                    </li>

                                </ul>

                                {{-- Advance Receipt: Warning Message --}}
                                <div id="advanceReceiptFields" class="mt-2 px-2" style="display: {{ $isAdvanceReceiptMode ? 'block' : 'none' }};">
                                    <p class="text-muted" style="font-size:11px; margin-top:4px;">
                                        <i class="fas fa-lock" style="color:#7367f0;"></i>
                                        Product price will be <strong>locked</strong> at current price when advance is paid.
                                    </p>
                                </div>

                                <div id="paymentDetailActions" class="payment-detail-actions" style="display:none;">
                                    <button type="button" id="cancelPaymentDetails" class="payment-detail-cancel me-2">
                                        Cancel
                                    </button>
                                </div>
                                <div id="posPaidTypeBox" style="display:none; margin-top:10px;">
                                    <div class="form-group mb-2" id="posPaidTypeDropdownGroup">
                                        <label for="posPaidType">Paid Type</label>
                                        <select id="posPaidType" class="form-control">
                                            <option value="" selected disabled>Select Paid Type</option>
                                            <option value="fully">Fully</option>
                                            <option value="partially">Partially</option>
                                        </select>
                                        <span class="error_paidtype text-danger"></span>
                                    </div>
                                    <div id="posPaidAmountFields" style="display:none;">
                                        <div class="row g-2">
                                            <div class="col-md-6 col-6">
                                                <div class="form-group mb-2">
                                                    <label for="posPaidAmount">Enter Amount</label>
                                                    <input type="number" class="form-control" id="posPaidAmount"
                                                        placeholder="0.00" min="0" step="0.01">
                                                    <span class="error_paidamount text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label for="posPendingAmount">Pending Amount</label>
                                                    <input type="number" class="form-control" id="posPendingAmount"
                                                        placeholder="0.00" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="cashOnlineBox" style="display:none; margin-top:10px;">
                                    <div class="row g-2">
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Cash Amount</label>
                                                <input type="number" class="form-control" id="posCashOnlineCashAmount"
                                                    placeholder="Enter Cash Amount">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Online Amount</label>
                                                <input type="number" class="form-control" id="posCashOnlineOnlineAmount"
                                                    placeholder="Enter Online Amount" value="0" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="bankSelectionBox" style="display:none; margin-top:10px;">
                                    <div class="row g-2">
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <div class="bank-label-row">
                                                    <label>Select Bank</label>
                                                    <button type="button" id="openAddBankModal"
                                                        class="bank-add-btn">Add Bank</button>
                                                </div>
                                                <select name="bank_id" id="bank_id" class="form-control">
                                                    <option value="">Select Bank</option>
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->id }}">{{ $bank->bank_name }}
                                                            ({{ $bank->account_number }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="error_bank text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label for="posPaymentRemark">Remark</label>
                                                <input type="text" class="form-control" id="posPaymentRemark"
                                                    placeholder="Enter remark">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="emiBox" style="display:none; margin-top:10px;">
                                    <div class="row g-2">
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Down Payment (Optional)</label>
                                                <input type="number" class="form-control" id="emiDownPayment" placeholder="₹ Amount" min="0" step="0.01">
                                                <small class="text-danger d-none" id="emiDownPaymentError"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Loan Amount</label>
                                                <input type="number" class="form-control" id="emiLoanAmount" placeholder="Auto Calculate" min="0" step="0.01" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>EMI Tenure</label>
                                                <select class="form-control" id="emiTenure">
                                                    <option value="">Select Tenure</option>
                                                    <option value="3">3 Months</option>
                                                    <option value="6">6 Months</option>
                                                    <option value="9">9 Months</option>
                                                    <option value="12">12 Months</option>
                                                    <option value="custom">Custom</option>
                                                </select>
                                                <small class="text-danger d-none" id="emiTenureError"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6 d-none" id="emiCustomTenureCol">
                                            <div class="form-group">
                                                <label>Custom Tenure (Months)</label>
                                                <input type="number" class="form-control" id="emiCustomTenure" min="1" max="120" step="1" placeholder="Enter months">
                                                <small class="text-danger d-none" id="emiCustomTenureError"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Interest Rate (%) <small class="text-muted">Optional</small></label>
                                                <input type="number" class="form-control" id="emiInterestRate" value="0" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Monthly EMI</label>
                                                <input type="number" class="form-control" id="emiMonthlyAmount" placeholder="Auto Calculate" min="0" step="0.01" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Aadhar Number</label>
                                                <input type="text" class="form-control" id="emiAadharNumber" placeholder="Customer Aadhar Number">
                                                <small class="text-danger d-none" id="emiAadharError"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>DO ID <small class="text-muted"></small></label>
                                                <input type="text" class="form-control" id="emiDoId" placeholder="DO ID">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>PAN Number <small class="text-muted">Optional</small></label>
                                                <input type="text" class="form-control" id="emiPanNumber" placeholder="PAN Number">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Guarantor Name <small class="text-muted">Optional</small></label>
                                                <input type="text" class="form-control" id="emiGuarantorName" placeholder="Guarantor Name">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <div class="bank-label-row">
                                                    <label class="mb-0">Select Bank <small class="text-muted">Optional</small></label>
                                                    <button type="button" id="openAddBankModalEmi" class="bank-add-btn">Add Bank</button>
                                                </div>
                                                <select class="form-control" id="emiBankId">
                                                    <option value="">Select Bank</option>
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->id }}">{{ $bank->bank_name }}
                                                            ({{ $bank->account_number }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-danger d-none" id="emiBankError"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <span class="error_peymentmethod"></span>
                            </div>

                            <div class="btn-totallabel">
                                <h6>Total Amount : 60.00$</h6>
                                <h5>{{ $isAdvanceReceiptMode ? 'Generate Advance Receipt' : 'Generate Bill' }} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16"
                                        style="margin-bottom: 0.1rem;">
                                        <path fill-rule="evenodd"
                                            d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8" />
                                    </svg></h5>
                            </div>
                            </div><!-- /#paymentPanelBody -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
    {{-- <script src="https://unpkg.com/html5-qrcode@2.3.4/minified/html5-qrcode.min.js"></script> --}}
    <script>
        if (typeof Html5Qrcode === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js';
            script.onload = function() {
                console.log('Library loaded from fallback');
            };
            document.head.appendChild(script);
        }
    </script>
    <script>
        // ==================== BARCODE SCANNER ====================
        // let html5QrCode = null;
        // ✅ MUST be defined BEFORE initScanner calls it
        // async function onScanSuccess(decodedText) {
        //     console.log("Scan success:", decodedText);
        //     stopScanner();

        //     document.activeElement && document.activeElement.blur();
        //     $('#barcodeScannerModal').modal('hide');
        //     $('#scan-message').text('');

        //     try {
        //         const product = await fetchProductByBarcode(decodedText);
        //         if (product) {
        //             addProductToCart(product);
        //             Swal.fire({
        //                 icon: 'success',
        //                 title: 'Product Added',
        //                 text: product.name + ' added to cart',
        //                 timer: 1500,
        //                 showConfirmButton: false
        //             });
        //         } else {
        //             Swal.fire({
        //                 icon: 'error',
        //                 title: 'Product Not Found',
        //                 text: 'No product with barcode: ' + decodedText
        //             });
        //         }
        //     } catch (error) {
        //         Swal.fire({
        //             icon: 'error',
        //             title: 'Error',
        //             text: error || 'Failed to fetch product. Please try again.'
        //         });
        //     }
        // }
        let html5QrCode = null;

        // 🔊 ADD THIS FUNCTION HERE
        function playBeep() {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

            function beep(time, freq) {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();

                osc.connect(gain);
                gain.connect(audioCtx.destination);

                osc.type = "square";
                osc.frequency.setValueAtTime(freq, audioCtx.currentTime + time);

                gain.gain.setValueAtTime(1, audioCtx.currentTime + time);

                osc.start(audioCtx.currentTime + time);
                osc.stop(audioCtx.currentTime + time + 0.2);
            }

            // 🔥 DOUBLE BEEP
            beep(0, 1200);
            beep(0.25, 1500);
        }
        async function onScanSuccess(decodedText) {
            console.log("Scan success:", decodedText);

            // 🔊 NEW SOUND
            playBeep();

            stopScanner();

            document.activeElement && document.activeElement.blur();
            $('#barcodeScannerModal').modal('hide');
            $('#scan-message').text('');

            try {
                const product = await fetchProductByBarcode(decodedText);
                if (product) {
                    addProductToCart(product);
                }
            } catch (error) {
                console.log(error);
            }
        }
        function onScanError(errorMessage) {
            console.debug("Scan error:", errorMessage);
        }

        function stopScanner() {
            if (!html5QrCode) return;

            if (html5QrCode.isScanning) {
                html5QrCode.stop()
                    .then(() => {
                        html5QrCode = null;
                    })
                    .catch(err => {
                        console.error("Stop error:", err);
                        html5QrCode = null;
                    });
            } else {
                html5QrCode = null;
            }
            $('#scan-message').text('');
        }

        function waitForLibrary(callback, retries = 20) {
            if (typeof Html5Qrcode !== "undefined") {
                callback();
            } else if (retries > 0) {
                setTimeout(() => waitForLibrary(callback, retries - 1), 200);
            } else {
                $('#scan-message').text('Scanner library failed to load. Please refresh.').css('color', 'red');
            }
        }

        function startScanner() {
            console.log("startScanner called");
            waitForLibrary(function() {
                if (html5QrCode) {
                    let stopPromise = html5QrCode.isScanning ?
                        html5QrCode.stop() :
                        Promise.resolve();
                    stopPromise.then(() => {
                        html5QrCode = null;
                        initScanner();
                    }).catch(() => {
                        html5QrCode = null;
                        initScanner();
                    });
                } else {
                    initScanner();
                }
            });
        }

        // function initScanner() {
        //     try {
        //         html5QrCode = new Html5Qrcode("qr-reader");
        //     } catch (e) {
        //         console.error("Failed to create Html5Qrcode:", e);
        //         $('#scan-message').text('Failed to initialize scanner.').css('color', 'red');
        //         return;
        //     }

        //     const config = {
        //         fps: 10,
        //         qrbox: {
        //             width: 250,
        //             height: 250
        //         }
        //     };
        //     $('#scan-message').text('Starting camera...').css('color', '');

        //     // Try rear camera first
        //     html5QrCode.start({
        //             facingMode: "environment"
        //         },
        //         config,
        //         onScanSuccess,
        //         onScanError
        //     ).then(() => {
        //         $('#scan-message').text('Point camera at barcode').css('color', 'green');
        //     }).catch(() => {
        //         console.warn("Rear camera failed, trying front camera...");
        //         html5QrCode.start({
        //                 facingMode: "user"
        //             },
        //             config,
        //             onScanSuccess,
        //             onScanError
        //         ).then(() => {
        //             $('#scan-message').text('Point camera at barcode').css('color', 'green');
        //         }).catch(() => {
        //             console.warn("Front camera also failed, trying any camera...");
        //             Html5Qrcode.getCameras().then(cameras => {
        //                 if (cameras && cameras.length > 0) {
        //                     html5QrCode.start(
        //                         cameras[0].id,
        //                         config,
        //                         onScanSuccess,
        //                         onScanError
        //                     ).then(() => {
        //                         $('#scan-message').text('Point camera at barcode').css(
        //                             'color', 'green');
        //                     }).catch(err => {
        //                         console.error("All camera attempts failed:", err);
        //                         html5QrCode = null;
        //                         showManualBarcodeInput();
        //                     });
        //                 } else {
        //                     html5QrCode = null;
        //                     showManualBarcodeInput();
        //                 }
        //             }).catch(() => {
        //                 html5QrCode = null;
        //                 showManualBarcodeInput();
        //             });
        //         });
        //     });
        // }
        function initScanner() {
            try {
                html5QrCode = new Html5Qrcode("qr-reader");
            } catch (e) {
                console.error("Failed to create Html5Qrcode:", e);
                return;
            }

            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            };

            $('#scan-message').text('Starting camera...');

            // 🔥 NEW CODE (IMPORTANT)
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {

                    let cameraId = devices.find(device =>
                        device.label.toLowerCase().includes('back') ||
                        device.label.toLowerCase().includes('rear')
                    )?.id;

                    // fallback
                    if (!cameraId) {
                        cameraId = devices[0].id;
                    }

                    html5QrCode.start(
                        cameraId,
                        config,
                        onScanSuccess,
                        onScanError
                    ).then(() => {
                        $('#scan-message').text('Point camera at barcode').css('color', 'green');
                    }).catch(err => {
                        console.error("Camera start failed:", err);
                    });

                } else {
                    console.log("No camera found");
                }
            }).catch(err => {
                console.error("Camera error:", err);
            });
        }

        function showManualBarcodeInput() {
            $('#scan-message').text('').hide();
            $('#qr-reader').html(`
        <div style="text-align:center; padding: 30px 20px;">
            <div style="font-size: 48px; margin-bottom: 10px;margin-top: 60px;"></div>
            <p style="color:#666; font-size:14px; margin-bottom:16px;">
                No camera found on this device.<br>Enter barcode manually below:
            </p>
            <div style="display:flex; gap:8px; justify-content:center;">
                <input type="text" id="manualBarcodeInput" class="form-control"
                    placeholder="Enter barcode / product code"
                    style="max-width:260px; font-size:14px;">
                <button type="button" class="btn btn-primary" id="manualBarcodeSubmit">Search</button>
            </div>
            <div id="manualBarcodeError" class="text-danger" style=" font-size:14px; margin-top:8px;"></div>
        </div>
                `);

            $('#qr-reader').off('click', '#manualBarcodeSubmit').on('click', '#manualBarcodeSubmit', function() {
                handleManualBarcode();
            });

            $('#qr-reader').off('keydown', '#manualBarcodeInput').on('keydown', '#manualBarcodeInput', function(e) {
                if (e.key === 'Enter') {
                    handleManualBarcode();
                }
            });
        }

        async function handleManualBarcode() {
            let barcode = $('#manualBarcodeInput').val().trim();

            if (!barcode) {
                $('#manualBarcodeError').text('Please enter a barcode.');
                return;
            }

            $('#manualBarcodeSubmit').prop('disabled', true).text('Searching...');
            $('#manualBarcodeError').text('');

            try {
                const product = await fetchProductByBarcode(barcode);

                document.activeElement && document.activeElement.blur();
                $('#barcodeScannerModal').modal('hide');
                $('#manualBarcodeInput').val('');

                if (product) {
                    addProductToCart(product);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Product Not Found',
                        text: 'No product with barcode: ' + barcode
                    });
                }
            } catch (error) {
                $('#manualBarcodeError').text('Product not found or error occurred.');
                // Swal.fire({
                //     icon: 'error',
                //     title: 'Error',
                //     text: 'Failed to fetch product. Please try again.'
                // });
            } finally {
                $('#manualBarcodeSubmit').prop('disabled', false).text('Search');
            }
        }

        function fetchProductByBarcode(barcode) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '/api/product-by-barcode/' + encodeURIComponent(barcode),
                    type: 'GET',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        resolve(response.status && response.product ? response.product : null);
                    },
                    error: function(xhr) {
                        reject(xhr.responseJSON?.message || 'Network error');
                    }
                });
            });
        }

        function isQuotationModeEnabled() {
            return $('.quotationToggle:checked').length > 0 ||
                new URLSearchParams(window.location.search).get('sale_type') === 'quotation';
        }

        function isWarrantyCategoryProduct(product) {
            if (!product) return false;
            if (product.isWarrantyCategory === true || product.is_warranty_category === true) return true;
            if (String(product.isWarrantyCategory || product.is_warranty_category || '').toLowerCase() === 'true') return true;
            if (String(product.isWarrantyCategory || product.is_warranty_category || '') === '1') return true;

            const categoryName = String(product.category_name || product.categoryName || product.category?.name || '').trim().toLowerCase();
            return categoryName === 'warranty';
        }

        /**
         * Add product to cart (works for both barcode and manual search)
         * FIX: Ensure product ID is stored as a string so that Map keys match
         */
        /**
         * Add product to cart (works for barcode scan, manual entry, or any product source)
         * @param {Object} product - Product object from API or search
         */
        function addProductToCart(product) {
            // ----------------------------------------------------------------------
            // 1. Convert ID to both string (Map key) and number (DOM targeting)
            //    Trim any whitespace to avoid mismatches
            // ----------------------------------------------------------------------
            const rawId = String(product.id || '').trim();
            const numericId = parseInt(rawId, 10) || 0; // use parseInt for integers
            const stringId = String(numericId); // string key for Map

            // ----------------------------------------------------------------------
            // 2. Basic product data
            // ----------------------------------------------------------------------
            const productName = product.name || 'Unknown';
            const rawPrice = parseFloat(product.price) || 0;

            // ----------------------------------------------------------------------
            // 3. Price formatting (use global currency settings)
            // ----------------------------------------------------------------------
            const currencySymbol = '{{ $currency_symbol }}';
            const currencyPosition = '{{ $currency_position }}';
            const productPrice = currencyPosition === 'right' ?
                rawPrice.toFixed(2) + currencySymbol :
                currencySymbol + rawPrice.toFixed(2);

            // ----------------------------------------------------------------------
            // 4. Image handling (fallback to noimage.png)
            // ----------------------------------------------------------------------
            let productImage = '{{ env('ImagePath') }}/admin/assets/img/product/noimage.png';
            if (product.image) {
                try {
                    let cleanImage = product.image.replace(/\\/g, '').replace(/^"(.*)"$/, '$1');
                    let basePath = '{{ env('ImagePath') }}';
                    if (cleanImage.startsWith('[')) {
                        let imagesArray = JSON.parse(cleanImage);
                        if (Array.isArray(imagesArray) && imagesArray.length > 0) {
                            productImage = `${basePath}/storage/${imagesArray[0]}`;
                        }
                    } else {
                        productImage = `${basePath}/storage/${cleanImage}`;
                    }
                } catch (e) {
                    console.warn('Image parse error, using default');
                }
            }

            // ----------------------------------------------------------------------
            // 5. GST option – ensure it's exactly "with_gst" or "without_gst"
            // ----------------------------------------------------------------------
            let gstOption = product.gst_option || 'without_gst';
            if (gstOption !== 'with_gst' && gstOption !== 'without_gst') {
                gstOption = 'without_gst';
            }

            // ----------------------------------------------------------------------
            // 6. GST data – parse JSON string to array if needed
            // ----------------------------------------------------------------------
            let productGst = product.product_gst || null;
            if (productGst) {
                if (typeof productGst === 'string') {
                    productGst = productGst.replace(/\\/g, '');
                    if (productGst === 'null' || productGst === '' || productGst === '[]') {
                        productGst = null;
                    } else {
                        try {
                            productGst = JSON.parse(productGst);
                        } catch (e) {
                            console.error('Failed to parse product_gst:', e);
                            productGst = null;
                        }
                    }
                }
                if (!Array.isArray(productGst) || productGst.length === 0) {
                    productGst = null;
                }
            }

            // If no valid GST data, force gst_option to 'without_gst'
            if (!productGst) {
                gstOption = 'without_gst';
            }

            // ----------------------------------------------------------------------
            // 7. Stock and category
            //    NOTE: If your API uses 'stock' instead of 'quantity', change here
            // ----------------------------------------------------------------------
            const stock = parseQuantityValue(product.quantity); // adjust field name if needed
            const categoryId = product.category_id || 0;
            const isWarrantyCategory = isWarrantyCategoryProduct(product);

            if (!isQuotationModeEnabled() && !isWarrantyCategory && stock <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Out of Stock',
                    text: 'This product is currently out of stock.'
                });
                return;
            }

            // ----------------------------------------------------------------------
            // 8. Check if product already exists in cart (use string ID as key)
            // ----------------------------------------------------------------------
            if (selectedItems.has(stringId)) {
                let item = selectedItems.get(stringId);
                const nextQuantity = parseQuantityValue(item.quantity) + 1;

                if (isQuotationModeEnabled() || item.isWarrantyCategory || nextQuantity <= parseQuantityValue(item.stock)) {
                    item.quantity = nextQuantity;

                    let price = parseItemPriceValue(item.price);
                    let baseAmount = price * item.quantity;

                    if (item.discount_percentage > 0) {
                        item.discount_amount = (baseAmount * item.discount_percentage) / 100;
                    }

                    selectedItems.set(stringId, item);
                    updateTotalItems();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock Limit',
                        text: 'Cannot add more than available stock (' + item.stock + ')'
                    });
                }

                return;
            }

            // ----------------------------------------------------------------------
            // 9. Add new item (store both string ID for Map and numeric productId)
            // ----------------------------------------------------------------------
            selectedItems.set(stringId, {
                id: stringId, // string key (for Map)
                productId: numericId, // numeric original ID (for DOM targeting)
                categoryId: categoryId,
                name: productName,
                price: productPrice,
                image: productImage,
                code: "PT001", // placeholder, adjust if you have real code
                quantity: 1,
                stock: stock,
                isWarrantyCategory: isWarrantyCategory,
                gst_option: gstOption,
                product_gst: productGst,
                discount_percentage: 0,
                discount_amount: 0
            });

            // ----------------------------------------------------------------------
            // 10. Refresh the cart UI
            // ----------------------------------------------------------------------
            updateTotalItems();
        }

        let deviceScanPollTimer = null;
        let deviceScanRequestInFlight = false;
        let mobileScannerConnected = false;

        function setMobileScannerStatus(connected, deviceName = '') {
            const $status = $('#mobileScannerStatus');
            if (!$status.length) return;

            mobileScannerConnected = !!connected;
            $status.removeClass('is-checking is-connected is-disconnected');

            if (mobileScannerConnected) {
                const label = deviceName ? `Mobile scanner: ${deviceName}` : 'Mobile scanner: connected';
                $status
                    .text(label)
                    .addClass('is-connected');
            } else {
                $status
                    .text('Mobile scanner: disconnected')
                    .addClass('is-disconnected');
            }
        }

        async function processConnectedDeviceScans(scans) {
            if (!Array.isArray(scans) || scans.length === 0) {
                return;
            }

            for (const scan of scans) {
                const barcode = String(scan?.barcode || '').trim();
                if (!barcode) {
                    continue;
                }

                try {
                    const product = await fetchProductByBarcode(barcode);
                    if (product) {
                        addProductToCart(product);
                    }
                } catch (error) {
                    console.warn('Failed to process mobile scan:', barcode, error);
                }
            }
        }

        function pullConnectedDeviceScans() {
            if (deviceScanRequestInFlight) {
                return;
            }

            deviceScanRequestInFlight = true;

            $.ajax({
                url: '/pull-device-scans',
                type: 'GET',
                data: {
                    limit: 15
                },
                success: async function(response) {
                    if (!response || response.connected !== true) {
                        setMobileScannerStatus(false);
                        return;
                    }

                    setMobileScannerStatus(true, response.device_name || '');
                    await processConnectedDeviceScans(response.scans || []);
                },
                error: function() {
                    // Keep previous status; next polling cycle will refresh.
                },
                complete: function() {
                    deviceScanRequestInFlight = false;
                }
            });
        }

        function startConnectedDeviceScannerSync() {
            if (deviceScanPollTimer) {
                return;
            }

            $.get('/get-session-device', function(res) {
                setMobileScannerStatus(!!res.connected, res.device_name || '');
            }).fail(function() {
                // Keep initial "checking" state if this check fails once.
            });

            pullConnectedDeviceScans();
            deviceScanPollTimer = setInterval(pullConnectedDeviceScans, 1200);
        }

        // ==================== MODAL EVENT LISTENERS ====================
        $('#scanBarcodeBtn').on('click', function() {
            if (mobileScannerConnected) {
                Swal.fire({
                    icon: 'info',
                    title: 'Mobile scanner is active',
                    text: 'Scan products from your connected phone in Setting > Connected Devices. Products will auto-add here.',
                    timer: 1800,
                    showConfirmButton: false
                });
                return;
            }
            $('#barcodeScannerModal').modal('show');
        });

        $('#barcodeScannerModal').on('shown.bs.modal', function() {
            setTimeout(startScanner, 400);
        });

        $('#barcodeScannerModal').on('hidden.bs.modal', function() {
            stopScanner();
            $('#qr-reader').html('').css('min-height', '300px');
            $('#scan-message').text('Initializing camera...').show().css('color', '');
        });
    </script>
    <script>
        let labourItemsList = [];
        let authToken = localStorage.getItem("authToken");
        let selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

        function loadLabourItems() {

            $.ajax({
                url: "/api/get-all-labour-items",
                type: "GET",
                dataType: "json",
                data: {
                    selectedSubAdminId: selectedSubAdminId
                },
                headers: {
                    "Authorization": "Bearer " + authToken,
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    if (response.status) {
                        labourItemsList = response.data;

                        $("#labour-items-container").html(""); // clear
                        addLabourRow(); // first row
                    }
                },
                error: function() {
                    // console.log("Labour items load failed");
                }
            });
        }

        function labourOptionsHtml() {

            let html = `<option value="">Select Labour</option>`;

            labourItemsList.forEach(item => {
                html += `
            <option value="${item.id}" data-price="${item.price}">
                ${item.item_name}
            </option>`;
            });

            return html;
        }

        function addLabourRow() {

            let row = `
                <div class="row gx-1 align-items-center labour-row mb-2">
                    <div class="col-5">
                        <select class="form-control labour-select select2-labour">
                            ${labourOptionsHtml()}
                        </select>
                    </div>
                    <div class="col-3">
                        <input type="number" class="form-control form-control labour-qty"
                            value="1" min="1" disabled>
                    </div>
                    <div class="col-3">
                        <input type="number" class="form-control form-control labour-price"
                            placeholder="Price" disabled>
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-danger  remove-labour" style="display:none; width: 40px; height: 38px; padding: 0;">×</button>
                        <button type="button" class="btn btn-success  add-labour" style="width: 40px; height: 38px; padding: 0;">+</button>
                    </div>
                </div>
            `;

            let $row = $(row);
            $("#labour-items-container").append($row);
            $row.find(".select2-labour").select2();
            updateLabourButtons();
        }

        function updateLabourButtons() {
            let rows = $(".labour-row");
            rows.each(function(index) {
                if (index === rows.length - 1) {
                    $(this).find(".add-labour").show();
                    $(this).find(".remove-labour").hide();
                } else {
                    $(this).find(".add-labour").hide();
                    $(this).find(".remove-labour").show();
                }
            });
        }

        $(document).on("click", ".remove-labour", function() {
            $(this).closest(".labour-row").remove();
            updateLabourButtons();
            calculateTotals();
        });

        $(document).on("input change", ".labour-qty, .labour-price", function() {
            calculateTotals();
        });

        $(document).on("change", ".labour-select", function() {

            let labourId = $(this).val();
            let price = $(this).find(":selected").data("price") || 0;
            let $row = $(this).closest(".labour-row");
            let $qtyInput = $row.find(".labour-qty");
            let $priceInput = $row.find(".labour-price");

            if (!labourId) {
                $qtyInput.val(1).prop("disabled", true);
                $priceInput.val("").prop("disabled", true);
                calculateTotals();
                return;
            }

            $qtyInput.prop("disabled", false);
            $priceInput.val(price).prop("disabled", false);

            const isLastRow = $row.is($(".labour-row").last());
            const allRowsSelected = $(".labour-row").toArray().every(function(rowEl) {
                return $(rowEl).find(".labour-select").val();
            });

            if (isLastRow && allRowsSelected) {
                addLabourRow();
            } else {
                updateLabourButtons();
            }

            calculateTotals(); // recalc bill
        });
        $(document).on("click", ".add-labour", function() {
            let $currentRow = $(this).closest(".labour-row");
            let selectedLabourId = $currentRow.find(".labour-select").val();

            if (!selectedLabourId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select Labour Item',
                    text: 'Please select a labour item before adding a new labour row.'
                });
                return;
            }

            addLabourRow();
        });
        loadLabourItems();
    </script>
    <script>
        let selectedItems = new Map();
        const isTdsEnabled = @json((bool) ($setting->tds_apply ?? false));
        let isAdvanceReceiptMode = @json($isAdvanceReceiptMode);

        function updatePosPageTitle(isQuotationMode) {
            let titleText = 'Add Sale';
            if (isAdvanceReceiptMode) {
                titleText = 'Add Advance Receipt';
            } else if (isQuotationMode) {
                titleText = 'Add Quotation';
            }
            $('#posPageTitle').text(titleText);
            document.title = titleText;
        }

        // Auto-calculate remaining balance for Advance Receipt mode


        function resetPosAddBankForm() {
            if ($('#addBankForm').length) {
                $('#addBankForm')[0].reset();
            }
            $('#add_opening_balance').val('0');
            $('#add_bank_status').val('1');
            $('#addBankForm .text-danger').text('');
        }

        function upsertPosBankOption(bank) {
            if (!bank || !bank.id) {
                return;
            }

            const bankId = String(bank.id);
            const bankName = bank.bank_name || 'Unnamed Bank';
            const accountNumber = bank.account_number ? ` (${bank.account_number})` : '';
            const optionLabel = `${bankName}${accountNumber}`;
            const existingOption = $('#bank_id option[value="' + bankId + '"]');

            if (existingOption.length) {
                existingOption.text(optionLabel);
            } else {
                $('#bank_id').append(new Option(optionLabel, bankId));
            }

            // Also sync the EMI bank dropdown
            const existingEmiOption = $('#emiBankId option[value="' + bankId + '"]');
            if (existingEmiOption.length) {
                existingEmiOption.text(optionLabel);
            } else {
                $('#emiBankId').append(new Option(optionLabel, bankId));
            }

            $('#bank_id').val(bankId).trigger('change');
        }

        function formatPosOrderDateForDisplay(value) {
            if (!value) {
                return '';
            }

            if (/^\d{2}\/\d{2}\/\d{4}$/.test(value)) {
                return value;
            }

            if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
                const parts = value.split('-');
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            }

            const parsed = moment(value, ['YYYY-MM-DD', 'DD/MM/YYYY', moment.ISO_8601], true);
            return parsed.isValid() ? parsed.format('DD/MM/YYYY') : value;
        }

        function formatPosOrderDateForApi(value) {
            if (!value) {
                return '';
            }

            if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
                return value;
            }

            if (/^\d{2}\/\d{2}\/\d{4}$/.test(value)) {
                const parts = value.split('/');
                return `${parts[2]}-${parts[1]}-${parts[0]}`;
            }

            const parsed = moment(value, ['DD/MM/YYYY', 'YYYY-MM-DD', moment.ISO_8601], true);
            return parsed.isValid() ? parsed.format('YYYY-MM-DD') : value;
        }

        function setPosOrderDate(value) {
            const apiValue = formatPosOrderDateForApi(value);
            $('#order_date').val(apiValue);
            $('#order_date_display').val(formatPosOrderDateForDisplay(apiValue));
        }

        function initPosOrderDatePicker() {
            const $orderDateDisplay = $('#order_date_display');

            if (!$orderDateDisplay.length || typeof $orderDateDisplay.datetimepicker !== 'function') {
                return;
            }

            $orderDateDisplay.val(formatPosOrderDateForDisplay($('#order_date').val()));
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
                const formattedDate = e.date ? e.date.format('YYYY-MM-DD') : formatPosOrderDateForApi($(this)
                    .val());
                setPosOrderDate(formattedDate);
            });

            $orderDateDisplay.on('blur', function() {
                setPosOrderDate($(this).val());
            });
        }

        document.querySelectorAll('.paymentmethod').forEach(el => {
            el.addEventListener('click', function() {
                document.querySelectorAll('input[name="payment_method"]').forEach(radio => radio.checked =
                    false);
                this.querySelector('input[type="radio"]').checked = true;
                document.querySelectorAll('.paymentmethod').forEach(item => item.classList.remove(
                    'active'));
                this.classList.add('active');
            });
        });
        $(document).ready(function() {
            initPosOrderDatePicker();

            function toAmount(value) {
                const parsed = parseFloat(value);
                return Number.isFinite(parsed) ? parsed : 0;
            }

            function clampAmount(value, min, max) {
                return Math.min(Math.max(value, min), max);
            }

            function getCurrentSaleTotal() {
                let total = toAmount($("input[name='total']").val());
                let advancePaid = toAmount("{{ $convertOrderAdvancePaid ?? 0 }}");
                return Math.max(0, total - advancePaid);
            }

            function getSelectedPaymentMethod() {
                return $("input[name='payment_method']:checked").val() || "";
            }

            function refreshPosPaymentUi(resetAmounts = false) {
                const selectedPayment = getSelectedPaymentMethod();
                const total = getCurrentSaleTotal();
                const $paidType = $("#posPaidType");

                $(".error_bank, .error_paidtype, .error_paidamount").text("");
                if (selectedPayment && selectedPayment !== "pending") {
                    $("#paymentDetailActions").css("display", "flex");
                } else {
                    $("#paymentDetailActions").hide();
                }

                if (!selectedPayment || selectedPayment === "pending") {
                    $("#posPaidTypeBox, #posPaidAmountFields, #cashOnlineBox, #bankSelectionBox, #emiBox").hide();
                    $("#posPaidAmount, #posPendingAmount, #posCashOnlineCashAmount, #posCashOnlineOnlineAmount")
                        .val("");
                    $("#posPaidType").val("");
                    return;
                }

                if (selectedPayment === "emi") {
                    $("#posPaidTypeBox, #posPaidAmountFields, #cashOnlineBox, #bankSelectionBox").hide();
                    $("#posPaidAmount, #posPendingAmount, #posCashOnlineCashAmount, #posCashOnlineOnlineAmount")
                        .val("");
                    $("#emiBox").show();
                    const downPayment = toAmount($("#emiDownPayment").val());
                    const loanAmount = Math.max(total - downPayment, 0);
                    $("#emiLoanAmount").val(loanAmount.toFixed(2));
                    const tenure = $("#emiTenure").val();
                    const isCustomTenure = tenure === "custom";
                    if (isCustomTenure) {
                        $("#emiCustomTenureCol").removeClass("d-none").show();
                    } else {
                        $("#emiCustomTenureCol").addClass("d-none").hide();
                    }
                    const months = isCustomTenure
                        ? parseInt($("#emiCustomTenure").val() || "0", 10)
                        : parseInt(tenure || "0", 10);
                    const annualRate = toAmount($("#emiInterestRate").val());

                    // Inline EMI validation
                    $("#emiCustomTenureError").addClass("d-none").text("");
                    if (isCustomTenure) {
                        const rawCustom = ($("#emiCustomTenure").val() || "").trim();
                        if (rawCustom !== "" && (months <= 0 || !Number.isInteger(months))) {
                            $("#emiCustomTenureError").removeClass("d-none").text("Please enter a valid positive whole number.");
                        }
                    }
                    if (downPayment > 0 && downPayment >= total && total > 0) {
                        $("#emiDownPaymentError").removeClass("d-none").text("Down payment must be less than loan amount.");
                    } else {
                        $("#emiDownPaymentError").addClass("d-none").text("");
                    }

                    let emi = 0;
                    if (loanAmount > 0 && months > 0) {
                        if (annualRate === 0) {
                            // Zero interest: simple division
                            emi = loanAmount / months;
                        } else {
                            // Standard amortization formula: EMI = P×R×(1+R)^N / [(1+R)^N - 1]
                            const R = annualRate / 12 / 100;
                            const pow = Math.pow(1 + R, months);
                            emi = (loanAmount * R * pow) / (pow - 1);
                        }
                        $("#emiMonthlyAmount").val(emi.toFixed(2));

                        // Update Total Amount bar: Down Payment + (Monthly EMI × N)
                        const emiTotal = downPayment + (emi * months);
                        $(".btn-totallabel h6").html('Total Amount : ₹' + emiTotal.toFixed(2));
                    } else {
                        $("#emiMonthlyAmount").val("");
                        // Reset to cart total when EMI not fully configured
                        $(".btn-totallabel h6").html('Total Amount : ₹' + total.toFixed(2));
                    }
                    return;
                }

                $("#emiCustomTenureCol").addClass("d-none").hide();

                $("#posPaidTypeBox").show();
                if (isAdvanceReceiptMode) {
                    $paidType.val('partially');
                    $("#posPaidTypeDropdownGroup").hide();
                } else {
                    $("#posPaidTypeDropdownGroup").show();
                }
                const paidType = $paidType.val();

                if (!paidType) {
                    $("#posPaidAmountFields, #cashOnlineBox, #bankSelectionBox, #emiBox").hide();
                    $("#posPaidAmount, #posPendingAmount, #posCashOnlineCashAmount, #posCashOnlineOnlineAmount")
                        .val("");
                    return;
                }

                if (selectedPayment === "debit card" || selectedPayment === "scan" || selectedPayment ===
                    "cash+online") {
                    $("#bankSelectionBox").show();
                } else {
                    $("#bankSelectionBox").hide();
                    $("#bank_id").val("");
                }

                $("#posPaidAmountFields").show();
                let paidAmount = 0;
                let cashAmount = 0;
                let onlineAmount = 0;

                if (selectedPayment === "cash+online") {
                    $("#cashOnlineBox").show();

                    const rawCashAmount = ($("#posCashOnlineCashAmount").val() || "").trim();
                    const rawOnlineAmount = ($("#posCashOnlineOnlineAmount").val() || "").trim();
                    const enteredCashAmount = toAmount(rawCashAmount);
                    const enteredOnlineAmount = toAmount(rawOnlineAmount);

                    cashAmount = clampAmount(enteredCashAmount, 0, total);
                    onlineAmount = enteredOnlineAmount;

                    if (paidType === "fully") {
                        onlineAmount = Math.max(total - cashAmount, 0);
                        $("#posCashOnlineOnlineAmount").prop("readonly", true).addClass("bg-light");
                        $("#posCashOnlineOnlineAmount").val(onlineAmount.toFixed(2));
                    } else {
                        onlineAmount = clampAmount(onlineAmount, 0, total);
                        if (cashAmount + onlineAmount > total) {
                            onlineAmount = Math.max(total - cashAmount, 0);
                        }
                        $("#posCashOnlineOnlineAmount").prop("readonly", false).removeClass("bg-light");

                        if (rawOnlineAmount === "") {
                            $("#posCashOnlineOnlineAmount").val("");
                        } else if (enteredOnlineAmount !== onlineAmount) {
                            $("#posCashOnlineOnlineAmount").val(onlineAmount.toFixed(2));
                        }
                    }

                    paidAmount = clampAmount(cashAmount + onlineAmount, 0, total);

                    if (rawCashAmount === "") {
                        $("#posCashOnlineCashAmount").val("");
                    } else if (enteredCashAmount !== cashAmount) {
                        $("#posCashOnlineCashAmount").val(cashAmount.toFixed(2));
                    }

                    $("#posPaidAmount").val(paidAmount.toFixed(2)).prop("readonly", true).addClass("bg-light");
                } else {
                    $("#emiDownPayment, #emiLoanAmount, #emiMonthlyAmount, #emiAadharNumber, #emiPanNumber, #emiGuarantorName")
                        .val("");
                    $("#emiTenure").val("");
                    $("#emiInterestRate").val("0");
                    $("#emiBankId").val("");
                    $("#cashOnlineBox").hide();
                    $("#posCashOnlineCashAmount, #posCashOnlineOnlineAmount").val("");

                    if (paidType === "fully") {
                        paidAmount = total;
                        $("#posPaidAmount").val(total.toFixed(2)).prop("readonly", true).addClass("bg-light");
                    } else {
                        const rawPaidAmount = ($("#posPaidAmount").val() || "").trim();
                        const entered = toAmount(rawPaidAmount);
                        paidAmount = clampAmount(entered, 0, total);

                        if (resetAmounts) {
                            $("#posPaidAmount").val("");
                        } else if (rawPaidAmount === "") {
                            $("#posPaidAmount").val("");
                        } else if (entered !== paidAmount) {
                            $("#posPaidAmount").val(paidAmount.toFixed(2));
                        }

                        $("#posPaidAmount").prop("readonly", false).removeClass("bg-light");
                    }
                }

                const pendingAmount = Math.max(total - paidAmount, 0);
                $("#posPendingAmount").val(pendingAmount.toFixed(2));
            }

            $(document).on('input change', '#emiDownPayment, #emiInterestRate, #emiTenure, #emiCustomTenure', function() {
                clearPosEmiValidation();
                if (getSelectedPaymentMethod() === 'emi') {
                    refreshPosPaymentUi();
                }
            });

            function getPosPaymentMeta() {
                const selectedPayment = getSelectedPaymentMethod();
                const paidType = $("#posPaidType").val();
                const paidAmount = toAmount($("#posPaidAmount").val());
                const pendingAmount = toAmount($("#posPendingAmount").val());
                const cashAmount = toAmount($("#posCashOnlineCashAmount").val());
                const onlineAmount = toAmount($("#posCashOnlineOnlineAmount").val());
                const emiDownPayment = toAmount($("#emiDownPayment").val());
                const emiLoanAmount = toAmount($("#emiLoanAmount").val());
                const emiInterestRate = toAmount($("#emiInterestRate").val());
                const emiTenure = $("#emiTenure").val();
                const emiCustomTenure = $("#emiCustomTenure").val();
                const emiMonthlyAmount = toAmount($("#emiMonthlyAmount").val());
                const emiAadharNumber = ($("#emiAadharNumber").val() || "").trim();
                const emiDoId = ($("#emiDoId").val() || "").trim();
                const emiPanNumber = ($("#emiPanNumber").val() || "").trim();
                const emiGuarantorName = ($("#emiGuarantorName").val() || "").trim();
                const emiBankId = $("#emiBankId").val() || "";

                return {
                    selectedPayment,
                    paidType,
                    paidAmount,
                    pendingAmount,
                    cashAmount,
                    onlineAmount,
                    emiDownPayment,
                    emiLoanAmount,
                    emiInterestRate,
                    emiTenure,
                    emiCustomTenure,
                    emiMonthlyAmount,
                    emiAadharNumber,
                    emiDoId,
                    emiPanNumber,
                    emiGuarantorName,
                    emiBankId
                };
            }

            function resetPosPaymentUi() {
                $("#posPaidType").val("");
                $("#posPaidAmount, #posPendingAmount, #posCashOnlineCashAmount, #posCashOnlineOnlineAmount, #posPaymentRemark")
                    .val("");
                $("#bank_id").val("");
                $("#emiBankId").val("");
                $("#emiDownPayment, #emiLoanAmount, #emiTenure, #emiInterestRate, #emiMonthlyAmount, #emiAadharNumber, #emiDoId, #emiPanNumber, #emiGuarantorName")
                    .val("");
                $("#emiTenure").val("");
                $("#emiInterestRate").val("0");
                $("#emiMonthlyAmount").val("");
                $("#paymentDetailActions, #posPaidTypeBox, #posPaidAmountFields, #cashOnlineBox, #bankSelectionBox, #emiBox").hide();
                $(".is-invalid").removeClass("is-invalid");
                $("#emiTenureError, #emiAadharError, #emiBankError, #emiCustomTenureError, #emiDownPaymentError").addClass("d-none").text("");
            }

            function selectPayLaterPayment() {
                const $payLater = $(".paymentmethod").has("input[name='payment_method'][value='pending']");
                $("input[name='payment_method']").prop("checked", false);
                $payLater.find("input[name='payment_method']").prop("checked", true);
                $(".paymentmethod").removeClass("active");
                $payLater.addClass("active");
                resetPosPaymentUi();
                $(".error_peymentmethod").text("");
                refreshPosPaymentUi(true);
            }

            function clearPosEmiValidation() {
                $("#emiTenure, #emiAadharNumber, #emiBankId").removeClass("is-invalid");
                $("#emiTenureError, #emiAadharError, #emiBankError").addClass("d-none").text("");
            }

            function validatePosEmiFields() {
                clearPosEmiValidation();
                if (getSelectedPaymentMethod() !== "emi") {
                    return true;
                }

                let valid = true;
                const tenure = $("#emiTenure").val();
                const customMonths = parseInt($("#emiCustomTenure").val() || "0", 10);
                const aadhar = ($("#emiAadharNumber").val() || "").trim();
                const bankId = $("#emiBankId").val();

                if (!tenure) {
                    $("#emiTenure").addClass("is-invalid");
                    $("#emiTenureError").removeClass("d-none").text("EMI tenure is required.");
                    valid = false;
                } else if (tenure === "custom" && (!Number.isFinite(customMonths) || customMonths <= 0)) {
                    $("#emiCustomTenure").addClass("is-invalid");
                    $("#emiCustomTenureError").removeClass("d-none").text("Enter a valid number of months.");
                    valid = false;
                }

                if (!bankId) {
                    $("#emiBankId").addClass("is-invalid");
                    $("#emiBankError").removeClass("d-none").text("Bank selection is required.");
                    valid = false;
                }

                return valid;
            }

            window.refreshPosPaymentUi = refreshPosPaymentUi;
            window.getPosPaymentMeta = getPosPaymentMeta;
            window.resetPosPaymentUi = resetPosPaymentUi;
            window.clearPosEmiValidation = clearPosEmiValidation;
            window.validatePosEmiFields = validatePosEmiFields;

    // Mobile → Desktop
    $(document).on('change', 'input[name="gst_option_mobile"]', function () {
        var val = $(this).val();
        $('input[name="gst_option"][value="' + val + '"]').prop('checked', true);
        if (typeof window.renderSelectedItems === 'function') window.renderSelectedItems();
        if (typeof window.calculateTotals === 'function') window.calculateTotals();
    });

    // Desktop → Mobile
    $(document).on('change', 'input[name="gst_option"]', function () {
        var val = $(this).val();
        $('input[name="gst_option_mobile"][value="' + val + '"]').prop('checked', true);
        if (typeof window.renderSelectedItems === 'function') window.renderSelectedItems();
        if (typeof window.calculateTotals === 'function') window.calculateTotals();
    });

    // On page load: make sure mobile reflects desktop default (without = checked)
    var currentGst = $('input[name="gst_option"]:checked').val() || 'without';
    $('input[name="gst_option_mobile"][value="' + currentGst + '"]').prop('checked', true);


            $(".paymentmethod").on("click", function() {
                const value = $(this).find("input[name='payment_method']").val();

                $("input[name='payment_method']").prop("checked", false);
                $(this).find("input[name='payment_method']").prop("checked", true);

                if (value !== "pending") {
                    $("#posPaidType").val("");
                }

                if (value !== "emi") {
                    $("#emiDownPayment, #emiLoanAmount, #emiMonthlyAmount, #emiAadharNumber, #emiPanNumber, #emiGuarantorName")
                        .val("");
                    $("#emiTenure").val("");
                    $("#emiInterestRate").val("0");
                    $("#emiBankId").val("");
                }
                refreshPosPaymentUi(true);
            });

            $("#cancelPaymentDetails").on("click", function() {
                selectPayLaterPayment();
            });

            $("#posPaidType").on("change", function() {
                refreshPosPaymentUi(false);
            });

            $("#posPaidAmount, #posCashOnlineCashAmount, #posCashOnlineOnlineAmount").on("input", function() {
                refreshPosPaymentUi(false);
            });

            refreshPosPaymentUi(true);
        });

        $(document).ready(function() {

            // hide payment buttons when quotation checked
            $(document).ready(function() {

                // $("#quotationToggle").on("change", function() {

                //     if ($(this).is(":checked")) {

                //         // Hide payment section
                //         $("#paymentSection").slideUp();

                //         // Optional: clear selected payment
                //         $("input[name='payment_method']").prop("checked", false);
                //         $(".paymentmethod").removeClass("active");

                //         // Hide extra boxes
                //         $("#cashOnlineBox").hide();
                //         $("#bankSelectionBox").hide();

                //     } else {

                //         // Show payment section again
                //         $("#paymentSection").slideDown();
                //     }
                // });
                $("#quotationToggle").on("change", function() {

                    if ($(this).is(":checked")) {

                        // ✅ mark quotation
                        $("#quotation_status").val("quotation");

                        // Hide payment UI
                        $("#paymentSection").slideUp();

                        // Clear payment selection
                        $("input[name='payment_method']").prop("checked", false);
                        $(".paymentmethod").removeClass("active");

                        if (typeof window.resetPosPaymentUi === "function") {
                            window.resetPosPaymentUi();
                        } else {
                            $("#posPaidTypeBox, #posPaidAmountFields, #cashOnlineBox, #bankSelectionBox")
                                .hide();
                        }

                    } else {

                        // ✅ normal sale
                        $("#quotation_status").val("sales");

                        $("#paymentSection").slideDown();
                    }
                });

            });

            selectedItems = new Map();
            // console.log('authToken', authToken);
            // console.log('selectedSubAdminId', selectedSubAdminId);
            // Setup CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize tax rows as hidden on page load
            $(".taxList").hide();

            function formatCurrency(amount, symbol, position) {
                return position === 'right' ? amount + symbol : symbol + amount;
            }

            function parseItemPriceValue(price) {
                return parseFloat(String(price).replace(/[^0-9.-]+/g, "")) || 0;
            }

            function parseQuantityValue(quantity) {
                const parsedQuantity = parseFloat(String(quantity).replace(/[^0-9.-]+/g, ""));
                return Number.isFinite(parsedQuantity) ? parsedQuantity : 0;
            }

            // Share quantity parser across script blocks.
            window.parseQuantityValue = parseQuantityValue;

            // Share stock availability UI updater across script blocks.
            window.refreshProductAvailabilityUI = function() {
                const allowOutOfStockForQuotation = isQuotationModeEnabled();

                $('.productset').each(function() {
                    const $product = $(this);
                    const stock = window.parseQuantityValue($product.data('stock'));
                    const isWarrantyCategory = isWarrantyCategoryProduct({
                        is_warranty_category: $product.data('is-warranty-category'),
                        category_name: $product.data('category-name')
                    });
                    const $button = $product.find('.productsetbtn button');

                    if (stock <= 0 && !isWarrantyCategory) {
                        $product.toggleClass('disabled-product', !allowOutOfStockForQuotation);
                        if ($button.length) {
                            $button.prop('disabled', !allowOutOfStockForQuotation);
                        }
                    } else {
                        $product.removeClass('disabled-product');
                        if ($button.length) {
                            $button.prop('disabled', false);
                        }
                    }
                });
            };

            function formatQuantityValue(quantity) {
                const parsedQuantity = parseQuantityValue(quantity);
                return Number.isInteger(parsedQuantity) ? String(parsedQuantity) : String(parsedQuantity);
            }

            window.calculateTotals = function() {

                const firstTaxElement = $(".tax-value").first();
                const currencySymbol = firstTaxElement.data("symbol") || '{{ $currency_symbol }}';
                const currencyPosition = firstTaxElement.data("position") || '{{ $currency_position }}';

                let baseSubtotal = 0;
                let subtotalAfterDiscount = 0;
                let totalProductDiscount = 0;
                let totalProductGst = 0;
                let labourTotal = 0;

                const gstOption = $("input[name='gst_option']:checked").val();

                // Calculate Labour Total
                $(".labour-row").each(function() {
                    let qty = parseFloat($(this).find(".labour-qty").val()) || 0;
                    let price = parseFloat($(this).find(".labour-price").val()) || 0;
                    labourTotal += qty * price;
                });

                selectedItems.forEach(function(item) {

                    let price = parseItemPriceValue(item.price);
                    let enteredAmount = price * item.quantity;
                    
                    let baseAmount = enteredAmount;
                    let productGst = 0;

                    // ===== GST CALCULATION =====
                    if (gstOption === "with" && item.gst_option === "with_gst" && item.product_gst) {
                        try {
                            const gstData = Array.isArray(item.product_gst) ?
                                item.product_gst :
                                JSON.parse(item.product_gst);

                            let totalGstRate = 0;
                            gstData.forEach(tax => {
                                totalGstRate += (parseFloat(tax.tax_rate) / 100);
                            });

                            if (totalGstRate > 0) {
                                baseAmount = enteredAmount;
                                let totalGstAmount = enteredAmount * totalGstRate;
                                gstData.forEach(tax => {
                                    productGst += totalGstAmount * ((parseFloat(tax.tax_rate) / 100) / totalGstRate);
                                });
                            }

                        } catch (e) {
                            // console.error("GST error:", e);
                        }
                    }

                    // ✅ GST INCLUDED AMOUNT
                    let gstIncludedAmount = enteredAmount + productGst;

                    // ===== DISCOUNT ON GST INCLUDED PRICE =====
                    let discountAmount = 0;

                    if (item.discount_percentage > 0) {
                        discountAmount =
                            (gstIncludedAmount * item.discount_percentage) / 100;
                    }

                    item.discount_amount = discountAmount;
                    selectedItems.set(item.id, item);

                    // ✅ FINAL PRODUCT AMOUNT AFTER DISCOUNT
                    let afterDiscountAmount = gstIncludedAmount - discountAmount;

                    // ===== TOTALS =====
                    baseSubtotal += baseAmount;
                    subtotalAfterDiscount += afterDiscountAmount;
                    totalProductDiscount += discountAmount;
                    totalProductGst += productGst;
                });
                // ================= GLOBAL DISCOUNT (REMOVED) =================
                let globalDiscountAmount = 0;
                let priceAfterGlobalDiscount = subtotalAfterDiscount - totalProductGst;

                // ================= FINAL TOTAL =================
                let shipping = parseFloat($("#shipping").val()) || 0;
                const rawTdsPercentage = isTdsEnabled ? ($("#tds_percentage").val() || "").trim() : "";
                const tdsPercentageInput = isTdsEnabled ? (parseFloat(rawTdsPercentage) || 0) : 0;
                const tdsPercentage = Math.max(0, Math.min(100, tdsPercentageInput));

                const preTdsTotal = priceAfterGlobalDiscount + totalProductGst + shipping + labourTotal;
                const tdsAmount = isTdsEnabled ? (preTdsTotal * tdsPercentage) / 100 : 0;

                let finalTotal = preTdsTotal - tdsAmount;
                let roundedTotal = Math.round(finalTotal);
                let roundOffAmount = roundedTotal - finalTotal;

                let advancePaid = parseFloat("{{ $convertOrderAdvancePaid ?? 0 }}") || 0;
                let payableTotal = roundedTotal;
                if (advancePaid > 0) {
                    payableTotal = Math.max(0, roundedTotal - advancePaid);
                }

                // ================= UI UPDATE =================

                $(".setvalue li:nth-child(1) h6").html(
                    formatCurrency(baseSubtotal.toFixed(2), currencySymbol, currencyPosition)
                );

                $(".discount-amount").html(
                    formatCurrency((totalProductDiscount + globalDiscountAmount).toFixed(2), currencySymbol,
                        currencyPosition)
                );

                $(".price-after-discount").html(
                    `${formatCurrency((priceAfterGlobalDiscount + totalProductGst).toFixed(2), currencySymbol, currencyPosition)}`
                );

                $(".shipping-cost-summary").html(
                    formatCurrency(shipping.toFixed(2), currencySymbol, currencyPosition)
                );

                if (isTdsEnabled) {
                    $("#tds_amount").val(tdsAmount.toFixed(2));
                    $(".tds-percentage-summary").text(tdsPercentage.toFixed(2));
                    $(".tds-amount-summary").html(
                        `- ${formatCurrency(tdsAmount.toFixed(2), currencySymbol, currencyPosition)}`
                    );
                    $(".tds-summary-row").show();
                } else {
                    $(".tds-summary-row").hide();
                }

                $(".round-off-amount").html(
                    formatCurrency(roundOffAmount.toFixed(2), currencySymbol, currencyPosition)
                );

                if (advancePaid > 0) {
                    $(".advance-paid-amount-summary").html(
                        `- ${formatCurrency(advancePaid.toFixed(2), currencySymbol, currencyPosition)}`
                    );
                    $(".advance-paid-summary-row").show();
                } else {
                    $(".advance-paid-summary-row").hide();
                }

                $(".total-value h6").html(
                    formatCurrency(payableTotal.toFixed(0), currencySymbol, currencyPosition)
                );

                $(".btn-totallabel h6").html(
                    'Total Amount : ' + formatCurrency(payableTotal.toFixed(0), currencySymbol,
                        currencyPosition)
                );

                $("input[name='subtotal']").val(baseSubtotal.toFixed(2));
                $("input[name='discount_amount']").val((totalProductDiscount + globalDiscountAmount).toFixed(
                    2));
                $("input[name='price_after_discount']").val((priceAfterGlobalDiscount + totalProductGst)
                    .toFixed(2));
                $("input[name='round_off']").val(roundOffAmount.toFixed(2));
                $("input[name='total']").val(roundedTotal.toFixed(0));
                if (typeof window.refreshPosPaymentUi === "function") {
                    window.refreshPosPaymentUi(false);
                }

                // ================= GST DISPLAY =================

                if (gstOption === "with") {
                    if ($(".gst-summary-row").length) {
                        $(".gst-summary-row")
                            .show()
                            .find("h6")
                            .html(formatCurrency(totalProductGst.toFixed(2), currencySymbol, currencyPosition));
                    }
                } else {
                    $(".gst-summary-row").hide();
                }

                if ((totalProductDiscount + globalDiscountAmount) > 0) {
                    $(".discount-summary-row").show();
                    $(".subtotal-summary-row").show();
                } else {
                    $(".discount-summary-row").hide();
                    $(".subtotal-summary-row").hide();
                }

                if (shipping > 0) {
                    $(".shipping-summary-row").show();
                } else {
                    $(".shipping-summary-row").hide();
                }

                // ================= LABOUR DISPLAY =================

                if (labourTotal > 0) {
                    $(".labour-summary-row")
                        .show()
                        .find("h6")
                        .html(formatCurrency(labourTotal.toFixed(2), currencySymbol, currencyPosition));
                } else {
                    $(".labour-summary-row").hide();
                }

                if (typeof window.updateRemainingBalance === "function") {
                    window.updateRemainingBalance();
                }
            }


            window.updateTotalItems = function() {
                let totalQty = 0;
                selectedItems.forEach(item => {
                    totalQty += parseQuantityValue(item.quantity);
                });
                $(".totalitem h4").text("Total items : " + formatQuantityValue(totalQty));
                renderSelectedItems();
                calculateTotals();
            }

            window.renderSelectedItems = renderSelectedItems;
            function renderSelectedItems() {
                try {
                    var $productTable = $(".product-table");
                    var productHtml = "";

                    // Get the global GST option
                    const globalGstOption = $("input[name='gst_option']:checked").val();

                    selectedItems.forEach(function(item) {
                        const hasProductGST = item.gst_option === "with_gst";
                        const gstRate = item.product_gst || null;
                        const showNoGstMessage = globalGstOption === "with" && !hasProductGST;

                        // Parse GST data if available AND global option is "with"
                        let gstDisplay = '';
                        let productGstTotal = 0;

                        const price = parseItemPriceValue(item.price);
                        const productTotal = price * item.quantity;

                        let gstIncludedTotal = productTotal;
                        let discountAmount = 0;
                        let finalProductTotal = productTotal;
                        let basePriceWithoutGst = productTotal;

                        if (globalGstOption === "with" && hasProductGST && gstRate) {

                            try {
                                const gstData = Array.isArray(gstRate) ? gstRate : JSON.parse(gstRate);

                                let totalGstRate = 0;
                                gstData.forEach(tax => {
                                    totalGstRate += parseFloat(tax.tax_rate) / 100;
                                });

                                let totalGstAmount = 0;
                                if (totalGstRate > 0) {
                                    basePriceWithoutGst = productTotal;
                                    totalGstAmount = productTotal * totalGstRate;
                                }

                                gstDisplay = gstData.map(tax => {
                                    const taxRate = parseFloat(tax.tax_rate) / 100;
                                    const taxAmount = totalGstRate > 0 ? totalGstAmount * (taxRate / totalGstRate) : 0;


                                productGstTotal += taxAmount;
                                // console.log(productGstTotal);
                                return `<small class="d-block" style="font-size: 11px; color: #666;">
                                ${tax.tax_name}: ${tax.tax_rate}%
                                (${formatCurrency(taxAmount.toFixed(2), '{{ $currency_symbol }}', '{{ $currency_position }}')})
                            </small>`;
                                }).join('');

                                // ✅ Exclusive: GST Included Total is Base + GST
                                gstIncludedTotal = productTotal + totalGstAmount;

                                // ✅ Discount on GST Included Amount
                                discountAmount = (gstIncludedTotal * (item.discount_percentage || 0)) / 100;

                                finalProductTotal = gstIncludedTotal - discountAmount;

                            } catch (e) {
                                console.error("GST parse error:", e);
                            }

                        } else {

                            // ✅ Discount on Base Amount
                            discountAmount = (productTotal * (item.discount_percentage || 0)) / 100;

                            finalProductTotal = productTotal - discountAmount;
                        }



                        // Build HTML for the product
                        productHtml += `
                                    <ul class="product-lists">
                                        <li>
                                            <div class="productimg">
                                                <div class="productimgs">
                                                    <img src="${item.image}" alt="${item.name}">
                                                </div>
                                                <div class="productcontet">
                                                    <h4 style="text-transform: capitalize;">${item.name}
                                                        <a href="javascript:void(0);" class="ms-2 edit-item" data-id="${item.id}">
                                                            <img src="{{ env('ImagePath') . '/admin/assets/img/icons/edit-5.svg' }}" alt="edit">
                                                        </a>
                                                    </h4>

                                                    ${(globalGstOption === "with" && hasProductGST && gstDisplay) ?
                                                        `<div class="gst-info" style="margin: 4px 0; padding: 8px; background: #f8f9fa; border-radius: 6px;width:135px;">
                                                                                                                          ${gstDisplay}
                                                                                                                                     <small class="d-block" id="gstrates"
                                                                                                                                    data-value="${productGstTotal.toFixed(2)}" style="font-weight: bold; color: #333; margin-top: 4px; font-size: 12px;">
                                                                                                                         Product GST Total: ${formatCurrency(productGstTotal.toFixed(2), '{{ $currency_symbol }}', '{{ $currency_position }}')}
                                                                                                                                                        </small>
                                                                                                                             <small
                                                                                                                             class="d-block"
                                                                                                                                 id="gstamount"
                                                                                                                                data-value="${gstIncludedTotal.toFixed(2)}"
                                                                                                                               style="font-weight: bold; color: #333; margin-top: 4px; font-size: 12px;"
                                                                                                                          >
                                                                                                                                Product GST WITH Total:
                                                                                                                                    ${formatCurrency(gstIncludedTotal.toFixed(2), '{{ $currency_symbol }}', '{{ $currency_position }}')}
                                                                                                                                    </small>
                                                                                                                          </div>`
                                                        : ''
                                                    }

                                                    ${showNoGstMessage ?
                                                        `<div class="gst-no-message">This product has no GST.</div>`
                                                        : ''
                                                    }

                                                    <div class="increment-decrement">
                                                        <div class="input-groups">
                                                            <input type="button" value="-" class="button-minus dec button" data-id="${item.id}">
                                                            <input type="text" name="quantity" value="${formatQuantityValue(item.quantity)}" class="quantity-field" data-id="${item.id}" data-stock="${item.stock}" inputmode="decimal" style="width: 50px;">
                                                            <input type="button" value="+" class="button-plus inc button" data-id="${item.id}">
                                                        </div>
                                                        <div style="font-size: 14px; color: #333; margin-bottom: 5px; margin-top:5px;">
                                                            <input
                                                                type="text"
                                                                class="product-price-input product-price-field"
                                                                data-id="${item.id}"
                                                                value="${price.toFixed(2)}"
                                                                min="0"
                                                                step="0.01">
                                                        </div>
                                                    </div>
                                                     </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="product-discount-box">
                                                    <div class="discount-field">
                                                        <label>Disc %</label>
                                                        <input type="text" class="product-discount-percentage" data-id="${item.id}" value="${item.discount_percentage || 0}" min="0" max="100">
                                                    </div>
                                                    <div class="discount-field">
                                                        <label>Disc Amt</label>
                                                        <input type="text"
                                                            class="product-discount-amount"
                                                            data-id="${item.id}"
                                                            value="${item.discount_amount || 0}"
                                                            min="0"
                                                            step="0.01">
                                                    </div>
                                                </div>
                                                ${(item.imei_no || (item.available_serials && item.available_serials.length > 0)) ? `
                                                <div class="serial-no-box" style="margin-top: 10px; padding: 0 10px;">
                                                    <label style="font-size: 12px; color: #555; font-weight: 600; display: block; margin-bottom: 2px;">IMEI No:</label>
                                                    <select class="form-control cart-imei-select" data-id="${item.id}" style="font-size: 12px; padding: 2px 5px; height: 35px;">
                                                        <option value="">Select IMEI</option>
                                                        ${(item.available_serials && item.available_serials.length > 0) ? 
                                                            item.available_serials.map(s => `<option value="${s}" ${s === item.imei_no ? 'selected' : ''}>${s}</option>`).join('')
                                                            : `<option value="${item.imei_no}">${item.imei_no}</option>`
                                                        }
                                                    </select>
                                                </div>
                                                ` : ''}
                                            </li>
                                     <li class="price">

    <div class="price-row sub-total" style="color:">
        <span>Sub Total:</span>
        <span>${formatCurrency(basePriceWithoutGst.toFixed(2), '{{ $currency_symbol }}', '{{ $currency_position }}')}</span>
    </div>

    ${(globalGstOption === "with" && hasProductGST) ? `
                                        <div class="price-row gst-inc">
                                            <span>GST Inc:</span>
                                            <span>${formatCurrency(productGstTotal.toFixed(2), '{{ $currency_symbol }}', '{{ $currency_position }}')}</span>
                                        </div>` : ''}

    ${discountAmount > 0 ? `
                                        <div class="price-row discount">
                                            <span>Disc Amt:</span>
                                            <span>- ${formatCurrency(discountAmount.toFixed(2), '{{ $currency_symbol }}', '{{ $currency_position }}')}</span>
                                        </div>` : ''}

    <div class="price-row final-total">
        <span>Final Total:</span>
        <span>${formatCurrency(finalProductTotal.toFixed(2), '{{ $currency_symbol }}', '{{ $currency_position }}')}</span>
    </div>

</li>

                                        <li class="delete-col">
                                            <a href="javascript:void(0);" class="remove-item" data-id="${item.id}" title="Remove item">
                                                <img src="{{ env('ImagePath') }}/admin/assets/img/icons/delete-2.svg" alt="delete">
                                            </a>
                                        </li>
                                    </ul>
                                `;
                    });

                    $productTable.html(productHtml);
                } catch (e) {
                    console.error("Error in renderSelectedItems:", e);
                }
            }

            // Product Discount Percentage
            $(document).on("change", ".product-discount-percentage", function() {

                // let id = $(this).data("id");
                let id = $(this).attr('data-id');
                let percent = parseFloat($(this).val()) || 0;

                if (percent < 0) percent = 0;
                if (percent > 100) percent = 100;

                let item = selectedItems.get(id);

                let baseTotal = getItemBaseAmount(item); // ✅ GST aware

                let discountAmount = (baseTotal * percent) / 100;

                item.discount_percentage = percent;
                item.discount_amount = discountAmount;

                selectedItems.set(id, item);

                $(`.product-discount-amount[data-id="${id}"]`)
                    .val(discountAmount.toFixed(2));

                updateTotalItems();
            });

            $(document).on("change", ".product-price-field", function() {

                let id = $(this).attr('data-id');
                let amount = parseFloat($(this).val());

                if (!selectedItems.has(id)) return;

                let item = selectedItems.get(id);

                if (isNaN(amount) || amount < 0) {
                    amount = 0;
                }

                item.price = formatCurrency(amount.toFixed(2), '{{ $currency_symbol }}', '{{ $currency_position }}');

                let baseTotal = getItemBaseAmount(item);

                if (item.discount_percentage > 0) {
                    item.discount_amount = (baseTotal * item.discount_percentage) / 100;
                } else if (item.discount_amount > 0) {
                    item.discount_amount = Math.min(item.discount_amount, baseTotal);
                    item.discount_percentage = baseTotal > 0 ? (item.discount_amount / baseTotal) * 100 : 0;
                }

                selectedItems.set(id, item);

                $(this).val(amount.toFixed(2));

                updateTotalItems();
            });

            // Product Discount Amount
            $(document).on("change", ".product-discount-amount", function() {

                // let id = $(this).data("id");
                let id = $(this).attr('data-id');
                let amount = parseFloat($(this).val()) || 0;

                if (amount < 0) amount = 0;

                let item = selectedItems.get(id);

                let baseTotal = getItemBaseAmount(item); // ✅ GST aware

                if (amount > baseTotal) amount = baseTotal;

                let percent = (amount / baseTotal) * 100;

                item.discount_percentage = percent;
                item.discount_amount = amount;

                selectedItems.set(id, item);

                $(`.product-discount-percentage[data-id="${id}"]`)
                    .val(percent.toFixed(2));

                updateTotalItems();
            });

            // Add this helper function if not already defined
            function formatCurrency(amount, symbol, position) {
                // Ensure the amount is a float and format it with commas
                let formattedAmount = parseFloat(amount).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                if (position === 'right') {
                    return formattedAmount + symbol;
                } else {
                    return symbol + formattedAmount;
                }
            }
            window.executeAddProduct = function(data, imei = null, available_serials = []) {
                var productIdStr = data.productIdStr;
                var productIdNum = data.productIdNum;

                if (selectedItems.has(productIdStr)) {
                    let item = selectedItems.get(productIdStr);
                    const nextQuantity = parseQuantityValue(item.quantity) + 1;

                    if (isQuotationModeEnabled() || data.isWarrantyCategory || item.isWarrantyCategory || nextQuantity <= parseQuantityValue(data.productStock)) {
                        item.quantity = nextQuantity;
                        if (imei) item.imei_no = imei;
                        if (available_serials && available_serials.length > 0) item.available_serials = available_serials;

                        // Recalculate discount
                        let baseAmount = getItemBaseAmount(item);
                        if (item.discount_percentage > 0) {
                            item.discount_amount = (baseAmount * item.discount_percentage) / 100;
                        }

                        selectedItems.set(productIdStr, item);
                        updateTotalItems();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock Limit',
                            text: 'Cannot add more than available stock (' + data.productStock + ')'
                        });
                    }
                    return;
                }

                selectedItems.set(productIdStr, {
                    id: productIdStr,
                    productId: productIdNum,
                    categoryId: data.categoryId,
                    name: data.productName,
                    price: data.productPrice,
                    image: data.productImage,
                    quantity: 1,
                    stock: data.productStock,
                    isWarrantyCategory: data.isWarrantyCategory || false,
                    gst_option: data.gstOption,
                    product_gst: data.productGst,
                    discount_percentage: 0,
                    discount_amount: 0,
                    imei_no: imei || null,
                    available_serials: available_serials
                });

                if (data.$checkIcon && data.$checkIcon.length) {
                    data.$checkIcon.addClass("selected");
                }
                updateTotalItems();
            }

            $(document).on("click", "#confirmImeiBtn", function() {
                var imei = $('#imeiNumberInput').val() || '';
                imei = imei.trim();
                

                var available_serials = [];
                $('#imeiNumberInput option').each(function() {
                    if ($(this).val()) {
                        available_serials.push($(this).val());
                    }
                });

                if (window.pendingImeiProduct) {
                    executeAddProduct(window.pendingImeiProduct, imei, available_serials);
                    window.pendingImeiProduct = null;
                }
                $('#imeiModal').modal('hide');
            });

            $(document).on("click", ".productset", function() {
                var $product = $(this);
                var productIdNum = $product.data("id");
                var productIdStr = String(productIdNum);
                var productName = $product.find("h4").text().trim();
                var productPrice = $product.find(".productsetcontent h6").text().trim();
                var categoryId = $product.find(".productsetcontent h1").text().trim();
                var productImage = $product.find("img").attr("src");
                var $checkIcon = $product.find(".check-product i");
                var productStock = parseQuantityValue($product.data("stock"));
                var gstOption = $product.data("gst-option") || "without_gst";

                var isWarrantyCategory = isWarrantyCategoryProduct({
                    is_warranty_category: $product.data("is-warranty-category"),
                    category_name: $product.data("category-name")
                });

                if (!isQuotationModeEnabled() && !isWarrantyCategory && productStock <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Out of Stock',
                        text: 'This product is currently out of stock.'
                    });
                    return;
                }

                // Get product GST data and parse it
                var productGst = $product.data("product-gst");
                if (productGst && productGst !== "null") {
                    try {
                        productGst = JSON.parse(productGst);
                    } catch (e) {
                        productGst = productGst;
                    }
                } else {
                    productGst = null;
                }

                var productData = {
                    productIdStr: productIdStr,
                    productIdNum: productIdNum,
                    categoryId: categoryId,
                    productName: productName,
                    productPrice: productPrice,
                    productImage: productImage,
                    productStock: productStock,
                    isWarrantyCategory: isWarrantyCategory,
                    gstOption: gstOption,
                    productGst: productGst,
                    $checkIcon: $checkIcon
                };

                // Check if it's mobile category
                if (String(categoryId).toLowerCase().includes('mobile') || String(productName).toLowerCase().includes('mobile')) {
                    $('#imeiProductName').text(productName);
                    if ($('#imeiNumberInput').hasClass("select2-hidden-accessible")) {
                        $('#imeiNumberInput').select2('destroy');
                    }
                    $('#imeiNumberInput').html('<option value="">Loading serials...</option>');
                    if ($.fn.select2) {
                        $('#imeiNumberInput').select2({
                            dropdownParent: $('#imeiModal'),
                            width: '100%'
                        });
                    }
                    window.pendingImeiProduct = productData;
                    $('#imeiModal').modal('show');

                    // Fetch available serial numbers
                    $.ajax({
                        url: '/api/get-available-serials/' + productIdNum,
                        type: 'GET',
                        headers: {
                            "Authorization": "Bearer " + authToken,
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            let selectHtml = '<option value="">Select serial number</option>';
                            if(response.status && response.serials.length > 0) {
                                response.serials.forEach(serial => {
                                    selectHtml += `<option value="${serial}">${serial}</option>`;
                                });
                            } else {
                                selectHtml = '<option value="">No serial numbers available</option>';
                            }
                            if ($('#imeiNumberInput').hasClass("select2-hidden-accessible")) {
                                $('#imeiNumberInput').select2('destroy');
                            }
                            $('#imeiNumberInput').html(selectHtml);
                            if ($.fn.select2) {
                                $('#imeiNumberInput').select2({
                                    dropdownParent: $('#imeiModal'),
                                    width: '100%'
                                });
                            }
                        },
                        error: function() {
                            if ($('#imeiNumberInput').hasClass("select2-hidden-accessible")) {
                                $('#imeiNumberInput').select2('destroy');
                            }
                            $('#imeiNumberInput').html('<option value="">Failed to load serials</option>');
                            if ($.fn.select2) {
                                $('#imeiNumberInput').select2({
                                    dropdownParent: $('#imeiModal'),
                                    width: '100%'
                                });
                            }
                        }
                    });

                    return;
                }

                executeAddProduct(productData);
            });

            function getItemBaseAmount(item) {

                let price = parseItemPriceValue(item.price);
                let productTotal = price * item.quantity;

                const globalGstOption = $("input[name='gst_option']:checked").val();

                if (
                    globalGstOption === "with" &&
                    item.gst_option === "with_gst" &&
                    item.product_gst
                ) {
                    try {
                        const gstData = Array.isArray(item.product_gst) ?
                            item.product_gst :
                            JSON.parse(item.product_gst);

                        let totalGstRate = 0;
                        gstData.forEach(tax => {
                            totalGstRate += (parseFloat(tax.tax_rate) / 100);
                        });

                        let productGstTotal = productTotal * totalGstRate;

                        return productTotal + productGstTotal; // ✅ GST Included Amount

                    } catch (e) {
                        return productTotal;
                    }
                }

                return productTotal; // Without GST
            }

            $(document).on("click", ".button-plus", function() {

                // var itemId = $(this).data("id");
                var itemId = $(this).attr('data-id');

                if (!selectedItems.has(itemId)) return;

                let item = selectedItems.get(itemId);

                const nextQuantity = parseQuantityValue(item.quantity) + 1;

                if (!isQuotationModeEnabled() && !item.isWarrantyCategory && nextQuantity > parseQuantityValue(item.stock)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock Limit Reached',
                        text: 'Cannot exceed available stock (' + item.stock + ')',
                        confirmButtonColor: '#ff9f43',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Increase quantity
                item.quantity = nextQuantity;

                // 🔥 Recalculate discount properly
                let baseAmount = getItemBaseAmount(item);

                if (item.discount_percentage > 0) {
                    item.discount_amount = (baseAmount * item.discount_percentage) / 100;
                } else if (item.discount_amount > 0) {
                    item.discount_percentage = (item.discount_amount / baseAmount) * 100;
                }

                selectedItems.set(itemId, item);

                updateTotalItems();
            });

            $(document).on("change", ".cart-imei-select", function() {
                var itemId = $(this).attr('data-id');
                var newImei = $(this).val();

                if (!selectedItems.has(itemId)) return;

                let item = selectedItems.get(itemId);
                item.imei_no = newImei;
                selectedItems.set(itemId, item);
                // We don't necessarily need to call updateTotalItems() and re-render everything
                // just for an IMEI change, but it keeps state consistent if we do,
                // or we can just leave it as is since the state is updated in the map.
            });

            $(document).on("change", ".quantity-field", function() {

                // var itemId = $(this).data("id");
                var itemId = $(this).attr('data-id');
                var newQty = parseQuantityValue($(this).val());

                if (!selectedItems.has(itemId)) return;

                let item = selectedItems.get(itemId);

                if (newQty < 0.01) {
                    newQty = 1;
                }

                if (!isQuotationModeEnabled() && !item.isWarrantyCategory && newQty > item.stock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock Quantity Exceeded',
                        text: 'Only ' + item.stock + ' quantity are available.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    newQty = item.stock;
                }

                item.quantity = newQty;

                // 🔥 Recalculate discount properly
                let baseAmount = getItemBaseAmount(item);

                if (item.discount_percentage > 0) {
                    item.discount_amount = (baseAmount * item.discount_percentage) / 100;
                } else if (item.discount_amount > 0) {
                    item.discount_percentage = (item.discount_amount / baseAmount) * 100;
                }

                selectedItems.set(itemId, item);

                $(this).val(formatQuantityValue(newQty));

                updateTotalItems();
            });

            $(document).on("click", ".button-minus", function() {

                // var itemId = $(this).data("id");
                var itemId = $(this).attr('data-id');

                if (!selectedItems.has(itemId)) return;

                let item = selectedItems.get(itemId);

                if (parseQuantityValue(item.quantity) <= 1) return;

                // Decrease quantity
                item.quantity = Math.max(1, parseQuantityValue(item.quantity) - 1);

                // 🔥 Recalculate discount properly
                let baseAmount = getItemBaseAmount(item);

                if (item.discount_percentage > 0) {
                    item.discount_amount = (baseAmount * item.discount_percentage) / 100;
                } else if (item.discount_amount > 0) {
                    item.discount_percentage = (item.discount_amount / baseAmount) * 100;
                }

                selectedItems.set(itemId, item);

                updateTotalItems();
            });

            $(document).on("click", ".remove-item", function(e) {
                e.preventDefault();
                var itemId = $(this).attr('data-id'); // string key
                var item = selectedItems.get(itemId);
                if (item) {
                    $(".productset[data-id='" + item.productId + "'] .check-product i").removeClass(
                        "selected");
                    selectedItems.delete(itemId);
                    updateTotalItems();
                }
            });

            $(".clear_items").click(function() {
                selectedItems.clear();
                $(".check-product i").removeClass("selected");
                updateTotalItems();
            });

            $("#shipping").on("input", function() {
                calculateTotals();
            });
            $("#tds_percentage").on("input", function() {
                calculateTotals();
            });
            $("#tds_percentage").on("blur", function() {
                if (!isTdsEnabled) {
                    return;
                }

                const rawValue = ($(this).val() || "").trim();
                if (rawValue === "") {
                    $("#tds_amount").val("0.00");
                    calculateTotals();
                    return;
                }

                const normalizedValue = Math.max(0, Math.min(100, parseFloat(rawValue) || 0));
                $(this).val(normalizedValue.toFixed(2));
                calculateTotals();
            });



            $(document).on("change", "input[name='gst_option']", function() {

                selectedItems.forEach(function(item, id) {

                    let baseAmount = getItemBaseAmount(item); // GST aware amount

                    if (item.discount_percentage > 0) {
                        item.discount_amount = (baseAmount * item.discount_percentage) / 100;
                    } else if (item.discount_amount > 0) {
                        item.discount_percentage = (item.discount_amount / baseAmount) * 100;
                    }

                    selectedItems.set(id, item);
                });

                updateTotalItems(); // 🔥 re-render + re-calc totals
            });
            $(document).on("change", "input[name='gst_option']", function() {
                let gstOption = $("input[name='gst_option']:checked").val();


                if (gstOption === "with") {
                    $(".taxList").show();
                } else {
                    $(".taxList").hide();
                }

                // Update product display and recalc totals
                renderSelectedItems();
                calculateTotals();
            });
            $(".btn-totallabel").click(function(event) {
                // ================= LABOUR ITEMS =================
                let labourItems = [];

                $(".labour-row").each(function() {

                    let labourId = $(this).find(".labour-select").val();
                    let qty = parseFloat($(this).find(".labour-qty").val()) || 0;
                    let price = parseFloat($(this).find(".labour-price").val()) || 0;

                    if (labourId && qty > 0) {

                        labourItems.push({
                            labour_item_id: labourId,
                            qty: qty,
                            price: price,
                            total: qty * price
                        });
                    }
                });

                event.preventDefault();
                var $btn = $(this); // cache the button
                var originalText = $btn.html();
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                // Show loading text and disable the button
                $btn.html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Placing Order...'
                ).prop('disabled', true);

                // Reset errors
                $(".error_customername, .error_customerphone, .error_peymentmethod, .error_discount, .error_bank, .error_paidtype, .error_paidamount")
                    .text(
                        "");
                $(".total-value .error_total").remove();

                // Get form values
                // let name = $("input[name='customer_name']").val().trim();
                let name = $("#customer_name option:selected").val();
                let phone = $("input[name='customer_phone']").val().trim();
                let orderDate = $("#order_date").val();
                let selectedPayment = $("input[name='payment_method']:checked").val();
                let bank_id = $("#bank_id").val();
                let subtotal = parseFloat($("input[name='subtotal']").val()) || 0;
                let total = parseFloat($("input[name='total']").val()) || 0;
                let discount_amount = parseFloat($("input[name='discount_amount']").val()) || 0;
                let remarks = $("#remarks").val().trim();
                let paymentRemarks = $("#posPaymentRemark").val().trim();
                let shipping = parseFloat($("#shipping").val()) || 0;
                let tds_percentage = parseFloat($("#tds_percentage").val()) || 0;
                let tds_amount = parseFloat($("#tds_amount").val()) || 0;
                let posPaymentMeta = (typeof window.getPosPaymentMeta === "function") ? window
                    .getPosPaymentMeta() : null;
                // Validation
                let isValid = true;

                // if (name === "") {
                //     $(".error_customername").text("Customer name is required").css("color", "red");
                //     isValid = false;
                // }

                if (phone === "") {
                    // $(".error_customerphone").text("Customer number is required").css("color", "red");
                    // isValid = false;
                } else if (!/^\d{10}$/.test(phone)) {
                    $(".error_customerphone").text("Enter a valid 10-digit phone number").css("color",
                        "red");
                    isValid = false;
                }

                // Determine if this is a quotation, advance receipt, or a sale
                let quotationStatus;
                if (isAdvanceReceiptMode) {
                    quotationStatus = 'advance_receipt';
                } else if ($('.quotationToggle:checked').length > 0) {
                    quotationStatus = 'quotation';
                } else {
                    quotationStatus = 'sales';
                }



                // require a payment method for sales and advance_receipt
                if (quotationStatus === 'sales' || quotationStatus === 'advance_receipt') {
                    if (!selectedPayment) {
                        $(".error_peymentmethod").text("Please select a payment method").css("color",
                            "red");
                        isValid = false;
                    } else if (quotationStatus === 'advance_receipt' && selectedPayment === 'pending') {
                        $(".error_peymentmethod").text("Advance Receipts cannot use 'Pay Later'").css("color",
                            "red");
                        isValid = false;
                    }
                }

                if ((quotationStatus === 'sales' || quotationStatus === 'advance_receipt') && selectedPayment && selectedPayment !== "pending") {
                    if (selectedPayment !== "emi" && (!posPaymentMeta || !posPaymentMeta.paidType)) {
                        $(".error_paidtype").text("Please select paid type.");
                        isValid = false;
                    } else if (selectedPayment !== "emi" && posPaymentMeta.paidAmount <= 0) {
                        $(".error_paidamount").text("Enter a valid payment amount.");
                        isValid = false;
                    } else if (selectedPayment !== "emi" && posPaymentMeta.paidType === "partially" && posPaymentMeta.paidAmount >=
                        total) {
                        $(".error_paidamount").text(
                            "Partial amount must be less than total amount.");
                        isValid = false;
                    }

                    if (selectedPayment === "cash+online") {
                        if (posPaymentMeta.cashAmount < 0 || posPaymentMeta.onlineAmount < 0) {
                            $(".error_paidamount").text("Negative amount is not allowed.");
                            isValid = false;
                        }

                        if ((posPaymentMeta.cashAmount + posPaymentMeta.onlineAmount) > total) {
                            $(".error_paidamount").text(
                                "Cash + Online total cannot exceed total amount.");
                            isValid = false;
                        }
                    }
                }

                // when quotation, we intentionally skip the generic check below
                // so do not run the unconditional validation block


                if (quotationStatus === 'sales' &&
                    (selectedPayment === "debit card" || selectedPayment === "scan" || selectedPayment ===
                        "cash+online") &&
                    !bank_id) {

                    $(".error_bank").text("Please select a bank").css("color", "red");
                    isValid = false;
                }

                if (typeof window.validatePosEmiFields === "function" && !window.validatePosEmiFields()) {
                    isValid = false;
                }

                // if (subtotal <= 0) {
                //     $(".total-value").append("<span class='error_total' style='color:red; display:block;'>Please select at least one product</span>");
                //     isValid = false;
                // }
                if (subtotal <= 0) {
                    $(".error_total")
                        .text("Please select at least one product")
                        .show();
                    isValid = false;
                } else {
                    $(".error_total").hide().text("");
                }


                // if (!isValid) return;
                if (!isValid) {
                    $btn.html(originalText).prop('disabled', false); // <-- important line
                    return;
                }


                // Prepare tax data
                let taxes = [];
                let gstOption = $("input[name='gst_option']:checked").val();

                // Prepare order items data
                // In the order submission section
                let orderItems = [];
                selectedItems.forEach(function(item) {
                    const price = parseItemPriceValue(item.price);
                    const itemTotal = price * item.quantity;

                    // Calculate product GST if applicable
                    let productGstDetails = [];
                    let productGstTotal = 0;

                    if (gstOption === "with" && item.gst_option === "with_gst" && item
                        .product_gst) {
                        try {

                            const gstData = Array.isArray(item.product_gst) ? item.product_gst :
                                JSON.parse(item.product_gst);
                            const totalGstRate = gstData.reduce((sum, tax) => sum + (parseFloat(tax.tax_rate || 0) / 100), 0);
                            const totalGstAmount = totalGstRate > 0 ? itemTotal * totalGstRate / (1 + totalGstRate) : 0;

                            gstData.forEach(tax => {
                                const taxRate = parseFloat(tax.tax_rate) / 100;
                                const taxAmount = totalGstRate > 0 ? totalGstAmount * (taxRate / totalGstRate) : 0;
                                productGstTotal += taxAmount;

                                productGstDetails.push({
                                    tax_name: tax.tax_name,
                                    tax_rate: tax.tax_rate,
                                    tax_amount: taxAmount
                                });
                            });
                        } catch (e) {
                            // console.error("Error calculating product GST:", e);
                        }
                    }

                    orderItems.push({
                        product_id: item.productId,
                        quantity: item.quantity,
                        categoryId: item.categoryId,
                        price: price,
                        discount_percentage: item.discount_percentage || 0,
                        discount_amount: item.discount_amount || 0,
                        total: itemTotal,
                        gst_option: item.gst_option,
                        product_gst_details: productGstDetails,
                        product_gst_total: productGstTotal,
                        final_price: itemTotal + productGstTotal,
                        imei_no: item.imei_no || null
                    });
                });

                // Prepare tax data
                // let taxes = [];
                // $(".taxList").each(function () {

                //     const taxName = $(this).find("h5").text().replace(" Tax", "");
                //     const taxAmount = parseFloat($(this).find("h6").text().replace(/[^0-9.]/g, ""));


                //     const taxRate = parseFloat($(this).find(".tax-value").data("rate"));
                //     taxes.push({
                //         tax_name: taxName,
                //         rate: taxRate,
                //         amount: taxAmount
                //     });
                // });
                if (gstOption === "with") {
                    $(".taxList").each(function() {
                        const taxName = $(this).find("h5").text().replace(" Tax", "");
                        const taxAmount = parseFloat($(this).find("h6").text().replace(/[^0-9.]/g,
                            "")) || 0;
                        const taxRate = parseFloat($(this).find(".tax-value").data("rate")) || 0;

                        taxes.push({
                            tax_name: taxName,
                            rate: taxRate,
                            amount: taxAmount
                        });
                    });
                } else {
                    taxes = []; // ✅ explicitly empty when "without GST"
                }

                // Prepare order data
                // In the order submission section, add gst_total to orderData
                // build base payload
                let orderData = {
                    convert_order_id: new URLSearchParams(window.location.search).get('convert_order_id') || null,
                    selectedSubAdminId: selectedSubAdminId,
                    customer_id: name,
                    customer_phone: phone,
                    customer_gst_number: ($('#pos_gst_number').val() || '').trim().toUpperCase(),
                    order_date: orderDate,
                    subtotal: subtotal,
                    discount: 0,
                    discount_amount: discount_amount,
                    tax: taxes,
                    gst_option: gstOption,
                    total: total,
                    items: orderItems,
                    remarks: remarks,
                    shipping: shipping,
                    tds_percentage: tds_percentage,
                    tds_amount: tds_amount,
                    labour_items: labourItems,
                    quotation_status: quotationStatus, // ✅ IMPORTANT
                    assigned_staff_id: $('#assigned_staff_id').val() || null,
                    order_type: $('#order_type').val() || 'self_pickup',
                };

                // add payment fields only when this is a sale or advance_receipt
                if (quotationStatus === 'sales' || quotationStatus === 'advance_receipt') {
                    orderData.payment_method = selectedPayment;
                    orderData.bank_id = bank_id;
                    if (selectedPayment === "pending") {
                        orderData.payment_amount = 0;
                        orderData.pending_amount = total;
                    } else {
                        orderData.payment_amount = posPaymentMeta ? posPaymentMeta.paidAmount : 0;
                        orderData.pending_amount = posPaymentMeta ? posPaymentMeta.pendingAmount : total;
                    }
                    orderData.payment_remarks = paymentRemarks;
                    if (selectedPayment === "emi" && posPaymentMeta) {
                        orderData.emi_down_payment = posPaymentMeta.emiDownPayment || 0;
                        orderData.emi_loan_amount = posPaymentMeta.emiLoanAmount || 0;
                        orderData.emi_interest_rate = posPaymentMeta.emiInterestRate || 0;
                        orderData.emi_tenure = posPaymentMeta.emiTenure || "";
                        orderData.emi_custom_tenure = posPaymentMeta.emiCustomTenure || "";
                        orderData.emi_monthly_amount = posPaymentMeta.emiMonthlyAmount || 0;
                        orderData.emi_aadhar_number = posPaymentMeta.emiAadharNumber || "";
                        orderData.emi_do_id = posPaymentMeta.emiDoId || "";
                        orderData.emi_pan_number = posPaymentMeta.emiPanNumber || "";
                        orderData.emi_guarantor_name = posPaymentMeta.emiGuarantorName || "";
                        orderData.bank_id = posPaymentMeta.emiBankId || "";
                    }

                    if (selectedPayment === "cash") {
                        orderData.paid_type = (posPaymentMeta && posPaymentMeta.paidType === "partially") ?
                            "cash_partially" : "cash_fully";
                        orderData.amount = posPaymentMeta ? posPaymentMeta.paidAmount : 0;
                    } else if (selectedPayment === "debit card" || selectedPayment === "scan") {
                        orderData.online_type = (posPaymentMeta && posPaymentMeta.paidType === "partially") ?
                            "online_partially" : "online_fully";
                        orderData.amount = posPaymentMeta ? posPaymentMeta.paidAmount : 0;
                    } else if (selectedPayment === "cash+online") {
                        orderData.cash_online_type = (posPaymentMeta && posPaymentMeta.paidType ===
                            "partially") ? "cash_online_partially" : "cash_online_fully";
                        orderData.cash_amount = posPaymentMeta ? posPaymentMeta.cashAmount : 0;
                        orderData.online_amount = posPaymentMeta ? posPaymentMeta.onlineAmount : 0;
                    }
                }





                // Submit order via AJAX
                $.ajax({
                    url: "/api/order_sale",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function(xhr) {
                        // Ensure CSRF token is included
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr(
                            'content'));
                    },
                    data: JSON.stringify(orderData),
                    success: function(response) {
                        // Re-enable and reset the button
                        $btn.html(originalText).prop('disabled', false);
                        if (response.status) {
                            orderId = response.order_id;
                            // alert("Order placed successfully!");
                            // location.reload();
                            const quotationStatus = document.getElementById('quotation_status')?.value || 'sales';
                            const successTitle = quotationStatus === 'advance_receipt'
                                ? 'Advance Receipt Created!'
                                : (quotationStatus === 'quotation' ? 'Quotation Saved!' : 'Order Placed!');
                            Swal.fire({
                                title: successTitle,
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43" // Set custom button color
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.open("/sales/invoice/pdf/" + orderId,
                                        "_blank");
                                    window.location.href = "/sales-invoice/" + orderId;
                                }
                            });
                            // Reset form
                            selectedItems.clear();
                            $(".check-product i").removeClass("selected");
                            $("input[name='customer_name'], input[name='customer_phone']").val("");
                            setPosOrderDate("{{ now()->format('Y-m-d') }}");
                            $("input[name='payment_method']").prop("checked", false);
                            $(".paymentmethod").removeClass("active");
                            if (typeof window.resetPosPaymentUi === "function") {
                                window.resetPosPaymentUi();
                            }
                            updateTotalItems();
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    // error: function(xhr, status, error) {
                    //     // Re-enable and reset the button
                    //     $btn.html(originalText).prop('disabled', false);

                    //     alert("Error placing order. Please try again.");
                    // }
                    error: function(xhr, status, error) {
                        $btn.html(originalText).prop('disabled', false);

                        // Try to extract error message from response
                        let response = xhr.responseJSON;

                        // Show all backend errors (including DB duplicate) as a SweetAlert
                        // Never show a "phone already registered" error for auto-filled phones
                        Swal.fire({
                            title: "Error",
                            text: response && response.message
                                ? response.message
                                : "Error placing order. Please try again.",
                            icon: "error",
                            confirmButtonColor: "#ff9f43"
                        });
                    }
                });
            });

            $(".owl-carousel").owlCarousel({
                items: 5,
                loop: false,
                nav: true,
                dots: false,
                margin: 10
            });

            // Handle category click
            $(".tabs li").click(function() {
                var categoryId = $(this).attr('id');


                // Remove active class from all tabs and tab contents
                $(".tabs li").removeClass('active');
                $(".tab_content").removeClass('active');

                // Add active class to clicked tab
                $(this).addClass('active');

                // Find the corresponding tab content
                var tabContentSelector = '.tab_content[data-tab="' + categoryId + '"]';
                var $tabContent = $(tabContentSelector);

                if ($tabContent.length) {
                    $tabContent.addClass('active');


                    // Check if content already loaded
                    if ($tabContent.find('.productset').length === 0) {
                        loadProductsByCategory(categoryId);
                    }
                } else {

                    // Create new tab content if not found
                    $('.tabs_container').append('<div class="tab_content active" data-tab="' + categoryId +
                        '"></div>');
                    loadProductsByCategory(categoryId);
                }
            });

            // Function to load products by category
            function loadProductsByCategory(categoryId) {

                $.ajax({
                    url: "/api/getProductsByCategory/" + categoryId,
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // console.log('respone:', response)
                        const currencySymbol = response.currency_symbol || 'Ã¢â€šÂ¹';
                        const currencyPosition = response.currency_position || 'left';


                        if (response.status) {
                            renderProducts(response.data, categoryId, currencySymbol, currencyPosition);
                        } else {
                            renderProducts([], categoryId, currencySymbol, currencyPosition);
                        }
                    },
                    error: function(xhr, status, error) {

                        renderProducts([], categoryId);
                    }
                });
            }

            // Function to render products
            function renderProducts(products, categoryId, currencySymbol, currencyPosition) {

                var productsHtml = '';

                if (products.length === 0) {
                    productsHtml = '<div class="col-12"><p>No products found in this category</p></div>';
                } else {
                    products.forEach(function(product) {
                        // Default image
                        let productImage =
                            '{{ env('ImagePath') . '/admin/assets/img/product/noimage.png' }}';

                        // Handle different image formats
                        if (product.image) {
                            try {
                                // Clean the image string
                                let cleanImageString = product.image.replace(/\\/g, '');
                                cleanImageString = cleanImageString.replace(/^"(.*)"$/, '$1');

                                let imageBasePath =
                                    '{{ env('ImagePath') }}'; // or config('app.url') if you prefer

                                if (cleanImageString.startsWith('[')) {
                                    let imagesArray = JSON.parse(cleanImageString);
                                    if (Array.isArray(imagesArray) && imagesArray.length > 0) {
                                        productImage = `${imageBasePath}/storage/${imagesArray[0]}`;
                                    }
                                } else {
                                    productImage = `${imageBasePath}/storage/${cleanImageString}`;
                                }
                            } catch (e) {

                            }
                        }
                        // Parse GST data
                        let gstBadge = '';
                        let gstOption = product.gst_option || "without_gst";
                        let productGst = product.product_gst || null;

                        if (gstOption === "with_gst" && productGst) {
                            try {
                                const gstData = JSON.parse(productGst);
                                const totalRate = gstData.reduce((sum, tax) => sum + parseFloat(tax
                                    .tax_rate), 0);
                                gstBadge = `<span class="gst-hover-badge with-gst">
                        GST: ${totalRate}%
                    </span>`;
                            } catch (e) {
                                gstBadge = `<span class="gst-hover-badge with-gst">
                        With GST
                    </span>`;
                            }
                        } else {
                            gstBadge = `<span class="gst-hover-badge no-gst">
                    No GST
                </span>`;
                        }

                        // Format price
                        let displayPrice = formatCurrency(product.price || 0, currencySymbol,
                            currencyPosition);

                        // Check if product is out of stock
                        let isWarrantyCategory = isWarrantyCategoryProduct(product);
                        let outOfStockBadge = '';
                        let disabledClass = '';
                        let addButtonDisabled = '';

                        if (Number(product.quantity) === 0 && !isWarrantyCategory) {
                            outOfStockBadge = `<div style="
                position: absolute;
                top: 10px;
                left: 10px;
                background: rgba(255,0,0,0.85);
                color: white;
                font-weight: 700;
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 12px;
                z-index: 10;
                ">Out of Stock</div>`;

                            if (!isQuotationModeEnabled()) {
                                disabledClass = 'disabled-product';
                                addButtonDisabled = 'disabled';
                            }
                        }

                        // ✅ ADD data-gst-option and data-product-gst ATTRIBUTES HERE
                        productsHtml += `
                <div class="col-lg-3 col-sm-6 d-flex position-relative">
                    <div class="productset flex-fill text-center ${disabledClass}"
                         data-id="${product.id}"
                         data-stock="${product.quantity}"
                         data-is-warranty-category="${isWarrantyCategory ? 1 : 0}"
                         data-category-name="${product.category_name || ''}"
                         data-gst-option="${gstOption}"
                         data-product-gst='${JSON.stringify(productGst).replace(/'/g, "&#39;")}'
                         style="position: relative;">
                        ${outOfStockBadge}
                        <div class="productsetimg">
                            <img src="${productImage}" alt="${product.name}" class="productsetimgin">
                            <h6>Qty: ${product.quantity || '0'}</h6>
                            ${gstBadge}
                        </div>
                        <div class="productsetcontent" style="padding: 0.5rem;">
                            <h4 style="font-size: 14px;text-transform: capitalize; font-weight: 600; margin: 0.5rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${product.name}
                            </h4>
                             <h5 class="text-dark" style="text-transform: capitalize; ">${product.unit || ''}</h5>
                            <h1 class="d-none">${product.categoryId || 'Uncategorized'}</h1>

                            <h6>${displayPrice}</h6>
                        </div>
                        <div class="productsetbtn">
                            <button class="btn btn-added" ${addButtonDisabled}>+ Add</button>
                        </div>
                    </div>
                </div>
            `;
                    });
                }

                // Update the tab content
                var $tabContent = $('.tab_content[data-tab="' + categoryId + '"]');
                if ($tabContent.length) {
                    $tabContent.html('<div class="row">' + productsHtml + '</div>');
                    window.refreshProductAvailabilityUI();

                } else {

                }
            }



            // Load products for the first category by default
            var firstCategory = $(".tabs li:first");
            if (firstCategory.length) {
                var firstCategoryId = firstCategory.attr('id');
                firstCategory.addClass('active');
                var firstTabContent = $('.tab_content[data-tab="' + firstCategoryId + '"]');

                if (firstTabContent.length) {
                    firstTabContent.addClass('active');
                } else {
                    // Create tab content if it doesn't exist
                    $('.tabs_container').append('<div class="tab_content active" data-tab="' + firstCategoryId +
                        '"></div>');
                }

                // Load data for first category
                loadProductsByCategory(firstCategoryId);
            }


            // Initialize with empty state
            startConnectedDeviceScannerSync();
            updateTotalItems();
        });
        $(".select2").not('#customer_name').select2({
            tags: true,
        });

        // ==================== CUSTOMER SELECT2 — TYPE & AUTO-SAVE ====================
        $('#customer_name').select2({
            tags: true,            // allows typing a new value
            width: '100%',
            createTag: function (params) {
                var term = $.trim(params.term);
                if (!term) return null;
                return {
                    id:   term,            // use the typed name as a temporary id
                    text: term,
                    newTag: true           // flag so we know it's a new entry
                };
            },
            templateResult: function (data) {
                if (data.newTag) {
                    return $('<span style="color:white;">' + data.text + '</span>');
                }
                return data.text;
            },
            matcher: function (params, data) {
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
        });

        // On select: fill phone and GST for existing, or auto-save for new
        $('#customer_name').on('select2:select', function (e) {
            var data = e.params.data;

            // Existing customer — fill phone and GST then done
            if (!data.newTag) {
                var phone = $(data.element).data('phone') || '';
                var gst   = $(data.element).data('gst')  || '';
                $('#customer_phone').val(phone);
                $('#pos_gst_number').val(gst);
                return;
            }

            // New name typed — auto-save to DB
            var newName  = data.text.trim();
            var $select  = $(this);

            // Temporarily disable while saving
            $select.prop('disabled', true);
            $(".error_customername").text('');

            // Generate a safe dummy phone that can never collide with real Indian numbers
            // Real Indian phones never start with 0 (they start with 6-9)
            // Format: 00000XXXXX where XXXXX is random 5 digits
            function generateSafeDummyPhone() {
                var rand = Math.floor(10000 + Math.random() * 90000); // 5-digit random
                return '00000' + rand;
            }

            function attemptCreateCustomer(phone, retryCount) {
                var formData = new FormData();
                formData.append('customer_name', newName);
                formData.append('phone', phone);

                var sid = localStorage.getItem('selectedSubAdminId');
                if (sid && sid !== 'null' && sid !== 'undefined') {
                    formData.append('selectedSubAdminId', sid);
                }

                $.ajax({
                    url: '/api/createCustomer',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + localStorage.getItem('authToken')
                    },
                    success: function (response) {
                        if (response.status) {
                            // Replace the temp tag option with a real one using the returned ID
                            var realId = response.customer_id || response.id || newName;
                            // Remove the temp tag option
                            $select.find('option[value="' + newName + '"]').remove();
                            // Add a proper option and select it
                            var $opt = new Option(newName, realId, true, true);
                            $($opt).attr('data-phone', '');
                            $select.append($opt).trigger('change');
                            $('#customer_phone').val('');
                        } else {
                            // If phone collision, retry once with new dummy
                            if (retryCount > 0 && response.message && response.message.toLowerCase().includes('phone')) {
                                return attemptCreateCustomer(generateSafeDummyPhone(), retryCount - 1);
                            }
                            // Deselect and show error
                            $select.val('').trigger('change');
                            $(".error_customername")
                                .text(response.message || 'Failed to save customer.')
                                .css('color', 'red');
                        }
                    },
                    error: function (xhr) {
                        var errors = (xhr.responseJSON || {}).errors || {};
                        // If phone collision error on auto-save, retry silently with a new dummy
                        if (retryCount > 0 && errors.phone) {
                            return attemptCreateCustomer(generateSafeDummyPhone(), retryCount - 1);
                        }
                        $select.val('').trigger('change');
                        var msg = errors.customer_name
                            ? errors.customer_name[0]
                            : (errors.phone
                                ? 'Failed to create customer (phone conflict). Please try again.'
                                : ((xhr.responseJSON || {}).message || 'Failed to save customer.'));
                        $(".error_customername").text(msg).css('color', 'red');
                    },
                    complete: function () {
                        $select.prop('disabled', false);
                    }
                });
            }

            attemptCreateCustomer(generateSafeDummyPhone(), 3);
        });

        // Fill phone and GST when selecting an existing customer via the change event
        $('#customer_name').on('change', function () {
            var val = $(this).val();
            var $selected = $(this).find('option[value="' + val + '"]');
            // Only fill phone/gst for real existing customers (data-phone attribute set)
            if ($selected.attr('data-phone') !== undefined) {
                $('#customer_phone').val($selected.data('phone') || '');
                $('#pos_gst_number').val($selected.data('gst') || '');
            }
        });

        // ===== GST API LOOKUP FOR POS (same as addcustomer page) =====
        (function () {
            var authToken = localStorage.getItem('authToken');
            var $gstInput  = $('#pos_gst_number');
            var $gstLoader = $('#pos-gst-loader');
            var $gstMsg    = $('#pos-gst-msg');

            function setGstMsg(msg, color) {
                $gstMsg.html('<span style="color:' + (color || '#333') + ';">' + msg + '</span>');
            }

            $gstInput.on('input', function () {
                // Normalize: uppercase, alphanumeric only, max 15
                var raw = $(this).val().toUpperCase().replace(/[^0-9A-Z]/g, '').substring(0, 15);
                $(this).val(raw);

                $gstMsg.html('');

                if (raw.length < 15) return; // wait until 15 chars

                // Check: does an existing customer already have this GST?
                var $existingOpt = $('#customer_name option').filter(function () {
                    return ($(this).data('gst') || '').toUpperCase() === raw;
                }).first();

                if ($existingOpt.length) {
                    // Select existing customer — no duplicate needed
                    var existingVal = $existingOpt.val();
                    $('#customer_name').val(existingVal).trigger('change');
                    // Also re-trigger select2 selection display
                    if ($('#customer_name').data('select2')) {
                        $('#customer_name').val(existingVal).trigger('change.select2');
                    }
                    setGstMsg('<i class="fas fa-check-circle"></i> Existing customer found & selected.', '#28a745');
                    return;
                }

                // Fetch from GST API
                $gstLoader.show();
                $gstInput.addClass('loading').prop('readonly', true);
                setGstMsg('<i class="fas fa-spinner fa-spin"></i> Fetching GST details...', '#1B2850');

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
                        if (res.error || !res.legal_name) {
                            setGstMsg('<i class="fas fa-times-circle"></i> GST details not found.', '#dc3545');
                            return;
                        }

                        var companyName = (res.company_name || '').trim();
                        var legalName = (res.legal_name || '').trim();
                        var fetchedName = companyName ? companyName : legalName;

                        // Double-check: search existing customers by name too
                        var $nameOpt = $('#customer_name option').filter(function () {
                            return $(this).text().toLowerCase().indexOf(fetchedName.toLowerCase()) > -1;
                        }).first();

                        if ($nameOpt.length) {
                            // Existing customer matched by name — select & update GST attr
                            $nameOpt.attr('data-gst', raw);
                            var existingVal = $nameOpt.val();
                            $('#customer_name').val(existingVal).trigger('change');
                            if ($('#customer_name').data('select2')) {
                                $('#customer_name').val(existingVal).trigger('change.select2');
                            }
                            setGstMsg('<i class="fas fa-check-circle"></i> Existing customer matched & selected.', '#28a745');
                        } else {
                            // New customer — pre-fill name in select2 as a typed new tag
                            var $newOpt = new Option(fetchedName, fetchedName, true, true);
                            $($newOpt).data('newTag', true).data('phone', '').data('gst', raw);
                            $('#customer_name').append($newOpt).trigger('change');
                            if ($('#customer_name').data('select2')) {
                                $('#customer_name').val(fetchedName).trigger('change.select2');
                            }
                            setGstMsg('<i class="fas fa-info-circle"></i> New customer: "' + fetchedName + '". Please fill phone & save bill.', '#ff9f43');
                        }
                    },
                    error: function () {
                        setGstMsg('<i class="fas fa-times-circle"></i> Failed to fetch GST details.', '#dc3545');
                    },
                    complete: function () {
                        $gstLoader.hide();
                        $gstInput.removeClass('loading').prop('readonly', false);
                    }
                });
            });
        })();
        $(document).ready(function() {
            const $searchInput = $('#customerSearch1');
            const $resultBox = $('#searchResults1');
            const addBankModalElement = document.getElementById('addBankModal');
            const addBankModal = addBankModalElement ? new bootstrap.Modal(addBankModalElement) : null;

            @if ($showNewBillModal)
                const newBillTypeModalElement = document.getElementById('newBillTypeModal');
                if (newBillTypeModalElement) {
                    const newBillTypeModal = new bootstrap.Modal(newBillTypeModalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    newBillTypeModal.show();
                }
            @endif

            $('#openAddBankModal').on('click', function() {
                resetPosAddBankForm();
                if (addBankModal) {
                    addBankModal.show();
                }
            });

            // EMI box "Add Bank" button — reuses the same modal
            $('#openAddBankModalEmi').on('click', function() {
                resetPosAddBankForm();
                if (addBankModal) {
                    addBankModal.show();
                }
            });

            $('#addBankForm').on('submit', function(e) {
                e.preventDefault();

                $('#addBankForm .text-danger').text('');

                const formData = new FormData(this);
                if (selectedSubAdminId && selectedSubAdminId !== "null" && selectedSubAdminId !==
                    "undefined") {
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
                        upsertPosBankOption(response.data || null);
                        $(".error_bank").text("");

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
                        $('#addAccountNumberError').text(errors.account_number ? errors
                            .account_number[0] : '');
                        $('#addIfscCodeError').text(errors.ifsc_code ? errors.ifsc_code[0] : '');
                        $('#addBranchNameError').text(errors.branch_name ? errors.branch_name[0] :
                            '');
                        $('#addOpeningBalanceError').text(errors.opening_balance ? errors
                            .opening_balance[0] : '');
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

            $searchInput.on('input', function() {
                const query = $(this).val().trim();

                if (query.length < 1) {
                    $resultBox.hide();
                    return;
                }

                $.ajax({
                    url: `/search-users`,
                    method: 'GET',
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        query: query
                    },
                    dataType: 'json',
                    success: function(data) {
                        $resultBox.empty();

                        let hasResults = false;

                        // Users section
                        // (add user search results here if needed)

                        // Products section
                        if (data.products && data.products.length > 0) {
                            hasResults = true;
                            $resultBox.append(
                                `<div class="list-group-item fw-bold mt-2 d-flex justify-content-between align-items-center">
            <span>Products</span>
            <button type="button" class="btn-close close-search-result"></button>
        </div>`);
                            data.products.forEach(product => {
                                // In the search AJAX success handler, update the product button:
                                $resultBox.append(`
                                <div class="list-group-item d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img src="${product.image}" alt="Product Image" class="rounded me-2" style="width:30px; height:30px; object-fit: cover;">
                                        <div>
                                            <strong>${product.name ?? 'N/A'}</strong><br>
                                            <small>Price: ${product.price_formatted ?? product.price ?? 'N/A'}</small>
                                            ${product.gst_option === 'with_gst' ?
                                                `<br><small style="color: green;">With GST</small>` :
                                                `<br><small style="color: gray;">Without GST</small>`
                                            }
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-primary add-product"
                                        data-id="${product.id}"
                                        data-name="${product.name}"
                                        data-price="${product.price}"
                                        data-image="${product.image}"
                                        data-stock="${product.quantity ?? product.stock ?? product.current_stock ?? product.available_qty ?? 0}"
                                        data-category="${product.category_id ?? 0}"
                                        data-category-name="${product.category_name ?? ''}"
                                        data-is-warranty-category="${isWarrantyCategoryProduct(product) ? 1 : 0}"
                                        data-gst-option="${product.gst_option || 'without_gst'}"
                                        data-product-gst='${product.product_gst || ''}'>+ Add</button>
                                </div>
                            `);
                            });
                        }

                        // Orders section
                        // (add orders section if needed)

                        if (!hasResults) {
                            $resultBox.html(
                                '<div class="list-group-item">No results found</div>');
                        }

                        $resultBox.show();
                    },
                    error: function(xhr, status, error) {

                        $resultBox.hide();
                    }
                });
            });

                $(document).on('click', '.close-search-result', function () {
                    $resultBox.hide().empty();
                });

            $(document).on("click", ".add-product", function() {
                const $btn = $(this);
                const productIdNum = $btn.data("id"); // number
                const productIdStr = String(productIdNum); // string key
                const productName = $btn.data("name");
                const productPrice = $btn.data("price");
                const productImage = $btn.data("image");
                const productStock = parseQuantityValue($btn.data("stock"));
                const categoryId = $btn.data("category");
                const isWarrantyCategory = isWarrantyCategoryProduct({
                    is_warranty_category: $btn.data("is-warranty-category"),
                    category_name: $btn.data("category-name")
                });
                const gstOptionItem = $btn.data("gst-option") || "without_gst";
                let productGst = $btn.data("product-gst") || null;

                if (!isQuotationModeEnabled() && !isWarrantyCategory && productStock <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Out of Stock',
                        text: 'This product is currently out of stock.'
                    });
                    return;
                }

                if (productGst && productGst !== "null" && typeof productGst === 'string' && productGst
                    .startsWith('[')) {
                    try {
                        productGst = JSON.parse(productGst);
                    } catch (e) {
                        // console.error("Error parsing GST data:", e);
                    }
                }

                var productData = {
                    productIdStr: productIdStr,
                    productIdNum: productIdNum,
                    categoryId: categoryId,
                    productName: productName,
                    productPrice: productPrice,
                    productImage: productImage,
                    productStock: productStock,
                    isWarrantyCategory: isWarrantyCategory,
                    gstOption: gstOptionItem,
                    productGst: productGst,
                    $checkIcon: null
                };

                // Check if it's mobile category
                if (String(categoryId).toLowerCase().includes('mobile') || String(productName).toLowerCase().includes('mobile')) {
                    $('#imeiProductName').text(productName);
                    $('#imeiNumberInput').val('');
                    window.pendingImeiProduct = productData;
                    $('#imeiModal').modal('show');
                    $btn.prop("disabled", true).text("Added");
                    $searchInput.val('');
                    $resultBox.hide().empty();
                    return;
                }

                window.executeAddProduct(productData);
                $btn.prop("disabled", true).text("Added");
                $searchInput.val('');
                $resultBox.hide().empty();
            });
            // isAdvanceReceiptMode is already declared globally above

            $('.advanceReceiptToggle').on('change', function() {
                const isChecked = $(this).is(':checked');
                syncAdvanceReceiptMode(isChecked);
                if (isChecked) {
                    syncQuotationMode(false); // mutually exclusive
                }
            });

            $('.quotationToggle').on('change', function() {
                const isChecked = $(this).is(':checked');
                syncQuotationMode(isChecked);
                if (isChecked) {
                    syncAdvanceReceiptMode(false); // mutually exclusive
                }
            });

            function syncAdvanceReceiptMode(isChecked) {
                $('.advanceReceiptToggle').prop('checked', isChecked);
                isAdvanceReceiptMode = isChecked;
                $('#quotation_status').val(isChecked ? 'advance_receipt' : ($('.quotationToggle').is(':checked') ? 'quotation' : 'sales'));
                updatePosPageTitle($('.quotationToggle').is(':checked'));
                
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('sale_type', isChecked ? 'advance_receipt' : ($('.quotationToggle').is(':checked') ? 'quotation' : 'sales'));
                window.history.replaceState({}, '', currentUrl.toString());

                if (isChecked) {
                    $('#advanceReceiptFields').show();
                    $('#emiPaymentOption').hide();
                    $('.btn-totallabel h5').html('Generate Advance Receipt <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16" style="margin-bottom: 0.1rem;"><path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8" /></svg>');
                } else {
                    $('#advanceReceiptFields').hide();
                    $('#emiPaymentOption').show();
                }

                if (typeof window.refreshPosPaymentUi === "function") {
                    window.refreshPosPaymentUi(true);
                }
            }

            function syncQuotationMode(isChecked) {
                $('.quotationToggle').prop('checked', isChecked);
                $('#quotation_status').val(isChecked ? 'quotation' : ($('.advanceReceiptToggle').is(':checked') ? 'advance_receipt' : 'sales'));
                updatePosPageTitle(isChecked);

                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('sale_type', isChecked ? 'quotation' : ($('.advanceReceiptToggle').is(':checked') ? 'advance_receipt' : 'sales'));
                window.history.replaceState({}, '', currentUrl.toString());

                if (isChecked) {
                    $('#paymentSection').hide();
                    $("input[name='payment_method']").prop('checked', false);
                    $('.paymentmethod').removeClass('active');
                    if (typeof window.resetPosPaymentUi === "function") {
                        window.resetPosPaymentUi();
                    } else {
                        $('#posPaidTypeBox, #posPaidAmountFields, #cashOnlineBox, #bankSelectionBox').hide();
                    }
                    $('.setvaluecash').hide();
                    $('input[name="gst_option"][value="without"]').prop('checked', true).trigger('change');
                } else {
                    $('#paymentSection').show();
                    $('.setvaluecash').show();
                    if (typeof window.refreshPosPaymentUi === "function") {
                        window.refreshPosPaymentUi(true);
                    }
                    $('input[name="gst_option"][value="without"]').prop('checked', true).trigger('change');
                }

                window.refreshProductAvailabilityUI();
            }

            // Initial Sync
            syncQuotationMode(new URLSearchParams(window.location.search).get('sale_type') === 'quotation' ||
                $('.quotationToggle:checked').length > 0);
            syncAdvanceReceiptMode(new URLSearchParams(window.location.search).get('sale_type') === 'advance_receipt' ||
                $('.advanceReceiptToggle:checked').length > 0);
            // Optional: Hide dropdown on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest($searchInput).length && !$(e.target).closest($resultBox).length) {
                    $resultBox.hide();
                }
            });
        });
    </script>

    <script>
    /* =====================================================
       Payment Panel — Drag Handle + Minimize/Restore
       ===================================================== */
    (function () {
        var panel     = document.getElementById('paymentPanel');
        var handle    = document.getElementById('paymentPanelHandle');
        var body      = document.getElementById('paymentPanelBody');
        var minBtn    = document.getElementById('paymentPanelMinBtn');
        var minIcon   = document.getElementById('panelMinIcon');

        if (!panel || !handle) return;

        var isDragging  = false;
        var startY      = 0;
        var startTop    = 0;
        var isCollapsed = false;

        /* ── Only apply dragging when the panel is position:fixed (mobile/tablet) ── */
        function isPanelFixed() {
            return getComputedStyle(panel).position === 'fixed';
        }

        /* ── Initialise top position ── */
        function initPanelTop() {
            if (!isPanelFixed()) return;
            if (!panel.style.top) {
                var rect = panel.getBoundingClientRect();
                panel.style.top    = rect.top + 'px';
                panel.style.bottom = 'auto';
            }
        }

        /* ── Mouse drag ── */
        handle.addEventListener('mousedown', function (e) {
            if (!isPanelFixed()) return;
            if (e.target === minBtn || minBtn.contains(e.target)) return;
            initPanelTop();
            isDragging = true;
            startY     = e.clientY;
            startTop   = parseInt(panel.style.top, 10) || panel.getBoundingClientRect().top;
            document.body.style.userSelect = 'none';
            e.preventDefault();
        });

        document.addEventListener('mousemove', function (e) {
            if (!isDragging) return;
            var delta  = e.clientY - startY;
            var newTop = startTop + delta;
            // Clamp: don't drag above 60px or below (viewport - 60px)
            newTop = Math.max(60, Math.min(window.innerHeight - 60, newTop));
            panel.style.top    = newTop + 'px';
            panel.style.bottom = 'auto';
        });

        document.addEventListener('mouseup', function () {
            if (isDragging) {
                isDragging = false;
                document.body.style.userSelect = '';
            }
        });

        /* ── Touch drag (mobile) ── */
        handle.addEventListener('touchstart', function (e) {
            if (!isPanelFixed()) return;
            if (e.target === minBtn || minBtn.contains(e.target)) return;
            initPanelTop();
            isDragging = true;
            startY     = e.touches[0].clientY;
            startTop   = parseInt(panel.style.top, 10) || panel.getBoundingClientRect().top;
        }, { passive: true });

        document.addEventListener('touchmove', function (e) {
            if (!isDragging) return;
            var delta  = e.touches[0].clientY - startY;
            var newTop = startTop + delta;
            newTop = Math.max(60, Math.min(window.innerHeight - 60, newTop));
            panel.style.top    = newTop + 'px';
            panel.style.bottom = 'auto';
        }, { passive: true });

        document.addEventListener('touchend', function () {
            isDragging = false;
        });

        /* ── Minimize / Restore button ── */
        var expandIcon = '<path d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/>';
        var collapseIcon = '<path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>';

        if (minBtn) {
            minBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                isCollapsed = !isCollapsed;
                if (isCollapsed) {
                    body.style.maxHeight = body.scrollHeight + 'px';
                    requestAnimationFrame(function () {
                        body.classList.add('panel-collapsed');
                    });
                    minIcon.innerHTML = collapseIcon;
                } else {
                    body.classList.remove('panel-collapsed');
                    body.style.maxHeight = body.scrollHeight + 'px';
                    setTimeout(function () { body.style.maxHeight = ''; }, 320);
                    minIcon.innerHTML = expandIcon;
                }
            });
        }
    })();
    </script>
    
    <script>
    $(document).ready(function() {
        const convertOrder = @json($convertOrder ?? null);
        
        if (convertOrder) {
            // Update UI title to indicate we are converting a sale
            $('.page-title').text('Convert Sale: ' + (convertOrder.order_number || ''));

            // Force the quotationStatus to be "sales"
            if (typeof syncQuotationMode === 'function') {
                syncQuotationMode(false);
                syncAdvanceReceiptMode(false);
            }
            $('#quotation_status').val('sales');
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sale_type', 'sales');
            window.history.replaceState({}, '', currentUrl.toString());

            // Prefill customer and assigned staff
            if (convertOrder.user_id) {
                setTimeout(() => {
                    $('#customer_name').val(convertOrder.user_id).trigger('change');
                }, 500);
            }
            if (convertOrder.staff_id) {
                setTimeout(() => {
                    $('#assigned_staff_id').val(convertOrder.staff_id).trigger('change');
                }, 500);
            }

            // Load products into the cart
            if (convertOrder.order_items && Array.isArray(convertOrder.order_items)) {
                convertOrder.order_items.forEach(item => {
                    if (item.product) {
                        // Create a faux product object structured for addProductToCart
                        let product = { ...item.product };
                        // Force the POS to use the locked price
                        product.sale_price = item.price;
                        product.discount_percentage = item.discount_percentage;
                        
                        // Add product to cart
                        addProductToCart(product);
                        
                        // Now quickly override the quantity and lock its price
                        const stringId = String(product.id);
                        if (selectedItems.has(stringId)) {
                            let cartItem = selectedItems.get(stringId);
                            cartItem.quantity = Number(item.quantity);
                            // Lock price & discount so user cannot edit them
                            cartItem.is_locked = true;
                            selectedItems.set(stringId, cartItem);
                        }
                    }
                });
                updateTotalItems();
            }
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

        function setQacMode(mode) {           // 'add' | 'edit'
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
            $('#qac_country').val(c.country            || (c.details && c.details.country) || '');
            $('#qac_city').val(c.city                  || (c.details && c.details.city)    || '');
            $('#qac_address').val(c.address            || (c.details && c.details.address) || '');
            $('#qac_delivery_address').val(c.delivery_address || (c.details && c.details.delivery_address) || '');

            var addressVal = $('#qac_address').val();
            var deliveryAddressVal = $('#qac_delivery_address').val();
            if (addressVal && addressVal === deliveryAddressVal) {
                $('#qac_use_same_address').prop('checked', true);
            } else {
                $('#qac_use_same_address').prop('checked', false);
            }
        }

        /* ── Show/hide edit pencil button ── */
        function syncEditBtn() {
            var val = $('#customer_name').val();
            // Only show for real numeric IDs (not empty, not a temp tag string)
            var isReal = val && /^\d+$/.test(String(val));
            $('#openEditCustomerBtn').css('display', isReal ? 'inline-flex' : 'none');
        }

        // Run on page load and on every change
        syncEditBtn();
        $(document).on('change', '#customer_name', syncEditBtn);

        /* ── Open ADD modal ── */
        $(document).on('click', '#openQuickAddCustomerBtn', function () {
            resetQacForm();
            setQacMode('add');
            if (qacModal) qacModal.show();
        });

        /* ── Open EDIT modal ── */
        $(document).on('click', '#openEditCustomerBtn', function () {
            var customerId = $('#customer_name').val();
            if (!customerId || !/^\d+$/.test(String(customerId))) return;

            resetQacForm();
            setQacMode('edit');
            $('#qac_edit_id').val(customerId);

            // Show a loading state in the modal body
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
                        // Merge details sub-object into top level for convenience
                        if (c.details) {
                            c.country = c.country || c.details.country;
                            c.city    = c.city    || c.details.city;
                            c.address = c.address || c.details.address;
                            c.delivery_address = c.delivery_address || c.details.delivery_address;
                        }
                        fillQacForm(c);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Could not load customer details.' });
                        if (qacModal) qacModal.hide();
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to fetch customer details.' });
                    if (qacModal) qacModal.hide();
                },
                complete: function () {
                    $('#qacSaveBtn').prop('disabled', false);
                    $('#qacBtnSpinner').addClass('d-none');
                }
            });
        });

        /* ── State code → state name ── */
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
        $(document).on('input', '#qac_phone', function () {
            this.value = this.value.replace(/\D/g, '').substring(0, 10);
        });
        $(document).on('input', '#qac_alternate_phone', function () {
            this.value = this.value.replace(/\D/g, '').substring(0, 10);
        });

        /* ── Form submit (Add or Edit) ── */
        $(document).on('submit', '#quickAddCustomerForm', function (e) {
            e.preventDefault();
            $('.qac-error').html('');

            var editId  = $('#qac_edit_id').val();
            var isEdit  = !!editId;

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

            if (isEdit) {
                formData.append('_method', 'POST');   // updateCustomer uses POST
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
                        var gst     = $('#qac_gst_number').val().trim();
                        var label   = (company || name) + (phone ? ' - ' + phone : '');

                        var $select = $('#customer_name');

                        if (isEdit) {
                            // 1. Update the <option> in the DOM
                            var $existing = $select.find('option[value="' + editId + '"]');
                            if ($existing.length) {
                                $existing.text(label)
                                         .attr('data-phone', phone)
                                         .attr('data-gst', gst);
                            } else {
                                // Option not in DOM (rare) — add it
                                $select.append(
                                    $('<option>', { value: editId, text: label })
                                        .attr('data-phone', phone)
                                        .attr('data-gst', gst)
                                );
                            }

                            // 2. Destroy & reinit select2 so it re-reads the DOM options,
                            //    then re-select the updated customer
                            var select2Options = $select.data('select2') && $select.data('select2').options
                                ? $select.data('select2').options.options
                                : null;

                            if ($select.data('select2')) {
                                $select.select2('destroy');
                            }

                            // Reinitialise with the same config used at page-load
                            $select.select2({
                                tags: true,
                                width: '100%',
                                createTag: function (params) {
                                    var term = $.trim(params.term);
                                    if (!term) return null;
                                    return { id: term, text: term, newTag: true };
                                },
                                templateResult: function (data) {
                                    if (data.newTag) return $('<span style="color:white;">' + data.text + '</span>');
                                    return data.text;
                                },
                                matcher: function (params, data) {
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
                            });

                            $select.val(editId).trigger('change');

                        } else {
                            var realId = response.customer_id || response.id;
                            $select.find('option[value="' + realId + '"]').remove();
                            var $opt = $('<option>', { value: realId, text: label })
                                .attr('data-phone', phone)
                                .attr('data-gst', gst);
                            $select.append($opt);
                            $select.val(realId).trigger('change');
                            if ($select.data('select2')) $select.trigger('change.select2');
                        }

                        // Sync phone & GST fields
                        $('#customer_phone').val(phone);
                        if (gst) $('#pos_gst_number').val(gst);

                        if (qacModal) qacModal.hide();
                        syncEditBtn();

                        Swal.fire({
                            icon: 'success',
                            title: isEdit ? 'Customer Updated' : 'Customer Added',
                            text: name + (isEdit ? ' updated' : ' added') + ' successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Operation failed.' });
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        var errors = (xhr.responseJSON || {}).errors || {};
                        $.each(errors, function (key, msgs) {
                            $('#qac_error_' + key).html(msgs[0]);
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: (xhr.responseJSON || {}).message || 'Something went wrong.' });
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
