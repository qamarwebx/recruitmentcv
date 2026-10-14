{{-- "Add SMTP With Google": Google came back with an unknown / used / expired
state (PartnerSmtpGoogleController::callback()). Nothing was connected. --}}
@extends('worker.layouts.app')

@section('title', __('locale.Google sign-in expired'))

@section('content')
    <section class="w-section">
        <div class="w-container" style="max-width:640px;">
            <div class="w-form-alert is-error" role="alert" style="display:block;">
                <strong>{{ __('locale.Google sign-in expired') }}</strong><br>
                {{ __('locale.This Google sign-in has expired or was already used, so no account was connected. Please go back to Website > SMTP in your dashboard and click "Add SMTP With Google" again.') }}
            </div>
        </div>
    </section>
@endsection
