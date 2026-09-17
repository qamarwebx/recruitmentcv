<?php

namespace App\Services;

use App\Models\SalarySetting;

/**
 * Single source of truth for "how much is one deduction-worthy occurrence
 * (currently: one Late Mark) worth, in rupees, for a given monthly salary."
 *
 * Every salary/payroll surface (Payroll, Payslip, Salary Preview, Reports,
 * the Salary Dashboard, the Attendance/Salary Slips) reaches this through
 * SalaryCalculationService rather than computing it independently, so a
 * settings change can never make two screens disagree.
 *
 * All three calculation types read their inputs fresh from SalarySetting on
 * every call - nothing here is hardcoded, and nothing here touches already
 * generated/locked Payroll rows (those are frozen snapshots; only a new
 * calculate() call picks up new settings).
 */
class PayrollDeductionService
{
    protected SalarySetting $settings;

    public function __construct()
    {
        $this->settings = SalarySetting::current();
    }

    /**
     * Deduction amount for ONE occurrence, given the staff member's monthly
     * salary. The caller (SalaryCalculationService) multiplies this by
     * however many occurrences are being charged.
     */
    public function calculatePerOccurrenceDeduction(float $monthlySalary): float
    {
        return match ($this->settings->deduction_calculation_type) {
            SalarySetting::DEDUCTION_TYPE_PERCENTAGE => $this->percentageBased($monthlySalary),
            SalarySetting::DEDUCTION_TYPE_FIXED_AMOUNT => $this->fixedAmount(),
            default => $this->fixedSlabBased($monthlySalary),
        };
    }

    /**
     * deduction = salary × configured_percentage / 100
     */
    protected function percentageBased(float $monthlySalary): float
    {
        return round($monthlySalary * ((float) $this->settings->late_deduction_percent / 100), 2);
    }

    /**
     * deduction = floor(salary / configured_slab_amount) × configured_deduction_per_slab
     *
     * A partial/incomplete slab (the remainder below the next full slab) is
     * always ignored - complete slabs only, never charged for a fraction.
     */
    protected function fixedSlabBased(float $monthlySalary): float
    {
        $slabAmount = (float) $this->settings->deduction_slab_amount;

        if ($slabAmount <= 0) {
            return 0.0;
        }

        $perSlab = (float) $this->settings->deduction_per_slab;
        $completeSlabs = floor($monthlySalary / $slabAmount);

        return round($completeSlabs * $perSlab, 2);
    }

    /**
     * deduction = configured_fixed_amount (reuses `deduction_per_slab` as the
     * flat rupee amount - the task's setting list has no separate "Fixed
     * Amount" field, and this is the field of matching scale/purpose).
     */
    protected function fixedAmount(): float
    {
        return round((float) $this->settings->deduction_per_slab, 2);
    }
}
