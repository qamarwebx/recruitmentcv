<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Normalwhatsappcampaignresponse extends Model
{
    use HasFactory;


    public function campaignlist(){
        return $this->belongsTo(Campaignlist::class);
    }

}
