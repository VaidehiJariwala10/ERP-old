@php
$user = auth()->user();
$settings = $settings ?? \App\Models\Setting::first();
$isStaff = $user && $user->role === 'staff';

$showErpSection =
app('hasPermission')(1, 'view') || app('hasPermission')(1, 'add') ||
app('hasPermission')(2, 'view') || app('hasPermission')(2, 'add') ||
app('hasPermission')(3, 'view') || app('hasPermission')(3, 'add') ||
app('hasPermission')(10, 'view') || app('hasPermission')(10, 'add') ||
app('hasPermission')(17, 'view') ||
(app('hasPermission')(2, 'add') && app('hasPermission')(2, 'edit')) ||
(app('hasPermission')(3, 'add') && app('hasPermission')(3, 'edit'));

$showCrmSection =
app('hasPermission')(9, 'view') || app('hasPermission')(9, 'add') ||
app('hasPermission')(32, 'view') || app('hasPermission')(32, 'add') ||
app('hasPermission')(30, 'view') || app('hasPermission')(30, 'add') ||
app('hasPermission')(31, 'view') || app('hasPermission')(31, 'add') ||
app('hasPermission')(33, 'view') || app('hasPermission')(33, 'add');

$showAccountingSection =
app('hasPermission')(16, 'view') ||
app('hasPermission')(5, 'view') || app('hasPermission')(5, 'add') ||
app('hasPermission')(27, 'view') ||
app('hasPermission')(20, 'view') ||
app('hasPermission')(34, 'view') || app('hasPermission')(35, 'view') ||
app('hasPermission')(36, 'view') || app('hasPermission')(37, 'view') ||
app('hasPermission')(38, 'view');
@endphp

<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <style>
                #sidebar-menu .sidebar-search-wrap {
                    display: flex;
                    align-items: center;
                    box-sizing: border-box;
                    width: 100%;
                    height: 40px;
                    margin: 0 0 18px;
                    padding: 0 14px;
                    background: #fff;
                    border: 1px solid #d1d5db;
                    border-radius: 6px;
                    box-shadow: none;
                }

                #sidebar-menu .sidebar-search-wrap:focus-within {
                    border-color: #9ca3af;
                }

                #sidebar-menu .sidebar-search-icon {
                    flex-shrink: 0;
                    margin-right: 10px;
                    color: #9ca3af;
                    font-size: 14px;
                    line-height: 1;
                }

                #sidebar-menu #sidebar-module-search {
                    flex: 1;
                    width: 100%;
                    min-width: 0;
                    height: 100%;
                    margin: 0;
                    padding: 0;
                    border: none !important;
                    outline: none !important;
                    background: transparent !important;
                    box-shadow: none !important;
                    font-size: 14px;
                    font-weight: 400;
                    color: #374151;
                    -webkit-appearance: none;
                    appearance: none;
                }

                #sidebar-menu #sidebar-module-search::placeholder {
                    color: #9ca3af;
                    opacity: 1;
                }

                #sidebar-menu #sidebar-module-search:focus {
                    border: none !important;
                    outline: none !important;
                    box-shadow: none !important;
                }

                .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a {
                    background: #f1dadaff !important;
                    color: #092c4c !important;
                    border-left: 3px solid #f82011ff !important;
                    border-radius: 8px !important;
                    font-weight: 600 !important;
                    padding: 10px 15px !important;
                    display: flex !important;
                    align-items: center !important;
                }

                .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a img {
                    filter: none !important;
                    margin-right: 0 !important;
                }

                .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a span {
                    font-weight: 600 !important;
                    color: #092c4c !important;
                }

                .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a.active {
                    background: #1b2850 !important;
                    color: #fff !important;
                }

                .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a.active span {
                    color: #fff !important;
                }

                .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a.active img {
                    filter: brightness(0) invert(1) !important;
                }

                .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a:hover:not(.active) {
                    background: rgba(241, 195, 195, 1)ff !important;
                }
            </style>
            <div class="sidebar-search-wrap">
                <i class="fas fa-search sidebar-search-icon"></i>
                <input type="text" id="sidebar-module-search" class="sidebar-search-input"
                    placeholder="Search..." autocomplete="off">
            </div>
            <ul class="sidebar-nav-list">
                {{-- Dashboard --}}
                @if ($isStaff || app('hasPermission')(0, 'view'))
                <li class="sidebar-category sidebar-category-dashboard">
                    @if ($isStaff)
                    <a href="{{ route('auth.staff-dashboard') }}"
                        class="{{ request()->routeIs('auth.staff-dashboard') ? 'active' : '' }}">
                        <img src="{{ image_path('admin/assets/img/icons/dashboard.svg') }}" alt="img">
                        <span>Dashboard</span>
                    </a>
                    @else
                    <a href="{{ route('auth.dashboard') }}"
                        class="{{ request()->routeIs('auth.dashboard') ? 'active' : '' }}">
                        <img src="{{ image_path('admin/assets/img/icons/dashboard.svg') }}" alt="img">
                        <span>Dashboard</span>
                    </a>
                    @endif
                </li>
                @endif

                {{-- ====== ERP ====== --}}
                @if ($showErpSection)
                <li class="submenu sidebar-category sidebar-category-erp">
                    <a href="javascript:void(0);">
                        <i class="fas fa-industry"></i>
                        <span>ERP</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        {{-- Products --}}
                        @if (app('hasPermission')(1, 'view') || app('hasPermission')(1, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <img src="{{ image_path('admin/assets/img/icons/product.svg') }}" alt="img">
                                <span>Products</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(1, 'view'))
                                <li><a href="{{ route('product.list') }}" data-search="products all products list erp">All Products</a></li>
                                @endif
                                @if (app('hasPermission')(1, 'add'))
                                <li><a href="{{ route('product.add') }}" data-search="products new product add erp">New Product</a></li>
                                @endif
                                @if (app('hasPermission')(1, 'view'))
                                <li><a href="{{ route('product.import') }}" data-search="products import erp">Import Products</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        {{-- Catalog Setup --}}
                        @if (app('hasPermission')(6, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fas fa-tags"></i>
                                <span>Catalog Setup</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ route('category.list') }}" data-search="category categories catalog erp">All Categories</a></li>
                                <li><a href="{{ route('category.add') }}" data-search="category new catalog erp">New Category</a></li>
                                <li><a href="{{ route('brand.list') }}" data-search="brand brands catalog erp">All Brands</a></li>
                                <li><a href="{{ route('brand.add') }}" data-search="brand new catalog erp">New Brand</a></li>
                                <li><a href="{{ route('unit.list') }}" data-search="unit units catalog erp">All Units</a></li>
                                <li><a href="{{ route('labour_item.all_labour_item') }}" data-search="labour items catalog erp">All Labour Items</a></li>
                            </ul>
                        </li>
                        @endif

                        {{-- Sales and Orders --}}
                        @if (app('hasPermission')(2, 'view') || app('hasPermission')(2, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <img src="{{ image_path('admin/assets/img/icons/sales1.svg') }}" alt="img">
                                <span>Sales & Bills</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(2, 'view'))
                                <li><a href="{{ route('sales.list') }}">All Sales & Bills</a></li>
                                @endif
                                @if (app('hasPermission')(2, 'add'))
                                <li><a href="{{ route('sales.add', ['new_bill' => 1]) }}">New Bill</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        {{-- Purchases --}}
                        @if (app('hasPermission')(3, 'view') || app('hasPermission')(3, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <img src="{{ image_path('admin/assets/img/icons/purchase1.svg') }}" alt="img">
                                <span>Purchases</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(3, 'view'))
                                <li><a href="{{ route('purchase.lists') }}">All Purchases</a></li>
                                @endif
                                @if (app('hasPermission')(3, 'add'))
                                <li><a href="{{ route('purchase.add') }}">New Purchase</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        {{-- Staff --}}
                        @if (app('hasPermission')(8, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <img src="{{ image_path('admin/assets/img/icons/users1.svg') }}" alt="img">
                                <span>Staff</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ route('staff.list') }}">All Staff</a></li>
                                <li><a href="{{ route('staff.add') }}">New Staff</a></li>
                            </ul>
                        </li>
                        @endif
                        {{-- Customers --}}
                        @if (app('hasPermission')(9, 'view') || app('hasPermission')(9, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-users"></i>
                                <span>Customers</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(9, 'view'))
                                <li><a href="{{ route('customer.list') }}">All Customers</a></li>
                                @endif
                                @if (app('hasPermission')(9, 'add'))
                                <li><a href="{{ route('customer.add') }}">New Customer</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        {{-- Vendors --}}
                        @if (app('hasPermission')(10, 'view') || app('hasPermission')(10, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-handshake"></i>
                                <span>Vendors</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(4, 'view'))
                                <li><a href="{{ route('vendor.list') }}">All Vendors</a></li>
                                @endif
                                @if (app('hasPermission')(4, 'add'))
                                <li><a href="{{ route('vendor.add') }}">New Vendor </a></li>
                                <li><a href="{{ route('vendor.import') }}">Import Vendor </a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        @if (app('hasPermission')(10, 'view') || app('hasPermission')(10, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-building-columns"></i>
                                <span>Financers</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(10, 'view'))
                                <li><a href="{{ route('financer.list') }}">All Financers</a></li>
                                @endif
                                @if (app('hasPermission')(10, 'add'))
                                <li><a href="{{ route('financer.import') }}">Import Financers</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        @if (app('hasPermission')(17, 'view'))
                        <li class="sidebar-module sidebar-module-link">
                            <a href="{{ route('inventory.list') }}">
                                <i class="fa fa-warehouse"></i>
                                <span>Manage Inventory</span>
                            </a>
                        </li>
                        @endif

                        {{-- Returns --}}
                        @if (
                        (app('hasPermission')(2, 'add') && app('hasPermission')(2, 'edit')) ||
                        (app('hasPermission')(3, 'add') && app('hasPermission')(3, 'edit')))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <img src="{{ image_path('admin/assets/img/icons/return1.svg') }}" alt="img">
                                <span>Returns</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(2, 'add') && app('hasPermission')(2, 'edit'))
                                <li><a href="{{ route('salesreturn.list') }}">Sales Return</a></li>
                                @endif
                                @if (app('hasPermission')(3, 'add') && app('hasPermission')(3, 'edit'))
                                <li><a href="{{ route('purchasereturn.list') }}">Purchase Return</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                {{-- ====== CRM ====== --}}
                @if ($showCrmSection)
                <li class="submenu sidebar-category sidebar-category-crm">
                    <a href="javascript:void(0);">
                        <i class="fas fa-user-friends"></i>
                        <span>CRM</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>


                        {{-- Manage Leads --}}
                        @if (app('hasPermission')(32, 'view') || app('hasPermission')(32, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-bullhorn"></i>
                                <span>Manage Leads</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(32, 'view'))
                                <li><a href="{{ route('lead.list') }}">All Leads</a></li>
                                @endif
                                @if (app('hasPermission')(32, 'add'))
                                <li><a href="{{ route('lead.add') }}">New Lead</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        {{-- Follow Ups --}}
                        @if (app('hasPermission')(30, 'view') || app('hasPermission')(30, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-calendar-check"></i>
                                <span>Follow Ups</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(30, 'view'))
                                <li><a href="{{ route('followup.list') }}">All Follow Ups</a></li>
                                @endif
                                @if (app('hasPermission')(30, 'add'))
                                <li><a href="{{ route('followup.add') }}">New Follow Up</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        {{-- Meetings --}}
                        @if (app('hasPermission')(31, 'view') || app('hasPermission')(31, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-handshake"></i>
                                <span>Meetings</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(31, 'view'))
                                <li><a href="{{ route('meeting.list') }}">All Meetings</a></li>
                                @endif
                                @if (app('hasPermission')(31, 'add'))
                                <li><a href="{{ route('meeting.add') }}">New Meeting</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        {{-- Tickets --}}
                        @if (app('hasPermission')(33, 'view') || app('hasPermission')(33, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-ticket-alt"></i>
                                <span>Tickets</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(33, 'view'))
                                <li><a href="{{ route('ticket.list') }}">All Tickets</a></li>
                                @endif
                                @if (app('hasPermission')(33, 'add'))
                                <li><a href="{{ route('ticket.add') }}">New Ticket</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                {{-- ====== ACCOUNTING ====== --}}
                @if ($showAccountingSection)
                <li class="submenu sidebar-category sidebar-category-accounting">
                    <a href="javascript:void(0);">
                        <i class="fas fa-calculator"></i>
                        <span>Accounting</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        {{-- Account Branch --}}
                        <!-- @if (app('hasPermission')(1, 'view') || app('hasPermission')(16, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-building"></i>
                                <span>Account Branch</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(1, 'view') || app('hasPermission')(16, 'view'))
                                    <li><a href="{{ route('account_branch.list') }}">All Account Branches</a></li>
                                @endif
                                @if (app('hasPermission')(1, 'add') || app('hasPermission')(16, 'add'))
                                    <li><a href="{{ route('account_branch.add') }}">New Account Branch</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif -->

                        @if (app('hasPermission')(16, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-calculator"></i>
                                <span>Manage Accounting</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(16, 'view'))
                                <li><a href="{{ route('account_ledger.add') }}">Account Ledger</a></li>
                                @endif
                                @if (app('hasPermission')(16, 'view'))
                                <li><a href="{{ route('income-statement.index') }}">Income Statement</a></li>
                                @endif
                                @if (app('hasPermission')(16, 'view'))
                                <li><a href="{{ route('accounting.balance-sheet') }}">Balance Sheet</a></li>
                                @endif
                                @if (app('hasPermission')(16, 'view'))
                                <li><a href="{{ route('banks.index') }}">Banks</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        {{-- Receipt & Payment --}}
                        @if (app('hasPermission')(34, 'view') || app('hasPermission')(34, 'add'))
                        <li class="sidebar-module sidebar-module-link">
                            <a href="{{ route('sales.receipt.index') }}">
                                <i class="fa fa-receipt"></i>
                                <span>Receipt &amp; Payment</span>
                            </a>
                        </li>
                        @endif


                        {{-- Expenses --}}
                        @if (app('hasPermission')(5, 'view') || app('hasPermission')(5, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <img src="{{ image_path('admin/assets/img/icons/expense1.svg') }}" alt="img">
                                <span>Expenses</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(5, 'view'))
                                <li><a href="{{ route('expense.list') }}">All Expenses</a></li>
                                @endif
                                @if (app('hasPermission')(5, 'add'))
                                <li><a href="{{ route('expense.add') }}">New Expense</a></li>
                                @endif
                                @if (app('hasPermission')(5, 'view'))
                                <li><a href="{{ route('expensetype.list') }}">All Expense Type</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        {{-- Invoices --}}
                        <!-- @if (app('hasPermission')(4, 'view') || app('hasPermission')(4, 'add'))
                                <li class="submenu sidebar-module">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-clipboard"></i>
                                        <span>Invoices</span> <span class="menu-arrow"></span></a>
                                    <ul>
                                        @if (app('hasPermission')(4, 'view'))
                                            <li><a href="{{ route('custom_invoice.lists') }}">All Invoices</a></li>
                                        @endif
                                        @if (app('hasPermission')(4, 'add'))
                                            <li><a href="{{ route('custom_invoice.add') }}">New Invoice</a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif -->



                        {{-- Cash & Bank --}}
                        @if (app('hasPermission')(27, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fas fa-book"></i>
                                <span>Cash & Bank</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(27, 'view'))
                                <li><a href="{{ route('transaction.bankbook') }}">Bank Book</a></li>
                                @endif
                                @if (app('hasPermission')(27, 'view'))
                                <li><a href="{{ route('transaction.cashbook') }}">Cash Book</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        {{-- Credit/Debit Notes --}}
                        @if (app('hasPermission')(27, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-credit-card"></i>
                                <span>Credit/Debit Notes</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(27, 'view'))
                                <li>
                                    <a href="{{ route('credit-notes-items.index') }}">
                                        Credit Notes
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('debit-notes-items.index') }}">
                                        Debit Notes
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('credit-notes.index') }}">
                                        Credit/Debit Notes Type
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        {{-- GST Reports --}}
                        @if (app('hasPermission')(20, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fas fa-chart-line"></i>
                                <span>GST Reports</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(20, 'view'))
                                <li><a href="{{ route('gst.sales_list') }}">GST Report</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        {{-- Reports --}}
                        @if (app('hasPermission')(34, 'view') || app('hasPermission')(35, 'view') || app('hasPermission')(36, 'view') || app('hasPermission')(37, 'view') || app('hasPermission')(38, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <img src="{{ image_path('admin/assets/img/icons/time.svg') }}" alt="img">
                                <span>Reports</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(34, 'view'))
                                <li><a href="{{ route('sales.report') }}">Sales Report</a></li>
                                @endif
                                @if (app('hasPermission')(38, 'view') && !empty($settings) && $settings->tds_apply)
                                <li><a href="{{ route('tds.report') }}">TDS Report</a></li>
                                @endif
                                @if (app('hasPermission')(35, 'view'))
                                <li><a href="{{ route('purchase.report') }}">Purchase Report</a></li>
                                @endif
                                @if (app('hasPermission')(36, 'view'))
                                <li><a href="{{ route('expense.report') }}">Expenses Report</a></li>
                                @endif
                                @if (app('hasPermission')(37, 'view'))
                                <li><a href="{{ route('profit-loss-report.index') }}">Profit-Loss Report</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                {{-- My Branch — only visible when plan allows multiple branches --}}
                <!-- @if (auth()->user()->role === 'admin' && canUseBranches())
                <li class="submenu sidebar-module">
                    <a href="javascript:void(0);">
                        <i class="fa fa-code-branch"></i>
                        <span>My Branch</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="{{ route('subbranch.list') }}">All Branches</a></li>
                        <li><a href="{{ route('subbranch.add') }}">New Branch</a></li>
                    </ul>
                </li>
                @endif
            </ul>
            </li> -->
            

            {{-- Settings --}}
            @if (app('hasPermission')(14, 'view') || app('hasPermission')(15, 'view'))
            <li class="submenu sidebar-category sidebar-category-settings">
                <a href="javascript:void(0);">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span> <span class="menu-arrow"></span>
                </a>
                <ul>
                    @if (in_array(auth()->user()->role, ['admin', 'sub-admin']))
                    <li><a href="{{ route('plans.planlist') }}">Plans</a></li>
                    @endif
                    @if (app('hasPermission')(14, 'view'))
                    <li><a href="{{ route('auth.change-password') }}">Change Password</a></li>
                    @endif
                    @if (app('hasPermission')(14, 'view'))
                    <li><a href="{{ route('setting.generalsettings') }}">Shop Settings</a></li>
                    @endif
                    @if (app('hasPermission')(14, 'view') && (!$settings || $settings->send_mail))
                    <li><a href="{{ route('setting.smtpsettings') }}">Smtp Settings</a></li>
                    @endif
                    @if (app('hasPermission')(14, 'view') && (!$settings || $settings->customer_whatsapp_message || $settings->admin_whatsapp_message))
                    <li><a href="{{ route('setting.facebookappconfiguration') }}">WhatsApp
                            Configuration</a></li>
                    @endif
                    @if (app('hasPermission')(15, 'view'))
                    <li><a href="{{ route('auth.taxrates') }}">Tax Rates</a></li>
                    @endif
                    {{-- @if (app('hasPermission')(14, 'view'))
                                <li><a href="{{ route('setting.connecteddevices') }}">Connected Devices</a>
            </li>
            @endif --}}

            </ul>
            </li>
            @endif
            </ul>
        </div>
    </div>


</div>