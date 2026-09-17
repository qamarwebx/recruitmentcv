<table class="datatables-users table border-top">
    <thead>
        <tr>
            <th>Campaign Name</th>
            <th>Template Name</th>
            <th>Audience</th>
            <th>Careoff</th>
            <th>Group</th>
            <th>Lead Date Range</th>
            <th>Send Date</th>
            <th>Status</th>
            <th>Message Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="listitem">
        @if ($posts->count() > 0)
            @foreach ($posts as $post)
                @php
                    if ($post->careoff_id != '') {

                        $careoffs = DB::table('admins')->where('status',1)->whereIn('id',explode(",",$post->careoff_id))->get();
                        if ($careoffs) {
                            $careoff_name = [];
                            foreach ($careoffs as $careoff) {
                                $careoff_name [] = $careoff->name;
                            }
                            $newcareoffname = implode(",",$careoff_name);
                            $careoffname = substr($newcareoffname,0,10).'...';

                        } else {
                            $careoffname = "---";
                            $newcareoffname = "---";
                        }

                    } else {

                        $careoffname = "---";
                    }

                    if ($post->group_id != '') {
                        if ($post->audience == 'contactp') {
                            $contactpsgrp = DB::table('groupms')->whereIn('id',explode(",",$post->group_id))->get();
                            if ($contactpsgrp) {
                                $contactpgroupname = [];
                                foreach ($contactpsgrp as $contactpsgrp2) {
                                    $contactpgroupname[] = $contactpsgrp2->name;
                                }

                                $group_name = implode(",",$contactpgroupname);

                            } else {
                                $group_name = "---";
                            }

                        } elseif ($post->audience == 'allcontact') {
                            $allcontactgrp = DB::table('groupallcs')->whereIn('id',explode(",",$post->group_id))->get();
                            if ($allcontactgrp) {
                                $allcontactgrpname = [];

                                foreach ($allcontactgrp as $allcontactgrp2) {
                                    $allcontactgrpname[] = $allcontactgrp2->name;
                                }

                                $group_name = implode(",",$allcontactgrpname);
                            } else {
                                $group_name = "---";
                            }

                        } else{
                            $group_name = "---";
                        }

                    } else {
                        $group_name = "---";
                    }

                    if ($post->message_status == 'success') {
                       $badge = "badge bg-label-success";
                       $message_status = $post->message_status;
                    } 
                    elseif ($post->message_status == 'Scheduled') {
                        $badge = "badge bg-label-warning";
                        $message_status = $post->message_status;
                    }
                    
                    else {
                       $badge = "badge bg-label-danger";

                       if ($post->message_status != '') {
                           $message_status = $post->message_status;
                       } else {
                           $message_status = 'Unknown Error';
                       }

                    }

                    if ($post->message_text != '') {
                        $message_text = $post->message_text;
                    } else {
                        $message_text = "Unknown Error";
                    }


                @endphp

                <tr>
                    <td><a href="{{ route('admin.metawhatsapp.campaignShow',$post->id) }}" target="_blank">{{ $post->campaign_name }}</a></td>
                    <td>
                        @if ($post->metatemp_id)
                            {{ $post->metatemp->template_name ?? '--' }}
                        @else
                            {{ '---' }}
                        @endif
                    </td>
                    <td>{{ $post->audience }}</td>
                    <td title="{{ $newcareoffname ?? '' }}">{{ $careoffname ?? '' }}</td>
                    <td>{{ $group_name }}</td>
                    <td>{{ $post->leads_date ?? '--' }}</td>
                    <td>{{ date('d-m-Y H:i:s', strtotime($post->created_at)) }}</td>
                    <td><span class="{{ $badge }}">{{ $message_status }}</span></td>
                    <td>{{ $message_text }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.metawhatsapp.campaignShow',$post->id) }}" target="_blank" class="text-body"><i class="ti ti-eye ti-sm me-2"></i></a>
                            <a href="javascript:void(0);"
                                class="text-danger deleteCampaignBtn"
                                data-id="{{ $post->id }}"
                                data-name="{{ $post->campaign_name ?? 'This Campaign' }}">
                                    <i class="ti ti-trash ti-sm"></i>
                                </a>
                            <!-- <a href="javascript:;" class="text-body deltemplate" data-bs-toggle="modal" data-bs-target="#deleteStaffold" data-id="{{ $post->id }}"><i class="ti ti-trash ti-sm mx-2"></i></a> -->
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
        {{ 'Showing '.$posts->firstItem().' to '.$posts->lastItem().' of '.$posts->total().' Entries' }}
    </div>
    <div class="float-end">
        {{ $posts->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
