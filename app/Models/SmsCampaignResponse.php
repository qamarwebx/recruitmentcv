<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsCampaignResponse extends Model
{
    use HasFactory;

    protected $table = 'sms_campaign_responses';

    protected $fillable = [
        'message_status',
        'message_text',
        'error_data_field',
        'name',
        'mobile_no',
        'lead_id',
        'partner_id',
        'associate_id',
        'client_id',
        'contactp_id',
        'allcontact_id',
        'sms_campaign_id',
        'main_response',
    ];

    protected $casts = [
        'main_response' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // 🔹 Each response belongs to one SMS campaign
    public function campaign()
    {
        return $this->belongsTo(SmsCampaign::class, 'sms_campaign_id');
    }

    // 🔹 Optionally link to related contact sources (if needed)
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function associate()
    {
        return $this->belongsTo(Associates::class, 'associate_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function contactp()
    {
        return $this->belongsTo(Contactplus::class, 'contactp_id');
    }

    public function allcontact()
    {
        return $this->belongsTo(Allcontact::class, 'allcontact_id');
    }
}
