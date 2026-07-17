@extends('layout.app')

@section('title', 'Add Purchase')

@section('content')

    <style>
        .gst-header {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            padding: 8px 10px 0;
        }

        .purchase-back-btn {
            margin-left: 6px;
            white-space: nowrap;
            background: #FF9F43;
            border-color: #FF9F43;
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .purchase-back-btn:hover,
        .purchase-back-btn:focus {
            background: #f08f2e;
            border-color: #f08f2e;
            color: #fff;
        }

        .purchase-back-btn i {
            font-size: 12px;
            line-height: 1;
        }

        .total-order{
            margin: 0px !important;
        }

        .custom-radio-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            color: #333;
            user-select: none;
            position: relative;
            padding-left: 24px;
            margin: 0;
            white-space: nowrap;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: color 0.2s ease;
        }

        .custom-radio-label input[type="radio"] {
            appearance: none;
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            border: 2px solid #888;
            border-radius: 50%;
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .custom-radio-label input[type="radio"]:checked {
            border-color: #0056b3;
            background-color: #0056b3;
        }

        .custom-radio-label input[type="radio"]:checked::after {
            content: "";
            display: block;
            width: 7px;
            height: 7px;
            background: white;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .custom-radio-label:hover input[type="radio"] {
            border-color: #5a50cc;
        }

        .custom-radio-label:hover {
            color: #0056b3;
        }

        .manage_btn {
            color: #fff;
            background: #ff9f43;
        }

        .manage_btn:hover {
            color: #e6e4e4ff;
        }

        .form-row {
            border: 1px solid #fdc794;
            padding: 20px 10px 10px 10px;
            margin-bottom: 20px;
            border-radius: 10px;
            background: #fdfdfd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .select2-container {
            width: 100% !important;
        }

        .add-row-btn {
            margin-top: 28px;
        }

        .purchase-remark-block .form-group {
            margin-bottom: 0;
        }

        .purchase-remark-textarea {
            min-height: 180px;
            resize: vertical;
        }

        @media (min-width: 992px) {
            .purchase-meta-row.purchase-bottom-meta-row .purchase-remark-block {
                order: 4;
            }
        }

        .purchase-product-row-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            margin: 0;
            flex-shrink: 0;
        }

        .purchase-product-row-actions .add-product-btn {
            padding: 7px 14px;
            font-size: 13px;
            line-height: 1.4;
            white-space: nowrap;
        }

        .purchase-items-section {
            margin-top: 6px;
        }

        .purchase-items-toolbar {
            display: flex;
            align-items: stretch;
            gap: 12px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .purchase-product-search-wrap {
            flex: 1 1 320px;
            width: auto;
            margin-bottom: 0;
        }

        .purchase-product-search-wrap .header-search {
            margin-bottom: 0 !important;
            height: 100%;
        }

        .purchase-product-search-wrap .purchase-search-input {
            height: 40px;
            font-size: 14px;
            padding-left: 42px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: #fff;
        }

        .purchase-product-search-wrap .purchase-search-input:focus {
            border-color: #ff9f43;
            box-shadow: 0 0 0 0.15rem rgba(255, 159, 67, 0.18);
        }

        .purchase-product-search-wrap .purchase-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            z-index: 10;
            opacity: 0.6;
        }

        #purchaseProductSearchResults {
            z-index: 1050;
            max-height: 300px;
            overflow-y: auto;
            top: calc(100% + 4px);
            left: 0;
            border: 1px solid #e5e7eb;
        }

        #purchaseProductSearchResults .list-group-item {
            border-left: 0;
            border-right: 0;
        }

        .form-row.purchase-product-row {
            padding: 16px 14px 12px;
        }

        .form-row.purchase-product-row label {
            font-size: 13px;
            margin-bottom: 6px;
            white-space: nowrap;
        }

        .form-row.purchase-product-row .form-control,
        .form-row.purchase-product-row .select2-container .select2-selection--single {
            min-height: 40px;
            height: 40px;
        }

        .form-row.purchase-product-row .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-right: 30px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .form-row.purchase-product-row .add-row-btn .btn,
        .form-row.purchase-product-row .remove-row {
            min-width: 40px;
            height: 40px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            line-height: 1;
        }

        @media (min-width: 992px) and (max-width: 1280px) {
            .form-row.purchase-product-row {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                align-items: end;
                gap: 12px 10px;
                margin-left: 0;
                margin-right: 0;
            }

            .form-row.purchase-product-row > [class*="col-"] {
                width: 100% !important;
                max-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                margin: 0 !important;
            }

            /* Span Total Amount to 2 columns on Row 3 */
            .form-row.purchase-product-row > div:nth-child(8) {
                grid-column: span 2;
            }

            .form-row.purchase-product-row .form-group {
                margin-bottom: 0 !important;
            }

            .form-row.purchase-product-row .add-row-btn {
                margin-top: 0;
                display: flex;
                align-items: end;
                justify-content: center;
            }

            .form-row.purchase-product-row .add-row-btn .btn {
                width: 100%;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .form-row.purchase-product-row {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 12px;
                align-items: end;
            }

            .form-row.purchase-product-row > [class*="col-"] {
                width: 100% !important;
                max-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                margin: 0 !important;
            }

            .form-row.purchase-product-row > div:nth-child(1) {
                grid-column: 1 / 3;
            }

            .form-row.purchase-product-row > div:nth-child(2) {
                grid-column: 3 / 5;
            }

            .form-row.purchase-product-row > div:nth-child(7) {
                grid-column: 1 / 3;
            }

            .form-row.purchase-product-row > div:nth-child(8) {
                grid-column: 4 / 5;
            }

            .form-row.purchase-product-row .add-row-btn {
                margin-top: 0;
            }
        }

        .vendor-modal-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px 16px;
        }

        .product-modal-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px 16px;
        }

        .vendor-modal-grid .vendor-full-width {
            grid-column: 1 / -1;
        }

        .product-modal-grid .product-full-width {
            grid-column: 1 / -1;
        }

        .vendor-modal-grid .form-group {
            margin-bottom: 0 !important;
        }

        .product-modal-grid .form-group {
            margin-bottom: 0 !important;
        }

        .vendor-modal-grid label {
            display: block;
            margin-bottom: 6px;
        }

        .product-modal-grid label {
            display: block;
            margin-bottom: 6px;
        }

        .product-modal-upload {
            border: 1px solid #d8d8d8;
            border-radius: 8px;
            padding: 14px;
            background: #fffaf5;
        }

        .product-modal-upload input[type="file"] {
            width: 100%;
        }

        .product-modal-help {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: #6c757d;
        }

        @media screen and (max-width: 767px) {
            .purchase-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 8px;
            }

            .purchase-header .page-title h4 {
                font-size: 16px;
                margin-bottom: 0;
            }

            .gst-header {
                display: flex;
                align-items: center;
                width: auto;
                padding: 0;
                gap: 14px;
                flex-wrap: wrap;
            }

            .purchase-back-btn {
                margin-left: 0;
            }

            .custom-radio-label {
                font-size: 13px;
                font-weight: 500;
                padding-left: 22px;
                gap: 6px;
            }

            .custom-radio-label input[type="radio"] {
                width: 15px;
                height: 15px;
            }

            .custom-radio-label input[type="radio"]:checked::after {
                width: 6px;
                height: 6px;
            }

            .add-row-btn {
                margin-top: 0;
                margin-bottom: 1rem;
            }

            .add-row {
                width: 100%;
                text-align: center;
            }

            .remove-row {
                width: 100%;
                text-align: center;
            }

            .form-group {
                margin-bottom: 10px !important;
            }

            .vendor-modal-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .vendor-modal-grid .vendor-full-width {
                grid-column: 1 / -1;
            }

            .product-modal-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .product-modal-grid .product-full-width {
                grid-column: 1 / -1;
            }

            .purchase-product-row-actions {
                width: 100%;
                justify-content: flex-start;
                margin-bottom: 4px;
            }
            }

        /* Tablet responsive fix for the top meta row */
        @media (min-width: 767px) and (max-width: 1280px) {
            .purchase-meta-row {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                align-items: start;
                gap: 12px 14px;
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            .purchase-meta-row > [class*="col-"] {
                width: 100% !important;
                max-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                margin: 0 !important;
            }

            .purchase-meta-row .form-group {
                margin-bottom: 0 !important;
            }

            .purchase-meta-row textarea.form-control {
                min-height: 76px;
            }

            /* Bottom meta row (Shipping, Payment Mode, Remark + dynamic payment fields) */
            .purchase-meta-row.purchase-bottom-meta-row {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            /* Remark always spans full width on tablet */
            .purchase-meta-row.purchase-bottom-meta-row .remark-col {
                grid-column: 1 / -1;
            }
        }

        @media (min-width: 767px) and (max-width: 991.98px) {
            .purchase-meta-row.purchase-top-meta-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .purchase-meta-row.purchase-top-meta-row .vendor-col {
                grid-column: 1 / -1;
            }
        }

        /* iPad Mini / Air / Pro: keep the payment section in a two-column grid */
        @media (min-width: 768px) and (max-width: 1366px) {
            .purchase-meta-row.purchase-bottom-meta-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px 14px;
            }

            .purchase-meta-row.purchase-bottom-meta-row > [class*="col-"] {
                width: 100% !important;
                max-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                margin: 0 !important;
            }

            .purchase-meta-row.purchase-bottom-meta-row .remark-col {
                grid-column: 1 / -1;
            }

            /* Make the Add Product modal wide enough for a true two-column layout */
            #addProductModal .modal-dialog {
                width: min(980px, calc(100vw - 1.5rem));
                max-width: min(980px, calc(100vw - 1.5rem));
            }

            .product-modal-grid {
                gap: 12px 14px;
            }

        }

        /* iPad Pro portrait: force all purchase select boxes to fill their columns */
        @media screen and (width: 1024px) and (height: 1366px) {
            .page-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                overflow-x: hidden !important;
            }

            .page-wrapper .content {
                width: 100% !important;
                max-width: 100% !important;
                padding-left: 275px !important;
                padding-right: 0px !important;
                box-sizing: border-box;
            }

            .page-wrapper .content > .card {
                width: 100% !important;
                max-width: 98% !important;
            }

            .purchase-meta-row .form-group,
            .purchase-meta-row select.form-control,
            .purchase-meta-row .select2-container {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
            }
            .purchase-meta-row .select2-container {
                display: block !important;
            }

            .purchase-meta-row .select2-selection--single {
                width: 100% !important;
            }

            .purchase-product-row .category-select,
            .purchase-product-row .product-select,
            .purchase-product-row .select2-container,
            .purchase-product-row .select2-selection--single,
            .purchase-product-row .select2-selection__rendered {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
            }

            .purchase-meta-row.purchase-top-meta-row {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .purchase-meta-row.purchase-bottom-meta-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            #addProductModal .product-modal-grid > .form-group {
                min-width: 0;
            }

            #addProductModal .product-modal-grid .form-control,
            #addProductModal .product-modal-grid .select2-container,
            #addProductModal .product-modal-grid .select2-selection--single {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
            }

            #addProductModal .product-modal-grid .select2-container {
                display: block !important;
            }
        }

        /* Barcode Scanner Styles */
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
    </style>

    <div class="content">
        <div class="page-header purchase-header">
            <div class="page-title">
                <h4>Add Purchase</h4>
            </div>
            <div class="gst-header">

                <label class="custom-radio-label">
                    <input type="radio" name="gst_option" id="without_gst" value="without" />
                    Without GST
                </label>

                <label class="custom-radio-label">
                    <input type="radio" name="gst_option" id="with_gst" value="with" checked />
                    With GST
                </label>

                <button type="button" class="btn btn-sm purchase-back-btn"
                    onclick="window.history.back();">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    Back
                </button>

            </div>
        </div>

        <div class="card">

            <div class="card-body">
                <div class="">
                    <div class="row d-flex align-items-center purchase-meta-row purchase-top-meta-row">

                        <div class="col-lg-4 col-md-12 col-12 vendor-col">
                            <div class="form-group">
                                <label>Vendor<button type="button" class="btn btn-sm manage_btn ms-2"
                                        id="addVendorBtn" style="padding: 3px 8px; font-size: 12px;">Add
                                        Vendor</button></label>
                                <select id="vendor_name" name="vendor_id" class="form-control select2 vendor-select w-100" style="width: 100%;">
                                    <option value="">Select Vendor</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" data-phone="{{ $vendor->phone }}">
                                            {{ $vendor->name }}{{ $vendor->phone ? ' - ' . $vendor->phone : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="error text-danger"></span>
                            </div>
                        </div>

                        <!-- Barcode Scanner Status -->
                        <div class="col-lg-4 col-md-12 col-12 d-none" style="display: flex; align-items: flex-end;">
                            <div id="purchaseScannerStatus" class="mobile-scanner-status is-checking" style="width: 100%; text-align: center;">
                                Mobile scanner: checking...
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 col-6">
                            <div class="form-group">
                                <label>Bill No.</label>
                                <input type="text" name="bill_no" id="bill_no" class="form-control"
                                    placeholder="Bill No">
                                <span class="error text-danger" id="bill_no_error"></span>
                                <span class="text-danger" id="bill_no_unique_error"
                                    style="display:none; font-size:12px;"></span>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 col-6">
                            <div class="form-group">
                                <label>Purchase Date</label>
                                <input type="text" name="purchase_date" id="purchase_date" class="form-control"
                                    value="{{ now()->format('d/m/Y') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                                <span class="error text-danger" id="purchase_date_error"></span>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-6 col-sm-6 d-none">
                        <div class="form-group">
                            <label>Vendor Phone</label>
                            <input type="number" id="vendor_phone" name="phone" class="form-control"
                                placeholder="Enter Phone">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                </div>

                <div class="purchase-items-section">
                    <div class="purchase-items-toolbar">
                        <div class="purchase-product-search-wrap">
                            <div class="header-search d-flex align-items-center position-relative">
                                <!-- <img src="{{ env('ImagePath') . '/admin/assets/img/icons/search.svg' }}"
                                    alt="Search" class="purchase-search-icon"> -->
                                <input type="text" id="purchaseProductSearch"
                                    class="form-control form-control-sm rounded px-3 purchase-search-input"
                                    placeholder="Search product by name..." autocomplete="off">
                                <div id="purchaseProductSearchResults"
                                    class="list-group bg-white position-absolute rounded shadow w-100"
                                    style="display: none;"></div>
                            </div>
                        </div>

                        <div class="purchase-product-row-actions">
                            <button type="button" class="btn btn-sm manage_btn add-product-btn">Add Product</button>
                            <button type="button" id="purchaseScanBarcodeBtn" class="btn btn-sm"
                                style="background: #ff9f43; border: 1px solid #ff9f43; color: #fff; padding: 7px 14px; font-size: 13px; border-radius: 4px; white-space: nowrap;">
                                <i class="fas fa-barcode"></i> Scan Barcode
                            </button>
                        </div>
                    </div>

                {{-- <div class="mt-2"> --}}
                <div class="row form-row purchase-product-row">


                    <div class="col-lg-2 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Category Name</label>
                            <select id="category_name" name="category_name[]" class="form-control select2 category-select">
                                <option value="">Category Name</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" data-price="{{ $category->price }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Product Name</label>
                            <select id="product_name" name="product_name[]" class="form-control select2 product-select"
                                disabled>
                                <option value="">Product Name</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}"
                                        data-category="{{ $product->category_id }}"
                                        data-gst-option="{{ $product->gst_option }}" data-gst='{!! $product->product_gst !!}'>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="product-gst-info mt-1"></div>

                            <span class="error text-danger product-error"></span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Product Price</label>
                            <input type="text" name="price[]" class="form-control price-input" placeholder="Enter Price"
                                min="0" oninput="this.value = this.value < 0 ? 0 : this.value" inputmode="decimal"
                                step="0.01">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Qty</label>
                            <input type="text" name="quantity[]" class="form-control quantity-input"
                                placeholder="Qty" value="0" min="0" step="0.01" inputmode="decimal"
                                oninput="this.value = this.value < 0 ? 0 : this.value">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Disc%</label>
                            <input type="text" name="product_discount[]" class="form-control product-discount-input"
                                placeholder="0.00" value="0" min="0" max="100" inputmode="decimal">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Disc-Amt</label>
                            <input type="text" name="product_discount_amount[]"
                                class="form-control product-discount-amount-input" placeholder="0.00" value="0"
                                min="0">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-6 col-6 serial-no-container">
                        <div class="form-group">
                            <div class="serial-no-btn-wrapper" style="display:none;">
                                <label>IMEI No <a href="javascript:void(0)" class="edit-serial-btn text-warning ml-1" title="Edit Serial Numbers"><i class="fas fa-edit"></i></a></label>
                                <div class="serial-status text-muted" style="font-size: 11px; line-height: 1.2;">0/0 serial numbers added</div>
                                <input type="hidden" class="serial-data-input" name="imei_no[]" value="[]">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Total Amount</label>
                            <input type="text" name="total[]" class="form-control total-input" placeholder="0"
                                readonly>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-6 add-row-btn">
                        <button type="button" class="btn btn-success add-row">+</button>
                    </div>
                </div>
                <div id="form-container"></div>
                </div>
                {{-- </div> --}}

                <div class="row purchase-meta-row purchase-bottom-meta-row">

                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="form-group">
                            <label>Shipping</label>
                            <input type="text" name="shipping" id="shipping" class="form-control"
                                placeholder="0.00" min="0">
                            <span class="error text-danger"></span>
                        </div>
                    </div>

                    {{-- <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Purchase Status</label>
                            <select name="status" class="form-control purchase-status-select">
                                <option value="">Choose Status</option>
                                <option value="pending">Pending</option>
                                <option value="partially">Partially</option>
                                <option value="completed">Completed</option>
                            </select>
                            <span class="error text-danger"></span>
                        </div>
                    </div> --}}



                    <div class="col-lg-3 col-md-6 col-6" id="payment_mode_wrapper">
                        <div class="form-group">
                            <label>Payment Mode</label>
                            <select name="payment_mode" id="payment_mode" class="form-control payment-mode-select">
                                <option value="">Select Payment Mode</option>
                                <option value="pending">Pending</option>
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                                <option value="cashonline">Cash + Online</option>
                            </select>
                            <div class="text-danger error-payment_mode"></div>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6 col-lg-2 col-6 d-none" id="paid_type_container">
                        <div class="form-group">
                            <label>Paid Type</label>
                            <select id="paid_type" name="paid_type" class="form-control paid-type-container">
                                <option value="">Select Paid Type</option>
                                <option value="full">Fully Paid</option>
                                <option value="partial">Partially Paid</option>
                            </select>
                            <div class="text-danger error-paid_type"></div>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6 col-lg-2 col-6 d-none" id="cash_amount_input_container">
                        <div class="form-group">
                            <label>Cash Amount</label>
                            <input type="text" id="cash_amount_input" name="cash_amount" class="form-control"
                                placeholder="Enter Cash Amount">
                            <span class="text-danger error-cash-amount" id="cash_amount_error"></span>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-lg-2 col-6 d-none" id="amount_input_container">
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="text" id="amount_input" name="amount" class="form-control"
                                placeholder="Enter Amount">
                            <span class="text-danger error-cash-amount" id="amount_error"></span>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-lg-2 col-6 d-none" id="upi_amount_input_container">
                        <div class="form-group">
                            <label>Online Amount</label>
                            <input type="text" id="upi_amount_input" name="upi_amount" class="form-control"
                                placeholder="Enter Online Amount">
                            <span class="text-danger error-upi-amount" id="upi_amount_error"></span>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-lg-2 col-6 d-none" id="pending_amount_container">
                        <div class="form-group">
                            <label>Pending Amount</label>
                            <input type="text" id="pending_amount" name="pending_amount" class="form-control"
                                readonly>
                            <span id="pending_error" style="color:red; font-size:12px;"></span>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6 col-lg-2 col-6 d-none" id="bank_container">
                        <div class="form-group">
                            <label>Select Bank <button type="button" class="btn btn-sm manage_btn ms-2"
                                id="addBankBtn" style="padding: 1px 0px; font-size: 12px; background-color: #ff9f43; color: #ffffff;">Add
                                Bank</button></label>
                            <select name="bank_id" id="bank_id" class="form-control select2" style="width: 100%;">
                                <option value="">Select Bank</option>

                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error-bank_id"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-sm-6 col-12 remark-col purchase-remark-block">
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remark" id="remark" class="form-control purchase-remark-textarea" placeholder="Enter any remarks"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6 float-md-right">
                        <div class="total-order">
                            <ul>
                                <li>
                                    <h4>Total Product Amount</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="total-product-amount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="total-product-amount">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li id="gst-section" style="display:none;">
                                    <h4>Total GST</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="total-gst-amount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="total-gst-amount">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li id="discount-amount-section">
                                    <h4>Discount Amount</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="total-discount-amount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="total-discount-amount">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li id="price-after-discount-section">
                                    <h4>SubTotal</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="price-after-discount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="price-after-discount">0.00</span>
                                        @endif
                                    </h5>
                                </li>



                                <li id="shipping-section" style="display:none;">
                                    <h4>Shipping</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="shipping-amount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="shipping-amount">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li id="round-off-section" class="d-none">
                                    <h4>Round Off</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="round-off-amount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="round-off-amount">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li class="total">
                                    <h4>Grand Total</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="grand-total">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="grand-total">0.00</span>
                                        @endif
                                    </h5>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mt-1">
                    <a href="javascript:void(0);" class="btn btn-submit me-2 " id="submitPurchaseBtn">Submit</a>
                    <a href="{{ route('purchase.lists') }}" class="btn btn-cancel">Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Barcode Scanner Modal for Purchase -->
    <div class="modal fade" id="purchaseBarcodeScannerModal" tabindex="-1" aria-labelledby="purchaseBarcodeScannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="purchaseBarcodeScannerModalLabel">Scan Product Barcode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <div id="purchase-qr-reader" style="width:100%; min-height:300px;"></div>
                    <div id="purchase-scan-message" class="text-center mt-2 small text-muted">
                        Initializing camera...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Serial Number Modal -->
    <div class="modal fade" id="serialNumberModal" tabindex="-1" aria-labelledby="serialNumberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serialNumberModalLabel">Enter Serial Numbers (IMEI)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body" id="serialNumberModalBody">
                    <!-- Dynamic inputs will be appended here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveSerialNumbersBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Vendor Modal -->
    <div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addVendorModalLabel">Add New Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <form id="addVendorForm" enctype="multipart/form-data">
                        <div class="vendor-modal-grid">
                            <div class="form-group">
                                <label>Vendor Name <span class="text-danger">*</span></label>
                                <input type="text" id="vendor_name_input" name="name" class="form-control"
                                    placeholder="Enter Vendor Name">
                                <span class="text-danger" id="vendor_name_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" id="vendor_email_input" name="email" class="form-control"
                                    placeholder="Enter Email">
                                <span class="text-danger" id="vendor_email_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" id="vendor_phone_input" name="phone" maxlength="10"
                                    class="form-control" placeholder="Enter Phone Number">
                                <span class="text-danger" id="vendor_phone_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Country</label>
                                <input type="text" id="country_input" name="country" class="form-control"
                                    placeholder="Enter Country">
                                <span class="text-danger" id="country_error"></span>
                            </div>
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" id="city_input" name="city" class="form-control"
                                    placeholder="Enter City">
                                <span class="text-danger" id="city_error"></span>
                            </div>
                            <div class="form-group">
                                <label>State Code</label>
                                <input type="text" id="state_code_input" name="state_code" class="form-control"
                                    placeholder="Enter State Code">
                                <span class="text-danger" id="state_code_error"></span>
                            </div>
                            <div class="form-group">
                                <label>PAN Number</label>
                                <input type="text" id="pan_number_input" name="pan_number" class="form-control"
                                    placeholder="Enter Pan Number">
                                <span class="text-danger" id="pan_number_error"></span>
                            </div>
                            <div class="form-group">
                                <label>GST Number</label>
                                <input type="text" id="gst_number_input" name="gst_number" class="form-control"
                                    placeholder="Enter GST Number">
                                <span class="text-danger" id="gst_number_error"></span>
                            </div>

                            <div class="form-group vendor-full-width">
                                <label>Address</label>
                                <textarea id="vendor_address_input" name="address" class="form-control" rows="3" placeholder="Enter Address"></textarea>
                                <span class="text-danger" id="vendor_address_error"></span>
                            </div>
                            <div class="form-group vendor-full-width">
                                <label>Photo</label>
                                <input type="file" id="photo_input" name="photo" class="form-control"
                                    placeholder="Upload Photo" accept="image">
                                <span class="text-danger" id="photo_error"></span>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-submit" id="saveVendorBtn">Save Vendor</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <form id="addProductForm">
                        <div class="product-modal-grid">
                            <div class="form-group">
                                <label>Product Name <span class="text-danger">*</span></label>
                                <input type="text" id="product_modal_name" class="form-control"
                                    placeholder="Enter Product Name">
                                <span class="text-danger" id="product_modal_name_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Category <span class="text-danger">*</span></label>
                                <select id="product_modal_category" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="product_modal_category_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Brand</label>
                                <select id="product_modal_brand" class="form-control">
                                    <option value="">Select Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="product_modal_brand_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Unit <span class="text-danger">*</span></label>
                                <select id="product_modal_unit" class="form-control">
                                    <option value="">Select Unit</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="product_modal_unit_error"></span>
                            </div>

                            <div class="form-group">
                                <label>SKU</label>
                                <input type="text" id="product_modal_sku" class="form-control" placeholder="Enter SKU">
                                <span class="text-danger" id="product_modal_sku_error"></span>
                            </div>
                            <div class="form-group">
                                <label>HSN Code</label>
                                <input type="text" id="product_modal_hsn_code" class="form-control"
                                    placeholder="Enter HSN Code">
                                <span class="text-danger" id="product_modal_hsn_code_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Price <span class="text-danger">*</span></label>
                                <input type="text" id="product_modal_price" class="form-control" inputmode="decimal"
                                    placeholder="Enter Price">
                                <span class="text-danger" id="product_modal_price_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Qty</label>
                                <input type="text" id="product_modal_quantity" class="form-control" inputmode="decimal"
                                    placeholder="Enter Quantity" value="0">
                                <span class="text-danger" id="product_modal_quantity_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select id="product_modal_status" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <span class="text-danger" id="product_modal_status_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Stock</label>
                                <select id="product_modal_availability" class="form-control">
                                    <option value="in_stock">In Stock</option>
                                    <option value="out_stock">Out Of Stock</option>
                                </select>
                                <span class="text-danger" id="product_modal_availability_error"></span>
                            </div>
                            <div class="form-group">
                                <label>GST Option</label>
                                <select id="product_modal_gst_option" class="form-control">
                                    <option value="without_gst">Without GST</option>
                                    <option value="with_gst">With GST</option>
                                </select>
                                <span class="text-danger" id="product_modal_gst_option_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Barcode</label>
                                <input type="text" id="product_modal_barcode" class="form-control"
                                    placeholder="Enter Barcode">
                                <span class="text-danger" id="product_modal_barcode_error"></span>
                            </div>
                            <div class="form-group  d-none" id="product_modal_tax_wrapper">
                                <label>GST Rates <span class="text-danger">*</span></label>
                                <select id="product_modal_taxes" class="form-control" multiple>
                                    @foreach ($taxes as $tax)
                                        <option value="{{ $tax->id }}">
                                            {{ $tax->tax_name }} ({{ $tax->tax_rate }}%)
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="product_modal_taxes_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea id="product_modal_description" class="form-control" rows="4"
                                    placeholder="Enter Description"></textarea>
                                <span class="text-danger" id="product_modal_description_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Product Image</label>
                                <div class="product-modal-upload">
                                    <input type="file" id="product_modal_images" multiple accept="image/*">
                                    <small class="product-modal-help">Select one or more product images.</small>
                                </div>
                                <span class="text-danger" id="product_modal_images_error"></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-submit" id="saveProductBtn">Save Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bank Modal -->
    <div class="modal fade" id="addBankModal" tabindex="-1" aria-labelledby="addBankModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBankModalLabel">Add New Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <form id="addBankForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                                <input type="text" id="bank_name_input" name="bank_name" class="form-control" placeholder="Enter Bank Name">
                                <span class="text-danger error-text" id="bank_name_error"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Account Number <span class="text-danger">*</span></label>
                                <input type="text" id="account_number_input" name="account_number" class="form-control" placeholder="Enter Account Number">
                                <span class="text-danger error-text" id="account_number_error"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">IFSC Code <span class="text-danger">*</span></label>
                                <input type="text" id="ifsc_code_input" name="ifsc_code" class="form-control" placeholder="Enter IFSC Code">
                                <span class="text-danger error-text" id="ifsc_code_error"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                                <input type="text" id="branch_name_input" name="branch_name" class="form-control" placeholder="Enter Branch Name">
                                <span class="text-danger error-text" id="branch_name_error"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Opening Balance <span class="text-danger">*</span></label>
                                <input type="number" id="opening_balance_input" name="opening_balance" class="form-control" placeholder="0.00" step="0.01">
                                <span class="text-danger error-text" id="opening_balance_error"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select id="bank_status_input" name="status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <span class="text-danger error-text" id="bank_status_error"></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-submit" id="saveBankBtn">Save Bank</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- Load html5-qrcode library for barcode scanner --}}
    <script>
        // Dynamically load html5-qrcode library with fallback
        if (typeof Html5Qrcode === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js';
            script.onload = function() {
                console.log('html5-qrcode library loaded from CDN');
            };
            script.onerror = function() {
                console.error('Failed to load html5-qrcode library');
            };
            document.head.appendChild(script);
        }
    </script>
    <script>
        const products = @json($productsArray);
        const categoriesData = @json($categoriesArray);
        const unitsData = @json($unitsArray);

        function formatPurchaseDateForDisplay(value) {
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

            return value;
        }

        function formatPurchaseDateForApi(value) {
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

            return value;
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function buildCategoryOptions(selectedId = '') {
            let options = '<option value="">Category Name</option>';

            categoriesData.forEach(category => {
                const isSelected = String(selectedId) === String(category.id) ? 'selected' : '';
                options +=
                    `<option value="${escapeHtml(category.id)}" ${isSelected}>${escapeHtml(category.name)}</option>`;
            });

            return options;
        }
        function updatePurchaseSelectTitle($select) {
            const text = $select.find('option:selected').text().trim();
            const $container = $select.next('.select2-container');
            $container.find('.select2-selection__rendered').attr('title', text);
        }
    </script>
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            const $purchaseDateInput = $("#purchase_date");

            $purchaseDateInput.val(formatPurchaseDateForDisplay($purchaseDateInput.val()));
            $purchaseDateInput.datetimepicker({
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

            $purchaseDateInput.on("dp.change", function() {
                $(this).val(formatPurchaseDateForDisplay($(this).val()));
            });

            // Initialize Select2
            $("#bank_id,.payment-mode-select,.purchase-status-select,.paid-type-container")
                .select2({
                    tags: true,
                    width: '100%'
                });

            $('.purchase-product-row .category-select, .purchase-product-row .product-select').each(function() {
                updatePurchaseSelectTitle($(this));
            });

            $(document).on('change', '.purchase-product-row .category-select, .purchase-product-row .product-select', function() {
                updatePurchaseSelectTitle($(this));
            });

            $(".category-select").each(function() {
                $(this).select2({
                    tags: true,
                    placeholder: "Category Name",
                    dropdownParent: $(this).parent()
                });
            });

            $(".product-select").each(function() {
                $(this).select2({
                    tags: true,
                    placeholder: "Product Name",
                    dropdownParent: $(this).parent()
                });
            });

            $(".vendor-select").select2({
                width: '100%',
                placeholder: 'Select Vendor',
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
                    return data.text; // dropdown shows "Name - Phone"
                },
                templateSelection: function(data) {
                    if (!data.id) return data.text;
                    // Show only vendor name (strip phone part) in the selection box
                    var text = data.text || '';
                    var dashIndex = text.lastIndexOf(' - ');
                    return dashIndex > -1 ? text.substring(0, dashIndex) : text;
                }
            });

            $("#product_modal_category").select2({
                width: '100%',
                tags: true,
                dropdownParent: $('#addProductModal')
            });

            $("#product_modal_brand, #product_modal_unit, #product_modal_status, #product_modal_availability, #product_modal_gst_option, #product_modal_taxes").select2({
                width: '100%',
                tags: false,
                dropdownParent: $('#addProductModal')
            });

            let activeProductRow = null;

            function toggleProductTaxField() {
                const shouldShowTaxes = $('#product_modal_gst_option').val() === 'with_gst';
                $('#product_modal_tax_wrapper').toggleClass('d-none', !shouldShowTaxes);

                if (!shouldShowTaxes) {
                    $('#product_modal_taxes').val(null).trigger('change');
                    $('#product_modal_taxes_error').text('');
                }
            }

            function clearProductModalErrors() {
                $('#product_modal_category_error, #product_modal_brand_error, #product_modal_unit_error, #product_modal_name_error, #product_modal_sku_error, #product_modal_hsn_code_error, #product_modal_price_error, #product_modal_quantity_error, #product_modal_status_error, #product_modal_availability_error, #product_modal_barcode_error, #product_modal_description_error, #product_modal_taxes_error, #product_modal_images_error')
                    .text('');
            }

            function syncProductModalAvailability() {
                const quantity = Number($('#product_modal_quantity').val());
                const availability = quantity > 0 ? 'in_stock' : 'out_stock';

                $('#product_modal_availability').val(availability).trigger('change');
            }

            function resetProductModal() {
                $('#addProductForm')[0].reset();
                clearProductModalErrors();
                $('#product_modal_category').val('').trigger('change');
                $('#product_modal_brand').val('').trigger('change');
                $('#product_modal_unit').val('').trigger('change');
                $('#product_modal_taxes').val(null).trigger('change');
                $('#product_modal_gst_option').val($("input[name='gst_option']:checked").val() === 'with' ? 'with_gst' : 'without_gst')
                    .trigger('change');
                $('#product_modal_quantity').val('0');
                $('#product_modal_price').val('');
                $('#product_modal_name').val('');
                $('#product_modal_sku').val('');
                $('#product_modal_hsn_code').val('');
                $('#product_modal_status').val('active').trigger('change');
                $('#product_modal_availability').val('in_stock').trigger('change');
                $('#product_modal_barcode').val('');
                $('#product_modal_description').val('');
                $('#product_modal_images').val('');
            }

            function syncCategoryOption(categoryId, categoryName) {
                const alreadyExists = categoriesData.some(category => String(category.id) === String(categoryId));

                if (!alreadyExists) {
                    categoriesData.push({
                        id: categoryId,
                        name: categoryName
                    });
                }

                $('.category-select').each(function() {
                    if ($(this).find(`option[value="${categoryId}"]`).length === 0) {
                        $(this).append(new Option(categoryName, categoryId, false, false));
                    }
                });

                if ($('#product_modal_category').find(`option[value="${categoryId}"]`).length === 0) {
                    $('#product_modal_category').append(new Option(categoryName, categoryId, false, false));
                }
            }

            function syncProductOption(productData) {
                const alreadyExists = products.some(product => String(product.id) === String(productData.id));

                if (!alreadyExists) {
                    products.push(productData);
                }

                $('.product-select').each(function() {
                    const $select = $(this);
                    const selectedCategory = $select.closest('.form-row').find('.category-select').val();

                    if (String(selectedCategory) !== String(productData.category_id)) {
                        return;
                    }

                    if ($select.find(`option[value="${productData.id}"]`).length === 0) {
                        const gstVal = typeof productData.product_gst === 'object' ? JSON.stringify(productData.product_gst) :
                            (productData.product_gst || '');

                        $select.append(
                            `<option value="${productData.id}" data-price="${productData.price || 0}" data-category="${productData.category_id}" data-gst-option="${productData.gst_option || 'without_gst'}" data-gst='${escapeHtml(gstVal)}'>${escapeHtml(productData.name)}</option>`
                        );
                    }
                });
            }

            function createCategoryIfNeeded(categoryValue) {
                if (!categoryValue || !isNaN(categoryValue)) {
                    return $.Deferred().resolve({
                        id: categoryValue,
                        name: $('#product_modal_category option:selected').text().trim()
                    }).promise();
                }

                return $.ajax({
                    url: '/api/addcategory',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Authorization': 'Bearer ' + authToken,
                    },
                    data: {
                        name: categoryValue,
                        sub_admin_id: localStorage.getItem('selectedSubAdminId') || null
                    }
                }).then(function(response) {
                    return {
                        id: response.category.id,
                        name: response.category.name
                    };
                });
            }

            $('#product_modal_gst_option').on('change', toggleProductTaxField);
            $('#product_modal_quantity').on('input', syncProductModalAvailability);

            $(document).on('click', '.add-product-btn', function() {
                activeProductRow = $(this).closest('.form-row');

                if (!activeProductRow.length) {
                    activeProductRow = $('.purchase-product-row').first();
                }

                resetProductModal();

                const selectedCategory = activeProductRow.find('.category-select').val();
                const currentPrice = activeProductRow.find('.price-input').val();
                const currentQuantity = activeProductRow.find('.quantity-input').val();

                if (selectedCategory) {
                    if (activeProductRow.find('.category-select option:selected').length &&
                        isNaN(selectedCategory)) {
                        if ($('#product_modal_category').find(`option[value="${selectedCategory}"]`).length === 0) {
                            $('#product_modal_category').append(new Option(activeProductRow.find(
                                '.category-select option:selected').text().trim(), selectedCategory, false, false));
                        }
                    }

                    $('#product_modal_category').val(selectedCategory).trigger('change');
                }

                if (currentPrice) {
                    $('#product_modal_price').val(currentPrice);
                }

                if (currentQuantity) {
                    $('#product_modal_quantity').val(currentQuantity);
                }

                syncProductModalAvailability();

                $('#addProductModal').modal('show');
            });

            $('#saveProductBtn').on('click', function() {
                const $btn = $(this);
                const originalText = $btn.text();
                const categoryValue = $('#product_modal_category').val();
                const productName = $('#product_modal_name').val().trim();
                const brandId = $('#product_modal_brand').val();
                const sku = $('#product_modal_sku').val().trim();
                const hsnCode = $('#product_modal_hsn_code').val().trim();
                const price = $('#product_modal_price').val().trim();
                const quantity = $('#product_modal_quantity').val().trim();
                const unitId = $('#product_modal_unit').val();
                const status = $('#product_modal_status').val();
                const availability = $('#product_modal_availability').val();
                const gstOption = $('#product_modal_gst_option').val();
                const barcode = $('#product_modal_barcode').val().trim();
                const description = $('#product_modal_description').val().trim();
                const taxes = $('#product_modal_taxes').val() || [];
                const images = $('#product_modal_images')[0].files || [];

                clearProductModalErrors();

                let hasError = false;

                if (!categoryValue) {
                    $('#product_modal_category_error').text('Category is required');
                    hasError = true;
                }

                if (!productName) {
                    $('#product_modal_name_error').text('Product name is required');
                    hasError = true;
                }

                if (!price || Number(price) <= 0) {
                    $('#product_modal_price_error').text('Valid price is required');
                    hasError = true;
                }

                if (!quantity || Number(quantity) <= 0) {
                    $('#product_modal_quantity_error').text('Valid quantity is required');
                    hasError = true;
                }

                if (!unitId) {
                    $('#product_modal_unit_error').text('Unit is required');
                    hasError = true;
                }

                if (gstOption === 'with_gst' && taxes.length === 0) {
                    $('#product_modal_taxes_error').text('Select at least one GST rate');
                    hasError = true;
                }

                if (hasError) {
                    return;
                }

                $btn.html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                ).prop('disabled', true);

                createCategoryIfNeeded(categoryValue).then(function(categoryResult) {
                    const formData = new FormData();

                    formData.append('name', productName);
                    formData.append('category_id', categoryResult.id);
                    formData.append('brand_id', brandId || '');
                    formData.append('SKU', sku);
                    formData.append('hsn_code', hsnCode);
                    formData.append('price', price);
                    formData.append('quantity', quantity);
                    formData.append('unit_id', unitId);
                    formData.append('status', status || 'active');
                    formData.append('availablility', availability || 'in_stock');
                    formData.append('gst_option', gstOption);
                    formData.append('barcode', barcode);
                    formData.append('description', description);
                    formData.append('sub_admin_id', localStorage.getItem('selectedSubAdminId') || '');

                    taxes.forEach(function(taxId) {
                        formData.append('product_gst[]', taxId);
                    });

                    Array.from(images).forEach(function(file) {
                        formData.append('images[]', file);
                    });

                    syncCategoryOption(categoryResult.id, categoryResult.name);

                    return $.ajax({
                        url: '/api/createProduct',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Authorization': 'Bearer ' + authToken,
                        },
                        processData: false,
                        contentType: false,
                        data: formData
                    }).then(function(response) {
                        return {
                            response: response,
                            category: categoryResult
                        };
                    });
                }).done(function(result) {
                    const newProduct = result.response.product;
                    const normalizedProduct = {
                        id: newProduct.id,
                        name: newProduct.name,
                        category_id: newProduct.category_id,
                        price: newProduct.price,
                        gst_option: newProduct.gst_option,
                        product_gst: newProduct.product_gst
                    };

                    syncProductOption(normalizedProduct);

                    if (activeProductRow && activeProductRow.length) {
                        const $categorySelect = activeProductRow.find('.category-select');
                        const $productSelect = activeProductRow.find('.product-select');

                        if ($categorySelect.find(`option[value="${result.category.id}"]`).length === 0) {
                            $categorySelect.append(new Option(result.category.name, result.category.id, false, false));
                        }

                        $categorySelect.val(String(result.category.id)).trigger('change');

                        setTimeout(function() {
                            if ($productSelect.find(`option[value="${newProduct.id}"]`).length === 0) {
                                const gstVal = typeof normalizedProduct.product_gst === 'object' ? JSON.stringify(
                                    normalizedProduct.product_gst) : (normalizedProduct.product_gst || '');

                                $productSelect.append(
                                    `<option value="${newProduct.id}" data-price="${newProduct.price}" data-category="${newProduct.category_id}" data-gst-option="${newProduct.gst_option || 'without_gst'}" data-gst='${escapeHtml(gstVal)}'>${escapeHtml(newProduct.name)}</option>`
                                );
                            }

                            $productSelect.prop('disabled', false).val(String(newProduct.id)).trigger('change');
                        }, 0);
                    }

                    $('#addProductModal').modal('hide');

                    Swal.fire({
                        title: 'Success',
                        text: result.response.message || 'Product added successfully',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ff9f43',
                        customClass: {
                            icon: 'swal-icon-small'
                        }
                    });
                }).fail(function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        const categoryCreationFailed = isNaN(categoryValue) && errors.name && !errors.category_id &&
                            !errors.price && !errors.quantity && !errors.unit_id;

                        $('#product_modal_name_error').text(categoryCreationFailed ? '' : ((errors.name && errors.name[0]) || ''));
                        $('#product_modal_category_error').text((errors.category_id && errors.category_id[0]) || '');
                        if (errors.brand_id) $('#product_modal_brand_error').text(errors.brand_id[0]);
                        if (errors.SKU) $('#product_modal_sku_error').text(errors.SKU[0]);
                        if (errors.hsn_code) $('#product_modal_hsn_code_error').text(errors.hsn_code[0]);
                        if (errors.price) $('#product_modal_price_error').text(errors.price[0]);
                        if (errors.quantity) $('#product_modal_quantity_error').text(errors.quantity[0]);
                        if (errors.unit_id) $('#product_modal_unit_error').text(errors.unit_id[0]);
                        if (errors.status) $('#product_modal_status_error').text(errors.status[0]);
                        if (errors.availablility) $('#product_modal_availability_error').text(errors.availablility[0]);
                        if (errors.product_gst) $('#product_modal_taxes_error').text(errors.product_gst[0]);
                        if (errors.barcode) $('#product_modal_barcode_error').text(errors.barcode[0]);
                        if (errors.description) $('#product_modal_description_error').text(errors.description[0]);
                        if (errors.images) $('#product_modal_images_error').text(errors.images[0]);
                        if (errors['images.0']) $('#product_modal_images_error').text(errors['images.0'][0]);
                        if (categoryCreationFailed) {
                            $('#product_modal_category_error').text(errors.name[0]);
                        }
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: (xhr.responseJSON && xhr.responseJSON.message) || 'Unable to add product',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            customClass: {
                                icon: 'swal-icon-small'
                            }
                        });
                    }
                }).always(function() {
                    $btn.text(originalText).prop('disabled', false);
                });
            });

            // Add Vendor Modal Functionality
            $('#addVendorBtn').on('click', function() {
                $('#addVendorModal').modal('show');
                $('#addVendorForm')[0].reset();
                $('#addVendorForm .text-danger').text('');
            });

            // Save Vendor
            $('#saveVendorBtn').on('click', function() {
                let $btn = $(this);
                let originalText = $btn.text();

                // Clear previous errors
                $('#addVendorForm .text-danger').text('');

                // Get form data
                let vendorData = {
                    name: $('#vendor_name_input').val().trim(),
                    email: $('#vendor_email_input').val().trim(),
                    phone: $('#vendor_phone_input').val().trim(),
                    address: $('#vendor_address_input').val().trim(),
                    country: $('#country_input').val().trim(),
                    city: $('#city_input').val().trim(),
                    state_code: $('#state_code_input').val().trim(),
                    pan_number: $('#pan_number_input').val().trim(),
                    gst_number: $('#gst_number_input').val().trim(),
                    photo: $('#photo_input')[0].files[0] || null,
                    selectedSubAdminId: localStorage.getItem('selectedSubAdminId') || null
                };

                // Validation
                let hasError = false;

                if (!vendorData.name) {
                    $('#vendor_name_error').text('Vendor name is required');
                    hasError = true;
                }

                if (!vendorData.phone) {
                    $('#vendor_phone_error').text('Phone number is required');
                    hasError = true;
                } else if (!/^[0-9]{10}$/.test(vendorData.phone)) {
                    $('#vendor_phone_error').text('Please enter a valid 10 digit phone number');
                    hasError = true;
                }

                if (vendorData.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(vendorData.email)) {
                    $('#vendor_email_error').text('Please enter a valid email');
                    hasError = true;
                }

                if (hasError) {
                    return;
                }

                // Disable button and show loading
                $btn.html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                ).prop('disabled', true);

                // AJAX request to save vendor
                $.ajax({
                    url: '/api/createSupplier',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Authorization': 'Bearer ' + authToken,
                    },
                    contentType: 'application/json',
                    data: JSON.stringify(vendorData),
                    success: function(response) {
                        $btn.text(originalText).prop('disabled', false);

                        if (response.success || response.status) {
                            let newVendor = response.vendor;

                            if (!newVendor || !newVendor.id || !newVendor.name) {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Vendor was created, but vendor details were not returned. Please refresh the page.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        icon: 'swal-icon-small'
                                    }
                                });
                                return;
                            }

                            // Add new vendor to dropdown
                            let newOption = new Option(newVendor.name + (newVendor.phone ? ' - ' + newVendor.phone : ''), newVendor.id, true, true);
                            $(newOption).attr('data-phone', newVendor.phone || '');
                            $('#vendor_name').append(newOption);

                            // Select the newly added vendor
                            $('#vendor_name').val(newVendor.id).trigger('change');

                            // Update vendor phone
                            $('#vendor_phone').val(newVendor.phone || '');

                            // Close modal
                            $('#addVendorModal').modal('hide');

                            // Show success message
                            Swal.fire({
                                title: 'Success',
                                text: response.message || 'Vendor added successfully',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#ff9f43',
                                customClass: {
                                    icon: 'swal-icon-small'
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'Failed to add vendor',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    icon: 'swal-icon-small'
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.text(originalText).prop('disabled', false);

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) $('#vendor_name_error').text(errors.name[0]);
                            if (errors.email) $('#vendor_email_error').text(errors.email[0]);
                            if (errors.phone) $('#vendor_phone_error').text(errors.phone[0]);
                            if (errors.state_code) $('#state_code_error').text(errors
                                .state_code[0]);
                            if (errors.pan_number) $('#pan_number_error').text(errors
                                .pan_number[0]);
                            if (errors.gst_number) $('#gst_number_error').text(errors
                                .gst_number[0]);
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            Swal.fire({
                                title: 'Error',
                                text: xhr.responseJSON.message,
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    icon: 'swal-icon-small'
                                }

                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'Something went wrong!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    icon: 'swal-icon-small'
                                }
                            });
                        }
                    }
                });
            });

            // Reset form on modal close
            $('#addVendorModal').on('hidden.bs.modal', function() {
                $('#addVendorForm')[0].reset();
                $('#addVendorForm .text-danger').text('');
            });

            $('#addProductModal').on('hidden.bs.modal', function() {
                activeProductRow = null;
                resetProductModal();
            });

            // Bank Modal
            $('#addBankBtn').on('click', function() {
                $('#addBankModal').modal('show');
                $('#addBankForm')[0].reset();
                $('#addBankForm .error-text').text('');
            });

            $('#saveBankBtn').on('click', function() {
                let $btn = $(this);
                let originalText = $btn.text();

                $('#addBankForm .error-text').text('');

                let bankData = {
                    bank_name: $('#bank_name_input').val().trim(),
                    account_number: $('#account_number_input').val().trim(),
                    ifsc_code: $('#ifsc_code_input').val().trim(),
                    branch_name: $('#branch_name_input').val().trim(),
                    opening_balance: $('#opening_balance_input').val().trim(),
                    status: $('#bank_status_input').val(),
                    selectedSubAdminId: localStorage.getItem('selectedSubAdminId') || null
                };

                let hasError = false;
                if (!bankData.bank_name) { $('#bank_name_error').text('Bank name is required'); hasError = true; }
                if (!bankData.account_number) { $('#account_number_error').text('Account number is required'); hasError = true; }
                if (!bankData.ifsc_code) { $('#ifsc_code_error').text('IFSC code is required'); hasError = true; }
                if (!bankData.branch_name) { $('#branch_name_error').text('Branch name is required'); hasError = true; }
                if (!bankData.opening_balance) { $('#opening_balance_error').text('Opening balance is required'); hasError = true; }

                if (hasError) return;

                $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);

                $.ajax({
                    url: '/api/banks',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Authorization': 'Bearer ' + authToken,
                    },
                    contentType: 'application/json',
                    data: JSON.stringify(bankData),
                    success: function(response) {
                        $btn.text(originalText).prop('disabled', false);
                        if (response.status) {
                            let newBank = response.data;
                            let newOption = new Option(newBank.bank_name, newBank.id, true, true);
                            $('#bank_id').append(newOption).trigger('change');
                            $('#addBankModal').modal('hide');
                            Swal.fire({
                                title: 'Success',
                                text: 'Bank added successfully',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#ff9f43'
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.text(originalText).prop('disabled', false);
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.bank_name) $('#bank_name_error').text(errors.bank_name[0]);
                            if (errors.account_number) $('#account_number_error').text(errors.account_number[0]);
                            if (errors.ifsc_code) $('#ifsc_code_error').text(errors.ifsc_code[0]);
                            if (errors.branch_name) $('#branch_name_error').text(errors.branch_name[0]);
                            if (errors.opening_balance) $('#opening_balance_error').text(errors.opening_balance[0]);
                        } else {
                            Swal.fire('Error', 'Something went wrong', 'error');
                        }
                    }
                });
            });

            // ✅ Common formatting handlers
            $(document).on("focus",
                ".product-discount-amount-input, #shipping, #amount_input, #cash_amount_input, #upi_amount_input",
                function() {
                    let val = $(this).val();
                    if (val) {
                        $(this).val(parseIndianNumber(val) || '');
                    }
                });

            $(document).on("blur",
                ".product-discount-amount-input, #shipping, #amount_input, #cash_amount_input, #upi_amount_input",
                function() {
                    let val = $(this).val();
                    if (val !== "" && !isNaN(parseIndianNumber(val))) {
                        $(this).val(formatIndianNumber(parseIndianNumber(val)));
                    }
                    if ($(this).hasClass("product-discount-amount-input")) {
                        let row = $(this).closest(".form-row");
                        applyRowDiscount(row, 'amount');
                        calculateTotal();
                    }
                });
            //  $(".select2, .category-select").select2({
            //     tags: true,
            // });
            let invalid = false;

            $('#upi_amount_input').on('keyup input', function() {
                let value = parseIndianNumber($(this).val());
                let $errorSpan = $('#upi_amount_error');

                if (!isNaN(value) && value < 0) {
                    invalid = true;
                    $errorSpan.text('Negative amount is not valid');
                } else {
                    invalid = false;
                    $errorSpan.text('');
                }
            });
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('price-input')) {
                    let val = e.target.value;

                    // allow empty while typing
                    if (val === '') return;

                    // prevent negative numbers
                    if (parseIndianNumber(val) < 0) {
                        e.target.value = 0;
                    }
                }
            });
            $('#amount_input').on('keyup input', function() {
                let value = parseIndianNumber($(this).val());
                let $errorSpan = $('#amount_error');

                if (!isNaN(value) && value < 0) {
                    invalid = true;
                    $errorSpan.text('Negative amount is not valid');
                } else {
                    invalid = false;
                    $errorSpan.text('');
                }
            });
            $('#cash_amount_input').on('keyup input', function() {
                let value = parseIndianNumber($(this).val());
                let $errorSpan = $('#cash_amount_error');

                if (!isNaN(value) && value < 0) {
                    invalid = true;
                    $errorSpan.text('Negative amount is not valid');
                } else {
                    invalid = false;
                    $errorSpan.text('');
                }
            });

            function resetPaymentFields() {
                $("#payment_mode").val("").trigger("change");
                $("#paid_type").val("").trigger("change");
                $("#bank_id").val("").trigger("change");
                $("#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount").val("");
                $(".error-payment_mode, .error-paid_type, .error-bank_id").text("");
                $("#amount_error, #cash_amount_error, #upi_amount_error, #pending_error").text("");
            }

            // REMOVED: togglePaymentModeVisibility function - Payment Mode is always visible now
            // REMOVED: purchase status condition for payment mode

            // Payment mode is always enabled
            $("#payment_mode").prop('disabled', false);

            // REMOVED: MutationObserver for grand total changes affecting payment mode visibility

            $(".purchase-status-select").on("change", function() {
                // Payment mode visibility no longer depends on status
                // Keeping this empty or removing if no other logic needed
            });

            $("#payment_mode").change(function() {
                const selectedMode = $(this).val();
                $(".error-payment_mode").text("");
                // Reset values
                $("#paid_type").val("");
                $("#bank_id").val("").trigger('change');
                $("#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount").val("");

                // Hide everything first
                $("#paid_type_container, #amount_input_container, #cash_amount_input_container, #upi_amount_input_container, #pending_amount_container, #bank_container")
                    .addClass("d-none");

                // ✅ ONLY show Paid Type for real payment modes
                if (selectedMode === "cash" || selectedMode === "online" || selectedMode === "cashonline") {
                    $("#paid_type_container").removeClass("d-none");
                }

                if (selectedMode === "online" || selectedMode === "cashonline") {
                    $("#bank_container").removeClass("d-none");
                }

                // ✅ Pending = no paid type, no amount, no validation
                if (selectedMode === "pending") {
                    return; // stop here
                }
            });


            $("#paid_type").change(function() {
                const type = $(this).val();
                const selectedMode = $("#payment_mode").val();

                // Clear all fields and errors
                $("#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount").val("");
                $("#cash_amount_error, #upi_amount_error").text("");

                // Hide all input containers first
                $("#amount_input_container, #pending_amount_container, #cash_amount_input_container, #upi_amount_input_container")
                    .addClass("d-none");

                // Remove required attributes initially
                $("#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount").prop("required",
                    false);

                if (type === "full") {
                    invalid1 = false;
                    if (selectedMode === "cash") {
                        // $("#amount_input_container").removeClass("d-none");
                        // $("#amount_input").prop("required", true);

                    } else if (selectedMode === "online") {
                        // $("#amount_input_container").removeClass("d-none");
                        // $("#amount_input").prop("required", true);

                    } else if (selectedMode === "cashonline") {
                        // Show both if cash + online
                        $("#cash_amount_input_container").removeClass("d-none");
                        $("#upi_amount_input_container").removeClass("d-none");

                        $("#cash_amount_input").prop("required", true);
                        $("#upi_amount_input").prop("required", true);
                    }

                } else if (type === "partial") {
                    if (selectedMode === "cash" || selectedMode === "online") {
                        // Normal partial
                        $("#amount_input_container").removeClass("d-none");
                        $("#pending_amount_container").removeClass("d-none");

                        $("#amount_input").prop("required", true);
                        $("#pending_amount").prop("required", true);

                    } else if (selectedMode === "cashonline") {
                        // Partial with cash + online
                        $("#cash_amount_input_container").removeClass("d-none");
                        $("#upi_amount_input_container").removeClass("d-none");
                        $("#pending_amount_container").removeClass("d-none");

                        $("#cash_amount_input").prop("required", true);
                        $("#upi_amount_input").prop("required", true);
                        $("#pending_amount").prop("required", true);
                    }
                }
            });

            $("#amount_input").on("input", function() {
                const partialPaid = parseIndianNumber($(this).val());
                const payable = parseIndianNumber($("#grand-total").text());
                const pending = payable - partialPaid;

                $("#pending_amount").val(pending > 0 ? formatIndianNumber(pending) : "0.00");
            });
            let invalid1 = false;
            $("#cash_amount_input, #upi_amount_input").on("input", function() {
                const cashPaid = parseIndianNumber($("#cash_amount_input").val());
                const onlinePaid = parseIndianNumber($("#upi_amount_input").val());
                const payable = parseIndianNumber($("#grand-total").text());

                const totalPaid = cashPaid + onlinePaid;
                const pending = payable - totalPaid;

                // Update pending field
                $("#pending_amount").val(pending >= 0 ? formatIndianNumber(pending) : "0.00");


                // Validation
                if (pending < 0 || isNaN(pending)) {
                    invalid1 = true;
                    $("#pending_error").text("Enter correct amount cash + Online"); // <-- your error span
                } else {
                    invalid1 = false;
                    $("#pending_error").text("");
                }
            });

            $("#amount_input").on("input", function() {
                const cashPaid = parseIndianNumber($("#amount_input").val());

                const payable = parseIndianNumber($("#grand-total").text());

                const totalPaid = cashPaid;
                const pending = payable - totalPaid;

                // Update pending field
                $("#pending_amount").val(pending > 0 ? formatIndianNumber(pending) : "0.00");

                // Validation
                if (pending <= 0 || isNaN(pending)) {
                    invalid1 = true;
                    $("#pending_error").text(
                        "Cannot enter more than payable amount"); // <-- your error span
                } else {
                    invalid1 = false;
                    $("#pending_error").text("");
                }
            });

            function parseIndianNumber(value) {
                return parseFloat(String(value).replace(/,/g, '')) || 0;
            }

            function formatIndianNumber(num) {
                if (num === null || num === undefined || num === '') {
                    return '0.00';
                }
                let number = typeof num === 'string' ? parseFloat(num.replace(/,/g, '')) : num;
                if (isNaN(number)) {
                    return '0.00';
                }
                let [intPart, decPart] = number.toFixed(2).split('.');
                let lastThree = intPart.slice(-3);
                let rest = intPart.slice(0, -3);
                if (rest) {
                    lastThree = rest.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + lastThree;
                }
                return lastThree + '.' + decPart;
            }

            function getRowAmountForDiscount(row) {
                let price = parseIndianNumber(row.find(".price-input").val()) || 0;
                let quantity = parseFloat(row.find(".quantity-input").val()) || 0;
                let baseTotal = price * quantity;
                
                return baseTotal;
            }

            function getRowDiscount(row, source = 'percent') {
                let amountForDiscount = getRowAmountForDiscount(row); // price * quantity
                
                let productGstAmount = 0;
                if ($('#with_gst').is(':checked')) {
                    let productOption = row.find(".product-select option:selected");
                    let gstOption = productOption.data("gst-option");
                    if (gstOption === 'with_gst') {
                        let gstData = productOption.data("gst");
                        if (gstData) {
                            try {
                                let taxes = gstData;
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                if (Array.isArray(taxes)) {
                                    taxes.forEach(tax => {
                                        let taxRate = parseFloat(tax.tax_rate) || 0;
                                        productGstAmount += (amountForDiscount * taxRate) / 100;
                                    });
                                }
                            } catch(e) {}
                        }
                    }
                }

                let inclusiveTotal = amountForDiscount + productGstAmount;

                let discountPercent = Math.min(100, Math.max(0,
                    parseIndianNumber(row.find(".product-discount-input").val()) || 0));
                let discountAmount = Math.max(0,
                    parseIndianNumber(row.find(".product-discount-amount-input").val()) || 0);

                if (inclusiveTotal <= 0) {
                    return {
                        amountForDiscount,
                        discountPercent,
                        discountAmount,
                        finalRowTotal: 0
                    };
                }

                if (source === 'amount') {
                    discountAmount = Math.min(inclusiveTotal, discountAmount);
                    discountPercent = Math.min(100, (discountAmount / inclusiveTotal) * 100);
                } else {
                    discountPercent = Math.min(100, discountPercent);
                    discountAmount = Math.min(inclusiveTotal, (inclusiveTotal * discountPercent) / 100);
                }

                return {
                    amountForDiscount,
                    discountPercent,
                    discountAmount,
                    finalRowTotal: Math.max(0, inclusiveTotal - discountAmount)
                };
            }

            function applyRowDiscount(row, source = 'percent') {
                let result = getRowDiscount(row, source);

                if (result.amountForDiscount <= 0) {
                    row.find(".total-input").val('0.00');
                    return result;
                }

                if (source === 'amount') {
                    row.find(".product-discount-input").val(result.discountPercent.toFixed(2));
                } else {
                    row.find(".product-discount-amount-input").val(result.discountAmount.toFixed(2));
                }

                row.find(".total-input").val(result.finalRowTotal.toFixed(2));
                return result;
            }

            function validateFullPayment() {
                const payable = parseIndianNumber($("#grand-total").text());
                const cash = parseIndianNumber($("#cash_amount_input").val());
                const upi = parseIndianNumber($("#upi_amount_input").val());
                const total = cash + upi;

                // Clear previous messages
                $("#cash_amount_error").text("");
                $("#upi_amount_error").text("");

                if ($("#paid_type").val() === "full") {
                    if (total !== payable) {
                        const msg = `Cash + UPI must equal payable amount (₹${payable})`;
                        $("#cash_amount_error").text(msg);
                        $("#upi_amount_error").text(msg);
                        return false;
                    }
                }

                return true;
            }


            // Bind validation on input
            $("#cash_amount_input, #upi_amount_input").on("input", function() {
                validateFullPayment();
            });
            // Add new form row dynamically
            $(document).on("click", ".add-row", function() {
                const categoryOptions = buildCategoryOptions();

                let row = `
                                            <div class="row form-row purchase-product-row">

                                                <div class="col-lg-2 col-sm-6 col-6">
                                                    <div class="form-group">
                                                        <label>Category Name</label>
                                                        <select name="category_name[]" class="form-control category-select">
                                                            ${categoryOptions}
                                                        </select>
                                                        <span class="error text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-sm-6 col-6">
                                                    <div class="form-group">
                                                        <label>Product Name</label>
                                                        <select name="product_name[]" class="form-control select2 product-select" disabled>
                                                            <option value="">Product Name</option>
                                                        </select>
                                                        <div class="product-gst-info mt-1"></div>
                                                        <span class="error text-danger product-error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-sm-6 col-6">
                                                    <div class="form-group">
                                                        <label>Product Price</label>
                                                        <input type="text"  inputmode="decimal" name="price[]" class="form-control price-input" placeholder="Enter Price" min="0" oninput="this.value = this.value < 0 ? 0 : this.value">
                                                        <span class="error text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-6 col-6">
                                                    <div class="form-group">
                                                        <label>Quantity</label>
                                                        <input type="text" name="quantity[]" class="form-control quantity-input" placeholder="Qty" value="0" min="0" step="0.01" inputmode="decimal" oninput="this.value = this.value < 0 ? 0 : this.value">
                                                        <span class="error text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-6 col-6">
                                                    <div class="form-group">
                                                        <label>Disc %</label>
                                                        <input type="text" name="product_discount[]" class="form-control product-discount-input" placeholder="0.00" value="0" min="0" max="100" inputmode="decimal">
                                                        <span class="error text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-6 col-6">
                                                    <div class="form-group">
                                                        <label>Disc-Amt</label>
                                                        <input type="text" name="product_discount_amount[]" class="form-control product-discount-amount-input" placeholder="0.00" value="0" min="0">
                                                        <span class="error text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-6 col-6 serial-no-container">
                                                    <div class="form-group">
                                                        <div class="serial-no-btn-wrapper" style="display:none;">
                                                            <label>Serial No <a href="javascript:void(0)" class="edit-serial-btn text-warning ml-1" title="Edit Serial Numbers"><i class="fas fa-edit"></i></a></label>
                                                            <div class="serial-status text-muted" style="font-size: 11px; line-height: 1.2;">0/0 serial numbers added</div>
                                                            <input type="hidden" class="serial-data-input" name="imei_no[]" value="[]">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-6 col-12">
                                                    <div class="form-group">
                                                        <label>Total Amount</label>
                                                        <input type="text" name="total[]" class="form-control total-input" placeholder="0" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-6 add-row-btn">
                                                    <button type="button" class="btn btn-danger remove-row">x</button>
                                                </div>
                                            </div>`;

                let newRow = $(row).appendTo("#form-container");
                updateSerialUI(newRow);

                // Reinitialize Select2 for dynamically added elements
                newRow.find('.category-select').each(function() {
                    $(this).select2({ tags: true, placeholder: "Category Name", dropdownParent: $(this).parent() });
                });
                newRow.find('.product-select').each(function() {
                    $(this).select2({ tags: true, placeholder: "Product Name", dropdownParent: $(this).parent() });
                });

                // Scroll to the new row
                newRow[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            });

            // Remove form row dynamically
            $(document).on("click", ".remove-row", function() {
                $(this).closest(".form-row").remove();
                calculateTotal();
            });

            // Update Vendor Phone when Vendor is selected
            $("#vendor_name").on("change", function() {
                let phone = $(this).find(":selected").data("phone");
                $("#vendor_phone").val(phone ? phone : "");
            });



            // Update Product Price when selecting a Product
            $(document).ready(function() {

                $('.category-select').on('change', function() {
                    var selectedCategory = $(this).val();
                    var $row = $(this).closest('.form-row');
                    var $productDropdown = $(this).closest('.col-lg-3, .col-sm-6').siblings().find(
                        '.product-select');

                    // Clear current options
                    let currentValue = $productDropdown.val();
                    $productDropdown.empty();
                    $row.find(".product-gst-info").empty();

                    if (currentValue && isNaN(currentValue)) {
                        // preserve typed product
                        $productDropdown.append(
                            `<option value="${currentValue}" selected>${currentValue}</option>`
                        );
                    }


                    if (selectedCategory) {
                        // Enable dropdown when category is selected
                        $productDropdown.prop('disabled', false);

                        // Add default placeholder
                        $productDropdown.append('<option value="">Product Name</option>');

                        // Filter products by category
                        var filteredProducts = products.filter(p => p.category_id ==
                            selectedCategory);

                        filteredProducts.forEach(function(product) {
                            // Ensure product_gst is a string if it's an object, or use it directly if it's already a string
                            let gstVal = (typeof product.product_gst === 'object') ? JSON
                                .stringify(product.product_gst) : product.product_gst;
                            $productDropdown.append(
                                `<option value="${product.id}" data-price="${product.price}" data-gst-option="${product.gst_option}" data-gst='${gstVal}'>${product.name}</option>`
                            );
                        });
                    } else {
                        // No category selected, disable product dropdown and add placeholder
                        $productDropdown.prop('disabled', true);
                        $productDropdown.append('<option value="">Product Name</option>');
                    }

                    // Refresh Select2
                    $productDropdown.trigger('change.select2');
                });
            });



            $(document).ready(function() {
                // Delegated change event for category-select
                // $(document).on('change', '.category-select', function() {
                //     var selectedCategory = $(this).val();

                //     var $row = $(this).closest('.form-row'); // find the row of this select
                //     var $productDropdown = $row.find('.product-select');

                //     // Clear current options
                //     let currentValue = $productDropdown.val();
                //     $productDropdown.empty();
                //     $row.find(".product-gst-info").empty();

                //     if (currentValue && isNaN(currentValue)) {
                //         // preserve typed product
                //         $productDropdown.append(
                //             `<option value="${currentValue}" selected>${currentValue}</option>`
                //         );
                //     }

                //     if (selectedCategory) {
                //         $productDropdown.prop('disabled', false);

                //         $productDropdown.append('<option value="">Product Name</option>');

                //         // Filter products by category
                //         var filteredProducts = products.filter(p => p.category_id ==
                //             selectedCategory);

                //         filteredProducts.forEach(function(product) {
                //             // Ensure product_gst is a string if it's an object, or use it directly if it's already a string
                //             let gstVal = (typeof product.product_gst === 'object') ? JSON.stringify(product.product_gst) : product.product_gst;
                //             $productDropdown.append(
                //                 `<option value="${product.id}" data-price="${product.price}" data-gst-option="${product.gst_option}" data-gst='${gstVal}'>${product.name}</option>`
                //             );
                //         });
                //     } else {
                //         $productDropdown.prop('disabled', true);
                //         $productDropdown.append('<option value="">Product Name</option>');
                //     }

                //     // Refresh Select2
                //     $productDropdown.trigger('change.select2');
                // });
                $(document).on('change', '.category-select', function() {

                    let selectedCategory = $(this).val();
                    let $row = $(this).closest('.form-row');
                    let $productDropdown = $row.find('.product-select');
                    let $error = $row.find('.product-error');

                    // Reset
                    $productDropdown.empty();
                    $error.text('');
                    $row.find(".product-gst-info").empty();

                    // 👉 NEW CATEGORY (typed by user)
                    if (selectedCategory && isNaN(selectedCategory)) {
                        $productDropdown.prop('disabled', false);

                        $productDropdown.append('<option value="">Product Name</option>');
                        $productDropdown.select2({
                            tags: true
                        });

                        return; // stop here
                    }

                    // 👉 NO CATEGORY
                    if (!selectedCategory) {
                        $productDropdown.prop('disabled', true);
                        $productDropdown.append('<option value="">Product Name</option>');
                        return;
                    }

                    // 👉 EXISTING CATEGORY
                    let filteredProducts = products.filter(p => p.category_id == selectedCategory);

                    $productDropdown.prop('disabled', false);
                    $productDropdown.append('<option value="">Product Name</option>');

                    if (filteredProducts.length === 0) {
                        // ❌ Category has NO products
                        $error.text('No products available for this category');
                        return;
                    }

                    // ✅ Category has products
                    filteredProducts.forEach(product => {
                        let gstVal = typeof product.product_gst === 'object' ?
                            JSON.stringify(product.product_gst) :
                            product.product_gst;

                        $productDropdown.append(
                            `<option value="${product.id}"
                data-price="${product.price}"
                data-gst-option="${product.gst_option}"
                data-gst='${gstVal}'>
                ${product.name}
            </option>`
                        );
                    });

                    $productDropdown.trigger('change.select2');
                });




                // Delegated change event for product-select
                $(document).on('change', '.product-select', function() {

                    let selectedOption = $(this).find("option:selected");
                    let price = selectedOption.data("price") || 0;

                    let $row = $(this).closest('.form-row');

                    // set price
                    $row.find(".price-input").val(price);

                    // get quantity
                    let quantity = parseFloat($row.find(".quantity-input").val()) || 0;

                    // calculate base total
                    let baseTotal = price * quantity;

                    // set total immediately
                    $row.find(".total-input").val(baseTotal.toFixed(2));

                    // update gst
                    updateProductGstInfo($row);

                    // recalc discount & gst properly
                    $row.find(".product-discount-input").trigger("input");

                    // update grand total
                    calculateTotal();
                });
                $(document).on("input", ".price-input", function() {
                    let val = this.value;

                    // allow only digits + one dot
                    val = val.replace(/[^0-9.]/g, '');
                    if ((val.match(/\./g) || []).length > 1) {
                        val = val.slice(0, -1);
                    }

                    this.value = val;

                    // calculate totals WITHOUT touching price again
                    let row = $(this).closest(".form-row");
                    let price = parseFloat(val) || 0;
                    let quantity = parseFloat(row.find(".quantity-input").val()) || 0;

                    row.find(".total-input").val((price * quantity).toFixed(2));
                    updateProductGstInfo(row);
                    calculateTotal();
                });
                $(document).on("blur", ".price-input", function() {
                    let val = parseIndianNumber(this.value);
                    if (!isNaN(val)) {
                        this.value = val.toFixed(2);
                    }
                });

                $(document).on("input", ".price-input, .quantity-input, .product-discount-input",
                    function() {
                        let row = $(this).closest(".form-row");
                        let source = $(this).hasClass("product-discount-input")
                            ? 'percent'
                            : (row.data('discount-source') || 'percent');
                        if ($(this).hasClass("product-discount-input")) {
                            row.data('discount-source', 'percent');
                        }
                        applyRowDiscount(row, source);
                        updateProductGstInfo(row);
                        calculateTotal();
                    });

                $(document).on("input", ".product-discount-amount-input", function() {
                    let row = $(this).closest(".form-row");
                    row.data('discount-source', 'amount');
                    applyRowDiscount(row, 'amount');
                    updateProductGstInfo(row);
                    calculateTotal();
                });

                $(document).on("blur", ".product-discount-input", function() {
                    let val = parseIndianNumber(this.value);
                    if (!isNaN(val)) {
                        this.value = Math.min(100, Math.max(0, val)).toFixed(2);
                    }
                    let row = $(this).closest(".form-row");
                    applyRowDiscount(row, 'percent');
                    calculateTotal();
                });
                $('input[name="gst_option"]').on('change', function() {

                    $(".form-row").each(function() {

                        let row = $(this);

                        // GST info update
                        updateProductGstInfo(row);

                        // 🔹 recalc discount percent → amount
                        row.find(".product-discount-input").trigger("input");

                        // 🔹 recalc discount amount → percent
                        row.find(".product-discount-amount-input").trigger("input");

                    });

                    calculateTotal();
                });

                function updateProductGstInfo($row) {
                    let selectedOption = $row.find(".product-select option:selected");
                    let gstOption = selectedOption.data("gst-option");
                    let gstData = selectedOption.data("gst");

                    let price = parseIndianNumber($row.find(".price-input").val()) || 0;
                    let quantity = parseFloat($row.find(".quantity-input").val()) || 0;
                    let taxableAmount = price * quantity;

                    let $gstContainer = $row.find(".product-gst-info");
                    $gstContainer.empty();

                    // Check global toggle first
                    if (!$('#with_gst').is(':checked')) {
                        return;
                    }

                    if (gstOption === 'with_gst' && gstData) {
                        try {
                            let taxes = gstData;
                            if (typeof taxes === "string") taxes = JSON.parse(taxes);
                            if (typeof taxes === "string") taxes = JSON.parse(taxes);

                            if (Array.isArray(taxes)) {
                                let totalTaxAmount = 0;
                                
                                let taxDetails = taxes.map(tax => {
                                    let taxRate = parseFloat(tax.tax_rate) || 0;
                                    let taxAmount = (taxableAmount * taxRate) / 100;
                                    totalTaxAmount += taxAmount;
                                    return `${tax.tax_name}: ${tax.tax_rate}%`;
                                }).join(', ');

                                let gstHtml = `<div style="font-size: 11px; color: #666; background: #f8f9fa; padding: 5px; border-radius: 4px; border-left: 3px solid #1b2850; margin-top: 5px;">
                                    <div><strong>Total GST: {{ $currencySymbol }}${totalTaxAmount.toFixed(2)}</strong></div>
                                    <div style="font-size: 10px;">(${taxDetails})</div>
                                </div>`;
                                $gstContainer.html(gstHtml);
                            }
                        } catch (e) {
                            // console.error("Error parsing GST data", e);
                        }
                    } else if (gstOption === 'without_gst') {
                        $gstContainer.html(
                            '<small class="text-muted" style="font-size: 11px;">No GST for this product</small>'
                        );
                    }
                }
            });





            // Calculate Total when Price or Quantity is updated


            function calculateTotal() {
                let totalBaseAmount = 0;
                let totalDiscountAmount = 0;
                let totalTaxAmount = 0;
                let products = [];
                let taxTotals = {};

                $(".form-row").each(function() {
                    let productOption = $(this).find(".product-select option:selected");
                    let productId = productOption.val();
                    let categoryId = $(this).find(".category-select").val();
                    let price = parseIndianNumber($(this).find(".price-input").val());
                    let quantity = parseFloat($(this).find(".quantity-input").val()) || 0;

                    let discountSource = $(this).data('discount-source') || 'percent';
                    let rowDiscount = getRowDiscount($(this), discountSource);
                    let discountPercent = rowDiscount.discountPercent;
                    let itemDiscountAmount = rowDiscount.discountAmount;
                    let finalRowTotal = rowDiscount.finalRowTotal;
                    let baseTotal = price * quantity;
                    let itemGstAmount = 0;

                    // Product-wise GST calculation
                    let productGstOption = productOption.data("gst-option");
                    if ($('#with_gst').is(':checked') && productGstOption === 'with_gst') {
                        let gstData = productOption.data("gst");
                        if (gstData) {
                            try {
                                let taxes = gstData;
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);

                                if (Array.isArray(taxes)) {
                                    taxes.forEach(tax => {
                                        let taxName = tax.tax_name;
                                        let taxRate = parseFloat(tax.tax_rate) || 0;
                                        let taxableAmount = baseTotal;
                                        let taxAmount = (taxableAmount * taxRate) / 100;
                                        itemGstAmount += taxAmount;

                                        if (!taxTotals[taxName]) {
                                            taxTotals[taxName] = {
                                                id: tax.tax_id,
                                                rate: tax.tax_rate,
                                                amount: 0
                                            };
                                        }
                                        taxTotals[taxName].amount += taxAmount;
                                    });
                                }
                            } catch (e) {
                                // console.error("Error parsing GST data in calculation", e);
                            }
                        }
                    }

                    let imeiNo = [];
                    try {
                        let imeiVal = $(this).find('.serial-data-input').val();
                        if (imeiVal && imeiVal !== 'null') {
                            imeiNo = JSON.parse(imeiVal);
                        }
                    } catch(e) {}

                    if (productId) {
                        products.push({
                            id: productId,
                            category_id: categoryId,
                            price: price,
                            quantity: quantity,
                            discount_percent: discountPercent,
                            discount_amount: itemDiscountAmount,
                            total: finalRowTotal, // Total with GST and discount applied
                            imei_no: JSON.stringify(imeiNo)
                        });
                    }

                    totalBaseAmount += baseTotal;
                    totalTaxAmount += itemGstAmount;
                    totalDiscountAmount += itemDiscountAmount;

                    $(this).find(".total-input").val(formatIndianNumber(finalRowTotal));
                });

                // **Update Summary Display**
                $("#total-product-amount").text(formatIndianNumber(totalBaseAmount));

                // let shipping = parseFloat($("#shipping").val()) || 0;
                let shipping = parseIndianNumber($("#shipping").val());

                if ($('#with_gst').is(':checked')) {
                    $('#gst-section').show();
                    $("#total-gst-amount").text(formatIndianNumber(totalTaxAmount));
                } else {
                    $('#gst-section').hide();
                    $("#total-gst-amount").text('0.00');
                }

                let priceAfterDiscount = Math.max(0, (totalBaseAmount + totalTaxAmount) - totalDiscountAmount);

                // Grand Total = Price after discount + Shipping
                let grandTotal = Math.max(0, priceAfterDiscount + shipping);

                // Calculate round off (difference between exact and rounded value)
                let roundedGrandTotal = Math.round(grandTotal);
                let roundOff = roundedGrandTotal - grandTotal;

                // Always show round off section
                $('#round-off-section').show();
                $("#round-off-amount").text(formatIndianNumber(roundOff));

                // Display rounded grand total
                $("#grand-total").text(formatIndianNumber(roundedGrandTotal));
                $("#shipping-amount").text(formatIndianNumber(shipping));
                $("#total-discount-amount").text(formatIndianNumber(totalDiscountAmount));
                $("#price-after-discount").text(formatIndianNumber(priceAfterDiscount));

                if (shipping > 0) {
                    $('#shipping-section').show();
                } else {
                    $('#shipping-section').hide();
                }

                // Hide discount sections when discount is 0
                if (totalDiscountAmount == 0) {
                    $('#discount-amount-section').hide();
                    $('#price-after-discount-section').hide();
                } else {
                    $('#discount-amount-section').show();
                    $('#price-after-discount-section').show();
                }

                return {
                    products: products,
                    totalProductAmount: totalBaseAmount,
                    totalAmount: totalBaseAmount,
                    discount: 0, // General discount removed/not used here
                    discountAmount: totalDiscountAmount,
                    taxAmount: totalTaxAmount,
                    taxTotals: taxTotals,
                    shipping: shipping,
                    grandTotal: grandTotal,
                    grandTotalExact: grandTotal,
                    gstOption: $('#with_gst').is(':checked') ? 'with' : 'without'
                };
            }

            // Update calculation when any relevant input changes
            $(document).on("input",
                "#discount, #shipping, .price-input, .quantity-input, .product-discount-input, .product-discount-amount-input",
                function() {
                    calculateTotal();
                });

            function validatePaymentSection() {
                $(".error-payment_mode, .error-paid_type").text("");
                $("#amount_error, #cash_amount_error, #upi_amount_error, #pending_error").text("");

                const purchaseStatus = $(".purchase-status-select").val();
                const paymentMode = $("#payment_mode").val();
                const paidType = $("#paid_type").val();
                const grandTotal = parseIndianNumber($("#grand-total").text());
                const amount = parseIndianNumber($("#amount_input").val());
                // const amount = parseFloat($("#amount_input").val()) || 0;
                const cash = parseIndianNumber($("#cash_amount_input").val());
                const online = parseIndianNumber($("#upi_amount_input").val());
                const totalPaid = cash + online;

                if (!paymentMode || paymentMode === '') {
                    $(".error-payment_mode").text("Please select a payment mode");
                    return false;
                }
                // ✅ Pending → skip everything
                if (paymentMode === "pending") {
                    return true;
                }

                // ❌ Paid type required for non-pending modes
                if (!paidType) {
                    $(".error-paid_type").text("Paid type is required");
                    return false;
                }

                // 🔸 PARTIAL
                if (paidType === "partial") {

                    if (paymentMode === "cash" || paymentMode === "online") {
                        if (amount <= 0) {
                            $("#amount_error").text("Amount is required");
                            return false;
                        }
                        if (amount >= grandTotal) {
                            $("#amount_error").text("Amount must be less than total");
                            return false;
                        }
                    }

                    if (paymentMode === "cashonline") {
                        if (cash <= 0) {
                            $("#cash_amount_error").text("Cash amount is required");
                            return false;
                        }
                        if (online <= 0) {
                            $("#upi_amount_error").text("Online amount is required");
                            return false;
                        }
                        if (totalPaid >= grandTotal) {
                            $("#pending_error").text("Cash + Online must be less than total");
                            return false;
                        }
                    }
                }

                // 🔸 FULL
                if (paidType === "full" && paymentMode === "cashonline") {
                    if (totalPaid !== grandTotal) {
                        $("#pending_error").text("Cash + Online must equal total");
                        return false;
                    }
                }

                if ((paymentMode === "online" || paymentMode === "cashonline") && !$("#bank_id").val()) {
                    $(".error-bank_id").text("Please select a bank");
                    return false;
                }

                return true;
            }

            // Form Validation
            function validateForm() {
                let isValid = true;

                // Clear previous errors
                $(".error").text("");
                $("#bill_no_unique_error").hide().text('');
                $("#bill_no_error").text('');

                // Validate Vendor
                if ($("#vendor_name").val() === "") {
                    $("#vendor_name").closest(".form-group").find(".error").text("Vendor is required.");
                    isValid = false;
                }

                // Check if bill number is empty
                if ($("#bill_no").val().trim() === "") {
                    $("#bill_no_error").text("Bill number is required.");
                    isValid = false;
                }

                // Check if bill number has unique error from blur validation
                if ($("#bill_no_unique_error").is(':visible')) {
                    isValid = false;
                }

                if ($("#purchase_date").val().trim() === "") {
                    $("#purchase_date_error").text("Purchase date is required.");
                    isValid = false;
                }

                // Validate Product Fields
                $(".form-row").each(function() {
                    let productSelect = $(this).find(".product-select");
                    let categorySelect = $(this).find(".category-select");
                    let priceInput = $(this).find(".price-input");
                    let quantityInput = $(this).find(".quantity-input");
                    let discountInput = $(this).find(".product-discount-input");
                    let discountAmountInput = $(this).find(".product-discount-amount-input");
                    let rowDiscount = getRowDiscount($(this), 'percent');

                    if (productSelect.val() === "") {
                        productSelect.closest(".form-group").find(".error").text("Product is required.");
                        isValid = false;
                    }
                    if (categorySelect.val() === "") {
                        categorySelect.closest(".form-group").find(".error").text("Category is required.");
                        isValid = false;
                    }
                    if (priceInput.val().trim() === "" || parseFloat(priceInput.val()) <= 0) {
                        priceInput.closest(".form-group").find(".error").text("Valid price is required.");
                        isValid = false;
                    }
                    if (quantityInput.val().trim() === "" || parseFloat(quantityInput.val()) <= 0) {
                        quantityInput.closest(".form-group").find(".error").text(
                            "Valid quantity is required.");
                        isValid = false;
                    }
                    if (discountInput.val().trim() !== "" && (parseFloat(discountInput.val()) < 0 ||
                            parseFloat(discountInput.val()) > 100)) {
                        discountInput.closest(".form-group").find(".error").text(
                        "Discount must be 0-100%.");
                        isValid = false;
                    }
                    if (rowDiscount.discountAmount > rowDiscount.amountForDiscount + 0.01) {
                        discountAmountInput.closest(".form-group").find(".error").text(
                            "Discount amount cannot exceed line total.");
                        isValid = false;
                    }
                    if (rowDiscount.finalRowTotal < 0) {
                        discountAmountInput.closest(".form-group").find(".error").text(
                            "Discount cannot make total negative.");
                        isValid = false;
                    }
                });

                // Validate Shipping
                let shipping = parseIndianNumber($("#shipping").val());

                if (shipping < 0) {
                    $("#shipping").closest(".form-group").find(".error").text("Shipping cost cannot be negative.");
                    isValid = false;
                }

                return isValid;
            }

            $(document).ready(function() {
                // Real-time bill number validation on blur
                $("#bill_no").on("blur", function() {
                    let billNo = $(this).val().trim();
                    let selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                    // Clear previous errors
                    $("#bill_no_unique_error").hide().text('');
                    $("#bill_no_error").text('');

                    if (!billNo) {
                        $("#bill_no_error").text("Bill number is required.");
                        return;
                    }

                    // Show loading indicator
                    $(this).css('border-color', '#ffc107');

                    $.ajax({
                        url: "/api/check-bill-no-unique",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            "Authorization": "Bearer " + authToken,
                        },
                        contentType: "application/json",
                        data: JSON.stringify({
                            bill_no: billNo,
                            invoice_id: null,
                            selectedSubAdminId: selectedSubAdminId
                        }),
                        success: function(response) {
                            if (response.isUnique === false) {
                                $("#bill_no_unique_error")
                                    .text(response.message ||
                                        'Bill number already exists. Please use a different bill number.'
                                        )
                                    .show();
                                // $("#bill_no").addClass('is-invalid');
                                // $("#bill_no").css('border-color', '#dc3545');
                            } else {
                                $("#bill_no_unique_error").hide();
                                // $("#bill_no").removeClass('is-invalid');
                                // $("#bill_no").css('border-color', '#28a745');
                                setTimeout(function() {
                                    $("#bill_no").css('border-color', '');
                                }, 1000);
                            }
                        },
                        error: function() {
                            // Silently fail - don't block user
                            $("#bill_no").css('border-color', '');
                        }
                    });
                });

                // Clear error when user starts typing
                $("#bill_no").on("input", function() {
                    $("#bill_no_unique_error").hide().text('');
                    $("#bill_no_error").text('');
                    $(this).removeClass('is-invalid');
                    // $(this).css('border-color', '');
                });
            });


            // Submit Form via AJAX
            $("#submitPurchaseBtn").click(function(e) {
                e.preventDefault();

                let $btn = $(this);
                let originalContent = $btn.html();

                // Show spinner and disable button
                $btn.html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                ).css("pointer-events", "none");

                // First validate the form fields
                if (!validateForm()) {
                    $btn.html(originalContent).css("pointer-events", "auto");
                    return;
                }

                if (!validatePaymentSection()) {
                    $btn.html(originalContent).css("pointer-events", "auto");
                    return;
                }

                // Validate unique bill number
                let billNo = $("#bill_no").val().trim();
                let selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                // Clear previous unique error
                $("#bill_no_unique_error").hide().text('');
                $("#bill_no_error").text('');

                // If bill number is empty, show error and stop
                if (!billNo) {
                    $("#bill_no_error").text("Bill number is required.");
                    $btn.html(originalContent).css("pointer-events", "auto");
                    return;
                }

                // Check bill number uniqueness via AJAX
                $.ajax({
                    url: "/api/check-bill-no-unique",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        "Authorization": "Bearer " + authToken,
                    },
                    contentType: "application/json",
                    data: JSON.stringify({
                        bill_no: billNo,
                        invoice_id: null,
                        selectedSubAdminId: selectedSubAdminId
                    }),
                    success: function(response) {
                        // Check if bill number is unique
                        if (response.isUnique === false) {
                            // Bill number already exists
                            $("#bill_no_unique_error")
                                .text(response.message ||
                                    'Bill number already exists. Please use a different bill number.'
                                    )
                                .show();
                            $btn.html(originalContent).css("pointer-events", "auto");
                            return;
                        }

                        // Bill number is unique, proceed with submission
                        submitPurchaseForm($btn, originalContent, authToken,
                        selectedSubAdminId);
                    },
                    error: function(xhr) {
                        $btn.html(originalContent).css("pointer-events", "auto");

                        let errorMessage = 'Something went wrong. Please try again.';

                        // Laravel validation error (422)
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            if (xhr.responseJSON.errors.bill_no) {
                                errorMessage = xhr.responseJSON.errors.bill_no[0];
                            }
                        }
                        // Custom API error message
                        else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        $("#bill_no_unique_error").text(errorMessage).show();
                    }
                });
            });
            // Function to submit purchase form
            function submitPurchaseForm($btn, originalContent, authToken, selectedSubAdminId) {
                let formData = calculateTotal();
                formData.vendor_id = $("#vendor_name").val();
                formData.vendor_phone = $("#vendor_phone").val();
                formData.status = $(".purchase-status-select").val();

                //                let billNo = $("#bill_no").val().trim();
                // if (!billNo) {
                //     $("#bill_no_error").text("Bill number is required.");
                //     $btn.html(originalContent).css("pointer-events", "auto");
                //     return;
                // }

                // // Check if unique error is still showing
                // if ($("#bill_no_unique_error").is(':visible')) {
                //     $btn.html(originalContent).css("pointer-events", "auto");
                //     return;
                // }

                let taxes = [];
                let gstOption = $("input[name='gst_option']:checked").val();

                if (gstOption === "with") {
                    for (let taxName in formData.taxTotals) {
                        let taxData = formData.taxTotals[taxName];
                        taxes.push({
                            id: taxData.id,
                            name: taxName,
                            rate: taxData.rate,
                            amount: taxData.amount
                        });
                    }
                }
                // Get GST option (radio)
                let gst_option = $("input[name='gst_option']:checked").val();
                // Get payment details
                let bill_no = $("#bill_no").val();
                let purchase_date = formatPurchaseDateForApi($("#purchase_date").val());
                let payment_mode = $("#payment_mode").val();
                let remark = $("#remark").val();
                let bank_id = $("#bank_id").val();
                let paid_type = $("#paid_type").val();
                let cash_amount = parseIndianNumber($("#cash_amount_input").val());
                let upi_amount = parseIndianNumber($("#upi_amount_input").val());
                let amount = null;

                if (paid_type === 'full') {
                    amount = parseIndianNumber(formData.grandTotal);
                } else {
                    amount = parseIndianNumber($("#amount_input").val());
                }

                // console.log(amount);
                // console.log(paid_type);
                // console.log(formData.grandTotal);
                let remaining_amount = parseIndianNumber($("#pending_amount").val());

                $.ajax({
                    url: "/api/purchase_order",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        "Authorization": "Bearer " + authToken,
                    },
                    contentType: "application/json",
                    data: JSON.stringify({
                        vendor_id: formData.vendor_id,
                        vendor_phone: formData.vendor_phone,
                        status: formData.status,
                        discount: formData.discount,
                        shipping: formData.shipping,
                        grand_total: formData.grandTotalExact,
                        products: formData.products,
                        taxes: taxes,
                        gst_option: gst_option,
                        payment_mode: payment_mode,
                        bank_id: bank_id,
                        bill_no: bill_no,
                        purchase_date: purchase_date,
                        remark: remark,
                        paid_type: paid_type,
                        cash_amount: cash_amount,
                        upi_amount: upi_amount,
                        amount: amount,
                        remaining_amount: remaining_amount,
                        selectedSubAdminId: selectedSubAdminId
                    }),
                    success: function(response) {
                        $btn.html(originalContent).css("pointer-events", "auto");

                        const purchaseId = response.purchase_id;

                        if (response.success) {
                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43",
                                customClass: {
                                    icon: 'swal-icon-small'
                                } // Set custom button color
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Open the PDF in a new tab
                                    window.open("/purchase/invoice/pdf/" + purchaseId,
                                        "_blank");

                                    // Redirect to purchase view page
                                    window.location.href = "/print-purchase/" +
                                        purchaseId;
                                }
                            });
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        $btn.html(originalContent).css("pointer-events", "auto");
                        $(".error").text("");

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;

                            // Show bill_no unique error in span
                            if (errors.bill_no) {
                                $("#bill_no_unique_error")
                                    .text(errors.bill_no[0])
                                    .show();
                                $("#bill_no")[0].scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                                return;
                            }

                            // Show vendor_id error
                            if (errors.vendor_id) {
                                $("#vendor_name").closest(".form-group").find(".error")
                                    .text(errors.vendor_id[0]);
                                return;
                            }

                            // Show purchase_date error
                            if (errors.purchase_date) {
                                $("#purchase_date_error").text(errors.purchase_date[0]);
                                return;
                            }

                            // Show shipping error
                            if (errors.shipping) {
                                $("#shipping").closest(".form-group").find(".error")
                                    .text(errors.shipping[0]);
                                return;
                            }

                            // Handle IMEI unique errors dynamically
                            let imeiErrors = [];
                            $.each(errors, function(key, val) {
                                if (key.includes('imei_error')) {
                                    imeiErrors.push(val[0]);
                                }
                            });

                            if (imeiErrors.length > 0) {
                                Swal.fire({
                                    title: "IMEI Error",
                                    html: imeiErrors.join('<br>'),
                                    icon: "error",
                                    confirmButtonColor: "#ff9f43"
                                });
                                return;
                            }

                            // Fallback for other validation errors
                            Swal.fire({
                                title: "Error",
                                text: xhr.responseJSON.message || "Validation failed",
                                icon: "error",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43",
                                customClass: {
                                    icon: 'swal-icon-small'
                                }
                            });

                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            let message = xhr.responseJSON.message;

                            if (message.includes("phone number")) {
                                $("#vendor_phone").closest(".form-group").find(".error").text(message);
                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: message,
                                    icon: "error",
                                    confirmButtonText: "OK",
                                    customClass: {
                                        icon: 'swal-icon-small'
                                    }
                                });
                            }
                        } else {
                            alert("Something went wrong!");
                        }
                    }

                });
            }
        });

        // ==================== PURCHASE BARCODE SCANNER ====================
        let purchaseHtml5QrCode = null;

        function playBeep() {
            try {
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

                // Double beep for success
                beep(0, 1200);
                beep(0.25, 1500);
            } catch (e) {
                console.log('Beep audio not available');
            }
        }

        async function onPurchaseScanSuccess(decodedText) {
            console.log("Purchase scan success:", decodedText);
            playBeep();

            // Stop scanner briefly to prevent duplicate reads, but keep modal open
            stopPurchaseScanner();
            $('#purchase-scan-message').css('color', '#888').text('Looking up product...');

            try {
                const product = await fetchPurchaseProductByBarcode(decodedText);
                if (product) {
                    // keepModalOpen = true → modal stays open for next scan
                    addProductToPurchaseRow(product, true);
                    // Restart scanner after a short pause so user can scan next item
                    setTimeout(() => {
                        startPurchaseScanner();
                    }, 1800);
                } else {
                    $('#purchase-scan-message').css('color', 'red').text('Product not found: ' + decodedText);
                    setTimeout(() => startPurchaseScanner(), 2000);
                }
            } catch (error) {
                $('#purchase-scan-message').css('color', 'red').text('Error: ' + error);
                setTimeout(() => startPurchaseScanner(), 2000);
            }
        }

        function onPurchaseScanError(errorMessage) {
            console.debug("Purchase scan error:", errorMessage);
        }

        function stopPurchaseScanner() {
            if (!purchaseHtml5QrCode) return;

            if (purchaseHtml5QrCode.isScanning) {
                purchaseHtml5QrCode.stop()
                    .then(() => {
                        purchaseHtml5QrCode = null;
                    })
                    .catch(err => {
                        console.error("Stop error:", err);
                        purchaseHtml5QrCode = null;
                    });
            } else {
                purchaseHtml5QrCode = null;
            }
            $('#purchase-scan-message').text('');
        }

        function waitForPurchaseLibrary(callback, retries = 20) {
            if (typeof Html5Qrcode !== "undefined") {
                callback();
            } else if (retries > 0) {
                setTimeout(() => waitForPurchaseLibrary(callback, retries - 1), 200);
            } else {
                $('#purchase-scan-message').text('Scanner library failed to load. Please refresh.').css('color', 'red');
            }
        }

        function startPurchaseScanner() {
            console.log("startPurchaseScanner called");
            waitForPurchaseLibrary(function() {
                if (purchaseHtml5QrCode) {
                    let stopPromise = purchaseHtml5QrCode.isScanning ?
                        purchaseHtml5QrCode.stop() :
                        Promise.resolve();
                    stopPromise.then(() => {
                        purchaseHtml5QrCode = null;
                        initPurchaseScanner();
                    }).catch(() => {
                        purchaseHtml5QrCode = null;
                        initPurchaseScanner();
                    });
                } else {
                    initPurchaseScanner();
                }
            });
        }

        function initPurchaseScanner() {
            try {
                purchaseHtml5QrCode = new Html5Qrcode("purchase-qr-reader");
            } catch (e) {
                console.error("Failed to create Html5Qrcode:", e);
                return;
            }

            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            };

            $('#purchase-scan-message').text('Starting camera...');

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    let cameraId = devices.find(device =>
                        device.label.toLowerCase().includes('back') ||
                        device.label.toLowerCase().includes('rear')
                    )?.id;

                    if (!cameraId) {
                        cameraId = devices[0].id;
                    }

                    purchaseHtml5QrCode.start(
                        cameraId,
                        config,
                        onPurchaseScanSuccess,
                        onPurchaseScanError
                    ).then(() => {
                        $('#purchase-scan-message').text('Point camera at barcode').css('color', 'green');
                    }).catch(err => {
                        console.error("Camera start failed:", err);
                        showPurchaseManualBarcodeInput();
                    });
                } else {
                    showPurchaseManualBarcodeInput();
                }
            }).catch(err => {
                console.error("Camera error:", err);
                showPurchaseManualBarcodeInput();
            });
        }

        function showPurchaseManualBarcodeInput() {
            $('#purchase-scan-message').text('').hide();
            $('#purchase-qr-reader').html(`
                <div style="text-align:center; padding: 30px 20px;">
                    <div style="font-size: 48px; margin-bottom: 10px;margin-top: 60px;"></div>
                    <p style="color:#666; font-size:14px; margin-bottom:16px;">
                        No camera found on this device.<br>Enter barcode manually below:
                    </p>
                    <div style="display:flex; gap:8px; justify-content:center;">
                        <input type="text" id="purchaseManualBarcodeInput" class="form-control"
                            placeholder="Enter barcode / product code"
                            style="max-width:260px; font-size:14px;">
                        <button type="button" class="btn btn-primary" id="purchaseManualBarcodeSubmit">Search</button>
                    </div>
                    <div id="purchaseManualBarcodeError" style="font-size:14px; margin-top:8px; font-weight:500;"></div>
                </div>
            `);

            $('#purchase-qr-reader').off('click', '#purchaseManualBarcodeSubmit').on('click', '#purchaseManualBarcodeSubmit', function() {
                handlePurchaseManualBarcode();
            });

            $('#purchase-qr-reader').off('keydown', '#purchaseManualBarcodeInput').on('keydown', '#purchaseManualBarcodeInput', function(e) {
                if (e.key === 'Enter') {
                    handlePurchaseManualBarcode();
                }
            });
        }

        async function handlePurchaseManualBarcode() {
            let barcode = $('#purchaseManualBarcodeInput').val().trim();

            if (!barcode) {
                $('#purchaseManualBarcodeError').text('Please enter a barcode.');
                return;
            }

            $('#purchaseManualBarcodeSubmit').prop('disabled', true).text('Searching...');
            $('#purchaseManualBarcodeError').text('');

            try {
                const product = await fetchPurchaseProductByBarcode(barcode);

                if (product) {
                    // keepModalOpen = true → modal stays open, input clears for next barcode
                    addProductToPurchaseRow(product, true);
                    $('#purchaseManualBarcodeInput').val('').focus();
                    $('#purchaseManualBarcodeError')
                        .css({ 'color': '#28a745', 'background': '#d4edda', 'padding': '6px 10px', 'border-radius': '4px', 'border': '1px solid #28a745' })
                        .html('&#10004; ' + product.name + ' added — enter next barcode');
                    setTimeout(() => $('#purchaseManualBarcodeError').text('').css({ 'background': '', 'padding': '', 'border-radius': '', 'border': '' }), 2500);
                } else {
                    $('#purchaseManualBarcodeError').css({ 'color': '#dc3545', 'background': '', 'padding': '', 'border-radius': '', 'border': '' }).text('No product found for: ' + barcode);
                }
            } catch (error) {
                $('#purchaseManualBarcodeError').css({ 'color': '#dc3545', 'background': '', 'padding': '', 'border-radius': '', 'border': '' }).text('Error: ' + (error || 'Product not found or server error.'));
            } finally {
                $('#purchaseManualBarcodeSubmit').prop('disabled', false).text('Search');
            }
        }

        function fetchPurchaseProductByBarcode(barcode) {
            return new Promise((resolve, reject) => {
                var token = localStorage.getItem("authToken");
                $.ajax({
                    url: '/api/product-by-barcode/' + encodeURIComponent(barcode),
                    type: 'GET',
                    headers: {
                        "Authorization": "Bearer " + token,
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        resolve(response.status && response.product ? response.product : null);
                    },
                    error: function(xhr) {
                        reject(xhr.responseJSON?.message || ('HTTP ' + xhr.status + ': ' + (xhr.statusText || 'Network error')));
                    }
                });
            });
        }

        function addProductToPurchaseRow(product, keepModalOpen) {
            const productId   = String(product.id || '').trim();
            const categoryId  = product.category_id || 0;

            const gstVal = typeof product.product_gst === 'object'
                ? JSON.stringify(product.product_gst)
                : (product.product_gst || '');

            // ✅ Check if this product already exists in any row — if so, increment qty
            let $existingRow = null;
            $('.purchase-product-row').each(function() {
                const rowProductVal = String($(this).find('.product-select').val() || '').trim();
                if (rowProductVal === productId) {
                    $existingRow = $(this);
                    return false; // break
                }
            });

            if ($existingRow) {
                // Product already in list → increment quantity by 1
                const $qtyInput = $existingRow.find('.quantity-input');
                const currentQty = parseFloat($qtyInput.val()) || 0;
                const newQty = currentQty + 1;
                $qtyInput.val(newQty).trigger('input');

                // Flash the row green
                $existingRow.css({
                    'background-color': '#d4edda',
                    'border': '2px solid #28a745',
                    'transition': 'all 0.3s ease'
                });
                setTimeout(() => {
                    $existingRow.css({ 'background-color': '', 'border': '' });
                }, 2000);

                $existingRow[0].scrollIntoView({ behavior: 'smooth', block: 'center' });

                if (keepModalOpen) {
                    $('#purchase-scan-message')
                        .css({
                            'color': '#28a745',
                            'font-weight': 'bold',
                            'background-color': '#d4edda',
                            'padding': '8px',
                            'border-radius': '4px',
                            'border': '1px solid #28a745'
                        })
                        .html('✔ ' + product.name + ' qty updated to ' + newQty + ' — scan next product');
                    setTimeout(() => {
                        $('#purchase-scan-message').text('').css({
                            'background-color': '', 'padding': '', 'border-radius': '', 'border': ''
                        });
                    }, 2000);
                }
                return; // stop here — no new row needed
            }

            // Check if the first row is empty (no product selected yet)
            let $targetRow = $('.purchase-product-row').first();
            let isFirstRowEmpty = $targetRow.find('.product-select').val() === '' || $targetRow.find('.product-select').val() === null;

            if (!isFirstRowEmpty) {
                // First row is filled, create a new row
                const categoryOptions = buildCategoryOptions();
                const rowHtml = `
                    <div class="row form-row purchase-product-row">
                        <div class="col-lg-2 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Category Name</label>
                                <select name="category_name[]" class="form-control category-select">
                                    ${categoryOptions}
                                </select>
                                <span class="error text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Product Name</label>
                                <select name="product_name[]" class="form-control select2 product-select" disabled>
                                    <option value="">Product Name</option>
                                </select>
                                <div class="product-gst-info mt-1"></div>
                                <span class="error text-danger product-error"></span>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Product Price</label>
                                <input type="text" inputmode="decimal" name="price[]" class="form-control price-input" placeholder="Enter Price" min="0" oninput="this.value = this.value < 0 ? 0 : this.value">
                                <span class="error text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="text" name="quantity[]" class="form-control quantity-input" placeholder="Qty" value="0" min="0" step="0.01" inputmode="decimal" oninput="this.value = this.value < 0 ? 0 : this.value">
                                <span class="error text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Disc %</label>
                                <input type="text" name="product_discount[]" class="form-control product-discount-input" placeholder="0.00" value="0" min="0" max="100" inputmode="decimal">
                                <span class="error text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Disc-Amt</label>
                                <input type="text" name="product_discount_amount[]" class="form-control product-discount-amount-input" placeholder="0.00" value="0" min="0">
                                <span class="error text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-6 col-6 serial-no-container">
                            <div class="form-group">
                                <div class="serial-no-btn-wrapper" style="display:none;">
                                    <label>Serial No <a href="javascript:void(0)" class="edit-serial-btn text-warning ml-1" title="Edit Serial Numbers"><i class="fas fa-edit"></i></a></label>
                                    <div class="serial-status text-muted" style="font-size: 11px; line-height: 1.2;">0/0 serial numbers added</div>
                                    <input type="hidden" class="serial-data-input" name="imei_no[]" value="[]">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Total Amount</label>
                                <input type="text" name="total[]" class="form-control total-input" placeholder="0" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-6 add-row-btn">
                            <button type="button" class="btn btn-danger remove-row">x</button>
                        </div>
                    </div>`;

                $targetRow = $(rowHtml).appendTo('#form-container');
                $targetRow.find('.category-select').each(function() {
                    $(this).select2({ tags: true, placeholder: "Category Name", dropdownParent: $(this).parent() });
                });
                $targetRow.find('.product-select').each(function() {
                    $(this).select2({ tags: true, placeholder: "Product Name", dropdownParent: $(this).parent() });
                });
            }

            // Populate the target row (first empty or newly created)
            const $categorySelect = $targetRow.find('.category-select');

            if (!$categorySelect.find(`option[value="${categoryId}"]`).length) {
                $categorySelect.append(new Option(product.category_name || 'Unknown', categoryId, false, false));
            }
            $categorySelect.val(categoryId).trigger('change');

            setTimeout(() => {
                const $productSelect = $targetRow.find('.product-select');

                if (!$productSelect.find(`option[value="${productId}"]`).length) {
                    $productSelect.append(
                        `<option value="${productId}"
                            data-price="${product.price}"
                            data-category="${categoryId}"
                            data-gst-option="${product.gst_option || 'without_gst'}"
                            data-gst='${gstVal}'>${product.name}</option>`
                    );
                }

                $productSelect.prop('disabled', false).val(productId).trigger('change');

                $targetRow.find('.price-input').val(product.price || 0).trigger('input');
                $targetRow.find('.quantity-input').val(1).trigger('input');

                if (typeof updatePurchaseSelectTitle === 'function') {
                    updatePurchaseSelectTitle($categorySelect);
                    updatePurchaseSelectTitle($productSelect);
                }
                $targetRow.css({
                    'background-color': '#d4edda',
                    'border': '2px solid #28a745',
                    'transition': 'all 0.3s ease'
                });

                setTimeout(() => {
                    $targetRow.css({
                        'background-color': '',
                        'border': ''
                    });
                }, 2000);

                $targetRow[0].scrollIntoView({ behavior: 'smooth', block: 'center' });

                if (!keepModalOpen) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Product Added',
                        text: product.name + ' added to purchase',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    // Show success in GREEN inside the modal
                    $('#purchase-scan-message')
                        .css({
                            'color': '#28a745',
                            'font-weight': 'bold',
                            'background-color': '#d4edda',
                            'padding': '8px',
                            'border-radius': '4px',
                            'border': '1px solid #28a745'
                        })
                        .html('✔ ' + product.name + ' added — scan next product');
                    setTimeout(() => {
                        $('#purchase-scan-message').text('').css({
                            'background-color': '',
                            'padding': '',
                            'border-radius': '',
                            'border': ''
                        });
                    }, 2000);
                }
            }, 300);
        }

        // Event listeners for barcode scanner button and product search
        $(document).ready(function() {
            const $purchaseSearchInput = $('#purchaseProductSearch');
            const $purchaseSearchResults = $('#purchaseProductSearchResults');

            $purchaseSearchInput.on('input', function() {
                const query = $(this).val().trim();

                if (query.length < 1) {
                    $purchaseSearchResults.hide().empty();
                    return;
                }

                $.ajax({
                    url: '/search-users',
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: { query: query },
                    dataType: 'json',
                    success: function(data) {
                        $purchaseSearchResults.empty();

                        if (!data.products || data.products.length === 0) {
                            $purchaseSearchResults
                                .html('<div class="list-group-item">No products found</div>')
                                .show();
                            return;
                        }

                        $purchaseSearchResults.append(
                            `<div class="list-group-item fw-bold d-flex justify-content-between align-items-center">
                                <span>Products</span>
                                <button type="button" class="btn-close close-purchase-search-result"></button>
                            </div>`
                        );

                        data.products.forEach(function(product) {
                            const gstLabel = product.gst_option === 'with_gst'
                                ? '<small style="color: green;">With GST</small>'
                                : '<small style="color: gray;">Without GST</small>';
                            const productGst = product.product_gst
                                ? String(product.product_gst).replace(/'/g, '&#039;')
                                : '';

                            $purchaseSearchResults.append(`
                                <div class="list-group-item d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img src="${product.image}" alt="Product" class="rounded me-2"
                                            style="width:30px; height:30px; object-fit: cover;">
                                        <div>
                                            <strong>${product.name ?? 'N/A'}</strong><br>
                                            <small>Price: ${product.price_formatted ?? product.price ?? 'N/A'}</small><br>
                                            ${gstLabel}
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary purchase-search-add-product"
                                        data-id="${product.id}"
                                        data-name="${product.name}"
                                        data-price="${product.price}"
                                        data-category="${product.category_id ?? 0}"
                                        data-gst-option="${product.gst_option || 'without_gst'}"
                                        data-product-gst='${productGst}'>+ Add</button>
                                </div>
                            `);
                        });

                        $purchaseSearchResults.show();
                    },
                    error: function() {
                        $purchaseSearchResults.hide().empty();
                    }
                });
            });

            $(document).on('click', '.close-purchase-search-result', function() {
                $purchaseSearchResults.hide().empty();
            });

            $(document).on('click', '.purchase-search-add-product', function() {
                const $btn = $(this);
                let productGst = $btn.data('product-gst') || null;

                if (productGst && typeof productGst === 'string' && productGst.startsWith('[')) {
                    try {
                        productGst = JSON.parse(productGst);
                    } catch (e) {
                        productGst = null;
                    }
                }

                const product = {
                    id: $btn.data('id'),
                    name: $btn.data('name'),
                    price: $btn.data('price'),
                    category_id: $btn.data('category'),
                    gst_option: $btn.data('gst-option') || 'without_gst',
                    product_gst: productGst
                };

                addProductToPurchaseRow(product, false);

                $purchaseSearchResults.hide().empty();
                $purchaseSearchInput.val('').focus();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.purchase-product-search-wrap').length) {
                    $purchaseSearchResults.hide();
                }
            });

            $('#purchaseScanBarcodeBtn').on('click', function() {
                $('#purchaseBarcodeScannerModal').modal('show');
                setTimeout(() => {
                    startPurchaseScanner();
                }, 500);
            });

            $('#purchaseBarcodeScannerModal').on('hidden.bs.modal', function() {
                stopPurchaseScanner();
            });
        });

        // IMEI / Serial Number Handling
        let currentSerialRow = null;

        function updateSerialUI(row) {
            let categorySelect = row.find('.category-select');
            if(!categorySelect.length) return;
            let categoryText = categorySelect.find('option:selected').text().trim().toLowerCase();
            let serialContainer = row.find('.serial-no-container');
            let serialBtnWrapper = serialContainer.find('.serial-no-btn-wrapper');
            let serialNa = serialContainer.find('.serial-no-na');
            let qty = parseFloat(row.find('.quantity-input').val()) || 0;
            let imeiInput = row.find('.serial-data-input');
            let productNameCol = row.find('.product-select').closest('div[class*="col-lg-"]');

            if (categoryText.includes('mobile')) {
                serialContainer.show();
                productNameCol.removeClass('col-lg-3').addClass('col-lg-2');
                serialBtnWrapper.show();
                if(serialNa.length) serialNa.hide();
                let existingSerials = [];
                try {
                    let val = imeiInput.val();
                    existingSerials = val ? JSON.parse(val) : [];
                } catch(e) {}
                let count = existingSerials.length;
                serialBtnWrapper.find('.serial-status').text(`${count}/${qty} serial numbers added`);
                if (count < qty && qty > 0) {
                    serialBtnWrapper.find('.serial-status').removeClass('text-success').addClass('text-danger');
                } else {
                    serialBtnWrapper.find('.serial-status').removeClass('text-danger').addClass('text-success');
                }
            } else {
                serialContainer.hide();
                productNameCol.removeClass('col-lg-2').addClass('col-lg-3');
                serialBtnWrapper.hide();
                if(serialNa.length) serialNa.show();
                imeiInput.val('[]');
            }
        }

        $(document).on('click', '.edit-serial-btn', function() {
            updateSerialUI($(this).closest('.purchase-product-row'));
            currentSerialRow = $(this).closest('.purchase-product-row');
            let qty = parseFloat(currentSerialRow.find('.quantity-input').val()) || 0;
            
            if (qty <= 0) {
                alert("Please enter a valid quantity first.");
                return;
            }

            let existingSerials = [];
            try {
                let val = currentSerialRow.find('.serial-data-input').val();
                existingSerials = val ? JSON.parse(val) : [];
            } catch(e) {}

            let modalBody = $('#serialNumberModalBody');
            modalBody.empty();

            for (let i = 0; i < qty; i++) {
                let val = existingSerials[i] || '';
                modalBody.append(`
                    <div class="form-group mb-2">
                        <label>IMEI No ${i + 1}</label>
                        <input type="text" class="form-control serial-input-item" value="${val}" placeholder="Enter IMEI / Serial No">
                    </div>
                `);
            }

            $('#serialNumberModal').modal('show');
        });

        $('#saveSerialNumbersBtn').on('click', function() {
            if (!currentSerialRow) return;
            let serials = [];
            $('#serialNumberModalBody .serial-input-item').each(function() {
                let val = $(this).val().trim();
                if (val !== '') {
                    serials.push(val);
                }
            });

            currentSerialRow.find('.serial-data-input').val(JSON.stringify(serials));
            updateSerialUI(currentSerialRow);
            $('#serialNumberModal').modal('hide');
        });

        $(document).on('change', '.category-select', function() {
            updateSerialUI($(this).closest('.purchase-product-row'));
        });

        $(document).on('input change', '.quantity-input', function() {
            updateSerialUI($(this).closest('.purchase-product-row'));
        });

        $(document).ready(function() {
            $('.purchase-product-row').each(function() {
                updateSerialUI($(this));
            });
        });
    </script>
@endpush
