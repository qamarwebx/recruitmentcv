<div class="wp-listing-toolbar">
    <p class="wp-result-count">
        <strong>{{ $orders->total() }}</strong> {{ $orders->total() == 1 ? __('locale.order available') : __('locale.orders available') }}
    </p>

    <div class="wp-view-switch" data-wp-view-switch role="tablist" aria-label="Order view">
        <button type="button" class="wp-view-btn" data-wp-view-btn="card" aria-pressed="true">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
            {{ __('locale.Card View') }}
        </button>
        <button type="button" class="wp-view-btn" data-wp-view-btn="table" aria-pressed="false">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            {{ __('locale.Table View') }}
        </button>
    </div>
</div>

@if ($orders->count() > 0)
    <div class="wp-order-grid wp-fade-in" data-wp-view-panel="card">
        @foreach ($orders as $order)
            @php
                $candName = (app()->getLocale() === 'ar' && !empty($order->arcand_name)) ? $order->arcand_name : ($order->cand_name ?? '---');
                $profession = (app()->getLocale() === 'ar' && !empty($order->profession_ar)) ? $order->profession_ar : ($order->profession_eng ?? '---');
                $orderStatus = (app()->getLocale() === 'ar' && !empty($order->ar_status)) ? $order->ar_status : ($order->ord_status ?: '---');
                $imagePath = \App\Support\CandidatePhoto::url($order->photo_file);
            @endphp
            <div class="wp-order-card">
                <div class="wp-order-card-head">
                    <img src="{{ $imagePath }}" alt="{{ $candName }}" onerror="this.onerror=null;this.src='{{ \App\Support\CandidatePhoto::defaultUrl() }}';">
                    <div>
                        <span class="wp-order-card-name">{{ $candName }}</span>
                        <span class="wp-order-card-sub">{{ $profession }}</span>
                    </div>
                    <span class="wp-order-card-ref">#{{ $order->reference_no }}</span>
                </div>

                <div class="wp-order-card-badges" data-order-status-cell data-order-id="{{ $order->id }}">
                    @if ((int) $order->booking_status === 2)
                        <span class="wp-badge is-neutral">{{ __('locale.Cancelled') }}</span>
                    @else
                        <span class="wp-badge is-info">{{ $orderStatus }}</span>
                    @endif
                    @if ($order->payment_status)
                        <span class="wp-badge is-success">{{ __('locale.Paid') }}</span>
                    @else
                        <span class="wp-badge is-warning">{{ __('locale.Unpaid') }}</span>
                    @endif
                    @if ($order->visa_status)
                        <span class="wp-badge is-success">{{ __('locale.Confirmed') }}</span>
                    @else
                        <span class="wp-badge is-warning">{{ __('locale.Pending') }}</span>
                    @endif
                </div>

                <div class="wp-order-card-meta">
                    <div>
                        <span>{{ __('locale.Booking Date') }}</span>
                        <strong>{{ $order->booking_date ? \Illuminate\Support\Carbon::parse($order->booking_date)->format('d M Y') : '---' }}</strong>
                    </div>
                    <div>
                        <span>{{ __('locale.Amount') }}</span>
                        <strong>{{ $order->amount ?: '---' }}</strong>
                    </div>
                    <div>
                        <span>{{ __('locale.Employer Name') }}</span>
                        <strong>{{ $order->employer_name ?: '---' }}</strong>
                    </div>
                    <div>
                        <span>{{ __('locale.Mobile Number') }}</span>
                        <strong>{{ $order->mobile_no ?: '---' }}</strong>
                    </div>
                </div>

                <div class="wp-order-card-actions">
                    <a href="{{ route('worker.partner.orders.show', $order->id) }}" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.View Order') }}</a>
                    {{-- Card View only - the Table view's own Actions dropdown
                    (below) has no equivalent button at all, and the
                    single-order show page's own trigger stays labeled
                    "Add Visa" (orders/show.blade.php) - same
                    trigger/route/modal/backend either way, just a
                    Card-View-only label ("Assign Visa") and position. --}}
                    @if ((int) $order->booking_status !== 2)
                        <button type="button" class="w-btn w-btn-outline w-btn-sm"
                            data-order-visa-trigger
                            data-visa-url="{{ route('worker.partner.orders.visa.store', $order->id) }}"
                            data-employer-name="{{ $order->employer_name }}"
                            data-employer-ar-name="{{ $order->employer_ar_name }}"
                            data-visa-no="{{ $order->visa_no }}"
                            data-id-no="{{ $order->id_no }}"
                            data-proff-id="{{ $order->visa_proff_id }}"
                            data-issuing-authority="{{ $order->issuing_authority }}"
                            data-wpcity-id="{{ $order->wpcity_id }}"
                            data-salary="{{ $order->salary }}"
                            data-exp-sal="{{ $order->exp_sal }}">
                            {{ __('locale.Assign Visa') }}
                        </button>
                    @endif
                    @if ($order->candidate_slug)
                        <a href="{{ route('worker.partner.candidates.show', $order->candidate_slug) }}" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.View Candidate') }}</a>
                    @endif
                    {{-- Same cv_execute gate and direct static-asset link as
                    candidates/show.blade.php - no separate download route,
                    reusing that exact condition/URL pattern rather than
                    duplicating CV logic. --}}
                    @if ($order->cv_execute == 1 && $order->cv_execute_file != '')
                        <a href="{{ asset('admin/assets/images/pdf/' . $order->cv_execute_file) }}" target="_blank" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.Download CV') }}</a>
                    @endif
                    @if ((int) $order->booking_status !== 2)
                        <button type="button" class="w-btn w-btn-outline w-btn-sm" style="color:#dc2626;border-color:#fecaca;" data-cancel-order-trigger data-cancel-url="{{ route('worker.partner.orders.cancel', $order->id) }}" data-order-id="{{ $order->id }}">
                            {{ __('locale.Cancel Booking') }}
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="wp-table-wrap wp-fade-in" data-wp-view-panel="table" hidden>
        <div class="wp-table-scroll">
            <table class="wp-table">
                <thead>
                    <tr>
                        <th>{{ __('locale.Order') }}</th>
                        <th>{{ __('locale.Candidate') }}</th>
                        <th>{{ __('locale.Profession') }}</th>
                        <th>{{ __('locale.Booking Date') }}</th>
                        <th>{{ __('locale.Amount') }}</th>
                        <th>{{ __('locale.Employer Name') }}</th>
                        <th>{{ __('locale.Mobile Number') }}</th>
                        <th>{{ __('locale.Order Status') }}</th>
                        <th>{{ __('locale.Payment Status') }}</th>
                        <th>{{ __('locale.Visa Status') }}</th>
                        <th>{{ __('locale.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        @php
                            $candName = (app()->getLocale() === 'ar' && !empty($order->arcand_name)) ? $order->arcand_name : ($order->cand_name ?? '---');
                            $profession = (app()->getLocale() === 'ar' && !empty($order->profession_ar)) ? $order->profession_ar : ($order->profession_eng ?? '---');
                            $orderStatus = (app()->getLocale() === 'ar' && !empty($order->ar_status)) ? $order->ar_status : ($order->ord_status ?: '---');
                            $imagePath = \App\Support\CandidatePhoto::url($order->photo_file);
                        @endphp
                        <tr>
                            <td>
                                <span class="wp-cell-title">#{{ $order->reference_no }}</span>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <img src="{{ $imagePath }}" alt="{{ $candName }}" style="width:34px;height:34px;border-radius:9px;object-fit:cover;flex-shrink:0;" onerror="this.onerror=null;this.src='{{ \App\Support\CandidatePhoto::defaultUrl() }}';">
                                    @if ($order->candidate_slug)
                                        <a href="{{ route('worker.partner.candidates.show', $order->candidate_slug) }}" class="wp-cell-title wp-cell-link">{{ $candName }}</a>
                                    @else
                                        <span class="wp-cell-title">{{ $candName }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $profession }}</td>
                            <td>{{ $order->booking_date ? \Illuminate\Support\Carbon::parse($order->booking_date)->format('d M Y') : '---' }}</td>
                            <td>{{ $order->amount ?: '---' }}</td>
                            <td>{{ $order->employer_name ?: '---' }}</td>
                            <td>{{ $order->mobile_no ?: '---' }}</td>
                            <td data-order-status-cell data-order-id="{{ $order->id }}">
                                @if ((int) $order->booking_status === 2)
                                    <span class="wp-badge is-neutral">{{ __('locale.Cancelled') }}</span>
                                @else
                                    <span class="wp-badge is-info">{{ $orderStatus }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($order->payment_status)
                                    <span class="wp-badge is-success">{{ __('locale.Paid') }}</span>
                                @else
                                    <span class="wp-badge is-warning">{{ __('locale.Unpaid') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($order->visa_status)
                                    <span class="wp-badge is-success">{{ __('locale.Confirmed') }}</span>
                                @else
                                    <span class="wp-badge is-warning">{{ __('locale.Pending') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="wp-dropdown-wrap">
                                    <button type="button" class="wp-icon-btn" data-order-actions-trigger data-order-id="{{ $order->id }}" aria-label="{{ __('locale.Actions') }}">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>
                                    </button>
                                    <div class="wp-dropdown" data-order-actions-menu="{{ $order->id }}">
                                        <a href="{{ route('worker.partner.orders.show', $order->id) }}" class="wp-menu-item">{{ __('locale.View Order') }}</a>
                                        @if ($order->candidate_slug)
                                            <a href="{{ route('worker.partner.candidates.show', $order->candidate_slug) }}" class="wp-menu-item">{{ __('locale.View Candidate') }}</a>
                                        @endif
                                        @if ($order->cv_execute == 1 && $order->cv_execute_file != '')
                                            <a href="{{ asset('admin/assets/images/pdf/' . $order->cv_execute_file) }}" target="_blank" class="wp-menu-item">{{ __('locale.Download CV') }}</a>
                                        @endif
                                        @if ((int) $order->booking_status !== 2)
                                            <button type="button" class="wp-menu-item is-danger" data-cancel-order-trigger data-cancel-url="{{ route('worker.partner.orders.cancel', $order->id) }}" data-order-id="{{ $order->id }}">
                                                {{ __('locale.Cancel Booking') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{ $orders->links('worker.partials.pagination') }}
@else
    <div class="wp-empty">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
        <h3>{{ __('locale.No orders found') }}</h3>
        <p>{{ __('locale.Orders linked to your account will appear here.') }}</p>
    </div>
@endif
