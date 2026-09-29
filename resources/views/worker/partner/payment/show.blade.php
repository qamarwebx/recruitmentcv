@extends('worker.partner.layouts.portal')

@section('title', __('locale.Invoice') . ' ' . $invoice->invoice_no)
@section('page-title', __('locale.Invoice'))

@section('content')
    {{-- Same content/order as the CRM invoice page
    (admin/invoice/show.blade.php, /admin/sales-invoice/show/{id}), read-only:
    partners get Download PDF only - no Send/Edit/Add Payment. --}}
    @php
        $partner = $invoice->partneroffice;
        // Same badge colours and balance as the CRM page.
        $badge = match ($invoice->payment_status) {
            'Paid' => 'is-success',
            'Partially Paid' => 'is-warning',
            default => 'is-danger',
        };
        $balancePayment = (float) $invoice->invoice_amount - (float) $invoice->paid_amount;
        $serviceCharge = explode(',', (string) $invoice->service_charge);
    @endphp

    <div class="wp-invoice-layout wp-fade-in">
        <div class="w-info-card wp-invoice-card">
            <div class="wp-invoice-head">
                <div class="wp-invoice-logo">
                    @if ($partner && $partner->website_logo != '')
                        <img src="{{ asset('admin/assets/images/partner/' . $partner->website_logo) }}" alt="{{ $partner->rec_off_name }}">
                    @endif
                </div>
                <div class="wp-invoice-meta">
                    <h2>{{ __('locale.Invoice No') }}: {{ $invoice->invoice_no }}</h2>
                    <p>
                        <span>{{ __('locale.Date Issued') }}:</span>
                        <strong>{{ $invoice->invoice_date ? date('d-m-Y', strtotime($invoice->invoice_date)) : '---' }}</strong>
                    </p>
                    <p>
                        <span>{{ __('locale.Payment Status') }}:</span>
                        <span class="wp-badge {{ $badge }}">{{ __('locale.' . $invoice->payment_status) }}</span>
                    </p>
                </div>
            </div>

            <div class="wp-invoice-parties">
                <div>
                    <h3>{{ __('locale.Invoice To') }}:</h3>
                    <p>{{ optional($partner)->owner_name }}</p>
                    <p>{{ optional($partner)->rec_off_name }}</p>
                    <p>{{ optional($partner)->info_eng_address }}</p>
                    <p>{{ optional($partner)->office_no }}</p>
                    <p>{{ optional($partner)->primary_email }}</p>
                </div>
                <div>
                    <h3>{{ __('locale.Invoice From') }}:</h3>
                    <p>Qamr International</p>
                    <p>Naseem Mansion, Charnull, Dongri, Mumbai, India 400009</p>
                    <p dir="ltr">91 9004266888 / +91 9004882666</p>
                    <p>jobs@qamrintl.com / hr@qamrintl.com</p>
                </div>
            </div>

            <div class="wp-table-scroll wp-invoice-items">
                <table class="wp-table">
                    <thead>
                        <tr>
                            <th>{{ __('locale.Services') }}</th>
                            <th colspan="2">{{ __('locale.Service Charge') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($empcands as $key => $empcand)
                            <tr>
                                <td>
                                    {{ __('locale.Employer') }}: {{ optional($empcand->emp)->employer_name }}<br>
                                    ({{ __('locale.Visa No') }}: {{ optional($empcand->emp)->visa_no }} | {{ __('locale.ID No') }}: {{ optional($empcand->emp)->id_no }})<br>
                                    {{ __('locale.Candidate') }}: {{ optional($empcand->cand)->cand_name }} ({{ __('locale.Pass No') }}: {{ optional($empcand->cand)->pass_no }})<br>
                                    {{ __('locale.Profession') }}: {{ optional($empcand->proff)->eng_name }}
                                </td>
                                <td colspan="2">{{ $serviceCharge[$key] ?? '' }}</td>
                            </tr>
                        @endforeach

                        <tr class="wp-invoice-totals">
                            <td>
                                <p>
                                    <strong>{{ __('locale.Responsible Person') }}:</strong>
                                    <span>{{ optional($invoice->admin)->name }}</span>
                                </p>
                                <p>{{ __('locale.Thanks for your business') }}</p>
                            </td>
                            <td class="wp-invoice-total-labels">
                                <p>{{ __('locale.Subtotal') }}:</p>
                                <p>{{ __('locale.Paid') }}:</p>
                                <p>{{ __('locale.Total') }}:</p>
                            </td>
                            <td class="wp-invoice-total-values">
                                <p>{{ $invoice->invoice_amount }}</p>
                                <p>@if ($invoice->paid_amount != '' || $invoice->paid_amount != 0) {{ $invoice->paid_amount }} @else 0 @endif</p>
                                <p>{{ $balancePayment }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="wp-invoice-note">
                <strong>{{ __('locale.Note') }}:</strong>
                {{ __('locale.It was a pleasure working with you and your team. We hope you will keep us in mind for future recruitment. Thank You!') }}
            </p>
        </div>

        <div class="w-info-card wp-invoice-actions">
            <a href="{{ route('worker.partner.payment.download', $invoice->id) }}" class="w-btn w-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                {{ __('locale.Download PDF') }}
            </a>
            <a href="{{ route('worker.partner.payment') }}" class="w-btn w-btn-outline">{{ __('locale.Back to Payments') }}</a>
        </div>
    </div>
@endsection
