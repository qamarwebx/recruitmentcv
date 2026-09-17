@extends('layout.admin.admin_layout')

@section('title','Email SMTP List')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

    @if (Auth::guard('admin')->user()->user_type == 2)
        @if (isset($perm) && $perm->add_email_smtp == 0)
        <style>.addsmtp{ display: none !important; }</style>
        @endif

        @if (isset($perm) && $perm->edit_email_smtp == 0)
        <style>.edtemplate{ display: none !important; }</style>
        @endif

        @if (isset($perm) && $perm->delete_email_smtp == 0)
        <style>.deltemplate{ display: none !important; }</style>
        @endif

        @if (isset($perm) && $perm->assign_email_smtp == 0)
        <style>.assigntosmtp{ display: none !important; }</style>
        @endif
    @endif
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="card">
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
                        <th>SMTP Name</th>
                        <th>Host</th>
                        <th>Port</th>
                        <th>From Email</th>
                        <th>From Name</th>
                        <th>Status</th>
                        <th>Assign To</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Add SMTP -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddSMTP" aria-labelledby="offcanvasAddSMTPLabel"  data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="offcanvas-header">
            <h5 id="offcanvasAddSMTPLabel" class="offcanvas-title">Add Email SMTP</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0" id="addSMTPForm" action="{{ route('admin.email.smtp.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">SMTP Name <span class="text-danger">*</span></label>
                    <input type="text" name="smtp_name" class="form-control" placeholder="Enter SMTP name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mail Host <span class="text-danger">*</span></label>
                    <input type="text" name="mail_host" class="form-control" placeholder="smtp.gmail.com">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mail Port <span class="text-danger">*</span></label>
                    <input type="text" name="mail_port" class="form-control" placeholder="465 / 587">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mail Username <span class="text-danger">*</span></label>
                    <input type="text" name="mail_username" class="form-control" placeholder="your@email.com">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mail Password <span class="text-danger">*</span></label>
                    <input type="password" name="mail_password" class="form-control" placeholder="********">
                </div>
                <div class="mb-3">
                    <label class="form-label">Encryption</label>
                    <select name="mail_encryption" class="form-control">
                        <option value="">None</option>
                        <option value="ssl">SSL</option>
                        <option value="tls">TLS</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">From Email <span class="text-danger">*</span></label>
                    <input type="email" name="from_address" class="form-control" placeholder="no-reply@domain.com">
                </div>
                <div class="mb-3">
                    <label class="form-label">From Name <span class="text-danger">*</span></label>
                    <input type="text" name="from_name" class="form-control" placeholder="Company Name">
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" placeholder="Any remarks..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Edit SMTP -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="editSMTP" aria-labelledby="editSMTPLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="offcanvas-header">
            <h5 id="editSMTPLabel" class="offcanvas-title">Edit Email SMTP</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form id="editSMTPForm" action="{{ route('admin.email.smtp.update') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">

                <div class="mb-3">
                    <label class="form-label">SMTP Name</label>
                    <input type="text" name="smtp_name" id="edit_smtp_name" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mail Host</label>
                    <input type="text" name="mail_host" id="edit_mail_host" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mail Port</label>
                    <input type="text" name="mail_port" id="edit_mail_port" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mail Username</label>
                    <input type="text" name="mail_username" id="edit_mail_username" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mail Password</label>
                    <input type="password" name="mail_password" id="edit_mail_password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Encryption</label>
                    <select name="mail_encryption" id="edit_mail_encryption" class="form-control">
                        <option value="">None</option>
                        <option value="ssl">SSL</option>
                        <option value="tls">TLS</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">From Email</label>
                    <input type="email" name="from_address" id="edit_from_address" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">From Name</label>
                    <input type="text" name="from_name" id="edit_from_name" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" id="edit_notes" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Assign To SMTP -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="smtpAssignTo" aria-labelledby="smtpAssignToLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="offcanvas-header">
            <h5 id="smtpAssignToLabel" class="offcanvas-title">SMTP Assign To</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form id="smtpAssignForm" action="{{ route('admin.email.smtp.getassigntoStore') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="editid2">
                <div class="mb-3">
                    <label class="form-label">Assign To</label>
                    <select name="api_assign_to[]" id="edit-smtp-assign-to" class="form-control select2" multiple data-placeholder="Select usage">
                        <option value="transactional">Transactional</option>
                        <option value="marketing">Marketing</option>
                        <option value="notification">Notification</option>
                        <option value="system_alert">System Alert</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Delete SMTP -->
    <div class="modal fade" id="deleteSMTP" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.email.smtp.delete') }}" method="POST">
                @csrf
                <input type="hidden" name="delID" id="delID">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete SMTP</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this SMTP configuration?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!--Change Status Modal Start -->
    <div class="modal fade" id="statusChange" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Change Status</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('admin.email.smtp.statusUpdate') }}" method="POST">
            @csrf
            <input type="hidden" name="statusID" id="statusID">
            <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label d-block">Select Status:</label>
                    <div class="form-check form-check-inline mt-3">
                    <input class="form-check-input" type="radio" name="status" id="changestact" value="1" />
                    <label class="form-check-label" for="changestact">Active</label>
                    </div>
                    <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="changestdeact" value="0" />
                    <label class="form-check-label" for="changestdeact">Inactive</label>
                    </div>
                </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary btn-sm">Save</button>
            </div>
        </form>
        </div>
    </div>
    </div>
    <!--Change Status Modal End -->


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


<!-- Page JS -->
<script src="{{ asset('admin/assets/pages/app-email-smtp-list.js') }}"></script>

<!-- Validation JS -->
<script src="{{ asset('admin/assets/pages/validation/email-smtp-validation.js') }}"></script>


<script>
$(document).ready(function() {
    var select2 = $('.select2');
    if (select2.length) {
        select2.each(function() {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>').select2({
                dropdownParent: $this.parent()
            });
        });
    }

    // Edit SMTP
    $('#editSMTP').on('show.bs.offcanvas', function(e) {
        var editID = $(e.relatedTarget).data('id');
        $('#edit_id').val(editID);
        $.ajax({
            url: '{{ url("admin/email-smtp-list/edit") }}',
            method: "POST",
            data: { "id": editID, "_token": "{{ csrf_token() }}" },
            success: function(data) {
                $('#edit_smtp_name').val(data.smtp_name);
                $('#edit_mail_host').val(data.mail_host);
                $('#edit_mail_port').val(data.mail_port);
                $('#edit_mail_username').val(data.mail_username);
                $('#edit_mail_password').val(data.mail_password);
                $('#edit_mail_encryption').val(data.mail_encryption);
                $('#edit_from_address').val(data.from_address);
                $('#edit_from_name').val(data.from_name);
                $('#edit_notes').val(data.notes);
            }
        });
    });

    // Assign To SMTP
    $('#smtpAssignTo').on('show.bs.offcanvas', function(e) {
        var smtpID = $(e.relatedTarget).data('id');
        $('#editid2').val(smtpID);
        $.ajax({
            url: "{{ route('admin.email.smtp.getassignto') }}",
            method: "GET",
            data: { id: smtpID },
            success: function(data) {
                if (data.smtp_assign_to) {
                    var assignList = data.smtp_assign_to.split(",");
                    $('#edit-smtp-assign-to').val(assignList).change();
                }
            }
        });
    });

    // Delete SMTP
    $('#deleteSMTP').on('show.bs.modal', function(e) {
        var delID = $(e.relatedTarget).data('id');
        $('#delID').val(delID);
    });

    $('#statusChange').on('show.bs.modal', function (e) {
        var smtpID = $(e.relatedTarget).data('id');
        $('#statusID').val(smtpID);

        $.ajax({
            url: '{{ url("admin/email-smtp-list/edit") }}',
            method: 'POST',
            data: {
                id: smtpID,
                _token: '{{ csrf_token() }}'
            },
            success: function (data) {
                $("input[name='status'][value='" + data.status + "']").prop('checked', true);
            }
        });
    });



});
</script>
@endsection
