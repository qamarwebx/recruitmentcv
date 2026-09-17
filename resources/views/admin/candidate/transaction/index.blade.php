@extends('layout.admin.admin_layout')

@section('title','Candidate Transaction')

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

            
            <div class="col-sm-6 col-xl-3 new-candidate-div">
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


            
            <div class="col-sm-6 col-xl-3 ready-for-publish-div">
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
        </div>
        <!-- Users List Table -->
        
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">Search Filter</h5>
                {{-- <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0"> --}}
                <div class="row">

                    <div class="col-md-3 mb-3 pass-type-div">
                        <select name="" id="by-pass-type" class="form-select select22">
                            <option value="">Passport Type</option>
                            <option value="ECNR">ECNR</option>
                            <option value="ECR">ECR</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Candidate Name</th>
                            <th>Passport</th>
                            <th>Amount</th>
                            <th>Payment Mode</th>
                            <th>Transaction ID</th>
                            <th>Transfer To</th>
                            <th>Payment Status</th>
                            <th>Create By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
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
        @if (isset($permission) && $permission->candidate_status == 1)
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
    <script src="{{ asset('admin/assets/pages/app-candidate-transaction-list.js') }}"></script>
    {{-- <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script> --}}

    <script src="{{ asset('admin/assets/pages/validation/candidate-validation.js') }}"></script>
    
    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>

    <script src="{{ asset('admin/assets/custom/main.js') }}"></script>

    


    














@endsection