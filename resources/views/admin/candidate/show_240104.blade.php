@extends('layout.admin.admin_layout')
@section('title','View Candidate')
@section('page-style')
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
<div class="row mb-2">
   <div class="col-lg-12">
      <div class="">
         <a href="{{ route('admin.candidate') }}" class="btn btn-sm btn-primary float-end">Back</a>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-lg-12">
      <div class="card">
         <div class="card-body">
            <div class="row">
               <div class="col-md-4">
                  <div class="d-flex align-items-start align-items-sm-center gap-4">
                     <div>
                        <img @if($post->photo_file != '') src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}"  @else src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" @endif alt="Avatar" class="d-block w-px-100 h-px-100 rounded" data-bs-toggle="modal" data-bs-target="#imageUpload" />
                     </div>
                     <div>
                        <h5 style="margin-top: -35px;">{{ $post->cand_name }}</h5>
                        <h6 style="margin-top: -15px;"><i class="ti ti-user-check"></i> @if(isset($occupation)) {{ $occupation->eng_name.' ('.$occupation->ar_name.')' }} @else {{ '---' }} @endif</h6>
                        <h6 style="margin-top: -15px;"><i class="tf-icons ti ti-phone-call ti-xs me-1"></i> @if($post->contact_no != '') {{ $post->contact_no }} @else {{ '---' }} @endif</h6>
                     </div>
                  </div>
               </div>
               <div class="col-md-8">
               </div>
            </div>
            <div class="row mt-3">
               <div class="col-md-12">
                  @if ($post->cv_execute == 1 && $post->cv_execute_file != '')
                  <a href="{{ asset('admin/assets/images/pdf/'.$post->cv_execute_file) }}" class="btn btn-sm btn-info" download=""><i class="ti ti-download"></i> CV</a>
                  {{-- <a href="{{ url('/admin/assets/images/pdf/'.$post->cv_execute_file) }}" class="btn btn-sm btn-info" download=""><i class="ti ti-download"></i> Download CV</a> --}}
                  @else
                  <button class="btn btn-sm btn-info" id="cvexecuteerro"><i class="ti ti-download"></i> CV</button>
                  @endif
                  {{-- <a href="{{ route('admin.candidate.single.resume',$post->id) }}" class="btn btn-sm btn-info" target="_blank"><i class="ti ti-download"></i> Download CV</a> --}}
                  <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#downloadcvcomp"><i class="ti ti-download"></i> CV As per company</button>
                  <button class="btn btn-sm btn-success" data-bs-toggle="offcanvas" data-bs-target="#edituser"><i class="ti ti-edit"></i> Edit</button>
                  <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocs"><i class="ti ti-file-upload"></i> Upload Docs</button>
                  {{-- <a href="{{ route('admin.candidate.publish',$post->id) }}" class="btn btn-sm btn-success"><i class="ti ti-cloud-upload"></i> Publish</a> --}}
                  @if ($post->cv_execute == 1)
                  <a href="{{ route('admin.candidate.cv.execute',$post->id) }}" class="btn btn-sm btn-success"><i class="ti ti-file-upload"></i> CV Execute</a>                                    
                  @else
                  <button class="btn btn-sm btn-warning" id="cvexecuteerro2"><i class="ti ti-file-upload"></i> CV Execute</button>
                  @endif
                  <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#upvidlink"><i class="ti ti-brand-youtube"></i> Upload Video</button>
                  <button class="btn btn-sm @if($post->publish == 1) btn-success @else btn-secondary @endif" data-bs-toggle="modal" data-bs-target="#updatePublished"><i class="ti ti-cloud-upload"></i> Publish</button>
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
                  <i class="tf-icons ti ti-home ti-xs me-1"></i> Details
                  </button>
               </li>
               <li class="nav-item">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-candidate-tab" aria-controls="nav-candidate-tab" aria-selected="false">
                  <i class="tf-icons ti ti-user-check ti-xs me-1"></i> Candidate
                  </button>
               </li>
               {{-- 
               <li class="nav-item"> 
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-personal" aria-controls="navs-justified-personal" aria-selected="false">
                  <i class="tf-icons ti ti-user-check ti-xs me-1"></i> Personal
                  </button>
               </li>
               <li class="nav-item">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-profile" aria-controls="navs-justified-profile" aria-selected="false">
                  <i class="tf-icons ti ti-file-check ti-xs me-1"></i> Passport
                  </button>
               </li>
               <li class="nav-item">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-experience" aria-controls="navs-justified-experience" aria-selected="false">
                  <i class="tf-icons ti ti-files ti-xs me-1"></i> Experience & Skills
                  </button>
               </li>
               <li class="nav-item">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-medical" aria-controls="navs-justified-experience" aria-selected="false">
                  <i class="tf-icons ti ti-files ti-xs me-1"></i> Medical
                  </button>
               </li>
               --}}
               <li class="nav-item">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-cand-payment-tab" aria-controls="nav-cand-payment-tab" aria-selected="false">
                  <i class="tf-icons ti ti-cash ti-xs me-1"></i> Payment
                  </button>
               </li>
               <li class="nav-item">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-messages" aria-controls="navs-justified-messages" aria-selected="false">
                  <i class="tf-icons ti ti-file ti-xs me-1"></i> File
                  </button>
               </li>
               <li class="nav-item">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-cvexecute-tab" aria-controls="nav-cvexecute-tab" aria-selected="false">
                  <i class="ti ti-file-upload"></i> CV Execute
                  </button>
               </li>
               <li class="nav-item">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-web-tab" aria-controls="nav-web-tab" aria-selected="false">
                  <i class="tf-icons ti ti-world ti-xs me-1"></i> Web
                  </button>
               </li>
               {{-- 
               <li class="nav-item">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-download-cv" aria-controls="nav-download-cv" aria-selected="false">
                  <i class="tf-icons ti ti-cloud-download ti-xs me-1"></i> Download CV
                  </button>
               </li>
               <li class="nav-item"> 
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-booking-hiring" aria-controls="nav-booking-hiring" aria-selected="true">
                  <i class="tf-icons ti ti-checklist ti-xs me-1"></i> Booking Details
                  </button>
               </li>
               <li class="nav-item">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-cand-booking-limit" aria-controls="nav-cand-booking-limit" aria-selected="true">
                  <i class="tf-icons ti ti-ban ti-xs me-1"></i> Booking Limit
                  </button>
               </li>
               --}}
               <li class="nav-item">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-actvities" aria-controls="navs-justified-actvities" aria-selected="false">
                  <i class="tf-icons ti ti-timeline ti-xs me-1"></i> Activities 
                  </button>
               </li>
            </ul>
         </div>
         <div class="card-body">
            <div class="tab-content p-0">
               <div class="tab-pane fade show active" id="navs-justified-home" role="tabpanel">
                  <div class="row">
                     @php
                     $total_exp = array_sum(explode(',',$post->experience));    
                     @endphp
                     <div class="col-md-4">
                        <div class="info-container">
                           <ul class="list-unstyled">
                              {{-- 
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Candidate Name:</span>
                                 <span>{{ $post->cand_name }}</span>
                              </li>
                              --}}
                              {{-- 
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Marital Status:</span>
                                 <span>{{ $post->marital_status }}</span>
                              </li>
                              --}}
                              {{-- 
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Occupation:</span>
                                 <span>@if(isset($occupation)) {{ $occupation->eng_name.' ('.$occupation->ar_name.')' }} @else {{ '---' }} @endif</span>
                              </li>
                              --}}
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Mobile No:</span>
                                 <span>@if($post->mobile_no != '') {{ $post->mobile_no }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Family Contact Number:</span>
                                 <span>@if($post->contact_no != '') {{ $post->contact_no }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-1">
                                 <span class="fw-semibold me-1">Email:</span>
                                 <span>@if($post->email != '') {{ $post->email }}  @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Care Off:</span>
                                 @if($post->careoff_id != '')
                                       @foreach( $careOff_data as  $careOff)
                                       <span>@if($careOff->id == $post->careoff_id) {{ $careOff->name }}@endif</span>
                                       @endforeach
                                 @else
                                       <span>---</span>
                                 @endif
                                 
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Created By:</span>
                                 <span>{{ $admin->name }}</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Reference Number</span>
                                 <span>{{ $post->reference_no }}</span>
                              </li>
                           </ul>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="info-container">
                           <ul class="list-unstyled">
                           </ul>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editDetails">Add/Edit</button>
                     </div>
                  </div>
               </div>
               <div class="tab-pane fade" id="nav-candidate-tab" role="tabpanel">
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
                           <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-musaned" aria-controls="navs-justified-musaned" aria-selected="false">
                           <i class="tf-icons ti ti-files ti-xs me-1"></i> Musaned
                           </button>
                        </li>
                        <li class="nav-item">
                           <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-medical" aria-controls="navs-justified-experience" aria-selected="false">
                           <i class="tf-icons ti ti-files ti-xs me-1"></i> Medical
                           </button>
                        </li>
                        <li class="nav-item">
                           <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-profile" aria-controls="navs-justified-profile" aria-selected="false">
                           <i class="tf-icons ti ti-file-check ti-xs me-1"></i> Passport
                           </button>
                        </li>
                     </ul>
                     <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-justified-personal" role="tabpanel">
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="info-container">
                                    <ul class="list-unstyled">
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Candidate Name:</span>
                                          <span>{{ $post->cand_name }}</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Candidate Arabic Name:</span>
                                          <span>@if($post->arcand_name != '') {{ $post->arcand_name }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Education:</span>
                                          <span>@if($post->education_id != '') {{ $educationname->name }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Religion:</span>
                                          {{-- <span>@if($post->religion != '') {{ $post->religion }} @else {{ '---' }} @endif</span> --}}
                                          <span>@if(isset($religionname)) {{ $religionname->name }} @else {{ "---" }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Marital Status:</span>
                                          <span>{{ $post->marital_status }}</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Occupation:</span>
                                          <span>@if(isset($occupation)) {{ $occupation->eng_name.' ('.$occupation->ar_name.')' }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Expected Salary:</span>
                                          <span>{{ $post->exp_sal }}</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Expected Work Location:</span>
                                          <span>@if($post->expwp_id != '') {{ implode(",",$myexpwl) }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Age:</span>
                                          <span>{{ $post->age }}</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Language Know:</span>
                                          <span>{{ $post->lang_known }}</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Region:</span>
                                          <span>@if(isset($candRegion)) {{ $candRegion->name }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">City:</span>
                                          {{-- <span>@if(isset($candcity)) {{ $candcity->name }} @else {{ '---' }} @endif</span> --}}
                                          <span>@if($post->candcity_text != '') {{ $post->candcity_text }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Embassy For:</span>
                                          <span>
                                          @if($post->embassy_for != '')  
                                          @if ($post->embassy_for == 1)
                                          {{ 'MUMBAI VISA REQUIRED' }}
                                          @endif
                                          @if ($post->embassy_for == 2)
                                          {{ 'DELHI VISA REQUIRED' }}
                                          @endif
                                          @else {{ '---' }} @endif
                                          </span>
                                       </li>
                                    </ul>
                                 </div>
                                 <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editPersonal">Edit</button>
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="navs-justified-experience" role="tabpanel">
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="info-container">
                                    <ul class="list-unstyled">
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Experience Region </span>
                                          <span>@if($post->gulfexperience == 1) {{ 'Indian Experience' }} @elseif($post->gulfexperience == 2) {{ 'Gulf Experience' }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Google Map:</span>
                                          <span>@if($post->google_map == 1) Yes @else No @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Car known:</span>
                                          <span>@if($post->carknown_id != '') {{ implode(',',$myCarkn) }} @else {{ '---' }} @endif</span>
                                       </li>
                                       @if ($post->experience != 0)
                                       @php
                                       $expls = explode(",",$post->experience);
                                       $professions = explode(",",$post->proff_id);
                                       $expcountries = explode(",",$post->expcountry_id);
                                       // $expcities = explode(",",$post->expcity_id);
                                       $expcities = explode(",",$post->expcity_id_text);
                                       @endphp
                                       @foreach ($expls as $key => $expl)
                                       @php
                                       $jobprof = DB::table('professions')->where('id','=',$professions[$key])->first();
                                       $expcountryl = DB::table('countries')->where('id','=',$expcountries[$key])->first();
                                       // $expcityl = DB::table('cities')->where('id','=',$expcities[$key])->first();
                                       @endphp
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Experience (in year):</span>
                                          <span>@if($expl != 0){{ $expl }} @else {{ 'Fresher' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Job Type:</span>
                                          <span>@if(isset($jobprof)) {{ $jobprof->eng_name }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Experience (country name):</span>
                                          <span>@if(isset($expcountryl)) {{ $expcountryl->name }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">City:</span>
                                          {{-- <span>@if(isset($expcityl)) {{ $expcityl->name }} @else {{ '---' }} @endif</span> --}}
                                          <span>@if($post->expcity_id_text != '') {{ $expcities[$key] }} @else {{ '---' }} @endif</span>
                                       </li>
                                       {{-- @if ($key != 0)
                                       <span class="text-primary">***</span>
                                       @endif --}}
                                       @endforeach     
                                       @else
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Experience (in year):</span>
                                          <span>Fresher</span>
                                       </li>
                                       @endif
                                    </ul>
                                 </div>
                                 <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editExperience">Add/ Edit</button>
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="navs-justified-musaned" role="tabpanel">
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="info-container">
                                    <ul class="list-unstyled">
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Musaned Status:</span>
                                          <span>
                                          @if($post->musaned_status != '')  
                                          @if ($post->musaned_status == 1)
                                          {{ 'Register' }}
                                          @endif
                                          @if ($post->musaned_status == 2)
                                          {{ 'Not Register' }}
                                          @endif
                                          @if ($post->musaned_status == 3)
                                          {{ 'Register at another office' }}
                                          @endif    
                                          @else {{ '---' }} @endif
                                          </span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Musaned Registration Date:</span>
                                          <span>@if($post->musaned_reg_date != '') {{ date('d-m-Y',strtotime($post->musaned_reg_date)) }} @else {{ '---' }} @endif</span>
                                       </li>
                                    </ul>
                                 </div>
                                 <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editMusaned">Add/Edit</button>
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="navs-justified-medical" role="tabpanel">
                           <div class="row">
                              @php
                              if ($post->medical_examine_date != '' && $post->medical_expiry_date != '') {
                              $diffdate = strtotime($post->medical_expiry_date) - time();
                              $getDays = round($diffdate / (60 * 60 * 24));
                              $days = $getDays." Days";
                              } else {
                              $days = "---";
                              }
                              @endphp
                              <div class="col-md-12">
                                 <div class="info-container">
                                    <ul class="list-unstyled">
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Medical Examine Date:</span>
                                          <span>@if($post->medical_examine_date != '') {{ date('d-m-Y',strtotime($post->medical_examine_date)) }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Medical Expiry Date:</span>
                                          <span>@if($post->medical_expiry_date != '') {{ date('d-m-Y',strtotime($post->medical_expiry_date)) }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Medical Expire (in Days):</span>
                                          <span>{{ $days }}</span>
                                       </li>
                                    </ul>
                                 </div>
                                 <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editMedical">Edit</button>
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="navs-justified-profile" role="tabpanel">
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="info-container">
                                    <ul class="list-unstyled">
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Passport No:</span>
                                          <span>{{ $post->pass_no }}</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Passport Type:</span>
                                          <span>{{ $post->pass_type }}</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Place of Issue:</span>
                                          {{-- <span>@if(isset($poi)) {{ $poi->name }} @else {{ '---' }} @endif</span> --}}
                                          <span>@if($post->poi_text !='') {{ $post->poi_text }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Date of Issue:</span>
                                          <span>@if($post->doi != '') {{ date('d-m-Y',strtotime($post->doi)) }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Date of Expiry:</span>
                                          <span>@if($post->doe != '') {{ date('d-m-Y',strtotime($post->doe)) }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Place of Birth:</span>
                                          {{-- <span>@if(isset($placeofbirth)) {{ $placeofbirth->name }}  @else {{ '---' }} @endif</span> --}}
                                          <span>@if($post->plb_text != '') {{ $post->plb_text }}  @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">DOB:</span>
                                          <span>@if($post->dob != '') {{ date('d-m-Y',strtotime($post->dob)) }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Nationality:</span>
                                          <span>@if(isset($nation)) {{ $nation->name }} @else {{ '---' }} @endif</span>
                                       </li>
                                       <li class="mb-2">
                                          <span class="fw-semibold me-1">Address:</span>
                                          <span>@if($post->address != '') {{ $post->address }} @else {{ '---' }} @endif</span>
                                       </li>
                                    </ul>
                                 </div>
                                 <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editPassport">Add/Edit</button>
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
                                          @if($post->embassy_for != '')  
                                          @if ($post->embassy_for == 1)
                                          {{ 'MUMBAI VISA REQUIRED' }}
                                          @endif
                                          @if ($post->embassy_for == 2)
                                          {{ 'DELHI VISA REQUIRED' }}
                                          @endif
                                          @else {{ '---' }} @endif
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
               {{-- 
               <div class="tab-pane fade" id="navs-justified-personal" role="tabpanel">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="info-container">
                           <ul class="list-unstyled">
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Religion:</span>
                                 <span>@if($post->religion != '') {{ $post->religion }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Expected Salary:</span>
                                 <span>{{ $post->exp_sal }}</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Expected Work Location:</span>
                                 <span>@if($post->expwp_id != '') {{ implode(",",$myexpwl) }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Age:</span>
                                 <span>{{ $post->age }}</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Language Know:</span>
                                 <span>{{ $post->lang_known }}</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Region:</span>
                                 <span>@if(isset($candRegion)) {{ $candRegion->name }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">City:</span>
                                 <span>@if(isset($candcity)) {{ $candcity->name }} @else {{ '---' }} @endif</span>
                              </li>
                           </ul>
                        </div>
                        <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editPersonal">Add/Edit</button>
                     </div>
                  </div>
               </div>
               --}}
               {{-- 
               <div class="tab-pane fade" id="navs-justified-profile" role="tabpanel">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="info-container">
                           <ul class="list-unstyled">
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Passport No:</span>
                                 <span>{{ $post->pass_no }}</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Passport Type:</span>
                                 <span>{{ $post->pass_type }}</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Place of Issue:</span>
                                 <span>@if(isset($poi)) {{ $poi->name }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Date of Issue:</span>
                                 <span>@if($post->doi != '') {{ date('d-m-Y',strtotime($post->doi)) }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Date of Expiry:</span>
                                 <span>@if($post->doe != '') {{ date('d-m-Y',strtotime($post->doe)) }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Place of Birth:</span>
                                 <span>@if(isset($placeofbirth)) {{ $placeofbirth->name }}  @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">DOB:</span>
                                 <span>@if($post->dob != '') {{ date('d-m-Y',strtotime($post->dob)) }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Nationality:</span>
                                 <span>@if(isset($nation)) {{ $nation->name }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Address:</span>
                                 <span>@if($post->address != '') {{ $post->address }} @else {{ '---' }} @endif</span>
                              </li>
                           </ul>
                        </div>
                        <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editPassport">Edit</button>
                     </div>
                  </div>
               </div>
               --}}
               {{-- 
               <div class="tab-pane fade" id="navs-justified-experience" role="tabpanel">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="info-container">
                           <ul class="list-unstyled">
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Experience Region</span>
                                 <span>@if($post->gulfexperience == 1) {{ 'Indian Experience' }} @elseif($post->gulfexperience == 2) {{ 'Gulf Experience' }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Google Map:</span>
                                 <span>@if($post->google_map == 1) Yes @else No @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Car known:</span>
                                 <span>@if($post->carknown_id != '') {{ implode(',',$myCarkn) }} @else {{ '---' }} @endif</span>
                              </li>
                              @if ($post->experience != 0)
                              @php
                              $expls = explode(",",$post->experience);
                              $professions = explode(",",$post->proff_id);
                              $expcountries = explode(",",$post->expcountry_id);
                              $expcities = explode(",",$post->expcity_id);
                              @endphp
                              @foreach ($expls as $key => $expl)
                              @php
                              $jobprof = DB::table('professions')->where('id','=',$professions[$key])->first();
                              $expcountryl = DB::table('countries')->where('id','=',$expcountries[$key])->first();
                              dd($expcountryl);
                              $expcityl = DB::table('cities')->where('id','=',$expcities[$key])->first();
                              @endphp
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Experience (in year):</span>
                                 <span>@if($expl != 0){{ $expl }} @else {{ 'Fresher' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Job Type:</span>
                                 <span>@if(isset($jobprof)) {{ $jobprof->eng_name }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Experience (country name):</span>
                                 <span>@if(isset($expcountryl)) {{ $expcountryl->name }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">City:</span>
                                 <span>@if(isset($expcityl)) {{ $expcityl->name }} @else {{ '---' }} @endif</span>
                              </li>
                              @if ($key != 0)
                              <span class="text-primary">***</span>
                              @endif
                              @endforeach     
                              @else
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Experience (in year):</span>
                                 <span>Fresher</span>
                              </li>
                              @endif
                           </ul>
                        </div>
                        <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editExperience">Edit</button>
                     </div>
                  </div>
               </div>
               --}}
               {{-- 
               <div class="tab-pane fade" id="navs-justified-medical" role="tabpanel">
                  <div class="row">
                     @php
                     if ($post->medical_examine_date != '' && $post->medical_expiry_date != '') {
                     $diffdate = strtotime($post->medical_expiry_date) - time();
                     $getDays = round($diffdate / (60 * 60 * 24));
                     $days = $getDays." Days";
                     } else {
                     $days = "---";
                     }
                     @endphp
                     <div class="col-md-12">
                        <div class="info-container">
                           <ul class="list-unstyled">
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Medical Examine Date:</span>
                                 <span>@if($post->medical_examine_date != '') {{ date('d-m-Y',strtotime($post->medical_examine_date)) }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Medical Expiry Date:</span>
                                 <span>@if($post->medical_expiry_date != '') {{ date('d-m-Y',strtotime($post->medical_expiry_date)) }} @else {{ '---' }} @endif</span>
                              </li>
                              <li class="mb-2">
                                 <span class="fw-semibold me-1">Medical Expire (in Days):</span>
                                 <span>{{ $days }}</span>
                              </li>
                           </ul>
                        </div>
                        <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editMedical">Add/Edit</button>
                     </div>
                  </div>
               </div>
               --}}
               <div class="tab-pane fade" id="nav-cand-payment-tab" role="tabpanel">
                  @if (isset($candsc))
                  <div class="row">
                     <div class="col-md-12" style="margin-bottom: 20px;">
                        <table class="table table-bordered">
                           <thead>
                              <tr>
                                 <th>#</th>
                                 <th>SERVICES CHARGES</th>
                                 <th>CANDIDATE</th>
                                 <th>GIVEN BY</th>
                                 <th>CREATED BY</th>
                                 <th>STATUS</th>
                                 <th>GIVEN DATE</th>
                                 <th>ACTION</th>
                              </tr>
                           </thead>
                           <tbody>
                              @foreach($candsc as $value)
                              @php
                              $is = 1;
                              $carbonDateTime = \Carbon\Carbon::parse($value->updated_at);
                              $activeService = DB::table('candsercharges')->where('id','=',$value->id)->where('status','=',1)->count();
                              @endphp
                              <tr>
                                 <th>{{ $is++ }}</th>
                                 <th>₹{{ $value->amount }}</th>
                                 <th>{{ $post->cand_name }}</th>
                                 <th>@foreach($users as $user) @if($user->id == $value->given_by) {{ $user->name }}  @endif @endforeach</th>
                                 <th>{{ $admin->name}}</th>
                                 <th>@if($value->status == 1) <span class="text-success">Active</span> @else <span class="text-danger">Deactive</span> @endif</th>
                                 <th>{{$carbonDateTime->format('Y-m-d')}}</th>
                                 <th>
                                    @if($value->edited_at && $value->edited_at->diffInMinutes(now()) <= 30)
                                    <a href="#" data-bs-target="#UpdateServiceCharge" data-bs-toggle="offcanvas" data-id="{{$value->id}}"><i class="ti ti-edit"></i></a>
                                    @endif
                                    @if(Auth::guard('admin')->user()->user_type == 1 && $activeService == 0)
                                    <a href="#" id="servdelcandamt" class="servdelcandamt" data-id="{{$value->id}}"><i class="ti ti-trash"></i></a>
                                    @endif
                                 </th>
                              </tr>
                              @endforeach
                           </tbody>
                        </table>
                     </div>
                     @endif
                     <div class="row">
                        <div class="col-md-12">
                           <table class="table table-bordered">
                              <thead>
                                 <tr>
                                    <th>#</th>
                                    <th>Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Transaction No</th>
                                    <th>Bank To</th>
                                    <th>Create By</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $ip = 1;
                                 @endphp
                                 @foreach ($paycands as $paycand)
                                 @php
                                 $bank_to = str_replace('_',' ',$paycand->bank_to);
                                 @endphp
                                 <tr>
                                    <th>{{ $ip++ }}</th>
                                    <th> ₹{{ $paycand->amount }}</th>
                                    <th>{{ $paycand->payment_mode }}</th>
                                    <th>{{ $paycand->txn_id }}</th>
                                    <th>{{ $bank_to }}</th>
                                    <th>{{ $paycand->uname }}</th>
                                    <th>
                                       <a href="#" data-bs-target="#editCandPayment" data-bs-toggle="offcanvas" data-id="{{ $paycand->id }}"><i class="ti ti-edit"></i></a>
                                       <a href="#" id="delcandamt" class="delcandamt" data-id="{{ $paycand->id }}"><i class="ti ti-trash"></i></a>
                                    </th>
                                 </tr>
                                 @endforeach
                                 <tr>
                                    <th class="float-right" colspan=4>Total Amount: {{$PaytotalAmount}}</th>
                                    <th colspan=2>Balance Amount: ₹{{$CalAmount}}</th>
                                    <th></th>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                        @php
                        $adminId = Auth::guard('admin')->user()->id;
                        $permission_staff = DB::table('candsercharges')->where('admin_id','=',$adminId)->count();
                        @endphp
                        <div class="col-md-12 mt-2">
                           @if(Auth::guard('admin')->user()->user_type == 2 &&  empty($candsc))
                           <button class="btn btn-sm btn-success me-1 float-end" data-bs-toggle="offcanvas" data-bs-target="#addServiceCharge">Service Charge</button>
                           @endif
                           @if(Auth::guard('admin')->user()->user_type == 1 &&  isset($candsc))
                           <button class="btn btn-sm btn-success me-1 float-end" data-bs-toggle="offcanvas" data-bs-target="#addServiceCharge">Service Charge</button>
                           @endif
                           @if (isset($candsc))
                           <button class="btn btn-sm btn-primary me-1 float-end" data-bs-toggle="offcanvas" data-bs-target="#addCandPayment">Add Payment</button>                                        
                           @else
                           <button class="btn btn-sm btn-primary me-1 float-end" id="paymentError">Add Payment</button>                                            
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
               <div class="tab-pane fade" id="navs-justified-messages" role="tabpanel">
                     <div class="row">
                        @if ($post->photo_file != '')
                        <div class="col-md-2">
                           <span class="badge bg-label-secondary">Photo</span>
                           <div class="mt-2">
                              <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="{{ $post->photo_file }}" class="d-block w-px-100 h-px-100 rounded" />
                           </div>
                           <div class="mt-1">
                              <a href="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                              <a href="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                              <a href=""><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                           </div>
                        </div>
                        @endif
                        @if ($post->pass_file != '')
                        <div class="col-md-2">
                           <span class="badge bg-label-secondary">Passport Copy</span>
                           <div class="mt-2">
                              <img src="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" alt="{{ $post->pass_file }}" class="d-block w-px-100 h-px-100 rounded" />
                           </div>
                           <div class="mt-1">
                              <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                              <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                              <a href=""><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                           </div>
                        </div>
                        @endif
                        @if ($post->lic_file != '')
                        <div class="col-md-2">
                           <span class="badge bg-label-secondary">License Copy</span>
                           <div class="mt-2">
                              <img src="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" alt="{{ $post->lic_file }}" class="d-block w-px-100 h-px-100 rounded" />
                           </div>
                           <div class="mt-1">
                              <a href="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                              <a href="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                              <a href=""><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                           </div>
                        </div>
                        @endif
                        @if ($post->cv_file != '')
                        <div class="col-md-2">
                           <span class="badge bg-label-secondary">Full Size Image</span>
                           <div class="mt-2">
                              <img src="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" alt="{{ $post->cv_file }}" class="d-block w-px-100 h-px-100 rounded" />
                           </div>
                           <div class="mt-1">
                              <a href="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                              <a href="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                              <a href=""><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                           </div>
                        </div>
                        @endif
                        @if ($filePhotos->count() > 0)
                        @foreach ($filePhotos as $filePhoto)
                        <div class="col-md-2">
                           <span class="badge 1bg-label-secondary">{{ $filePhoto->label }}</span>
                           <div class="mt-2">
                              <img src="{{ asset('admin/assets/images/candidate/'.$filePhoto->filename) }}" alt="{{ $post->filename }}" class="d-block w-px-100 h-px-100 rounded" />
                           </div>
                           <div class="mt-1">
                              <a href="{{ asset('admin/assets/images/candidate/'.$filePhoto->filename) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                              <a href="{{ asset('admin/assets/images/candidate/'.$filePhoto->filename) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                              <a href=""><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                           </div>
                        </div>
                        @endforeach
                        @endif
                        @if ($post->musaned_file != '')
                        <div class="col-md-2">
                           <span class="badge bg-label-secondary">Musaned Copy</span>
                           <div class="mt-2">
                              <img src="{{ asset('admin/assets/images/candidate/'.$post->musaned_file) }}" alt="{{ $post->musaned_file }}" class="d-block w-px-100 h-px-100 rounded" />
                           </div>
                           <div class="mt-1">
                              <a href="{{ asset('admin/assets/images/candidate/'.$post->musaned_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                              <a href="{{ asset('admin/assets/images/candidate/'.$post->musaned_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                              <a href=""><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                           </div>
                        </div>
                        @endif
                        <div class="col-md-12">
                           <button class="float-end btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocs2">Upload Docs</button>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane fade" id="nav-cvexecute-tab" role="tabpanel">
                     <div class="row">
                        <div class="col-md-12">
                           <table class="table table-bordered">
                              <thead>
                                 <tr>
                                    <th>#</th>
                                    <th>Company Name</th>
                                    <th>CV Execute Status</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $ipc = 1;
                                 @endphp
                                 @foreach ($partners as $partner)
                                 @php
                                 // CV execute File
                                 $recCVex = DB::table('companycvexecutes')->where('partner_id','=',$partner->id)->where('cand_id','=',$post->id)->first();
                                 @endphp
                                 <tr>
                                    <th>{{ $ipc++ }}</th>
                                    <th>{{ $partner->rec_off_name }}</th>
                                    <th>
                                       @if (isset($recCVex))
                                       @if ($recCVex->status == 1)
                                       <span class="bg-label-success">Execute</span>
                                       @else
                                       <span class="bg-label-danger">Not Execute</span>
                                       @endif
                                       @endif
                                    </th>
                                    <th>
                                       @if (isset($recCVex))
                                       @if ($recCVex->status == 1)
                                       <a href="{{ asset('admin/assets/images/pdf/partner/'.$recCVex->cv_file) }}" title="Download CV" target="_blank"><i class="ti ti-file-download"></i> Download CV</a>
                                       <a href="{{ url('admin/candidate/cv/execute/'.$post->id.'/'.$partner->id) }}" title="Execute CV"><i class="ti ti-file-upload"></i> Execute CV</a>
                                       {{-- <a href="" title="Delete CV"><i class="ti ti-trash"></i></a> --}}
                                       @else
                                       <a href="#" id="errorDownload1" title="Download CV"><i class="ti ti-file-download"></i> Download CV</a>
                                       <a href="{{ url('admin/candidate/cv/execute/'.$post->id.'/'.$partner->id) }}" title="Execute CV"><i class="ti ti-file-upload"></i> Execute CV</a>
                                       {{-- <a href="" title="Delete CV"><i class="ti ti-trash"></i></a> --}}
                                       @endif
                                       @else
                                       <a href="#" id="errorDownload2" title="Download CV"><i class="ti ti-file-download"></i> Download CV</a>  
                                       <a href="{{ url('admin/candidate/cv/execute/'.$post->id.'/'.$partner->id) }}" title="Execute CV"><i class="ti ti-file-upload"></i> Execute CV</a>     
                                       {{-- <a href="" title="Delete CV"><i class="ti ti-trash"></i></a> --}}                                                         
                                       @endif
                                    </th>
                                 </tr>
                                 @endforeach
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane fade" id="nav-web-tab" role="tabpanel">
                     <div class="nav-align-left mb-4">
                        <ul class="nav nav-pills me-3" role="tablist">
                           <li class="nav-item">
                              <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-download-cv" aria-controls="nav-download-cv" aria-selected="false">
                              <i class="tf-icons ti ti-cloud-download ti-xs me-1"></i> Download CV
                              </button>
                           </li>
                           <li class="nav-item"> 
                              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-booking-hiring" aria-controls="nav-booking-hiring" aria-selected="true">
                              <i class="tf-icons ti ti-checklist ti-xs me-1"></i> Booking Details
                              </button>
                           </li>
                           <li class="nav-item">
                              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-cand-booking-limit" aria-controls="nav-cand-booking-limit" aria-selected="true">
                              <i class="tf-icons ti ti-ban ti-xs me-1"></i> Booking Limit
                              </button>
                           </li>
                        </ul>
                        <div class="tab-content">
                           <div class="tab-pane fade show active" id="navs-download-cv" role="tabpanel">
                              <div class="row">
                                 <div class="col-md-12">
                                    <table class="table table-bordered">
                                       <thead>
                                          <tr>
                                             <th>#</th>
                                             <th>Customer Name</th>
                                             <th>Download Date</th>
                                          </tr>
                                       </thead>
                                       @if ($candDownloads)
                                       <tbody>
                                          @php
                                          $i = 1;
                                          @endphp
                                          @foreach ($candDownloads as $candDownload)
                                          <tr>
                                             <td>{{ $i++ }}</td>
                                             <td>{{ $candDownload->uname }}</td>
                                             <td>{{ date('d-m-Y h:i:s A',strtotime($candDownload->created_at)) }}</td>
                                          </tr>
                                          @endforeach
                                       </tbody>
                                       @endif
                                    </table>
                                 </div>
                              </div>
                           </div>
                           <div class="tab-pane fade" id="nav-booking-hiring" role="tabpanel">
                              <div class="row">
                                 <div class="col-md-12">
                                    <table class="table table-bordered">
                                       <thead>
                                          <tr>
                                             <th>Reference No</th>
                                             <th>Employer Name</th>
                                             <th>Booking Status</th>
                                             <th>Visa Status</th>
                                             <th>Payment Status</th>
                                             <th>Booking Date</th>
                                          </tr>
                                       </thead>
                                       @if (isset($candBooks))
                                       @foreach ($candBooks as $candBook)
                                       <tr>
                                          <td>{{ $candBook->reference_no }}</td>
                                          <td>{{ $candBook->uname }}</td>
                                          <td>
                                             @if ($candBook->booking_status == 0)
                                             <span class="badge bg-label-info">Pending</span>
                                             @elseif ($candBook->booking_status == 1)
                                             <span class="badge bg-label-success">Confirm</span>
                                             @elseif ($candBook->booking_status == 2)
                                             <span class="badge bg-label-danger">Cancel</span>
                                             @endif
                                          </td>
                                          <td>
                                             @if ($candBook->visa_status == 0)
                                             <span class="badge bg-label-danger">Pending</span>
                                             @elseif ($candBook->visa_status == 1)
                                             <span class="badge bg-label-success">Complete</span>
                                             @endif
                                          </td>
                                          <td>
                                             @if ($candBook->payment_status == 0)
                                             <span class="badge bg-label-danger">Pending</span>
                                             @elseif ($candBook->payment_status == 1)
                                             <span class="badge bg-label-success">Complete</span>
                                             @endif
                                          </td>
                                          <td>{{ date('d-m-Y h:i:s A',strtotime($candBook->booking_date)) }}</td>
                                       </tr>
                                       @endforeach
                                       @endif
                                    </table>
                                 </div>
                              </div>
                           </div>
                           <div class="tab-pane fade" id="nav-cand-booking-limit" role="tabpanel">
                              <form action="{{ route('admin.booking.limit',$post->id) }}" method="POST" id="candBookinglimit">
                                 @csrf
                                 <div class="row">
                                    <div class="col-md-4 mb-3">
                                       <label for="candlimit" class="form-label">Booking Limit <span class="text-danger">*</span></label>
                                       <input type="text" name="limit" id="candlimit" class="form-control" value="@if(isset($candcount)) {{ $candcount->limit }} @endif" placeholder="Enter booking limit...">
                                    </div>
                                    <div class="col-md-12">
                                       <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </div>
                                 </div>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane fade" id="navs-justified-actvities" role="tabpanel">
                     <div class="row">
                        <div class="col-md-6">
                           @if ($timelines->count() > 0)
                           <ul class="timeline mt-3 mb-0">
                              @php
                              $totalTimeline = $timelines->count();
                              @endphp
                              @foreach ($timelines as $timeline)
                              @php
                              if ($timeline->admin_id != '') {
                              $aUser = DB::table('admins')->where('id','=',$timeline->admin_id)->first();
                              }
                              if ($timeline->user_id != '') {
                                 $cUser = DB::table('users as user')
                                 ->leftJoin('userprofiles as profile', 'user.id', '=', 'profile.user_id')
                                 ->select('user.name as uname', 'profile.photo')
                                 ->where('user.id', '=', $timeline->user_id)
                                 ->first(); 

                              // Check if $cUser is null before accessing its properties
                              if ($cUser) {
                                 // $cUser is not null, so you can safely access its properties
                                 $userName = $cUser->uname;
                                 $userPhoto = $cUser->photo;
                              } else {
                                 // Handle the case where no user is found
                                 // For example, you can set default values or show an error message
                                 $userName = 'Unknown';
                                 $userPhoto = 'default.jpg';
                              }
                              } else {
                                 // Handle the case where $timeline->user_id is empty
                                 // For example, you can set default values or show an error message
                                 $userName = 'Unknown';
                                 $userPhoto = 'default.jpg';
                              }
                              if ($timeline->partner_id != '') {
                              $pUser = DB::table('partners as partner')->where('id','=',$timeline->partner_id)->first();
                              }
                              $staeNum = rand(0,6);
                              $states = ['primary','success','danger','warning','info','secondary','dark'];
                              $state = $states[$staeNum];
                              $borderNumber = $totalTimeline - 1;
                              if ($borderNumber > 0) {
                              $borderStyle = "pb-4 border-left-dashed";
                              } else {
                              $borderStyle = "pb-3 border-0";
                              }
                              @endphp
                              <li class="timeline-item timeline-item-{{ $state.' '.$borderStyle }}">
                                 <span class="timeline-indicator timeline-indicator-{{ $state }}">
                                 <i class="ti {{ $timeline->icons }}"></i>
                                 </span>
                                 <div class="timeline-event">
                                    <div class="timeline-header border-bottom mb-3">
                                       <h6 class="mb-0">{{ $timeline->headline }}</h6>
                                       <span class="text-muted">{{ date('d-m-Y h:i A',strtotime($timeline->created_at)) }}</span>
                                    </div>
                                    <p>{{ $timeline->bodyMessage }}</p>
                                    <div class="d-flex justify-content-between flex-wrap gap-2">
                                       <div class="d-flex flex-wrap">
                                          @if ($timeline->admin_id != '')
                                          <div class="avatar me-3">
                                             <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Avatar" class="rounded-circle" />
                                          </div>
                                          <div>
                                          @if ($aUser)
                                             <p class="mb-0">{{ $aUser->name }}</p>
                                          @else
                                              <p class="mb-0">Unknown</p> <!-- or any default value you prefer -->
                                          @endif
                                             {{-- <span class="text-muted">Javascript Developer</span> --}}
                                          </div>
                                          @endif
                                          @if ($timeline->user_id != '')
                                          <div class="avatar me-3">
                                             @if ($userPhoto != '')
                                             <img src="{{ asset('user/img/avatars',$userPhoto) }}" alt="Avatar" class="rounded-circle" />                                                                        
                                             @else
                                             <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Avatar" class="rounded-circle" />
                                              @endif
                                          </div>
                                          <div>
                                             <p class="mb-0">{{ $userName }}</p>
                                             {{-- <span class="text-muted">Javascript Developer</span> --}}
                                          </div>
                                          @endif
                                          @if ($timeline->partner_id != '')
                                          <div class="avatar me-3">
                                             <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Avatar" class="rounded-circle" />
                                          </div>
                                          <div>
                                             <p class="mb-0">{{ $pUser->rec_off_name }}</p>
                                             {{-- <span class="text-muted">Javascript Developer</span> --}}
                                          </div>
                                          @endif
                                       </div>
                                    </div>
                                 </div>
                              </li>
                              @php
                              $totalTimeline--;
                              @endphp
                              @endforeach
                           </ul>
                           @endif    
                        </div>
                     </div>
                  </div>
            </div>
         </div>
      </div>
   </div>
   <!-- Upload video link start -->
   <!-- Modal -->
   <div class="modal fade" id="upvidlink" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel1">Upload Video</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <!-- Tabs -->
               <ul class="nav nav-tabs" id="myTabs">
                  <li class="nav-item">
                     <a class="nav-link active" id="link-tab" data-bs-toggle="tab" href="#video-upload">Video Upload</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="video-tab" data-bs-toggle="tab" href="#video-link">Introduction Video</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="test_video-tab" data-bs-toggle="tab" href="#test-video-link">Trade Test Video</a>
                  </li>
               </ul>
               <!-- Tab Content -->
               <div class="tab-content mt-2">
                  <!-- Link Form Tab -->
                  <div class="tab-pane fade show active" id="video-upload">
                     <form action="/admin/candidate/videoFile/{{$post->id}}" method="POST" id="upvidlinkval" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                           <div class="row">
                              <div class="col-md-10">
                                 <label class="form-label" for="add-upload-pp2">Upload File</label>
                                 <input type="file" name="video_file" id="add-upload-pp2"  accept="video/*" class="form-control"  @if($post->video_file != '') value="{{ $post->video_file }}" @endif>
                              </div>
                           </div>
                        </div>
                        <button type="button" class="btn btn-label-secondary mt-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary mt-3">Save changes</button>
                     </form>
                  </div>
                  <!-- Video Form Tab -->
                  <div class="tab-pane fade" id="video-link">
                     <form action="{{ route('admin.candidate.video.store',$post->id) }}" method="POST" id="upvidlinkval">
                        @csrf
                        <label for="add-video-link" class="form-label">Introduction Video</label>
                        <input class="form-control" type="text" id="add-video-link" @if($post->video_link != '') value="{{ $post->video_link }}" @endif name="video_link" placeholder="Enter video link..." />
                        <button type="submit" class="btn btn-primary mt-3">Save Video Link</button>
                     </form>
                  </div>
                  <!-- Test Video Tab -->
                  <div class="tab-pane fade" id="test-video-link">
                     <form action="{{ route('admin.candidate.testvideo.store',$post->id) }}" method="POST" id="uptestvidlinkval">
                        @csrf
                        <label for="add-test-video-link" class="form-label">Trade Test Video Link</label>
                        <input class="form-control" type="text" id="add-test-video-link" @if($post->trade_test_video_link != '') value="{{ $post->trade_test_video_link }}" @endif name="trade_test_video_link" placeholder="Enter video link..." />
                        <button type="submit" class="btn btn-primary mt-3">Save Video Link</button>
                     </form>
                  </div>
               </div>
            </div>
            <!--  <div class="modal-footer">
               <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-primary">Save changes</button>
               </div> -->
         </div>
      </div>
   </div>
   <!-- Upload Video Link end -->
   <!-- Candidate Image Upload Modal Start-->
   <div class="modal fade" id="imageUpload" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel1">Upload Photo</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.candidate.photo.store',$post->id) }}" method="POST" id="photoValid" enctype="multipart/form-data">
               @csrf
               <div class="modal-body">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="mb-3">
                           <label for="add-photo-file" class="form-label">Photo</label>
                           <input class="form-control" type="file" id="add-photo-file" name="file_photo" />
                        </div>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save changes</button>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- Candidate Image Upload Modal End -->
   <!-- Download CV as per company Start -->
   <div class="modal fade" id="downloadcvcomp" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel125">Download CV as per company</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.download.cvcompany',$post->id) }}" method="POST" id="downloadCVvalidation">
               @csrf
               <div class="modal-body">
                  <div class="row">
                     @php
                     $partners = DB::table('partners')->get();
                     @endphp
                     <div class="col-md-12 mb-3">
                        <input type="hidden" name="candcv_id" id="candcv_id" value="{{ $post->id }}">
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
                  <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary" id="downloadCandCV">Download Cv</button>
                  {{-- <button type="button" class="btn btn-primary" id="downloadCandCV2">Download Cv</button> 10-07-2023 --}} 
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- Download CV as per company End -->
   <!-- Candidate Docs Upload Modal Start -->
   <div class="modal fade" id="uploadDocs" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel1">Upload Docs</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.candidate.docs.store',$post->id) }}" method="POST" id="photoValid2" enctype="multipart/form-data">
               @csrf
               <div class="modal-body">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="mb-3">
                           <div class="mb-2">
                              @if ($post->photo_file != '')
                              <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="photo" class="d-block w-px-100 h-px-100 rounded" id="viewupphoto">
                              @else
                              <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="Photo" class="d-block w-px-100 h-px-100 rounded" id="viewupphoto">
                              @endif
                           </div>
                           <label class="form-label" for="add-upload-photo2">Upload Photo</label>
                           <input type="file" name="photo_file" id="add-upload-photo2" class="form-control add-upload-photo2">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <div class="mb-2">
                              @if ($post->pass_file != '')
                              <img src="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" alt="photo" class="d-block w-px-100 h-px-100 rounded" id="viewpassfile">
                              @else
                              <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="Photo" class="d-block w-px-100 h-px-100 rounded" id="viewpassfile">
                              @endif
                           </div>
                           <label class="form-label" for="add-upload-pp2">Upload Passport</label>
                           <input type="file" name="pass_file" id="add-upload-pp2" class="form-control add-upload-pp2">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <div class="mb-2">
                              @if ($post->lic_file != '')
                              <img src="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" alt="photo" class="d-block w-px-100 h-px-100 rounded" id="viewlicfile">
                              @else
                              <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="Photo" class="d-block w-px-100 h-px-100 rounded" id="viewlicfile">
                              @endif
                           </div>
                           <label class="form-label" for="add-upload-lic2">Upload License</label>
                           <input type="file" name="lic_file" id="add-upload-lic2" class="form-control add-upload-lic2">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <div class="mb-2">
                              @if ($post->cv_file != '')
                              <img src="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" alt="photo" class="d-block w-px-100 h-px-100 rounded" id="viewcvfile">
                              @else
                              <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="Photo" class="d-block w-px-100 h-px-100 rounded" id="viewcvfile">
                              @endif
                           </div>
                           <label class="form-label" for="add-upload-cv2">Upload Full Image</label>
                           <input type="file" name="cv_file" id="add-upload-cv2" class="form-control add-upload-cv2">
                        </div>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save changes</button>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- Candidate Docs Upload Modal End -->
   <!-- Candidate Docs Upload Modal Start -->
   <div class="modal fade" id="uploadDocs2" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel1">Upload File</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.candidate.docs2.store',$post->id) }}" method="POST" id="photoValid22" enctype="multipart/form-data">
               @csrf
               <div class="modal-body">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="add-label-file">File Type <span class="text-danger">*</span></label>
                           <input type="text" name="label" id="add-label-file" class="form-control" placeholder="Enter file type">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label class="form-label" for="add-upload-pp2">Upload File</label>
                           <input type="file" name="filename" id="add-upload-pp2" class="form-control">
                        </div>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save changes</button>
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- Candidate Docs Upload Modal End -->
   <!-- Candidate Edit Page Start -->
   <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel">
      <div class="offcanvas-header">
         <h5 id="edituserLabel" class="offcanvas-title">Edit Candidate</h5>
         <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
         <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.candidate.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @php
            $experience = explode(",",$post->experience);
            $expconts = explode(",",$post->expcountry_id);
            $expcits = explode(",",$post->expcity_id);
            $proffsp = explode(",",$post->proff_id);
            $langsk = explode(",",$post->lang_known);
            $expwp = explode(",",$post->expwp_id);
            $carknow1 = explode(",",$post->carknown_id);
            $expcittext = explode(",",$post->expcity_id_text);
            $ieexp = 0;
            // dd($langsk);
            @endphp
            <div class="row">
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="add-associate-id" class="form-label">Associate <span class="text-danger">*</span></label>
                     <select name="associate_id" class="form-select select2" id="add-associate-id" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="7" @if($post->associate_id == 7) selected @endif>Direct Candidate</option>
                        @foreach ($associates as $associate2)
                        <option value="{{ $associate2->id }}" @if($associate2->id == $post->associate_id) selected @endif>{{ $associate2->pty_full_name.' ('.$associate2->pty_ag_name.')' }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-care-off">Careoff <span class="text-danger">*</span></label>
                     <select name="careoff_id" id="add-care-off" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($careoffs as $careoff2)
                        <option value="{{ $careoff2->id }}" @if($careoff2->id == $post->careoff_id) selected @endif>{{ $careoff2->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <input type="hidden" name="editID" value="{{ $post->id }}">
                     <label class="form-label" for="add-cand-name">Name <span class="text-danger">*</span></label>
                     <input type="text" name="cand_name" id="add-cand-name" class="form-control" value="{{ $post->cand_name }}" placeholder="Enter candidate name...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-cand-arname">Arabic Name <span class="text-danger">*</span></label>
                     <input type="text" name="arcand_name" id="add-cand-arname" class="form-control" value="{{ $post->arcand_name }}" placeholder="Enter candidate name...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-pass-no">Passport No <span class="text-danger">*</span></label>
                     <input type="text" name="pass_no" id="add-pass-no" class="form-control" value="{{ $post->pass_no }}" placeholder="Enter candidate name...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-pass-type">Passport Type <span class="text-danger">*</span></label>
                     <select name="pass_type" id="add-pass-type" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="ECNR" @if($post->pass_type == 'ECNR') selected @endif>ECNR</option>
                        <option value="ECR" @if($post->pass_type == 'ECR') selected @endif>ECR</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-dob">Date of Birth <span class="text-danger">*</span></label>
                     <input type="date" name="dob" id="add-dob" value="{{ $post->dob }}" class="form-control" placeholder="Enter date of birth...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-doi">Date of Issue <span class="text-danger">*</span></label>
                     <input type="date" name="doi" id="add-doi" value="{{ $post->doi }}" class="form-control" placeholder="Enter date of issue...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-doe">Date of Expiry <span class="text-danger">*</span></label>
                     <input type="date" name="doe" id="add-doe" value="{{ $post->doe }}" class="form-control" placeholder="Enter date of expiry...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="form-label" for="add-place-of-issue">Place of Issue</label>
                     {{-- 
                     <select name="poi" id="add-place-of-issue" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($poiss as $pois)
                        <option value="{{ $pois->id }}" @if($pois->id == $post->poi) selected @endif>{{ $pois->name }}</option>
                        @endforeach
                     </select>
                     --}}
                     <input type="text" name="poi_text" id="add-place-of-issue" value="{{ $post->poi_text }}" class="form-control cityTypehead" placeholder="Type city here..">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-place-of-birth">Place of Birth</label>
                     {{-- 
                     <select name="plb_id" id="add-place-of-birth" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($cities as $cityplb)
                        <option value="{{ $cityplb->id }}" @if($cityplb->id == $post->plb_id) selected @endif>{{ $cityplb->name }}</option>
                        @endforeach
                     </select>
                     --}}
                     <input type="text" name="plb_text" id="add-place-of-birth" class="form-control cityTypehead" value="{{ $post->plb_text }}" placeholder="Type city here...">
                  </div>
               </div>
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-age">Age <span class="text-danger">*</span></label>
                     <input type="text" name="age" id="add-age" value="{{ $post->age }}" class="form-control" placeholder="Enter age...">
                  </div>
               </div>
               --}}
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-nationality-id">Nationality</label>
                     <select name="nation_id" id="add-nationality-id" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($countries as $nationid)
                        <option value="{{ $nationid->id }}" @if($nationid->id == $post->nation_id) selected @endif>{{ $nationid->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-region">Region</label>
                     <select name="region_id" id="add-region" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($regions as $region)
                        <option value="{{ $region->id }}" @if($region->id == $post->region_id) selected @endif>{{ $region->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-cand-city">City</label>
                     {{-- 
                     <select name="candcity_id" id="add-cand-city" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($cities as $candcity)
                        <option value="{{ $candcity->id }}" @if($candcity->id == $post->candcity_id) @selected(true) @endif>{{ $candcity->name }}</option>
                        @endforeach
                     </select>
                     --}}
                     <input type="text" name="candcity_text" class="form-control cityTypehead" value="{{ $post->candcity_text }}" placeholder="Type city here...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-religion-id">Religion</label>
                     <select name="religion_id" id="add-religion-id" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        {{-- <option value="Muslim" @if($post->religion == 'Muslim') selected @endif>Muslim</option>
                        <option value="Hindu" @if($post->religion == 'Hindu') selected @endif>Hindu</option>
                        <option value="Christian" @if($post->religion == 'Christian') selected @endif>Christian</option>
                        <option value="Sikh" @if($post->religion == 'Sikh') selected @endif>Sikh</option> --}}
                        @foreach ($religions as $religion)
                        <option value="{{ $religion->id }}" @if($religion->id == $post->religion_id) selected @endif>{{ $religion->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-contact-number">Contact Number</label>
                     <input type="text" name="contact_no" id="add-contact-number" value="{{ $post->contact_no }}" class="form-control" placeholder="Enter contact number...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-marital-status">Marital Status <span class="text-danger">*</span></label>
                     <select name="marital_status" id="add-marital-status" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="Unmarried" @if($post->marital_status == 'Unmarried') selected @endif>Unmarried</option>
                        <option value="Married" @if($post->marital_status == 'Married') selected @endif>Married</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-language-known">Language Known <span class="text-danger">*</span></label>
                     <select name="lang_known[]" id="add-language-known" class="form-select select2" multiple>
                     <option value="English" @if(in_array('English',$langsk)) selected @endif>English</option>
                     <option value="Hindi" @if(in_array('Hindi',$langsk)) selected @endif>Hindi</option>
                     <option value="Urdu" @if(in_array('Urdu',$langsk)) selected @endif>Urdu</option>
                     <option value="Arabic" @if(in_array('Arabic',$langsk)) selected @endif>Arabic</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-expected-sal">Expected Salary <span class="text-danger">*</span></label>
                     <input type="text" name="exp_sal" id="add-expected-sal" value="{{ $post->exp_sal }}" class="form-control" placeholder="Enter expected salary...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-mob-number">Mobile Number</label>
                     <input type="text" name="mobile_no" value="{{ $post->mobile_no }}" id="add-mob-number" class="form-control" placeholder="Enter mobile number...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="form-label" for="add-occupation">Applied for, Occupation</label>
                     <select name="jobtype_id" id="add-occupation" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($jobtypes as $jobtype)
                        <option value="{{ $jobtype->id }}" @if($post->jobtype_id == $jobtype->id) selected @endif>{{ $jobtype->eng_name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-address">Address</label>
                     <input type="text" class="form-control" id="add-address" value="{{ $post->address }}" name="address" placeholder="Enter address...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-expect-wkp">Expected Work Place</label>
                     <select name="expwp_id[]" id="add-expect-wkp" class="form-select select2" data-allow-clear="true" multiple>
                        <option value="">Select</option>
                        @foreach ($expworklocs as $expwkpc)
                        <option value="{{ $expwkpc->id }}" @if(in_array($expwkpc->id,$expwp)) selected @endif>{{ $expwkpc->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <label for="" class="form-label">Google Map</label>
                  <div class="mb-3">
                     <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="google_map" id="add-google-map-yes" @if($post->google_map == 1) checked @endif value="1"/>
                        <label class="form-check-label" for="add-google-map-yes">Yes</label>
                     </div>
                     <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="google_map" id="add-google-map-no" @if($post->google_map == 0) checked @endif value="0"/>
                        <label class="form-check-label" for="add-google-map-no">No</label>
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-gulfexperience">Experience <span class="text-danger">*</span></label>
                     <select name="gulfexperience" id="add-gulfexperience" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="1" @if($post->gulfexperience == 1) selected @endif>Indian Experience</option>
                        <option value="2" @if($post->gulfexperience == 2) selected @endif>Gulf Experience</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-car-known">Car Known</label>
                     <select name="carknown_id[]" id="add-car-known" class="form-select select2" multiple>
                     @foreach ($cars as $car1)
                     <option value="{{ $car1->id }}" @if(in_array($car1->id,$carknow1)) selected @endif>{{ $car1->name }}</option>
                     @endforeach
                     </select>
                  </div>
               </div>
            </div>
            @foreach ($experience as $index => $value )
            <div class="row">
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-exoinyear{{ $index }}">Experience (in year) <span class="text-danger">*</span></label>
                     <input type="text" name="experience[]" id="add-exoinyear{{ $index }}" value="{{ $value }}"  class="form-control" placeholder="Enter experience in year...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-job-type{{ $index }}">Job Type</label>
                     <select name="proff_id[]" id="add-job-type{{ $index }}" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($jobtypes as $jobtype)
                        <option value="{{ $jobtype->id }}" @if($jobtype->id == $proffsp[$index]) selected @endif>{{ $jobtype->eng_name.' ('.$jobtype->ar_name.')' }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-expcountry_id{{ $index }}">Experience (country name)</label>
                     <select name="expcountry_id[]" id="add-expcountry_id{{ $index }}" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($countries as $country)
                        <option value="{{ $country->id }}" @if($country->id == $expconts[$index]) selected @endif>{{ $country->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="form-label" for="add-expcity_id{{ $index }}">City</label>
                     {{-- 
                     <select name="expcity_id[]" id="add-expcity_id{{ $index }}" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($expworkcities as $city)
                        <option value="{{ $city->id }}" @if($city->id == $expcits[$index]) selected @endif>{{ $city->name }}</option>
                        @endforeach
                     </select>
                     --}}
                     <input type="text" name="expcity_id_text[]" id="add-expcity_id{{ $index }}" @if(count($expcittext) > $ieexp) value="{{ $expcittext[$index] }}" @endif class="form-control cityTypehead" placeholder="Type city here...">
                  </div>
               </div>
               @if ($index == 0)
               <div class="col-md-12">
                  <button type="button" class="btn btn-sm btn-primary float-end addExp">Add</button>
               </div>
               @endif
               @if ($index != 0)
               <div class="col-md-12">
                  <button type="button" class="btn btn-sm btn-danger remove float-end">Remove</button>
               </div>
               @endif
            </div>
            @php
            $ieexp++;
            @endphp
            @endforeach
            <div id="dispEXP">
            </div>
            <div class="row">
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-upload-pp">Upload Passport</label>
                     <input type="file" name="pass_file" id="add-upload-pp" class="form-control">
                  </div>
               </div>
               --}}
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-upload-lic">Upload License</label>
                     <input type="file" name="lic_file" id="add-upload-lic" class="form-control">
                  </div>
               </div>
               --}}
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-upload-cv">Upload CV</label>
                     <input type="file" name="cv_file" id="add-upload-cv" class="form-control">
                  </div>
               </div>
               --}}
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-upload-photo">Upload Photo</label>
                     <input type="file" name="photo_file" id="add-upload-photo" class="form-control">
                  </div>
               </div>
               --}}
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
         </form>
      </div>
   </div>
   <!-- Candidate Edit Page End-->
   <!-- Personal Edit Page Start -->
   <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editPersonal" aria-labelledby="editPersonalLabel">
      <div class="offcanvas-header">
         <h5 id="editPersonalLabel" class="offcanvas-title">Edit Personal</h5>
         <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
         <form class="add-new-user pt-0" id="editPersonalForm" action="{{ route('admin.candidate.update.personal') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @php
            $langsk = explode(",",$post->lang_known);
            $expwp = explode(",",$post->expwp_id);
            @endphp
            <div class="row">
               <input type="hidden" name="editID" value="{{ $post->id }}">
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-cand-name-det">Name <span class="text-danger">*</span></label>
                     <input type="text" name="cand_name" id="add-cand-name-det" class="form-control" value="{{ $post->cand_name }}" placeholder="Enter candidate name...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-cand-arname-det">Arabic Name <span class="text-danger">*</span></label>
                     <input type="text" name="arcand_name" id="add-cand-arname-det" class="form-control" value="{{ $post->arcand_name }}" placeholder="Enter candidate name...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-marital-status-det">Marital Status <span class="text-danger">*</span></label>
                     <select name="marital_status" id="add-marital-status-det" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="Unmarried" @if($post->marital_status == 'Unmarried') selected @endif>Unmarried</option>
                        <option value="Married" @if($post->marital_status == 'Married') selected @endif>Married</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="add-education-id" class="form-label">Education</label>
                     <select name="education_id" id="add-education-id" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($educations as $education)
                        <option value="{{ $education->id }}" @if($post->education_id == $education->id) selected @endif>{{ $education->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-regionp">Region</label>
                     <select name="region_id" id="add-regionp" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($regions as $region)
                        <option value="{{ $region->id }}" @if($region->id == $post->region_id) selected @endif>{{ $region->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-cand-cityp">City</label>
                     {{-- 
                     <select name="candcity_id" id="add-cand-cityp" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($cities as $candcity)
                        <option value="{{ $candcity->id }}" @if($candcity->id == $post->candcity_id) @selected(true) @endif>{{ $candcity->name }}</option>
                        @endforeach
                     </select>
                     --}}
                     <input type="text" name="candcity_text" id="add-cand-cityp" class="form-control cityTypehead" value="{{ $post->candcity_text }}" placeholder="Type city here...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-religionp-id">Religion</label>
                     <select name="religion_id" id="add-religionp-id" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        {{-- <option value="Muslim" @if($post->religion == 'Muslim') selected @endif>Muslim</option>
                        <option value="Hindu" @if($post->religion == 'Hindu') selected @endif>Hindu</option>
                        <option value="Christian" @if($post->religion == 'Christian') selected @endif>Christian</option>
                        <option value="Sikh" @if($post->religion == 'Sikh') selected @endif>Sikh</option> --}}
                        @foreach ($religions as $religion2)
                        <option value="{{ $religion2->id }}" @if($religion2->id == $post->religion_id) selected @endif>{{ $religion2->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-language-knownp">Language Known</label>
                     <select name="lang_known[]" id="add-language-knownp" class="form-select select2" multiple>
                     <option value="English" @if(in_array('English',$langsk)) selected @endif>English</option>
                     <option value="Hindi" @if(in_array('Hindi',$langsk)) selected @endif>Hindi</option>
                     <option value="Urdu" @if(in_array('Urdu',$langsk)) selected @endif>Urdu</option>
                     <option value="Arabic" @if(in_array('Arabic',$langsk)) selected @endif>Arabic</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="form-label" for="add-occupation-det">Applied for, Occupation</label>
                     <select name="jobtype_id" id="add-occupation-det" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($jobtypes as $jobtype)
                        <option value="{{ $jobtype->id }}" @if($post->jobtype_id == $jobtype->id) selected @endif>{{ $jobtype->eng_name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-expected-salp">Expected Salary <span class="text-danger">*</span></label>
                     <input type="text" name="exp_sal" id="add-expected-salp" value="{{ $post->exp_sal }}" class="form-control" placeholder="Enter expected salary...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-expect-wkpp">Expected Work Place</label>
                     <select name="expwp_id[]" id="add-expect-wkpp" class="form-select select2" data-allow-clear="true" multiple>
                        <option value="">Select</option>
                        @foreach ($expworklocs as $expwkpc)
                        <option value="{{ $expwkpc->id }}" @if(in_array($expwkpc->id,$expwp)) selected @endif>{{ $expwkpc->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="form-label" for="add-embassy-det">Embassy For</label>
                     <select name="embassy" id="add-embassy-det" class="form-select select2" data-allow-clear="true">
                        <option value="">Select Embassy</option>
                        @foreach ($embassies as $embassy) 
                        <option value="{{ $embassy->id}}" @if($post->embassy_for == $embassy->id) selected @endif>{{ $embassy->embassy }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="add-p-age" class="form-label">Age <span class="text-danger">*</span></label>
                     <input type="text" name="age" id="add-p-age" value="{{ $post->age }}" class="form-control" placeholder="Enter age....">
                  </div>
               </div>
               --}}
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
         </form>
      </div>
   </div>
   <!-- Personal Edit Page End-->
   <!-- Passport Edit Page Start -->
   <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editPassport" aria-labelledby="editPassportLabel">
      <div class="offcanvas-header">
         <h5 id="editPassportLabel" class="offcanvas-title">Edit Passport</h5>
         <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
         <form class="add-new-user pt-0" id="editPassportForm" action="{{ route('admin.candidate.update.passport') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
               <input type="hidden" name="editID" value="{{ $post->id }}">
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-pass-no-pp">Pass No <span class="text-danger">*</span></label>
                     <input type="text" name="pass_no" id="add-pass-no-pp" class="form-control" value="{{ $post->pass_no }}" placeholder="Enter candidate name...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-pass-type-pp">Passport Type <span class="text-danger">*</span></label>
                     <select name="pass_type" id="add-pass-type-pp" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="ECNR" @if($post->pass_type == 'ECNR') selected @endif>ECNR</option>
                        <option value="ECR" @if($post->pass_type == 'ECR') selected @endif>ECR</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-dob-det">Date of Birth <span class="text-danger">*</span></label>
                     <input type="date" name="dob" id="add-dob-det" value="{{ $post->dob }}" class="form-control" placeholder="Enter date of birth...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-place-of-birth-det">Place of Birth</label>
                     {{-- 
                     <select name="plb_id" id="add-place-of-birth-det" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($cities as $cityplb)
                        <option value="{{ $cityplb->id }}" @if($cityplb->id == $post->plb_id) selected @endif>{{ $cityplb->name }}</option>
                        @endforeach
                     </select>
                     --}}
                     <input type="text" name="plb_text" id="add-place-of-birth-det" class="form-control cityTypehead" placeholder="Type city here..." value="{{ $post->plb_text }}">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-nationality-id-det">Nationality</label>
                     <select name="nation_id" id="add-nationality-id-det" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($countries as $nationid)
                        <option value="{{ $nationid->id }}" @if($nationid->id == $post->nation_id) selected @endif>{{ $nationid->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-doi-pp">Date of Issue <span class="text-danger">*</span></label>
                     <input type="date" name="doi" id="add-doi-pp" value="{{ $post->doi }}" class="form-control" placeholder="Enter date of issue...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-doe-pp">Date of Expiry <span class="text-danger">*</span></label>
                     <input type="date" name="doe" id="add-doe-pp" value="{{ $post->doe }}" class="form-control" placeholder="Enter date of expiry...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="form-label" for="add-place-of-issue-pp">Place of Issue</label>
                     {{-- 
                     <select name="poi" id="add-place-of-issue-pp" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($poiss as $pois)
                        <option value="{{ $pois->id }}" @if($pois->id == $post->poi) selected @endif>{{ $pois->name }}</option>
                        @endforeach
                     </select>
                     --}}
                     <input type="text" name="poi_text" id="add-place-of-issue-pp" class="form-control cityTypehead" value="{{ $post->poi_text }}" placeholder="Type city...">
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="mb-3">
                     <label class="form-label" for="add-address-det">Address</label>
                     <input type="text" class="form-control" id="add-address-det" value="{{ $post->address }}" name="address" placeholder="Enter address...">
                  </div>
               </div>
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
         </form>
      </div>
   </div>
   <!-- Passport Edit Page End-->
   <!-- Experience Edit Page Start -->
   <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editExperience" aria-labelledby="editExperienceLabel">
      <div class="offcanvas-header">
         <h5 id="editExperienceLabel" class="offcanvas-title">Edit Experience</h5>
         <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
         <form class="add-new-user pt-0" id="editExperienceForm" action="{{ route('admin.candidate.update.experience') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @php
            $experience = explode(",",$post->experience);
            $expconts = explode(",",$post->expcountry_id);
            $expcits = explode(",",$post->expcity_id);
            $proffsp = explode(",",$post->proff_id);
            $langsk = explode(",",$post->lang_known);
            $expwp = explode(",",$post->expwp_id);
            $carknow2 = explode(",",$post->carknown_id);
            $expcittext = explode(",",$post->expcity_id_text);
            // dd($expcittext);
            $iexp = 0;
            @endphp
            <input type="hidden" name="editID" value="{{ $post->id }}">
            <div class="row">
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-gulfexperience-ex">Experience <span class="text-danger">*</span></label>
                     <select name="gulfexperience" id="add-gulfexperience-ex" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="1" @if($post->gulfexperience == 1) selected @endif>Indian Experience</option>
                        <option value="2" @if($post->gulfexperience == 2) selected @endif>Gulf Experience</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-car-known-ex">Car Known</label>
                     <select name="carknown_id[]" id="add-car-known-ex" class="form-select select2" multiple>
                     @foreach ($cars as $car2)
                     <option value="{{ $car2->id }}" @if(in_array($car2->id,$carknow2)) selected @endif>{{ $car2->name }}</option>
                     @endforeach
                     </select>
                  </div>
               </div>
            </div>
            @foreach ($experience as $index => $value )
            <div class="row">
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-exoinyear-ex{{ $index }}">Experience (in year) <span class="text-danger">*</span></label>
                     <input type="text" name="experience[]" id="add-exoinyear-ex{{ $index }}" value="{{ $value }}"  class="form-control" placeholder="Enter experience in year...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-job-type-ex{{ $index }}">Job Type</label>
                     <select name="proff_id[]" id="add-job-type-ex{{ $index }}" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($jobtypes as $jobtype)
                        <option value="{{ $jobtype->id }}" @if($jobtype->id == $proffsp[$index]) selected @endif>{{ $jobtype->eng_name.' ('.$jobtype->ar_name.')' }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-expcountry_id-ex{{ $index }}">Experience (country name)</label>
                     <select name="expcountry_id[]" id="add-expcountry_id-ex{{ $index }}" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($countries as $country)
                        <option value="{{ $country->id }}" @if($country->id == $expconts[$index]) selected @endif>{{ $country->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="form-label" for="add-expcity_id-ex{{ $index }}">City</label>
                     {{-- 
                     <select name="expcity_id[]" id="add-expcity_id-ex{{ $index }}" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($expworkcities as $city)
                        <option value="{{ $city->id }}" @if($city->id == $expcits[$index]) selected @endif>{{ $city->name }}</option>
                        @endforeach
                     </select>
                     --}}
                     <input type="text" name="expcity_id_text[]" id="add-expcity_id-ex{{ $index }}" @if (count($expcittext) > $iexp) value="{{ $expcittext[$index] }}" @endif class="form-control cityTypehead" placeholder="Type city here...">
                  </div>
               </div>
               @if ($index == 0)
               <div class="col-md-12">
                  <button type="button" class="btn btn-sm btn-primary float-end addExp2">Add</button>
               </div>
               @endif
               @if ($index != 0)
               <div class="col-md-12">
                  <button type="button" class="btn btn-sm btn-danger remove2 float-end">Remove</button>
               </div>
               @endif
            </div>
            <?php $iexp++ ?>
            @endforeach
            <div id="dispEXP2" class="dispEXP2">
            </div>
            <div class="row">
               <div class="col-md-6">
                  <label for="" class="form-label">Google Map</label>
                  <div class="mb-3">
                     <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="google_map" id="add-google-map-yes" @if($post->google_map == 1) checked @endif value="1"/>
                        <label class="form-check-label" for="add-google-map-yes">Yes</label>
                     </div>
                     <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="google_map" id="add-google-map-no" @if($post->google_map == 0) checked @endif value="0"/>
                        <label class="form-check-label" for="add-google-map-no">No</label>
                     </div>
                  </div>
               </div>
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
         </form>
      </div>
   </div>
   <!-- Experience Edit Page End-->
   <!-- Medical Edit Page Start -->
   <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editMedical" aria-labelledby="editMedicalLabel">
      <div class="offcanvas-header">
         <h5 id="editMedicalLabel" class="offcanvas-title">Edit Medical</h5>
         <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
         <form class="add-new-user pt-0" id="editMedicalForm" action="{{ route('admin.candidate.update.medical') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
               <input type="hidden" name="editID" value="{{ $post->id }}">
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-medical-examine-date">Medical Examine Date <span class="text-danger">*</span></label>
                     <input type="text" name="medical_examine_date" id="add-medical-examine-date" value="{{ $post->medical_examine_date }}" class="form-control flatpickr-basic" placeholder="Enter date of birth...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-doi-medical-expiry-date">Medical Expiry Date <span class="text-danger">*</span></label>
                     <input type="text" name="medical_expiry_date" id="add-doi-medical-expiry-date" value="{{ $post->medical_expiry_date }}" class="form-control flatpickr-basic" placeholder="Enter date of issue...">
                  </div>
               </div>
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
         </form>
      </div>
   </div>
   <!-- Medical Edit Page End-->
   <!-- Musaned Edit Page Start-->
   <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editMusaned" aria-labelledby="editMusanedLabel">
      <div class="offcanvas-header">
         <h5 id="editMusanedLabel" class="offcanvas-title">Edit Musaned</h5>
         <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
         <form class="add-new-user pt-0" id="editMusanedForm" action="{{ route('admin.candidate.update.musaned') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
               <input type="hidden" name="editMusanedID" value="{{ $post->id }}">
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="musaned_status" class="form-label">Musaned Status<span class="text-danger">*</span></label>
                     <select name="musaned_status" id="musaned_status" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="1" @if($post->musaned_status == 1) selected @endif>Register</option>
                        <option value="2" @if($post->musaned_status == 2) selected @endif>Not Register</option>
                        <option value="3" @if($post->musaned_status == 3) selected @endif>Register at another office</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-musaned-registration-date">Musaned Registration Date <span class="text-danger">*</span></label>
                     <input type="date" name="musaned_reg_date" id="add-musaned-registration-date" value="{{ $post->musaned_reg_date }}" class="form-control" placeholder="Enter musaned registration date...">
                  </div>
               </div>
               <div class="col-md-6 musCopyDisp">
                  <div class="mb-3">
                     <label class="form-label" for="add-musaned-copy">Musaned copy</label>
                     <input type="file" name="musaned_file" id="add-musaned-copy" class="form-control musnaedfile">
                  </div>
               </div>
               <div class="col-md-6 musCopyDisp">
                  <div class="mb-3">
                     @if ($post->musaned_file != '')
                     <img src="{{ asset('admin/assets/images/candidate/'.$post->musaned_file) }}" alt="musaned_file" class="d-block w-px-100 h-px-100 rounded" id="addmusanedcopy">
                     @else
                     <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="musaned_file" class="d-block w-px-100 h-px-100 rounded" id="addmusanedcopy">
                     @endif
                  </div>
               </div>
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
         </form>
      </div>
   </div>
   <!-- Musaned Edit Page End-->

   <!-- Candidate Edit Page Start -->
   <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editDetails" aria-labelledby="editDetailsLabel">
      <div class="offcanvas-header">
         <h5 id="editDetailsLabel" class="offcanvas-title">Edit Details</h5>
         <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
         <form class="add-new-user pt-0" id="editDetailsForm" action="{{ route('admin.candidate.update.details') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @php
            $langsk = explode(",",$post->lang_known);
            // dd($langsk);
            @endphp
            <div class="row">
               <input type="hidden" name="editID" value="{{ $post->id }}">
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-cand-name-det">Name <span class="text-danger">*</span></label>
                     <input type="text" name="cand_name" id="add-cand-name-det" class="form-control" value="{{ $post->cand_name }}" placeholder="Enter candidate name...">
                  </div>
               </div>
               --}}
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-pass-no-det">Pass No <span class="text-danger">*</span></label>
                     <input type="text" name="pass_no" id="add-pass-no-det" class="form-control" value="{{ $post->pass_no }}" placeholder="Enter candidate name...">
                  </div>
               </div>
               --}}
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-pass-type-det">Passport Type <span class="text-danger">*</span></label>
                     <select name="pass_type" id="add-pass-type-det" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="ECNR" @if($post->pass_type == 'ECNR') selected @endif>ECNR</option>
                        <option value="ECR" @if($post->pass_type == 'ECR') selected @endif>ECR</option>
                     </select>
                  </div>
               </div>
               --}}
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-dob-det">Date of Birth <span class="text-danger">*</span></label>
                     <input type="text" name="dob" id="add-dob-det" value="{{ $post->dob }}" class="form-control flatpickr-basic" placeholder="Enter date of birth...">
                  </div>
               </div>
               --}}
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-doi-det">Date of Issue <span class="text-danger">*</span></label>
                     <input type="text" name="doi" id="add-doi-det" value="{{ $post->doi }}" class="form-control flatpickr-basic" placeholder="Enter date of issue...">
                  </div>
               </div>
               --}}
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-doe-det">Date of Expiry <span class="text-danger">*</span></label>
                     <input type="text" name="doe" id="add-doe-det" value="{{ $post->doe }}" class="form-control flatpickr-basic" placeholder="Enter date of expiry...">
                  </div>
               </div>
               --}}
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="form-label" for="add-place-of-issue-det">Place of Issue</label>
                     <select name="poi" id="add-place-of-issue-det" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($poiss as $pois)
                        <option value="{{ $pois->id }}" @if($pois->id == $post->poi) selected @endif>{{ $pois->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               --}}
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-place-of-birth-det">Place of Birth</label>
                     <select name="plb_id" id="add-place-of-birth-det" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($cities as $cityplb)
                        <option value="{{ $cityplb->id }}" @if($cityplb->id == $post->plb_id) selected @endif>{{ $cityplb->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               --}}
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-nationality-id-det">Nationality</label>
                     <select name="nation_id" id="add-nationality-id-det" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($countries as $nationid)
                        <option value="{{ $nationid->id }}" @if($nationid->id == $post->nation_id) selected @endif>{{ $nationid->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               --}}
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-marital-status-det">Marital Status <span class="text-danger">*</span></label>
                     <select name="marital_status" id="add-marital-status-det" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="Unmarried" @if($post->marital_status == 'Unmarried') selected @endif>Unmarried</option>
                        <option value="Married" @if($post->marital_status == 'Married') selected @endif>Married</option>
                     </select>
                  </div>
               </div>
               --}}
               <div class="col-md-4">
                  <div class="mb-3">
                     <label class="form-label" for="add-mob-number-det">Mobile Number</label>
                     <input type="text" name="mobile_no" value="{{ $post->mobile_no }}" id="add-mob-number-det" class="form-control" placeholder="Enter mobile number...">
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="mb-3">
                     <label class="form-label" for="add-contact-number-det">Family Contact Number</label>
                     <input type="text" name="contact_no" id="add-contact-number-det" value="{{ $post->contact_no }}" class="form-control" placeholder="Enter contact number...">
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="mb-3">
                     <label for="add-email">Email</label>
                     <input type="text" name="email" id="add-email" class="form-control" value="{{ $post->email }}" placeholder="Enter email...">
                  </div>
               </div>
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="form-label" for="add-occupation-det">Applied for, Occupation</label>
                     <select name="jobtype_id" id="add-occupation-det" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($jobtypes as $jobtype)
                        <option value="{{ $jobtype->id }}" @if($post->jobtype_id == $jobtype->id) selected @endif>{{ $jobtype->eng_name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               --}}
               {{-- 
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-address-det">Address</label>
                     <input type="text" class="form-control" id="add-address-det" value="{{ $post->address }}" name="address" placeholder="Enter address...">
                  </div>
               </div>
               --}}
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
         </form>
      </div>
   </div>
   <!-- Candidate Edit Page End-->
   <!-- Update Published Status Start -->
   <div class="modal fade" id="updatePublished" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel1">Update Published Status</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.candidate.published.update',$post->id) }}" method="POST" enctype="multipart/form-data">
               @csrf
               <div class="modal-body">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="mb-3">
                           <div class="form-check form-check-inline mt-3">
                              <input class="form-check-input" type="radio" name="publish" id="published" @if($post->publish == 1) checked @endif value="1"/>
                              <label class="form-check-label" for="published">Publish</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="publish" id="unpublished" @if($post->publish == 0) checked @endif value="0"/>
                              <label class="form-check-label" for="unpublished">Unpublish</label>
                           </div>
                        </div>
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
   <!-- Add Service Charge Page Start -->
   <div class="offcanvas offcanvas-end" tabindex="-1" id="addServiceCharge" aria-labelledby="addServiceChargeLabel">
      <div class="offcanvas-header">
         <h5 id="addServiceChargeLabel" class="offcanvas-title">Add Service Charge</h5>
         <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
         <form class="add-new-user pt-0" id="addServiceChargeForm" action="{{ route('admin.candscharge',$post->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
               <div class="col-md-12">
                  <div class="mb-3">
                     <label for="" class="form-label">Given By <span class="text-danger">*</span></label>
                     <select name="given_by" id="given_by" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12">
                  <div class="mb-3">
                     <label class="form-label" for="add-amount">Service Charge</label>
                     <input type="text" name="amount" id="add-amount" value="" class="form-control" placeholder="Enter amount...">
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12">
                  <div class="mb-3">
                     <label for="" class="form-label">Status <span class="text-danger">*</span></label>
                     <select name="status" id="status" class="form-select select2" data-allow-clear="true">
                        <option value="">Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Deactive</option>
                     </select>
                  </div>
               </div>
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
         </form>
      </div>
   </div>
   <!-- end Service Charge Page End-->
   <!-- Update Service Charge Page Start -->
   <div class="offcanvas offcanvas-end" tabindex="-1" id="UpdateServiceCharge" aria-labelledby="UpdateServiceChargeLabel">
      <div class="offcanvas-header">
         <h5 id="UpdateServiceChargeLabel" class="offcanvas-title">Update Service Charge</h5>
         <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
         <form class="add-new-user pt-0" id="UpdateServiceChargeForm" action="{{ route('admin.candscharge',$post->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
               <input type="hidden" name="cand_id" id="cand_id">
               <div class="col-md-12">
                  <div class="mb-3">
                     <label for="edit-given_by" class="form-label">Given By <span class="text-danger">*</span></label>
                     <select name="given_by" id="edit-given_by" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12">
                  <div class="mb-3">
                     <label class="form-label" for="edit-amount">Service Charge</label>
                     <input type="text" name="amount" id="edit-amount" value="" class="form-control" placeholder="Enter amount...">
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12">
                  <div class="mb-3">
                     <label for="edit-status" class="form-label">Status <span class="text-danger">*</span></label>
                     <select name="status" id="edit-status" class="form-select select2" data-allow-clear="true">
                        <option value="">Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Deactive</option>
                     </select>
                  </div>
               </div>
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
         </form>
      </div>
   </div>
   <!-- End Update Service Charge Page End-->
   <!-- Add Payment Page Start -->
   <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="addCandPayment" aria-labelledby="addCandPaymentLabel">
      <div class="offcanvas-header">
         <h5 id="addCandPaymentLabel" class="offcanvas-title">Add Payment</h5>
         <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
         <form class="add-new-user pt-0" id="addCandPaymentForm" action="{{ route('admin.candamtStr',$post->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="add-amount">Amount <span class="text-danger">*</span></label>
                     <input type="text" name="amount" id="add-amount" class="form-control" placeholder="Enter amount...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="add-payment-mode" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                     <select name="payment_mode" id="add-payment-mode" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="netbanking">Net Banking</option>
                        <option value="upi">UPI</option>
                        <option value="cash">Cash</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="add-txn-id" class="form-label">Transaction ID</label>
                     <input type="text" name="txn_id" id="add-txn-id" class="form-control" placeholder="Enter Transaction number...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="add-bank-to" class="form-label">Bank To <span class="text-danger">*</span></label>
                     <select name="bank_to" id="add-bank-to" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="Khurshid_Khan">Khurshid Khan</option>
                        <option value="Juned_Khan">Juned Khan</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="add-payment-slip-file" class="form-label">Payment Slip</label>
                     <input class="form-control" name="payment_slip" type="file" id="add-payment-slip-file" />
                  </div>
               </div>
               <div class="col-md-6">
                  <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar"/>
               </div>
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
         </form>
      </div>
   </div>
   <!-- Add Payment Page End-->
   <!-- Edit Payment Page Start -->
   <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editCandPayment" aria-labelledby="editCandPaymentLabel">
      <div class="offcanvas-header">
         <h5 id="editCandPaymentLabel" class="offcanvas-title">Update Payment</h5>
         <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
         <form class="add-new-user pt-0" id="editCandPaymentForm" action="{{ route('admin.candamtUpdt') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
               <input type="hidden" name="paycand_id" id="paycand_id">
               <div class="col-md-6">
                  <div class="mb-3">
                     <label class="form-label" for="edit-amount">Amount<span class="text-danger">*</span></label>
                     <input type="text" name="amount" id="edit-amount" class="form-control" placeholder="Enter amount...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="edit-payment-mode" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                     <select name="payment_mode" id="edit-payment-mode" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="netbanking">Net Banking</option>
                        <option value="upi">UPI</option>
                        <option value="cash">Cash</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="edit-txn-id" class="form-label">Transaction ID</label>
                     <input type="text" name="txn_id" id="edit-txn-id" class="form-control" placeholder="Enter Transaction number...">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="edit-bank-to" class="form-label">Bank To <span class="text-danger">*</span></label>
                     <select name="bank_to" id="edit-bank-to" class="form-select select2" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="Khurshid_Khan">Khurshid Khan</option>
                        <option value="Juned_Khan">Juned Khan</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3">
                     <label for="edit-payment-slip-file" class="form-label">Payment Slip</label>
                     <input class="form-control edit-payment-slip-file" name="payment_slip" type="file" id="edit-payment-slip-file" />
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mb-3" id="dispIMG">
                     <img alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2"/>
                  </div>
               </div>
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
         </form>
      </div>
   </div>
   <!-- Edit Payment Page End-->
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
{{-- <script src="{{ asset('admin/assets/js/forms-selects.js') }}"></script> --}}
<script src="{{ asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>
<script src="{{ asset('admin/assets/pages/validation/candidate-validation.js') }}"></script>
<script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
<script>
   var ecount = {{ count($experience) }};
   var rowMin = ecount + 1;
   var rowMax = 5 - ecount;
   
   $(document).on('click','.addExp',function(){
       var html = '';
       html += '<div class="row"><div class="col-md-6"><div class="mb-3">';
       html += '<label class="form-label" for="edit-exoinyear'+rowMin+'">Experience (in year) <span class="text-danger">*</span></label>';
       html += '<input type="text" name="experience[]" id="edit-exoinyear'+rowMin+'" class="form-control" placeholder="Enter experience in year...">';
       html += '</div></div>';
   
       html += '<div class="col-md-6"><div class="mb-3">';
       html += '<label class="form-label" for="edit-job-type'+rowMin+'">Job Type</label>';
       html += '<select name="proff_id[]" id="edit-job-type'+rowMin+'" class="form-select select2"><option value="">Select</option>';
       html += '@foreach ($jobtypes as $jobtype)<option value="{{ $jobtype->id }}">{{ $jobtype->eng_name.' ('.$jobtype->ar_name.')' }}</option>@endforeach';
       html += '</select></div></div>';
   
       html += '<div class="col-md-6"><div class="mb-3">';
       html += '<label class="form-label" for="edit-expcountry_id'+rowMin+'">Experience (country name)</label>';
       html += '<select name="expcountry_id[]" id="edit-expcountry_id'+rowMin+'" class="form-select select2">';
       html += '<option value="">Select</option>@foreach ($countries as $country)<option value="{{ $country->id }}">{{ $country->name }}</option>@endforeach';
       html += '</select></div></div>';
   
       // html += '<div class="col-md-6"><div class="mb-3">';
       // html += '<label for="form-label" for="edit-expcity_id'+rowMin+'">City</label>';
       // html += '<select name="expcity_id[]" id="edit-expcity_id'+rowMin+'" class="form-select select2"><option value="">Select</option>';
       // html += '@foreach ($cities as $city)<option value="{{ $city->id }}">{{ $city->name }}</option>@endforeach';
       // html += '</select></div></div>';
   
       html += '<div class="col-md-6"><div class="mb-3">';
       html += '<label for="form-label" for="edit-expcity_id-ex'+rowMin+'">City</label>';
       html += '<input type="text" name="expcity_id_text[]" id=edit-expcity_id-ex'+rowMin+'" class="form-control cityTypehead" placeholder="Type city here..."></div>';
       
   
       html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end">Remove</button></div>';
   
       $('#dispEXP').append(html);
       rowMin++;
       rowMax--;
       if (rowMax == 0) {
           $('.addExp').prop('disabled',true);
       }else{
           $('.addExp').prop('disabled',false);
       }
   
   
   });
   
   $(document).on('click','.remove',function(){
       $(this).closest('.row').remove();
       rowMax++;
       rowMin--;
       if (rowMax == 0) {
           $('.addExp').prop('disabled',true);
       }else{
           $('.addExp').prop('disabled',false);
       }
   });
   
</script>
<script>
   var ecount2 = {{ count($experience) }};
   var rowMin2 = ecount2 + 1;
   var rowMax2 = 5 - ecount2;
   
   $(document).on('click','.addExp2',function(){
       var html2 = '';
       html2 += '<div class="row"><div class="col-md-6"><div class="mb-3">';
       html2 += '<label class="form-label" for="edit-exoinyear-ex'+rowMin2+'">Experience (in year) <span class="text-danger">*</span></label>';
       html2 += '<input type="text" name="experience[]" id="edit-exoinyear-ex'+rowMin2+'" class="form-control" placeholder="Enter experience in year...">';
       html2 += '</div></div>';
   
       html2 += '<div class="col-md-6"><div class="mb-3">';
       html2 += '<label class="form-label" for="edit-job-type-ex'+rowMin2+'">Job Type</label>';
       html2 += '<select name="proff_id[]" id="edit-job-type-ex'+rowMin2+'" class="form-select select2"><option value="">Select</option>';
       html2 += '@foreach ($jobtypes as $jobtype)<option value="{{ $jobtype->id }}">{{ $jobtype->eng_name.' ('.$jobtype->ar_name.')' }}</option>@endforeach';
       html2 += '</select></div></div>';
   
       html2 += '<div class="col-md-6"><div class="mb-3">';
       html2 += '<label class="form-label" for="edit-expcountry_id-ex'+rowMin2+'">Experience (country name)</label>';
       html2 += '<select name="expcountry_id[]" id="edit-expcountry_id-ex'+rowMin2+'" class="form-select select2">';
       html2 += '<option value="">Select</option>@foreach ($countries as $country)<option value="{{ $country->id }}">{{ $country->name }}</option>@endforeach';
       html2 += '</select></div></div>';
   
       // html2 += '<div class="col-md-6"><div class="mb-3">';
       // html2 += '<label for="form-label" for="edit-expcity_id-ex'+rowMin2+'">City</label>';
       // html2 += '<select name="expcity_id[]" id="edit-expcity_id-ex'+rowMin2+'" class="form-select select2"><option value="">Select</option>';
       // html2 += '@foreach ($cities as $city)<option value="{{ $city->id }}">{{ $city->name }}</option>@endforeach';
       // html2 += '</select></div></div>';
   
       html2 += '<div class="col-md-6"><div class="mb-3">';
       html2 += '<label for="form-label" for="edit-expcity_id-ex'+rowMin2+'">City</label>';
       html2 += '<input type="text" name="expcity_id_text[]" id=edit-expcity_id-ex'+rowMin2+'" class="form-control cityTypehead" placeholder="Type city here..."></div>';
       html2 += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove2 float-end">Remove</button></div>';
   
       $('#dispEXP2').append(html2);
       rowMin2++;
       rowMax2--;
       if (rowMax2 == 0) {
           $('.addExp2').prop('disabled',true);
       }else{
           $('.addExp2').prop('disabled',false);
       }
   
   
   });
   
   $(document).on('click','.remove2',function(){
       $(this).closest('.row').remove();
       rowMax2++;
       rowMin2--;
       if (rowMax2 == 0) {
           $('.addExp2').prop('disabled',true);
       }else{
           $('.addExp2').prop('disabled',false);
       }
   });
   
   
   $(document).on('input',function(){
       var textValue = $(this).val();
       // console.log(textValue);
   
       var substringMatcher2 = function (strs2) {
           return function findMatches(q2, cb2) {
               var matches2, substrRegex2;
               matches2 = [];
               substrRegex2 = new RegExp(q2, 'i');
               $.each(strs2, function (i, str2) {
                   if (substrRegex2.test(str2)) {
                       matches2.push(str2);
                   }
               });
   
               cb2(matches2);
           };
       };
   
       var cities2 = ['Mumbai','Pune'];
   
       $('.texttest').typeahead(
           {
               hint: true,
               highlight: true,
               minLength: 1
           },
           {
               name: 'cities2',
               source: substringMatcher2(cities2)
           }
       );
   
   
   });
   
</script>
<script>
   $(document).ready(function(){
       $('#publishedSts').on('click',function(){
           var cand_id = {{ $post->id }};
   
           Swal.fire({
               title: 'Are you sure?',
               text: "to publish candidate!",
               icon: 'success',
               showCancelButton: true,
               confirmButtonText: 'Yes, publish it!',
               customClass: {
                   confirmButton: 'btn btn-primary me-3',
                   cancelButton: 'btn btn-label-secondary'
               },
               buttonsStyling: false
           }).then(function (result) {
               if (result.value) {
   
                   jQuery.ajax({
                       url:"{{ url('admin/candidate/published/update') }}",
                       method: 'post',
                       type: 'html',
                       data: {
                           "_token": "{{ csrf_token() }}",
                           cand_id: cand_id,
                           
                       },
                       success: function(data){
                           Swal.fire({
                               icon: 'success',
                               title: 'Published!',
                               text: 'Your file has been published.',
                               customClass: {
                                   confirmButton: 'btn btn-success'
                               }
                           });
                       }
                   });                        
               }
           });
       });
   });
</script>
<script>
   $(document).ready(function(){
       $('.delcandamt').on('click',function(e){
           var paycand_id2 = $(this).data('id');
   
           // alert(paycand_id2);
   
           Swal.fire({
               title: 'Are you sure?',
               text: 'To delete payment detail!',
               icon: 'success',
               showCancelButton: true,
               confirmButtonText: 'Yes, delete it!',
               customClass: {
                   confirmButton: 'btn btn-primary me-3',
                   cancelButton: 'btn btn-label-secondary'
               },
               buttonsStyling: false
           }).then(function(result){
               if (result.value) {
                   jQuery.ajax({
                       url:"{{ url('admin/candidate/candamt/delete') }}",
                       method: 'post',
                       type: 'html',
                       data: {
                           "_token": "{{ csrf_token() }}",
                           id: paycand_id2,
                           
                       },
                       success: function(data){
                           Swal.fire({
                               icon: 'success',
                               title: 'Delete!',
                               text: 'Your file has been deleted.',
                               customClass: {
                                   confirmButton: 'btn btn-success'
                               }
                           }).then(function(result){
                               location.reload();
                           });
                       }
                   });
               }
           });
       });
   });
</script>
<script>
   $(document).ready(function(){
       $('.servdelcandamt').on('click',function(e){
           var cand_id2 = $(this).data('id');
   
           // alert(paycand_id2);
   
           Swal.fire({
               title: 'Are you sure?',
               text: 'To delete Service detail!',
               icon: 'success',
               showCancelButton: true,
               confirmButtonText: 'Yes, delete it!',
               customClass: {
                   confirmButton: 'btn btn-primary me-3',
                   cancelButton: 'btn btn-label-secondary'
               },
               buttonsStyling: false
           }).then(function(result){
               if (result.value) {
                   jQuery.ajax({
                       url:"{{ url('admin/candidate/service/delete') }}",
                       method: 'post',
                       type: 'html',
                       data: {
                           "_token": "{{ csrf_token() }}",
                           id: cand_id2,
                           
                       },
                       success: function(data){
                           Swal.fire({
                               icon: 'success',
                               title: 'Delete!',
                               text: 'Your file has been deleted.',
                               customClass: {
                                   confirmButton: 'btn btn-success'
                               }
                           }).then(function(result){
                               location.reload();
                           });
                       }
                   });
               }
           });
       });
   });
</script>
{{--Start Temporary Remove 10072023 --}}
{{-- <script>
   $(document).ready(function(){
       $('#downloadCandCV2').on('click',function(){
           var partnerID = $('#partner_id').val();
           var candcv_id = {{ $post->id }};
   
           if (partnerID != '') {
               var cvUrl = '{{ url("admin/download/candidate-cv") }}/'+partnerID+'/'+candcv_id;
               window.open(cvUrl,'_blank');
               $('#errorPartner').text('');
               $('#downloadCandCV2').attr('disabled',false);
           }else{
               $('#errorPartner').text('Please select office name!');
               $('#downloadCandCV2').attr('disabled',true);
           }
       });
   
       $('#partner_id').on('change',function(){
           var pID = $(this).val();
           if (pID != '') {
               $('#errorPartner').text('');
               $('#downloadCandCV2').attr('disabled',false);
           } else {
               $('#errorPartner').text('Please select office name!');
               $('#downloadCandCV2').attr('disabled',true);
           }
       });
   });
</script> --}}
{{--End Temporary Remove 10072023 --}}
<script>
   $(document).ready(function(){
       $('#cvexecuteerro').on('click',function(){
           toastr.options = {
               "timeOut": 5000,
               "showDuration": 300,
               "showEasing": "swing",
               "hideEasing": "linear",
               "showMethod": "fadeIn",
               "hideMethod": "fadeOut",
           };
   
           toastr.error('Cv is not execute due incomplete candidate details!');
   
       });
   });
</script>
<script>
   $(document).ready(function(){
       $('#cvexecuteerro2').on('click',function(){
           toastr.options = {
               "timeOut": 5000,
               "showDuration": 300,
               "showEasing": "swing",
               "hideEasing": "linear",
               "showMethod": "fadeIn",
               "hideMethod": "fadeOut",
           };
   
           toastr.error('Cv is not execute due incomplete candidate details!');
       });
   });
</script>
<script>
   $(document).ready(function(){
       $('#errorDownload1').on('click',function(){
           toastr.options = {
               "timeOut": 5000,
               "showDuration": 300,
               "showEasing": "swing",
               "hideEasing": "linear",
               "showMethod": "fadeIn",
               "hideMethod": "fadeOut",
           };
   
           toastr.error('Cv is not execute!');
   
       });
   });
</script>
<script>
   $(document).ready(function(){
       $('#errorDownload2').on('click',function(){
           toastr.options = {
               "timeOut": 5000,
               "showDuration": 300,
               "showEasing": "swing",
               "hideEasing": "linear",
               "showMethod": "fadeIn",
               "hideMethod": "fadeOut",
           };
   
           toastr.error('Cv is not execute!');
   
       });
   
       $('#paymentError').on('click',function(){
           toastr.options = {
               "timeOut": 5000,
               "showDuration": 300,
               "showEasing": "swing",
               "hideEasing": "linear",
               "showMethod": "fadeIn",
               "hideMethod": "fadeOut",
           };
   
           toastr.error('Service charge is not updated!');
       });
   
   });
</script>
<script>
   $(document).ready(function(){
       $('#editCandPayment').on('show.bs.offcanvas',function(e){
           var paycand_id = $(e.relatedTarget).data('id');
   
           var imgPath = "{{ asset('admin/assets/images/payment/candidate') }}";
           var blankImg = "{{ asset('admin/assets/img/avatars/blank.jpeg') }}";
   
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
   
           jQuery.ajax({
               url : '{{ url("admin/candidate/addpayment/edit") }}',
               method: "POST",
               type: "html",
               data: {
                   "id": paycand_id,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   $('#paycand_id').val(data.id);
                   $('#edit-amount').val(data.amount);
                   $('#edit-payment-mode').val(data.payment_mode).change();
                   $('#edit-txn-id').val(data.txn_id);
                   $('#edit-bank-to').val(data.bank_to).change();
   
                   if (data.payment_slip != '') {
                       var file_path = imgPath+'/'+data.payment_slip;
                       $('.uploadedAvatar2').attr("src",file_path);
                   } else {
                       // var file_path = blankImg+'/blank.jpeg';
                       $('.uploadedAvatar2').attr("src",blankImg);
                   }
   
                   // var html = "";
                   // if (data.payment_slip != '') {
                   //     var file_path = imgPath+'/'+data.payment_slip;
                   //     html += '<img src="'+file_path+'" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2">';
                   // } else {    
                   //     // var file_path = blankImg+'/blank.jpeg';
                   //     html += '<img src="'+blankImg+'" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2">';
                   // }
   
                   // $('#dispIMG').html(html);
               }
           });
       });
   });
   
</script>
<script>
   $(document).ready(function(){
       $('#UpdateServiceCharge').on('show.bs.offcanvas',function(e){
           var cand_id = $(e.relatedTarget).data('id');
   
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
   
           jQuery.ajax({
               url : '{{ url("admin/candidate/service/edit") }}',
               method: "POST",
               type: "html",
               data: {
                   "id": cand_id,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   $('#cand_id').val(data.id);
                   $('#edit-given_by').val(data.given_by);
                   $('#edit-amount').val(data.amount).change();
                   $('#edit-status').val(data.status).change();
   
                  
                   // var html = "";
                   // if (data.payment_slip != '') {
                   //     var file_path = imgPath+'/'+data.payment_slip;
                   //     html += '<img src="'+file_path+'" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2">';
                   // } else {    
                   //     // var file_path = blankImg+'/blank.jpeg';
                   //     html += '<img src="'+blankImg+'" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2">';
                   // }
   
                   // $('#dispIMG').html(html);
               }
           });
       });
   });
   
</script>
{{-- <script>
   $(document).ready(function(){
       $('#embassy').on('change',function(){
           var statusval = $(this).val();
           if (statusval == 1) {
               $('#musDisp').show();
               $('.musCopyDisp').show();
           } else {
               $('#musDisp').hide();
               $('.musCopyDisp').hide();
           }
       });
   });
</script> --}}
<script>
   // Activate Bootstrap Tabs
   var linkTabs = new bootstrap.Tab(document.getElementById('link-tab'));
   linkTabs.show();
   
   var testVideoTabs = new bootstrap.Tab(document.getElementById('test_video-tab'));
   testVideoTabs.show();
   
   // Switch tabs on click
   $('#linkTabs a').on('click', function (e) {
       e.preventDefault();
       $(this).tab('show');
   });
   
   $('#testVideoTabs a').on('click', function (e) {
       e.preventDefault();
       $(this).tab('show');
   });
</script>
<!--   <script>
   // Get the time the button was displayed from the server
   var displayTime = new Date("{{ session('button_display_time') }}").getTime();
   
   // Calculate the time difference
   var currentTime = new Date().getTime();
   var timeDifference = currentTime - displayTime;
   
   // Set the timeout for 10 minutes
   var timeout = 10 * 60 * 1000;
   
   // Hide the button if 10 minutes have passed
   if (timeDifference > timeout) {
       document.getElementById('UpdateDeactive').style.display = 'none';
   }
   </script> -->
@endsection