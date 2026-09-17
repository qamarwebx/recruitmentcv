<?php

namespace App\Http\Controllers;

use App\Jobs\AutoSendMessageForLeadJobs;
use App\Models\Admin;
use App\Models\Adminpermission;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mpdf\Tag\Tr;
use App\Models\LeadAdminSaveFilter;
use Carbon\Carbon;
use App\Models\DealPipeline;
use App\Models\DealStage;
use App\Models\Business;
use App\Models\Autometanotification;
use App\Models\ScheduledSendMsgAutomation;
use Stevebauman\Location\Facades\Location;
use App\Models\Autoemailnotification;
use App\Models\ScheduledSendEmailAutomation;
use App\Models\FacebookAccount;
use App\Models\LeadAutoAssignStatus;
use App\Models\Metawhatsapptemplate;
use App\Models\Metawhatsappapi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MetaLeadLog;
use App\Models\Leadnote;
use Illuminate\Support\Facades\Cache;
use DataTables;
use Str;
use App\Helpers\Helper;
use App\Models\LeadActivityLog;
use App\Services\Meta\MetaConversionService;
use Illuminate\Support\Facades\Schema;

class LeadController extends Controller
{

    public function __construct(private MetaConversionService $metaConversionService
    ) {
        // keep it blank
    }

    /** Whitelisted operator labels for admin-defined Leads custom filters (must match the UI <option> values verbatim). */
    private const CUSTOM_FILTER_OPERATORS = [
        'LIKE %...%', 'LIKE', 'NOT LIKE', 'NOT LIKE %...%',
        '=', '!=', 'REGEXP', 'REGEXP ^...$', 'NOT REGEXP',
        "= ''", "!= ''", 'IN (...)', 'NOT IN (...)',
        'BETWEEN', 'NOT BETWEEN',
    ];

    /** Value length cap applied to every custom filter value (including REGEXP patterns), as a sanity safeguard. */
    private const CUSTOM_FILTER_VALUE_MAX_LENGTH = 500;

    /**
     * Decodes and validates raw custom-filter rows (from request input or a saved
     * model's custom_filters column) against a live whitelist of leads columns and
     * the fixed operator list above. Invalid/incomplete rows are silently dropped
     * rather than raising a validation error, so live filtering degrades gracefully
     * mid-edit. This is the only place custom filter column/operator whitelisting happens.
     *
     * @param mixed $raw array of ['column'=>..,'operator'=>..,'value'=>..] rows, or a JSON-encoded string of the same.
     * @return array<int, array{column:string, operator:string, value:string}>
     */
    private function validateCustomFilters($raw): array
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true) ?: [];
        }

        if (!is_array($raw)) {
            return [];
        }

        // Table structure only changes on deploy/migration, not per-request —
        // this was otherwise an information_schema round trip on every single
        // jsonData()/saveFilter() call (custom_filters is always sent, even
        // as an empty array). `php artisan cache:clear` after a schema change
        // refreshes it; that's an existing deploy step, not a new one.
        $allowedColumns = Cache::rememberForever(
            'lead_table_columns',
            fn() => Schema::getColumnListing((new Lead())->getTable())
        );

        $result = [];

        foreach ($raw as $row) {
            if (!is_array($row)) {
                continue;
            }

            $column   = is_string($row['column'] ?? null) ? trim($row['column']) : null;
            $operator = is_string($row['operator'] ?? null) ? trim($row['operator']) : null;
            $value    = $row['value'] ?? '';
            $value    = is_scalar($value) ? trim((string) $value) : '';

            if (blank($column) || !in_array($column, $allowedColumns, true)) {
                continue;
            }

            if (blank($operator) || !in_array($operator, self::CUSTOM_FILTER_OPERATORS, true)) {
                continue;
            }

            $isValueless = in_array($operator, ["= ''", "!= ''"], true);

            if (!$isValueless && $value === '') {
                continue;
            }

            if (in_array($operator, ['BETWEEN', 'NOT BETWEEN'], true)) {
                $parts = array_values(array_filter(array_map('trim', explode(',', $value)), fn($v) => $v !== ''));
                if (count($parts) !== 2) {
                    continue;
                }
            }

            if (strlen($value) > self::CUSTOM_FILTER_VALUE_MAX_LENGTH) {
                $value = substr($value, 0, self::CUSTOM_FILTER_VALUE_MAX_LENGTH);
            }

            $result[] = [
                'column'   => $column,
                'operator' => $operator,
                'value'    => $value,
            ];
        }

        return $result;
    }

    /**
     * Row-level visibility: mirrors jsonData()'s scoping rule everywhere a
     * single lead is opened/acted on directly by id — a staff member without
     * leads_view may only act on leads assigned to them. Prevents tampering
     * with lead_id in the request from reaching leads outside the caller's
     * scope (IDOR).
     */
    private function canAccessLead(?Lead $lead, $user, ?Adminpermission $permission): bool
    {
        if (!$lead) {
            return false;
        }

        return $user->user_type == 1
            || optional($permission)->leads_view == 1
            || $lead->leadassign_id == $user->id;
    }

    /**
     * Action-level permission: mirrors the full_access / per-action flag
     * gating that already decides which buttons index_new.blade.php and
     * jsonData()'s "actions" column show (delete / assign / bulk variants).
     */
    private function hasLeadPermission($user, ?Adminpermission $permission, string $key): bool
    {
        return $user->user_type == 1
            || optional($permission)->full_access == 1
            || optional($permission)->{$key} == 1;
    }

    public function index_new($leadId = null)
    {
        $user = Auth::guard('admin')->user();
        $autoOpenLeadId = $leadId ? (int) $leadId : null;

        $permission = Adminpermission::where(
            'staff_id',
            $user->id
        )->first();

        // These four are dropdown/master data (which staff exist, which
        // sources leads have been submitted from) — not per-request figures,
        // so a short-lived but non-trivial TTL is safe: it avoids recomputing
        // them (one of them, lead_sources, is a ~15k-row JSON scan) on every
        // single page load while staying acceptably fresh for an admin list.
        $leadassignees = Cache::remember(
            'lead_assignees',
            60,
            function () {
                return Admin::where('status', 1)
                    ->whereIn('id', function ($query) {
                        $query->select('leadassign_id')
                            ->from('leads')
                            ->whereNotNull('leadassign_id')
                            ->distinct();
                    })
                    ->get();
            }
        );

        $active_lead_assignee = $leadassignees->where('lead_assign_status', 1)->pluck('id')->toArray();

        $staffs = Cache::remember(
            'lead_staffs',
            60,
            fn() => Admin::where('status', 1)->get()
        );

        $saveadminfilter = LeadAdminSaveFilter::where(
            'admin_id',
            $user->id
        )->first();

        $sources = Cache::remember(
            'lead_sources',
            600,
            fn() => Lead::whereRaw("JSON_VALID(submit_lead_from)")
                ->selectRaw("
                    DISTINCT JSON_UNQUOTE(
                        JSON_EXTRACT(submit_lead_from,'$.from')
                    ) as source
                ")
                ->pluck('source')
        );

        return view(
            'admin.leads.index_new',
            compact(
                'permission',
                'leadassignees',
                'active_lead_assignee',
                'staffs',
                'saveadminfilter',
                'sources',
                'autoOpenLeadId'
            )
        );
    }

    public function jsonData(Request $request)
    {
        $user = Auth::guard('admin')->user();

        $isAdmin = $user->user_type == 1;

        $permission = Adminpermission::where(
            'staff_id',
            $user->id
        )->first();

        // Only the columns actually read by the addColumn()/filter callbacks
        // below — the leads table also carries several large text/JSON
        // columns (lead_request_data, submit_lead_from, qualified_reason,
        // ...) that this list never displays and that would otherwise be
        // fetched for every row on every page/sort/search request. WHERE/
        // ORDER BY filters further down (search, saved filters, custom
        // filters) can still reference any column — MySQL doesn't require a
        // column to be selected to filter or sort by it.
        $posts = Lead::query()
            ->select([
                'id', 'cand_name', 'company_name', 'mob_no', 'whatsapp_no',
                'required_service', 'other_job_title', 'experience',
                'saudi_license', 'india_license', 'country', 'expected_days',
                'message', 'lead_date', 'leadassign_id', 'is_qualified',
                'is_repeted', 'candidate_updated_at',
            ])
            ->with([
                'leadassign:id,name'
            ])->orderBy('lead_date', 'DESC');

        /*
        |--------------------------------------------------------------------------
        | Permission
        |--------------------------------------------------------------------------
        */

        if (
            !$isAdmin &&
            optional($permission)->leads_view == 0
        ) {
            $posts->where(
                'leadassign_id',
                $user->id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Request Filters
        |--------------------------------------------------------------------------
        */

        $customFilters = $this->validateCustomFilters($request->input('custom_filters'));

        $hasRequestFilter = filled($customFilters) || collect([
            'by_assignee',
            'by_driving_license',
            'by_job_title',
            'by_expected_days',
            'by_expected_country',
            'by_submit_from',
            'by_looking_for',
            'by_is_qualified',
            'by_call_not_connected_type',
            'by_lead_date',
            'by_candidate_updated_date',
            'staff_updated_at',
            'by_followup_before',
            'by_location'
        ])->contains(
            fn($field) => filled($request->$field)
        );

        if ($hasRequestFilter) {

            if (filled($request->by_is_qualified) && $request->by_is_qualified !== 'All') {
               $posts->filterQualifiedStatus($request->by_is_qualified);
            }

            if (filled($request->by_call_not_connected_type)) {
                $posts->filterCallNotConnectedType($request->by_call_not_connected_type);
            }

            if (filled($request->by_assignee)) {
                $posts->whereIn(
                    'leadassign_id',
                    (array) $request->by_assignee
                );
            }

            if (filled($request->by_driving_license)) {
                $posts->filterDrivingLicense(
                    $request->by_driving_license, 
                );
            }

            if (filled($request->by_job_title)) {
                $posts->whereIn(
                    'required_service',
                    (array) $request->by_job_title
                );
            }

            if (filled($request->by_expected_days)) {
                $posts->whereIn(
                    'expected_days',
                    (array) $request->by_expected_days
                );
            }

            if (filled($request->by_expected_country)) {
                $posts->whereIn(
                    'country',
                    (array) $request->by_expected_country
                );
            }

            if (filled($request->by_submit_from)) {

                $posts->where(function ($query) use ($request) {

                    foreach ((array)$request->by_submit_from as $source) {

                        $query->orWhereRaw(
                            "JSON_UNQUOTE(JSON_EXTRACT(submit_lead_from,'$.from')) = ?",
                            [$source]
                        );
                    }
                });
            }

            if (filled($request->by_looking_for)) {
                $posts->whereIn(
                    'looking_for',
                    (array) $request->by_looking_for
                );
            }

            if (filled($request->by_location)) {

                $location = trim(
                    $request->by_location
                );

                $posts->where(function ($q) use ($location) {

                    $q->whereRaw(
                        "LOWER(JSON_UNQUOTE(JSON_EXTRACT(submit_lead_from,'$.country'))) LIKE LOWER(?)",
                        ["%{$location}%"]
                    )
                        ->orWhereRaw(
                            "LOWER(JSON_UNQUOTE(JSON_EXTRACT(submit_lead_from,'$.region'))) LIKE LOWER(?)",
                            ["%{$location}%"]
                        )
                        ->orWhereRaw(
                            "LOWER(JSON_UNQUOTE(JSON_EXTRACT(submit_lead_from,'$.city'))) LIKE LOWER(?)",
                            ["%{$location}%"]
                        );
                });
            }

            if (filled($request->by_lead_date)) {

                [$from, $to] = explode(
                    ' - ',
                    $request->by_lead_date
                );

                $posts->whereBetween(
                    'lead_date',
                    [
                        Carbon::parse($from)->startOfDay(),
                        Carbon::parse($to)->endOfDay()
                    ]
                );
            }

            if (filled($request->by_candidate_updated_date)) {

                [$from, $to] = explode(
                    ' - ',
                    $request->by_candidate_updated_date
                );

                $posts->whereBetween(
                    'candidate_updated_at',
                    [
                        Carbon::parse($from)->startOfDay(),
                        Carbon::parse($to)->endOfDay()
                    ]
                );
            }

            if (filled($request->staff_updated_at)) {

                [$from, $to] = explode(
                    ' - ',
                    $request->staff_updated_at
                );

                $posts->whereBetween(
                    'staff_updated_at',
                    [
                        Carbon::parse($from)->startOfDay(),
                        Carbon::parse($to)->endOfDay()
                    ]
                );
            }

            if (filled($request->by_followup_before)) {

                $days = (int) $request->by_followup_before;

                $from = Carbon::now()
                    ->subDays($days);

                $to = Carbon::now();

                if ($days == 1) {
                    $from = Carbon::today();
                    $to = Carbon::today();
                }

                $posts->whereBetween(
                    'staff_updated_at',
                    [
                        $from->startOfDay(),
                        $to->endOfDay()
                    ]
                );
            }

            $posts->FilterCustom($customFilters);

        } else {

            $saved = LeadAdminSaveFilter::where(
                'admin_id',
                $user->id
            )->first();

            if ($saved) {

                if (
                    !empty($saved->by_is_qualified) &&
                    $saved->by_is_qualified !== 'All'
                ) {
                    $posts->filterQualifiedStatus(explode(',', $saved->by_is_qualified));
                }

                if (!empty($saved->by_call_not_connected_type)) {
                    $posts->filterCallNotConnectedType(
                        explode(',', $saved->by_call_not_connected_type)
                    );
                }

                if (!empty($saved->by_assignee)) {

                    $posts->whereIn(
                        'leadassign_id',
                        explode(',', $saved->by_assignee)
                    );
                }

                if (!empty($saved->by_driving_license)) {

                    $posts->filterDrivingLicense(
                        explode(',', $saved->by_driving_license)
                    );
                }

                if (!empty($saved->by_job_title)) {

                    $posts->whereIn(
                        'required_service',
                        explode(',', $saved->by_job_title)
                    );
                }

                if (!empty($saved->by_expected_days)) {

                    $posts->whereIn(
                        'expected_days',
                        explode(',', $saved->by_expected_days)
                    );
                }

                if (!empty($saved->by_expected_country)) {

                    $posts->whereIn(
                        'country',
                        explode(',', $saved->by_expected_country)
                    );
                }

                if (!empty($saved->by_submit_from)) {

                    $sources = explode(
                        ',',
                        $saved->by_submit_from
                    );

                    $posts->where(function ($query) use ($sources) {

                        foreach ($sources as $source) {

                            $query->orWhereRaw(
                                "JSON_UNQUOTE(JSON_EXTRACT(submit_lead_from,'$.from')) = ?",
                                [$source]
                            );
                        }
                    });
                }

                if (!empty($saved->by_looking_for)) {

                    $posts->whereIn(
                        'looking_for',
                        explode(',', $saved->by_looking_for)
                    );
                }


                if (!empty($saved->by_lead_date)) {

                    $range = explode(
                        ' - ',
                        $saved->by_lead_date
                    );

                    if (count($range) == 2) {

                        $posts->whereBetween(
                            'lead_date',
                            [
                                Carbon::parse($range[0])->startOfDay(),
                                Carbon::parse($range[1])->endOfDay()
                            ]
                        );
                    }
                }

                if (!empty($saved->by_candidate_updated_date)) {

                    $range = explode(
                        ' - ',
                        $saved->by_candidate_updated_date
                    );

                    if (count($range) == 2) {

                        $posts->whereBetween(
                            'candidate_updated_at',
                            [
                                Carbon::parse($range[0])->startOfDay(),
                                Carbon::parse($range[1])->endOfDay()
                            ]
                        );
                    }
                }

                if (!empty($saved->staff_updated_at)) {

                    $range = explode(
                        ' - ',
                        $saved->staff_updated_at
                    );

                    if (count($range) == 2) {

                        $posts->whereBetween(
                            'updated_at',
                            [
                                Carbon::parse($range[0])->startOfDay(),
                                Carbon::parse($range[1])->endOfDay()
                            ]
                        );
                    }
                }

                if (!empty($saved->by_followup_before)) {

                    $days = (int) $saved->by_followup_before;

                    $from = Carbon::now()->subDays($days);
                    $to = Carbon::now();

                    if ($days == 1) {

                        $from = Carbon::today();
                        $to = Carbon::today();
                    }

                    $posts->whereBetween(
                        'staff_updated_at',
                        [
                            $from->startOfDay(),
                            $to->endOfDay()
                        ]
                    );
                }

                if (!empty($saved->by_location)) {

                    $location = trim(
                        $saved->by_location
                    );

                    $posts->where(function ($q) use ($location) {

                        $q->whereRaw(
                            "LOWER(JSON_UNQUOTE(JSON_EXTRACT(submit_lead_from,'$.country'))) LIKE LOWER(?)",
                            ["%{$location}%"]
                        )
                            ->orWhereRaw(
                                "LOWER(JSON_UNQUOTE(JSON_EXTRACT(submit_lead_from,'$.region'))) LIKE LOWER(?)",
                                ["%{$location}%"]
                            )
                            ->orWhereRaw(
                                "LOWER(JSON_UNQUOTE(JSON_EXTRACT(submit_lead_from,'$.city'))) LIKE LOWER(?)",
                                ["%{$location}%"]
                            );
                    });
                }

                $posts->FilterCustom($this->validateCustomFilters($saved->custom_filters));

            }
        }


        // Global summary counts: scoped only by visibility permission, never by the
        // active table filters, so the status cards always show fixed/global totals.
        // This is a full-table aggregate (15 SUM/CASE branches over every row) that
        // otherwise re-runs on every single DataTable draw — initial load, each
        // keystroke in search, every filter/sort/page change — since it doesn't
        // depend on any of those. It's identical for every full-view user, so one
        // shared cache entry (or one per restricted-view user, since their scope
        // differs) is safe; a short TTL keeps the status cards effectively live.
        $isScopedToOwn = !$isAdmin && optional($permission)->leads_view == 0;
        $countsCacheKey = $isScopedToOwn ? "lead_counts_user_{$user->id}" : 'lead_counts_global';

        $counts = Cache::remember($countsCacheKey, 10, function () use ($isScopedToOwn, $user) {
            return Lead::query()
                ->when(
                    $isScopedToOwn,
                    fn ($q) => $q->where('leadassign_id', $user->id)
                )
                ->selectRaw("
                    COUNT(*) as total,

                    SUM(CASE WHEN is_repeted = 1 AND candidate_updated_at IS NOT NULL THEN 1 ELSE 0 END) AS previous_lead,

                    SUM(is_reassigned = 1) as reassigned,

                    SUM(COALESCE(is_qualified,0) = 0) as qualified_0,
                    SUM(is_qualified = 1) as qualified_1,
                    SUM(is_qualified = 2) as qualified_2,
                    SUM(is_qualified = 3) as qualified_3,
                    SUM(is_qualified = 4) as qualified_4,
                    SUM(is_qualified = 5) as qualified_5,

                    SUM(call_not_connected_type = 'Not Answer') as cnc_not_answer,
                    SUM(call_not_connected_type = 'Busy') as cnc_busy,
                    SUM(call_not_connected_type = 'Not Reachable') as cnc_not_reachable,
                    SUM(call_not_connected_type = 'Not Available') as cnc_not_available,
                    SUM(call_not_connected_type = 'Wrong Number') as cnc_wrong_number,
                    SUM(call_not_connected_type IS NULL) as cnc_null
                ")
                ->first();
        });

        // The "Not Yet" card must reflect the selected Lead Created Date range
        // (unlike the other status cards above, which stay global on purpose).
        // Recompute it from real DB rows scoped to that range instead of the
        // unfiltered total qualified_0 already carries.
        if (filled($request->by_lead_date)) {

            [$from, $to] = explode(
                ' - ',
                $request->by_lead_date
            );

            $counts->qualified_0 = Lead::query()
                ->when(
                    !$isAdmin &&
                    optional($permission)->leads_view == 0,
                    fn ($q) => $q->where('leadassign_id', $user->id)
                )
                ->where(function ($q) {
                    $q->whereNull('is_qualified')
                        ->orWhere('is_qualified', 0);
                })
                ->whereBetween('lead_date', [
                    Carbon::parse($from)->startOfDay(),
                    Carbon::parse($to)->endOfDay(),
                ])
                ->count();
        }

            


        return DataTables::of($posts)

            ->with([
                'counts' => $counts,
            ])

            ->filter(function ($query) use ($request) {

                $search = data_get(
                    $request->all(),
                    'search.value'
                );

                if (filled($search)) {
                    $query->filterSearchText($search);
                }
            })

            ->addColumn('checkbox', function ($row) {

                return '
                <div class="form-check form-check-inline">

                    <input
                        class="form-check-input dt-checkboxes sub-chk"
                        data-id="' . $row->id . '"
                        type="checkbox"
                        value="' . $row->id . '"
                        id="checkbox' . $row->id . '" />

                    <label
                        class="form-check-label"
                        for="checkbox' . $row->id . '">
                    </label>

                </div>';
            })

            ->addColumn('name', function ($row) {

                return '
                <a href="javascript:void(0);"
                id="dtx_lead_name_' . $row->id . '"
                class="text-body view-lead-btn"
                data-id="' . $row->id . '">

                    ' . e($row->cand_name ?? '---') . '

                </a>';
            })

            ->addColumn('company', function ($row) {

                return '
                    <span id="dtx_company_' . $row->id . '">
                        ' . e($row->company_name ?: '---') . '
                    </span>
                ';
            })

            ->addColumn('mobile', function ($row) {

                return '
                <a href="javascript:void(0);"
                id="dtx_mobile_' . $row->id . '"
                class="editContact"
                data-id="' . $row->id . '"
                data-mob="' . e($row->mob_no) . '"
                data-whatsapp="' . e($row->whatsapp_no) . '">

                    ' . e($row->mob_no ?: '---') . '

                </a>';
            })

            ->addColumn('whatsapp', function ($row) {

                return '
                <a href="javascript:void(0);"
                id="dtx_whatsapp_' . $row->id . '"
                class="editContact"
                data-id="' . $row->id . '"
                data-mob="' . e($row->mob_no) . '"
                data-whatsapp="' . e($row->whatsapp_no) . '">

                    ' . e($row->whatsapp_no ?: '---') . '

                </a>';
            })

            ->addColumn('job_title', function ($row) {

                $fullJobTitle = $row->required_service;

                if ($row->required_service === 'Other') {
                    $fullJobTitle = $row->other_job_title ?? '---';
                } else {
                    $fullJobTitle = preg_replace('/\s*\([^)]*\)/', '', $row->required_service);
                }

                return '
                    <span id="dtx_job_title_' . $row->id . '">
                        ' . e(\Illuminate\Support\Str::limit($fullJobTitle, 13, '..')) . '
                    </span>
                ';
            })

            ->addColumn('experience', function ($row) {

                if (empty($row->experience)) {
                    return '<div id="dtx_experience_' . $row->id . '">---</div>';
                }

                return '
                    <div id="dtx_experience_' . $row->id . '">
                        ' .
                    collect(explode(',', $row->experience))
                    ->map(function ($exp) {
                        return '<span class="badge rounded-pill bg-label-primary">'
                            . e(trim($exp)) .
                            '</span>';
                    })
                    ->implode(' ')
                    . '
                    </div>
                ';
            })

            ->addColumn('driving_license', function ($row) {

                $licenses = [];

                if ($row->saudi_license == 'yes') {
                    $licenses[] = '
                        <span class="badge rounded-pill bg-label-primary">
                            Saudi License
                        </span>';
                }

                if ($row->india_license == 'yes') {
                    $licenses[] = '
                        <span class="badge rounded-pill bg-label-primary">
                            Indian License
                        </span>';
                }

                return '
                    <div id="dtx_driving_license_' . $row->id . '">
                        ' . (count($licenses) ? implode(' ', $licenses) : '---') . '
                    </div>
                ';
            })

            ->addColumn('country', function ($row) {

                return '
                    <span id="dtx_country_' . $row->id . '">
                        ' . e($row->country ?: '---') . '
                    </span>
                ';
            })

            ->addColumn('expected_days', function ($row) {

                return '
                    <span id="dtx_expected_days_' . $row->id . '">
                        ' . e($row->expected_days ?: '---') . '
                    </span>
                ';
            })

            ->addColumn('message', function ($row) {

                $msgBody = strlen($row->message) >= 13
                    ? substr($row->message, 0, 13) . '..'
                    : ($row->message ?: '---');

                return '
                    <span
                        id="dtx_message_' . $row->id . '"
                        title="' . e($row->message) . '">
                        ' . e($msgBody) . '
                    </span>
                ';
            })

            ->addColumn('lead_date', function ($row) {

                return '
                    <span id="dtx_lead_date_' . $row->id . '">
                        ' . (
                    $row->lead_date
                    ? date('d-m-Y h:i', strtotime($row->lead_date))
                    : '---'
                ) . '
                    </span>
                ';
            })

            ->addColumn('assignee', function ($row) use ($permission) {

                $admin = Auth::guard('admin')->user();

                if (!empty($row->leadassign_id)) {

                    return '
                        <span id="dtx_assignee_' . $row->id . '">
                            ' . e($row->leadassign->name ?? '---') . '
                        </span>
                    ';
                }

                if (
                    $admin->user_type == 1 ||
                    (
                        isset($permission) &&
                        (
                            $permission->full_access == 1 ||
                            $permission->leads_assignto == 1
                        )
                    )
                ) {

                    return '
                    <span id="dtx_assignee_' . $row->id . '">
                        <a
                            href="javascript:void(0);"
                            data-bs-toggle="modal"
                            data-bs-target="#assignlead"
                            data-id="' . $row->id . '">

                            <i class="ti ti-refresh me-2"></i>

                        </a>
                    </span>';
                }

                return '<span id="dtx_assignee_' . $row->id . '">---</span>';
            })

            ->addColumn('qualified', function ($row) {

                $badge = match ($row->is_qualified) {
                    null => '<span class="badge bg-label-warning">Not Yet</span>',
                    1    => '<span class="badge bg-label-info">Followed Up</span>',
                    2    => '<span class="badge bg-label-secondary">Call Not Connected</span>',
                    3    => '<span class="badge bg-label-success">Lead Qualified</span>',
                    4    => '<span class="badge bg-label-danger">Lead Not Qualified</span>',
                    5    => '<span class="badge bg-label-dark">Lead Not Relevant</span>',
                    default => '<span class="badge bg-label-dark">Unknown</span>',
                };

               $previousLeadBadge = ($row->is_repeted && !is_null($row->candidate_updated_at))
                ? '<span class="badge bg-label-warning ms-1">Previous Lead</span>'
                : '';

                return '
                    <div id="dtx_qualified_' . $row->id . '">
                        <a 
                            class="view-lead-btn"
                            id="dtx_lead_name_' . $row->id . '"
                            href="javascript:void(0);"
                            data-bs-toggle="modal"
                            data-bs-target="#viewLeadModal"
                            data-id="' . $row->id . '">

                            ' . $badge . '
                            ' . $previousLeadBadge . '

                        </a>
                    </div>';
            })

            // ->addColumn('status', function ($row) {
            //     return '
            //         <span id="dtx_status_' . $row->id . '" class="badge bg-label-success">
            //             ' . ($row->lead_status_text ?: 'New') . '
            //         </span>
            //     ';
            // })

            ->addColumn('actions', function ($row) use ($permission) {

                $admin = Auth::guard('admin')->user();

                $html = '
                <div class="dropdown">

                    <button
                        class="btn p-0"
                        type="button"
                        id="leadActions' . $row->id . '"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">

                        <i class="ti ti-dots-vertical ti-sm text-muted"></i>

                    </button>

                    <div
                        class="dropdown-menu dropdown-menu-end"
                        aria-labelledby="leadActions' . $row->id . '">';


                if (
                    $admin->user_type == 1 ||
                    (isset($permission) && $permission->full_access == 1)
                ) {

                    $html .= '

                        <a
                            class="dropdown-item"
                            href="javascript:void(0);"
                            data-bs-toggle="modal"
                            data-bs-target="#deletelead"
                            data-id="' . $row->id . '">

                            <i class="ti ti-trash me-2"></i>

                            Delete Lead

                        </a>

                        <a
                            class="dropdown-item"
                            href="javascript:void(0);"
                            data-bs-toggle="modal"
                            data-bs-target="#assignlead"
                            data-id="' . $row->id . '">

                            <i class="ti ti-refresh me-2"></i>

                            Assign Lead

                        </a>

                        <a
                            class="dropdown-item"
                            href="javascript:void(0);"
                            data-bs-toggle="modal"
                            data-bs-target="#viewLeadModal"
                            data-id="' . $row->id . '">

                            <i class="ti ti-check me-2"></i>

                            Qualified Lead

                        </a>';
                } elseif (
                    $admin->user_type == 2 ||
                    (isset($permission) && $permission->full_access == 0)
                ) {

                    if (
                        isset($permission) &&
                        $permission->leads_delete == 1
                    ) {

                        $html .= '

                        <a
                            class="dropdown-item"
                            href="javascript:void(0);"
                            data-bs-toggle="modal"
                            data-bs-target="#deletelead"
                            data-id="' . $row->id . '">

                            <i class="ti ti-trash me-2"></i>

                            Delete Lead

                        </a>';
                    }

                    if (
                        isset($permission) &&
                        $permission->leads_assignto == 1
                    ) {

                        $html .= '

                        <a
                            class="dropdown-item"
                            href="javascript:void(0);"
                            data-bs-toggle="modal"
                            data-bs-target="#assignlead"
                            data-id="' . $row->id . '">

                            <i class="ti ti-refresh me-2"></i>

                            Assign Lead

                        </a>';
                    }

                    $html .= '

                        <a
                            class="dropdown-item"
                            href="javascript:void(0);"
                            data-bs-toggle="modal"
                            data-bs-target="#viewLeadModal"
                            data-id="' . $row->id . '">

                            <i class="ti ti-check me-2"></i>

                            Qualified Lead

                        </a>';
                }

                $html .= '
                    </div>
                </div>';

                return $html;
            })

            ->rawColumns([
                'checkbox',
                'name',
                'company',
                'mobile',
                'whatsapp',
                'job_title',
                'experience',
                'driving_license',
                'country',
                'expected_days',
                'message',
                'lead_date',
                'assignee',
                'qualified',
                // 'status',
                'actions'
            ])

            ->make(true);
    }

    public function show($id)
    {
        $lead = Lead::with([
            'notes' => function ($q) {
                $q->latest();
            },
            'notes.admin'
        ])->findOrFail($id);

        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if (!$this->canAccessLead($lead, $user, $permission)) {
            abort(403, 'You are not authorized to view this lead.');
        }

        return response()->json($lead);
    }

    public function lead_store_new(Request $request)
    {
        try {
            $request_data = $request->all();
            $ip = $request->ip_address;
            $location = Location::get($ip);
            $country = $location->countryName ?? null;
            $region  = $location->regionName ?? null;
            $city    = $location->cityName ?? null;

            $lead_auto_assign_status = false;
            $LeadAutoAssignStatus = LeadAutoAssignStatus::first();

            if ($LeadAutoAssignStatus && $LeadAutoAssignStatus->isActive()) {
                $lead_auto_assign_status = true;
            } else {
                $lead_auto_assign_status = false;
            }

            $submitLeadFrom = [
                'from'    => $request_data['submit_lead_from'] ?? 'www.qamrjob.com',
                'ip'      => $ip,
                'country' => $country,
                'region'  => $region,
                'city'    => $city,
            ];

            $is_repeted = false;


            // 🔹 If new lead
            if ($request_data['firstRequest'] == '1') {

                $existLead = Lead::where('mob_no', $request_data['mob_no'])
                        ->exists();

                $is_repeted = $existLead;

                $admin = DB::table('admins as admin')
                    ->leftJoin('leads as lead', 'lead.leadassign_id', '=', 'admin.id')
                    ->select('admin.id', DB::raw('COUNT(lead.id) as lead_count'))
                    ->groupBy('admin.id')
                    ->orderBy('lead_count', 'asc')
                    ->where('admin.lead_assign_status', true)
                    ->get();

                $post = new Lead();
                $post->cand_name = $request_data['cand_name'] ?? null;
                $post->mob_no = $request_data['mob_no'] ?? null;
                $post->email = $request_data['email'] ?? null;
                $post->required_service = $request_data['required_service'] ?? null;
                $post->message = $request_data['message'] ?? null;
                $post->lead_date = now();
                $post->lead_status_text = "New";
                $post->lead_source = $request_data['lead_source'] ?? "www.qamrjob.com";
                $post->company_name = $request_data['company_name'] ?? null;
                $post->looking_for = $request_data['looking_for'] ?? null;

                // 🧩 Add missing fields
                $post->saudi_license  = $request_data['saudi_license'] ?? null;
                $post->india_license  = $request_data['india_license'] ?? null;
                $post->whatsapp_no    = $request_data['whatsapp_no'] ?? null;
                $post->expected_days  = $request_data['expected_days'] ?? null;
                $post->experience     = $request_data['experience'] ?? null;
                $post->country        = $request_data['country'] ?? null;
                $post->no_of_requirement        = $request_data['no_of_requirement'] ?? null;
                if (!empty($request_data['required_service']) && $request_data['required_service'] == 'Other') {
                    $post->other_job_title = $request_data['job_title_sel'] ?? null;
                }

                if(!$is_repeted){
                    if ($lead_auto_assign_status) {
                        if (count($admin) > 0) {
                            $assignStaff = $admin->shuffle()->first();
                            $post->leadassign_id = $assignStaff->id;
                        }
                    }
                }

                // Store IP/location JSON
                $post->submit_lead_from = json_encode($submitLeadFrom);
                $post->lead_request_data = json_encode($request->all());
                // $post->leadassign_id = $request_data['tid'] ?? null;
                $post->candidate_updated_at = null;     
                if($is_repeted){
                     $existLead = Lead::where('mob_no', $request_data['mob_no'])->first();
                    if ($existLead && !$existLead->created_at->isToday()) {
                        $post->candidate_updated_at = now();
                    }
                      
                }           
                $post->updated_at = null;               
                $post->is_repeted = $is_repeted;
                $post->save();


                if($is_repeted){
                    $this->leadAssignedProcess($post);
                }

                // AutoSendMessageForLeadJobs::dispatch($post)->onQueue('default');
                // $this->createScheduledAutomationForLead($post);
                // $this->createEmailScheduledAutomationForLead($post);

                return response()->json([
                    'res_status' => 'true',
                    'message' => 'Lead stored successfully',
                    'data' => $post,
                ], 200);
            }
            // 🔹 If updating existing lead
            else {

                // NOTE (security): this is a public, unauthenticated endpoint
                // and lead_id is a guessable sequential id — there is no
                // check here that the caller is the visitor who created this
                // lead, so a crafted request with someone else's lead_id can
                // overwrite their contact/service fields. Closing this the
                // same way leadshow()/update_contact() were closed (requiring
                // the caller's own mob_no to match) needs confirmation from
                // whoever maintains the public form's JS that mob_no is sent
                // on this "continue filling the form" step too, since this is
                // the primary public lead-capture path and a wrong guess here
                // would break live lead intake — see the Leads module audit.
                $post = Lead::find($request_data['lead_id']);
                if (!$post) {
                    return response()->json([
                        'res_status' => 'false',
                        'message' => 'Lead not found',
                    ], 404);
                }

                if ($post->whatsapp_no == ($request_data['whatsapp_no'] ?? null)) {
                    $is_repeted = true;
                }

                // Update fields
                $post->cand_name = $request_data['cand_name'] ?? $post->cand_name;
                $post->email = $request_data['email'] ?? $post->email;
                $post->required_service = $request_data['required_service'] ?? $post->required_service;
                $post->message = $request_data['message'] ?? $post->message;
                $post->saudi_license  = $request_data['saudi_license'] ?? $post->saudi_license;
                $post->india_license  = $request_data['india_license'] ?? $post->india_license;
                $post->whatsapp_no    = $request_data['whatsapp_no'] ?? $post->whatsapp_no;
                $post->expected_days  = $request_data['expected_days'] ?? $post->expected_days;
                $post->experience     = $request_data['experience'] ?? $post->experience;
                $post->country        = $request_data['country'] ?? $post->country;
                $post->no_of_requirement = $request_data['no_of_requirement'] ?? $post->no_of_requirement;
                $post->looking_for = $request_data['looking_for'] ?? $post->looking_for;
                
                $post->lead_date = now();
                $post->submit_lead_from = json_encode($submitLeadFrom);
                $post->lead_request_data = json_encode($request->all());
                // $post->leadassign_id = $request_data['tid'] ?? null;    
                $post->candidate_updated_at = null;     
                
                if($is_repeted){
                    if ($post && !$post->created_at->isToday()) {
                        $post->candidate_updated_at = now();
                    }
                }      
                    
                $post->updated_at = null;  
                $post->is_repeted = $is_repeted;
                $post->save();

                AutoSendMessageForLeadJobs::dispatch($post)->onQueue('default');

                // Saudi logic (single source) — mirrors the CRM's own "KSA DL" filter
                // (Lead::scopeFilterDrivingLicense) via Lead::isKsaDrivingLicenseLead(),
                // so the frontend's decision to fire the Meta Pixel (based on this same
                // response) and the backend's decision to fire the Meta CAPI event can't
                // disagree with each other or with the CRM's own reporting.
                $isSaudiLead = $post->isKsaDrivingLicenseLead();

                // Idempotency
                if (!$post->meta_lead_sent && $isSaudiLead) {

                    // $sent = $this->sendMetaLeadEvent($post, $request);
                    $this->metaConversionService->sendLeadStatusEvent($post, $request);
                }

                $this->createScheduledAutomationForLead($post);
                $this->createEmailScheduledAutomationForLead($post);

                if($is_repeted){
                    $this->leadAssignedProcess($post);
                }
                return response()->json([
                    'res_status' => 'true',
                    'message' => 'Lead updated successfully',
                    'data' => $post,
                    'meta_qualifies' => $isSaudiLead,
                ], 200);
            }
        } catch (\Exception $e) {

            Log::channel('facebook_capi')->error('Error in lead_store:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return response()->json([
                'res_status' => 'false',
                'message' => 'Something went wrong while saving the lead.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function leadAssignedProcess($lead)
    {
        $lead = Lead::find($lead->id);

        if (empty($lead->leadassign_id)) {
            return;
        }

        $hasRecentNote = Leadnote::where('lead_id', $lead->id)
            ->where('admin_id', $lead->leadassign_id)
            ->where('created_at', '>=', now()->subWeek())
            ->exists();

        if (! $hasRecentNote) {

            $assignStaff = Admin::where('login_status', 1) 
                ->where('status', 1)
                ->whereJsonContains('role', 'Candidate Source')
                ->where('id', '!=', $lead->leadassign_id)
                ->inRandomOrder()
                ->first();

            if ($assignStaff) {
                $lead->update([
                    'leadassign_id' => $assignStaff->id,
                ]);
            }
        }
    }
    
    public function createScheduledAutomationForLead($lead)
    {
        try {

            \Log::info('⏳ Starting Scheduled Automation Creation', [
                'lead_id' => $lead->id ?? null
            ]);

            // 1️⃣ Fetch Active Autometa Notifications for LEADS
            $notifications = Autometanotification::where('template_table_name', 'leads')
                ->where('status', 1)
                ->get();

            if ($notifications->isEmpty()) {

                \Log::warning('⚠ No active autometa notifications found for leads');
                return false;
            }

            foreach ($notifications as $noti) {

                try {

                    $minutes = 0;
                    $trigger_template_time = 'Immediate';

                    // -----------------------------------
                    // 🔥 NEW LOGIC → Based on action_type
                    // -----------------------------------

                    if ($noti->action_type === 'wait') {

                        $time = (int) ($noti->trigger_template_time ?? 0);
                        $type = strtolower($noti->trigger_template_time_type ?? '');

                        switch ($type) {

                            case 'minute':
                                $minutes = $time;
                                break;

                            case 'hour':
                                $minutes = $time * 60;
                                break;

                            case 'day':
                                $minutes = $time * 60 * 24;
                                break;

                            case 'week':
                                $minutes = $time * 60 * 24 * 7;
                                break;

                            case 'month':
                                $minutes = $time * 60 * 24 * 30;
                                break;

                            case 'year':
                                $minutes = $time * 60 * 24 * 365;
                                break;

                            default:
                                \Log::error("❌ Invalid trigger time type", [
                                    'notification_id' => $noti->id,
                                    'type' => $type
                                ]);
                                continue 2; // ✅ FIXED
                        }

                        $trigger_template_time = trim($noti->trigger_template_time . ' ' . $type);
                    }

                    // -----------------------------------
                    // 3️⃣ Calculate final time
                    // -----------------------------------
                    $finalTime = now()->addMinutes($minutes);

                    // -----------------------------------
                    // 4️⃣ Store Scheduling Entry
                    // -----------------------------------

                    ScheduledSendMsgAutomation::create([
                        'template_table_name'       => $noti->template_table_name,
                        'template_for'              => $noti->template_for,
                        'autometanotifications_id'  => $noti->id,
                        'metatemp_id'               => $noti->metatemp_id,
                        'send_user_to'              => $lead->id,
                        'trigger_template_time'     => $trigger_template_time,
                        'calculated_time'           => $finalTime,
                        'status'                    => 0,
                    ]);

                    \Log::info('✅ Scheduled automation created (LEAD)', [
                        'notification_id' => $noti->id,
                        'lead_id'         => $lead->id,
                        'scheduled_for'   => $finalTime,
                        'action_type'     => $noti->action_type
                    ]);
                } catch (\Exception $ex) {

                    \Log::error('❌ Error while processing notification', [
                        'error' => $ex->getMessage(),
                        'line'  => $ex->getLine(),
                        'file'  => $ex->getFile(),
                        'notification_id' => $noti->id ?? null
                    ]);
                }
            }

            \Log::info('🎉 Completed Scheduled Automation Creation');

            return true;
        } catch (\Exception $e) {

            \Log::error('❌ Fatal Error: createScheduledAutomationForLead()', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    public function createEmailScheduledAutomationForLead($lead)
    {
        try {

            // 1️⃣ Fetch Active Auto-Email Notifications
            $notifications = Autoemailnotification::where('template_table_name', 'leads')
                ->where('status', 1)
                ->get();

            if ($notifications->isEmpty()) {
                return false;
            }

            foreach ($notifications as $noti) {
                try {
                    // --------------------------------
                    // 2️⃣ Calculate Timing (Same Logic)
                    // --------------------------------
                    $minutes = 0;
                    $time = (int) ($noti->trigger_template_time ?? 0);
                    $type = strtolower($noti->trigger_template_time_type ?? '');

                    if ($noti->trigger_template_type == 'wait') {

                        // Convert time → minutes
                        $minutes = match ($type) {
                            'minute' => $time,
                            'hour'   => $time * 60,
                            'day'    => $time * 1440,
                            'week'   => $time * 10080,
                            'month'  => $time * 43200,
                            'year'   => $time * 525600,
                            default  => null,
                        };

                        if ($minutes === null) {
                            continue;
                        }

                        $trigger_template_time = trim($noti->trigger_template_time . ' ' . $type);
                    } else {
                        // Immediate → send after 1 minute
                        $minutes = 1;
                        $trigger_template_time = 'Now';
                    }

                    $finalTime = now()->addMinutes($minutes);

                    // --------------------------------
                    // 3️⃣ Insert Into Email Scheduler
                    // --------------------------------
                    ScheduledSendEmailAutomation::create([
                        'template_table_name'      => $noti->template_table_name,
                        'template_for'             => $noti->template_for,
                        'autoemailnotifications_id' => $noti->id,
                        'emailtemp_id'             => $noti->emailtemp_id,
                        'send_user_to'             => $lead->id,
                        'trigger_template_time'    => $trigger_template_time,
                        'calculated_time'          => $finalTime,
                        'status'                   => 0,
                    ]);
                } catch (\Exception $ex) {
                    Log::channel('scheduled_email_automation')->error('❌ Error while processing email notification', [
                        'error' => $ex->getMessage(),
                        'line' => $ex->getLine(),
                        'file' => $ex->getFile(),
                        'notification_id' => $noti->id
                    ]);
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::channel('scheduled_email_automation')->error('❌ Fatal Error in createEmailScheduledAutomationForLead()', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return false;
        }
    }

    public function getClientIp()
    {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // remove spaces
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip; // ✅ public IP only
                    }
                }
            }
        }

        return request()->ip(); // fallback
    }

    public function update_contact(Request $request)
    {
        $request->validate([
            'lead_id'     => 'required|integer',
            'email'       => 'nullable|email|max:255',
            'whatsapp_no' => 'nullable|string|max:20',
        ]);

        // This is a public, unauthenticated endpoint — lead_id alone is a
        // guessable sequential id, so it cannot prove the caller is the same
        // visitor who owns this lead. Requiring their own mobile number
        // (something only they would know, distinct from the whatsapp_no
        // being changed here) closes that IDOR without needing a login.
        $mobile = $request->input('mob_no', $request->input('mobile_no'));

        if (blank($mobile)) {
            abort(404);
        }

        $post = Lead::where('id', $request->lead_id)
            ->where(function ($q) use ($mobile) {
                $q->where('mob_no', $mobile)->orWhere('whatsapp_no', $mobile);
            })
            ->first();

        if (!$post) {
            abort(404);
        }

        $post->email = $request->email;
        $post->whatsapp_no = $request->whatsapp_no;
        $post->staff_updated_at = now();
        $post->save();

        return response()->json([
            'res_status' => 'true',
            'message' => 'Lead contact updated successfully',
            'data' => $post,
        ], 200);
    }

    public function deactiveassigneelead(Request $request)
    {
        // Matches the UI: the "Active Lead Assignee" action is only rendered
        // for user_type == 1 (site admin) — see index_new.blade.php.
        if (Auth::guard('admin')->user()->user_type != 1) {
            return back()->with('error', 'You are not authorized to perform this action.');
        }

        $ids = $request->input('lead_assign_status', []);

        Admin::query()->update([
            'lead_assign_status' => 0,
        ]);

        if (!empty($ids)) {
            Admin::whereIn('id', $ids)
                ->update([
                    'lead_assign_status' => 1,
                ]);
        }

        return back()->with('success', 'Lead active assignees updated successfully!');
    }

    public function leadexists(Request $request)
    {
        $request->validate([
            'mobile_no' => 'required|string|max:20',
        ]);

        $checkexists = Lead::where('mob_no', '=', $request->mobile_no)->first();
        if ($checkexists) {

            $checkexists->lead_status_text = "New";
            $checkexists->save();

            return response()->json([
                'response_status' => 'false',
                'response_message' => $request->mobile_no . ' number is already exists in our system',
                'data_lead_id' => $checkexists->id
            ], 200);
        } else {
            return response()->json([
                'response_status' => 'true',
                'response_message' => $request->mobile_no . ' number is not exists in our system'
            ], 200);
        }
    }

    public function leadshow(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        // Public, unauthenticated endpoint — id alone is a guessable
        // sequential primary key and would otherwise let anyone dump any
        // lead's PII. Require the caller's own mobile/WhatsApp number as
        // proof they are the visitor who submitted this lead.
        $mobile = $request->input('mob_no', $request->input('mobile_no'));

        if (blank($mobile)) {
            abort(404);
        }

        $post = Lead::where('id', $request->id)
            ->where(function ($q) use ($mobile) {
                $q->where('mob_no', $mobile)->orWhere('whatsapp_no', $mobile);
            })
            ->first();

        if (!$post) {
            abort(404);
        }

        return response()->json($post);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|integer',
        ]);

        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if (!$this->hasLeadPermission($user, $permission, 'leads_delete')) {
            return redirect()->back()->with('error', 'You are not authorized to delete leads.');
        }

        $post = Lead::find($request->lead_id);

        if (!$this->canAccessLead($post, $user, $permission)) {
            return redirect()->back()->with('error', 'Lead not found or you are not authorized to delete it.');
        }

        $post->delete();

        return redirect()->back()->with('success', 'Lead deleted!');
    }

    public function bulkdelete(Request $request)
    {
        $request->validate([
            'bulklead_id' => 'required|string',
        ]);

        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if (!$this->hasLeadPermission($user, $permission, 'leads_bulk_delete')) {
            return redirect()->back()->with('error', 'You are not authorized to bulk delete leads.');
        }

        $idsv = array_values(array_unique(array_filter(
            array_map('intval', explode(',', $request->bulklead_id))
        )));

        if (empty($idsv)) {
            return redirect()->back()->with('error', 'No leads selected.');
        }

        $query = Lead::whereIn('id', $idsv);

        // Every selected lead is validated server-side: a staff member without
        // leads_view can only bulk-delete leads assigned to them, mirroring
        // jsonData()'s row scoping — ids outside that scope are silently
        // excluded rather than trusted from the request.
        if ($user->user_type != 1 && optional($permission)->leads_view != 1) {
            $query->where('leadassign_id', $user->id);
        }

        $deleted = $query->delete();

        return redirect()->back()->with('success', "{$deleted} lead(s) deleted!");
    }

    public function assignto(Request $request)
    {
        $request->validate([
            'lead_id'       => 'required|integer',
            'leadassign_id' => 'nullable|integer|exists:admins,id',
        ]);

        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if (!$this->hasLeadPermission($user, $permission, 'leads_assignto')) {
            return redirect()->back()->with('error', 'You are not authorized to assign leads.');
        }

        $lead = Lead::find($request->lead_id);

        if (!$this->canAccessLead($lead, $user, $permission)) {
            return redirect()->back()->with('error', 'Lead not found or you are not authorized to assign it.');
        }

        $lead->leadassign_id = $request->leadassign_id;
        $lead->staff_updated_at = now();

        // The "Mark as Reassigned" checkbox is the sole source of truth for
        // is_reassigned — it defaults to checked in the Assign Lead modal, but
        // the admin can uncheck it (e.g. a genuine first-time assignment), and
        // that explicit choice is what gets recorded, not an inferred guess
        // from the lead's prior assignment history.
        $lead->is_reassigned = $request->boolean('mark_as_reassigned');

        $lead->save();

        $autometanotifications = DB::table('autometanotifications')
            ->where('trigger_template_type', 'lead_assign')
            ->where('status', 1)
            ->get();

        foreach ($autometanotifications as $autometanotifications_val) {

            $load = Helper::sendWhatsappAssignTemplate($lead, $autometanotifications_val);
        }

        $sendmail = Helper::sendLeadAssignMail($lead);

        // $autometanotifications1 = DB::table('autometanotifications')
        //     ->where('trigger_template_type', 'add_new_entry_added')
        //     ->where('status', 1)
        //     ->get();

        // foreach ($autometanotifications1 as $autometanotifications_val1) {

        //      $load = Helper::sendWhatsappAssignTemplate($lead, $autometanotifications_val1);
        // }


        return redirect()->back()->with('success', 'Lead Assign successfully!');
    }

    public function qualified(Request $request)
    {

        try {
            // ✅ Validation
            $validated = $request->validate([
                'lead_id'           => 'required|integer|exists:leads,id',
                'is_qualified'      => 'required|in:1,2,3,4,5',
                'qualified_reason'  => 'nullable|string|max:1000',
                'conversation_type' => 'nullable|string|max:50',
                'add_to_pipeline'   => 'nullable|boolean'
            ]);

            \DB::beginTransaction();

            $lead = Lead::lockForUpdate()->find($validated['lead_id']);

            if (!$lead) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Lead not found.'
                ], 404);
            }

            // Same row-level rule show()/jsonData() use: staff without
            // leads_view can only act on leads assigned to them.
            $user = Auth::guard('admin')->user();
            $permission = Adminpermission::where('staff_id', $user->id)->first();

            if (!$this->canAccessLead($lead, $user, $permission)) {
                \DB::rollBack();

                return response()->json([
                    'status'  => false,
                    'message' => 'You are not authorized to update this lead.'
                ], 403);
            }

            $oldQualifiedStatus = $lead->is_qualified;

            // ✅ Update Lead
            $lead->update([
                'is_qualified'     => $validated['is_qualified'],
                'call_not_connected_type' => $request->call_not_connected_type ?? null,
                'qualified_reason' => $validated['qualified_reason'] ?? null,
                'staff_updated_at' => now(),
            ]);

            // ✅ Force updated_at
            $lead->touch();

            // ✅ If Lead Qualified 
            // for temporory stop as per disscuss with sir

            if ((int) $validated['is_qualified'] === 3) {
                if (!empty($validated['add_to_pipeline'])) {
                    $this->transferLeadToPipeline($lead);
                }
            }

            $oldQualifiedStatus = (int) $oldQualifiedStatus;
            $newQualifiedStatus = (int) $validated['is_qualified'];

            // if ($oldQualifiedStatus !== $newQualifiedStatus && in_array($newQualifiedStatus, [3, 4], true)) {
            if ($oldQualifiedStatus !== $newQualifiedStatus) {


                $lead_request_data = json_decode(
                    $lead->lead_request_data,
                    true
                );

                // if (!empty($lead_request_data)) {

                    // if (
                    //     isset($lead_request_data['saudi_license']) &&
                    //     $lead_request_data['saudi_license'] === 'yes' &&
                    //     isset($lead_request_data['country']) &&
                    //     str_contains($lead_request_data['country'], 'Saudi') &&
                    //     isset($lead_request_data['experience']) &&
                    //     str_contains($lead_request_data['experience'], 'SAUDI')
                    // ) {
                    //     $this->metaConversionService->sendLeadStatusEvent($lead,new Request($lead_request_data));
                    // }
                // }
            }

            // ✅ Save Note
            if (
                !empty($validated['qualified_reason']) ||
                !empty($validated['conversation_type'])
            ) {
                                //abid 
                $lead->notes()->create([
                    'notes'             => $validated['qualified_reason'] ?? null,
                    'is_qualified'             => $validated['is_qualified'] ?? null,
                    'conversation_type' => $validated['conversation_type'] ?? null,
                    'call_not_connected_type' => $request->call_not_connected_type ?? null,
                    'admin_id'          => Auth::guard('admin')->id(),
                ]);
            }

            // ✅ Qualified Badge HTML for Row Update
            $badgeHtml = '';

            switch ((int) $lead->is_qualified) {

                case 1:

                    $badgeHtml = '
                        <a href="javascript:void(0);"
                        data-bs-toggle="modal"
                        data-bs-target="#viewLeadModal"
                        data-id="' . $lead->id . '">

                            <span class="badge bg-label-info">
                                Followed Up
                            </span>

                        </a>';
                    break;

                case 2:

                    $badgeHtml = '
                        <a href="javascript:void(0);"
                        data-bs-toggle="modal"
                        data-bs-target="#viewLeadModal"
                        data-id="' . $lead->id . '">

                            <span class="badge bg-label-secondary">
                                Call Not Connected
                            </span>

                        </a>';
                    break;

                case 3:

                    $badgeHtml = '
                        <a href="javascript:void(0);"
                        data-bs-toggle="modal"
                        data-bs-target="#viewLeadModal"
                        data-id="' . $lead->id . '">

                            <span class="badge bg-label-success">
                                Lead Qualified
                            </span>

                        </a>';
                    break;

                case 4:

                    $badgeHtml = '
                        <a href="javascript:void(0);"
                        data-bs-toggle="modal"
                        data-bs-target="#viewLeadModal"
                        data-id="' . $lead->id . '">

                            <span class="badge bg-label-danger">
                                Lead Not Qualified
                            </span>

                        </a>';
                    break;

                 case 5:

                    $badgeHtml = '
                        <a href="javascript:void(0);"
                        data-bs-toggle="modal"
                        data-bs-target="#viewLeadModal"
                        data-id="' . $lead->id . '">

                            <span class="badge bg-label-danger">
                                Lead Not Relevant
                            </span>

                        </a>';
                    break;

                default:

                    $badgeHtml = '
                        <a href="javascript:void(0);"
                        data-bs-toggle="modal"
                        data-bs-target="#viewLeadModal"
                        data-id="' . $lead->id . '">

                            <span class="badge bg-label-warning">
                                Not Yet
                            </span>

                        </a>';
            }

            // add activity
            $this->logQualificationStatusActivity($lead,$oldQualifiedStatus,$validated);

            \DB::commit();

            $lead = Lead::with([
                'notes' => function ($q) {
                    $q->latest();
                },
                'notes.admin'
            ])->findOrFail($validated['lead_id']);

            return response()->json([
                'status'         => true,
                'message'        => 'Lead qualification status updated successfully!',
                'lead_id'        => $lead->id,
                'is_qualified'   => $lead->is_qualified,
                'qualified_html' => $badgeHtml,
                'lead' => $lead
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {

            \DB::rollBack();

            \Log::error('Lead Qualification Error', [
                'error'   => $e->getMessage(),
                'lead_id' => $request->lead_id ?? null
            ]);

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function logQualificationStatusActivity(Lead $lead,?int $oldQualifiedStatus,array $validated): void
    {
        // Single source of truth — see Lead::QUALIFIED_STATUS_LABELS.
        $statuses = Lead::QUALIFIED_STATUS_LABELS;

        Helper::leadActivityLog(
            $lead->id,
            [
                'action'            => 'Qualification Status Changed',
                'old_status'        => $statuses[$oldQualifiedStatus] ?? 'Not Yet',
                'new_status'        => $statuses[$validated['is_qualified']] ?? 'Unknown',
                'reason'            => $validated['qualified_reason'] ?? null,
                'conversation_type' => $validated['conversation_type'] ?? null,
            ],
            Auth::guard('admin')->id() ?? null,
            'qualified_status'
        );
    }

    public function leadActivity(Lead $lead)
    {
        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if (!$this->canAccessLead($lead, $user, $permission)) {
            abort(403, 'You are not authorized to view this lead.');
        }

        $activityLogs = LeadActivityLog::with('admin')
            ->where('lead_id', $lead->id);

        $qualified_status = (clone $activityLogs)
            ->where('module', 'qualified_status')
            ->latest()
            ->get();

        $meta_capi = (clone $activityLogs)
            ->where('module', 'meta_capi')
            ->latest()
            ->get();

        $lead_assign_action = (clone $activityLogs)
            ->where('module', 'lead_assign_action')
            ->latest()
            ->get();

        return view('admin.leads.partials.activity', compact(
            'qualified_status',
            'meta_capi',
            'lead_assign_action'
        ));
    }

    public function transferLeadToPipeline($lead)
    {
        // dd($lead->mob_no);
        $business = Business::where('name', 'LIKE', '%Job seeker%')->first();
        $dealStage = DealStage::where('name', 'Prospecting')->first();
        if (!$dealStage) {
            return response()->json([
                'success' => false,
                'message' => 'Default Deal Stage "Prospecting" not found. Please create it first.'
            ], 500);
        }
        $deal_stage_id = $dealStage->id;

        $add_associate_id = 73;


        // Create a new deal record
        $deal = new DealPipeline();

        $deal->business_id       = $business->id ?? null; // or dynamic if your system supports multiple businesses
        $deal->deal_stage_id     = $deal_stage_id; // default pipeline stage (e.g., "New")
        $deal->associate_id      = $add_associate_id ?? null;
        $deal->job_title_other   = $lead->other_job_title ?? null;
        $deal->candidate         = $lead->cand_name ?? null;
        $deal->company           = $lead->company_name ?? null;
        $deal->contact           = $lead->mob_no ?? null;
        $deal->contact_whatsapp  = $lead->whatsapp_no ?? null;
        $deal->email             = $lead->email ?? null;
        $deal->country           = $lead->country ?? null;
        $deal->notes             = $lead->qualified_reason ?? null;
        $deal->source            = $lead->lead_source ?? null;
        $deal->care_of           = $lead->leadassign_id ?? null;
        $deal->created_by        = auth()->id() ?? 1;
        $deal->created_at        = now();
        $deal->updated_at        = now();

        $deal->save();
    }

    public function getQualifiedStatus($id)
    {
        // $lead = Lead::with('notes')->whereId($id)->first;
        $lead = Lead::with([
            'notes' => function ($q) {
                $q->latest();
            },
            'notes.admin'
        ])->findOrFail($id);

        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if (!$this->canAccessLead($lead, $user, $permission)) {
            abort(403, 'You are not authorized to view this lead.');
        }

        return response()->json([
            'lead' => $lead,
            'is_qualified' => $lead->is_qualified,
            'qualified_reason' => $lead->qualified_reason,
        ]);
    }

    public function updateLeadDetails(Request $request)
    {
        $request->validate([
            'lead_id'         => 'required|integer|exists:leads,id',
            'name'            => 'nullable|string|max:255',
            'job_title'       => 'nullable|string|max:255',
            'job_title_other' => 'nullable|string|max:255',
            'experience'      => 'nullable|string|max:1000',
            'expected_days'   => 'nullable|string|max:100',
            'looking_for'     => 'nullable|string|max:255',
            'driving_licence' => 'nullable',
        ]);

        $lead = Lead::find($request->lead_id);

        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if (!$this->canAccessLead($lead, $user, $permission)) {
            return response()->json([
                'status'  => false,
                'message' => 'You are not authorized to update this lead.',
            ], 403);
        }

        $lead->cand_name = $request->name;
        $lead->required_service = $request->job_title;
        $lead->other_job_title = $request->job_title_other ?? null;
        $lead->experience = $request->experience;
        $lead->expected_days = $request->expected_days;
        $lead->looking_for = $request->looking_for ?? null;

        $drivingLicence = is_array($request->driving_licence) ? $request->driving_licence : [];

        $lead->india_license = in_array('india_license', $drivingLicence) ? 'Yes' : 'No';
        $lead->saudi_license = in_array('saudi_license', $drivingLicence) ? 'Yes' : 'No';

        $lead->staff_updated_at = now();

        $lead->save();

        // Job Title
        $jobTitle = $lead->required_service;

        if ($lead->required_service === 'Other') {
            $jobTitle = $lead->other_job_title ?: '---';
        } else {
            $jobTitle = preg_replace(
                '/\s*\([^)]*\)/',
                '',
                $lead->required_service
            );
        }

        // Experience HTML
        $experienceHtml = '---';

        if (!empty($lead->experience)) {

            $experienceHtml = collect(
                explode(',', $lead->experience)
            )->map(function ($exp) {

                return '<span class="badge rounded-pill bg-label-primary">'
                    . e(trim($exp)) .
                    '</span>';
            })->implode(' ');
        }

        // Driving License HTML
        $licenses = [];

        if (strtolower($lead->saudi_license) == 'yes') {
            $licenses[] =
                '<span class="badge rounded-pill bg-label-primary">
                    Saudi License
                </span>';
        }

        if (strtolower($lead->india_license) == 'yes') {
            $licenses[] =
                '<span class="badge rounded-pill bg-label-primary">
                    Indian License
                </span>';
        }

        $drivingLicenseHtml = count($licenses)
            ? implode(' ', $licenses)
            : '---';

        $lead = Lead::with([
            'notes' => function ($q) {
                $q->latest();
            },
            'notes.admin'
        ])->findOrFail($request->lead_id);

        return response()->json([
            'status' => true,
            'message' => 'Lead updated successfully',

            'lead_id' => $lead->id,

            'name' => e($lead->cand_name ?: '---'),

            'job_title' => e(\Illuminate\Support\Str::limit(
                $jobTitle,
                13,
                '..'
            )),

            'experience_html' => $experienceHtml,

            'expected_days' => e($lead->expected_days ?: '---'),

            'driving_license_html' => $drivingLicenseHtml,
            'lead' => $lead
        ]);
    }

    public function checkMobileDetails(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|min:1|max:50',
        ]);

        $search = "%{$request->mobile}%";

        $lead = Lead::with('leadassign')
            ->where(function ($q) use ($search) {
                $q->where('mob_no', 'LIKE', $search)
                    ->orWhere('whatsapp_no', 'LIKE', $search);
            })
            ->first();

        if (!$lead) {
            return response()->json([
                'status' => false,
                'message' => 'No record found.'
            ]);
        }

        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        // Duplicate detection stays cross-staff (that's the point of this
        // check), but the assignee identity is only revealed to staff who
        // could otherwise see this lead — same row-level rule as jsonData().
        $assignTo = $this->canAccessLead($lead, $user, $permission)
            ? ($lead->leadassign->name ?? 'Unassigned')
            : 'Another agent';

        return response()->json([
            'status' => true,
            'data' => [
                'name'       => e($lead->cand_name ?: '---'),
                'mobile'     => e($request->mobile),
                'assign_to'  => e($assignTo),
                'created_at' => $lead->created_at
                    ? $lead->created_at->format('d-m-Y h:i A')
                    : 'N/A',
            ]
        ]);
    }

    public function updateNumber(Request $request)
    {
        $request->validate([
            'id'           => 'required|integer|exists:leads,id',
            'mob_no'       => 'nullable|string|max:20',
            'whatsapp_no'  => 'nullable|string|max:20',
        ]);

        $lead = Lead::find($request->id);

        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if (!$this->canAccessLead($lead, $user, $permission)) {
            return response()->json([
                'status'  => false,
                'message' => 'You are not authorized to update this lead.',
            ], 403);
        }

        $lead->mob_no = $request->mob_no;
        $lead->whatsapp_no = $request->whatsapp_no;
        $lead->staff_updated_at = now();
        $lead->save();

        return response()->json([
            'status'   => true,
            'message'  => 'Contact details updated successfully',
            'lead_id'  => $lead->id,
            'mobile'   => e($lead->mob_no ?: '---'),
            'whatsapp' => e($lead->whatsapp_no ?: '---'),
        ]);
    }

    public function bulkassignto(Request $request)
    {
        $request->validate([
            'bulklead_id'   => 'required|string',
            'leadassign_id' => 'nullable|integer|exists:admins,id',
        ]);

        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if (!$this->hasLeadPermission($user, $permission, 'leads_bulk_assignto')) {
            return redirect()->back()->with('error', 'You are not authorized to bulk assign leads.');
        }

        $idsv = array_values(array_unique(array_filter(
            array_map('intval', explode(',', $request->bulklead_id))
        )));

        if (empty($idsv)) {
            return redirect()->back()->with('error', 'No leads selected.');
        }

        $query = Lead::whereIn('id', $idsv);

        // Every selected lead is validated server-side: a staff member without
        // leads_view can only bulk-assign leads already assigned to them.
        if ($user->user_type != 1 && optional($permission)->leads_view != 1) {
            $query->where('leadassign_id', $user->id);
        }

        DB::transaction(function () use ($query, $request) {
            $posts = $query->lockForUpdate()->get();

            foreach ($posts as $post) {
                $post->is_reassigned = $request->boolean('mark_as_reassigned');
                $post->leadassign_id = $request->leadassign_id;
                $post->save();
            }
        });

        return redirect()->back()->with('success', 'Bulk Assignto successfully!');
    }

    public function bulkExport(Request $request)
    {
        // Matches the UI: bulk Export is only rendered for user_type == 1 /
        // full_access staff — see index_new.blade.php's bulk action menu.
        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if (!($user->user_type == 1 || optional($permission)->full_access == 1)) {
            abort(403, 'You are not authorized to export leads.');
        }

        $request->validate([
            'lead_ids' => 'required|string',
            'columns'  => 'required|array|min:1',
        ]);

        $leadIds = array_values(array_unique(array_filter(
            array_map('intval', explode(',', $request->lead_ids))
        )));

        // Columns selected from frontend
        $requestedColumns = $request->columns ?? [];

        // Exportable column map
        $exportMap = $this->exportColumnMap();

        // ✅ Keep only valid export keys
        $columns = array_values(array_intersect(
            $requestedColumns,
            array_keys($exportMap)
        ));

        if (empty($columns) || empty($leadIds)) {
            abort(400, 'No valid columns selected');
        }

        // Fetch leads with relation (NO N+1) — only the columns mapLeadRow()
        // actually reads, same reasoning as jsonData()'s trimmed select.
        $leads = Lead::query()
            ->select([
                'id', 'cand_name', 'email', 'company_name', 'mob_no',
                'whatsapp_no', 'required_service', 'experience',
                'saudi_license', 'india_license', 'country', 'expected_days',
                'message', 'created_at', 'leadassign_id', 'is_qualified',
                'lead_status_text',
            ])
            ->with('leadassign:id,name')
            ->whereIn('id', $leadIds)
            ->get();

        $fileName = 'leads_export_' . Str::random(20) . '.csv';

        // Written under storage/, never public/ — a CSV of lead PII must not
        // sit in the public webroot even for the brief window before download.
        $filePath = storage_path('app/' . $fileName);

        $handle = fopen($filePath, 'w');

        // ✅ UTF-8 BOM (Hindi / Arabic safe)
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header row
        fputcsv($handle, $this->getExportHeaders($columns));

        foreach ($leads as $lead) {

            // Get row with ORIGINAL KEYS
            $row = $this->mapLeadRow($lead, $columns);

            // Neutralize CSV/spreadsheet formula injection: lead text fields
            // (name, company, message, ...) originate from the public,
            // unauthenticated lead capture form, so a value starting with
            // =, +, -, @ or a tab/CR could execute as a formula when the
            // export is opened in Excel/Sheets.
            foreach ($row as $key => $value) {
                if (is_string($value) && preg_match('/^[=+\-@\t\r]/', $value)) {
                    $row[$key] = "'" . $value;
                }
            }

            // ✅ Force Excel text for numbers (applied after the formula
            // guard above so this deliberate leading "=" isn't neutralized)
            if (isset($row[4])) { // Mobile
                $row[4] = '="' . $row[4] . '"';
            }

            if (isset($row[5])) { // Whatsapp
                $row[5] = '="' . $row[5] . '"';
            }

            // Write CSV row
            fputcsv($handle, array_values($row));
        }

        fclose($handle);

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ])->deleteFileAfterSend(true);
    }

    public function storeBrowserMeta(Request $request)
    {
        $lead = Lead::findOrFail($request->lead_id);

        $lead->browser_meta_event = $request->browser_meta_event;
        $lead->save();

        return response()->json(['status' => 'stored']);
    }

    public function bulkLeadSendToMeta(Request $request)
    {
        // Matches the UI: "Update To Meta" is only rendered for user_type == 1
        // / full_access staff — see index_new.blade.php's bulk action menu.
        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if (!($user->user_type == 1 || optional($permission)->full_access == 1)) {
            return back()->with('error', 'You are not authorized to perform this action.');
        }

        $request->validate([
            'bulklead_id' => 'required|string',
            'is_bulk_qualified' => 'required',
        ]);

        $leadIds = array_filter(explode(',', $request->bulklead_id));

        if (empty($leadIds)) {
            return back()->with('error', 'No leads selected.');
        }

        $leads = Lead::whereIn('id', $leadIds)->get();

        if ($leads->isEmpty()) {
            return back()->with('error', 'Selected leads not found.');
        }

        // Resending an already-sent lead is only intentional when explicitly
        // requested — otherwise every lead already carrying meta_lead_sent=1 is
        // skipped, mirroring the idempotency check in lead_store_new().
        $forceResend = $request->boolean('force_resend');

        $successCount = 0;
        $failedCount  = 0;
        $skippedCount = 0;

        foreach ($leads as $lead) {

            if ($lead->meta_lead_sent && !$forceResend) {
                $skippedCount++;
                continue;
            }

            $eventId = 'lead_' . round(microtime(true) * 1000) . '_' . random_int(100000, 999999);

            $fakeRequest = new Request([
                'event_id' => $eventId,
                'page'     => 'bulk-send-meta-from-backend',
                'fbp'      => $request->fbp ?? null,
                'fbc'      => $request->fbc ?? null,
            ]);

            $fakeRequest->setMethod('POST');
            $fakeRequest->server->set('REMOTE_ADDR', request()->ip());
            $fakeRequest->headers->set('User-Agent', request()->userAgent());


            try {

                // 🔥 Existing function
                // $this->sendMetaLeadEvent($lead, $fakeRequest);
                $this->metaConversionService->sendLeadStatusEvent($lead, $fakeRequest);


                $successCount++;

                // ✅ DB LOG (SUCCESS)
                MetaLeadLog::create([
                    'lead_id'    => $lead->id,
                    'event_id'   => $eventId,
                    'status'     => 'success',
                    'page'       => $fakeRequest->page,
                    'ip'         => $fakeRequest->ip(),
                    'user_agent' => $fakeRequest->userAgent(),
                    'message'    => 'Lead sent to Meta successfully',
                ]);

                // ✅ FILE LOG
                \Log::channel('facebook_capi')->info('Lead sent to Meta (Bulk)', [
                    'lead_id' => $lead->id,
                    'event_id' => $eventId,
                ]);
            } catch (\Throwable $e) {

                $failedCount++;

                // ❌ DB LOG (FAILED)
                MetaLeadLog::create([
                    'lead_id'    => $lead->id,
                    'event_id'   => $eventId,
                    'status'     => 'failed',
                    'page'       => $fakeRequest->page,
                    'ip'         => $fakeRequest->ip(),
                    'user_agent' => $fakeRequest->userAgent(),
                    'message'    => $e->getMessage(),
                    'trace'      => substr($e->getTraceAsString(), 0, 3000),
                ]);

                // ❌ FILE LOG
                \Log::channel('facebook_capi')->error('Bulk Meta Lead send FAILED', [
                    'lead_id'  => $lead->id,
                    'event_id' => $eventId,
                    'message' => $e->getMessage(),
                ]);

                continue;
            }
        }

        return back()->with(
            'success',
            "{$successCount} leads sent to Meta successfully. {$failedCount} failed."
                . ($skippedCount > 0 ? " {$skippedCount} skipped (already sent — pass force_resend to override)." : '')
        );
    }

    private function getExportHeaders(array $columns): array
    {
        return array_values(
            array_intersect_key(
                $this->exportColumnMap(),
                array_flip($columns)
            )
        );
    }

    private function mapLeadRow($lead, array $columns): array
    {
        $qualifiedMap = [
            null => 'Not yet',
            1    => 'Followed Up',
            2    => 'Call Not Connected',
            3    => 'Lead Qualified',
            4    => 'Lead Not Qualified',
            5 => 'Lead Not Relevant',
        ];

        // Driving license logic
        $licenses = [];
        if ($lead->saudi_license === 'yes') $licenses[] = 'Saudi License';
        if ($lead->india_license === 'yes') $licenses[] = 'Indian License';

        $map = [
            1   => $lead->cand_name ?? '---',
            100 => $lead->email ?? '',
            3   => $lead->company_name ?? '---',
            4   => $lead->mob_no ?? '',
            5   => $lead->whatsapp_no ?? '',
            6   => $lead->required_service ?? '---',
            7   => $lead->experience ?? '---',
            8   => $licenses ? implode(', ', $licenses) : '---',
            9   => $lead->country ?? '---',
            10  => $lead->expected_days ?? '---',
            11  => $lead->message ?? '---',
            12  => optional($lead->created_at)->format('Y-m-d H:i'),
            13  => optional($lead->leadassign)->name ?? '---',
            14  => $qualifiedMap[$lead->is_qualified] ?? 'Not yet',
            15  => $lead->lead_status_text ?: 'New',
        ];

        // ✅ Return ONLY selected columns (keep keys)
        return array_intersect_key($map, array_flip($columns));
    }

    private function exportColumnMap(): array
    {
        return [
            1   => 'Name',
            100 => 'Email',
            3   => 'Company',
            4   => 'Mobile No',
            5   => 'Whatsapp No',
            6   => 'Job Title',
            7   => 'Experience',
            8   => 'Driving License',
            9   => 'Country',
            10  => 'Expected Days',
            11  => 'Message',
            12  => 'Date',
            13  => 'Assign To',
            14  => 'Qualified Status',
            15  => 'Status',
        ];
    }

    public function userleadassignstatus(Request $request)
    {
        $user_id = Auth::guard('admin')->user()->id;

        $post = Admin::find($user_id);
        $post->lead_assign_status = $request->lead_assign_status;
        $post->save();

        return redirect()->back()->with('success', 'Leadassign Status Updated!');

        // $data = [
        //     'resp_message' => 'Assign Status Updated!'
        // ];

        // return response()->json($data);


    }

    public function saveFilter(Request $request)
    {
        $adminId = Auth::guard('admin')->user()->id;

        // Retrieve existing filter or create a new one
        $filter = LeadAdminSaveFilter::firstOrNew(['admin_id' => $adminId]);

        // Convert multi-select inputs to comma-separated strings
        $filter->by_assignee        = $request->by_assignee        ? implode(',', (array)$request->by_assignee) : '';
        $filter->by_is_qualified = $request->by_is_qualified ? implode(',', (array)$request->by_is_qualified) : '';
        $filter->by_call_not_connected_type = $request->by_call_not_connected_type ? implode(',', (array) $request->by_call_not_connected_type) : '';
        $filter->by_driving_license = $request->by_driving_license ? implode(',', (array)$request->by_driving_license) : '';
        $filter->by_job_title       = $request->by_job_title       ? implode(',', (array)$request->by_job_title) : '';
        $filter->by_expected_days   = $request->by_expected_days   ? implode(',', (array)$request->by_expected_days) : '';
        $filter->by_expected_country = $request->by_expected_country ? implode(',', (array)$request->by_expected_country) : '';
        $filter->by_submit_from = $request->by_submit_from ? implode(',', (array)$request->by_submit_from) : '';
        $filter->by_looking_for       = $request->by_looking_for       ? implode(',', (array)$request->by_looking_for) : '';
        $filter->by_lead_date       = $request->by_lead_date       ?? '';
        $filter->by_candidate_updated_date       = $request->by_candidate_updated_date       ?? '';
        $filter->by_location      = $request->by_location       ?? '';
        $filter->staff_updated_at   = $request->staff_updated_at ?? '';
        $filter->by_followup_before   = $request->by_followup_before ?? '';
        $filter->custom_filters = $this->validateCustomFilters($request->input('custom_filters'));

        $filter->save();

        return response()->json([
            'message' => $filter->wasRecentlyCreated ? 'Filter saved successfully!' : 'Filter updated successfully!'
        ]);
    }

    public function resetFilter(Request $request)
    {
        $adminId = Auth::guard('admin')->user()->id;

        $filter = LeadAdminSaveFilter::where('admin_id', $adminId)->first();

        if ($filter) {
            $filter->by_assignee         = null;
            $filter->by_lead_date        = null;
            $filter->by_candidate_updated_date        = null;
            $filter->by_is_qualified     = null;
            $filter->by_call_not_connected_type     = null;
            $filter->by_driving_license  = null;
            $filter->by_job_title        = null;
            $filter->by_expected_days    = null;
            $filter->by_expected_country = null;
            $filter->by_submit_from = null;
            $filter->by_looking_for = null;
            $filter->by_location = null;
            $filter->staff_updated_at = null;
            $filter->by_followup_before = null;
            $filter->custom_filters = null;

            $filter->save();
        }

        return response()->json([
            'message' => 'Filter reset successfully!'
        ]);
    }

    public function LeadAutoAssign()
    {
        $status = LeadAutoAssignStatus::first(); // single row expected

        return view('admin.leads.auto_assign.index', compact('status'));
    }

    public function changeLeadAutoAssignStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:0,1'
        ]);

        $row = LeadAutoAssignStatus::updateOrCreate(
            ['id' => 1],
            ['status' => $request->status]
        );

        return response()->json([
            'success' => true,
            'status'  => $row->status,
            'message' => $row->status == 1
                ? 'Lead Auto Assign Enabled'
                : 'Lead Auto Assign Disabled'
        ]);
    }

    public function addnotes(Request $request)
    {
        try {

            // ✅ Validation
            $validated = $request->validate([
                'lead_id'           => 'required|integer|exists:leads,id',
                'notes'             => 'required|string|max:2000',
                'conversation_type' => 'nullable|string|max:50',
            ]);

            $user = Auth::guard('admin')->user();
            $permission = Adminpermission::where('staff_id', $user->id)->first();

            \DB::beginTransaction();

            // ✅ Get lead
            $lead = Lead::lockForUpdate()->find($validated['lead_id']);

            // Same row-level rule show()/qualified() use: staff without
            // leads_view can only act on leads assigned to them.
            if (!$this->canAccessLead($lead, $user, $permission)) {
                \DB::rollBack();

                return response()->json([
                    'status'  => false,
                    'message' => 'You are not authorized to update this lead.',
                ], 403);
            }

            // ✅ Create note
            $note = $lead->notes()->create([
                'notes'             => $validated['notes'],
                'is_qualified'      => $validated['is_qualified'] ?? null,
                'conversation_type' => $validated['conversation_type'] ?? null,
                'call_not_connected_type' => $request->call_not_connected_type ?? null,
                'admin_id'          => Auth::guard('admin')->id(),
            ]);

            // ✅ Update staff timestamp
            $lead->staff_updated_at = now();
            $lead->save();

            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Note added successfully',
                'note' => [
                    'id' => $note->id,
                    'notes' => $note->notes,
                    'conversation_type' => $note->conversation_type,
                    'created_at' => $note->created_at,
                    'admin_name' => Auth::guard('admin')->user()->name,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {

            \DB::rollBack();

            \Log::error('Add Note Error', [
                'error' => $e->getMessage(),
                'lead_id' => $request->lead_id ?? null
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function deleteNote($id)
    {
        try {

            $note = Leadnote::with('lead')->findOrFail($id);

            $user = Auth::guard('admin')->user();
            $permission = Adminpermission::where('staff_id', $user->id)->first();

            if (!$this->canAccessLead($note->lead, $user, $permission)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'You are not authorized to delete this note.',
                ], 403);
            }

            $leadId = $note->lead_id;

            $note->delete();

            Lead::whereKey($leadId)->update([
                'staff_updated_at' => now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Note deleted successfully'
            ]);
        } catch (\Throwable $e) {

            \Log::error('Delete Note Error', [
                'error' => $e->getMessage(),
                'note_id' => $id
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function bulkChangeStatus(Request $request)
    {
        // Matches the UI: bulk "Change Status" is only rendered for
        // user_type == 1 / full_access staff — see index_new.blade.php.
        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if (!($user->user_type == 1 || optional($permission)->full_access == 1)) {
            return redirect()->back()->with('error', 'You are not authorized to perform this action.');
        }

        $validated = $request->validate([
            'bulklead_id'  => 'required|string',
            'is_qualified' => 'required|in:1,2,3,4',
        ]);

        $leadIds = array_filter(explode(',', $validated['bulklead_id']));

        if (empty($leadIds)) {
            return redirect()->back()->with('error', 'No leads selected.');
        }

        DB::beginTransaction();

        try {

            foreach ($leadIds as $leadId) {

                $lead = Lead::lockForUpdate()->find($leadId);

                if (!$lead) {
                    continue;
                }

                $oldQualifiedStatus = (int) $lead->is_qualified;
                $newQualifiedStatus = (int) $validated['is_qualified'];

                // Skip if status is already the same
                if ($oldQualifiedStatus === $newQualifiedStatus) {
                    continue;
                }

                $lead->update([
                    'is_qualified'     => $newQualifiedStatus,
                    'staff_updated_at' => now(),
                ]);

                $lead->touch();

                // Activity Log
                $this->logQualificationStatusActivity(
                    $lead,
                    $oldQualifiedStatus,
                    $validated
                );
            }

            DB::commit();

            return redirect()->back()->with(
                'success',
                'Lead status updated successfully.'
            );

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Bulk Lead Qualification Error', [
                'error'    => $e->getMessage(),
                'lead_ids' => $leadIds,
            ]);

            return redirect()->back()->with(
                'error',
                $e->getMessage()
            );
        }
    }
}
