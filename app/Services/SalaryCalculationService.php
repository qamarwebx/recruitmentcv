<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AttendanceLog;
use App\Models\Holiday;
use App\Models\SalarySetting;
use Carbon\Carbon;

/**
 * Computes "Live Salary" for one staff member / one month, purely from the
 * current state of attendance logs, holidays and salary settings - nothing
 * here is persisted. Called fresh on every request so the dashboard is
 * always in sync with the latest attendance/holiday/settings data.
 */
class SalaryCalculationService
{
    protected SalarySetting $settings;

    /**
     * No constructor parameter for the settings row on purpose: a nullable
     * SalarySetting type-hint here would still let Laravel's container
     * auto-resolve it to a blank, non-persisted model (since Eloquent models
     * are trivially constructible) instead of passing null, silently
     * bypassing SalarySetting::current() and leaving every time field empty.
     */
    public function __construct(protected PayrollDeductionService $deductionService)
    {
        $this->settings = SalarySetting::current();
    }

    public function calculate(Admin $admin, int $month, int $year): array
    {
        $monthlySalary = (float) ($admin->monthly_salary ?? 0);
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $dailySalary = $daysInMonth > 0 ? $monthlySalary / $daysInMonth : 0.0;

        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->startOfDay();
        $today = Carbon::today();

        // Only evaluate days that have actually happened - a future month
        // (or the remainder of the current month) can't be Absent/Late yet.
        $evalEnd = $today->lt($monthEnd) ? $today->copy()->startOfDay() : $monthEnd;
        if ($evalEnd->gt($monthEnd)) {
            $evalEnd = $monthEnd->copy();
        }
        $hasElapsedDays = $evalEnd->gte($monthStart);

        $dayStatus = $hasElapsedDays
            ? $this->buildDayStatusMap($admin, $monthStart, $evalEnd)
            : [];

        // How many calendar days of this period have actually happened -
        // the full month for a completed previous month, but only 1st..today
        // for the month currently in progress. Drives the prorated payable
        // base below so an in-progress month is never paid as if it were
        // already complete.
        $elapsedDays = $hasElapsedDays ? ($monthStart->diffInDays($evalEnd) + 1) : 0;

        $counts = ['present' => 0, 'absent' => 0, 'half_day' => 0, 'qualifying_late' => 0, 'late' => 0, 'holiday' => 0, 'weekly_off' => 0];

        foreach ($dayStatus as $status) {
            switch ($status) {
                case 'present':
                    $counts['present']++;
                    break;
                case 'qualifying_late':
                    $counts['present']++;
                    $counts['qualifying_late']++;
                    break;
                case 'late':
                    $counts['present']++;
                    $counts['late']++;
                    break;
                case 'half_day':
                    $counts['half_day']++;
                    break;
                case 'absent':
                    $counts['absent']++;
                    break;
                case 'holiday':
                    $counts['holiday']++;
                    break;
                case 'weekly_off':
                    $counts['weekly_off']++;
                    break;
            }
        }

        // Late Rule: first `free_late_count` lates (qualifying + non-qualifying
        // combined - every late arrival counts, regardless of how late) are
        // free; once the total exceeds that, the deduction applies
        // retroactively to every late day in the month, not just the ones
        // past the free allowance. The rupee amount charged per late day
        // comes from PayrollDeductionService - Percentage Based, Fixed Slab
        // Based or Fixed Amount, whichever is configured in Salary Settings
        // at calculation time - and is priced off the staff member's monthly
        // salary, not the daily rate.
        $totalLateCount = $counts['qualifying_late'] + $counts['late'];

        $lateDeduction = 0.0;
        if ($totalLateCount > (int) $this->settings->free_late_count) {
            $deductionPerLateDay = $this->deductionService->calculatePerOccurrenceDeduction($monthlySalary);
            $lateDeduction = round($totalLateCount * $deductionPerLateDay, 2);
        }

        $absentDeduction = round($counts['absent'] * $dailySalary, 2);
        $halfDayDeduction = round($counts['half_day'] * $dailySalary * 0.5, 2);

        [$sundayDeduction, $sundayOffCount] = $hasElapsedDays
            ? $this->calculateSundayDeduction($dayStatus, $monthStart, $evalEnd, $dailySalary)
            : [0.0, 0];

        // Payable base for this period: the full monthly salary for a
        // completed previous month (elapsedDays == daysInMonth, so this is
        // unchanged from before), but prorated to only the days that have
        // actually elapsed for the month currently in progress - e.g.
        // 18000 / 30 * 11 for 1-11 Sep, not the full 18000. Deductions below
        // are then subtracted from this same prorated base, exactly like
        // they always were subtracted from the full month's salary.
        $payableGrossSalary = round($dailySalary * $elapsedDays, 2);

        $totalDeduction = round($lateDeduction + $absentDeduction + $halfDayDeduction + $sundayDeduction, 2);
        $netPayable = round($payableGrossSalary - $totalDeduction, 2);

        return [
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'month' => $month,
            'year' => $year,
            'monthly_salary' => round($monthlySalary, 2),
            'daily_salary' => round($dailySalary, 2),
            'days_in_month' => $daysInMonth,
            'elapsed_days' => $elapsedDays,
            'payable_gross_salary' => $payableGrossSalary,
            'current_date' => $today->toDateString(),
            'present' => $counts['present'],
            'absent' => $counts['absent'],
            'half_days' => $counts['half_day'],
            'late_marks' => $counts['qualifying_late'],
            'late_marks_non_qualifying' => $counts['late'],
            'total_late_count' => $totalLateCount,
            'holiday_days' => $counts['holiday'],
            'weekly_off_days' => $counts['weekly_off'],
            'sunday_off_count' => $sundayOffCount,
            'late_deduction' => $lateDeduction,
            'absent_deduction' => $absentDeduction,
            'half_day_deduction' => $halfDayDeduction,
            'sunday_deduction' => $sundayDeduction,
            'total_deduction' => $totalDeduction,
            'net_payable' => $netPayable,
            'is_locked' => false,
        ];
    }

    /**
     * One status per elapsed calendar day in the month:
     * weekly_off | holiday | present | qualifying_late | late | half_day | absent
     */
    protected function buildDayStatusMap(Admin $admin, Carbon $monthStart, Carbon $evalEnd): array
    {
        // Two-phase approval: a day only counts as attended once BOTH Normal
        // Approval (`status`) and Final Approval (`final_status`) are done.
        // Missing either one falls through to 'absent' below, same as
        // before - so a day short of a phase is simply not payable.
        $approvedInTimes = AttendanceLog::where('admin_id', $admin->id)
            ->where('attendance_type', 'in')
            ->where('status', 'approved')
            ->where('final_status', 'approved')
            ->whereBetween('attendance_time', [$monthStart, $evalEnd->copy()->endOfDay()])
            ->get()
            ->groupBy(fn ($log) => $log->attendance_time->format('Y-m-d'))
            ->map(fn ($logs) => $logs->min('attendance_time'));

        $holidayDates = Holiday::whereBetween('holiday_date', [$monthStart->toDateString(), $evalEnd->toDateString()])
            ->pluck('holiday_date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->flip();

        $officeStartTime = (string) $this->settings->office_start_time;
        $qualifyingLateEndTime = (string) $this->settings->qualifying_late_end_time;
        $lateWindowEndTime = (string) $this->settings->late_window_end_time;
        $halfDayAfterTime = (string) $this->settings->half_day_after_time;

        $dayStatus = [];

        for ($date = $monthStart->copy(); $date->lte($evalEnd); $date->addDay()) {
            $dateKey = $date->toDateString();

            if ($date->isSunday()) {
                $dayStatus[$dateKey] = 'weekly_off';
                continue;
            }

            if ($holidayDates->has($dateKey)) {
                $dayStatus[$dateKey] = 'holiday';
                continue;
            }

            $inTime = $approvedInTimes->get($dateKey);

            if (!$inTime) {
                $dayStatus[$dateKey] = 'absent';
                continue;
            }

            $officeStart = $date->copy()->setTimeFromTimeString($officeStartTime);
            $qualifyingLateEnd = $date->copy()->setTimeFromTimeString($qualifyingLateEndTime);
            $lateWindowEnd = $date->copy()->setTimeFromTimeString($lateWindowEndTime);
            $halfDayAfter = $date->copy()->setTimeFromTimeString($halfDayAfterTime);

            if ($inTime->lte($officeStart)) {
                $dayStatus[$dateKey] = 'present';
            } elseif ($inTime->lte($qualifyingLateEnd)) {
                $dayStatus[$dateKey] = 'qualifying_late';
            } elseif ($inTime->lte($lateWindowEnd) && $inTime->lte($halfDayAfter)) {
                // 10:41 AM - 12:00 PM = Late; 12:00 PM exactly is still Late,
                // strictly after 12:00 PM is Half Day.
                $dayStatus[$dateKey] = 'late';
            } else {
                $dayStatus[$dateKey] = 'half_day';
            }
        }

        return $dayStatus;
    }

    /**
     * Weekend Rule: a Sunday (Weekly Off) is deducted as a full day when the
     * adjoining Saturday and/or Monday was Absent. Capped at one daily
     * salary per Sunday even if both neighbours are absent.
     */
    protected function calculateSundayDeduction(array $dayStatus, Carbon $monthStart, Carbon $evalEnd, float $dailySalary): array
    {
        $deduction = 0.0;
        $count = 0;

        for ($date = $monthStart->copy(); $date->lte($evalEnd); $date->addDay()) {
            if (!$date->isSunday()) {
                continue;
            }

            $saturday = $date->copy()->subDay();
            $monday = $date->copy()->addDay();

            $saturdayAbsent = $this->settings->sat_absent_sunday_deduction
                && $saturday->gte($monthStart) && $saturday->lte($evalEnd)
                && ($dayStatus[$saturday->toDateString()] ?? null) === 'absent';

            $mondayAbsent = $this->settings->mon_absent_prev_sunday_deduction
                && $monday->gte($monthStart) && $monday->lte($evalEnd)
                && ($dayStatus[$monday->toDateString()] ?? null) === 'absent';

            if ($saturdayAbsent || $mondayAbsent) {
                $deduction += $dailySalary;
                $count++;
            }
        }

        return [round($deduction, 2), $count];
    }

    /**
     * Raw count of Sundays (Weekly Off) between two dates inclusive - the
     * single implementation shared by the live/current-period calculation
     * above (bounded to the elapsed cutoff via $counts['weekly_off']) and a
     * locked payroll's finalized snapshot (bounded to the full month, since
     * a locked period is by definition complete), so neither has to keep
     * its own copy of this loop.
     */
    public function countSundays(Carbon $start, Carbon $end): int
    {
        $count = 0;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->isSunday()) {
                $count++;
            }
        }

        return $count;
    }
}
