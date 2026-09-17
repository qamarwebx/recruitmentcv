<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Lead;
use App\Models\Admin;

class LeadActivityLog extends Model
{
    protected $fillable = [
        'lead_id',
        'admin_id',
        'module',
        'activity',
    ];

    protected $casts = [
        'activity' => 'array',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}