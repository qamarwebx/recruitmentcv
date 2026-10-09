<?php

namespace App\Support;

use Illuminate\Support\Facades\View;

/**
 * Public-site branding (titles, meta, footer, logo alt) for the current
 * host. On a partner subdomain it is the partner's Company Profile (Partner
 * Portal -> Website -> Company Profile = that partner's domains row, handed
 * over by ResolvePartnerWebsiteDomain as partnerBrand['company']), field by
 * field, falling back to the global value only where a field is empty. On
 * the main recruitmentcv.com site everything stays the global Qamr
 * branding, unchanged.
 */
class SiteBrand
{
    /** The literal brand in translated titles/meta (brandEscapedText()); the shown global name is CompanyProfile::globalName(). */
    public const GLOBAL_NAME = CompanyProfile::DEFAULT_NAME;

    public const GLOBAL_PORTAL_NAME = 'Qamr Worker Portal';

    /** The current partner site's Company Profile, or null on the main site. */
    public static function company(): ?array
    {
        $brand = View::shared('partnerBrand');

        return is_array($brand) && is_array($brand['company'] ?? null) ? $brand['company'] : null;
    }

    /**
     * Website -> Branding "Append Company Name": the effective Company
     * Profile name shown with the logo - Company Name in English, Company
     * Name (Arabic) in Arabic (no cross-language mixing): the partner's
     * ($company), else the CRM Global one ($company null = the main site);
     * '' when the setting is off or no name is set (= logo only). The one
     * rule (App\Support\CompanyProfile, twin of the CRM's) for the Partner
     * Portal sidebar, the Partner Login page and the public site.
     */
    public static function appendedName(?array $company, bool $on, ?string $locale = null): string
    {
        return CompanyProfile::appendedName($company, $on, $locale);
    }

    /**
     * appendedName() for a partner (Partner Portal sidebar): its Company
     * Profile ($domain = its domains row) when its effective "Append Company
     * Name" is on - PartnerPageContent::brandingFor(): own value, else global.
     */
    public static function partnerAppendedName(int $partnerId, $domain, ?string $locale = null): string
    {
        return self::appendedName(
            self::companyFromDomain($domain),
            \App\Models\PartnerPageContent::brandingFor($partnerId)[\App\Models\PartnerPageContent::APPEND_COMPANY_NAME],
            $locale
        );
    }

    /**
     * appendedName() for the current site (public pages, Partner Login) -
     * partnerBrand carries the effective setting resolved by
     * ResolvePartnerWebsiteDomain (the main site: the CRM Global setting
     * and Global Company Name).
     */
    public static function currentAppendedName(): string
    {
        $brand = View::shared('partnerBrand');

        return is_array($brand) ? self::appendedName(self::company(), ($brand['append_company_name'] ?? null) === true) : '';
    }

    public static function isPartnerSite(): bool
    {
        return self::company() !== null;
    }

    /**
     * Company Profile fields of a partner's domains row, in the shape the
     * rest of this class uses (ResolvePartnerWebsiteDomain shares it as
     * partnerBrand['company']; the Partner Portal passes its own).
     */
    public static function companyFromDomain($domain): ?array
    {
        if (!$domain) {
            return null;
        }

        return [
            'name' => $domain->company_name,
            'name_ar' => $domain->company_name_ar,
            'address' => $domain->company_address,
            'address_ar' => $domain->company_address_ar,
            'mobile' => $domain->company_mobile,
            'email' => $domain->company_email,
            // The address this website is shown on: the current host when it
            // is one of this partner's live addresses, else its primary one.
            'host' => in_array(strtolower((string) optional(request())->getHost()), \App\Support\PartnerDomains::hosts($domain), true)
                ? strtolower(request()->getHost())
                : (\App\Support\PartnerDomains::primaryHost($domain) ?? $domain->full_domain),
        ];
    }

    /**
     * A Company Profile text field in a language: the Arabic value for
     * 'ar' when set, else the English one; null when the partner left it
     * empty (caller falls back to the global value).
     */
    public static function companyField(?array $company, string $field, string $locale): ?string
    {
        if (!$company) {
            return null;
        }

        $value = $locale === 'ar' ? trim((string) ($company[$field . '_ar'] ?? '')) : '';
        if ($value === '') {
            $value = trim((string) ($company[$field] ?? ''));
        }

        return $value !== '' ? $value : null;
    }

    /** companyField() for the current site and language. */
    private static function localized(string $field): ?string
    {
        return self::companyField(self::company(), $field, app()->getLocale());
    }

    /**
     * Legal-party values for the default Privacy Policy / Terms of Service
     * on a partner website: the partner operates the site (its Company
     * Profile name, in $locale) on the RecruitmentCV platform provided by
     * Qamr International. Null when there is no partner (main site) or no
     * partner name - the default legal text then stays Qamr-only.
     *
     * @return array{operator: string, site: string}|null
     */
    public static function legalOperator(?array $company, string $locale): ?array
    {
        $name = self::companyField($company, 'name', $locale);

        return $name === null ? null : [
            'operator' => $name,
            'site' => !empty($company['host']) ? $company['host'] : RecruitmentDomain::root(),
        ];
    }

    /** legalOperator() for the current site (public pages) and language. */
    public static function currentLegalOperator(): ?array
    {
        return self::legalOperator(self::company(), app()->getLocale());
    }

    /** Company name shown for this site (partner name, else the CRM Global Company Name - default Qamr International). */
    public static function name(): string
    {
        return self::localized('name') ?? CompanyProfile::globalName();
    }

    /**
     * Swaps the global brand in an ALREADY-ESCAPED title/meta string (Blade
     * section content is escaped) for this partner's name/host; inserted
     * values are escaped too. Main site: returned unchanged.
     */
    public static function brandEscapedText(string $escaped): string
    {
        $company = self::company();
        if (!$company) {
            return $escaped;
        }

        $name = e(self::name());
        $text = str_replace([self::GLOBAL_PORTAL_NAME, self::GLOBAL_NAME], $name, $escaped);

        // "…at recruitmentcv.com" (the root domain) -> this partner's own host.
        if (!empty($company['host'])) {
            $text = preg_replace('#(?<![.\w/])' . preg_quote(RecruitmentDomain::root(), '#') . '#', e($company['host']), $text);
        }

        return $text;
    }

    /**
     * Footer values. Partner site: Company Profile address / mobile / email
     * (each falling back to the global footer value only if empty), the
     * partner's name in the copyright, its own host in the tagline, no
     * social links (partners have none configured) and the neutral default
     * blurb instead of Qamr's own "about" text. Main site: the global values
     * exactly as before.
     */
    /**
     * The global RecruitmentCV footer phone - what recruitmentcv.com's footer
     * shows (frontendwebsiteconfigs: bottom contact phone, else contact phone).
     * Also the support number of the partner Candidate Detail "Hire Candidate"
     * modal. $frontwebsite omitted = the saved row.
     */
    public static function globalFooterPhone($frontwebsite = false): ?string
    {
        if ($frontwebsite === false) {
            $frontwebsite = \Illuminate\Support\Facades\DB::table('frontendwebsiteconfigs')->first();
        }

        return $frontwebsite->bottom_contact_us_phone ?? ($frontwebsite->contact_us_phone ?? null);
    }

    public static function footer($frontwebsite): array
    {
        $globalPhone = self::globalFooterPhone($frontwebsite);
        $globalEmail = $frontwebsite->bottom_contact_us_email ?? null;
        $company = self::company();
        $locale = app()->getLocale();

        // Description + address: Website Config -> Footer for this site
        // (partner override -> global -> PartnerPageContent::defaultFooter(),
        // i.e. what the footer showed before).
        $partner = app()->bound('currentPartner') ? app('currentPartner') : null;
        $content = \App\Models\PartnerPageContent::contentFor(optional($partner)->id, 'footer', $locale);
        $defaults = \App\Models\PartnerPageContent::defaultFooter($locale, $frontwebsite, $company);
        $about = $content['description'] ?? $defaults['description'];
        $address = $content['address'] ?? $defaults['address'];

        if (!$company) {
            return [
                'address' => $address,
                'phone' => $globalPhone,
                'email' => $globalEmail,
                'about' => $about,
                'socials' => [
                    'Facebook' => $frontwebsite->bottom_contact_us_fb_link ?? null,
                    'Twitter' => $frontwebsite->bottom_contact_us_twitter_link ?? null,
                    'Instagram' => $frontwebsite->bottom_contact_us_instagram_link ?? null,
                    'LinkedIn' => $frontwebsite->bottom_contact_us_linkedin_link ?? null,
                ],
                'copyright' => CompanyProfile::globalName($locale),
                'tagline_host' => RecruitmentDomain::root(),
            ];
        }

        // Stored as digits (country code + number) - shown/dialled with a +.
        $mobile = trim((string) ($company['mobile'] ?? ''));
        if ($mobile !== '' && preg_match('/^\d+$/', $mobile)) {
            $mobile = '+' . $mobile;
        }
        $email = trim((string) ($company['email'] ?? ''));

        return [
            'address' => $address,
            'phone' => $mobile !== '' ? $mobile : $globalPhone,
            'email' => $email !== '' ? $email : $globalEmail,
            'about' => $about,
            'socials' => [],
            'copyright' => self::name(),
            'tagline_host' => $company['host'] ?: RecruitmentDomain::root(),
        ];
    }
}
