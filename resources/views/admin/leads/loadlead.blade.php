<table id="leadTable" class="datatables-users table border-top">
    <thead>
        <tr>
            <th>
                <div class="form-check form-check-inline">
                    <input class="form-check-input checkboxSelectAll" type="checkbox" id="checkboxSelectAll" />
                    <label class="form-check-label" for="checkboxSelectAll"></label>
                </div>
            </th>
            <th>Name</th>
            @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1) || (isset($permission) && $permission->leads_company_column == 1))
                <th>Company</th>
            @endif

            <th>Mobile No</th>
            <th>Whatsapp No</th>
            <th>Job Title</th>
            <th>Experience</th>
            <th>Driving License</th>
            <th>Expected</th>
            <th>Expected</th>
            <th>Message</th>
            <th>Date</th>
            <th>Assign To</th>
            <th>Is Qualified</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody class="listitem">
        @if ($posts->count() > 0)
            @foreach ($posts as $post)

                @php
                    if (strlen($post->message) >= 13 ) {
                        $msg_body = substr($post->message,0,13).'..';
                    } else {
                        $msg_body = $post->message;
                    }

                    $fullJobTitle = $post->required_service === 'Other' ? ($post->other_job_title ?? '---') : preg_replace('/\s*\([^)]*\)/', '', $post->required_service);
                    $job_title = Str::limit($fullJobTitle, 13, '..');

                @endphp

                <tr>
                    <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input dt-checkboxes sub-chk" data-id="{{ $post->id }}" type="checkbox" value="{{ $post->id }}" id="checkbox{{ $post->id }}" />
                            <label class="form-check-label" for="checkbox{{ $post->id }}"></label>
                        </div>
                    </td>
                    <td>
                        <a href="javascript:void(0);" 
                        class="text-body view-lead-btn" 
                        data-id="{{ $post->id }}">
                        {{ $post->cand_name ?? '---' }}
                        </a>
                    </td>


                    <!-- <td><a href="{{ route('admin.leads.show',$post->id) }}" class="text-body">{{ $post->cand_name }}</a></td> -->
                    @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1) || (isset($permission) && $permission->leads_company_column == 1))

                        <td>@if($post->company_name != '') {{ $post->company_name }} @else {{ '---' }} @endif</td>
                    @endif
                    <!-- <td>{{ $post->mob_no }}</td>
                    <td>@if($post->whatsapp_no != '') {{ $post->whatsapp_no }} @else {{ '---' }} @endif</td> -->
                    <td>
                        <a href="javascript:void(0);" 
                        class="editContact"
                        data-id="{{ $post->id }}"
                        data-mob="{{ $post->mob_no }}"
                        data-whatsapp="{{ $post->whatsapp_no }}">
                        {{ $post->mob_no }}
                        </a>
                    </td>

                    <td>
                        <a href="javascript:void(0);" 
                        class="editContact"
                        data-id="{{ $post->id }}"
                        data-mob="{{ $post->mob_no }}"
                        data-whatsapp="{{ $post->whatsapp_no }}">
                        {{ $post->whatsapp_no != '' ? $post->whatsapp_no : '---' }}
                        </a>
                    </td>
                    <td>
                        <span title="{{ $fullJobTitle }}">
                            {{ $job_title }}
                        </span>
                        <!-- @if($post->required_service == 'Other')
                            {{ $post->other_job_title ?? '---' }}
                        @else
                            {{ preg_replace('/\s*\([^)]*\)/', '', $post->required_service) }}

                        @endif -->
                    </td>
                    <td>
                        @if ($post->experience != '')
                            @foreach (explode(",",$post->experience) as $experience)
                                <span class="badge rounded-pill bg-label-primary">{{ $experience }}</span>
                            @endforeach
                        @else
                            <span class="text-body">---</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $licenses = [];
                            if($post->saudi_license == 'yes') $licenses[] = 'Saudi License';
                            if($post->india_license == 'yes') $licenses[] = 'Indian License';
                        @endphp

                        @if(count($licenses) > 0)
                            @foreach($licenses as $license)
                                <span class="badge rounded-pill bg-label-primary">{{ $license }}</span>
                            @endforeach
                        @else
                            ---
                        @endif

                    </td>
                    <td>{{ $post->country ?? '---' }}</td>
                    <td>{{ $post->expected_days ?? '---' }}</td>
                    <td title="{{ $post->message }}">{{ $msg_body ?? '---' }}</td>
                    <td>{{ date('d-m-Y h:i',strtotime($post->lead_date)) }}</td>
                    <!-- <td>
                        @php $submitData = json_decode($post->submit_lead_from, true); @endphp
                        @if($submitData)
                            <strong>{{ $submitData['from'] ?? '---' }}</strong><br>
                            <small>
                                {{ $submitData['city'] ?? '' }}
                                {{ !empty($submitData['region']) ? ', ' . $submitData['region'] : '' }}
                                {{ !empty($submitData['country']) ? ', ' . $submitData['country'] : '' }}
                            </small><br>
                            <small>IP: {{ $submitData['ip'] ?? '---' }}</small>
                        @else
                            ---
                        @endif

                    </td> -->
                    <td>
                        @if (!empty($post->leadassign_id))
                            {{ $post->leadassign->name }}
                        @elseif (
                            Auth::guard('admin')->user()->user_type == 1 ||
                            (isset($permission) && ($permission->full_access == 1 || $permission->leads_assignto == 1))
                        )
                            <a 
                            href="javascript:void(0);"
                            data-bs-toggle="modal"
                            data-bs-target="#assignlead"
                            data-id="{{ $post->id }}">
                                <i class="ti ti-refresh me-2"></i>
                            </a>
                        @else
                            ---
                        @endif
                    </td>


                    <td id="qualified_badge_{{ $post->id }}">
                        <a href="javascript:void(0);"
                        data-bs-toggle="modal"
                        data-bs-target="#qualifiedlead"
                        data-id="{{ $post->id }}">

                            @if($post->is_qualified === null)
                                <span class="badge bg-label-warning">Not Yet</span>

                            @elseif($post->is_qualified === 1)
                                <span class="badge bg-label-info">Followed Up</span>

                            @elseif($post->is_qualified === 2)
                                <span class="badge bg-label-secondary">Call Not Connected</span>

                            @elseif($post->is_qualified === 3)
                                <span class="badge bg-label-success">Lead Qualified</span>

                            @elseif($post->is_qualified === 4)
                                <span class="badge bg-label-danger">Lead Not Qualified</span>
                            @endif

                        </a>
                    </td>
                    
                    <td><span class="badge bg-label-success">@if($post->lead_status_text != '') {{ $post->lead_status_text }} @else New @endif</span></td>
                    <td>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="leadActions{{ $post->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="leadActions{{ $post->id }}">
                                
                                @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1))
                                    <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deletelead" data-id="{{ $post->id }}">
                                        <i class="ti ti-trash me-2"></i> Delete Lead
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#assignlead" data-id="{{ $post->id }}">
                                        <i class="ti ti-refresh me-2"></i> Assign Lead
                                    </a>

                                    <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#qualifiedlead" data-id="{{ $post->id }}">
                                        <i class="ti ti-check me-2"></i> Qualified Lead
                                    </a>

                                @elseif (Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0))
                                    @if (isset($permission) && $permission->leads_delete == 1)
                                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deletelead" data-id="{{ $post->id }}">
                                            <i class="ti ti-trash me-2"></i> Delete Lead
                                        </a>
                                    @endif

                                    @if (isset($permission) && $permission->leads_assignto == 1)
                                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#assignlead" data-id="{{ $post->id }}">
                                            <i class="ti ti-refresh me-2"></i> Assign Lead
                                        </a>
                                    @endif

                                    <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#qualifiedlead" data-id="{{ $post->id }}">
                                        <i class="ti ti-check me-2"></i> Qualified Lead
                                    </a>
                                @endif

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
        {{ 'Showing '.$posts->firstItem().' to '.$posts->lastItem().' of '.$posts->total().' Entries' }}
    </div>
    <div class="float-end">
        {{ $posts->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
