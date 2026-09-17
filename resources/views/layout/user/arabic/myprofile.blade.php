@extends('layout.user.arabic.layout')

@section('title','ملفي')

@section('page-style')
    
@endsection

@section('content')
    <div class="container pt-md-5 pb-lg-4 mt-5 mb-sm-2">
        <div class="row">
            <div class="col-lg-8 col-md-7 mb-5 ardir">
                <form action="{{ route('myprofile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
                    <h1 class="h2 cv-head">معلومات شخصية</h1>
                    <div class="row pb-2">
                        {{-- <div class="col-lg-9 col-sm-8 mb-4">
                            <textarea class="form-control" id="account-bio" rows="6" placeholder="Write your bio here. It will be displayed on your public profile."></textarea>
                        </div> --}}
                        {{-- <div class="col-lg-3 col-sm-4 mb-4">
                            <div class="filepond--root file-uploader bg-secondary filepond--hopper"
                                data-style-panel-layout="compact" 
                                data-style-button-remove-item-position="left"
                                data-style-button-process-item-position="right"
                                data-style-load-indicator-position="right"
                                data-style-progress-indicator-position="right"
                                data-style-button-remove-item-align="false" 
                                style="height: 160px;">
                                <a class="filepond--credits" aria-hidden="true" type="file" target="_blank" rel="noopener noreferrer" style="transform: translateY(152px);"></a>
                                <div class="filepond--drop-label my-4 px-4" style="transform: translate3d(0px, 0px, 0px); opacity: 1;">
                                    <label for="filepond--browser-xxt4ofnsd" id="filepond--drop-label-xxt4ofnsd">
                                        <i class="d-inline-block fi-camera-plus fs-2 text-muted my-2 mx-5 text-center"></i><br>
                                        <span class="fw-bold">Change picture</span>
                                        <input type="file" name="" id="">
                                    </label>
                                    
                                </div>
                                <div class="filepond--list-scroller" style="transform: translate3d(0px, 0px, 0px);">
                                    <ul class="filepond--list" role="list"></ul>
                                </div>
                                <div class="filepond--panel filepond--panel-root" data-scalable="true">
                                    <div class="filepond--panel-top filepond--panel-root"></div>
                                    <div class="filepond--panel-center filepond--panel-root" style="transform: translate3d(0px, 8px, 0px) scale3d(1, 1.44, 1);"></div>
                                    <div class="filepond--panel-bottom filepond--panel-root" style="transform: translate3d(0px, 152px, 0px);"></div>
                                </div>
                                <span class="filepond--assistant" id="filepond--assistant-xxt4ofnsd" role="status" aria-live="polite" aria-relevant="additions"></span>
                                <div class="filepond--drip"></div>
                                <fieldset class="filepond--data"></fieldset>
                            </div>
                        </div> --}}
                        <div class="col-lg-3 mb-4">
                            <span id="blah">
                                @if ($post->photo != '')
                                    <img src="{{ asset('user/img/avatars/'.$post->photo) }}" alt="avatars" style="width:150px;height:150px;" class="img-thumbnail">
                                @else
                                    <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="avatar" style="width:150px;height:150px;" class="img-thumbnail">
                                @endif
                            </span>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <input type="file" name="photo" class="form-control" id="photo" onchange="readImg(this)">
                        </div>
                    </div>
    
                    <div class="border rounded-3 p-3 mb-4" id="personal-info">
    
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pe-2">
                                    <label class="form-label fw-bold">الاسم الكامل</label>
                                    <div id="name-value">{{ $post->uname }}</div>
                                </div>
                                <div class="me-n3" data-bs-toggle="tooltip" title="Edit">
                                    <a class="nav-link py-0" href="#name-collapse" data-bs-toggle="collapse"><i class="fi-edit"></i></a>
                                </div>
                            </div>
                            <div class="collapse" id="name-collapse" data-bs-parent="#personal-info">
                                <input class="form-control mt-3" name="name" type="text" data-bs-binded-element="#name-value" data-bs-unset-value="Not specified" value="{{ $post->uname }}">
                            </div>
                        </div>
    
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pe-2">
                                    <label class="form-label fw-bold">بريد إلكتروني</label>
                                    <div id="email-value">{{ $post->uemail }}</div>
                                </div>
                                <div class="me-n3" data-bs-toggle="tooltip" title="Edit">
                                    <a class="nav-link py-0" href="#email-collapse" data-bs-toggle="collapse"><i class="fi-edit"></i></a>
                                </div>
                            </div>
                            <div class="collapse" id="email-collapse" data-bs-parent="#personal-info">
                                <input class="form-control mt-3" name="email" type="email" data-bs-binded-element="#email-value" data-bs-unset-value="Not specified" disabled value="{{ $post->uemail }}">
                            </div>
                        </div>
    
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pe-2">
                                    <label class="form-label fw-bold">رقم التليفون</label>
                                    <div id="phone-value">@if($post->mobile_no != '') {{ $post->mobile_no }} @else {{ '---' }} @endif</div>
                                </div>
                                <div class="me-n3" data-bs-toggle="tooltip" title="Edit">
                                    <a class="nav-link py-0" href="#phone-collapse" data-bs-toggle="collapse"><i class="fi-edit"></i></a>
                                </div>
                            </div>
                            <div class="collapse" id="phone-collapse" data-bs-parent="#personal-info">
                                <input class="form-control mt-3" name="mobile_no" type="text" placeholder="Enter mobile no..." data-bs-binded-element="#phone-value" data-bs-unset-value="Not specified" value="{{ $post->mobile_no }}">
                            </div>
                        </div>
    
                        {{-- <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pe-2">
                                    <label class="form-label fw-bold">Company name</label>
                                    <div id="company-value">@if($post->company_name != '') {{ $post->company_name }} @else {{ '---' }} @endif</div>
                                </div>
                                <div class="me-n3" data-bs-toggle="tooltip" title="Edit">
                                    <a class="nav-link py-0" href="#company-collapse" data-bs-toggle="collapse"><i class="fi-edit"></i></a>
                                </div>
                            </div>
                            <div class="collapse" id="company-collapse" data-bs-parent="#personal-info">
                                <input class="form-control mt-3" name="company_name" type="text" data-bs-binded-element="#company-value" data-bs-unset-value="{{ $post->company_name }}" placeholder="Enter company name">
                            </div>
                        </div> --}}
    
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pe-2">
                                    <label class="form-label fw-bold">دولة</label>
                                    <div id="country-value">@if($post->conname != '') {{ $post->conname }} @else {{ '---' }} @endif</div>
                                </div>
                                <div class="me-n3" data-bs-toggle="tooltip" title="Edit">
                                    <a class="nav-link py-0" href="#country-collapse" data-bs-toggle="collapse"><i class="fi-edit"></i></a>
                                </div>
                            </div>
                            <div class="collapse" id="country-collapse" data-bs-parent="#personal-info">
                                <select name="country_id" class="form-control mt-3">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" @if($country->id == $post->country_id) selected @endif>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                {{-- <input class="form-control mt-3" type="text" data-bs-binded-element="#country-value" data-bs-unset-value="{{ $post->company_name }}" placeholder="Enter company name"> --}}
                            </div>
                        </div>
    
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pe-2">
                                    <label class="form-label fw-bold">مدينة</label>
                                    <div id="city-value">@if($post->citname != '') {{ $post->citname }} @else {{ '---' }} @endif</div>
                                </div>
                                <div class="me-n3" data-bs-toggle="tooltip" title="Edit">
                                    <a class="nav-link py-0" href="#city-collapse" data-bs-toggle="collapse"><i class="fi-edit"></i></a>
                                </div>
                            </div>
                            <div class="collapse" id="city-collapse" data-bs-parent="#personal-info">
                                <select name="city_id" class="form-control mt-3">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @if($city->id == $post->city_id) selected @endif>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                {{-- <input class="form-control mt-3" type="text" data-bs-binded-element="#country-value" data-bs-unset-value="{{ $post->company_name }}" placeholder="Enter company name"> --}}
                            </div>
                        </div>
    
                        <div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pe-2">
                                    <label class="form-label fw-bold">عنوان</label>
                                    <div id="address-value">@if($post->address != '') {{ $post->address }} @else {{ '---' }} @endif</div>
                                </div>
                                <div class="me-n3" data-bs-toggle="tooltip" title="Edit">
                                    <a class="nav-link py-0" href="#address-collapse" data-bs-toggle="collapse"><i class="fi-edit"></i></a>
                                </div>
                            </div>
                            <div class="collapse" id="address-collapse" data-bs-parent="#personal-info">
                                {{-- <input class="form-control mt-3" name="address" type="text" data-bs-binded-element="#address-value" data-bs-unset-value="{{ $post->address }}" placeholder="Enter address"> --}}
                                <textarea name="address" class="form-control mt-3" cols="30" rows="5" data-bs-binded-element="#address-value" data-bs-unset-value="{{ $post->address }}" placeholder="Enter address">{{ $post->address }}</textarea>
                            </div>
                        </div>
                    </div>
    
                    <div class="d-flex align-items-center justify-content-between border-top mt-4 pt-4 pb-1">
                        <button class="btn btn-primary btn-sm px-3 px-sm-4" type="submit">احفظ التغييرات</button>
                        {{-- <button class="btn btn-link btn-sm px-0" type="button"><i class="fi-trash me-2"></i>Delete account</button> --}}
                    </div>
                </form>
            </div>

            <aside class="col-lg-4 col-md-5 pe-xl-4 mb-5">
                @include('layout.user.arabic.myorder')
            </aside>
            <!-- Content-->



        </div>
    </div>
@endsection

@section('page-script')
    
    <script>
        function readImg(input){
            $('#blah').empty();
            for (var i = 0; i < input.files.length; i++) {
                if (input.files && input.files[i]) {
                    var reader = new FileReader();
                    reader.onload = function(e){
                        $($.parseHTML('<img style="width: 150;height: 150;" class="img-thumbnail">&nbsp&nbsp')).attr('src', e.target.result).appendTo('#blah');
                    };

                    reader.readAsDataURL(input.files[i]);
                }
            }
        }
    </script>
@endsection

