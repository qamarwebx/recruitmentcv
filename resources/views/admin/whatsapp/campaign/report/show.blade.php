@extends('layout.admin.admin_layout')

@section('title','Whatsapp Campaign Response')

@section('page-style')

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-datatable table-responsive content-reload-page">
                <table class="datatables-users table">
                    <thead>
                        <tr>
                            <th>Campaign Name</th>
                            <th>Audience</th>
                            <th>Name</th>
                            <th>Mobile No</th>
                            <th>Send Date</th>
                            <th>Status</th>
                            <th>Response Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($posts->count() > 0)
                            @foreach ($posts as $post)
                                @php
                                    if ($post->campaignlist_id != '') {
                                        if ($post->campaignlist->name != '') {
                                            if ($post->campaignlist->name != 'Contact+') {
                                                $campaign_name = "Contact Plus";
                                            } else {
                                                $campaign_name = $post->campaignlist->name;
                                            }

                                        } else {
                                            $campaign_name = "---";
                                        }

                                        $campaign_audience = $post->campaignlist->audience;
                                    } else {
                                        $campaign_name = "---";
                                        $campaign_audience = "---";
                                    }

                                @endphp
                                <tr>
                                    <td>{{ $campaign_name }}</td>
                                    <td>{{ $campaign_audience }}</td>
                                    <td>{{ $post->name }}</td>
                                    <td>{{ $post->mobile_no }}</td>
                                    <td>{{ date('d-m-Y h:i',strtotime($post->created_at)) }}</td>
                                    <td>{{ $post->message_status }}</td>
                                    <td>{{ $post->message_text }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">No Response Found</td>
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
                        <div class="float-end">
                            {{ $posts->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('page-script')


@endsection
