Map popup
{{-- <div class="map-popup invisible" id="map">
    <button class="btn btn-icon btn-light btn-sm shadow-sm rounded-circle" type="button"
      data-bs-toggle-class="invisible" data-bs-target="#map"><i class="fi-x fs-xs"></i></button>
    <div class="interactive-map" data-map-options-json="json/map-options-real-estate-sale.json"></div>
  </div> --}}
  <style>
    .shadow-sm {
        box-shadow: 0 !important;
    }

    .robin_hood {
        box-shadow: none !important;
    }

    .card {
      background-color: #ffffff !important;
    }

  </style>
  <!-- Breadcrumb-->
  <nav class="mb-3 pt-md-2 ardir d-none" aria-label="Breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('ar.welcome') }}">منزل</a></li>
      <li class="breadcrumb-item active" aria-current="page">أعلى السير الذاتية</li>
    </ol>
  </nav>

  <!-- Title-->
  <div class="d-sm-flex align-items-center justify-content-between pb-3 pb-sm-4 ardir">
    <h1 class="h2 mb-0 cv-head d-none">أعلى السير الذاتية</h1>
  </div>

  <!-- Sorting-->
  <div class="d-flex flex-sm-row flex-column align-items-sm-center align-items-stretch my-2">
    

    <div class="d-none d-sm-flex align-items-center flex-shrink-0 text-muted">
      <i class="fi-check-circle me-2"></i><span class="fs-sm mt-n1">{{ $posts->count() }} نتائج</span>
    </div>
    <hr class="d-none d-sm-block w-100 mx-4">
    <div class="d-flex align-items-center flex-shrink-0 ">
      <label class="fs-sm text-nowrap d-none" for="sortby"></label>
      <select class="form-select form-select-sm me-2 d-none" id="sortby">
        <option><i class="fi-arrows-sort text-muted mt-n1 me-2"></i>ترتيب حسب:</option>
        <option>شعبية</option>
        <option>منخفض - مرتفع السعر</option>
        <option>مرتفع - منخفض السعر</option>
        <option>تقييم عالي</option>
        <option>متوسط ​​تقييم</option>
      </select>

      <button type="button" class="btn btn-outline-secondary d-md-none w-100 btn-sm" data-bs-toggle="offcanvas" data-bs-target="#filter-bar" style="font-family: Noto Sans; font-size: 14px; font-weight: 400; text-align: start;">منقي : <i class="fi-filter" style="float: right; margin-top: 4px;"></i>
      </button>

    </div>
  </div>

 <!-- Catalog grid-->
<div class="row g-2 g-md-4 py-4 resumes ardir">
@if ($posts->count() > 0)

@foreach ($posts as $post)
@php
$total_exp = array_sum(explode(',',$post->experience));
$getAge = (date('Y') - date('Y',strtotime($post->dob)));

if (!empty($post->religion_id)) {
    $religion = App\Models\Religion::find($post->religion_id);
} else {
    $religion = null;
}
@endphp

<div class="col-6 col-sm-4 col-xl-4">
<div class="card robin_hood shadow-sm card-hover border-2 h-100">

<div class="tns-carousel-wrapper robin_img card-img-hover box-thumb">

<a class="img-overlay" href="{{ url('ar/resumes/details/'.$post->slug_text) }}"></a>

<div class="content-overlay end-0 top-0 pt-3 pe-3">
<button class="btn btn-icon btn-light btn-xs text-primary rounded-circle"
type="button"
data-bs-toggle="tooltip"
data-bs-placement="left"
title="أضف إلى قائمة الامنيات">
<i class="fi-heart"></i>
</button>
</div>

@if ($post->photo_file != '')
<img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="Image">
@else
<img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Image">
@endif

</div>

@php
if($total_exp != 0){
    if ($total_exp < 1) {
        $expper = $total_exp." سنة تجربة";
    }else{
        $expper = $total_exp." سنين تجربة";
    }
}else{
    $expper = 'أعذب';
}
@endphp

<div class="text-center name-candidate py-3 ardir">

<h3 class="robin_text fw-bold mb-1">
@if($post->arcand_name != '')
{{ $post->arcand_name }}
@else
{{ $post->cand_name }}
@endif
<!-- <i class="fa fa-check-square text-primary"></i> -->
<img class="pb-1" src="{{ asset('admin/assets/images/candidate/cadidate-verified.svg') }}" height="15" width="15" alt="Thumbnail">
</h3>

<p class="robin_text">
<i class="fi-calendar me-1"></i>
{{ $getAge.' سنوات من العمر' }}
@if(optional($religion)->name)
{{ $religion->arbname }}
@endif
</p>

<div class="robin_text">

<i class="fi-briefcase me-2"></i>
{{ $expper }}

@if ($post->arname !='')
<i class="fi-user mx-2"></i>
{{ $post->arname }}
@endif

</div>

<!-- Buttons -->
<div class="mt-3 d-flex flex-column flex-md-row gap-2">

<a class="btn btn-sm btn-primary w-100"
href="{{ url('ar/resumes/details/'.$post->slug_text) }}">
<i class="fi-cart"></i> اطلب الان
</a>

<a class="btn btn-sm btn-outline-primary w-100"
href="{{ url('ar/resumes/details/'.$post->slug_text) }}">
<i class="fi-user"></i> تفاصيل كاملة
</a>

</div>

</div>
</div>
</div>

@endforeach

@else
<p>لم يتم العثور على مرشح</p>
@endif
</div>

<!-- Pagination -->
<div class="pb-4" style="padding-bottom: 30px;">
{{ $posts->links('vendor.pagination.bootstrap-4') }}
</div>