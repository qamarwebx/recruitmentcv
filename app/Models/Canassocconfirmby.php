<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canassocconfirmby extends Model
{
    use HasFactory;

    public function assoc(){
        return $this->belongsTo(Associates::class);
    }

    public function confirmby(){
        return $this->belongsTo(Admin::class);
    }

    public function cand() {
        return $this->belongsTo(Candidate::class);
    }

}
