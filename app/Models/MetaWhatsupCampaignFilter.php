<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaWhatsupCampaignFilter extends Model
{
    protected $table = 'meta_whatsup_campaign_filters';

    protected $fillable = [
        'admin_id',
        'audience',
        'careoff',
        'group',
        'created_by',
        'lead_date_range',
        'send_date',
        'status',
        'sch_type'
    ];

    protected $casts = [
        'audience'   => 'array',
        'careoff'    => 'array',
        'group'      => 'array',
        'created_by' => 'array',
    ];
}
