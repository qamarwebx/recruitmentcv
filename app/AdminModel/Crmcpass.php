<?php

namespace App\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crmcpass extends Model
{
    use HasFactory;

    public function poi()
    {
        return $this->belongsTo(CandidatePlaceIssue::class);
    }

    
}
