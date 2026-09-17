@if($activities->count() > 0)
    <style>
        /* Force dropdown to stay inside modal */
        .modal .dropdown-menu {
            position: absolute !important;
            transform: translate3d(0, 35px, 0) !important;
            max-width: 260px;
            white-space: nowrap;
            z-index: 2000;
        }
        
        .table-responsive {
            padding-bottom: 107px;
        }   

    </style>
    <div class="table-responsive">
        <table id="adminActivityTable" class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Device Type</th>
                    <th>Browser</th>
                    <th>OS</th>
                    <th>IP Address</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Accuracy</th>
                    <th>Map</th>
                    <th>Live</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $device)
                <tr data-id="{{ $device->id }}">
                    <td>{{ $device->updated_at?->format('d M Y H:i') ?? '--' }}</td>
                    <td>{{ $device->device_type ?? '--' }}</td>
                    <td>{{ $device->browser ?? '--' }}</td>
                    <td>{{ $device->os ?? '--' }}</td>
                    <td>
                        @if($device->ip_address)
                            <span class="ip-text">{{ Str::limit($device->ip_address, 10) }}</span>
                            <a href="javascript:void(0);" class="copy-ip text-primary" data-ip="{{ $device->ip_address }}">Copy</a>
                        @else
                            --
                        @endif
                    </td>
                    <td>{{ $device->latitude ?? '--' }}</td>
                    <td>{{ $device->longitude ?? '--' }}</td>
                    <td>{{ $device->accuracy !== null ? '±'.round($device->accuracy).'m' : '--' }}</td>
                    <td>
                        @if($device->latitude !== null && $device->longitude !== null)
                            <a href="https://www.google.com/maps?q={{ $device->latitude }},{{ $device->longitude }}" target="_blank" rel="noopener" class="text-primary">
                                <i class="ti ti-map-pin ti-sm"></i> View
                            </a>
                        @else
                            --
                        @endif
                    </td>
                    <td>
                        @if($device->login_status == 'Active')
                            <span class="badge bg-label-success">Active</span>
                        @else
                            <span class="badge bg-label-warning text-dark">InActive</span>
                        @endif
                    </td>
                    <td>
                        @if($device->is_approved)
                            <span class="badge bg-label-success">Approved</span>
                        @else
                            <span class="badge bg-label-warning text-dark">Pending</span>
                        @endif
                    </td>
                    <td>
    <div class="dropdown position-relative">

        <!-- Dropdown Toggle -->
        <a href="javascript:void(0);" 
           class="text-body dropdown-toggle hide-arrow"
           data-bs-toggle="dropdown"
           data-bs-display="static"  aria-expanded="false">
            <i class="ti ti-dots-vertical ti-sm mx-1"></i>
        </a>

        @php
            $showDropdown = false;
            if (
                Auth::guard('admin')->user()->user_type == 1 ||
                ($permission->full_access ?? 0) ||
                ($permission->access_allowed_ip_revoke ?? 0) ||
                ($permission->access_allowed_ip_approved_by ?? 0) ||
                ($permission->access_allowed_ip_delete ?? 0)
            ) {
                $showDropdown = true;
            }
        @endphp

        @if($showDropdown)
        <ul class="dropdown-menu dropdown-menu-end shadow m-0">

            {{-- Approve / Revoke --}}
            @if(
                Auth::guard('admin')->user()->user_type == 1 ||
                ($permission->full_access ?? 0) ||
                ($permission->access_allowed_ip_revoke ?? 0)
            )
                @if(!$device->is_approved)
                    <li>
                        <button class="dropdown-item btn-approve" data-id="{{ $device->id }}">
                            <i class="ti ti-check ti-sm"></i> Approve
                        </button>
                    </li>
                @else
                    <li>
                        <button class="dropdown-item btn-revoke" data-id="{{ $device->id }}">
                            <i class="ti ti-x ti-sm"></i> Revoke
                        </button>
                    </li>
                @endif
            @endif

            {{-- Approved By --}}
            @if(
                Auth::guard('admin')->user()->user_type == 1 ||
                ($permission->full_access ?? 0) ||
                ($permission->access_allowed_ip_approved_by ?? 0)
            )
                <li>
                    <button type="button" 
                        class="dropdown-item approved-by-btn"
                        data-approved-by="{{ $device->approvedBy->name ?? '--' }}"
                        data-device-type="{{ $device->approved_by_device_type ?? '--' }}"
                        data-browser="{{ $device->approved_by_browser ?? '--' }}"
                        data-os="{{ $device->approved_by_os ?? '--' }}"
                        data-ip="{{ $device->approved_by_ip ?? '--' }}"
                        data-location="{{ $device->approved_by_location ?? '--' }}"
                        data-comments="{{ $device->comments ?? '--' }}">
                        <i class="ti ti-user-check ti-sm"></i> Approved By
                    </button>
                </li>
            @endif

            {{-- Delete --}}
            @if(
                Auth::guard('admin')->user()->user_type == 1 ||
                ($permission->full_access ?? 0) ||
                ($permission->access_allowed_ip_delete ?? 0)
            )
                <li>
                    <button class="dropdown-item text-danger btn-delete" data-id="{{ $device->id }}">
                        <i class="ti ti-trash ti-sm"></i> Delete
                    </button>
                </li>
            @endif

        </ul>
        @endif

    </div>
</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
<p class="text-center mb-0 text-muted">No activity found for this admin.</p>
@endif
