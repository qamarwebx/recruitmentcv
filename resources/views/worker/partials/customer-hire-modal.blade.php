{{-- Customer Hire Now (web guard) - posts to CustomerHireController, which
re-checks everything and decides the partner (recruitment office) and the
embassy server-side - neither is asked here. $hire comes from WorkerPageController::resumeDetails(). --}}
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
            @else
                @php $needsVerify = !$hire['canHire']; @endphp
                @if ($needsVerify)
                    {{-- Unverified mobile: verify it right here with the existing
                    Profile > Change Mobile endpoints (DashboardController
                    getMobileOTPandUpdate / applyVerifiedMobile - same
                    duplicate/ownership rules), then the order step below opens
                    for this same candidate (customer-hire.js). --}}
                    <div data-customer-hire-verify
                        data-send-url="{{ route('booking.profile.otpandupdate') }}"
                        data-verify-url="{{ app()->getLocale() === 'ar' ? route('booking.profile.arvalidate-otp2') : route('booking.profile.validate-otp2') }}"
                        data-err-mobile="{{ __('locale.Please enter a valid mobile number.') }}"
                        data-err-not-sent="{{ __('locale.We could not send the OTP. Please try again in a moment.') }}"
                        data-err-technical="{{ __('locale.A technical error occurred. Please try again shortly.') }}"
                        data-err-otp="{{ __('locale.Incorrect OTP. Please try again.') }}"
                        data-sending="{{ __('locale.Sending…') }}"
                        data-verifying="{{ __('locale.Verifying…') }}"
                        data-resend-in="{{ __('locale.Resend in :seconds s') }}"
                        data-verified="{{ __('locale.Mobile number verified. You can continue your order.') }}">
                        <div class="w-form-alert is-error">{{ __('locale.Please verify your mobile number first.') }}</div>
                        <div class="w-form-alert" data-customer-hire-verify-alert hidden></div>

                        <div data-customer-hire-verify-step="enter">
                            <div class="w-form-row">
                                <label class="w-form-label" for="customerHireMobile">{{ __('locale.Mobile Number') }}</label>
                                <div class="w-form-phone-row">
                                    @include('worker.partials.country-code-select', [
                                        'attr' => 'data-customer-hire-country',
                                        'selected' => $hire['countryCode'] ?? null,
                                    ])
                                    <input type="tel" class="w-input" id="customerHireMobile" data-customer-hire-mobile inputmode="numeric" maxlength="15" dir="ltr" value="{{ $hire['mobile'] ?? '' }}" aria-describedby="customerHireMobileError">
                                </div>
                                {{-- Number step errors (invalid / already used / not sent). --}}
                                <div class="w-form-error" id="customerHireMobileError" data-customer-hire-mobile-error role="alert" hidden></div>
                            </div>
                            <button type="button" class="w-btn w-btn-primary w-btn-block" data-customer-hire-send data-default-text="{{ __('locale.Send OTP') }}">{{ __('locale.Send OTP') }}</button>
                        </div>

                        <div data-customer-hire-verify-step="code" hidden>
                            {{-- "Enter the OTP sent to your WhatsApp number ending in 3401." (otp-destination.js). --}}
                            @include('worker.partials.otp-destination-script')
                            <p class="w-form-hint" data-customer-hire-target>{{ __('locale.Enter the OTP sent to your WhatsApp number.') }}</p>
                            <div class="w-form-row">
                                <input type="text" class="w-input w-input-otp" maxlength="4" inputmode="numeric" autocomplete="one-time-code" placeholder="••••" data-customer-hire-otp aria-label="OTP">
                            </div>
                            <button type="button" class="w-btn w-btn-primary w-btn-block" data-customer-hire-verify-btn data-default-text="{{ __('locale.Verify & Continue') }}">{{ __('locale.Verify & Continue') }}</button>
                            <div class="w-hire-verify-links" style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:12px;">
                                <button type="button" class="w-auth-forgot-link" data-customer-hire-change>{{ __('locale.Change Number') }}</button>
                                <span style="color:var(--w-ink-500);font-size:0.85rem;">{{ __("locale.Didn't receive the OTP?") }}</span>
                                <button type="button" class="w-auth-forgot-link" data-customer-hire-resend data-default-text="{{ __('locale.Resend OTP') }}">{{ __('locale.Resend OTP') }}</button>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($hire['limitReached'])
                <div data-customer-hire-after-verify @if ($needsVerify) hidden @endif>
                    <div class="w-form-alert">{{ __('locale.You have reached the maximum number of active orders.') }}</div>
                    <a href="{{ route('worker.account.orders') }}" class="w-btn w-btn-primary w-btn-block">{{ __('locale.View My Orders') }}</a>
                </div>
                @else
                <div data-customer-hire-form data-customer-hire-after-verify @if ($needsVerify) hidden @endif>
                    <div class="w-form-row">
                        <label class="w-form-check">
                            <input type="checkbox" data-customer-hire-salary>
                            {{ __('locale.Are you offering a salary of') }} {{ $post->display_expected_salary ?: '---' }}?
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
            @endif
        </div>
    </div>
</div>
