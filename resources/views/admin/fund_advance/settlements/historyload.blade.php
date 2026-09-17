<table class="table border-top">
    <thead>
        <tr>
            <th>Date</th><th>Settlement No.</th><th>Original Transaction</th><th>Party/Employee</th>
            <th class="text-end">Original Amount</th><th class="text-end">Settlement Amount</th><th>Payment Mode</th><th>Reference</th>
            <th>Created By</th><th>Status</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($posts as $post)
            @php
                $canReverse = $post->status === 'Active' && ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && ($permission->full_access == 1 || $permission->fund_advance_delete == 1)));
            @endphp
            <tr>
                <td>{{ $post->settlement_date->format('d-m-Y') }}</td>
                <td>{{ $post->settlement_no }}</td>
                <td>{{ optional($post->transaction)->transaction_no }}</td>
                <td>{{ optional($post->transaction)->party_display_name }}</td>
                <td class="text-end">{{ number_format(optional($post->transaction)->amount, 2) }}</td>
                <td class="text-end">{{ number_format($post->amount, 2) }}</td>
                <td>{{ $post->payment_mode }}</td>
                <td>{{ $post->reference_no ?: '-' }}</td>
                <td>{{ optional($post->creator)->name ?? '-' }}</td>
                <td><span class="badge bg-label-{{ $post->status === 'Active' ? 'success' : 'secondary' }}">{{ $post->status }}</span></td>
                <td>
                    @if ($canReverse)
                        <a href="javascript:;" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reverseModal" data-id="{{ $post->id }}">Reverse</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="11" class="text-center py-4">No settlements found.</td></tr>
        @endforelse
    </tbody>
</table>

@if ($posts->count() > 0)
    <div class="px-4 pt-3 pb-3">
        <div class="float-start">{{ 'Showing '.$posts->firstItem().' to '.$posts->lastItem().' of '.$posts->total().' Entries' }}</div>
        <div class="float-end">{{ $posts->links('vendor.pagination.bootstrap-4') }}</div>
    </div>
@endif
