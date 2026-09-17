<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupSetting extends Model
{
    protected $fillable = [
        'daily_enabled',
        'daily_time',
        'daily_retention',
        'interval_enabled',
        'interval_value',
        'interval_retention',
    ];

    protected $casts = [
        'daily_enabled' => 'boolean',
        'daily_retention' => 'integer',
        'interval_enabled' => 'boolean',
        'interval_value' => 'integer',
        'interval_retention' => 'integer',
    ];

    /**
     * This module has exactly one settings row. Everything reads/writes
     * through here instead of querying the table directly.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }
}
