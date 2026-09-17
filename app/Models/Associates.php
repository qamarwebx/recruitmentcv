<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Associates extends Model
{
    use HasFactory;

    public function careoff(){
        return $this->belongsTo(Admin::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function region(){
        return $this->belongsTo(Region::class);
    }

    public function admin(){
        return $this->belongsTo(Admin::class);
    }

    public function scopeFilterSearchText($query, $searchText)
    {
        return $query->when($searchText, function ($query, $searchText) {
            $searchText = '%' . $searchText . '%';
            $query->where(function ($q) use ($searchText) {
                $q->orWhere('id', 'like', $searchText)
                ->orWhere('pty_full_name', 'like', $searchText)
                ->orWhere('pty_ag_name', 'like', $searchText)
                ->orWhere('pty_email', 'like', $searchText)
                ->orWhere('pty_mobile', 'like', $searchText)
                ->orWhere('address', 'like', $searchText)
                ->orWhere('sec_mob_no', 'like', $searchText)
                ->orWhereHas('careoff', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('city', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('country', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('region', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('admin', fn($q) => $q->where('name', 'like', $searchText));
            });
        });
    }

    // status is boolean (0 = Inactive, 1 = Active) — when($status, ...) would
    // skip filtering for the valid value 0/'0' since it's falsy, so this
    // guards on "was a value actually given" instead of "is it truthy".
    public function scopeFilterStatus($query, $status)
    {
        if ($status === null || $status === '' || (is_array($status) && count($status) === 0)) {
            return $query;
        }
        return $query->whereIn('status', (array) $status);
    }

    // Same boolean-zero-is-valid gotcha as scopeFilterStatus above.
    public function scopeFilterContactVerified($query, $contactVerified)
    {
        if ($contactVerified === null || $contactVerified === '' || (is_array($contactVerified) && count($contactVerified) === 0)) {
            return $query;
        }
        return $query->whereIn('contact_verified', (array) $contactVerified);
    }

    public function scopeFilterCountry($query, $countryId)
    {
        return $query->when($countryId, function ($query, $countryId) {
            $query->whereIn('country_id', (array) $countryId);
        });
    }

    public function scopeFilterCity($query, $cityId)
    {
        return $query->when($cityId, function ($query, $cityId) {
            $query->whereIn('city_id', (array) $cityId);
        });
    }

    public function scopeFilterRegion($query, $regionId)
    {
        return $query->when($regionId, function ($query, $regionId) {
            $query->whereIn('region_id', (array) $regionId);
        });
    }

    public function scopeFilterCreatedBy($query, $adminId)
    {
        return $query->when($adminId, function ($query, $adminId) {
            $query->whereIn('admin_id', (array) $adminId);
        });
    }

    public function scopeFilterCareoff($query, $careoffId)
    {
        return $query->when($careoffId, function ($query, $careoffId) {
            $query->whereIn('careoff_id', (array) $careoffId);
        });
    }

}
