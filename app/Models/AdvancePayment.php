<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancePayment extends Model
{
    protected $fillable = [
        'admin_id',
        'payroll_id',
        'month',
        'year',
        'advance_date',
        'amount',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'advance_date' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Pending - no payroll exists yet (or it was skipped for the reason
     * below) for this advance's month/year.
     * Applied - linked to that period's payroll while it's still a Draft.
     * Locked - the linked payroll has since been finalized; the advance's
     * amount is frozen into Payroll::advance_deduction and can no longer be
     * edited/deleted (see AdvancePaymentService::guardAgainstLockedPayroll).
     * Paid - the linked payroll has also been marked Paid.
     */
    public function getStatusAttribute(): string
    {
        if (!$this->payroll_id) {
            return 'Pending';
        }

        $payroll = $this->relationLoaded('payroll')
            ? $this->payroll
            : $this->payroll()->first(['id', 'status', 'payment_status']);

        if (!$payroll) {
            return 'Pending';
        }

        if ($payroll->payment_status === Payroll::PAYMENT_PAID) {
            return 'Paid';
        }

        return $payroll->status === 'locked' ? 'Locked' : 'Applied';
    }

    public function scopeFilterStaff($query, $adminId)
    {
        return $query->when($adminId, fn ($query, $adminId) => $query->whereIn('admin_id', (array) $adminId));
    }
}
