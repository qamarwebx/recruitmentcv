<ul class="menu-inner py-1">
    <!-- Dashboards -->

    {{-- <li class="menu-item {{ (request()->routeIs('partner.dashboard')) ?'active':'' }}">
      <a href="{{ route('partner.dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-smart-home"></i>
        <div>Dashboard</div>
      </a>
    </li> --}}

    <li class="menu-item {{ (request()->routeIs('partner.booking')) || (request()->routeIs('partner.booking.show')) ?'active':'' }}">
        <a href="{{ route('partner.booking') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
            <div>{{ __('locale.Orders') }}</div>
        </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('partner.employer')) ?'active':'' }}">
      <a href="{{ route('partner.employer') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-users"></i>
          <div>{{ __('locale.Employer') }}</div>
      </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('partner.client')) ?'active':'' }}">
      <a href="{{ route('partner.client') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-user"></i>
          <div>{{ __('locale.Customer') }}</div>
      </a>
    </li>

       <!-- Finance Management -->
    <li class="menu-item {{
        (request()->routeIs('partner.candidate.transactionList')) ||
        (request()->routeIs('partner.invoice.list')) ||
        (request()->routeIs('partner.invoice.show')) ||
        (request()->routeIs('partner.payment.list')) ?'active open':'' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons ti ti-currency-rupee"></i>
          <div>Finance</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{
            (request()->routeIs('partner.invoice.list')) ||
            (request()->routeIs('partner.payment.list')) ||
            (request()->routeIs('partner.invoice.show')) ?'active open':'' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">Client</a>
            <ul class="menu-sub">
                <li class="menu-item {{
                (request()->routeIs('partner.invoice.list')) ||
                (request()->routeIs('partner.invoice.show')) ?'active':'' }}">
                    <a href="{{ route('partner.invoice.list') }}" class="menu-link">
                        <div>Sale Invoices</div>
                    </a>
                </li>
                <li class="menu-item {{ (request()->routeIs('partner.payment.list')) ?'active':'' }}">
                    <a href="{{ route('partner.payment.list') }}" class="menu-link">
                        <div>Payments</div>
                    </a>
                </li>
            </ul>
          </li>
        </ul>
    </li>



  </ul>