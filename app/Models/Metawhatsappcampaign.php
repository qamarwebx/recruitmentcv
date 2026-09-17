<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metawhatsappcampaign extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function admin(){
        return $this->belongsTo(Admin::class);
    }

    public function metatemp(){
        return $this->belongsTo(Metawhatsapptemplate::class);
    }

    public function scopeFilterSearchText($query, $searchText)
    {

        return $query->when($searchText, function ($query, $searchText) {
            $searchText = '%' . $searchText . '%';
            $query->where(function ($q) use ($searchText) {
                $q->orWhere('id', 'like', $searchText)
                ->orWhere('campaign_name', 'like', $searchText)
                ->orWhere('meta_campaign_name', 'like', $searchText)
                ->orWhere('audience', 'like', $searchText)
                ->orWhereHas('admin', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('metatemp', fn($q) => $q->where('template_name', 'like', $searchText));
            });
        });
    }

    public function metaapi()
    {
        return $this->belongsTo(\App\Models\Metawhatsappapi::class, 'metaapi_id');
    }

}
