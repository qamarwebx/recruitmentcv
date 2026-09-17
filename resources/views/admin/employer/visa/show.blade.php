@extends('layout.admin.admin_layout')

@section('title','Visa Details')

@section('page-style')
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
@endsection


@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row mb-2">
            <div class="col-md-12">
                {{-- <a href="{{ route('admin.employer') }}" class="float-end btn btn-sm btn-primary">Back</a> --}}
                <button class="btn btn-sm btn-primary float-end" id="closedButton">Close</button>
            </div>
        </div>
        <div class="row" id="mainContainDiv">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                    <div>
                                        @if ($post->uavatar_url != '')
                                            <img src="{{ $post->uavatar_url }}" alt="Avatar" class="d-block w-px-100 h-px-100 rounded">
                                        @elseif($post->uphoto != '')
                                            <img src="{{ asset('user/img/avatars/'.$post->uphoto) }}" alt="Avatar" class="d-block w-px-100 h-px-100 rounded">
                                        @else
                                            <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="Avatar" class="d-block w-px-100 h-px-100 rounded">
                                        @endif
                                    </div>
                                    <div>
                                        {{-- <h5 style="margin-top: -10px;">@if($post->employer_ar_name != '') {{ $post->employer_ar_name }} @else {{ $post->employer_name }} @endif </h5> --}}
                                        <h5 style="margin-top: -10px;">@if($post2->partneroffice_id != '') {{ $post2->partneroffice->rec_off_name }} @else {{ '---' }} @endif </h5>
                                        <h6 style="margin-top: -15px;"><i class="tf-icons ti ti-phone-call ti-xs me-1"></i> @if($post->mobile_no != '') {{ $post->mobile_no }} @else {{ '---' }} @endif</h6>
                                        @foreach ($expworkcities as $expworkcity)
                                            @if($post->wpcity_id == $expworkcity->id)
                                                <h6 style="margin-top: -15px;"><i class="menu-icon tf-icons ti ti-smart-home"></i> {{ $expworkcity->name . ' (' . $expworkcity->arname . ')' }}
                                                </h6>
                                            @endif
                                        @endforeach
                                        <h6 style="margin-top: -15px;"><i class="menu-icon tf-icons ti ti-user-plus"></i> Created By: @if($post->admin_id != '') {{ $post->createBy }} @endif</h6>
                                        <h6 style="margin-top: -15px;"><i class="menu-icon tf-icons ti ti-user-plus"></i> Care Off: @if($post->careoff_id != '') {{ $post->careoffby }} @endif</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Employer Name:</span>
                                    <span class="copied_text">@if($post->employer_name != '') {{ $post->employer_name }} @else {{ '---' }} @endif</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Employer Arabic Name:</span>
                                    <span class="copied_text">@if($post->employer_ar_name != '') {{ $post->employer_ar_name }} @else {{ '---' }} @endif</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">ID No:</span>
                                    <span class="copied_text">@if($post->id_no != '') {{ $post->id_no }} @else {{ '---' }} @endif</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Visa No:</span>
                                    <span class="copied_text">@if($post->visa_no != '') {{ $post->visa_no }} @else {{ '---' }} @endif</span>
                                </div>
                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Visa Date:</span>
                                    <span class="copied_text">@if($post->visa_date != '') {{ $post->visa_date }} @else {{ '---' }} @endif</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Visa Issue Authority:</span>
                                    <span class="copied_text">@if($post->issuing_authority != '') {{ $post->issuing_authority }} @else {{ '---' }} @endif</span>
                                </div>


                            </div>

                            <div class="col-md-4">
                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Opening:</span>
                                    <span class="copied_text">{{ implode(",",$visaBalanceData['visaProfessionQty']) }}</span>
                                </div>
                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Total Visa Quantity:</span>
                                    <span class="copied_text">{{ $visaBalanceData['visa_quantity'] }}</span>
                                </div>
                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Balance:</span>
                                    <span class="copied_text">{{ $visaBalanceData['balance_visa'] }}</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Created By:</span>
                                    <span class="copied_text">@if($post->admin_id != '') {{ $post->createBy }} @else {{ '---' }} @endif</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Careoff:</span>
                                    <span class="copied_text">@if($post->careoff_id != '') {{ $post->careoffby }} @else {{ '---' }} @endif</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">City of Work:</span>
                                    <span class="copied_text">@if($post->expworkname != '') {{ $post->expworkname }} @else {{ '---' }} @endif</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">

                                <button class="btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-id="{{ $post->id }}" data-bs-target="#editEmployerVisa"><i class="ti ti-edit"></i> Edit</button>
                                {{-- <button class="btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editVisa"><i class="ti ti-edit"></i> Edit</button> --}}
                                {{-- <button class="btn btn-sm btn-success" id="add_btn_candidate" data-bs-toggle="modal" data-bs-target="#addCandidate"><i class="ti ti-plus"></i>  @if($post->cand_id != '') Update Candidate @else Add Candidate @endif</button> --}}
                                <button class="btn btn-sm btn-success" id="add_assign_candidate" data-bs-toggle="modal" data-bs-target="#addAssignCandidate" @if($visaBalanceData['balance_visa'] == 0) disabled @endif data-bs-target="#addassigncand"><i class="ti ti-plus"></i> Assign Candidate</button>
                                <button class="btn btn-sm btn-primary" id="generate-work-agreement" data-bs-toggle="modal" data-bs-target="#generateworkagreement"><i class="ti ti-file-text"></i> Generate Work Agreement</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <ul class="nav nav-tabs nav-fill" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-home" aria-controls="navs-justified-home" aria-selected="true">
                                <i class="tf-icons ti ti-home ti-xs me-1"></i> Visa Details
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-payment-tab" aria-controls="navs-payment-tab" aria-selected="true">
                                    <i class="tf-icons ti ti-currency-rupee ti-xs me-1"></i> Payment
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-candidate-tab" aria-controls="nav-candidate-tab" aria-selected="false">
                                    <i class="tf-icons ti ti-user-check ti-xs me-1"></i> Candidate
                                </button>
                            </li>
                            {{-- <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-candidate-file-tab" aria-controls="nav-candidate-file-tab" aria-selected="false">
                                    <i class="tf-icons ti ti-file ti-xs me-1"></i> Candidate FIle
                                </button>
                            </li> --}}


                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-cand-payment-tab" aria-controls="nav-cand-payment-tab" aria-selected="false">
                                    <i class="tf-icons ti ti-cash ti-xs me-1"></i> Order
                                </button>
                            </li>


                            <li class="nav-item" role="presentation">
                              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-actvities" aria-controls="navs-justified-actvities" aria-selected="true">
                              <i class="tf-icons ti ti-timeline ti-xs me-1"></i> Activities
                              </button>
                           </li>

                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-0">
                            <div class="tab-pane fade show active" id="navs-justified-home" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Employer</th>
                                                        <th>Employer Arabic Name</th>
                                                        <th>ID No</th>
                                                        <th>Visa No</th>
                                                        <th>Visa Issue</th>
                                                        <th>City of Work</th>
                                                        <th>Visa Profession</th>
                                                        <th>Monthly Salary</th>
                                                        <th>Notes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>@if($post->employer_name != '') {{ $post->employer_name }} @else {{ '---' }} @endif</td>
                                                        <td>@if($post->employer_ar_name != '') {{ $post->employer_ar_name }} @else {{ '---' }} @endif</td>
                                                        <td>@if($post->id_no != '') {{ $post->id_no }} @else {{ '---' }} @endif</td>
                                                        <td>@if($post->visa_no != '') {{ $post->visa_no }} @else {{ '---' }} @endif</td>
                                                        <td>@if($post->issuing_authority != '') {{ $post->issuing_authority }} @else {{ '---' }} @endif</td>
                                                        <td>@if($post->expworkname != '') {{ $post->expworkname }} @else {{ '---' }} @endif</td>
                                                        {{-- <td>@if($post->pengname != '') {{ $post->pengname }}  @else {{ '---' }} @endif</td> --}}
                                                        <td>{{ implode(",",$visaProfession) }}</td>
                                                        <td>@if($post->salary != '') {{ $post->salary }} @else {{ '---' }} @endif</td>
                                                        <td>@if($post->notes != '') {{ $post->notes }} @else {{ '---' }} @endif</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="tab-pane fade" id="nav-candidate-file-tab" role="tabpanel">
                                <div class="row">
                                    @if ($post->photo_file != '')
                                    <div class="col-md-2">
                                        <span class="badge bg-label-secondary">Photo</span>
                                        <div class="mt-2">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" target="_blank">
                                                <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="{{ $post->photo_file }}" class="d-block w-px-100 h-px-100 rounded" />

                                            </a>
                                        </div>
                                        <div class="mt-1">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>

                                        </div>
                                    </div>
                                @endif
                                @if ($post->pass_file != '')
                                    <div class="col-md-2">
                                        <span class="badge bg-label-secondary">Passport Front Copy</span>
                                        <div class="mt-2">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" target="_blank">
                                            <img src="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" alt="{{ $post->pass_file }}" class="d-block w-px-100 h-px-100 rounded" />

                                            </a>
                                        </div>
                                        <div class="mt-1">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>

                                        </div>
                                    </div>
                                @endif
                                @if ($post->pass_back_file != '')
                                    <div class="col-md-2">
                                        <span class="badge bg-label-secondary">Passport Back Copy</span>
                                        <div class="mt-2">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_back_file) }}" target="_blank">
                                                <img src="{{ asset('admin/assets/images/candidate/'.$post->pass_back_file) }}" alt="{{ $post->pass_back_file }}" class="d-block w-px-100 h-px-100 rounded" />

                                            </a>
                                        </div>
                                        <div class="mt-1">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_back_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_back_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>

                                        </div>
                                    </div>
                                @endif
                                @if ($post->lic_file != '')
                                    <div class="col-md-2">
                                        <span class="badge bg-label-secondary">License Copy</span>
                                        <div class="mt-2">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" target="_blank">
                                                <img src="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" alt="{{ $post->lic_file }}" class="d-block w-px-100 h-px-100 rounded" />

                                            </a>
                                        </div>
                                        <div class="mt-1">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>

                                        </div>
                                    </div>
                                @endif
                                @if ($post->cv_file != '')
                                    <div class="col-md-2">
                                    <span class="badge bg-label-secondary">Full Size Image</span>
                                    <div class="mt-2">
                                        <a href="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" target="_blank">
                                            <img src="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" alt="{{ $post->cv_file }}" class="d-block w-px-100 h-px-100 rounded" />

                                        </a>
                                    </div>
                                    <div class="mt-1">
                                        <a href="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                        <a href="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>

                                    </div>
                                    </div>
                                @endif
                                </div>
                            </div> --}}
                            <div class="tab-pane fade show" id="navs-payment-tab" role="tabpanel">

                            </div>
                            <div class="tab-pane fade" id="nav-candidate-tab" role="tabpanel">
                                <div class="row nav-candidate-tab" >
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Candidate</th>
                                                        <th>Passport No</th>
                                                        <th>DOB</th>
                                                        <th>Experience</th>
                                                        <th>Profession</th>

                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="candidate_details">
                                                    @if ($candempLists->count() > 0)
                                                        @foreach ($candempLists as $candempList)
                                                            <tr>
                                                                <td><a href="{{ url('admin/candidate/view/'.$candempList->cand_id) }}" class="text-body">{{ $candempList->candname }}</a></td>
                                                                <td>{{ $candempList->candpassno }}</td>
                                                                <td>{{ date('d-m-Y',strtotime($candempList->candob)) }}</td>
                                                                <td> {{ $candempList->canoverall_exp.' Years' }} </td>
                                                                <td>{{ $candempList->profffengname }}</td>
                                                                <td id="delete_td">
                                                                    {{-- <a href="javascript:;" class="text-body delemployer" data-bs-toggle="modal" data-bs-target="#deleteStaff" data-id="{{$post->id}}"><i class="ti ti-trash ti-sm mx-2"></i></a> --}}
                                                                    <a href="javascript:void(0);" class="text-body" data-bs-toggle="modal" data-bs-target="#deasignCand" data-id="{{ $candempList->id }}"><i class="ti ti-switch ti-sm"></i> Deasign</a>
                                                                    <a href="{{ url('admin/candidate/view/'.$candempList->cand_id) }}" target="_blank" class="text-body"><i class="ti ti-eye ti-sm"></i></a>

                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        {{-- @elseif ($post->cand_id != '')
                                                        <tr>
                                                            <td>{{ $post->cand_name }}</td>
                                                            <td>{{ $post->pass_no }} </td>
                                                            <td>{{ date('d-m-Y',strtotime($post->candob)) }}</td>
                                                            <td>{{ $post->overall_exp.' Years' }}</td>
                                                            <td>{{ $post->candprofession }}</td>
                                                            @if($post->cand_id != '')

                                                                <td id="delete_td">
                                                                    <a href="javascript:;" class="text-body delemployer" data-bs-toggle="modal" data-bs-target="#deleteStaff" data-id="{{$post->id}}"><i class="ti ti-trash ti-sm mx-2"></i></a>
                                                                    <a href="{{ url('admin/candidate/view/'.$post->cand_id) }}" target="_blank" class="text-body"><i class="ti ti-eye ti-sm"></i></a>
                                                                </td>
                                                            @endif
                                                        </tr> --}}
                                                    @endif

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-cand-payment-tab" role="tabpanel"></div>
                            <div class="tab-pane fade" id="navs-justified-actvities" role="tabpanel">
                                 <div class="nav-align-left mb-4">
                     <ul class="nav nav-pills me-3" role="tablist">
                        <li class="nav-item">
                           <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-personal" aria-controls="navs-justified-personal" aria-selected="false">
                           <i class="tf-icons ti ti-user-check ti-xs me-1"></i> Personal
                           </button>
                        </li>
                        <li class="nav-item">
                           <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-experience" aria-controls="navs-justified-experience" aria-selected="false">
                           <i class="tf-icons ti ti-files ti-xs me-1"></i> Experience & Skills
                           </button>
                        </li>
                        <li class="nav-item">
                           <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-profile" aria-controls="navs-justified-profile" aria-selected="false">
                           <i class="tf-icons ti ti-file-check ti-xs me-1"></i> Passport
                           </button>
                        </li>
                        <li class="nav-item">
                           <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-musaned" aria-controls="navs-justified-musaned" aria-selected="false">
                           <i class="tf-icons ti ti-files ti-xs me-1"></i> Musaned
                           </button>
                        </li>
                        <li class="nav-item">
                           <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-medical" aria-controls="navs-justified-medical" aria-selected="false">
                           <i class="tf-icons ti ti-files ti-xs me-1"></i> Medical
                           </button>
                        </li>
                        <li class="nav-item">
                           <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-justified-mofa-no" aria-controls="nav-justified-mofa-no" aria-selected="false">
                              <i class="tf-icons ti ti-files ti-xs me-1"></i> Mofa No
                           </button>
                        </li>

                        <li class="nav-item">
                           <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-justified-flight" aria-controls="nav-justified-flight" aria-selected="false">
                              <i class="tf-icons ti ti-files ti-xs me-1"></i> Flight
                           </button>
                        </li>

                     </ul>
                     <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-justified-personal" role="tabpanel">
                           <div class="row" id="nav-personal-div">
                              <div class="col-md-12">
                                 <div class="info-container">
                                    <ul class="list-unstyled">
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Candidate Name:</span>
                                          <span>{{ $post->cand_name }}</span>
                                       </li>





                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">City:</span>

                                          <span></span>
                                       </li>

                                    </ul>
                                 </div>
                                 <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editPersonal">Add / Edit</button>
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="navs-justified-experience" role="tabpanel">
                           <div class="row" id="nav-experience-div">
                              <div class="col-md-12">
                                 <div class="info-container">
                                    <ul class="list-unstyled">
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Experience Region:</span>

                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Google Map:</span>
                                          <span></span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Car known:</span>
                                          <span></span>
                                       </li>
                                       <li>
                                          <span class="fw-semibold me-1">Vehical Transmission:</span>
                                          <span>

                                          </span>

                                       </li>

                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Experience (in year):</span>
                                          <span></span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Job Type:</span>
                                          <span></span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Experience (country name):</span>
                                          <span></span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">City:</span>
                                          <span></span>

                                       </li>


                                    </ul>
                                 </div>
                                 <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editExperience">Add/ Edit</button>
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="navs-justified-profile" role="tabpanel">
                           <div class="row" id="nav-passport-div">
                              <div class="col-md-12">
                                 <div class="info-container">
                                    <ul class="list-unstyled">
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Passport No:</span>
                                          <span></span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Passport Type:</span>
                                          <span></span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Place of Issue:</span>
                                          <span></span>

                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Date of Issue:</span>
                                          <span></span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Date of Expiry:</span>
                                          <span></span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Place of Birth:</span>

                                          <span></span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">DOB:</span>
                                          <span></span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Nationality:</span>
                                          <span></span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Address:</span>
                                          <span></span>
                                       </li>
                                    </ul>
                                 </div>
                                 <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editPassport">Add/Edit</button>
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="navs-justified-musaned" role="tabpanel">
                           <div class="row" id="nav-musaned-status-div" >
                              <div class="col-md-12">
                                 <div class="info-container">
                                    <ul class="list-unstyled">
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Musaned Status:</span>
                                          <span>

                                          </span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Musaned Registration Date:</span>
                                          <span></span>
                                       </li>
                                    </ul>
                                 </div>
                                 <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editMusaned">Add/Edit</button>
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="navs-justified-medical" role="tabpanel">
                           <div class="row" id="nav-medical-div">

                              <div class="col-md-12">
                                 <div class="info-container">
                                    <ul class="list-unstyled">

                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Medical Expiry Date:</span>
                                          <span></span>
                                       </li>

                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Medical Expire (in Days):</span>
                                          <span></span>
                                       </li>
                                    </ul>
                                 </div>
                                 <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editMedical">Add / Edit</button>
                              </div>
                           </div>
                        </div>

                        <div class="tab-pane fade" id="nav-justified-mofa-no" role="tabpanel">
                           <div class="row" id="nav-mofa-div">

                              <div class="col-md-12">
                                 <div class="info-container">
                                    <ul class="list-unstyled">

                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Mofa No:</span>
                                          <span></span>
                                       </li>


                                    </ul>
                                 </div>
                              </div>
                              <div class="col-md-12">
                                 <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editMofaNo">Add / Edit</button>
                              </div>
                           </div>
                        </div>

                        <div class="tab-pane fade" id="nav-justified-flight" role="tabpanel">
                           <div class="row" id="nav-flight-div">

                              <div class="col-md-12">
                                 <div class="info-container">
                                    <ul class="list-unstyled">

                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Fligt Date:</span>
                                          <span></span>
                                       </li>


                                    </ul>
                                 </div>
                              </div>
                              <div class="col-md-12">
                                 <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editFlight">Add / Edit</button>
                              </div>
                           </div>
                        </div>

                        <div class="tab-pane fade" id="navs-justified-embassy" role="tabpanel">
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="info-container">
                                    <ul class="list-unstyled">
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Embassy For:</span>
                                          <span>

                                          </span>
                                       </li>
                                    </ul>
                                 </div>
                                 <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editEmbassy">Add/Edit</button>
                              </div>
                           </div>
                        </div>


                     </div>
                  </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>

        <!-- Edit Employer Canvas Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editEmployerVisa" aria-labelledby="editEmployerVisaLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editEmployerVisaLabel" class="offcanvas-title">Edit Employer</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editEmployerVisaValidation" action="{{ route('admin.employer.updateVisaDet') }}" method="POST">
                    @csrf
                    <input type="hidden" name="edit_id" id="editID">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-visa-from">Business <span class="text-danger">*</span></label>
                                <select name="businesstype" id="edit-visa-from" class="form-select select2" data-placeholder="Select Visa From...." data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="B2B Online">B2B Online</option>
                                    <option value="B2B Offline">B2B Offline</option>
                                    <option value="B2C Online">B2C Online</option>
                                    <option value="B2C Offline">B2C Offline</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-partner">Partner</label>
                                <select name="partner_office_id" id="edit-partner" class="form-select select2" data-placeholder="Select Partner...." data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($partners as $partner)
                                        <option value="{{ $partner->id }}">{{ $partner->rec_off_name.' ('.$partner->rec_office_arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-careoff-id" class="form-label">Careoff</label>
                                <select name="careoff_id" id="edit-careoff-id" class="form-select select2" data-placeholder="Select Careoff" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-employer-name">Employer Name</label>
                                <input type="text" name="employer_name" id="edit-employer-name" class="form-control" placeholder="Enter Employer Name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-employer-ar-name">Employer Name (Arabic) <span class="text-danger">*</span></label>
                                <input type="text" name="employer_ar_name" id="edit-employer-ar-name" class="form-control" placeholder="Enter Employer Name (Arabic)...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-visa-no">Visa No <span class="text-danger">*</span></label>
                                <input type="text" name="visa_no" id="edit-visa-no" class="form-control" placeholder="Enter Visa No...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-id-no">ID No <span class="text-danger">*</span></label>
                                <input type="text" name="id_no" id="edit-id-no" class="form-control" placeholder="Enter ID No...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-visa-date">Visa Date</label>
                                <input type="text" name="visa_date" id="edit-visa-date" class="form-control" placeholder="Enter Visa Date...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-visa-received-date">Visa Received Date</label>
                                <input type="text" name="visa_received_date" id="edit-visa-received-date" class="form-control date-mask2" placeholder="Enter Visa Received Date...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-wakala-status" class="form-label">Wakala Status</label>
                                <select name="wakala_status" id="edit-wakala-status" class="form-select select2" data-allow-clear="true" data-placeholder="Select Wakala Status">
                                    <option value="">Select Wakala Status</option>
                                    <option value="Wakala Completed">Wakala Completed</option>
                                    <option value="Wakala Not Completed">Wakala Not Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-12 mb-3">
                            <div class="">
                                <table class="table table-bordered table-condensed" id="dispItemAppEd">
                                    <tr>
                                        <th>Issuing Authority <span class="text-danger">*</span></th>
                                        <th>Profession <span class="text-danger">*</span></th>
                                        <th>Opening <span class="text-danger">*</span></th>
                                        <th>
                                            <button type="button" class="btn btn-sm btn-primary float-end addEditVisaDelegation mb-1">Add</button>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="mb-3">
                                                <select name="issuing_authority" id="edit-issueing-authority" class="form-select select2" data-placeholder="Select Issuing Authority..." data-allow-clear="true">
                                                    <option value=""></option>
                                                    <option value="Mumbai">Mumbai</option>
                                                    <option value="New Delhi">New Delhi</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-3">
                                                <select name="proff_id[]" id="edit-proff-id" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession...">
                                                    <option value=""></option>
                                                    @foreach ($professions as $profession)
                                                        <option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="mb-3">
                                                <input type="text" name="openings[]" id="edit-visa-openings" class="form-control" placeholder="Enter number of vacancies">
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>

                                </table>
                            </div>

                        </div>

                        {{-- <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="edit-proff-id">Profession <span class="text-danger">*</span></label>
                                <select name="proff_id[]" id="edit-proff-id" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession...">
                                    <option value=""></option>
                                    @foreach ($professions as $profession)
                                        <option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="edit-issueing-authority">Issuing Authority <span class="text-danger">*</span></label>
                                <select name="issuing_authority[]" id="edit-issueing-authority" class="form-select select2" data-placeholder="Select Visa Issuing Authority..." data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="Mumbai">Mumbai</option>
                                    <option value="New Delhi">New Delhi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-visa-openings" class="form-label">Opening <span class="text-danger">*</span></label>
                                <input type="text" name="openings[]" id="edit-visa-openings" class="form-control" placeholder="Enter number of vacancies">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-primary float-end mb-1 addEditVisaDelegation">Add Visa</button>
                        </div> --}}
                    </div>
                    <div class="dispVisaDelegationEdit"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-wpcity-id">City of Work <span class="text-danger">*</span></label>
                                <select name="wpcity_id" id="edit-wpcity-id" class="form-select select2" data-allow-clear="true" data-placeholder="Select City of Work...">
                                    <option value=""></option>
                                    @foreach ($expworkcities as $expworkcity)
                                        <option value="{{ $expworkcity->id }}">{{ $expworkcity->name.' ('.$expworkcity->arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-salary">Monthly Salary</label>
                                <input type="text" name="salary" id="edit-salary" class="form-control" placeholder="Enter Salary...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-notes">Notes</label>
                                <input type="text" name="notes" id="edit-notes" class="form-control" placeholder="Enter Notes...">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Edit Employer Canvas End -->



    <!-- Add Candidate Modal Start -->

    <div class="modal fade" id="addCandidate" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add Candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="candidateValidation_two" class="addcandidatetoemployer">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <input type="hidden" name="visaeditid" id="visaeditid" value="{{ $post->id }}">
                                <label for="add-candidate-list" class="form-lable">Select Candidate <span class="text-danger">*</span></label>
                                <select name="cand_id" id="add-candidate-list" class="form-select select2" data-placeholder="Select Candidate..." data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($candidates as $candi)
                                        <option value="{{ $candi->id }}">{{ $candi->cand_name.' ('.$candi->pass_no.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btndisabled">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Candidate Modal End -->

    <!-- Generate Work Agreement Start -->
    <div class="modal fade" id="generateworkagreement" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Generate Work Agreement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.employer.generateworkagreement',$post->id) }}" method="POST" id="generateworkagreementvalidation" class="generateworkagreementvalidation">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="add-generate-candidate-list" class="form-label">Select Candidate <span class="text-danger">*</span></label>
                                    <select name="cand_id" id="add-generate-candidate-list" class="form-select select2" data-placeholder="Select Candidate" data-allow-clear="true">
                                        <option value="">Select Candidate</option>
                                        @foreach ($employercands as $employercand)
                                            <option value="{{ $employercand->cand_id }}">{{ $employercand->cand->cand_name.' ('.$employercand->cand->pass_no.')' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-label-secondary" data-bs-toggle="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-primary btndisabled">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Generate Work Agreement End -->

    <!-- Assign Candidate Modal Start Here -->
    <div class="modal fade" id="addAssignCandidate" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Assign Candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignCandidateValidation" class="addcandidatetoemployer2">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="visaeditid" id="visaeditid2" value="{{ $post->id }}">
                            <input type="hidden" name="fromreq" value="employerplus">
                            <div class="col-md-6">
                                <div class="col mb-3">
                                    <label for="add-assign-candidate-list" class="form-label">Select Candidate <span class="text-danger">*</span></label>
                                    <select name="cand_id" id="add-assign-candidate-list" class="form-select select2" data-placeholder="Select Candidate..." data-allow-clear="true">
                                        <option value=""></option>
                                        @foreach ($candidates as $candi)
                                            <option value="{{ $candi->id }}">{{ $candi->cand_name.' ('.$candi->pass_no.')' }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add-candidate-assign-profession" class="form-label">Select Priofession <span class="text-danger">*</span></label>
                                    <select name="proff_id" id="add-candidate-assign-profession" class="form-select select2" data-placeholder="Select Profession..." data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($getAvailableProfessions as $getAvailableProfession)
                                            <option value="{{ $getAvailableProfession->id }}">{{ $getAvailableProfession->eng_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btndisabled">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Assign Candidate Modal End Here -->

    <!-- Deassign Candidate Modal Start Here -->
    <div class="modal fade" id="deasignCand" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Dessign Candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="DeassignCandidateValidation" class="Deaddcandidatetoemployer2">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="candemp_id" id="candemp_id">
                            <input type="hidden" name="fromreq" value="employerplus">

                            <div class="col-md-12">
                                <p id="deasign_text" class="text-danger"></p>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Deasign Candidate Modal End Here -->


    <div class="modal fade" id="deleteStaff" aria-hidden="true" aria-labelledby="deleteStaffLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-l">
            <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="updateLstageLabel">Remove Candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="text-danger">Are you sure you want to remove this candidate?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-sm btn-danger" id="confirmDelete">Yes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
{{-- <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script> --}}
{{-- <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script> --}}
<script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
<script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>
<script src="{{ asset('admin/assets/pages/validation/employer-visa-validation.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script>

<script>
    $(document).ready(function(){
        var select2 = $('.select2');
        if (select2.length) {
            select2.each(function () {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>');
                $this.select2({
                    dropdownParent: $this.parent()
                });
            });
        }

        $(document).ready(function(){
            $(document).on('click','.copied_text',function(){
                var value = $(this).text();
                navigator.clipboard.writeText(value);
                toastr['success']('Text copied - '+value+'', 'Success', { hideDuration: 3000 });
            });
        });
    });

</script>

<script>
    let rowMin2 = 0;
    let rowMax2 = 1000;

    // Add Visa Delegation row
    // $(document).on('click','.addEditVisaDelegation',function(){
    //     var html = '';
    //         html += '<div class="row">';
    //         html += '<div class="col-md-4"><div class="mb-3">';
    //         html += '<label class="form-label" for="add-edit-proff-id'+rowMin2+'">Profession <span class="text-danger">*</span></label><select name="proff_id[]" id="add-edit-proff-id'+rowMin2+'" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession..."><option value=""></option>@foreach ($professions as $profession)<option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>@endforeach</select></div></div>';
    //         html += '<div class="col-md-4"><div class="mb-3">';
    //         html += '<label class="form-label" for="add-edit-issueing-authority'+rowMin2+'">Issuing Authority <span class="text-danger">*</span></label><select name="issuing_authority[]" id="add-edit-issueing-authority'+rowMin2+'" class="form-select select2" data-placeholder="Select Visa Issuing Authority..." data-allow-clear="true"><option value=""></option><option value="Mumbai">Mumbai</option><option value="New Delhi">New Delhi</option></select></div></div>';
    //         html += '<div class="col-md-4"><div class="mb-3">';
    //         html += '<label for="add-edit-visa-openings'+rowMin2+'" class="form-label">Opening <span class="text-danger">*</span></label><input type="text" name="openings[]" id="add-edit-visa-openings'+rowMin2+'" class="form-control" placeholder="Enter number of vacancies"></div></div>';
    //         html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger float-end remove2">Remove</button></div>'
    //         html += '</div>'

    //     $('.dispVisaDelegationEdit').append(html);
    //     initializeSelect2();
    //     rowMin2++;
    //     rowMax2--;
    //     $('.addEditVisaDelegation').prop('disabled', rowMax2 <= 1);
    // });

    $(document).on('click','.addEditVisaDelegation',function(){
        var html = '';
            html += '<tr>';
            html += '<td></td>';
            html += '<td><div class="mb-3">';
            html += '<select name="proff_id[]" id="add-edit-proff-id'+rowMin2+'" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession..."><option value=""></option>@foreach ($professions as $profession)<option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>@endforeach</select></div></td>';
            html += '<td><div class="mb-3">';
            html += '<input type="text" name="openings[]" id="add-edit-visa-openings'+rowMin2+'" class="form-control" placeholder="Enter number of vacancies"></div></td>';
            html += '<td><button type="button" class="btn btn-sm btn-danger float-end remove2">Remove</button></td>'
            html += '</tr>';

        $('#dispItemAppEd').append(html);
        initializeSelect2();
        rowMin2++;
        rowMax2--;
        $('.addEditVisaDelegation').prop('disabled', rowMax2 <= 1);
    });

    // Initialize Select2
    function initializeSelect2() {
        $('.select2').each(function () {
            const $this = $(this);
            // $this.select2({
            $this.wrap('<div class="position-relative"></div>').select2({
                dropdownParent: $this.parent()
            });
        });
    }

    // Remove Visa Delegation row
    $(document).on('click', '.remove2', function () {

        // $(this).closest('.row').remove();
        $(this).closest('tr').remove();

        rowMin++;
        rowMin--;
        $('.addEditVisaDelegation').prop('disabled', rowMax === 0);
    });

</script>

<script>


        var dateMask2 = $('.date-mask2');


        if (dateMask2) {
            new Cleave(dateMask2, {
                date: true,
                delimiter: '-',
                datePattern: ['Y', 'm', 'd']
            });
        }

</script>

<script>
    $(document).ready(function(){
        $('#editEmployerVisa').on('show.bs.offcanvas',function(e){
            var edit_id = $(e.relatedTarget).data('id');

            $('#editID').val(edit_id);

            jQuery.ajax({
                url: "{{ route('admin.employer.editVisaDetp') }}",
                method: "get",
                type: "html",
                data: {
                    id: edit_id
                },
                // success: function(data){

                //     $('#edit-visa-from').val(data.post.businesstype).change();
                //     $('#edit-partner').val(data.post.partner_office_id).change();
                //     $('#edit-careoff-id').val(data.post.careoff_id).change();
                //     $('#edit-employer-name').val(data.post.employer_name);
                //     $('#edit-employer-ar-name').val(data.post.employer_ar_name);
                //     $('#edit-visa-no').val(data.post.visa_no);
                //     $('#edit-id-no').val(data.post.id_no);
                //     $('#edit-visa-date').val(data.post.visa_date);
                //     $('#edit-visa-received-date').val(data.post.visa_received_date);
                //     // $('#edit-proff-id').val(data.proff_id).change();
                //     // $('#edit-issueing-authority').val(data.issuing_authority).change();
                //     $('#edit-wpcity-id').val(data.post.wpcity_id).change();
                //     $('#edit-wakala-status').val(data.post.wakala_status).change();
                //     $('#edit-salary').val(data.post.salary);
                //     $('#edit-notes').val(data.post.notes);

                //     const proff_ids = data.post.proff_id.split(",");
                //     const issuing_authos = data.post.issuing_authority.split(",");
                //     if (data.post.openings != null) {
                //         var visa_openings = data.post.openings.split(",");
                //         $('#edit-visa-openings').val(visa_openings[0]);
                //     }else{
                //         $('#edit-visa-openings').val(null);
                //     }



                //     $('#edit-proff-id').val(proff_ids[0]).change();
                //     $('#edit-issueing-authority').val(issuing_authos[0]).change();


                //     // display dynamic created visa delegation work on tomorrow
                //     var dynamic_visa_delegation_var = "";

                //     for (let i = 1; i < proff_ids.length; i++) {
                //         if (issuing_authos[i] == 'Mumbai') {
                //             var mumbaiselected = "selected";
                //         } else {
                //             var mumbaiselected = "";
                //         }
                //         if (issuing_authos[i] == 'New Delhi') {
                //             var delhiselected = "selected";
                //         } else {
                //             var delhiselected = "";
                //         }

                //         if (data.post.openings != null) {
                //             var visaOpenings = visa_openings[i];
                //         } else {
                //             var visaOpenings = "";
                //         }



                //         dynamic_visa_delegation_var += '<div class="row">';
                //         dynamic_visa_delegation_var += '<div class="col-md-4"><div class="mb-3">';
                //         dynamic_visa_delegation_var += '<label class="form-label" for="edit-proff-id'+i+'">Profession <span class="text-danger">*</span></label><select name="proff_id[]" id="edit-proff-id'+i+'" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession..."><option value=""></option>';

                //         // fetch profession with selected
                //         data.professions.forEach(function(profession){
                //             if (profession.id == proff_ids[i]) {
                //                 var selectedProf = "selected";
                //             }else{
                //                 var selectedProf = "";
                //             }

                //             dynamic_visa_delegation_var += '<option value="'+profession.id+'" '+selectedProf+'>'+profession.eng_name+' ('+profession.ar_name+')</option>';

                //         });

                //         dynamic_visa_delegation_var += '</select></div></div>';
                //         dynamic_visa_delegation_var += '<div class="col-md-4"><div class="mb-3">';
                //         dynamic_visa_delegation_var += '<label class="form-label" for="edit-issueing-authority'+i+'">Issuing Authority <span class="text-danger">*</span></label><select name="issuing_authority[]" id="edit-issueing-authority'+i+'" class="form-select select2" data-placeholder="Select Visa Issuing Authority..." data-allow-clear="true"><option value=""></option><option value="Mumbai" '+mumbaiselected+'>Mumbai</option><option value="New Delhi" '+delhiselected+'>New Delhi</option></select></div></div>';

                //         dynamic_visa_delegation_var += '<div class="col-md-4"><div class="mb-3">';
                //         dynamic_visa_delegation_var += '<label for="edit-visa-openings'+i+'" class="form-label">Opening <span class="text-danger">*</span></label><input type="text" name="openings[]" id="edit-visa-openings'+i+'" value="'+visaOpenings+'" class="form-control" placeholder="Enter number of vacancies"></div></div>';
                //         dynamic_visa_delegation_var += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger float-end remove2e">Remove</button></div></div>';


                //     }

                //     $('.dispVisaDelegationEdit').html(dynamic_visa_delegation_var);

                //     var select2ae1 = $('.select2');
                //     if (select2ae1.length) {
                //         select2ae1.each(function () {
                //             var $this = $(this);
                //             $this.wrap('<div class="position-relative"></div>').select2({
                //                 dropdownParent: $this.parent()
                //             });
                //         });
                //     }
                // }
                success: function(data){

                    console.log(data);

                    $('#edit-visa-from').val(data.post.businesstype).change();
                    $('#edit-partner').val(data.post.partneroffice_id).change();
                    $('#edit-careoff-id').val(data.post.careoff_id).change();
                    $('#edit-employer-name').val(data.post.employer_name);
                    $('#edit-employer-ar-name').val(data.post.employer_ar_name);
                    $('#edit-visa-no').val(data.post.visa_no);
                    $('#edit-id-no').val(data.post.id_no);
                    $('#edit-visa-date').val(data.post.visa_date);
                    $('#edit-visa-received-date').val(data.post.visa_received_date);
                    // $('#edit-proff-id').val(data.proff_id).change();
                    // $('#edit-issueing-authority').val(data.issuing_authority).change();
                    $('#edit-wpcity-id').val(data.post.wpcity_id).change();
                    $('#edit-wakala-status').val(data.post.wakala_status).change();
                    $('#edit-salary').val(data.post.salary);
                    $('#edit-notes').val(data.post.notes);

                    const proff_ids = data.post.proff_id.split(",");
                    const issuing_authos = data.post.issuing_authority.split(",");
                    if (data.post.openings != null) {
                        var visa_openings = data.post.openings.split(",");
                        $('#edit-visa-openings').val(visa_openings[0]);
                    }else{
                        $('#edit-visa-openings').val(null);
                    }




                    $('#edit-proff-id').val(proff_ids[0]).change();
                    $('#edit-issueing-authority').val(issuing_authos[0]).change();


                    // display dynamic created visa delegation work on tomorrow
                    var dynamic_visa_delegation_var = "";

                    for (let i = 1; i < proff_ids.length; i++) {

                        if (data.post.openings != null) {
                            var visaOpenings = visa_openings[i];
                        } else {
                            var visaOpenings = "";
                        }


                        dynamic_visa_delegation_var += '<tr>';
                        dynamic_visa_delegation_var += '<td></td>';
                        dynamic_visa_delegation_var += '<td><div class="mb-3">';
                        dynamic_visa_delegation_var += '<select name="proff_id[]" id="edit-proff-id'+i+'" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession..."><option value=""></option>';

                        // fetch profession with selected
                        data.professions.forEach(function(profession){
                            if (profession.id == proff_ids[i]) {
                                var selectedProf = "selected";
                            }else{
                                var selectedProf = "";
                            }

                            dynamic_visa_delegation_var += '<option value="'+profession.id+'" '+selectedProf+'>'+profession.eng_name+' ('+profession.ar_name+')</option>';

                        });

                        dynamic_visa_delegation_var += '</select></div></td>';

                        dynamic_visa_delegation_var += '<td><div class="mb-3">';
                        dynamic_visa_delegation_var += '<input type="text" name="openings[]" id="edit-visa-openings'+i+'" value="'+visaOpenings+'" class="form-control" placeholder="Enter number of vacancies"></div></td>';
                        dynamic_visa_delegation_var += '<td><button type="button" class="btn btn-sm btn-danger float-end remove2e">Remove</button></td></tr>';


                    }

                    // $('.dispVisaDelegationEdit').html(dynamic_visa_delegation_var);
                    $('#dispItemAppEd').append(dynamic_visa_delegation_var);
                    var select2ae1 = $('.select2');
                    if (select2ae1.length) {
                        select2ae1.each(function () {
                            var $this = $(this);
                            // $this.select2({
                            $this.wrap('<div class="position-relative"></div>').select2({
                                dropdownParent: $this.parent()
                            });
                        });
                    }
                }
            });

            // remove button

            $(document).on('click','.remove2e',function(){
                // $(this).closest('.row').remove();
                $(this).closest('tr').remove();
            });

        });
    });
</script>

    <script>
        $(document).ready(function(){
            $('#deasignCand').on('show.bs.modal',function(e){
                var candEmpID = $(e.relatedTarget).data('id');
                // alert(candEmpID);
                $('#candemp_id').val(candEmpID);
                $.ajax({
                    url: "{{ route('admin.employer.assigngetdata') }}",
                    method: "GET",
                    data:{
                        id: candEmpID,
                    },
                    success: function(data){
                        $('#deasign_text').text('Are you sure to deasign '+data.cand_name+' associate with pass no '+data.pass_no+' ?');
                    }
                });

            });

            // Deassign Candidate
            $('#DeassignCandidateValidation').on('submit',function(e){
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.employer.deassigncandidate') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data){
                        if (data.status == 1) {
                            toastr['success'](data.message, 'Success', { hideDuration: 3000 });
                            $('#DeassignCandidateValidation')[0].reset();
                            $('#deasignCand').modal('hide');
                            $('.nav-candidate-tab').load(' .nav-candidate-tab');
                            $('#mainContainDiv').load(' #mainContainDiv');

                        } else {
                            toastr['error'](data.message, 'Error', { hideDuration: 3000 });
                        }
                    }

                });

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


        let candidateId;

$(document).on('click', '.delemployer', function() {
    candidateId = $(this).data('id');
});

// Handle delete confirmation
$('#confirmDelete').on('click', function() {
    $.ajax({
        url: '/admin/employer-list/remove-visa-cand/' + candidateId,
        type: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}' // Include CSRF token for security
        },
        success: function(response) {
            if (response.success) {
                // Remove the candidate row from the table
                $('#delete_td').closest('tr').remove();
                $('#deleteStaff').modal('hide'); // Hide the modal
                $("#add_btn_candidate").html('<i class="ti ti-plus"></i> Add Candidate');
            } else {
                alert('Failed to delete the candidate. Please try again.');
            }
        },
        error: function() {
            alert('Error occurred while deleting the candidate. Please try again.');
        }
    });
});





 </script>

 <script>

    $(document).ready(function(){

        $('#assignCandidateValidation').validate({
            rules: {
                cand_id: {
                    required: true
                },
                proff_id:{
                    required: true
                }
            },
            messages: {
                cand_id: {
                    required: "Please select candidate..."
                },
                proff_id:{
                    required: "Please select profession"
                }
            },
            submitHandler: function (form) {
                let formData = new FormData(form);



                $.ajax({
                    url: "{{ route('admin.employer.assigncandtoemp') }}",
                    method: "POST",
                    type: "html",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data){
                        if (data.status == 1) {

                            toastr['success'](data.message, 'Success', { hideDuration: 3000 });

                            // 🔽 AUTO DOWNLOAD WORK AGREEMENT
                            let empId  = $('#visaeditid2').val();
                            let candId = $('#add-assign-candidate-list').val();

                            let downloadUrl = "{{ url('admin/employer-list/download-workagreement') }}/"
                                                + empId + "/" + candId;

                            window.open(downloadUrl);

                            $('#assignCandidateValidation')[0].reset();
                            $('#assignCandidateValidation select').val('').trigger('change');
                            $('#addAssignCandidate').modal('hide');
                            $('.nav-candidate-tab').load(' .nav-candidate-tab');
                            $('#mainContainDiv').load(' #mainContainDiv');

                        } else {
                            toastr['error'](data.message, 'Error', { hideDuration: 3000 });
                        }
                    }

                });

            }
        });

    });
 </script>

 <script>

$(document).ready(function() {

    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }



});

function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
}

    $('#candidateValidation_two').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Get values from the input fields
        var visaeditid = $("#visaeditid").val();
        var cand_id = $("#add-candidate-list").val();

        // Make sure the URL is correct
        $.ajax({
            url: '/admin/employer-list/add-visa-cand', // Update with the correct URL
            type: 'POST',
            data: {
                visaeditid: visaeditid,
                cand_id: cand_id
            },
            success: function(response) {
                console.log(response.post);
                if (response.success) {

                $('#addCandidate').modal('hide');
                $('#candidateValidation_two')[0].reset();

                // Update the table with the new candidate data


                var newRow = `
                    <tr>
                        <td>${response.post.cand_name || '---'}</td>
                        <td>${response.post.pass_no || '---'}</td>
                        <td>${response.post.candob ? formatDate(response.post.candob) : '---'}</td>
                        <td>${response.post.overall_exp ? response.post.overall_exp + ' Years' : '---'}</td>
                        <td>${response.post.pengname || '---'}</td>
                        ${response.post.id ? `
                            <td id="delete_td">
                                <a href="javascript:;" class="text-body delemployer" data-bs-toggle="modal" data-bs-target="#deleteStaff" data-id="${response.post.id}">
                                    <i class="ti ti-trash ti-sm mx-2"></i>
                                </a>
                            </td>
                        ` : ''}
                    </tr>
                `;

                // Append the new row to the table body
                $('#candidate_details').append(newRow);
                $("#add_btn_candidate").html('<i class="ti ti-plus"></i> Update Candidate');
                 toastr['success']('updated successfully', 'Success', { hideDuration: 3000 });
                } else {
                    toastr['success']('Already Exists', 'Success', { hideDuration: 3000 });

                }
            }
        });
    });
});
</script>

<script>
    $(document).ready(function(){
        $('#closedButton').on('click',function(){
            window.close();
        });
    });
</script>

@endsection
