<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaAdsStoreRecord extends Model
{
    protected $table = 'meta_ads_store_records';

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'event',
        'page',
        'source',
        'platform',
        'country',
        'country_code',
        'city',
        'event_id',
        'pixel_id',
        'fbtrace_id',
        'ip_address',
        'user_agent',
    ];
}
