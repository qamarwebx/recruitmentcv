{{-- Site-wide floating WhatsApp button - included from both Worker layouts
(worker/layouts/app.blade.php and worker/partner/layouts/portal.blade.php)
so it appears once per page across the whole site, not duplicated per-page.
Always bottom-right regardless of locale/direction (see the CSS -
deliberately not mirrored in RTL like .w-back-top, matching how this brand
icon is conventionally kept in a fixed corner).

[white circle + WhatsApp icon] + label, e.g. "Customer Support". Number /
icon / colours / label come from the Partner's WhatsApp settings (Partner
Website -> WhatsApp tab), falling back to PartnerPageContent::
defaultWhatsapp(). $waPartnerId is passed by the including layout: the
host-resolved partner on the public site, the signed-in partner in the
Partner Portal, null on the apex. The WhatsApp settings tab reuses this
partial as its live preview ($waPreview = true, $wa = that page's settings). --}}
@php
    $wa = $wa ?? \App\Models\PartnerPageContent::effectiveWhatsapp($waPartnerId ?? null);
    $waPreview = !empty($waPreview);
@endphp
<a href="{{ $wa['link'] }}" target="_blank" rel="noopener" class="w-whatsapp-float{{ $waPreview ? ' is-preview' : '' }}" @if ($waPreview) data-wa-preview @endif
   aria-label="{{ $wa['display_label'] }}"
   style="--wa-bg: {{ $wa['bg_color'] }}; --wa-icon: {{ $wa['icon_color'] }}; --wa-shadow: {{ $wa['shadow'] }}; --wa-shadow-hover: {{ $wa['shadow_hover'] }};">
    <span class="w-whatsapp-float-icon" data-wa-float-icon>
        @if ($wa['icon_url'])
            <img src="{{ $wa['icon_url'] }}" alt="" aria-hidden="true">
        @else
            @include('worker.partials.whatsapp-glyph')
        @endif
    </span>
    <span class="w-whatsapp-float-label" data-wa-float-label>{{ $wa['display_label'] }}</span>
</a>
