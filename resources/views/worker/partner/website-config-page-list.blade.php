{{-- One About Us / How It Works / Pricing list (App\Support\PageLists: trust items,
cards, offices, setup rows, compare rows, FAQs, driver categories, billing checks),
saved as a whole by partner-website-config-lists.js like the steps / plans.
$page, $listKey; $state = ['items' => what the website shows, 'own' => saved by this partner]. --}}
@php
    $plDef = \App\Support\PageLists::definition($page, $listKey);
    $plRows = $state['items'] ?? [];
@endphp
<div class="wp-wc-section" data-wc-collapse data-info-list="items" data-page-list="{{ $page }}.{{ $listKey }}"
    data-save-url="{{ route('worker.partner.website-config.page-list.update', ['page' => $page, 'list' => $listKey]) }}"
    data-confirm-delete="{{ __('locale.Are you sure you want to delete this item?') }}" style="margin-top:16px;">
    <button type="button" class="wp-wc-section-header" data-wc-collapse-toggle aria-expanded="false">
        <span class="wp-wc-section-title">{{ __('locale.' . $plDef['label']) }}</span>
        <svg class="wp-wc-section-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
    </button>
    <div class="wp-wc-section-body" data-wc-collapse-body>
        <div class="wp-wc-section-body-inner">
            <p style="color:var(--w-ink-500);font-size:0.85rem;margin:0 0 12px;">
                {{ !empty($state['own']) ? __('locale.Your website shows this list.') : __('locale.Not changed yet: your website shows this list from the main website. Save a changed list to use your own.') }}
                {{ __('locale.Arabic left empty shows the English text.') }}
            </p>
            <div class="w-form-alert" style="display:none;" data-info-alert></div>
            <div data-info-rows>
                @foreach ($plRows as $i => $plRow)
                    @include('worker.partner.website-config-page-list-row', ['plDef' => $plDef, 'plRow' => is_array($plRow) ? $plRow : [], 'index' => $i])
                @endforeach
            </div>
            <p style="color:var(--w-ink-500);font-size:0.85rem;" data-info-empty @if (count($plRows)) hidden @endif>{{ __('locale.No items: this part of the page is not shown.') }}</p>
            <template data-info-template>
                @include('worker.partner.website-config-page-list-row', ['plDef' => $plDef, 'plRow' => [], 'index' => null])
            </template>
            <button type="button" class="w-btn w-btn-outline" data-info-add style="margin-top:6px;">{{ __('locale.Add Item') }}</button>
            <button type="button" class="w-btn w-btn-primary" style="margin-top:16px;margin-inline-start:10px;" data-info-save
                data-default-text="{{ __('locale.Save Changes') }}" data-loading-text="{{ __('locale.Submitting…') }}">{{ __('locale.Save Changes') }}</button>
        </div>
    </div>
</div>
