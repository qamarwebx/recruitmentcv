{{--
    Partner Hiring Limit (set in CRM -> Website -> Settings): the signed-in
    partner's limits and how many orders count against them - the same
    numbers Hire Now checks (App\Support\PartnerHiringLimit; enforced in
    Worker\PartnerHireController::store, not here). Shown only when the
    partner has at least one limit; turns into a warning once any is used up.
--}}
@php
    $hlPartnerId = (int) \Illuminate\Support\Facades\Auth::guard('partner')->id();
    $hlStatus = $hlPartnerId ? \App\Support\PartnerHiringLimit::status($hlPartnerId) : [];
    $hlShown = array_filter($hlStatus, fn ($s) => $s['limit'] !== null);
    $hlBlocked = $hlShown ? \App\Support\PartnerHiringLimit::blocked($hlPartnerId, null, $hlStatus) : null;
@endphp
@if ($hlShown)
    <div class="w-form-alert {{ $hlBlocked ? 'is-error' : 'is-info' }} wp-hiring-limit {{ $hlClass ?? '' }}" role="status" data-hiring-limit>
        <strong>{{ __('locale.Hiring limit') }}</strong>
        @if ($hlBlocked)
            <span data-hiring-limit-blocked="{{ $hlBlocked['period'] }}">{{ $hlBlocked['message'] }} {{ __('locale.New orders can be placed again from') }} <span dir="ltr">{{ $hlBlocked['resets_at']->format('d M Y') }}</span>.</span>
        @endif
        <ul class="wp-hiring-limit-list">
            @foreach ($hlShown as $hlPeriod => $hl)
                <li class="{{ $hl['reached'] ? 'is-reached' : '' }}" data-hiring-limit-period="{{ $hlPeriod }}">
                    {{ \App\Support\PartnerHiringLimit::label($hlPeriod) }}: <span dir="ltr">{{ $hl['used'] }} / {{ $hl['limit'] }}</span> {{ __('locale.orders used') }}
                    @if ($hl['reached'])
                        <span class="wp-hiring-limit-reset">({{ __('locale.Resets on') }} <span dir="ltr">{{ $hl['resets_at']->format('d M Y') }}</span>)</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif
