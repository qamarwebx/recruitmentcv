@extends('layout.admin.admin_layout')

@section('title','Booking Requirement')

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
        <div class="row">
            <div class="col-md-12">
                <h6 class="text-muted">Lead Assign</h6>
                  <div class="nav-align-left nav-tabs-shadow">
                    <ul class="nav nav-tabs" role="tablist">
                      <li class="nav-item">

                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-limit" aria-controls="navs-left-limit" aria-selected="false">
                            Lead Assign 
                        </button>
                    
                      </li>
                    </ul>
                    <div class="tab-content">
                        
                        <div class="tab-pane fade show active" id="navs-left-limit">
                            <form action="{{ route('admin.booking.requirementStr') }}" id="reqEngBL" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Lead Auto Assign</label>

                                            <div class="form-check form-switch mt-3">
                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    id="leadAutoAssignToggle"
                                                    {{ isset($status) && $status->status == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="leadAutoAssignToggle">
                                                    Enable / Disable
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                      

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
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

    <script src="{{ asset('admin/assets/pages/validation/bkrequirement.js') }}"></script>

    <script>
        $(document).ready(function () {

            $('#leadAutoAssignToggle').on('change', function () {

                let status = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('admin.leads.auto_assign.update') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: status
                    },
                    success: function (res) {
                        if (res.success) {
                            toastr.success(res.message);
                        }
                    },
                    error: function () {
                        toastr.error('Something went wrong');
                    }
                });
            });

        });
    </script>


@endsection
