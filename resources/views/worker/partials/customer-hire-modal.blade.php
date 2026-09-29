{{-- Customer Hire Now (web guard) - posts to CustomerHireController, which
re-checks everything and decides the partner server-side. $hire comes from
WorkerPageController::resumeDetails(). --}}
@php
    $isAr = app()->getLocale() === 'ar';
    $hirePartnerName = null;
    if ($hire['partner']) {
        $hirePartnerName = ($isAr && !empty($hire['partner']->rec_office_arname))
            ? $hire['partner']->rec_office_arname
            : ($hire['partner']->portal_rec_off_name ?: $hire['partner']->rec_off_name);
    }
@endphp
<div class="w-modal-backdrop" data-customer-hire-backdrop></div>
<div class="w-modal" data-customer-hire-modal role="dialog" aria-modal="true" aria-labelledby="customerHireTitle"
    data-hire-url="{{ route('worker.account.hire', $post->slug_text) }}"
    data-error-text="{{ __('locale.Something went wrong. Please try again.') }}">
    <div class="w-modal-dialog">
        <button type="button" class="w-modal-close" data-customer-hire-close aria-label="{{ __('locale.Close') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        <div class="w-modal-body">
            <h3 id="customerHireTitle" class="w-modal-title">{{ __('locale.Hire Now') }}</h3>
            <p class="w-modal-subtitle">{{ $post->display_name }}</p>

            <div class="w-form-alert" data-customer-hire-alert hidden></div>

            @if ($hire['existingRef'])
                <div class="w-form-alert is-success">{{ __('locale.You already have an order for this candidate.') }} #{{ $hire['existingRef'] }}</div>
                <a href="{{ route('worker.account.orders') }}" class="w-btn w-btn-primary w-btn-block">{{ __('locale.View My Orders') }}</a>
            @elseif (!$hire['canHire'])
                <div class="w-form-alert">{{ __('locale.Please verify your mobile number first.') }}</div>
                <a href="{{ route('worker.account.profile') }}" class="w-btn w-btn-primary w-btn-block">{{ __('locale.Personal Information') }}</a>
            @elseif ($hire['limitReached'])
                <div class="w-form-alert">{{ __('locale.You have reached the maximum number of active orders.') }}</div>
                <a href="{{ route('worker.account.orders') }}" class="w-btn w-btn-primary w-btn-block">{{ __('locale.View My Orders') }}</a>
            @else
                <div data-customer-hire-form>
                    <div class="w-form-row">
                        <label class="w-form-check">
                            <input type="checkbox" data-customer-hire-salary>
                            {{ __('locale.Are you offering a salary of') }} {{ $post->exp_sal ?: '---' }}?
                        </label>
                    </div>

                    <div class="w-form-row">
                        <label class="w-form-label" for="customerHireCity">{{ __('locale.In which city will the worker be working?') }}</label>
                        <select class="w-select" id="customerHireCity" data-customer-hire-city>
                            <option value="">{{ __('locale.Select') }}</option>
                            @foreach ($hire['cities'] as $city)
                                <option value="{{ $city->id }}">{{ $city->display_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-form-row">
                        <label class="w-form-label" for="customerHireEmbassy">{{ __('locale.Embassy') }}</label>
                        <select class="w-select" id="customerHireEmbassy" data-customer-hire-embassy>
                            @if ($hire['embassies']->count() !== 1)
                                <option value="">{{ __('locale.Select') }}</option>
                            @endif
                            @foreach ($hire['embassies'] as $embassy)
                                <option value="{{ $embassy->id }}">{{ $embassy->embassy }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-form-row">
                        <label class="w-form-label" for="customerHireOffice">{{ __('locale.Recruitment Office') }}</label>
                        @if ($hirePartnerName)
                            {{-- Decided server-side (this website's partner, or the
                            office the main site assigns) - display only. --}}
                            <div class="w-hire-office">
                                <strong>{{ $hirePartnerName }}</strong>
                                @if (!empty($hire['partner']->portal_add_disp_only))
                                    <span>{{ $hire['partner']->portal_add_disp_only }}</span>
                                @endif
                            </div>
                        @else
                            <select class="w-select" id="customerHireOffice" data-customer-hire-office>
                                <option value="">{{ __('locale.Select') }}</option>
                                @foreach ($hire['offices'] as $office)
                                    <option value="{{ $office->id }}">{{ $office->portal_rec_off_name ?: $office->rec_off_name }}{{ $office->portal_add_disp_only ? ' — ' . $office->portal_add_disp_only : '' }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <button type="button" class="w-btn w-btn-primary w-btn-block" data-customer-hire-submit disabled
                        data-default-text="{{ __('locale.Confirm Order') }}"
                        data-loading-text="{{ __('locale.Submitting…') }}">
                        {{ __('locale.Confirm Order') }}
                    </button>
                </div>

                <div data-customer-hire-success hidden>
                    <div class="w-form-alert is-success" data-customer-hire-success-message></div>
                    <a href="{{ route('worker.account.orders') }}" class="w-btn w-btn-primary w-btn-block">{{ __('locale.View My Orders') }}</a>
                </div>
            @endif
        </div>
    </div>
</div>
