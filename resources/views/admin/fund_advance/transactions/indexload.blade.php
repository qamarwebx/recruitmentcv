<table class="table border-top">
    <thead>
        <tr>
            <th>Date</th>
            <th>Transaction No.</th>
            <th>Party/Employee</th>
            <th>Type</th>
            <th>Nature</th>
            <th>Description</th>
            <th class="text-end">Amount</th>
            <th class="text-end">Settled</th>
            <th class="text-end">Outstanding</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($posts as $post)
            @php
                $statusColors = ['Pending' => 'warning', 'Partially Settled' => 'info', 'Settled' => 'success', 'Cancelled' => 'secondary'];
                $status = $post->computed_status;
            @endphp
            <tr>
                <td>{{ $post->transaction_date->format('d-m-Y') }}</td>
                <td>{{ $post->transaction_no }}</td>
                <td>{{ $post->party_display_name }}</td>
                <td>{{ $post->transaction_type }}</td>
                <td>{{ $post->transaction_nature }}</td>
                <td>{{ \Illuminate\Support\Str::limit($post->description, 40) }}</td>
                <td class="text-end">{{ number_format($post->amount, 2) }}</td>
                <td class="text-end">{{ number_format($post->settled_amount ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($post->outstanding, 2) }}</td>
                <td><span class="badge bg-label-{{ $statusColors[$status] ?? 'secondary' }}">{{ $status }}</span></td>
                <td>{{ optional($post->creator)->name ?? '-' }}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>
                        <div class="dropdown-menu dropdown-menu-end m-0">
                            <a href="javascript:void(0);" class="dropdown-item viewTransactionBtn" data-id="{{ $post->id }}">View</a>
                            @if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && ($permission->full_access == 1 || $permission->fund_advance_edit == 1)))
                                @if ($post->status !== 'Cancelled')
                                    <a href="javascript:;" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEditTransaction" data-id="{{ $post->id }}">Edit</a>
                                @endif
                            @endif
                            @if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && ($permission->full_access == 1 || $permission->fund_advance_delete == 1)))
                                @if ($post->status !== 'Cancelled')
                                    <a href="javascript:;" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#cancelTransactionModal" data-id="{{ $post->id }}">Cancel</a>
                                @endif
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="12" class="text-center py-4">No transactions found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

@if ($posts->count() > 0)
    <div class="px-4 pt-3 pb-3">
        <div class="float-start">{{ 'Showing '.$posts->firstItem().' to '.$posts->lastItem().' of '.$posts->total().' Entries' }}</div>
        <div class="float-end">{{ $posts->links('vendor.pagination.bootstrap-4') }}</div>
    </div>
@endif
