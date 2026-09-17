@extends('layout.partner.partner_layout')

@section('title','Client List')

@section('page-style')
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />

  @if (Auth::guard('partner')->user()->user_type == 2)

    @if (isset($perm) && $perm->delete_client	 == 0)
    <style>
    .delclient{
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

        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">Search Filter</h5>
                <div class="row">
                <div class="col-md-3 mb-3 country-div" @if(isset($filter_user) && $filter_user->country_filter == 1) @else style="display:none" @endif>
                    <select name="" id="by-country" class="form-select select22">
                        <option value="">Select Country</option>
                        @foreach ($countryfs as $countryf)
                            @php
                                $countryf2 = DB::table('countries')->where('id','=',$countryf->country_id)->first();
                            @endphp
                            <option value="{{ $countryf2->id ?? '' }}">{{ $countryf2->name ?? '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3 city-div" @if(isset($filter_user) && $filter_user->city_filter == 1) @else style="display:none" @endif>
                    <select name="" id="by-city" class="form-select select22">
                        <option value="">Select City</option>
                        @foreach ($cityfs as $cityf)
                            @php
                                $cityf2 = DB::table('cities')->where('id','=',$cityf->city_id)->first();
                            @endphp
                            <option value="{{ $cityf2->id }}">{{ $cityf2->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3 status-div" @if(isset($filter_user) && $filter_user->status_filter == 1) @else style="display:none" @endif>
                    <select name="" id="by-status" class="form-select select22">
                        <option value="">Verified</option>
                        @foreach ($verifiedfs as $verifiedf)
                            @if ($verifiedf->verification_status == 1)
                                <option value="1">Verified</option>
                            @elseif($verifiedf->verification_status == 0)
                                <option value="0">Not Verified</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3 created-date-div" @if(isset($filter_user) && $filter_user->created_date_filter == 1) @else style="display:none" @endif>
                    <input type="text" name="" id="by-created-date" class="form-control createdate-picker" placeholder="Booking date...">
                </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                    <tr>
                        <th></th>
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
                            <div class="form-check mt-2" id="country-f">
                                <input class="form-check-input" type="checkbox" name="countryf" value="1" id="countryf" @if(isset($filter_user) && $filter_user->country_filter == 1) checked @endif />
                                <label class="form-check-label" for="countryf"> Country</label>
                            </div>
                            <div class="form-check mt-2" id="city-f">
                                <input class="form-check-input" type="checkbox" name="cityf" value="1" id="cityf" @if(isset($filter_user) && $filter_user->city_filter == 1) checked @endif />
                                <label class="form-check-label" for="cityf"> City</label>
                            </div>
                            <div class="form-check mt-2" id="status-f">
                                <input class="form-check-input" type="checkbox" name="statusf" value="1" id="statusf" @if(isset($filter_user) && $filter_user->status_filter == 1) checked @endif />
                                <label class="form-check-label" for="statusf"> Status</label>
                            </div>
                            <div class="form-check mt-2" id="created-date-f">
                                <input class="form-check-input" type="checkbox" name="created-datef" value="1" id="created-datef" @if(isset($filter_user) && $filter_user->created_date_filter == 1) checked @endif />
                                <label class="form-check-label" for="created-datef"> Created Date</label>
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

        <!-- Delete Client Data Start -->
        <div class="modal fade" id="deleteClient" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('partner.client.delete') }}" method="POST" enctype="multipart/form-data">
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
                <form class="add-new-user pt-0" id="editUserForm" action="{{ route('partner.client.update') }}" method="POST" enctype="multipart/form-data">
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

    <script>
            const guard = @json(Auth::getDefaultDriver());  // e.g., "web" or "api"
            var assetPath = $('body').attr('data-asset-path');
            var urlPath =  assetPath + guard +"/client/list/json";
    </script>
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

    <script src="{{ asset('admin/assets/pages/app-partner-customer-list.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/partner-customer-validation.js') }}"></script>

    <script src="{{ asset('admin/assets/custom/main.js') }}"></script>


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
                    url: "{{ route('partner.client.edit') }}",
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

        $('#countryf').click(function(){
            $('.country-div').toggle();
        });

        $('#cityf').click(function(){
            $('.city-div').toggle();
        });

        $('#statusf').click(function(){
            $('.status-div').toggle();
        });

        $('#created-datef').click(function(){
            $('.created-date-div').toggle();
        });

        // Basic Select
        $('#default-chk').click(function(){
            if ($('#created-datef:checkbox:checked').length > 0) {
                $('#created-datef').trigger('click');
            }


            if($('#countryf:checkbox:checked').length > 0){

            }else{
            $('#countryf').trigger('click');
            }

            if($('#cityf:checkbox:checked').length > 0){

            }else{
                $('#cityf').trigger('click');
            }

            if($('#statusf:checkbox:checked').length > 0){

            }else{
                $('#statusf').trigger('click');
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

            $('input[name="cityf"]').each(function () {
                if ($(this).prop('checked')) {

                }else{
                    $(this).trigger('click');
                }
            });

            $('input[name="statusf"]').each(function () {
                if ($(this).prop('checked')) {

                }else{
                    $(this).trigger('click');
                }
            });

            $('input[name="created-datef"]').each(function () {
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
            if($('#cityf:checkbox:checked').length > 0){
                $('#cityf').trigger('click');
            }
            if($('#statusf:checkbox:checked').length > 0){
                $('#statusf').trigger('click');
            }
            if($('#created-datef:checkbox:checked').length > 0){
                $('#created-datef').trigger('click');
            }

        });

        // Update Checkbox
        $('#update-chk').click(function(){
            var countryf = $('#countryf:checked').val();
            var cityf = $('#cityf:checked').val();
            var statusf = $('#statusf:checked').val();
            var created_datef = $('#created-datef:checked').val();


            jQuery.ajax({
                url:"{{ url('admin/client/filterList/update') }}",
                method: 'post',
                type: 'html',
                data: {
                    "_token": "{{ csrf_token() }}",
                    countryf: countryf,
                    cityf: cityf,
                    statusf: statusf,
                    created_datef: created_datef,
                },
                success: function(data){
                    if(data){
                        toastr['success']('Filter updated successfully', 'Success', { hideDuration: 3000 });
                        // $('#filter_final_div').load(location.href + ' #filter_final_div');
                    }
                }
            });

        });

        //////////////////////////////////////////////////////////////////////////////////////////
       
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
                url: "{{ route('partner.client.status.update') }}",
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
                url: "/partner/client/view/" + id,
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

@endsection
