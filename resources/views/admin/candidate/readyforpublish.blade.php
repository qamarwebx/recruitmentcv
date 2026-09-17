@extends('layout.admin.admin_layout')

@section('title','Ready for Publish Stage')

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
            <div class="col-lg-12">
                <div class="">
                    <a href="{{ route('admin.candidate') }}" class="btn btn-sm btn-primary float-end">Back</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title mb-0">
                            <h5>Ready for Publish Stage</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="info-container">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Candidate Name:</span>
                                            <span>{{ $post->cand_name }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Passport No:</span>
                                            <span>{{ $post->pass_no }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Passport Type:</span>
                                            <span>{{ $post->pass_type }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Date Of Birth:</span>
                                            <span>{{ date('d-m-Y',strtotime($post->dob)) }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Date Of Issue:</span>
                                            <span>{{ date('d-m-Y',strtotime($post->doi)) }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Date Of Expiry:</span>
                                            <span>{{ date('d-m-Y',strtotime($post->doe)) }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Place of Issue:</span>
                                            {{-- <span>@if(isset($poi)) {{ $poi->name }} @else {{ '---' }} @endif</span> --}}
                                            <span>@if($post->poi_text != '') {{ $post->poi_text }} @else {{ '---' }} @endif</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <form action="{{ route('admin.candidate.publish.uppub') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <input type="hidden" name="editID" value="{{ $post->id }}">
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" name="publish" class="form-check-input" id="publishRadio1" value="0" @if($post->publish == 0) checked @endif>
                                                    <label for="publishRadio1">Pending</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" name="publish" class="form-check-input" id="publishRadio1" value="1" @if($post->publish == 1) checked @endif>
                                                    <label for="publishRadio1">Publish</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-sm data-submit float-end">Save and Next</button>
                                    <a href="{{ route('admin.candidate.publish.backpublish',$post->id) }}" class="btn btn-warning btn-sm me-sm-3 me-1 float-end">Back</a>                                    
                                </form>
                            </div>
                        </div>
                    </div>
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
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>

    <script src="{{ asset('admin/assets/js/forms-selects.js') }}"></script>

    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/candidate-validation.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
@endsection