<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleAccount extends Model
{
    protected $fillable = [
        'admin_id',
        'google_id',
        'name',
        'email',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'last_synced_at',
        'total_contacts',
        'is_active',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'last_synced_at'   => 'datetime',
        'is_active'        => 'boolean',
        'total_contacts'   => 'integer',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}