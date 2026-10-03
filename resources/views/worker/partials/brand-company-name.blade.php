{{-- Website -> Branding "Append Company Name" on a partner site: the
Company Profile name (current language) next to the x-brand-logo it follows
(SiteBrand::currentAppendedName()). Always rendered - empty (hidden by CSS)
when off/blank - so the English and Arabic page have the same structure
(the Partner Login page swaps texts in place). --}}
@php $__appendedName = \App\Support\SiteBrand::currentAppendedName(); @endphp
<span class="w-logo-name" data-brand-name @if ($__appendedName !== '') title="{{ $__appendedName }}" @endif>{{ $__appendedName }}</span>
