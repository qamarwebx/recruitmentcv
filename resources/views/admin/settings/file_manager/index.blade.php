@extends('layout.admin.admin_layout')

@section('title', 'File Manager Settings')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <style>
        .fq-stat-card .fq-icon { width: 44px; height: 44px; border-radius: .6rem; display: flex; align-items: center; justify-content: center; }
        .fq-overview-bar { height: 22px; border-radius: .4rem; overflow: hidden; display: flex; background: rgba(0,0,0,.06); }
        .fq-overview-bar .seg { height: 100%; }
        .fq-legend-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: .4rem; }
        .fq-chip { border: 1px solid var(--bs-border-color,#e5e7eb); border-radius: 999px; padding: .3rem .8rem; font-size: .8rem; cursor: pointer; background: transparent; display: inline-flex; align-items:center; gap:.35rem; }
        .fq-chip.active { background: #7367f0; color: #fff; border-color: #7367f0; }
        .fq-user-card { border: 1px solid var(--bs-border-color,#e5e7eb); border-radius: .6rem; padding: 1rem; height: 100%; }
        .fq-user-avatar { width: 42px; height: 42px; border-radius: 50%; background: rgba(115,103,240,.12); color: #7367f0; display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .fq-progress { height: 8px; border-radius: 999px; background: rgba(0,0,0,.06); overflow: hidden; }
        .fq-progress .fill { height: 100%; border-radius: 999px; }
        .fq-suggest-btn { font-size: .72rem; }
    </style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    {{-- Top cards --}}
    <div class="row">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="card fq-stat-card h-100">
                <div class="card-body">
                    <div class="fq-icon bg-label-secondary mb-2"><i class="ti ti-server-2 ti-sm"></i></div>
                    <h5 class="card-title mb-0">{{ $summaryHuman['physical_total_human'] }}</h5>
                    <small class="text-muted">Total Storage</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="card fq-stat-card h-100">
                <div class="card-body">
                    <div class="fq-icon bg-label-info mb-2"><i class="ti ti-files ti-sm"></i></div>
                    <h5 class="card-title mb-0">{{ $summaryHuman['file_manager_usage_human'] }}</h5>
                    <small class="text-muted">Used Storage</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="card fq-stat-card h-100">
                <div class="card-body">
                    <div class="fq-icon bg-label-success mb-2"><i class="ti ti-disc ti-sm"></i></div>
                    <h5 class="card-title mb-0">{{ $summaryHuman['physical_free_human'] }}</h5>
                    <small class="text-muted">Available Storage</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="card fq-stat-card h-100">
                <div class="card-body">
                    <div class="fq-icon bg-label-primary mb-2"><i class="ti ti-lock ti-sm"></i></div>
                    <h5 class="card-title mb-0">{{ $summaryHuman['total_allocated_human'] }}</h5>
                    <small class="text-muted">Allocated Storage</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="card fq-stat-card h-100">
                <div class="card-body">
                    <div class="fq-icon bg-label-warning mb-2"><i class="ti ti-square-off ti-sm"></i></div>
                    <h5 class="card-title mb-0">{{ $summaryHuman['unallocated_human'] }}</h5>
                    <small class="text-muted">Unallocated Storage</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="card fq-stat-card h-100">
                <div class="card-body">
                    <div class="fq-icon bg-label-dark mb-2"><i class="ti ti-users ti-sm"></i></div>
                    <h5 class="card-title mb-0">{{ $usersCount }}</h5>
                    <small class="text-muted">Users</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Storage overview visualization --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="mb-3">Storage Overview</h6>
            @php
                $total = max(1, $summaryHuman['physical_total']);
                $usedPct = min(100, round(($summaryHuman['file_manager_usage'] / $total) * 100, 2));
                $allocRemainPct = min(100 - $usedPct, round((max(0, $summaryHuman['total_allocated'] - $summaryHuman['file_manager_usage']) / $total) * 100, 2));
                $unallocPct = max(0, 100 - $usedPct - $allocRemainPct);
            @endphp
            <div class="fq-overview-bar mb-3">
                <div class="seg bg-primary" style="width: {{ $usedPct }}%" title="Used"></div>
                <div class="seg bg-info" style="width: {{ $allocRemainPct }}%" title="Allocated (unused)"></div>
                <div class="seg" style="width: {{ $unallocPct }}%; background: rgba(0,0,0,.08);" title="Unallocated"></div>
            </div>
            <div class="row text-center">
                <div class="col-6 col-md-2 mb-2"><span class="fq-legend-dot" style="background:#999;"></span>Total<br><strong>{{ $summaryHuman['physical_total_human'] }}</strong></div>
                <div class="col-6 col-md-2 mb-2"><span class="fq-legend-dot bg-primary"></span>Used<br><strong>{{ $summaryHuman['file_manager_usage_human'] }}</strong></div>
                <div class="col-6 col-md-2 mb-2"><span class="fq-legend-dot bg-primary" style="background:#7367f0;"></span>Allocated<br><strong>{{ $summaryHuman['total_allocated_human'] }}</strong></div>
                <div class="col-6 col-md-2 mb-2"><span class="fq-legend-dot bg-success"></span>Available<br><strong>{{ $summaryHuman['physical_free_human'] }}</strong></div>
                <div class="col-6 col-md-2 mb-2"><span class="fq-legend-dot" style="background:rgba(0,0,0,.25);"></span>Unallocated<br><strong>{{ $summaryHuman['unallocated_human'] }}</strong></div>
                <div class="col-6 col-md-2 mb-2"><span class="fq-legend-dot bg-danger"></span>Allocation %<br><strong>{{ $summaryHuman['allocation_percent'] }}%</strong></div>
            </div>
        </div>
    </div>

    {{-- User quota cards --}}
    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <h5 class="mb-0">User Storage Allocation</h5>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary active" id="fq-view-card-btn"><i class="ti ti-layout-grid ti-xs"></i></button>
                    <button type="button" class="btn btn-outline-secondary" id="fq-view-list-btn"><i class="ti ti-list ti-xs"></i></button>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text"><i class="ti ti-search ti-xs"></i></span>
                    <input type="text" id="fq-search" class="form-control" placeholder="Search user...">
                </div>
                <select id="fq-user-type" class="selectpicker" data-width="140px" data-style="default-btn" title="Admin/Staff">
                    <option value="">All Users</option>
                    <option value="1">Admin</option>
                    <option value="2">Staff</option>
                </select>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#fqFilterModal"><i class="ti ti-filter ti-xs me-1"></i>More Filters</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="fq-reset">Reset</button>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="fq-chip active" data-status="">All</button>
                <button type="button" class="fq-chip" data-status="near_limit">Near Limit <span class="badge bg-label-warning ms-1" id="fq-cnt-near_limit">0</span></button>
                <button type="button" class="fq-chip" data-status="full">Full <span class="badge bg-label-danger ms-1" id="fq-cnt-full">0</span></button>
                <button type="button" class="fq-chip" data-status="no_allocation">No Allocation <span class="badge bg-label-secondary ms-1" id="fq-cnt-no_allocation">0</span></button>
                <button type="button" class="fq-chip" data-status="highest_usage">Highest Usage</button>
                <button type="button" class="fq-chip" data-status="recently_updated">Recently Updated <span class="badge bg-label-info ms-1" id="fq-cnt-recently_updated">0</span></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row" id="fq-cards"></div>
            <div class="text-center mt-2 d-none" id="fq-load-more-wrap">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="fq-load-more">Load more</button>
            </div>
            <div class="text-center py-4 d-none" id="fq-loading"><span class="spinner-border spinner-border-sm text-primary"></span></div>
            <div class="text-center py-5 d-none text-muted" id="fq-empty"><i class="ti ti-users-group ti-lg mb-2 d-block"></i>No users match these filters.</div>
        </div>
    </div>
</div>

{{-- More Filters Modal (Leads filter pattern) --}}
<div class="modal fade" id="fqFilterModal" aria-hidden="true" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="offcanvas-title">Storage Allocation Filter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 mb-3"><input type="number" min="0" id="fq-alloc-from" class="form-control" placeholder="Allocated from (MB)"></div>
                    <div class="col-md-4 mb-3"><input type="number" min="0" id="fq-alloc-to" class="form-control" placeholder="Allocated to (MB)"></div>
                    <div class="col-md-4 mb-3"><input type="number" min="0" id="fq-used-from" class="form-control" placeholder="Used from (MB)"></div>
                    <div class="col-md-4 mb-3"><input type="number" min="0" id="fq-used-to" class="form-control" placeholder="Used to (MB)"></div>
                    <div class="col-md-4 mb-3">
                        <select id="fq-sort" class="selectpicker w-100" data-style="default-btn" title="Sort By">
                            <option value="name">Name</option>
                            <option value="allocated">Allocated</option>
                            <option value="used">Used</option>
                            <option value="available">Available</option>
                            <option value="usage_percent">Usage %</option>
                            <option value="file_count">File Count</option>
                            <option value="updated_at">Updated Date</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-warning btn-sm" id="fq-modal-reset">Reset</button>
                <button class="btn btn-success btn-sm" id="fq-modal-apply">Apply</button>
            </div>
        </div>
    </div>
</div>

{{-- Manage Quota Modal --}}
<div class="modal fade" id="fqEditModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Storage Quota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="fqEditForm">
                @csrf
                <input type="hidden" name="admin_id" id="fq-edit-id">
                <div class="modal-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="fq-user-avatar" id="fq-edit-avatar">?</div>
                        <div>
                            <div class="fw-semibold" id="fq-edit-name"></div>
                            <small class="text-muted" id="fq-edit-type"></small>
                        </div>
                    </div>
                    <div class="row text-center mb-3">
                        <div class="col-4"><small class="text-muted d-block">Current</small><strong id="fq-edit-current"></strong></div>
                        <div class="col-4"><small class="text-muted d-block">Used</small><strong id="fq-edit-used"></strong></div>
                        <div class="col-4"><small class="text-muted d-block">Usage %</small><strong id="fq-edit-percent"></strong></div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">New Allocation (GB) <span class="text-danger">*</span></label>
                        <input type="number" min="0" step="0.1" name="allocated_gb" id="fq-edit-allocated" class="form-control" required>
                        <div class="alert alert-warning py-1 px-2 mt-2 d-none" id="fq-edit-warning" style="font-size:.78rem;"></div>
                    </div>
                    <div class="mb-3 d-flex gap-1 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary fq-suggest-btn fq-suggest" data-add="5">+5 GB</button>
                        <button type="button" class="btn btn-outline-secondary fq-suggest-btn fq-suggest" data-add="10">+10 GB</button>
                        <button type="button" class="btn btn-outline-secondary fq-suggest-btn fq-suggest" data-add="20">+20 GB</button>
                        <button type="button" class="btn btn-outline-secondary fq-suggest-btn" id="fq-suggest-roundup">Round up to next 10 GB</button>
                    </div>
                    <small class="text-muted">Available system capacity: <strong id="fq-edit-system-available"></strong></small>
                    <div class="mb-3 mt-2">
                        <label class="form-label">Status</label>
                        <select name="status" id="fq-edit-status" class="form-select">
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Allocation</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- User Files Modal --}}
<div class="modal fade" id="fqFilesModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Files - <span id="fq-files-name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="fq-files-list" class="list-group"></div>
                <div class="text-center mt-2 d-none" id="fq-files-more-wrap">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="fq-files-more">Load more</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script>
    (function () {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
        $('.selectpicker').selectpicker();
        $('body').on('shown.bs.modal', '.modal', function () { $(this).find('.selectpicker').selectpicker(); });

        const state = { search: '', userType: '', status: '', allocFrom: '', allocTo: '', usedFrom: '', usedTo: '', sort: 'name', page: 1, view: localStorage.getItem('fq_view') || 'card' };
        applyView();
        function applyView() {
            $('#fq-cards').toggleClass('fq-list-mode', state.view === 'list');
            $('#fq-view-card-btn').toggleClass('active', state.view === 'card');
            $('#fq-view-list-btn').toggleClass('active', state.view === 'list');
        }

        function toBytes(mb) { return mb ? Math.round(parseFloat(mb) * 1024 * 1024) : ''; }
        function formatBytes(bytes) {
            if (!bytes || bytes <= 0) { return '0 B'; }
            const units = ['B', 'KB', 'MB', 'GB', 'TB'];
            const power = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
            return (bytes / Math.pow(1024, power)).toFixed(power === 0 ? 0 : 2) + ' ' + units[power];
        }
        function debounce(fn, wait) { let t; return function () { clearTimeout(t); t = setTimeout(fn, wait); }; }

        function cardHtml(u) {
            const pct = Math.min(100, u.usage_percent);
            const barColor = pct >= 100 ? 'bg-danger' : (pct >= 80 ? 'bg-warning' : 'bg-success');
            const statusBadges = {
                no_allocation: ['bg-label-secondary', 'No Allocation'], available: ['bg-label-success', 'Available'],
                near_limit: ['bg-label-warning', 'Near Limit'], full: ['bg-label-danger', 'Full'], over_limit: ['bg-label-danger', 'Over Limit'],
            };
            const badge = statusBadges[u.quota_status] || ['bg-label-secondary', u.quota_status];
            const initials = (u.name || '?').trim().split(' ').map(s => s[0]).slice(0, 2).join('').toUpperCase();

            return '' +
            '<div class="col-xl-3 col-lg-4 col-md-6 mb-4 fq-card-col">' +
                '<div class="fq-user-card">' +
                    '<div class="d-flex align-items-center justify-content-between mb-2">' +
                        '<div class="d-flex align-items-center gap-2">' +
                            '<div class="fq-user-avatar">' + initials + '</div>' +
                            '<div><div class="fw-semibold">' + $('<div>').text(u.name).html() + '</div><small class="text-muted">' + u.user_type + '</small></div>' +
                        '</div>' +
                        '<span class="badge ' + badge[0] + '">' + badge[1] + '</span>' +
                    '</div>' +
                    '<div class="mb-1"><strong>' + u.used_human + '</strong> <span class="text-muted">/ ' + u.allocated_human + '</span></div>' +
                    '<div class="fq-progress mb-1"><div class="fill ' + barColor + '" style="width:' + pct + '%"></div></div>' +
                    '<small class="text-muted d-block mb-2">' + u.usage_percent + '% used &bull; ' + u.file_count + ' files &bull; ' + u.folder_count + ' folders</small>' +
                    '<div class="d-flex gap-2">' +
                        '<button type="button" class="btn btn-sm btn-primary flex-grow-1 fq-manage" data-id="' + u.id + '" data-name="' + $('<div>').text(u.name).html() + '" data-type="' + u.user_type + '" data-allocated="' + u.allocated_bytes + '" data-used="' + u.used_bytes + '" data-percent="' + u.usage_percent + '">Manage</button>' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary fq-view-files" data-id="' + u.id + '" data-name="' + $('<div>').text(u.name).html() + '">View Files</button>' +
                    '</div>' +
                '</div>' +
            '</div>';
        }

        function loadUsers(reset) {
            if (reset) { state.page = 1; $('#fq-cards').empty(); }
            $('#fq-loading').removeClass('d-none');
            $.get("{{ route('admin.settings.file_manager.json') }}", {
                search_user: state.search, user_type: state.userType, quota_status: state.status,
                alloc_from: toBytes(state.allocFrom), alloc_to: toBytes(state.allocTo),
                used_from: toBytes(state.usedFrom), used_to: toBytes(state.usedTo),
                sort: state.sort, page: state.page, per_page: 24,
            }).done(function (res) {
                res.data.forEach(function (u) { $('#fq-cards').append(cardHtml(u)); });
                $('#fq-empty').toggleClass('d-none', $('#fq-cards').children().length > 0);
                $('#fq-load-more-wrap').toggleClass('d-none', res.meta.current_page >= res.meta.last_page);
                Object.keys(res.quick_counts).forEach(function (k) { $('#fq-cnt-' + k).text(res.quick_counts[k]); });
            }).always(function () { $('#fq-loading').addClass('d-none'); });
        }
        $('#fq-load-more').on('click', function () { state.page++; loadUsers(false); });

        $('#fq-search').on('keyup', debounce(function () { state.search = $('#fq-search').val(); loadUsers(true); }, 400));
        $('#fq-user-type').on('change', function () { state.userType = $(this).val(); loadUsers(true); });

        $(document).on('click', '.fq-chip', function () {
            $('.fq-chip').removeClass('active'); $(this).addClass('active');
            state.status = $(this).data('status') || '';
            loadUsers(true);
        });

        $('#fq-reset').on('click', function () {
            state.search = ''; state.userType = ''; state.status = ''; state.allocFrom = ''; state.allocTo = ''; state.usedFrom = ''; state.usedTo = ''; state.sort = 'name';
            $('#fq-search').val(''); $('#fq-user-type').selectpicker('val', '');
            $('#fq-alloc-from, #fq-alloc-to, #fq-used-from, #fq-used-to').val('');
            $('#fq-sort').selectpicker('val', 'name');
            $('.fq-chip').removeClass('active'); $('.fq-chip[data-status=""]').addClass('active');
            loadUsers(true);
        });

        $('#fq-modal-apply').on('click', function () {
            state.allocFrom = $('#fq-alloc-from').val(); state.allocTo = $('#fq-alloc-to').val();
            state.usedFrom = $('#fq-used-from').val(); state.usedTo = $('#fq-used-to').val();
            state.sort = $('#fq-sort').val() || 'name';
            $('#fqFilterModal').modal('hide');
            loadUsers(true);
        });
        $('#fq-modal-reset').on('click', function () {
            $('#fq-alloc-from, #fq-alloc-to, #fq-used-from, #fq-used-to').val('');
            $('#fq-sort').selectpicker('val', 'name');
        });

        $('#fq-view-card-btn').on('click', function () { state.view = 'card'; localStorage.setItem('fq_view', 'card'); applyView(); });
        $('#fq-view-list-btn').on('click', function () { state.view = 'list'; localStorage.setItem('fq_view', 'list'); applyView(); });

        /* ---------------- Manage quota modal ---------------- */
        $(document).on('click', '.fq-manage', function () {
            const id = $(this).data('id');
            $('#fq-edit-id').val(id);
            $('#fq-edit-name').text($(this).data('name'));
            $('#fq-edit-avatar').text(($(this).data('name') || '?').toString().trim().split(' ').map(s => s[0]).slice(0, 2).join('').toUpperCase());
            $('#fq-edit-type').text($(this).data('type'));
            const allocated = Number($(this).data('allocated'));
            const used = Number($(this).data('used'));
            $('#fq-edit-current').text(formatBytes(allocated));
            $('#fq-edit-used').text(formatBytes(used));
            $('#fq-edit-percent').text($(this).data('percent') + '%');
            $('#fq-edit-allocated').val((allocated / 1024 / 1024 / 1024).toFixed(1)).data('used-bytes', used);
            $('#fq-edit-warning').addClass('d-none');
            $.get("{{ route('admin.settings.file_manager.index') }}").always(function () {}); // no-op, summary already loaded once
            $('#fq-edit-system-available').text('{{ $summaryHuman["unallocated_human"] }}');
            new bootstrap.Modal('#fqEditModal').show();
        });

        $('.fq-suggest').on('click', function () {
            const current = parseFloat($('#fq-edit-allocated').val()) || 0;
            $('#fq-edit-allocated').val((current + parseFloat($(this).data('add'))).toFixed(1));
        });
        $('#fq-suggest-roundup').on('click', function () {
            const current = parseFloat($('#fq-edit-allocated').val()) || 0;
            $('#fq-edit-allocated').val((Math.ceil(current / 10) * 10 || 10).toFixed(1));
        });
        $('#fq-edit-allocated').on('input', function () {
            const usedBytes = $(this).data('used-bytes') || 0;
            const newBytes = Math.round(parseFloat($(this).val() || 0) * 1024 * 1024 * 1024);
            if (newBytes < usedBytes) {
                $('#fq-edit-warning').removeClass('d-none').text('This is below the current usage (' + formatBytes(usedBytes) + '). Allocation cannot be saved below usage.');
            } else {
                $('#fq-edit-warning').addClass('d-none');
            }
        });

        $('#fqEditForm').on('submit', function (e) {
            e.preventDefault();
            const allocatedBytes = Math.round(parseFloat($('#fq-edit-allocated').val()) * 1024 * 1024 * 1024);
            $.post("{{ route('admin.settings.file_manager.quota.update') }}", {
                _token: '{{ csrf_token() }}',
                admin_id: $('#fq-edit-id').val(),
                allocated_bytes: allocatedBytes,
                status: $('#fq-edit-status').val(),
            }).done(function (res) {
                toastr['success'](res.message, 'Success');
                bootstrap.Modal.getInstance(document.getElementById('fqEditModal')).hide();
                loadUsers(true);
            }).fail(function (xhr) {
                toastr['error'](xhr.responseJSON?.message || 'Failed to update quota.', 'Error');
            });
        });

        /* ---------------- View files modal ---------------- */
        let filesState = { adminId: null, page: 1 };
        function loadUserFiles(reset) {
            if (reset) { filesState.page = 1; $('#fq-files-list').empty(); }
            $.get("{{ url('admin/settings/file-manager/user') }}/" + filesState.adminId + '/files', { page: filesState.page }).done(function (res) {
                res.data.forEach(function (item) {
                    $('#fq-files-list').append(
                        '<div class="list-group-item d-flex justify-content-between align-items-center">' +
                            '<span><i class="ti ' + (item.type === 'folder' ? 'ti-folder text-warning' : 'ti-file text-muted') + ' me-2"></i>' + $('<div>').text(item.name).html() + '</span>' +
                            '<small class="text-muted">' + (item.type === 'folder' ? '' : item.size_human + ' &bull; ') + item.updated_at + '</small>' +
                        '</div>'
                    );
                });
                $('#fq-files-more-wrap').toggleClass('d-none', res.current_page >= res.last_page);
            });
        }
        $(document).on('click', '.fq-view-files', function () {
            filesState.adminId = $(this).data('id');
            $('#fq-files-name').text($(this).data('name'));
            loadUserFiles(true);
            new bootstrap.Modal('#fqFilesModal').show();
        });
        $('#fq-files-more').on('click', function () { filesState.page++; loadUserFiles(false); });

        loadUsers(true);
    })();
    </script>
@endsection
