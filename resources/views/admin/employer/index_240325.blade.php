@extends('layout.admin.admin_layout')

@section('title','Employer')
    
@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />

    @if (Auth::guard('admin')->user()->user_type == 2)
      
        @if (isset($perm) && $perm->delete_employer == 0)
        <style>
        .delemployer{
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
        <!-- Employer List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">Search Filter</h5>
                <div class="row">
                    <div class="col-md-3 mb-3 candidate-name-div" @if(isset($filter_user) && $filter_user->candidate_name_filter == 1) @else style="display:none" @endif>
                        <select name="" id="by-candidate" class="form-select select2">
                            <option value="">Select Candidate</option>
                            @foreach ($candidates as $candidate)
                                @php
                                    $candDet = DB::table('candidates')->where('id','=',$candidate->cand_id)->first();
                                    $countCand = DB::table('bookings')->where('cand_id','=',$candidate->cand_id)->count();
                                @endphp
                                <option value="{{ $candDet->id }}">{{ $candDet->cand_name.' ('.$countCand.')' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 country-div" @if(isset($filter_user) && $filter_user->country_filter == 1) @else style="display:none" @endif>
                        <select name="" id="by-country" class="form-select select2">
                            <option value="">Select Country</option>
                            @foreach ($countryfs as $countryf)
                                @php
                                    $countryf2 = DB::table('countries')->where('id','=',$countryf->country_id)->first();
                                @endphp
                                <option value="{{ $countryf2->id }}">{{ $countryf2->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 city-div" @if(isset($filter_user) && $filter_user->city_filter == 1) @else style="display:none" @endif>
                        <select name="" id="by-city" class="form-select select2">
                            <option value="">Select City</option>
                            @foreach ($cityfs as $cityf)
                                @php
                                    $cityf2 = DB::table('cities')->where('id','=',$cityf->city_id)->first();
                                @endphp
                                <option value="{{ $cityf2->id }}">{{ $cityf2->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 booking-status-div" @if(isset($filter_user) && $filter_user->booking_status_filter == 1) @else style="display:none" @endif>
                        <select name="" id="by-booking-status" class="form-select select2">
                            <option value="">Booking Status</option>
                            @foreach ($bookingsts as $bookingst)
                                @if ($bookingst->booking_status == 1)
                                    <option value="Confirm">Confirm</option>
                                @elseif($bookingst->booking_status == 2)
                                    <option value="Cancel">Cancel</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 booking-date-div" @if(isset($filter_user) && $filter_user->booking_date_filter == 1) @else style="display:none" @endif>
                        <input type="text" name="" id="by-booking-date" class="form-control createdate-picker" placeholder="Booking date...">
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Employer Name</th>
                            <th>City</th>
                            <th>Mobile</th>
                            <th>Candidate Name</th>
                            <th>Passport No</th>
                            <th>Reference No</th>
                            <th>Booking Date</th>
                            <th>Status</th>
                            <th>Country ID</th>
                            <th>City ID</th>
                            <th>Candidate ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

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
                            <div class="form-check mt-2" id="booking-status-f">
                                <input class="form-check-input" type="checkbox" name="booking-statusf" value="1" id="booking-statusf" @if(isset($filter_user) && $filter_user->booking_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="booking-statusf"> Booking Status</label>
                            </div>
                            <div class="form-check mt-2" id="booking-date-f">
                                <input class="form-check-input" type="checkbox" name="booking-datef" value="1" id="booking-datef" @if(isset($filter_user) && $filter_user->booking_date_filter == 1) checked @endif />
                                <label class="form-check-label" for="booking-datef"> Booking Date</label>
                            </div>
                            <div class="form-check mt-2" id="candidate-name-f">
                                <input class="form-check-input" type="checkbox" name="candidate-namef" value="1" id="candidate-namef" @if(isset($filter_user) && $filter_user->candidate_name_filter == 1) checked @endif />
                                <label class="form-check-label" for="candidate-namef"> Candidate Name</label>
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
    {{-- <script src="{{ asset('admin/assets/custom/main.js') }}"></script> --}}

    <!-- Page JS -->
    <script src="{{ asset('admin/assets/pages/app-employer-list.js') }}"></script>

    <script>
        $(document).ready(function(){

            $('#countryf').click(function(){
                $('.country-div').toggle();
            });

            $('#cityf').click(function(){
                $('.city-div').toggle();
            });

            $('#booking-statusf').click(function(){
                $('.booking-status-div').toggle();
            });

            $('#booking-datef').click(function(){
                $('.booking-date-div').toggle();
            });

            $('#candidate-namef').click(function(){
                $('.candidate-name-div').toggle();
            });

            // Basic Select
            $('#default-chk').click(function(){
                if ($('#booking-datef:checkbox:checked').length > 0) {
                    $('#booking-datef').trigger('click');
                }

                if ($('#candidate-namef:checkbox:checked').length > 0) {
                    $('#candidate-namef').trigger('click');
                }


                if($('#countryf:checkbox:checked').length > 0){

                }else{
                    $('#countryf').trigger('click');
                }

                if($('#cityf:checkbox:checked').length > 0){

                }else{
                    $('#cityf').trigger('click');
                }

                if($('#booking-statusf:checkbox:checked').length > 0){

                }else{
                    $('#booking-statusf').trigger('click');
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

                $('input[name="booking-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="booking-datef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="candidate-namef"]').each(function () {
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
                if($('#booking-statusf:checkbox:checked').length > 0){
                    $('#booking-statusf').trigger('click');
                }
                if($('#booking-datef:checkbox:checked').length > 0){
                    $('#booking-datef').trigger('click');
                }

                if($('#candidate-namef:checkbox:checked').length > 0){
                    $('#candidate-namef').trigger('click');
                }

            });

            // Update Checkbox
            $('#update-chk').click(function(){
                var countryf = $('#countryf:checked').val();
                var cityf = $('#cityf:checked').val();
                var booking_statusf = $('#booking-statusf:checked').val();
                var booking_datef = $('#booking-datef:checked').val();
                var candidate_namef = $('#candidate-namef:checked').val();


                jQuery.ajax({
                    url:"{{ url('admin/employer/filterList/update') }}",
                    method: 'post',
                    type: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        countryf: countryf,
                        cityf: cityf,
                        booking_statusf: booking_statusf,
                        booking_datef: booking_datef,
                        candidate_namef: candidate_namef,
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

@endsection