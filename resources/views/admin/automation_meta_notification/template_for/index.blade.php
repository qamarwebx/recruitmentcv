@extends('layout.admin.admin_layout')

@section('title','Meta Notification')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/quill/editor.css') }}" />

    @if (Auth::guard('admin')->user()->user_type == 2)

        @if (isset($perm) && $perm->meta_automation == 0)
        <style>
        .meta_automation{
            display: none !important;
        }
        </style>
        @endif

    @endif

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Session</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">21,459</h4>
                        <span class="text-success">(+29%)</span>
                        </div>
                        <span>Total Users</span>
                    </div>
                    <span class="badge bg-label-primary rounded p-2">
                        <i class="ti ti-user ti-sm"></i>
                    </span>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Paid Users</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">4,567</h4>
                        <span class="text-success">(+18%)</span>
                        </div>
                        <span>Last week analytics </span>
                    </div>
                    <span class="badge bg-label-danger rounded p-2">
                        <i class="ti ti-user-plus ti-sm"></i>
                    </span>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Active Users</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">19,860</h4>
                        <span class="text-danger">(-14%)</span>
                        </div>
                        <span>Last week analytics</span>
                    </div>
                    <span class="badge bg-label-success rounded p-2">
                        <i class="ti ti-user-check ti-sm"></i>
                    </span>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Pending Users</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">237</h4>
                        <span class="text-success">(+42%)</span>
                        </div>
                        <span>Last week analytics</span>
                    </div>
                    <span class="badge bg-label-warning rounded p-2">
                        <i class="ti ti-user-exclamation ti-sm"></i>
                    </span>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <!-- Users List Table -->
        <div class="card meta_automation">
            <div class="card-header border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-2">
                        Templates for: <span class="text-primary">{{ ucfirst($template_for ?? 'All') }}</span>
                    </h5>

                    <!-- 🔵 Back Button -->
                    <a href="{{ route('admin.metaautomation.list') }}" class="btn btn-secondary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">Search Filter</h5>
                <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 user_role"></div>
                <div class="col-md-4 user_plan"></div>
                <div class="col-md-4 user_status"></div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Template Name</th>
                            <th>Meta Template Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!--- Add Candidate Start --->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">

            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Add Template</h5>
                <button type="button" class="btn-close text-reset"
                    data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">

                <form class="add-new-user pt-0"
                    action="{{ route('admin.automation-metanotification.store') }}"
                    method="POST">

                    @csrf

                    <input type="hidden" name="template_for" value="{{ $template_for }}">
                    <input type="hidden" name="template_table_name" value="{{ $tableName }}">

                    <div class="row">

                        {{-- Meta Template --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Meta Template <span class="text-danger">*</span>
                                </label>
                                <select name="metatemp_id"
                                    class="form-control select2"
                                    data-placeholder="Select Meta Template">
                                    <option value=""></option>
                                    @foreach ($templates as $t)
                                        <option value="{{ $t->id }}">
                                            {{ $t->template_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Template Name --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Template Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    name="meta_template_name"
                                    class="form-control">
                            </div>
                        </div>

                        {{-- Trigger (Common for ALL modules) --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Trigger <span class="text-danger">*</span>
                                </label>
                                <select name="trigger_template_type"
                                    class="form-control select2"
                                    data-placeholder="Select Trigger">
                                    <option value=""></option>
                                    <option value="lead_assign">Lead Assign</option>
                                    <option value="add_new_entry_added">Add New Entry Added</option>
                                    <option value="order">Order</option>
                                    <option value="cancel_order">Cancel Order</option>
                                    <option value="cancel_order_to_partner">Cancel Order To Partner</option>
                                    <option value="order_to_partner">Order To Partner</option>
                                </select>
                            </div>
                        </div>

                        {{-- Action Type --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Action <span class="text-danger">*</span>
                                </label>
                                <select name="action_type"
                                    id="action-type"
                                    class="form-control select2"
                                    data-placeholder="Select Action">
                                    <option value=""></option>
                                    <option value="immediate">Send Immediately</option>
                                    <option value="wait">Wait</option>
                                </select>
                            </div>
                        </div>

                        {{-- Template (Sent to) --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Template Send to <span class="text-danger">*</span>
                                </label>
                                <select name="template_send_to"
                                    class="form-control select2"
                                    data-placeholder="Select send to">
                                    <option value=""></option>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>

                        {{-- WAIT SECTION --}}
                        <div class="col-md-3 trigger-time-div" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">
                                    Time <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                    name="trigger_template_time"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3 trigger-time-div" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">
                                    Type <span class="text-danger">*</span>
                                </label>
                                <select name="trigger_template_time_type"
                                    class="form-control select2">
                                    <option value=""></option>
                                    <option value="minute">Minute</option>
                                    <option value="hour">Hour</option>
                                    <option value="day">Day</option>
                                    <option value="week">Week</option>
                                    <option value="month">Month</option>
                                    <option value="year">Year</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 trigger-time-div" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">
                                    Field Not Completed
                                </label>
                                <select name="field_not_completed[]"
                                    class="form-control select2"
                                    multiple>
                                    @foreach($columns as $field)
                                        <option value="{{ $field }}">
                                            {{ ucfirst(str_replace('_', ' ', $field)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Message --}}
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea name="msg_whatsapp"
                                    class="form-control"
                                    rows="6"></textarea>
                            </div>
                        </div>

                    </div>

                    <button type="submit"
                        class="btn btn-primary me-sm-3 me-1">
                        Submit
                    </button>

                    <button type="reset"
                        class="btn btn-label-secondary"
                        data-bs-dismiss="offcanvas">
                        Cancel
                    </button>

                </form>
            </div>
        </div>
        <!--- Add Candidate End --->

        <!--- Edit Candidate Start --->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel" data-bs-backdrop="static" data-bs-keyboard="false">

            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Edit Template</h5>
                <button type="button"
                    class="btn-close text-reset"
                    data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">

                <form class="add-new-user pt-0"
                    id="editUserForm2"
                    action="{{ route('admin.automation-metanotification.update') }}"
                    method="POST">

                    @csrf

                    <input type="hidden" name="edit_id" id="editid">
                    <input type="hidden" name="template_for" value="{{ $template_for }}">
                    <input type="hidden" name="template_table_name" value="{{ $tableName }}">

                    <div class="row">

                        {{-- Meta Template --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Meta Template <span class="text-danger">*</span>
                                </label>
                                <select name="metatemp_id"
                                    id="edit-meta-template"
                                    class="form-control select2">
                                    <option value=""></option>
                                    @foreach ($templates as $t)
                                        <option value="{{ $t->id }}">
                                            {{ $t->template_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Template Name --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Template Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    name="meta_template_name"
                                    id="edit-template-name"
                                    class="form-control">
                            </div>
                        </div>

                        {{-- Trigger (Common for ALL modules) --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Trigger <span class="text-danger">*</span>
                                </label>
                                <select name="trigger_template_type"
                                    id="edit-trigger-template-type"
                                    class="form-control select2">
                                    <option value=""></option>
                                    <option value="lead_assign">Lead Assign</option>
                                    <option value="add_new_entry_added">Add New Entry Added</option>
                                    <option value="order">Order</option>
                                    <option value="cancel_order">Cancel Order</option>
                                    <option value="cancel_order_to_partner">Cancel Order To Partner</option>
                                    <option value="order_to_partner">Order To Partner</option>
                                </select>
                            </div>
                        </div>

                        {{-- Action Type --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Action <span class="text-danger">*</span>
                                </label>
                                <select name="action_type"
                                    id="edit-action-type"
                                    class="form-control select2">
                                    <option value=""></option>
                                    <option value="immediate">Send Immediately</option>
                                    <option value="wait">Wait</option>
                                </select>
                            </div>
                        </div>
                        
                        {{-- Template (Sent to) --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Template Send to <span class="text-danger">*</span>
                                </label>
                                <select name="template_send_to"
                                    id="edit-template-send-to"
                                    class="form-control select2"
                                    data-placeholder="Select send to">
                                    <option value=""></option>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>

                        {{-- WAIT SECTION --}}
                        <div class="col-md-3 edit-trigger-time-div" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">
                                    Time <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                    name="trigger_template_time"
                                    id="edit-trigger-template-time"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3 edit-trigger-time-div" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">
                                    Type <span class="text-danger">*</span>
                                </label>
                                <select name="trigger_template_time_type"
                                    id="edit-trigger-template-time-type"
                                    class="form-control select2">
                                    <option value=""></option>
                                    <option value="minute">Minute</option>
                                    <option value="hour">Hour</option>
                                    <option value="day">Day</option>
                                    <option value="week">Week</option>
                                    <option value="month">Month</option>
                                    <option value="year">Year</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 edit-trigger-time-div" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">
                                    Field Not Completed
                                </label>
                                <select name="field_not_completed[]"
                                    id="edit-field-not-completed"
                                    class="form-control select2"
                                    multiple>
                                    @foreach($columns as $field)
                                        <option value="{{ $field }}">
                                            {{ ucfirst(str_replace('_', ' ', $field)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Message --}}
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea name="msg_whatsapp"
                                    id="edit-msg-body"
                                    class="form-control"
                                    rows="6"></textarea>
                            </div>
                        </div>

                    </div>

                    <button type="submit"
                        class="btn btn-primary me-sm-3 me-1">
                        Submit
                    </button>

                    <button type="reset"
                        class="btn btn-label-secondary"
                        data-bs-dismiss="offcanvas">
                        Cancel
                    </button>

                </form>
            </div>
        </div>
        <!--- Edit Candidate End --->

        <!-- Delete Template Modal Start -->
        <div class="modal fade" id="deleteTemplate" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.automation-metanotification.destroy') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="del_temp_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-12">
                                <p>Are you sure!, to delete template?</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-danger btn-sm " id="disbtn">Delete</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
        <!-- Delete Template Modal End -->

         <!--Change Status Modal Start -->
         <div class="modal fade" id="statusChange" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Status change</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.autometanotification.status.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tempchstID" id="tempchstID">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check form-check-inline mt-3">
                                            <input class="form-check-input" type="radio" name="status" id="changestact" value="1"/>
                                            <label class="form-check-label" for="changestact">Active</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="changestdeact" value="0"/>
                                            <label class="form-check-label" for="changestdeact">Deactive</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                            <button type="submit" class="btn btn-danger btn-sm">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--Change Status Modal End -->

        <!-- Publish / status modals remain unchanged -->
        <div class="modal fade" id="activeStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Active Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.template.activeSt') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tempactID" id="tempactID">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-12">
                                <p>Are you sure!, to active template?</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-success btn-sm ">Active</button>
                    </div>
                </form>
            </div>
            </div>
        </div>

        <div class="modal fade" id="deactiveStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Deactive Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.template.deactiveSt') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tempdeactID" id="tempdeactID">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p>Are you sure!, to deactive template?</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-danger btn-sm">Deactive</button>
                    </div>
                </form>
            </div>
            </div>
        </div>

        <!-- other modals left as in your original file... -->

    </div>
@endsection

@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>

    <script>
        window.metaTemplateFor = {!! json_encode($template_for) !!};
    </script>

    <script src="{{ asset('admin/assets/pages/app-tempalate-wise-list.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/auto-meta-notification-validation.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>

    @if (isset($perm) && $perm->add_meta_automation == 0)
        <script>
        $(document).ready(function () {
                $('.addtemplate').each(function () {
                    this.style.setProperty('display', 'none', 'important');
                });
            });
        </script>
    @endif

    @if (isset($perm) && $perm->edit_meta_automation == 0)
        <script>
        $(document).ready(function () {
                $('.edtemplate').each(function () {
                    this.style.setProperty('display', 'none', 'important');
                });
            });
        </script>
    @endif

    @if (isset($perm) && $perm->delete_meta_automation == 0)
        <script>
        $(document).ready(function () {
                $('.deltemplate').each(function () {
                    this.style.setProperty('display', 'none', 'important');
                });
            });
        </script>
    @endif


    <script>
        $(document).ready(function(){

            // ----------------------------------
            // Initialize Select2
            // ----------------------------------
            $('.select2').each(function () {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    dropdownParent: $this.parent()
                });
            });

            // ----------------------------------
            // ADD PAGE META TEMPLATE CHANGE
            // ----------------------------------
            $('#add-meta-template').on("change", function(){
                var template_id = $(this).val();

                if (!template_id) return;

                $.ajax({
                    url: "{{ route('admin.metatemplate.getListID') }}",
                    method: 'GET',
                    data:{ id: template_id },
                    success: function(data){
                        $('#add-template-name').val(data.template_name);
                        $('#add-msg-body').val(data.whatsapp_message);
                    }
                });
            });

            // ----------------------------------
            // REUSABLE TEMPLATE LOADER
            // ----------------------------------
            function loadMetaTemplateData(template_id) {
                if (!template_id) return;

                $.ajax({
                    url: "{{ route('admin.metatemplate.getListID') }}",
                    method: 'GET',
                    data: { id: template_id },
                    success: function(data){
                        $('#edit-template-name').val(data.template_name);
                        $('#edit-msg-body').val(data.whatsapp_message);
                    }
                });
            }

            $('#edit-meta-template').on("change", function(){
                let template_id = $(this).val();
                loadMetaTemplateData(template_id);
            });

            // ----------------------------------
            // ADD FORM ACTION TYPE HANDLER
            // ----------------------------------
            $('#action-type').on('change', function(){

                if($(this).val() === 'wait'){
                    $('.trigger-time-div').slideDown();
                } else {
                    $('.trigger-time-div').slideUp();
                    $('input[name="trigger_template_time"]').val('');
                    $('select[name="trigger_template_time_type"]').val('').trigger('change');
                }

            });

            // ----------------------------------
            // EDIT FORM ACTION TYPE HANDLER
            // ----------------------------------
            $('#edit-action-type').on('change', function(){

                if($(this).val() === 'wait'){
                    $('.edit-trigger-time-div').slideDown();
                } else {
                    $('.edit-trigger-time-div').slideUp();
                    $('#edit-trigger-template-time').val('');
                    $('#edit-trigger-template-time-type').val('').trigger('change');
                    $('#edit-field-not-completed').val('').trigger('change');
                }

            });

            // ----------------------------------
            // EDIT OFFCANVAS OPEN
            // ----------------------------------
            $('#edituser').on('show.bs.offcanvas', function(e){

                var editID = $(e.relatedTarget).data('id');

                $.ajax({
                    url : '{{ route("admin.autometanotification.edit") }}',
                    method: "GET",
                    data: { id: editID },
                    success: function(data){

                        $('#editid').val(data.id);

                        // Meta template
                        $('#edit-meta-template')
                            .val(data.metatemp_id)
                            .trigger('change');

                        loadMetaTemplateData(data.metatemp_id);

                        // Trigger
                        $('#edit-trigger-template-type')
                            .val(data.trigger_template_type)
                            .trigger('change');

                        // Action type
                        $('#edit-action-type')
                            .val(data.action_type)
                            .trigger('change');

                        // edit-template-send-to
                        $('#edit-template-send-to')
                            .val(data.template_send_to)
                            .trigger('change');

                        // If wait → show time section
                        if (data.action_type === 'wait') {

                            $('.edit-trigger-time-div').show();

                            $('#edit-trigger-template-time')
                                .val(data.trigger_template_time);

                            $('#edit-trigger-template-time-type')
                                .val(data.trigger_template_time_type)
                                .trigger('change');

                            if (data.field_not_completed) {
                                $('#edit-field-not-completed')
                                    .val(JSON.parse(data.field_not_completed))
                                    .trigger('change');
                            }

                        } else {
                            $('.edit-trigger-time-div').hide();
                        }
                    }
                });

            });

            // ----------------------------------
            // DELETE TEMPLATE
            // ----------------------------------
            $('#deleteTemplate').on('show.bs.modal',function(e){
                $('#del_temp_id').val($(e.relatedTarget).data('id'));
            });

            // ----------------------------------
            // STATUS CHANGE
            // ----------------------------------
            $('#statusChange').on('show.bs.modal',function(e){

                var id = $(e.relatedTarget).data('id');
                $('#tempchstID').val(id);

                $.ajax({
                    url : "{{ route('admin.autometanotification.getStatus') }}",
                    method: "GET",
                    data: { id: id },
                    success: function(data){
                        if (data.status == 1) {
                            $('#changestact').prop("checked",true);
                        } else {
                            $('#changestdeact').prop("checked",true);
                        }
                    }
                });

            });

        });
    </script>

@endsection
