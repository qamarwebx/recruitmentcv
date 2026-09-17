<table class="table border-top">
    <thead>
        <tr>
            <th>Date</th><th>Transaction No.</th><th>Party/Employee</th><th>Type</th>
            <th class="text-end">Original Amount</th><th class="text-end">Settled</th><th class="text-end">Outstanding</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($posts as $post)
            @php
                $label = $post->transaction_no . ' - ' . $post->party_display_name . ' (' . $post->transaction_type . ')';
                $canSettle = (Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && ($permission->full_access == 1 || $permission->fund_advance_settlement_create == 1 || $permission->fund_advance_adjustment_create == 1));
            @endphp
            <tr>
                <td>{{ $post->transaction_date->format('d-m-Y') }}</td>
                <td>{{ $post->transaction_no }}</td>
                <td>{{ $post->party_display_name }}</td>
                <td>{{ $post->transaction_type }}</td>
                <td class="text-end">{{ number_format($post->amount, 2) }}</td>
                <td class="text-end">{{ number_format($post->settled_amount ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($post->outstanding, 2) }}</td>
                <td>
                    @if ($canSettle)
                        <button type="button" class="btn btn-sm btn-primary settleBtn"
                            data-id="{{ $post->id }}"
                            data-label="{{ $label }}"
                            data-outstanding="{{ $post->outstanding }}">Settle</button>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center py-4">No pending settlements.</td></tr>
        @endforelse
    </tbody>
</table>

@if ($posts->count() > 0)
    <div class="px-4 pt-3 pb-3">
        <div class="float-start">{{ 'Showing '.$posts->firstItem().' to '.$posts->lastItem().' of '.$posts->total().' Entries' }}</div>
        <div class="float-end">{{ $posts->links('vendor.pagination.bootstrap-4') }}</div>
    </div>
@endif
