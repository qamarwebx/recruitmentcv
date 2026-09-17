<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'attendance_type',
        'attendance_time',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'final_status',
        'final_approved_by',
        'final_approved_at',
        'final_rejection_reason',
    ];

    protected $casts = [
        'attendance_time' => 'datetime',
        'approved_at' => 'datetime',
        'final_approved_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function finalApprover()
    {
        return $this->belongsTo(Admin::class, 'final_approved_by');
    }

    public function approvalLogs()
    {
        return $this->hasMany(AttendanceApprovalLog::class, 'attendance_log_id')->latest();
    }

    /**
     * Counts toward salary only once both approval phases are complete -
     * anything short of that is treated as not payable (same as Absent).
     */
    public function getIsFullyApprovedAttribute(): bool
    {
        return $this->status === 'approved' && $this->final_status === 'approved';
    }

    public function scopeFilterStaff($query, $adminId)
    {
        return $query->when($adminId, function ($query, $adminId) {
            $query->whereIn('admin_id', (array) $adminId);
        });
    }

    public function scopeFilterType($query, $type)
    {
        return $query->when($type, function ($query, $type) {
            $query->whereIn('attendance_type', (array) $type);
        });
    }

    public function scopeFilterStatus($query, $status)
    {
        return $query->when($status, function ($query, $status) {
            $query->whereIn('status', (array) $status);
        });
    }

    public function scopeFilterApprovedBy($query, $approvedBy)
    {
        return $query->when($approvedBy, function ($query, $approvedBy) {
            $query->whereIn('approved_by', (array) $approvedBy);
        });
    }

    public function scopeFilterDateFrom($query, $date)
    {
        return $query->when($date, function ($query, $date) {
            $query->whereDate('attendance_time', '>=', $date);
        });
    }

    public function scopeFilterDateTo($query, $date)
    {
        return $query->when($date, function ($query, $date) {
            $query->whereDate('attendance_time', '<=', $date);
        });
    }

    public function scopeFilterSearchText($query, $searchText)
    {
        return $query->when($searchText, function ($query, $searchText) {
            $searchText = '%' . $searchText . '%';
            $query->where(function ($q) use ($searchText) {
                $q->orWhere('status', 'like', $searchText)
                  ->orWhereHas('admin', fn ($q) => $q->where('name', 'like', $searchText));
            });
        });
    }
}
