<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getDisplayStatusAttribute()
    {
        if (app()->getLocale() === 'ar' && !empty($this->ar_status)) {
            return $this->ar_status;
        }

        return $this->ord_status;
    }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }
    public function candidate()
    {
        return $this->hasMany(Candidate::class);
    }
}
