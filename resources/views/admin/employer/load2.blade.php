<table class="datatables-users table border-top">
    <thead>
        <tr>
            {{-- <th></th> --}}
            <th>Employer</th>
            <th>Partner</th>
            <th>Business</th>
            <th>Visa No</th>
            <th>ID No</th>
            <th>Profession</th>
            {{-- <th>Issuing Authority</th>
            <th>Visa Date</th>
            <th>Cty Of Work</th>
            <th>Salary</th> --}}
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody class="listitem">
        @if ($employerLists->count() > 0)
            @foreach ($employerLists as $employerList)
                @php
                    // Profession
                    $professions = DB::table('professions')->wherein('id',explode(",",$employerList->proff_id))->get();

                    $proff = [];

                    foreach ($professions as $profession) {
                        $proff[] = $profession->eng_name;
                    }

                    if ($employerList->employer_ar_name != '') {
                        $name = $employerList->employer_ar_name;
                    } else {
                        $name = $employerList->employer_name;
                    }


                    // Name
                    $stateNum = rand(0,6);
                    $statesName = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                    $stateName = $statesName[$stateNum];
                    $fullname = explode(" ",$name);
                    $firstword = current($fullname);
                    $lastword = end($fullname);
                    $firstCharacter = substr($firstword, 0, 1);
                    $lastCharacter = substr($lastword, 0, 1);
                    $defaultProfile = strtoupper($firstCharacter.$lastCharacter);


                    // Status
                    $employerStatus = $employerList->status;
                    $states = ['danger','success','warning', 'info', 'primary', 'secondary'];
                    $statusMsg = ['Inactive','Active'];
                    $textStatus = $statusMsg[$employerStatus];
                    $state = $states[$employerStatus];
                    $bstarget = ['#activemp','#inactivemp'];
                    $targetData = $bstarget[$employerStatus];
                @endphp
                <tr>
                    {{-- <td></td> --}}
                    <td>
                        <div class="d-flex justify-content-start align-items-center user-name">
                            <div class="avatar-wrapper">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-{{ $stateName }}">{{ $defaultProfile }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                @if ($name != '')
                                    <a href="{{ route('admin.employer.visaDetshow',$employerList->id) }}" class="text-body text-truncate" target="_blank"><span class="fw-semibold">{{ $name }}</a>
                                @else
                                    <a href="{{ route('admin.employer.visaDetshow',$employerList->id) }}" class="text-body text-truncate" target="_blank"><span class="fw-semibold">{{ '---' }}</a>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>@if($employerList->partneroffice_id != '') {{ $employerList->partneroffice->rec_off_name }} @else {{ '---' }} @endif</td>
                    <td>{{ $employerList->businesstype }}</td>
                    <td>{{ $employerList->visa_no }}</td>
                    <td>{{ $employerList->id_no }}</td>
                    <td>{{ implode(",",$proff) }}</td>
                    {{-- <td>{{ $employerList->issuing_authority }}</td>
                    <td>{{ $employerList->visa_date }}</td>
                    <td>@if($employerList->wpcity_id != '') {{ $employerList->wpcity->name }} @else {{ '---' }} @endif</td>
                    <td>{{ $employerList->salary }}</td> --}}
                    <td><span class="badge bg-label-{{ $state }}" data-bs-toggle="modal" data-bs-target="{{ $targetData }}" data-id="{{ $employerList->id }}">{{ $textStatus }}</span></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>

                            <div class="dropdown-menu dropdown-menu-end m-0">
                                {{-- Edit Employer --}}
                                <a class="dropdown-item" href="javascript:;" 
                                data-bs-toggle="offcanvas" 
                                data-bs-target="#editEmployerVisa" 
                                data-id="{{ $employerList->id }}">
                                <i class="ti ti-edit me-2"></i> Edit
                                </a>

                                {{-- Delete Employer --}}
                                <a class="dropdown-item delete-record" href="javascript:;" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteemployer" 
                                data-id="{{ $employerList->id }}">
                                <i class="ti ti-trash me-2"></i> Delete
                                </a>

                                {{-- View Employer --}}
                                <a class="dropdown-item" href="{{ route('admin.employer.visaDetshow', $employerList->id) }}" target="_blank">
                                <i class="ti ti-eye me-2"></i> View
                                </a>

                                {{-- Suspend Employer --}}
                                <a class="dropdown-item" href="javascript:;">
                                <i class="ti ti-alert-triangle me-2"></i> Suspend
                                </a>

                                {{-- Publish Employer --}}
                                <a class="dropdown-item" href="javascript:;">
                                <i class="ti ti-send me-2"></i> Publish
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="12" class="text-center">No Data Found</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="px-4 pt-3">
    <div class="float-start">
        {{ 'Showing '.$employerLists->firstItem().' to '.$employerLists->lastItem().' of '.$employerLists->total().' Entries' }}
    </div>
    <div class="float-end">
        {{ $employerLists->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
