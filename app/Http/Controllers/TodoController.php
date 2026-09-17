<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todostatus;
use App\Models\Todo;
use App\Models\Admin;
use App\Models\Todoactivity;
use App\Models\Todolabel;
use App\Models\Department;
use App\Models\Basepathstatus;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Adminpermission;
use App\Models\Todoadminsavefilter;
use Carbon\Carbon;

class TodoController extends Controller
{

    public function index(Request $request, $todoId = null)
    {
        $autoOpenTodoId = $todoId ? (int) $todoId : null;

        /* -----------------------------------------
        | Auth & Permission
        -----------------------------------------*/
        $user       = Auth::guard('admin')->user();
        $adminId    = $user->id;
        $isAdmin    = $user->user_type == 1;
    
        $permission = Adminpermission::where('staff_id',$adminId)->first();
        $saveadminfilter = Todoadminsavefilter::where('admin_id',$adminId)->first();
    
    
        /* -----------------------------------------
        | Master Data
        -----------------------------------------*/
        $todoLabels  = Todolabel::orderBy('name','DESC')->get();
        $departments = Department::orderBy('name')->get();

        $assignIds = Todo::whereNotNull('assignto_id')
            ->pluck('assignto_id')
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->unique()
            ->filter()
            ->values()
            ->toArray();

        $staffs = Admin::where('status', 1)
            ->whereIn('id', $assignIds)
            ->latest()
            ->get();

        // $staffs      = Admin::where('status',1)->latest()->get();
    
    
        /* -----------------------------------------
        | Filter Data
        -----------------------------------------*/
        $departfilters = DB::table('todos as todo')
            ->leftJoin('departments as department','department.id','=','todo.department_id')
            ->select('todo.department_id','department.name as deptname')
            ->whereNotNull('todo.department_id')
            ->groupBy('todo.department_id','deptname')
            ->get();
    
    
        $todoLabelfilters = DB::table('todos as todo')
            ->leftJoin('todolabels as todolabel','todolabel.id','=','todo.todolabel_id')
            ->select('todo.todolabel_id','todolabel.name as labelname')
            ->whereNotNull('todo.todolabel_id')
            ->groupBy('todo.todolabel_id','labelname')
            ->get();
    
    
        $todoStatusfilters = DB::table('todos')
            ->select('task_status')
            ->groupBy('task_status')
            ->when(!$isAdmin && !optional($permission)->todo_achieved,function($q){
                $q->where('task_status','!=','Achieved');
            })
            ->get();
    
    
        $todoPriorityfilters = DB::table('todos')
            ->select('reminder_cycle')
            ->whereNotNull('reminder_cycle')
            ->whereNotIn('reminder_cycle',['1'])
            ->groupBy('reminder_cycle')
            ->get();
    
    
    
        /* -----------------------------------------
        | Base Query
        -----------------------------------------*/
        $posts = Todo::with(['department','todolabel','admin']);
    
    
    
        /* -----------------------------------------
        | Permission Restriction
        -----------------------------------------*/
        if(!$isAdmin && !optional($permission)->full_access){
    
            if(optional($permission)->todo_view != 1){
                $posts->whereRaw("FIND_IN_SET(?, assignto_id)", [$adminId]);
            }
    
            if(optional($permission)->todo_achieved != 1){
                $posts->where('task_status','!=','Achieved');
            }
        }
    
    
    
        /* -----------------------------------------
        | Resolve Effective Filters
        | On AJAX requests, the live values submitted from the browser (the
        | status/priority/follow-up bar, the filter panel, search,
        | pagination — everything funnels through the same request payload)
        | are the full, authoritative filter state. The saved filter is only
        | used to pre-fill the very first, non-AJAX page load. Previously
        | BOTH were applied together unconditionally, stacking two
        | whereIn()s on the same column (e.g. task_status) whenever they
        | disagreed — which returned zero rows and made the status bar
        | look like it "did not filter correctly". Each field is now
        | resolved once and applied once.
        -----------------------------------------*/
        $this->applyTodoFilters($posts, $this->resolveTodoFilters($request, $saveadminfilter));



        /* -----------------------------------------
        | AJAX Response
        -----------------------------------------*/
        if($request->ajax()){

            // Which view is actually on screen right now decides the
            // response shape — not the last-saved switch_to, which can be
            // stale (e.g. a fresh admin who never saved a filter, or one
            // who switched views without it persisting in time). The list
            // and kanban containers both call this same endpoint and
            // always tell us which one is currently visible.
            $isKanbanView = $request->filled('view_mode')
                ? $request->view_mode === 'kanban'
                : ($saveadminfilter && $saveadminfilter->switch_to == 1);

            /* ---------- Kanban ---------- */

            if($isKanbanView){

                $kanbanLimit = 4;

                $todolistsKanbans = $posts
                    ->orderBy('sort_order','ASC')
                    // ->orderBy('updated_at','DESC')
                    ->get()
                    ->groupBy('task_status')
                    ->map(fn ($items) => [
                        'todos' => $items->take($kanbanLimit),
                        'total' => $items->count()
                    ]);

                return view('admin.todo.kanban_load',
                    compact('todolistsKanbans','permission','saveadminfilter'));
            }



            /* ---------- Table ---------- */

            $todoLists = $posts
                ->orderBy('sort_order','ASC')
                ->paginate($request->page_list ?? 10)
                ->withQueryString();

            return view('admin.todo.load',
                compact('todoLists','permission'));
        }
    
    
    
        /* -----------------------------------------
        | Initial Page Load
        -----------------------------------------*/
    
        $kanbanLimit = 4;
    
        $todolistsKanbans = $posts
            ->orderBy('sort_order','ASC')
            ->get()
            ->groupBy('task_status')
            ->map(fn ($items) => [
                'todos' => $items->take($kanbanLimit),
                'total' => $items->count()
            ]);
    
    
        $todoLists = $posts
            ->orderBy('sort_order','ASC')
            ->paginate($request->page_list ?? 10)
            ->withQueryString();


        $todoStatusSummary = $this->buildTodoStatusSummary($isAdmin, $permission, $adminId);


        return view('admin.todo.index',compact(
            'todoLists',
            'todolistsKanbans',
            'saveadminfilter',
            'permission',
            'departfilters',
            'todoStatusfilters',
            'staffs',
            'todoLabels',
            'departments',
            'todoLabelfilters',
            'todoPriorityfilters',
            'autoOpenTodoId',
            'todoStatusSummary'
        ));
    }

    /**
     * Resolve the effective filter values for the Todo list/kanban query.
     * On AJAX requests (list, kanban, load-more-kanban all funnel through
     * here) the live request payload is authoritative; the saved filter
     * only pre-fills the very first, non-AJAX page load. Applying both at
     * once — as the old code did — stacked two whereIn()s on the same
     * column whenever they disagreed and silently returned zero rows.
     */
    private function resolveTodoFilters(Request $request, ?Todoadminsavefilter $saveadminfilter): array
    {
        if ($request->ajax()) {
            return [
                'department_id' => $request->department_id,
                'todolabel_id' => $request->todolabel_id,
                'reminder_cycle' => $request->reminder_cycle,
                'task_status' => $request->task_status,
                'followup_due' => $request->followup_due,
                'assignto_id' => $request->assignto_id,
                'followup_before' => $request->followup_before,
                'created_by' => $request->created_by,
                'today_reminder_task' => $request->today_reminder_task,
                'type_fi' => $request->type_fi,
                'start_on' => $request->start_on,
                'finish_on' => $request->finish_on,
                'complete_date' => $request->complete_date,
                'achieved_date' => $request->achieved_date,
                'created_at' => $request->created_at,
                'updated_at' => $request->updated_at,
                'search_text' => $request->search_text ?: $request->search_text_kanban,
            ];
        }

        if ($saveadminfilter) {
            return [
                'department_id' => $saveadminfilter->department_id ? explode(",", $saveadminfilter->department_id) : null,
                'todolabel_id' => $saveadminfilter->todolabel_id ? explode(",", $saveadminfilter->todolabel_id) : null,
                'reminder_cycle' => $saveadminfilter->priority ? explode(",", $saveadminfilter->priority) : null,
                'task_status' => $saveadminfilter->task_status ? explode(",", $saveadminfilter->task_status) : null,
                'followup_due' => null,
                'assignto_id' => $saveadminfilter->assignto_id ? explode(",", $saveadminfilter->assignto_id) : null,
                'followup_before' => $saveadminfilter->followup_before ?: null,
                'created_by' => $saveadminfilter->created_by_id ? explode(",", $saveadminfilter->created_by_id) : null,
                'today_reminder_task' => $saveadminfilter->today_reminder_task ?? null,
                'type_fi' => $saveadminfilter->type ? explode(",", $saveadminfilter->type) : null,
                'start_on' => $saveadminfilter->start_date,
                'finish_on' => $saveadminfilter->finish_date,
                'complete_date' => $saveadminfilter->complete_date_range,
                'achieved_date' => $saveadminfilter->achieved_date_range,
                'created_at' => $saveadminfilter->created_date_range,
                'updated_at' => $saveadminfilter->updated_date_range,
                'search_text' => null,
            ];
        }

        return array_fill_keys([
            'department_id', 'todolabel_id', 'reminder_cycle', 'task_status', 'followup_due',
            'assignto_id', 'followup_before', 'created_by', 'today_reminder_task', 'type_fi',
            'start_on', 'finish_on', 'complete_date', 'achieved_date', 'created_at', 'updated_at', 'search_text',
        ], null);
    }

    private function applyTodoFilters($query, array $f): void
    {
        $query->filterDepartment($f['department_id']);
        $query->filterLabel($f['todolabel_id']);
        $query->filterReminderCycle($f['reminder_cycle']);
        $query->filterTaskStatus($f['task_status']);
        $query->filterFollowupDue($f['followup_due']);
        $query->filterAssignedTo($f['assignto_id']);
        $query->filterFollowupBefore($f['followup_before']);
        $query->filterCreatedBy($f['created_by']);
        $query->filterTodayReminderTask($f['today_reminder_task']);
        $query->filterType($f['type_fi']);
        $query->filterDate('start_on', $f['start_on']);
        $query->filterDate('finish_on', $f['finish_on']);
        $query->filterDateRange('complete_date', $f['complete_date']);
        $query->filterDateRange('achieved_date', $f['achieved_date']);
        $query->filterDateRange('created_at', $f['created_at']);
        $query->filterDateRange('updated_at', $f['updated_at']);
        $query->filterSearchText($f['search_text']);
    }

    /**
     * Global (permission-scoped, not filter-scoped) status/priority/
     * follow-up-due counts for the top bar — same "counts stay fixed while
     * the table filters" principle used by the Leads/Testimonial summary
     * cards. A handful of aggregate queries, not one per bucket.
     */
    private function buildTodoStatusSummary(bool $isAdmin, $permission, int $adminId): array
    {
        $base = Todo::query();

        if (!$isAdmin && !optional($permission)->full_access) {
            if (optional($permission)->todo_view != 1) {
                $base->whereRaw("FIND_IN_SET(?, assignto_id)", [$adminId]);
            }

            if (optional($permission)->todo_achieved != 1) {
                $base->where('task_status', '!=', 'Achieved');
            }
        }

        $statusCounts = (clone $base)
            ->select('task_status', DB::raw('COUNT(*) as total'))
            ->groupBy('task_status')
            ->pluck('total', 'task_status');

        $priorityCounts = (clone $base)
            ->whereIn('reminder_cycle', ['High', 'Medium', 'Low'])
            ->select('reminder_cycle', DB::raw('COUNT(*) as total'))
            ->groupBy('reminder_cycle')
            ->pluck('total', 'reminder_cycle');

        $today = Carbon::today()->toDateString();

        $followupRow = (clone $base)
            ->where('is_completed', 0)
            ->whereNotNull('finish_on')
            ->selectRaw(
                'SUM(CASE WHEN finish_on < ? THEN 1 ELSE 0 END) as before_today,
                SUM(CASE WHEN finish_on = ? THEN 1 ELSE 0 END) as today,
                SUM(CASE WHEN finish_on > ? THEN 1 ELSE 0 END) as upcoming',
                [$today, $today, $today]
            )->first();

        return [
            'total' => (int) $statusCounts->sum(),
            'statuses' => $statusCounts,
            'priorities' => [
                'High' => (int) ($priorityCounts['High'] ?? 0),
                'Medium' => (int) ($priorityCounts['Medium'] ?? 0),
                'Low' => (int) ($priorityCounts['Low'] ?? 0),
            ],
            'followup' => [
                'before_today' => (int) ($followupRow->before_today ?? 0),
                'today' => (int) ($followupRow->today ?? 0),
                'upcoming' => (int) ($followupRow->upcoming ?? 0),
            ],
        ];
    }

    public function loadMoreKanban(Request $request)
    {
        $stageId = $request->stage_id;
        $offset  = $request->offset;
        $limit   = 4;
    
        $user = Auth::guard('admin')->user();
        $adminId = $user->id;
        $isAdmin = $user->user_type == 1;
    
        $permission = Adminpermission::where('staff_id',$adminId)->first();
        $saveadminfilter = Todoadminsavefilter::where('admin_id',$adminId)->first();

    
        $posts = Todo::with(['department','todolabel','admin'])
            ->where('task_status',$stageId);

         /* Permission restriction */
    
         if(!$isAdmin && !optional($permission)->full_access){
    
            if(optional($permission)->todo_view != 1){
                $posts->whereRaw("FIND_IN_SET(?, assignto_id)", [$adminId]);
            }
    
            if(optional($permission)->todo_achieved != 1){
                $posts->where('task_status','!=','Achieved');
            }
        }

        /* -----------------------------------------
        | Apply Filters
        | The "Load more" button already sends ...getFilterData() (status,
        | priority, follow-up-due, etc.) alongside stage_id/offset, so this
        | reuses the same resolve-once-apply-once logic as index() to
        | actually honor it — previously it only ever consulted the saved
        | filter, ignoring whatever the client sent.
        -----------------------------------------*/
        $this->applyTodoFilters($posts, $this->resolveTodoFilters($request, $saveadminfilter));


        /* Fetch next chunk */
    
        $todos = $posts
            // ->orderBy('updated_at','DESC')
            ->orderBy('sort_order','ASC')
            ->skip($offset)
            ->take($limit)
            ->get();
    
    
        return view('admin.todo.partials.kanban_cards',compact('todos'));
    
    }


    public function todokanbanlist(Request $request){

        return view('admin.todo.kanbanlist');
    }

    public function store(Request $request){

        $basepathstatus = Basepathstatus::first();
    
        // --- Handle file attachment ---
        $todo_file = "";
        if ($request->hasFile('file_attachment')) {
            $file = $request->file('file_attachment');
            $name = $file->getClientOriginalName();
            $filename_ren = pathinfo($name, PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name, PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ", "_", $filename_ren);
            $new_file = $repspfilename . '.' . $fileext_ren;
    
            $destination = $basepathstatus->base_path_status == 1
                ? base_path() . '/public/admin/assets/images/todo'
                : base_path() . '/public_html/admin/assets/images/todo';
    
            $file->move($destination, $new_file);
            $todo_file = $new_file;
        }
    
        // --- Create Todo ---
        $post = new Todo();
        $post->task_title = $request->task_title;
        $post->task_description = $request->task_description;
    
        // --- Assign To ---
        $post->assignto_id = !empty($request->assignto_id)
            ? implode(",", $request->assignto_id)
            : Auth::guard('admin')->user()->id;
    
        // --- Reminder Cycle / Type ---
        $post->reminder_cycle = $request->reminder_cycle ?? null;
        $post->reminder_type = $request->reminder_type ?? null;
        $post->notification_type = $request->notification_type;

    
        // ----- OneTime Reminder -----
        if ($request->reminder_type === 'OneTime' && !empty($request->scheduled_date_time)) {
            $post->scheduled_date_time = Carbon::parse($request->scheduled_date_time)->format('Y-m-d H:i');
            $post->scheduled_status = true;
    
            // Clear other fields
            $post->recurring_type = null;
            $post->recurring_time = null;
            $post->recurring_weekdays = null;
            $post->recurring_month_day = null;
            $post->recurring_year_month_day = null;
            $post->custom_start_date = null;
            $post->custom_end_date = null;
            $post->custom_time = null;
        }
    
        // ----- Recurring Reminder -----
        if ($request->reminder_type === 'Recurring') {
            $post->recurring_type = $request->recurring_type ?? 'Daily';

            // Clear unrelated fields
            $post->scheduled_date_time = null;
            $post->custom_start_date = null;
            $post->custom_end_date = null;
            $post->custom_time = null;

            // Reset all recurring-specific fields first
            $post->recurring_time = null;
            $post->recurring_weekdays = null;
            $post->recurring_month_day = null;
            $post->recurring_year_month_day = null;

            switch ($request->recurring_type) {
                case 'Daily':
                    $formattedTimes = [];
                    $times = $request->recurring_time_daily;
                
                    // If JSON string → decode
                    if (is_string($times)) {
                        $times = json_decode($times, true);
                    }
                
                    if (!empty($times) && is_array($times)) {
                        foreach ($times as $t) {
                            if (!empty($t)) {
                                try {
                                    $formattedTimes[] = Carbon::parse($t)->format('H:i');
                                } catch (\Exception $e) {}
                            }
                        }
                    }
                
                    $post->recurring_time = !empty($formattedTimes)
                        ? json_encode($formattedTimes)
                        : null;
                    break;                

                case 'Weekly':
                    // Decode weekdays
                    $post->recurring_weekdays = !empty($request->recurring_weekdays)
                        ? json_encode(is_string($request->recurring_weekdays)
                            ? json_decode($request->recurring_weekdays, true)
                            : $request->recurring_weekdays)
                        : null;
                
                    // Decode recurring times (handle JSON string or array)
                    $formattedTimes = [];
                    $times = $request->recurring_time_weekly;
                    
                    // Decode if string
                    if (is_string($times)) {
                        $times = json_decode($times, true);
                    }
                    // Process valid array
                    if (!empty($times) && is_array($times)) {
                        foreach ($times as $time) {
                            if (!empty($time)) {
                                try {
                                    $formattedTimes[] = Carbon::parse($time)->format('H:i');
                                } catch (\Exception $e) {
                                    // skip invalid time formats
                                }
                            }
                        }
                    }
                
                    $post->recurring_time = !empty($formattedTimes)
                        ? json_encode($formattedTimes)
                        : null;
                    break;                    

                case 'Monthly':
                    $post->recurring_month_day = $request->recurring_month_day ?? null;
                    $post->recurring_time = !empty($request->recurring_time_monthly)
                        ? Carbon::parse($request->recurring_time_monthly)->format('H:i')
                        : null;
                    break;

                case 'Yearly':
                    $post->recurring_year_month_day = $request->recurring_year_month_day ?? null; // MM-DD
                    $post->recurring_time = !empty($request->recurring_time_yearly)
                        ? Carbon::parse($request->recurring_time_yearly)->format('H:i')
                        : null;
                    break;
            }
        }

        // ----- Custom Reminder -----
        if ($request->reminder_type === 'Custom') {
            if (!empty($request->custom_date_range)) {
                [$start, $end] = explode(' to ', $request->custom_date_range);
                $post->custom_start_date = Carbon::parse($start)->format('Y-m-d');
                $post->custom_end_date = Carbon::parse($end)->format('Y-m-d');
            }
            $post->custom_time = !empty($request->custom_time) ? Carbon::parse($request->custom_time)->format('H:i') : null;
    
            // Clear other fields
            $post->scheduled_date_time = null;
            $post->recurring_type = null;
            $post->recurring_time = null;
            $post->recurring_weekdays = null;
            $post->recurring_month_day = null;
            $post->recurring_year_month_day = null;
        }
    
        // --- Staff Work ---
        $post->staff_work_name = !empty($request->add_staff_work) ? $request->add_staff_work : null;
    
        // --- Other fields ---
        $post->todolabel_id = $request->todolabel_id;
        $post->department_id = $request->department_id;
        $post->start_on = !empty($request->start_on) ? Carbon::parse($request->start_on)->format('Y-m-d') : null;
        $post->finish_on = !empty($request->finish_on) ? Carbon::parse($request->finish_on)->format('Y-m-d') : null;
        $post->support_team_name = $request->support_team_name ?? null;
        $post->support_team_number = $request->support_team_number ?? null;
        $post->type = $request->type ?? 'Regular';
        $post->file_attachment = $todo_file;
        $post->task_status = "New Task";
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();
    
        // --- Todo Task Activity ---
        $taskactivity = new Todoactivity();
        $taskactivity->todo_id = $post->id;
        $taskactivity->message = "The {$post->task_status} was created by " . Auth::guard('admin')->user()->name . ' on ' . $post->created_at->format('d-m-Y H:i');
        $taskactivity->admin_id = Auth::guard('admin')->user()->id;
        $taskactivity->save();

        $this->reindexColumn($post->task_status);

        if(isset($request->insert_from)  && $request->insert_from == 'todo-short-form'){
            return redirect()->back()->with('success', 'New Task Created!');
        }
    
        return response()->json(['success' => 'New Task Created!']);
    }
    
    public function edit(Request $request)
    {
        $post = DB::table('todos as todo')
            ->leftJoin('admins as admin', 'admin.id', '=', 'todo.admin_id')
            ->leftJoin('departments as dept', 'dept.id', '=', 'todo.department_id')
            ->select(
                'todo.*',
                'admin.id as adminId',
                'admin.name as adminname',
                'dept.name as department_name',
                DB::raw("(SELECT GROUP_CONCAT(name) FROM admins WHERE FIND_IN_SET(id, todo.assignto_id)) as assignto_names")
            )
            ->where('todo.id', $request->id)
            ->first();

        if ($post) {
            $post->start_on = $post->start_on ? date('Y-m-d', strtotime($post->start_on)) : null;
            $post->finish_on = $post->finish_on ? date('Y-m-d', strtotime($post->finish_on)) : null;
            $post->scheduled_date_time = $post->scheduled_date_time
                ? date('Y-m-d\TH:i', strtotime($post->scheduled_date_time))
                : null;
            $post->custom_start_date = $post->custom_start_date ? date('Y-m-d', strtotime($post->custom_start_date)) : null;
            $post->custom_end_date = $post->custom_end_date ? date('Y-m-d', strtotime($post->custom_end_date)) : null;
        }

        $basepathstatus = Basepathstatus::first();

        $imageBaseUrl = $basepathstatus && $basepathstatus->base_path_status == 1
            ? asset('admin/assets/images/todo')
            : asset('public_html/admin/assets/images/todo');

        if (!empty($post->file_attachment)) {
            $filePath1 = public_path('admin/assets/images/todo/' . $post->file_attachment);
            $filePath2 = base_path('public_html/admin/assets/images/todo/' . $post->file_attachment);
            if (file_exists($filePath1) || file_exists($filePath2)) {
                $post->file_url = $imageBaseUrl . '/' . $post->file_attachment;
            } else {
                $post->file_url = asset('admin/assets/img/avatars/blank.jpeg');
            }
        } else {
            $post->file_url = asset('admin/assets/img/avatars/blank.jpeg');
        }

        return response()->json($post);
    }

    public function update(Request $request)
    {
        $post = Todo::findOrFail($request->edit_id);
        $basepathstatus = Basepathstatus::first();
    
        // 🟢 Handle file upload
        $todo_file = $post->file_attachment;
        if ($request->hasFile('file_attachment')) {
            $file = $request->file('file_attachment');
            $name = $file->getClientOriginalName();
            $filename_ren = pathinfo($name, PATHINFO_FILENAME);
            $fileext_ren = pathinfo($name, PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ", "_", $filename_ren);
            $new_file = $repspfilename . '.' . $fileext_ren;
    
            $destination = $basepathstatus->base_path_status == 1
                ? base_path() . '/public/admin/assets/images/todo'
                : base_path() . '/public_html/admin/assets/images/todo';
    
            $file->move($destination, $new_file);
            $todo_file = $new_file;
        }
    
        // 🟢 Core fields
        $post->task_title = $request->task_title;
        $post->task_description = $request->task_description;
    
        // 🟢 Assign to
        $post->assignto_id = !empty($request->assignto_id)
            ? implode(",", $request->assignto_id)
            : Auth::guard('admin')->user()->id;
    
        // 🟢 Department & Label
        $post->department_id = $request->department_id;
        $post->todolabel_id = $request->todolabel_id;
    
        // 🟢 Work frequency type
        $post->type = $request->type ?? 'Regular';
    
        // 🟢 Dates
        $post->start_on = !empty($request->start_on) ? date('Y-m-d', strtotime($request->start_on)) : null;
        $post->finish_on = !empty($request->finish_on) ? date('Y-m-d', strtotime($request->finish_on)) : null;
    
        // 🟢 Priority
        $post->reminder_cycle = $request->reminder_cycle;
    
        // 🟢 Reminder Type
        $post->reminder_type = $request->reminder_type;

        $post->notification_type = $request->notification_type;
    
        // 🟩 RESET ALL REMINDER FIELDS FIRST
        $post->scheduled_date_time = null;
        $post->scheduled_status = false;
        $post->recurring_type = null;
        $post->recurring_weekdays = null;
        $post->recurring_month_day = null;
        $post->recurring_year_month_day = null;
        $post->recurring_time = null;
        $post->custom_start_date = null;
        $post->custom_end_date = null;
        $post->custom_time = null;
    
        // --- 🟩 OneTime ---
        if ($request->reminder_type === 'OneTime') {
            if (!empty($request->scheduled_date_time)) {
                $post->scheduled_date_time = date('Y-m-d H:i', strtotime($request->scheduled_date_time));
                $post->scheduled_status = true;
            }
        }
    
        // --- 🟦 Recurring ---
        elseif ($request->reminder_type === 'Recurring') {
    
            $post->recurring_type = $request->recurring_type ?? 'Daily';
            $post->scheduled_status = true;
    
            switch ($request->recurring_type) {
    
                case 'Daily':
                    $formattedTimes = [];
                    $times = $request->recurring_time_daily;
    
                    if (is_string($times)) {
                        $times = json_decode($times, true);
                    }
    
                    if (!empty($times) && is_array($times)) {
                        foreach ($times as $t) {
                            try {
                                $formattedTimes[] = Carbon::parse($t)->format('H:i');
                            } catch (\Exception $e) {}
                        }
                    }
    
                    $post->recurring_time = !empty($formattedTimes)
                        ? json_encode($formattedTimes)
                        : null;
    
                    break;
    
                case 'Weekly':
    
                    if (!empty($request->recurring_weekdays)) {
                        $post->recurring_weekdays = is_string($request->recurring_weekdays)
                            ? $request->recurring_weekdays
                            : json_encode($request->recurring_weekdays);
                    }
    
                    $formattedTimes = [];
                    $times = $request->recurring_time_weekly;
    
                    if (is_string($times)) {
                        $times = json_decode($times, true);
                    }
    
                    if (!empty($times) && is_array($times)) {
                        foreach ($times as $time) {
                            try {
                                $formattedTimes[] = Carbon::parse($time)->format('H:i');
                            } catch (\Exception $e) {}
                        }
                    }
    
                    $post->recurring_time = !empty($formattedTimes)
                        ? json_encode($formattedTimes)
                        : null;
    
                    break;
    
                case 'Monthly':
                    $post->recurring_month_day = $request->recurring_month_day ? intval($request->recurring_month_day) : null;
                    $post->recurring_time = !empty($request->recurring_time)
                        ? date('H:i', strtotime($request->recurring_time))
                        : null;
                    break;
    
                case 'Yearly':
                    $post->recurring_year_month_day = $request->recurring_year_month_day ?? null;
                    $post->recurring_time = !empty($request->recurring_time)
                        ? date('H:i', strtotime($request->recurring_time))
                        : null;
                    break;
            }
        }
    
        // --- 🟨 Custom ---
        elseif ($request->reminder_type === 'Custom') {
    
            if (!empty($request->custom_date_range)) {
    
                $range = explode(' to ', $request->custom_date_range);
    
                if (count($range) == 2) {
                    $post->custom_start_date = $range[0];
                    $post->custom_end_date = $range[1];
                } else {
                    $post->custom_start_date = $request->custom_date_range;
                    $post->custom_end_date = null;
                }
            }
    
            $post->custom_time = !empty($request->custom_time)
                ? date('H:i', strtotime($request->custom_time))
                : null;
        }
    
        // 🧱 Staff Work
        if (!empty($request->input('edit_staff_work_input_hidden'))) {
            $post->staff_work_name = $request->input('edit_staff_work_input_hidden');
        }
    
        // 🖼️ File Attachment
        $post->file_attachment = $todo_file;
    
        // 💾 Save
        $post->save();
    
        // 📝 Log Activity
        $taskactivity = new Todoactivity();
        $taskactivity->todo_id = $post->id;
        $taskactivity->message = "Task '{$post->task_title}' was updated by " . Auth::guard('admin')->user()->name . ' on ' . now()->format('d-m-Y h:i');
        $taskactivity->admin_id = Auth::guard('admin')->user()->id;
        $taskactivity->save();

        $this->reindexColumn($post->task_status);

        $post = Todo::with(['department','todolabel','admin'])->find($post->id);
        $assignees = DB::table('admins')->wherein('id',explode(",",$post->assignto_id))->get();

        return response()->json([
            'success' => 'Task updated successfully!',
            'post' => $post,
            'assignees' => $assignees
        ]);
    }
    

    public function todoDelete(Request $request){
        $post = Todo::find($request->todo_id);
        $post->delete();

        // return redirect()->back()->with('success','Todo successfully deleted!');
        return response()->json(['success' => 'Todo successfully deleted!']);
    }

    public function addStaffWork(Request $request)
    {
        $request->validate([
            'todo_id' => 'required|exists:todos,id',
            'staff_work_name' => 'required|string|max:1000',
        ]);

        $todo = Todo::findOrFail($request->todo_id);

        $existingWork = $todo->staff_work_name ?? [];

        $existingWork[] = $request->staff_work_name;

        $todo->staff_work_name = $existingWork;
        $todo->save();

        return response()->json([
            'status' => true,
            'message' => 'Staff work added successfully',
            'data' => $existingWork
        ]);
    }

    public function deleteStaffWork(Request $request)
    {
        $request->validate([
            'todo_id' => 'required|exists:todos,id',
            'index' => 'required|integer',
        ]);

        $todo = Todo::findOrFail($request->todo_id);

        $staffWorks = $todo->staff_work_name;

        // ✅ ALWAYS convert to array
        if (is_string($staffWorks)) {
            $staffWorks = json_decode($staffWorks, true);

            // handle double encoded JSON
            if (is_string($staffWorks)) {
                $staffWorks = json_decode($staffWorks, true);
            }
        }

        // fallback safety
        if (!is_array($staffWorks)) {
            $staffWorks = [];
        }

        if (isset($staffWorks[$request->index])) {

            // ✅ remove item
            array_splice($staffWorks, $request->index, 1);

            // ✅ save back as JSON string
            $todo->staff_work_name = json_encode($staffWorks);
            $todo->save();

            return response()->json([
                'status' => true,
                'message' => 'Staff work deleted successfully'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid index'
        ], 400);
    }

    public function updateStatus(Request $request) {
        $post = Todo::find($request->id);
        $todoText = $request->text;

        if ($todoText == 'Mark as New Task') {
            $post->task_status = "New Task";
            $post->is_completed = false;
            $post->save();
            $res = [
                'status' => $todoText,
                'message' => 'Mark as New Task successfull!'
            ];
        }elseif ($todoText == 'Mark as In Process') {
            $post->task_status = "In Process";
            $post->is_completed = false;
            $post->save();
            $res = [
                'status' => $todoText,
                'message' => 'Mark as In Process successfull!'
            ];
        }elseif ($todoText == 'Mark as Incomplete') {
            $post->task_status = "Incomplete";
            $post->is_completed = false;
            $post->save();
            $res = [
                'status' => $todoText,
                'message' => 'Mark as Incomplete successfull!'
            ];
        }elseif ($todoText == 'Mark as Complete') {
            $post->task_status = "Complete";
            $post->is_completed = true;
            $post->complete_date = date('Y-m-d');
            $post->save();
            $res = [
                'status' => $todoText,
                'message' => 'Mark as Complete successfull!'
            ];
        }elseif ($todoText == 'Mark as Always') {
            $post->task_status = "Always";
            $post->is_completed = false;
            $post->save();
            $res = [
                'status' => $todoText,
                'message' => 'Mark as Always successfull!'
            ];
        }elseif ($todoText == 'Mark as On Hold') {
            $post->task_status = "On Hold";
            $post->is_completed = false;
            $post->save();
            $res = [
                'status' => $todoText,
                'message' => 'Mark as On Hold successfull!'
            ];
        }elseif ($todoText == 'Mark as Cancelled') {
            $post->task_status = "Cancelled";
            $post->is_completed = false;
            $post->save();
            $res = [
                'status' => $todoText,
                'message' => 'Mark as Cancelled successfull!'
            ];
        }elseif ($todoText == 'Mark as Achieved') {
            $post->task_status = "Achieved";
            $post->achieved_date = date('Y-m-d');
            $post->is_completed = true;
            $post->save();
            $res = [
                'status' => $todoText,
                'message' => 'Mark as Achieved successfull!'
            ];
        }elseif ($todoText == 'Mark as Not Required') {
            $post->task_status = "Not Required";
            $post->achieved_date = date('Y-m-d');
            $post->is_completed = true;
            $post->save();
            $res = [
                'status' => $todoText,
                'message' => 'Mark as Not Required successfull!'
            ];
        }else{
            $res = [
                'status' => $todoText,
                'message' => 'Nothing!'
            ];
        }


        return response()->json($res);


    }

    public function todokanbanstatusupdate(Request $request)
    {
        $post = Todo::find($request->task_id);
    
        if(!$post){
            return response()->json([
                'responseStatus' => 404,
                'responseMessage' => 'Task not found'
            ]);
        }
    
        $oldStatus = $post->task_status;
    
        $statusMap = [
            'stage-new-task' => 'New Task',
            'stage-in-process' => 'In Process',
            'stage-complete' => 'Complete',
            'stage-always' => 'Always',
            'stage-achieved' => 'Achieved',
            'stage-not-required' => 'Not Required',
        ];
    
        $task_status = $statusMap[$request->status] ?? null;
    
        if(!$task_status){
            return response()->json([
                'responseStatus' => 400,
                'responseMessage' => 'Invalid status'
            ]);
        }
    
        /* move task */
        $post->task_status = $task_status;
        $post->sort_order = $request->position;
    
        $post->save();
    
        /* shift other tasks down */
        Todo::where('task_status',$task_status)
            ->where('id','!=',$post->id)
            ->where('sort_order','>=',$request->position)
            ->increment('sort_order');
    
        /* fix old column */
        $this->reindexColumn($oldStatus);
    
        return response()->json([
            'responseStatus' => 200,
            'responseMessage' => 'Todo Task Status updated to '.$task_status
        ]);
    }

    public function kanbanreorder(Request $request)
    {

        if (!$request->order || !is_array($request->order)) {
            return response()->json([
                'responseStatus' => 400,
                'responseMessage' => 'Invalid order data'
            ]);
        }
    
        foreach ($request->order as $item) {
    
            Todo::where('id', $item['id'])
                ->update([
                    'sort_order' => $item['position']
                ]);
    
        }
    
        /* get column status */
        $status = Todo::where('id', $request->order[0]['id'])->value('task_status');
    
        $this->reindexColumn($status);
    
        return response()->json([
            'responseStatus' => 200,
            'responseMessage' => 'Kanban order updated'
        ]);
    }

    private function reindexColumn($status)
    {
        $tasks = Todo::where('task_status', $status)
            ->orderBy('sort_order', 'ASC')
            ->pluck('id');

        $i = 1;

        foreach ($tasks as $id) {

            Todo::where('id', $id)
                ->where('sort_order', '!=', $i)
                ->update(['sort_order' => $i]);

            $i++;
        }
    }

    public function todobulkstatusupdate(Request $request) {
        $idsv = $request->bulktodo_id;
        $posts = Todo::wherein('id',$idsv)->get();

        foreach ($posts as $post) {
            $post->task_status = $request->task_status;

            if ($request->task_status = 'New Task') {
                $post->is_completed = false;
            }elseif ($request->task_status = 'In Process') {
                $post->is_completed = false;
            }elseif ($request->task_status = 'Complete') {
                $post->is_completed = true;
            }elseif ($request->task_status = 'Achieved') {
                $post->is_completed = true;
            }

            $post->save();
        }

        $data = [
            'message' => "Bulk Status updated!"
        ];

        return response()->json($data);

    }

    public function todobulkpriorityupdate(Request $request) {
        $idsv = explode(",",$request->bulktodo_id);
        $posts = Todo::wherein('id',$idsv)->get();

        foreach ($posts as $post) {
            $post->reminder_cycle = $request->reminder_cycle;
            $post->save();
        }

        $data = [
            'message' => "Bulk Priority updated!"
        ];

        return response()->json($data);
    }

    public function todobulkassigntoupdate(Request $request){
        $idsv = explode(",",$request->bulktodo_id);
        $posts = Todo::wherein('id',$idsv)->get();

        foreach ($posts as $post) {
            $post->assignto_id = implode(",",$request->assignto_id);
            $post->save();
        }

        $data = [
            'message' => "Bulk Assignto updated!"
        ];

        return response()->json($data);
    }

    public function todobulkdelete(Request $request) {
        $idsv = explode(',',$request->bulktodo_id);
        $posts = Todo::wherein('id',$idsv)->get();

        foreach ($posts as $post) {
            $post->delete();
        }

        // $data = [
        //     'message' => "Bulk todo deleted!"
        // ];

        return response()->json(['success' => 'Todo successfully deleted!']);
    }
    
    public function saveshortformcode(Request $request){
        $checkFilter = Todoadminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();
        if (isset($checkFilter)) {
            $checkFilter->short_form_code = $request->short_form_code;
            $checkFilter->save();

            $data = [
                'res' => 'Short Form update successfully!'
            ];

        }else{
            $saveFilter = new Todoadminsavefilter();
            $saveFilter->admin_id = Auth::guard('admin')->user()->id;
            $saveFilter->short_form_code = $request->short_form_code;
            $saveFilter->save();

            $data = [
                'res' => 'Short Form update successfully!'
            ];
        }

        return response()->json($data);
    }

    // Save Filter
    public function saveFilter(Request $request) {

        $checkFilter = Todoadminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

        if (isset($checkFilter)) {
            if ($request->department_id != '') {
                $checkFilter->department_id = implode(",",$request->department_id);
            } else {
                $checkFilter->department_id = "";
            }

            if ($request->todolabel_id != '') {
                $checkFilter->todolabel_id = implode(",",$request->todolabel_id);
            }else {
                $checkFilter->todolabel_id = "";
            }

            if ($request->reminder_cycle != '') {
                $checkFilter->priority = implode(",",$request->reminder_cycle);
            } else {
                $checkFilter->priority = "";
            }

            if ($request->task_status != '') {
                $checkFilter->task_status = implode(",",$request->task_status);
            }else {
                $checkFilter->task_status = "";
            }

            if ($request->assignto_id != '') {
                $checkFilter->assignto_id = implode(",",$request->assignto_id);
            } else {
                $checkFilter->assignto_id = "";
            }

            if($request->followup_before != ''){
                $checkFilter->followup_before = $request->followup_before;
            } else {
                $checkFilter->followup_before = "";
            }

            if ($request->created_by != '') {
                $checkFilter->created_by_id = implode(",",$request->created_by);
            } else {
                $checkFilter->created_by_id = "";
            }

            if ($request->type_fi != '') {
                $checkFilter->type = implode(",",$request->type_fi);
            } else {
                $checkFilter->type = "";
            }

            if ($request->start_on != '') {
                $checkFilter->start_date = $request->start_on;
            } else {
                $checkFilter->start_date = "";
            }

            if ($request->finish_on != '') {
                $checkFilter->finish_date = $request->finish_on;
            }else {
                $checkFilter->finish_date = "";
            }

            if ($request->complete_date != '') {
                $checkFilter->complete_date_range = $request->complete_date;
            } else {
                $checkFilter->complete_date_range = "";
            }

            if ($request->achieved_date != '') {
                $checkFilter->achieved_date_range = $request->achieved_date;
            } else {
                $checkFilter->achieved_date_range = "";
            }

            if ($request->created_at != '') {
                $checkFilter->created_date_range = $request->created_at;
            } else {
                $checkFilter->created_date_range = "";
            }

            if ($request->updated_at != '') {
                $checkFilter->updated_date_range = $request->updated_at;
            } else {
                $checkFilter->updated_date_range = "";
            }

            $checkFilter->today_reminder_task = $request->today_reminder_task ?? 0;

            $checkFilter->save();
            $data = [
                'res' => 'Filter update successfully!'
            ];




        } else {
            $saveFilter = new Todoadminsavefilter();
            $saveFilter->admin_id = Auth::guard('admin')->user()->id;

            if ($request->department_id != '') {
                $saveFilter->department_id = implode(",",$request->department_id);
            } else {
                $saveFilter->department_id = "";
            }

            if ($request->todolabel_id != '') {
                $saveFilter->todolabel_id = implode(",",$request->todolabel_id);
            } else {
                $saveFilter->todolabel_id = "";
            }

            if ($request->reminder_cycle != '') {
                $saveFilter->priority = implode(",",$request->reminder_cycle);
            } else {
                $saveFilter->priority = "";
            }

            if ($request->task_status != '') {
                $saveFilter->task_status = implode(",",$request->task_status);
            } else {
                $saveFilter->task_status = "";
            }

            if ($request->assignto_id != '') {
                $saveFilter->assignto_id = implode(",",$request->assignto_id);
            } else {
                $saveFilter->assignto_id = "";
            }

            if($request->followup_before != ''){
                $saveFilter->followup_before = $request->followup_before;
            } else {
                $saveFilter->followup_before = "";
            }

            if ($request->created_by != '') {
                $saveFilter->created_by_id = implode(",",$request->created_by);
            } else {
                $saveFilter->created_by_id = "";
            }

            if ($request->type_fi != '') {
                $saveFilter->type = implode(",",$request->type_fi);
            } else {
                $saveFilter->type = "";
            }

            if ($request->start_on != '') {
                $saveFilter->start_date = $request->start_on;
            } else {
                $saveFilter->start_date = "";
            }

            if ($request->finish_on != '') {
                $saveFilter->finish_date = $request->finish_on;
            } else {
                $saveFilter->finish_date = "";
            }

            if ($request->complete_date != '') {
                $saveFilter->complete_date_range = $request->complete_date;
            } else {
                $saveFilter->complete_date_range = "";
            }

            if ($request->achieved_date != '') {
                $saveFilter->achieved_date_range = $request->achieved_date;
            }else {
                $saveFilter->achieved_date_range = "";
            }

            if ($request->created_at != '') {
                $saveFilter->created_date_range = $request->created_at;
            } else {
                $saveFilter->created_date_range = "";
            }

            if ($request->updated_at != '') {
                $saveFilter->updated_date_range = $request->updated_at;
            } else {
                $saveFilter->updated_date_range = "";
            }

            $saveFilter->today_reminder_task = $request->today_reminder_task ?? 0;

            // $saveFilter->page_list = $request->page_list;
            // $saveFilter->search_text = $request->search_text;


            $saveFilter->save();
            $data = [
                'res' => 'Filter save successfully!'
            ];

        }

        return response()->json($data);

    }

    // Reset Filter
    public function resetfilter(Request $request){
        $checkFilter = Todoadminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

        if (isset($checkFilter)) {
            $checkFilter->department_id = "";
            $checkFilter->todolabel_id = "";
            $checkFilter->priority = "";
            $checkFilter->task_status = "";
            $checkFilter->assignto_id = "";
            $checkFilter->followup_before = "";
            $checkFilter->created_by_id = "";
            $checkFilter->type = "";
            $checkFilter->start_date = "";
            $checkFilter->finish_date = "";
            $checkFilter->complete_date_range = "";
            $checkFilter->achieved_date_range = "";
            $checkFilter->created_date_range = "";
            $checkFilter->updated_date_range = "";
            $checkFilter->today_reminder_task = 0;

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

    // Todo Label
    public function todolabelList() {
        $posts = Todolabel::orderBy('name','DESC')->get();

        return view('admin.todolabel.index',compact('posts'));

    }

    public function todolabelListJson(Request $request) {
        $post = DB::table('todolabels as todolabel')
        ->leftjoin('admins as admin','admin.id','=','todolabel.admin_id')
        ->select('todolabel.*','admin.name as uname')
        ->orderBy('todolabel.name')
        ->get();

        $data['data'] = $post;
        return response()->json($data);
    }

    public function todolabelStore(Request $request) {
        $post = new Todolabel();
        $post->name = $request->name;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Todo Label Added!');

    }

    public function todolabelEdit(Request $request) {
        $post = Todolabel::find($request->id);

        return response()->json($post);
    }

    public function todolableUpdate(Request $request) {
        $post = Todolabel::find($request->edit_id);
        $post->name = $request->name;
        $post->save();

        return redirect()->back()->with('success','Todo Label updated!');
    }

    public function labelcheckname(Request $request) {
        $name = $request->name;

        if(isset($request->id) && $request->id != ''){
            $post = Todolabel::where('name','=',$name)->where('id','!=',$request->id)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        }else{
            $post = Todolabel::where('name','=',$name)->count();
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

    // Todo Department Categoru
    public function departmentList() {
        $posts = Todolabel::orderBy('name','DESC')->get();

        return view('admin.department.index',compact('posts'));

    }

    public function departmentListJson(Request $request) {
        $post = DB::table('departments as department')
        ->leftjoin('admins as admin','admin.id','=','department.admin_id')
        ->select('department.*','admin.name as uname')
        ->orderBy('department.name')
        ->get();

        $data['data'] = $post;
        return response()->json($data);
    }

    public function departmentStore(Request $request) {
        $post = new Department();
        $post->name = $request->name;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back()->with('success','Department Added!');

    }

    public function departmentEdit(Request $request) {
        $post = Department::find($request->id);

        return response()->json($post);
    }

    public function departmentUpdate(Request $request) {
        $post = Department::find($request->edit_id);
        $post->name = $request->name;
        $post->save();

        return redirect()->back()->with('success','Department updated!');
    }

    public function departmentcheckname(Request $request) {
        $name = $request->name;

        if(isset($request->id) && $request->id != ''){
            $post = Department::where('name','=',$name)->where('id','!=',$request->id)->count();

            if ($post == 0) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));

        }else{
            $post = Department::where('name','=',$name)->count();
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

    // Swicth To Kanban or List
    public function switchto(Request $request){
        $checkFilter = Todoadminsavefilter::where('admin_id','=',Auth::guard('admin')->user()->id)->first();
        if ($request->switch == 'kanban') {
            $switch = true;
            $data = [
                'res' => 'Kanban is available!'
            ];
        } elseif ($request->switch == 'todolist') {

            $switch = false;
            $data = [
                'res' => 'Todo List is available!'
            ];
        }

        if (isset($checkFilter)) {
            $checkFilter->switch_to = $switch;
            $checkFilter->save();
        } else {
            $todoPost = new Todoadminsavefilter();
            $todoPost->admin_id = Auth::guard('admin')->user()->id;
            $todoPost->switch_to = $switch;
            $todoPost->save();
        }

        return response()->json($data);

    }

    public function todointervalpopup(){
        $todayDate = date('Y-m-d');
        $todyaTime = date('H:i');
        $userID = Auth::guard('admin')->user()->id;



        $posts = Todo::with(['department','todolabel','admin'])
        ->where('type','!=','Recruiting')
        ->where('scheduled_status','!=',1)
        ->where('is_completed',false)
        ->whereRaw('FIND_IN_SET(?,assignto_id)',[$userID])
        ->get();

        if (count($posts) > 0) {
           $data = [
            'status' => 'true',
            'response_message' => 'Your '.count($posts).' task are pending!, please complete ASAP'
           ];
        } else {
           $data = [
            'status' => 'false',
            'response_message' => ''
           ];
        }


        return response()->json($data);

    }

    public function rescheduleTaskReminderView($taskId, Request $request)
    {
        // 1️⃣ Fetch the task
        $task = Todo::findOrFail($taskId);
            // 3️⃣ Show the reschedule form
        return view('admin.todo.reschedule_task_reminder', compact('task'));
    }

    public function reminderRescheduleUpdate(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:todos,id',
            'reminder_before_option' => 'required',
            'custom_reminder_at' => 'nullable|date',
        ]);
    
        $task = Todo::findOrFail($request->task_id);
    
        $option = $request->reminder_before_option;
    
        if (is_numeric($option)) {
            // 🕒 Case 1: Reminder before in minutes
            $task->reminder_before = (int) $option;
            $task->reminder_at = now()->addMinutes((int) $option);
    
        } elseif ($option === 'next_day') {
            // 🌅 Case 2: Next Day (same time tomorrow)
            $task->reminder_before = 'next_day';
            $task->reminder_at = now()->addDay()->setTimeFromTimeString(now()->format('H:i:s'));
    
        } elseif ($option === 'custom' && $request->filled('custom_reminder_at')) {
            // 📅 Case 3: Custom date & time
            $task->reminder_before = 'custom';
            $task->reminder_at = $request->custom_reminder_at.':00';
        }
    
        $task->save();
    
        return redirect()
        ->route('admin.todo.reschedule', ['taskId' => $task->id])
        ->with('success', 'Reminder updated successfully!');
    }
    

}






