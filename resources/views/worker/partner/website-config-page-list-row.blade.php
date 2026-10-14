{{-- One item of a PageLists list (partner-website-config-lists.js reads the
data-info-field hooks). Fields from App\Support\PageLists::LISTS: text / long =
English (first one required) + Arabic, icon, bool, choice:a,b. --}}
@php
    $plPrimary = collect($plDef['fields'])->search(fn ($f) => in_array($f[0], ['text', 'long'], true));
    $plChoiceLabels = ['done' => __('locale.Done'), 'now' => __('locale.In progress'), 'next' => __('locale.Next'), 'free' => __('locale.Free'), 'paid' => __('locale.Paid')];
@endphp
<div class="wp-branch-config-row" data-info-row>
    <div class="wp-branch-config-row-head">
        <strong>{{ __('locale.Item') }} <span data-info-number>{{ is_numeric($index ?? null) ? $index + 1 : '' }}</span></strong>
        <div class="wp-branch-config-row-actions">
            <button type="button" class="w-btn w-btn-outline w-btn-sm" data-info-move-up aria-label="{{ __('locale.Move up') }}">↑</button>
            <button type="button" class="w-btn w-btn-outline w-btn-sm" data-info-move-down aria-label="{{ __('locale.Move down') }}">↓</button>
            <button type="button" class="w-btn w-btn-danger w-btn-sm" data-info-remove>{{ __('locale.Delete') }}</button>
        </div>
    </div>
    <div class="wp-info-grid" style="grid-template-columns:repeat(2,1fr);">
        @foreach ($plDef['fields'] as $plField => [$plKind, $plLabel])
            @if (in_array($plKind, ['text', 'long'], true))
                @foreach (['en' => __('locale.English'), 'ar' => __('locale.Arabic')] as $plLocale => $plLang)
                    @php $plValue = $plRow[$plField . '_' . $plLocale] ?? ''; $plMax = $plKind === 'text' ? 200 : 1000; @endphp
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.' . $plLabel) }} ({{ $plLang }})@if ($plField === $plPrimary && $plLocale === 'en') *@endif</label>
                        @if ($plKind === 'long')
                            <textarea class="w-input" rows="2" maxlength="{{ $plMax }}" data-info-field="{{ $plField }}_{{ $plLocale }}" @if ($plLocale === 'ar') dir="rtl" @endif>{{ $plValue }}</textarea>
                        @else
                            <input type="text" class="w-input" maxlength="{{ $plMax }}" data-info-field="{{ $plField }}_{{ $plLocale }}" value="{{ $plValue }}" @if ($plLocale === 'ar') dir="rtl" @endif>
                        @endif
                    </div>
                @endforeach
            @elseif ($plKind === 'icon')
                <div class="w-form-row"><label class="w-form-label">{{ __('locale.' . $plLabel) }}</label>
                    <select class="w-input" data-info-field="{{ $plField }}">
                        <option value="">{{ __('locale.None') }}</option>
                        @foreach (\App\Support\PageLists::ICONS as $iconKey => $iconLabel)
                            <option value="{{ $iconKey }}" @selected(($plRow[$plField] ?? '') === $iconKey)>{{ __('locale.' . $iconLabel) }}</option>
                        @endforeach
                    </select>
                </div>
            @elseif ($plKind === 'bool')
                <div class="w-form-row" style="display:flex;align-items:flex-end;"><label style="display:flex;gap:8px;align-items:center;font-weight:600;"><input type="checkbox" value="1" data-info-field="{{ $plField }}" @checked(!empty($plRow[$plField]))> {{ __('locale.' . $plLabel) }}</label></div>
            @elseif (str_starts_with($plKind, 'choice:'))
                @php $plOptions = explode(',', substr($plKind, 7)); @endphp
                <div class="w-form-row"><label class="w-form-label">{{ __('locale.' . $plLabel) }}</label>
                    <select class="w-input" data-info-field="{{ $plField }}">
                        @foreach ($plOptions as $plOption)
                            <option value="{{ $plOption }}" @selected(($plRow[$plField] ?? $plOptions[0]) === $plOption)>{{ $plChoiceLabels[$plOption] ?? $plOption }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        @endforeach
    </div>
</div>
