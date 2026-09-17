<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledSendEmailAutomation extends Model
{
    protected $table = 'scheduled_send_email_automation';

    protected $fillable = [
        'template_table_name',
        'template_for',
        'autoemailnotifications_id',
        'emailtemp_id',
        'send_user_to',
        'calculated_time',
        'trigger_template_time',
        'status',
    ];

    public function autoemailnotification()
    {
        return $this->belongsTo(Autoemailnotification::class, 'autoemailnotifications_id');
    }

    public function emailtemplate()
    {
        return $this->belongsTo(EmailTemplate::class, 'emailtemp_id');
    }
}
