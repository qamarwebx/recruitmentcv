<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Attendance Slip - {{ $target->name }} - {{ $periodLabel }}</title>
<style>
    @page { margin: 18px 22px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1f2a44; margin: 0; padding: 0; }
    table { border-collapse: collapse; }

    /* ---- Header ---- */
    .brand-table { width: 100%; margin-bottom: 8px; }
    .brand-logo-cell { width: 44px; vertical-align: middle; }
    .brand-logo-img { width: 36px; height: 36px; border-radius: 50%; border: 2px solid #0f1f3d; }
    .brand-text-cell { vertical-align: middle; }
    .brand-title { font-size: 17px; font-weight: bold; color: #0f1f3d; letter-spacing: .5px; line-height: 1.2; }
    .brand-subtitle-table { border-collapse: collapse; margin-top: 2px; }
    .brand-subtitle-table td { padding: 0; vertical-align: middle; }
    .brand-subtitle-line { width: 26px; }
    .brand-subtitle-line .rule { height: 1px; width: 100%; background-color: #0f1f3d; font-size: 0; line-height: 0; }
    .brand-subtitle-text { font-size: 8px; letter-spacing: 2px; color: #0f1f3d; padding: 0 6px; white-space: nowrap; }
    .header-right { text-align: right; vertical-align: middle; }
    .doc-title { font-size: 14px; font-weight: bold; color: #0f1f3d; }
    .month-badge { display: inline-block; background: #0f1f3d; color: #fff; padding: 4px 12px; font-size: 10px; font-weight: bold; border-radius: 3px; margin-top: 5px; }
    .header-rule { border-bottom: 2px solid #0f1f3d; margin: 8px 0 10px 0; }

    /* ---- Employee info box ---- */
    .info-box { width: 100%; border: 1px solid #0f1f3d; padding: 6px 10px; margin-bottom: 10px; }
    .info-box table { width: 100%; }
    .info-box td { padding: 2px 3px; font-size: 9.5px; vertical-align: top; }
    .info-label { color: #444; width: 95px; }
    .info-colon { width: 10px; }
    .info-value { font-weight: bold; }

    /* ---- Day tables ---- */
    .days-split { width: 100%; }
    .days-split > tbody > tr > td { vertical-align: top; }
    .day-table { width: 100%; border: 1px solid #0f1f3d; }
    .day-table th { background: #0f1f3d; color: #fff; padding: 3px 2px; font-size: 7.5px; text-transform: uppercase; }
    .day-table td { padding: 2.5px 2px; font-size: 8px; border-top: 1px solid #dfe3ea; text-align: center; }
    .row-off { background: #e9edf5; }
    .row-late td.remark-cell { color: #b5650d; font-weight: bold; }
    .row-absent td.remark-cell { color: #b02a2a; font-weight: bold; }
    .total-late-row td { font-weight: bold; border-top: 2px solid #0f1f3d; background: #f2f4f8; }

    /* ---- Sidebar ---- */
    .sidebar-box { border: 1px solid #0f1f3d; margin-bottom: 8px; }
    .sidebar-box .box-header { background: #0f1f3d; color: #fff; font-size: 9px; font-weight: bold; text-transform: uppercase; padding: 4px 8px; text-align: center; }
    .sidebar-box table { width: 100%; }
    .sidebar-box td { padding: 3px 8px; font-size: 8.5px; border-top: 1px solid #dfe3ea; }
    .sidebar-box .val { text-align: right; font-weight: bold; }
    .ded-total td { border-top: 2px solid #0f1f3d; font-weight: bold; color: #b02a2a; }
    .net-box { border: 1px solid #0d6b3a; background: #d9f2e3; text-align: center; padding: 8px; }
    .net-box .net-label { font-size: 9px; font-weight: bold; color: #0d6b3a; text-transform: uppercase; }
    .net-box .net-value { font-size: 16px; font-weight: bold; color: #0d6b3a; margin-top: 3px; }

    /* ---- Absent details + summary ---- */
    .bottom-table { width: 100%; margin-top: 10px; }
    .bottom-table > tbody > tr > td { vertical-align: top; }
    .section-title { background: #0f1f3d; color: #fff; font-size: 9px; font-weight: bold; text-transform: uppercase; padding: 4px 8px; text-align: center; }
    .absent-table { width: 100%; border: 1px solid #0f1f3d; }
    .absent-table th { background: #eef1f7; padding: 3px 4px; font-size: 8px; text-transform: uppercase; border-top: 1px solid #0f1f3d; }
    .absent-table td { padding: 3px 4px; font-size: 8.5px; border-top: 1px solid #dfe3ea; text-align: center; color: #b02a2a; font-weight: bold; }
    .absent-total-row td { background: #f2f4f8; color: #1f2a44; text-align: right; border-top: 2px solid #0f1f3d; }
    .summary-box { border: 1px solid #0f1f3d; border-top: none; }
    .summary-box table { width: 100%; }
    .summary-box td { padding: 4px 10px; font-size: 8.5px; border-top: 1px solid #dfe3ea; }
    .summary-box .val { text-align: right; font-weight: bold; }

    /* ---- Footer ---- */
    .footer-table { width: 100%; margin-top: 24px; }
    .footer-table td { font-size: 8.5px; text-align: center; padding-top: 24px; border-top: 1px solid #333; width: 33%; }
    .footer-note { text-align: center; font-size: 8px; color: #666; margin-top: 12px; }
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
            <td class="header-right" width="220">
                <div class="doc-title">MONTHLY ATTENDANCE DETAILS</div>
                <span class="month-badge">MONTH: {{ strtoupper($periodLabel) }}</span>
            </td>
        </tr>
    </table>
    <div class="header-rule"></div>

    <div class="info-box">
        <table>
            <tr>
                <td class="info-label">Employee ID</td><td class="info-colon">:</td><td class="info-value">{{ $target->id }}</td>
                <td class="info-label">Department</td><td class="info-colon">:</td><td class="info-value">-</td>
            </tr>
            <tr>
                <td class="info-label">Employee Name</td><td class="info-colon">:</td><td class="info-value">{{ strtoupper($target->name) }}</td>
                <td class="info-label">Date of Joining</td><td class="info-colon">:</td><td class="info-value">{{ $target->created_at?->format('d M Y') ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Designation</td><td class="info-colon">:</td><td class="info-value">-</td>
                <td class="info-label">Location</td><td class="info-colon">:</td><td class="info-value">-</td>
            </tr>
        </table>
    </div>

    @php
        $half = (int) ceil(count($rows) / 2);
        $leftRows = array_slice($rows, 0, $half);
        $rightRows = array_slice($rows, $half);

        $dayTable = function ($tableRows, $showTotal) use ($totalLateMarks) {
            $html = '<table class="day-table"><thead><tr>
                <th>Date</th><th>Day</th><th>Status</th><th>In</th><th>Out</th><th>Hrs</th><th>Remarks</th>
            </tr></thead><tbody>';

            foreach ($tableRows as $row) {
                $rowClass = in_array($row['status_key'], ['weekly_off', 'holiday']) ? 'row-off'
                    : (in_array($row['status_key'], ['qualifying_late', 'late']) ? 'row-late'
                    : ($row['status_key'] === 'absent' ? 'row-absent' : ''));

                $html .= '<tr class="' . $rowClass . '">
                    <td>' . $row['date_num'] . '</td>
                    <td>' . $row['day_short'] . '</td>
                    <td>' . strtoupper($row['status']) . '</td>
                    <td>' . $row['in_time'] . '</td>
                    <td>' . $row['out_time'] . '</td>
                    <td>' . $row['work_hrs'] . '</td>
                    <td class="remark-cell">' . $row['remarks'] . '</td>
                </tr>';
            }

            if ($showTotal) {
                $html .= '<tr class="total-late-row"><td colspan="6">TOTAL LATE MARKS</td><td>' . $totalLateMarks . '</td></tr>';
            }

            $html .= '</tbody></table>';
            return $html;
        };
    @endphp

    <table class="days-split">
        <tr>
            <td width="50%" style="padding-right: 4px;">{!! $dayTable($leftRows, false) !!}</td>
            <td width="50%" style="padding-left: 4px;">{!! $dayTable($rightRows, true) !!}</td>
        </tr>
    </table>

    <table class="days-split" style="margin-top: 10px;">
        <tr>
            <td width="74%" style="padding-right: 6px;">

                <table class="bottom-table">
                    <tr>
                        <td width="62%" style="padding-right: 6px;">
                            <div class="section-title">Absent Days Details</div>
                            <table class="absent-table">
                                <thead>
                                    <tr><th>Date</th><th>Day</th><th>Status</th><th>Remarks</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($absentRows as $row)
                                    <tr>
                                        <td>{{ $row['date'] }}</td>
                                        <td>{{ $row['day_short'] }}</td>
                                        <td>ABSENT</td>
                                        <td>-</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" style="color:#1f2a44; font-weight:normal;">No absences this month.</td></tr>
                                    @endforelse
                                    <tr class="absent-total-row">
                                        <td colspan="3">Total Absent Days</td>
                                        <td>{{ $result['absent'] }} DAYS</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td width="38%">
                            <div class="section-title">Summary</div>
                            <div class="summary-box">
                                <table>
                                    <tr><td>Total Working Days</td><td class="val">{{ $totalWorkingDays }}</td></tr>
                                    <tr><td>Present Days</td><td class="val">{{ $result['present'] }}</td></tr>
                                    <tr><td>Absent Days</td><td class="val">{{ $result['absent'] }}</td></tr>
                                    <tr><td>Late Marks</td><td class="val">{{ $totalLateMarks }}</td></tr>
                                    <tr><td>Half Day (HD)</td><td class="val">{{ $result['half_days'] }}</td></tr>
                                    <tr><td>Weekly Off Days</td><td class="val">{{ $weeklyOffDays }}</td></tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>

            </td>
            <td width="26%">
                <div class="sidebar-box">
                    <div class="box-header">Salary Details</div>
                    <table>
                        <tr><td>Basic Salary</td><td class="val">{{ number_format($result['payable_gross_salary'], 2) }}</td></tr>
                        <tr><td>Per Day (Salary)</td><td class="val">{{ number_format($result['daily_salary'], 2) }}</td></tr>
                        <tr><td>Per Hour (Salary)</td><td class="val">{{ number_format($perHourSalary, 2) }}</td></tr>
                        <tr><td>HD (Half Day)</td><td class="val">{{ number_format($halfDayAmount, 2) }}</td></tr>
                    </table>
                </div>

                <div class="sidebar-box">
                    <div class="box-header">Deduction Summary</div>
                    <table>
                        <tr><td>Absent Days ({{ $result['absent'] }})</td><td class="val">{{ number_format($result['absent_deduction'], 2) }}</td></tr>
                        <tr><td>Late Marks ({{ $result['total_late_count'] }})</td><td class="val">{{ number_format($result['late_deduction'], 2) }}</td></tr>
                        <tr><td>Half Day ({{ $result['half_days'] }})</td><td class="val">{{ number_format($result['half_day_deduction'], 2) }}</td></tr>
                        <tr><td>Sunday Ded.</td><td class="val">{{ number_format($result['sunday_deduction'], 2) }}</td></tr>
                        <tr class="ded-total"><td>Total Deduction</td><td class="val">{{ number_format($result['total_deduction'], 2) }}</td></tr>
                    </table>
                </div>

                @if($advanceDeduction > 0)
                <div class="sidebar-box">
                    <div class="box-header">Advance Payment</div>
                    <table>
                        <tr><td>Advance Payment</td><td class="val" style="color:#b02a2a;">{{ number_format($advanceDeduction, 2) }}</td></tr>
                    </table>
                </div>
                @endif

                <div class="net-box">
                    <div class="net-label">{{ $advanceDeduction > 0 ? 'Net Payable After Advance' : 'Net Salary Payable' }}</div>
                    <div class="net-value">{{ number_format($advanceDeduction > 0 ? $finalPayableAfterAdvance : $result['net_payable'], 2) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="footer-table">
        <tr>
            <td>Prepared By</td>
            <td style="border-top: none;"></td>
            <td>Authorized Signatory</td>
        </tr>
    </table>
    <div class="footer-note">
        Date of Issue: {{ now()->format('d M Y') }}<br>
        This is a system-generated report and does not require a manual signature.
    </div>

</body>
</html>
