<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Whatsappchaturl extends Model
{
    use HasFactory;

    public function staff(){
        return $this->belongsTo(Admin::class);
    }

}
