@extends('layout.admin.admin_layout')

@section('title','CRM Candidate')

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
    </style>

    @if (Auth::guard('admin')->user()->user_type == 2)
        @if (isset($perm) && $perm->add_candidate == 0)
        <style>
        .addcandidate{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->edit_candidate == 0)
        <style>
        .edcandidate{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->delete_candidate == 0)
        <style>
        .delcandidate{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->publish_candidate == 0)
            <style>
                .pubcandidate{
                    display: none;
                }
            </style>
        @endif
    @endif

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-4 mb-4">

            
            <div class="col-sm-6 col-xl-3 new-candidate-div" @if (isset($filter_user) && $filter_user->new_candidate_status_filter == 1) @else style="display:none" @endif>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="card-title mb-0">
                                <h5 class="mb-0 me-2">1000</h5>
                                <small>New Candidate</small>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-success rounded-pill p-2">
                                <i class="ti ti-server ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>                


            
            <div class="col-sm-6 col-xl-3 ready-for-publish-div" @if (isset($filter_user) && $filter_user->ready_for_published_status_filter == 1) @else style="display: none" @endif>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="card-title mb-0">
                                <h5 class="mb-0 me-2">500</h5>
                                <small>Ready For Publish</small>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-success rounded-pill p-2">
                                <i class="ti ti-server ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            


            {{-- <div class="col-sm-6 col-xl-3">
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
            </div> --}}
        </div>
        <!-- Users List Table -->
        @php
            $job_types = DB::table('candidates as cand')->leftjoin('professions as proff','proff.id','=','cand.jobtype_id')->select('proff.eng_name','proff.id')->groupBy('proff.eng_name')->groupBy('proff.id')->where('cand.jobtype_id','!=','')->where('cand.isdelete','=',0)->get(); 
            $createbys = DB::table('candidates as cand2')->leftjoin('admins as admin','admin.id','=','cand2.admin_id')->select('admin.id','admin.name')->groupBy('admin.id')->groupBy('admin.name')->where('cand2.admin_id','!=','')->where('cand2.isdelete','=',0)->get();
            $religions = DB::table('candidates as cand3')->leftjoin('religions as religion','cand3.religion_id','=','religion.id')->select('religion.id','religion.name')->groupBy('religion.id')->groupBy('religion.name')->where('cand3.religion_id','!=','')->where('cand3.isdelete','=',0)->get();
            $regions = DB::table('candidates as cand4')->leftjoin('regions as region','cand4.region_id','=','region.id')->select('region.id','region.name')->groupBy('region.id')->groupBy('region.name')->where('cand4.region_id','!=','')->where('cand4.isdelete','=',0)->get();
            $citiesfs = DB::table('candidates')->select('candcity_text')->groupBy('candcity_text')->where('candcity_text','!=','')->get();
            $gulfexperiences = DB::table('candidates')->select('gulfexperience')->groupBy('gulfexperience')->where('gulfexperience','!=','')->get();
            $publishes = DB::table('candidates')->select('publish')->groupBy('publish')->where('publish','!=','')->get();
            $medicalStatus = DB::table('candidates')->select('medical_health_status')->groupBy('medical_health_status')->where('medical_health_status','!=','')->get();
            $careoffFilters = DB::table('candidates as cand5')->leftjoin('admins as careoff','careoff.id','=','cand5.careoff_id')->select('careoff.id','careoff.name')->groupBy('careoff.id')->groupBy('careoff.name')->where('cand5.careoff_id','!=','')->where('cand5.isdelete','=',0)->get();
            $paymentStatusFilters = DB::table('candidates')->select('cand_payment_status')->groupBy('cand_payment_status')->where('cand_payment_status','!=','')->get();

            


        @endphp
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">Search Filter</h5>
                {{-- <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0"> --}}
                <div class="row">
                    {{-- <div class="col-md user_role"></div>
                    <div class="col-md user_plan"></div>
                    <div class="col-md user_status"></div> --}}
                    <div class="col-md-3 mb-3 pass-type-div" @if(isset($filter_user) && $filter_user->pass_type_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-pass-type" class="form-select select22" multiple data-placeholder="Passport Type">
                            <option value="">Passport Type</option>
                            <option value="ECNR">ECNR</option>
                            <option value="ECR">ECR</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 job-type-div" @if(isset($filter_user) && $filter_user->job_type_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-job-type" class="form-select select22" multiple data-placeholder="Job Type">
                            <option value="">Job Type</option>
                            @foreach ($job_types as $job_type)
                                <option value="{{ $job_type->id }}">{{ $job_type->eng_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 careoff-div" @if(isset($filter_user) && $filter_user->careoff_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-careoff" class="form-select select22" data-placeholder="Careoff" multiple>
                            <option value="">Careoff</option>
                            @foreach ($careoffFilters as $careoffFilter)
                                <option value="{{ $careoffFilter->id }}">{{ $careoffFilter->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 create-by-div" @if(isset($filter_user) && $filter_user->create_by_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-create-by" class="form-select select22" multiple data-placeholder="Created By">
                            <option value="">Created By</option>
                            @foreach ($createbys as $createby)
                                <option value="{{ $createby->id }}">{{ $createby->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 religion-div" @if(isset($filter_user) && $filter_user->religion_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-religion" class="form-select select22" data-placeholder="Religion" multiple>
                            <option value="">Religion</option>
                            @foreach ($religions as $religion)
                                <option value="{{ $religion->id }}">{{ $religion->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 city-div" @if(isset($filter_user) && $filter_user->city_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-city" class="form-select select22" data-placeholder="City" multiple>
                            <option value="">City</option>
                            @foreach ($citiesfs as $citiesf)

                                <option value="{{ $citiesf->candcity_text }}">{{ $citiesf->candcity_text }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 region-div" @if(isset($filter_user) && $filter_user->region_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-region" class="form-select select22" data-placeholder="Region" multiple>
                            <option value="">Region</option>
                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 experience-region-div" @if(isset($filter_user) && $filter_user->experience_region_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-experience-region" class="form-select select22" data-placeholder="Experience Region" multiple>
                            <option value="">Experience Region</option>
                            @foreach ($gulfexperiences as $gulfexperience)
                                <option value="{{ $gulfexperience->gulfexperience }}">@if($gulfexperience->gulfexperience == 1) {{ 'Indian Experience' }} @elseif($gulfexperience->gulfexperience == 2) {{ 'Gulf Experience' }} @endif</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 publish-status-div" @if(isset($filter_user) && $filter_user->publish_status_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-publish-status" class="form-select select22" data-placeholder="Publish Status" multiple>
                            <option value="">Publish Status</option>
                            @foreach ($publishes as $publish)
                                <option value="{{ $publish->publish }}">@if($publish->publish == 1) {{ 'Publish' }} @else {{ 'Unpublish' }} @endif</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="col-md-3 mb-3 candidate-status-div" @if(isset($filter_user) && $filter_user->candidate_status_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-candidate-status" class="form-select select22">
                            <option value="">Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div> --}}
                    <div class="col-md-3 mb-3 created-date-div" @if(isset($filter_user) && $filter_user->create_date_filter == 1) @else style="display: none" @endif>
                        <input type="text" name="" id="by-created-date" class="form-control createdate-picker" placeholder="Created date with range...">
                    </div>
                    <div class="col-md-3 mb-3 medical-expiry-div" @if(isset($filter_user) && $filter_user->medical_expiry_date_filter == 1) @else style="display: none" @endif>
                        <input type="text" name="" id="by-medical-expiry-date" class="form-control medicalexpiry-picker" placeholder="Medical expiry date...">
                    </div>
                    <div class="col-md-3 mb-3 cand-status-div" @if(isset($filter_user) && $filter_user->candidate_status_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-cand-status" class="form-select select22" multiple data-placeholder="Candidate Status">
                            <option value="">Candidate Status</option>
                            {{-- @foreach ($candStatusFilters as $candStatusFilter)
                                <option value="{{ $candStatusFilter->candidate_current_status }}">{{ $candStatusFilter->candidate_current_status }}</option>
                            @endforeach --}}


                            
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 cand-medical-status-div" @if(isset($filter_user) && $filter_user->medical_health_status_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-cand-medical-status" class="form-select select22" data-placeholder="Medical Status" multiple>
                            <option value="">Medical Status</option>
                            @foreach ($medicalStatus as $medicalSt)
                                <option value="{{ $medicalSt->medical_health_status }}">{{ $medicalSt->medical_health_status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 cand-payment-status-div" @if(isset($filter_user) && $filter_user->cand_payment_status_filter == 1) @else style="display: none"  @endif>
                        <select name="" id="by-cand-payment-status" class="form-select select22" data-placeholder="Payment Status" multiple>
                            <option value="">Payment Status</option>
                            @foreach ($paymentStatusFilters as $paymentStatusFilter)
                                <option value="{{ $paymentStatusFilter->cand_payment_status }}">{{ $paymentStatusFilter->cand_payment_status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Candidate</th>
                            <th>Passport</th>
                            {{-- <th>File No</th> --}}
                            <th>Party Name</th>
                            {{-- <th>Mofa No</th> --}}
                            {{-- <th>Stage</th> --}}
                            {{-- <th>Mofa Status</th> --}}
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <!-- View Model Candidate Details-->
        <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="exampleModalToggleLabel">New Candidate</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-start align-items-sm-center gap-4">
                                                <div>
                                                    <img src="http://127.0.0.1:8000/admin/assets/images/candidate/19.jpg" alt="Avatar" class="d-block w-px-100 h-px-100 rounded" data-bs-toggle="modal" data-bs-target="#imageUpload">
                                                </div>
                                                <div>
                                                    <h6 class="offcanvas-title">Candidate Name</h6>
                                                    <span class="form-label">Ramu</span>
                                                    <h6 class="offcanvas-title">CAROFF</h6>
                                                    <span class="form-label">RUQAIYA</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="offcanvas-title">Candidate Name</h6>
                                            <span class="form-label">Ramu</span>
                                            <h6 class="offcanvas-title">CAROFF</h6>
                                            <span class="form-label">RUQAIYA</span>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="offcanvas-title">Candidate Name</h6>
                                            <span class="form-label">Ramu</span>
                                            <h6 class="offcanvas-title">CAROFF</h6>
                                            <span class="form-label">RUQAIYA</span>
                                        </div>
                                    </div>
                                    <!-- Secend Row-->
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-start align-items-sm-center gap-4">
                                               
                                                    <h6 class="offcanvas-title">Candidate Name</h6>
                                                    <span class="form-label">Ramu</span>
                                                    <h6 class="offcanvas-title">CAROFF</h6>
                                                    <span class="form-label">RUQAIYA</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="offcanvas-title">Candidate Name</h6>
                                            <span class="form-label">Ramu</span>
                                            <h6 class="offcanvas-title">CAROFF</h6>
                                            <span class="form-label">RUQAIYA</span>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="offcanvas-title">Candidate Name</h6>
                                            <span class="form-label">Ramu</span>
                                            <h6 class="offcanvas-title">CAROFF</h6>
                                            <span class="form-label">RUQAIYA</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-primary" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">Save and Next</button>
                </div>
              </div>
            </div>
        </div>
        <div class="modal fade" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel2">Modal 2</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Hide this modal and show the first with the button below.
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" data-bs-target="#exampleModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">Back to first</button>
            </div>
            </div>
        </div>
        </div>
        <!--- Add Candidate Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Candidate</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.candidate.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit" id="addSubmBtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Add Candidate End --->

        <!--- Edit Candidate Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="edituserLabel" class="offcanvas-title">Edit Candidate</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.candidate.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Edit Candidate End --->

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
                            <div class="form-check mt-2" id="pass-type-f">
                                <input class="form-check-input" type="checkbox" name="pass-typef" value="1" id="pass-typef" @if(isset($filter_user) && $filter_user->pass_type_filter == 1) checked @endif />
                                <label class="form-check-label" for="pass-typef"> Passport Type</label>
                            </div>
                            <div class="form-check mt-2" id="job-type-f">
                                <input class="form-check-input" type="checkbox" name="job-typef" value="1" id="job-typef" @if(isset($filter_user) && $filter_user->job_type_filter == 1) checked @endif />
                                <label class="form-check-label" for="job-typef"> Occupation</label>
                            </div>
                            <div class="form-check mt-2" id="careoff-f">
                                <input class="form-check-input" type="checkbox" name="careofff" value="1" id="careofff" @if(isset($filter_user) && $filter_user->careoff_filter == 1) checked @endif />
                                <label class="form-check-label" for="careofff"> Careoff</label>
                            </div>
                            <div class="form-check mt-2" id="create-by-f">
                                <input class="form-check-input" type="checkbox" name="create-byf" value="1" id="create-byf" @if(isset($filter_user) && $filter_user->create_by_filter == 1) checked @endif />
                                <label class="form-check-label" for="create-byf"> Create By</label>
                            </div>
                            <div class="form-check mt-2" id="create-date-f">
                                <input class="form-check-input" type="checkbox" name="create-datef" value="1" id="create-datef" @if(isset($filter_user) && $filter_user->create_date_filter == 1) checked @endif />
                                <label class="form-check-label" for="create-datef"> Create Date</label>
                            </div>
                            <div class="form-check mt-2" id="religion-f">
                                <input class="form-check-input" type="checkbox" name="religionf" value="1" id="religionf" @if(isset($filter_user) && $filter_user->religion_filter == 1) checked @endif />
                                <label class="form-check-label" for="religionf"> Religion</label>
                            </div>
                            <div class="form-check mt-2" id="region-f">
                                <input class="form-check-input" type="checkbox" name="regionf" value="1" id="regionf" @if(isset($filter_user) && $filter_user->region_filter == 1) checked @endif />
                                <label class="form-check-label" for="regionf"> Region</label>
                            </div>
                            <div class="form-check mt-2" id="city-f">
                                <input class="form-check-input" type="checkbox" name="cityf" value="1" id="cityf" @if(isset($filter_user) && $filter_user->city_filter == 1) checked @endif />
                                <label class="form-check-label" for="cityf"> City</label>
                            </div>
                            <div class="form-check mt-2" id="experience-region-f">
                                <input class="form-check-input" type="checkbox" name="experience-regionf" value="1" id="experience-regionf" @if(isset($filter_user) && $filter_user->experience_region_filter == 1) checked @endif />
                                <label class="form-check-label" for="experience-regionf"> Experience Region</label>
                            </div>
                            <div class="form-check mt-2" id="medical-expiry-date-f">
                                <input class="form-check-input" type="checkbox" name="medical-expiry-datef" value="1" id="medical-expiry-datef" @if(isset($filter_user) && $filter_user->medical_expiry_date_filter == 1) checked @endif />
                                <label class="form-check-label" for="medical-expiry-datef"> Medical Expiry Date</label>
                            </div>
                            <div class="form-check mt-2" id="candidate-status-f">
                                <input class="form-check-input" type="checkbox" name="candidate-statusf" value="1" id="candidate-statusf" @if(isset($filter_user) && $filter_user->candidate_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="candidate-statusf"> Candidate Status</label>
                            </div>
                            <div class="form-check mt-2" id="publish-status-f">
                                <input class="form-check-input" type="checkbox" name="publish-statusf" value="1" id="publish-statusf" @if(isset($filter_user) && $filter_user->publish_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="publish-statusf"> Publish Status</label>
                            </div>
                            <div class="form-check mt-2" id="new-candidate-status-f">
                                <input class="form-check-input" type="checkbox" name="new-candidate-statusf" value="1" id="new-candidate-statusf" @if(isset($filter_user) && $filter_user->new_candidate_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="new-candidate-statusf"> New Candidate</label>
                            </div>
                            <div class="form-check mt-2" id="ready-for-published-status-f">
                                <input class="form-check-input" type="checkbox" name="ready-for-published-statusf" value="1" id="ready-for-published-statusf" @if(isset($filter_user) && $filter_user->ready_for_published_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="ready-for-published-statusf"> Ready for Published</label>
                            </div>
                            <div class="form-check mt-2" id="cand-medical-status-f">
                                <input class="form-check-input" type="checkbox" name="cand-medical-statusf" value="1" id="cand-medical-statusf" @if(isset($filter_user) && $filter_user->medical_health_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="cand-medical-statusf"> Medical Status</label>
                            </div>
                            <div class="form-check mt-2" id="cand-payment-status-f">
                                <input class="form-check-input" type="checkbox" name="cand-payment-statusf" value="1" id="cand-payment-statusf" @if(isset($filter_user) && $filter_user->cand_payment_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="cand-payment-statusf"> Payment Status</label>
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

        <!-- Delete Staff start -->
        <div class="modal fade" id="deleteStaff" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.candidate.delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="staff_id" id="delStaffID">
                    <div class="modal-body">
                    <div class="row">
                        <div class="col mb-12">
                        <p>Are you sure!, to delete candidate?</p>
                        </div>
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-danger btn-sm " id="disbtn">Delete</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Delete Staff end -->
        <!-- Update Published Status Start -->
        <div class="modal fade" id="publish" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel125">Update Published Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.candidate.bulk.published.update') }}" method="POST" id="updatepublishstatus">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="idsv" id="idsv">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="publish" id="published" value="1"/>
                                        <label class="form-check-label" for="published">Publish</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="publish" id="unpublished" value="0"/>
                                        <label class="form-check-label" for="unpublished">Unpublish</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p>Total publish is: <span id="countcan"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Update Published Status end -->

        <!-- Download CV as per company Start -->
        <div class="modal fade" id="downloadcv" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel125">Download CV as per company</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.cvmultiplepartexec') }}" method="POST" id="downloadCVvalidation2">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                @php
                                    $partners = DB::table('partners')->where('status','=',1)->get();
                                @endphp
                                <div class="col-md-12 mb-3">
                                    <input type="hidden" name="candcv_id" id="candcv_id" >
                                    <label for="partner_id" class="form-label">Office Name <span class="text-danger">*</span></label>
                                    <select name="partner_id" id="partner_id" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}">{{ $partner->rec_off_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger" id="errorPartner"></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                            {{-- <button type="submit" class="btn btn-primary" id="downloadCandCV">Download Cv</button> --}}
                            {{-- <button type="button" class="btn btn-primary btn-sm" id="downloadCvbtn">Download CV</button> 15072023--}}
                            <button type="submit" class="btn btn-primary btn-sm">Execute CV</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Status Modal Start -->
        <div class="modal fade" id="NewCandStatusUpdate" aria-hidden="true" aria-labelledby="NewCandStatusUpdateLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="NewCandStatusUpdateLabel">New Candidate</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="cand_id" id="candIDN">
                        <!-- Candidate Are Ready Status -->
                        <div class="row">

                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div>
                                            <h6 class="offcanvas-title">Candidate Name</h6>
                                            <span class="form-label" id="candidateName"></span>
                                        </div>
                                    </div>
        
                                    <div class="col-md-4">
                                        <div>
                                            <h6 class="offcanvas-title">Passport No</h6>
                                            <span class="form-label" id="candPassport"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-4">
                                        <button type="button" class="btn btn-danger btn-sm cancelledButton" >Cancelled</button>
                                            <button type="button" class="btn btn-warning btn-sm holdButton" >Hold</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('admin.candidate.newcandidateupdate') }}" method="POST" id="candidateareready" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Candidate Are Ready</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="candidate_are_ready" id="candidate_are_ready" value="1"/>
                                                    <label class="form-check-label" for="candidate_are_ready">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="candidate_are_ready" id="candidate_are_ready_not" value="0"/>
                                                    <label class="form-check-label" for="candidate_are_ready_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton" >Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton" >Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Publish for Selection -->
                                <form action="{{ route('admin.candidate.publishforselectionupdate') }}" method="POST" id="publishedforselection" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Published For Selection</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="published_for_selection" id="published_for_selection" value="1"/>
                                                    <label class="form-check-label" for="published_for_selection">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="published_for_selection" id="published_for_selection_not" value="0"/>
                                                    <label class="form-check-label" for="published_for_selection_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Selected Status -->
                                <form action="{{ route('admin.candidate.selected') }}" method="POST" id="selectedStatus" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Selected</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="selected" id="selected" value="1"/>
                                                    <label class="form-check-label" for="selected">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="selected" id="selected_not" value="0"/>
                                                    <label class="form-check-label" for="selected_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Visa Received Status -->
                                <form action="{{ route('admin.candidate.visareceived') }}" method="POST" id="visareceived" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Visa Received</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="visa_received" id="visa_received" value="1"/>
                                                    <label class="form-check-label" for="visa_received">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="visa_received" id="visa_received_not" value="0"/>
                                                    <label class="form-check-label" for="visa_received_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Passport in Embassy Status -->
                                <form action="{{ route('admin.candidate.passportinembassy') }}" method="POST" id="passportinembassy" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Passport in Embassy</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="passport_in_embassy" id="passport_in_embassy" value="1"/>
                                                    <label class="form-check-label" for="passport_in_embassy">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="passport_in_embassy" id="passport_in_embassy_not" value="0"/>
                                                    <label class="form-check-label" for="passport_in_embassy_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Visa Stamped -->
                                <form action="{{ route('admin.candidate.visastamped') }}" method="POST" id="visastamped" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Visa Stamped</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="visa_stamped" id="visa_stamped" value="1"/>
                                                    <label class="form-check-label" for="visa_stamped">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="visa_stamped" id="visa_stamped_not" value="0"/>
                                                    <label class="form-check-label" for="visa_stamped_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Applied For Emigration -->
                                <form action="{{ route('admin.candidate.appliedforemigration') }}" method="POST" id="appliedforemigration" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Applied For Emigration</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="applied_for_emigration" id="applied_for_emigration" value="1"/>
                                                    <label class="form-check-label" for="applied_for_emigration">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="applied_for_emigration" id="applied_for_emigration_not" value="0"/>
                                                    <label class="form-check-label" for="applied_for_emigration_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Emigration Approved -->
                                <form action="{{ route('admin.candidate.emigrationapproved') }}" method="POST" id="emigrationapproved" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Emigration Approved</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="emigration_approved" id="emigration_approved" value="1"/>
                                                    <label class="form-check-label" for="emigration_approved">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="emigration_approved" id="emigration_approved_not" value="0"/>
                                                    <label class="form-check-label" for="emigration_approved_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Waiting for FLight Ticket -->
                                <form action="{{ route('admin.candidate.waitingforticket') }}" method="POST" id="waitingforflightticket" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Waiting For Flight Ticket</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="waiting_for_flight_ticket" id="waiting_for_flight_ticket" value="1"/>
                                                    <label class="form-check-label" for="waiting_for_flight_ticket">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="waiting_for_flight_ticket" id="waiting_for_flight_ticket_not" value="0"/>
                                                    <label class="form-check-label" for="waiting_for_flight_ticket_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Ticket Confirmed -->
                                <form action="{{ route('admin.candidate.ticketconfirmed') }}" method="POST" id="ticketconfirmed" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Ticket Confirmed</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="ticket_confirmed" id="ticket_confirmed" value="1"/>
                                                    <label class="form-check-label" for="ticket_confirmed">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="ticket_confirmed" id="ticket_confirmed_not" value="0"/>
                                                    <label class="form-check-label" for="ticket_confirmed_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Deployed Status -->
                                <form action="{{ route('admin.candidate.deployed') }}" method="POST" id="deployedStatus" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Deployed</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="deployed" id="deployed" value="1"/>
                                                    <label class="form-check-label" for="deployed">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="deployed" id="deployed_not" value="0"/>
                                                    <label class="form-check-label" for="deployed_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Cancelled Status -->
                                <form action="{{ route('admin.candidate.cancelled') }}" method="POST" id="cancelledStatus" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Cancelled</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="cancelled" id="cancelled" value="1"/>
                                                    <label class="form-check-label" for="cancelled">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="cancelled" id="cancelled_not" value="0"/>
                                                    <label class="form-check-label" for="cancelled_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Hold Status -->
                                <form action="{{ route('admin.candidate.hold') }}" method="POST" id="holdStatus" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Hold</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="hold" id="hold" value="1"/>
                                                    <label class="form-check-label" for="hold">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="hold" id="hold_not" value="0"/>
                                                    <label class="form-check-label" for="hold_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Visa Cancelled Status -->
                                <form action="{{ route('admin.candidate.visacancelled') }}" method="POST" id="visacencelled" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Visa Cancelled</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="visa_cencelled" id="visa_cencelled" value="1"/>
                                                    <label class="form-check-label" for="visa_cencelled">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="visa_cencelled" id="visa_cencelled_not" value="0"/>
                                                    <label class="form-check-label" for="visa_cencelled_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-foo">

                    </div>
                </div>
            </div>
        </div>
        <!-- Update Status Modal End -->
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
    
    @if (Auth::guard('admin')->user()->user_type == 1)
        <script>
            var cand_status = '1';

        </script>
    @else
        @if (isset($perm) && $perm->candidate_status == 1)
            <script>
                var cand_status = '1';

            </script>
        @else
            <script>
                var cand_status = '0';

            </script>    
        @endif
    @endif

    <!-- Page JS -->
    <script src="{{ asset('admin/assets/pages/app-crm-candidate-list.js') }}"></script>
    {{-- <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script> --}}

    <script src="{{ asset('admin/assets/pages/validation/candidate-validation.js') }}"></script>
    
    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>

    {{-- <script src="{{ asset('admin/assets/custom/main.js') }}"></script> --}}

    <script>
        $(document).ready(function(){
            $('.select22').select2();
        });
    </script>
    <script>
        $(document).on('click','.reloadModal',function(event){
            event.preventDefault();
            var pertner_id = $('.partner_id2').val();

            $(".partner_id2").trigger("change");            


        });
    </script>

    <script>
        $(document).on('change','.partner_id2',function(event){
            event.preventDefault();
            var pertner_id = $(this).val();
            var ids = $("#candcvs_id").val();

            // Get ALl Candidate Executed and Non Executed List
            jQuery.ajax({
                url: "{{ route('admin.candidate.getCVData') }}",
                method: "GET",
                type: "html",
                data: {
                    ids: ids,
                    pertner_id: pertner_id
                },
                success: function(response){
                    $("#executeData").html(response.res);
                    if(response.cv_status == 0){
                        $('.shareCVButton').attr("disabled",true);
                    }else{
                        $('.shareCVButton').attr("disabled",false);
                    }
                }
            });

        });
    </script>

    <script>
        toastr.options = {
            "timeOut": 5000,
            "showDuration": 300,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
        };
        $(document).on('click','.shareCVButton',function(event){
            event.preventDefault();

            // Form Validation
            // Check Flag is true
            var isValid = true;
            $('.error-class').text('');

            var partner_id = $('#partner_id2').val();
            // var cv_purpose = $('input[name="cv_purpose"]:checked').val();
            var cv_purpose = $('input[name="cv_purpose"]').is(":checked");

            if (partner_id === '') {
                $('#error-partner').text("Please Select Partner...");
                isValid = false;
            }

            if(cv_purpose == true){
                var cv_purpose_value = $('input[name="cv_purpose"]:checked').val();
                if (cv_purpose_value == 1) {
                    var shared_to = $('input[name="share_to"]').is(":checked");

                    if(shared_to === true){
                        var shared_to_value = $('input[name="share_to"]:checked').val();
                        if(shared_to_value == 1){
                            var staff_id2 = $('#staff_id2').val();

                            if (staff_id2 === '') {
                                $('#error-staff2').text("Please Select Staff...");
                                isValid = false;
                            }

                        }
                    }else{
                        $('#error-shareto').text("Please Select Share To...");
                        isValid = false;
                    }

                }
            }else{
                $('#error-cvpurpose').text("Please Select CV Purpose...");
                isValid = false;
            }




            var formdata = $('#sharedCVvalidation2').serialize();

            if (isValid) {
                // Disable Button
                $('#shareCVButton').attr("disabled",true);

                // Progress Bar Start
                $('.progress-wrapper').show();
                updateProgressBar(0); // Initialize the progress bar to 0%

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.cvmultisharepartner') }}",
                    data: formdata,
                    xhr: function(){
                        var xhr = new window.XMLHttpRequest();
                        // Upload progress
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                updateProgressBar(percentComplete * 100);
                            }
                        }, false);
                        // Download progress
                        xhr.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                updateProgressBar(percentComplete * 100);
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response){
                        if(response.cv_purpose == 0){
                            
                            if(response.total_files > 0){
                                const files = response.files;

                                files.forEach(function(fileUrl){
                                    const a = document.createElement('a');
                                    a.href = fileUrl;
                                    a.target = '_blank';
                                    a.download = fileUrl.split('/').pop();
                                    document.body.appendChild(a);
                                    a.click();
                                    document.body.removeChild(a);
                                });

                                toastr.success(response.message);

                            }else{
                                toastr.success(response.message);
                            }

                        }
                        
                        if(response.cv_purpose == 1){
                            toastr.success(response.message);
                        }

                        updateProgressBar(100); // Update to 100% on success
                        // Handle the response
                        setTimeout(function() {
                            $(".progress-wrapper").hide();
                            updateProgressBar(0); // Reset progress bar
                        }, 2000);

                        // Form Reset
                        $('#shareCVButton').attr("disabled",false);


                        // Hide Modal
                        $('#sharedCV').modal('hide');


                    },
                    error: function(xhr){
                        toastr.error(xhr.responseText);
                        $("#progress-label-new").text("Error!");
                        $('#shareCVButton').attr("disabled",false);
                        setTimeout(function() {
                            $(".progress-wrapper").hide();
                            updateProgressBar(0); // Reset progress bar
                        }, 2000);
                    }
                });    
            }

            

        });

        function updateProgressBar(value) {
            $(".progress-bar").width(value + '%');
            $("#progress-label-new").text(Math.round(value) + '%');
        }

    </script>
    

    <script>
        $(document).ready(function(){
            $('[name="cv_purpose"]').on("change",function(){
                var value = $(this).val();
                
                if (value == '1') {
                    $('#disShareTo').show();

                } else {
                    $('[name="share_to"]').prop("checked",false);        
                    $('#disShareTo').hide();
                    $('#disStaffList').hide();
                }

            });

            $('[name="share_to"]').on("change",function(){
                var value = $(this).val();
                
                if (value == '1') {
                    $('#disStaffList').show();

                } else {
                    $('#disStaffList').hide();
                    
                }

            });
        });
    </script>


    <script>
        $(document).ready(function(){
            // $('#by-created-date').flatpickr();
            $('#by-created-date').flatpickr({
                mode: "range"
            });
            $('#by-medical-expiry-date').flatpickr();

            // $('.createdate-picker').on('change',function(){
            //     var newDate = $(this).val();
            //     var newSplitDate = newDate.split("to");
            //     var startDate = newSplitDate[0];
            //     var endDate = newSplitDate[1];

                
            //     var ObbjecStartDate = new Date(startDate);
            //     var ObbjecendDate = new Date(endDate);

                
            //     if (!isNaN(ObbjecStartDate) && !isNaN(ObbjecendDate)) {
            //         var timeDifference = ObbjecendDate.getTime() - ObbjecStartDate.getTime();
            //         var dayDifference = timeDifference / (1000 * 3600 * 24);

            //         let year,month,day,finalDay;

            //         let date = new Date(ObbjecStartDate);
                    
            //         let datesArray = [];
            //         datesArray.push(startDate);
            //         for (let i = 0; i < dayDifference; i++) {
            //             // Increment the date by one day
            //             date.setDate(date.getDate() + 1);
                        
            //             year = date.getFullYear();
            //             month = (date.getMonth() + 1).toString().padStart(2, '0');
            //             day = date.getDate().toString().padStart(2, '0');

            //             finalDay = year+"-"+month+"-"+day;


            //             datesArray.push(finalDay);
            //         }

            //         console.log(datesArray);
            //     }
            // });


        });
    </script>

    <script>
        function check_pass(pass){
            var pass_no = pass.value;
            var _token = $('meta[name="csrf-token"]').attr('content');
            jQuery.ajax({
                url: "{{ url('admin/candidate/check/pass-no/new') }}",
                method: "POST",
                type: "html",
                data: {
                    "_token": "{{ csrf_token() }}",
                    pass_no: pass_no
                },
                success: function(result){
                    if (result) {
                        $("#errorPassportNo").text('Passport no "'+pass_no+'" already exists');
                        $("#duplipass").show();
                        $("#addSubmBtn").prop('disabled',true);
                    } else {
                        $("#errorPassportNo").text('');
                        $("#duplipass").hide();
                        $("#addSubmBtn").prop('disabled',false);
                    }
                }
            });
        }
        
    </script>
    <script>
        $(document).ready(function(){
            $('#repeatPass').on('change',function(){
                let value = $(this).is(':checked');

                if (value == true) {
                $('#addSubmBtn').prop('disabled',false);
                }else{
                $('#addSubmBtn').prop('disabled',true);
                }

            });
        });
    </script>
    <script>
        $(document).ready(function(){
            var table = $('.datatables-users').DataTable();

            $('#NewCandStatusUpdate').on('show.bs.modal',function(e){
                var editID = $(e.relatedTarget).data('id');
                $('#candIDN').val(editID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url: '{{ url("admin/candidate/status/get") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        // Candidate Are Ready
                        $('#candidateName').text(data.cand_name);
                        $('#candPassport').text(data.pass_no);
                        if (data.new_candidate == 1 && data.candidate_are_ready == 0 && data.hold == 0 && data.cancelled == 0) {
                            $('#NewCandStatusUpdateLabel').text("New Candidate");
                            $('#candidateareready').show();
                            $('#publishedforselection').hide();
                            $('#selectedStatus').hide();
                            $('#visareceived').hide();
                            $('#passportinembassy').hide();
                            $('#visastamped').hide();
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').hide();
                            $('#cancelledStatus').hide();
                            $('#holdStatus').hide();
                            $('#visacencelled').hide();
                            if (data.candidate_are_ready == 1) {
                                $('#candidate_are_ready').attr('checked',true).change();
                            } else {
                                $('#candidate_are_ready_not').attr('checked',true).change();
                            } 
                        }

                        // Publish For Selection
                        if (data.candidate_are_ready == 1 && data.published_for_selection == 0 && data.hold == 0 && data.cancelled == 0) {
                            $('#NewCandStatusUpdateLabel').text("Candidate Are Ready");
                            $('#candidateareready').hide();
                            $('#publishedforselection').show();
                            $('#selectedStatus').hide();
                            $('#visareceived').hide();
                            $('#passportinembassy').hide();
                            $('#visastamped').hide();
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').hide();
                            $('#cancelledStatus').hide();
                            $('#holdStatus').hide();
                            $('#visacencelled').hide();
                            if (data.published_for_selection == 1) {
                                $('#published_for_selection').attr('checked',true).change();
                            } else {
                                $('#published_for_selection_not').attr('checked',true).change();
                            } 
                        }
                        // Selected
                        if (data.published_for_selection == 1 && data.selected == 0 && data.hold == 0 && data.cancelled == 0) {
                            $('#NewCandStatusUpdateLabel').text("Published For Selection");
                            $('#candidateareready').hide();
                            $('#publishedforselection').hide();
                            $('#selectedStatus').show();
                            $('#visareceived').hide();
                            $('#passportinembassy').hide();
                            $('#visastamped').hide();
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').hide();
                            $('#cancelledStatus').hide();
                            $('#holdStatus').hide();
                            $('#visacencelled').hide();
                            if (data.selected == 1) {
                                $('#selected').attr('checked',true).change();
                            } else {
                                $('#selected_not').attr('checked',true).change();
                            } 
                        }
                        // Visa Received
                        if (data.selected == 1 && data.visa_received == 0 && data.hold == 0 && data.cancelled == 0) {
                            $('#NewCandStatusUpdateLabel').text("Selected");
                            $('#candidateareready').hide();
                            $('#publishedforselection').hide();
                            $('#selectedStatus').hide();
                            $('#visareceived').show();
                            $('#passportinembassy').hide();
                            $('#visastamped').hide();
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').hide();
                            $('#cancelledStatus').hide();
                            $('#holdStatus').hide();
                            $('#visacencelled').hide();
                            if (data.visa_received == 1) {
                                $('#visa_received').attr('checked',true).change();
                            } else {
                                $('#visa_received_not').attr('checked',true).change();
                            } 
                        }
                        // Passport in Embassy
                        if (data.visa_received == 1 && data.passport_in_embassy == 0 && data.hold == 0 && data.cancelled == 0) {
                            $('#NewCandStatusUpdateLabel').text("Visa Received");
                            $('#candidateareready').hide();
                            $('#publishedforselection').hide();
                            $('#selectedStatus').hide();
                            $('#visareceived').hide();
                            $('#passportinembassy').show();
                            $('#visastamped').hide();
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').hide();
                            $('#cancelledStatus').hide();
                            $('#holdStatus').hide();
                            $('#visacencelled').hide();
                            if (data.passport_in_embassy == 1) {
                                $('#passport_in_embassy').attr('checked',true).change();
                            } else {
                                $('#passport_in_embassy_not').attr('checked',true).change();
                            } 
                        }
                        // Visa Stamped
                        if (data.passport_in_embassy == 1 && data.visa_stamped == 0 && data.hold == 0 && data.cancelled == 0) {
                            $('#NewCandStatusUpdateLabel').text("Passport in Embassy");
                            $('#candidateareready').hide();
                            $('#publishedforselection').hide();
                            $('#selectedStatus').hide();
                            $('#visareceived').hide();
                            $('#passportinembassy').hide();
                            $('#visastamped').show();
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').hide();
                            $('#cancelledStatus').hide();
                            $('#holdStatus').hide();
                            $('#visacencelled').hide();
                            if (data.visa_stamped == 1) {
                                $('#visa_stamped').attr('checked',true).change();
                            } else {
                                $('#visa_stamped_not').attr('checked',true).change();
                            } 
                        }
                        // Applied For Emigration
                        if (data.visa_stamped == 1 && data.applied_for_emigration == 0 && data.hold == 0 && data.cancelled == 0) {
                            $('#NewCandStatusUpdateLabel').text("Visa Stamped");
                            $('#candidateareready').hide();
                            $('#publishedforselection').hide();
                            $('#selectedStatus').hide();
                            $('#visareceived').hide();
                            $('#passportinembassy').hide();
                            $('#visastamped').hide();
                            $('#appliedforemigration').show();
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').hide();
                            $('#cancelledStatus').hide();
                            $('#holdStatus').hide();
                            $('#visacencelled').hide();
                            if (data.applied_for_emigration == 1) {
                                $('#applied_for_emigration').attr('checked',true).change();
                            } else {
                                $('#applied_for_emigration_not').attr('checked',true).change();
                            } 
                        }
                        // Emigration Approved
                        if (data.applied_for_emigration == 1 && data.emigration_approved == 0 && data.hold == 0 && data.cancelled == 0) {
                            $('#NewCandStatusUpdateLabel').text("Applied For Emigration");
                            $('#candidateareready').hide();
                            $('#publishedforselection').hide();
                            $('#selectedStatus').hide();
                            $('#visareceived').hide();
                            $('#passportinembassy').hide();
                            $('#visastamped').hide();
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').show();
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').hide();
                            $('#cancelledStatus').hide();
                            $('#holdStatus').hide();
                            $('#visacencelled').hide();
                            if (data.emigration_approved == 1) {
                                $('#emigration_approved').attr('checked',true).change();
                            } else {
                                $('#emigration_approved_not').attr('checked',true).change();
                            } 
                        }
                        // Waiting For Flight Ticket
                        if (data.emigration_approved == 1 && data.waiting_for_flight_ticket == 0 && data.hold == 0 && data.cancelled == 0) {
                            $('#NewCandStatusUpdateLabel').text("Emigration Approved");
                            $('#candidateareready').hide();
                            $('#publishedforselection').hide();
                            $('#selectedStatus').hide();
                            $('#visareceived').hide();
                            $('#passportinembassy').hide();
                            $('#visastamped').hide();
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').show();
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').hide();
                            $('#cancelledStatus').hide();
                            $('#holdStatus').hide();
                            $('#visacencelled').hide();
                            if (data.waiting_for_flight_ticket == 1) {
                                $('#waiting_for_flight_ticket').attr('checked',true).change();
                            } else {
                                $('#waiting_for_flight_ticket_not').attr('checked',true).change();
                            } 
                        }
                        // Ticket Confirmed
                        if (data.waiting_for_flight_ticket == 1 && data.ticket_confirmed == 0 && data.hold == 0 && data.cancelled == 0) {
                            $('#NewCandStatusUpdateLabel').text("Waiting For Flight Ticket");
                            $('#candidateareready').hide();
                            $('#publishedforselection').hide();
                            $('#selectedStatus').hide();
                            $('#visareceived').hide();
                            $('#passportinembassy').hide();
                            $('#visastamped').hide();
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').show();
                            $('#deployedStatus').hide();
                            $('#cancelledStatus').hide();
                            $('#holdStatus').hide();
                            $('#visacencelled').hide();
                            if (data.ticket_confirmed == 1) {
                                $('#ticket_confirmed').attr('checked',true).change();
                            } else {
                                $('#ticket_confirmed_not').attr('checked',true).change();
                            } 
                        }
                        // Deployed
                        if (data.ticket_confirmed == 1 && data.deployed == 0 && data.hold == 0 && data.cancelled == 0) {
                            $('#NewCandStatusUpdateLabel').text("Ticket Confirmed");
                            $('#candidateareready').hide();
                            $('#publishedforselection').hide();
                            $('#selectedStatus').hide();
                            $('#visareceived').hide();
                            $('#passportinembassy').hide();
                            $('#visastamped').hide();
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').show();
                            $('#cancelledStatus').hide();
                            $('#holdStatus').hide();
                            $('#visacencelled').hide();
                            if (data.deployed == 1) {
                                $('#deployed').attr('checked',true).change();
                            } else {
                                $('#deployed_not').attr('checked',true).change();
                            } 
                        }
                        // Deployed
                        if (data.ticket_confirmed == 1 && data.deployed == 1 && data.hold == 0 && data.cancelled == 0) {
                            $('#NewCandStatusUpdateLabel').text("Deployed");
                            $('#candidateareready').hide();
                            $('#publishedforselection').hide();
                            $('#selectedStatus').hide();
                            $('#visareceived').hide();
                            $('#passportinembassy').hide();
                            $('#visastamped').hide();
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').show();
                            $('#cancelledStatus').hide();
                            $('#holdStatus').hide();
                            $('#visacencelled').hide();
                            if (data.deployed == 1) {
                                $('#deployed').attr('checked',true).change();
                            } else {
                                $('#deployed_not').attr('checked',true).change();
                            } 
                        }
                        // Cancelled
                        if (data.cancelled == 1) {
                            $('#NewCandStatusUpdateLabel').text("Cancelled");
                            $('#candidateareready').hide();
                            $('#publishedforselection').hide();
                            $('#selectedStatus').hide();
                            $('#visareceived').hide();
                            $('#passportinembassy').hide();
                            $('#visastamped').hide();
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').hide();
                            $('#cancelledStatus').show();
                            $('#holdStatus').hide();
                            $('#visacencelled').hide();
                            
                            $('#cancelled').attr('checked',true).change();
                             
                        }
                        // Hold
                        if (data.hold == 1) {
                            $('#NewCandStatusUpdateLabel').text("Hold");
                            $('#candidateareready').hide();
                            $('#publishedforselection').hide();
                            $('#selectedStatus').hide();
                            $('#visareceived').hide();
                            $('#passportinembassy').hide();
                            $('#visastamped').hide();
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').hide();
                            $('#cancelledStatus').hide();
                            $('#holdStatus').show();
                            $('#visacencelled').hide();
                            
                            $('#hold').attr('checked',true).change();
                             
                        }
                        // Visa Cancelled
                        if (data.visa_cencelled == 1) {
                            $('#NewCandStatusUpdateLabel').text("Visa Cancelled");
                            $('#candidateareready').hide();
                            $('#publishedforselection').hide();
                            $('#selectedStatus').hide();
                            $('#visareceived').hide();
                            $('#passportinembassy').hide();
                            $('#visastamped').hide();
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').hide();
                            $('#cancelledStatus').hide();
                            $('#holdStatus').hide();
                            $('#visacencelled').show();
                            
                            $('#visa_cencelled').attr('checked',true).change();
                             
                        }
                        
                    }
                });
            });

            // Hold Status Onclick
            $('.holdButton').on('click',function(e){
                e.preventDefault();
                var candID = $('#candIDN').val();
                var newradiovalue = "1";
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/hold") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'hold': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        table.ajax.reload();
                        $('#NewCandStatusUpdate').modal('hide');
                        
                    }
                });
            });

            // Cancelled Status Onclick
            $('.cancelledButton').on('click',function(e){
                e.preventDefault();
                var candID = $('#candIDN').val();
                var newradiovalue = "1";
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/cancelled") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'cancelled': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#NewCandStatusUpdate').modal('hide');
                        table.ajax.reload();
                    }
                });

            });

            // Update Candidate Ready Status
            $(document).on('submit','#candidateareready',function(e){
                e.preventDefault();
                var newradiovalue = $('input[name="candidate_are_ready"]:checked').val();
                var candID = $('#candIDN').val();
                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/newcandidateupdate") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'candidate_are_ready': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if (data.candidate_are_ready == 1 && data.published_for_selection == 0) {
                            $('#candidateareready').hide();
                            $('#publishedforselection').show();
                            $('#NewCandStatusUpdateLabel').text("Candidate Are Ready");
                            if (data.published_for_selection == 1) {
                                $('#published_for_selection').attr('checked',true).change();
                            } else {
                                $('#published_for_selection_not').attr('checked',true).change();
                            } 
                        }

                        table.ajax.reload();
                    }
                });
            });
            // Update Published For Selection Status
            $(document).on('submit','#publishedforselection',function(e){
                e.preventDefault();
                var newradiovalue = $('input[name="published_for_selection"]:checked').val();
                var candID = $('#candIDN').val();
                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/publishedforslection") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'published_for_selection': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if (data.published_for_selection == 1 && data.selected == 0) {
                            $('#publishedforselection').hide();
                            $('#selectedStatus').show();
                            $('#NewCandStatusUpdateLabel').text("Published For Selection");
                            if (data.selected == 1) {
                                $('#selected').attr('checked',true).change();
                            } else {
                                $('#selected_not').attr('checked',true).change();
                            } 
                        }
                        table.ajax.reload();
                    }
                });
            });
            // Update Selected Status
            $(document).on('submit','#selectedStatus',function(e){
                e.preventDefault();
                var newradiovalue = $('input[name="selected"]:checked').val();
                var candID = $('#candIDN').val();
                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/selected") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'selected': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if (data.selected == 1 && data.visa_received == 0) {
                            $('#selectedStatus').hide();
                            $('#visareceived').show();
                            $('#NewCandStatusUpdateLabel').text("Selected");
                            if (data.visa_received == 1) {
                                $('#visa_received').attr('checked',true).change();
                            } else {
                                $('#visa_received_not').attr('checked',true).change();
                            } 
                        }
                        table.ajax.reload();
                    }
                });
            });
            // Update Visa Received Status
            $(document).on('submit','#visareceived',function(e){
                e.preventDefault();
                var newradiovalue = $('input[name="visa_received"]:checked').val();
                var candID = $('#candIDN').val();
                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/visareceived") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'visa_received': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if (data.visa_received == 1 && data.passport_in_embassy == 0) {
                            $('#visareceived').hide();
                            $('#passportinembassy').show();
                            $('#NewCandStatusUpdateLabel').text("Visa Received");
                            if (data.passport_in_embassy == 1) {
                                $('#passport_in_embassy').attr('checked',true).change();
                            } else {
                                $('#passport_in_embassy_not').attr('checked',true).change();
                            } 
                        }
                        table.ajax.reload();
                    }
                });
            });
            // Update Passport in Embassy Status
            $(document).on('submit','#passportinembassy',function(e){
                e.preventDefault();
                var newradiovalue = $('input[name="passport_in_embassy"]:checked').val();
                var candID = $('#candIDN').val();
                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/passportinembassy") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'passport_in_embassy': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if (data.passport_in_embassy == 1 && data.visa_stamped == 0) {
                            $('#passportinembassy').hide();
                            $('#visastamped').show();
                            $('#NewCandStatusUpdateLabel').text("Passport In Embassy");
                            if (data.visa_stamped == 1) {
                                $('#visa_stamped').attr('checked',true).change();
                            } else {
                                $('#visa_stamped_not').attr('checked',true).change();
                            } 
                        }
                        table.ajax.reload();
                    }
                });
            });
            // Update Visa Stamp Status
            $(document).on('submit','#visastamped',function(e){
                e.preventDefault();
                var newradiovalue = $('input[name="visa_stamped"]:checked').val();
                var candID = $('#candIDN').val();
                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/visastamped") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'visa_stamped': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if (data.visa_stamped == 1 && data.applied_for_emigration == 0) {
                            $('#visastamped').hide();
                            $('#appliedforemigration').show();
                            $('#NewCandStatusUpdateLabel').text("Visa Stamped");
                            if (data.applied_for_emigration == 1) {
                                $('#applied_for_emigration').attr('checked',true).change();
                            } else {
                                $('#applied_for_emigration_not').attr('checked',true).change();
                            } 
                        }
                        table.ajax.reload();
                    }
                });
            });
            // Update Applied For Emigration Status
            $(document).on('submit','#appliedforemigration',function(e){
                e.preventDefault();
                var newradiovalue = $('input[name="applied_for_emigration"]:checked').val();
                var candID = $('#candIDN').val();
                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/appliedforemigration") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'applied_for_emigration': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if (data.applied_for_emigration == 1 && data.emigration_approved == 0) {
                            $('#appliedforemigration').hide();
                            $('#emigrationapproved').show();
                            $('#NewCandStatusUpdateLabel').text("Applied For Emigration");
                            if (data.emigration_approved == 1) {
                                $('#emigration_approved').attr('checked',true).change();
                            } else {
                                $('#emigration_approved_not').attr('checked',true).change();
                            } 
                        }
                        table.ajax.reload();
                    }
                });
            });
            // Update Emigration Approved Status
            $(document).on('submit','#emigrationapproved',function(e){
                e.preventDefault();
                var newradiovalue = $('input[name="emigration_approved"]:checked').val();
                var candID = $('#candIDN').val();
                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/emigrationapproved") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'emigration_approved': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if (data.emigration_approved == 1 && data.waiting_for_flight_ticket == 0) {
                            $('#emigrationapproved').hide();
                            $('#waitingforflightticket').show();
                            $('#NewCandStatusUpdateLabel').text("Emigration Approved");
                            if (data.waiting_for_flight_ticket == 1) {
                                $('#waiting_for_flight_ticket').attr('checked',true).change();
                            } else {
                                $('#waiting_for_flight_ticket_not').attr('checked',true).change();
                            } 
                        }
                        table.ajax.reload();
                    }
                });
            });
            // Update Waiting For Flight Ticket Status
            $(document).on('submit','#waitingforflightticket',function(e){
                e.preventDefault();
                var newradiovalue = $('input[name="waiting_for_flight_ticket"]:checked').val();
                var candID = $('#candIDN').val();
                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/waitingforticket") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'waiting_for_flight_ticket': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if (data.waiting_for_flight_ticket == 1 && data.ticket_confirmed == 0) {
                            $('#waitingforflightticket').hide();
                            $('#ticketconfirmed').show();
                            $('#NewCandStatusUpdateLabel').text("Waiting For Flight Ticket");
                            if (data.ticket_confirmed == 1) {
                                $('#ticket_confirmed').attr('checked',true).change();
                            } else {
                                $('#ticket_confirmed_not').attr('checked',true).change();
                            } 
                        }
                        table.ajax.reload();
                    }
                });
            });
            // Update Ticket Confirmed Status
            $(document).on('submit','#ticketconfirmed',function(e){
                e.preventDefault();
                var newradiovalue = $('input[name="ticket_confirmed"]:checked').val();
                var candID = $('#candIDN').val();
                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/ticketconfirmed") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'ticket_confirmed': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if (data.ticket_confirmed == 1 && data.deployed == 0) {
                            $('#ticketconfirmed').hide();
                            $('#deployedStatus').show();
                            $('#NewCandStatusUpdateLabel').text("Ticket Confirmed");
                            if (data.deployed == 1) {
                                $('#deployed').attr('checked',true).change();
                            } else {
                                $('#deployed_not').attr('checked',true).change();
                            } 
                        }
                        table.ajax.reload();
                    }
                });
            });
            // Update Deployed Status
            $(document).on('submit','#deployedStatus',function(e){
                e.preventDefault();
                var newradiovalue = $('input[name="deployed"]:checked').val();
                var candID = $('#candIDN').val();
                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/deployed") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'deployed': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        // if (data.deployed == 1 && data.cancelled == 0) {
                        //     $('#deployedStatus').hide();
                        //     $('#cancelledStatus').show();
                        //     $('#NewCandStatusUpdateLabel').text("Visa Received");
                        //     if (data.cancelled == 1) {
                        //         $('#cancelled').attr('checked',true).change();
                        //     } else {
                        //         $('#cancelled_not').attr('checked',true).change();
                        //     } 
                        // }
                        $('#deployedStatus').hide();
                        $('#NewCandStatusUpdate').modal('hide');
                        table.ajax.reload();
                    }
                });
            });
            // Update Cancelled Status
            $(document).on('submit','#cancelledStatus',function(e){
                e.preventDefault();
                var newradiovalue = $('input[name="cancelled"]:checked').val();
                var candID = $('#candIDN').val();
                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/cancelled") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'cancelled': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        // if (data.cancelled == 1 && data.hold == 0) {
                        //     $('#cancelledStatus').hide();
                        //     $('#holdStatus').show();
                        //     $('#NewCandStatusUpdateLabel').text("Visa Received");
                        //     if (data.hold == 1) {
                        //         $('#hold').attr('checked',true).change();
                        //     } else {
                        //         $('#hold_not').attr('checked',true).change();
                        //     } 
                        // }
                        $('#cancelledStatus').hide();
                        $('#NewCandStatusUpdate').modal('hide');
                        table.ajax.reload();
                    }
                });
            });
            // Update Hold Status
            $(document).on('submit','#holdStatus',function(e){
                e.preventDefault();
                var newradiovalue = $('input[name="hold"]:checked').val();
                var candID = $('#candIDN').val();
                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/hold") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'hold': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){

                        $('#holdStatus').hide();
                        $('#NewCandStatusUpdate').modal('hide');
                        table.ajax.reload();
                    }
                });
            });
            // Update Visa Cancelled Status
            $(document).on('submit','#visacencelled',function(e){
                e.preventDefault();
                var newradiovalue = $('input[name="visa_cencelled"]:checked').val();
                var candID = $('#candIDN').val();
                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/visacancelled") }}',
                    method: "POST",
                    type: "html",
                    data:{
                        'cand_id': candID,
                        'visa_cencelled': newradiovalue,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        
                        $('#visacencelled').hide();
                            // $('#NewCandStatusUpdateLabel').text("Visa Received");
                        $('#NewCandStatusUpdate').modal('hide');
                        
                        table.ajax.reload();
                    }
                });
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
                    url : '{{ url("admin/candidate/edit") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#edit_ID').val(data.id);
                        $('#edit-cand-name').val(data.cand_name);
                        $('#edit-cand-arname').val(data.arcand_name);
                        $('#edit-pass-no').val(data.pass_no);
                        $('#edit-pass-type').val(data.pass_type).change();
                        $('#edit-dob').val(data.dob);
                        $('#edit-doi').val(data.doi);
                        $('#edit-doe').val(data.doe);
                        $('#edit-place-of-issue').val(data.poi).change();
                        // $('#edit-place-of-birth').val(data.plb_id).change();
                        // $('#edit-place-of-issue').val(data.poi_text).change();
                        $('#edit-place-of-birth').val(data.plb_text).change();
                        $('#edit-nationality-id').val(data.nation_id).change();
                        $('#edit-contact-number').val(data.contact_no);
                        $('#edit-marital-status').val(data.marital_status).change();
                        $('#edit-mob-number').val(data.mobile_no);
                        $('#edit-occupation').val(data.jobtype_id).change();
                        $('#edit-address').val(data.address);
                        $('#edit-care-off').val(data.careoff_id).change();
                        $('#edit-associate-id').val(data.associate_id).change();
                    }
                });
            });
        });
    </script>



    <script>
        $(document).ready(function(){

            $('#pass-typef').click(function(){
                $('.pass-type-div').toggle();
            });

            $('#job-typef').click(function(){
                $('.job-type-div').toggle();
            });

            $('#create-byf').click(function(){
                $('.create-by-div').toggle();
            });

            $('#careofff').click(function(){
                $('.careoff-div').toggle();
            });

            $('#religionf').click(function(){
                $('.religion-div').toggle();
            });

            $('#cityf').click(function(){
                $('.city-div').toggle();
            });

            $('#regionf').click(function(){
                $('.region-div').toggle();
            });

            $('#experience-regionf').click(function(){
                $('.experience-region-div').toggle();
            });

            $('#publish-statusf').click(function(){
                $('.publish-status-div').toggle();
            });

            $('#candidate-statusf').click(function(){
                $('.cand-status-div').toggle();
            });

            $('#create-datef').click(function(){
                $('.created-date-div').toggle();
            });

            $('#medical-expiry-datef').click(function(){
                $('.medical-expiry-div').toggle();
            });

            $('#new-candidate-statusf').click(function(){
                $('.new-candidate-div').toggle();
            });

            $('#ready-for-published-statusf').click(function(){
                $('.ready-for-publish-div').toggle();
            });

            $('#cand-medical-statusf').click(function(){
                $('.cand-medical-status-div').toggle();
            });

            $('#cand-payment-statusf').click(function(){
                $('.cand-payment-status-div').toggle();
            });

            // Basic Select
            $('#default-chk').click(function(){
                if ($('#new-candidate-statusf:checkbox:checked').length > 0) {
                    $('#new-candidate-statusf').trigger('click');
                }

                if ($('#ready-for-published-statusf:checkbox:checked').length > 0) {
                    $('#ready-for-published-statusf').trigger('click');
                }

                if ($('#cand-medical-statusf:checkbox:checked').length > 0) {
                    $('#cand-medical-statusf').trigger('click');
                }

                if ($('#cand-payment-statusf:checkbox:checked').length > 0) {
                    $('#cand-payment-statusf').trigger('click');
                }

                if ($('#medical-expiry-datef:checkbox:checked').length > 0) {
                    $('#medical-expiry-datef').trigger('click');
                }

                if ($('#create-datef:checkbox:checked').length > 0) {
                    $('#create-datef').trigger('click');
                }

                if ($('#candidate-statusf:checkbox:checked').length > 0) {
                    $('#candidate-statusf').trigger('click');
                }

                if ($('#publish-statusf:checkbox:checked').length > 0) {
                    $('#publish-statusf').trigger('click');
                }

                if ($('#cityf:checkbox:checked').length > 0) {
                    $('#cityf').trigger('click');
                }

                if ($('#religionf:checkbox:checked').length > 0) {
                    $('#religionf').trigger('click');
                }

                if ($('#create-byf:checkbox:checked').length > 0) {
                    $('#create-byf').trigger('click');
                }

                if ($('#careofff:checkbox:checked').length > 0) {
                    $('#careofff').trigger('click');
                }


                if($('#pass-typef:checkbox:checked').length > 0){

                }else{
                    $('#pass-typef').trigger('click');
                }

                if($('#job-typef:checkbox:checked').length > 0){

                }else{
                    $('#job-typef').trigger('click');
                }

                if($('#regionf:checkbox:checked').length > 0){

                }else{
                    $('#regionf').trigger('click');
                }
                if($('#experience-regionf:checkbox:checked').length > 0){

                }else{
                    $('#experience-regionf').trigger('click');
                }
            });

            // All Select
            $('#all-chk').click(function(){
                $('input[name="pass-typef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="job-typef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="create-byf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="careofff"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="religionf"]').each(function () {
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

                $('input[name="regionf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="experience-regionf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="publish-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="candidate-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="create-datef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="medical-expiry-datef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="new-candidate-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="ready-for-published-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="cand-medical-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="cand-payment-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });
            });

            // Uncheck All
            $('#all-unchk').click(function(){
                if($('#pass-typef:checkbox:checked').length > 0){
                    $('#pass-typef').trigger('click');
                }
                if($('#job-typef:checkbox:checked').length > 0){
                    $('#job-typef').trigger('click');
                }
                if($('#create-byf:checkbox:checked').length > 0){
                    $('#create-byf').trigger('click');
                }
                if($('#careofff:checkbox:checked').length > 0){
                    $('#careofff').trigger('click');
                }
                if($('#religionf:checkbox:checked').length > 0){
                    $('#religionf').trigger('click');
                }
                if($('#cityf:checkbox:checked').length > 0){
                    $('#cityf').trigger('click');
                }
                if($('#regionf:checkbox:checked').length > 0){
                    $('#regionf').trigger('click');
                }
                if($('#experience-regionf:checkbox:checked').length > 0){
                    $('#experience-regionf').trigger('click');
                }
                if($('#publish-statusf:checkbox:checked').length > 0){
                    $('#publish-statusf').trigger('click');
                }
                if($('#candidate-statusf:checkbox:checked').length > 0){
                    $('#candidate-statusf').trigger('click');
                }
                if($('#create-datef:checkbox:checked').length > 0){
                    $('#create-datef').trigger('click');
                }
                if($('#medical-expiry-datef:checkbox:checked').length > 0){
                    $('#medical-expiry-datef').trigger('click');
                }
                if($('#new-candidate-statusf:checkbox:checked').length > 0){
                    $('#new-candidate-statusf').trigger('click');
                }
                if($('#ready-for-published-statusf:checkbox:checked').length > 0){
                    $('#ready-for-published-statusf').trigger('click');
                }
                if($('#cand-medical-statusf:checkbox:checked').length > 0){
                    $('#cand-medical-statusf').trigger('click');
                }
                if($('#cand-payment-statusf:checkbox:checked').length > 0){
                    $('#cand-payment-statusf').trigger('click');
                }

            });

            // Update Checkbox
            $('#update-chk').click(function(){
                var pass_typef = $('#pass-typef:checked').val();
                var job_typef = $('#job-typef:checked').val();
                var careofff = $('#careofff:checked').val();
                var create_byf = $('#create-byf:checked').val();
                var religionf = $('#religionf:checked').val();
                var cityf = $('#cityf:checked').val();
                var regionf = $('#regionf:checked').val();
                var experience_regionf = $('#experience-regionf:checked').val();
                var publish_statusf = $('#publish-statusf:checked').val();
                var candidate_statusf = $('#candidate-statusf:checked').val();
                var create_datef = $('#create-datef:checked').val();
                var medical_expiry_datef = $('#medical-expiry-datef:checked').val();
                var new_candidate_statusf = $('#new-candidate-statusf:checked').val();
                var ready_for_published_statusf = $('#ready-for-published-statusf:checked').val();
                var cand_medical_statusf = $('#cand-medical-statusf:checked').val();
                var cand_payment_statusf = $('#cand-payment-statusf:checked').val();

                jQuery.ajax({
                    url:"{{ url('admin/candidate/filterList/update') }}",
                    method: 'post',
                    type: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        pass_typef: pass_typef,
                        job_typef: job_typef,
                        careofff: careofff,
                        create_byf: create_byf,
                        religionf: religionf,
                        cityf: cityf ,
                        regionf: regionf,
                        experience_regionf: experience_regionf ,
                        publish_statusf : publish_statusf ,
                        candidate_statusf : candidate_statusf ,
                        create_datef : create_datef ,
                        medical_expiry_datef : medical_expiry_datef ,
                        new_candidate_statusf : new_candidate_statusf ,
                        ready_for_published_statusf : ready_for_published_statusf , 
                        cand_medical_statusf : cand_medical_statusf , 
                        cand_payment_statusf: cand_payment_statusf
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

    <script>
        $(document).ready(function(){
        $('#deleteStaff').on('show.bs.modal',function(e){
            var staff_id =  $(e.relatedTarget).data('id');
            // alert(staff_id);
            $('#delStaffID').val(staff_id);
            // check already exist in any table or nor
            $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
            });

            jQuery.ajax({
                url : '{{ url('admin/candidate/check/exist') }}',
                method: "POST",
                type: "html",
                data: {
                    "id": staff_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data){
                    if (data) {
                    $('#disbtn').prop('disabled',true);
                    } else {
                    $('#disbtn').prop('disabled',false);
                    }
                }
            });
        });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('.bulkpublish').on('click',function(e){
                var allVals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select atleast one checkbox");
                    location.reload();
                }else{
                    var count_id = allVals.length;
                    var join_selected_values = allVals.join(",");

                    $('#idsv').val(join_selected_values);
                    $('#countcan').text(count_id);

                }
            });
        });
    </script>

    <script>
        $(document).ready(function(){

            $('#sharedCV').on('show.bs.modal',function(e){
                var allselectedvals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });

                if (allselectedvals <= 0) {
                    alert("Please select atleast one checkbox");
                    location.reload();
                } else {
                    var countc_id = allselectedvals.length;
                    var join_all_selected_values = allselectedvals.join(",");

                    $('#candcvs_id').val(join_all_selected_values);
                }

                

            });

            // $('.bulksharedcv').on('click',function(e){
            //     var allselectedvals = [];
            //     $('.dt-checkboxes:checked').each(function(){
            //         allselectedvals.push($(this).attr('data-id'));
            //     });

            //     if (allselectedvals <= 0) {
            //         alert("Please select atleast one checkbox");
            //         location.reload();
            //     } else {
            //         var countc_id = allselectedvals.length;
            //         var join_all_selected_values = allselectedvals.join(",");

            //         $('#candcvs_id').val(join_all_selected_values);
            //     }

            // });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('.bulkdownloadcv').on('click',function(e){
                var allselectedvals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });

                if (allselectedvals <= 0) {
                    alert("Please select atleast one checkbox");
                    location.reload();
                } else {
                    var countc_id = allselectedvals.length;
                    var join_all_selected_values = allselectedvals.join(",");

                    $('#candcv_id').val(join_all_selected_values);
                }

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#downloadCvbtn').on('click',function(){
                var partnerID = $('#partner_id').val();
                var candCVID = $('#candcv_id').val();

                const candIDs = candCVID.split(",");
                if (partnerID != '') {
                    for (let i = 0; i < candIDs.length; i++) {
                        // const element = array[i];
                        var cvUrl = '{{ url("admin/download/candidate-cv") }}/'+partnerID+'/'+candIDs[i];
                        window.open(cvUrl,'_blank');
                    }           
                    
                    $('#errorPartner').text('');
                    $('#downloadCvbtn').attr('disabled',false);
                } else {
                    $('#errorPartner').text('Please select office name!');
                    $('#downloadCvbtn').attr('disabled',true);
                }



                // var cvUrl = '{{ url("admin/singlecv/download") }}/'+partnerID
                // window.open(cvUrl,'_blank');

            });

            // $('#partner_id').on('change',function(){
            //     var pID = $(this).val();
            //     if (pID != '') {
            //         $('#errorPartner').text('');
            //         $('#downloadCvbtn').attr('disabled',false);
            //     } else {
            //         $('#errorPartner').text('Please select office name!');
            //         $('#downloadCvbtn').attr('disabled',true);
            //     }
            // }); 15072023
        });
    </script>

@endsection