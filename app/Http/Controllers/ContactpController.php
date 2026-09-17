<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Contactp;
use App\Models\Contactplus;
use App\Models\Leadstage;
use App\Models\Lifecyclestatus;
use App\Models\Whatsappapi;
use App\Models\Whatsappcamptemplate;
use Illuminate\Support\Facades\Auth;
use App\Models\Businesstype;
use App\Models\Adminpermission;
use App\Models\Groupm;
use App\Models\Contactpnotes;
use App\Models\Contactreminder;
use App\Models\Metawhatsapptemplate;
use App\Models\Metawhatsappapi;
use App\Models\Contactsendwhatsapp;
use App\Models\Metawhatsappcampaignresponse;
use App\Models\Sendwhatsappresponse;
use App\Models\Contactpfilter;
use App\Models\Contactsendtag;
use App\Models\Campaignlist;
use App\Jobs\BulkContactSendJob;
use App\Models\Allcontactadminsavefilter;
use App\Models\Todo;
use Str;
use App\Models\Contactplusadminsavefilter;
use App\Models\Industry;
use App\Jobs\ContactPlusProcessCsvImport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use App\Services\ContactPlusExportService;
use App\Models\ExportContactPlusHistory;

use App\Jobs\ExportContactPlusHistoryJob;

class ContactpController extends Controller
{

   protected ContactPlusExportService $contactPlusExportService;

    public function __construct(ContactPlusExportService $contactPlusExportService)
    {
        $this->contactPlusExportService = $contactPlusExportService;
    }

    public function index_new()
    {
        $contactsaveadminfilter = Contactplusadminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

        return view('admin.contactp.index_new',['contactsaveadminfilter' => $contactsaveadminfilter]);

    }

    public function jsonData(Request $request) {
        
    }

    /** Whitelisted operator labels for admin-defined Contact Plus custom filters (must match the UI <option> values verbatim). */
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
     * model's custom_filters column) against a live whitelist of contactpluses
     * columns and the fixed operator list above. Invalid/incomplete rows are
     * silently dropped rather than raising a validation error, so live AJAX
     * filtering degrades gracefully mid-edit. This is the only place custom
     * filter column/operator whitelisting happens.
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

        $allowedColumns = Schema::getColumnListing((new Contactplus())->getTable());

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

    public function refinedcontact(Request $request){

        $countries = Country::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        $groupms = Groupm::orderBy('name','ASC')->get();
        $lcss = Lifecyclestatus::orderBy('id')->get();
        $leadStages = Leadstage::orderBy('name')->get();
        $metatemplates = Metawhatsapptemplate::where('status','=',1)->get();
        $normaltemplates = Whatsappcamptemplate::where('status','=',1)->where('audience','=','Contact+')->get();
        $wapis = Whatsappapi::where('api_for','=','campaign_not')->orderBy('id','DESC')->get();
        // $metawhatsappAPIs = Metawhatsappapi::where('status','=',1)->get();
        $metawhatsappAPIs = Metawhatsappapi::orderBy('api_name')->whereRaw("FIND_IN_SET (?,api_assign_to)",['campaign'])->get();

        $businesstypes = Businesstype::orderBy('name')->get();
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        $adminusers = Admin::where('status', 1)->orderBy('name')->get();
        $contactpfilter = Contactpfilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();
        $industries = Industry::orderBy('name')->get();
        $contactsaveadminfilter = Contactplusadminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {

            $posts = Contactplus::with(['country','city','contactstatus','ls','lcs','businesstype','groupm','group']);

            if ($request->ajax()) {
                $posts->FilterCountry($request->country_id)
                ->FilterCity($request->city_id)
                ->FilterLcs($request->lcs_id)
                ->FilterLs($request->ls_id)
                ->FilterBusinesstype($request->businesstype_id)
                ->FilterIndustry($request->industries)
                ->FilterGroup($request->group_id)
                ->FilterCreatedBy($request->created_by)
                ->FilterSendTag($request->send_tag)
                ->FilterSubscribe($request->subscribe)
                ->FilterDate('send_date',$request->send_date)
                ->FilterDateRange('update_lead_status_date',$request->update_lead_status_date)
                ->FilterDate('created_at',$request->created_at)
                ->FilterDate('updated_at',$request->updated_at)
                ->FilterSearchText($request->search_text)
                ->FilterCustom($this->validateCustomFilters($request->input('custom_filters')));

                $posts->FilterStatus($request->status);

                $data_contact= $posts->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                return view('admin.contactp.loadcontact',['posts' => $data_contact,'permission' => $permission]);
            }


            if ($contactsaveadminfilter) {

                $country_id = $contactsaveadminfilter->country_id ? explode(",",$contactsaveadminfilter->country_id):'';
                $city_id = $contactsaveadminfilter->city_id ? explode(",",$contactsaveadminfilter->city_id) :'';
                $groupm_id = $contactsaveadminfilter->group_id ? explode(",",$contactsaveadminfilter->group_id) :'';
                $businesstype_id = $contactsaveadminfilter->businesstype_id ?explode(",",$contactsaveadminfilter->businesstype_id):'';
                $industry_id = $contactsaveadminfilter->industry_id ?explode(",",$contactsaveadminfilter->industry_id): '';
                $lcs_id = $contactsaveadminfilter->lcs_id ? explode(",",$contactsaveadminfilter->lcs_id):'';
                $ls_id = $contactsaveadminfilter->ls_id ? explode(",",$contactsaveadminfilter->ls_id):'';
                $send_tag = $contactsaveadminfilter->sned_tag ? explode(",",$contactsaveadminfilter->sned_tag) :'';
                $create_by = $contactsaveadminfilter->created_by ? explode(",",$contactsaveadminfilter->created_by): "";
                $subscribe = $contactsaveadminfilter->subscribe != '' ? explode(",", $contactsaveadminfilter->subscribe) : '';


                $posts->FilterCountry($country_id)
                ->FilterCity($city_id)
                ->FilterLcs($lcs_id)
                ->FilterLs($ls_id)
                ->FilterBusinesstype($businesstype_id)
                ->FilterIndustry($industry_id)
                ->FilterGroup($groupm_id)
                ->FilterCreatedBy($create_by)
                ->FilterSendTag($send_tag)
                ->FilterSubscribe($subscribe)
                ->FilterDate('send_date',$contactsaveadminfilter->send_date)
                ->FilterDateRange('update_lead_status_date',$contactsaveadminfilter->update_status_date)
                ->FilterDate('updated_at',$contactsaveadminfilter->updated_date)
                ->FilterDate('created_at',$contactsaveadminfilter->created_date)
                ->FilterCustom($this->validateCustomFilters($contactsaveadminfilter->custom_filters));
            }

            $statusCounts = $this->contactStatusCounts($posts);
            $leadStageCounts = $this->contactLeadStageCounts($posts);

            $status = isset($contactsaveadminfilter) && $contactsaveadminfilter->status != '' ? explode(",", $contactsaveadminfilter->status) : '';
            $posts->FilterStatus($status);

            $data_contact= $posts->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();


            return view('admin.contactp.index',['industries' => $industries,'posts' => $data_contact,'contactsaveadminfilter' => $contactsaveadminfilter,'countries' => $countries,'cities' => $cities,'groupms' => $groupms,'lcss' => $lcss,'metatemplates' => $metatemplates,'normaltemplates' => $normaltemplates,'wapis' => $wapis,'metawhatsappAPIs' => $metawhatsappAPIs,'businesstypes' => $businesstypes,'permission' => $permission,'adminusers' => $adminusers,'contactpfilter' => $contactpfilter,'statusCounts' => $statusCounts,'leadStages' => $leadStages,'leadStageCounts' => $leadStageCounts]);



        }elseif (isset($permission) && $permission->full_access == 0) {
           if ($permission->view_contactp == 1) {
                $posts = Contactplus::with(['country','city','contactstatus','ls','lcs','businesstype','groupm','group']);

                if ($request->ajax()) {
                    $posts->FilterCountry($request->country_id)
                    ->FilterCity($request->city_id)
                    ->FilterLcs($request->lcs_id)
                    ->FilterLs($request->ls_id)
                    ->FilterBusinesstype($request->businesstype_id)
                    ->FilterIndustry($request->industries)
                    ->FilterGroup($request->group_id)
                    ->FilterCreatedBy($request->created_by)
                    ->FilterSendTag($request->send_tag)
                    ->FilterSubscribe($request->subscribe)
                    ->FilterDate('send_date',$request->send_date)
                    ->FilterDateRange('update_lead_status_date',$request->update_lead_status_date)
                    ->FilterDate('created_at',$request->created_at)
                    ->FilterDate('updated_at',$request->updated_at)
                    ->FilterSearchText($request->search_text)
                ->FilterCustom($this->validateCustomFilters($request->input('custom_filters')));

                    $posts->FilterStatus($request->status);

                    $data_contact= $posts->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                    return view('admin.contactp.loadcontact',['posts' => $data_contact,'permission' => $permission]);
                }

                if ($contactsaveadminfilter) {
                    $country_id = $contactsaveadminfilter->country_id ? explode(",",$contactsaveadminfilter->country_id):'';
                    $city_id = $contactsaveadminfilter->city_id ? explode(",",$contactsaveadminfilter->city_id) :'';
                    $groupm_id = $contactsaveadminfilter->group_id ? explode(",",$contactsaveadminfilter->group_id) :'';
                    $businesstype_id = $contactsaveadminfilter->businesstype_id ?explode(",",$contactsaveadminfilter->businesstype_id):'';
                    $industry_id = $contactsaveadminfilter->industry_id ?explode(",",$contactsaveadminfilter->industry_id): '';
                    $lcs_id = $contactsaveadminfilter->lcs_id ? explode(",",$contactsaveadminfilter->lcs_id):'';
                    $ls_id = $contactsaveadminfilter->ls_id ? explode(",",$contactsaveadminfilter->ls_id):'';
                    $send_tag = $contactsaveadminfilter->sned_tag ? explode(",",$contactsaveadminfilter->sned_tag) :'';
                    $create_by = $contactsaveadminfilter->created_by ? explode(",",$contactsaveadminfilter->created_by): "";
                    $subscribe = $contactsaveadminfilter->subscribe != '' ? explode(",", $contactsaveadminfilter->subscribe) : '';

                    $posts->FilterCountry($country_id)
                    ->FilterCity($city_id)
                    ->FilterLcs($lcs_id)
                    ->FilterLs($ls_id)
                    ->FilterBusinesstype($businesstype_id)
                    ->FilterIndustry($industry_id)
                    ->FilterGroup($groupm_id)
                    ->FilterCreatedBy($create_by)
                    ->FilterSendTag($send_tag)
                    ->FilterSubscribe($subscribe)
                    ->FilterDate('send_date',$contactsaveadminfilter->send_date)
                    ->FilterDateRange('update_lead_status_date',$contactsaveadminfilter->update_status_date)
                    ->FilterDate('updated_at',$contactsaveadminfilter->updated_date)
                    ->FilterDate('created_at',$contactsaveadminfilter->created_date)
                ->FilterCustom($this->validateCustomFilters($contactsaveadminfilter->custom_filters));
                }


                $statusCounts = $this->contactStatusCounts($posts);
                $leadStageCounts = $this->contactLeadStageCounts($posts);

                $status = isset($contactsaveadminfilter) && $contactsaveadminfilter->status != '' ? explode(",", $contactsaveadminfilter->status) : '';
                $posts->FilterStatus($status);

                $data_contact= $posts->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                return view('admin.contactp.index',['industries' => $industries,'posts' => $data_contact,'contactsaveadminfilter' => $contactsaveadminfilter,'countries' => $countries,'cities' => $cities,'groupms' => $groupms,'lcss' => $lcss,'metatemplates' => $metatemplates,'normaltemplates' => $normaltemplates,'wapis' => $wapis,'metawhatsappAPIs' => $metawhatsappAPIs,'businesstypes' => $businesstypes,'permission' => $permission,'adminusers' => $adminusers,'contactpfilter' => $contactpfilter,'statusCounts' => $statusCounts,'leadStages' => $leadStages,'leadStageCounts' => $leadStageCounts]);


           }else{
                $posts = Contactplus::with(['country','city','contactstatus','ls','lcs','businesstype','groupm','group'])->where('careoff_id','=',Auth::guard('admin')->user()->id);


                if ($request->ajax()) {
                    $posts->FilterCountry($request->country_id)
                    ->FilterCity($request->city_id)
                    ->FilterLcs($request->lcs_id)
                    ->FilterLs($request->ls_id)
                    ->FilterBusinesstype($request->businesstype_id)
                    ->FilterIndustry($request->industries)
                    ->FilterGroup($request->group_id)
                    ->FilterCreatedBy($request->created_by)
                    ->FilterSendTag($request->send_tag)
                    ->FilterSubscribe($request->subscribe)
                    ->FilterDate('send_date',$request->send_date)
                    ->FilterDateRange('update_lead_status_date',$request->update_lead_status_date)
                    ->FilterDate('created_at',$request->created_at)
                    ->FilterDate('updated_at',$request->updated_at)
                    ->FilterSearchText($request->search_text)
                ->FilterCustom($this->validateCustomFilters($request->input('custom_filters')));

                    $posts->FilterStatus($request->status);

                    $data_contact= $posts->where('careoff_id','=',Auth::guard('admin')->user()->id)->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                    return view('admin.contactp.loadcontact',['posts' => $data_contact,'permission' => $permission]);
                }


                if ($contactsaveadminfilter) {

                    $country_id = $contactsaveadminfilter->country_id ? explode(",",$contactsaveadminfilter->country_id):'';
                    $city_id = $contactsaveadminfilter->city_id ? explode(",",$contactsaveadminfilter->city_id) :'';
                    $groupm_id = $contactsaveadminfilter->group_id ? explode(",",$contactsaveadminfilter->group_id) :'';
                    $businesstype_id = $contactsaveadminfilter->businesstype_id ?explode(",",$contactsaveadminfilter->businesstype_id):'';
                    $industry_id = $contactsaveadminfilter->industry_id ?explode(",",$contactsaveadminfilter->industry_id): '';
                    $lcs_id = $contactsaveadminfilter->lcs_id ? explode(",",$contactsaveadminfilter->lcs_id):'';
                    $ls_id = $contactsaveadminfilter->ls_id ? explode(",",$contactsaveadminfilter->ls_id):'';
                    $send_tag = $contactsaveadminfilter->sned_tag ? explode(",",$contactsaveadminfilter->sned_tag) :'';
                    $create_by = $contactsaveadminfilter->created_by ? explode(",",$contactsaveadminfilter->created_by): "";
                    $subscribe = $contactsaveadminfilter->subscribe != '' ? explode(",", $contactsaveadminfilter->subscribe) : '';

                    $posts->FilterCountry($country_id)
                    ->FilterCity($city_id)
                    ->FilterLcs($lcs_id)
                    ->FilterLs($ls_id)
                    ->FilterBusinesstype($businesstype_id)
                    ->FilterIndustry($industry_id)
                    ->FilterGroup($groupm_id)
                    ->FilterCreatedBy($create_by)
                    ->FilterSendTag($send_tag)
                    ->FilterSubscribe($subscribe)
                    ->FilterDate('send_date',$contactsaveadminfilter->send_date)
                    ->FilterDateRange('update_lead_status_date',$contactsaveadminfilter->update_status_date)
                    ->FilterDate('updated_at',$contactsaveadminfilter->updated_date)
                    ->FilterDate('created_at',$contactsaveadminfilter->created_date)
                ->FilterCustom($this->validateCustomFilters($contactsaveadminfilter->custom_filters));
                }

                $statusCounts = $this->contactStatusCounts($posts);
                $leadStageCounts = $this->contactLeadStageCounts($posts);

                $status = isset($contactsaveadminfilter) && $contactsaveadminfilter->status != '' ? explode(",", $contactsaveadminfilter->status) : '';
                $posts->FilterStatus($status);

                $data_contact = $posts->where('careoff_id','=',Auth::guard('admin')->user()->id)->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                return view('admin.contactp.index',['industries' => $industries,'posts' => $data_contact,'contactsaveadminfilter' => $contactsaveadminfilter,'countries' => $countries,'cities' => $cities,'groupms' => $groupms,'lcss' => $lcss,'metatemplates' => $metatemplates,'normaltemplates' => $normaltemplates,'wapis' => $wapis,'metawhatsappAPIs' => $metawhatsappAPIs,'businesstypes' => $businesstypes,'permission' => $permission,'adminusers' => $adminusers,'contactpfilter' => $contactpfilter,'statusCounts' => $statusCounts,'leadStages' => $leadStages,'leadStageCounts' => $leadStageCounts]);


            }
        }


        // $posts = Contactplus::with(['country','city','contactstatus','ls'])
        //     // ->select('id','office_eng_name','prim_concern_name','status','country_id','city_id','cstatus_id')
        //     ->orderBy('id','DESC')
        //     ->paginate(10)
        //     ->withQueryString();

        // if ($request->ajax()) {
        //     return view('admin.contactp.loadcontact',['posts' => $data_contact])->render();
        // }

        // return view('admin.contactp.index',['posts' => $data_contact,'contactsaveadminfilter' => $contactsaveadminfilter,'countries' => $countries,'cities' => $cities,'groupms' => $groupms,'lcss' => $lcss,'metatemplates' => $metatemplates,'normaltemplates' => $normaltemplates,'wapis' => $wapis,'metawhatsappAPIs' => $metawhatsappAPIs,'businesstypes' => $businesstypes,'permission' => $permission,'adminusers' => $adminusers,'contactpfilter' => $contactpfilter]);
    }

    /**
     * Status counts (Total / Active / Inactive) for the Contact+ status bar,
     * computed against $query as already filtered (every active filter
     * except FilterStatus itself) so the pills reflect the currently
     * applied filters instead of the whole table.
     */
    private function contactStatusCounts($query)
    {
        return [
            'total'    => (clone $query)->count(),
            'active'   => (clone $query)->where('status', 1)->count(),
            'inactive' => (clone $query)->where('status', 0)->count(),
        ];
    }

    /**
     * Contact counts grouped by Lead Stage (ls_id) for the Contact+ status
     * bar, computed against $query as already filtered (every active filter)
     * so the pills reflect the currently applied filters instead of the
     * whole table. Keyed by ls_id => count; stages with no matching contacts
     * simply have no key.
     */
    private function contactLeadStageCounts($query)
    {
        return (clone $query)
            ->whereNotNull('ls_id')
            ->where('ls_id', '!=', '')
            ->select('ls_id', DB::raw('count(*) as aggregate'))
            ->groupBy('ls_id')
            ->pluck('aggregate', 'ls_id');
    }

    public function index(){
        $countries = Country::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        $lcss = Lifecyclestatus::orderBy('id')->get();

        $adminusers = Admin::where('status', 1)->orderBy('name')->get();

        $businesstypes = Businesstype::orderBy('name')->get();
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        $contactpfilter = Contactpfilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

        $metatemplates = Metawhatsapptemplate::where('status','=',1)->get();
        $normaltemplates = Whatsappcamptemplate::where('status','=',1)->get();
        // $metawhatsappAPIs = Metawhatsappapi::where('status','=',1)->get();
        $metawhatsappAPIs = Metawhatsappapi::orderBy('api_name')->whereRaw("FIND_IN_SET (?,api_assign_to)",['campaign'])->get();
        $wapis = Whatsappapi::where('api_for','=','campaign_not')->orderBy('id','DESC')->get();



        $groupms = Groupm::orderBy('name','ASC')->get();

        return view('admin.contactp.index',compact('countries','cities','groupms','lcss','metatemplates','normaltemplates','wapis','metawhatsappAPIs','businesstypes','permission','adminusers','contactpfilter'));
    }

    public function indexJson(Request $request){

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        if(Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)){
            $post = DB::table('contactpluses as contactp')
            ->leftJoin('countries as country','contactp.country_id','=','country.id')
            ->leftJoin('cities as city','contactp.city_id','=','city.id')
            ->leftJoin('contactstatuses as contactstatus','contactp.cstatus_id','=','contactstatus.id')
            ->leftJoin('admins as admin','contactp.staff_id','=','admin.id')
            ->leftJoin('leadstages as leadstage','leadstage.id','=','contactp.ls_id')
            ->select('contactp.*','country.name as cname','city.name as citname','contactstatus.name as statusname','admin.name as uname','leadstage.name as lsname')
            ->orderBy('contactp.id','DESC')
            ->get();
        }elseif(Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_contactp == 1){
                $post = DB::table('contactpluses as contactp')
                ->leftJoin('countries as country','contactp.country_id','=','country.id')
                ->leftJoin('cities as city','contactp.city_id','=','city.id')
                ->leftJoin('contactstatuses as contactstatus','contactp.cstatus_id','=','contactstatus.id')
                ->leftJoin('admins as admin','contactp.staff_id','=','admin.id')
                ->leftJoin('leadstages as leadstage','leadstage.id','=','contactp.ls_id')
                ->select('contactp.*','country.name as cname','city.name as citname','contactstatus.name as statusname','admin.name as uname','leadstage.name as lsname')
                ->orderBy('contactp.id','DESC')
                ->get();
            }else{
                $post = DB::table('contactpluses as contactp')
                ->leftJoin('countries as country','contactp.country_id','=','country.id')
                ->leftJoin('cities as city','contactp.city_id','=','city.id')
                ->leftJoin('contactstatuses as contactstatus','contactp.cstatus_id','=','contactstatus.id')
                ->leftJoin('admins as admin','contactp.staff_id','=','admin.id')
                ->leftJoin('leadstages as leadstage','leadstage.id','=','contactp.ls_id')
                ->select('contactp.*','country.name as cname','city.name as citname','contactstatus.name as statusname','admin.name as uname','leadstage.name as lsname')
                // ->where('contactp.staff_id','=',Auth::guard('admin')->user()->id)
                ->where('contactp.careoff_id','=',Auth::guard('admin')->user()->id)
                ->orderBy('contactp.id','DESC')
                ->get();
            }
        }



        $data['data'] = $post;

        return response()->json($data);

    }

    public function bulkExport(Request $request)
    {
        $contactIds = !empty($request->bulkcontactp_id)
            ? explode(',', $request->bulkcontactp_id)
            : [];

        $requestedColumns = $request->columns ?? [];

        $exportMap = $this->contactPlusExportService->exportColumnMap();

        $columns = array_values(array_intersect(
            $requestedColumns,
            array_keys($exportMap)
        ));

        if (empty($columns)) {
            return redirect()->back()->with('error', 'No valid columns selected.');
        }

        $history = ExportContactPlusHistory::create([
            'admin_id'         => auth()->id(),
            'columns'          => $columns,
            'contact_ids'      => implode(',', $contactIds),
            'is_all_export'    => $request->boolean('is_all_export'),
            'status'           => 'pending',
            'total_records'    => 0,
            'exported_records' => 0,
        ]);

        ExportContactPlusHistoryJob::dispatch($history->id,Auth::guard('admin')->user()->id);

        return redirect('admin/contact-plus-export-history')
            ->with(
                'success',
                'Export has been queued successfully. You can continue using the system while the export is generated.'
            );
    }

    // public function bulkExport(Request $request)
    // {
    //     // 1️⃣ Selected Contact IDs
    //     $bulkcontactp_id = explode(',', $request->bulkcontactp_id);
    
    //     // 2️⃣ Columns selected from frontend
    //     $requestedColumns = $request->columns ?? [];
    
    //     // 3️⃣ Exportable column map
    //     $exportMap = $this->exportColumnMap();
    
    //     // 4️⃣ Keep only valid export keys (preserve frontend order)
    //     $columns = array_values(array_filter(
    //         $requestedColumns,
    //         fn ($col) => array_key_exists($col, $exportMap)
    //     ));
    
    //     if (empty($columns)) {
    //         abort(400, 'No valid columns selected');
    //     }
    
    //     // 5️⃣ Fetch contacts with relations (NO N+1)
    //     $contactp = Contactplus::with([
    //             'ls',
    //             'city',
    //             'country',
    //             'group',
    //             'contactstatus'
    //         ])
    //         ->whereIn('id', $bulkcontactp_id)
    //         ->get();
    
    //     // 6️⃣ Create CSV file
    //     $fileName = 'contactp_export_' . time() . '.csv';
    //     $filePath = public_path($fileName);
    
    //     $handle = fopen($filePath, 'w');
    
    //     // ✅ UTF-8 BOM (Arabic / Hindi / Excel safe)
    //     fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
    
    //     // 7️⃣ Header row
    //     fputcsv($handle, $this->getExportHeaders($columns));
    
    //     // 8️⃣ Data rows
    //     foreach ($contactp as $contact) {
    
    //         // Get mapped row (with original keys)
    //         $row = $this->mapContactpRow($contact, $columns);
    
    //         // ✅ Force Excel text for CONTACT numbers
    //         if (isset($row[6]) && $row[6] !== '---' && $row[6] != '' ) { // Primary Contact
    //             $row[6] = '="' . $row[6] . '"';
    //         }else{
    //              $row[6] = '---';
    //         }
    
    //         if (isset($row[7]) && $row[7] !== '---' && $row[7] != '') { // Secondary Contact
    //             $row[7] = '="' . $row[7] . '"';
    //         }else{
    //              $row[7] = '---';
    //         }
    
    //         if (isset($row[8]) && $row[8] !== '---' && $row[8] != '') { // Contact No3
    //             $row[8] = '="' . $row[8] . '"';
    //         }else{
    //              $row[8] = '---';
    //         }
    
    //         if (isset($row[9]) && $row[9] !== '---' && $row[9] != '') { // Contact No4
    //             $row[9] = '="' . $row[9] . '"';
    //         }else{
    //              $row[9] = '---';
    //         }
    
    //         // Write CSV row
    //         fputcsv($handle, array_values($row));
    //     }
    
    //     fclose($handle);
    
    //     // 9️⃣ Download & auto delete
    //     return response()->download($filePath, $fileName, [
    //         'Content-Type' => 'text/csv; charset=UTF-8',
    //     ])->deleteFileAfterSend(true);
    // }
    
    // private function mapContactpRow($contactp, array $columns): array
    // {
    //     $map = [
    //         1  => $contactp->prim_concern_name ?? '---',
    //         2  => $contactp->office_eng_name ?? '---',
    //         3  => $contactp->office_ar_name ?? '---',
    //         4  => $contactp->prim_email ?? '---',
    //         5  => $contactp->sec_email ?? '---',
    //         6  => $contactp->prim_contact ?? '---',
    //         7  => $contactp->sec_contact ?? '---',
    //         8  => $contactp->contact3 ?? '---',
    //         9  => $contactp->contact4 ?? '---',

    //         10 => optional($contactp->ls)->name ?? 'None',
    //         11 => optional($contactp->city)->name ?? '---',
    //         12 => optional($contactp->country)->name ?? '---',
    //         13 => optional($contactp->group)->name ?? '---',
    //         14 => optional($contactp->contactstatus)->name ?? 'None',

    //         15 => $contactp->status == 1 ? 'Active' : 'Inactive',
    //     ];
    
    //     // ✅ Return ONLY selected columns (preserve keys & order)
    //     return array_intersect_key($map, array_flip($columns));
    // }
    
    // private function exportColumnMap(): array
    // {
    //     return [
    //         1  => 'Full Name',
    //         2  => 'Office English Name',
    //         3  => 'Office Arabic Name',
    //         4  => 'Primary Email',
    //         5  => 'Secondary Email',
    //         6  => 'Primary Contact',
    //         7  => 'Secondary Contact',
    //         8  => 'Contact No. 3',
    //         9  => 'Contact No. 4',
    //         10 => 'Lead Stage',
    //         11 => 'City',
    //         12 => 'Country',
    //         13 => 'Group',
    //         14 => 'Work',
    //         15 => 'Status',
    //     ];
    // }
    

    // private function getExportHeaders(array $columns): array
    // {
    //     $map = $this->exportColumnMap();
    
    //     return array_map(
    //         fn ($col) => $map[$col],
    //         $columns
    //     );
    // }
    

    public function store(Request $request){
        // Group ID ASSIGN
        $getGroups = Contactplus::where('group_id','!=','')->get();

        $isRecruitmentAgency = optional(Businesstype::find($request->businesstype_id))->name === 'Recruitment Agency';

        $request->validate([
            'licence_number' => $isRecruitmentAgency ? 'required|string|max:255' : 'nullable|string|max:255',
        ]);

        $post = new Contactplus();
        $post->office_eng_name = $request->office_eng_name;
        $post->office_ar_name = $request->office_ar_name;
        $post->office_no = $request->office_no;
        $post->office_email = $request->office_email;
        $post->owner_name = $request->owner_name;
        $post->owner_contact = $request->owner_contact;
        $post->owenr_email = $request->owenr_email;
        $post->country_id = $request->country_id;
        $post->licence_number = $isRecruitmentAgency ? $request->licence_number : null;
        $post->city_id = $request->city_id;
        $post->prim_concern_name = $request->prim_concern_name;
        $post->prim_contact = str_replace(" ","",$request->prim_contact);
        $post->prim_email = $request->prim_email;
        $post->sec_concern_name = $request->sec_concern_name;
        $post->sec_contact = str_replace(" ","",$request->sec_contact);
        $post->sec_email = $request->sec_email;
        $post->concern_name3 = $request->concern_name3;
        $post->contact3 = str_replace(" ","",$request->contact3);
        $post->concern_name4 = $request->concern_name4;
        $post->contact4 = str_replace(" ","",$request->contact4);
        $post->concern_name5 = $request->concern_name5;
        $post->contact5 = str_replace(" ","",$request->contact5);
        $post->concern_name6 = $request->concern_name6;
        $post->contact6 = str_replace(" ","",$request->contact6);
        $post->contact7 = str_replace(" ","",$request->contact7);
        $post->contact8 = str_replace(" ","",$request->contact8);
        $post->contact9 = str_replace(" ","",$request->contact9);
        $post->contact10 = str_replace(" ","",$request->contact10);
        $post->contact11 = str_replace(" ","",$request->contact11);
        $post->contact12 = str_replace(" ","",$request->contact12);
        $post->lcs_id = '1';
        $post->ls_id = '1';
        $post->businesstype_id = $request->businesstype_id;
        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->careoff_id = $request->careoff_id;
        $post->leadowner_id = $request->leadowner_id;
        $post->industry_id = $request->industry_id;
        // if($getGroups->count() > 0){
        //     // Associate as per group
        //     $groupMs = Groupm::orderBy('name','ASC')->get();
        //     $ic = 0;

        //     foreach($groupMs as $groupM){
        //         if($ic < 1){
        //             $getAllc = Contactplus::where('group_id','=',$groupM->id)->count();
        //             // check if maximaum limit is greater than specific count
        //             if($getAllc < $groupM->max_limit){
        //                 $post->group_id = $groupM->id;
        //                 $ic++;
        //             }
        //         }
        //     }

        // }else{
        //     $groupM = Groupm::orderBy('name','ASC')->first();
        //     if($groupM){
        //         $post->group_id = $groupM->id;
        //     }
        // }

        // $post->group_id = "0";

        $post->save();

        // return redirect()->back()->with('success','Contact added!');
        return response()->json(['success' => 'Contact added!']);
    }

    public function shortstore(Request $request){
        // Group ID ASSIGN
        $getGroups = Contactplus::where('leadowner_id','=',$request->leadowner_id)->where('group_id','!=','')->get();

        $post = new Contactplus();

        $post->lcs_id = '1';
        $post->ls_id = '1';
        $post->businesstype_id = $request->businesstype_id;
        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->careoff_id = Auth::guard('admin')->user()->id;
        $post->leadowner_id = Auth::guard('admin')->user()->id;
        $post->prim_concern_name = $request->prim_concern_name;
        $post->prim_contact = str_replace(" ","",$request->prim_contact);
        $post->prim_email = $request->prim_email;
        
        $post->country_id = $request->country_id;

        Contactplusadminsavefilter::where('admin_id', Auth::guard('admin')->user()->id)->update([
            'last_business_type_id' => $request->businesstype_id,
            'last_country_id' => $request->country_id
        ]);
        
        // if($getGroups->count() > 0){
        //     // Associate as per group
        //     $groupMs = Groupm::orderBy('name','ASC')->get();
        //     $ic = 0;

        //     foreach($groupMs as $groupM){
        //         if($ic < 1){
        //             $getAllc = Contactplus::where('group_id','=',$groupM->id)->where('leadowner_id','=',$request->leadowner_id)->count();
        //             // check if maximaum limit is greater than specific count
        //             if($getAllc < $groupM->max_limit){
        //                 $post->group_id = $groupM->id;
        //                 $ic++;
        //             }
        //         }
        //     }

        // }else{
        //     $groupM = Groupm::orderBy('name','ASC')->first();
        //     if($groupM){
        //         $post->group_id = $groupM->id;
        //     }
        // }

        // $post->group_id = "0";

        $post->save();

        // return redirect()->back()->with('success','Contact added!');
        return response()->json(['success' => 'Contact added!']);
    }


    public function companyDataStore(Request $request)
    {
        $jsonString = $request->raw_company_text;
    
        $data = json_decode($jsonString, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid JSON format'
            ], 422);
        }
    
        // ✅ VALIDATION (IMPORTANT)
        $validator = Validator::make($data, [
            'membership_number' => 'required|unique:contactpluses,membership_number'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Membership number already exists!'
            ], 422);
        }
    
        // ✅ CREATE RECORD
        $post = new Contactplus();
    
        $post->office_eng_name = $data['company_name'] ?? null;
        $post->membership_number = $data['membership_number'] ?? null;
        $post->company_size = $data['company_size'] ?? null;
        $post->office_no = $data['mobile'] ?? null;
        $post->office_email = $data['email'] ?? null;
    
        $post->prim_concern_name = null;
        $post->prim_contact = isset($data['mobile']) 
            ? str_replace(" ", "", $data['mobile']) 
            : null;
    
        $post->prim_email = $data['email'] ?? null;
    
        $post->lcs_id = '1';
        $post->ls_id = '1';
        $post->staff_id = Auth::guard('admin')->user()->id;
        $post->careoff_id = Auth::guard('admin')->user()->id;
        $post->leadowner_id = Auth::guard('admin')->user()->id;
    
        // Country
        if (!empty($data['country'])) {
            $country = \App\Models\Country::where('name', $data['country'])->first();
            $post->country_id = $country->id ?? null;
        }
    
        // City
        if (!empty($data['city'])) {
            $city = \App\Models\City::where('name', $data['city'])->first();
            $post->city_id = $city->id ?? null;
        }
    
        $post->businesstype_id = 3;
    
        $post->save();
    
        return response()->json([
            'status' => true,
            'message' => 'Contact added successfully'
        ]);
    }

    public function edit(Request $request){
        $post = Contactplus::find($request->id);

        return response()->json($post);
    }

    public function update(Request $request){
        // Group ID ASSIGN
        $getGroups = Contactplus::where('group_id','!=','')->get();

        $isRecruitmentAgency = optional(Businesstype::find($request->businesstype_id))->name === 'Recruitment Agency';

        $request->validate([
            'licence_number' => $isRecruitmentAgency ? 'required|string|max:255' : 'nullable|string|max:255',
        ]);

        $post = Contactplus::find($request->editID);
        $post->office_eng_name = $request->office_eng_name;
        $post->office_ar_name = $request->office_ar_name;
        $post->office_no = $request->office_no;
        $post->office_email = $request->office_email;
        $post->owner_name = $request->owner_name;
        $post->owner_contact = str_replace(" ","",$request->owner_contact);
        $post->owenr_email = $request->owenr_email;
        $post->country_id = $request->country_id;
        // Only touch licence_number when Business Type is Recruitment Agency so that
        // switching away and back to another type does not wipe an existing licence.
        if ($isRecruitmentAgency) {
            $post->licence_number = $request->licence_number;
        }
        $post->city_id = $request->city_id;
        $post->prim_concern_name = $request->prim_concern_name;
        $post->prim_contact = str_replace(" ","",$request->prim_contact);
        $post->prim_email = $request->prim_email;
        $post->sec_concern_name = $request->sec_concern_name;
        $post->sec_contact = str_replace(" ","",$request->sec_contact);
        $post->sec_email = $request->sec_email;
        $post->concern_name3 = $request->concern_name3;
        $post->contact3 = str_replace(" ","",$request->contact3);
        $post->concern_name4 = $request->concern_name4;
        $post->contact4 = str_replace(" ","",$request->contact4);
        $post->concern_name5 = $request->concern_name5;
        $post->contact5 = str_replace(" ","",$request->contact5);
        $post->concern_name6 = $request->concern_name6;
        $post->contact6 = str_replace(" ","",$request->contact6);
        $post->contact7 = str_replace(" ","",$request->contact7);
        $post->contact8 = str_replace(" ","",$request->contact8);
        $post->contact9 = str_replace(" ","",$request->contact9);
        $post->contact10 = str_replace(" ","",$request->contact10);
        $post->contact11 = str_replace(" ","",$request->contact11);
        $post->contact12 = str_replace(" ","",$request->contact12);
        if ($post->lcs_id == '') {
            $post->lcs_id = '1';
        }
        if ($post->ls_id == '') {
            $post->ls_id = '1';
        }
        $post->businesstype_id = $request->businesstype_id;
        $post->careoff_id = $request->careoff_id;
        $post->leadowner_id = $request->leadowner_id;
        $post->industry_id = $request->industry_id;

        // if($post->group_id == ''){
        //     if($getGroups->count() > 0){
        //         // Associate as per group
        //         $groupMs = Groupm::orderBy('name','ASC')->get();
        //         $ic = 0;

        //         foreach($groupMs as $groupM){
        //             if($ic < 1){
        //                 $getAllc = Contactplus::where('group_id','=',$groupM->id)->count();
        //                 // check if maximaum limit is greater than specific count
        //                 if($getAllc < $groupM->max_limit){
        //                     $post->group_id = $groupM->id;
        //                     $ic++;
        //                 }
        //             }
        //         }

        //     }else{
        //         $groupM = Groupm::orderBy('name','ASC')->first();
        //         if($groupM){
        //             $post->group_id = $groupM->id;
        //         }
        //     }
        // }

        $post->save();

        // return redirect()->back()->with('success','Contact updated!');

        return response()->json(['success' => 'Contact Updated!']);
    }

    public function checkGrpLimit(Request $request){
        $ids = explode(",",$request->totalSend);
        $group = Groupm::find($request->grpID);
        $total_send = count($ids);
        $allcontacts = Contactplus::where('group_id','=',$request->grpID)->count();
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

    public function show($id){
        $post = DB::table('contactpluses as contactp')
            ->leftjoin('businesstypes as busttype','contactp.businesstype_id','=','busttype.id')
            ->leftJoin('countries as country','contactp.country_id','=','country.id')
            ->leftJoin('cities as city','contactp.city_id','=','city.id')
            ->leftJoin('contactstatuses as contactstatus','contactp.cstatus_id','=','contactstatus.id')
            ->leftJoin('admins as admin','contactp.staff_id','=','admin.id')
            ->leftJoin('admins as admin2','contactp.careoff_id','=','admin2.id')
            ->leftJoin('admins as admin3','contactp.leadowner_id','=','admin3.id')
            ->leftJoin('lifecyclestatuses as lcs','lcs.id','=','contactp.lcs_id')
            ->leftjoin('leadstages as lst','lst.id','=','contactp.ls_id')
            ->leftjoin('groupms as groupm','contactp.group_id','=','groupm.id')
            ->select('contactp.*','country.name as contname','city.name as citname','contactstatus.name as contstatus','admin.name as uname','admin2.name as careoffname','admin3.name as leadownername','lcs.name as lcsname','lst.name as lsname','busttype.name as businesstype','groupm.name as groupname')
            ->where('contactp.id','=',$id)
            ->first();

        $wapis = Whatsappapi::where('api_for','=','campaign_not')->orderBy('id','DESC')->get();
        $wtemps = Whatsappcamptemplate::orderBy('id','DESC')->get();

        $countries = Country::orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        $businesstypes = Businesstype::orderBy('name')->get();

        $lcsds = Lifecyclestatus::orderby('name')->get();

        $leadownerlist = Admin::where('status', 1)->where('id','!=',$post->leadowner_id)->orderBy('name')->get();
        $careofflist = Admin::where('status', 1)->where('id','!=',$post->careoff_id)->orderBy('name')->get();

        // $contactnotes = Contactpnotes::orderBy('id','DESC')->where('contactp_id','=',$id)->get();
        $contactnotes = DB::table('contactpnotes as contactpnote')
            ->leftjoin('admins as admin','admin.id','=','contactpnote.admin_id')
            ->select('contactpnote.*','admin.name as uname')
            ->where('contactpnote.contactp_id','=',$id)
            ->get();

        $reminders2 = DB::table('contactreminders as contactreminder')
            ->leftjoin('admins as admin','admin.id','=','contactreminder.admin_id')
            ->leftJoin('admins as admin2','admin2.id','=','contactreminder.careoff_id')
            ->select('contactreminder.*','admin.name as uname','admin2.name as careoffname')
            ->where('contactreminder.contactp_id','=',$id)
            ->orderBy('contactreminder.id','DESC')
            ->get();

        $metatemplates = Metawhatsapptemplate::where('status','=',1)->get();
        $normaltemplates = Whatsappcamptemplate::where('status','=',1)->get();
        // $metawhatsappAPIs = Metawhatsappapi::where('status','=',1)->get();
        $metawhatsappAPIs = Metawhatsappapi::orderBy('api_name')->whereRaw("FIND_IN_SET (?,api_assign_to)",['campaign'])->get();

        // $sendcontactmsgs = Contactsendwhatsapp::where('contactp_id','=',$id)->get();
        $sendcontactmsgs = DB::table('contactsendwhatsapps as contactsendwhatsapp')
            ->leftjoin('metawhatsapptemplates as metawhatsapptemp','metawhatsapptemp.id','=','contactsendwhatsapp.metatemplate_id')
            ->leftjoin('whatsappcamptemplates as whatsappcamptemp','whatsappcamptemp.id','=','contactsendwhatsapp.template_id')
            ->leftjoin('admins as admin','admin.id','=','contactsendwhatsapp.admin_id')
            ->select('contactsendwhatsapp.*','metawhatsapptemp.template_name as metatempname','whatsappcamptemp.template_name as ntemp_name','admin.name as adminname')
            ->where('contactsendwhatsapp.contactp_id','=',$id)
            ->orderBy('contactsendwhatsapp.id','DESC')
            ->get();

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        $admins = Admin::where('status', 1)->orderBy('name')->get();





        return view('admin.contactp.show',compact('post','admins','permission','sendcontactmsgs','metawhatsappAPIs','normaltemplates','metatemplates','wapis','wtemps','countries','cities','businesstypes','lcsds','leadownerlist','careofflist','contactnotes','reminders2'));
    }



    public function unsubscribereport(){
        $posts = Contactplus::where('subscribe','=',0)->orderBy('id','DESC')->get();


        return view('admin.contactp.unsubscribereport',compact('posts'));
    }

    public function updateSubscribe(Request $request){
        $post = Contactplus::find($request->contactID);
        $post->subscribe = $request->subscribe;
        if ($request->subscribe == 0) {
            $post->unsubscribe_date = date('Y-m-d h:i');
        }
        $post->save();

        return redirect()->back()->with('success','Whatsapp subscribe updated!');

    }

    public function getSendContactList(Request $request){
        $posts = Sendwhatsappresponse::where('contactsend_id','=',$request->id)->get();
        $res = '';

        if($posts->count() > 0){
            foreach($posts as $post){
                $res .= '<tr><td>'.$post->name.'</td><td>'.$post->mobile_no.'</td><td>'.$post->message_status.'</td><td>'.$post->message_text.'</td></tr>';
            }
        }else{
            $res .= '<tr><td colspan="4">No Status Found...</td></tr>';
        }

        $data['res'] = $res;

        return response()->json($data);

    }

    public function contactaddreminder(Request $request){
        $post = new Contactreminder();
        $post->contactp_id = $request->contactID;
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

    public function contacteditreminder(Request $request){
        $post = Contactreminder::find($request->id);

        return response()->json($post);
    }

    public function contactreminderupdate(Request $request){
        if ($request->new_reminder == 'Required') {
            $post = new Contactreminder();
            $post->title = $request->title;
            $post->contactp_id = $request->contactID;
            $post->careoff_id = $request->careoff_id;
            $post->desc = $request->desc;
            $post->reminder_type = $request->reminder_type;
            $post->due_date = $request->due_date;
            $post->time = $request->time;
            $post->admin_id = Auth::guard('admin')->user()->id;
            $post->save();

            // Inactive Previous Reminder
            $prepost = Contactreminder::find($request->reminder_id);
            $prepost->status = false;
            $prepost->save();

            return redirect()->back()->with('success','New reminder created!');
        }else{
            $updatePost = Contactreminder::find($request->reminder_id);
            $updatePost->title = $request->title;
            $updatePost->desc = $request->desc;
            $updatePost->save();

            return redirect()->back()->with('success','Reminder updated!');
        }
    }

    public function transferLeadOwner(Request $request){
        $post = Contactplus::find($request->contactID);
        $post->leadowner_id = $request->leadowner_id;
        $post->save();

        // Lead Owner Transfer Activities

        return redirect()->back()->with('success','Lead Owner Transfer Successfully!');

    }

    public function transfercareoff(Request $request){
        $post = Contactplus::find($request->contactID);
        $post->careoff_id = $request->careoff_id;
        $post->save();

        // Lead Owner Transfer Activities

        return redirect()->back()->with('success','Careoff Transfer Successfully!');
    }

    public function getStageList(Request $request){
        // Contact Details
        $cont = Contactplus::find($request->id);

        // Lead Stage List
        $posts = Leadstage::where('leadcyclestatus_id','=',$request->lcs_id)->get();

        $res = '';
        $res .= '<option value=""></option>';
        foreach ($posts as $post) {
            $res .= '<option value="'.$post->id.'"';
            if ($post->id == $cont->ls_id) {
                $res .= 'selected';
            }
            $res .= '>'.$post->name.'</option>';
        }

        $arr['res'] = $res;
        return response()->json($arr);
    }

    public function bulkleadownertransfer(Request $request){
        $ids = explode(",",$request->contactIDLTR);

        // dd($ids);

        foreach($ids as $id){
            $post = Contactplus::find($id);

            $post->leadowner_id = $request->leadowner_id;
            $post->save();
        }

        return redirect()->back()->with('success','Bulk lead owner transfer successfully!');

    }

    public function bulkgrouptransfer(Request $request){
        $ids = explode(",",$request->contactIDGRPTR);

        // dd($ids);

        foreach($ids as $id){
            $post = Contactplus::find($id);

            $post->group_id = $request->group_id;
            $post->save();
        }

        return redirect()->back()->with('success','Bulk Group Transfer successfully!');
    }

    public function bulkcareofftransfer(Request $request){
        $ids = explode(",",$request->contactIDCTR);

        foreach($ids as $id){
            $post = Contactplus::find($id);
            $post->careoff_id = $request->careoff_id;
            $post->save();
        }

        return redirect()->back()->with('success','Bulk careoff transfer successfully!');
    }

    public function bulkdelete(Request $request){
        $ids = explode(",",$request->contactids);

        foreach ($ids as $id) {
            $post = Contactplus::find($id);
            $post->delete();
        }

        return redirect()->back()->with('success','Contactplus Deleted!');
    }

    public function bulksendwhatsapp(Request $request){
        $ids = explode(",",$request->contactpID);

        $groupID = $request->contact_group_id;

        $posts = Contactplus::wherein('id',$ids)->wherein('subscribe',$request->subscribe)->wherein('status',$request->contact_status)->where(function($query) use($groupID){
            if($groupID != ''){
                $query->wherein('group_id',$groupID);
            }
        })->get();
        $total_rec = count($posts);



        if($request->send_whatsapp_type == 'meta_whatsapp'){
            $templateDet = Metawhatsapptemplate::find($request->metatemplate_id);
            // Meta Whatsapp API
            // $metaAPI = Metawhatsappapi::where('status','=',0)->first();
            $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['campaign'])->first();

            // dd($metaAPI);

            if ($posts->count() > 0) {

                foreach ($posts as $post) {
                    // Store Bulk contact detail in contactsendwhatsapps
                    $sendcontact = new Contactsendwhatsapp();
                    $sendcontact->contactp_id = $post->id;
                    $sendcontact->for_whatsapp = $request->send_whatsapp_type;
                    $sendcontact->metatemplate_id = $request->metatemplate_id;
                    $sendcontact->contact_type = implode(",",$request->contact_type);
                    $sendcontact->campaign_type = $request->campaign_type;
                    $sendcontact->send_type = "2";
                    if($request->campaign_type == '2'){
                        $sendcontact->date_time = date('Y-m-d h:i',strtotime($request->date_and_time));
                    }
                    $sendcontact->contact_status = implode(",",$request->contact_status);
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
                                "[Office Name (English)]",
                                "[Office Name (Arabic)]",
                                "[Office Number]",
                                "[Office Email]",
                                "[Owner Name]",
                                "[Owner Contact]",
                                "[Owner Email]",
                                "[Country]",
                                "[City]",
                                "[Primary Concern Person]",
                                "[Primary Contact No]",
                                "[Primary Email]",
                                "[Secondary Concern Person]",
                                "[Secondary Contact No]",
                                "[Secondary Email]",
                                "[Concern Person 3]",
                                "[Contact No 3]",
                                "[Concern Person 4]",
                                "[Contact No 4]",
                                "[Concern Person 5]",
                                "[Contact No 5]",
                                "[Concer Person 6]",
                                "[Contact No 6]",
                                "[Status]",
                                "[Company]"
                            ];

                            $repStr = [
                                "[$post->office_eng_name]",
                                "[$post->office_ar_name]",
                                "[$post->office_no]",
                                "[$post->office_email]",
                                "[$post->owner_name]",
                                "[$post->owner_contact]",
                                "[$post->owenr_email]",
                                "[$countryName]",
                                "[$cityName]",
                                "[$post->prim_concern_name]",
                                "[$post->prim_contact]",
                                "[$post->prim_email]",
                                "[$post->sec_concern_name]",
                                "[$post->sec_contact]",
                                "[$post->sec_email]",
                                "[$post->concern_name3]",
                                "[$post->contact3]",
                                "[$post->concern_name4]",
                                "[$post->contact4]",
                                "[$post->concern_name5]",
                                "[$post->contact5]",
                                "[$post->concern_name6]",
                                "[$post->contact6]",
                                "[$post->status]",
                                "[$post->office_eng_name]"
                            ];

                            $final_array = str_replace($remStr,$repStr,$assign_var);

                            $rem2 = ["[","]"];
                            $rep2 = ["",""];

                            // Send Whatsapp Start
                            $data = [];
                            $data['template_name'] = $templateDet->template_name;
                            $data['template_language'] = "en_US";

                            foreach ($contact_types as $contact_type) {
                                if (($contact_type == 'all' || $contact_type == 'owner') && $post->owner_contact != '') {
                                    $data['phone_number'] = $post->owner_contact;

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
                                        'first_name' => $post->owner_name,
                                        'last_name' => "--",
                                        "email" => $post->owenr_email,
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
                                    $respost->contactsend_id = $sendcontact->id;
                                    $respost->name = $post->owner_name;
                                    $respost->mobile_no = $post->owner_contact;
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


                                if (($contact_type == 'all' || $contact_type == 'Primary') && $post->prim_contact != '') {
                                    $data['phone_number'] = $post->prim_contact;

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
                                        'first_name' => $post->prim_concern_name,
                                        'last_name' => "--",
                                        "email" => $post->prim_email,
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
                                    $respost->contactsend_id = $sendcontact->id;
                                    $respost->name = $post->prim_concern_name;
                                    $respost->mobile_no = $post->prim_contact;
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

                                if (($contact_type == 'all' || $contact_type == 'Secondary') && $post->sec_contact != '') {
                                    $data['phone_number'] = $post->sec_contact;

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
                                        'first_name' => $post->sec_concern_name,
                                        'last_name' => "--",
                                        "email" => $post->sec_email,
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
                                    $respost->contactsend_id = $sendcontact->id;
                                    $respost->name = $post->sec_concern_name;
                                    $respost->mobile_no = $post->sec_contact;
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
                            $countcontactTag = Contactsendtag::where('contact_id','=',$post->id)->count();
                            $contactTag = new Contactsendtag();
                            $contactTag->contact_id = $post->id;
                            $contactTag->send_tag = $countcontactTag + 1;
                            $contactTag->send_date = date('Y-m-d');
                            $contactTag->save();

                            // Update Contact Send Tag and Send Date
                            $updContactSendTag = Contactplus::find($post->id);
                            $updContactSendTag->send_tag = "Send ".$contactTag->send_tag;
                            $updContactSendTag->send_date = $contactTag->send_date.",".$post->send_date;
                            $updContactSendTag->save();

                        }else{
                            $respost = new Sendwhatsappresponse();
                            $respost->contactsend_id = $sendcontact->id;
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
                    $post = new Contactsendwhatsapp();
                    $post->contactp_id = $contactdet->id;
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
                    $post->contact_status = implode(",",$request->contact_status);
                    $post->contact_subscribe = implode(",",$request->subscribe);
                    if ($request->contact_group_id != '') {
                        $post->contact_group = implode(",",$request->contact_group_id);
                    }
                    $post->save();

                    if ($request->campaign_type2 == '1') {
                        // Create Send Job
                        foreach ($getAPIs as $getAPI) {
                            dispatch(new BulkContactSendJob($contactdet,$post,$getAPI))->onConnection('database')->onQueue('default');
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

    public function updateStage(Request $request){
        $post = Contactplus::find($request->contactID);
        $post->lcs_id = $request->lcs_id;
        $post->ls_id = $request->ls_id;
        $post->update_lead_status_date = date('Y-m-d');
        $post->save();

        if(isset($request->notes) && $request->notes != ''){
            $notestP = new Contactpnotes();
            $notestP->notes = $request->notes;
            $notestP->contactp_id = $request->contactID;
            $notestP->admin_id = Auth::guard('admin')->user()->id;
            $notestP->save();
        }

        // return redirect()->back()->with('success','Lead Stage updated!');

        return response()->json(['success' => 'Lead Stage updated!']);
    }

    public function contactsAddNotes(Request $request){
        $post = new Contactpnotes();
        $post->notes = $request->notes;
        $post->contactp_id = $request->contactID;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->conversation_type = $request->conversation_type;
        $post->save();

        return redirect()->back()->with('success','Notes updated!');
    }

    public function contactsDeleteNotes($id)
    {
        try {
    
            $note = Contactpnotes::findOrFail($id);
    
            $note->delete();
    
            return redirect()->back()->with('success','Notes Deleted!');
           
    
        } catch (\Throwable $e) {
    
            return redirect()->back()->with('error','Notes Not Deleted!');

        }
    }


    public function delete(Request $request){
        $post = Contactplus::find($request->contactID);

        $post->delete();

        return redirect()->back()->with('success','Contact Deleted!');
    }

    public function sendcontactwhatsapp(Request $request,$id){
        $post = new Contactsendwhatsapp();

        if($request->send_whatsapp_type == 'meta_whatsapp'){
            // Store in DB
            $post->contactp_id = $id;
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
                $contactDet = Contactplus::find($id);

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
                    "[Office Name (English)]",
                    "[Office Name (Arabic)]",
                    "[Office Number]",
                    "[Office Email]",
                    "[Owner Name]",
                    "[Owner Contact]",
                    "[Owner Email]",
                    "[Country]",
                    "[City]",
                    "[Primary Concern Person]",
                    "[Primary Contact No]",
                    "[Primary Email]",
                    "[Secondary Concern Person]",
                    "[Secondary Contact No]",
                    "[Secondary Email]",
                    "[Concern Person 3]",
                    "[Contact No 3]",
                    "[Concern Person 4]",
                    "[Contact No 4]",
                    "[Concern Person 5]",
                    "[Contact No 5]",
                    "[Concer Person 6]",
                    "[Contact No 6]",
                    "[Status]",
                    "[Company]"
                ];

                $repStr = [
                    "[$contactDet->office_eng_name]",
                    "[$contactDet->office_ar_name]",
                    "[$contactDet->office_no]",
                    "[$contactDet->office_email]",
                    "[$contactDet->owner_name]",
                    "[$contactDet->owner_contact]",
                    "[$contactDet->owenr_email]",
                    "[$countryName]",
                    "[$cityName]",
                    "[$contactDet->prim_concern_name]",
                    "[$contactDet->prim_contact]",
                    "[$contactDet->prim_email]",
                    "[$contactDet->sec_concern_name]",
                    "[$contactDet->sec_contact]",
                    "[$contactDet->sec_email]",
                    "[$contactDet->concern_name3]",
                    "[$contactDet->contact3]",
                    "[$contactDet->concern_name4]",
                    "[$contactDet->contact4]",
                    "[$contactDet->concern_name5]",
                    "[$contactDet->contact5]",
                    "[$contactDet->concern_name6]",
                    "[$contactDet->contact6]",
                    "[$contactDet->status]",
                    "[$contactDet->office_eng_name]"
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


                        if($contactDet->owner_contact != ''){
                            $data['phone_number'] = $contactDet->owner_contact;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->owner_name,
                                'last_name' => "--",
                                "email" => $contactDet->owenr_email,
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
                                $respost->contactsend_id = $post->id;
                                $respost->name = $contactDet->owner_name;
                                $respost->mobile_no = $contactDet->owner_contact;
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

                        if($contactDet->prim_contact != ''){
                            $data['phone_number'] = $contactDet->prim_contact;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->prim_concern_name,
                                'last_name' => "--",
                                "email" => $contactDet->prim_email,
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
                                $respost->contactsend_id = $post->id;
                                $respost->name = $contactDet->prim_concern_name;
                                $respost->mobile_no = $contactDet->prim_contact;
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

                        if($contactDet->sec_contact != ''){
                            $data['phone_number'] = $contactDet->sec_contact;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->sec_concern_name,
                                'last_name' => "--",
                                "email" => $contactDet->sec_email,
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
                                $respost->contactsend_id = $post->id;
                                $respost->name = $contactDet->sec_concern_name;
                                $respost->mobile_no = $contactDet->sec_contact;
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

                        if($contactDet->contact3 != ''){
                            $data['phone_number'] = $contactDet->contact3;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->concern_name3,
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
                                $respost->contactsend_id = $post->id;
                                $respost->name = $contactDet->concern_name3;
                                $respost->mobile_no = $contactDet->contact3;
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

                        if($contactDet->contact4 != ''){
                            $data['phone_number'] = $contactDet->contact4;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->concern_name4,
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
                                $respost->contactsend_id = $post->id;
                                $respost->name = $contactDet->concern_name4;
                                $respost->mobile_no = $contactDet->contact4;
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

                        if($contactDet->contact5 != ''){
                            $data['phone_number'] = $contactDet->contact5;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->concern_name5,
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
                                $respost->contactsend_id = $post->id;
                                $respost->name = $contactDet->concern_name5;
                                $respost->mobile_no = $contactDet->contact5;
                                if (isset($responseGet->result)){
                                    $respost->message_status = $responseGet->result;
                                    $respost->message_text = $responseGet->message;
                                }else{
                                    $respost->message_status = "Failed";
                                    $respost->message_text = $responseGet->message;
                                    $respost->error_data_field = json_encode($responseGet->errors);
                                }
                                $respost->contactp_id = $id;
                                $respost->save();
                            }
                        }

                        if($contactDet->contact6 != ''){
                            $data['phone_number'] = $contactDet->contact6;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->concern_name6,
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
                                $respost->contactsend_id = $post->id;
                                $respost->name = $contactDet->concern_name6;
                                $respost->mobile_no = $contactDet->contact6;
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
                        if($contactDet->owner_contact != ''){
                            $data['phone_number'] = $contactDet->owner_contact;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->owner_name,
                                'last_name' => "--",
                                "email" => $contactDet->owenr_email,
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
                                $respost->contactsend_id = $post->id;
                                $respost->name = $contactDet->owner_name;
                                $respost->mobile_no = $contactDet->owner_contact;
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
                        if($contactDet->prim_contact != ''){
                            $data['phone_number'] = $contactDet->prim_contact;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->prim_concern_name,
                                'last_name' => "--",
                                "email" => $contactDet->prim_email,
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
                                $respost->contactsend_id = $post->id;
                                $respost->name = $contactDet->prim_concern_name;
                                $respost->mobile_no = $contactDet->prim_contact;
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
                        if($contactDet->sec_contact != ''){
                            $data['phone_number'] = $contactDet->sec_contact;

                            for($i=0;count($field_var) > $i;$i++){
                               if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                    $data[$field_var[$i]] = $headerfilepath;
                               }else{
                                    $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                               }
                            }

                            $data['contact'] =  [
                                'first_name' => $contactDet->sec_concern_name,
                                'last_name' => "--",
                                "email" => $contactDet->sec_email,
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
                                $respost->contactsend_id = $post->id;
                                $respost->name = $contactDet->sec_concern_name;
                                $respost->mobile_no = $contactDet->sec_contact;
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
                $countcontactTag = Contactsendtag::where('contact_id','=',$id)->count();

                $contactTag = new Contactsendtag();
                $contactTag->contact_id = $id;
                $contactTag->send_tag = $countcontactTag + 1;
                $contactTag->send_date = date('Y-m-d');
                $contactTag->save();

                // Update Contact Send Tag and Send Date
                $updContactSendTag = Contactplus::find($id);
                $updContactSendTag->send_tag = "Send ".$contactTag->send_tag;
                $updContactSendTag->send_date = $contactTag->send_date.",".$contactDet->send_date;
                $updContactSendTag->save();

                return redirect()->back()->with('success','Whatsapp Message sent!');

            }else{

                return redirect()->back()->with('success','Send Message are scheduled');
            }

        }else{

            // Store in DB
            $post->contactp_id = $id;
            $post->for_whatsapp = $request->send_whatsapp_type;
            $post->template_id = $request->template_id;
            $post->whatsapp_api = implode(",",$request->wapi_id_text);
            $post->contact_type = implode(",",$request->contact_type_normal);
            $post->campaign_type = $request->campaign_type2;
            $post->send_type = "1";

            if($request->campaign_type2 == '2'){
                $post->date_time = date('Y-m-d h:i',strtotime($request->date_and_time2));
            }

            $post->admin_id = Auth::guard('admin')->user()->id;
            $post->save();

            $contact_types = $request->contact_type_normal;

            if ($request->campaign_type2 == '1') {
                return redirect()->back()->with('success','Normal Whatsapp Send Now is under development');
            }else{
                return redirect()->back()->with('success','Send Normal Message are scheduled!');
            }



        }
    }

    public function contactpfilter(Request $request){
        $post = Contactpfilter::where('admin_id','=',Auth::guard('admin')->user()->id)->count();
        if($post > 0){
            $updateF = Contactpfilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

            if($request->short_form_filter == 1){
				$updateF->short_form_filter = true;
			}else{
				$updateF->short_form_filter = false;
			}

            if($request->countryf == 1){
				$updateF->country_filter = true;
			}else{
				$updateF->country_filter = false;
			}

            if($request->cityf == 1){
				$updateF->city_filter = true;
			}else{
				$updateF->city_filter = false;
			}

            if($request->groupnamef == 1){
				$updateF->group_name_filter = true;
			}else{
				$updateF->group_name_filter = false;
			}

            if($request->lifecyclestatusf == 1){
				$updateF->life_cycle_status_filter = true;
			}else{
				$updateF->life_cycle_status_filter = false;
			}

            if($request->leadstagef == 1){
				$updateF->lead_stage_filter = true;
			}else{
				$updateF->lead_stage_filter = false;
			}

            if($request->businesstypef == 1){
				$updateF->business_type_filter = true;
			}else{
				$updateF->business_type_filter = false;
			}

            if($request->createddatef == 1){
				$updateF->created_date_filter = true;
			}else{
				$updateF->created_date_filter = false;
			}

            if($request->createbyf == 1){
				$updateF->created_by = true;
			}else{
				$updateF->created_by = false;
			}


            $updateF->save();
    		return response()->json('success');
        }else{
            $newFilter = new Contactpfilter();

            $newFilter->admin_id = Auth::guard('admin')->user()->id;

            if($request->countryf == 1){
				$newFilter->country_filter = true;
			}else{
				$newFilter->country_filter = false;
			}

            if($request->short_form_filter == 1){
				$newFilter->short_form_filter = true;
			}else{
				$newFilter->short_form_filter = false;
			}

            if($request->cityf == 1){
				$newFilter->city_filter = true;
			}else{
				$newFilter->city_filter = false;
			}

            if($request->groupnamef == 1){
				$newFilter->group_name_filter = true;
			}else{
				$newFilter->group_name_filter = false;
			}

            if($request->lifecyclestatusf == 1){
				$newFilter->life_cycle_status_filter = true;
			}else{
				$newFilter->life_cycle_status_filter = false;
			}

            if($request->leadstagef == 1){
				$newFilter->lead_stage_filter = true;
			}else{
				$newFilter->lead_stage_filter = false;
			}

            if($request->businesstypef == 1){
				$newFilter->business_type_filter = true;
			}else{
				$newFilter->business_type_filter = false;
			}

            if($request->createddatef == 1){
				$newFilter->created_date_filter = true;
			}else{
				$newFilter->created_date_filter = false;
			}

            if($request->createbyf == 1){
				$newFilter->created_by = true;
			}else{
				$newFilter->created_by = false;
			}



            $newFilter->save();

            return response()->json('success');
        }
    }

    public function saveadminfilter(Request $request){
        $checkFilter = Contactplusadminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();
        if (isset($checkFilter)) {

            if ($request->country_id != '') {
                $checkFilter->country_id = implode(",",$request->country_id);
            } else {
                $checkFilter->country_id = "";
            }

            if ($request->city_id != '') {
                $checkFilter->city_id = implode(",",$request->city_id);
            } else {
                $checkFilter->city_id = "";
            }

            if ($request->group_id != '') {
                $checkFilter->group_id = implode(",",$request->group_id);
            } else {
                $checkFilter->group_id = "";
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

            if ($request->businesstype_id != '') {
                $checkFilter->businesstype_id = implode(",",$request->businesstype_id);
            } else {
                $checkFilter->businesstype_id = "";
            }

            if ($request->industry_id != '') {
                $checkFilter->industry_id = implode(",",$request->industry_id);
            } else {
                $checkFilter->industry_id = "";
            }

            if ($request->sned_tag != '') {
                $checkFilter->sned_tag = implode(",",$request->sned_tag);
            } else {
                $checkFilter->sned_tag = "";
            }

            if ($request->send_date != '') {
                $checkFilter->send_date = $request->send_date;
            } else {
                $checkFilter->send_date = "";
            }

            if ($request->created_date != '') {
                $checkFilter->created_date = $request->created_date;
            } else {
                $checkFilter->created_date = "";
            }

            if ($request->updated_date != '') {
                $checkFilter->updated_date = $request->updated_date;
            } else {
                $checkFilter->updated_date = "";
            }

            if ($request->update_status_date != '') {
                $checkFilter->update_status_date = $request->update_status_date;
            } else {
                $checkFilter->update_status_date = "";
            }
            if ($request->created_by != '') {
                $checkFilter->created_by = implode(",",$request->created_by);
            } else {
                $checkFilter->created_by = "";
            }

            if ($request->subscribe != '') {
                $checkFilter->subscribe = implode(",",$request->subscribe);
            } else {
                $checkFilter->subscribe = "";
            }

            if ($request->status != '') {
                $checkFilter->status = implode(",",$request->status);
            } else {
                $checkFilter->status = "";
            }

            $checkFilter->custom_filters = $this->validateCustomFilters($request->input('custom_filters'));

            $checkFilter->save();

            $data = [
                'res' => 'Filter update successfully!'
            ];
        }else{
            $saveFilter = new Contactplusadminsavefilter();
            $saveFilter->admin_id = Auth::guard('admin')->user()->id;
            if ($request->country_id != '') {
                $saveFilter->country_id = implode(",",$request->country_id);
            } else {
                $saveFilter->country_id = "";
            }

            if ($request->city_id != '') {
                $saveFilter->city_id = implode(",",$request->city_id);
            } else {
                $saveFilter->city_id = "";
            }

            if ($request->group_id != '') {
                $saveFilter->group_id = implode(",",$request->group_id);
            } else {
                $saveFilter->group_id = "";
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

            if ($request->businesstype_id != '') {
                $saveFilter->businesstype_id = implode(",",$request->businesstype_id);
            } else {
                $saveFilter->businesstype_id = "";
            }

            if ($request->industry_id != '') {
                $saveFilter->industry_id = implode(",",$request->industry_id);
            } else {
                $saveFilter->industry_id = "";
            }

            if ($request->sned_tag != '') {
                $saveFilter->sned_tag = implode(",",$request->sned_tag);
            } else {
                $saveFilter->sned_tag = "";
            }

            if ($request->send_date != '') {
                $saveFilter->send_date = $request->send_date;
            } else {
                $saveFilter->send_date = "";
            }

            if ($request->created_date != '') {
                $saveFilter->created_date = $request->created_date;
            } else {
                $saveFilter->created_date = "";
            }

            if ($request->updated_date != '') {
                $saveFilter->updated_date = $request->updated_date;
            } else {
                $saveFilter->updated_date = "";
            }

            if ($request->update_status_date != '') {
                $saveFilter->update_status_date = $request->update_status_date;
            } else {
                $saveFilter->update_status_date = "";
            }
            if ($request->created_by != '') {
                $saveFilter->created_by = implode(",",$request->created_by);
            } else {
                $saveFilter->created_by = "";
            }

            if ($request->subscribe != '') {
                $saveFilter->subscribe = implode(",",$request->subscribe);
            } else {
                $saveFilter->subscribe = "";
            }

            if ($request->status != '') {
                $saveFilter->status = implode(",",$request->status);
            } else {
                $saveFilter->status = "";
            }

            $saveFilter->custom_filters = $this->validateCustomFilters($request->input('custom_filters'));

            $saveFilter->save();


            $data = [
                'res' => 'Filter save successfully!'
            ];
        }

        return response()->json($data);
    }

    public function resetadminfilter(Request $request) {
        $checkFilter = Contactplusadminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();
        if (isset($checkFilter)) {

            $checkFilter->country_id = "";
            $checkFilter->city_id = "";
            $checkFilter->group_id = "";
            $checkFilter->lcs_id = "";
            $checkFilter->ls_id = "";
            $checkFilter->businesstype_id = "";
            $checkFilter->industry_id = "";
            $checkFilter->sned_tag = "";
            $checkFilter->send_date = "";
            $checkFilter->created_date = "";
            $checkFilter->updated_date = "";
            $checkFilter->update_status_date = "";
            $checkFilter->created_by = "";
            $checkFilter->subscribe = "";
            $checkFilter->status = "";
            $checkFilter->custom_filters = null;
            $checkFilter->save();

            $data = [
                'res' => 'Filter reset successfully!'
            ];
        }else{
            $data = [
                'res' => 'Filter reset successfully!'
            ];
        }

        return response()->json($data);
    }

    public function lifecyclelist(){

        return view('admin.contactp.lifecycleindex');
    }

    public function lifecyclelistJson(Request $request){
        $posts = Lifecyclestatus::orderBy('name')->get();

        $data['data'] = $posts;

        return response()->json($data);
    }

    public function lifecyclestr(Request $request){
        $post = new Lifecyclestatus();
        $post->name = $request->name;
        $post->save();

        return redirect()->back()->with('success','Lifecycle Status added!');
    }

    public function lifecycleedit(Request $request){
        $post = Lifecyclestatus::find($request->id);

        return response()->json($post);
    }

    public function lifecycleupdt(Request $request){
        $post = Lifecyclestatus::find($request->edit_id);
        $post->name = $request->name;
        $post->save();

        return redirect()->back()->with('success','Lifecycle Status updated!');
    }

    public function leadstagelist(){
        $lifecycles = Lifecyclestatus::orderBy('name')->get();
        return view('admin.contactp.leadindex',compact('lifecycles'));
    }

    public function leadstagelistJson(Request $request) {
        // $posts = Leadstage::orderBy('name')->get();
        $post = DB::table('leadstages as leadstage')
            ->leftjoin('lifecyclestatuses as lifecyclestatus','leadstage.leadcyclestatus_id','=','lifecyclestatus.id')
            ->select('leadstage.*','lifecyclestatus.name as lifecyclename')
            ->orderBy('leadstage.name')
            ->get();
        $data['data'] = $post;
        return response()->json($data);
    }

    public function leadstagestr(Request $request){
        $post = new Leadstage();
        $post->name = $request->name;
        $post->leadcyclestatus_id = $request->leadcyclestatus_id;
        $post->save();

        return redirect()->back()->with('success','Lead Stage Status added!');
    }

    public function leadstageedit(Request $request){
        $post = Leadstage::find($request->id);

        return response()->json($post);
    }

    public function leadstageupdt(Request $request){
        $post = Leadstage::find($request->edit_id);
        $post->name = $request->name;
        $post->leadcyclestatus_id = $request->leadcyclestatus_id;
        $post->save();

        return redirect()->back()->with('success','Lead Stage Status updated!');
    }

    public function industrieslist(){
        return view('admin.contactp.industries');
    }

    public function industrieslistJson(Request $request){
        $posts = Industry::orderBy('name')->get();

        $data['data'] = $posts;

        return response()->json($data);
    }

    public function industriesStore(Request $request){
        $post = new Industry();
        $post->name = $request->name;
        $post->save();

        return redirect()->back()->with('success','Industry added!');
    }

    public function industriesedit(Request $request) {
        $post = Industry::find($request->id);

        return response()->json($post);
    }

    public function industriesupdate(Request $request){
        $post = Industry::find($request->edit_id);
        $post->name = $request->name;
        $post->save();

        return redirect()->back()->with('success','Industry updated!');
    }

    public function industriesCheckName(Request $request) {
        $name = $request->name;

        if(isset($request->id) && $request->id != ''){
            $post = Industry::where('name','=',$name)->where('id','!=',$request->id)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        }else{
            $post = Industry::where('name','=',$name)->count();
            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        }

    }

    public function checknameLifecycle(Request $request){
        $name = $request->name;

        if(isset($request->id) && $request->id != ''){
            $post = Lifecyclestatus::where('name','=',$name)->where('id','!=',$request->id)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        }else{
            $post = Lifecyclestatus::where('name','=',$name)->count();
            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        }
    }

    public function checknameLeadstage(Request $request){
        $name = $request->name;

        if(isset($request->id) && $request->id != ''){
            $post = Leadstage::where('name','=',$name)->where('id','!=',$request->id)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        }else{
            $post = Leadstage::where('name','=',$name)->count();
            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        }
    }

    public function saveshortformcode(Request $request){

        $checkFilter = Contactplusadminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();
        if (isset($checkFilter)) {
            $checkFilter->short_form_code = $request->short_form_code;
            $checkFilter->save();

            $data = [
                'res' => 'Short Form update successfully!'
            ];

        }else{
            $saveFilter = new Contactplusadminsavefilter();
            $saveFilter->admin_id = Auth::guard('admin')->user()->id;

            $saveFilter->short_form_code = $request->short_form_code;
            $saveFilter->save();

            $data = [
                'res' => 'Short Form update successfully!'
            ];
        }

        return response()->json($data);
    }


    public function saveaddcompanyform(Request $request){

        $checkFilter = Contactplusadminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();
        if (isset($checkFilter)) {
            $checkFilter->saveaddcompanyform = $request->add_company_form;
            $checkFilter->save();

            $data = [
                'res' => 'Company Form update successfully!'
            ];

        }else{
            $saveFilter = new Contactplusadminsavefilter();
            $saveFilter->admin_id = Auth::guard('admin')->user()->id;

            $saveFilter->saveaddcompanyform = $request->add_company_form;
            $saveFilter->save();

            $data = [
                'res' => 'Company Form update successfully!'
            ];
        }

        return response()->json($data);
    }


    public function previewCsvAllcontact(Request $request){
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

            $mappedFields = $request->input('mapped_fields');
            $csvData = $request->input('csv_data');
            $user_id = Auth::guard('admin')->user()->id;

            // 🔹 Get dropdown values
            $business_type_id = $request->input('business_type_id');
            $careoff_id = $request->input('careoff_id');
            $source = $request->input('source');
            $group_id = $request->input('group_id');
            $country_id = $request->input('country_id');

            $insertData = [];
            $duplicateData = [];
            $now = now();

            foreach ($csvData as $row) {
                $data = ['staff_id' => Auth::guard('admin')->user()->id];

                foreach ($mappedFields as $dbField => $csvIndex) {
                    $data[$dbField] = isset($row[$csvIndex]) ? trim($row[$csvIndex]) : null;
                }
                
                 // 🔹 Include dropdown values if not empty
                if (!empty($business_type_id)) {
                    $data['businesstype_id'] = $business_type_id;
                }
                if (!empty($country_id)) {
                    $data['country_id'] = $country_id;
                }
                if (!empty($careoff_id) && $careoff_id != 'not_required') {
                    $data['careoff_id'] = $careoff_id;
                }
                if (!empty($source) && $source != 'not_required') {
                    $data['source'] = $source;
                }
                if (!empty($group_id) && $group_id != 'not_required') {
                    $data['group_id'] = $group_id;
                }

                // **Handle Country ID Lookup & Creation**
                if (!empty($data['country_id'])) {
                    $country = Country::firstOrCreate(
                        ['name' => $data['country_id']],
                        ['admin_id' => $user_id]
                    );
                    $data['country_id'] = $country->id;
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
                $duplicateExists = Contactplus::where(function ($query) use ($data) {
                    if (!empty($data['prim_email'])) {
                        $query->orWhere('prim_email', '=', $data['prim_email']);
                    }
                    if (!empty($data['prim_contact'])) {
                        $query->orWhere('prim_contact', '=', $data['prim_contact']);
                    }
                })->exists();


                if (!$duplicateExists) {
                    $insertData[] = $data;
                } else {
                    $duplicateData[] = $data['email'] ?? ($data['prim_contact'] ?? 'Unknown');
                }
            }

            // **Dispatch Job for Import**
            if (!empty($insertData)) {
                ContactPlusProcessCsvImport::dispatch($insertData)->onQueue('default');
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
    

}
