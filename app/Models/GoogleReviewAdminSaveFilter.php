<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleReviewAdminSaveFilter extends Model
{
    use HasFactory;

    protected $table = 'google_review_admin_save_filters';

    protected $fillable = [
        'admin_id',
        'by_branch',
        'by_created_by',
        'by_date_range',
        'by_approval_status',
        'by_payment_status',
    ];

    public function getByBranchArrayAttribute()
    {
        return $this->by_branch ? explode(',', $this->by_branch) : [];
    }

    public function getByCreatedByArrayAttribute()
    {
        return $this->by_created_by ? explode(',', $this->by_created_by) : [];
    }

    public function getByApprovalStatusArrayAttribute()
    {
        return $this->by_approval_status ? explode(',', $this->by_approval_status) : [];
    }

    public function getByPaymentStatusArrayAttribute()
    {
        return $this->by_payment_status ? explode(',', $this->by_payment_status) : [];
    }
}
