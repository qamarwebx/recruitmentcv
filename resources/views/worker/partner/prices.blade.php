@extends('worker.partner.layouts.portal')

@section('title', __('locale.Price Update'))
@section('page-title', __('locale.Price Update'))

@section('content')
    @php
        $professionName = fn ($profession) => $profession
            ? (app()->getLocale() === 'ar' && !empty($profession->ar_name) ? $profession->ar_name : $profession->eng_name)
            : '---';
    @endphp

    {{-- Add a price for one Experience Type + Profession. --}}
    <div class="w-info-card wp-fade-in">
        <h2>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            {{ __('locale.Add Price') }}
        </h2>
        <p class="wp-field-hint" style="margin:-6px 0 16px;">{{ __('locale.Candidate pages show your price for each Experience Type and Profession. Combinations without a price show the default price.') }}</p>
        <form method="POST" action="{{ route('worker.partner.prices.store') }}">
            @csrf
            <div class="wp-info-grid">
                <div class="w-form-row">
                    <label class="w-form-label" for="price-exp-type">{{ __('locale.Experience Type') }}</label>
                    <select name="exp_type" id="price-exp-type" class="w-select" required>
                        <option value="">{{ __('locale.Select') }}</option>
                        @foreach ($experienceTypes as $code => $key)
                            <option value="{{ $code }}" @selected((string) old('exp_type') === (string) $code)>{{ __($key) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label" for="price-profession">{{ __('locale.Profession') }}</label>
                    <select name="proff_id" id="price-profession" class="w-select" required>
                        <option value="">{{ __('locale.Select') }}</option>
                        @foreach ($professions as $profession)
                            <option value="{{ $profession->id }}" @selected((string) old('proff_id') === (string) $profession->id)>{{ $professionName($profession) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label" for="price-cost">{{ __('locale.Service Price') }} (SAR)</label>
                    <input type="number" name="cost" id="price-cost" class="w-input" min="0" max="9999999" step="1" inputmode="numeric" value="{{ old('cost') }}" required>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label" for="price-days">{{ __('locale.Departure Days') }}</label>
                    <input type="number" name="days" id="price-days" class="w-input" min="0" max="3650" step="1" inputmode="numeric" value="{{ old('days') }}" required>
                </div>
            </div>
            <button type="submit" class="w-btn w-btn-primary">{{ __('locale.Add Price') }}</button>
        </form>
    </div>

    {{-- This partner's prices - Service Price / Departure Days editable in place. --}}
    <div class="w-info-card wp-fade-in">
        <h2>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>
            {{ __('locale.Your Prices') }}
        </h2>

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
                            <tr>
                                <td>{{ isset($experienceTypes[$price->exp_type]) ? __($experienceTypes[$price->exp_type]) : '---' }}</td>
                                <td>{{ $professionName($price->profession) }}</td>
                                <td>
                                    <input type="number" name="cost" form="price-form-{{ $price->id }}" class="w-input wp-price-input" min="0" max="9999999" step="1" inputmode="numeric"
                                           value="{{ (float) $price->cost == (int) $price->cost ? (int) $price->cost : $price->cost }}" required aria-label="{{ __('locale.Service Price') }}">
                                </td>
                                <td>
                                    <input type="number" name="days" form="price-form-{{ $price->id }}" class="w-input wp-price-input" min="0" max="3650" step="1" inputmode="numeric"
                                           value="{{ $price->days }}" required aria-label="{{ __('locale.Departure Days') }}">
                                </td>
                                <td>
                                    <div class="wp-price-actions">
                                        <form method="POST" action="{{ route('worker.partner.prices.update', $price->id) }}" id="price-form-{{ $price->id }}">
                                            @csrf
                                            <button type="submit" class="w-btn w-btn-primary w-btn-sm">{{ __('locale.Save') }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('worker.partner.prices.destroy', $price->id) }}"
                                              onsubmit="return confirm(@js(__('locale.Remove this price? The default price will be shown again.')));">
                                            @csrf
                                            <button type="submit" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.Remove') }}</button>
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
