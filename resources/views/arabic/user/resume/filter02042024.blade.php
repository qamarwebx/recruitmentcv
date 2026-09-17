<!-- Map popup-->
{{-- <div class="map-popup invisible" id="map">
    <button class="btn btn-icon btn-light btn-sm shadow-sm rounded-circle" type="button"
      data-bs-toggle-class="invisible" data-bs-target="#map"><i class="fi-x fs-xs"></i></button>
    <div class="interactive-map" data-map-options-json="json/map-options-real-estate-sale.json"></div>
  </div> --}}

  <!-- Breadcrumb-->
  <nav class="mb-3 pt-md-2 ardir" aria-label="Breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('ar.welcome') }}">منزل</a></li>
      <li class="breadcrumb-item active" aria-current="page">أعلى السير الذاتية</li>
    </ol>
  </nav>

  <!-- Title-->
  <div class="d-sm-flex align-items-center justify-content-between pb-3 pb-sm-4 ardir">
    <h1 class="h2 mb-0 cv-head">أعلى السير الذاتية</h1>
  </div>

  <!-- Sorting-->
  <div class="d-flex flex-sm-row flex-column align-items-sm-center align-items-stretch my-2">
    

    <div class="d-none d-sm-flex align-items-center flex-shrink-0 text-muted">
      <i class="fi-check-circle me-2"></i><span class="fs-sm mt-n1">{{ $posts->count() }} نتائج</span>
    </div>
    <hr class="d-none d-sm-block w-100 mx-4">
    <div class="d-flex align-items-center flex-shrink-0 ">
      <label class="fs-sm text-nowrap" for="sortby"></label>
      <select class="form-select form-select-sm me-2" id="sortby">
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
  <div class="row g-4 py-4 resumes ardir">
    @if ($posts->count() > 0)
      <!-- Item-->
      @foreach ($posts as $post)
        @php
            $total_exp = array_sum(explode(',',$post->experience));
            $getAge = (date('Y') - date('Y',strtotime($post->dob)));
        @endphp
        <div class="col-sm-6 col-xl-4">
            <div class="card robin_hood shadow-sm card-hover border-0 h-100">
                <div class="tns-carousel-wrapper robin_img card-img-hover box-thumb">
                  <a class="img-overlay" href="{{ route('ar.fullresume',$post->id) }}"></a>
                    <div class="content-overlay end-0 top-0 pt-3 pe-3">
                      <button class="btn btn-icon btn-light btn-xs text-primary rounded-circle" type="button" data-bs-toggle="tooltip" data-bs-placement="left" title="أضف إلى قائمة الامنيات"><i class="fi-heart"></i></button>
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
                    <div class="position-relative">
                    <h2 class="robin_text">@if($post->arcand_name != '') {{ $post->arcand_name }} @else {{ $post->cand_name }} @endif</h2>
                    <p class="robin_text"><i class="fi-calendar mt-n1 me-1 lead align-middle"></i> {{ $getAge.' سنوات من العمر' }}</p>
                    <div class="robin_text">
                      <i class="fi-briefcase mt-n1 me-2 lead align-middle opacity-70"></i> {{ $expper }}
                        {{-- <i class="fi-briefcase mt-n1 me-2 lead align-middle opacity-70"></i>@if($post->experience != 0) {{ $total_exp }} Years @else أعذب @endif  --}}
                        @if ($post->arname !='')
                        <i class="fi-user mt-n1 me-2 lead align-middle opacity-70 mx-3"></i>{{ $post->arname }}
                        @endif
                    </div>
                    </div>
                    <div>
                    <a class="btn custom btn-primary" href="{{ route('ar.fullresume',$post->id) }}"><i class="fi-cart"></i> احجز الآن</a>
                    <a class="rbn custom btn btn-primary" href="{{ route('ar.fullresume',$post->id) }}"><i class="fi-user"></i>حساب تعريفي</a>
                    </div>
                </div>
            </div>
        </div>
      @endforeach
    @else
      <p>لم يتم العثور على مرشح</p>
    @endif
  </div>

   <!-- Pagination-->
          {{-- <nav class="border-top pb-md-4 pt-4 mt-2" aria-label="Pagination">
            <ul class="pagination mb-1">
              <li class="page-item d-sm-none"><span class="page-link page-link-static">1 / 5</span></li>
              <li class="page-item active d-none d-sm-block" aria-current="page"><span class="page-link">1<span class="visually-hidden">(current)</span></span></li>
              <li class="page-item d-none d-sm-block"><a class="page-link" href="#">2</a></li>
              <li class="page-item d-none d-sm-block"><a class="page-link" href="#">3</a></li>
              <li class="page-item d-none d-sm-block">...</li>
              <li class="page-item d-none d-sm-block"><a class="page-link" href="#">8</a></li>
              <li class="page-item"><a class="page-link" href="#" aria-label="Next"><i class="fi-chevron-right"></i></a>
              </li>
            </ul>
          </nav> --}}

          {{ $posts->links('vendor.pagination.bootstrap-4') }}
