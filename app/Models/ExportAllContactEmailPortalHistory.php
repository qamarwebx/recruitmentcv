<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportAllContactEmailPortalHistory extends Model
{
    use HasFactory;

    protected $table = 'export_all_contact_email_portal_histories';

    protected $fillable = [
        'admin_id',
        'api_token',
        'list_uid',
        'list_name',
        'business_type',
        'industry_ids',
        'industry_names',
        'filters',
        'status',
        'total_records',
        'pending_count',
        'success_count',
        'failed_count',
        'error_message',
        'started_at',
        'completed_at',
        'last_refreshed_at',
    ];

    protected $casts = [
        'api_token'         => 'encrypted',
        'industry_ids'      => 'array',
        'filters'           => 'array',
        'started_at'        => 'datetime',
        'completed_at'      => 'datetime',
        'last_refreshed_at' => 'datetime',
    ];

    protected $hidden = [
        'api_token',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
