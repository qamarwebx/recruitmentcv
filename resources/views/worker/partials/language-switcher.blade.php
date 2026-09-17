@php
    $__langCurrent = app()->getLocale();
    $__langTarget = $__langCurrent === 'ar' ? 'en' : 'ar';
    $__langTargetFlag = $__langTarget === 'ar' ? '🇸🇦' : '🇬🇧';
    $__langTargetLabel = $__langTarget === 'ar' ? 'العربية' : 'English';
@endphp
<a href="{{ route($switchRoute, $__langTarget) }}" class="lang-switcher" aria-label="{{ __('locale.Language') }}">
    <span class="lang-switcher-flag">{{ $__langTargetFlag }}</span>
    <span class="lang-switcher-label">{{ $__langTargetLabel }}</span>
</a>
