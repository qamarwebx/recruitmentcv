<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceApprovalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_log_id',
        'approval_type',
        'action',
        'previous_status',
        'new_status',
        'admin_id',
        'remarks',
    ];

    public function attendanceLog()
    {
        return $this->belongsTo(AttendanceLog::class, 'attendance_log_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
