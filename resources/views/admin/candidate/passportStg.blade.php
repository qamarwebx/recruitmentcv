@extends('layout.admin.admin_layout')

@section('title','Passport Verifcation Stage')

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
                            <h5>Passport Verification Stage</h5>
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
                                            <span>@if($post->poi_text !='') {{ $post->poi_text }} @else {{ '---' }} @endif</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <form action="{{ route('admin.candidate.publish.pass') }}" method="POST" id="pubstgpassvalid">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <input type="hidden" name="editID" value="{{ $post->id }}">
                                                <label for="add-cand-name">Candidate Name <span class="text-danger">*</span></label>
                                                <input type="text" name="cand_name" class="form-control" value="{{ $post->cand_name }}" id="add-cand-name" placeholder="Enter Candidate Name...">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="add-passport-no">Passport No <span class="text-danger">*</span></label>
                                                <input type="text" name="pass_no" class="form-control" value="{{ $post->pass_no }}" id="add-passport-no" placeholder="Enter Candidate Name...">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <div class="mb-3">
                                                    <label class="form-label" for="add-pass-type">Passport Type <span class="text-danger">*</span></label>
                                                    <select name="pass_type" id="add-pass-type" class="form-select select2" data-allow-clear="true">
                                                        <option value="">Select</option>
                                                        <option value="ECNR" @if($post->pass_type == 'ECNR') selected @endif>ECNR</option>
                                                        <option value="ECR" @if($post->pass_type == 'ECR') selected @endif>ECR</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <div class="mb-3">
                                                    <label class="form-label" for="add-dob">Date of Birth <span class="text-danger">*</span></label>
                                                    <input type="date" name="dob" id="add-dob" value="{{ $post->dob }}" class="form-control" placeholder="Enter date of birth...">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <div class="mb-3">
                                                    <label class="form-label" for="add-doi">Date of Issue <span class="text-danger">*</span></label>
                                                    <input type="date" name="doi" id="add-doi" value="{{ $post->doi }}" class="form-control" placeholder="Enter date of issue...">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <div class="mb-3">
                                                    <label class="form-label" for="add-doe">Date of Expiry <span class="text-danger">*</span></label>
                                                    <input type="date" name="doe" id="add-doe" value="{{ $post->doe }}" class="form-control" placeholder="Enter date of expiry...">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="form-label" for="add-place-of-issue">Place of Issue</label>
                                                {{-- <select name="poi" id="add-place-of-issue" class="form-select select2" data-allow-clear="true">
                                                    <option value="">Select</option>
                                                    @foreach ($poiss as $pois)
                                                        <option value="{{ $pois->id }}" @if($pois->id == $post->poi) selected @endif>{{ $pois->name }}</option>
                                                    @endforeach
                                                </select> --}}
                                                <input type="text" name="poi_text" id="add-place-of-issue" value="{{ $post->poi_text }}" class="form-control cityTypehead" placeholder="Type city here...">
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm data-submit float-end">Save and Next</button>
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