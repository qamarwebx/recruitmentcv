@extends('layout.user.layout')

@section('title', $website['company_name'] ?? 'Qamr International')

@section('page-style')
    
@endsection

@section('content')
<div class="container-fluid mt-5 pt-5 p-0">
    <div class="row g-0 mt-n3">
      <!-- Filters sidebar (Offcanvas on mobile)-->
      <aside class="col-md-3 border-top-lg border-end-lg shadow-sm px-3 px-xl-4 px-xxl-5 pt-lg-2 position-fixed desktop-filter">
        <div class="offcanvas offcanvas-start offcanvas-collapse" id="filters-sidebar">
          <div class="offcanvas-header d-flex d-lg-none align-items-center">
            <h2 class="h5 mb-0">Filters</h2>
            <button class="btn-close" type="button" data-bs-dismiss="offcanvas"></button>
          </div>
          <div class="offcanvas-body py-lg-4">
            <div class="pb-0 mb-2">
              <h3 class="h6">Location</h3>
              <select class="form-select mb-2">
                <option value="" selected>Choose city</option>
                <option value="Chicago">Riyadh</option>
                <option value="Dallas">Jeddah</option>
                <option value="Los Angeles">Dammam</option>
                <option value="New York">Al-Qasim</option>
                <option value="San Diego">Abha</option>
              </select>
            </div>
            <div class="pb-0 mb-2">
              <h3 class="h6">Experience Required</h3>
              <div class="overflow-auto" data-simplebar data-simplebar-auto-hide="false" style="height: 9.5rem;">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="house">
                  <label class="form-check-label fs-sm" for="house">Fresher</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="apartment" checked>
                  <label class="form-check-label fs-sm" for="apartment">1-2 Years</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="room">
                  <label class="form-check-label fs-sm" for="room">2-5 Years</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="office">
                  <label class="form-check-label fs-sm" for="office">5-10 Years</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="commercial">
                  <label class="form-check-label fs-sm" for="commercial">10+ Years</label>
                </div>

              </div>
            </div>
            <div class="pb-0 mb-2">
              <h3 class="h6">Experienced</h3>
              <select class="form-select mb-2">
                <option value="" selected>Choose city</option>
                <option value="Chicago">Gulf Experienced</option>
                <option value="Dallas">Indian Experienced</option>
              </select>
            </div>
            <div class="pb-0 mb-2">
              <h3 class="h6 pt-1">Age</h3>
              <div class="d-flex align-items-center">
                <input class="form-control w-100" type="number" min="20" max="500" step="10" placeholder="Min">
                <div class="mx-2">&mdash;</div>
                <input class="form-control w-100" type="number" min="20" max="500" step="10" placeholder="Max">
              </div>
            </div>
            <div class="pb-0 mb-2">
              <h3 class="h6">Job Type</h3>
              <select class="form-select mb-2">
                <option value="" selected>Choose</option>
                <option value="House Driver">House Driver</option>
                <option value="House Boy">House Boy</option>
                <option value="Technician">Technician</option>
                <option value="Helper">Helper</option>
                <option value="Heavy Driver">Heavy Driver</option>
              </select>
            </div>
            <div class=" py-4">
              <button class="btn btn-outline-primary px-2" type="button"><i class="fi-rotate-right me-1"></i>Reset
                filters</button>
              <button class="btn btn-primary py-2 px-4 " type="button"><i class="fi-search me-1"></i>Search</button>
            </div>
          </div>
        </div>
      </aside>
      <!-- Page content-->
      <div class="col-lg-8 offset-lg-3 position-relative overflow-hidden pb-5 pt-2 px-3 px-xl-4 px-xxl-5">
        <!-- Map popup-->
        <div class="map-popup invisible" id="map">
          <button class="btn btn-icon btn-light btn-sm shadow-sm rounded-circle" type="button"
            data-bs-toggle-class="invisible" data-bs-target="#map"><i class="fi-x fs-xs"></i></button>
          <div class="interactive-map" data-map-options-json="json/map-options-real-estate-sale.json"></div>
        </div>
        <!-- Breadcrumb-->
        <nav class="mb-3 pt-md-2" aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Top Resumes</li>
          </ol>
        </nav>
        <!-- Title-->
        <div class="d-sm-flex align-items-center justify-content-between pb-3 pb-sm-4">
          <h1 class="h2 mb-0">Top resumes</h1>
        </div>

        <!-- Sorting-->
        <div class="d-flex flex-sm-row flex-column align-items-sm-center align-items-stretch my-2">
          <div class="d-flex align-items-center flex-shrink-0">
            <label class="fs-sm text-nowrap" for="sortby"></label>
            <select class="form-select form-select-sm me-2" id="sortby">
              <option><i class="fi-arrows-sort text-muted mt-n1 me-2"></i>Sort by:</option>
              <option>Popularity</option>
              <option>Low - High Price</option>
              <option>High - Low Price</option>
              <option>High rating</option>
              <option>Average Rating</option>
            </select>

            <button type="button" class="btn btn-outline-secondary d-md-none w-100 btn-sm" data-bs-toggle="offcanvas"
              data-bs-target="#filter-bar"
              style="font-family: Noto Sans; font-size: 14px; font-weight: 400; text-align: start;">Filter : <i
                class="fi-filter" style="float: right; margin-top: 4px;"></i>
            </button>

          </div>
          <hr class="d-none d-sm-block w-100 mx-4">
          <div class="d-none d-sm-flex align-items-center flex-shrink-0 text-muted"><i
              class="fi-check-circle me-2"></i><span class="fs-sm mt-n1">{{ $count }} results</span></div>
        </div>
        <!-- Catalog grid-->
        <div class="row g-4 py-4 resumes">
          <!-- Item-->
          @foreach ($posts as $post)
              <div class="col-sm-6 col-xl-4">
                  <div class="card shadow-sm card-hover border-0 h-100">
                      <div class="tns-carousel-wrapper card-img-top card-img-hover box-thumb">
                          <a class="img-overlay" href="{{ route('fullresume',$post->id) }}"></a>
                          <div class="content-overlay end-0 top-0 pt-3 pe-3">
                              <button class="btn btn-icon btn-light btn-xs text-primary rounded-circle" type="button"
                              data-bs-toggle="tooltip" data-bs-placement="left" title="Add to Wishlist"><i
                              class="fi-heart"></i></button>
                          </div>
                          @if ($post->photo_file != '')
                            <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="Image">
                          @else
                            <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Image">
                          @endif
                          
                      </div>
                      <div class="text-center name-candidate py-3">
                          <div class="card-body position-relative pb-3">
                          <h2 class="mb-2">{{ $post->cand_name }}</h2>
                          <div class="fw-bold"><i class="fi-briefcase mt-n1 me-2 lead align-middle opacity-70"></i>@if($post->experience != 0) {{ $post->experience }} Years @else Fresher @endif <i
                              class="fi-user mt-n1 me-2 lead align-middle opacity-70 mx-3"></i>{{ $post->job_type }}</div>
                          </div>
                          <div>
                          <a class="btn btn-primary btn-sm ms-2 mb-3 px-5" href="{{ route('fullresume',$post->id) }}">Book Now</a>
                          </div>
      
                      </div>
                  </div>
              </div>
          @endforeach
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

      </div>
    </div>
  </div>
@endsection

@section('page-script')
    
@endsection