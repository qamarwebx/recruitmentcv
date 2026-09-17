@props(['mode' => 'light', 'lang' => null])
@php
    $isArabic = ($lang ?? app()->getLocale()) === 'ar';

    // RecruitmentCV partner-subdomain branding (recruitmentcv.com only -
    // populated by ResolvePartnerWebsiteDomain, unset/empty everywhere
    // else including this same component on worker.qamarhire.com, so this
    // is a no-op there). Deliberately checked before the default asset
    // below, never the reverse - an active partner subdomain always wins.
    $partnerLogoUrl = $isArabic
        ? ($partnerBrand['logo_ar'] ?? null)
        : ($partnerBrand['logo_en'] ?? null);

    if (!$partnerLogoUrl) {
        $logoFile = $isArabic
            ? ($mode === 'dark' ? 'new_logo_Arabic_white.webp' : 'new_logo_Arabic.png')
            : ($mode === 'dark' ? 'Logo_eng_white.webp' : 'Logo_eng_dark.webp');
    }
@endphp
<img src="{{ $partnerLogoUrl ?: asset('user/img/logo/' . $logoFile) }}" {{ $attributes }}>
