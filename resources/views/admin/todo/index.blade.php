@extends('layout.admin.admin_layout')

@section('title','Todo List')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/jkanban/jkanban.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/pages/app-kanban.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/quill/typography.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/quill/katex.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/quill/editor.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}">
    
    <style>
        /* ==============================
        INPUT / FILTER STYLE
        ============================== */

        .pagestyle {
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .pagestyle:focus {
            color: #6e6b7b;
            background-color: #fff;
            border-color: #7467f0;
            outline: 0;
            box-shadow: 0 3px 10px rgba(34, 41, 47, 0.1);
        }

        .fixedTodokanbanFilter {
            position: relative;
            z-index: 2;
            width: 100% !important;
        }

        /* ==============================
        MAIN KANBAN CONTAINER
        ============================== */

        /* No overflow here on purpose — .card-body-kanban-row below is the
        single scroll owner for the whole board (both axes). Giving this
        row its own overflow-x too would create a second, nested scroll
        container fighting the outer one. */
        .todoKanban {
            white-space: nowrap;
            padding-top: 1rem;
            padding-left: 0;
        }

        /* ==============================
        KANBAN COLUMN
        ============================== */

        .todoKanban .col-kanban {
            display: inline-block;
            vertical-align: top;
            float: none;
            width: 340px;
            margin-right: 8px;
        }

        /* ==============================
        CARD CONTAINER
        ============================== */

        /* No max-height/overflow-y here on purpose: a per-column viewport-
        relative scrollbox was fighting the page's own scroll (nested-scroll
        conflict) and its calc(100vh - 260px) offset went stale the moment
        content was added above the board. Columns now grow naturally and
        the page scrolls vertically; .todoKanban below still scrolls the
        board horizontally so all columns stay reachable. */
        .kanban-todo {
            padding-bottom: 10px;
            padding-right: 6px;
        }

        /* ==============================
        CARDS
        ============================== */

        .kanban-card {
            cursor: grab;
            margin-bottom: 8px;
            transition: transform 0.2s ease;
        }

        .kanban-card:hover {
            transform: scale(1.02);
        }

        .todo-desc-toggle {
            display: inline-block;
            font-size: 11px;
            text-decoration: none;
            white-space: nowrap;
        }

        .todo-desc-toggle:hover {
            text-decoration: underline;
        }

        /* ==============================
        STICKY STATUS HEADER
        ============================== */

        .statusesRow {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* ==============================
        PAGINATION WRAPPER
        ============================== */

        .todoPaginate {
            padding: 0;
        }

        /* ==============================
        KANBAN BODY FIX
        ============================== */

        /* The kanban board's one and only scroll container — height is set
        dynamically in JS (sizeKanbanBoard()) to the actual remaining
        viewport space below wherever this box starts, so it stays correct
        regardless of what's above it (status bar, filters, etc.) instead
        of a hardcoded/stale calc(). Scrolls both axes: vertically for
        columns taller than the box, horizontally to reach every column. */
        .card-body-kanban-row {
            position: relative;
            top: 0;
            padding-left: 50px;
            overflow: auto;
            min-height: 0;
        }

        /* Purely a layout pass-through: natural size, no clipping of its
        own — .card-body-kanban-row above owns the actual scrolling. */
        .kanban-row {
            height: auto;
            overflow: visible;
        }

        /* ==============================
        LOAD MORE BUTTON
        ============================== */

        .load-more-kanban {
            position: sticky;
            bottom: 10px;
            background: #fff;
            z-index: 2;
        }

        /* ==============================
        STATUS HEADER STYLE
        ============================== */

        .card-status {
            max-height: 20px !important;
            z-index: 1;
        }

        /* ==============================
        CUSTOM BUTTON
        ============================== */

        .optionBtnCustom {
            height: 24.6px;
        }

        /* ==============================
        FILTER INDICATOR
        ============================== */

        .filter-indicator {
            position: absolute;
            top: 1px;
            right: 2px;
            width: 8px;
            height: 8px;
            background: #ffc107;
            border-radius: 50%;
        }

        /* ==============================
        TODO STATUS/PRIORITY/FOLLOW-UP BAR
        Compact toolbar layout: one row per group, label inline to the
        left of its pills instead of stacked above them, so the whole
        bar takes a fraction of the vertical space the boxed-card
        version used while keeping every pill/count/click target as-is.
        ============================== */

        .todo-filterbar {
            padding: 8px 14px;
        }

        .todo-filterbar-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            padding: 5px 0;
        }

        .todo-filterbar-row + .todo-filterbar-row {
            border-top: 1px solid var(--bs-border-color, rgba(0, 0, 0, .06));
        }

        .todo-filterbar-label {
            flex: 0 0 auto;
            min-width: 74px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: var(--bs-secondary-color, #6c757d);
        }

        .candidate-status-grid {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
            flex: 1 1 auto;
            min-width: 0;
        }

        .status-item {
            flex: 0 0 auto;
        }

        .status-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            min-height: 24px;
            font-size: 11px;
            line-height: 1;
            white-space: nowrap;
            border: 1px solid;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
        }

        .status-btn strong {
            font-size: 13px;
            line-height: 1;
        }

        .status-btn:hover {
            background: rgba(115, 103, 240, 0.05);
            text-decoration: none;
        }

        .status-btn.active {
            background: var(--tsc-color, #7367f0);
        }

        .status-btn.active span,
        .status-btn.active strong {
            color: #fff !important;
        }

        @media (max-width: 575px) {
            .todo-filterbar-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .todo-filterbar-label {
                min-width: 0;
            }
        }

        /* ==============================
        DROPDOWN FIX
        ============================== */

        .dropdown-custom.show .dropdown-menu {
            transform: none !important;
            z-index: 100;
        }

        /* ==============================
        SKELETON LOADER
        ============================== */

        .kanban-skeleton {
            background: linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 37%,#f0f0f0 63%);
            background-size: 400% 100%;
            animation: shimmer 1.2s infinite;
            height: 110px;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        @keyframes shimmer {
            0% { background-position: -400px 0 }
            100% { background-position: 400px 0 }
        }

      /* ==============================
        RESPONSIVE
        ============================== */

        @media (max-width:1200px){

            .fixedTodokanbanFilter{
                position:fixed;
                z-index:2;
                width:96%;
            }

            .todokanbanmaindiv{top:128px;}
            .Kanban-filter-today-reminder{top:10px;}

            .search-box{
                position:relative;
                right:95px;
                top:13px;
            }

            .disTodoKanban{height:100%;}

            .load-more-kanban-box{
                position:relative;
                bottom:45px;
            }

            .Todolistsecrow{
                position:relative;
                left:-15px;
                padding-left:0 !important;
            }

            .today-switch{top:10px;}

            .btn-group-custom{left:-127px;}

            .search_table_field{
                position:relative;
                right:148px;
                top:10px;
            }

            .todolistaddtodobtn{
                top:40px;
                right:90px;
            }

            .Todo-filter-today-reminder{
                top:34px;
                right:79px;
            }

            .switch-label{
                display:ruby-text;
                top:4px;
            }

            .todoKanban .col-kanban{
                width:300px;
            }
        }

        @media (max-width:992px){
            .todoKanban .col-kanban{
                width:260px;
            }
        }

        @media (max-width:768px){
            .todoKanban .col-kanban{
                width:220px;
            }
        }

        @media (max-width:576px){

            .todoKanban .col-kanban{
                width:180px;
            }

            .kanban-card h6{
                font-size:.85rem;
            }

            .kanban-card p{
                font-size:.75rem;
            }
        }

       
    </style>

    <style id="dynamicStyle">
        /* Default style (optional) */
    </style>
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y todo-wrapper">
        @php
            if(empty($saveadminfilter)){
                $listDisplay = '';
                $kanbanDisplay = 'none;';
            }else{
                if($saveadminfilter->switch_to == 1){
                    $listDisplay = 'none;';
                    $kanbanDisplay = '';
                }else{
                    $listDisplay = '';
                    $kanbanDisplay = 'none;';
                }
            }

            $todoStatusColors = [
                'New Task'     => '#00CFE8',
                'In Process'   => '#7367F0',
                'Complete'     => '#FF9F43',
                'Achieved'     => '#28C76F',
                'Always'       => '#28C76F',
                'Not Required' => '#00CFE8',
                'Incomplete'   => '#EA5455',
                'On Hold'      => '#6C757D',
                'Cancelled'    => '#4B4B4B',
            ];

            // Status is single-select (radio-button semantics), so exactly
            // one pill reflects the real current filter on first paint —
            // the "All" pill was previously hardcoded active regardless of
            // an actually-applied saved status filter.
            $activeTodoStatus = '';
            if (!empty($saveadminfilter) && $saveadminfilter->task_status) {
                $activeTodoStatus = trim(explode(',', $saveadminfilter->task_status)[0] ?? '');
            }
        @endphp

        <!-- Todo Status Summary Bar Start -->
        <div class="card mb-3" id="todoStatusBar">
            <div class="card-body todo-filterbar">

                <div class="todo-filterbar-row">
                    <span class="todo-filterbar-label">Status</span>
                    <div class="candidate-status-grid">
                        <div class="status-item">
                            <a href="javascript:;" class="status-btn todoStatusCard{{ $activeTodoStatus === '' ? ' active' : '' }}"
                               style="border-color:#6C757D;color:#6C757D;--tsc-color:#6C757D;"
                               data-group="status" data-field="task_status" data-value="">
                                <span>All</span>
                                <strong>{{ number_format($todoStatusSummary['total'] ?? 0) }}</strong>
                            </a>
                        </div>

                        @foreach (($todoStatusSummary['statuses'] ?? []) as $statusName => $statusCount)
                            @php $todoStatusColor = $todoStatusColors[$statusName] ?? '#6C757D'; @endphp
                            <div class="status-item">
                                <a href="javascript:;" class="status-btn todoStatusCard{{ $activeTodoStatus === $statusName ? ' active' : '' }}"
                                   style="border-color:{{ $todoStatusColor }};color:{{ $todoStatusColor }};--tsc-color:{{ $todoStatusColor }};"
                                   data-group="status" data-field="task_status" data-value="{{ $statusName }}">
                                    <span>{{ $statusName }}</span>
                                    <strong>{{ number_format($statusCount) }}</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="todo-filterbar-row">
                    <span class="todo-filterbar-label">Priority</span>
                    <div class="candidate-status-grid">
                        @php
                            $todoPriorityColors = ['High' => '#EA5455', 'Medium' => '#FF9F43', 'Low' => '#00CFE8'];
                        @endphp
                        @foreach ($todoPriorityColors as $priorityName => $priorityColor)
                            <div class="status-item">
                                <a href="javascript:;" class="status-btn todoStatusCard"
                                   style="border-color:{{ $priorityColor }};color:{{ $priorityColor }};--tsc-color:{{ $priorityColor }};"
                                   data-group="priority" data-field="reminder_cycle" data-value="{{ $priorityName }}">
                                    <span>{{ $priorityName }}</span>
                                    <strong>{{ number_format($todoStatusSummary['priorities'][$priorityName] ?? 0) }}</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="todo-filterbar-row">
                    <span class="todo-filterbar-label">Follow-up Due</span>
                    <div class="candidate-status-grid">
                        @php
                            $todoFollowupOptions = [
                                'before_today' => ['label' => 'Before Today', 'color' => '#EA5455'],
                                'today' => ['label' => 'Today', 'color' => '#FF9F43'],
                                'upcoming' => ['label' => 'Upcoming', 'color' => '#28C76F'],
                            ];
                        @endphp
                        @foreach ($todoFollowupOptions as $followupKey => $followupOption)
                            <div class="status-item">
                                <a href="javascript:;" class="status-btn todoStatusCard"
                                   style="border-color:{{ $followupOption['color'] }};color:{{ $followupOption['color'] }};--tsc-color:{{ $followupOption['color'] }};"
                                   data-group="followup" data-field="followup_due" data-value="{{ $followupKey }}">
                                    <span>{{ $followupOption['label'] }}</span>
                                    <strong>{{ number_format($todoStatusSummary['followup'][$followupKey] ?? 0) }}</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
        <input type="hidden" id="by-todo-followup-due" value="">
        <!-- Todo Status Summary Bar End -->
        <!-- Todo Short Form Start -->
        <div class="card mb-3 hideshowaddmodule" id="hideshowaddmodule" @if(isset($saveadminfilter) && $saveadminfilter->short_form_code == 1) @else style="display: none" @endif>
            <div class="card-body pb-0">
                <form action="{{ route('admin.todo.store') }}" id="shortTodosaveValidation" method="POST" class="shortform-div">
                    @csrf
                    <input type="hidden" name="insert_from" value="todo-short-form">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-short-subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="task_title" id="add-short-subject" class="form-control" placeholder="Enter Subject..." required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-short-desc" class="form-label">Task Description <span class="text-danger">*</span></label>
                                <input type="text" name="task_description" id="add-short-desc" class="form-control" placeholder="Enter task description..." required>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="add-short-priority" class="form-label">Priority</label>
                                <select name="reminder_cycle" id="add-short-priority" class="form-select select22">
                                    <option value="">Select</option>
                                    <option value="High">High</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Low">Low</option>
                                    <option value="Urgent">Urgent</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="add-short-assignee" class="form-label">Assignee</label>
                                <select name="assignto_id[]" id="add-short-assignee" class="form-select select22" multiple>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}"
                                            @if(Auth::guard('admin')->user()->id == $staff->id) selected @endif>
                                            {{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="mb-3 mt-4">
                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Todo Short Form End -->

        <!-- Todo List Start -->
        <div class="card disTodoList" style="display: {{$listDisplay}}">
            
            <div class="card-header py-3 px-4 TodoLostHeader">

                <div class="float-start">
                    <select id="pagination_list" class="form-select form-select-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                    </select>
                </div>
         
                <div class="px-3 float-start d-flex align-items-center gap-2 Todolistsecrow">
                    <!-- Kanban Button -->
                    <button class="btn btn-xs btn-primary changetokanban" 
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="Switch to Kanban">
                        <i class="ti ti-layout-kanban me-0 me-sm-1 ti-xs"></i>
                    </button>

                    <!-- Filter Button -->
                    <button class="btn btn-xs btn-primary filterpanel" 
                        data-bs-toggle="modal" 
                        data-bs-target="#filterpanel">
                        <i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filters
                        <span class="filter-indicator d-none"></span>
                    </button>

                    <!-- Option Button -->
                    <div class="btn-group mx-1">
                        <button class="btn btn-primary btn-xs dropdown-toggle optionBtnCustom" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" name="short_form_code" value="1" id="short_form_code2" class="switch-input option-switch-input" @if(isset($saveadminfilter) && $saveadminfilter->short_form_code == 1) checked @endif />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Short Form</span>
                                </label>
                            </div>

                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" value="1" class="switch-input todo-status-bar-switch" checked />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Status Bar</span>
                                </label>
                            </div>

                        </div>
                    </div>

                    <!-- 🌗 Toggle Switch -->
                    <label class="switch mb-0 Todo-filter-today-reminder" data-bs-toggle="tooltip" data-bs-placement="top">
                        <input type="checkbox" class="switch-input filterData filter-today-reminder" value="0" @if(isset($saveadminfilter) && $saveadminfilter->today_reminder_task == 1) checked @endif>
                        <span class="switch-toggle-slider today-switch">
                            <span class="switch-on"></span>
                            <span class="switch-off"></span>
                        </span>
                        <span class="switch-label ms-1 fw-bold">Today Reminder</span>
                    </label>
                </div>

                
                <div class="float-end">
                    <div class="btn-group mx-2 btn-group-custom">
                        <button class="btn btn-primary btn-sm dropdown-toggle bulkactions" type="button" disabled id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-list-check me-0 me-sm-1 ti-xs"></i> Bulk Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1))
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkchangestatus"><i class="ti ti-users me-2"></i> Update Status</a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkchangepriority"><i class="ti ti-user me-2"></i> Update Priority</a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkassignto"><i class="ti ti-user me-2"></i> Assign To</a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkdelete"><i class="ti ti-user me-2"></i> Delete</a>

                            @elseif (Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0))
                                @if ($permission->bulk_todo_update_status == 1)
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkchangestatus"><i class="ti ti-users me-2"></i> Update Status</a>
                                @endif

                                @if ($permission->bulk_todo_update_priority == 1)
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkchangepriority"><i class="ti ti-user me-2"></i> Update Priority</a>
                                @endif

                                @if ($permission->bulk_todo_update_assignto == 1)
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkassignto"><i class="ti ti-user me-2"></i> Assign To</a>
                                @endif

                                @if ($permission->bulk_todo_delete == 1)
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkdelete"><i class="ti ti-user me-2"></i> Delete</a>
                            @endif
                            @endif

                        </div>
                    </div>
                    @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1) || (isset($permission) && $permission->full_access == 0 && $permission->todo_add))
                    <button class="add-new btn btn-sm btn-primary addcontact addcandidate mx-1 todolistaddtodobtn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New Task</span></button>

                    @endif
                </div>
                <div class="float-end">
                    <input type="text" id="search_text" class="form-control search_text form-control-sm search_table_field" placeholder="Search...">
                </div>
            </div>

            <div class="card-datatable table-responsive contactpaginate">
                @include('admin.todo.load')
            </div>
        </div>
        <!-- Todo List End -->

        <!-- Kanban Deal Pipeline Start -->
        <div class="app-kanban card disTodoKanban" style="display: {{$kanbanDisplay}}">

            <div class="card-header fixedTodokanbanFilter py-3 px-4" id="layout-navbar">

                <div class="float-start">
                    <!-- List Button -->
                    <button class="btn btn-xs btn-primary changetolist" data-bs-toggle="tooltip" data-bs-placement="top" title="Switch to List">
                        <i class="ti ti-list me-0 me-sm-1 ti-xs"></i>
                    </button>

                    <!-- Filter Button -->
                    <button class="btn btn-xs btn-primary filterpanel" data-bs-toggle="modal" data-bs-target="#filterpanel">
                        <i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filters
                        <span class="filter-indicator d-none"></span>

                    </button>

                    <!-- Option Button -->
                    <div class="btn-group mx-1">
                        <button class="btn btn-primary btn-xs dropdown-toggle optionBtnCustom" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" name="short_form_code" value="1" id="short_form_code2" class="switch-input option-switch-input" @if(isset($saveadminfilter) && $saveadminfilter->short_form_code == 1) checked @endif />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Short Form</span>
                                </label>
                            </div>

                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" value="1" class="switch-input todo-status-bar-switch" checked />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Status Bar</span>
                                </label>
                            </div>

                        </div>
                    </div>

                    <!-- 🌗 Toggle Switch -->
                    <label class="switch mb-0 Kanban-filter-today-reminder" data-bs-toggle="tooltip" data-bs-placement="top">
                        <input type="checkbox" class="switch-input filterData filter-today-reminder" value="0" @if(isset($saveadminfilter) && $saveadminfilter->today_reminder_task == 1) checked @endif>
                        <span class="switch-toggle-slider today-switch">
                            <span class="switch-on"></span>
                            <span class="switch-off"></span>
                        </span>
                        <span class="switch-label ms-1 fw-bold">Today Reminder</span>
                    </label>
                </div>

                <div class="search-box">

                    <div class="float-end">
                        <button class="add-new btn btn-sm btn-primary addcontact addcandidate mx-1" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
                            <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                            <span class="d-none d-sm-inline-block">Add New Task</span>
                        </button>
                    </div>

                    <div class="float-end">
                        <input type="text" id="search_text_kanban" class="form-control search_text form-control-sm" placeholder="Search...">
                    </div>
                </div>
            </div>


            <div class="card-body card-body-kanban-row contactpaginate todokanbanmaindiv">
                <div class="row kanban-row">
                    @include('admin.todo.kanban_load')
                </div>
            </div>
        </div>
        <!-- Kanban Deal Pipeline End -->

        <!--- Add Todo Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add New Task</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0 addNewUserForm" id="addNewUserForm" action="{{ route('admin.todo.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">

                        <!-- Work Frequency -->
                        <div class="col-md-4 mb-3">
                            <label for="add-type" class="form-label">Work Frequency</label>
                            <select name="type" id="add-type" class="form-select select22" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="Once" selected>Once</option>
                                <option value="Always">Always</option>
                            </select>
                        </div>

                        <!-- Department -->
                        <div class="col-md-4 mb-3">
                            <label for="add-department" class="form-label">Department <span class="text-danger">*</span></label>
                            <select name="department_id" id="add-department" class="form-select select22" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Label -->
                        <div class="col-md-4 mb-3">
                            <label for="add-label" class="form-label">Label <span class="text-danger">*</span></label>
                            <select name="todolabel_id" id="add-label" class="form-select select22" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($todoLabels as $todoLabel)
                                <option value="{{ $todoLabel->id }}">{{ $todoLabel->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subject -->
                        <div class="col-md-12 mb-3">
                            <label for="add-todo-subject" class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="task_title" class="form-control" id="add-todo-subject" maxlength="70" placeholder="Enter Todo Subject...">
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label for="add-task-desc" class="form-label">Task Description</label>
                            <textarea name="task_description" id="add-task-desc" class="form-control" rows="5" placeholder="Enter task description..."></textarea>
                        </div>

                        <!-- Staff Work -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">List of Work</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control add_staff_work_input" placeholder="Enter List of work...">
                            </div>
                            <ul class="list-group staffWorkList"></ul>
                            <!-- Add Button -->
                            <div class="text-end mb-2 add_staff_work_container">
                                <a href="javascript:void(0);" class="add_staff_work_btn">+ Add column</a>
                            </div>
                        </div>

                        <!-- Start & Finish Dates -->
                        <div class="col-md-6 mb-3">
                            <label for="add-start-date" class="form-label">Start On</label>
                            <input type="text" name="start_on" id="add-start-date" class="form-control flatpicker-date" placeholder="Select Start Date...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add-finish-date" class="form-label">Finish On</label>
                            <input type="text" name="finish_on" id="add-finish-date" class="form-control flatpicker-date" placeholder="Select Finish Date...">
                        </div>

                        <!-- Priority -->
                        <div class="col-md-12 mb-3">
                            <label for="add-priority" class="form-label">Priority</label>
                            <select name="reminder_cycle" id="add-priority" class="form-select select22">
                                <option value="">Select</option>
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>

                        <!-- Reminder Type -->
                        <div class="col-md-6 mb-3">
                            <label for="add-reminder-type" class="form-label">Reminder Type</label>
                            <select name="reminder_type" id="add-reminder-type" class="form-select select22">
                                <option value="">Select</option>
                                <option value="OneTime">One Time</option>
                                <option value="Recurring">Recurring</option>
                                <option value="Custom">Custom</option>
                                <option value="None">None</option>
                            </select>
                        </div>
                      
                        <!-- OneTime Reminder -->
                        <div class="col-md-6 mb-3" id="addispSchTime" style="display:none;">
                            <label for="add-start-on-time" class="form-label">Scheduled Date & Time</label>
                            <input type="text" name="scheduled_date_time" id="add-start-on-time" class="form-control jsSingledatepicker">
                        </div>


                        <!-- Recurring Reminder -->
                        <div class="col-md-6 mb-3" id="addisRecurring" style="display:none;">
                            <label for="add-recurring-type" class="form-label">Recurring Type</label>
                            <select name="recurring_type" id="add-recurring-type" class="form-select select22">
                                <option value="">Select</option>
                                <option value="Daily">Daily</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Yearly">Yearly</option>
                            </select>

                            <!-- Daily Multiple Times -->
                            <div class="mt-2" id="recurring-daily-time" style="display:none;">
                                <label class="form-label">Time(s)</label>

                                <div id="daily-times-container">
                                    <div class="input-group mb-2 daily-time-group">
                                        <input type="text" name="recurring_time_daily[]" class="form-control flatpickertime" placeholder="Select time">
                                        <button type="button" class="btn btn-outline-danger remove-daily-time-btn" style="display:none;">✕</button>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="add-daily-time-btn">
                                    + Add More Time
                                </button>
                            </div>

                            <!-- Weekly Days + Multiple Times -->
                            <div class="mt-2" id="recurring-weekly-days" style="display:none;">
                                <label class="form-label">Select Week Days</label>
                                <select name="recurring_weekdays[]" class="form-select select22" multiple>
                                    <option value="Mon">Mon</option>
                                    <option value="Tue">Tue</option>
                                    <option value="Wed">Wed</option>
                                    <option value="Thu">Thu</option>
                                    <option value="Fri">Fri</option>
                                    <option value="Sat">Sat</option>
                                    <option value="Sun">Sun</option>
                                </select>

                                <label class="form-label mt-2">Time(s)</label>

                                <!-- Container for multiple time fields -->
                                <div id="weekly-times-container">
                                    <div class="input-group mb-2 time-group">
                                        <input type="text" name="recurring_time_weekly[]" class="form-control flatpickertime" placeholder="Select time">
                                        <button type="button" class="btn btn-outline-danger remove-time-btn" style="display:none;">✕</button>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="add-weekly-time-btn">
                                    + Add More Time
                                </button>
                            </div>

                            <!-- Monthly Date + Time -->
                            <div class="mt-2" id="recurring-monthly" style="display:none;">
                                <label class="form-label">Select Day of Month</label>
                                <input type="text" name="recurring_month_day" class="form-control flatpickerday">
                                <label class="form-label mt-2">Time</label>
                                <input type="text" name="recurring_time_monthly" class="form-control flatpickertime">
                            </div>

                            <!-- Yearly Month-Day + Time -->
                            <div class="mt-2" id="recurring-yearly" style="display:none;">
                                <label class="form-label">Select Month & Day</label>
                                <input type="text" name="recurring_year_month_day" class="form-control jsMonthDayPicker" placeholder="MM-DD">
                                <label class="form-label mt-2">Time</label>
                                <input type="text" name="recurring_time_yearly" class="form-control flatpickertime">
                            </div>
                        </div>

                        <!-- Custom Reminder -->
                        <div class="col-md-6 mb-3" id="addisCustom" style="display:none;">
                            <label class="form-label">Custom Date Range</label>
                            <input type="text" id="custom-date-range-picker" name="custom_date_range" class="form-control jsCustomDateRangePicker">
                            <label class="form-label mt-2">Time of Day</label>
                            <input type="text" id="custom-time" name="custom_time" class="form-control flatpickertime">
                        </div>

                         <!-- Notification Type -->
                         <div class="col-md-12 mb-3 notification-type-container" style="display:none;">
                            <label class="form-label d-block">Notification Through</label>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" 
                                    type="checkbox" 
                                    name="notification_type[]" 
                                    id="notify_whatsapp" 
                                    value="whatsapp">
                                <label class="form-check-label" for="notify_whatsapp">
                                    WhatsApp
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" 
                                    type="checkbox" 
                                    name="notification_type[]" 
                                    id="notify_email" 
                                    value="email">
                                <label class="form-check-label" for="notify_email">
                                    Email
                                </label>
                            </div>
                        </div>

                        <!-- Assignee -->
                        <div class="col-md-6 mb-3">
                            <label for="add-assignee" class="form-label">Assignee</label>
                            <select name="assignto_id[]" id="add-assignee" class="form-select select22" multiple>
                                @foreach ($staffs as $staff)
                                <option value="{{ $staff->id }}" @if(Auth::guard('admin')->user()->id == $staff->id) selected @endif>{{ $staff->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="add-support-team-name" class="form-label">Support Team Name</label>
                            <input type="text" name="support_team_name" class="form-control add_support_team_name" id="add_support_team_name" placeholder="Enter Support Team Name...">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="add-assigneesupport-team-number" class="form-label">Support Team Number</label>
                            <input type="number" name="support_team_number" class="form-control add_support_team_number"  id="add_support_team_number" placeholder="Enter Support Team Number...">
                        </div>

                        <!-- File Attachment -->
                        <div class="col-md-6 mb-3">
                            <label for="add-file-attachment" class="form-label">File Attachment</label>
                            <input class="form-control file-attachement-input" name="file_attachment" type="file" id="add-file-attachment">
                        </div>

                        <!-- Image Preview -->
                        <div class="col-md-6 mb-3">
                            <div id="dispIMG">
                                <img alt="user-avatar" src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" class="d-block w-px-100 h-px-100 rounded uploadedAvatar">
                            </div>
                        </div>

                    </div>

                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="addnewcandidateBtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Add Todo End --->

        <!--- View Todo Modal Start --->
        <div class="modal fade" id="viewTodoModal" tabindex="-1" aria-labelledby="viewTodoModalLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewTodoModalLabel">
                            View Todo Notes
                        </h5>

                        <button type="button" class="btn-close view-todo-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>
                       <!-- Title and Description (stacked vertically) -->
                    <div class="modal-body border-bottom pb-2">
                        <div class="mb-2">
                        <strong class="d-block text-heading text-bold TodoTitleClass"></strong>
                        </div>
                        <div>
                        <strong class="d-block text-heading text-bold TodoDescClass"></strong>
                        </div>
                    </div>

                    <div class="modal-body">

                        <div class="card mb-3">
                            <div class="card-header">
                                <ul class="nav nav-tabs nav-fill" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button type="button" class="nav-link active notes-tab-btn" role="tab" data-bs-toggle="tab" data-bs-target="#todo-notes" aria-controls="todo-notes" aria-selected="true">
                                            <i class="tf-icons ti ti-notes ti-xs me-1"></i> Notes
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button type="button" class="nav-link staff-work-tab-btn"
                                            data-bs-toggle="tab" data-bs-target="#todo-staff-work">
                                            <i class="tf-icons ti ti-briefcase ti-xs me-1"></i> Staff Work
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link detail-tab-btn" id="tab-details" data-bs-toggle="tab" data-bs-target="#todo-details" type="button" role="tab" aria-controls="todo-details" aria-selected="false">
                                            <i class="tf-icons ti ti-home ti-xs me-1"></i> Details
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link file-tab-btn" id="tab-file" data-bs-toggle="tab" data-bs-target="#todo-file" type="button" role="tab" aria-controls="todo-file" aria-selected="false">
                                            <i class="tf-icons ti ti-file ti-xs me-1"></i> File
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="tab-activity" data-bs-toggle="tab" data-bs-target="#todo-activity" type="button" role="tab" aria-controls="todo-activity" aria-selected="false">
                                            <i class="tf-icons ti ti-timeline ti-xs me-1"></i> Activity
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <input type="hidden" name="todo_id" id="view_todo_notes_id" value="">

                                    <!-- Notes Tab -->
                                    <div class="tab-pane fade show active" id="todo-notes" role="tabpanel" aria-labelledby="tab-notes">
                                        <div class="row">
                                            <!-- Add Note Form -->
                                            <div class="col-md-4">
                                                <form action="{{ route('admin.todonotes.store') }}" method="POST" id="todoNotesValidation">
                                                    @csrf
                                                    <div class="row">
                                                        <input type="hidden" name="todo_id" id="todo_notes_id" value="">

                                                        <div class="col-md-12">
                                                            <label for="add-todo-notes-tab" class="form-label">Notes <span class="text-danger">*</span></label>
                                                            <textarea name="notes" id="add-todo-notes-tab" cols="30" rows="5" class="form-control"></textarea>
                                                        </div>

                                                        <div class="col-md-12 mt-3">
                                                            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Notes Table -->
                                            <div class="col-md-8">
                                                <div class="table table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Notes</th>
                                                                <th>Created By</th>
                                                                <th>Created At</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="todoNotesTableBody">
                                                            <!-- Todo Notes will load dynamically here -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                     <!-- Staff Work -->
                                     <div class="tab-pane fade" id="todo-staff-work" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Staff</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="todoStaffTableBody">
                                                            <!-- Staff will load dynamically here -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Details Tab Content -->
                                    <div class="tab-pane fade" id="todo-details" role="tabpanel" aria-labelledby="tab-details">
                                        <div id="todoDetailsContainer" class="p-3 text-center text-muted">
                                            Click on “Details” to load Todo information...
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="todo-file" role="tabpanel" aria-labelledby="tab-file">
                                        <div class="p-3 text-center text-muted">No files available.</div>
                                    </div>

                                    <div class="tab-pane fade" id="todo-activity" role="tabpanel" aria-labelledby="tab-activity">
                                        <div class="p-3 text-center text-muted">No activity available.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer d-flex justify-content-between">

                        <button type="button" class="btn btn-primary btn-sm modalEditTodo">
                             Edit
                        </button>

                        <button type="button" class="btn btn-primary btn-sm view-todo-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            Close
                        </button>

                    </div>
                </div>
            </div>
        </div>
        <!--- View Todo Modal End --->

        <!-- Edit Todo Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasEditUser"
            aria-labelledby="offcanvasEditUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasEditUserLabel" class="offcanvas-title">Edit Task</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>

            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form id="editNewUserForm" action="{{ route('admin.todo.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="edit_id" id="editID">

                    <div class="row">

                        <!-- Work Frequency -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Work Frequency</label>
                                <select name="type" id="edit-type" class="form-select select22"
                                    data-placeholder="Select Work Frequency">
                                    <option value="">Select</option>
                                    <option value="Once">Once</option>
                                    <option value="Always">Always</option>
                                </select>
                            </div>
                        </div>

                        <!-- Department -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Department (Type Of Category) <span
                                        class="text-danger">*</span></label>
                                <select name="department_id" id="edit-department" class="form-select select22"
                                    data-placeholder="Select Department">
                                    <option value="">Select</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Label -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Label (Type of Work) <span class="text-danger">*</span></label>
                                <select name="todolabel_id" id="edit-label" class="form-select select22"
                                    data-placeholder="Select Label">
                                    <option value="">Select</option>
                                    @foreach ($todoLabels as $todoLabel)
                                        <option value="{{ $todoLabel->id }}">{{ $todoLabel->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="task_title" id="edit-todo-subject" maxlength="70" class="form-control"
                                    placeholder="Enter Todo Subject...">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Task Description</label>
                                <textarea name="task_description" id="edit-task-desc" class="form-control" rows="4"
                                    placeholder="Enter task description..."></textarea>
                            </div>
                        </div>

                        <!-- Staff Work -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">List of Work</label>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control edit_staff_work_input" placeholder="Enter List of work...">
                                </div>
                                <ul class="list-group EditStaffWorkList"></ul>
                                <!-- Add Button -->
                                <div class="text-end mb-2">
                                    <a href="javascript:void(0);" class="edit_staff_work_btn">+ Add Column</a>
                                </div>
                                <input type="hidden" name="edit_staff_work_input" class="edit_staff_work_input_hidden">
                            </div>
                        </div>

                        <!-- Start / Finish Dates -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start On</label>
                                <input type="text" name="start_on" id="edit-start-date"
                                    class="form-control flatpicker-date" placeholder="Select Start Date...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Finish On</label>
                                <input type="text" name="finish_on" id="edit-finish-date"
                                    class="form-control flatpicker-date" placeholder="Select Finish Date...">
                            </div>
                        </div>

                        <!-- Priority -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-priority" class="form-label">Priority</label>
                                <select name="reminder_cycle" id="edit-priority" class="form-select select22"
                                    data-allow-clear="true" data-placeholder="Select Priority">
                                    <option value="">Select</option>
                                    <option value="High">High</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Low">Low</option>
                                    <option value="Urgent">Urgent</option>
                                </select>
                            </div>
                        </div>

                        <!-- Reminder Type -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Reminder Type</label>
                                <select name="reminder_type" id="edit-reminder-type" class="form-select select22"
                                    data-placeholder="Select Reminder Type">
                                    <option value="">Select</option>
                                    <option value="OneTime">One Time</option>
                                    <option value="Recurring">Recurring</option>
                                    <option value="Custom">Custom</option>
                                    <option value="None">None</option>
                                </select>
                            </div>
                        </div>

                        <!-- OneTime Fields -->
                        <div class="col-md-6 reminder-section" id="edit-oneTime" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">Scheduled Date & Time</label>
                                <input type="text" name="scheduled_date_time" id="edit-scheduled-datetime"
                                    class="form-control jsSingledatepicker" placeholder="Select date & time...">
                            </div>
                        </div>


                        <!-- 🟦 Recurring Reminder -->
                        <div class="col-md-6 mb-3" id="edit-recurring-section" style="display:none;">
                            <label for="edit-recurring-type" class="form-label">Recurring Type</label>
                            <select name="recurring_type" id="edit-recurring-type" class="form-select select22">
                                <option value="">Select</option>
                                <option value="Daily">Daily</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Yearly">Yearly</option>
                            </select>

                           <!-- Daily Multiple Times -->
                            <div class="mt-2" id="edit-recurring-daily" style="display:none;">
                                <label class="form-label">Time(s)</label>

                                <div id="edit-daily-times-container">
                                    <!-- Dynamic time fields will load here -->
                                </div>

                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="edit-add-daily-time-btn">
                                    + Add More Time
                                </button>
                            </div>


                            <!-- 📅 Weekly Days + Multiple Times -->
                            <div class="mt-2" id="edit-recurring-weekly" style="display:none;">
                                <label class="form-label">Select Week Days</label>
                                <select name="recurring_weekdays[]" id="edit-recurring-weekdays" class="form-select select22" multiple>
                                    <option value="Mon">Mon</option>
                                    <option value="Tue">Tue</option>
                                    <option value="Wed">Wed</option>
                                    <option value="Thu">Thu</option>
                                    <option value="Fri">Fri</option>
                                    <option value="Sat">Sat</option>
                                    <option value="Sun">Sun</option>
                                </select>

                                <label class="form-label mt-2">Time(s)</label>

                                <div id="edit-weekly-times-container">
                                    <!-- dynamic time fields go here -->
                                </div>

                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="edit-add-weekly-time-btn">
                                    + Add More Time
                                </button>
                            </div>


                            <!-- 🗓️ Monthly Day + Time -->
                            <div class="mt-2" id="edit-recurring-monthly" style="display:none;">
                                <label class="form-label">Select Day of Month</label>
                                <input type="text" name="recurring_month_day" id="edit-recurring-month-day" class="form-control flatpickerday">
                                <label class="form-label mt-2">Time</label>
                                <input type="text" name="recurring_time_monthly" id="edit-recurring-monthly-time" class="form-control flatpickertime">
                            </div>

                            <!-- 🎉 Yearly Month-Day + Time -->
                            <div class="mt-2" id="edit-recurring-yearly" style="display:none;">
                                <label class="form-label">Select Month & Day</label>
                                <input type="text" name="recurring_year_month_day" id="edit-recurring-year-month-day" class="form-control jsMonthDayPicker" placeholder="MM-DD">
                                <label class="form-label mt-2">Time</label>
                                <input type="text" name="recurring_time_yearly" id="edit-recurring-yearly-time" class="form-control flatpickertime">
                            </div>
                        </div>

                        <!-- Custom Fields -->
                        <div class="col-md-6 reminder-section" id="edit-custom" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">Custom Date Range</label>
                                <input type="text" name="custom_date_range" id="edit-custom-date-range"
                                    class="form-control jsCustomDateRangePicker" placeholder="Select date range...">
                            </div>
                            <div class="mb-3">
                                <label for="edit-custom-time" class="form-label">Time of Day</label>
                                <input type="text" id="edit-custom-time" name="custom_time"
                                    class="form-control flatpickertimeEdit" placeholder="Select Time...">
                            </div>
                        </div>

                        <!-- Notification Type -->
                        <div class="col-md-12 mb-3 edit-notification-type-container" style="display:none;">
                        <label class="form-label d-block">Notification Through</label>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" 
                                type="checkbox" 
                                name="edit_notification_type[]" 
                                id="edit_notify_whatsapp" 
                                value="whatsapp">
                            <label class="form-check-label" for="edit_notify_whatsapp">
                                WhatsApp
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" 
                                type="checkbox" 
                                name="edit_notification_type[]" 
                                id="edit_notify_email" 
                                value="email">
                            <label class="form-check-label" for="edit_notify_email">
                                Email
                            </label>
                        </div>
                    </div>
                        
                        <!-- Assignee -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Assignee</label>
                                <select name="assignto_id[]" id="edit-assignee" class="form-select select22" multiple
                                    data-placeholder="Select Assignee">
                                    <option value="">Select</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}"
                                            @if(Auth::guard('admin')->user()->id == $staff->id) selected @endif>
                                            {{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit-support-team-name" class="form-label">Support Team Name</label>
                            <input type="text" name="support_team_name" class="form-control edit_support_team_name" id="edit_support_team_name" placeholder="Enter Support Team Name...">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit-assigneesupport-team-number" class="form-label">Support Team Number</label>
                            <input type="number" name="support_team_number" class="form-control edit_support_team_number"  id="edit_support_team_number" placeholder="Enter Support Team Number...">
                        </div>

                        <!-- File Attachment -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">File Attachment</label>
                                <input class="form-control file-attachement-input-edit" name="file_attachment" type="file"
                                    id="edit-file-attachment" />
                            </div>
                        </div>

                        <!-- Display Image -->
                        <div class="col-md-6">
                            <div class="mb-3" id="dispIMG">
                                <img alt="file-preview" src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}"
                                    class="d-block w-px-100 h-px-100 rounded uploadedAvatarEd" id="uploadedAvatarEd" />
                            </div>
                        </div>

                        <!-- Created Info -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <p>Created By Id: <span id="todocreatedbyId"></span></p>
                                <p>Created By: <span id="todocreatedby"></span></p>
                                <p>Created On: <span id="todocreatedon"></span></p>
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary me-2" id="editnewcandidateBtn">Update</button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Edit Todo End -->

        <!-- Delete Todo Start -->
        <div class="modal fade" id="deleteStaff" aria-hidden="true" aria-labelledby="deleteStaffLabel" tabindex="-1"  data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Delete Todo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.todo.delete') }}" id="deleteTodoForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="todo_id" id="todoID2">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="mb-3">
                                      <p class="text-danger">Are you sure to delete todo?</p>
                                   </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-danger btn-sm deletebtn" id="disbtn">Delete</button>
                            {{-- <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Delete</button> --}}
                         </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Todo End -->

        <!-- Filter Panel Start -->
        <div class="modal fade" id="filterpanel" aria-hidden="true" aria-labelledby="filterpanelLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="filterpanelLabel">Todo Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <select id="by-type" class="form-select filterData select22f" multiple data-placeholder="Select Work Frequency">
                                    <option value="Once" @if(isset($saveadminfilter) && in_array("Once",explode(",",$saveadminfilter->type))) selected @endif>Once</option>
                                    <option value="Always" @if(isset($saveadminfilter) && in_array("Always",explode(",",$saveadminfilter->type))) selected @endif>Always</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-department" class="selectpicker w-100 filterData"  multiple data-actions-box="true" data-live-search="true" data-style="default-btn" title="Select Department">
                                    {{-- <option value="">Select Department</option> --}}
                                    @foreach ($departfilters as $departfilter)
                                        <option value="{{ $departfilter->department_id }}" @if(isset($saveadminfilter) && in_array($departfilter->department_id,explode(",",$saveadminfilter->department_id))) selected @endif>{{ $departfilter->deptname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-todo-label" class="selectpicker w-100 filterData" multiple data-actions-box="true" data-live-search="true" data-style="default-btn" title="Select Todo Label">
                                    {{-- <option value="">Select Todo Label</option> --}}
                                    @foreach ($todoLabelfilters as $todoLabelfilter)
                                        <option value="{{ $todoLabelfilter->todolabel_id }}" @if(isset($saveadminfilter) && in_array($todoLabelfilter->todolabel_id,explode(",",$saveadminfilter->todolabel_id))) selected @endif>{{ $todoLabelfilter->labelname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-todo-priority" class="selectpicker w-100 filterData" multiple data-actions-box="true" data-live-search="true" data-style="default-btn" title="Select Priority">
                                    {{-- <option value="">Select Priority</option> --}}
                                    @foreach ($todoPriorityfilters as $todoPriorityfilter)
                                        <option value="{{ $todoPriorityfilter->reminder_cycle }}" @if(isset($saveadminfilter) && in_array($todoPriorityfilter->reminder_cycle,explode(",",$saveadminfilter->priority))) selected @endif>{{ $todoPriorityfilter->reminder_cycle }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-todo-status" class="selectpicker w-100 filterData" multiple data-actions-box="true" data-live-search="true" data-style="default-btn" title="Select Status">
                                    {{-- <option value="">Select Status</option> --}}
                                    @foreach ($todoStatusfilters as $todoStatusfilter)
                                        <option value="{{ $todoStatusfilter->task_status }}" @if(isset($saveadminfilter) && in_array($todoStatusfilter->task_status,explode(",",$saveadminfilter->task_status))) selected @endif>{{ $todoStatusfilter->task_status }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-assignee" class="selectpicker w-100 filterData" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Assignee">
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}" @if(isset($saveadminfilter) && in_array($staff->id,explode(",",$saveadminfilter->assignto_id))) selected @endif>{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <input name="todo_start_date" type="text" id="start-date" class="form-control start-date singledatepicker" @if(isset($saveadminfilter)) value="{{ $saveadminfilter->start_date }}" @endif  placeholder="Start Date...">
                            </div>

                            <div class="col-md-4 mb-3">
                                <input name="todo_end_date" type="text" id="end-date" class="form-control end-date singledatepicker" @if(isset($saveadminfilter)) value="{{ $saveadminfilter->finish_date }}" @endif placeholder="Due Date...">
                            </div>

                            <div class="col-md-4 mb-3">
                                <input type="text" id="complete-task-date-range-picket" name="complate_date_range" class="form-control bsdatpicket" @if(isset($saveadminfilter)) value="{{ $saveadminfilter->complete_date_range }}" @endif placeholder="Complete Date Range Picker" />
                            </div>

                            <div class="col-md-4 mb-3">
                                <input type="text" id="achieved-task-date-range-picket" name="achieved_date_range" class="form-control bsdatpicket" @if(isset($saveadminfilter)) value="{{ $saveadminfilter->achieved_date_range }}" @endif placeholder="Achieved Date Range Picker" />
                            </div>

                            <!-- Created Date Range Picker -->
                            <div class="col-md-4 mb-3">
                                <input type="text" id="created-date-range-picker" name="created_date_range" class="form-control bsdatpicket" @if(isset($saveadminfilter)) value="{{ $saveadminfilter->created_date_range }}" @endif placeholder="Created Date Range Picker" />
                            </div>

                            <!-- Updated Date Range Picker -->
                            <div class="col-md-4 mb-3">
                                <input type="text" id="updated-date-range-picker" name="updated_date_range" class="form-control bsdatpicket" @if(isset($saveadminfilter)) value="{{ $saveadminfilter->updated_date_range }}" @endif placeholder="Updated Date Range Picker" />
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-followup-before" name="followup_before" class="selectpicker w-100"
                                    data-live-search="true" data-style="default-btn" title="Select followup before">

                                    <option value="">Select followup before</option>

                                    <option value="1" {{ ($saveadminfilter->followup_before ?? '') == '1' ? 'selected' : '' }}>Today</option>
                                    <option value="3" {{ ($saveadminfilter->followup_before ?? '') == '3' ? 'selected' : '' }}>3 Days</option>
                                    <option value="7" {{ ($saveadminfilter->followup_before ?? '') == '7' ? 'selected' : '' }}>7 Days</option>
                                    <option value="14" {{ ($saveadminfilter->followup_before ?? '') == '14' ? 'selected' : '' }}>14 Days</option>
                                    <option value="30" {{ ($saveadminfilter->followup_before ?? '') == '30' ? 'selected' : '' }}>1 Month</option>

                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-created-by" class="selectpicker w-100 filterData" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Created By">
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}" @if(isset($saveadminfilter) && in_array($staff->id,explode(",",$saveadminfilter->created_by_id))) selected @endif>{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-warning btn-sm resetfilter">Reset Filter</button>
                        <button type="button" class="btn btn-success btn-sm savetodoFilter">Save Filter</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filter Panel End -->

        <!-- Bulkstatus Update Start -->
        <div class="modal fade" id="bulkchangestatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel125">Update Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="POST" id="bulkstatusupdateValidation">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" name="bulktodo_id" id="bulkstatustodo_id">
                                    <div class="mb-3">
                                        <label for="">Task Status <span class="text-danger">*</span></label>
                                        <select name="task_status" id="bulk-task-status" class="form-select select22" data-placeholder="Select Status" data-allow-clear="true">
                                            <option value="">Select Status</option>
                                            <option value="New Task">New Task</option>
                                            <option value="In Process">In Process</option>
                                            <option value="Complete">Complete</option>
                                            @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1) || (isset($permission) && $permission->todo_achieved == 1))
                                                <option value="Achieved">Achieved</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="bulk-update-status" class="btn btn-sm btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Bulkstatus Update End -->

        <!-- Bulk Change Priority Start -->
        <div class="modal fade" id="bulkchangepriority" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel125">Update Priority</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="POST" id="bulkchangepriorityValidation">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" name="bulktodo_id" id="bulkchangepriority_id">
                                    <div class="mb-3">
                                        <label for="">Priority <span class="text-danger">*</span></label>
                                        <select name="reminder_cycle" id="bulk-reminder-cycle" class="form-select select22" data-placeholder="Select Priority" data-allow-clear="true">
                                            <option value="">Priority</option>
                                            <option value="High">High (1 Hrs)</option>
                                            <option value="Medium">Medium (3 Hrs)</option>
                                            <option value="Low">Low (4 Hrs)</option>
                                            <option value="Urgent">Urgent (30 Min)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="bulk-update-priority" class="btn btn-sm btn-primary">Update Priority</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Bulk Change Priority End -->

        <!-- Bulk Change Priority Start -->
        <div class="modal fade" id="bulkassignto" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel125">Update Assignto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="POST" id="bulkassigntoValidation">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" name="bulktodo_id" id="bulkassignto_id">
                                    <div class="mb-3">
                                        <label for="">Assignto <span class="text-danger">*</span></label>
                                        <select name="assignto_id[]" id="bulk-assignto-id" class="form-select select22" data-placeholder="Select Assignto" multiple>
                                            <option value="">Assignto</option>
                                            @foreach ($staffs as $staff25)
                                                <option value="{{ $staff25->id }}">{{ $staff25->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="bulk-update-assignto" class="btn btn-sm btn-primary">Update Assignto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Bulk Change Priority End -->

        <!-- Bulk Change Priority Start -->
        <div class="modal fade" id="bulkdelete" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel125">Bulk Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.todo.bulkdelete') }}" method="POST" id="bulkdeleteValidation">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" name="bulktodo_id" id="bulkdelete_id">
                                    <div class="mb-3">
                                        <p class="text-danger">Are you sure to delete todo?</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="bulk-delete" class="btn btn-sm btn-primary">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Bulk Change Priority End -->

    </div>
@endsection

@section('page-script')

    <!-- Dark mode script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const t = localStorage.getItem('templateCustomizer-vertical-menu-template--Style');
            const styleEl = document.getElementById('dynamicStyle');
            
            if (t === 'dark') {
                styleEl.innerHTML = `
                    body > div.layout-wrapper.layout-content-navbar > div.layout-container > div > div > div.container-fluid.flex-grow-1.container-p-y > div.card.disTodoKanban > div.card-body.card-body-kanban-row > div > div {
                        background-color: #26293d;
                    }
                `;
            } else {
                // default or light style
                styleEl.innerHTML = `
                    body > div.layout-wrapper.layout-content-navbar > div.layout-container > div > div > div.container-fluid.flex-grow-1.container-p-y > div.card.disTodoKanban > div.card-body.card-body-kanban-row > div > div {
                        background-color: #f8f7fa;
                    }
                `;
            }
        });
    </script>

    <!-- Vendors JS -->
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    {{-- <script src="{{ asset('admin/assets/vendor/libs/jkanban/jkanban.js') }}"></script> --}}
    <script src="{{ asset('admin/assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/task-todd-validation.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    {{-- <script src="{{ asset('admin/assets/js/app-kanban.js') }}"></script> --}}
    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>


    <!-- Load Today reminder lable start -->
    <script>
        function loadTodayReminderToggle(isKanban, isChecked) {

            let block = `
                <label class="switch mb-0 today-switch">
                    <input type="checkbox" class="switch-input filterData filter-today-reminder" value="0" ${isChecked ? "checked" : ""}>
                    <span class="switch-toggle-slider today-switch">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                    <span class="switch-label ms-1 fw-bold">Today Reminder</span>
                </label>
            `;

            if (isKanban) {
                $(".Todo-filter-today-reminder").html("");
                $(".Kanban-filter-today-reminder").html(block);
            } else {
                $(".Kanban-filter-today-reminder").html("");
                $(".Todo-filter-today-reminder").html(block);
            }
        }

        const filterSelectors = `
            #by-type,
            #by-department,
            #by-todo-label,
            #by-todo-priority,
            #by-todo-status,
            #by-assignee,
            #by-followup-before,
            #by-created-by,
            #start-date,
            #end-date,
            #complete-task-date-range-picket,
            #achieved-task-date-range-picket,
            #created-date-range-picker,
            #updated-date-range-picker
        `;

        $(document).on('change input keyup', filterSelectors, function () {
            updateAdminFilterIndicator();
        });

        function updateAdminFilterIndicator() {

            let isFiltered =
                // Work Type
                ($('#by-type').val() && $('#by-type').val().length > 0) ||

                // Department
                ($('#by-department').val() && $('#by-department').val().length > 0) ||

                // Todo Label
                ($('#by-todo-label').val() && $('#by-todo-label').val().length > 0) ||

                // Priority
                ($('#by-todo-priority').val() && $('#by-todo-priority').val().length > 0) ||

                // Status
                ($('#by-todo-status').val() && $('#by-todo-status').val().length > 0) ||

                // Follow-up due (status summary bar)
                !!$('#by-todo-followup-due').val() ||

                // Assignee
                ($('#by-assignee').val() && $('#by-assignee').val().length > 0) ||

                // Followup before
                ($('#by-followup-before').val() && $('#by-followup-before').val().length > 0) ||

                // Created By
                ($('#by-created-by').val() && $('#by-created-by').val().length > 0) ||

                // Start / End dates
                $('#start-date').val() ||
                $('#end-date').val() ||

                // Date range pickers
                $('#complete-task-date-range-picket').val() ||
                $('#achieved-task-date-range-picket').val() ||
                $('#created-date-range-picker').val() ||
                $('#updated-date-range-picker').val();

            if (isFiltered) {
                $('.filterpanel .filter-indicator')
                    .removeClass('d-none')
                    .addClass('d-block');
            } else {
                $('.filterpanel .filter-indicator')
                    .removeClass('d-block')
                    .addClass('d-none');
            }
        }

        $(document).ready(function () {

            updateAdminFilterIndicator();

            let toggleBlock = `
                <label class="switch mb-0 today-switch">
                    <input type="checkbox" class="switch-input filterData filter-today-reminder" value="0">
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                    <span class="switch-label ms-1 fw-bold">Today Reminder Task</span>
                </label>
            `;

            loadTodayReminderToggle(
                {{ isset($saveadminfilter) && $saveadminfilter->switch_to == 1 ? 'true' : 'false' }},
                {{ isset($saveadminfilter) && $saveadminfilter->today_reminder_task == 1 ? 'true' : 'false' }}
            );

        });
    </script>
    <!-- Load Today reminder lable end -->

    <!--  -->
    <script>
        $(document).ready(function(){
            // Show/Hide Add Module based on Switch
            $('.option-switch-input').on('change',function(){
                if($(this).is(':checked')){
                    $('.hideshowaddmodule').show();
                }else{
                    $('.hideshowaddmodule').hide();
                }
            });
            
            // Save Short Form Code Setting
            $('.option-switch-input').on('change',function(){
                var short_form_code = $(this).is(':checked') ?'1':'0';
                jQuery.ajax({
                    url: "{{ route('admin.todo.saveshortformcode') }}",
                    method: "POST",
                    type: "html",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        short_form_code: short_form_code
                    },
                    success: function(data){
                        if(data){
                            toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                        }
                    }
                });
            });

            // Status Bar toggle: shows/hides the Deal Stage / Recruit Status
            // Summary Bar (the Todo Status Summary Bar). Persisted
            // client-side so the choice survives reloads and keeps working
            // across list/kanban switches and AJAX refreshes, since those
            // never re-render this bar's container. display is forced with
            // !important (and bound via delegation) so nothing else on this
            // page can silently override it. Both the List-view and
            // Kanban-view Option dropdowns render their own copy of this
            // switch, so state is synced across every instance via the
            // shared .todo-status-bar-switch class.
            (function(){
                function setTodoStatusBarVisible(visible){
                    var bar = document.getElementById('todoStatusBar');
                    if (!bar) return;
                    if (visible) {
                        bar.style.removeProperty('display');
                    } else {
                        bar.style.setProperty('display', 'none', 'important');
                    }
                }

                var stored = null;
                try { stored = localStorage.getItem('todo_status_bar_visible'); } catch(e) {}
                var visible = stored === null ? true : stored === '1';
                $('.todo-status-bar-switch').prop('checked', visible);
                setTodoStatusBarVisible(visible);

                $(document).on('change', '.todo-status-bar-switch', function(){
                    var isVisible = $(this).is(':checked');
                    $('.todo-status-bar-switch').prop('checked', isVisible);
                    setTodoStatusBarVisible(isVisible);
                    try { localStorage.setItem('todo_status_bar_visible', isVisible ? '1' : '0'); } catch(e) {}
                });
            })();
        });
    </script>
    <!--  -->


    <!-- Add/Edit Staff logics -->
    <script>

        // Function to retrieve all filter values
        function getFilterData() {
            return {
                page_list: $('#pagination_list').val(),
                search_text: $('#search_text').val(),
                search_text_kanban: $('#search_text_kanban').val(),
                department_id: $('#by-department').val(),
                todolabel_id: $('#by-todo-label').val(),
                reminder_cycle: $('#by-todo-priority').val(),
                task_status: $('#by-todo-status').val(),
                followup_due: $('#by-todo-followup-due').val(),
                assignto_id: $('#by-assignee').val(),
                followup_before: $('#by-followup-before').val(),
                created_by: $('#by-created-by').val(),
                today_reminder_task: $('.filter-today-reminder').prop('checked') ? 1 : 0,
                type_fi: $('#by-type').val(),
                start_on: $('#start-date').val(),
                finish_on: $('#end-date').val(),
                complete_date: $('#complete-task-date-range-picket').val(),
                achieved_date: $('#achieved-task-date-range-picket').val(),
                created_at: $('#created-date-range-picker').val(),
                updated_at: $('#updated-date-range-picker').val()
            };
        }

        // Function to reload todo list based on filter data
        function reloadTodoList() {
            // .contactpaginate exists once inside the list card and once
            // inside the kanban card — writing to the bare selector hits
            // both (and, worse, decides table-vs-kanban server-side from a
            // possibly-stale saved switch_to). Tell the server which view
            // is actually on screen and only touch that one's container.
            var isKanban = $('.disTodoKanban').is(':visible');
            var $target = isKanban ? $('.disTodoKanban .contactpaginate') : $('.disTodoList .contactpaginate');

            var data = getFilterData();
            data.view_mode = isKanban ? 'kanban' : 'list';

            $.ajax({
                url: "{{ route('admin.todo.list') }}",
                method: "GET",
                dataType: "html",
                data: data,
                success: function (data) {
                    $target.html(data);
                    initKanbanSortable();
                    sizeKanbanBoard();

                }
            });
        }

        function updateTodoList(post, assignees) {

            // =========================
            // STATUS BADGE COLOR
            // =========================

            let badgeLabel = 'success';

            if (post.task_status == 'New Task') {
                badgeLabel = 'info';
            } else if (post.task_status == 'In Process') {
                badgeLabel = 'primary';
            } else if (post.task_status == 'Complete') {
                badgeLabel = 'warning';
            } else if (post.task_status == 'Achieved') {
                badgeLabel = 'success';
            } else if (post.task_status == 'Not Required') {
                badgeLabel = 'danger';
            } else if (post.task_status == 'Always') {
                badgeLabel = 'dark';
            }

            // =========================
            // PRIORITY COLOR
            // =========================

            let priorityClass = 'primary';

            if (post.reminder_cycle == 'Urgent') {
                priorityClass = 'danger';
            } else if (post.reminder_cycle == 'High') {
                priorityClass = 'warning';
            } else if (post.reminder_cycle == 'Medium') {
                priorityClass = 'info';
            }

             // =========================
            // ASSIGNEE HTML
            // =========================

            let assigneeHtml = '';

            if (assignees && assignees.length > 0) {

                $.each(assignees, function (key, assignee) {

                    assigneeHtml += `
                        <span class="badge rounded-pill bg-label-primary me-1">
                            ${assignee.name}
                        </span>
                    `;

                });

            }


            // =========================
            // ROW UPDATE
            // =========================

            let row = $('#todo-row-' + post.id);

            row.html(`
                
                <td>
                    <div class="form-check form-check-inline">

                        <input class="form-check-input dt-checkboxes sub-chk"
                            data-id="${post.id}"
                            type="checkbox"
                            value="${post.id}"
                            id="checkbox${post.id}" />

                        <label class="form-check-label"
                            for="checkbox${post.id}">
                        </label>

                    </div>
                </td>

                <td>

                    <a href="javascript:void(0);"
                        class="view-todo-btn"
                        data-id="${post.id}"
                        data-bs-toggle="modal"
                        data-bs-target="#viewTodoModal">

                        ${post.task_title}

                    </a>

                </td>

                <td>

                    <span class="badge rounded-pill bg-label-${badgeLabel} dropdown-toggle hide-arrow"
                        data-bs-toggle="dropdown">

                        ${post.task_status}

                        <i class='ti ti-chevron-down ti-xs'></i>

                    </span>

                    <div class="dropdown-menu dropdown-menu-end m-0">

                        <a href="javascript:void(0);"
                            data-id="${post.id}"
                            class="dropdown-item updateStatus">
                            Mark as New Task
                        </a>

                        <a href="javascript:void(0);"
                            data-id="${post.id}"
                            class="dropdown-item updateStatus">
                            Mark as In Process
                        </a>

                        <a href="javascript:void(0);"
                            data-id="${post.id}"
                            class="dropdown-item updateStatus">
                            Mark as Complete
                        </a>

                        <a href="javascript:void(0);"
                            data-id="${post.id}"
                            class="dropdown-item updateStatus">
                            Mark as Always
                        </a>

                        <a href="javascript:void(0);"
                            data-id="${post.id}"
                            class="dropdown-item updateStatus">
                            Mark as Achieved
                        </a>

                        <a href="javascript:void(0);"
                            data-id="${post.id}"
                            class="dropdown-item updateStatus">
                            Mark as Not Required
                        </a>

                    </div>

                </td>

                <td>

                    <span class="badge bg-label-${priorityClass}">
                        ${post.reminder_cycle}
                    </span>

                </td>

                <td>
                    ${post.department_id != null && post.department
                        ? post.department.name
                        : '---'}
                </td>

                <td>
                    ${post.todolabel_id != null && post.todolabel
                        ? post.todolabel.name
                        : '---'}
                </td>

                <td>
                    ${post.start_on != null && post.start_on != ''
                        ? moment(post.start_on).format('DD-MM-YYYY')
                        : '---'}
                </td>

                <td>
                    ${post.finish_on != null && post.finish_on != ''
                        ? moment(post.finish_on).format('DD-MM-YYYY')
                        : '---'}
                </td>

                <td>
                    ${assigneeHtml != '' ? assigneeHtml : '---'}
                </td>

                <td>

                    <div class="dropdown">

                        <button class="btn p-0"
                            type="button"
                            data-bs-toggle="dropdown">

                            <i class="ti ti-dots-vertical ti-sm text-muted"></i>

                        </button>

                        <div class="dropdown-menu dropdown-menu-end">

                            <a class="dropdown-item editcontact edcandidate"
                                href="javascript:void(0);"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasEditUser"
                                data-id="${post.id}">

                                <i class="ti ti-edit me-2"></i> Edit

                            </a>

                            <a class="dropdown-item"
                                href="javascript:void(0);"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteStaff"
                                data-id="${post.id}">

                                <i class="ti ti-trash me-2"></i> Delete

                            </a>

                        </div>

                    </div>

                </td>

            `);

        }

        function updateKanban(post, assignees = []) {

            // =========================
            // STATUS SLUG
            // =========================

            let statusSlug = post.task_status
                .toLowerCase()
                .replaceAll(' ', '-');

            // =========================
            // PRIORITY BADGE
            // =========================

            let badgeColor = 'primary';

            // =========================
            // ASSIGNEE AVATAR HTML
            // =========================

            let assigneeHtml = '';

            if (assignees.length > 0) {

                $.each(assignees, function (key, assignee) {

                    let avatar = assignee.profile
                        ? `/admin/assets/img/avatars/${assignee.profile}`
                        : `/admin/assets/img/avatars/blank.jpeg`;

                    assigneeHtml += `
                        <li class="avatar avatar-xs">
                            <img class="rounded-circle" src="${avatar}">
                        </li>
                    `;

                });

            }

            // =========================
            // LABEL HTML
            // =========================

            let labelHtml = '';

            if (post.todolabel_id != null && post.todolabel) {

                labelHtml += `
                    <span class="badge bg-label-primary">
                        ${post.todolabel.name}
                    </span>
                `;
            }

            if (post.department_id != null && post.department) {

                labelHtml += `
                    <span class="badge bg-label-secondary">
                        ${post.department.name}
                    </span>
                `;
            }

            // =========================
            // CARD HTML
            // =========================

            let cardHtml = `

                <div class="kanban-card" data-id="${post.id}">

                    <div class="card mb-3 shadow-sm">

                        <div class="card-body p-2">

                            <div class="d-flex align-items-center mb-2">

                                <div>

                                    <span class="badge bg-label-${badgeColor}">
                                        ${post.reminder_cycle ?? ''}
                                    </span>

                                </div>

                                <div class="ms-auto">

                                    <ul class="list-inline mb-0 d-flex align-items-center">

                                        <li class="list-inline-item">

                                            <a href="javascript:void(0);"
                                            class="view-todo-btn"
                                            data-id="${post.id}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewTodoModal">

                                                <i class="tf-icons ti ti-eye ti-sm"></i>

                                            </a>

                                        </li>

                                        <li class="list-inline-item">

                                            <a href="javascript:void(0);"
                                            class="offcanvasEditTodo edit_todo_${post.id}"
                                            data-bs-toggle="offcanvas"
                                            data-bs-target="#offcanvasEditUser"
                                            data-id="${post.id}">

                                                <i class="tf-icons ti ti-edit ti-sm"></i>

                                            </a>

                                        </li>

                                        <li class="list-inline-item">

                                            <div class="dropdown dropdown-custom">

                                                <button class="btn dropdown-toggle hide-arrow p-0"
                                                    data-bs-toggle="dropdown">

                                                    <i class="ti ti-dots-vertical text-muted"></i>

                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-end">

                                                    <li>

                                                        <a class="dropdown-item"
                                                        data-bs-toggle="offcanvas"
                                                        data-bs-target="#offcanvasEditUser"
                                                        data-id="${post.id}">

                                                            Edit

                                                        </a>

                                                    </li>

                                                    <li>

                                                        <a class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteStaff"
                                                        data-id="${post.id}">

                                                            Delete

                                                        </a>

                                                    </li>

                                                </ul>

                                            </div>

                                        </li>

                                    </ul>

                                </div>

                            </div>

                            <div class="fw-semibold text-wrap mb-1">
                                ${post.task_title ?? ''}
                            </div>

                            <div class="small text-muted text-wrap mb-2">
                                ${post.task_description ?? ''}
                            </div>

                            <div class="d-flex align-items-center">

                                <ul class="list-unstyled d-flex avatar-group mb-0">

                                    ${assigneeHtml}

                                </ul>

                                <small class="text-muted ms-auto">

                                    ${moment(post.created_at).format('DD-MM-YYYY')}

                                </small>

                            </div>

                            <div class="mt-2">

                                ${labelHtml}

                            </div>

                        </div>

                    </div>

                </div>
            `;

            // =========================
            // REMOVE OLD CARD
            // =========================

            $('.kanban-card[data-id="' + post.id + '"]').remove();

            // =========================
            // APPEND NEW CARD
            // =========================

            $('#stage-' + statusSlug).prepend(cardHtml);

        }

        $(document).ready(function() {
            // initialize first time picker
            $('.flatpickertime').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: false
            });

            // add new time field
            $(document).on('click', '#add-weekly-time-btn', function() {
                let newField = `
                    <div class="input-group mb-2 time-group">
                        <input type="text" name="recurring_time_weekly[]" class="form-control flatpickertime" placeholder="Select time">
                        <button type="button" class="btn btn-outline-danger remove-time-btn">✕</button>
                    </div>
                `;
                $('#weekly-times-container').append(newField);

                // reinitialize flatpickr for new input
                $('.flatpickertime').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: false
                });
            });

            // remove time field
            $(document).on('click', '.remove-time-btn', function() {
                $(this).closest('.time-group').remove();
            });
        });



        // 5️⃣ Staff Work add/remove handlers

        function updateEditStaffWorkHidden() {
            let staffWorkList = [];
            staffWorkList.push($('.edit_staff_work_input').val().trim());
            $('.EditStaffWorkList .staff-work-text').each(function () {
                let val = $(this).val().trim();
                if (val) staffWorkList.push(val);
            });

            $('.edit_staff_work_input_hidden').val(JSON.stringify(staffWorkList));
        }

        $(document).ready(function(){

            // ============================================================== 
            // Handle Todo Notes Tab Section - Start
            // ==============================================================

            const todoNotesTableBody = $('#todoNotesTableBody');
            const todoNotesForm = $('#todoNotesValidation');

            // 🟡 Load Todo Notes Function
            function loadTodoNotes(todoId) {
                $('#todo_notes_id').val(todoId);

                $.ajax({
                    url: "{{ route('admin.todonotes.list') }}",
                    type: "GET",
                    data: { todo_id: todoId },
                    beforeSend: function() {
                        todoNotesTableBody.html('<tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>');
                    },
                    success: function(response) {
                        if (response.html) {
                            todoNotesTableBody.html(response.html);
                        } else {
                            todoNotesTableBody.html('<tr><td colspan="4" class="text-center text-muted">No notes found</td></tr>');
                        }

                        if(response.todo) {
                            $('.TodoTitleClass').text('');
                            $('.TodoDescClass').text('');
                            $('.TodoTitleClass').text('Task Title: '+response.todo.task_title); 
                            let desc = (response?.todo?.task_description || "").toString();
                            let words = desc.split(/\s+/);

                            $('.TodoDescClass').text(
                                'Description: ' + words.slice(0,500).join(' ') + (words.length > 500 ? '...' : '')
                            );
                        }
                    },
                    error: function() {
                        todoNotesTableBody.html('<tr><td colspan="4" class="text-center text-danger">Failed to load notes</td></tr>');
                    }
                });
            }

            // 🟢 Add Todo Note
            $(document).on('submit', '#todoNotesValidation', function(e) {
                e.preventDefault();
                const form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    type: "POST",
                    data: form.serialize(),
                    beforeSend: function() {
                        form.find('button[type="submit"]').prop('disabled', true).text('Saving...');
                    },
                    success: function(response) {
                        form.find('button[type="submit"]').prop('disabled', false).text('Submit');

                        if (response.status === 'success') {
                            toastr.success(response.message || 'Note added successfully', 'Success');

                            // Dynamically prepend new note row
                            $('#todoNotesTableBody').prepend(`
                                <tr id="todoNoteRow_${response.data.id}">
                                    <td>${response.data.notes}</td>
                                    <td>${response.data.created_by_name || '---'}</td>
                                    <td>${response.data.created_at}</td>
                                    <td>
                                        <a href="javascript:void(0);" class="delete-todo-note" data-id="${response.data.id}" data-url="${response.data.delete_url}">
                                            <i class="ti ti-trash ti-sm text-danger"></i>
                                        </a>
                                    </td>
                                </tr>
                            `);

                            form[0].reset();
                        } else {
                            toastr.error(response.message || 'Failed to add note', 'Error');
                        }
                    },
                    error: function(xhr) {
                        form.find('button[type="submit"]').prop('disabled', false).text('Submit');

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessages = [];

                            $.each(errors, function(key, messages) {
                                errorMessages.push(messages[0]);
                            });

                            toastr.error(errorMessages.join("\n"), 'Error');
                        } else {
                            toastr.error('Something went wrong while saving the note', 'Error');
                            console.error(xhr.responseText);
                        }
                    }
                });
            });

            // 🔴 Delete Todo Note
            $(document).on('click', '#todoNotesTableBody a.delete-todo-note', function(e) {
                e.preventDefault();

                const deleteId = $(this).data('id');
                const deleteUrl = $(this).data('url');

                if (confirm('Are you sure you want to delete this note?')) {
                    $.ajax({
                        url: deleteUrl,
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            delete_id: deleteId
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                toastr.success(response.message || 'Note deleted successfully', 'Success');
                                $('#todoNoteRow_' + deleteId).remove();
                            } else {
                                toastr.error(response.message || 'Failed to delete note', 'Error');
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Something went wrong while deleting the note', 'Error');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });

            // 🟠 Load notes when clicking Notes tab or View Todo button
            $(document).on('click', '.todo-notes-tab-btn', function () {
                const todoId = $('#todo_notes_id').val();
                loadTodoNotes(todoId);
            });

            $(document).on('click', '.view-todo-btn', function () {
                const todoId = $(this).data('id');
                loadTodoNotes(todoId);
                $('.notes-tab-btn').trigger('click');
            });

            @if($autoOpenTodoId)
                // Global "open this task" deep link (e.g. from the Dashboard).
                // Reuses the exact same modal-open logic above via a synthetic
                // click on .view-todo-btn — no duplicate modal/fetch logic.
                (function () {
                    var $autoOpenTrigger = $('<a href="javascript:void(0);" class="view-todo-btn d-none" data-id="{{ $autoOpenTodoId }}" data-bs-toggle="modal" data-bs-target="#viewTodoModal"></a>').appendTo('body');
                    $autoOpenTrigger.trigger('click');
                    $('#viewTodoModal').modal('show');
                    $autoOpenTrigger.remove();
                })();
            @endif

            $(document).on('click', '.modalEditTodo', function(e) {
                e.preventDefault();
                $('.view-todo-btn-close').trigger('click');
                const todoId =  $('#todo_notes_id').val();
                document.querySelector('.edit_todo_' + todoId)?.click();
            });

            // 🟢  Add staff work
            $(document).on('click', '.add_staff_work_btn', function () {

                let wrapper = $(this).closest('.mb-3');
                let input = wrapper.find('.add_staff_work_input');
                let list = wrapper.find('.staffWorkList');

                let work = input.val();

                let listItem = `
                    <div class="input-group mb-2 staff-work-item">
                        <input type="text" class="form-control staff-work-text"
                            value="" placeholder="Enter staff work...">

                        <button type="button" class="btn btn-outline-danger btn-sm remove-staff-work-btn">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                `;

                list.append(listItem);

            });

            // Add new staff work
            $(document).on('click', '.edit_staff_work_btn', function () {

                let wrapper = $(this).closest('.mb-3');
                let input = wrapper.find('.edit_staff_work_input');
                let list = wrapper.find('.EditStaffWorkList');

                let work = input.val();


                let listItem = `
                    <div class="input-group mb-2 staff-work-item">
                        <input type="text" 
                            class="form-control staff-work-text"
                            value="" 
                            placeholder="Enter staff work...">

                        <button type="button" class="btn btn-outline-danger btn-sm remove-staff-work-btn">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                `;

                list.append(listItem);
            });

           // Remove staff work
            $(document).on('click', '.remove-staff-work-btn', function() {
                $(this).closest('.staff-work-item').remove();
            });

            // ==============================================================
            // Handle Todo Notes Tab Section - End
            // ==============================================================

            // Initialize Flatpickr for Monthly day picker
            var flatpickerday = $('.flatpickerday');
            if (flatpickerday.length) {
                flatpickerday.flatpickr({
                    enableTime: false,       // No time picker
                    noCalendar: false,       // Show calendar
                    dateFormat: "d",         // Show only day
                    minDate: "1",            // Minimum day 1
                    maxDate: "31",           // Maximum day 31
                    defaultDate: "1",        // Optional default value
                    onReady: function(selectedDates, dateStr, instance) {
                        // Limit displayed days to 1-30 if needed
                        instance.config.disable.push(function(date) {
                            var day = date.getDate();
                            return day > 30; // disable 31st
                        });
                    }
                });
            }

            // Initialize Flatpickr for Yearly Month-Day picker
            var jsMonthDayPicker = $('.jsMonthDayPicker');
            if (jsMonthDayPicker.length) {
                jsMonthDayPicker.flatpickr({
                    enableTime: false,          // Only date
                    noCalendar: false,          // Show calendar
                    dateFormat: "m-d",          // Show month-day only
                    altInput: true,             // Optional: shows a nice formatted input
                    altFormat: "F j",           // Optional: shows Month name + day
                    defaultDate: "01-01",       // Optional default
                    onChange: function(selectedDates, dateStr, instance) {
                        // Format to MM-DD if needed
                        if (selectedDates.length > 0) {
                            const date = selectedDates[0];
                            const month = ("0" + (date.getMonth() + 1)).slice(-2);
                            const day = ("0" + date.getDate()).slice(-2);
                            instance.input.value = `${month}-${day}`;
                        }
                    }
                });
            }

            var flatpickrtime = $('.flatpickertime');
            if (flatpickrtime) {
                flatpickrtime.flatpickr({
                    enableTime: true,
                    noCalendar: true
                });
            }

            var flatpickrtimeEdit = $('.flatpickertimeEdit');
            if (flatpickrtimeEdit) {
                flatpickrtimeEdit.flatpickr({
                    enableTime: true,
                    noCalendar: true
                });
            }

            var selectPicker = $('.selectpicker');

            if (selectPicker.length) {
                selectPicker.selectpicker();
            }
       
            $('#bulkchangestatus').on('show.bs.modal',function(e){
                var allselectedvals = [];

                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });

                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");
                $('#bulkstatustodo_id').val(join_all_selected_values);

            });

            $('#bulkchangepriority').on('show.bs.modal',function(e){
                var allselectedvals = [];

                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });

                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");
                $('#bulkchangepriority_id').val(join_all_selected_values);

            });

            $('#bulkassignto').on('show.bs.modal',function(e){
                var allselectedvals = [];

                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });

                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");
                $('#bulkassignto_id').val(join_all_selected_values);

            });

            $('#bulkdelete').on('show.bs.modal',function(e){
                var allselectedvals = [];

                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });

                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");
                $('#bulkdelete_id').val(join_all_selected_values);

            });


            // update status by bulk action
            $(document).on('click','#bulk-update-status',function(e){
                e.preventDefault();
                var candID = $('#bulkstatustodo_id').val();

                var formdata = $('#bulkstatusupdateValidation').serialize();
                const select22 = $('.select22');
                var bulkstatusform = $(' #bulkstatusupdateValidation ');

                bulkstatusform.validate({
                    rules:{
                        task_status:{
                            required: true
                        },
                    },
                    messages: {
                        task_status:{
                            required: "Please select Task Status"
                        },
                    },
                });

                if (bulkstatusform.valid()) {
                    $.ajax({
                        url: "{{ route('admin.todo.bulkstatus.update') }}",
                        method: "POST",
                        data: formdata,
                        success: function(data){
                            toastr.success(data.message);
                            bulkstatusform[0].reset();

                            $('#bulkchangestatus').modal('hide');

                            if (select22.length) {
                                select22.each(function () {
                                    var $this = $(this);
                                    $this.wrap('<div class="position-relative"></div>').select2({
                                    //   placeholder: 'Select value',
                                    dropdownParent: $this.parent()
                                    });
                                });
                            }

                            // Load Page
                            jQuery.ajax({
                                url: "{{ route('admin.todo.list') }}",
                                method: "GET",
                                success: function(data){
                                    $('.contactpaginate').html(data);

                                }
                            });

                            // disabled button
                            $('.bulkactions').prop('disabled',true);



                        }
                    });
                }

                return false;

            });

            // update pririty by bulk action
            $(document).on('click','#bulk-update-priority',function(e){
                e.preventDefault();

                var formdata = $('#bulkchangepriorityValidation').serialize();
                const select22 = $('.select22');
                var bulkpriorityform = $(' #bulkchangepriorityValidation ');

                bulkpriorityform.validate({
                    rules:{
                        reminder_cycle:{
                            required: true
                        },
                    },
                    messages: {
                        reminder_cycle:{
                            required: "Please select Priority"
                        },
                    },
                });

                if (bulkpriorityform.valid()) {
                    $.ajax({
                        url: "{{ route('admin.todo.bulkpriority.update') }}",
                        method: "POST",
                        data: formdata,
                        success: function(data){
                            toastr.success(data.message);
                            bulkpriorityform[0].reset();

                            $('#bulkchangepriority').modal('hide');

                            if (select22.length) {
                                select22.each(function () {
                                    var $this = $(this);
                                    $this.wrap('<div class="position-relative"></div>').select2({
                                    //   placeholder: 'Select value',
                                    dropdownParent: $this.parent()
                                    });
                                });
                            }

                            // Load Page
                            jQuery.ajax({
                                url: "{{ route('admin.todo.list') }}",
                                method: "GET",
                                success: function(data){
                                    $('.contactpaginate').html(data);

                                }
                            });

                            // disabled button
                            $('.bulkactions').prop('disabled',true);



                        }
                    });
                }

                return false;

            });

            // update assignto by bulk action
            $(document).on('click','#bulk-update-assignto',function(e){
                e.preventDefault();

                var formdata = $('#bulkassigntoValidation').serialize();
                const select22 = $('.select22');
                var bulkassigntoform = $(' #bulkassigntoValidation ');

                bulkassigntoform.validate({
                    rules:{
                        'assignto_id[]':{
                            required: true
                        },
                    },
                    messages: {
                        'assignto_id[]':{
                            required: "Please select Assignto"
                        },
                    },
                });

                if (bulkassigntoform.valid()) {
                    $.ajax({
                        url: "{{ route('admin.todo.bulkassignto.update') }}",
                        method: "POST",
                        data: formdata,
                        success: function(data){
                            toastr.success(data.message);
                            bulkassigntoform[0].reset();

                            $('#bulkassignto').modal('hide');

                            if (select22.length) {
                                select22.each(function () {
                                    var $this = $(this);
                                    $this.wrap('<div class="position-relative"></div>').select2({
                                    //   placeholder: 'Select value',
                                    dropdownParent: $this.parent()
                                    });
                                });
                            }

                            // Load Page
                            jQuery.ajax({
                                url: "{{ route('admin.todo.list') }}",
                                method: "GET",
                                success: function(data){
                                    $('.contactpaginate').html(data);

                                }
                            });

                            // disabled button
                            $('.bulkactions').prop('disabled',true);



                        }
                    });
                }

                return false;

            });

            // delete to by Bulk action
            $(document).on('click','#bulk-delete',function(e){
                e.preventDefault();
                var formdata = $('#bulkdeleteValidation').serialize();
                $.ajax({
                    url: "{{ route('admin.todo.bulkdelete') }}",
                    method: "POST",
                    data: formdata,
                    success: function(response){
                        toastr.success(response.success);
                        $('#bulkdelete').modal('hide');

                        // Load Page
                        jQuery.ajax({
                            url: "{{ route('admin.todo.list') }}",
                            method: "GET",
                            success: function(data){
                                $('.contactpaginate').html(data);

                            }
                        });
                    }
                });
            });

            // 🟢 On change of reminder type
            $(document).on('change', '#edit-reminder-type', function () {
                var type = $(this).val();

                // Hide all main sections initially
                $('#edit-oneTime, #edit-recurring-section, #edit-custom').hide();

                // Hide all recurring subtype sections
                $('#edit-recurring-daily, #edit-recurring-weekly, #edit-recurring-monthly, #edit-recurring-yearly').hide();

                if (type === 'OneTime') {
                    $('#edit-oneTime').show();
                } else if (type === 'Recurring') {
                    $('#edit-recurring-section').show();

                    // Trigger change on recurring type to show correct subtype
                    $('#edit-recurring-type').trigger('change');
                } else if (type === 'Custom') {
                    $('#edit-custom').show();
                }

                if (type === 'None' || type == '') {
                    $('.edit-notification-type-container').hide();
                }else{
                    $('.edit-notification-type-container').show();
                }
            });

            // 🟢 On change of recurring type
            $(document).on('change', '#edit-recurring-type', function () {
                var recType = $(this).val();

                // Hide all recurring subtype sections
                $('#edit-recurring-daily, #edit-recurring-weekly, #edit-recurring-monthly, #edit-recurring-yearly').hide();

                if (recType === 'Daily') {
                    $('#edit-recurring-daily').show();
                } else if (recType === 'Weekly') {
                    $('#edit-recurring-weekly').show();
                } else if (recType === 'Monthly') {
                    $('#edit-recurring-monthly').show();
                } else if (recType === 'Yearly') {
                    $('#edit-recurring-yearly').show();
                }
            });

        });

       
    </script>

    <!-- add reminder type on change -->
    <script>
        $(document).ready(function() {
            // Toggle sections based on reminder type
            $('#add-reminder-type').on('change', function () {
                const value = $(this).val();

                // Hide all sections first
                $('#addispSchTime, #addisRecurring, #addisCustom').hide();

                if (value === 'OneTime') $('#addispSchTime').show();
                else if (value === 'Recurring') $('#addisRecurring').show();
                else if (value === 'Custom') $('#addisCustom').show();


                if (value === 'None' || value == '') {
                    $('.notification-type-container').hide();
                }else{
                    $('.notification-type-container').show();
                }
            });

            $(document).ready(function() {
                $('#add-recurring-type').on('change', function() {
                    var type = $(this).val();

                    // Hide all recurring sections first
                    $('#recurring-daily-time, #recurring-weekly-days, #recurring-monthly, #recurring-yearly').hide();

                    switch(type) {
                        case 'Daily':
                            $('#recurring-daily-time').show();
                            break;

                        case 'Weekly':
                            $('#recurring-weekly-days').show();
                            break;

                        case 'Monthly':
                            $('#recurring-monthly').show();
                            break;

                        case 'Yearly':
                            $('#recurring-yearly').show();
                            break;

                        default:
                            // if none selected, hide all
                            $('#recurring-daily-time, #recurring-weekly-days, #recurring-monthly, #recurring-yearly').hide();
                            break;
                    }
                });
            });

            // Initialize Select2
            $('.select22').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Initialize Flatpickr for One Time picker
            $('.jsSingledatepicker').flatpickr({
                enableTime: true,
                time_24hr: true,
                dateFormat: "Y-m-d H:i"
            });

            // Initialize Flatpickr for Recurring Time picker (time only)
            $('#recurring-time').flatpickr({
                enableTime: true,
                noCalendar: true,
                time_24hr: true,
                dateFormat: "H:i"
            });

            // Initialize Flatpickr for Custom Time picker (time only)
            $('#custom-time').flatpickr({
                enableTime: true,
                noCalendar: true,
                time_24hr: true,
                dateFormat: "H:i"
            });

            // Initialize Daterangepicker for Custom Date Range (date only)
            $('.jsCustomDateRangePicker').daterangepicker({
                opens: 'auto',
                drops: 'auto',
                autoUpdateInput: false, 
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            });

            // Update input value for Custom Date Range on Apply
            $('.jsCustomDateRangePicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(
                    picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD')
                );
            });

            $('.jsCustomDateRangePicker').on('cancel.daterangepicker', function() {
                $(this).val('');
            });
        });
    </script>

    <script>
        // Sizes the kanban board's own scroll container to the actual
        // remaining viewport space below wherever it currently starts, so
        // scrolling stays contained to the board (not the page) no matter
        // what's rendered above it (status bar, filters, etc.) — measured
        // live instead of a hardcoded calc() that would go stale the
        // moment content above the board changes.
        function sizeKanbanBoard() {
            var $board = $('.card-body-kanban-row');
            if (!$board.length || !$('.disTodoKanban').is(':visible')) {
                return;
            }

            var top = $board[0].getBoundingClientRect().top;
            var bottomGap = 16; // small breathing room at the bottom of the viewport
            var available = window.innerHeight - top - bottomGap;

            $board.css('height', Math.max(available, 250) + 'px');
        }

        $(window).on('resize', sizeKanbanBoard);

        // Task description View More / View Less — delegated so it works
        // for cards rendered now, after a filter reload, or appended by
        // "Load more" alike, with no extra AJAX call (the full text is
        // already on the card via data-full).
        $(document).on('click', '.todo-desc-toggle', function (e) {
            e.preventDefault();

            var $toggle = $(this);
            var $wrap = $toggle.closest('.todo-desc-wrap');
            var $text = $wrap.find('.todo-desc-text');
            var expanded = $toggle.attr('data-expanded') === '1';

            if (expanded) {
                $text.text($toggle.attr('data-short'));
                $toggle.text('View More').attr('data-expanded', '0');
            } else {
                $text.text($toggle.attr('data-full'));
                $toggle.text('View Less').attr('data-expanded', '1');
            }
        });

        function initKanbanSortable() {

            console.log("Initializing TODO Kanban...");

            $(".kanban-todo").sortable({

                items: ".kanban-card",
                connectWith: ".kanban-todo",
                tolerance: "pointer",
                animation: 150,

                /* =========================
                DRAG START
                ========================= */
                start: function (event, ui) {
                    ui.item.addClass("dragging");
                },

                /* =========================
                COLUMN CHANGE (MAIN LOGIC)
                ========================= */
                receive: function (event, ui) {

                    let taskId = ui.item.data("id");
                    let newStatus = $(this).attr("id");
                    let oldStatus = ui.sender.attr("id");

                    let position = ui.item.index() + 1;

                    console.log("Moved:", taskId, "From:", oldStatus, "To:", newStatus);

                    /* --------------------------
                    BUILD ORDER ARRAY (NEW COLUMN)
                    -------------------------- */

                    let order = [];

                    $(this).find(".kanban-card").each(function (index) {
                        order.push({
                            id: $(this).data("id"),
                            position: index + 1
                        });
                    });

                    /* --------------------------
                    AJAX: REORDER (NEW COLUMN)
                    -------------------------- */

                    $.ajax({
                        url: "{{ route('admin.todo.kanbanreorder') }}",
                        method: "POST",
                        data: {
                            column: newStatus,
                            order: order,
                            "_token": "{{ csrf_token() }}"
                        },
                        error: function () {
                            toastr['error']("Reorder failed", "Error");
                        }
                    });

                    /* --------------------------
                    AJAX: STATUS UPDATE
                    -------------------------- */

                    $.ajax({
                        url: "{{ route('admin.todo.kanbanstatusupdate') }}",
                        method: "POST",
                        data: {
                            task_id: taskId,
                            status: newStatus,
                            position: position,
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function (response) {

                            if (response.responseStatus == 200) {
                                toastr['success'](response.responseMessage, 'Success');
                            } else {
                                toastr['error'](response.responseMessage, 'Error');
                            }

                        },
                        error: function () {
                            toastr['error']("Status update failed", "Error");
                        }
                    });

                },

                /* =========================
                SAME COLUMN SORT
                ========================= */
                update: function (event, ui) {

                    if (this === ui.item.parent()[0]) {

                        let column = $(this);
                        let columnId = column.attr("id");

                        let order = [];

                        column.find(".kanban-card").each(function (index) {
                            order.push({
                                id: $(this).data("id"),
                                position: index + 1
                            });
                        });

                        console.log("Reordered in:", columnId, order);

                        $.ajax({
                            url: "{{ route('admin.todo.kanbanreorder') }}",
                            method: "POST",
                            data: {
                                column: columnId,
                                order: order,
                                "_token": "{{ csrf_token() }}"
                            },
                            error: function () {
                                toastr['error']("Reorder failed", "Error");
                            }
                        });

                    }
                },

                /* =========================
                DRAG END
                ========================= */
                stop: function (event, ui) {
                    ui.item.removeClass("dragging");
                }

            }).disableSelection();
        }

        $(document).ready(function () {
            initKanbanSortable();
            sizeKanbanBoard();
        });
    </script>

    <!-- reset filter and save todo filter -->
    <script>

        $(document).on('click','.resetfilter',function(){

            // val([]) rather than deselectAll() — deselectAll() fires a
            // real native 'change' per field, which would trigger each
            // field's own bindFilterChange reload immediately and race
            // against the single reload fired at the end of this handler.
            $('.selectpicker').selectpicker('val', []);

            // Reset the status/priority/follow-up bar back to defaults:
            // "All" active for Status, nothing active for Priority/Follow-up.
            $('#by-todo-followup-due').val('');
            $('.todoStatusCard').removeClass('active');
            $('.todoStatusCard[data-group="status"][data-value=""]').addClass('active');

            // Filter Data Blank
            // if ($('#by-department').val() != '') {
            //     $('#by-department').val('').trigger('change');
            // }

            // if ($('#by-todo-label').val() != '') {
            //     $('#by-todo-label').val('').trigger('change');
            // }

            // if ($('#by-todo-priority').val() != '') {
            //     $('#by-todo-priority').val('').trigger('change');
            // }

            // if ($('#by-todo-status').val() != '') {
            //     $('#by-todo-status').val('').trigger('change');
            // }

            // if ($('#by-assignee').val() != '') {
            //     $('#by-assignee').val('').trigger('change');
            // }

            if ($('#by-followup-before').val() != '') {
                $('#by-followup-before').val('').trigger('change');
            }

            if ($('#by-type').val() != '') {
                $('#by-type').val('').trigger('change');
            }

            if ($('#start-date').val() != '') {
                $('#start-date').trigger('cancel.daterangepicker');
            }

            if ($('#end-date').val() != '') {
                $('#end-date').trigger('cancel.daterangepicker');
            }

            if ($('#complete-task-date-range-picket').val() != '') {
                $('#complete-task-date-range-picket').trigger('cancel.daterangepicker');
            }

            if ($('#achieved-task-date-range-picket').val() != '') {
                $('#achieved-task-date-range-picket').trigger('cancel.daterangepicker');
            }

            if ($('#created-date-range-picker').val() != '') {
                $('#created-date-range-picker').trigger('cancel.daterangepicker');
            }

            if ($('#updated-date-range-picker').val() != '') {
                $('#updated-date-range-picker').trigger('cancel.daterangepicker');
            }

            $(".filter-today-reminder").prop("checked", false).trigger("change");            

            $.ajaxSetup({
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ route('admin.todo.resetfilter') }}",
                method: "POST",
                type: "html",
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data){
                    if(data){
                        toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                    }

                    // Reload whichever view (list/kanban) is currently on
                    // screen, into its own container — same helper every
                    // other filter change already uses.
                    reloadTodoList();
                }
            });

            updateAdminFilterIndicator();

        });

        $(document).on('click','.savetodoFilter',function(){
            var page_list = $('#pagination_list').val();
            var search_text = $('#search_text').val();
            var search_text_kanban = $('#search_text_kanban').val();
            var department_id = $('#by-department').val();
            var todolabel_id = $('#by-todo-label').val();
            var reminder_cycle = $('#by-todo-priority').val();
            var task_status = $('#by-todo-status').val();
            var assignto_id = $('#by-assignee').val();
            var followup_before = $('#by-followup-before').val();
            var created_by = $('#by-created-by').val();
            var today_reminder_task = $('.filter-today-reminder').prop('checked') ? 1 : 0;
            var type_fi = $('#by-type').val();
            var start_on = $('#start-date').val();
            var finish_on = $('#end-date').val();
            var complete_date = $('#complete-task-date-range-picket').val();
            var achieved_date = $('#achieved-task-date-range-picket').val();
            var created_at = $('#created-date-range-picker').val();
            var updated_at = $('#updated-date-range-picker').val();

            $.ajaxSetup({
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ route('admin.todo.savefilter') }}",
                method: "POST",
                type: "html",
                data: {
                    "_token": "{{ csrf_token() }}",
                    page_list:page_list,
                    search_text: search_text,
                    search_text_kanban: search_text_kanban,
                    department_id: department_id,
                    todolabel_id: todolabel_id,
                    reminder_cycle: reminder_cycle,
                    task_status: task_status,
                    assignto_id: assignto_id,
                    followup_before: followup_before,
                    created_by: created_by,
                    today_reminder_task: today_reminder_task,
                    type_fi: type_fi,
                    start_on: start_on,
                    finish_on: finish_on,
                    complete_date: complete_date,
                    achieved_date: achieved_date,
                    created_at: created_at,
                    updated_at: updated_at
                },
                success: function(data){

                    updateAdminFilterIndicator();


                    if(data){
                        toastr['success'](data.res, 'Success', { hideDuration: 3000 });

                        jQuery.ajax({
                            url: "{{ route('admin.todo.list') }}",
                            method: "GET",
                            type: "html",
                            data: {
                                page_list: page_list,
                                search_text: search_text,
                                search_text_kanban: search_text_kanban,
                                department_id: department_id,
                                todolabel_id: todolabel_id,
                                reminder_cycle: reminder_cycle,
                                task_status: task_status,
                                assignto_id: assignto_id,
                                followup_before: followup_before,
                                created_by: created_by,
                                today_reminder_task: today_reminder_task,
                                type_fi: type_fi,
                                start_on: start_on,
                                finish_on: finish_on,
                                complete_date: complete_date,
                                achieved_date: achieved_date,
                                created_at: created_at,
                                updated_at: updated_at
                            },
                            success: function(data){
                                $('.kanban-row').html(data);

                                initKanbanSortable();
                                sizeKanbanBoard();

                            }
                        });
                    }
                }
            });
        });
    </script>

    <!-- delete Staff -->
    <script>
        $(document).ready(function(){
            $('#deleteStaff').on("show.bs.modal",function(e){
                var deleteID = $(e.relatedTarget).data('id');
                $('#todoID2').val(deleteID);
            });

            $('body').on('shown.bs.modal', '#filterpanel', function() {
                $(this).find('.select22f').each(function() {

                    $(this).select2({
                        dropdownParent: $(this).parent()

                    });
                });
            });
        });
    </script>

    <!-- date picker for remiders  -->
    <script>
        $(document).ready(function() {
            var bsRangePickerBasic = $('.bsdatpicket');
            var singledatepicket = $('.singledatepicker');

            // RTL detection (example, if you use RTL)
            var isRtl = $('html').attr('dir') === 'rtl';

            // Basic range picker
            if (bsRangePickerBasic.length) {
                bsRangePickerBasic.daterangepicker({
                    opens: 'auto',
                    drops: 'auto',
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });

                bsRangePickerBasic.on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(
                        picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD')
                    );
                });

                bsRangePickerBasic.on('cancel.daterangepicker', function() {
                    $(this).val('');
                });
            }

            // Single Date Picker
            if (singledatepicket.length) {
                singledatepicket.daterangepicker({
                    opens: 'auto',
                    drops: 'auto',
                    autoUpdateInput: false,
                    singleDatePicker: true,
                    timePicker: true,
                    timePicker24Hour: true,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'YYYY-MM-DD HH:mm'
                    }
                });

                singledatepicket.on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD HH:mm'));
                });

                singledatepicket.on('cancel.daterangepicker', function() {
                    $(this).val('');
                });
            }
        });
    </script>

    <!-- else date pickers  -->
    <script>
        $(document).ready(function(){
            var select22 = $('.select22');
            var select22f = $('.select22f');
            var datepicker = $('.flatpicker-date');
            var datepicker2 = $('.flatpicker-date2');

            if (select22.length) {
                select22.each(function () {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        //   placeholder: 'Select value',
                        dropdownParent: $this.parent()
                    });
                });
            }
            if (datepicker) {
                datepicker.flatpickr({
                    monthSelectorType: 'static',
                    // altInput: true,
                    // altFormat: 'j F, Y',
                    dateFormat: 'Y-m-d',
                    minDate: "today"
                });
            }
            if (datepicker2) {
                datepicker2.flatpickr({
                    monthSelectorType: 'static',
                    // altInput: true,
                    // altFormat: 'j F, Y',
                    dateFormat: 'Y-m-d',

                });
            }

        });
    </script>

    <!-- change to kanban and change to list on change -->
    <script>
        $(document).ready(function(){
            $(document).on("click", ".changetokanban", function () {
                let isChecked = $(".filter-today-reminder").prop("checked");
                loadTodayReminderToggle(true, isChecked);
                            
                $('.disTodoKanban').show();
                $('.disTodoList').hide();

                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var search_text_kanban = $('#search_text_kanban').val();
                var department_id = $('#by-department').val();
                var todolabel_id = $('#by-todo-label').val();
                var reminder_cycle = $('#by-todo-priority').val();
                var task_status = $('#by-todo-status').val();
                var assignto_id = $('#by-assignee').val();
                var followup_before = $('#by-followup-before').val();
                var created_by = $('#by-created-by').val();
                var today_reminder_task = $('.filter-today-reminder').prop('checked') ? 1 : 0;
                var type_fi = $('#by-type').val();
                var start_on = $('#start-date').val();
                var finish_on = $('#end-date').val();
                var complete_date = $('#complete-task-date-range-picket').val();
                var achieved_date = $('#achieved-task-date-range-picket').val();
                var created_at = $('#created-date-range-picker').val();
                var updated_at = $('#updated-date-range-picker').val();

                $.ajaxSetup({
                headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ route("admin.todo.switchto") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "switch": 'kanban',
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if(data){                        
                            // toastr['success'](data.res, 'Success', { hideDuration: 3000 });

                            location.reload();

                            // jQuery.ajax({
                            //     url: "{{ route('admin.todo.list') }}",
                            //     method: "GET",
                            //     type: "html",
                            //     data: {
                            //         page_list: page_list,
                            //         search_text: search_text,
                            //         department_id: department_id,
                            //         todolabel_id: todolabel_id,
                            //         reminder_cycle: reminder_cycle,
                            //         task_status: task_status,
                            //         assignto_id: assignto_id,
                            //         created_by: created_by,
                            //         today_reminder_task: today_reminder_task,
                            //         type_fi: type_fi,
                            //         start_on: start_on,
                            //         finish_on: finish_on,
                            //         complete_date: complete_date,
                            //         achieved_date: achieved_date,
                            //         created_at: created_at,
                            //         updated_at: updated_at
                            //     },
                            //     success: function(data){
                            //         $('body').css('overflow-y', 'hidden');
                            //         $('.contactpaginate').html(data);
                            //     }
                            // });
                        }
                    }
                });


            });

            $(document).on("click", ".changetolist", function () {

                let isChecked = $(".filter-today-reminder").prop("checked");
                loadTodayReminderToggle(false, isChecked);

                $('.disTodoKanban').hide();
                $('.disTodoList').show();

                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var search_text_kanban = $('#search_text_kanban').val();
                var department_id = $('#by-department').val();
                var todolabel_id = $('#by-todo-label').val();
                var reminder_cycle = $('#by-todo-priority').val();
                var task_status = $('#by-todo-status').val();
                var assignto_id = $('#by-assignee').val();
                var followup_before = $('#by-followup-before').val();
                var created_by = $('#by-created-by').val();
                var today_reminder_task = $('.filter-today-reminder').prop('checked') ? 1 : 0;
                var type_fi = $('#by-type').val();
                var start_on = $('#start-date').val();
                var finish_on = $('#end-date').val();
                var complete_date = $('#complete-task-date-range-picket').val();
                var achieved_date = $('#achieved-task-date-range-picket').val();
                var created_at = $('#created-date-range-picker').val();
                var updated_at = $('#updated-date-range-picker').val();

                $.ajaxSetup({
                headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ route("admin.todo.switchto") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "switch": 'todolist',
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if(data){
                            
                            // toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                            location.reload();

                            // jQuery.ajax({
                            //     url: "{{ route('admin.todo.list') }}",
                            //     method: "GET",
                            //     type: "html",
                            //     data: {
                            //         page_list: page_list,
                            //         search_text: search_text,
                            //         department_id: department_id,
                            //         todolabel_id: todolabel_id,
                            //         reminder_cycle: reminder_cycle,
                            //         task_status: task_status,
                            //         assignto_id: assignto_id,
                            //         created_by: created_by,
                            //         today_reminder_task: today_reminder_task,
                            //         type_fi: type_fi,
                            //         start_on: start_on,
                            //         finish_on: finish_on,
                            //         complete_date: complete_date,
                            //         achieved_date: achieved_date,
                            //         created_at: created_at,
                            //         updated_at: updated_at
                            //     },
                            //     success: function(data){
                            //         $('body').css('overflow-y', 'auto');
                            //         $('.contactpaginate').html(data);
                            //     }
                            // });

                        }
                    }
                });

            });
        });
    </script>

    <!-- edit model and detail view model  -->
    <script>
        $(document).ready(function () {

            // 🟦 Edit Modal
            $('#offcanvasEditUser').on('show.bs.offcanvas', function (e) {
                // Reset staff work UI
                $('.edit_staff_work_input_hidden').val('[]');
                $('.EditStaffWorkList').empty();
                $('.edit_staff_work_input').val('');

                const editID = $(e.relatedTarget).data('id');
                const blankImg = "{{ asset('admin/assets/img/avatars/blank.jpeg') }}";
                $('#editID').val(editID);

                $.ajax({
                    url: '{{ route("admin.todo.edit") }}',
                    method: 'GET',
                    data: { id: editID },
                    success: function (data) {
                        
                        // 🧩 Basic Fields
                        $('#edit-department').val(data.department_id).trigger('change');
                        $('#edit-label').val(data.todolabel_id).trigger('change');
                        $('#edit-todo-subject').val(data.task_title);
                        $('#edit-task-desc').val(data.task_description);
                        $('#edit-type').val(data.type).trigger('change');
                        $('#edit-priority').val(data.reminder_cycle).trigger('change');
                        $('#edit-start-date').val(data.start_on);
                        $('#edit-finish-date').val(data.finish_on);
                        $('#edit_support_team_name').val(data.support_team_name);
                        $('#edit_support_team_number').val(data.support_team_number);
                        
                        // Hide all reminder sections first
                        $('#edit-oneTime, #edit-recurring-section, #edit-custom').hide();
                        $('#edit-recurring-daily, #edit-recurring-weekly, #edit-recurring-monthly, #edit-recurring-yearly').hide();

                        const reminderType = data.reminder_type;
                        
                        $('input[name="edit_notification_type[]"]').prop('checked', false);

                        let types = data.notification_type;

                        // Convert to array safely
                        if (typeof types === 'string') {
                            try {
                                types = JSON.parse(types); // if stored as JSON string
                            } catch (e) {
                                types = types.split(','); // if stored as "1,2,3"
                            }
                        }

                        // Ensure it's an array
                        if (!Array.isArray(types)) {
                            types = types ? [types] : [];
                        }

                        if (types.length > 0) {

                            $('.edit-notification-type-container').show();

                            types.forEach(function (type) {
                                $('input[name="edit_notification_type[]"][value="' + type + '"]')
                                    .prop('checked', true);
                            });

                        } else {
                            $('.edit-notification-type-container').hide();
                        }

                        $('#edit-reminder-type').val(data.reminder_type).trigger('change');

                        // 🟩 OneTime Reminder
                        if (reminderType === 'OneTime') {
                            $('#edit-oneTime').show();
                            $('#edit-scheduled-datetime').val(data.scheduled_date_time ? data.scheduled_date_time.replace('T', ' ') : '');
                        }

                        // 🟦 Recurring Reminder
                        else if (reminderType === 'Recurring') {
                            $('#edit-recurring-section').show();
                            $('#edit-recurring-type').val(data.recurring_type).trigger('change');

                            switch (data.recurring_type) {
                                case 'Daily':
                                    $('#edit-recurring-daily').show();

                                    // 1️⃣ Prepare Container
                                    const dailyContainer = $('#edit-daily-times-container');
                                    dailyContainer.empty();

                                    // 2️⃣ Parse multiple daily times
                                    let dailyTimes = [];

                                    try {
                                        if (data.recurring_time) {
                                            dailyTimes = JSON.parse(data.recurring_time);   // e.g. ["09:00","12:00"]
                                        }
                                    } catch (e) {
                                        // fallback if DB stores single time string
                                        if (typeof data.recurring_time === 'string' && data.recurring_time.length > 0) {
                                            dailyTimes = [data.recurring_time];
                                        }
                                    }

                                    // 3️⃣ If no times, add default empty input
                                    if (dailyTimes.length === 0) {
                                        dailyTimes = [''];
                                    }

                                    // 4️⃣ Generate multiple inputs
                                    dailyTimes.forEach((time, index) => {
                                        let html = `
                                            <div class="input-group mb-2 edit-daily-time-group">
                                                <input type="text" name="recurring_time_daily[]" value="${time}" class="form-control flatpickertime" placeholder="Select time">
                                                <button type="button" class="btn btn-outline-danger remove-edit-daily-time-btn" ${index === 0 ? 'style="display:none;"' : ''}>✕</button>
                                            </div>
                                        `;
                                        dailyContainer.append(html);
                                    });

                                    // 5️⃣ Re-init flatpickr
                                    $(".flatpickertime").flatpickr({
                                        enableTime: true,
                                        noCalendar: true,
                                        dateFormat: "H:i",
                                        time_24hr: false
                                    });

                                    break;

                                case 'Weekly':
                                    $('#edit-recurring-weekly').show();

                                    // 1️⃣ Set weekdays
                                    if (data.recurring_weekdays) {
                                        let weekdays = JSON.parse(data.recurring_weekdays);
                                        $('#edit-recurring-weekdays').val(weekdays).trigger('change');
                                    }

                                    // 2️⃣ Handle multiple times
                                    const container = $('#edit-weekly-times-container');
                                    container.empty(); // clear old inputs

                                    if (data.recurring_time) {
                                        let times = [];
                                        try {
                                            times = JSON.parse(data.recurring_time); // parse ["12:00","13:00","14:00"]
                                        } catch (e) {
                                            if (typeof data.recurring_time === 'string' && data.recurring_time.length > 0) {
                                                times = [data.recurring_time];
                                            }
                                        }

                                        // Create time input fields dynamically
                                        times.forEach((t, index) => {
                                            const timeField = `
                                                <div class="input-group mb-2 time-group">
                                                    <input type="text" name="recurring_time_weekly[]" class="form-control flatpickertime" value="${t}">
                                                    <button type="button" class="btn btn-outline-danger remove-time-btn" ${index === 0 ? 'style="display:none;"' : ''}>✕</button>
                                                </div>
                                            `;
                                            container.append(timeField);
                                        });
                                    } else {
                                        // Default one field if no time
                                        const timeField = `
                                            <div class="input-group mb-2 time-group">
                                                <input type="text" name="recurring_time_weekly[]" class="form-control flatpickertime" placeholder="Select time">
                                                <button type="button" class="btn btn-outline-danger remove-time-btn" style="display:none;">✕</button>
                                            </div>
                                        `;
                                        container.append(timeField);
                                    }

                                    // 3️⃣ Reinitialize flatpickr for all new inputs
                                    $('.flatpickertime').flatpickr({
                                        enableTime: true,
                                        noCalendar: true,
                                        dateFormat: "H:i",
                                        time_24hr: false
                                    });

                                    break;


                                case 'Monthly':
                                    $('#edit-recurring-monthly').show();
                                    $('#edit-recurring-month-day').val(data.recurring_month_day ?? '');
                                    if (data.recurring_time) {
                                        const timeStr = data.recurring_time.substring(0, 5); // "HH:mm"
                                        const fpInstance = document.getElementById('edit-recurring-monthly-time')._flatpickr;
                                        if (fpInstance) {
                                            fpInstance.setDate(timeStr, true, "H:i");
                                        } else {
                                            $('#edit-recurring-monthly-time').val(timeStr);
                                        }
                                    } else {
                                        $('#edit-recurring-monthly-time').val('');
                                    }
                                    break;

                                case 'Yearly':
                                    $('#edit-recurring-yearly').show();

                                    if (data.recurring_year_month_day) {
                                        const currentYear = new Date().getFullYear();
                                        let dateStr = currentYear + '-' + data.recurring_year_month_day;

                                        if ($('#edit-recurring-year-month-day')[0]._flatpickr) {
                                            $('#edit-recurring-year-month-day')[0]._flatpickr.setDate(dateStr, true, "Y-m-d");
                                        } else {
                                            $('#edit-recurring-year-month-day').val(data.recurring_year_month_day);
                                        }
                                    } else {
                                        $('#edit-recurring-year-month-day').val('');
                                    }

                                    if (data.recurring_time) {
                                        const timeStr = data.recurring_time.substring(0, 5); // "HH:mm"
                                        const fpInstance = document.getElementById('edit-recurring-yearly-time')._flatpickr;
                                        if (fpInstance) {
                                            fpInstance.setDate(timeStr, true, "H:i");
                                        } else {
                                            $('#edit-recurring-yearly-time').val(timeStr);
                                        }
                                    } else {
                                        $('#edit-recurring-yearly-time').val('');
                                    }
                                    break;
                            }
                        }

                        // 🟨 Custom Reminder
                        else if (reminderType === 'Custom') {
                            $('#edit-custom').show();
                            if (data.custom_start_date && data.custom_end_date) {
                                $('#edit-custom-date-range').val(`${data.custom_start_date} to ${data.custom_end_date}`);
                            }
                            if (data.custom_time) {
                                $('#edit-custom-time').val(data.custom_time.substring(0, 5));
                            }
                        }

                        // 👥 Assignees
                        const assign_id = data.assignto_id ? data.assignto_id.split(",") : [];
                        $('#edit-assignee').val(assign_id).trigger('change');

                        // 🧑 Created Info
                        $('#todocreatedbyId').text(data.adminId || '-');
                        $('#todocreatedby').text(data.adminname || '-');
                        $('#todocreatedon').text(data.created_at || '-');

                        // 🖼️ File Preview
                        $('.uploadedAvatarEd').attr('src', data.file_url && data.file_url !== '' ? data.file_url : blankImg);

                        let staffWorkArray = [];

                        if (data.staff_work_name) {
                            try {
                                staffWorkArray = typeof data.staff_work_name === 'string'
                                    ? JSON.parse(data.staff_work_name)
                                    : data.staff_work_name;
                            } catch (e) {
                                staffWorkArray = [];
                            }
                        }

                        // ✅ clear old data first
                        $('.EditStaffWorkList').html('');
                        $('.edit_staff_work_input').val('');

                        // ✅ if array has values
                        if (staffWorkArray.length > 0) {

                            // 🔥 First item → input field
                            $('.edit_staff_work_input').val(staffWorkArray[0]);

                            // 🔥 Remaining items → list
                            staffWorkArray.slice(1).forEach(function (work) {
                                $('.EditStaffWorkList').append(`
                                    <div class="input-group mb-2 staff-work-item">
                                        <input type="text" 
                                            class="form-control staff-work-text"
                                            value="${work}" 
                                            placeholder="Enter staff work...">

                                        <button type="button" 
                                            class="btn btn-outline-danger btn-sm remove-staff-work-btn">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                `);
                            });
                        }

                        $('.edit_staff_work_input_hidden').val(JSON.stringify(staffWorkArray));
                    },
                    error: function (xhr) {
                        console.error('Error fetching todo:', xhr.responseText);
                    }
                });
            });

            // 🟩 Detail View
            $(document).on('click', '.detail-tab-btn', function () {
                const todoId = $('#todo_notes_id').val();
                const container = $('#todoDetailsContainer');

                if (!todoId) {
                    container.html('<div class="text-danger p-3">No Todo selected.</div>');
                    return;
                }

                container.html('<div class="p-3 text-muted">Loading details...</div>');

                $.ajax({
                    url: '{{ route("admin.todo.edit") }}',
                    method: 'GET',
                    data: { id: todoId },
                    success: function (data) {
                        let html = `
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <p><strong>Task Title:</strong> ${data.task_title || '-'}</p>
                                    <p><strong>Description:</strong> ${data.task_description || '-'}</p>
                                    <p><strong>Type:</strong> ${data.type || '-'}</p>
                                    <p><strong>Reminder Type:</strong> ${data.reminder_type || '-'}</p>
                                    <p><strong>Recurring Cycle:</strong> ${data.reminder_cycle || '-'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Start On:</strong> ${data.start_on || '-'}</p>
                                    <p><strong>Finish On:</strong> ${data.finish_on || '-'}</p>
                                    <p><strong>Scheduled Date/Time:</strong> ${data.scheduled_date_time || '-'}</p>
                                    <p><strong>Status:</strong> ${data.task_status || '-'}</p>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <hr>
                                    <p><strong>Assignee(s):</strong> ${data.assignto_names || '-'}</p>
                                    <p><strong>Created By:</strong> ${data.adminname || '-'}</p>
                                    <p><strong>Created On:</strong> ${data.created_at || '-'}</p>
                                </div>
                            </div>
                        `;
                        container.html(html);
                    },
                    error: function () {
                        container.html('<div class="text-danger p-3">Failed to load details.</div>');
                    }
                });
            });

            $(document).on('click', '.staff-work-tab-btn', function () {

                const todoId = $('#todo_notes_id').val();
                const todoStaffTableBody = $('#todoStaffTableBody');

                let tbody = $('.tab-pane.active').find('#todoStaffTableBody');

                if (!todoId) {
                    todoStaffTableBody.html('<tr><td colspan="2" class="text-danger text-center">No Todo selected.</td></tr>');
                    return;
                }

                todoStaffTableBody.html('<tr><td colspan="2" class="text-muted text-center">Loading staff work...</td></tr>');

                $.ajax({
                    url: '{{ route("admin.todo.edit") }}', // same API
                    method: 'GET',
                    data: { id: todoId },

                    success: function (data) {

                        todoStaffTableBody.html('');

                        let staffWorks = data.staff_work_name || [];
                        
                        // ✅ If empty
                        if (!staffWorks || staffWorks.length === 0 || staffWorks == '[]') {
                            todoStaffTableBody.html('<tr><td colspan="2" class="text-center">No staff work found</td></tr>');
                            return;
                        }


                        // ✅ convert string → array
                        if (typeof staffWorks === 'string') {
                            try {
                                staffWorks = JSON.parse(staffWorks);
                            } catch (e) {
                                staffWorks = [];
                            }
                        }

                        // ✅ now safe to loop
                        staffWorks.forEach(function (item, index) {
                            todoStaffTableBody.append(`
                                <tr>
                                    <td>${item}</td>
                                    <td>
                                        <a href="javascript:void(0);" class="delete-staff-work" data-index="${index}" data-todo-id="${todoId}">
                                            <i class="ti ti-trash ti-sm text-danger"></i>
                                        </a>
                                    </td>
                                </tr>
                            `);
                        });

                    },
                    error: function () {
                        todoStaffTableBody.html('<tr><td colspan="2" class="text-danger text-center">Failed to load staff work.</td></tr>');
                    }
                });
            });

            $(document).on('click', '.delete-staff-work', function () {

                let btn = $(this);
                let index = btn.data('index');
                let todoId = btn.data('todo-id');

                $.ajax({
                    url: '/admin/todo/delete-staff-work', // change if needed
                    type: 'POST',
                    data: {
                        index: index,
                        todo_id: todoId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function (response) {

                        // ✅ remove row from UI instantly
                        btn.closest('tr').remove();
                    },

                    error: function () {
                        toastr.error('Failed to delete');
                    }
                });
            });
        });
    </script>

    <!-- update stage status -->
    <script>

        // Put this at the top of your <script> or before any AJAX call
        const statusColors = {
            "New Task": "info",
            "In Process": "primary",
            "Incomplete": "danger",
            "Complete": "warning",
            "On Hold": "secondary",
            "Cancelled": "dark",
            "Achieved": "success",
            "Always": "success"
        };

        $(document).on('change', '.filter-today-reminder', function () {
            // Automatically trigger save button
            $(".savetodoFilter").trigger("click");
             // Re-fetch kanban & reinitialize
             reloadTodoList();
        });


        $(document).on('click','.updateStatus',function(e){

            // let pageList = $('.pagination .page-item.active .page-link').text();
            var todoID = $(this).attr('data-id');
            var todoText = $(this).text();

            // Update Status with Post Method
            $.ajaxSetup({
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            jQuery.ajax({
                url : '{{ route("admin.todo.updateStatus") }}',
                method: "POST",
                type: "html",
                data: {
                    "id": todoID,
                    "text": todoText,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    toastr['success'](data.message, 'Success', { hideDuration: 3000 });

                    let newStatus = data.status.replace('Mark as ', ''); // e.g. "Mark as Complete" → "Complete"
                    let badgeColor = statusColors[newStatus] || 'info';

                    // Find the badge span for the clicked row
                    let $row = $(e.target).closest('td'); 
                    let $badge = $row.find('.badge');

                    // Update text
                    $badge.text(newStatus + " ");
                    $badge.append("<i class='ti ti-chevron-down ti-xs'></i>");

                    // Remove old bg-label-* and add new one
                    $badge.removeClass(function(index, className) {
                        return (className.match(/(^|\s)bg-label-\S+/g) || []).join(' ');
                    }).addClass('bg-label-' + badgeColor);
                }
            });

        });
    </script>

    <!-- Seatch Filter Start Here -->
    <script>
        $(document).ready(function(){

            // Common AJAX setup for CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            // Generic event handler for change and input events on filter elements
            function bindFilterChange(selector) {
                $(selector).on('change input', function () {
                    reloadTodoList();
                });
            }
            // Bind change/input events to all filter elements
            bindFilterChange('#pagination_list');
            bindFilterChange('#search_text');
            bindFilterChange('#search_text_kanban');
            bindFilterChange('#by-department');
            bindFilterChange('#by-todo-label');
            bindFilterChange('#by-todo-priority');
            bindFilterChange('#by-todo-status');
            bindFilterChange('#by-assignee');
            bindFilterChange('#by-followup-before');
            bindFilterChange('#by-created-by');
            bindFilterChange('#by-type');
            bindFilterChange('.filter-today-reminder');

            // ===============================
            // Todo status/priority/follow-up bar -> apply matching filter
            //
            // Each pill belongs to one group (status / priority / followup)
            // via data-group. Clicking only clears/sets pills within that
            // SAME group — the other groups' selections are left alone —
            // so Status + Priority + Follow-up combine (AND together)
            // instead of one click resetting the others.
            // ===============================
            var todoFieldSelectorMap = {
                task_status: '#by-todo-status',
                reminder_cycle: '#by-todo-priority',
            };

            // Status behaves like a radio button: exactly one pill (or
            // "All") must be highlighted for whatever #by-todo-status
            // actually holds right now — not just after a bar click, but
            // also if it's changed from the filter-panel dropdown, so the
            // bar never shows a stale/wrong selection.
            function syncActiveStatusPill() {
                var current = $('#by-todo-status').val() || [];
                var value = current.length ? String(current[0]) : '';

                $('.todoStatusCard[data-group="status"]').removeClass('active');

                var $match = $('.todoStatusCard[data-group="status"][data-value="' + value + '"]');
                if (!$match.length) {
                    $match = $('.todoStatusCard[data-group="status"][data-value=""]'); // fall back to "All"
                }
                $match.addClass('active');
            }

            syncActiveStatusPill();
            $('#by-todo-status').on('change', syncActiveStatusPill);

            $(document).on('click', '.todoStatusCard', function (e) {
                e.preventDefault();

                var $this = $(this);
                var group = $this.data('group');
                var field = $this.data('field');
                var value = $this.data('value') || '';
                var wasActive = $this.hasClass('active');

                if (wasActive && !value) {
                    return; // already at rest (e.g. "All" already active) — avoid a duplicate request
                }

                $('.todoStatusCard[data-group="' + group + '"]').removeClass('active');

                var effectiveValue = wasActive ? '' : value; // clicking the active pill again clears it
                if (!wasActive) {
                    $this.addClass('active');
                }

                if (field === 'followup_due') {
                    $('#by-todo-followup-due').val(effectiveValue);
                } else {
                    var selector = todoFieldSelectorMap[field];
                    // val() (not deselectAll()) — deselectAll() fires a real
                    // native 'change' on the field, which would trigger its
                    // own bindFilterChange reload immediately and race
                    // against the single reload fired below.
                    $(selector).selectpicker('val', effectiveValue ? [String(effectiveValue)] : []);
                }

                reloadTodoList();
                updateAdminFilterIndicator();
            });

            // Date Range Picker for Start and End Dates with Apply and Cancel Event Handling
            function bindDatePicker(selector) {
                $(selector).on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD'));
                    reloadTodoList();
                }).on('cancel.daterangepicker', function () {
                    $(this).val('');
                    reloadTodoList();
                });
            }

            function bindDateRangePicker(selector) {
                $(selector).on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                    reloadTodoList();
                }).on('cancel.daterangepicker', function () {
                    $(this).val('');
                    reloadTodoList();
                });
            }

            // Bind date range picker to the relevant fields
            bindDatePicker('input[name="todo_start_date"]');
            bindDatePicker('input[name="todo_end_date"]');
            bindDateRangePicker('input[name="complate_date_range"]');
            bindDateRangePicker('input[name="achieved_date_range"]');
            bindDateRangePicker('input[name="created_date_range"]');
            bindDateRangePicker('input[name="updated_date_range"]');


            $('body').on('click','.pagination a',function(e){
                e.preventDefault();
                var url = $(this).attr('href');
                var url_data = getFilterData();
                url_data.view_mode = 'list'; // pagination only ever renders in the table view
                var finalURL = url + "&" + $.param(url_data);

                getPaginations(finalURL);
                window.history.pushState("", url);

            });

            function getPaginations(finalURL){
                $.ajax({
                    url : finalURL
                }).done(function(data){
                    $('.disTodoList .contactpaginate').html(data);


                }).fail(function(){

                    alert("Something gone wrong!")
                });
            }

        });
    </script>

    <!-- table checkbox logics --> 
    <script>
        $(document).ready(function(){
            // Click Event on Master Check
            $(document).on('click','.checkboxSelectAll',function(){
                var listcheckitem = $('.listitem :checkbox');
                var isMasterChecked = $(this).is(":checked");

                // row checked list
                var checkedList = listcheckitem.length;

                // alert(checkedList);

                if(isMasterChecked){
                    if (checkedList > 0) {
                        $('.bulkactions').prop("disabled",false);
                    } else {
                        $('.bulkactions').prop("disabled",true);
                    }

                }else{
                    $('.bulkactions').prop("disabled",true);
                }
                listcheckitem.prop("checked", isMasterChecked);
            });

            // Change Event on each item checkbox
            $(document).on('change','.listitem :checkbox',function(){
                var listcheckitem = $('.listitem :checkbox');
                var masterCheck = $('.checkboxSelectAll');
                // Total Checkboxes in list
                var totalItems = listcheckitem.length;
                // Total Checked Checkboxes in list
                var checkedItems = listcheckitem.filter(":checked").length;
                //If all are checked
                if (totalItems == checkedItems) {
                    masterCheck.prop("indeterminate", false);
                    masterCheck.prop("checked", true);
                    $('.bulkactions').prop("disabled",false);
                }
                // Not all but only some are checked
                else if (checkedItems > 0 && checkedItems < totalItems) {
                    masterCheck.prop("indeterminate", true);
                    $('.bulkactions').prop("disabled",false);
                }
                //If none is checked
                else {
                    masterCheck.prop("indeterminate", false);
                    masterCheck.prop("checked", false);
                    $('.bulkactions').prop("disabled",true);
                }
            });
        });
    </script>

    <!-- Add/edit/Delete Candidate -->
    <script>
        
        $(document).on('click', '#addnewcandidateBtn', function(e) {
            e.preventDefault();

            var formData = new FormData();
            var _token = "{{ csrf_token() }}";

            // --- Basic fields ---
            formData.append('department_id', $('#add-department').val());
            formData.append('todolabel_id', $('#add-label').val());
            formData.append('task_title', $('#add-todo-subject').val());
            formData.append('task_description', $('#add-task-desc').val());
            formData.append('start_on', $('#add-start-date').val());
            formData.append('finish_on', $('#add-finish-date').val());
            formData.append('reminder_cycle', $('#add-priority').val());
            formData.append('support_team_name', $('#add_support_team_name').val());
            formData.append('support_team_number', $('#add_support_team_number').val());
            formData.append('_token', _token);

            // Get all checked notification checkboxes
            $('input[name="notification_type[]"]:checked').each(function () {
                formData.append('notification_type[]', $(this).val());
            });

            // --- File attachment ---
            if ($('#add-file-attachment')[0].files.length > 0) {
                formData.append('file_attachment', $('#add-file-attachment')[0].files[0]);
            }

            // --- Reminder type ---
            var reminderType = $('#add-reminder-type').val();
            formData.append('reminder_type', reminderType);

            // --- OneTime ---
            if (reminderType === 'OneTime') {
                formData.append('scheduled_date_time', $('#add-start-on-time').val());
            }

            // --- Recurring ---
            if (reminderType === 'Recurring') {
                var recurringType = $('#add-recurring-type').val();
                formData.append('recurring_type', recurringType);

                switch (recurringType) {
                    case 'Daily':
                        // Collect all daily time inputs
                        var dailyTimes = [];
                        $('#recurring-daily-time input[name="recurring_time_daily[]"]').each(function () {
                            var val = $(this).val();
                            if (val) {
                                dailyTimes.push(val);
                            }
                        });

                        // Save as JSON
                        formData.append('recurring_time_daily', dailyTimes.length ? JSON.stringify(dailyTimes) : null);
                        break;

                    case 'Weekly':
                        // Get selected weekdays (array)
                        var weekdays = $('#recurring-weekly-days select[name="recurring_weekdays[]"]').val();
                        formData.append('recurring_weekdays', weekdays ? JSON.stringify(weekdays) : null);

                        // Collect all weekly time inputs
                        var weeklyTimes = [];
                        $('#recurring-weekly-days input[name="recurring_time_weekly[]"]').each(function() {
                            var val = $(this).val();
                            if (val) {
                                weeklyTimes.push(val);
                            }
                        });

                        // Append as JSON string
                        formData.append('recurring_time_weekly', weeklyTimes.length ? JSON.stringify(weeklyTimes) : null);
                        break;


                    case 'Monthly':
                        formData.append('recurring_month_day', $('#recurring-monthly input[name="recurring_month_day"]').val());
                        formData.append('recurring_time_monthly', $('#recurring-monthly input[name="recurring_time_monthly"]').val());
                        break;

                    case 'Yearly':
                        formData.append('recurring_year_month_day', $('#recurring-yearly input[name="recurring_year_month_day"]').val());
                        formData.append('recurring_time_yearly', $('#recurring-yearly input[name="recurring_time_yearly"]').val());
                        break;
                }
            }


            // --- Custom ---
            if (reminderType === 'Custom') {
                formData.append('custom_date_range', $('#custom-date-range-picker').val());
                formData.append('custom_time', $('#custom-time').val());
            }

            // --- Assignees ---
            var assignees = $('#add-assignee').val();
            if (assignees) {
                assignees.forEach(function(id) {
                    formData.append('assignto_id[]', id);
                });
            }

            // --- Staff Work ---
            let staffWorkList = [];
            staffWorkList.push($('.add_staff_work_input').val().trim());
            $('.staffWorkList .staff-work-text').each(function() {
                staffWorkList.push($(this).val().trim());
            });
            formData.append('add_staff_work', JSON.stringify(staffWorkList));

            // --- Form validation ---
            var addnewcandidateForm = $('#addNewUserForm');
            addnewcandidateForm.validate({
                rules: {
                    task_title: { required: true },
                    department_id: { required: true },
                    todolabel_id: { required: true },
                },
                messages: {
                    task_title: { required: "Please enter task title" },
                    department_id: { required: "Please select department" },
                    todolabel_id: { required: "Please select todo label" },
                },
            });

            if (addnewcandidateForm.valid()) {
                $.ajax({
                    url: "{{ route('admin.todo.store') }}",
                    method: "POST",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(response) {
                        toastr.success(response.success);

                        // Reset form
                        addnewcandidateForm[0].reset();
                        $('.uploadedAvatar').attr('src', "{{ asset('admin/assets/img/avatars/blank.jpeg') }}");
                        $('#offcanvasAddUser').offcanvas('hide');

                        // Reset select2
                        $('.select22').each(function() {
                            $(this).val(null).trigger('change');
                        });

                        // Reload Todo list
                        reloadTodoList();
                        // $.ajax({
                        //     url: "{{ route('admin.todo.list') }}",
                        //     method: "GET",
                        //     success: function(data) {
                        //         $('.contactpaginate').html(data);
                        //     }
                        // });
                    },
                    error: function(err) {
                        toastr.error('Something went wrong!');
                        console.log(err);
                    }
                });
            }

            return false;
        });

        $(document).on('click', '#editnewcandidateBtn', function (e) {
            e.preventDefault();

            const formData = new FormData();
            const _token = "{{ csrf_token() }}";

            // 🧩 Basic fields
            formData.append('edit_id', $('#editID').val());
            formData.append('department_id', $('#edit-department').val());
            formData.append('todolabel_id', $('#edit-label').val());
            formData.append('task_title', $('#edit-todo-subject').val());
            formData.append('task_description', $('#edit-task-desc').val());
            formData.append('start_on', $('#edit-start-date').val());
            formData.append('finish_on', $('#edit-finish-date').val());
            formData.append('support_team_name', $('#edit_support_team_name').val());
            formData.append('support_team_number', $('#edit_support_team_number').val());
            formData.append('reminder_cycle', $('#edit-priority').val());
            formData.append('type', $('#edit-type').val());
            formData.append('_token', _token);

            // Get all checked notification checkboxes
            $('input[name="edit_notification_type[]"]:checked').each(function () {
                formData.append('notification_type[]', $(this).val());
            });
            
            // 🖼️ File (optional)
            if ($('#edit-file-attachment')[0].files.length > 0) {
                formData.append('file_attachment', $('#edit-file-attachment')[0].files[0]);
            }

            // 🕒 Reminder type
            const reminderType = $('#edit-reminder-type').val();
            formData.append('reminder_type', reminderType);

            // 🟩 OneTime Reminder
            if (reminderType === 'OneTime') {
                formData.append('scheduled_date_time', $('#edit-scheduled-datetime').val());
            }

            // 🟦 Recurring Reminder
            else if (reminderType === 'Recurring') {
                const recurringType = $('#edit-recurring-type').val();
                formData.append('recurring_type', recurringType);

                switch (recurringType) {
                    case 'Daily':
                        // Collect ALL daily time inputs
                        const dailyTimes = [];
                        $('#edit-daily-times-container input[name="recurring_time_daily[]"]').each(function () {
                            const val = $(this).val();
                            if (val) dailyTimes.push(val);
                        });

                        // Save JSON array or null
                        formData.append('recurring_time_daily', dailyTimes.length ? JSON.stringify(dailyTimes) : null);
                        break;

                    case 'Weekly':
                        // 🗓️ Collect selected weekdays
                        const weekdays = $('#edit-recurring-weekdays').val() || [];
                        formData.append('recurring_weekdays', weekdays.length ? JSON.stringify(weekdays) : null);

                        // ⏰ Collect multiple times
                        const weeklyTimes = [];
                        $('#edit-weekly-times-container input[name="recurring_time_weekly[]"]').each(function() {
                            const val = $(this).val();
                            if (val) weeklyTimes.push(val);
                        });

                        // Append as JSON string
                        formData.append('recurring_time_weekly', weeklyTimes.length ? JSON.stringify(weeklyTimes) : null);
                        break;


                    case 'Monthly':
                        formData.append('recurring_month_day', $('#edit-recurring-month-day').val());
                        formData.append('recurring_time', $('#edit-recurring-monthly-time').val());
                        break;

                    case 'Yearly':
                        formData.append('recurring_year_month_day', $('#edit-recurring-year-month-day').val());
                        formData.append('recurring_time', $('#edit-recurring-yearly-time').val());
                        break;
                }
            }

            // 🟨 Custom Reminder
            else if (reminderType === 'Custom') {
                formData.append('custom_date_range', $('#edit-custom-date-range').val());
                formData.append('custom_time', $('#edit-custom-time').val());
            }

            // 👥 Assignees
            const assignees = $('#edit-assignee').val();
            if (assignees) {
                assignees.forEach(id => formData.append('assignto_id[]', id));
            }

            // 🧱 Staff Work
            updateEditStaffWorkHidden();
            formData.append('edit_staff_work_input_hidden', $('.edit_staff_work_input_hidden').val());

            // ✅ Validation
            const editForm = $('#editNewUserForm');
            editForm.validate({
                rules: {
                    task_title: { required: true },
                    department_id: { required: true },
                    todolabel_id: { required: true },
                },
                messages: {
                    task_title: { required: "Please enter task title" },
                    department_id: { required: "Please select department" },
                    todolabel_id: { required: "Please select todo label" },
                },
            });

            if (editForm.valid()) {
                $.ajax({
                    url: "{{ route('admin.todo.update') }}",
                    method: "POST",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        toastr.success(response.success || 'Todo updated successfully');

                        // Reset select2
                        $('.select22').each(function () {
                            $(this).val(null).trigger('change');
                        });

                        // updateTodoList
                        updateTodoList(response.post, response.assignees);
                        updateKanban(response.post, response.assignees);

                        // 🧹 Reset form + image
                        editForm[0].reset();
                        $('.uploadedAvatarEd').attr('src', "{{ asset('admin/assets/img/avatars/blank.jpeg') }}");
                        $('#offcanvasEditUser').offcanvas('hide');

                        // 🔄 Reload Todo list
                       //reloadTodoList();

                    },
                    error: function (err) {
                        toastr.error('Something went wrong!');
                        console.error(err);
                    }
                });
            }

            return false;
        });



        $(document).on('click','.deletebtn',function(e){
            e.preventDefault();
            var formdata = $('#deleteTodoForm').serialize();
            $.ajax({
                url: "{{ route('admin.todo.delete') }}",
                method: "POST",
                data: formdata,
                success: function(response){
                    toastr.success(response.success);
                    // addnewcandidateForm[0].reset();
                    // reset image
                    $('#deleteStaff').modal('hide');
                    // Load Page
                    // jQuery.ajax({
                    //     url: "{{ route('admin.todo.list') }}",
                    //     method: "GET",
                    //     success: function(data){
                    //         $('.contactpaginate').html(data);

                    //     }
                    // });

                    reloadTodoList();

                }
            });
        });
      
    </script>

    <script>
       $(document).on('click', '.view-todo-btn-close', function () {

// ✅ FIX: remove focus first
$(this).blur();

const urlParams = new URLSearchParams(window.location.search);
const openModal = urlParams.get('open_todo_model');

if (openModal) {
    $('#viewTodoModal').modal('hide');

    if (window.history.pushState) {
        const baseUrl = "{{ route('admin.todo.list') }}";
        window.history.pushState({}, '', baseUrl);
    }
}
});


$('#viewTodoModal').on('hidden.bs.modal', function () {
    document.activeElement.blur();
});
    </script>

    <script>
         $(document).ready(function(){
            // Add new time input
            $(document).on('click', '#edit-add-weekly-time-btn', function() {
                const container = $('#edit-weekly-times-container');
                const newField = `
                    <div class="input-group mb-2 time-group">
                        <input type="text" name="recurring_time_weekly[]" class="form-control flatpickertime" placeholder="Select time">
                        <button type="button" class="btn btn-outline-danger remove-time-btn">✕</button>
                    </div>
                `;
                container.append(newField);

                // Initialize flatpickr
                $('.flatpickertime').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: false
                });
            });

            // Remove time input
            $(document).on('click', '.remove-time-btn', function() {
                $(this).closest('.time-group').remove();
            });
        });
    </script>

    <script>
         $(document).ready(function(){
            // =======================
            // DAILY TIME - ADD
            // =======================
            $(document).on("click", "#add-daily-time-btn", function () {
                let html = `
                    <div class="input-group mb-2 daily-time-group">
                        <input type="text" name="recurring_time_daily[]" class="form-control flatpickertime" placeholder="Select time">
                        <button type="button" class="btn btn-outline-danger remove-daily-time-btn">✕</button>
                    </div>
                `;

                $("#daily-times-container").append(html);

                $(".flatpickertime").flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                });
            });

            // =======================
            // DAILY TIME - REMOVE
            // =======================
            $(document).on("click", ".remove-daily-time-btn", function () {
                $(this).closest(".daily-time-group").remove();
            });


            // ==================================
            // EDIT MODE - ADD DAILY TIME
            // ==================================
            $(document).on("click", "#edit-add-daily-time-btn", function () {
                let html = `
                    <div class="input-group mb-2 edit-daily-time-group">
                        <input type="text" name="recurring_time_daily[]" class="form-control flatpickertime" placeholder="Select time">
                        <button type="button" class="btn btn-outline-danger remove-edit-daily-time-btn">✕</button>
                    </div>
                `;

                $("#edit-daily-times-container").append(html);

                $(".flatpickertime").flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                });
            });

            // ==================================
            // EDIT MODE - REMOVE
            // ==================================
            $(document).on("click", ".remove-edit-daily-time-btn", function () {
                $(this).closest(".edit-daily-time-group").remove();
            });

        });
    </script>

    <script>
        $(document).on('click', '.load-more-kanban', function () {

            let btn        = $(this);
            let stageSlug  = btn.data('stage');   // slug for DOM
            let statusName = btn.data('status');  // real status for query
            let offset     = btn.data('offset');

            // UI state
            btn.prop('disabled', true);
            btn.find('.btn-text').text('Loading...');
            btn.find('.spinner-border').removeClass('d-none');

            $.ajax({
                url: '{{ route("admin.todo.loadMoreKanban") }}',
                method: 'GET',
                data: {
                    stage_id: statusName,
                    offset: offset,
                    ...getFilterData() // optional filters like deal pipeline
                },

                success: function (html) {

                    if ($.trim(html) === '') {
                        btn.fadeOut(200, function () { $(this).remove(); });
                        return;
                    }

                    let container = $('.stage-' + stageSlug);
                    let newCards  = $(html).css({ opacity: 0 });

                    container.append(newCards);

                    newCards.animate({ opacity: 1 }, 400);

                    // smooth scroll after append
                    setTimeout(function(){
                        newCards.last()[0].scrollIntoView({
                            behavior: "smooth",
                            block: "end"
                        });
                    }, 50);

                    btn.data('offset', offset + 4);
                },

                complete: function () {
                    btn.prop('disabled', false);
                    btn.find('.btn-text').text('Load more');
                    btn.find('.spinner-border').addClass('d-none');
                }

            });

        });
    </script>
            
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const openModal = urlParams.get('open_todo_model');
        const taskId = urlParams.get('todo_model_task_id');

        if (openModal === 'true' && taskId) {
            // Wait until DOM is ready and modal button exists
            setTimeout(() => {
                const todoBtn = document.querySelector(`.todo_btn_${taskId}`);
                const detailTabBtn = document.querySelector(`.detail-tab-btn`);

                if (todoBtn) {
                    // Trigger modal open
                    todoBtn.click();

                    // Wait a moment for modal to render fully before switching tab
                    setTimeout(() => {
                        // Set hidden input value
                        $('#todo_notes_id').val(taskId);

                        // Click detail tab
                        if (detailTabBtn) {
                            detailTabBtn.click();
                        }
                    }, 300); // adjust delay if modal loads slowly
                } else {
                    // Fallback: open modal manually
                    const viewModal = document.getElementById('viewTodoModal');
                    if (viewModal) {
                        const modal = new bootstrap.Modal(viewModal);
                        modal.show();

                        // After modal shows, go to details tab
                        setTimeout(() => {
                            $('#todo_notes_id').val(taskId);
                            if (detailTabBtn) detailTabBtn.click();
                        }, 300);
                    }
                }
            }, 400);
        }
    });
</script>



