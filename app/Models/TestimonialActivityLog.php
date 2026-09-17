<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestimonialActivityLog extends Model
{
    /**
     * Microsecond precision so the Activity tab (which merges this table with
     * testimonial_approval_logs and sorts purely by created_at) can reliably
     * order actions fired in quick succession — second precision isn't
     * enough once two independently auto-incrementing tables are involved.
     */
    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $fillable = [
        'testimonial_id',
        'admin_id',
        'module',
        'activity',
    ];

    protected $casts = [
        'activity' => 'array',
    ];

    public function testimonial()
    {
        return $this->belongsTo(Testimonial::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
