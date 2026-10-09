<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metawhatsapplog extends Model
{
    use HasFactory;

    protected static function booted()
    {
        // Every RecruitmentCV mobile OTP send writes one of these rows: also
        // listed in CRM -> Website -> Settings -> Notification Logs.
        static::created(function (self $log) {
            if ($log->message_for === 'OTP') {
                \App\Support\NotificationCenter::recordMobileOtp($log->message_status, $log->template_name, $log->message_text);
            }
        });
    }
}
