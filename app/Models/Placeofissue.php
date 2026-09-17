<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Placeofissue extends Model
{
    use HasFactory;

    /**
     * Locale-aware display name - arname when the app locale is Arabic and
     * that field is actually set, else name. Matches the same fallback
     * pattern already used by the Arabic front-end (resources/views/arabic/
     * user/fullresumes.blade.php: `$nation->arname`, `$region->arname`, etc.)
     */
    public function getDisplayNameAttribute()
    {
        if (app()->getLocale() === 'ar' && !empty($this->arname)) {
            return $this->arname;
        }

        return $this->name;
    }
}
