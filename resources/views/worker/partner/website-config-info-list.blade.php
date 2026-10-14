{{-- How It Works -> Steps / Pricing -> Plans (App\Support\InfoPages): the whole list
saved in one request by partner-website-config-lists.js (add / edit / delete /
reorder on the page), like Branches above. --}}
<div class="wp-wc-section is-open" data-wc-collapse data-info-list="{{ $listKey }}"
    data-save-url="{{ $listKey === 'steps' ? route('worker.partner.website-config.steps.update') : route('worker.partner.website-config.plans.update') }}"
    data-confirm-delete="{{ __('locale.Are you sure you want to delete this item?') }}" style="margin-top:16px;">
    <button type="button" class="wp-wc-section-header" data-wc-collapse-toggle aria-expanded="true">
        <span class="wp-wc-section-title">{{ $listKey === 'steps' ? __('locale.Steps') : __('locale.Plans') }}</span>
        <svg class="wp-wc-section-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
    </button>
    <div class="wp-wc-section-body" data-wc-collapse-body>
        <div class="wp-wc-section-body-inner">
            <p style="color:var(--w-ink-500);font-size:0.85rem;margin:0 0 12px;">
                @if ($listKey === 'steps')
                    {{ $own ? __('locale.Your website shows these steps.') : __('locale.Not changed yet: your website shows these steps (from the Home page process). Save a changed list to use your own.') }}
                @else
                    {{ $own || !count($rows) ? __('locale.Your Pricing page shows these plans (only those with "Show on website"). Prices are shown exactly as you enter them. With no plans, the page shows the "Text when no plans are set".') : __('locale.Not changed yet: your Pricing page shows these plans from the main website. Save a changed list to use your own.') }}
                    {{ __('locale."Test plan" marks dummy plans: they are always labelled TEST PLAN and their button only leads to Contact Us. Nothing can be bought.') }}
                @endif
            </p>
            <div class="w-form-alert" style="display:none;" data-info-alert></div>
            <div data-info-rows>
                @foreach ($rows as $i => $row)
                    @include('worker.partner.website-config-info-row', ['listKey' => $listKey, 'row' => $row, 'index' => $i])
                @endforeach
            </div>
            <p style="color:var(--w-ink-500);font-size:0.85rem;" data-info-empty @if (count($rows)) hidden @endif>{{ $listKey === 'steps' ? __('locale.No steps: the page shows no steps section.') : __('locale.No plans yet.') }}</p>
            <template data-info-template>
                @include('worker.partner.website-config-info-row', ['listKey' => $listKey, 'row' => [], 'index' => null])
            </template>
            <button type="button" class="w-btn w-btn-outline" data-info-add style="margin-top:6px;">{{ $listKey === 'steps' ? __('locale.Add Step') : __('locale.Add Plan') }}</button>
            <button type="button" class="w-btn w-btn-primary" style="margin-top:16px;margin-inline-start:10px;" data-info-save
                data-default-text="{{ __('locale.Save Changes') }}" data-loading-text="{{ __('locale.Submitting…') }}">{{ __('locale.Save Changes') }}</button>
        </div>
    </div>
</div>
