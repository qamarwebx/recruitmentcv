@extends('layout.admin.admin_layout')

@section('title','Order receive panel')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Order Received Panel</h5>
                    <div class="card-body">
                        <form action="{{ route('admin.order.received.panel.update') }}" method="POST" id="orderRCPValidation">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="orderrc_id" id="orderrc_id">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="staff_id" class="form-label">Staff Name <span class="text-danger">*</span></label>
                                        <select name="staff_id" id="staff_id" class="form-select select2" data-allow-clear="true">
                                            <option value="">Select Staff</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="mobile" class="form-label">Mobile No <span class="text-danger">*</span></label>
                                        <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter mobile number...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="text" name="email" id="email" class="form-control" placeholder="Enter email address...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Order Received <span class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-select select2" data-allow-clear="true">
                                            <option value="">Select</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Order Receievd Staff list table Start-->
        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Staff Name</th>
                            <th>Mobile Number</th>
                            <th>Email</th>
                            <th>Order Received</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <!-- Order Receievd Staff list table End-->
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
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('admin/assets/pages/app-orderreceived-list.js') }}"></script>

    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/orderreceived-validation.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('#staff_id').on('change',function(){
                var staffID = $(this).val();

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });


                jQuery.ajax({
                    url : '{{ url("admin/order-received-panel/check") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "staff_id": staffID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        console.log(data);
                        $('#orderrc_id').val(data.id);
                        $('#mobile').val(data.mobile);
                        $('#email').val(data.email);
                        $('#status').val(data.status).change();
                    }
        });

            });
        });
    </script>

@endsection