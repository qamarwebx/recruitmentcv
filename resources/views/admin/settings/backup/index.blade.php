@extends('layout.admin.admin_layout')

@section('title', 'DB Backup')

@section('page-style')
    <style>
        .db-status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: .35rem; }
        .db-status-dot.on { background: #28c76f; }
        .db-status-dot.off { background: #a8aaae; }
        .db-badge-success { background-color: rgba(40,199,111,.12); color: #28c76f; }
        .db-badge-failed { background-color: rgba(234,84,85,.12); color: #ea5455; }
        .db-badge-running { background-color: rgba(115,103,240,.12); color: #7367f0; }
        #db-table-body td { vertical-align: middle; }
        #drive-table-body td { vertical-align: middle; }
        .db-error-msg { max-width: 320px; white-space: normal; word-break: break-word; }
    </style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    <div class="row">
        {{-- Daily Backup --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Daily Backup</h5>
                    <span id="db-daily-dot" class="db-status-dot {{ $settings->daily_enabled ? 'on' : 'off' }}"></span>
                </div>
                <div class="card-body">
                    <form id="db-daily-form">
                        @csrf
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="daily_enabled" id="db-daily-enabled" {{ $settings->daily_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="db-daily-enabled">Enabled</label>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Backup Time</label>
                                <input type="time" class="form-control" name="daily_time" value="{{ $settings->daily_time }}" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Retention Copies</label>
                                <input type="number" class="form-control" name="daily_retention" min="{{ $retentionMin }}" max="{{ $retentionMax }}" value="{{ $settings->daily_retention }}" required>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary btn-sm" id="db-daily-save">Save</button>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="db-daily-generate">
                                <span class="spinner-border spinner-border-sm d-none" id="db-daily-generate-spin"></span>
                                Generate Now
                            </button>
                        </div>
                    </form>
                    <hr>
                    <div class="small text-muted">
                        <div>Last successful backup: <strong id="db-daily-last-success">&mdash;</strong></div>
                        <div>Next scheduled: <strong id="db-daily-next">&mdash;</strong></div>
                        <div id="db-daily-running-note" class="alert alert-info py-1 px-2 mt-2 mb-0 d-none" style="font-size:.8rem;">
                            <i class="ti ti-loader-2 ti-xs"></i> Daily backup in progress&hellip;
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Interval Backup --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Interval Backup</h5>
                    <span id="db-interval-dot" class="db-status-dot {{ $settings->interval_enabled ? 'on' : 'off' }}"></span>
                </div>
                <div class="card-body">
                    <form id="db-interval-form">
                        @csrf
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="interval_enabled" id="db-interval-enabled" {{ $settings->interval_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="db-interval-enabled">Enabled</label>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Backup Interval</label>
                                <select class="form-select" name="interval_value" required>
                                    @foreach($intervalOptions as $minutes => $label)
                                        <option value="{{ $minutes }}" {{ (int) $settings->interval_value === (int) $minutes ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Retention Copies</label>
                                <input type="number" class="form-control" name="interval_retention" min="{{ $retentionMin }}" max="{{ $retentionMax }}" value="{{ $settings->interval_retention }}" required>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary btn-sm" id="db-interval-save">Save</button>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="db-interval-generate">
                                <span class="spinner-border spinner-border-sm d-none" id="db-interval-generate-spin"></span>
                                Generate Now
                            </button>
                        </div>
                    </form>
                    <hr>
                    <div class="small text-muted">
                        <div>Last successful backup: <strong id="db-interval-last-success">&mdash;</strong></div>
                        <div>Next scheduled: <strong id="db-interval-next">&mdash;</strong></div>
                        <div id="db-interval-running-note" class="alert alert-info py-1 px-2 mt-2 mb-0 d-none" style="font-size:.8rem;">
                            <i class="ti ti-loader-2 ti-xs"></i> Interval backup in progress&hellip;
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Google Drive Backup --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="ti ti-brand-google-drive ti-xs me-1"></i> Google Drive Backup</h5>
                    <span id="drive-status-dot" class="db-status-dot off"></span>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8 mb-3 mb-md-0">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="drive-enabled">
                                <label class="form-check-label" for="drive-enabled">Also upload backups to Google Drive</label>
                            </div>
                            <div class="small text-muted">
                                <div>Status: <strong id="drive-status-text">&mdash;</strong></div>
                                <div>Connected account: <strong id="drive-account-email">&mdash;</strong></div>
                                <div>Drive folder: <strong id="drive-folder-name">&mdash;</strong></div>
                                <div>Last tested: <strong id="drive-last-tested">&mdash;</strong></div>
                                <div id="drive-error-note" class="alert alert-danger py-1 px-2 mt-2 mb-0 d-none" style="font-size:.8rem;"></div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                <a href="{{ route('admin.settings.db_backup.drive.connect') }}" id="drive-connect-btn" class="btn btn-primary btn-sm d-none">Connect Google Drive</a>
                                <a href="{{ route('admin.settings.db_backup.drive.connect') }}" id="drive-reconnect-btn" class="btn btn-outline-primary btn-sm d-none">Reconnect</a>
                                <button type="button" class="btn btn-outline-secondary btn-sm d-none" id="drive-test-btn">
                                    <span class="spinner-border spinner-border-sm d-none" id="drive-test-spin"></span>
                                    Test Connection
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm d-none" id="drive-disconnect-btn">Disconnect</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Backup Management - DB --}}
    <div class="card mb-4">
        <div class="card-header border-bottom">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="mb-0">Backup Management - DB</h5>
                <select id="db-filter-type" class="form-select form-select-sm" style="width: 160px;">
                    <option value="">All Types</option>
                    <option value="daily">Daily</option>
                    <option value="interval">Interval</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Backup Name</th>
                        <th>Date &amp; Time</th>
                        <th>File Size</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="db-table-body"></tbody>
            </table>
            <div class="text-center py-4 d-none" id="db-loading"><span class="spinner-border spinner-border-sm text-primary"></span></div>
            <div class="text-center py-5 d-none text-muted" id="db-empty"><i class="ti ti-database-off ti-lg mb-2 d-block"></i>No backups found.</div>
        </div>
        <div class="card-footer d-flex align-items-center justify-content-between">
            <small class="text-muted" id="db-page-info"></small>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary" id="db-prev-page"><i class="ti ti-chevron-left ti-xs"></i></button>
                <button type="button" class="btn btn-outline-secondary" id="db-next-page"><i class="ti ti-chevron-right ti-xs"></i></button>
            </div>
        </div>
    </div>

    {{-- Backup Management - Google Drive --}}
    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="mb-0">Backup Management - Google Drive</h5>
                <select id="drive-filter-type" class="form-select form-select-sm" style="width: 160px;">
                    <option value="">All Types</option>
                    <option value="daily">Daily</option>
                    <option value="interval">Interval</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Backup Name</th>
                        <th>Date &amp; Time</th>
                        <th>File Size</th>
                        <th>Status</th>
                        <th>Google Account</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="drive-table-body"></tbody>
            </table>
            <div class="text-center py-4 d-none" id="drive-loading"><span class="spinner-border spinner-border-sm text-primary"></span></div>
            <div class="text-center py-5 d-none text-muted" id="drive-empty"><i class="ti ti-brand-google-drive ti-lg mb-2 d-block"></i>No Google Drive backups found.</div>
        </div>
        <div class="card-footer d-flex align-items-center justify-content-between">
            <small class="text-muted" id="drive-page-info"></small>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary" id="drive-prev-page"><i class="ti ti-chevron-left ti-xs"></i></button>
                <button type="button" class="btn btn-outline-secondary" id="drive-next-page"><i class="ti ti-chevron-right ti-xs"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    const routes = {
        json: "{{ route('admin.settings.db_backup.json') }}",
        status: "{{ route('admin.settings.db_backup.status') }}",
        saveDaily: "{{ route('admin.settings.db_backup.daily.save') }}",
        saveInterval: "{{ route('admin.settings.db_backup.interval.save') }}",
        generate: "{{ url('admin/settings/db-backup/generate') }}",
        download: "{{ url('admin/settings/db-backup') }}",
        delete: "{{ url('admin/settings/db-backup') }}",
        driveJson: "{{ route('admin.settings.db_backup.drive.json') }}",
        driveStatus: "{{ route('admin.settings.db_backup.drive.status') }}",
        driveEnabled: "{{ route('admin.settings.db_backup.drive.enabled') }}",
        driveDisconnect: "{{ route('admin.settings.db_backup.drive.disconnect') }}",
        driveTest: "{{ route('admin.settings.db_backup.drive.test') }}",
        driveDelete: "{{ url('admin/settings/db-backup/drive') }}",
    };

    const state = { type: '', page: 1 };
    let statusPolling = null;

    function badgeHtml(status) {
        if (status === 'success') { return '<span class="badge db-badge-success">Success</span>'; }
        if (status === 'failed') { return '<span class="badge db-badge-failed">Failed</span>'; }
        return '<span class="badge db-badge-running">' + status + '</span>';
    }

    function rowHtml(b) {
        let actions = '';
        if (b.can_download) {
            actions += '<a class="btn btn-icon btn-sm btn-outline-primary me-1" title="Download" href="' + routes.download + '/' + b.id + '/download"><i class="ti ti-download ti-xs"></i></a>';
        }
        actions += '<button type="button" class="btn btn-icon btn-sm btn-outline-danger db-delete-btn" data-id="' + b.id + '" title="Delete"><i class="ti ti-trash ti-xs"></i></button>';

        const typeLabel = b.type === 'daily' ? 'Daily' : 'Interval';
        const nameCell = (b.filename ? $('<div>').text(b.filename).html() : '&mdash;') + ' <span class="badge bg-label-secondary">' + typeLabel + '</span>';
        const errorNote = b.error_message ? '<div class="text-danger small db-error-msg">' + $('<div>').text(b.error_message).html() + '</div>' : '';

        return '' +
        '<tr>' +
            '<td>' + nameCell + '</td>' +
            '<td>' + (b.generated_at || '&mdash;') + '</td>' +
            '<td>' + (b.file_size_human || '&mdash;') + '</td>' +
            '<td>' + badgeHtml(b.status) + errorNote + '</td>' +
            '<td class="text-end">' + actions + '</td>' +
        '</tr>';
    }

    function loadTable() {
        $('#db-loading').removeClass('d-none');
        $.get(routes.json, { type: state.type, page: state.page })
            .done(function (res) {
                $('#db-table-body').empty();
                (res.data || []).forEach(function (b) { $('#db-table-body').append(rowHtml(b)); });
                $('#db-empty').toggleClass('d-none', (res.data || []).length > 0);
                $('#db-page-info').text('Page ' + res.current_page + ' of ' + res.last_page + ' (' + res.total + ' total)');
                $('#db-prev-page').prop('disabled', !res.prev_page_url);
                $('#db-next-page').prop('disabled', !res.next_page_url);
            })
            .always(function () { $('#db-loading').addClass('d-none'); });
    }

    function applyStatus(summary) {
        ['daily', 'interval'].forEach(function (type) {
            const s = summary[type];
            if (!s) { return; }
            $('#db-' + type + '-dot').toggleClass('on', s.enabled).toggleClass('off', !s.enabled);
            $('#db-' + type + '-last-success').text(s.last_success_at || 'Never');
            $('#db-' + type + '-next').text(s.enabled ? (s.next_scheduled_at || '&mdash;') : 'Disabled');
            $('#db-' + type + '-running-note').toggleClass('d-none', !s.is_busy);
            $('#db-' + type + '-generate').prop('disabled', s.is_busy);
            $('#db-' + type + '-generate-spin').toggleClass('d-none', !s.is_busy);
        });

        const anyBusy = (summary.daily && summary.daily.is_busy) || (summary.interval && summary.interval.is_busy);
        if (anyBusy && !statusPolling) {
            statusPolling = setInterval(loadStatus, 5000);
        } else if (!anyBusy && statusPolling) {
            clearInterval(statusPolling);
            statusPolling = null;
            loadTable();
        }
    }

    function loadStatus() {
        $.get(routes.status).done(function (res) { applyStatus(res.summary); });
    }

    $('#db-filter-type').on('change', function () { state.type = $(this).val(); state.page = 1; loadTable(); });
    $('#db-prev-page').on('click', function () { if (state.page > 1) { state.page--; loadTable(); } });
    $('#db-next-page').on('click', function () { state.page++; loadTable(); });

    $(document).on('click', '.db-delete-btn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This backup will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: { confirmButton: 'btn btn-danger me-3', cancelButton: 'btn btn-label-secondary' },
            buttonsStyling: false
        }).then(function (result) {
            if (!result.isConfirmed) { return; }
            $.post(routes.delete + '/' + id + '/delete', { _token: '{{ csrf_token() }}' })
                .done(function (res) {
                    toastr['success'](res.message, 'DB Backup');
                    loadTable();
                })
                .fail(function (xhr) {
                    toastr['error'](xhr.responseJSON?.message || 'Failed to delete backup.', 'Error');
                });
        });
    });

    function bindGenerate(type) {
        $('#db-' + type + '-generate').on('click', function () {
            const $btn = $(this);
            if ($btn.prop('disabled')) { return; }
            $btn.prop('disabled', true);
            $('#db-' + type + '-generate-spin').removeClass('d-none');
            $.post(routes.generate + '/' + type, { _token: '{{ csrf_token() }}' })
                .done(function (res) {
                    toastr['info'](res.message, 'DB Backup');
                    loadStatus();
                })
                .fail(function (xhr) {
                    toastr['error'](xhr.responseJSON?.message || 'Failed to start backup.', 'Error');
                    $btn.prop('disabled', false);
                    $('#db-' + type + '-generate-spin').addClass('d-none');
                });
        });
    }
    bindGenerate('daily');
    bindGenerate('interval');

    function bindSave(formId, route, btnId) {
        $(formId).on('submit', function (e) {
            e.preventDefault();
            const $btn = $(btnId);
            if ($btn.prop('disabled')) { return; }
            $btn.prop('disabled', true);

            const data = $(this).serializeArray().reduce(function (acc, f) { acc[f.name] = f.value; return acc; }, {});
            data[$(formId).find('input[type=checkbox]').attr('name')] = $(formId).find('input[type=checkbox]').is(':checked') ? 1 : 0;

            $.post(route, data)
                .done(function (res) {
                    toastr['success'](res.message, 'DB Backup');
                    loadStatus();
                })
                .fail(function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to save settings.';
                    toastr['error'](msg, 'Error');
                })
                .always(function () { $btn.prop('disabled', false); });
        });
    }
    bindSave('#db-daily-form', routes.saveDaily, '#db-daily-save');
    bindSave('#db-interval-form', routes.saveInterval, '#db-interval-save');

    // -----------------------------------------------------------
    // Google Drive Backup
    // -----------------------------------------------------------
    const driveState = { type: '', page: 1 };

    function driveBadgeHtml(status) {
        if (status === 'uploaded') { return '<span class="badge db-badge-success">Uploaded</span>'; }
        if (status === 'failed') { return '<span class="badge db-badge-failed">Failed</span>'; }
        return '<span class="badge db-badge-running">Pending</span>';
    }

    function driveRowHtml(b) {
        const typeLabel = b.type === 'daily' ? 'Daily' : 'Interval';
        const nameCell = (b.backup_reference ? $('<div>').text(b.backup_reference).html() : '&mdash;') + ' <span class="badge bg-label-secondary">' + typeLabel + '</span>';
        const errorNote = b.error_message ? '<div class="text-danger small db-error-msg">' + $('<div>').text(b.error_message).html() + '</div>' : '';

        let actions = '';
        if (b.drive_view_link) {
            actions += '<a class="btn btn-icon btn-sm btn-outline-primary me-1" title="Open in Google Drive" target="_blank" rel="noopener noreferrer" href="' + b.drive_view_link + '"><i class="ti ti-external-link ti-xs"></i></a>';
        }
        actions += '<button type="button" class="btn btn-icon btn-sm btn-outline-danger drive-delete-btn" data-id="' + b.id + '" title="Delete"><i class="ti ti-trash ti-xs"></i></button>';

        return '' +
        '<tr>' +
            '<td>' + nameCell + '</td>' +
            '<td>' + (b.created_at || '&mdash;') + '</td>' +
            '<td>' + (b.file_size_human || '&mdash;') + '</td>' +
            '<td>' + driveBadgeHtml(b.status) + errorNote + '</td>' +
            '<td>' + (b.google_account_email ? $('<div>').text(b.google_account_email).html() : '&mdash;') + '</td>' +
            '<td class="text-end">' + actions + '</td>' +
        '</tr>';
    }

    function loadDriveTable() {
        $('#drive-loading').removeClass('d-none');
        $.get(routes.driveJson, { type: driveState.type, page: driveState.page })
            .done(function (res) {
                $('#drive-table-body').empty();
                (res.data || []).forEach(function (b) { $('#drive-table-body').append(driveRowHtml(b)); });
                $('#drive-empty').toggleClass('d-none', (res.data || []).length > 0);
                $('#drive-page-info').text('Page ' + res.current_page + ' of ' + res.last_page + ' (' + res.total + ' total)');
                $('#drive-prev-page').prop('disabled', !res.prev_page_url);
                $('#drive-next-page').prop('disabled', !res.next_page_url);
            })
            .always(function () { $('#drive-loading').addClass('d-none'); });
    }

    $('#drive-filter-type').on('change', function () { driveState.type = $(this).val(); driveState.page = 1; loadDriveTable(); });
    $('#drive-prev-page').on('click', function () { if (driveState.page > 1) { driveState.page--; loadDriveTable(); } });
    $('#drive-next-page').on('click', function () { driveState.page++; loadDriveTable(); });

    $(document).on('click', '.drive-delete-btn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will remove the backup from Google Drive.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: { confirmButton: 'btn btn-danger me-3', cancelButton: 'btn btn-label-secondary' },
            buttonsStyling: false
        }).then(function (result) {
            if (!result.isConfirmed) { return; }
            $.post(routes.driveDelete + '/' + id + '/delete', { _token: '{{ csrf_token() }}' })
                .done(function (res) {
                    toastr['success'](res.message, 'Google Drive Backup');
                    loadDriveTable();
                })
                .fail(function (xhr) {
                    toastr['error'](xhr.responseJSON?.message || 'Failed to delete Drive backup.', 'Error');
                });
        });
    });

    function applyDriveAccount(account) {
        const connected = !!account.is_connected;
        $('#drive-status-dot').toggleClass('on', account.enabled && connected).toggleClass('off', !(account.enabled && connected));
        $('#drive-status-text').text(connected ? (account.status.charAt(0).toUpperCase() + account.status.slice(1)) : 'Not connected');
        $('#drive-account-email').text(account.google_email || '&mdash;');
        $('#drive-folder-name').text(account.drive_folder_name || '&mdash;');
        $('#drive-last-tested').text(account.last_tested_at || 'Never');
        $('#drive-enabled').prop('checked', account.enabled);
        $('#drive-enabled').prop('disabled', !connected);

        $('#drive-connect-btn').toggleClass('d-none', connected);
        $('#drive-reconnect-btn').toggleClass('d-none', !connected);
        $('#drive-test-btn').toggleClass('d-none', !connected);
        $('#drive-disconnect-btn').toggleClass('d-none', !connected);

        if (account.last_error) {
            $('#drive-error-note').removeClass('d-none').text(account.last_error);
        } else {
            $('#drive-error-note').addClass('d-none').text('');
        }
    }

    function loadDriveStatus() {
        $.get(routes.driveStatus).done(function (res) { applyDriveAccount(res.account); });
    }

    $('#drive-enabled').on('change', function () {
        const $chk = $(this);
        const enabled = $chk.is(':checked') ? 1 : 0;
        $chk.prop('disabled', true);
        $.post(routes.driveEnabled, { _token: '{{ csrf_token() }}', enabled: enabled })
            .done(function (res) {
                toastr['success'](res.message, 'Google Drive Backup');
                applyDriveAccount(res.account);
            })
            .fail(function (xhr) {
                toastr['error'](xhr.responseJSON?.message || 'Failed to save setting.', 'Error');
                loadDriveStatus();
            })
            .always(function () { $chk.prop('disabled', false); });
    });

    $('#drive-test-btn').on('click', function () {
        const $btn = $(this);
        if ($btn.prop('disabled')) { return; }
        $btn.prop('disabled', true);
        $('#drive-test-spin').removeClass('d-none');
        $.post(routes.driveTest, { _token: '{{ csrf_token() }}' })
            .done(function (res) {
                toastr['success'](res.message, 'Google Drive Backup');
                applyDriveAccount(res.account);
            })
            .fail(function (xhr) {
                toastr['error'](xhr.responseJSON?.message || 'Google Drive connection test failed.', 'Error');
                if (xhr.responseJSON && xhr.responseJSON.account) { applyDriveAccount(xhr.responseJSON.account); }
            })
            .always(function () { $btn.prop('disabled', false); $('#drive-test-spin').addClass('d-none'); });
    });

    $('#drive-disconnect-btn').on('click', function () {
        Swal.fire({
            title: 'Disconnect Google Drive?',
            text: 'Scheduled backups will no longer be uploaded to Google Drive until you reconnect.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, disconnect',
            customClass: { confirmButton: 'btn btn-danger me-3', cancelButton: 'btn btn-label-secondary' },
            buttonsStyling: false
        }).then(function (result) {
            if (!result.isConfirmed) { return; }
            $.post(routes.driveDisconnect, { _token: '{{ csrf_token() }}' })
                .done(function (res) {
                    toastr['success'](res.message, 'Google Drive Backup');
                    applyDriveAccount(res.account);
                })
                .fail(function (xhr) {
                    toastr['error'](xhr.responseJSON?.message || 'Failed to disconnect.', 'Error');
                });
        });
    });

    loadStatus();
    loadTable();
    loadDriveStatus();
    loadDriveTable();
    setInterval(loadStatus, 30000);
    setInterval(function () { loadDriveStatus(); loadDriveTable(); }, 30000);
})();
</script>
@endsection
