@extends('layout.admin.admin_layout')

@section('title','Meta Campaign Show List')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/dropzone/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/quill/editor.css') }}" />

    @if (Auth::guard('admin')->user()->user_type == 2)
        @if (isset($perm) && $perm->add_template == 0)
        <style>
        .addtemplate{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->edit_template == 0)
        <style>
        .edtemplate{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->delete_template == 0)
        <style>
        .deltemplate{
            display: none !important;
        }
        </style>
        @endif
    @endif

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="float-end mb-2">
                    <a href="{{ route('admin.metawhatsapp.campaign') }}" class="btn btn-sm btn-primary">Back</a>
                </div>
            </div>
        </div>
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
        <!-- Users List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">Search Filter</h5>
                {{-- <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                    <div class="col-md-4 user_role"></div>
                    <div class="col-md-4 user_plan"></div>
                    <div class="col-md-4 user_status"></div>
                </div> --}}

                <div class="mb-1 float-end">
                    <div class="btn-group mx-2">
                        <button class="btn btn-primary btn-sm dropdown-toggle bulkactions" type="button" disabled id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Bulk Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfergroup"><i class="ti ti-brand-whatsapp"></i> Resend Message</a>
                        </div>
                    </div>
                </div>

                <div class="mb-1 float-end">
                    <button type="button" class="btn btn-danger btn-sm page-refresh"><i class="ti ti-refresh ti-xs"></i> Refresh</button>
                </div>

            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input checkboxSelectAll" type="checkbox" id="checkboxSelectAll" />
                                    <label class="form-check-label" for="checkboxSelectAll"></label>
                                </div>
                            </th>
                            <th>Campaign Name</th>
                            <th>Mobile No</th>
                            <th>Audience</th>
                            <th>Careoff</th>
                            <th>Sent Date</th>
                            <th>Status</th>
                            <th>Message Status</th>
                        </tr>
                    </thead>
                    <tbody class="listitem">
                        @if (count($postResponses) > 0)
                            @foreach ($postResponses as $postResponse)
                                @php
                                    if ($post->audience != '') {
                                        if($post->audience == 'contactp'){
                                            $audience = "Contact Plus";
                                        }else{
                                            $audience = $post->audience;
                                        }
                                    } else {
                                        $audience = "---";
                                    }

                                @endphp

                                <tr>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input dt-checkboxes sub-chk" data-id="{{ $postResponse->id }}" type="checkbox" value="{{ $postResponse->id }}" id="checkbox{{ $postResponse->id }}" />
                                            <label class="form-check-label" for="checkbox{{ $postResponse->id }}"></label>
                                        </div>
                                    </td>
                                    <td>{{ $post->campaign_name }}</td>
                                    <td>
                                        <a href="javascript:void(0);" 
                                        onclick="openMobileModal('{{ $postResponse->id }}', '{{ $postResponse->mobile_no }}', '{{ $post->audience }}')">
                                            {{ $postResponse->mobile_no }}
                                        </a>
                                    </td>                                  
                                      <td> {{ $audience }}</td>
                                    <td>
                                        @if ($post->careoffname != '')
                                            {{ $post->careoffname }}
                                        @else
                                            {{ '---' }}
                                        @endif
                                    </td>
                                    <td>{{ $postResponse->created_at }}</td>
                                    <td>
                                        @if($postResponse->message_status == 'success')
                                            <span class="badge bg-label-success">{{ $postResponse->message_status }}</span>
                                        @elseif ($postResponse->message_status == 'Scheduled')
                                            <span class="badge bg-label-warning">{{ $postResponse->message_status }}</span>
                                        @else
                                            <span class="badge bg-label-danger">{{ $postResponse->message_status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $postResponse->message_text }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td></td>
                                <td>{{ $post->campaign_name }}</td>
                                <td>---</td>
                                <td>{{ $post->audience }}</td>
                                <td>
                                    @if ($post->careoffname != '')
                                        {{ $post->careoffname }}
                                    @else
                                        {{ '---' }}
                                    @endif
                                </td>
                                <td>---</td>
                                <td>
                                    @if($post->message_status == 'success')
                                        <span class="badge bg-label-success">{{ $post->message_status }}</span>
                                    @elseif ($post->message_status == 'Scheduled')
                                        <span class="badge bg-label-warning">{{ $post->message_status }}</span>
                                    @else
                                        <span class="badge bg-label-danger">{{ $post->message_status }}</span>
                                    @endif
                                </td>
                                <td>{{ $post->message_text }}</td>
                            </tr>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>


    </div>

    <!-- Update Mobile Modal -->
    <div class="modal fade" id="mobileModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="mobileForm">
                    @csrf
                    <input type="hidden" id="record_id" name="id">
                    <input type="hidden" id="umn_audience" name="umn_audience">

                    <div class="modal-header">
                        <h5 class="modal-title">Update Mobile Number</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Mobile Number</label>
                            <input type="text" class="form-control" id="mobile_no" name="mobile_no">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Group Transfer Start -->
    <div class="modal fade" id="bulktransfergroup" aria-hidden="true" aria-labelledby="bulktransfergroupLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-l">
            <div class="modal-content">
            <div class="modal-header pb-2">
                <h5 class="offcanvas-title" id="bulktransfergroupLabel">Transfer Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.metawhatsapp.campaign.resend') }}" method="POST" id="bulktransfergroupvalidation">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" name="contactIDGRPTR" id="contactIDGRPTR">

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <p id="totlaTransferGrp" class="text-success"></p>
                                    <p>Are you sure to resend whatsapp message?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-primary dibtngrp float-end" type="submit">Yes</button>
                        <button type="button" class="btn btn-sm btn-danger float-end mx-2">No</button>
                        {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
    <!-- Bulk Group Transfer End -->

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
    <script src="{{ asset('admin/assets/js/forms-selects.js') }}"></script>

    <script src="{{ asset('admin/assets/vendor/libs/dropzone/dropzone.js') }}"></script>

    <script src="{{ asset('admin/assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/quill/quill.js') }}"></script>

    <!-- Page JS -->
    {{-- <script src="{{ asset('admin/assets/pages/app-meta-whatsapp-campaign-list.js') }}"></script> --}}
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>

    {{-- <script src="{{ asset('admin/assets/pages/validation/meta-whatsapp-campaign-validation.js') }}"></script> --}}



    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-file-upload.js') }}"></script>


    <script>
        $('.datatables-users').dataTable();
    </script>

    <script>
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

    </script>

    <script>
        $(document).ready(function(){
            $('.page-refresh').on('click',function(){
                location.reload();
            });
        });

        function openMobileModal(id, mobile, umn_audience) {
            document.getElementById('record_id').value = id;
            document.getElementById('mobile_no').value = mobile;
            document.getElementById('umn_audience').value = umn_audience;
            

            let modal = new bootstrap.Modal(document.getElementById('mobileModal'));
            modal.show();
        }

        $('#mobileForm').on('submit', function(e) {
            e.preventDefault();
             $.ajax({
                url: "{{ route('admin.metawhatsapp.campaign.update.mobile') }}",
                method: "POST",
                data: $(this).serialize(),

                success: function(response) {

                    Swal.fire({
                        title: 'Success!',
                        text: 'Updated successfully',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-success btn-sm'
                        },
                        buttonsStyling: false
                    }).then(() => {
                        location.reload(); // optional (or update DOM instead)
                    });

                },

                error: function(xhr) {

                    let msg = 'Error updating mobile number';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: 'Error!',
                        text: msg,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-danger btn-sm'
                        },
                        buttonsStyling: false
                    });
                }
            });
        });
    </script>

@endsection
