<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaLeadLog extends Model
{
    protected $fillable = [
        'lead_id',
        'event_id',
        'status',
        'page',
        'ip',
        'user_agent',
        'message',
        'trace',
    ];
}
