@extends('layout.admin.admin_layout')

@section('title','Unsubscribe Report')

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
        .pagestyle{
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out
        }
        .pagestyle:focus{
            color: #6e6b7b;
            background-color: #fff;
            border-color: #7367f0;
            outline: 0;
            box-shadow: 0 3px 10px 0 rgba(34, 41, 47, 0.1);
        }
    </style>

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">

        <!--- Search Filter Start -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-3">Search Filter</h5>
                <div class="row">
                    <div class="col-md-3 mb-3 country-div">
                        <select id="by-country" class="form-select select222" multiple data-placeholder="Select Country">
                            <option value="">Select Country</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 city-div">
                        <select id="by-city" class="form-select select222" multiple data-placeholder="Select City">
                            <option value="">Select City</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 group-name-div">
                        <select id="by-group" class="form-select select222" multiple data-placeholder="Select Group Name">
                            <option value="">Select Group</option>
                            <option value="gb0">Null</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 life-cycle-status-div">
                        <select id="by-lifecycle-status" class="form-select select222" multiple data-placeholder="Select Life Cycle Status">
                            <option value="">Select Life Cycle Status</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 lead-stage-div">
                        <select id="by-lead-stage" class="form-select select222" multiple data-placeholder="Select Lead Stage">
                            <option value="">Select Lead Stage</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 business-type-div">
                        <select id="by-business-type" class="form-select select222" multiple data-placeholder="Select Business Type">
                            <option value="">Select Business Type</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <select name="" id="by-send-tag" class="form-select select222" multiple data-placeholder="Select Whatsapp Send Tag...">
                            <option value="">Select Whatsapp Send Tag</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <input name="" type="text" id="by-send-date" class="form-control senddate-picker" placeholder="Send Date...">
                    </div>

                    <div class="col-md-3 mb-3 created-date-div">
                        <input type="text" id="by-created-date" class="form-control createdate-picker" placeholder="Created date...">
                    </div>

                    <div class="col-md-3 mb-3 createdby-div">
                        <select name="" id="by-createdby" class="form-select select222" multiple data-placeholder="Select created by...">
                            <option value="">Select Created By</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!-- Search Filter End -->

        <!-- Users List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <div class="mb-1 float-start">
                    {{-- <label for="">Show</label> --}}
                    <select id="pagination_list" class="pagestyle">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                    </select>
                </div>
                <div class="mb-1 float-end">
                    <input type="text" id="search_text" class="form-control" placeholder="Search...">
                </div>
            </div>
            <div class="card-datatable table-responsive contactpaginate">
                {{-- @include('admin.contactp.loadcontact') --}}
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input checkboxSelectAll" type="checkbox" id="checkboxSelectAll" />
                                    <label class="form-check-label" for="checkboxSelectAll"></label>
                                </div>
                            </th>
                            <th>Full Name</th>
                            <th>Agency Name</th>
                            <th>Lead Stage</th>
                            <th>City</th>
                            <th>Country</th>
                            <th>Unsubscribe Date</th>
                        </tr>
                    </thead>
                    <tbody class="listitem">
                        @if (count($posts) > 0)
                            @foreach ($posts as $post)
                                @php
                                    $stateNum = rand(0,6);
                                    $states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                                    $state = $states[$stateNum];
                                    $fullname = explode(" ",$post->prim_concern_name);
                                    $firstword = current($fullname);
                                    $lastword = end($fullname);
                                    $firstCharacter = substr($firstword, 0, 1);
                                    $lastCharacter = substr($lastword, 0, 1);
                                    $defaultProfile = strtoupper($firstCharacter.$lastCharacter);

                                    if ($post->ls_id != '') {
                                        $lead_stage = $post->ls->name;
                                    }else {
                                        $lead_stage = "None";
                                    }

                                @endphp

                                <tr>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input dt-checkboxes sub-chk" data-id="{{ $post->id }}" type="checkbox" value="{{ $post->id }}" id="checkbox{{ $post->id }}" />
                                            <label class="form-check-label" for="checkbox{{ $post->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center user-name">
                                            <div class="avatar-wrapper">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded-circle bg-label-{{ $state }}">{{ $defaultProfile }}</span>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                @if ($post->prim_concern_name != '')
                                                    <a href="{{ route('admin.contact.show',$post->id) }}" class="text-body text-truncate" target="_blank"><span class="fw-semibold">{{ $post->prim_concern_name }}</a>
                                                @else
                                                    <a href="{{ route('admin.contact.show',$post->id) }}" class="text-body text-truncate" target="_blank"><span class="fw-semibold">{{ '---' }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>@if($post->office_eng_name != '') {{ $post->office_eng_name }} @else {{ '---' }} @endif</td>
                                    <td class="tdListFetch{{ $post->id }}"><span class='text-truncate d-flex align-items-center' data-bs-toggle='modal' data-bs-target='#updateLstage' data-id='{{ $post->id }}'>{{ $lead_stage }} <i class='ti ti-chevron-down ti-sm me-2'></i></span></td>
                                    <td>@if($post->city_id != '') {{ $post->city->name }} @else {{ '---' }} @endif</td>
                                    <td>@if($post->country_id != '') {{ $post->country->name }} @else {{ '---' }} @endif</td>
                                    <td>@if($post->unsubscribe_date != '') {{ date('d-m-Y h:',strtotime($post->unsubscribe_date)) }} @else {{ '---' }} @endif</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center">No data found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Delete Contactplus Start -->

        <div class="modal fade" id="deleteStaff" aria-hidden="true" aria-labelledby="deleteStaffLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Delete Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.contact.delete') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="contactID" id="contactID2">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="mb-3">
                                      <p class="text-danger">Are you sure to delete contact?</p>
                                   </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Delete</button>
                         </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Contactplus Start -->


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

                            <div class="form-check mt-2" id="createby-f">
                                <input class="form-check-input" type="checkbox" name="createbyf" value="1" id="createbyf" @if(isset($contactpfilter) && $contactpfilter->created_by == 1) checked @endif />
                                <label class="form-check-label" for="createbyf"> Created By</label>
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
    {{-- <script src="{{ asset('admin/assets/pages/app-contactp-list.js') }}"></script> --}}

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
            $('#send-template-name').on('change',function(){
                var template_id = $(this).val();

                var imgPath = "{{ asset('admin/assets/images/template') }}";
                var blankImg = "{{ asset('admin/assets/img/avatars/blank.jpeg') }}";

                if (template_id != '') {
                    jQuery.ajax({
                        url : "{{ url('admin/whatsapp-campaign-list/template/get') }}",
                        method: "GET",
                        type: "html",
                        data: {
                            "id": template_id,
                            // "_token": "{{ csrf_token() }}",
                        },
                        success: function(data){


                            $('#send-template-message').val(data.msg_whatsapp);
                            $('#send-template-message-ar').val(data.msg_whatsapp_ar);
                            if (data.file != '') {
                                var file_path = imgPath+'/'+data.file;
                                $('.uploadedAvatar2').attr("src",file_path);
                            } else {
                                $('.uploadedAvatar2').attr("src",blankImg);
                            }
                        }
                    });
                } else {
                    $('#send-template-message').val('');
                    $('#send-template-message-ar').val('');
                    $('.uploadedAvatar2').attr("src",blankImg);
                }
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

    {{-- <script>
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

    </script> --}}


    {{-- <script>
        $(function () {
            // ID selector on Master Checkbox
            var masterCheck = $('.checkboxSelectAll');
            // ID selector on Items Container
            var listcheckitem = $('.listitem :checkbox');
            // Click Event on Master Check
            masterCheck.on("click", function() {
                var isMasterChecked = $(this).is(":checked");
                if(isMasterChecked){
                    $('.bulkactions').prop("disabled",false);
                }else{
                    $('.bulkactions').prop("disabled",true);
                }
                listcheckitem.prop("checked", isMasterChecked);
            });
            // Change Event on each item checkbox
            listcheckitem.on("change", function() {
                // Total Checkboxes in list
                var totalItems = listcheckitem.length;
                // Total Checked Checkboxes in list
                var checkedItems = listcheckitem.filter(":checked").length;
                //If all are checked
                if (totalItems == checkedItems) {
                    masterCheck.prop("indeterminate", false);
                    masterCheck.prop("checked", true);
                    $('.bulkactions').prop("disabled",false);
                }
                // Not all but only some are checked
                else if (checkedItems > 0 && checkedItems < totalItems) {
                    masterCheck.prop("indeterminate", true);
                    $('.bulkactions').prop("disabled",false);
                }
                //If none is checked
                else {
                    masterCheck.prop("indeterminate", false);
                    masterCheck.prop("checked", false);
                    $('.bulkactions').prop("disabled",true);
                }
            });
        });
    </script> --}}

    <script>
        // Click Event on Master Check
        $(document).on('click','.checkboxSelectAll',function(){
            var listcheckitem = $('.listitem :checkbox');
            var isMasterChecked = $(this).is(":checked");

            // row checked list
            var checkedList = listcheckitem.length;

            // alert(checkedList);

            if(isMasterChecked){
                if (checkedList > 0) {
                    $('.bulkactions').prop("disabled",false);
                } else {
                    $('.bulkactions').prop("disabled",true);
                }

            }else{
                $('.bulkactions').prop("disabled",true);
            }
            listcheckitem.prop("checked", isMasterChecked);
        });

        // Change Event on each item checkbox
        $(document).on('change','.listitem :checkbox',function(){
            var listcheckitem = $('.listitem :checkbox');
            var masterCheck = $('.checkboxSelectAll');
            // Total Checkboxes in list
            var totalItems = listcheckitem.length;
            // Total Checked Checkboxes in list
            var checkedItems = listcheckitem.filter(":checked").length;
            //If all are checked
            if (totalItems == checkedItems) {
                masterCheck.prop("indeterminate", false);
                masterCheck.prop("checked", true);
                $('.bulkactions').prop("disabled",false);
            }
            // Not all but only some are checked
            else if (checkedItems > 0 && checkedItems < totalItems) {
                masterCheck.prop("indeterminate", true);
                $('.bulkactions').prop("disabled",false);
            }
            //If none is checked
            else {
                masterCheck.prop("indeterminate", false);
                masterCheck.prop("checked", false);
                $('.bulkactions').prop("disabled",true);
            }
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
                            // $('.datatables-users').DataTable().ajax.reload();
                            $('.datatables-users').load(' .datatables-users');
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
                // Page Redirect URL
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var country = $('#by-country').val();
                var city = $('#by-city').val();
                var lcs = $('#by-lifecycle-status').val();
                var ls = $('#by-lead-stage').val();
                var business_type = $('#by-business-type').val();
                var send_tag = $('#by-send-tag').val();
                var by_send_date = $('#by-send-date').val();
                var by_created_date = $('#by-created-date').val();
                var by_created_by = $('#by-createdby');
                var groupm = $('#by-group').val();
                var contactID = $('#contactID').val();
                var tdListFetch = ".tdListFetch"+contactID;

                var current_page = $('.pagination .active span').text();

                if (current_page.length > 0){
                    var getPaginationURL = $('.pagination a').attr('href').split('page=')[0];
                    var newPaginationURL = getPaginationURL+"page="+current_page;

                }else{
                    var getPaginationURL = window.location.href;

                    var newPaginationURL = getPaginationURL+"?page_list="+page_list+"&search_text="+search_text+"&country_id="+country+"&city_id="+city+"&lcs_id="+lcs+"&ls_id="+ls+"&businesstype_id="+business_type+"&send_tag="+send_tag+"&send_date="+by_send_date+"&created_at="+by_created_date+"&group_id="+groupm;
                }


                //alert(newPaginationURL);

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
                            // $('.datatables-users').DataTable().ajax.reload();

                            //$(tdListFetch).load(" "+tdListFetch);



                            // Send Request for page refresh
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



                            // Load Page
                            jQuery.ajax({
                                url: newPaginationURL,
                                method: "GET",
                                success: function(data){
                                    $('.contactpaginate').html(data);
                                }
                            });



                            /**jQuery.ajax({
                                url: "{{ route('admin.contact.list') }}",
                                method: "GET",
                                type: "html",
                                data: {
                                    page_list: page_list,
                                    search_text: search_text,
                                    country_id: country,
                                    city_id: city,
                                    lcs_id: lcs,
                                    ls_id: ls,
                                    businesstype_id: business_type,
                                    send_tag: send_tag,
                                    send_date: by_send_date,
                                    created_at: by_created_date,
                                    group_id: groupm
                                },
                                success: function(data){
                                    $('.contactpaginate').html(data);
                                }
                            });
                            **/





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

                // Page Redirect URL
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var country = $('#by-country').val();
                var city = $('#by-city').val();
                var lcs = $('#by-lifecycle-status').val();
                var ls = $('#by-lead-stage').val();
                var business_type = $('#by-business-type').val();
                var send_tag = $('#by-send-tag').val();
                var by_send_date = $('#by-send-date').val();
                var by_created_date = $('#by-created-date').val();
                var by_created_by = $('#by-createdby');
                var groupm = $('#by-group').val();
                var contactID = $('#contactID').val();
                var tdListFetch = ".tdListFetch"+contactID;

                var current_page = $('.pagination .active span').text();

                if (current_page.length > 0){
                    var getPaginationURL = $('.pagination a').attr('href').split('page=')[0];
                    var newPaginationURL = getPaginationURL+"page="+current_page;

                }else{
                    var getPaginationURL = window.location.href;

                    var newPaginationURL = getPaginationURL+"?page_list="+page_list+"&search_text="+search_text+"&country_id="+country+"&city_id="+city+"&lcs_id="+lcs+"&ls_id="+ls+"&businesstype_id="+business_type+"&send_tag="+send_tag+"&send_date="+by_send_date+"&created_at="+by_created_date+"&group_id="+groupm;
                }



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
                            // $('.datatables-users').DataTable().ajax.reload();
                            $('.datatables-users').load(' .datatables-users');
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

                            // Load Page
                            jQuery.ajax({
                                url: newPaginationURL,
                                method: "GET",
                                success: function(data){
                                    $('.contactpaginate').html(data);
                                }
                            });
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

                // Page Redirect URL
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var country = $('#by-country').val();
                var city = $('#by-city').val();
                var lcs = $('#by-lifecycle-status').val();
                var ls = $('#by-lead-stage').val();
                var business_type = $('#by-business-type').val();
                var send_tag = $('#by-send-tag').val();
                var by_send_date = $('#by-send-date').val();
                var by_created_date = $('#by-created-date').val();
                var groupm = $('#by-group').val();
                var contactID = $('#contactID').val();
                var tdListFetch = ".tdListFetch"+contactID;

                var current_page = $('.pagination .active span').text();

                if (current_page.length > 0){
                    var getPaginationURL = $('.pagination a').attr('href').split('page=')[0];
                    var newPaginationURL = getPaginationURL+"page="+current_page;

                }else{
                    var getPaginationURL = window.location.href;

                    var newPaginationURL = getPaginationURL+"?page_list="+page_list+"&search_text="+search_text+"&country_id="+country+"&city_id="+city+"&lcs_id="+lcs+"&ls_id="+ls+"&businesstype_id="+business_type+"&send_tag="+send_tag+"&send_date="+by_send_date+"&created_at="+by_created_date+"&group_id="+groupm;
                }

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
                            // $('.datatables-users').DataTable().ajax.reload();
                            $('.datatables-users').load(' .datatables-users');
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

                            // Load Page
                            jQuery.ajax({
                                url: newPaginationURL,
                                method: "GET",
                                success: function(data){
                                    $('.contactpaginate').html(data);
                                }
                            });

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

            $('#businesstypef').click(function(){
                $('.business-type-div').toggle();
            });

            $('#createddatef').click(function(){
                $('.created-date-div').toggle();
            });

            $('#createbyf').click(function(){
                $('.createdby-div').toggle();
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

                if ($('#createbyf:checkbox:checked').length > 0) {
                    $('#createbyf').trigger('click');
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


                $('input[name="createbyf"]').each(function () {
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

                if($('#createbyf:checkbox:checked').length > 0){
                    $('#createbyf').trigger('click');
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
                var createbyf = $('#createbyf:checked').val();


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
                        createbyf: createbyf,
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
                    $('#send-template-name').val('').trigger('change');
                    $('.dismetawhatsapp').show();
                    $('.disnormalwhatsapp').hide();
                    $('.disallforwhatsapp').show();
                } else if(sendwhatsapptype == "normal_whatsapp"){
                    $('#send-meta-template-name').val('').trigger('change');
                    $('.dismetawhatsapp').hide();
                    $('.disnormalwhatsapp').show();
                    $('.disallforwhatsapp').show();
                } else {
                    $('#send-meta-template-name').val('').trigger('change');
                    $('#send-template-name').val('').trigger('change');
                    $('.dismetawhatsapp').hide();
                    $('.disnormalwhatsapp').hide();
                    $('.disallforwhatsapp').hide();
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

    <script>
        $(document).ready(function(){
            $('#pagination_list').on('change',function(){
                var page_list = $(this).val();
                var search_text = $('#search_text').val();
                var country = $('#by-country').val();
                var city = $('#by-city').val();
                var lcs = $('#by-lifecycle-status').val();
                var ls = $('#by-lead-stage').val();
                var business_type = $('#by-business-type').val();
                var send_tag = $('#by-send-tag').val();
                var by_send_date = $('#by-send-date').val();
                var by_created_date = $('#by-created-date').val();
                var by_created_by = $('#by-createdby');
                var groupm = $('#by-group').val();

                jQuery.ajax({
                    url: "{{ route('admin.contact.list') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        page_list: page_list,
                        search_text: search_text,
                        country_id: country,
                        city_id: city,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: business_type,
                        send_tag: send_tag,
                        send_date: by_send_date,
                        created_at: by_created_date,
                        created_by: by_created_by,
                        group_id: groupm
                    },
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });
            });

            $('#search_text').on('input',function(){
                var search_text = $(this).val();
                var page_list = $('#pagination_list').val();
                var country = $('#by-country').val();
                var city = $('#by-city').val();
                var lcs = $('#by-lifecycle-status').val();
                var ls = $('#by-lead-stage').val();
                var business_type = $('#by-business-type').val();
                var send_tag = $('#by-send-tag').val();
                var by_send_date = $('#by-send-date').val();
                var by_created_date = $('#by-created-date').val();
                var by_created_by = $('#by-createdby');
                var groupm = $('#by-group').val();

                jQuery.ajax({
                    url: "{{ route('admin.contact.list') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        search_text: search_text,
                        page_list: page_list,
                        country_id: country,
                        city_id: city,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: business_type,
                        send_tag: send_tag,
                        send_date: by_send_date,
                        created_at: by_created_date,
                        created_by: by_created_by,
                        group_id: groupm
                    },
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });
            });

            $('#by-country').on('change',function(){
                var country = $(this).val();
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var city = $('#by-city').val();
                var lcs = $('#by-lifecycle-status').val();
                var ls = $('#by-lead-stage').val();
                var business_type = $('#by-business-type').val();
                var send_tag = $('#by-send-tag').val();
                var by_send_date = $('#by-send-date').val();
                var by_created_date = $('#by-created-date').val();
                var by_created_by = $('#by-createdby');
                var groupm = $('#by-group').val();

                jQuery.ajax({
                    url: "{{ route('admin.contact.list') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        country_id: country,
                        search_text: search_text,
                        page_list: page_list,
                        city_id: city,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: business_type,
                        send_tag: send_tag,
                        send_date: by_send_date,
                        created_at: by_created_date,
                        created_by: by_created_by,
                        group_id: groupm
                    },
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });
            });

            $('#by-city').on('change',function(){
                var city = $(this).val();
                var country = $('#by-country').val();
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var lcs = $('#by-lifecycle-status').val();
                var ls = $('#by-lead-stage').val();
                var business_type = $('#by-business-type').val();
                var send_tag = $('#by-send-tag').val();
                var by_send_date = $('#by-send-date').val();
                var by_created_date = $('#by-created-date').val();
                var by_created_by = $('#by-createdby');
                var groupm = $('#by-group').val();

                jQuery.ajax({
                    url: "{{ route('admin.contact.list') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        city_id: city,
                        country_id: country,
                        search_text: search_text,
                        page_list: page_list,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: business_type,
                        send_tag: send_tag,
                        send_date: by_send_date,
                        created_at: by_created_date,
                        created_by: by_created_by,
                        group_id: groupm
                    },
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });
            });

            $('#by-lifecycle-status').on('change',function(){
                var lcs = $(this).val();
                var ls = $('#by-lead-stage').val();
                var city = $('#by-city').val();
                var country = $('#by-country').val();
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var business_type = $('#by-business-type').val();
                var send_tag = $('#by-send-tag').val();
                var by_send_date = $('#by-send-date').val();
                var by_created_date = $('#by-created-date').val();
                var by_created_by = $('#by-createdby');
                var groupm = $('#by-group').val();

                jQuery.ajax({
                    url: "{{ route('admin.contact.list') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        city_id: city,
                        country_id: country,
                        search_text: search_text,
                        page_list: page_list,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: business_type,
                        send_tag: send_tag,
                        send_date: by_send_date,
                        created_at: by_created_date,
                        created_by: by_created_by,
                        group_id: groupm
                    },
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });
            });

            $('#by-lead-stage').on('change',function(){
                var ls = $(this).val();
                var lcs = $('#by-lifecycle-status').val();
                var city = $('#by-city').val();
                var country = $('#by-country').val();
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var business_type = $('#by-business-type').val();
                var send_tag = $('#by-send-tag').val();
                var by_send_date = $('#by-send-date').val();
                var by_created_date = $('#by-created-date').val();
                var by_created_by = $('#by-createdby');
                var groupm = $('#by-group').val();

                jQuery.ajax({
                    url: "{{ route('admin.contact.list') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        city_id: city,
                        country_id: country,
                        search_text: search_text,
                        page_list: page_list,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: business_type,
                        send_tag: send_tag,
                        send_date: by_send_date,
                        created_at: by_created_date,
                        created_by: by_created_by,
                        group_id: groupm
                    },
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });
            });


            $('#by-business-type').on('change',function(){
                var business_type = $(this).val();
                var ls = $('#by-lead-stage').val();
                var lcs = $('#by-lifecycle-status').val();
                var city = $('#by-city').val();
                var country = $('#by-country').val();
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var send_tag = $('#by-send-tag').val();
                var by_send_date = $('#by-send-date').val();
                var by_created_date = $('#by-created-date').val();
                var by_created_by = $('#by-createdby');
                var groupm = $('#by-group').val();

                jQuery.ajax({
                    url: "{{ route('admin.contact.list') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        city_id: city,
                        country_id: country,
                        search_text: search_text,
                        page_list: page_list,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: business_type,
                        send_tag: send_tag,
                        send_date: by_send_date,
                        created_at: by_created_date,
                        created_by: by_created_by,
                        group_id: groupm
                    },
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });
            });

            $('#by-send-tag').on('change',function(){
                var send_tag = $(this).val();
                var business_type = $('#by-business-type').val();
                var ls = $('#by-lead-stage').val();
                var lcs = $('#by-lifecycle-status').val();
                var city = $('#by-city').val();
                var country = $('#by-country').val();
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var by_send_date = $('#by-send-date').val();
                var by_created_date = $('#by-created-date').val();
                var by_created_by = $('#by-createdby');
                var groupm = $('#by-group').val();

                jQuery.ajax({
                    url: "{{ route('admin.contact.list') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        city_id: city,
                        country_id: country,
                        search_text: search_text,
                        page_list: page_list,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: business_type,
                        send_tag: send_tag,
                        send_date: by_send_date,
                        created_at: by_created_date,
                        created_by: by_created_by,
                        group_id: groupm
                    },
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });
            });


            $('#by-send-date').on('change',function(){
                var by_send_date = $(this).val();
                var send_tag = $('#by-send-tag').val();
                var business_type = $('#by-business-type').val();
                var ls = $('#by-lead-stage').val();
                var lcs = $('#by-lifecycle-status').val();
                var city = $('#by-city').val();
                var country = $('#by-country').val();
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var by_created_date = $('#by-created-date').val();
                var by_created_by = $('#by-createdby');
                var groupm = $('#by-group').val();

                jQuery.ajax({
                    url: "{{ route('admin.contact.list') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        city_id: city,
                        country_id: country,
                        search_text: search_text,
                        page_list: page_list,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: business_type,
                        send_tag: send_tag,
                        send_date: by_send_date,
                        created_at: by_created_date,
                        created_by: by_created_by,
                        group_id: groupm
                    },
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });
            });

            $('#by-created-date').on('change',function(){

                var by_created_date = $(this).val();
                var by_send_date = $('#by-send-date').val();
                var send_tag = $('#by-send-tag').val();
                var business_type = $('#by-business-type').val();
                var ls = $('#by-lead-stage').val();
                var lcs = $('#by-lifecycle-status').val();
                var city = $('#by-city').val();
                var country = $('#by-country').val();
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var groupm = $('#by-group').val();
                var by_created_by = $('#by-createdby');

                jQuery.ajax({
                    url: "{{ route('admin.contact.list') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        city_id: city,
                        country_id: country,
                        search_text: search_text,
                        page_list: page_list,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: business_type,
                        send_tag: send_tag,
                        send_date: by_send_date,
                        created_at: by_created_date,
                        created_by: by_created_by
                        group_id: groupm,
                    },
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });
            });


            $('#by-group').on('change',function(){
                var groupm = $(this).val();
                var by_created_date = $('#by-created-date').val();
                var by_send_date = $('#by-send-date').val();
                var send_tag = $('#by-send-tag').val();
                var business_type = $('#by-business-type').val();
                var ls = $('#by-lead-stage').val();
                var lcs = $('#by-lifecycle-status').val();
                var city = $('#by-city').val();
                var country = $('#by-country').val();
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var by_created_by = $('#by-createdby');


                jQuery.ajax({
                    url: "{{ route('admin.contact.list') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        city_id: city,
                        country_id: country,
                        search_text: search_text,
                        page_list: page_list,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: business_type,
                        send_tag: send_tag,
                        send_date: by_send_date,
                        created_at: by_created_date,
                        created_by: by_created_by,
                        group_id: groupm
                    },
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });
            });


            $('#by-createdby').on('change',function(){
                var groupm = $('#by-group').val();
                var by_created_date = $('#by-created-date').val();
                var by_send_date = $('#by-send-date').val();
                var send_tag = $('#by-send-tag').val();
                var business_type = $('#by-business-type').val();
                var ls = $('#by-lead-stage').val();
                var lcs = $('#by-lifecycle-status').val();
                var city = $('#by-city').val();
                var country = $('#by-country').val();
                var page_list = $('#pagination_list').val();
                var search_text = $('#search_text').val();
                var by_created_by = $(this).val();


                jQuery.ajax({
                    url: "{{ route('admin.contact.list') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        city_id: city,
                        country_id: country,
                        search_text: search_text,
                        page_list: page_list,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: business_type,
                        send_tag: send_tag,
                        send_date: by_send_date,
                        created_at: by_created_date,
                        created_by: by_created_by,
                        group_id: groupm
                    },
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });
            });


        });
    </script>

    <!-- Pagination Page Reload Start -->
    <script>
        $(function(){
          $('body').on('click','.pagination a',function(e){
            e.preventDefault();
            var url = $(this).attr('href');

            getPaginations(url);
            window.history.pushState("", url);
          });

          function getPaginations(url){


            $.ajax({
              url : url
            }).done(function(data){
              $('.contactpaginate').html(data);
            }).fail(function(){

              alert("Something gone wrong!")
            });
          }
        });
      </script>
    <!-- Pagination Page Reload End -->

@endsection
