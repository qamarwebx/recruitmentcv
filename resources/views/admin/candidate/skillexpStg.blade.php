@extends('layout.admin.admin_layout')

@section('title','Skills and Experience Stage')

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
                            <h5>Skills and Experience Stage</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $total_exp = array_sum(explode(',',$post->experience));
                           
                        @endphp
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="info-container">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Experience in(Year) :</span>
                                            <span>{{ $total_exp }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Job Type :</span>
                                            <span>{{ implode(',',$myproff) }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Experience (country name) :</span>
                                            <span>{{ implode(',',$mycont) }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">City :</span>
                                            <span>{{ implode(',',$mycity) }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Occupation :</span>
                                            <span>@if($post->job_type != '') {{ $post->job_type }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-3">
                                            <span class="fw-semibold mw-1">Language Known :</span>
                                            <span>@if($post->lang_known != '') {{ $post->lang_known }} @else {{ '---' }} @endif</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <form action="{{ route('admin.candidate.publish.skillandexp') }}" method="POST" id="pubstgskillandexpvalid">
                                    @csrf
                                    @php
                                        $experience = explode(",",$post->experience);
                                        $expconts = explode(",",$post->expcountry_id);
                                        $expcits = explode(",",$post->expcity_id);
                                        $proffsp = explode(",",$post->proff_id);

                                        $langsk = explode(",",$post->lang_known);
                                        $cararr = explode(",",$post->carknown_id);

                                        // dd($langsk);
                                    @endphp
                                    <input type="hidden" name="editID" value="{{ $post->id }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="add-gulfexperience">Experience <span class="text-danger">*</span></label>
                                                <select name="gulfexperience" id="add-gulfexperience" class="form-select select2" data-allow-clear="true">
                                                    <option value="">Select</option>
                                                    <option value="1" @if($post->gulfexperience == 1) selected @endif>Indian Experience</option>
                                                    <option value="2" @if($post->gulfexperience == 2) selected @endif>Gulf Experience</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="add-car-known">Car Known</label>
                                                <select name="carknown_id[]" id="add-car-known" class="form-select select2" multiple>
                                                    @foreach ($cars as $car)
                                                        <option value="{{ $car->id }}" @if(in_array($car->id,$cararr)) selected @endif>{{ $car->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @foreach ($experience as $index => $value )
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="add-exoinyear{{ $index }}">Experience (in year) <span class="text-danger">*</span></label>
                                                <input type="text" name="experience[]" id="add-exoinyear{{ $index }}" value="{{ $value }}"  class="form-control" placeholder="Enter experience in year...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="add-job-type{{ $index }}">Job Type</label>
                                                <select name="proff_id[]" id="add-job-type{{ $index }}" class="form-select select2" data-allow-clear="true">
                                                    <option value="">Select</option>
                                                    @foreach ($jobtypes as $jobtype)
                                                        <option value="{{ $jobtype->id }}" @if($jobtype->id == $proffsp[$index]) selected @endif>{{ $jobtype->eng_name.' ('.$jobtype->ar_name.')' }}</option>
                                                    @endforeach
                                                </select>                                
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="add-expcountry_id{{ $index }}">Experience (country name)</label>
                                                <select name="expcountry_id[]" id="add-expcountry_id{{ $index }}" class="form-select select2" data-allow-clear="true">
                                                    <option value="">Select</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}" @if($country->id == $expconts[$index]) selected @endif>{{ $country->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="form-label" for="add-expcity_id{{ $index }}">City</label>
                                                <select name="expcity_id[]" id="add-expcity_id{{ $index }}" class="form-select select2" data-allow-clear="true">
                                                    <option value="">Select</option>
                                                    @foreach ($expworklocs as $expworkloc)
                                                        <option value="{{ $expworkloc->id }}" @if($expworkloc->id == $expcits[$index]) selected @endif>{{ $expworkloc->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        @if ($index == 0)
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-sm btn-primary float-end addExp">Add</button>
                                            </div>                        
                                        @endif
                
                                        @if ($index != 0)
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-sm btn-danger remove float-end">Remove</button>
                                            </div>
                                        @endif
                
                                    </div>
                                    @endforeach
                                    <div id="dispEXP">

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="add-language-known">Language Known <span class="text-danger">*</span></label>
                                                <select name="lang_known[]" id="add-language-known" class="form-select select2" multiple>
                                                    <option value="English" @if(in_array('English',$langsk)) selected @endif>English</option>
                                                    <option value="Hindi" @if(in_array('Hindi',$langsk)) selected @endif>Hindi</option>
                                                    <option value="Urdu" @if(in_array('Urdu',$langsk)) selected @endif>Urdu</option>
                                                    <option value="Arabic" @if(in_array('Arabic',$langsk)) selected @endif>Arabic</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="form-label" for="add-occupation">Occupation</label>
                                                <select name="job_type" id="add-occupation" class="form-select select2" data-allow-clear="true">
                                                    <option value="">Select</option>
                                                    @foreach ($jobtypes as $jobtype)
                                                        <option value="{{ $jobtype->eng_name }}" @if($post->job_type) selected @endif>{{ $jobtype->eng_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="" class="form-label">Google Map</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="google_map" id="add-google-map-yes" @if($post->google_map == 1) checked @endif value="1"/>
                                                    <label class="form-check-label" for="add-google-map-yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="google_map" id="add-google-map-no" @if($post->google_map == 0) checked @endif value="0"/>
                                                    <label class="form-check-label" for="add-google-map-no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>  
                                    
                                    <button type="submit" class="btn btn-primary btn-sm data-submit float-end">Save and Next</button>
                                    <a href="{{ route('admin.candidate.publish.backskillandexp',$post->id) }}" class="btn btn-success btn-sm me-sm-3 me-1 float-end">Back</a>
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

    <script>
        var ecount = {{ count($experience) }};
        var rowMin = ecount + 1;
        var rowMax = 5 - ecount;

        $(document).on('click','.addExp',function(){
            var html = '';
            html += '<div class="row"><div class="col-md-6"><div class="mb-3">';
            html += '<label class="form-label" for="edit-exoinyear'+rowMin+'">Experience (in year) <span class="text-danger">*</span></label>';
            html += '<input type="text" name="experience[]" id="edit-exoinyear'+rowMin+'" class="form-control" placeholder="Enter experience in year...">';
            html += '</div></div>';

            html += '<div class="col-md-6"><div class="mb-3">';
            html += '<label class="form-label" for="edit-job-type'+rowMin+'">Job Type</label>';
            html += '<select name="proff_id[]" id="edit-job-type'+rowMin+'" class="form-select select2"><option value="">Select</option>';
            html += '@foreach ($jobtypes as $jobtype)<option value="{{ $jobtype->id }}">{{ $jobtype->eng_name.' ('.$jobtype->ar_name.')' }}</option>@endforeach';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="mb-3">';
            html += '<label class="form-label" for="edit-expcountry_id'+rowMin+'">Experience (country name)</label>';
            html += '<select name="expcountry_id[]" id="edit-expcountry_id'+rowMin+'" class="form-select select2">';
            html += '<option value="">Select</option>@foreach ($countries as $country)<option value="{{ $country->id }}">{{ $country->name }}</option>@endforeach';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="mb-3">';
            html += '<label for="form-label" for="edit-expcity_id'+rowMin+'">City</label>';
            html += '<select name="expcity_id[]" id="edit-expcity_id'+rowMin+'" class="form-select select2"><option value="">Select</option>';
            html += '@foreach ($cities as $city)<option value="{{ $city->id }}">{{ $city->name }}</option>@endforeach';
            html += '</select></div></div>';

            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end">Remove</button></div>';

            $('#dispEXP').append(html);
            rowMin++;
            rowMax--;
            if (rowMax == 0) {
                $('.addExp').prop('disabled',true);
            }else{
                $('.addExp').prop('disabled',false);
            }


        });

        $(document).on('click','.remove',function(){
            $(this).closest('.row').remove();
            rowMax++;
            rowMin--;
            if (rowMax == 0) {
                $('.addExp').prop('disabled',true);
            }else{
                $('.addExp').prop('disabled',false);
            }
        });

    </script>
@endsection