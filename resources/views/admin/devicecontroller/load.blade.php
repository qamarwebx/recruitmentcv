@forelse($devices as $device)
<tr data-id="{{ $device->id }}">
    <td>
        @if($device->admin)
            <a href="javascript:void(0);" 
               class="text-primary fw-semibold viewAdminBtn" 
               data-admin-id="{{ $device->admin->id }}">
                {{ $device->admin->name }}
            </a>
        @else
            N/A
        @endif
    </td>

    <td>
        @if($device->admin)
            <a href="javascript:void(0);" 
               class="text-primary fw-semibold viewAdminBtn" 
               data-admin-id="{{ $device->admin->id }}">
                {{ $device->admin->username }}
            </a>
        @else
            N/A
        @endif
    </td>

    <td>
        {{ $device->updated_at ? $device->updated_at->format('d M Y H:i A') : '--' }}
    </td>
</tr>
@empty
<tr>
    <td colspan="3" class="text-center">No Devices Found</td>
</tr>
@endforelse