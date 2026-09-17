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

    @if (Auth::guard('admin')->user()->user_type == 2)
        @if ($permission->add_contactp == 0)
            <style>
                .addcontact{
                    display: none !important;
                }
            </style>
        @endif
        @if ($permission->edit_contactp == 0)
            <style>
                .editcontact{
                    display: none !important;
                }
            </style>
        @endif
        @if ($permission->view_contactp == 0)
            <style>
                .viewcontact{
                    display: none !important;
                }
            </style>
        @endif
        @if ($permission->delete_contactp == 0)
            <style>
                .delcontact{
                    display: none !important;
                }
            </style>
        @endif

        @if ($permission->contact_bulk_whatsapp_send == 0)
            <style>
                .bulcontactwhpsend{
                    display: none !important;
                }
            </style>
        @endif

    @endif

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

        .filterpanel {
            position: relative;
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

        /* Summary count pills — same design as the Leads/Testimonial list's status cards */
        .candidate-status-wrapper {
            width: 100%;
        }

        .contact-status-bar-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .02em;
            color: var(--bs-secondary-color, #6c757d);
            margin-bottom: 6px;
        }

        .candidate-status-card {
            background: var(--bs-card-bg);
            padding: 10px;
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
                                            @foreach ($businesstypes as $businesstype)
                                                <option value="{{ $businesstype->id }}" @if($contactsaveadminfilter && $contactsaveadminfilter->last_business_type_id == $businesstype->id) selected @endif>{{ $businesstype->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label" for="short-add-country-id">Country <span class="text-danger">*</span></label>
                                        <select name="country_id" id="short-add-country-id" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}" @if($contactsaveadminfilter && $contactsaveadminfilter->last_country_id == $country->id) selected @endif>{{ $country->name }}</option>
                                            @endforeach
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

            $customFilterColumns = collect(\Illuminate\Support\Facades\Schema::getColumnListing((new \App\Models\Contactplus())->getTable()))
                ->map(fn($col) => ['value' => $col, 'label' => ucwords(str_replace('_', ' ', $col))])
                ->values();
        @endphp

        <!--- Search Filter Start -->
        {{-- <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-3">Search Filter</h5>
                <div class="row">
                    <div class="col-md-3 mb-3 country-div"  @if(isset($contactpfilter) && $contactpfilter->country_filter == 1) @else style="display: none" @endif>
                        <select id="by-country" class="form-select select222" multiple data-placeholder="Select Country">
                            <option value="">Select Country</option>
                            @foreach ($countryfs as $country)
                                <option value="{{ $country->country_id }}">{{ $country->cname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 city-div" @if(isset($contactpfilter) && $contactpfilter->city_filter == 1) @else style="display: none" @endif>
                        <select id="by-city" class="form-select select222" multiple data-placeholder="Select City">
                            <option value="">Select City</option>
                            @foreach ($cityfs as $cityf)
                                <option value="{{ $cityf->city_id }}">{{ $cityf->citname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 group-name-div" @if(isset($contactpfilter) && $contactpfilter->group_name_filter == 1) @else style="display: none" @endif>
                        <select id="by-group" class="form-select select222" multiple data-placeholder="Select Group Name">
                            <option value="">Select Group</option>
                            <option value="gb0">Null</option>
                            @foreach ($groupfs as $groupf)
                                <option value="{{ $groupf->group_id }}">{{ $groupf->grpname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 life-cycle-status-div" @if(isset($contactpfilter) && $contactpfilter->life_cycle_status_filter == 1) @else style="display: none" @endif>
                        <select id="by-lifecycle-status" class="form-select select222" multiple data-placeholder="Select Life Cycle Status">
                            <option value="">Select Life Cycle Status</option>
                            @foreach ($lifcsts as $lifcst)
                                <option value="{{ $lifcst->lcs_id }}">{{ $lifcst->lfsname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 lead-stage-div" @if(isset($contactpfilter) && $contactpfilter->lead_stage_filter == 1) @else style="display: none" @endif>
                        <select id="by-lead-stage" class="form-select select222" multiple data-placeholder="Select Lead Stage">
                            <option value="">Select Lead Stage</option>
                            @foreach ($leadstg as $leadstg)
                                <option value="{{ $leadstg->ls_id }}">{{ $leadstg->leadstage }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 business-type-div" @if(isset($contactpfilter) && $contactpfilter->business_type_filter == 1) @else style="display: none" @endif>
                        <select id="by-business-type" class="form-select select222" multiple data-placeholder="Select Business Type">
                            <option value="">Select Business Type</option>
                            @foreach ($businessTypes as $businessType)
                                <option value="{{ $businessType->businesstype_id }}">{{ $businessType->businame }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <select name="" id="by-send-tag" class="form-select select222" multiple data-placeholder="Select Whatsapp Send Tag...">
                            <option value="">Select Whatsapp Send Tag</option>
                            @foreach ($sendTags as $sendTag)
                                <option value="{{ $sendTag->send_tag }}">{{ $sendTag->send_tag }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <input name="send_date_filter" type="text" id="by-send-date" class="form-control singledatepicker" placeholder="Send Date...">
                    </div>

                    <div class="col-md-3 mb-3 created-date-div" @if(isset($contactpfilter) && $contactpfilter->created_date_filter  == 1) @else style="display: none" @endif>
                        <input type="text" name="created_date_filter" id="by-created-date" class="form-control singledatepicker" placeholder="Created date...">
                    </div>

                    <div class="col-md-3 mb-3">
                        <input name="by_update_status_date" type="text" id="by-update-status-date" class="form-control bsdatpicket" placeholder="Update Status Date...">
                    </div>

                    <div class="col-md-3 mb-3 createdby-div" @if(isset($contactpfilter) && $contactpfilter->created_by  == 1) @else style="display: none" @endif>
                        <select name="" id="by-createdby" class="form-select select222" multiple data-placeholder="Select created by...">
                            <option value="">Select Created By</option>
                            @foreach ($createdBys as $createdBy)
                                <option value="{{ $createdBy->staff_id }}">{{ $createdBy->adminname }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div> --}}
        <!-- Search Filter End -->

        <!-- Contact Status Count Bar Start -->
        @php
            $leadStageColors = ['#7367F0', '#28C76F', '#00CFE8', '#FF9F43', '#EA5455', '#6F42C1', '#0D6EFD', '#4B4B4B', '#20C997'];
        @endphp
        <div class="card mb-3" id="contactStatusBar">
            <div class="card-body">
                <div class="contact-status-bar-label">Status</div>
                <div class="candidate-status-wrapper mb-3">
                    <div class="candidate-status-card">
                        <div class="candidate-status-grid">
                            <div class="status-item">
                                <a href="javascript:void(0)"
                                   class="status-btn contact-status-card"
                                   data-value=""
                                   style="border-color:#6C757D;color:#6C757D;--tsc-color:#6C757D;">
                                    <span>Total</span>
                                    <strong id="contact_status_count_total">{{ $statusCounts['total'] ?? 0 }}</strong>
                                </a>
                            </div>
                            <div class="status-item">
                                <a href="javascript:void(0)"
                                   class="status-btn contact-status-card"
                                   data-value="1"
                                   style="border-color:#28C76F;color:#28C76F;--tsc-color:#28C76F;">
                                    <span>Active</span>
                                    <strong id="contact_status_count_active">{{ $statusCounts['active'] ?? 0 }}</strong>
                                </a>
                            </div>
                            <div class="status-item">
                                <a href="javascript:void(0)"
                                   class="status-btn contact-status-card"
                                   data-value="0"
                                   style="border-color:#EA5455;color:#EA5455;--tsc-color:#EA5455;">
                                    <span>Inactive</span>
                                    <strong id="contact_status_count_inactive">{{ $statusCounts['inactive'] ?? 0 }}</strong>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact-status-bar-label">Lead Stage</div>
                <div class="candidate-status-wrapper">
                    <div class="candidate-status-card">
                        <div class="candidate-status-grid">
                            <div class="status-item">
                                <a href="javascript:void(0)"
                                   class="status-btn contact-leadstage-card"
                                   data-value=""
                                   style="border-color:#6C757D;color:#6C757D;--tsc-color:#6C757D;">
                                    <span>All</span>
                                    <strong id="contact_leadstage_count_all">{{ $statusCounts['total'] ?? 0 }}</strong>
                                </a>
                            </div>
                            @foreach ($leadStages as $index => $stage)
                                @php $leadStageColor = $leadStageColors[$index % count($leadStageColors)]; @endphp
                                <div class="status-item">
                                    <a href="javascript:void(0)"
                                       class="status-btn contact-leadstage-card"
                                       data-value="{{ $stage->id }}"
                                       style="border-color:{{ $leadStageColor }};color:{{ $leadStageColor }};--tsc-color:{{ $leadStageColor }};">
                                        <span>{{ $stage->name }}</span>
                                        <strong>{{ $leadStageCounts[$stage->id] ?? 0 }}</strong>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact Status Count Bar End -->

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
                    <button class="btn btn-xs btn-primary filterpanel" data-bs-toggle="modal" data-bs-target="#filterpanel"><i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter
                        <span class="filter-indicator d-none"></span>
                    </button>

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

                            <!-- Status Bar (Total / Active / Inactive count pills) -->
                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" value="1" class="switch-input contact-status-bar-toggle" checked />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Status Bar</span>
                                </label>
                            </div>

                            <a href="{{ url('admin/contact-plus-export-history') }}" class="dropdown-item">
                              <i class="fas fa-download me-1"></i> Contact Export History
                            </a>

                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#exportEmailQamrModal">
                              <i class="fas fa-paper-plane me-1"></i> Transfer Contact to email Portal
                            </a>

                            <a href="{{ url('admin/email-qamr-portal/export-history') }}" class="dropdown-item">
                              <i class="fas fa-history me-1"></i> Email Export History
                            </a>

                        </div>
                    </div>
                </div>
                <div class="mb-1 float-end">
                    <div class="btn-group mx-2">
                        <button class="btn btn-primary btn-sm dropdown-toggle bulkactions" type="button" disabled id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Bulk Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkexport"><i class="ti ti-download me-2"></i> Export</a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfergroup"><i class="ti ti-users me-2"></i> Transfer Group</a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfercareoff"><i class="ti ti-user me-2"></i> Transfer Careoff</a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransferleadowner"><i class="ti ti-user me-2"></i> Transfer Lead Owner</a>
                            <a class="dropdown-item bulcontactwhpsend" href="#" data-bs-toggle="offcanvas" data-bs-target="#bulkwhatsappsend"><i class="ti ti-brand-whatsapp me-2"></i> Send Whatsapp</a>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-target="#bulkdelete" data-bs-toggle="modal"><i class="ti ti-trash me-2"></i> Delete</a>
                        </div>
                    </div>
                    <button class="add-new btn btn-sm btn-primary addcontact addcandidate" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New Contact</span></button>
                </div>
                <div class="mb-1 float-end">
                    <input type="text" id="search_text" class="form-control" placeholder="Search..." autocomplete="off">
                </div>
            </div>
            <div class="card-datatable table-responsive contactpaginate">
                @include('admin.contactp.loadcontact')
            </div>
        </div>

        <!--- Add Candidate Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add New Contact</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.contact.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-business-type" class="form-label">Business Type <span class="text-danger">*</span></label>
                                <select name="businesstype_id" id="add-business-type" class="form-select select22" data-placeholder="Select Business Type" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($businesstypes as $businesstype)
                                        <option value="{{ $businesstype->id }}" @if($businesstype->name == 'B2C') selected @endif>{{ $businesstype->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label for="add-office-name-eng" class="form-label">Office / Company Name (Eng) <span class="text-danger">*</span></label>
                                    <input type="text" name="office_eng_name" id="add-office-name-eng" class="form-control" placeholder="Enter Office Name (eng)...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-office-name-ar" class="form-label">Office / Company Name (Arabic)</label>
                                <input type="text" name="office_ar_name" id="add-office-name-ar" class="form-control" placeholder="Enter Office Name (arabic)...">
                            </div>
                        </div>

                        <div class="col-md-6 showDIvInd" style="display: none">
                            <div class="mb-3">
                                <label for="add-industry" class="form-label">Industry</label>
                                <select name="industry_id" id="add-industry" class="form-select select22" data-placeholder="Select Industry" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($industries as $industry)
                                        <option value="{{ $industry->id }}">{{ $industry->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-office-no" class="form-label">Office Number</label>
                                <input type="text" name="office_no" id="add-office-no" class="form-control" placeholder="Enter Office Contact No...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-office-email" class="form-label">Office Email</label>
                                <input type="text" name="office_email" id="add-office-email" class="form-control" placeholder="Enter Office Email...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-owner-name" class="form-label">Owner Name</label>
                                <input type="text" name="owner_name" id="add-owner-name" class="form-control" placeholder="Enter Owner Name...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-owner-contact" class="form-label">Owner Contact</label>
                                <input type="text" name="owner_contact" id="add-owner-contact" class="form-control" placeholder="Enter Owner Phone...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-owner-email" class="form-label">Owner Email</label>
                                <input type="text" name="owenr_email" id="add-owner-email" class="form-control" placeholder="Enter Owner Email...">
                            </div>
                        </div> --}}

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-country-id">Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="add-country-id" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 showLicenceDiv" style="display: none">
                            <div class="mb-3">
                                <label for="add-licence-number" class="form-label">Licence Number <span class="text-danger">*</span></label>
                                <input type="text" name="licence_number" id="add-licence-number" class="form-control" placeholder="Enter Licence Number...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-city-id">City</label>
                                <select name="city_id" id="add-city-id" class="form-select select22" data-placeholder="Select City" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-primary-person" class="form-label">Primary Concern Person Name</label>
                                <input type="text" name="prim_concern_name" id="add-primary-person" class="form-control" placeholder="Enter Primary Concern Person...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-primary-contact" class="form-label">Primary Contact No</label>
                                <input type="text" name="prim_contact" id="add-primary-contact" class="form-control checkNoExistance" placeholder="Enter Primary Contact No...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-primary-email" class="form-label">Primary Email</label>
                                <input type="text" name="prim_email" id="add-primary-email" class="form-control" placeholder="Enter Primary Email...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-secondary-person" class="form-label">Seconday Concern Person Name</label>
                                <input type="text" name="sec_concern_name" id="add-secondary-person" class="form-control" placeholder="Enter Secondary Concern Person...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-secondary-contact" class="form-label">Seconday Contact No</label>
                                <input type="text" name="sec_contact" id="add-secondary-contact" class="form-control checkNoExistance" placeholder="Enter Secondary Contact No...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-secondary-email" class="form-label">Seconday Email</label>
                                <input type="text" name="sec_email" id="add-secondary-email" class="form-control" placeholder="Enter Seconday Email...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-person3" class="form-label">Concern Person Name 3</label>
                                <input type="text" name="concern_name3" id="add-person3" class="form-control" placeholder="Enter Concern Person 3...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact3" class="form-label">Contact No3</label>
                                <input type="text" name="contact3" id="add-contact3" class="form-control checkNoExistance" placeholder="Enter Contact No3...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-person4" class="form-label">Concern Person Name 4</label>
                                <input type="text" name="concern_name4" id="add-person4" class="form-control" placeholder="Enter Concern Person4...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact4" class="form-label">Contact No4</label>
                                <input type="text" name="contact4" id="add-contact4" class="form-control checkNoExistance" placeholder="Enter Contact No4...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-person5" class="form-label">Concern Person Name 5</label>
                                <input type="text" name="concern_name5" id="add-person5" class="form-control" placeholder="Enter Concern Person5...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact5" class="form-label">Contact No5</label>
                                <input type="text" name="contact5" id="add-contact5" class="form-control checkNoExistance" placeholder="Enter Contact No5...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-person6" class="form-label">Concern Person Name6</label>
                                <input type="text" name="concern_name6" id="add-person6" class="form-control" placeholder="Enter Concern Person6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact6" class="form-label">Contact No6</label>
                                <input type="text" name="contact6" id="add-contact6" class="form-control checkNoExistance" placeholder="Enter Contact No6...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact7" class="form-label">Contact No7</label>
                                <input type="text" name="contact7" id="add-contact7" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact8" class="form-label">Contact No8</label>
                                <input type="text" name="contact8" id="add-contact8" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact9" class="form-label">Contact No9</label>
                                <input type="text" name="contact9" id="add-contact9" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact10" class="form-label">Contact No10</label>
                                <input type="text" name="contact10" id="add-contact10" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact11" class="form-label">Contact No11</label>
                                <input type="text" name="contact11" id="add-contact11" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact12" class="form-label">Contact No12</label>
                                <input type="text" name="contact12" id="add-contact12" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-careoff" class="form-label">Careoff <span class="text-danger">*</span></label>
                                <select name="careoff_id" id="add-careoff" class="form-select select22" data-placeholder="Select Careoff" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($adminusers as $adminuser)
                                        <option value="{{ $adminuser->id }}" @if($adminuser->id == Auth::guard('admin')->user()->id) selected @endif>{{ $adminuser->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-leadowner" class="form-label">Lead Owner <span class="text-danger">*</span></label>
                                <select name="leadowner_id" id="add-leadowner" class="form-select select22" data-placeholder="Select Lead Owner" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($adminusers as $adminuser2)
                                        <option value="{{ $adminuser2->id }}" @if($adminuser2->id == Auth::guard('admin')->user()->id) selected @endif>{{ $adminuser2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="addnewcandidateBtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Add Candidate End --->

        <!--- Edit Candidate Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="edituserLabel" class="offcanvas-title">Edit Contact</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.contact.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="editID" id="edit_ID">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-business-type" class="form-label">Business Type</label>
                                <select name="businesstype_id" id="edit-business-type" class="form-select select22" data-placeholder="Select Business Type" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($businesstypes as $businesstype2)
                                        <option value="{{ $businesstype2->id }}">{{ $businesstype2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-name-eng" class="form-label">Office Name (Eng) <span class="text-danger">*</span></label>
                                <input type="text" name="office_eng_name" id="edit-office-name-eng" class="form-control" placeholder="Enter Office Name (eng)...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-name-ar" class="form-label">Office Name (Arabic)</label>
                                <input type="text" name="office_ar_name" id="edit-office-name-ar" class="form-control" placeholder="Enter Office Name (arabic)...">
                            </div>
                        </div>

                        <div class="col-md-6 showDIvInd2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-industry-id" class="form-label">Industry</label>
                                <select name="industry_id" id="edit-industry-id" class="form-select select22" data-placeholder="Select Industry" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($industries as $industry)
                                        <option value="{{ $industry->id }}">{{ $industry->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-no" class="form-label">Office Number</label>
                                <input type="text" name="office_no" id="edit-office-no" class="form-control" placeholder="Enter Office Contact No...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-email" class="form-label">Office Email</label>
                                <input type="text" name="office_email" id="edit-office-email" class="form-control" placeholder="Enter Office Email...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-owner-name" class="form-label">Owner Name</label>
                                <input type="text" name="owner_name" id="edit-owner-name" class="form-control" placeholder="Enter Owner Name...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-owner-contact" class="form-label">Owner Contact</label>
                                <input type="text" name="owner_contact" id="edit-owner-contact" class="form-control" placeholder="Enter Owner Phone...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-owner-email" class="form-label">Owner Email</label>
                                <input type="text" name="owenr_email" id="edit-owner-email" class="form-control" placeholder="Enter Owner Email...">
                            </div>
                        </div> --}}

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-country-id">Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="edit-country-id" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 showLicenceDiv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-licence-number" class="form-label">Licence Number <span class="text-danger">*</span></label>
                                <input type="text" name="licence_number" id="edit-licence-number" class="form-control" placeholder="Enter Licence Number...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-city-id">City</label>
                                <select name="city_id" id="edit-city-id" class="form-select select22" data-placeholder="Select City" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-primary-person" class="form-label">Primary Concern Person Name</label>
                                <input type="text" name="prim_concern_name" id="edit-primary-person" class="form-control" placeholder="Enter Primary Concern Person...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-primary-contact" class="form-label">Primary Contact No</label>
                                <input type="text" name="prim_contact" id="edit-primary-contact" class="form-control checkNoExistance" placeholder="Enter Primary Contact No...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-primary-email" class="form-label">Primary Email</label>
                                <input type="text" name="prim_email" id="edit-primary-email" class="form-control" placeholder="Enter Primary Email...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-secondary-person" class="form-label">Seconday Concern Person Name</label>
                                <input type="text" name="sec_concern_name" id="edit-secondary-person" class="form-control" placeholder="Enter Secondary Concern Person...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-secondary-contact" class="form-label">Seconday Contact No</label>
                                <input type="text" name="sec_contact" id="edit-secondary-contact" class="form-control checkNoExistance" placeholder="Enter Secondary Contact No...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-secondary-email" class="form-label">Seconday Email</label>
                                <input type="text" name="sec_email" id="edit-secondary-email" class="form-control " placeholder="Enter Seconday Email...">

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person3" class="form-label">Concern Person Name 3</label>
                                <input type="text" name="concern_name3" id="edit-person3" class="form-control" placeholder="Enter Concern Person 3...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact3" class="form-label">Contact No3</label>
                                <input type="text" name="contact3" id="edit-contact3" class="form-control checkNoExistance" placeholder="Enter Contact No3...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person4" class="form-label">Concern Person Name 4</label>
                                <input type="text" name="concern_name4" id="edit-person4" class="form-control" placeholder="Enter Concern Person4...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact4" class="form-label">Contact No4</label>
                                <input type="text" name="contact4" id="edit-contact4" class="form-control checkNoExistance" placeholder="Enter Contact No4...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person5" class="form-label">Concern Person Name 5</label>
                                <input type="text" name="concern_name5" id="edit-person5" class="form-control" placeholder="Enter Concern Person5...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact5" class="form-label">Contact No5</label>
                                <input type="text" name="contact5" id="edit-contact5" class="form-control checkNoExistance" placeholder="Enter Contact No5...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person6" class="form-label">Concern Person Name6</label>
                                <input type="text" name="concern_name6" id="edit-person6" class="form-control" placeholder="Enter Concern Person6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact6" class="form-label">Contact No6</label>
                                <input type="text" name="contact6" id="edit-contact6" class="form-control checkNoExistance" placeholder="Enter Contact No6...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact7" class="form-label">Contact No7</label>
                                <input type="text" name="contact7" id="edit-contact7" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact8" class="form-label">Contact No8</label>
                                <input type="text" name="contact8" id="edit-contact8" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact9" class="form-label">Contact No9</label>
                                <input type="text" name="contact9" id="edit-contact9" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact10" class="form-label">Contact No10</label>
                                <input type="text" name="contact10" id="edit-contact10" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact11" class="form-label">Contact No11</label>
                                <input type="text" name="contact11" id="edit-contact11" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact12" class="form-label">Contact No12</label>
                                <input type="text" name="contact12" id="edit-contact12" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-careoff" class="form-label">Careoff <span class="text-danger">*</span></label>
                                <select name="careoff_id" id="edit-careoff" class="form-select select22" data-placeholder="Select Careoff" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($adminusers as $adminuser3)
                                        <option value="{{ $adminuser3->id }}">{{ $adminuser3->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-leadowner" class="form-label">Lead Owner <span class="text-danger">*</span></label>
                                <select name="leadowner_id" id="edit-leadowner" class="form-select select22" data-placeholder="Select Lead Owner" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($adminusers as $adminuser4)
                                        <option value="{{ $adminuser4->id }}">{{ $adminuser4->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editnewcontact">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Edit Candidate End --->
        <!-- Delete Contactplus Start -->

        <div class="modal fade" id="deleteStaff" aria-hidden="true" aria-labelledby="deleteStaffLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Delete Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.contact.delete') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="contactID" id="contactID2">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                <div class="mb-3">
                                    <p class="text-danger">Are you sure to delete contact?</p>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Contactplus Start -->
        <!-- Lead Stage Update Start -->
        <div class="modal fade" id="updateLstage" aria-hidden="true" aria-labelledby="updateLstageLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
            <div class="modal-content">
                <div class="modal-header pb-2">
                <h5 class="offcanvas-title" id="updateLstageLabel">Lead Stage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.stageUpdate') }}" method="POST" id="updateLeadStageValidation">
                    @csrf
                    <input type="hidden" name="contactID" id="contactID">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="update-lead-cycle-status" class="form-label">Life Cycle Status <span class="text-danger">*</span></label>
                                        <select name="lcs_id" id="update-lead-cycle-status" class="form-control select22" data-allow-clear="true" data-placeholder="Select Lead Cycle Status">
                                            <option value=""></option>
                                            @foreach ($lcss as $lcs)
                                                <option value="{{ $lcs->id }}">{{ $lcs->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="update-lead-stage" class="form-label">Lead Stage <span class="text-danger">*</span></label>
                                        <select name="ls_id" id="update-lead-stage" class="form-control select22" data-allow-clear="true" data-placeholder="Select Lead...">

                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer">
                            {{-- <button class="btn btn-primary" type="submit">Update Lead Stage</button> --}}
                            <button class="btn btn-primary" type="button" id="leadStageUpdateBtn">Update Lead Stage</button>
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </div>
        <!-- Lead Stage Update End -->

        <!-- Filter List Start-->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="filter" aria-labelledby="filterLabel">
            <div class="offcanvas-header">
                <h5 id="filterLabel" class="offcanvas-title">Add Filter</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <div class="row">
                    <div class="col-md-12">
                        <div class="all-check">
                            <div class="form-check mt-2" id="shortform-f">
                                <input class="form-check-input" type="checkbox" name="shortformf" id="shortformf" value="1" @if(isset($contactpfilter) && $contactpfilter->short_form_filter == 1) checked @endif>
                                <label class="form-check-label" for="shortformf">Short Form</label>
                            </div>
                            <div class="form-check mt-2" id="country-f">
                                <input class="form-check-input" type="checkbox" name="countryf" value="1" id="countryf" @if(isset($contactpfilter) && $contactpfilter->country_filter == 1) checked @endif />
                                <label class="form-check-label" for="countryf"> Country</label>
                            </div>
                            <div class="form-check mt-2" id="city-f">
                                <input class="form-check-input" type="checkbox" name="cityf" value="1" id="cityf" @if(isset($contactpfilter) && $contactpfilter->city_filter == 1) checked @endif />
                                <label class="form-check-label" for="cityf"> City</label>
                            </div>
                            <div class="form-check mt-2" id="groupname-f">
                                <input class="form-check-input" type="checkbox" name="groupnamef" value="1" id="groupnamef" @if(isset($contactpfilter) && $contactpfilter->group_name_filter == 1) checked @endif />
                                <label class="form-check-label" for="groupnamef"> Group Name</label>
                            </div>
                            <div class="form-check mt-2" id="lifecyclestatus-f">
                                <input class="form-check-input" type="checkbox" name="lifecyclestatusf" value="1" id="lifecyclestatusf" @if(isset($contactpfilter) && $contactpfilter->life_cycle_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="lifecyclestatusf"> Life Cycle Status</label>
                            </div>
                            <div class="form-check mt-2" id="leadstage-f">
                                <input class="form-check-input" type="checkbox" name="leadstagef" value="1" id="leadstagef" @if(isset($contactpfilter) && $contactpfilter->lead_stage_filter == 1) checked @endif />
                                <label class="form-check-label" for="leadstagef"> Lead Stage</label>
                            </div>
                            <div class="form-check mt-2" id="businesstype-f">
                                <input class="form-check-input" type="checkbox" name="businesstypef" value="1" id="businesstypef" @if(isset($contactpfilter) && $contactpfilter->business_type_filter == 1) checked @endif />
                                <label class="form-check-label" for="businesstypef"> Business Type</label>
                            </div>
                            <div class="form-check mt-2" id="createddate-f">
                                <input class="form-check-input" type="checkbox" name="createddatef" value="1" id="createddatef" @if(isset($contactpfilter) && $contactpfilter->created_date_filter == 1) checked @endif />
                                <label class="form-check-label" for="createddatef"> Created Date</label>
                            </div>

                            <div class="form-check mt-2" id="createby-f">
                                <input class="form-check-input" type="checkbox" name="createbyf" value="1" id="createbyf" @if(isset($contactpfilter) && $contactpfilter->created_by == 1) checked @endif />
                                <label class="form-check-label" for="createbyf"> Created By</label>
                            </div>

                            <div class="mt-3">
                                <a href="#" id="all-chk"><span class="badge bg-label-primary">Select all</span></a>
                                <a href="#" id="all-unchk"><span class="badge bg-label-primary">Unselect all</span></a>
                                <a href="#" id="default-chk"><span class="badge bg-label-primary">Basic</span></a>
                                <a href="#" id="update-chk"><span class="badge bg-label-primary">Update</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filter List End -->

        <!-- Bulk Send Whatsapp Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="bulkwhatsappsend" aria-labelledby="bulkwhatsappsendLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="bulkwhatsappsendLabel" class="offcanvas-title">Send Bulk Whatsapp</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="bulksendwhatsapp" action="{{ route('admin.contact.bulksendwhatsapp') }}" method="POST" >
                    @csrf
                    <div class="row">
                        <input type="hidden" id="contactpID2" name="contactpID">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="send-whatsapp-type" class="form-label">For Whatsapp <span class="text-danger">*</span></label>
                                <select name="send_whatsapp_type" id="send-whatsapp-type" class="form-select select22" data-placeholder="Select For Whatsapp" data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="meta_whatsapp">Meta Whatsapp</option>
                                    <option value="normal_whatsapp" selected>Normal Whatsapp</option>
                                </select>
                            </div>
                        </div>
                        <!-- Meta Whatsapp Display Start -->
                        <div class="col-md-4 dismetawhatsapp" style="display: none">
                            <div class="mb-3">
                                <label for="send-meta-template-name" class="form-label">Meta Template <span class="text-danger">*</span></label>
                                <select name="metatemplate_id" id="send-meta-template-name" class="form-select select22" data-placeholder="Select Meta Template" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($metatemplates as $metatemplate)
                                        <option value="{{ $metatemplate->id }}">{{ $metatemplate->template_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Meta Whatsapp Display End -->
                        <!-- Normal Whatsapp Display Start -->
                        <div class="col-md-4 disnormalwhatsapp">
                            <div class="mb-3">
                                <label for="send-template-name" class="form-label">Template Name <span class="text-danger">*</span></label>
                                <select name="template_id" id="send-template-name" class="form-select select22" data-placeholder="Select Template Name" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($normaltemplates as $normaltemplate)
                                        <option value="{{ $normaltemplate->id }}">{{ $normaltemplate->template_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 disnormalwhatsapp">
                            <div class="mb-3">
                                <label class="form-label" for="send-template-whatsapp-api">Select Whatsapp API <span class="text-danger">*</span></label>
                                <select name="wapi_id_text[]" id="send-template-whatsapp-api" class="form-select select22"  multiple data-placeholder="Select Whatsapp API...">
                                    <option value=""></option>
                                    @foreach ($wapis as $wapi)
                                        <option value="{{ $wapi->id }}">{{ $wapi->mobile_no }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Common Element Start -->
                        <div class="col-md-4 disallforwhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-contact-type" class="form-label">Contact Type <span class="text-danger">*</span></label>
                                <select name="contact_type[]" id="send-meta-template-contact-type" class="form-select select22" data-placeholder="Select Contact Type" multiple>
                                    <option value="all" selected>All</option>
                                    <option value="owner">Owner</option>
                                    <option value="Primary">Primary</option>
                                    <option value="Secondary">Secondary</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 disallforwhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-contact-status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="contact_status[]" id="send-meta-template-contact-status" class="form-select select22" multiple data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 disallforwhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-subscribe" class="form-label">Subscribe <span class="text-danger">*</span></label>
                                <select name="subscribe[]" id="send-meta-template-subscribe" class="form-select select22" multiple data-placeholder="Select Subscribe">
                                    <option value=""></option>
                                    <option value="1">Subscribe</option>
                                    <option value="0">Unsubscribe</option>
                                </select>
                            </div>
                        </div>
                        <!-- Common Element End -->


                        <div class="col-md-4 disallforwhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-contact-group" class="form-label">Group</label>
                                <select name="contact_group_id[]" id="send-meta-template-contact-group" class="form-select select22" multiple data-placeholder="Select Group...">
                                    <option value="">Select Group</option>
                                    @foreach ($groupms as $groupm)
                                        <option value="{{ $groupm->id }}">{{ $groupm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 disnormalwhatsapp">
                            <div class="mb-3">
                                <label for="send-template-personalise-class" class="form-label">Personalise Name</label>
                                <select name="personalise_class" id="send-template-personalise-class" class="form-select select22" data-allow-clear="true" data-placeholder="Select Personalise Class">
                                    <option value=""></option>
                                    <option value="[Company]">Company</option>
                                    <option value="[Office Name (English)]">Office Name (English)</option>
                                    <option value="[Office Name (Arabic)]">Office Name (Arabic)</option>
                                    <option value="[Country]">Country</option>
                                    <option value="[City]">City</option>
                                    <option value="[Primary Concern Person]">Primary Concern Person</option>
                                    <option value="[Primary Contact No]">Primary Contact No</option>
                                    <option value="[Primary Email]">Primary Email</option>
                                    <option value="[Secondary Concern Person]">Secondary Concern Person</option>
                                    <option value="[Secondary Contact No]">Secondary Contact No</option>
                                    <option value="[Secondary Email]">Secondary Email</option>
                                    <option value="[Concern Person 3]">Concern Person 3</option>
                                    <option value="[Contact No 3]">Contact No 3</option>
                                    <option value="[Concern Person 4]">Concern Person 4</option>
                                    <option value="[Contact No 4]">Contact No 4</option>
                                    <option value="[Concern Person 5]">Concern Person 5</option>
                                    <option value="[Contact No 5]">Contact No 5</option>
                                    <option value="[Concer Person 6]">Concer Person 6</option>
                                    <option value="[Contact No 6]">Contact No 6</option>
                                    <option value="[Status]">Status</option>
                                    <option value="[Unsubscribe]">Unsubscribe</option>
                                </select>
                            </div>
                        </div>
                        <!-- Normal Whatsapp Display End -->
                    </div>
                    <!-- Meta Whatsapp Display Start -->

                    <div class="row dismetawhatsapp" style="display: none">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="send-meta-template-whatsapp-message" class="form-label">Whatsapp Message</label>
                                <textarea name="msg_whatsapp" id="send-meta-template-whatsapp-message" class="form-control" cols="30" rows="14"></textarea>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="mb-3">
                                <label for="send-meta-template-whatsapp-message-ar" class="form-label">Whatsapp Message Arabic</label>
                                <textarea name="msg_whatsapp_ar" id="send-meta-template-whatsapp-message-ar" class="form-control" cols="30" rows="5"></textarea>
                            </div>
                        </div> --}}

                        <div class="col-md-6">
                            <div class="mb-3">
                                <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar" id="uploadedAvatar"/>
                            </div>
                        </div>
                    </div>
                    <div class="row dismetawhatsapp" style="display: none">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-campaign-type" class="form-label">Campaign Type <span class="text-danger">*</span></label>
                                <select name="campaign_type" id="add-campaign-type" class="form-select select22" data-allow-clear="true" data-placeholder="Campaign Type...">
                                    <option value=""></option>
                                    <option value="1">Now</option>
                                    <option value="2">Scheduled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="disdateandtime" style="display: none">
                            <div class="mb-3">
                                <label for="add-date-and-time" class="form-label">Date and Time <span class="text-danger">*</span></label>
                                <input type="text" name="date_and_time" id="add-date-and-time" class="form-control flatpickr-datetime" placeholder="Enter date and time...">
                            </div>
                        </div>
                    </div>
                    <!-- Meta Whatsapp Display End -->
                    <!-- Normal Whatsapp Display Start -->
                    <div class="row disnormalwhatsapp">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="send-template-message" class="form-label">Whatsapp Message</label>
                                <textarea name="whatsapp_message" class="form-control" id="send-template-message" cols="30" rows="10"></textarea>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="mb-3">
                                <label for="send-template-message-ar" class="form-label">Whatsapp Message Arabic</label>
                                <textarea name="msg_whatsapp_ar" class="form-control" id="send-template-message-ar" cols="30" rows="6"></textarea>
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2"/>
                            </div>
                        </div>
                    </div>
                    <div class="row disnormalwhatsapp">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-campaign-type2" class="form-label">Campaign Type <span class="text-danger">*</span></label>
                                <select name="campaign_type2" id="add-campaign-type2" class="form-select select22" data-allow-clear="true" data-placeholder="Campaign Type...">
                                    <option value=""></option>
                                    <option value="1">Now</option>
                                    <option value="2">Scheduled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="disdateandtime2" style="display: none">
                            <div class="mb-3">
                                <label for="add-date-and-time2" class="form-label">Date and Time <span class="text-danger">*</span></label>
                                <input type="text" name="date_and_time2" id="add-date-and-time2" class="form-control flatpickr-datetime2" placeholder="Enter date and time...">
                            </div>
                        </div>
                    </div>
                    <!-- Normal Whatsapp Display End -->
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Bulk Send Whatsapp End -->

        <!-- Bulk Transfer Lead Owner Start -->
        <div class="modal fade" id="bulktransferleadowner" aria-hidden="true" aria-labelledby="bulktransferleadownerLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulktransferleadownerLabel">Transfer Lead Owner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.bulkleadownertransfer') }}" method="POST" id="bulktransferleadownevalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDLTR" id="contactIDLTRB">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="transfer-lead-owner" class="form-label">Transfer Lead Owner <span class="text-danger">*</span></label>
                                        <select name="leadowner_id" id="transfer-lead-owner" class="form-select select22" data-placeholder="Select Lead Owner" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($adminusers as $adminuserl)
                                                <option value="{{ $adminuserl->id }}">{{ $adminuserl->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Transfer Lead Owner</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Transfer Lead Owner End -->

        <!-- Bulk Transfer Careoff Start -->
        <div class="modal fade" id="bulktransfercareoff" aria-hidden="true" aria-labelledby="bulktransfercareoffLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulktransfercareoffLabel">Transfer Careoff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.bulkcareofftransfer') }}" method="POST" id="bulktransfercareoffvalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDCTR" id="contactIDCTR">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="transfer-careoff" class="form-label">Transfer Careoff <span class="text-danger">*</span></label>
                                        <select name="careoff_id" id="transfer-careoff" class="form-select select22" data-placeholder="Select Careoff" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($adminusers as $adminuser2)
                                                <option value="{{ $adminuser2->id }}">{{ $adminuser2->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Transfer Careoff</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Transfer Careoff End -->

        <!-- Bulk Group Transfer Start -->
        <div class="modal fade" id="bulktransfergroup" aria-hidden="true" aria-labelledby="bulktransfergroupLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulktransfergroupLabel">Transfer Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.bulkgrouptransfer') }}" method="POST" id="bulktransfergroupvalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDGRPTR" id="contactIDGRPTR">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <p id="totlaTransferGrp" class="text-success"></p>
                                        <label for="transfer-groupm" class="form-label">Transfer Group <span class="text-danger">*</span></label>
                                        <select name="group_id" id="transfer-groupm" class="form-select select22" data-placeholder="Select Group" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($groupms as $groupm)
                                                <option value="{{ $groupm->id }}">{{ $groupm->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <p id="respMessageGroupTransfer"></p>
                                    <p id="respGroupLimit"></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary dibtngrp" type="submit">Transfer Group</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Group Transfer End -->

        <!-- Export to email.qamr.in Portal Start -->
        <div class="modal fade" id="exportEmailQamrModal" aria-hidden="true" aria-labelledby="exportEmailQamrModalLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="exportEmailQamrModalLabel">Transfer Contact to email Portal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="eqp_alert" class="alert d-none" role="alert"></div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="eqp_api_token">API Token <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="eqp_api_token" class="form-control" placeholder="Enter email.qamr.in API token" autocomplete="off" value="{{ optional(Auth::guard('admin')->user())->email_qamr_api_token }}">
                                    <button type="button" id="eqp_token_toggle_btn" class="btn btn-outline-secondary" title="View token"><i class="ti ti-eye"></i></button>
                                    <button type="button" id="eqp_token_save_btn" class="btn btn-outline-primary">Save</button>
                                </div>
                                <small id="eqp_token_status" class="text-muted"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="eqp_business_type">Business Type <span class="text-danger">*</span></label>
                                <select id="eqp_business_type" class="form-select" required>
                                    <option value="">Select Business Type</option>
                                    @foreach ($businessTypes as $businessType)
                                        <option value="{{ $businessType->businesstype_id }}">{{ $businessType->businame }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="eqp_industries">Industry</label>
                                <select id="eqp_industries" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Industry">
                                    @foreach ($industruesFs as $industruesF)
                                        <option value="{{ $industruesF->industry_id }}">{{ $industruesF->industname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mb-2">
                                <label class="form-label" for="eqp_email_list">Email List <span class="text-danger">*</span></label>
                                <select id="eqp_email_list" class="form-select" disabled>
                                    <option value="">Enter API token first</option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3 mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Custom Field Mapping</label>
                                    <button type="button" id="eqp_add_mapping_btn" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-plus me-1"></i>Add Custom Field
                                    </button>
                                </div>
                                <div id="eqp_mapping_rows"></div>
                                <small class="text-muted">Map Contact Plus fields to Email Portal custom fields (optional). Select an Email List first.</small>
                            </div>

                            <div class="col-md-12">
                                <strong>Selected Contact Count: <span id="eqp_selected_count">0</span></strong>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="eqp_refresh_btn" class="btn btn-outline-secondary me-auto" title="Refresh"><i class="ti ti-refresh me-1"></i>Refresh</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="eqp_export_btn" class="btn btn-primary">Export</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Export to email.qamr.in Portal End -->

        <!-- Filter Panel Start -->
        <div class="modal fade" id="filterpanel" aria-hidden="true" aria-labelledby="filterpanelLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="filterpanelLabel">Contactplus Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <select id="by-business-type" class="form-select select22f" multiple data-placeholder="Select Business Type">
                                    <option value="">Select Business Type</option>
                                    @foreach ($businessTypes as $businessType)
                                        <option value="{{ $businessType->businesstype_id }}" @if(isset($contactsaveadminfilter) && in_array($businessType->businesstype_id,explode(",",$contactsaveadminfilter->businesstype_id))) selected @endif>{{ $businessType->businame }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select name="" id="by-industries" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Industry">
                                    {{-- <select name="" id="by-industries" class="form-select select22f" multiple data-placeholder="Select Industry"> --}}
                                    {{-- <option value="">Select Industry</option> --}}
                                    @foreach ($industruesFs as $industruesF)
                                        <option value="{{ $industruesF->industry_id }}" @if(isset($contactsaveadminfilter) && in_array($industruesF->industry_id,explode(",",$contactsaveadminfilter->industry_id))) selected @endif>{{ $industruesF->industname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-country" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" data-placeholder="Select Country">
                                    {{-- <select id="by-country" class="form-select select22f" multiple data-placeholder="Select Country"> --}}
                                    {{-- <option value="">Select Country</option> --}}
                                    @foreach ($countryfs as $country)
                                        <option value="{{ $country->country_id }}" @if(isset($contactsaveadminfilter) && in_array($country->country_id,explode(",",$contactsaveadminfilter->country_id))) selected @endif>{{ $country->cname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-city" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select City">
                                    {{-- <select id="by-city" class="form-select select22f" multiple data-placeholder="Select City"> --}}
                                    {{-- <option value="">Select City</option> --}}
                                    @foreach ($cityfs as $cityf)
                                        <option value="{{ $cityf->city_id }}" @if(isset($contactsaveadminfilter) && in_array($cityf->city_id,explode(",",$contactsaveadminfilter->city_id))) selected @endif>{{ $cityf->citname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-group" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Group Name">
                                    {{-- <select id="by-group" class="form-select select22f" multiple data-placeholder="Select Group Name"> --}}
                                    {{-- <option value="">Select Group</option> --}}
                                    <option value="gb0" @if(isset($contactsaveadminfilter) && in_array("gb0",explode(",",$contactsaveadminfilter->group_id))) selected @endif>Null</option>
                                    @foreach ($groupfs as $groupf)
                                        <option value="{{ $groupf->group_id }}" @if(isset($contactsaveadminfilter) && in_array($groupf->group_id,explode(",",$contactsaveadminfilter->group_id))) selected @endif>{{ $groupf->grpname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-lifecycle-status" class="selectpicker w-100" multiple data-actions-box="true" data-live-search="true" data-style="default-btn" title="Select Life Cycle Status">
                                    <option value="">Select Life Cycle Status</option>
                                    @foreach ($lifcsts as $lifcst)
                                        <option value="{{ $lifcst->lcs_id }}" @if(isset($contactsaveadminfilter) && in_array($lifcst->lcs_id,explode(",",$contactsaveadminfilter->lcs_id))) selected @endif>{{ $lifcst->lfsname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-lead-stage" class="selectpicker w-100" multiple data-actions-box="true" data-live-search="true" data-style="default-btn" title="Select Lead Stage">
                                    <option value="">Select Lead Stage</option>
                                    @foreach ($leadstg as $leadstg)
                                        <option value="{{ $leadstg->ls_id }}" @if(isset($contactsaveadminfilter) && in_array($leadstg->ls_id,explode(",",$contactsaveadminfilter->ls_id))) selected @endif>{{ $leadstg->leadstage }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-4 mb-3">
                                <select name="" id="by-send-tag" class="selectpicker w-100" data-live-search="true" data-actions-box="true" data-style="default-btn" multiple title="Select Whatsapp Send Tag...">
                                    <option value="">Select Whatsapp Send Tag</option>
                                    @foreach ($sendTags as $sendTag)
                                        <option value="{{ $sendTag->send_tag }}" @if(isset($contactsaveadminfilter) && in_array($sendTag->send_tag,explode(",",$contactsaveadminfilter->sned_tag))) selected @endif>{{ $sendTag->send_tag }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <input name="send_date_filter" type="text" id="by-send-date" @if(isset($contactsaveadminfilter)) value="{{ $contactsaveadminfilter->send_date }}" @endif class="form-control singledatepicker" placeholder="Send Date...">
                            </div>

                            <div class="col-md-4 mb-3">
                                <input type="text" name="created_date_filter" id="by-created-date" @if(isset($contactsaveadminfilter)) value="{{ $contactsaveadminfilter->created_date }}" @endif class="form-control singledatepicker" placeholder="Created date...">
                            </div>

                            <div class="col-md-4 mb-3">
                                <input type="text" name="update_date_filter" id="by-update-date" @if(isset($contactsaveadminfilter)) value="{{ $contactsaveadminfilter->updated_date }}" @endif class="form-control singledatepicker" placeholder="Updated date...">
                            </div>

                            <div class="col-md-4 mb-3">
                                <input name="by_update_status_date" type="text" id="by-update-status-date" @if(isset($contactsaveadminfilter)) value="{{ $contactsaveadminfilter->update_status_date }}" @endif class="form-control bsdatpicket" placeholder="Update Status Date...">
                            </div>
                            <div class="col-md-4 mb-3">
                                <select name="" id="by-createdby" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select created by...">
                                    {{-- <select name="" id="by-createdby" class="form-select select22f" multiple data-placeholder="Select created by..."> --}}
                                    {{-- <option value="">Select Created By</option> --}}
                                    @foreach ($createdBys as $createdBy)
                                        <option value="{{ $createdBy->staff_id }}" @if(isset($contactsaveadminfilter) && in_array($createdBy->staff_id,explode(",",$contactsaveadminfilter->created_by))) selected @endif>{{ $createdBy->adminname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select name="subscribe" id="by-subscribe" class="form-select select22f" multiple data-placeholder="Select Subscribe">
                                    <option value="">Select Subscribe</option>
                                    <option value="1" @if(isset($contactsaveadminfilter) && in_array("1",explode(",",$contactsaveadminfilter->subscribe))) selected @endif>Subscribe</option>
                                    <option value="0" @if(isset($contactsaveadminfilter) && in_array("0",explode(",",$contactsaveadminfilter->subscribe))) selected @endif>Unsubscribe</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select name="status" id="by-status" class="form-select select22f" multiple data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="1" @if(isset($contactsaveadminfilter) && in_array("1",explode(",",$contactsaveadminfilter->status))) selected @endif>Active</option>
                                    <option value="0" @if(isset($contactsaveadminfilter) && in_array("0",explode(",",$contactsaveadminfilter->status))) selected @endif>Inactive</option>
                                </select>
                            </div>

                        </div>

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
                        {{-- <button type="button" class="btn btn-sm btn-primary applyfilter">Apply Filter</button> --}}
                        <button type="button" class="btn btn-warning btn-sm resetfilter">Reset Filter</button>
                        <button type="button" class="btn btn-success btn-sm savetodoFilter">Save Filter</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filter Panel End -->

        <!-- Bulk Export Start -->
        <div class="modal fade" id="bulkexport" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel125">Export Contact Plus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.contactPlus.bulkexport') }}" method="POST" id="bulkassigntoValidation">
                    @csrf
                    <div class="modal-body">
                                <input type="hidden" name="bulkcontactp_id" id="bulkcontactp_id">
                                
                                <div id="Contactpcolumns" class="row g-2">
                                </div>

                                <p class="d-none text-danger AllexportdataMsg pt-2">
                                    This action will be applied only to the currently filtered data.
                                </p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="bulk-export" class="btn btn-sm btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
        <!-- Bulk Export End -->

        <!-- Bulk Delete Start -->
        <div class="modal fade" id="bulkdelete" aria-hidden="true" aria-labelledby="bulkdeleteLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Delete Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.contact.bulk.delete') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="contactids" id="delcontactIDS">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="mb-3">
                                      <p class="text-danger">Are you sure to delete contact?</p>
                                   </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Delete</button>
                         </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Bulk Delete End -->

        <!--- Start Bulk Import Contact Start -->
        <div class="modal fade" id="bulkimport" aria-hidden="true" aria-labelledby="bulkimportLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Import Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form id="importValidationAllcontact" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="modal-body">
                            <div class="row">
                                
                                <!-- CSV Upload -->
                                <div class="col-md-10 mb-3">
                                    <label for="add-allcontact-csv-data" class="form-label">Upload CSV File</label>
                                    <input type="file" name="allcontact_csv_data" id="add-allcontact-csv-data" class="form-control" accept=".csv">
                                </div>
                                <div class="col-md-2 mt-4">
                                    <button type="button" class="btn btn-primary btn-sm" id="uploadCSVBtn">Upload CSV</button>                                
                                </div>

                                <div class="col-md-4 6_dropdowns" style="display:none;">
                                    <div class="mb-3">
                                        <label for="bulk_business_type_id" class="form-label">Business Type<span class="text-danger">*</span></label>
                                        <select name="business_type_id" id="bulk_business_type_id" class="form-select select22" data-placeholder="Select Business Type" data-allow-clear="true">
                                            <option value="">Select</option>
                                            @foreach ($businesstypes as $businesstype)
                                                <option value="{{ $businesstype->id }}" @if($businesstype->name == 'B2C') selected @endif>{{ $businesstype->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-display-business-type" style="display:none;"></span>
                                    </div>
                                </div>

                                <div class="col-md-4 6_dropdowns" style="display:none;">
                                    <div class="mb-3">
                                        <label for="bulk_country_id" class="form-label">Country<span class="text-danger">*</span></label>
                                        <select name="country_id" id="bulk_country_id" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                            <option value="">Select</option>
                                            @foreach ($countryfs as $country)
                                                <option value="{{ $country->country_id }}">{{ $country->cname }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-display-country" style="display:none;"></span>
                                    </div>
                                </div>

                                

                                <div class="col-md-4 6_dropdowns" style="display:none;">
                                    <div class="mb-3">
                                        <label for="bulk_careoff_id" class="form-label">Careoff</label>
                                        <select name="careoff_id" id="bulk_careoff_id" class="form-select select22" data-placeholder="Select Careoff" data-allow-clear="true">
                                            <option value="not_required">Not Required</option>
                                            @foreach ($adminusers as $adminuser)
                                                <option value="{{ $adminuser->id }}" @if($adminuser->id == Auth::guard('admin')->user()->id) selected @endif>{{ $adminuser->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 6_dropdowns" style="display:none;">
                                    <div class="mb-3">
                                        <label for="bulk_source" class="form-label">Source</label>
                                            <select name="source" id="bulk_source" class="form-select select22" data-allow-clear="true" data-placeholder="Select Source">
                                                <option value="not_required">Not Required</option>
                                                <option value="Direct">Direct</option>
                                                <option value="Facebook">Facebook</option>
                                                <option value="Instagram">Instagram</option>
                                                <option value="Google">Google</option>
                                                <option value="LinkedIn">LinkedIn</option>
                                                <option value="Quora">Quora</option>
                                                <option value="Twitter">Twitter</option>
                                                <option value="Bing">Bing</option>
                                                <option value="Reddit">Reddit</option>
                                            </select>      
                                    </div>
                                </div>

                               
                                <div class="col-md-4 6_dropdowns" style="display:none;">
                                    <div class="mb-3">
                                        <label for="bulk_group_id" class="form-label">Group</label>
                                        <select name="group_id" id="bulk_group_id" class="form-select select22" data-allow-clear="true" data-placeholder="Select Group...">
                                            <option value="not_required">Not Required</option>
                                            @foreach ($groupms as $groupm)
                                                <option value="{{ $groupm->id }}">{{ $groupm->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- Error Display -->
                                <div class="col-md-12">
                                    <span class="text-danger error-display" style="display:none;"></span>
                                </div>
                                

                                <!-- Preview Table -->
                                <div class="col-md-12 mt-3">
                                    <p class="error-message text-danger" style="display:none;"></p>
                                    <table id="previewTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 50%;">CSV Header</th>
                                                <th style="width: 50%;">DB List Field</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamically filled via JS -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Progress Bar -->
                                <div class="col-md-12 mt-2">
                                    <div class="progress" style="height: 20px; display:none;" id="importProgressBarContainer">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                            role="progressbar" style="width: 0%;" id="importProgressBar">
                                            0%
                                        </div>
                                    </div>
                                </div>


                                <!-- Duplicate / Result Display -->
                                <div class="col-md-12 mt-2">
                                    <div class="duplicate-display" style="display:none;"></div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                Close
                            </button>
                           
                            <button type="button" class="btn btn-primary btn-sm" id="importCSVData" style="display:none;">
                                Import Data
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!-- Start Bulk Import Contact End -->

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
            applyContactPaginateHtml(data);
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
                        $('#edit-licence-number').val(data.licence_number);
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

                var typeName = $(this).find('option:selected').text().trim();
                if (typeName === 'Recruitment Agency') {
                    $('.showLicenceDiv').show();
                    $('#add-licence-number').prop('required', true);
                } else {
                    $('.showLicenceDiv').hide();
                    $('#add-licence-number').prop('required', false);
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

                var typeName2 = $(this).find('option:selected').text().trim();
                if (typeName2 === 'Recruitment Agency') {
                    $('.showLicenceDiv2').show();
                    $('#edit-licence-number').prop('required', true);
                } else {
                    $('.showLicenceDiv2').hide();
                    $('#edit-licence-number').prop('required', false);
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

                let is_all_export = $('#is-all-export').is(':checked');

                form.append(`<input type="hidden" name="_token" value="{{ csrf_token() }}">`);
                form.append(`<input type="hidden" name="bulkcontactp_id" value="${bulkcontactp_id}">`);
                form.append(`<input type="hidden" name="is_all_export" value="${is_all_export}">`);

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
                    2: 'Office English Name',
                    3: 'Office Arabic Name',
                    4: 'Primary Email',
                    5: 'Secondary Email',
                    6: 'Primary Contact',
                    7: 'Secondary Contact',
                    8: 'Contact No. 3',
                    9: 'Contact No. 4',
                    10: 'Lead Stage',
                    11: 'City',
                    12: 'Country',
                    13: 'Group',
                    14: 'Work',
                    15: 'Status',
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

                @if(auth()->user()->user_type == 1)
                    toggleContainer.append(`
                        <div class="form-check mb-2 col-6">
                            <input class="form-check-input"
                                type="checkbox"
                                id="is-all-export">
                            <label class="form-check-label" for="is-all-export">
                                Export all Data
                            </label>
                        </div>
                    `);
                @endif
            } 
            
            $(document).on('change', '.toggle-contactplus-column', function () {
                const col = $(this).data('column');
                localStorage.setItem('contactplus_col_' + col, this.checked);
            });

            $(document).on('change', '#is-all-export', function () {
                if ($(this).is(':checked')) {
                    $('.AllexportdataMsg').removeClass('d-none');
                } else {
                    $('.AllexportdataMsg').addClass('d-none');
                }
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
                                    applyContactPaginateHtml(data);
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
                        licence_number:{
                            required: function(){
                                return $('#add-business-type option:selected').text().trim() === 'Recruitment Agency';
                            }
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
                        licence_number:{
                            required: "Please Enter Licence Number"
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
                                    applyContactPaginateHtml(data);
                                }
                            });
                        },
                        error: function(xhr){
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                var firstError = Object.values(errors)[0][0];
                                toastr.error(firstError);
                            } else {
                                toastr.error('Something went wrong. Please try again.');
                            }
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
                        licence_number:{
                            required: function(){
                                return $('#edit-business-type option:selected').text().trim() === 'Recruitment Agency';
                            }
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
                        licence_number:{
                            required: "Please Enter Licence Number"
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
                                    applyContactPaginateHtml(data);
                                }
                            });

                        },
                        error: function(xhr){
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                var firstError = Object.values(errors)[0][0];
                                toastr.error(firstError);
                            } else {
                                toastr.error('Something went wrong. Please try again.');
                            }
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
            // The browser (not this app) was remembering old values typed into
            // this search box and re-filling it on every page load/refresh,
            // since it had no autocomplete attribute. Nothing here ever saves
            // this value server-side - clearing it on load stops the browser
            // from re-showing a stale one.
            $('#search_text').attr('autocomplete', 'off').val('');

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
                    subscribe:  $('#by-subscribe').val(),
                    status: $('#by-status').val(),
                    custom_filters: JSON.stringify(collectCustomFilters())
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
                        applyContactPaginateHtml(data);
                    },
                    error: function(xhr){
                        console.error('Contact list reload failed', xhr.status, xhr.responseText);
                    }
                });

                updateContactPlusFilterIndicator();
                syncContactStatusBarActive();
                syncContactLeadStageBarActive();
            }

            // Swaps the table partial in. Assigned on window (not a local
            // function) because several other, separate <script> blocks on
            // this page also swap .contactpaginate and need to call it too.
            // The Total/Active/Inactive counts on the status bar itself are
            // not touched here - same as the Testimonial list this mirrors,
            // they're rendered once on page load and stay put; only the
            // table and the active pill highlight change per click.
            window.applyContactPaginateHtml = function (data) {
                $('.contactpaginate').html(data);
            };

            // Highlights whichever status pill matches the current #by-status
            // selection (Total when nothing is selected, or both = same as none).
            window.syncContactStatusBarActive = function () {
                var selected = $('#by-status').val() || [];
                $('.contact-status-card').each(function () {
                    var value = $(this).data('value');
                    var isActive = (value === '' || value === undefined)
                        ? selected.length === 0
                        : selected.length === 1 && selected.indexOf(String(value)) !== -1;
                    $(this).toggleClass('active', isActive);
                });
            };

            // Calls reloadTodoList()/updateContactPlusFilterIndicator() directly
            // instead of setting #by-status and relying on its bound
            // change/input listener to pick it up - keeps this click acting
            // exactly like every other filter field's own explicit reload,
            // with nothing depending on select2 (which only initializes once
            // the Filter Panel modal has actually been opened) to notice the
            // programmatic value change.
            $(document).on('click', '.contact-status-card', function (e) {
                e.preventDefault();

                var value = $(this).data('value');
                var newValue = (value === '' || value === undefined) ? [] : [String(value)];

                $('#by-status').val(newValue);

                syncContactStatusBarActive();
                reloadTodoList();
            });

            // Highlights whichever lead-stage pill matches the current
            // #by-lead-stage selection (All when nothing is selected).
            window.syncContactLeadStageBarActive = function () {
                var selected = $('#by-lead-stage').val() || [];
                $('.contact-leadstage-card').each(function () {
                    var value = $(this).data('value');
                    var isActive = (value === '' || value === undefined)
                        ? selected.length === 0
                        : selected.length === 1 && selected.indexOf(String(value)) !== -1;
                    $(this).toggleClass('active', isActive);
                });
            };

            // Same direct-call approach as the status pills above: #by-lead-stage
            // is a bootstrap-select (selectpicker) field, decorated only once the
            // Filter Panel modal has actually been shown, so this doesn't rely on
            // that widget - or a change event bubbling through it - to filter.
            $(document).on('click', '.contact-leadstage-card', function (e) {
                e.preventDefault();

                var value = $(this).data('value');
                var newValue = (value === '' || value === undefined) ? [] : [String(value)];

                $('#by-lead-stage').val(newValue);
                try { $('#by-lead-stage').selectpicker('refresh'); } catch (err) {}

                syncContactLeadStageBarActive();
                reloadTodoList();
            });

            // Show / hide the yellow dot on the Filter button whenever any filter is applied
            function updateContactPlusFilterIndicator() {
                var selectFields = [
                    '#by-country',
                    '#by-city',
                    '#by-lifecycle-status',
                    '#by-lead-stage',
                    '#by-business-type',
                    '#by-industries',
                    '#by-send-tag',
                    '#by-createdby',
                    '#by-group',
                    '#by-subscribe',
                    '#by-status'
                ];

                var dateFields = [
                    '#by-send-date',
                    '#by-created-date',
                    '#by-update-date',
                    '#by-update-status-date'
                ];

                var isFiltered = false;

                selectFields.forEach(function (selector) {
                    if ($(selector).length && $(selector).val() && $(selector).val().length > 0) {
                        isFiltered = true;
                    }
                });

                dateFields.forEach(function (selector) {
                    if ($(selector).length && $(selector).val()) {
                        isFiltered = true;
                    }
                });

                if ($('.custom-filter-row').filter(function () {
                    return $(this).find('.custom-filter-column').val();
                }).length > 0) {
                    isFiltered = true;
                }

                if (isFiltered) {
                    $('.filterpanel .filter-indicator').removeClass('d-none').addClass('d-block');
                } else {
                    $('.filterpanel .filter-indicator').removeClass('d-block').addClass('d-none');
                }
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
            bindFilterChange('#by-status');

            // Reflect any already-applied (saved) filters on page load
            updateContactPlusFilterIndicator();
            syncContactStatusBarActive();
            syncContactLeadStageBarActive();

            // Custom Filter (Column + Operator + Value rows)
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

            // Columns whose values are dates/datetimes (contactpluses uses *_date / *_at
            // naming consistently — send_date, created_at, updated_at, etc.)
            function isCustomFilterDateColumn(column) {
                return /_at$|_date$/i.test(column || '');
            }

            // Tears down any date picker previously attached to the row's value input, so
            // it can be safely re-initialized (or left as plain text) after a change.
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
                        reloadTodoList();
                    }).on('cancel.daterangepicker', function () {
                        $(this).val('');
                        reloadTodoList();
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
                        reloadTodoList();
                    }).on('cancel.daterangepicker', function () {
                        $(this).val('');
                        reloadTodoList();
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

            $('#add_custom_filter_btn').on('click', function () {
                addCustomFilterRow();
            });

            $('#custom_filter_rows').on('click', '.custom-filter-remove', function () {
                var $row = $(this).closest('.custom-filter-row');
                destroyCustomFilterDatePicker($row.find('.custom-filter-value'));
                $row.remove();
                reloadTodoList();
            });

            $('#custom_filter_rows').on('change', '.custom-filter-column, .custom-filter-operator', function () {
                applyCustomFilterOperatorUI($(this).closest('.custom-filter-row'));
                reloadTodoList();
            });

            var customFilterValueDebounce = null;
            $('#custom_filter_rows').on('input', '.custom-filter-value', function () {
                clearTimeout(customFilterValueDebounce);
                customFilterValueDebounce = setTimeout(reloadTodoList, 400);
            });

            @if(isset($contactsaveadminfilter) && !empty($contactsaveadminfilter->custom_filters))
                var savedCustomFilters = @json($contactsaveadminfilter->custom_filters);
                savedCustomFilters.forEach(function (row) {
                    addCustomFilterRow(row.column, row.operator, row.value);
                });
                updateContactPlusFilterIndicator();
            @endif

            // Export to email.qamr.in Portal
            (function () {
                var eqpCsrfToken = "{{ csrf_token() }}";
                var eqpEmailListCache = [];
                var eqpSavedConfig = @json(optional(Auth::guard('admin')->user())->email_qamr_contactplus_config);

                function eqpShowAlert(message, type) {
                    $('#eqp_alert')
                        .removeClass('d-none alert-success alert-danger')
                        .addClass('alert-' + type)
                        .text(message);
                }

                function eqpHideAlert() {
                    $('#eqp_alert').addClass('d-none').text('');
                }

                // Select2's own ajax + infinite-scroll pagination drives the Email
                // List dropdown: scrolling near the bottom of the still-open list
                // fetches and appends the next page in place, preserving scroll
                // position - no manual "Show More" trigger/state needed.
                function eqpInitEmailList() {
                    var $el = $('#eqp_email_list');

                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }

                    eqpEmailListCache = [];

                    $el.prop('disabled', false).empty().select2({
                        dropdownParent: $el.parent(),
                        placeholder: 'Select Email List',
                        ajax: {
                            delay: 250,
                            cache: false,
                            transport: function (params, success, failure) {
                                var term = $.trim(params.data.term || '');

                                // The vendor API has no search/term support on
                                // GET /api/v1/lists, so typed search filters
                                // client-side over whatever pages have already
                                // been fetched via scrolling, instead of hitting
                                // the network again on every keystroke.
                                if (term) {
                                    success({ eqpLocalFilter: true, term: term });
                                    return;
                                }

                                $.ajax({
                                    url: "{{ route('admin.email_qamr_portal.lists') }}",
                                    method: 'POST',
                                    data: {
                                        page: params.data.page || 1,
                                        _token: eqpCsrfToken
                                    }
                                }).then(success, failure);
                            },
                            processResults: function (resp) {
                                if (resp.eqpLocalFilter) {
                                    var term = resp.term.toLowerCase();
                                    var filtered = eqpEmailListCache.filter(function (item) {
                                        return item.text.toLowerCase().indexOf(term) !== -1;
                                    });

                                    return { results: filtered, pagination: { more: false } };
                                }

                                if (!resp.success) {
                                    $('#eqp_token_status').text(resp.message || 'Invalid API token.');
                                    return { results: [], pagination: { more: false } };
                                }

                                $('#eqp_token_status').text('');

                                var results = (resp.lists || []).map(function (list) {
                                    var item = { id: list.uid, text: list.name + ' (' + list.uid + ')' };
                                    var alreadyCached = eqpEmailListCache.some(function (cached) {
                                        return cached.id === item.id;
                                    });
                                    if (!alreadyCached) {
                                        eqpEmailListCache.push(item);
                                    }
                                    return item;
                                });

                                return {
                                    results: results,
                                    pagination: { more: !!resp.has_more }
                                };
                            }
                        }
                    });
                }

                function eqpUpdateCount() {
                    var data = typeof getFilterData === 'function' ? getFilterData() : {};
                    data.businesstype_id = $('#eqp_business_type').val();
                    data.industries = $('#eqp_industries').val();
                    data._token = eqpCsrfToken;

                    $.ajax({
                        url: "{{ route('admin.email_qamr_portal.filtered_count') }}",
                        method: 'POST',
                        data: data,
                        success: function (resp) {
                            if (resp.success) {
                                $('#eqp_selected_count').text(resp.count);
                            }
                        }
                    });
                }

                // Custom Field Mapping
                var eqpContactFields = [];
                var eqpContactFieldsLoaded = false;
                var eqpPortalFields = [];

                function eqpLoadContactFields(callback) {
                    if (eqpContactFieldsLoaded) {
                        if (callback) {
                            callback();
                        }
                        return;
                    }

                    $.ajax({
                        url: "{{ route('admin.email_qamr_portal.contact_fields') }}",
                        method: 'GET',
                        success: function (resp) {
                            if (resp.success) {
                                eqpContactFields = resp.fields || [];
                                eqpContactFieldsLoaded = true;
                            }
                            if (callback) {
                                callback();
                            }
                        },
                        error: function () {
                            if (callback) {
                                callback();
                            }
                        }
                    });
                }

                function eqpLoadPortalFields(listUid, callback) {
                    eqpPortalFields = [];
                    $('.eqp-mapping-portal-field').each(function () {
                        eqpFillPortalFieldSelect($(this));
                    });

                    if (!listUid) {
                        if (callback) {
                            callback();
                        }
                        return;
                    }

                    $.ajax({
                        url: "{{ route('admin.email_qamr_portal.list_fields') }}",
                        method: 'POST',
                        data: { list_uid: listUid, _token: eqpCsrfToken },
                        success: function (resp) {
                            if (!resp.success) {
                                eqpShowAlert(resp.message || 'Unable to load Email Portal fields for this list.', 'danger');
                                if (callback) {
                                    callback();
                                }
                                return;
                            }

                            eqpPortalFields = resp.fields || [];
                            $('.eqp-mapping-portal-field').each(function () {
                                eqpFillPortalFieldSelect($(this));
                            });

                            if (callback) {
                                callback();
                            }
                        },
                        error: function () {
                            eqpShowAlert('Unable to load Email Portal fields for this list.', 'danger');
                            if (callback) {
                                callback();
                            }
                        }
                    });
                }

                function eqpFillContactFieldSelect($select) {
                    var current = $select.val();
                    $select.empty().append($('<option>').val('').text('Select Contact Plus Field'));

                    eqpContactFields.forEach(function (field) {
                        $select.append($('<option>').val(field).text(field));
                    });

                    if (current) {
                        $select.val(current);
                    }

                    $select.trigger('change');
                }

                function eqpFillPortalFieldSelect($select) {
                    var $row = $select.closest('.eqp-mapping-row');
                    var preset = $row.data('preset-key');
                    var current = $select.val();

                    $select.empty().append($('<option>').val('').text('Select Email Portal Field'));

                    eqpPortalFields.forEach(function (field) {
                        $select.append(
                            $('<option>').val(field.key).text(field.label + ' (' + field.key + ')')
                        );
                    });

                    if (preset) {
                        $select.val(preset);
                        $row.removeData('preset-key');
                    } else if (current) {
                        $select.val(current);
                    }

                    $select.trigger('change');
                }

                function eqpAddMappingRow(presetField, presetKey) {
                    var $row = $(
                        '<div class="row g-2 align-items-center mb-2 eqp-mapping-row">' +
                            '<div class="col-md-5">' +
                                '<select class="form-select eqp-mapping-contact-field"></select>' +
                            '</div>' +
                            '<div class="col-md-5">' +
                                '<select class="form-select eqp-mapping-portal-field"></select>' +
                            '</div>' +
                            '<div class="col-md-2 d-flex gap-1">' +
                                '<button type="button" class="btn btn-sm btn-outline-secondary eqp-mapping-add" title="Add More"><i class="ti ti-plus"></i></button>' +
                                '<button type="button" class="btn btn-sm btn-outline-danger eqp-mapping-remove" title="Remove"><i class="ti ti-trash"></i></button>' +
                            '</div>' +
                        '</div>'
                    );

                    if (presetKey) {
                        $row.data('preset-key', presetKey);
                    }

                    $('#eqp_mapping_rows').append($row);

                    var $contactFieldSelect = $row.find('.eqp-mapping-contact-field');
                    var $portalFieldSelect = $row.find('.eqp-mapping-portal-field');

                    $contactFieldSelect.select2({ dropdownParent: $row, width: '100%' });
                    $portalFieldSelect.select2({ dropdownParent: $row, width: '100%' });

                    eqpLoadContactFields(function () {
                        eqpFillContactFieldSelect($contactFieldSelect);
                        if (presetField) {
                            $contactFieldSelect.val(presetField).trigger('change');
                        }
                    });

                    eqpFillPortalFieldSelect($portalFieldSelect);

                    return $row;
                }

                function eqpCollectFieldMappings() {
                    var mappings = [];
                    var incomplete = false;

                    $('.eqp-mapping-row').each(function () {
                        var contactField = $(this).find('.eqp-mapping-contact-field').val();
                        var portalField = $(this).find('.eqp-mapping-portal-field').val();

                        if (contactField && portalField) {
                            mappings.push({
                                all_contact_field: contactField,
                                email_portal_field: portalField
                            });
                        } else if (contactField || portalField) {
                            incomplete = true;
                        }
                    });

                    return { mappings: mappings, incomplete: incomplete };
                }

                function eqpApplySavedConfig() {
                    if (!eqpSavedConfig) {
                        return;
                    }

                    if (eqpSavedConfig.businesstype_id) {
                        $('#eqp_business_type').val(eqpSavedConfig.businesstype_id).trigger('change');
                    }

                    if (eqpSavedConfig.industries && eqpSavedConfig.industries.length) {
                        $('#eqp_industries').val(eqpSavedConfig.industries.map(String));
                        if ($('#eqp_industries').hasClass('selectpicker')) {
                            $('#eqp_industries').selectpicker('refresh');
                        }
                        $('#eqp_industries').trigger('change');
                    }

                    function eqpRestoreMappingRows() {
                        if (eqpSavedConfig.field_mappings && eqpSavedConfig.field_mappings.length) {
                            $('#eqp_mapping_rows').empty();

                            eqpSavedConfig.field_mappings.forEach(function (mapping) {
                                eqpAddMappingRow(mapping.field, mapping.key);
                            });
                        }
                    }

                    if (eqpSavedConfig.list_uid) {
                        var $emailList = $('#eqp_email_list');

                        if (!$emailList.find('option[value="' + eqpSavedConfig.list_uid + '"]').length) {
                            var optionText = (eqpSavedConfig.list_name || eqpSavedConfig.list_uid) + ' (' + eqpSavedConfig.list_uid + ')';
                            $emailList.append(new Option(optionText, eqpSavedConfig.list_uid, true, true));
                        }

                        $emailList.val(eqpSavedConfig.list_uid).trigger('change');

                        // Wait for the Email Portal field list to finish loading before
                        // rebuilding mapping rows, otherwise the portal-field <select>
                        // has no options yet and the saved preset silently fails to select.
                        eqpLoadPortalFields(eqpSavedConfig.list_uid, eqpRestoreMappingRows);
                    } else {
                        eqpRestoreMappingRows();
                    }
                }

                $('#eqp_add_mapping_btn').on('click', function () {
                    eqpAddMappingRow();
                });

                $('#eqp_mapping_rows').on('click', '.eqp-mapping-add', function () {
                    eqpAddMappingRow();
                });

                $('#eqp_mapping_rows').on('click', '.eqp-mapping-remove', function () {
                    $(this).closest('.eqp-mapping-row').remove();
                });

                $('#eqp_mapping_rows').on('change', '.eqp-mapping-portal-field', function () {
                    var val = $(this).val();
                    var $this = $(this);

                    if (!val) {
                        return;
                    }

                    var duplicate = false;
                    $('.eqp-mapping-portal-field').not($this).each(function () {
                        if ($(this).val() === val) {
                            duplicate = true;
                        }
                    });

                    if (duplicate) {
                        toastr.error('This Email Portal Field is already mapped in another row.');
                        $this.val('').trigger('change');
                    }
                });

                $('#eqp_email_list').on('change', function () {
                    eqpLoadPortalFields($(this).val());
                });

                $('#exportEmailQamrModal').on('show.bs.modal', function () {
                    eqpHideAlert();
                    eqpUpdateCount();
                });

                $('body').on('shown.bs.modal', '#exportEmailQamrModal', function () {
                    if (!$('#eqp_business_type').hasClass('select2-hidden-accessible')) {
                        $('#eqp_business_type').select2({
                            dropdownParent: $('#eqp_business_type').parent()
                        });
                    }

                    eqpInitEmailList();
                    eqpApplySavedConfig();
                });

                $('#eqp_token_toggle_btn').on('click', function () {
                    var $input = $('#eqp_api_token');
                    var $icon = $(this).find('i');
                    var isHidden = $input.attr('type') === 'password';

                    $input.attr('type', isHidden ? 'text' : 'password');
                    $icon.toggleClass('ti-eye', !isHidden).toggleClass('ti-eye-off', isHidden);
                    $(this).attr('title', isHidden ? 'Hide token' : 'View token');
                });

                // Save button is the only trigger for persisting the modal's
                // whole configuration: token, Business Type, Industry, Email
                // List, and Custom Field Mappings, all in one request.
                $('#eqp_token_save_btn').on('click', function () {
                    eqpHideAlert();

                    var token = $.trim($('#eqp_api_token').val());

                    if (!token) {
                        eqpShowAlert('API Token is required.', 'danger');
                        return;
                    }

                    var mappingResult = eqpCollectFieldMappings();

                    if (mappingResult.incomplete) {
                        eqpShowAlert('Please select both fields for every custom field mapping, or remove the incomplete row.', 'danger');
                        return;
                    }

                    var $btn = $(this);
                    $btn.prop('disabled', true);

                    var data = {
                        api_token: token,
                        businesstype_id: $('#eqp_business_type').val(),
                        industries: $('#eqp_industries').val(),
                        list_uid: $('#eqp_email_list').val(),
                        list_name: $('#eqp_email_list option:selected').text(),
                        field_mappings: mappingResult.mappings,
                        _token: eqpCsrfToken
                    };

                    $.ajax({
                        url: "{{ route('admin.email_qamr_portal.config.save') }}",
                        method: 'POST',
                        data: data,
                        success: function (resp) {
                            if (resp.success) {
                                eqpShowAlert(resp.message || 'Configuration saved.', 'success');
                            } else {
                                eqpShowAlert(resp.message || 'Unable to save configuration.', 'danger');
                            }
                        },
                        error: function (xhr) {
                            var msg = 'Unable to save configuration.';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            eqpShowAlert(msg, 'danger');
                        },
                        complete: function () {
                            $btn.prop('disabled', false);
                        }
                    });
                });

                $('#eqp_business_type, #eqp_industries').on('change', function () {
                    eqpUpdateCount();
                });

                $('#eqp_refresh_btn').on('click', function () {
                    eqpHideAlert();
                    eqpUpdateCount();
                    eqpInitEmailList();

                    toastr.info('Refreshed.');
                });

                $('#eqp_export_btn').on('click', function () {
                    eqpHideAlert();

                    var token = $.trim($('#eqp_api_token').val());
                    var businessType = $('#eqp_business_type').val();
                    var listUid = $('#eqp_email_list').val();
                    var listName = $('#eqp_email_list option:selected').text();

                    if (!token) {
                        eqpShowAlert('Please enter and save your API Token first.', 'danger');
                        return;
                    }

                    if (!businessType) {
                        eqpShowAlert('Business Type is required.', 'danger');
                        return;
                    }

                    if (!listUid) {
                        eqpShowAlert('Please select an Email List.', 'danger');
                        return;
                    }

                    var mappingResult = eqpCollectFieldMappings();

                    if (mappingResult.incomplete) {
                        eqpShowAlert('Please select both fields for every custom field mapping, or remove the incomplete row.', 'danger');
                        return;
                    }

                    var $btn = $(this);
                    $btn.prop('disabled', true).text('Exporting...');

                    // The saved API Token is used server-side; it is not sent in this payload.
                    var data = typeof getFilterData === 'function' ? getFilterData() : {};
                    data.list_uid = listUid;
                    data.list_name = listName;
                    data.businesstype_id = $('#eqp_business_type').val();
                    data.industries = $('#eqp_industries').val();
                    data.field_mappings = mappingResult.mappings;
                    data._token = eqpCsrfToken;

                    $.ajax({
                        url: "{{ route('admin.email_qamr_portal.export') }}",
                        method: 'POST',
                        data: data,
                        success: function (resp) {
                            if (resp.success) {
                                // Job is already dispatched to the queue; redirect immediately
                                // without waiting for it to finish processing.
                                window.location.href = "{{ route('admin.email_qamr_portal.history') }}";
                            } else {
                                eqpShowAlert(resp.message || 'Unable to start export.', 'danger');
                            }
                        },
                        error: function (xhr) {
                            var msg = 'Unable to start export.';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            eqpShowAlert(msg, 'danger');
                        },
                        complete: function () {
                            $btn.prop('disabled', false).text('Export');
                        }
                    });
                });
            })();


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
                var status = $('#by-status').val();


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
                        subscribe: subscribe,
                        status: status,
                        custom_filters: JSON.stringify(collectCustomFilters())
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

                if ($('#by-status').val() != '') {
                    $('#by-status').val('').trigger('change');
                }

                $('#custom_filter_rows').empty();
                reloadTodoList();

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
                    applyContactPaginateHtml(data);

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

            // Status Bar toggle: shows/hides the Total/Active/Inactive count
            // bar. Persisted client-side (not saved server-side, per request)
            // so the choice survives reloads and keeps working across the
            // AJAX filter/table refresh, since that refresh only swaps the
            // table partial and never touches this bar's container. display
            // is forced with !important (and bound via delegation) so
            // nothing else on this page can silently override it.
            (function(){
                function setContactStatusBarVisible(visible){
                    var bar = document.getElementById('contactStatusBar');
                    if (!bar) return;
                    if (visible) {
                        bar.style.removeProperty('display');
                    } else {
                        bar.style.setProperty('display', 'none', 'important');
                    }
                }

                var stored = null;
                try { stored = localStorage.getItem('contactplus_status_bar_visible'); } catch(e) {}
                var visible = stored === null ? true : stored === '1';
                $('.contact-status-bar-toggle').prop('checked', visible);
                setContactStatusBarVisible(visible);

                $(document).on('change', '.contact-status-bar-toggle', function(){
                    var isVisible = $(this).is(':checked');
                    setContactStatusBarVisible(isVisible);
                    try { localStorage.setItem('contactplus_status_bar_visible', isVisible ? '1' : '0'); } catch(e) {}
                });
            })();


        });




    </script>


@endsection

