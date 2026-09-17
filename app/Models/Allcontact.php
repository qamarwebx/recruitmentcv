<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allcontact extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'ai_call_response' => 'array',
    ];

    public function owner(){
        return $this->belongsTo(Admin::class);
    }

    public function state(){
        return $this->belongsTo(Region::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function assign() {
        return $this->belongsTo(Admin::class);
    }

    public function user(){
        return $this->belongsTo(Admin::class);
    }

    public function lcs(){
        return $this->belongsTo(Lifecyclestatus::class);
    }

    public function ls(){
        return $this->belongsTo(Leadstage::class);
    }

    public function indust(){
        return $this->belongsTo(Industry::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function deleteby(){
        return $this->belongsTo(Admin::class);
    }

    public function group(){
        return $this->belongsTo(Groupallc::class);
    }

    public function careoff(){
        return $this->belongsTo(Admin::class);
    }


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

    public function scopeFilterState($query,$state_id){
        return $query->when($state_id, function($query,$state_id){
            $query->whereIn('state_id',(array) $state_id);
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

    public function scopeFilterBusinesstype($query,$lead_type){
        return $query->when($lead_type, function($query,$lead_type){
            $query->whereIn('lead_type',(array) $lead_type);
        });
    }

    public function scopeFilterIndustry($query,$indust_id){
        return $query->when($indust_id, function($query,$indust_id){
            $query->whereIn('indust_id',(array) $indust_id);
        });
    }

    public function scopeFilterGroup($query, $group_id)
    {
        if (filled($group_id)) {

            if (is_string($group_id)) {
                $group_id = explode(',', $group_id);
            }

            // Check if "null" is selected
            $index = array_search('null', $group_id, true);

            if ($index !== false) {
                // Remove "null" from the array
                unset($group_id[$index]);
                $group_id = array_values($group_id);

                return $query->where(function ($q) use ($group_id) {
                    if (!empty($group_id)) {
                        $q->whereIn('group_id', $group_id);
                    }

                    $q->orWhereNull('group_id');
                });
            }

            return $query->whereIn('group_id', $group_id);
        }

        return $query;
    }



    public function scopeFilterCreatedBy($query,$user_id){
        return $query->when($user_id, function($query,$user_id){
            $query->whereIn('user_id',(array) $user_id);
        });
    }

    public function scopeFilterLeadPriority($query,$lead_prority){
        return $query->when($lead_prority, function($query,$lead_prority){
            $query->whereIn('lead_prority',(array) $lead_prority);
        });
    }

    public function scopeFilterLeadOwner($query,$owner_id){
        return $query->when($owner_id, function($query,$owner_id){
            $query->whereIn('owner_id',(array) $owner_id);
        });
    }

    public function scopeFilterLeadCareoff($query,$careoff_id){
        return $query->when($careoff_id, function($query,$careoff_id){
            $query->whereIn('careoff_id',(array) $careoff_id);
        });
    }

    public function scopeFilterDateRange($query, $column, $date_range)
    {
        return $query->when($date_range, function ($query) use ($column, $date_range) {
            [$start, $end] = array_map('trim', explode('-', $date_range));

            $startDate = date('Y-m-d 00:00:00', strtotime($start));
            $endDate = date('Y-m-d 23:59:59', strtotime($end));

            // $query->whereBetween($column, [date('Y-m-d', strtotime($start)), date('Y-m-d', strtotime($end))]);
            $query->whereBetween($column, [$startDate,$endDate]);
        });
    }

    public function scopeFilterCountryDialCodeField($query, $country_codes)
    {
        if (filled($country_codes)) {

            $country_codes = (array) $country_codes;

            // Remove + if passed
            $country_codes = array_map(function ($code) {
                return ltrim($code, '+');
            }, $country_codes);

            $columns = [
                'primary_no_wsp',
                'secondary_no_wsp',
                'mobile_no1_wsp',
                'mobile_no2_wsp',
                'mobile_no3_wsp',
            ];

            return $query
                // ✅ Include selected country codes
                ->where(function ($q) use ($country_codes, $columns) {

                    foreach ($columns as $column) {
                        foreach ($country_codes as $code) {
                            $q->orWhere($column, 'like', $code . '%')
                            ->orWhere($column, 'like', '+' . $code . '%');
                        }
                    }

                })

                // ✅ Ensure at least one mobile exists
                ->where(function ($q) use ($columns) {

                    foreach ($columns as $column) {
                        $q->orWhere(function ($sub) use ($column) {
                            $sub->whereNotNull($column)
                                ->whereRaw("TRIM($column) != ''");
                        });
                    }

                });
        }

        return $query;
    }

    public function scopeFilterExludeCountryMobileCode($query, $country_codes)
    {
        if (filled($country_codes)) {
    
            $country_codes = (array) $country_codes;
    
            // Remove + if passed
            $country_codes = array_map(function ($code) {
                return ltrim($code, '+');
            }, $country_codes);
    
            $columns = [
                'primary_no_wsp',
                'secondary_no_wsp',
                'mobile_no1_wsp',
                'mobile_no2_wsp',
                'mobile_no3_wsp'
            ];
    
            return $query->whereNot(function ($q) use ($country_codes, $columns) {
    
                foreach ($country_codes as $code) {
    
                    foreach ($columns as $column) {
    
                        $q->orWhere($column, 'like', $code . '%')
                          ->orWhere($column, 'like', '+' . $code . '%');
                    }
                }
    
            });
        }
    
        return $query;
    }

    public function scopeFilterCountryDialCode($query, $country_code)
    {
        if (filled($country_code)) {

            // Ensure it's always an array
            $country_codes = (array) $country_code;

            // Remove + if passed
            $country_codes = array_map(function ($code) {
                return ltrim($code, '+');
            }, $country_codes);

            $columns = [
                "primary_con_dial_code",
                "primary_no_wsp_dial_code",
                "mobile_no1_wsp_dial_code",
                "mobile_no2_wsp_dial_code"
            ];

            return $query->where(function ($q) use ($columns, $country_codes) {

                foreach ($columns as $column) {
                    $q->orWhereIn($column, $country_codes);
                }

            });
        }

        return $query;
    }

    public function scopeFilterSearchText($query, $searchText)
    {

        return $query->when($searchText, function ($query, $searchText) {
            $searchText = '%' . $searchText . '%';
            $query->where(function ($q) use ($searchText) {
                $q->orWhere('id', 'like', $searchText)
                ->orWhere('lead_type', 'like', $searchText)
                ->orWhere('full_name', 'like', $searchText)
                ->orWhere('primary_no_wsp', 'like', $searchText)
                ->orWhere('email', 'like', $searchText)
                ->orWhere('secondary_no_wsp', 'like', $searchText)
                ->orWhere('lead_prority', 'like', $searchText)
                ->orWhere('company_name', 'like', $searchText)
                ->orWhere('mobile_no1_wsp', 'like', $searchText)
                ->orWhere('mobile_no2_wsp', 'like', $searchText)
                ->orWhere('mobile_no3_wsp', 'like', $searchText)
                ->orWhere('email0', 'like', $searchText)
                ->orWhere('email1', 'like', $searchText)
                ->orWhere('email2', 'like', $searchText)
                ->orWhere('job_desg', 'like', $searchText)
                ->orWhere('descr', 'like', $searchText)
                ->orWhere('job_title', 'like', $searchText)
                ->orWhere('office_name', 'like', $searchText)
                ->orWhereHas('country', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('city', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('state',fn($q) => $q->where('name','like', $searchText))
                ->orWhereHas('user', fn($q) => $q->where('name','like', $searchText))
                ->orWhereHas('lcs', fn($q) => $q->where('name','like', $searchText))
                ->orWhereHas('owner', fn($q) => $q->where('name','like', $searchText))
                ->orWhereHas('indust', fn($q) => $q->where('name','like', $searchText))
                ->orWhereHas('group', fn($q) => $q->where('name','like', $searchText))
                ->orWhereHas('careoff', fn($q) => $q->where('name','like', $searchText))
                ->orWhereHas('ls', fn($q) => $q->where('name', 'like', $searchText));
            });
        });
    }

    public function scopeFilterFollowupBefore($query, $days)
    {
        if (empty($days)) {
            return $query;
        }

        $days = (int) $days;

        $today = \Carbon\Carbon::today();
        $futureDate = \Carbon\Carbon::today()->addDays($days);

        if ($days == 1) {
            $futureDate = \Carbon\Carbon::today();
        }

        return $query->whereBetween('updated_at', [
            $today->startOfDay(),
            $futureDate->endOfDay()
        ]);
    }

    public function notes()
    {
        return $this->hasMany(Allcontactnote::class, 'allcontact_id');
    }

    public function scopeFilterConversationType($query, $conversationtype)
    {
        if (!empty($conversationtype)) {

            $types = is_array($conversationtype)
                ? $conversationtype
                : explode(',', $conversationtype);

            $query->whereHas('notes', function ($q) use ($types) {
                $q->whereIn('conversation_type', $types);
            });
        }

        return $query;
    }

    public function scopeFilterStatus($query, $status)
    {
        return $query->when($status, function ($query, $status) {
            $query->whereIn('status', (array) $status);
        });
    }

    public function scopeFilterSource($query, $source_id)
    {
        return $query->when($source_id, function ($query, $source_id) {
            $query->whereIn('source_id', (array) $source_id);
        });
    }

    public function scopeFilterHasEmail($query, $flag)
    {
        return $query->when(filled($flag), function ($query) use ($flag) {
            if ((string) $flag === '1') {
                $query->whereNotNull('email')->where('email', '!=', '');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('email')->orWhere('email', '');
                });
            }
        });
    }

    public function scopeFilterHasMobile($query, $flag)
    {
        return $query->when(filled($flag), function ($query) use ($flag) {
            if ((string) $flag === '1') {
                $query->whereNotNull('primary_no_wsp')->where('primary_no_wsp', '!=', '');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('primary_no_wsp')->orWhere('primary_no_wsp', '');
                });
            }
        });
    }

    public function scopeFilterRecentlyAdded($query, $flag)
    {
        return $query->when((string) $flag === '1', function ($query) {
            $query->where('created_at', '>=', now()->subDays(7));
        });
    }

    protected static function booted()
    {
        static::updating(function ($contact) {
            $contact->staff_updated_date = now();
        });
    }

    /**
     * Applies admin-defined custom filter rows (Column + Operator + Value), ANDed
     * together and with all other active scopes. Callers must whitelist column and
     * operator before invoking this scope (see AllContactController::validateCustomFilters) —
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
