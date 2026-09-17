@extends('layout.admin.admin_layout')

@section('title','Document Stage')

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
                            <h5>Docuemnt Stage</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $total_exp = array_sum(explode(',',$post->experience));
                           
                        @endphp
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="row">
                                    @if ($post->photo_file != '')
                                        <div class="col-lg-6">
                                            <span class="badge bg-label-secondary mt-2">Photo</span>
                                            <div class="mt-2">
                                                <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="{{ $post->photo_file }}" class="d-block w-px-100 h-px-100 rounded" />
                                            </div>
                                            {{-- <div class="mt-1">
                                                <a href="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                                <a href="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                                                <a href=""><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                                            </div> --}}
                                        </div>
                                    @endif
                                    @if ($post->pass_file != '')
                                        <div class="col-lg-6">
                                            <span class="badge bg-label-secondary mt-2">Passport Copy</span>
                                            <div class="mt-2">
                                                <img src="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" alt="{{ $post->pass_file }}" class="d-block w-px-100 h-px-100 rounded" />
                                            </div>
                                            {{-- <div class="mt-1">
                                                <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                                <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                                                <a href=""><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                                            </div> --}}
                                        </div>
                                    @endif
                                    @if ($post->lic_file != '')
                                        <div class="col-lg-6">
                                            <span class="badge bg-label-secondary mt-2">License Copy</span>
                                            <div class="mt-2">
                                                <img src="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" alt="{{ $post->lic_file }}" class="d-block w-px-100 h-px-100 rounded" />
                                            </div>
                                        </div>
                                    @endif
                                    @if ($post->cv_file != '')
                                        <div class="col-lg-6">
                                            <span class="badge bg-label-secondary mt-2">CV</span>
                                            <div class="mt-2">
                                                <img src="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" alt="{{ $post->cv_file }}" class="d-block w-px-100 h-px-100 rounded" />
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <form action="{{ route('admin.candidate.publish.docsstg') }}" method="POST" id="publishdocstg" enctype="multipart/form-data">
                                    @csrf
                                    
                                    <input type="hidden" name="editID" value="{{ $post->id }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="add-upload-pp">Upload Passport</label>
                                                <input type="file" name="pass_file" id="add-upload-pp" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="add-upload-lic">Upload License</label>
                                                <input type="file" name="lic_file" id="add-upload-lic" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="add-upload-cv">Upload Full Image</label>
                                                <input type="file" name="cv_file" id="add-upload-cv" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="add-upload-photo">Upload Photo</label>
                                                <input type="file" name="photo_file" id="add-upload-photo" class="form-control">
                                            </div>
                                        </div>
                                    </div>  

                                    <button type="submit" class="btn btn-primary btn-sm data-submit float-end">Save and Next</button>
                                    <a href="{{ route('admin.candidate.publish.backdocs',$post->id) }}" class="btn btn-success btn-sm me-sm-3 me-1 float-end">Back</a>
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