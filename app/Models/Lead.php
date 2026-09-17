<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Admin;
use App\Helpers\Helper;

class Lead extends Model
{
    use HasFactory;

    protected $guarded = ['*'];

    /**
     * $guarded = ['*'] blocks mass assignment on every column by default;
     * these are the fields real call sites already mass-assign through a
     * model instance (->update()/->fill()) and therefore need explicit
     * fillable access:
     *  - is_qualified, call_not_connected_type, qualified_reason,
     *    staff_updated_at: LeadController::qualified()/bulk-qualify (the
     *    View Lead Details qualification update).
     *  - leadassign_id: the lead-assignment cron jobs
     *    (leads:assign-continuously, leads:assign-pending-night,
     *    ReassignUnfollowedLeads) and LeadController::leadAssignedProcess().
     *  - meta_lead_sent: MetaConversionService's Facebook CAPI dedup flag.
     * Direct property assignment (e.g. $lead->cand_name = ...; $lead->save())
     * bypasses this list entirely and is unaffected either way.
     */
    protected $fillable = [
        'is_qualified',
        'call_not_connected_type',
        'qualified_reason',
        'staff_updated_at',
        'leadassign_id',
        'meta_lead_sent',
    ];

    protected $casts = [
        'meta_lead_sent' => 'boolean',
        'is_reassigned' => 'boolean',
    ];
    

    public function leadassign()
    {
        return $this->belongsTo(Admin::class, 'leadassign_id');
    }


    public function scopeFilterByAssignee($query,$leadassign) {
        if (filled($leadassign)) {
            if (is_string($leadassign)) {
                $leadassign = explode(",",$leadassign);
            }

            $index = array_search('nulldata', $leadassign);

            if ($index !== false) {
                $leadassign[$index] = null;
                return $query->where(function($query) use($leadassign){
                    $query->whereIn('leadassign_id', $leadassign)->orWhereNull('leadassign_id');
                });
            } else {
                return $query->whereIn('leadassign_id', $leadassign);
            }
        }

        return $query;
    }

    // public function scopeFilterDateRange($query, $column, $date_range)
    // {
    //     return $query->when($date_range, function ($query) use ($column, $date_range) {
    //         [$start, $end] = array_map('trim', explode('-', $date_range));
    //         $query->whereBetween($column, [date('Y-m-d', strtotime($start)), date('Y-m-d', strtotime($end))]);
    //     });
    // }


    public function scopeFilterDateRange($query, $column, $date_range)
    {
        return $query->when($date_range, function ($query) use ($column, $date_range) {

            // Split the range "10/15/2025 - 11/18/2025"
            [$start, $end] = array_map('trim', explode('-', $date_range));

            try {
                $startDate = Carbon::createFromFormat('m/d/Y', $start)->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', $end)->endOfDay();

                $query->whereBetween($column, [$startDate, $endDate]);
            } catch (\Exception $e) {
                \Log::error('Date range filter error', [
                    'input' => $date_range,
                    'message' => $e->getMessage(),
                ]);
            }
        });
    }


    public function scopeFilterSearchText($query, $searchText)
    {
        return $query->when($searchText, function ($query, $searchText) {
            $searchText = '%' . $searchText . '%';
            $query->where(function ($q) use ($searchText) {
                $q->orWhere('id', 'like', $searchText)
                ->orWhere('cand_name', 'like', $searchText)
                ->orWhere('mob_no', 'like', $searchText)
                ->orWhere('email', 'like', $searchText)
                ->orWhere('required_service', 'like', $searchText)
                ->orWhere('country', 'like', $searchText)
                ->orWhere('message', 'like', $searchText)
                ->orWhere('whatsapp_no','like', $searchText)
                ->orWhere('experience','like', $searchText)
                ->orWhere('lead_date', 'like', $searchText)
                ->orWhereHas('leadassign', fn($q) => $q->where('name', 'like', $searchText));
            });
        });
    }

    // Lead.php (Model)
    public function scopeFilterQualified($query, $qualified)
    {
        if ($qualified !== null && $qualified !== '') {
            return $query->where('is_qualified', $qualified);
        }
        return $query;
    }

    public function scopeFilterDrivingLicense($query, $by_driving_license)
    {
        if (filled($by_driving_license)) {
            if (is_string($by_driving_license)) {
                $by_driving_license = explode(',', $by_driving_license);
            }

            $query->where(function ($q) use ($by_driving_license) {
                foreach ($by_driving_license as $license) {
                    if ($license === 'KSA DL') {
                        $q->where('saudi_license', 'yes')
                        ->where('experience', 'like', '%Saudi%')
                        ->where('country', 'like', '%Saudi Arabia%');
                    } elseif ($license === 'Indian DL') {
                        $q->orWhere(function ($sub) {
                            $sub->where('india_license', 'yes')
                                ->orWhere('experience', 'like', '%INDIA%');
                        });
                    }
                }
            });
        }

        return $query;
    }

    /**
     * Canonical "KSA DL" eligibility check, mirroring the SQL predicate in
     * scopeFilterDrivingLicense() above (saudi_license='yes' AND experience LIKE
     * '%Saudi%' AND country LIKE '%Saudi Arabia%') so PHP-side callers — e.g. the
     * Meta send gate — agree with the CRM filter instead of using a separate,
     * stricter, independently-maintained condition.
     */
    public function isKsaDrivingLicenseLead(): bool
    {
        return $this->saudi_license === 'yes'
            && mb_stripos((string) $this->experience, 'Saudi') !== false
            && mb_stripos((string) $this->country, 'Saudi Arabia') !== false;
    }

    public function scopeFilterQualifiedStatus($query, $by_is_qualified)
    {
        if (filled($by_is_qualified)) {

            // Convert comma-separated string to array if needed
            if (is_string($by_is_qualified)) {
                $by_is_qualified = explode(',', $by_is_qualified);
            }

            // Remove "All" if selected
            $by_is_qualified = array_filter($by_is_qualified, function ($value) {
                return $value !== 'All';
            });

            if (!empty($by_is_qualified)) {

                $query->where(function ($q) use ($by_is_qualified) {

                    foreach ($by_is_qualified as $status) {

                        if ($status === 'null') {

                            $q->orWhereNull('is_qualified');

                        } elseif ($status === 'Previous Lead') {

                            $q->orWhere(function ($subQuery) {
                                $subQuery->where('is_repeted', 1)
                                        ->whereNotNull('candidate_updated_at');
                            });

                        } elseif ($status === 'Lead Reassigned') {

                            $q->orWhere('is_reassigned', 1);

                        } else {

                            $q->orWhere('is_qualified', (int) $status);

                        }
                    }

                });

            }
        }

        return $query;
    }

    public function scopeFilterCallNotConnectedType($query, $by_call_not_connected_type)
    {
        if (filled($by_call_not_connected_type)) {

            // Convert comma-separated string to array if needed
            if (is_string($by_call_not_connected_type)) {
                $by_call_not_connected_type = explode(',', $by_call_not_connected_type);
            }

            // Remove "All" if selected
            $by_call_not_connected_type = array_filter($by_call_not_connected_type, function ($value) {
                return $value !== 'All';
            });

            if (!empty($by_call_not_connected_type)) {

                $query->where(function ($q) use ($by_call_not_connected_type) {

                    foreach ($by_call_not_connected_type as $type) {

                        if ($type === 'null') {
                            $q->orWhereNull('call_not_connected_type');
                        } else {
                            $q->orWhere('call_not_connected_type', $type);
                        }

                    }

                });

            }
        }

        return $query;
    }

    public function notes()
    {
        return $this->hasMany(Leadnote::class, 'lead_id');
    }

    /**
     * Applies admin-defined custom filter rows (Column + Operator + Value), ANDed
     * together and with all other active scopes. Callers must whitelist column and
     * operator before invoking this scope (see LeadController::validateCustomFilters) —
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


    protected static function booted()
    {
        static::updated(function (Lead $lead) {

            if (! $lead->wasChanged('leadassign_id')) {
                return;
            }

            $oldAssignId = $lead->getOriginal('leadassign_id');
            $newAssignId = $lead->leadassign_id;

            $oldAdmin = $oldAssignId
                ? Admin::find($oldAssignId)?->name
                : 'Unassigned';

            $newAdmin = $newAssignId
                ? Admin::find($newAssignId)?->name
                : 'Unassigned';

            // Manual assignment only ever happens from an authenticated admin
            // session (leads/assignto, leads/bulkassignto). The scheduled
            // commands (leads:assign-continuously, leads:assign-pending-night)
            // and any other automated reassignment run outside a web request,
            // so the admin guard is empty there — that absence is what marks
            // an assignment as Auto rather than Manual.
            $actingAdminId = auth('admin')->id();

            Helper::leadActivityLog(
                $lead->id,
                [
                    'action'          => 'Lead Assignment Updated',
                    'old_assign'      => $oldAdmin,
                    'new_assign'      => $newAdmin,
                    'assignment_type' => $actingAdminId ? 'Manual' : 'Auto',
                ],
                $actingAdminId,
                'lead_assign_action'
            );
        });

        // Structured stage-history record, independent of the assignment
        // logging above and of LeadController::qualified()'s own manual
        // lead_activity_logs write — this fires for ANY code path that
        // changes is_qualified, not just the one controller action.
        static::updated(function (Lead $lead) {

            if (! $lead->wasChanged('is_qualified')) {
                return;
            }

            $oldStatus = $lead->getOriginal('is_qualified');
            $newStatus = $lead->is_qualified;

            \App\Models\StageHistory::create([
                'trackable_type' => 'lead',
                'trackable_id'   => $lead->id,
                'from_value'     => $oldStatus === null ? null : (string) $oldStatus,
                'to_value'       => $newStatus === null ? null : (string) $newStatus,
                'from_label'     => self::QUALIFIED_STATUS_LABELS[$oldStatus ?? 0] ?? 'Not Yet',
                'to_label'       => self::QUALIFIED_STATUS_LABELS[$newStatus ?? 0] ?? 'Not Yet',
                'changed_by'     => auth('admin')->id(),
                'meta'           => [
                    'reason'                  => $lead->qualified_reason,
                    'call_not_connected_type' => $lead->call_not_connected_type,
                ],
            ]);
        });
    }

    /**
     * is_qualified enum labels. Single source of truth — also used by
     * LeadDashboardService and LeadController::logQualificationStatusActivity()
     * so all three never drift out of sync.
     */
    const QUALIFIED_STATUS_LABELS = [
        0 => 'Not Yet',
        1 => 'Followed Up',
        2 => 'Call Not Connected',
        3 => 'Lead Qualified',
        4 => 'Lead Not Qualified',
        5 => 'Lead Not Relevant',
    ];
}
