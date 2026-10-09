{{-- One hiring-status pill (App\Support\PartnerCandidateAccess::hiringStatuses()):
own = Hired by You (green), hired = Already Hired (amber; a button that opens the
existing Already Hired notice - already-hired.js, data-already-hired-badge),
available = Candidate Available (blue). On a card it sits over the photo, outside
the profile link (a click never opens the profile or Hire Now); $inline = in a
table cell. Text + icon, never colour alone. Nothing for any other status. --}}
@php
    $statusClass = 'w-hiring-status' . (!empty($inline) ? ' is-inline' : '');
@endphp
@if ($status === \App\Support\PartnerCandidateAccess::STATUS_OWN)
    <span class="{{ $statusClass }} is-own">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
        {{ __('locale.Hired by You') }}
    </span>
@elseif ($status === \App\Support\PartnerCandidateAccess::STATUS_HIRED)
    <button type="button" class="{{ $statusClass }} is-hired" data-already-hired-badge aria-haspopup="dialog">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
        {{ __('locale.Already Hired') }}
    </button>
@elseif ($status === \App\Support\PartnerCandidateAccess::STATUS_AVAILABLE)
    <span class="{{ $statusClass }} is-available">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
        {{ __('locale.Candidate Available') }}
    </span>
@endif
