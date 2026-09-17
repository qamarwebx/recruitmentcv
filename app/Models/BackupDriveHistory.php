<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupDriveHistory extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_UPLOADED = 'uploaded';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'backup_history_id',
        'backup_reference',
        'type',
        'file_size',
        'google_account_email',
        'drive_file_id',
        'drive_folder_id',
        'drive_view_link',
        'status',
        'error_message',
        'uploaded_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'uploaded_at' => 'datetime',
    ];

    public function backup()
    {
        return $this->belongsTo(BackupHistory::class, 'backup_history_id');
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeUploaded($query)
    {
        return $query->where('status', self::STATUS_UPLOADED);
    }

    public function isUploaded(): bool
    {
        return $this->status === self::STATUS_UPLOADED;
    }
}
