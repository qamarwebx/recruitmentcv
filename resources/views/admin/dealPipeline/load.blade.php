@php
    $canDealAdd = isset($perms) && $perms->deal_add != 0;
    $canDealView = isset($perms) && $perms->deal_view != 0;
    $canDealEdit = isset($perms) && $perms->deal_edit != 0;
    $canDealDelete = isset($perms) && $perms->deal_delete != 0;
    $canDealUpdateStage = isset($perms) && $perms->deal_update_stage != 0;
    $canDealUpdateRecruitStatus = isset($perms) && $perms->deal_update_recruit_status != 0;
@endphp

<style>
        body {
            overflow-y: auto;
        }

        .dropdown-custom.show .dropdown-menu {
            transform: none !important;
            z-index: 100px;
        }

    </style>
<table class="datatables-users table border-top">
    <thead>
        <tr>
            <th>
                <div class="form-check form-check-inline">
                    <input class="form-check-input checkboxSelectAll" type="checkbox" id="checkboxSelectAll" />
                    <label class="form-check-label" for="checkboxSelectAll"></label>
                </div>
            </th>
            <th>Business</th>
            <th>Deal Stage</th>
            <th>Recruit Status</th>
            <th>Deal Name</th>
            <th>Name</th>
            <th>Company</th>
            <th>Candidate</th>
            <th>care off</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody class="listitem">
        @if ($dealsLists->count() > 0)
            
        
            @foreach ($dealsLists as $dealsList)

                @if ($dealsList->stage->name == 'Prospecting')
                    @php $badgelabel = 'info'; @endphp
                @elseif ($dealsList->stage->name == 'Qualification')
                    @php $badgelabel = 'primary'; @endphp
                @elseif ($dealsList->stage->name == 'Discussion')
                    @php $badgelabel = 'secondary'; @endphp
                @elseif ($dealsList->stage->name == 'Proposal')
                    @php $badgelabel = 'warning'; @endphp
                @elseif ($dealsList->stage->name == 'Review')
                    @php $badgelabel = 'dark'; @endphp
                @elseif ($dealsList->stage->name == 'Closed Won')
                    @php $badgelabel = 'success'; @endphp
                @elseif ($dealsList->stage->name == 'Closed Lost')
                    @php $badgelabel = 'danger'; @endphp
                @else
                    @php $badgelabel = 'primary'; @endphp
                @endif
                    

                <tr>
                    <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input checkboxItem" type="checkbox" value="{{ $dealsList->id }}" id="checkbox_{{ $dealsList->id }}" />
                            <label class="form-check-label" for="checkbox_{{ $dealsList->id }}"></label>
                        </div>
                    </td>

                    <td>
                        <a href="javascript:void(0);" 
                        class="view-deal-btn" 
                        data-id="{{ $dealsList->id }}" 
                        data-bs-toggle="modal" 
                        data-bs-target="#viewDealModal" 
                        title="View Deal">
                        {{ $dealsList->business->name ?? '-' }}
                        </a>
                    </td>




                    <td>
                        @if($isAdmin || $canDealUpdateStage)
                        <div class="dropdown">
                            <span class="badge rounded-pill bg-label-{{ $badgelabel }} dropdown-toggle hide-arrow" data-bs-toggle="dropdown"> {{ $dealsList->stage->name ?? $dealsList->task_status }} <i class='ti ti-chevron-down ti-xs'></i></span>
                            <div class="dropdown-menu dropdown-menu-end m-0">
                                @foreach($dealStages as $stage)
                                    <a href="javascript:void(0);" data-id="{{ $dealsList->id }}" data-status="{{ $stage->name }}" class="dropdown-item updateStatus">{{ $stage->name }}</a>
                                @endforeach
                            </div>
                        </div>
                        @else
                            {{ '-' }}
                        @endif
                    </td>

                 
                   
                    <td>
                        @if($isAdmin || $canDealUpdateRecruitStatus)
                            @php
                                // Map recruitStatus → badge class
                                $recruitBadgeMap = [
                                    'On Medical'   => 'warning',
                                    'Medical Fit'  => 'success',
                                    'Not Ready'    => 'secondary',
                                    'FOL'          => 'danger',
                                ];
                                $recruitName = $dealsList->recruitStatus->name ?? 'Select Status';
                                $badgeClass = $recruitBadgeMap[$recruitName] ?? 'primary';
                            @endphp
                            <div class="dropdown dropdown-custom">
                                <span class="badge rounded-pill bg-label-{{ $badgeClass }} dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown"
                                    data-bs-display="static"
                                    data-bs-auto-close="outside"
                                    data-bs-offset="0,8">
                                    {{ $recruitName }}
                                    <i class='ti ti-chevron-down ti-xs'></i>
                                </span>
                                
                                <div class="dropdown-menu dropdown-menu-end m-0">
                                    <a href="javascript:void(0);" data-id="{{ $dealsList->id }}" data-status="On Medical" class="dropdown-item updateRecruitStatus">On Medical</a>
                                    <a href="javascript:void(0);" data-id="{{ $dealsList->id }}" data-status="Medical Fit" class="dropdown-item updateRecruitStatus">Medical Fit</a>
                                    <a href="javascript:void(0);" data-id="{{ $dealsList->id }}" data-status="Not Ready" class="dropdown-item updateRecruitStatus">Not Ready</a>
                                    <a href="javascript:void(0);" data-id="{{ $dealsList->id }}" data-status="FOL" class="dropdown-item updateRecruitStatus">FOL</a>
                                </div>
                            </div>

                        @else
                            {{ '-' }}
                        @endif
                    </td>

                    <td>{{ $dealsList->deal_name ?? '-' }}</td>
                    <td>{{ $dealsList->name ?? '-' }}</td>
                    <td>{{ $dealsList->company ?? '-' }}</td>
                    <td>{{ $dealsList->candidate ?? '-' }}</td>
                    <td>{{ $dealsList->careOf->name ?? '-' }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="dealActions{{ $dealsList->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dealActions{{ $dealsList->id }}">

                                {{-- Edit Deal --}}
                                @if($isAdmin || $canDealEdit)
                                    <a class="dropdown-item edit-deal-btn" href="javascript:void(0);" 
                                    data-bs-toggle="offcanvas" 
                                    data-bs-target="#offcanvasEditDealPipeline" 
                                    data-id="{{ $dealsList->id }}">
                                    <i class="ti ti-edit me-2"></i> Edit
                                    </a>
                                @endif

                                {{-- Delete Deal --}}
                                @if($isAdmin || $canDealDelete)
                                    <a class="dropdown-item" href="javascript:void(0);" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteDealPipeline" 
                                    data-id="{{ $dealsList->id }}">
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
        {{ 'Showing '.$dealsLists->firstItem().' to '.$dealsLists->lastItem().' of '.$dealsLists->total().' Entries' }}
    </div>
    <div class="float-end">
        {{ $dealsLists->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
