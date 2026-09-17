<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Whatsappapi extends Model
{
    use HasFactory;

    public function createby(){
        return $this->belongsTo(Admin::class);
    }

    public function staff(){
        return $this->belongsTo(Admin::class);
    }
}
