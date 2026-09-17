<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadAdminSaveFilter extends Model
{
    use HasFactory;

    protected $table = 'lead_admin_save_filters';

    protected $fillable = [
        'admin_id',
        'by_assignee',
        'by_lead_date',
        'by_is_qualified',
        'by_driving_license',
        'by_job_title',
        'by_expected_days',
        'by_expected_country',
        'by_submit_from',
        'by_location',
        'by_updated_date',
        'by_followup_before',
        'by_looking_for',
        'staff_updated_at',
        'custom_filters'
    ];

    protected $casts = [
        'custom_filters' => 'array',
    ];

    // Helper accessors for array conversion
    public function getByAssigneeArrayAttribute()
    {
        return $this->by_assignee ? explode(',', $this->by_assignee) : [];
    }

    public function getByIsQualifiedArrayAttribute()
    {
        return $this->by_is_qualified
            ? explode(',', $this->by_is_qualified)
            : [];
    }

    public function getByDrivingLicenseArrayAttribute()
    {
        return $this->by_driving_license ? explode(',', $this->by_driving_license) : [];
    }

    public function getByJobTitleArrayAttribute()
    {
        return $this->by_job_title ? explode(',', $this->by_job_title) : [];
    }

    public function getByExpectedDaysArrayAttribute()
    {
        return $this->by_expected_days ? explode(',', $this->by_expected_days) : [];
    }

    public function getByExpectedCountryArrayAttribute()
    {
        return $this->by_expected_country ? explode(',', $this->by_expected_country) : [];
    }

    public function getByCallNotConnectedTypeArrayAttribute()
    {
        return $this->by_call_not_connected_type
            ? explode(',', $this->by_call_not_connected_type)
            : [];
    }
}
