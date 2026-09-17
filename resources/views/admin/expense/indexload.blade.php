<table class="datatables-users table border-top">
    <thead>
        <tr>
            <th>
                <div class="form-check form-check-inline">
                    <input class="form-check-input checkboxSelectAll" type="checkbox" id="checkboxSelectAll" />
                    <label class="form-check-label" for="checkboxSelectAll"></label>
                </div>
            </th>
            <th>Expense Name</th>
            <th>Expense For</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Payment Mode</th>
            <th>Date</th>
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
                    <td>{{ $post->name }}</td>
                    <td>@if($post->expensefor_id != '') {{ $post->expensefor->name }} @else {{ "---" }} @endif</td>
                    <td>{{ $post->expensecat->name }}</td>
                    <td>{{ $post->amount }}</td>
                    <td>@if($post->payment_mode != '') {{ $post->payment_mode }} @else {{ '---' }} @endif</td>
                    <td>{{ date('d-m-Y',strtotime($post->expense_date)) }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>
                            <div class="dropdown-menu dropdown-menu-end m-0">
                                @if ((Auth::guard('admin')->user()->user_type == 1) || (isset($adminpermission) && $adminpermission->view_expense == 1))
                                    <a href="javascript:void(0);" 
                                    data-id="{{ $post->id }}" 
                                    class="dropdown-item viewExpenseBtn">
                                        View
                                    </a>
                                @endif

                                @if ((Auth::guard('admin')->user()->user_type == 1) || (isset($adminpermission) && $adminpermission->edit_expense == 1))
                                <a href="javascript:;" data-bs-toggle="offcanvas" data-bs-target="#updateReg" data-id="{{ $post->id }}" class="dropdown-item edcandidate">Edit</a>
                                @endif

                                @if ((Auth::guard('admin')->user()->user_type == 1) || (isset($adminpermission) && $adminpermission->delete_expense == 1))
                                    <a href="javascript:;" class="dropdown-item delcandidate" data-bs-toggle="modal" data-bs-target="#deleteStaff" data-id="{{ $post->id }}">Delete</a>
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

@if ($posts->count() > 0)
    <div class="px-4 pt-3">
        <div class="float-start">
            {{ 'Showing '.$posts->firstItem().' to '.$posts->lastItem().' of '.$posts->total().' Entries' }}
        </div>
        <div class="float-end">
            {{ $posts->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>
@else
    <div class="px-4 pt-3">
        <div class="float-start">
            {{ 'Showing 0 to 0 of 0 Entries' }}
        </div>
        {{-- <div class="float-end">
            {{ $posts->links('vendor.pagination.bootstrap-4') }}
        </div> --}}
    </div>
@endif

