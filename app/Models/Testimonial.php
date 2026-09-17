<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'passport_number',
        'uploaded_video',
        'uploading_video_max_limit',
        'link',
        'created_by',
        'approval_status',
        'payment_status',
        'payment_slip',
        'paid_amount',
        'paid_by',
        'paid_at',
        'video_received_status',
        'social_media_status',
        'social_media_platforms',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'paid_amount' => 'decimal:2',
        'social_media_platforms' => 'array',
    ];

    const APPROVAL_PENDING = 'Pending';
    const APPROVAL_APPROVED = 'Approved';
    const APPROVAL_REJECTED = 'Rejected';

    const PAYMENT_PENDING = 'Pending';
    const PAYMENT_PAID = 'Paid';

    const VIDEO_NOT_RECEIVED = 'Not Received';
    const VIDEO_RECEIVED = 'Received';

    const SOCIAL_NOT_POSTED = 'Not Posted';
    const SOCIAL_POSTED = 'Posted';

    const SOCIAL_MEDIA_PLATFORMS = [
        'Instagram',
        'YouTube Videos',
        'YouTube Shorts',
        'Facebook',
        'LinkedIn',
    ];


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
        // id as a tiebreaker: two actions can land in the same second, and
        // ordering by created_at alone doesn't guarantee stable order then.
        return $this->hasMany(TestimonialApprovalLog::class)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');
    }

}
