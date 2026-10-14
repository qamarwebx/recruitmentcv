{{-- Partner Website -> SMTP: the signed-in partner's SMTP list in sending
     order (App\Support\SmtpSettings), re-rendered by every SMTP action
     (PartnerPortalController::smtpResponse). $smtps: this partner's only.
     "Add SMTP With Google" (PartnerSmtpGoogleController): a connected but
     not yet saved Google account (GoogleSmtpPending - its email only, the
     token stays on the server) shows its prefilled form below the list. --}}
@php
    $primaryId = \App\Support\SmtpSettings::primaryId($smtps);
    $max = \App\Models\WebsiteSmtpSetting::MAX_PER_WEBSITE;
    $encryptions = \App\Models\WebsiteSmtpSetting::ENCRYPTIONS;
    $smtpPartner = \Illuminate\Support\Facades\Auth::guard('partner')->user();
    $googlePending = $smtpPartner ? \App\Support\GoogleSmtpPending::forView((int) $smtpPartner->id) : null;
    $googleDefaults = \App\Support\GoogleSmtpOAuth::smtpDefaults();
    $googleReady = \App\Support\GoogleSmtpOAuth::canRefresh();
@endphp
<div class="wp-smtp-current">
    <span class="w-form-label">{{ __('locale.Currently sending with') }}</span>
    @php $primary = $primaryId ? $smtps->firstWhere('id', $primaryId) : null; @endphp
    <span class="wp-badge {{ $primary ? 'is-success' : 'is-neutral' }}">
        {{ $primary ? __('locale.Your SMTP') . ' #' . ($smtps->search(fn ($row) => $row->id === $primary->id) + 1) : __('locale.Default RecruitmentCV email settings') }}
    </span>
</div>

@forelse ($smtps as $i => $smtp)
    @php
        $position = $i + 1;
        $isPrimary = $smtp->id === $primaryId;
        try { $usable = $smtp->isUsable(); } catch (\Throwable $e) { $usable = false; }
    @endphp
    <div class="wp-wc-section wp-smtp-item is-open {{ $isPrimary ? 'is-primary' : '' }}" data-smtp-item>
        <button type="button" class="wp-wc-section-header" data-smtp-toggle aria-expanded="true">
            <span class="wp-wc-section-title">
                SMTP #{{ $position }}@if ($smtp->name) <span class="wp-smtp-name">— {{ $smtp->name }}</span>@endif
            </span>
            <span class="wp-smtp-badges">
                @if ($smtp->isGoogle())<span class="wp-badge is-neutral wp-smtp-google-badge">@include('worker.partner.partials.google-g', ['size' => 14]) Google</span>@endif
                @if ($isPrimary)<span class="wp-badge is-success">{{ __('locale.Primary') }}</span>@endif
                <span class="wp-badge {{ $smtp->status ? 'is-info' : 'is-neutral' }}">{{ $smtp->status ? __('locale.Enabled') : __('locale.Disabled') }}</span>
                <svg class="wp-wc-section-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </span>
        </button>
        <div class="wp-wc-section-body">
            <div class="wp-wc-section-body-inner">
                <div data-smtp-view>
                    <dl class="wp-smtp-details">
                        <div><dt>{{ __('locale.Host') }} / {{ __('locale.Port') }}</dt><dd dir="ltr">{{ $smtp->host }}:{{ $smtp->port }}</dd></div>
                        <div><dt>{{ __('locale.Encryption') }}</dt><dd>{{ $smtp->encryption ? ($encryptions[$smtp->encryption] ?? $smtp->encryption) : __('locale.None') }}</dd></div>
                        @if ($smtp->isGoogle())
                            <div><dt>{{ __('locale.Google account') }}</dt><dd dir="ltr">{{ $smtp->username }}</dd></div>
                            <div><dt>{{ __('locale.Authentication') }}</dt><dd>{{ __('locale.Google OAuth 2.0 (XOAUTH2)') }}</dd></div>
                        @else
                            <div><dt>{{ __('locale.Username') }}</dt><dd dir="ltr">{{ $smtp->username }}</dd></div>
                        @endif
                        <div><dt>{{ __('locale.From Email') }}</dt><dd dir="ltr">{{ $smtp->from_address }}</dd></div>
                        <div><dt>{{ __('locale.From Name') }}</dt><dd>{{ $smtp->from_name ?: '---' }}</dd></div>
                    </dl>
                    @if ($smtp->isGoogle() && !$googleReady)
                        <p class="wp-field-hint wp-smtp-warning">{{ __('locale.Google sign-in for SMTP is not set up at the moment - this SMTP is skipped when sending.') }}</p>
                    @elseif ($smtp->status && !$usable)
                        <p class="wp-field-hint wp-smtp-warning">{{ __('locale.Enabled but incomplete - skipped when sending.') }}</p>
                    @endif
                    <div class="wp-smtp-actions">
                        <button type="button" class="w-btn w-btn-sm w-btn-outline" data-smtp-test data-url="{{ route('worker.partner.settings.smtp.test', $smtp->id) }}">{{ __('locale.Test') }}</button>
                        <button type="button" class="w-btn w-btn-sm w-btn-outline" data-smtp-edit>{{ __('locale.Edit') }}</button>
                        <button type="button" class="w-btn w-btn-sm w-btn-outline" data-smtp-move data-direction="up" data-url="{{ route('worker.partner.settings.smtp.move', $smtp->id) }}"
                                title="{{ __('locale.Move up') }}" aria-label="{{ __('locale.Move up') }}" @disabled($position === 1)>&uarr;</button>
                        <button type="button" class="w-btn w-btn-sm w-btn-outline" data-smtp-move data-direction="down" data-url="{{ route('worker.partner.settings.smtp.move', $smtp->id) }}"
                                title="{{ __('locale.Move down') }}" aria-label="{{ __('locale.Move down') }}" @disabled($position === $smtps->count())>&darr;</button>
                        @if (!$isPrimary && $usable)
                            <button type="button" class="w-btn w-btn-sm w-btn-outline" data-smtp-move data-direction="top" data-url="{{ route('worker.partner.settings.smtp.move', $smtp->id) }}">{{ __('locale.Make Primary') }}</button>
                        @endif
                        <button type="button" class="w-btn w-btn-sm w-btn-danger wp-smtp-delete" data-smtp-remove data-url="{{ route('worker.partner.settings.smtp.remove', $smtp->id) }}"
                                data-confirm="{{ __('locale.Delete SMTP #:n (:host)? This cannot be undone.', ['n' => $position, 'host' => $smtp->host]) }}">{{ __('locale.Delete') }}</button>
                    </div>
                </div>
                <form data-smtp-form action="{{ route('worker.partner.settings.smtp.save', $smtp->id) }}" autocomplete="off" hidden data-busy-testing="{{ __('locale.Testing SMTP connection...') }}" data-busy-saving="{{ __('locale.Saving...') }}">
                    <div class="w-form-alert" style="display:none;" data-smtp-form-alert></div>
                    @include('worker.partner.partials.smtp-fields', ['smtp' => $smtp, 'key' => $smtp->id])
                    <div class="wp-smtp-form-actions">
                        <button type="button" class="w-btn w-btn-outline" data-smtp-cancel>{{ __('locale.Cancel') }}</button>
                        <button type="submit" class="w-btn w-btn-primary">{{ __('locale.Save Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@empty
    <p class="wp-field-hint">{{ __('locale.No SMTP yet - your emails use the default RecruitmentCV email settings.') }}</p>
@endforelse

@if ($smtps->count() < $max)
    <div class="wp-smtp-add-actions" data-smtp-add-actions @if ($googlePending) hidden @endif>
        <button type="button" class="w-btn w-btn-primary" data-smtp-add>+ {{ __('locale.Add SMTP') }}</button>
        <button type="button" class="w-btn w-btn-outline wp-smtp-google-btn" data-smtp-google-start data-url="{{ route('worker.partner.settings.smtp.google') }}">
            @include('worker.partner.partials.google-g')
            <span>+ {{ __('locale.Add SMTP With Google') }}</span>
        </button>
    </div>
    <div class="wp-wc-section wp-smtp-new is-open" data-smtp-new hidden>
        <div class="wp-wc-section-header">{{ __('locale.New SMTP') }} (SMTP #{{ $smtps->count() + 1 }})</div>
        <div class="wp-wc-section-body">
            <div class="wp-wc-section-body-inner">
                <form data-smtp-form action="{{ route('worker.partner.settings.smtp.add') }}" autocomplete="off" data-busy-testing="{{ __('locale.Testing SMTP connection...') }}" data-busy-saving="{{ __('locale.Saving...') }}">
                    <div class="w-form-alert" style="display:none;" data-smtp-form-alert></div>
                    @include('worker.partner.partials.smtp-fields', ['smtp' => null, 'key' => 'new'])
                    <div class="wp-smtp-form-actions">
                        <button type="button" class="w-btn w-btn-outline" data-smtp-cancel>{{ __('locale.Cancel') }}</button>
                        <button type="submit" class="w-btn w-btn-primary">{{ __('locale.Add SMTP') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if ($googlePending)
        {{-- Connected Google account, not saved yet: server, login and From
        address are fixed by the account (sent nowhere from here - the server
        uses the connected values). "Add SMTP" tests the connection on the
        server first and saves only when it passed (PartnerSmtpGoogleController::save()). --}}
        <div class="wp-wc-section wp-smtp-new wp-smtp-google-new is-open" data-smtp-google-new>
            <div class="wp-wc-section-header">@include('worker.partner.partials.google-g') {{ __('locale.New Google SMTP') }} (SMTP #{{ $smtps->count() + 1 }})</div>
            <div class="wp-wc-section-body">
                <div class="wp-wc-section-body-inner">
                    <form data-smtp-form data-smtp-google-form action="{{ route('worker.partner.settings.smtp.google.save') }}" autocomplete="off" data-busy-testing="{{ __('locale.Testing SMTP connection...') }}" data-busy-saving="{{ __('locale.Saving...') }}">
                        <div class="w-form-alert" style="display:none;" data-smtp-form-alert role="status"></div>
                        <p class="wp-smtp-google-account">
                            @include('worker.partner.partials.google-g')
                            <span>{{ __('locale.Connected Google account') }}: <strong dir="ltr">{{ $googlePending['email'] }}</strong></span>
                        </p>
                        <div class="wp-info-grid">
                            <div class="w-form-row">
                                <label class="w-form-label" for="smtp-name-google">{{ __('locale.Name') }} <span class="wp-field-hint">({{ __('locale.optional') }})</span></label>
                                <input type="text" id="smtp-name-google" name="name" class="w-input" maxlength="100" value="Gmail">
                            </div>
                            <div class="w-form-row">
                                <label class="w-form-label" for="smtp-provider-google">{{ __('locale.SMTP Provider') }}</label>
                                <input type="text" id="smtp-provider-google" class="w-input" value="Gmail / Google" readonly>
                            </div>
                            <div class="w-form-row">
                                <label class="w-form-label" for="smtp-host-google">{{ __('locale.Host') }}</label>
                                <input type="text" id="smtp-host-google" class="w-input" dir="ltr" value="{{ $googleDefaults['host'] }}" readonly>
                            </div>
                            <div class="w-form-row">
                                <label class="w-form-label" for="smtp-port-google">{{ __('locale.Port') }}</label>
                                <input type="text" id="smtp-port-google" class="w-input" dir="ltr" value="{{ $googleDefaults['port'] }}" readonly>
                            </div>
                            <div class="w-form-row">
                                <label class="w-form-label" for="smtp-encryption-google">{{ __('locale.Encryption') }}</label>
                                <input type="text" id="smtp-encryption-google" class="w-input" value="{{ $googleDefaults['encryption'] === 'tls' ? 'TLS (STARTTLS)' : ($encryptions[$googleDefaults['encryption'] ?? ''] ?? __('locale.None')) }}" readonly>
                            </div>
                            <div class="w-form-row">
                                <label class="w-form-label" for="smtp-username-google">{{ __('locale.Username') }}</label>
                                <input type="text" id="smtp-username-google" class="w-input" dir="ltr" value="{{ $googlePending['email'] }}" readonly>
                            </div>
                            <div class="w-form-row">
                                <label class="w-form-label" for="smtp-auth-google">{{ __('locale.Authentication') }}</label>
                                <input type="text" id="smtp-auth-google" class="w-input" value="{{ __('locale.Google OAuth 2.0 (XOAUTH2)') }}" readonly>
                                <div class="wp-field-hint">{{ __('locale.No password needed - Google authorizes the sending.') }}</div>
                            </div>
                            <div class="w-form-row">
                                <label class="w-form-label" for="smtp-from-address-google">{{ __('locale.From Email') }}</label>
                                <input type="email" id="smtp-from-address-google" class="w-input" dir="ltr" value="{{ $googlePending['email'] }}" readonly>
                                <div class="wp-field-hint">{{ __('locale.Gmail sends from the connected account.') }}</div>
                            </div>
                            <div class="w-form-row">
                                <label class="w-form-label" for="smtp-from-name-google">{{ __('locale.From Name') }}</label>
                                <input type="text" id="smtp-from-name-google" name="from_name" class="w-input" maxlength="255" value="{{ $smtpPartner ? (optional($smtpPartner->domain)->company_name ?: $smtpPartner->rec_off_name) : '' }}">
                            </div>
                            <div class="w-form-row">
                                <label class="w-form-label" for="smtp-status-google">{{ __('locale.Status') }}</label>
                                <select id="smtp-status-google" name="status" class="w-select">
                                    <option value="1" selected>{{ __('locale.Enabled') }}</option>
                                    <option value="0">{{ __('locale.Disabled') }}</option>
                                </select>
                                <div class="wp-field-hint">{{ __('locale.Added last in the sending order - your current SMTPs keep sending first.') }}</div>
                                <div class="wp-field-hint">{{ __('locale.The connection is tested automatically when you click Add SMTP.') }}</div>
                            </div>
                        </div>
                        <div class="wp-smtp-form-actions">
                            <button type="button" class="w-btn w-btn-outline" data-smtp-google-discard data-url="{{ route('worker.partner.settings.smtp.google.discard') }}">{{ __('locale.Cancel') }}</button>
                            <button type="submit" class="w-btn w-btn-primary" data-smtp-google-submit>{{ __('locale.Add SMTP') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@else
    <p class="wp-field-hint">{{ __('locale.You can add at most :max SMTPs.', ['max' => $max]) }}</p>
@endif
