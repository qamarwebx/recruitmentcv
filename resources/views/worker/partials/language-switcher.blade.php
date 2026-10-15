@php
    $__langCurrent = app()->getLocale();
    $__langTarget = $__langCurrent === 'ar' ? 'en' : 'ar';
    $__langTargetFlag = $__langTarget === 'ar' ? '🇸🇦' : '🇬🇧';
    $__langTargetLabel = $__langTarget === 'ar' ? 'العربية' : 'English';
@endphp
<a href="{{ route($switchRoute, $__langTarget) }}" class="lang-switcher" aria-label="{{ __('locale.Language') }}">
    <span class="lang-switcher-flag">{{ $__langTargetFlag }}</span>
    <span class="lang-switcher-label">{{ $__langTargetLabel }}</span>
    {{-- Phones: just the short form ("ع" / "EN"), so the header controls never overlap. --}}
    <span class="lang-switcher-short" aria-hidden="true">{{ $__langTarget === 'ar' ? 'ع' : 'EN' }}</span>
</a>
