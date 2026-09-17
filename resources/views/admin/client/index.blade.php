@extends('layout.admin.admin_layout')

@section('title','Client List')

@section('page-style')
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />

  @if (Auth::guard('admin')->user()->user_type == 2)

    @if (isset($perm) && $perm->delete_client	 == 0)
    <style>
    .delclient{
        display: none !important;
    }
    </style>
    @endif
    @endif

    <style>
    .filterpanel {
        position: relative;
    }

    .client-filter-count-badge {
        display: inline-block;
        margin-left: 4px;
        padding: 1px 7px;
        font-size: 10.5px;
        font-weight: 600;
        border-radius: 10px;
        background: #ffc107;
        color: #212529;
        vertical-align: middle;
    }

    /* ==============================
    CLIENT STATUS BAR
    Compact toolbar layout (same pattern as the Todo/Employer/Associate
    modules): one row per group, label inline to the left of its pills.
    ============================== */
    .client-filterbar {
        padding: 8px 14px;
    }

    .client-filterbar-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .client-filterbar-row + .client-filterbar-row {
        border-top: 1px solid var(--bs-border-color, rgba(0, 0, 0, .06));
        margin-top: 5px;
        padding-top: 5px;
    }

    .client-filterbar-label {
        flex: 0 0 auto;
        min-width: 96px;
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
        text-decoration: none;
    }

    .status-btn.active {
        background: var(--tsc-color, #7367f0);
    }

    .status-btn.active span,
    .status-btn.active strong {
        color: #fff !important;
    }

    @media (max-width: 575px) {
        .client-filterbar-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }

        .client-filterbar-label {
            min-width: 0;
        }
    }
    </style>

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

        @php
            $clientStatusOptions = [
                0 => ['label' => 'Deactive', 'color' => '#EA5455'],
                1 => ['label' => 'Active',   'color' => '#28C76F'],
            ];
            $clientVerificationOptions = [
                0 => ['label' => 'Not Verified', 'color' => '#EA5455'],
                1 => ['label' => 'Verified',     'color' => '#28C76F'],
            ];
        @endphp

        <!-- Client Status Bar Start -->
        <div class="card mb-3" id="clientStatusBar">
            <div class="card-body client-filterbar">

                <div class="client-filterbar-row">
                    <span class="client-filterbar-label">Status</span>
                    <div class="candidate-status-grid">
                        <div class="status-item">
                            <a href="javascript:void(0)" class="status-btn clientStatusCard active"
                               style="border-color:#6C757D;color:#6C757D;--tsc-color:#6C757D;"
                               data-group="status" data-value="">
                                <span>All</span>
                                <strong>{{ number_format($clientStatusSummary['total'] ?? 0) }}</strong>
                            </a>
                        </div>
                        @foreach ($clientStatusOptions as $statusValue => $statusOption)
                            <div class="status-item">
                                <a href="javascript:void(0)" class="status-btn clientStatusCard"
                                   style="border-color:{{ $statusOption['color'] }};color:{{ $statusOption['color'] }};--tsc-color:{{ $statusOption['color'] }};"
                                   data-group="status" data-value="{{ $statusValue }}">
                                    <span>{{ $statusOption['label'] }}</span>
                                    <strong>{{ number_format($clientStatusSummary['statuses'][$statusValue] ?? 0) }}</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="client-filterbar-row">
                    <span class="client-filterbar-label">Verification</span>
                    <div class="candidate-status-grid">
                        <div class="status-item">
                            <a href="javascript:void(0)" class="status-btn clientStatusCard active"
                               style="border-color:#6C757D;color:#6C757D;--tsc-color:#6C757D;"
                               data-group="verification" data-value="">
                                <span>All</span>
                                <strong>{{ number_format($clientStatusSummary['total'] ?? 0) }}</strong>
                            </a>
                        </div>
                        @foreach ($clientVerificationOptions as $verificationValue => $verificationOption)
                            <div class="status-item">
                                <a href="javascript:void(0)" class="status-btn clientStatusCard"
                                   style="border-color:{{ $verificationOption['color'] }};color:{{ $verificationOption['color'] }};--tsc-color:{{ $verificationOption['color'] }};"
                                   data-group="verification" data-value="{{ $verificationValue }}">
                                    <span>{{ $verificationOption['label'] }}</span>
                                    <strong>{{ number_format($clientStatusSummary['verification'][$verificationValue] ?? 0) }}</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
        <input type="hidden" id="by-client-status" value="">
        <input type="hidden" id="by-verification-status" value="">
        <!-- Client Status Bar End -->

        <div class="card">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center flex-wrap" style="gap: .5rem;">
                    <button class="btn btn-xs btn-primary filterpanel" data-bs-toggle="modal" data-bs-target="#filterpanel">
                        <i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter
                        <span class="client-filter-count-badge d-none"></span>
                    </button>

                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="clientOptionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>
                        <div class="dropdown-menu" aria-labelledby="clientOptionDropdown">
                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" name="client_status_bar_toggle" value="1" id="client_status_bar_toggle" class="switch-input" checked />
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
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Country</th>
                        <th>City</th>
                        <th>Verified</th>
                        <th>Status</th>
                        <th>Country ID</th>
                        <th>City ID</th>
                        <th>Created At</th>
                        <th>Status</th>
                        <th>Verification Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!-- Customer View Modal Start -->
        <div class="modal fade" id="viewCustomerModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">

                <div class="modal-header border-bottom">
                    <h5 class="modal-title">Customer Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-semibold text-primary"
                                    data-bs-toggle="tab"
                                    data-bs-target="#detailsTab"
                                    type="button">
                            <i class="tf-icons ti ti-home ti-xs me-1"></i> Details
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold"
                                    data-bs-toggle="tab"
                                    data-bs-target="#activityTab"
                                    type="button">
                            <i class="bx bx-history me-1"></i> Activity
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">

                        <!-- DETAILS TAB -->
                        <div class="tab-pane fade show active" id="detailsTab">

                            <!-- Top Section -->
                            <div class="row align-items-center mb-4 text-center text-md-start">
                                <!-- Avatar -->
                                <div class="col-12 col-md-2 mb-3 mb-md-0">
                                    <div id="customerAvatar" class="d-flex justify-content-center justify-content-md-start"></div>
                                </div>
                                <!-- Info -->
                                <div class="col-12 col-md-10">
                                    <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-2">

                                        <h4 id="customerName" class="mb-0"></h4>

                                        <div id="customerStatus"></div>

                                    </div>
                                    <small class="text-muted d-block mt-1" id="customerEmail"></small>
                                </div>
                            </div>

                            <hr>

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Mobile</label>
                                    <div id="customerMobile" class="fw-semibold"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Company Name</label>
                                    <div id="customerCompany" class="fw-semibold"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Verification Status</label>
                                    <div id="customerVerification" class="fw-semibold"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">WhatsApp Notification</label>
                                    <div id="customerWhatsapp" class="fw-semibold"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Email Verified</label>
                                    <div id="customerEmailVerified" class="fw-semibold"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Mobile Verified</label>
                                    <div id="customerMobileVerified" class="fw-semibold"></div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small">Address</label>
                                    <div id="customerAddress" class="fw-semibold"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Created At</label>
                                    <div id="customerCreated"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Updated At</label>
                                    <div id="customerUpdated"></div>
                                </div>

                            </div>

                        </div>

                        <!-- Activity TAB -->
                        <div class="tab-pane fade" id="activityTab">

                            <h6 class="fw-bold mb-3">Last Login Activity</h6>

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Device Type</label>
                                    <div id="activityDevice" class="fw-semibold"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Browser</label>
                                    <div id="activityBrowser" class="fw-semibold"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Operating System</label>
                                    <div id="activityOS" class="fw-semibold"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">IP Address</label>
                                    <div id="activityIP" class="fw-semibold"></div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small">Last Logged At</label>
                                    <div id="activityTime" class="fw-semibold"></div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                </div>
            </div>
        </div>
        <!-- Customer View Modal End -->


        <!-- Client Filter Panel Start -->
        <div class="modal fade" id="filterpanel" aria-hidden="true" aria-labelledby="filterpanelLabel" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="filterpanelLabel">Client Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <select id="by-country" class="form-select filterData select22f">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $countryFilter)
                                        <option value="{{ $countryFilter->id }}" @if(isset($saveadminfilter) && $saveadminfilter->by_country == $countryFilter->id) selected @endif>{{ $countryFilter->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <select id="by-city" class="form-select filterData select22f">
                                    <option value="">Select City</option>
                                    @foreach ($cities as $cityFilter)
                                        <option value="{{ $cityFilter->id }}" @if(isset($saveadminfilter) && $saveadminfilter->by_city == $cityFilter->id) selected @endif>{{ $cityFilter->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <input type="text" id="by-created-date" class="form-control createdate-picker filterData" placeholder="Created date..." value="{{ $saveadminfilter->by_created_date ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-warning btn-sm clientResetFilter">Reset Filter</button>
                        <button type="button" class="btn btn-success btn-sm clientSaveFilter">Save Filter</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Client Filter Panel End -->

        <!-- Delete Client Data Start -->
        <div class="modal fade" id="deleteClient" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.client.delete') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="client_id" id="clientID">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                            <div class="mb-3">
                                <p class="text-danger">Are you sure to delete!, All related data will be delete!</p>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Yes</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
        <!-- Delete Client Data End -->

        <!-- Update Status Modal Start -->
        <div class="modal fade" id="updateStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form id="updateStatusForm">
                        @csrf
                        <input type="hidden" name="id" id="statusID">

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Select Status</label>
                                <select class="form-select" name="status" id="statusSelect" required>
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
        <!-- Update Status Modal End -->

        <!--- Edit Partner Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="edituserLabel" class="offcanvas-title">Edit Customer</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.client.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <input type="hidden" name="edit_id" id="editid">
                                <label class="form-label" for="edit-name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit-name" class="form-control" placeholder="Enter name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-email">Email</label>
                                <input type="text" name="email" id="edit-email" class="form-control" placeholder="Enter Email...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-mobile-no">Mobile No</label>
                                <input type="text" name="mobile_no" id="edit-mobile-no" class="form-control" placeholder="Enter Mobile...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-country-id">Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="edit-country-id" class="form-select select2" data-placeholder="Select Country" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name.' ('.$country->arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-city-id">City <span class="text-danger">*</span></label>
                                <select name="city_id" id="edit-city-id" class="form-select select2" data-placeholder="Select City" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name.' ('.$city->arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-address">Address</label>
                                <input type="text" name="address" id="edit-address" class="form-control" placeholder="Enter Address...">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Edit Partner End --->

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
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/app-customer-list.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/customer-validation.js') }}"></script>

    <script src="{{ asset('admin/assets/custom/main.js') }}"></script>
    <script>
            const guard = @json(Auth::getDefaultDriver());  // e.g., "web" or "api"
            var assetPath = $('body').attr('data-asset-path');
            var urlPath =  assetPath + guard +"/client/list/json";
    </script>

    <script>
        $(document).ready(function(){
            var select2 = $('.select2');

            if (select2.length) {
                select2.each(function () {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        //   placeholder: 'Select value',
                        dropdownParent: $this.parent()
                    });
                });
            }

        });
    </script>

    <script>
        $(document).ready(function(){
            $('#edituser').on('show.bs.offcanvas',function(e){
                var editID = $(e.relatedTarget).data('id');

                $('#editid').val(editID);

                $.ajax({
                    url: "{{ route('admin.client.edit') }}",
                    method: "GET",
                    data:{
                        id: editID
                    },
                    success: function(data){
                        $('#edit-name').val(data.name);
                        $('#edit-email').val(data.email);
                        $('#edit-mobile-no').val(data.mobile_no);
                        $('#edit-country-id').val(data.country_id).change();
                        $('#edit-city-id').val(data.city_id).change();
                        $('#edit-address').val(data.address);
                    }
                });

            });
        });
    </script>

    <script>
      $(document).ready(function(){
        $('#deleteClient').on('show.bs.modal',function(e){
          var id = $(e.relatedTarget).data('id');

          $('#clientID').val(id);



        });
      });
    </script>

    <script>
      $(document).ready(function(){

        let currentRow = null;

        $(document).on('click', '.open-status-modal', function () {
            let id = $(this).data('id');
            let status = $(this).data('status');

            console.log(status);
            

            $('#statusID').val(id);
            $('#statusSelect').val(status);

            currentRow = $(this); // store clicked element
        });

        $('#updateStatusForm').on('submit', function (e) {
            e.preventDefault();

            let formData = {
                _token: $('input[name="_token"]').val(),
                id: $('#statusID').val(),
                status: $('#statusSelect').val()
            };

            $.ajax({
                url: "{{ route('admin.client.status.update') }}",
                type: "POST",
                data: formData,
                success: function (response) {

                    if (response.success) {

                        // Change badge without refresh
                        let status = formData.status;

                        let statusobj = {
                            1: { title: 'Active', class: 'bg-label-success' },
                            0: { title: 'Deactive', class: 'bg-label-warning' },
                        };

                        currentRow.html(`
                            <span class="badge ${statusobj[status].class}">
                                ${statusobj[status].title}
                            </span>
                        `);

                        // Close modal
                        $('#updateStatus').modal('hide');

                    }
                }
            });
        });

        //////////////////////////////////////////////////////////////////////////////////////////
        $(document).on('click', '.view-customer', function () {

            let id = $(this).data('id');

            $.ajax({
                url: "/admin/client/view/" + id,
                type: "GET",
                success: function (data) {

                    $('#customerName').text(data.name ?? '-');
                    $('#customerEmail').text(data.email ?? '-');
                    $('#customerMobile').text(data.mobile_no ?? '-');
                    $('#customerCompany').text(data.company_name ?? '-');
                    $('#customerAddress').text(data.address ?? '-');
                    $('#customerCreated').text(data.created_at ?? '-');
                    $('#customerUpdated').text(data.updated_at ?? '-');

                    // Status
                    let statusBadge = data.status == 1
                        ? '<span class="badge bg-label-success">Active</span>'
                        : '<span class="badge bg-label-warning">Deactive</span>';

                    $('#customerStatus').html(statusBadge);

                    // Verification
                    $('#customerVerification').text(data.verification_status == 1 ? 'Verified' : 'Not Verified');
                    $('#customerWhatsapp').text(data.whatsapp_notification == 1 ? 'Enabled' : 'Disabled');
                    $('#customerEmailVerified').text(data.email_verified_at ? 'Yes' : 'No');
                    $('#customerMobileVerified').text(data.mobile_verified_at ? 'Yes' : 'No');

                    // Avatar
                    if (data.avatar_url) {
                        $('#customerAvatar').html(
                            `<img src="${data.avatar_url}" 
                                class="rounded-circle img-fluid"
                                style="width:120px; height:120px; object-fit:cover;">`
                        );
                    } else {
                        let initials = data.name.charAt(0).toUpperCase();
                        $('#customerAvatar').html(
                            `<span class="avatar-initial rounded-circle bg-label-primary fs-3">${initials}</span>`
                        );
                    }

                    // Parse last_login_from JSON
                    if (data.last_login_from) {

                        let activity = JSON.parse(data.last_login_from);

                        $('#activityDevice').text(activity.device_type ?? '-');
                        $('#activityBrowser').text(activity.browser ?? '-');
                        $('#activityOS').text(activity.os ?? '-');
                        $('#activityIP').text(activity.ip_address ?? '-');

                        // Format date nicely
                        let loginDate = new Date(activity.last_logged_at);
                        $('#activityTime').text(
                            loginDate.toLocaleString('en-IN', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })
                        );

                    } else {

                        $('#activityDevice').text('-');
                        $('#activityBrowser').text('-');
                        $('#activityOS').text('-');
                        $('#activityIP').text('-');
                        $('#activityTime').text('-');

                    }


                    $('#viewCustomerModal').modal('show');
                }
            });

        });



      });
    </script>

    <script>
        $(document).ready(function () {

            // Lazily init select2 on the filter modal's selects only once
            // it is visible — sizing them while hidden breaks their
            // dropdown width (same pattern as Associate's filter panel).
            $('body').on('shown.bs.modal', '#filterpanel', function () {
                $(this).find('.select22f').each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({ dropdownParent: $(this).parent() });
                    }
                });
            });

            function getClientSaveFilterData() {
                return {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    by_country: $('#by-country').val(),
                    by_city: $('#by-city').val(),
                    by_created_date: $('#by-created-date').val(),
                };
            }

            function updateClientFilterCount() {
                var count = 0;
                if ($('#by-country').val()) count++;
                if ($('#by-city').val()) count++;
                if ($('#by-created-date').val()) count++;

                var $badge = $('.client-filter-count-badge');
                if (count > 0) {
                    $badge.text('Filters (' + count + ')').removeClass('d-none');
                } else {
                    $badge.addClass('d-none');
                }
            }

            $('#by-country, #by-city, #by-created-date').on('change', function () {
                updateClientFilterCount();
            });

            // Initial check on page load — a saved filter is pre-selected
            // server-side, so the badge must reflect that immediately.
            updateClientFilterCount();

            // ===============================
            // Client status bar -> drives the DataTable's own client-side
            // column search directly (no server AJAX exists on this page
            // at all — the whole dataset is already loaded into the
            // DataTable, so a redraw is the only "reload" there is).
            // Single-select per group: clicking a pill clears/sets only
            // pills in its own group, so Status + Verification combine
            // (AND together) instead of one click resetting the other.
            // ===============================
            $(document).on('click', '.clientStatusCard', function (e) {
                e.preventDefault();

                var $this = $(this);
                var group = $this.data('group');
                var value = $this.data('value');
                value = (value === undefined || value === null) ? '' : String(value);
                var wasActive = $this.hasClass('active');

                if (wasActive && !value) {
                    return; // "All" already active — avoid a pointless redraw
                }

                $('.clientStatusCard[data-group="' + group + '"]').removeClass('active');

                var effectiveValue = wasActive ? '' : value;
                if (!wasActive) {
                    $this.addClass('active');
                }

                var selector = group === 'status' ? '#by-client-status' : '#by-verification-status';
                var columnIndex = group === 'status' ? 11 : 12;

                $(selector).val(effectiveValue);

                var table = $('.datatables-users').DataTable();
                table.column(columnIndex).search(effectiveValue ? '^' + effectiveValue + '$' : '', true, false).draw();
            });

            $(document).on('click', '.clientSaveFilter', function () {
                $.post("{{ route('admin.client.saveFilter') }}", getClientSaveFilterData())
                    .done(function (res) {
                        toastr.success(res.message || 'Filter saved successfully!', 'Success', { timeOut: 2000 });
                    })
                    .fail(function () {
                        toastr.error('Failed to save filter', 'Error');
                    });
            });

            $(document).on('click', '.clientResetFilter', function () {
                var table = $('.datatables-users').DataTable();

                var hadFilter = !!($('#by-country').val() || $('#by-city').val() || $('#by-created-date').val()
                    || $('#by-client-status').val() || $('#by-verification-status').val());

                if (!hadFilter) {
                    return; // nothing active — avoid a pointless redraw
                }

                $('#by-country').val('').trigger('change');
                $('#by-city').val('').trigger('change');
                $('#by-created-date').val('');
                if ($('#by-created-date')[0]._flatpickr) {
                    $('#by-created-date')[0]._flatpickr.clear();
                }
                $('#by-client-status').val('');
                $('#by-verification-status').val('');

                $('.clientStatusCard').removeClass('active');
                $('.clientStatusCard[data-value=""]').addClass('active');

                // Clear every column's search state, then redraw exactly
                // once — this is the only "reload" this page has.
                table.columns([8, 9, 10, 11, 12]).search('');
                table.draw();

                updateClientFilterCount();

                $.post("{{ route('admin.client.saveFilter') }}", getClientSaveFilterData())
                    .done(function (res) {
                        toastr.success(res.message || 'Filter reset successfully!', 'Success', { timeOut: 2000 });
                    })
                    .fail(function () {
                        toastr.error('Failed to reset filter', 'Error');
                    });
            });

            // Status Bar toggle: shows/hides the Client Status Bar.
            // Persisted client-side (same pattern as Associate/All
            // Contact/Leads/Todo) so the choice survives reloads; display
            // is forced with !important so nothing else on this page can
            // silently override it.
            (function () {
                function setClientStatusBarVisible(visible) {
                    var bar = document.getElementById('clientStatusBar');
                    if (!bar) return;
                    if (visible) {
                        bar.style.removeProperty('display');
                    } else {
                        bar.style.setProperty('display', 'none', 'important');
                    }
                }

                var stored = null;
                try { stored = localStorage.getItem('client_status_bar_visible'); } catch (e) {}
                var visible = stored === null ? true : stored === '1';
                $('#client_status_bar_toggle').prop('checked', visible);
                setClientStatusBarVisible(visible);

                $(document).on('change', '#client_status_bar_toggle', function () {
                    var isVisible = $(this).is(':checked');
                    setClientStatusBarVisible(isVisible);
                    try { localStorage.setItem('client_status_bar_visible', isVisible ? '1' : '0'); } catch (e) {}
                });
            })();
        });
    </script>

@endsection
