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
                <h6 class="text-muted">Booking Requirement</h6>
                  <div class="nav-align-left nav-tabs-shadow">
                    <ul class="nav nav-tabs" role="tablist">
                      <li class="nav-item">

                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-limit" aria-controls="navs-left-limit" aria-selected="false">
                            Requirement English
                        </button>
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-left-candlimit" aria-controls="nav-left-candlimit" aria-selected="false">
                            Requirement Arabic
                        </button>



                      </li>
                    </ul>
                    <div class="tab-content">
                        
                        <div class="tab-pane fade show active" id="navs-left-limit">
                            <form action="{{ route('admin.booking.requirementStr') }}" id="reqEngBL" method="POST">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="language_type" value="1">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="requirement_eng" class="form-label">Requirement English <span>*</span></label>
                                            <input type="text" name="requirement_eng" id="requirement_eng" value="" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="nav-left-candlimit">
                            <form action="{{ route('admin.booking.requirementStr') }}" id="reqArBL" method="POST">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="language_type" value="2">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="requirement_ar" class="form-label">Requirement Arabic <span>*</span></label>
                                            <input type="text" name="requirement_ar" class="form-control" id="requirement_ar">
                                        </div>
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

@endsection
