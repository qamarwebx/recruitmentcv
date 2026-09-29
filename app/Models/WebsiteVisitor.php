<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One public page view on recruitmentcv.com (partner_id null) or a partner
 * subdomain. Written by RecruitmentCV (TrackWebsiteVisitor middleware),
 * reported in CRM -> Website -> Website Visitor. Kept identical in both
 * codebases.
 */
class WebsiteVisitor extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'partner_id',
        'host',
        'path',
        'route_name',
        'referrer',
        'ip_address',
        'visitor_hash',
        'user_id',
        'browser',
        'platform',
        'device',
        'user_agent',
        'visited_at',
        'visited_on',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'visited_on' => 'date',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
