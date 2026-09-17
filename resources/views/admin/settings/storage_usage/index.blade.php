@extends('layout.admin.admin_layout')

@section('title', 'Storage Usage')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <style>
        .su-stat-card .su-icon { width: 44px; height: 44px; border-radius: .6rem; display: flex; align-items: center; justify-content: center; }
        .su-overview-bar { height: 22px; border-radius: .4rem; overflow: hidden; display: flex; background: rgba(0,0,0,.06); }
        .su-overview-bar .seg { height: 100%; }
        .su-legend-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: .4rem; }
        .su-row-bar { height: 6px; border-radius: 999px; background: rgba(0,0,0,.06); overflow: hidden; }
        .su-row-bar .fill { height: 100%; border-radius: 999px; background: #7367f0; }
        #su-spin { display: inline-block; }
    </style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    {{-- Top cards --}}
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card su-stat-card h-100">
                <div class="card-body">
                    <div class="su-icon bg-label-secondary mb-2"><i class="ti ti-server-2 ti-sm"></i></div>
                    <h5 class="card-title mb-0" id="su-total">{{ $summaryHuman['total_human'] }}</h5>
                    <small class="text-muted">Total Storage</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card su-stat-card h-100">
                <div class="card-body">
                    <div class="su-icon bg-label-primary mb-2"><i class="ti ti-database ti-sm"></i></div>
                    <h5 class="card-title mb-0" id="su-used">{{ $summaryHuman['used_human'] }}</h5>
                    <small class="text-muted">Used Storage</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card su-stat-card h-100">
                <div class="card-body">
                    <div class="su-icon bg-label-success mb-2"><i class="ti ti-disc ti-sm"></i></div>
                    <h5 class="card-title mb-0" id="su-free">{{ $summaryHuman['free_human'] }}</h5>
                    <small class="text-muted">Free Storage</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card su-stat-card h-100">
                <div class="card-body">
                    <div class="su-icon bg-label-warning mb-2"><i class="ti ti-chart-pie ti-sm"></i></div>
                    <h5 class="card-title mb-0" id="su-percent">{{ $summaryHuman['usage_percent'] }}%</h5>
                    <small class="text-muted">Usage</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Overview bar + chart --}}
    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="mb-0">Used vs Free</h6>
                        <div class="d-flex align-items-center gap-2">
                            <small class="text-muted">Last calculated: <span id="su-last-calculated">{{ $summaryHuman['last_calculated_at'] ?? 'Never' }}</span></small>
                            <button type="button" class="btn btn-sm btn-primary" id="su-refresh-btn">
                                <span id="su-spin" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                <i class="ti ti-refresh ti-xs" id="su-refresh-icon"></i> Recalculate
                            </button>
                        </div>
                    </div>
                    <div class="su-overview-bar mb-3">
                        <div class="seg bg-primary" id="su-bar-used" style="width: {{ $summaryHuman['usage_percent'] }}%" title="Used"></div>
                        <div class="seg" id="su-bar-free" style="width: {{ 100 - $summaryHuman['usage_percent'] }}%; background: rgba(0,0,0,.08);" title="Free"></div>
                    </div>
                    <div class="row text-center">
                        <div class="col-4"><span class="su-legend-dot" style="background:#999;"></span>Total<br><strong id="su-legend-total">{{ $summaryHuman['total_human'] }}</strong></div>
                        <div class="col-4"><span class="su-legend-dot bg-primary"></span>Used<br><strong id="su-legend-used">{{ $summaryHuman['used_human'] }}</strong></div>
                        <div class="col-4"><span class="su-legend-dot bg-success"></span>Free<br><strong id="su-legend-free">{{ $summaryHuman['free_human'] }}</strong></div>
                    </div>
                    <div id="su-running-note" class="alert alert-info py-1 px-2 mt-3 mb-0 {{ $isRunning ? '' : 'd-none' }}" style="font-size:.8rem;">
                        <i class="ti ti-loader-2 ti-xs"></i> Recalculating storage usage in the background&hellip;
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="mb-2">Module-wise Storage</h6>
                    <div id="su-donut"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Module table --}}
    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="mb-0">Modules</h5>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="input-group input-group-sm" style="width: 220px;">
                        <span class="input-group-text"><i class="ti ti-search ti-xs"></i></span>
                        <input type="text" id="su-search" class="form-control" placeholder="Search module...">
                    </div>
                    <select id="su-sort" class="form-select form-select-sm" style="width: 170px;">
                        <option value="size_desc">Sort: Largest first</option>
                        <option value="size_asc">Sort: Smallest first</option>
                        <option value="name">Sort: Name</option>
                        <option value="files">Sort: File count</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Module</th>
                        <th>Files</th>
                        <th>Size</th>
                        <th style="width: 25%;">Share</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody id="su-table-body"></tbody>
            </table>
            <div class="text-center py-4 d-none" id="su-loading"><span class="spinner-border spinner-border-sm text-primary"></span></div>
            <div class="text-center py-5 d-none text-muted" id="su-empty"><i class="ti ti-database-off ti-lg mb-2 d-block"></i>No modules match this search.</div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    const palette = ['#7367f0', '#00cfe8', '#28c76f', '#ff9f43', '#ea5455', '#82868b', '#5a8dee', '#ffab00', '#39da8a', '#e83e8c'];
    const state = { search: '', sort: 'size_desc' };
    let donutChart = null;

    function debounce(fn, wait) { let t; return function () { clearTimeout(t); t = setTimeout(fn, wait); }; }

    function rowHtml(m, index, maxSize) {
        const pct = maxSize > 0 ? Math.max(2, Math.round((m.size_bytes / maxSize) * 100)) : 0;
        const color = palette[index % palette.length];
        return '' +
        '<tr>' +
            '<td><span class="su-legend-dot" style="background:' + color + ';"></span>' + $('<div>').text(m.label).html() + '</td>' +
            '<td>' + m.file_count.toLocaleString() + '</td>' +
            '<td><strong>' + m.size_human + '</strong></td>' +
            '<td><div class="su-row-bar"><div class="fill" style="width:' + pct + '%; background:' + color + ';"></div></div></td>' +
            '<td class="text-muted">' + (m.updated_at || '&mdash;') + '</td>' +
        '</tr>';
    }

    function renderChart(data) {
        const labels = data.map(m => m.label);
        const series = data.map(m => m.size_bytes);
        const options = {
            chart: { type: 'donut', height: 260 },
            labels: labels,
            series: series,
            colors: palette,
            legend: { position: 'bottom', fontSize: '12px' },
            dataLabels: { enabled: false },
            tooltip: { y: { formatter: function (val) { return formatBytes(val); } } },
        };

        if (donutChart) { donutChart.updateOptions(options); return; }
        donutChart = new ApexCharts(document.querySelector('#su-donut'), options);
        donutChart.render();
    }

    function formatBytes(bytes) {
        if (!bytes || bytes <= 0) { return '0 B'; }
        const units = ['B', 'KB', 'MB', 'GB', 'TB'];
        const power = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
        return (bytes / Math.pow(1024, power)).toFixed(power === 0 ? 0 : 2) + ' ' + units[power];
    }

    function applySummary(summary) {
        $('#su-total, #su-legend-total').text(summary.total_human);
        $('#su-used, #su-legend-used').text(summary.used_human);
        $('#su-free, #su-legend-free').text(summary.free_human);
        $('#su-percent').text(summary.usage_percent + '%');
        $('#su-bar-used').css('width', summary.usage_percent + '%');
        $('#su-bar-free').css('width', (100 - summary.usage_percent) + '%');
        $('#su-last-calculated').text(summary.last_calculated_at || 'Never');
    }

    function loadData() {
        $('#su-loading').removeClass('d-none');
        $.get("{{ route('admin.settings.storage_usage.json') }}", { search: state.search, sort: state.sort })
            .done(function (res) {
                applySummary(res.summary);

                const rows = res.data;
                $('#su-table-body').empty();
                const maxSize = rows.reduce((max, m) => Math.max(max, m.size_bytes), 0);
                rows.forEach(function (m, i) { $('#su-table-body').append(rowHtml(m, i, maxSize)); });
                $('#su-empty').toggleClass('d-none', rows.length > 0);

                // Chart always reflects the unfiltered breakdown by size.
                if (!state.search) {
                    renderChart([...rows].sort((a, b) => b.size_bytes - a.size_bytes));
                }
            })
            .always(function () { $('#su-loading').addClass('d-none'); });
    }

    $('#su-search').on('keyup', debounce(function () { state.search = $('#su-search').val(); loadData(); }, 400));
    $('#su-sort').on('change', function () { state.sort = $(this).val(); loadData(); });

    let polling = null;
    function pollStatus() {
        $.get("{{ route('admin.settings.storage_usage.status') }}").done(function (res) {
            $('#su-running-note').toggleClass('d-none', !res.running);
            $('#su-refresh-btn').prop('disabled', res.running);
            $('#su-spin').toggleClass('d-none', !res.running);
            $('#su-refresh-icon').toggleClass('d-none', res.running);

            if (!res.running && polling) {
                clearInterval(polling);
                polling = null;
                loadData();
            }
        });
    }

    $('#su-refresh-btn').on('click', function () {
        $.post("{{ route('admin.settings.storage_usage.recalculate') }}", { _token: '{{ csrf_token() }}' })
            .done(function (res) {
                toastr['info'](res.message, 'Storage Usage');
                $('#su-running-note').removeClass('d-none');
                $('#su-refresh-btn').prop('disabled', true);
                $('#su-spin').removeClass('d-none');
                $('#su-refresh-icon').addClass('d-none');
                if (!polling) { polling = setInterval(pollStatus, 5000); }
            })
            .fail(function (xhr) {
                toastr['error'](xhr.responseJSON?.message || 'Failed to start recalculation.', 'Error');
            });
    });

    // Light auto-refresh so the dashboard reflects the scheduled background
    // recalculation (and any other admin's manual refresh) without a full
    // page reload - reads cached stats only, never triggers a scan itself.
    setInterval(loadData, 30000);

    loadData();
    @if($isRunning)
        polling = setInterval(pollStatus, 5000);
    @endif
})();
</script>
@endsection
