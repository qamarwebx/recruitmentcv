<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsCampaign extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function admin(){
        return $this->belongsTo(Admin::class);
    }

    public function smstemp()
    {
        // campaign.sms_temp_id → sms_templates.id
        return $this->belongsTo(SmsTemplate::class, 'sms_temp_id', 'id');
    }
    
    public function scopeFilterSearchText($query, $searchText)
    {
        return $query->when($searchText, function ($query, $searchText) {
            $searchText = '%' . $searchText . '%';
            $query->where(function ($q) use ($searchText) {
                $q->orWhere('id', 'like', $searchText)
                ->orWhere('campaign_name', 'like', $searchText)
                ->orWhere('sms_campaign_name', 'like', $searchText)
                ->orWhere('audience', 'like', $searchText)
                ->orWhereHas('admin', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('smstemp', fn($q) => $q->where('template_name', 'like', $searchText));
            });
        });
    }
}
