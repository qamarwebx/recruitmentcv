<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contactp extends Model
{
    use HasFactory;

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function contactstatus(){
        return $this->belongsTo(Contactstatus::class);
    }

    public function ls(){
        return $this->belongsTo(Leadstage::class);
    }

    public function lcs(){
        return $this->belongsTo(Lifecyclestatus::class);
    }

    public function businesstype(){
        return $this->belongsTo(Businesstype::class);
    }

    public function groupm(){
        return $this->belongsTo(Groupm::class);
    }

}
