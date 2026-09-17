@extends('layout.admin.admin_layout')

@section('title', 'Email Campaign Details')

@section('page-style')
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Email Campaign Details</h4>
            <a href="{{ route('admin.emailCampaign.list') }}" class="btn btn-sm btn-primary">← Back to List</a>
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
                    <strong>SMTP:</strong> {{ $campaign->smtp->smtp_name ?? '---' }}
                </div>
                <div class="col-md-3">
                    <strong>Template:</strong> {{ $campaign->template->template_name ?? '---' }}
                </div>
                <div class="col-md-3">
                    <strong>Status:</strong>
                    @if ($campaign->email_status == 'Success')
                        <span class="badge bg-success">Success</span>
                    @elseif ($campaign->email_status == 'Scheduled')
                        <span class="badge bg-warning">Scheduled</span>
                    @elseif ($campaign->email_status == 'Failed')
                        <span class="badge bg-danger">Failed</span>
                    @else
                        <span class="badge bg-secondary">Pending</span>
                    @endif
                </div>
                <div class="col-md-3">
                    <strong>Sent / Scheduled:</strong>
                    {{ $campaign->schedule_datetime ?? '---' }}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3">
                    <strong>Audience:</strong> {{ ucfirst($campaign->audience ?? '---') }}
                </div>
                <div class="col-md-3">
                    <strong>Created By:</strong> {{ $campaign->admin_name ?? '---' }}
                </div>
                <div class="col-md-3">
                    <strong>Careoff:</strong> {{ $campaign->careoff_name ?? '---' }}
                </div>
                <div class="col-md-3">
                    <strong>Group:</strong> {{ $campaign->group_name ?? '---' }}
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <strong>Subject:</strong>
                    <div class="border rounded bg-light p-2">{{ $campaign->email_subject ?? '---' }}</div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <strong>Email Body:</strong>
                    <div class="border rounded p-3 bg-light">
                        {!! $campaign->email_body !!}
                    </div>
                </div>
            </div>

            @if($campaign->attachment)
            <div class="row mt-3">
                <div class="col-md-12">
                    <strong>Attachment:</strong>
                    <div>
                        <a href="{{ asset('admin/assets/images/email-template/'.$campaign->attachment) }}"
                           target="_blank" class="btn btn-outline-primary btn-sm mt-2">
                            <i class="ti ti-download"></i> Download Attachment
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Response Table -->
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between">
            <h5 class="card-title mb-0">Email Delivery Reports</h5>
            <button type="button" class="btn btn-sm btn-danger page-refresh">
                <i class="ti ti-refresh"></i> Refresh
            </button>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table datatables-users border-top">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Response Message</th>
                        <th>Error Code</th>
                        <th>Sent At</th>
                    </tr>
                </thead>
                <tbody>
                    @if($responses->count() > 0)
                        @foreach($responses as $index => $r)
                            @php
                                $response = json_decode($r->main_response, true);
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $r->email ?? '---' }}</td>
                                <td>{{ $r->name ?? '---' }}</td>
                                <td>
                                    @if (strtolower($r->message_status) == 'success')
                                        <span class="badge bg-label-success">Success</span>
                                    @elseif (strtolower($r->message_status) == 'scheduled')
                                        <span class="badge bg-label-warning">Scheduled</span>
                                    @elseif (strtolower($r->message_status) == 'failed')
                                        <span class="badge bg-label-danger">Failed</span>
                                    @else
                                        <span class="badge bg-label-secondary">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $response['MessageErrorDescription'] ?? $r->message_text ?? '---' }}</td>
                                <td>{{ $response['MessageErrorCode'] ?? '---' }}</td>
                                <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y h:i A') }}</td>
                            </tr>
                        @endforeach
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
