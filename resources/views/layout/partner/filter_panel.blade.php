<div class="filter-div">
    @if ((Request::segment(1) == 'partner' && Request::segment(2) == 'booking'))
        <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#filter" class="filter"><i class="fa-solid fa-filter"></i></a>
    @endif
</div>
