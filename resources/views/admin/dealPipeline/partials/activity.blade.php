@foreach($activities as $log)
    @php
        $data = $log->activity ?? [];
        $performedBy = $log->admin->name ?? 'System';

        $moduleMeta = [
            'deal_created'       => ['color' => 'success',  'icon' => 'ti-plus'],
            'deal_stage_change'  => ['color' => 'primary',  'icon' => 'ti-arrows-right'],
            'deal_status_change' => ['color' => 'info',     'icon' => 'ti-status-change'],
            'deal_assign_action' => ['color' => 'warning',  'icon' => 'ti-user-plus'],
            'deal_amount_change' => ['color' => 'success',  'icon' => 'ti-currency-dollar'],
            'deal_updated'       => ['color' => 'secondary','icon' => 'ti-edit'],
        ];

        $meta = $moduleMeta[$log->module] ?? ['color' => 'secondary', 'icon' => 'ti-history'];
    @endphp

    <li class="timeline-item timeline-item-{{ $meta['color'] }} pb-4 border-left-dashed">

        <span class="timeline-indicator timeline-indicator-{{ $meta['color'] }}">
            <i class="ti {{ $meta['icon'] }}"></i>
        </span>

        <div class="timeline-event">

            <div class="timeline-header border-bottom mb-3">

                <h6 class="mb-0">
                    {{ $data['action'] ?? 'Deal Activity' }}

                    @if($log->module === 'deal_assign_action')
                        @if(($data['assignment_type'] ?? 'Manual') === 'Auto')
                            <span class="badge bg-label-info ms-1">
                                <i class="ti ti-robot me-1"></i> Auto Assignment
                            </span>
                        @else
                            <span class="badge bg-label-primary ms-1">
                                <i class="ti ti-user me-1"></i> Manual Assignment
                            </span>
                        @endif
                    @endif
                </h6>

                <span class="text-muted">
                    {{ $log->created_at->format('d M Y, h:i A') }}
                </span>

            </div>

            @switch($log->module)

                @case('deal_created')
                    @if(!empty($data['deal_name']))
                        <p class="mb-2">
                            <strong>Deal:</strong> {{ $data['deal_name'] }}
                        </p>
                    @endif
                    @break

                @case('deal_stage_change')
                    <p class="mb-2">
                        Stage changed from
                        <span class="badge bg-label-danger">{{ $data['old_stage'] ?? 'Not Set' }}</span>
                        to
                        <span class="badge bg-label-success">{{ $data['new_stage'] ?? 'Not Set' }}</span>
                    </p>
                    @break

                @case('deal_status_change')
                    <p class="mb-2">
                        Status changed from
                        <span class="badge bg-label-danger">{{ $data['old_status'] ?? 'Not Set' }}</span>
                        to
                        <span class="badge bg-label-success">{{ $data['new_status'] ?? 'Not Set' }}</span>
                    </p>
                    @break

                @case('deal_assign_action')
                    <p class="mb-2">
                        Assigned from
                        <span class="badge bg-label-danger">{{ $data['old_assign'] ?? 'Unassigned' }}</span>
                        to
                        <span class="badge bg-label-success">{{ $data['new_assign'] ?? 'Unassigned' }}</span>
                    </p>
                    @break

                @case('deal_amount_change')
                    <p class="mb-2">
                        Amount changed from
                        <span class="badge bg-label-danger">{{ $data['old_amount'] ?? '0' }}</span>
                        to
                        <span class="badge bg-label-success">{{ $data['new_amount'] ?? '0' }}</span>
                    </p>
                    @break

                @case('deal_updated')
                    @foreach(($data['changes'] ?? []) as $change)
                        <p class="mb-2">
                            <strong>{{ $change['field'] }}:</strong>
                            <span class="badge bg-label-danger">{{ $change['old'] ?? '---' }}</span>
                            to
                            <span class="badge bg-label-success">{{ $change['new'] ?? '---' }}</span>
                        </p>
                    @endforeach
                    @break

            @endswitch

            <div class="d-flex align-items-center mt-3">

                <div class="avatar me-3">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                        {{ strtoupper(substr($performedBy, 0, 1)) }}
                    </span>
                </div>

                <div>
                    <h6 class="mb-0">{{ $performedBy }}</h6>
                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                </div>

            </div>

        </div>

    </li>
@endforeach
