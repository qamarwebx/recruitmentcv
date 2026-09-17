<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookAccount extends Model
{
    protected $table = 'facebook_accounts';

    protected $fillable = [
        'account_name',
        'business_manager_id',
        'pixel_id',
        'capi_access_token',
        'test_event_code',
        'meta_pixel_base_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope: Only active accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
    