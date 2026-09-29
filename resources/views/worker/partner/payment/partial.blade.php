{{-- Payment results (initial render + AJAX filter/pagination responses).
Table on desktop, cards on small screens (CSS only, see .wp-payment-*). --}}
@php
    $statusBadge = fn ($status) => [
        'Paid' => 'is-success',
        'Partially Paid' => 'is-warning',
    ][$status] ?? 'is-danger'; // same colours as the CRM invoice page
    $amount = fn ($value) => is_numeric($value) ? number_format((float) $value, (float) $value == (int) $value ? 0 : 2) . ' SAR' : ($value ?: '---');
    $employers = fn ($invoice) => $invoice->employer_name ? str_replace(',', ', ', $invoice->employer_name) : '---';
    $date = fn ($invoice) => $invoice->invoice_date ? \Illuminate\Support\Carbon::parse($invoice->invoice_date)->format('d M Y') : '---';
@endphp

<div class="wp-listing-toolbar">
    <p class="wp-result-count">
        <strong>{{ $invoices->total() }}</strong> {{ $invoices->total() == 1 ? __('locale.invoice') : __('locale.invoices') }}
    </p>
</div>

@if ($invoices->count() > 0)
    <div class="wp-table-wrap wp-fade-in wp-payment-table">
        <div class="wp-table-scroll">
            <table class="wp-table">
                <thead>
                    <tr>
                        <th>{{ __('locale.Employer') }}</th>
                        <th>{{ __('locale.Invoice Number') }}</th>
                        <th>{{ __('locale.Amount') }}</th>
                        <th>{{ __('locale.Status') }}</th>
                        <th>{{ __('locale.Date') }}</th>
                        <th>{{ __('locale.Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td class="wp-payment-employers">{{ $employers($invoice) }}</td>
                            <td><span class="wp-cell-title">{{ $invoice->invoice_no }}</span></td>
                            <td>
                                <span class="wp-cell-title">{{ $amount($invoice->invoice_amount) }}</span>
                                @if ($invoice->payment_status === 'Partially Paid')
                                    <span class="wp-cell-sub">{{ __('locale.Paid') }}: {{ $amount($invoice->paid_amount) }}</span>
                                @endif
                            </td>
                            <td><span class="wp-badge {{ $statusBadge($invoice->payment_status) }}">{{ __('locale.' . $invoice->payment_status) }}</span></td>
                            <td style="white-space:nowrap;">{{ $date($invoice) }}</td>
                            <td>
                                <div class="wp-payment-actions">
                                    <a href="{{ route('worker.partner.payment.show', $invoice->id) }}" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.View Invoice') }}</a>
                                    <a href="{{ route('worker.partner.payment.download', $invoice->id) }}" class="w-btn w-btn-primary w-btn-sm">{{ __('locale.Download PDF') }}</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="wp-order-grid wp-fade-in wp-payment-cards">
        @foreach ($invoices as $invoice)
            <div class="wp-order-card">
                <div class="wp-order-card-head">
                    <div>
                        <span class="wp-order-card-name">{{ $invoice->invoice_no }}</span>
                        <span class="wp-order-card-sub">{{ $date($invoice) }}</span>
                    </div>
                    <span class="wp-badge {{ $statusBadge($invoice->payment_status) }}">{{ __('locale.' . $invoice->payment_status) }}</span>
                </div>
                <div class="wp-order-card-meta">
                    <div>
                        <span>{{ __('locale.Amount') }}</span>
                        <strong>{{ $amount($invoice->invoice_amount) }}</strong>
                    </div>
                    @if ($invoice->payment_status === 'Partially Paid')
                        <div>
                            <span>{{ __('locale.Paid') }}</span>
                            <strong>{{ $amount($invoice->paid_amount) }}</strong>
                        </div>
                    @endif
                    <div class="wp-payment-employers">
                        <span>{{ __('locale.Employer') }}</span>
                        <strong>{{ $employers($invoice) }}</strong>
                    </div>
                </div>
                <div class="wp-order-card-actions">
                    <a href="{{ route('worker.partner.payment.show', $invoice->id) }}" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.View Invoice') }}</a>
                    <a href="{{ route('worker.partner.payment.download', $invoice->id) }}" class="w-btn w-btn-primary w-btn-sm">{{ __('locale.Download PDF') }}</a>
                </div>
            </div>
        @endforeach
    </div>

    {{ $invoices->links('worker.partials.pagination') }}
@else
    <div class="wp-empty">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h5"/></svg>
        <h3>{{ __('locale.No invoices found') }}</h3>
        <p>{{ __('locale.Invoices issued to your account will appear here.') }}</p>
    </div>
@endif
