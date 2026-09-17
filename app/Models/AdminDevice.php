<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'device_id',
        'device_type',
        'browser',
        'os',
        'ip_address',
        'location',
        'latitude',
        'longitude',
        'accuracy',
        'is_approved',
        'approved_by_id', 
        'approved_by_device_type', 
        'approved_by_browser',
        'approved_by_os', 
        'approved_by_ip', 
        'approved_by_location',
        'comments',
        'login_status',
    ];

    /**
     * Relationship with Admin
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Relationship with Admin who approved the device
     */
    public function approvedBy()
    {
        return $this->belongsTo(Admin::class, 'approved_by_id');
    }

    /**
     * Accessor for device status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_approved ? 'Approved' : 'Pending';
    }
}
