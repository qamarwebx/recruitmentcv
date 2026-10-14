{{-- One How It Works step / Pricing plan row (partner-website-config-lists.js reads the data-info-field hooks). --}}
@php
    $row = $row ?? [];
    $lines = fn ($v) => is_array($v) ? implode("\n", $v) : (string) $v;
    $img = trim((string) ($row['image'] ?? ''));
@endphp
<div class="wp-branch-config-row" data-info-row>
    <div class="wp-branch-config-row-head">
        <strong>{{ $listKey === 'steps' ? __('locale.Step') : __('locale.Plan') }} <span data-info-number>{{ is_numeric($index ?? null) ? $index + 1 : '' }}</span></strong>
        <div class="wp-branch-config-row-actions">
            <button type="button" class="w-btn w-btn-outline w-btn-sm" data-info-move-up aria-label="{{ __('locale.Move up') }}">↑</button>
            <button type="button" class="w-btn w-btn-outline w-btn-sm" data-info-move-down aria-label="{{ __('locale.Move down') }}">↓</button>
            <button type="button" class="w-btn w-btn-danger w-btn-sm" data-info-remove>{{ __('locale.Delete') }}</button>
        </div>
    </div>
    @if ($listKey === 'steps')
        <div class="wp-info-grid" style="grid-template-columns:repeat(2,1fr);">
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Title (English)') }} *</label><input type="text" class="w-input" maxlength="150" data-info-field="title_en" value="{{ $row['title_en'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Title (Arabic)') }}</label><input type="text" class="w-input" maxlength="150" dir="rtl" data-info-field="title_ar" value="{{ $row['title_ar'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Description (English)') }}</label><textarea class="w-input" rows="3" maxlength="1000" data-info-field="text_en">{{ $row['text_en'] ?? '' }}</textarea></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Description (Arabic)') }}</label><textarea class="w-input" rows="3" maxlength="1000" dir="rtl" data-info-field="text_ar">{{ $row['text_ar'] ?? '' }}</textarea></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Tag (English)') }}</label><input type="text" class="w-input" maxlength="60" data-info-field="tag_en" value="{{ $row['tag_en'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Tag (Arabic)') }}</label><input type="text" class="w-input" maxlength="60" dir="rtl" data-info-field="tag_ar" value="{{ $row['tag_ar'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Tag colour') }}</label>
                <select class="w-input" data-info-field="tag_style">
                    @foreach (['agency' => __('locale.Green (partner agency)'), 'customer' => __('locale.Gold (customer)'), 'qamr' => __('locale.Dark green')] as $styleKey => $styleLabel)
                        <option value="{{ $styleKey }}" @selected(($row['tag_style'] ?? 'agency') === $styleKey)>{{ $styleLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Icon') }}</label>
                <select class="w-input" data-info-field="icon">
                    <option value="">{{ __('locale.None') }}</option>
                    @foreach (\App\Support\PageLists::ICONS as $iconKey => $iconLabel)
                        <option value="{{ $iconKey }}" @selected(($row['icon'] ?? '') === $iconKey)>{{ __('locale.' . $iconLabel) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="w-form-row">
            <label class="w-form-label">{{ __('locale.Image / Icon (optional)') }}</label>
            <div class="wp-logo-upload">
                <img data-info-image-preview src="{{ $img }}" alt="" @if ($img === '') hidden @endif>
                <div>
                    <input type="file" class="w-input" accept="{{ \App\Support\ImageUpload::ACCEPT }}" data-info-image-input>
                    <div class="form-text" style="color:var(--w-ink-500);font-size:0.82rem;margin-top:4px;">{{ __('locale.JPG, PNG, GIF, WEBP or SVG, up to 2MB.') }} {{ __('locale.Without an image, the step number is shown.') }}</div>
                    <button type="button" class="w-btn w-btn-outline w-btn-sm" style="margin-top:6px;" data-info-image-clear>{{ __('locale.Remove image') }}</button>
                </div>
            </div>
            <input type="hidden" data-info-field="image" value="{{ $img }}">
        </div>
    @else
        <div class="wp-info-grid" style="grid-template-columns:repeat(2,1fr);">
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Plan Name (English)') }} *</label><input type="text" class="w-input" maxlength="100" data-info-field="name_en" value="{{ $row['name_en'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Plan Name (Arabic)') }}</label><input type="text" class="w-input" maxlength="100" dir="rtl" data-info-field="name_ar" value="{{ $row['name_ar'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Description (English)') }}</label><textarea class="w-input" rows="2" maxlength="500" data-info-field="description_en">{{ $row['description_en'] ?? '' }}</textarea></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Description (Arabic)') }}</label><textarea class="w-input" rows="2" maxlength="500" dir="rtl" data-info-field="description_ar">{{ $row['description_ar'] ?? '' }}</textarea></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Price') }}</label><input type="text" class="w-input" maxlength="40" dir="ltr" data-info-field="price" value="{{ $row['price'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Price (Arabic)') }}</label><input type="text" class="w-input" maxlength="40" dir="rtl" data-info-field="price_ar" value="{{ $row['price_ar'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Currency') }}</label><input type="text" class="w-input" maxlength="10" dir="ltr" data-info-field="currency" value="{{ $row['currency'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Second Price Line') }}</label><input type="text" class="w-input" maxlength="40" data-info-field="price_2" value="{{ $row['price_2'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Second Price Line (Arabic)') }}</label><input type="text" class="w-input" maxlength="40" dir="rtl" data-info-field="price_2_ar" value="{{ $row['price_2_ar'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Badge (English)') }}</label><input type="text" class="w-input" maxlength="40" data-info-field="badge_en" value="{{ $row['badge_en'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Badge (Arabic)') }}</label><input type="text" class="w-input" maxlength="40" dir="rtl" data-info-field="badge_ar" value="{{ $row['badge_ar'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Billing Period (English)') }}</label><input type="text" class="w-input" maxlength="40" data-info-field="period_en" value="{{ $row['period_en'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Billing Period (Arabic)') }}</label><input type="text" class="w-input" maxlength="40" dir="rtl" data-info-field="period_ar" value="{{ $row['period_ar'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Features (English) - one per line') }}</label><textarea class="w-input" rows="4" maxlength="4000" data-info-field="features_en">{{ $lines($row['features_en'] ?? '') }}</textarea></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Features (Arabic) - one per line') }}</label><textarea class="w-input" rows="4" maxlength="4000" dir="rtl" data-info-field="features_ar">{{ $lines($row['features_ar'] ?? '') }}</textarea></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Button Text (English)') }}</label><input type="text" class="w-input" maxlength="60" data-info-field="button_text_en" value="{{ $row['button_text_en'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Button Text (Arabic)') }}</label><input type="text" class="w-input" maxlength="60" dir="rtl" data-info-field="button_text_ar" value="{{ $row['button_text_ar'] ?? '' }}"></div>
            <div class="w-form-row"><label class="w-form-label">{{ __('locale.Button Link') }}</label><input type="text" class="w-input" maxlength="500" dir="ltr" placeholder="https://... / /contact-us" data-info-field="button_url" value="{{ $row['button_url'] ?? '' }}"></div>
            <div class="w-form-row" style="display:flex;align-items:flex-end;"><label style="display:flex;gap:8px;align-items:center;font-weight:600;"><input type="checkbox" data-info-field="highlighted" value="1" @checked(!empty($row['highlighted']))> {{ __('locale.Recommended plan') }}</label></div>
            {{-- New rows start shown; saved plans without the flag (older records) count as shown. --}}
            <div class="w-form-row"><label style="display:flex;gap:8px;align-items:center;font-weight:600;"><input type="checkbox" data-info-field="enabled" value="1" @checked(($row['enabled'] ?? true) !== false)> {{ __('locale.Show on website') }}</label></div>
            <div class="w-form-row"><label style="display:flex;gap:8px;align-items:center;font-weight:600;color:#92400E;"><input type="checkbox" data-info-field="test" value="1" @checked(!empty($row['test']))> {{ __('locale.Test plan (dummy price, not for sale)') }}</label></div>
        </div>
    @endif
</div>
