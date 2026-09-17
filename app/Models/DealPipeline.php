<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class DealPipeline extends Model
{
    use HasFactory;

    protected $table = 'deal_pipeline';

    protected $fillable = [
        'sort_order',
        'business_id',
        'deal_stage_id',
        'recruite_status_id',
        'associate_id',
        'job_title',
        'job_title_other',
        'deal_name',
        'name',
        'company',
        'candidate',
        'passport_no',
        'amount',
        'notes',
        'contact',
        'contact_whatsapp',
        'email',
        'country',
        'city',
        'care_of',
        'close_date',
        'created_by',
        'modified_by',
        'source',
    ];

    // Relationships

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function stage()
    {
        return $this->belongsTo(DealStage::class, 'deal_stage_id');
    }

    public function recruitStatus()
    {
        return $this->belongsTo(RecruitStatus::class, 'recruite_status_id');
    }

    public function careOf()
    {
        return $this->belongsTo(Admin::class, 'care_of');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function modifier()
    {
        return $this->belongsTo(Admin::class, 'modified_by');
    }

    public function scopeFilterDealStage($query, $dealStageId)
    {
        return $query->when($dealStageId, function ($query, $dealStageId) {
            $query->whereIn('deal_stage_id', (array) $dealStageId);
        });
    }

    public function scopeFilterRecruitStatus($query, $recruiteStatusId)
    {
        return $query->when($recruiteStatusId, function ($query, $recruiteStatusId) {
            $query->whereIn('recruite_status_id', (array) $recruiteStatusId);
        });
    }

    public function scopeFilterSearchText($query, $searchText)
    {

        if (empty(trim($searchText))) {
            return $query;
        }
            
        return $query->when($searchText, function ($query, $searchText) {
            $searchText = '%' . $searchText . '%';
    
            $query->where(function ($q) use ($searchText) {
    
                // Search in all fillable fields
                $q->orWhere('job_title', 'like', $searchText)
                ->orWhere('candidate', 'like', $searchText)
                  ->orWhere('deal_name', 'like', $searchText)
                  ->orWhere('name', 'like', $searchText)
                  ->orWhere('company', 'like', $searchText)
                  ->orWhere('passport_no', 'like', $searchText)
                  ->orWhere('amount', 'like', $searchText)
                  ->orWhere('notes', 'like', $searchText)
                  ->orWhere('contact', 'like', $searchText)
                  ->orWhere('contact_whatsapp', 'like', $searchText)
                  ->orWhere('email', 'like', $searchText)
                  ->orWhere('country', 'like', $searchText)
                  ->orWhere('city', 'like', $searchText)
                  ->orWhere('care_of', 'like', $searchText)
                  ->orWhere('close_date', 'like', $searchText)
                  ->orWhere('created_by', 'like', $searchText)
                  ->orWhere('modified_by', 'like', $searchText)
                  ->orWhere('source', 'like', $searchText);
    
                // Search in related models
                $q->orWhereHas('business', fn($q) => $q->where('name', 'like', $searchText))
                  ->orWhereHas('stage', fn($q) => $q->where('name', 'like', $searchText))
                  ->orWhereHas('recruitStatus', fn($q) => $q->where('name', 'like', $searchText))
                  ->orWhereHas('careOf', fn($q) => $q->where('name', 'like', $searchText))
                  ->orWhereHas('creator', fn($q) => $q->where('name', 'like', $searchText))
                  ->orWhereHas('modifier', fn($q) => $q->where('name', 'like', $searchText));
            });
        });
    }
    public function scopeFilterSearchTextForkanban($query, $searchText)
    {
        $searchText = trim($searchText);

        if ($searchText === '') {
            return $query;
        }

        $query->where(function ($q) use ($searchText) {

            $q->where(function ($q2) use ($searchText) {
                $q2->whereNotNull('candidate')
                ->Where('passport_no', '=', $searchText)
                ->orWhere('candidate', 'LIKE', "%{$searchText}%")
                ->orWhere('id', '=', $searchText);
            });

        });

    }

    public function dealNotes()  
    {
        return $this->hasMany(DealNote::class, 'deal_id');
    }

    public function associate()  
    {
        return $this->belongsTo(Associates::class, 'associate_id');
    }

    public function jobTitle()  
    {
        return $this->belongsTo(JobTitle::class, 'job_title');
    }

    public function source()
    {
        return $this->belongsTo(GlobalSourceType::class, 'source');
    }

    /**
     * Structured stage-history log — this model previously had no event
     * hooks at all, so deal_stage_id was overwritten in place with zero
     * trace of the prior value. Both hooks fire automatically for every
     * write path (store(), updateDealStage(), kanbanUpdateStatus(), lead
     * auto-transfer, tinker/artisan) — no controller changes needed.
     */
    protected static function booted()
    {
        static::created(function (DealPipeline $deal) {
            \App\Models\StageHistory::create([
                'trackable_type' => 'deal',
                'trackable_id'   => $deal->id,
                'from_value'     => null,
                'to_value'       => $deal->deal_stage_id === null ? null : (string) $deal->deal_stage_id,
                'from_label'     => null,
                'to_label'       => optional(DealStage::find($deal->deal_stage_id))->name,
                'changed_by'     => auth('admin')->id() ?? $deal->created_by,
                'meta'           => ['amount' => $deal->amount],
            ]);
        });

        static::updated(function (DealPipeline $deal) {

            if (! $deal->wasChanged('deal_stage_id')) {
                return;
            }

            $oldStageId = $deal->getOriginal('deal_stage_id');
            $newStageId = $deal->deal_stage_id;

            \App\Models\StageHistory::create([
                'trackable_type' => 'deal',
                'trackable_id'   => $deal->id,
                'from_value'     => $oldStageId === null ? null : (string) $oldStageId,
                'to_value'       => $newStageId === null ? null : (string) $newStageId,
                'from_label'     => optional(DealStage::find($oldStageId))->name,
                'to_label'       => optional(DealStage::find($newStageId))->name,
                'changed_by'     => auth('admin')->id() ?? $deal->modified_by,
                'meta'           => ['amount' => $deal->amount],
            ]);
        });

        // Deal Activity Log — separate from the StageHistory hooks above so
        // existing stage-history behaviour is never touched. Powers the
        // "View Deal Notes" → Activity tab, mirroring Lead::booted()'s
        // leadassign_id activity hook.
        static::created(function (DealPipeline $deal) {
            Helper::dealActivityLog(
                $deal->id,
                [
                    'action'    => 'Deal Created',
                    'deal_name' => $deal->deal_name ?: $deal->name,
                ],
                auth('admin')->id() ?? $deal->created_by,
                'deal_created'
            );
        });

        static::updated(function (DealPipeline $deal) {

            $actingAdminId = auth('admin')->id() ?? $deal->modified_by;

            if ($deal->wasChanged('deal_stage_id')) {
                $oldStageId = $deal->getOriginal('deal_stage_id');
                $newStageId = $deal->deal_stage_id;

                Helper::dealActivityLog(
                    $deal->id,
                    [
                        'action'    => 'Deal Stage Changed',
                        'old_stage' => optional(DealStage::find($oldStageId))->name ?? 'Not Set',
                        'new_stage' => optional(DealStage::find($newStageId))->name ?? 'Not Set',
                    ],
                    $actingAdminId,
                    'deal_stage_change'
                );
            }

            if ($deal->wasChanged('recruite_status_id')) {
                $oldStatusId = $deal->getOriginal('recruite_status_id');
                $newStatusId = $deal->recruite_status_id;

                Helper::dealActivityLog(
                    $deal->id,
                    [
                        'action'     => 'Deal Status Changed',
                        'old_status' => optional(RecruitStatus::find($oldStatusId))->name ?? 'Not Set',
                        'new_status' => optional(RecruitStatus::find($newStatusId))->name ?? 'Not Set',
                    ],
                    $actingAdminId,
                    'deal_status_change'
                );
            }

            if ($deal->wasChanged('care_of')) {
                $oldCareOfId = $deal->getOriginal('care_of');
                $newCareOfId = $deal->care_of;

                Helper::dealActivityLog(
                    $deal->id,
                    [
                        'action'          => 'Deal Assignment Updated',
                        'old_assign'      => $oldCareOfId ? (optional(Admin::find($oldCareOfId))->name ?? 'Unknown') : 'Unassigned',
                        'new_assign'      => $newCareOfId ? (optional(Admin::find($newCareOfId))->name ?? 'Unknown') : 'Unassigned',
                        'assignment_type' => auth('admin')->id() ? 'Manual' : 'Auto',
                    ],
                    $actingAdminId,
                    'deal_assign_action'
                );
            }

            if ($deal->wasChanged('amount')) {
                Helper::dealActivityLog(
                    $deal->id,
                    [
                        'action'     => 'Deal Amount Changed',
                        'old_amount' => $deal->getOriginal('amount'),
                        'new_amount' => $deal->amount,
                    ],
                    $actingAdminId,
                    'deal_amount_change'
                );
            }

            $changedFields = [];

            foreach (self::ACTIVITY_TRACKED_FIELDS as $field => $label) {
                if ($deal->wasChanged($field)) {
                    $changedFields[] = [
                        'field' => $label,
                        'old'   => self::activityFieldLabel($field, $deal->getOriginal($field)),
                        'new'   => self::activityFieldLabel($field, $deal->{$field}),
                    ];
                }
            }

            if (! empty($changedFields)) {
                Helper::dealActivityLog(
                    $deal->id,
                    [
                        'action'  => 'Deal Updated',
                        'changes' => $changedFields,
                    ],
                    $actingAdminId,
                    'deal_updated'
                );
            }
        });
    }

    /**
     * Plain/free-text + lookup fields eligible for the generic "Deal
     * Updated" activity entry. Deliberately excludes deal_stage_id,
     * recruite_status_id, care_of and amount (each already gets its own
     * richer activity type above) and internal bookkeeping columns
     * (sort_order, created_by, modified_by).
     */
    const ACTIVITY_TRACKED_FIELDS = [
        'business_id'      => 'Business',
        'associate_id'     => 'Associate',
        'job_title'        => 'Job Title',
        'job_title_other'  => 'Job Title',
        'deal_name'        => 'Deal Name',
        'name'             => 'Name',
        'company'          => 'Company',
        'candidate'        => 'Candidate',
        'passport_no'      => 'Passport No.',
        'contact'          => 'Mobile',
        'contact_whatsapp' => 'Mobile (WhatsApp)',
        'email'            => 'Email',
        'country'          => 'Country',
        'city'             => 'City',
        'close_date'       => 'Close Date',
        'notes'            => 'Notes',
        'source'           => 'Source',
    ];

    /**
     * Resolves a raw column value to a human-readable label for the
     * generic "Deal Updated" activity entry, following the same
     * relationships the Details tab already resolves in getDeal().
     */
    protected static function activityFieldLabel(string $field, $value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        switch ($field) {
            case 'business_id':
                return optional(Business::find($value))->name;
            case 'associate_id':
                return optional(Associates::find($value))->pty_full_name;
            case 'job_title':
                return optional(JobTitle::find($value))->name;
            case 'source':
                return optional(GlobalSourceType::find($value))->name;
            default:
                return (string) $value;
        }
    }
}
