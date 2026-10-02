{{-- Blue "verified" mark placed right after a candidate's name (cards,
detail pages, order/wishlist cards) - the CRM's own candidate asset, as on
qamarhire.com, served from this site via App\Support\CandidatePhoto. Shown
for every listed candidate exactly as the green "Verified" badge it replaces
was (the verified rule itself is unchanged). Sized in em, so it follows the
name's own font size; the word joiner (U+2060) keeps it on the same line as
the name's last word when a long name wraps. --}}
&#8288;<img src="{{ \App\Support\CandidatePhoto::verifiedIconUrl() }}" class="w-verified-icon" width="16" height="16" alt="{{ __('locale.Verified') }}" title="{{ __('locale.Verified') }}">