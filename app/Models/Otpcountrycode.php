<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otpcountrycode extends Model
{
    use HasFactory;

    public function getDisplayNameAttribute()
    {
        if (app()->getLocale() === 'ar' && !empty($this->ar_name)) {
            return $this->ar_name;
        }

        return $this->name;
    }
}
