@php
    $statusColors = ['Pending' => 'warning', 'Partially Settled' => 'info', 'Settled' => 'success', 'Cancelled' => 'secondary'];
    $status = $transaction->computed_status;
@endphp
<div class="row g-3">
    <div class="col-md-4"><strong>Transaction No.</strong><div>{{ $transaction->transaction_no }}</div></div>
    <div class="col-md-4"><strong>Date</strong><div>{{ $transaction->transaction_date->format('d-m-Y') }}</div></div>
    <div class="col-md-4"><strong>Status</strong><div><span class="badge bg-label-{{ $statusColors[$status] ?? 'secondary' }}">{{ $status }}</span></div></div>

    <div class="col-md-4"><strong>Type</strong><div>{{ $transaction->transaction_type }}</div></div>
    <div class="col-md-4"><strong>Nature</strong><div>{{ $transaction->transaction_nature }}</div></div>
    <div class="col-md-4"><strong>Party/Employee</strong><div>{{ $transaction->party_display_name }} <span class="text-muted">({{ ucfirst($transaction->party_type) }})</span></div></div>

    <div class="col-md-4"><strong>Amount</strong><div>{{ number_format($transaction->amount, 2) }}</div></div>
    <div class="col-md-4"><strong>Settled</strong><div>{{ number_format($transaction->settled_amount, 2) }}</div></div>
    <div class="col-md-4"><strong>Outstanding</strong><div>{{ number_format($transaction->outstanding, 2) }}</div></div>

    <div class="col-md-4"><strong>Payment Mode</strong><div>{{ $transaction->payment_mode }}</div></div>
    <div class="col-md-4"><strong>Reference No.</strong><div>{{ $transaction->reference_no ?: '-' }}</div></div>
    <div class="col-md-4"><strong>Created By</strong><div>{{ optional($transaction->creator)->name ?? '-' }} on {{ $transaction->created_at->format('d-m-Y H:i') }}</div></div>

    <div class="col-md-12"><strong>Description</strong><div>{{ $transaction->description ?: '-' }}</div></div>

    @if ($transaction->attachment)
        <div class="col-md-12">
            <strong>Attachment</strong>
            <div>
                <a href="{{ route('admin.fund_advance.transactions.attachment.view', $transaction->id) }}" target="_blank">View</a>
                &middot;
                <a href="{{ route('admin.fund_advance.transactions.attachment.download', $transaction->id) }}">Download</a>
            </div>
        </div>
    @endif

    @if ($transaction->status === 'Cancelled')
        <div class="col-md-12">
            <div class="alert alert-secondary mb-0"><strong>Cancelled:</strong> {{ $transaction->cancel_reason }}</div>
        </div>
    @endif

    <div class="col-md-12">
        <hr>
        <h6>Settlement History</h6>
        @if ($transaction->settlements->isEmpty())
            <p class="text-muted">No settlements recorded yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Date</th><th>No.</th><th>Type</th><th class="text-end">Amount</th><th>Mode</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach ($transaction->settlements as $s)
                            <tr>
                                <td>{{ $s->settlement_date->format('d-m-Y') }}</td>
                                <td>{{ $s->settlement_no }}</td>
                                <td>{{ $s->display_label }}</td>
                                <td class="text-end">{{ number_format($s->amount, 2) }}</td>
                                <td>{{ $s->payment_mode }}</td>
                                <td><span class="badge bg-label-{{ $s->status === 'Active' ? 'success' : 'secondary' }}">{{ $s->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
