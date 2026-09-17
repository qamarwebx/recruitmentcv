<table class="datatables-users table border-top">
    <thead>
        <tr>
            <th>
                <div class="form-check form-check-inline">
                    <input class="form-check-input checkboxSelectAll" type="checkbox" id="checkboxSelectAll" />
                    <label class="form-check-label" for="checkboxSelectAll"></label>
                </div>
            </th>
            <th>Reference</th>
            <th>Candidate</th>
            <th>Passport</th>
            <th>Occupation</th>
            <th>Status</th>
            <th>Work City</th>
            <th>Careoff</th>
            <th>Created On</th>
            <th>Sourcing At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="listitem">
        @if ($candidateLists->count() > 0)
            @foreach ($candidateLists as $candidateList)
                @php

                    // Name
                    $candname = $candidateList->cand_name;
                    $stateNum = rand(0,6);
                    $statesName = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                    $stateName = $statesName[$stateNum];
                    $fullname = explode(" ",$candname);
                    $firstword = current($fullname);
                    $lastword = end($fullname);
                    $firstCharacter = substr($firstword, 0, 1);
                    $lastCharacter = substr($lastword, 0, 1);
                    $defaultProfile = strtoupper($firstCharacter.$lastCharacter);

                    // Candidate Status

                    $statusMessage = $candidateList->candidate_current_status;

                    if ($statusMessage == "Hold" || $statusMessage == "Cancelled") {
                        $badgeClass = "bg-label-danger";
                    }else {
                        $badgeClass = "bg-label-success";
                    }

                    if (Auth::guard('admin')->user()->user_type == 1 || (isset($perm) && $perm->candidate_status == 1)) {
                       $dataTarget = '#NewCandStatusUpdate';
                    } else {
                       $dataTarget = '';
                    }


                @endphp
                <tr>

                    <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input dt-checkboxes sub-chk" data-id="{{ $candidateList->id }}" type="checkbox" value="{{ $candidateList->id }}" id="checkbox{{ $candidateList->id }}" />
                            <label class="form-check-label" for="checkbox{{ $candidateList->id }}"></label>
                        </div>
                    </td>
                    <td>{{ $candidateList->reference_no }}</td>
                    <td>
                        <div class="d-flex justify-content-start align-items-center user-name">
                            <div class="avatar-wrapper">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-{{ $stateName }}">{{ $defaultProfile }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                @if ($candname != '')
                                    <a href="{{ route('admin.candidate.show',$candidateList->id) }}" class="text-body text-truncate" target="_blank"><span class="fw-semibold">{{ $candname }}</a>
                                @else
                                    <a href="{{ route('admin.candidate.show',$candidateList->id) }}" class="text-body text-truncate" target="_blank"><span class="fw-semibold">{{ '---' }}</a>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $candidateList->pass_no }}</td>
                    <td>@if($candidateList->jobtype_id != '') {{ $candidateList->jobtype->eng_name }} @else {{ '---' }} @endif</td>
                    <td><a  href="#" class="badge {{ $badgeClass }}" data-bs-toggle="modal" data-bs-target="{{ $dataTarget }}" data-id="{{ $candidateList->id }}">{{ $statusMessage }}</a></td>
                    <td>
                        @if (($candidateList->work_city_editable ?? false) && (Auth::guard('admin')->user()->user_type == 1 || (isset($perm) && $perm->edit_candidate == 1)))
                            <a href="javascript:;" class="updateworkcity" data-bs-toggle="modal" data-bs-target="#updateWorkCity" data-id="{{ $candidateList->id }}" data-wpcity-id="{{ $candidateList->work_city_id }}" title="Update Work City">{{ $candidateList->work_city }}</a>
                        @else
                            {{ $candidateList->work_city ?: '--' }}
                        @endif
                    </td>
                    <td>@if($candidateList->careoff_id != '') {{ $candidateList->careoff->name }} @else {{ '---' }} @endif</td>
                    <td>@if($candidateList->created_at != '') {{ date('d-m-Y',strtotime($candidateList->created_at)) }} @else {{ '---' }} @endif</td>
                    <td>@if($candidateList->sourcing_date != '') {{ date('d-m-Y',strtotime($candidateList->sourcing_date)) }} @else {{ '---' }} @endif</td>


                    <td>
                        <div class="d-flex align-items-center">
                            <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>
                            <div class="dropdown-menu dropdown-menu-end m-0">

                                {{-- Edit Candidate --}}
                                <a class="dropdown-item edcandidate" href="javascript:;" 
                                data-bs-toggle="offcanvas" 
                                data-bs-target="#edituser" 
                                data-id="{{ $candidateList->id }}">
                                <i class="ti ti-edit me-2"></i> Edit
                                </a>

                                {{-- Delete Candidate --}}
                                <a class="dropdown-item delcandidate" href="javascript:;" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteStaff" 
                                data-id="{{ $candidateList->id }}">
                                <i class="ti ti-trash me-2"></i> Delete
                                </a>

                                {{-- View Candidate --}}
                                <a class="dropdown-item" href="{{ route('admin.candidate.show', $candidateList->id) }}" target="_blank">
                                <i class="ti ti-eye me-2"></i> View
                                </a>

                                {{-- Suspend Candidate --}}
                                <a class="dropdown-item" href="javascript:;">
                                <i class="ti ti-alert-triangle me-2"></i> Suspend
                                </a>

                                {{-- Publish Candidate --}}
                                <a class="dropdown-item pubcandidate" href="{{ route('admin.candidate.publish', $candidateList->id) }}">
                                <i class="ti ti-send me-2"></i> Publish
                                </a>

                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="9" class="text-center">No Data Found</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="px-4 pt-3">
    <div class="float-start">
        {{ 'Showing '.$candidateLists->firstItem().' to '.$candidateLists->lastItem().' of '.$candidateLists->total().' Entries' }}
    </div>
    <div class="float-end">
        {{ $candidateLists->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
