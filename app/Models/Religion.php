<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    use HasFactory;

    protected $table = 'religions';

    /**
     * Locale-aware display name - arbname when the app locale is Arabic and
     * that field is actually set, else name.
     */
    public function getDisplayNameAttribute()
    {
        if (app()->getLocale() === 'ar' && !empty($this->arbname)) {
            return $this->arbname;
        }

        return $this->name;
    }
}
