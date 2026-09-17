<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportAllContactHistory extends Model
{
    use HasFactory;

    protected $table = 'export_all_contact_histories';

    protected $fillable = [
        'admin_id',
        'file_name',
        'file_path',
        'status',
        'columns',
        'contact_ids',
        'is_all_export',
        'total_records',
        'exported_records',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'columns'            => 'array',
        'is_all_export'      => 'boolean',
        'started_at'         => 'datetime',
        'completed_at'       => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}