@extends('worker.partner.layouts.portal')

@section('title', __('locale.Customers'))
@section('page-title', __('locale.Customers'))

@section('content')
    {{-- Plain GET form: search/filter/pagination are all query-string based
    (withQueryString in the controller), no AJAX needed. --}}
    <form method="GET" action="{{ route('worker.partner.customers') }}" class="wp-filter-bar wp-fade-in">
        <div class="wp-filter-search">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="search" class="w-input" placeholder="{{ __('locale.Search by name, email or mobile') }}" value="{{ request('search') }}">
        </div>
        <select name="status" class="w-select">
            <option value="">{{ __('locale.All Statuses') }}</option>
            <option value="1" @selected(request('status') === '1')>{{ __('locale.Active') }}</option>
            <option value="0" @selected(request('status') === '0')>{{ __('locale.Inactive') }}</option>
        </select>
        <button type="submit" class="w-btn w-btn-primary w-btn-sm">{{ __('locale.Search') }}</button>
        <a href="{{ route('worker.partner.customers') }}" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.Reset') }}</a>
    </form>

    <div class="wp-listing-toolbar">
        <p class="wp-result-count">
            <strong>{{ $customers->total() }}</strong> {{ __('locale.Customers') }}
        </p>
        <button type="button" class="w-btn w-btn-primary w-btn-sm" data-customer-add>
            + {{ __('locale.Add Customer') }}
        </button>
    </div>

    @if ($customers->count() > 0)
        <div class="wp-table-wrap wp-fade-in">
            <div class="wp-table-scroll">
                <table class="wp-table">
                    <thead>
                        <tr>
                            <th>{{ __('locale.Full Name') }}</th>
                            <th>{{ __('locale.Mobile Number') }}</th>
                            <th>{{ __('locale.Email') }}</th>
                            <th>{{ __('locale.Status') }}</th>
                            <th>{{ __('locale.Orders') }}</th>
                            <th>{{ __('locale.Registered On') }}</th>
                            <th>{{ __('locale.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>
                                    <a href="{{ route('worker.partner.customers.show', $customer->id) }}"><strong>{{ $customer->name ?: '---' }}</strong></a>
                                </td>
                                <td>
                                    {{ $customer->mobile_no ? '+' . ltrim((string) $customer->country_code, '+') . ' ' . $customer->mobile_no : '---' }}
                                    @if ($customer->mobile_no)
                                        <br>
                                        @if ($customer->mobile_verified_at)
                                            <span class="wp-badge is-success">{{ __('locale.Verified') }}</span>
                                        @else
                                            <span class="wp-badge is-warning">{{ __('locale.Not Verified') }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $customer->email ?: '---' }}</td>
                                <td>
                                    @if ((int) $customer->status === 1)
                                        <span class="wp-badge is-success">{{ __('locale.Active') }}</span>
                                    @else
                                        <span class="wp-badge is-neutral">{{ __('locale.Inactive') }}</span>
                                    @endif
                                </td>
                                <td>{{ (int) $customer->orders_count }}</td>
                                <td>{{ $customer->created_at ? $customer->created_at->format('d M Y') : '---' }}</td>
                                <td>
                                    <div class="wp-customer-actions">
                                        <a href="{{ route('worker.partner.customers.show', $customer->id) }}" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.View Details') }}</a>
                                        @include('worker.partner.customers.row-actions', ['customer' => $customer, 'partnerId' => $partnerId])
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{ $customers->links('worker.partials.pagination') }}
    @else
        <div class="wp-empty">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <h3>{{ __('locale.No customers found') }}</h3>
            <p>{{ __('locale.Customers who register on your website or order from you will appear here.') }}</p>
        </div>
    @endif

    @include('worker.partner.customers.modals', ['countries' => $countries])
@endsection

@section('page-script')
    <script src="{{ asset('worker/js/partner-customers.js') }}?v={{ @filemtime(public_path('worker/js/partner-customers.js')) ?: time() }}"></script>
@endsection
