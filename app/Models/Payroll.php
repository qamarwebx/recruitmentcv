<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'admin_id',
        'month',
        'year',
        'monthly_salary',
        'daily_salary',
        'days_in_month',
        'present_days',
        'absent_days',
        'half_days',
        'qualifying_late_count',
        'late_count',
        'holiday_days',
        'late_deduction',
        'absent_deduction',
        'half_day_deduction',
        'sunday_deduction',
        'total_deduction',
        'net_payable',
        'status',
        'generated_by',
        'generated_at',
        'locked_by',
        'locked_at',
        'notes',
        'payment_status',
        'payment_slip',
        'paid_by',
        'paid_at',
        'extra_paid',
        'advance_deduction',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'locked_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    const PAYMENT_PENDING = 'Pending';
    const PAYMENT_PAID = 'Paid';

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function generatedBy()
    {
        return $this->belongsTo(Admin::class, 'generated_by');
    }

    public function lockedBy()
    {
        return $this->belongsTo(Admin::class, 'locked_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(Admin::class, 'paid_by');
    }

    /**
     * Advance Payments currently linked to this payroll (only ever linked
     * while this payroll is a Draft - see AdvancePaymentService). Their sum
     * is kept in sync on Payroll::advance_deduction, so this relation is
     * used for the Payment Details breakdown, not for the total itself.
     */
    public function advancePayments()
    {
        return $this->hasMany(AdvancePayment::class, 'payroll_id');
    }

    /**
     * Total Paid = Net Payable + Any Extra Paid - Total Advance Payment
     * (single source of truth so the listing column and anything else that
     * needs this figure never drift out of sync).
     */
    public function getTotalPaidAttribute(): float
    {
        return round((float) $this->net_payable + (float) $this->extra_paid - (float) $this->advance_deduction, 2);
    }

    public function scopeFilterStaff($query, $adminId)
    {
        return $query->when($adminId, fn ($query, $adminId) => $query->whereIn('admin_id', (array) $adminId));
    }

    public function scopeFilterStatus($query, $status)
    {
        return $query->when($status, fn ($query, $status) => $query->whereIn('payrolls.status', (array) $status));
    }

    public function scopeFilterPaymentStatus($query, $paymentStatus)
    {
        return $query->when($paymentStatus, fn ($query, $paymentStatus) => $query->whereIn('payrolls.payment_status', (array) $paymentStatus));
    }

    public function scopeFilterMonth($query, $month)
    {
        return $query->when($month, fn ($query, $month) => $query->where('month', $month));
    }

    public function scopeFilterYear($query, $year)
    {
        return $query->when($year, fn ($query, $year) => $query->where('year', $year));
    }

    public function scopeFilterGeneratedFrom($query, $date)
    {
        return $query->when($date, fn ($query, $date) => $query->whereDate('generated_at', '>=', $date));
    }

    public function scopeFilterGeneratedTo($query, $date)
    {
        return $query->when($date, fn ($query, $date) => $query->whereDate('generated_at', '<=', $date));
    }

    public function scopeFilterSearchText($query, $searchText)
    {
        return $query->when($searchText, function ($query, $searchText) {
            $query->whereHas('admin', fn ($q) => $q->where('name', 'like', '%' . $searchText . '%'));
        });
    }
}
