<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    /**
     * candidates.gulfexperience codes => translation key (same codes as the
     * CRM's customercosts.exp_type). Used by display_profession_label and the
     * Partner Price Update page.
     */
    public const EXPERIENCE_TYPES = [
        1 => 'locale.Indian Experience',
        2 => 'locale.Ex-Abroad',
    ];

    /**
     * Locale-aware display name - arcand_name when the app locale is Arabic
     * and that field is actually set, else cand_name. Matches the same
     * fallback pattern already used by the Arabic front-end
     * (resources/views/arabic/user/fullresumes.blade.php: `$post->arcand_name
     * != '' ? $post->arcand_name : $post->cand_name`).
     */
    public function getDisplayNameAttribute()
    {
        if (app()->getLocale() === 'ar' && !empty($this->arcand_name)) {
            return $this->arcand_name;
        }

        return $this->cand_name;
    }

    /**
     * Experience type + profession, e.g. "Indian Experience House Driver" /
     * "Ex-Abroad House Driver" ("---" when no profession) - shown on the
     * candidate detail pages and under the name on every candidate card.
     * Locale-aware through __('locale.*') and Profession::display_name.
     */
    public function getDisplayProfessionLabelAttribute()
    {
        if ($this->jobtype_id == '') {
            return '---';
        }

        $experience = '';
        foreach (static::EXPERIENCE_TYPES as $code => $key) {
            if ($this->gulfexperience == $code) {
                $experience = __($key) . ' ';
                break;
            }
        }

        return $experience . optional($this->profession)->display_name;
    }

    public function getDisplayMaritalStatusAttribute()
    {
        if (app()->getLocale() === 'ar' && !empty($this->ar_marital_status)) {
            return $this->ar_marital_status;
        }

        return $this->marital_status;
    }

    public function getDisplayLanguageAttribute()
    {
        if (app()->getLocale() === 'ar' && !empty($this->ar_language)) {
            return $this->ar_language;
        }

        return $this->lang_known;
    }

    /**
     * Get the admin that owns the Candidate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the jobtype that owns the Candidate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function jobtype()
    {
        return $this->belongsTo(Profession::class);
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class, 'jobtype_id', 'id');
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class, 'religion_id', 'id');
    }

    /**
     * Get the careoff that owns the Candidate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function careoff()
    {
        return $this->belongsTo(Admin::class);
    }

    public function associate(){
        return $this->belongsTo(Associates::class);
    }

    public function associateconfirmby(){
        return $this->belongsTo(Admin::class);
    }

    /**
     * The candidate's latest active employer assignment
     * (employercandidates.status = 1, highest id), used to resolve
     * the linked employer's "City of Work" for the candidate list.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function workEmployercandidate()
    {
        return $this->hasOne(Employercandidate::class, 'cand_id')
            ->ofMany(['id' => 'max'], function ($query) {
                $query->where('status', 1);
            });
    }

    /**
     * The Employerplus or Employer record behind workEmployercandidate
     * (whichever of emp_id/emp2_id is set on the assignment).
     */
    public function getWorkEmployerAttribute()
    {
        $assignment = $this->workEmployercandidate;

        if (! $assignment) {
            return null;
        }

        return $assignment->emp ?? $assignment->emp2;
    }

    /**
     * "City of Work" (Expecworkcity name) from the employer linked via
     * workEmployercandidate - either the Employer Plus (emp_id) or base
     * Employer (emp2_id) record, whichever is set on the assignment.
     */
    public function getWorkCityAttribute()
    {
        return optional(optional($this->work_employer)->wpcity)->displayname;
    }

    /**
     * wpcity_id of the employer linked via workEmployercandidate - used to
     * preselect the current city when editing it from the candidate list.
     */
    public function getWorkCityIdAttribute()
    {
        return optional($this->work_employer)->wpcity_id;
    }

    // Local Scope
    public function scopeFilterPassType($query, $pass_type) {
        return $query->when($pass_type, function ($query, $pass_type) {
            $query->whereIn('pass_type', (array) $pass_type);
        });
    }

    public function scopeFilterExpSal($query, $exp_sal)
    {
        return $query->when($exp_sal, function ($query, $exp_sal) {
            $query->whereIn('exp_sal', (array) $exp_sal);
        });
    }

    public function scopeFilterJobType($query, $jobtype_id) {
        return $query->when($jobtype_id, function ($query, $jobtype_id) {
            $query->whereIn('jobtype_id', (array) $jobtype_id);
        });
    }

    public function scopeFilterCareoff($query, $careoff_id) {
        return $query->when($careoff_id, function ($query, $careoff_id) {
            $query->whereIn('careoff_id', (array) $careoff_id);
        });
    }

    public function scopeFilterCreateBy($query, $admin_id) {
        return $query->when($admin_id, function ($query, $admin_id) {
            $query->whereIn('admin_id', (array) $admin_id);
        });
    }

    public function scopeFilterReligion($query, $religion_id) {
        return $query->when($religion_id, function ($query, $religion_id) {
            $query->whereIn('religion_id', (array) $religion_id);
        });
    }

    public function scopeFilterCity($query, $candcity_text) {
        return $query->when($candcity_text, function ($query, $candcity_text) {
            $query->whereIn('candcity_text', (array) $candcity_text);
        });
    }

    public function scopeFilterRegion($query, $region_id) {
        return $query->when($region_id, function ($query, $region_id) {
            $query->whereIn('region_id', (array) $region_id);
        });
    }

    public function scopeFilterExperienceRegion($query, $gulfexperience) {
        return $query->when($gulfexperience, function ($query, $gulfexperience) {
            $query->whereIn('gulfexperience', (array) $gulfexperience);
        });
    }

    public function scopeFilterPublish($query, $publish) {
        return $query->when($publish, function ($query, $publish) {
            $query->whereIn('publish', (array) $publish);
        });
    }

    public function scopeFilterCandStatus($query, $candidate_current_status) {
        return $query->when($candidate_current_status, function ($query, $candidate_current_status) {
            $query->whereIn('candidate_current_status', (array) $candidate_current_status);
        });
    }

    public function scopeFilterMedicalStatus($query, $medical_health_status) {
        return $query->when($medical_health_status, function ($query, $medical_health_status) {
            $query->whereIn('medical_health_status', (array) $medical_health_status);
        });
    }

    public function scopeFilterPaymentStatus($query, $cand_payment_status) {
        return $query->when($cand_payment_status, function ($query, $cand_payment_status) {
            $query->whereIn('cand_payment_status', (array) $cand_payment_status);
        });
    }

    public function scopeFilterExpwp($query, $expwp_id)
    {
        return $query->when($expwp_id, function ($query, $expwp_id) {
            $expwp_id = is_array($expwp_id) ? $expwp_id : [$expwp_id];
            $query->where(function ($q) use ($expwp_id) {
                foreach ($expwp_id as $id) {
                    $q->orWhereRaw("FIND_IN_SET(?, expwp_id)", [$id]);
                }
            });
        });
    }





    public function scopeFilterDate($query, $column, $date)
    {
        return $query->when($date, function ($query) use ($column, $date) {
            $query->whereDate($column, $date);
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
                ->orWhere('cand_name', 'like', $searchText)
                ->orWhere('pass_no', 'like', $searchText)
                ->orWhere('pass_type', 'like', $searchText)
                ->orWhere('contact_no', 'like', $searchText)
                ->orWhere('marital_status', 'like', $searchText)
                ->orWhere('lang_known', 'like', $searchText)
                ->orWhere('exp_sal', 'like', $searchText)
                ->orWhere('mobile_no', 'like', $searchText)
                ->orWhere('reference_no', 'like', $searchText)
                ->orWhere('candidate_current_status', 'like', $searchText)
                ->orWhereHas('careoff', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('jobtype', fn($q) => $q->where('eng_name', 'like', $searchText))
                ->orWhereHas('admin', fn($q) => $q->where('name', 'like', $searchText));
            });
        });
    }

}
