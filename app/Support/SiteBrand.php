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
    public const GLOBAL_NAME = 'Qamr International';

    public const GLOBAL_PORTAL_NAME = 'Qamr Worker Portal';

    /** The current partner site's Company Profile, or null on the main site. */
    public static function company(): ?array
    {
        $brand = View::shared('partnerBrand');

        return is_array($brand) && is_array($brand['company'] ?? null) ? $brand['company'] : null;
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
            'host' => $domain->full_domain,
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

    /** Company name shown for this site (partner name, else Qamr International). */
    public static function name(): string
    {
        return self::localized('name') ?? self::GLOBAL_NAME;
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
    public static function footer($frontwebsite): array
    {
        $globalAddress = $frontwebsite->bottom_contact_us_addr ?? null;
        $globalPhone = $frontwebsite->bottom_contact_us_phone ?? ($frontwebsite->contact_us_phone ?? null);
        $globalEmail = $frontwebsite->bottom_contact_us_email ?? null;
        $globalAbout = app()->getLocale() === 'ar' && !empty($frontwebsite->about_us_ar ?? null)
            ? $frontwebsite->about_us_ar
            : ($frontwebsite->about_us_eng ?? null);
        $defaultAbout = __('locale.Connecting verified, work-ready candidates with employers who need reliable talent, fast.');

        $company = self::company();

        if (!$company) {
            return [
                'address' => $globalAddress,
                'phone' => $globalPhone,
                'email' => $globalEmail,
                'about' => $globalAbout ?: $defaultAbout,
                'socials' => [
                    'Facebook' => $frontwebsite->bottom_contact_us_fb_link ?? null,
                    'Twitter' => $frontwebsite->bottom_contact_us_twitter_link ?? null,
                    'Instagram' => $frontwebsite->bottom_contact_us_instagram_link ?? null,
                    'LinkedIn' => $frontwebsite->bottom_contact_us_linkedin_link ?? null,
                ],
                'copyright' => self::GLOBAL_NAME,
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
            'address' => self::localized('address') ?? $globalAddress,
            'phone' => $mobile !== '' ? $mobile : $globalPhone,
            'email' => $email !== '' ? $email : $globalEmail,
            'about' => $defaultAbout,
            'socials' => [],
            'copyright' => self::name(),
            'tagline_host' => $company['host'] ?: RecruitmentDomain::root(),
        ];
    }
}
