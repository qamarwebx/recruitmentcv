<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DealPipeline;
use App\Models\Business;
use App\Models\Associates;
use App\Models\Admin;
use App\Models\DealStage;
use App\Models\RecruitStatus;
use App\Models\DealAdminSaveFilter;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Adminpermission;
use App\Models\JobTitle;
use App\Models\GlobalSourceType;
use App\Models\Allcontact;
use App\Models\DealReminder;
use App\Models\DealActivityLog;

class DealPipelineController extends Controller
{
    public function index(Request $request, $dealId = null)
    {
        $autoOpenDealId = $dealId ? (int) $dealId : null;

        /* -------------------------------------------------
        | Auth & Permissions
        -------------------------------------------------*/
        $user      = Auth::guard('admin')->user();
        $adminId  = $user->id;
        $isAdmin  = $user->user_type == 1;

        $careoffIds = DealPipeline::whereNotNull('care_of')
            ->pluck('care_of')
            ->unique()
            ->toArray();

        $careoffs = Admin::where('status', 1)
            // ->whereIn('id', $careoffIds)
            ->orderBy('name', 'ASC')
            ->get();

        $activeCareoffs = Admin::where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();

        // $careoffs = Admin::where('status', 1)->orderBy('name', 'ASC')->get();
        // $admins = $careoffs;

        $perms = Adminpermission::where('staff_id', $adminId)->first();
        if ($perms && $perms->deal_pipeline != 1) {
            abort(403, 'You do not have permission to view Deal Pipeline.');
        }

        /* -------------------------------------------------
        | Master Data (cached / light queries)
        -------------------------------------------------*/
        $businesses        = Business::all();
        $dealStages        = DealStage::orderBy('id')->get();
        $recruitStatuses   = RecruitStatus::all();
        $jobTitles         = JobTitle::all();
        $globalSourceType  = GlobalSourceType::where('is_active', 1)->get();
        $admins            = Admin::where('status', 1)->orderBy('name')->get();

        $associates = Associates::where('id', '!=', 73)
            ->when(!$isAdmin, fn ($q) => $q->where('careoff_id', $adminId))
            ->orderBy('pty_full_name')
            ->get();

        /* -------------------------------------------------
        | Saved Filters
        -------------------------------------------------*/
        $saveadminfilter = DealAdminSaveFilter::where('admin_id', $adminId)->first();

        /* -------------------------------------------------
        | Base Query (IMPORTANT)
        -------------------------------------------------*/
        $posts = DealPipeline::with([
            'business',
            'stage',
            'recruitStatus',
            'careOf',
            'jobTitle'
        ]);

        /* -------------------------------------------------
        | Resolve Deal Stage / Recruit Status once
        | AJAX requests (the new stage/status bars included) are fully
        | authoritative for these two columns; the saved filter only
        | pre-fills the very first, non-AJAX page load. Everything else in
        | this method still stacks the saved filter with the live request
        | (pre-existing behavior, left as-is), but doing that for these two
        | specific columns would stack two whereIn()s whenever they
        | disagreed and silently return zero rows — exactly what a stage/
        | status pill click would trigger the moment it didn't match a
        | stale saved value.
        -------------------------------------------------*/
        $dealStageId = $request->ajax()
            ? $request->deal_stage_id
            : (optional($saveadminfilter)->deal_stage_id ? explode(',', $saveadminfilter->deal_stage_id) : null);

        $recruiteStatusId = $request->ajax()
            ? $request->recruite_status_id
            : (optional($saveadminfilter)->recruite_status_id ? explode(',', $saveadminfilter->recruite_status_id) : null);

        $posts->filterDealStage($dealStageId);
        $posts->filterRecruitStatus($recruiteStatusId);

        /* -------------------------------------------------
        | Apply Saved Filters
        -------------------------------------------------*/
        if ($saveadminfilter) {

            $posts->when($saveadminfilter->business_id, fn ($q) =>
                $q->whereIn('business_id', explode(',', $saveadminfilter->business_id))
            );

            $posts->when($saveadminfilter->associate_id, fn ($q) =>
                $q->whereIn('associate_id', explode(',', $saveadminfilter->associate_id))
            );

            $posts->when($saveadminfilter->job_title_id, fn ($q) =>
                $q->whereIn('job_title', explode(',', $saveadminfilter->job_title_id))
            );

            $posts->when($saveadminfilter->care_of, fn ($q) =>
                $q->whereIn('care_of', explode(',', $saveadminfilter->care_of))
            );

            $posts->when($saveadminfilter->source, fn ($q) =>
                $q->whereIn('source', explode(',', $saveadminfilter->source))
            );

            $posts->when($saveadminfilter->created_by, fn ($q) =>
                $q->whereIn('created_by', explode(',', $saveadminfilter->created_by))
            );

            if ($saveadminfilter->created_date) {
                [$from, $to] = explode(' - ', $saveadminfilter->created_date);
                $posts->whereBetween('created_at', [
                    Carbon::parse($from)->startOfDay(),
                    Carbon::parse($to)->endOfDay()
                ]);
            }

            if ($saveadminfilter->updated_date) {
                [$from, $to] = explode(' - ', $saveadminfilter->updated_date);
                $posts->whereBetween('updated_at', [
                    Carbon::parse($from)->startOfDay(),
                    Carbon::parse($to)->endOfDay()
                ]);
            }

            if (!empty($saveadminfilter->followup_before)) {

                $days = (int) $saveadminfilter->followup_before;

                $today = \Carbon\Carbon::today();
                $futureDate = \Carbon\Carbon::today()->addDays($days);

                if($days == 1){
                    $today = \Carbon\Carbon::today();
                    $futureDate = \Carbon\Carbon::today();
               }
            
                $posts->whereBetween('updated_at', [
                    $today->startOfDay(),
                    $futureDate->endOfDay()
                ]);
            }
        }

        /* -------------------------------------------------
        | AJAX Filters (Live)
        -------------------------------------------------*/
        if ($request->ajax()) {

            $posts->when($request->search_text,
                fn ($q) => $q->filterSearchText($request->search_text)
            );

            $posts->when($request->kanban_search_text,
                fn ($q) => $q->FilterSearchTextForkanban($request->kanban_search_text)
            );

            $posts->when($request->business_id,
                fn ($q) => $q->whereIn('business_id', (array)$request->business_id)
            );

            // deal_stage_id / recruite_status_id are resolved once, above —
            // not reapplied here.

            $posts->when($request->associate_id,
                fn ($q) => $q->whereIn('associate_id', (array)$request->associate_id)
            );

            $posts->when($request->job_title_id,
                fn ($q) => $q->whereIn('job_title', (array)$request->job_title_id)
            );

            $posts->when($request->care_of,
                fn ($q) => $q->whereIn('care_of', (array)$request->care_of)
            );

            $posts->when($request->source,
                fn ($q) => $q->whereIn('source', (array)$request->source)
            );

            $posts->when($request->created_by,
                fn ($q) => $q->whereIn('created_by', (array)$request->created_by)
            );

            if ($request->created_date) {
                [$from, $to] = explode(' - ', $request->created_date);
                $posts->whereBetween('created_at', [
                    Carbon::parse($from)->startOfDay(),
                    Carbon::parse($to)->endOfDay()
                ]);
            }

            if ($request->updated_date) {
                [$from, $to] = explode(' - ', $request->updated_date);
                $posts->whereBetween('updated_at', [
                    Carbon::parse($from)->startOfDay(),
                    Carbon::parse($to)->endOfDay()
                ]);
            }
            
            if ($request->followup_before) {

                $days = (int) $request->followup_before;
                $from = Carbon::now()->subDays($days)->format('Y-m-d');
                $to = Carbon::now()->format('Y-m-d');

               if($days == 1){
                    $from = Carbon::now()->format('Y-m-d');
                    $to = Carbon::now()->format('Y-m-d');
               }

                $posts->whereBetween('updated_at', [
                    Carbon::parse($from)->startOfDay(),
                    Carbon::parse($to)->endOfDay()
                ]);
            }

            if (!$isAdmin && $perms->deal_view != 1) {
                $posts->where('created_by', $adminId);
            }

            /* ---------- Kanban / List Switch ---------- */
            if ($saveadminfilter && $saveadminfilter->switch_to == '0') {

                $kanbanLimit = 4;

                $dealslistsKanbans = $posts
                    ->orderBy('sort_order','ASC')
                    ->get()
                    ->groupBy('deal_stage_id')
                    ->map(fn ($items) => [
                        'deals' => $items->take($kanbanLimit),
                        'total' => $items->count()
                    ]);

                return view('admin.dealPipeline.kanban_load',
                    compact('dealslistsKanbans', 'dealStages', 'isAdmin', 'perms')
                );
            }

            $dealsLists = $posts
                ->orderBy('sort_order','ASC')
                ->paginate($request->page_list ?? 10)
                ->withQueryString();

            return view('admin.dealPipeline.load',
                compact('dealsLists', 'dealStages', 'isAdmin', 'perms')
            );
        }

        /* -------------------------------------------------
        | Initial Page Load
        -------------------------------------------------*/
        if (!$isAdmin && $perms->deal_view != 1) {

            $posts->Where('care_of', $adminId);
            // $posts->where(function ($query) use ($adminId) {
            //     // $query->where('created_by', $adminId)
            //           $query->Where('care_of', $adminId);
            // });
        
        }

        $kanbanLimit = 4;


        $dealslistsKanbans = $posts
            ->orderBy('sort_order','ASC')
            ->get()
            ->groupBy('deal_stage_id')
            ->map(fn ($items) => [
                'deals' => $items->take($kanbanLimit),
                'total' => $items->count()
            ]);


        $dealsLists = $posts
            ->orderBy('sort_order','ASC')
            ->paginate($request->page_list ?? 10)
            ->withQueryString();

        $dealStatusSummary = $this->buildDealStatusSummary($isAdmin, $perms, $adminId);

        return view('admin.dealPipeline.index', compact(
            'dealslistsKanbans',
            'dealsLists',
            'businesses',
            'associates',
            'recruitStatuses',
            'jobTitles',
            'admins',
            'dealStages',
            'saveadminfilter',
            'perms',
            'isAdmin',
            'globalSourceType',
            'careoffs',
            'activeCareoffs',
            'autoOpenDealId',
            'dealStatusSummary'
        ));
    }

    /**
     * Global (permission-scoped, not filter-scoped) Deal Stage / Recruit
     * Status counts for the top bars — same "counts stay fixed while the
     * table filters" principle used by the Leads/Testimonial/Todo summary
     * cards. A couple of GROUP BY aggregates, not one query per bucket.
     * Scoped the same way the initial page load's own list/kanban queries
     * already are (care_of), since these counts render only on that load.
     */
    private function buildDealStatusSummary(bool $isAdmin, $perms, int $adminId): array
    {
        $base = DealPipeline::query();

        if (!$isAdmin && optional($perms)->deal_view != 1) {
            $base->where('care_of', $adminId);
        }

        $stageCounts = (clone $base)
            ->select('deal_stage_id', DB::raw('COUNT(*) as total'))
            ->groupBy('deal_stage_id')
            ->pluck('total', 'deal_stage_id');

        $recruitStatusCounts = (clone $base)
            ->whereNotNull('recruite_status_id')
            ->select('recruite_status_id', DB::raw('COUNT(*) as total'))
            ->groupBy('recruite_status_id')
            ->pluck('total', 'recruite_status_id');

        return [
            'total' => (int) $stageCounts->sum(),
            'stages' => $stageCounts,
            'recruitStatuses' => $recruitStatusCounts,
        ];
    }

    public function loadMoreKanban(Request $request)
    {

        /* -------------------------------------------------
        | Auth & Permissions
        -------------------------------------------------*/
        $user      = Auth::guard('admin')->user();
        $adminId  = $user->id;
        $isAdmin  = $user->user_type == 1;

        $careoffs = Admin::where('status', 1)->orderBy('name', 'ASC')->get();
        $admins = $careoffs;

        $perms = Adminpermission::where('staff_id', $adminId)->first();
        if ($perms && $perms->deal_pipeline != 1) {
            abort(403, 'You do not have permission to view Deal Pipeline.');
        }


        $stageId = (int) $request->stage_id;
        $offset  = (int) $request->offset;
        $limit   = 4;

        $user     = Auth::guard('admin')->user();
        $adminId = $user->id;
        $isAdmin = $user->user_type == 1;

        /* -----------------------------------------
        | Base query
        -----------------------------------------*/
        $query = DealPipeline::with([
            'business',
            'stage',
            'recruitStatus',
            'careOf',
            'jobTitle'
        ])->where('deal_stage_id', $stageId);

        /* -----------------------------------------
        | Permission restriction
        -----------------------------------------*/
        if (!$isAdmin) {
            $query->where('created_by', $adminId);
        }

        /* -----------------------------------------
        | Apply AJAX filters (same as index)
        -----------------------------------------*/
        $query->when($request->search_text,
            fn ($q) => $q->filterSearchText($request->search_text)
        );

        $query->when($request->kanban_search_text,
            fn ($q) => $q->FilterSearchTextForkanban($request->kanban_search_text)
        );

        $query->when($request->business_id,
            fn ($q) => $q->whereIn('business_id', (array)$request->business_id)
        );

        $query->when($request->deal_stage_id,
            fn ($q) => $q->whereIn('deal_stage_id', (array)$request->deal_stage_id)
        );

        $query->when($request->recruite_status_id,
            fn ($q) => $q->whereIn('recruite_status_id', (array)$request->recruite_status_id)
        );

        $query->when($request->associate_id,
            fn ($q) => $q->whereIn('associate_id', (array)$request->associate_id)
        );

        $query->when($request->job_title_id,
            fn ($q) => $q->whereIn('job_title', (array)$request->job_title_id)
        );

        $query->when($request->care_of,
            fn ($q) => $q->whereIn('care_of', (array)$request->care_of)
        );

        $query->when($request->source,
            fn ($q) => $q->whereIn('source', (array)$request->source)
        );

        if ($request->created_date) {
            [$from, $to] = explode(' - ', $request->created_date);
            $query->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        }

        if ($request->updated_date) {
            [$from, $to] = explode(' - ', $request->updated_date);
            $query->whereBetween('updated_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        }

        if ($request->followup_before) {

            $days = (int) $request->followup_before;
        
            $from = Carbon::now()->subDays($days);
            $to = Carbon::now();
        
            $query->whereBetween('updated_at', [
                $from->startOfDay(),
                $to->endOfDay()
            ]);
        }

        /* -----------------------------------------
        | Fetch next chunk
        -----------------------------------------*/
        $deals = $query
            ->orderBy('sort_order','ASC')
            ->skip($offset)
            ->take($limit)
            ->get();

        /* -----------------------------------------
        | Return partial HTML
        -----------------------------------------*/
        return view('admin.dealPipeline.partials.kanban_cards', compact('deals','isAdmin','perms'));
    }

    public function kanbanReorder(Request $request)
    {
        if (!$request->order || !is_array($request->order)) {
            return response()->json([
                'responseStatus' => 400,
                'responseMessage' => 'Invalid order data'
            ]);
        }
    
        foreach ($request->order as $item) {
    
            DealPipeline::where('id', $item['id'])
                ->update([
                    'sort_order' => $item['position']
                ]);
        }
    
        /* get stage_id (same as task_status in todo) */
        $stageId = DealPipeline::where('id', $request->order[0]['id'])
            ->value('deal_stage_id');
    
        $this->reindexDealColumn($stageId);
    
        return response()->json([
            'responseStatus' => 200,
            'responseMessage' => 'Deal order updated'
        ]);
    }

    private function reindexDealColumn($stageId)
    {
        $deals = DealPipeline::where('deal_stage_id', $stageId)
            ->orderBy('sort_order', 'ASC')
            ->pluck('id');

        $i = 1;

        foreach ($deals as $id) {

            DealPipeline::where('id', $id)
                ->where('sort_order', '!=', $i)
                ->update(['sort_order' => $i]);

            $i++;
        }
    }

    public function create()
    {
        // $stages = DealStage::all();
        // $statuses = RecruitStatus::all();
        $users = User::all();

        return view('admin.dealPipeline.create', compact('stages', 'statuses', 'users'));
    }

    public function store(Request $request)
    {
        try {
            // Get business type
            $business = Business::find($request->add_business_id);
            $dealStage = DealStage::where('name','Prospecting')->first();
            if(!$dealStage){
                return response()->json([
                    'success' => false,
                    'message' => 'Default Deal Stage "Prospecting" not found. Please create it first.'
                ], 500);
            }
            $deal_stage_id = $dealStage->id;

            // Dynamic validation
            if ($business && $business->name === 'Job seeker') {
                $rules = [
                    'add_business_id' => 'required|integer',
                    'add_associate_id' => 'required|integer',
                    'add_careoff_id' => 'required|integer',
                    'add_job_title' => 'nullable|string|max:255',
                    'add_candidate' => 'nullable|string|max:255',
                    'add_passport_no' => 'nullable|string|max:255',
                    'add_country' => 'nullable|string|max:100',
                    'add_city' => 'nullable|string|max:100',
                    'add_amount' => 'nullable|numeric',
                    'add_mobile' => 'nullable|string|max:20',
                    'add_mobile_whatsapp' => 'nullable|string|max:20',
                    'add_email' => 'nullable|email|max:255',
                    'add_source' => 'nullable|string|max:100',
                    'add_notes' => 'nullable|string',
                ];
            } 
            else {
                // Common fields only
                $rules = [
                    'add_business_id' => 'required|integer',
                    'add_common_careoff' => 'required|integer',
                    'add_common_name' => 'nullable|string|max:255',
                    'add_common_job_title' => 'nullable|string|max:255',
                    'add_common_company_name' => 'nullable|string|max:255',
                    'add_common_country' => 'nullable|string|max:100',
                    'add_common_city' => 'nullable|string|max:100',
                    'add_common_mobile' => 'nullable|string|max:20',
                    'add_ommon_mobile_whatsapp' => 'nullable|string|max:20',
                    'add_common_email' => 'nullable|email|max:255',
                    'add_common_source' => 'nullable|string|max:100',
                    'add_common_notes' => 'nullable|string',
                ];
            }

            $validated = $request->validate($rules);

            if ($business && $business->name === 'Job seeker') {

                $dealPipeline = DealPipeline::create([
                    'deal_stage_id' => $deal_stage_id,
                    'business_id' => $request->add_business_id ?? null,
                    'associate_id' => $request->add_associate_id ?? null,
                    'care_of' => $request->add_careoff_id ?? null,
                    'job_title' => $request->add_job_title ?? null,
                    'candidate' => $request->add_candidate ?? null,
                    'passport_no' => $request->add_passport_no ?? null,
                    'country' => $request->add_country ?? null,
                    'city' => $request->add_city ?? null,
                    'amount' => $request->add_amount ?? null,
                    'contact' => $request->add_mobile ?? null,
                    'contact_whatsapp' => $request->add_mobile_whatsapp ?? null,
                    'email' => $request->add_email ?? null,
                    'source' => $request->add_source ?? null,
                    'notes' => $request->add_notes ?? null,
                    'created_by' => Auth::guard('admin')->user()->id
                ]);

            }else {
                $dealPipeline = DealPipeline::create([
                    'deal_stage_id' => $deal_stage_id,
                    'business_id' => $request->add_business_id ?? null,
                    'care_of' => $request->add_common_careoff ?? null,
                    'name' => $request->add_common_name ?? null,
                    'job_title_other' => $request->add_common_job_title ?? null,
                    'company' => $request->add_common_company_name ?? null,
                    'country' => $request->add_common_country ?? null,
                    'city' => $request->add_common_city ?? null,
                    'contact' => $request->add_common_mobile ?? null,
                    'contact_whatsapp' => $request->add_ommon_mobile_whatsapp ?? null,
                    'email' => $request->add_common_email ?? null,
                    'source' => $request->add_common_source ?? null,
                    'notes' => $request->add_common_notes ?? null,
                    'created_by' => Auth::guard('admin')->user()->id
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Deal created successfully!',
                'data' => $dealPipeline
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getDeal(Request $request)
    {
        $deal = DealPipeline::with(['business', 'associate', 'stage', 'recruitStatus', 'careOf','creator','jobTitle','source:id,name'])->find($request->deal_id);
        if(!$deal){
            return response()->json(['success' => false, 'message' => 'Deal not found']);
        }

        // Same visibility rule index() uses for the initial page load: staff
        // without deal_view can only open deals assigned to them.
        $user = Auth::guard('admin')->user();
        $isAdmin = $user->user_type == 1;
        $perms = Adminpermission::where('staff_id', $user->id)->first();

        if (!$isAdmin && optional($perms)->deal_view != 1 && $deal->care_of != $user->id) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to view this deal.'], 403);
        }

        return response()->json(['success' => true, 'data' => $deal]);
    }

    /**
     * Deal Activity tab (AJAX, paginated) — same visibility rule as
     * getDeal()/DealNoteController::list(): staff without deal_view can
     * only see activity for deals assigned to them.
     */
    public function dealActivity(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deal_pipeline,id',
        ]);

        $deal = DealPipeline::find($request->deal_id);

        $user = Auth::guard('admin')->user();
        $isAdmin = $user->user_type == 1;
        $perms = Adminpermission::where('staff_id', $user->id)->first();

        if (!$isAdmin && optional($perms)->deal_view != 1 && $deal->care_of != $user->id) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to view this deal.'], 403);
        }

        $activities = DealActivityLog::with('admin')
            ->where('deal_id', $deal->id)
            ->latest()
            ->paginate(10);

        $html = view('admin.dealPipeline.partials.activity', compact('activities'))->render();

        return response()->json([
            'success'     => true,
            'html'        => $html,
            'has_more'    => $activities->hasMorePages(),
            'next_page'   => $activities->currentPage() + 1,
            'is_empty'    => $activities->total() === 0,
        ]);
    }

    public function update(Request $request)
    {
        try {
            $deal = DealPipeline::findOrFail($request->edit_deal_id);
            $business = Business::find($request->edit_business_id);

            $rules = [];

            if($business->name === 'Job seeker'){
                $rules = [
                    'edit_deal_id' => 'required|integer',
                    'edit_business_id' => 'required|integer',
                    'edit_associate_id' => 'required|integer',
                    'edit_careoff_id' => 'required|integer',
                    'edit_job_title' => 'nullable|string|max:255',
                    'edit_candidate' => 'nullable|string|max:255',
                    'edit_passport_no' => 'nullable|string|max:255',
                    'edit_country' => 'nullable|string|max:255',
                    'edit_city' => 'nullable|string|max:255',
                    'edit_amount' => 'nullable|numeric',
                    'edit_mobile' => 'nullable|string|max:255',
                    'edit_mobile_whatsapp' => 'nullable|string|max:20',
                    'edit_email' => 'nullable|email|max:255',
                    'edit_source' => 'nullable|string|max:100',
                    'edit_closed_date' => 'nullable|date_format:Y-m-d',
                    'edit_notes' => 'nullable|string',
                ];
            } 
            else {
                // Common fields
                $rules = [
                    'edit_deal_id' => 'required|integer',
                    'edit_business_id' => 'required|integer',
                    'edit_common_careoff' => 'required|integer',
                    'edit_common_name' => 'nullable|string|max:255',
                    'edit_common_job_title' => 'nullable|string|max:255',
                    'edit_common_company_name' => 'nullable|string|max:255',
                    'edit_common_country' => 'nullable|string|max:100',
                    'edit_common_city' => 'nullable|string|max:100',
                    'edit_common_mobile' => 'nullable|string|max:20',
                    'edit_common_mobile_whatsapp' => 'nullable|string|max:20',
                    'edit_common_email' => 'nullable|email|max:255',
                    'edit_common_source' => 'nullable|string|max:100',
                    'edit_common_notes' => 'nullable|string',
                ];
            }

            $validated = $request->validate($rules);

            // Update based on business type
            if($business->name === 'Job seeker'){
                $deal->update([
                    'business_id'  => $request->edit_business_id,
                    'associate_id' => $request->edit_associate_id,
                    'care_of'      => $request->edit_careoff_id,
                    'job_title'    => $request->edit_job_title ?? null,
                    'candidate'    => $request->edit_candidate ?? null,
                    'passport_no'  => $request->edit_passport_no ?? null,
                    'country'      => $request->edit_country ?? null,
                    'city'         => $request->edit_city ?? null,
                    'amount'       => $request->edit_amount ?? null,
                    'contact'      => $request->edit_mobile ?? null,
                    'contact_whatsapp' => $request->edit_mobile_whatsapp ?? null,
                    'email'        => $request->edit_email ?? null,
                    'source'       => $request->edit_source ?? null,
                    'close_date'   => $request->edit_closed_date ?? null,
                    'notes'        => $request->edit_notes ?? null,
                    'modified_by'  => Auth::guard('admin')->user()->id
                ]);
            }
            else {                
                // Common fields update
                $deal->update([
                    'business_id'  => $request->edit_business_id,
                    'care_of'      => $request->edit_common_careoff,
                    'name'         => $request->edit_common_name ?? null,
                    'job_title_other' => $request->edit_common_job_title ?? null,
                    'company'      => $request->edit_common_company_name ?? null,
                    'country'      => $request->edit_common_country ?? null,
                    'city'         => $request->edit_common_city ?? null,
                    'contact'      => $request->edit_common_mobile ?? null,
                    'contact_whatsapp' => $request->edit_common_mobile_whatsapp ?? null,
                    'email'        => $request->edit_common_email ?? null,
                    'source'       => $request->edit_common_source ?? null,
                    'notes'        => $request->edit_common_notes ?? null,
                    'modified_by'  => Auth::guard('admin')->user()->id
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Deal updated successfully', 'data' => $deal]);

        } catch (\Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateDealStage(Request $request)
    {
        $deal = DealPipeline::find($request->id);
        if (!$deal) {
            return response()->json(['message' => 'Deal not found'], 404);
        }
    
        $dealStage = DealStage::where('name', $request->text)->first();
        if (!$dealStage) {
            return response()->json(['message' => 'Invalid stage name'], 400);
        }
    
        // Map stage → message and badge
        $stageData = [
            'Prospecting'   => ['badge' => 'info', 'message' => 'Deal moved to Prospecting stage!'],
            'Qualification' => ['badge' => 'primary', 'message' => 'Deal marked as in Qualification stage!'],
            'Discussion'    => ['badge' => 'secondary', 'message' => 'Deal moved to Discussion stage!'],
            'Proposal'      => ['badge' => 'warning', 'message' => 'Proposal stage updated!'],
            'Review'        => ['badge' => 'dark', 'message' => 'Deal is now under Review!'],
            'Closed Won'    => ['badge' => 'success', 'message' => 'Congratulations! Deal marked as Closed Won!'],
            'Closed Lost'   => ['badge' => 'danger', 'message' => 'Deal marked as Closed Lost!'],
        ];
    
        // Update the stage
        $deal->deal_stage_id = $dealStage->id;
        $deal->save();
    
        $stageName = $request->text;
        $response = $stageData[$stageName] ?? ['badge' => 'primary', 'message' => 'Stage updated successfully!'];
    
        // Return JSON with badge and message
        return response()->json([
            'status'  => $stageName,
            'badge'   => $response['badge'],
            'message' => $response['message'],
        ]);
    }

    public function updateRecruitStatus(Request $request)
    {
        $deal = DealPipeline::find($request->id);
        if (!$deal) {
            return response()->json(['message' => 'Deal not found'], 404);
        }

        $status = $request->status;

        // Map status → badge & message
        $statusData = [
            'On Medical'   => ['badge' => 'warning', 'message' => 'Recruit status updated to On Medical!'],
            'Medical Fit'  => ['badge' => 'success', 'message' => 'Recruit status updated to Medical Fit!'],
            'Not Ready'    => ['badge' => 'secondary', 'message' => 'Recruit status updated to Not Ready!'],
            'FOL'          => ['badge' => 'danger', 'message' => 'Recruit status updated to FOL!'],
        ];

        // Update the recruite_status_id
        $recruitStatus = RecruitStatus::where('name', $status)->first();
        if (!$recruitStatus) {
            return response()->json(['message' => 'Invalid recruit status'], 400);
        }

        $deal->recruite_status_id = $recruitStatus->id;
        $deal->save();

        $response = $statusData[$status] ?? ['badge' => 'primary', 'message' => 'Recruit status updated successfully!'];

        return response()->json([
            'status'  => $status,
            'badge'   => $response['badge'],
            'message' => $response['message'],
        ]);
    }

    public function kanbanUpdateStatus(Request $request)
    {
        $deal = DealPipeline::find($request->deal_id); // Get the deal
        // $newStageId = $request->stage_id;             // New stage ID
        $newStageId = (int) str_replace('stage-', '', $request->stage_id);

        if (!$deal || !$newStageId) {
            return response()->json([
                'responseStatus' => 404,
                'responseMessage' => 'Deal or stage not found. Please refresh the page.',
            ]);
        }
    
        // Update the deal stage
        $deal->deal_stage_id = $newStageId;
        $deal->save();
    
        // Optional: You can return a message with the stage name
        $stageName = optional(DealStage::find($newStageId))->name ?? 'Unknown Stage';
    
        /* reindex target column */
        $this->reindexDealColumn($request->stage_id);

        return response()->json([
            'responseStatus' => 200,
            'responseMessage' => "Deal moved to '{$stageName}' stage successfully!",
        ]);
    }
    
    public function saveFilter(Request $request)
    {
        $adminId = Auth::guard('admin')->user()->id;
    
        // Retrieve existing filter or create a new one
        $filter = DealAdminSaveFilter::firstOrNew(['admin_id' => $adminId]);
    
        // Convert multi-select inputs to comma-separated strings
        $filter->business_id  = $request->business_id   ? implode(',', $request->business_id) : '';
        $filter->deal_stage_id  = $request->deal_stage_id   ? implode(',', $request->deal_stage_id) : '';
        $filter->recruite_status_id  = $request->recruite_status_id   ? implode(',', $request->recruite_status_id) : '';
        $filter->associate_id = $request->associate_id  ? implode(',', $request->associate_id) : '';
        $filter->job_title_id = $request->job_title_id  ? implode(',', $request->job_title_id) : '';
        $filter->care_of   = $request->care_of    ? implode(',', $request->care_of) : '';
        $filter->source       = $request->source        ? implode(',', $request->source) : '';
        $filter->created_by   = $request->created_by    ? implode(',', $request->created_by) : '';
        $filter->created_date   = $request->created_date;
        $filter->updated_date   = $request->updated_date;
        $filter->followup_before   = $request->followup_before;

    
        $filter->save();
    
        return response()->json([
            'switch_to' => $filter->switch_to,
            'message' => $filter->wasRecentlyCreated ? 'Filter saved successfully!' : 'Filter updated successfully!'
        ]);
    }

    public function resetFilter(Request $request)
    {
        $adminId = Auth::guard('admin')->user()->id;
    
        $filter = DealAdminSaveFilter::where('admin_id', $adminId)->first();
    
        if ($filter) {
            $filter->business_id  = null;
            $filter->recruite_status_id  = null;
            $filter->associate_id = null;
            $filter->job_title_id = null;
            $filter->care_of      = null;
            $filter->source       = null;
            $filter->created_by   = null;
            $filter->created_date = null;
            $filter->updated_date   = null;
            $filter->followup_before   = null;
            $filter->save();
        }
    
        return response()->json([
            'switch_to' => $filter->switch_to,
            'res' => 'Filter reset successfully!'
        ]);
    }

    public function switchto(Request $request){
    
        
        $checkFilter = DealAdminSaveFilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();
        if ($request->switch == 'kanban') {
            $switch = 0;
            $data = [
                'res' => 'Kanban is available!'
            ];
        } elseif ($request->switch == 'deallist') {

            $switch = 1;
            $data = [
                'res' => 'Deal List is available!'
            ];
        }
        if (isset($checkFilter)) {
            
            $checkFilter->switch_to = $switch;
            $checkFilter->save();

        } else {
            $todoPost = new DealAdminSaveFilter();
            $todoPost->admin_id = Auth::guard('admin')->user()->id;
            $todoPost->switch_to = $switch;
            $todoPost->save();
        }

        return response()->json($data);

    }
    
    public function destroy(Request $request)
    {
        $deal = DealPipeline::findOrFail($request->deal_pipeline_id);
        $deal->delete();
        return response()->json(['success' => 'Deal successfully deleted!']);

    }

    public function checkPassport(Request $request)
    {
        $passportNo = $request->input('passport_no');
        $dealId = $request->input('deal_id'); // optional, only for edit
    
        // Query the deal pipeline for the passport number
        $query = DealPipeline::where('passport_no', $passportNo);
    
        // Exclude the current deal if deal_id is provided (edit scenario)
        if($dealId) {
            $query->where('id', '!=', $dealId);
        }
    
        $deal = $query->first();
    
        return response()->json([
            'exists' => $deal ? true : false,
            'creator' => $deal->creator->name ?? 'Unknown', // adjust relation if needed
        ]);
    }

    public function searchAllcontact(Request $request)
    {
        $search = "%{$request->search}%";

        $data = Allcontact::where(function ($q) use ($search) {
            $q->where('primary_no_wsp', 'LIKE', $search)
                ->orWhere('secondary_no_wsp', 'LIKE', $search)
                ->orWhere('mobile_no1_wsp', 'LIKE', $search)
                ->orWhere('mobile_no2_wsp', 'LIKE', $search)
                ->orWhere('mobile_no3_wsp', 'LIKE', $search)
                ->orwhere('full_name', $search);
        })
        ->first();

        if ($data) {
            $status = true;
            $display = $data->full_name . " - " . $data->primary_no_wsp; // 👈 added
            $message = "Contact Found with All Contact [".$display.']';
        } else {
            $status = false;
            $message = "Contact Not Found with All Contact!";
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
    }

    public function storeReminder(Request $request)
    {
        DealReminder::create([
            'deal_id' => $request->deal_id,
            'created_by' => Auth::guard('admin')->user()->id,
            'reminder_at' => Carbon::parse($request->reminder_at),
            'whatsapp_description' => $request->whatsapp_description ?? null,
        ]);

        return response()->json(['status' => 'success']);
    }

    public function listReminder(Request $request)
    {
        $reminders = DealReminder::where('deal_id', $request->deal_id)
            ->latest()
            ->get();

        return view('admin.dealPipeline.partials.reminder_list', compact('reminders'))->render();
    }

    public function deleteReminder(Request $request)
    {
        $reminder = DealReminder::find($request->reminder_id);

        if (!$reminder) {
            return response()->json(['status' => 'error']);
        }

        $reminder->delete();

        return response()->json(['status' => 'success']);
    }


}
