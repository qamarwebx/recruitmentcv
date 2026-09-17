<table id="contactsTable" class="datatables-users table border-top">
    <thead>
        <tr>
            <th>
                <div class="form-check form-check-inline">
                    <input class="form-check-input checkboxSelectAll" type="checkbox" id="checkboxSelectAll" />
                    <label class="form-check-label" for="checkboxSelectAll"></label>
                </div>
            </th>
            <th>Full Name</th>
            <th>Office Name</th>
            <th>Lead Stage</th>
            <th>City</th>
            <th>Country</th>
            <th>Group</th>
            <th>Work</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>


    <tbody class="listitem">
        @if (count($posts) > 0)
            @foreach ($posts as $post)
                @php
                    $stateNum = rand(0,6);
                    $states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                    $state = $states[$stateNum];
                    $fullname = explode(" ",$post->prim_concern_name);
                    $firstword = current($fullname);
                    $lastword = end($fullname);
                    $firstCharacter = substr($firstword, 0, 1);
                    $lastCharacter = substr($lastword, 0, 1);
                    $defaultProfile = strtoupper($firstCharacter.$lastCharacter);

                    if ($post->ls_id != '') {
                        $lead_stage = $post->ls->name;
                    }else {
                        $lead_stage = "None";
                    }



                @endphp

                <tr>
                    <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input dt-checkboxes sub-chk" data-id="{{ $post->id }}" type="checkbox" value="{{ $post->id }}" id="checkbox{{ $post->id }}" />
                            <label class="form-check-label" for="checkbox{{ $post->id }}"></label>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-start align-items-center user-name">
                            <div class="avatar-wrapper">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-{{ $state }}">{{ $defaultProfile }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                @if ($post->prim_concern_name != '')
                                    <a href="{{ route('admin.contact.show',$post->id) }}" class="text-body text-truncate" target="_blank"><span class="fw-semibold">{{ $post->prim_concern_name }}</a>
                                @else
                                    <a href="{{ route('admin.contact.show',$post->id) }}" class="text-body text-truncate" target="_blank"><span class="fw-semibold">{{ '---' }}</a>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>@if($post->office_eng_name != '') {{ $post->office_eng_name }} @else {{ '---' }} @endif</td>
                    <td class="tdListFetch{{ $post->id }}"><span class='text-truncate d-flex align-items-center' data-bs-toggle='modal' data-bs-target='#updateLstage' data-id='{{ $post->id }}'>{{ $lead_stage }} <i class='ti ti-chevron-down ti-sm me-2'></i></span></td>
                    <td>@if($post->city_id != '') {{ $post->city->name ?? '---' }} @else {{ '---' }} @endif</td>
                    <td>@if($post->country_id != '') {{ $post->country->name ?? '---' }} @else {{ '---' }} @endif</td>
                    <td>@if($post->group_id != '') {{ $post->group->name ?? '---' }} @else {{ '---' }} @endif</td>
                    <td>@if($post->cstatus_id != '') <a class="badge bg-label-success text-capitalized" herf="#" data-toggle="modal" data-target="#modalptstatus" data-id="{{ $post->id }}">{{ $post->contactstatus->name }}</a> @else <a class="badge bg-label-danger text-capitalized" herf="#" data-toggle="modal" data-target="#modalptstatus" data-id="{{ $post->id }}">None</a> @endif</td>
                    <td>@if($post->status == 1) <span class="badge bg-label-success">Active</span> @else <span class="badge bg-label-warning">Inactive</span> @endif</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="javascript:;" data-bs-toggle="offcanvas" data-bs-target="#edituser" data-id="{{ $post->id }}" class="text-body editcontact edcandidate"><i class="ti ti-edit ti-sm me-2"></i></a>
                            <a href="javascript:;" class="text-body delcontact delcandidate" data-bs-toggle="modal" data-bs-target="#deleteStaff" data-id="{{ $post->id }}"><i class="ti ti-trash ti-sm mx-2"></i></a>
                            <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>
                            <div class="dropdown-menu dropdown-menu-end m-0">
                                <a href="{{ route('admin.contact.show',$post->id) }}" class="dropdown-item viewcontact">View</a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="9" class="text-center">No data found</td>
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


{{-- <script>
    $(function () {
        // ID selector on Master Checkbox
        var masterCheck = $('.checkboxSelectAll');
        // ID selector on Items Container
        var listcheckitem = $('.listitem :checkbox');
        // Click Event on Master Check
        masterCheck.on("click", function() {
            var isMasterChecked = $(this).is(":checked");
            if(isMasterChecked){
                $('.bulkactions').prop("disabled",false);
            }else{
                $('.bulkactions').prop("disabled",true);
            }
            listcheckitem.prop("checked", isMasterChecked);
        });
        // Change Event on each item checkbox
        listcheckitem.on("change", function() {
            // Total Checkboxes in list
            var totalItems = listcheckitem.length;
            // Total Checked Checkboxes in list
            var checkedItems = listcheckitem.filter(":checked").length;
            //If all are checked
            if (totalItems == checkedItems) {
                masterCheck.prop("indeterminate", false);
                masterCheck.prop("checked", true);
                $('.bulkactions').prop("disabled",false);
            }
            // Not all but only some are checked
            else if (checkedItems > 0 && checkedItems < totalItems) {
                masterCheck.prop("indeterminate", true);
                $('.bulkactions').prop("disabled",false);
            }
            //If none is checked
            else {
                masterCheck.prop("indeterminate", false);
                masterCheck.prop("checked", false);
                $('.bulkactions').prop("disabled",true);
            }
        });
    });
</script> --}}


{{-- <script>

    // Click Event on Master Check
    $(document).on('click','.checkboxSelectAll',function(){
        var listcheckitem = $('.listitem :checkbox');
        var isMasterChecked = $(this).is(":checked");

        // row checked list
        var checkedList = listcheckitem.length;

        if(isMasterChecked){
            if (checkedList > 0) {
                $('.bulkactions').prop("disabled",false);
            } else {
                $('.bulkactions').prop("disabled",true);
            }

        }else{
            $('.bulkactions').prop("disabled",true);
        }
        listcheckitem.prop("checked", isMasterChecked);
    });

    // Change Event on each item checkbox
    $(document).on('change','.listitem :checkbox',function(){
        var listcheckitem = $('.listitem :checkbox');
        var masterCheck = $('.checkboxSelectAll');
        // Total Checkboxes in list
        var totalItems = listcheckitem.length;
        // Total Checked Checkboxes in list
        var checkedItems = listcheckitem.filter(":checked").length;
        //If all are checked
        if (totalItems == checkedItems) {
            masterCheck.prop("indeterminate", false);
            masterCheck.prop("checked", true);
            $('.bulkactions').prop("disabled",false);
        }
        // Not all but only some are checked
        else if (checkedItems > 0 && checkedItems < totalItems) {
            masterCheck.prop("indeterminate", true);
            $('.bulkactions').prop("disabled",false);
        }
        //If none is checked
        else {
            masterCheck.prop("indeterminate", false);
            masterCheck.prop("checked", false);
            $('.bulkactions').prop("disabled",true);
        }
    });

</script> --}}
