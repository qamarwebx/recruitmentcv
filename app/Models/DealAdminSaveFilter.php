<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealAdminSaveFilter extends Model
{
    use HasFactory;

    protected $table = 'deal_admin_save_filters';

    protected $fillable = [
        'admin_id',
        'priority',
        'business_id',
        'recruite_status_id',
        'associate_id',
        'job_title_id',
        'care_of',
        'source',
        'created_date',
        'updated_date',
        'created_by',
        'switch_to',
        'followup_before'
    ];

    // Helper to get comma-separated values as array
    public function getBusinessIdArrayAttribute()
    {
        return $this->business_id ? explode(',', $this->business_id) : [];
    }

    public function getAssociateIdArrayAttribute()
    {
        return $this->associate_id ? explode(',', $this->associate_id) : [];
    }

    public function getCareOfIdArrayAttribute()
    {
        return $this->care_of ? explode(',', $this->care_of) : [];
    }

    public function getSourceArrayAttribute()
    {
        return $this->source ? explode(',', $this->source) : [];
    }

    public function getCreatedByArrayAttribute()
    {
        return $this->created_by ? explode(',', $this->created_by) : [];
    }
}
