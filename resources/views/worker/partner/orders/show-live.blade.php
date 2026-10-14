{{-- The live part of the Partner order detail page (PartnerPortalController::orderShow() renders it
inside [data-order-live]; orderLive() re-renders it when the persisted CRM / order data changed -
order-live.js swaps it in). Header status + actions, the CRM workflow stepper, stage documents,
order details, activity timeline and the side cards - all from the database on every render.
Needs $order (PartnerPortalController::partnerOrder()) and $partner. --}}
@php
    $isAr = app()->getLocale() === 'ar';
    $candName = ($isAr && !empty($order->arcand_name)) ? $order->arcand_name : ($order->cand_name ?? '---');
    $profession = ($isAr && !empty($order->profession_ar)) ? $order->profession_ar : ($order->profession_eng ?? '---');
    $isCancelled = (int) $order->booking_status === 2;
    $orderStatus = $isCancelled
        ? __('locale.Cancelled')
        : (($isAr && !empty($order->ar_status)) ? $order->ar_status : ($order->ord_status ?: '---'));
    $workLocation = ($isAr && !empty($order->worklocation_ar)) ? $order->worklocation_ar : ($order->worklocation_eng ?: '---');
    $employerName = ($isAr && !empty($order->employer_ar_name)) ? $order->employer_ar_name : ($order->employer_name ?: null);
    $pipeline = \App\Support\OrderPipeline::for($order);
    $bookingDate = $order->booking_date ? \Illuminate\Support\Carbon::parse($order->booking_date) : null;
    $canVisa = \App\Support\PartnerTeam::allowsSection('orders', 'assign_visa');
    $canCancel = \App\Support\PartnerTeam::allowsSection('orders', 'cancel_booking');
    $visaAttrs = 'data-order-visa-trigger data-visa-url="' . e(route('worker.partner.orders.visa.store', $order->id)) . '" data-employer-name="' . e($order->employer_name) . '" data-employer-ar-name="' . e($order->employer_ar_name)
        . '" data-visa-no="' . e($order->visa_no) . '" data-id-no="' . e($order->id_no) . '" data-proff-id="' . e($order->visa_proff_id) . '" data-issuing-authority="' . e($order->issuing_authority)
        . '" data-wpcity-id="' . e($order->wpcity_id) . '" data-salary="' . e($order->salary) . '" data-exp-sal="' . e($order->exp_sal) . '"';
    // CRM workflow stages (App\Support\OrderPipeline) + the documents of completed
    // stages and the booked flight - from this order's own linked workflow only.
    $stageDocs = \App\Support\OrderPipeline::documents($order, $pipeline);
    $flight = \App\Support\OrderPipeline::flight($pipeline['workflow']);
    $isDone = fn ($key) => ($pipeline['stages'][$key]['state'] ?? '') === 'done';
    // CRM Payment Details -> Amount (the payment record), else the order amount - OrderPipeline::amount().
    $orderAmount = \App\Support\OrderPipeline::amountLabel($order); // "100 SAR"
    // When each completed stage happened (persisted CRM data only - OrderPipeline::stageTimes()).
    $stageTimes = \App\Support\OrderPipeline::stageTimes($order, $pipeline);
    $when = fn ($key) => !empty($stageTimes[$key]) ? \Illuminate\Support\Carbon::parse($stageTimes[$key])->format('d M Y, H:i') : null;
    // Stepper sub-labels - persisted data only.
    $stepNotes = [
        'placed' => $bookingDate ? $bookingDate->format('d M Y') : '',
        'confirmed' => $isDone('confirmed') ? ($when('confirmed') ?: __('locale.Confirmed')) : ($isCancelled ? '' : __('locale.Pending')),
        'visa_authorization' => $isDone('visa_authorization') ? ($order->visa_no ? __('locale.Visa Number') . ' ' . $order->visa_no : __('locale.Authorized')) : ($isCancelled ? '' : __('locale.Not assigned')),
        'visa_stamped' => $isDone('visa_stamped') ? ($when('visa_stamped') ?: __('locale.Stamped')) : '',
        'immigration_cleared' => $isDone('immigration_cleared') ? ($when('immigration_cleared') ?: __('locale.Cleared')) : '',
        'ticket_confirmed' => $isDone('ticket_confirmed') ? ($when('ticket_confirmed') ?: __('locale.Confirmed')) : '',
        'deployed' => $isDone('deployed') ? ($when('deployed') ?: __('locale.Deployed')) : '',
    ];
@endphp

<div class="wp-detail-head wp-fade-in">
    <a href="{{ route('worker.partner.orders') }}" class="wp-icon-btn wp-back-btn" aria-label="{{ __('locale.Back to Orders') }}" title="{{ __('locale.Back to Orders') }}">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <div class="wp-detail-status" data-order-status-cell data-order-id="{{ $order->id }}">
        <span class="wp-badge {{ $isCancelled ? 'is-danger' : 'is-info' }}">{{ $orderStatus }}</span>
    </div>
    <div class="wp-detail-actions">
        <button type="button" class="w-btn w-btn-outline" onclick="window.print()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            {{ __('locale.Print') }}
        </button>
        @if (!$isCancelled && $canVisa)
            <button type="button" class="w-btn w-btn-accent" {!! $visaAttrs !!}>{{ $order->visa_status ? __('locale.Edit Visa') : __('locale.Assign Visa') }}</button>
        @endif
        @if (!$isCancelled && $canCancel)
            <button type="button" class="w-btn w-btn-danger" data-cancel-order-trigger data-cancel-url="{{ route('worker.partner.orders.cancel', $order->id) }}" data-order-id="{{ $order->id }}">
                {{ __('locale.Cancel Booking') }}
            </button>
        @endif
    </div>
</div>

{{-- Stepper: the CRM booking workflow - Order Placed > Order Confirmed > Visa Authorization >
Visa Stamped > Immigration Cleared > Ticket Confirmed > Deployed (App\Support\OrderPipeline). --}}
<section class="w-info-card wp-stepper is-long wp-fade-in" aria-label="{{ __('locale.Deployment pipeline') }}" style="--wp-stages: {{ $pipeline['total'] }};">
    <ol>
        @foreach ($pipeline['stages'] as $key => $stage)
            <li class="is-{{ $stage['state'] }}">
                <span class="wp-stepper-dot">
                    @if ($stage['state'] === 'done')
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                    @else
                        {{ $loop->iteration }}
                    @endif
                </span>
                <span class="wp-stepper-text">
                    <strong>{{ $stage['label'] }}</strong>
                    <span>{{ $stepNotes[$key] ?? '' }}</span>
                    @if (!empty($stageDocs[$key]))
                        <a href="#stage-documents-{{ $key }}" class="wp-stepper-docs">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/></svg>
                            {{ trans_choice('locale.:count document|:count documents', count($stageDocs[$key]), ['count' => count($stageDocs[$key])]) }}
                        </a>
                    @endif
                </span>
            </li>
        @endforeach
    </ol>
</section>

<div class="wp-detail-grid">
    <div class="wp-detail-main">
        {{-- Candidate hero --}}
        <section class="wp-hero wp-fade-in">
            <div class="wp-hero-top">
                <span class="wp-avatar is-xl">
                    @if (!empty($order->photo_file))
                        <img src="{{ \App\Support\CandidatePhoto::url($order->photo_file) }}" alt="" onerror="this.remove();">
                    @endif
                    <span class="wp-avatar-initials">{{ \App\Support\OrderPipeline::initials($order->cand_name) }}</span>
                </span>
                <div class="wp-hero-text">
                    <div class="wp-hero-name">
                        <h2>{{ $candName }}</h2>
                        <span class="wp-pill-verified is-on-dark">{{ __('locale.Verified') }}</span>
                    </div>
                    <p>{{ $profession }}@if ($order->candidate_ref) · {{ __('locale.Ref') }} <strong>{{ $order->candidate_ref }}</strong>@endif</p>
                </div>
            </div>
            <dl class="wp-hero-strip">
                <div><dt>{{ __('locale.Profession') }}</dt><dd>{{ $profession }}</dd></div>
                <div><dt>{{ __('locale.Expected Salary') }}</dt><dd>{{ $order->exp_sal ?: '---' }}</dd></div>
                <div><dt>{{ __('locale.Work Location') }}</dt><dd>{{ $workLocation }}</dd></div>
                <div><dt>{{ __('locale.Recruitment Office') }}</dt><dd>{{ $partner->rec_off_name ?: $partner->owner_name ?: '---' }}</dd></div>
            </dl>
        </section>

        {{-- Order details --}}
        <section class="w-info-card wp-fade-in">
            <h2>{{ __('locale.Order details') }}</h2>
            <dl class="wp-detail-fields">
                <div><dt>{{ __('locale.Order reference') }}</dt><dd>#{{ $order->reference_no }}</dd></div>
                <div><dt>{{ __('locale.Booking Date') }}</dt><dd>{{ $bookingDate ? $bookingDate->format('d M Y, H:i') : '---' }}</dd></div>
                <div>
                    <dt>{{ __('locale.Employer') }}</dt>
                    <dd>
                        @if ($employerName)
                            {{ $employerName }}
                        @elseif (!$isCancelled && $canVisa)
                            <button type="button" class="wp-link-btn" {!! $visaAttrs !!}>{{ __('locale.Assign employer') }}</button>
                        @else
                            <span class="is-muted">{{ __('locale.Not assigned') }}</span>
                        @endif
                    </dd>
                </div>
                <div><dt>{{ __('locale.Employer mobile') }}</dt><dd class="{{ $order->mobile_no ? '' : 'is-muted' }}">{{ $order->mobile_no ?: __('locale.Not provided') }}</dd></div>
                <div><dt>{{ __('locale.Work Location') }}</dt><dd>{{ $workLocation }}</dd></div>
                <div><dt>{{ __('locale.Visa Number') }}</dt><dd class="{{ $order->visa_no ? '' : 'is-muted' }}">{{ $order->visa_no ?: __('locale.Not assigned yet') }}</dd></div>
                <div><dt>{{ __('locale.Issuing Authority') }}</dt><dd class="{{ $order->issuing_authority ? '' : 'is-muted' }}">{{ $order->issuing_authority ?: '---' }}</dd></div>
                <div><dt>{{ __('locale.Amount') }}</dt><dd class="{{ $orderAmount !== null ? '' : 'is-muted' }}">@if ($orderAmount !== null)<span dir="ltr">{{ $orderAmount }}</span>@else{{ __('locale.Not set') }}@endif</dd></div>
                <div><dt>{{ __('locale.Payment Status') }}</dt><dd>{{ $order->payment_status ? __('locale.Paid') : __('locale.Unpaid') }}</dd></div>
                <div><dt>{{ __('locale.Visa Status') }}</dt><dd>{{ $order->visa_status ? __('locale.Visa assigned') : __('locale.Visa pending') }}</dd></div>
            </dl>
        </section>

        {{-- Documents of completed stages - the CRM Candidate Status workflow files of
        this order's own candidate, opened through the partner-only route
        (PartnerPortalController::orderDocument), never a public file URL. Shown once
        the order's workflow is linked to this partner (OrderPipeline::workflow()). --}}
        @if ($pipeline['workflow'])
            <section class="w-info-card wp-fade-in wp-stage-docs" id="stage-documents">
                <h2>{{ __('locale.Stage documents') }}</h2>
                @if (!$stageDocs && !($isDone('ticket_confirmed') && $flight))
                    <p class="wp-stage-docs-empty">{{ __('locale.No documents for the completed stages yet.') }}</p>
                @endif
                @foreach ($pipeline['stages'] as $key => $stage)
                    @php $docs = $stageDocs[$key] ?? []; $showFlight = $key === 'ticket_confirmed' && $stage['state'] === 'done' && $flight; @endphp
                    @if ($docs || $showFlight)
                        <div class="wp-stage-docs-group" id="stage-documents-{{ $key }}">
                            <h3>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                                {{ $stage['label'] }}
                            </h3>
                            @if ($showFlight)
                                <p class="wp-stage-flight">
                                    {{ __('locale.Flight') }}:
                                    <strong>{{ $flight['from'] ?: '---' }} → {{ $flight['to'] ?: '---' }}</strong>
                                    @if (!empty($flight['at']))· {{ \Illuminate\Support\Carbon::parse($flight['at'])->format('d M Y, H:i') }}@endif
                                </p>
                            @endif
                            @foreach ($docs as $doc)
                                <div class="wp-doc-card">
                                    <span class="wp-doc-icon is-{{ in_array($doc['ext'], ['jpg', 'jpeg', 'png', 'webp'], true) ? 'image' : ($doc['ext'] === 'pdf' ? 'pdf' : 'file') }}" aria-hidden="true">{{ strtoupper($doc['ext'] ?: 'FILE') }}</span>
                                    <span class="wp-doc-text">
                                        <strong>{{ $doc['label'] }}</strong>
                                        <span class="wp-doc-name" title="{{ $doc['name'] }}" dir="ltr">{{ $doc['name'] }}</span>
                                        @if ($doc['uploaded_at'])<span class="wp-doc-date">{{ __('locale.Uploaded') }} {{ \Illuminate\Support\Carbon::parse($doc['uploaded_at'])->format('d M Y') }}</span>@endif
                                    </span>
                                    <span class="wp-doc-actions">
                                        @if (in_array($doc['ext'], ['pdf', 'jpg', 'jpeg', 'png', 'webp'], true))
                                            <a href="{{ route('worker.partner.orders.show.document', [$order->id, $doc['id']]) }}" target="_blank" rel="noopener" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.View') }}</a>
                                        @endif
                                        <a href="{{ route('worker.partner.orders.show.document', [$order->id, $doc['id'], 'download' => 1]) }}" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.Download') }}</a>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </section>
        @endif

        {{-- Activity timeline - from the order's own dates and flags. --}}
        <section class="w-info-card wp-fade-in">
            <h2>{{ __('locale.Activity timeline') }}</h2>
            <ol class="wp-timeline">
                <li class="is-info">
                    <strong>{{ __('locale.Order created') }}</strong>
                    <span>{{ $bookingDate ? $bookingDate->format('d M Y, H:i') : '---' }}</span>
                </li>
                @if (!empty($order->partner_order_confirm_date))
                    <li class="is-info">
                        <strong>{{ __('locale.Order confirmed') }}</strong>
                        <span>{{ \Illuminate\Support\Carbon::parse($order->partner_order_confirm_date)->format('d M Y, H:i') }}</span>
                    </li>
                @endif
                @if ($order->payment_status)
                    <li class="is-success"><strong>{{ __('locale.Payment received') }}</strong></li>
                @endif
                @if ($order->visa_status)
                    <li class="is-success">
                        <strong>{{ __('locale.Visa assigned') }}</strong>
                        @if ($order->visa_no)<span>{{ __('locale.Visa Number') }}: {{ $order->visa_no }}</span>@endif
                        @if ($when('visa_authorization'))<span>{{ $when('visa_authorization') }}</span>@endif
                    </li>
                @endif
                {{-- CRM Candidate Status workflow stages of this order (linked workflow only), with their times. --}}
                @foreach (['visa_stamped', 'immigration_cleared', 'ticket_confirmed', 'deployed'] as $workflowStage)
                    @if ($isDone($workflowStage))
                        <li class="is-success">
                            <strong>{{ $pipeline['stages'][$workflowStage]['label'] }}</strong>
                            @if ($when($workflowStage))<span>{{ $when($workflowStage) }}</span>@endif
                            @if ($workflowStage === 'ticket_confirmed' && $flight)<span>{{ __('locale.Flight') }}: {{ $flight['from'] ?: '---' }} → {{ $flight['to'] ?: '---' }}@if (!empty($flight['at'])) · {{ \Illuminate\Support\Carbon::parse($flight['at'])->format('d M Y, H:i') }}@endif</span>@endif
                        </li>
                    @endif
                @endforeach
                @if ($isCancelled)
                    <li class="is-critical">
                        <strong>{{ __('locale.Booking cancelled') }}</strong>
                        @if (!empty($order->booking_cancelled_at))<span>{{ \Illuminate\Support\Carbon::parse($order->booking_cancelled_at)->format('d M Y, H:i') }}</span>@endif
                    </li>
                @elseif (!$order->visa_status || !$order->payment_status)
                    <li class="is-waiting">
                        <strong>{{ !$order->visa_status && !$order->payment_status ? __('locale.Waiting: payment & visa') : (!$order->visa_status ? __('locale.Waiting: visa') : __('locale.Waiting: payment')) }}</strong>
                        <span>{{ !$order->visa_status ? __('locale.Assign a visa to move this order forward.') : __('locale.Payment is still pending.') }}</span>
                    </li>
                @endif
            </ol>
        </section>
    </div>

    <aside class="wp-detail-side">
        @if (!$isCancelled && (!$order->visa_status || !$order->payment_status))
            <section class="wp-next-action wp-fade-in">
                <span class="wp-next-action-label">{{ __('locale.Next action') }}</span>
                <h3>{{ !$order->visa_status ? __('locale.Assign visa') : __('locale.Payment pending') }}</h3>
                <p>
                    @if (!$order->visa_status)
                        {{ __('locale.Assign a visa to move this order forward.') }}
                    @endif
                    @if (!$order->payment_status)
                        {{ __('locale.Payment is still pending.') }}
                    @endif
                </p>
                @if (!$order->visa_status && $canVisa)
                    <button type="button" class="w-btn w-btn-accent w-btn-block" {!! $visaAttrs !!}>{{ __('locale.Assign visa') }}</button>
                @endif
            </section>
        @endif

        <section class="w-info-card wp-side-card wp-fade-in">
            <div class="wp-side-head">
                <h2>{{ __('locale.Payment') }}</h2>
                <span class="wp-badge {{ $order->payment_status ? 'is-success' : 'is-warning' }}">{{ $order->payment_status ? __('locale.Paid') : __('locale.Unpaid') }}</span>
            </div>
            <div class="wp-side-row">
                <span>{{ __('locale.Order amount') }}</span>
                <strong>@if ($orderAmount !== null)<span dir="ltr">{{ $orderAmount }}</span>@else—@endif</strong>
            </div>
        </section>

        <section class="w-info-card wp-side-card wp-fade-in">
            <div class="wp-side-head">
                <h2>{{ __('locale.Employer contact') }}</h2>
            </div>
            @if ($employerName)
                <div class="wp-contact">
                    <span class="wp-contact-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></span>
                    <div>
                        <strong>{{ $employerName }}</strong>
                        <span>{{ $order->mobile_no ?: __('locale.Not provided') }}</span>
                    </div>
                </div>
                @if ($order->mobile_no && preg_replace('/\D+/', '', $order->mobile_no) !== '')
                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $order->mobile_no) }}" target="_blank" rel="noopener" class="w-btn w-btn-outline w-btn-block wp-wa-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm5.2 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.6-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.8s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.2.1.4 0 .5l-.3.5-.4.4c-.1.1-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1 2.1 1.4 2.4 1.5.3.1.5.1.6-.1l.9-1c.2-.3.4-.2.6-.1l1.9.9c.3.1.5.2.5.3.1.2.1.7-.1 1.3Z"/></svg>
                        WhatsApp
                    </a>
                @endif
            @else
                <div class="wp-contact is-empty">
                    <span class="wp-contact-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></span>
                    <div>
                        <strong>{{ __('locale.No employer linked') }}</strong>
                        <span>{{ __('locale.The employer is added with the visa.') }}</span>
                    </div>
                </div>
                @if (!$isCancelled && $canVisa)
                    <button type="button" class="w-btn w-btn-outline w-btn-block" {!! $visaAttrs !!}>{{ __('locale.Assign employer') }}</button>
                @endif
            @endif
        </section>
    </aside>
</div>
