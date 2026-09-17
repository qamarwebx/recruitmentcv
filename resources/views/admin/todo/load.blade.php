
    <table class="datatables-users table border-top">
        <thead>
            <tr>
                <th>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input checkboxSelectAll" type="checkbox" id="checkboxSelectAll" />
                        <label class="form-check-label" for="checkboxSelectAll"></label>
                    </div>
                </th>
                <th>Subject</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Department</th>
                <th>Label</th>
                <th>Start</th>
                <th>Finish</th>
                <th>Assign To</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="listitem">
            @if ($todoLists->count() > 0)
                @foreach ($todoLists as $todoList)
                    @php
                        $assignees = DB::table('admins')->wherein('id',explode(",",$todoList->assignto_id))->get();
                        // $assignname = [];
                        // foreach ($assignees as $assignee) {
                        //     $assignname[] = $assignee->name;
                        // }

                        if ($todoList->reminder_cycle == 'Urgent') {
                            $status = 'danger';
                        }elseif ($todoList->reminder_cycle == 'High') {
                            $status = 'warning';
                        }elseif ($todoList->reminder_cycle == 'Medium') {
                            $status = 'info';
                        }else{
                            $status = 'primary';
                        }

                        if ($todoList->task_status == 'New Task') {
                            $badgelabel = 'info';
                        }elseif ($todoList->task_status == 'In Process') {
                            $badgelabel = 'primary';
                        }elseif ($todoList->task_status == 'Complete') {
                            $badgelabel = 'warning';
                        }elseif ($todoList->task_status == 'Achieved') {
                            $badgelabel = 'warning';
                        }elseif ($todoList->task_status == 'Not Required') {
                            $badgelabel = 'info';
                        }else{
                            $badgelabel = 'success';
                        }

                    @endphp
                    <tr id="todo-row-{{ $todoList->id}}">
                        <td>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input dt-checkboxes sub-chk" data-id="{{ $todoList->id }}" type="checkbox" value="{{ $todoList->id }}" id="checkbox{{ $todoList->id }}" />
                                <label class="form-check-label" for="checkbox{{ $todoList->id }}"></label>
                            </div>
                        </td>
                        <td>
                            <a href="javascript:void(0);" 
                            class="view-todo-btn" 
                            data-id="{{ $todoList->id }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#viewTodoModal">{{ $todoList->task_title }}</a>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-label-{{ $badgelabel }} dropdown-toggle hide-arrow" data-bs-toggle="dropdown"> {{ $todoList->task_status }} <i class='ti ti-chevron-down ti-xs'></i></span>
                            <div class="dropdown-menu dropdown-menu-end m-0">
                                <a href="javascript:void(0);" data-id="{{ $todoList->id }}" class="dropdown-item updateStatus">Mark as New Task</a>
                                <a href="javascript:void(0);" data-id="{{ $todoList->id }}" class="dropdown-item updateStatus">Mark as In Process</a>
                                {{-- <a href="javascript:void(0);" data-id="{{ $todoList->id }}" class="dropdown-item updateStatus">Mark as Incomplete</a> --}}
                                <a href="javascript:void(0);" data-id="{{ $todoList->id }}" class="dropdown-item updateStatus">Mark as Complete</a>
                                <a href="javascript:void(0);" data-id="{{ $todoList->id }}" class="dropdown-item updateStatus">Mark as Always</a>
                                {{-- <a href="javascript:void(0);" data-id="{{ $todoList->id }}" class="dropdown-item updateStatus">Mark as On Hold</a> --}}
                                {{-- <a href="javascript:void(0);" data-id="{{ $todoList->id }}" class="dropdown-item updateStatus">Mark as Cancelled</a> --}}
                                @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1) || (isset($permission) && $permission->update_status_achieved == 1))
                                    <a href="javascript:void(0);" data-id="{{ $todoList->id }}" class="dropdown-item updateStatus">Mark as Achieved</a>
                                @endif
                                <a href="javascript:void(0);" data-id="{{ $todoList->id }}" class="dropdown-item updateStatus">Mark as Not Required</a>

                            </div>
                        </td>
                        <td><span class="badge bg-label-{{ $status }}">{{ $todoList->reminder_cycle }}</span></td>
                        <td>@if($todoList->department_id != '') {{ $todoList->department->name }} @else {{ '---' }} @endif</td>
                        <td>@if($todoList->todolabel_id != '') {{ $todoList->todolabel->name }} @else {{ '---' }} @endif</td>
                        <td>@if($todoList->start_on != '') {{ date('d-m-Y',strtotime($todoList->start_on)) }} @else {{ '---' }} @endif</td>
                        <td>@if($todoList->finish_on != '') {{ date('d-m-Y',strtotime($todoList->finish_on)) }} @else {{ '---' }} @endif</td>
                        <td>
                            @foreach ($assignees as $assignee)
                                <span class="badge rounded-pill bg-label-primary">{{ $assignee->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn p-0" type="button" id="todoActions{{ $todoList->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="todoActions{{ $todoList->id }}">

                                    {{-- Edit Todo --}}
                                    @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1) || (isset($permission) && $permission->todo_edit == 1))
                                        <a class="dropdown-item editcontact edcandidate edit_todo_{{$todoList->id}}" href="javascript:void(0);" 
                                        data-bs-toggle="offcanvas" 
                                        data-bs-target="#offcanvasEditUser" 
                                        data-id="{{ $todoList->id }}">
                                        <i class="ti ti-edit me-2"></i> Edit
                                        </a>
                                    @endif

                                    {{-- Delete Todo --}}
                                    @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1) || (isset($permission) && $permission->todo_delete == 1))
                                        <a class="dropdown-item" href="javascript:void(0);" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteStaff" 
                                        data-id="{{ $todoList->id }}">
                                        <i class="ti ti-trash me-2"></i> Delete
                                        </a>
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
            {{ 'Showing '.$todoLists->firstItem().' to '.$todoLists->lastItem().' of '.$todoLists->total().' Entries' }}
        </div>
        <div class="float-end">
            {{ $todoLists->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>
