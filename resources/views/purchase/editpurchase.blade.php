@extends('layout.app')

@section('title', 'Edit Purchase')

@section('content')
    <style>
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
                margin-bottom: 10px !important
            }
        }

        .gst-header {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            padding: 8px 0 0;
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

        .form-row {
            border: 1px solid #fdc794;
            padding: 20px 10px 10px 10px;
            margin-bottom: 20px;
            border-radius: 10px;
            background: #fdfdfd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            position: relative;
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

        .select2-container {
            width: 100% !important;
        }

        @media (min-width: 992px) {
            .purchase-meta-row.purchase-bottom-meta-row .purchase-remark-block {
                order: 4;
            }
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

            /* Row 1: Category, Product, Price (1 col each in 3-col grid) */
            /* Row 2: Qty, Disc%, Disc-Amt (1 col each in 3-col grid) */

            /* Span Total Amount to 2 columns on Row 3 */
            .form-row.purchase-product-row > div:nth-child(7) {
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

        /* Tablet responsive fix for the top meta row */
        @media (min-width: 767px) and (max-width: 1280px) {
            .purchase-meta-row {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                align-items: start;
                gap: 12px 14px;
                margin-left: 0 !important;
                margin-right: 0 !important;
                margin-bottom: 12px !important;
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

            /* Bottom meta row: Shipping, Payment Mode, Remark + dynamic payment fields */
            .purchase-meta-row.purchase-bottom-meta-row {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            /* Remark always spans full width on tablet */
            .purchase-meta-row.purchase-bottom-meta-row .remark-col {
                grid-column: 1 / -1;
            }
        }

        /* iPad Mini / smaller iPad tablets: keep product fields in two columns */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .purchase-meta-row .select2-container {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                display: block !important;
            }

            .purchase-meta-row .select2-selection--single {
                width: 100% !important;
            }

            .form-row.purchase-product-row {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px 14px;
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

            .form-row.purchase-product-row .select2-container,
            .form-row.purchase-product-row .select2-selection--single,
            .form-row.purchase-product-row .select2-selection__rendered {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
            }

            .form-row.purchase-product-row .add-row-btn {
                margin-top: 0;
                display: flex;
                /* align-items: end; */
                justify-content: center;
            }

            .form-row.purchase-product-row .add-row-btn .btn {
                width: 100%;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                  margin-top: 30px;

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

            .purchase-meta-row.purchase-bottom-meta-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
          </style>
    @php
        $user = Auth()->user();
        $branchId = $user->id ?? null;
        $userRole = $user->role ?? '';
        $subAdminId = session('selectedSubAdminId');
        if ($userRole === 'sub-admin') {
            $branchId = $user->id; // sub-admin uses own id
        } elseif ($userRole === 'admin' && !empty($subAdminId)) {
            $branchId = $subAdminId; // admin chooses sub-admin
        } elseif ($userRole === 'staff') {
            $branchId = $user->branch_id; // admin with no sub-admins
        } else {
            $branchId = $user->id; // for other roles, use own id
        }
        $vendors = App\Models\User::where('role', 'vendor')
            ->where('isDeleted', 0) // Only non-deleted vendors
            ->where('branch_id', $branchId) // Filter by branch_id
            ->get();
        $taxes = App\Models\TaxRate::where('status', 'active')
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->get();
        $products = App\Models\Product::where('isDeleted', 0)->where('branch_id', $branchId)->get();

        $settings = \DB::table('settings')->where('branch_id', $branchId)->first();
        $currencySymbol = $settings->currency_symbol ?? '₹';
        $currencyPosition = $settings->currency_position ?? 'left';
        $productsArray = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'price' => $product->price,
                'gst_option' => $product->gst_option,
                'product_gst' => $product->product_gst,
            ];
        });
        $productsJson = json_encode($productsArray);
    @endphp
    <div class="content">
        <div class="page-header purchase-header">
            <div class="page-title">
                <h4>Edit Purchase</h4>
            </div>
            <div class="gst-header">

                <label class="custom-radio-label">
                    <input type="radio" name="gst_option" id="without_gst" value="without" checked />
                    Without GST
                </label>

                <label class="custom-radio-label">
                    <input type="radio" name="gst_option" id="with_gst" value="with" />
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
                <div class="row purchase-meta-row">
                    <div class="col-lg-4 col-sm-4 col-6">
                        <div class="form-group">
                            <label>Vendor Name</label>
                            <select id="vendor_name" name="vendor_id" class="form-control select2 vendor-select" >
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
                    <div class="col-lg-4 col-sm-4 col-6">
                        <div class="form-group">
                            <label>Bill No</label>
                            <input type="text" id="bill_no" name="bill_no" class="form-control" placeholder="Bill No"
                            >
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label>Purchase Date</label>
                            <input type="text" id="purchase_date" name="purchase_date" class="form-control" placeholder="dd/mm/yyyy" autocomplete="off"
                            >
                            <span class="error text-danger"></span>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-12 d-none">
                        <div class="form-group">
                            <label>Vendor Phone</label>
                            <input type="number" id="vendor_phone" name="phone" class="form-control"
                                placeholder="Enter Phone" readonly>
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                </div>
                {{-- <div class="mt-2"> --}}
                <div class="row form-row purchase-product-row">
                    <div class="col-lg-2 col-sm-12 col-6">
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
                    <div class="col-lg-2 col-sm-12 col-6">
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

                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-12 col-6">
                        <div class="form-group">
                            <label>Product Price</label>
                            <input type="text" name="price[]" class="form-control price-input" placeholder="Enter Price">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-12 col-6">
                        <div class="form-group">
                            <label> Qty</label>
                            <input type="text" name="quantity[]" class="form-control quantity-input" placeholder="Qty"
                                value="1" min="0" step="0.01" inputmode="decimal"
                                oninput="this.value = this.value < 0 ? 0 : this.value">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-12 col-6">
                        <div class="form-group">
                            <label>Disc%</label>
                            <input type="text" name="product_discount[]" class="form-control product-discount-input"
                                placeholder="0.00" value="0" min="0" max="100"
                                oninput="this.value = this.value < 0 ? 0 : (this.value > 100 ? 100 : this.value)">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-12 col-6">
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
                                <div class="serial-status text-muted" style="font-size: 11px; line-height: 1.2;">0/0 IMEI numbers added</div>
                                <input type="hidden" class="serial-data-input" name="imei_no[]" value="{{ $detail->imei_no ?? '[]' }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-12 col-12">
                        <div class="form-group">
                            <label>Total Amt</label>
                            <input type="text" name="total[]" class="form-control total-input" placeholder="0"
                                min="0" readonly>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-12 add-row-btn">
                        <button type="button" class="btn btn-success add-row">+</button>
                    </div>
                </div>

                <!-- Placeholder for additional rows -->
                <div id="form-container"></div>
                {{-- </div> --}}

                <div class="row purchase-meta-row purchase-bottom-meta-row">
                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Shipping</label>
                            <input type="text" name="shipping" id="shipping" class="form-control"
                                placeholder="0.00">
                            <span class="error text-danger"></span>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-6 d-none">
                        <div class="form-group">
                            <label>Purchase Status</label>
                            <select name="status" class="form-control">
                                <option value="">Choose Status</option>
                                <option value="completed">Completed</option>
                                <option value="partially">Partially</option>
                                <option value="pending">Pending</option>
                            </select>
                            <span class="error text-danger"></span>
                        </div>
                    </div>



                    {{-- <div class="col-lg-3 col-sm-6 col-6 d-none">
                        <div class="form-group">
                            <label>Payment Status</label>
                            <select name="payment_status" class="form-control payment_status">
                                <option value="">Choose Status</option>
                                <option value="completed">Completed</option>
                                <option value="partially">Partially</option>
                                <option value="pending">Pending</option>
                            </select>
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                </div> --}}

                <!-- Payment Details Section - Always Visible -->
                {{-- <div class="row"> --}}
                    <div class="col-sm-6 col-lg-3 col-6">
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

                    <div class="col-md-3 col-sm-6 col-lg-2 col-6 d-none" id="paid_type_container">
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

                    <div class="col-md-3 col-sm-6 col-lg-2 col-6 d-none" id="cash_amount_input_container">
                        <div class="form-group">
                            <label>Cash Amount</label>
                            <input type="text" id="cash_amount_input" name="cash_amount" class="form-control"
                                placeholder="Enter Cash Amount">
                            <span class="text-danger error-cash-amount" id="cash_amount_error"></span>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-lg-2 col-6 d-none" id="amount_input_container">
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="text" id="amount_input" name="amount" class="form-control"
                                placeholder="Enter Amount">
                            <span class="text-danger error-cash-amount" id="amount_error"></span>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-lg-2 col-6 d-none" id="upi_amount_input_container">
                        <div class="form-group">
                            <label>Online Amount</label>
                            <input type="text" id="upi_amount_input" name="upi_amount" class="form-control"
                                placeholder="Enter Online Amount">
                            <span class="text-danger error-upi-amount" id="upi_amount_error"></span>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-lg-2 col-6 d-none" id="pending_amount_container">
                        <div class="form-group">
                            <label>Pending Amount</label>
                            <input type="text" id="pending_amount" name="pending_amount" class="form-control"
                                readonly>
                            <span id="pending_error" style="color:red; font-size:12px;"></span>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-lg-3 col-6 d-none" id="bank_container">
                        <div class="form-group">
                            <label>Select Bank <button type="button" class="btn btn-sm manage_btn ms-2"
                                id="addBankBtn" style="padding: 3px 0px; font-size: 12px;background-color: #ff9f43; color: #ffff">Add
                                Bank</button></label>
                            <select name="bank_id" id="bank_id" class="form-control select2">
                                <option value="">Select Bank</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error-bank_id"></div>
                        </div>
                    </div>
                {{-- </div> --}}
                </div>{{-- end purchase-bottom-meta-row --}}
                <div class="row">
                    <div class="col-lg-6 col-sm-12 col-12 remark-col purchase-remark-block">
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
                <div class="col-lg-12">
                    <a href="javascript:void(0);" class="btn btn-submit me-2">Submit</a>
                    <a href="{{ route('purchase.lists') }}" class="btn btn-cancel">Cancel</a>
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

    <!-- Add Bank Modal -->
    <div class="modal fade" id="addBankModal" tabindex="-1" aria-labelledby="addBankModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBankModalLabel">Add New Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <form id="addBankForm">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                                <input type="text" id="bank_name_input" name="bank_name" class="form-control" placeholder="Enter Bank Name">
                                <span class="text-danger error-text" id="bank_name_error"></span>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Account Number <span class="text-danger">*</span></label>
                                <input type="text" id="account_number_input" name="account_number" class="form-control" placeholder="Enter Account Number">
                                <span class="text-danger error-text" id="account_number_error"></span>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">IFSC Code <span class="text-danger">*</span></label>
                                <input type="text" id="ifsc_code_input" name="ifsc_code" class="form-control" placeholder="Enter IFSC Code">
                                <span class="text-danger error-text" id="ifsc_code_error"></span>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                                <input type="text" id="branch_name_input" name="branch_name" class="form-control" placeholder="Enter Branch Name">
                                <span class="text-danger error-text" id="branch_name_error"></span>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Opening Balance <span class="text-danger">*</span></label>
                                <input type="number" id="opening_balance_input" name="opening_balance" class="form-control" placeholder="0.00" step="0.01">
                                <span class="text-danger error-text" id="opening_balance_error"></span>
                            </div>
                            <div class="col-6 mb-3">
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
    <script>
        const products = @json($productsArray);
    </script>
    <script>
        function formatEditPurchaseDateForDisplay(value) {
            if (!value) return "";
            if (/^\d{2}\/\d{2}\/\d{4}$/.test(value)) return value;

            const datePart = String(value).split(" ")[0];
            if (/^\d{4}-\d{2}-\d{2}$/.test(datePart)) {
                const parts = datePart.split("-");
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            }

            const parsedDate = new Date(value);
            if (!isNaN(parsedDate.getTime())) {
                const year = parsedDate.getFullYear();
                const month = String(parsedDate.getMonth() + 1).padStart(2, "0");
                const day = String(parsedDate.getDate()).padStart(2, "0");
                return `${day}/${month}/${year}`;
            }

            return value;
        }

        function formatEditPurchaseDateForApi(value) {
            if (!value) return "";
            if (/^\d{4}-\d{2}-\d{2}$/.test(value)) return value;
            if (/^\d{2}\/\d{2}\/\d{4}$/.test(value)) {
                const parts = value.split("/");
                return `${parts[2]}-${parts[1]}-${parts[0]}`;
            }
            return value;
        }

        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            const $purchaseDateInput = $("#purchase_date");

            $purchaseDateInput.val(formatEditPurchaseDateForDisplay($purchaseDateInput.val()));
            $purchaseDateInput.datetimepicker({
                format: 'DD/MM/YYYY',
                useCurrent: false,
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

            // ✅ Global payment tracking variables
            let existingOrderTotalAmount = 0;
            let existingOrderPaidAmount = 0;

            // ✅ Extract invoice ID from URL
            let urlSegments = window.location.pathname.split("/");
            let invoiceId = urlSegments[urlSegments.length - 1];

            if (!isNaN(invoiceId) && invoiceId > 0) {
                loadPurchaseData(invoiceId);
            }

            // ===== NUMBER FORMATTING HELPERS =====
            function parseIndianNumber(value) {
                return parseFloat(String(value).replace(/,/g, '')) || 0;
            }

            function formatIndianNumber(num) {
                if (num === null || num === undefined || num === '') return '0.00';
                let number = typeof num === 'string' ? parseFloat(num.replace(/,/g, '')) : num;
                if (isNaN(number)) return '0.00';
                let [intPart, decPart] = number.toFixed(2).split('.');
                let lastThree = intPart.slice(-3);
                let rest = intPart.slice(0, -3);
                if (rest) {
                    lastThree = rest.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + lastThree;
                }
                return lastThree + '.' + decPart;
            }

            // Initialize Select2
            $("#bank_id, .payment-mode-select, .paid-type-container")
                .select2({});

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
            // Vendor select: search by name OR phone, display "Name - Phone", show only name when selected
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

            // ===== PAYMENT SECTION (same logic as Edit Sales) =====

            function getEditGrandTotal() {
                return parseIndianNumber($("#grand-total").text()) || 0;
            }

            function getEditPendingBaseAmount() {
                const recalculatedGrandTotal = getEditGrandTotal();
                return Math.max(recalculatedGrandTotal - existingOrderPaidAmount, 0);
            }

            function resetEditPaymentInputs() {
                $("#paid_type").val("");
                $("#bank_id").val("").trigger('change');
                $("#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount").val("");
                $("#amount_input").prop("readonly", false);
                $("#upi_amount_input").prop("readonly", false);
                $("#cash_amount_error, #upi_amount_error, #amount_error, #pending_error").text("");
            }

            function hideAllEditPaymentBlocks() {
                $("#paid_type_container, #amount_input_container, #cash_amount_input_container, #upi_amount_input_container, #pending_amount_container, #bank_container")
                    .addClass("d-none");
            }

            function canShowPaidTypeForPurchase(paymentMethod) {
                return ["cash", "online", "cashonline"].includes((paymentMethod || "").toLowerCase());
            }

            function syncEditPaymentMethodUI(paymentMethod) {
                hideAllEditPaymentBlocks();
                if (canShowPaidTypeForPurchase(paymentMethod)) {
                    $("#paid_type_container").removeClass("d-none");
                }
                if (paymentMethod === "online" || paymentMethod === "cashonline") {
                    $("#bank_container").removeClass("d-none");
                }
            }

            function updateSinglePendingAmount() {
                const total = getEditPendingBaseAmount();
                const paid = Math.max(parseIndianNumber($("#amount_input").val()) || 0, 0);
                $("#pending_amount").val(formatIndianNumber(Math.max(total - paid, 0)));
            }

            function updateCashOnlinePendingAmount() {
                const total = getEditPendingBaseAmount();
                const paidType = $("#paid_type").val();
                let cash = Math.max(parseIndianNumber($("#cash_amount_input").val()) || 0, 0);
                let online = Math.max(parseIndianNumber($("#upi_amount_input").val()) || 0, 0);

                if (paidType === "full") {
                    if (cash > total) {
                        cash = total;
                        $("#cash_amount_input").val(formatIndianNumber(cash));
                    }
                    online = Math.max(total - cash, 0);
                    $("#upi_amount_input").val(formatIndianNumber(online));
                    $("#upi_amount_input").prop("readonly", true);
                } else {
                    $("#upi_amount_input").prop("readonly", false);
                }

                const pending = Math.max(total - cash - online, 0);
                $("#pending_amount").val(formatIndianNumber(pending));

                if ((cash + online) > total) {
                    $("#pending_error").text("Cash + Online cannot exceed payable amount");
                } else {
                    $("#pending_error").text("");
                }
            }

            function togglePurchasePaidTypeFields() {
                const paymentMethod = $("#payment_mode").val() || "";
                const paidType = $("#paid_type").val();

                // Hide all input containers first
                $("#amount_input_container, #pending_amount_container, #cash_amount_input_container, #upi_amount_input_container")
                    .addClass("d-none");

                if (!canShowPaidTypeForPurchase(paymentMethod) || !paidType) {
                    return;
                }

                const total = getEditPendingBaseAmount();
                const isCashOnline = paymentMethod === "cashonline";

                if (isCashOnline) {
                    $("#cash_amount_input_container, #upi_amount_input_container, #pending_amount_container")
                        .removeClass("d-none");

                    if (paidType === "full") {
                        if (!(parseIndianNumber($("#cash_amount_input").val()) > 0)) {
                            $("#cash_amount_input").val(formatIndianNumber(total));
                        }
                    } else {
                        if (!(parseIndianNumber($("#cash_amount_input").val()) > 0)) {
                            $("#cash_amount_input, #upi_amount_input").val("");
                        }
                    }
                    updateCashOnlinePendingAmount();
                    return;
                }

                // cash or online (single amount)
                $("#amount_input_container, #pending_amount_container").removeClass("d-none");

                if (paidType === "full") {
                    $("#amount_input").val(formatIndianNumber(total)).prop("readonly", true);
                    updateSinglePendingAmount();
                } else {
                    if ($("#amount_input").prop("readonly")) {
                        $("#amount_input").val("");
                    }
                    $("#amount_input").prop("readonly", false);
                    updateSinglePendingAmount();
                }
            }

            // Payment Mode change handler
            $("#payment_mode").on("change", function() {
                const selectedMode = $(this).val();
                resetEditPaymentInputs();
                syncEditPaymentMethodUI(selectedMode);
                togglePurchasePaidTypeFields();
            });

            // Paid Type change handler
            $("#paid_type").on("change", function() {
                $("#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount").val("");
                $("#amount_input").prop("readonly", false);
                $("#upi_amount_input").prop("readonly", false);
                $("#cash_amount_error, #upi_amount_error, #amount_error, #pending_error").text("");
                togglePurchasePaidTypeFields();
            });

            // Amount input handler (cash or online single)
            $("#amount_input").on("input", function() {
                updateSinglePendingAmount();
                const total = getEditPendingBaseAmount();
                const paid = parseIndianNumber($(this).val());
                if (paid > total) {
                    $("#amount_error").text("Cannot enter more than payable amount");
                } else {
                    $("#amount_error").text("");
                }
            });

            // Cash + Bank amount input handlers
            $("#cash_amount_input, #upi_amount_input").on("input", function() {
                updateCashOnlinePendingAmount();
                const paidType = $("#paid_type").val();
                const total = getEditPendingBaseAmount();
                const cash = parseIndianNumber($("#cash_amount_input").val());
                const online = parseIndianNumber($("#upi_amount_input").val());

                if (paidType === "full" && Math.abs((cash + online) - total) > 0.01) {
                    const msg = `Cash + Online must equal payable amount (${formatIndianNumber(total)})`;
                    $("#cash_amount_error").text(msg);
                    $("#upi_amount_error").text(msg);
                } else {
                    $("#cash_amount_error").text("");
                    $("#upi_amount_error").text("");
                }
            });

            // Expose refresh function for calculateTotal
            window.refreshPurchasePaymentBlocks = function() {
                togglePurchasePaidTypeFields();
            };

            // ===== END PAYMENT SECTION =====

            // Add new form row dynamically
            $(document).on("click", ".add-row", function() {
                let row = `
                    <div class="row form-row purchase-product-row">
                        <div class="col-lg-2 col-sm-12 col-6">
                            <div class="form-group">
                                <label>Category Name</label>
                                <select name="category_name[]" class="form-control category-select">
                                    <option value="">Category Name</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" data-price="{{ $category->price }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="error text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-12 col-6">
                            <div class="form-group">
                                <label>Product Name</label>
                                <select name="product_name[]" class="form-control select2 product-select" disabled>
                                    <option value="">Product Name</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                            data-price="{{ $product->price }}"
                                            data-category="{{ $product->category_id }}"
                                            data-gst-option="{{ $product->gst_option }}"
                                            data-gst='{!! $product->product_gst !!}'>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="product-gst-info mt-1"></div>
                                <span class="error text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-12 col-6">
                            <div class="form-group">
                                <label>Product Price</label>
                                <input type="text" name="price[]" class="form-control price-input" placeholder="Enter Price">
                                <span class="error text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-12 col-6">
                            <div class="form-group">
                                <label>Qty</label>
                                <input type="text" name="quantity[]" class="form-control quantity-input" placeholder="Qty" value="1" min="0" step="0.01" inputmode="decimal" oninput="this.value = this.value < 0 ? 0 : this.value">
                                <span class="error text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-12 col-6">
                            <div class="form-group">
                                <label>Disc%</label>
                                <input type="text" name="product_discount[]" class="form-control product-discount-input" placeholder="0.00" value="0" min="0" max="100" oninput="this.value = this.value < 0 ? 0 : (this.value > 100 ? 100 : this.value)">
                                <span class="error text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-12 col-6">
                            <div class="form-group">
                                <label>Disc Amt</label>
                                <input type="text" name="product_discount_amount[]" class="form-control product-discount-amount-input" placeholder="0.00" value="0" min="0">
                                <span class="error text-danger"></span>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-6 col-6 serial-no-container">
                            <div class="form-group">
                                <div class="serial-no-btn-wrapper" style="display:none;">
                                    <label>IMEI No <a href="javascript:void(0)" class="edit-serial-btn text-warning ml-1" title="Edit Serial Numbers"><i class="fas fa-edit"></i></a></label>
                                    <div class="serial-status text-muted" style="font-size: 11px; line-height: 1.2;">0/0 IMEI numbers added</div>
                                    <input type="hidden" class="serial-data-input" name="imei_no[]" value="[]">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Total Amt</label>
                                <input type="text" name="total[]" class="form-control total-input" placeholder="0" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-12 add-row-btn">
                            <button type="button" class="btn btn-danger remove-row">x</button>
                        </div>
                    </div>`;

                let newRow = $(row).appendTo("#form-container");
                updateSerialUI(newRow);
                newRow.find('.category-select').each(function() {
                    $(this).select2({ tags: true, placeholder: "Category Name", dropdownParent: $(this).parent() });
                });
                newRow.find('.product-select').each(function() {
                    $(this).select2({ tags: true, placeholder: "Product Name", dropdownParent: $(this).parent() });
                });
                newRow[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            });

            // Remove form row
            $(document).on("click", ".remove-row", function() {
                $(this).closest(".form-row").remove();
                calculateTotal();
            });

            // Category change
            $(document).on('change', '.category-select', function() {
                var selectedCategory = $(this).val();
                var $row = $(this).closest('.form-row');
                var $productDropdown = $row.find('.product-select');

                $productDropdown.empty();

                if (selectedCategory) {
                    $productDropdown.prop('disabled', false);
                    $productDropdown.append('<option value="">Product Name</option>');
                    var filteredProducts = products.filter(p => p.category_id == selectedCategory);
                    filteredProducts.forEach(function(product) {
                        $productDropdown.append(
                            `<option value="${product.id}"
                                data-price="${product.price}"
                                data-gst-option="${product.gst_option}"
                                data-gst='${JSON.stringify(product.product_gst)}'>
                                ${product.name}
                            </option>`
                        );
                    });
                } else {
                    $productDropdown.prop('disabled', true);
                    $productDropdown.append('<option value="">Product Name</option>');
                }
                $productDropdown.select2('destroy').select2();
            });

            // Product change
            $(document).on('change', '.product-select', function() {
                let selectedOption = $(this).find("option:selected");
                let price = selectedOption.data("price") || 0;
                let $row = $(this).closest('.form-row');
                $row.find(".price-input").val(parseFloat(price)).trigger("input");
                updateProductGstInfo($row);
            });

            // GST option change
            $(document).on('change', 'input[name="gst_option"]', function() {
                $(".form-row").each(function() {
                    let row = $(this);
                    updateProductGstInfo(row);
                    row.find(".product-discount-input").trigger("input");
                    row.find(".product-discount-amount-input").trigger("input");
                });
                calculateTotal();
            });

            function getRowAmountForDiscount(row) {
                let price = parseIndianNumber(row.find(".price-input").val()) || 0;
                let quantity = parseFloat(row.find(".quantity-input").val()) || 0;
                return price * quantity;
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
                                if (typeof taxes === "string") {
                                    if (taxes === "undefined" || taxes === "") taxes = "[]";
                                    taxes = JSON.parse(taxes);
                                }
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                if (Array.isArray(taxes)) {
                                    taxes.forEach(tax => {
                                        let taxRate = parseFloat(tax.tax_rate || tax.rate) || 0;
                                        productGstAmount += (amountForDiscount * taxRate) / 100;
                                    });
                                }
                            } catch(e) {}
                        }
                    }
                }

                let inclusiveTotal = amountForDiscount + productGstAmount;

                let discountPercent = Math.min(100, Math.max(0, parseFloat(row.find(".product-discount-input").val()) || 0));
                let discountAmount = Math.max(0, parseIndianNumber(row.find(".product-discount-amount-input").val()) || 0);

                if (inclusiveTotal <= 0) {
                    return { amountForDiscount: 0, discountPercent, discountAmount, finalRowTotal: 0 };
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
                    row.find(".product-discount-amount-input").val(formatIndianNumber(result.discountAmount));
                }

                row.find(".total-input").val(formatIndianNumber(result.finalRowTotal));
                return result;
            }

            function updateProductGstInfo($row) {
                let selectedOption = $row.find(".product-select option:selected");
                let gstOption = selectedOption.data("gst-option");
                let gstData = selectedOption.data("gst");

                let price = parseIndianNumber($row.find(".price-input").val()) || 0;
                let quantity = parseFloat($row.find(".quantity-input").val()) || 0;
                let taxableAmount = price * quantity;

                let $gstContainer = $row.find(".product-gst-info");
                $gstContainer.empty();

                if (!$('#with_gst').is(':checked')) return;

                if (gstOption === 'with_gst' && gstData) {
                    try {
                        let taxes = gstData;
                        if (typeof taxes === "string") {
                            if (taxes === "undefined" || taxes === "") taxes = "[]";
                            taxes = JSON.parse(taxes);
                        }
                        if (typeof taxes === "string") taxes = JSON.parse(taxes);

                        if (Array.isArray(taxes)) {
                            let totalTaxAmount = 0;
                            let taxDetails = taxes.map(tax => {
                                let taxName = tax.tax_name || tax.name;
                                let taxRate = parseFloat(tax.tax_rate || tax.rate) || 0;
                                let taxAmount = (taxableAmount * taxRate) / 100;
                                totalTaxAmount += taxAmount;
                                return `${taxName}: ${taxRate}%`;
                            }).join(', ');

                            let gstHtml = `<div style="font-size: 11px; color: #666; background: #f8f9fa; padding: 5px; border-radius: 4px; border-left: 3px solid #1b2850; margin-top: 5px;">
                                <div><strong>Total GST: {{ $currencySymbol }}${formatIndianNumber(totalTaxAmount)}</strong></div>
                                <div style="font-size: 10px;">(${taxDetails})</div>
                            </div>`;
                            $gstContainer.html(gstHtml);
                        }
                    } catch (e) {}
                } else if (gstOption === 'without_gst') {
                    $gstContainer.html('<small class="text-muted" style="font-size: 11px;">No GST for this product</small>');
                }
            }

            $(document).on("input", ".price-input, .quantity-input, .product-discount-input", function() {
                let row = $(this).closest(".form-row");
                let source = $(this).hasClass("product-discount-input") ? 'percent' : (row.data('discount-source') || 'percent');
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
                let val = parseFloat(this.value);
                if (!isNaN(val)) {
                    this.value = Math.min(100, Math.max(0, val)).toFixed(2);
                }
                let row = $(this).closest(".form-row");
                applyRowDiscount(row, 'percent');
                calculateTotal();
            });

            function calculateTotal() {
                let totalAmount = 0;
                let taxTotals = {};
                let totalTaxAmount = 0;
                let products = [];
                let totalDiscountAmount = 0;

                $(".form-row").each(function() {
                    let productOption = $(this).find(".product-select option:selected");
                    let productId = productOption.val();
                    let categoryId = $(this).find(".category-select").val();
                    let price = parseIndianNumber($(this).find(".price-input").val()) || 0;
                    let quantity = parseFloat($(this).find(".quantity-input").val()) || 0;

                    let discountSource = $(this).data('discount-source') || 'percent';
                    let rowDiscount = getRowDiscount($(this), discountSource);
                    let discountPercent = rowDiscount.discountPercent;
                    let discountAmount = rowDiscount.discountAmount;
                    let finalRowTotal = rowDiscount.finalRowTotal;
                    let subTotal = rowDiscount.amountForDiscount;

                    let rowGstAmount = 0;

                    let productGstOption = productOption.data("gst-option");

                    if ($('#with_gst').is(':checked') && productGstOption === 'with_gst') {
                        let gstData = productOption.data("gst");
                        if (gstData) {
                            try {
                                let taxes = gstData;
                                if (typeof taxes === "string") {
                                    if (taxes === "undefined" || taxes === "") taxes = "[]";
                                    taxes = JSON.parse(taxes);
                                }
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                if (Array.isArray(taxes)) {
                                    taxes.forEach(tax => {
                                        let taxName = tax.tax_name || tax.name;
                                        let taxRate = parseFloat(tax.tax_rate || tax.rate) || 0;
                                        let taxAmount = (subTotal * taxRate) / 100;
                                        rowGstAmount += taxAmount;
                                        totalTaxAmount += taxAmount;
                                        if (!taxTotals[taxName]) {
                                            taxTotals[taxName] = { id: tax.tax_id || tax.id, rate: taxRate, amount: 0 };
                                        }
                                        taxTotals[taxName].amount += taxAmount;
                                    });
                                }
                            } catch (e) {}
                        }
                    }

                    totalDiscountAmount += discountAmount;

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
                            discount_amount: discountAmount,
                            total: finalRowTotal,
                            imei_no: JSON.stringify(imeiNo)
                        });
                    }

                    totalAmount += subTotal;
                    $(this).find(".total-input").val(formatIndianNumber(finalRowTotal));
                });

                $("#total-product-amount").text(formatIndianNumber(totalAmount));

                let shipping = parseIndianNumber($("#shipping").val()) || 0;

                if ($('#with_gst').is(':checked')) {
                    $('#gst-section').show();
                    $("#total-gst-amount").text(formatIndianNumber(totalTaxAmount));
                } else {
                    $('#gst-section').hide();
                    $("#total-gst-amount").text('0.00');
                }

                let priceAfterDiscount = (totalAmount + totalTaxAmount) - totalDiscountAmount;
                let grandTotal = priceAfterDiscount + shipping;
                let roundedGrandTotal = Math.round(grandTotal);
                let roundOff = roundedGrandTotal - grandTotal;

                $('#round-off-section').show();
                $("#round-off-amount").text(formatIndianNumber(roundOff));
                $("#grand-total").text(formatIndianNumber(roundedGrandTotal));
                $("#shipping-amount").text(formatIndianNumber(shipping));
                $("#total-discount-amount").text(formatIndianNumber(totalDiscountAmount));
                $("#price-after-discount").text(formatIndianNumber(priceAfterDiscount));

                if (shipping > 0) {
                    $('#shipping-section').show();
                } else {
                    $('#shipping-section').hide();
                }

                if (totalDiscountAmount == 0) {
                    $('#discount-amount-section').hide();
                    $('#price-after-discount-section').hide();
                } else {
                    $('#discount-amount-section').show();
                    $('#price-after-discount-section').show();
                }

                // ✅ Refresh payment blocks using new logic
                if (typeof window.refreshPurchasePaymentBlocks === 'function') {
                    window.refreshPurchasePaymentBlocks();
                }

                return {
                    products: products,
                    totalProductAmount: totalAmount,
                    totalAmount: totalAmount,
                    taxAmount: totalTaxAmount,
                    taxTotals: taxTotals,
                    shipping: shipping,
                    grandTotal: grandTotal,
                    grandTotalExact: grandTotal,
                    gstOption: $('#with_gst').is(':checked') ? 'with' : 'without'
                };
            }

            // Format focus/blur handlers
            $(document).on("focus", ".price-input", function() {
                let val = parseIndianNumber($(this).val());
                $(this).val(val > 0 ? val : "");
            });

            $(document).on("blur", ".price-input", function() {
                let val = parseIndianNumber($(this).val());
                $(this).val(formatIndianNumber(val));
                calculateTotal();
            });

            $(document).on("focus", ".product-discount-amount-input", function() {
                let val = parseIndianNumber($(this).val());
                $(this).val(val > 0 ? val : "");
            });

            $(document).on("blur", ".product-discount-amount-input", function() {
                let val = parseIndianNumber($(this).val());
                $(this).val(formatIndianNumber(val));
                calculateTotal();
            });

            $(document).on("focus", "#shipping", function() {
                let val = parseIndianNumber($(this).val());
                $(this).val(val > 0 ? val : "");
            });

            $(document).on("blur", "#shipping", function() {
                let val = parseIndianNumber($(this).val());
                $(this).val(formatIndianNumber(val));
                calculateTotal();
            });

            $(document).on("input", "#shipping", function() {
                calculateTotal();
            });

            // ===== FORM VALIDATION =====
            function validateForm() {
                let isValid = true;
                $(".error").text("");

                if ($("#vendor_name").val() === "") {
                    $("#vendor_name").closest(".form-group").find(".error").text("Vendor is required.");
                    isValid = false;
                }
                if ($("#bill_no").val() === "") {
                    $("#bill_no").closest(".form-group").find(".error").text("Bill Number is required.");
                    isValid = false;
                }

                $(".form-row").each(function() {
                    let productSelect = $(this).find(".product-select");
                    let categorySelect = $(this).find(".category-select");
                    let priceInput = $(this).find(".price-input");
                    let quantityInput = $(this).find(".quantity-input");
                    let categoryVal = categorySelect.val();

                    if (categoryVal !== "") {
                        if (productSelect.val() === "") {
                            productSelect.closest(".form-group").find(".error").text("Product is required.");
                            isValid = false;
                        }
                        if (priceInput.val().trim() === "" || parseIndianNumber(priceInput.val()) <= 0) {
                            priceInput.closest(".form-group").find(".error").text("Valid price is required.");
                            isValid = false;
                        }
                        if (quantityInput.val().trim() === "" || parseFloat(quantityInput.val()) <= 0) {
                            quantityInput.closest(".form-group").find(".error").text("Valid quantity is required.");
                            isValid = false;
                        }
                    }
                });

                let shipping = parseIndianNumber($("#shipping").val()) || 0;
                if (shipping < 0) {
                    $("#shipping").closest(".form-group").find(".error").text("Shipping cost cannot be negative.");
                    isValid = false;
                }

                return isValid;
            }

            function validatePaymentSection() {
                $(".error-payment_mode, .error-paid_type").text("");
                $("#amount_error, #cash_amount_error, #upi_amount_error, #pending_error").text("");

                const paymentMode = $("#payment_mode").val();
                const paidType = $("#paid_type").val();
                // ✅ Use remaining payable (grand total - already paid)
                const grandTotal = getEditPendingBaseAmount();
                const amount = parseIndianNumber($("#amount_input").val());
                const cash = parseIndianNumber($("#cash_amount_input").val());
                const online = parseIndianNumber($("#upi_amount_input").val());
                const totalPaid = cash + online;

                if (paymentMode === "pending") return true;

                if (!paidType) {
                    $(".error-paid_type").text("Paid type is required");
                    return false;
                }

                if (paidType === "partial") {
                    if (paymentMode === "cash" || paymentMode === "online") {
                        if (amount <= 0) {
                            $("#amount_error").text("Amount is required");
                            return false;
                        }
                        if (amount >= grandTotal) {
                            $("#amount_error").text("Amount must be less than payable total");
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
                            $("#pending_error").text("Cash + Online must be less than payable total");
                            return false;
                        }
                    }
                }

                if (paidType === "full" && paymentMode === "cashonline") {
                    if (Math.abs(totalPaid - grandTotal) > 0.01) {
                        $("#pending_error").text("Cash + Online must equal payable total");
                        return false;
                    }
                }

                if ((paymentMode === "online" || paymentMode === "cashonline") && !$("#bank_id").val()) {
                    $(".error-bank_id").text("Please select a bank");
                    return false;
                }

                return true;
            }

            // ===== LOAD PURCHASE DATA =====
            function loadPurchaseData(invoiceId) {
                var authToken = localStorage.getItem("authToken");
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                $.ajax({
                    url: "/api/purchase_get/" + invoiceId,
                    type: "GET",
                    data: { selectedSubAdminId: selectedSubAdminId },
                    dataType: "json",
                    headers: { "Authorization": "Bearer " + authToken },
                    success: function(response) {
                        if (response.success) {
                            let taxes = response.taxes;
                            let data = response.data;
                            let invoice = response.invoice;
                            let paymentDetails = response.payment_details;

                            // ✅ Set global payment tracking variables
                            let originalTotalPaid = parseFloat(data.total_paid) || 0;
                            let originalRemainingAmount = parseFloat(data.remaining_amount) || 0;
                            existingOrderTotalAmount = parseFloat(data.grand_total) || 0;
                            existingOrderPaidAmount = originalTotalPaid;

                            $("#vendor_phone").val(data.vendor_phone);
                            $("#bill_no").val(invoice.bill_no);
                            $("#shipping").val(formatIndianNumber(data.shipping));
                            $("#shipping-amount").text(formatIndianNumber(data.shipping));
                            $("select[name='status']").val(data.status);
                            $("#purchase_date").val(formatEditPurchaseDateForDisplay(invoice.purchase_date || data.purchase_date));
                            $("#remark").val(invoice.remark ?? data.remark ?? "");
                            $("#total-product-amount").text(formatIndianNumber(data.total_amount));
                            $("#vendor_name").val(data.vendor_id).trigger("change");
                            $("#vendor_phone").val(data.vendor_phone);
                            $("#grand-total").text(parseFloat(data.grand_total).toFixed(2));

                            // ✅ Set pending amount from backend remaining_amount
                            $("#pending_amount").val(formatIndianNumber(originalRemainingAmount));

                          // ✅ Set payment details
if (paymentDetails) {
    let paymentMode = 'pending';
    if (paymentDetails.payment_method === 'Cash') {
        paymentMode = 'cash';
    } else if (paymentDetails.payment_method === 'Online') {
        paymentMode = 'online';
    } else if (paymentDetails.cash_amount > 0 && paymentDetails.upi_amount > 0) {
        paymentMode = 'cashonline';
    } else if (paymentDetails.cash_amount > 0) {
        paymentMode = 'cash';
    } else if (paymentDetails.upi_amount > 0) {
        paymentMode = 'online';
    }

    // ✅ Set value without triggering change event (avoids reset)
    $("#payment_mode").val(paymentMode);
    if ($("#payment_mode").hasClass("select2-hidden-accessible")) {
        $("#payment_mode").trigger("change.select2");
    }
    syncEditPaymentMethodUI(paymentMode);
    $("#bank_id").val(paymentDetails.bank_id).trigger("change");

} else {
    $("#payment_mode").val('pending');
    if ($("#payment_mode").hasClass("select2-hidden-accessible")) {
        $("#payment_mode").trigger("change.select2");
    }
    syncEditPaymentMethodUI('pending');
}

                            // ✅ Set GST option
                            if (data.gst_option) {
                                let val = (data.gst_option === 'with_gst' || data.gst_option === 'with') ? 'with' : 'without';
                                $("input[name='gst_option'][value='" + val + "']")
                                    .prop("checked", true)
                                    .trigger("change");
                            }

                            $("#total-product-amount").text(parseFloat(data.total_amount).toFixed(2));

                            if (data.gst_option === "with_gst") {
                                $(".tax-section").show();
                                if (taxes && taxes.length) {
                                    taxes.forEach(function(tax) {
                                        let $el = $("#tax-" + tax.id);
                                        if ($el.length) {
                                            $el.text(parseFloat(tax.amount).toFixed(2));
                                            $el.attr("data-rate", tax.rate);
                                        }
                                    });
                                }
                            } else {
                                $(".tax-section").hide();
                            }

                            // ✅ Build product rows
                            let productIds = data.product_ids ? data.product_ids.split(", ") : [];
                            let productNames = data.product_names ? data.product_names.split(", ") : [];
                            let productPrices = data.product_prices ? data.product_prices.split(", ") : [];
                            let productQuantities = data.product_quantities ? data.product_quantities.split(", ") : [];
                            let discountPercents = data.discount_percents ? data.discount_percents.split(", ") : [];
                            let discountAmounts = data.discount_amounts ? data.discount_amounts.split(", ") : [];
                            let categoryIds = data.category_ids ? data.category_ids.split(", ") : [];
                            let categoryNames = data.category_names ? data.category_names.split(", ") : [];
                            let productGstDetails = data.product_gst_details ? data.product_gst_details.split("|||") : [];
                            let productImeiNos = data.product_imei_nos ? data.product_imei_nos.split("|||") : [];

                            $(".form-row").remove();
                            $("#form-container").empty();

                            for (let i = 0; i < productIds.length; i++) {
                                let productObj = products.find(p => p.id == productIds[i]);
                                let gstOption = productObj ? productObj.gst_option : "";
                                let discountPercent = discountPercents[i] || 0;
                                let discountAmount = discountAmounts[i] || 0;
                                let imeiNos = productImeiNos[i] && productImeiNos[i] !== "null" ? productImeiNos[i] : "[]";


                                let gstData = "[]";
                                let itemGstTotal = 0;
                                if (productGstDetails[i] && productGstDetails[i] !== "null" && productGstDetails[i] !== "[]") {
                                    gstData = productGstDetails[i];
                                    try {
                                        let parsedGst = JSON.parse(gstData);
                                        if (Array.isArray(parsedGst)) {
                                            let subTotal = parseFloat(productPrices[i]) * parseFloat(productQuantities[i]);
                                            parsedGst.forEach(tg => {
                                                itemGstTotal += (subTotal * parseFloat(tg.tax_rate || tg.rate || 0)) / 100;
                                            });
                                        }
                                    } catch (e) {}
                                } else if (productObj && productObj.product_gst) {
                                    gstData = typeof productObj.product_gst === 'string' ? productObj.product_gst : JSON.stringify(productObj.product_gst);
                                    try {
                                        let parsedGst = JSON.parse(gstData);
                                        if (Array.isArray(parsedGst)) {
                                            let subTotal = parseFloat(productPrices[i]) * parseFloat(productQuantities[i]);
                                            parsedGst.forEach(tg => {
                                                itemGstTotal += (subTotal * parseFloat(tg.tax_rate || tg.rate || 0)) / 100;
                                            });
                                        }
                                    } catch (e) {}
                                }

                                let categoryProducts = products.filter(p => p.category_id == categoryIds[i]);
                                let productOptions = `<option value="">Product Name</option>`;

                                if (!categoryProducts.find(p => p.id == productIds[i])) {
                                    productOptions += `<option value="${productIds[i]}" selected data-price="${productPrices[i]}" data-gst-option="${gstOption}" data-gst='${gstData}'>${productNames[i]}</option>`;
                                }

                                categoryProducts.forEach(p => {
                                    let selected = p.id == productIds[i] ? 'selected' : '';
                                    let pGst = typeof p.product_gst === 'string' ? p.product_gst : JSON.stringify(p.product_gst);
                                    productOptions += `<option value="${p.id}" ${selected} data-price="${p.price}" data-gst-option="${p.gst_option}" data-gst='${pGst}'>${p.name}</option>`;
                                });

                                let buttonHtml = i === 0 ?
                                    `<button type="button" class="btn btn-success add-row">+</button>` :
                                    `<button type="button" class="btn btn-danger remove-row">x</button>`;

                                let subTotal = parseFloat(productPrices[i]) * parseFloat(productQuantities[i]);
                                let rowTotalWithGst = subTotal + itemGstTotal;
                                let discountAmt = (rowTotalWithGst * parseFloat(discountPercent)) / 100;
                                let finalRowTotal = rowTotalWithGst - discountAmt;

                                let rowHtml = `
                                    <div class="row form-row purchase-product-row">
                                        <div class="col-lg-2 col-sm-12 col-6">
                                            <div class="form-group">
                                                <label>Category Name</label>
                                                <select name="category_name[]" class="form-control select2 category-select">
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
                                        <div class="col-lg-2 col-sm-12 col-6">
                                            <div class="form-group">
                                                <label>Product Name</label>
                                                <select name="product_name[]" class="form-control select2 product-select">
                                                    ${productOptions}
                                                </select>
                                                <div class="product-gst-info mt-1"></div>
                                                <span class="error text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-sm-12 col-6">
                                            <div class="form-group">
                                                <label>Product Price</label>
                                                <input type="text" name="price[]" class="form-control price-input"
                                                    value="${formatIndianNumber(productPrices[i])}">
                                                <span class="error text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-1 col-sm-12 col-6">
                                            <div class="form-group">
                                                <label>Quantity</label>
                                                <input type="text" name="quantity[]" class="form-control quantity-input"
                                                    value="${productQuantities[i]}" min="0" step="0.01" inputmode="decimal" oninput="this.value = this.value < 0 ? 0 : this.value">
                                                <span class="error text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-1 col-sm-12 col-6">
                                            <div class="form-group">
                                                <label>Disc%</label>
                                                <input type="text" name="product_discount[]" class="form-control product-discount-input"
                                                    value="${discountPercent}" min="0" max="100" oninput="this.value = this.value < 0 ? 0 : (this.value > 100 ? 100 : this.value)">
                                                <span class="error text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-1 col-sm-12 col-6">
                                            <div class="form-group">
                                                <label>Disc-Amt</label>
                                                <input type="text" name="product_discount_amount[]" class="form-control product-discount-amount-input"
                                                    value="${formatIndianNumber(discountAmount)}">
                                                <span class="error text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-1 col-sm-6 col-6 serial-no-container">
                                            <div class="form-group">
                                                <div class="serial-no-btn-wrapper" style="display:none;">
                                                    <label>IMEI No <a href="javascript:void(0)" class="edit-serial-btn text-warning ml-1" title="Edit Serial Numbers"><i class="fas fa-edit"></i></a></label>
                                                    <div class="serial-status text-muted" style="font-size: 11px; line-height: 1.2;">0/0 IMEI numbers added</div>
                                                    <input type="hidden" class="serial-data-input" name="imei_no[]" value='${imeiNos}'>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-1 col-sm-12 col-12">
                                            <div class="form-group">
                                                <label>Total Amt</label>
                                                <input type="text" name="total[]" class="form-control total-input"
                                                    value="${formatIndianNumber(finalRowTotal)}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-1 col-sm-12 add-row-btn">
                                            ${buttonHtml}
                                        </div>
                                    </div>`;

                                let $newRow = $(rowHtml).appendTo("#form-container");
                                $newRow.find(".category-select").val(categoryIds[i]);
                                $newRow.find('.category-select').each(function() {
                                    $(this).select2({ tags: true, placeholder: "Category Name", dropdownParent: $(this).parent() });
                                });
                                $newRow.find('.product-select').each(function() {
                                    $(this).select2({ tags: true, placeholder: "Product Name", dropdownParent: $(this).parent() });
                                });
                                updateSerialUI($newRow);
                            }

                            $(".form-row").each(function() {
                                updateProductGstInfo($(this));
                            });

                            calculateTotal();

                            // ✅ After calculateTotal, restore payment fields with correct pending amount
                            setTimeout(function() {
                                if (paymentDetails) {
                                    let paymentMode = $("#payment_mode").val();
                                    syncEditPaymentMethodUI(paymentMode);

                                    if (paymentDetails.payment_type) {
                                        $("#paid_type").val(paymentDetails.payment_type);
// ✅ Manually trigger Select2 visual update for paid_type
            if ($("#paid_type").hasClass("select2-hidden-accessible")) {
                $("#paid_type").trigger("change.select2");
            }
                                        setTimeout(function() {
                                            togglePurchasePaidTypeFields();

                                            // ✅ Restore partial amounts (full amounts are auto-filled)
                                            if (paymentDetails.payment_type === "partial") {
                                                if (paymentMode === "cashonline") {
                                                    $("#cash_amount_input").val(formatIndianNumber(paymentDetails.cash_amount || 0));
                                                    $("#upi_amount_input").val(formatIndianNumber(paymentDetails.upi_amount || 0));
                                                    updateCashOnlinePendingAmount();
                                                } else {
                                                    $("#amount_input").val(formatIndianNumber(paymentDetails.payment_amount || 0));
                                                    updateSinglePendingAmount();
                                                }
                                            }
                                        }, 100);
                                    }
                                }
                            }, 200);

                        } else {
                            Swal.fire("Error!", "Purchase not found!", "error");
                        }
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", "Purchase not found!", "error");
                    }
                });
            }

            // ===== SUBMIT HANDLER =====
            $(document).on("click", ".btn-submit", function(e) {
                e.preventDefault();

                const $btn = $(this);
                const originalText = $btn.html();
                $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...')
                    .prop('disabled', true);

                if (!validateForm()) {
                    $btn.html(originalText).prop('disabled', false);
                    return;
                }

                if (!validatePaymentSection()) {
                    $btn.html(originalText).prop('disabled', false);
                    return;
                }

                const billNo = $("#bill_no").val().trim();
                const currentInvoiceId = invoiceId;
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                if (billNo) {
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
                            invoice_id: currentInvoiceId,
                            selectedSubAdminId: selectedSubAdminId
                        }),
                        success: function(response) {
                            if (!response.isUnique) {
    $btn.html(originalText).prop('disabled', false);
    $("#bill_no").closest(".form-group").find(".error").text("Bill Number already exists.");
    $("#bill_no")[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
}
                            submitPurchaseForm($btn, originalText, invoiceId, authToken, selectedSubAdminId);
                        },
                        error: function(xhr) {
                            $btn.html(originalText).prop('disabled', false);
                            Swal.fire({
                                title: "Error!",
                                text: "Error validating bill number. Please try again.",
                                icon: "error",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43",
                                customClass: {
        icon: 'swal-icon-small'
    }
                            });
                        }
                    });
                } else {
                    $btn.html(originalText).prop('disabled', false);
                }
            });

            function submitPurchaseForm($btn, originalText, invoiceId, authToken, selectedSubAdminId) {
                let formData = calculateTotal();
                formData.vendor_id = $("#vendor_name").val();
                formData.vendor_phone = $("#vendor_phone").val();
                formData.status = $("select[name='status']").val();
                formData.payment_status = $("select[name='payment_status']").val();
                formData.bill_no = $("#bill_no").val();
                formData.remark = $("#remark").val().trim();
                formData.purchase_status = $("select[name='status']").val();
                formData.purchase_date = formatEditPurchaseDateForApi($("#purchase_date").val());
                formData.payment_mode = $("#payment_mode").val();
                formData.paid_type = $("#paid_type").val();
                formData.bank_id = $("#bank_id").val();
                formData.cash_amount = parseIndianNumber($("#cash_amount_input").val());
                formData.upi_amount = parseIndianNumber($("#upi_amount_input").val());
                formData.amount = parseIndianNumber($("#amount_input").val());
                formData.remaining_amount = parseIndianNumber($("#pending_amount").val());

                let taxArray = [];
                for (let taxName in formData.taxTotals) {
                    let taxData = formData.taxTotals[taxName];
                    taxArray.push({
                        name: taxName,
                        id: taxData.id,
                        rate: taxData.rate,
                        amount: taxData.amount,
                    });
                }
                formData.taxes = taxArray;

                $.ajax({
                    url: "/api/purchase_update/" + invoiceId,
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
                        payment_status: formData.payment_status,
                        purchase_status: formData.purchase_status,
                        discount: formData.discount,
                        shipping: formData.shipping,
                        bill_no: formData.bill_no,
                        grand_total: formData.grandTotalExact,
                        taxes: formData.taxes,
                        purchase_date: formData.purchase_date,
                        remark: formData.remark,
                        products: formData.products,
                        gst_option: formData.gstOption,
                        selectedSubAdminId: selectedSubAdminId,
                        payment_mode: formData.payment_mode,
                        paid_type: formData.paid_type,
                        bank_id: formData.bank_id,
                        cash_amount: formData.cash_amount,
                        upi_amount: formData.upi_amount,
                        amount: formData.amount,
                        remaining_amount: formData.remaining_amount
                    }),
                    success: function(response) {
                        $btn.html(originalText).prop('disabled', false);
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: "Purchase updated successfully!",
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43",
                                customClass: {
        icon: 'swal-icon-small'
    }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('purchase.lists') }}";
                                }
                            });
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
               error: function(xhr, status, error) {
    $btn.html(originalText).prop('disabled', false);

    if (xhr.status === 422) {
        let response = xhr.responseJSON;
        if (response && response.errors) {
            // Show backend validation errors in their span tags
            if (response.errors.bill_no) {
                $("#bill_no").closest(".form-group").find(".error")
                    .text(response.errors.bill_no[0]);
                $("#bill_no")[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            if (response.errors.vendor_id) {
                $("#vendor_name").closest(".form-group").find(".error")
                    .text(response.errors.vendor_id[0]);
            }

            // Handle IMEI unique errors dynamically
            let imeiErrors = [];
            $.each(response.errors, function(key, val) {
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
        }
    } else {
        Swal.fire({
            title: "Error!",
            text: "Error updating purchase. Please try again.",
            icon: "error",
            confirmButtonText: "OK",
            confirmButtonColor: "#ff9f43"
        });
    }
}
                });
            }

            $(document).on("input", ".quantity-input", function() {
                let value = parseFloat($(this).val()) || 0;
                if (value < 0) value = 0;
                $(this).val(value);
            });

            calculateTotal();

            // Bank Modal logic
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
            // IMEI / Serial Number Handling
            let currentSerialRow = null;

            function updateSerialUI(row) {
                let categorySelect = row.find('.category-select');
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
                    serialNa.hide();
                    let existingSerials = [];
                    try {
                        let imeiVal = imeiInput.val();
                        if (imeiVal && imeiVal !== 'null') {
                            existingSerials = JSON.parse(imeiVal);
                        }
                    } catch(e) {}
                    
                    // Trim serials if qty is reduced
                    if (existingSerials.length > qty) {
                        existingSerials = existingSerials.slice(0, qty);
                        imeiInput.val(JSON.stringify(existingSerials));
                    }
                    
                    row.find('.serial-status').text(`${existingSerials.length}/${qty} serial numbers added`);
                } else {
                    serialContainer.hide();
                    productNameCol.removeClass('col-lg-2').addClass('col-lg-3');
                    serialBtnWrapper.hide();
                    serialNa.show();
                    imeiInput.val('[]'); // clear if not mobile
                }
            }

            $(document).on('change', '.category-select', function() {
                updateSerialUI($(this).closest('.purchase-product-row'));
            });

            $(document).on('input change', '.quantity-input', function() {
                updateSerialUI($(this).closest('.purchase-product-row'));
            });

            $(document).on('click', '.edit-serial-btn', function() {
                currentSerialRow = $(this).closest('.purchase-product-row');
                let qty = parseFloat(currentSerialRow.find('.quantity-input').val()) || 0;
                
                if (qty <= 0) {
                    Swal.fire('Warning', 'Please enter a quantity greater than 0 first.', 'warning');
                    return;
                }

                let existingSerials = [];
                try {
                    let imeiVal = currentSerialRow.find('.serial-data-input').val();
                    if (imeiVal && imeiVal !== 'null') {
                        existingSerials = JSON.parse(imeiVal);
                    }
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

            // Make sure UI updates when row is loaded from backend
            let observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                        $(mutation.addedNodes).each(function() {
                            if ($(this).hasClass('purchase-product-row')) {
                                updateSerialUI($(this));
                            }
                        });
                    }
                });
            });

            let formContainer = document.getElementById('form-container');
            if (formContainer) {
                observer.observe(formContainer, { childList: true });
            }
            
            // Wait for initial rows
            setTimeout(function() {
                $('.purchase-product-row').each(function() {
                    updateSerialUI($(this));
                });
            }, 1000);
            
        });
    </script>
@endpush
