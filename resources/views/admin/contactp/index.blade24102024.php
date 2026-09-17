@extends('layout.admin.admin_layout')

@section('title','Contact')

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

    @if (Auth::guard('admin')->user()->user_type == 2)
        @if ($permission->add_contactp == 0)
            <style>
                .addcontact{
                    display: none !important;
                }
            </style>
        @endif
        @if ($permission->edit_contactp == 0)
            <style>
                .editcontact{
                    display: none !important;
                }
            </style>
        @endif
        @if ($permission->view_contactp == 0)
            <style>
                .viewcontact{
                    display: none !important;
                }
            </style>
        @endif
        @if ($permission->delete_contactp == 0)
            <style>
                .delcontact{
                    display: none !important;
                }
            </style>
        @endif        
    @endif


@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-4 mb-4">
            <div class="col-sm-12 col-md-12 col-xl-12">
                <div class="card">
                    <div class="card-body nav-form-short-load">
                        <form action="{{ route('admin.contact.shortstore') }}" method="POST" id="shortsubmitcontact" class="shortsubmitcontact">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="short-add-business-type" class="form-label">Business Type <span class="text-danger">*</span></label>
                                        <select name="businesstype_id" id="short-add-business-type" class="form-select select22" data-placeholder="Select Business Type" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($businesstypes as $businesstype)
                                                <option value="{{ $businesstype->id }}" @if($businesstype->name == 'B2C') selected @endif>{{ $businesstype->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label" for="short-add-country-id">Country <span class="text-danger">*</span></label>
                                        <select name="country_id" id="short-add-country-id" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}" @if($country->id == '1') selected @endif>{{ $country->name }}</option> 
                                            @endforeach
                                        </select>
                                        
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="short-add-primary-person" class="form-label">Primary Concern Person Name</label>
                                        <input type="text" name="prim_concern_name" id="short-add-primary-person" class="form-control" placeholder="Enter Primary Concern Person...">
                                    </div>
                                </div>
        
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="short-add-primary-contact" class="form-label">Primary Contact No</label>
                                        <input type="text" name="prim_contact" id="short-add-primary-contact" class="form-control" placeholder="Enter Primary Contact No...">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    
                                    {{-- <button class="btn btn-sm btn-primary" type="submit">Submit</button> --}}
                                    <button class="btn btn-sm btn-primary" id="submitShortMsg" type="button">Submit</button>
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @php
            $countryfs = DB::table('contactps as contactp')
                ->leftjoin('countries as country','contactp.country_id','=','country.id')
                ->select('contactp.country_id','country.name as cname')->groupBy('contactp.country_id','cname')->where('contactp.country_id','!=','')->get();
            $cityfs = DB::table('contactps as contactp')
                ->leftjoin('cities as city','contactp.city_id','=','city.id')
                ->select('contactp.city_id','city.name as citname')
                ->groupBy('contactp.city_id','citname')
                ->where('contactp.city_id','!=','')
                ->get();

            $groupfs = DB::table('contactps as contactp')
                ->leftjoin('groupms as groupm','groupm.id','=','contactp.group_id')
                ->select('contactp.group_id','groupm.name as grpname')
                ->groupBy('contactp.group_id','grpname')
                ->where('contactp.group_id','!=','')
                ->where('contactp.group_id','!=','0')
                ->get();
            
            $lifcsts = DB::table('contactps as contactp')
                ->leftjoin('lifecyclestatuses as lfs','lfs.id','=','contactp.lcs_id')
                ->select('contactp.lcs_id','lfs.name as lfsname')
                ->groupBy('contactp.lcs_id','lfsname')
                ->where('contactp.lcs_id','!=','')
                ->get();

            $leadstg = DB::table('contactps as contactp')
                ->leftjoin('leadstages as lstage','lstage.id','=','contactp.ls_id')
                ->select('contactp.ls_id','lstage.name as leadstage')
                ->groupBy('contactp.ls_id','leadstage')
                ->where('contactp.ls_id','!=','')
                ->get();
            $businessTypes = DB::table('contactps as contactp')
                ->leftjoin('businesstypes as businesstyp','businesstyp.id','=','contactp.businesstype_id')
                ->select('contactp.businesstype_id','businesstyp.name as businame')
                ->groupBy('contactp.businesstype_id','businame')
                ->where('contactp.businesstype_id','!=','')
                ->get();

            $sendTags = DB::table('contactps as contactp')->select('contactp.send_tag')->groupBy('contactp.send_tag')->where('contactp.send_tag','!=','')->get();

            // $sendTags = DB::table('contactps as contactp2')->select('contactp2.send_tag')->groupBy('contactp2.send_tag')->where('contactp2.send_tag','!=','')->get();
        @endphp

        <!-- Users List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">Search Filter</h5>
                <div class="row">
                    <div class="col-md-3 mb-3 country-div"  @if(isset($contactpfilter) && $contactpfilter->country_filter == 1) @else style="display: none" @endif>
                        <select id="by-country" class="form-select select222" multiple data-placeholder="Select Country">
                            <option value="">Select Country</option>
                            @foreach ($countryfs as $country)
                                <option value="{{ $country->country_id }}">{{ $country->cname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 city-div" @if(isset($contactpfilter) && $contactpfilter->city_filter == 1) @else style="display: none" @endif>
                        <select id="by-city" class="form-select select222" multiple data-placeholder="Select City">
                            <option value="">Select City</option>
                            @foreach ($cityfs as $cityf)
                                <option value="{{ $cityf->city_id }}">{{ $cityf->citname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 group-name-div" @if(isset($contactpfilter) && $contactpfilter->group_name_filter == 1) @else style="display: none" @endif>
                        <select id="by-group" class="form-select select222" multiple data-placeholder="Select Group Name">
                            <option value="">Select Group</option>
                            <option value="grp0">Null</option>
                            @foreach ($groupfs as $groupf)
                                <option value="{{ 'grp'.$groupf->group_id }}">{{ $groupf->grpname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 life-cycle-status-div" @if(isset($contactpfilter) && $contactpfilter->life_cycle_status_filter == 1) @else style="display: none" @endif>
                        <select id="by-lifecycle-status" class="form-select select222" multiple data-placeholder="Select Life Cycle Status">
                            <option value="">Select Life Cycle Status</option>
                            @foreach ($lifcsts as $lifcst)
                                <option value="{{ $lifcst->lcs_id }}">{{ $lifcst->lfsname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 lead-stage-div" @if(isset($contactpfilter) && $contactpfilter->lead_stage_filter == 1) @else style="display: none" @endif>
                        <select id="by-lead-stage" class="form-select select222" multiple data-placeholder="Select Lead Stage">
                            <option value="">Select Lead Stage</option>
                            @foreach ($leadstg as $leadstg)
                                <option value="{{ $leadstg->ls_id }}">{{ $leadstg->leadstage }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 business-type-div" @if(isset($contactpfilter) && $contactpfilter->business_type_filter == 1) @else style="display: none" @endif>
                        <select id="by-business-type" class="form-select select222" multiple data-placeholder="Select Business Type">
                            <option value="">Select Business Type</option>
                            @foreach ($businessTypes as $businessType)
                                <option value="{{ $businessType->businesstype_id }}">{{ $businessType->businame }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <select name="" id="by-send-tag" class="form-select select222" multiple data-placeholder="Select Whatsapp Send Tag...">
                            <option value="">Select Whatsapp Send Tag</option>
                            @foreach ($sendTags as $sendTag)
                                <option value="{{ $sendTag->send_tag }}">{{ $sendTag->send_tag }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <input name="" type="text" id="by-send-date" class="form-control senddate-picker" placeholder="Send Date...">
                    </div>

                    <div class="col-md-3 mb-3 created-date-div" @if(isset($contactpfilter) && $contactpfilter->created_date_filter  == 1) @else style="display: none" @endif>
                        <input type="text" id="by-created-date" class="form-control createdate-picker" placeholder="Created date...">
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Full Name</th>
                            <th>Agency Name</th>
                            <th>Lead Stage</th>
                            <th>City</th>
                            <th>Country</th>
                            <th>Work</th>
                            <th>Status</th>
                            <th></th>
                            <th></th>
                            <th>Group ID</th>
                            <th></th>
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
                    <tbody class="listitem">

                    </tbody>
                </table>
            </div>
        </div>

         <!--- Add Candidate Start --->
         <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add New Contact</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.contact.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-business-type" class="form-label">Business Type <span class="text-danger">*</span></label>
                                <select name="businesstype_id" id="add-business-type" class="form-select select22" data-placeholder="Select Business Type" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($businesstypes as $businesstype)
                                        <option value="{{ $businesstype->id }}" @if($businesstype->name == 'B2C') selected @endif>{{ $businesstype->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label for="add-office-name-eng" class="form-label">Office Name (Eng) <span class="text-danger">*</span></label>
                                    <input type="text" name="office_eng_name" id="add-office-name-eng" class="form-control" placeholder="Enter Office Name (eng)...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-office-name-ar" class="form-label">Office Name (Arabic)</label>
                                <input type="text" name="office_ar_name" id="add-office-name-ar" class="form-control" placeholder="Enter Office Name (arabic)...">
                            </div>
                        </div>
                        {{-- <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-office-no" class="form-label">Office Number</label>
                                <input type="text" name="office_no" id="add-office-no" class="form-control" placeholder="Enter Office Contact No...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-office-email" class="form-label">Office Email</label>
                                <input type="text" name="office_email" id="add-office-email" class="form-control" placeholder="Enter Office Email...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-owner-name" class="form-label">Owner Name</label>
                                <input type="text" name="owner_name" id="add-owner-name" class="form-control" placeholder="Enter Owner Name...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-owner-contact" class="form-label">Owner Contact</label>
                                <input type="text" name="owner_contact" id="add-owner-contact" class="form-control" placeholder="Enter Owner Phone...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-owner-email" class="form-label">Owner Email</label>
                                <input type="text" name="owenr_email" id="add-owner-email" class="form-control" placeholder="Enter Owner Email...">
                            </div>
                        </div> --}}

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-country-id">Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="add-country-id" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option> 
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-city-id">City</label>
                                <select name="city_id" id="add-city-id" class="form-select select22" data-placeholder="Select City" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-primary-person" class="form-label">Primary Concern Person Name</label>
                                <input type="text" name="prim_concern_name" id="add-primary-person" class="form-control" placeholder="Enter Primary Concern Person...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-primary-contact" class="form-label">Primary Contact No</label>
                                <input type="text" name="prim_contact" id="add-primary-contact" class="form-control checkNoExistance" placeholder="Enter Primary Contact No...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-primary-email" class="form-label">Primary Email</label>
                                <input type="text" name="prim_email" id="add-primary-email" class="form-control" placeholder="Enter Primary Email...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-secondary-person" class="form-label">Seconday Concern Person Name</label>
                                <input type="text" name="sec_concern_name" id="add-secondary-person" class="form-control" placeholder="Enter Secondary Concern Person...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-secondary-contact" class="form-label">Seconday Contact No</label>
                                <input type="text" name="sec_contact" id="add-secondary-contact" class="form-control checkNoExistance" placeholder="Enter Secondary Contact No...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-secondary-email" class="form-label">Seconday Email</label>
                                <input type="text" name="sec_email" id="add-secondary-email" class="form-control" placeholder="Enter Seconday Email...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-person3" class="form-label">Concern Person Name 3</label>
                                <input type="text" name="concern_name3" id="add-person3" class="form-control" placeholder="Enter Concern Person 3...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact3" class="form-label">Contact No3</label>
                                <input type="text" name="contact3" id="add-contact3" class="form-control checkNoExistance" placeholder="Enter Contact No3...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-person4" class="form-label">Concern Person Name 4</label>
                                <input type="text" name="concern_name4" id="add-person4" class="form-control" placeholder="Enter Concern Person4...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact4" class="form-label">Contact No4</label>
                                <input type="text" name="contact4" id="add-contact4" class="form-control checkNoExistance" placeholder="Enter Contact No4...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-person5" class="form-label">Concern Person Name 5</label>
                                <input type="text" name="concern_name5" id="add-person5" class="form-control" placeholder="Enter Concern Person5...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact5" class="form-label">Contact No5</label>
                                <input type="text" name="contact5" id="add-contact5" class="form-control checkNoExistance" placeholder="Enter Contact No5...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-person6" class="form-label">Concern Person Name6</label>
                                <input type="text" name="concern_name6" id="add-person6" class="form-control" placeholder="Enter Concern Person6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact6" class="form-label">Contact No6</label>
                                <input type="text" name="contact6" id="add-contact6" class="form-control checkNoExistance" placeholder="Enter Contact No6...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact7" class="form-label">Contact No7</label>
                                <input type="text" name="contact7" id="add-contact7" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact8" class="form-label">Contact No8</label>
                                <input type="text" name="contact8" id="add-contact8" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact9" class="form-label">Contact No9</label>
                                <input type="text" name="contact9" id="add-contact9" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact10" class="form-label">Contact No10</label>
                                <input type="text" name="contact10" id="add-contact10" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact11" class="form-label">Contact No11</label>
                                <input type="text" name="contact11" id="add-contact11" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact12" class="form-label">Contact No12</label>
                                <input type="text" name="contact12" id="add-contact12" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-careoff" class="form-label">Careoff <span class="text-danger">*</span></label>
                                <select name="careoff_id" id="add-careoff" class="form-select select22" data-placeholder="Select Careoff" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($adminusers as $adminuser)
                                        <option value="{{ $adminuser->id }}" @if($adminuser->id == Auth::guard('admin')->user()->id) selected @endif>{{ $adminuser->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-leadowner" class="form-label">Lead Owner <span class="text-danger">*</span></label>
                                <select name="leadowner_id" id="add-leadowner" class="form-select select22" data-placeholder="Select Lead Owner" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($adminusers as $adminuser2)
                                        <option value="{{ $adminuser2->id }}" @if($adminuser2->id == Auth::guard('admin')->user()->id) selected @endif>{{ $adminuser2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="addnewcandidateBtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Add Candidate End --->

        <!--- Edit Candidate Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel">
            <div class="offcanvas-header">
                <h5 id="edituserLabel" class="offcanvas-title">Edit Contact</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.contact.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="editID" id="edit_ID">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-business-type" class="form-label">Business Type</label>
                                <select name="businesstype_id" id="edit-business-type" class="form-select select22" data-placeholder="Select Business Type" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($businesstypes as $businesstype2)
                                        <option value="{{ $businesstype2->id }}">{{ $businesstype2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-name-eng" class="form-label">Office Name (Eng) <span class="text-danger">*</span></label>
                                <input type="text" name="office_eng_name" id="edit-office-name-eng" class="form-control" placeholder="Enter Office Name (eng)...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-name-ar" class="form-label">Office Name (Arabic)</label>
                                <input type="text" name="office_ar_name" id="edit-office-name-ar" class="form-control" placeholder="Enter Office Name (arabic)...">
                            </div>
                        </div>
                        {{-- <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-no" class="form-label">Office Number</label>
                                <input type="text" name="office_no" id="edit-office-no" class="form-control" placeholder="Enter Office Contact No...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-email" class="form-label">Office Email</label>
                                <input type="text" name="office_email" id="edit-office-email" class="form-control" placeholder="Enter Office Email...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-owner-name" class="form-label">Owner Name</label>
                                <input type="text" name="owner_name" id="edit-owner-name" class="form-control" placeholder="Enter Owner Name...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-owner-contact" class="form-label">Owner Contact</label>
                                <input type="text" name="owner_contact" id="edit-owner-contact" class="form-control" placeholder="Enter Owner Phone...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-owner-email" class="form-label">Owner Email</label>
                                <input type="text" name="owenr_email" id="edit-owner-email" class="form-control" placeholder="Enter Owner Email...">
                            </div>
                        </div> --}}

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-country-id">Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="edit-country-id" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option> 
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-city-id">City</label>
                                <select name="city_id" id="edit-city-id" class="form-select select22" data-placeholder="Select City" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-primary-person" class="form-label">Primary Concern Person Name</label>
                                <input type="text" name="prim_concern_name" id="edit-primary-person" class="form-control" placeholder="Enter Primary Concern Person...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-primary-contact" class="form-label">Primary Contact No</label>
                                <input type="text" name="prim_contact" id="edit-primary-contact" class="form-control checkNoExistance" placeholder="Enter Primary Contact No...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-primary-email" class="form-label">Primary Email</label>
                                <input type="text" name="prim_email" id="edit-primary-email" class="form-control" placeholder="Enter Primary Email...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-secondary-person" class="form-label">Seconday Concern Person Name</label>
                                <input type="text" name="sec_concern_name" id="edit-secondary-person" class="form-control" placeholder="Enter Secondary Concern Person...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-secondary-contact" class="form-label">Seconday Contact No</label>
                                <input type="text" name="sec_contact" id="edit-secondary-contact" class="form-control checkNoExistance" placeholder="Enter Secondary Contact No...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-secondary-email" class="form-label">Seconday Email</label>
                                <input type="text" name="sec_email" id="edit-secondary-email" class="form-control " placeholder="Enter Seconday Email...">
                                
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person3" class="form-label">Concern Person Name 3</label>
                                <input type="text" name="concern_name3" id="edit-person3" class="form-control" placeholder="Enter Concern Person 3...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact3" class="form-label">Contact No3</label>
                                <input type="text" name="contact3" id="edit-contact3" class="form-control checkNoExistance" placeholder="Enter Contact No3...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person4" class="form-label">Concern Person Name 4</label>
                                <input type="text" name="concern_name4" id="edit-person4" class="form-control" placeholder="Enter Concern Person4...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact4" class="form-label">Contact No4</label>
                                <input type="text" name="contact4" id="edit-contact4" class="form-control checkNoExistance" placeholder="Enter Contact No4...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person5" class="form-label">Concern Person Name 5</label>
                                <input type="text" name="concern_name5" id="edit-person5" class="form-control" placeholder="Enter Concern Person5...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact5" class="form-label">Contact No5</label>
                                <input type="text" name="contact5" id="edit-contact5" class="form-control checkNoExistance" placeholder="Enter Contact No5...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person6" class="form-label">Concern Person Name6</label>
                                <input type="text" name="concern_name6" id="edit-person6" class="form-control" placeholder="Enter Concern Person6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact6" class="form-label">Contact No6</label>
                                <input type="text" name="contact6" id="edit-contact6" class="form-control checkNoExistance" placeholder="Enter Contact No6...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact7" class="form-label">Contact No7</label>
                                <input type="text" name="contact7" id="edit-contact7" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact8" class="form-label">Contact No8</label>
                                <input type="text" name="contact8" id="edit-contact8" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact9" class="form-label">Contact No9</label>
                                <input type="text" name="contact9" id="edit-contact9" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact10" class="form-label">Contact No10</label>
                                <input type="text" name="contact10" id="edit-contact10" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact11" class="form-label">Contact No11</label>
                                <input type="text" name="contact11" id="edit-contact11" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact12" class="form-label">Contact No12</label>
                                <input type="text" name="contact12" id="edit-contact12" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-careoff" class="form-label">Careoff <span class="text-danger">*</span></label>
                                <select name="careoff_id" id="edit-careoff" class="form-select select22" data-placeholder="Select Careoff" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($adminusers as $adminuser3)
                                        <option value="{{ $adminuser3->id }}">{{ $adminuser3->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-leadowner" class="form-label">Lead Owner <span class="text-danger">*</span></label>
                                <select name="leadowner_id" id="edit-leadowner" class="form-select select22" data-placeholder="Select Lead Owner" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($adminusers as $adminuser4)
                                        <option value="{{ $adminuser4->id }}">{{ $adminuser4->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editnewcontact">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Edit Candidate End --->
        <!-- Delete Contactplus Start -->
        
        <div class="modal fade" id="deleteStaff" aria-hidden="true" aria-labelledby="deleteStaffLabel" tabindex="-1">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                    <div class="modal-header pb-2">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Delete Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.contact.delete') }}" method="POST">
                        @csrf
                        <input type="hidden" name="contactID" id="contactID2">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="text-danger">Ary you sure to delete this contact?</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-sm btn-danger">Yes</button>
                                <button type="button" class="btn btn-sm btn-success" data-bs-dismiss="modal">No</button>
                            </div>
                        </div>
                    </form>
                </div>  
            </div>
        </div>
        <!-- Delete Contactplus Start -->
        <!-- Lead Stage Update Start -->
        <div class="modal fade" id="updateLstage" aria-hidden="true" aria-labelledby="updateLstageLabel" tabindex="-1">
            <div class="modal-dialog modal-l">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="updateLstageLabel">Lead Stage</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.stageUpdate') }}" method="POST" id="updateLeadStageValidation">
                    @csrf
                    <input type="hidden" name="contactID" id="contactID">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="update-lead-cycle-status" class="form-label">Life Cycle Status <span class="text-danger">*</span></label>
                                        <select name="lcs_id" id="update-lead-cycle-status" class="form-control select22" data-allow-clear="true" data-placeholder="Select Lead Cycle Status">
                                            <option value=""></option>
                                            @foreach ($lcss as $lcs)
                                                <option value="{{ $lcs->id }}">{{ $lcs->name }}</option>
                                            @endforeach
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
                                
                            </div>
                        </div>
                        <div class="card-footer">
                            {{-- <button class="btn btn-primary" type="submit">Update Lead Stage</button> --}}
                            <button class="btn btn-primary" type="button" id="leadStageUpdateBtn">Update Lead Stage</button>
                        </div>
                    </div>
                </form>
              </div>
            </div>
        </div>
        <!-- Lead Stage Update End -->

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
                                <input class="form-check-input" type="checkbox" name="countryf" value="1" id="countryf" @if(isset($contactpfilter) && $contactpfilter->country_filter == 1) checked @endif />
                                <label class="form-check-label" for="countryf"> Country</label>
                            </div>
                            <div class="form-check mt-2" id="city-f">
                                <input class="form-check-input" type="checkbox" name="cityf" value="1" id="cityf" @if(isset($contactpfilter) && $contactpfilter->city_filter == 1) checked @endif />
                                <label class="form-check-label" for="cityf"> City</label>
                            </div>
                            <div class="form-check mt-2" id="groupname-f">
                                <input class="form-check-input" type="checkbox" name="groupnamef" value="1" id="groupnamef" @if(isset($contactpfilter) && $contactpfilter->group_name_filter == 1) checked @endif />
                                <label class="form-check-label" for="groupnamef"> Group Name</label>
                            </div>
                            <div class="form-check mt-2" id="lifecyclestatus-f">
                                <input class="form-check-input" type="checkbox" name="lifecyclestatusf" value="1" id="lifecyclestatusf" @if(isset($contactpfilter) && $contactpfilter->life_cycle_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="lifecyclestatusf"> Life Cycle Status</label>
                            </div>
                            <div class="form-check mt-2" id="leadstage-f">
                                <input class="form-check-input" type="checkbox" name="leadstagef" value="1" id="leadstagef" @if(isset($contactpfilter) && $contactpfilter->lead_stage_filter == 1) checked @endif />
                                <label class="form-check-label" for="leadstagef"> Lead Stage</label>
                            </div>
                            <div class="form-check mt-2" id="businesstype-f">
                                <input class="form-check-input" type="checkbox" name="businesstypef" value="1" id="businesstypef" @if(isset($contactpfilter) && $contactpfilter->business_type_filter == 1) checked @endif />
                                <label class="form-check-label" for="businesstypef"> Business Type</label>
                            </div>
                            <div class="form-check mt-2" id="createddate-f">
                                <input class="form-check-input" type="checkbox" name="createddatef" value="1" id="createddatef" @if(isset($contactpfilter) && $contactpfilter->created_date_filter == 1) checked @endif />
                                <label class="form-check-label" for="createddatef"> Created Date</label>
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

        <!-- Bulk Send Whatsapp Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="bulkwhatsappsend" aria-labelledby="bulkwhatsappsendLabel">
            <div class="offcanvas-header">
                <h5 id="bulkwhatsappsendLabel" class="offcanvas-title">Send Bulk Whatsapp</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="bulksendwhatsapp" action="{{ route('admin.contact.bulksendwhatsapp') }}" method="POST" >
                    @csrf
                    <div class="row">
                        <input type="hidden" id="contactpID2" name="contactpID">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="send-whatsapp-type" class="form-label">For Whatsapp <span class="text-danger">*</span></label>
                                <select name="send_whatsapp_type" id="send-whatsapp-type" class="form-select select22" data-placeholder="Select For Whatsapp" data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="meta_whatsapp" selected>Meta Whatsapp</option>
                                    <option value="normal_whatsapp">Normal Whatsapp</option>
                                </select>
                            </div>
                        </div>
                        <!-- Meta Whatsapp Display Start -->
                        <div class="col-md-4 dismetawhatsapp">
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
                        <div class="col-md-4 dismetawhatsapp">
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

                        <div class="col-md-4 dismetawhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-contact-status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="contact_status[]" id="send-meta-template-contact-status" class="form-select select22" multiple data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 dismetawhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-contact-group" class="form-label">Group</label>
                                <select name="contact_group_id[]" id="send-meta-template-contact-group" class="form-select select22" multiple data-placeholder="Select Group...">
                                    <option value="">Select Group</option>
                                    @foreach ($groupms as $groupm)
                                        <option value="{{ $groupm->id }}">{{ $groupm->name }}</option>                                    
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 dismetawhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-subscribe" class="form-label">Subscribe <span class="text-danger">*</span></label>
                                <select name="subscribe[]" id="send-meta-template-subscribe" class="form-select select22" multiple data-placeholder="Select Subscribe">
                                    <option value=""></option>
                                    <option value="1">Subscribe</option>
                                    <option value="0">Unsubscribe</option>
                                </select>
                            </div>
                        </div>



                        <!-- Meta Whatsapp Display End -->
                        <!-- Normal Whatsapp Display Start -->
                        <div class="col-md-4 disnormalwhatsapp" style="display: none">
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
                        <div class="col-md-4 disnormalwhatsapp" style="display: none">
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
                        <div class="col-md-4 disnormalwhatsapp" style="display: none">
                            <div class="mb-3">
                                <label for="send-template-contact-type" class="form-label">Contact Type <span class="text-danger">*</span></label>
                                <select name="contact_type_normal" id="send-template-contact-type" class="form-select select22" data-placeholder="Select Contact Type" data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="all">All</option>
                                    <option value="owner">Owner</option>
                                    <option value="Primary">Primary</option>
                                    <option value="Secondary">Secondary</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 disnormalwhatsapp" style="display: none">
                            <div class="mb-3">
                                <label for="send-template-personalise-class" class="form-label">Personalise Name</label>
                                <select name="personalise_class" id="send-template-personalise-class" class="form-select select22" data-allow-clear="true" data-placeholder="Select Personalise Class">
                                    <option value=""></option>
                                    <option value="[Others]">Other</option>
                                    <option value="[Office Name (English)]">Office Name (English)</option>
                                    <option value="[Office Name (Arabic)]">Office Name (Arabic)</option>
                                    <option value="[Office Number]">Office Number</option>
                                    <option value="[Office Email]">Office Email</option>
                                    <option value="[Owner Name]">Owner Name</option>
                                    <option value="[Owner Contact]">Owner Contact</option>
                                    <option value="[Owner Email]">Owner Email</option>
                                    <option value="[Country]">Country</option>
                                    <option value="[City]">City</option>
                                    <option value="[Primary Concern Person]">Primary Concern Person</option>
                                    <option value="[Primary Contact No]">Primary Contact No</option>
                                    <option value="[Primary Email]">Primary Email</option>
                                    <option value="[Secondary Concern Person]">Secondary Concern Person</option>
                                    <option value="[Secondary Contact No]">Secondary Contact No</option>
                                    <option value="[Secondary Email]">Secondary Email</option>
                                    <option value="[Concern Person 3]">Concern Person 3</option>
                                    <option value="[Contact No 3]">Contact No 3</option>
                                    <option value="[Concern Person 4]">Concern Person 4</option>
                                    <option value="[Contact No 4]">Contact No 4</option>
                                    <option value="[Concern Person 5]">Concern Person 5</option>
                                    <option value="[Contact No 5]">Contact No 5</option>
                                    <option value="[Concer Person 6]">Concer Person 6</option>
                                    <option value="[Contact No 6]">Contact No 6</option>
                                    <option value="[Status]">Work Status</option>
                                </select>
                            </div>
                        </div>
                        <!-- Normal Whatsapp Display End -->
                    </div>
                    <!-- Meta Whatsapp Display Start -->
                    
                    <div class="row dismetawhatsapp">
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
                    <div class="row dismetawhatsapp">
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
                    <div class="row disnormalwhatsapp" style="display: none">
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
                    <div class="row disnormalwhatsapp" style="display: none">
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
        <div class="modal fade" id="bulktransferleadowner" aria-hidden="true" aria-labelledby="bulktransferleadownerLabel" tabindex="-1">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulktransferleadownerLabel">Transfer Lead Owner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.bulkleadownertransfer') }}" method="POST" id="bulktransferleadownevalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDLTR" id="contactIDLTRB">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="transfer-lead-owner" class="form-label">Transfer Lead Owner <span class="text-danger">*</span></label>
                                        <select name="leadowner_id" id="transfer-lead-owner" class="form-select select22" data-placeholder="Select Lead Owner" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($adminusers as $adminuserl)
                                                <option value="{{ $adminuserl->id }}">{{ $adminuserl->name }}</option>
                                            @endforeach
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
        <div class="modal fade" id="bulktransfercareoff" aria-hidden="true" aria-labelledby="bulktransfercareoffLabel" tabindex="-1">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulktransfercareoffLabel">Transfer Careoff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.bulkcareofftransfer') }}" method="POST" id="bulktransfercareoffvalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDCTR" id="contactIDCTR">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="transfer-careoff" class="form-label">Transfer Careoff <span class="text-danger">*</span></label>
                                        <select name="careoff_id" id="transfer-careoff" class="form-select select22" data-placeholder="Select Careoff" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($adminusers as $adminuser2)
                                                <option value="{{ $adminuser2->id }}">{{ $adminuser2->name }}</option>
                                            @endforeach
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

        <!-- Bulk Group Transfer Start -->
        <div class="modal fade" id="bulktransfergroup" aria-hidden="true" aria-labelledby="bulktransfergroupLabel" tabindex="-1">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulktransfergroupLabel">Transfer Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.bulkgrouptransfer') }}" method="POST" id="bulktransfergroupvalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDGRPTR" id="contactIDGRPTR">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <p id="totlaTransferGrp" class="text-success"></p>
                                        <label for="transfer-groupm" class="form-label">Transfer Group <span class="text-danger">*</span></label>
                                        <select name="group_id" id="transfer-groupm" class="form-select select22" data-placeholder="Select Group" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($groupms as $groupm)
                                                <option value="{{ $groupm->id }}">{{ $groupm->name }}</option>
                                            @endforeach
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
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('admin/assets/pages/app-contactp-list.js') }}"></script>

    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/contactp-validation.js') }}"></script>

    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('.select222').select2();
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#deleteStaff').on("show.bs.modal",function(e){
                var deleteID = $(e.relatedTarget).data('id');
                $('#contactID2').val(deleteID);
            });
        });
    </script>

    <script>
        $(document).on('input','.checkNoExistance',function(){
            let mobileNo = $(this).val();
            let errorElement = $(this).closest('.mb-3').find('.errorShowMob');
            let EditID = $('#edit_ID').val();
            // alert(EditID);
            if (mobileNo != '') {
                jQuery.ajax({
                    url: "{{ route('admin.contact.checkmobileNo') }}",
                    method: "GET",
                    type: "html",
                    data:{
                        "mobile_no": mobileNo,
                        "edit_id": EditID
                    },
                    success: function(data){
                        console.log(data);
                        if (data.response_status == 1) {
                            $(errorElement).text(data.response_msg);
                        } else {
                            $(errorElement).text(data.response_msg);
                        }
                    }
                });                
            }else{
                $(errorElement).text('');
            }


        });
    </script>

    <script>
        $(document).ready(function(){
            $("#transfer-groupm").on("change",function(){
                var groupValue = $(this).val();
                var totalsend = $("#contactIDGRPTR").val();

                if (groupValue != '') {
                    jQuery.ajax({
                        url: "{{ route('admin.contact.checkgroupLimit') }}",
                        method: "GET",
                        type: "html",
                        data:{
                            "grpID": groupValue,
                            "totalSend": totalsend
                        },
                        success: function(data){

                            $("#respMessageGroupTransfer").text(data.message);
                            $('#respGroupLimit').text(data.grouplimit);                            
                            if (data.status == 1) {
                                $(".dibtngrp").attr("disabled",false);
                            } else {
                                $(".dibtngrp").attr("disabled",true);                                
                            }

                        }
                    });                    
                } else {
                    $("#respMessageGroupTransfer").text(""); 
                    $('#respGroupLimit').text("");
                    $(".dibtngrp").attr("disabled",false);
                }
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
                    url : '{{ url("admin/contact-list/edit") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#edit_ID').val(data.id);
                        $("#edit-business-type").val(data.businesstype_id).trigger('change');
                        $('#edit-office-name-eng').val(data.office_eng_name);
                        $('#edit-office-name-ar').val(data.office_ar_name);
                        // $('#edit-office-no').val(data.office_no);
                        // $('#edit-office-email').val(data.office_email);
                        // $('#edit-owner-name').val(data.owner_name);
                        // $('#edit-owner-contact').val(data.owner_contact);
                        // $('#edit-owner-email').val(data.owenr_email);
                        $('#edit-country-id').val(data.country_id).change();
                        $('#edit-city-id').val(data.city_id).change();
                        $('#edit-primary-person').val(data.prim_concern_name);
                        $('#edit-primary-contact').val(data.prim_contact);
                        $('#edit-primary-email').val(data.prim_email);
                        $('#edit-secondary-person').val(data.sec_concern_name);
                        $('#edit-secondary-contact').val(data.sec_contact);
                        $('#edit-secondary-email').val(data.sec_email);
                        $('#edit-person3').val(data.concern_name3);
                        $('#edit-contact3').val(data.contact3);
                        $('#edit-person4').val(data.concern_name4);
                        $('#edit-contact4').val(data.contact4);
                        $('#edit-person5').val(data.concern_name5);
                        $('#edit-contact5').val(data.contact5);
                        $('#edit-person6').val(data.concern_name6);
                        $('#edit-contact6').val(data.contact6);
                        $('#edit-contact7').val(data.contact7);
                        $('#edit-contact8').val(data.contact8);
                        $('#edit-contact9').val(data.contact9);
                        $('#edit-contact10').val(data.contact10);
                        $('#edit-contact11').val(data.contact11);
                        $('#edit-contact12').val(data.contact12);
                        $('#edit-careoff').val(data.careoff_id).change();
                        $('#edit-leadowner').val(data.leadowner_id).change();
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            // Trigger Status if avalable
            $("#updateLstage").on("show.bs.modal",function(e){
                var contactID = $(e.relatedTarget).data('id');
                $('#contactID').val(contactID);

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url("admin/contact-list/edit") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": contactID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $("#update-lead-cycle-status").val(data.lcs_id).change();
                    }
                });

            });
            
            
            $('#update-lead-cycle-status').on('change',function(){
                var lcs_id = $(this).val();
                var contact_id = $("#contactID").val();
                
                $("#update-lead-stage").empty();

                jQuery.ajax({
                    url: "{{ url('admin/contacts/getstage') }}",
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
        });
    </script>
    <script>
        $(document).ready(function(){
            $('#add-business-type').on('change',function(){
                var value = $(this).val();
                if (value == 2 || value == '') {
                    $('.showDIv').hide();
                } else {
                    $('.showDIv').show();
                }
            });

            $('#edit-business-type').on('change',function(){
                var value = $(this).val();
                if (value == 2 || value == '') {
                    $('.showDIv2').hide();
                } else {
                    $('.showDIv2').show();
                }
            });

        });
    </script>

    <script>
        $(document).ready(function(){
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
        });
    </script>

    <script>
        $(document).ready(function(){
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

                    $('#contactIDCTR').val(join_all_selected_values);


                }

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $("#bulktransfercareoff").on('show.bs.modal',function(e){
                var allselectedvals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });

                if (allselectedvals <= 0) {

                }else{
                    var countc_id = allselectedvals.length;
                    var join_all_selected_values = allselectedvals.join(",");

                    $('#contactpID2').val(join_all_selected_values);
                }

            });
        });
    </script>

    <script>
        $(document).ready(function(){
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
        });
    </script>

    <script>
        $(function(){
            // ID selector on Master Chec
            var masterCheck = $('#checkboxSelectAll');
            
            var listcheckitem = $('.listitem:checkbox');

            

            masterCheck.on('click',function(){
           
                var isMasterChecked = $(this).is(":checked");

                if (isMasterChecked) {
                    $('.bulkactions').attr("disabled",false);
                } else {
                    $('.bulkactions').attr("disabled",true);
                }
            });

            $('.datatables-users tbody').on("change", 'input[type="checkbox"]',function(){
                // var allchecked = true;
                // if (!this.checked) {
                //     allchecked = false;
                // } 

                var isCheckboxchecked = $(this).is(":checked");

                var allselectedvals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });

                totalCheckItem = allselectedvals.length;


                
                if (totalCheckItem > 0) {
                    $('.bulkactions').attr("disabled",false);                    
                } else {
                    $('.bulkactions').attr("disabled",true);
                }
                

                

                // if (isCheckboxchecked) {
                //     $('.bulkactions').attr("disabled",false);
                // } else {
                //     $('.bulkactions').attr("disabled",true);
                // }

            });


            
            // listcheckitem.on("change",function(){
            //     // Total Checkboxes in list
            //     var totalItems = listcheckitem.length;

            //     // Total Checked Checkboxes in list
            //     var checkedItems = listcheckitem.filter(":checked").length;
            //     if (totalItems == checkedItems) {
            //         $('.bulkactions').attr("disabled",false);
            //     }else if(checkedItems > 0 && checkedItems < totalItems){
            //         $('.bulkactions').attr("disabled",false);
            //     }else{
            //         $('.disBulkbtn').prop("disabled",true);
            //     }
            // });


        });

    </script>



    <script>
        $(document).ready(function(){
            toastr.options = {
                "timeOut": 5000,
                "showDuration": 300,
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut",
            };

            // $(document).on('click','#submitShortMsg',function(e){
            //     e.preventDefault();
            //     var formdata = $('#shortsubmitcontact').serialize();



            //     $.ajax({
            //         url: "{{ route('admin.contact.shortstore') }}",
            //         method: "POST",
            //         data: formdata,
            //         success: function(response){
            //             toastr.success(response.success);
            //             $('.nav-form-short-load').load(" .nav-form-short-load");
            //             $('.datatables-users').DataTable().ajax.reload();
            //         }
            //     });
            // });



            
            
            
            // Short Contact Form Save

            $(document).on('click','#submitShortMsg',function(e){
                e.preventDefault();
                var formdata = $('#shortsubmitcontact').serialize();
                const select22 = $('.select22');
                var shortsubmitForm = $(' .shortsubmitcontact ');
                shortsubmitForm.validate({
                    rules:{
                        businesstype_id:{
                            required: true
                        }
                    },
                    messages:{
                        businesstype_id: "Please Select Business Type"
                    },
                    // submitHandler: function (form){
                    //     $.ajax({
                    //         url: form.action,
                    //         type: form.method,
                    //         data: $(form).serialize(),
                    //         success: function(response){
                    //             toastr.success(response.success);
                    //             $('.nav-form-short-load').load(" .nav-form-short-load");
                    //             $('.datatables-users').DataTable().ajax.reload();

                    //         }
                    //     });
                    // }
                });


                if (shortsubmitForm.valid()) {

                    

                    $.ajax({
                        url: "{{ route('admin.contact.shortstore') }}",
                        method: "POST",
                        data: formdata,
                        success: function(response){
                            toastr.success(response.success);
                            shortsubmitForm[0].reset();
                            // $('.nav-form-short-load').load(" .nav-form-short-load");
                            $('.datatables-users').DataTable().ajax.reload();
                            
                            if (select22.length) {
                                select22.each(function () {
                                    var $this = $(this);
                                    $this.wrap('<div class="position-relative"></div>').select2({
                                    //   placeholder: 'Select value',
                                    dropdownParent: $this.parent()
                                    });
                                });
                            }

                        }
                    });    
                }

                return false;
            });

            // Update Lead Status and Stage
            $(document).on('click','#leadStageUpdateBtn',function(e){
                e.preventDefault();
                const select22 = $('.select22');
                var formdata = $('#updateLeadStageValidation').serialize();
                var leadupdateForm = $(' #updateLeadStageValidation ');
                leadupdateForm.validate({
                    rules:{
                        lcs_id:{
                            required: true
                        },
                        ls_id:{
                            required: true
                        }
                    },
                    messages:{
                        lcs_id:{
                            required: "Please Select Life Cycle Status"
                        },
                        ls_id:{
                            required: "Please Select Lead Stage"
                        }
                    },

                });

                if (leadupdateForm.valid()) {
                    $.ajax({
                        url: "{{ route('admin.contact.stageUpdate') }}",
                        method: "POST",
                        data: formdata,
                        success: function(response){
                            toastr.success(response.success);
                            leadupdateForm[0].reset();
                            // $('.nav-form-short-load').load(" .nav-form-short-load");
                            $('.datatables-users').DataTable().ajax.reload();
                            $('#updateLstage').modal('hide');
                            if (select22.length) {
                                select22.each(function () {
                                    var $this = $(this);
                                    $this.wrap('<div class="position-relative"></div>').select2({
                                    //   placeholder: 'Select value',
                                    dropdownParent: $this.parent()
                                    });
                                });
                            }

                        }
                    });    
                }

                return false;
            });

            // Add New Contacp 
            $(document).on('click','#addnewcandidateBtn',function(e){
                e.preventDefault();
                const select22 = $('.select22');
                var formdata = $('#addNewUserForm').serialize();
                var addnewcandidateForm = $(' #addNewUserForm ');
                addnewcandidateForm.validate({
                    rules:{
                        businesstype_id:{
                            required: true
                        },
                        country_id:{
                            required: true
                        },
                        careoff_id:{
                            required: true
                        },
                        leadowner_id:{
                            required: true
                        }
                    },
                    messages: {
                        businesstype_id:{
                            required: "Please Select Business Type"
                        },
                        country_id:{
                            required: "Please Select Country"
                        },
                        careoff_id:{
                            required: "Please Select Careoff"
                        },
                        leadowner_id:{
                            required: "Please Select Lead Owner"
                        }
                    },
                });
                if (addnewcandidateForm.valid()) {
                    $.ajax({
                        url: "{{ route('admin.contact.store') }}",
                        method: "POST",
                        data: formdata,
                        success: function(response){
                            toastr.success(response.success);
                            $('.errorShowMob').text('');
                            addnewcandidateForm[0].reset();
                            $('.datatables-users').DataTable().ajax.reload();
                            $('#offcanvasAddUser').offcanvas('hide');
                            if (select22.length) {
                                select22.each(function () {
                                    var $this = $(this);
                                    $this.wrap('<div class="position-relative"></div>').select2({
                                    //   placeholder: 'Select value',
                                    dropdownParent: $this.parent()
                                    });
                                });
                            }
                        }
                    });
                }
                return false;
            });

            // Edit New Contactp
            $(document).on('click','#editnewcontact',function(e){
                e.preventDefault();
                const select22 = $('.select22');
                var formdata = $('#editUserForm').serialize();
                var editnewcandidateForm = $(' #editUserForm ');

                editnewcandidateForm.validate({
                    rules:{
                        businesstype_id:{
                            required: true
                        },
                        country_id:{
                            required: true
                        },
                        careoff_id:{
                            required: true
                        },
                        leadowner_id:{
                            required: true
                        }
                    },
                    messages: {
                        businesstype_id:{
                            required: "Please Select Business Type"
                        },
                        country_id:{
                            required: "Please Select Country"
                        },
                        careoff_id:{
                            required: "Please Select Careoff"
                        },
                        leadowner_id:{
                            required: "Please Select Lead Owner"
                        }
                    },
                });

                if (editnewcandidateForm.valid()) {
                    $.ajax({
                        url: "{{ route('admin.contact.update') }}",
                        method: "POST",
                        data: formdata,
                        success: function(response){
                            toastr.success(response.success);
                            $('.errorShowMob').text('');
                            editnewcandidateForm[0].reset();
                            $('.datatables-users').DataTable().ajax.reload();
                            $('#edituser').offcanvas('hide');
                            if (select22.length) {
                                select22.each(function () {
                                    var $this = $(this);
                                    $this.wrap('<div class="position-relative"></div>').select2({
                                    //   placeholder: 'Select value',
                                    dropdownParent: $this.parent()
                                    });
                                });
                            }
                        }
                    });
                }
                return false;

            });


        });

    </script>

    <script>
        jQuery.validator.setDefaults({
            errorElement: "em",
            errorPlacement: function(error, element){
                if (element.parent().hasClass('input-group') || element.hasClass('.form-group') || element.hasClass('mb-3') || element.attr('type') == 'checkbox') {
                    error.insertAfter(element.parent());
                }else{
                    error.insertAfter(element);
                }

                if (element.parent().hasClass('input-group')) {
                    element.parent().addClass('is-invalid');
                }
            },
            highlight: function ( element, errorClass, validClass ) {
                $( element ).parents( ".mb-3" ).addClass( "is-invalid" ).removeClass( "is-valid" );
            },
            unhighlight: function (element, errorClass, validClass) {
                $( element ).parents( ".mb-3" ).addClass( "is-valid" ).removeClass( "is-invalid" );
            }
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

            $('#groupnamef').click(function(){
                $('.group-name-div').toggle();
            });

            $('#lifecyclestatusf').click(function(){
                $('.life-cycle-status-div').toggle();
            });

            $('#leadstagef').click(function(){
                $('.lead-stage-div').toggle();
            });

            $('#businesstypef').click(function(){
                $('.business-type-div').toggle();
            });

            $('#createddatef').click(function(){
                $('.created-date-div').toggle();
            });

            
            
            

        

            // Basic Select
            $('#default-chk').click(function(){
                if ($('#createddatef:checkbox:checked').length > 0) {
                    $('#createddatef').trigger('click');
                }

                if ($('#businesstypef:checkbox:checked').length > 0) {
                    $('#businesstypef').trigger('click');
                }

                if ($('#groupnamef:checkbox:checked').length > 0) {
                    $('#groupnamef').trigger('click');
                }

                if ($('#cityf:checkbox:checked').length > 0) {
                    $('#cityf').trigger('click');
                }

                if ($('#countryf:checkbox:checked').length > 0) {
                    $('#countryf').trigger('click');
                }

            

                if($('#leadstagef:checkbox:checked').length > 0){

                }else{
                    $('#leadstagef').trigger('click');
                }

                if($('#lifecyclestatusf:checkbox:checked').length > 0){

                }else{
                    $('#lifecyclestatusf').trigger('click');
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

                $('input[name="groupnamef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="lifecyclestatusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="leadstagef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="businesstypef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="createddatef"]').each(function () {
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
                if($('#groupnamef:checkbox:checked').length > 0){
                    $('#groupnamef').trigger('click');
                }
                if($('#lifecyclestatusf:checkbox:checked').length > 0){
                    $('#lifecyclestatusf').trigger('click');
                }
                if($('#leadstagef:checkbox:checked').length > 0){
                    $('#leadstagef').trigger('click');
                }
                if($('#businesstypef:checkbox:checked').length > 0){
                    $('#businesstypef').trigger('click');
                }
                if($('#createddatef:checkbox:checked').length > 0){
                    $('#createddatef').trigger('click');
                }


            });

            // Update Checkbox
            $('#update-chk').click(function(){
                var countryf = $('#countryf:checked').val();
                var cityf = $('#cityf:checked').val();
                var groupnamef = $('#groupnamef:checked').val();
                var lifecyclestatusf = $('#lifecyclestatusf:checked').val();
                var leadstagef = $('#leadstagef:checked').val();
                var businesstypef = $('#businesstypef:checked').val();
                var createddatef = $('#createddatef:checked').val();
                

                jQuery.ajax({
                    url:"{{ url('admin/contactp/filterList/update') }}",
                    method: 'post',
                    type: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        countryf: countryf,
                        cityf: cityf,
                        groupnamef: groupnamef,
                        lifecyclestatusf: lifecyclestatusf,
                        leadstagef: leadstagef,
                        businesstypef: businesstypef ,
                        createddatef: createddatef,
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

    <!-- Bulk Send Whatsapp Start -->
    <script>
        $(document).ready(function(){
            $('#send-whatsapp-type').on('change',function(){
                var sendwhatsapptype = $(this).val();
                if (sendwhatsapptype == "meta_whatsapp") {
                    $('.dismetawhatsapp').show();
                    $('.disnormalwhatsapp').hide();
                } else if(sendwhatsapptype == "normal_whatsapp"){
                    $('.dismetawhatsapp').hide();
                    $('.disnormalwhatsapp').show();
                } else {
                    $('.dismetawhatsapp').hide();
                    $('.disnormalwhatsapp').hide();
                }
            });
        });
    </script>

    <script>
        $(document).ready(function(){
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
        });
    </script>

    <script>
        $(document).ready(function(){
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
        });
    </script>





    <!-- Bulk Send Whatsapp End -->

@endsection