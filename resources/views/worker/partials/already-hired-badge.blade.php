{{-- "Already Hired" badge after a candidate's name + verified mark (Partner
candidate page, public resume page). Rendered only when the server says the
candidate is currently hired by another customer / partner
(App\Support\PartnerCandidateAccess::hiredByOthers()). Clicking it opens the
"Already Hired" notice (already-hired.js); Continue there runs the page's own
Hire Now. The word joiner keeps it on the name's last line when it fits. --}}
&#8288;<button type="button" class="w-already-hired-badge" data-already-hired-badge aria-haspopup="dialog">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
    {{ __('locale.Already Hired') }}
</button>
