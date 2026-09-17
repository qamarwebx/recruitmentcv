@php
use Illuminate\Support\Facades\DB;

if (!function_exists('getTodoBadgeColor')) {
    function getTodoBadgeColor($priority) {
        return match($priority) {
            'High' => 'danger',
            'Medium' => 'warning',
            'Low' => 'info',
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
@endphp


@foreach ($todos as $todo)

@php
    $badgeColor = getTodoBadgeColor($todo->priority ?? 'Normal');
    $assignees  = getAssignees($todo->assignto_id ?? '');
@endphp


<div class="kanban-card" data-id="{{ $todo->id }}">

    <div class="card mb-3 shadow-sm">

        <div class="card-body p-2">

            {{-- Top Row --}}
            <div class="d-flex align-items-center mb-2">

                {{-- Reminder Cycle --}}
                <div>
                    <span class="badge bg-label-{{ $badgeColor }}">
                        {{ $todo->reminder_cycle }}
                    </span>
                </div>


                {{-- Actions --}}
                <div class="ms-auto">

                    <ul class="list-inline mb-0 d-flex align-items-center">

                        {{-- View --}}
                        <li class="list-inline-item">
                            <a href="javascript:void(0);"
                               class="view-todo-btn"
                               data-id="{{ $todo->id }}"
                               data-bs-toggle="modal"
                               data-bs-target="#viewTodoModal">

                                <i class="tf-icons ti ti-eye ti-sm"></i>
                            </a>
                        </li>


                        {{-- Edit --}}
                        <li class="list-inline-item">
                            <a href="javascript:void(0);"
                               class="offcanvasEditTodo edit_todo_{{$todo->id}}"
                               data-id="{{ $todo->id }}"
                               data-bs-toggle="offcanvas"
                               data-bs-target="#offcanvasEditUser">

                                <i class="tf-icons ti ti-edit ti-sm"></i>
                            </a>
                        </li>


                        {{-- Dropdown --}}
                        <li class="list-inline-item">

                            <div class="dropdown dropdown-custom">

                                <button type="button"
                                        class="btn dropdown-toggle hide-arrow p-0"
                                        data-bs-toggle="dropdown">

                                    <i class="ti ti-dots-vertical text-muted"></i>

                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li>
                                        <a class="dropdown-item"
                                           data-bs-toggle="offcanvas"
                                           data-bs-target="#offcanvasEditUser"
                                           data-id="{{ $todo->id }}">

                                            Edit
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item"
                                           data-bs-toggle="modal"
                                           data-bs-target="#deleteStaff"
                                           data-id="{{ $todo->id }}">

                                            Delete
                                        </a>
                                    </li>

                                </ul>

                            </div>

                        </li>

                    </ul>

                </div>

            </div>


            {{-- Title --}}
            <div class="fw-semibold text-wrap mb-1">
                {{ $todo->task_title }}
            </div>


            {{-- Description --}}
            @php
                $todoDescText = trim((string) ($todo->task_description ?? ''));
                $todoDescLimit = 90;
                $todoDescIsLong = mb_strlen($todoDescText) > $todoDescLimit;
                $todoDescShort = $todoDescIsLong ? \Illuminate\Support\Str::limit($todoDescText, $todoDescLimit, '...') : $todoDescText;
            @endphp
            @if($todoDescText !== '')
                <div class="text-muted small text-wrap mb-2 todo-desc-wrap">
                    <span class="todo-desc-text">{{ $todoDescShort }}</span>
                    @if($todoDescIsLong)
                        <a href="javascript:void(0);" class="todo-desc-toggle fw-semibold"
                           data-full="{{ $todoDescText }}" data-short="{{ $todoDescShort }}">View More</a>
                    @endif
                </div>
            @endif


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


                <small class="text-muted ms-auto">
                    {{ $todo->created_at->format('d-m-Y') }}
                </small>

            </div>


            {{-- Labels --}}
            <div class="mt-2">

                @if ($todo->todolabel_id)
                <span class="badge bg-label-primary">
                    {{ $todo->todolabel->name }}
                </span>
                @endif


                @if ($todo->department_id)
                <span class="badge bg-label-secondary">
                    {{ $todo->department->name }}
                </span>
                @endif

            </div>

        </div>

    </div>

</div>

@endforeach