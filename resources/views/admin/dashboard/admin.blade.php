@extends('layout.admin.admin_layout')

@section('title','Dashboard')

@section('page-style')
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
<style>
  .kpi-value { font-size: 1.6rem; font-weight: 600; line-height: 1.2; }
  .kpi-card .card-body { padding: 1rem 1.25rem; }
  .section-title { margin: 2rem 0 .75rem; }
  .chart-card .card-body { padding: 1rem; }
  .rank-row { display:flex; align-items:center; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid #eee; }
  .rank-row:last-child { border-bottom:none; }
  .rank-badge { width:1.75rem; height:1.75rem; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-weight:600; font-size:.8rem; }
  #workReportTable th { cursor:pointer; white-space:nowrap; }
  #workReportTable th .ti { font-size:.8rem; opacity:.5; }
  .skeleton { background:linear-gradient(90deg,#f0f0f0 25%,#f7f7f7 37%,#f0f0f0 63%); background-size:400% 100%; animation:sk 1.4s ease infinite; border-radius:4px; height:1.4rem; }
  @keyframes sk { 0%{background-position:100% 50%} 100%{background-position:0 50%} }
</style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
      <h4 class="mb-0">CRM Performance Dashboard</h4>
      <small class="text-muted">Organization-wide overview — who's working, how much, and what needs attention.</small>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap" id="dateRangeBar">
      <select id="staffFilter" class="form-select form-select-sm">
        <option value="">All Staff</option>
        @foreach($staffList as $s)
          <option value="{{ $s->id }}">{{ $s->name }}</option>
        @endforeach
      </select>
      <div class="btn-group btn-group-sm" role="group">
        <button type="button" class="btn btn-outline-primary range-btn" data-range="today">Today</button>
        <button type="button" class="btn btn-outline-primary range-btn" data-range="yesterday">Yesterday</button>
        <button type="button" class="btn btn-outline-primary range-btn" data-range="this_week">This Week</button>
        <button type="button" class="btn btn-outline-primary range-btn active" data-range="this_month">This Month</button>
        <button type="button" class="btn btn-outline-primary range-btn" data-range="this_year">This Year</button>
      </div>
      <input type="text" id="customRangeInput" class="form-control form-control-sm" style="width:220px" placeholder="Custom range" />
      <button type="button" id="clearRangeBtn" class="btn btn-outline-secondary btn-sm" title="Clear custom range"><i class="ti ti-x ti-xs"></i> Clear</button>
    </div>
  </div>

  {{-- ===================== LIVE SNAPSHOT (current state; reacts to Staff filter, not Date) ===================== --}}
  <h6 class="text-uppercase text-muted section-title mt-0">Live Snapshot <small class="normal-case" id="snapshotScopeLabel">(current, all staff)</small></h6>
  <div class="row g-3 mb-2" id="snapshotCards">
    @php
      $snapshot = [
        ['Total Leads', 'lead.total_assigned', $leadKpis['total_assigned'], 'ti-users', 'primary', route('admin.leads.list'), null],
        ['Follow-ups Due / Overdue', 'lead.needs_followup', $leadKpis['needs_followup'], 'ti-clock', 'warning', route('admin.leads.list'), ['by_is_qualified' => [1,2]]],
        ['Unworked Leads', 'lead.unworked', $leadKpis['unworked'], 'ti-user-exclamation', 'danger', route('admin.leads.list'), ['by_is_qualified' => ['null']]],
        ['Pending Todos', 'todo.pending', $todoKpis['pending'], 'ti-list-details', 'info', route('admin.todo.list'), null],
        ['Overdue Todos', 'todo.overdue', $todoKpis['overdue'], 'ti-alert-circle', 'danger', route('admin.todo.list'), null],
        ['Active Deals', 'deal.active_deals', $dealKpis['active_deals'], 'ti-briefcase', 'primary', route('admin.dealPipeline.list'), null],
        ['Stale Deals', 'deal.stale_deals', $dealKpis['stale_deals'], 'ti-clock-off', 'danger', route('admin.dealPipeline.list'), null],
        ['Pipeline Value', 'deal.pipeline_value', number_format($dealKpis['pipeline_value']), 'ti-currency-dollar', 'success', route('admin.dealPipeline.list'), null],
        ['Total Contacts', 'contact.total_contacts', number_format($contactKpis['total_contacts']), 'ti-address-book', 'info', route('admin.allcontact.list'), null],
      ];
    @endphp
    @foreach($snapshot as [$label, $kpiKey, $value, $icon, $color, $link, $leadFilter])
      <div class="col-6 col-md-3">
        @if($leadFilter)
          <div class="card kpi-card clickable h-100" data-go-leads='@json($leadFilter)'>
            <div class="card-body">
              <span class="badge bg-label-{{ $color }} rounded-pill p-2 mb-2"><i class="ti {{ $icon }} ti-sm"></i></span>
              <div class="kpi-value" data-kpi="{{ $kpiKey }}">{{ $value }}</div>
              <small class="text-muted">{{ $label }}</small>
            </div>
          </div>
        @else
          <a href="{{ $link }}" class="text-decoration-none text-body">
            <div class="card kpi-card h-100"><div class="card-body">
              <span class="badge bg-label-{{ $color }} rounded-pill p-2 mb-2"><i class="ti {{ $icon }} ti-sm"></i></span>
              <div class="kpi-value" data-kpi="{{ $kpiKey }}">{{ $value }}</div>
              <small class="text-muted">{{ $label }}</small>
            </div></div>
          </a>
        @endif
      </div>
    @endforeach
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card h-100"><div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="mb-0">Follow-up Health</h6>
            <small class="text-muted">Share of open leads within SLA (not overdue)</small>
          </div>
          @php
            $openLeads = $leadKpis['needs_followup'];
            $health = $openLeads > 0 ? round((($openLeads - $leadKpis['overdue_followup']) / $openLeads) * 100) : 100;
            $healthColor = $health >= 80 ? 'success' : ($health >= 50 ? 'warning' : 'danger');
          @endphp
          <h3 class="mb-0 text-{{ $healthColor }}" id="followupHealthValue">{{ $health }}%</h3>
        </div>
        <div class="progress mt-2" style="height:6px"><div class="progress-bar bg-{{ $healthColor }}" id="followupHealthBar" style="width:{{ $health }}%"></div></div>
      </div></div>
    </div>
    <div class="col-md-6">
      <div class="card h-100"><div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="mb-0">Lead Aging</h6>
            <small class="text-muted">Average age of unworked leads</small>
          </div>
          <h3 class="mb-0"><span id="leadAgingValue">{{ $leadKpis['avg_unworked_age_days'] }}</span> <small class="fs-6 text-muted">days</small></h3>
        </div>
      </div></div>
    </div>
  </div>

  {{-- ===================== PERIOD ACTIVITY (date-filtered) ===================== --}}
  <h6 class="text-uppercase text-muted section-title">Period Activity</h6>
  <div class="row g-3 mb-4" id="periodKpis">
    @foreach(['New Leads','Leads Worked','Leads Converted','New Contacts','Calls Made','New Deals','Deals Won','Won Value'] as $label)
      <div class="col-6 col-md-4 col-xl-2">
        <div class="card kpi-card h-100"><div class="card-body">
          <div class="kpi-value skeleton" style="width:3rem"></div>
          <small class="text-muted">{{ $label }}</small>
        </div></div>
      </div>
    @endforeach
  </div>

  {{-- ===================== CHARTS ===================== --}}
  <h6 class="text-uppercase text-muted section-title">Charts</h6>
  <div class="row g-4 mb-4">
    <div class="col-lg-8">
      <div class="card chart-card h-100">
        <div class="card-header"><h6 class="mb-0">Leads Trend</h6></div>
        <div class="card-body"><div id="chartLeadTrend"></div></div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card chart-card h-100">
        <div class="card-header"><h6 class="mb-0">Lead Status Distribution</h6></div>
        <div class="card-body"><div id="chartLeadStatus"></div></div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card chart-card h-100">
        <div class="card-header"><h6 class="mb-0">Todo: Created vs Completed</h6></div>
        <div class="card-body"><div id="chartTodoTrend"></div></div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card chart-card h-100">
        <div class="card-header"><h6 class="mb-0">Deal Stage Distribution</h6></div>
        <div class="card-body"><div id="chartDealStages"></div></div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card chart-card h-100">
        <div class="card-header"><h6 class="mb-0">Won vs Lost Trend</h6></div>
        <div class="card-body"><div id="chartWonLost"></div></div>
      </div>
    </div>
  </div>

  {{-- ===================== DEAL PIPELINE MOVEMENT ===================== --}}
  <h6 class="text-uppercase text-muted section-title">Deal Pipeline Movement <small class="normal-case text-muted">— from real stage-change history, not the current stage column</small></h6>
  <div class="card mb-4">
    <div class="card-body table-responsive">
      <table class="table table-hover" id="pipelineMovementTable">
        <thead>
          <tr>
            <th>Stage</th>
            <th class="text-end">Current Count</th>
            <th class="text-end">Current Value</th>
            <th class="text-end">Movement In</th>
            <th class="text-end">Movement Out</th>
          </tr>
        </thead>
        <tbody id="pipelineMovementBody">
          <tr><td colspan="5" class="text-center text-muted py-4">Loading…</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  {{-- ===================== ACTIVITY SUMMARY ===================== --}}
  <h6 class="text-uppercase text-muted section-title">Activity Summary <small class="normal-case text-muted" id="activityScopeLabel">(current scope, this period)</small></h6>
  <div class="row g-3 mb-4" id="activitySummaryCards">
    @foreach(['Leads Created','Leads Updated','Calls Made','Notes Added','Deals Created','Deals Moved','Deals Closed','Tasks Completed'] as $label)
      <div class="col-6 col-md-3">
        <div class="card kpi-card h-100"><div class="card-body">
          <div class="kpi-value skeleton" style="width:3rem"></div>
          <small class="text-muted">{{ $label }}</small>
        </div></div>
      </div>
    @endforeach
  </div>

  {{-- ===================== STAFF PERFORMANCE RANKING ===================== --}}
  <h6 class="text-uppercase text-muted section-title">Staff Performance Ranking</h6>
  <div class="card mb-4">
    <div class="card-body">
      <div id="rankingList"><div class="skeleton mb-2" style="height:2rem"></div></div>
    </div>
  </div>

  {{-- ===================== UNIFIED WORK REPORT ===================== --}}
  <h6 class="text-uppercase text-muted section-title">Staff Work Report <small class="normal-case text-muted">— Leads + Tasks + Deals combined</small></h6>
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="btn-group btn-group-sm" role="group">
        <button type="button" class="btn btn-outline-primary report-view-btn active" data-view="table">
          <i class="ti ti-table ti-xs"></i> Table
        </button>
        <button type="button" class="btn btn-outline-primary report-view-btn" data-view="chart">
          <i class="ti ti-chart-bar ti-xs"></i> Chart
        </button>
        <button type="button" class="btn btn-outline-primary report-view-btn" data-view="comparison">
          <i class="ti ti-chart-infographic ti-xs"></i> Comparison
        </button>
      </div>
      <div class="btn-group btn-group-sm" id="reportMetricToggle" style="display:none">
        <button type="button" class="btn btn-outline-secondary report-metric-btn active" data-metric="leads_worked">Leads</button>
        <button type="button" class="btn btn-outline-secondary report-metric-btn" data-metric="tasks_completed">Tasks</button>
        <button type="button" class="btn btn-outline-secondary report-metric-btn" data-metric="deals_handled">Deals</button>
        <button type="button" class="btn btn-outline-secondary report-metric-btn" data-metric="total_activity">Total Activity</button>
      </div>
    </div>
    <div class="card-body">
      <div id="reportTableView" class="table-responsive">
        <table class="table table-hover" id="workReportTable">
          <thead>
            <tr>
              <th data-key="name">Staff <i class="ti ti-arrows-sort"></i></th>
              <th data-key="leads_assigned" class="text-end">Total Lead Assigned <i class="ti ti-arrows-sort"></i></th>
              <th data-key="leads_worked" class="text-end">Leads Worked <i class="ti ti-arrows-sort"></i></th>
              <th data-key="leads_converted" class="text-end">Leads Converted <i class="ti ti-arrows-sort"></i></th>
              <th data-key="tasks_completed" class="text-end">Tasks Completed <i class="ti ti-arrows-sort"></i></th>
              <th data-key="deals_handled" class="text-end">Deals Worked <i class="ti ti-arrows-sort"></i></th>
              <th data-key="deals_won" class="text-end">Deals Won <i class="ti ti-arrows-sort"></i></th>
              <th data-key="total_activity" class="text-end">Total Activity <i class="ti ti-arrows-sort"></i></th>
            </tr>
          </thead>
          <tbody id="workReportBody">
            <tr><td colspan="8" class="text-center text-muted py-4">Loading…</td></tr>
          </tbody>
        </table>
      </div>
      <div id="reportChartView" style="display:none">
        <div id="chartWorkReportMetric"></div>
      </div>
      <div id="reportComparisonView" style="display:none">
        <small class="text-muted d-block mb-2">Click a bar to open that staff member's filtered records in the relevant module.</small>
        <div id="chartWorkReportComparison"></div>
      </div>
    </div>
  </div>

</div>
@endsection

@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
<script>
(function () {
  const urls = {
    summary: '{{ route('admin.dashboard.adminSummary') }}',
    performance: '{{ route('admin.dashboard.staffPerformance') }}',
    pipelineMovement: '{{ route('admin.dashboard.pipelineMovement') }}',
    activitySummary: '{{ route('admin.dashboard.activitySummary') }}',
    leadsSaveFilter: '{{ route('admin.leads.saveFilter') }}',
    leadsList: '{{ route('admin.leads.list') }}',
    todoSaveFilter: '{{ route('admin.todo.savefilter') }}',
    todoList: '{{ route('admin.todo.list') }}',
    dealsSaveFilter: '{{ route('admin.dealPipeline.saveFilter') }}',
    dealsSwitchTo: '{{ route('admin.dealPipeline.switchto') }}',
    dealsList: '{{ route('admin.dealPipeline.list') }}',
  };
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  let currentRange = 'this_month', customFrom = null, customTo = null;
  let currentStaffId = '';
  let charts = {};
  let reportRows = [];
  let dealStageIds = [];
  let sortKey = 'total_activity', sortDir = 'desc';

  function money(n) { return Number(n || 0).toLocaleString(undefined, { maximumFractionDigits: 0 }); }

  function fetchJson(url, params) {
    const q = new URLSearchParams(params).toString();
    return fetch(url + '?' + q, { headers: { 'Accept': 'application/json' } }).then(r => r.json());
  }

  function postJson(url, body) {
    return fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
      body: JSON.stringify(body || {}),
    });
  }

  // KPI cards -> save the equivalent filter on the Leads module, then open it filtered.
  // If a Staff is selected on the dashboard, carry it into the Leads filter too.
  document.querySelectorAll('[data-go-leads]').forEach(function (card) {
    card.style.cursor = 'pointer';
    card.addEventListener('click', function () {
      const filters = JSON.parse(card.getAttribute('data-go-leads') || '{}');
      if (currentStaffId) filters.by_assignee = [currentStaffId];
      postJson(urls.leadsSaveFilter, filters).finally(function () { window.location = urls.leadsList; });
    });
  });

  // Deal stage chart bar click -> filter the pipeline to that stage (list view).
  function goToDealsStage(stageId) {
    postJson(urls.dealsSaveFilter, { deal_stage_id: [stageId] })
      .then(function () { return postJson(urls.dealsSwitchTo, { switch: 'deallist' }); })
      .finally(function () { window.location = urls.dealsList; });
  }

  // Common filter-state resolver — every widget's AJAX call goes through
  // this one function, so Staff + Date always stay in sync everywhere.
  function rangeParams() {
    const params = (currentRange === 'custom' && customFrom && customTo)
      ? { range: 'custom', from: customFrom, to: customTo }
      : { range: currentRange };
    if (currentStaffId) params.staff_id = currentStaffId;
    return params;
  }

  function upsertChart(el, options) {
    if (charts[el]) { charts[el].updateOptions(options); return; }
    charts[el] = new ApexCharts(document.querySelector('#' + el), options);
    charts[el].render();
  }

  // Updates the "Live Snapshot" cards + Follow-up Health / Lead Aging
  // in place from the same adminSummary payload the charts use — no
  // separate endpoint, no page flash, reacts to the Staff filter.
  function renderSnapshot(data) {
    const l = data.lead_kpis || {}, t = data.todo_kpis || {}, d = data.deal_kpis || {}, c = data.contact_kpis || {};
    const values = {
      'lead.total_assigned': l.total_assigned, 'lead.needs_followup': l.needs_followup, 'lead.unworked': l.unworked,
      'todo.pending': t.pending, 'todo.overdue': t.overdue,
      'deal.active_deals': d.active_deals, 'deal.stale_deals': d.stale_deals,
      'deal.pipeline_value': money(d.pipeline_value),
      'contact.total_contacts': money(c.total_contacts),
    };
    Object.keys(values).forEach(function (key) {
      const el = document.querySelector('[data-kpi="' + key + '"]');
      if (el && values[key] !== undefined) el.textContent = values[key];
    });

    const openLeads = l.needs_followup || 0;
    const health = openLeads > 0 ? Math.round(((openLeads - (l.overdue_followup || 0)) / openLeads) * 100) : 100;
    const healthColor = health >= 80 ? 'success' : (health >= 50 ? 'warning' : 'danger');
    const healthEl = document.getElementById('followupHealthValue');
    healthEl.textContent = health + '%';
    healthEl.className = 'mb-0 text-' + healthColor;
    const healthBar = document.getElementById('followupHealthBar');
    healthBar.style.width = health + '%';
    healthBar.className = 'progress-bar bg-' + healthColor;

    document.getElementById('leadAgingValue').textContent = l.avg_unworked_age_days ?? 0;

    const opt = document.querySelector('#staffFilter option[value="' + (currentStaffId || '') + '"]');
    document.getElementById('snapshotScopeLabel').textContent = currentStaffId && opt
      ? '(current — ' + opt.textContent + ')'
      : '(current, all staff)';
  }

  function sumValues(obj) { return Object.values(obj || {}).reduce((a, b) => a + Number(b), 0); }

  function renderPeriodKpis(data) {
    const wrap = document.getElementById('periodKpis');
    const newLeads = sumValues(data.lead_trend);
    // Sourced from stage_histories — leads that actually BECAME Qualified in
    // this period, not (a different question) leads currently Qualified
    // that happen to have been created in this period.
    const converted = sumValues(data.lead_qualified_trend);
    const newContacts = sumValues(data.contact_trend);
    const callsMade = sumValues(data.calls_made_trend);
    const newDeals = sumValues(data.deal_trend);
    const dealWon = sumValues((data.deal_won_lost_trend || {}).won);
    const dealLost = sumValues((data.deal_won_lost_trend || {}).lost);
    const todoCompleted = sumValues((data.todo_trend || {}).completed);

    wrap.innerHTML = '';
    const cards = [
      ['New Leads', newLeads],
      ['Leads Converted', converted],
      ['New Contacts', newContacts],
      ['Calls Made', callsMade],
      ['New Deals', newDeals],
      ['Tasks Completed', todoCompleted],
      ['Deals Won', dealWon],
      ['Deals Lost', dealLost],
    ];
    cards.forEach(function ([label, value]) {
      wrap.insertAdjacentHTML('beforeend',
        '<div class="col-6 col-md-4 col-xl-2"><div class="card kpi-card h-100"><div class="card-body">' +
        '<div class="kpi-value">' + money(value) + '</div><small class="text-muted">' + label + '</small></div></div></div>');
    });
  }

  function renderPipelineMovement(stages) {
    const body = document.getElementById('pipelineMovementBody');
    if (!stages || !stages.length) {
      body.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No pipeline data.</td></tr>';
      return;
    }
    body.innerHTML = stages.map(function (s) {
      const badge = s.is_closed ? (s.name.indexOf('Won') !== -1 ? 'success' : 'secondary') : 'primary';
      return '<tr><td><span class="badge bg-label-' + badge + '">' + s.name + '</span></td>' +
        '<td class="text-end">' + money(s.current_count) + '</td>' +
        '<td class="text-end">' + money(s.current_value) + '</td>' +
        '<td class="text-end text-success">' + (s.movement_in ? '+' + money(s.movement_in) : '0') + '</td>' +
        '<td class="text-end text-danger">' + (s.movement_out ? '-' + money(s.movement_out) : '0') + '</td></tr>';
    }).join('');
  }

  function renderActivitySummary(summary) {
    const wrap = document.getElementById('activitySummaryCards');
    const cards = [
      ['Leads Created', summary.leads_created],
      ['Leads Updated', summary.leads_updated],
      ['Calls Made', summary.calls_made],
      ['Notes Added', summary.notes_added],
      ['Deals Created', summary.deals_created],
      ['Deals Moved', summary.deals_moved],
      ['Deals Closed', summary.deals_closed],
      ['Tasks Completed', summary.tasks_completed],
    ];
    wrap.innerHTML = cards.map(function ([label, value]) {
      return '<div class="col-6 col-md-3"><div class="card kpi-card h-100"><div class="card-body">' +
        '<div class="kpi-value">' + money(value) + '</div><small class="text-muted">' + label + '</small></div></div></div>';
    }).join('');

    const opt = document.querySelector('#staffFilter option[value="' + (currentStaffId || '') + '"]');
    document.getElementById('activityScopeLabel').textContent = currentStaffId && opt
      ? '(' + opt.textContent + ', this period)'
      : '(all staff, this period)';
  }

  function renderCharts(data) {
    const leadDates = Object.keys(data.lead_trend || {});
    upsertChart('chartLeadTrend', {
      chart: { type: 'area', height: 260, toolbar: { show: false } },
      series: [{ name: 'Leads', data: leadDates.map(d => data.lead_trend[d]) }],
      xaxis: { categories: leadDates },
      dataLabels: { enabled: false },
      colors: ['#696cff'],
    });

    upsertChart('chartLeadStatus', {
      chart: { type: 'donut', height: 260 },
      series: (data.lead_status || []).map(s => s.total),
      labels: (data.lead_status || []).map(s => s.label),
      legend: { position: 'bottom', fontSize: '11px' },
    });

    const todoDates = Array.from(new Set([
      ...Object.keys((data.todo_trend || {}).created || {}),
      ...Object.keys((data.todo_trend || {}).completed || {}),
    ])).sort();
    upsertChart('chartTodoTrend', {
      chart: { type: 'bar', height: 260, toolbar: { show: false } },
      series: [
        { name: 'Created', data: todoDates.map(d => (data.todo_trend.created || {})[d] || 0) },
        { name: 'Completed', data: todoDates.map(d => (data.todo_trend.completed || {})[d] || 0) },
      ],
      xaxis: { categories: todoDates },
      colors: ['#ffab00', '#71dd37'],
    });

    dealStageIds = (data.deal_stages || []).map(s => s.id);
    upsertChart('chartDealStages', {
      chart: {
        type: 'bar', height: 260, toolbar: { show: false },
        events: { dataPointSelection: function (e, chartCtx, cfg) { goToDealsStage(dealStageIds[cfg.dataPointIndex]); } },
      },
      plotOptions: { bar: { horizontal: true, borderRadius: 4, cursor: 'pointer' } },
      series: [{ name: 'Deals', data: (data.deal_stages || []).map(s => s.total) }],
      xaxis: { categories: (data.deal_stages || []).map(s => s.name) },
      colors: ['#03c3ec'],
      tooltip: { y: { formatter: function (v) { return v + ' deals — click to open'; } } },
    });

    const wlDates = Array.from(new Set([
      ...Object.keys((data.deal_won_lost_trend || {}).won || {}),
      ...Object.keys((data.deal_won_lost_trend || {}).lost || {}),
    ])).sort();
    upsertChart('chartWonLost', {
      chart: { type: 'bar', height: 260, toolbar: { show: false } },
      series: [
        { name: 'Won', data: wlDates.map(d => (data.deal_won_lost_trend.won || {})[d] || 0) },
        { name: 'Lost', data: wlDates.map(d => (data.deal_won_lost_trend.lost || {})[d] || 0) },
      ],
      xaxis: { categories: wlDates },
      colors: ['#71dd37', '#ff3e1d'],
    });
  }

  function renderRankingList(rows) {
    const medalColors = ['#ffd700', '#c0c0c0', '#cd7f32'];
    let html = '';
    rows.slice(0, 5).forEach(function (r, i) {
      const bg = medalColors[i] || '#e7e7ff';
      const fg = i < 3 ? '#fff' : '#696cff';
      html += '<div class="rank-row">' +
        '<div class="d-flex align-items-center gap-2">' +
        '<span class="rank-badge" style="background:' + bg + ';color:' + fg + '">' + (i + 1) + '</span>' +
        '<span class="fw-semibold">' + r.name + '</span></div>' +
        '<span class="text-muted">Activity score: <strong>' + r.total_activity + '</strong></span></div>';
    });
    if (!rows.length) html = '<div class="text-muted text-center py-3">No staff activity in this period.</div>';
    document.getElementById('rankingList').innerHTML = html;
  }

  function renderReportTable() {
    const rows = [...reportRows].sort(function (a, b) {
      const va = a[sortKey], vb = b[sortKey];
      const cmp = typeof va === 'string' ? va.localeCompare(vb) : (va - vb);
      return sortDir === 'asc' ? cmp : -cmp;
    });
    const body = document.getElementById('workReportBody');
    if (!rows.length) {
      body.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No activity in this period.</td></tr>';
      return;
    }
    body.innerHTML = rows.map(function (r) {
      return '<tr><td>' + r.name + '</td>' +
        '<td class="text-end">' + r.leads_assigned + '</td>' +
        '<td class="text-end">' + r.leads_worked + '</td>' +
        '<td class="text-end">' + r.leads_converted + '</td>' +
        '<td class="text-end">' + r.tasks_completed + '</td>' +
        '<td class="text-end">' + r.deals_handled + '</td>' +
        '<td class="text-end">' + r.deals_won + '</td>' +
        '<td class="text-end fw-semibold">' + r.total_activity + '</td></tr>';
    }).join('');
  }

  // Chart-value click-through: open the relevant module, filtered to this
  // staff member, via each module's own existing saveFilter endpoint (same
  // pattern already used for the KPI cards / deal-stage chart). Total
  // Activity is a composite of all three so it has no single destination.
  function goToModuleForStaff(metricKey, staffId) {
    if (metricKey === 'leads_worked') {
      postJson(urls.leadsSaveFilter, { by_assignee: [staffId] }).finally(function () { window.location = urls.leadsList; });
    } else if (metricKey === 'tasks_completed') {
      postJson(urls.todoSaveFilter, { assignto_id: [staffId] }).finally(function () { window.location = urls.todoList; });
    } else if (metricKey === 'deals_handled') {
      postJson(urls.dealsSaveFilter, { care_of: [staffId] })
        .then(function () { return postJson(urls.dealsSwitchTo, { switch: 'deallist' }); })
        .finally(function () { window.location = urls.dealsList; });
    }
  }

  const reportMetricLabels = { leads_worked: 'Leads Worked', tasks_completed: 'Tasks Completed', deals_handled: 'Deals Worked', total_activity: 'Total Activity' };
  let reportChartStaffIds = [];
  let comparisonStaffIds = [];

  function renderReportChart() {
    const rows = [...reportRows].sort(function (a, b) { return b[currentReportMetric] - a[currentReportMetric]; });
    reportChartStaffIds = rows.map(r => r.admin_id);
    const clickable = currentReportMetric !== 'total_activity';
    upsertChart('chartWorkReportMetric', {
      chart: {
        type: 'bar', height: Math.max(280, rows.length * 32), toolbar: { show: false },
        events: { dataPointSelection: function (e, ctx, cfg) {
          if (clickable) goToModuleForStaff(currentReportMetric, reportChartStaffIds[cfg.dataPointIndex]);
        } },
      },
      plotOptions: { bar: { horizontal: true, borderRadius: 4, cursor: clickable ? 'pointer' : 'default' } },
      series: [{ name: reportMetricLabels[currentReportMetric], data: rows.map(r => r[currentReportMetric]) }],
      xaxis: { categories: rows.map(r => r.name) },
      colors: ['#696cff'],
      tooltip: clickable ? { y: { formatter: function (v) { return v + ' — click to open'; } } } : {},
    });
    if (!rows.length) document.getElementById('chartWorkReportMetric').innerHTML = '<div class="text-muted text-center py-4">No staff activity in this period.</div>';
  }

  function renderReportComparison() {
    const rows = [...reportRows].sort(function (a, b) { return b.total_activity - a.total_activity; });
    comparisonStaffIds = rows.map(r => r.admin_id);
    const metricBySeries = ['leads_worked', 'tasks_completed', 'deals_handled'];
    upsertChart('chartWorkReportComparison', {
      chart: {
        type: 'bar', height: Math.max(300, rows.length * 40), toolbar: { show: false },
        events: { dataPointSelection: function (e, ctx, cfg) {
          goToModuleForStaff(metricBySeries[cfg.seriesIndex], comparisonStaffIds[cfg.dataPointIndex]);
        } },
      },
      plotOptions: { bar: { horizontal: true, borderRadius: 3, cursor: 'pointer' } },
      series: [
        { name: 'Leads', data: rows.map(r => r.leads_worked) },
        { name: 'Tasks', data: rows.map(r => r.tasks_completed) },
        { name: 'Deals', data: rows.map(r => r.deals_handled) },
      ],
      xaxis: { categories: rows.map(r => r.name) },
      colors: ['#696cff', '#ffab00', '#03c3ec'],
      legend: { position: 'top', fontSize: '11px' },
    });
    if (!rows.length) document.getElementById('chartWorkReportComparison').innerHTML = '<div class="text-muted text-center py-4">No staff activity in this period.</div>';
  }

  // Switching Table/Chart/Comparison is purely client-side — reportRows is
  // already in memory from the last staff-performance fetch, so this never
  // triggers a network request.
  let currentReportView = 'table';
  let currentReportMetric = 'leads_worked';

  function renderReportView() {
    if (currentReportView === 'table') renderReportTable();
    else if (currentReportView === 'chart') renderReportChart();
    else renderReportComparison();
  }

  document.querySelectorAll('.report-view-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.report-view-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentReportView = btn.getAttribute('data-view');
      document.getElementById('reportTableView').style.display = currentReportView === 'table' ? '' : 'none';
      document.getElementById('reportChartView').style.display = currentReportView === 'chart' ? '' : 'none';
      document.getElementById('reportComparisonView').style.display = currentReportView === 'comparison' ? '' : 'none';
      document.getElementById('reportMetricToggle').style.display = currentReportView === 'chart' ? '' : 'none';
      renderReportView();
    });
  });

  document.querySelectorAll('.report-metric-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.report-metric-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentReportMetric = btn.getAttribute('data-metric');
      renderReportChart();
    });
  });

  document.querySelectorAll('#workReportTable th[data-key]').forEach(function (th) {
    th.addEventListener('click', function () {
      const key = th.getAttribute('data-key');
      sortDir = (sortKey === key && sortDir === 'desc') ? 'asc' : 'desc';
      sortKey = key;
      renderReportTable();
    });
  });

  function loadAll() {
    const params = rangeParams();
    fetchJson(urls.summary, params).then(function (data) {
      renderSnapshot(data);
      renderPeriodKpis(data);
      renderCharts(data);
    });
    fetchJson(urls.performance, params).then(function (data) {
      reportRows = data.ranking || [];
      renderRankingList(reportRows);
      renderReportView();
    });
    fetchJson(urls.pipelineMovement, params).then(function (data) {
      renderPipelineMovement(data.stages || []);
    });
    fetchJson(urls.activitySummary, params).then(function (data) {
      renderActivitySummary(data.summary || {});
    });
  }

  document.querySelectorAll('.range-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.range-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentRange = btn.getAttribute('data-range');
      loadAll();
    });
  });

  // Select2: single-select with search, matching the pattern used
  // elsewhere in the admin panel (e.g. leads filters).
  $('#staffFilter').select2({ width: '220px' });
  $('#staffFilter').on('change', function () {
    currentStaffId = $(this).val();
    loadAll();
  });

  function toLocalYmd(d) {
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
  }

  let customRangePicker = null;
  if (window.flatpickr) {
    customRangePicker = flatpickr('#customRangeInput', {
      mode: 'range',
      dateFormat: 'Y-m-d',
      onClose: function (selectedDates) {
        if (selectedDates.length === 2) {
          document.querySelectorAll('.range-btn').forEach(b => b.classList.remove('active'));
          currentRange = 'custom';
          customFrom = toLocalYmd(selectedDates[0]);
          customTo = toLocalYmd(selectedDates[1]);
          loadAll();
        }
      },
    });
  }

  // Clear resets the whole filter bar — custom range AND Staff — back to
  // the defaults (This Month / All Staff), then reloads once.
  document.getElementById('clearRangeBtn').addEventListener('click', function () {
    if (customRangePicker) customRangePicker.clear(false);
    customFrom = null;
    customTo = null;
    currentRange = 'this_month';
    document.querySelectorAll('.range-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.range-btn[data-range="this_month"]').classList.add('active');
    // .trigger('change') fires the #staffFilter change handler above, which
    // sets currentStaffId and calls loadAll() — so the range reset above
    // takes effect together with the staff reset in one reload, not two.
    $('#staffFilter').val('').trigger('change');
  });

  loadAll();
})();
</script>
@endsection
