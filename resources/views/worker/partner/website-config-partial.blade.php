@php
    // Field metadata for every Website Config page - the single source
    // both the form markup below AND (mirrored, for validation) the
    // controller's websiteConfigValidationRules() are built from. Every
    // key here is a dot-path into that page's content JSON (see
    // PartnerPageContent), e.g. 'hero.eyebrow' -> content[en][hero][eyebrow].
    $fieldGroups = [
        'home' => [
            ['title' => __('locale.Hero Section'), 'fields' => [
                ['key' => 'hero.eyebrow', 'label' => __('locale.Eyebrow'), 'type' => 'text'],
                ['key' => 'hero.heading_prefix', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'hero.heading_highlight', 'label' => __('locale.Highlighted Heading'), 'type' => 'text'],
                ['key' => 'hero.heading_suffix', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'hero.lead', 'label' => __('locale.Lead Text'), 'type' => 'textarea'],
                ['key' => 'hero.primary_cta_text', 'label' => __('locale.Button Text'), 'type' => 'text'],
                ['key' => 'hero.card_title', 'label' => __('locale.Card Title'), 'type' => 'text'],
                ['key' => 'hero.card_subtitle', 'label' => __('locale.Card Subtitle'), 'type' => 'text'],
            ]],
            // The 4 stat cards under the hero (same order/icons as the page).
            ...collect([1, 2, 3, 4])->map(fn ($i) => ['title' => __('locale.Stat Card') . ' ' . $i, 'fields' => [
                ['key' => "hero.stat{$i}_value", 'label' => __('locale.Value'), 'type' => 'text'],
                ['key' => "hero.stat{$i}_label", 'label' => __('locale.Label'), 'type' => 'text'],
            ]])->all(),
            ['title' => __('locale.Process Section'), 'fields' => [
                ['key' => 'process.eyebrow', 'label' => __('locale.Eyebrow'), 'type' => 'text'],
                ['key' => 'process.heading', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'process.subheading', 'label' => __('locale.Subheading'), 'type' => 'textarea'],
            ]],
            ['title' => __('locale.Step') . ' 1', 'fields' => [
                ['key' => 'process.step1_heading', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'process.step1_text', 'label' => __('locale.Text'), 'type' => 'textarea'],
            ]],
            ['title' => __('locale.Step') . ' 2', 'fields' => [
                ['key' => 'process.step2_heading', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'process.step2_text', 'label' => __('locale.Text'), 'type' => 'textarea'],
            ]],
            ['title' => __('locale.Step') . ' 3', 'fields' => [
                ['key' => 'process.step3_heading', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'process.step3_text', 'label' => __('locale.Text'), 'type' => 'textarea'],
            ]],
            ['title' => __('locale.Categories Section'), 'fields' => [
                ['key' => 'categories.eyebrow', 'label' => __('locale.Eyebrow'), 'type' => 'text'],
                ['key' => 'categories.heading', 'label' => __('locale.Heading'), 'type' => 'text'],
            ]],
            ['title' => __('locale.CTA Banner'), 'fields' => [
                ['key' => 'cta.heading', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'cta.text', 'label' => __('locale.Text'), 'type' => 'textarea'],
                ['key' => 'cta.button_text', 'label' => __('locale.Button Text'), 'type' => 'text'],
            ]],
        ],
        'resumes' => [
            ['title' => __('locale.Page Header'), 'fields' => [
                ['key' => 'breadcrumb', 'label' => __('locale.Breadcrumb Label'), 'type' => 'text'],
                ['key' => 'header_title', 'label' => __('locale.Title'), 'type' => 'text'],
                ['key' => 'header_subtitle', 'label' => __('locale.Subtitle'), 'type' => 'textarea'],
            ]],
        ],
        'about' => [
            ['title' => __('locale.Page Header'), 'fields' => [
                ['key' => 'header_title', 'label' => __('locale.Title'), 'type' => 'text'],
                ['key' => 'header_subtitle', 'label' => __('locale.Subtitle'), 'type' => 'textarea'],
                ['key' => 'intro_text', 'label' => __('locale.Lead Text'), 'type' => 'textarea'],
            ]],
            ['title' => __('locale.Why Choose Us Section'), 'fields' => [
                ['key' => 'why_choose_eyebrow', 'label' => __('locale.Eyebrow'), 'type' => 'text'],
                ['key' => 'why_choose_heading', 'label' => __('locale.Heading'), 'type' => 'text'],
            ]],
            ['title' => __('locale.Mission'), 'fields' => [
                ['key' => 'mission_heading', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'mission_text', 'label' => __('locale.Text'), 'type' => 'textarea'],
            ]],
            ['title' => __('locale.Vision'), 'fields' => [
                ['key' => 'vision_heading', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'vision_text', 'label' => __('locale.Text'), 'type' => 'textarea'],
            ]],
            ['title' => __('locale.Values'), 'fields' => [
                ['key' => 'values_heading', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'values_text', 'label' => __('locale.Text'), 'type' => 'textarea'],
            ]],
            ['title' => __('locale.Value Added Services Section'), 'fields' => [
                ['key' => 'value_added_eyebrow', 'label' => __('locale.Eyebrow'), 'type' => 'text'],
                ['key' => 'value_added_heading', 'label' => __('locale.Heading'), 'type' => 'text'],
            ]],
            ['title' => __('locale.Item') . ' 1', 'fields' => [
                ['key' => 'value_added_1_heading', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'value_added_1_text', 'label' => __('locale.Text'), 'type' => 'textarea'],
            ]],
            ['title' => __('locale.Item') . ' 2', 'fields' => [
                ['key' => 'value_added_2_heading', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'value_added_2_text', 'label' => __('locale.Text'), 'type' => 'textarea'],
            ]],
            ['title' => __('locale.Item') . ' 3', 'fields' => [
                ['key' => 'value_added_3_heading', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'value_added_3_text', 'label' => __('locale.Text'), 'type' => 'textarea'],
            ]],
            ['title' => __('locale.CTA Banner'), 'fields' => [
                ['key' => 'cta_heading', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'cta_text', 'label' => __('locale.Text'), 'type' => 'textarea'],
                ['key' => 'cta_button_text', 'label' => __('locale.Button Text'), 'type' => 'text'],
            ]],
        ],
        'contact' => [
            ['title' => __('locale.Page Header'), 'fields' => [
                ['key' => 'header_title', 'label' => __('locale.Title'), 'type' => 'text'],
                ['key' => 'header_subtitle', 'label' => __('locale.Subtitle'), 'type' => 'textarea'],
                ['key' => 'intro_text', 'label' => __('locale.Lead Text'), 'type' => 'textarea'],
            ]],
            ['title' => __('locale.Contact Details'), 'fields' => [
                ['key' => 'address', 'label' => __('locale.Address'), 'type' => 'text'],
                ['key' => 'phone', 'label' => __('locale.Phone'), 'type' => 'text'],
                ['key' => 'email', 'label' => __('locale.Email'), 'type' => 'text'],
            ]],
            ['title' => __('locale.Branches Section'), 'fields' => [
                ['key' => 'branches_eyebrow', 'label' => __('locale.Eyebrow'), 'type' => 'text'],
                ['key' => 'branches_heading', 'label' => __('locale.Heading'), 'type' => 'text'],
                ['key' => 'branches_subheading', 'label' => __('locale.Subheading'), 'type' => 'textarea'],
            ]],
        ],
        'privacy' => [
            ['title' => __('locale.Page Header'), 'fields' => [
                ['key' => 'title', 'label' => __('locale.Title'), 'type' => 'text'],
                ['key' => 'subtitle', 'label' => __('locale.Subtitle'), 'type' => 'textarea'],
            ]],
            ['title' => __('locale.Legal Content'), 'fields' => [
                ['key' => 'body', 'label' => __('locale.Full Page Content (HTML)'), 'type' => 'textarea-lg'],
            ]],
        ],
        'terms' => [
            ['title' => __('locale.Page Header'), 'fields' => [
                ['key' => 'title', 'label' => __('locale.Title'), 'type' => 'text'],
                ['key' => 'subtitle', 'label' => __('locale.Subtitle'), 'type' => 'textarea'],
            ]],
            ['title' => __('locale.Legal Content'), 'fields' => [
                ['key' => 'body', 'label' => __('locale.Full Page Content (HTML)'), 'type' => 'textarea-lg'],
            ]],
        ],
    ];

    $wcPages = [
        'home' => __('locale.Home'),
        'resumes' => __('locale.Browse Resumes'),
        'about' => __('locale.About Us'),
        'contact' => __('locale.Contact Us'),
        'privacy' => __('locale.Privacy Policy'),
        'terms' => __('locale.Terms of Service'),
    ];

    // Purely a presentational grouping on top of $fieldGroups above (no
    // new fields, no new storage) - each entry names one collapsible
    // section by the $fieldGroups[$pageKey] group titles it should
    // contain, e.g. Home's 3 separate "Step" groups collapse together
    // under one "Process / Features" section since they're one logical
    // part of the page, not 3 standalone ones. The first section per
    // page starts open, every other one starts closed.
    $collapseSectionsByPage = [
        'home' => [
            ['title' => __('locale.Hero Section'), 'groupTitles' => [__('locale.Hero Section'), __('locale.Stat Card') . ' 1', __('locale.Stat Card') . ' 2', __('locale.Stat Card') . ' 3', __('locale.Stat Card') . ' 4']],
            ['title' => __('locale.Process / Features Section'), 'groupTitles' => [__('locale.Process Section'), __('locale.Step') . ' 1', __('locale.Step') . ' 2', __('locale.Step') . ' 3']],
            ['title' => __('locale.Categories Section'), 'groupTitles' => [__('locale.Categories Section')]],
            ['title' => __('locale.CTA Banner'), 'groupTitles' => [__('locale.CTA Banner')]],
        ],
        'resumes' => [
            ['title' => __('locale.Page Header'), 'groupTitles' => [__('locale.Page Header')]],
        ],
        'about' => [
            ['title' => __('locale.Page Header'), 'groupTitles' => [__('locale.Page Header')]],
            ['title' => __('locale.Why Choose Us Section'), 'groupTitles' => [__('locale.Why Choose Us Section'), __('locale.Mission'), __('locale.Vision'), __('locale.Values')]],
            ['title' => __('locale.Value Added Services Section'), 'groupTitles' => [__('locale.Value Added Services Section'), __('locale.Item') . ' 1', __('locale.Item') . ' 2', __('locale.Item') . ' 3']],
            ['title' => __('locale.CTA Banner'), 'groupTitles' => [__('locale.CTA Banner')]],
        ],
        'contact' => [
            ['title' => __('locale.Page Header'), 'groupTitles' => [__('locale.Page Header')]],
            ['title' => __('locale.Contact Details'), 'groupTitles' => [__('locale.Contact Details')]],
            ['title' => __('locale.Branches Section'), 'groupTitles' => [__('locale.Branches Section')]],
        ],
        'privacy' => [
            ['title' => __('locale.Page Header'), 'groupTitles' => [__('locale.Page Header')]],
            ['title' => __('locale.Legal Content'), 'groupTitles' => [__('locale.Legal Content')]],
        ],
        'terms' => [
            ['title' => __('locale.Page Header'), 'groupTitles' => [__('locale.Page Header')]],
            ['title' => __('locale.Legal Content'), 'groupTitles' => [__('locale.Legal Content')]],
        ],
    ];
@endphp

<div class="w-info-card wp-fade-in">
    <h2>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        {{ __('locale.Website Config') }}
    </h2>
    <p style="color:var(--w-ink-500);font-size:0.85rem;margin:-6px 0 18px;">{{ __('locale.Fields below already show the current content of your website. Edit a field to override it, or clear it to fall back to the current default.') }}</p>

    @if ($errors->any())
        <div class="w-form-alert is-error" style="margin-bottom:18px;">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="wp-wc-layout">
        <div class="wp-wc-nav" role="tablist">
            @foreach ($wcPages as $pageKey => $pageLabel)
                <button type="button" class="wp-wc-nav-btn {{ $activeWcPage === $pageKey ? 'is-active' : '' }}" data-wc-page-btn="{{ $pageKey }}">{{ $pageLabel }}</button>
            @endforeach
        </div>

        <div class="wp-wc-content">
    @foreach ($wcPages as $pageKey => $pageLabel)
        <div data-wc-page-pane="{{ $pageKey }}" class="{{ $activeWcPage === $pageKey ? 'is-active' : '' }}" style="{{ $activeWcPage === $pageKey ? '' : 'display:none;' }}">
            <form method="POST" action="{{ route('worker.partner.website-config.update', $pageKey) }}">
                @csrf

                <div class="wp-settings-tabs" role="tablist" style="margin-bottom:16px;">
                    <button type="button" class="wp-settings-tab-btn is-active" data-wc-lang-btn="en">English</button>
                    <button type="button" class="wp-settings-tab-btn" data-wc-lang-btn="ar">العربية</button>
                </div>

                @foreach (['en', 'ar'] as $locale)
                    <div data-wc-lang-pane="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}" style="{{ $locale === 'en' ? '' : 'display:none;' }}">
                        @foreach ($collapseSectionsByPage[$pageKey] as $sectionIndex => $section)
                            @php
                                $sectionGroups = collect($fieldGroups[$pageKey])->filter(fn ($g) => in_array($g['title'], $section['groupTitles'], true))->values();
                                $sectionOpen = $sectionIndex === 0;
                            @endphp
                            <div class="wp-wc-section {{ $sectionOpen ? 'is-open' : '' }}" data-wc-collapse>
                                <button type="button" class="wp-wc-section-header" data-wc-collapse-toggle aria-expanded="{{ $sectionOpen ? 'true' : 'false' }}">
                                    <span class="wp-wc-section-title">{{ $section['title'] }}</span>
                                    <svg class="wp-wc-section-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                                </button>
                                <div class="wp-wc-section-body" data-wc-collapse-body>
                                    <div class="wp-wc-section-body-inner">
                                        @foreach ($sectionGroups as $group)
                                            <div class="wp-info-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:6px;">
                                                @if (count($sectionGroups) > 1)
                                                    <div style="grid-column:1 / -1;font-weight:700;color:var(--w-ink-800);font-size:0.9rem;margin-top:10px;">{{ $group['title'] }}</div>
                                                @endif
                                                @foreach ($group['fields'] as $field)
                                                    @php
                                                        $inputName = 'content[' . $locale . '][' . str_replace('.', '][', $field['key']) . ']';
                                                        $oldKey = 'content.' . $locale . '.' . $field['key'];
                                                        $value = old($oldKey, data_get($pageContents[$pageKey][$locale] ?? [], $field['key']));
                                                    @endphp
                                                    <div class="w-form-row" @if ($field['type'] !== 'text') style="grid-column:1 / -1;" @endif>
                                                        <label class="w-form-label">{{ $field['label'] }}</label>
                                                        @if ($field['type'] === 'text')
                                                            <input type="text" name="{{ $inputName }}" class="w-input" value="{{ $value }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
                                                        @elseif ($field['type'] === 'textarea-lg')
                                                            {{-- Quill rich-text editor (partner-website-config-quill.js)
                                                            replaces the plain textarea here. The editor container
                                                            starts EMPTY - initQuillEditor() constructs Quill on it, then
                                                            loads the resolved effective HTML (carried here in the
                                                            hidden input's value) via Quill's own
                                                            clipboard.convert()/setContents() API, never by putting HTML
                                                            directly inside the container or writing to
                                                            quill.root.innerHTML. The hidden input keeps the SAME name
                                                            the plain-textarea version used (content[locale][body]) and
                                                            stays in sync with Quill's HTML output on every edit, so
                                                            form submission/validation/save are completely unchanged.
                                                            Explicit, unique ids (one editor/input pair per page+locale)
                                                            since 4 independent Quill instances exist on this one page.
                                                            Starting inside a COLLAPSED (closed) section is exactly the
                                                            "hidden container" case partner-website-config-quill.js's
                                                            MutationObserver already handles - it lazily initializes the
                                                            first time this collapse is opened, no changes needed there. --}}
                                                            @php
                                                                $wcEditorSlug = ($pageKey === 'privacy' ? 'privacy-policy' : 'terms-of-service') . '-' . $locale;
                                                            @endphp
                                                            <div class="wp-quill-wrap" data-wc-quill-group dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
                                                                <div class="wp-quill-editor" id="{{ $wcEditorSlug }}-editor" data-wc-quill></div>
                                                                <input type="hidden" name="{{ $inputName }}" id="{{ $wcEditorSlug }}-input" value="{{ $value }}" data-wc-quill-input>
                                                            </div>
                                                        @else
                                                            <textarea name="{{ $inputName }}" class="w-input" rows="3" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">{{ $value }}</textarea>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                <button type="submit" class="w-btn w-btn-primary" style="margin-top:10px;">{{ __('locale.Save Changes') }}</button>
            </form>

            @if ($pageKey === 'contact')
                {{-- Branches/Locations - its own card/form/save action,
                separate from the text fields above (see
                PartnerPortalController::websiteConfigBranchesUpdate()'s
                own doc comment for why: a repeater of records with image
                uploads doesn't fit the flat locale-nested text endpoint).
                Its own collapse, same as every other Website Config
                section - closed by default (it's not the first section
                on this tab), independent of the text-field collapses
                above since it lives in its own form/save flow already. --}}
                <div class="wp-wc-section" data-wc-collapse style="margin-top:16px;">
                    <button type="button" class="wp-wc-section-header" data-wc-collapse-toggle aria-expanded="false">
                        <span class="wp-wc-section-title">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-3px;margin-inline-end:6px;"><path d="M12 22s7-7.5 7-12a7 7 0 1 0-14 0c0 4.5 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            {{ __('locale.Branches / Locations') }}
                        </span>
                        <svg class="wp-wc-section-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="wp-wc-section-body" data-wc-collapse-body>
                        <div class="wp-wc-section-body-inner">
                            <div class="w-form-alert" style="display:none;" data-branches-alert></div>

                            <div data-branches-list>
                                @foreach ($pageContents['contact']['branches'] ?? [] as $i => $branch)
                                    @include('worker.partner.website-config-branch-row', ['branch' => $branch, 'index' => $i])
                                @endforeach
                            </div>

                            <button type="button" class="w-btn w-btn-outline" data-branches-add style="margin-top:6px;">{{ __('locale.Add Branch') }}</button>
                            <button type="button" class="w-btn w-btn-primary" style="margin-top:16px;margin-inline-start:10px;" data-branches-save
                                data-default-text="{{ __('locale.Save Changes') }}"
                                data-loading-text="{{ __('locale.Submitting…') }}">
                                {{ __('locale.Save Changes') }}
                            </button>
                        </div>
                    </div>
                </div>

                <template data-branch-row-template>
                    @include('worker.partner.website-config-branch-row', ['branch' => ['name_en' => '', 'name_ar' => '', 'image' => asset('user/img/real-estate/illustrations/contact.svg')], 'index' => null])
                </template>
            @endif
        </div>
    @endforeach
        </div>
    </div>
</div>
