<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

/**
 * Website Config override row - see the create_partner_page_contents
 * migration for the `content` JSON shape (locale => field => value). One row
 * per partner+page for a partner subdomain, or per page with partner_id
 * NULL for the central recruitmentcv.com website (managed from CRM ->
 * Website). Never holds a full copy of a page's content, only the fields
 * actually overridden in that context.
 */
class PartnerPageContent extends Model
{
    protected $fillable = [
        'partner_id',
        'page',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public const PAGES = ['home', 'resumes', 'about', 'contact', 'privacy', 'terms'];

    /**
     * Every overridable Website Config field per page (dot keys inside
     * content.{locale}) and its kind: text (255), long (2000), email, or
     * body (sanitized rich-text HTML). The single definition behind both
     * the Partner Portal and CRM -> Website forms/validation - the CRM's
     * twin of this model carries the same list.
     */
    public const FIELDS = [
        'home' => [
            'hero.eyebrow' => 'text',
            'hero.heading_prefix' => 'text',
            'hero.heading_highlight' => 'text',
            'hero.heading_suffix' => 'text',
            'hero.lead' => 'long',
            'hero.primary_cta_text' => 'text',
            'hero.card_title' => 'text',
            'hero.card_subtitle' => 'text',
            // The 4 stat cards under the hero (value e.g. "78+", label).
            'hero.stat1_value' => 'text',
            'hero.stat1_label' => 'text',
            'hero.stat2_value' => 'text',
            'hero.stat2_label' => 'text',
            'hero.stat3_value' => 'text',
            'hero.stat3_label' => 'text',
            'hero.stat4_value' => 'text',
            'hero.stat4_label' => 'text',
            'process.eyebrow' => 'text',
            'process.heading' => 'text',
            'process.subheading' => 'long',
            'process.step1_heading' => 'text',
            'process.step1_text' => 'long',
            'process.step2_heading' => 'text',
            'process.step2_text' => 'long',
            'process.step3_heading' => 'text',
            'process.step3_text' => 'long',
            'categories.eyebrow' => 'text',
            'categories.heading' => 'text',
            'cta.heading' => 'text',
            'cta.text' => 'long',
            'cta.button_text' => 'text',
        ],
        // Browse Resumes (/resumes) page banner.
        'resumes' => [
            'breadcrumb' => 'text',
            'header_title' => 'text',
            'header_subtitle' => 'long',
        ],
        'about' => [
            'header_title' => 'text',
            'header_subtitle' => 'long',
            'intro_text' => 'long',
            'why_choose_eyebrow' => 'text',
            'why_choose_heading' => 'text',
            'mission_heading' => 'text',
            'mission_text' => 'long',
            'vision_heading' => 'text',
            'vision_text' => 'long',
            'values_heading' => 'text',
            'values_text' => 'long',
            'value_added_eyebrow' => 'text',
            'value_added_heading' => 'text',
            'value_added_1_heading' => 'text',
            'value_added_1_text' => 'long',
            'value_added_2_heading' => 'text',
            'value_added_2_text' => 'long',
            'value_added_3_heading' => 'text',
            'value_added_3_text' => 'long',
            'cta_heading' => 'text',
            'cta_text' => 'long',
            'cta_button_text' => 'text',
        ],
        'contact' => [
            'header_title' => 'text',
            'header_subtitle' => 'long',
            'intro_text' => 'long',
            'address' => 'text',
            'phone' => 'text',
            'email' => 'email',
            'branches_eyebrow' => 'text',
            'branches_heading' => 'text',
            'branches_subheading' => 'long',
        ],
        'privacy' => [
            'title' => 'text',
            'subtitle' => 'long',
            'body' => 'body',
        ],
        'terms' => [
            'title' => 'text',
            'subtitle' => 'long',
            'body' => 'body',
        ],
    ];

    private const FIELD_RULES = [
        'text' => 'nullable|string|max:255',
        'long' => 'nullable|string|max:2000',
        'email' => 'nullable|email|max:255',
        'body' => 'nullable|string|max:50000',
    ];

    /**
     * Every field is optional - a blank value means "use the default" -
     * and both content.en.* and content.ar.* get identical rules.
     */
    public static function validationRules(string $page): array
    {
        $rules = [];
        foreach (['en', 'ar'] as $locale) {
            foreach (static::FIELDS[$page] ?? [] as $field => $kind) {
                $rules["content.{$locale}.{$field}"] = static::FIELD_RULES[$kind];
            }
        }

        return $rules;
    }

    /**
     * Partner Website -> WhatsApp tab settings row (not a page, so never in
     * PAGES). content = {number, icon, bg_color, icon_color, label}; each key
     * only present once the Partner has saved it.
     */
    public const WHATSAPP = 'whatsapp';

    /** Per-request cache for effectiveWhatsapp() (float renders on every page). */
    private static array $whatsappCache = [];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * The value every public page itself already falls back to for one
     * Website Config field, in one locale - built from the exact same
     * sources those pages read from (never a second copy of the actual
     * copy): translation keys via __(..., $locale) (resources/lang/{en,ar}/
     * locale.php - the same key the public Blade's own `?? __('locale.X')`
     * uses), the frontendwebsiteconfigs row (About/Contact intro + contact
     * details, same as worker.about/worker.contact), and for Privacy/Terms'
     * full legal body, the same worker.partials.*-legal-content partial
     * the public page itself @includes, rendered here to a string. This is
     * ALSO what a Partner's saved override is diffed against on save (see
     * stripDefaultValues()) - one function, two call sites, so the two can
     * never drift apart.
     */
    public static function defaultContent(string $page, string $locale, $frontwebsite, ?array $company = null): array
    {
        $isArabic = $locale === 'ar';
        // Privacy/Terms on a partner website: the partner (its Company
        // Profile, $company - see SiteBrand) operates the site on the
        // RecruitmentCV platform provided by Qamr International. No
        // $company (central recruitmentcv.com content) = Qamr-only text.
        $legal = \App\Support\SiteBrand::legalOperator($company, $locale);

        switch ($page) {
            case 'home':
                return [
                    'hero' => [
                        // Not translated on the public page either (worker/home.blade.php
                        // writes this eyebrow as a plain literal, not __()) - kept
                        // identical to that literal, not reworded here.
                        'eyebrow' => 'Qamr Worker Portal',
                        'heading_prefix' => __('locale.Hire', [], $locale),
                        'heading_highlight' => __('locale.Verified, Work-Ready', [], $locale),
                        'heading_suffix' => __('locale.Talent — Faster', [], $locale),
                        'lead' => __('locale.Browse professionally screened candidate resumes across trades, domestic and skilled roles. Every profile is reviewed for accuracy so you can shortlist with confidence.', [], $locale),
                        'primary_cta_text' => __('locale.Browse Resumes', [], $locale),
                        'card_title' => __('locale.Find talent in seconds', [], $locale),
                        'card_subtitle' => __("locale.Jump straight to what you're hiring for.", [], $locale),
                    ] + static::defaultHomeStats($locale),
                    'process' => [
                        'eyebrow' => __('locale.Simple Process', [], $locale),
                        'heading' => __('locale.Hiring made straightforward', [], $locale),
                        'subheading' => __('locale.From search to shortlist in three simple steps.', [], $locale),
                        'step1_heading' => __('locale.Search & Filter', [], $locale),
                        'step1_text' => __('locale.Narrow candidates by profession, experience type, work location and more.', [], $locale),
                        'step2_heading' => __('locale.Review Full Profile', [], $locale),
                        'step2_text' => __('locale.Check personal details, employment history, education and passport information.', [], $locale),
                        'step3_heading' => __('locale.Reach Out To Hire', [], $locale),
                        'step3_text' => __('locale.Contact our team directly by phone or WhatsApp to start the hiring process.', [], $locale),
                    ],
                    'categories' => [
                        'eyebrow' => __('locale.Popular Categories', [], $locale),
                        'heading' => __('locale.Browse by profession', [], $locale),
                    ],
                    'cta' => [
                        'heading' => __('locale.Ready to find your next hire?', [], $locale),
                        'text' => __('locale.Explore the full list of verified, work-ready candidates on the portal today.', [], $locale),
                        'button_text' => __('locale.Browse All Resumes', [], $locale),
                    ],
                ];

            case 'resumes':
                return static::defaultResumesHeader($locale);

            case 'about':
                return [
                    'header_title' => __('locale.About Us', [], $locale),
                    'header_subtitle' => __('locale.Learn about our mission, vision and the team behind Qamr International.', [], $locale),
                    // Same frontendwebsiteconfigs.about_us_eng/about_us_ar +
                    // hardcoded fallback chain as worker.about's own $aboutText.
                    'intro_text' => ($isArabic && !empty($frontwebsite->about_us_ar ?? null))
                        ? $frontwebsite->about_us_ar
                        : ($frontwebsite->about_us_eng ?? __('locale.Connecting verified, work-ready candidates with employers who need reliable talent, fast.', [], $locale)),
                    'why_choose_eyebrow' => __('locale.About Us', [], $locale),
                    'why_choose_heading' => __('locale.Why Choose Us', [], $locale),
                    'mission_heading' => __('locale.Mission', [], $locale),
                    'mission_text' => __('locale.To provide unmatched recruitment solutions that help our clients become more productive and profitable.', [], $locale),
                    'vision_heading' => __('locale.Vision', [], $locale),
                    'vision_text' => __('locale.To be globally known for an impactful, efficient and innovative Human Resources Consulting Partner.', [], $locale),
                    'values_heading' => __('locale.Values', [], $locale),
                    'values_text' => __('locale.Creating a self-sustaining, productive environment that gives the best experiences and opportunities of growth to our customers and employees alike.', [], $locale),
                    'value_added_eyebrow' => __('locale.Why Choose Us', [], $locale),
                    'value_added_heading' => __('locale.Value Added Services for World-Class Customer Experience', [], $locale),
                    'value_added_1_heading' => __('locale.Providing Service Before Self-Interest', [], $locale),
                    'value_added_1_text' => __('locale.We take care of everything before your visit so that you can focus on your business and growth by saving your valuable time.', [], $locale),
                    'value_added_2_heading' => __('locale.We have always been at the forefront of providing value-added services', [], $locale),
                    'value_added_2_text' => __('locale.Catering our clients with all the benefits and convenience.', [], $locale),
                    'value_added_3_heading' => __('locale.Delightful Experience', [], $locale),
                    'value_added_3_text' => __('locale.Taking into account the overall journey by building long term relationship with our clients.', [], $locale),
                    'cta_heading' => __('locale.Have a question for our team?', [], $locale),
                    'cta_text' => __("locale.We'd love to hear from you - get in touch and we'll respond as soon as we can.", [], $locale),
                    'cta_button_text' => __('locale.Contact Us', [], $locale),
                ];

            case 'contact':
                return [
                    'header_title' => __('locale.Get in touch!', [], $locale),
                    'header_subtitle' => __('locale.Have questions about hiring or working with Qamr International? Reach us directly using the details below.', [], $locale),
                    // Same frontendwebsiteconfigs fields + hardcoded fallback
                    // chain as worker.contact's own $contactIntro/$contactAddr/
                    // $contactPhone/$contactEmail.
                    'intro_text' => ($isArabic && !empty($frontwebsite->contact_us_ar ?? null))
                        ? $frontwebsite->contact_us_ar
                        : ($frontwebsite->contact_us_eng ?? __('locale.Fill out the form and our team will get back to you within 24 hours.', [], $locale)),
                    'address' => ($isArabic && !empty($frontwebsite->contact_us_location_ar ?? null))
                        ? $frontwebsite->contact_us_location_ar
                        : ($frontwebsite->contact_us_location ?: __('locale.Mumbai, India', [], $locale)),
                    'phone' => $frontwebsite->contact_us_phone ?: '+919969566388',
                    'email' => $frontwebsite->contact_us_email ?: 'info@qamrintl.com',
                    'branches_eyebrow' => __('locale.Our Office', [], $locale),
                    'branches_heading' => __('locale.Location', [], $locale),
                    'branches_subheading' => __('locale.Our branches across the region.', [], $locale),
                ];

            case 'privacy':
                return [
                    'title' => __('locale.Privacy Policy', [], $locale),
                    'subtitle' => $legal
                        ? __('locale.How :operator collects, uses, discloses and protects information on this website, provided on the RecruitmentCV platform by Qamr International.', $legal, $locale)
                        : __('locale.How Qamr International collects, uses, discloses and protects information on the Worker Portal.', [], $locale),
                    'body' => static::renderDefaultLegalBody('worker.partials.privacy-policy-legal-content', $frontwebsite, $locale, $legal),
                ];

            case 'terms':
                return [
                    'title' => __('locale.Terms of Service', [], $locale),
                    'subtitle' => $legal
                        ? __('locale.The terms that govern access to and use of the Worker Portal operated by :operator on the RecruitmentCV platform provided by Qamr International.', $legal, $locale)
                        : __('locale.The terms that govern access to and use of the Qamr International Worker Portal.', [], $locale),
                    'body' => static::renderDefaultLegalBody('worker.partials.terms-of-service-legal-content', $frontwebsite, $locale, $legal),
                ];
        }

        return [];
    }

    /**
     * Renders the same worker.partials.*-legal-content partial the public
     * Privacy/Terms pages @include, in a specific locale, to a plain HTML
     * string - the Website Config "Full Page Content" textarea's default.
     * Temporarily swaps the app locale (that partial's __() calls read it,
     * not a parameter) and always restores it in a finally, since this
     * runs mid-request while building the Partner's own admin page (which
     * has its own, unrelated current locale).
     */
    private static function renderDefaultLegalBody(string $view, $frontwebsite, string $locale, ?array $legalOperator = null): string
    {
        $previousLocale = App::getLocale();
        App::setLocale($locale);

        try {
            return view($view, ['frontwebsite' => $frontwebsite, 'legalOperator' => $legalOperator])->render();
        } finally {
            App::setLocale($previousLocale);
        }
    }

    /**
     * Fields whose partner-site default is partner-specific and therefore
     * never inherited from the global row: the Privacy/Terms subtitle and
     * body name the partner (its Company Profile) as the website operator
     * (SiteBrand::legalOperator()), which the global Qamr-only legal text
     * must not replace. Only applies when the partner has that name.
     */
    public const PARTNER_DEFAULT_FIELDS = [
        'privacy' => ['subtitle', 'body'],
        'terms' => ['subtitle', 'body'],
    ];

    /**
     * Browse Resumes (/resumes) banner defaults - the texts the page has
     * always shown (same keys in the CRM's copy of this model).
     */
    public static function defaultResumesHeader(string $locale): array
    {
        return [
            'breadcrumb' => __('locale.Resumes', [], $locale),
            'header_title' => __('locale.Browse Verified Worker Resumes', [], $locale),
            'header_subtitle' => __('locale.Filter by profession, experience and work location to find the right candidate for your team.', [], $locale),
        ];
    }

    /** Per-request cache for homeStats(). */
    private static ?array $homeStatsCache = null;

    /**
     * The live counts behind the homepage stat cards (published, active,
     * CV-ready candidates; professions; work cities and experience
     * countries those candidates list). The one place they are computed -
     * the public homepage and the Website Config defaults both read it.
     */
    public static function homeStats(): array
    {
        if (static::$homeStatsCache !== null) {
            return static::$homeStatsCache;
        }

        $available = fn ($q) => $q->where('status', 1)->where('publish', 1)->where('isdelete', 0)->where('cv_execute', 1);
        $listedBy = fn (string $table, string $column) => \Illuminate\Support\Facades\DB::table($table)
            ->whereExists(function ($query) use ($table, $column, $available) {
                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('candidates')
                    ->whereRaw("FIND_IN_SET({$table}.id, candidates.{$column})");
                $available($query);
            })
            ->count();

        return static::$homeStatsCache = [
            'candidates' => $available(Candidate::query())->count(),
            'professions' => Profession::count(),
            'cities' => $listedBy('expecworkcities', 'expwp_id'),
            'countries' => $listedBy('countries', 'expcountry_id'),
        ];
    }

    /**
     * Default values of the hero's 4 stat cards: the live count ("78+") and
     * the card's own label - what the homepage shows until a partner (or
     * the central config) overrides a value or label.
     */
    public static function defaultHomeStats(string $locale): array
    {
        $stats = static::homeStats();
        $cards = [
            1 => [$stats['candidates'], 'Verified Candidates'],
            2 => [$stats['professions'], 'Job Categories'],
            3 => [$stats['cities'], 'Work Locations'],
            4 => [$stats['countries'], 'Countries of Experience'],
        ];

        $defaults = [];
        foreach ($cards as $i => [$count, $label]) {
            $defaults["stat{$i}_value"] = number_format($count) . '+';
            $defaults["stat{$i}_label"] = __('locale.' . $label, [], $locale);
        }

        return $defaults;
    }

    /**
     * One context's own saved row for a page (no inheritance). $partnerId
     * null = the central recruitmentcv.com website (partner_id NULL, CRM ->
     * Website); an id = that partner's overrides. ResolvePartnerWebsiteDomain
     * decides the context from the Host header (currentPartner null on the apex).
     */
    private static function rowFor(?int $partnerId, string $page): ?self
    {
        return static::query()
            ->when($partnerId, fn ($q) => $q->where('partner_id', $partnerId), fn ($q) => $q->whereNull('partner_id'))
            ->where('page', $page)
            ->first();
    }

    /**
     * The effective-value layer for Website Config - the one place the
     * hierarchy is applied: partner override -> global (central row) ->
     * page default. Returns the first two layers merged field by field;
     * every call site adds the page default with
     * `$content['field'] ?? $existingDefault`. The apex gets the global row.
     */
    public static function contentFor(?int $partnerId, string $page, string $locale): array
    {
        if (!$partnerId) {
            return static::rowFor(null, $page)->content[$locale] ?? [];
        }

        return array_replace_recursive(
            static::inheritedGlobal($partnerId, $page, $locale),
            static::rowFor($partnerId, $page)->content[$locale] ?? []
        );
    }

    /**
     * The global (central) values a partner inherits for one page/locale:
     * all of them, minus PARTNER_DEFAULT_FIELDS when the partner has its own
     * legal operator name.
     */
    public static function inheritedGlobal(int $partnerId, string $page, string $locale): array
    {
        $global = static::rowFor(null, $page)->content[$locale] ?? [];

        if (isset(static::PARTNER_DEFAULT_FIELDS[$page])) {
            $company = \App\Support\SiteBrand::companyFromDomain(Domain::where('partner_id', $partnerId)->first());

            if (\App\Support\SiteBrand::legalOperator($company, $locale)) {
                $global = \Illuminate\Support\Arr::except($global, static::PARTNER_DEFAULT_FIELDS[$page]);
            }
        }

        return $global;
    }

    /**
     * What a partner's Website Config shows before it overrides anything:
     * its page default (partner-aware legal text) under the inherited
     * global values. The Partner Portal's prefill, and what an unchanged
     * field is compared against on save.
     */
    public static function inheritedContent(int $partnerId, string $page, string $locale, $frontwebsite, ?array $company = null): array
    {
        return array_replace_recursive(
            static::defaultContent($page, $locale, $frontwebsite, $company),
            static::inheritedGlobal($partnerId, $page, $locale)
        );
    }

    /**
     * Contact Us "Branches/Locations" - stored under content.branches (a
     * sibling of content.en/content.ar, not nested inside either one),
     * since each branch pairs an English name + Arabic name + ONE shared
     * image in a single record - splitting that across two locale buckets
     * would need extra id-matching logic for no benefit. A list, not a
     * flat field map, so it's override-the-whole-list, never a per-branch
     * merge with the default (there's no sane way to "merge" a reordered/
     * added/removed list against a different one).
     *
     * The exact same 5 branches (photo + label) worker.contact's own
     * hardcoded $branches array used to define - relocated here, not
     * duplicated, so this is still the only place that list is written.
     */
    public static function defaultBranches(): array
    {
        $defaults = [
            ['image' => 'user/img/ksa.png', 'key' => 'Al Riyadh and Al Qassim'],
            ['image' => 'user/img/Mumbai.png', 'key' => 'Mumbai'],
            ['image' => 'user/img/delhi.png', 'key' => 'New Delhi'],
            ['image' => 'user/img/lucknow.png', 'key' => 'Lucknow'],
            ['image' => 'user/img/hydr.png', 'key' => 'Hyderabad'],
        ];

        return collect($defaults)->map(fn ($branch) => [
            'name_en' => __('locale.' . $branch['key'], [], 'en'),
            'name_ar' => __('locale.' . $branch['key'], [], 'ar'),
            'image' => asset($branch['image']),
        ])->values()->all();
    }

    /**
     * The branches to render/prefill, same hierarchy as contentFor(): the
     * partner's saved list, else the global (central) list, else the
     * default list above. A whole list, never merged; a saved empty array
     * is a deliberate "no branches" and is honored as such.
     */
    public static function effectiveBranches(?int $partnerId): array
    {
        foreach (array_unique([$partnerId, null]) as $context) {
            $branches = static::rowFor($context, 'contact')->content['branches'] ?? null;

            if (is_array($branches)) {
                return $branches;
            }
        }

        return static::defaultBranches();
    }

    /**
     * Last-resort WhatsApp values, only for what the central (CRM ->
     * Website -> WhatsApp) row doesn't provide - e.g. that row missing.
     * The real default is centralWhatsapp(). icon null = the built-in
     * WhatsApp glyph; label null = the translated default "Customer
     * Support". icon_color is the button's text (label) colour.
     */
    public static function defaultWhatsapp(): array
    {
        return [
            'number' => '919004006272',
            'icon' => null,
            'bg_color' => '#25d366',
            'icon_color' => '#ffffff',
            'label' => null,
        ];
    }

    /** One context's saved WhatsApp values (empty ones dropped). */
    private static function savedWhatsapp(?int $partnerId): array
    {
        return array_filter(static::rowFor($partnerId, static::WHATSAPP)->content ?? [], fn ($value) => $value !== null && $value !== '');
    }

    /**
     * The default WhatsApp settings: the central row (CRM -> Website ->
     * WhatsApp, partner_id NULL) over the code fallback. What a partner
     * without its own values uses, and the placeholder the Partner
     * Website -> WhatsApp tab shows.
     */
    public static function centralWhatsapp(): array
    {
        return array_merge(static::defaultWhatsapp(), static::savedWhatsapp(null));
    }

    /**
     * This context's saved WhatsApp settings (a partner's, or the central
     * apex website's - see rowFor()) merged key-by-key over the defaults, plus the
     * derived values the button renders: wa.me link, versioned icon URL and
     * shadow colors from the background.
     */
    public static function effectiveWhatsapp(?int $partnerId): array
    {
        $key = (int) $partnerId;

        if (isset(static::$whatsappCache[$key])) {
            return static::$whatsappCache[$key];
        }

        $saved = static::savedWhatsapp($partnerId);

        // Field by field: a partner's own values, else the central (CRM)
        // settings, else the code fallback; the central context itself
        // (main site, Partner Portal support) = central over code fallback.
        $settings = array_merge($partnerId ? static::centralWhatsapp() : static::defaultWhatsapp(), $saved);

        $iconUrl = null;
        if ($settings['icon']) {
            $relative = 'admin/assets/images/partner/' . $settings['icon'];
            $version = @filemtime(public_path($relative));
            $iconUrl = $version ? asset($relative) . '?v=' . $version : null;
        }

        [$r, $g, $b] = sscanf($settings['bg_color'], '#%02x%02x%02x');

        return static::$whatsappCache[$key] = $settings + [
            'is_custom' => !empty($saved),
            'display_label' => $settings['label'] ?: __('locale.Customer Support'),
            'link' => 'https://wa.me/' . preg_replace('/\D/', '', $settings['number']),
            'icon_url' => $iconUrl,
            'shadow' => "rgba($r, $g, $b, 0.45)",
            'shadow_hover' => "rgba($r, $g, $b, 0.55)",
        ];
    }

    /**
     * Partner Portal button: central RecruitmentCV support (number, icon,
     * colours - the partner_id NULL settings, never the partner's own
     * number), labelled with the signed-in partner's saved Button Label
     * (Partner Website -> WhatsApp); the central/default label when it is
     * empty.
     */
    public static function portalWhatsapp(int $partnerId): array
    {
        $wa = static::effectiveWhatsapp(null);
        $label = static::savedWhatsapp($partnerId)['label'] ?? null;

        if ($label !== null) {
            $wa['label'] = $label;
            $wa['display_label'] = $label;
        }

        return $wa;
    }

    public static function forgetWhatsapp(?int $partnerId): void
    {
        unset(static::$whatsappCache[(int) $partnerId]);
    }

    /**
     * Partner Website -> Branding settings row (not a page, like WHATSAPP).
     * content = {append_company_name: bool} - show the Company Profile name
     * under the logo in the Partner Portal sidebar. Partner rows only.
     */
    public const BRANDING = 'branding';

    public static function brandingFor(int $partnerId): array
    {
        $saved = static::rowFor($partnerId, static::BRANDING)->content ?? [];

        return ['append_company_name' => (bool) ($saved['append_company_name'] ?? false)];
    }

    public static function saveBranding(int $partnerId, array $values): void
    {
        $row = static::firstOrNew(['partner_id' => $partnerId, 'page' => static::BRANDING]);
        $row->content = array_merge($row->content ?? [], $values);
        $row->save();
    }
}
