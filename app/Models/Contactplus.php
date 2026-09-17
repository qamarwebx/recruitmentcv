<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contactplus extends Model
{
    use HasFactory;

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function contactstatus(){
        return $this->belongsTo(Contactstatus::class);
    }

    public function ls(){
        return $this->belongsTo(Leadstage::class);
    }

    public function lcs(){
        return $this->belongsTo(Lifecyclestatus::class);
    }

    public function businesstype(){
        return $this->belongsTo(Businesstype::class);
    }

    public function groupm(){
        return $this->belongsTo(Groupm::class);
    }

    public function group(){
        return $this->belongsTo(Groupm::class);
    }

    // Local Scope
    public function scopeFilterCountry($query,$country_id){
        return $query->when($country_id, function($query,$country_id){
            $query->whereIn('country_id',(array) $country_id);
        });
    }

    public function scopeFilterCity($query,$city_id){
        return $query->when($city_id, function($query,$city_id){
            $query->whereIn('city_id',(array) $city_id);
        });
    }

    public function scopeFilterLcs($query,$lcs_id){
        return $query->when($lcs_id, function($query,$lcs_id){
            $query->whereIn('lcs_id',(array) $lcs_id);
        });
    }

    public function scopeFilterLs($query,$ls_id){
        return $query->when($ls_id, function($query,$ls_id){
            $query->whereIn('ls_id',(array) $ls_id);
        });
    }

    public function scopeFilterBusinesstype($query,$businesstype_id){
        return $query->when($businesstype_id, function($query,$businesstype_id){
            $query->whereIn('businesstype_id',(array) $businesstype_id);
        });
    }

    public function scopeFilterIndustry($query,$industry_id){
        return $query->when($industry_id, function($query,$industry_id){
            $query->whereIn('industry_id',(array) $industry_id);
        });
    }

    public function scopeFilterSubscribe($query, $subscribe){
        return $query->when($subscribe, function($query, $subscribe){
            $query->whereIn('subscribe', (array) $subscribe);
        });
    }

    public function scopeFilterStatus($query, $status){
        return $query->when(filled($status), function($query) use ($status){
            $query->whereIn('status', (array) $status);
        });
    }

    // public function scopeFilterGroup($query,$group_id){
    //     return $query->when($group_id,function($query,$group_id){


    //         $query->whereIn('group_id',(array) $group_id);
    //     });



    // }


    public function scopeFilterGroup($query,$group_id){
        if (filled($group_id)) {

            // $groupIDS = explode(",",$group_id);
            if (is_string($group_id)) {
                $group_id = explode(",",$group_id);
            }


            $index = array_search('gb0', $group_id);

            if ($index !== false) {
                $group_id[$index] = null;
                return $query->where(function($query) use($group_id){
                    $query->whereIn('group_id', $group_id)->orWhereNull('group_id');
                });
            } else {
                return $query->whereIn('group_id', $group_id);
            }


            // if ($index !== false) {
            //     $group_id[$index] = null;
            //     return $query->whereIn('group_id', $group_id)->whereNull('group_id');
            // }else{
            //     return $query->whereIn('group_id', $group_id);
            // }

        }


        return $query;


    }



    public function scopeFilterCreatedBy($query,$staff_id){
        return $query->when($staff_id, function($query,$staff_id){
            $query->whereIn('staff_id',(array) $staff_id);
        });
    }


    public function scopeFilterSendTag($query, $send_tag){
        return $query->when($send_tag, function($query, $send_tag){
            $query->whereIn('send_tag',(array) $send_tag);
        });
    }


    public function scopeFilterDateRange($query, $column, $date_range)
    {
        return $query->when($date_range, function ($query) use ($column, $date_range) {
            [$start, $end] = array_map('trim', explode('-', $date_range));
            $query->whereBetween($column, [date('Y-m-d', strtotime($start)), date('Y-m-d', strtotime($end))]);
        });
    }

    public function scopeFilterDate($query, $column, $date)
    {
        return $query->when($date, function ($query) use ($column, $date) {
            $query->whereDate($column, $date);
        });
    }

    public function scopeFilterSearchText($query, $searchText)
    {
        return $query->when($searchText, function ($query, $searchText) {
            $searchText = '%' . $searchText . '%';
            $query->where(function ($q) use ($searchText) {
                $q->orWhere('id', 'like', $searchText)
                ->orWhere('prim_concern_name', 'like', $searchText)
                ->orWhere('office_eng_name', 'like', $searchText)
                ->orWhere('office_ar_name', 'like', $searchText)
                ->orWhere('prim_contact', 'like', $searchText)
                ->orWhere('sec_concern_name', 'like', $searchText)
                ->orWhere('concern_name3', 'like', $searchText)
                ->orWhere('concern_name4', 'like', $searchText)
                ->orWhere('concern_name5', 'like', $searchText)
                ->orWhere('concern_name6', 'like', $searchText)
                ->orWhere('sec_contact', 'like', $searchText)
                ->orWhere('contact3', 'like', $searchText)
                ->orWhere('contact4', 'like', $searchText)
                ->orWhere('contact5', 'like', $searchText)
                ->orWhere('contact6', 'like', $searchText)
                ->orWhere('contact7', 'like', $searchText)
                ->orWhere('contact8', 'like', $searchText)
                ->orWhere('contact9', 'like', $searchText)
                ->orWhere('contact10', 'like', $searchText)
                ->orWhere('contact11', 'like', $searchText)
                ->orWhere('contact12', 'like', $searchText)
                ->orWhere('prim_email', 'like', $searchText)
                ->orWhere('sec_email', 'like', $searchText)
                ->orWhere('membership_number', 'like', $searchText)
                ->orWhereHas('country', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('city', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('ls', fn($q) => $q->where('name', 'like', $searchText));
            });
        });
    }

    /**
     * Applies admin-defined custom filter rows (Column + Operator + Value), ANDed
     * together and with all other active scopes. Callers must whitelist column and
     * operator before invoking this scope (see ContactpController::validateCustomFilters) —
     * this scope trusts $customFilters completely and never receives raw request input.
     *
     * @param array<int, array{column:string, operator:string, value:mixed}> $customFilters
     */
    public function scopeFilterCustom($query, array $customFilters)
    {
        return $query->when(!empty($customFilters), function ($query) use ($customFilters) {
            foreach ($customFilters as $filter) {
                $column   = $filter['column'];
                $operator = $filter['operator'];
                $value    = $filter['value'] ?? '';

                $query->where(function ($q) use ($column, $operator, $value) {
                    switch ($operator) {
                        case 'LIKE %...%':
                            $q->where($column, 'LIKE', '%' . $value . '%');
                            break;
                        case 'LIKE':
                            $q->where($column, 'LIKE', $value);
                            break;
                        case 'NOT LIKE':
                            $q->where($column, 'NOT LIKE', $value);
                            break;
                        case 'NOT LIKE %...%':
                            $q->where($column, 'NOT LIKE', '%' . $value . '%');
                            break;
                        case '=':
                            $q->where($column, '=', $value);
                            break;
                        case '!=':
                            $q->where($column, '!=', $value);
                            break;
                        case 'REGEXP':
                            $q->whereRaw("`{$column}` REGEXP ?", [$value]);
                            break;
                        case 'REGEXP ^...$':
                            $q->whereRaw("`{$column}` REGEXP ?", ['^' . $value . '$']);
                            break;
                        case 'NOT REGEXP':
                            $q->whereRaw("`{$column}` NOT REGEXP ?", [$value]);
                            break;
                        case "= ''":
                            $q->where($column, '=', '')->orWhereNull($column);
                            break;
                        case "!= ''":
                            $q->where($column, '!=', '')->whereNotNull($column);
                            break;
                        case 'IN (...)':
                            $q->whereIn($column, self::splitCustomFilterValue($value));
                            break;
                        case 'NOT IN (...)':
                            $q->whereNotIn($column, self::splitCustomFilterValue($value));
                            break;
                        case 'BETWEEN':
                            $parts = self::splitCustomFilterValue($value);
                            if (count($parts) === 2) {
                                $q->whereBetween($column, [$parts[0], $parts[1]]);
                            }
                            break;
                        case 'NOT BETWEEN':
                            $parts = self::splitCustomFilterValue($value);
                            if (count($parts) === 2) {
                                $q->whereNotBetween($column, [$parts[0], $parts[1]]);
                            }
                            break;
                    }
                });
            }
        });
    }

    /**
     * Splits a comma-separated custom filter value into trimmed, non-empty parts.
     * Used for IN / NOT IN / BETWEEN / NOT BETWEEN operators.
     */
    protected static function splitCustomFilterValue($value): array
    {
        return array_values(array_filter(
            array_map('trim', explode(',', (string) $value)),
            fn($v) => $v !== ''
        ));
    }

}
