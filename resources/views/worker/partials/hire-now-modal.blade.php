@php
    $__hireSalary = $post->exp_sal ?: '---';
@endphp
<div class="w-modal-backdrop" data-hire-now-backdrop></div>
<div class="w-modal" data-hire-now-modal role="dialog" aria-modal="true" aria-labelledby="hireNowTitle">
    <div class="w-modal-dialog">
        <button type="button" class="w-modal-close" data-hire-now-close aria-label="Close">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        <div class="w-modal-body">
            <h3 id="hireNowTitle" class="w-modal-title">{{ __('locale.Hire Now') }}</h3>
            <p class="w-modal-subtitle">{{ $post->display_name }}</p>

            <div class="w-form-alert" data-hire-now-alert hidden></div>

            <div data-hire-now-form-wrap>
                <div class="w-form-row">
                    <label class="w-form-check">
                        <input type="checkbox" id="hireAgreedSalary" data-hire-now-salary-check>
                        {{ __('locale.Are you offering a salary of') }} {{ $__hireSalary }}?
                    </label>
                </div>

                <div class="w-form-row">
                    <label class="w-form-label" for="hireWorklocation">{{ __('locale.In which city will the worker be working?') }}</label>
                    <select class="w-select select2" id="hireWorklocation" data-hire-now-worklocation data-placeholder="{{ __('locale.Select') }}">
                        <option value="">{{ __('locale.Select') }}</option>
                        @foreach ($hireWorkLocations as $location)
                            <option value="{{ $location->id }}">{{ $location->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-form-row">
                    <label class="w-form-label" for="hireEmployerName">
                        {{ __('locale.Employer Name') }}
                        <span class="w-form-optional-tag">({{ __('locale.Optional') }})</span>
                    </label>
                    <input type="text" class="w-input" id="hireEmployerName" data-hire-now-employer-name maxlength="150" placeholder="{{ __('locale.Employer Name') }}">
                </div>

                <div class="w-form-row">
                    <label class="w-form-label" for="hireMobileNumber">
                        {{ __('locale.Mobile Number') }}
                        <span class="w-form-optional-tag">({{ __('locale.Optional') }})</span>
                    </label>
                    <input type="tel" class="w-input" id="hireMobileNumber" data-hire-now-mobile-number maxlength="20" placeholder="{{ __('locale.Mobile Number') }}">
                </div>

                <button type="button" class="w-btn w-btn-primary w-btn-block" data-hire-now-submit disabled
                    data-default-text="{{ __('locale.Confirm Order') }}"
                    data-loading-text="{{ __('locale.Submitting…') }}">
                    {{ __('locale.Confirm Order') }}
                </button>
            </div>

            <div data-hire-now-success hidden>
                <div class="w-form-alert is-success" data-hire-now-success-message></div>
                <a href="{{ route('worker.partner.orders') }}" class="w-btn w-btn-primary w-btn-block">
                    {{ __('locale.View My Orders') }}
                </a>
            </div>
        </div>
    </div>
</div>
