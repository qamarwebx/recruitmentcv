<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class Booking extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }  

    public function workLocation()
    {
        return $this->belongsTo(Expecworkcity::class, 'worklocation', 'id');
    }  

    
    
    
}
