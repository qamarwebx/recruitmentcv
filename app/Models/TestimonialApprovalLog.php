<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestimonialApprovalLog extends Model
{
    use HasFactory;

    /**
     * Microsecond precision so the Activity tab (which merges this table with
     * testimonial_activity_logs and sorts purely by created_at) can reliably
     * order actions fired in quick succession.
     */
    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $fillable = [
        'testimonial_id',
        'action',
        'previous_status',
        'new_status',
        'admin_id',
        'remarks',
    ];

    public function testimonial()
    {
        return $this->belongsTo(Testimonial::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
