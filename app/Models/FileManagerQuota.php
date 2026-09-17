<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileManagerQuota extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'admin_id',
        'allocated_bytes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'allocated_bytes' => 'integer',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
