<ul class="nav nav-tabs mb-4" id="activitySubTabs" role="tablist">

    <li class="nav-item" role="presentation">
        <button class="nav-link active"
                id="lead-assignment-tab"
                data-bs-toggle="tab"
                data-bs-target="#lead-assignment-pane"
                type="button"
                role="tab">
            <i class="ti ti-user-plus me-1"></i>
            Lead Assignment
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="qualification-tab"
                data-bs-toggle="tab"
                data-bs-target="#qualification-pane"
                type="button"
                role="tab">
            <i class="ti ti-user-check me-1"></i>
            Qualification Status
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="meta-tab"
                data-bs-toggle="tab"
                data-bs-target="#meta-pane"
                type="button"
                role="tab">
            <i class="ti ti-brand-meta me-1"></i>
            Meta CAPI
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="google-tab"
                data-bs-toggle="tab"
                data-bs-target="#google-pane"
                type="button"
                role="tab">
            <i class="ti ti-brand-google me-1"></i>
            Google Conversion
        </button>
    </li>

</ul>

<div class="tab-content">

    {{-- ================= Lead Assignment ================= --}}
    <div class="tab-pane fade show active" id="lead-assignment-pane" role="tabpanel">

        @if($lead_assign_action->count())

            <ul class="timeline mt-3 mb-0">

                @foreach($lead_assign_action as $activity)

                    @php
                        $data = $activity->activity;

                        // Older records predate the assignment_type field —
                        // fall back to the admin_id that was already recorded
                        // (present only for manual assignto/bulkassignto calls).
                        $assignmentType = $data['assignment_type'] ?? ($activity->admin_id ? 'Manual' : 'Auto');
                        $assignedBy = $activity->admin->name ?? 'System';
                    @endphp

                    <li class="timeline-item timeline-item-warning pb-4 border-left-dashed">

                        <span class="timeline-indicator timeline-indicator-warning">
                            <i class="ti ti-user-plus"></i>
                        </span>

                        <div class="timeline-event">

                            <div class="timeline-header border-bottom mb-3">

                                <h6 class="mb-0">
                                    {{ $data['action'] ?? 'Lead Assignment' }}

                                    @if($assignmentType === 'Auto')
                                        <span class="badge bg-label-info ms-1">
                                            <i class="ti ti-robot me-1"></i>
                                            Auto Assignment
                                        </span>
                                    @else
                                        <span class="badge bg-label-primary ms-1">
                                            <i class="ti ti-user me-1"></i>
                                            Manual Assignment
                                        </span>
                                    @endif
                                </h6>

                                <span class="text-muted">
                                    {{ $activity->created_at->format('d M Y, h:i A') }}
                                </span>

                            </div>

                            @if(isset($data['old_assign']) || isset($data['new_assign']))
                                <p class="mb-2">

                                    Assigned from

                                    <span class="badge bg-label-danger">
                                        {{ $data['old_assign'] ?? 'Unassigned' }}
                                    </span>

                                    to

                                    <span class="badge bg-label-success">
                                        {{ $data['new_assign'] ?? 'Unassigned' }}
                                    </span>

                                </p>
                            @endif

                            <div class="d-flex align-items-center mt-3">

                                <div class="avatar me-3">

                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ strtoupper(substr($assignedBy, 0, 1)) }}
                                    </span>

                                </div>

                                <div>

                                    <h6 class="mb-0">
                                        Assigned By: {{ $assignedBy }}
                                    </h6>

                                    <small class="text-muted">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </small>

                                </div>

                            </div>

                        </div>

                    </li>

                @endforeach

            </ul>

        @else

            <div class="text-center py-5">

                <i class="ti ti-user-share fs-1 text-muted"></i>

                <h6 class="mt-3">
                    No lead assignment activity found
                </h6>

            </div>

        @endif

    </div>

    {{-- ================= Qualification ================= --}}
    <div class="tab-pane fade" id="qualification-pane" role="tabpanel">

        @if($qualified_status->count())

            <ul class="timeline mt-3 mb-0">

                @foreach($qualified_status as $activity)

                    @php
                        $data = $activity->activity;
                    @endphp

                    <li class="timeline-item timeline-item-primary pb-4 border-left-dashed">

                        <span class="timeline-indicator timeline-indicator-primary">
                            <i class="ti ti-history"></i>
                        </span>

                        <div class="timeline-event">

                            <div class="timeline-header border-bottom mb-3">

                                <h6 class="mb-0">
                                    {{ $data['action'] ?? 'Activity' }}
                                </h6>

                                <span class="text-muted">
                                    {{ $activity->created_at->format('d M Y h:i A') }}
                                </span>

                            </div>

                            @if(isset($data['old_status']) || isset($data['new_status']))
                                <p class="mb-2">
                                    Status changed from

                                    <span class="badge bg-label-danger">
                                        {{ $data['old_status'] }}
                                    </span>

                                    to

                                    <span class="badge bg-label-success">
                                        {{ $data['new_status'] }}
                                    </span>
                                </p>
                            @endif

                            @if(!empty($data['reason']))
                                <p class="mb-2">
                                    <strong>Reason:</strong>
                                    {{ $data['reason'] }}
                                </p>
                            @endif

                            @if(!empty($data['conversation_type']))
                                <p class="mb-2">
                                    <strong>Conversation:</strong>
                                    {{ ucfirst($data['conversation_type']) }}
                                </p>
                            @endif

                            <div class="d-flex align-items-center mt-3">

                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ strtoupper(substr($activity->admin->name ?? 'S',0,1)) }}
                                    </span>
                                </div>

                                <div>

                                    <h6 class="mb-0">
                                        {{ $activity->admin->name ?? 'System' }}
                                    </h6>

                                    <small class="text-muted">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </small>

                                </div>

                            </div>

                        </div>

                    </li>

                @endforeach

            </ul>

        @else

            <div class="text-center py-5">

                <i class="ti ti-history fs-1 text-muted"></i>

                <h6 class="mt-3">
                    No qualification activity found
                </h6>

            </div>

        @endif

    </div>

    {{-- ================= Meta CAPI ================= --}}
    <div class="tab-pane fade" id="meta-pane" role="tabpanel">

        @if($meta_capi->count())

            <ul class="timeline mt-3 mb-0">

                @foreach($meta_capi as $activity)

                    @php
                        $data = $activity->activity;
                    @endphp

                    <li class="timeline-item timeline-item-success pb-4 border-left-dashed">

                        <span class="timeline-indicator timeline-indicator-success">
                            <i class="ti ti-brand-meta"></i>
                        </span>

                        <div class="timeline-event">

                            <div class="timeline-header border-bottom mb-3">

                                <h6 class="mb-0">
                                    {{ $data['action'] ?? 'Meta Conversion API' }}
                                </h6>

                                <span class="text-muted">
                                    {{ $activity->created_at->format('d M Y h:i A') }}
                                </span>

                            </div>

                            @if(!empty($data['event_name']))
                                <p class="mb-2">
                                    <strong>Event Name :</strong>

                                    <span class="badge bg-label-primary">
                                        {{ $data['event_name'] }}
                                    </span>
                                </p>
                            @endif

                            @if(!empty($data['fbtrace_id']))
                                <p class="mb-2">
                                    <strong>FB Trace ID :</strong>
                                    <code>{{ $data['fbtrace_id'] }}</code>
                                </p>
                            @endif

                            @if(!empty($data['status']))
                                <div class="d-flex align-items-center mb-2">

                                    <strong class="me-2">Status :</strong>

                                    @if(strtolower($data['status']) === 'success')

                                        <span class="badge rounded-pill bg-label-success">
                                            <i class="ti ti-circle-check me-1"></i>
                                            Success
                                        </span>

                                    @else

                                        <span class="badge rounded-pill bg-label-danger">
                                            <i class="ti ti-alert-circle me-1"></i>
                                            Failed
                                        </span>

                                    @endif

                                </div>
                            @endif

                            @if(!empty($data['message']))
                                <p class="mb-2">
                                    <strong>Message :</strong>
                                    {{ $data['message'] }}
                                </p>
                            @endif

                            <div class="d-flex align-items-center mt-3">

                                <div class="avatar me-3">

                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ strtoupper(substr($activity->admin->name ?? 'S',0,1)) }}
                                    </span>

                                </div>

                                <div>

                                    <h6 class="mb-0">
                                        {{ $activity->admin->name ?? 'System' }}
                                    </h6>

                                    <small class="text-muted">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </small>

                                </div>

                            </div>

                        </div>

                    </li>

                @endforeach

            </ul>

        @else

            <div class="text-center py-5">

                <i class="ti ti-brand-meta fs-1 text-muted"></i>

                <h6 class="mt-3">
                    No Meta CAPI activity found
                </h6>

            </div>

        @endif

    </div>

    {{-- ================= Google ================= --}}
    <div class="tab-pane fade" id="google-pane" role="tabpanel">

        <div class="card border shadow-none">

            <div class="card-body text-center py-5">

                <i class="ti ti-brand-google text-danger" style="font-size:60px;"></i>

                <h5 class="mt-3">
                    Google Conversion
                </h5>

                <p class="text-muted mb-0">
                    Google Ads conversion activity will be displayed here.
                </p>

            </div>

        </div>

    </div>

</div>