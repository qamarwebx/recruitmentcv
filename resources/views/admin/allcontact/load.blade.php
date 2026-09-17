<table class="datatables-users table border-top">
    <thead>
        <tr>
            <th>
                <div class="form-check form-check-inline">
                    <input class="form-check-input checkboxSelectAll" type="checkbox" id="checkboxSelectAll" />
                    <label class="form-check-label" for="checkboxSelectAll"></label>
                </div>
            </th>
            <th>Name</th>
            @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1) || (isset($permission) && $permission->allcontact_company_column == 1))
                <th>Company</th>
            @endif
            <th>Business</th>
            <th>Stage</th>
            <th>Priority</th>
            <th>Opt In</th>
            <th>Group</th>
            <th>Careoff</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody class="listitem">
        @if ($posts->count() > 0)
            @foreach ($posts as $post)
                @php
                    $stateNum = rand(0,6);
                    $states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                    $state = $states[$stateNum];
                    $fullname = explode(" ",$post->full_name);
                    $firstword = current($fullname);
                    $lastword = end($fullname);
                    $firstCharacter = substr($firstword, 0, 1);
                    $lastCharacter = substr($lastword, 0, 1);
                    $defaultProfile = strtoupper($firstCharacter.$lastCharacter);

                    if ($post->lead_prority == 'Warm') {
                        $badgelabel = 'success';
                        $lead_priority_title = $post->lead_prority;
                    }elseif ($post->lead_prority == 'Cold') {
                        $badgelabel = 'info';
                        $lead_priority_title = $post->lead_prority;
                    }elseif ($post->lead_prority == 'Hot') {
                        $badgelabel = 'warning';
                        $lead_priority_title = $post->lead_prority;
                    }else{
                        $badgelabel = 'secondary';
                        $lead_priority_title = "Select";
                    }


                @endphp
                <tr>
                    <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input dt-checkboxes sub-chk" data-id="{{ $post->id }}" type="checkbox" value="{{ $post->id }}" id="checkbox{{ $post->id }}" />
                            <label class="form-check-label" for="checkbox{{ $post->id }}"></label>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-start align-items-center user-name">
                            <div class="avatar-wrapper">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-{{ $state }}">{{ $defaultProfile }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                @if ($post->full_name != '')
                                    <a href="{{ route('admin.allcontact.show',$post->id) }}" class="text-body text-truncate" target="_blank"><span class="fw-semibold">{{ $post->full_name }}</a>
                                @else
                                    <a href="{{ route('admin.allcontact.show',$post->id) }}" class="text-body text-truncate" target="_blank"><span class="fw-semibold">{{ '---' }}</a>
                                @endif
                            </div>
                        </div>
                    </td>
                    @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1) || (isset($permission) && $permission->allcontact_company_column == 1))
                        <td>
                            @if ($post->company_name != '')
                                {{ substr($post->company_name,0,20).'...' }}
                            @elseif ($post->office_name != '')
                                {{ substr($post->office_name,0,20).'...' }}
                            @else
                                {{ '---' }}
                            @endif
                        </td>
                    @endif

                    <td>{{ $post->lead_type }}</td>
                    <td>
                        <a href="javascript:void(0);" class="text-body contact_id_{{$post->id}}" data-bs-target="#editStatus" data-id="{{ $post->id }}" data-bs-toggle="modal">
                            @if ($post->ls_id != '')
                                {{ $post->ls->name }}
                            @else
                                {{ 'Select' }}

                            @endif
                            <i class='ti ti-chevron-down ti-sm me-2'></i>
                        </a>
                    </td>
                    <td>
                        <span class="badge rounded-pill bg-label-{{ $badgelabel }} dropdown-toggle hide-arrow" data-bs-toggle="dropdown"> {{ $lead_priority_title }} <i class='ti ti-chevron-down ti-sm'></i></span>
                        <div class="dropdown-menu dropdown-menu-end m-0">
                            <a href="javascript:void(0);" data-id="{{ $post->id }}" class="dropdown-item lead-priority">Cold</a>
                            <a href="javascript:void(0);" data-id="{{ $post->id }}" class="dropdown-item lead-priority">Warm</a>
                            <a href="javascript:void(0);" data-id="{{ $post->id }}" class="dropdown-item lead-priority">Hot</a>


                        </div>
                    </td>
                    <td>
                        <a href="javascript::void(0);" class="text-body" data-bs-toggle="modal" data-bs-target="#editOptin" data-id="{{ $post->id }}">
                            @if ($post->optinout != '')
                                @if ($post->optinout == 1)
                                    {{ 'Opt In' }}
                                @else
                                    {{ 'Opt Out' }}
                                @endif
                            @else
                                {{ 'Select' }}
                            @endif
                            <i class='ti ti-chevron-down ti-sm'></i>
                        </a>
                    </td>
                    {{-- <td>@if($post->city_id != '') {{ $post->city->name }} @else {{ '---' }} @endif</td> --}}
                    <td>@if($post->group_id != '') {{ $post->group->name }} @else {{ '---' }} @endif</td>
                    <td>@if($post->careoff_id != '') {{ $post->careoff->name }} @else {{ '---' }} @endif</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="javascript::void(0);" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-brand-whatsapp ti-sm mx-1"></i></a>
                            <div class="dropdown-menu dropdown-menu-end m-0">
                                @if ($post->primary_no_wsp != '')
                                    <a href="https://wa.me/{{ $post->primary_no_wsp }}" class="dropdown-item text-success">{{ $post->primary_no_wsp }}</a>
                                @endif
                                @if ($post->mobile_no1_wsp != '')
                                    <a href="https://wa.me/{{ $post->mobile_no1_wsp }}" class="dropdown-item text-success">{{ $post->mobile_no1_wsp }}</a>
                                @endif
                                @if ($post->mobile_no2_wsp != '')
                                    <a href="https://wa.me/{{ $post->mobile_no2_wsp }}" class="dropdown-item text-success">{{ $post->mobile_no2_wsp }}</a>
                                @endif
                                @if ($post->mobile_no3_wsp != '')
                                    <a href="https://wa.me/{{ $post->mobile_no3_wsp }}" class="dropdown-item text-success">{{ $post->mobile_no3_wsp }}</a>
                                @endif
                            </div>
                            <a href="#" class="text-primary"><i class="ti ti-message ti-sm mx-2"></i></a>
                            <a href="javascript::void(0);" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>
                            <div class="dropdown-menu dropdown-menu-end m-0">
                                    @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1))
                                    <a href="#" data-bs-target="#updateReg" data-id="{{ $post->id }}" data-bs-toggle="offcanvas" class="dropdown-item allcup"><i  class="ti ti-edit ti-sm"></i> Edit</a>
                                        <a href="{{ route('admin.allcontact.show',$post->id) }}" class="dropdown-item" target="_blank"><i class="ti ti-eye ti-sm"></i> View</a>
                                        <a href="#" data-bs-target="#deletescon" data-bs-toggle="modal" data-id="{{ $post->id }}" class="dropdown-item allcd"><i class="ti ti-trash ti-sm"></i> Delete</a>
                                    @elseif (isset($permission) && $permission->full_access == 0)
                                        @if ($permission->allcontact_edit == 1)
                                            <a href="#" data-bs-target="#updateReg" data-id="{{ $post->id }}" data-bs-toggle="offcanvas" class="dropdown-item allcup"><i  class="ti ti-edit ti-sm"></i> Edit</a>
                                        @endif
                                        <a href="{{ route('admin.allcontact.show',$post->id) }}" class="dropdown-item" target="_blank"><i class="ti ti-eye ti-sm"></i> View</a>
                                        @if ($permission->allcontact_delete == 1)
                                            <a href="#" data-bs-target="#deletescon" data-bs-toggle="modal" data-id="{{ $post->id }}" class="dropdown-item allcd"><i class="ti ti-trash ti-sm"></i> Delete</a>
                                        @endif
                                @endif


                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="10" class="text-center">No Data Found</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="px-4 pt-3">
    <div class="float-start">
        {{ 'Showing '.$posts->firstItem().' to '.$posts->lastItem().' of '.$posts->total().' Entries' }}
    </div>
    <div class="float-end">
        {{ $posts->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
