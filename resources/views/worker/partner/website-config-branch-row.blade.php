{{-- One Branches/Locations row - used both for server-rendered existing
branches and (inside a <template>) as the blank row partner-website-config-
branches.js clones on "+ Add Branch". Every field carries a data-branch-*
hook the JS reads generically (no per-row wiring needed after a clone). --}}
<div class="wp-branch-config-row" data-branch-row>
    <div class="wp-branch-config-row-head">
        <strong>{{ __('locale.Branch') }} <span data-branch-number>{{ is_numeric($index ?? null) ? $index + 1 : '' }}</span></strong>
        <div class="wp-branch-config-row-actions">
            <button type="button" class="w-btn w-btn-outline w-btn-sm" data-branch-move-up aria-label="{{ __('locale.Move up') }}">↑</button>
            <button type="button" class="w-btn w-btn-outline w-btn-sm" data-branch-move-down aria-label="{{ __('locale.Move down') }}">↓</button>
            <button type="button" class="w-btn w-btn-danger w-btn-sm" data-branch-remove>{{ __('locale.Delete') }}</button>
        </div>
    </div>
    <div class="wp-info-grid" style="grid-template-columns:repeat(2,1fr);">
        <div class="w-form-row">
            <label class="w-form-label">{{ __('locale.Branch Name (English)') }}</label>
            <input type="text" class="w-input" data-branch-field="name_en" value="{{ $branch['name_en'] ?? '' }}">
        </div>
        <div class="w-form-row">
            <label class="w-form-label">{{ __('locale.Branch Name (Arabic)') }}</label>
            <input type="text" class="w-input" dir="rtl" data-branch-field="name_ar" value="{{ $branch['name_ar'] ?? '' }}">
        </div>
    </div>
    <div class="w-form-row">
        <label class="w-form-label">{{ __('locale.Branch Image') }}</label>
        <div class="wp-logo-upload">
            <img data-branch-image-preview src="{{ $branch['image'] ?? asset('user/img/real-estate/illustrations/contact.svg') }}" alt="">
            <div>
                <input type="file" class="w-input" accept="image/png,image/jpeg,image/webp" data-branch-image-input>
                <div class="form-text" style="color:var(--w-ink-500);font-size:0.82rem;margin-top:4px;">{{ __('locale.JPG, PNG or WEBP, up to 2MB.') }}</div>
            </div>
        </div>
        <input type="hidden" data-branch-field="image" value="{{ $branch['image'] ?? '' }}">
    </div>
</div>
