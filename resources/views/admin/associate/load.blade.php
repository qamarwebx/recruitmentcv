<table class="datatables-users table border-top">
    <thead>
        <tr>
            {{-- <th>
                <div class="form-check form-check-inline">
                    <input class="form-check-input checkboxSelectAll" type="checkbox" id="checkboxSelectAll" />
                    <label class="form-check-label" for="checkboxSelectAll"></label>
                </div>
            </th> --}}
            <th>Name</th>
            <th>Agency Name</th>
            <th>Mobile</th>
            <th>City</th>
            <th>careoff</th>
            <th>Create By</th>
            <th>Status</th>
            <th>Contact</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="listitem">
        @if ($posts->count() > 0)
            @foreach ($posts as $post)
                @php
                    $party_name = $post->pty_full_name;
                    $stateNum = rand(0,6);
                    $statesName = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                    $stateName = $statesName[$stateNum];
                    $fullname = explode(" ",$party_name);
                    $firstword = current($fullname);
                    $lastword = end($fullname);
                    $firstCharacter = substr($firstword, 0, 1);
                    $lastCharacter = substr($lastword, 0, 1);
                    $defaultProfile = strtoupper($firstCharacter.$lastCharacter);

                @endphp
                <tr>
                    {{-- <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input dt-checkboxes sub-chk" data-id="{{ $post->id }}" type="checkbox" value="{{ $post->id }}" id="checkbox{{ $post->id }}" />
                            <label class="form-check-label" for="checkbox{{ $post->id }}"></label>
                        </div>
                    </td> --}}
                    <td>
                        <div class="d-flex justify-content-start align-items-center user-name">
                            <div class="avatar-wrapper">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-{{ $stateName }}">{{ $defaultProfile }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                @if ($party_name != '')
                                    <a href="{{ route('admin.associate.show',$post->id) }}" class="text-body text-truncate" target="_blank"><span class="fw-semibold">{{ $party_name }}</a>
                                @else
                                    <a href="{{ route('admin.associate.show',$post->id) }}" class="text-body text-truncate" target="_blank"><span class="fw-semibold">{{ '---' }}</a>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>@if($post->pty_ag_name != '') {{ $post->pty_ag_name }} @else {{ '---' }} @endif</td>
                    <td>@if($post->pty_mobile != '') {{ $post->pty_mobile }} @else {{ '---' }} @endif</td>
                    <td>@if($post->city_id != '') {{ $post->city->name }} @else {{ '---' }} @endif</td>
                    <td>@if($post->careoff_id != '') {{ $post->careoff->name }} @else {{ '---' }} @endif</td>
                    <td>{{ $post->admin->name }}</td>
                    <td>
                        @if ($post->status == '1')
                            <span class="badge bg-label-success text-capitalized" data-bs-target="#deactiveStatus" data-bs-toggle="modal" data-id="{{ $post->id }}">Active</span>
                        @else
                            <span class="badge bg-label-warning text-capitalized" data-bs-target="#activeStatus" data-bs-toggle="modal" data-id="{{ $post->id }}">Inactive</span>
                        @endif
                    </td>
                    <td>
                        @if ($post->contact_verified == '1')
                            <span class="badge bg-label-success text-capitalized">Verified</span>
                        @else
                            <span class="badge bg-label-danger text-capitalized">Not Verified</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical ti-sm mx-1"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end m-0">

                                {{-- View Associate --}}
                                <a class="dropdown-item" href="{{ route('admin.associate.show', $post->id) }}">
                                <i class="ti ti-eye me-2"></i> View
                                </a>

                                {{-- Edit Associate --}}
                                <a class="dropdown-item edcandidate" href="javascript:;" 
                                data-bs-toggle="offcanvas" 
                                data-bs-target="#edituser" 
                                data-id="{{ $post->id }}">
                                <i class="ti ti-edit me-2"></i> Edit
                                </a>

                                {{-- Delete Associate --}}
                                <a class="dropdown-item delcandidate" href="javascript:;" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteStaff" 
                                data-id="{{ $post->id }}">
                                <i class="ti ti-trash me-2"></i> Delete
                                </a>

                            </div>
                        </div>
                    </td>

                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="9">No Data Found</td>
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
