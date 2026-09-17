<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'screenshot',
        'created_by',
        'approval_status',
        'payment_status',
        'payment_slip',
        'paid_amount',
        'paid_by',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'paid_amount' => 'decimal:2',
    ];

    const APPROVAL_PENDING = 'Pending';
    const APPROVAL_APPROVED = 'Approved';
    const APPROVAL_REJECTED = 'Rejected';

    const PAYMENT_PENDING = 'Pending';
    const PAYMENT_PAID = 'Paid';

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'google_review_branch');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(Admin::class, 'paid_by');
    }

    public function approvalLogs()
    {
        return $this->hasMany(GoogleReviewApprovalLog::class)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');
    }
}
