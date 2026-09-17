{{-- Site-wide floating WhatsApp button - included from both Worker layouts
(worker/layouts/app.blade.php and worker/partner/layouts/portal.blade.php)
so it appears once per page across the whole worker.qamarhire.com site,
not duplicated per-page. Always bottom-right regardless of locale/direction
(see the CSS - deliberately not mirrored in RTL like .w-back-top, matching
how this brand icon is conventionally kept in a fixed corner). Number is
the same one already used for the modal's "Contact Support" WhatsApp link. --}}
<a href="https://wa.me/919004006272" target="_blank" rel="noopener" class="w-whatsapp-float" aria-label="{{ __('locale.Chat on WhatsApp') }}">
    <svg width="30" height="30" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
        <path d="M16.004 3.2c-7.06 0-12.8 5.74-12.8 12.8 0 2.258.596 4.42 1.723 6.33L3.2 28.8l6.65-1.694a12.74 12.74 0 0 0 6.154 1.566h.005c7.06 0 12.8-5.74 12.8-12.8s-5.74-12.8-12.805-12.8zm7.512 18.302c-.32.9-1.586 1.646-2.593 1.862-.69.146-1.59.263-4.62-.993-3.878-1.606-6.375-5.545-6.57-5.802-.187-.257-1.573-2.096-1.573-4a4.29 4.29 0 0 1 1.34-3.196c.29-.29.634-.37.847-.37.213 0 .427.002.613.011.196.01.46-.075.72.549.267.64.907 2.214.986 2.375.08.16.133.347.027.56-.107.213-.16.347-.32.534-.16.187-.336.418-.48.561-.16.16-.326.334-.14.654.187.32.827 1.365 1.777 2.212 1.222 1.09 2.253 1.427 2.573 1.587.32.16.507.133.694-.08.187-.213.8-.933 1.014-1.253.213-.32.427-.267.72-.16.293.107 1.867.881 2.187 1.041.32.16.533.24.613.373.08.133.08.774-.24 1.674z"/>
    </svg>
</a>
