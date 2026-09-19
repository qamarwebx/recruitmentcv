<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expecworkcity extends Model
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

    /**
     * Backs the Partner Employer "City of Work" -> Other option
     * (Worker\PartnerPortalController::resolveWorkCityId()): finds an
     * existing city by case-insensitive, trimmed name match, or creates
     * one. "Dubai"/"dubai"/" DUBAI "/etc. always resolve to the same row.
     *
     * The SELECT-then-INSERT here has an inherent TOCTOU race under
     * concurrent identical requests, so it doesn't rely on the SELECT
     * alone for correctness - the unique index on `name` (added
     * alongside this method, same collation already case-insensitive)
     * is the actual safety net. A losing request's INSERT throws a
     * QueryException on that constraint, which is caught and treated as
     * "someone else just created it" by re-selecting that row, rather
     * than surfacing a 500 or leaving a partially-failed write behind.
     */
    public static function findOrCreateByName(string $name, int $adminId): self
    {
        $name = trim($name);

        $existing = static::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])->first();

        if ($existing) {
            return $existing;
        }

        $city = new static();
        $city->name = $name;
        $city->admin_id = $adminId;
        $city->status = 1;

        try {
            $city->save();

            return $city;
        } catch (\Illuminate\Database\QueryException $e) {
            $existing = static::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])->first();

            if ($existing) {
                return $existing;
            }

            throw $e;
        }
    }
}
