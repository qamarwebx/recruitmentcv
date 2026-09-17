@extends('layout.admin.admin_layout')

@section('title', 'SMS Campaign Details')

@section('page-style')
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">SMS Campaign Details</h4>
            <a href="{{ route('admin.smsCampaign.list') }}" class="btn btn-sm btn-primary">← Back to List</a>
        </div>
    </div>

    <!-- Campaign Summary -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ $campaign->campaign_name }}</h5>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-3">
                    <strong>Audience:</strong> {{ ucfirst($campaign->audience ?? 'N/A') }}
                </div>
                <div class="col-md-3">
                    <strong>Created By:</strong> {{ $campaign->admin_name ?? '---' }}
                </div>
                <div class="col-md-3">
                    <strong>Status:</strong>
                    @if ($campaign->message_status == 'Success')
                        <span class="badge bg-success">Success</span>
                    @elseif ($campaign->message_status == 'Scheduled')
                        <span class="badge bg-warning">Scheduled</span>
                    @else
                        <span class="badge bg-danger">{{ ucfirst($campaign->message_status) }}</span>
                    @endif
                </div>
                <div class="col-md-3">
                    <strong>Sent / Scheduled:</strong>
                    {{ $campaign->date_and_time ?? '---' }}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-12">
                    <strong>Message:</strong>
                    <div class="border rounded p-2 bg-light">{{ $campaign->msg_body }}</div>
                </div>
            </div>

            <div class="row mt-3">
                @if($campaign->partner_name)
                    <div class="col-md-3"><strong>Partner:</strong> {{ $campaign->partner_name }}</div>
                @endif
                @if($campaign->associate_name)
                    <div class="col-md-3"><strong>Associate:</strong> {{ $campaign->associate_name }}</div>
                @endif
                @if($campaign->client_name)
                    <div class="col-md-3"><strong>Client:</strong> {{ $campaign->client_name }}</div>
                @endif
                @if($campaign->contactp_name)
                    <div class="col-md-3"><strong>Contact Plus:</strong> {{ $campaign->contactp_name }}</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Response Table -->
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between">
            <h5 class="card-title mb-0">Sent Messages</h5>
            <button type="button" class="btn btn-sm btn-danger page-refresh">
                <i class="ti ti-refresh"></i> Refresh
            </button>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table datatables-users border-top">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>Mobile No</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Message Text</th>
                        <th>Error Info</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @if($responses->count() > 0)
                        @forelse($responses as $index => $r)
                            @php
                                // Decode only once, safely
                                $response = json_decode($r->main_response, true);
                                $decoded = json_decode($response, true);
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $r->mobile_no }}</td>
                                <td>{{ $r->name ?? '---' }}</td>
                                <td>
                                    @if (strtolower($r->message_status) == 'success')
                                        <span class="badge bg-label-success">Success</span>
                                    @elseif (strtolower($r->message_status) == 'scheduled')
                                        <span class="badge bg-label-warning">Scheduled</span>
                                    @else
                                        <span class="badge bg-label-danger">{{ ucfirst($r->message_status ?? 'Failed') }}</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($r->message_text, 80) }}</td>
                                <td>
                                    @if(is_array($decoded))
                                        <div>
                                            <strong>Status:</strong> {{ $decoded['MessageErrorDescription'] ?? '---' }}<br>
                                            <strong>Mobile:</strong> {{ $decoded['MobileNumber'] ?? '---' }}<br>
                                            <strong>Message ID:</strong> {{ $decoded['MessageId'] ?? '---' }}<br>
                                            <strong>Error Code:</strong> {{ $decoded['MessageErrorCode'] ?? '---' }}
                                        </div>
                                    @else
                                        <div>---</div>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No responses found.</td>
                            </tr>
                        @endforelse
                    @else
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">No responses found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/datatables-bs5/dataTables.bootstrap5.js') }}"></script>
@if($responses->count() > 0)
<script>
    $('.datatables-users').DataTable({
        autoWidth: false,
        responsive: true,
        columnDefs: [
            { targets: "_all", defaultContent: "---" }
        ]
    });

    $('.page-refresh').on('click', () => location.reload());
</script>
@endif
@endsection
