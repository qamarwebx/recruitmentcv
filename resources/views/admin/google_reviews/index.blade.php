@extends('layout.admin.admin_layout')


@section('title','Google Review')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />

    <style>
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

        /* Summary count pills — same design as the Leads/Testimonial status cards */
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
            $googleReviewSummaryCards = [
                [
                    'label' => 'All',
                    'color' => '#6C757D',
                    'count' => $summaryCounts['total'] ?? 0,
                    'filter' => [],
                ],
                [
                    'label' => 'Pending Approval',
                    'color' => '#FF9F43',
                    'count' => $summaryCounts['pending_approval'] ?? 0,
                    'filter' => ['approval_status' => ['Pending']],
                ],
                [
                    'label' => 'Approved',
                    'color' => '#28C76F',
                    'count' => $summaryCounts['approved'] ?? 0,
                    'filter' => ['approval_status' => ['Approved']],
                ],
                [
                    'label' => 'Rejected',
                    'color' => '#EA5455',
                    'count' => $summaryCounts['rejected'] ?? 0,
                    'filter' => ['approval_status' => ['Rejected']],
                ],
                [
                    'label' => 'Payment Pending',
                    'color' => '#FF9F43',
                    'count' => $summaryCounts['payment_pending'] ?? 0,
                    'filter' => ['approval_status' => ['Approved'], 'payment_status' => ['Pending']],
                ],
                [
                    'label' => 'Payment Paid',
                    'color' => '#28C76F',
                    'count' => $summaryCounts['payment_paid'] ?? 0,
                    'filter' => ['payment_status' => ['Paid']],
                ],
            ];
        @endphp

        <!-- Google Review Summary Cards Start -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="candidate-status-wrapper">
                    <div class="candidate-status-card">
                        <div class="candidate-status-grid">
                            @foreach ($googleReviewSummaryCards as $card)
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
        <!-- Google Review Summary Cards End -->

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

                    <a href="{{ route('admin.testimonial') }}" class="btn btn-xs btn-outline-primary ms-2">
                        <i class="ti ti-arrow-left"></i> Back to Testimonial
                    </a>
                </div>

                <div class="float-end">
                    @if(auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->add_google_review == 1)))
                    <button class="btn btn-sm btn-primary mx-1"
                            data-bs-toggle="modal"
                            data-bs-target="#addGoogleReviewModal">
                        <i class="ti ti-plus me-1 ti-xs"></i>
                        <span class="d-none d-sm-inline-block">Add Google Review</span>
                    </button>
                    @endif
                </div>
                <div class="float-end">
                    <input type="text" id="search_text" class="form-control form-control-sm" placeholder="Search branch...">
                </div>
            </div>
            <div class="card-datatable table-responsive candloadpaginate">
                @include('admin.google_reviews.google_review_load')
            </div>
        </div>

        <!-- Add Google Review Modal -->
        <div class="modal fade" id="addGoogleReviewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="addGoogleReviewForm" action="{{ route('admin.google_review.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Add Google Review</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="add-branch-select" class="form-label">Branches <span class="text-danger">*</span></label>
                                <select name="branch_id[]" id="add-branch-select" class="selectpicker w-100" multiple
                                    data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Branch" required>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="add-screenshot" class="form-label">Upload Screenshot <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="add-screenshot" name="screenshot"
                                       accept=".jpg,.jpeg,.png,.pdf" required>
                                <div class="form-text">Accepted: JPG, PNG, PDF. Max size 5 MB.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add Google Review Modal End -->

        <!-- Filter Panel Start -->
        <div class="modal fade" id="filterpanel" aria-hidden="true" aria-labelledby="filterpanelLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="filterpanelLabel">Google Review Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <!-- Branch Filter -->
                            <div class="col-md-4 mb-3">
                                @php $selectedBranch = isset($saveadminfilter) ? $saveadminfilter->by_branch_array : []; @endphp
                                <select name="by_branch[]" id="by_branch" class="selectpicker w-100" multiple
                                    data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Branch">
                                    @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        @if(in_array($branch->id, $selectedBranch)) selected @endif>
                                        {{ $branch->name }}
                                    </option>
                                    @endforeach
                                </select>
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

        <!--- Edit Google Review Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editGoogleReview" aria-labelledby="editGoogleReviewLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editGoogleReviewLabel" class="offcanvas-title">Edit Google Review</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="edit-google-review-form pt-0" id="editGoogleReviewForm" action="{{ route('admin.google_review.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="google_review_id" id="edit_google_review_id">

                    <div class="mb-3">
                        <label class="form-label" for="edit-branch-select">Branches <span class="text-danger">*</span></label>
                        <select name="branch_id[]" id="edit-branch-select" class="selectpicker w-100" multiple
                            data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Branch" required>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="edit-screenshot">Replace Screenshot</label>
                        <input type="file" class="form-control" id="edit-screenshot" name="screenshot" accept=".jpg,.jpeg,.png,.pdf">
                        <div class="form-text">Leave empty to keep the existing screenshot. Accepted: JPG, PNG, PDF. Max size 5 MB.</div>
                    </div>

                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Edit Google Review End --->

       <!--- View Google Review Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="viewGoogleReview" aria-labelledby="viewGoogleReviewLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="viewGoogleReviewLabel" class="offcanvas-title">View Google Review</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <input type="hidden" id="view_google_review_id">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Branches</label>
                        <input type="text" id="view-branch-name" class="form-control" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Care Of</label>
                        <input type="text" id="view-care-of" class="form-control" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Screenshot</label>
                    <div id="viewScreenshotLinks">
                        <span class="text-muted">No screenshot uploaded</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Approval History</label>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Action By</th>
                                    <th>Date &amp; Time</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="viewApprovalHistoryList">
                                {{-- Approval history rows will be appended here dynamically --}}
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        <!--- View Google Review End --->

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
                            <input type="hidden" id="markPaidGoogleReviewId" name="google_review_id">

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
    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>

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
                reloadGoogleReviewsList();
                updateFilterIndicator();
            }).on('cancel.daterangepicker', function() {
                $(this).val('');
                reloadGoogleReviewsList();
                updateFilterIndicator();
            });

            // ===============================
            // Filter data + reload
            // ===============================
            function getFilterData() {
                return {
                    page_list: $('#pagination_list').val(),
                    search_text: $('#search_text').val(),
                    branch_id: $('#by_branch').val(),
                    created_by: $('#by_created_by').val(),
                    date_range: $('#by_date_range').val(),
                    approval_status: $('#by_approval_status').val(),
                    payment_status: $('#by_payment_status').val(),
                };
            }

            function getGoogleReviewFilterData() {
                return {
                    by_branch: $('#by_branch').val(),
                    by_created_by: $('#by_created_by').val(),
                    by_date_range: $('#by_date_range').val(),
                    by_approval_status: $('#by_approval_status').val(),
                    by_payment_status: $('#by_payment_status').val(),
                };
            }

            var googleReviewsListXhr = null;

            function reloadGoogleReviewsList() {
                // Abort any still-in-flight reload so a slower, stale
                // response can never land after (and overwrite) the latest
                // click's result — that race was making the status-bar
                // filter appear to need a second click to "take".
                if (googleReviewsListXhr && googleReviewsListXhr.readyState !== 4) {
                    googleReviewsListXhr.abort();
                }

                googleReviewsListXhr = $.ajax({
                    url: "{{ route('admin.google_review') }}",
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
                    ($('#by_branch').val() && $('#by_branch').val().length > 0) ||
                    ($('#by_created_by').val() && $('#by_created_by').val().length > 0) ||
                    !!$('#by_date_range').val() ||
                    ($('#by_approval_status').val() && $('#by_approval_status').val().length > 0) ||
                    ($('#by_payment_status').val() && $('#by_payment_status').val().length > 0);

                if (hasFilter) {
                    $('.filterpanel .filter-indicator').removeClass('d-none');
                } else {
                    $('.filterpanel .filter-indicator').addClass('d-none');
                }
            }

            function bindFilterChange(selector) {
                $(selector).on('change input', function() {
                    reloadGoogleReviewsList();
                    updateFilterIndicator();
                });
            }

            bindFilterChange('#pagination_list');
            bindFilterChange('#search_text');
            bindFilterChange('#by_branch');
            bindFilterChange('#by_created_by');
            bindFilterChange('#by_approval_status');
            bindFilterChange('#by_payment_status');

            updateFilterIndicator();

            // ===============================
            // Summary count cards -> apply matching filter
            // ===============================
            var summaryFilterFieldMap = {
                approval_status: '#by_approval_status',
                payment_status: '#by_payment_status',
            };

            // Highlight whichever card the currently-applied filter matches
            // (e.g. a previously saved filter, already pre-selected
            // server-side) — so the active indicator is correct on page
            // load too, not only right after a card is clicked.
            function syncActiveSummaryCard() {
                var hasOtherFilter =
                    ($('#by_branch').val() && $('#by_branch').val().length > 0) ||
                    ($('#by_created_by').val() && $('#by_created_by').val().length > 0) ||
                    !!$('#by_date_range').val();

                $('.status-btn').removeClass('active');

                if (hasOtherFilter) {
                    return; // a non-status filter is active; no card represents that
                }

                var current = {};
                $.each(summaryFilterFieldMap, function(key, selector) {
                    var val = $(selector).val() || [];
                    if (val.length) {
                        current[key] = val.slice().sort();
                    }
                });

                $('.summaryCountCard').each(function() {
                    var $card = $(this);
                    var filters = $card.data('filter') || {};
                    var filterKeys = Object.keys(filters);

                    if (filterKeys.length !== Object.keys(current).length) {
                        return;
                    }

                    var matches = filterKeys.every(function(key) {
                        var expected = (filters[key] || []).slice().sort().map(String);
                        var actual = current[key] || [];
                        return expected.length === actual.length &&
                            expected.every(function(v, i) { return v === String(actual[i]); });
                    });

                    if (matches) {
                        $card.addClass('active');
                        return false;
                    }
                });
            }

            syncActiveSummaryCard();

            $(document).on('click', '.summaryCountCard', function(e) {
                e.preventDefault();

                var $this = $(this);
                var wasActive = $this.hasClass('active');
                var filters = $this.data('filter') || {};

                $('.status-btn').removeClass('active');

                // Use val([]) rather than deselectAll() to clear these —
                // deselectAll() fires a real native 'change' event (unlike
                // val()), which would trigger each field's own bindFilterChange
                // reload immediately, racing against the reload fired below.
                $('#by_branch').selectpicker('val', []);
                $('#by_created_by').selectpicker('val', []);
                $('#by_date_range').val('');
                $.each(summaryFilterFieldMap, function(key, selector) {
                    $(selector).selectpicker('val', []);
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

                reloadGoogleReviewsList();
                updateFilterIndicator();
            });

            // Reset Filter
            $(document).on('click', '.resetfilter', function() {
                $('.status-btn').removeClass('active');
                $('#by_branch').selectpicker('val', []);
                $('#by_created_by').selectpicker('val', []);
                $('#by_date_range').val('');
                $('#by_approval_status').selectpicker('val', []);
                $('#by_payment_status').selectpicker('val', []);

                $.post('{{ route("admin.google_review.resetFilter") }}', getGoogleReviewFilterData(), function(res) {
                    toastr.success(res.message || 'Filter reset successfully!', 'Success', {
                        timeOut: 2000
                    });
                }).fail(function() {
                    toastr.error('Failed to reset filter', 'Error');
                });

                reloadGoogleReviewsList();
                updateFilterIndicator();
            });

            // Save Filter
            $(document).on('click', '.apply_filters', function() {
                $.post('{{ route("admin.google_review.saveFilter") }}', getGoogleReviewFilterData(), function(res) {
                    toastr.success(res.message || 'Filter saved successfully!', 'Success', {
                        timeOut: 2000
                    });
                }).fail(function() {
                    toastr.error('Failed to save filter', 'Error');
                });

                reloadGoogleReviewsList();
                updateFilterIndicator();
            });

            // When edit button is clicked
            $(document).on('click', '.editGoogleReviewBtn', function() {
                var reviewId = $(this).data('id');

                $.ajax({
                    url: "{{ route('admin.google_review.edit') }}",
                    type: 'POST',
                    data: { id: reviewId },
                    success: function(res) {
                        $('#edit_google_review_id').val(res.id);

                        // destroy + reinit (rather than 'refresh') to avoid bootstrap-select
                        // re-appending duplicate <li> options into the dropdown on repeat opens
                        var $branchSelect = $('#edit-branch-select');
                        $branchSelect.selectpicker('destroy');
                        $branchSelect.val(res.branch_ids);
                        $branchSelect.selectpicker({
                            liveSearch: true,
                            actionsBox: true,
                            style: 'default-btn',
                            title: 'Select Branch'
                        });

                        $('#edit-screenshot').val('');

                        var editModal = new bootstrap.Offcanvas(document.getElementById('editGoogleReview'));
                        editModal.show();
                    }
                });
            });

            function renderApprovalHistory(history) {
                var $list = $('#viewApprovalHistoryList');
                $list.empty();

                if (!history || history.length === 0) {
                    $list.append('<tr><td colspan="4" class="text-center text-muted">No approval history yet.</td></tr>');
                    return;
                }

                history.forEach(function(entry) {
                    var badgeClass = entry.status === 'Approved' ? 'bg-label-success' : (entry.status === 'Rejected' ? 'bg-label-danger' : 'bg-label-warning');
                    $list.append(
                        '<tr>' +
                        '<td><span class="badge ' + badgeClass + '">' + $('<div>').text(entry.status || '').html() + '</span></td>' +
                        '<td>' + $('<div>').text(entry.action_by || '---').html() + '</td>' +
                        '<td>' + $('<div>').text(entry.date_time || '---').html() + '</td>' +
                        '<td>' + $('<div>').text(entry.remarks || '---').html() + '</td>' +
                        '</tr>'
                    );
                });
            }

            $(document).on('click', '.viewGoogleReviewBtn', function() {
                var reviewId = $(this).data('id');

                $.ajax({
                    url: '{{ url("admin/google-review") }}/' + reviewId,
                    type: 'GET',
                    success: function(res) {
                        $('#view_google_review_id').val(res.id);
                        $('#view-branch-name').val(res.branch_names);
                        $('#view-care-of').val(res.care_of);

                        if (res.screenshot_view_url) {
                            $('#viewScreenshotLinks').html(
                                '<a href="' + res.screenshot_view_url + '" target="_blank" class="btn btn-sm btn-outline-primary">View Screenshot</a> ' +
                                '<a href="' + res.screenshot_download_url + '" class="btn btn-sm btn-outline-success">Download Screenshot</a>'
                            );
                        } else {
                            $('#viewScreenshotLinks').html('<span class="text-muted">No screenshot uploaded</span>');
                        }

                        renderApprovalHistory(res.approval_history);

                        var viewModal = new bootstrap.Offcanvas(document.getElementById('viewGoogleReview'));
                        viewModal.show();
                    },
                    error: function() {
                        toastr.error('Unable to fetch Google Review details.');
                    }
                });
            });

            // Delete a Google Review
            $(document).on('click', '.delgooglereview', function() {
                var reviewId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This Google Review will be permanently deleted.',
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
                        url: "{{ route('admin.google_review.delete') }}",
                        type: 'POST',
                        data: {
                            google_review_id: reviewId
                        },
                        success: function(res) {
                            toastr.success(res.message || 'Google Review deleted successfully!');
                            reloadGoogleReviewsList();
                        },
                        error: function(xhr) {
                            if (xhr.status === 403) {
                                toastr.error('You do not have permission to perform this action.');
                            } else {
                                toastr.error('Unable to delete Google Review.');
                            }
                        }
                    });
                });
            });

            // Approve / Reject a Google Review
            function updateApprovalStatus(url, reviewId, confirmText, confirmButtonText) {
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
                            google_review_id: reviewId,
                            remarks: result.value || ''
                        },
                        success: function(res) {
                            toastr.success(res.message || 'Status updated successfully!');
                            reloadGoogleReviewsList();
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

            $(document).on('click', '.approveGoogleReviewBtn', function() {
                updateApprovalStatus(
                    "{{ route('admin.google_review.approve') }}",
                    $(this).data('id'),
                    'Approve this Google Review?',
                    'Yes, approve it!'
                );
            });

            $(document).on('click', '.rejectGoogleReviewBtn', function() {
                updateApprovalStatus(
                    "{{ route('admin.google_review.reject') }}",
                    $(this).data('id'),
                    'Reject this Google Review?',
                    'Yes, reject it!'
                );
            });

            // ===============================
            // Mark Payment as Paid
            // ===============================
            $('#markPaidModal').on('hidden.bs.modal', function() {
                $('#markPaidForm')[0].reset();
                $('#markPaidGoogleReviewId').val('');
                $('#markPaidAmount').removeClass('is-invalid');
                $('#markPaidAmountError').text('');
                $('#markPaidSlip').removeClass('is-invalid');
                $('#markPaidSlipError').text('');
                $('#markPaidMessage').html('');
            });

            $(document).on('click', '.markPaidBtn', function() {
                $('#markPaidGoogleReviewId').val($(this).data('id'));
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
                    url: "{{ route('admin.google_review.mark_paid') }}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        toastr.success(res.message || 'Payment marked as paid successfully!');
                        var markPaidModalEl = document.getElementById('markPaidModal');
                        bootstrap.Modal.getInstance(markPaidModalEl).hide();
                        reloadGoogleReviewsList();
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
                var reviewId = $(this).data('id');

                $.ajax({
                    url: '{{ url("admin/google-review") }}/' + reviewId,
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

        });
    </script>

@endsection
