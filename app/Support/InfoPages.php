<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * How It Works + Pricing pages - twin file, identical in the CRM and
 * RecruitmentCV.
 *
 * Both are ordinary Website Config pages (App\Models\PartnerPageContent::
 * PAGES/FIELDS: header, intro rich text, section texts, CTA - partner
 * override -> global -> the defaults below), edited in CRM -> Website ->
 * Website Configuration and Partner Website -> Website Config, rendered by
 * RecruitmentCV (/how-it-works, /pricing). Their lists sit beside
 * content.en/content.ar on the same partner_page_contents row, like
 * Contact -> Branches:
 *
 * - how_it_works.steps: [{title_en, title_ar, text_en, text_ar, image}]
 *   (image = URL or null). Same hierarchy as Branches: the partner's list,
 *   else the "Recruitmentcv Partner (Global)" list (App\Support\PartnerGlobal),
 *   else the recruitmentcv.com list, else the Home page's own "process"
 *   steps (the workflow text the site already shows - nothing invented).
 *   A saved empty list = deliberately no steps.
 * - pricing.plans: [{name_*, description_*, price, currency, period_*,
 *   features_* (lists), highlighted, enabled, test, button_text_*,
 *   button_url}]. Same hierarchy as the steps (the partner's list, else the
 *   global list) but never defaulted: no plans = the page's empty-state
 *   text. enabled = shown on the website; test = dummy content, always
 *   labelled "TEST PLAN" with a non-purchase (Contact Us) button. Prices
 *   are shown exactly as typed. Display only - no checkout, payment or
 *   subscription exists behind a plan.
 *
 * Arabic values fall back to the English ones when left empty.
 */
final class InfoPages
{
    public const HOW = 'how_it_works';
    public const PRICING = 'pricing';
    public const PAGES = [self::HOW, self::PRICING];

    /** Pages whose every field is optional (blank = that part is not shown) - also in the global context. */
    public const OPTIONAL_PAGES = ['about', self::HOW, self::PRICING];

    /** Step "who does it" tag styles (reference: partner agency / customer / Qamr). */
    public const TAG_STYLES = ['agency', 'customer', 'qamr'];

    public const MAX_STEPS = 12;
    public const MAX_PLANS = 6;
    public const MAX_FEATURES = 20;

    /** Page defaults (what a field shows until it is set) - existing site strings where one fits. */
    public static function defaults(string $page, string $locale): array
    {
        $t = fn ($key) => __('locale.' . $key, [], $locale);

        return match ($page) {
            self::HOW => [
                'header_title' => $t('How It Works'),
                'header_subtitle' => $t('How hiring works on this website, step by step.'),
                'steps_eyebrow' => $t('Simple Process'),
                'steps_heading' => $t('Hiring made straightforward'),
                'cta_heading' => $t('Ready to find your next hire?'),
                'cta_text' => $t('Explore the full list of verified, work-ready candidates on the portal today.'),
                'cta_button_text' => $t('Browse All Resumes'),
            ],
            self::PRICING => [
                'header_title' => $t('Pricing'),
                'empty_text' => $t('Pricing details are not published yet. Please contact us and our team will be happy to help.'),
                'cta_heading' => $t('Have a question about pricing?'),
                'cta_text' => $t('Contact our team and we will answer your questions.'),
                'cta_button_text' => $t('Contact Us'),
            ],
            default => [],
        };
    }

    /** One context's saved list (steps / plans), or null when that context has none of its own. */
    public static function saved(?int $partnerId, string $page, string $key): ?array
    {
        $raw = DB::table('partner_page_contents')
            ->when($partnerId, fn ($q) => $q->where('partner_id', $partnerId), fn ($q) => $q->whereNull('partner_id'))
            ->where('page', $page)->value('content');
        $content = is_string($raw) ? json_decode($raw, true) : null;
        $list = is_array($content) ? ($content[$key] ?? null) : null;

        return is_array($list) ? array_values($list) : null;
    }

    /**
     * The How It Works steps to show / prefill: partner list -> "Recruitmentcv
     * Partner (Global)" list -> recruitmentcv.com list -> the Home page's
     * process steps.
     */
    public static function steps(?int $partnerId): array
    {
        if ($partnerId && ($own = self::saved($partnerId, self::HOW, 'steps')) !== null) {
            return $own;
        }

        return self::inheritedSteps($partnerId);
    }

    /** What a partner shows without its own steps (null = recruitmentcv.com itself). */
    public static function inheritedSteps(?int $partnerId): array
    {
        if ($partnerId && ($partnerGlobal = PartnerGlobal::list(self::HOW, 'steps')) !== null) {
            return $partnerGlobal;
        }

        return self::saved(null, self::HOW, 'steps') ?? self::defaultSteps($partnerId);
    }

    /**
     * The 3 steps of the Home page's "Simple Process" section, as this
     * website shows them (its Home overrides, else the standard text).
     */
    public static function defaultSteps(?int $partnerId): array
    {
        $fallback = [
            1 => ['Search & Filter', 'Narrow candidates by profession, experience type, work location and more.'],
            2 => ['Review Full Profile', 'Check personal details, employment history, education and passport information.'],
            3 => ['Reach Out To Hire', 'Contact our team directly by phone or WhatsApp to start the hiring process.'],
        ];
        $process = [];
        foreach (['en', 'ar'] as $locale) {
            $process[$locale] = \App\Models\PartnerPageContent::contentFor($partnerId, 'home', $locale)['process'] ?? [];
        }
        $steps = [];
        foreach ($fallback as $i => [$heading, $text]) {
            $step = [];
            foreach (['en', 'ar'] as $locale) {
                $step['title_' . $locale] = (string) ($process[$locale]["step{$i}_heading"] ?? __('locale.' . $heading, [], $locale));
            }
            foreach (['en', 'ar'] as $locale) {
                $step['text_' . $locale] = (string) ($process[$locale]["step{$i}_text"] ?? __('locale.' . $text, [], $locale));
            }
            $steps[] = $step + ['image' => null];
        }

        return $steps;
    }

    /** Pricing plans to edit / show: partner list -> "Recruitmentcv Partner (Global)" list -> recruitmentcv.com list (never invented). */
    public static function plans(?int $partnerId): array
    {
        if ($partnerId && ($own = self::saved($partnerId, self::PRICING, 'plans')) !== null) {
            return $own;
        }

        return self::inheritedPlans($partnerId);
    }

    /** What a partner shows without its own plans (null = recruitmentcv.com itself). */
    public static function inheritedPlans(?int $partnerId): array
    {
        if ($partnerId && ($partnerGlobal = PartnerGlobal::list(self::PRICING, 'plans')) !== null) {
            return $partnerGlobal;
        }

        return self::saved(null, self::PRICING, 'plans') ?? [];
    }

    /** The plans the public page shows: only those switched on ("Show on website"; older records without the flag count as on). */
    public static function visiblePlans(array $plans): array
    {
        return array_values(array_filter($plans, fn ($p) => is_array($p) && ($p['enabled'] ?? true) !== false && trim((string) ($p['name_en'] ?? '')) !== ''));
    }

    /** Any test / dummy plan among these? (the page then shows its test-only notice) */
    public static function hasTestPlans(array $plans): bool
    {
        return count(array_filter($plans, fn ($p) => !empty($p['test']))) > 0;
    }

    /** A record's text in a language - Arabic falls back to English. */
    public static function text(array $item, string $field, string $locale): string
    {
        $value = trim((string) ($item[$field . '_' . $locale] ?? ''));

        return $value !== '' ? $value : trim((string) ($item[$field . '_en'] ?? ''));
    }

    /** A plan's feature list in a language - Arabic falls back to English. */
    public static function features(array $plan, string $locale): array
    {
        $list = array_values(array_filter((array) ($plan['features_' . $locale] ?? []), fn ($f) => trim((string) $f) !== ''));

        return $list ?: array_values(array_filter((array) ($plan['features_en'] ?? []), fn ($f) => trim((string) $f) !== ''));
    }

    /** Validation of a submitted steps list (the image itself is checked by each app's upload). */
    public static function stepRules(): array
    {
        return [
            'steps' => 'present|array|max:' . self::MAX_STEPS,
            'steps.*.title_en' => 'required|string|max:150',
            'steps.*.title_ar' => 'nullable|string|max:150',
            'steps.*.text_en' => 'nullable|string|max:1000',
            'steps.*.text_ar' => 'nullable|string|max:1000',
            'steps.*.tag_en' => 'nullable|string|max:60',
            'steps.*.tag_ar' => 'nullable|string|max:60',
            'steps.*.tag_style' => 'nullable|in:' . implode(',', self::TAG_STYLES),
            'steps.*.icon' => 'nullable|in:' . implode(',', array_keys(PageLists::ICONS)),
        ];
    }

    public static function stepAttributes(): array
    {
        return ['steps.*.title_en' => 'step title (EN)', 'steps.*.title_ar' => 'step title (AR)', 'steps.*.text_en' => 'step description (EN)', 'steps.*.text_ar' => 'step description (AR)'];
    }

    /** Validation of a submitted plans list. Features: one per line. Button URL: http(s):// or a /path on the site. */
    public static function planRules(): array
    {
        return [
            'plans' => 'present|array|max:' . self::MAX_PLANS,
            'plans.*.name_en' => 'required|string|max:100',
            'plans.*.name_ar' => 'nullable|string|max:100',
            'plans.*.description_en' => 'nullable|string|max:500',
            'plans.*.description_ar' => 'nullable|string|max:500',
            'plans.*.price' => 'nullable|string|max:40',
            'plans.*.price_ar' => 'nullable|string|max:40',
            'plans.*.price_2' => 'nullable|string|max:40',
            'plans.*.price_2_ar' => 'nullable|string|max:40',
            'plans.*.badge_en' => 'nullable|string|max:40',
            'plans.*.badge_ar' => 'nullable|string|max:40',
            'plans.*.currency' => 'nullable|string|max:10',
            'plans.*.period_en' => 'nullable|string|max:40',
            'plans.*.period_ar' => 'nullable|string|max:40',
            'plans.*.features_en' => 'nullable|string|max:4000',
            'plans.*.features_ar' => 'nullable|string|max:4000',
            'plans.*.highlighted' => 'nullable|boolean',
            'plans.*.enabled' => 'nullable|boolean',
            'plans.*.test' => 'nullable|boolean',
            'plans.*.button_text_en' => 'nullable|string|max:60',
            'plans.*.button_text_ar' => 'nullable|string|max:60',
            'plans.*.button_url' => ['nullable', 'string', 'max:500', 'regex:#^(https?://[^\s]+|/[^\s]*)$#i'],
        ];
    }

    public static function planAttributes(): array
    {
        return ['plans.*.name_en' => 'plan name (EN)', 'plans.*.name_ar' => 'plan name (AR)', 'plans.*.button_url' => 'button URL', 'plans.*.price' => 'price'];
    }

    /** A validated steps list -> stored records (images already resolved to URLs by the caller). */
    public static function cleanSteps(array $steps, array $images = []): array
    {
        $out = [];
        foreach (array_values($steps) as $i => $step) {
            $image = trim((string) ($images[$i] ?? ($step['image'] ?? '')));
            $out[] = [
                'title_en' => trim((string) ($step['title_en'] ?? '')),
                'title_ar' => trim((string) ($step['title_ar'] ?? '')),
                'text_en' => trim((string) ($step['text_en'] ?? '')),
                'text_ar' => trim((string) ($step['text_ar'] ?? '')),
                'tag_en' => trim((string) ($step['tag_en'] ?? '')),
                'tag_ar' => trim((string) ($step['tag_ar'] ?? '')),
                'tag_style' => in_array($step['tag_style'] ?? '', self::TAG_STYLES, true) ? $step['tag_style'] : 'agency',
                'icon' => array_key_exists((string) ($step['icon'] ?? ''), PageLists::ICONS) ? $step['icon'] : '',
                'image' => preg_match('#^(https?://|/)#i', $image) ? $image : null,
            ];
        }

        return $out;
    }

    /** A validated plans list -> stored records (features split into lines; at most one highlighted plan). */
    public static function cleanPlans(array $plans): array
    {
        $out = [];
        $highlighted = false;
        foreach (array_values($plans) as $plan) {
            $isHighlighted = !$highlighted && filter_var($plan['highlighted'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $highlighted = $highlighted || $isHighlighted;
            $out[] = [
                'name_en' => trim((string) ($plan['name_en'] ?? '')),
                'name_ar' => trim((string) ($plan['name_ar'] ?? '')),
                'description_en' => trim((string) ($plan['description_en'] ?? '')),
                'description_ar' => trim((string) ($plan['description_ar'] ?? '')),
                'price' => trim((string) ($plan['price'] ?? '')),
                'price_ar' => trim((string) ($plan['price_ar'] ?? '')),
                'price_2' => trim((string) ($plan['price_2'] ?? '')),
                'price_2_ar' => trim((string) ($plan['price_2_ar'] ?? '')),
                'badge_en' => trim((string) ($plan['badge_en'] ?? '')),
                'badge_ar' => trim((string) ($plan['badge_ar'] ?? '')),
                'currency' => trim((string) ($plan['currency'] ?? '')),
                'period_en' => trim((string) ($plan['period_en'] ?? '')),
                'period_ar' => trim((string) ($plan['period_ar'] ?? '')),
                'features_en' => self::lines($plan['features_en'] ?? ''),
                'features_ar' => self::lines($plan['features_ar'] ?? ''),
                'highlighted' => $isHighlighted,
                // Not sent = shown (older records / callers without the flag); the forms always send it.
                'enabled' => filter_var($plan['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'test' => filter_var($plan['test'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'button_text_en' => trim((string) ($plan['button_text_en'] ?? '')),
                'button_text_ar' => trim((string) ($plan['button_text_ar'] ?? '')),
                'button_url' => trim((string) ($plan['button_url'] ?? '')),
            ];
        }

        return $out;
    }

    /** "one per line" text (or a list) -> trimmed non-empty lines, each at most 150 characters, at most MAX_FEATURES. */
    private static function lines($value): array
    {
        $text = is_array($value) ? implode("\n", $value) : (string) $value;
        $lines = [];
        foreach (preg_split('/\r\n|\r|\n/', $text) as $line) {
            $line = mb_substr(trim($line), 0, 150);
            if ($line !== '') {
                $lines[] = $line;
            }
        }

        return array_slice($lines, 0, self::MAX_FEATURES);
    }
}
