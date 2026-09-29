@extends('worker.account.layout')

@section('title', __('locale.Password & Security'))

@section('account-content')
    <div class="w-account-card">
        <h2>{{ __('locale.Password & Security') }}</h2>

        <div class="w-account-contact-row">
            <div>
                <span class="w-account-muted">{{ __('locale.Password') }}</span>
                <strong>{{ __('locale.Your account does not use a password.') }}</strong>
                <span class="w-account-muted">{{ __('locale.You sign in with a one-time code sent to your mobile number, or with Google.') }}</span>
            </div>
        </div>

        @include('worker.account.partials.contact-rows')

        <div class="w-account-contact-row">
            <div>
                <span class="w-account-muted">{{ __('locale.Google account') }}</span>
                <strong>{{ $customer->google_id ? __('locale.Linked') : __('locale.Not linked') }}</strong>
            </div>
        </div>
    </div>

    <div class="w-account-card">
        <h2>{{ __('locale.Last sign-in') }}</h2>

        @if ($lastLogin)
            <div class="w-account-fields">
                <div class="w-form-row">
                    <span class="w-account-muted">{{ __('locale.Date & Time') }}</span>
                    <strong>{{ !empty($lastLogin['last_logged_at']) ? \Illuminate\Support\Carbon::parse($lastLogin['last_logged_at'])->format('d M Y, h:i A') : '---' }}</strong>
                </div>
                <div class="w-form-row">
                    <span class="w-account-muted">{{ __('locale.Device') }}</span>
                    <strong>{{ $lastLogin['device_type'] ?? '---' }}</strong>
                </div>
                <div class="w-form-row">
                    <span class="w-account-muted">{{ __('locale.Browser') }}</span>
                    <strong>{{ trim(($lastLogin['browser'] ?? '---') . ' · ' . ($lastLogin['os'] ?? '')) }}</strong>
                </div>
                <div class="w-form-row">
                    <span class="w-account-muted">{{ __('locale.IP Address') }}</span>
                    <strong>{{ $lastLogin['ip_address'] ?? '---' }}</strong>
                </div>
            </div>
        @else
            <p style="margin:0;color:var(--w-ink-500);">{{ __('locale.No sign-in has been recorded yet.') }}</p>
        @endif
    </div>

    @include('worker.account.partials.contact-change-modal')
@endsection

@section('page-style')
    @include('worker.account.partials.contact-change-styles')
@endsection

@section('page-script')
    @include('worker.account.partials.contact-change-scripts')
@endsection
