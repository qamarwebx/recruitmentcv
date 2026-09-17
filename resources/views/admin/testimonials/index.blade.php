@extends('layout.admin.admin_layout')


@section('title','Testimonial')

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
        /* styles.css */
        #progressWrapper {
            width: 100%;
            background-color: #f3f3f3;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

        #progressBar {
            width: 0%;
            height: 20px;
            background-color: #4caf50;
            text-align: center;
            line-height: 20px;
            color: white;
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

        /* Summary count pills — same design as the Leads list's status cards */
        .candidate-status-wrapper {
            width: 100%;
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

        @php
            $summaryCards = [
                [
                    'label' => 'Links Generated',
                    'color' => '#7367F0',
                    'count' => $summaryCounts['links_generated'] ?? 0,
                    // No dedicated "has link" filter field exists (nearly every
                    // record has one), so clicking just clears filters and
                    // shows the full list — an empty filter payload, not null,
                    // so it's still treated as clickable below.
                    'filter' => [],
                ],
                [
                    'label' => 'Videos Received',
                    'color' => '#00CFE8',
                    'count' => $summaryCounts['videos_received'] ?? 0,
                    'filter' => ['video_received' => ['Received']],
                ],
                [
                    'label' => 'Payments Pending',
                    'color' => '#FF9F43',
                    'count' => $summaryCounts['payments_pending'] ?? 0,
                    'filter' => ['approval_status' => ['Approved'], 'payment_status' => ['Pending']],
                ],
                [
                    'label' => 'Payments Paid',
                    'color' => '#28C76F',
                    'count' => $summaryCounts['payments_paid'] ?? 0,
                    'filter' => ['payment_status' => ['Paid']],
                ],
                [
                    'label' => 'Posts Published',
                    'color' => '#28C76F',
                    'count' => $summaryCounts['posts_published'] ?? 0,
                    'filter' => ['social_media_status' => ['Posted']],
                ],
                [
                    'label' => 'Posts Pending',
                    'color' => '#EA5455',
                    'count' => $summaryCounts['posts_pending'] ?? 0,
                    'filter' => ['social_media_status' => ['Not Posted']],
                ],
            ];
        @endphp

        <!-- Testimonial Summary Cards Start -->
        <div class="card mb-3" id="testimonialStatusBar">
            <div class="card-body">
                <div class="candidate-status-wrapper">
                    <div class="candidate-status-card">
                        <div class="candidate-status-grid">
                            @foreach ($summaryCards as $card)
                                <div class="status-item">
                                    <a href="javascript:;"
                                       class="status-btn {{ $card['filter'] !== null ? 'summaryCountCard' : '' }}"
                                       style="border-color: {{ $card['color'] }}; color: {{ $card['color'] }}; --tsc-color: {{ $card['color'] }};"
                                       @if($card['filter'] !== null) data-filter='@json($card['filter'])' @endif>
                                        <span>{{ $card['label'] }}</span>
                                        <strong>{{ number_format($card['count']) }}</strong>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Testimonial Summary Cards End -->

        <div class="card">
            <div class="card-header py-3 px-4">

                <div class="float-start d-flex align-items-center">
                    <select id="pagination_list" class="form-select form-select-sm" style="width: 66.2px;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                    </select>

                    <button class="btn btn-xs btn-primary filterpanel ms-2"
                            data-bs-toggle="modal"
                            data-bs-target="#filterpanel">
                        <i class="ti ti-filter"></i> Filter
                        <span class="filter-indicator d-none"></span>
                    </button>

                    <div class="btn-group mx-1 ms-2">
                        <button class="btn btn-primary btn-xs dropdown-toggle" type="button" id="testimonialOptionBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>
                        <div class="dropdown-menu" aria-labelledby="testimonialOptionBtn">
                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" value="1" class="switch-input testimonial-status-bar-switch" checked />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Status Bar</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->google_review == 1)))
                    <a href="{{ route('admin.google_review') }}" class="btn btn-xs btn-outline-primary ms-2">
                        <i class="ti ti-brand-google"></i> Google Review
                    </a>
                    @endif
                </div>

                <div class="float-end">
                    @if(auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->add_testimonial == 1)))
                    <button class="btn btn-sm btn-primary mx-1"
                            data-bs-toggle="modal"
                            data-bs-target="#copyLinkModal">
                        <i class="ti ti-plus me-1 ti-xs"></i>
                        <span class="d-none d-sm-inline-block">Generate Link</span>
                    </button>
                    @endif
                </div>
                <div class="float-end">
                    <input type="text" id="search_text" class="form-control form-control-sm" placeholder="Search...">
                </div>
            </div>
            <div class="card-datatable table-responsive candloadpaginate">
                @include('admin.testimonials.testimonial_load')
            </div>
        </div>

       <!-- Generate Link Modal -->
        <div class="modal fade" id="copyLinkModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Link</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="generateLinkName" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="generateLinkName" placeholder="Enter candidate name...">
                            <div class="invalid-feedback" id="generateLinkNameError"></div>
                        </div>

                        <div class="mb-3">
                            <label for="generateLinkPassport" class="form-label">Passport No. <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="generateLinkPassport" placeholder="Enter passport number...">
                            <div class="invalid-feedback" id="generateLinkPassportError"></div>
                        </div>

                        <div class="mb-3 d-none" id="generatedLinkWrapper">
                            <label for="testimonialLink" class="form-label">Generated Link</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="testimonialLink" readonly>
                                <button type="button" class="btn btn-outline-primary" id="copyLinkBtn">
                                    <i class="ti ti-copy me-1 ti-xs"></i> Copy Link
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-sm" id="generateLinkBtn">
                            <span class="generate-link-label">Generate Link</span>
                            <span class="spinner-border spinner-border-sm d-none" id="generateLinkSpinner"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copy Testimonial Link Modal (row-level Copy Link action) -->
        <div class="modal fade" id="rowCopyLinkModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Testimonial Link</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rowTestimonialLink" class="form-label">Link</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="rowTestimonialLink" readonly>
                                <button type="button" class="btn btn-outline-primary" id="rowCopyLinkBtn">
                                    <i class="ti ti-copy me-1 ti-xs"></i> Copy Link
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Panel Start -->
        <div class="modal fade" id="filterpanel" aria-hidden="true" aria-labelledby="filterpanelLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="filterpanelLabel">Testimonial Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <!-- Candidate Name Filter -->
                            <div class="col-md-4 mb-3">
                                <input type="text" name="by_full_name" id="by_full_name" class="form-control"
                                    placeholder="Candidate Name..."
                                    value="{{ $saveadminfilter->by_full_name ?? '' }}">
                            </div>

                            <!-- Passport No Filter -->
                            <div class="col-md-4 mb-3">
                                <input type="text" name="by_passport_number" id="by_passport_number" class="form-control"
                                    placeholder="Passport No..."
                                    value="{{ $saveadminfilter->by_passport_number ?? '' }}">
                            </div>

                            <!-- Care Of Filter -->
                            <div class="col-md-4 mb-3">
                                @php $selectedCareOf = isset($saveadminfilter) ? $saveadminfilter->by_created_by_array : []; @endphp
                                <select name="by_created_by[]" id="by_created_by" class="selectpicker w-100" multiple
                                    data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Care Of">
                                    @foreach ($careOfStaffs as $careOfStaff)
                                    <option value="{{ $careOfStaff->id }}"
                                        @if(in_array($careOfStaff->id, $selectedCareOf)) selected @endif>
                                        {{ $careOfStaff->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date Range Filter -->
                            <div class="col-md-4 mb-3">
                                <input name="by_date_range" type="text" id="by_date_range" class="form-control bsdatpicket"
                                    value="{{ $saveadminfilter->by_date_range ?? '' }}" placeholder="Created Date Range...">
                            </div>

                            <!-- Approval Status Filter -->
                            <div class="col-md-4 mb-3">
                                @php $selectedApprovalStatus = isset($saveadminfilter) ? $saveadminfilter->by_approval_status_array : []; @endphp
                                <select name="by_approval_status[]" id="by_approval_status" class="selectpicker w-100" multiple
                                    data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Approval Status">
                                    <option value="Pending" @if(in_array('Pending', $selectedApprovalStatus)) selected @endif>Pending</option>
                                    <option value="Approved" @if(in_array('Approved', $selectedApprovalStatus)) selected @endif>Approved</option>
                                    <option value="Rejected" @if(in_array('Rejected', $selectedApprovalStatus)) selected @endif>Rejected</option>
                                </select>
                            </div>

                            <!-- Payment Status Filter -->
                            <div class="col-md-4 mb-3">
                                @php $selectedPaymentStatus = isset($saveadminfilter) ? $saveadminfilter->by_payment_status_array : []; @endphp
                                <select name="by_payment_status[]" id="by_payment_status" class="selectpicker w-100" multiple
                                    data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Payment Status">
                                    <option value="Pending" @if(in_array('Pending', $selectedPaymentStatus)) selected @endif>Pending</option>
                                    <option value="Paid" @if(in_array('Paid', $selectedPaymentStatus)) selected @endif>Paid</option>
                                </select>
                            </div>

                            <!-- Video Received Filter -->
                            <div class="col-md-4 mb-3">
                                @php $selectedVideoReceived = isset($saveadminfilter) ? $saveadminfilter->by_video_received_array : []; @endphp
                                <select name="by_video_received[]" id="by_video_received" class="selectpicker w-100" multiple
                                    data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Video Received">
                                    <option value="Not Received" @if(in_array('Not Received', $selectedVideoReceived)) selected @endif>Not Received</option>
                                    <option value="Received" @if(in_array('Received', $selectedVideoReceived)) selected @endif>Received</option>
                                </select>
                            </div>

                            <!-- Social Media Post Filter -->
                            <div class="col-md-4 mb-3">
                                @php $selectedSocialMediaStatus = isset($saveadminfilter) ? $saveadminfilter->by_social_media_status_array : []; @endphp
                                <select name="by_social_media_status[]" id="by_social_media_status" class="selectpicker w-100" multiple
                                    data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Social Media Post">
                                    <option value="Not Posted" @if(in_array('Not Posted', $selectedSocialMediaStatus)) selected @endif>Not Posted</option>
                                    <option value="Posted" @if(in_array('Posted', $selectedSocialMediaStatus)) selected @endif>Posted</option>
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

        <!--- Edit Testimonial Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editTestimonial" aria-labelledby="editTestimonialLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editTestimonialLabel" class="offcanvas-title">Edit Testimonial</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="edit-testimonial-form pt-0" id="editTestimonialForm" action="{{ route('admin.testimonials.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="testimonial_id" id="edit_testimonial_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-full-name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" id="edit-full-name" class="form-control" placeholder="Enter full name...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-passport">Passport No. <span class="text-danger">*</span></label>
                                <input type="text" name="passport_number" id="edit-passport" class="form-control" placeholder="Enter Passport...">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-primary btn-sm copyTestimonialLinkBtn" id="edit-copy-link-btn" data-link="">
                                    <i class="ti ti-link me-1 ti-xs"></i> Copy Link
                                </button>
                            </div>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Edit Testimonial End --->

       <!--- View Testimonial Start --->
        <div class="modal fade" id="viewTestimonial" tabindex="-1" aria-labelledby="viewTestimonialLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="viewTestimonialLabel">View Testimonial</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-0">
                        <input type="hidden" id="view_testimonial_id">

                        <div class="card mb-0" style="box-shadow:none;">
                            <div class="card-header">
                                <ul class="nav nav-tabs nav-fill" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="tab-testimonial-details" data-bs-toggle="tab" data-bs-target="#testimonial-details" type="button" role="tab" aria-controls="testimonial-details" aria-selected="true">
                                            <i class="tf-icons ti ti-user ti-xs me-1"></i> Details
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="tab-testimonial-activity" data-bs-toggle="tab" data-bs-target="#testimonial-activity" type="button" role="tab" aria-controls="testimonial-activity" aria-selected="false">
                                            <i class="tf-icons ti ti-history ti-xs me-1"></i> Activity
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content p-1">

                                    <!-- Details Tab -->
                                    <div class="tab-pane fade show active" id="testimonial-details" role="tabpanel" aria-labelledby="tab-testimonial-details">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" id="view-full-name" class="form-control" readonly>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Passport No.</label>
                                                <input type="text" id="view-passport" class="form-control" readonly>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Video Received</label>
                                                <input type="text" id="view-video-received" class="form-control" readonly>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Social Media</label>
                                                <input type="text" id="view-social-media" class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Existing Videos</label>
                                            <ul class="list-group" id="viewVideosList">
                                                {{-- Existing videos will be appended here dynamically --}}
                                            </ul>
                                        </div>

                                    </div>

                                    <!-- Activity Tab -->
                                    <div class="tab-pane fade" id="testimonial-activity" role="tabpanel" aria-labelledby="tab-testimonial-activity">

                                        <div id="testimonialActivityLoading" class="text-center py-5">
                                            <div class="spinner-border text-primary"></div>
                                            <p class="mt-2 mb-0">Loading activity...</p>
                                        </div>

                                        <div id="testimonialActivityEmpty" class="text-center py-5 d-none">
                                            <i class="ti ti-history fs-1 text-muted"></i>
                                            <h6 class="mt-3">No activity recorded yet</h6>
                                        </div>

                                        <div id="testimonialActivityError" class="alert alert-danger mb-0 d-none">
                                            Unable to load activity.
                                        </div>

                                        <ul class="timeline mt-3 mb-0" id="testimonialActivityTimeline"></ul>

                                        <div class="text-center mt-2 d-none" id="testimonialActivityLoadMoreWrapper">
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="testimonialActivityLoadMoreBtn">
                                                <span class="load-more-label">Load More</span>
                                                <span class="spinner-border spinner-border-sm d-none" id="testimonialActivityLoadMoreSpinner"></span>
                                            </button>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
        <!--- View Testimonial End --->

       <!-- Mark Payment as Paid Modal -->
        <div class="modal fade" id="markPaidModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="markPaidForm" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title">Mark Payment as Paid</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="markPaidTestimonialId" name="testimonial_id">

                            <p class="mb-3">
                                Confirm the payment details below and upload the payment slip. The payment will
                                only be marked as <strong>Paid</strong> once the slip is uploaded successfully.
                            </p>

                            <div class="mb-3">
                                <label class="form-label" for="markPaidAmount">Amount <span class="text-danger">*</span></label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control" id="markPaidAmount" name="amount"
                                           min="0.01" step="0.01" placeholder="Enter paid amount..." required>
                                    <div class="invalid-feedback" id="markPaidAmountError"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="markPaidSlip">Payment Slip <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="markPaidSlip" name="payment_slip"
                                       accept=".jpg,.jpeg,.png,.pdf" required>
                                <div class="form-text">Accepted: JPG, PNG, PDF. Max size 5 MB.</div>
                                <div class="invalid-feedback" id="markPaidSlipError"></div>
                            </div>

                            <div id="markPaidMessage"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="markPaidSubmitBtn">
                                <span class="mark-paid-label">Mark as Paid</span>
                                <span class="spinner-border spinner-border-sm d-none" id="markPaidSpinner"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Mark Payment as Paid Modal End -->

        <!-- Social Media Platforms Modal -->
        <div class="modal fade" id="socialMediaModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="socialMediaForm">
                        <div class="modal-header">
                            <h5 class="modal-title">Social Media Post</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="socialMediaTestimonialId" name="testimonial_id">

                            <div class="mb-3">
                                <label class="form-label" for="socialMediaPlatforms">Platform(s) <span class="text-danger">*</span></label>
                                <select id="socialMediaPlatforms" name="platforms[]" class="selectpicker w-100" multiple
                                        data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Platform(s)">
                                    @foreach (\App\Models\Testimonial::SOCIAL_MEDIA_PLATFORMS as $platform)
                                        <option value="{{ $platform }}">{{ $platform }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="socialMediaPlatformsError"></div>
                            </div>

                            <div id="socialMediaMessage"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="socialMediaSubmitBtn">
                                <span class="social-media-label">Save</span>
                                <span class="spinner-border spinner-border-sm d-none" id="socialMediaSpinner"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Social Media Platforms Modal End -->

        <!-- View Payment Info Modal -->
        <div class="modal fade" id="viewPaymentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Payment Information</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Paid Amount</label>
                                <input type="text" id="viewPaymentAmount" class="form-control" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Paid By</label>
                                <input type="text" id="viewPaymentPaidBy" class="form-control" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Paid Date &amp; Time</label>
                                <input type="text" id="viewPaymentPaidAt" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Payment Slip</label>
                            <div>
                                <a href="javascript:;" id="viewPaymentSlipView" target="_blank" class="btn btn-sm btn-outline-primary">View Slip</a>
                                <a href="javascript:;" id="viewPaymentSlipDownload" class="btn btn-sm btn-outline-success">Download Slip</a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- View Payment Info Modal End -->

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
    <script src="{{ asset('user/intl-tel-input-master/build/js/intlTelInput-jquery.min.js') }}"></script>


    <script src="{{ asset('admin/assets/pages/validation/candidate-validation.js') }}"></script>

    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>


    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $('.selectpicker').selectpicker();

            $('.bsdatpicket').daterangepicker({
                autoUpdateInput: false,
                opens: 'right',
                locale: {
                    cancelLabel: 'Clear'
                }
            }).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                reloadTestimonialsList();
                updateFilterIndicator();
            }).on('cancel.daterangepicker', function() {
                $(this).val('');
                reloadTestimonialsList();
                updateFilterIndicator();
            });

            // ===============================
            // Filter data + reload
            // ===============================
            function getFilterData() {
                return {
                    page_list: $('#pagination_list').val(),
                    search_text: $('#search_text').val(),
                    full_name: $('#by_full_name').val(),
                    passport_number: $('#by_passport_number').val(),
                    created_by: $('#by_created_by').val(),
                    date_range: $('#by_date_range').val(),
                    approval_status: $('#by_approval_status').val(),
                    payment_status: $('#by_payment_status').val(),
                    video_received: $('#by_video_received').val(),
                    social_media_status: $('#by_social_media_status').val(),
                };
            }

            function getTestimonialFilterData() {
                return {
                    by_full_name: $('#by_full_name').val(),
                    by_passport_number: $('#by_passport_number').val(),
                    by_created_by: $('#by_created_by').val(),
                    by_date_range: $('#by_date_range').val(),
                    by_approval_status: $('#by_approval_status').val(),
                    by_payment_status: $('#by_payment_status').val(),
                    by_video_received: $('#by_video_received').val(),
                    by_social_media_status: $('#by_social_media_status').val(),
                };
            }

            function reloadTestimonialsList() {
                $.ajax({
                    url: "{{ route('admin.testimonial') }}",
                    method: "GET",
                    dataType: "html",
                    data: getFilterData(),
                    success: function(data) {
                        $('.candloadpaginate').html(data);
                    }
                });
            }

            // Pagination Request — swap the page via AJAX instead of a full page load
            $('body').on('click', '.candloadpaginate .pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                if (!url) {
                    return;
                }
                var finalURL = url + "&" + $.param(getFilterData());

                $.ajax({
                    url: finalURL
                }).done(function(data) {
                    $('.candloadpaginate').html(data);
                }).fail(function() {
                    toastr.error('Unable to load that page.');
                });

                window.history.pushState("", "", url);
            });

            function updateFilterIndicator() {
                var hasFilter =
                    !!$('#by_full_name').val() ||
                    !!$('#by_passport_number').val() ||
                    ($('#by_created_by').val() && $('#by_created_by').val().length > 0) ||
                    !!$('#by_date_range').val() ||
                    ($('#by_approval_status').val() && $('#by_approval_status').val().length > 0) ||
                    ($('#by_payment_status').val() && $('#by_payment_status').val().length > 0) ||
                    ($('#by_video_received').val() && $('#by_video_received').val().length > 0) ||
                    ($('#by_social_media_status').val() && $('#by_social_media_status').val().length > 0);

                if (hasFilter) {
                    $('.filterpanel .filter-indicator').removeClass('d-none');
                } else {
                    $('.filterpanel .filter-indicator').addClass('d-none');
                }
            }

            function bindFilterChange(selector) {
                $(selector).on('change input', function() {
                    reloadTestimonialsList();
                    updateFilterIndicator();
                });
            }

            bindFilterChange('#pagination_list');
            bindFilterChange('#search_text');
            bindFilterChange('#by_full_name');
            bindFilterChange('#by_passport_number');
            bindFilterChange('#by_created_by');
            bindFilterChange('#by_approval_status');
            bindFilterChange('#by_payment_status');
            bindFilterChange('#by_video_received');
            bindFilterChange('#by_social_media_status');

            updateFilterIndicator();

            // ===============================
            // Summary count cards -> apply matching filter
            // ===============================
            var summaryFilterFieldMap = {
                approval_status: '#by_approval_status',
                payment_status: '#by_payment_status',
                video_received: '#by_video_received',
                social_media_status: '#by_social_media_status',
            };

            $(document).on('click', '.summaryCountCard', function(e) {
                e.preventDefault();

                var $this = $(this);
                var wasActive = $this.hasClass('active');
                var filters = $this.data('filter') || {};

                $('.status-btn').removeClass('active');

                $('#by_full_name').val('');
                $('#by_passport_number').val('');
                $('#by_created_by').selectpicker('deselectAll');
                $('#by_date_range').val('');
                $.each(summaryFilterFieldMap, function(key, selector) {
                    $(selector).selectpicker('deselectAll');
                });

                if (!wasActive) {
                    $this.addClass('active');

                    $.each(filters, function(key, values) {
                        var selector = summaryFilterFieldMap[key];
                        if (selector) {
                            $(selector).selectpicker('val', values);
                        }
                    });
                }

                reloadTestimonialsList();
                updateFilterIndicator();
            });

            // Reset Filter
            $(document).on('click', '.resetfilter', function() {
                $('.status-btn').removeClass('active');
                $('#by_full_name').val('');
                $('#by_passport_number').val('');
                $('#by_created_by').selectpicker('deselectAll');
                $('#by_date_range').val('');
                $('#by_approval_status').selectpicker('deselectAll');
                $('#by_payment_status').selectpicker('deselectAll');
                $('#by_video_received').selectpicker('deselectAll');
                $('#by_social_media_status').selectpicker('deselectAll');

                $.post('{{ route("admin.testimonials.resetFilter") }}', getTestimonialFilterData(), function(res) {
                    toastr.success(res.message || 'Filter reset successfully!', 'Success', {
                        timeOut: 2000
                    });
                }).fail(function() {
                    toastr.error('Failed to reset filter', 'Error');
                });

                reloadTestimonialsList();
                updateFilterIndicator();
            });

            // Save Filter
            $(document).on('click', '.apply_filters', function() {
                $.post('{{ route("admin.testimonials.saveFilter") }}', getTestimonialFilterData(), function(res) {
                    toastr.success(res.message || 'Filter saved successfully!', 'Success', {
                        timeOut: 2000
                    });
                }).fail(function() {
                    toastr.error('Failed to save filter', 'Error');
                });

                reloadTestimonialsList();
                updateFilterIndicator();
            });

            // ===============================
            // Generate Link modal
            // ===============================
            function copyInputToClipboard($input) {
                $input.select();
                $input[0].setSelectionRange(0, 99999); // for mobile
                document.execCommand('copy');
                toastr.success('Link copied to clipboard!');
            }

            function clearGenerateLinkErrors() {
                $('#generateLinkName, #generateLinkPassport').removeClass('is-invalid');
                $('#generateLinkNameError, #generateLinkPassportError').text('');
            }

            function showGenerateLinkErrors(errors) {
                clearGenerateLinkErrors();

                if (errors && errors.full_name) {
                    $('#generateLinkName').addClass('is-invalid');
                    $('#generateLinkNameError').text(errors.full_name[0]);
                }

                if (errors && errors.passport_number) {
                    $('#generateLinkPassport').addClass('is-invalid');
                    $('#generateLinkPassportError').text(errors.passport_number[0]);
                }
            }

            // Reset the modal each time it is opened / closed
            $('#copyLinkModal').on('hidden.bs.modal', function() {
                $('#generateLinkName').val('');
                $('#generateLinkPassport').val('');
                $('#testimonialLink').val('');
                $('#generatedLinkWrapper').addClass('d-none');
                clearGenerateLinkErrors();
            });

            $('#generateLinkBtn').on('click', function() {
                var name = $('#generateLinkName').val().trim();
                var passport = $('#generateLinkPassport').val().trim();

                clearGenerateLinkErrors();

                var errors = {};
                if (!name) {
                    errors.full_name = ['Name is required.'];
                }
                if (!passport) {
                    errors.passport_number = ['Passport No. is required.'];
                }

                if (Object.keys(errors).length > 0) {
                    showGenerateLinkErrors(errors);
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true);
                $('.generate-link-label', $btn).addClass('d-none');
                $('#generateLinkSpinner').removeClass('d-none');

                $.ajax({
                    url: "{{ route('admin.testimonials.generate_link') }}",
                    type: 'POST',
                    data: {
                        full_name: name,
                        passport_number: passport
                    },
                    success: function(response) {
                        $('#testimonialLink').val(response.url);
                        $('#generatedLinkWrapper').removeClass('d-none');
                        toastr.success('Testimonial link generated successfully!');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            showGenerateLinkErrors(xhr.responseJSON.errors);
                        } else {
                            toastr.error('Failed to generate link!');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $('.generate-link-label', $btn).removeClass('d-none');
                        $('#generateLinkSpinner').addClass('d-none');
                    }
                });
            });

            // Copy the generated link to clipboard
            $('#copyLinkBtn').on('click', function() {
                copyInputToClipboard($('#testimonialLink'));
            });

            // ===============================
            // Row-level Copy Link action
            // ===============================
            $(document).on('click', '.copyTestimonialLinkBtn', function() {
                $('#rowTestimonialLink').val($(this).data('link'));
                var rowCopyLinkModal = new bootstrap.Modal(document.getElementById('rowCopyLinkModal'));
                rowCopyLinkModal.show();
            });

            $('#rowCopyLinkBtn').on('click', function() {
                copyInputToClipboard($('#rowTestimonialLink'));
            });


            // When edit button is clicked
            $(document).on('click', '.editTestimonialBtn', function() {
                var testimonialId = $(this).data('id');

                // Fetch testimonial data via AJAX
                $.ajax({
                    url: '{{ url("admin/testimonials") }}/' + testimonialId + '/edit',
                    type: 'GET',
                    success: function(res) {
                        $('#edit_testimonial_id').val(res.id);
                        $('#edit-full-name').val(res.full_name);
                        $('#edit-passport').val(res.passport_number);
                        $('#edit-copy-link-btn').data('link', res.link);

                        // Open the offcanvas
                        var editModal = new bootstrap.Offcanvas(document.getElementById('editTestimonial'));
                        editModal.show();
                    }
                });
            });


            var testimonialActivityOffset = 0;
            var testimonialActivityLimit = 4;
            var testimonialActivityLoading = false;

            function resetTestimonialActivityState() {
                testimonialActivityOffset = 0;
                testimonialActivityLoading = false;
                $('#testimonialActivityTimeline').empty();
                $('#testimonialActivityLoadMoreWrapper').addClass('d-none');
                $('#testimonialActivityEmpty').addClass('d-none');
                $('#testimonialActivityError').addClass('d-none');
                $('#testimonialActivityLoadMoreBtn').prop('disabled', false);
                $('#testimonialActivityLoadMoreBtn .load-more-label').removeClass('d-none');
                $('#testimonialActivityLoadMoreSpinner').addClass('d-none');
            }

            function loadTestimonialActivity(testimonialId, append) {
                if (testimonialActivityLoading) {
                    return;
                }
                testimonialActivityLoading = true;

                if (append) {
                    $('#testimonialActivityLoadMoreBtn').prop('disabled', true);
                    $('#testimonialActivityLoadMoreBtn .load-more-label').addClass('d-none');
                    $('#testimonialActivityLoadMoreSpinner').removeClass('d-none');
                } else {
                    $('#testimonialActivityLoading').removeClass('d-none');
                    $('#testimonialActivityEmpty').addClass('d-none');
                    $('#testimonialActivityError').addClass('d-none');
                }

                $.ajax({
                    url: '{{ url("admin/testimonials") }}/' + testimonialId + '/activity',
                    type: 'GET',
                    data: {
                        offset: testimonialActivityOffset,
                        limit: testimonialActivityLimit
                    },
                    success: function(res) {
                        $('#testimonialActivityLoading').addClass('d-none');

                        if (res.html) {
                            $('#testimonialActivityTimeline').append(res.html);
                        }

                        testimonialActivityOffset += testimonialActivityLimit;

                        if (res.has_more) {
                            $('#testimonialActivityLoadMoreWrapper').removeClass('d-none');
                        } else {
                            $('#testimonialActivityLoadMoreWrapper').addClass('d-none');
                        }

                        if (res.total === 0) {
                            $('#testimonialActivityEmpty').removeClass('d-none');
                        }
                    },
                    error: function() {
                        $('#testimonialActivityLoading').addClass('d-none');
                        $('#testimonialActivityError').removeClass('d-none');
                    },
                    complete: function() {
                        testimonialActivityLoading = false;
                        $('#testimonialActivityLoadMoreBtn').prop('disabled', false);
                        $('#testimonialActivityLoadMoreBtn .load-more-label').removeClass('d-none');
                        $('#testimonialActivityLoadMoreSpinner').addClass('d-none');
                    }
                });
            }

            $(document).on('shown.bs.tab', '#tab-testimonial-activity', function() {
                resetTestimonialActivityState();
                loadTestimonialActivity($('#view_testimonial_id').val(), false);
            });

            $(document).on('click', '#testimonialActivityLoadMoreBtn', function() {
                loadTestimonialActivity($('#view_testimonial_id').val(), true);
            });

            function renderViewVideos(testimonialId, uploadedVideoRaw) {
                var videosContainer = $('#viewVideosList');
                videosContainer.empty();

                if (uploadedVideoRaw) {
                    var uploadedVideos = [];
                    try {
                        uploadedVideos = JSON.parse(uploadedVideoRaw);
                    } catch (e) {
                        console.error('Failed to parse uploaded_video', e);
                    }

                    if (uploadedVideos.length > 0) {
                        uploadedVideos.forEach(function(videoPath) {
                            var fileName = videoPath.split('/').pop();
                            videosContainer.append(
                                `<li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>${fileName}</span>
                                    <div class="btn-group">
                                       <a href="{{ url('videos') }}/${videoPath}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                       <a href="{{ url('videos') }}/${videoPath}" download class="btn btn-sm btn-outline-success">Download</a>
                                       <button type="button" class="btn btn-sm btn-outline-danger deleteTestimonialVideoBtn" data-id="${testimonialId}" data-video="${fileName}">Delete</button>
                                    </div>
                                </li>`
                            );
                        });
                    } else {
                        videosContainer.append('<li class="list-group-item">No videos uploaded</li>');
                    }

                } else {
                    videosContainer.append('<li class="list-group-item">No videos uploaded</li>');
                }
            }

            $(document).on('click', '.viewTestimonialBtn', function() {

                var testimonialId = $(this).data('id');

                $.ajax({
                    url: '{{ url("admin/testimonials") }}/' + testimonialId,
                    type: 'GET',
                    success: function(res) {
                        $('#view_testimonial_id').val(res.id);
                        $('#view-full-name').val(res.full_name);
                        $('#view-passport').val(res.passport_number);
                        $('#view-video-received').val(res.video_received_status || 'Not Received');

                        var socialMediaText = res.social_media_status || 'Not Posted';
                        if (res.social_media_status === 'Posted' && res.social_media_platforms && res.social_media_platforms.length > 0) {
                            socialMediaText += ' (' + res.social_media_platforms.join(', ') + ')';
                        }
                        $('#view-social-media').val(socialMediaText);

                        renderViewVideos(res.id, res.uploaded_video);

                        // Always open on the Details tab, regardless of which tab was active last time
                        bootstrap.Tab.getOrCreateInstance(document.getElementById('tab-testimonial-details')).show();

                        var viewModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('viewTestimonial'));
                        viewModal.show();
                    },
                    error: function() {
                        alert('Unable to fetch testimonial details.');
                    }
                });
            });

            // Delete a single uploaded video from the View Testimonial panel
            $(document).on('click', '.deleteTestimonialVideoBtn', function() {
                var $btn = $(this);
                var testimonialId = $btn.data('id');
                var video = $btn.data('video');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This video will be permanently deleted.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                        confirmButton: 'btn btn-danger me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: '{{ route("admin.testimonials.delete_video") }}',
                        type: 'POST',
                        data: {
                            testimonial_id: testimonialId,
                            video: video
                        },
                        success: function(res) {
                            toastr.success('Video deleted successfully!');
                            renderViewVideos(testimonialId, JSON.stringify(res.all_videos));
                        },
                        error: function() {
                            toastr.error('Unable to delete video.');
                        }
                    });
                });
            });


            // Delete a testimonial
            $(document).on('click', '.deltestimonial', function() {
                var testimonialId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This testimonial will be permanently deleted.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                        confirmButton: 'btn btn-danger me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: "{{ route('admin.testimonials.delete') }}",
                        type: 'POST',
                        data: {
                            testimonial_id: testimonialId
                        },
                        success: function(res) {
                            toastr.success(res.message || 'Testimonial deleted successfully!');
                            reloadTestimonialsList();
                        },
                        error: function(xhr) {
                            if (xhr.status === 403) {
                                toastr.error('You do not have permission to perform this action.');
                            } else {
                                toastr.error('Unable to delete testimonial.');
                            }
                        }
                    });
                });
            });

            // Approve / Reject a testimonial
            function updateApprovalStatus(url, testimonialId, confirmText, confirmButtonText) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: confirmText,
                    icon: 'warning',
                    input: 'textarea',
                    inputPlaceholder: 'Add a remark (optional)...',
                    showCancelButton: true,
                    confirmButtonText: confirmButtonText,
                    customClass: {
                        confirmButton: 'btn btn-primary me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            testimonial_id: testimonialId,
                            remarks: result.value || ''
                        },
                        success: function(res) {
                            toastr.success(res.message || 'Status updated successfully!');
                            reloadTestimonialsList();
                        },
                        error: function(xhr) {
                            if (xhr.status === 403) {
                                toastr.error('You do not have permission to perform this action.');
                            } else {
                                toastr.error('Unable to update approval status.');
                            }
                        }
                    });
                });
            }

            $(document).on('click', '.approveTestimonialBtn', function() {
                updateApprovalStatus(
                    "{{ route('admin.testimonials.approve') }}",
                    $(this).data('id'),
                    'Approve this testimonial?',
                    'Yes, approve it!'
                );
            });

            $(document).on('click', '.rejectTestimonialBtn', function() {
                updateApprovalStatus(
                    "{{ route('admin.testimonials.reject') }}",
                    $(this).data('id'),
                    'Reject this testimonial?',
                    'Yes, reject it!'
                );
            });

            // ===============================
            // Video Received
            // ===============================
            $(document).on('click', '.videoReceivedBtn', function() {
                var testimonialId = $(this).data('id');
                var newStatus = $(this).data('status');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Mark video as "' + newStatus + '"?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update it!',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: "{{ route('admin.testimonials.video_received.update') }}",
                        type: 'POST',
                        data: {
                            testimonial_id: testimonialId,
                            status: newStatus
                        },
                        success: function(res) {
                            toastr.success(res.message || 'Video received status updated successfully!');
                            reloadTestimonialsList();
                        },
                        error: function(xhr) {
                            if (xhr.status === 403) {
                                toastr.error('You do not have permission to perform this action.');
                            } else {
                                toastr.error('Unable to update video received status.');
                            }
                        }
                    });
                });
            });

            // ===============================
            // Social Media Post
            // ===============================
            $('#socialMediaModal').on('hidden.bs.modal', function() {
                $('#socialMediaForm')[0].reset();
                $('#socialMediaTestimonialId').val('');
                $('#socialMediaPlatforms').selectpicker('deselectAll');
                $('#socialMediaPlatforms').removeClass('is-invalid');
                $('#socialMediaPlatformsError').text('');
                $('#socialMediaMessage').html('');
            });

            $(document).on('click', '.socialMediaPostedBtn', function() {
                var testimonialId = $(this).data('id');
                var platforms = $(this).data('platforms') || [];

                $('#socialMediaTestimonialId').val(testimonialId);
                $('#socialMediaPlatforms').selectpicker('deselectAll');
                $('#socialMediaPlatforms').selectpicker('val', platforms);

                var socialMediaModal = new bootstrap.Modal(document.getElementById('socialMediaModal'));
                socialMediaModal.show();
            });

            $('#socialMediaForm').on('submit', function(e) {
                e.preventDefault();

                $('#socialMediaPlatforms').removeClass('is-invalid');
                $('#socialMediaPlatformsError').text('');
                $('#socialMediaMessage').html('');

                var platforms = $('#socialMediaPlatforms').val() || [];
                if (platforms.length === 0) {
                    $('#socialMediaPlatforms').addClass('is-invalid');
                    $('#socialMediaPlatformsError').text('Please select at least one platform.');
                    return;
                }

                var $btn = $('#socialMediaSubmitBtn');
                $btn.prop('disabled', true);
                $('.social-media-label', $btn).addClass('d-none');
                $('#socialMediaSpinner').removeClass('d-none');

                $.ajax({
                    url: "{{ route('admin.testimonials.social_media.update') }}",
                    type: 'POST',
                    data: {
                        testimonial_id: $('#socialMediaTestimonialId').val(),
                        status: 'Posted',
                        platforms: platforms
                    },
                    success: function(res) {
                        toastr.success(res.message || 'Social media status updated successfully!');
                        var socialMediaModalEl = document.getElementById('socialMediaModal');
                        bootstrap.Modal.getInstance(socialMediaModalEl).hide();
                        reloadTestimonialsList();
                    },
                    error: function(xhr) {
                        var message = 'Unable to update social media status.';

                        if (xhr.status === 422 && xhr.responseJSON) {
                            if (xhr.responseJSON.errors && xhr.responseJSON.errors.platforms) {
                                $('#socialMediaPlatforms').addClass('is-invalid');
                                $('#socialMediaPlatformsError').text(xhr.responseJSON.errors.platforms[0]);
                            }
                            message = xhr.responseJSON.message || message;
                        } else if (xhr.status === 403) {
                            message = 'You do not have permission to perform this action.';
                        }

                        $('#socialMediaMessage').html('<div class="alert alert-danger mt-2 mb-0">' + message + '</div>');
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $('.social-media-label', $btn).removeClass('d-none');
                        $('#socialMediaSpinner').addClass('d-none');
                    }
                });
            });

            $(document).on('click', '.socialMediaNotPostedBtn', function() {
                var testimonialId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Mark as Not Posted? Any saved platforms will be cleared.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update it!',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: "{{ route('admin.testimonials.social_media.update') }}",
                        type: 'POST',
                        data: {
                            testimonial_id: testimonialId,
                            status: 'Not Posted'
                        },
                        success: function(res) {
                            toastr.success(res.message || 'Social media status updated successfully!');
                            reloadTestimonialsList();
                        },
                        error: function(xhr) {
                            if (xhr.status === 403) {
                                toastr.error('You do not have permission to perform this action.');
                            } else {
                                toastr.error('Unable to update social media status.');
                            }
                        }
                    });
                });
            });

            // ===============================
            // Mark Payment as Paid
            // ===============================
            $('#markPaidModal').on('hidden.bs.modal', function() {
                $('#markPaidForm')[0].reset();
                $('#markPaidTestimonialId').val('');
                $('#markPaidAmount').removeClass('is-invalid');
                $('#markPaidAmountError').text('');
                $('#markPaidSlip').removeClass('is-invalid');
                $('#markPaidSlipError').text('');
                $('#markPaidMessage').html('');
            });

            $(document).on('click', '.markPaidBtn', function() {
                $('#markPaidTestimonialId').val($(this).data('id'));
                var markPaidModal = new bootstrap.Modal(document.getElementById('markPaidModal'));
                markPaidModal.show();
            });

            $('#markPaidForm').on('submit', function(e) {
                e.preventDefault();

                $('#markPaidAmount').removeClass('is-invalid');
                $('#markPaidAmountError').text('');
                $('#markPaidSlip').removeClass('is-invalid');
                $('#markPaidSlipError').text('');
                $('#markPaidMessage').html('');

                var amount = parseFloat($('#markPaidAmount').val());
                if (isNaN(amount) || amount <= 0) {
                    $('#markPaidAmount').addClass('is-invalid');
                    $('#markPaidAmountError').text('Please enter a valid amount greater than 0.');
                    return;
                }

                var $btn = $('#markPaidSubmitBtn');
                $btn.prop('disabled', true);
                $('.mark-paid-label', $btn).addClass('d-none');
                $('#markPaidSpinner').removeClass('d-none');

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.testimonials.mark_paid') }}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        toastr.success(res.message || 'Payment marked as paid successfully!');
                        var markPaidModalEl = document.getElementById('markPaidModal');
                        bootstrap.Modal.getInstance(markPaidModalEl).hide();
                        reloadTestimonialsList();
                    },
                    error: function(xhr) {
                        var message = 'Unable to mark payment as paid.';

                        if (xhr.status === 422 && xhr.responseJSON) {
                            if (xhr.responseJSON.errors && xhr.responseJSON.errors.amount) {
                                $('#markPaidAmount').addClass('is-invalid');
                                $('#markPaidAmountError').text(xhr.responseJSON.errors.amount[0]);
                            }
                            if (xhr.responseJSON.errors && xhr.responseJSON.errors.payment_slip) {
                                $('#markPaidSlip').addClass('is-invalid');
                                $('#markPaidSlipError').text(xhr.responseJSON.errors.payment_slip[0]);
                            }
                            message = xhr.responseJSON.message || message;
                        } else if (xhr.status === 403) {
                            message = 'You do not have permission to perform this action.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        $('#markPaidMessage').html('<div class="alert alert-danger mt-2 mb-0">' + message + '</div>');
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $('.mark-paid-label', $btn).removeClass('d-none');
                        $('#markPaidSpinner').addClass('d-none');
                    }
                });
            });

            // ===============================
            // View Payment Info
            // ===============================
            $(document).on('click', '.viewPaymentBtn', function() {
                var testimonialId = $(this).data('id');

                $.ajax({
                    url: '{{ url("admin/testimonials") }}/' + testimonialId,
                    type: 'GET',
                    success: function(res) {
                        $('#viewPaymentAmount').val(res.paid_amount_formatted || '---');
                        $('#viewPaymentPaidBy').val(res.paid_by_name || '---');
                        $('#viewPaymentPaidAt').val(res.paid_at || '---');

                        if (res.payment_slip_view_url) {
                            $('#viewPaymentSlipView').attr('href', res.payment_slip_view_url).removeClass('disabled');
                            $('#viewPaymentSlipDownload').attr('href', res.payment_slip_download_url).removeClass('disabled');
                        } else {
                            $('#viewPaymentSlipView').attr('href', 'javascript:;').addClass('disabled');
                            $('#viewPaymentSlipDownload').attr('href', 'javascript:;').addClass('disabled');
                        }

                        var viewPaymentModal = new bootstrap.Modal(document.getElementById('viewPaymentModal'));
                        viewPaymentModal.show();
                    },
                    error: function() {
                        toastr.error('Unable to fetch payment details.');
                    }
                });
            });

            // Ensure backdrop is removed when offcanvas is hidden
            var offcanvasElements = document.querySelectorAll('.offcanvas');
            offcanvasElements.forEach(function(offcanvasEl) {
                offcanvasEl.addEventListener('hidden.bs.offcanvas', function() {
                    const backdrop = document.querySelector('.offcanvas-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                });
            });

            // Status Bar toggle: shows/hides the Testimonial Summary Cards
            // bar. Persisted client-side so the choice survives reloads and
            // keeps working across the AJAX list/filter refresh, since that
            // refresh only reloads the table, never this bar's container.
            // display is forced with !important (and bound via delegation)
            // so nothing else on this page can silently override it.
            (function(){
                function setTestimonialStatusBarVisible(visible){
                    var bar = document.getElementById('testimonialStatusBar');
                    if (!bar) return;
                    if (visible) {
                        bar.style.removeProperty('display');
                    } else {
                        bar.style.setProperty('display', 'none', 'important');
                    }
                }

                var stored = null;
                try { stored = localStorage.getItem('testimonial_status_bar_visible'); } catch(e) {}
                var visible = stored === null ? true : stored === '1';
                $('.testimonial-status-bar-switch').prop('checked', visible);
                setTestimonialStatusBarVisible(visible);

                $(document).on('change', '.testimonial-status-bar-switch', function(){
                    var isVisible = $(this).is(':checked');
                    setTestimonialStatusBarVisible(isVisible);
                    try { localStorage.setItem('testimonial_status_bar_visible', isVisible ? '1' : '0'); } catch(e) {}
                });
            })();

        });
    </script>




@endsection
