  @extends('layouts/contentLayoutMaster')

  @section('title', 'Candidates List')

  @section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(('vendors/css/tables/datatable/buttons.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(('vendors/css/pickers/pickadate/pickadate.css')) }}">
    <link rel="stylesheet" href="{{ asset(('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(('vendors/css/extensions/toastr.min.css')) }}">
  @endsection

  @section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(('css/base/pages/app-user.css')) }}">
    <link rel="stylesheet" href="{{ asset(('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
    <link rel="stylesheet" href="{{ asset(('css/base/plugins/forms/pickers/form-pickadate.css')) }}">
    <link rel="stylesheet" href="{{ asset(('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <link rel="stylesheet" href="{{ asset('fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{asset('css/base/pages/ui-feather.css')}}">
    <style type="text/css">
      .iconDetails {
        margin-left:2%;
        float:left; 
        height:63px;
        width:40px; 
        color: white;
      }     

      .container2 {
        width:100%;
        height:45px;
        padding:1%;
        margin: 3px;
      }

      h4 {
        margin:5px;
      }
      h5 b {
        color: white;
      }
      .info-box-icon {
        border-radius: 0.25rem;
        -ms-flex-align: center;
        align-items: center;
        display: -ms-flexbox;
        display: flex;
        font-size: 1rem;
        -ms-flex-pack: center;
        justify-content: center;
        text-align: center;
        width: 34px;
        height: 34px;
        margin-top: 4px;
      }
      .info-box-content {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        -ms-flex-pack: center;
        justify-content: center;
        line-height: 1.8;
        -ms-flex: 1;
        flex: 1;
        padding: 0 10px;
        padding-top: 5px;
      }
      h5 b {
      color: #92969f;
      font-size: 0.912rem;
      }
    </style>
  @endsection

  @section('content')

    <section id="app-user-list">
      <div class="row match-height">
        <div class="col-xl-12 col-lg-12">
          <div class="card">
            <div class="card-body">
              <!-- Search Area Start -->
              <div class="container-fluid">
                <div class="row">
                  @php
                    $parties = \App\AdminModel\Candidate::select("pty_id")->groupBy('pty_id')->where('pty_id','!=','200')->get();
                    $stages = \App\AdminModel\CandidateStatus::select("active_status")->groupBy('active_status')->where('active_status','!=','')->get();
                    $incomps = \App\AdminModel\CandidateStatus::select("incomplete_status")->groupBy('incomplete_status')->where('incomplete_status','!=','')->get();
                    $finalstatus = \App\AdminModel\CandCompFinalStatus::select('final_status')->groupBy('final_status')->get();
                    $careofs = \App\AdminModel\Candidate::select('care_of_id')->groupBy('care_of_id')->where('care_of_id','!=','')->where('care_of_id','!=','200')->where('care_of_id','!=','None')->where('care_of_id','!=','0')->get();
                    $visa_dates = \App\AdminModel\ServiceDetails::select('visa_stamped_date')->groupBy('visa_stamped_date')->where('visa_stamped_date','!=','')->get();
                    $create_ats = \App\AdminModel\Candidate::select(DB::raw('DATE(created_at) as date'))->groupBy('date')->get();
                    $updated_ats = \App\AdminModel\Candidate::select(DB::raw('DATE(updated_at) as udate'))->groupBy('udate')->get();
                    $ser_ids = \App\AdminModel\Candidate::select('ser_id')->groupBy('ser_id')->where('ser_id','!=','')->get();
                    // dd($parties);
                  @endphp
                  <div class="col-md col-lg col-xl col-sm mt-1 party-div">
                    <select  id="by-party" class="form-control select2">
                      <option value="">Party</option>
                      @foreach ($parties as $party)
                        @php
                          $prt = \App\AdminModel\Party::where('pty_id','=',$party->pty_id)->where('pty_id','!=','200')->first();
                        @endphp
                        <option value="{{ $prt->pty_ag_name }}">{{ $prt->pty_ag_name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md col-lg col-xl col-sm mt-1 stage-div">
                    <select class="form-control select2 byStage" id="by-stage">
                      <option value="">Stage</option>
                      @foreach ($stages as $stage)
                        <option value="{{ $stage->active_status }}">{{ $stage->active_status }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md col-lg col-xl col-sm mt-1 incomplete-div">
                    <select class="form-control select2 byIncom" id="by-incom">
                      <option value="">Incomplete</option>
                      @foreach ($incomps as $incomp)
                          @foreach (explode(",",$incomp->incomplete_status) as $inc)
                            <option value="{{ $inc }}">{{ $inc }}</option>                            
                          @endforeach
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md col-lg col-xl col-sm mt-1 cstatus-div">
                    <select class="form-control select2 fstatus"  id="fstatus">
                      <option value="">Status</option>
                      @foreach ($finalstatus as $finals)
                          <option value="{{ $finals->final_status }}">{{ $finals->final_status }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md col-lg col-xl col-sm mt-1 care-of-div">
                    <select class="form-control select2" id="careofid">
                      <option value="">Care Of</option>
                      @foreach ($careofs as $careof)
                        @php
                            if ($careof->care_of_id != '200' || $careof->care_of_id != '0' || $careof->care_of_id != '' || $careof->care_of_id != 'None') {
                              $user = \App\User::where('user_id','=',$careof->care_of_id)->first();
                            }                            
                        @endphp
                        @if (isset($user) && $user != '')
                        <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md col-lg col-xl col-sm mt-1 service-type-div" style="display: none">
                    <select id="ser-ID" class="form-control select2">
                      <option value="">Service Type</option>
                      @foreach ($ser_ids as $ser_id)
                        @php
                            $service =  \App\AdminModel\EmployeeServiceType::where('ser_id','=',$ser_id->ser_id)->first();
                        @endphp
                        <option value="{{ $service->ser_id }}">{{ $service->ser_name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md col-lg col-xl col-sm mt-1 visa-stamped-date-div" style="display: none">
                    <select class="form-control select2" id="by-visa-date">
                      <option value="">Visa Stamped Date</option>
                      @foreach ($visa_dates as $visa_date)
                          <option value="{{ $visa_date->visa_stamped_date }}">{{ $visa_date->visa_stamped_date }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md col-lg col-xl col-sm mt-1 created-at-div" style="display: none">
                    <select class="form-control select2" id="by-create-date">
                      <option value="">Created At</option>
                      @foreach ($create_ats as $create_at)
                          <option value="{{ $create_at->date }}">{{ date('d-m-Y',strtotime($create_at->date)) }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md col-lg col-xl col-sm mt-1 updated-at-div" style="display: none">
                    <select class="form-control select2" id="by-date">
                      <option value="">Updated At</option>
                      @foreach ($updated_ats as $updated_at)
                      <option value="{{ $updated_at->udate }}">{{ date('d-m-Y',strtotime($updated_at->udate)) }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <!-- Search Area End -->
              <!-- Status Area Start -->
              <div class="container-fluid mt-3">
                <div class="row">
                  <div class=" col-md col-sm col-lg all-document-div" style="display: none">
                    <a href="">
                      <div class='card' style="word-wrap: normal;border:1px solid #95959a54;">
                        <div class='container2'>
                          <div>
                            <span class="info-box-icon bg-success iconDetails"> <i class="fas fa-file-alt fa-lg"></i></span>
                          </div>  
                          <div style='margin-left:60px;' class="info-box-content">
                            <h5 ><b>All DOCUMENT</b></h5>
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>
                  <div class=" col-md col-sm col-lg new-document-div" style="display: none">
                    <a href="#">
                      <div class='card' style="word-wrap: normal;border:1px solid #95959a54;">
                        <div class='container2' onclick="get_newdocument_list()">
                          <div>
                            <span class="info-box-icon bg-success iconDetails"> <i class="fas fa-file-alt fa-lg"></i></span>
                          </div>  
                          <div style='margin-left:60px;' class="info-box-content">
                            <h5 ><b>NEW DOCUMENT</b></h5>
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>
                  <!-- /.col -->
                  <div class=" col-md col-sm col-lg for-mofa-div" style="display: none">
                    <a href="#">
                      <div class='card' style="word-wrap: normal;border:1px solid #95959a54;">
                        <div class='container2' onclick="get_formofa_list()">
                          <div>
                            <span class="info-box-icon bg-info iconDetails"><i class="fas fa-users-cog fa-lg"></i></span>
                          </div>  
                          <div style='margin-left:60px;' class="info-box-content">
                            <h5><b>FOR MOFA</b></h5>
                            <div style="float:right;font-size:.6em" id="cnt_get_formofa_list"></div>
                          </div>
                        </div>
                      </div>
                      <!-- /.info-box -->
                    </a>
                  </div>
                  <div class=" col-md col-sm col-lg mofa-unpaid-div" style="display: none">
                    <a href="#"> 
                      <div class='card' style="word-wrap: normal;border:1px solid #95959a54;">
                        <div class='container2' onclick="get_mofaunpaid_list()">
                          <div>
                            <span class="info-box-icon bg-warning iconDetails">
                              <i class="fab fa-creative-commons-nc fa-lg"></i>
                            </span>
                          </div>  
                          <div style='margin-left:60px;' class="info-box-content">
                            <h5><b>MOFA UNPAID</b></h5>
                          </div>
                        </div>
                      </div>
                      <!-- /.info-box -->
                    </a>
                  </div>
                  <div class=" col-md col-sm col-lg awaited-passport-div" style="display: none">
                    <a href="#"> 
                      <div class='card' style="word-wrap: normal;border:1px solid #95959a54;">
                        <div class='container2' onclick="get_awaitedpassport_list()">
                          <div>
                            <span class="info-box-icon bg-info iconDetails"><i class="far fa-clock fa-lg"></i></span>
                          </div>  
                          <div style='margin-left:60px;' class="info-box-content">
                            <h5><b>AWAITED PASSPORT</b></h5>

                          </div>
                        </div>
                      </div>
                    </a>
                  </div>
                  <div class=" col-md col-sm col-lg medicle-div" style="display: none">
                    <a href="#"> 
                      <div class='card' style="word-wrap: normal;border:1px solid #95959a54;">
                        <div class='container2' onclick="get_medicle_list()">
                          <div>
                            <span class="info-box-icon iconDetails" style="background-color: #30475e;color: white;"><i class="fas fa-file-medical fa-lg"></i></span>
                          </div>  
                          <div style='margin-left:60px;' class="info-box-content">
                            <h5><b>MEDICLE</b></h5>
                          </div>
                        </div>
                      </div>
                      <!-- /.info-box -->
                    </a>
                  </div>
                  <div class=" col-md col-sm col-lg for-submission-div" style="display: none">
                    <a href="#"> 
                      <div class='card' style="word-wrap: normal;border:1px solid #95959a54;">
                        <div class='container2' onclick="get_tosubmission_list()">
                          <div>
                            <span class="info-box-icon bg-success iconDetails"><i class="far fa-paper-plane fa-lg"></i></span>
                          </div>  
                          <div style='margin-left:60px;' class="info-box-content">
                            <h5><b>FOR SUBMISSION</b></h5>

                            {{-- <div style="float:right;font-size:.6em">0 mins ago</div> --}}
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>
                  <div class=" col-md col-sm col-lg submission-ready-div" style="display: none">
                    <a href="#"> 
                      <div class='card' style="word-wrap: normal;border:1px solid #95959a54;">
                        <div class='container2' onclick="get_readysubmission_list()">
                          <div>
                            <span class="info-box-icon bg-success iconDetails"><i class="far fa-paper-plane fa-lg"></i></span>
                          </div>  
                          <div style='margin-left:60px;' class="info-box-content">
                            <h5><b>SUBMISSION READY(NEW)</b></h5>

                            {{-- <div style="float:right;font-size:.6em">0 mins ago</div> --}}
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>
                  <div class=" col-md col-sm col-lg hold-div" style="display: none">
                    <a href="#"> 
                      <div class='card' style="word-wrap: normal;border:1px solid #95959a54;">
                        <div class='container2' onclick="get_hold_list()">
                          <div>
                            <span class="info-box-icon bg-warning iconDetails"><i class="fas fa-pause-circle fa-lg"></i> </span>
                          </div>  
                          <div style='margin-left:60px;' class="info-box-content">
                            <h5><b>HOLD</b></h5>

                            {{-- <div style="float:right;font-size:.6em">0 mins ago</div> --}}
                          </div>
                        </div>
                      </div>
                      <!-- /.info-box -->
                    </a>
                  </div>
                  <div class=" col-md col-sm col-lg submitted-div" style="display: none">
                    <a href="#"> 
                      <div class='card' style="word-wrap: normal;border:1px solid #95959a54;">
                        <div class='container2' onclick="get_submitted_list()">
                          <div>
                            <span class="info-box-icon bg-danger iconDetails"><i class="far fa-list-alt fa-lg"></i></span>
                          </div>  
                          <div style='margin-left:60px;' class="info-box-content">
                            <h5><b>SUBMITTED</b></h5>

                            {{-- <div style="float:right;font-size:.6em">0 mins ago</div> --}}
                          </div>
                        </div>
                      </div>
                      <!-- /.info-box -->
                    </a>
                  </div>
                  <div class=" col-md col-sm col-lg rejection-div" style="display: none">
                    <a href="#"> 
                      <div class='card' style="word-wrap: normal;border:1px solid #95959a54;">
                        <div class='container2' onclick="get_rejection_list()">
                          <div>
                            <span class="info-box-icon iconDetails" style="background-color: #30475e;color: white;"><i class="far fa-registered fa-lg"></i></span>
                          </div>  
                          <div style='margin-left:60px;' class="info-box-content">
                            <h5><b>REJECTION</b></h5>
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>
                  <div class=" col-md col-sm col-lg emigration-applied-div" style="display: none">
                    <a href="#"> 
                      <div class='card' style="word-wrap: normal;border:1px solid #95959a54;">
                        <div class='container2' onclick="get_foremigration_list()">
                          <div>
                            <span class="info-box-icon bg-success iconDetails"><i class="far fa-registered fa-lg"></i></span>
                          </div>  
                          <div style='margin-left:60px;' class="info-box-content">
                            <h5><b>EMIGRATION APPLIED (NEW)</b></h5>
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>
                  <div class=" col-md col-sm col-lg done-div" style="display: none">
                    <a href="#"> 
                      <div class='card' style="word-wrap: normal;border:1px solid #95959a54;">
                        <div class='container2' onclick="get_done_list()">
                          <div>
                            <span class="info-box-icon bg-danger iconDetails"><i class="fas fa-user-graduate fa-lg"></i></span>
                          </div>  
                          <div style='margin-left:60px;' class="info-box-content">
                            <h5><b>DONE</b></h5>
                            {{-- <div style="float:right;font-size:.6em">0 mins ago</div> --}}
                          </div>
                        </div>
                      </div>
                      <!-- /.info-box -->
                    </a>
                  </div>
                  <div class="col-md col-sm col-lg cancelled-div" style="display: none">
                    <a href="#"> 
                      <div class='card' style="word-wrap: normal;border:1px solid #95959a54;">
                        <div class='container2' onclick="get_CANCEL_list()">
                          <div>
                            <span class="info-box-icon bg-danger iconDetails"><i class="fas fa-user-graduate fa-lg"></i></span>
                          </div>  
                          <div style='margin-left:60px;' class="info-box-content">
                            <h5><b>CANCELED(NEW)</b></h5>

                            {{-- <div style="float:right;font-size:.6em">0 mins ago</div> --}}
                          </div>
                        </div>
                      </div>
                      <!-- /.info-box -->
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>



    <section class="app-user-list">
      <h3 id="candidate_list_type" style="font-weight: 500;color: #7367f0;"></h3>
      <!-- users filter end -->
      <!-- list section start -->
      <div class="card">
        <div class="card-datatable table-responsive pt-0">
          <table class="user-list-table table">
            <thead class="thead-light">
              <tr>
                <th></th>
                <th>Candidate </th>
                <th>Passport</th>
                <th>File No</th>
                <th>Party</th>
                <th>mofa no</th>
                <th>Stage</th>
                <th>Mofa</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Actions</th>
              </tr>
            </thead>
          </table>
        </div>
        <!-- Modal to add new user starts-->
        <div class="modal modal-slide-in  new-user-modal fade" id="modals-slide-in" data-bs-backdrop="static" data-bs-keyboard="false">
          <div class="modal-dialog sidebar-lg">
            <form class="modal-content pt-0" id="jquery-val-form"  action="{{ url('services/candidate/store') }}" enctype="multipart/form-data" method="post">
              @csrf
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
              <div class="modal-header mb-1">
                <h5 class="modal-title" id="exampleModalLabel">New Candidate</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <input type="hidden" id="cand_id_edit" name="cand_id_edit">
                      <label>Party<span style="color: red;">*</span></label>
                      <?php
                        $partytbl = \App\AdminModel\Party::where('pty_id','!=','200')->where('act_status','=','0')->get();
                      ?>
                      <select class="form-control care_of_id select2"  style="width: 100%;"  name="pty_id" id="pty_id" required="" title="Select Party except None!">
                        <option value=""></option>
                        <option value="200">--None--</option>
                        <?php
                          if(isset($partytbl)){
                            foreach($partytbl as $party)
                            { ?>
                              <option value="<?php echo $party->pty_id;?>"<?php if(isset($cand_tbl)) if($party->pty_id==$cand_tbl->pty_id){ echo "selected='selected'"; } ?>><?php echo $party->pty_ag_name; ?></option>
                          <?php 
                            }?>
                      </select>
                      <?php }?>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label> Passport Status<span style="color: red;">*</span></label>
                      <select  onchange="show_div(this)" class="form-control select2" style="width:100%" name="cand_pass_status" id="pass_status" required="">
                        <option value=""></option>
                        <option value="1">Passport Copy</option>
                        <option value="2">Original Passport</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3" id="passport_original_copy2" style="display: none;">
                    <div class="form-group">
                      <label>PP Received Date</label>
                      <input id="fp-default" class="form-control flatpickr-basic flatpickr-input active pass_date" type="text" required  placeholder="Enter Your PP Received Date"  name="pass_date"   autocomplete="off" value="<?php if(isset($cand_tbl)){echo $cand_tbl->cand_date_expiry; } ?>">
                    </div>
                  </div>
                  <div class="col-md-3" id="passport_original_copy3" style="display: none;">
                    <div class="form-group">
                      <label>Received Location</label>
                      <select class="form-control select2" style="width:100%" required=""  name="pass_locatpassn" id="pass_locatpassn" >
                        <option value=""></option>
                        <?php
                          $branch = \App\AdminModel\Branch::all();
                          foreach($branch as $br){
                        ?>
                          <option value="<?php echo $br->br_id?>"><?php echo $br->br_name?></option>
                        <?php }?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row"  id="passport_original_copy" style="display: none;">
                  <div class="col-md-3">
                    <label>Old Passport Received?</label>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input" value="1" id="customRadio18" name="old_passport">
                      <label class="custom-control-label" for="customRadio18" >yes</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input"  value="0" id="customRadio28" name="old_passport">
                      <label class="custom-control-label" for="customRadio28">no</label>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <label>Driving License Received?</label>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input" value="1" id="customRadio38" name="d_liecense">
                      <label class="custom-control-label" for="customRadio38">yes</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input" value="0" id="customRadio48" name="d_liecense">
                      <label class="custom-control-label" for="customRadio48">no</label>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <label>Exit Paper Received?</label>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input" value="1" id="customRadio58" name="exist_paper">
                      <label class="custom-control-label" for="customRadio58">yes</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input" value="0" id="customRadio68" name="exist_paper">
                      <label class="custom-control-label" for="customRadio68">no</label>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <label>Photo Received?</label>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input" value="1" id="customRadio78" name="photo">
                      <label class="custom-control-label" for="customRadio78">yes</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input" value="0" id="customRadio88" name="photo">
                      <label class="custom-control-label" for="customRadio88">no</label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label>Medical Report Received?</label>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input" value="1" id="customRadio98" name="m_report">
                      <label class="custom-control-label" for="customRadio98">yes</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input" value="0" id="customRadio108" name="m_report">
                      <label class="custom-control-label" for="customRadio108">no</label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="">Care Of <span style="color: red">*</span></label>
                      @php
                        
                        $users = \App\User::where('user_id','!=','200')->where('status','!=','0')->get();
                      @endphp
                      <select name="care_of_id" id="care_of_id" class="form-control care_of_id select2" required title="Select Care Of except None">
                        <option></option>
                        <option value="200">--None--</option>
                        @foreach ($users as $us)
                            
                          <option value="{{ $us->user_id }}" @if(Auth::user()->user_type==1) @if($us->user_id == Auth::user()->user_id) selected @endif @endif >{{ $us->name }}</option>
                        @endforeach
                          
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="exampleSelectGender">Passport Type<span style="color: red;">*</span></label>
                      <select class="form-control select2" required=""  style="width:100%" onchange="set_service(this)" id="pass_type" name="cand_passport_type" id="cand_passport_type" >
                        <option value=""></option>
                        <option value="ECR">ECR</option>
                        <option value="ECNR">ECNR</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4"  >
                    <div class="form-group" id="pro_type">
                      <label>Visa Type<span style="color: red;">*</span></label>
                      <select class="form-control select2" style="width:100%"  name="cand_visa_type" id="cand_visa_type"  required="">
                        <option value=""></option>
                        @php
                          $visa_data = \App\AdminModel\Service::all();
                        @endphp
                        @foreach($visa_data as $s)
                          @if ($s->type=='visa')
                            <option value="{{$s->ser_id}}">{{$s->ser_name}}</option>
                          @endif
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6" id="services_id">
                    <div class="form-group">
                      <label>Optional Required Services<span style="color: red;">*</span></label></label>
                      <select  class="form-control select2" multiple="multiple" required="" style="width:100%"  name="cand_service_type[]" id="ser_status" onchange="get_flight_info(this)">
                        
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Service type <span style="color: red;">*</span></label>
                      <select class="form-control select2" style="width: 100%;"  name="ser_id" id="ser_id" required="">
                        @php
                          $servicetbl = \App\AdminModel\EmployeeServiceType::all();
                        @endphp
                        <option></option>
                        @foreach ($servicetbl as $service)
                          <option value="<?php echo $service->ser_id;?>"><?php echo $service->ser_name; ?></option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row" id="after-flight" style="display: none;">
                  <div class="col-md-4">
                    @php
                      $city  = \App\AdminModel\City::all();
                    @endphp
                    <div class="form-group">
                      <label>From<span style="color: red;">*</span></label>
                      <select class="form-control select2" name="travel_from">
                        <option></option>
                        @foreach($city  as $c) 
                          <option value="{{$c->city_id}}">{{$c->city_name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>To<span style="color: red;">*</span></label>
                      <select class="form-control select2" name="travel_to">
                        <option></option>
                        @foreach($city  as $c) 
                          <option value="{{$c->city_id}}">{{$c->city_name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>start travel date<span style="color: red;">*</span></label>
                      <input type="text" id="fp-default" class="form-control flatpickr-basic flatpickr-input active" name="travel_start_date">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>end travel date<span style="color: red;">*</span></label>
                      <input type="text" id="fp-default" class="form-control flatpickr-basic flatpickr-input active" name="travel_end_date">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>First Name<span style="color: red;">*</span></label>
                      <input class="form-control"  type="text" placeholder="Enter Your First Name" name="cand_fname" id="cand_fname" required="" autocomplete="off"  value="<?php if(isset($cand_tbl)){echo $cand_tbl->cand_fname; } ?>">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Middle Name</label>
                      <input class="form-control" type="text" placeholder="Enter Your Middle Name" name="cand_mname" id="cand_mname"  autocomplete="off" value="<?php if(isset($cand_tbl)){echo $cand_tbl->cand_mname; } ?>">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Last Name<span style="color: red;">*</span></label>
                      <input class="form-control" type="text" placeholder="Enter Your Last Name" name="cand_lname" id="cand_lname" required="" autocomplete="off" value="<?php if(isset($cand_tbl)){echo $cand_tbl->cand_lname; } ?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Passport Number<span style="color: red;">*</span></label>
                      <input class="form-control" type="text" oninput="check_pass(this)" placeholder="Enter Your Passport Number" maxlength="8" name="cand_passport_no" id="cand_passport_no" required="" autocomplete="off" >
                      <small id="errorPassportno" style="color: #ea5455;"></small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Place Of Birth<span style="color: red;">*</span></label>
                      <input  class="form-control" type="text" placeholder="Enter Your Place Of Birth" name="cand_place_birth"  required="" autocomplete="off" >
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Place Of Issue<span style="color: red;">*</span></label>
                      @php
                        $place = \App\AdminModel\CandidatePlaceIssue::all();
                      @endphp
                      <select class="form-control select2 cand-issue" name="cand_place_issue" required="">
                        <option>
                          <div class="add-new-customer btn btn-flat-success cursor-pointer rounded-0 text-left mb-50 p-50 w-100" data-toggle="modal" data-target="#add-new-customer-sidebar">
                            <span class="align-middle"></span>
                          </div>
                        </option>
                        @foreach ($place as $p)
                          <option value="{{$p->id}}">{{$p->place}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Date Of Issue<span style="color: red;">*</span></label>
                      <input id="fp-default" class="form-control flatpickr-basic flatpickr-input active"  type="text" placeholder="Enter Your Date Of Issue" name="cand_date_issue" required="" autocomplete="off" value="<?php if(isset($cand_tbl)){echo $cand_tbl->cand_date_issue; } ?>">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Date of Expiry<span style="color: red;">*</span></label>
                      <input id="fp-default" class="form-control flatpickr-basic flatpickr-input active exp-date" type="text" placeholder="Enter Your Date of expiry" name="cand_date_expiry" required="" autocomplete="off" value="<?php if(isset($cand_tbl)){echo $cand_tbl->cand_date_expiry; } ?>">
                      <span class="error" id="expiryError" style="color:#ea5455;display: none;"></span>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="exampleSelectGender">Nationality<span style="color: red;">*</span></label>
                      <?php
                        $nationltbl= \App\AdminModel\Nationality::all();
                        if(isset($nationltbl)){
                      ?>
                      <select class="form-control select2" required="" style="width:100%"  name="nat_id" id="nat_id">
                        <option value=""></option>
                        <?php foreach($nationltbl as $nat){ ?>
                          <option value="<?php echo $nat->nat_id;?>"<?php if(isset($cand_tbl)) if($cand_tbl->nat_id==$nat->nat_id){ echo "selected"; } ?>><?php echo $nat->nat_name;?></option>
                        <?php } ?>
                      </select>
                      <?php  } ?>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Date of Birth<span style="color: red;">*</span></label>
                      <input id="fp-default" class="form-control flatpickr-basic flatpickr-input active" type="text" placeholder="Enter Your Date of Birth" name="cand_dob" required="" autocomplete="off" value="<?php if(isset($cand_tbl)){echo $cand_tbl->cand_dob; } ?>">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="exampleSelectGender">Gender<span style="color: red;">*</span></label>
                      <select class="form-control select2" required="" style="width:100%"   name="cand_gender" id="cand_gender">
                        <option value=""></option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="exampleSelectGender">Religion <span style="color: red;">*</span></label>
                      <?php
                        $religiontbl= \App\AdminModel\ReligionCast::all();
                        if(isset($religiontbl)){
                      ?>
                      <select class="form-control select2" style="width:100%" required="" name="rel_cst_id" id="rel_cst_id">
                        <option value=""></option>
                        <?php foreach($religiontbl as $religion){ ?>
                          <option value="<?php echo $religion->rel_cst_id;?>"<?php if(isset($cand_tbl)) if($cand_tbl->rel_cst_id==$religion->rel_cst_id){ echo "selected"; } ?>><?php echo $religion->rel_cst_name;?></option>
                        <?php } ?>
                      </select>
                      <?php  } ?>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="exampleSelectGender">Profession <span style="color: red;">*</span></label>
                      <?php
                        $professiontbl= \App\AdminModel\Profession::all();
                        if(isset($professiontbl)){
                      ?>
                      <select class="form-control select2" style="width:100%"  name="prof_id" id="prof_id" required="">
                        <option value=""></option>
                          <?php foreach($professiontbl as $prof){ ?>
                            <option value="<?php echo $prof->prof_id;?>"<?php if(isset($cand_tbl)) if($cand_tbl->prof_id==$prof->prof_id){ echo "selected"; } ?>><?php echo $prof->prof_eng_name; // echo " ".$prof->prof_arabic_name;?></option>
                          <?php } ?>
                      </select>
                      <?php }  ?>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Address <span style="color: red;">*</span></label>
                      <input class="form-control" type="text" placeholder="Enter Your Address 1" name="cand_pre_address" id="cand_pre_address" required="" autocomplete="off" value="<?php if(isset($cand_tbl)){echo $cand_tbl->cand_pre_address; } ?>">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="">Pin Code (Postal Code) <span style="color: red;">*</span></label>
                      <input type="text" class="form-control" placeholder="Enter Your Postal Code" maxlength="6" name="pincode" id="pincode" required autocomplete="off" value="<?php if(isset($cand_tbl)){ echo $cand_tbl->pincode; } ?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Mobile No</label>
                      <input type="text" class="form-control"  placeholder="Enter Your Mobile No." name="cand_mobile_no" id="cand_mobile_no"  maxlength="10" autocomplete="off" value="<?php if(isset($cand_tbl)){echo $cand_tbl->cand_mobile_no; } ?>">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Note </label>
                      <textarea style="height: 60px;" class="form-control" type="text" placeholder="Enter Your Note" name="cand_local_note" id="cand_local_note"  autocomplete="off" value="<?php if(isset($cand_tbl)){echo $cand_tbl->cand_local_address; } ?>"></textarea>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                      <label>Upload Photo</label>
                      <div class="custom-file">
                        <input type="file"  name="cand_photo[]" multiple="" required="" class="custom-file-input" id="cand_photo">
                        <label class="custom-file-label" for="customFile">Choose File</label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                      <label>Upload Passport </label>
                      <div class="custom-file">
                        <input type="file"  name="cand_passport_doc[]" multiple=""  required="" class="custom-file-input" id="cand_photo">
                        <label class="custom-file-label" for="customFile">Choose File</label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                      <label>Upload Documents</label>
                      <div class="custom-file">
                        <input type="file"  name="cand_doc[]" multiple="" class="custom-file-input" id="cand_doc[]">
                        <label class="custom-file-label" for="customFile">Choose File</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                      <span id="blah"></span>
                    </div>
                  </div>
                  <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                      <span id="blah2"></span>
                    </div>
                  </div>
                  <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                      <span id="blah3"></span>
                    </div>
                  </div>
                  <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                      <span id="blah5"></span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-body -->
            </form>
          </div>
        </div>
        <!-- Modal to add new user Ends-->

        <!-- Filter Modal Start -->
        <div class="modal modal-slide-in new-user-modal fade" id="modals-slide-in-filter" data-bs-backdrop="static" data-bs-keyboard="false">
          <div class="modal-dialog sidebar-sm">
            <form class="modal-content pt-0">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
              <div class="modal-header mb-1">
                <h5 class="modal-title" id="exampleModalLabel">Add Filter</h5>
              </div>
              <div class="modal-body flex-grow-1">
                <div class="form-row">
                  <div class="form-group col-md-12">
                    <select name="" id="select-filter" class="form-control select2">
                      <option value="">Select</option>
                      <option value="1">Custom</option>
                      <option value="0">Basic</option>
                    </select>
                  </div>


                  <div class="form-group col-md-12">
                    <div class="all-check">

                      <div class="custom-control custom-checkbox mt-1" id="party-f" style="display: none">
                        <input type="checkbox"  class="custom-control-input" name="filter" id="partyf" checked>
                        <label for="partyf" class="custom-control-label">Party</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="stage-f" style="display: none">
                        <input type="checkbox"  class="custom-control-input" name="filter" id="stagef" checked>
                        <label for="stagef" class="custom-control-label">Stage</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="incomplete-f" style="display: none">
                        <input type="checkbox"  class="custom-control-input" name="filter" id="incompletef" checked>
                        <label for="incompletef" class="custom-control-label">Incomplete</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="cstatus2-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="cstatus2f" checked>
                        <label for="cstatus2f" class="custom-control-label">Status</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="care-of-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="care-off" checked>
                        <label for="care-off" class="custom-control-label">Care Of</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="servicetype-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="servicetypef">
                        <label for="servicetypef" class="custom-control-label">Service Type</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="visa-stamp-date-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="visa-stamp-datef">
                        <label for="visa-stamp-datef" class="custom-control-label">Visa Stamped Date</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="updated-at-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="updated-atf">
                        <label for="updated-atf" class="custom-control-label">Updated At</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="created-at-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="created-atf">
                        <label for="created-atf" class="custom-control-label">Created At</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="all-document-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="all-documentf">
                        <label for="all-documentf" class="custom-control-label">All Document</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="new-document-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="new-documentf">
                        <label for="new-documentf" class="custom-control-label">New Document</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="for-mofa-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="for-mofaf">
                        <label for="for-mofaf" class="custom-control-label">For Mofa</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="mofa-unpaid-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="mofa-unpaidf">
                        <label for="mofa-unpaidf" class="custom-control-label">Mofa Unpaid</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="awaited-passport-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="awaited-passportf">
                        <label for="awaited-passportf" class="custom-control-label">Awaited Passport</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="medicle-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="mediclef">
                        <label for="mediclef" class="custom-control-label">Medicle</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="for-submission-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="for-submissionf">
                        <label for="for-submissionf" class="custom-control-label">For Submission</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="submission-ready-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="submission-readyf">
                        <label for="submission-readyf" class="custom-control-label">Submission Ready</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="hold-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="holdf">
                        <label for="holdf" class="custom-control-label">Hold</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="submitted-f" style="display: none">
                        <input type="checkbox" class="custom-control-input" name="filter" id="submittedf">
                        <label for="submittedf" class="custom-control-label">Submitted</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="rejection-f" style="display: none">
                        <input type="checkbox"  class="custom-control-input" name="filter" id="rejectionf">
                        <label for="rejectionf" class="custom-control-label">Rejection</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="emigration-applied-f" style="display: none">
                        <input type="checkbox"  class="custom-control-input" name="filter" id="emigration-appliedf">
                        <label for="emigration-appliedf" class="custom-control-label">Emigration Applied</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="done-f" style="display: none">
                        <input type="checkbox"  class="custom-control-input" name="filter" id="donef">
                        <label for="donef" class="custom-control-label">Done</label>
                      </div>

                      <div class="custom-control custom-checkbox mt-1" id="cancelled-f" style="display: none">
                        <input type="checkbox"  class="custom-control-input" name="filter" id="cancelledf">
                        <label for="cancelledf" class="custom-control-label">Cancelled</label>
                      </div>

                      <p style="margin-top: 22px;">
                        <a href="#" id="all-chk2" style="display: none"><span class="badge badge-light-primary badge-pill ">Select all</span></a>
                        <a href="#" style="margin-left: 24px;display:none" id="default-chk"><span class="badge badge-light-primary badge-pill ">Default</span></a>
                        <a href="#" style="margin-left: 24px;display:none" id="update-chk"><span class="badge badge-light-primary badge-pill ">Update</span></a>
                      </p>  
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
        <!-- Filter Modal End -->

      </div>
      <!-- list section end -->
    </section>
    <!-- users list ends -->
    <div class="modal fade modal-danger text-left" id="danger" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <form method="post" action="{{ url('services/candidate/delete') }}">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title" id="myModalLabel120"></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="id" class="form-control" id="id_delete">
              <h5 style="color: #ea5455;">Do You Want To Delete This Record ?</h5> 
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-danger btn-sm">Yes</button>
              <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">No</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Update Status Modal Start -->
    <div class="modal fade text-left modal-primary" id="updateSt" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
          
          <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel16">New Documents</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xl-12 col-lg-12 d-flex flex-column justify-content-between border-container-lg">
                <form action="">
                  <div class="row text-center" style="margin-top: 10px;">
                    <div class="col-md-4 col-sm-4">
                      <div class="form-group">
                        <div class="form-group">
                          <span id="blah">
                            <figure class="figure">
                              <img src="" alt="" style="width:100px;height:100pc;" class="img-thumbnail">
                              <figcaption class=""></figcaption>
                            </figure>
                          </span>
                          
                        </div>
                      </div>
                      <div class="form-group">
                        <span id="blah2">
                          <?php

                          ?>
                          <hr>
                          <img src="" alt="" style="width:100px; height:100px;" class="img-thumbnail"> 
                        </span>
                        <p>Passport Document</p>
                      </div>
                      <div class="form-group">
                        <label>Upload Photo</label>
                          <div class="custom-file">
                            <input type="file"  name="cand_photo"   class="custom-file-input" id="cand_photo" onchange="readURL1(this)">
                            <label class="custom-file-label" for="customFile">Choose File</label>
                          </div>
                      </div>
                      <div class="form-group">
                        <label>Upload Doc</label>
                          <div class="custom-file">
                            <input type="file"  name="cand_doc[]" multiple=""  class="custom-file-input" id="cand_doc">
                            <label class="custom-file-label" for="customFile">Choose File</label>
                          </div>
                      </div>
                    </div>
                    <div class="col-md-2 col-lg-2 col-sm-2"></div>
                    <div class="col-md-6 col-lg-6 col-sm-2">
                      <div class="table-responsive text-center">
                        <table id="copied_table" class=""  style="width: 100%;">
                          <tr class="">
                            <th width="25%;">Agency Name</th>
                            <th width="25%;">Candidate Name</th>
                            <th width="25%;">Passport No</th>
                            {{-- <th width="25%;">First name</th> --}}
                            {{-- <th width="25%;">Middle name</th> --}}
                            {{-- <th width="25%;">Last name</th> --}}
                            <th width="25%;">Passport type</th>
                          </tr>   
                          <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>
                          <tr>
                            <th width="25%;">Date of Birth</th>
                            <th width="25%;">Place of birth</th>
                            <th width="25%">Place of Issue</th>
                            <th width="25%;">Date of Issues</th>
                          </tr>
                          <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>
                        </table>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Accept</button> --}}
            <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success btn-sm" id="">Save and Next</button>
          </div>
          <p id="new_document"></p>
          
          

          
        </div>
      </div>
    </div>
    <!-- Update Status Modal End -->

  @endsection

  @section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
    <script src="{{ asset(('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(('vendors/js/tables/datatable/responsive.bootstrap4.js')) }}"></script>
    <script src="{{ asset(('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(('vendors/js/tables/datatable/buttons.bootstrap4.min.js')) }}"></script>
    <script src="{{ asset(('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
    <script src="{{ asset(('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
    <script src="{{ asset(('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset(('vendors/js/extensions/toastr.min.js')) }}"></script>
  @endsection

  @section('page-script')
    
  {{-- Permision Candidate Start --}}
    @if (Auth::user()->user_type == 2)
      @php
          $perms = \App\AdminModel\AccessPermissionModule2::where('user_id','=',Auth::user()->user_id)->first();
          // dd($perms);
      @endphp
      @if (isset($perms))
          @if ($perms->ser_cand_c == '0')
          <style>
            .candbtn{
              display: none !important;
            }
          </style>      
          @endif
          @if ($perms->ser_cand_d == '0')
              <style>
                .delBtn{
                  display: none !important;
                }
              </style>
          @endif
      @endif
    @elseif(Auth::user()->user_type == 1)
        <style>
          .candbtn{
            display: block !important;
          }
        </style>
    @endif
  {{-- Permisson Candidate End --}}
  
  <script>feather.replace()</script>
    <script type="text/javascript">
      $(document).ready(function(){
        $('#danger').on('show.bs.modal', function (e) {
          var id= $(e.relatedTarget).data('id');
          $('#id_delete').val(id);
        });
      });
    </script>

    

    <script>
      $(document).ready(function(){

        $('#updateSt').on('show.bs.modal',function(e){
          
          var id = $(e.relatedTarget).data('id');
          console.log(id);

          // Get Candidate Data From Table
          jQuery.ajax({
            url:"{{ url('services/candidate/getDetailsC') }}",
            method: 'get',
            type:'html',
            data:{
              id:id
            },
            success: function(data){
              console.log(data);
              
                $('#new_document').val(data.new_document);                

            }
          });

        });
      });
    </script>

    <script type="text/javascript">
      var candidateDelete = "{{ url('services/candidate/delete/') }}";
      var candidateView = "{{ url('services/candidate/view/') }}";
      var candidateStUpdate = "{{ url('services/candidate-status/') }}";
      var imagePath = "{{ asset('images/staff/') }}";
      var medicletabPath = "{{ asset('services/medicle/') }}";
      var canDelete = {{ \Auth::user()->user_type }}
    </script>
    <script src="{{ asset(('js/scripts/pages/service-candidate-list.js')) }}"></script>
    {{-- <script src="{{ asset(('js/scripts/pages/service-candidate-list-2.js')) }}"></script> --}}
    <script src="{{ asset(('js/scripts/pages/service-newdocument-list.js')) }}"></script>
    <script src="{{ asset(('js/scripts/pages/service-formofa-list.js')) }}"></script>
    <script src="{{ asset(('js/scripts/pages/service-mofaunpaid-list.js')) }}"></script>
    <script src="{{ asset(('js/scripts/pages/service-mofapaid-list.js')) }}"></script>
    <script src="{{ asset(('js/scripts/pages/service-passportcopy-list.js')) }}"></script>
    <script src="{{ asset(('js/scripts/pages/service-medicle-list.js')) }}"></script>
    <script src="{{ asset(('js/scripts/pages/service-tosubmission-list.js')) }}"></script>
    <script src="{{ asset(('js/scripts/pages/service-awaitedpassport-list.js')) }}"></script>
    <script src="{{ asset(('js/scripts/pages/service-hold-list.js')) }}"></script>
    <script src="{{ asset(('js/scripts/pages/service-submitted-list.js')) }}"></script>
    <script src="{{ asset(('js/scripts/pages/service-rejection-list.js')) }}"></script>
    <script src="{{ asset(('js/scripts/pages/service-foremigration-list.js')) }}"></script>
    <script src="{{ asset(('js/scripts/pages/service-inemigration-list.js')) }}"></script>
    <script src="{{ asset(('js/scripts/pages/service-emigrationreject-list.js')) }}"></script>
    <script src="{{ asset(('js/scripts/pages/service-done-list.js')) }}"></script>

    <script src="{{ asset(('js/scripts/forms/form-validation.js')) }}"></script>
    <script src="{{ asset(('js/scripts/forms/form-select2.js')) }}"></script>
    <script src="{{ asset(('js/scripts/forms/pickers/form-pickers.js')) }}"></script>
    <script src="{{asset('js/scripts/ui/ui-feather.js')}}"></script>
    <script src="{{asset('js/scripts/pages/unserialize-data.js')}}"></script>
  
    <script type="text/javascript">
      $('.exp-date').change(function (){
        var exp_date = this.value;
        jQuery.ajax({
          url: "{{ url('services/candidate/check/expdate') }}",
          method: 'post',
          type: 'html',
          data: {
            "_token": "{{ csrf_token() }}",
            exp_date: exp_date
                },
          success: function(result){
            $('#expiryError').show();
            if(result==0){
              $('#expiryError').text('Passport is not eligible for visa endorsment')
            }else if(result==1){
              $('#expiryError').text('');
            }else if(result==2){
              $('#expiryError').text('Passport is expired');
            }
          }                      
        });
      });
  </script>
  {{-- <script type="text/javascript">
      if (select2.length) {
    select2.on('select2:open', function () {
        if (!$(document).find('.add-new-customer').length) {
          $(document)
            .find('.select2-results__options')
            .before(
              '<div class="add-new-customer btn btn-flat-success cursor-pointer rounded-0 text-left mb-50 p-50 w-100" data-toggle="modal" data-target="#add-new-customer-sidebar">' +
                feather.icons['plus'].toSvg({ class: 'font-medium-1 mr-50' }) +
                '<span class="align-middle">Add New Customer</span></div>'
            );
        }
      });
  }
  </script> --}}

  <script type="text/javascript">
    function get_flight_info(a)
    {
      var cc = $('#ser_status').val();
      var check = a.value;
      if(cc.includes('3'))
      {
        $('#after-flight').show();
      }
      else
      {
        $('#after-flight').hide();
      }
    }
  </script>
  <script type="text/javascript">
    function check_pass(pass){
      var pass_no = pass.value;
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
      });
      jQuery.ajax({
        url: "{{ url('services/candidate/check/passport') }}",
        method: 'post',
        type: 'html',
        data: {
          "_token": "{{ csrf_token() }}",
          pass_no: pass_no
        },
        success: function(result){
          if(result){
          $('input[name="cand_passport_no"]').val('');
            $('#errorPassportno').text('Passport no "'+pass_no+'" already exist')
          }else{
            $('#errorPassportno').text('');
          }
        }
      });
    }
  </script>
  <script>
    function getService(val){
      //   $("#ser_status").val(' ');
      $("#ser_status option[value='']").attr('selected', true)
      // var work_id =  val.value;
      var p_id = $("#pty_id").val();
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
      });
      jQuery.ajax({
        url: "{{ url('/req_service') }}",
        method: 'post',
        type: 'html',
        data: {
          "_token": "{{ csrf_token() }}",
          // work_id: work_id,
          p_id: p_id
        },
        success: function(result){
            // $("#ser_status option[value='']").attr('selected', true);
            $('#ser_status').empty();
            $("#ser_status").append(result);
        }});
      }
    </script>
    <script type="text/javascript">
      function show_div(id)
      {
        var st= id.value;
        if(st==2){
          $("#passport_original_copy").show();
          $("#passport_original_copy2").show();
          $("#passport_original_copy3").show();
        } if(st==1){
          $("#passport_original_copy").hide();
          $("#passport_original_copy2").hide();
          $("#passport_original_copy3").hide();
        }
      }
    </script>
    <script type="text/javascript">
      function readURL1(input) {
        $("#blah").empty();
        for(var i=0;i<input.files.length;i++)
        {
          if (input.files && input.files[i]) {
            var reader = new FileReader();
            reader.onload = function(e) {
              $($.parseHTML('<img style="width: 80px;height: 70px;margin:5px;">&nbsp&nbsp')).attr('src', e.target.result).appendTo('#blah');
            }
            reader.readAsDataURL(input.files[i]);
          }
        }
      }
      function readURL2med(input) {
        $("#pre2med").empty();
        for(var i=0;i<input.files.length;i++)
        {
          if (input.files && input.files[i]) {
            $($.parseHTML('<span  style="margin:5px;">'+input.files[i].name+'</span>')).appendTo('#pre2med');
            $($.parseHTML('<hr>')).appendTo('#blah2');
          }
        }
      }
      function readURL2(input) {
        $("#blah2").empty();
        for(var i=0;i<input.files.length;i++)
        {
          if (input.files && input.files[i]) {

            $($.parseHTML('<span  style="margin:5px;">'+input.files[i].name+'</span>')).appendTo('#blah2');
            $($.parseHTML('<hr>')).appendTo('#blah2');
          }
        }
      }
    </script>
    <script type="text/javascript">
      function readURL3(input) {
        $("#blah3").empty();
        for(var i=0;i<input.files.length;i++)
        {
          if (input.files && input.files[i]) {

            $($.parseHTML('<span  style="margin:5px;">'+input.files[i].name+'</span>')).appendTo('#blah3');
            $($.parseHTML('<hr>')).appendTo('#blah3');

          }
        }
      }
    </script>
    <script type="text/javascript">
      function readURL4(input) {
        $("#blah4").empty();
        for(var i=0;i<input.files.length;i++)
        {
          if (input.files && input.files[i]) {
            $($.parseHTML('<span  style="margin:5px;">'+input.files[i].name+'</span>')).appendTo('#blah4');
            $($.parseHTML('<hr>')).appendTo('#blah4');
          }
        }
      }
    </script>
    <script type="text/javascript">
      function set_service(a){
        var pass_type = a.value;
        $('#ser_status').empty();
        // $.ajaxSetup({
        //   headers: {
        //     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        //   }
        // });
        jQuery.ajax({
          url: "{{ url('services/candidate/getservice') }}",
          method: 'get',
          type: 'html',
          data: {

            pass_type: pass_type

          },
          success: function(data)
          {

          $('#ser_status').html(data.res);
          console.log(data.res);
        }
      });

      }
    </script>
  {{-- Filter JS Start --}}
  <script type="text/javascript">
    $(document).ready(function(){
      if ( localStorage.servicetypef == 1 || localStorage.visastampdatef==1 || localStorage.cancelledf==1 || localStorage.donef==1 || localStorage.emigrationappliedf==1 || localStorage.rejectionf==1 || localStorage.submittedf==1 || localStorage.holdf==1 || localStorage.partyf == 1 || localStorage.stagef==1 || localStorage.incompletef==1 || localStorage.cstatus2f==1 || localStorage.careoff==1 || localStorage.submissionreadyf==1 || localStorage.forsubmissionf==1 || localStorage.medcalf==1 || localStorage.awaitedpassf==1 || localStorage.mofaunpaidf==1 || localStorage.formofaf==1 || localStorage.updatedate==1 || localStorage.createdatf==1 || localStorage.alldocumentf == 1 || localStorage.newdocumentf==1) {
        
        $('#update-chk').show();
      }

      if (localStorage.servicetypef == 1) {
        $('#servicetypef').trigger('click');
        $('#servicetype-f').show();
      }

      if(localStorage.holdf==1){
        $('#holdf').trigger('click');
        $('#hold-f').show();
      }

      if(localStorage.submittedf==1){
        $('#submittedf').trigger('click');
        $('#submitted-f').show();
      }

      if(localStorage.rejectionf==1){
        $('#rejectionf').trigger('click');
        $('#rejection-f').show();
      }

      if(localStorage.emigrationappliedf==1){
        $('#emigration-appliedf').trigger('click');
        $('#emigration-applied-f').show();
      }

      if(localStorage.donef==1){
        $('#donef').trigger('click');
        $('#done-f').show();
      }

      if(localStorage.cancelledf==1){
        $('#cancelledf').trigger('click');
        $('#cancelled-f').show();
      }
 
      if(localStorage.visastampdatef==1){
        $('#visa-stamp-datef').trigger('click');
        $('#visa-stamp-date-f').show();
      }

      if(localStorage.updatedate==1){
        $('#updated-atf').trigger('click');
        $('#updated-at-f').show();
      }
      if(localStorage.createdatf==1){
        $('#created-atf').trigger('click');
        $('#created-at-f').show();
      }
      if(localStorage.alldocumentf==1){
        $('#all-documentf').trigger('click');
        $('#all-document-f').show();
      }
      if(localStorage.newdocumentf==1){
        $('#new-documentf').trigger('click');
        $('#new-document-f').show();
      }
      if(localStorage.formofaf==1){
        $('#for-mofaf').trigger('click');
        $('#for-mofa-f').show();
      }
      if(localStorage.mofaunpaidf==1){
        $('#mofa-unpaidf').trigger('click');
        $('#mofa-unpaid-f').show();
      }
      if(localStorage.awaitedpassf==1){
        $('#awaited-passportf').trigger('click');
        $('#awaited-passport-f').show();

      }
      if(localStorage.medcalf==1){
        $('#mediclef').trigger('click');
        $('#medicle-f').show();
      }
      if(localStorage.forsubmissionf==1){
        $('#for-submissionf').trigger('click');
        $('#for-submission-f').show();
      }
      if(localStorage.submissionreadyf==1){
        $('#submission-readyf').trigger('click');
        $('#submission-ready-f').show();

      }

      if(localStorage.partyf==1){
        $('#partyf').attr('checked',true);
        $('#party-f').show();
      
      }else{
        $('#partyf').attr('checked',false);
        $('.party-div').hide();
      }
      if(localStorage.stagef==1){
        $('#stagef').attr('checked',true);
        $('#stage-f').show();

      }else{
        $('#stagef').attr('checked',false);
        $('.stage-div').hide();
      }
      if(localStorage.incompletef==1){
        $('#incompletef').attr('checked',true);
        $('#incomplete-f').show();

      }else{
        $('#incompletef').attr('checked',false);
        $('.incomplete-div').hide();
      }

      if(localStorage.cstatus2f==1){
        $('#cstatus2f').attr('checked',true);
        $('#cstatus2-f').show();
      }else{
        $('#cstatus2f').attr('checked',false);
        $('.cstatus-div').hide();
      }

      if(localStorage.careoff==1){
        $('#care-off').attr('checked',true);
        $('#care-of-f').show();
      }else{
        $('#care-off').attr('checked',false);
        $('.care-of-div').hide();
      }
    });

    $('#update-chk').click(function(){

      var selectv = $('#select-filter').val();
      // console.log(selectv);
      if (selectv == 0) {
          localStorage.selectfilter2 = 0;
      }else if(selectv == 1){
        localStorage.selectfilter2 = 1;
      }else{
        localStorage.selectfilter2 = '';
      }

      if($('#servicetypef:checkbox:checked').length > 0){
        localStorage.servicetypef=1;
        
      }else{
        localStorage.servicetypef=0;
      }
    
      if($('#holdf:checkbox:checked').length > 0){
        localStorage.holdf=1;
        
      }else{
        localStorage.holdf=0;
      }

      if($('#submittedf:checkbox:checked').length > 0){
        localStorage.submittedf=1;
        
      }else{
        localStorage.submittedf=0;
      }

      if($('#rejectionf:checkbox:checked').length > 0){
        localStorage.rejectionf=1;
        
      }else{
        localStorage.rejectionf=0;
      }

      if($('#emigration-appliedf:checkbox:checked').length > 0){
        localStorage.emigrationappliedf=1;
        
      }else{
        localStorage.emigrationappliedf=0;
      }

      if($('#donef:checkbox:checked').length > 0){
        localStorage.donef=1;
        
      }else{
        localStorage.donef=0;
      }

      if($('#cancelledf:checkbox:checked').length > 0){
        localStorage.cancelledf=1;
        
      }else{
        localStorage.cancelledf=0;
      }


      if($('#visa-stamp-datef:checkbox:checked').length > 0){
        localStorage.visastampdatef=1;
        
      }else{
        localStorage.visastampdatef=0;
      }

      if($('#updated-atf:checkbox:checked').length > 0){
        localStorage.updatedate=1;
      }else{
        localStorage.updatedate=0;
      }

      if($('#created-atf:checkbox:checked').length > 0){
        localStorage.createdatf=1;
      }else{
        localStorage.createdatf=0;
      }

      if($('#all-documentf:checkbox:checked').length > 0){
        localStorage.alldocumentf=1;
      }else{
        localStorage.alldocumentf=0;
      }
      if($('#new-documentf:checkbox:checked').length > 0){
        localStorage.newdocumentf=1;
      }else{
        localStorage.newdocumentf=0;
      }

      if($('#for-mofaf:checkbox:checked').length > 0){
        localStorage.formofaf=1;
      }else{
        localStorage.formofaf=0;
      }
      if($('#mofa-unpaidf:checkbox:checked').length > 0){
        localStorage.mofaunpaidf=1;
      }else{
        localStorage.mofaunpaidf=0;
      }
      if($('#awaited-passportf:checkbox:checked').length > 0){
        localStorage.awaitedpassf=1;
      }else{
        localStorage.awaitedpassf=0;
      }
      if($('#mediclef:checkbox:checked').length > 0){
        localStorage.medcalf=1;
      }else{
        localStorage.medcalf=0;
      }
      if($('#for-submissionf:checkbox:checked').length > 0){
        localStorage.forsubmissionf=1;
      }else{
        localStorage.forsubmissionf=0;
      }
      if($('#submission-readyf:checkbox:checked').length > 0){
        localStorage.submissionreadyf=1;
      }else{
        localStorage.submissionreadyf=0;
      }

      if($('#partyf:checkbox:checked').length > 0){

        localStorage.partyf=1;
      }else{
        localStorage.partyf=0;
      }

      if($('#stagef:checkbox:checked').length > 0){

        localStorage.stagef=1;
      }else{
        localStorage.stagef=0;
      }

      if($('#incompletef:checkbox:checked').length > 0){

        localStorage.incompletef=1;
      }else{
        localStorage.incompletef=0;
      }

      if($('#cstatus2f:checkbox:checked').length > 0){

        localStorage.cstatus2f=1;
      }else{
        localStorage.cstatus2f=0;
      }
      if($('#care-off:checkbox:checked').length > 0){
        localStorage.careoff=1;
      }else{
        localStorage.careoff=0;
      }
      toastr['success']('Filter updated successfully', 'Success', { hideDuration: 3000 });
    });


    $('#default-chk').click(function(){

      
      if($('#servicetypef:checkbox:checked').length > 0){
        $('#servicetypef').trigger('click');
      }

      if($('#holdf:checkbox:checked').length > 0){
        $('#holdf').trigger('click');
      }

      if($('#submittedf:checkbox:checked').length > 0){
        $('#submittedf').trigger('click');
      }

      if($('#rejectionf:checkbox:checked').length > 0){
        $('#rejectionf').trigger('click');
      }

      if($('#emigration-appliedf:checkbox:checked').length > 0){
        $('#emigration-appliedf').trigger('click');
      }

      if($('#donef:checkbox:checked').length > 0){
        $('#donef').trigger('click');
      }

      if($('#cancelledf:checkbox:checked').length > 0){
        $('#cancelledf').trigger('click');
      }


      if($('#visa-stamp-datef:checkbox:checked').length > 0){
        $('#visa-stamp-datef').trigger('click');
      }

      if($('#updated-atf:checkbox:checked').length > 0){
        $('#updated-atf').trigger('click');
      }

      if($('#created-atf:checkbox:checked').length > 0){
        $('#created-atf').trigger('click');
      }

      if($('#all-documentf:checkbox:checked').length > 0){
        $('#all-documentf').trigger('click');
      }

      if($('#new-documentf:checkbox:checked').length > 0){
        $('#new-documentf').trigger('click');
      }

      if($('#for-mofaf:checkbox:checked').length > 0){
        $('#for-mofaf').trigger('click');
      }

      if($('#mofa-unpaidf:checkbox:checked').length > 0){
        $('#mofa-unpaidf').trigger('click');
      }
      if($('#awaited-passportf:checkbox:checked').length > 0){
        $('#awaited-passportf').trigger('click');
      }

      if($('#mediclef:checkbox:checked').length > 0){
        $('#mediclef').trigger('click');
      }

      if($('#for-submissionf:checkbox:checked').length > 0){
        $('#for-submissionf').trigger('click');
      }

      if($('#submission-readyf:checkbox:checked').length > 0){
        $('#submission-readyf').trigger('click');
      }

      if($('#partyf:checkbox:checked').length > 0){

      }else{
        $('#partyf').trigger('click');
      }

      if($('#stagef:checkbox:checked').length > 0){

      }else{
        $('#stagef').trigger('click');
      }

      if($('#incompletef:checkbox:checked').length > 0){

      }else{
        $('#incompletef').trigger('click');
      }

      if($('#cstatus2f:checkbox:checked').length > 0){

      }else{
        $('#cstatus2f').trigger('click');
      }

      if($('#care-off:checkbox:checked').length > 0){

      }else{
        $('#care-off').trigger('click');
      }
      toastr['success']('Default filter applied successfully', 'Success', { hideDuration: 3000 });
    });



    $('#servicetypef').click(function(){
      $('.service-type-div').toggle();
    });
    
    $('#holdf').click(function(){
      $('.hold-div').toggle();
    });

    $('#submittedf').click(function(){
      $('.submitted-div').toggle();
    });

    $('#rejectionf').click(function(){
      $('.rejection-div').toggle();
    });

    $('#emigration-appliedf').click(function(){
      $('.emigration-applied-div').toggle();
    });

    $('#donef').click(function(){
      $('.done-div').toggle();
    });

    $('#cancelledf').click(function(){
      $('.cancelled-div').toggle();
    });

    $('#visa-stamp-datef').click(function(){
      $('.visa-stamped-date-div').toggle();
    });
    $('#updated-atf').click(function(){
      $('.updated-at-div').toggle();
    });
    $('#created-atf').click(function(){
      $('.created-at-div').toggle();
    });
    $('#all-documentf').click(function(){
      $('.all-document-div').toggle();
    });
    $('#new-documentf').click(function(){
      $('.new-document-div').toggle();
    });
    $('#for-mofaf').click(function(){
      $('.for-mofa-div').toggle();
    });
    $('#mofa-unpaidf').click(function(){
      $('.mofa-unpaid-div').toggle();
    });
    $('#awaited-passportf').click(function(){
      $('.awaited-passport-div').toggle();
     
    });
    $('#mediclef').click(function(){
      $('.medicle-div').toggle();
    });
    $('#for-submissionf').click(function(){
      $('.for-submission-div').toggle();
    });
    $('#submission-readyf').click(function(){
      $('.submission-ready-div').toggle();
    });
    $('#partyf').click(function(){
      $('.party-div').toggle();
    });
    $('#stagef').click(function(){
      $('.stage-div').toggle();
    });
    $('#cstatus2f').click(function(){
      $('.cstatus-div').toggle();
    });
    $('#incompletef').click(function(){
      $('.incomplete-div').toggle();
    });

    $('#care-off').click(function(){
      $('.care-of-div').toggle();
    });

  </script>

  <script type="text/javascript">

    $('#all-chk2').click(function () {
      $('input[name="filter"]').each(function () {
        if ($(this).prop('checked')) {

        }else{
          $(this).trigger('click');
        }
      });
    });
  </script>
<script type="text/javascript">
  $(document).ready(function(){
    $('#modals-slide-in').on('show.bs.modal', function (e) {
      $('.customizer .filter').css('visibility','hidden');
    });
    $('#modals-slide-in').on('hidden.bs.modal', function (e) {
      $('.customizer .filter').css('visibility','visible');
    });
  });
</script>

<script>
  $(document).ready(function(){
    $('#select-filter').on('change',function(){
      var value = $(this).val();
      if(value == 0){
        $('#party-f').show();
        $('#stage-f').show();
        $('#incomplete-f').show();
        $('#cstatus2-f').show();
        $('#care-of-f').show();
        $('#servicetype-f').hide();
        $('#visa-stamp-date-f').hide();
        $('#updated-at-f').hide();
        $('#created-at-f').hide();
        $('#all-document-f').hide();
        $('#new-document-f').hide();
        $('#for-mofa-f').hide();
        $('#mofa-unpaid-f').hide();
        $('#awaited-passport-f').hide();
        $('#medicle-f').hide();
        $('#for-submission-f').hide();
        $('#submission-ready-f').hide();
        $('#hold-f').hide();
        $('#submitted-f').hide();
        $('#rejection-f').hide();
        $('#emigration-applied-f').hide();
        $('#done-f').hide();
        $('#cancelled-f').hide();
        $('#all-chk2').hide();
        $('#update-chk').show();
      }else if(value == 1){
        $('#party-f').show();
        $('#stage-f').show();
        $('#incomplete-f').show();
        $('#cstatus2-f').show();
        $('#care-of-f').show();
        $('#servicetype-f').show();
        $('#visa-stamp-date-f').show();
        $('#updated-at-f').show();
        $('#created-at-f').show();
        $('#all-document-f').show();
        $('#new-document-f').show();
        $('#for-mofa-f').show();
        $('#mofa-unpaid-f').show();
        $('#awaited-passport-f').show();
        $('#medicle-f').show();
        $('#for-submission-f').show();
        $('#submission-ready-f').show();
        $('#hold-f').show();
        $('#submitted-f').show();
        $('#rejection-f').show();
        $('#emigration-applied-f').show();
        $('#done-f').show();
        $('#cancelled-f').show();
        $('#all-chk2').show();
        $('#update-chk').show();
      }else{
        $('#party-f').hide();
        $('#stage-f').hide();
        $('#incomplete-f').hide();
        $('#cstatus2-f').hide();
        $('#care-of-f').hide();
        $('#servicetype-f').hide();
        $('#visa-stamp-date-f').hide();
        $('#updated-at-f').hide();
        $('#created-at-f').hide();
        $('#all-document-f').hide();
        $('#new-document-f').hide();
        $('#for-mofa-f').hide();
        $('#mofa-unpaid-f').hide();
        $('#awaited-passport-f').hide();
        $('#medicle-f').hide();
        $('#for-submission-f').hide();
        $('#submission-ready-f').hide();
        $('#hold-f').hide();
        $('#submitted-f').hide();
        $('#rejection-f').hide();
        $('#emigration-applied-f').hide();
        $('#done-f').hide();
        $('#cancelled-f').hide();
        $('#all-chk2').hide();
        $('#update-chk').show();
      }
    });
  });
</script>

  {{-- Filter Js End --}}

@endsection
