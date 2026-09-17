<table class="datatables-users table border-top">
    <thead>
        <tr>
            <th>ID</th>
            <th>Care Of</th>
            <th>Created At</th>
            <th>Approval Status</th>
            <th>Payment Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="listitem">
        @if ($googleReviewLists->count() > 0)
            @foreach ($googleReviewLists as $review)
                <tr>
                    @php
                        $canView = auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->view_google_review == 1));
                    @endphp
                    <td>{{ $review->id }}</td>
                    <td>
                        @if($canView)
                            <a href="javascript:;" class="viewGoogleReviewBtn" data-bs-toggle="offcanvas"
                               data-bs-target="#viewGoogleReview" data-id="{{ $review->id }}">
                                {{ optional($review->creator)->name ?? '---' }}
                            </a>
                        @else
                            {{ optional($review->creator)->name ?? '---' }}
                        @endif
                    </td>
                    <td>{{ $review->created_at?->format('d M Y, h:i A') ?? '---' }}</td>
                    <td>
                        @php
                            $approvalBadge = match ($review->approval_status) {
                                'Approved' => 'bg-label-success',
                                'Rejected' => 'bg-label-danger',
                                default => 'bg-label-warning',
                            };
                            $canApprove = auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->approval_google_review == 1));
                        @endphp
                        @if($canApprove)
                            <div class="dropdown">
                                <span class="badge {{ $approvalBadge }} dropdown-toggle" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                                    {{ $review->approval_status ?? 'Pending' }}
                                </span>
                                <ul class="dropdown-menu">
                                    @if($review->approval_status !== 'Approved')
                                    <li>
                                        <a class="dropdown-item approveGoogleReviewBtn text-success" href="javascript:;"
                                        data-id="{{ $review->id }}">
                                        <i class="ti ti-check me-2"></i> Approve
                                        </a>
                                    </li>
                                    @endif
                                    @if($review->approval_status !== 'Rejected')
                                    <li>
                                        <a class="dropdown-item rejectGoogleReviewBtn text-danger" href="javascript:;"
                                        data-id="{{ $review->id }}">
                                        <i class="ti ti-x me-2"></i> Reject
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        @else
                            <span class="badge {{ $approvalBadge }}">{{ $review->approval_status ?? 'Pending' }}</span>
                        @endif
                    </td>
                    <td>
                        @if($review->approval_status === 'Approved')
                            @php
                                $paymentBadge = $review->payment_status === 'Paid' ? 'bg-label-success' : 'bg-label-warning';
                                $canMarkPaid = auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->payment_google_review == 1));
                            @endphp
                            @if($review->payment_status === 'Paid')
                                <span class="badge {{ $paymentBadge }} viewPaymentBtn" role="button"
                                      data-id="{{ $review->id }}" title="View Payment Info" style="cursor:pointer;">
                                    ₹{{ number_format((float) $review->paid_amount) }} Paid
                                </span>
                            @else
                                <span class="badge {{ $paymentBadge }}">{{ $review->payment_status ?? 'Pending' }}</span>
                            @endif
                            @if($review->payment_status !== 'Paid' && $canMarkPaid)
                                <button type="button" class="btn btn-xs btn-outline-primary markPaidBtn ms-1" data-id="{{ $review->id }}">
                                    Mark Paid
                                </button>
                            @endif
                        @else
                            <span class="text-muted">&mdash;</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical ti-sm mx-1"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end m-0">

                                {{-- View Google Review --}}
                                @if($canView)
                                <a class="dropdown-item viewGoogleReviewBtn" href="javascript:;"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#viewGoogleReview"
                                data-id="{{ $review->id }}">
                                <i class="ti ti-eye me-2"></i> View
                                </a>
                                @endif

                                {{-- Edit Google Review --}}
                                @if(auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->edit_google_review == 1)))
                                <a class="dropdown-item editGoogleReviewBtn" href="javascript:;"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#editGoogleReview"
                                data-id="{{ $review->id }}">
                                <i class="ti ti-edit me-2"></i> Edit
                                </a>
                                @endif

                                {{-- Payment Status / Mark Paid --}}
                                @if($review->approval_status === 'Approved')
                                    @if($review->payment_status === 'Paid' && $canView)
                                    <a class="dropdown-item viewPaymentBtn" href="javascript:;" data-id="{{ $review->id }}">
                                    <i class="ti ti-receipt me-2"></i> Payment Status
                                    </a>
                                    @elseif($review->payment_status !== 'Paid' && $canMarkPaid)
                                    <a class="dropdown-item markPaidBtn" href="javascript:;" data-id="{{ $review->id }}">
                                    <i class="ti ti-cash me-2"></i> Mark Paid
                                    </a>
                                    @endif
                                @endif

                                {{-- Delete Google Review --}}
                                @if(auth()->user()->user_type == 1 || (isset($perm) && ($perm->full_access == 1 || $perm->delete_google_review == 1)))
                                <a class="dropdown-item delgooglereview" href="javascript:;"
                                data-id="{{ $review->id }}">
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
                <td colspan="6" class="text-center">No Google Reviews Found</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="px-4 pt-3">
    <div class="float-start">
        {{ 'Showing '.$googleReviewLists->firstItem().' to '.$googleReviewLists->lastItem().' of '.$googleReviewLists->total().' Entries' }}
    </div>
    <div class="float-end">
        {{ $googleReviewLists->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
