<!-- Map popup-->
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
  <nav class="mb-3 pt-md-2 d-none" aria-label="Breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Top Resumes</li>
    </ol>
  </nav>

  <!-- Title-->
  <div class="d-sm-flex align-items-center justify-content-between pb-3 pb-sm-4">
    <h1 class="h2 mb-0 d-none">Top resumes</h1>
  </div>

  <!-- Sorting-->
  <div class="d-flex flex-sm-row flex-column align-items-sm-center align-items-stretch my-2">
    <div class="d-flex align-items-center flex-shrink-0">
      <label class="fs-sm text-nowrap d-none" for="sortby"></label>
      <select class="form-select form-select-sm me-2 d-none" id="sortby">
        <option><i class="fi-arrows-sort text-muted mt-n1 me-2"></i>Sort by:</option>
        <option>Popularity</option>
        <option>Low - High Price</option>
        <option>High - Low Price</option>
        <option>High rating</option>
        <option>Average Rating</option>
      </select>

      <button type="button" class="btn btn-outline-secondary d-md-none w-100 btn-sm" data-bs-toggle="offcanvas" data-bs-target="#filter-bar" style="font-family: Noto Sans; font-size: 14px; font-weight: 400; text-align: start;">Filter : <i class="fi-filter" style="float: right; margin-top: 4px;"></i></button>

    </div>
    <hr class="d-none d-sm-block w-100 mx-4">
    <div class="d-none d-sm-flex align-items-center flex-shrink-0 text-muted">
      <i class="fi-check-circle me-2"></i><span class="fs-sm mt-n1">{{ $posts->count() }} results</span></div>
    </div>

 <!-- Catalog grid-->
 <div class="row g-2 g-md-4 py-4 resumes">
    @if ($posts->count() > 0)

    @foreach ($posts as $post)
    @php

        if (!empty($post->religion_id)) {
            $religion = App\Models\Religion::find($post->religion_id);
        } else {
            $religion = null;
        }

        $total_exp = $post->experience ? array_sum(explode(',', $post->experience)) : 0;
        $getAge = $post->dob ? (date('Y') - date('Y', strtotime($post->dob))) : null;

        $imagePath = $post->photo_file
            ? asset('admin/assets/images/candidate/'.$post->photo_file)
            : asset('admin/assets/img/avatars/avatar.jpg');
    @endphp

    <div class="col-6 col-sm-4 col-xl-4">
        <div class="card border-1 robin_hood shadow-sm card-hover h-100">

            <div class="tns-carousel-wrapper robin_img card-img-hover box-thumb">
                <div class="content-overlay end-0 top-0 pt-3 pe-3">
                    <button class="btn btn-icon btn-light btn-xs text-primary rounded-circle"
                            type="button"
                            data-bs-toggle="tooltip"
                            data-bs-placement="left"
                            title="Add to Wishlist">
                        <i class="fi-heart"></i>
                    </button>
                </div>

                <a href="{{ url('resumes/details/'.$post->slug_text) }}"></a>

                <img src="{{ $imagePath }}"
                    alt="Candidate Image"
                    width="300"
                    height="300"
                    loading="lazy"
                    decoding="async"
                    style="object-fit:cover; width:100%;">
            </div>

            {{-- TEXT AREA --}}
            <div class="text-center name-candidate py-3">

                {{-- NAME --}}
                <h3 class="robin_text fw-bold text-uppercase mb-1">
                    {{ $post->cand_name }}
                    <img class="pb-1" src="{{ asset('admin/assets/images/candidate/cadidate-verified.svg') }}" height="15" width="15" alt="Thumbnail">
                    <!-- <i class="fa fa-check-square text-primary"></i> -->
                </h3>

                {{-- AGE --}}
                @if($getAge)
                <div class="d-flex justify-content-center align-items-center mb-2 robin_text">
                    <i class="fi-calendar me-2"></i>
                    <span>
                    Age {{ $getAge }} 
                    @if(optional($religion)->name)
                        {{ $religion->name }}
                    @endif
                    </span>            
                </div>
                @endif

                {{-- EXPERIENCE + PROFESSION --}}
                <div class="d-flex justify-content-center gap-2 flex-wrap">

                    <div class="d-flex align-items-center robin_text mb-0">
                        <i class="fi-briefcase me-2"></i>
                        <span>{{ $total_exp > 0 ? $total_exp.' Years Exp' : 'Fresher' }}</span>
                    </div>

                    @if(optional($post->profession)->eng_name)
                    <div class="d-flex align-items-center robin_text  mb-0">
                        <i class="fi-user me-2"></i>
                        <span>{{ $post->profession->eng_name }}</span>
                    </div>
                    @endif

                </div>

                {{-- BUTTONS --}}
                <div class="mt-3 d-flex flex-column flex-md-row gap-2">

                  <a class="btn btn-sm btn-primary w-100"
                    href="{{ url('resumes/details/'.$post->slug_text) }}">
                      <i class="fi-cart"></i> Order Now
                  </a>

                  <a class="btn btn-sm btn-outline-primary w-100"
                    href="{{ url('resumes/details/'.$post->slug_text) }}">
                      <i class="fi-user"></i> View Full Details
                  </a>

                </div>
            </div>
        </div>
    </div>

    @endforeach

    @else
    <p class="text-center">No Candidate Found</p>
    @endif
</div>


<!-- Pagination -->
<div class="pb-4" style="padding-bottom: 30px;">
    {{ $posts->links('vendor.pagination.bootstrap-4') }}
</div>