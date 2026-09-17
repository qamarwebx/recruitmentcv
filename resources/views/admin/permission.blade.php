@extends('layout.admin.admin_layout')

@section('title','Permission')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <style>
        #permission-groups .permission-group-body > div {
            margin-top: 1.25rem;
        }
        #permission-groups .permission-group-body > div:first-child {
            margin-top: 1.5rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.permission.store') }}" method="POST" id="permissionV">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="permission-search" class="form-label">Search Permission</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                                    <input type="text" id="permission-search" class="form-control" placeholder="Search a module or permission...">
                                </div>
                                <small class="text-muted" id="permission-search-count"></small>
                            </div>
                        </div>
                        <div class="col-md-3 offset-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Staff <span class="text-danger">*</span></label>
                                <select name="staff_id" id="staff_id" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-check-label" for="full-access"> Full Access</label>
                                <input class="form-check-input" type="checkbox" name="full_access" value="1" id="full-access" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-check-label" for="permission-setting">Permission</label>
                                <input class="form-check-input" type="checkbox" name="permission_setting" id="permission-setting" value="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary btn-sm float-end data-submit">Submit</button>
                            </div>
                        </div>
                    </div>

                    <div id="permission-groups">
                        <div class="card mb-4 permission-group" data-group="main">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Main Menu</h6>
                            </div>
                            <div class="card-body permission-group-body"></div>
                        </div>
                        <div class="card mb-4 permission-group" data-group="finance">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Finance</h6>
                            </div>
                            <div class="card-body permission-group-body"></div>
                        </div>
                        <div class="card mb-4 permission-group" data-group="campaigns">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Campaigns</h6>
                            </div>
                            <div class="card-body permission-group-body"></div>
                        </div>
                        <div class="card mb-4 permission-group" data-group="settings">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Settings</h6>
                            </div>
                            <div class="card-body permission-group-body"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="8">
                                                <label class="form-check-label" for="leads">Leads</label>
                                                <input type="checkbox" name="leads" class="form-check-input" id="leads" value="1">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="leads-view">View All</label>
                                                <input type="checkbox" name="leads_view" class="form-check-input groups15" id="leads-view" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="leads-delete">Delete</label>
                                                <input type="checkbox" name="leads_delete" class="form-check-input groups15" id="leads-delete" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="leads-assignto">Assign To</label>
                                                <input type="checkbox" name="leads_assignto" class="form-check-input groups15" id="leads-assignto" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="leads-bulk-delete">Bulk Delete</label>
                                                <input type="checkbox" name="leads_bulk_delete" class="form-check-input groups15" id="leads-bulk-delete" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="leads-bulk-assignto">Bulk Assign To</label>
                                                <input type="checkbox" name="leads_bulk_assignto" class="form-check-input groups15" id="leads-bulk-assignto" value="1" disabled>
                                            </td>

                                            <td>
                                                <label for="leads-candidate-view" class="form-check-label">Candidate Leads</label>
                                                <input type="checkbox" name="leads_candidate_view" id="leads-candidate-view" class="form-check-input groups15" value="1" disabled>
                                            </td>

                                            <td>
                                                <label for="leads-employer-view" class="form-check-label">Employer Leads</label>
                                                <input type="checkbox" name="leads_employer_view" id="leads-employer-view" class="form-check-input groups15" value="1" disabled>
                                            </td>

                                            <td>
                                                <label for="leads-company-column" class="form-check-label">Company Column</label>
                                                <input type="checkbox" name="leads_company_column" id="leads-company-column" class="form-check-input groups15" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowwrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="12">
                                                <label for="allcontact" class="form-check-label">Allcontact</label>
                                                <input type="checkbox" class="form-check-input" name="allcontact" id="allcontact" value="1">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="allcontact-add">Add</label>
                                                <input class="form-check-input groups16" type="checkbox" name="allcontact_add" id="allcontact-add" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="allcontact-edit">Edit</label>
                                                <input class="form-check-input groups16" type="checkbox" name="allcontact_edit" id="allcontact-edit" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="allcontact-view">View All</label>
                                                <input class="form-check-input groups16" type="checkbox" name="allcontact_view" id="allcontact-view" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="allcontact-delete">Delete</label>
                                                <input class="form-check-input groups16" type="checkbox" name="allcontact_delete" id="allcontact-delete" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="allcontact-bulk-transfer-group">Bulk Transfer Group</label>
                                                <input class="form-check-input groups16" type="checkbox" name="allcontact_bulk_transfer_group" id="allcontact-bulk-transfer-group" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="allcontact-bulk-transfer-careoff">Bulk Transfer Careoff</label>
                                                <input class="form-check-input groups16" type="checkbox" name="allcontact_bulk_transfer_careoff" id="allcontact-bulk-transfer-careoff" value="1" disabled>
                                            </td>
                                            {{-- <td>
                                                <label class="form-check-label" for="allcontact-bulk-transfer-lead-owner">Bulk Transfer Lead Owner</label>
                                                <input class="form-check-input groups16" type="checkbox" name="allcontact_bulk_transfer_leadowner" id="allcontact-bulk-transfer-lead-owner" value="1" disabled>
                                            </td> --}}
                                            <td>
                                                <label class="form-check-label" for="allcontact-bulk-send-whatsapp">Bulk Send Whatsapp</label>
                                                <input class="form-check-input groups16" type="checkbox" name="allcontact_bulk_send_whatsapp" id="allcontact-bulk-send-whatsapp" value="1" disabled>
                                            </td>

                                            <td>
                                                <label class="form-check-label" for="allcontact-bulk-delete">Bulk Delete</label>
                                                <input class="form-check-input groups16" type="checkbox" name="allcontact_bulk_delete" id="allcontact-bulk-delete" value="1" disabled>
                                            </td>

                                            <td>
                                                <label class="form-check-label" for="allcontact-bulk-country-update">Bulk Country Update</label>
                                                <input class="form-check-input groups16" type="checkbox" name="allcontact_bulk_country_update" id="allcontact-bulk-country-update" value="1" disabled>
                                            </td>

                                            <td>
                                                <label class="form-check-label" for="allcontact-bulk-country-code-update">Bulk Country Code Update</label>
                                                <input class="form-check-input groups16" type="checkbox" name="allcontact_bulk_country_code_update" id="allcontact-bulk-country-code-update" value="1" disabled>
                                            </td>

                                            <td>
                                                <label class="form-check-label" for="allcontact-company-column">Company Column</label>
                                                <input class="form-check-input groups16" type="checkbox" name="allcontact_company_column" id="allcontact-company-column" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="10">
                                                <label class="form-check-label" for="todo">Todo</label>
                                                <input class="form-check-input" type="checkbox" name="todo" value="1" id="todo">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="todo-add">Add</label>
                                                <input class="form-check-input groups12" type="checkbox" name="todo_add" id="todo-add" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="todo-view">View All</label>
                                                <input class="form-check-input groups12" type="checkbox" name="todo_view" id="todo-view" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="todo-edit">Edit</label>
                                                <input class="form-check-input groups12" type="checkbox" name="todo_edit" id="todo-edit" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="todo-delete">Delete</label>
                                                <input class="form-check-input groups12" type="checkbox" name="todo_delete" id="todo-delete" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="todo-achieved">View All Achieved</label>
                                                <input class="form-check-input groups12" type="checkbox" name="todo_achieved" id="todo-achieved" value="1" disabled>
                                            </td>

                                            <td>
                                                <label class="form-check-label" for="update-status-for-achieved">Update Status For Achieved</label>
                                                <input class="form-check-input groups12" type="checkbox" name="update_status_achieved" id="update-status-for-achieved" value="1" disabled>
                                            </td>

                                            <td>
                                                <label class="form-check-label" for="bulk-todo-status-update">Bulk Status</label>
                                                <input class="form-check-input groups12" type="checkbox" name="bulk_todo_update_status" id="bulk-todo-status-update" value="1" disabled>
                                            </td>

                                            <td>
                                                <label class="form-check-label" for="bulk-todo-priority-update">Bulk Priority</label>
                                                <input class="form-check-input groups12" type="checkbox" name="bulk_todo_update_priority" id="bulk-todo-priority-update" value="1" disabled>
                                            </td>

                                            <td>
                                                <label class="form-check-label" for="bulk-todo-assignto-update">Bulk Assignto</label>
                                                <input class="form-check-input groups12" type="checkbox" name="bulk_todo_update_assignto" id="bulk-todo-assignto-update" value="1" disabled>
                                            </td>

                                            <td>
                                                <label class="form-check-label" for="bulk-todo-delete">Bulk Delete</label>
                                                <input class="form-check-input groups12" type="checkbox" name="bulk_todo_delete" id="bulk-todo-delete" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="10">
                                                <label class="form-check-label" for="deal-pipeline">Deal Pipeline</label>
                                                <input class="form-check-input" type="checkbox" name="deal_pipeline" value="1" id="deal-pipeline">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="deal-add">Add</label>
                                                <input class="form-check-input groupsDeal" type="checkbox" name="deal_add" id="deal-add" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="deal-view">View All</label>
                                                <input class="form-check-input groupsDeal" type="checkbox" name="deal_view" id="deal-view" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="deal-edit">Edit</label>
                                                <input class="form-check-input groupsDeal" type="checkbox" name="deal_edit" id="deal-edit" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="deal-delete">Delete</label>
                                                <input class="form-check-input groupsDeal" type="checkbox" name="deal_delete" id="deal-delete" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="deal-stage-update">Update Deal Stage</label>
                                                <input class="form-check-input groupsDeal" type="checkbox" name="deal_update_stage" id="deal-stage-update" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="deal-recruit-status-update">Update Recruit Status</label>
                                                <input class="form-check-input groupsDeal" type="checkbox" name="deal_update_recruit_status" id="deal-recruit-status-update" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="deal-delete-files">Delete Files</label>
                                                <input class="form-check-input groupsDeal" type="checkbox" name="deal_delete_files" id="deal-delete-files" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="6">
                                                <label class="form-check-label" for="bookings">Bookings</label>
                                                <input class="form-check-input" type="checkbox" name="bookings" value="1" id="bookings">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="confirm-booking">Confirm Booking</label>
                                                <input class="form-check-input groups1" type="checkbox" name="booking_confirm" id="confirm-booking" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-visa-details">Add Visa Details</label>
                                                <input class="form-check-input groups1" type="checkbox" name="add_visa_details" id="add-visa-details" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-booking">View All</label>
                                                <input class="form-check-input groups1" type="checkbox" name="view_booking" id="view-booking" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-payment">Add Payment</label>
                                                <input class="form-check-input groups1" type="checkbox" name="add_payment" id="add-payment" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="cancel-booking">Cancel Booking</label>
                                                <input class="form-check-input groups1" type="checkbox" name="cancel_booking" id="cancel-booking" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="replace-booking">Replace Candidate</label>
                                                <input class="form-check-input groups1" type="checkbox" name="replace_candidate" id="replace-booking" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="2">
                                                <label class="form-check-label" for="employer">Employer</label>
                                                <input class="form-check-input" type="checkbox" name="employer" value="1" id="employer">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="view-employer">View All Employer</label>
                                                <input class="form-check-input groups2" type="checkbox" name="view_employer" id="view-employer" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-employer">Delete Employer</label>
                                                <input class="form-check-input groups2" type="checkbox" name="delete_employer" id="delete-employer" value="1" disabled>
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="4">
                                                <label class="form-check-label" for="employerplus">Employer Plus</label>
                                                <input class="form-check-input" type="checkbox" name="employerplus" value="1" id="employerplus">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="add-employerplus">Add Employer Plus</label>
                                                <input class="form-check-input groups13" type="checkbox" name="employerplus_add" id="add-employerplus" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-employerplus">View All Employer Plus</label>
                                                <input class="form-check-input groups13" type="checkbox" name="employerplus_view" id="view-employerplus" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-employerplus">Edit Employer Plus</label>
                                                <input class="form-check-input groups13" type="checkbox" name="employerplus_edit" id="edit-employerplus" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-employerplus">Delete Employer Plus</label>
                                                <input class="form-check-input groups13" type="checkbox" name="employerplus_delete" id="delete-employerplus" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="7">
                                                <label class="form-check-label" for="candidate">Candidate</label>
                                                <input class="form-check-input" type="checkbox" name="candidate" value="1" id="candidate">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="add-candidate">Add Candidate</label>
                                                <input class="form-check-input groups3" type="checkbox" name="add_candidate" id="add-candidate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-candidate">Edit Candidate</label>
                                                <input class="form-check-input groups3" type="checkbox" name="edit_candidate" id="edit-candidate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-candidate">View All Candidate</label>
                                                <input class="form-check-input groups3" type="checkbox" name="view_candidate" id="view-candidate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-candidate">Delete Candidate</label>
                                                <input class="form-check-input groups3" type="checkbox" name="delete_candidate" id="delete-candidate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="publish-candidate">Publish</label>
                                                <input class="form-check-input groups3" type="checkbox" name="publish_candidate" id="publish-candidate" value="1" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="cv-b2b">CV B2B</label>
                                                <input class="form-check-input groups3" type="checkbox" name="cv_b2b" id="cv-b2b" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="cv-b2c">CV B2C</label>
                                                <input class="form-check-input groups3" type="checkbox" name="cv_b2c" id="cv-b2c" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="upload-docs">Upload Docs</label>
                                                <input class="form-check-input groups3" type="checkbox" name="upload_docs" id="upload-docs" value="1" disabled>
                                            </td>
                                            <td>
                                                <label for="cand-delete-file" class="form-check-label">Delete File</label>
                                                <input class="form-check-input groups3" type="checkbox" name="cand_delete_file" id="cand-delete-file" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="candidate-status">Candidate Status</label>
                                                <input class="form-check-input groups3" type="checkbox" name="candidate_status" id="candidate-status" value="1" disabled>
                                            </td>
                                             <td>
                                                <label class="form-check-label" for="candidate-reset-status">Candidate Reset Status</label>
                                                <input class="form-check-input groups3" type="checkbox" name="candidate_reset_status" id="candidate-reset-status" value="1" disabled>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="add-service-charge">Add Service Charge</label>
                                                <input class="form-check-input groups3" type="checkbox" name="add_service_charge" id="add-service-charge" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-service-charge">Edit Service Charge</label>
                                                <input class="form-check-input groups3" type="checkbox" name="edit_service_charge" id="edit-service-charge" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-service-charge">Delete Service Charge</label>
                                                <input class="form-check-input groups3" type="checkbox" name="delete_service_charge" id="delete-service-charge" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="update-status-service-charge">Service Charge Status Update</label>
                                                <input class="form-check-input groups3" type="checkbox" name="updt_status_service_charge" id="update-status-service-charge" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-cand-payment">Edit Candidate Payment</label>
                                                <input class="form-check-input groups3" type="checkbox" name="edit_cand_payment" id="edit-cand-payment" value="1" disabled>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="delete-cand-payment">Delete Candidate Payment</label>
                                                <input class="form-check-input groups3" type="checkbox" name="delete_cand_payment" id="delete-cand-payment" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="cand-edit-careoff">Edit Careoff</label>
                                                <input class="form-check-input groups3" type="checkbox" name="cand_edit_careoff" id="cand-edit-careoff" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="cand-edit-associate">Edit Associate</label>
                                                <input class="form-check-input groups3" type="checkbox" name="cand_edit_associate" id="cand-edit-associate" value="1" disabled>
                                            </td>

                                            <td>
                                                <label class="form-check-label" for="cand-add-edit">Add Edit</label>
                                                <input class="form-check-input groups3" type="checkbox" name="cand_add_edit" id="cand-add-edit" value="1" disabled>
                                            </td>

                                            <td>
                                                <label class="form-check-label" for="cand-sourcing-date">Sourcing Date</label>
                                                <input class="form-check-input groups3" type="checkbox" name="cand_sourcing_date" id="cand-sourcing-date" value="1" disabled>
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="9">
                                                <label class="form-check-label" for="testimonial">Testimonial</label>
                                                <input class="form-check-input" type="checkbox" name="testimonial" value="1" id="testimonial">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="add-testimonial">Generate Link</label>
                                                <input class="form-check-input groups18" type="checkbox" name="add_testimonial" id="add-testimonial" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-testimonial">Edit</label>
                                                <input class="form-check-input groups18" type="checkbox" name="edit_testimonial" id="edit-testimonial" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-testimonial">View All</label>
                                                <input class="form-check-input groups18" type="checkbox" name="view_testimonial" id="view-testimonial" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="copy-link-testimonial">Copy Link</label>
                                                <input class="form-check-input groups18" type="checkbox" name="copy_link_testimonial" id="copy-link-testimonial" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-testimonial">Delete</label>
                                                <input class="form-check-input groups18" type="checkbox" name="delete_testimonial" id="delete-testimonial" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="approval-testimonial">Approval Candidate</label>
                                                <input class="form-check-input groups18" type="checkbox" name="approval_testimonial" id="approval-testimonial" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="payment-testimonial">Mark Payment</label>
                                                <input class="form-check-input groups18" type="checkbox" name="payment_testimonial" id="payment-testimonial" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="video-received-testimonial">Video Received</label>
                                                <input class="form-check-input groups18" type="checkbox" name="video_received_testimonial" id="video-received-testimonial" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="social-media-testimonial">Social Media Post</label>
                                                <input class="form-check-input groups18" type="checkbox" name="social_media_testimonial" id="social-media-testimonial" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="7">
                                                <label class="form-check-label" for="google_review">Google Review</label>
                                                <input class="form-check-input" type="checkbox" name="google_review" value="1" id="google_review">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="add-google_review">Add</label>
                                                <input class="form-check-input groups19" type="checkbox" name="add_google_review" id="add-google_review" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-google_review">Edit</label>
                                                <input class="form-check-input groups19" type="checkbox" name="edit_google_review" id="edit-google_review" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-google_review">View All</label>
                                                <input class="form-check-input groups19" type="checkbox" name="view_google_review" id="view-google_review" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-google_review">Delete</label>
                                                <input class="form-check-input groups19" type="checkbox" name="delete_google_review" id="delete-google_review" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="approval-google_review">Approval</label>
                                                <input class="form-check-input groups19" type="checkbox" name="approval_google_review" id="approval-google_review" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="payment-google_review">Mark Payment</label>
                                                <input class="form-check-input groups19" type="checkbox" name="payment_google_review" id="payment-google_review" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="10">
                                                <label class="form-check-label" for="associate">Associate</label>
                                                <input class="form-check-input" type="checkbox" name="associate" value="1" id="associate">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="add-associate">Add</label>
                                                <input class="form-check-input groups7" type="checkbox" name="add_associate" id="add-associate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-associate">Edit</label>
                                                <input class="form-check-input groups7" type="checkbox" name="edit_associate" id="edit-associate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-associate">View All</label>
                                                <input class="form-check-input groups7" type="checkbox" name="view_associate" id="view-associate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-associate">Delete</label>
                                                <input class="form-check-input groups7" type="checkbox" name="delete_associate" id="delete-associate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="publish-associate">Publish Stage</label>
                                                <input class="form-check-input groups7" type="checkbox" name="publish_associate" id="publish-associate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label for="active-deactive-associate" class="form-check-label">Active / Deactive</label>
                                                <input class="form-check-input groups7" type="checkbox" name="activedeactive_associate" id="active-deactive-associate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="verified-not-verified-associate">Verified / Not Verified</label>
                                                <input class="form-check-input groups7" type="checkbox" name="verified_notverified_associate" id="verified-not-verified-associate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="primary-no-val-associate">Primary Mobile Validation</label>
                                                <input class="form-check-input groups7" type="checkbox" name="primary_no_val_associate" id="primary-no-val-associate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="secondary-no-val-associate">Secondary Mobile Validation</label>
                                                <input class="form-check-input groups7" type="checkbox" name="secondary_no_val_associate" id="secondary-no-val-associate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="email-associate">Email Validation</label>
                                                <input class="form-check-input groups7" type="checkbox" name="email_associate" id="email-associate" value="1" disabled>
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="2">
                                                <label class="form-check-label" for="chat">Chat (Internal Messaging)</label>
                                                <input class="form-check-input" type="checkbox" name="chat" value="1" id="chat">
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="7">
                                                <label class="form-check-label" for="contactp">Contact+</label>
                                                <input class="form-check-input" type="checkbox" name="contactp" value="1" id="contactp">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="add-contactp">Add</label>
                                                <input class="form-check-input groups9" type="checkbox" name="add_contactp" id="add-contactp" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-contactp">Edit</label>
                                                <input class="form-check-input groups9" type="checkbox" name="edit_contactp" id="edit-contactp" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-contactp">View All</label>
                                                <input class="form-check-input groups9" type="checkbox" name="view_contactp" id="view-contactp" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-contactp">Delete</label>
                                                <input class="form-check-input groups9" type="checkbox" name="delete_contactp" id="delete-contactp" value="1" disabled>
                                            </td>
                                            <td>
                                                <label for="lead-onwer-transfer" class="form-check-label">Lead Owner Transfer</label>
                                                <input type="checkbox" name="lead_owner_transfer" id="lead-onwer-transfer" class="form-check-input groups9" value="1" disabled>
                                            </td>
                                            <td>
                                                <label for="careoff-transfer" class="form-check-label">Careoff Transfer</label>
                                                <input type="checkbox" name="careoff_transfer" id="careoff-transfer" class="form-check-input groups9" value="1" disabled>
                                            </td>
                                            <td>
                                                <label for="contact-bulk-whatsapp-send" class="form-check-label">Bulk Whatsapp Send</label>
                                                <input type="checkbox" name="contact_bulk_whatsapp_send" id="contact-bulk-whatsapp-send" class="form-check-input groups9" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="2">
                                                <label class="form-check-label" for="client">Customer</label>
                                                <input class="form-check-input" type="checkbox" name="client" value="1" id="client">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="view-client">View All Customer</label>
                                                <input class="form-check-input groups4" type="checkbox" name="view_client" id="view-client" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-client">Delete Customer</label>
                                                <input class="form-check-input groups4" type="checkbox" name="delete_client" id="delete-client" value="1" disabled>
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="4">
                                                <label class="form-check-label" for="partner">Recruitment Partner</label>
                                                <input class="form-check-input" type="checkbox" name="partner" value="1" id="partner">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="add-partner">Add Partner</label>
                                                <input class="form-check-input groups5" type="checkbox" name="add_partner" id="add-partner" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-partner">Edit Partner</label>
                                                <input class="form-check-input groups5" type="checkbox" name="edit_partner" id="edit-partner" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-partner">View All Partner</label>
                                                <input class="form-check-input groups5" type="checkbox" name="view_partner" id="view-partner" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-partner">Delete Partner</label>
                                                <input class="form-check-input groups5" type="checkbox" name="delete_partner" id="delete-partner" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="4">
                                                <label class="form-check-label" for="file-manager">File Manager</label>
                                                <input class="form-check-input" type="checkbox" name="file_manager" value="1" id="file-manager">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="add-file-manager">Create Folder</label>
                                                <input class="form-check-input groups17" type="checkbox" name="add_file_manager" id="add-file-manager" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="upload-file-manager">Upload</label>
                                                <input class="form-check-input groups17" type="checkbox" name="upload_file_manager" id="upload-file-manager" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="download-file-manager">Download</label>
                                                <input class="form-check-input groups17" type="checkbox" name="download_file_manager" id="download-file-manager" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="preview-file-manager">Preview</label>
                                                <input class="form-check-input groups17" type="checkbox" name="preview_file_manager" id="preview-file-manager" value="1" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="rename-file-manager">Rename</label>
                                                <input class="form-check-input groups17" type="checkbox" name="rename_file_manager" id="rename-file-manager" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="move-file-manager">Move</label>
                                                <input class="form-check-input groups17" type="checkbox" name="move_file_manager" id="move-file-manager" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-file-manager">Delete</label>
                                                <input class="form-check-input groups17" type="checkbox" name="delete_file_manager" id="delete-file-manager" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="manage-all-file-manager">Manage All Users' Files</label>
                                                <input class="form-check-input groups17" type="checkbox" name="manage_all_file_manager" id="manage-all-file-manager" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="7">
                                                    <label class="form-check-label" for="settings">Settings</label>
                                                    <input class="form-check-input" type="checkbox" name="settings" value="1" id="settings">
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="7">
                                                        <label class="form-check-label" for="access-setting">Access</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="access_setting" id="access-setting" value="1" disabled>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label class="form-check-label" for="access_allowed_ip">Allowed IP</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="access_allowed_ip" id="access_allowed_ip" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="access_allowed_ip_revoke">Revoke</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="access_allowed_ip_revoke" id="access_allowed_ip_revoke" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="access_allowed_ip_approved_by">Approved By</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="access_allowed_ip_approved_by" id="access_allowed_ip_approved_by" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="access_allowed_ip_delete">Delete</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="access_allowed_ip_delete" id="access_allowed_ip_delete" value="1" disabled>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="7">
                                                        <label class="form-check-label" for="todo-setting">Todo</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="todo_setting" id="todo-setting" value="1" disabled>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label class="form-check-label" for="todo-label">Todo Label</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="todo_label" id="todo-label" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="todo-label-add">Todo Label Add</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="todo_label_add" id="todo-label-add" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="todo-label-view">Todo Label View All</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="todo_label_view" id="todo-label-view" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="todo-label-edit">Todo Label Edit</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="todo_label_edit" id="todo-label-edit" value="1" disabled>
                                                    </td>
                                                    <td colspan="3">
                                                        <label class="form-check-label" for="todo-label-delete">Todo Label Delete</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="todo_label_delete" id="todo-label-delete" value="1" disabled>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label class="form-check-label" for="department">Department</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="department" id="department" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="department-add">Department Add</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="department_add" id="department-add" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="department-view">Department View All</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="department_view" id="department-view" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="department-edit">Department Edit</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="department_edit" id="department-edit" value="1" disabled>
                                                    </td>
                                                    <td colspan="3">
                                                        <label class="form-check-label" for="department-delete">Department Delete</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="department_delete" id="department-delete" value="1" disabled>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="7">
                                                        <label for="contact-plus-setting" class="form-check-label">Contact Plus</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="contact_plus_setting" id="contact-plus-setting" value="1" disabled>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <label for="industries" class="form-check-label">Industries</label>
                                                        <input type="checkbox" name="industries" id="industries" class="form-check-input groups6" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label for="add-industries" class="form-check-label">Add Industries</label>
                                                        <input type="checkbox" name="add_industries" id="add-industries" class="form-check-input groups6" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label for="edit-industries" class="form-check-label">Edit Industries</label>
                                                        <input type="checkbox" name="edit_industries" id="edit-industries" class="form-check-input groups6" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label for="view-industries" class="form-check-label">View Industries</label>
                                                        <input type="checkbox" name="view_industries" id="view-industries" class="form-check-input groups6" value="1" disabled>
                                                    </td>
                                                    <td colspan="3">
                                                        <label for="delete-industries" class="form-check-label">Delete Industries</label>
                                                        <input type="checkbox" name="delete_industries" id="delete-industries" class="form-check-input groups6" value="1" disabled>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <label class="form-check-label" for="businesstype">Business Type</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="businesstype" id="businesstype" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="add-businesstype">Add</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="add_businesstype" id="add-businesstype" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="edit-businesstype">Edit</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="edit_businesstype" id="edit-businesstype" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="view-businesstype">View All</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="view_businesstype" id="view-businesstype" value="1" disabled>
                                                    </td>
                                                    <td colspan="3">
                                                        <label class="form-check-label" for="delete-businesstype">Delete</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="delete_businesstype" id="delete-businesstype" value="1" disabled>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <label class="form-check-label" for="groupcp">Group</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="groupcp" id="groupcp" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="add-groupcp">Add</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="add_groupcp" id="add-groupcp" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="edit-groupcp">Edit</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="edit_groupcp" id="edit-groupcp" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="view-groupcp">View All</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="view_groupcp" id="view-groupcp" value="1" disabled>
                                                    </td>
                                                    <td colspan="3">
                                                        <label class="form-check-label" for="delete-groupcp">Delete</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="delete_groupcp" id="delete-groupcp" value="1" disabled>
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td colspan="7">
                                                        <label class="form-check-label" for="websiteconfig">Website Configuration</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="websiteconfig" id="websiteconfig" value="1" disabled>
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td>
                                                        <label class="form-check-label" for="carknown">Car Known</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="carknown" id="carknown" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="add-carknown">Add Car Known</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="add_car_known" id="add-carknown" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="edit-carknown">Edit Car Known</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="edit_car_known" id="edit-carknown" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="view-carknown">View All Car Known</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="view_car_known" id="view-carknown" value="1" disabled>
                                                    </td>
                                                    <td colspan="3">
                                                        <label class="form-check-label" for="delete-carknown">Delete Car Known</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="delete_car_known" id="delete-carknown" value="1" disabled>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <label class="form-check-label" for="personalise-class">Personalise Class</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="personalise_class" id="personalise-class" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="add-personalise-class">Add Personalise Class</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="add_personalise_class" id="add-personalise-class" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="edit-personalise-class">Edit Personalise Class</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="edit_personalise_class" id="edit-personalise-class" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="view-personalise-class">View All Personalise Class</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="view_personalise_class" id="view-personalise-class" value="1" disabled>
                                                    </td>
                                                    <td colspan="3">
                                                        <label class="form-check-label" for="delete-personalise-class">Delete Personalise Class</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="delete_personalise_class" id="delete-personalise-class" value="1" disabled>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="7">
                                                        <label class="form-check-label" for="storage-usage-setting">Storage Usage</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="storage_usage_setting" id="storage-usage-setting" value="1" disabled>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="7">
                                                        <label class="form-check-label" for="db-backup-setting">DB Backup</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="db_backup_setting" id="db-backup-setting" value="1" disabled>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <label class="form-check-label" for="template">Template</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="template" id="template" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="add-template">Add Template</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="add_template" id="add-template" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="edit-template">Edit Template</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="edit_template" id="edit-template" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="view-template">View All Template</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="view_template" id="view-template" value="1" disabled>
                                                    </td>
                                                    <td colspan="3">
                                                        <label class="form-check-label" for="delete-template">Delete Template</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="delete_template" id="delete-template" value="1" disabled>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="7">
                                                        <label class="form-check-label" for="setup">Setup</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="setup" id="setup" value="1" disabled>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <label class="form-check-label" for="mailsetup">Mail Setup</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="mailsetup" id="mailsetup" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="add-mailsetup">Add Mail Setup</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="add_mailsetup" id="add-mailsetup" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="edit-mailsetup">Edit Mail Setup</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="edit_mailsetup" id="edit-mailsetup" value="1" disabled>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label" for="view-mailsetup">View All Mail Setup</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="view_mailsetup" id="view-mailsetup" value="1" disabled>
                                                    </td>
                                                    <td colspan="3">
                                                        <label class="form-check-label" for="delete-mailsetup">Delete Mail Setup</label>
                                                        <input class="form-check-input groups6" type="checkbox" name="delete_mailsetup" id="delete-mailsetup" value="1" disabled>
                                                    </td>
                                                </tr>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="5">
                                                <label class="form-check-label" for="finance">Finance</label>
                                                <input class="form-check-input" type="checkbox" name="finance" value="1" id="finance">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5">
                                                <label class="form-check-label" for="candidate-finance">Candidate</label>
                                                <input class="form-check-input groups14" type="checkbox" name="candidate_finance" id="candidate-finance" value="1" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5">
                                                <label class="form-check-label" for="transaction-finance">Transaction</label>
                                                <input class="form-check-input groups14" type="checkbox" name="transaction_finance" id="transaction-finance" value="1" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5">
                                                <label class="form-check-label" for="transaction-client">Client</label>
                                                <input class="form-check-input groups14" type="checkbox" name="transaction_client" id="transaction-client" value="1" disabled>
                                            </td>

                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="finance-sale-invoices">Sale Invoices</label>
                                                <input class="form-check-input groups14" type="checkbox" name="finance_sale_invoices" id="finance-sale-invoices" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="finance-sale-invoices-create">Create Sale Invoices</label>
                                                <input class="form-check-input groups14" type="checkbox" name="finance_sale_invoices_create" id="finance-sale-invoices-create" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="finance-sale-invoices-edit">Edit</label>
                                                <input class="form-check-input groups14" type="checkbox" name="finance_sale_invoices_edit" id="finance-sale-invoices-edit" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="finance-sale-invoices-view">View All</label>
                                                <input class="form-check-input groups14" type="checkbox" name="finance_sale_invoices_view" id="finance-sale-invoices-view" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="finance-sale-invoices-delete">Delete</label>
                                                <input class="form-check-input groups14" type="checkbox" name="finance_sale_invoices_delete" id="finance-sale-invoices-delete" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="finance-payment">Payment</label>
                                                <input class="form-check-input groups14" type="checkbox" name="finance_payment" id="finance-payment" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="finance-payment-add">Add</label>
                                                <input class="form-check-input groups14" type="checkbox" name="finance_payment_add" id="finance-payment-add" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="finance-payment-edit">Edit</label>
                                                <input class="form-check-input groups14" type="checkbox" name="finance_payment_edit" id="finance-payment-edit" value="1" disabled>
                                            </td>

                                            <td colspan="2">
                                                <label class="form-check-label" for="finance-payment-delete">Delete</label>
                                                <input class="form-check-input groups14" type="checkbox" name="finance_payment_delete" id="finance-payment-delete" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="expense">Expense</label>
                                                <input class="form-check-input groups14" type="checkbox" name="expense" id="expense" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-expense">Add</label>
                                                <input class="form-check-input groups14" type="checkbox" name="add_expense" id="add-expense" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-expense">Edit</label>
                                                <input class="form-check-input groups14" type="checkbox" name="edit_expense" id="edit-expense" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-expense">View All</label>
                                                <input class="form-check-input groups14" type="checkbox" name="view_expense" id="view-expense" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-expense">Delete</label>
                                                <input class="form-check-input groups14" type="checkbox" name="delete_expense" id="delete-expense" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="hr-management">HR Management</label>
                                                <input class="form-check-input groups14" type="checkbox" name="hr_management" id="hr-management" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="hr-dashboard">Dashboard</label>
                                                <input class="form-check-input groups14" type="checkbox" name="hr_dashboard" id="hr-dashboard" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="hr-attendance">Attendance</label>
                                                <input class="form-check-input groups14" type="checkbox" name="hr_attendance" id="hr-attendance" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="hr-payroll">Payroll</label>
                                                <input class="form-check-input groups14" type="checkbox" name="hr_payroll" id="hr-payroll" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="hr-settings">Settings</label>
                                                <input class="form-check-input groups14" type="checkbox" name="hr_settings" id="hr-settings" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="hr-attendance-view-all">View All</label>
                                                <input class="form-check-input groups14" type="checkbox" name="hr_attendance_view_all" id="hr-attendance-view-all" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="fund-advance">Fund & Advance</label>
                                                <input class="form-check-input groups14" type="checkbox" name="fund_advance" id="fund-advance" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="fund-advance-create">Create Transaction</label>
                                                <input class="form-check-input groups14" type="checkbox" name="fund_advance_create" id="fund-advance-create" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="fund-advance-edit">Edit Transaction</label>
                                                <input class="form-check-input groups14" type="checkbox" name="fund_advance_edit" id="fund-advance-edit" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="fund-advance-delete">Delete/Cancel Transaction</label>
                                                <input class="form-check-input groups14" type="checkbox" name="fund_advance_delete" id="fund-advance-delete" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="fund-advance-settlement-create">Create Settlement</label>
                                                <input class="form-check-input groups14" type="checkbox" name="fund_advance_settlement_create" id="fund-advance-settlement-create" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="fund-advance-settlement-view">View Settlement</label>
                                                <input class="form-check-input groups14" type="checkbox" name="fund_advance_settlement_view" id="fund-advance-settlement-view" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="fund-advance-adjustment-create">Create Adjustment</label>
                                                <input class="form-check-input groups14" type="checkbox" name="fund_advance_adjustment_create" id="fund-advance-adjustment-create" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="fund-advance-ledger-view">View Ledger</label>
                                                <input class="form-check-input groups14" type="checkbox" name="fund_advance_ledger_view" id="fund-advance-ledger-view" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="fund-advance-reports-view">View Reports</label>
                                                <input class="form-check-input groups14" type="checkbox" name="fund_advance_reports_view" id="fund-advance-reports-view" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="fund-advance-export">Export</label>
                                                <input class="form-check-input groups14" type="checkbox" name="fund_advance_export" id="fund-advance-export" value="1" disabled>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="5">
                                            <label class="form-check-label" for="dynamic">Dynamic</label>
                                                <input class="form-check-input" type="checkbox" name="dynamic" value="1" id="dynamic">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="staff">Staff</label>
                                                <input class="form-check-input groups8" type="checkbox" name="staff" id="staff" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-staff">Add Staff</label>
                                                <input class="form-check-input groups8" type="checkbox" name="add_staff" id="add-staff" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-staff">Edit Staff</label>
                                                <input class="form-check-input groups8" type="checkbox" name="edit_staff" id="edit-staff" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-staff">View All Staff</label>
                                                <input class="form-check-input groups8" type="checkbox" name="view_staff" id="view-staff" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-staff">Delete Staff</label>
                                                <input class="form-check-input groups8" type="checkbox" name="delete_staff" id="delete-staff" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="branch">Branch</label>
                                                <input class="form-check-input groups8" type="checkbox" name="branch" id="branch" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-branch">Add Branch</label>
                                                <input class="form-check-input groups8" type="checkbox" name="add_branch" id="add-branch" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-branch">Edit Branch</label>
                                                <input class="form-check-input groups8" type="checkbox" name="edit_branch" id="edit-branch" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-branch">View All Branch</label>
                                                <input class="form-check-input groups8" type="checkbox" name="view_branch" id="view-branch" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-branch">Delete Branch</label>
                                                <input class="form-check-input groups8" type="checkbox" name="delete_branch" id="delete-branch" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="profession">Profession</label>
                                                <input class="form-check-input groups8" type="checkbox" name="profession" id="profession" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-profession">Add Profession</label>
                                                <input class="form-check-input groups8" type="checkbox" name="add_profession" id="add-profession" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-profession">Edit Profession</label>
                                                <input class="form-check-input groups8" type="checkbox" name="edit_profession" id="edit-profession" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-profession">View All Profession</label>
                                                <input class="form-check-input groups8" type="checkbox" name="view_profession" id="view-profession" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-profession">Delete Profession</label>
                                                <input class="form-check-input groups8" type="checkbox" name="delete_profession" id="delete-profession" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="placeofissue">Place of Issue</label>
                                                <input class="form-check-input groups8" type="checkbox" name="placeofissue" id="placeofissue" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-placeofissue">Add Place of Issue</label>
                                                <input class="form-check-input groups8" type="checkbox" name="add_placeofissue" id="add-placeofissue" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-placeofissue">Edit Place of Issue</label>
                                                <input class="form-check-input groups8" type="checkbox" name="edit_placeofissue" id="edit-placeofissue" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-placeofissue">View All Place of Issue</label>
                                                <input class="form-check-input groups8" type="checkbox" name="view_placeofissue" id="view-placeofissue" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-placeofissue">Delete Place of Issue</label>
                                                <input class="form-check-input groups8" type="checkbox" name="delete_placeofissue" id="delete-placeofissue" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="country">Country</label>
                                                <input class="form-check-input groups8" type="checkbox" name="country" id="country" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-country">Add Country</label>
                                                <input class="form-check-input groups8" type="checkbox" name="add_country" id="add-country" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-country">Edit Country</label>
                                                <input class="form-check-input groups8" type="checkbox" name="edit_country" id="edit-country" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-country">View All Country</label>
                                                <input class="form-check-input groups8" type="checkbox" name="view_country" id="view-country" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-country">Delete Country</label>
                                                <input class="form-check-input groups8" type="checkbox" name="delete_country" id="delete-country" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="region">Region</label>
                                                <input class="form-check-input groups8" type="checkbox" name="region" id="region" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-region">Add Region</label>
                                                <input class="form-check-input groups8" type="checkbox" name="add_region" id="add-region" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-region">Edit Region</label>
                                                <input class="form-check-input groups8" type="checkbox" name="edit_region" id="edit-region" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-region">View All Region</label>
                                                <input class="form-check-input groups8" type="checkbox" name="view_region" id="view-region" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-region">Delete Region</label>
                                                <input class="form-check-input groups8" type="checkbox" name="delete_region" id="delete-region" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="city">City</label>
                                                <input class="form-check-input groups8" type="checkbox" name="city" id="city" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-city">Add City</label>
                                                <input class="form-check-input groups8" type="checkbox" name="add_city" id="add-city" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-city">Edit City</label>
                                                <input class="form-check-input groups8" type="checkbox" name="edit_city" id="edit-city" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-city">View All City</label>
                                                <input class="form-check-input groups8" type="checkbox" name="view_city" id="view-city" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-city">Delete City</label>
                                                <input class="form-check-input groups8" type="checkbox" name="delete_city" id="delete-city" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="expworklocation">Expected Work Location</label>
                                                <input class="form-check-input groups8" type="checkbox" name="expworklocation" id="expworklocation" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-expworklocation">Add</label>
                                                <input class="form-check-input groups8" type="checkbox" name="add_expworklocation" id="add-expworklocation" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-expworklocation">Edit</label>
                                                <input class="form-check-input groups8" type="checkbox" name="edit_expworklocation" id="edit-expworklocation" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-expworklocation">View All</label>
                                                <input class="form-check-input groups8" type="checkbox" name="view_expworklocation" id="view-expworklocation" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-expworklocation">Delete</label>
                                                <input class="form-check-input groups8" type="checkbox" name="delete_expworklocation" id="delete-expworklocation" value="1" disabled>
                                            </td>
                                        </tr>



                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="7">
                                                <label for="whatsapp-plus" class="form-check-label">Whatsapp Meta</label>
                                                <input type="checkbox" name="whatsapp_plus" class="form-check-input" id="whatsapp-plus" value="1">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="meta-whatsapp-template">Meta Whatsapp Template</label>
                                                <input type="checkbox" name="meta_whatsapp_template" class="form-check-input groups10" id="meta-whatsapp-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-meta-whatsapp-template">Add Meta Whatsapp Template</label>
                                                <input type="checkbox" name="add_meta_whatsapp_template" class="form-check-input groups10" id="add-meta-whatsapp-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-meta-whatsapp-template">View All Meta Whatsapp Template</label>
                                                <input type="checkbox" name="view_meta_whatsapp_template" class="form-check-input groups10" id="view-meta-whatsapp-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-meta-whatsapp-template">Edit Meta Whatsapp Template</label>
                                                <input type="checkbox" name="edit_meta_whatsapp_template" class="form-check-input groups10" id="edit-meta-whatsapp-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-meta-whatsapp-template">Delete Meta Whatsapp Template</label>
                                                <input type="checkbox" name="delete_meta_whatsapp_template" class="form-check-input groups10" id="delete-meta-whatsapp-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="status-meta-whatsapp-template">Status Meta Whatsapp Template</label>
                                                <input type="checkbox" name="status_meta_whatsapp_template" class="form-check-input groups10" id="status-meta-whatsapp-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="public-meta-whatsapp-template">Public Meta Whatsapp Template</label>
                                                <input type="checkbox" name="public_meta_whatsapp_template" class="form-check-input groups10" id="public-meta-whatsapp-template" value="1" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="meta-whatsapp-campaign">Meta Whatsapp Campaign</label>
                                                <input type="checkbox" name="meta_whatsapp_campaign" class="form-check-input groups10" id="meta-whatsapp-campaign" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="meta-whatsapp-campaign-add">Add Meta Whatsapp Campaign</label>
                                                <input type="checkbox" name="meta_whatsapp_campaign_add" class="form-check-input groups10" id="meta-whatsapp-campaign-add" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="meta-whatsapp-campaign-view">Meta Whatsapp Campaign View All</label>
                                                <input type="checkbox" name="meta_whatsapp_campaign_view" class="form-check-input groups10" id="meta-whatsapp-campaign-view" value="1" disabled>
                                            </td>
                                            <td>
                                                <label for="meta-whatsapp-campaign-create-user" class="form-check-label">Create User Campaign</label>
                                                <input type="checkbox" class="form-check-input groups10" id="meta-whatsapp-campaign-create-user" name="meta_whatsapp_campaign_create_user" value="1" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="meta-automation">Meta Automation</label>
                                                <input type="checkbox" name="meta_automation" class="form-check-input groups10" id="meta-automation" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-meta-automation">Add Meta Automation</label>
                                                <input type="checkbox" name="add_meta_automation" class="form-check-input groups10" id="add-meta-automation" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-meta-automation">View All Meta Automation</label>
                                                <input type="checkbox" name="view_meta_automation" class="form-check-input groups10" id="view-meta-automation" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-meta-automation">Edit Meta Automation</label>
                                                <input type="checkbox" name="edit_meta_automation" class="form-check-input groups10" id="edit-meta-automation" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-meta-automation">Delete Meta Automation</label>
                                                <input type="checkbox" name="delete_meta_automation" class="form-check-input groups10" id="delete-meta-automation" value="1" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7">
                                                <label for="image-host" class="form-check-label">Image Host</label>
                                                <input type="checkbox" name="image_host" id="image-host" class="form-check-input groups10" value="1" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="whatsapp-meta-api">Whatsapp Meta API</label>
                                                <input class="form-check-input groups10" type="checkbox" name="whatsapp_meta_api" id="whatsapp-meta-api" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-whatsapp-meta-api">Add Whatsapp Meta API</label>
                                                <input class="form-check-input groups10" type="checkbox" name="add_whatsapp_meta_api" id="add-whatsapp-meta-api" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-whatsapp-meta-api">Edit Whatsapp Meta API</label>
                                                <input class="form-check-input groups10" type="checkbox" name="edit_whatsapp_meta_api" id="edit-whatsapp-meta-api" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-whatsapp-meta-api">View All Whatsapp Meta API</label>
                                                <input class="form-check-input groups10" type="checkbox" name="view_whatsapp_meta_api" id="view-whatsapp-meta-api" value="1" disabled>
                                            </td>
                                            <td colspan="3">
                                                <label class="form-check-label" for="delete-whatsapp-meta-api">Delete Whatsapp Meta API</label>
                                                <input class="form-check-input groups10" type="checkbox" name="delete_whatsapp_meta_api" id="delete-whatsapp-meta-api" value="1" disabled>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="whatsapp-url">Whatsapp URL</label>
                                                <input class="form-check-input groups10" type="checkbox" name="whatsapp_url" id="whatsapp-url" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-whatsapp-url">Add Whatsapp Meta API</label>
                                                <input class="form-check-input groups10" type="checkbox" name="add_whatsapp_url" id="add-whatsapp-url" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-whatsapp-url">Edit Whatsapp Meta API</label>
                                                <input class="form-check-input groups10" type="checkbox" name="edit_whatsapp_url" id="edit-whatsapp-url" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-whatsapp-url">View All Whatsapp Meta API</label>
                                                <input class="form-check-input groups10" type="checkbox" name="view_whatsapp_url" id="view-whatsapp-url" value="1" disabled>
                                            </td>
                                            <td colspan="3">
                                                <label class="form-check-label" for="delete-whatsapp-url">Delete Whatsapp Meta API</label>
                                                <input class="form-check-input groups10" type="checkbox" name="delete_whatsapp_url" id="delete-whatsapp-url" value="1" disabled>
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="7">
                                                <label for="sms-campaign-module" class="form-check-label">SMS Campaign</label>
                                                <input type="checkbox" name="sms_campaign_module" class="form-check-input" id="sms-campaign-module" value="1">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="sms-api">SMS API</label>
                                                <input type="checkbox" name="sms_api" class="form-check-input group_sms" id="sms-api" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-sms-api">Add SMS API</label>
                                                <input type="checkbox" name="add_sms_api" class="form-check-input group_sms" id="add-sms-api" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-sms-api">Edit SMS API</label>
                                                <input type="checkbox" name="edit_sms_api" class="form-check-input group_sms" id="edit-sms-api" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-sms-api">Delete SMS API</label>
                                                <input type="checkbox" name="delete_sms_api" class="form-check-input group_sms" id="delete-sms-api" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="change-status-sms-api">Change Status SMS API</label>
                                                <input type="checkbox" name="change_status_sms_api" class="form-check-input group_sms" id="change-status-sms-api" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="assign-sms-api">Assign SMS API</label>
                                                <input type="checkbox" name="assign_sms_api" class="form-check-input group_sms" id="assign-sms-api" value="1" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="sms-template">SMS Template</label>
                                                <input type="checkbox" name="sms_template" class="form-check-input group_sms" id="sms-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-sms-template">Add SMS Template</label>
                                                <input type="checkbox" name="add_sms_template" class="form-check-input group_sms" id="add-sms-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-sms-template">Edit SMS Template</label>
                                                <input type="checkbox" name="edit_sms_template" class="form-check-input group_sms" id="edit-sms-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-sms-template">Delete SMS Template</label>
                                                <input type="checkbox" name="delete_sms_template" class="form-check-input group_sms" id="delete-sms-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="change-status-sms-template">Change Status SMS Template</label>
                                                <input type="checkbox" name="change_status_sms_template" class="form-check-input group_sms" id="change-status-sms-template" value="1" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="sms-campaign">SMS Campaign</label>
                                                <input class="form-check-input group_sms" type="checkbox" name="sms_campaign" id="sms-campaign" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-sms-campaign">Add SMS Campaign</label>
                                                <input class="form-check-input group_sms" type="checkbox" name="add_sms_campaign" id="add-sms-campaign" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-sms-campaign">View SMS Campaign</label>
                                                <input class="form-check-input group_sms" type="checkbox" name="view_sms_campaign" id="view-sms-campaign" value="1" disabled>
                                            </td>
                                            <td colspan="3">
                                                <label class="form-check-label" for="delete-sms-campaign">Delete SMS Campaign</label>
                                                <input class="form-check-input group_sms" type="checkbox" name="delete_sms_campaign" id="delete-sms-campaign" value="1" disabled>
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="7">
                                                <label for="whatsapp-normal" class="form-check-label">Whatsapp Business</label>
                                                <input type="checkbox" name="whatsapp_nromal" class="form-check-input" id="whatsapp-normal" value="1">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="whatsapp-template">Whatsapp Template</label>
                                                <input type="checkbox" name="whatsapp_template" class="form-check-input groups11" id="whatsapp-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-whatsapp-template">Add Whatsapp Template</label>
                                                <input type="checkbox" name="add_whatsapp_template" class="form-check-input groups11" id="add-whatsapp-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-whatsapp-template">Edit Whatsapp Template</label>
                                                <input type="checkbox" name="edit_whatsapp_template" class="form-check-input groups11" id="edit-whatsapp-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-whatsapp-template">View All Whatsapp Template</label>
                                                <input type="checkbox" name="view_whatsapp_template" class="form-check-input groups11" id="view-whatsapp-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-whatsapp-template">Delete Whatsapp Template</label>
                                                <input type="checkbox" name="delete_whatsapp_template" class="form-check-input groups11" id="delete-whatsapp-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="status-whatsapp-template">Status Whatsapp Template</label>
                                                <input type="checkbox" name="status_whatsapp_template" class="form-check-input groups11" id="status-whatsapp-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="public-whatsapp-template">Public Whatsapp Template</label>
                                                <input type="checkbox" name="public_whatsapp_template" class="form-check-input groups11" id="public-whatsapp-template" value="1" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="whatsapp-campaign">Whatsapp Campaign</label>
                                                <input type="checkbox" name="whatsapp_campaign" class="form-check-input groups11" id="whatsapp-campaign" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="whatsapp-campaign-add">Whatsapp Campaign Add</label>
                                                <input type="checkbox" name="whatsapp_campaign_add" class="form-check-input groups11" id="whatsapp-campaign-add" value="1" disabled>

                                            </td>
                                            <td colspan="5">
                                                <label class="form-check-label" for="whatsapp-campaign-view">Whatsapp Campaign View All</label>
                                                <input type="checkbox" name="whatsapp_campaign_view" class="form-check-input groups11" id="whatsapp-campaign-view" value="1" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="whatsapp-api">Whatsapp API</label>
                                                <input class="form-check-input groups11" type="checkbox" name="whatsapp_api" id="whatsapp-api" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-whatsapp-api">Add Whatsapp API</label>
                                                <input class="form-check-input groups11" type="checkbox" name="add_whatsapp_api" id="add-whatsapp-api" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-whatsapp-api-for-user">Add Whatsapp API For User</label>
                                                <input class="form-check-input groups11" type="checkbox" name="add_whatsapp_api_for_user" id="add-whatsapp-api-for-user" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-whatsapp-api">Edit Whatsapp API</label>
                                                <input class="form-check-input groups11" type="checkbox" name="edit_whatsapp_api" id="edit-whatsapp-api" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-whatsapp-api">View All Whatsapp API</label>
                                                <input class="form-check-input groups11" type="checkbox" name="view_whatsapp_api" id="view-whatsapp-api" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-whatsapp-api">Delete Whatsapp API</label>
                                                <input class="form-check-input groups11" type="checkbox" name="delete_whatsapp_api" id="delete-whatsapp-api" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="whatsapp-api-test">Test Whatsapp API</label>
                                                <input class="form-check-input groups11" type="checkbox" name="whatsapp_api_test" id="whatsapp-api-test" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm float-end data-submit">Submit</button>
                </form>
            </div>
        </div>
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
    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/permission-validation.js') }}"></script>



    <script>
        $(document).ready(function(){
            $('#staff_id').on('change',function(){
                var staff_id = $(this).val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url: "{{ url('admin/permission/get') }}",
                    method: 'post',
                    type: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        staff_id: staff_id

                    },
                    success: function(data){
                        if (data.full_access == 1) {
                            $('#full-access').prop('checked',true);
                        }else{
                            $('#full-access').prop('checked',false);
                        }

                        if (data.bookings == 1) {
                            $('#bookings').prop('checked',true).change();
                        } else {
                            $('#bookings').prop('checked',false).change();
                        }

                        if (data.employer == 1) {
                            $('#employer').prop('checked',true).change();
                        } else {
                            $('#employer').prop('checked',false).change();
                        }

                        if (data.employerplus == 1) {
                            $('#employerplus').prop('checked',true).change();
                        } else {
                            $('#employerplus').prop('checked',false).change();
                        }

                        if (data.candidate == 1) {
                            $('#candidate').prop('checked',true).change();
                        } else {
                            $('#candidate').prop('checked',false).change();
                        }

                        if (data.testimonial == 1) {
                            $('#testimonial').prop('checked',true).change();
                        } else {
                            $('#testimonial').prop('checked',false).change();
                        }

                        if (data.google_review == 1) {
                            $('#google_review').prop('checked',true).change();
                        } else {
                            $('#google_review').prop('checked',false).change();
                        }

                        if (data.associate == 1) {
                            $('#associate').prop('checked',true).change();
                        } else {
                            $('#associate').prop('checked',false).change();
                        }

                        if (data.chat == 1) {
                            $('#chat').prop('checked',true);
                        } else {
                            $('#chat').prop('checked',false);
                        }

                        if (data.client == 1) {
                            $('#client').prop('checked',true).change();
                        } else {
                            $('#client').prop('checked',false).change();
                        }

                        if (data.partner == 1) {
                            $('#partner').prop('checked',true).change();
                        } else {
                            $('#partner').prop('checked',false).change();
                        }

                        if (data.dynamic == 1) {
                            $('#dynamic').prop('checked',true).change();
                        } else {
                            $('#dynamic').prop('checked',false).change();
                        }

                        if (data.settings == 1) {
                            $('#settings').prop('checked',true).change();
                        } else {
                            $('#settings').prop('checked',false).change();
                        }

                        if (data.finance == 1) {
                            $('#finance').prop('checked',true).change();
                        } else {
                            $('#finance').prop('checked',false).change();
                        }

                        if (data.contactp == 1) {
                            $('#contactp').prop('checked',true).change();
                        }else{
                            $('#contactp').prop('checked',false).change();
                        }

                        if (data.todo == 1) {
                            $('#todo').prop('checked',true).change();
                        }else{
                            $('#todo').prop('checked',false).change();
                        }

                        if (data.deal_pipeline == 1) {
                            $('#deal-pipeline').prop('checked',true).change();
                        }else{
                            $('#deal-pipeline').prop('checked',false).change();
                        }

                        if (data.leads == 1) {
                            $('#leads').prop('checked',true).change();
                        }else{
                            $('#leads').prop('checked',false).change();
                        }

                        if (data.allcontact == 1) {
                            $('#allcontact').prop('checked',true).change();
                        }else{
                            $('#allcontact').prop('checked',false).change();
                        }

                        if (data.whatsapp_plus == 1) {
                            $('#whatsapp-plus').prop('checked',true).change();
                        }else{
                            $('#whatsapp-plus').prop('checked',false).change();
                        }

                        if (data.meta_whatsapp_template == 1) {
                            $('#meta-whatsapp-template').prop('checked',true);
                        } else {
                            $('#meta-whatsapp-template').prop('checked',false);
                        }

                        if (data.sms_campaign_module == 1) {
                            $('#sms-campaign-module').prop('checked',true).change();
                        }else{
                            $('#sms-campaign-module').prop('checked',false).change();
                        }

                        if (data.sms_api == 1) {
                            $('#sms-api').prop('checked',true);
                        } else {
                            $('#sms-api').prop('checked',false);
                        }

                        if (data.add_sms_api == 1) {
                            $('#add-sms-api').prop('checked',true);
                        } else {
                            $('#add-sms-api').prop('checked',false);
                        }

                        if (data.edit_sms_api == 1) {
                            $('#edit-sms-api').prop('checked',true);
                        } else {
                            $('#edit-sms-api').prop('checked',false);
                        }

                        if (data.delete_sms_api == 1) {
                            $('#delete-sms-api').prop('checked',true);
                        } else {
                            $('#delete-sms-api').prop('checked',false);
                        }

                        if (data.change_status_sms_api == 1) {
                            $('#change-status-sms-api').prop('checked',true);
                        } else {
                            $('#change-status-sms-api').prop('checked',false);
                        }

                        if (data.assign_sms_api == 1) {
                            $('#assign-sms-api').prop('checked',true);
                        } else {
                            $('#assign-sms-api').prop('checked',false);
                        }

                        if (data.sms_template == 1) {
                            $('#sms-template').prop('checked',true);
                        } else {
                            $('#sms-template').prop('checked',false);
                        }

                        if (data.add_sms_template == 1) {
                            $('#add-sms-template').prop('checked',true);
                        } else {
                            $('#add-sms-template').prop('checked',false);
                        }

                        if (data.edit_sms_template == 1) {
                            $('#edit-sms-template').prop('checked',true);
                        } else {
                            $('#edit-sms-template').prop('checked',false);
                        }

                        if (data.delete_sms_template == 1) {
                            $('#delete-sms-template').prop('checked',true);
                        } else {
                            $('#delete-sms-template').prop('checked',false);
                        }

                        if (data.change_status_sms_template == 1) {
                            $('#change-status-sms-template').prop('checked',true);
                        } else {
                            $('#change-status-sms-template').prop('checked',false);
                        }

                        if (data.sms_campaign == 1) {
                            $('#sms-campaign').prop('checked',true);
                        } else {
                            $('#sms-campaign').prop('checked',false);
                        }

                        if (data.add_sms_campaign == 1) {
                            $('#add-sms-campaign').prop('checked',true);
                        } else {
                            $('#add-sms-campaign').prop('checked',false);
                        }

                        if (data.view_sms_campaign == 1) {
                            $('#view-sms-campaign').prop('checked',true);
                        } else {
                            $('#view-sms-campaign').prop('checked',false);
                        }

                        if (data.delete_sms_campaign == 1) {
                            $('#delete-sms-campaign').prop('checked',true);
                        } else {
                            $('#delete-sms-campaign').prop('checked',false);
                        }


                        if (data.todo_add == 1) {
                            $('#todo-add').prop('checked',true);
                        } else {
                            $('#todo-add').prop('checked',false);
                        }

                        if (data.todo_view == 1) {
                            $('#todo-view').prop('checked',true);
                        } else {
                            $('#todo-view').prop('checked',false);
                        }

                        if (data.todo_edit == 1) {
                            $('#todo-edit').prop('checked',true);
                        } else {
                            $('#todo-edit').prop('checked',false);
                        }

                        if (data.todo_delete == 1) {
                            $('#todo-delete').prop('checked',true);
                        } else {
                            $('#todo-delete').prop('checked',false);
                        }

                        if (data.todo_achieved == 1) {
                            $('#todo-achieved').prop('checked',true);
                        } else {
                            $('#todo-achieved').prop('checked',false);
                        }

                        if (data.bulk_todo_update_status == 1) {
                            $('#bulk-todo-status-update').prop('checked',true);
                        } else {
                            $('#bulk-todo-status-update').prop('checked',false);
                        }


                        if (data.deal_add == 1) {
                            $('#deal-add').prop('checked', true);
                        } else {
                            $('#deal-add').prop('checked', false);
                        }

                        if (data.deal_view == 1) {
                            $('#deal-view').prop('checked', true);
                        } else {
                            $('#deal-view').prop('checked', false);
                        }

                        if (data.deal_edit == 1) {
                            $('#deal-edit').prop('checked', true);
                        } else {
                            $('#deal-edit').prop('checked', false);
                        }

                        if (data.deal_delete == 1) {
                            $('#deal-delete').prop('checked', true);
                        } else {
                            $('#deal-delete').prop('checked', false);
                        }

                        if (data.deal_update_stage == 1) {
                            $('#deal-stage-update').prop('checked', true);
                        } else {
                            $('#deal-stage-update').prop('checked', false);
                        }

                        if (data.deal_update_recruit_status == 1) {
                            $('#deal-recruit-status-update').prop('checked', true);
                        } else {
                            $('#deal-recruit-status-update').prop('checked', false);
                        }

                         if (data.deal_delete_files == 1) {
                            $('#deal-delete-files').prop('checked', true);
                        } else {
                            $('#deal-delete-files').prop('checked', false);
                        }

                        if (data.expense == 1) {
                            $('#expense').prop('checked',true);
                        } else {
                            $('#expense').prop('checked',false);
                        }

                        if (data.add_expense == 1) {
                            $('#add-expense').prop('checked',true);
                        } else {
                            $('#add-expense').prop('checked',false);
                        }

                        if (data.edit_expense == 1) {
                            $('#edit-expense').prop('checked',true);
                        } else {
                            $('#edit-expense').prop('checked',false);
                        }

                        if (data.view_expense == 1) {
                            $('#view-expense').prop('checked',true);
                        } else {
                            $('#view-expense').prop('checked',false);
                        }

                        if (data.delete_expense == 1) {
                            $('#delete-expense').prop('checked',true);
                        } else {
                            $('#delete-expense').prop('checked',false);
                        }

                        if (data.file_manager == 1) {
                            $('#file-manager').prop('checked',true).change();
                        } else {
                            $('#file-manager').prop('checked',false).change();
                        }

                        if (data.add_file_manager == 1) {
                            $('#add-file-manager').prop('checked',true);
                        } else {
                            $('#add-file-manager').prop('checked',false);
                        }

                        if (data.upload_file_manager == 1) {
                            $('#upload-file-manager').prop('checked',true);
                        } else {
                            $('#upload-file-manager').prop('checked',false);
                        }

                        if (data.download_file_manager == 1) {
                            $('#download-file-manager').prop('checked',true);
                        } else {
                            $('#download-file-manager').prop('checked',false);
                        }

                        if (data.preview_file_manager == 1) {
                            $('#preview-file-manager').prop('checked',true);
                        } else {
                            $('#preview-file-manager').prop('checked',false);
                        }

                        if (data.rename_file_manager == 1) {
                            $('#rename-file-manager').prop('checked',true);
                        } else {
                            $('#rename-file-manager').prop('checked',false);
                        }

                        if (data.move_file_manager == 1) {
                            $('#move-file-manager').prop('checked',true);
                        } else {
                            $('#move-file-manager').prop('checked',false);
                        }

                        if (data.delete_file_manager == 1) {
                            $('#delete-file-manager').prop('checked',true);
                        } else {
                            $('#delete-file-manager').prop('checked',false);
                        }

                        if (data.manage_all_file_manager == 1) {
                            $('#manage-all-file-manager').prop('checked',true);
                        } else {
                            $('#manage-all-file-manager').prop('checked',false);
                        }



                        if (data.bulk_todo_update_priority == 1) {
                            $('#bulk-todo-priority-update').prop('checked',true);
                        } else {
                            $('#bulk-todo-priority-update').prop('checked',false);
                        }

                        if (data.bulk_todo_update_assignto == 1) {
                            $('#bulk-todo-assignto-update').prop('checked',true);
                        } else {
                            $('#bulk-todo-assignto-update').prop('checked',false);
                        }

                        if (data.bulk_todo_delete == 1) {
                            $('#bulk-todo-delete').prop('checked',true);
                        } else {
                            $('#bulk-todo-delete').prop('checked',false);
                        }

                        if (data.update_status_achieved == 1) {
                            $('#update-status-for-achieved').prop('checked',true);
                        } else {
                            $('#update-status-for-achieved').prop('checked',false);
                        }

                        if (data.leads_view == 1) {
                            $('#leads-view').prop('checked',true);
                        } else {
                            $('#leads-view').prop('checked',false);
                        }

                        if (data.leads_delete == 1) {
                            $('#leads-delete').prop('checked',true);
                        } else {
                            $('#leads-delete').prop('checked',false);
                        }

                        if (data.leads_assignto == 1) {
                            $('#leads-assignto').prop('checked',true);
                        } else {
                            $('#leads-assignto').prop('checked',false);
                        }

                        if (data.leads_bulk_delete == 1) {
                            $('#leads-bulk-delete').prop('checked',true);
                        } else {
                            $('#leads-bulk-delete').prop('checked',false);
                        }

                        if (data.leads_bulk_assignto == 1) {
                            $('#leads-bulk-assignto').prop('checked',true);
                        } else {
                            $('#leads-bulk-assignto').prop('checked',false);
                        }


                        if (data.leads_candidate_view == 1) {
                            $('#leads-candidate-view').prop('checked',true);
                        } else {
                            $('#leads-candidate-view').prop('checked',false);
                        }

                        if (data.leads_employer_view == 1) {
                            $('#leads-employer-view').prop('checked',true);
                        } else {
                            $('#leads-employer-view').prop('checked',false);
                        }

                        if (data.leads_company_column == 1) {
                            $('#leads-company-column').prop('checked',true);
                        } else {
                            $('#leads-company-column').prop('checked',false);
                        }


                        if (data.candidate_finance == 1) {
                            $('#candidate-finance').prop('checked',true);
                        } else {
                            $('#candidate-finance').prop('checked',false);
                        }

                        if (data.transaction_finance == 1) {
                            $('#transaction-finance').prop('checked',true);
                        } else {
                            $('#transaction-finance').prop('checked',false);
                        }

                        if (data.transaction_client == 1) {
                            $('#transaction-client').prop('checked',true);
                        } else {
                            $('#transaction-client').prop('checked',false);
                        }

                        if (data.setup == 1) {
                            $('#setup').prop('checked',true);
                        } else {
                            $('#setup').prop('checked',false);
                        }

                        if (data.whatsapp_setup == 1) {
                            $('#whatsapp-setup').prop('checked',true);
                        } else {
                            $('#whatsapp-setup').prop('checked',false);
                        }

                        if (data.whatsapp_api == 1) {
                            $('#whatsapp-api').prop('checked',true);
                        } else {
                            $('#whatsapp-api').prop('checked',false);
                        }

                        if (data.add_whatsapp_api == 1) {
                            $('#add-whatsapp-api').prop('checked',true);
                        } else {
                            $('#add-whatsapp-api').prop('checked',false);
                        }

                        if (data.add_whatsapp_api_for_user == 1) {
                            $('#add-whatsapp-api-for-user').prop('checked',true);
                        } else {
                            $('#add-whatsapp-api-for-user').prop('checked',false);
                        }

                        if (data.edit_whatsapp_api == 1) {
                            $('#edit-whatsapp-api').prop('checked',true);
                        } else {
                            $('#edit-whatsapp-api').prop('checked',false);
                        }

                        if (data.view_whatsapp_api == 1) {
                            $('#view-whatsapp-api').prop('checked',true);
                        } else {
                            $('#view-whatsapp-api').prop('checked',false);
                        }

                        if (data.delete_whatsapp_api == 1) {
                            $('#delete-whatsapp-api').prop('checked',true);
                        } else {
                            $('#delete-whatsapp-api').prop('checked',false);
                        }

                        if (data.whatsapp_api_test == 1) {
                            $('#whatsapp-api-test').prop('checked',true);
                        } else {
                            $('#whatsapp-api-test').prop('checked',false);
                        }

                        if (data.whatsapp_meta_api == 1) {
                            $('#whatsapp-meta-api').prop('checked',true);
                        } else {
                            $('#whatsapp-meta-api').prop('checked',false);
                        }

                        if (data.add_whatsapp_meta_api == 1) {
                            $('#add-whatsapp-meta-api').prop('checked',true);
                        } else {
                            $('#add-whatsapp-meta-api').prop('checked',false);
                        }

                        if (data.edit_whatsapp_meta_api == 1) {
                            $('#edit-whatsapp-meta-api').prop('checked',true);
                        } else {
                            $('#edit-whatsapp-meta-api').prop('checked',false);
                        }

                        if (data.view_whatsapp_meta_api == 1) {
                            $('#view-whatsapp-meta-api').prop('checked',true);
                        } else {
                            $('#view-whatsapp-meta-api').prop('checked',false);
                        }

                        if (data.delete_whatsapp_meta_api == 1) {
                            $('#delete-whatsapp-meta-api').prop('checked',true);
                        } else {
                            $('#delete-whatsapp-meta-api').prop('checked',false);
                        }

                        // Whatsapp URL
                        if (data.whatsapp_url == 1) {
                            $('#whatsapp-url').prop('checked', true);
                        } else {
                            $('#whatsapp-url').prop('checked', false);
                        }

                        // Add Whatsapp Meta API
                        if (data.add_whatsapp_url == 1) {
                            $('#add-whatsapp-url').prop('checked', true);
                        } else {
                            $('#add-whatsapp-url').prop('checked', false);
                        }

                        // Edit Whatsapp Meta API
                        if (data.edit_whatsapp_url == 1) {
                            $('#edit-whatsapp-url').prop('checked', true);
                        } else {
                            $('#edit-whatsapp-url').prop('checked', false);
                        }

                        // View Whatsapp Meta API
                        if (data.view_whatsapp_url == 1) {
                            $('#view-whatsapp-url').prop('checked', true);
                        } else {
                            $('#view-whatsapp-url').prop('checked', false);
                        }

                        // Delete Whatsapp Meta API
                        if (data.delete_whatsapp_url == 1) {
                            $('#delete-whatsapp-url').prop('checked', true);
                        } else {
                            $('#delete-whatsapp-url').prop('checked', false);
                        }


                        if (data.finance_sale_invoices == 1) {
                            $('#finance-sale-invoices').prop('checked',true);
                        } else {
                            $('#finance-sale-invoices').prop('checked',false);
                        }

                        if (data.finance_sale_invoices_create == 1) {
                            $('#finance-sale-invoices-create').prop('checked',true);
                        } else {
                            $('#finance-sale-invoices-create').prop('checked',false);
                        }

                        if (data.finance_sale_invoices_edit == 1) {
                            $('#finance-sale-invoices-edit').prop('checked',true);
                        } else {
                            $('#finance-sale-invoices-edit').prop('checked',false);
                        }

                        if (data.finance_sale_invoices_view == 1) {
                            $('#finance-sale-invoices-view').prop('checked',true);
                        } else {
                            $('#finance-sale-invoices-view').prop('checked',false);
                        }

                        if (data.finance_sale_invoices_delete == 1) {
                            $('#finance-sale-invoices-delete').prop('checked',true);
                        } else {
                            $('#finance-sale-invoices-delete').prop('checked',false);
                        }

                        if (data.finance_payment == 1) {
                            $('#finance-payment').prop('checked',true);
                        } else {
                            $('#finance-payment').prop('checked',false);
                        }

                        if (data.finance_payment_add == 1) {
                            $('#finance-payment-add').prop('checked',true);
                        } else {
                            $('#finance-payment-add').prop('checked',false);
                        }

                        if (data.finance_payment_edit == 1) {
                            $('#finance-payment-edit').prop('checked',true);
                        } else {
                            $('#finance-payment-edit').prop('checked',false);
                        }

                        if (data.finance_payment_delete == 1) {
                            $('#finance-payment-delete').prop('checked',true);
                        } else {
                            $('#finance-payment-delete').prop('checked',false);
                        }

                        if (data.hr_management == 1) {
                            $('#hr-management').prop('checked',true);
                        } else {
                            $('#hr-management').prop('checked',false);
                        }

                        if (data.hr_dashboard == 1) {
                            $('#hr-dashboard').prop('checked',true);
                        } else {
                            $('#hr-dashboard').prop('checked',false);
                        }

                        if (data.hr_attendance == 1) {
                            $('#hr-attendance').prop('checked',true);
                        } else {
                            $('#hr-attendance').prop('checked',false);
                        }

                        if (data.hr_payroll == 1) {
                            $('#hr-payroll').prop('checked',true);
                        } else {
                            $('#hr-payroll').prop('checked',false);
                        }

                        if (data.hr_settings == 1) {
                            $('#hr-settings').prop('checked',true);
                        } else {
                            $('#hr-settings').prop('checked',false);
                        }

                        if (data.hr_attendance_view_all == 1) {
                            $('#hr-attendance-view-all').prop('checked',true);
                        } else {
                            $('#hr-attendance-view-all').prop('checked',false);
                        }

                        if (data.fund_advance == 1) {
                            $('#fund-advance').prop('checked',true);
                        } else {
                            $('#fund-advance').prop('checked',false);
                        }

                        if (data.fund_advance_create == 1) {
                            $('#fund-advance-create').prop('checked',true);
                        } else {
                            $('#fund-advance-create').prop('checked',false);
                        }

                        if (data.fund_advance_edit == 1) {
                            $('#fund-advance-edit').prop('checked',true);
                        } else {
                            $('#fund-advance-edit').prop('checked',false);
                        }

                        if (data.fund_advance_delete == 1) {
                            $('#fund-advance-delete').prop('checked',true);
                        } else {
                            $('#fund-advance-delete').prop('checked',false);
                        }

                        if (data.fund_advance_settlement_create == 1) {
                            $('#fund-advance-settlement-create').prop('checked',true);
                        } else {
                            $('#fund-advance-settlement-create').prop('checked',false);
                        }

                        if (data.fund_advance_settlement_view == 1) {
                            $('#fund-advance-settlement-view').prop('checked',true);
                        } else {
                            $('#fund-advance-settlement-view').prop('checked',false);
                        }

                        if (data.fund_advance_adjustment_create == 1) {
                            $('#fund-advance-adjustment-create').prop('checked',true);
                        } else {
                            $('#fund-advance-adjustment-create').prop('checked',false);
                        }

                        if (data.fund_advance_ledger_view == 1) {
                            $('#fund-advance-ledger-view').prop('checked',true);
                        } else {
                            $('#fund-advance-ledger-view').prop('checked',false);
                        }

                        if (data.fund_advance_reports_view == 1) {
                            $('#fund-advance-reports-view').prop('checked',true);
                        } else {
                            $('#fund-advance-reports-view').prop('checked',false);
                        }

                        if (data.fund_advance_export == 1) {
                            $('#fund-advance-export').prop('checked',true);
                        } else {
                            $('#fund-advance-export').prop('checked',false);
                        }

                        if (data.allcontact_add == 1) {
                            $('#allcontact-add').prop('checked',true);
                        } else {
                            $('#allcontact-add').prop('checked',false);
                        }

                        if (data.allcontact_edit == 1) {
                            $('#allcontact-edit').prop('checked',true);
                        } else {
                            $('#allcontact-edit').prop('checked',false);
                        }

                        if (data.allcontact_view == 1) {
                            $('#allcontact-view').prop('checked',true);
                        } else {
                            $('#allcontact-view').prop('checked',false);
                        }

                        if (data.allcontact_delete == 1) {
                            $('#allcontact-delete').prop('checked',true);
                        } else {
                            $('#allcontact-delete').prop('checked',false);
                        }

                        if (data.allcontact_bulk_transfer_group == 1) {
                            $('#allcontact-bulk-transfer-group').prop('checked',true);
                        } else {
                            $('#allcontact-bulk-transfer-group').prop('checked',false);
                        }

                        if (data.allcontact_bulk_transfer_careoff == 1) {
                            $('#allcontact-bulk-transfer-careoff').prop('checked',true);
                        } else {
                            $('#allcontact-bulk-transfer-careoff').prop('checked',false);
                        }

                        // if (data.allcontact_bulk_transfer_leadowner == 1) {
                        //     $('#allcontact-bulk-transfer-lead-owner').prop('checked',true);
                        // } else {
                        //     $('#allcontact-bulk-transfer-lead-owner').prop('checked',false);
                        // }

                        if (data.allcontact_bulk_send_whatsapp == 1) {
                            $('#allcontact-bulk-send-whatsapp').prop('checked',true);
                        } else {
                            $('#allcontact-bulk-send-whatsapp').prop('checked',false);
                        }

                        if (data.allcontact_bulk_delete == 1) {
                            $('#allcontact-bulk-delete').prop('checked',true);
                        } else {
                            $('#allcontact-bulk-delete').prop('checked',false);
                        }

                        if (data.allcontact_bulk_country_update == 1) {
                            $('#allcontact-bulk-country-update').prop('checked',true);
                        } else {
                            $('#allcontact-bulk-country-update').prop('checked',false);
                        }

                        if (data.allcontact_bulk_country_code_update == 1) {
                            $('#allcontact-bulk-country-code-update').prop('checked',true);
                        } else {
                            $('#allcontact-bulk-country-code-update').prop('checked',false);
                        }

                        if (data.allcontact_company_column == 1) {
                            $('#allcontact-company-column').prop('checked', true);
                        } else {
                            $('#allcontact-company-column').prop('checked', false);
                        }




                        if (data.add_meta_whatsapp_template == 1) {
                            $('#add-meta-whatsapp-template').prop('checked',true);
                        } else {
                            $('#add-meta-whatsapp-template').prop('checked',false);
                        }

                        if (data.view_meta_whatsapp_template == 1) {
                            $('#view-meta-whatsapp-template').prop('checked',true);
                        } else {
                            $('#view-meta-whatsapp-template').prop('checked',false);
                        }

                        if (data.edit_meta_whatsapp_template == 1) {
                            $('#edit-meta-whatsapp-template').prop('checked',true);
                        } else {
                            $('#edit-meta-whatsapp-template').prop('checked',false);
                        }

                        if (data.delete_meta_whatsapp_template == 1) {
                            $('#delete-meta-whatsapp-template').prop('checked',true);
                        } else {
                            $('#delete-meta-whatsapp-template').prop('checked',false);
                        }

                        if (data.status_meta_whatsapp_template == 1) {
                            $('#status-meta-whatsapp-template').prop('checked',true);
                        } else {
                            $('#status-meta-whatsapp-template').prop('checked',false);
                        }

                        if (data.public_meta_whatsapp_template == 1) {
                            $('#public-meta-whatsapp-template').prop('checked',true);
                        } else {
                            $('#public-meta-whatsapp-template').prop('checked',false);
                        }

                        if (data.meta_whatsapp_campaign == 1) {
                            $('#meta-whatsapp-campaign').prop('checked',true);
                        } else {
                            $('#meta-whatsapp-campaign').prop('checked',false);
                        }


                        if (data.meta_whatsapp_campaign_view == 1) {
                            $('#meta-whatsapp-campaign-view').prop('checked',true);
                        } else {
                            $('#meta-whatsapp-campaign-view').prop('checked',false);
                        }

                        if (data.meta_whatsapp_campaign_create_user == 1) {
                            $('#meta-whatsapp-campaign-create-user').prop('checked',true);
                        } else {
                            $('#meta-whatsapp-campaign-create-user').prop('checked',false);
                        }

                        if (data.meta_automation == 1) {
                            $('#meta-automation').prop('checked',true);
                        } else {
                            $('#meta-automation').prop('checked',false);
                        }

                        if (data.add_meta_automation == 1) {
                            $('#add-meta-automation').prop('checked',true);
                        } else {
                            $('#add-meta-automation').prop('checked',false);
                        }


                        if (data.view_meta_automation == 1) {
                            $('#view-meta-automation').prop('checked',true);
                        } else {
                            $('#view-meta-automation').prop('checked',false);
                        }


                        if (data.edit_meta_automation == 1) {
                            $('#edit-meta-automation').prop('checked',true);
                        } else {
                            $('#edit-meta-automation').prop('checked',false);
                        }


                        if (data.delete_meta_automation == 1) {
                            $('#delete-meta-automation').prop('checked',true);
                        } else {
                            $('#delete-meta-automation').prop('checked',false);
                        }


                        if (data.meta_whatsapp_campaign_add == 1) {
                            $('#meta-whatsapp-campaign-add').prop('checked',true);
                        } else {
                            $('#meta-whatsapp-campaign-add').prop('checked',false);
                        }


                        if (data.whatsapp_nromal == 1) {
                            $('#whatsapp-normal').prop('checked',true).change();
                        } else {
                            $('#whatsapp-normal').prop('checked',false).change();
                        }

                        if (data.whatsapp_template == 1) {
                            $('#whatsapp-template').prop('checked',true);
                        } else {
                            $('#whatsapp-template').prop('checked',false);
                        }

                        if (data.add_whatsapp_template == 1) {
                            $('#add-whatsapp-template').prop('checked',true);
                        } else {
                            $('#add-whatsapp-template').prop('checked',false);
                        }

                        if (data.edit_whatsapp_template == 1) {
                            $('#edit-whatsapp-template').prop('checked',true);
                        } else {
                            $('#edit-whatsapp-template').prop('checked',false);
                        }

                        if (data.view_whatsapp_template == 1) {
                            $('#view-whatsapp-template').prop('checked',true);
                        } else {
                            $('#view-whatsapp-template').prop('checked',false);
                        }

                        if (data.delete_whatsapp_template == 1) {
                            $('#delete-whatsapp-template').prop('checked',true);
                        } else {
                            $('#delete-whatsapp-template').prop('checked',false);
                        }

                        if (data.status_whatsapp_template == 1) {
                            $('#status-whatsapp-template').prop('checked',true);
                        } else {
                            $('#status-whatsapp-template').prop('checked',false);
                        }

                        if (data.public_whatsapp_template == 1) {
                            $('#public-whatsapp-template').prop('checked',true);
                        } else {
                            $('#public-whatsapp-template').prop('checked',false);
                        }

                        if (data.whatsapp_campaign == 1) {
                            $('#whatsapp-campaign').prop('checked',true);
                        } else {
                            $('#whatsapp-campaign').prop('checked',false);
                        }

                        if (data.whatsapp_campaign_add == 1) {
                            $('#whatsapp-campaign-add').prop('checked',true);
                        } else {
                            $('#whatsapp-campaign-add').prop('checked',false);
                        }

                        if (data.whatsapp_campaign_view == 1) {
                            $('#whatsapp-campaign-view').prop('checked',true);
                        } else {
                            $('#whatsapp-campaign-view').prop('checked',false);
                        }


                        if (data.booking_confirm == 1) {
                            $('#confirm-booking').prop('checked',true);
                        }else{
                            $('#confirm-booking').prop('checked',false);
                        }

                        if (data.add_visa_details == 1) {
                            $('#add-visa-details').prop('checked',true);
                        }else{
                            $('#add-visa-details').prop('checked',false);
                        }

                        if (data.view_booking == 1) {
                            $('#view-booking').prop('checked',true);
                        }else{
                            $('#view-booking').prop('checked',false);
                        }

                        if (data.add_payment == 1) {
                            $('#add-payment').prop('checked',true);
                        }else{
                            $('#add-payment').prop('checked',false);
                        }

                        if (data.cancel_booking == 1) {
                            $('#cancel-booking').prop('checked',true);
                        }else{
                            $('#cancel-booking').prop('checked',false);
                        }

                        if (data.replace_candidate == 1) {
                            $('#replace-booking').prop('checked',true);
                        }else{
                            $('#replace-booking').prop('checked',false);
                        }

                        if (data.view_employer == 1) {
                            $('#view-employer').prop('checked',true);
                        }else{
                            $('#view-employer').prop('checked',false);
                        }

                        if (data.employerplus_view == 1) {
                            $('#view-employerplus').prop('checked',true);
                        }else{
                            $('#view-employerplus').prop('checked',false);
                        }

                        if (data.delete_employer == 1) {
                            $('#delete-employer').prop('checked',true);
                        }else{
                            $('#delete-employer').prop('checked',false);
                        }

                        if (data.employerplus_add == 1) {
                            $('#add-employerplus').prop('checked',true);
                        }else{
                            $('#add-employerplus').prop('checked',false);
                        }

                        if (data.employerplus_edit == 1) {
                            $('#edit-employerplus').prop('checked',true);
                        }else{
                            $('#edit-employerplus').prop('checked',false);
                        }

                        if (data.employerplus_delete == 1) {
                            $('#delete-employerplus').prop('checked',true);
                        }else{
                            $('#delete-employerplus').prop('checked',false);
                        }

                        if (data.add_candidate == 1) {
                            $('#add-candidate').prop('checked',true);
                        }else{
                            $('#add-candidate').prop('checked',false);
                        }

                        if (data.edit_candidate == 1) {
                            $('#edit-candidate').prop('checked',true);
                        }else{
                            $('#edit-candidate').prop('checked',false);
                        }

                        if (data.view_candidate == 1) {
                            $('#view-candidate').prop('checked',true);
                        }else{
                            $('#view-candidate').prop('checked',false);
                        }

                        if (data.delete_candidate == 1) {
                            $('#delete-candidate').prop('checked',true);
                        }else{
                            $('#delete-candidate').prop('checked',false);
                        }

                        if (data.publish_candidate == 1) {
                            $('#publish-candidate').prop('checked',true);
                        }else{
                            $('#publish-candidate').prop('checked',false);
                        }

                        if (data.add_testimonial == 1) {
                            $('#add-testimonial').prop('checked',true);
                        }else{
                            $('#add-testimonial').prop('checked',false);
                        }

                        if (data.edit_testimonial == 1) {
                            $('#edit-testimonial').prop('checked',true);
                        }else{
                            $('#edit-testimonial').prop('checked',false);
                        }

                        if (data.view_testimonial == 1) {
                            $('#view-testimonial').prop('checked',true);
                        }else{
                            $('#view-testimonial').prop('checked',false);
                        }

                        if (data.copy_link_testimonial == 1) {
                            $('#copy-link-testimonial').prop('checked',true);
                        }else{
                            $('#copy-link-testimonial').prop('checked',false);
                        }

                        if (data.delete_testimonial == 1) {
                            $('#delete-testimonial').prop('checked',true);
                        }else{
                            $('#delete-testimonial').prop('checked',false);
                        }

                        if (data.approval_testimonial == 1) {
                            $('#approval-testimonial').prop('checked',true);
                        }else{
                            $('#approval-testimonial').prop('checked',false);
                        }
                        if (data.payment_testimonial == 1) {
                            $('#payment-testimonial').prop('checked',true);
                        }else{
                            $('#payment-testimonial').prop('checked',false);
                        }
                        if (data.video_received_testimonial == 1) {
                            $('#video-received-testimonial').prop('checked',true);
                        }else{
                            $('#video-received-testimonial').prop('checked',false);
                        }
                        if (data.social_media_testimonial == 1) {
                            $('#social-media-testimonial').prop('checked',true);
                        }else{
                            $('#social-media-testimonial').prop('checked',false);
                        }

                        if (data.add_google_review == 1) {
                            $('#add-google_review').prop('checked',true);
                        }else{
                            $('#add-google_review').prop('checked',false);
                        }

                        if (data.edit_google_review == 1) {
                            $('#edit-google_review').prop('checked',true);
                        }else{
                            $('#edit-google_review').prop('checked',false);
                        }

                        if (data.view_google_review == 1) {
                            $('#view-google_review').prop('checked',true);
                        }else{
                            $('#view-google_review').prop('checked',false);
                        }

                        if (data.delete_google_review == 1) {
                            $('#delete-google_review').prop('checked',true);
                        }else{
                            $('#delete-google_review').prop('checked',false);
                        }

                        if (data.approval_google_review == 1) {
                            $('#approval-google_review').prop('checked',true);
                        }else{
                            $('#approval-google_review').prop('checked',false);
                        }

                        if (data.payment_google_review == 1) {
                            $('#payment-google_review').prop('checked',true);
                        }else{
                            $('#payment-google_review').prop('checked',false);
                        }

                        if (data.view_client == 1) {
                            $('#view-client').prop('checked',true);
                        }else{
                            $('#view-client').prop('checked',false);
                        }

                        if (data.delete_client == 1) {
                            $('#delete-client').prop('checked',true);
                        }else{
                            $('#delete-client').prop('checked',false);
                        }

                        if (data.add_partner == 1) {
                            $('#add-partner').prop('checked',true);
                        }else{
                            $('#add-partner').prop('checked',false);
                        }

                        if (data.edit_partner == 1) {
                            $('#edit-partner').prop('checked',true);
                        }else{
                            $('#edit-partner').prop('checked',false);
                        }

                        if (data.view_partner == 1) {
                            $('#view-partner').prop('checked',true);
                        }else{
                            $('#view-partner').prop('checked',false);
                        }

                        if (data.delete_partner == 1) {
                            $('#delete-partner').prop('checked',true);
                        }else{
                            $('#delete-partner').prop('checked',false);
                        }

                        if (data.staff == 1) {
                            $('#staff').prop('checked',true);
                        }else{
                            $('#staff').prop('checked',false);
                        }

                        if (data.add_staff == 1) {
                            $('#add-staff').prop('checked',true);
                        }else{
                            $('#add-staff').prop('checked',false);
                        }

                        if (data.edit_staff == 1) {
                            $('#edit-staff').prop('checked',true);
                        }else{
                            $('#edit-staff').prop('checked',false);
                        }

                        if (data.delete_staff == 1) {
                            $('#delete-staff').prop('checked',true);
                        }else{
                            $('#delete-staff').prop('checked',false);
                        }

                        if (data.view_staff == 1) {
                            $('#view-staff').prop('checked',true);
                        }else{
                            $('#view-staff').prop('checked',false);
                        }

                        if (data.branch == 1) {
                            $('#branch').prop('checked',true);
                        }else{
                            $('#branch').prop('checked',false);
                        }

                        if (data.add_branch == 1) {
                            $('#add-branch').prop('checked',true);
                        }else{
                            $('#add-branch').prop('checked',false);
                        }

                        if (data.edit_branch == 1) {
                            $('#edit-branch').prop('checked',true);
                        }else{
                            $('#edit-branch').prop('checked',false);
                        }

                        if (data.delete_branch == 1) {
                            $('#delete-branch').prop('checked',true);
                        }else{
                            $('#delete-branch').prop('checked',false);
                        }

                        if (data.view_branch == 1) {
                            $('#view-branch').prop('checked',true);
                        }else{
                            $('#view-branch').prop('checked',false);
                        }

                        if (data.profession == 1) {
                            $('#profession').prop('checked',true);
                        }else{
                            $('#profession').prop('checked',false);
                        }

                        if (data.add_profession == 1) {
                            $('#add-profession').prop('checked',true);
                        }else{
                            $('#add-profession').prop('checked',false);
                        }

                        if (data.edit_profession == 1) {
                            $('#edit-profession').prop('checked',true);
                        }else{
                            $('#edit-profession').prop('checked',false);
                        }

                        if (data.delete_profession == 1) {
                            $('#delete-profession').prop('checked',true);
                        }else{
                            $('#delete-profession').prop('checked',false);
                        }

                        if (data.view_profession == 1) {
                            $('#view-profession').prop('checked',true);
                        }else{
                            $('#view-profession').prop('checked',false);
                        }

                        if (data.placeofissue == 1) {
                            $('#placeofissue').prop('checked',true);
                        }else{
                            $('#placeofissue').prop('checked',false);
                        }

                        if (data.add_placeofissue == 1) {
                            $('#add-placeofissue').prop('checked',true);
                        }else{
                            $('#add-placeofissue').prop('checked',false);
                        }

                        if (data.edit_placeofissue == 1) {
                            $('#edit-placeofissue').prop('checked',true);
                        }else{
                            $('#edit-placeofissue').prop('checked',false);
                        }

                        if (data.delete_placeofissue == 1) {
                            $('#delete-placeofissue').prop('checked',true);
                        }else{
                            $('#delete-placeofissue').prop('checked',false);
                        }

                        if (data.view_placeofissue == 1) {
                            $('#view-placeofissue').prop('checked',true);
                        }else{
                            $('#view-placeofissue').prop('checked',false);
                        }

                        if (data.country == 1) {
                            $('#country').prop('checked',true);
                        }else{
                            $('#country').prop('checked',false);
                        }

                        if (data.add_country == 1) {
                            $('#add-country').prop('checked',true);
                        }else{
                            $('#add-country').prop('checked',false);
                        }

                        if (data.edit_country == 1) {
                            $('#edit-country').prop('checked',true);
                        }else{
                            $('#edit-country').prop('checked',false);
                        }

                        if (data.view_country == 1) {
                            $('#view-country').prop('checked',true);
                        }else{
                            $('#view-country').prop('checked',false);
                        }

                        if (data.delete_country == 1) {
                            $('#delete-country').prop('checked',true);
                        }else{
                            $('#delete-country').prop('checked',false);
                        }

                        if (data.region == 1) {
                            $('#region').prop('checked',true);
                        }else{
                            $('#region').prop('checked',false);
                        }

                        if (data.add_region == 1) {
                            $('#add-region').prop('checked',true);
                        }else{
                            $('#add-region').prop('checked',false);
                        }

                        if (data.edit_region == 1) {
                            $('#edit-region').prop('checked',true);
                        }else{
                            $('#edit-region').prop('checked',false);
                        }

                        if (data.view_region == 1) {
                            $('#view-region').prop('checked',true);
                        }else{
                            $('#view-region').prop('checked',false);
                        }

                        if (data.delete_region == 1) {
                            $('#delete-region').prop('checked',true);
                        }else{
                            $('#delete-region').prop('checked',false);
                        }

                        if (data.city == 1) {
                            $('#city').prop('checked',true);
                        }else{
                            $('#city').prop('checked',false);
                        }

                        if (data.add_city == 1) {
                            $('#add-city').prop('checked',true);
                        }else{
                            $('#add-city').prop('checked',false);
                        }

                        if (data.edit_city == 1) {
                            $('#edit-city').prop('checked',true);
                        }else{
                            $('#edit-city').prop('checked',false);
                        }

                        if (data.view_city == 1) {
                            $('#view-city').prop('checked',true);
                        }else{
                            $('#view-city').prop('checked',false);
                        }

                        if (data.delete_city == 1) {
                            $('#delete-city').prop('checked',true);
                        }else{
                            $('#delete-city').prop('checked',false);
                        }

                        if (data.expworklocation == 1) {
                            $('#expworklocation').prop('checked',true);
                        }else{
                            $('#expworklocation').prop('checked',false);
                        }

                        if (data.add_expworklocation == 1) {
                            $('#add-expworklocation').prop('checked',true);
                        }else{
                            $('#add-expworklocation').prop('checked',false);
                        }

                        if (data.edit_expworklocation == 1) {
                            $('#edit-expworklocation').prop('checked',true);
                        }else{
                            $('#edit-expworklocation').prop('checked',false);
                        }

                        if (data.view_expworklocation == 1) {
                            $('#view-expworklocation').prop('checked',true);
                        }else{
                            $('#view-expworklocation').prop('checked',false);
                        }

                        if (data.delete_expworklocation == 1) {
                            $('#delete-expworklocation').prop('checked',true);
                        }else{
                            $('#delete-expworklocation').prop('checked',false);
                        }


                        if (data.businesstype == 1) {
                            $('#businesstype').prop('checked',true);
                        }else{
                            $('#businesstype').prop('checked',false);
                        }

                        if (data.add_businesstype == 1) {
                            $('#add-businesstype').prop('checked',true);
                        }else{
                            $('#add-businesstype').prop('checked',false);
                        }

                        if (data.edit_businesstype == 1) {
                            $('#edit-businesstype').prop('checked',true);
                        }else{
                            $('#edit-businesstype').prop('checked',false);
                        }

                        if (data.view_businesstype == 1) {
                            $('#view-businesstype').prop('checked',true);
                        }else{
                            $('#view-businesstype').prop('checked',false);
                        }

                        if (data.delete_businesstype == 1) {
                            $('#delete-businesstype').prop('checked',true);
                        }else{
                            $('#delete-businesstype').prop('checked',false);
                        }

                        if (data.industries == 1) {
                            $('#industries').prop('checked',true);
                        }else{
                            $('#industries').prop('checked',false);
                        }

                        if (data.add_industries == 1) {
                            $('#add-industries').prop('checked',true);
                        }else{
                            $('#add-industries').prop('checked',false);
                        }

                        if (data.edit_industries == 1) {
                            $('#edit-industries').prop('checked',true);
                        }else{
                            $('#edit-industries').prop('checked',false);
                        }

                        if (data.view_industries == 1) {
                            $('#view-industries').prop('checked',true);
                        }else{
                            $('#view-industries').prop('checked',false);
                        }

                        if (data.delete_industries == 1) {
                            $('#delete-industries').prop('checked',true);
                        }else{
                            $('#delete-industries').prop('checked',false);
                        }



                        if (data.groupcp == 1) {
                            $('#groupcp').prop('checked',true);
                        }else{
                            $('#groupcp').prop('checked',false);
                        }

                        if (data.add_groupcp == 1) {
                            $('#add-groupcp').prop('checked',true);
                        }else{
                            $('#add-groupcp').prop('checked',false);
                        }

                        if (data.edit_groupcp == 1) {
                            $('#edit-groupcp').prop('checked',true);
                        }else{
                            $('#edit-groupcp').prop('checked',false);
                        }

                        if (data.view_groupcp == 1) {
                            $('#view-groupcp').prop('checked',true);
                        }else{
                            $('#view-groupcp').prop('checked',false);
                        }

                        if (data.delete_groupcp == 1) {
                            $('#delete-groupcp').prop('checked',true);
                        }else{
                            $('#delete-groupcp').prop('checked',false);
                        }

                        if (data.websiteconfig == 1) {
                            $('#websiteconfig').prop('checked',true);
                        }else{
                            $('#websiteconfig').prop('checked',false);
                        }

                        if (data.access_setting == 1) {
                            $('#access_setting').prop('checked', true);
                        } else {
                            $('#access_setting').prop('checked', false);
                        }

                        if (data.access_allowed_ip == 1) {
                            $('#access_allowed_ip').prop('checked', true);
                        } else {
                            $('#access_allowed_ip').prop('checked', false);
                        }

                        if (data.access_allowed_ip_revoke == 1) {
                            $('#access_allowed_ip_revoke').prop('checked', true);
                        } else {
                            $('#access_allowed_ip_revoke').prop('checked', false);
                        }

                        if (data.access_allowed_ip_approved_by == 1) {
                            $('#access_allowed_ip_approved_by').prop('checked', true);
                        } else {
                            $('#access_allowed_ip_approved_by').prop('checked', false);
                        }

                        if (data.access_allowed_ip_delete == 1) {
                            $('#access_allowed_ip_delete').prop('checked', true);
                        } else {
                            $('#access_allowed_ip_delete').prop('checked', false);
                        }


                        if (data.todo_setting == 1) {
                            $('#todo-setting').prop('checked',true);
                        }else{
                            $('#todo-setting').prop('checked',false);
                        }

                        if (data.contact_plus_setting == 1) {
                            $('#contact-plus-setting').prop('checked',true);
                        } else {
                            $('#contact-plus-setting').prop('checked',false);
                        }

                        if (data.todo_label == 1) {
                            $('#todo-label').prop('checked',true);
                        }else{
                            $('#todo-label').prop('checked',false);
                        }

                        if (data.todo_label_add == 1) {
                            $('#todo-label-add').prop('checked',true);
                        }else{
                            $('#todo-label-add').prop('checked',false);
                        }

                        if (data.todo_label_view == 1) {
                            $('#todo-label-view').prop('checked',true);
                        }else{
                            $('#todo-label-view').prop('checked',false);
                        }

                        if (data.todo_label_edit == 1) {
                            $('#todo-label-edit').prop('checked',true);
                        }else{
                            $('#todo-label-edit').prop('checked',false);
                        }

                        if (data.todo_label_delete == 1) {
                            $('#todo-label-delete').prop('checked',true);
                        }else{
                            $('#todo-label-delete').prop('checked',false);
                        }

                        if (data.department == 1) {
                            $('#department').prop('checked',true);
                        }else{
                            $('#department').prop('checked',false);
                        }

                        if (data.department_add == 1) {
                            $('#department-add').prop('checked',true);
                        }else{
                            $('#department-add').prop('checked',false);
                        }

                        if (data.department_view == 1) {
                            $('#department-view').prop('checked',true);
                        }else{
                            $('#department-view').prop('checked',false);
                        }

                        if (data.department_edit == 1) {
                            $('#department-edit').prop('checked',true);
                        }else{
                            $('#department-edit').prop('checked',false);
                        }

                        if (data.department_delete == 1) {
                            $('#department-delete').prop('checked',true);
                        }else{
                            $('#department-delete').prop('checked',false);
                        }



                        if (data.mailsetup == 1) {
                            $('#mailsetup').prop('checked',true);
                        }else{
                            $('#mailsetup').prop('checked',false);
                        }

                        if (data.add_mailsetup == 1) {
                            $('#add-mailsetup').prop('checked',true);
                        }else{
                            $('#add-mailsetup').prop('checked',false);
                        }

                        if (data.edit_mailsetup == 1) {
                            $('#edit-mailsetup').prop('checked',true);
                        }else{
                            $('#edit-mailsetup').prop('checked',false);
                        }

                        if (data.view_mailsetup == 1) {
                            $('#view-mailsetup').prop('checked',true);
                        }else{
                            $('#view-mailsetup').prop('checked',false);
                        }

                        if (data.delete_mailsetup == 1) {
                            $('#delete-mailsetup').prop('checked',true);
                        }else{
                            $('#delete-mailsetup').prop('checked',false);
                        }
                        if (data.carknown == 1) {
                            $('#carknown').prop('checked',true);
                        }else{
                            $('#carknown').prop('checked',false);
                        }
                        if (data.add_car_known == 1) {
                            $('#add-carknown').prop('checked',true);
                        }else{
                            $('#add-carknown').prop('checked',false);
                        }
                        if (data.edit_car_known == 1) {
                            $('#edit-carknown').prop('checked',true);
                        }else{
                            $('#edit-carknown').prop('checked',false);
                        }
                        if (data.view_car_known == 1) {
                            $('#view-carknown').prop('checked',true);
                        }else{
                            $('#view-carknown').prop('checked',false);
                        }
                        if (data.delete_car_known == 1) {
                            $('#delete-carknown').prop('checked',true);
                        }else{
                            $('#delete-carknown').prop('checked',false);
                        }
                        if (data.personalise_class == 1) {
                            $('#personalise-class').prop('checked',true);
                        }else{
                            $('#personalise-class').prop('checked',false);
                        }
                        if (data.add_personalise_class == 1) {
                            $('#add-personalise-class').prop('checked',true);
                        }else{
                            $('#add-personalise-class').prop('checked',false);
                        }
                        if (data.edit_personalise_class == 1) {
                            $('#edit-personalise-class').prop('checked',true);
                        }else{
                            $('#edit-personalise-class').prop('checked',false);
                        }
                        if (data.view_personalise_class == 1) {
                            $('#view-personalise-class').prop('checked',true);
                        }else{
                            $('#view-personalise-class').prop('checked',false);
                        }
                        if (data.delete_personalise_class == 1) {
                            $('#delete-personalise-class').prop('checked',true);
                        }else{
                            $('#delete-personalise-class').prop('checked',false);
                        }
                        if (data.storage_usage_setting == 1) {
                            $('#storage-usage-setting').prop('checked',true);
                        }else{
                            $('#storage-usage-setting').prop('checked',false);
                        }
                        if (data.db_backup_setting == 1) {
                            $('#db-backup-setting').prop('checked',true);
                        }else{
                            $('#db-backup-setting').prop('checked',false);
                        }
                        if (data.template == 1) {
                            $('#template').prop('checked',true);
                        }else{
                            $('#template').prop('checked',false);
                        }
                        if (data.add_template == 1) {
                            $('#add-template').prop('checked',true);
                        }else{
                            $('#add-template').prop('checked',false);
                        }
                        if (data.edit_template == 1) {
                            $('#edit-template').prop('checked',true);
                        }else{
                            $('#edit-template').prop('checked',false);
                        }
                        if (data.view_template == 1) {
                            $('#view-template').prop('checked',true);
                        }else{
                            $('#view-template').prop('checked',false);
                        }
                        if (data.delete_template == 1) {
                            $('#delete-template').prop('checked',true);
                        }else{
                            $('#delete-template').prop('checked',false);
                        }

                        if (data.permission_setting == 1) {
                            $('#permission-setting').prop('checked',true);
                        }else{
                            $('#permission-setting').prop('checked',false);
                        }


                        if (data.add_associate == 1) {
                            $('#add-associate').prop('checked',true);
                        }else{
                            $('#add-associate').prop('checked',false);
                        }

                        if (data.edit_associate == 1) {
                            $('#edit-associate').prop('checked',true);
                        }else{
                            $('#edit-associate').prop('checked',false);
                        }

                        if (data.view_associate == 1) {
                            $('#view-associate').prop('checked',true);
                        }else{
                            $('#view-associate').prop('checked',false);
                        }

                        if (data.delete_associate == 1) {
                            $('#delete-associate').prop('checked',true);
                        }else{
                            $('#delete-associate').prop('checked',false);
                        }

                        if (data.publish_associate == 1) {
                            $('#publish-associate').prop('checked',true);
                        }else{
                            $('#publish-associate').prop('checked',false);
                        }

                        if (data.activedeactive_associate == 1) {
                            $('#active-deactive-associate').prop('checked',true);
                        }else{
                            $('#active-deactive-associate').prop('checked',false);
                        }


                        if (data.verified_notverified_associate == 1) {
                            $('#verified-not-verified-associate').prop('checked',true);
                        }else{
                            $('#verified-not-verified-associate').prop('checked',false);
                        }

                        if (data.primary_no_val_associate == 1) {
                            $('#primary-no-val-associate').prop('checked',true);
                        }else{
                            $('#primary-no-val-associate').prop('checked',false);
                        }

                        if (data.secondary_no_val_associate == 1) {
                            $('#secondary-no-val-associate').prop('checked',true);
                        }else{
                            $('#secondary-no-val-associate').prop('checked',false);
                        }

                        if (data.email_associate == 1) {
                            $('#email-associate').prop('checked',true);
                        }else{
                            $('#email-associate').prop('checked',false);
                        }

                        if (data.upload_docs == 1) {
                            $('#upload-docs').prop('checked',true);
                        }else{
                            $('#upload-docs').prop('checked',false);
                        }

                        if (data.cv_b2b == 1) {
                            $('#cv-b2c').prop('checked',true);
                        }else{
                            $('#cv-b2c').prop('checked',false);
                        }

                        if (data.cv_b2c == 1) {
                            $('#cv-b2b').prop('checked',true);
                        }else{
                            $('#cv-b2b').prop('checked',false);
                        }

                        if (data.add_service_charge == 1) {
                            $('#add-service-charge').prop('checked',true);
                        }else{
                            $('#add-service-charge').prop('checked',false);
                        }

                        if (data.edit_service_charge == 1) {
                            $('#edit-service-charge').prop('checked',true);
                        }else{
                            $('#edit-service-charge').prop('checked',false);
                        }

                        if (data.delete_service_charge == 1) {
                            $('#delete-service-charge').prop('checked',true);
                        }else{
                            $('#delete-service-charge').prop('checked',false);
                        }

                        if (data.updt_status_service_charge == 1) {
                            $('#update-status-service-charge').prop('checked',true);
                        }else{
                            $('#update-status-service-charge').prop('checked',false);
                        }

                        if (data.cand_delete_file == 1) {
                            $('#cand-delete-file').prop('checked',true);
                        }else{
                            $('#cand-delete-file').prop('checked',false);
                        }

                        if (data.candidate_status == 1) {
                            $('#candidate-status').prop('checked',true);
                        }else{
                            $('#candidate-status').prop('checked',false);
                        }

                        if (data.candidate_reset_status == 1) {
                            $('#candidate-reset-status').prop('checked',true);
                        }else{
                            $('#candidate-reset-status').prop('checked',false);
                        }

                        if (data.cand_add_edit == 1) {
                            $('#cand-add-edit').prop('checked', true);
                        } else {
                            $('#cand-add-edit').prop('checked', false);
                        }

                        if (data.cand_sourcing_date == 1) {
                            $('#cand-sourcing-date').prop('checked', true);
                        } else {
                            $('#cand-sourcing-date').prop('checked', false);
                        }

                        if (data.delete_cand_payment == 1) {
                            $('#delete-cand-payment').prop('checked',true);
                        }else{
                            $('#delete-cand-payment').prop('checked',false);
                        }

                        if (data.cand_edit_careoff == 1) {
                            $('#cand-edit-careoff').prop('checked',true);
                        }else{
                            $('#cand-edit-careoff').prop('checked',false);
                        }

                        if (data.cand_edit_associate == 1) {
                            $('#cand-edit-associate').prop('checked',true);
                        }else{
                            $('#cand-edit-associate').prop('checked',false);
                        }

                        if (data.edit_cand_payment == 1) {
                            $('#edit-cand-payment').prop('checked',true);
                        }else{
                            $('#edit-cand-payment').prop('checked',false);
                        }



                        if (data.add_contactp == 1) {
                            $('#add-contactp').prop('checked',true);
                        }else{
                            $('#add-contactp').prop('checked',false);
                        }

                        if (data.edit_contactp == 1) {
                            $('#edit-contactp').prop('checked',true);
                        }else{
                            $('#edit-contactp').prop('checked',false);
                        }

                        if (data.view_contactp == 1) {
                            $('#view-contactp').prop('checked',true);
                        }else{
                            $('#view-contactp').prop('checked',false);
                        }

                        if (data.delete_contactp == 1) {
                            $('#delete-contactp').prop('checked',true);
                        }else{
                            $('#delete-contactp').prop('checked',false);
                        }

                        if (data.lead_owner_transfer == 1) {
                            $('#lead-onwer-transfer').prop('checked',true);
                        }else{
                            $('#lead-onwer-transfer').prop('checked',false);
                        }

                        if (data.contact_bulk_whatsapp_send == 1) {
                            $('#contact-bulk-whatsapp-send').prop('checked',true);
                        }else{
                            $('#contact-bulk-whatsapp-send').prop('checked',false);
                        }

                        if (data.careoff_transfer == 1) {
                            $('#careoff-transfer').prop('checked',true);
                        }else{
                            $('#careoff-transfer').prop('checked',false);
                        }

                        if (data.image_host == 1) {
                            $('#image-host').prop('checked',true);
                        }else{
                            $('#image-host').prop('checked',false);
                        }



                    }
                });
            });
        });
        $(document).ready(function () {
            let staffId = "{{ session('staffid') }}";

            if (staffId) {
                $('#staff_id').val(staffId).trigger('change');
            }
        });
    </script>

    <script>
        // $(document).ready(function(){
        //     $('#full-access').on('change',function(){
        //         if (this.checked) {
        //             $('#bookings').prop("checked",true);
        //             $('#employer').prop("checked",true);
        //             $('#candidate').prop("checked",true);
        //             $('#client').prop("checked",true);
        //             $('#partner').prop("checked",true);
        //             $('#settings').prop("checked",true);
        //         } else {
        //             $('#bookings').prop("checked",false);
        //             $('#employer').prop("checked",false);
        //             $('#candidate').prop("checked",false);
        //             $('#client').prop("checked",false);
        //             $('#partner').prop("checked",false);
        //             $('#settings').prop("checked",false);
        //         }
        //     });
        // });

        $(document).ready(function(){
            $('#bookings').on('change',function(){

                if (this.checked) {
                    $("input.groups1").removeAttr("disabled");
                } else {
                    $("input.groups1").attr("disabled", true);
                    $('input.groups1').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#employer').on('change',function(){

                if (this.checked) {
                    $("input.groups2").removeAttr("disabled");
                } else {
                    $("input.groups2").attr("disabled", true);
                    $('input.groups2').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#candidate').on('change',function(){

                if (this.checked) {
                    $("input.groups3").removeAttr("disabled");
                } else {
                    $("input.groups3").attr("disabled", true);
                    $('input.groups3').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#testimonial').on('change',function(){

                if (this.checked) {
                    $("input.groups18").removeAttr("disabled");
                } else {
                    $("input.groups18").attr("disabled", true);
                    $('input.groups18').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#google_review').on('change',function(){

                if (this.checked) {
                    $("input.groups19").removeAttr("disabled");
                } else {
                    $("input.groups19").attr("disabled", true);
                    $('input.groups19').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#client').on('change',function(){

                if (this.checked) {
                    $("input.groups4").removeAttr("disabled");
                } else {
                    $("input.groups4").attr("disabled", true);
                    $('input.groups4').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#partner').on('change',function(){

                if (this.checked) {
                    $("input.groups5").removeAttr("disabled");
                } else {
                    $("input.groups5").attr("disabled", true);
                    $('input.groups5').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#file-manager').on('change',function(){

                if (this.checked) {
                    $("input.groups17").removeAttr("disabled");
                } else {
                    $("input.groups17").attr("disabled", true);
                    $('input.groups17').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#settings').on('change',function(){

                if (this.checked) {
                    $("input.groups6").removeAttr("disabled");
                } else {
                    $("input.groups6").attr("disabled", true);
                    $('input.groups6').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#associate').on('change',function(){

                if (this.checked) {
                    $("input.groups7").removeAttr("disabled");
                } else {
                    $("input.groups7").attr("disabled", true);
                    $('input.groups7').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#dynamic').on('change',function(){

                if (this.checked) {
                    $("input.groups8").removeAttr("disabled");
                } else {
                    $("input.groups8").attr("disabled", true);
                    $('input.groups8').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#contactp').on('change',function(){
                if (this.checked) {
                    $("input.groups9").removeAttr("disabled");
                } else {
                    $("input.groups9").attr("disabled", true);
                    $('input.groups9').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#whatsapp-plus').on('change',function(){
                if (this.checked) {
                    $("input.groups10").removeAttr("disabled");
                } else {
                    $("input.groups10").attr("disabled", true);
                    $('input.groups10').prop("checked",false);
                }
            });
        });

        
        $(document).ready(function(){
            $('#sms-campaign-module').on('change',function(){
                if (this.checked) {
                    $("input.group_sms").removeAttr("disabled");
                } else {
                    $("input.group_sms").attr("disabled", true);
                    $('input.group_sms').prop("checked",false);
                }
            });
        });


        $(document).ready(function(){
            $('#whatsapp-normal').on('change',function(){
                if (this.checked) {
                    $("input.groups11").removeAttr("disabled");
                } else {
                    $("input.groups11").attr("disabled", true);
                    $('input.groups11').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#todo').on('change',function(){
                if (this.checked) {
                    $("input.groups12").removeAttr("disabled");
                } else {
                    $("input.groups12").attr("disabled", true);
                    $('input.groups12').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#deal-pipeline').on('change',function(){
                if (this.checked) {
                    $("input.groupsDeal").removeAttr("disabled");
                } else {
                    $("input.groupsDeal").attr("disabled", true);
                    $('input.groupsDeal').prop("checked",false);
                }
            });
        });

        

        $(document).ready(function(){
            $('#leads').on('change',function(){
                if (this.checked) {
                    $("input.groups15").removeAttr("disabled");
                } else {
                    $("input.groups15").attr("disabled", true);
                    $('input.groups15').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#employerplus').on('change',function(){
                if (this.checked) {
                    $("input.groups13").removeAttr("disabled");
                } else {
                    $("input.groups13").attr("disabled", true);
                    $('input.groups13').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#finance').on('change',function(){
                if (this.checked) {
                    $("input.groups14").removeAttr("disabled");
                } else {
                    $("input.groups14").attr("disabled", true);
                    $('input.groups14').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#allcontact').on('change',function(){
                if (this.checked) {
                    $("input.groups16").removeAttr("disabled");
                } else {
                    $("input.groups16").attr("disabled",true);
                    $("input.groups16").prop("checked",false);
                }
            });
        });



    </script>

    {{-- Permission page UI: groups modules to match the Admin Sidebar hierarchy and
         adds an instant client-side search. Pure DOM re-arrangement/filtering only -
         no permission names/ids/values are touched, so backend logic is unaffected. --}}
    <script>
        $(document).ready(function () {
            var permissionGroups = {
                main: ['leads', 'allcontact', 'todo', 'deal-pipeline', 'bookings', 'employer', 'employerplus', 'candidate', 'testimonial', 'google_review', 'associate', 'client', 'contactp', 'partner', 'file-manager'],
                finance: ['finance'],
                campaigns: ['whatsapp-normal', 'whatsapp-plus', 'sms-campaign-module'],
                settings: ['settings', 'dynamic']
            };

            Object.keys(permissionGroups).forEach(function (groupKey) {
                var $body = $('.permission-group[data-group="' + groupKey + '"] .permission-group-body');
                permissionGroups[groupKey].forEach(function (moduleId) {
                    var $checkbox = $('#' + moduleId);
                    if (!$checkbox.length) return;
                    var $block = $checkbox.closest('.row.mb-3, .mb-3');
                    if ($block.length) {
                        $body.append($block);
                    }
                });
            });

            var $searchInput = $('#permission-search');
            var $searchCount = $('#permission-search-count');

            $searchInput.on('input', function () {
                var term = $(this).val().trim().toLowerCase();
                var visibleModules = 0;

                $('#permission-groups .permission-group-body > div').each(function () {
                    var $block = $(this);
                    var $table = $block.find('table');
                    var titleText = $table.find('thead label').first().text().trim().toLowerCase();
                    var titleMatch = term === '' || titleText.indexOf(term) !== -1;
                    var anyRowVisible = false;

                    $table.find('tbody tr').each(function () {
                        var $row = $(this);
                        var rowMatch = false;

                        $row.find('td').each(function () {
                            var $cell = $(this);
                            var cellText = $cell.find('label').text().trim().toLowerCase();
                            var cellMatch = titleMatch || cellText.indexOf(term) !== -1;
                            $cell.toggleClass('d-none', !cellMatch);
                            if (cellMatch) rowMatch = true;
                        });

                        $row.toggleClass('d-none', !rowMatch);
                        if (rowMatch) anyRowVisible = true;
                    });

                    var blockVisible = titleMatch || anyRowVisible;
                    $block.toggleClass('d-none', !blockVisible);
                    if (blockVisible) visibleModules++;
                });

                $('#permission-groups .permission-group').each(function () {
                    var $group = $(this);
                    var hasVisible = $group.find('.permission-group-body > div').not('.d-none').length > 0;
                    $group.toggleClass('d-none', !hasVisible);
                });

                $searchCount.text(term === '' ? '' : (visibleModules + ' module(s) found'));
            });
        });
    </script>

@endsection
