<div class="filter-div">
    @if (
        (Request::segment(1) == 'admin' && Request::segment(2) == 'candidate' && Request::segment(3) == 'list') || 
        (Request::segment(1) == 'admin' && Request::segment(2) == 'employer-list') ||
        (Request::segment(1) == 'admin' && Request::segment(2) == 'partner') ||
        (Request::segment(1) == 'admin' && Request::segment(2) == 'client') ||
        (Request::segment(1) == 'admin' && Request::segment(2) == 'booking') || 
        (Request::segment(1) == 'partner' && Request::segment(2) == 'booking') ||
        (Request::segment(1) == 'admin' && Request::segment(2) == 'contact-list') )
        <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#filter" class="filter"><i class="fa-solid fa-filter"></i></a>
    @endif
</div>
