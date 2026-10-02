@extends('worker.partner.layouts.portal')

@section('title', __('locale.Price Update'))
@section('page-title', __('locale.Price Update'))

@section('content')
    @php
        $professionName = fn ($profession) => $profession
            ? (app()->getLocale() === 'ar' && !empty($profession->ar_name) ? $profession->ar_name : $profession->eng_name)
            : '---';
    @endphp

    {{-- Every Experience Type + Profession with this partner's price
    (PartnerPrice::effectiveForPartner()): their saved price, or the
    default 3000 SAR / 15 days. Save updates a saved row, or creates the
    partner's row for a combination still on the default. --}}
    <div class="w-info-card wp-fade-in">
        <h2>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>
            {{ __('locale.Your Prices') }}
        </h2>

        @if ($errors->any())
            <div class="w-form-alert is-error">{{ $errors->first() }}</div>
        @endif

        @if ($prices->isEmpty())
            <p class="wp-field-hint" style="margin:0;">{{ __('locale.No prices added yet. Candidate pages show the default price.') }}</p>
        @else
            <div class="wp-table-scroll">
                <table class="wp-table wp-price-table">
                    <thead>
                        <tr>
                            <th>{{ __('locale.Experience Type') }}</th>
                            <th>{{ __('locale.Profession') }}</th>
                            <th>{{ __('locale.Service Price') }} (SAR)</th>
                            <th>{{ __('locale.Departure Days') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prices as $price)
                            @php $formId = 'price-form-' . $price->exp_type . '-' . $price->proff_id; @endphp
                            <tr>
                                <td>{{ isset($experienceTypes[$price->exp_type]) ? __($experienceTypes[$price->exp_type]) : '---' }}</td>
                                <td>{{ $professionName($price->profession) }}</td>
                                <td>
                                    <input type="number" name="cost" form="{{ $formId }}" class="w-input wp-price-input" min="0" max="9999999" step="1" inputmode="numeric"
                                           value="{{ (float) $price->cost == (int) $price->cost ? (int) $price->cost : $price->cost }}" required aria-label="{{ __('locale.Service Price') }}">
                                </td>
                                <td>
                                    <input type="number" name="days" form="{{ $formId }}" class="w-input wp-price-input" min="0" max="3650" step="1" inputmode="numeric"
                                           value="{{ $price->days }}" required aria-label="{{ __('locale.Departure Days') }}">
                                </td>
                                <td>
                                    <div class="wp-price-actions">
                                        <form method="POST" id="{{ $formId }}"
                                              action="{{ $price->exists ? route('worker.partner.prices.update', $price->id) : route('worker.partner.prices.store') }}">
                                            @csrf
                                            @unless ($price->exists)
                                                <input type="hidden" name="exp_type" value="{{ $price->exp_type }}">
                                                <input type="hidden" name="proff_id" value="{{ $price->proff_id }}">
                                            @endunless
                                            <button type="submit" class="w-btn w-btn-primary w-btn-sm">{{ __('locale.Save') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
