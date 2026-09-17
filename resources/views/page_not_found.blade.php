@extends('layout.user.layout')

@section('title', $website['company_name'] ?? 'Qamr International')

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
        .explsal{
            list-style: none
        }
        .expmar{
            margin-left: 10px;
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
    
    <section class="container mt-5 mb-lg-5 mb-4 pt-5 pb-lg-5">  
        <!-- Breadcrumb-->
        <div class="row">
            <div class="col-lg-5">
                <nav class="mb-3 pt-md-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Page Not Found</li>                
                    </ol>
                </nav>
            </div>
            
            <div class="col-lg-7">
                
            </div>
        </div>

        
        <div class="row gy-5 pt-lg-2">
            <div class="col-lg-12">
                <p>Page Not Found</p>
            </div>
        </div>
    </section>




@endsection

@section('page-script')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('user/intl-tel-input-master/build/js/intlTelInput-jquery.min.js') }}"></script>

@endsection