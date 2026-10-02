{{-- "Account Details" prompt: rendered by the portal layout only on the first
/partner/* page after a partner login, and only when that partner's Edit
Account Details are incomplete (App\Support\PartnerAccountPrompt). Same
fields/names as the Account page form; saves through the same
accountUpdate route (JSON). Closing only hides it - it won't be rendered
again until the next login. $missing = form field names still empty. --}}
@php
    $promptPartner = Auth::guard('partner')->user();
    $promptCountries = \App\Models\Country::orderBy('name')->get();
    $promptCities = \App\Models\City::orderBy('name')->get();
    $isMissing = fn (string $field) => in_array($field, $missing, true);
    $requiredTag = '<span class="wp-required-tag">' . e(__('locale.Required')) . '</span>';
@endphp
<div class="w-modal-backdrop" data-account-prompt-backdrop></div>
<div class="w-modal" data-account-prompt role="dialog" aria-modal="true" aria-labelledby="accountPromptTitle">
    <div class="w-modal-dialog">
        <button type="button" class="w-modal-close" data-account-prompt-close aria-label="{{ __('locale.Close') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
        <div class="w-modal-body">
            <h3 id="accountPromptTitle" class="w-modal-title">{{ __('locale.Account Details') }}</h3>
            <p class="w-modal-subtitle">{{ __('locale.Please complete your account details.') }}</p>
            <div class="w-form-alert" data-account-prompt-alert hidden></div>

            <form action="{{ route('worker.partner.account.update') }}" method="POST" data-account-prompt-form novalidate>
                @csrf
                <div class="w-form-row {{ $isMissing('owner_name') ? 'wp-prompt-missing' : '' }}" data-prompt-field="owner_name">
                    <label class="w-form-label" for="prompt-owner-name">{{ __('locale.Full Name') }} {!! $isMissing('owner_name') ? $requiredTag : '' !!}</label>
                    <input type="text" name="owner_name" id="prompt-owner-name" class="w-input" value="{{ $promptPartner->owner_name }}" maxlength="255">
                </div>
                <div class="w-form-row {{ $isMissing('rec_off_name') ? 'wp-prompt-missing' : '' }}" data-prompt-field="rec_off_name">
                    <label class="w-form-label" for="prompt-rec-off-name">{{ __('locale.Company / Recruitment Office Name') }} {!! $isMissing('rec_off_name') ? $requiredTag : '' !!}</label>
                    <input type="text" name="rec_off_name" id="prompt-rec-off-name" class="w-input" value="{{ $promptPartner->rec_off_name }}" maxlength="255">
                </div>
                <div class="w-form-row {{ $isMissing('licence_number') ? 'wp-prompt-missing' : '' }}" data-prompt-field="licence_number">
                    <label class="w-form-label" for="prompt-licence-number">{{ __('locale.Recruitment Licence Number') }} {!! $isMissing('licence_number') ? $requiredTag : '' !!}</label>
                    <input type="text" name="licence_number" id="prompt-licence-number" class="w-input" value="{{ $promptPartner->licence_number }}" maxlength="100" autocomplete="off">
                </div>
                <div class="w-form-row {{ $isMissing('country_id') ? 'wp-prompt-missing' : '' }}" data-prompt-field="country_id">
                    <label class="w-form-label" for="prompt-country">{{ __('locale.Country') }} {!! $isMissing('country_id') ? $requiredTag : '' !!}</label>
                    <select name="country_id" id="prompt-country" class="w-select" data-prompt-select>
                        <option value="">{{ __('locale.Select') }}</option>
                        @foreach ($promptCountries as $country)
                            <option value="{{ $country->id }}" @selected($promptPartner->country_id == $country->id)>{{ $country->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-form-row {{ $isMissing('city_id') ? 'wp-prompt-missing' : '' }}" data-prompt-field="city_id">
                    <label class="w-form-label" for="prompt-city">{{ __('locale.City') }} {!! $isMissing('city_id') ? $requiredTag : '' !!}</label>
                    <select name="city_id" id="prompt-city" class="w-select" data-prompt-select>
                        <option value="">{{ __('locale.Select') }}</option>
                        @foreach ($promptCities as $city)
                            <option value="{{ $city->id }}" @selected($promptPartner->city_id == $city->id)>{{ $city->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wp-form-actions">
                    <button type="submit" class="w-btn w-btn-primary" data-account-prompt-save>{{ __('locale.Save Changes') }}</button>
                    <button type="button" class="w-btn w-btn-outline" data-account-prompt-close>{{ __('locale.Close') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
