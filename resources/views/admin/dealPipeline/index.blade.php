@php
    $canDealAdd = isset($perms) && $perms->deal_add != 0;
    $canDealView = isset($perms) && $perms->deal_view != 0;
    $canDealEdit = isset($perms) && $perms->deal_edit != 0;
    $canDealDelete = isset($perms) && $perms->deal_delete != 0;
    $canDealUpdateStage = isset($perms) && $perms->deal_update_stage != 0;
    $canDealUpdateRecruitStatus = isset($perms) && $perms->deal_update_recruit_status != 0;
    $canDeleteFiles = isset($perms) && $perms->deal_delete_files != 0;
@endphp

@extends('layout.admin.admin_layout')

@section('title','Deal Pipeline List')

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
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

    <style>
        .pagestyle {
            /* height: calc(2.25rem + 2px); */
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .pagestyle:focus {
            color: #6e6b7b;
            background-color: #fff;
            border-color: #7367f0;
            outline: 0;
            box-shadow: 0 3px 10px 0 rgba(34, 41, 47, 0.1);
        }

        .fixedDealkanbanFilter {
            position: relative;
            z-index: 1;
            width: 100% !important;
        }

        /* No overflow here on purpose — .card-body-kanban-row below is the
        single scroll owner for the whole board (both axes). Giving this
        row its own overflow-x too would create a second, nested scroll
        container fighting the outer one. */
        .dealKanban {
            white-space: nowrap;
            padding-top: 1rem;
            padding-left: 0px;
        }


        .dealKanban .col-kanban {
            display: inline-block;
            vertical-align: top;
            float: none;
            width: 340px;
            margin-right: 8px;
        }

        /* No max-height/overflow-y here on purpose: a per-column viewport-
        relative scrollbox fights the board's own scroll (nested-scroll
        conflict) and a calc(100vh - Npx) offset goes stale the moment
        content above the board changes. Columns grow naturally and
        .card-body-kanban-row below scrolls the whole board. */
        .kanban-deal {
            padding-bottom: 10px; /* space for Load More */
            padding-right: 6px;
        }

        .kanban-card {
            cursor: grab;
            transition: transform 0.2s ease;
        }

        .kanban-card:hover {
            transform: scale(1.02);
        }

        /* Sticky stage headers */
        .statusesRow {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .dealpaginate {
            padding: 0px;
        }


        /* The kanban board's one and only scroll container — height is set
        dynamically in JS (sizeKanbanBoard()) to the actual remaining
        viewport space below wherever this box starts, so it stays correct
        regardless of what's above it (status bar, filters, etc.) instead
        of a hardcoded/stale calc(). Scrolls both axes: vertically for
        columns taller than the box, horizontally to reach every column. */
        .card-body-kanban-row {
            position: relative;
            top: 0px;
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

        .load-more-kanban {
            position: sticky;
            bottom: 10px;
            background: #fff;
        }
      

        .card-status {
            max-height: 20px !important;
            /* height: 3px !important; */
            /* z-index:1; */

        }

        .table-responsive {
            overflow: visible !important;
        }

        /* Notes tab table: the sticky header needs an opaque background (so
        scrolled rows don't show through underneath it), but it must use the
        theme's own surface color instead of a hardcoded white — otherwise it
        stays light in Dark Mode while the theme's header text color switches
        to light, making the header unreadable. Text/border colors are left
        alone so they cascade the same way they do for every other table.
        Selector is deliberately as specific as the core theme's own
        ".table > :not(caption) > * > *" background rule so it actually wins. */
        .notes-table-wrapper .notes-table-head th {
            background-color: var(--bs-modal-bg, #fff);
        }


        /* Responsive adjustments */
        @media (max-width: 1200px) { .dealKanban .col-kanban { width: 300px; } }
        @media (max-width: 992px)  { .dealKanban .col-kanban { width: 260px; } }
        @media (max-width: 768px)  { .dealKanban .col-kanban { width: 220px; } }
        @media (max-width: 576px)  {
            .dealKanban .col-kanban { width: 180px; }
            .kanban-card h6 { font-size: 0.85rem; }
            .kanban-card p { font-size: 0.75rem; }
        }

        @media (max-width: 1200px)  {
            .fixedDealkanbanFilter {
                /* position: fixed; */
                z-index: 1;
                width: 96%;
            }   
        }

        .filter-indicator {
            position: absolute;
            top: 1px;
            right: 2px;
            width: 8px;
            height: 8px;
            background: #ffc107;
            border-radius: 50%;
        }

        .kanban-skeleton {
            background: linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 37%,#f0f0f0 63%);
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
        DEAL STAGE / RECRUIT STATUS BAR
        Compact toolbar layout (same pattern as the Todo module): one row
        per group, label inline to the left of its pills.
        ============================== */

        .deal-filterbar {
            padding: 8px 14px;
        }

        .deal-filterbar-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            padding: 5px 0;
        }

        .deal-filterbar-row + .deal-filterbar-row {
            border-top: 1px solid var(--bs-border-color, rgba(0, 0, 0, .06));
        }

        .deal-filterbar-label {
            flex: 0 0 auto;
            min-width: 96px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: var(--bs-secondary-color, #6c757d);
        }

        .deal-status-grid {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
            flex: 1 1 auto;
            min-width: 0;
        }

        .deal-status-item {
            flex: 0 0 auto;
        }

        .deal-status-btn {
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

        .deal-status-btn strong {
            font-size: 13px;
            line-height: 1;
        }

        .deal-status-btn:hover {
            background: rgba(115, 103, 240, 0.05);
            text-decoration: none;
        }

        .deal-status-btn.active {
            background: var(--tsc-color, #7367f0);
        }

        .deal-status-btn.active span,
        .deal-status-btn.active strong {
            color: #fff !important;
        }

        @media (max-width: 575px) {
            .deal-filterbar-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .deal-filterbar-label {
                min-width: 0;
            }
        }
    </style>


<style id="dynamicStyle">
    /* Default style (optional) */
</style>
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        @php
            if(empty($saveadminfilter)){
                $listDisplay = '';
                $kanbanDisplay = 'none';
            }else{
                if($saveadminfilter->switch_to == 0){
                    $listDisplay = 'none';
                    $kanbanDisplay = '';
                }else{
                    $listDisplay = '';
                    $kanbanDisplay = 'none';
                }
            }

            // Deal Stage / Recruit Status are single-select (radio-button
            // semantics): compute which single pill actually reflects the
            // current saved filter so "All" isn't shown active by default
            // regardless of a real applied filter.
            $activeDealStageId = '';
            if (!empty($saveadminfilter) && $saveadminfilter->deal_stage_id) {
                $activeDealStageId = trim(explode(',', $saveadminfilter->deal_stage_id)[0] ?? '');
            }

            $activeRecruitStatusId = '';
            if (!empty($saveadminfilter) && $saveadminfilter->recruite_status_id) {
                $activeRecruitStatusId = trim(explode(',', $saveadminfilter->recruite_status_id)[0] ?? '');
            }

            $dealStatusColors = ['#00CFE8', '#7367F0', '#FF9F43', '#28C76F', '#EA5455', '#6C757D', '#4B4B4B'];
        @endphp

        <!-- Deal Stage / Recruit Status Summary Bar Start -->
        <div class="card mb-3" id="dealStatusBar">
            <div class="card-body deal-filterbar">

                <div class="deal-filterbar-row">
                    <span class="deal-filterbar-label">Deal Stage</span>
                    <div class="deal-status-grid">
                        <div class="deal-status-item">
                            <a href="javascript:;" class="deal-status-btn dealStatusCard{{ $activeDealStageId === '' ? ' active' : '' }}"
                               style="border-color:#6C757D;color:#6C757D;--tsc-color:#6C757D;"
                               data-group="stage" data-field="deal_stage_id" data-value="">
                                <span>All</span>
                                <strong>{{ number_format($dealStatusSummary['total'] ?? 0) }}</strong>
                            </a>
                        </div>

                        @foreach ($dealStages as $index => $dealStage)
                            @php $dealStageColor = $dealStatusColors[$index % count($dealStatusColors)]; @endphp
                            <div class="deal-status-item">
                                <a href="javascript:;" class="deal-status-btn dealStatusCard{{ (string) $activeDealStageId === (string) $dealStage->id ? ' active' : '' }}"
                                   style="border-color:{{ $dealStageColor }};color:{{ $dealStageColor }};--tsc-color:{{ $dealStageColor }};"
                                   data-group="stage" data-field="deal_stage_id" data-value="{{ $dealStage->id }}">
                                    <span>{{ $dealStage->name }}</span>
                                    <strong>{{ number_format($dealStatusSummary['stages'][$dealStage->id] ?? 0) }}</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="deal-filterbar-row">
                    <span class="deal-filterbar-label">Recruit Status</span>
                    <div class="deal-status-grid">
                        <div class="deal-status-item">
                            <a href="javascript:;" class="deal-status-btn dealStatusCard{{ $activeRecruitStatusId === '' ? ' active' : '' }}"
                               style="border-color:#6C757D;color:#6C757D;--tsc-color:#6C757D;"
                               data-group="recruit" data-field="recruite_status_id" data-value="">
                                <span>All</span>
                                <strong>{{ number_format($dealStatusSummary['total'] ?? 0) }}</strong>
                            </a>
                        </div>

                        @foreach ($recruitStatuses as $index => $recruitStatus)
                            @php $recruitStatusColor = $dealStatusColors[$index % count($dealStatusColors)]; @endphp
                            <div class="deal-status-item">
                                <a href="javascript:;" class="deal-status-btn dealStatusCard{{ (string) $activeRecruitStatusId === (string) $recruitStatus->id ? ' active' : '' }}"
                                   style="border-color:{{ $recruitStatusColor }};color:{{ $recruitStatusColor }};--tsc-color:{{ $recruitStatusColor }};"
                                   data-group="recruit" data-field="recruite_status_id" data-value="{{ $recruitStatus->id }}">
                                    <span>{{ $recruitStatus->name }}</span>
                                    <strong>{{ number_format($dealStatusSummary['recruitStatuses'][$recruitStatus->id] ?? 0) }}</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
        <!-- Deal Stage / Recruit Status Summary Bar End -->

        <!--Deal Pipeline List Start -->
        <div class="card disDealPipelineList" style="display: {{$listDisplay}}">

            <div class="card-header py-3 px-4">

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
                <div class="px-3 float-start">
                    <button class="btn btn-xs btn-primary changetokanban" data-bs-toggle="tooltip" data-bs-placement="top" title="Switch to Kanban"><i class="ti ti-layout-kanban me-0 me-sm-1 ti-xs"></i></button>
                    <button class="btn btn-xs btn-primary deal-filterpanel" data-bs-toggle="modal" data-bs-target="#dealFilterPanel">
                        <i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter
                        <span class="filter-indicator d-none"></span>
                    </button>

                    <div class="btn-group mx-1">
                        <button class="btn btn-primary btn-xs dropdown-toggle" type="button" id="dealPipelineOptionBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dealPipelineOptionBtn">
                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" value="1" class="switch-input deal-status-bar-switch" checked />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Status Bar</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="float-end">
                    <div class="btn-group mx-2"> 
                       
                    </div>
                  
                </div>
                @if($isAdmin || $canDealAdd)
                <div class="float-end">
                    <button class="add-new btn btn-xs btn-primary mx-1" 
                            data-bs-toggle="offcanvas" 
                            data-bs-target="#offcanvasAddDealPipeline">
                        <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                        <span class="d-none d-sm-inline-block">Add New Deal</span>
                    </button>
                </div>
                @endif
                <div class="float-end">
                    <input type="text" id="search_text" class="form-control search_text form-control-sm" placeholder="Search...">
                </div>
               
            </div>
            

            <div class="card-datatable table-responsive dealpaginate">
                @include('admin.dealPipeline.load')
            </div>
        </div>
        <!--Deal Pipeline List End -->

        <!-- Kanban Deal Pipeline Start -->
        <div class="card disDealPipelineKanban" style="display: {{$kanbanDisplay}}">

            <!-- <div class="card-header fixedDealkanbanFilter py-3 px-4"> -->
            <div class="card-header fixedDealkanbanFilter py-3 px-4" id="layout-navbar">
            
                <div class="float-start">
                    <button class="btn btn-xs btn-primary changetolist" data-bs-toggle="tooltip" data-bs-placement="top" title="Switch to List">
                        <i class="ti ti-list me-0 me-sm-1 ti-xs"></i>
                    </button>

                    <button class="btn btn-xs btn-primary deal-filterpanel" data-bs-toggle="modal" data-bs-target="#dealFilterPanel">
                        <i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter
                        <span class="filter-indicator d-none"></span>
                    </button>

                    <div class="btn-group mx-1">
                        <button class="btn btn-primary btn-xs dropdown-toggle" type="button" id="dealPipelineKanbanOptionBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dealPipelineKanbanOptionBtn">
                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" value="1" class="switch-input deal-status-bar-switch" checked />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Status Bar</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @if($isAdmin || $canDealAdd)
                <div class="float-end">
                    <button class="add-new btn btn-xs btn-primary mx-1" 
                            data-bs-toggle="offcanvas" 
                            data-bs-target="#offcanvasAddDealPipeline">
                        <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                        <span class="d-none d-sm-inline-block">Add New Deal</span>
                    </button>
                </div>
                @endif

                <div class="float-end">
                    <input type="text" id="kanban_search_text" class="form-control form-control-sm" placeholder="Search...">
                </div>
            </div>

            <div class="card-body card-body-kanban-row">
                <div class="row kanban-row">
                    @include('admin.dealPipeline.kanban_load')
                </div>
            </div>
        </div>
        <!-- Kanban Deal Pipeline End -->

        <!--- Add Deal Pipeline Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddDealPipeline" aria-labelledby="offcanvasAddDealPipelineLabel"  data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddDealPipelineLabel" class="offcanvas-title">Add New Deal Pipeline</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="pt-0 addDealPipelineForm" id="addDealPipelineForm" action="{{ route('admin.dealPipeline.store') }}" method="POST">
                    @csrf
                    <div class="row">
                            @foreach ($businesses as $business)
                                @if($business->name == 'Job seeker')
                                    <input type="hidden" id="JobSeekerOptionValueId" value="{{$business->id}}">
                                @endif
                            @endforeach
                        <!-- Business -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_business_id" class="form-label">Business <span class="text-danger">*</span></label>
                                <select name="add_business_id" id="add_business_id" class="form-select select22" data-allow-clear="true" data-placeholder="Select Business" >
                                    <option value="">Select Business</option>    
                                    @foreach ($businesses as $business)
                                        <option value="{{ $business->id }}">{{ $business->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Job Seeker Fields -->
                    <div id="jobSeekerFields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_associate_id" class="form-label">Associate <span class="text-danger">*</span></label>
                                    <select name="add_associate_id" class="form-select select22" id="add_associate_id" data-allow-clear="true"  data-placeholder="Select Associate" >
                                    <option value="">Select Associate</option>    
                                    <option value="73">Direct Candidate</option>
                                        @foreach ($associates as $associate)
                                            <option value="{{ $associate->id }}">{{ $associate->pty_full_name.' ('.$associate->pty_ag_name.')' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_careoff_id">Careoff <span class="text-danger">*</span></label>
                                    <select name="add_careoff_id" id="add_careoff_id" class="form-select select22" data-allow-clear="true" data-placeholder="Select Careoff">
                                        <option value="">Select Careoff</option>
                                        @foreach ($activeCareoffs as $careoff)
                                            <option value="{{ $careoff->id }}" 
                                                @if(Auth::guard('admin')->user()->id == $careoff->id) selected @endif>
                                                {{ $careoff->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>

                            
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Job Title</label>
                                    <select type="text" name="add_job_title" id="add_job_title" class="form-select select22" data-allow-clear="true" data-placeholder="Select Job Title">
                                        <option value="">Select Job Title</option>
                                        @foreach ($jobTitles as $jobtitle)
                                            <option value="{{ $jobtitle->id }}">{{ $jobtitle->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                           
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Candidate</label>
                                    <input type="text" name="add_candidate" id="add_candidate" class="form-control" placeholder="Enter Candidate Name" >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Passport No.</label>
                                    <input type="text" name="add_passport_no" id="add_passport_no" class="form-control" placeholder="Enter Passport Number">
                                    <span class="text-danger small" id="passport-error"></span>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="add_country" id="add_country" class="form-control" placeholder="Enter Country" >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="add_city" id="add_city" class="form-control" placeholder="Enter City" >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input type="number" name="add_amount" id="add_amount" class="form-control" placeholder="Enter Amount" >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mobile</label>

                                    <div class="input-group">
                                        <input type="text"
                                            name="add_mobile"
                                            id="add_mobile"
                                            class="form-control searchAllContact"
                                            placeholder="Enter Mobile">

                                        <button class="btn btn-outline-primary"
                                                type="button"
                                                id="searchMobileBtn">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>

                                    <div class="contact_msg" style="font-size:14px; margin-top:5px;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="add_email" id="add_email" class="form-control" placeholder="Enter Email" >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Source <span class="text-danger">*</span></label>
                                    <select name="add_source" id="add_source" class="form-select select22" data-allow-clear="true" data-placeholder="Select Source">
                                            <option value="">Select Source</option>
                                            @foreach ($globalSourceType as $source)
                                                <option value="{{ $source->id }}">{{ $source->name }}</option>
                                            @endforeach
                                    </select>                                
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="add_notes" id="add_notes" class="form-control" placeholder="Enter Notes..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Else Remaining Option Common Fields -->
                    <div id="commonFields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_common_careoff">Careoff <span class="text-danger">*</span></label>
                                    <select name="add_common_careoff" id="add_common_careoff" class="form-select select22" data-allow-clear="true" data-placeholder="Select Careoff">
                                        <option value="">Select Careoff</option>
                                        @foreach ($careoffs as $careoff)
                                            <option value="{{ $careoff->id }}" 
                                                @if(Auth::guard('admin')->user()->id == $careoff->id) selected @endif>
                                                {{ $careoff->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_common_name">Name</label>
                                    <input type="text" name="add_common_name" id="add_common_name" class="form-control" placeholder="Enter Name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_common_job_title">Job Title</label>
                                    <input type="text" name="add_common_job_title" id="add_common_job_title" class="form-control" placeholder="Enter Job Title">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_common_company_name">Company Name</label>
                                    <input type="text" name="add_common_company_name" id="add_common_company_name" class="form-control" placeholder="Enter Company Name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_common_country">Country</label>
                                    <input type="text" name="add_common_country" id="add_common_country" class="form-control" placeholder="Enter Country">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_common_city">City</label>
                                    <input type="text" name="add_common_city" id="add_common_city" class="form-control" placeholder="Enter City">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_common_mobile">Mobile</label>
                                    <input type="text" name="add_common_mobile" id="add_common_mobile" class="form-control" placeholder="Enter Mobile Number">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_ommon_mobile_whatsapp">Mobile (WhatsApp)</label>
                                    <input type="text" name="add_ommon_mobile_whatsapp" id="add_ommon_mobile_whatsapp" class="form-control" placeholder="Enter WhatsApp Number">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_common_email">Email</label>
                                    <input type="email" name="add_common_email" id="add_common_email" class="form-control" placeholder="Enter Email">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add_common_source">Source <span class="text-danger">*</span></label>
                                    <select name="add_common_source" id="add_common_source" class="form-select select22" data-allow-clear="true" data-placeholder="Select Source">
                                        <option value="">Select Source</option>
                                        @foreach ($globalSourceType as $source)
                                            <option value="{{ $source->id }}">{{ $source->name }}</option>
                                        @endforeach
                                    </select>                                
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="add_common_notes">Notes</label>
                                    <textarea name="add_common_notes" id="add_common_notes" class="form-control" placeholder="Enter Notes..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>


                    <button type="submit" class="btn btn-primary me-sm-3 me-1 disableAddDealBtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Add Deal Pipeline End --->

        <!--- Edit Deal Pipeline Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasEditDealPipeline" aria-labelledby="offcanvasEditDealPipelineLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasEditDealPipelineLabel" class="offcanvas-title">Edit Deal Pipeline</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="pt-0 editDealPipelineForm" id="editDealPipelineForm" action="{{ route('admin.dealPipeline.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="edit_deal_id" id="edit_deal_id">

                    <div class="row">
                        <!-- Business -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_business_id" class="form-label">Business <span class="text-danger">*</span></label>
                                <select name="edit_business_id" id="edit_business_id" class="form-select select222" data-allow-clear="true" data-placeholder="Select Business">
                                    <option value="">Select Business</option>    
                                    @foreach ($businesses as $business)
                                        <option value="{{ $business->id }}">{{ $business->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Job Seeker Fields -->
                    <div id="editJobSeekerFields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_associate_id" class="form-label">Associate <span class="text-danger">*</span></label>
                                    <select name="edit_associate_id" class="form-select select222" id="edit_associate_id" data-allow-clear="true" data-placeholder="Select Associate">
                                        <option value="">Select Associate</option>    
                                        <option value="73">Direct Candidate</option>
                                        @foreach ($associates as $associate)
                                            <option value="{{ $associate->id }}">{{ $associate->pty_full_name.' ('.$associate->pty_ag_name.')' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_careoff_id">Careoff <span class="text-danger">*</span></label>
                                    <select name="edit_careoff_id" id="edit_careoff_id" class="form-select select222" data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($activeCareoffs as $careoff)
                                            <option value="{{ $careoff->id }}" @if(Auth::guard('admin')->user()->id == $careoff->id) selected @endif>{{ $careoff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Job Seeker Input Fields -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Job Title</label>
                                    <select type="text" name="edit_job_title" id="edit_job_title" class="form-select select222" data-allow-clear="true" data-placeholder="Select Job Title">
                                        <option value="">Select Job Title</option>
                                        @foreach ($jobTitles as $jobtitle)
                                            <option value="{{ $jobtitle->id }}">{{ $jobtitle->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Candidate</label>
                                    <input type="text" name="edit_candidate" id="edit_candidate" class="form-control" placeholder="Enter Candidate Name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Passport No.</label>
                                    <input type="text" name="edit_passport_no" id="edit_passport_no" class="form-control" placeholder="Enter Passport Number">
                                    <span id="edit-passport-error" class="text-danger small"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="edit_country" id="edit_country" class="form-control" placeholder="Enter Country">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="edit_city" id="edit_city" class="form-control" placeholder="Enter City">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input type="number" name="edit_amount" id="edit_amount" class="form-control" placeholder="Enter Amount">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mobile</label>
                                    <input type="text" name="edit_mobile" id="edit_mobile" class="form-control searchAllContact" placeholder="Enter Mobile Number">
                                    <div class="contact_msg" style="font-size:14px; margin-top:5px;"></div>
                                </div>
                            </div> --}}

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mobile</label>

                                    <div class="input-group">
                                        <input type="text"
                                            name="edit_mobile"
                                            id="edit_mobile"
                                            class="form-control searchAllContact"
                                            placeholder="Enter Mobile Number">

                                        <button class="btn btn-outline-primary searchAllContactBtn"
                                                type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>

                                    <div class="contact_msg" style="font-size:14px; margin-top:5px;"></div>
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mobile (WhatsApp)</label>
                                    <input type="text" name="edit_mobile_whatsapp" id="edit_mobile_whatsapp" class="form-control" placeholder="Enter WhatsApp Number">
                                </div>
                            </div> -->
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="edit_email" id="edit_email" class="form-control" placeholder="Enter Email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Source <span class="text-danger">*</span></label>
                                    <select name="edit_source" id="edit_source" class="form-select select222" data-placeholder="Select Source">
                                        <option value="">Select Source</option>
                                        @foreach ($globalSourceType as $source)
                                            <option value="{{ $source->id }}">{{ $source->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Closed Date</label>
                                    <input type="text" name="edit_closed_date" id="edit_closed_date" class="form-control flatpicker-edit-closed-date" placeholder="Select Closed Date...">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="edit_notes" id="edit_notes" class="form-control" placeholder="Enter Notes..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Else Remaining Option Common Fields (Edit) -->
                    <div id="editCommonFields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_common_careoff">Careoff <span class="text-danger">*</span></label>
                                    <select name="edit_common_careoff" id="edit_common_careoff" class="form-select select222" data-allow-clear="true">
                                        <option value="">Select Careoff</option>
                                        @foreach ($careoffs as $careoff)
                                            <option value="{{ $careoff->id }}" @if(Auth::guard('admin')->user()->id == $careoff->id) selected @endif>{{ $careoff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_common_name">Name</label>
                                    <input type="text" name="edit_common_name" id="edit_common_name" class="form-control" placeholder="Enter Name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_common_job_title">Job Title</label>
                                    <input type="text" name="edit_common_job_title" id="edit_common_job_title" class="form-control" placeholder="Enter Job Title">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_common_company_name">Company Name</label>
                                    <input type="text" name="edit_common_company_name" id="edit_common_company_name" class="form-control" placeholder="Enter Company Name">
                                </div>
                            </div>
                        </div>

                        <!-- New Fields Added -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_common_country">Country</label>
                                    <input type="text" name="edit_common_country" id="edit_common_country" class="form-control" placeholder="Enter Country">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_common_city">City</label>
                                    <input type="text" name="edit_common_city" id="edit_common_city" class="form-control" placeholder="Enter City">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_common_mobile">Mobile</label>
                                    <input type="text" name="edit_common_mobile" id="edit_common_mobile" class="form-control" placeholder="Enter Mobile Number">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_common_mobile_whatsapp">Mobile (WhatsApp)</label>
                                    <input type="text" name="edit_common_mobile_whatsapp" id="edit_common_mobile_whatsapp" class="form-control" placeholder="Enter WhatsApp Number">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_common_email">Email</label>
                                    <input type="email" name="edit_common_email" id="edit_common_email" class="form-control" placeholder="Enter Email">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_common_source">Source <span class="text-danger">*</span></label>
                                    <select name="edit_common_source" id="edit_common_source" class="form-select select222" data-allow-clear="true" data-placeholder="Select Source">
                                        <option value="">Select Source</option>
                                        @foreach ($globalSourceType as $source)
                                            <option value="{{ $source->id }}">{{ $source->name }}</option>
                                        @endforeach
                                    </select>                                
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="edit_common_notes">Notes</label>
                                    <textarea name="edit_common_notes" id="edit_common_notes" class="form-control" placeholder="Enter Notes..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary me-sm-3 me-1 disableEditDealBtn">Update</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Edit Deal Pipeline End --->

        <!--- View Deal Modal Start --->
        <div class="modal fade" id="viewDealModal" tabindex="-1" aria-labelledby="viewDealModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewDealModalLabel">View Deal Notes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="view_deal_id">

                        <div class="card mb-3">
                            <div class="card-header">
                                <ul class="nav nav-tabs nav-fill" role="tablist">
                                    <li class="nav-item" role="presentation">
                                    <button type="button" class="nav-link active notes-tab-btn" role="tab" data-bs-toggle="tab" data-bs-target="#notes" aria-controls="notes" aria-selected="true">
                                    <i class="tf-icons ti ti-notes ti-xs me-1"></i> Notes
                                </button>
                                    </li>
                                    <li class="nav-item" role="presentation">

                                    <button class="nav-link detail-tab-btn" id="tab-details" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="false">
                                        <i class="tf-icons ti ti-home ti-xs me-1"></i>Details
                                    </button>
                                       
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link file-tab-btn" id="tab-file" data-bs-toggle="tab" data-bs-target="#file" type="button" role="tab" aria-controls="file" aria-selected="false">
                                            <i class=" tf-icons ti ti-file ti-xs me-1"></i>File
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link activity-tab-btn" id="tab-activity" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-controls="activity" aria-selected="false">
                                            <i class="tf-icons ti ti-timeline ti-xs me-1"></i>Activity
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <input type="hidden" name="deal_id" id="view_deal_notes_deal_id" value="">

                                    <!-- Notes Tab -->
                                    <div class="tab-pane fade show active" id="notes" role="tabpanel" aria-labelledby="tab-notes">
                                        <div class="row notes-div-2">
                                            <!-- Add Note Form -->
                                            <div class="col-md-4">
                                                <form action="{{ route('admin.dealnotes.store') }}" method="POST" id="notesValidation">
                                                    @csrf
                                                    <div class="row">
                                                        <input type="hidden" name="deal_id" id="notes_deal_id" value="">

                                                        <div class="col-md-12">
                                                            <label for="add-notes-tab" class="form-label">Notes <span class="text-danger">*</span></label>
                                                            <textarea name="notes" id="add-notes-tab" cols="30" rows="5" class="form-control"></textarea>
                                                        </div>

                                                        <div class="col-md-12 mt-3">
                                                            <div class="mb-3">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="conversation_type" value="Normal" id="add-normal-conversation" checked/>
                                                                    <label class="form-check-label" for="add-normal-conversation">Normal</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="conversation_type" value="Call" id="add-call-conversation"/>
                                                                    <label class="form-check-label" for="add-call-conversation">Call</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="conversation_type" value="Whatsapp" id="add-whatsapp-conversation"/>
                                                                    <label class="form-check-label" for="add-whatsapp-conversation">Whatsapp</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Notes Table -->
                                            <div class="col-md-8">
                                                <div class="p-3" id="notes-content">
                                                    <p>Loading Notes...</p>
                                                </div>
                                                <div class="notes-table-wrapper"
                                                    style="max-height: 350px; overflow-y: auto; border: 1px solid var(--bs-border-color); border-radius: 5px;">
                                                    <table class="table table-bordered mb-0">
                                                        <thead class="notes-table-head" style="position: sticky; top: 0; z-index: 5;">
                                                            <tr>
                                                                <th>Notes</th>
                                                                <th>Conversation</th>
                                                                <th>Created By</th>
                                                                <th>Created At</th>
                                                                @if(auth()->user()->user_type == 1)
                                                                    <th>Action</th>
                                                                @endif
                                                            </tr>
                                                        </thead>
                                                        <tbody id="notesTableBody">
                                                            <!-- Notes will load dynamically here -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Details Tab -->
                                    <div class="tab-pane fade" id="details" role="tabpanel" aria-labelledby="tab-details">
                                        <div class="p-3" id="details-content">
                                            <p>Loading details...</p>
                                        </div>
                                    </div>

                                    <!-- File Tab -->
                                    <div class="tab-pane fade" id="file" role="tabpanel" aria-labelledby="tab-file">
                                        <div class="row file-div-2">

                                            <!-- Upload File Form -->
                                            <div class="col-md-4">
                                                <form id="fileUploadForm" action="{{ route('admin.dealfiles.store') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="deal_id" id="file_deal_id" value="">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label for="file_name" class="form-label">File Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="file_name" id="file_name" class="form-control" placeholder="Enter file name" required>
                                                        </div>
                                                        <div class="col-md-12 mt-3">
                                                            <label for="file_upload" class="form-label">Upload File <span class="text-danger">*</span></label>
                                                            <input type="file" name="file_upload" id="file_upload" class="form-control" accept="*/*" required>
                                                            <small class="text-muted">Max size: 2 MB</small>
                                                        </div>
                                                        <div class="col-md-12 mt-3">
                                                            <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Files Table -->
                                            <div class="col-md-8">
                                                <div class="table table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>File preview</th>
                                                                <th>File Name</th>
                                                                <th>Uploaded By</th>
                                                                <th>Uploaded At</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="fileTableBody">
                                                            <!-- Files will be dynamically loaded via AJAX when File tab is clicked -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Activity Tab -->
                                    <div class="tab-pane fade" id="activity" role="tabpanel" aria-labelledby="tab-activity">
                                        <div class="p-3">
                                            <div id="dealActivityLoading" class="text-center py-5" style="display:none;">
                                                <div class="spinner-border text-primary" role="status"></div>
                                                <p class="mt-2 mb-0">Loading activity...</p>
                                            </div>
                                            <div id="dealActivityEmpty" class="text-center py-5" style="display:none;">
                                                <i class="ti ti-timeline fs-1 text-muted"></i>
                                                <h6 class="mt-3">No activity found</h6>
                                            </div>
                                            <div id="dealActivityError" class="alert alert-danger mb-0" style="display:none;">
                                                Unable to load activity.
                                            </div>
                                            <ul class="timeline mt-3 mb-0" id="dealActivityList"></ul>
                                            <div class="text-center mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="dealActivityLoadMore" style="display:none;">Load more</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!--- View Deal Modal End --->

        <!--- View Deal Modal Start --->
        <div class="modal fade" id="viewDealReminderModal" tabindex="-1"
            aria-labelledby="viewDealReminderModalLabel"
            aria-hidden="true"
            data-bs-backdrop="static"
            data-bs-keyboard="false">

            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">Deal Reminder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">

                        <div class="row align-items-end">

                            <!-- Hidden Deal ID -->
                            <input type="hidden" id="reminder_deal_id">

                            <!-- Date Time -->
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Select Date & Time</label>
                                <input type="text"
                                    id="add-start-on-time"
                                    class="form-control form-control-sm jsSingledatepicker"
                                    placeholder="Select Date & Time">
                            </div>

                             <!-- Date Time -->
                             <div class="col-md-5 mb-3">
                                <label class="form-label">Enter Description</label>
                                <textarea class="form-control" rows="1"
                                        id="add-whatsapp-description">
                                </textarea>

                            </div>

                            <!-- Save Button -->
                            <div class="col-md-2 mb-3">
                                <button class="btn btn-success btn-sm px-3" id="saveReminderBtn">
                                    <i class="fi-check"></i> Set Remider
                                </button>
                            </div>

                        </div>

                        <hr>

                        <!-- Reminder List -->
                        <div id="reminderList">
                            <div class="text-center text-muted py-3">
                                No reminders found
                            </div>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <!--- View Deal Modal End --->

        <!-- Recruit Status Modal Start -->
        <div class="modal fade" id="recruitStatusModal" 
            tabindex="-1" 
            aria-labelledby="recruitStatusModalLabel" 
            aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="recruitStatusModalLabel">Update Recruit Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body text-center">
                        <input type="hidden" id="recruit_deal_id">

                        <div class="dropdown w-100">
                            <button class="btn btn-outline-primary dropdown-toggle w-100" 
                                    type="button" 
                                    id="recruitStatusDropdown" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false">
                                Select Status
                            </button>

                            <ul class="dropdown-menu w-100" aria-labelledby="recruitStatusDropdown">
                                @foreach($recruitStatuses as $status)
                                <li>
                                    <a class="dropdown-item selectRecruitStatus" 
                                    href="javascript:void(0);" 
                                    data-status="{{ $status->name }}" 
                                    data-recruite-status-id="{{ $status->id }}">
                                    {{ $status->name }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-sm btn-primary" id="updateRecruitStatusBtn">Update</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Recruit Status Modal End -->

        <!-- Delete Deal Pipeline Start -->
        <div class="modal fade" id="deleteDealPipeline" aria-hidden="true" aria-labelledby="deleteDealPipelineLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="deleteDealPipelineLabel">Delete Deal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.dealPipeline.delete') }}" id="deleteDealPipelineForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="deal_pipeline_id" id="delete_deal_pipeline_id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="mb-3">
                                      <p class="text-danger">Are you sure to delete this Deal?</p>
                                   </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-danger btn-sm deletebtn" id="disbtn">Delete</button>
                         </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Deal Pipeline End -->

        <!-- Deals Filter Panel Start -->
        <div class="modal fade" id="dealFilterPanel" aria-labelledby="dealFilterPanelLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="dealFilterPanelLabel">Deals Filter</h5>
                        <button type="button" class="btn-close closeDealFilter" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <!-- Business -->
                            <div class="col-md-4 mb-3">
                                <select id="by-business" class="selectpicker w-100 filterData" multiple data-actions-box="true"
                                    data-live-search="true" data-style="default-btn" title="Select Business">
                                    @foreach ($businesses as $business)
                                        <option value="{{ $business->id }}"
                                            @if(isset($saveadminfilter) && in_array($business->id, explode(',', $saveadminfilter->business_id ?? '')))
                                                selected
                                            @endif>
                                            {{ $business->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Deal Stage -->
                            <div class="col-md-4 mb-3">
                                <select id="by-deal-stage" class="selectpicker w-100 filterData"
                                    multiple
                                    data-actions-box="true"
                                    data-live-search="true"
                                    data-style="default-btn"
                                    title="Select Deal Stage">

                                    @foreach ($dealStages as $dealStage)
                                        <option value="{{ $dealStage->id }}"
                                            @if(isset($saveadminfilter) && in_array($dealStage->id, explode(',', $saveadminfilter->deal_stage_id ?? '')))
                                                selected
                                            @endif>
                                            {{ $dealStage->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <!-- Recruite Status -->
                            <div class="col-md-4 mb-3">
                                <select id="by-recruite-status" class="selectpicker w-100 filterData" multiple data-actions-box="true"
                                    data-live-search="true" data-style="default-btn" title="Select Recruit Status">
                                    @foreach ($recruitStatuses as $recruitStatus)
                                        <option value="{{ $recruitStatus->id }}"
                                            @if(isset($saveadminfilter) && in_array($recruitStatus->id, explode(',', $saveadminfilter->recruite_status_id ?? '')))
                                                selected
                                            @endif>
                                            {{ $recruitStatus->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Job Title -->
                            <div class="col-md-4 mb-3">
                                <select id="by-job-title" class="selectpicker w-100 filterData" multiple data-actions-box="true"
                                    data-live-search="true" data-style="default-btn" title="Select Job Title">
                                    @foreach ($jobTitles as $job)
                                        <option value="{{ $job->id }}"
                                            @if(isset($saveadminfilter) && in_array($job->id, explode(',', $saveadminfilter->job_title_id ?? '')))
                                                selected
                                            @endif>
                                            {{ $job->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Associate -->
                            <div class="col-md-4 mb-3">
                                <select id="by-associate" class="selectpicker w-100 filterData" multiple data-actions-box="true"
                                    data-live-search="true" data-style="default-btn" title="Select Associate">
                                    @foreach ($associates as $associate)
                                        <option value="{{ $associate->id }}"
                                            @if(isset($saveadminfilter) && in_array($associate->id, explode(',', $saveadminfilter->associate_id ?? '')))
                                                selected
                                            @endif>
                                            {{ $associate->pty_full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Care Of -->
                            <div class="col-md-4 mb-3">
                                <select id="by-careoff" class="selectpicker w-100 filterData" multiple data-actions-box="true"
                                    data-live-search="true" data-style="default-btn" title="Select Care Of">
                                    @foreach ($careoffs as $care)
                                        <option value="{{ $care->id }}"
                                            @if(isset($saveadminfilter) && in_array($care->id, explode(',', $saveadminfilter->care_of ?? '')))
                                                selected
                                            @endif>
                                            {{ $care->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Source -->
                            <div class="col-md-4 mb-3">
                                <select id="by-source" class="selectpicker w-100 filterData" multiple data-actions-box="true"
                                    data-live-search="true" data-style="default-btn" title="Select Source">

                                    @php
                                        $selectedSources = isset($saveadminfilter) ? explode(',', $saveadminfilter->source ?? '') : [];
                                    @endphp

                                    @foreach ($globalSourceType as $source)
                                        <option value="{{ $source->id }}" 
                                            @if(in_array($source->id, $selectedSources)) selected @endif>
                                            {{ $source->name }}
                                        </option>
                                    @endforeach


                                </select>
                            </div>

                            <!-- Created Date -->
                            <div class="col-md-4 mb-3">
                                <input type="text" name="created_date" id="created-date"
                                    class="form-control bsdatpicket"
                                    value="{{ $saveadminfilter->created_date ?? '' }}"
                                    placeholder="Created Date Range...">
                            </div>

                            <!-- Updated Date -->
                            <div class="col-md-4 mb-3">
                                <input type="text" name="updated_date" id="updated-date"
                                    class="form-control bsdatpicket"
                                    value="{{ $saveadminfilter->updated_date ?? '' }}"
                                    placeholder="Updated Date Range...">
                            </div>

                            <!-- Followup before -->
                            <div class="col-md-4 mb-3">
                                <select name="followup_before" id="by-followup-before"
                                    class="selectpicker w-100 filterData"
                                    data-actions-box="true"
                                    data-live-search="true"
                                    data-style="default-btn"
                                    title="Select followup before">
                                    <option value="">Select followup before</option>
                                    <option value="1" {{ ($saveadminfilter->followup_before ?? '') == 1 ? 'selected' : '' }}>Today</option>
                                    <option value="3" {{ ($saveadminfilter->followup_before ?? '') == 3 ? 'selected' : '' }}>3 Days</option>
                                    <option value="7" {{ ($saveadminfilter->followup_before ?? '') == 7 ? 'selected' : '' }}>7 Days</option>
                                    <option value="14" {{ ($saveadminfilter->followup_before ?? '') == 14 ? 'selected' : '' }}>14 Days</option>
                                    <option value="30" {{ ($saveadminfilter->followup_before ?? '') == 30 ? 'selected' : '' }}>1 Month</option>

                                </select>
                            </div>

                            <!-- Created By -->
                            <div class="col-md-4 mb-3">
                                <select id="by-created-by" class="selectpicker w-100 filterData" multiple data-actions-box="true"
                                    data-live-search="true" data-style="default-btn" title="Select Created By">
                                    @foreach ($admins as $admin)
                                        <option value="{{ $admin->id }}"
                                            @if(isset($saveadminfilter) && in_array($admin->id, explode(',', $saveadminfilter->created_by ?? '')))
                                                selected
                                            @endif>
                                            {{ $admin->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm closeDealFilter" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-warning btn-sm resetDealFilter">Reset Filter</button>
                        <button type="button" class="btn btn-success btn-sm saveDealFilter">Save Filter</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Deals Filter Panel End -->
    </div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>

    <script>
    $(document).ready(function () {

        // datepicker initialization for filter panel

        $('.jsSingledatepicker').flatpickr({
            enableTime: true,
            time_24hr: true,
            dateFormat: "Y-m-d H:i"
        });

        ////////////////////////////////////////////////////////////////////////////////////////////////////////
        //                                      Deal Pipeline Reminder Module
        ////////////////////////////////////////////////////////////////////////////////////////////////////////

        // check if url has open_deal_model=true and deal_model_id, then open modal and load reminders
        const urlParams = new URLSearchParams(window.location.search);
        const openModal = urlParams.get('open_deal_model');
        const dealId = urlParams.get('deal_model_id');

        if (openModal === 'true' && dealId) {

            setTimeout(function () {

                $('#reminder_deal_id').val(dealId);
                $('#viewDealReminderModal').modal('show');
                loadReminders(dealId);

            }, 500);

        }

        // open modal and load list
        $(document).on('click', '.openReminderModal', function () {

            let deal_id = $(this).data('id');

            $('#reminder_deal_id').val(deal_id);

            // reset field
            $('#add-start-on-time').val('');

            $('#viewDealReminderModal').modal('show');

            loadReminders(deal_id); // load list
        });

        // save reminder
        $('#saveReminderBtn').on('click', function () {

            let deal_id = $('#reminder_deal_id').val();
            let reminder_at = $('#add-start-on-time').val();
            let whatsapp_description = $('#add-whatsapp-description').val();


            // validation
            if (!reminder_at) {
                toastr['warning']('Please select date & time', 'Warning', { hideDuration: 3000 });
                return;
            }

            if (!whatsapp_description || whatsapp_description.trim() === '') {
                toastr['warning']('Please Enter Description', 'Warning', { hideDuration: 3000 });
                return;
            }

            let selectedDate = moment(reminder_at, 'YYYY-MM-DD HH:mm');
            let currentDate = moment();

            // remove seconds comparison
            if (selectedDate.isBefore(currentDate, 'minute')) {
                toastr['error']('Past time not allowed', 'Error', { hideDuration: 3000 });
                return;
            }


            $.ajax({
                url: "{{ route('admin.dealPipeline.reminder.store') }}",
                type: "POST",
                data: {
                    deal_id: deal_id,
                    reminder_at: reminder_at,
                    whatsapp_description: whatsapp_description,
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {

                    if (res.status === 'success') {

                        // reload list
                        loadReminders(deal_id);

                        // reset input
                        $('#add-start-on-time').val('');
                        $('#add-whatsapp-description').val('');

                    }
                },
                error: function () {
                    alert('Something went wrong');
                }
            });

        });

        $(document).on('click', '.delete-reminder', function () {

            let reminder_id = $(this).data('id');

            $.ajax({
                url: "{{ route('admin.dealPipeline.reminder.delete') }}",
                type: "POST",
                data: {
                    reminder_id: reminder_id,
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {

                    if (res.status === 'success') {
                        loadReminders($('#reminder_deal_id').val());
                    }

                },
                error: function () {
                    alert('Something went wrong');
                }
            });

        });

        // load reminder
        function loadReminders(deal_id) {

            $.ajax({
                url: "{{ route('admin.dealPipeline.reminder.list') }}",
                type: "GET",
                data: { deal_id: deal_id },
                success: function (res) {
                    $('#reminderList').html(res);
                }
            });

        }



        let timer;

        function searchAllContact(input) {

            let search = input.val().trim();
            let msgBox = input.closest('.mb-3').find('.contact_msg');

            clearTimeout(timer);

            timer = setTimeout(function () {

                if (search.length < 1) {
                    msgBox.html("");
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.dealPipeline.search-allcontact') }}",
                    type: "GET",
                    data: { search: search },
                    success: function (response) {

                        let color = response.status ? "green" : "red";
                        let text = response.display ?? response.message;

                        msgBox.html(`<span style="color:${color};">${text}</span>`);
                    },
                    error: function () {
                        msgBox.html('<span style="color:red;">Server error, try again.</span>');
                    }
                });

            }, 300);
        }

        // Search while typing
        $(document).on('keyup', '.searchAllContact', function () {
            searchAllContact($(this));
        });

        // Search on button click
        $(document).on('click', '.searchAllContactBtn', function () {
            searchAllContact($(this).closest('.input-group').find('.searchAllContact'));
        });

    });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const t = localStorage.getItem('templateCustomizer-vertical-menu-template--Style');
            const styleEl = document.getElementById('dynamicStyle');
            
            if (t === 'dark') {
                styleEl.innerHTML = `
                    body > div.layout-wrapper.layout-content-navbar > div.layout-container > div > div > div.container-fluid.flex-grow-1.container-p-y > div.card.disDealPipelineKanban > div.card-body.card-body-kanban-row > div > div {
                        background-color: #26293d;
                    }
                `;
            } else {
                // default or light style
                styleEl.innerHTML = `
                    body > div.layout-wrapper.layout-content-navbar > div.layout-container > div > div > div.container-fluid.flex-grow-1.container-p-y > div.card.disDealPipelineKanban > div.card-body.card-body-kanban-row > div > div {
                        background-color: #f8f7fa;
                    }
                `;
            }
        });
    </script>
    @if($isAdmin || $canDealUpdateStage)
    <script>
        function initKanbanSortable() {

            console.log("Initializing DEAL Kanban...");

            $(".kanban-deal").sortable({

                items: ".kanban-card",
                connectWith: ".kanban-deal",
                tolerance: "pointer",
                animation: 150,

                /* =========================
                DRAG START
                ========================= */
                start: function (event, ui) {
                    ui.item.addClass("dragging");
                },

                /* =========================
                COLUMN CHANGE (MOVE DEAL)
                ========================= */
                receive: function (event, ui) {

                    let dealId   = ui.item.data("id");
                    let newStage = $(this).attr("id");
                    let oldStage = ui.sender.attr("id");

                    let position = ui.item.index() + 1;

                    console.log("Deal moved:", dealId, "From:", oldStage, "To:", newStage);

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
                    AJAX: REORDER NEW COLUMN
                    -------------------------- */
                    $.ajax({
                        url: "{{ route('admin.dealPipeline.kanbanReorder') }}",
                        method: "POST",
                        data: {
                            stage_id: newStage,
                            order: order,
                            "_token": "{{ csrf_token() }}"
                        },
                        error: function () {
                            toastr['error']("Reorder failed", "Error");
                        }
                    });

                    /* --------------------------
                    AJAX: UPDATE STAGE + POSITION
                    -------------------------- */
                    $.ajax({
                        url: "{{ route('admin.dealPipeline.kanbanUpdateStatus') }}",
                        method: "POST",
                        data: {
                            deal_id: dealId,
                            stage_id: newStage,
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

                        let column   = $(this);
                        let stageId  = column.attr("id");

                        let order = [];

                        column.find(".kanban-card").each(function (index) {
                            order.push({
                                id: $(this).data("id"),
                                position: index + 1
                            });
                        });

                        console.log("Reordered in stage:", stageId, order);

                        $.ajax({
                            url: "{{ route('admin.dealPipeline.kanbanReorder') }}",
                            method: "POST",
                            data: {
                                stage_id: stageId,
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

        /* INIT */
        $(document).ready(function () {
            initKanbanSortable();
        });
    </script>
    @endif

    <script>

        // Sizes the kanban board's own scroll container to the actual
        // remaining viewport space below wherever it currently starts, so
        // scrolling stays contained to the board (not the page) no matter
        // what's rendered above it (status bar, filters, etc.) — measured
        // live instead of a hardcoded calc() that would go stale the
        // moment content above the board changes. Defined here (not inside
        // the drag-reorder permission-gated block) so it runs for everyone.
        function sizeKanbanBoard() {
            var $board = $('.card-body-kanban-row');
            if (!$board.length || !$('.disDealPipelineKanban').is(':visible')) {
                return;
            }

            var top = $board[0].getBoundingClientRect().top;
            var bottomGap = 16; // small breathing room at the bottom of the viewport
            var available = window.innerHeight - top - bottomGap;

            $board.css('height', Math.max(available, 200) + 'px');
        }

        $(window).on('resize', sizeKanbanBoard);
        $(document).ready(sizeKanbanBoard);

        function updateDealFilterIndicator() {

            let isFiltered =
                // Business
                ($('#by-business').val() && $('#by-business').val().length > 0) ||

                // Deal Stage
                ($('#by-deal-stage').val() && $('#by-deal-stage').val().length > 0) ||

                // Recruit Status
                ($('#by-recruite-status').val() && $('#by-recruite-status').val().length > 0) ||

                // Job Title
                ($('#by-job-title').val() && $('#by-job-title').val().length > 0) ||

                // Associate
                ($('#by-associate').val() && $('#by-associate').val().length > 0) ||

                // Care Of
                ($('#by-careoff').val() && $('#by-careoff').val().length > 0) ||

                // Source
                ($('#by-source').val() && $('#by-source').val().length > 0) ||

                // Created / Updated date ranges
                $('#created-date').val() ||
                $('#updated-date').val() ||

                // followup before
                ($('#by-followup-before').val() && $('#by-followup-before').val().length > 0) ||

                // Created By
                ($('#by-created-by').val() && $('#by-created-by').val().length > 0);

            if (isFiltered) {
                $('.deal-filterpanel .filter-indicator')
                    .removeClass('d-none')
                    .addClass('d-block');
            } else {
                $('.deal-filterpanel .filter-indicator')
                    .removeClass('d-block')
                    .addClass('d-none');
            }
        }

        $(document).on(
            'change keyup',
            `
            #by-business,
            #by-deal-stage,
            #by-recruite-status,
            #by-job-title,
            #by-associate,
            #by-careoff,
            #by-followup-before,
            #by-source,
            #created-date,
            #updated-date,
            #by-created-by
            `,
            function () {
                updateDealFilterIndicator();
            }
        );


        // ===============================
        // 7. AJAX Filter & Pagination
        // ===============================
        function getDealFilterData(){            
            return {
                _token: '{{ csrf_token() }}',
                page_list: $('#pagination_list').val(),
                search_text: $('#search_text').val(),
                kanban_search_text: $('#kanban_search_text').val(),
                business_id: $('#by-business').val(),
                deal_stage_id: $('#by-deal-stage').val(),
                recruite_status_id: $('#by-recruite-status').val(),
                job_title_id: $('#by-job-title').val(),
                associate_id: $('#by-associate').val(),
                care_of: $('#by-careoff').val(),
                followup_before: $('#by-followup-before').val(),
                source: $('#by-source').val(),
                created_date: $('#created-date').val(),
                updated_date: $('#updated-date').val(),
                created_by: $('#by-created-by').val()
            };
        }


        function openViewDealModal(dealId) {

            $('#view_deal_notes_deal_id').val(dealId);  // for global access
            $('#notes_deal_id').val(dealId); // for notes tab

            $('.notes-tab-btn').trigger('click');
        }

        @if($autoOpenDealId)
            // Global "open this deal" deep link (e.g. from the Dashboard).
            // Reuses the exact same modal-open logic above — no duplicate
            // modal/fetch logic.
            $(document).ready(function () {
                openViewDealModal({{ $autoOpenDealId }});
                $('#viewDealModal').modal('show');
            });
        @endif

        // ==============================================================
        // Handle Deal Notes Tab Section - Start
        // ==============================================================
        const notesTableBody = $('#notesTableBody');
        const notesContent = $('#notes-content');
        const notesForm = $('#notesValidation');
        const deleteForm = $('#deleteNoteForm');

        // 🟡 Load Notes Function
        function loadNotes(dealId) {
            $('#view_deal_notes_deal_id').val(dealId);  // for global access
            $('#notes_deal_id').val(dealId); // for notes tab
            $.ajax({
                url: "{{ route('admin.dealnotes.list') }}",
                type: "GET",
                data: { deal_id: dealId },
                beforeSend: function() {
                    notesTableBody.html('<tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>');
                },
                success: function(response) {

                    if (response.html) {                        
                        notesTableBody.html(response.html);
                    } else {
                        notesTableBody.html('');
                    }

                    if (response.deal) {                        
                        notesContent.html('<p><b>Notes: </b>' + response.deal.notes + '</p>');
                    } else {
                        notesContent.html('');
                    }
                },
                error: function() {
                    notesTableBody.html('<tr><td colspan="5" class="text-center text-danger">Failed to load notes</td></tr>');
                }
            });
        }

        $(document).ready(function(){

            updateDealFilterIndicator(); // Check on page load if any filter is active and update indicator accordingly

            $('.closeDealFilter').on('click', function () {

                if (document.activeElement) {
                    document.activeElement.blur();
                }

                $('#dealFilterPanel').modal('hide');

            });

            $('#dealFilterPanel').on('hidden.bs.modal', function () {

                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();

            });

            // ===============================
            // 1. Initialize Select2
            // ===============================
            function initSelect2(selector) {
                $(selector).each(function(){
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        dropdownParent: $this.parent()
                    });
                });
            }
            initSelect2('.select22, .select222, .select22f');


            
            // 🟢 Add Note
            $('#notesValidation').on('submit', function(e) {
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
                            $('#notesTableBody').prepend(`
                                <tr id="noteRow_${response.data.id}">
                                    <td>${response.data.notes}</td>
                                    <td>${response.data.conversation_type || '---'}</td>
                                    <td>${response.data.created_by_name || '---'}</td>
                                    <td>${response.data.created_at}</td>
                                    @if(auth()->user()->user_type == 1)
                                        <td>
                                            <a href="javascript:void(0);" 
                                            class="delete-note" 
                                            data-id="${response.data.id}" 
                                            data-url="${response.data.delete_url}">
                                                <i class="ti ti-trash ti-sm text-danger"></i>
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                            `);

                            form[0].reset();
                        } else {
                            toastr.error(response.message || 'Failed to add note', 'Error');
                        }
                    },
                    error: function(xhr) {
                        form.find('button[type="submit"]').prop('disabled', false).text('Submit');

                        // Handle Laravel validation errors
                        if (xhr.status === 422) { // 422 Unprocessable Entity
                            const errors = xhr.responseJSON.errors;
                            let errorMessages = [];

                            // Collect all messages into one array
                            $.each(errors, function(key, messages) {
                                errorMessages.push(messages[0]); // take the first message per field
                            });

                            // Show all messages in a single alert or toast
                            toastr.error(errorMessages.join("\n"), 'Error');
                        } else {
                            toastr.error('Something went wrong while saving the note', 'Error');
                            console.error(xhr.responseText);
                        }
                    }
                });
            });

            // 🔴 Delete Note using confirm()
            $(document).on('click', '#notesTableBody a.delete-note', function(e) {
                e.preventDefault();

                const deleteId = $(this).data('id');
                const deleteUrl = $(this).data('url'); // URL to delete note


                // Ask user for confirmation
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
                                $('#noteRow_' + deleteId).remove();
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

            $('.notes-tab-btn').on('click', function() {
                const dealId = $('#notes_deal_id').val();
                loadNotes(dealId);
            });

            // Table view "View Deal" links (.view-deal-btn) are re-rendered via
            // AJAX on every pagination/filter/reload (see reloadDealsList()),
            // so the handler must be delegated off a static ancestor rather
            // than bound directly - otherwise deal rows loaded after the
            // first page load would open the modal with no deal id set at
            // all, leaving every tab (Notes/Details/File) with stale or
            // empty data. Reuses the exact same open flow the Kanban view's
            // "eye" icon and the deep-link auto-open already use.
            $(document).on('click', '.view-deal-btn', function() {
                const dealId = $(this).data('id');
                openViewDealModal(dealId);
            });

          


            // ============================================================== 
            // Handle Deal Deatails Tab Section - Start
            // ==============================================================

            function formatDate(dateStr) {
                if (!dateStr) return '-';
                let date = new Date(dateStr);
                if (isNaN(date)) return dateStr; // fallback if invalid
                return date.toLocaleString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }

            $('.detail-tab-btn').on('click', function() {
                const dealId = $('#notes_deal_id').val();
                $('#details-content').html('<p>Loading details...</p>');
                $.ajax({
                    url: "{{ route('admin.dealPipeline.getDeal') }}",
                    type: 'GET',
                    data: { deal_id: dealId },
                    success: function(response) {
                        if (response.success) {
                                let d = response.data;
                                let html = `
                                    <div class="row g-3">
                                        <div class="col-md-6"><strong>Business:</strong> ${d.business?.name ?? '-'}</div>
                                        <div class="col-md-6"><strong>Associate:</strong> ${d.associate?.pty_full_name ?? '-'}</div>
                                        <div class="col-md-6"><strong>Careoff:</strong> ${d.care_of?.name ?? (d.care_of_name ?? '-')}</div>
                                        <div class="col-md-6"><strong>Job Title:</strong> ${d.job_title?.name ?? '-'}</div>
                                        <div class="col-md-6"><strong>Candidate:</strong> ${d.candidate ?? '-'}</div>
                                        <div class="col-md-6"><strong>Passport No.:</strong> ${d.passport_no ?? '-'}</div>
                                        <div class="col-md-6"><strong>Country:</strong> ${d.country ?? '-'}</div>
                                        <div class="col-md-6"><strong>City:</strong> ${d.city ?? '-'}</div>
                                        <div class="col-md-6"><strong>Amount:</strong> ${d.amount ?? '-'}</div>
                                        <div class="col-md-6"><strong>Mobile:</strong> ${d.contact ?? '-'}</div>
                                        <div class="col-md-6"><strong>Mobile (WhatsApp):</strong> ${d.contact_whatsapp ?? '-'}</div>
                                        <div class="col-md-6"><strong>Email:</strong> ${d.email ?? '-'}</div>
                                        <div class="col-md-6"><strong>Source:</strong>${d.source?.name ?? '-'}</div>
                                        <div class="col-md-6"><strong>Created At:</strong> ${formatDate(d.created_at)}</div>
                                        <div class="col-md-6"><strong>Created By:</strong> ${d.creator.name ?? '-'}</div>
                                        <div class="col-md-12"><strong>Notes:</strong> ${d.notes ?? '-'}</div>
                                    </div>
                                `;
                                $('#details-content').html(html).data('loaded', dealId);
                        } else {
                            $('#details-content').html('<p class="text-danger">No details found.</p>');
                        }
                    },
                    error: function() {
                        $('#details-content').html('<p class="text-danger">Error loading details.</p>');
                    }
                });
            });

            // ==============================================================
            // Handle Deal Activity Tab Section - Start
            // ==============================================================
            let dealActivityNextPage = 1;
            let dealActivityLoadedDealId = null;
            let dealActivityLoading = false;

            function loadDealActivity(dealId, page, append) {
                if (dealActivityLoading) return;
                dealActivityLoading = true;

                const $list = $('#dealActivityList');
                const $empty = $('#dealActivityEmpty');
                const $error = $('#dealActivityError');
                const $loading = $('#dealActivityLoading');
                const $loadMore = $('#dealActivityLoadMore');

                $error.hide();
                $empty.hide();
                $loading.show();

                if (!append) {
                    $list.empty();
                    $loadMore.hide();
                }

                $.ajax({
                    url: "{{ route('admin.dealPipeline.activity') }}",
                    type: 'GET',
                    data: { deal_id: dealId, page: page },
                    success: function(response) {
                        $loading.hide();

                        if (!response.success) {
                            $error.text(response.message || 'Unable to load activity.').show();
                            return;
                        }

                        if (append) {
                            $list.append(response.html);
                        } else {
                            $list.html(response.html);
                        }

                        if (response.is_empty) {
                            $empty.show();
                        }

                        dealActivityNextPage = response.next_page;

                        if (response.has_more) {
                            $loadMore.show();
                        } else {
                            $loadMore.hide();
                        }
                    },
                    error: function() {
                        $loading.hide();
                        $error.text('Unable to load activity.').show();
                    },
                    complete: function() {
                        dealActivityLoading = false;
                    }
                });
            }

            $('.activity-tab-btn').on('click', function() {
                const dealId = $('#notes_deal_id').val();

                // Re-fetch fresh from page 1 every time the tab is opened for
                // a (possibly different) deal, so the timeline never shows a
                // stale/previous deal's activity or duplicates on reopen.
                if (dealActivityLoadedDealId !== dealId) {
                    dealActivityLoadedDealId = dealId;
                }

                loadDealActivity(dealId, 1, false);
            });

            $('#dealActivityLoadMore').on('click', function() {
                loadDealActivity(dealActivityLoadedDealId, dealActivityNextPage, true);
            });
            // ==============================================================
            // Handle Deal Activity Tab Section - End
            // ==============================================================


            // ============================================================== 
            // Handle Deal File Tab Section - Start
            // ==============================================================

            // Load files function
            function loadFiles(dealId) {
                $.post("{{ route('admin.dealfiles.list') }}", {
                    deal_id: dealId,
                    _token: "{{ csrf_token() }}"
                }, function (files) {

                    $('#fileTableBody').empty();

                    if (!files.length) {
                        $('#fileTableBody').html(`
                            <tr>
                                <td colspan="5" class="text-center">No files found</td>
                            </tr>
                        `);
                        return;
                    }

                    files.forEach(file => {

                        let preview = '';
                        let ext = file.extension;

                        // 🖼️ Image
                        if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
                            preview = `<img src="${file.file_url}" width="60" height="60"
                                            style="object-fit:cover; border-radius:5px;">`;

                        // 📄 PDF
                        } else if (ext === 'pdf') {
                            preview = `<iframe src="${file.file_url}" 
                                                width="80" height="60"
                                                style="border:1px solid #ddd; pointer-events:none;">
                                    </iframe>`;

                        // 🎥 Video
                        } else if (['mp4','webm','ogg'].includes(ext)) {
                            preview = `<video width="80" height="60">
                                            <source src="${file.file_url}" type="video/${ext}">
                                    </video>`;

                        // 📁 Other
                        } else {
                            preview = `📁 File`;
                        }

                        $('#fileTableBody').append(`
                            <tr id="fileRow_${file.id}">
                                <td>
                                    <a href="${file.file_url}" target="_blank">
                                        ${preview}
                                    </a>
                                </td>

                                <td>${file.file_name}</td>

                                <td>${file.uploader ? file.uploader.name : '---'}</td>

                                <td>${file.created_at}</td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2 flex-nowrap">

                                        <!-- View -->
                                        <a href="${file.file_url}"
                                        target="_blank"
                                        class="text-primary"
                                        title="View">
                                            <i class="ti ti-eye fs-5"></i>
                                        </a>

                                        <!-- Download -->
                                        <a href="${file.download_url}"
                                        class="text-primary"
                                        title="Download">
                                            <i class="ti ti-download fs-5"></i>
                                        </a>

                                        @if($isAdmin || $canDeleteFiles)
                                        
                                            <a href="javascript:void(0);"
                                            class="text-danger delete-file"
                                            data-id="${file.id}"
                                            data-url="${file.delete_url}"
                                            title="Delete">
                                                <i class="ti ti-trash fs-5"></i>
                                            </a>
                                       
                                         @endif

                                    </div>
                                </td>
                            </tr>
                        `);
                    });
                });
            }

            // Upload file
            $('#fileUploadForm').on('submit', function(e){
                e.preventDefault();
                const formData = new FormData(this);
                const dealId = $('#file_deal_id').val();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#fileUploadForm button[type="submit"]').prop('disabled', true).text('Uploading...');
                    },
                    success: function(response){
                        $('#fileUploadForm button[type="submit"]').prop('disabled', false).text('Upload');

                        if(response.status === 'success') {
                            toastr.success(response.message);
                            $('#fileUploadForm')[0].reset();
                            loadFiles(dealId);
                        } else {
                            toastr.error(response.message || 'Failed to upload file');
                        }
                    },
                    error: function(xhr){
                        $('#fileUploadForm button[type="submit"]').prop('disabled', false).text('Upload');
                        if(xhr.status === 422){
                            let errors = xhr.responseJSON.errors;
                            let messages = [];
                            $.each(errors, function(key, value){
                                messages.push(value[0]);
                            });
                            toastr.error(messages.join("\n"), 'Error');
                        } else {
                            toastr.error('Something went wrong');
                        }
                    }
                });
            });

            // Delete file
            $(document).on('click', '.delete-file', function(){
                if(!confirm("Are you sure you want to delete this file?")) return;

                const id = $(this).data('id');
                const url = $(this).data('url'); // make sure this route is DELETE

                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: {_token: "{{ csrf_token() }}"},
                    success: function(res){
                        if(res.status === 'success') {
                            toastr.success(res.message);
                            $('#fileRow_' + id).remove();
                        } else {
                            toastr.error(res.message || 'Failed to delete file');
                        }
                    },
                    error: function(err){
                        toastr.error('Something went wrong');
                    }
                });
            });


            // Handle File tab click to load files dynamically
            $('.file-tab-btn').on('click', function() {
                const dealId = $('#view_deal_notes_deal_id').val();
                $('#file_deal_id').val(dealId);

                // Load existing files via AJAX for this deal
                loadFiles(dealId);
            });


            // ============================================================== 
            // Passport Number On Change Validation 
            // ==============================================================
            $(document).on('input', '#passport_no', function() {
                var passportNo = $(this).val().trim();
                var $error = $('#passport-error');
                var $submitBtn = $('.disableAddDealBtn');

                if(passportNo === '') {
                    $error.text('');
                    $submitBtn.prop('disabled', false);
                    return;
                }

                $.ajax({
                    url: '{{ route("admin.dealPipeline.checkPassport") }}',
                    type: 'POST',
                    data: {
                        passport_no: passportNo,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if(response.exists) {
                            $error.text('This Passport number exists with "' + response.creator + '"');
                            $submitBtn.prop('disabled', true);  // Disable submit
                        } else {
                            $error.text('');
                            $submitBtn.prop('disabled', false); // Enable submit
                        }
                    },
                    error: function() {
                        $error.text('Error checking passport number');
                        $submitBtn.prop('disabled', true);  // Disable submit on error
                    }
                });
            });

            $(document).on('input', '#edit_passport_no', function() {
                var passportNo = $(this).val().trim();
                var $error = $('#edit-passport-error');
                var $submitBtn = $('.disableEditDealBtn');
                var dealId = $(this).data('deal-id'); // set current deal ID on the input as data attribute

                if(passportNo === '') {
                    $error.text('');
                    $submitBtn.prop('disabled', false);
                    return;
                }

                $.ajax({
                    url: '{{ route("admin.dealPipeline.checkPassport") }}',
                    type: 'POST',
                    data: {
                        passport_no: passportNo,
                        deal_id: dealId, // exclude current deal
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if(response.exists) {
                            $error.text('This Passport number exists with "' + response.creator + '"');
                            $submitBtn.prop('disabled', true);
                        } else {
                            $error.text('');
                            $submitBtn.prop('disabled', false);
                        }
                    },
                    error: function() {
                        $error.text('Error checking passport number');
                        $submitBtn.prop('disabled', true);
                    }
                });
            });

            // ============================================================== 
            // Toggle fields based on Business type 
            // ==============================================================
            $('.add-new').click(function(){
                if($('#JobSeekerOptionValueId').val()){
                    let JobSeekerOptionValueId = $('#JobSeekerOptionValueId').val();
                    $("#add_business_id").val(JobSeekerOptionValueId).change();
                }
            });


            // ============================================================== 
            // Open edit modal
            // ==============================================================
            $('body').on('click', '.edit-deal-btn', function() {
                var dealId = $(this).data('id');
                $.get('{{ route("admin.dealPipeline.getDeal") }}', { deal_id: dealId }, function(res) {
                    if(res.success) {
                        var deal = res.data;                        
                        $('#edit_deal_id').val(deal.id);
                        $('#edit_business_id').val(deal.business_id).trigger('change');

                        if(deal.business.name === 'Job seeker') {
                            $('#editCommonFields').hide();
                            $('#editJobSeekerFields').show();

                            $('#edit_associate_id').val(deal.associate_id).trigger('change');
                            $('#edit_careoff_id').val(deal.care_of.id).trigger('change');
                            $('#edit_candidate').val(deal.candidate);
                            $('#edit_passport_no').val(deal.passport_no);
                            $('#edit_country').val(deal.country);
                            $('#edit_city').val(deal.city);
                            $('#edit_amount').val(deal.amount);
                            $('#edit_mobile').val(deal.contact);
                            $('#edit_mobile_whatsapp').val(deal.contact_whatsapp);
                            $('#edit_email').val(deal.email);
                            $('#edit_source').val(deal.source.id).trigger('change');
                            $('#edit_closed_date').val(deal.close_date);

                            if(deal.job_title != null){
                                $('#edit_job_title').val(deal.job_title.id).trigger('change');
                            }

                           


                            // Initialize Flatpickr for single date
                            var flatpickerEditClosedDate = $('.flatpicker-edit-closed-date');
                            var flatpickerEditClosedDateInstance;
                            if(flatpickerEditClosedDate.length) {
                                flatpickerEditClosedDateInstance = flatpickerEditClosedDate.flatpickr({
                                    monthSelectorType: 'static',
                                    dateFormat: 'Y-m-d',
                                    minDate: "today"
                                });
                            }

                            // Set date in Flatpickr
                            if(flatpickerEditClosedDateInstance && flatpickerEditClosedDateInstance[0]) {
                                flatpickerEditClosedDateInstance[0].setDate(deal.close_date, true);
                            }

                            $('#edit_notes').val(deal.notes);

                        }
                        else{
                            $('#editJobSeekerFields').hide();
                            $('#editCommonFields').show();

                            $('#edit_common_careoff').val(deal.care_of.id).trigger('change');
                            $('#edit_common_name').val(deal.name);
                            $('#edit_common_job_title').val(deal.job_title_other);
                            $('#edit_common_company_name').val(deal.company);
                            $('#edit_common_country').val(deal.country);
                            $('#edit_common_city').val(deal.city);
                            $('#edit_common_mobile').val(deal.contact);
                            $('#edit_common_mobile_whatsapp').val(deal.contact_whatsapp);
                            $('#edit_common_email').val(deal.email);
                            $('#edit_common_source').val(deal.source).trigger('change');
                            $('#edit_common_notes').val(deal.notes);

                        }

                    } else {
                        toastr.error(res.message || "Unable to fetch deal data!");
                    }
                }, 'json');
            });

            
            // ============================================================== 
            // editDealPipelineForm submit
            // ==============================================================
            $('#editDealPipelineForm').on('submit', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    $.post(form.attr('action'), form.serialize(), function(res) {
                        if(res.success) {
                            toastr.success(res.message);
                            form[0].reset();
                            $('#editJobSeekerFields, #editAssociateFields').hide();

                            var offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasEditDealPipeline'));
                            if(offcanvas) offcanvas.hide();

                            // setTimeout(function() { 
                            //     location.reload(); 
                            // }, 1000); // 1000ms = 1 second
                            reloadDealsList();


                        } else {
                            toastr.error(res.message);
                        }
                    }, 'json');
            });


            // ===============================
            // Pre-fill saved filter values
            // ===============================
            $('#dealFilterPanel').on('shown.bs.modal', function () {
                // Refresh selectpicker to pick up selected values from Blade
                // $('.selectpicker').selectpicker('refresh');

                // Created Date
                var createdStart = '{{ $saveDealFilter->created_date_start ?? '' }}';
                var createdEnd   = '{{ $saveDealFilter->created_date_end ?? '' }}';
                if(createdStart && createdEnd){
                    $('#created-date').data('daterangepicker').setStartDate(moment(createdStart));
                    $('#created-date').data('daterangepicker').setEndDate(moment(createdEnd));
                    $('#created-date').val(moment(createdStart).format('MM/DD/YYYY') + ' - ' + moment(createdEnd).format('MM/DD/YYYY'));
                }

                // Updated Date
                var updatedStart = '{{ $saveDealFilter->updated_date_start ?? '' }}';
                var updatedEnd   = '{{ $saveDealFilter->updated_date_end ?? '' }}';
                if(updatedStart && updatedEnd){
                    $('#updated-date').data('daterangepicker').setStartDate(moment(updatedStart));
                    $('#updated-date').data('daterangepicker').setEndDate(moment(updatedEnd));
                    $('#updated-date').val(moment(updatedStart).format('MM/DD/YYYY') + ' - ' + moment(updatedEnd).format('MM/DD/YYYY'));
                }
            });


            // ===============================
            // 2. Initialize Flatpickr for single dates
            // ===============================
            function initFlatpickr(selector) {
                $(selector).flatpickr({
                    monthSelectorType: 'static',
                    dateFormat: 'Y-m-d',
                    minDate: "today"
                });
            }
            initFlatpickr('.flatpicker-edit-closed-date, .flatpicker-edit-assoc-closed-date');

            // ===============================
            // 3. Initialize Date Range Picker
            // ===============================
            function initDateRangePicker(selector) {
                $(selector).daterangepicker({
                    autoUpdateInput: false,
                    opens: 'right',
                    locale: { cancelLabel: 'Clear' }
                }).on('apply.daterangepicker', function(ev, picker){
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                    reloadDealsList();
                }).on('cancel.daterangepicker', function(){
                    $(this).val('');
                    reloadDealsList();
                });
            }
            initDateRangePicker('.bsdatpicket');

            // ===========================================================
            // 4. Toggle Job Seeker / Associate fields
            // ===========================================================

            $('#add_business_id').on('change', function() {
                var selectedText = $("#add_business_id option:selected").text(); 

                if (selectedText === "Job seeker") { 
                    // Clear fields for job seeker mode
                    $('select[name="add_common_careoff"]').val(null).trigger('change');
                    $('select[name="add_common_name"]').val(null).trigger('change');
                    $('select[name="add_common_job_title"]').val(null).trigger('change');
                    $('input[name="add_common_company_name"]').val(null).trigger('change');
                    $('input[name="add_common_country"]').val(null).trigger('change');
                    $('input[name="add_common_city"]').val(null).trigger('change');
                    $('input[name="add_common_mobile"]').val(null).trigger('change');
                    $('input[name="add_common_mobile_whatsapp"]').val(null).trigger('change');
                    $('input[name="add_common_email"]').val(null).trigger('change');
                    $('input[name="add_common_source"]').val(null).trigger('change');
                    $('input[name="add_common_notes"]').val(null).trigger('change');

                    // Hide common fields
                    $('#commonFields').hide().find('#add_common_careoff').prop('required', false);

                    // Show job seeker fields
                    $('#jobSeekerFields').show().find('#add_associate_id, #add_careoff_id, #add_source').prop('required', true); 
                } 
                else {
                    // 👇 KEEP the preselected careoff value (don’t clear)
                    var defaultCareoff = $('#add_common_careoff option[selected]').val();
                    if (defaultCareoff) {
                        $('select[name="add_common_careoff"]').val(defaultCareoff).trigger('change');
                    }

                    $('input[name="add_common_name"]').val(null).trigger('change');
                    $('input[name="add_common_job_title"]').val(null).trigger('change');
                    $('input[name="add_common_company_name"]').val(null).trigger('change');
                    $('input[name="add_common_country"]').val(null).trigger('change');
                    $('input[name="add_common_city"]').val(null).trigger('change');
                    $('input[name="add_common_mobile"]').val(null).trigger('change');
                    $('input[name="add_common_mobile_whatsapp"]').val(null).trigger('change');
                    $('input[name="add_common_email"]').val(null).trigger('change');
                    $('input[name="add_common_source"]').val(null).trigger('change');
                    $('input[name="add_common_notes"]').val(null).trigger('change');

                    // Hide job seeker fields
                    $('#jobSeekerFields').hide().find(':input').prop('required', false);

                    // Show common fields again
                    $('#commonFields').show().find('#add_common_careoff, #add_common_source').prop('required', true);
                } 
            });

            $('#edit_business_id').on('change', function() { 
                var selectedText = $("#edit_business_id option:selected").text(); 

                if (selectedText === "Job seeker") {
                    // Reset Job common Fields
                    $('#editCommonFields').hide().find('#edit_common_careoff').prop('required', false);
                    // Reset Job Seeker Fields
                    $('#editJobSeekerFields').find(':input').val(null).trigger('change');
                    // Show Job Seeker Fields
                    $('#editJobSeekerFields').show().find('#edit_associate_id, #edit_careoff_id, #edit_source').prop('required', true); 
                } 
                else {
                    // Reset Job Seeker Fields
                    $('#editJobSeekerFields').hide().find(':input').prop('required', false); 
                    // Reset Common Fields
                    $('#editCommonFields').find(':input').val(null).trigger('change');
                    // Show Common Fields
                    $('#editCommonFields').show().find('#edit_common_careoff, #edit_common_source').prop('required', true);
                } 
            });


            // ===============================
            // 5. AJAX Form Submission
            // ===============================
            function ajaxFormSubmit(formSelector, offcanvasSelector) {
                $(formSelector).on('submit', function(e){
                    e.preventDefault();
                    var form = $(this);
                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: form.serialize(),
                        dataType: 'json',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        beforeSend: function(){ form.find('button[type="submit"]').prop('disabled', true).text('Submitting...'); },
                        success: function(res){
                            if(res.success){
                                toastr.success(res.message || "Form submitted successfully!");
                                form[0].reset();
                                $(offcanvasSelector + ' #jobSeekerFields').hide();
                                var offcanvas = bootstrap.Offcanvas.getInstance(document.querySelector(offcanvasSelector));
                                if(offcanvas) offcanvas.hide();
                                setTimeout(()=>location.reload(), 1000);
                            } else toastr.error(res.message || "Something went wrong!");
                        },
                        error: function(xhr){
                            if(xhr.status === 422){
                                $.each(xhr.responseJSON.errors, function(k,v){ toastr.error(v[0]); });
                            } else toastr.error("Server error occurred!");
                        },
                        complete: function(){ form.find('button[type="submit"]').prop('disabled', false).text('Submit'); }
                    });
                });
            }
            ajaxFormSubmit('#addDealPipelineForm', '#offcanvasAddDealPipeline');
            // ajaxFormSubmit('#editDealPipelineForm', '#offcanvasEditDealPipeline');


            // ================================
            // 🔹 Update Deal Stage
            // ================================
            $(document).on('click', '.updateStatus', function (e) {
                e.preventDefault();

                var $this = $(this);
                var dealId = $this.data('id');
                var dealStageText = $this.text().trim();
                var badgeElement = $this.closest('.dropdown').find('.badge');

                // Disable button while processing
                $this.prop('disabled', true);

                $.ajax({
                    url: '{{ route("admin.dealPipeline.updateDealStage") }}',
                    type: 'POST',
                    data: {
                        id: dealId,
                        text: dealStageText,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.message) {
                            toastr.success(response.message, 'Success', { timeOut: 2000 });

                            // Update badge instantly
                            badgeElement
                                .removeClass(function (i, c) {
                                    return (c.match(/(^|\s)bg-label-\S+/g) || []).join(' ');
                                })
                                .addClass('bg-label-' + (response.badge ?? 'primary'))
                                .html(response.status + ' <i class="ti ti-chevron-down ti-xs"></i>');

                            // Highlight active dropdown item
                            $this.closest('.dropdown-menu')
                                .find('.dropdown-item')
                                .removeClass('active fw-bold text-primary');
                            $this.addClass('active fw-bold text-primary');
                        } else {
                            toastr.error(response.message || 'Something went wrong', 'Error');
                        }
                    },
                    error: function (xhr) {
                        toastr.error('Failed to update deal stage', 'Error');
                        console.error(xhr.responseText);
                    },
                    complete: function () {
                        $this.prop('disabled', false);
                    }
                });
            });

            // ===================================================
            // 🔹 Open Recruit Status Model For Used Only Kanban
            // ===================================================
            $(document).on('click', '.openRecruitModal', function () {
                let dealId = $(this).data('id');
                let currentStatus = $(this).data('status');

                $('#recruit_deal_id').val(dealId);
                $('#recruitStatusDropdown').text(currentStatus);
                tempSelectedStatus = currentStatus;

                // Remove previous highlights
                $('#recruitStatusModal .selectRecruitStatus').removeClass('active fw-bold text-primary');

                $('#recruitStatusModal').modal('show');
            });

            // Select status in modal
            $(document).on('click', '.selectRecruitStatus', function () {
                tempSelectedStatus = $(this).data('status');
                $('#recruitStatusDropdown').text(tempSelectedStatus);

                // Highlight selected item
                $(this).closest('.dropdown-menu').find('.dropdown-item').removeClass('active fw-bold text-primary');
                $(this).addClass('active fw-bold text-primary');
            });

            // Update button triggers existing .updateRecruitStatus code
            $(document).on('click', '#updateRecruitStatusBtn', function () {
                let dealId = $('#recruit_deal_id').val();

                // Prevent if no status selected
                if(tempSelectedStatus == 'Select Status'){
                    toastr.error('Please select a status first', 'Error');
                    return;
                }

                // Find the corresponding hidden dropdown item and trigger click
                let $fakeItem = $('<a>')
                    .addClass('updateRecruitStatus')
                    .attr('data-id', dealId)
                    .text(tempSelectedStatus)
                    .appendTo('body');

                $fakeItem.trigger('click');
                $fakeItem.remove(); // clean up
            });

            // ================================
            // 🔹 Update Recruit Status
            // ================================
            $(document).on('click', '.updateRecruitStatus', function (e) {
                e.preventDefault();

                var $this = $(this);
                var dealId = $this.data('id');
                var recruitStatus = $this.text().trim();
                var badgeElement = $this.closest('.dropdown-custom').find('.badge');

                // If not found (means clicked inside modal)
                if (badgeElement.length === 0) {
                    // Find the card in Kanban matching this deal ID
                    badgeElement = $('.dropdown-custom').find(`[data-id="${dealId}"]`).closest('.dropdown-custom').find('.badge');
                }

                // Disable button while processing
                $this.prop('disabled', true);

                $.ajax({
                    url: '{{ route("admin.dealPipeline.updateRecruitStatus") }}',
                    type: 'POST',
                    data: {
                        id: dealId,
                        status: recruitStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.message) {
                            toastr.success(response.message, 'Success', { timeOut: 2000 });

                            // ✅ Update badge instantly
                            badgeElement
                                .removeClass(function (i, c) {
                                    return (c.match(/(^|\s)bg-label-\S+/g) || []).join(' ');
                                })
                                .addClass('bg-label-' + (response.badge ?? 'primary'))
                                .html(response.status + ' <i class="ti ti-chevron-down ti-xs"></i>');

                            // ✅ Highlight active dropdown item
                            $this.closest('.dropdown-menu')
                                .find('.dropdown-item')
                                .removeClass('active fw-bold text-primary');
                            $this.addClass('active fw-bold text-primary');

                            // ✅ If triggered from modal — update dropdown text and close
                            if ($this.closest('#recruitStatusModal').length) {
                                $('#recruitStatusDropdown').text(recruitStatus);
                                $('#recruitStatusModal').modal('hide');
                            }

                            // ✅ Update the .openRecruitModal data and status text dynamically
                            $('.openRecruitModal[data-id="' + dealId + '"]')
                                .attr('data-status', recruitStatus) // update status
                                .data('status', recruitStatus);      // update jQuery data cache too
                        } else {
                            toastr.error(response.message || 'Something went wrong', 'Error');
                        }
                    },
                    error: function (xhr) {
                        toastr.error('Failed to update recruit status', 'Error');
                        console.error(xhr.responseText);
                    },
                    complete: function () {
                        $this.prop('disabled', false);
                    }
                });
            });


            // Tracks the in-flight deals request so overlapping triggers
            // (rapid clicks, cascading change events, etc.) can never race
            // each other or produce more than one error toast.
            var dealListRequest = null;

            function reloadDealsList() {

                // Cancel any request still in flight before starting a new one.
                if (dealListRequest && dealListRequest.readyState !== 4) {
                    dealListRequest.abort();
                }

                // 1️⃣ Add skeletons to ALL kanban columns
                $('.kanban-deal').each(function () {
                    for (let i = 0; i < 3; i++) {
                        $(this).append('<div class="kanban-skeleton"></div>');
                    }
                });

                dealListRequest = $.ajax({
                    url: '{{ route("admin.dealPipeline.list") }}',
                    method: 'GET',
                    data: getDealFilterData(),

                    success: function (data) {

                        // 2️⃣ Replace content
                        $('.dealpaginate').empty().html(data);
                        $('.kanban-row').empty().html(data);

                        initKanbanSortable();
                        sizeKanbanBoard();

                        // ✅ RESET SCROLL AFTER DOM UPDATE — .card-body-kanban-row
                        // is the board's scroll owner now, not each column.
                        setTimeout(function () {
                            $('.card-body-kanban-row').scrollTop(0).scrollLeft(0);
                        }, 50);

                    },

                    error: function (jqXHR, textStatus) {
                        // A request we aborted ourselves (superseded by a
                        // newer one) isn't a real failure — skip the toast.
                        if (textStatus === 'abort') return;
                        toastr.error('Failed to fetch deals.', 'Error');
                    },

                    complete: function () {
                        // 3️⃣ Remove skeleton AFTER load
                        $('.kanban-skeleton').remove();
                    }
                });

                updateDealFilterIndicator();
            }


            // Bind filters change
            var dealFilterFieldsSelector = '#pagination_list, #search_text, #kanban_search_text, #by-business, #by-deal-stage, #by-recruite-status, #by-job-title, #by-associate, #by-careoff, #by-followup-before, #by-source, #by-created-by, #created-date, #updated-date';

            $(dealFilterFieldsSelector).on('change.dealFilter input.dealFilter', reloadDealsList);

            // ===============================
            // Deal Stage / Recruit Status bar -> apply matching filter
            //
            // Each pill belongs to one group (stage / recruit) via
            // data-group. Clicking only clears/sets pills within that SAME
            // group — the other group's selection is left alone — so Deal
            // Stage + Recruit Status combine (AND together) instead of one
            // click resetting the other.
            // ===============================
            var dealStatusFieldSelectorMap = {
                deal_stage_id: '#by-deal-stage',
                recruite_status_id: '#by-recruite-status',
            };

            $(document).on('click', '.dealStatusCard', function (e) {
                e.preventDefault();

                var $this = $(this);
                var group = $this.data('group');
                var field = $this.data('field');
                var value = $this.data('value') || '';
                var wasActive = $this.hasClass('active');

                if (wasActive && !value) {
                    return; // already at rest ("All" already active) — avoid a duplicate request
                }

                $('.dealStatusCard[data-group="' + group + '"]').removeClass('active');

                var effectiveValue = wasActive ? '' : value; // clicking the active pill again clears it
                if (!wasActive) {
                    $this.addClass('active');
                }

                var selector = dealStatusFieldSelectorMap[field];
                // val() (not deselectAll()) — deselectAll() fires a real
                // native 'change' on the field, which would trigger its own
                // bound reload immediately and race against the single
                // reload fired below.
                $(selector).selectpicker('val', effectiveValue ? [String(effectiveValue)] : []);

                reloadDealsList(); // already calls updateDealFilterIndicator() itself
            });

            // Keep each bar's active pill accurate even if #by-deal-stage /
            // #by-recruite-status change from somewhere other than these
            // pills (e.g. the filter-panel dropdown) — single-select/
            // radio-button semantics, not just a cosmetic default.
            function syncActiveDealStatusPill(group, fieldSelector) {
                var current = $(fieldSelector).val() || [];
                var value = current.length ? String(current[0]) : '';

                $('.dealStatusCard[data-group="' + group + '"]').removeClass('active');

                var $match = $('.dealStatusCard[data-group="' + group + '"][data-value="' + value + '"]');
                if (!$match.length) {
                    $match = $('.dealStatusCard[data-group="' + group + '"][data-value=""]');
                }
                $match.addClass('active');
            }

            syncActiveDealStatusPill('stage', '#by-deal-stage');
            syncActiveDealStatusPill('recruit', '#by-recruite-status');
            $('#by-deal-stage').on('change', function () { syncActiveDealStatusPill('stage', '#by-deal-stage'); });
            $('#by-recruite-status').on('change', function () { syncActiveDealStatusPill('recruit', '#by-recruite-status'); });


            // Pagination click handling
            $(document).on('click','.pagination a',function(e){
                e.preventDefault();
                var url = $(this).attr('href') + "&" + $.param(getDealFilterData());
                $.get(url, function(data){ $('.dealpaginate').html(data); });
                window.history.pushState("", "", url);
            });

            // Reset Filter
            $(document).on('click', '.resetDealFilter', function () {

                // Detach the filter-change listener while we clear each
                // field below — bootstrap-select's deselectAll() fires a
                // native 'change' event per select that had a value, and
                // without this guard each one would trigger its own
                // reloadDealsList() call (and its own error toast on failure).
                $(dealFilterFieldsSelector).off('change.dealFilter input.dealFilter');

                $('.selectpicker').selectpicker('deselectAll');
                $('#created-date, #updated-date').val('');
                $('#by-followup-before').val('').change();

                // Reset the Deal Stage / Recruit Status bars back to "All".
                $('.dealStatusCard').removeClass('active');
                $('.dealStatusCard[data-value=""]').addClass('active');

                // Re-attach for subsequent user-driven filter changes.
                $(dealFilterFieldsSelector).on('change.dealFilter input.dealFilter', reloadDealsList);

                updateDealFilterIndicator();

                $.post('{{ route("admin.dealPipeline.saveFilter") }}', getDealFilterData(), function(res){
                    toastr.success(res.message || 'Filter Reset successfully!', 'Success', {timeOut:2000});
                }).fail(function(){ toastr.error('Failed to save filter','Error'); });

                $('.load-more-kanban').data('offset', 0);

                // Fetch the deals exactly once, after all filters are cleared.
                reloadDealsList();

            });

            // Save Filter
            $(document).on('click', '.saveDealFilter', function () {

                $.post('{{ route("admin.dealPipeline.saveFilter") }}', getDealFilterData(), function(res){
                    toastr.success(res.message || 'Filter saved successfully!', 'Success', { timeOut: 2000 });
                }).fail(function(){
                    toastr.error('Failed to save filter','Error');
                });

                // ✅ RESET LOAD MORE STATE
                $('.load-more-kanban').data('offset', 0);

                // ✅ RELOAD AFTER MODAL CLOSE
                reloadDealsList();

            });

            // ===============================
            // 8. Delete Deal Pipeline
            // ===============================
            $('#deleteDealPipeline').on('show.bs.modal', function(e){
                $('#delete_deal_pipeline_id').val($(e.relatedTarget).data('id'));
            });

            $(document).on('click','.deletebtn', function(e){
                e.preventDefault();
                $.post('{{ route("admin.dealPipeline.delete") }}', $('#deleteDealPipelineForm').serialize(), function(res){
                    toastr.success(res.success);
                    $('#deleteDealPipeline').modal('hide');
                    reloadDealsList();
                });
            });


            // ===== Switch to Kanban View =====
            $('.changetokanban').on('click', function(){
                $('#disDealKanban').show();    // Show Kanban
                $('.disDealList').hide();      // Hide list view

                // Fetch filter values
                var page_list     = $('#pagination_list').val();
                var search_text   = $('#search_text').val();
                var kanban_search_text   = $('#kanban_search_text').val();
                var business_id   = $('#by-business').val();
                var deal_stage_id = $('#by-deal-stage').val();
                var recruite_status_id   = $('#by-recruite-status').val();
                var job_title_id  = $('#by-job-title').val();
                var associate_id  = $('#by-associate').val();
                var careoff_id    = $('#by-careoff').val();
                var followup_before = $('#by-followup-before').val();
                var source        = $('#by-source').val();
                var created_date  = $('#created-date').val();
                var updated_date  = $('#updated-date').val();
                var created_by    = $('#by-created-by').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                // Switch mode to Kanban
                $.ajax({
                    url : '{{ route("admin.dealPipeline.switchto") }}',
                    method: "POST",
                    data: {
                        "switch": 'kanban',
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(data){
                        toastr.success(data.res);
                        setTimeout(function() {
                            location.reload();
                        }, 500); // 2000 milliseconds = 2 seconds
                    }
                });
            });

            // ===== Switch to List View =====
            $('.changetolist').on('click', function(){
                $('#disDealKanban').hide();    // Hide Kanban
                $('.disDealList').show();      // Show list view

                // Fetch filter values
                var page_list     = $('#pagination_list').val();
                var search_text   = $('#search_text').val();
                var kanban_search_text   = $('#kanban_search_text').val();
                var business_id   = $('#by-business').val();
                var deal_stage_id = $('#by-deal-stage').val();
                var recruite_status_id   = $('#by-recruite-status').val();
                var job_title_id  = $('#by-job-title').val();
                var associate_id  = $('#by-associate').val();
                var careoff_id    = $('#by-careoff').val();
                var followup_before = $('#by-followup-before').val();
                var source        = $('#by-source').val();
                var created_date  = $('#created-date').val();
                var updated_date  = $('#updated-date').val();
                var created_by    = $('#by-created-by').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                // Switch mode to List
                $.ajax({
                    url : '{{ route("admin.dealPipeline.switchto") }}',
                    method: "POST",
                    data: {
                        "switch": 'deallist',
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(data){
                        toastr.success(data.res);

                        setTimeout(function() {
                            location.reload();
                        }, 500); // 2000 milliseconds = 2 seconds

                    }
                });
            });

            // Status Bar toggle: shows/hides the Deal Stage / Recruit Status
            // Summary Bar. Persisted client-side so the choice survives
            // reloads and keeps working across list/kanban switches and
            // AJAX refreshes, since those never re-render this bar's
            // container. display is forced with !important (and bound via
            // delegation) so nothing else on this page can silently
            // override it. Both the List-view and Kanban-view Option
            // dropdowns render their own copy of this switch, so state is
            // synced across every instance via the shared
            // .deal-status-bar-switch class.
            (function(){
                function setDealStatusBarVisible(visible){
                    var bar = document.getElementById('dealStatusBar');
                    if (!bar) return;
                    if (visible) {
                        bar.style.removeProperty('display');
                    } else {
                        bar.style.setProperty('display', 'none', 'important');
                    }
                }

                var stored = null;
                try { stored = localStorage.getItem('dealPipeline_status_bar_visible'); } catch(e) {}
                var visible = stored === null ? true : stored === '1';
                $('.deal-status-bar-switch').prop('checked', visible);
                setDealStatusBarVisible(visible);

                $(document).on('change', '.deal-status-bar-switch', function(){
                    var isVisible = $(this).is(':checked');
                    $('.deal-status-bar-switch').prop('checked', isVisible);
                    setDealStatusBarVisible(isVisible);
                    try { localStorage.setItem('dealPipeline_status_bar_visible', isVisible ? '1' : '0'); } catch(e) {}
                });
            })();

        });

        $(document).on('click', '.load-more-kanban', function () {

            let btn     = $(this);
            let stageId = btn.data('stage');
            let offset  = btn.data('offset');

            // UI state
            btn.prop('disabled', true);
            btn.find('.btn-text').text('Loading...');
            btn.find('.spinner-border').removeClass('d-none');

            $.ajax({
                url: '{{ route("admin.dealPipeline.kanban.loadMore") }}',
                method: 'GET',
                data: {
                    stage_id: stageId,
                    offset: offset,
                    ...getDealFilterData() // ✅ THIS IS REQUIRED

                },
                success: function (html) {

                    if ($.trim(html) === '') {
                        btn.fadeOut(200, function () { $(this).remove(); });
                        return;
                    }

                    let container = $('.stage-' + stageId);
                    let newCards  = $(html).css({ opacity: 0 });

                    container.append(newCards);

                    newCards.animate({ opacity: 1 }, 400);

                    // smooth scroll AFTER DOM update
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
