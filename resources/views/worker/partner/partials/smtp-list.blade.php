{{-- Partner Website -> SMTP: the signed-in partner's SMTP list in sending
     order (App\Support\SmtpSettings), re-rendered by every SMTP action
     (PartnerPortalController::smtpResponse). $smtps: this partner's only. --}}
@php
    $primaryId = \App\Support\SmtpSettings::primaryId($smtps);
    $max = \App\Models\WebsiteSmtpSetting::MAX_PER_WEBSITE;
    $encryptions = \App\Models\WebsiteSmtpSetting::ENCRYPTIONS;
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
                        <div><dt>{{ __('locale.Username') }}</dt><dd dir="ltr">{{ $smtp->username }}</dd></div>
                        <div><dt>{{ __('locale.From Email') }}</dt><dd dir="ltr">{{ $smtp->from_address }}</dd></div>
                        <div><dt>{{ __('locale.From Name') }}</dt><dd>{{ $smtp->from_name ?: '---' }}</dd></div>
                    </dl>
                    @if ($smtp->status && !$usable)
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
                <form data-smtp-form action="{{ route('worker.partner.settings.smtp.save', $smtp->id) }}" autocomplete="off" hidden>
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
    <button type="button" class="w-btn w-btn-primary" data-smtp-add>+ {{ __('locale.Add SMTP') }}</button>
    <div class="wp-wc-section wp-smtp-new is-open" data-smtp-new hidden>
        <div class="wp-wc-section-header">{{ __('locale.New SMTP') }} (SMTP #{{ $smtps->count() + 1 }})</div>
        <div class="wp-wc-section-body">
            <div class="wp-wc-section-body-inner">
                <form data-smtp-form action="{{ route('worker.partner.settings.smtp.add') }}" autocomplete="off">
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
@else
    <p class="wp-field-hint">{{ __('locale.You can add at most :max SMTPs.', ['max' => $max]) }}</p>
@endif
