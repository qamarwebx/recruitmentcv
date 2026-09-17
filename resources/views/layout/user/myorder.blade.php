<div class="card card-body space border-0 shadow-sm pb-1 me-lg-1">
    <div class="collapse d-md-block">
        <div class="card-nav">
            <a class="card-nav-link order-menu {{ (request()->routeIs('myorder')) ?'active':'' }}" href="{{ route('myorder') }}"><i class="fi-layers opacity-60 me-2"></i>My Orders</a>
            <a class="card-nav-link" href=""><i class="fi-heart opacity-60 me-2"></i>Wishlist</a>
            <a class="card-nav-link {{ (request()->routeIs('myprofile')) ?'active':'' }}" href="{{ route('myprofile') }}"><i class="fi-user opacity-60 me-2"></i>Profile</a>
            <a class="card-nav-link" href=""><i class="fi-lock opacity-60 me-2"></i>Password &amp;Security</a>
            <a class="card-nav-link" href=""><i class="fi-bell opacity-60 me-2"></i>Notifications</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}">
                <a class="card-nav-link outline" href="{{ route('logout') }}" onclick="event.preventDefault();this.closest('form').submit();"><i class="fi-logout opacity-60 me-2"></i>Sign Out</a>
            </form>
            {{-- <a class="card-nav-link outline" href="index.html"><i class="fi-logout opacity-60 me-2"></i>Sign Out</a> --}}
        </div>
    </div>
</div>