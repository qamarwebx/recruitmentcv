@extends('worker.account.layout')

@section('title', __('locale.Personal Information'))

@section('account-content')
    @php
        $photoUrl = $customer->photo
            ? asset('user/img/avatars/' . $customer->photo)
            : ($customer->avatar_url ?: null);
    @endphp

    @if (session('success'))
        <div class="w-form-alert is-success" style="margin-bottom:16px;">{{ __('locale.Profile updated!') }}</div>
    @endif
    @if ($errors->any())
        <div class="w-form-alert" style="margin-bottom:16px;">{{ $errors->first() }}</div>
    @endif

    {{-- Existing endpoint DashboardController::updateMyprofile (always the
    logged-in customer). Country/city are passed through unchanged; the mobile
    number is changed only through the verified flow below. --}}
    <form class="w-account-card" method="POST" action="{{ route('myprofile.update') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="country_id" value="{{ $customer->country_id }}">
        <input type="hidden" name="city_id" value="{{ $customer->city_id }}">

        <h2>{{ __('locale.Personal Information') }}</h2>

        <div class="w-account-photo-row">
            @if ($photoUrl)
                <img src="{{ $photoUrl }}" alt="{{ $customer->name }}" class="w-account-avatar">
            @else
                <span class="w-account-avatar is-initial">{{ mb_strtoupper(mb_substr($customer->name ?: '?', 0, 1)) }}</span>
            @endif
            <div class="w-form-row" style="margin:0;flex:1;">
                <label class="w-form-label">{{ __('locale.Profile Photo') }}</label>
                <input type="file" name="photo" class="w-input" accept="image/jpeg,image/png,image/webp">
            </div>
        </div>

        <div class="w-account-fields">
            <div class="w-form-row">
                <label class="w-form-label">{{ __('locale.Full Name') }} *</label>
                <input type="text" name="name" class="w-input" value="{{ $customer->name }}" maxlength="255" required>
            </div>
            <div class="w-form-row">
                <label class="w-form-label">{{ __('locale.Company Name') }}</label>
                <input type="text" name="company_name" class="w-input" value="{{ $customer->company_name }}" maxlength="255">
            </div>
            <div class="w-form-row w-account-field-wide">
                <label class="w-form-label">{{ __('locale.Address') }}</label>
                <textarea name="address" class="w-input" rows="2" maxlength="1000">{{ $customer->address }}</textarea>
            </div>
        </div>

        <button type="submit" class="w-btn w-btn-primary w-btn-sm">{{ __('locale.Save Changes') }}</button>
    </form>

    <div class="w-account-card">
        <h2>{{ __('locale.Login & Contact') }}</h2>

        @include('worker.account.partials.contact-rows')
    </div>

    @include('worker.account.partials.contact-change-modal')
@endsection

@section('page-style')
    @include('worker.account.partials.contact-change-styles')
@endsection

@section('page-script')
    @include('worker.account.partials.contact-change-scripts')
@endsection
