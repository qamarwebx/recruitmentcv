@if (Auth::guard('admin')->user()->user_type == 1)
  <ul class="menu-inner py-1">
    <!-- Dashboards -->

    <li class="menu-item {{ (request()->routeIs('admin.dashboard')) ?'active':'' }}">
      <a href="{{ route('admin.dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-smart-home"></i>
        <div>Dashboard</div>
      </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.booking')) || (request()->routeIs('admin.booking.view'))  ?'active':'' }}">
        <a href="{{ route('admin.booking') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
            <div>Orders</div>
        </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.employer')) ?'active':'' }}">
      <a href="{{ route('admin.employer') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-3d-cube-sphere"></i>
          <div>Employer</div>
      </a>
    </li>


    <li class="menu-item {{ (request()->routeIs('admin.client')) ?'active':'' }}">
        <a href="{{ route('admin.client') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-user"></i>
            <div>Clients</div>
        </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.candidate')) || (request()->routeIs('admin.candidate.show')) || (request()->routeIs('admin.candidate.publish')) ?'active':'' }}">
      <a href="{{ route('admin.candidate') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-users"></i>
        <div>Candidates</div>
      </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.associate')) ?'active':'' }}">
      <a href="{{ route('admin.associate') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-user-plus"></i>
        <div>Associate</div>
      </a>
    </li>

    <li class="menu-item {{ (request()->routeIs('admin.partner')) || (request()->routeIs('admin.partner.show')) ?'active':'' }}">
        <a href="{{ route('admin.partner') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-layout-sidebar"></i>
          <div>Partner</div>
        </a>
    </li>

    <!-- Layouts -->
    <li class="menu-item {{ 
      (request()->routeIs('admin.webconfig')) || 
      (request()->routeIs('admin.mail.setup.index')) || 
      (request()->routeIs('admin.permission')) || 
      (request()->routeIs('admin.personaliseclass')) || 
      (request()->routeIs('admin.template.index')) || 
      (request()->routeIs('admin.order.received.panel')) || 
      (request()->routeIs('admin.cv.setting')) || 
      (request()->routeIs('admin.whatsapp.api')) ? 'active open':'' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-settings"></i>
        <div>Settings</div>
      </a>

      <ul class="menu-sub">
        <li class="menu-item {{ (request()->routeIs('admin.webconfig')) ?'active':'' }}">
          <a href="{{ route('admin.webconfig') }}" class="menu-link">
            <div>Website Configuration</div>
          </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.mail.setup.index')) ?'active':'' }}">
          <a href="{{ route('admin.mail.setup.index') }}" class="menu-link">
            <div>Mail Setup</div>
          </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.personaliseclass')) ?'active':'' }}">
          <a href="{{ route('admin.personaliseclass') }}" class="menu-link">
            <div>Personalise Class</div>
          </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.template.index')) ?'active':'' }}">
          <a href="{{ route('admin.template.index') }}" class="menu-link">
            <div>Template</div>
          </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.order.received.panel')) ?'active':'' }}">
          <a href="{{ route('admin.order.received.panel') }}" class="menu-link">
            <div>Order Receiver Panel</div>
          </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.permission')) ?'active':'' }}">
          <a href="{{ route('admin.permission') }}" class="menu-link">
            <div>Permission</div>
          </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.cv.setting')) ?'active':'' }}">
          <a href="{{ route('admin.cv.setting') }}" class="menu-link">
            <div>CV Setting</div>
          </a>
        </li>

        <li class="menu-item {{ (request()->routeIs('admin.whatsapp.api')) ?'active':'' }}">
          <a href="{{ route('admin.whatsapp.api') }}" class="menu-link">
            <div>Whatsapp API</div>
          </a>
        </li>

      </ul>
    </li>

    <!-- Dynamic -->
    <li class="menu-item {{ 
      (request()->routeIs('admin.staff')) || 
      (request()->routeIs('admin.profession')) || 
      (request()->routeIs('admin.placeofissue')) || 
      (request()->routeIs('admin.country')) || 
      (request()->routeIs('admin.region')) || 
      (request()->routeIs('admin.city')) || 
      (request()->routeIs('admin.carknown.list')) || 
      (request()->routeIs('admin.education')) || 
      (request()->routeIs('admin.religion')) ||
      (request()->routeIs('admin.expworklocation')) ? 'active open':'' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-settings"></i>
        <div>Dynamic</div>
      </a>

      <ul class="menu-sub">
        <li class="menu-item {{ (request()->routeIs('admin.staff')) ? 'active':'' }}">
          <a href="{{ route('admin.staff') }}" class="menu-link">
            <div>Staff</div>
          </a>
        </li>
        <li class="menu-item {{ (request()->routeIs('admin.profession')) ?'active':'' }}">
          <a href="{{ route('admin.profession') }}" class="menu-link">
            <div>Profession</div>
          </a>
        </li>
        <li class="menu-item {{ (request()->routeIs('admin.placeofissue')) ?'active':'' }}">
          <a href="{{ route('admin.placeofissue') }}" class="menu-link">
            <div>Place of Issue</div>
          </a>
        </li>
        <li class="menu-item {{ (request()->routeIs('admin.expworklocation')) ?'active':'' }}">
          <a href="{{ route('admin.expworklocation') }}" class="menu-link">
            <div>Expected Work Location</div>
          </a>
        </li>
        <li class="menu-item {{ (request()->routeIs('admin.country')) ?'active':'' }}">
          <a href="{{ route('admin.country') }}" class="menu-link">
            <div>Country</div>
          </a>
        </li>
        <li class="menu-item {{ (request()->routeIs('admin.region')) ?'active':'' }}">
          <a href="{{ route('admin.region') }}" class="menu-link">
            <div>Region</div>
          </a>
        </li>
        <li class="menu-item {{ (request()->routeIs('admin.city')) ?'active':'' }}">
          <a href="{{ route('admin.city') }}" class="menu-link">
            <div>City</div>
          </a>
        </li>
        <li class="menu-item {{ (request()->routeIs('admin.carknown.list')) ?'active':'' }}">
          <a href="{{ route('admin.carknown.list') }}" class="menu-link">
            <div>Car Known</div>
          </a>
        </li>
        <li class="menu-item {{ (request()->routeIs('admin.education')) ?'active':'' }}">
          <a href="{{ route('admin.education') }}" class="menu-link">
            <div>Education</div>
          </a>
        </li>
        <li class="menu-item {{ (request()->routeIs('admin.religion')) ?'active':'' }}">
          <a href="{{ route('admin.religion') }}" class="menu-link">
            <div>Religion</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Whatsapp -->
    <li class="menu-item {{ (request()->routeIs('admin.whatsapp.campaign')) || (request()->routeIs('admin.whatsapp.templateList')) ?'active open':'' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-brand-whatsapp"></i>
        <div>Whatsapp</div>
      </a>

      <ul class="menu-sub">
        <li class="menu-item {{ (request()->routeIs('admin.whatsapp.campaign')) ?'active':'' }}">
          <a href="{{ route('admin.whatsapp.campaign') }}" class="menu-link">
            <div>Whatsapp Campaign</div>
          </a>
        </li>
        <li class="menu-item {{ (request()->routeIs('admin.whatsapp.templateList')) ?'active':'' }}">
          <a href="{{ route('admin.whatsapp.templateList') }}" class="menu-link">
            <div>Whatsapp Template</div>
          </a>
        </li>
      </ul>
    </li>


    <!-- Testing Section -->

    <li class="menu-item {{ (request()->routeIs('admin.mailtest')) ?'active open':'' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-settings"></i>
        <div>Testing</div>
      </a>

      <ul class="menu-sub {{ (request()->routeIs('admin.mailtest')) ?'active':'' }}">
        <li class="menu-item">
          <a href="{{ route('admin.mailtest') }}" class="menu-link">
            <div>Mail</div>
          </a>
        </li>
      </ul>

    </li>
  </ul>
@else
  @php
    $permission = DB::table('adminpermissions')->where('staff_id','=',Auth::guard('admin')->user()->id)->first();
  @endphp
  @if (isset($permission) && $permission->full_access == 1)
    <ul class="menu-inner py-1">
      <!-- Dashboards -->

      <li class="menu-item {{ (request()->routeIs('admin.dashboard')) ?'active':'' }}">
        <a href="{{ route('admin.dashboard') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-smart-home"></i>
          <div>Dashboard</div>
        </a>
      </li>

      <li class="menu-item {{ (request()->routeIs('admin.booking')) || (request()->routeIs('admin.booking.view'))  ?'active':'' }}">
          <a href="{{ route('admin.booking') }}" class="menu-link">
              <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
              <div>Orders</div>
          </a>
      </li>

      <li class="menu-item {{ (request()->routeIs('admin.employer')) ?'active':'' }}">
        <a href="{{ route('admin.employer') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-3d-cube-sphere"></i>
            <div>Employer</div>
        </a>
      </li>


      <li class="menu-item {{ (request()->routeIs('admin.client')) ?'active':'' }}">
          <a href="{{ route('admin.client') }}" class="menu-link">
              <i class="menu-icon tf-icons ti ti-user"></i>
              <div>Clients</div>
          </a>
      </li>

      <li class="menu-item {{ (request()->routeIs('admin.candidate')) || (request()->routeIs('admin.candidate.show')) || (request()->routeIs('admin.candidate.publish')) ?'active':'' }}">
        <a href="{{ route('admin.candidate') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-users"></i>
          <div>Candidates</div>
        </a>
      </li>

      <li class="menu-item {{ (request()->routeIs('admin.associate')) || (request()->routeIs('admin.associate.show')) || (request()->routeIs('admin.associate.publish')) ?'active':'' }}">
        <a href="{{ route('admin.associate') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-users"></i>
          <div>Associate</div>
        </a>
      </li>

      <li class="menu-item {{ (request()->routeIs('admin.partner')) || (request()->routeIs('admin.partner.show')) ?'active':'' }}">
          <a href="{{ route('admin.partner') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-layout-sidebar"></i>
            <div>Partner</div>
          </a>
      </li>

      <!-- Layouts -->
      <li class="menu-item {{ (request()->routeIs('admin.staff')) || (request()->routeIs('admin.profession')) || (request()->routeIs('admin.placeofissue')) || (request()->routeIs('admin.country')) || (request()->routeIs('admin.region')) || (request()->routeIs('admin.city')) || (request()->routeIs('admin.webconfig')) || (request()->routeIs('admin.mail.setup.index')) || (request()->routeIs('admin.carknown.list')) || (request()->routeIs('admin.permission')) || (request()->routeIs('admin.personaliseclass')) || (request()->routeIs('admin.template.index')) ? 'active open':'' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons ti ti-settings"></i>
          <div>Settings</div>
        </a>

        <ul class="menu-sub">
          <li class="menu-item {{ (request()->routeIs('admin.staff')) ? 'active':'' }}">
            <a href="{{ route('admin.staff') }}" class="menu-link">
              <div>Staff</div>
            </a>
          </li>
          <li class="menu-item {{ (request()->routeIs('admin.profession')) ?'active':'' }}">
            <a href="{{ route('admin.profession') }}" class="menu-link">
              <div>Profession</div>
            </a>
          </li>

          <li class="menu-item {{ (request()->routeIs('admin.placeofissue')) ?'active':'' }}">
            <a href="{{ route('admin.placeofissue') }}" class="menu-link">
              <div>Place of Issue</div>
            </a>
          </li>

          <li class="menu-item {{ (request()->routeIs('admin.country')) ?'active':'' }}">
            <a href="{{ route('admin.country') }}" class="menu-link">
              <div>Country</div>
            </a>
          </li>

          <li class="menu-item {{ (request()->routeIs('admin.region')) ?'active':'' }}">
            <a href="{{ route('admin.region') }}" class="menu-link">
              <div>Region</div>
            </a>
          </li>

          <li class="menu-item {{ (request()->routeIs('admin.city')) ?'active':'' }}">
            <a href="{{ route('admin.city') }}" class="menu-link">
              <div>City</div>
            </a>
          </li>

          <li class="menu-item {{ (request()->routeIs('admin.webconfig')) ?'active':'' }}">
            <a href="{{ route('admin.webconfig') }}" class="menu-link">
              <div>Website Configuration</div>
            </a>
          </li>

          <li class="menu-item {{ (request()->routeIs('admin.mail.setup.index')) ?'active':'' }}">
            <a href="{{ route('admin.mail.setup.index') }}" class="menu-link">
              <div>Mail Setup</div>
            </a>
          </li>

          <li class="menu-item {{ (request()->routeIs('admin.carknown.list')) ?'active':'' }}">
            <a href="{{ route('admin.carknown.list') }}" class="menu-link">
              <div>Car Known</div>
            </a>
          </li>

          <li class="menu-item {{ (request()->routeIs('admin.personaliseclass')) ?'active':'' }}">
            <a href="{{ route('admin.personaliseclass') }}" class="menu-link">
              <div>Personalise Class</div>
            </a>
          </li>

          <li class="menu-item {{ (request()->routeIs('admin.template.index')) ?'active':'' }}">
            <a href="{{ route('admin.template.index') }}" class="menu-link">
              <div>Template</div>
            </a>
          </li>
        </ul>
      </li>

    </ul>
  @elseif(isset($permission) && $permission->full_access == 0)
    <ul class="menu-inner py-1">
      <!-- Dashboards -->

      <li class="menu-item {{ (request()->routeIs('admin.dashboard')) ?'active':'' }}">
        <a href="{{ route('admin.dashboard') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-smart-home"></i>
          <div>Dashboard</div>
        </a>
      </li>
      @if ($permission->bookings == 1)
        <li class="menu-item {{ (request()->routeIs('admin.booking')) || (request()->routeIs('admin.booking.view'))  ?'active':'' }}">
          <a href="{{ route('admin.booking') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
            <div>Orders</div>
          </a>
        </li>          
      @endif

      @if ($permission->employer == 1)
        <li class="menu-item {{ (request()->routeIs('admin.employer')) ?'active':'' }}">
          <a href="{{ route('admin.employer') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-3d-cube-sphere"></i>
              <div>Employer</div>
          </a>
        </li>          
      @endif


      @if ($permission->client == 1)
        <li class="menu-item {{ (request()->routeIs('admin.client')) ?'active':'' }}">
          <a href="{{ route('admin.client') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-user"></i>
            <div>Clients</div>
          </a>
        </li>          
      @endif

      @if ($permission->candidate == 1)
        <li class="menu-item {{ (request()->routeIs('admin.candidate')) || (request()->routeIs('admin.candidate.show')) || (request()->routeIs('admin.candidate.publish')) ?'active':'' }}">
          <a href="{{ route('admin.candidate') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-users"></i>
            <div>Candidates</div>
          </a>
        </li>          
      @endif

      @if ($permission->associate == 1)
        <li class="menu-item {{ (request()->routeIs('admin.associate')) || (request()->routeIs('admin.associate.show')) || (request()->routeIs('admin.associate.publish')) ?'active':'' }}">
          <a href="{{ route('admin.associate') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-users"></i>
            <div>Associates</div>
          </a>
        </li>          
      @endif


      @if ($permission->partner == 1)
        <li class="menu-item {{ (request()->routeIs('admin.partner')) || (request()->routeIs('admin.partner.show')) ?'active':'' }}">
          <a href="{{ route('admin.partner') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-layout-sidebar"></i>
            <div>Partner</div>
          </a>
        </li>
      @endif

      @if ($permission->dynamic == 1)
        <!-- Layouts -->
        <li class="menu-item {{ (request()->routeIs('admin.staff')) || (request()->routeIs('admin.profession')) || (request()->routeIs('admin.placeofissue')) || (request()->routeIs('admin.country')) || (request()->routeIs('admin.region')) || (request()->routeIs('admin.city')) || (request()->routeIs('admin.webconfig')) || (request()->routeIs('admin.mail.setup.index')) || (request()->routeIs('admin.carknown.list')) || (request()->routeIs('admin.permission')) || (request()->routeIs('admin.personaliseclass')) || (request()->routeIs('admin.template.index')) ? 'active open':'' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-settings"></i>
            <div>Dynamic</div>
          </a>

          <ul class="menu-sub">

            @if ($permission->staff)
              <li class="menu-item {{ (request()->routeIs('admin.staff')) ? 'active':'' }}">
                <a href="{{ route('admin.staff') }}" class="menu-link">
                  <div>Staff</div>
                </a>
              </li>                
            @endif

            @if ($permission->profession == 1)
              <li class="menu-item {{ (request()->routeIs('admin.profession')) ?'active':'' }}">
                <a href="{{ route('admin.profession') }}" class="menu-link">
                  <div>Profession</div>
                </a>
              </li>    
            @endif

            @if ($permission->placeofissue == 1)
              <li class="menu-item {{ (request()->routeIs('admin.placeofissue')) ?'active':'' }}">
                <a href="{{ route('admin.placeofissue') }}" class="menu-link">
                  <div>Place of Issue</div>
                </a>
              </li>                
            @endif
            
            @if ($permission->country == 1)
              <li class="menu-item {{ (request()->routeIs('admin.country')) ?'active':'' }}">
                <a href="{{ route('admin.country') }}" class="menu-link">
                  <div>Country</div>
                </a>
              </li>                
            @endif

            @if ($permission->region == 1)
              <li class="menu-item {{ (request()->routeIs('admin.region')) ?'active':'' }}">
                <a href="{{ route('admin.region') }}" class="menu-link">
                  <div>Region</div>
                </a>
              </li>                
            @endif

            @if ($permission->city == 1)
              <li class="menu-item {{ (request()->routeIs('admin.city')) ?'active':'' }}">
                <a href="{{ route('admin.city') }}" class="menu-link">
                  <div>City</div>
                </a>
              </li>                
            @endif
          </ul>
        </li>
      @endif

      @if ($permission->settings == 1)
        <!-- Layouts -->
        <li class="menu-item {{ (request()->routeIs('admin.webconfig')) || (request()->routeIs('admin.mail.setup.index')) || (request()->routeIs('admin.carknown.list')) || (request()->routeIs('admin.permission')) || (request()->routeIs('admin.personaliseclass')) || (request()->routeIs('admin.template.index')) ? 'active open':'' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-settings"></i>
            <div>Settings</div>
          </a>

          <ul class="menu-sub">

            @if ($permission->websiteconfig == 1)
              <li class="menu-item {{ (request()->routeIs('admin.webconfig')) ?'active':'' }}">
                <a href="{{ route('admin.webconfig') }}" class="menu-link">
                  <div>Website Configuration</div>
                </a>
              </li>                
            @endif

            @if ($permission->mailsetup == 1)
              <li class="menu-item {{ (request()->routeIs('admin.mail.setup.index')) ?'active':'' }}">
                <a href="{{ route('admin.mail.setup.index') }}" class="menu-link">
                  <div>Mail Setup</div>
                </a>
              </li>                
            @endif

            @if ($permission->carknown == 1)
              <li class="menu-item {{ (request()->routeIs('admin.carknown.list')) ?'active':'' }}">
                <a href="{{ route('admin.carknown.list') }}" class="menu-link">
                  <div>Car Known</div>
                </a>
              </li>                
            @endif

            @if ($permission->personalise_class == 1)
              <li class="menu-item {{ (request()->routeIs('admin.personaliseclass')) ?'active':'' }}">
                <a href="{{ route('admin.personaliseclass') }}" class="menu-link">
                  <div>Personalise Class</div>
                </a>
              </li>                  
            @endif


            @if ($permission->template == 1)
              <li class="menu-item {{ (request()->routeIs('admin.template.index')) ?'active':'' }}">
                <a href="{{ route('admin.template.index') }}" class="menu-link">
                  <div>Template</div>
                </a>
              </li>
            @endif

          </ul>
        </li>
      @endif
    </ul>
  @else
    <div><p>Permission are not granted</p></div>
  @endif
@endif