@extends('layout.admin.admin_layout')

@section('title', 'Email Template List')

@section('page-style')
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    <!-- Page Card -->
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Email Template List</h5>
            <button class="btn btn-sm btn-primary addtemplate" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddTemplate">
                <i class="ti ti-plus me-1"></i> Add Template
            </button>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table datatables-templates border-top">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Template Name</th>
                        <th>Subject</th>
                        <th>SMTP Name</th>
                        <th>Status</th>
                        <th>Public</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Offcanvas: Add Template -->
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="offcanvasAddTemplate" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Add Email Template</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="addEmailTemplateForm" action="{{ route('admin.email.templateStore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-template-for" class="form-label">Template For</label>
                            <select name="template_for" id="add-template-for" class="form-control select2" data-placeholder="Select Template For" data-allow-clear="true" required>
                                <option value=""></option>
                                <option value="contactpluses">Contact Plus</option>
                                <option value="allcontacts">Allcontact</option>
                                <option value="associates">Associate</option>
                                <option value="partners">Partner</option>
                                <option value="employers">Employer</option>
                                <option value="leads">Leads</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Template Name</label>
                            <input type="text" name="template_name" class="form-control" placeholder="Enter Template Name" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="Enter Subject" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">SMTP</label>
                            <select name="smtp_id" class="form-select select2 w-100" data-placeholder="Select SMTP">
                                <option value="">Select SMTP</option>
                                @foreach($smtpLists as $smtp)
                                    <option value="{{ $smtp->id }}">{{ $smtp->smtp_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Variable Fields -->
                <div id="dispVars" class="dispVars" style="display:none;"></div>

                <!-- Add Variable Button Block -->
                <div class="row dispAddVarsBtnBlock" id="dispAddVarsBtnBlock" style="display:none;">
                    <div class="col-md-12">
                        <button type="button"
                                class="btn btn-sm btn-primary DispAddVarBtn float-end">
                            Add Variable
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Body</label>
                    <x-quill-editor
                        id="add_email_body"
                        name="email_body"
                        :value="old('email_body', $emailCampaign->email_body ?? '')"
                    />
                </div>


                <div class="mb-3">
                    <label class="form-label">Attachment (optional)</label>
                    <input type="file" name="photo" class="form-control">
                </div>
                <div class="form-check form-switch mb-3">
                    <input type="checkbox" name="public" class="form-check-input" id="publicTemplate">
                    <label class="form-check-label" for="publicTemplate">Make Public</label>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Offcanvas: Edit Template -->
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="editEmailTemplate" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Edit Email Template</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="editEmailTemplateForm" action="{{ route('admin.email.templateUpdate') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit-template-for" class="form-label">Template For</label>
                            <select name="template_for" id="edit_template_for" class="form-control select2" data-placeholder="Select Template For" data-allow-clear="true" required>
                                <option value=""></option>
                                <option value="contactpluses">Contact Plus</option>
                                <option value="allcontacts">Allcontact</option>
                                <option value="associates">Associate</option>
                                <option value="partners">Partner</option>
                                <option value="employers">Employer</option>
                                <option value="leads">Leads</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Template Name</label>
                            <input type="text" name="template_name" id="edit_template_name" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" id="edit_subject" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">SMTP</label>
                            <select name="smtp_id" id="edit_smtp_id" class="form-select select2" data-placeholder="Select SMTP">
                                <option value="">Select SMTP</option>
                                @foreach($smtpLists as $smtp)
                                    <option value="{{ $smtp->id }}">{{ $smtp->smtp_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Leads Employer Dynamic Fields -->
                <div id="responseValueLeadsEmployer" class="disviewaleadsEmployere"></div>
                <div id="dispLeadsEmployere" class="disviewaleadsEmployere" style="display:none;"></div>

                <div id="editVars"></div>

                <div class="row" id="editAddVarBtnRow" style="display:none;">
                    <div class="col-md-12 mt-2">
                        <button type="button" class="btn btn-sm btn-primary" id="editAddVarBtn">
                            Add Variable
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Body</label>
                    <x-quill-editor id="edit_email_body" name="email_body" value="" />
                </div>
              
                <div class="mb-3">
                    <label class="form-label">Attachment (optional)</label>
                    <input type="file" name="photo" class="form-control">
                    <div id="edit_attachment_preview" class="mt-2"></div>
                </div>

                <div class="form-check form-switch mb-3">
                    <input type="checkbox" name="public" class="form-check-input" id="edit_public">
                    <label class="form-check-label" for="edit_public">Make Public</label>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Change Status Modal -->
    <div class="modal fade" id="statusChange" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.email.templateStatusUpdate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tempchstID" id="tempchstID">
                    <div class="modal-header">
                        <h5 class="modal-title">Change Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-check form-check-inline mt-2">
                            <input class="form-check-input" type="radio" name="status" id="changestact" value="1">
                            <label class="form-check-label" for="changestact">Active</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="changestdeact" value="0">
                            <label class="form-check-label" for="changestdeact">Inactive</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger btn-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteEmailTemplate" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.email.templateDelete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="delete_id" id="delete_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Template</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this template?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@section('vendor-script')

<script src="{{ asset(('vendors/js/tables/datatable/buttons.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>

<script>
$(document).ready(function() {  

    function cleanText(val) {
        return String(val).replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }

    // -------------------------------------------------------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------------------------------
    //                                                           Add Template
    // -------------------------------------------------------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------------------------------

    let rowIndex = 0;

    // ---------------------------------------------
    // Template Dropdown Change (ADD Mode)
    // ---------------------------------------------
    $('#add-template-for').on('change', function () {

        // Clear rows
        $('#dispVars').html('').hide();

        // Hide Add Variable button wrapper
        $('#dispAddVarsBtnBlock').hide();

        // Reset counter
        rowIndex = 0;

        let templateFor = $(this).val();

        // If selected → show the container + button
        if (templateFor !== "") {
            $('#dispVars').show();
            $('#dispAddVarsBtnBlock').show();
        }
    });

    // ---------------------------------------------
    // ADD VARIABLE (ADD Mode)
    // ---------------------------------------------
    $(document).on('click', '.DispAddVarBtn', function () {

        let templateFor = $('#add-template-for').val();

        if (templateFor === "") {
            alert("Please select Template For first.");
            return;
        }

        $.ajax({
            url: "{{ route('admin.whatsapp.getTemplateVariables') }}",
            type: "POST",
            data: {
                template_for: templateFor,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {

                // Ensure response arrays exist
                let metaFields = response.meta_fields ?? [];
                let tableFields = response.table_fields ?? [];

                rowIndex++;

                let html = `
                    <div class="row mb-3 variableRow" id="row_${rowIndex}">

                        <!-- META VARIABLE -->
                        <div class="col-md-6">
                            <label class="form-label">Field Variable *</label>
                            <select name="field_variable[]" class="form-select">
                                ${metaFields.map(f => `<option value="${f}">${f}</option>`).join('')}
                            </select>
                        </div>

                        <!-- TEMPLATE TYPE VARIABLE -->
                        <div class="col-md-6">
                        <label class="form-label">${cleanText(templateFor)} Variable *</label>
                            <select name="assign_variable[]" class="form-select">
                                ${tableFields.map(f => `<option value="${f}">${f}</option>`).join('')}
                            </select>
                        </div>

                        <div class="col-md-12 mt-2">
                            <button type="button"
                                    class="btn btn-danger btn-sm removeVarRow"
                                    data-id="${rowIndex}">
                                Remove
                            </button>
                        </div>

                    </div>
                `;

                $('#dispVars').append(html).show();
            }
        });
    });

    // ---------------------------------------------
    // REMOVE VARIABLE ROW (ADD MODE)
    // ---------------------------------------------
    $(document).on("click", ".removeVarRow", function () {
        let id = $(this).data("id");
        $("#row_" + id).remove();
    });


    // -------------------------------------------------------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------------------------------
    //                                                          Edit / Update Template
    // -------------------------------------------------------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------------------------------

    let editRowIndex = 0;
    let editLoading = false;

    // ---------------------------------------------
    // EDIT Template Dropdown Change
    // ---------------------------------------------
    $('#edit_template_for').on('change', function () {

        if (editLoading) return;

        $('#editVars').html('').hide();
        $('#editAddVarBtnRow').hide();
        editRowIndex = 0;

        let templateFor = $(this).val();

        if (templateFor !== "") {
            $('#editVars').show();
            $('#editAddVarBtnRow').show();
        }
    });

    // ---------------------------------------------
    // FUNCTION TO APPEND ROW
    // ---------------------------------------------
    function appendEditRow(templateFor, metaFields, tableFields, selectedMeta = "", selectedAssign = "") {

        editRowIndex++;

        let html = `
            <div class="row mb-3 variableRow" id="edit_row_${editRowIndex}">

                <div class="col-md-6">
                    <label class="form-label">Meta Variable *</label>
                    <select name="field_variable[]" class="form-select">
                        ${metaFields.map(m =>
                            `<option value="${m}" ${m == selectedMeta ? "selected" : ""}>${m}</option>`
                        ).join('')}
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">${cleanText(templateFor)} Variable *</label>
                    <select name="assign_variable[]" class="form-select">
                        ${tableFields.map(t =>
                            `<option value="${t}" ${t == selectedAssign ? "selected" : ""}>${t}</option>`
                        ).join('')}
                    </select>
                </div>

                <div class="col-md-12 mt-2">
                    <button type="button"
                            class="btn btn-danger btn-sm removeEditVarRow"
                            data-id="${editRowIndex}">
                        Remove
                    </button>
                </div>

            </div>
        `;

        $("#editVars").append(html).show();
    }


    // ---------------------------------------------
    // ADD VARIABLE (EDIT MODE → AJAX call REQUIRED)
    // ---------------------------------------------
    $(document).on("click", "#editAddVarBtn", function () {

        let templateFor = $('#edit_template_for').val();

        if (templateFor === "") {
            alert("Please select Template For first.");
            return;
        }

        $.ajax({
            url: "{{ route('admin.whatsapp.getTemplateVariables') }}",
            type: "POST",
            data: {
                template_for: templateFor,
                _token: "{{ csrf_token() }}"
            },

            success: function (response) {
                appendEditRow(templateFor, response.meta_fields, response.table_fields);
            }
        });
    });


    // ---------------------------------------------
    // REMOVE ROW
    // ---------------------------------------------
    $(document).on("click", ".removeEditVarRow", function () {
        let id = $(this).data("id");
        $("#edit_row_" + id).remove();
    });


    // ✅ Handle background color on form submit
    $('#addEmailTemplateForm').on('submit', function() {
        let addorDiv = $('#add_email_body').find('.quill-editor');
        let addbg = addorDiv.css('background-color');
        const add_email_body_bg = rgbToHex(addbg);
        $('.quill-bg').val( add_email_body_bg );
    });

    // ✅ Handle background color on form submit
    $('#editEmailTemplateForm').on('submit', function() {
        let editorDiv = $('#edit_email_body').find('.quill-editor');
        let bg = editorDiv.css('background-color');
        const edit_email_body_bg = rgbToHex(bg);
        $('.quill-bg').val( edit_email_body_bg );
    });

    // ✅ Load data into Edit Offcanvas
    $('#editEmailTemplate').on('show.bs.offcanvas', function (e) {
        const id = $(e.relatedTarget).data('id');

        $.get('{{ route("admin.email.templateEdit") }}', { id }, function (data) {           
            /* ----------------------------
            BASIC FIELDS
            ---------------------------- */
            $('#edit_id').val(data.id);
            $('#edit_template_for').val(data.template_for).trigger('change');
            $('#edit_template_name').val(data.template_name);
            $('#edit_subject').val(data.subject);
            $('#edit_smtp_id').val(data.smtp_id).trigger('change');
            $('#edit_public').prop('checked', data.public == 1);

            /* ----------------------------
            QUILL EDITOR LOAD
            ---------------------------- */
            const wrapper = document.querySelector('#edit_email_body');
            if (!wrapper) return console.error("❌ edit_email_body wrapper NOT found");

            const editorDiv     = wrapper.querySelector('.quill-editor');
            const hiddenInput   = wrapper.querySelector('.quill-content');
            const bgPicker      = wrapper.querySelector('.bgColorPicker');
            const hiddenBgInput = wrapper.querySelector('.quill-bg');

            if (!editorDiv) return console.error("❌ .quill-editor NOT found inside #edit_email_body");

            const quill = Quill.find(editorDiv);
            if (!quill) return console.error("❌ Quill instance NOT found for edit_email_body");

            /* ----------------------------
            SET EMAIL HTML
            ---------------------------- */
            const content = data.email_body ?? "";
            quill.root.innerHTML = content;
            if (hiddenInput) hiddenInput.value = content;

            /* ----------------------------
            LOAD BACKGROUND COLOR
            ---------------------------- */
            let bg = "#ffffff";

            // 1️⃣ If saved in DB → use it
            if (data.email_body_bg) {
                bg = data.email_body_bg;
            }
            // 2️⃣ Otherwise keep previous background set by component
            else if (hiddenBgInput && hiddenBgInput.value) {
                bg = hiddenBgInput.value;
            }

            // Apply background
            editorDiv.style.backgroundColor = bg;

            // Sync color picker + hidden input
            if (bgPicker) bgPicker.value = bg;
            if (hiddenBgInput) hiddenBgInput.value = bg;

            /* ----------------------------
            ATTACHMENT PREVIEW
            ---------------------------- */
            $('#edit_attachment_preview').html(
                data.attachment_url
                    ? `
                    <div class="mt-2 d-flex align-items-center justify-content-between border p-2 rounded">
                        <div>
                            <p class="mb-1"><strong>Current File:</strong></p>
                            <a href="${data.attachment_url}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-download"></i> Download Attachment
                            </a>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm delete-attachment" data-id="${data.id}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>`
                    : `<small class="text-muted">No attachment uploaded.</small>`
            );


            /* ----------------------------
            Add Variable
            ---------------------------- */

            let templateFor = data.template_for;

            if (data.field_variable && data.assign_variable) {

                let fields = data.field_variable.split(",");
                let assigns = data.assign_variable.split(",");

                // 🔥 GET META + TABLE FIELDS
                $.ajax({
                    url: "{{ route('admin.whatsapp.getTemplateVariables') }}",
                    type: "POST",
                    data: {
                        template_for: templateFor,
                        _token: "{{ csrf_token() }}"
                    },

                    success: function (response) {

                        // Load saved rows
                        fields.forEach((f, i) => {
                            appendEditRow(templateFor, response.meta_fields, response.table_fields, f, assigns[i]);
                        });

                        $("#editAddVarBtnRow").show();
                    },

                    complete: function () {
                        editLoading = false;
                    }
                });
            } 
            else {
                editLoading = false;
            }

        });
    });

    /* ----------------------------
    Convert RGB → HEX (Safe)
    ---------------------------- */
    function rgbToHex(rgb) {
        if (!rgb) return "#ffffff";

        if (rgb.startsWith("#")) return rgb; // already HEX

        const parts = rgb.match(/\d+/g);
        if (!parts || parts.length < 3) return "#ffffff";

        return (
            "#" +
            parts
                .slice(0, 3)
                .map(n => parseInt(n).toString(16).padStart(2, "0"))
                .join("")
        );
    }

    // ✅ DataTable
    $('.datatables-templates').DataTable({
        ajax: {
            url: '{{ url("admin/email-template-list/json") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' }
        },
        order: [[0, 'desc']], 
        columns: [
            { data: 'id' },
            { data: 'template_name' },
            { data: 'subject' },
            { data: 'smtp_name' },
            {
                data: 'status',
                render: function(data, type, full) {
                    const badgeClass = data == 1 ? 'bg-label-success' : 'bg-label-warning';
                    const text = data == 1 ? 'Active' : 'Inactive';
                    return `<span class="badge ${badgeClass}" data-bs-toggle="modal" data-bs-target="#statusChange" data-id="${full.id}">${text}</span>`;
                }
            },
            {
                data: 'public',
                render: data => data == 1
                    ? '<span class="badge bg-label-info">Public</span>'
                    : '<span class="badge bg-label-secondary">Private</span>'
            },
            {
                data: 'id',
                render: data => `
                    <div class="d-flex align-items-center">
                        <a href="javascript:void(0)" class="text-primary" data-bs-toggle="offcanvas" data-bs-target="#editEmailTemplate" data-id="${data}">
                            <i class="ti ti-edit me-1"></i>
                        </a>
                        <a href="javascript:void(0)" class="text-danger" data-bs-toggle="modal" data-bs-target="#deleteEmailTemplate" data-id="${data}">
                            <i class="ti ti-trash"></i>
                        </a>
                    </div>`
            }
        ]
    });

    // ✅ Status Modal
    $('#statusChange').on('show.bs.modal', function(e) {
        const id = $(e.relatedTarget).data('id');
        $('#tempchstID').val(id);
        $.get('{{ route("admin.email.templateStatus") }}', { id }, function(data) {
            $(`input[name="status"][value="${data.status}"]`).prop('checked', true);
        });
    });

    // ✅ Delete Modal
    $('#deleteEmailTemplate').on('show.bs.modal', function(e) {
        $('#delete_id').val($(e.relatedTarget).data('id'));
    });

    // ✅ Delete attachment via AJAX
    $(document).on('click', '.delete-attachment', function() {
        const id = $(this).data('id');
        if (!confirm('Are you sure you want to delete this attachment?')) return;

        $.ajax({
            url: '{{ route("admin.email.deleteAttachment") }}',
            method: 'POST',
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr['success']('Attachment deleted successfully', 'Success');
                    $('#edit_attachment_preview').html(`<small class="text-muted">No attachment uploaded.</small>`);
                } else {
                    toastr['error']('Failed to delete attachment', 'Error');
                }
            },
            error: function() {
                toastr['error']('Server error while deleting file', 'Error');
            }
        });
    });

    // ✅ Select2 Initialization
    $(function () {
        var select2 = $('.select2');
        // For all Select2
        if (select2.length) {
        select2.each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
            dropdownParent: $this.parent(),
            });
        });
        }
    });
});
</script>
@endsection
