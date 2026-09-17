<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Salary Slip - {{ $target->name }} - {{ $periodLabel }}</title>
<style>
    @page { margin: 20px 28px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2a44; margin: 0; padding: 0; }
    .navy { color: #0f1f3d; }
    table { border-collapse: collapse; }

    /* ---- Header ---- */
    .brand-table { margin: 0 auto 8px auto; }
    .brand-logo-cell { width: 52px; vertical-align: middle; }
    .brand-logo-img { width: 44px; height: 44px; border-radius: 50%; border: 2px solid #0f1f3d; }
    .brand-text-cell { vertical-align: middle; }
    .brand-title { font-size: 23px; font-weight: bold; color: #0f1f3d; letter-spacing: .5px; line-height: 1.2; }
    .brand-subtitle-table { border-collapse: collapse; margin-top: 3px; }
    .brand-subtitle-table td { padding: 0; vertical-align: middle; }
    .brand-subtitle-line { width: 46px; }
    .brand-subtitle-line .rule { height: 1px; width: 100%; background-color: #0f1f3d; font-size: 0; line-height: 0; }
    .brand-subtitle-text { font-size: 10px; letter-spacing: 3px; color: #0f1f3d; padding: 10px 8px; white-space: nowrap; }
    .doc-title-row { margin-top: 14px; text-align: center; }
    .doc-title { font-size: 18px; font-weight: bold; color: #0f1f3d; letter-spacing: 1px; }
    .header-rule { border-bottom: 2px solid #0f1f3d; margin: 10px 0 12px 0; }

    /* ---- Employee info box ---- */
    .info-box { width: 100%; border: 1px solid #0f1f3d; padding: 8px 12px; margin-bottom: 14px; }
    .info-box table { width: 100%; }
    .info-box td { padding: 3px 4px; font-size: 11px; vertical-align: top; }
    .info-label { color: #444; width: 110px; }
    .info-colon { width: 12px; }
    .info-value { font-weight: bold; }

    /* ---- Earnings / Deductions ---- */
    .amounts-table { width: 100%; border: 1px solid #0f1f3d; margin-bottom: 12px; }
    .amounts-table th { padding: 7px 10px; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
    .th-earn { background: #0f1f3d; color: #fff; text-align: left; }
    .th-ded { background: #0f1f3d; color: #fff; text-align: left; border-left: 1px solid #fff; }
    .amounts-table td { padding: 5px 10px; font-size: 11px; border-top: 1px solid #dfe3ea; }
    .col-particulars { text-align: left; }
    .col-amount { text-align: right; width: 90px; }
    .col-divider { border-left: 1px solid #0f1f3d; }
    .totals-row td { font-weight: bold; border-top: 2px solid #0f1f3d; }
    .totals-earn { background: #e8edf7; }
    .totals-ded { background: #fbe6e6; }

    /* ---- Summary rows ---- */
    .grand-table { width: 100%; border: 1px solid #0f1f3d; margin-bottom: 12px; }
    .grand-table td { padding: 7px 12px; font-size: 12px; }
    .grand-table tr + tr td { border-top: 1px solid #dfe3ea; }
    .grand-label { font-weight: bold; }
    .grand-value { text-align: right; font-weight: bold; }
    .net-row { background: #d9f2e3; }
    .net-row td { font-size: 14px; color: #0d6b3a; }

    /* ---- Stat strip ---- */
    .stat-table { width: 100%; border-collapse: separate; border-spacing: 4px; margin-bottom: 12px; }
    .stat-cell { border: 1px solid #0f1f3d; text-align: center; padding: 6px 4px; width: 16.66%; }
    .stat-label { font-size: 8.5px; text-transform: uppercase; color: #444; letter-spacing: .2px; }
    .stat-value { font-size: 14px; font-weight: bold; color: #0f1f3d; margin-top: 2px; }

    /* ---- Amount in words ---- */
    .words-box { border-top: 1px solid #0f1f3d; padding-top: 8px; margin-bottom: 20px; font-size: 11px; }
    .words-label { font-weight: bold; }

    /* ---- Footer ---- */
    .footer-table { width: 100%; margin-top: 30px; }
    .footer-table td { font-size: 10px; text-align: center; padding-top: 30px; border-top: 1px solid #333; width: 33%; }
    .footer-note { text-align: center; font-size: 9px; color: #666; margin-top: 14px; }
    .status-pill { display: inline-block; padding: 2px 10px; border-radius: 3px; font-size: 9px; font-weight: bold; margin-left: 8px; }
    .status-final { background: #d1f2df; color: #1a7f4e; }
    .status-provisional { background: #fff2cc; color: #8a6d00; }
</style>
</head>
<body>

    <table class="brand-table">
        <tr>
            <td class="brand-logo-cell"><img class="brand-logo-img" src="{{ $logoPath }}"></td>
            <td class="brand-text-cell">
                <div class="brand-title">{{ $companyName }}</div>
                <table class="brand-subtitle-table"><tr>
                    <td class="brand-subtitle-line"><div class="rule"></div></td>
                    <td class="brand-subtitle-text">{{ $companySubtitle }}</td>
                    <td class="brand-subtitle-line"><div class="rule"></div></td>
                </tr></table>
            </td>
        </tr>
    </table>

    <div class="doc-title-row">
        <span class="doc-title">SALARY SLIP</span>
        <span class="status-pill {{ $result['is_locked'] ? 'status-final' : 'status-provisional' }}">
            {{ $result['is_locked'] ? 'FINAL' : 'PROVISIONAL (LIVE)' }}
        </span>
    </div>
    <div class="header-rule"></div>

    <div class="info-box">
        <table>
            <tr>
                <td class="info-label">Employee ID</td><td class="info-colon">:</td><td class="info-value">{{ $target->id }}</td>
                <td class="info-label">Month</td><td class="info-colon">:</td><td class="info-value">{{ $periodLabel }}</td>
            </tr>
            <tr>
                <td class="info-label">Employee Name</td><td class="info-colon">:</td><td class="info-value">{{ strtoupper($target->name) }}</td>
                <td class="info-label">Date of Joining</td><td class="info-colon">:</td><td class="info-value">{{ $dateOfJoining }}</td>
            </tr>
            <tr>
                <td class="info-label">Designation</td><td class="info-colon">:</td><td class="info-value">-</td>
                <td class="info-label">Department</td><td class="info-colon">:</td><td class="info-value">-</td>
            </tr>
        </table>
    </div>

    @php
        // payable_gross_salary is the prorated earnings base for the period
        // actually covered - the full monthly_salary for a completed month,
        // but only 1st..today's worth for the month currently in progress -
        // so Total Salary (Gross) minus Total Deductions always equals Net
        // Payable Salary on the printed slip, whichever period this is.
        $earningsRows = [['Basic Salary', $result['payable_gross_salary']]];
        $deductionRows = [
            ['Late Deduction', $result['late_deduction']],
            ['Absent Deduction', $result['absent_deduction']],
            ['Half-Day Deduction', $result['half_day_deduction']],
            ['Sunday Deduction', $result['sunday_deduction']],
        ];
        $rowCount = max(count($earningsRows), count($deductionRows));
    @endphp

    <table class="amounts-table">
        <thead>
            <tr>
                <th class="th-earn" colspan="2">Earnings</th>
                <th class="th-ded col-divider" colspan="2">Deductions</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < $rowCount; $i++)
            <tr>
                <td class="col-particulars">{{ $earningsRows[$i][0] ?? '' }}</td>
                <td class="col-amount">{{ isset($earningsRows[$i]) ? number_format($earningsRows[$i][1], 2) : '' }}</td>
                <td class="col-particulars col-divider">{{ $deductionRows[$i][0] ?? '' }}</td>
                <td class="col-amount">{{ isset($deductionRows[$i]) ? number_format($deductionRows[$i][1], 2) : '' }}</td>
            </tr>
            @endfor
            <tr class="totals-row">
                <td class="col-particulars totals-earn">Total Earnings</td>
                <td class="col-amount totals-earn">{{ number_format($result['payable_gross_salary'], 2) }}</td>
                <td class="col-particulars col-divider totals-ded">Total Deductions</td>
                <td class="col-amount totals-ded">{{ number_format($result['total_deduction'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="grand-table">
        <tr>
            <td class="grand-label">Total Salary (Gross)</td>
            <td class="grand-value">&#8377; {{ number_format($result['payable_gross_salary'], 2) }}</td>
        </tr>
        <tr>
            <td class="grand-label">Total Deductions</td>
            <td class="grand-value">&#8377; {{ number_format($result['total_deduction'], 2) }}</td>
        </tr>
        <tr class="net-row">
            <td class="grand-label">Net Payable Salary</td>
            <td class="grand-value">&#8377; {{ number_format($result['net_payable'], 2) }}</td>
        </tr>
        @if($result['advance_deduction'] > 0)
        <tr>
            <td class="grand-label">Advance Payment</td>
            <td class="grand-value" style="color:#b02a2a;">- &#8377; {{ number_format($result['advance_deduction'], 2) }}</td>
        </tr>
        <tr class="net-row">
            <td class="grand-label">Net Payable After Advance</td>
            <td class="grand-value">&#8377; {{ number_format($result['final_payable_after_advance'], 2) }}</td>
        </tr>
        @endif
    </table>

    <table class="stat-table">
        <tr>
            <td class="stat-cell">
                <div class="stat-label">Total Working Days</div>
                <div class="stat-value">{{ $totalWorkingDays }}</div>
            </td>
            <td class="stat-cell">
                <div class="stat-label">Present Days</div>
                <div class="stat-value">{{ $result['present'] }}</div>
            </td>
            <td class="stat-cell">
                <div class="stat-label">Holidays</div>
                <div class="stat-value">{{ $result['holiday_days'] }}</div>
            </td>
            <td class="stat-cell">
                <div class="stat-label">Total Absent Days</div>
                <div class="stat-value">{{ $result['absent'] }}</div>
            </td>
            <td class="stat-cell">
                <div class="stat-label">Total Late Mark</div>
                <div class="stat-value">{{ $result['total_late_count'] }}</div>
            </td>
            <td class="stat-cell">
                <div class="stat-label">Weekly Off</div>
                <div class="stat-value">{{ $weeklyOffDays }}</div>
            </td>
        </tr>
    </table>

    <div class="words-box">
        <span class="words-label">Amount in Words:</span><br>
        {{ $amountInWords }}
    </div>

    <table class="footer-table">
        <tr>
            <td>Prepared By</td>
            <td style="border-top: none;"></td>
            <td>Authorized Signatory</td>
        </tr>
    </table>
    <div class="footer-note">
        Generated on {{ now()->format('d M Y, h:i A') }}
        @if($result['is_locked'] && !empty($result['locked_at']))
            &middot; Finalized on {{ $result['locked_at'] }}
        @endif
        <br>This is a system-generated document and does not require a signature.
    </div>

</body>
</html>
