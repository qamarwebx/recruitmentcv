<table class="datatables-users table border-top">
    <thead>
        <tr>
            <th>ID</th>
            <th>Candidate Name</th>
            <th>Passport No.</th>
            <th>Care Of</th>
            <th>Created At</th>
            <th>Approval Status</th>
            <th>Payment Status</th>
            <th>Video Received</th>
            <th>Social Media</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="listitem">
        @if ($testimonialLists->count() > 0)
            @foreach ($testimonialLists as $testimonial)
                <tr>
                    @php
                        $canView = auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->view_testimonial == 1));
                    @endphp
                    <td>{{ $testimonial->id }}</td>
                    <td>
                        @if($canView)
                            <a href="javascript:;" class="viewTestimonialBtn" data-bs-toggle="modal"
                               data-bs-target="#viewTestimonial" data-id="{{ $testimonial->id }}">
                                {{ $testimonial->full_name ?? '---' }}
                            </a>
                        @else
                            {{ $testimonial->full_name ?? '---' }}
                        @endif
                    </td>
                    <td>{{ $testimonial->passport_number ?? '---' }}</td>
                    <td>{{ optional($testimonial->creator)->name ?? '---' }}</td>
                    <td>{{ $testimonial->created_at?->format('d M Y, h:i A') ?? '---' }}</td>
                    <td>
                        @php
                            $approvalBadge = match ($testimonial->approval_status) {
                                'Approved' => 'bg-label-success',
                                'Rejected' => 'bg-label-danger',
                                default => 'bg-label-warning',
                            };
                            $canApprove = auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->approval_testimonial == 1));
                        @endphp
                        @if($canApprove)
                            <div class="dropdown">
                                <span class="badge {{ $approvalBadge }} dropdown-toggle" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                                    {{ $testimonial->approval_status ?? 'Pending' }}
                                </span>
                                <ul class="dropdown-menu">
                                    @if($testimonial->approval_status !== 'Approved')
                                    <li>
                                        <a class="dropdown-item approveTestimonialBtn text-success" href="javascript:;"
                                        data-id="{{ $testimonial->id }}">
                                        <i class="ti ti-check me-2"></i> Approve
                                        </a>
                                    </li>
                                    @endif
                                    @if($testimonial->approval_status !== 'Rejected')
                                    <li>
                                        <a class="dropdown-item rejectTestimonialBtn text-danger" href="javascript:;"
                                        data-id="{{ $testimonial->id }}">
                                        <i class="ti ti-x me-2"></i> Reject
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        @else
                            <span class="badge {{ $approvalBadge }}">{{ $testimonial->approval_status ?? 'Pending' }}</span>
                        @endif
                    </td>
                    <td>
                        @if($testimonial->approval_status === 'Approved')
                            @php
                                $paymentBadge = $testimonial->payment_status === 'Paid' ? 'bg-label-success' : 'bg-label-warning';
                                $canMarkPaid = auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->payment_testimonial == 1));
                            @endphp
                            @if($testimonial->payment_status === 'Paid')
                                <span class="badge {{ $paymentBadge }} viewPaymentBtn" role="button"
                                      data-id="{{ $testimonial->id }}" title="View Payment Info" style="cursor:pointer;">
                                    ₹{{ number_format((float) $testimonial->paid_amount) }} Paid
                                </span>
                            @else
                                <span class="badge {{ $paymentBadge }}">{{ $testimonial->payment_status ?? 'Pending' }}</span>
                            @endif
                            @if($testimonial->payment_status !== 'Paid' && $canMarkPaid)
                                <button type="button" class="btn btn-xs btn-outline-primary markPaidBtn ms-1" data-id="{{ $testimonial->id }}">
                                    Mark Paid
                                </button>
                            @endif
                        @else
                            <span class="text-muted">&mdash;</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $videoBadge = $testimonial->video_received_status === 'Received' ? 'bg-label-success' : 'bg-label-warning';
                            $canUpdateVideo = auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->video_received_testimonial == 1));
                        @endphp
                        @if($canUpdateVideo)
                            <div class="dropdown">
                                <span class="badge {{ $videoBadge }} dropdown-toggle" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                                    {{ $testimonial->video_received_status ?? 'Not Received' }}
                                </span>
                                <ul class="dropdown-menu">
                                    @if($testimonial->video_received_status !== 'Received')
                                    <li>
                                        <a class="dropdown-item videoReceivedBtn text-success" href="javascript:;"
                                        data-id="{{ $testimonial->id }}" data-status="Received">
                                        <i class="ti ti-check me-2"></i> Mark as Received
                                        </a>
                                    </li>
                                    @else
                                    <li>
                                        <a class="dropdown-item videoReceivedBtn text-warning" href="javascript:;"
                                        data-id="{{ $testimonial->id }}" data-status="Not Received">
                                        <i class="ti ti-x me-2"></i> Mark as Not Received
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        @else
                            <span class="badge {{ $videoBadge }}">{{ $testimonial->video_received_status ?? 'Not Received' }}</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $socialBadge = $testimonial->social_media_status === 'Posted' ? 'bg-label-success' : 'bg-label-warning';
                            $canUpdateSocial = auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->social_media_testimonial == 1));
                            $platforms = $testimonial->social_media_platforms ?? [];
                        @endphp
                        @if($canUpdateSocial)
                            <div class="dropdown">
                                <span class="badge {{ $socialBadge }} dropdown-toggle" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                                    {{ $testimonial->social_media_status ?? 'Not Posted' }}
                                </span>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item socialMediaPostedBtn text-success" href="javascript:;"
                                        data-id="{{ $testimonial->id }}" data-platforms='@json($platforms)'>
                                        <i class="ti ti-share me-2"></i>
                                        {{ $testimonial->social_media_status === 'Posted' ? 'Edit Platforms' : 'Mark as Posted' }}
                                        </a>
                                    </li>
                                    @if($testimonial->social_media_status === 'Posted')
                                    <li>
                                        <a class="dropdown-item socialMediaNotPostedBtn text-warning" href="javascript:;"
                                        data-id="{{ $testimonial->id }}">
                                        <i class="ti ti-x me-2"></i> Mark as Not Posted
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        @else
                            <span class="badge {{ $socialBadge }}">{{ $testimonial->social_media_status ?? 'Not Posted' }}</span>
                        @endif
                        @if($testimonial->social_media_status === 'Posted' && count($platforms) > 0)
                            <div class="small text-muted mt-1">Platforms: {{ implode(', ', $platforms) }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical ti-sm mx-1"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end m-0">

                                {{-- View Testimonial --}}
                                @if($canView)
                                <a class="dropdown-item viewTestimonialBtn" href="javascript:;"
                                data-bs-toggle="modal"
                                data-bs-target="#viewTestimonial"
                                data-id="{{ $testimonial->id }}">
                                <i class="ti ti-eye me-2"></i> View
                                </a>
                                @endif

                                {{-- Edit Testimonial --}}
                                @if(auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->edit_testimonial == 1)))
                                <a class="dropdown-item editTestimonialBtn" href="javascript:;"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#editTestimonial"
                                data-id="{{ $testimonial->id }}">
                                <i class="ti ti-edit me-2"></i> Edit
                                </a>
                                @endif

                                {{-- Copy Link --}}
                                @if(auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->copy_link_testimonial == 1)))
                                <a class="dropdown-item copyTestimonialLinkBtn" href="javascript:;"
                                data-link="{{ $testimonial->link }}">
                                <i class="ti ti-link me-2"></i> Copy Link
                                </a>
                                @endif

                                {{-- Payment Status / Mark Paid --}}
                                @if($testimonial->approval_status === 'Approved')
                                    @if($testimonial->payment_status === 'Paid' && $canView)
                                    <a class="dropdown-item viewPaymentBtn" href="javascript:;" data-id="{{ $testimonial->id }}">
                                    <i class="ti ti-receipt me-2"></i> Payment Status
                                    </a>
                                    @elseif($testimonial->payment_status !== 'Paid' && $canMarkPaid)
                                    <a class="dropdown-item markPaidBtn" href="javascript:;" data-id="{{ $testimonial->id }}">
                                    <i class="ti ti-cash me-2"></i> Mark Paid
                                    </a>
                                    @endif
                                @endif

                                {{-- Delete Testimonial --}}
                                @if(auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->delete_testimonial == 1)))
                                <a class="dropdown-item deltestimonial" href="javascript:;"
                                data-id="{{ $testimonial->id }}">
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
                <td colspan="10" class="text-center">No Testimonials Found</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="px-4 pt-3">
    <div class="float-start">
        {{ 'Showing '.$testimonialLists->firstItem().' to '.$testimonialLists->lastItem().' of '.$testimonialLists->total().' Entries' }}
    </div>
    <div class="float-end">
        {{ $testimonialLists->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
