<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCampaignResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_campaign_id',
        'email',
        'status',
        'response_message',
        'error_data_field'
    ];

    public function campaign()
    {
        return $this->belongsTo(EmailCampaign::class, 'email_campaign_id');
    }
}
