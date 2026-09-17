@extends('layout.admin.admin_layout')

@section('title','Dashboard')

@section('page-style')
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<style>
  .kpi-card { transition: box-shadow .15s ease; }
  .kpi-card.clickable { cursor: pointer; }
  .kpi-card.clickable:hover { box-shadow: 0 .25rem .75rem rgba(0,0,0,.08); }
  .kpi-value { font-size: 1.6rem; font-weight: 600; line-height: 1.2; }
  .action-list-item { border-left: 3px solid transparent; }
  .action-list-item.urgent { border-left-color: #ff3e1d; }
  .action-list-item.warn { border-left-color: #ffab00; }
  .action-list-item.info { border-left-color: #03c3ec; }
  .action-empty { padding: 2rem 1rem; text-align: center; color: #8592a3; }
  .skeleton { background:linear-gradient(90deg,#f0f0f0 25%,#f7f7f7 37%,#f0f0f0 63%); background-size:400% 100%; animation:sk 1.4s ease infinite; border-radius:4px; height:1.4rem; }
  @keyframes sk { 0%{background-position:100% 50%} 100%{background-position:0 50%} }
  .add-attendance-btn { padding: .55rem 1.35rem; font-weight: 500; color: #fff; transition: transform .15s ease, box-shadow .15s ease; }
  .add-attendance-btn:hover, .add-attendance-btn:focus { color: #fff; transform: translateY(-1px); box-shadow: 0 .4rem .85rem rgba(var(--bs-primary-rgb), .35); }
</style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
      @php
        $hour = now()->hour;
        $greeting = match(true) {
          $hour >= 5 && $hour < 12 => 'Good Morning',
          $hour >= 12 && $hour < 17 => 'Good Afternoon',
          $hour >= 17 && $hour < 21 => 'Good Evening',
          default => 'Good Night',
        };
      @endphp
      <h4 class="mb-0">{{ $greeting }}, {{ explode(' ', $user->name)[0] }}</h4>
      <small class="text-muted">Here is what needs your attention right now.</small>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap" id="dateRangeBar">
      <a href="{{ route('admin.attendance.top') }}" class="btn btn-primary rounded-pill add-attendance-btn"><i class="ti ti-calendar-stats"></i> Add Attendance</a>
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

  {{-- ===================== MY ACTIVITY (period, date-filtered) ===================== --}}
  <div class="row g-3 mb-4" id="myActivityKpis">
    @foreach(['New Leads','Leads Worked','Leads Converted','Tasks Completed','Deals Won'] as $label)
      <div class="col-6 col-md-4 col-xl-2">
        <div class="card kpi-card h-100"><div class="card-body">
          <div class="kpi-value skeleton" style="width:3rem"></div>
          <small class="text-muted">{{ $label }}</small>
        </div></div>
      </div>
    @endforeach
  </div>

  {{-- ===================== TODAY'S WORK / ACTION CENTER ===================== --}}
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="ti ti-bolt text-warning me-1"></i> Today's Work — Action Center</h5>
    </div>
    <div class="card-body">
      <div class="row g-4">

        {{-- LEADS --}}
        @if($showLeads)
        <div class="col-md-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0"><i class="ti ti-user-plus me-1"></i> Leads needing action</h6>
            <a href="{{ route('admin.leads.list') }}" class="small">View all &raquo;</a>
          </div>
          @if($actionCenter['leads']->isEmpty())
            <div class="action-empty"><i class="ti ti-circle-check ti-lg mb-2 d-block text-success"></i>No leads need action. Great job!</div>
          @else
            <ul class="list-unstyled">
              @foreach($actionCenter['leads'] as $lead)
                @php
                  $urgency = is_null($lead->is_qualified) ? 'urgent' : ($lead->is_reassigned ? 'warn' : 'info');
                  $reason = is_null($lead->is_qualified) ? 'Not yet contacted' : ($lead->is_reassigned ? 'Reassigned to you' : 'Follow-up overdue');
                @endphp
                <li class="action-list-item {{ $urgency }} ps-2 py-2 mb-1">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="fw-semibold">{{ $lead->cand_name ?: 'Unnamed lead' }}</div>
                      <small class="text-muted">{{ $reason }} &middot; {{ \Carbon\Carbon::parse($lead->lead_date)->diffForHumans() }}</small>
                    </div>
                    <div class="text-end text-nowrap">
                      @if($lead->mob_no)
                        <a href="tel:{{ $lead->mob_no }}" class="btn btn-icon btn-sm btn-outline-success" title="Call"><i class="ti ti-phone ti-sm"></i></a>
                      @endif
                      <a href="{{ route('admin.leads.list.view', $lead->id) }}" class="btn btn-icon btn-sm btn-outline-primary" title="View / Follow-up"><i class="ti ti-eye ti-sm"></i></a>
                    </div>
                  </div>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
        @endif

        {{-- TODOS --}}
        @if($showTodo)
        <div class="col-md-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0"><i class="ti ti-checklist me-1"></i> Tasks due</h6>
            <a href="{{ route('admin.todo.list') }}" class="small">View all &raquo;</a>
          </div>
          @if($actionCenter['todos']->isEmpty())
            <div class="action-empty"><i class="ti ti-circle-check ti-lg mb-2 d-block text-success"></i>No pending tasks.</div>
          @else
            <ul class="list-unstyled" id="dashTodoList">
              @foreach($actionCenter['todos'] as $todo)
                @php
                  $today = \Carbon\Carbon::today();
                  $finish = $todo->finish_on ? \Carbon\Carbon::parse($todo->finish_on) : null;
                  $urgency = $finish && $finish->lt($today) ? 'urgent' : ($finish && $finish->isSameDay($today) ? 'warn' : 'info');
                  $reason = $finish ? ($finish->lt($today) ? 'Overdue since '.$finish->format('d M') : ($finish->isSameDay($today) ? 'Due today' : 'Due '.$finish->format('d M'))) : 'No due date';
                @endphp
                <li class="action-list-item {{ $urgency }} ps-2 py-2 mb-1" data-todo-row="{{ $todo->id }}">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="fw-semibold">{{ $todo->task_title }}</div>
                      <small class="text-muted">{{ $reason }} &middot; {{ $todo->reminder_cycle ?: 'Normal' }} priority</small>
                    </div>
                    <div class="text-end text-nowrap">
                      <a href="{{ route('admin.todo.list.view', $todo->id) }}" class="btn btn-icon btn-sm btn-outline-primary" title="View"><i class="ti ti-eye ti-sm"></i></a>
                      <button type="button" class="btn btn-icon btn-sm btn-outline-success mark-todo-complete" data-id="{{ $todo->id }}" title="Mark complete"><i class="ti ti-check ti-sm"></i></button>
                    </div>
                  </div>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
        @endif

        {{-- DEALS --}}
        @if($showDeals)
        <div class="col-md-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0"><i class="ti ti-briefcase me-1"></i> Recent Deals</h6>
            <a href="javascript:void(0);" id="dealsViewMoreBtn" class="small">View all &raquo;</a>
          </div>
          @if($actionCenter['deals']->isEmpty())
            <div class="action-empty"><i class="ti ti-briefcase ti-lg mb-2 d-block text-muted"></i>No deals assigned to you yet.</div>
          @else
            <ul class="list-unstyled">
              @foreach($actionCenter['deals'] as $deal)
                <li class="action-list-item info ps-2 py-2 mb-1">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="fw-semibold">{{ $deal->deal_name ?: $deal->name ?: $deal->company ?: ('Deal #'.$deal->id) }}</div>
                      <small class="text-muted">{{ $deal->stage->name ?? 'Stage n/a' }} &middot; Updated {{ \Carbon\Carbon::parse($deal->updated_at)->diffForHumans() }}</small>
                    </div>
                    <a href="{{ route('admin.dealPipeline.list.view', $deal->id) }}" class="btn btn-icon btn-sm btn-outline-primary" title="View"><i class="ti ti-eye ti-sm"></i></a>
                  </div>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
        @endif

      </div>
    </div>
  </div>

  {{-- ===================== LEADS ===================== --}}
  @if($showLeads)
  <h5 class="mb-3"><i class="ti ti-user-plus me-1"></i> My Leads</h5>
  <div class="row g-3 mb-4">
    @php
      $leadCards = [
        ['Total Assigned', $leadKpis['total_assigned'], 'ti-users', 'primary', ['leads', []]],
        ['New / Unworked', $leadKpis['unworked'], 'ti-user-exclamation', 'warning', ['leads', ['by_is_qualified' => ['null']]]],
        ['Without Follow-up', $leadKpis['without_followup'], 'ti-message-off', 'secondary', ['leads', []]],
        ['Needs Follow-up', $leadKpis['needs_followup'], 'ti-clock', 'info', ['leads', ['by_is_qualified' => [1,2]]]],
        ['Overdue Follow-up', $leadKpis['overdue_followup'], 'ti-alert-triangle', 'danger', ['leads', ['by_is_qualified' => [1,2]]]],
        ['Qualified', $leadKpis['qualified'], 'ti-circle-check', 'success', ['leads', ['by_is_qualified' => [3]]]],
      ];
    @endphp
    @foreach($leadCards as [$label, $value, $icon, $color, $link])
      <div class="col-6 col-md-4 col-xl-2">
        <div class="card kpi-card clickable h-100" data-go-leads='@json($link[1])'>
          <div class="card-body">
            <span class="badge bg-label-{{ $color }} rounded-pill p-2 mb-2"><i class="ti {{ $icon }} ti-sm"></i></span>
            <div class="kpi-value">{{ $value }}</div>
            <small class="text-muted">{{ $label }}</small>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  @endif

  {{-- ===================== TODOS ===================== --}}
  @if($showTodo)
  <h5 class="mb-3"><i class="ti ti-checklist me-1"></i> My Tasks</h5>
  <div class="row g-3 mb-4">
    @php
      $todoCards = [
        ['Pending', $todoKpis['pending'], 'ti-list-details', 'primary', null],
        ['Due Today', $todoKpis['due_today'], 'ti-calendar-event', 'warning', 'Due Today'],
        ['Overdue', $todoKpis['overdue'], 'ti-alert-circle', 'danger', null],
        ['Upcoming', $todoKpis['upcoming'], 'ti-calendar-time', 'info', null],
        ['High Priority', $todoKpis['high_priority'], 'ti-flag', 'danger', null],
        ['Completed (all time)', $todoKpis['completed_total'], 'ti-circle-check', 'success', 'Complete'],
      ];
    @endphp
    @foreach($todoCards as [$label, $value, $icon, $color, $status])
      <div class="col-6 col-md-4 col-xl-2">
        <div class="card kpi-card h-100"><div class="card-body">
          <span class="badge bg-label-{{ $color }} rounded-pill p-2 mb-2"><i class="ti {{ $icon }} ti-sm"></i></span>
          <div class="kpi-value">{{ $value }}</div>
          <small class="text-muted">{{ $label }}</small>
        </div></div>
      </div>
    @endforeach
  </div>
  @endif

  {{-- ===================== DEALS ===================== --}}
  @if($showDeals)
  <h5 class="mb-3"><i class="ti ti-briefcase me-1"></i> My Deals</h5>
  <div class="row g-3 mb-4">
    @php
      $dealCards = [
        ['Active Deals', $dealKpis['active_deals'], 'ti-briefcase', 'primary'],
        ['Pipeline Value', number_format($dealKpis['pipeline_value']), 'ti-currency-dollar', 'info'],
        ['Stale (6+ days)', $dealKpis['stale_deals'], 'ti-clock-off', 'danger'],
        ['Won', $dealKpis['won_deals'], 'ti-trophy', 'success'],
        ['Lost', $dealKpis['lost_deals'], 'ti-x', 'secondary'],
        ['Recently Updated', $dealKpis['recently_updated'], 'ti-refresh', 'info'],
      ];
    @endphp
    @foreach($dealCards as [$label, $value, $icon, $color])
      <div class="col-6 col-md-4 col-xl-2">
        <div class="card kpi-card h-100">
          <a href="{{ route('admin.dealPipeline.list') }}" class="text-decoration-none text-body"><div class="card-body">
            <span class="badge bg-label-{{ $color }} rounded-pill p-2 mb-2"><i class="ti {{ $icon }} ti-sm"></i></span>
            <div class="kpi-value">{{ $value }}</div>
            <small class="text-muted">{{ $label }}</small>
          </div></a>
        </div>
      </div>
    @endforeach
  </div>
  @endif

</div>
@endsection

@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script>
(function () {
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  let currentRange = 'this_month', customFrom = null, customTo = null;

  function postJson(url, body) {
    return fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
      body: JSON.stringify(body || {}),
    });
  }

  function money(n) { return Number(n || 0).toLocaleString(undefined, { maximumFractionDigits: 0 }); }

  function rangeParams() {
    return (currentRange === 'custom' && customFrom && customTo)
      ? { range: 'custom', from: customFrom, to: customTo }
      : { range: currentRange };
  }

  // "My Activity" — period-filtered, self-scoped only (no staff_id input
  // exists on this endpoint at all).
  function loadMyActivity() {
    const q = new URLSearchParams(rangeParams()).toString();
    fetch('{{ route('admin.dashboard.staffSummary') }}?' + q, { headers: { 'Accept': 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        const wrap = document.getElementById('myActivityKpis');
        const cards = [
          ['New Leads', data.new_leads],
          ['Leads Worked', data.leads_worked],
          ['Leads Converted', data.leads_converted],
          ['Tasks Completed', data.tasks_completed],
          ['Deals Won', data.deals_won],
        ];
        wrap.innerHTML = '';
        cards.forEach(function ([label, value]) {
          if (value === null || value === undefined) return;
          wrap.insertAdjacentHTML('beforeend',
            '<div class="col-6 col-md-4 col-xl-2"><div class="card kpi-card h-100"><div class="card-body">' +
            '<div class="kpi-value">' + money(value) + '</div><small class="text-muted">' + label + '</small></div></div></div>');
        });
      });
  }

  document.querySelectorAll('.range-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.range-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentRange = btn.getAttribute('data-range');
      loadMyActivity();
    });
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
          loadMyActivity();
        }
      },
    });
  }

  document.getElementById('clearRangeBtn').addEventListener('click', function () {
    if (customRangePicker) customRangePicker.clear(false);
    customFrom = null;
    customTo = null;
    currentRange = 'this_month';
    document.querySelectorAll('.range-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.range-btn[data-range="this_month"]').classList.add('active');
    loadMyActivity();
  });

  loadMyActivity();

  // KPI cards -> save the equivalent filter on the Leads module, then open it filtered.
  document.querySelectorAll('[data-go-leads]').forEach(function (card) {
    card.addEventListener('click', function () {
      const filters = JSON.parse(card.getAttribute('data-go-leads') || '{}');
      postJson('{{ route('admin.leads.saveFilter') }}', filters).finally(function () {
        window.location = '{{ route('admin.leads.list') }}';
      });
    });
  });

  // "View More" -> scope the Deal Pipeline list to this staff's own deals
  // (care_of) via the module's existing saveFilter endpoint, then open it
  // in list view.
  var dealsViewMoreBtn = document.getElementById('dealsViewMoreBtn');
  if (dealsViewMoreBtn) {
    dealsViewMoreBtn.addEventListener('click', function () {
      postJson('{{ route('admin.dealPipeline.saveFilter') }}', { care_of: ['{{ $user->id }}'] })
        .then(function () {
          return postJson('{{ route('admin.dealPipeline.switchto') }}', { switch: 'deallist' });
        })
        .finally(function () {
          window.location = '{{ route('admin.dealPipeline.list') }}';
        });
    });
  }

  // Quick action: mark a task complete straight from the Action Center.
  document.querySelectorAll('.mark-todo-complete').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      const id = btn.getAttribute('data-id');
      btn.disabled = true;
      postJson('{{ route('admin.todo.updateStatus') }}', { id: id, text: 'Mark as Complete' })
        .then(function () {
          const row = document.querySelector('[data-todo-row="' + id + '"]');
          if (row) row.remove();
        })
        .catch(function () { btn.disabled = false; });
    });
  });
})();
</script>
@endsection
