{{-- Effective Header / Footer Logo Width & Height for this page
     (App\Support\LogoDimensions: partner -> global -> default) as the CSS
     variables the shared .w-brand-logo rule reads. Numbers only (validated),
     so the output is safe unescaped. $logoPartnerId: the partner whose
     settings apply (null = global). --}}
<style>:root{ {!! \App\Support\LogoDimensions::cssVariables($logoPartnerId ?? null) !!} }</style>
