@extends('layout.user.arabic.layout')

@section('title', $website['company_name_ar'] ?? 'قمر انترناشيونال')

@section('page-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('user/intl-tel-input-master/build/css/intlTelInput.css') }}">
    <style>
        .box-slide{
            width: 460px;
            height: 259px;
            background-color: rgb(218, 218, 218);
        }
        .box-slide img{
            object-fit: contain;
            width: 100%;
            height: 100%;
        }
        .box-thumb{
            /* width: 460px; */
            height: 250px;
            background-color: rgb(218, 218, 218);
        }
        .box-thumb img{
            object-fit: contain;
            width: 100%;
            height: 100%;
        }
        .swal2-icon {
            margin: 0 auto 0!important;
        }
        .swal2-icon .swal2-icon-content {
        display: ruby-text!important;
        }
        
    </style>
@endsection

@section('content')

    @php
        $countries = DB::table('countries')->orderBy('name','ASC')->get();
    @endphp

    <section class="container mt-5 mb-lg-5 mb-4 pt-5 pb-lg-5">  
        <!-- Breadcrumb-->
        <nav class="mb-3 pt-md-3 ardir" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ar.welcome') }}">منزل</a></li>
                <li class="breadcrumb-item active" aria-current="page">الصفحة غير موجودة</li>
            </ol>
        </nav>

        <div class="row ardir">
            <!-- Sidebar with details-->
            <div class="col-md-12">
                <p>الصفحة غير موجودة</p>
            </div>
        </div>
    </section>



    <!-- Recently viewed-->

@endsection

@section('page-script')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('user/intl-tel-input-master/build/js/intlTelInput-jquery.min.js') }}"></script>
@endsection