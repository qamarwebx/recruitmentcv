@extends('layout.admin.admin_layout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <div class="card shadow-lg border-0 rounded-4 bg-body text-body">

                {{-- Card Header --}}
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h4 class="mb-0 text-white">
                        <i class="ti ti-shield-check me-2"></i> Device Approval Request
                    </h4>
                </div>

                {{-- Card Body --}}
                @if($device->is_approved === 0)
                    <div class="card-body p-4">

                        {{-- Admin Info --}}
                        <div class="mb-4">
                            <h5 class="fw-bold mb-1">{{ $device->admin->name ?? 'Unknown Admin' }}</h5>
                            <p class="text-muted mb-0"><strong>Email:</strong> {{ $device->admin->email ?? 'N/A' }}</p>
                        </div>

                        {{-- Device Details --}}
                        <div class="bg-light-subtle p-3 rounded mb-4 border border-secondary-subtle">
                            <h6 class="text-uppercase fw-bold small mb-3 text-secondary">Device Details</h6>
                            <p class="mb-1"><strong>Device:</strong> {{ $device->device_type ?? 'Unknown' }}</p>
                            <p class="mb-1"><strong>Browser:</strong> {{ $device->browser ?? 'Unknown' }}</p>
                            <p class="mb-1"><strong>Operating System:</strong> {{ $device->os ?? 'Unknown' }}</p>
                            <p class="mb-1"><strong>IP Address:</strong> {{ $device->ip_address ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Location:</strong> {{ $device->location ?? 'Unknown' }}</p>
                        </div>

                        {{-- Approval Form --}}
                        <form method="POST" action="{{ route('admin.device.approval.submit', $device->id) }}">
                            @csrf

                            {{-- Approval Decision --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Approval Decision</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="approval_status" id="approve" value="approved" required>
                                    <label class="form-check-label text-success fw-semibold" for="approve">
                                        ✅ Approve Device
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="approval_status" id="reject" value="rejected" required>
                                    <label class="form-check-label text-danger fw-semibold" for="reject">
                                        ❌ Reject Device
                                    </label>
                                </div>
                            </div>

                            {{-- Comments --}}
                            <div class="mb-4">
                                <label for="comments" class="form-label fw-semibold">Comments <span class="text-danger">*</span></label>
                                <textarea id="comments" name="comments" class="form-control bg-body-secondary text-body border-secondary-subtle"
                                          rows="4" required placeholder="Please explain why you are approving or rejecting this device..."></textarea>
                            </div>

                            {{-- Admin Email --}}
                            <!-- <div class="mb-4">
                                <label for="login_access_email" class="form-label fw-semibold">Login Access Email <span class="text-danger">*</span></label>
                                <input type="email" id="login_access_email" name="login_access_email" value="{{ $admin->login_access_email }}" class="form-control bg-body-secondary text-body border-secondary-subtle"
                                       placeholder="Enter your super admin email to confirm" required>
                            </div> -->

                            {{-- Submit --}}
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary px-4" type="submit">
                                    <i class="ti ti-send me-1"></i> Submit Decision
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    {{-- Already approved --}}
                    <div class="card-body text-center">
                        <div class="alert alert-success mb-0 mt-3">
                            ✅ This device has already been approved.
                        </div>
                    </div>
                @endif

                {{-- Card Footer --}}
                <div class="card-footer text-center small text-muted py-2 bg-body-tertiary border-top border-secondary-subtle">
                    <i class="ti ti-lock me-1"></i> Secure Device Verification System
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Dark Mode Support --}}
<style>
    @media (prefers-color-scheme: dark) {
        .bg-light-subtle {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
        .border-secondary-subtle {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        .bg-body-tertiary {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }
    }
</style>
@endsection
