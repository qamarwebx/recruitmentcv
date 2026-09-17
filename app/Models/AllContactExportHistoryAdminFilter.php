<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllContactExportHistoryAdminFilter extends Model
{
    use HasFactory;

    protected $table = 'all_contact_export_history_admin_filters';

    protected $fillable = [
        'admin_id',
        'status',
        'filter_admin_id',
        'list_name',
        'business_type',
        'created_date',
    ];
}
