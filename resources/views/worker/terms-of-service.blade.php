@extends('worker.layouts.app')

@section('title', 'Terms of Service — Qamr Worker Portal')
@section('meta_description', 'Read the Terms of Service governing use of the Qamr International Worker Portal at recruitmentcv.com.')

@section('content')

    <section class="w-page-header">
        <div class="w-container">
            <div class="w-breadcrumb">
                <a href="{{ route('worker.home') }}">{{ __('locale.Home') }}</a>
                <span>/</span>
                <span>{{ __('locale.Terms of Service') }}</span>
            </div>
            <h1>{{ $termsContent['title'] ?? __('locale.Terms of Service') }}</h1>
            <p>{{ $termsContent['subtitle'] ?? __('locale.The terms that govern access to and use of the Qamr International Worker Portal.') }}</p>
        </div>
    </section>

    @if (!empty($termsContent['body']))
        {{-- Partner Website Config override - the entire body is a single
        Partner-authored block replacing every section below, never mixed
        field-by-field with the default legal text. --}}
        <section class="w-section">
            <div class="w-container">
                <div class="w-legal w-fade" style="max-width:900px;margin-inline:auto;">
                    {!! $termsContent['body'] !!}
                </div>
            </div>
        </section>
    @else
    <section class="w-section">
        <div class="w-container">
            <div class="w-legal w-fade" style="max-width:900px;margin-inline:auto;">
                @include('worker.partials.terms-of-service-legal-content')
            </div>
        </div>
    </section>
    @endif

@endsection
