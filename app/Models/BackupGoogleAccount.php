<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupGoogleAccount extends Model
{
    const STATUS_DISCONNECTED = 'disconnected';
    const STATUS_CONNECTED = 'connected';
    const STATUS_ERROR = 'error';

    protected $fillable = [
        'enabled',
        'google_id',
        'google_email',
        'google_name',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'scope',
        'drive_folder_id',
        'drive_folder_name',
        'status',
        'last_error',
        'last_tested_at',
        'connected_at',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        // Encrypted at rest - never exposed to Blade/AJAX/logs, see
        // App\Http\Controllers\BackupController::presentDriveAccount().
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'token_expires_at' => 'datetime',
        'last_tested_at' => 'datetime',
        'connected_at' => 'datetime',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    /**
     * This feature has exactly one settings/connection row, the same
     * pattern as BackupSetting::current().
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }

    public function isConnected(): bool
    {
        return $this->status === self::STATUS_CONNECTED && !empty($this->refresh_token);
    }

    public function isReadyToUpload(): bool
    {
        return (bool) $this->enabled && $this->isConnected();
    }
}
