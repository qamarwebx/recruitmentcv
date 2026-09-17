<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledSendMsgAutomation extends Model
{
    use HasFactory;

    protected $table = 'scheduled_send_msg_automation';

    protected $fillable = [
        'template_table_name',
        'template_for',
        'autometanotifications_id',
        'metatemp_id',
        'send_user_to',
        'calculated_time',
        'trigger_template_time',
        'status'
    ];

    // Relation → belongs to AutoMetaNotification
    public function autometanotification()
    {
        return $this->belongsTo(\App\Models\Autometanotification::class, 'autometanotifications_id');
    }

    // Relation → belongs to Meta WhatsApp Template
    public function metatemplate()
    {
        return $this->belongsTo(\App\Models\Metawhatsapptemplate::class, 'metatemp_id');
    }
}
