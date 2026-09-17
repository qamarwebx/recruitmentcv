<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdvancePayment;
use App\Models\Payroll;
use Illuminate\Support\Facades\DB;

/**
 * Owns the Final Salary lifecycle (Payroll rows): generating a snapshot from
 * SalaryCalculationService, locking it (Super Admin only, per the payroll
 * access decision for this module), and applying manual overrides.
 */
class PayrollService
{
    public function __construct(protected SalaryCalculationService $calculator)
    {
    }

    /**
     * Snapshot the live calculation into a draft Payroll row. Re-running this
     * on an existing draft refreshes it with the latest attendance data.
     * Locking is permanent - generate() never touches a locked row.
     */
    public function generate(Admin $admin, int $month, int $year, Admin $actingAdmin): Payroll
    {
        $result = $this->calculator->calculate($admin, $month, $year);

        return DB::transaction(function () use ($admin, $month, $year, $result, $actingAdmin) {
            // Locked inside the transaction (not just checked beforehand) so
            // two concurrent "Generate" clicks for the same admin/month/year
            // can't both pass the locked-status check before either writes.
            $existing = Payroll::where('admin_id', $admin->id)
                ->where('month', $month)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->status === 'locked') {
                throw new \RuntimeException('This payroll is locked as Final Salary and cannot be regenerated.');
            }

            // Every Advance Payment recorded for this staff member/month,
            // whether it predates this payroll or was added while a draft
            // already existed - always a full re-sum (never incremented), so
            // regenerating a draft can never double-count.
            $advanceTotal = (float) AdvancePayment::where('admin_id', $admin->id)
                ->where('month', $month)
                ->where('year', $year)
                ->lockForUpdate()
                ->sum('amount');

            $payroll = Payroll::updateOrCreate(
                ['admin_id' => $admin->id, 'month' => $month, 'year' => $year],
                [
                    'monthly_salary' => $result['monthly_salary'],
                    'daily_salary' => $result['daily_salary'],
                    'days_in_month' => $result['days_in_month'],
                    'present_days' => $result['present'],
                    'absent_days' => $result['absent'],
                    'half_days' => $result['half_days'],
                    'qualifying_late_count' => $result['late_marks'],
                    'late_count' => $result['late_marks_non_qualifying'],
                    'holiday_days' => $result['holiday_days'],
                    'late_deduction' => $result['late_deduction'],
                    'absent_deduction' => $result['absent_deduction'],
                    'half_day_deduction' => $result['half_day_deduction'],
                    'sunday_deduction' => $result['sunday_deduction'],
                    'total_deduction' => $result['total_deduction'],
                    'net_payable' => $result['net_payable'],
                    'status' => 'draft',
                    'generated_by' => $actingAdmin->id,
                    'generated_at' => now(),
                    'advance_deduction' => round($advanceTotal, 2),
                ]
            );

            // Link every matching advance to this (freshly created or
            // regenerated) payroll row - the unique admin_id/month/year
            // constraint on payrolls guarantees these can only ever belong
            // to this one row for this period.
            AdvancePayment::where('admin_id', $admin->id)
                ->where('month', $month)
                ->where('year', $year)
                ->update(['payroll_id' => $payroll->id]);

            return $payroll;
        });
    }

    public function lock(Payroll $payroll, Admin $actingAdmin): Payroll
    {
        $payroll->status = 'locked';
        $payroll->locked_by = $actingAdmin->id;
        $payroll->locked_at = now();
        $payroll->save();

        return $payroll;
    }

    /**
     * Manual override of a Final Salary record - Super Admin only. Deduction
     * components are re-summed server-side so total/net can never drift from
     * whatever component values were actually saved.
     */
    public function applyManualUpdate(Payroll $payroll, array $data): Payroll
    {
        $payroll->fill(array_intersect_key($data, array_flip([
            'monthly_salary',
            'daily_salary',
            'present_days',
            'absent_days',
            'half_days',
            'qualifying_late_count',
            'late_count',
            'late_deduction',
            'absent_deduction',
            'half_day_deduction',
            'sunday_deduction',
            'notes',
        ])));

        $payroll->total_deduction = round(
            $payroll->late_deduction + $payroll->absent_deduction + $payroll->half_day_deduction + $payroll->sunday_deduction,
            2
        );
        $payroll->net_payable = round($payroll->monthly_salary - $payroll->total_deduction, 2);
        $payroll->save();

        return $payroll;
    }
}
