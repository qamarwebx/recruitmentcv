{{-- One SMTP's fields (Partner Website -> SMTP): add ($smtp null) or edit.
     $key: 'new' or the SMTP id (unique element ids). The password is never
     rendered - blank keeps the saved one. --}}
@php
    $hasPassword = $smtp && $smtp->hasPassword();
    $value = fn ($field, $default = '') => $smtp ? ($smtp->{$field} ?? $default) : $default;
@endphp
<div class="wp-info-grid">
    <div class="w-form-row">
        <label class="w-form-label" for="smtp-name-{{ $key }}">{{ __('locale.Name') }} <span class="wp-field-hint">({{ __('locale.optional') }})</span></label>
        <input type="text" id="smtp-name-{{ $key }}" name="name" class="w-input" maxlength="100" placeholder="{{ __('locale.e.g. Gmail, Hostinger') }}" value="{{ $value('name') }}">
    </div>
    <div class="w-form-row">
        <label class="w-form-label" for="smtp-host-{{ $key }}">{{ __('locale.Host') }}</label>
        <input type="text" id="smtp-host-{{ $key }}" name="host" class="w-input" dir="ltr" maxlength="255" required placeholder="smtp.example.com" value="{{ $value('host') }}">
    </div>
    <div class="w-form-row">
        <label class="w-form-label" for="smtp-port-{{ $key }}">{{ __('locale.Port') }}</label>
        <select id="smtp-port-{{ $key }}" name="port" class="w-select" dir="ltr">
            @foreach (\App\Models\WebsiteSmtpSetting::PARTNER_PORTS as $port)
                <option value="{{ $port }}" @selected((int) $value('port', 587) === $port)>{{ $port }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-form-row">
        <label class="w-form-label" for="smtp-encryption-{{ $key }}">{{ __('locale.Encryption') }}</label>
        <select id="smtp-encryption-{{ $key }}" name="encryption" class="w-select">
            @foreach (\App\Models\WebsiteSmtpSetting::ENCRYPTIONS as $option => $label)
                <option value="{{ $option }}" @selected((string) $value('encryption', 'tls') === (string) $option)>{{ $option === '' ? __('locale.None') : $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-form-row">
        <label class="w-form-label" for="smtp-username-{{ $key }}">{{ __('locale.Username') }}</label>
        <input type="text" id="smtp-username-{{ $key }}" name="username" class="w-input" dir="ltr" maxlength="255" required autocomplete="off" value="{{ $value('username') }}">
    </div>
    <div class="w-form-row">
        <label class="w-form-label" for="smtp-password-{{ $key }}">{{ __('locale.Password') }}</label>
        <input type="password" id="smtp-password-{{ $key }}" name="password" class="w-input" dir="ltr" maxlength="255" autocomplete="new-password"
               value="" @unless ($hasPassword) required @endunless placeholder="{{ $hasPassword ? __('locale.Saved - leave blank to keep') : '' }}">
        <div class="wp-field-hint">{{ __('locale.Stored encrypted and never shown again.') }}</div>
    </div>
    <div class="w-form-row">
        <label class="w-form-label" for="smtp-from-address-{{ $key }}">{{ __('locale.From Email') }}</label>
        <input type="email" id="smtp-from-address-{{ $key }}" name="from_address" class="w-input" dir="ltr" maxlength="255" required value="{{ $value('from_address') }}">
    </div>
    <div class="w-form-row">
        <label class="w-form-label" for="smtp-from-name-{{ $key }}">{{ __('locale.From Name') }}</label>
        <input type="text" id="smtp-from-name-{{ $key }}" name="from_name" class="w-input" maxlength="255" value="{{ $value('from_name') }}">
    </div>
    <div class="w-form-row">
        <label class="w-form-label" for="smtp-status-{{ $key }}">{{ __('locale.Status') }}</label>
        <select id="smtp-status-{{ $key }}" name="status" class="w-select">
            <option value="1" @selected(!$smtp || $smtp->status)>{{ __('locale.Enabled') }}</option>
            <option value="0" @selected($smtp && !$smtp->status)>{{ __('locale.Disabled') }}</option>
        </select>
        <div class="wp-field-hint">{{ __('locale.Enabled checks the connection and login before saving.') }}</div>
    </div>
</div>
