<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_name',
        'audience',
        'smtp_id',
        'email_template_id',
        'admin_id',
        'email_subject',
        'email_body',
        'attachment',
        'email_status',
        'email_response',
        'schedule_type',
        'schedule_datetime',
        'raw_request_data',
        'careoff_id',
        'group_id',
        'field_var',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    public function emailTemplate()
    {
        return $this->belongsTo(EmailTemplate::class, 'email_template_id');
    }

    public function smtp()
    {
        return $this->belongsTo(EmailSmtp::class, 'smtp_id');
    }

    public function responses()
    {
        return $this->hasMany(EmailCampaignResponse::class, 'email_campaign_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeFilterSearchText($query, $text)
    {
        if ($text) {
            $query->where(function ($q) use ($text) {
                $q->where('campaign_name', 'like', "%{$text}%")
                  ->orWhere('email_subject', 'like', "%{$text}%")
                  ->orWhere('audience', 'like', "%{$text}%");
            });
        }
    }
}
