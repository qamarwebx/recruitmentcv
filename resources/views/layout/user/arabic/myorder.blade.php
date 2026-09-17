<div class="card card-body space border-0 shadow-sm pb-1 me-lg-1 ardir">
    <div class="collapse d-md-block">
        <div class="card-nav">
            <a class="card-nav-link order-menu {{ (request()->routeIs('ar.myorder')) ?'active':'' }}" href="{{ route('ar.myorder') }}"><i class="fi-layers opacity-60 me-2"></i> طلباتي</a>
            <a class="card-nav-link" href=""><i class="fi-heart opacity-60 me-2"></i> قائمة الرغبات</a>
            <a class="card-nav-link {{ (request()->routeIs('ar.myprofile')) ?'active':'' }}" href="{{ route('ar.myprofile') }}"><i class="fi-user opacity-60 me-2"></i>حساب تعريفي</a>
            <a class="card-nav-link" href=""><i class="fi-lock opacity-60 me-2"></i> كلمة المرور والأمان</a>
            <a class="card-nav-link" href=""><i class="fi-bell opacity-60 me-2"></i> إشعارات</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}">
                <a class="card-nav-link outline" href="{{ route('logout') }}" onclick="event.preventDefault();this.closest('form').submit();"><i class="fi-logout opacity-60 me-2"></i> خروج</a>
            </form>
            {{-- <a class="card-nav-link outline" href="index.html"><i class="fi-logout opacity-60 me-2"></i>Sign Out</a> --}}
        </div>
    </div>
</div>