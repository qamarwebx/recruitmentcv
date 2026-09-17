@extends('layout.admin.admin_layout')

@section('title','Lead List')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/pickr/pickr-themes.css') }}" />

    <style>
        .pagestyle{
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
        .pagestyle:focus{
            color: #6e6b7b;
            background-color: #fff;
            border-color: #7367f0;
            outline: 0;
            box-shadow: 0 3px 10px 0 rgba(34, 41, 47, 0.1);
        }

        .optionBtn{
            height:26.6px !important;
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


    .lead_conversation_type{
        margin-right: 5px !important;
    }
    

    </style>
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header border-bottom">
                <div class="mb-1 float-start">
                    {{-- <label for="">Show</label> --}}
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
                    <!-- Filter Button -->
                    <button class="btn btn-xs btn-primary filterpanel" data-bs-toggle="modal" data-bs-target="#filterpanel">
                        <i class="ti ti-filter"></i> Filter
                        <span class="filter-indicator d-none"></span>
                    </button>

                    <!-- Option Dropdown -->
                    <div class="btn-group mx-2">
                        <button class="btn btn-xs btn-primary dropdown-toggle bulkactions optionBtn" 
                                type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                            {{-- Lead Assign Button --}}
                            @if (Auth::guard('admin')->user()->lead_assign_status == 1)
                                <button class="dropdown-item text-primary" data-bs-toggle="modal" data-bs-target="#updateUserLeadAssign">
                                    Lead Assign
                                </button>
                            @else
                                <button class="dropdown-item text-secondary" data-bs-toggle="modal" data-bs-target="#updateUserLeadAssign">
                                    Lead Assign
                                </button>
                            @endif

                            {{-- Active Lead Assignee (for admin only) --}}
                            @if (Auth::guard('admin')->user()->user_type == 1)
                                <button class="dropdown-item text-primary" data-bs-toggle="modal" data-bs-target="#deactiveleadassign">
                                    Active Lead Assignee
                                </button>
                            @endif

                            <hr class="dropdown-divider">

                            {{-- Show / Hide Columns --}}
                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#showHideColumnsModal">
                                Show / Hide Columns
                            </button>

                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#checkMobileModal"><i class="ti ti-phone ti-xs"></i> Check Mobile</a>


                        </div>
                    </div>
                </div>

                <div class="float-end">
                    <div class="btn-group mx-2">
                        <button class="btn btn-primary btn-sm dropdown-toggle bulkactions" type="button" disabled id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-list-check me-0 me-sm-1 ti-xs"></i> Bulk Action
                        </button>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                            @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1))
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkdelete"><i class="ti ti-user me-2"></i> Delete</a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkassignto"><i class="ti ti-refresh"></i> Assign TO</a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkexport"><i class="ti ti-download"></i> Export</a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkSendToMeta"><i class="ti ti-brand-facebook"></i> Send To Meta</a>
                            @elseif (Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0))
                                @if ($permission->leads_bulk_delete == 1)
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkdelete"><i class="ti ti-user me-2"></i> Delete</a>
                                @endif

                                @if ($permission->leads_bulk_assignto == 1)
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkassignto"><i class="ti ti-refresh"></i> Assign TO</a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>


                <div class="mb-1 float-end">
                    <input type="text" id="search_text" class="form-control" placeholder="Search...">
                </div>
            </div>
            <div class="card-datatable table-responsive leadpaginate">
                @include('admin.leads.loadlead')
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
        <div class="modal fade" id="showHideColumnsModal" tabindex="-1" aria-labelledby="showHideColumnsModalLabel" aria-hidden="true"  data-bs-backdrop="static" data-bs-keyboard="false">
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
                                        {{-- <p class="text-danger">Are you sure to Assign Lead?</p> --}}

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
                                                <option value="{{ $leadassignee->id }}">{{ $leadassignee->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                            {{-- <button type="button" class="btn btn-danger btn-sm deletebtn" id="disbtn">Delete</button> --}}
                            <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Deactive</button>
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

       <!-- Qualified Lead Modal Start -->
        <div class="modal fade" id="qualifiedlead" aria-hidden="true" aria-labelledby="qualifiedleadLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            
            <!-- ✅ IMPORTANT: modal-lg for 2 column -->
            <div class="modal-dialog modal-xl" role="document">
                
                <div class="modal-content">
                    
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="qualifiedleadLabel">Lead Detail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <!-- 🔵 LEFT SIDE FORM -->
                            <div class="col-md-5 border-end">

                            <form id="leadDetailForm">

                                <input type="hidden" id="q_lead_id" name="q_lead_id">

                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" id="q_lead_name" name="q_lead_name">
                                </div>


                                <div class="mb-3">
                                    <label class="form-label">Job Title</label>
                                    <select class="form-control select2" name="q_job_title" id="q_job_title">
                                        <option value="">Select Job</option>
                                        <option value="Return House Driver">Return House Driver</option>
                                        <option value="House Driver">House Driver</option>
                                        <option value="Private Driver">Private Driver</option>
                                        <option value="Other">Other Job</option>
                                    </select>
                                </div>

                                <div class="mb-3 d-none other_job_title_container">
                                    <label class="form-label">Job Title Other</label>
                                    <input type="text" class="form-control" id="q_job_title_other" name="q_job_title_other" placeholder="Enter custom job title">
                                </div>


                                <div class="mb-3">
                                    <label class="form-label">Experience</label>
                                    <input type="text" class="form-control" id="q_experience" name="q_experience">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Expected Days</label>
                                    <input type="text" class="form-control" id="q_expected_days" name="q_expected_days">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Driving Licence</label>
                                    <select class="form-control select2" name="q_driving_licence[]" multiple id="q_driving_licence">
                                        <option value="india_license">Indian License</option>
                                        <option value="saudi_license">Saudi License</option>
                                    </select>
                                </div>

                                <!-- ✅ Submit Button Right Align -->
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                </div>

                            </form>

                            </div>

                            <!-- 🟢 RIGHT SIDE FORM (YOUR ORIGINAL FORM) -->
                            <div class="col-md-7">

                                <form action="{{ route('admin.leads.qualified') }}" id="qualifiedLeadForm" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="lead_id" id="leadIDQualified">

                                    <div class="mb-3">
                                        <label for="qualified_status" class="form-label">
                                            Is this lead qualified? <span class="text-danger">*</span>
                                        </label>
                                        <select name="is_qualified" id="qualified_status" class="form-select" required>
                                            <option value="">Select</option>
                                            <option value="1">Followed Up</option>
                                            <option value="2">Call Not Connected</option>
                                            <option value="3">Lead Qualified</option>
                                            <option value="4">Lead Not Qualified</option>
                                        </select>
                                    </div>

                                    <div id="reason_section">

                                        <div class="mb-3">
                                            <label for="qualified_reason" class="form-label">
                                                Comment <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="qualified_reason" id="qualified_reason"
                                                class="form-control" rows="3" placeholder="Enter reason..." required></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="conversation_type" value="Normal" id="add-normal-conversation"/>
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

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="conversation_type" value="Email" id="add-email-conversation"/>
                                                <label class="form-check-label" for="add-email-conversation">Email</label>
                                            </div>

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="conversation_type" value="In-person" id="add-in-person-conversation"/>
                                                <label class="form-check-label" for="add-in-person-conversation">In person</label>
                                            </div>
                                        </div>

                                        <div class="form-check mb-3 AddToPipelineCheckbox">
                                            <input class="form-check-input" type="checkbox" value="1" id="add_to_pipeline" name="add_to_pipeline" checked>
                                            <label class="form-check-label" for="add_to_pipeline">
                                                Add to Deal Pipeline
                                            </label>
                                        </div>

                                    </div>

                                    <!-- ✅ Only right form submit -->
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success btn-sm">Submit</button>
                                    </div>

                                </form>

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Qualified Lead Modal End -->

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
                        <h5 class="modal-title">Forward To Meta</h5>
                        <button type="button" class="btn-close"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.leads.bulkLeadSendToMeta') }}"
                        method="POST" id="bulkSendToMetaForm">
                        @csrf

                        <div class="modal-body">

                            <input type="hidden" name="bulklead_id" id="bulkLeadSendToMeta_id">

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

                <div class="modal-body">
                    <!-- Tabs Header -->
                    <div class="card mb-3">
                    <div class="card-header">
                        <ul class="nav nav-tabs nav-fill" role="tablist">

                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-lead-notes" data-bs-toggle="tab" data-bs-target="#lead-notes" type="button" role="tab" aria-controls="lead-notes" aria-selected="true">
                                <i class="tf-icons ti ti-home ti-xs me-1"></i> Notes
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
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content p-3">

                        <!-- Notes Tab -->
                        <div class="tab-pane fade show active" id="lead-notes" role="tabpanel" aria-labelledby="tab-lead-notes">
                            <div id="leadNotesContent">

                                <div class="row notes-div-2">

                                    <!-- ===================== LEFT: ADD NOTE ===================== -->
                                    <div class="col-md-4">

                                        <form action="{{ route('admin.leads.addnotes') }}" method="POST" id="notesValidation">
                                            @csrf

                                            <!-- ✅ IMPORTANT: ADD LEAD ID -->
                                            <input type="hidden" name="lead_id" id="notes_lead_id">

                                            <div class="mb-3">
                                                <label class="form-label">Notes <span class="text-danger">*</span></label>
                                                <textarea name="notes" id="add-notes-tab" rows="5" class="form-control"></textarea>
                                            </div>

                                            <!-- Conversation Type -->
                                            <div class="mb-3 small-radio">

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="conversation_type" value="Normal" id="add-lead-normal-conversation"/>
                                                    <label class="form-check-label" for="add-lead-normal-conversation">Normal</label>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="conversation_type" value="Call" id="add-lead-call-conversation"/>
                                                    <label class="form-check-label" for="add-lead-call-conversation">Call</label>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="conversation_type" value="Whatsapp" id="add-lead-whatsapp-conversation"/>
                                                    <label class="form-check-label" for="add-lead-whatsapp-conversation">Whatsapp</label>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="conversation_type" value="Email" id="add-lead-email-conversation"/>
                                                    <label class="form-check-label" for="add-lead-email-conversation">Email</label>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="conversation_type" value="In-person" id="add-lead-in-person-conversation"/>
                                                    <label class="form-check-label" for="add-lead-in-person-conversation">In person</label>
                                                </div>

                                            </div>

                                            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                        </form>

                                    </div>

                                    <!-- ===================== RIGHT: NOTES TABLE ===================== -->
                                    <div class="col-md-8">

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Notes</th>
                                                        <th>Type</th>
                                                        <th>Created By</th>
                                                        <th>Created At</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>

                                                <!-- ✅ IMPORTANT: ADD ID FOR JS -->
                                                <tbody id="lead-notes-tbody">
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">No data</td>
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
                                    value="{{ $saveadminfilter->by_lead_date ?? '' }}" placeholder="Lead Date Range...">
                            </div>

                            <!-- Qualified Status Filter -->
                            <div class="col-md-4 mb-3">
                                <select name="by_is_qualified" id="by_is_qualified"
                                    class="selectpicker w-100"
                                    data-live-search="false" data-style="default-btn" title="All">
                                    <option value="All" @if(isset($saveadminfilter) && $saveadminfilter->by_is_qualified == 'All') selected @endif>All</option>
                                    <option value="null" @if(isset($saveadminfilter) && $saveadminfilter->by_is_qualified == 'null') selected @endif>Not yet</option>
                                    <option value="1" @if(isset($saveadminfilter) && $saveadminfilter->by_is_qualified == '1') selected @endif>Followed Up</option>
                                    <option value="2" @if(isset($saveadminfilter) && $saveadminfilter->by_is_qualified == '2') selected @endif>Call Not Connected</option>
                                    <option value="3" @if(isset($saveadminfilter) && $saveadminfilter->by_is_qualified == '3') selected @endif>Lead Qualified</option>
                                    <option value="4" @if(isset($saveadminfilter) && $saveadminfilter->by_is_qualified == '4') selected @endif>Lead Not Qualified</option>
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
                                    value="{{ isset($saveadminfilter) ? $saveadminfilter->by_location : '' }}"
                                >
                            </div>


                              <!-- Updated Date -->
                            <div class="col-md-4 mb-3">
                                <input type="text" name="by_updated_date" id="by_updated_date"
                                    class="form-control bsdatpicket"
                                    value="{{ $saveadminfilter->by_updated_date ?? '' }}"
                                    placeholder="Updated Date Range...">
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




    </div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/leads-validation.js') }}"></script>

    <script>
        $(document).ready(function(){

            // ===============================
            // 1. View Lead Modal
            // ===============================
            $(document).on('click', '.view-lead-btn', function () {

                var leadId = $(this).data('id');
                $('#notes_lead_id').val(leadId);
                $('#viewLeadModal').modal('show');

                // Reset placeholders
                $('#leadDetailsContent').html('<div class="text-center text-muted">Loading...</div>');
                $('#leadLocationContent').html('<div class="text-center text-muted">Loading...</div>');
                $('#leadNotesContent table tbody').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');

                setTimeout(() => { $('#tab-lead-notes').tab('show'); }, 300);

                $.ajax({
                    url: '/admin/leads/view/' + leadId,
                    type: 'GET',

                    success: function (response) {

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
                        // Format Updated At
                        // ===============================
                        let updatedAt = '---';

                        if (response.updated_at) {
                            let date = new Date(response.updated_at);

                            updatedAt = date.toLocaleString('en-IN', {
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
                                    <p><strong>Name:</strong> ${response.cand_name || '---'}</p>
                                    <p><strong>Email:</strong> ${response.email || '---'}</p>
                                    <p><strong>Mobile:</strong> ${response.mob_no || '---'}</p>
                                    <p><strong>Country:</strong> ${response.country || '---'}</p>
                                    <p><strong>No. of Requirements:</strong> ${response.no_of_requirement || '---'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Experience:</strong> ${response.experience || '---'}</p>
                                    <p><strong>Saudi License:</strong> ${response.saudi_license === 'yes' ? 'Yes' : 'No'}</p>
                                    <p><strong>Indian License:</strong> ${response.india_license === 'yes' ? 'Yes' : 'No'}</p>
                                    <p><strong>Qualified:</strong> ${response.is_qualified == 1 ? 'Yes' : 'No'}</p>
                                    <p><strong>Updated At:</strong> ${updatedAt}</p>
                                </div>
                            </div>
                            <hr>
                            <p><strong>Reason:</strong> ${response.qualified_reason || '---'}</p>
                        `;

                        $('#leadDetailsContent').html(leadHtml);

                        // ===============================
                        // Location Details
                        // ===============================
                        let locationHtml = `
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>Submitted From:</strong> ${locationData.from || '---'}</p>
                                    <p><strong>Country:</strong> ${locationData.country || '---'}</p>
                                    <p><strong>Region:</strong> ${locationData.region || '---'}</p>
                                    <p><strong>City:</strong> ${locationData.city || '---'}</p>
                                    <p><strong>IP Address:</strong> ${locationData.ip || '---'}</p>
                                </div>
                            </div>
                        `;

                        $('#leadLocationContent').html(locationHtml);

                        // ===============================
                        // Notes Table Render
                        // ===============================
                        let notesHtml = '';

                        if (response.notes && response.notes.length > 0) {

                            response.notes.forEach(note => {

                                let createdAt = note.created_at
                                    ? new Date(note.created_at).toLocaleString('en-IN', {
                                        timeZone: 'Asia/Kolkata',
                                        day: '2-digit',
                                        month: 'short',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })
                                    : '---';

                                notesHtml += `
                                    <tr>
                                        <td>${note.notes || '---'}</td>
                                        <td>${note.conversation_type || '---'}</td>
                                        <td>${note.admin?.name || '---'}</td>
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

                        $('#lead-notes-tbody').html(notesHtml);
                    },

                    error: function () {
                        $('#leadDetailsContent').html('<div class="text-danger text-center">Failed to load lead details.</div>');
                        $('#leadLocationContent').html('<div class="text-danger text-center">Failed to load location data.</div>');
                        $('#leadNotesContent table tbody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load notes</td></tr>');
                    }
                });
            });

            // ===============================
            // 1.1 Add Note via AJAX
            // ===============================
            $(document).on('submit', '#notesValidation', function (e) {
                e.preventDefault();

                let form = $(this);
                let formData = form.serialize();

                $.ajax({
                    url: form.attr('action'),
                    type: "POST",
                    data: formData,

                    beforeSend: function () {
                        form.find('button[type="submit"]').prop('disabled', true).text('Saving...');
                    },

                    success: function (res) {

                        // ✅ Reset form
                        form[0].reset();

                        // ===============================
                        // Append New Note to Table
                        // ===============================
                        let createdAt = res.note.created_at
                            ? new Date(res.note.created_at).toLocaleString('en-IN', {
                                timeZone: 'Asia/Kolkata',
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })
                            : '---';

                        let newRow = `
                            <tr>
                                <td>${res.note.notes || '---'}</td>
                                <td>${res.note.conversation_type || '---'}</td>
                                <td>${res.note.admin_name || '---'}</td>
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

                    error: function (xhr) {
                        toastr.error('Failed to save note');

                        if (xhr.status === 422) {
                            console.log(xhr.responseJSON.errors);
                        }
                    },

                    complete: function () {
                        form.find('button[type="submit"]').prop('disabled', false).text('Submit');
                    }
                });
            });

            // ===============================
            // 1.2 Note via AJAX
            // ===============================
            $(document).on('click', '.delete-note', function () {

                let noteId = $(this).data('id');
                let row = $(this).closest('tr');

                $.ajax({
                    url: '/admin/leads/delete-note/' + noteId,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function (res) {

                        if (res.status) {

                            // ✅ Remove row from table
                            row.fadeOut(300, function () {
                                $(this).remove();

                                // ✅ If no rows left
                                if ($('#lead-notes-tbody tr').length === 0) {
                                    $('#lead-notes-tbody').html(`
                                        <tr>
                                            <td colspan="5" class="text-center">No notes found</td>
                                        </tr>
                                    `);
                                }
                            });

                        }
                    },

                    error: function () {
                        toastr.error('Failed to delete note');
                    }
                });
            });

            // ===============================
            // 2. Initialize Select2 and Selectpicker
            // ===============================
            $('.select2').each(function () {
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
                locale: { cancelLabel: 'Clear' }
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

            $(document).on('change', '.listitem :checkbox', function(){
                var listcheckitem = $('.listitem :checkbox');
                var checkedItems = listcheckitem.filter(":checked").length;
                var masterCheck = $('.checkboxSelectAll');

                if(checkedItems === listcheckitem.length){
                    masterCheck.prop({ indeterminate: false, checked: true });
                } else if(checkedItems > 0){
                    masterCheck.prop({ indeterminate: true, checked: false });
                } else {
                    masterCheck.prop({ indeterminate: false, checked: false });
                }

                $('.bulkactions').prop("disabled", !checkedItems);
            });

            // ===============================
            // 5. Switch Input for Assign Status
            // ===============================
            $('.switch-input').on('change', function(){
                var checked_value = this.checked ? 1 : 0;
                $.post("{{ route('admin.lead.userleadassign.update') }}", {
                    _token: "{{ csrf_token() }}",
                    lead_assign_status: checked_value
                }, function(response){
                    toastr.success(response.resp_message, 'Success', { hideDuration: 3000 });
                });
            });

            // ===============================
            // 6. AJAX Filter & Pagination for Leads
            // ===============================
            function getLeadFilterData() {
                return {
                    _token: '{{ csrf_token() }}',
                    page_list: $('#pagination_list').val(),
                    search_text: $('#search_text').val(),
                    by_assignee: $('#by_assignee').val(),
                    by_lead_date: $('#by_lead_date').val(),
                    by_is_qualified: $('#by_is_qualified').val(),
                    by_driving_license: $('#by_driving_license').val(),
                    by_job_title: $('#by_job_title').val(),
                    by_expected_days: $('#by_expected_days').val(),
                    by_expected_country: $('#by_expected_country').val(),
                    by_location: $('#by_location').val(),
                    by_updated_date: $('#by_updated_date').val(),
                    by_followup_before: $('#by_followup_before').val(),
                    by_submit_from: $('#by_submit_from').val(),
                    
                };
            }

            function reloadLeadsList() {
                $.ajax({
                    url: '{{ route("admin.leads.list") }}',
                    method: 'GET',
                    data: getLeadFilterData(),
                    success: function(data){
                        // replace the HTML (your existing behavior)
                        $('.leadpaginate, .lead-list').html(data);

                        // <-- NEW: reinitialize column toggles AFTER the new table is in DOM
                        // make sure initializeColumnToggles() is defined and available in scope
                        if (typeof initializeColumnToggles === 'function') {
                            initializeColumnToggles();
                        }
                    },
                    error: function(){
                        toastr.error('Failed to fetch leads.','Error');
                    }
                });
            }

            $('#pagination_list, #search_text, #by_assignee, #by_lead_date, #by_is_qualified, #by_driving_license, #by_job_title, #by_expected_days, #by_expected_country, #by_location, #by_updated_date, #by_followup_before, #by_submit_from')
            .on('change input', reloadLeadsList);

            // Pagination links
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();

                var url = $(this).attr('href') + "&" + $.param(getLeadFilterData());

                $.get(url, function(data) {
                    // ✅ Update lead list and pagination
                    $('.leadpaginate, .lead-list').html(data);

                    // ✅ Re-initialize column toggles AFTER table reload
                    initializeColumnToggles();
                });

                // ✅ Update browser history (no effect on DOM)
                window.history.pushState("", "", url);
            });

            // Reset Filter
            $(document).on('click', '.resetfilter', function () {

                // 1️⃣ Reset UI filters
                $('.selectpicker').selectpicker('deselectAll');

                $('#by_lead_date').val('');

                $('#by_is_qualified').val('').selectpicker('render');

                $('#by_location').val('');

                $('#by_updated_date').val('');

                $('#by_followup_before').val('').selectpicker('render');

                $('#by_submit_from').val('').selectpicker('render');
                
                // 2️⃣ Save reset state (existing logic)
                $.post('{{ route("admin.leads.saveFilter") }}', getLeadFilterData(), function (res) {
                    toastr.success(res.message || 'Filter Reset successfully!', 'Success', { timeOut: 2000 });
                }).fail(function () {
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
            $(document).on('click', '.apply_filters', function(){
                $.post('{{ route("admin.leads.saveFilter") }}', getLeadFilterData(), function(res){
                    toastr.success(res.message || 'Filter saved successfully!', 'Success', {timeOut:2000});
                }).fail(function(){ toastr.error('Failed to save filter','Error'); });

                reloadLeadsList();

                updateAdminFilterIndicator();
            });

            // ===============================
            // 7. Bulk Assign/Delete Modals
            // ===============================
            $('#bulkdelete, #bulkassignto, #bulkexport, #bulkSendToMeta').on('show.bs.modal', function(e){
                var allselectedvals = $('.dt-checkboxes:checked').map(function(){ return $(this).data('id'); }).get();
                var join_all_selected_values = allselectedvals.join(",");

                if(this.id === 'bulkexport'){
                    initializeColumns();
                }
                
                if(this.id === 'bulkdelete') $('#bulkdelete_id').val(join_all_selected_values);
                else if(this.id === 'bulkassignto') $('#bulkassignto_id').val(join_all_selected_values);
                else if(this.id === 'bulkexport') $('#bulkexport_id').val(join_all_selected_values);
                else if(this.id === 'bulkSendToMeta') $('#bulkLeadSendToMeta_id').val(join_all_selected_values);
                
            });


            // ===============================
            // 7.1. Bulk Export Modals (UPDATED)
            // ===============================
            $(document).on('click', '#bulk-export', function (e) {
                e.preventDefault();

                let leadIds = $('#bulkexport_id').val();
                let columns = [];

                $('.toggle-lead-column:checked').each(function () {
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



            // ===============================
            // 8. Qualified Lead Modal Logic
            // ===============================
            $('#qualifiedlead').on("show.bs.modal", function(e) {
                var leadID = $(e.relatedTarget).data('id');
                $('#leadIDQualified').val(leadID);

                // AJAX call to get current qualified status
                $.ajax({
                    url: '/admin/leads/' + leadID + '/qualified', // or use route() helper if rendering in Blade
                    method: 'GET',
                    success: function(response) {
                        $('#qualified_status').val(response.is_qualified).trigger('change');
                        $('#qualified_reason').val(response.qualified_reason);

                        if(response.is_qualified == 3) {
                            $('.AddToPipelineCheckbox').show();
                        } else {
                            $('.AddToPipelineCheckbox').hide();
                        }
                                                
                        $('#q_lead_id').val(response.lead.id);
                        $('#q_lead_name').val(response.lead.cand_name || '---');
                        $('#q_job_title').val(response.lead.required_service || '---').trigger('change');
                        $('#q_job_title_other').val(response.lead.other_job_title);
                        $('#q_experience').val(response.lead.experience || '---');
                        $('#q_expected_days').val(response.lead.expected_days || '---');

                        let licences = [];

                        // Saudi License
                        if (response.lead.saudi_license === 'Yes') {
                            licences.push('saudi_license');
                        }

                        // India License
                        if (response.lead.india_license === 'Yes') {
                            licences.push('india_license');
                        }

                        // Set into Select2
                        $('#q_driving_licence').val(licences).trigger('change');

                    },
                    error: function() {
                        // fallback to default (Not selected)
                        $('#qualified_status').val('').trigger('change');
                    }
                });
            });

          

            // ✅ Init Select2 (modal support)
            $('#q_job_title').select2({
                width: '100%',
                dropdownParent: $('#qualifiedlead') // modal fix
            });

            // ✅ Toggle Function (Reusable)
            function toggleJobTitle() {
                let value = $('#q_job_title').val();

                if (value === 'Other') {
                    $('.other_job_title_container').removeClass('d-none');
                } else {
                    $('.other_job_title_container').addClass('d-none');
                    $('#q_job_title_other').val('');
                }
            }

            // ✅ On change (Select2 compatible)
            $('#q_job_title').on('change', function() {
                toggleJobTitle();
            });

            // ✅ Run on page load (important for edit mode)
            toggleJobTitle();


            $(document).on('submit', '#leadDetailForm', function(e) {
                e.preventDefault();

                if($('#q_job_title').val() != 'Other'){
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
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: "{{ route('admin.leads.updateLeadDetails') }}",
                    type: "POST",
                    data: formData,

                    success: function(response) {
                        toastr.success(response.message || 'Updated successfully');
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
                if(selectedValue == '3') {
                    $('.AddToPipelineCheckbox').show();
                } else {
                    $('.AddToPipelineCheckbox').hide();
                }
            });

            // ===============================
            // 9. Add Lead id 
            // ===============================

            $('#assignlead').on("show.bs.modal",function(e){
                var leadID = $(e.relatedTarget).data('id');
                $('#leadID3').val(leadID);
            });

            $('#deletelead').on("show.bs.modal",function(e){
                var leadID = $(e.relatedTarget).data('id');
                $('#leadID2').val(leadID);
            });

            initializeColumnToggles();

            // =====================================================
            // TRIGGER ON CHANGE (Bootstrap Select + Inputs)
            // =====================================================
            $('.selectpicker').on('changed.bs.select', function () {
            updateAdminFilterIndicator();
            });

            $('#by_lead_date, #by_location, #by_updated_date, #by_followup_before, #by_submit_from').on('change keyup', function () {
            updateAdminFilterIndicator();
            });

            // =====================================================
            // INITIAL CHECK (ON PAGE LOAD / SAVED FILTER)
            // =====================================================
            updateAdminFilterIndicator();


            // =======================================
            // update mobile number Check (AJAX)
            // =======================================
            $(document).on('click', '.editContact', function () {

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
                    success: function(res) {
                        toastr.success('Updated successfully');
                        $('#editContactModal').modal('hide');

                        var url = window.location.href + "&" + $.param(getLeadFilterData());
                        
                        $.get(url, function(data) {
                            // ✅ Update lead list and pagination
                            $('.leadpaginate, .lead-list').html(data);

                            // ✅ Re-initialize column toggles AFTER table reload
                            initializeColumnToggles();
                        });

                        // ✅ Update browser history (no effect on DOM)
                        window.history.pushState("", "", url);

                                                
                    },
                    error: function(err) {
                        toastr.error('Something went wrong');
                    }
                });
            });



        });

        // ===============================
        // 10. ADMIN FILTER INDICATOR (SHOW / HIDE DOT)
        // ===============================

        function updateAdminFilterIndicator() {

            let isFiltered =
                ($('#by_assignee').val() && $('#by_assignee').val().length > 0) ||
                $('#by_lead_date').val() ||
                $('#by_location').val() ||
                ($('#by_is_qualified').val() && $('#by_is_qualified').val() !== 'All') ||
                ($('#by_driving_license').val() && $('#by_driving_license').val().length > 0) ||
                ($('#by_job_title').val() && $('#by_job_title').val().length > 0) ||
                ($('#by_expected_days').val() && $('#by_expected_days').val().length > 0) ||
                ($('#by_expected_country').val() && $('#by_expected_country').val().length > 0) ||
                ($('#by_submit_from').val() && $('#by_submit_from').val().length > 0) ||
                ($('#by_updated_date').val() && $('#by_updated_date').val() !== '') ||
                ($('#by_followup_before').val() && $('#by_followup_before').val() !== '');

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


        $(document).on('change', '.toggle-lead-column', function () {
            const col = $(this).data('column');
            localStorage.setItem('lead_col_' + col, this.checked);
        });


        // ===============================
        // Custom Column Show/Hide Feature
        // ===============================
        function initializeColumnToggles() {
            const table = $('#leadTable');
            const toggleContainer = $('#columnToggles');

            if (!table.length || !toggleContainer.length) return;

            // Clear old checkboxes
            toggleContainer.html('');

            // Create toggles dynamically from table headers
            table.find('thead th').each(function (index) {
                const columnName = $(this).text().trim();
                if (!columnName) return;

                // Read visibility state from localStorage (default: visible)
                const stored = localStorage.getItem('col_' + index);
                const isChecked = stored !== 'false';

                // Add toggle checkbox
                toggleContainer.append(`
                    <div class="form-check mb-2 col-6">
                        <input id="col_${index}" class="form-check-input toggle-column" type="checkbox" 
                            data-column="${index}" ${isChecked ? 'checked' : ''}>
                        <label for="col_${index}" class="form-check-label">${columnName}</label>
                    </div>
                `);

                // Apply stored visibility immediately
                if (!isChecked) {
                    table.find('tr').each(function () {
                        $(this).find('th:eq(' + index + '), td:eq(' + index + ')').hide();
                    });
                }
            });

            // ✅ Toggle event: show/hide columns and store preference
            $(document).off('change', '.toggle-column').on('change', '.toggle-column', function () {
                const columnIndex = $(this).data('column');
                const isVisible = $(this).is(':checked');

                // Show/hide the column in both header and body
                table.find('tr').each(function () {
                    $(this).find('th:eq(' + columnIndex + '), td:eq(' + columnIndex + ')')[isVisible ? 'show' : 'hide']();
                });

                // Save state to localStorage
                localStorage.setItem('col_' + columnIndex, isVisible);
            });
        }

        // 🔹 Initialize toggles every time modal opens
        $('#showHideColumnsModal').on('shown.bs.modal', function () {
            initializeColumnToggles();
        });


        // ===============================
        // Qualified Lead Form Submission (AJAX)
        // ===============================
        $(document).on('submit', '#qualifiedLeadForm', function (e) {
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

                success: function (response) {

                    if (response.status) {

                        $('#qualifiedlead').modal('hide');
                        form[0].reset();

                        toastr.success(response.message, 'Success', { timeOut: 2000 });

                        // ✅ Update badge dynamically
                        let badgeHtml = '';

                        switch (parseInt(response.is_qualified)) {

                            case 1:
                                badgeHtml = '<span class="badge bg-label-info">Followed Up</span>';
                                break;

                            case 2:
                                badgeHtml = '<span class="badge bg-label-secondary">Call Not Connected</span>';
                                break;

                            case 3:
                                badgeHtml = '<span class="badge bg-label-success">Lead Qualified</span>';
                                break;

                            case 4:
                                badgeHtml = '<span class="badge bg-label-danger">Lead Not Qualified</span>';
                                break;

                            default:
                                badgeHtml = '<span class="badge bg-label-warning">Not Yet</span>';
                        }

                        $('#qualified_badge_' + response.lead_id + ' a').html(badgeHtml);

                    } else {
                        toastr.error(response.message || 'Operation failed');
                    }
                },
                error: function (xhr) {

                    if (xhr.status === 422 && xhr.responseJSON.errors) {

                        let errorMessage = '';
                        $.each(xhr.responseJSON.errors, function (key, value) {
                            errorMessage += value[0] + "<br>";
                        });

                        toastr.error(errorMessage, 'Validation Error');

                    } else if (xhr.status === 404) {

                        toastr.error('Lead not found.');

                    } else {

                        toastr.error('Unexpected error occurred.');
                    }
                },

                complete: function () {
                    submitBtn.prop('disabled', false).text('Submit');
                }
            });
        });

        // ===============================
        // 11. Mobile Number Check (AJAX)
        // ===============================
        $("#check-mobile-input").on("keyup", function () {
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
                success: function (response) {

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

       



    </script>


@endsection
