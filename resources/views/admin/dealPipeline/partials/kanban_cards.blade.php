@php
use Illuminate\Support\Facades\DB;

if (!function_exists('getDealBadgeColor')) {
    function getDealBadgeColor($priority) {
        return match($priority) {
            'Hot' => 'danger',
            'Warm' => 'warning',
            'Cold' => 'info',
            default => 'primary',
        };
    }
}

if (!function_exists('getAssignees')) {
    function getAssignees($assignto_id) {
        if (!$assignto_id) return collect();
        return DB::table('admins')
            ->whereIn('id', explode(',', $assignto_id))
            ->get();
    }
}

$canDealAdd = isset($perms) && $perms->deal_add != 0;
$canDealView = isset($perms) && $perms->deal_view != 0;
$canDealEdit = isset($perms) && $perms->deal_edit != 0;
$canDealDelete = isset($perms) && $perms->deal_delete != 0;
$canDealUpdateStage = isset($perms) && $perms->deal_update_stage != 0;
$canDealUpdateRecruitStatus = isset($perms) && $perms->deal_update_recruit_status != 0;


@endphp

@foreach ($deals as $deal)

@php
    $badgeColor   = getDealBadgeColor($deal->priority ?? 'Normal');
    $businessName = optional($deal->business)->name ?? '';
    $assignees    = getAssignees($deal->assignto_id ?? '');
@endphp

<div class="kanban-card" data-id="{{ $deal->id }}">
    <div class="card mb-3 shadow-sm">
        <div class="card-body p-2">

            {{-- Top row: Badges + Actions --}}
            <div class="d-flex align-items-center mb-2">

                {{-- Left: Badges --}}
                <div class="d-flex align-items-center gap-1">
                    <span class="badge bg-label-{{ $badgeColor }}" style="font-size: 10px;">
                        {{ $deal->priority ?? 'Normal' }}
                    </span>

                    <span class="badge bg-label-secondary" style="font-size: 10px;">
                        {{ $businessName }}
                    </span>
                </div>

                {{-- Right: Actions --}}
                <div class="ms-auto">
                    <ul class="list-inline mb-0 d-flex align-items-center">

                        {{-- View --}}
                        <li class="list-inline-item">
                            <a href="javascript:void(0);"
                            data-bs-toggle="modal"
                            onclick="openViewDealModal({{ $deal->id }})"
                            data-bs-target="#viewDealModal"
                            title="View Deal">
                                <i class="tf-icons ti ti-eye ti-sm"></i>
                            </a>
                        </li>

                        {{-- Reminder --}}
                        <li class="list-inline-item">
                            <a href="javascript:void(0);"
                            class="openReminderModal"
                            data-id="{{ $deal->id }}"
                            data-bs-toggle="modal"
                            data-bs-target="#viewDealReminderModal"
                            title="View Reminder">

                                <i class="tf-icons ti ti-bell ti-sm"></i>
                            </a>
                        </li>
                        
                        {{-- Edit --}}
                        @if($isAdmin || $canDealEdit)
                        <li class="list-inline-item">
                            <a href="javascript:void(0);"
                            class="edit-deal-btn"
                            data-id="{{ $deal->id }}"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasEditDealPipeline"
                            title="Edit Deal">
                                <i class="tf-icons ti ti-edit ti-sm"></i>
                            </a>
                        </li>
                        @endif

                        {{-- More --}}
                        @if($isAdmin || $canDealEdit || $canDealDelete || $canDealUpdateRecruitStatus)
                        <li class="list-inline-item">
                            <div class="dropdown dropdown-custom">
                                <button type="button"
                                        class="btn dropdown-toggle hide-arrow p-0"
                                        data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical text-muted"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">

                                    @if($isAdmin || $canDealEdit)
                                    <li>
                                        <a class="dropdown-item edit-deal-btn"
                                        data-id="{{ $deal->id }}"
                                        data-bs-toggle="offcanvas"
                                        data-bs-target="#offcanvasEditDealPipeline">
                                            <i class="ti ti-edit ti-sm me-1"></i> Edit
                                        </a>
                                    </li>
                                    @endif

                                    @if($isAdmin || $canDealUpdateRecruitStatus)
                                    <li>
                                        <a class="dropdown-item openRecruitModal"
                                        data-id="{{ $deal->id }}"
                                        data-status="{{ $deal->recruitStatus->name ?? 'Select Status' }}">
                                            <i class="ti ti-user-check ti-sm me-1"></i> Recruit Status
                                        </a>
                                    </li>
                                    @endif

                                    @if($isAdmin || $canDealDelete)
                                    <li>
                                        <a class="dropdown-item deleteDeal"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteDealPipeline"
                                        data-id="{{ $deal->id }}">
                                            <i class="ti ti-trash ti-sm me-1"></i> Delete
                                        </a>
                                    </li>
                                    @endif

                                </ul>
                            </div>
                        </li>
                        @endif

                    </ul>
                </div>
            </div>

            {{-- Main text --}}
            <div class="mt-2">
                <div class="fw-semibold text-wrap">
                    {{ $businessName === 'Job seeker'
                        ? ($deal->candidate ?? '')
                        : ($deal->company ?? '') }}
                </div>

                <div class="text-muted small mt-1 text-wrap">
                    {{ $businessName === 'Job seeker'
                        ? ($deal->jobTitle->name ?? '')
                        : ($deal->job_title_other ?? '') }}
                </div>

                @if($deal->notes)
                    <div class="small mt-1 text-wrap">
                        {{ $deal->notes }}
                    </div>
                @endif
            </div>

            {{-- Assignees --}}
            <div class="d-flex align-items-center mt-2">
                <ul class="list-unstyled d-flex avatar-group mb-0">
                    @foreach ($assignees as $assignee)
                        @php
                            $avatar = (!empty($assignee->profile) &&
                                file_exists(public_path('admin/assets/img/avatars/'.$assignee->profile)))
                                ? asset('admin/assets/img/avatars/'.$assignee->profile)
                                : asset('admin/assets/img/avatars/blank.jpeg');
                        @endphp
                        <li class="avatar avatar-xs">
                            <img class="rounded-circle" src="{{ $avatar }}">
                        </li>
                    @endforeach
                </ul>

                <div class="col-md-6">
                    <small class="text-muted">
                        {{ $deal->careOf->name }}
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        {{ $deal->created_at->format('d-m-Y') }}
                    </small>
                </div>
            </div>

        </div>
    </div>
</div>

@endforeach