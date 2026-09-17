@extends('layout.admin.admin_layout')

@section('title','Lead List')

@section('page-style')
{{-- Only vendor assets this page actually initializes (verified against every
     init call in this file and in the page's own leads-validation.js) are
     loaded here — buttons.bootstrap5/flatpickr/bootstrap-datepicker/
     jquery-timepicker/tagify/pickr are unused on this page and were removed. --}}
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
<style>
    .pagestyle {
        height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out
    }

    .pagestyle:focus {
        color: #6e6b7b;
        background-color: #fff;
        border-color: #7367f0;
        outline: 0;
        box-shadow: 0 3px 10px 0 rgba(34, 41, 47, 0.1);
    }

    .optionBtn {
        height: 26.6px !important;
    }
</style>
<style>
    .filter-indicator {
        position: absolute;
        top: 1px;
        right: 2px;
        width: 8px;
        height: 8px;
        background: #ffc107;
        border-radius: 50%;
    }

    .lead_conversation_type {
        margin-right: 5px !important;
    }
</style>
<style>
    .candidate-status-wrapper {
      width: 100%;
    }

    .candidate-status-card {
      background: var(--bs-card-bg);
      padding: 15px;
      border-radius: 8px;
    }

    .candidate-status-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .status-item {
      flex: 0 0 auto;
    }

    .status-btn {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 4px 10px;
      min-height: 28px;
      font-size: 11px;
      white-space: nowrap;
      border: 1px solid;
      border-radius: 4px;
      text-decoration: none;
      font-weight: 500;
    }


    .status-btn strong {
      font-size: 16px;
      line-height: 1;
    }

    .candidate-status-card {
      padding: 10px;
    }

    .status-btn:hover {
      background: rgba(115, 103, 240, 0.05);
    }

    @media (max-width: 991px) {
      .status-item {
        flex: 0 0 calc(25% - 10px);
      }
    }

    @media (max-width: 767px) {
      .status-item {
        flex: 0 0 calc(50% - 10px);
      }
    }

    .iconDetails {
      margin-left: 2%;
      float: left;
      height: 63px;
      width: 40px;
      color: white;
    }

    .container2 {
      width: 100%;
      height: 45px;
      padding: 1%;
      margin: 3px;
    }

    h4 {
      margin: 5px;
    }

    h5 b {
      color: white;
    }

    .info-box-icon {
      border-radius: 0.25rem;
      -ms-flex-align: center;
      align-items: center;
      display: -ms-flexbox;
      display: flex;
      font-size: 1rem;
      -ms-flex-pack: center;
      justify-content: center;
      text-align: center;
      width: 34px;
      height: 34px;
      margin-top: 4px;
    }

    .info-box-content {
      display: -ms-flexbox;
      display: flex;
      -ms-flex-direction: column;
      flex-direction: column;
      -ms-flex-pack: center;
      justify-content: center;
      line-height: 1.8;
      -ms-flex: 1;
      flex: 1;
      padding: 0 10px;
      padding-top: 5px;
    }

    h5 b {
      color: #92969f;
      font-size: 0.912rem;
    }

    #leadTable_length {
        display: flex !important;
        flex-direction: row !important;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    #leadTable_length label {
        order: 1;
        margin-bottom: 0;
    }

    #leadTable_length .custom-toolbar {
        order: 2;
    }

    #leadTable thead th {
      text-transform: none !important;
    }
</style>
<style>
    .stage-card.active {
        background: #7367F0;
        color: #fff !important;
    }

    .stage-card.active span,
    .stage-card.active strong {
        color: #fff !important;
    }

    .stage-card.active,
    .cnc-type-card.active {
        background: #7367F0;
        color: #fff !important;
    }

    .stage-card.active span,
    .stage-card.active strong,
    .cnc-type-card.active span,
    .cnc-type-card.active strong {
        color: #fff !important;
    }

    .notes-table-scroll thead th{
        position:sticky;
        top:0;
        z-index:2;
        background: var(--bs-card-bg);
        border-bottom:1px solid var(--bs-border-color, #dee2e6);
    }

    .notes-table-scroll td,
    .notes-table-scroll th{
        padding:.75rem 1rem;
        vertical-align:middle;
    }

    .notes-table-scroll td:first-child{
        white-space:normal;
        word-break:break-word;
    }

    .notes-table-scroll::-webkit-scrollbar{
        width:6px;
    }

    .notes-table-scroll::-webkit-scrollbar-thumb{
        background:#c7c7c7;
        border-radius:10px;
    }

    #lead-tab .card {
        box-shadow: none !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">


    <!-- Deal Stage / Recruit Status Summary Bar Start -->
    <div class="card" id="leadStatusBar">
        <div class="row match-height">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">

                        @php
                            $stages = [
                                0 => ['title' => 'Not Yet', 'color' => '#7367F0'],
                                1 => ['title' => 'Followed Up', 'color' => '#28C76F'],
                                2 => ['title' => 'Call Not Connected', 'color' => '#FF9F43'],
                                3 => ['title' => 'Lead Qualified', 'color' => '#00CFE8'],
                                4 => ['title' => 'Lead Not Qualified', 'color' => '#EA5455'],
                                5 => ['title' => 'Lead Not Relevant', 'color' => '#d28989'],
                            ];
                        @endphp

                        <div class="candidate-status-wrapper">
                            <div class="candidate-status-card">
                                <div class="candidate-status-grid">

                                    <div class="status-item">
                                        <a href="javascript:void(0)"
                                        class="status-btn stage-card"
                                        data-stage="All"
                                        style="border-color:#6C757D;color:#6C757D;">
                                            <span>All</span>
                                            <strong id="qualified_all">0</strong>
                                        </a>
                                    </div>


                                    <div class="status-item">
                                        <a href="javascript:void(0)"
                                        class="status-btn stage-card"
                                        data-stage="Previous Lead"
                                        style="border-color:#6F42C1;color:#6F42C1;">
                                            <span>Previous Lead</span>
                                            <strong id="qualified_previous_lead">0</strong>
                                        </a>
                                    </div>

                                    @foreach($stages as $key => $stage)
                                        <div class="status-item">
                                            <a href="javascript:void(0)"
                                            class="status-btn stage-card"
                                            data-stage="{{ $key }}"
                                            style="border-color:{{ $stage['color'] }};color:{{ $stage['color'] }};">
                                                <span>{{ $stage['title'] }}</span>
                                                <strong id="qualified_{{ $key }}">0</strong>
                                            </a>
                                        </div>
                                    @endforeach

                                    <div class="status-item">
                                        <a href="javascript:void(0)"
                                        class="status-btn stage-card"
                                        data-stage="Lead Reassigned"
                                        style="border-color:#0D6EFD;color:#0D6EFD;">
                                            <span>Lead Reassigned</span>
                                            <strong id="qualified_reassigned">0</strong>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>

                         @php
                            $callNotConnectedTypes = [
                                'Not Answer'     => ['title' => 'Not Answer', 'color' => '#7367F0'],
                                'Busy'           => ['title' => 'Busy', 'color' => '#FF9F43'],
                                'Not Reachable'  => ['title' => 'Not Reachable', 'color' => '#EA5455'],
                                'Not Available'  => ['title' => 'Not Available', 'color' => '#28C76F'],
                                'Wrong Number'   => ['title' => 'Wrong Number', 'color' => '#00CFE8'],
                            ];
                        @endphp

                        <div class="candidate-status-wrapper">
                            <div class="candidate-status-card">
                                <div class="candidate-status-grid">

                                    @foreach($callNotConnectedTypes as $key => $type)
                                        <div class="status-item">
                                            <a href="javascript:void(0)"
                                            class="status-btn cnc-type-card"
                                            data-type="{{ $key }}"
                                            style="border-color:{{ $type['color'] }};color:{{ $type['color'] }};">
                                                <span>{{ $type['title'] }}</span>
                                                <strong id="cnc_{{ \Illuminate\Support\Str::slug($key, '_') }}">0</strong>
                                            </a>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </div>
        <br>
    </div>
    <!-- Deal Stage / Recruit Status Summary Bar End -->

    <div class="card">
        <div class="card-datatable table-responsive leadpaginate">
            @include('admin.leads.loadlead_new')
        </div>
    </div>

    <!-- Delete Todo Start -->
    <div class="modal fade" id="deletelead" aria-hidden="true" aria-labelledby="deleteleadLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="offcanvas-title" id="updateLstageLabel">Delete Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.leads.delete') }}" id="deleteleadForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="lead_id" id="leadID2">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <p class="text-danger">Are you sure to delete Lead?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="button" class="btn btn-danger btn-sm deletebtn" id="disbtn">Delete</button> --}}
                        <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Delete Todo End -->

    <div class="modal fade" id="editContactModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="updateContactForm">
                @csrf
                <input type="hidden" name="id" id="contact_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Mobile Number</label>
                            <input type="text" name="mob_no" id="mob_no" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>WhatsApp Number</label>
                            <input type="text" name="whatsapp_no" id="whatsapp_no" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Show / Hide Columns Modal -->
    <div class="modal fade" id="showHideColumnsModal" tabindex="-1" aria-labelledby="showHideColumnsModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showHideColumnsModalLabel">
                        Show / Hide Columns
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="columnToggles" class="row g-2">
                        <!-- Column toggle checkboxes will be dynamically injected here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Todo Start -->
    <div class="modal fade" id="updateUserLeadAssign" aria-hidden="true" aria-labelledby="updateUserLeadAssignLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="offcanvas-title" id="updateLstageLabel">Lead Assign</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.lead.userleadassign.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="lead_assign_status" id="inlineRadio1" value="1" @if(Auth::guard('admin')->user()->lead_assign_status == 1) checked @endif />
                                        <label class="form-check-label" for="inlineRadio1">Assign Lead Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="lead_assign_status" id="inlineRadio2" value="0" @if(Auth::guard('admin')->user()->lead_assign_status == 0) checked @endif />
                                        <label class="form-check-label" for="inlineRadio2">Assign Lead Inactive</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="button" class="btn btn-danger btn-sm deletebtn" id="disbtn">Delete</button> --}}
                        <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Delete Todo End -->

    <!-- Check Mobile Number Modal Start -->
    <div class="modal fade" id="checkMobileModal" tabindex="-1" aria-labelledby="checkMobileModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">

        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="checkMobileModalLabel">Check Mobile Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <!-- Input -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Enter Mobile Number</label>
                            <input type="text" id="check-mobile-input" class="form-control"
                                placeholder="Type mobile number...">
                            <small class="text-danger" id="mobile-check-error" style="display:none;"></small>
                        </div>

                        <!-- Result Table -->
                        <div class="col-md-12">
                            <table class="table table-bordered" id="mobileResultTable" style="display:none;">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Assign to</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>

    </div>
    <!-- Check Mobile Number Modal End -->

    <!-- Deactive Lead Assignee Start -->
    <div class="modal fade" id="deactiveleadassign" aria-hidden="true" aria-labelledby="deactiveleadassignLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="offcanvas-title" id="deactiveleadassignLabel">Active Lead Assign</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.leads.deactiveassignee') }}" id="deactiveleadassignValidation" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="update-lead-assign-status">Active Assignee <span class="text-danger">*</span></label>
                                    <select name="lead_assign_status[]" multiple id="update-lead-assign-status" data-placeholder="Select Assignee" class="form-select select2">
                                       @foreach ($leadassignees as $leadassignee)
                                            <option value="{{ $leadassignee->id }}"
                                                {{ in_array($leadassignee->id, $active_lead_assignee) ? 'selected' : '' }}>
                                                {{ $leadassignee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="button" class="btn btn-danger btn-sm deletebtn" id="disbtn">Delete</button> --}}
                        <button type="submit" class="btn btn-success btn-sm" id="disbtn">save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Deactive Lead Assignee End -->

    <!-- Assign Lead to Staff Start -->
    <div class="modal fade" id="assignlead" aria-hidden="true" aria-labelledby="assignleadLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="offcanvas-title" id="updateLstageLabel">Assign Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.leads.assignto') }}" id="assignleadForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="lead_id" id="leadID3">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="add-assign-staff" class="form-label">Assign To <span class="text-danger">*</span></label>
                                    <select name="leadassign_id" id="add-assign-staff" class="form-select select2" data-allow-clear="true" data-placeholder="Select Staff">
                                        <option value="">Select</option>
                                        @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="mark_as_reassigned" name="mark_as_reassigned" value="1" checked>
                                    <label class="form-check-label" for="mark_as_reassigned">Mark as Reassigned</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="button" class="btn btn-danger btn-sm deletebtn" id="disbtn">Delete</button> --}}
                        <button type="submit" class="btn btn-success btn-sm" id="disbtn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Assign Lead to Staff End -->

    <!-- Bulk Change Priority Start -->
    <div class="modal fade" id="bulkdelete" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel125">Bulk Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.leads.bulkdelete') }}" method="POST" id="bulkdeleteValidation">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="bulklead_id" id="bulkdelete_id">
                                <div class="mb-3">
                                    <p class="text-danger">Are you sure to delete lead?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" id="bulk-delete" class="btn btn-sm btn-primary">Delete</button> --}}
                        <button type="submit" id="bulk-delete" class="btn btn-sm btn-primary">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Bulk Change Priority End -->

    <!-- Bulk Assign TO Start -->
    <div class="modal fade" id="bulkassignto" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel125">Bulk Assign To</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.leads.bulkassignto') }}" method="POST" id="bulkassigntoValidation">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="bulklead_id" id="bulkassignto_id">
                                <div class="mb-3">
                                    <label for="bulk-assignto-staff" class="form-label">Assign To <span class="text-danger">*</span></label>
                                    <select name="leadassign_id" id="bulk-assignto-staff" class="form-select select2" data-allow-clear="true" data-placeholder="Select Assign To">
                                        <option value="">Select</option>
                                        @foreach ($staffs as $staff2)
                                        <option value="{{ $staff2->id }}">{{ $staff2->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="mark_as_reassigned_bulk" name="mark_as_reassigned" value="1" checked>
                                    <label class="form-check-label" for="mark_as_reassigned_bulk">Mark as Reassigned</label>
                                </div>
                            </div>

                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" id="bulk-delete" class="btn btn-sm btn-primary">Delete</button> --}}
                        <button type="submit" id="bulk-delete" class="btn btn-sm btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Bulk Assign TO End -->

    <!-- Bulk Export Start -->
    <div class="modal fade" id="bulkexport" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel125">Export Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.leads.bulkexport') }}" method="POST" id="bulkassigntoValidation">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="bulklead_id" id="bulkexport_id">

                        <div id="Leadcolumns" class="row g-2">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="bulk-export" class="btn btn-sm btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Bulk Export End -->

    <!-- Bulk Send To Meta Start -->
    <div class="modal fade" id="bulkSendToMeta" tabindex="-1"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Update To Meta</h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.leads.bulkLeadSendToMeta') }}"
                    method="POST" id="bulkSendToMetaForm">
                    @csrf

                    <div class="modal-body">

                        <input type="hidden" name="bulklead_id" id="bulkLeadSendToMeta_id">


                         <div class="mb-3">
                            <label class="form-label fw-semibold">Select Qualified Status</label>
                              <select name="is_bulk_qualified" class="form-select" required="">
                                <option value="">Select</option>
                                <option value="3">Lead Qualified</option>
                                <option value="4">Lead Not Qualified</option>
                            </select>
                        </div>
                     

                        <!-- Confirmation Textarea -->
                        <div class="mb-3">
                            <label for="meta_confirm_text" class="form-label fw-semibold">
                                Are you sure?
                            </label>
                            <textarea
                                class="form-control"
                                id="meta_confirm_text"
                                name="confirm_text"
                                rows="3"
                                placeholder="Type your confirmation or note before sending to Meta..." required></textarea>
                        </div>

                        <small class="text-danger d-block mb-2">
                            After clicking <strong>Send to Meta</strong>, the selected lead status will be sent to Meta.
                        </small>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="btnbulkLeadSendToMeta_id"
                            class="btn btn-sm btn-primary">
                            Send To Meta
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <!-- Bulk Send To Meta End -->

    <!-- View Lead Modal Start -->
    <div class="modal fade" id="viewLeadModal" tabindex="-1" aria-labelledby="viewLeadModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="viewLeadModalLabel">View Lead Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    <!-- Tabs Header -->
                    <div class="card mb-3" style="box-shadow:none;">
                        <div class="card-header">
                            <ul class="nav nav-tabs nav-fill" role="tablist">   

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-lead" data-bs-toggle="tab" data-bs-target="#lead-tab" type="button" role="tab" aria-controls="lead-tab" aria-selected="true">
                                        <i class="tf-icons ti ti-home ti-xs me-1"></i> Lead
                                    </button>
                                </li> 

                                <!-- Tab 1 -->
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-lead-details" data-bs-toggle="tab" data-bs-target="#lead-details" type="button" role="tab" aria-controls="lead-details" aria-selected="true">
                                        <i class="tf-icons ti ti-user ti-xs me-1"></i> Details
                                    </button>
                                </li>

                                <!-- Tab 2 -->
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-lead-location" data-bs-toggle="tab" data-bs-target="#lead-location" type="button" role="tab" aria-controls="lead-location" aria-selected="false">
                                        <i class="tf-icons ti ti-map-pin ti-xs me-1"></i> Location
                                    </button>
                                </li>

                                <!-- Tab 2 -->
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-lead-activity" data-bs-toggle="tab" data-bs-target="#lead-activity" type="button" role="tab" aria-controls="lead-activity" aria-selected="false">
                                        <i class="tf-icons ti ti-history ti-xs me-1"></i> Activity
                                    </button>
                                </li>   
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content p-1">

                                <!-- Lead Tab -->
                                <div class="tab-pane fade" id="lead-tab" role="tabpanel" aria-labelledby="tab-lead">
                                    <div class="row">
                                        <input type="hidden" name="lead_id" id="notes_lead_id">

                                        <!-- ========================================================= -->
                                        <!-- LEFT SIDE -->
                                        <!-- ========================================================= -->

                                        <div class="col-lg-5">

                                            <!-- ================= Lead Information ================= -->

                                            <div class="card mb-1">

                                                <div class="card-header pt-3 pb-3">
                                                    <h5 class="mb-0">
                                                        <i class="ti ti-user me-2 text-primary"></i>
                                                        Lead Information
                                                    </h5>
                                                </div>

                                                <div class="card-body pb-3">

                                                    <form id="ld_leadDetailForm">

                                                        <input type="hidden"
                                                            id="ld_lead_id"
                                                            name="ld_lead_id">

                                                        <div class="row">

                                                            <div class="col-md-12 mb-2">

                                                                <label class="form-label">
                                                                    Name
                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    id="ld_lead_name"
                                                                    name="ld_lead_name">

                                                            </div>

                                                            <div class="col-md-12 mb-2">

                                                                <label class="form-label">
                                                                    Job Title
                                                                </label>

                                                                <select
                                                                    class="form-select select2"
                                                                    id="ld_job_title"
                                                                    name="ld_job_title">

                                                                    <option value="Return House Driver">
                                                                        Return House Driver
                                                                    </option>

                                                                    <option value="House Driver">
                                                                        House Driver
                                                                    </option>

                                                                    <option value="Private Driver">
                                                                        Private Driver
                                                                    </option>

                                                                    <option value="Other">
                                                                        Other Job
                                                                    </option>

                                                                </select>

                                                            </div>

                                                            <div class="col-md-12 mb-2 other_job_title_container d-none">

                                                                <label class="form-label">
                                                                    Job Title Other
                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    id="ld_job_title_other"
                                                                    name="ld_job_title_other">

                                                            </div>

                                                            <div class="col-md-6 mb-2">

                                                                <label class="form-label">
                                                                    Experience
                                                                </label>

                                                                <select
                                                                    class="form-select select2"
                                                                    id="ld_experience"
                                                                    name="ld_experience">
                                                                        <option value="INDIA भारत">INDIA भारत</option>
                                                                        <option value="SAUDI سऊदी">SAUDI سऊदी</option>
                                                                        <option value="KUWAIT कुवैत">KUWAIT कुवैत</option>
                                                                        <option value="DUBAI दुबई">DUBAI दुबई</option>
                                                                        <option value="QATAR कतर">QATAR कतर</option>
                                                                        <option value="UAE दुबई">UAE दुबई</option>
                                                                        <option value="OMAN ओमान">OMAN ओमान</option>
                                                                        <option value="OTHER अन्य">OTHER अन्य</option>
                                                                </select>

                                                            </div>

                                                            <div class="col-md-6 mb-2">

                                                                <label class="form-label">
                                                                    Expected Days
                                                                </label>

                                                                <select
                                                                    class="form-select select2"
                                                                    id="ld_expected_days"
                                                                    name="ld_expected_days">

                                                                    <option>15 Days</option>
                                                                    <option>30 Days</option>
                                                                    <option>45 Days</option>
                                                                    <option>60 Days</option>

                                                                </select>

                                                            </div>

                                                            <div class="col-md-12 mb-2">

                                                                <label class="form-label">
                                                                    Driving Licence
                                                                </label>

                                                                <select
                                                                    class="form-select select2"
                                                                    multiple
                                                                    id="ld_driving_licence"
                                                                    name="ld_driving_licence[]">

                                                                   <option value="india_license">Indian License</option>
                                                                    <option value="saudi_license">Saudi License</option>

                                                                </select>

                                                            </div>

                                                            <div class="col-md-12 mb-2">

                                                                <label class="form-label">
                                                                    Applying for this job?
                                                                </label>

                                                                <select
                                                                    class="form-select select2"
                                                                    id="ld_looking_for"
                                                                    name="ld_looking_for">

                                                                    <option value="Self">Self</option>
                                                                    <option value="Brother">Brother</option>
                                                                    <option value="Friend">Friend</option>
                                                                    <option value="Relative">Relative</option>
                                                                    <option value="Other">Other</option>

                                                                </select>

                                                            </div>

                                                        </div>

                                                        <div class="text-end">

                                                            <button
                                                                type="submit"
                                                                class="btn btn-sm btn-primary">

                                                                <i class="ti ti-device-floppy me-1"></i>

                                                                Update

                                                            </button>

                                                        </div>

                                                    </form>

                                                </div>

                                            </div>

                                        </div>

                                        <!-- ========================================================= -->
                                        <!-- RIGHT SIDE -->
                                        <!-- ========================================================= -->

                                        <div class="col-lg-7">

                                            <div class="card">

                                                <div class="card-header pt-3 pb-3">

                                                    <h5 class="mb-0">

                                                        <i class="ti ti-list-check me-2 text-success"></i>

                                                        Qualification

                                                    </h5>

                                                </div>

                                                <div class="card-body">

                                                    <form
                                                        action="{{ route('admin.leads.qualified') }}"
                                                        method="POST"
                                                        id="ld_qualifiedLeadForm">

                                                        @csrf

                                                        <input
                                                            type="hidden"
                                                            id="ld_leadIDQualified"
                                                            name="lead_id">

                                                        <div class="row">

                                                            <!-- Qualified Status -->
                                                            <div class="col-md-6 mb-2">

                                                                <label class="form-label">
                                                                    Is this lead qualified?
                                                                    <span class="text-danger">*</span>
                                                                </label>

                                                                @php
                                                                    $roles = json_decode(auth()->user()->role, true) ?? [];
                                                                @endphp



                                                                <select
                                                                    class="form-select"
                                                                    id="ld_qualified_status"
                                                                    name="is_qualified"
                                                                    required>

                                                                    <option value="">Select</option>
                                                                    <option value="1">Followed Up</option>
                                                                    <option value="2">Call Not Connected</option>
                                                                    <option value="5">Lead Not Relevant</option>
                                                                    <option value="3">Lead Qualified</option>
                                                                    @if(in_array('Team Head', $roles))
                                                                        <option value="4">Lead Not Qualified</option>
                                                                    @endif
                                                                   

                                                                </select>

                                                            </div>

                                                            <!-- Add Pipeline -->
                                                            <div class="col-md-6 d-flex align-items-center">

                                                                <div class="form-check mt-4 AddToPipelineCheckbox">

                                                                    <input
                                                                        class="form-check-input"
                                                                        type="checkbox"
                                                                        id="ld_add_to_pipeline"
                                                                        name="add_to_pipeline"
                                                                        value="1"
                                                                        checked>

                                                                    <label
                                                                        class="form-check-label"
                                                                        for="ld_add_to_pipeline">

                                                                        Add to Deal Pipeline

                                                                    </label>

                                                                </div>

                                                            </div>

                                                        </div>

                                                        <div class="mb-2 mt-2 d-none call_not_connected_type_div">

                                                            <label class="form-label d-block mb-2">
                                                                Connection Type
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input"
                                                                    type="radio"
                                                                    name="call_not_connected_type"
                                                                    value="Not Answer"
                                                                    id="ld_cnc_not_answer">

                                                                <label class="form-check-label"
                                                                    for="ld_cnc_not_answer">
                                                                    Not Answer
                                                                </label>
                                                            </div>

                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input"
                                                                    type="radio"
                                                                    name="call_not_connected_type"
                                                                    value="Busy"
                                                                    id="ld_cnc_busy">

                                                                <label class="form-check-label"
                                                                    for="ld_cnc_busy">
                                                                    Busy
                                                                </label>
                                                            </div>

                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input"
                                                                    type="radio"
                                                                    name="call_not_connected_type"
                                                                    value="Not Reachable"
                                                                    id="ld_cnc_not_reachable">

                                                                <label class="form-check-label"
                                                                    for="ld_cnc_not_reachable">
                                                                    Not Reachable
                                                                </label>
                                                            </div>

                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input"
                                                                    type="radio"
                                                                    name="call_not_connected_type"
                                                                    value="Not Available"
                                                                    id="ld_cnc_not_available">

                                                                <label class="form-check-label"
                                                                    for="ld_cnc_not_available">
                                                                    Not Available
                                                                </label>
                                                            </div>

                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input"
                                                                    type="radio"
                                                                    name="call_not_connected_type"
                                                                    value="Wrong Number"
                                                                    id="ld_cnc_wrong_number">

                                                                <label class="form-check-label"
                                                                    for="ld_cnc_wrong_number">
                                                                    Wrong Number
                                                                </label>
                                                            </div>

                                                        </div>

                                                        <div id="ld_reason_section">

                                                            <div class="mb-2">

                                                                <label class="form-label">
                                                                    Comment
                                                                    <span class="text-danger comment_reqired_star">*</span>
                                                                </label>

                                                                <textarea
                                                                    class="form-control"
                                                                    rows="4"
                                                                    id="ld_qualified_reason"
                                                                    name="qualified_reason"
                                                                    placeholder="Enter comment..."
                                                                    required></textarea>

                                                            </div>

                                                            <div class="mb-2" id="conversation_type_div">

                                                                <label class="form-label d-block mb-2">
                                                                    Conversation Type
                                                                </label>

                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input"
                                                                        type="radio"
                                                                        name="conversation_type"
                                                                        value="Normal"
                                                                        id="ld_conversation_normal"
                                                                        required>

                                                                    <label class="form-check-label"
                                                                        for="ld_conversation_normal">
                                                                        Normal
                                                                    </label>
                                                                </div>

                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input"
                                                                        type="radio"
                                                                        name="conversation_type"
                                                                        value="Call"
                                                                        id="ld_conversation_call">

                                                                    <label class="form-check-label"
                                                                        for="ld_conversation_call">
                                                                        Call
                                                                    </label>
                                                                </div>

                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input"
                                                                        type="radio"
                                                                        name="conversation_type"
                                                                        value="Whatsapp"
                                                                        id="ld_conversation_whatsapp">

                                                                    <label class="form-check-label"
                                                                        for="ld_conversation_whatsapp">
                                                                        WhatsApp
                                                                    </label>
                                                                </div>

                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input"
                                                                        type="radio"
                                                                        name="conversation_type"
                                                                        value="Email"
                                                                        id="ld_conversation_email">

                                                                    <label class="form-check-label"
                                                                        for="ld_conversation_email">
                                                                        Email
                                                                    </label>
                                                                </div>

                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input"
                                                                        type="radio"
                                                                        name="conversation_type"
                                                                        value="In-person"
                                                                        id="ld_conversation_inperson">

                                                                    <label class="form-check-label"
                                                                        for="ld_conversation_inperson">
                                                                        In Person
                                                                    </label>
                                                                </div>

                                                            </div>

                                                            <div class="text-end">

                                                                <button
                                                                    type="submit"
                                                                    class="btn btn-sm btn-success">

                                                                    <i class="ti ti-send me-1"></i>

                                                                    Submit

                                                                </button>

                                                            </div>

                                                        </div>

                                                    </form>

                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card">
                                                <div class="card-header py-3">
                                                    <h5 class="mb-0">
                                                        <i class="ti ti-map-pin me-2 text-success"></i>
                                                        Contact Information
                                                    </h5>
                                                </div>

                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-2 mb-3">
                                                            <div class="text-muted small">Mobile</div>
                                                            <div class="fw-medium" id="ld_location_section_mob_no"></div>
                                                        </div>

                                                        <div class="col-md-2 mb-3">
                                                            <div class="text-muted small">WhatsApp</div>
                                                            <div class="fw-medium" id="ld_location_section_whats_no"></div>
                                                        </div>

                                                        <div class="col-md-2 mb-3">
                                                            <div class="text-muted small">Country</div>
                                                            <div class="fw-medium" id="ld_location_section_country"></div>
                                                        </div>

                                                        <div class="col-md-2 mb-3">
                                                            <div class="text-muted small">Region</div>
                                                            <div class="fw-medium" id="ld_location_section_region"></div>
                                                        </div>

                                                        <div class="col-md-2 mb-3">
                                                            <div class="text-muted small">City</div>
                                                            <div class="fw-medium" id="ld_location_section_city"></div>
                                                        </div>

                                                        <div class="col-md-2 mb-0">
                                                            <div class="text-muted small">Submitted From</div>
                                                            <div class="fw-medium" id="ld_location_section_submitted_from"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <!-- ================= Notes History ================= -->
                                            <div class="card mt-0">
                                                <div class="card-header pt-3 pb-0">
                                                    <h5 class="mb-0">
                                                        <i class="ti ti-notes me-2 text-warning"></i>
                                                        Notes History
                                                    </h5>
                                                </div>

                                                <div class="table-responsive notes-table-scroll">
                                                    <table class="table table-hover align-middle mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th width="20%">Notes</th>
                                                                <th width="15%">Lead Type</th>
                                                                <th width="12%">Conversation Type</th>
                                                                <th width="15%">Created By</th>
                                                                <th width="20%">Created At</th>
                                                                <th width="8%" class="text-center">Action</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody id="ld-lead-notes-tbody-q">
                                                            <tr>
                                                                <td colspan="5" class="text-center text-muted py-4">
                                                                    No Notes Found
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Details Tab -->
                                <div class="tab-pane fade" id="lead-details" role="tabpanel" aria-labelledby="tab-lead-details">
                                    <div id="leadDetailsContent">
                                        <div class="text-center text-muted">Loading details...</div>
                                    </div>
                                </div>

                                <!-- Location Tab -->
                                <div class="tab-pane fade" id="lead-location" role="tabpanel" aria-labelledby="tab-lead-location">
                                    <div id="leadLocationContent">
                                        <div class="text-center text-muted">Loading location...</div>
                                    </div>
                                </div>

                                <!-- Activity Tab -->
                                <div class="tab-pane fade" id="lead-activity" role="tabpanel" aria-labelledby="tab-lead-activity">
                                    <div id="leadActivityContent">
                                        <div class="text-center text-muted">
                                            Loading activity...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    <!-- View Lead Modal End -->

    <!-- Filter Panel Start -->
    <div class="modal fade" id="filterpanel" aria-hidden="true" aria-labelledby="filterpanelLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="offcanvas-title" id="filterpanelLabel">Leads Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">

                        <!-- Assignee Filter -->
                        <div class="col-md-4 mb-3">
                            <select name="by_assignee[]" id="by_assignee" class="selectpicker w-100" multiple
                                data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Assignee">
                                @foreach ($leadassignees as $leadassignee)
                                <option value="{{ $leadassignee->id }}"
                                    @if(isset($saveadminfilter) && in_array($leadassignee->id, $saveadminfilter->by_assignee_array)) selected @endif>
                                    {{ $leadassignee->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Lead Date Filter -->
                        <div class="col-md-4 mb-3">
                            <input name="by_lead_date" type="text" id="by_lead_date" class="form-control bsdatpicket"
                                value="{{ $saveadminfilter->by_lead_date ?? '' }}" placeholder="Lead Created Date...">
                        </div>

                         <!-- Updated Date -->
                        <div class="col-md-4 mb-3">
                            <input type="text" name="by_candidate_updated_date" id="by_candidate_updated_date"
                                class="form-control bsdatpicket"
                                value="{{ $saveadminfilter->by_candidate_updated_date ?? '' }}"
                                placeholder="Web Updated Date...">
                        </div>

                         <!-- Updated Date -->
                        <div class="col-md-4 mb-3">
                            <input type="text" name="staff_updated_at" id="staff_updated_at"
                                class="form-control bsdatpicket"
                                value="{{ $saveadminfilter->staff_updated_at ?? '' }}"
                                placeholder="Staff Updated Date Range...">
                        </div>

                        <!-- Qualified Status Filter -->
                        <div class="col-md-4 mb-3">
                           @php $selectedQualified = isset($saveadminfilter) ? $saveadminfilter->by_is_qualified_array : []; @endphp
                            <select name="by_is_qualified[]" id="by_is_qualified"
                                class="selectpicker w-100"
                                multiple
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Qualified Status">

                                <option value="null" @if(in_array('null', $selectedQualified)) selected @endif>Not yet</option>
                                <option value="1" @if(in_array('1', $selectedQualified)) selected @endif>Followed Up</option>
                                <option value="2" @if(in_array('2', $selectedQualified)) selected @endif>Call Not Connected</option>
                                <option value="3" @if(in_array('3', $selectedQualified)) selected @endif>Lead Qualified</option>
                                <option value="4" @if(in_array('4', $selectedQualified)) selected @endif>Lead Not Qualified</option>
                                <option value="Previous Lead" @if(in_array('Previous Lead', $selectedQualified)) selected @endif>Previous Lead</option>
                                <option value="5" @if(in_array('Lead Not Relevant', $selectedQualified)) selected @endif>Lead Not Relevant</option>
                                <option value="Lead Reassigned" @if(in_array('Lead Reassigned', $selectedQualified)) selected @endif>Lead Reassigned</option>
                            </select>
                        </div>

                        <!-- call not connected reson Filter -->
                        <div class="col-md-4 mb-3">
                            @php
                                $selectedCallType = isset($saveadminfilter)
                                    ? $saveadminfilter->by_call_not_connected_type_array
                                    : [];

                            @endphp

                            <select name="by_call_not_connected_type[]"
                                id="by_call_not_connected_type"
                                class="selectpicker w-100"
                                multiple
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Call Not Connected Type">

                                <option value="Not Answer" @if(in_array('Not Answer', $selectedCallType)) selected @endif>
                                    Not Answer
                                </option>

                                <option value="Busy" @if(in_array('Busy', $selectedCallType)) selected @endif>
                                    Busy
                                </option>

                                <option value="Not Reachable" @if(in_array('Not Reachable', $selectedCallType)) selected @endif>
                                    Not Reachable
                                </option>

                                <option value="Not Available" @if(in_array('Not Available', $selectedCallType)) selected @endif>
                                    Not Available
                                </option>

                                <option value="Wrong Number" @if(in_array('Wrong Number', $selectedCallType)) selected @endif>
                                    Wrong Number
                                </option>

                            </select>
                        </div>


                        <!-- Driving License Filter -->
                        <div class="col-md-4 mb-3">
                            @php $selectedDL = isset($saveadminfilter) ? $saveadminfilter->by_driving_license_array : []; @endphp
                            <select name="by_driving_license[]" id="by_driving_license" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Driving License">
                                <option value="KSA DL" @if(in_array('KSA DL', $selectedDL)) selected @endif>KSA DL</option>
                                <option value="Indian DL" @if(in_array('Indian DL', $selectedDL)) selected @endif>Indian DL</option>
                            </select>
                        </div>

                        <!-- Job Title Filter -->
                        <div class="col-md-4 mb-3">
                            @php $savedJobTitles = isset($saveadminfilter) ? $saveadminfilter->by_job_title_array : []; @endphp
                            <select id="by_job_title" name="by_job_title[]" class="selectpicker w-100" multiple data-actions-box="true"
                                data-live-search="true" data-style="default-btn" title="Select Job Title">
                                @php
                                $predefinedTitles = [
                                'Return House Driver',
                                'House Driver',
                                'Private Driver',
                                'Other'
                                ];
                                $allTitles = array_unique(array_merge($predefinedTitles, $savedJobTitles));
                                @endphp

                                @foreach ($allTitles as $title)
                                <option value="{{ $title }}"
                                    @if(in_array($title, $savedJobTitles)) selected @endif>
                                    {{ $title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Expected Days Filter -->
                        <div class="col-md-4 mb-3">
                            @php $selectedDays = isset($saveadminfilter) ? $saveadminfilter->by_expected_days_array : []; @endphp
                            <select name="by_expected_days[]" id="by_expected_days" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Expected Days">
                                <option value="15 Days" @if(in_array('15 Days', $selectedDays)) selected @endif>15 Days</option>
                                <option value="30 Days" @if(in_array('30 Days', $selectedDays)) selected @endif>30 Days</option>
                                <option value="45 Days" @if(in_array('45 Days', $selectedDays)) selected @endif>45 Days</option>
                                <option value="60 Days" @if(in_array('60 Days', $selectedDays)) selected @endif>60 Days</option>
                            </select>
                        </div>

                        <!-- Expected Country Filter -->
                        <div class="col-md-4 mb-3">
                            @php $selectedCountry = isset($saveadminfilter) ? $saveadminfilter->by_expected_country_array : []; @endphp
                            <select name="by_expected_country[]" id="by_expected_country" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Expected Country">
                                <option value="Saudi Arabia सऊदी अरबिया" @if(in_array('Saudi Arabia सऊदी अरबिया', $selectedCountry)) selected @endif>Saudi Arabia</option>
                                <option value="QATAR क़तर" @if(in_array('QATAR क़तर', $selectedCountry)) selected @endif>QATAR</option>
                                <option value="OMAN ओमान" @if(in_array('OMAN ओमान', $selectedCountry)) selected @endif>OMAN</option>
                                <option value="Kuwait कुवैट" @if(in_array('Kuwait कुवैट', $selectedCountry)) selected @endif>Kuwait</option>
                                <option value="Bahrain बहरीन" @if(in_array('Bahrain बहरीन', $selectedCountry)) selected @endif>Bahrain</option>
                                <option value="UAE संयुक्त अरब अमीरात" @if(in_array('UAE संयुक्त अरब अमीरात', $selectedCountry)) selected @endif>UAE</option>
                            </select>
                        </div>

                        <!-- Location Filter -->
                        <div class="col-md-4 mb-3">
                            <input
                                type="text"
                                name="by_location"
                                id="by_location"
                                class="form-control"
                                placeholder="Enter country, state or city (e.g. India, Gujarat, Sidhpur)"
                                value="{{ isset($saveadminfilter) ? $saveadminfilter->by_location : '' }}">
                        </div>


                        <!-- Followup before -->
                        <div class="col-md-4 mb-3">
                            <select name="by_followup_before" id="by_followup_before"
                                class="selectpicker w-100"
                                data-actions-box="true"
                                data-live-search="true"
                                data-style="default-btn"
                                title="Select followup before">
                                <option value="">Select followup before</option>
                                <option value="1" {{ ($saveadminfilter->by_followup_before ?? '') == 1 ? 'selected' : '' }}>Today</option>
                                <option value="3" {{ ($saveadminfilter->by_followup_before ?? '') == 3 ? 'selected' : '' }}>3 Days</option>
                                <option value="7" {{ ($saveadminfilter->by_followup_before ?? '') == 7 ? 'selected' : '' }}>7 Days</option>
                                <option value="14" {{ ($saveadminfilter->by_followup_before ?? '') == 14 ? 'selected' : '' }}>14 Days</option>
                                <option value="30" {{ ($saveadminfilter->by_followup_before ?? '') == 30 ? 'selected' : '' }}>1 Month</option>

                            </select>
                        </div>

                        @php

                        $selectedSources = isset($saveadminfilter->by_submit_from) &&
                        $saveadminfilter->by_submit_from != ''
                        ? explode(',', $saveadminfilter->by_submit_from)
                        : [];

                        @endphp

                        <!-- Submit From Filter -->
                        <div class="col-md-4 mb-3">

                            <select name="by_submit_from[]"
                                id="by_submit_from"
                                class="selectpicker w-100"
                                multiple
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select Submit From">
                                @foreach($sources as $source)

                                <option value="{{$source}}"
                                    @if(in_array($source, $selectedSources)) selected @endif>

                                    {{$source}}

                                </option>

                                @endforeach

                            </select>

                        </div>

                          @php

                            $selectedLookingFor = isset($saveadminfilter->by_looking_for) &&
                            $saveadminfilter->by_looking_for != ''
                            ? explode(',', $saveadminfilter->by_looking_for)
                            : [];

                        @endphp

                        <!-- Looking For Filter -->
                        <div class="col-md-4 mb-3">

                            <select name="by_looking_for[]"
                                id="by_looking_for"
                                class="selectpicker w-100"
                                multiple
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select Looking For">

                                <option value="Self" @if(in_array('Self', $selectedLookingFor ?? [])) selected @endif>Self</option>

                                <option value="Brother" @if(in_array('Brother', $selectedLookingFor ?? [])) selected @endif>Brother</option>

                                <option value="Friend" @if(in_array('Friend', $selectedLookingFor ?? [])) selected @endif>Friend</option>

                                <option value="Relative" @if(in_array('Relative', $selectedLookingFor ?? [])) selected @endif>Relative</option>

                                <option value="Other" @if(in_array('Other', $selectedLookingFor ?? [])) selected @endif>Other</option>

                            </select>

                        </div>
                    </div>

                    @php
                        $customFilterColumns = collect(\Illuminate\Support\Facades\Schema::getColumnListing((new \App\Models\Lead())->getTable()))
                            ->map(fn($col) => ['value' => $col, 'label' => ucwords(str_replace('_', ' ', $col))])
                            ->values();
                    @endphp

                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Custom Filter</strong>
                    </div>
                    <div id="custom_filter_rows"></div>
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add_custom_filter_btn">
                            <i class="ti ti-plus"></i> Add Filter
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning btn-sm resetfilter">Reset Filter</button>
                    <button type="button" class="btn btn-success btn-sm apply_filters">Save Filter</button>
                </div>

            </div>
        </div>
    </div>
    <!-- Filter Panel End -->
    
    {{-- this is for js rendring Start --}}
    <div id="leadToolbarTemplate" class="d-none">

        <div class="custom-toolbar px-3 float-start">

            <!-- Filter Button -->
            <button class="btn btn-xs btn-primary filterpanel"
                    data-bs-toggle="modal"
                    data-bs-target="#filterpanel">
                <i class="ti ti-filter"></i> Filter
                <span class="filter-indicator d-none"></span>
            </button>

            <!-- Option Dropdown -->
            <div class="btn-group mx-2">
                <button class="btn btn-xs btn-primary dropdown-toggle optionBtn"
                        type="button"
                        id="dropdownMenuButton"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                    Option
                </button>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                    {{-- Lead Assign --}}
                    @if (Auth::guard('admin')->user()->lead_assign_status == 1)
                        <button class="dropdown-item text-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#updateUserLeadAssign">
                            Lead Assign
                        </button>
                    @else
                        <button class="dropdown-item text-secondary"
                                data-bs-toggle="modal"
                                data-bs-target="#updateUserLeadAssign">
                            Lead Assign
                        </button>
                    @endif

                    {{-- Active Lead Assignee --}}
                    @if (Auth::guard('admin')->user()->user_type == 1)
                        <button class="dropdown-item text-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#deactiveleadassign">
                            Active Lead Assignee
                        </button>
                    @endif

                    <hr class="dropdown-divider">

                    {{-- Show / Hide Columns --}}
                    <button class="dropdown-item"
                            data-bs-toggle="modal"
                            data-bs-target="#showHideColumnsModal">
                        Show / Hide Columns
                    </button>

                    {{-- Check Mobile --}}
                    <a class="dropdown-item"
                    href="#"
                    data-bs-toggle="modal"
                    data-bs-target="#checkMobileModal">
                        <i class="ti ti-phone ti-xs"></i> Check Mobile
                    </a>

                    {{-- Status Bar --}}
                    <div class="dropdown-item">
                        <label class="switch switch-square">
                            <input type="checkbox" name="status_bar_toggle" value="1" id="lead_status_bar_toggle" class="switch-input" checked />
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

    </div>
  
    <div id="bulkActionTemplate" class="d-none">
        <div class="bulk-action-toolbar me-2">

            <div class="btn-group">
                <button class="btn btn-primary btn-sm dropdown-toggle bulkactions"
                        type="button"
                        disabled
                        data-bs-toggle="dropdown">
                    <i class="ti ti-list-check me-1 ti-xs"></i> Bulk Action
                </button>

                <div class="dropdown-menu">

                    @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1))
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkexport">
                            <i class="ti ti-download"></i> Export
                        </a>
                         <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkassignto">
                            <i class="ti ti-refresh"></i> Assign TO
                        </a>
                         <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkChangeStatus">
                            <i class="ti ti-edit"></i> Change Status
                        </a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkSendToMeta">
                            <i class="ti ti-brand-meta"></i> Update To Meta
                        </a>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkdelete">
                            <i class="ti ti-trash me-2"></i> Delete
                        </a>

                    @elseif (Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0))

                        @if ($permission->leads_bulk_delete == 1)
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkdelete">
                                <i class="ti ti-user me-2"></i> Delete
                            </a>
                        @endif

                        @if ($permission->leads_bulk_assignto == 1)
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkassignto">
                                <i class="ti ti-refresh"></i> Assign TO
                            </a>
                        @endif

                    @endif

                </div>
            </div>

        </div>
    </div>
    {{-- this is for js rendring End --}}

    <!-- Bulk Change Status Start -->
   <div class="modal fade" id="bulkChangeStatus" tabindex="-1"
        aria-hidden="true"
        data-bs-backdrop="static"
        data-bs-keyboard="false">

        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Change Status</h5>
                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('admin.leads.bulkChangeStatus') }}"
                    method="POST">
                    @csrf

                    <div class="modal-body">

                        <input type="hidden"
                            name="bulklead_id"
                            id="bulkChangeStatus_id">

                        <div class="mb-3">

                            <label class="form-label">
                                Is this lead qualified?
                                <span class="text-danger">*</span>
                            </label>

                            <select class="form-select"
                                id="bulk_qualified_status"
                                name="is_qualified"
                                required>

                                <option value="">Select</option>
                                <option value="1">Followed Up</option>
                                <option value="2">Call Not Connected</option>
                                <option value="3">Lead Qualified</option>
                                <option value="4">Lead Not Qualified</option>

                            </select>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-secondary btn-sm"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit"
                            class="btn btn-success btn-sm">
                            Update Status
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <!-- Bulk Change Status End -->

</div>
@endsection

@section('page-script')
{{-- Only vendor scripts this page actually calls are loaded — cleave.js/
     cleave-phone.js, jquery.validate, flatpickr, plain bootstrap-datepicker,
     tagify, typeahead.js and pickr have no init call anywhere on this page
     (or in leads-validation.js) and were removed. moment.js stays: it's a
     peer dependency of bootstrap-daterangepicker.js below. --}}
<script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
<script src="{{ asset('admin/assets/pages/validation/leads-validation.js') }}"></script>

<script>

    // Escapes text before it is inserted via .html()/template literals.
    // Lead fields (name, mobile, notes, ...) can originate from the public,
    // unauthenticated lead capture form or from other staff, so they must
    // never be treated as trusted HTML.
    function escapeHtml(value) {
        if (value === null || value === undefined) return '';
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Initialize DataTable
    let leadTable;
    $(function() {

        // Company column permission
        const showCompanyColumn =
            @json(
                Auth::guard('admin')->user()->user_type == 1 ||
                (isset($permission) && $permission->full_access == 1) ||
                (isset($permission) && $permission->leads_company_column == 1)
            );

        // Build DataTable columns
        let columns = [
            {
                data: 'checkbox',
                name: 'checkbox',
                searchable: false,
                orderable: false
            },
            {
                data: 'name',
                name: 'cand_name'
            }
        ];

        // Company column (Conditional)
        if (showCompanyColumn) {
            columns.push({
                data: 'company',
                name: 'company_name'
            });
        }

        // Remaining columns
        columns.push(
            {
                data: 'mobile',
                name: 'mob_no'
            },
            {
                data: 'whatsapp',
                name: 'whatsapp_no'
            },
            {
                data: 'job_title',
                name: 'required_service'
            },
            {
                data: 'experience',
                name: 'experience'
            },
            {
                data: 'driving_license',
                name: 'driving_license'
            },
            {
                data: 'country',
                name: 'country'
            },
            {
                data: 'expected_days',
                name: 'expected_days'
            },
            {
                data: 'message',
                name: 'message'
            },
            {
                data: 'lead_date',
                name: 'lead_date'
            },
            {
                data: 'assignee',
                name: 'leadassign_id'
            },
            {
                data: 'qualified',
                name: 'is_qualified'
            },
            // {
            //     data: 'status',
            //     name: 'status'
            // },
            {
                data: 'actions',
                name: 'actions',
                searchable: false,
                orderable: false
            }
        );

        leadTable = $('.datatables-leads').DataTable({

            processing: false,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ordering: false,
            destroy: true,
            pageLength: 10,

            // Debounce the built-in global search box: without this, every
            // keystroke fires a full server-side search request (a LIKE
            // %term% scan across several columns, one of the heavier queries
            // this page runs). 500ms only sends the request once typing pauses.
            searchDelay: 500,

            lengthMenu: [
                [10, 20, 50, 500, 1000, 2000],
                [10, 20, 50, 500, 1000, 2000]
            ],

            ajax: {
                url: "{{ route('admin.leads.json') }}",
                type: "GET",
                data: function (d) {
                    d.by_assignee = $('#by_assignee').val();
                    d.by_driving_license = $('#by_driving_license').val();
                    d.by_job_title = $('#by_job_title').val();
                    d.by_expected_days = $('#by_expected_days').val();
                    d.by_expected_country = $('#by_expected_country').val();
                    d.by_submit_from = $('#by_submit_from').val();
                    d.by_looking_for = $('#by_looking_for').val();
                    d.by_is_qualified = $('#by_is_qualified').val();
                    d.by_call_not_connected_type = $('#by_call_not_connected_type').val();
                    d.by_lead_date = $('#by_lead_date').val();
                    d.by_candidate_updated_date = $('#by_candidate_updated_date').val();
                    d.staff_updated_at = $('#staff_updated_at').val();
                    d.by_followup_before = $('#by_followup_before').val();
                    d.by_location = $('#by_location').val();
                    d.custom_filters = JSON.stringify(collectCustomFilters());
                }
            },

            columns: columns,

            initComplete: function () {

                initializeColumnToggles();

                // Remove "Show" and "entries"
                $('#leadTable_length label').contents().filter(function () {
                    return this.nodeType === 3;
                }).remove();

                const $length = $('#leadTable_length');

                // Already exists?
                if (!$length.find('.custom-toolbar').length) {
                    $length.append($('#leadToolbarTemplate').html());
                }

                // Fix layout
                $length.css({
                    display: 'flex',
                    'flex-direction': 'row',
                    'align-items': 'center',
                    'flex-wrap': 'wrap',
                    gap: '10px'
                });

                // Label first
                $length.find('label').css({
                    order: 1,
                    margin: 0
                });

                // Toolbar second
                $length.find('.custom-toolbar').css({
                    order: 2
                });

                // Add Bulk Action before input
                if (!$('#leadTable_filter .bulk-action-toolbar').length) {
                    $('#leadTable_filter label').after($('#bulkActionTemplate').html());
                }

                // Layout
                $('#leadTable_filter').css({
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'end',
                    gap: '10px'
                });
            }
        });

        leadTable.on('xhr.dt', function (e, settings, json) {

            if (!json || !json.counts) {
                return;
            }

            const counts = json.counts;

            // Lead Status Counts
            $('#qualified_all').text(counts.total ?? 0);
            $('#qualified_previous_lead').text(counts.previous_lead ?? 0);
            $('#qualified_reassigned').text(counts.reassigned ?? 0);

            for (let i = 0; i <= 5; i++) {
                $('#qualified_' + i).text(counts['qualified_' + i] ?? 0);
            }

            // Call Not Connected Type Counts
            $('#cnc_not_answer').text(counts.cnc_not_answer ?? 0);
            $('#cnc_busy').text(counts.cnc_busy ?? 0);
            $('#cnc_not_reachable').text(counts.cnc_not_reachable ?? 0);
            $('#cnc_not_available').text(counts.cnc_not_available ?? 0);
            $('#cnc_wrong_number').text(counts.cnc_wrong_number ?? 0);

        });

    });

    const qualificationStatuses = {
        null: 'Not Yet',
        1: 'Followed Up',
        2: 'Call Not Connected',
        3: 'Lead Qualified',
        4: 'Lead Not Qualified',
        5: 'Lead Not Relevant'
    };
    
    // Update Filter Indicator
    function updateAdminFilterIndicator() {

        let isFiltered =
            ($('#by_assignee').val() && $('#by_assignee').val().length > 0) ||
            $('#by_lead_date').val() ||
            $('#by_candidate_updated_date').val() ||
            $('#by_location').val() ||
            ($('#by_is_qualified').val() && $('#by_is_qualified').val().length > 0) ||
            ($('#by_call_not_connected_type').val() && $('#by_call_not_connected_type').val().length > 0) ||
            ($('#by_driving_license').val() && $('#by_driving_license').val().length > 0) ||
            ($('#by_job_title').val() && $('#by_job_title').val().length > 0) ||
            ($('#by_expected_days').val() && $('#by_expected_days').val().length > 0) ||
            ($('#by_expected_country').val() && $('#by_expected_country').val().length > 0) ||
            ($('#by_submit_from').val() && $('#by_submit_from').val().length > 0) ||
            ($('#by_looking_for').val() && $('#by_looking_for').val().length > 0) ||
            ($('#staff_updated_at').val() && $('#staff_updated_at').val() !== '') ||
            ($('#by_followup_before').val() && $('#by_followup_before').val() !== '') ||
            ($('.custom-filter-row').filter(function () {
                return $(this).find('.custom-filter-column').val();
            }).length > 0);

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

    // Reload Leads List
    function reloadLeadsList() {
        leadTable.ajax.reload(
            null,
            false
        );

        updateAdminFilterIndicator();
    }

    // Custom Filter (Column + Operator + Value rows) — declared top-level (not inside any
    // $(document).ready/$(function(){...}) closure) so it's reachable both from the
    // DataTable ajax.data callback above (script tag 1's own closure) and from the
    // save/reset click handlers further down the page (script tag 2's separate closure),
    // matching this file's existing convention for getLeadFilterData/reloadLeadsList.
    var customFilterColumns = @json($customFilterColumns);
    var customFilterOperatorGroups = [
        { label: 'Text', options: [
            { value: 'LIKE %...%', label: 'Contains' },
            { value: 'LIKE', label: 'Matches' },
            { value: 'NOT LIKE', label: "Doesn't match" },
            { value: 'NOT LIKE %...%', label: "Doesn't contain" }
        ] },
        { label: 'Comparison', options: [
            { value: '=', label: 'Equals' },
            { value: '!=', label: 'Not equal to' }
        ] },
        { label: 'Pattern (Regex)', options: [
            { value: 'REGEXP', label: 'Matches pattern' },
            { value: 'REGEXP ^...$', label: 'Matches pattern exactly' },
            { value: 'NOT REGEXP', label: "Doesn't match pattern" }
        ] },
        { label: 'Empty Checks', options: [
            { value: "= ''", label: 'Is empty' },
            { value: "!= ''", label: 'Is not empty' }
        ] },
        { label: 'List', options: [
            { value: 'IN (...)', label: 'Is any of' },
            { value: 'NOT IN (...)', label: 'Is none of' }
        ] },
        { label: 'Range', options: [
            { value: 'BETWEEN', label: 'Is between' },
            { value: 'NOT BETWEEN', label: 'Is not between' }
        ] }
    ];
    var customFilterPlaceholders = {
        'LIKE %...%': 'e.g. john',
        'LIKE': 'e.g. john%',
        'NOT LIKE': 'e.g. john%',
        'NOT LIKE %...%': 'e.g. john',
        '=': 'e.g. John Doe',
        '!=': 'e.g. John Doe',
        'REGEXP': 'e.g. ^[A-Z]+$',
        'REGEXP ^...$': 'e.g. john',
        'NOT REGEXP': 'e.g. ^[A-Z]+$',
        'IN (...)': 'value1, value2, value3',
        'NOT IN (...)': 'value1, value2, value3',
        'BETWEEN': 'min, max',
        'NOT BETWEEN': 'min, max'
    };

    function customFilterRowTemplate() {
        var $row = $('<div class="row g-2 align-items-center mb-2 custom-filter-row"></div>');

        var $columnCol = $('<div class="col-md-4"></div>');
        var $columnSelect = $('<select class="form-select custom-filter-column"></select>');
        $columnSelect.append($('<option value="">Select Column</option>'));
        customFilterColumns.forEach(function (col) {
            $columnSelect.append($('<option></option>').attr('value', col.value).text(col.label));
        });
        $columnCol.append($columnSelect);

        var $operatorCol = $('<div class="col-md-3"></div>');
        var $operatorSelect = $('<select class="form-select custom-filter-operator"></select>');
        customFilterOperatorGroups.forEach(function (group) {
            var $optgroup = $('<optgroup></optgroup>').attr('label', group.label);
            group.options.forEach(function (op) {
                $optgroup.append($('<option></option>').attr('value', op.value).text(op.label));
            });
            $operatorSelect.append($optgroup);
        });
        $operatorCol.append($operatorSelect);

        var $valueCol = $('<div class="col-md-3"></div>');
        var $valueInput = $('<input type="text" class="form-control custom-filter-value" placeholder="Value">');
        $valueCol.append($valueInput);

        var $actionCol = $('<div class="col-md-2 d-flex gap-1"></div>');
        var $removeBtn = $('<button type="button" class="btn btn-sm btn-outline-danger custom-filter-remove" title="Remove"><i class="ti ti-trash"></i></button>');
        $actionCol.append($removeBtn);

        $row.append($columnCol, $operatorCol, $valueCol, $actionCol);

        return $row;
    }

    // Columns whose values are dates/datetimes (leads table uses *_date / *_at naming
    // consistently — lead_date, created_at, updated_at, staff_updated_at, candidate_updated_at)
    function isCustomFilterDateColumn(column) {
        return /_at$|_date$/i.test(column || '');
    }

    // Tears down any date picker previously attached to the row's value input, so it can
    // be safely re-initialized (or left as a plain text input) after a column/operator change.
    function destroyCustomFilterDatePicker($value) {
        if ($value.data('daterangepicker')) {
            $value.off('apply.daterangepicker cancel.daterangepicker');
            $value.data('daterangepicker').remove();
        }
    }

    function applyCustomFilterOperatorUI($row) {
        var operator = $row.find('.custom-filter-operator').val();
        var column = $row.find('.custom-filter-column').val();
        var $value = $row.find('.custom-filter-value');

        destroyCustomFilterDatePicker($value);

        if (operator === "= ''" || operator === "!= ''") {
            $value.val('').prop('disabled', true).attr('placeholder', '(no value needed)');
            return;
        }

        $value.prop('disabled', false);

        if (isCustomFilterDateColumn(column) && (operator === 'BETWEEN' || operator === 'NOT BETWEEN')) {
            $value.attr('placeholder', 'Select date range...');
            $value.daterangepicker({
                autoUpdateInput: false,
                opens: 'right',
                locale: { cancelLabel: 'Clear' }
            }).on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ', ' + picker.endDate.format('YYYY-MM-DD'));
                reloadLeadsList();
            }).on('cancel.daterangepicker', function () {
                $(this).val('');
                reloadLeadsList();
            });
            return;
        }

        if (isCustomFilterDateColumn(column) && (operator === '=' || operator === '!=')) {
            $value.attr('placeholder', 'Select date...');
            $value.daterangepicker({
                singleDatePicker: true,
                autoUpdateInput: false,
                opens: 'right',
                locale: { cancelLabel: 'Clear' }
            }).on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
                reloadLeadsList();
            }).on('cancel.daterangepicker', function () {
                $(this).val('');
                reloadLeadsList();
            });
            return;
        }

        $value.attr('placeholder', customFilterPlaceholders[operator] || 'Value');
    }

    function addCustomFilterRow(presetColumn, presetOperator, presetValue) {
        var $row = customFilterRowTemplate();

        if (presetColumn) {
            $row.find('.custom-filter-column').val(presetColumn);
        }
        if (presetOperator) {
            $row.find('.custom-filter-operator').val(presetOperator);
        }
        if (presetValue !== undefined && presetValue !== null) {
            $row.find('.custom-filter-value').val(presetValue);
        }

        applyCustomFilterOperatorUI($row);

        $('#custom_filter_rows').append($row);

        $row.find('.custom-filter-column').select2({
            dropdownParent: $row,
            width: '100%',
            placeholder: 'Select Column'
        });

        $row.find('.custom-filter-operator').select2({
            dropdownParent: $row,
            width: '100%',
            minimumResultsForSearch: Infinity
        });

        return $row;
    }

    function collectCustomFilters() {
        var filters = [];

        $('.custom-filter-row').each(function () {
            var $row = $(this);
            var column = $row.find('.custom-filter-column').val();
            var operator = $row.find('.custom-filter-operator').val();
            var value = $row.find('.custom-filter-value').val();

            if (!column || !operator) {
                return;
            }

            if (operator !== "= ''" && operator !== "!= ''" && !value) {
                return;
            }

            filters.push({ column: column, operator: operator, value: value || '' });
        });

        return filters;
    }

    // Initialize Column Toggles
    function initializeColumnToggles() {

        const toggleContainer = $('#columnToggles');

        if (!toggleContainer.length) {
            return;
        }

        toggleContainer.html('');

        leadTable.columns().every(function(index) {

            const column = this;

            const columnName =
                $(column.header()).text().trim();

            if (!columnName) {
                return;
            }

            const stored =
                localStorage.getItem(
                    'lead_col_' + index
                );

            const isVisible =
                stored === null ?
                true :
                stored === 'true';

            column.visible(isVisible);

            toggleContainer.append(`
                <div class="form-check mb-2 col-md-6">

                    <input
                        class="form-check-input toggle-column"
                        type="checkbox"
                        id="lead_col_${index}"
                        data-column="${index}"
                        ${isVisible ? 'checked' : ''}>

                    <label
                        class="form-check-label"
                        for="lead_col_${index}">
                        ${columnName}
                    </label>

                </div>
            `);

        });

        $(document)
            .off('change', '.toggle-column')
            .on('change', '.toggle-column', function() {

                const columnIndex =
                    $(this).data('column');

                const visible =
                    $(this).is(':checked');

                leadTable
                    .column(columnIndex)
                    .visible(visible);

                localStorage.setItem(
                    'lead_col_' + columnIndex,
                    visible
                );

            });

    }

    // Get Lead Filter Data
    function getLeadFilterData() {
        return {
            _token: '{{ csrf_token() }}',
            by_assignee: $('#by_assignee').val(),
            by_lead_date: $('#by_lead_date').val(),
            by_candidate_updated_date: $('#by_candidate_updated_date').val(),
            by_is_qualified: $('#by_is_qualified').val(),
            by_call_not_connected_type: $('#by_call_not_connected_type').val(),
            by_driving_license: $('#by_driving_license').val(),
            by_job_title: $('#by_job_title').val(),
            by_expected_days: $('#by_expected_days').val(),
            by_expected_country: $('#by_expected_country').val(),
            by_location: $('#by_location').val(),
            staff_updated_at: $('#staff_updated_at').val(),
            by_followup_before: $('#by_followup_before').val(),
            by_submit_from: $('#by_submit_from').val(),
            by_looking_for: $('#by_looking_for').val(),
            custom_filters: JSON.stringify(collectCustomFilters()),

        };
    }

    // Toggle Job Title Function
    function toggleJobTitle() {
        let value = $('#q_job_title').val();

        if (value === 'Other') {
            $('.other_job_title_container').removeClass('d-none');
        } else {
            $('.other_job_title_container').addClass('d-none');
            $('#q_job_title_other').val('');
        }
    }

    // Initialize Export Column Toggles
    function initializeColumns() {
        const toggleContainer = $('#Leadcolumns');
        if (!toggleContainer.length) return;

        toggleContainer.html('');

        // 🔥 MUST match backend exportColumnMap()
        const EXPORT_KEY_MAP = {
            1: 'Name',
            100: 'Email',
            3: 'Company',
            4: 'Mobile No',
            5: 'Whatsapp No',
            6: 'Job Title',
            7: 'Experience',
            8: 'Driving License',
            9: 'Country',
            10: 'Expected Days',
            11: 'Message',
            12: 'Date',
            13: 'Assign To',
            14: 'Qualified Status',
            15: 'Status'
        };

        Object.entries(EXPORT_KEY_MAP).forEach(([key, label]) => {
            const stored = localStorage.getItem('lead_col_' + key);
            const isChecked = stored !== 'false';

            toggleContainer.append(`
                    <div class="form-check mb-2 col-6">
                        <input class="form-check-input toggle-lead-column"
                            type="checkbox"
                            data-column="${key}"
                            ${isChecked ? 'checked' : ''}>
                        <label class="form-check-label">${label}</label>
                    </div>
                `);
        });
    }

    // updateLeadRow - Update a specific lead row in the DataTable after an edit action, without reloading the entire table
    function updateLeadRow(data) {

        const leadId = data.lead_id;

        if (data.name !== undefined) {
            $('#dtx_lead_name_' + leadId).html(data.name);
        }

        if (data.company !== undefined) {
            $('#dtx_company_' + leadId).html(data.company);
        }

        if (data.mobile !== undefined) {
            $('#dtx_mobile_' + leadId)
                .html(data.mobile)
                .attr('data-mob', data.mobile);
        }

        if (data.whatsapp !== undefined) {
            $('#dtx_whatsapp_' + leadId)
                .html(data.whatsapp)
                .attr('data-whatsapp', data.whatsapp);
        }

        if (data.job_title !== undefined) {
            $('#dtx_job_title_' + leadId).html(data.job_title);
        }

        if (data.experience_html !== undefined) {
            $('#dtx_experience_' + leadId).html(data.experience_html);
        }

        if (data.driving_license_html !== undefined) {
            $('#dtx_driving_license_' + leadId).html(data.driving_license_html);
        }

        if (data.country !== undefined) {
            $('#dtx_country_' + leadId).html(data.country);
        }

        if (data.expected_days !== undefined) {
            $('#dtx_expected_days_' + leadId).html(data.expected_days);
        }

        if (data.message !== undefined) {
            const shortMessage =
                data.message.length > 13 ?
                data.message.substring(0, 13) + '..' :
                data.message;

            $('#dtx_message_' + leadId)
                .html(shortMessage)
                .attr('title', data.message);
        }

        if (data.assignee_html !== undefined) {
            $('#dtx_assignee_' + leadId).html(data.assignee_html);
        }

        if (data.qualified_html !== undefined) {
            $('#dtx_qualified_' + leadId).html(data.qualified_html);
        }

        if (data.status !== undefined) {
            $('#dtx_status_' + leadId).html(data.status);
        }

        if (data.lead_date !== undefined) {
            $('#dtx_lead_date_' + leadId).html(data.lead_date);
        }
    }

    function updateNotesTable(leadResponse) {

        const response = leadResponse.lead;

        // ===============================
        // Notes Table Render
        // ===============================
        let notesHtml = '';

        if (response.notes && response.notes.length > 0) {

            response.notes.forEach(note => {

                let createdAt = note.created_at ?
                    new Date(note.created_at).toLocaleString('en-IN', {
                        timeZone: 'Asia/Kolkata',
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) :
                    '---';

                    const qualificationStatus = qualificationStatuses[note.is_qualified] ?? '-';

                    const qualificationHtml = `
                        ${escapeHtml(qualificationStatus)}
                        ${
                            note.call_not_connected_type
                                ? `<br><small class="text-muted">${escapeHtml(note.call_not_connected_type)}</small>`
                                : ''
                        }
                    `;

                notesHtml += `
                        <tr>
                            <td>${escapeHtml(note.notes) || '---'}</td>
                            <td>${qualificationHtml}</td>
                            <td>${escapeHtml(note.conversation_type) || '---'}</td>
                            <td>${escapeHtml(note.admin?.name) || '---'}</td>
                            <td>${createdAt}</td>
                            <td>
                                <a href="javascript:void(0);" class="delete-note" data-id="${note.id}"><i class="ti ti-trash ti-sm"></i></a>
                            </td>
                        </tr>
                    `;
            });

        } else {

            notesHtml = `
                    <tr>
                        <td colspan="5" class="text-center">No notes found</td>
                    </tr>
                `;
        }

        $('#lead-notes-tbody-q').html(notesHtml);
        $('#ld-lead-notes-tbody-q').html(notesHtml);

    }

    function loadLeadTab(leadId) {

        $('#ld_leadIDQualified').val(leadId);

        $.ajax({
            url: '/admin/leads/view/' + leadId,
            type: 'GET',
            success: function(response) {

                fillLeadInformation(response);
                fillContactInformation(response);
                fillQualification(response);   
                fillNotes(response);

            },
            error: function() {
                $('#leadDetailsContent').html('<div class="text-danger text-center">Failed to load lead details.</div>');
                $('#leadLocationContent').html('<div class="text-danger text-center">Failed to load location data.</div>');
                $('#leadNotesContent table tbody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load notes</td></tr>');
            }
        });

    }

    function fillLeadInformation(response){
        $('#ld_lead_id').val(response.id);
        $('#ld_lead_name').val(response.cand_name || '---');
        $('#ld_job_title').val(response.required_service || '').trigger('change');
        $('#ld_job_title_other').val(response.other_job_title);
        $('#ld_experience').val(response.experience || '').trigger('change');
        $('#ld_expected_days').val(response.expected_days || '').trigger('change');
        $('#ld_looking_for').val(response.looking_for || '').trigger('change');

        let licences = [];

        // Saudi License
        if (response.saudi_license === 'Yes' || response.saudi_license ===  'yes') {
            licences.push('saudi_license');
        }

        // India License
        if (response.india_license === 'Yes' || response.india_license ===  'yes') {
            licences.push('india_license');
        }

        // Set into Select2
        $('#ld_driving_licence').val(licences).trigger('change');
    }

    function fillContactInformation(response){ 
            
        // ===============================
        // Parse Location JSON
        // ===============================
        let locationData = {};
        try {
            locationData = response.submit_lead_from ? JSON.parse(response.submit_lead_from) : {};
        } catch (e) {
            locationData = {};
        }
        
        $('#ld_location_section_submitted_from').text(locationData.from ?? '---');
        $('#ld_location_section_country').text(locationData.country ?? '---');
        $('#ld_location_section_region').text(locationData.region ?? '---');
        $('#ld_location_section_city').text(locationData.city ?? '---');
        $('#ld_location_section_ip_address').text(locationData.ip ?? '---');
        $('#ld_location_section_mob_no').text(response.mob_no ?? '');
        $('#ld_location_section_whats_no').text(response.whatsapp_no ?? '');
    }

    function fillQualification(response){
        
        $('#qualified_status').val(response.is_qualified).trigger('change');

        if (response.is_qualified == 3) {
            $('.AddToPipelineCheckbox').show();
        } else {
            $('.AddToPipelineCheckbox').hide();
        }

    }

    function fillNotes(response) {
        console.log(response);
        
        const notes = response.notes || [];
        let html = '';

        if (notes.length) {

            notes.forEach(note => {

                const createdAt = note.created_at
                    ? new Date(note.created_at).toLocaleString('en-IN', {
                        timeZone: 'Asia/Kolkata',
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })
                    : '---';

                const qualificationStatus = qualificationStatuses[note.is_qualified] ?? '-';

                const qualificationHtml = `
                    ${escapeHtml(qualificationStatus)}
                    ${
                        note.call_not_connected_type
                            ? `<br><small class="text-muted">${escapeHtml(note.call_not_connected_type)}</small>`
                            : ''
                    }
                `;

                html += `
                    <tr>
                        <td>${escapeHtml(note.notes) || '---'}</td>
                        <td>${qualificationHtml}</td>
                        <td>${escapeHtml(note.conversation_type) || '---'}</td>
                        <td>${escapeHtml(note.admin?.name) || '---'}</td>
                        <td>${createdAt}</td>
                        <td class="text-center">
                            <a href="javascript:void(0)"
                            class="delete-note text-danger"
                            data-id="${note.id}"
                            title="Delete Note">
                                <i class="ti ti-trash"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });

        } else {

            html = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="ti ti-notes-off fs-3 d-block mb-2"></i>
                        No Notes Found
                    </td>
                </tr>
            `;
        }

        $('#ld-lead-notes-tbody-q').html(html);
    }

        
</script>

<script>
    $(document).ready(function() {

        // ===============================
        // 1. View Lead Modal
        // ===============================
        $(document).on('click', '.view-lead-btn', function() {

            var leadId = $(this).data('id');   
            
            $('#viewLeadModal').modal('show');
            $('#notes_lead_id').val(leadId);

            // Reset placeholders
            $('#leadDetailsContent').html('<div class="text-center text-muted">Loading...</div>');
            $('#leadLocationContent').html('<div class="text-center text-muted">Loading...</div>');
            $('#leadNotesContent table tbody').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');

            $.ajax({
                url: '/admin/leads/view/' + leadId,
                type: 'GET',

                success: function(response) {

                    // ===============================
                    // Parse Location JSON
                    // ===============================
                    let locationData = {};
                    try {
                        locationData = response.submit_lead_from ? JSON.parse(response.submit_lead_from) : {};
                    } catch (e) {
                        locationData = {};
                    }

                    // ===============================
                    // Format Candidate Updated At
                    // ===============================
                    let candidateUpdatedAt = '---';

                    if (response.candidate_updated_at) {
                        const date = new Date(response.candidate_updated_at);

                        candidateUpdatedAt = date.toLocaleString('en-IN', {
                            timeZone: 'Asia/Kolkata',
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }

                    // ===============================
                    // Format Updated At
                    // ===============================
                    let staffUpdatedAt = '---';

                    if (response.staff_updated_at) {
                        let date = new Date(response.staff_updated_at);

                        staffUpdatedAt = date.toLocaleString('en-IN', {
                            timeZone: 'Asia/Kolkata',
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }


                    let createdAt = '---';

                    if (response.created_at) {
                        let date = new Date(response.created_at);

                        createdAt = date.toLocaleString('en-IN', {
                            timeZone: 'Asia/Kolkata',
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }

                    // ===============================
                    // Lead Details
                    // ===============================
                    let leadHtml = `
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Lead ID:</strong> ${escapeHtml(response.id) || '---'}</p>
                                    <p><strong>Name:</strong> ${escapeHtml(response.cand_name) || '---'}</p>
                                    <p><strong>Email:</strong> ${escapeHtml(response.email) || '---'}</p>
                                    <p><strong>Mobile:</strong> ${escapeHtml(response.mob_no) || '---'}</p>
                                    <p><strong>Country:</strong> ${escapeHtml(response.country) || '---'}</p>
                                    <p><strong>No. of Requirements:</strong> ${escapeHtml(response.no_of_requirement) || '---'}</p>
                                    <p><strong>Applying for this job:</strong> ${escapeHtml(response.looking_for) || '---'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Experience:</strong> ${escapeHtml(response.experience) || '---'}</p>
                                    <p><strong>Saudi License:</strong> ${response.saudi_license === 'yes' ? 'Yes' : 'No'}</p>
                                    <p><strong>Indian License:</strong> ${response.india_license === 'yes' ? 'Yes' : 'No'}</p>
                                    <p><strong>Qualified:</strong> ${response.is_qualified == 1 ? 'Yes' : 'No'}</p>
                                    <p><strong>Created At:</strong> ${createdAt}</p>
                                    <p><strong>Web Updated At:</strong> ${candidateUpdatedAt}</p>
                                    <p><strong>Staff Updated At:</strong> ${staffUpdatedAt}</p>
                                </div>
                            </div>
                            <hr>
                            <p><strong>Reason:</strong> ${escapeHtml(response.qualified_reason) || '---'}</p>
                        `;

                    $('#leadDetailsContent').html(leadHtml);

                    // ===============================
                    // Location Details
                    // ===============================
                    let locationHtml = `
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>Submitted From:</strong> ${escapeHtml(locationData.from) || '---'}</p>
                                    <p><strong>Country:</strong> ${escapeHtml(locationData.country) || '---'}</p>
                                    <p><strong>Region:</strong> ${escapeHtml(locationData.region) || '---'}</p>
                                    <p><strong>City:</strong> ${escapeHtml(locationData.city) || '---'}</p>
                                    <p><strong>IP Address:</strong> ${escapeHtml(locationData.ip) || '---'}</p>
                                </div>
                            </div>
                        `;

                    $('#leadLocationContent').html(locationHtml);

                },

                error: function() {
                    $('#leadDetailsContent').html('<div class="text-danger text-center">Failed to load lead details.</div>');
                    $('#leadLocationContent').html('<div class="text-danger text-center">Failed to load location data.</div>');
                    $('#leadNotesContent table tbody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load notes</td></tr>');
                }
            });

            setTimeout(() => {
                $('#tab-lead').trigger('click');
            }, 300);
        });

        @if($autoOpenLeadId)
            // Global "open this lead" deep link (e.g. from the Dashboard).
            // Reuses the exact same modal-open logic above via a synthetic
            // click on .view-lead-btn — no duplicate modal/fetch logic.
            (function () {
                var $autoOpenTrigger = $('<button type="button" class="view-lead-btn d-none" data-id="{{ $autoOpenLeadId }}"></button>').appendTo('body');
                $autoOpenTrigger.trigger('click');
                $autoOpenTrigger.remove();
            })();
        @endif

        // this is for Lead Tab section all jquery code - start
        $(document).on('click', '#tab-lead', function () {

            var leadId = $('#notes_lead_id').val();

            $('#ld_leadIDQualified').val(leadId);

              $.ajax({
                url: '/admin/leads/view/' + leadId,
                type: 'GET',
                success: function(response) {

                    fillLeadInformation(response);
                    fillContactInformation(response);
                    fillQualification(response);   
                    fillNotes(response);

                },
                error: function() {
                    $('#leadDetailsContent').html('<div class="text-danger text-center">Failed to load lead details.</div>');
                    $('#leadLocationContent').html('<div class="text-danger text-center">Failed to load location data.</div>');
                    $('#leadNotesContent table tbody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load notes</td></tr>');
                }
            });
        });

        $(document).on('submit', '#ld_leadDetailForm', function(e) {
            e.preventDefault();

            if ($('#ld_job_title').val() != 'Other') {
                $('#ld_job_title_other').val('');
            }

            let formData = {
                lead_id: $('#ld_lead_id').val(),
                name: $('#ld_lead_name').val(),
                job_title: $('#ld_job_title').val(),
                job_title_other: $('#ld_job_title_other').val(),
                experience: $('#ld_experience').val(),
                expected_days: $('#ld_expected_days').val(),
                driving_licence: $('#ld_driving_licence').val(),
                looking_for: $('#ld_looking_for').val(),
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: "{{ route('admin.leads.updateLeadDetails') }}",
                type: "POST",
                data: formData,

                success: function(response) {
                    toastr.success(response.message || 'Updated successfully');
                    updateLeadRow(response);
                },

                error: function(xhr) {
                    if (xhr.status === 422) {
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error('Something went wrong');
                    }
                }
            });
        });

        $('#ld_qualified_status').on('change', function () {

            const value = $(this).val();

            // Lead Qualified
            $('.AddToPipelineCheckbox').toggle(value == '3');

            // Call Not Connected
            const showCallType = value == '2';
            
            $('.call_not_connected_type_div').toggleClass('d-none', !showCallType);
            $('input[name="call_not_connected_type"]').prop('required', showCallType);
           

            if(showCallType){
                $('textarea[name="qualified_reason"]').prop('required', false);
                $('.comment_reqired_star').html('');
                $('#conversation_type_div').hide();
                 $('input[name="conversation_type"]').prop('required', false);
                

            }else{
                $('textarea[name="qualified_reason"]').prop('required', true);
                $('.comment_reqired_star').html('*');
                $('#conversation_type_div').show();
                 $('input[name="conversation_type"]').prop('required', true);
            }
            
            if (!showCallType) {
                $('input[name="call_not_connected_type"]').prop('checked', false);
            }

        }).trigger('change');

        $(document).on('submit', '#ld_qualifiedLeadForm', function(e) {
            e.preventDefault();

            let form = $(this);
            let submitBtn = form.find('button[type="submit"]');

            if (submitBtn.prop('disabled')) return;

            submitBtn.prop('disabled', true).html(
                `<span class="spinner-border spinner-border-sm me-1"></span> Processing...`
            );

            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: form.serialize(),
                dataType: "json",

                success: function(response) {

                    if (response.status) {

                        //reset form

                        form[0].reset();
                        $('#ld_qualified_status').trigger('change');

                        toastr.success(response.message, 'Success', {
                            timeOut: 2000
                        });

                        // ✅ Update badge dynamically
                        updateLeadRow(response);
                        updateNotesTable(response);


                    } else {
                        toastr.error(response.message || 'Operation failed');
                    }
                },
                error: function(xhr) {

                    if (xhr.status === 422) {

                        // Laravel validation errors
                        if (xhr.responseJSON.errors) {

                            let errorMessage = '';

                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errorMessage += value[0] + '<br>';
                            });

                            toastr.error(errorMessage, 'Validation Error');

                        }
                        // Custom business validation
                        else if (xhr.responseJSON.message) {

                            toastr.warning(xhr.responseJSON.message, 'Action Not Allowed', {
                                timeOut: 4000,
                                closeButton: true,
                                progressBar: true
                            });

                        } else {

                            toastr.error('Validation failed.');
                        }

                    } else if (xhr.status === 404) {

                        toastr.error(xhr.responseJSON?.message || 'Lead not found.');

                    } else if (xhr.status === 500) {

                        toastr.error(xhr.responseJSON?.message || 'Something went wrong.');

                    } else {

                        toastr.error(xhr.responseJSON?.message || 'Unexpected error occurred.');
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text('Submit');
                }
            });
        });

        // this is for Lead Tab section all jquery code - end

        // ===============================
        // 1.1 Add Note via AJAX
        // ===============================
        $(document).on('submit', '#notesValidation', function(e) {
            e.preventDefault();

            let form = $(this);
            let formData = form.serialize();

            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: formData,

                beforeSend: function() {
                    form.find('button[type="submit"]').prop('disabled', true).text('Saving...');
                },

                success: function(res) {

                    // ✅ Reset form
                    form[0].reset();

                    // ===============================
                    // Append New Note to Table
                    // ===============================
                    let createdAt = res.note.created_at ?
                        new Date(res.note.created_at).toLocaleString('en-IN', {
                            timeZone: 'Asia/Kolkata',
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) :
                        '---';

                    let newRow = `
                            <tr>
                                <td>${escapeHtml(res.note.notes) || '---'}</td>
                                <td>${escapeHtml(res.note.conversation_type) || '---'}</td>
                                <td>${escapeHtml(res.note.admin_name) || '---'}</td>
                                <td>${createdAt}</td>
                                <td>
                                    <a href="javascript:void(0);" class="delete-note" data-id="${res.note.id}"><i class="ti ti-trash ti-sm"></i></a>
                                </td>
                            </tr>
                        `;

                    // ✅ Remove "no data" row if exists
                    $('#lead-notes-tbody tr:first td[colspan]').parent().remove();

                    // ✅ Add new row on top
                    $('#lead-notes-tbody').prepend(newRow);
                },

                error: function(xhr) {
                    toastr.error('Failed to save note');

                    if (xhr.status === 422) {
                        console.log(xhr.responseJSON.errors);
                    }
                },

                complete: function() {
                    form.find('button[type="submit"]').prop('disabled', false).text('Submit');
                }
            });
        });

        // ===============================
        // 1.2 Note via AJAX
        // ===============================
        $(document).on('click', '.delete-note', function() {

            let noteId = $(this).data('id');
            let row = $(this).closest('tr');

            $.ajax({
                url: '/admin/leads/delete-note/' + noteId,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                success: function(res) {

                    if (res.status) {

                        // ✅ Remove row from table
                        row.fadeOut(300, function() {
                            $(this).remove();

                            // ✅ If no rows left
                            if ($('#ld-lead-notes-tbody tr').length === 0) {
                                $('#ld-lead-notes-tbody').html(`
                                        <tr>
                                            <td colspan="5" class="text-center">No notes found</td>
                                        </tr>
                                    `);
                            }
                        });

                    }
                },

                error: function() {
                    toastr.error('Failed to delete note');
                }
            });
        });

        // ===============================
        // 2. Initialize Select2 and Selectpicker
        // ===============================
        $('.select2').each(function() {
            $(this).wrap('<div class="position-relative"></div>').select2({
                dropdownParent: $(this).parent()
            });
        });

        $('.selectpicker').selectpicker();

        // ===============================
        // 3. Date Range Picker
        // ===============================
        $('.bsdatpicket').daterangepicker({
            autoUpdateInput: false,
            opens: 'right',
            locale: {
                cancelLabel: 'Clear'
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            reloadLeadsList();
            updateAdminFilterIndicator();
        }).on('cancel.daterangepicker', function() {
            $(this).val('');
            reloadLeadsList();
            updateAdminFilterIndicator();
        });

        // ===============================
        // 4. Bulk Checkbox Handling
        // ===============================
        $(document).on('click', '.checkboxSelectAll', function() {
            var listcheckitem = $('.listitem :checkbox');
            listcheckitem.prop("checked", $(this).is(":checked"));
            $('.bulkactions').prop("disabled", !listcheckitem.filter(":checked").length);
        });

        $(document).on('change', '.listitem :checkbox', function() {
            var listcheckitem = $('.listitem :checkbox');
            var checkedItems = listcheckitem.filter(":checked").length;
            var masterCheck = $('.checkboxSelectAll');

            if (checkedItems === listcheckitem.length) {
                masterCheck.prop({
                    indeterminate: false,
                    checked: true
                });
            } else if (checkedItems > 0) {
                masterCheck.prop({
                    indeterminate: true,
                    checked: false
                });
            } else {
                masterCheck.prop({
                    indeterminate: false,
                    checked: false
                });
            }

            $('.bulkactions').prop("disabled", !checkedItems);
        });

        // ===============================
        // 5. Switch Input for Assign Status
        // ===============================
        $('.switch-input').not('#lead_status_bar_toggle').on('change', function() {
            var checked_value = this.checked ? 1 : 0;
            $.post("{{ route('admin.lead.userleadassign.update') }}", {
                _token: "{{ csrf_token() }}",
                lead_assign_status: checked_value
            }, function(response) {
                toastr.success(response.resp_message, 'Success', {
                    hideDuration: 3000
                });
            });
        });

        // Status Bar toggle: shows/hides the Deal Stage / Recruit Status
        // Summary Bar. Persisted client-side so the choice survives reloads
        // and keeps working across the AJAX list/count refresh, since that
        // refresh only patches counts in place and never re-renders this
        // bar's container. display is forced with !important (and bound via
        // delegation) so nothing else on this page can silently override it.
         (function () {

            function setLeadStatusBarVisible(visible) {
                var bar = document.getElementById('leadStatusBar');
                if (!bar) return;

                if (visible) {
                    bar.style.removeProperty('display');
                } else {
                    bar.style.setProperty('display', 'none', 'important');
                }
            }

            var stored = null;

            try {
                stored = localStorage.getItem('leads_status_bar_visible');
            } catch (e) {}

            var visible = stored === null ? true : stored === '1';

            // Checkbox checked/unchecked
            $('#lead_status_bar_toggle').prop('checked', visible);

            // Show/hide status bar
            setLeadStatusBarVisible(visible);

            $(document).on('change', '#lead_status_bar_toggle', function () {

                var isVisible = $(this).is(':checked');

                setLeadStatusBarVisible(isVisible);

                try {
                    localStorage.setItem(
                        'leads_status_bar_visible',
                        isVisible ? '1' : '0'
                    );
                } catch (e) {}
            });

        })();

        // ===============================
        // 6. Filter Change Handling
        // ===============================

        $(' #by_assignee, #by_lead_date, #by_candidate_updated_date, #by_is_qualified, #by_call_not_connected_type, #by_driving_license, #by_job_title, #by_expected_days, #by_expected_country, #by_location, #staff_updated_at, #by_followup_before, #by_submit_from, #by_looking_for')
            .on('change input', reloadLeadsList);

        // Custom Filter row add/remove/change wiring
        $('#add_custom_filter_btn').on('click', function () {
            addCustomFilterRow();
        });

        $('#custom_filter_rows').on('click', '.custom-filter-remove', function () {
            var $row = $(this).closest('.custom-filter-row');
            destroyCustomFilterDatePicker($row.find('.custom-filter-value'));
            $row.remove();
            reloadLeadsList();
        });

        $('#custom_filter_rows').on('change', '.custom-filter-column, .custom-filter-operator', function () {
            applyCustomFilterOperatorUI($(this).closest('.custom-filter-row'));
            reloadLeadsList();
        });

        var customFilterValueDebounce = null;
        $('#custom_filter_rows').on('input', '.custom-filter-value', function () {
            clearTimeout(customFilterValueDebounce);
            customFilterValueDebounce = setTimeout(reloadLeadsList, 400);
        });

        @if(isset($saveadminfilter) && !empty($saveadminfilter->custom_filters))
            var savedCustomFilters = @json($saveadminfilter->custom_filters);
            savedCustomFilters.forEach(function (row) {
                addCustomFilterRow(row.column, row.operator, row.value);
            });
            updateAdminFilterIndicator();
        @endif

        // Reset Filter
        $(document).on('click', '.resetfilter', function() {

            // 1️⃣ Reset UI filters
            $('.selectpicker').selectpicker('deselectAll');
            $('#by_lead_date').val('');
            $('#by_candidate_updated_date').val('');
            // $('#by_is_qualified').val('').selectpicker('render');
            $('#by_location').val('');
            $('#staff_updated_at').val('');
            $('#by_followup_before').val('').selectpicker('render');
            $('#by_submit_from').val('').selectpicker('render');
            $('#by_looking_for').val('').selectpicker('render');
            $('#custom_filter_rows').empty();

            // 2️⃣ Save reset state (existing logic)
            $.post('{{ route("admin.leads.saveFilter") }}', getLeadFilterData(), function(res) {
                toastr.success(res.message || 'Filter Reset successfully!', 'Success', {
                    timeOut: 2000
                });
            }).fail(function() {
                toastr.error('Failed to reset filter', 'Error');
            });

            // 3️⃣ Reload list (existing)
            reloadLeadsList();

            updateAdminFilterIndicator();

            // 4️⃣ 🔥 RESET URL (remove all query params)
            if (window.history && window.history.replaceState) {
                const cleanUrl = window.location.origin + window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            }
        });

        // Save Filter
        $(document).on('click', '.apply_filters', function() {
            $.post('{{ route("admin.leads.saveFilter") }}', getLeadFilterData(), function(res) {
                toastr.success(res.message || 'Filter saved successfully!', 'Success', {
                    timeOut: 2000
                });
            }).fail(function() {
                toastr.error('Failed to save filter', 'Error');
            });

            reloadLeadsList();

            updateAdminFilterIndicator();
        });

        // ===============================
        // 7. Bulk Assign/Delete Modals
        // ===============================
        $('#bulkdelete, #bulkassignto, #bulkexport, #bulkSendToMeta, #bulkChangeStatus').on('show.bs.modal', function (e) {

            var allselectedvals = $('.dt-checkboxes:checked').map(function () {
                return $(this).data('id');
            }).get();

            var join_all_selected_values = allselectedvals.join(",");

            if (this.id === 'bulkexport') {
                initializeColumns();
            }

            switch (this.id) {
                case 'bulkdelete':
                    $('#bulkdelete_id').val(join_all_selected_values);
                    break;

                case 'bulkassignto':
                    $('#bulkassignto_id').val(join_all_selected_values);
                    break;

                case 'bulkexport':
                    $('#bulkexport_id').val(join_all_selected_values);
                    break;

                case 'bulkSendToMeta':
                    $('#bulkLeadSendToMeta_id').val(join_all_selected_values);
                    break;

                case 'bulkChangeStatus':
                    $('#bulkChangeStatus_id').val(join_all_selected_values);
                    break;
            }

        });

        // $('#bulkdelete, #bulkassignto, #bulkexport, #bulkSendToMeta').on('show.bs.modal', function(e) {
            //     var allselectedvals = $('.dt-checkboxes:checked').map(function() {
            //         return $(this).data('id');
            //     }).get();
            //     var join_all_selected_values = allselectedvals.join(",");

            //     if (this.id === 'bulkexport') {
            //         initializeColumns();
            //     }

            //     if (this.id === 'bulkdelete') $('#bulkdelete_id').val(join_all_selected_values);
            //     else if (this.id === 'bulkassignto') $('#bulkassignto_id').val(join_all_selected_values);
            //     else if (this.id === 'bulkexport') $('#bulkexport_id').val(join_all_selected_values);
            //     else if (this.id === 'bulkSendToMeta') $('#bulkLeadSendToMeta_id').val(join_all_selected_values);

        // });

        // ===============================
        // 7.1. Bulk Export Modals (UPDATED)
        // ===============================
        $(document).on('click', '#bulk-export', function(e) {
            e.preventDefault();

            let leadIds = $('#bulkexport_id').val();
            let columns = [];

            $('.toggle-lead-column:checked').each(function() {
                columns.push($(this).data('column'));
            });

            if (columns.length === 0) {
                alert('Please select at least one column');
                return;
            }

            let form = $('<form>', {
                method: 'POST',
                action: "{{ route('admin.leads.bulkexport') }}"
            });

            form.append(`<input type="hidden" name="_token" value="{{ csrf_token() }}">`);
            form.append(`<input type="hidden" name="lead_ids" value="${leadIds}">`);

            columns.forEach(col => {
                form.append(`<input type="hidden" name="columns[]" value="${col}">`);
            });

            $('body').append(form);
            form.submit();
            form.remove();

            $('#bulkexport').modal('hide');
        });

        // ✅ Init Select2 (modal support)
        $('#q_job_title').select2({
            width: '100%',
            dropdownParent: $('#viewLeadModal') // modal fix
        });

        // ✅ On change (Select2 compatible)
        $('#q_job_title').on('change', function() {
            toggleJobTitle();
        });

        // ✅ Run on page load (important for edit mode)
        toggleJobTitle();


        $(document).on('submit', '#leadDetailForm', function(e) {
            e.preventDefault();

            if ($('#q_job_title').val() != 'Other') {
                $('#q_job_title_other').val('');
            }

            let formData = {
                lead_id: $('#q_lead_id').val(),
                name: $('#q_lead_name').val(),
                job_title: $('#q_job_title').val(),
                job_title_other: $('#q_job_title_other').val(),
                experience: $('#q_experience').val(),
                expected_days: $('#q_expected_days').val(),
                driving_licence: $('#q_driving_licence').val(),
                looking_for: $('#looking_for').val(),
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: "{{ route('admin.leads.updateLeadDetails') }}",
                type: "POST",
                data: formData,

                success: function(response) {
                    toastr.success(response.message || 'Updated successfully');
                    updateLeadRow(response);
                },

                error: function(xhr) {
                    if (xhr.status === 422) {
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error('Something went wrong');
                    }
                }
            });
        });

        $('#qualified_status').on('change', function() {
            var selectedValue = $(this).val();
            if (selectedValue == '3') {
                $('.AddToPipelineCheckbox').show();
            } else {
                $('.AddToPipelineCheckbox').hide();
            }
        });

        // ===============================
        // 9. Add Lead id 
        // ===============================

        $('#assignlead').on("show.bs.modal", function(e) {
            var leadID = $(e.relatedTarget).data('id');
            $('#leadID3').val(leadID);
            // Mark as Reassigned must default to checked every time the modal opens.
            $('#mark_as_reassigned').prop('checked', true);
        });

        $('#deletelead').on("show.bs.modal", function(e) {
            var leadID = $(e.relatedTarget).data('id');
            $('#leadID2').val(leadID);
        });

        initializeColumnToggles();

        // =====================================================
        // TRIGGER ON CHANGE (Bootstrap Select + Inputs)
        // =====================================================
        $('.selectpicker').on('changed.bs.select', function() {
            updateAdminFilterIndicator();
        });

        $('#by_lead_date, #by_candidate_updated_date, #by_location, #staff_updated_at, #by_followup_before, #by_submit_from, #by_looking_for').on('change keyup', function() {
            updateAdminFilterIndicator();
        });

        // =====================================================
        // INITIAL CHECK (ON PAGE LOAD / SAVED FILTER)
        // =====================================================
        updateAdminFilterIndicator();


        // =======================================
        // update mobile number Check (AJAX)
        // =======================================
        $(document).on('click', '.editContact', function() {

            let id = $(this).data('id');
            let mob = $(this).data('mob');
            let whatsapp = $(this).data('whatsapp');

            $('#contact_id').val(id);
            $('#mob_no').val(mob);
            $('#whatsapp_no').val(whatsapp);

            $('#editContactModal').modal('show');
        });

        // ===========================================
        //  Update Contact Form Submission (AJAX)
        // ===========================================
        $(document).on('submit', '#updateContactForm', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('admin.leads.update.number') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {

                    toastr.success(response.message);

                    updateLeadRow(response);

                    $('#editContactModal').modal('hide');

                },
                error: function(err) {
                    toastr.error('Something went wrong');
                }
            });
        });

    });


    $(document).on('change', '.toggle-lead-column', function() {
        const col = $(this).data('column');
        localStorage.setItem('lead_col_' + col, this.checked);
    });


    // 🔹 Initialize toggles every time modal opens
    $('#showHideColumnsModal').on('shown.bs.modal', function() {
        initializeColumnToggles();
    });


    // ===============================
    // 11. Mobile Number Check (AJAX)
    // ===============================
    $("#check-mobile-input").on("keyup", function() {
        let mobile = $(this).val();
        $("#mobileResultTable").hide();
        $("#mobileResultTable tbody").html("");

        if (mobile.length < 5) return; // Start checking after 5 digits

        $.ajax({
            url: "{{ route('admin.leads.checkMobileDetails') }}",
            method: "POST",
            data: {
                mobile: mobile,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {

                if (!response.status) {
                    $("#mobile-check-error").text(response.message).show();
                    return;
                }

                $("#mobile-check-error").hide();

                let row = `
                        <tr>
                            <td>${response.data.name}</td>
                            <td>${response.data.mobile}</td>
                            <td>${response.data.assign_to}</td>
                            <td>${response.data.created_at}</td>
                        </tr>
                    `;

                $("#mobileResultTable tbody").html(row);
                $("#mobileResultTable").show();
            }
        });
    });

    // ===============================
    // 12. Lead Activity
    // ===============================

    $(document).on('shown.bs.tab', '#tab-lead-activity', function () {
        let currentLeadId = $('#notes_lead_id').val();
        loadLeadActivity(currentLeadId);
    });

    function loadLeadActivity(leadId) {

        $('#leadActivityContent').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2 mb-0">Loading activity...</p>
            </div>
        `);

        $.ajax({
            url: "{{ route('admin.lead.activity', ':id') }}".replace(':id', leadId),
            type: "GET",
            success: function (response) {
                $('#leadActivityContent').html(response);
            },
            error: function () {
                $('#leadActivityContent').html(`
                    <div class="alert alert-danger mb-0">
                        Unable to load activity.
                    </div>
                `);
            }
        });
    }

    $(document).on('click', '.stage-card', function (e) {
        e.preventDefault();

        let $this = $(this);

        if ($this.hasClass('active')) {

            $this.removeClass('active');

            // Clear all selected values
            $('#by_is_qualified')
                .selectpicker('val', [])
                .trigger('change');

        } else {

            $('.stage-card').removeClass('active');
            $this.addClass('active');

            let stage = $this.attr('data-stage');

            if (stage === '0') {
                stage = 'null';
            }

            $('#by_is_qualified')
                .selectpicker('val', [stage]) // pass array
                .trigger('change');
        }
    });

    $(document).on('click', '.cnc-type-card', function (e) {
        e.preventDefault();

        let $this = $(this);

        if ($this.hasClass('active')) {

            $this.removeClass('active');

            // Clear selected values
            $('#by_call_not_connected_type')
                .selectpicker('val', [])
                .trigger('change');

        } else {

            $('.cnc-type-card').removeClass('active');
            $this.addClass('active');

            let type = $this.attr('data-type');

            $('#by_call_not_connected_type')
                .selectpicker('val', [type])
                .trigger('change');
        }
    });


    
</script>


@endsection