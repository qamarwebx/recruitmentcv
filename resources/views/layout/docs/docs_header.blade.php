<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>QAMAR HIRE Documentation - @yield('title')</title>

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('admin/assets/img/favicon/favicon.ico') }}" />

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Icons -->
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/fonts/fontawesome.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/fonts/tabler-icons.css') }}">

<!-- Core CSS -->
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/rtl/core.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/rtl/theme-default.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/css/demo.css') }}">

<!-- Vendors -->
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/node-waves/node-waves.css') }}">

<!-- Documentation CSS -->
<link rel="stylesheet" href="{{ asset('admin/assets/custom/style.css') }}">

<style>

body{
    background:#f8f9fa;
}

.docs-sidebar,
#layout-menu{
    width:280px;
    min-height:100vh;
    background:#fff;
    border-right:1px solid #e5e7eb;
    position:fixed;
    top:0;
    left:0;
    height:100vh;
    overflow-y:auto;
    z-index:1030;
}

.layout-page{
    margin-left:280px;
}

.docs-content{
    padding:30px;
}

.docs-toc{
    position:sticky;
    top:20px;
}

@media (max-width: 991.98px){
    .docs-sidebar,
    #layout-menu{
        position:relative;
        height:auto;
        min-height:auto;
    }
    .layout-page{
        margin-left:0;
    }
    .docs-toc{
        position:static;
    }
}

.docs-logo{
    font-size:22px;
    font-weight:700;
    padding:20px;
    border-bottom:1px solid #eee;
}

.docs-logo a{
    color:#7367f0;
    text-decoration:none;
}

.docs-sidebar .nav-link{
    color:#566a7f;
    padding:10px 20px;
    border-radius:6px;
    margin:4px 10px;
}

.docs-sidebar .nav-link:hover{
    background:#f3f2ff;
    color:#7367f0;
}

.docs-sidebar .nav-link.active{
    background:#7367f0;
    color:#fff;
}

.docs-section-title{
    font-size:11px;
    text-transform:uppercase;
    color:#999;
    padding:15px 20px 5px;
    font-weight:600;
}

</style>

<script src="{{ asset('admin/assets/vendor/js/helpers.js') }}"></script>

<script src="{{ asset('admin/assets/js/config.js') }}"></script>

@yield('page-style')