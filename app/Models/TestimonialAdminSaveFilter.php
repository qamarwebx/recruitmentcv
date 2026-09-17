<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestimonialAdminSaveFilter extends Model
{
    use HasFactory;

    protected $table = 'testimonial_admin_save_filters';

    protected $fillable = [
        'admin_id',
        'by_full_name',
        'by_passport_number',
        'by_created_by',
        'by_date_range',
        'by_approval_status',
        'by_payment_status',
        'by_video_received',
        'by_social_media_status',
    ];

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

    public function getByVideoReceivedArrayAttribute()
    {
        return $this->by_video_received ? explode(',', $this->by_video_received) : [];
    }

    public function getBySocialMediaStatusArrayAttribute()
    {
        return $this->by_social_media_status ? explode(',', $this->by_social_media_status) : [];
    }
}
