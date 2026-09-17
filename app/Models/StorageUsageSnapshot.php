<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageUsageSnapshot extends Model
{
    use HasFactory;

    const UNCATEGORIZED_KEY = 'uncategorized';

    protected $fillable = [
        'module_key',
        'label',
        'file_count',
        'size_bytes',
        'last_calculated_at',
    ];

    protected $casts = [
        'file_count' => 'integer',
        'size_bytes' => 'integer',
        'last_calculated_at' => 'datetime',
    ];
}
