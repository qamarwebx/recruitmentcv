@extends('layout.admin.admin_layout')

@section('title','Contact')

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
    </style>

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="row g-4 mb-4 hideShowAddCompanyForm" @if(isset($contactsaveadminfilter) && $contactsaveadminfilter->saveaddcompanyform == 1) @else style="display: none" @endif>
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <form id="companyShortForm">
                        @csrf
                            
                        <!-- Textarea -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Company Raw Data <span class="text-danger">*</span>
                            </label>

                            <textarea
                                name="raw_company_text"
                                id="raw_company_text"
                                rows="5"
                                class="form-control form-control-lg"
                                placeholder="Paste company raw data here..."
                                required></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="row g-3">

                            <div class="col-lg-3 col-md-6">
                                <button type="button"
                                       class="btn btn-info w-100 py-2"  
                                        id="clearPromptBtn">
                                    <i class="ti ti-trash me-2"></i>
                                    Clear
                                </button>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <button type="button"
                                        class="btn btn-success w-100 py-2"
                                        id="pastePromptBtn">
                                    <i class="ti ti-clipboard me-2"></i>
                                    Paste
                                </button>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <button type="submit"
                                        class="btn btn-primary w-100 py-2"
                                        id="saveBtn">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Save Company
                                </button>
                            </div>

                             <div class="col-lg-3 col-md-6">
                                <button type="button"
                                        class="btn btn-warning w-100 py-2"
                                        id="copyPromptBtn">
                                    <i class="ti ti-copy me-2"></i>
                                    Copy Prompt
                                </button>
                            </div>  

                        </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4 short-form-div hideshowaddmodule" id="hideshowaddmodule" @if(isset($contactsaveadminfilter) && $contactsaveadminfilter->short_form_code == 1) @else style="display: none" @endif>
            <div class="col-sm-12 col-md-12 col-xl-12">
                <div class="card">
                    <div class="card-body nav-form-short-load">
                        <form action="{{ route('admin.contact.shortstore') }}" method="POST" id="shortsubmitcontact" class="shortsubmitcontact">
                            @csrf
                            <div class="row">
                                
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="short-add-primary-person" class="form-label">Primary Concern Person Name</label>
                                        <input type="text" name="prim_concern_name" id="short-add-primary-person" class="form-control" placeholder="Enter Primary Concern Person...">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="short-add-primary-contact" class="form-label">Primary Contact No</label>
                                        <input type="text" name="prim_contact" id="short-add-primary-contact" class="form-control" placeholder="Enter Primary Contact No...">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="short-add-primary-email" class="form-label">Email</label>
                                        <input type="email" name="prim_email" id="short-add-primary-email" class="form-control" placeholder="Enter Email..">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="short-add-business-type" class="form-label">Business Type <span class="text-danger">*</span></label>
                                        <select name="businesstype_id" id="short-add-business-type" class="form-select select22" data-placeholder="Select Business Type" data-allow-clear="true">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label" for="short-add-country-id">Country <span class="text-danger">*</span></label>
                                        <select name="country_id" id="short-add-country-id" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2" style="margin-top: 30px;">
                                    <div>
                                        <button class="btn btn-sm btn-primary" id="submitShortMsg" type="button">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @php
            $countryfs = DB::table('contactpluses as contactp')
                ->leftjoin('countries as country','contactp.country_id','=','country.id')
                ->select('contactp.country_id','country.name as cname')->groupBy('contactp.country_id','cname')->where('contactp.country_id','!=','')->get();
            $cityfs = DB::table('contactpluses as contactp')
                ->leftjoin('cities as city','contactp.city_id','=','city.id')
                ->select('contactp.city_id','city.name as citname')
                ->groupBy('contactp.city_id','citname')
                ->where('contactp.city_id','!=','')
                ->get();

            $groupfs = DB::table('contactpluses as contactp')
                ->leftjoin('groupms as groupm','groupm.id','=','contactp.group_id')
                ->select('contactp.group_id','groupm.name as grpname')
                ->groupBy('contactp.group_id','grpname')
                ->where('contactp.group_id','!=','')
                ->where('contactp.group_id','!=','0')
                ->get();


            $lifcsts = DB::table('contactpluses as contactp')
                ->leftjoin('lifecyclestatuses as lfs','lfs.id','=','contactp.lcs_id')
                ->select('contactp.lcs_id','lfs.name as lfsname')
                ->groupBy('contactp.lcs_id','lfsname')
                ->where('contactp.lcs_id','!=','')
                ->get();

            $leadstg = DB::table('contactpluses as contactp')
                ->leftjoin('leadstages as lstage','lstage.id','=','contactp.ls_id')
                ->select('contactp.ls_id','lstage.name as leadstage')
                ->groupBy('contactp.ls_id','leadstage')
                ->where('contactp.ls_id','!=','')
                ->get();
            $businessTypes = DB::table('contactpluses as contactp')
                ->leftjoin('businesstypes as businesstyp','businesstyp.id','=','contactp.businesstype_id')
                ->select('contactp.businesstype_id','businesstyp.name as businame')
                ->groupBy('contactp.businesstype_id','businame')
                ->where('contactp.businesstype_id','!=','')
                ->get();

            $sendTags = DB::table('contactpluses as contactp')->select('contactp.send_tag')->groupBy('contactp.send_tag')->where('contactp.send_tag','!=','')->get();

            $createdBys = DB::table('contactpluses as contactp')
                ->leftJoin('admins as admin','admin.id','=','contactp.staff_id')
                ->select('contactp.staff_id','admin.name as adminname')
                ->groupBy('contactp.staff_id','adminname')
                ->where('contactp.staff_id','!=','')
                ->get();

            $industruesFs = DB::table('contactpluses as contactp')
                ->leftJoin('industries as industry','industry.id','=','contactp.industry_id')
                ->select('contactp.industry_id','industry.name as industname')
                ->groupBy('contactp.industry_id','industname')
                ->where('contactp.industry_id','!=','')
                ->get();
            // $sendTags = DB::table('contactpluses as contactp2')->select('contactp2.send_tag')->groupBy('contactp2.send_tag')->where('contactp2.send_tag','!=','')->get();
        @endphp

        <!-- Users List Table -->
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
                    <button class="btn btn-xs btn-primary filterpanel" data-bs-toggle="modal" data-bs-target="#filterpanel"><i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter</button>

                    <div class="btn-group mx-2">
                        <button class="btn btn-primary btn-sm dropdown-toggle bulkactions" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkimport">
                                <i class="ti ti-refresh me-0 me-sm-1 ti-xs"></i> Import Contact
                            </a>

                            <!-- ✅ Add Company (Switch Style) -->
                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" name="add_company_form" value="1" id="add_company_toggle" class="switch-input add-company-form" 
                                    @if(isset($contactsaveadminfilter) && $contactsaveadminfilter->saveaddcompanyform == 1) checked @endif />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Add Company</span>
                                </label>
                            </div>

                            <!-- Existing Short Form -->
                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" name="short_form_code" value="1" id="short_form_code2" class="switch-input short-form"
                                        @if(isset($contactsaveadminfilter) && $contactsaveadminfilter->short_form_code == 1) checked @endif />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Short Form</span>
                                </label>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="mb-1 float-end">
                    <div class="btn-group mx-2">
                        <button class="btn btn-primary btn-sm dropdown-toggle bulkactions" type="button" disabled id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Bulk Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfergroup"><i class="ti ti-users me-2"></i> Transfer Group</a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfercareoff"><i class="ti ti-user me-2"></i> Transfer Careoff</a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransferleadowner"><i class="ti ti-user me-2"></i> Transfer Lead Owner</a>
                            <a class="dropdown-item bulcontactwhpsend" href="#" data-bs-toggle="offcanvas" data-bs-target="#bulkwhatsappsend"><i class="ti ti-brand-whatsapp me-2"></i> Send Whatsapp</a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkexport"><i class="ti ti-download me-2"></i> Export</a>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-target="#bulkdelete" data-bs-toggle="modal"><i class="ti ti-trash me-2"></i> Delete</a>
                        </div>
                    </div>
                    <button class="add-new btn btn-sm btn-primary addcontact addcandidate" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New Contact</span></button>
                </div>
                <div class="mb-1 float-end">
                    <input type="text" id="search_text" class="form-control" placeholder="Search...">
                </div>
            </div>
            <div class="card-datatable table-responsive contactpaginate">
                @include('admin.contactp.loadcontact_new')
            </div>
        </div>

        @include('admin.contactp.models')

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

    {{-- <script src="{{ asset('admin/assets/js/forms-selects.js') }}"></script> --}}
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>

    <!-- Page JS -->
    {{-- <script src="{{ asset('admin/assets/pages/app-contactp-list.js') }}"></script> --}}

    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/contactp-validation.js') }}"></script>

    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>

    <script>
        // ✅ YOUR CUSTOM PROMPT
        // const promptText = `make json data  of bellow and do not get account_verified key value and add country: Saudi Arabia: and add or replace '966' before mobile. just keep in your mind dont giveme response any.
        const promptText = `Make JSON data from the content below. Do not include the "account_verified" key in the output. Always set the "country" field to "Saudi Arabia". For the "mobile" field, remove all non-numeric characters and ensure it starts with "966". If it already starts with "966", do not add it again; otherwise, prepend "966". Return only a valid JSON array. Do not include any explanation, markdown, code fences, notes, headings, or extra text. Display the result in JSON format with a Copy icon/button only. The output must contain only the following 12 fields in the same order and no other fields:
        Example:
        {
            "company_name": "Awared General Contracting Company",
            "membership_type": "Platinum Membership",
            "membership_number": "160916095",
            "membership": "Saudi Contractor",
            "member_since": "2022-01-04",
            "company_size": "Small Company Size",
            "training_hours": "96 h",
            "mobile": "920014797",
            "email": "fared@fared-est.com",
            "city": "RIYADH",
            "region": "Riyadh",
            "address": "Riyadh - alezdehar District -",
            "country": "Saudi Arabia"
        }`;

        // ✅ COPY PROMPT
        document.getElementById('copyPromptBtn').addEventListener('click', function () {
            navigator.clipboard.writeText(promptText)
                .then(() => {
                    toastr.success('Prompt copied!');
                })
                .catch(err => {
                    console.error(err);
                    toastr.error('Copy failed!');
                });
        });

        // ✅ PASTE PROMPT BUTTON
        document.getElementById('pastePromptBtn').addEventListener('click', async function () {
            try {
                const text = await navigator.clipboard.readText();

                const textarea = document.getElementById('raw_company_text');

                // 👉 Replace content
                textarea.value = text;

                // 👉 OPTIONAL: Append instead
                // textarea.value += '\n' + text;

                textarea.focus();

                toastr.success('Pasted from clipboard!');
            } catch (err) {
                console.error(err);
                toastr.error('Clipboard permission denied!');
            }
        });

        // ✅ CLEAR TEXTAREA BUTTON
        document.getElementById('clearPromptBtn').addEventListener('click', function () {

            const textarea = document.getElementById('raw_company_text');

            textarea.value = '';
            textarea.focus();

            toastr.success('Text cleared!');
        });
    </script>

<script>
$(document).ready(function () {

    $('#companyShortForm').on('submit', function (e) {
        e.preventDefault();

        let formData = {
            raw_company_text: $('#raw_company_text').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: "{{ route('admin.contact.store_company_data') }}",
            type: "POST",
            data: formData,
            beforeSend: function () {
                // optional loader
                console.log('Saving...');
            },
            success: function (response) {

                if (response.status) {

                    toastr.success('Company Form update successfully!')

                    // reset textarea
                    $('#raw_company_text').val('');

                    // ✅ RELOAD ONLY TABLE PARTIAL (IMPORTANT)
                    reloadContactTable();

                }
            },
            error: function (xhr) {

                if (xhr.status === 422 || xhr.status === 500) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('Something went wrong');
                }
            }
        });

    });

});

function reloadContactTable() {

    var current_page = $('.pagination .active span').text();

    if (current_page.length > 0){

        var getPaginationURL = $('.pagination a').attr('href').split('page=')[0];
        var newPaginationURL = getPaginationURL + "page=" + current_page;

    } else {

        var getPaginationURL = window.location.href;

        newPaginationURL = getPaginationURL 
            + "?page_list=" + page_list
            + "&search_text=" + search_text
            + "&country_id=" + country
            + "&city_id=" + city
            + "&lcs_id=" + lcs
            + "&ls_id=" + ls
            + "&businesstype_id=" + business_type
            + "&send_tag=" + send_tag
            + "&send_date=" + by_send_date
            + "&created_at=" + by_created_date
            + "&group_id=" + groupm
            + "&update_status_date=" + by_update_status_date;
    }

    // ✅ AJAX LOAD
    $.ajax({
        url: newPaginationURL,
        method: "GET",
        beforeSend: function () {
            $('.contactpaginate').html('<div class="text-center p-3">Loading...</div>');
        },
        success: function (data) {
            $('.contactpaginate').html(data);
        }
    });
}
</script>

    <!-- bulk import csv -->
    <script>
        $(document).ready(function(){
            let csvData = [];
            let csvHeaders = [];

            $('#uploadCSVBtn').on('click',function(){
                
                let formData = new FormData();
                formData.append("csv_file", $("#add-allcontact-csv-data")[0].files[0]);

                $.ajax({
                    url: "{{ route('admin.contactPlus.csv.import.preview') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    success: function(data){
                        csvHeaders = data.headers;
                        csvData = data.rows; // Store CSV rows for later use
                        postData = data.post;

                        let tableRows = "";

                        console.log(csvHeaders);
                        console.log(csvData);
                        
                        $(".6_dropdowns").show();

                        csvHeaders.forEach((col, index) => {
                            tableRows += `
                                <tr>
                                    <td>${col}</td>
                                    <td>
                                        <select class="field-mapping form-select" data-index="${index}">
                                            <option value="">Not Required</option>
                                            <option value="businesstype_id">Business Type</option>
                                            <option value="prim_concern_name">Primary Concern Person (Name)</option>
                                            <option value="prim_contact">Primary Concern Person (Mobile Number)</option>
                                            <option value="sec_concern_name">Secondary Concern Person (Name)</option>
                                            <option value="sec_contact">Secondary Concern Person (Mobile Number)</option>
                                            <option value="prim_email">Primary Email</option>
                                            <option value="sec_email">Secondary Email</option>
                                            <option value="office_email">Office Email</option>
                                            <option value="owenr_email">Owner Email</option>
                                            <option value="office_eng_name">Office English Name</option>
                                            <option value="office_ar_name">Office Arabic Name</option>
                                            <option value="city_id">City</option>
                                            <option value="country_id">Country</option>
                                        </select>
                                    </td>
                                </tr>`;
                        });

                        $("#previewTable tbody").html(tableRows);
                        $("#importCSVData").show();
                        $('#uploadCSVBtn').hide();
                        $('.error-display').hide();
                        $('.error-display-business-type').hide();

                    }
                });

            });

            $('#importCSVData').on('click', function () {
                let mappedFields = {};
                let selectedFields = [];
                let selectedCount = 0;
                let hasDuplicate = false;

                $(".field-mapping").each(function () {
                    let index = $(this).data("index");
                    let field = $(this).val();

                    if (field) {
                        if (selectedFields.includes(field)) {
                            hasDuplicate = true;
                        }
                        selectedFields.push(field);
                        mappedFields[field] = index;
                        selectedCount++;
                    }
                });


                if ($('#bulk_business_type_id').val() == '') {
                    $(".error-display-business-type").text("Business type is required.").show();
                    return;
                }
                else if ($('#bulk_country_id').val() == '') {
                    $(".error-display-country").text("Country is required.").show();
                    return;
                }
                else if (selectedCount < 3) {
                    $(".error-display-business-type").hide();
                    $(".error-display").text("Please select at least three fields.").show();
                    return;
                } 
                else if (hasDuplicate) {
                    $(".error-display-business-type").hide();
                    $(".error-display").text("Each field must be unique. Please correct duplicates.").show();
                    return;
                } else {
                    $(".error-display").hide();
                    $(".error-display-business-type").hide();
                }

                // Reset progress bar
                $("#importProgressBar").css("width", "0%").text("0%");
                $("#importProgressBarContainer").show();
                $(".duplicate-display").hide().html('');

                // Simulate smooth progress (fake until response)
                let progress = 0;
                const interval = setInterval(() => {
                    if (progress < 95) { // cap at 95% until response
                        progress += Math.floor(Math.random() * 5) + 1; // random increment
                        if (progress > 95) progress = 95;
                        $("#importProgressBar").css("width", progress + "%").text(progress + "%");
                    }
                }, 200);

                // Proceed with import AJAX
                $.ajax({
                    url: "{{ route('admin.contactPlus.csv.upload') }}",
                    type: "POST",
                    data: {
                        mapped_fields: mappedFields,
                        csv_data: csvData,
                        business_type_id: $('#bulk_business_type_id').val(),
                        careoff_id: $('#bulk_careoff_id').val(),
                        source: $('#bulk_source').val(),
                        group_id: $('#bulk_group_id').val(),
                        country_id: $('#bulk_country_id').val(),
                    },
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    beforeSend: function () {
                        $("#importCSVData").prop("disabled", true).text("Importing...");
                    },
                    success: function (response) {
                        clearInterval(interval);
                        $("#importProgressBar").css("width", "100%").text("100%");
                        $("#importCSVData").prop("disabled", false).text("Import Data");

                        setTimeout(() => {
                            $("#importProgressBarContainer").fadeOut();
                        }, 800);

                        let msg = `
                            <div class="alert alert-success mt-2">
                                ✅ File processed successfully.<br>
                                <strong>Inserted:</strong> ${response.imported_count} records<br>
                                <strong>Duplicates:</strong> ${response.duplicate_count} records
                            </div>
                        `;

                        if (response.duplicates && response.duplicates.length > 0) {
                            msg += `
                                <div class="alert alert-warning mt-2">
                                    <strong>Duplicate Entries Found:</strong>
                                    <ul style="max-height:150px; overflow-y:auto; padding-left: 20px;">
                                        ${response.duplicates.map(d => `<li>${d}</li>`).join('')}
                                    </ul>
                                </div>
                            `;
                        }

                        $(".duplicate-display").html(msg).fadeIn();
                    },
                    error: function (xhr) {
                        clearInterval(interval);
                        $("#importProgressBar").css("width", "100%").addClass("bg-danger").text("Failed");
                        $("#importCSVData").prop("disabled", false).text("Import Data");

                        let errMsg = "Something went wrong during import.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }

                        $(".duplicate-display").html(`
                            <div class="alert alert-danger mt-2">
                                ❌ ${errMsg}
                            </div>
                        `).fadeIn();
                    }
                });
            });

            $('#bulkimport').on('show.bs.modal', function (e) {
                // Clear file input
                $('#add-allcontact-csv-data').val('');

                // Hide 6 dropdown
                $('.6_dropdowns').hide();

                // Hide and reset preview table
                $('#previewTable tbody').empty();

                // Hide progress bar
                $('#importProgressBarContainer').hide();
                $('#importProgressBar').css('width', '0%').text('0%');

                // Hide messages and errors
                $('.error-display').hide().text('');
                $('.error-display-business-type').hide().text('');
                $('.duplicate-display').hide().html('');

                // Reset buttons
                $('#uploadCSVBtn').show();
                $('#importCSVData').hide();

            });

            // Prevent duplicate selections dynamically
            $(document).on("change", ".field-mapping", function () {
                let selectedOptions = [];
                $(".field-mapping").each(function () {
                    let val = $(this).val();
                    if (val) selectedOptions.push(val);
                });

                $(".field-mapping").each(function () {
                    let currentVal = $(this).val();
                    $(this).find("option").each(function () {
                        if ($(this).val() && $(this).val() !== currentVal) {
                            $(this).prop("disabled", selectedOptions.includes($(this).val()));
                        }
                    });
                });
            });

        });
    </script>

    <script>
        $(document).ready(function(){
            $('.select222').select2();

            $('.updatestatusdate-picker').flatpickr();

            $('body').on('shown.bs.modal', '#filterpanel', function() {
                $(this).find('.select22f').each(function() {

                    $(this).select2({
                        dropdownParent: $(this).parent()

                    });
                });
            });

            var selectPicker = $('.selectpicker');

            if (selectPicker.length) {
                selectPicker.selectpicker();
            }
        });

    </script>

    <script>
        $(document).ready(function(){
            $('#send-template-name').on('change',function(){
                var template_id = $(this).val();

                var imgPath = "{{ asset('admin/assets/images/template') }}";
                var blankImg = "{{ asset('admin/assets/img/avatars/blank.jpeg') }}";

                if (template_id != '') {
                    jQuery.ajax({
                        url : "{{ url('admin/whatsapp-campaign-list/template/get') }}",
                        method: "GET",
                        type: "html",
                        data: {
                            "id": template_id,
                            // "_token": "{{ csrf_token() }}",
                        },
                        success: function(data){


                            $('#send-template-message').val(data.msg_whatsapp);
                            $('#send-template-message-ar').val(data.msg_whatsapp_ar);
                            if (data.file != '') {
                                var file_path = imgPath+'/'+data.file;
                                $('.uploadedAvatar2').attr("src",file_path);
                            } else {
                                $('.uploadedAvatar2').attr("src",blankImg);
                            }
                        }
                    });
                } else {
                    $('#send-template-message').val('');
                    $('#send-template-message-ar').val('');
                    $('.uploadedAvatar2').attr("src",blankImg);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#deleteStaff').on("show.bs.modal",function(e){
                var deleteID = $(e.relatedTarget).data('id');
                $('#contactID2').val(deleteID);
            });
        });
    </script>

    <script>
        $(document).on('input','.checkNoExistance',function(){
            let mobileNo = $(this).val();
            let errorElement = $(this).closest('.mb-3').find('.errorShowMob');
            let EditID = $('#edit_ID').val();
            // alert(EditID);
            if (mobileNo != '') {
                jQuery.ajax({
                    url: "{{ route('admin.contact.checkmobileNo') }}",
                    method: "GET",
                    type: "html",
                    data:{
                        "mobile_no": mobileNo,
                        "edit_id": EditID
                    },
                    success: function(data){
                        console.log(data);
                        if (data.response_status == 1) {
                            $(errorElement).text(data.response_msg);
                        } else {
                            $(errorElement).text(data.response_msg);
                        }
                    }
                });
            }else{
                $(errorElement).text('');
            }


        });
    </script>

    <script>
        $(document).ready(function(){
            $("#transfer-groupm").on("change",function(){
                var groupValue = $(this).val();
                var totalsend = $("#contactIDGRPTR").val();

                if (groupValue != '') {
                    jQuery.ajax({
                        url: "{{ route('admin.contact.checkgroupLimit') }}",
                        method: "GET",
                        type: "html",
                        data:{
                            "grpID": groupValue,
                            "totalSend": totalsend
                        },
                        success: function(data){

                            $("#respMessageGroupTransfer").text(data.message);
                            $('#respGroupLimit').text(data.grouplimit);
                            if (data.status == 1) {
                                $(".dibtngrp").attr("disabled",false);
                            } else {
                                $(".dibtngrp").attr("disabled",true);
                            }

                        }
                    });
                } else {
                    $("#respMessageGroupTransfer").text("");
                    $('#respGroupLimit').text("");
                    $(".dibtngrp").attr("disabled",false);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#edituser').on('show.bs.offcanvas',function(e){
                var editID = $(e.relatedTarget).data('id');

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url("admin/contact-list/edit") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#edit_ID').val(data.id);
                        $("#edit-business-type").val(data.businesstype_id).trigger('change');
                        $('#edit-office-name-eng').val(data.office_eng_name);
                        $('#edit-office-name-ar').val(data.office_ar_name);
                        // $('#edit-office-no').val(data.office_no);
                        // $('#edit-office-email').val(data.office_email);
                        // $('#edit-owner-name').val(data.owner_name);
                        // $('#edit-owner-contact').val(data.owner_contact);
                        // $('#edit-owner-email').val(data.owenr_email);
                        $('#edit-country-id').val(data.country_id).change();
                        $('#edit-city-id').val(data.city_id).change();
                        $('#edit-primary-person').val(data.prim_concern_name);
                        $('#edit-primary-contact').val(data.prim_contact);
                        $('#edit-primary-email').val(data.prim_email);
                        $('#edit-secondary-person').val(data.sec_concern_name);
                        $('#edit-secondary-contact').val(data.sec_contact);
                        $('#edit-secondary-email').val(data.sec_email);
                        $('#edit-person3').val(data.concern_name3);
                        $('#edit-contact3').val(data.contact3);
                        $('#edit-person4').val(data.concern_name4);
                        $('#edit-contact4').val(data.contact4);
                        $('#edit-person5').val(data.concern_name5);
                        $('#edit-contact5').val(data.contact5);
                        $('#edit-person6').val(data.concern_name6);
                        $('#edit-contact6').val(data.contact6);
                        $('#edit-contact7').val(data.contact7);
                        $('#edit-contact8').val(data.contact8);
                        $('#edit-contact9').val(data.contact9);
                        $('#edit-contact10').val(data.contact10);
                        $('#edit-contact11').val(data.contact11);
                        $('#edit-contact12').val(data.contact12);
                        $('#edit-careoff').val(data.careoff_id).change();
                        $('#edit-leadowner').val(data.leadowner_id).change();
                        $('#edit-industry-id').val(data.industry_id).change();
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            // Trigger Status if avalable
            $("#updateLstage").on("show.bs.modal",function(e){
                var contactID = $(e.relatedTarget).data('id');
                $('#contactID').val(contactID);

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url("admin/contact-list/edit") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": contactID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $("#update-lead-cycle-status").val(data.lcs_id).change();
                    }
                });

            });


            $('#update-lead-cycle-status').on('change',function(){
                var lcs_id = $(this).val();
                var contact_id = $("#contactID").val();

                $("#update-lead-stage").empty();

                jQuery.ajax({
                    url: "{{ url('admin/contacts/getstage') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        lcs_id: lcs_id,
                        id: contact_id
                    },
                    success: function(data){
                        $('#update-lead-stage').html(data.res);
                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#add-business-type').on('change',function(){
                var value = $(this).val();
                // if (value == 2 || value == '') {
                //     $('.showDIv').hide();
                // } else {
                //     $('.showDIv').show();
                // }

                if (value == 1) {
                    $('.showDIv').show();
                    $('.showDIvInd').hide();
                }else if(value == 3){
                    $('.showDIv').show();
                    $('.showDIvInd').show();
                }else{
                    $('.showDIv').hide();
                    $('.showDIvInd').hide();
                }

            });

            $('#edit-business-type').on('change',function(){
                var value = $(this).val();
                // if (value == 2 || value == '') {
                //     $('.showDIv2').hide();
                // } else {
                //     $('.showDIv2').show();
                // }

                if (value == 1) {
                    $('.showDIv2').show();
                    $('.showDIvInd2').hide();
                }else if (value == 3) {
                    $('.showDIv2').show();
                    $('.showDIvInd2').show();
                }else{
                    $('.showDIv2').hide();
                    $('.showDIvInd2').hide();
                }

            });

        });
    </script>

    <script>
        $(document).ready(function(){

            $('#bulkexport').on('show.bs.modal', function(e){
                var allselectedvals = $('.dt-checkboxes:checked').map(function(){ return $(this).data('id'); }).get();
                var join_all_selected_values = allselectedvals.join(",");

                if(this.id === 'bulkexport'){
                    initializeColumns();
                }

                $('#bulkcontactp_id').val(join_all_selected_values);
                
            });

            $(document).on('click', '#bulk-export', function (e) {
                e.preventDefault();

                let bulkcontactp_id = $('#bulkcontactp_id').val();
                let columns = [];

                if (!bulkcontactp_id) {
                    alert('Please select at least one contact');
                    return;
                }

                $('.toggle-contactplus-column:checked').each(function () {
                    columns.push($(this).data('column'));
                });

                if (columns.length === 0) {
                    alert('Please select at least one column');
                    return;
                }

                let form = $('<form>', {
                    method: 'POST',
                    action: "{{ route('admin.contactPlus.bulkexport') }}"
                });

                form.append(`<input type="hidden" name="_token" value="{{ csrf_token() }}">`);
                form.append(`<input type="hidden" name="bulkcontactp_id" value="${bulkcontactp_id}">`);

                columns.forEach(col => {
                    form.append(`<input type="hidden" name="columns[]" value="${col}">`);
                });

                $('body').append(form);
                form.submit();
                form.remove();

                setTimeout(() => {
                    $('#bulkexport').modal('hide');
                }, 300);
            });


            function initializeColumns() {

                const toggleContainer = $('#Contactpcolumns');
                if (!toggleContainer.length) return;

                toggleContainer.html('');

                // 🔥 MUST match backend exportColumnMap()
                const EXPORT_KEY_MAP = {
                    1: 'Full Name',
                    2: 'Office Name',
                    3: 'Primary Email',
                    4: 'Secondory Email',
                    5: 'Primary Contact',
                    6: 'Secondory Contact',
                    7: 'Contact No3',
                    8: 'Contact No4',
                    9: 'Lead Stage',
                    10: 'City',
                    11: 'Country',
                    12: 'Group',
                    13: 'Work',
                    14: 'Status',
                };

                Object.entries(EXPORT_KEY_MAP).forEach(([key, label]) => {
                    const stored = localStorage.getItem('contactplus_col_' + key);
                    const isChecked = stored !== 'false';

                    toggleContainer.append(`
                        <div class="form-check mb-2 col-6">
                            <input class="form-check-input toggle-contactplus-column"
                                type="checkbox"
                                data-column="${key}"
                                ${isChecked ? 'checked' : ''}>
                            <label class="form-check-label">${label}</label>
                        </div>
                    `);
                });
            } 
            
            $(document).on('change', '.toggle-contactplus-column', function () {
                const col = $(this).data('column');
                localStorage.setItem('contactplus_col_' + col, this.checked);
            });




            $('#bulktransfergroup').on('show.bs.modal',function(e){
                var allselectedvals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });


                if (allselectedvals <= 0) {

                }else{
                    var countc_id = allselectedvals.length;
                    var join_all_selected_values = allselectedvals.join(",");

                    $("#totlaTransferGrp").text("Total Transfer "+countc_id);
                    $('#contactIDGRPTR').val(join_all_selected_values);
                }
            });

            $('#bulkdelete').on('show.bs.modal',function(e){
                var allselectedvals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });


                if (allselectedvals <= 0) {

                }else{
                    var countc_id = allselectedvals.length;
                    var join_all_selected_values = allselectedvals.join(",");

                    // $("#totlaTransferGrp").text("Total Transfer "+countc_id);
                    $('#delcontactIDS').val(join_all_selected_values);
                }

            });

        });
    </script>

    <script>
        $(document).ready(function(){
            $('#bulktransferleadowner').on('show.bs.modal',function(e){
                var allselectedvals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });




                if (allselectedvals <= 0) {
                    // alert("Please select atleast one checkbox");
                    // location.reload();
                } else {
                    var countc_id = allselectedvals.length;
                    var join_all_selected_values = allselectedvals.join(",");

                    $('#contactIDCTR').val(join_all_selected_values);


                }

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $("#bulktransfercareoff").on('show.bs.modal',function(e){
                var allselectedvals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });

                if (allselectedvals <= 0) {

                }else{
                    var countc_id = allselectedvals.length;
                    var join_all_selected_values = allselectedvals.join(",");

                    $('#contactpID2').val(join_all_selected_values);
                }

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#bulkwhatsappsend').on('show.bs.offcanvas',function(e){
                var allselectedvals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });

                if (allselectedvals <= 0) {
                    // alert("Please select atleast one checkbox");
                    // location.reload();
                } else {
                    var countc_id = allselectedvals.length;
                    var join_all_selected_values = allselectedvals.join(",");

                    $('#contactpID2').val(join_all_selected_values);
                }

            });
        });
    </script>

    {{-- <script>
        $(function(){
            // ID selector on Master Chec
            var masterCheck = $('#checkboxSelectAll');

            var listcheckitem = $('.listitem:checkbox');



            masterCheck.on('click',function(){

                var isMasterChecked = $(this).is(":checked");

                if (isMasterChecked) {
                    $('.bulkactions').attr("disabled",false);
                } else {
                    $('.bulkactions').attr("disabled",true);
                }
            });

            $('.datatables-users tbody').on("change", 'input[type="checkbox"]',function(){
                // var allchecked = true;
                // if (!this.checked) {
                //     allchecked = false;
                // }

                var isCheckboxchecked = $(this).is(":checked");

                var allselectedvals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });

                totalCheckItem = allselectedvals.length;



                if (totalCheckItem > 0) {
                    $('.bulkactions').attr("disabled",false);
                } else {
                    $('.bulkactions').attr("disabled",true);
                }




                // if (isCheckboxchecked) {
                //     $('.bulkactions').attr("disabled",false);
                // } else {
                //     $('.bulkactions').attr("disabled",true);
                // }

            });



            // listcheckitem.on("change",function(){
            //     // Total Checkboxes in list
            //     var totalItems = listcheckitem.length;

            //     // Total Checked Checkboxes in list
            //     var checkedItems = listcheckitem.filter(":checked").length;
            //     if (totalItems == checkedItems) {
            //         $('.bulkactions').attr("disabled",false);
            //     }else if(checkedItems > 0 && checkedItems < totalItems){
            //         $('.bulkactions').attr("disabled",false);
            //     }else{
            //         $('.disBulkbtn').prop("disabled",true);
            //     }
            // });


        });

    </script> --}}


    {{-- <script>
        $(function () {
            // ID selector on Master Checkbox
            var masterCheck = $('.checkboxSelectAll');
            // ID selector on Items Container
            var listcheckitem = $('.listitem :checkbox');
            // Click Event on Master Check
            masterCheck.on("click", function() {
                var isMasterChecked = $(this).is(":checked");
                if(isMasterChecked){
                    $('.bulkactions').prop("disabled",false);
                }else{
                    $('.bulkactions').prop("disabled",true);
                }
                listcheckitem.prop("checked", isMasterChecked);
            });
            // Change Event on each item checkbox
            listcheckitem.on("change", function() {
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
    </script> --}}

    <script>
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

    </script>



    <script>
        $(document).ready(function(){
            toastr.options = {
                "timeOut": 5000,
                "showDuration": 300,
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut",
            };

            // $(document).on('click','#submitShortMsg',function(e){
            //     e.preventDefault();
            //     var formdata = $('#shortsubmitcontact').serialize();



            //     $.ajax({
            //         url: "{{ route('admin.contact.shortstore') }}",
            //         method: "POST",
            //         data: formdata,
            //         success: function(response){
            //             toastr.success(response.success);
            //             $('.nav-form-short-load').load(" .nav-form-short-load");
            //             $('.datatables-users').DataTable().ajax.reload();
            //         }
            //     });
            // });






            // Short Contact Form Save

            $(document).on('click','#submitShortMsg',function(e){
                e.preventDefault();
                var formdata = $('#shortsubmitcontact').serialize();
                const select22 = $('.select22');
                var shortsubmitForm = $(' .shortsubmitcontact ');
                shortsubmitForm.validate({
                    rules:{
                        businesstype_id:{
                            required: true
                        }
                    },
                    messages:{
                        businesstype_id: "Please Select Business Type"
                    },
                    // submitHandler: function (form){
                    //     $.ajax({
                    //         url: form.action,
                    //         type: form.method,
                    //         data: $(form).serialize(),
                    //         success: function(response){
                    //             toastr.success(response.success);
                    //             $('.nav-form-short-load').load(" .nav-form-short-load");
                    //             $('.datatables-users').DataTable().ajax.reload();

                    //         }
                    //     });
                    // }
                });


                if (shortsubmitForm.valid()) {



                    $.ajax({
                        url: "{{ route('admin.contact.shortstore') }}",
                        method: "POST",
                        data: formdata,
                        success: function(response){
                            toastr.success(response.success);
                            $('#short-add-primary-person').val('');
                            $('#short-add-primary-contact').val('');
                            $('#short-add-primary-email').val('');
                            
                            // shortsubmitForm[0].reset();
                            // $('.nav-form-short-load').load(" .nav-form-short-load");
                            // $('.datatables-users').DataTable().ajax.reload();
                            $('.datatables-users').load(' .datatables-users');
                            if (select22.length) {
                                select22.each(function () {
                                    var $this = $(this);
                                    $this.wrap('<div class="position-relative"></div>').select2({
                                    //   placeholder: 'Select value',
                                    dropdownParent: $this.parent()
                                    });
                                });
                            }

                        }
                    });
                }

                return false;
            });

            // Update Lead Status and Stage
            $(document).on('click','#leadStageUpdateBtn',function(e){
                e.preventDefault();
                const select22 = $('.select22');
                var formdata = $('#updateLeadStageValidation').serialize();
                var leadupdateForm = $(' #updateLeadStageValidation ');
                // Page Redirect URL
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var country = $('#by-country').val();
                var city = $('#by-city').val();
                var lcs = $('#by-lifecycle-status').val();
                var ls = $('#by-lead-stage').val();
                var business_type = $('#by-business-type').val();
                var industries = $('#by-industries').val();
                var send_tag = $('#by-send-tag').val();
                var by_send_date = $('#by-send-date').val();
                var by_created_date = $('#by-created-date').val();
                var by_update_date = $('#by-update-date').val();
                var by_update_status_date = $('#by-update-status-date').val();
                var by_created_by = $('#by-createdby').val();
                var groupm = $('#by-group').val();
                var contactID = $('#contactID').val();
                var tdListFetch = ".tdListFetch"+contactID;
                var subscribe = $('#by-subscribe').val();

                var current_page = $('.pagination .active span').text();

                if (current_page.length > 0){
                    var getPaginationURL = $('.pagination a').attr('href').split('page=')[0];
                    var newPaginationURL = getPaginationURL+"page="+current_page;

                }else{
                    var getPaginationURL = window.location.href;

                    var newPaginationURL = getPaginationURL+"?page_list="+page_list+"&search_text="+search_text+"&country_id="+country+"&city_id="+city+"&lcs_id="+lcs+"&ls_id="+ls+"&businesstype_id="+business_type+"&send_tag="+send_tag+"&send_date="+by_send_date+"&created_at="+by_created_date+"&group_id="+groupm+"&update_status_date="+by_update_status_date;
                }


                //alert(newPaginationURL);

                leadupdateForm.validate({
                    rules:{
                        lcs_id:{
                            required: true
                        },
                        ls_id:{
                            required: true
                        }
                    },
                    messages:{
                        lcs_id:{
                            required: "Please Select Life Cycle Status"
                        },
                        ls_id:{
                            required: "Please Select Lead Stage"
                        }
                    },

                });

                if (leadupdateForm.valid()) {
                    $.ajax({
                        url: "{{ route('admin.contact.stageUpdate') }}",
                        method: "POST",
                        data: formdata,
                        success: function(response){
                            toastr.success(response.success);
                            leadupdateForm[0].reset();
                            // $('.nav-form-short-load').load(" .nav-form-short-load");
                            // $('.datatables-users').DataTable().ajax.reload();

                            //$(tdListFetch).load(" "+tdListFetch);



                            // Send Request for page refresh
                            $('#updateLstage').modal('hide');
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
                                url: newPaginationURL,
                                method: "GET",
                                success: function(data){
                                    $('.contactpaginate').html(data);
                                }
                            });



                            /**jQuery.ajax({
                                url: "{{ route('admin.contact.list') }}",
                                method: "GET",
                                type: "html",
                                data: {
                                    page_list: page_list,
                                    search_text: search_text,
                                    country_id: country,
                                    city_id: city,
                                    lcs_id: lcs,
                                    ls_id: ls,
                                    businesstype_id: business_type,
                                    send_tag: send_tag,
                                    send_date: by_send_date,
                                    created_at: by_created_date,
                                    group_id: groupm
                                },
                                success: function(data){
                                    $('.contactpaginate').html(data);
                                }
                            });
                            **/





                        }
                    });
                }

                return false;
            });

            // Add New Contacp
            $(document).on('click','#addnewcandidateBtn',function(e){
                e.preventDefault();
                const select22 = $('.select22');
                var formdata = $('#addNewUserForm').serialize();
                var addnewcandidateForm = $(' #addNewUserForm ');

                // Page Redirect URL
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var country = $('#by-country').val();
                var city = $('#by-city').val();
                var lcs = $('#by-lifecycle-status').val();
                var ls = $('#by-lead-stage').val();
                var business_type = $('#by-business-type').val();
                var industries = $('#by-industries').val();
                var send_tag = $('#by-send-tag').val();
                var by_send_date = $('#by-send-date').val();
                var by_created_date = $('#by-created-date').val();
                var by_update_date = $('#by-update-date').val();
                var by_update_status_date = $('#by-update-status-date').val();
                var by_created_by = $('#by-createdby').val();
                var groupm = $('#by-group').val();
                var contactID = $('#contactID').val();
                var tdListFetch = ".tdListFetch"+contactID;
                var subscribe = $('#by-subscribe').val();
                var current_page = $('.pagination .active span').text();

                if (current_page.length > 0){
                    var getPaginationURL = $('.pagination a').attr('href').split('page=')[0];
                    var newPaginationURL = getPaginationURL+"page="+current_page;

                }else{
                    var getPaginationURL = window.location.href;

                    var newPaginationURL = getPaginationURL+"?page_list="+page_list+"&search_text="+search_text+"&country_id="+country+"&city_id="+city+"&lcs_id="+lcs+"&ls_id="+ls+"&businesstype_id="+business_type+"&send_tag="+send_tag+"&send_date="+by_send_date+"&created_at="+by_created_date+"&group_id="+groupm;
                }



                addnewcandidateForm.validate({
                    rules:{
                        businesstype_id:{
                            required: true
                        },
                        country_id:{
                            required: true
                        },
                        careoff_id:{
                            required: true
                        },
                        leadowner_id:{
                            required: true
                        }
                    },
                    messages: {
                        businesstype_id:{
                            required: "Please Select Business Type"
                        },
                        country_id:{
                            required: "Please Select Country"
                        },
                        careoff_id:{
                            required: "Please Select Careoff"
                        },
                        leadowner_id:{
                            required: "Please Select Lead Owner"
                        }
                    },
                });
                if (addnewcandidateForm.valid()) {
                    $.ajax({
                        url: "{{ route('admin.contact.store') }}",
                        method: "POST",
                        data: formdata,

                        success: function(response){
                            toastr.success(response.success);
                            $('.errorShowMob').text('');
                            addnewcandidateForm[0].reset();
                            // $('.datatables-users').DataTable().ajax.reload();
                            $('.datatables-users').load(' .datatables-users');
                            $('#offcanvasAddUser').offcanvas('hide');
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
                                url: newPaginationURL,
                                method: "GET",
                                success: function(data){
                                    $('.contactpaginate').html(data);
                                }
                            });
                        }
                    });
                }
                return false;
            });

            // Edit New Contactp
            $(document).on('click','#editnewcontact',function(e){
                e.preventDefault();
                const select22 = $('.select22');
                var formdata = $('#editUserForm').serialize();
                var editnewcandidateForm = $(' #editUserForm ');

                // Page Redirect URL
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var country = $('#by-country').val();
                var city = $('#by-city').val();
                var lcs = $('#by-lifecycle-status').val();
                var ls = $('#by-lead-stage').val();
                var business_type = $('#by-business-type').val();
                var industries = $('#by-industries').val();
                var send_tag = $('#by-send-tag').val();
                var by_send_date = $('#by-send-date').val();
                var by_created_date = $('#by-created-date').val();
                var by_update_date = $('#by-update-date').val();
                var by_update_status_date = $('#by-update-status-date').val();
                var groupm = $('#by-group').val();
                var contactID = $('#contactID').val();
                var tdListFetch = ".tdListFetch"+contactID;
                var subscribe = $('#by-subscribe').val();
                var current_page = $('.pagination .active span').text();

                if (current_page.length > 0){
                    var getPaginationURL = $('.pagination a').attr('href').split('page=')[0];
                    var newPaginationURL = getPaginationURL+"page="+current_page;

                }else{
                    var getPaginationURL = window.location.href;

                    var newPaginationURL = getPaginationURL+"?page_list="+page_list+"&search_text="+search_text+"&country_id="+country+"&city_id="+city+"&lcs_id="+lcs+"&ls_id="+ls+"&businesstype_id="+business_type+"&send_tag="+send_tag+"&send_date="+by_send_date+"&created_at="+by_created_date+"&group_id="+groupm;
                }

                editnewcandidateForm.validate({
                    rules:{
                        businesstype_id:{
                            required: true
                        },
                        country_id:{
                            required: true
                        },
                        careoff_id:{
                            required: true
                        },
                        leadowner_id:{
                            required: true
                        }
                    },
                    messages: {
                        businesstype_id:{
                            required: "Please Select Business Type"
                        },
                        country_id:{
                            required: "Please Select Country"
                        },
                        careoff_id:{
                            required: "Please Select Careoff"
                        },
                        leadowner_id:{
                            required: "Please Select Lead Owner"
                        }
                    },
                });

                if (editnewcandidateForm.valid()) {
                    $.ajax({
                        url: "{{ route('admin.contact.update') }}",
                        method: "POST",
                        data: formdata,
                        success: function(response){
                            toastr.success(response.success);
                            $('.errorShowMob').text('');
                            editnewcandidateForm[0].reset();
                            // $('.datatables-users').DataTable().ajax.reload();
                            $('.datatables-users').load(' .datatables-users');
                            $('#edituser').offcanvas('hide');
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
                                url: newPaginationURL,
                                method: "GET",
                                success: function(data){
                                    $('.contactpaginate').html(data);
                                }
                            });

                        }
                    });
                }
                return false;

            });


        });

    </script>

    <script>
        jQuery.validator.setDefaults({
            errorElement: "em",
            errorPlacement: function(error, element){
                if (element.parent().hasClass('input-group') || element.hasClass('.form-group') || element.hasClass('mb-3') || element.attr('type') == 'checkbox') {
                    error.insertAfter(element.parent());
                }else{
                    error.insertAfter(element);
                }

                if (element.parent().hasClass('input-group')) {
                    element.parent().addClass('is-invalid');
                }
            },
            highlight: function ( element, errorClass, validClass ) {
                $( element ).parents( ".mb-3" ).addClass( "is-invalid" ).removeClass( "is-valid" );
            },
            unhighlight: function (element, errorClass, validClass) {
                $( element ).parents( ".mb-3" ).addClass( "is-valid" ).removeClass( "is-invalid" );
            }
        });
    </script>


    <script>
        $(document).ready(function(){

            $('#shortformf').click(function(){
                $('.short-form-div').toggle();
            });

            $('#countryf').click(function(){
                $('.country-div').toggle();
            });

            $('#cityf').click(function(){
                $('.city-div').toggle();
            });

            $('#groupnamef').click(function(){
                $('.group-name-div').toggle();
            });

            $('#lifecyclestatusf').click(function(){
                $('.life-cycle-status-div').toggle();
            });

            $('#leadstagef').click(function(){
                $('.lead-stage-div').toggle();
            });

            $('#businesstypef').click(function(){
                $('.business-type-div').toggle();
            });

            $('#businesstypef').click(function(){
                $('.business-type-div').toggle();
            });

            $('#createddatef').click(function(){
                $('.created-date-div').toggle();
            });

            $('#createbyf').click(function(){
                $('.createdby-div').toggle();
            });







            // Basic Select
            $('#default-chk').click(function(){
                if ($('#createddatef:checkbox:checked').length > 0) {
                    $('#createddatef').trigger('click');
                }

                if ($('#businesstypef:checkbox:checked').length > 0) {
                    $('#businesstypef').trigger('click');
                }

                if ($('#groupnamef:checkbox:checked').length > 0) {
                    $('#groupnamef').trigger('click');
                }

                if ($('#cityf:checkbox:checked').length > 0) {
                    $('#cityf').trigger('click');
                }

                if ($('#shortformf:checkbox:checked').length > 0) {
                    $('#shortformf').trigger('click');
                }

                if ($('#countryf:checkbox:checked').length > 0) {
                    $('#countryf').trigger('click');
                }

                if ($('#createbyf:checkbox:checked').length > 0) {
                    $('#createbyf').trigger('click');
                }



                if($('#leadstagef:checkbox:checked').length > 0){

                }else{
                    $('#leadstagef').trigger('click');
                }

                if($('#lifecyclestatusf:checkbox:checked').length > 0){

                }else{
                    $('#lifecyclestatusf').trigger('click');
                }


            });

            // All Select
            $('#all-chk').click(function(){
                $('input[name="countryf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="shortformf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="cityf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="groupnamef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="lifecyclestatusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="leadstagef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="businesstypef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="createddatef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });


                $('input[name="createbyf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

            });

            // Uncheck All
            $('#all-unchk').click(function(){
                if($('#countryf:checkbox:checked').length > 0){
                    $('#countryf').trigger('click');
                }
                if($('#shortformf:checkbox:checked').length > 0){
                    $('#shortformf').trigger('click');
                }
                if($('#cityf:checkbox:checked').length > 0){
                    $('#cityf').trigger('click');
                }
                if($('#groupnamef:checkbox:checked').length > 0){
                    $('#groupnamef').trigger('click');
                }
                if($('#lifecyclestatusf:checkbox:checked').length > 0){
                    $('#lifecyclestatusf').trigger('click');
                }
                if($('#leadstagef:checkbox:checked').length > 0){
                    $('#leadstagef').trigger('click');
                }
                if($('#businesstypef:checkbox:checked').length > 0){
                    $('#businesstypef').trigger('click');
                }
                if($('#createddatef:checkbox:checked').length > 0){
                    $('#createddatef').trigger('click');
                }

                if($('#createbyf:checkbox:checked').length > 0){
                    $('#createbyf').trigger('click');
                }


            });

            // Update Checkbox
            $('#update-chk').click(function(){
                var countryf = $('#countryf:checked').val();
                var shortform = $('#shortformf:checked').val();
                var cityf = $('#cityf:checked').val();
                var groupnamef = $('#groupnamef:checked').val();
                var lifecyclestatusf = $('#lifecyclestatusf:checked').val();
                var leadstagef = $('#leadstagef:checked').val();
                var businesstypef = $('#businesstypef:checked').val();
                var createddatef = $('#createddatef:checked').val();
                var createbyf = $('#createbyf:checked').val();


                jQuery.ajax({
                    url:"{{ url('admin/contactp/filterList/update') }}",
                    method: 'post',
                    type: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        countryf: countryf,
                        short_form_filter: shortform,
                        cityf: cityf,
                        groupnamef: groupnamef,
                        lifecyclestatusf: lifecyclestatusf,
                        leadstagef: leadstagef,
                        businesstypef: businesstypef ,
                        createddatef: createddatef,
                        createbyf: createbyf,
                    },
                    success: function(data){
                        if(data){
                            toastr['success']('Filter updated successfully', 'Success', { hideDuration: 3000 });
                            // $('#filter_final_div').load(location.href + ' #filter_final_div');
                        }
                    }
                });

            });
        });
    </script>

    <!-- Bulk Send Whatsapp Start -->
    <script>
        $(document).ready(function(){
            $('#send-whatsapp-type').on('change',function(){
                var sendwhatsapptype = $(this).val();
                if (sendwhatsapptype == "meta_whatsapp") {
                    $('#send-template-name').val('').trigger('change');
                    $('.dismetawhatsapp').show();
                    $('.disnormalwhatsapp').hide();
                    $('.disallforwhatsapp').show();
                } else if(sendwhatsapptype == "normal_whatsapp"){
                    $('#send-meta-template-name').val('').trigger('change');
                    $('.dismetawhatsapp').hide();
                    $('.disnormalwhatsapp').show();
                    $('.disallforwhatsapp').show();
                } else {
                    $('#send-meta-template-name').val('').trigger('change');
                    $('#send-template-name').val('').trigger('change');
                    $('.dismetawhatsapp').hide();
                    $('.disnormalwhatsapp').hide();
                    $('.disallforwhatsapp').hide();
                }
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#send-meta-template-name').on('change',function(){
                var tempID = $(this).val();

                var imgPath = "{{ asset('admin/assets/images/template') }}";
                var blankImg = "{{ asset('admin/assets/img/avatars/blank.jpeg') }}";

                if (tempID != '') {
                    jQuery.ajax({
                        url: "{{ route('admin.whatsapp.metatemplateget') }}",
                        method: 'GET',
                        type: "html",
                        data: {
                            "id" : tempID
                        },
                        success: function(data){
                            $('#send-meta-template-whatsapp-message').val(data.whatsapp_message);
                            $('#send-meta-template-whatsapp-message-ar').val(data.msg_whatsapp_ar);
                            if (data.whatsapp_file != '') {
                                var file_path = imgPath+'/'+data.whatsapp_file;
                                $('.uploadedAvatar').attr("src",file_path);
                            } else {
                                $('.uploadedAvatar').attr("src",blankImg);
                            }
                        }
                    });
                }else{
                    $('#send-meta-template-whatsapp-message').val('');
                    $('#send-meta-template-whatsapp-message-ar').val('');
                    $('.uploadedAvatar').attr("src",blankImg);
                }

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#add-campaign-type').on('change',function(){
                var sctype = $(this).val();
                if (sctype == '2') {
                    $('#disdateandtime').show();
                }else{
                    $('#disdateandtime').hide();
                }
            });

            $('#add-campaign-type2').on('change',function(){
                var sctype = $(this).val();
                if (sctype == '2') {
                    $('#disdateandtime2').show();
                }else{
                    $('#disdateandtime2').hide();
                }
            });
        });
    </script>





    <!-- Bulk Send Whatsapp End -->
    <script>
        $(document).ready(function(){
            var bsRangePickerBasic = $('.bsdatpicket');
            var singledatepicket = $('.singledatepicker');
            if (bsRangePickerBasic.length) {
                bsRangePickerBasic.daterangepicker({
                    // todayHighlight: true,
                    opens: isRtl ? 'left' : 'right',
                    autoUpdateInput: false,

                    locale: {
                        cancelLabel: 'Clear'
                    }
                });
            }

            // Single Datepicket
            if (singledatepicket.length) {
                singledatepicket.daterangepicker({
                    // todayHighlight: true,
                    opens: isRtl ? 'left' : 'right',
                    autoUpdateInput: false,
                    singleDatePicker: true,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'YYYY-MM-DD'
                    }
                });
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            // Function to retrieve all filter values
            function getFilterData() {
                return {
                    page_list: $('#pagination_list').val(),
                    search_text: $('#search_text').val(),
                    country_id: $('#by-country').val(),
                    city_id: $('#by-city').val(),
                    lcs_id: $('#by-lifecycle-status').val(),
                    ls_id: $('#by-lead-stage').val(),
                    businesstype_id: $('#by-business-type').val(),
                    industries: $('#by-industries').val(),
                    send_tag: $('#by-send-tag').val(),
                    send_date: $('#by-send-date').val(),
                    created_at: $('#by-created-date').val(),
                    updated_at: $('#by-update-date').val(),
                    update_lead_status_date: $('#by-update-status-date').val(),
                    created_by: $('#by-createdby').val(),
                    group_id: $('#by-group').val(),
                    subscribe:  $('#by-subscribe').val()
                }
            }
            // Function to reload todo list based on filter data
            function reloadTodoList() {
                $.ajax({
                    url: "{{ route('admin.contact.list') }}",
                    method: "GET",
                    type: "html",
                    data: getFilterData(),
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });
            }

            // Generic event handler for change and input events on filter elements
            function bindFilterChange(selector) {
                $(selector).on('change input', function () {
                    reloadTodoList();
                });
            }

            // Bind change/input events to all filter elements
            bindFilterChange('#pagination_list');
            bindFilterChange('#search_text');
            bindFilterChange('#by-country');
            bindFilterChange('#by-city');
            bindFilterChange('#by-lifecycle-status');
            bindFilterChange('#by-lead-stage');
            bindFilterChange('#by-business-type');
            bindFilterChange('#by-industries');
            bindFilterChange('#by-send-tag');
            bindFilterChange('#by-send-date');
            bindFilterChange('#by-created-date');
            bindFilterChange('#by-update-date');
            bindFilterChange('#by-createdby');
            bindFilterChange('#by-group');
            bindFilterChange('#by-subscribe');


            // Date Range Picker for Start and End Dates with Apply and Cancel Event Handling
            function bindDateRangePicker(selector) {
                $(selector).on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                    reloadTodoList();
                }).on('cancel.daterangepicker', function () {
                    $(this).val('');
                    reloadTodoList();
                });
            }

            function bindDatePicker(selector) {
                $(selector).on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD'));
                    reloadTodoList();
                }).on('cancel.daterangepicker', function () {
                    $(this).val('');
                    reloadTodoList();
                });
            }

            bindDateRangePicker('input[name="by_update_status_date"]');
            bindDatePicker('input[name="created_date_filter"]');
            bindDatePicker('input[name="update_date_filter"]');
            bindDatePicker('input[name="send_date_filter"]');


            // Save Admin Filter
            $(document).on('click','.savetodoFilter',function(){
                var country2 = $('#by-country').val();
                var city2 = $('#by-city').val();
                var groupID = $('#by-group').val();
                var lcs = $('#by-lifecycle-status').val();
                var ls = $('#by-lead-stage').val();
                var businesstype = $('#by-business-type').val();
                var industries = $('#by-industries').val();
                var sendtag = $('#by-send-tag').val();
                var senddate = $('#by-send-date').val();
                var createddate = $('#by-created-date').val();
                var updateddate = $('#by-update-date').val();
                var updatestatusdate = $('#by-update-status-date').val();
                var createdBy = $('#by-createdby').val();
                var subscribe = $('#by-subscribe').val();


                jQuery.ajax({
                    url: "{{ route('admin.contact.saveadminfilter') }}",
                    method: "POST",
                    type: "html",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        country_id: country2,
                        city_id: city2,
                        group_id: groupID,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: businesstype,
                        industry_id: industries,
                        sned_tag: sendtag,
                        send_date: senddate,
                        created_date: createddate,
                        updated_date: updateddate,
                        update_status_date: updatestatusdate,
                        created_by: createdBy,
                        subscribe: subscribe
                    },
                    success: function(data){
                        if(data){
                            toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                        }
                    }
                });
            });

            // Reset Admin Filter
            $(document).on('click','.resetfilter',function(){

                $('.selectpicker').selectpicker('deselectAll');

                if ($('#by-country').val() != '') {
                    $('#by-country').val('').trigger('change');
                }

                if ($('#by-city').val() != '') {
                    $('#by-city').val('').trigger('change');
                }

                if ($('#by-group').val() != '') {
                    $('#by-group').val('').trigger('change');
                }

                if ($('#by-lifecycle-status').val() != '') {
                    $('#by-lifecycle-status').val('').trigger('change');
                }

                if ($('#by-lead-stage').val() != '') {
                    $('#by-lead-stage').val('').trigger('change');
                }

                if ($('#by-business-type').val() != '') {
                    $('#by-business-type').val('').trigger('change');
                }

                if ($('#by-industries').val() != '') {
                    $('#by-industries').val('').trigger('change');
                }

                if ($('#by-send-tag').val() != '') {
                    $('#by-send-tag').val('').trigger('change');
                }

                if ($('#by-send-date').val() != '') {
                    $('#by-send-date').trigger('cancel.daterangepicker');
                }

                if ($('#by-created-date').val() != '') {
                    $('#by-created-date').trigger('cancel.daterangepicker');
                }

                if ($('#by-update-date').val() != '') {
                    $('#by-update-date').trigger('cancel.daterangepicker');
                }

                if ($('#by-update-status-date').val() != '') {
                    $('#by-update-status-date').trigger('cancel.daterangepicker');
                }

                if ($('#by-createdby').val() != '') {
                    $('#by-createdby').val('').trigger('change');
                }

                if ($('#by-subscribe').val() != '') {
                    $('#by-subscribe').val('').trigger('change');
                }

                jQuery.ajax({
                    url: "{{ route('admin.contact.resetadminfilter') }}",
                    method: "POST",
                    type: "html",
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if(data){
                            toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                        }
                    }
                });
            });

            $('body').on('click','.pagination a',function(e){
                e.preventDefault();
                var url = $(this).attr('href');
                var url_data = getFilterData();
                var finalURL = url + "&" + $.param(url_data);;
                // alert(finalURL);

                getPaginations(finalURL);
                window.history.pushState("", url);

            });

            function getPaginations(finalURL){
                $.ajax({
                    url : finalURL
                }).done(function(data){
                    $('.contactpaginate').html(data);

                }).fail(function(){

                    alert("Something gone wrong!")
                });
            }

        });
    </script>


    <script>
        $(document).ready(function(){
            $('.short-form').on('change',function(){
                if($(this).is(':checked')){
                    // document.getElementById("hideshowaddmodule").classList.remove("d-none");
                    $('.hideshowaddmodule').show();
                }else{
                    // document.getElementById("hideshowaddmodule").classList.add("d-none");
                    $('.hideshowaddmodule').hide();
                }
            });

            $('#short_form_code2').on('change',function(){
                var short_form_code = $(this).is(':checked') ?'1':'0';

                jQuery.ajax({
                    url: "{{ route('admin.contact.saveshortformcode') }}",
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


            $('.add-company-form').on('change',function(){
                if($(this).is(':checked')){
                    $('.hideShowAddCompanyForm').show();
                }else{
                    $('.hideShowAddCompanyForm').hide();
                }
            });

            $('#add_company_toggle').on('change',function(){
                var add_company_form = $(this).is(':checked') ?'1':'0';

                jQuery.ajax({
                    url: "{{ route('admin.contact.saveaddcompanyform') }}",
                    method: "POST",
                    type: "html",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        add_company_form: add_company_form
                    },
                    success: function(data){
                        if(data){
                            toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                        }
                    }
                });


            });
            

        });




    </script>


@endsection

