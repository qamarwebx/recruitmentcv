@props(['mode' => 'light', 'lang' => null])
@php
    $isArabic = ($lang ?? app()->getLocale()) === 'ar';

    // light = header logo, dark = footer logo (same pairing as the built-in
    // files). The effective image - this site's partner upload, else the
    // global (CRM -> Website -> Company Profile & Branding) upload, else the
    // built-in file - comes from App\Support\BrandingAssets, the one
    // resolver. The partner is the host-resolved one (ResolvePartnerWebsiteDomain),
    // null on the main site.
    $brandSlot = ($mode === 'dark' ? 'footer' : 'header') . '_logo_' . ($isArabic ? 'ar' : 'en');
    $brandPartnerId = app()->bound('currentPartner') ? optional(app('currentPartner'))->id : null;
@endphp
<img src="{{ \App\Support\BrandingAssets::url($brandSlot, $brandPartnerId) }}" {{ $attributes }}>
