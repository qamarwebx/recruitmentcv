<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmCandidate extends Model
{
    use HasFactory;

    public function prof()
    {
        return $this->belongsTo(Profession::class,'prof_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function followup()
    {
        return $this->belongsTo(FollowUpStage::class);
    }

    public function workstatus()
    {
        return $this->belongsTo(WorkStatus::class);
    }

}
