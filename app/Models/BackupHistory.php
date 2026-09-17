<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupHistory extends Model
{
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'type',
        'disk',
        'path',
        'filename',
        'file_size',
        'status',
        'error_message',
        'generated_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'generated_at' => 'datetime',
    ];

    public function driveHistory()
    {
        return $this->hasOne(BackupDriveHistory::class, 'backup_history_id');
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }
}
