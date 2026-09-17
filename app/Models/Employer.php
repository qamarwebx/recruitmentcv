<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    use HasFactory;

    /**
     * Get the visadetails that owns the Employer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function visadetails()
    {
        return $this->belongsTo(Visadetails::class);
    }

    /**
     * Get the wpcity that owns the Employer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wpcity()
    {
        return $this->belongsTo(Expecworkcity::class);
    }


    /**
     * Get the admin that owns the Employer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the partner that owns the Employer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the booking that owns the Employer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user that owns the Employer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the partneroffice that owns the Employer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partneroffice()
    {
        return $this->belongsTo(Partner::class);
    }


    /**
     * Get the careoff that owns the Employer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function careoff()
    {
        return $this->belongsTo(Admin::class);
    }


    public function scopeFilterpartnerOffice($query, $partner_office) {
        return $query->when($partner_office, function ($query, $partner_office) {
            $query->whereIn('partneroffice_id', (array) $partner_office);
        });
    }

    public function scopeFilterCreateByPartner($query, $created_by_partner) {
        return $query->when($created_by_partner, function ($query, $created_by_partner) {
            $query->whereIn('partner_id', (array) $created_by_partner);
        });
    }


    public function scopeFilterWakalaStatus($query, $wakalastatus) {
        return $query->when($wakalastatus, function ($query, $wakalastatus) {
            $query->whereIn('wakala_status', (array) $wakalastatus);
        });
    }

    // status is boolean (0 = Inactive, 1 = Active) — when($status, ...) would
    // skip filtering for the valid value 0/'0' since it's falsy, so this
    // guards on "was a value actually given" instead of "is it truthy".
    public function scopeFilterStatus($query, $status) {
        if ($status === null || $status === '' || (is_array($status) && count($status) === 0)) {
            return $query;
        }
        return $query->whereIn('status', (array) $status);
    }


    public function scopeFilterCareoff($query, $careoff) {
        return $query->when($careoff, function ($query, $careoff) {
            $query->whereIn('careoff_id', (array) $careoff);
        });
    }

    public function scopeFilterCreatedBy($query, $created_by) {
        return $query->when($created_by, function ($query, $created_by) {
            $query->whereIn('admin_id', (array) $created_by);
        });
    }

    public function scopeFilterBusinessType($query, $businesstype) {
        return $query->when($businesstype, function ($query, $businesstype) {
            $query->whereIn('businesstype', (array) $businesstype);
        });

    }

    public function scopeFilterCityOfWork($query, $city_of_work) {
        return $query->when($city_of_work, function ($query, $city_of_work) {
            $query->whereIn('wpcity_id', (array) $city_of_work);
        });
    }

    public function scopeFilterVisaIssuingAuthority($query, $issuing_authority) {
        return $query->when($issuing_authority, function ($query, $issuing_authority) {
            $query->whereIn('issuing_authority', (array) $issuing_authority);
        });
    }

    public function scopeFilterProfession($query, $profession_id)
    {
        return $query->when($profession_id, function ($query, $profession_id) {
            $profession_ids = is_array($profession_id) ? $profession_id : [$profession_id];
            $query->where(function ($q) use ($profession_ids) {
                foreach ($profession_ids as $id) {
                    $q->orWhereRaw("FIND_IN_SET(?, proff_id)", [$id]);
                }
            });
        });
    }

    public function scopeFilterDateRange($query, $column, $date_range)
    {
        return $query->when($date_range, function ($query) use ($column, $date_range) {
            [$start, $end] = array_map('trim', explode('-', $date_range));
            $query->whereBetween($column, [date('Y-m-d', strtotime($start)), date('Y-m-d', strtotime($end))]);
        });
    }


    public function scopeFilterSearchText($query, $searchText)
    {
        return $query->when($searchText, function ($query, $searchText) {
            $searchText = '%' . $searchText . '%';
            $query->where(function ($q) use ($searchText) {
                $q->orWhere('id', 'like', $searchText)
                ->orWhere('employer_name', 'like', $searchText)
                ->orWhere('employer_ar_name', 'like', $searchText)
                ->orWhere('visa_no', 'like', $searchText)
                ->orWhere('id_no', 'like', $searchText)
                ->orWhere('issuing_authority', 'like', $searchText)
                ->orWhere('salary', 'like', $searchText)
                ->orWhere('businesstype', 'like', $searchText)
                ->orWhere('visa_date', 'like', $searchText)
                ->orWhere('mobile_no', 'like', $searchText)
                ->orWhere('wakala_status', 'like', $searchText)
                ->orWhere('visa_received_date', 'like', $searchText)
                ->orWhereHas('wpcity', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('partneroffice', fn($q) => $q->where('rec_off_name', 'like', $searchText))
                ->orWhereHas('admin', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('careoff',fn($q) => $q->where('name','like', $searchText));
            });
        });
    }

}
