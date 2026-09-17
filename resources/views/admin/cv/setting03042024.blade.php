@extends('layout.admin.admin_layout')

@section('title','CV Setting')

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
                    <h5 class="card-header">CV text format</h5>
                    <div class="card-body">
                        <form action="{{ route('admin.cv.setting.update') }}" method="POST" id="cvsettingvalidation">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="cvsetting_id" id="cvsetting_id">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="db-field-name">DB Field Name <span class="text-danger">*</span></label>
                                    <select name="db_field_name" id="db-field-name" class="form-select select22">
                                        <option value="">Select</option>
                                        <option value="cand_name">Candidate Name</option>
                                        <option value="exp_sal">Expected Salary</option>
                                        <option value="expwp_id">Expected Job Place</option>
                                        <option value="age">Age</option>
                                        <option value="marital_status">Marital Status</option>
                                        <option value="religion">Religion</option>
                                        <option value="dob">Date of Birth</option>
                                        <option value="plb_id">Place of Birth</option>
                                        <option value="nation_id">Nationality</option>
                                        <option value="region_id">Region</option>
                                        <option value="lang_known">Language</option>
                                        <option value="google_map">Google Map</option>
                                        <option value="carknown_id">Vehicle</option>
                                        <option value="proff_id">Job Experience</option>
                                        <option value="experience">Period</option>
                                        <option value="expcountry_id">Country</option>
                                        <option value="expcity_id">City</option>
                                        <option value="pass_no">Passport No</option>
                                        <option value="pass_type">Passport Type</option>
                                        <option value="doi">Date of Issue</option>
                                        <option value="doe">Date of Expiry</option>
                                        <option value="poi">Place of Issue</option>
                                        <option value="photo">Photo</option>
                                        <option value="fullsize">Fullsize Photo</option>
                                        <option value="reference_no">Reference No</option>
                                        <option value="gulf_experience">Applied For (English)</option>
                                        <option value="gulf_experience_arabic">Applied For (Arabic)</option>
                                        <option value="education">Education</option>
                                        <option value="remark">Remark</option>
                                        <option value="embassy_required">Embassy Required Eng</option>
                                        <option value="embassy_required_ar">Embassy Required Ar</option>
                                        <option value="created_date">CV Date</option>
                                        <option value="text1">Text1</option>
                                        <option value="text2">Text2</option>
                                        <option value="text3">Text3</option>
                                        <option value="text4">Text4</option>

                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="label-name">Label Name <span class="text-danger">*</span></label>
                                    <input type="text" name="label_name" class="form-control" id="label-name" placeholder="Enter lable name...">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="x-axis">X Axis <span class="text-danger">*</span></label>
                                    <input type="text" name="x_axis" class="form-control" id="x-axis" placeholder="Enter X Axis point...">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="y-axis">Y Axis <span class="text-danger">*</span></label>
                                    <input type="text" name="y_axis" class="form-control" id="y-axis" placeholder="Enter Y Axis point...">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="add-y-axis">Add space</label>
                                    <input type="text" name="add_y_axis" class="form-control" id="add-y-axis" placeholder="Enter space between Y-axis...">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="font-name">Font Name</label>
                                    <input type="text" name="font_name" class="form-control" id="font-name" placeholder="Enter Font name...">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="font-family">Font Type</label>
                                    {{-- <input type="text" name="font_family" class="form-control" id="font-family" placeholder="Enter Font type..."> --}}
                                    <select name="font_family" id="font-family" class="form-select select22">
                                        <option value="">Select</option>
                                        <option value="none">Regular</option>
                                        <option value="B">Bold</option>
                                        <option value="I">Italic</option>
                                        <option value="U">Underline</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="font-size">Font Size</label>
                                    <input type="text" name="font_size" class="form-control" id="font-size" placeholder="Enter Font size...">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="font-color">Font Color</label>
                                    <input type="text" name="font_color" class="form-control" id="font-color" placeholder="Enter color code rgb(255,255,255)...">
                                </div>

                                <div class="col-md-12">
                                    {{-- <button type="submit" class="btn btn-sm btn-primary float-end">Save</button> --}}
                                    <button type="button" class="btn btn-sm btn-primary float-end" id="savePdfsetting">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Image and Text alignment</h5>
                    <div class="card-body">
                        <form action="{{ route('admin.cv.setting.update.image') }}" method="POST" id="cvsettingimgupdate" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-1">
                                    @if (isset($image1) && $image1->filename != '')
                                        <img src="{{ asset('admin/assets/images/cv_setting/'.$image1->filename) }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar1">
                                    @else
                                        <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar1"/>
                                    @endif
                                </div>
                                <input type="hidden" name="cvimagesetting1" value="@if(isset($image1)) {{ $image1->id }} @endif">
                                <div class="col-md-3 mb-3">
                                    <label for="image1file" class="form-label">Image 1</label>
                                    <input class="form-control image1file" name="image1" type="file" id="image1file" />
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="x-axis1">X Axis <span class="text-danger">*</span></label>
                                    @if (isset($image1) && $image1->x_axis != '')
                                        <input type="text" name="x_axis1" class="form-control" value="{{ $image1->x_axis }}" id="x-axis1" placeholder="Enter X Axis point...">
                                    @else
                                        <input type="text" name="x_axis1" class="form-control" id="x-axis1" placeholder="Enter X Axis point...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="y-axis1">Y Axis <span class="text-danger">*</span></label>
                                    @if (isset($image1) && $image1->y_axis != '')
                                        <input type="text" name="y_axis1" class="form-control" id="y-axis1" value="{{ $image1->y_axis }}" placeholder="Enter Y Axis point...">
                                    @else
                                        <input type="text" name="y_axis1" class="form-control" id="y-axis1" placeholder="Enter Y Axis point...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="width-img1">Width</label>
                                    @if (isset($image1) && $image1->width != '')
                                        <input type="text" name="width1" class="form-control" id="width-img1" value="{{ $image1->width }}" placeholder="Enter image width...">
                                    @else
                                        <input type="text" name="width1" class="form-control" id="width-img1" placeholder="Enter image width...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="">Active / Deactive</label>
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="status1" id="statusactive1" @if(isset($image1) && $image1->status == 1) checked @endif value="1"/>
                                        <label class="form-check-label" for="statusactive1">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status1" id="statusdeactive1" @if(isset($image1) && $image1->status == 0) checked @endif value="0"/>
                                        <label class="form-check-label" for="statusdeactive1">Deactive</label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-1">
                                    @if (isset($image2) && $image2->filename != '')
                                        <img src="{{ asset('admin/assets/images/cv_setting/'.$image2->filename) }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar2">
                                    @else
                                        <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar2"/>
                                    @endif
                                </div>
                                <input type="hidden" name="cvimagesetting2" value="@if(isset($image2)) {{ $image2->id }} @endif">
                                <div class="col-md-3 mb-3">
                                    <label for="image2file" class="form-label">Image 2</label>
                                    <input class="form-control image2file" name="image2" type="file" id="image2file" />
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="x-axis2">X Axis</label>

                                    @if (isset($image2) && $image2->x_axis != '')
                                        <input type="text" name="x_axis2" value="{{ $image2->x_axis }}"  class="form-control" id="x-axis2" placeholder="Enter X Axis point...">                                    
                                    @else
                                       <input type="text" name="x_axis2" class="form-control" id="x-axis2" placeholder="Enter X Axis point...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="y-axis2">Y Axis</label>
                                    @if (isset($image2) && $image2->y_axis != '')
                                        <input type="text" name="y_axis2" value="{{ $image2->y_axis }}"  class="form-control" id="y-axis2" placeholder="Enter Y Axis point...">
                                    @else
                                        <input type="text" name="y_axis2" class="form-control" id="y-axis2" placeholder="Enter Y Axis point...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="width-img2">Width</label>
                                    @if (isset($image2) && $image2->width != '')
                                        <input type="text" name="width2" value="{{ $image2->width }}"  class="form-control" id="width-img2" placeholder="Enter image width...">
                                    @else
                                        <input type="text" name="width2" class="form-control" id="width-img2" placeholder="Enter image width...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="">Active / Deactive</label>
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="status2" id="statusactive2" @if(isset($image2) && $image2->status == 1) checked @endif value="1"/>
                                        <label class="form-check-label" for="statusactive2">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status2" id="statusdeactive2" @if(isset($image2) && $image2->status == 0) checked @endif value="0"/>
                                        <label class="form-check-label" for="statusdeactive2">Deactive</label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-1">
                                    @if (isset($image3) && $image3->filename != '')
                                        <img src="{{ asset('admin/assets/images/cv_setting/'.$image3->filename) }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar3">
                                    @else
                                        <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar3"/>
                                    @endif
                                </div>
                                <input type="hidden" name="cvimagesetting3" value="@if(isset($image3)) {{ $image3->id }} @endif">
                                <div class="col-md-3 mb-3">
                                    <label for="image3file" class="form-label">Image 3</label>
                                    <input class="form-control image3file" name="image3" type="file" id="image3file" />
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="x-axis3">X Axis <span class="text-danger">*</span></label>
                                    @if (isset($image3) && $image3->x_axis != '')
                                        <input type="text" name="x_axis3" value="{{ $image3->x_axis }}" class="form-control" id="x-axis3" placeholder="Enter X Axis point...">
                                    @else
                                        <input type="text" name="x_axis3" class="form-control" id="x-axis3" placeholder="Enter X Axis point...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="y-axis3">Y Axis <span class="text-danger">*</span></label>
                                    @if (isset($image3) && $image3->y_axis != '')
                                        <input type="text" name="y_axis3" class="form-control" value="{{ $image3->y_axis }}" id="y-axis3" placeholder="Enter Y Axis point...">
                                    @else
                                        <input type="text" name="y_axis3" class="form-control" id="y-axis3" placeholder="Enter Y Axis point...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="width-img3">Width</label>
                                    @if (isset($image3) && $image3->width != '')
                                        <input type="text" name="width3" class="form-control" value="{{ $image3->width }}" id="width-img3" placeholder="Enter image width...">                                    
                                    @else
                                        <input type="text" name="width3" class="form-control" id="width-img3" placeholder="Enter image width...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="">Active / Deactive</label>
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="status3" id="statusactive3" @if(isset($image3) && $image3->status == 1) checked @endif value="1"/>
                                        <label class="form-check-label" for="statusactive3">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status3" id="statusdeactive3" @if(isset($image3) && $image3->status == 0) checked @endif value="0"/>
                                        <label class="form-check-label" for="statusdeactive3">Deactive</label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-1">
                                    @if (isset($image4) && $image4->filename != '')
                                        <img src="{{ asset('admin/assets/images/cv_setting/'.$image4->filename) }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar4">
                                    @else
                                        <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar4"/>
                                    @endif
                                </div>
                                <input type="hidden" name="cvimagesetting4" value="@if(isset($image4)) {{ $image4->id }} @endif">
                                <div class="col-md-3 mb-3">
                                    <label for="image4file" class="form-label">Image 4</label>
                                    <input class="form-control image4file" name="image4" type="file" id="image4file" />
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="x-axis4">X Axis <span class="text-danger">*</span></label>
                                    @if (isset($image4) && $image4->x_axis)
                                        <input type="text" name="x_axis4" class="form-control" value="{{ $image4->x_axis }}" id="x-axis4" placeholder="Enter X Axis point...">                                    
                                    @else
                                        <input type="text" name="x_axis4" class="form-control" id="x-axis4" placeholder="Enter X Axis point...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="y-axis4">Y Axis <span class="text-danger">*</span></label>
                                    @if (isset($image4) && $image4->y_axis)
                                        <input type="text" name="y_axis4" class="form-control" id="y-axis4" value="{{ $image4->y_axis }}" placeholder="Enter Y Axis point...">
                                    @else
                                        <input type="text" name="y_axis4" class="form-control" id="y-axis4" placeholder="Enter Y Axis point...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="width-img4">Width</label>
                                    @if (isset($image4) && $image4->width)
                                        <input type="text" name="width4" class="form-control" id="width-img4" value="{{ $image4->width }}" placeholder="Enter image width...">
                                    @else
                                        <input type="text" name="width4" class="form-control" id="width-img4" placeholder="Enter image width...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="">Active / Deactive</label>
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="status4" id="statusactive4" @if(isset($image4) && $image4->status == 1) checked @endif value="1"/>
                                        <label class="form-check-label" for="statusactive4">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status4" id="statusdeactive4" @if(isset($image4) && $image4->status == 0) checked @endif value="0"/>
                                        <label class="form-check-label" for="statusdeactive4">Deactive</label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-1">
                                    @if (isset($image5) && $image5->filename != '')
                                    <img src="{{ asset('admin/assets/images/cv_setting/'.$image5->filename) }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar5"/>
                                    @else
                                        <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar5"/>
                                    @endif
                                </div>
                                <input type="hidden" name="cvimagesetting5" value="@if(isset($image5)) {{ $image5->id }} @endif">
                                <div class="col-md-3 mb-3">
                                    <label for="image5file" class="form-label">Image 5</label>
                                    <input class="form-control image5file" name="image5" type="file" id="image5file" />
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="x-axis5">X Axis <span class="text-danger">*</span></label>
                                    @if (isset($image5) && $image5->x_axis != '')
                                        <input type="text" name="x_axis5" class="form-control" id="x-axis5" value="{{ $image5->x_axis }}" placeholder="Enter X Axis point...">                                
                                    @else
                                        <input type="text" name="x_axis5" class="form-control" id="x-axis5" placeholder="Enter X Axis point...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="y-axis5">Y Axis <span class="text-danger">*</span></label>
                                    @if (isset($image5) && $image5->y_axis != '')
                                        <input type="text" name="y_axis5" class="form-control" id="y-axis5" value="{{ $image5->y_axis }}" placeholder="Enter Y Axis point...">
                                    @else
                                        <input type="text" name="y_axis5" class="form-control" id="y-axis5" placeholder="Enter Y Axis point...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="width-img5">Width</label>
                                    @if (isset($image5) && $image5->width != '')
                                        <input type="text" name="width5" class="form-control" id="width-img5" value="{{ $image5->width }}" placeholder="Enter image width...">
                                    @else
                                        <input type="text" name="width5" class="form-control" id="width-img5" placeholder="Enter image width...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="">Active / Deactive</label>
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="status5" id="statusactive5" @if(isset($image5) && $image5->status == 1) checked @endif value="1"/>
                                        <label class="form-check-label" for="statusactive5">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status5" id="statusdeactive5" @if(isset($image5) && $image5->status == 0) checked @endif value="0"/>
                                        <label class="form-check-label" for="statusdeactive5">Deactive</label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <input type="hidden" name="cvtextsetting1" value="@if(isset($text1)) {{ $text1->id }} @endif">
                                <div class="col-md-10 mb-3">
                                    <label for="add-text1" class="form-label">Text1</label>
                                    @if (isset($text1) && $text1->text_desc != '')
                                        <input type="text" name="text1" id="add-text1" class="form-control" placeholder="Enter text1..." value="{{ $text1->text_desc }}">                                    
                                    @else
                                        <input type="text" name="text1" id="add-text1" class="form-control" placeholder="Enter text1...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="">Active / Deactive</label>
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="textstatus1" id="textstatusactive1" @if(isset($text1) && $text1->status == 1) checked @endif value="1"/>
                                        <label class="form-check-label" for="textstatusactive1">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="textstatus1" id="textstatusdeactive1" @if(isset($text1) && $text1->status == 0) checked @endif value="0"/>
                                        <label class="form-check-label" for="textstatusdeactive1">Deactive</label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <input type="hidden" name="cvtextsetting2" value="@if(isset($text2)) {{ $text2->id }} @endif">
                                <div class="col-md-10 mb-3">
                                    <label for="add-text2" class="form-label">Text2</label>
                                    @if (isset($text2) && $text2->text_desc != '')
                                        <input type="text" name="text2" id="add-text2" class="form-control" placeholder="Enter text2..." value="{{ $text2->text_desc }}">                                    
                                    @else
                                        <input type="text" name="text2" id="add-text2" class="form-control" placeholder="Enter text2...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="">Active / Deactive</label>
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="textstatus2" id="textstatusactive2" @if(isset($text2) && $text2->status == 1) checked @endif value="1"/>
                                        <label class="form-check-label" for="textstatusactive2">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="textstatus2" id="textstatusdeactive2" @if(isset($text2) && $text2->status == 0) checked @endif value="0"/>
                                        <label class="form-check-label" for="textstatusdeactive2">Deactive</label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <input type="hidden" name="cvtextsetting3" value="@if(isset($text3)) {{ $text3->id }} @endif">
                                <div class="col-md-10 mb-3">
                                    <label for="add-text3" class="form-label">Text3</label>
                                    @if (isset($text3) && $text3->text_desc != '')
                                        <input type="text" name="text3" id="add-text3" class="form-control" placeholder="Enter text3..." value="{{ $text3->text_desc }}">                                    
                                    @else
                                        <input type="text" name="text3" id="add-text3" class="form-control" placeholder="Enter text3...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="">Active / Deactive</label>
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="textstatus3" id="textstatusactive3" @if(isset($text3) && $text3->status == 1) checked @endif value="1"/>
                                        <label class="form-check-label" for="textstatusactive3">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="textstatus3" id="textstatusdeactive3" @if(isset($text3) && $text3->status == 0) checked @endif value="0"/>
                                        <label class="form-check-label" for="textstatusdeactive3">Deactive</label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <input type="hidden" name="cvtextsetting4" value="@if(isset($text4)) {{ $text4->id }} @endif">
                                <div class="col-md-10 mb-3">
                                    <label for="add-text4" class="form-label">Text4</label>
                                    @if (isset($text4) && $text4->text_desc != '')
                                        <input type="text" name="text4" id="add-text4" class="form-control" placeholder="Enter text4..." value="{{ $text4->text_desc }}">                                    
                                    @else
                                        <input type="text" name="text4" id="add-text4" class="form-control" placeholder="Enter text4...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="">Active / Deactive</label>
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="textstatus4" id="textstatusactive4" @if(isset($text4) && $text4->status == 1) checked @endif value="1"/>
                                        <label class="form-check-label" for="textstatusactive4">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="textstatus4" id="textstatusdeactive4" @if(isset($text4) && $text4->status == 0) checked @endif value="0"/>
                                        <label class="form-check-label" for="textstatusdeactive4">Deactive</label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <input type="hidden" name="textoverview" value="@if(isset($text_overview)) {{ $text_overview->id }} @endif">
                                <div class="col-md-10 mb-3">
                                    <label for="add-overview-text" class="form-label">Overview Text</label>
                                    @if (isset($text_overview) && $text_overview->text_desc != '')
                                        <input type="text" name="text_overview" id="add-overview-text" class="form-control" placeholder="Enter overview text..." value="{{ $text_overview->text_desc }}">                                    
                                    @else
                                        <input type="text" name="text_overview" id="add-overview-text" class="form-control" placeholder="Enter overview text...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="">Active / Deactive</label>
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="overviewtextstatus" id="overviewtextact" @if(isset($text_overview) && $text_overview->status == 1) checked @endif value="1"/>
                                        <label class="form-check-label" for="overviewtextact">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="overviewtextstatus" id="overviewtextdeact" @if(isset($text_overview) && $text_overview->status == 0) checked @endif value="0"/>
                                        <label class="form-check-label" for="overviewtextdeact">Deactive</label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <input type="hidden" name="remarktext" value="@if(isset($remark_text)) {{ $remark_text->id }} @endif">
                                <div class="col-md-10 mb-3">
                                    <label for="add-overview-text" class="form-label">Remark Text</label>
                                    @if (isset($remark_text) && $remark_text->text_desc != '')
                                        <input type="text" name="remark_text" id="add-overview-text" class="form-control" placeholder="Enter overview text..." value="{{ $remark_text->text_desc }}">                                    
                                    @else
                                        <input type="text" name="remark_text" id="add-overview-text" class="form-control" placeholder="Enter overview text...">
                                    @endif
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="">Active / Deactive</label>
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="remarktextstatus" id="remarktextact" @if(isset($remark_text) && $remark_text->status == 1) checked @endif value="1"/>
                                        <label class="form-check-label" for="remarktextact">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="remarktextstatus" id="remarktextdeact" @if(isset($remark_text) && $remark_text->status == 0) checked @endif value="0"/>
                                        <label class="form-check-label" for="remarktextdeact">Deactive</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-sm btn-primary float-end">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Order Receievd Staff list table Start-->
        {{-- <div class="card">
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Label Name</th>
                            <th>X-Axis</th>
                            <th>Y-Axis</th>
                            <th>Font Name</th>
                            <th>Font Format</th>
                            <th>Font Size</th>
                            <th>Font Color</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div> --}}
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
    <script src="{{ asset('admin/assets/custom/main.js') }}"></script>
    <!-- Page JS -->
    <script src="{{ asset('admin/assets/pages/app-cvsetting-list.js') }}"></script>

    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/cvsetting-validation.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('#db-field-name').on('change',function(){
                var field_name = $(this).val();

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });


                jQuery.ajax({
                    url : '{{ url("admin/cv-setting/get/details") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "field_name": field_name,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        console.log(data);
                        $('#cvsetting_id').val(data.id);
                        $('#label-name').val(data.label_name);
                        $('#x-axis').val(data.x_axis);
                        $('#y-axis').val(data.y_axis);
                        $('#add-y-axis').val(data.add_y_axis);
                        $('#font-name').val(data.font_name);
                        $('#font-family').val(data.font_family).change();
                        $('#font-size').val(data.font_size);
                        $('#font-color').val(data.font_color);
                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#savePdfsetting').on('click',function(){
                var id = $('#cvsetting_id').val();
                var db_field_name = $('#db-field-name').val();
                var label_name = $('#label-name').val();
                var x_axis = $('#x-axis').val();
                var y_axis = $('#y-axis').val();
                var add_y_axis = $('#add-y-axis').val();
                var font_name = $('#font-name').val();
                var font_family = $('#font-family').val();
                var font_size = $('#font-size').val();
                var font_color = $('#font-color').val();

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url("admin/cv-setting/update") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "cvsetting_id": id,
                        "db_field_name": db_field_name,
                        "label_name": label_name,
                        "x_axis": x_axis,
                        "y_axis": y_axis,
                        "add_y_axis": add_y_axis,
                        "font_name": font_name,
                        "font_family": font_family,
                        "font_size": font_size,
                        "font_color": font_color,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        toastr.options = {
                            "timeOut": 5000,
                            "showDuration": 300,
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut",
                        };
                        if (data.success != null) {
                            toastr.success(data.success);
                        } else {
                            toastr.error(data.error);
                        }
                    }
                });

            });
        });
    </script>

    <script>
        (function(){
            // Update/reset user image of account page
            let accountUserImage1 = document.getElementById('uploadedAvatar1');
            const fileInput1 = document.querySelector('.image1file');

            let accountUserImage2 = document.getElementById('uploadedAvatar2');
            const fileInput2 = document.querySelector('.image2file');

            let accountUserImage3 = document.getElementById('uploadedAvatar3');
            const fileInput3 = document.querySelector('.image3file');

            let accountUserImage4 = document.getElementById('uploadedAvatar4');
            const fileInput4 = document.querySelector('.image4file');

            let accountUserImage5 = document.getElementById('uploadedAvatar5');
            const fileInput5 = document.querySelector('.image5file');


            if (accountUserImage1) {
                fileInput1.onchange = () => {
                if (fileInput1.files[0]) {
                    accountUserImage1.src = window.URL.createObjectURL(fileInput1.files[0]);
                }
                };
            }

            if (accountUserImage2) {
                fileInput2.onchange = () => {
                if (fileInput2.files[0]) {
                    accountUserImage2.src = window.URL.createObjectURL(fileInput2.files[0]);
                }
                };
            }

            if (accountUserImage3) {
                fileInput3.onchange = () => {
                if (fileInput3.files[0]) {
                    accountUserImage3.src = window.URL.createObjectURL(fileInput3.files[0]);
                }
                };
            }

            if (accountUserImage4) {
                fileInput4.onchange = () => {
                if (fileInput4.files[0]) {
                    accountUserImage4.src = window.URL.createObjectURL(fileInput4.files[0]);
                }
                };
            }

            if (accountUserImage5) {
                fileInput5.onchange = () => {
                if (fileInput5.files[0]) {
                    accountUserImage5.src = window.URL.createObjectURL(fileInput5.files[0]);
                }
                };
            }

        })();
    </script>

@endsection