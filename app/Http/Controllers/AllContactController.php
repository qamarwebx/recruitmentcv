<?php

namespace App\Http\Controllers;

use App\Jobs\BulkAllContactSendJob;
use App\Jobs\BulkContactSendJob;
use App\Jobs\ProcessCsvImport;
use App\Jobs\SendAllcontactautomessage;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\Allcontact;
use App\Models\Allcontactadminsavefilter;
use App\Models\Allcontactnote;
use App\Models\Allcontactreminder;
use App\Models\Allcontactsendwhatsapp;
use App\Models\Basepathstatus;
use App\Models\City;
use App\Models\Country;
use App\Models\Groupallc;
use App\Models\Groupm;
use App\Models\Importfile;
use App\Models\Industry;
use App\Models\Leadstage;
use App\Models\Lead;
use App\Models\Lifecyclestatus;
use App\Models\Metawhatsappapi;
use App\Models\Metawhatsapptemplate;
use App\Models\Region;
use App\Models\Sendwhatsappresponse;
use App\Models\Todo;
use App\Models\Whatsappapi;
use App\Models\Whatsappcamptemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\GlobalSourceType;
use App\Models\Autometanotification;
use App\Models\ScheduledSendMsgAutomation;
use Carbon\Carbon;
use App\Models\AllcontactFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use DataTables;
use App\Jobs\ExportAllContactHistoryJob;
use App\Models\ExportAllContactHistory;
use App\Services\ContactExportService;

class AllContactController extends Controller
{
    protected ContactExportService $contactExportService;

    public function __construct(ContactExportService $contactExportService)
    {
        $this->contactExportService = $contactExportService;
    }

    public function previewCsvAllcontact(Request $request){
        $basepathstatus = Basepathstatus::first();
        $file = $request->file('csv_file');

        $headers = [];
        $csvData = [];

        if (($handle = fopen($file->getRealPath(),'r')) !== false) {
            // Get Headers

            if (($headerLine = fgetcsv($handle,1000,',')) !== false) {
                $headers = $headerLine;
            }

            // Get Rows
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Only push if row matches count
                if (count($row) == count($headers)) {
                    $csvData[] = $row;
                }
            }
            fclose($handle);
        }

        return response()->json([
            'headers' => $headers,
            'rows' => $csvData
        ]);
    }

    public function uploadCsvAllcontact(Request $request)
    {
        try {
            // dd(count($request->input('csv_data')));
            $mappedFields = $request->input('mapped_fields');
            $csvData = $request->input('csv_data');
            $user_id = Auth::guard('admin')->user()->id;

            // 🔹 Get dropdown values
            $lead_type = $request->input('lead_type');
            $lcs_id = $request->input('lcs_id');
            $ls_id = $request->input('ls_id');
            $careoff_id = $request->input('careoff_id');
            $group_id = $request->input('group_id');
            $source = $request->input('source');

            $insertData = [];
            $duplicateData = [];
            $now = now();


            foreach ($csvData as $row) {
                $data = ['user_id' => $user_id];

                foreach ($mappedFields as $dbField => $csvIndex) {
                    $data[$dbField] = isset($row[$csvIndex]) ? trim($row[$csvIndex]) : null;
                }

                 // 🔹 Include dropdown values if not empty
                if (!empty($lead_type)) {
                    $data['lead_type'] = $lead_type;
                }
                if (!empty($lcs_id) && $lcs_id != 'not_required') {
                    $data['lcs_id'] = $lcs_id;
                }
                if (!empty($ls_id) && $ls_id != 'not_required') {
                    $data['ls_id'] = $ls_id;
                }
                if (!empty($careoff_id) && $careoff_id != 'not_required') {
                    $data['careoff_id'] = $careoff_id;
                }
                if (!empty($group_id) && $group_id != 'not_required') {
                    $data['group_id'] = $group_id;
                }
                if (!empty($source) && $source != 'not_required') {
                    $data['source_id'] = $source;
                }

                // **Handle Country ID Lookup & Creation**
                if (!empty($data['country_id'])) {
                    $country = Country::firstOrCreate(
                        ['name' => $data['country_id']],
                        ['admin_id' => $user_id]
                    );
                    $data['country_id'] = $country->id;
                }

                // **Handle State ID Lookup & Creation**
                if (!empty($data['state_id'])) {
                    $region = Region::firstOrCreate(
                        ['name' => $data['state_id']],
                        ['admin_id' => $user_id]
                    );
                    $data['state_id'] = $region->id;
                }

                // **Handle City ID Lookup & Creation**
                if (!empty($data['city_id'])) {
                    $city = City::firstOrCreate(
                        ['name' => $data['city_id']],
                        ['admin_id' => $user_id]
                    );
                    $data['city_id'] = $city->id;
                }

                $data['created_at'] = $now;
                $data['updated_at'] = $now;

                // **Check for Duplicate Entries**
                $duplicateExists = Allcontact::where(function ($query) use ($data) {
                    if (!empty($data['email'])) {
                        $query->orWhere('email', '=', $data['email']);
                    }
                    if (!empty($data['primary_no_wsp'])) {
                        $query->orWhere('primary_no_wsp', '=', $data['primary_no_wsp']);
                    }
                })->exists();

                if (!$duplicateExists) {
                    $insertData[] = $data;
                } else {
                    $duplicateData[] = $data['email'] ?? ($data['primary_no_wsp'] ?? 'Unknown');
                }
            }

            // **Dispatch Job for Import**
            if (!empty($insertData)) {
                ProcessCsvImport::dispatch($insertData)->onQueue('default');
            }

            return response()->json([
                'status' => true,
                'message' => 'File processed successfully.',
                'imported_count' => count($insertData),
                'duplicate_count' => count($duplicateData),
            ], 200);

        } catch (\Throwable $e) {
            // Log error details for debugging
            \Log::error('CSV Upload Error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return friendly JSON error message
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred during file processing.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updatewrongdata(){
        $dial_code = "966";
        $allcontactPosts = Allcontact::where('secondary_no_wsp_dial_code','=',$dial_code)->where('country_id','=',2)->get();

        foreach ($allcontactPosts as $allcontactPost) {
            $primary_no = strlen($allcontactPost->secondary_no_wsp);

            if ($primary_no == '10') {
                $updPost = Allcontact::find($allcontactPost->id);
                $updPost->secondary_no_wsp_dial_code = "91";
                $updPost->save();
            }

        }

        return redirect()->back()->with('success','Updated!');
    }

    public function index_new(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Static Data Only
        |--------------------------------------------------------------------------
        */

        $metatemplates = Cache::remember(
            'metatemplates',
            30,
            fn() =>
            Metawhatsapptemplate::where(
                'status',
                1
            )->get()
        );

        $normaltemplates = Cache::remember(
            'normaltemplates',
            30,
            fn() =>
            Whatsappcamptemplate::where([
                'status' => 1,
                'audience' => 'Allcontact'
            ])->get()
        );

        $wapis = Cache::remember(
            'wapis',
            30,
            fn() =>
            Whatsappapi::where([
                'api_for'=>'campaign_not',
                'status'=>1
            ])
            ->latest()
            ->get()
        );


        /*
        |--------------------------------------------------------------------------
        | Saved Filter
        |--------------------------------------------------------------------------
        */

        $allcontactsaveadminfilter = Allcontactadminsavefilter::where('admin_id',Auth::guard('admin')->id() )->first();
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->id())->first();

        /*
        |--------------------------------------------------------------------------
        | Business Type / Lead Stage card labels (global lookup lists, not scoped
        | to any filter - the counts for these are filled in via the same AJAX
        | response used for the listing table, see jsonData()).
        |--------------------------------------------------------------------------
        */

        $businessTypes = Allcontact::query()
            ->whereNotNull('lead_type')
            ->where('lead_type', '!=', '')
            ->distinct()
            ->orderBy('lead_type')
            ->pluck('lead_type');

        $leadStages = Leadstage::orderBy('name')->get(['id', 'name']);

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view('admin.allcontact.index_new',
            compact(
                'metatemplates',
                'normaltemplates',
                'wapis',
                'permission',
                'allcontactsaveadminfilter',
                'businessTypes',
                'leadStages'
            )
        );
    }

    /** Whitelisted operator labels for admin-defined All Contact custom filters (must match the UI <option> values verbatim). */
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
     * model's custom_filters column) against a live whitelist of allcontacts
     * columns and the fixed operator list above. Invalid/incomplete rows are
     * silently dropped rather than raising a validation error, so live filtering
     * degrades gracefully mid-edit. This is the only place custom filter
     * column/operator whitelisting happens.
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

        $allowedColumns = Schema::getColumnListing((new Allcontact())->getTable());

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

    public function jsonData(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $isAdmin = $user->user_type == 1;
        $userID = $user->id;

        $permission = Adminpermission::select(
            'staff_id',
            'full_access',
            'allcontact_view',
            'allcontact_edit',
            'allcontact_delete'
        )->where(
            'staff_id',
            $userID
        )->first();

        $posts = Allcontact::query()
            ->with([
                'group:id,name',
                'owner:id,name',
                'careoff:id,name',
                'ls:id,name'
            ])
            ->when(
                !$isAdmin &&
                optional($permission)->allcontact_view == 0,
                fn ($q) => $q->where('careoff_id', $userID)
            )
            ->latest();


        $customFilters = $this->validateCustomFilters($request->input('custom_filters'));

        $hasRequestFilter = collect([
            'business_type',
            'careoff_id',
            'created_by',
            'group_id',
            'lcs_id',
            'ls_id',
            'lead_priority',
            'indust_id',
            'state_id',
            'country_id',
            'city_id',
            'owner_id',
            'conversation_type',
            'country_dial_code',
            'country_dial_code_number',
            'exlude_country_mobile_code',
            'created_date',
            'updated_date',
            'staff_updated_date',
            'followup_before',
            'status',
            'source_id',
            'has_email',
            'has_mobile',
            'recently_added',
        ])->contains(fn ($field) => filled($request->$field)) || filled($customFilters);


        if ($hasRequestFilter) {

            $filterScopes = [
                'country_id'                 => 'FilterCountry',
                'city_id'                    => 'FilterCity',
                'state_id'                    => 'FilterState',
                'lcs_id'                      => 'FilterLcs',
                'ls_id'                       => 'FilterLs',
                'business_type'               => 'FilterBusinesstype',
                'indust_id'                   => 'FilterIndustry',
                'group_id'                    => 'FilterGroup',
                'created_by'                  => 'FilterCreatedBy',
                'lead_priority'               => 'FilterLeadPriority',
                'careoff_id'                  => 'FilterLeadCareoff',
                'country_dial_code'           => 'FilterCountryDialCode',
                'country_dial_code_number'    => 'FilterCountryDialCodeField',
                'exlude_country_mobile_code'  => 'FilterExludeCountryMobileCode',
                'conversation_type'           => 'FilterConversationType',
                'owner_id'                    => 'FilterLeadOwner',
                'followup_before'             => 'FilterFollowupBefore',
                'status'                      => 'FilterStatus',
                'source_id'                   => 'FilterSource',
                'has_email'                   => 'FilterHasEmail',
                'has_mobile'                  => 'FilterHasMobile',
                'recently_added'              => 'FilterRecentlyAdded',
            ];

            foreach ($filterScopes as $requestField => $scope) {

                if (filled($request->$requestField)) {
                    $posts->{$scope}($request->$requestField);
                }
            }

            if (filled($request->created_date)) {
                $posts->FilterDateRange(
                    'created_at',
                    $request->created_date
                );
            }

            if (filled($request->updated_date)) {
                $posts->FilterDateRange(
                    'updated_at',
                    $request->updated_date
                );
            }

            if (filled($request->staff_updated_date)) {
                $posts->FilterDateRange(
                    'staff_updated_date',
                    $request->staff_updated_date
                );
            }

            $posts->FilterCustom($customFilters);

        }
        
        else {

            $allcontactsaveadminfilter = Allcontactadminsavefilter::where(
                'admin_id',
                $userID
            )->first();

            if ($allcontactsaveadminfilter) {

                $getArray = fn ($value) =>
                    filled($value)
                        ? explode(',', $value)
                        : [];

                $country_id = $getArray($allcontactsaveadminfilter->country_id);
                $city_id = $getArray($allcontactsaveadminfilter->city_id);
                $state_id = $getArray($allcontactsaveadminfilter->state_id);
                $lcs_id = $getArray($allcontactsaveadminfilter->lcs_id);
                $ls_id = $getArray($allcontactsaveadminfilter->ls_id);
                $lead_type = $getArray($allcontactsaveadminfilter->lead_type);
                $indust_id = $getArray($allcontactsaveadminfilter->indust_id);
                $group_id = $getArray($allcontactsaveadminfilter->group_id);
                $user_id = $getArray($allcontactsaveadminfilter->user_id);
                $lead_priority = $getArray($allcontactsaveadminfilter->lead_prority);
                $careoff_id = $getArray($allcontactsaveadminfilter->careoff_id);
                $owner_id = $getArray($allcontactsaveadminfilter->owner_id);
                $country_dial_code = $getArray($allcontactsaveadminfilter->country_dial_code);
                $country_dial_code_number = $getArray($allcontactsaveadminfilter->country_dial_code_number);
                $exlude_country_mobile_code = $getArray($allcontactsaveadminfilter->exlude_country_mobile_code);
                $conversation_type = $getArray($allcontactsaveadminfilter->conversation_type);
                $status = $getArray($allcontactsaveadminfilter->status);
                $source_id = $getArray($allcontactsaveadminfilter->source_id);

                if (filled($allcontactsaveadminfilter->followup_before)) {

                    $days = (int) $allcontactsaveadminfilter->followup_before;

                    $today = Carbon::today();

                    $futureDate = $days == 1
                        ? Carbon::today()
                        : Carbon::today()->addDays($days);

                    $posts->whereBetween(
                        'updated_at',
                        [
                            $today->startOfDay(),
                            $futureDate->endOfDay()
                        ]
                    );
                }

                $posts
                    ->FilterCountry($country_id)
                    ->FilterCity($city_id)
                    ->FilterState($state_id)
                    ->FilterLcs($lcs_id)
                    ->FilterLs($ls_id)
                    ->FilterBusinesstype($lead_type)
                    ->FilterIndustry($indust_id)
                    ->FilterGroup($group_id)
                    ->FilterCreatedBy($user_id)
                    ->FilterLeadPriority($lead_priority)
                    ->FilterLeadCareoff($careoff_id)
                    ->FilterCountryDialCode($country_dial_code)
                    ->FilterCountryDialCodeField($country_dial_code_number)
                    ->FilterExludeCountryMobileCode($exlude_country_mobile_code)
                    ->FilterDateRange(
                        'created_at',
                        $allcontactsaveadminfilter->by_created_date
                    )
                    ->FilterDateRange(
                        'updated_at',
                        $allcontactsaveadminfilter->by_updated_date
                    )
                     ->FilterDateRange(
                        'staff_updated_date',
                        $allcontactsaveadminfilter->by_staff_updated_date
                    )
                    ->FilterConversationType($conversation_type)
                    ->FilterLeadOwner($owner_id)
                    ->FilterStatus($status)
                    ->FilterSource($source_id)
                    ->FilterHasEmail($allcontactsaveadminfilter->has_email)
                    ->FilterHasMobile($allcontactsaveadminfilter->has_mobile)
                    ->FilterCustom($this->validateCustomFilters($allcontactsaveadminfilter->custom_filters));
            }
        }

        // Global summary counts: scoped only by visibility permission, never by the
        // active table filters, so the status cards always show fixed/global totals.
        $scopeToVisibility = fn ($q) => $q->when(
            !$isAdmin &&
            optional($permission)->allcontact_view == 0,
            fn ($q) => $q->where('careoff_id', $userID)
        );

        $counts = $scopeToVisibility(Allcontact::query())
            ->selectRaw("
                COUNT(*) as total,
                SUM(lcs_id = 1) as new_contacts,
                SUM(status = 1) as active_contacts,
                SUM(status = 0) as inactive_contacts,
                SUM(email IS NOT NULL AND email != '') as with_email,
                SUM(primary_no_wsp IS NOT NULL AND primary_no_wsp != '') as with_mobile,
                SUM(created_at >= ?) as recently_added
            ", [now()->subDays(7)])
            ->first();

        // Business-type-wise and lead-stage-wise breakdown, same global/unfiltered scope.
        $businessTypeCounts = $scopeToVisibility(Allcontact::query())
            ->whereNotNull('lead_type')
            ->where('lead_type', '!=', '')
            ->selectRaw('lead_type, COUNT(*) as total')
            ->groupBy('lead_type')
            ->pluck('total', 'lead_type');

        $leadStageCounts = $scopeToVisibility(Allcontact::query())
            ->whereNotNull('ls_id')
            ->selectRaw('ls_id, COUNT(*) as total')
            ->groupBy('ls_id')
            ->pluck('total', 'ls_id');

        return DataTables::of($posts)

            ->with([
                'counts' => $counts,
                'business_type_counts' => $businessTypeCounts,
                'lead_stage_counts' => $leadStageCounts,
            ])

        ->filter(function ($query) use ($request) {

            $searchText = data_get(
                $request->all(),
                'search.value'
            );

            if (filled($searchText)) {
                $query->FilterSearchText($searchText);
            }
        })

            ->addColumn('checkbox', function ($row) {
            return '
                <div class="form-check form-check-inline">
                    <input
                        class="form-check-input dt-checkboxes sub-chk"
                        data-id="'.$row->id.'"
                        type="checkbox"
                        value="'.$row->id.'"
                        id="checkbox'.$row->id.'">
                    <label
                        class="form-check-label"
                        for="checkbox'.$row->id.'">
                    </label>
                </div>
            ';
        })

        ->editColumn('name', function ($row) {

            $states = [
                'success',
                'danger',
                'warning',
                'info',
                'dark',
                'primary',
                'secondary'
            ];

            $state = $states[
                $row->id % count($states)
            ];

            $fullName = trim(
                $row->full_name ?? ''
            );

            $avatar = '--';

            if ($fullName) {

                $words = preg_split(
                    '/\s+/',
                    $fullName
                );

                $avatar = strtoupper(
                    mb_substr($words[0], 0, 1) .
                    (count($words) > 1
                        ? mb_substr(end($words), 0, 1)
                        : '')
                );
            }

            return '

            <div class="d-flex justify-content-start align-items-center user-name">

                <div class="avatar-wrapper">

                    <div class="avatar avatar-sm me-3">

                        <span class="avatar-initial rounded-circle bg-label-'.$state.'">

                            '.$avatar.'

                        </span>

                    </div>

                </div>

                <div class="d-flex flex-column">

                    <a
                        href="'.route(
                            'admin.allcontact.show',
                            $row->id
                        ).'"
                        target="_blank"
                        class="text-body text-truncate">

                        <span class="fw-semibold">

                            '.($fullName ?: '---').'

                        </span>

                    </a>

                </div>

            </div>';
        })

        ->addColumn( 'company', fn ($row) => $row->company_name ?: '---' ) 
        
        ->addColumn( 'business', fn ($row) => $row->lead_type ?: '---' )
    
        ->addColumn('stage', function($row){
    
            return '
    
            <a
                href="javascript:void(0);"
                class="text-body contact_id_'.$row->id.'"
                data-bs-target="#editStatus"
                data-id="'.$row->id.'"
                data-bs-toggle="modal">
    
                '.($row->ls->name ?? 'Select').'
    
                <i class="ti ti-chevron-down ti-sm me-2"></i>
    
            </a>';
    
        })
    
        ->addColumn('priority', function ($row) {

            $priorities = [
                'Cold' => 'info',
                'Warm' => 'success',
                'Hot'  => 'warning',
            ];
        
            $title = $row->lead_prority ?: 'Select';
            $badge = $priorities[$row->lead_prority] ?? 'secondary';
        
            return '
            <span
                class="badge rounded-pill bg-label-'.$badge.'
                dropdown-toggle hide-arrow"
                data-bs-toggle="dropdown">
        
                '.$title.'
        
                <i class="ti ti-chevron-down ti-sm"></i>
        
            </span>
        
            <div class="dropdown-menu dropdown-menu-end">
        
                <a href="javascript:void(0)"
                   data-id="'.$row->id.'"
                   class="dropdown-item lead-priority">
                    Cold
                </a>
        
                <a href="javascript:void(0)"
                   data-id="'.$row->id.'"
                   class="dropdown-item lead-priority">
                    Warm
                </a>
        
                <a href="javascript:void(0)"
                   data-id="'.$row->id.'"
                   class="dropdown-item lead-priority">
                    Hot
                </a>
        
            </div>';
        })
        
        ->addColumn('optin', function ($row) {
        
            $title = match ((string) $row->optinout) {
                '1' => 'Opt In',
                '0' => 'Opt Out',
                default => 'Select'
            };
        
            return '
            <a href="javascript:void(0);"
               class="text-body"
               data-bs-toggle="modal"
               data-bs-target="#editOptin"
               data-id="'.$row->id.'">
        
                '.$title.'
        
                <i class="ti ti-chevron-down ti-sm"></i>
        
            </a>';
        })

        ->addColumn('group',fn ($row) => $row->group->name ?? '---')
        
        ->addColumn('careoff', fn ($row) => $row->careoff->name ?? '---')
        
        ->addColumn('actions', function ($row) use ($permission) {
        
            $admin = Auth::guard('admin')->user();
        
            $canFullAccess =
                $admin->user_type == 1 ||
                optional($permission)->full_access == 1;
        
            $canEdit =
                $canFullAccess ||
                optional($permission)->allcontact_edit == 1;
        
            $canDelete =
                $canFullAccess ||
                optional($permission)->allcontact_delete == 1;
        
            $whatsappMenu = collect([
                $row->primary_no_wsp,
                $row->mobile_no1_wsp,
                $row->mobile_no2_wsp,
                $row->mobile_no3_wsp,
            ])
            ->filter()
            ->unique()
            ->map(fn ($number) => '
                <a href="https://wa.me/'.$number.'"
                   target="_blank"
                   class="dropdown-item text-success">
                    '.$number.'
                </a>
            ')
            ->implode('');
        
            $actionMenu = '';
        
            if ($canEdit) {
        
                $actionMenu .= '
                    <a href="#"
                       data-bs-target="#updateReg"
                       data-id="'.$row->id.'"
                       data-bs-toggle="offcanvas"
                       class="dropdown-item allcup">
        
                        <i class="ti ti-edit ti-sm"></i>
        
                        Edit
        
                    </a>';
            }
        
            $actionMenu .= '
                <a href="'.route(
                    'admin.allcontact.show',
                    $row->id
                ).'"
                   target="_blank"
                   class="dropdown-item">
        
                    <i class="ti ti-eye ti-sm"></i>
        
                    View
        
                </a>';
        
            if ($canDelete) {
        
                $actionMenu .= '
                    <a href="#"
                       data-bs-target="#deletescon"
                       data-bs-toggle="modal"
                       data-id="'.$row->id.'"
                       class="dropdown-item allcd">
        
                        <i class="ti ti-trash ti-sm"></i>
        
                        Delete
        
                    </a>';
            }
        
            return '
            <div class="d-flex align-items-center">
        
                <a href="javascript:void(0);"
                   class="text-body dropdown-toggle hide-arrow"
                   data-bs-toggle="dropdown">
        
                    <i class="ti ti-brand-whatsapp ti-sm mx-1"></i>
        
                </a>
        
                <div class="dropdown-menu dropdown-menu-end m-0">
        
                    '.$whatsappMenu.'
        
                </div>
        
                <a href="#"
                   class="text-primary">
        
                    <i class="ti ti-message ti-sm mx-2"></i>
        
                </a>
        
                <a href="javascript:void(0);"
                   class="text-body dropdown-toggle hide-arrow"
                   data-bs-toggle="dropdown">
        
                    <i class="ti ti-dots-vertical ti-sm mx-1"></i>
        
                </a>
        
                <div class="dropdown-menu dropdown-menu-end m-0">
        
                    '.$actionMenu.'
        
                </div>
        
            </div>';
        })
    
        ->rawColumns([
    
            'checkbox',
            'optin',
            'name',
            'stage',
            'priority',
            'actions'
    
        ])
    
        ->make(true);
    } 
    
    public function loadFilter(Request $request)
    {
        $filter = $request->filter;
    
        $data = [];
    
        switch ($filter) {
    
            case 'business_type':
    
                $data = Allcontact::query()
                    ->whereNotNull('lead_type')
                    ->where('lead_type', '!=', '')
                    ->distinct()
                    ->pluck('lead_type');
    
            break;
    
            case 'industry':
    
                $data = Industry::orderBy('name')
                    ->get();
    
            break;
    
            case 'country':
    
                $data = Country::orderBy('name')
                    ->get();
    
            break;
    
            case 'state':
                
                $data = Allcontact::with(['state'])
                ->where('state_id','!=','')
                ->groupBy('state_id')
                ->select('state_id')
                ->get();
                
            break;

            case 'lead_owner':

                $data = Allcontact::query()
                    ->with('owner')
                    ->whereNotNull('owner_id')
                    ->whereHas('owner')
                    ->select('owner_id')
                    ->distinct()
                    ->get();
            
            break;
    
            case 'city':
    
                $data = City::orderBy('name')
                    ->get();
    
            break;
    
            case 'careoff':
    
                $data = Admin::query()
                    ->where('status',1)
                    ->orderBy('name')
                    ->get();
    
            break;
    
            case 'created_by':
    
                $data = DB::table('allcontacts')
                    ->join(
                        'admins',
                        'admins.id',
                        '=',
                        'allcontacts.user_id'
                    )
                    ->where('admins.status',1)
                    ->groupBy(
                        'allcontacts.user_id',
                        'admins.name'
                    )
                    ->select(
                        'allcontacts.user_id as id',
                        'admins.name'
                    )
                    ->get();
    
            break;

            case 'lifecycle':

                $data = Lifecyclestatus::query()
                    ->orderBy('name')
                    ->get();
            
            break;
    
            case 'group':
    
                $data = Groupallc::query()
                    ->orderBy('name')
                    ->get();
    
            break;

            case 'source':

                $data =
                GlobalSourceType::query()
                ->orderBy('name')
                ->get();
            
            break;

            case 'lead_stage':

                $data = Leadstage::query()
                    ->orderBy('name')
                    ->get();
            
            break;
    
            case 'lead_priority':
    
                $data = Allcontact::query()
                    ->whereNotNull('lead_prority')
                    ->where('lead_prority','!=','')
                    ->distinct()
                    ->pluck('lead_prority');
    
            break;
    
            default:
    
                $data = collect();
    
            break;
    
        }
    
        return view(
            'admin.allcontact.filter_options',
            compact(
                'data',
                'filter'
            )
        );
    }

    public function loadAllFilters()
    {
        return Cache::remember(
            'allcontact_filter_data',
            now()->addSeconds(5),
            function () {

                return [

                    'business_type' => Allcontact::query()
                        ->whereNotNull('lead_type')
                        ->where('lead_type', '!=', '')
                        ->distinct()
                        ->pluck('lead_type'),

                    'careoff' => Admin::query()
                        ->where('status', 1)
                        ->orderBy('name')
                        ->get([
                            'id',
                            'name'
                        ]),

                    'created_by' => DB::table('allcontacts')
                        ->join(
                            'admins',
                            'admins.id',
                            '=',
                            'allcontacts.user_id'
                        )
                        ->where('admins.status', 1)
                        ->distinct()
                        ->select(
                            'admins.id',
                            'admins.name'
                        )
                        ->get(),

                  'group' => Groupallc::query()
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->prepend((object) [
                        'id' => 'null',
                        'name' => 'null',
                    ]),

                    'source' => GlobalSourceType::query()
                        ->orderBy('name')
                        ->get([
                            'id',
                            'name'
                        ]),

                    'lifecycle' => Lifecyclestatus::query()
                        ->orderBy('name')
                        ->get([
                            'id',
                            'name'
                        ]),

                    'lead_stage' => Leadstage::query()
                        ->orderBy('name')
                        ->get([
                            'id',
                            'name'
                        ]),

                    'lead_priority' => Allcontact::query()
                        ->whereNotNull('lead_prority')
                        ->where('lead_prority', '!=', '')
                        ->distinct()
                        ->orderBy('lead_prority')
                        ->pluck('lead_prority'),

                    'industry' => Industry::query()
                        ->orderBy('name')
                        ->get([
                            'id',
                            'name'
                        ]),

                   'state' => Allcontact::with('state')
                        ->where('state_id','!=','')
                        ->groupBy('state_id')
                        ->select('state_id')
                        ->get(),

                    'country' => Country::query()
                        ->orderBy('name')
                        ->get([
                            'id',
                            'name'
                        ]),

                    'city' => City::query()
                        ->orderBy('name')
                        ->get([
                            'id',
                            'name'
                        ]),

                    'lead_owner' => Admin::query()
                        ->where('status', 1)
                        ->orderBy('name')
                        ->get([
                            'id',
                            'name'
                        ])

                ];

            }
        );
    }

    private function normalizePhone($phone)
    {
        $codes = [
            '966'=>9,
            '91'=>10,
            '971'=>7,
            '974'=>8,
            '965'=>8
        ];

        $countryMap = Country::pluck(
            'iso_code',
            'country_code'
        );

        $default = [
            'primary_no_wsp'=>$phone,
            'primary_no_wsp_dial_code'=>'91',
            'primary_no_wsp_iso_code'=>'in'
        ];

        if (!$phone) {
            return $default;
        }

        foreach ($codes as $code=>$length) {

            if (
                str_starts_with(
                    $phone,
                    $code.$code
                )
            ) {
                $phone = substr(
                    $phone,
                    strlen($code)
                );
            }

            if (
                str_starts_with(
                    $phone,
                    $code
                )
            ) {

                $number = substr(
                    $phone,
                    strlen($code)
                );

                if (
                    strlen($number)
                    == $length
                ) {

                    return [
                        'primary_no_wsp'=>$number,
                        'primary_no_wsp_dial_code'=>$code,
                        'primary_no_wsp_iso_code'=>
                            $countryMap[$code] ?? null
                    ];

                }

            }

        }

        return $default;
    }

    private function applyFilters($query,$request)
    {
        return $query
            ->FilterCountry($request->country_id)
            ->FilterCity($request->city_id)
            ->FilterState($request->state_id)
            ->FilterLcs($request->lcs_id)
            ->FilterLs($request->ls_id)
            ->FilterBusinesstype($request->businesstype_id)
            ->FilterIndustry($request->industries)
            ->FilterGroup($request->group_id)
            ->FilterCreatedBy($request->created_by)
            ->FilterCountryDialCode($request->country_dial_code)
            ->FilterCountryDialCodeField($request->country_dial_code_number)
            ->FilterExludeCountryMobileCode($request->exlude_country_mobile_code)
            ->FilterLeadPriority($request->lead_priority)
            ->FilterLeadCareoff($request->careoff)
            ->FilterLeadOwner($request->lead_owner)
            ->FilterConversationType($request->conversation_type)
            ->FilterSearchText($request->search_text)
            ->FilterDateRange(
                'created_at',
                $request->created_at
            )
            ->FilterDateRange(
                'updated_at',
                $request->updated_at
            );
    }

    private function applySavedFilters($query, $savedFilter)
    {
        return $query
            ->FilterCountry(
                $savedFilter->country_id ?? null
            )
            ->FilterCity(
                $savedFilter->city_id ?? null
            )
            ->FilterState(
                $savedFilter->state_id ?? null
            )
            ->FilterLcs(
                $savedFilter->lcs_id ?? null
            )
            ->FilterLs(
                $savedFilter->ls_id ?? null
            )
            ->FilterBusinesstype(
                $savedFilter->businesstype_id ?? null
            )
            ->FilterIndustry(
                $savedFilter->industries ?? null
            )
            ->FilterGroup(
                $savedFilter->group_id ?? null
            )
            ->FilterCreatedBy(
                $savedFilter->created_by ?? null
            )
            ->FilterCountryDialCode(
                $savedFilter->country_dial_code ?? null
            )
            ->FilterCountryDialCodeField(
                $savedFilter->country_dial_code_number ?? null
            )
            ->FilterExludeCountryMobileCode(
                $savedFilter->exlude_country_mobile_code ?? null
            )
            ->FilterLeadPriority(
                $savedFilter->lead_priority ?? null
            )
            ->FilterLeadCareoff(
                $savedFilter->careoff ?? null
            )
            ->FilterLeadOwner(
                $savedFilter->lead_owner ?? null
            )
            ->FilterConversationType(
                $savedFilter->conversation_type ?? null
            )
            ->FilterSearchText(
                $savedFilter->search_text ?? null
            )
            ->FilterDateRange(
                'created_at',
                $savedFilter->created_at ?? null
            )
            ->FilterDateRange(
                'updated_at',
                $savedFilter->updated_at ?? null
            );
    }

    public function checkmobileall(Request $request){

        if($request->primary_no_wsp){
            $check_mobile_no = "%".$request->primary_no_wsp."%";
        }elseif($request->secondary_no_wsp){
            $check_mobile_no = "%".$request->secondary_no_wsp."%";
        }elseif ($request->mobile_no1_wsp) {
            $check_mobile_no = "%".$request->mobile_no1_wsp."%";
        }elseif ($request->mobile_no2_wsp) {
            $check_mobile_no = "%".$request->mobile_no2_wsp."%";
        }else{
            $check_mobile_no = "";
        }

        $id = $request->id;

        if ($id) {
            $primary_no_wsp = Allcontact::where('primary_no_wsp',"LIKE",$check_mobile_no)->where('id','!=',$id)->count();
            $secondary_no_wsp = Allcontact::where('secondary_no_wsp',"LIKE",$check_mobile_no)->where('id','!=',$id)->count();
            $mobile_no1_wsp = Allcontact::where('mobile_no1_wsp',"LIKE",$check_mobile_no)->where('id','!=',$id)->count();
            $mobile_no2_wsp = Allcontact::where('mobile_no2_wsp',"LIKE",$check_mobile_no)->where('id','!=',$id)->count();
            $mobile_no3_wsp = Allcontact::where('mobile_no3_wsp',"LIKE",$check_mobile_no)->where('id','!=',$id)->count();
        } else {
            $primary_no_wsp = Allcontact::where('primary_no_wsp',"LIKE",$check_mobile_no)->count();
            $secondary_no_wsp = Allcontact::where('secondary_no_wsp',"LIKE",$check_mobile_no)->count();
            $mobile_no1_wsp = Allcontact::where('mobile_no1_wsp',"LIKE",$check_mobile_no)->count();
            $mobile_no2_wsp = Allcontact::where('mobile_no2_wsp',"LIKE",$check_mobile_no)->count();
            $mobile_no3_wsp = Allcontact::where('mobile_no3_wsp',"LIKE",$check_mobile_no)->count();
        }




        if ($primary_no_wsp == 0 && $secondary_no_wsp == 0 && $mobile_no1_wsp == 0 && $mobile_no2_wsp == 0 && $mobile_no3_wsp == 0) {
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable
        ));

    }

    public function checkshortmobile(Request $request)
    {
        $primary_no_wsp = preg_replace('/\D/', '', $request->primary_no_wsp);

        $search = "%{$primary_no_wsp}%";

        $contact = Allcontact::with(['careoff'])
            ->where(function ($q) use ($search) {
                $q->where('primary_no_wsp', 'LIKE', $search)
                    ->orWhere('secondary_no_wsp', 'LIKE', $search)
                    ->orWhere('mobile_no1_wsp', 'LIKE', $search)
                    ->orWhere('mobile_no2_wsp', 'LIKE', $search)
                    ->orWhere('mobile_no3_wsp', 'LIKE', $search);
            })
            ->first();

        if ($contact) {

            $careoff = $contact->careoff->name ?? "Unknown";

            $message = "Already exists with {$careoff}";

            // ✅ Condition: Only if logged admin is owner
            if (Auth::guard('admin')->id() == $contact->user_id) {
                $message .= ' <a href="javascript:void(0);" class="text-primary" data-bs-target="#editStatus" data-id="'.$contact->id.'" data-bs-toggle="modal">update stage</a>';
            }

            return response()->json([
                'valid'   => false,
                'id' => $contact->id,
                'message' => $message
            ]);
        }

        return response()->json([
            'valid'   => true,
            'id' => null,
            'message' => ""
        ]);
    }

    public function checkMobileDetails(Request $request)
    {
        $search = "%{$request->mobile}%";

        $contact = Allcontact::with('user')
            ->where('primary_no_wsp', 'LIKE', $search)
            ->orWhere('secondary_no_wsp', 'LIKE', $search)
            ->orWhere('mobile_no1_wsp', 'LIKE', $search)
            ->orWhere('mobile_no2_wsp', 'LIKE', $search)
            ->orWhere('mobile_no3_wsp', 'LIKE', $search)
            ->first();

        if (!$contact) {
            return response()->json([
                'status' => false,
                'message' => 'No record found.'
            ]);
        }

        // Find field where match happened
        $fields = [
            'primary_no_wsp',
            'secondary_no_wsp',
            'mobile_no1_wsp',
            'mobile_no2_wsp',
            'mobile_no3_wsp'
        ];

        return response()->json([
            'status' => true,
            'data' => [
                'name'       => $contact->full_name,
                'mobile_in'  => $request->mobile,
                'created_by' => $contact->user->name ?? 'Unknown',
                'created_at' => $contact->created_at 
                                    ? $contact->created_at->format('d-m-Y h:i A') 
                                    : 'N/A',
            ]
        ]);
        
    }

    public function uploadshortstore(Request $request){

        $isModelShow = false;
        if ($request->primary_no_wsp_dial_code != '') {
            $primary_no_wsp_dial_code = $request->primary_no_wsp_dial_code;
        } else {
            $primary_no_wsp_dial_code = "91";
        }

        $full_number = $primary_no_wsp_dial_code.''.$request->primary_no_wsp;

        $fakeRequest = new \Illuminate\Http\Request([
            'primary_no_wsp' => $full_number
        ]);
        
        $response = $this->checkshortmobile($fakeRequest);
        
        $data = $response->getData(); // decode JSON response

        $isModelShow = $data->valid;
       
        
        $post = new Allcontact();
        $post->primary_no_wsp = $primary_no_wsp_dial_code.''.$request->primary_no_wsp;
        $post->full_name = $request->full_name;
        $post->lead_type = $request->lead_type;
        $post->lcs_id = 1;
        $post->ls_id = 1;
        $post->owner_id = Auth::guard('admin')->user()->id;
        $post->user_id = Auth::guard('admin')->user()->id;
        $post->careoff_id = Auth::guard('admin')->user()->id;

        $post->source = $request->source;

        if ($request->send_whatsapp) {
            $post->send_whatsapp = true;
        } else {
            $post->send_whatsapp = false;
        }

        $post->updated_at = null;
        // $post->primary_no_wsp_dial_code = $primary_no_wsp_dial_code;

        $post->save();
        
        $isModelShowId = $post->id;
        // Send Onetime Notification JOb
        if ($post->send_whatsapp == 1) {
            \Log::channel('SendAllcontactautomessage')->info('⏳ check started');
            SendAllcontactautomessage::dispatch($post)->onQueue('default');
        }



        return redirect()->back()->with('success','Contact Updated!')->with('isModelShow', $isModelShow)->with('isModelShowId', $isModelShowId);
    }

    private function getCountryByPhoneNumber($phone_number, $country_codes, $fallback_country_id) {
        foreach ($country_codes as $code => $length) {
            if (strpos($phone_number, $code) === 0) {
                $number_length = strlen($phone_number) - strlen($code);
                $country = Country::where('country_code', $code)->first();
                if ($number_length == $length && !empty($country->iso_code)) {
                    return $country->iso_code;
                }
                break;
            }
        }

        $fallback_country = Country::find($fallback_country_id);
        return $fallback_country->iso_code ?? "";
    }

    public function show($id) {
        $post = Allcontact::find($id);
        $lifecycless = Lifecyclestatus::orderBy('name')->get();
        $leadstages = Leadstage::where('leadcyclestatus_id','=',$post->lcs_id)->orderBy('name')->get();
        $sendcontactmsgs = Allcontactsendwhatsapp::where('allcontact_id','=',$id)->get();
        $metatemplates = Metawhatsapptemplate::where('status','=',1)->get();
        $normaltemplates = Whatsappcamptemplate::where('status','=',1)->where('audience','=','Allcontact')->get();
        $wapis = Whatsappapi::where('api_for','=','campaign_not')->orderBy('id','DESC')->get();
        $contactnotes = Allcontactnote::where('allcontact_id','=',$id)->get();
        $reminders2 = Allcontactreminder::where('allcontact_id','=',$id)->get();
        $industries = Industry::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        $admins = Admin::where('status', 1)->orderBy('name')->get();
        $contactFiles = AllcontactFile::with('uploader')->where('contact_id', $id)->get();

        $groupallcs = Groupallc::orderBy('name')->get();

        // Post Data
        $leadtype = $post->lead_type ? $post->lead_type :'---';
        $fullname = $post->full_name ? $post->full_name :'---';
        $officename = $post->office_name ? $post->office_name :'---';

        $secondary_no_wsp_dial_code = $post->secondary_no_wsp_dial_code ? $post->secondary_no_wsp_dial_code : '';
        $secondary_no_wsp = $post->secondary_no_wsp ? $secondary_no_wsp_dial_code.''.$post->secondary_no_wsp:'---';

        $primary_no_wsp_dial_code = $post->primary_no_wsp_dial_code ? $post->primary_no_wsp_dial_code : '';
        $primary_no_wsp = $post->primary_no_wsp ? $primary_no_wsp_dial_code.''.$post->primary_no_wsp:'---';

        $mobile_no1_wsp_dial_code = $post->mobile_no1_wsp_dial_code ? $post->mobile_no1_wsp_dial_code : '';
        $mobile_no1_wsp = $post->mobile_no1_wsp ? $mobile_no1_wsp_dial_code.''.$post->mobile_no1_wsp:'---';

        $mobile_no2_wsp_dial_code = $post->mobile_no2_wsp_dial_code ? $post->mobile_no2_wsp_dial_code : '';
        $mobile_no2_wsp = $post->mobile_no2_wsp ? $mobile_no2_wsp_dial_code.''.$post->mobile_no2_wsp:'---';

        $mobile_no3_wsp_dial_code = $post->mobile_no3_wsp_dial_code ? $post->mobile_no3_wsp_dial_code : '';
        $mobile_no3_wsp = $post->mobile_no3_wsp ? $mobile_no3_wsp_dial_code.''.$post->mobile_no3_wsp:'---';

        $primary_email = $post->email ? $post->email : '---';
        $email1 = $post->email0 ? $post->email0 : '---';
        $email2 = $post->email1 ? $post->email1 : '---';
        $email3 = $post->email2 ? $post->email2 : '---';

        $city = $post->city_id ? $post->city->name : '---';
        $state = $post->state_id ? $post->state->name : '---';
        $country = $post->country_id ? $post->country->name : '---';
        $industry = $post->indust_id ? $post->indust->name : '---';

        $lifecycle = $post->lcs_id ? $post->lcs->name : '---';
        $leadstage = $post->ls_id ? $post->ls->name : '---';
        $leadpriority = $post->lead_prority ? $post->lead_prority : '---';
        $subscribe = $post->optinout == '1' ? 'Subscribe' : 'Unsubscribe';
        $createBy = $post->user_id ? $post->user->name : '---';
        $leadowner = $post->owner_id ? $post->owner->name : '---';
        $careoff = $post->careoff_id ? $post->careoff->name :'---';
        $deleteby = $post->deleteby_id ? $post->deleteby->name : '---';
        $groupname = $post->group?->name ?? '---';
        $public_status = $post->public_st == 1 ? "Public" : 'Unpublic';
        $allcontact_status = $post->status == 1 ? "Active" : 'Inactive';

        $data = [
            'full_name' => $fullname,
            'lead_type' => $leadtype,
            'office_name' => $officename,
            'secondary_no_wsp' => $secondary_no_wsp,
            'primary_no_wsp' => $primary_no_wsp,
            'mobile_no1_wsp' => $mobile_no1_wsp,
            'mobile_no2_wsp' => $mobile_no2_wsp,
            'mobile_no3_wsp' => $mobile_no3_wsp,
            'primary_email' => $primary_email,
            'email0' => $email1,
            'email1' => $email2,
            'email2' => $email3,
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'industry' => $industry,
            'lifecycle' => $lifecycle,
            'leadstage' => $leadstage,
            'lead_priority' => $leadpriority,
            'subscribe' => $subscribe,
            'createBy' => $createBy,
            'leadowner' => $leadowner,
            'careoff' => $careoff,
            'deleteby' => $deleteby,
            'groupname' => $groupname,
            'public_status' => $public_status,
            'allcontact_status' => $allcontact_status,
            'source' => $post->source ?? '---'
        ];



        // dd($data);



        return view('admin.allcontact.show', compact('post','contactFiles','groupallcs','data','countries','cities','admins','contactnotes','industries','reminders2','lifecycless','leadstages','sendcontactmsgs','metatemplates','normaltemplates','wapis'));
    }

    
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
            'file' => 'required|file|max:2048',
            'contact_id' => 'required'
        ]);
    
        if ($request->hasFile('file')) {
    
            $file = $request->file('file');
    
            // ✅ Folder path (public folder)
            $destinationPath = public_path('uploads/contacts');
    
            // ✅ Create folder if not exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
    
            // ✅ Unique file name
            $fileName = time() . '_' . $file->getClientOriginalName();
    
            // ✅ Move file
            $file->move($destinationPath, $fileName);
    
            // ✅ Save path (relative for URL)
            $path = 'uploads/contacts/' . $fileName;
    
            $fileRecord = AllcontactFile::create([
                'contact_id' => $request->contact_id,
                'uploaded_by' => Auth::guard('admin')->user()->id,
                'file_name' => $request->file_name,
                'file_path' => $path,
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'File uploaded successfully',
                'data' => [
                    'id' => $fileRecord->id,
                    'file_name' => $fileRecord->file_name,
                    'file_path' => asset($fileRecord->file_path),
                    'uploaded_by' => Auth::guard('admin')->user()->name ?? '---',
                    'uploaded_at' => now()->format('Y-m-d h:i A')
                ]
            ]);
        }
    
        return response()->json([
            'status' => false,
            'message' => 'No file uploaded'
        ], 400);
    }

    public function deleteFile($id)
    {
        $file = AllcontactFile::findOrFail($id);

        // ✅ Full path from public folder
        $filePath = public_path($file->file_path);

        // ✅ Delete file if exists
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // ✅ Delete DB record
        $file->delete();

        return response()->json([
            'status' => true,
            'message' => 'File deleted successfully'
        ]);
    }

    public function addreminderedit(Request $request){
        $post = Allcontactreminder::find($request->id);
        return response()->json($post);
    }


    public function edit(Request $request) {
        $post = Allcontact::find($request->id);

        $data_con = [];

        $country_codes = ["966" => 9, "91" => 10, "971" => 7, "974" => 8, "965" => 8];

        $phones = [
            'secondary_no_wsp' => $post->secondary_no_wsp,
            'primary_no_wsp' => $post->primary_no_wsp,
            'mobile_no1_wsp' => $post->mobile_no1_wsp,
            'mobile_no2_wsp' => $post->mobile_no2_wsp,
            'mobile_no3_wsp' => $post->mobile_no3_wsp,
        ];

        $results = [];

        foreach ($phones as $key => $number) {
            $dial_code_field = "{$key}_dial_code";
            if (!empty($post->$dial_code_field)) {
                $country = Country::where('country_code', $post->$dial_code_field)->first();
                $results["{$key}_initial_country"] = $country->iso_code ?? "";
            }else{
                $results["{$key}_initial_country"] = $this->getCountryByPhoneNumber($number, $country_codes, $post->country_id);
            }
        }


        $check_no = [];

        $primary_no_wsp = str_replace(['+', ' '],'',$post->primary_no_wsp);
        $secondary_no_wsp = str_replace(['+', ' '],'',$post->secondary_no_wsp);
        $mobile_no1_wsp = str_replace(['+', ' '],'',$post->mobile_no1_wsp);
        $mobile_no2_wsp = str_replace(['+', ' '],'',$post->mobile_no2_wsp);
        $mobile_no3_wsp = str_replace(['+', ' '],'',$post->mobile_no3_wsp);

        $update_phones = [];

        if ($primary_no_wsp != "") {

            $detected = false;

            foreach ($country_codes as $code => $code_length) {
                // Remove double country code if exists (e.g., 9191..., 966966...)
                if (str_starts_with($primary_no_wsp, $code . $code)) {
                    $primary_no_wsp = substr($primary_no_wsp, strlen($code));
                }

                // Check for single country code
                if (str_starts_with($primary_no_wsp, $code)) {
                    $refined_number = substr($primary_no_wsp, strlen($code));
                    if (strlen($refined_number) == $code_length) {
                        $update_phones['primary_no_wsp'] = $refined_number;
                        $update_phones['primary_no_wsp_dial_code'] = $code;

                        $country = Country::where('country_code', $code)->first();
                        $update_phones['primary_no_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;
                    }
                }

                // Handle already-local numbers (no country code at all)
                if (strlen($primary_no_wsp) == $code_length) {

                    // Heuristic match (e.g., Saudi usually starts with 5)
                    if ($code == '966' && str_starts_with($primary_no_wsp,'5')) {
                        $update_phones['primary_no_wsp'] = $primary_no_wsp;
                        $update_phones['primary_no_wsp_dial_code'] = $code;
                        $country = Country::where('country_code', $code)->first();
                        $update_phones['primary_no_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;

                    }elseif ($code == '91' && preg_match('/^[6-9]/',$primary_no_wsp)) {
                        $update_phones['primary_no_wsp'] = $primary_no_wsp;
                        $update_phones['primary_no_wsp_dial_code'] = $code;
                        $country = Country::where('country_code', $code)->first();
                        $update_phones['primary_no_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;

                    }else{
                        $update_phones["primary_no_wsp"] = $primary_no_wsp;
                        $update_phones['primary_no_wsp_dial_code'] = "91";
                        $update_phones['primary_no_wsp_iso_code'] = "in";

                        $detected = true;
                        break;
                    }


                }
            }

            if (!$detected) {
                $update_phones["primary_no_wsp"] = $primary_no_wsp;
                $update_phones['primary_no_wsp_dial_code'] = "91";
                $update_phones['primary_no_wsp_iso_code'] = "in";
            }

        } else {
            $update_phones["primary_no_wsp"] = $primary_no_wsp;
            $update_phones['primary_no_wsp_dial_code'] = "91";
            $update_phones['primary_no_wsp_iso_code'] = "in";
        }


        if ($secondary_no_wsp != "") {

            $detected = false;

            foreach ($country_codes as $code => $code_length) {
                // Remove double country code if exists (e.g., 9191..., 966966...)
                if (str_starts_with($secondary_no_wsp, $code . $code)) {
                    $secondary_no_wsp = substr($secondary_no_wsp, strlen($code));
                }

                // Check for single country code
                if (str_starts_with($secondary_no_wsp, $code)) {
                    $refined_number = substr($secondary_no_wsp, strlen($code));
                    if (strlen($refined_number) == $code_length) {
                        $update_phones['secondary_no_wsp'] = $refined_number;
                        $update_phones['secondary_no_wsp_dial_code'] = $code;

                        $country = Country::where('country_code', $code)->first();
                        $update_phones['secondary_no_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;
                    }
                }

                // Handle already-local numbers (no country code at all)
                if (strlen($secondary_no_wsp) == $code_length) {

                    // Heuristic match (e.g., Saudi usually starts with 5)
                    if ($code == '966' && str_starts_with($secondary_no_wsp,'5')) {
                        $update_phones['secondary_no_wsp'] = $secondary_no_wsp;
                        $update_phones['secondary_no_wsp_dial_code'] = $code;
                        $country = Country::where('country_code', $code)->first();
                        $update_phones['secondary_no_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;

                    }elseif ($code == '91' && preg_match('/^[6-9]/',$secondary_no_wsp)) {
                        $update_phones['secondary_no_wsp'] = $secondary_no_wsp;
                        $update_phones['secondary_no_wsp_dial_code'] = $code;
                        $country = Country::where('country_code', $code)->first();
                        $update_phones['secondary_no_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;

                    }else{
                        $update_phones["secondary_no_wsp"] = $secondary_no_wsp;
                        $update_phones['secondary_no_wsp_dial_code'] = "91";
                        $update_phones['secondary_no_wsp_iso_code'] = "in";

                        $detected = true;
                        break;
                    }


                }
            }

            if (!$detected) {
                $update_phones["secondary_no_wsp"] = $secondary_no_wsp;
                $update_phones['secondary_no_wsp_dial_code'] = "91";
                $update_phones['secondary_no_wsp_iso_code'] = "in";
            }

        } else {
            $update_phones["secondary_no_wsp"] = $secondary_no_wsp;
            $update_phones['secondary_no_wsp_dial_code'] = "91";
            $update_phones['secondary_no_wsp_iso_code'] = "in";
        }


        if ($mobile_no1_wsp != "") {

            $detected = false;

            foreach ($country_codes as $code => $code_length) {
                // Remove double country code if exists (e.g., 9191..., 966966...)
                if (str_starts_with($mobile_no1_wsp, $code . $code)) {
                    $mobile_no1_wsp = substr($mobile_no1_wsp, strlen($code));
                }

                // Check for single country code
                if (str_starts_with($mobile_no1_wsp, $code)) {
                    $refined_number = substr($mobile_no1_wsp, strlen($code));
                    if (strlen($refined_number) == $code_length) {
                        $update_phones['mobile_no1_wsp'] = $refined_number;
                        $update_phones['mobile_no1_wsp_dial_code'] = $code;

                        $country = Country::where('country_code', $code)->first();
                        $update_phones['mobile_no1_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;
                    }
                }

                // Handle already-local numbers (no country code at all)
                if (strlen($mobile_no1_wsp) == $code_length) {

                    // Heuristic match (e.g., Saudi usually starts with 5)
                    if ($code == '966' && str_starts_with($mobile_no1_wsp,'5')) {
                        $update_phones['mobile_no1_wsp'] = $mobile_no1_wsp;
                        $update_phones['mobile_no1_wsp_dial_code'] = $code;
                        $country = Country::where('country_code', $code)->first();
                        $update_phones['mobile_no1_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;

                    }elseif ($code == '91' && preg_match('/^[6-9]/',$mobile_no1_wsp)) {
                        $update_phones['mobile_no1_wsp'] = $mobile_no1_wsp;
                        $update_phones['mobile_no1_wsp_dial_code'] = $code;
                        $country = Country::where('country_code', $code)->first();
                        $update_phones['mobile_no1_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;

                    }else{
                        $update_phones["mobile_no1_wsp"] = $mobile_no1_wsp;
                        $update_phones['mobile_no1_wsp_dial_code'] = "91";
                        $update_phones['mobile_no1_wsp_iso_code'] = "in";

                        $detected = true;
                        break;
                    }


                }
            }

            if (!$detected) {
                $update_phones["mobile_no1_wsp"] = $mobile_no1_wsp;
                $update_phones['mobile_no1_wsp_dial_code'] = "91";
                $update_phones['mobile_no1_wsp_iso_code'] = "in";
            }

        } else {
            $update_phones["mobile_no1_wsp"] = $mobile_no1_wsp;
            $update_phones['mobile_no1_wsp_dial_code'] = "91";
            $update_phones['mobile_no1_wsp_iso_code'] = "in";
        }

        if ($mobile_no2_wsp != "") {

            $detected = false;

            foreach ($country_codes as $code => $code_length) {
                // Remove double country code if exists (e.g., 9191..., 966966...)
                if (str_starts_with($mobile_no2_wsp, $code . $code)) {
                    $mobile_no2_wsp = substr($mobile_no2_wsp, strlen($code));
                }

                // Check for single country code
                if (str_starts_with($mobile_no2_wsp, $code)) {
                    $refined_number = substr($mobile_no2_wsp, strlen($code));
                    if (strlen($refined_number) == $code_length) {
                        $update_phones['mobile_no2_wsp'] = $refined_number;
                        $update_phones['mobile_no2_wsp_dial_code'] = $code;

                        $country = Country::where('country_code', $code)->first();
                        $update_phones['mobile_no2_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;
                    }
                }

                // Handle already-local numbers (no country code at all)
                if (strlen($mobile_no2_wsp) == $code_length) {

                    // Heuristic match (e.g., Saudi usually starts with 5)
                    if ($code == '966' && str_starts_with($mobile_no2_wsp,'5')) {
                        $update_phones['mobile_no2_wsp'] = $mobile_no2_wsp;
                        $update_phones['mobile_no2_wsp_dial_code'] = $code;
                        $country = Country::where('country_code', $code)->first();
                        $update_phones['mobile_no2_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;

                    }elseif ($code == '91' && preg_match('/^[6-9]/',$mobile_no2_wsp)) {
                        $update_phones['mobile_no2_wsp'] = $mobile_no2_wsp;
                        $update_phones['mobile_no2_wsp_dial_code'] = $code;
                        $country = Country::where('country_code', $code)->first();
                        $update_phones['mobile_no2_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;

                    }else{
                        $update_phones["mobile_no2_wsp"] = $mobile_no2_wsp;
                        $update_phones['mobile_no2_wsp_dial_code'] = "91";
                        $update_phones['mobile_no2_wsp_iso_code'] = "in";

                        $detected = true;
                        break;
                    }


                }
            }

            if (!$detected) {
                $update_phones["mobile_no2_wsp"] = $mobile_no2_wsp;
                $update_phones['mobile_no2_wsp_dial_code'] = "91";
                $update_phones['mobile_no2_wsp_iso_code'] = "in";
            }

        } else {
            $update_phones["mobile_no2_wsp"] = $mobile_no2_wsp;
            $update_phones['mobile_no2_wsp_dial_code'] = "91";
            $update_phones['mobile_no2_wsp_iso_code'] = "in";
        }

        if ($mobile_no3_wsp != "") {

            $detected = false;

            foreach ($country_codes as $code => $code_length) {
                // Remove double country code if exists (e.g., 9191..., 966966...)
                if (str_starts_with($mobile_no3_wsp, $code . $code)) {
                    $mobile_no3_wsp = substr($mobile_no3_wsp, strlen($code));
                }

                // Check for single country code
                if (str_starts_with($mobile_no3_wsp, $code)) {
                    $refined_number = substr($mobile_no3_wsp, strlen($code));
                    if (strlen($refined_number) == $code_length) {
                        $update_phones['mobile_no3_wsp'] = $refined_number;
                        $update_phones['mobile_no3_wsp_dial_code'] = $code;

                        $country = Country::where('country_code', $code)->first();
                        $update_phones['mobile_no3_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;
                    }
                }

                // Handle already-local numbers (no country code at all)
                if (strlen($mobile_no3_wsp) == $code_length) {

                    // Heuristic match (e.g., Saudi usually starts with 5)
                    if ($code == '966' && str_starts_with($mobile_no3_wsp,'5')) {
                        $update_phones['mobile_no3_wsp'] = $mobile_no3_wsp;
                        $update_phones['mobile_no3_wsp_dial_code'] = $code;
                        $country = Country::where('country_code', $code)->first();
                        $update_phones['mobile_no3_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;

                    }elseif ($code == '91' && preg_match('/^[6-9]/',$mobile_no3_wsp)) {
                        $update_phones['mobile_no3_wsp'] = $mobile_no3_wsp;
                        $update_phones['mobile_no3_wsp_dial_code'] = $code;
                        $country = Country::where('country_code', $code)->first();
                        $update_phones['mobile_no3_wsp_iso_code'] = $country->iso_code ?? null;

                        $detected = true;
                        break;

                    }else{
                        $update_phones["mobile_no3_wsp"] = $mobile_no3_wsp;
                        $update_phones['mobile_no3_wsp_dial_code'] = "91";
                        $update_phones['mobile_no3_wsp_iso_code'] = "in";

                        $detected = true;
                        break;
                    }


                }
            }

            if (!$detected) {
                $update_phones["mobile_no3_wsp"] = $mobile_no3_wsp;
                $update_phones['mobile_no3_wsp_dial_code'] = "91";
                $update_phones['mobile_no3_wsp_iso_code'] = "in";
            }

        } else {
            $update_phones["mobile_no3_wsp"] = $mobile_no3_wsp;
            $update_phones['mobile_no3_wsp_dial_code'] = "91";
            $update_phones['mobile_no3_wsp_iso_code'] = "in";
        }


        $data = [
            'post' => $post,
            'result' => $results,
            'check_no' => $check_no,
            'update_phones' => $update_phones
        ];

        // return response()->json($post);
        return response()->json($data);
    }

    public function store(Request $request){

        if ($request->secondary_no_wsp_dial_code != '') {
            $secondary_dial_code = $request->secondary_no_wsp_dial_code;
        } else {
            $secondary_dial_code = "91";
        }

        if ($request->primary_no_wsp_dial_code != '') {
            $primary_no_wsp_dial_code = $request->primary_no_wsp_dial_code;
        } else {
            $primary_no_wsp_dial_code = "91";
        }

        if ($request->mobile_no1_wsp_dial_code != '') {
            $mobile_no1_wsp_dial_code = $request->mobile_no1_wsp_dial_code;
        } else {
            $mobile_no1_wsp_dial_code = "91";
        }

        if ($request->mobile_no2_wsp_dial_code != '') {
            $mobile_no2_wsp_dial_code = $request->mobile_no2_wsp_dial_code;
        } else {
            $mobile_no2_wsp_dial_code = "91";
        }

        if ($request->mobile_no3_wsp_dial_code != '') {
            $mobile_no3_wsp_dial_code = $request->mobile_no3_wsp_dial_code;
        } else {
            $mobile_no3_wsp_dial_code = "91";
        }


        $post = new Allcontact();
        $post->lead_type = $request->lead_type;
        $post->lcs_id = $request->lcs_id;
        $post->ls_id = $request->ls_id;
        $post->full_name = $request->full_name;
        $post->job_title = $request->job_title;
        $post->job_desg = $request->job_desg;
        $post->company_name = $request->company_name;
        $post->indust_id = $request->indust_id;
        $post->office_name = $request->office_name;

        if ($request->secondary_no_wsp != '') {
            $post->secondary_no_wsp = $secondary_dial_code.''.$request->secondary_no_wsp;
        }

        if ($request->primary_no_wsp != '') {
            $post->primary_no_wsp = $primary_no_wsp_dial_code.''.$request->primary_no_wsp;
        }

        if ($request->mobile_no1_wsp != '') {
            $post->mobile_no1_wsp = $mobile_no1_wsp_dial_code.''.$request->mobile_no1_wsp;
        }

        if ($request->mobile_no2_wsp != '') {
            $post->mobile_no2_wsp = $mobile_no2_wsp_dial_code.''.$request->mobile_no2_wsp;
        }

        if ($request->mobile_no3_wsp != '') {
                $post->mobile_no3_wsp = $mobile_no3_wsp_dial_code.''.$request->mobile_no3_wsp;
        }

        if ($request->send_whatsapp) {
            $post->send_whatsapp = true;
        }

        $post->email = $request->email;
        $post->email0 = $request->email0;
        $post->email1 = $request->email1;
        $post->email2 = $request->email2;
        $post->country_id = $request->country_id;
        $post->city_id = $request->city_id;
        $post->careoff_id = $request->careoff_id;
        $post->owner_id = $request->leadowner_id;
        $post->descr = $request->descr;
        $post->user_id = Auth::guard('admin')->user()->id;
        // $post->secondary_no_wsp_dial_code = $primary_dial_code;
        // $post->primary_no_wsp_dial_code = $primary_no_wsp_dial_code;
        // $post->mobile_no1_wsp_dial_code = $mobile_no1_wsp_dial_code;
        // $post->mobile_no2_wsp_dial_code = $mobile_no2_wsp_dial_code;
        // $post->mobile_no3_wsp_dial_code = $mobile_no3_wsp_dial_code;

        $post->updated_at = null;
        $post->source_id = $request->ins_source;
        $post->save();

        // 🔥 Create Scheduled Automation
        $this->createScheduledAutomationForAllcontact($post);

        // Send Onetime Notification JOb
        if ($post->send_whatsapp == 1) {
            SendAllcontactautomessage::dispatch($post)->onQueue('default');
        }

        return redirect()->back()->with('success','Allcontact store!');
    }

    public function createScheduledAutomationForAllcontact($contact){
        try {

            // 1️⃣ Fetch Active Autometa Notifications FOR ALLCONTACT
            $notifications = Autometanotification::where('template_table_name', 'allcontacts')
                ->where('status', 1)
                ->get();

            if ($notifications->isEmpty()) {
                return false;
            }

            foreach ($notifications as $noti) {

                try {

                    // ----------------------------
                    // 2️⃣ Calculate dynamic timing
                    // ----------------------------
                    $minutes = 0;
                    $time = (int) ($noti->trigger_template_time ?? 0);
                    $type = strtolower($noti->trigger_template_time_type ?? '');

                    // CASE 1 → WAIT TYPE
                    if ($noti->trigger_template_type === 'wait') {

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
                                continue 2; // ✅ IMPORTANT FIX
                        }

                        $trigger_template_time = trim($noti->trigger_template_time . ' ' . $type);

                    }
                    // CASE 2 → IMMEDIATE (+1 MIN)
                    elseif($noti->trigger_template_type === 'add_new_entry_added') {

                        $minutes = 1;
                        $trigger_template_time = 'Now';

                        \Log::info("⚡ Trigger type is NOT WAIT → Using +1 minute default", [
                            'notification_id' => $noti->id,
                            'trigger_type'    => $noti->trigger_template_type
                        ]);
                    }

                    // Calculate when to send
                    $finalTime = now()->addMinutes($minutes);

                    // ----------------------------
                    // 3️⃣ Store Scheduled Automation
                    // ----------------------------
                    ScheduledSendMsgAutomation::create([
                        'template_table_name'       => $noti->template_table_name, // allcontacts
                        'template_for'              => $noti->template_for,
                        'autometanotifications_id'  => $noti->id,
                        'metatemp_id'               => $noti->metatemp_id,
                        'send_user_to'              => $contact->id,
                        'trigger_template_time'     => $trigger_template_time,
                        'calculated_time'           => $finalTime,
                        'status'                    => 0,
                    ]);

                    \Log::info('✅ New scheduled task created for ALLCONTACT', [
                        'notification_id' => $noti->id,
                        'contact_id'     => $contact->id,
                        'scheduled_for'  => $finalTime
                    ]);

                } catch (\Exception $ex) {

                    \Log::error('❌ Error while processing notification', [
                        'error' => $ex->getMessage(),
                        'line' => $ex->getLine(),
                        'file' => $ex->getFile(),
                        'notification_id' => $noti->id
                    ]);
                }
            }

            return true;

        } catch (\Exception $e) {

            \Log::error('❌ Fatal Error: createScheduledAutomationForAllcontact()', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return false;
        }
    }

    public function update(Request $request){

        try {

            if ($request->secondary_no_wsp_dial_code != '') {
                $secondary_dial_code = $request->secondary_no_wsp_dial_code;
            } else {
                $secondary_dial_code = "91";
            }

            if ($request->primary_no_wsp_dial_code != '') {
                $primary_no_wsp_dial_code = $request->primary_no_wsp_dial_code;
            } else {
                $primary_no_wsp_dial_code = "91";
            }

            if ($request->mobile_no1_wsp_dial_code != '') {
                $mobile_no1_wsp_dial_code = $request->mobile_no1_wsp_dial_code;
            } else {
                $mobile_no1_wsp_dial_code = "91";
            }

            if ($request->mobile_no2_wsp_dial_code != '') {
                $mobile_no2_wsp_dial_code = $request->mobile_no2_wsp_dial_code;
            } else {
                $mobile_no2_wsp_dial_code = "91";
            }

            if ($request->mobile_no3_wsp_dial_code != '') {
                $mobile_no3_wsp_dial_code = $request->mobile_no3_wsp_dial_code;
            } else {
                $mobile_no3_wsp_dial_code = "91";
            }

            $post = Allcontact::find($request->editID);

            $post->lead_type = $request->lead_type;
            $post->lcs_id = $request->lcs_id;
            $post->ls_id = $request->ls_id;
            $post->full_name = $request->full_name;
            $post->job_title = $request->job_title;
            $post->job_desg = $request->job_desg;
            $post->company_name = $request->company_name;
            $post->indust_id = $request->indust_id;
            $post->office_name = $request->office_name;

            if ($request->secondary_no_wsp != '') {
                $post->secondary_no_wsp = $request->secondary_no_wsp;
            }else{
                $post->secondary_no_wsp = "";
            }

            if ($request->primary_no_wsp != '') {
                $post->primary_no_wsp = $request->primary_no_wsp;
            }else{
                $post->primary_no_wsp = "";
            }

            if ($request->mobile_no1_wsp != '') {
                $post->mobile_no1_wsp = $request->mobile_no1_wsp;
            }else{
                $post->mobile_no1_wsp = "";
            }

            if ($request->mobile_no2_wsp != '') {
                $post->mobile_no2_wsp = $request->mobile_no2_wsp;
            }else{
                $post->mobile_no2_wsp = "";
            }

            if ($request->mobile_no3_wsp != '') {
                    $post->mobile_no3_wsp = $request->mobile_no3_wsp;
            }else{
                    $post->mobile_no3_wsp = "";

            }

            $post->email = $request->email;
            $post->email0 = $request->email0;
            $post->email1 = $request->email1;
            $post->email2 = $request->email2;
            $post->country_id = $request->country_id;
            $post->city_id = $request->city_id;
            $post->careoff_id = $request->careoff_id;
            $post->owner_id = $request->leadowner_id;
            $post->descr = $request->descr;

            if ($request->group_id) {
                $post->group_id = $request->group_id;
            }

            $post->source_id = $request->edit_source;


            // $post->secondary_no_wsp_dial_code = $primary_dial_code;
            // $post->primary_no_wsp_dial_code = $primary_no_wsp_dial_code;
            // $post->mobile_no1_wsp_dial_code = $mobile_no1_wsp_dial_code;
            // $post->mobile_no2_wsp_dial_code = $mobile_no2_wsp_dial_code;
            // $post->mobile_no3_wsp_dial_code = $mobile_no3_wsp_dial_code;

            $post->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Allcontact updated successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Allcontact updated successfully!');


        } catch (\Exception $e) {

            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', $e->getMessage());

        }

    }

    public function getleadstage(Request $request){
        $allcont = Allcontact::find($request->id);
        $posts = Leadstage::where('leadcyclestatus_id','=',$request->lcs_id)->get();

        $res = '';
        $res .= '<option value=""></option>';
        foreach ($posts as $post) {
            $res .= '<option value="'.$post->id.'"';
            if ($post->id == $allcont->ls_id) {
                $res .= 'selected';
            }
            $res .= '>'.$post->name.'</option>';
        }

        $arr['res'] = $res;
        return response()->json($arr);
    }

    public function getleadstageupdt(Request $request){

        $post = Allcontact::find($request->allcontactID);
        $post->lcs_id = $request->lcs_id;
        $post->ls_id = $request->ls_id;
        $post->updated_at = Now();
        
        $post->save();

        $post = new Allcontactnote();
        $post->notes = $request->notes;
        $post->allcontact_id = $request->allcontactID;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->conversation_type = $request->conversation_type;
        $post->save();


        return redirect()->back()->with('success','Lifecycle Status updated');
    }

    

    // public function getleadstageupdt(Request $request){
    //     $post = Allcontact::find($request->allcontactID);
    //     $post->lcs_id = $request->lcs_id;
    //     $post->ls_id = $request->ls_id;
    //     $post->save();

    //     return redirect()->back()->with('success','Lifecycle Status updated');
    // }

    public function updatelifecyclestatus(Request $request){
        $post = Allcontact::find($request->id);
        $post->lcs_id = $request->lcs_id;
        $post->save();

        // return redirect()->back()->with('success','Lifecycle Status updated!');
    }

    public function updateleadstage(Request $request){
        $post = Allcontact::find($request->id);
        $post->ls_id = $request->ls_id;
        $post->save();
    }

    public function leadpriorityupdate(Request $request){
        $post = Allcontact::find($request->id);
        $post->lead_prority = $request->lead_priority;
        $post->save();
    }

    public function allgetoptin(Request $request) {
        $post = Allcontact::find($request->id);

        return response()->json($post);
    }

    public function optinoutupdt(Request $request){
        $post = Allcontact::find($request->id);
        $post->optinout = $request->subscribe;
        $post->save();

        return redirect()->back()->with('success','Subscribe and Unsubscribe updated!');
    }

    public function alcontactsaveadminfilter(Request $request){
        $checkFilter = Allcontactadminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();
        if (isset($checkFilter)) {

            if ($request->businesstype_id != '') {
                $checkFilter->lead_type = implode(",",$request->businesstype_id);
            } else {
                $checkFilter->lead_type = "";
            }

            if ($request->state_id != '') {
                $checkFilter->state_id = implode(",",$request->state_id);
            } else {
                $checkFilter->state_id = "";
            }

            if ($request->city_id != '') {
                $checkFilter->city_id = implode(",",$request->city_id);
            } else {
                $checkFilter->city_id = "";
            }

            if ($request->followup_before != '') {
                $checkFilter->followup_before = $request->followup_before;
            } else {
                $checkFilter->followup_before = "";
            }

            if ($request->by_created_date != '') {
                $checkFilter->by_created_date = $request->by_created_date;
            } else {
                $checkFilter->by_created_date = "";
            }

            if ($request->by_updated_date != '') {
                $checkFilter->by_updated_date = $request->by_updated_date;
            } else {
                $checkFilter->by_updated_date = "";
            }

            if ($request->by_staff_updated_date != '') {
                $checkFilter->by_staff_updated_date = $request->by_staff_updated_date;
            } else {
                $checkFilter->by_staff_updated_date = "";
            }


            if ($request->conversation_type != '') {
                $checkFilter->conversation_type = implode(",",$request->conversation_type);
            } else {
                $checkFilter->conversation_type = "";
            }

            // if ($request->short_form_code == 1) {
            //     $checkFilter->short_form_code = true;
            // } else {
            //     $checkFilter->short_form_code = false;
            // }



            // if ($request->assign_id != '') {
            //     $checkFilter->assign_id = implode(",",$request->lcs_id);
            // } else {
            //     $checkFilter->assign_id = "";
            // }

            if ($request->created_by != '') {
                $checkFilter->user_id = implode(",",$request->created_by);
            } else {
                $checkFilter->user_id = "";
            }

            if ($request->lcs_id != '') {
                $checkFilter->lcs_id = implode(",",$request->lcs_id);
            } else {
                $checkFilter->lcs_id = "";
            }

            if ($request->ls_id != '') {
                $checkFilter->ls_id = implode(",",$request->ls_id);
            } else {
                $checkFilter->ls_id = "";
            }

            if ($request->lead_priority != '') {
                $checkFilter->lead_prority = implode(",",$request->lead_priority);
            } else {
                $checkFilter->lead_prority = "";
            }

            if ($request->lead_owner != '') {
                $checkFilter->owner_id = implode(",",$request->lead_owner);
            } else {
                $checkFilter->owner_id = "";
            }

            if ($request->industry_id != '') {
                $checkFilter->indust_id = implode(",",$request->industry_id);
            } else {
                $checkFilter->indust_id = "";
            }

            if ($request->country_id != '') {
                $checkFilter->country_id = implode(",",$request->country_id);
            } else {
                $checkFilter->country_id = "";
            }
            
            if ($request->group_id != '') {
                $checkFilter->group_id = implode(",",$request->group_id);
            } else {
                $checkFilter->group_id = null;
            }

            if ($request->careoff != '') {
                $checkFilter->careoff_id = implode(",",$request->careoff);
            } else {
                $checkFilter->careoff_id = "";
            }

            if ($request->country_dial_code != '') {
                $checkFilter->country_dial_code = implode(",",$request->country_dial_code);
            } else {
                $checkFilter->country_dial_code = "";
            }

            if ($request->country_dial_code_number != '') {
                $checkFilter->country_dial_code_number = implode(",",$request->country_dial_code_number);
            } else {
                $checkFilter->country_dial_code_number = "";
            }

            if ($request->exlude_country_mobile_code != '') {
                $checkFilter->exlude_country_mobile_code = implode(",",$request->exlude_country_mobile_code);
            } else {
                $checkFilter->exlude_country_mobile_code = "";
            }

            if ($request->status != '') {
                $checkFilter->status = implode(",",$request->status);
            } else {
                $checkFilter->status = "";
            }

            if ($request->source_id != '') {
                $checkFilter->source_id = implode(",",$request->source_id);
            } else {
                $checkFilter->source_id = "";
            }

            $checkFilter->has_email = $request->has_email != '' ? $request->has_email : "";
            $checkFilter->has_mobile = $request->has_mobile != '' ? $request->has_mobile : "";

            $checkFilter->custom_filters = $this->validateCustomFilters($request->input('custom_filters'));

            $checkFilter->save();

            $data = [
                'res' => 'Filter update successfully!'
            ];
        }else{
            $saveFilter = new Allcontactadminsavefilter();
            $saveFilter->admin_id = Auth::guard('admin')->user()->id;


            if ($request->businesstype_id != '') {
                $saveFilter->lead_type = implode(",",$request->businesstype_id);
            } else {
                $saveFilter->lead_type = "";
            }

            if ($request->state_id != '') {
                $saveFilter->state_id = implode(",",$request->state_id);
            } else {
                $saveFilter->state_id = "";
            }

            if ($request->city_id != '') {
                $saveFilter->city_id = implode(",",$request->city_id);
            } else {
                $saveFilter->city_id = "";
            }

            if ($request->followup_before != '') {
                $saveFilter->followup_before = $request->followup_before;
            } else {
                $saveFilter->followup_before = "";
            }

            if ($request->by_created_date != '') {
                $saveFilter->by_created_date = $request->by_created_date;
            } else {
                $saveFilter->by_created_date = "";
            }

            if ($request->conversation_type != '') {
                $saveFilter->conversation_type = implode(",",$request->conversation_type);
            } else {
                $saveFilter->conversation_type = "";
            }

            // if ($request->assign_id != '') {
            //     $saveFilter->assign_id = implode(",",$request->lcs_id);
            // } else {
            //     $saveFilter->assign_id = "";
            // }

            // if ($request->short_form_code == 1) {
            //     $saveFilter->short_form_code = true;
            // } else {
            //     $saveFilter->short_form_code = false;
            // }

            if ($request->created_by != '') {
                $saveFilter->user_id = implode(",",$request->created_by);
            } else {
                $saveFilter->user_id = "";
            }

            if ($request->lcs_id != '') {
                $saveFilter->lcs_id = implode(",",$request->lcs_id);
            } else {
                $saveFilter->lcs_id = "";
            }

            if ($request->ls_id != '') {
                $saveFilter->ls_id = implode(",",$request->ls_id);
            } else {
                $saveFilter->ls_id = "";
            }

            if ($request->lead_priority != '') {
                $saveFilter->lead_prority = implode(",",$request->lead_priority);
            } else {
                $saveFilter->lead_prority = "";
            }

            if ($request->lead_owner != '') {
                $saveFilter->owner_id = implode(",",$request->lead_owner);
            } else {
                $saveFilter->owner_id = "";
            }

            if ($request->industry_id != '') {
                $saveFilter->indust_id = implode(",",$request->industry_id);
            } else {
                $saveFilter->indust_id = "";
            }

            if ($request->country_id != '') {
                $saveFilter->country_id = implode(",",$request->country_id);
            } else {
                $saveFilter->country_id = "";
            }
            if ($request->group_id != '') {
                $saveFilter->group_id = implode(",",$request->group_id);
            } else {
                $saveFilter->group_id = "";
            }

            if ($request->careoff != '') {
                $saveFilter->careoff_id = implode(",",$request->careoff);
            } else {
                $saveFilter->careoff_id = "";
            }

            if ($request->country_dial_code != '') {
                $saveFilter->country_dial_code = implode(",",$request->country_dial_code);
            } else {
                $saveFilter->country_dial_code = "";
            }

            if ($request->country_dial_code_number != '') {
                $saveFilter->country_dial_code_number = implode(",",$request->country_dial_code_number);
            } else {
                $saveFilter->country_dial_code_number = "";
            }


            if ($request->exlude_country_mobile_code != '') {
                $saveFilter->exlude_country_mobile_code = implode(",",$request->exlude_country_mobile_code);
            } else {
                $saveFilter->exlude_country_mobile_code = "";
            }

            if ($request->status != '') {
                $saveFilter->status = implode(",",$request->status);
            } else {
                $saveFilter->status = "";
            }

            if ($request->source_id != '') {
                $saveFilter->source_id = implode(",",$request->source_id);
            } else {
                $saveFilter->source_id = "";
            }

            $saveFilter->has_email = $request->has_email != '' ? $request->has_email : "";
            $saveFilter->has_mobile = $request->has_mobile != '' ? $request->has_mobile : "";

            $saveFilter->custom_filters = $this->validateCustomFilters($request->input('custom_filters'));

            $saveFilter->save();


            $data = [
                'res' => 'Filter save successfully!'
            ];
        }

        return response()->json($data);
    }

    public function saveshortformcode(Request $request){
        $checkFilter = Allcontactadminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();
        if (isset($checkFilter)) {
            $checkFilter->short_form_code = $request->short_form_code;
            $checkFilter->save();

            $data = [
                'res' => 'Short Form update successfully!'
            ];

        }else{
            $saveFilter = new Allcontactadminsavefilter();
            $saveFilter->admin_id = Auth::guard('admin')->user()->id;

            $saveFilter->short_form_code = $request->short_form_code;
            $saveFilter->save();

            $data = [
                'res' => 'Short Form update successfully!'
            ];
        }

        return response()->json($data);
    }
    
    public function alcontactresetadminfilter(Request $request)
    {
        $adminId = Auth::guard('admin')->user()->id;

        $filter = Allcontactadminsavefilter::where('admin_id', $adminId)->first();

        if ($filter) {

            $exclude = [
                'id',
                'admin_id',
                'user_id',
                'short_form_code',
                'created_at',
                'updated_at'
            ];

            $updateData = collect($filter->getAttributes())
                ->except($exclude)
                ->map(fn() => null)
                ->toArray();

            $filter->update($updateData);
        }

        return response()->json([
            'res' => 'Filter reset successfully!'
        ]);
    }


    public function bulkExport(Request $request)
    {
        $contact_ids = !empty($request->contact_ids)
            ? explode(',', $request->contact_ids)
            : [];

        // Columns selected from frontend
        $requestedColumns = $request->columns ?? [];

        // Exportable column map
        $exportMap = $this->contactExportService->exportColumnMap();

        // Keep only valid export keys
        $columns = array_values(array_intersect(
            $requestedColumns,
            array_keys($exportMap)
        ));

        if (empty($columns)) {
           return redirect()->back()->with('error','No valid columns selected.');
        }

        // Create export history
        $history = ExportAllContactHistory::create([
            'admin_id'         => auth()->id(),
            'columns'          => $columns,
            'contact_ids'      => implode(',', $contact_ids),
            'is_all_export'    => $request->boolean('is_all_export'),
            'status'           => 'pending',
            'total_records'    => 0,
            'exported_records' => 0,
        ]);

        // Dispatch background job
        ExportAllContactHistoryJob::dispatch($history->id,Auth::guard('admin')->user()->id);

        return redirect('admin/contact-export-history')->with('success','Export has been queued successfully. You can continue using the system while the export is generated.');

    }

    public function bulkupdatecountrycodefield(Request $request){
        $ids = explode(",",$request->contactIDGRPTRC25);
        $posts = Allcontact::whereIn('id', $ids)->get();

        // Supported country codes and expected number lengths
        $country_codes = [
            "91" => 10, // India (Usually start with 6 - 9)
            "966" => 9, // Saudi Arabia (Usually start with 5)
            "974" => 8, // Qatar (Usually start with 3,5,6,or 7)
            "971" => 9, // UAE (Usually start with 5)
            "968" => 8, // Oman (Usuallt start with 7 or 9)
            "965" => 8, // Kuwait (Usually start with 5,6, or 9)
        ];

        foreach ($posts as $post) {
            $updateContact = Allcontact::find($post->id);

            // Get Numbers and Dialcode

            $primary_no_wsp = str_replace(['+', ' '],'',$post->primary_no_wsp);
            $secondary_no_wsp = str_replace(['+', ' '],'',$post->secondary_no_wsp);
            $mobile_no1_wsp = str_replace(['+', ' '],'',$post->mobile_no1_wsp);
            $mobile_no2_wsp = str_replace(['+', ' '],'',$post->mobile_no2_wsp);
            $mobile_no3_wsp = str_replace(['+', ' '],'',$post->mobile_no3_wsp);

            $secondary_no_wsp_dial_code = $post->secondary_no_wsp_dial_code;
            $primary_no_wsp_dial_code = $post->primary_no_wsp_dial_code;
            $mobile_no1_wsp_dial_code = $post->mobile_no1_wsp_dial_code;
            $mobile_no2_wsp_dial_code = $post->mobile_no2_wsp_dial_code;
            $mobile_no3_wsp_dial_code = $post->mobile_no3_wsp_dial_code;

            // Country Code
            $country = Country::find($post->country_id);

            if ($country) {
                $country_dial_code = $country->country_code;
            } else {
                $country_dial_code = "";
            }

            if ($primary_no_wsp != '') {
                if ($primary_no_wsp_dial_code != '') {
                    if (strpos($primary_no_wsp,$primary_no_wsp_dial_code) === 0) {
                        // Update Allcontact
                        $updateContact->primary_no_wsp = $primary_no_wsp;
                        $updateContact->primary_no_wsp_dial_code = "";
                    }else{
                        if (strlen($primary_no_wsp) == $country_codes[$primary_no_wsp_dial_code]) {
                            $updateContact->primary_no_wsp = $primary_no_wsp_dial_code.''.$primary_no_wsp;
                            $updateContact->primary_no_wsp_dial_code = "";
                        }else{
                            $updateContact->primary_no_wsp = $primary_no_wsp;
                            $updateContact->primary_no_wsp_dial_code = "";
                        }
                    }
                }elseif ($country_dial_code != '') {
                    if (strpos($primary_no_wsp, $country_dial_code) === 0) {
                        $updateContact->primary_no_wsp = $primary_no_wsp;
                        $updateContact->primary_no_wsp_dial_code = "";
                    }else{
                        if (strlen($primary_no_wsp) == $country_codes[$country_dial_code]) {
                            $updateContact->primary_no_wsp = $country_dial_code.''.$primary_no_wsp;
                            $updateContact->primary_no_wsp_dial_code = "";
                        }else{
                            $updateContact->primary_no_wsp = $primary_no_wsp;
                            $updateContact->primary_no_wsp_dial_code = "";
                        }
                    }
                }else{
                    // Update Allcontact
                    $updateContact->primary_no_wsp = $primary_no_wsp;
                    $updateContact->primary_no_wsp_dial_code = "";
                }
            } else {
                // Update Allcontact
                $updateContact->primary_no_wsp = $primary_no_wsp;
                $updateContact->primary_no_wsp_dial_code = "";
            }

            if ($secondary_no_wsp != '') {
                if ($secondary_no_wsp_dial_code != '') {
                    if (strpos($secondary_no_wsp,$secondary_no_wsp_dial_code) === 0) {
                        // Update Allcontact
                        $updateContact->secondary_no_wsp = $secondary_no_wsp;
                        $updateContact->secondary_no_wsp_dial_code = "";
                    }else{
                        if (strlen($secondary_no_wsp) == $country_codes[$secondary_no_wsp_dial_code]) {
                            $updateContact->secondary_no_wsp = $secondary_no_wsp_dial_code.''.$secondary_no_wsp;
                            $updateContact->secondary_no_wsp_dial_code = "";
                        }else{
                            $updateContact->secondary_no_wsp = $secondary_no_wsp;
                            $updateContact->secondary_no_wsp_dial_code = "";
                        }
                    }
                }elseif ($country_dial_code != '') {
                    if (strpos($secondary_no_wsp, $country_dial_code) === 0) {
                        $updateContact->secondary_no_wsp = $secondary_no_wsp;
                        $updateContact->secondary_no_wsp_dial_code = "";
                    }else{
                        if (strlen($secondary_no_wsp) == $country_codes[$country_dial_code]) {
                            $updateContact->secondary_no_wsp = $country_dial_code.''.$secondary_no_wsp;
                            $updateContact->secondary_no_wsp_dial_code = "";
                        }else{
                            $updateContact->secondary_no_wsp = $secondary_no_wsp;
                            $updateContact->secondary_no_wsp_dial_code = "";
                        }
                    }
                }else{
                    // Update Allcontact
                    $updateContact->secondary_no_wsp = $secondary_no_wsp;
                    $updateContact->secondary_no_wsp_dial_code = "";
                }
            } else {
                // Update Allcontact
                $updateContact->secondary_no_wsp = $secondary_no_wsp;
                $updateContact->secondary_no_wsp_dial_code = "";
            }

            if ($mobile_no1_wsp != '') {
                if ($mobile_no1_wsp_dial_code != '') {
                    if (strpos($mobile_no1_wsp,$mobile_no1_wsp_dial_code) === 0) {
                        // Update Allcontact
                        $updateContact->mobile_no1_wsp = $mobile_no1_wsp;
                        $updateContact->mobile_no1_wsp_dial_code = "";
                    }else{
                        if (strlen($mobile_no1_wsp) == $country_codes[$mobile_no1_wsp_dial_code]) {
                            $updateContact->mobile_no1_wsp = $mobile_no1_wsp_dial_code.''.$mobile_no1_wsp;
                            $updateContact->mobile_no1_wsp_dial_code = "";
                        }else{
                            $updateContact->mobile_no1_wsp = $mobile_no1_wsp;
                            $updateContact->mobile_no1_wsp_dial_code = "";
                        }
                    }
                }elseif ($country_dial_code != '') {
                    if (strpos($mobile_no1_wsp, $country_dial_code) === 0) {
                        $updateContact->mobile_no1_wsp = $mobile_no1_wsp;
                        $updateContact->mobile_no1_wsp_dial_code = "";
                    }else{
                        if (strlen($mobile_no1_wsp) == $country_codes[$country_dial_code]) {
                            $updateContact->mobile_no1_wsp = $country_dial_code.''.$mobile_no1_wsp;
                            $updateContact->mobile_no1_wsp_dial_code = "";
                        }else{
                            $updateContact->mobile_no1_wsp = $mobile_no1_wsp;
                            $updateContact->mobile_no1_wsp_dial_code = "";
                        }
                    }
                }else{
                    // Update Allcontact
                    $updateContact->mobile_no1_wsp = $mobile_no1_wsp;
                    $updateContact->mobile_no1_wsp_dial_code = "";
                }
            } else {
                // Update Allcontact
                $updateContact->mobile_no1_wsp = $mobile_no1_wsp;
                $updateContact->mobile_no1_wsp_dial_code = "";
            }

            if ($mobile_no2_wsp != '') {
                if ($mobile_no2_wsp_dial_code != '') {
                    if (strpos($mobile_no2_wsp,$mobile_no2_wsp_dial_code) === 0) {
                        // Update Allcontact
                        $updateContact->mobile_no2_wsp = $mobile_no2_wsp;
                        $updateContact->mobile_no2_wsp_dial_code = "";
                    }else{
                        if (strlen($mobile_no2_wsp) == $country_codes[$mobile_no2_wsp_dial_code]) {
                            $updateContact->mobile_no2_wsp = $mobile_no2_wsp_dial_code.''.$mobile_no2_wsp;
                            $updateContact->mobile_no2_wsp_dial_code = "";
                        }else{
                            $updateContact->mobile_no2_wsp = $mobile_no2_wsp;
                            $updateContact->mobile_no2_wsp_dial_code = "";
                        }
                    }
                }elseif ($country_dial_code != '') {
                    if (strpos($mobile_no2_wsp, $country_dial_code) === 0) {
                        $updateContact->mobile_no2_wsp = $mobile_no2_wsp;
                        $updateContact->mobile_no2_wsp_dial_code = "";
                    }else{
                        if (strlen($mobile_no2_wsp) == $country_codes[$country_dial_code]) {
                            $updateContact->mobile_no2_wsp = $country_dial_code.''.$mobile_no2_wsp;
                            $updateContact->mobile_no2_wsp_dial_code = "";
                        }else{
                            $updateContact->mobile_no2_wsp = $mobile_no2_wsp;
                            $updateContact->mobile_no2_wsp_dial_code = "";
                        }
                    }
                }else{
                    // Update Allcontact
                    $updateContact->mobile_no2_wsp = $mobile_no2_wsp;
                    $updateContact->mobile_no2_wsp_dial_code = "";
                }
            } else {
                // Update Allcontact
                $updateContact->mobile_no2_wsp = $mobile_no2_wsp;
                $updateContact->mobile_no2_wsp_dial_code = "";
            }

            if ($mobile_no3_wsp != '') {
                if ($mobile_no3_wsp_dial_code != '') {
                    if (strpos($mobile_no3_wsp,$mobile_no3_wsp_dial_code) === 0) {
                        // Update Allcontact
                        $updateContact->mobile_no3_wsp = $mobile_no3_wsp;
                        $updateContact->mobile_no3_wsp_dial_code = "";
                    }else{
                        if (strlen($mobile_no3_wsp) == $country_codes[$mobile_no3_wsp_dial_code]) {
                            $updateContact->mobile_no3_wsp = $mobile_no3_wsp_dial_code.''.$mobile_no3_wsp;
                            $updateContact->mobile_no3_wsp_dial_code = "";
                        }else{
                            $updateContact->mobile_no3_wsp = $mobile_no3_wsp;
                            $updateContact->mobile_no3_wsp_dial_code = "";
                        }
                    }
                }elseif ($country_dial_code != '') {
                    if (strpos($mobile_no3_wsp, $country_dial_code) === 0) {
                        $updateContact->mobile_no3_wsp = $mobile_no3_wsp;
                        $updateContact->mobile_no3_wsp_dial_code = "";
                    }else{
                        if (strlen($mobile_no3_wsp) == $country_codes[$country_dial_code]) {
                            $updateContact->mobile_no3_wsp = $country_dial_code.''.$mobile_no3_wsp;
                            $updateContact->mobile_no3_wsp_dial_code = "";
                        }else{
                            $updateContact->mobile_no3_wsp = $mobile_no3_wsp;
                            $updateContact->mobile_no3_wsp_dial_code = "";
                        }
                    }
                }else{
                    // Update Allcontact
                    $updateContact->mobile_no3_wsp = $mobile_no3_wsp;
                    $updateContact->mobile_no3_wsp_dial_code = "";
                }
            } else {
                // Update Allcontact
                $updateContact->mobile_no3_wsp = $mobile_no3_wsp;
                $updateContact->mobile_no3_wsp_dial_code = "";
            }


            $updateContact->save();

        }

        return redirect()->back()->with('success','Country Code updated in Field!');

    }

    public function bulkupdatecountrycodefield_old(Request $request) {
        $ids = explode(",",$request->contactIDGRPTRC25);
        $posts = Allcontact::whereIn('id', $ids)->get();

        // Supported country codes and expected number lengths
        $country_codes = [
            "91" => 10, // India (Usually start with 6 - 9)
            "966" => 9, // Saudi Arabia (Usually start with 5)
            "974" => 8, // Qatar (Usually start with 3,5,6,or 7)
            "971" => 9, // UAE (Usually start with 5)
            "968" => 8, // Oman (Usuallt start with 7 or 9)
            "965" => 8, // Kuwait (Usually start with 5,6, or 9)
        ];

        foreach ($posts as $post) {
            $updateContact = Allcontact::find($post->id);

            // Get Numbers and Dialcode

            $primary_no_wsp = str_replace(['+', ' '],'',$post->primary_no_wsp);
            $secondary_no_wsp = str_replace(['+', ' '],'',$post->secondary_no_wsp);
            $mobile_no1_wsp = str_replace(['+', ' '],'',$post->mobile_no1_wsp);
            $mobile_no2_wsp = str_replace(['+', ' '],'',$post->mobile_no2_wsp);
            $mobile_no3_wsp = str_replace(['+', ' '],'',$post->mobile_no3_wsp);

            $secondary_no_wsp_dial_code = $post->secondary_no_wsp_dial_code;
            $primary_no_wsp_dial_code = $post->primary_no_wsp_dial_code;
            $mobile_no1_wsp_dial_code = $post->mobile_no1_wsp_dial_code;
            $mobile_no2_wsp_dial_code = $post->mobile_no2_wsp_dial_code;
            $mobile_no3_wsp_dial_code = $post->mobile_no3_wsp_dial_code;

            // Country Code
            $country = Country::find($post->country_id);

            if ($country) {
                $country_dial_code = $country->country_code;
            } else {
                $country_dial_code = "";
            }

            if ($primary_no_wsp != '') {
                if ($primary_no_wsp_dial_code != '') {
                    if (strpos($primary_no_wsp,$primary_no_wsp_dial_code) === 0) {
                        // Update Allcontact
                        $updateContact->primary_no_wsp = $primary_no_wsp;
                    }else{
                        if (strlen($primary_no_wsp) == $country_codes[$primary_no_wsp_dial_code]) {
                            $updateContact->primary_no_wsp = $primary_no_wsp_dial_code.''.$primary_no_wsp;
                        }else{
                            $updateContact->primary_no_wsp = $primary_no_wsp;
                        }
                    }
                }elseif ($country_dial_code != '') {
                    if (strpos($primary_no_wsp, $country_dial_code) === 0) {
                        $updateContact->primary_no_wsp = $primary_no_wsp;
                    }else{
                        if (strlen($primary_no_wsp) == $country_codes[$country_dial_code]) {
                            $updateContact->primary_no_wsp = $country_dial_code.''.$primary_no_wsp;
                            $updateContact->primary_no_wsp_dial_code = $country_dial_code;
                        }else{
                            $updateContact->primary_no_wsp = $primary_no_wsp;
                        }
                    }
                }else{
                    // Update Allcontact
                    $updateContact->primary_no_wsp = $primary_no_wsp;
                }
            } else {
                // Update Allcontact
                $updateContact->primary_no_wsp = $primary_no_wsp;
            }

            if ($secondary_no_wsp != '') {
                if ($secondary_no_wsp_dial_code != '') {
                    if (strpos($secondary_no_wsp,$secondary_no_wsp_dial_code) === 0) {
                        // Update Allcontact
                        $updateContact->secondary_no_wsp = $secondary_no_wsp;
                    }else{
                        if (strlen($secondary_no_wsp) == $country_codes[$secondary_no_wsp_dial_code]) {
                            $updateContact->secondary_no_wsp = $secondary_no_wsp_dial_code.''.$secondary_no_wsp;
                        }else{
                            $updateContact->secondary_no_wsp = $secondary_no_wsp;
                        }
                    }
                }elseif ($country_dial_code != '') {
                    if (strpos($secondary_no_wsp, $country_dial_code) === 0) {
                        $updateContact->secondary_no_wsp = $secondary_no_wsp;
                    }else{
                        if (strlen($secondary_no_wsp) == $country_codes[$country_dial_code]) {
                            $updateContact->secondary_no_wsp = $country_dial_code.''.$secondary_no_wsp;
                            $updateContact->secondary_no_wsp_dial_code = $country_dial_code;
                        }else{
                            $updateContact->secondary_no_wsp = $secondary_no_wsp;
                        }
                    }
                }else{
                    // Update Allcontact
                    $updateContact->secondary_no_wsp = $secondary_no_wsp;
                }
            } else {
                // Update Allcontact
                $updateContact->secondary_no_wsp = $secondary_no_wsp;
            }

            if ($mobile_no1_wsp != '') {
                if ($mobile_no1_wsp_dial_code != '') {
                    if (strpos($mobile_no1_wsp,$mobile_no1_wsp_dial_code) === 0) {
                        // Update Allcontact
                        $updateContact->mobile_no1_wsp = $mobile_no1_wsp;
                    }else{
                        if (strlen($mobile_no1_wsp) == $country_codes[$mobile_no1_wsp_dial_code]) {
                            $updateContact->mobile_no1_wsp = $mobile_no1_wsp_dial_code.''.$mobile_no1_wsp;
                        }else{
                            $updateContact->mobile_no1_wsp = $mobile_no1_wsp;
                        }
                    }
                }elseif ($country_dial_code != '') {
                    if (strpos($mobile_no1_wsp, $country_dial_code) === 0) {
                        $updateContact->mobile_no1_wsp = $mobile_no1_wsp;
                    }else{
                        if (strlen($mobile_no1_wsp) == $country_codes[$country_dial_code]) {
                            $updateContact->mobile_no1_wsp = $country_dial_code.''.$mobile_no1_wsp;
                            $updateContact->mobile_no1_wsp_dial_code = $country_dial_code;
                        }else{
                            $updateContact->mobile_no1_wsp = $mobile_no1_wsp;
                        }
                    }
                }else{
                    // Update Allcontact
                    $updateContact->mobile_no1_wsp = $mobile_no1_wsp;
                }
            } else {
                // Update Allcontact
                $updateContact->mobile_no1_wsp = $mobile_no1_wsp;
            }

            if ($mobile_no2_wsp != '') {
                if ($mobile_no2_wsp_dial_code != '') {
                    if (strpos($mobile_no2_wsp,$mobile_no2_wsp_dial_code) === 0) {
                        // Update Allcontact
                        $updateContact->mobile_no2_wsp = $mobile_no2_wsp;
                    }else{
                        if (strlen($mobile_no2_wsp) == $country_codes[$mobile_no2_wsp_dial_code]) {
                            $updateContact->mobile_no2_wsp = $mobile_no2_wsp_dial_code.''.$mobile_no2_wsp;
                        }else{
                            $updateContact->mobile_no2_wsp = $mobile_no2_wsp;
                        }
                    }
                }elseif ($country_dial_code != '') {
                    if (strpos($mobile_no2_wsp, $country_dial_code) === 0) {
                        $updateContact->mobile_no2_wsp = $mobile_no2_wsp;
                    }else{
                        if (strlen($mobile_no2_wsp) == $country_codes[$country_dial_code]) {
                            $updateContact->mobile_no2_wsp = $country_dial_code.''.$mobile_no2_wsp;
                            $updateContact->mobile_no2_wsp_dial_code = $country_dial_code;
                        }else{
                            $updateContact->mobile_no2_wsp = $mobile_no2_wsp;
                        }
                    }
                }else{
                    // Update Allcontact
                    $updateContact->mobile_no2_wsp = $mobile_no2_wsp;
                }
            } else {
                // Update Allcontact
                $updateContact->mobile_no2_wsp = $mobile_no2_wsp;
            }

            if ($mobile_no3_wsp != '') {
                if ($mobile_no3_wsp_dial_code != '') {
                    if (strpos($mobile_no3_wsp,$mobile_no3_wsp_dial_code) === 0) {
                        // Update Allcontact
                        $updateContact->mobile_no3_wsp = $mobile_no3_wsp;
                    }else{
                        if (strlen($mobile_no3_wsp) == $country_codes[$mobile_no3_wsp_dial_code]) {
                            $updateContact->mobile_no3_wsp = $mobile_no3_wsp_dial_code.''.$mobile_no3_wsp;
                        }else{
                            $updateContact->mobile_no3_wsp = $mobile_no3_wsp;
                        }
                    }
                }elseif ($country_dial_code != '') {
                    if (strpos($mobile_no3_wsp, $country_dial_code) === 0) {
                        $updateContact->mobile_no3_wsp = $mobile_no3_wsp;
                    }else{
                        if (strlen($mobile_no3_wsp) == $country_codes[$country_dial_code]) {
                            $updateContact->mobile_no3_wsp = $country_dial_code.''.$mobile_no3_wsp;
                            $updateContact->mobile_no3_wsp_dial_code = $country_dial_code;
                        }else{
                            $updateContact->mobile_no3_wsp = $mobile_no3_wsp;
                        }
                    }
                }else{
                    // Update Allcontact
                    $updateContact->mobile_no3_wsp = $mobile_no3_wsp;
                }
            } else {
                // Update Allcontact
                $updateContact->mobile_no3_wsp = $mobile_no3_wsp;
            }


            $updateContact->save();
        }


        return redirect()->back()->with('success','Country Code updated in Field!');

    }


    public function bulkupdatecountrycodefield_old2(Request $request) {
        $ids = explode(",",$request->contactIDGRPTRC25);
        $contacts = Allcontact::whereIn('id', $ids)->get();

        $countryCodes = [
            "91" => 10,   // India
            "966" => 9,   // Saudi Arabia
            "974" => 8,   // Qatar
            "971" => 9,   // UAE
            "968" => 8,   // Oman
            "965" => 8,   // Kuwait
        ];

        // Fields to check and their respective dial code fields
        $phoneFields = [
            'primary_no_wsp' => 'primary_no_wsp_dial_code',
            'secondary_no_wsp' => 'secondary_no_wsp_dial_code',
            'mobile_no1_wsp' => 'mobile_no1_wsp_dial_code',
            'mobile_no2_wsp' => 'mobile_no2_wsp_dial_code',
            'mobile_no3_wsp' => 'mobile_no3_wsp_dial_code',
        ];

        foreach ($contacts as $contact) {
            $countryDialCode = optional(Country::find($contact->country_id))->country_code;
            $updateContact = Allcontact::find($contact->id);

            foreach ($phoneFields as $field => $dialCodeField) {
                $rawNumber = str_replace(['+', ' '], '', $contact->$field);
                $dialCode = $contact->$dialCodeField;

                if (empty($rawNumber)) {
                    $updateContact->$field = '';
                    continue;
                }

                $finalDialCode = $dialCode ?: $countryDialCode;

                if ($finalDialCode && isset($countryCodes[$finalDialCode])) {
                    $expectedLength = $countryCodes[$finalDialCode];

                    if (strpos($rawNumber, $finalDialCode) === 0) {
                        $updateContact->$field = $rawNumber;
                    }elseif (strlen($rawNumber) == $expectedLength) {
                        $updateContact->$field = $finalDialCode . $rawNumber;
                        // if (empty($dialCode)) {
                        //     $updateContact->$dialCodeField = $finalDialCode;
                        // }
                    }else {
                        $updateContact->$field = $rawNumber;
                    }

                    $updateContact->$dialCodeField = "";
                }else{
                    $updateContact->$field = $rawNumber;
                }
            }

            $updateContact->save();
        }

        return redirect()->back()->with('success','Country Code updated in Field!');

    }

    public function bulkupdatecountrycode(Request $request){

        $ids = explode(",", $request->contactIDGRPTRC2);
        $posts = Allcontact::whereIn('id', $ids)->get();

        // Supported country codes and expected number lengths
        $country_codes = [
            "91" => 10, // India (Usually start with 6 - 9)
            "966" => 9, // Saudi Arabia (Usually start with 5)
            "974" => 8, // Qatar (Usually start with 3,5,6,or 7)
            "971" => 9, // UAE (Usually start with 5)
            "968" => 8, // Oman (Usuallt start with 7 or 9)
            "965" => 8, // Kuwait (Usually start with 5,6, or 9)
        ];

        foreach ($posts as $post) {

            $updateContact = Allcontact::find($post->id);
            $country = Country::find($post->country_id);

            // Fields to process
            $fields = [
                'secondary_no_wsp' => 'secondary_no_wsp_dial_code',
                'primary_no_wsp' => 'primary_no_wsp_dial_code',
                'mobile_no1_wsp' => 'mobile_no1_wsp_dial_code',
                'mobile_no2_wsp' => 'mobile_no2_wsp_dial_code',
            ];


            foreach ($fields as $numberField => $dialCodeField) {
                $number = str_replace(['+', ' '], '', $post->$numberField);
                $dialCode = $post->$dialCodeField;

                // Case A: No dialcode exists in DB
                if (empty($dialCode)) {
                    // No dial code set
                    $updated = false;

                    foreach ($country_codes as $code => $expectedLength) {
                        if (strpos($number, $code) === 0) {
                            $numPart = substr($number, strlen($code));

                            if (strlen($numPart) == $expectedLength) {
                                // Case 1: with code and proper number
                                $updateContact->$numberField = $numPart;
                                $updateContact->$dialCodeField = $code;
                            }else{
                                // Case 2: With Code and Inproper Number
                                $updateContact->$dialCodeField = $code;
                            }

                            $updated = true;
                            break;
                        }
                    }

                    if (!$updated) {
                        if ($country) {
                            $expectedLength = $country_codes[$country->country_code] ?? 0;

                            if (strlen($number) == $expectedLength) {
                                // Case 3: Without code and proper number
                                $updateContact->$numberField = $number;
                                $updateContact->$dialCodeField = $country->country_code;
                            } else {
                                // Case 4: Without code and inproper number
                                $updateContact->$dialCodeField = $country->country_code;
                            }

                        } else {
                            $updateContact->$numberField = $number;
                        }

                    }

                }else{
                    // Case B: Dial code already exists
                    if (strpos($number, $dialCode) === 0) {
                        $numPart = substr($number, strlen($dialCode));
                        $expectedLength = $country_codes[$dialCode] ?? 0;

                        if (strlen($numPart) == $expectedLength) {
                            $updateContact->$numberField = $numPart;
                        }
                    }
                }
            }


            $updateContact->save();

        }

        return redirect()->back()->with('success','Country Code updated!');

    }

    public function allbulkleadownertransfer(Request $request) {
        $ids = explode(",",$request->contactIDLTR);

        foreach ($ids as $id) {
            $post = Allcontact::find($id);

            if ($request->leadowner_id != '') {
                $post->owner_id = $request->leadowner_id;
            }

            $post->save();
        }

        return redirect()->back()->with('success','Lead Owner transfer successfully!');

    }

    public function allbulkcareofftransfer(Request $request)
    {
        $ids = explode(",", $request->contactIDCTR);
    
        foreach ($ids as $id) {
    
            $post = Allcontact::find($id);
    
            if (!$post) {
                continue; // skip invalid ID
            }
    
            if (!empty($request->careoff_id)) {
                $post->careoff_id = $request->careoff_id;
            }
    
            $post->save();
        }
    
        return redirect()->back()->with('success', 'Careoff transfer successfully!');
    }

    // public function allbulkgrouptransfer(Request $request){
    //     $ids = explode(",",$request->contactIDGRPTR);

    //     foreach ($ids as $id) {
    //         $post = Allcontact::find($id);

    //         if ($request->group_id != '') {
    //             $post->group_id = $request->group_id;
    //         }

    //         $post->save();
    //     }

    //     return redirect()->back()->with('success','Group transfer successfully!');
    // }

    public function allbulkgrouptransfer(Request $request)
    {
        if (empty($request->group_id)) {
            return redirect()->back()->with('error', 'Please select group');
        }

        $ids = array_filter(explode(",", $request->contactIDGRPTR));

        Allcontact::whereIn('id', $ids)->update([
            'group_id' => $request->group_id
        ]);

        return redirect()->back()->with('success', 'Group transfer successfully!');
    }

    public function allcheckgroupLimit(Request $request){
        $ids = explode(",",$request->totalSend);
        $group = Groupallc::find($request->grpID);
        $total_send = count($ids);
        $allcontacts = Allcontact::where('group_id','=',$request->grpID)->count();
        $finalLimit = $group->max_limit - $allcontacts;
        if($total_send <= $finalLimit){
            $data = [
                'status' => 1,
                'message' => "Group Limit Available for transfer : ".$finalLimit,
                'grouplimit' => "Group Limit is :".$group->max_limit
            ];
        }else{
            $data = [
                'status' => 0,
                'message' => "Group Limit Exceed, available limit : ".$finalLimit,
                'grouplimit' => "Group Limit is :".$group->max_limit
            ];
        }





        return response()->json($data);
    }


    public function allbulksendwhatsapp(Request $request){
        $ids = explode(",",$request->contactpID);

        $groupID = $request->contact_group_id;

        // $posts = Allcontact::wherein('id',$ids)->wherein('optinout',$request->subscribe)->wherein('status',$request->contact_status)->where(function($query) use($groupID){
        $posts = Allcontact::wherein('id',$ids)->wherein('optinout',$request->subscribe)->where(function($query) use($groupID){
            if($groupID != ''){
                $query->wherein('group_id',$groupID);
            }
        })->get();
        $total_rec = count($posts);

        if($request->send_whatsapp_type == 'meta_whatsapp'){
            $templateDet = Metawhatsapptemplate::find($request->metatemplate_id);
            // Meta Whatsapp API
            // $metaAPI = Metawhatsappapi::where('status','=',1)->first();
            $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['campaign'])->first();
            // dd($metaAPI);

            if ($posts->count() > 0) {

                foreach ($posts as $post) {
                    // Store Bulk contact detail in contactsendwhatsapps
                    $sendcontact = new Allcontactsendwhatsapp();
                    $sendcontact->allcontact_id = $post->id;
                    $sendcontact->for_whatsapp = $request->send_whatsapp_type;
                    $sendcontact->metatemplate_id = $request->metatemplate_id;
                    $sendcontact->contact_type = implode(",",$request->contact_type);
                    $sendcontact->campaign_type = $request->campaign_type;
                    $sendcontact->send_type = "2";
                    if($request->campaign_type == '2'){
                        $sendcontact->date_time = date('Y-m-d h:i',strtotime($request->date_and_time));
                    }
                    // $sendcontact->contact_status = implode(",",$request->contact_status);
                    $sendcontact->contact_subscribe = implode(",",$request->subscribe);
                    if ($request->contact_group_id != '') {
                        $sendcontact->contact_group = implode(",",$request->contact_group_id);
                    }
                    $sendcontact->admin_id = Auth::guard('admin')->user()->id;
                    $sendcontact->save();

                    // Send Message when
                    if ($request->campaign_type == 1) {

                        if (isset($metaAPI)) {
                            // API Detai
                            $base_url = $metaAPI->api_base_url;
                            $vendor_id = $metaAPI->vendor_uid;
                            $access_token = $metaAPI->api_access_token;
                            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                            $token = "Authorization: Bearer ".$access_token;

                            $contact_types = $request->contact_type;

                            // Get Header Data if file not blank
                            if ($templateDet->meta_url_type == 0) {
                                if($templateDet->whatsapp_file != ''){
                                    $headerfilepath = url('admin/assets/images/template/'.$templateDet->whatsapp_file);
                                }else{
                                    $headerfilepath = "";
                                }
                            } elseif ($templateDet->meta_url_type == 1) {
                                if($templateDet->static_url != ''){
                                    $headerfilepath = $templateDet->static_url;
                                }else{
                                    $headerfilepath = "";
                                }
                            } else {
                                if($templateDet->whatsapp_file != ''){
                                    $headerfilepath = url('admin/assets/images/template/'.$templateDet->whatsapp_file);
                                }else{
                                    $headerfilepath = "";
                                }
                            }

                            // Get Unsubscribe URL
                            $random_string = Str::random(32);
                            $unsubscribe_url = 'whatsapp/unsubscribe/request/'.$random_string.'/'.$post->id;

                            // ChatURL Link
                            $staffDet = Admin::find($post->careoff_id);
                            if (isset($staffDet)) {
                                $dynamichaturl = $staffDet->whatsaapp_chat_url.'/'.$staffDet->work_number;
                            }else{
                                $dynamichaturl = "https://wa.me";
                            }

                            // Get Country
                            $getCountry = Country::find($post->country_id);

                            if ($getCountry) {
                                $countryName = $getCountry->name;
                            } else {
                                $countryName = '';
                            }

                            // Get City
                            $getCity = City::find($post->city_id);
                            if ($getCity) {
                                $cityName = $getCity->name;
                            } else {
                                $cityName = '';
                            }

                            $field_var = explode(",",$templateDet->meta_field_var);
                            $assign_var = explode(",",$templateDet->meta_assign_ar);

                            $remStr = [
                                "[Business Type]",
                                "[Full Name]",
                                "[Mobile No]",
                                "[Email]",
                                "[Country]",
                                "[City]",
                                "[Phone0]",
                                "[Email0]",
                                "[Phone1]",
                                "[Email1]",
                                "[Phone2]",
                                "[Email2]",
                                "[Company]"
                            ];

                            $repStr = [
                                "[$post->lead_type]",
                                "[$post->full_name]",
                                "[$post->primary_no_wsp]",
                                "[$post->email]",
                                "[$countryName]",
                                "[$cityName]",
                                "[$post->mobile_no1_wsp]",
                                "[$post->email0]",
                                "[$post->mobile_no2_wsp]",
                                "[$post->email1]",
                                "[$post->mobile_no3_wsp]",
                                "[$post->email2]",
                                "[$post->company_name]"
                            ];

                            $final_array = str_replace($remStr,$repStr,$assign_var);

                            $rem2 = ["[","]"];
                            $rep2 = ["",""];

                            // Send Whatsapp Start
                            $data = [];
                            $data['template_name'] = $templateDet->template_name;
                            $data['template_language'] = "en_US";

                            foreach ($contact_types as $contact_type) {
                                if (($contact_type == 'all' || $contact_type == 'owner') && $post->primary_no_wsp != '') {
                                    $data['phone_number'] = $post->primary_no_wsp;

                                    for ($i=0; $i < count($field_var) ; $i++) {
                                        if ($final_array[$i] == '[Others]') {
                                            $data[$field_var[$i]] = $headerfilepath;
                                        }elseif ($final_array[$i] == '[Dynamic Unsubscribe URL]') {
                                            $data[$field_var[$i]] = $unsubscribe_url;
                                        }elseif ($final_array[$i] == '[Whatsapp Chat Dynamic URL]') {
                                            $data[$field_var[$i]] = $dynamichaturl;
                                        }else{
                                            $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                                        }
                                    }

                                    $data['contact'] =  [
                                        'first_name' => $post->full_name,
                                        'last_name' => "--",
                                        "email" => $post->email,
                                        "country" => $countryName,
                                        "language_code" => "en"
                                    ];

                                    $curl = curl_init();
                                    curl_setopt_array($curl, array(
                                        CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                        CURLOPT_URL => $endpoint_api,
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING => '',
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 0,
                                        CURLOPT_FOLLOWLOCATION => true,
                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => 'POST',
                                        CURLOPT_POSTFIELDS => json_encode($data)
                                    ));

                                    $response = curl_exec($curl);
                                    curl_close($curl);
                                    $responseGet = json_decode($response);
                                    // Upload Campaign Data
                                    $respost = new Sendwhatsappresponse();
                                    $respost->allcontactsend_id = $sendcontact->id;
                                    $respost->name = $post->full_name;
                                    $respost->mobile_no = $post->primary_no_wsp;
                                    if (isset($responseGet->result)){
                                        $respost->message_status = $responseGet->result;
                                        $respost->message_text = $responseGet->message;
                                    }else{
                                        $respost->message_status = "Failed";
                                        $respost->message_text = $responseGet->message;
                                        $respost->error_data_field = json_encode($responseGet->errors);
                                    }
                                    $respost->save();

                                }


                                if (($contact_type == 'all' || $contact_type == 'Primary') && $post->mobile_no1_wsp != '') {
                                    $data['phone_number'] = $post->mobile_no1_wsp;

                                    for ($i=0; $i < count($field_var) ; $i++) {
                                        if ($final_array[$i] == '[Others]') {
                                            $data[$field_var[$i]] = $headerfilepath;
                                        }elseif ($final_array[$i] == '[Dynamic Unsubscribe URL]') {
                                            $data[$field_var[$i]] = $unsubscribe_url;
                                        }elseif ($final_array[$i] == '[Whatsapp Chat Dynamic URL]') {
                                            $data[$field_var[$i]] = $dynamichaturl;
                                        }else{
                                            $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                                        }
                                    }

                                    $data['contact'] =  [
                                        'first_name' => $post->full_name,
                                        'last_name' => "--",
                                        "email" => $post->email0,
                                        "country" => $countryName,
                                        "language_code" => "en"
                                    ];

                                    $curl = curl_init();
                                    curl_setopt_array($curl, array(
                                        CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                        CURLOPT_URL => $endpoint_api,
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING => '',
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 0,
                                        CURLOPT_FOLLOWLOCATION => true,
                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => 'POST',
                                        CURLOPT_POSTFIELDS => json_encode($data)
                                    ));

                                    $response = curl_exec($curl);
                                    curl_close($curl);
                                    $responseGet = json_decode($response);
                                    // Upload Campaign Data
                                    $respost = new Sendwhatsappresponse();
                                    $respost->allcontactsend_id = $sendcontact->id;
                                    $respost->name = $post->full_name;
                                    $respost->mobile_no = $post->mobile_no1_wsp;
                                    if (isset($responseGet->result)){
                                        $respost->message_status = $responseGet->result;
                                        $respost->message_text = $responseGet->message;
                                    }else{
                                        $respost->message_status = "Failed";
                                        $respost->message_text = $responseGet->message;
                                        $respost->error_data_field = json_encode($responseGet->errors);
                                    }
                                    $respost->save();

                                }

                                if (($contact_type == 'all' || $contact_type == 'Secondary') && $post->mobile_no2_wsp != '') {
                                    $data['phone_number'] = $post->mobile_no2_wsp;

                                    for ($i=0; $i < count($field_var) ; $i++) {
                                        if ($final_array[$i] == '[Others]') {
                                            $data[$field_var[$i]] = $headerfilepath;
                                        }elseif ($final_array[$i] == '[Dynamic Unsubscribe URL]') {
                                            $data[$field_var[$i]] = $unsubscribe_url;
                                        }elseif ($final_array[$i] == '[Whatsapp Chat Dynamic URL]') {
                                            $data[$field_var[$i]] = $dynamichaturl;
                                        }else{
                                            $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                                        }
                                    }

                                    $data['contact'] =  [
                                        'first_name' => $post->full_name,
                                        'last_name' => "--",
                                        "email" => $post->email1,
                                        "country" => $countryName,
                                        "language_code" => "en"
                                    ];

                                    $curl = curl_init();
                                    curl_setopt_array($curl, array(
                                        CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                        CURLOPT_URL => $endpoint_api,
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING => '',
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 0,
                                        CURLOPT_FOLLOWLOCATION => true,
                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => 'POST',
                                        CURLOPT_POSTFIELDS => json_encode($data)
                                    ));

                                    $response = curl_exec($curl);
                                    curl_close($curl);
                                    $responseGet = json_decode($response);
                                    // Upload Campaign Data
                                    $respost = new Sendwhatsappresponse();
                                    $respost->allcontactsend_id = $sendcontact->id;
                                    $respost->name = $post->full_name;
                                    $respost->mobile_no = $post->mobile_no2_wsp;
                                    if (isset($responseGet->result)){
                                        $respost->message_status = $responseGet->result;
                                        $respost->message_text = $responseGet->message;
                                    }else{
                                        $respost->message_status = "Failed";
                                        $respost->message_text = $responseGet->message;
                                        $respost->error_data_field = json_encode($responseGet->errors);
                                    }
                                    $respost->save();

                                }
                            }

                            // Update Contact Send Tag and Store Send Tag
                            // $countcontactTag = Contactsendtag::where('contact_id','=',$post->id)->count();
                            // $contactTag = new Contactsendtag();
                            // $contactTag->contact_id = $post->id;
                            // $contactTag->send_tag = $countcontactTag + 1;
                            // $contactTag->send_date = date('Y-m-d');
                            // $contactTag->save();

                            // Update Contact Send Tag and Send Date
                            // $updContactSendTag = Contactplus::find($post->id);
                            // $updContactSendTag->send_tag = "Send ".$contactTag->send_tag;
                            // $updContactSendTag->send_date = $contactTag->send_date.",".$post->send_date;
                            // $updContactSendTag->save();

                        }else{
                            $respost = new Sendwhatsappresponse();
                            $respost->allcontactsend_id = $sendcontact->id;
                            $respost->message_status = "Failed";
                            $respost->message_text = "Meta Whatsapp API Not Connect";
                            $respost->save();
                        }


                    }
                }

                return redirect()->back()->with('success',$total_rec.' whatsapp message are processs for scheduled send!');
            }else{
                return redirect()->back()->with('success',$total_rec.' whatsapp message are processs for scheduled send!');
            }



        }else{

            // dd($request);

            // Send Instant Message

            // Get Template Data
            $templateData = Whatsappcamptemplate::find($request->template_id);
            // get API
            $getAPIs = Whatsappapi::wherein('id',$request->wapi_id_text)->get();

            $filepath = url('/admin/assets/images/template/'.$templateData->file);

            // dd($filepath);

            if ($posts->count() > 0) {

                foreach ($posts as $contactdet) {
                    // Store Bulk Contact in
                    $post = new Allcontactsendwhatsapp();
                    $post->allcontact_id = $contactdet->id;
                    $post->for_whatsapp = $request->send_whatsapp_type;
                    $post->template_id = $request->template_id;
                    $post->msg_body_temp = $request->whatsapp_message;
                    $post->template_file = $templateData->file;
                    $post->file_path_url = $filepath;
                    $post->whatsapp_api = implode(",",$request->wapi_id_text);
                    $post->contact_type = implode(",",$request->contact_type);
                    $post->campaign_type = $request->campaign_type2;
                    if($request->campaign_type2 == '2'){
                        $post->date_time = date('Y-m-d h:i',strtotime($request->date_and_time2));
                    }
                    $post->admin_id = Auth::guard('admin')->user()->id;
                    // $post->contact_status = implode(",",$request->contact_status);
                    $post->contact_subscribe = implode(",",$request->subscribe);
                    if ($request->contact_group_id != '') {
                        $post->contact_group = implode(",",$request->contact_group_id);
                    }
                    $post->save();

                    if ($request->campaign_type2 == '1') {
                        // Create Send Job
                        foreach ($getAPIs as $getAPI) {
                            dispatch(new BulkAllContactSendJob($contactdet,$post,$getAPI))->onConnection('database')->onQueue('default');
                        }
                    }
                }



                return redirect()->back()->with('success',$total_rec.' whatsapp message are processs for send!');
            } else {
                return redirect()->back()->with('success',$total_rec.' whatsapp message are processs for send!');
            }


            return redirect()->back()->with('success',$$total_recsch);
        }

    }


    public function sendcontactwhatsapp($id,Request $request){
        $post = new Allcontactsendwhatsapp();

        if($request->send_whatsapp_type == 'meta_whatsapp'){
            // Store in DB
            $post->allcontact_id = $id;
            $post->for_whatsapp = $request->send_whatsapp_type;
            $post->metatemplate_id = $request->metatemplate_id;
            $post->contact_type = implode(",",$request->contact_type);
            $post->campaign_type = $request->campaign_type;
            $post->send_type = "1";
            if($request->campaign_type == '2'){
                $post->date_time = date('Y-m-d h:i',strtotime($request->date_and_time));
            }
            $post->admin_id = Auth::guard('admin')->user()->id;
            $post->save();

            $contact_types = $request->contact_type;
            // Send Whatsapp as per Campaign Type
            if($request->campaign_type == '1'){
                $templateDet = Metawhatsapptemplate::find($request->metatemplate_id);
                $contactDet = Allcontact::find($id);

                // Meta Whatsapp API
                // $metaAPI = Metawhatsappapi::where('status','=',1)->first();
                $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['campaign'])->first();
                // Get Country
                $getCountry = Country::find($contactDet->country_id);

                if ($getCountry) {
                    $countryName = $getCountry->name;
                } else {
                    $countryName = '';
                }

                // Get City
                $getCity = City::find($contactDet->city_id);
                if ($getCity) {
                    $cityName = $getCity->name;
                } else {
                    $cityName = '';
                }

                $field_var = explode(",",$templateDet->meta_field_var);
                $assign_var = explode(",",$templateDet->meta_assign_ar);

                $remStr = [
                    "[Business Type]",
                    "[Full Name]",
                    "[Mobile No]",
                    "[Email]",
                    "[Country]",
                    "[City]",
                    "[Phone0]",
                    "[Email0]",
                    "[Phone1]",
                    "[Email1]",
                    "[Phone2]",
                    "[Email2]",
                    "[Company]"
                ];

                $repStr = [
                    "[$post->lead_type]",
                    "[$post->full_name]",
                    "[$post->primary_no_wsp]",
                    "[$post->email]",
                    "[$countryName]",
                    "[$cityName]",
                    "[$post->mobile_no1_wsp]",
                    "[$post->email0]",
                    "[$post->mobile_no2_wsp]",
                    "[$post->email1]",
                    "[$post->mobile_no3_wsp]",
                    "[$post->email2]",
                    "[$post->company_name]"
                ];

                $final_array = str_replace($remStr,$repStr,$assign_var);

                // Get Header Data if file not blank
                if($templateDet->whatsapp_file != ''){
                    $headerfilepath = url('admin/assets/images/template/'.$templateDet->whatsapp_file);
                }else{
                    $headerfilepath = "";
                }

                $data = [];
                $data['template_name'] = $templateDet->template_name;
                $data['template_language'] = "en_US";

                $rem2 = ["[","]"];
                $rep2 = ["",""];

                foreach($contact_types as $contact_type){
                    if($contact_type == 'all'){


                        if($contactDet->primary_no_wsp != ''){
                            $data['phone_number'] = $contactDet->primary_no_wsp;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->full_name,
                                'last_name' => "--",
                                "email" => $contactDet->email,
                                "country" => $countryName,
                                "language_code" => "en"
                            ];

                            // Send Campaign Message
                            if(isset($metaAPI)){
                                $base_url = $metaAPI->api_base_url;
                                $vendor_id = $metaAPI->vendor_uid;
                                $access_token = $metaAPI->api_access_token;
                                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                                $token = "Authorization: Bearer ".$access_token;

                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                    CURLOPT_URL => $endpoint_api,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => json_encode($data)
                                ));

                                $response = curl_exec($curl);
                                curl_close($curl);
                                $responseGet = json_decode($response);
                                // Upload Campaign Data
                                $respost = new Sendwhatsappresponse();
                                $respost->allcontact_id = $post->id;
                                $respost->name = $contactDet->full_name;
                                $respost->mobile_no = $contactDet->primary_no_wsp;
                                if (isset($responseGet->result)){
                                    $respost->message_status = $responseGet->result;
                                    $respost->message_text = $responseGet->message;
                                }else{
                                    $respost->message_status = "Failed";
                                    $respost->message_text = $responseGet->message;
                                    $respost->error_data_field = json_encode($responseGet->errors);
                                }
                                $respost->save();
                            }
                        }

                        if($contactDet->secondary_no_wsp != ''){
                            $data['phone_number'] = $contactDet->secondary_no_wsp;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->full_name,
                                'last_name' => "--",
                                "email" => $contactDet->email,
                                "country" => $countryName,
                                "language_code" => "en"
                            ];

                            // Send Campaign Message
                            if(isset($metaAPI)){
                                $base_url = $metaAPI->api_base_url;
                                $vendor_id = $metaAPI->vendor_uid;
                                $access_token = $metaAPI->api_access_token;
                                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                                $token = "Authorization: Bearer ".$access_token;

                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                    CURLOPT_URL => $endpoint_api,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => json_encode($data)
                                ));

                                $response = curl_exec($curl);
                                curl_close($curl);
                                $responseGet = json_decode($response);
                                // Upload Campaign Data
                                $respost = new Sendwhatsappresponse();
                                $respost->allcontact_id = $post->id;
                                $respost->name = $contactDet->full_name;
                                $respost->mobile_no = $contactDet->primary_no_wsp;
                                if (isset($responseGet->result)){
                                    $respost->message_status = $responseGet->result;
                                    $respost->message_text = $responseGet->message;
                                }else{
                                    $respost->message_status = "Failed";
                                    $respost->message_text = $responseGet->message;
                                    $respost->error_data_field = json_encode($responseGet->errors);
                                }
                                $respost->save();
                            }
                        }

                        if($contactDet->mobile_no1_wsp != ''){
                            $data['phone_number'] = $contactDet->mobile_no1_wsp;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->full_name,
                                'last_name' => "--",
                                "email" => $contactDet->email0,
                                "country" => $countryName,
                                "language_code" => "en"
                            ];

                            // Send Campaign Message
                            if(isset($metaAPI)){
                                $base_url = $metaAPI->api_base_url;
                                $vendor_id = $metaAPI->vendor_uid;
                                $access_token = $metaAPI->api_access_token;
                                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                                $token = "Authorization: Bearer ".$access_token;

                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                    CURLOPT_URL => $endpoint_api,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => json_encode($data)
                                ));

                                $response = curl_exec($curl);
                                curl_close($curl);
                                $responseGet = json_decode($response);
                                // Upload Campaign Data
                                $respost = new Sendwhatsappresponse();
                                $respost->allcontact_id = $post->id;
                                $respost->name = $contactDet->full_name;
                                $respost->mobile_no = $contactDet->mobile_no1_wsp;
                                if (isset($responseGet->result)){
                                    $respost->message_status = $responseGet->result;
                                    $respost->message_text = $responseGet->message;
                                }else{
                                    $respost->message_status = "Failed";
                                    $respost->message_text = $responseGet->message;
                                    $respost->error_data_field = json_encode($responseGet->errors);
                                }
                                $respost->save();
                            }
                        }

                        if($contactDet->mobile_no2_wsp != ''){
                            $data['phone_number'] = $contactDet->mobile_no2_wsp;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->full_name,
                                'last_name' => "--",
                                "email" => "---",
                                "country" => $countryName,
                                "language_code" => "en"
                            ];

                            // Send Campaign Message
                            if(isset($metaAPI)){
                                $base_url = $metaAPI->api_base_url;
                                $vendor_id = $metaAPI->vendor_uid;
                                $access_token = $metaAPI->api_access_token;
                                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                                $token = "Authorization: Bearer ".$access_token;

                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                    CURLOPT_URL => $endpoint_api,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => json_encode($data)
                                ));

                                $response = curl_exec($curl);
                                curl_close($curl);
                                $responseGet = json_decode($response);
                                // Upload Campaign Data
                                $respost = new Sendwhatsappresponse();
                                $respost->allcontact_id = $post->id;
                                $respost->name = $contactDet->full_name;
                                $respost->mobile_no = $contactDet->mobile_no2_wsp;
                                if (isset($responseGet->result)){
                                    $respost->message_status = $responseGet->result;
                                    $respost->message_text = $responseGet->message;
                                }else{
                                    $respost->message_status = "Failed";
                                    $respost->message_text = $responseGet->message;
                                    $respost->error_data_field = json_encode($responseGet->errors);
                                }

                                $respost->save();
                            }
                        }

                        if($contactDet->mobile_no3_wsp != ''){
                            $data['phone_number'] = $contactDet->mobile_no3_wsp;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->full_name,
                                'last_name' => "--",
                                "email" => "---",
                                "country" => $countryName,
                                "language_code" => "en"
                            ];

                            // Send Campaign Message
                            if(isset($metaAPI)){
                                $base_url = $metaAPI->api_base_url;
                                $vendor_id = $metaAPI->vendor_uid;
                                $access_token = $metaAPI->api_access_token;
                                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                                $token = "Authorization: Bearer ".$access_token;

                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                    CURLOPT_URL => $endpoint_api,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => json_encode($data)
                                ));

                                $response = curl_exec($curl);
                                curl_close($curl);
                                $responseGet = json_decode($response);
                                // Upload Campaign Data
                                $respost = new Sendwhatsappresponse();
                                $respost->allcontact_id = $post->id;
                                $respost->name = $contactDet->full_name;
                                $respost->mobile_no = $contactDet->mobile_no3_wsp;
                                if (isset($responseGet->result)){
                                    $respost->message_status = $responseGet->result;
                                    $respost->message_text = $responseGet->message;
                                }else{
                                    $respost->message_status = "Failed";
                                    $respost->message_text = $responseGet->message;
                                    $respost->error_data_field = json_encode($responseGet->errors);
                                }

                                $respost->save();
                            }
                        }


                    }

                    if($contact_type == 'owner'){
                        if($contactDet->primary_no_wsp != ''){
                            $data['phone_number'] = $contactDet->primary_no_wsp;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->full_name,
                                'last_name' => "--",
                                "email" => $contactDet->email0,
                                "country" => $countryName,
                                "language_code" => "en"
                            ];

                            // Send Campaign Message
                            if(isset($metaAPI)){
                                $base_url = $metaAPI->api_base_url;
                                $vendor_id = $metaAPI->vendor_uid;
                                $access_token = $metaAPI->api_access_token;
                                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                                $token = "Authorization: Bearer ".$access_token;

                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                    CURLOPT_URL => $endpoint_api,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => json_encode($data)
                                ));

                                $response = curl_exec($curl);
                                curl_close($curl);
                                $responseGet = json_decode($response);
                                // Upload Campaign Data
                                $respost = new Sendwhatsappresponse();
                                $respost->allcontact_id = $post->id;
                                $respost->name = $contactDet->full_name;
                                $respost->mobile_no = $contactDet->primary_no_wsp;
                                if (isset($responseGet->result)){
                                    $respost->message_status = $responseGet->result;
                                    $respost->message_text = $responseGet->message;
                                }else{
                                    $respost->message_status = "Failed";
                                    $respost->message_text = $responseGet->message;
                                    $respost->error_data_field = json_encode($responseGet->errors);
                                }
                                $respost->save();
                            }
                        }
                    }

                    if($contact_type == 'Primary'){
                        if($contactDet->secondary_no_wsp != ''){
                            $data['phone_number'] = $contactDet->secondary_no_wsp;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->full_name,
                                'last_name' => "--",
                                "email" => $contactDet->email1,
                                "country" => $countryName,
                                "language_code" => "en"
                            ];

                            // Send Campaign Message
                            if(isset($metaAPI)){
                                $base_url = $metaAPI->api_base_url;
                                $vendor_id = $metaAPI->vendor_uid;
                                $access_token = $metaAPI->api_access_token;
                                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                                $token = "Authorization: Bearer ".$access_token;

                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                    CURLOPT_URL => $endpoint_api,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => json_encode($data)
                                ));

                                $response = curl_exec($curl);
                                curl_close($curl);
                                $responseGet = json_decode($response);
                                // Upload Campaign Data
                                $respost = new Sendwhatsappresponse();
                                $respost->allcontact_id = $post->id;
                                $respost->name = $contactDet->full_name;
                                $respost->mobile_no = $contactDet->secondary_no_wsp;
                                if (isset($responseGet->result)){
                                    $respost->message_status = $responseGet->result;
                                    $respost->message_text = $responseGet->message;
                                }else{
                                    $respost->message_status = "Failed";
                                    $respost->message_text = $responseGet->message;
                                    $respost->error_data_field = json_encode($responseGet->errors);
                                }
                                $respost->save();
                            }
                        }
                    }

                    if($contact_type == 'Secondary'){
                        if($contactDet->mobile_no1_wsp != ''){
                            $data['phone_number'] = $contactDet->mobile_no1_wsp;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->full_name,
                                'last_name' => "--",
                                "email" => $contactDet->email1,
                                "country" => $countryName,
                                "language_code" => "en"
                            ];

                            // Send Campaign Message
                            if(isset($metaAPI)){
                                $base_url = $metaAPI->api_base_url;
                                $vendor_id = $metaAPI->vendor_uid;
                                $access_token = $metaAPI->api_access_token;
                                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                                $token = "Authorization: Bearer ".$access_token;

                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                    CURLOPT_URL => $endpoint_api,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => json_encode($data)
                                ));

                                $response = curl_exec($curl);
                                curl_close($curl);
                                $responseGet = json_decode($response);
                                // Upload Campaign Data
                                $respost = new Sendwhatsappresponse();
                                $respost->allcontact_id = $post->id;
                                $respost->name = $contactDet->full_name;
                                $respost->mobile_no = $contactDet->mobile_no1_wsp;
                                if (isset($responseGet->result)){
                                    $respost->message_status = $responseGet->result;
                                    $respost->message_text = $responseGet->message;
                                }else{
                                    $respost->message_status = "Failed";
                                    $respost->message_text = $responseGet->message;
                                    $respost->error_data_field = json_encode($responseGet->errors);
                                }

                                $respost->save();
                            }
                        }
                    }
                }


                // Update Contact Send Tag and Store Send Tag
                // $countcontactTag = Contactsendtag::where('contact_id','=',$id)->count();

                // $contactTag = new Contactsendtag();
                // $contactTag->contact_id = $id;
                // $contactTag->send_tag = $countcontactTag + 1;
                // $contactTag->send_date = date('Y-m-d');
                // $contactTag->save();

                // Update Contact Send Tag and Send Date
                // $updContactSendTag = Contactplus::find($id);
                // $updContactSendTag->send_tag = "Send ".$contactTag->send_tag;
                // $updContactSendTag->send_date = $contactTag->send_date.",".$contactDet->send_date;
                // $updContactSendTag->save();

                return redirect()->back()->with('success','Whatsapp Message sent!');

            }else{

                return redirect()->back()->with('success','Send Message are scheduled');
            }

        }else{

            $templateDet = Whatsappcamptemplate::find($request->template_id);
            $contactDet = Allcontact::find($id);
            $getAPI = Whatsappapi::wherein('id',$request->wapi_id_text)->get();
            $filepath = url('/admin/assets/images/template/'.$templateDet->file);
            // Store in DB
            $post->allcontact_id = $id;
            $post->for_whatsapp = $request->send_whatsapp_type;
            $post->template_id = $request->template_id;
            $post->msg_body_temp = $templateDet->msg_whatsapp;
            $post->template_file = $templateDet->file;
            $post->file_path_url = $filepath;
            $post->whatsapp_api = implode(",",$request->wapi_id_text);
            $post->contact_type = implode(",",$request->contact_type);
            $post->campaign_type = $request->campaign_type;
            $post->send_type = "1";
            if($request->campaign_type == '2'){
                $post->date_time = date('Y-m-d h:i',strtotime($request->date_and_time));
            }
            $post->admin_id = Auth::guard('admin')->user()->id;
            $post->save();

            $contact_types = $request->contact_type;
            // Send Whatsapp as per Campaign Type
            if ($request->campaign_type == '1') {
                foreach ($getAPI as $getapi2) {
                    dispatch(new BulkContactSendJob($contactDet,$post,$getapi2))->onConnection('database')->onQueue('default');
                }
            } else {
                return redirect()->back()->with('success','Send Message are scheduled');
            }



            return redirect()->back()->with('success','Normal Whatsapp is under process');
        }
    }

    public function addreminder(Request $request){
        $post = new Allcontactreminder();
        $post->allcontact_id = $request->contactID;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->title = $request->title;
        $post->desc = $request->desc;
        $post->reminder_type = $request->reminder_type;
        $post->due_date = $request->due_date;
        $post->time = $request->time;
        $post->careoff_id = $request->careoff_id;
        $post->save();

        // Add reminder in todo
        $todopost = new Todo();
        $todopost->task_title = $request->title;
        $todopost->task_description = $request->desc;
        $todopost->assignto_id = $post->careoff_id;
        $todopost->start_on = date('Y-m-d');
        $todopost->finish_on = date('Y-m-d',strtotime($request->due_date));
        $todopost->reminder_cycle = "Low";
        $todopost->task_status = "New Task";
        $todopost->admin_id	= Auth::guard('admin')->user()->id;
        $todopost->save();

        return redirect()->back()->with('success','Reminder created!');
    }

    public function updateaddreminder(Request $request){
        if ($request->new_reminder == 'Required') {
            $post = new Allcontactreminder();
            $post->title = $request->title;
            $post->allcontact_id = $request->contactID;
            $post->careoff_id = $request->careoff_id;
            $post->desc = $request->desc;
            $post->reminder_type = $request->reminder_type;
            $post->due_date = $request->due_date;
            $post->time = $request->time;
            $post->admin_id = Auth::guard('admin')->user()->id;
            $post->save();

            // Inactive Previous Reminder
            $prepost = Allcontactreminder::find($request->reminder_id);
            $prepost->status = false;
            $prepost->save();

            return redirect()->back()->with('success','New reminder created!');
        }else{
            $updatePost = Allcontactreminder::find($request->reminder_id);
            $updatePost->title = $request->title;
            $updatePost->desc = $request->desc;
            $updatePost->save();

            return redirect()->back()->with('success','Reminder updated!');
        }
    }

    public function addnotes(Request $request){

        $all_contact = Allcontact::find($request->contactID);

        $post = new Allcontactnote();
        $post->notes = $request->notes;
        $post->allcontact_id = $request->contactID;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->conversation_type = $request->conversation_type;
        $post->save();

        $all_contact->updated_at = Now();
        $all_contact->save();

        return redirect()->back()->with('success','Notes updated!');
    }


    public function deletenotes(Request $request)
    {
        $data = Allcontactnote::find($request->id);
    
        if (!$data) {
            return redirect()->back()->with('error', 'Note not found!');
        }
    
        $allContactId = $data->allcontact_id;
    
        $data->delete();
    
        $all_contact = Allcontact::find($allContactId);
        if ($all_contact) {
            $all_contact->updated_at = now();
            $all_contact->save();
        }
    
        return redirect()->back()->with('success','Notes Removed!');
    }
    
    
    

    public function allcontactgrouplist(Request $request){
        $getStaffGroup = Groupallc::where('staff_id','=',Auth::guard('admin')->user()->id)->count();
        $reservedGrp = 'Group'.($getStaffGroup + 1);

        $stafflists = Admin::where('status', 1)->orderBy('name')->get();

        return view('admin.allcontact.group.index',compact('reservedGrp','stafflists'));
    }

    public function allcontactgrouplistJson(Request $request) {
        $post = Groupallc::orderBy('id','DESC')->get();
        $data['data'] = $post;

        return response()->json($data);

    }

    public function allcontactgroupcheckname(Request $request){
        $name = $request->name;

        if (isset($request->id) && $request->id != '') {
            $post = Groupallc::where('name','=',$name)->where('id','!=',$request->id)->count();
        } else {
            $post = Groupallc::where('name','=',$name)->count();
        }

        if ($post == 0) {
            $isAvailable = 'true';
        } else {
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));

    }



    public function updatecountrybasequery(Request $request){
        $idsv = explode(",",$request->contactIDGRPTRC);
        if ($request->country_query == 'India') {
            $countryno = "91%";
        } else {
            $countryno = "966%";
        }

        $posts = Allcontact::where('primary_no_wsp','LIKE',$countryno)->where('id',$idsv)->get();

        foreach ($posts as $post) {
            $post->country_id = $request->country_id;
            $post->save();
        }

        return redirect()->back()->with('success','Country updated!');
    }

    public function groupstore(Request $request){

        // dd($request);

        $post = new Groupallc();
        $post->name = $request->name;
        // $post->name = $request->group_name;
        $post->max_limit = $request->max_limit;
        $post->admin_id = Auth::guard('admin')->user()->id;
        // if (Auth::guard('admin')->user()->user_type == 1) {
        //     $post->staff_id = $request->staff_id;
        // } else {
        //     $post->staff_id = Auth::guard('admin')->user()->id;
        // }

        $post->save();

        return redirect()->back()->with('success','Group Allcontact created!');
    }

    public function groupedit(Request $request){
        $post = Groupallc::find($request->id);

        return response()->json($post);
    }

    public function groupupdt(Request $request){
        $post = Groupallc::find($request->edit_id);
        $post->name = $request->name;
        $post->max_limit = $request->max_limit;
        $post->save();

        return redirect()->back()->with('success','Group Allcontact Updated!');

    }

    public function bulkdeleteallc(Request $request){
        $ids = explode(",",$request->contactids);

        foreach ($ids as $id) {
            $post = Allcontact::find($id);

            $post->delete();

        }

        return redirect()->back()->with('success','Delete contact data');
    }

    public function  deleteContact(Request $request) {
        $post = Allcontact::find($request->contactID);

        $post->delete();

        return redirect()->back()->with('success','Delete All Contact');

    }

    public function getCareoffByNumber(Request $request)
    {
        $request->validate([
            'number' => 'required|string'
        ]);

        $searchText = '%' . $request->number . '%';

        try {

            $post = Allcontact::select('id', 'careoff_id')
                ->with('careoff:id,name')
                ->where(function ($q) use ($searchText) {
                    $q->where('primary_no_wsp', 'like', $searchText)
                    ->orWhere('secondary_no_wsp', 'like', $searchText)
                    ->orWhere('mobile_no1_wsp', 'like', $searchText)
                    ->orWhere('mobile_no2_wsp', 'like', $searchText)
                    ->orWhere('mobile_no3_wsp', 'like', $searchText);
                })
                ->orderBy('created_at', 'ASC')
                ->first();

            // Find lead by mobile or whatsapp number
            $lead = Lead::select('id', 'leadassign_id')
                ->with('leadassign:id,name')   
                ->where('mob_no', 'like', $searchText)
                ->orWhere('whatsapp_no', 'like', $searchText)
                ->first();

            if (!$post || !$post->careoff) {
                return response()->json([
                    'status' => false,
                    'message' => 'Careoff not found',
                    'data' => null,
                    'lead' => $lead ? [
                        'id' => $lead->id,
                        'name' => $lead->name
                    ] : null
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Careoff found',
                'data' => [
                    'id' => $post->careoff->id,
                    'name' => $post->careoff->name
                ],
                'lead' => $lead ? [
                    'id' => $lead->leadassign->id,
                    'name' => $lead->leadassign->name
                ] : null
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    // public function getCareoffByNumber(Request $request)
    // {
    //     // Validate input
    //     $request->validate([
    //         'number' => 'required|string'
    //     ]);
    
    //     $searchText = '%' . $request->number . '%';
    
    //     try {
    //         $post = Allcontact::select('id', 'careoff_id')
    //             ->with('careoff:id,name')
    //             ->where(function ($q) use ($searchText) {
    //                 $q->where('primary_no_wsp', 'like', $searchText)
    //                   ->orWhere('secondary_no_wsp', 'like', $searchText)
    //                   ->orWhere('mobile_no1_wsp', 'like', $searchText)
    //                   ->orWhere('mobile_no2_wsp', 'like', $searchText)
    //                   ->orWhere('mobile_no3_wsp', 'like', $searchText);
    //             })
    //             ->orderBy('created_at', 'ASC')
    //             ->first();
    
    //         // If no contact OR no careoff relation
    //         if (!$post || !$post->careoff) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Careoff not found',
    //                 'data' => null
    //             ], 200);
    //         }
    
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Careoff found',
    //             'data' => [
    //                 'id' => $post->careoff->id,
    //                 'name' => $post->careoff->name
    //             ]
    //         ], 200);
    
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function syncUserContacts(Request $request)
    {

        $request->validate([
            'careoff_id' => 'required',
            'contacts' => 'required|string',
            'full_name' => 'nullable|string',
        ]);
    
        try {

            $careoffId = $request->careoff_id;
            $fullName = $request->full_name;
            $email = $request->email;
            
            $isActiveCareoff = Admin::where('login_status',1)->where('status',1)->whereId($careoffId)->exists();
            
            // ❌ Stop if not active
            if (!$isActiveCareoff) {
                return response()->json([
                    'status' => false,
                    'message' => 'Careoff user is not active'
                ], 200);
            }
            // normalize phone
            $phone = preg_replace('/\D/', '', $request->contacts);
            if (!$phone) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid phone number'
                ], 200);
            }
    
            $searchText = $phone;
    
            // 🔍 Check existing in all number fields
            $existing = Allcontact::where(function ($q) use ($searchText) {
                $q->where('primary_no_wsp', $searchText)
                  ->orWhere('secondary_no_wsp', $searchText)
                  ->orWhere('mobile_no1_wsp', $searchText)
                  ->orWhere('mobile_no2_wsp', $searchText)
                  ->orWhere('mobile_no3_wsp', $searchText);
            })->first();

            if ($existing) {
                // ✅ Update
                $existing->update([
                    'full_name'  => $fullName ?? $existing->full_name,
                    'email'      => $email ?? $existing->email,
                    'careoff_id' => $careoffId,
                ]);
    
                return response()->json([
                    'status' => true,
                    'message' => 'Contact updated successfully',
                    'data' => $existing
                ], 200);
            }
    
            // ✅ Insert new
            $new = Allcontact::create([
                'full_name'      => $fullName ?? '',
                'email'          => $email,
                'primary_no_wsp' => $phone,
                'careoff_id'     => $careoffId,
                'source' => 'whatsAir'
            ]);
    
            return response ()->json([
                'status' => true,
                'message' => 'Contact created successfully',
                'data' => $new
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Sync failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function groupWiseReport()
    {
        $users = Admin::where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        $groups = Groupallc::orderBy('name')
            ->get(['id', 'name']);

        // Get all counts in one query
        $counts = Allcontact::select(
                'group_id',
                'careoff_id',
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('group_id')
            ->whereNotNull('careoff_id')
            ->groupBy('group_id', 'careoff_id')
            ->get();

        // Lookup array
        $lookup = [];

        foreach ($counts as $count) {
            $lookup[$count->group_id][$count->careoff_id] = $count->total;
        }

        /*
        |--------------------------------------------------------------------------
        | Remove users having no contacts in any group
        |--------------------------------------------------------------------------
        */
        $activeUsers = collect();

        foreach ($users as $user) {

            $hasData = false;

            foreach ($groups as $group) {

                if (($lookup[$group->id][$user->id] ?? 0) > 0) {
                    $hasData = true;
                    break;
                }
            }

            if ($hasData) {
                $activeUsers->push($user);
            }
        }

        $users = $activeUsers;

        /*
        |--------------------------------------------------------------------------
        | Prepare rows and remove empty groups
        |--------------------------------------------------------------------------
        */
        $data = [];

        foreach ($groups as $group) {

            $row = [
                'group' => $group->name
            ];

            $hasRowData = false;

            foreach ($users as $user) {

                $count = $lookup[$group->id][$user->id] ?? 0;

                $row[$user->id] = $count;

                if ($count > 0) {
                    $hasRowData = true;
                }
            }

            // Skip empty groups
            if ($hasRowData) {
                $data[] = $row;
            }
        }

        $html = view('admin.allcontact.groupwise_report_table', compact(
            'users',
            'data'
        ))->render();

        return response()->json([
            'html' => $html
        ]);
    }
    

}
