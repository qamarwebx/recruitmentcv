<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleReviewApprovalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'google_review_id',
        'action',
        'previous_status',
        'new_status',
        'admin_id',
        'remarks',
    ];

    public function googleReview()
    {
        return $this->belongsTo(GoogleReview::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
