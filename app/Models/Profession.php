<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    use HasFactory;

    /**
     * Locale-aware display name - ar_name when the app locale is Arabic and
     * that field is actually set, else eng_name. Matches the same fallback
     * pattern already used by the Arabic front-end (resources/views/arabic/
     * user/fullresumes.blade.php: `$profession->ar_name`).
     */
    public function getDisplayNameAttribute()
    {
        if (app()->getLocale() === 'ar' && !empty($this->ar_name)) {
            return $this->ar_name;
        }

        return $this->eng_name;
    }
}
