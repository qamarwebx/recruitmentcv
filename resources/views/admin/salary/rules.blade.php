@extends('layout.admin.admin_layout')

@section('title','Salary Calculation Rules')

@section('page-style')
    <style>
        .rule-card { margin-bottom: 1.25rem; }
        .rule-card .card-header h6 { margin-bottom: 0; }
        .rule-formula {
            background: #f5f6f8;
            border: 1px solid #e7e7e7;
            border-radius: .375rem;
            padding: .75rem 1rem;
            font-family: 'Courier New', monospace;
            font-size: .85rem;
            white-space: pre-wrap;
            margin: .5rem 0;
        }
        .rule-live-value {
            display: inline-block;
            background: rgba(105,108,255,.08);
            color: #696cff;
            border-radius: .25rem;
            padding: .1rem .5rem;
            font-weight: 600;
        }
        .rule-example {
            border-left: 3px solid #696cff;
            padding: .5rem .75rem;
            background: rgba(105,108,255,.04);
            margin-top: .5rem;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="mb-0"><i class="ti ti-book me-1"></i> Salary Calculation Rules</h5>
    <a href="{{ route('admin.salary.settings.page') }}" class="btn btn-sm btn-outline-primary">
      <i class="ti ti-settings me-1"></i> Back to Settings
    </a>
  </div>

  <div class="alert alert-info">
    This page explains, in plain language, exactly how every figure on the Salary Dashboard, Payroll, Attendance Slip and
    Salary Slip is calculated. The highlighted values below are your <strong>current live Salary Settings</strong> -
    change them from the Settings page and the numbers on this page (and every calculation in the app) update with them.
    Nothing on this page can be edited here.
  </div>

  {{-- ================= 1. PER DAY SALARY ================= --}}
  <div class="card rule-card">
    <div class="card-header"><h6><i class="ti ti-calendar me-1"></i> 1. Per Day Salary</h6></div>
    <div class="card-body">
      <p>Every deduction in this app (Late, Absent, Half Day, Sunday) is priced per calendar day, never as a fraction of
      the whole month's salary directly.</p>
      <div class="rule-formula">per_day_salary = monthly_salary &divide; days_in_month</div>
      <p class="mb-0 text-muted">"days_in_month" is the real number of calendar days in that month (28-31) - not hardcoded.</p>
    </div>
  </div>

  {{-- ================= 2. ATTENDANCE STATUS WINDOWS ================= --}}
  <div class="card rule-card">
    <div class="card-header"><h6><i class="ti ti-clock me-1"></i> 2. How a Day Gets Classified</h6></div>
    <div class="card-body">
      <p>For every calendar day (that has actually happened - never a future date), the approved Time In is compared
      against these time windows to decide its status:</p>
      <table class="table table-sm table-bordered">
        <thead class="table-light">
          <tr><th>Status</th><th>Condition</th><th>Current Setting</th></tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="badge bg-label-success">Present</span></td>
            <td>Time In at or before Office Start Time</td>
            <td><span class="rule-live-value">{{ substr($settings->office_start_time,0,5) }}</span></td>
          </tr>
          <tr>
            <td><span class="badge bg-label-warning">Qualifying Late</span></td>
            <td>Time In after Office Start, up to Qualifying Late Ends</td>
            <td><span class="rule-live-value">{{ substr($settings->qualifying_late_end_time,0,5) }}</span></td>
          </tr>
          <tr>
            <td><span class="badge bg-label-warning">Late</span> (non-qualifying)</td>
            <td>Time In after that, up to Late Window Ends</td>
            <td><span class="rule-live-value">{{ substr($settings->late_window_end_time,0,5) }}</span></td>
          </tr>
          <tr>
            <td><span class="badge bg-label-danger">Half Day</span></td>
            <td>Time In after Half Day After time (or after the Late Window)</td>
            <td><span class="rule-live-value">{{ substr($settings->half_day_after_time,0,5) }}</span></td>
          </tr>
          <tr>
            <td><span class="badge bg-label-secondary">Absent</span></td>
            <td>No approved Time In for that day at all</td>
            <td>-</td>
          </tr>
          <tr>
            <td><span class="badge bg-label-info">Weekly Off</span></td>
            <td>Every Sunday</td>
            <td>-</td>
          </tr>
          <tr>
            <td><span class="badge bg-label-info">Holiday</span></td>
            <td>Any date listed under Settings &rarr; Holidays</td>
            <td>-</td>
          </tr>
        </tbody>
      </table>
      <p class="mb-0 text-muted">A day only counts as "attended" once its attendance log has BOTH Normal Approval and
      Final Approval - an approval still pending is treated the same as Absent for salary purposes, until it clears.</p>
    </div>
  </div>

  {{-- ================= 3. LATE MARK DEDUCTION ================= --}}
  <div class="card rule-card">
    <div class="card-header"><h6><i class="ti ti-alarm me-1"></i> 3. Late Mark Deduction</h6></div>
    <div class="card-body">
      <p><strong>Total Late Count</strong> = Qualifying Late days + Late (non-qualifying) days added together -
      every late arrival counts toward this total, however late it was.</p>
      <div class="rule-formula">if total_late_count &le; free_late_count:
    deduction_days = 0

if total_late_count &gt; free_late_count:
    deduction_days = total_late_count   (ALL of them, not just the ones over the limit)

total_late_deduction = deduction_days &times; deduction_per_late_day</div>
      <p>Free Qualifying Lates / Month = <span class="rule-live-value">{{ $settings->free_late_count }}</span></p>

      <h6 class="mt-3">Payroll Deduction Calculation - how <code>deduction_per_late_day</code> is priced</h6>
      <p>Configurable from Salary Settings, three ways - all priced off the staff member's <strong>monthly</strong>
      salary, not the daily rate:</p>
      <table class="table table-sm table-bordered mb-3">
        <thead class="table-light"><tr><th>Type</th><th>Formula</th></tr></thead>
        <tbody>
          <tr @if($settings->deduction_calculation_type === 'percentage') class="table-primary" @endif>
            <td>Percentage Based</td>
            <td><code>deduction_per_late_day = monthly_salary &times; (late_deduction_percent &divide; 100)</code></td>
          </tr>
          <tr @if($settings->deduction_calculation_type === 'fixed_slab') class="table-primary" @endif>
            <td>Fixed Slab Based <span class="text-muted">(default)</span></td>
            <td><code>deduction_per_late_day = floor(monthly_salary &divide; slab_amount) &times; deduction_per_slab</code></td>
          </tr>
          <tr @if($settings->deduction_calculation_type === 'fixed_amount') class="table-primary" @endif>
            <td>Fixed Amount</td>
            <td><code>deduction_per_late_day = configured_fixed_amount</code> (flat, ignores salary)</td>
          </tr>
        </tbody>
      </table>

      <p>Current settings (highlighted row above is the active type - only that type's field(s) are actually used):
        Deduction Calculation Type = <span class="rule-live-value">{{ ucwords(str_replace('_', ' ', $settings->deduction_calculation_type)) }}</span>
        @if($settings->deduction_calculation_type === 'percentage')
          , Percentage = <span class="rule-live-value">{{ $settings->late_deduction_percent }}%</span>
        @elseif($settings->deduction_calculation_type === 'fixed_slab')
          , Slab Amount = <span class="rule-live-value">&#8377;{{ number_format($settings->deduction_slab_amount, 2) }}</span>,
          Deduction Per Slab = <span class="rule-live-value">&#8377;{{ number_format($settings->deduction_per_slab, 2) }}</span>
        @else
          , Fixed Amount = <span class="rule-live-value">&#8377;{{ number_format($settings->deduction_per_slab, 2) }}</span>
        @endif
      </p>

      @php
          $exampleSalary = 18000;
          $exampleSlabs = $settings->deduction_slab_amount > 0 ? floor($exampleSalary / $settings->deduction_slab_amount) : 0;
          $examplePerLateDay = match($settings->deduction_calculation_type) {
              'percentage' => round($exampleSalary * ($settings->late_deduction_percent / 100), 2),
              'fixed_amount' => round((float) $settings->deduction_per_slab, 2),
              default => round($exampleSlabs * $settings->deduction_per_slab, 2),
          };
          $exampleLateCount = (int) $settings->free_late_count + 3;
      @endphp
      <div class="rule-example">
        <strong>Example</strong> with the settings above, for a &#8377;{{ number_format($exampleSalary) }}/month staff
        member with {{ $exampleLateCount }} total late marks this month ({{ $settings->free_late_count }} free +
        3 over):
        @if($settings->deduction_calculation_type === 'fixed_slab')
          floor(18000 &divide; {{ number_format($settings->deduction_slab_amount, 0) }}) &times; {{ number_format($settings->deduction_per_slab, 2) }}
          = {{ $exampleSlabs }} slabs &times; &#8377;{{ number_format($settings->deduction_per_slab, 2) }} =
        @elseif($settings->deduction_calculation_type === 'percentage')
          18000 &times; {{ $settings->late_deduction_percent }}% =
        @endif
        <strong>&#8377;{{ number_format($examplePerLateDay, 2) }} per late day</strong>, so
        {{ $exampleLateCount }} &times; &#8377;{{ number_format($examplePerLateDay, 2) }} =
        <strong>&#8377;{{ number_format($exampleLateCount * $examplePerLateDay, 2) }} total late deduction</strong>.
      </div>
      <p class="mt-2 mb-0 text-muted">Only the setting(s) belonging to the currently selected Deduction Calculation
      Type are shown on the Settings page and used in this calculation - a value left over from switching away from
      another type is never read. Everything is fetched fresh from Salary Settings on every calculation, never
      hardcoded, via the shared <code>PayrollDeductionService</code>, so Payroll, Dashboard, Attendance Slip and Salary
      Slip can never disagree.</p>
    </div>
  </div>

  {{-- ================= 4. ABSENT DEDUCTION ================= --}}
  <div class="card rule-card">
    <div class="card-header"><h6><i class="ti ti-user-off me-1"></i> 4. Absent Deduction</h6></div>
    <div class="card-body">
      <div class="rule-formula">absent_deduction = absent_days &times; per_day_salary</div>
      <p class="mb-0 text-muted">Every Absent day is a full day's salary deducted - no free allowance for absences.</p>
    </div>
  </div>

  {{-- ================= 5. HALF DAY DEDUCTION ================= --}}
  <div class="card rule-card">
    <div class="card-header"><h6><i class="ti ti-hourglass me-1"></i> 5. Half Day Deduction</h6></div>
    <div class="card-body">
      <div class="rule-formula">half_day_deduction = half_day_count &times; per_day_salary &times; 0.5</div>
      <p class="mb-0 text-muted">A Half Day (Time In after {{ substr($settings->half_day_after_time,0,5) }}) costs half
      a day's salary.</p>
    </div>
  </div>

  {{-- ================= 6. SUNDAY (WEEKLY OFF) DEDUCTION ================= --}}
  <div class="card rule-card">
    <div class="card-header"><h6><i class="ti ti-calendar-off me-1"></i> 6. Sunday (Weekly Off) Deduction</h6></div>
    <div class="card-body">
      <p>A Sunday is normally unpaid-but-free (Weekly Off). It only gets deducted as a full day's salary when the
      adjoining Saturday and/or Monday was Absent - and each rule is its own on/off switch:</p>
      <ul class="mb-2">
        <li>Saturday Absent &rarr; deducts the following Sunday:
          <span class="rule-live-value">{{ $settings->sat_absent_sunday_deduction ? 'ON' : 'OFF' }}</span>
        </li>
        <li>Monday Absent &rarr; deducts the previous Sunday:
          <span class="rule-live-value">{{ $settings->mon_absent_prev_sunday_deduction ? 'ON' : 'OFF' }}</span>
        </li>
      </ul>
      <div class="rule-formula">sunday_deduction = (number of Sundays flagged by either rule above) &times; per_day_salary</div>
      <p class="mb-0 text-muted">Capped at one day's salary per Sunday, even if both the Saturday and Monday were Absent.</p>
    </div>
  </div>

  {{-- ================= 7. CURRENT MONTH PRORATION ================= --}}
  <div class="card rule-card">
    <div class="card-header"><h6><i class="ti ti-calendar-stats me-1"></i> 7. Current (In-Progress) Month Proration</h6></div>
    <div class="card-body">
      <p>A month that hasn't finished yet is never paid as if it were already complete. Everything below is scoped to
      only the days that have actually elapsed:</p>
      <div class="rule-formula">elapsed_days = today's day-of-month           (for the CURRENT month)
elapsed_days = days_in_month                  (for any COMPLETED previous month)

payable_gross_salary = per_day_salary &times; elapsed_days</div>
      <div class="rule-example">
        <strong>Example:</strong> &#8377;18,000/month, 30-day month, today is the 11th &rarr;
        payable_gross_salary = 18000 &divide; 30 &times; 11 = &#8377;6,600 (not the full &#8377;18,000).
        Future dates within the month are never marked Absent/Late - they simply haven't happened yet.
      </div>
      <p class="mt-2 mb-0 text-muted">A completed previous month is unaffected - elapsed_days always equals the full
      days_in_month there, so payable_gross_salary equals the full monthly_salary, exactly as before.</p>
    </div>
  </div>

  {{-- ================= 8. NET PAYABLE ================= --}}
  <div class="card rule-card">
    <div class="card-header"><h6><i class="ti ti-cash me-1"></i> 8. Net Payable (Final Formula)</h6></div>
    <div class="card-body">
      <div class="rule-formula">total_deduction = late_deduction + absent_deduction + half_day_deduction + sunday_deduction
net_payable      = payable_gross_salary - total_deduction</div>
      <p class="mb-0 text-muted">This single formula is what the Salary Dashboard, Payroll, Attendance Slip and Salary
      Slip all use - one shared calculation, so none of them can ever disagree with each other.</p>
    </div>
  </div>

  {{-- ================= 9. LOCKED PAYROLL ================= --}}
  <div class="card rule-card">
    <div class="card-header"><h6><i class="ti ti-lock me-1"></i> 9. Locked (Final) Payroll</h6></div>
    <div class="card-body mb-0">
      <p class="mb-0">Once a Payroll record is <strong>Locked</strong> as Final Salary, its figures are frozen exactly as
      they were at that moment - they no longer recalculate even if attendance, holidays or these settings change
      afterwards. A <strong>Draft</strong> payroll (not yet locked) always reflects the latest live calculation each
      time "Generate Payroll" is run again for that staff member/period.</p>
    </div>
  </div>

</div>
@endsection
