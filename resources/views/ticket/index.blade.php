@extends('layout.app')

@section('title', 'Tickets')

@section('content')
<style>
    /* ── Badges ──────────────────────────────────────── */
    .ticket-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    .ticket-priority-low      { background:#eef6ff; color:#1d4ed8; }
    .ticket-priority-medium   { background:#fff7e6; color:#b45309; }
    .ticket-priority-high     { background:#fff1f2; color:#be123c; }
    .ticket-priority-urgent   { background:#fee2e2; color:#991b1b; }
    .ticket-status-open       { background:#eef6ff; color:#1d4ed8; }
    .ticket-status-in_progress{ background:#fff7e6; color:#b45309; }
    .ticket-status-resolved   { background:#ecfdf5; color:#047857; }
    .ticket-status-closed     { background:#f3f4f6; color:#4b5563; }

    /* ── Table ───────────────────────────────────────── */
    table.ticket-table {
        table-layout: auto !important;
        width: 100% !important;
    }
    table.ticket-table td,
    table.ticket-table th {
        vertical-align: middle;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
        padding: 10px 8px !important;
    }
    /* Customer column: force wrap */
    table.ticket-table td:nth-child(3),
    table.ticket-table th:nth-child(3) {
        min-width: 120px;
        max-width: 200px;
        white-space: normal !important;
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
    }

    /* ── Action buttons ──────────────────────────────── */
    .action-buttons {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }
    .icon-btn {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        /* background: #f5f5f5; */
        text-decoration: none;
    }
    .icon-btn:hover { background: #ececec; }

    .listing-action-dropdown { display: inline-flex; justify-content: center; width: 100%; }
    .listing-action-dropdown .action-menu-toggle {
        align-items: center; background: #fff; border: 1px solid #d7dde8; border-radius: 4px;
        color: #1b2850; display: inline-flex; height: 28px; justify-content: center; padding: 0; width: 34px;
    }
    .listing-action-menu {
        border: 1px solid #e8ebed; border-radius: 6px; box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
        min-width: 168px; padding: 6px; z-index: 999999999999;
    }
    .listing-action-menu-floating {
        position: fixed !important;
        z-index: 999999999999 !important;
    }
    .listing-action-menu .dropdown-item {
        align-items: center; border-radius: 4px; color: #344054; display: flex; font-size: 12px;
        gap: 8px; padding: 7px 8px; width: 100%;
    }
    .listing-action-menu .dropdown-item i { color: #667085; font-size: 13px; text-align: center; width: 15px; }
    .listing-action-menu .dropdown-item.text-danger,
    .listing-action-menu .dropdown-item.text-danger i { color: #ea5455 !important; }
    .listing-action-menu .dropdown-item:hover { background: #f4f6f8; color: #1b2850; }

    /* ── Toolbar ─────────────────────────────────────── */
    .ticket-toolbar {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    .ticket-toolbar-search {
        flex: 1 1 260px;
        min-width: 200px;
        max-width: 300px;
    }
    .ticket-toolbar .form-group {
        flex: 0 0 180px;
        margin-bottom: 0;
        min-width: 0;
    }
    .ticket-toolbar-actions { display: flex; gap: 8px; align-items: flex-end; margin-left: auto; }
    .search-input { position: relative; display: flex; align-items: center; }
    .search-input input { padding-left: 35px !important; border-radius: 5px; width: 100%; }
    .btn-searchset { position: absolute; left: 10px; z-index: 10; padding: 0; top: 7px !important; }
    .ticket-toolbar-search,
    .ticket-toolbar .form-group {
        min-width: 0;
    }
    .ticket-toolbar .form-control,
    .ticket-toolbar .select2-container {
        width: 100% !important;
        min-width: 0 !important;
    }
    .ticket-toolbar .select2-selection {
        min-height: 38px;
    }
    .ticket-toolbar .select2-selection__rendered {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ── Pagination ──────────────────────────────────── */
    .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate { display:none !important; }
    .dataTables_wrapper .row:first-child { display:none !important; }
    .dataTables_wrapper { margin-top:0 !important; padding-top:0 !important; }

    .pagination .page-item .page-link {
        background-color: #5d6d7e; color: #fff; border: none;
        margin: 0 3px; padding: 4px 10px; font-weight: bold;
    }
    .pagination .page-item.active .page-link { background-color: #ff9f43 !important; }
    .pagination .page-item .page-link:hover { background-color: #4a5766; }
    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        background-color: #fff; color: #6c757d; border: 1px solid #dee2e6;
    }
    .pagination .page-item.disabled .page-link {
        background-color: #fff !important; color: #dee2e6 !important;
        border: 1px solid #dee2e6 !important; pointer-events: none !important;
    }

    /* ── Details toggle column (mobile/tablet) ───────── */
    @media (min-width: 1200px) {
        table.ticket-table thead th.details-column,
        table.ticket-table tbody td.details-cell { display: none !important; }
    }
    @media (max-width: 1199px) {
        table.ticket-table thead th:nth-child(n+3),
        table.ticket-table tbody td:nth-child(n+3) { display: none !important; }

        /* Hide the separate details column — button is inside first cell */
        table.ticket-table thead th.details-column,
        table.ticket-table tbody td.details-cell { display: none !important; }

        /* First cell: relative so toggle button can be absolute top-right */
        table.ticket-table tbody td:first-child {
            position: relative;
            padding-right: 48px !important; /* room for the button */
        }

        /* Toggle button: absolute top-right of first cell */
        .ticket-toggle-inline {
            position: absolute;
            top: 10px;
            right: 8px;
            background: #ff9f43;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #fff;
            font-size: 20px;
            font-weight: bold;
            line-height: 1;
            text-decoration: none;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .table tbody tr td a {
            color: white !important;
        }
        .ticket-toggle-inline.open { background: #dc3545; }
        .ticket-toggle-inline:hover { opacity: 0.85; }

        /* Customer name word-wrap same as desktop */
        table.ticket-table tbody td:first-child .ticket-customer-mobile {
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: anywhere !important;
            font-size: 13px;
            color: #333;
            margin-top: 2px;
        }
    }

    /* iPad Pro and tablets (768px – 1199px): inline row layout */
    @media (min-width: 768px) and (max-width: 1199px) {
        .ticket-toolbar {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(0, 1fr) minmax(0, 1fr) auto;
            align-items: end;
            gap: 8px;
        }
        .ticket-toolbar-search {
            flex: none;
            min-width: 0;
            max-width: none;
        }
        .ticket-toolbar .form-group {
            flex: none;
            min-width: 0;
            max-width: none;
            margin-bottom: 0;
        }
        .ticket-toolbar-actions {
            flex: none;
            margin-left: 0;
            justify-content: flex-end;
            flex-wrap: nowrap;
            gap: 8px;
            white-space: nowrap;
        }
        .ticket-toolbar-actions .btn {
            flex: none;
            white-space: nowrap;
            padding: .35rem .6rem;
            font-size: 12px;
        }
        .ticket-toolbar-search .form-label,
        .ticket-toolbar .form-group .form-label {
            font-size: 12px;
        }
    }

    /* Small phones (< 768px): stack layout */
    @media (max-width: 767px) {
        .ticket-toolbar {
            flex-wrap: wrap;
        }
        .ticket-toolbar-search { flex: 1 1 100%; max-width: 100%; }
        .ticket-toolbar .form-group { flex: 1 1 calc(50% - 6px); }
        .ticket-toolbar-actions { width: 100%; margin-left: 0; justify-content: space-between; }
        .ticket-toolbar-actions .btn { flex: 1; }
    }
</style>

<div class="content">
    <div class="page-header">
        <div class="page-title"><h4>Tickets</h4></div>
        <div class="page-btn">
            @if (app('hasPermission')(33, 'add'))
                <a href="{{ route('ticket.add') }}" class="btn btn-sm btn-added">
                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1" alt="img">
                    New Ticket
                </a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            {{-- ── Toolbar ── --}}
            <div class="ticket-toolbar">
                <div class="ticket-toolbar-search">
                    <label class="form-label mb-1">Search</label>
                    <div class="search-input">
                        <a class="btn btn-searchset">
                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                        </a>
                        <input type="text" id="ticket-search" class="form-control" placeholder="Ticket no, subject, customer…">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label mb-1">Status</label>
                    <select id="filter-status" class="form-control form-control-sm select2-ticket-filter">
                        <option value="">All Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}">{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label mb-1">Priority</label>
                    <select id="filter-priority" class="form-control form-control-sm select2-ticket-filter">
                        <option value="">All Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>

                {{-- ── Export buttons ── --}}
                <div class="ticket-toolbar-actions">
                    <button id="ticketExcelBtn" class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                    <button id="ticketPdfBtn" class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                    </button>
                </div>
            </div>

            {{-- ── Table ── --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped ticket-table mb-0">
                    <thead>
                        <tr>
                            <th>Ticket No</th>
                            <th class="details-column" style="display:none;"></th>
                            <th>Customer</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-center" style="width:110px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="ticket-tbody">
                        <tr><td colspan="8" class="text-center text-muted">Loading…</td></tr>
                    </tbody>
                </table>
            </div>

            {{-- ── Pagination ── --}}
            <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <span class="me-2" style="font-size:14px;color:#555;">Show per page:</span>
                    <select id="per-page-select" class="form-select form-select-sm" style="width:auto;border:1px solid #ddd;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="ms-3" style="font-size:14px;color:#555;">
                        <span id="pagination-from">0</span> – <span id="pagination-to">0</span> of <span id="pagination-total">0</span> items
                    </span>
                </div>
                <nav><ul class="pagination pagination-sm mb-0" id="pagination-numbers"></ul></nav>
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function () {

    const authToken        = localStorage.getItem('authToken');
    const selectedSubAdmin = localStorage.getItem('selectedSubAdminId') || '';

    let currentPage  = 1;
    let lastPage     = 1;
    let perPage      = 10;
    let searchQuery  = '';

    // ── Select2 for filters ───────────────────────────────────
    $('.select2-ticket-filter').select2({ placeholder: 'Select', width: '100%' });

    // ── Fetch ─────────────────────────────────────────────────
    function fetchTickets(page) {
        page = page || 1;

        $.ajax({
            url: '/api/tickets',
            type: 'GET',
            headers: { 'Authorization': 'Bearer ' + authToken },
            data: {
                page:               page,
                per_page:           perPage,
                search:             searchQuery,
                status:             $('#filter-status').val(),
                priority:           $('#filter-priority').val(),
                selectedSubAdminId: selectedSubAdmin
            },
            success: function (res) {
                if (! res.status || res.data.length === 0) {
                    $('#ticket-tbody').html('<tr><td colspan="8" class="text-center text-muted">No tickets found.</td></tr>');
                    resetPagination();
                    return;
                }

                let html = '';

                $.each(res.data, function (i, t) {
                    const priorityClass = 'ticket-priority-' + (t.priority || 'medium').toLowerCase();
                    const statusClass   = 'ticket-status-'   + (t.status   || 'open'  ).toLowerCase();
                    const statusLabel   = (t.status || '-').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                    const priorityLabel = (t.priority || '-').charAt(0).toUpperCase() + (t.priority || '-').slice(1);

                    // Action buttons
                    const viewBtn = `{{ app('hasPermission')(33, 'view') ? 1 : 0 }}` == 1
                        ? `<a class="icon-btn" href="/ticket-view/${t.id}" title="View Ticket">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"/>
                                    <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"/>
                                </svg>
                           </a>` : '';

                    const historyBtn = t.customer_id
                        ? `<a class="icon-btn" href="/ticket-history/${t.customer_id}" title="Customer Ticket History">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13 3C8.03 3 4 7.03 4 12H1L4.89 15.89L4.96 16.03L9 12H6C6 8.13 9.13 5 13 5C16.87 5 20 8.13 20 12C20 15.87 16.87 19 13 19C11.07 19 9.32 18.21 8.06 16.94L6.64 18.36C8.27 19.99 10.52 21 13 21C17.97 21 22 16.97 22 12C22 7.03 17.97 3 13 3ZM12 8V13L16.25 15.52L17.02 14.24L13.5 12.15V8H12Z" fill="#092C4C"/>
                                </svg>
                           </a>` : '';

                    const editBtn = `{{ app('hasPermission')(33, 'edit') ? 1 : 0 }}` == 1
                        ? `<a class="icon-btn" href="/ticket-edit/${t.id}" title="Edit">
                                <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"/>
                                </svg>
                           </a>` : '';

                    const deleteBtn = `{{ app('hasPermission')(33, 'delete') ? 1 : 0 }}` == 1
                        ? `<a class="icon-btn delete-ticket" data-id="${t.id}" href="javascript:void(0);" title="Delete">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z" fill="#092C4C"/>
                                    <path d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"/>
                                </svg>
                           </a>` : '';

                    const actionItems = `
                        ${`{{ app('hasPermission')(33, 'view') ? 1 : 0 }}` == 1 ? `<a class="dropdown-item" href="/ticket-view/${t.id}"><i class="fas fa-eye"></i> View</a>` : ''}
                        ${t.customer_id ? `<a class="dropdown-item" href="/ticket-history/${t.customer_id}"><i class="fas fa-history"></i> History</a>` : ''}
                        ${`{{ app('hasPermission')(33, 'edit') ? 1 : 0 }}` == 1 ? `<a class="dropdown-item" href="/ticket-edit/${t.id}"><i class="fas fa-edit"></i> Edit</a>` : ''}
                        ${`{{ app('hasPermission')(33, 'delete') ? 1 : 0 }}` == 1 ? `<a class="dropdown-item text-danger delete-ticket" data-id="${t.id}" href="javascript:void(0);"><i class="fas fa-trash"></i> Delete</a>` : ''}
                    `;
                    const desktopActions = actionItems.trim() ? `
                        <div class="dropdown listing-action-dropdown">
                            <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end listing-action-menu">${actionItems}</div>
                        </div>
                    ` : '<span class="text-muted">N/A</span>';

                    html += `
                        <tr>
                            <td style="position:relative;">
                                <a href="#ticket-details-${t.id}"
                                   class="ticket-toggle-inline toggle-details d-xl-none"
                                   data-bs-toggle="collapse"
                                   data-id="${t.id}">
                                    <span class="toggle-icon">+</span>
                                </a>
                                <div><strong>${t.ticket_no}</strong></div>
                                <div class="ticket-customer-mobile d-xl-none"
                                     style="margin-top:4px;font-size:13px;color:#333;white-space:normal;word-break:break-word;overflow-wrap:anywhere;">
                                    ${t.customer_name}
                                </div>
                                <div class="collapse mt-2 d-xl-none" id="ticket-details-${t.id}">
                                    <p class="mb-1"><strong>Subject:</strong> ${t.subject}</p>
                                    <p class="mb-1"><strong>Priority:</strong> <span class="ticket-badge ${priorityClass}">${priorityLabel}</span></p>
                                    <p class="mb-1"><strong>Status:</strong> <span class="ticket-badge ${statusClass}">${statusLabel}</span></p>
                                    <p class="mb-1"><strong>Created:</strong> ${t.created_at}</p>
                                    <div class="mt-2"><div class="action-buttons">${viewBtn}${historyBtn}${editBtn}${deleteBtn}</div></div>
                                </div>
                            </td>
                            <td class="details-cell" style="display:none;"></td>
                            <td>${t.customer_name}</td>
                            <td>${t.subject}</td>
                            <td><span class="ticket-badge ${priorityClass}">${priorityLabel}</span></td>
                            <td><span class="ticket-badge ${statusClass}">${statusLabel}</span></td>
                            <td>${t.created_at}</td>
                            <td class="text-center">${desktopActions}</td>
                        </tr>`;
                });

                $('#ticket-tbody').html(html);
                updatePagination(res.pagination);
            },
            error: function () {
                $('#ticket-tbody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load tickets.</td></tr>');
            }
        });
    }

    function positionListingActionMenu(dropdownEl) {
        const $dropdown = $(dropdownEl);
        const $menu = $dropdown.data('floating-menu') || $dropdown.find('.listing-action-menu');
        const button = $dropdown.find('.action-menu-toggle')[0];

        if (!$menu.length || !button) {
            return;
        }

        const rect = button.getBoundingClientRect();
        const menuEl = $menu[0];
        const menuWidth = menuEl.offsetWidth || 168;
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

        $menu.css({
            left: `${left}px`,
            position: 'fixed',
            top: `${top}px`,
            transform: 'none'
        });
    }

    $(document).on('show.bs.dropdown', '.listing-action-dropdown', function() {
        const $dropdown = $(this);
        const $menu = $dropdown.find('.listing-action-menu');

        if (!$menu.length) {
            return;
        }

        $dropdown.data('floating-menu', $menu);
        $dropdown.data('menu-parent', $menu.parent());
        $dropdown.data('menu-next', $menu.next());
        $menu.addClass('listing-action-menu-floating').appendTo('body');
    });

    $(document).on('shown.bs.dropdown', '.listing-action-dropdown', function() {
        positionListingActionMenu(this);
    });

    $(document).on('hidden.bs.dropdown', '.listing-action-dropdown', function() {
        const $dropdown = $(this);
        const $menu = $dropdown.data('floating-menu');
        const $parent = $dropdown.data('menu-parent');
        const $next = $dropdown.data('menu-next');

        if ($menu && $menu.length && $parent && $parent.length) {
            $menu.removeClass('listing-action-menu-floating').removeAttr('style');
            if ($next && $next.length && $.contains($parent[0], $next[0])) {
                $menu.insertBefore($next);
            } else {
                $parent.append($menu);
            }
        }

        $dropdown.removeData('floating-menu menu-parent menu-next');
    });

    $(window).on('scroll resize', function() {
        $('.listing-action-dropdown.show').each(function() {
            positionListingActionMenu(this);
        });
    });

    // ── Pagination UI ─────────────────────────────────────────
    function updatePagination(p) {
        currentPage = p.current_page;
        lastPage    = p.last_page;

        const from = p.total === 0 ? 0 : (p.current_page - 1) * p.per_page + 1;
        const to   = Math.min(p.current_page * p.per_page, p.total);
        $('#pagination-from').text(from);
        $('#pagination-to').text(to);
        $('#pagination-total').text(p.total);

        let html = '';
        html += `<li class="page-item ${p.current_page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0);" data-page="${p.current_page - 1}">Previous</a></li>`;

        const visible = 2;
        let start = Math.floor((p.current_page - 1) / visible) * visible + 1;
        let end   = Math.min(p.last_page, start + visible - 1);

        if (start > 1) {
            html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="${start - 1}" data-action="prev-group">..</a></li>`;
        }
        for (let i = start; i <= end; i++) {
            html += `<li class="page-item ${i === p.current_page ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a></li>`;
        }
        if (end < p.last_page) {
            html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="${end + 1}" data-action="next-group">..</a></li>`;
        }

        html += `<li class="page-item ${p.current_page === p.last_page || p.last_page === 0 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0);" data-page="${p.current_page + 1}">Next</a></li>`;

        $('#pagination-numbers').html(html);
        $('.pagination-controls').show();
    }

    function resetPagination() {
        $('#pagination-from, #pagination-to, #pagination-total').text(0);
        $('#pagination-numbers').html('');
        $('.pagination-controls').hide();
    }

    // ── Pagination clicks ─────────────────────────────────────
    $(document).on('click', '#pagination-numbers .page-link', function (e) {
        e.preventDefault();
        const page   = parseInt($(this).data('page'));
        const action = $(this).data('action');

        if (action === 'next-group' || action === 'prev-group') {
            if (page >= 1 && page <= lastPage) fetchTickets(page);
            return;
        }
        if (page && page !== currentPage && page >= 1 && page <= lastPage) {
            fetchTickets(page);
        }
    });

    // ── Delete ────────────────────────────────────────────────
    $(document).on('click', '.delete-ticket', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This ticket will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff9f43',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/tickets/${id}/delete`,
                    type: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        if (res.status) {
                            Swal.fire({ title: 'Deleted!', text: res.message, icon: 'success',
                                        confirmButtonColor: '#ff9f43' });
                            fetchTickets(currentPage);
                        } else {
                            Swal.fire('Error!', res.error || 'Could not delete.', 'error');
                        }
                    },
                    error: function () { Swal.fire('Error!', 'Something went wrong.', 'error'); }
                });
            }
        });
    });

    // ── Toggle details icon ───────────────────────────────────
    $(document).on('click', '.toggle-details', function () {
        const btn  = $(this);
        const icon = btn.find('.toggle-icon');
        if (btn.hasClass('open')) {
            btn.removeClass('open');
            icon.text('+');
        } else {
            btn.addClass('open');
            icon.text('−');
        }
    });

    // ── Excel export ──────────────────────────────────────────
    $('#ticketExcelBtn').on('click', function () {
        const params = new URLSearchParams({
            search:             searchQuery,
            status:             $('#filter-status').val() || '',
            priority:           $('#filter-priority').val() || '',
            selectedSubAdminId: selectedSubAdmin
        });

        const url = '/api/tickets/export-excel?' + params.toString();

        Swal.fire({ title: 'Exporting Excel…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        fetch(url, { headers: { 'Authorization': 'Bearer ' + authToken } })
            .then(function (response) {
                // Check content-type — if it's not a spreadsheet, it's an error response
                const contentType = response.headers.get('Content-Type') || '';
                if (!response.ok || contentType.indexOf('spreadsheet') === -1 && contentType.indexOf('excel') === -1 && contentType.indexOf('octet') === -1) {
                    return response.text().then(function (text) {
                        throw new Error(text || 'Export failed');
                    });
                }
                return response.blob();
            })
            .then(function (blob) {
                Swal.close();
                const blobUrl = URL.createObjectURL(blob);
                const link    = document.createElement('a');
                link.href     = blobUrl;
                link.download = 'tickets_' + Date.now() + '.xlsx';
                document.body.appendChild(link);
                link.click();
                link.remove();
                setTimeout(function () { URL.revokeObjectURL(blobUrl); }, 1000);
            })
            .catch(function (err) {
                Swal.close();
                Swal.fire('Export Failed', 'Could not generate Excel file. Please try again.', 'error');
                console.error('Excel export error:', err);
            });
    });

    // ── PDF export ────────────────────────────────────────────
    $('#ticketPdfBtn').on('click', function () {
        const params = new URLSearchParams({
            search:             searchQuery,
            status:             $('#filter-status').val() || '',
            priority:           $('#filter-priority').val() || '',
            selectedSubAdminId: selectedSubAdmin
        });

        const url = '/api/tickets/export-pdf?' + params.toString();

        Swal.fire({ title: 'Generating PDF…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        fetch(url, { headers: { 'Authorization': 'Bearer ' + authToken } })
            .then(function (response) {
                if (!response.ok) throw new Error('Export failed');
                return response.blob();
            })
            .then(function (blob) {
                Swal.close();
                const blobUrl  = URL.createObjectURL(blob);
                const link     = document.createElement('a');
                link.href      = blobUrl;
                link.download  = 'tickets_' + Date.now() + '.pdf';
                document.body.appendChild(link);
                link.click();
                link.remove();
                URL.revokeObjectURL(blobUrl);
            })
            .catch(function () {
                Swal.close();
                Swal.fire('Error!', 'Failed to generate PDF.', 'error');
            });
    });

    // ── Filter/search events ──────────────────────────────────
    let debounce;
    $('#ticket-search').on('keyup', function () {
        clearTimeout(debounce);
        debounce = setTimeout(function () {
            searchQuery = $('#ticket-search').val();
            fetchTickets(1);
        }, 400);
    });

    $('#filter-status, #filter-priority').on('change', function () { fetchTickets(1); });
    $('#per-page-select').on('change', function () { perPage = $(this).val(); fetchTickets(1); });

    // ── Initial load ──────────────────────────────────────────
    fetchTickets(1);
});
</script>
@endpush
