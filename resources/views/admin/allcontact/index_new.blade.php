@extends('layout.admin.admin_layout')

@section('title','All Contact')

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
    <link rel="stylesheet" href="{{ asset('user/intl-tel-input-master/build/css/intlTelInput.css') }}">

    <style>

        .pagestyle{
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            /* color: #495057; */
            /* background-color: #fff; */
            background-clip: padding-box;
            /* border: 1px solid #ced4da; */
            border-radius: .25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out
        }

        .pagestyle:focus{
            /* color: #6e6b7b; */
            /* background-color: #fff; */
            /* border-color: #7367f0; */
            outline: 0;
            /* box-shadow: 0 3px 10px 0 rgba(34, 41, 47, 0.1); */
        }
        .shortformmargin{
            /* margin-right: 195px; */
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
    </style>

    <style>
        /* Compact toolbar layout: one row per category, label inline to
        the left of its pills instead of a heading line + boxed card above
        them — same information/functionality, a fraction of the height. */
        .allcontact-filterbar {
          padding: 8px 14px;
        }

        .allcontact-filterbar-row {
          display: flex;
          align-items: center;
          flex-wrap: wrap;
          gap: 8px;
          padding: 5px 0;
        }

        .allcontact-filterbar-row + .allcontact-filterbar-row {
          border-top: 1px solid var(--bs-border-color, rgba(0, 0, 0, .06));
        }

        .allcontact-filterbar-label {
          flex: 0 0 auto;
          min-width: 100px;
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
        }

        .status-btn.all-status-card.active {
            background: #7367F0;
            color: #fff !important;
        }

        .status-btn.all-status-card.active span,
        .status-btn.all-status-card.active strong {
            color: #fff !important;
        }

        @media (max-width: 575px) {
          .allcontact-filterbar-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
          }

          .allcontact-filterbar-label {
            min-width: 0;
          }
        }
    </style>

    <style>
        .report-scroll{
            max-height:70vh;      /* Only table data scrolls */
            overflow-y:auto;
            overflow-x:auto;
        }

        .report-scroll table{
            white-space:nowrap;
            margin-bottom:0;
        }

        .report-scroll thead th{
            position:sticky;
            top:0;
            z-index:3;
            background:#343a40;
            color:#fff;
        }

        /* Keep Group column visible while horizontal scrolling */
        .report-scroll .sticky-col{
            position:sticky;
            left:0;
            z-index:2;
            background:#fff;
        }

        .report-scroll thead .sticky-col{
            z-index:4;
            background:#343a40;
            color:#fff;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">

        <!-- Group Wise Contact Report Modal -->

        <div class="modal fade"
            id="groupwisecontactreport"
            tabindex="-1"
            aria-hidden="true"
            data-bs-backdrop="static"
            data-bs-keyboard="false">

            <div class="modal-dialog modal-xl" style="max-width:95%;">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-chart-bar me-2"></i>
                            Group Wise Contact Report
                        </h5>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-0">

                        <div class="groupwisecontactreportContainer">
                            <!-- AJAX Report -->
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-primary"
                                id="refreshModalBtn">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>

                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>

                </div>
            </div>
        </div>


        <div class="modal fade" id="gmailAccountModal" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('admin.allcontact.google.redirect') }}" method="GET">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">Select Careoff User to Sync Google Contacts</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                        

                            <div class="mb-3">

                                <label class="form-label">Careoff</label>
                                    <select
                                    class="selectpicker w-100 dynamic-filter load-filter"
                                    name="sync_contact_by_careof_id"
                                    data-filter="careoff"
                                    data-live-search="true"
                                    data-actions-box="true"
                                    data-style="default-btn"
                                    title="Select Careoff" required>
                                    </select>
                            </div>


                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                                Close
                            </button>

                            <button type="submit"
                                class="btn btn-danger">
                                Continue with Google
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-3 hideshowaddmodule" id="hideshowaddmodule" @if(isset($allcontactsaveadminfilter) && $allcontactsaveadminfilter->short_form_code == 1) @else style="display: none" @endif>
            <div class="card-body">
                <form action="{{ route('admin.allcontact.uploadstore') }}" id="shortcontactsaveValidation" method="POST" class="shortform-div">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-short-fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" id="add-short-fullname" class="form-control" placeholder="Enter Full Name...">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-short-mobile-number" class="form-label">Primary Number (Whatsapp) <span class="text-danger">*</span></label>
                                <input type="text" name="primary_no_wsp" id="add-short-mobile-number" class="form-control add-short-mobile-no" placeholder="Enter mobile number">
                                <input type="hidden" name="primary_no_wsp_dial_code" id="mobile-no-dial-code-short">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="add_short_lead_type" class="form-label">Business Type</label>
                                <select name="lead_type" id="add_short_lead_type" class="form-control select2e lead_type" data-allow-clear="true" data-placeholder="Select Business Type">
                                    <option value="">Select</option>
                                    <option value="Unknown">Other</option>
                                    <option value="Job seeker" selected>Job seeker</option>
                                    <option value="Regular Wakala Party">Regular Wakala Party</option>
                                    <option value="Direct Wakala Candidate">Direct Wakala Candidate</option>
                                    <option value="Agent without office">Agent without office </option>
                                    <option value="Associate with office">Associate with office</option>
                                    <option value="Company">Company</option>
                                    <option value="Trade site Center">Trade site Center</option>
                                    <option value="HIRING US">HIRING US</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="add-short-source" class="form-label">Source</label>
                                <select name="source" id="add-short-source" class="form-control select2e source" data-allow-clear="true" data-placeholder="Select Source">
                                    <option value="">Select</option>
                                    <option value="Whats Air" selected>Whats Air</option>
                                    <option value="Office Visit">Office Visit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="mb-3 mt-3">
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" name="send_whatsapp" type="checkbox" id="add-short-send-whatsapp" value="1" />
                                    <label class="form-check-label" for="add-short-send-whatsapp">Msg</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="mb-3 mt-4">
                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        </div>
                    </div> --}}
                </form>
            </div>
        </div>

        @if(Auth::guard('admin')->user()->user_type == 1 || optional($permission)->full_access == 1 || optional($permission)->allcontact_view == 1)
        @php
            // Each category below is now an independent single-select group
            // (Contact Status / Business Type / Lead Stage combine via AND
            // instead of one all-or-nothing selection across the whole
            // bar). Reflect whichever pill the saved filter actually
            // implies so the correct one starts active, instead of always
            // defaulting to nothing selected regardless of the real filter.
            $activeBusinessType = '';
            if (!empty($allcontactsaveadminfilter) && $allcontactsaveadminfilter->lead_type) {
                $activeBusinessType = trim(explode(',', $allcontactsaveadminfilter->lead_type)[0] ?? '');
            }

            $activeLeadStageId = '';
            if (!empty($allcontactsaveadminfilter) && $allcontactsaveadminfilter->ls_id) {
                $activeLeadStageId = trim(explode(',', $allcontactsaveadminfilter->ls_id)[0] ?? '');
            }

            // Contact Status covers several different underlying fields
            // (status / lcs_id / has_email / has_mobile) — mirror the same
            // precedence the click handler itself uses, since only one can
            // be reflected as "active" at a time.
            $activeContactCard = 'total';
            if (!empty($allcontactsaveadminfilter)) {
                $savedStatus = $allcontactsaveadminfilter->status ? explode(',', $allcontactsaveadminfilter->status) : [];
                $savedLcs = $allcontactsaveadminfilter->lcs_id ? explode(',', $allcontactsaveadminfilter->lcs_id) : [];

                if (in_array('1', $savedLcs)) {
                    $activeContactCard = 'new';
                } elseif (in_array('1', $savedStatus)) {
                    $activeContactCard = 'active';
                } elseif (in_array('0', $savedStatus)) {
                    $activeContactCard = 'inactive';
                } elseif (!empty($allcontactsaveadminfilter->has_email)) {
                    $activeContactCard = 'with_email';
                } elseif (!empty($allcontactsaveadminfilter->has_mobile)) {
                    $activeContactCard = 'with_mobile';
                }
            }
        @endphp
        <!-- Deal Stage / Recruit Status Summary Bar Start (category-wise: Contact Status / Business Type / Lead Stage — each an independent single-select group) -->
        <div class="card mb-3" id="allcontactStatusBar">
            <div class="card-body allcontact-filterbar">

                <div class="allcontact-filterbar-row">
                    <span class="allcontact-filterbar-label">Contact Status</span>
                    <div class="candidate-status-grid">

                        @php
                            $contactStatusCards = [
                                'total'          => ['title' => 'Total Contacts',    'color' => '#6C757D'],
                                'new'            => ['title' => 'New Contacts',      'color' => '#7367F0'],
                                'active'         => ['title' => 'Active Contacts',   'color' => '#28C76F'],
                                'inactive'       => ['title' => 'Inactive Contacts', 'color' => '#EA5455'],
                                'with_email'     => ['title' => 'With Email',        'color' => '#00CFE8'],
                                'with_mobile'    => ['title' => 'With Mobile',       'color' => '#FF9F43'],
                                'recently_added' => ['title' => 'Recently Added',    'color' => '#0D6EFD'],
                            ];
                        @endphp

                        @foreach($contactStatusCards as $key => $card)
                            <div class="status-item">
                                <a href="javascript:void(0)"
                                   class="status-btn all-status-card{{ $activeContactCard === $key ? ' active' : '' }}"
                                   data-type="contact"
                                   data-card="{{ $key }}"
                                   style="border-color:{{ $card['color'] }};color:{{ $card['color'] }};">
                                    <span>{{ $card['title'] }}</span>
                                    <strong id="contact_count_{{ $key }}">0</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="allcontact-filterbar-row">
                    <span class="allcontact-filterbar-label">Business Type</span>
                    <div class="candidate-status-grid">
                        @foreach($businessTypes as $type)
                            <div class="status-item">
                                <a href="javascript:void(0)"
                                   class="status-btn all-status-card{{ (string) $activeBusinessType === (string) $type ? ' active' : '' }}"
                                   data-type="business"
                                   data-value="{{ $type }}"
                                   style="border-color:#7367F0;color:#7367F0;">
                                    <span>{{ $type }}</span>
                                    <strong class="biz-count-num" data-key="{{ $type }}">0</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="allcontact-filterbar-row">
                    <span class="allcontact-filterbar-label">Lead Stage</span>
                    <div class="candidate-status-grid">
                        @foreach($leadStages as $stage)
                            <div class="status-item">
                                <a href="javascript:void(0)"
                                   class="status-btn all-status-card{{ (string) $activeLeadStageId === (string) $stage->id ? ' active' : '' }}"
                                   data-type="stage"
                                   data-value="{{ $stage->id }}"
                                   style="border-color:#00CFE8;color:#00CFE8;">
                                    <span>{{ $stage->name }}</span>
                                    <strong class="stage-count-num" data-key="{{ $stage->id }}">0</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
        <!-- Deal Stage / Recruit Status Summary Bar End -->
        @endif

        <div class="card">
            <div class="card-header border-bottom">

                <div class="px-3 float-start">
                    {{-- <a href="{{ route('admin.allcontact.updatewrongcountry') }}" class="btn btn-sm btn-primary">Update Number</a> --}}
                    <button class="btn btn-xs btn-primary filterpanel" data-bs-toggle="modal" data-bs-target="#filterpanel">
                        <i class="ti ti-filter"></i> Filter                     
                        <span class="filter-indicator d-none"></span>
                    </button>
                    {{-- <button class="btn btn-xs btn-primary" id="disableshortcode"><i class="ti ti-x"></i> Disable Short Form</button> --}}
                    {{-- <button class="btn btn-xs btn-primary d-none" id="enableshortcode"><i class="ti ti-checkbox"></i> Enable Short Form</button> --}}

                    <div class="btn-group mx-2">
                        <button class="btn btn-primary btn-sm dropdown-toggle " type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkimport"><i class="ti ti-refresh me-0 me-sm-1 ti-xs"></i> Import Contact</a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#checkMobileModal"><i class="ti ti-phone ti-xs"></i> Check Mobile</a>

                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" name="short_form_code" value="1" id="short_form_code2" class="switch-input" @if(isset($allcontactsaveadminfilter) && $allcontactsaveadminfilter->short_form_code == 1) checked @endif />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Short Form</span>
                                </label>
                            </div>

                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" name="status_bar_toggle" value="1" id="status_bar_toggle" class="switch-input" checked />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Status Bar</span>
                                </label>
                            </div>

                            @if(auth()->user()->user_type == 1)
                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#gmailAccountModal">
                                <i class="fab fa-google me-1"></i> Sync with Google Contact
                            </a>
                            @else
                            <a href="{{ route('admin.allcontact.google.redirect') }}" class="dropdown-item">
                                <i class="fab fa-google me-1"></i> Sync with Google Contact
                            </a>
                            @endif

                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#groupwisecontactreport">
                               <i class="fas fa-file me-1"></i> Group wise contact report
                            </a>
                            
                             <a href="{{ route('admin.sync_contact_to_lead') }}" class="dropdown-item">
                              <i class="fas fa-sync-alt me-1"></i> Sync Contact to Lead
                            </a>
                            
                            <a href="{{ route('admin.contact_export_history') }}" class="dropdown-item">
                              <i class="fas fa-download me-1"></i> Contact Export History
                            </a>

                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#exportEmailQamrAllContactModal">
                              <i class="fas fa-paper-plane me-1"></i> Transfer Contact to email Portal
                            </a>

                            <a href="{{ url('admin/email-qamr-portal/allcontact/export-history') }}" class="dropdown-item">
                              <i class="fas fa-history me-1"></i> email.qamr.in Export History
                            </a>

                        </div>
                    </div>

                </div>

                <div class="mb-1 float-end">
                    <div class="btn-group mx-2">

                        <button
                            class="btn btn-primary btn-sm dropdown-toggle bulkactions"
                            type="button"
                            disabled
                            id="dropdownMenuButton"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Bulk Action
                        </button>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkexport"><i class="ti ti-download"></i> Export</a>
                            @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1))
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfergroup"><i class="ti ti-users me-2"></i> Transfer To Group</a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfercareoff"><i class="ti ti-user me-2"></i> Transfer To Careoff</a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransferleadowner"><i class="ti ti-user me-2"></i> Transfer To Lead Owner</a>
                                <a class="dropdown-item bulcontactwhpsend" href="#" data-bs-toggle="offcanvas" data-bs-target="#bulkwhatsappsend"><i class="ti ti-brand-whatsapp me-2"></i> Send Whatsapp</a>
                                <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkdelete"><i class="ti ti-trash me-2"></i> Delete</a>
                                <a href="javascript:" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkcountryupdate"><i class="ti ti-flag me-2"></i> Country Update</a>
                                <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkcountrycodeupdate"><i class="ti ti-flag me-2"></i> Country Code Update</a>
                                <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkcountrycodeupdatefield"><i class="ti ti-flag me-2"></i> Country Code Update Field</a>

                            @elseif (isset($permission) && $permission->full_access == 0)

                                @if ($permission->allcontact_bulk_transfer_group == 1)
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfergroup"><i class="ti ti-users me-2"></i> Transfer To Group</a>
                                @endif

                                @if ($permission->allcontact_bulk_transfer_careoff == 1)
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfercareoff"><i class="ti ti-user me-2"></i> Transfer To Careoff</a>
                                @endif

                                @if ($permission->allcontact_bulk_transfer_leadowner == 1)
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransferleadowner"><i class="ti ti-user me-2"></i> Transfer To Lead Owner</a>
                                @endif

                                @if ($permission->allcontact_bulk_send_whatsapp == 1)
                                    <a class="dropdown-item bulcontactwhpsend" href="#" data-bs-toggle="offcanvas" data-bs-target="#bulkwhatsappsend"><i class="ti ti-brand-whatsapp me-2"></i> Send Whatsapp</a>
                                @endif

                                @if ($permission->allcontact_bulk_delete == 1)
                                    <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkdelete"><i class="ti ti-trash me-2"></i> Delete</a>
                                @endif

                                @if ($permission->allcontact_bulk_country_update == 1)
                                    <a href="javascript:" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkcountryupdate"><i class="ti ti-flag me-2"></i> Country Update</a>
                                @endif

                                @if ($permission->allcontact_bulk_country_code_update == 1)
                                    <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkcountrycodeupdate"><i class="ti ti-flag me-2"></i> Country Code Update</a>
                                @endif


                            @endif


                        </div>
                    </div>
                    @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1))
                        <button class="add-new btn btn-sm btn-primary addcontact addcandidate" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Contact</span></button>
                    @elseif (isset($permission) && $permission->full_access == 0)
                        @if ($permission->allcontact_add == 1)
                            <button class="add-new btn btn-sm btn-primary addcontact addcandidate" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Contact</span></button>
                        @endif
                    @endif
                </div>

                {{-- <div class="float-end mx-2">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#bulkimport"><i class=""></i><i class="ti ti-refresh me-0 me-sm-1 ti-xs"></i> Import</button>
                </div> --}}

                <!-- <div class="mb-1 float-end">
                    <input type="text" id="search_text" class="form-control" placeholder="Search...">
                </div> -->
            </div>
            <div class="card-datatable table-responsive contactpaginate">
                @include('admin.allcontact.load_new')
            </div>
        </div>

        <!-- Update Stage Status Start -->
        <div class="modal fade" id="editStatus" aria-hidden="true" aria-labelledby="editStatusLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
            <div class="modal-content">
                <div class="modal-header pb-2">
                <h5 class="offcanvas-title" id="editStatusLabel">Update Lifecycle Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.allcontact.getleadstageupdate') }}" method="POST" id="updateLeadStageValidation">
                    @csrf
                    <input type="hidden" name="allcontactID" id="allcontactID">
                    <input type="hidden" name="hidden_lcs_id" id="hidden_lcs_id">
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="update-lead-cycle-status" class="form-label">Life Cycle Status <span class="text-danger">*</span></label>
                                    
                                        <select name="lcs_id" id="update-lead-cycle-status" class="selectpicker w-100 dynamic-filter"  
                                        data-filter="lifecycle"
                                            data-live-search="true"
                                            data-style="default-btn"
                                         data-allow-clear="true" data-placeholder="Select Lead Cycle Status">
                                         
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

                                <div class="col-md-12">
                                    <label for="add-notes-tab" class="form-label">Notes <span class="text-danger">*</span></label>
                                    <textarea name="notes" id="add-notes-tab" cols="30" rows="5" class="form-control"></textarea>
                                </div>
                                <div class="col-md-12 mt-3">
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
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer">
                            {{-- <button class="btn btn-primary" type="submit">Update Lead Stage</button> --}}
                            <button class="btn btn-primary" type="submit" id="leadStageUpdateBtn">Update Lifecycle Status</button>
                            {{-- <button class="btn btn-primary" type="button" id="leadStageUpdateBtn">Update Lifecycle Status</button> --}}
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </div>
        <!-- Update Stage Status End -->

        <!-- Update Optin and Optout Start -->
        <div class="modal fade modal-danger text-left" id="editOptin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel123" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                    <div class="modal-header pb-2">
                        <h5 class="offcanvas-title" id="myModalLabel123">Update Opt-In</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="{{ route('admin.allcontact.optinupdate') }}" id="editOptinValidate">
                        @csrf

                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" name="id" class="form-control" id="editoptinID">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="subscribe" value="1" id="add-subscribe"/>
                                            <label class="form-check-label" for="add-subscribe">Optin</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="subscribe" value="0" id="add-unsubscribe"/>
                                            <label class="form-check-label" for="add-unsubscribe">Optout</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!-- Update Optin and Optout End -->

         <!-- Bulk Export Start -->
        <div class="modal fade" id="bulkexport" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel125">Export Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.allcontact.bulkexport') }}" method="POST" id="bulkassigntoValidation">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="bulkcontact_id" id="bulkexport_id">

                            <div id="Contactscolumns" class="row g-2">
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

        <!-- Bulk Group Transfer Start -->
        <div class="modal fade" id="bulktransfergroup" aria-hidden="true" aria-labelledby="bulktransfergroupLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulktransfergroupLabel">Transfer Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.allcontact.bulkgrouptransfer') }}" method="POST" id="bulktransfergroupvalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDGRPTR" id="contactIDGRPTR">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <p id="totlaTransferGrp" class="text-success"></p>
                                        <label for="transfer-groupm" class="form-label">Transfer Group <span class="text-danger">*</span></label>
                                        <select
                                            name="group_id"
                                            id="transfer-groupm"
                                            class="selectpicker w-100 dynamic-filter load-filter"
                                            data-filter="group"
                                            data-live-search="true"
                                            data-style="default-btn"
                                            title="Select Group">

                                            <option value="">

                                            Select Group

                                            </option>

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

        <!-- Bulk Country Code Update Start -->
        <div class="modal fade" id="bulkcountryupdate" aria-hidden="true" aria-labelledby="bulkcountryupdateLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulkcountryupdateLabel">Update Country</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.allcontact.countryupdt.querycountry') }}" method="POST" id="bulkcountryupdatevalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDGRPTRC" id="contactIDGRPTRC">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="add-transfer-country-query" class="form-label">Select Country Query <span>*</span></label>
                                        <select name="country_query" id="add-transfer-country-query" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                            <option value="">Select</option>
                                            <option value="India">India</option>
                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="add-transfer-country-update" class="form-label">Select Country <span class="text-danger">*</span></label>
                                        <select
                                            name="country_id"
                                            id="add-transfer-country-update"
                                            class="selectpicker w-100 dynamic-filter load-filter"
                                            data-filter="country"
                                            data-live-search="true"
                                            data-style="default-btn"
                                            title="Select Country">

                                            <option value="">

                                            Select Country

                                            </option>

                                            </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Update</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Country Code Update End -->

        <!-- Bulk Country Code Updated From Bulk Start -->
        <div class="modal fade" id="bulkcountrycodeupdate" aria-hidden="true" aria-labelledby="bulkcountrycodeupdateLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulkcountrycodeupdateLabel">Update Country Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.allcontact.bulkupdatecountrycode') }}" method="POST" id="bulkcountrycodeupdatevalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDGRPTRC2" id="contactIDGRPTRC2">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <p id="getListBulkUpdate" class="text-primary"></p>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Update</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Country Code Updated From Bulk Start -->

        <!-- Bulk Country Code Update Field From Bulk Start -->
        <div class="modal fade" id="bulkcountrycodeupdatefield" aria-hidden="true" aria-labelledby="bulkcountrycodeupdatefieldLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulkcountrycodeupdatefieldLabel">Update Country Code Field</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.allcontact.bulkupdatecountrycodefield') }}" method="POST" id="bulkcountrycodeupdatefieldvalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDGRPTRC25" id="contactIDGRPTRC25">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <p id="getListBulkUpdate25" class="text-primary"></p>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Update</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Country Code Update Field From Bulk End -->

        <!-- Bulk Send Whatsapp Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="bulkwhatsappsend" aria-labelledby="bulkwhatsappsendLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="bulkwhatsappsendLabel" class="offcanvas-title">Send Bulk Whatsapp</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="bulksendwhatsapp" action="{{ route('admin.allcontact.bulksendwhatsapp') }}" method="POST" >
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
                        {{-- 04-01-2024 <div class="col-md-4 disallforwhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-contact-status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="contact_status[]" id="send-meta-template-contact-status" class="form-select select22" multiple data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div> --}}
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
                                <select
                                    name="contact_group_id[]"
                                    id="send-meta-template-contact-group"
                                    multiple
                                    class="selectpicker w-100 dynamic-filter load-filter"
                                    data-filter="group"
                                    data-live-search="true"
                                    data-actions-box="true"
                                    data-style="default-btn"
                                    title="Select Group">

                                    <option value="">

                                    Select Group

                                    </option>

                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 disnormalwhatsapp">
                            <div class="mb-3">
                                <label for="send-template-personalise-class" class="form-label">Personalise Name</label>
                                <select name="personalise_class" id="send-template-personalise-class" class="form-select select22" data-allow-clear="true" data-placeholder="Select Personalise Class">
                                    <option value=""></option>
                                    <option value="[Company]">Company</option>
                                    <option value="[Business Type]">Business Type</option>
                                    <option value="[Full Name]">Full Name</option>
                                    <option value="[Email]">Email</option>
                                    <option value="[Country]">Country</option>
                                    <option value="[City]">City</option>
                                    <option value="[Phone0]">Phone0</option>
                                    <option value="[Email0]">Email0</option>
                                    <option value="[Phone1]">Phone1</option>
                                    <option value="[Email1]">Email1</option>
                                    <option value="[Phone2]">Phone2</option>
                                    <option value="[Email2]">Email2</option>
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

                <form action="{{ route('admin.allcontact.bulkleadownertransfer') }}" method="POST" id="bulktransferleadownevalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDLTR" id="contactIDLTRB">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="transfer-lead-owner" class="form-label">Transfer Lead Owner <span class="text-danger">*</span></label>
                                        <select
                                        name="leadowner_id"
                                        id="transfer-lead-owner"
                                        class="selectpicker
                                        w-100
                                        dynamic-filter
                                        load-filter"
                                        data-filter="lead_owner"
                                        data-live-search="true"
                                        data-style="default-btn"
                                        title="Select Lead Owner">

                                        <option value="">

                                        Select Lead Owner

                                        </option>

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

                <form action="{{ route('admin.allcontact.bulkcareofftransfer') }}" method="POST" id="bulktransfercareoffvalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDCTR" id="contactIDCTR">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="transfer-careoff" class="form-label">Transfer Careoff <span class="text-danger">*</span></label>
                                        <select
                                            name="careoff_id"
                                            id="transfer-careoff"
                                            class="selectpicker
                                            w-100
                                            dynamic-filter
                                            load-filter"
                                            data-filter="careoff"
                                            data-live-search="true"
                                            data-style="default-btn"
                                            title="Select Careoff">

                                            <option value="">

                                            Select Careoff

                                            </option>

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

        <!-- Edit Contact Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="updateReg" aria-labelledby="updateRegLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="updateRegLabel" class="offcanvas-title">Edit Contact</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form action="{{ route('admin.allcontact.update') }}" id="editContactForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="editID" id="edit_ID">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="lead_type" class="form-label">Business Type</label>
                                <select name="lead_type" id="lead_type" class="form-control select2e lead_type" data-allow-clear="true" data-placeholder="Select Business Type">
                                    <option value="">Select</option>
                                    <option value="Unknown">Other</option>
                                    <option value="Job seeker">Job seeker</option>
                                    <option value="Regular Wakala Party">Regular Wakala Party</option>
                                    {{-- <option value="Direct Wakala Candidate">Direct Wakala Candidate</option> --}}
                                    <option value="Agent without office">Agent without office </option>
                                    <option value="Associate with office">Associate with office</option>
                                    <option value="Company">Company</option>
                                    <option value="HIRING US">HIRING US</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-lcs-id" class="form-label">Life Cycle Status</label>
                                <select
                                    name="lcs_id"
                                    id="edit-lcs-id"
                                    class="selectpicker w-100 dynamic-filter load-filter-edit"
                                    data-filter="lifecycle"
                                    data-live-search="true"
                                    data-style="default-btn"
                                    title="Select Life Cycle Status">

                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-ls-id" class="form-label">Lead Stage</label>
                                <select
                                    name="ls_id"
                                    id="edit-ls-id"
                                    class="selectpicker w-100 dynamic-filter load-filter-edit"
                                    data-filter="lead_stage"
                                    data-live-search="true"
                                    data-style="default-btn"
                                    title="Select Lead Stage">

                                </select>
                            </div>
                        </div>

                        <div class="col-md-4" id="full_name_div">
                            <div class="mb-3">
                                <label for="edit-full-name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" id="edit-full-name" placeholder="Please enter full name" class="form-control" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-4" id="job_title_div">
                            <div class="mb-3">
                                <label for="">Job Title</label>
                                <input type="text" name="job_title" id="job_title" class="form-control" placeholder="Enter Job Title">

                                <div class="job_title_list" id="job_title_list">

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4" id="job_desg_div">
                            <div class="mb-3">
                                <label for="">Designation</label>
                                <input type="text" name="job_desg" id="job_desg" class="form-control" placeholder="Enter Designation">
                                <div id="job_desg_list" class="job_desg_list">

                                </div>
                            </div>
                        </div>

                        <div class="col-md-6" id="company_name_div">
                            <div class="mb-3">
                                <label for="edit-company-name">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control" id="edit-company-name" placeholder="Enter Company Name">
                            </div>
                        </div>

                        <div class="col-md-4" id="indust_id_div">
                            <div class="mb-3">
                                <label for="edit-industry-id">Industry <span class="text-danger">*</span></label>
                                <select
                                    name="indust_id"
                                    id="edit-industry-id"
                                    class="selectpicker w-100 dynamic-filter load-filter-edit"
                                    data-filter="industry"
                                    data-live-search="true"
                                    data-style="default-btn"
                                    title="Select Industry">


                                </select>
                            </div>
                        </div>

                        <div class="col-md-4" id="office_name_div">
                            <div class="mb-3">
                                <label for="edit-agency-name">Agency Name <span class="text-danger">*</span></label>
                                <input type="text" name="office_name" id="edit-agency-name" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-mobile-no" class="form-label">Primary Number (Whatsapp Number)
                                <i class="ti ti-info-circle text-primary ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Number should be entered with country code, without leading 0 or + sign. Example: 919827063409">
                                    </i>
                                </label>
                                <input type="text" name="primary_no_wsp" id="edit-mobile-no" class="form-control edit-mobile-no">
                                <input type="hidden" name="primary_no_wsp_dial_code" class="mobile-no-dial-code-ed" id="mobile-no-dial-code-ed">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-primary-no" class="form-label">Secondary Number 
                                <i class="ti ti-info-circle text-primary ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Number should be entered with country code, without leading 0 or + sign. Example: 919827063409">
                                    </i>
                                </label>
                                <input type="text" name="secondary_no_wsp" id="edit-primary-no" class="form-control edit-primary-no">
                                <input type="hidden" name="secondary_no_wsp_dial_code" class="primary-con-dial-code-ed" id="primary-con-dial-code-ed">
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-phone0" class="form-label">Mobile No.01
                                <i class="ti ti-info-circle text-primary ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Number should be entered with country code, without leading 0 or + sign. Example: 919827063409">
                                    </i>
                                </label>
                                <input type="text" name="mobile_no1_wsp" id="edit-phone0" class="form-control edit-phone0">
                                <input type="hidden" name="mobile_no1_wsp_dial_code" class="phone0-dial-code-ed" id="phone0-dial-code-ed">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-phone1" class="form-label">Mobile No.02
                                <i class="ti ti-info-circle text-primary ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Number should be entered with country code, without leading 0 or + sign. Example: 919827063409">
                                    </i>
                                </label>
                                <input type="text" name="mobile_no2_wsp" id="edit-phone1" class="form-control edit-phone1">
                                <input type="hidden" name="mobile_no2_wsp_dial_code" class="phone1-dial-code-ed" id="phone1-dial-code-ed">
                            </div>
                        </div>

                        {{-- <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-phone2" class="form-label">Mobile No.04</label>
                                <input type="text" name="mobile_no3_wsp" id="edit-phone2" class="form-control edit-phone2">
                                <input type="hidden" name="mobile_no3_wsp_dial_code" class="phone2-dial-code-ed" id="phone2-dial-code-ed">
                            </div>
                        </div> --}}

                        <div class="col-md-4">
                            <div class="mb-3">

                            <label for="edit-email" class="form-label">Email 1</label>
                            <input type="text" name="email" id="edit-email" class="form-control">
                        </div>

                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-email0" class="form-label">Email 2</label>
                                <input type="text" name="email0" id="edit-email0" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-email1">Email 3</label>
                                <input type="text" name="email1" id="edit-email1" class="form-control">
                            </div>
                        </div>

                        {{-- <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-email2">Email 4</label>
                                <input type="text" name="email2" id="edit-email2" class="form-control">
                            </div>
                        </div> --}}



                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="edit-country-id">Country</label>
                                <select
                                    name="country_id"
                                    id="edit-country-id"
                                    class="selectpicker w-100 dynamic-filter load-filter-edit"
                                    data-filter="country"
                                    data-live-search="true"
                                    data-style="default-btn"
                                    title="Select Country">


                                </select>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="edit-city-id">City</label>

                                <select
                                name="city_id"
                                id="edit-city-id"
                                class="selectpicker w-100 dynamic-filter load-filter-edit"
                                data-filter="city"
                                data-live-search="true"
                                data-style="default-btn"
                                title="Select City">

                                </select>

                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-3">

                                <label for="edit-careoff" class="form-label">Careoff<span class="text-danger">*</span></label>

                                <select name="careoff_id" id="edit-careoff" class="selectpicker w-100 dynamic-filter load-filter-edit" data-filter="careoff" data-live-search="true" data-style="default-btn"
                                title="Select Careoff">


                                </select>

                            </div>
                        </div>


                        @if(Auth::guard('admin')->user()->user_type==1)

                        <div class="col-md-4">

                        <div class="mb-3">

                        <label
                        for="edit-leadowner"
                        class="form-label">

                        Lead Owner

                        <span class="text-danger">

                        *

                        </span>

                        </label>

                        <select
                        name="leadowner_id"
                        id="edit-leadowner"
                        class="selectpicker w-100 dynamic-filter load-filter-edit"
                        data-filter="lead_owner"
                        data-live-search="true"
                        data-style="default-btn"
                        title="Select Lead Owner">


                        </select>

                        </div>

                        </div>

                        @endif


                        <div class="col-md-4">

                        <div class="mb-3">

                        <label
                        for="edit-group-id"
                        class="form-label">

                        Group

                        </label>

                        <select
                        name="group_id"
                        id="edit-group-id"
                        class="selectpicker w-100 dynamic-filter load-filter-edit"
                        data-filter="group"
                        data-live-search="true"
                        data-style="default-btn"
                        title="Select Group">



                        </select>

                        </div>

                        </div>


                        <div class="col-md-4">

                        <div class="mb-3">

                        <label
                        class="form-label">

                        Source

                        </label>

                        <select
                        name="edit_source"
                        id="edit_source"
                        class="selectpicker w-100 dynamic-filter load-filter-edit"
                        data-filter="source"
                        data-live-search="true"
                        data-style="default-btn"
                        title="Select Source">


                        </select>

                        </div>

                        </div>

                        <div class="col-md-12" id="descr_div">
                            <div class="mb-3">
                                <label for="edit-descr">Description</label>
                                <textarea name="descr" id="edit-descr" class="form-control" cols="30" rows="5"></textarea>
                            </div>

                        </div>

                    </div>
                    {{-- <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editnewcontact">Submit</button> --}}
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}

                    <button type="submit" id="submitBtn" class="btn btn-primary">
                        Submit
                    </button>
                    <button type="reset" class="btn btn-label-secondary contact-edit-close-btn" data-bs-dismiss="offcanvas">Cancel</button>

                </form>
            </div>
        </div>
        <!-- Edit Contact End -->

        <!-- Add Contact Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Contact</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form action="{{ route('admin.allcontact.store') }}" id="addUserForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add_lead_type" class="form-label">Business Type</label>
                                <select name="lead_type" id="add_lead_type" class="form-control select2e lead_type" data-allow-clear="true" data-placeholder="Select Business Type">
                                    <option value="">Select</option>
                                    <option value="Unknown">Other</option>
                                    <option value="Job seeker" selected>Job seeker</option>
                                    <option value="Regular Wakala Party">Regular Wakala Party</option>
                                    {{-- <option value="Direct Wakala Candidate">Direct Wakala Candidate</option> --}}
                                    <option value="Agent without office">Agent without office </option>
                                    <option value="Associate with office">Associate with office</option>
                                    <option value="Company">Company</option>
                                    <option value="Trade site Center">Trade site Center</option>
                                    <option value="HIRING US">HIRING US</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">

                            <div class="mb-3">

                                <label for="add-lcs-id" class="form-label">Life Cycle Status</label>

                                <select
                                name="lcs_id"
                                id="add-lcs-id"
                                class="selectpicker
                                w-100
                                dynamic-filter
                                load-filter"
                                data-filter="lifecycle"
                                data-default="1"
                                data-live-search="true"
                                data-style="default-btn"
                                title="Select Life Cycle Status">

                                <option value="">

                                Select

                                </option>

                                </select>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="mb-3">

                            <label
                            for="add-ls-id"
                            class="form-label">

                            Lead Stage

                            </label>

                            <select
                            name="ls_id"
                            id="add-ls-id"
                            class="selectpicker
                            w-100
                            dynamic-filter
                            load-filter"
                            data-filter="lead_stage"
                            data-default="1"
                            data-live-search="true"
                            data-style="default-btn"
                            title="Select Lead Stage">

                            <option value="">

                            Select

                            </option>

                            </select>

                            </div>

                        </div>

                        <div class="col-md-4" id="full_name_div_add">
                            <div class="mb-3">
                                <label for="add-full-name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" id="add-full-name" placeholder="Please enter full name" class="form-control" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-4" id="job_title_div_add">
                            <div class="mb-3">
                                <label for="add_job_title" class="form-label">Job Title</label>
                                <input type="text" name="job_title" id="add_job_title" class="form-control" placeholder="Enter Job Title">

                                <div class="job_title_list" id="job_title_list">

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4" id="job_desg_div_add" style="display: none">
                            <div class="mb-3">
                                <label for="add_job_desg" class="form-label">Designation</label>
                                <input type="text" name="job_desg" id="add_job_desg" class="form-control" placeholder="Enter Designation">
                                <div id="job_desg_list" class="job_desg_list">

                                </div>
                            </div>
                        </div>

                        <div class="col-md-6" id="company_name_div_add" style="display: none">
                            <div class="mb-3">
                                <label for="add-company-name">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control" id="add-company-name" placeholder="Enter Company Name">
                            </div>
                        </div>

                        <div class="col-md-4" id="indust_id_div_add" style="display: none">

                            <div class="mb-3">
                                <label for="add-industry-id">Industry <span class="text-danger">*</span></label>
                                <select
                                    name="indust_id"
                                    id="add-industry-id"
                                    class="selectpicker
                                    w-100
                                    dynamic-filter
                                    load-filter"
                                    data-filter="industry"
                                    data-live-search="true"
                                    data-style="default-btn"
                                    title="Select Industry">

                                    <option value="">

                                    Select

                                    </option>

                                </select>
                            </div>
                        </div>

                        <div class="col-md-4" id="office_name_div_add" style="display: none">
                            <div class="mb-3">
                                <label for="add-agency-name">Agency Name <span class="text-danger">*</span></label>
                                <input type="text" name="office_name" id="add-agency-name" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-mobile-no" class="form-label">
                                    Primary Number (Whatsapp Number)

                                    <i class="ti ti-info-circle text-primary ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Number should be entered with country code, without leading 0 or + sign. Example: 919827063409">
                                    </i>
                                </label>                                
                                <input type="text" name="primary_no_wsp" id="add-mobile-no" class="form-control add-mobile-no">
                                <input type="hidden" name="primary_no_wsp_dial_code" id="mobile-no-dial-code">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-primary-no" class="form-label">Secondary Number
                                <i class="ti ti-info-circle text-primary ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Number should be entered with country code, without leading 0 or + sign. Example: 919827063409">
                                    </i>
                                </label>
                                <input type="text" name="secondary_no_wsp" id="add-primary-no" class="form-control add-primary-no">
                                <input type="hidden" name="secondary_no_wsp_dial_code" id="primary-con-dial-code">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-phone0" class="form-label">Mobile No.01
                                    <i class="ti ti-info-circle text-primary ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Number should be entered with country code, without leading 0 or + sign. Example: 919827063409">
                                    </i>
                                </label>
                                <input type="text" name="mobile_no1_wsp" id="add-phone0" class="form-control add-phone0">
                                <input type="hidden" name="mobile_no1_wsp_dial_code" id="phone0-dial-code">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-phone1" class="form-label">Mobile No.02
                                    <i class="ti ti-info-circle text-primary ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Number should be entered with country code, without leading 0 or + sign. Example: 919827063409">
                                    </i>
                                </label>
                                <input type="text" name="mobile_no2_wsp" id="add-phone1" class="form-control add-phone1">
                                <input type="hidden" name="mobile_no2_wsp_dial_code" id="phone1-dial-code">

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">

                            <label for="add-email" class="form-label">Email 1</label>
                            <input type="text" name="email" id="add-email" class="form-control">
                        </div>

                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-email0" class="form-label">Email 2</label>
                                <input type="text" name="email0" id="add-email0" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-email1" class="form-label">Email 3</label>
                                <input type="text" name="email1" id="add-email1" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">

                                <label
                                class="form-label"
                                for="add-country-id">

                                Country

                                </label>

                                <select
                                name="country_id"
                                id="add-country-id"
                                class="selectpicker
                                w-100
                                dynamic-filter
                                load-filter"
                                data-filter="country"
                                data-live-search="true"
                                data-style="default-btn"
                                title="Select Country">

                                    <option value="">

                                        Select

                                    </option>

                                </select>

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">

                                <label
                                class="form-label"
                                for="add-city-id">

                                City

                                </label>

                                <select
                                name="city_id"
                                id="add-city-id"
                                class="selectpicker
                                w-100
                                dynamic-filter
                                load-filter"
                                data-filter="city"
                                data-live-search="true"
                                data-style="default-btn"
                                title="Select City">

                                <option value="">

                                Select

                                </option>

                                </select>

                            </div>
                        </div>

                        <div class="col-md-4">

                            <div class="mb-3">

                                <label
                                for="add-careoff"
                                class="form-label">

                                Careoff

                                <span class="text-danger">

                                *

                                </span>

                                </label>

                                <select
                                name="careoff_id"
                                id="add-careoff"
                                class="selectpicker
                                w-100
                                dynamic-filter
                                load-filter"
                                data-filter="careoff"
                                data-default="{{ Auth::guard('admin')->id() }}"
                                data-live-search="true"
                                data-style="default-btn"
                                title="Select Careoff">

                                <option value="">

                                Select

                                </option>

                                </select>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="mb-3">

                                <label
                                for="add-leadowner"
                                class="form-label">

                                Lead Owner

                                <span class="text-danger">

                                *

                                </span>

                                </label>

                                <select
                                name="leadowner_id"
                                id="add-leadowner"
                                class="selectpicker
                                w-100
                                dynamic-filter
                                load-filter"
                                data-filter="lead_owner"
                                data-default="{{ Auth::guard('admin')->id() }}"
                                data-live-search="true"
                                data-style="default-btn"
                                title="Select Lead Owner">

                                <option value="">

                                Select

                                </option>

                                </select>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="mb-3">

                                <label class="form-label">Source</label>

                                <select
                                name="ins_source"
                                id="ins_source"
                                class="selectpicker
                                w-100
                                dynamic-filter
                                load-filter"
                                data-filter="source"
                                data-live-search="true"
                                data-style="default-btn"
                                title="Select Source">

                                <option value="">

                                Select Source

                                </option>

                                </select>

                            </div>

                        </div>
                        
                        <div class="col-md-12">
                            <div class="col-md-12" id="descr_div">
                                <div class="mb-3">
                                    <label for="add-descr" class="form-label">Description</label>
                                    <textarea name="descr" id="add-descr" class="form-control" cols="30" rows="5"></textarea>
                                </div>

                            </div>

                            <div class="col-md-12">
                                <div class="mb-3 mt-3">
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" name="send_whatsapp" type="checkbox" id="add-send-whatsapp" value="1" />
                                        <label class="form-check-label" for="add-send-whatsapp">Send Msg</label>
                                    </div>
                                </div>
                            </div>

                        </div>

                    
                    </div>

                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>



                </form>
            </div>
        </div>
        <!-- Add Contact End -->

        <!-- Export to email.qamr.in Portal Start -->
        <div class="modal fade" id="exportEmailQamrAllContactModal" aria-hidden="true" aria-labelledby="exportEmailQamrAllContactModalLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="exportEmailQamrAllContactModalLabel">Transfer Contact to email Portal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="eqpac_alert" class="alert d-none" role="alert"></div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="eqpac_api_token">API Token <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="eqpac_api_token" class="form-control" placeholder="Enter email.qamr.in API token" autocomplete="off" value="{{ optional(Auth::guard('admin')->user())->email_qamr_api_token }}">
                                    <button type="button" id="eqpac_token_toggle_btn" class="btn btn-outline-secondary" title="View token"><i class="ti ti-eye"></i></button>
                                    <button type="button" id="eqpac_token_save_btn" class="btn btn-outline-primary">Save</button>
                                </div>
                                <small id="eqpac_token_status" class="text-muted"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="eqpac_business_type">Business Type <span class="text-danger">*</span></label>
                                <select id="eqpac_business_type" class="form-select" required>
                                    <option value="">Select Business Type</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="eqpac_industries">Industry</label>
                                <select id="eqpac_industries" class="form-select" multiple>
                                </select>
                            </div>

                            <div class="col-md-12 mb-2">
                                <label class="form-label" for="eqpac_email_list">Email List <span class="text-danger">*</span></label>
                                <select id="eqpac_email_list" class="form-select" disabled>
                                    <option value="">Enter API token first</option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3 mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Custom Field Mapping</label>
                                    <button type="button" id="eqpac_add_mapping_btn" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-plus me-1"></i>Add Custom Field
                                    </button>
                                </div>
                                <div id="eqpac_mapping_rows"></div>
                                <small class="text-muted">Map All Contact fields to Email Portal custom fields (optional). Select an Email List first.</small>
                            </div>

                            <div class="col-md-12">
                                <strong>Selected Contact Count: <span id="eqpac_selected_count">0</span></strong>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="eqpac_refresh_btn" class="btn btn-outline-secondary me-auto" title="Refresh"><i class="ti ti-refresh me-1"></i>Refresh</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="eqpac_export_btn" class="btn btn-primary">Export</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Export to email.qamr.in Portal End -->

        @php
            $customFilterColumns = collect(\Illuminate\Support\Facades\Schema::getColumnListing((new \App\Models\Allcontact())->getTable()))
                ->map(fn($col) => ['value' => $col, 'label' => ucwords(str_replace('_', ' ', $col))])
                ->values();
        @endphp

        <!-- Filter Panel Start -->
        <div class="modal fade" id="filterpanel" aria-hidden="true" aria-labelledby="filterpanelLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="filterpanelLabel">Allcontact Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                            <select
                                id="by-business-type"
                                multiple
                                class="selectpicker w-100 dynamic-filter load-filter-filterpanel"
                                data-filter="business_type"
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select Business Type">
                                @foreach($businessTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select
                                id="by-careoff-id"
                                multiple
                                class="selectpicker w-100 dynamic-filter load-filter-filterpanel"
                                data-filter="careoff"
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select Careoff">
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select
                                id="by-create-by"
                                multiple
                                class="selectpicker w-100 dynamic-filter load-filter-filterpanel"
                                data-filter="created_by"
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select Create By">
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select
                                id="by-groupby-id"
                                multiple
                                class="selectpicker w-100 dynamic-filter load-filter-filterpanel"
                                data-filter="group"
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select Group">
                                
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">

                                <select
                                id="by-lifecycle-status"
                                multiple
                                class="selectpicker w-100 dynamic-filter load-filter-filterpanel"
                                data-filter="lifecycle"
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select Lifecycle Status">
                                </select>

                            </div>

                            <div class="col-md-4 mb-3">
                                <select
                                id="by-lead-stage"
                                multiple
                                class="selectpicker w-100 dynamic-filter load-filter-filterpanel"
                                data-filter="lead_stage"
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select Lead Stage">
                                @foreach($leadStages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select
                                id="by-lead-priority"
                                multiple
                                class="selectpicker w-100 dynamic-filter load-filter-filterpanel"
                                data-filter="lead_priority"
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select Lead Priority">
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">

                                <select
                                id="by-industry"
                                multiple
                                class="selectpicker w-100 dynamic-filter load-filter-filterpanel"
                                data-filter="industry"
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select Industry">
                                </select>

                            </div>


                            <div class="col-md-4 mb-3">

                                <select
                                id="by-state-id"
                                multiple
                                class="selectpicker w-100 dynamic-filter load-filter-filterpanel"
                                data-filter="state"
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select State">
                                </select>

                            </div>
                            <div class="col-md-4 mb-3">
                                <select
                                id="by-country-id"
                                multiple
                                class="selectpicker w-100 dynamic-filter load-filter-filterpanel"
                                data-filter="country"
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select Country">
                                </select>
                            </div>

                            @php
                                $conversationTypes = isset($allcontactsaveadminfilter) && $allcontactsaveadminfilter->conversation_type
                                    ? explode(',', $allcontactsaveadminfilter->conversation_type)
                                    : [];

                            @endphp


                            <div class="col-md-4 mb-3">
                                <select id="by-conversation-type" multiple class="selectpicker w-100 load-filter-filterpanel" data-live-search="true" data-actions-box="true" data-style="default-btn" title="Conversation Type">
                                    <option value="Normal" {{ in_array('Normal', $conversationTypes) ? 'selected' : '' }}>Normal</option>
                                    <option value="Call" {{ in_array('Call', $conversationTypes) ? 'selected' : '' }}>Call</option>
                                    <option value="Whatsapp" {{ in_array('Whatsapp', $conversationTypes) ? 'selected' : '' }}>Whatsapp</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select name="followup_before" id="by-followup-before"
                                    class="selectpicker w-100"
                                    data-actions-box="true"
                                    data-live-search="true"
                                    data-style="default-btn"
                                    title="Select followup before">
                                    <option value="">Select followup before</option>
                                    <option value="1" {{ ($allcontactsaveadminfilter->followup_before ?? '') == 1 ? 'selected' : '' }}>Today</option>
                                    <option value="3" {{ ($allcontactsaveadminfilter->followup_before ?? '') == 3 ? 'selected' : '' }}>3 Days</option>
                                    <option value="7" {{ ($allcontactsaveadminfilter->followup_before ?? '') == 7 ? 'selected' : '' }}>7 Days</option>
                                    <option value="14" {{ ($allcontactsaveadminfilter->followup_before ?? '') == 14 ? 'selected' : '' }}>14 Days</option>
                                    <option value="30" {{ ($allcontactsaveadminfilter->followup_before ?? '') == 30 ? 'selected' : '' }}>1 Month</option>

                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <input name="by_created_date" type="text" id="by-created-date" @if(isset($allcontactsaveadminfilter)) value="{{ $allcontactsaveadminfilter->by_created_date }}" @endif class="form-control bsdatpicket" placeholder="Created Date...">
                            </div>

                            <div class="col-md-4 mb-3">
                                <input name="by_updated_date" type="text" id="by-updated-date" @if(isset($allcontactsaveadminfilter)) value="{{ $allcontactsaveadminfilter->by_updated_date }}" @endif class="form-control bsdatpicket" placeholder="Updated Date...">
                            </div>

                            <div class="col-md-4 mb-3">
                                <input name="by_staff_updated_date" type="text" id="by-staff-updated-date" @if(isset($allcontactsaveadminfilter)) value="{{ $allcontactsaveadminfilter->by_staff_updated_date }}" @endif class="form-control bsdatpicket" placeholder="Staff Updated Date...">
                            </div>

                            <div class="col-md-4 mb-3">

                                <select
                                id="by-city-id"
                                multiple
                                class="selectpicker w-100 dynamic-filter load-filter-filterpanel"
                                data-filter="city"
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select City">
                                </select>

                            </div>


                            @if (Auth::guard('admin')->user()->user_type == 1)
                            <div class="col-md-4 mb-3">
                                <select
                                    id="by-lead-owner"
                                    multiple
                                    class="selectpicker w-100 dynamic-filter load-filter-filterpanel"
                                    data-filter="lead_owner"
                                    data-live-search="true"
                                    data-actions-box="true"
                                    data-style="default-btn"
                                    title="Select Lead Owner">
                                    </select>
                            </div>
                            @endif

                            <div class="col-md-4 mb-3">
                                <select id="by-coutry-dial-code"
                                    class="selectpicker w-100"
                                    multiple
                                    data-live-search="true"
                                    data-actions-box="true"
                                    data-style="default-btn"
                                    title="Select Country">

                                    <option value="91"
                                        {{ isset($allcontactsaveadminfilter) && in_array('91', array_map('trim', explode(',', $allcontactsaveadminfilter->country_dial_code ?? ''))) ? 'selected' : '' }}>
                                        India (+91)
                                    </option>

                                    <option value="966"
                                        {{ isset($allcontactsaveadminfilter) && in_array('966', array_map('trim', explode(',', $allcontactsaveadminfilter->country_dial_code ?? ''))) ? 'selected' : '' }}>
                                        Saudi Arabia (+966)
                                    </option>
                                </select>
                            </div>


                            <div class="col-md-4 mb-3">
                                <select id="by-coutry-dial-code-number"
                                    class="selectpicker w-100"
                                    multiple
                                    data-live-search="true"
                                    data-actions-box="true"
                                    data-style="default-btn"
                                    title="Select Country Mobile Code">

                                    <option value="91"
                                        {{ isset($allcontactsaveadminfilter) && in_array('91', array_map('trim', explode(',', $allcontactsaveadminfilter->country_dial_code_number ?? ''))) ? 'selected' : '' }}>
                                        India (+91)
                                    </option>

                                    <option value="966"
                                        {{ isset($allcontactsaveadminfilter) && in_array('966', array_map('trim', explode(',', $allcontactsaveadminfilter->country_dial_code_number ?? ''))) ? 'selected' : '' }}>
                                        Saudi Arabia (+966)
                                    </option>
                                </select>
                            </div>


                            <div class="col-md-4 mb-3">
                                <select id="by-exlude-coutry-mobile-code"
                                    class="selectpicker w-100"
                                    multiple
                                    data-live-search="true"
                                    data-actions-box="true"
                                    data-style="default-btn"
                                    title="Select Exclude Country Mobile Code">

                                    <option value="91"
                                        {{ isset($allcontactsaveadminfilter) && in_array('91', array_map('trim', explode(',', $allcontactsaveadminfilter->exlude_country_mobile_code ?? ''))) ? 'selected' : '' }}>
                                        India (+91)
                                    </option>

                                    <option value="966"
                                        {{ isset($allcontactsaveadminfilter) && in_array('966', array_map('trim', explode(',', $allcontactsaveadminfilter->exlude_country_mobile_code ?? ''))) ? 'selected' : '' }}>
                                        Saudi Arabia (+966)
                                    </option>
                                </select>
                            </div>

                            @php
                                $contactStatusFilter = !empty($allcontactsaveadminfilter?->status)
                                    ? array_map('trim', explode(',', $allcontactsaveadminfilter->status))
                                    : [];
                            @endphp

                            <div class="col-md-4 mb-3">
                                <select
                                id="by-contact-status"
                                multiple
                                class="selectpicker w-100 load-filter-filterpanel"
                                data-live-search="false"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select Contact Status">
                                    <option value="1" {{ in_array('1', $contactStatusFilter) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ in_array('0', $contactStatusFilter) ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select
                                id="by-source-id"
                                multiple
                                class="selectpicker w-100 dynamic-filter load-filter-filterpanel"
                                data-filter="source"
                                data-live-search="true"
                                data-actions-box="true"
                                data-style="default-btn"
                                title="Select Source">
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select name="has_email" id="by-has-email"
                                    class="selectpicker w-100"
                                    data-actions-box="true"
                                    data-style="default-btn"
                                    title="Select email availability">
                                    <option value="">Email Available?</option>
                                    <option value="1" {{ ($allcontactsaveadminfilter->has_email ?? '') == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ ($allcontactsaveadminfilter->has_email ?? '') == '0' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select name="has_mobile" id="by-has-mobile"
                                    class="selectpicker w-100"
                                    data-actions-box="true"
                                    data-style="default-btn"
                                    title="Select mobile availability">
                                    <option value="">Mobile Available?</option>
                                    <option value="1" {{ ($allcontactsaveadminfilter->has_mobile ?? '') == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ ($allcontactsaveadminfilter->has_mobile ?? '') == '0' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>

                            <input type="hidden" id="by-recently-added" value="">

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
                        <button type="button" class="btn btn-warning btn-sm resetfilter">Reset Filter</button>
                        <button type="button" class="btn btn-success btn-sm savetodoFilter">Save Filter</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filter Panel End -->

        <!-- Bulk Delete Start -->
        <div class="modal fade" id="bulkdelete" aria-hidden="true" aria-labelledby="bulkdeleteLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Delete Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.allcontact.bulk.delete') }}" method="POST" enctype="multipart/form-data">
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

        <!-- Delete Contact Staff Start -->
        <div class="modal fade" id="deletescon" aria-hidden="true" aria-labelledby="deletesconLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Delete Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.allcontact.delete') }}" method="POST" enctype="multipart/form-data">
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
        <!-- Delete Contact Staff End -->

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


                                <!-- 🔽 Dropdown Section (first 3 replaced with real DB-driven ones) -->
                                    <div class="col-md-4 6_dropdowns" style="display:none;">
                                        <div class="mb-3">
                                            <label for="bulk_lead_type" class="form-label">Business Type<span class="text-danger">*</span></label>
                                            <select name="lead_type" id="bulk_lead_type" class="form-control select2e lead_type" data-allow-clear="true" data-placeholder="Select Business Type">
                                                <option value="">Select</option>
                                                <option value="Unknown">Other</option>
                                                <option value="Job seeker">Job seeker</option>
                                                <option value="Direct Wakala Candidate">Direct Wakala Candidate</option>
                                                <option value="Agent without office">Agent without office </option>
                                                <option value="Associate with office">Associate with office</option>
                                                <option value="Company">Company</option>
                                                <option value="Trade site Center">Trade site Center</option>
                                                <option value="HIRING US">HIRING US</option>
                                            </select>
                                            <span class="text-danger error-display-business-type" style="display:none;"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4 6_dropdowns" style="display:none;">
                                        <div class="mb-3">
                                            <label for="bulk-lcs-id" class="form-label">Life Cycle Status</label>
                                            <select name="lcs_id" id="bulk-lcs-id" class="selectpicker w-100 dynamic-filter load-filter" data-filter="lifecycle"
                                                    data-live-search="true" data-actions-box="false" data-style="default-btn" title="Select Life Cycle Status">

                                                        <option value="not_required">Not Required</option>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 6_dropdowns" style="display:none;">

                                        <div class="mb-3">

                                        <label
                                        for="bulk-ls-id"
                                        class="form-label">

                                        Lead Stage

                                        </label>

                                        <select
                                        name="ls_id"
                                        id="bulk-ls-id"
                                        class="selectpicker
                                        w-100
                                        dynamic-filter
                                        load-filter"
                                        data-filter="lead_stage"
                                        data-live-search="true"
                                        data-style="default-btn"
                                        title="Select Lead Stage">

                                        <option value="not_required">

                                        Not Required

                                        </option>

                                        </select>

                                        </div>

                                        </div>


                                        <div class="col-md-4 6_dropdowns" style="display:none;">

                                        <div class="mb-3">

                                        <label
                                        for="bulk-careoff"
                                        class="form-label">

                                        Careoff

                                        </label>

                                        <select
                                        name="careoff_id"
                                        id="bulk-careoff"
                                        class="selectpicker
                                        w-100
                                        dynamic-filter
                                        load-filter"
                                        data-filter="careoff"
                                        data-default="{{ Auth::guard('admin')->id() }}"
                                        data-live-search="true"
                                        data-style="default-btn"
                                        title="Select Careoff">

                                        <option value="not_required">

                                        Not Required

                                        </option>

                                        </select>

                                        </div>

                                        </div>


                                        <div class="col-md-4 6_dropdowns" style="display:none;">

                                        <div class="mb-3">

                                        <label
                                        for="bulk-group-id"
                                        class="form-label">

                                        Group

                                        </label>

                                        <select
                                        name="group_id"
                                        id="bulk-group-id"
                                        class="selectpicker
                                        w-100
                                        dynamic-filter
                                        load-filter"
                                        data-filter="group"
                                        data-live-search="true"
                                        data-style="default-btn"
                                        title="Select Group">

                                        <option value="not_required">

                                        Not Required

                                        </option>

                                        </select>

                                        </div>

                                        </div>


                                        <div class="col-md-4 6_dropdowns" style="display:none;">

                                        <div class="mb-3">

                                        <label
                                        for="bulk-source"
                                        class="form-label">

                                        Source

                                        </label>

                                        <select
                                        name="source"
                                        id="add_source"
                                        class="selectpicker
                                        w-100
                                        dynamic-filter
                                        load-filter"
                                        data-filter="source"
                                        data-live-search="true"
                                        data-style="default-btn"
                                        title="Select Source">

                                        <option value="">

                                        Select Source

                                        </option>

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
                                            <th>Created By</th>
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


    </div>
@endsection

@php

$businessType = !empty($allcontactsaveadminfilter?->lead_type)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->lead_type))
    : [];

$careoffId = !empty($allcontactsaveadminfilter?->careoff_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->careoff_id))
    : [];

$createdBy = !empty($allcontactsaveadminfilter?->user_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->user_id))
    : [];

$groupId = !empty($allcontactsaveadminfilter?->group_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->group_id))
    : [];

$lcsId = !empty($allcontactsaveadminfilter?->lcs_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->lcs_id))
    : [];

$lsId = !empty($allcontactsaveadminfilter?->ls_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->ls_id))
    : [];

$leadPriority = !empty($allcontactsaveadminfilter?->lead_prority)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->lead_prority))
    : [];

$industId = !empty($allcontactsaveadminfilter?->indust_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->indust_id))
    : [];

$stateId = !empty($allcontactsaveadminfilter?->state_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->state_id))
    : [];

$countryId = !empty($allcontactsaveadminfilter?->country_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->country_id))
    : [];

$conversation_type = !empty($allcontactsaveadminfilter?->conversation_type)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->conversation_type))
    : [];

$cityId = !empty($allcontactsaveadminfilter?->city_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->city_id))
    : [];

$ownerId = !empty($allcontactsaveadminfilter?->owner_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->owner_id))
    : [];

$country_dial_code = !empty($allcontactsaveadminfilter?->country_dial_code)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->country_dial_code))
    : [];

$country_dial_code_number = !empty($allcontactsaveadminfilter?->country_dial_code_number)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->country_dial_code_number))
    : [];

$exlude_country_mobile_code = !empty($allcontactsaveadminfilter?->exlude_country_mobile_code)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->exlude_country_mobile_code))
    : [];

$sourceId = !empty($allcontactsaveadminfilter?->source_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->source_id))
    : [];

@endphp

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ asset('user/intl-tel-input-master/build/js/intlTelInput-jquery.min.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/all-contact-validation.js') }}"></script>
    <script>
        let table;

        // Declared globally (outside any ready-handler closure) because this file wires
        // up its DataTables ajax "data" callback (below) in the same top-level scope,
        // and Custom Filter's own row-management code lives in a later, separate
        // $(document).ready closure further down this page — plain function declarations
        // don't cross those closure boundaries, only truly global ones do.
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

        $(function () {

            table = $('.datatables-users').DataTable({

                processing: true,

                serverSide: true,

                responsive: true,

                autoWidth: false,

                ordering: false,
                
                destroy: true,

                pageLength: 10,

                ajax: {

                    url: "{{ route('admin.allcontact.json') }}",

                    type: "GET",

                    data: function (d) {

                        d.business_type = $('#by-business-type').val();
                        d.careoff_id = $('#by-careoff-id').val();
                        d.created_by = $('#by-create-by').val();
                        d.group_id = $('#by-groupby-id').val();

                        d.lcs_id = $('#by-lifecycle-status').val();
                        d.ls_id = $('#by-lead-stage').val();

                        d.lead_priority = $('#by-lead-priority').val();

                        d.indust_id = $('#by-industry').val();

                        d.state_id = $('#by-state-id').val();
                        d.country_id = $('#by-country-id').val();
                        d.city_id = $('#by-city-id').val();

                        d.owner_id = $('#by-lead-owner').val();

                        d.created_date = $('#by-created-date').val();
                        d.updated_date = $('#by-updated-date').val();
                        d.staff_updated_date = $('#by-staff-updated-date').val();

                        d.conversation_type =
                        $('#by-conversation-type').val();

                        d.followup_before =
                        $('#by-followup-before').val();

                        d.country_dial_code =
                        $('#by-coutry-dial-code').val();

                        d.country_dial_code_number =
                        $('#by-coutry-dial-code-number').val();

                        d.exlude_country_mobile_code =
                        $('#by-exlude-coutry-mobile-code').val();

                        d.status = $('#by-contact-status').val();
                        d.source_id = $('#by-source-id').val();
                        d.has_email = $('#by-has-email').val();
                        d.has_mobile = $('#by-has-mobile').val();
                        d.recently_added = $('#by-recently-added').val();

                        d.custom_filters = JSON.stringify(collectCustomFilters());

                    }

                },

                columns: [

                    {
                        data: 'checkbox',
                        name: 'checkbox',
                        searchable:false,
                        orderable:false
                    },

                    {
                        data:'name',
                        name:'full_name'
                    },

                    {
                        data:'company',
                        name:'company_name'
                    },

                    {
                        data:'business',
                        name:'lead_type'
                    },

                    {
                        data:'stage',
                        name:'ls.name'
                    },

                    {
                        data:'priority',
                        name:'lead_prority'
                    },

                    {
                        data:'optin',
                        name:'optin'
                    },

                    {
                        data:'group',
                        name:'group.name'
                    },

                    {
                        data:'careoff',
                        name:'careoff.name'
                    },

                    {
                        data:'actions',
                        name:'actions',
                        searchable:false,
                        orderable:false
                    }

                ],

                order:[
                    [1,'desc']
                ],

                language:{

                    processing:
                    '<i class="fa fa-spinner fa-spin"></i> Loading...'

                },

                initComplete: function () {

                    // The browser (not this app) was remembering old values typed into
                    // this search box and re-filling it on every page load because it
                    // had no autocomplete attribute. Disabling it stops that.
                    $('#DataTables_Table_0_filter input[type="search"]')
                        .attr('autocomplete', 'off')
                        .val('');

                }

            });

            table.on('xhr.dt', function (e, settings, json) {

                if (!json || !json.counts) {
                    return;
                }

                const counts = json.counts;

                $('#contact_count_total').text(counts.total ?? 0);
                $('#contact_count_new').text(counts.new_contacts ?? 0);
                $('#contact_count_active').text(counts.active_contacts ?? 0);
                $('#contact_count_inactive').text(counts.inactive_contacts ?? 0);
                $('#contact_count_with_email').text(counts.with_email ?? 0);
                $('#contact_count_with_mobile').text(counts.with_mobile ?? 0);
                $('#contact_count_recently_added').text(counts.recently_added ?? 0);

                const bizCounts = json.business_type_counts || {};
                $('.biz-count-num').each(function () {
                    $(this).text(bizCounts[$(this).attr('data-key')] ?? 0);
                });

                const stageCounts = json.lead_stage_counts || {};
                $('.stage-count-num').each(function () {
                    $(this).text(stageCounts[$(this).attr('data-key')] ?? 0);
                });

            });
        });
    </script>

    <script>
        // Status cards are grouped into three categories — Contact Status,
        // Business Type, Lead Stage. Clicking a card only clears/sets cards
        // within its OWN category (data-type); the other categories' active
        // selections are left alone, so the three combine (AND together)
        // instead of one click resetting the other two.
        $(document).on('click', '.all-status-card', function (e) {
            e.preventDefault();

            var $this = $(this);
            var type = $this.attr('data-type');
            var wasActive = $this.hasClass('active');

            $('.all-status-card[data-type="' + type + '"]').removeClass('active');

            // Clear only the filter field(s) THIS category's cards can drive.
            if (type === 'contact') {
                $('#by-contact-status').selectpicker('val', []);
                $('#by-lifecycle-status').selectpicker('val', []);
                $('#by-has-email').val('');
                $('#by-has-mobile').val('');
                $('#by-recently-added').val('');
            } else if (type === 'business') {
                $('#by-business-type').selectpicker('val', []);
            } else if (type === 'stage') {
                $('#by-lead-stage').selectpicker('val', []);
            }

            if (!wasActive) {
                $this.addClass('active');

                if (type === 'contact') {
                    switch ($this.attr('data-card')) {
                        case 'new':
                            $('#by-lifecycle-status').selectpicker('val', ['1']);
                            break;
                        case 'active':
                            $('#by-contact-status').selectpicker('val', ['1']);
                            break;
                        case 'inactive':
                            $('#by-contact-status').selectpicker('val', ['0']);
                            break;
                        case 'with_email':
                            $('#by-has-email').val('1');
                            break;
                        case 'with_mobile':
                            $('#by-has-mobile').val('1');
                            break;
                        case 'recently_added':
                            $('#by-recently-added').val('1');
                            break;
                        case 'total':
                        default:
                            // Total is a shortcut to clear every card above; no filter to set.
                            break;
                    }
                } else if (type === 'business') {
                    $('#by-business-type').selectpicker('val', [$this.attr('data-value')]);
                } else if (type === 'stage') {
                    $('#by-lead-stage').selectpicker('val', [$this.attr('data-value')]);
                }
            }

            // A single trigger is enough: reloadTodoList() (bound to this field via
            // bindFilterChange) re-reads every filter field's current value from the
            // DOM, so firing it once here covers whichever field(s) were set above
            // without stacking up one AJAX request per field.
            $('#by-recently-added').trigger('change');
        });
    </script>

    <script>

        const savedFilter = {

            business_type: @json($businessType),

            careoff_id: @json($careoffId),

            created_by: @json($createdBy),

            group_id: @json($groupId),

            lcs_id: @json($lcsId),

            ls_id: @json($lsId),

            lead_priority: @json($leadPriority),

            indust_id: @json($industId),

            state_id: @json($stateId),

            country_id: @json($countryId),

            city_id: @json($cityId),

            owner_id: @json($ownerId),

            conversation_type: @json($conversation_type),

            country_dial_code: @json($country_dial_code),

            country_dial_code_number: @json($country_dial_code_number),

            exlude_country_mobile_code: @json($exlude_country_mobile_code),

            source_id: @json($sourceId),

        };

        let filterLoaded = false;

        $('.filterpanel').on('click', function () {

            if (filterLoaded) {
                $('#filterpanel').modal('show');
                return;
            }

            $.ajax({

                url: "{{ route('admin.allcontact.loadallfilters') }}",

                type: "GET",

                success:function(res){

                    setDropdown(
                        '#by-business-type',
                        res.business_type,
                        'text',
                        savedFilter.business_type
                    );

                    setDropdown(
                        '#by-careoff-id',
                        res.careoff,
                        'normal',
                        savedFilter.careoff_id
                    );

                    setDropdown(
                        '#by-create-by',
                        res.created_by,
                        'normal',
                        savedFilter.created_by
                    );

                    setDropdown(
                        '#by-groupby-id',
                        res.group,
                        'normal',
                        savedFilter.group_id
                    );

                    setDropdown(
                        '#by-lifecycle-status',
                        res.lifecycle,
                        'normal',
                        savedFilter.lcs_id
                    );

                    setDropdown(
                        '#by-lead-stage',
                        res.lead_stage,
                        'normal',
                        savedFilter.ls_id
                    );

                    setDropdown(
                        '#by-lead-priority',
                        res.lead_priority,
                        'text',
                        savedFilter.lead_priority
                    );

                    setDropdown(
                        '#by-industry',
                        res.industry,
                        'normal',
                        savedFilter.indust_id
                    );

                    setDropdown(
                        '#by-state-id',
                        res.state,
                        'normal',
                        savedFilter.state_id
                    );

                    setDropdown(
                        '#by-country-id',
                        res.country,
                        'normal',
                        savedFilter.country_id
                    );

                    setDropdown(
                        '#by-city-id',
                        res.city,
                        'normal',
                        savedFilter.city_id
                    );

                    setDropdown(
                        '#by-lead-owner',
                        res.lead_owner,
                        'normal',
                        savedFilter.owner_id
                    );

                    setDropdown(
                        '#by-source-id',
                        res.source,
                        'normal',
                        savedFilter.source_id
                    );

                    filterLoaded = true;

                    $('#filterpanel').modal('show');

                }

            });

        });

        function setDropdown(
            selector,
            data,
            type = 'normal',
            selected = []
        ) {

            let html = '';

            selected = selected.map(
                item => String(item).trim()
            );

            $.each(data, function(index, item) {

                let value = '';
                let text = '';

                if(type === 'text'){

                    value = item;
                    text = item;

                }else{

                    value = item.id;
                    text = item.name;

                }

                let isSelected = selected.includes(
                    String(value).trim()
                );

                html += `
                    <option
                        value="${value}"
                        ${isSelected ? 'selected' : ''}>
                        ${text}
                    </option>
                `;

            });

            $(selector)
                .html(html)
                .selectpicker('refresh');

        }

    </script>

    <script>
     // Initialize Export Column Toggles
    function initializeColumns() {
        const toggleContainer = $('#Contactscolumns');
        if (!toggleContainer.length) return;

        toggleContainer.html('');

        // 🔥 MUST match backend exportColumnMap()
        const EXPORT_KEY_MAP = {
            1: 'Name',
            2: 'Company',
            3: 'Business',
            4: 'Stage',
            5: 'Priority',
            6: 'Opt In',
            7: 'Group',
            8: 'Careoff',
            9: 'Primary No',
            10: 'Secondory No',
            11: 'Mobile 1',
            12: 'Mobile 2',
            13: 'Mobile 3',
        };

        Object.entries(EXPORT_KEY_MAP).forEach(([key, label]) => {
            const stored = localStorage.getItem('contacts_col_' + key);
            const isChecked = stored !== 'false';

            toggleContainer.append(`
                    <div class="form-check mb-2 col-6">
                        <input class="form-check-input toggle-contact-column"
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

    $(document).on('change', '#is-all-export', function () {
        if ($(this).is(':checked')) {
            $('.AllexportdataMsg').removeClass('d-none');
        } else {
            $('.AllexportdataMsg').addClass('d-none');
        }
    });
    </script>


    @if(session('isModelShow') == true)
        <script>
            $(document).ready(function(){

                var contactId = "{{ session('isModelShowId') }}";

                // create a temporary element with the same attributes
                var fakeButton = $('<a>')
                    .attr('data-bs-toggle', 'modal')
                    .attr('data-bs-target', '#editStatus')
                    .attr('data-id', contactId);

                // manually trigger the modal event with relatedTarget
                $('#editStatus').trigger({
                    type: 'show.bs.modal',
                    relatedTarget: fakeButton[0]
                });

                var modal = new bootstrap.Modal(document.getElementById('editStatus'));
                modal.show();

                $.ajax({
                    url: "{{ route('admin.allcontact.edit') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        id: contactId
                    },
                    success: function(data){
                        
                            var lcs_id = data.post.lcs_id;
                            var contact_id = contactId;
                            $("#allcontactID").val(contact_id);
                            $("#update-lead-stage").empty();
                            $.ajax({
                                url: "{{ route('admin.allcontact.getleadstage') }}",
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

                        $('#update-lead-cycle-status').val(data.post.lcs_id).change();


                    }
                });

                               

            });
        </script>
    @endif
    <script>

        
        $(document).ready(function(){
            let csvData = [];
            let csvHeaders = [];


            $('#uploadCSVBtn').on('click',function(){
                let formData = new FormData();
                formData.append("csv_file", $("#add-allcontact-csv-data")[0].files[0]);

                $.ajax({
                    url: "{{ route('admin.allcontact.csv.import.preview') }}",
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
                        
                        $(".6_dropdowns").show();


                        csvHeaders.forEach((col, index) => {
                            tableRows += `
                                <tr>
                                    <td>${col}</td>
                                    <td>
                                        <select class="field-mapping form-select" data-index="${index}">
                                            <option value="">Not Required</option>
                                            <option value="lead_type">Business Type</option>
                                            <option value="full_name">Full Name</option>
                                            <option value="primary_no_wsp">Primary Mobile No</option>
                                            <option value="secondary_no_wsp">Secondary Mobile Number</option>
                                            <option value="mobile_no1_wsp">Mobile No 1</option>
                                            <option value="mobile_no2_wsp">Mobile No 2</option>
                                            <option value="mobile_no3_wsp">Mobile No 3</option>
                                            <option value="email">Email</option>
                                            <option value="email0">Email 1</option>
                                            <option value="email1">Email 2</option>
                                            <option value="email2">Email 3</option>
                                            <option value="state_id">State</option>
                                            <option value="city_id">City</option>
                                            <option value="company_name">Company Name</option>
                                            <option value="full_address">Full Address</option>
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


                if ($('#bulk_lead_type').val() == '') {
                    $(".error-display-business-type").text("Business type is required.").show();
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

               
                console.log( csvData.length);
                
                // Proceed with import AJAX
                $.ajax({
                    url: "{{ route('admin.allcontact.csv.upload') }}",
                    type: "POST",
                    data: {
                        mapped_fields: mappedFields,
                        csv_data: csvData,
                        lead_type: $('#bulk_lead_type').val(),
                        lcs_id: $('#bulk-lcs-id').val(),
                        ls_id: $('#bulk-ls-id').val(),
                        careoff_id: $('#bulk-careoff').val(),
                        group_id: $('#bulk-group-id').val(),
                        source: $('#add_source').val(),
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
            // Select22
            var select22 = $('.select22');
            if (select22.length) {
                select22.each(function () {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                    //   placeholder: 'Select value',
                    dropdownParent: $this.parent()
                    });
                });
            }

            const select2e = $('.select2e');

            if (select2e.length) {
                select2e.each(function(){
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        dropdownParent: $this.parent()
                    });
                });
            }

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

            // Trigger Status if avalable
            $('#editStatus').on("show.bs.modal",function(e){
                
                var allcontactID = $(e.relatedTarget).data('id') || $('#editStatus').data('id');
                
                $('#allcontactID').val(allcontactID);
                $.ajax({
                    url: "{{ route('admin.allcontact.edit') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        id: allcontactID
                    },
                    success: function(data){

                        $('#hidden_lcs_id').val(data.post.lcs_id);

                        const $dropdown = $('#update-lead-cycle-status');

                        if ($dropdown.length && !$dropdown.data('loaded')) {
                            $dropdown.data('loaded', true);
                            loadDropdown($dropdown);
                        }

                        // $('#update-lead-cycle-status').val(data.post.lcs_id).change();

                        $('#update-lead-cycle-status').val(data.post.lcs_id).trigger('change');
                    }
                });
            });

            // change lead stage when click lifecycle status
            $('#update-lead-cycle-status').on('change',function(){
                
               var lcs_id = $(this).val() ?? $('#hidden_lcs_id').val();
                var contact_id = $("#allcontactID").val();
                $("#update-lead-stage").empty();
                $.ajax({
                    url: "{{ route('admin.allcontact.getleadstage') }}",
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

            // Update Prority Type
            $(document).on('click','.lead-priority',
                function(){

                    let lead_priority =
                    $(this)
                    .text()
                    .trim();

                    let allcontactID =
                    $(this)
                    .data('id');


                    Swal.fire({

                        title:'Are you sure?',

                        text:
                        "You want to update Lead Priority",

                        icon:'warning',

                        showCancelButton:true,

                        confirmButtonText:
                        'Yes, update it!',

                        customClass:{

                            confirmButton:
                            'btn btn-primary me-3',

                            cancelButton:
                            'btn btn-label-secondary'

                        },

                        buttonsStyling:false

                    })

                    .then(function(result){

                        if(

                        result.isConfirmed

                        ){

                            $.ajax({

                                url:

                                "{{ route('admin.allcontact.leadpriorityupdate') }}",

                                method:'POST',

                                data:{

                                    _token:

                                    "{{ csrf_token() }}",

                                    lead_priority:

                                    lead_priority,

                                    id:

                                    allcontactID

                                },

                                success:function(){

                                    Swal.fire({

                                        title:'Updated!',

                                        text:

                                        'Lead Priority updated successfully!',

                                        icon:'success',

                                        timer:1000,

                                        showConfirmButton:false,

                                        buttonsStyling:false

                                    });

                                    setTimeout(

                                        function(){

                                            $('.datatables-users')
                                            .DataTable()
                                            .ajax
                                            .reload(
                                                null,
                                                false
                                            );

                                        },

                                        1100

                                    );

                                }

                            });

                        }

                    });

                }

            );

            // Update Optin and Optout
            $('#editOptin').on("show.bs.modal",function(e){
                var allcontactID = $(e.relatedTarget).data('id');
                $('#editoptinID').val(allcontactID);

                $.ajax({
                    url: "{{ route('admin.allcontact.getoptin') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        id: allcontactID
                    },
                    success: function(data){
                        if (data.optinout == 1) {
                            $('#add-subscribe').attr("checked",true);
                            $('#add-unsubscribe').attr("checked", false);
                        } else {
                            $('#add-subscribe').attr("checked",false);
                            $('#add-unsubscribe').attr("checked", true);
                        }
                    }
                });

            });

            $('#lead_type').on('change',function(){
                var leadtype = $(this).val();
                if(leadtype == 'Job seeker'){
                $('#job_title_div').show();
                $('#job_title').show();
                $('#indust_id_div').hide();
                $('#company_name_div').hide();
                }else if(leadtype == 'HIRING US'){
                $('#job_title_div').show();
                $('#indust_id_div').hide();
                $('#company_name_div').hide();
                }else if (leadtype == 'Company') {
                $('#job_title_div').hide();
                $('#indust_id_div').show();
                $('#company_name_div').show();
                }else if (leadtype == 'Regular Wakala Party') {
                $('#company_name_div').show();
                }else{
                $('#job_title_div').hide();
                $('#indust_id_div').hide();
                $('#company_name_div').hide();
                }

            });

            $('#add_lead_type').on('change',function(){
                var leadtype = $(this).val();
                if(leadtype == 'Job seeker'){
                    $('#job_title_div_add').show();
                    $('#indust_id_div_add').hide();
                    $('#company_name_div_add').hide();
                    $('#office_name_div_add').hide();
                }else if(leadtype == 'HIRING US'){
                    $('#job_title_div_add').show();
                    $('#indust_id_div_add').hide();
                    $('#company_name_div_add').hide();
                    $('#office_name_div_add').hide();
                }else if (leadtype == 'Company') {
                    $('#job_title_div_add').hide();
                    $('#office_name_div_add').hide();
                    $('#indust_id_div_add').show();
                    $('#company_name_div_add').show();
                }else if (leadtype == 'Associate with office') {
                    $('#job_title_div_add').hide();
                    $('#company_name_div_add').hide();
                    $('#indust_id_div_add').hide();
                    $('#office_name_div_add').show();
                }else if (leadtype == 'Regular Wakala Party') {
                    $('#company_name_div_add').show();
                }else{
                    $('#job_title_div_add').hide();
                    $('#indust_id_div_add').hide();
                    $('#company_name_div_add').hide();
                    $('#office_name_div_add').hide();
                }

            });

            $('#deletescon').on("show.bs.modal",function(e){
                var deleteID = $(e.relatedTarget).data('id');
                $('#contactID2').val(deleteID);
            });

        });
    </script>

    <script>
        $(document).ready(function(){

            let editDropdownsLoaded = false;
            let isLoadingDropdowns = false;
            let filterCache = null;

            $('#updateReg').on("show.bs.offcanvas", function (e) {

                const editID = $(e.relatedTarget).data('id');
                const staff_id = "{{ Auth::guard('admin')->user()->id }}";

                if (!editDropdownsLoaded) {

                    loadEditDropdowns(function () {

                        editDropdownsLoaded = true;
                        SelectFeilds(editID,staff_id);
                    });

                }

                SelectFeilds(editID,staff_id);


            });

            function SelectFeilds(editID,staff_id){

                $('#edit_ID').val(editID);
                console.log(editID);
                 $.ajax({
                    url: '{{ route("admin.allcontact.edit") }}',
                    method: "GET",
                    data: { id: editID },
                    success: function (data) {
                        const post = data.post;
                        const phones = data.update_phones;

                        // Basic field assignments
                        $('#lead_type').val(post.lead_type).trigger('change');
                  
                        $('#edit-full-name').val(post.full_name);
                        $('#job_title').val(post.job_title);
                        $('#edit-company-name').val(post.company_name);
                        $('#edit-agency-name').val(post.office_name);
                        $('#edit-primary-no').val(post.secondary_no_wsp);
                        $('#edit-mobile-no').val(post.primary_no_wsp);
                        $('#edit-phone0').val(post.mobile_no1_wsp);
                        $('#edit-phone1').val(post.mobile_no2_wsp);
                        $('#edit-phone2').val(post.mobile_no3_wsp);
                        $('#edit-email').val(post.email);
                        $('#edit-email0').val(post.email0);
                        $('#edit-email1').val(post.email1);
                        $('#edit-email2').val(post.email2);
                        $('#edit-descr').val(post.descr);

                        $('#edit-lcs-id').selectpicker('val', String(post.lcs_id).trim());
                        $('#edit-ls-id').selectpicker('val', String(post.ls_id).trim());
                        $('#edit-industry-id').selectpicker('val', String(post.indust_id).trim());
                        $('#edit-country-id').selectpicker('val', String(post.country_id).trim());
                        $('#edit-city-id').selectpicker('val', String(post.city_id).trim());
                        $('#edit-careoff').selectpicker('val', String(post.careoff_id).trim());
                        $('#edit-leadowner').selectpicker('val',String(post.owner_id || staff_id));
                        $('#edit-group-id').selectpicker('val', String(post.group_id).trim());
                        $('#edit_source').selectpicker('val', String(post.source_id).trim());

                        // // Lead type conditional fields
                        toggleLeadTypeFields(post.lead_type);

                        // // Dial code fields
                        $('#primary-con-dial-code-ed').val(phones.secondary_no_wsp_dial_code);
                        $('#mobile-no-dial-code-ed').val(phones.primary_no_wsp_dial_code);
                        $('#phone0-dial-code-ed').val(phones.mobile_no1_wsp_dial_code);
                        $('#phone1-dial-code-ed').val(phones.mobile_no2_wsp_dial_code);
                        $('#phone2-dial-code-ed').val(phones.mobile_no3_wsp_dial_code);

                        // // Initialize intlTelInput for all phone fields
                        // initIntlTelInput('.edit-primary-no', phones.secondary_no_wsp_iso_code, '.primary-con-dial-code-ed');
                        // initIntlTelInput('.edit-mobile-no', phones.primary_no_wsp_iso_code, '.mobile-no-dial-code-ed');
                        // initIntlTelInput('.edit-phone0', phones.mobile_no1_wsp_iso_code, '.phone0-dial-code-ed');
                        // initIntlTelInput('.edit-phone1', phones.mobile_no2_wsp_iso_code, '.phone1-dial-code-ed');
                        // initIntlTelInput('.edit-phone2', phones.mobile_no3_wsp_iso_code, '.phone2-dial-code-ed');
                    }
                });
            }

            function loadEditDropdowns(callback = null)
            {
                if(filterCache){

                    setEditDropdown('#edit-lcs-id', filterCache.lifecycle);
                    setEditDropdown('#edit-ls-id', filterCache.lead_stage);
                    setEditDropdown('#edit-industry-id', filterCache.industry);
                    setEditDropdown('#edit-country-id', filterCache.country);
                    setEditDropdown('#edit-city-id', filterCache.city);
                    setEditDropdown('#edit-careoff', filterCache.careoff);
                    setEditDropdown('#edit-leadowner', filterCache.lead_owner);
                    setEditDropdown('#edit-group-id', filterCache.group);
                    setEditDropdown('#edit_source', filterCache.source);

                    if(callback) callback(filterCache);

                    return;
                }

                $.ajax({
                    url: "{{ route('admin.allcontact.loadallfilters') }}",
                    type: "GET",
                    success: function(res){

                        filterCache = res;

                        // Populate dropdowns
                        setEditDropdown('#edit-lcs-id', res.lifecycle);
                        setEditDropdown('#edit-ls-id', res.lead_stage);
                        setEditDropdown('#edit-industry-id', res.industry);
                        setEditDropdown('#edit-country-id', res.country);
                        setEditDropdown('#edit-city-id', res.city);
                        setEditDropdown('#edit-careoff', res.careoff);
                        setEditDropdown('#edit-leadowner', res.lead_owner);
                        setEditDropdown('#edit-group-id', res.group);
                        setEditDropdown('#edit_source', res.source);


                        if(callback) callback(res);
                    }
                });


            }

            function setEditDropdown(selector, data, selectedValue = '')
            {
                let dropdown = $(selector);

                let html = '<option value="">Select</option>';

                $.each(data, function(index, item){

                    let selected = (String(item.id) === String(selectedValue))
                        ? 'selected'
                        : '';

                    html += `
                        <option value="${item.id}" ${selected}>
                            ${item.name}
                        </option>
                    `;
                });

                dropdown.html(html);

                dropdown.selectpicker('refresh');
            }

            /**
             * Show/Hide sections based on lead_type
            */

            function toggleLeadTypeFields(lead_type) {
                const show = (id) => $(id).show();
                const hide = (id) => $(id).hide();

                // Hide all optional fields first
                hide('#job_title_div');
                hide('#office_name_div');
                hide('#job_desg_div');
                hide('#company_name_div');
                hide('#indust_id_div');

                if (lead_type === 'Job seeker') {
                    show('#job_title_div');
                }else if (lead_type === 'HIRING US') {
                    show('#job_title_div');
                } else if (lead_type === 'Company') {
                    show('#job_desg_div');
                    show('#company_name_div');
                    show('#indust_id_div');
                }else if (lead_type === 'Regular Wakala Party') {
                    show('#company_name_div');
                } 
                else if (lead_type === 'Associate with office') {
                    show('#office_name_div');
                }
            }

            /**
             * Initialize intlTelInput plugin for a phone field
            */

            function initIntlTelInput(selector, initialCountryISO, dialCodeTarget) {
                const input = $(selector);
                if (!input.length) return;

                input.intlTelInput('destroy');

                input.intlTelInput({
                    onlyCountries: ["sa", "in", "qa", "ae", "kw"],
                    preferredCountries: ["in", "sa"],
                    separateDialCode: true,
                    initialCountry: initialCountryISO
                }).on('countrychange', function () {
                    const dialCode = input.intlTelInput("getSelectedCountryData").dialCode;
                    $(dialCodeTarget).val(dialCode);
                });
            }

        });
    </script>

    <script>
        $(document).ready(function(){

            const allowedCountries = ["sa", "in", "qa", "ae", "kw"];
            const preferredCountries = ["in", "sa"];

            function initializeIntlTelInput(selector, dialCodeInputId){
                const $input = $(selector);

                $input.intlTelInput({
                    onlyCountries: allowedCountries,
                    preferredCountries: preferredCountries,
                    separateDialCode: true,
                    initialCountry: "",
                }).on('countrychange', function(){
                    const dialCode = $input.intlTelInput("getSelectedCountryData").dialCode;
                    $(dialCodeInputId).val(dialCode);
                });
            }

            // Initialize all fields with corresponding dial code input IDs
          //  initializeIntlTelInput('.add-short-mobile-no', '#mobile-no-dial-code-short');
            // initializeIntlTelInput('.add-primary-no', '#primary-con-dial-code');
            // initializeIntlTelInput('.add-mobile-no', '#mobile-no-dial-code');
            // initializeIntlTelInput('.add-phone0', '#phone0-dial-code');
            // initializeIntlTelInput('.add-phone1', '#phone1-dial-code');
            // initializeIntlTelInput('.add-phone2', '#phone2-dial-code');


        });
    </script>

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



        $('#bulkexport').on('show.bs.modal', function(e) {

            var allselectedvals = [];
            $('.dt-checkboxes:checked').each(function(){
                allselectedvals.push($(this).attr('data-id'));
            });

            if (allselectedvals <= 0) {

            }else{
                initializeColumns();
                var join_all_selected_values = allselectedvals.join(",");
                $('#bulkexport_id').val(join_all_selected_values);
            }
        });

        $(document).on('click', '#bulk-export', function(e) {
            e.preventDefault();

            let contactIds = $('#bulkexport_id').val();
            let columns = [];

            $('.toggle-contact-column:checked').each(function() {
                columns.push($(this).data('column'));
            });

            if (columns.length === 0) {
                alert('Please select at least one column');
                return;
            }

            let form = $('<form>', {
                method: 'POST',
                action: "{{ route('admin.allcontact.bulkexport') }}"
            });

            let is_all_export = $('#is-all-export').is(':checked');

            form.append(`<input type="hidden" name="_token" value="{{ csrf_token() }}">`);
            form.append(`<input type="hidden" name="contact_ids" value="${contactIds}">`);
            form.append(`<input type="hidden" name="is_all_export" value="${is_all_export}">`);

            columns.forEach(col => {
                form.append(`<input type="hidden" name="columns[]" value="${col}">`);
            });

            $('body').append(form);
            form.submit();
            form.remove();

            $('#bulkexport').modal('hide');
        });


        $('#bulkcountryupdate').on('show.bs.modal',function(e){
            var allselectedvals = [];
            $('.dt-checkboxes:checked').each(function(){
                allselectedvals.push($(this).attr('data-id'));
            });

            if (allselectedvals <= 0) {

            }else{
                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");

                $('#contactIDGRPTRC').val(join_all_selected_values);
            }
        });

        $('#bulkcountrycodeupdate').on('show.bs.modal',function(e){
            var allselectedvals = [];
            $('.dt-checkboxes:checked').each(function(){
                allselectedvals.push($(this).attr('data-id'));
            });
            if (allselectedvals <= 0) {

            }else{
                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");

                $('#getListBulkUpdate').text(countc_id+" contacts will be update, are you sure?");
                $('#contactIDGRPTRC2').val(join_all_selected_values);
            }

        });

        $('#bulkcountrycodeupdatefield').on('show.bs.modal', function(e){
            var allselectedvals = [];
            $('.dt-checkboxes:checked').each(function(){
                allselectedvals.push($(this).attr('data-id'));
            });

            if (allselectedvals <= 0) {

            } else {
                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");

                $('#getListBulkUpdate25').text(countc_id+" contacts will be update, are you sure?");
                $('#contactIDGRPTRC25').val(join_all_selected_values);
            }
        });

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

                $('#contactIDLTRB').val(join_all_selected_values);


            }

        });

        $("#bulktransfercareoff").on('show.bs.modal',function(e){
            var allselectedvals = [];
            $('.dt-checkboxes:checked').each(function(){
                allselectedvals.push($(this).attr('data-id'));
            });



            if (allselectedvals <= 0) {

            }else{
                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");

                $('#contactIDCTR').val(join_all_selected_values);
            }

        });

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

        // $("#transfer-groupm").on("change",function(){
        //     var groupValue = $(this).val();
        //     var totalsend = $("#contactIDGRPTR").val();

        //     if (groupValue != '') {
        //         jQuery.ajax({
        //             url: "{{ route('admin.allcontact.checkgroupLimit') }}",
        //             method: "GET",
        //             type: "html",
        //             data:{
        //                 "grpID": groupValue,
        //                 "totalSend": totalsend
        //             },
        //             success: function(data){

        //                 $("#respMessageGroupTransfer").text(data.message);
        //                 $('#respGroupLimit').text(data.grouplimit);
        //                 if (data.status == 1) {
        //                     $(".dibtngrp").attr("disabled",false);
        //                 } else {
        //                     $(".dibtngrp").attr("disabled",true);
        //                 }

        //             }
        //         });
        //     } else {
        //         $("#respMessageGroupTransfer").text("");
        //         $('#respGroupLimit').text("");
        //         $(".dibtngrp").attr("disabled",false);
        //     }
        // });

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

        $('#send-template-personalise-class').on('change',function(){
            var partvalue = $(this).val();
            navigator.clipboard.writeText(partvalue);
            toastr['success']('Text copied - '+partvalue+'', 'Success', { hideDuration: 3000 });
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


    </script>

    <!-- Bulk Send Whatsapp End -->
    <script>
        $(document).ready(function(){
            var bsRangePickerBasic = $('.bsdatpicket');
            // var singledatepicket = $('.singledatepicker');
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
            // if (singledatepicket.length) {
            //     singledatepicket.daterangepicker({
            //         // todayHighlight: true,
            //         opens: isRtl ? 'left' : 'right',
            //         autoUpdateInput: false,
            //         singleDatePicker: true,
            //         locale: {
            //             cancelLabel: 'Clear',
            //             format: 'YYYY-MM-DD'
            //         }
            //     });
            // }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            // Function to retrieve all filter values
            function getFilterData() {
                return {
                    page_list: $('#pagination_list').val(),
                    search_text: $('#DataTables_Table_0_filter input[type="search"]').val(),
                    state_id: $('#by-state-id').val(),
                    country_id: $('#by-country-id').val(),
                    city_id: $('#by-city-id').val(),
                    followup_before: $('#by-followup-before').val(),
                    conversation_type: $('#by-conversation-type').val(),
                    lcs_id: $('#by-lifecycle-status').val(),
                    ls_id: $('#by-lead-stage').val(),
                    businesstype_id: $('#by-business-type').val(),
                    lead_priority: $("#by-lead-priority").val(),
                    lead_owner: $('#by-lead-owner').val(),
                    industries: $('#by-industry').val(),
                    country_dial_code: $('#by-coutry-dial-code').val(),
                    country_dial_code_number: $('#by-coutry-dial-code-number').val(),
                    exlude_country_mobile_code: $('#by-exlude-coutry-mobile-code').val(),
                    short_form_code: $('#short_form_code').is(':checked') ?"1":"0",
                    // send_tag: $('#by-send-tag').val(),
                    // send_date: $('#by-send-date').val(),
                    // created_at: $('#by-created-date').val(),
                    careoff: $('#by-careoff-id').val(),
                    created_by: $('#by-create-by').val(),
                    group_id: $('#by-groupby-id').val(),
                    created_at: $('#by-created-date').val(),
                    updated_at: $('#by-updated-date').val(),
                    staff_updated_at: $('#by-staff-updated-date').val()
                }
            }

            // Function to reload todo list based on filter data
            // function reloadTodoList() {
            //     $.ajax({
            //         url: "{{ route('admin.allcontact.list') }}",
            //         method: "GET",
            //         type: "html",
            //         data: getFilterData(),
            //         success: function(data){
            //             $('.contactpaginate').html(data);
            //         }
            //     });

            //     updateAllContactFilterIndicator();
            // }
            function reloadTodoList()
            {
                table.ajax.reload(

                    null,

                    false

                );

                updateAllContactFilterIndicator();
            }

            // Generic event handler for change and input events on filter elements
            function bindFilterChange(selector) {
                $(selector).on('change input', function () {
                    reloadTodoList();
                });
            }

            // Bind change/input events to all filter elements
            bindFilterChange('#pagination_list');
            bindFilterChange('#DataTables_Table_0_filter input[type="search"]');
            bindFilterChange('#by-state-id');
            bindFilterChange('#by-country-id');
            bindFilterChange('#by-city-id');
            bindFilterChange('#by-followup-before');
            bindFilterChange('#by-conversation-type');
            bindFilterChange('#by-lifecycle-status');
            bindFilterChange('#by-lead-stage');
            bindFilterChange('#by-business-type');
            bindFilterChange('#by-lead-priority');
            bindFilterChange('#by-lead-owner');
            bindFilterChange('#by-coutry-dial-code');
            bindFilterChange('#by-coutry-dial-code-number');
            bindFilterChange('#by-exlude-coutry-mobile-code');
            bindFilterChange('#by-industry');
            // bindFilterChange('#by-send-tag');
            // bindFilterChange('#by-send-date');
            bindFilterChange('#by-created-date');
            bindFilterChange('#by-updated-date');
            bindFilterChange('#by-staff-updated-date');
            bindFilterChange('#by-create-by');
            bindFilterChange('#by-groupby-id');
            bindFilterChange('#by-careoff-id');
            bindFilterChange('#by-contact-status');
            bindFilterChange('#by-source-id');
            bindFilterChange('#by-has-email');
            bindFilterChange('#by-has-mobile');
            bindFilterChange('#by-recently-added');

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

            // Columns whose values are dates/datetimes (allcontacts uses *_date / *_at
            // naming consistently — followup_date, created_at, updated_at, etc.)
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

            @if(isset($allcontactsaveadminfilter) && !empty($allcontactsaveadminfilter->custom_filters))
                var savedCustomFilters = @json($allcontactsaveadminfilter->custom_filters);
                savedCustomFilters.forEach(function (row) {
                    addCustomFilterRow(row.column, row.operator, row.value);
                });
                updateAllContactFilterIndicator();
            @endif

            // Transfer Contact to email Portal
            (function () {
                var eqpacCsrfToken = "{{ csrf_token() }}";
                var eqpacOptionsLoaded = false;
                var eqpacEmailListCache = [];
                var eqpacSavedConfig = @json(optional(Auth::guard('admin')->user())->email_qamr_allcontact_config);

                // Same field names AllContactController::jsonData() already consumes
                // for its DataTable ajax "data" function - reused as-is here.
                function eqpacGetFilterData() {
                    return {
                        business_type: $('#eqpac_business_type').val(),
                        indust_id: $('#eqpac_industries').val(),
                        careoff_id: $('#by-careoff-id').val(),
                        created_by: $('#by-create-by').val(),
                        group_id: $('#by-groupby-id').val(),
                        lcs_id: $('#by-lifecycle-status').val(),
                        ls_id: $('#by-lead-stage').val(),
                        lead_priority: $('#by-lead-priority').val(),
                        state_id: $('#by-state-id').val(),
                        country_id: $('#by-country-id').val(),
                        city_id: $('#by-city-id').val(),
                        owner_id: $('#by-lead-owner').val(),
                        created_date: $('#by-created-date').val(),
                        updated_date: $('#by-updated-date').val(),
                        staff_updated_date: $('#by-staff-updated-date').val(),
                        conversation_type: $('#by-conversation-type').val(),
                        followup_before: $('#by-followup-before').val(),
                        country_dial_code: $('#by-coutry-dial-code').val(),
                        country_dial_code_number: $('#by-coutry-dial-code-number').val(),
                        exlude_country_mobile_code: $('#by-exlude-coutry-mobile-code').val()
                    };
                }

                function eqpacShowAlert(message, type) {
                    $('#eqpac_alert')
                        .removeClass('d-none alert-success alert-danger')
                        .addClass('alert-' + type)
                        .text(message);
                }

                function eqpacHideAlert() {
                    $('#eqpac_alert').addClass('d-none').text('');
                }

                function eqpacLoadOptions(callback) {
                    if (eqpacOptionsLoaded) {
                        if (callback) {
                            callback();
                        }
                        return;
                    }

                    $.ajax({
                        url: "{{ route('admin.allcontact.loadallfilters') }}",
                        method: 'GET',
                        success: function (res) {
                            var $bt = $('#eqpac_business_type');
                            (res.business_type || []).forEach(function (value) {
                                $bt.append($('<option>').val(value).text(value));
                            });

                            var $ind = $('#eqpac_industries');
                            (res.industry || []).forEach(function (item) {
                                $ind.append($('<option>').val(item.id).text(item.name));
                            });

                            eqpacOptionsLoaded = true;

                            if (callback) {
                                callback();
                            }
                        }
                    });
                }

                // Select2's own ajax + infinite-scroll pagination drives the Email
                // List dropdown: scrolling near the bottom of the still-open list
                // fetches and appends the next page in place, preserving scroll
                // position - no manual "Show More" trigger/state needed.
                function eqpacInitEmailList() {
                    var $el = $('#eqpac_email_list');

                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }

                    eqpacEmailListCache = [];

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
                                    success({ eqpacLocalFilter: true, term: term });
                                    return;
                                }

                                $.ajax({
                                    url: "{{ route('admin.email_qamr_portal.lists') }}",
                                    method: 'POST',
                                    data: {
                                        page: params.data.page || 1,
                                        _token: eqpacCsrfToken
                                    }
                                }).then(success, failure);
                            },
                            processResults: function (resp) {
                                if (resp.eqpacLocalFilter) {
                                    var term = resp.term.toLowerCase();
                                    var filtered = eqpacEmailListCache.filter(function (item) {
                                        return item.text.toLowerCase().indexOf(term) !== -1;
                                    });

                                    return { results: filtered, pagination: { more: false } };
                                }

                                if (!resp.success) {
                                    $('#eqpac_token_status').text(resp.message || 'Invalid API token.');
                                    return { results: [], pagination: { more: false } };
                                }

                                $('#eqpac_token_status').text('');

                                var results = (resp.lists || []).map(function (list) {
                                    var item = { id: list.uid, text: list.name + ' (' + list.uid + ')' };
                                    var alreadyCached = eqpacEmailListCache.some(function (cached) {
                                        return cached.id === item.id;
                                    });
                                    if (!alreadyCached) {
                                        eqpacEmailListCache.push(item);
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

                function eqpacUpdateCount() {
                    var data = eqpacGetFilterData();
                    data._token = eqpacCsrfToken;

                    $.ajax({
                        url: "{{ route('admin.email_qamr_portal.allcontact.filtered_count') }}",
                        method: 'POST',
                        data: data,
                        success: function (resp) {
                            if (resp.success) {
                                $('#eqpac_selected_count').text(resp.count);
                            }
                        }
                    });
                }

                // Custom Field Mapping
                var eqpacContactFields = [];
                var eqpacContactFieldsLoaded = false;
                var eqpacPortalFields = [];

                function eqpacLoadContactFields(callback) {
                    if (eqpacContactFieldsLoaded) {
                        if (callback) {
                            callback();
                        }
                        return;
                    }

                    $.ajax({
                        url: "{{ route('admin.email_qamr_portal.allcontact.contact_fields') }}",
                        method: 'GET',
                        success: function (resp) {
                            if (resp.success) {
                                eqpacContactFields = resp.fields || [];
                                eqpacContactFieldsLoaded = true;
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

                function eqpacLoadPortalFields(listUid, callback) {
                    eqpacPortalFields = [];
                    $('.eqpac-mapping-portal-field').each(function () {
                        eqpacFillPortalFieldSelect($(this));
                    });

                    if (!listUid) {
                        if (callback) {
                            callback();
                        }
                        return;
                    }

                    $.ajax({
                        url: "{{ route('admin.email_qamr_portal.allcontact.list_fields') }}",
                        method: 'POST',
                        data: { list_uid: listUid, _token: eqpacCsrfToken },
                        success: function (resp) {
                            if (!resp.success) {
                                eqpacShowAlert(resp.message || 'Unable to load Email Portal fields for this list.', 'danger');
                                if (callback) {
                                    callback();
                                }
                                return;
                            }

                            eqpacPortalFields = resp.fields || [];
                            $('.eqpac-mapping-portal-field').each(function () {
                                eqpacFillPortalFieldSelect($(this));
                            });

                            if (callback) {
                                callback();
                            }
                        },
                        error: function () {
                            eqpacShowAlert('Unable to load Email Portal fields for this list.', 'danger');
                            if (callback) {
                                callback();
                            }
                        }
                    });
                }

                function eqpacFillContactFieldSelect($select) {
                    var current = $select.val();
                    $select.empty().append($('<option>').val('').text('Select All Contact Field'));

                    eqpacContactFields.forEach(function (field) {
                        $select.append($('<option>').val(field).text(field));
                    });

                    if (current) {
                        $select.val(current);
                    }

                    $select.trigger('change');
                }

                function eqpacFillPortalFieldSelect($select) {
                    var $row = $select.closest('.eqpac-mapping-row');
                    var preset = $row.data('preset-key');
                    var current = $select.val();

                    $select.empty().append($('<option>').val('').text('Select Email Portal Field'));

                    eqpacPortalFields.forEach(function (field) {
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

                function eqpacAddMappingRow(presetField, presetKey) {
                    var $row = $(
                        '<div class="row g-2 align-items-center mb-2 eqpac-mapping-row">' +
                            '<div class="col-md-5">' +
                                '<select class="form-select eqpac-mapping-contact-field"></select>' +
                            '</div>' +
                            '<div class="col-md-5">' +
                                '<select class="form-select eqpac-mapping-portal-field"></select>' +
                            '</div>' +
                            '<div class="col-md-2 d-flex gap-1">' +
                                '<button type="button" class="btn btn-sm btn-outline-secondary eqpac-mapping-add" title="Add More"><i class="ti ti-plus"></i></button>' +
                                '<button type="button" class="btn btn-sm btn-outline-danger eqpac-mapping-remove" title="Remove"><i class="ti ti-trash"></i></button>' +
                            '</div>' +
                        '</div>'
                    );

                    if (presetKey) {
                        $row.data('preset-key', presetKey);
                    }

                    $('#eqpac_mapping_rows').append($row);

                    var $contactFieldSelect = $row.find('.eqpac-mapping-contact-field');
                    var $portalFieldSelect = $row.find('.eqpac-mapping-portal-field');

                    $contactFieldSelect.select2({ dropdownParent: $row, width: '100%' });
                    $portalFieldSelect.select2({ dropdownParent: $row, width: '100%' });

                    eqpacLoadContactFields(function () {
                        eqpacFillContactFieldSelect($contactFieldSelect);
                        if (presetField) {
                            $contactFieldSelect.val(presetField).trigger('change');
                        }
                    });

                    eqpacFillPortalFieldSelect($portalFieldSelect);

                    return $row;
                }

                function eqpacCollectFieldMappings() {
                    var mappings = [];
                    var incomplete = false;

                    $('.eqpac-mapping-row').each(function () {
                        var contactField = $(this).find('.eqpac-mapping-contact-field').val();
                        var portalField = $(this).find('.eqpac-mapping-portal-field').val();

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

                function eqpacApplySavedConfig() {
                    if (!eqpacSavedConfig) {
                        return;
                    }

                    // Business Type / Industry options load asynchronously via
                    // eqpacLoadOptions(); wait for them so .val() has something
                    // to select against on every reopen, not just the first.
                    eqpacLoadOptions(function () {
                        if (eqpacSavedConfig.business_type) {
                            $('#eqpac_business_type').val(eqpacSavedConfig.business_type).trigger('change');
                        }

                        if (eqpacSavedConfig.indust_id && eqpacSavedConfig.indust_id.length) {
                            $('#eqpac_industries').val(eqpacSavedConfig.indust_id.map(String)).trigger('change');
                        }
                    });

                    function eqpacRestoreMappingRows() {
                        if (eqpacSavedConfig.field_mappings && eqpacSavedConfig.field_mappings.length) {
                            $('#eqpac_mapping_rows').empty();

                            eqpacSavedConfig.field_mappings.forEach(function (mapping) {
                                eqpacAddMappingRow(mapping.field, mapping.key);
                            });
                        }
                    }

                    if (eqpacSavedConfig.list_uid) {
                        var $emailList = $('#eqpac_email_list');

                        if (!$emailList.find('option[value="' + eqpacSavedConfig.list_uid + '"]').length) {
                            var optionText = (eqpacSavedConfig.list_name || eqpacSavedConfig.list_uid) + ' (' + eqpacSavedConfig.list_uid + ')';
                            $emailList.append(new Option(optionText, eqpacSavedConfig.list_uid, true, true));
                        }

                        $emailList.val(eqpacSavedConfig.list_uid).trigger('change');

                        // Wait for the Email Portal field list to finish loading before
                        // rebuilding mapping rows, otherwise the portal-field <select>
                        // has no options yet and the saved preset silently fails to select.
                        eqpacLoadPortalFields(eqpacSavedConfig.list_uid, eqpacRestoreMappingRows);
                    } else {
                        eqpacRestoreMappingRows();
                    }
                }

                $('#eqpac_add_mapping_btn').on('click', function () {
                    eqpacAddMappingRow();
                });

                $('#eqpac_mapping_rows').on('click', '.eqpac-mapping-add', function () {
                    eqpacAddMappingRow();
                });

                $('#eqpac_mapping_rows').on('click', '.eqpac-mapping-remove', function () {
                    $(this).closest('.eqpac-mapping-row').remove();
                });

                $('#eqpac_mapping_rows').on('change', '.eqpac-mapping-portal-field', function () {
                    var val = $(this).val();
                    var $this = $(this);

                    if (!val) {
                        return;
                    }

                    var duplicate = false;
                    $('.eqpac-mapping-portal-field').not($this).each(function () {
                        if ($(this).val() === val) {
                            duplicate = true;
                        }
                    });

                    if (duplicate) {
                        toastr.error('This Email Portal Field is already mapped in another row.');
                        $this.val('').trigger('change');
                    }
                });

                $('#eqpac_email_list').on('change', function () {
                    eqpacLoadPortalFields($(this).val());
                });

                $('#exportEmailQamrAllContactModal').on('show.bs.modal', function () {
                    eqpacHideAlert();
                    eqpacLoadOptions();
                    eqpacUpdateCount();
                });

                $('body').on('shown.bs.modal', '#exportEmailQamrAllContactModal', function () {
                    $('#eqpac_business_type, #eqpac_industries').each(function () {
                        if (!$(this).hasClass('select2-hidden-accessible')) {
                            $(this).select2({
                                dropdownParent: $(this).parent()
                            });
                        }
                    });

                    eqpacInitEmailList();
                    eqpacApplySavedConfig();
                });

                $('#eqpac_token_toggle_btn').on('click', function () {
                    var $input = $('#eqpac_api_token');
                    var $icon = $(this).find('i');
                    var isHidden = $input.attr('type') === 'password';

                    $input.attr('type', isHidden ? 'text' : 'password');
                    $icon.toggleClass('ti-eye', !isHidden).toggleClass('ti-eye-off', isHidden);
                    $(this).attr('title', isHidden ? 'Hide token' : 'View token');
                });

                // Save button is the only trigger for persisting the modal's
                // whole configuration: token, Business Type, Industry, Email
                // List, and Custom Field Mappings, all in one request.
                $('#eqpac_token_save_btn').on('click', function () {
                    eqpacHideAlert();

                    var token = $.trim($('#eqpac_api_token').val());

                    if (!token) {
                        eqpacShowAlert('API Token is required.', 'danger');
                        return;
                    }

                    var mappingResult = eqpacCollectFieldMappings();

                    if (mappingResult.incomplete) {
                        eqpacShowAlert('Please select both fields for every custom field mapping, or remove the incomplete row.', 'danger');
                        return;
                    }

                    var $btn = $(this);
                    $btn.prop('disabled', true);

                    var data = {
                        api_token: token,
                        business_type: $('#eqpac_business_type').val(),
                        indust_id: $('#eqpac_industries').val(),
                        list_uid: $('#eqpac_email_list').val(),
                        list_name: $('#eqpac_email_list option:selected').text(),
                        field_mappings: mappingResult.mappings,
                        _token: eqpacCsrfToken
                    };

                    $.ajax({
                        url: "{{ route('admin.email_qamr_portal.allcontact.config.save') }}",
                        method: 'POST',
                        data: data,
                        success: function (resp) {
                            if (resp.success) {
                                eqpacShowAlert(resp.message || 'Configuration saved.', 'success');
                            } else {
                                eqpacShowAlert(resp.message || 'Unable to save configuration.', 'danger');
                            }
                        },
                        error: function (xhr) {
                            var msg = 'Unable to save configuration.';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            eqpacShowAlert(msg, 'danger');
                        },
                        complete: function () {
                            $btn.prop('disabled', false);
                        }
                    });
                });

                $('#eqpac_business_type, #eqpac_industries').on('change', function () {
                    eqpacUpdateCount();
                });

                $('#eqpac_refresh_btn').on('click', function () {
                    eqpacHideAlert();
                    eqpacUpdateCount();
                    eqpacInitEmailList();

                    toastr.info('Refreshed.');
                });

                $('#eqpac_export_btn').on('click', function () {
                    eqpacHideAlert();

                    var token = $.trim($('#eqpac_api_token').val());
                    var businessType = $('#eqpac_business_type').val();
                    var listUid = $('#eqpac_email_list').val();
                    var listName = $('#eqpac_email_list option:selected').text();

                    if (!token) {
                        eqpacShowAlert('Please enter and save your API Token first.', 'danger');
                        return;
                    }

                    if (!businessType) {
                        eqpacShowAlert('Business Type is required.', 'danger');
                        return;
                    }

                    if (!listUid) {
                        eqpacShowAlert('Please select an Email List.', 'danger');
                        return;
                    }

                    var mappingResult = eqpacCollectFieldMappings();

                    if (mappingResult.incomplete) {
                        eqpacShowAlert('Please select both fields for every custom field mapping, or remove the incomplete row.', 'danger');
                        return;
                    }

                    var $btn = $(this);
                    $btn.prop('disabled', true).text('Exporting...');

                    // The saved API Token is used server-side; it is not sent in this payload.
                    var data = eqpacGetFilterData();
                    data.list_uid = listUid;
                    data.list_name = listName;
                    data.field_mappings = mappingResult.mappings;
                    data._token = eqpacCsrfToken;

                    $.ajax({
                        url: "{{ route('admin.email_qamr_portal.allcontact.export') }}",
                        method: 'POST',
                        data: data,
                        success: function (resp) {
                            if (resp.success) {
                                // Job is already dispatched to the queue; redirect immediately
                                // without waiting for it to finish processing.
                                window.location.href = "{{ route('admin.email_qamr_portal.allcontact.history') }}";
                            } else {
                                eqpacShowAlert(resp.message || 'Unable to start export.', 'danger');
                            }
                        },
                        error: function (xhr) {
                            var msg = 'Unable to start export.';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            eqpacShowAlert(msg, 'danger');
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

            // function bindDatePicker(selector) {
            //     $(selector).on('apply.daterangepicker', function (ev, picker) {
            //         $(this).val(picker.startDate.format('YYYY-MM-DD'));
            //         reloadTodoList();
            //     }).on('cancel.daterangepicker', function () {
            //         $(this).val('');
            //         reloadTodoList();
            //     });
            // }

            bindDateRangePicker('input[name="by_created_date"]');
            bindDateRangePicker('input[name="by_updated_date"]');
            bindDateRangePicker('input[name="by_staff_updated_date"]');
            

            // bindDatePicker('input[name="created_date_filter"]');
            // bindDatePicker('input[name="send_date_filter"]');


            // Save Admin Filter
            $(document).on('click','.savetodoFilter',function(){
                var state_id = $('#by-state-id').val();
                var country2 = $('#by-country-id').val();
                var city2 = $('#by-city-id').val();
                var by_followup_before = $('#by-followup-before').val();
                var conversation_type = $('#by-conversation-type').val();
                var groupID = $('#by-groupby-id').val();
                var lcs = $('#by-lifecycle-status').val();
                var ls = $('#by-lead-stage').val();
                var businesstype = $('#by-business-type').val();
                var lead_priority = $('#by-lead-priority').val();
                var lead_owner = $('#by-lead-owner').val();
                var country_dial_code = $('#by-coutry-dial-code').val();
                var country_dial_code_number = $('#by-coutry-dial-code-number').val();
                var exlude_country_mobile_code = $('#by-exlude-coutry-mobile-code').val();
                var industries = $('#by-industry').val();
                var careoff = $('#by-careoff-id').val();
                var by_created_date = $('#by-created-date').val();
                var by_updated_date = $('#by-updated-date').val();
                var by_staff_updated_date = $('#by-staff-updated-date').val();
                var short_form_code = $('#short_form_code').is(':checked') ?"1":"0";
                //     var sendtag = $('#by-send-tag').val();
                //     var senddate = $('#by-send-date').val();
                //     var createddate = $('#by-created-date').val();
                //     var updatestatusdate = $('#by-update-status-date').val();
                var createdBy = $('#by-create-by').val();
                var contactStatus = $('#by-contact-status').val();
                var sourceId = $('#by-source-id').val();
                var hasEmail = $('#by-has-email').val();
                var hasMobile = $('#by-has-mobile').val();


                jQuery.ajax({
                    url: "{{ route('admin.allcontact.saveadminfilter') }}",
                    method: "POST",
                    type: "html",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        state_id: state_id,
                        country_id: country2,
                        city_id: city2,
                        followup_before: by_followup_before,
                        conversation_type: conversation_type,
                        group_id: groupID,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: businesstype,
                        lead_priority: lead_priority,
                        lead_owner: lead_owner,
                        industry_id: industries,
                        careoff: careoff,
                        created_by: createdBy,
                        by_created_date: by_created_date,
                        by_updated_date: by_updated_date,
                        by_staff_updated_date: by_staff_updated_date,
                        country_dial_code: country_dial_code,
                        country_dial_code_number: country_dial_code_number,
                        exlude_country_mobile_code: exlude_country_mobile_code,
                        short_form_code: short_form_code,
                        status: contactStatus,
                        source_id: sourceId,
                        has_email: hasEmail,
                        has_mobile: hasMobile,
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
            $(document).on('click', '.resetfilter', function () {

                $('.selectpicker').selectpicker('deselectAll');

                if ($('#by-business-type').val() != '') {
                    $('#by-business-type').val('').trigger('change');
                }

                if ($('#by-careoff-id').val() != '') {
                    $('#by-careoff-id').val('').trigger('change');
                }

                if ($('#by-create-by').val() != '') {
                    $('#by-create-by').val('').trigger('change');
                }

                if ($('#by-groupby-id').val() != '') {
                    $('#by-groupby-id').val('').trigger('change');
                }

                if ($('#by-lifecycle-status').val() != '') {
                    $('#by-lifecycle-status').val('').trigger('change');
                }

                if ($('#by-lead-stage').val() != '') {
                    $('#by-lead-stage').val('').trigger('change');
                }

                if ($('#by-lead-priority').val() != '') {
                    $('#by-lead-priority').val('').trigger('change');
                }

                if ($('#by-industry').val() != '') {
                    $('#by-industry').val('').trigger('change');
                }

                if ($('#by-state-id').val() != '') {
                    $('#by-state-id').val('').trigger('change');
                }

                if ($('#by-country-id').val() != '') {
                    $('#by-country-id').val('').trigger('change');
                }

                if ($('#by-city-id').val() != '') {
                    $('#by-city-id').val('').trigger('change');
                }

                if ($('#by-followup-before').val() != '') { 
                    $('#by-followup-before').val('').trigger('change');
                }

                if ($('#by-conversation-type').val() != '') {
                    $('#by-conversation-type').val('').trigger('change');
                }
                
                if ($('#by-lead-owner').val() != '') {
                    $('#by-lead-owner').val('').trigger('change');
                }

                if ($('#by-coutry-dial-code').val() != '') {
                    $('#by-coutry-dial-code').val('').trigger('change');
                }

                if ($('#by-coutry-dial-code-number').val() != '') {
                    $('#by-coutry-dial-code-number').val('').trigger('change');
                }

                if ($('#by-exlude-coutry-mobile-code').val() != '') {
                    $('#by-exlude-coutry-mobile-code').val('').trigger('change');
                }

                if ($('#by-created-date').val() != '') {
                    $('#by-created-date').trigger('cancel.daterangepicker');
                }

                if ($('#by-updated-date').val() != '') {
                    $('#by-updated-date').trigger('cancel.daterangepicker');
                }

                if ($('#by-staff-updated-date').val() != '') {
                    $('#by-staff-updated-date').trigger('cancel.daterangepicker');
                }

                if ($('#by-contact-status').val() != '') {
                    $('#by-contact-status').val('').trigger('change');
                }

                if ($('#by-source-id').val() != '') {
                    $('#by-source-id').val('').trigger('change');
                }

                if ($('#by-has-email').val() != '') {
                    $('#by-has-email').val('').trigger('change');
                }

                if ($('#by-has-mobile').val() != '') {
                    $('#by-has-mobile').val('').trigger('change');
                }

                if ($('#by-recently-added').val() != '') {
                    $('#by-recently-added').val('').trigger('change');
                }

                // Reset all three categories back to their defaults: "Total
                // Contacts" active for Contact Status, nothing active for
                // Business Type / Lead Stage (matching a fresh, unfiltered load).
                $('.all-status-card').removeClass('active');
                $('.all-status-card[data-type="contact"][data-card="total"]').addClass('active');

                // Reset Short Form
                $('#short_form_code').prop('checked', false);

                $('#custom_filter_rows').empty();
                reloadTodoList();

                $.ajax({
                    url: "{{ route('admin.allcontact.resetadminfilter') }}",
                    method: "POST",
                    type: "html",
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function (data) {
                        if (data) {
                            toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                        }
                    }
                });

                updateAllContactFilterIndicator();

            });

            // $('body').on('click','.pagination a',function(e){
            //     e.preventDefault();
            //     var url = $(this).attr('href');
            //     var url_data = getFilterData();
            //     var finalURL = url + "&" + $.param(url_data);;


            //     getPaginations(finalURL);
            //     window.history.pushState("", url);
            // });

            // function getPaginations(finalURL){
            //     $.ajax({
            //         url : finalURL
            //     }).done(function(data){
            //         $('.contactpaginate').html(data);

            //     }).fail(function(){

            //         alert("Something gone wrong!")
            //     });
            // }

        });
    </script>

    {{-- <script>
        document.getElementById("disableshortcode").addEventListener("click", function() {
            this.classList.add("d-none");
            document.getElementById("enableshortcode").classList.remove("d-none");
            document.getElementById("hideshowaddmodule").classList.add("d-none");
        });

        document.getElementById("enableshortcode").addEventListener("click", function() {
            this.classList.add("d-none");
            document.getElementById("disableshortcode").classList.remove("d-none");
            document.getElementById("hideshowaddmodule").classList.remove("d-none");
        });
    </script> --}}


    <script>
        $(document).ready(function(){

            $('#editContactForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('#submitBtn').prop('disabled', true).text('Saving...');
                    },
                    success: function(response) {

                        $('#submitBtn').prop('disabled', false).text('Save');

                        if(response.status){
                            toastr.success(response.message);

                            // Hide modal if using one
                            $('.contact-edit-close-btn').trigger('click');

                            // Reload DataTable if using DataTables
                            $('.datatables-users').DataTable().ajax.reload(null, false);

                        }else{
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {

                        $('#submitBtn').prop('disabled', false).text('Save');

                        if(xhr.status === 422){
                            let errors = xhr.responseJSON.errors;

                            $.each(errors, function(key, value){
                                toastr.error(value[0]);
                            });
                        }else{
                            toastr.error(xhr.responseJSON?.message ?? 'Something went wrong.');
                        }
                    }
                });
            });

            $('#short_form_code2').on('change',function(){
                if($(this).is(':checked')){
                    // document.getElementById("hideshowaddmodule").classList.remove("d-none");
                    $('.hideshowaddmodule').show();
                }else{
                    // document.getElementById("hideshowaddmodule").classList.add("d-none");
                    $('.hideshowaddmodule').hide();
                }
            });

            // Status Bar toggle: shows/hides the Deal Stage / Recruit Status
            // Summary Bar. Persisted client-side so the choice survives
            // reloads and keeps working across the AJAX filter/count refresh,
            // since that refresh only updates counts in place and never
            // re-renders this bar's container. display is forced with
            // !important (and bound via delegation) so nothing else on this
            // page can silently override it.
            (function(){
                function setAllcontactStatusBarVisible(visible){
                    var bar = document.getElementById('allcontactStatusBar');
                    if (!bar) return;
                    if (visible) {
                        bar.style.removeProperty('display');
                    } else {
                        bar.style.setProperty('display', 'none', 'important');
                    }
                }

                var stored = null;
                try { stored = localStorage.getItem('allcontact_status_bar_visible'); } catch(e) {}
                var visible = stored === null ? true : stored === '1';
                $('#status_bar_toggle').prop('checked', visible);
                setAllcontactStatusBarVisible(visible);

                $(document).on('change', '#status_bar_toggle', function(){
                    var isVisible = $(this).is(':checked');
                    setAllcontactStatusBarVisible(isVisible);
                    try { localStorage.setItem('allcontact_status_bar_visible', isVisible ? '1' : '0'); } catch(e) {}
                });
            })();

            $('#short_form_code2').on('change',function(){
                var short_form_code = $(this).is(':checked') ?'1':'0';

                jQuery.ajax({
                    url: "{{ route('admin.allcontact.saveshortformcode') }}",
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

        });




    </script>

    <script>
        $(document).ready(function(){
            $("#check-mobile-input").on("keyup", function () {
                let mobile = $(this).val();
                $("#mobileResultTable").hide();
                $("#mobileResultTable tbody").html("");

                if (mobile.length < 5) return; // Start checking after 5 digits

                $.ajax({
                    url: "{{ route('admin.checkMobileDetails') }}",
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
                                <td>${response.data.mobile_in}</td>
                                <td>${response.data.created_by}</td>
                                <td>${response.data.created_at}</td>
                            </tr>
                        `;

                        $("#mobileResultTable tbody").html(row);
                        $("#mobileResultTable").show();
                    }
                });
            });
        });
    </script>

    <script>
        // =====================================================================
        // ALL CONTACT FILTER INDICATOR (SHOW / HIDE DOT)
        // =====================================================================

        
        function updateAllContactFilterIndicatorOnPageLoad()
        {
            let isFiltered = false;

            $.each(

                savedFilter,

                function(key, value){

                    if(

                        value &&
                        value.length > 0

                    ){

                        isFiltered = true;

                        return false;

                    }

                }

            );

             // ✅ Check group is null select field
            if ($('#by-groupby-id').length && $('#by-groupby-id').val() === 'null') {
                isFiltered = true;
            }

            if ($('#by-created-date').val()) {

                isFiltered = true;

            }

            if ($('#by-updated-date').val()) {

                isFiltered = true;

            }

            if ($('#by-staff-updated-date').val()) {

                isFiltered = true;

            }


            if ($('#by-followup-before').val()) {

                isFiltered = true;

            }

            if ($('#by-contact-status').val() && $('#by-contact-status').val().length > 0) {

                isFiltered = true;

            }

            if ($('#by-has-email').val()) {

                isFiltered = true;

            }

            if ($('#by-has-mobile').val()) {

                isFiltered = true;

            }

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

        function updateAllContactFilterIndicator() {
            
            let selectFields = [
                '#by-business-type',
                '#by-careoff-id',
                '#by-create-by',
                '#by-groupby-id',
                '#by-lifecycle-status',
                '#by-lead-stage',
                '#by-lead-priority',
                '#by-industry',
                '#by-state-id',
                '#by-country-id',
                '#by-city-id',
                '#by-followup-before',
                '#by-conversation-type',
                '#by-lead-owner',
                '#by-coutry-dial-code',
                '#by-coutry-dial-code-number',
                '#by-exlude-coutry-mobile-code',
                '#by-contact-status',
                '#by-source-id'
            ];

            let isFiltered = false;

            // ✅ Check all select fields
            selectFields.forEach(function(selector) {
                if ($(selector).length && $(selector).val() && $(selector).val().length > 0) {
                    isFiltered = true;
                }
            });

            // ✅ Check group is null select field
            if ($('#by-groupby-id').length && $('#by-groupby-id').val() === 'null') {
                isFiltered = true;
            }

            // ✅ Check date fields
            if ($('#by-created-date').val() || $('#by-updated-date').val() || $('#by-staff-updated-date').val()) {
                isFiltered = true;
            }

            // ✅ Check email / mobile availability and recently-added shortcut
            if ($('#by-has-email').val() || $('#by-has-mobile').val() || $('#by-recently-added').val()) {
                isFiltered = true;
            }

            // ✅ Check short form switch
            if ($('#short_form_code').is(':checked')) {
                isFiltered = true;
            }

            // ✅ Check custom filter rows
            if ($('.custom-filter-row').filter(function () {
                return $(this).find('.custom-filter-column').val();
            }).length > 0) {
                isFiltered = true;
            }

            // ✅ Show / Hide indicator
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


            // =====================================================================
            // TRIGGERS (Bootstrap Select + Inputs + Checkbox)
            // =====================================================================
            $('.selectpicker').on('changed.bs.select', function () {
                updateAllContactFilterIndicator();
            });

            $('#by-created-date, #by-updated-date, #by-staff-updated-date').on('change keyup', function () {
                updateAllContactFilterIndicator();
            });

            // =====================================================================
            // INITIAL LOAD (SAVED FILTER SUPPORT)
            // =====================================================================
            updateAllContactFilterIndicator();

            updateAllContactFilterIndicatorOnPageLoad();

        });

        $(document).on(
            'click',
            '.bootstrap-select .dropdown-toggle',
            function(){

                let dropdown =
                $(this)
                .closest('.bootstrap-select')
                .find('select.load-filter');

                if(
                    !dropdown.length
                ){
                    return;
                }

                if(
                    dropdown.attr(
                        'data-loaded'
                    )
                ){
                    return;
                }

                dropdown.attr('data-loaded','1');

                loadDropdown(
                    dropdown
                );

            }
        );

        function loadDropdown(
        dropdown
        ){

            console.log(
                'loadDropdown called'
            );

            $.ajax({

                url:
                "{{ route('admin.allcontact.filter.load') }}",

                type:"GET",

                data:{
                    filter:
                    dropdown.data(
                        'filter'
                    )
                },

                success:function(res){

                    dropdown
                    .html(res);

                    dropdown
                    .selectpicker(
                        'refresh'
                    );

                },

                error:function(){

                    dropdown
                    .removeAttr(
                        'data-loaded'
                    );

                }

            });

        }

        function loadgroupwisecontactreport(){

            let container = $('.groupwisecontactreportContainer');

            container.html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                </div>
            `);

            $.ajax({
                url: "{{ route('admin.allcontact.groupwise.report') }}",
                type: "GET",
                dataType: "json",
                success: function (res) {
                    container.html(res.html);
                },
                error: function () {
                    container.html('<div class="alert alert-danger">Unable to load report.</div>');
                }
            });
        }


        $('#groupwisecontactreport').on('show.bs.modal', function () {

           loadgroupwisecontactreport();

        });

         $('#refreshModalBtn').on('click', function () {

           loadgroupwisecontactreport();

        });
        
    </script>   




    
@endsection
