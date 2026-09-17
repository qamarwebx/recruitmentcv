<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdvancePayment;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Owns the Advance Payment lifecycle and its one-way link into Payroll:
 *
 * - An advance is only ever auto-linked to a payroll that is still a Draft
 *   for the exact same admin/month/year - never to a Locked/Paid one, so a
 *   finalized payroll is never silently modified (see
 *   guardAgainstLockedPayroll()).
 * - Payroll::advance_deduction is always a full re-sum of whatever is
 *   currently linked (never incremented), so create/edit/delete/regenerate
 *   can never double-count or drift.
 * - Every read-modify-write against a Payroll or the advances tied to it
 *   happens inside a transaction with lockForUpdate(), so two concurrent
 *   requests can't both apply/relink the same advance.
 */
class AdvancePaymentService
{
    public function create(Admin $staff, Carbon $advanceDate, float $amount, ?string $remarks, Admin $actingAdmin): AdvancePayment
    {
        return DB::transaction(function () use ($staff, $advanceDate, $amount, $remarks, $actingAdmin) {
            $advance = AdvancePayment::create([
                'admin_id' => $staff->id,
                'month' => $advanceDate->month,
                'year' => $advanceDate->year,
                'advance_date' => $advanceDate,
                'amount' => $amount,
                'remarks' => $remarks,
                'created_by' => $actingAdmin->id,
                'updated_by' => $actingAdmin->id,
            ]);

            $this->relink($advance, null);

            return $advance->fresh();
        });
    }

    /**
     * @throws \RuntimeException if the advance is currently linked to a
     * Locked/Paid payroll - editing it would silently change a finalized
     * payroll's Total Paid.
     */
    public function update(AdvancePayment $advance, Admin $staff, Carbon $advanceDate, float $amount, ?string $remarks, Admin $actingAdmin): AdvancePayment
    {
        return DB::transaction(function () use ($advance, $staff, $advanceDate, $amount, $remarks, $actingAdmin) {
            $advance = AdvancePayment::whereKey($advance->id)->lockForUpdate()->firstOrFail();

            $this->guardAgainstLockedPayroll($advance);

            $oldPayrollId = $advance->payroll_id;

            $advance->admin_id = $staff->id;
            $advance->month = $advanceDate->month;
            $advance->year = $advanceDate->year;
            $advance->advance_date = $advanceDate;
            $advance->amount = $amount;
            $advance->remarks = $remarks;
            $advance->updated_by = $actingAdmin->id;
            $advance->save();

            $this->relink($advance, $oldPayrollId);

            return $advance->fresh();
        });
    }

    /**
     * @throws \RuntimeException if the advance is currently linked to a
     * Locked/Paid payroll.
     */
    public function delete(AdvancePayment $advance): void
    {
        DB::transaction(function () use ($advance) {
            $advance = AdvancePayment::whereKey($advance->id)->lockForUpdate()->firstOrFail();

            $this->guardAgainstLockedPayroll($advance);

            $oldPayrollId = $advance->payroll_id;
            $advance->delete();

            if ($oldPayrollId) {
                $this->recalcPayrollById($oldPayrollId);
            }
        });
    }

    /**
     * The advance total that applies to one admin/month/year "on paper" -
     * used by the Salary Slip / Attendance Slip / Live Salary dashboard.
     * A Locked payroll's own frozen figure is authoritative once it exists
     * (advances created afterwards never silently change it); otherwise
     * this is a live sum of whatever advances currently match the period.
     */
    public function resolveApplicableAdvance(int $adminId, int $month, int $year): float
    {
        $lockedAdvanceDeduction = Payroll::where('admin_id', $adminId)
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'locked')
            ->value('advance_deduction');

        if ($lockedAdvanceDeduction !== null) {
            return round((float) $lockedAdvanceDeduction, 2);
        }

        return round((float) AdvancePayment::where('admin_id', $adminId)
            ->where('month', $month)
            ->where('year', $year)
            ->sum('amount'), 2);
    }

    private function guardAgainstLockedPayroll(AdvancePayment $advance): void
    {
        if (!$advance->payroll_id) {
            return;
        }

        $payroll = Payroll::find($advance->payroll_id);

        if ($payroll && $payroll->status === 'locked') {
            throw new \RuntimeException('This advance payment is linked to a Locked/Paid payroll and cannot be modified. Delete and regenerate that payroll (if permitted) to pick up the change.');
        }
    }

    /**
     * Re-resolve which payroll (if any) this advance should be linked to,
     * based on its current admin_id/month/year, then recompute
     * advance_deduction on both the newly-linked payroll and whichever
     * payroll it was linked to before (if different) - so a relink never
     * leaves either side's total stale.
     */
    private function relink(AdvancePayment $advance, ?int $oldPayrollId): void
    {
        $target = Payroll::where('admin_id', $advance->admin_id)
            ->where('month', $advance->month)
            ->where('year', $advance->year)
            ->lockForUpdate()
            ->first();

        $newPayrollId = ($target && $target->status === 'draft') ? $target->id : null;

        if ($advance->payroll_id !== $newPayrollId) {
            $advance->payroll_id = $newPayrollId;
            $advance->save();
        }

        if ($target && $target->status === 'draft') {
            $this->recalcPayroll($target);
        }

        if ($oldPayrollId && $oldPayrollId !== $newPayrollId) {
            $this->recalcPayrollById($oldPayrollId);
        }
    }

    private function recalcPayrollById(int $payrollId): void
    {
        $payroll = Payroll::whereKey($payrollId)->lockForUpdate()->first();

        if ($payroll) {
            $this->recalcPayroll($payroll);
        }
    }

    private function recalcPayroll(Payroll $payroll): void
    {
        // A Locked/Paid payroll is never touched here - its advance_deduction
        // stays frozen at whatever it was when it was locked.
        if ($payroll->status !== 'draft') {
            return;
        }

        $sum = AdvancePayment::where('payroll_id', $payroll->id)->sum('amount');
        $payroll->advance_deduction = round((float) $sum, 2);
        $payroll->save();
    }
}
