<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One tracked partner action on a candidate (App\Support\PartnerActivity):
 * a Candidate Detail visit being timed for the 1-minute alert, or a Hire Now
 * / Download CV click. partner_id / candidate_id are always set server-side
 * from the signed-in partner and the candidate it may open. Twin file in
 * both apps.
 */
class PartnerActivityEvent extends Model
{
    protected $fillable = [
        'partner_id', 'team_member_id', 'candidate_id', 'event', 'token', 'status',
        'started_at', 'last_seen_at', 'notified_at', 'ip',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'notified_at' => 'datetime',
    ];
}
