@extends('worker.layouts.app')

@section('title', 'Privacy Policy — Qamr Worker Portal')
@section('meta_description', 'Learn how Qamr International collects, uses and protects your information on the Worker Portal at recruitmentcv.com.')

@section('content')

    <section class="w-page-header">
        <div class="w-container">
            <div class="w-breadcrumb">
                <a href="{{ route('worker.home') }}">{{ __('locale.Home') }}</a>
                <span>/</span>
                <span>{{ __('locale.Privacy Policy') }}</span>
            </div>
            <h1>{{ $privacyContent['title'] ?? __('locale.Privacy Policy') }}</h1>
            @php $legalOperator = \App\Support\SiteBrand::currentLegalOperator(); @endphp
            <p>{{ $privacyContent['subtitle'] ?? ($legalOperator
                ? __('locale.How :operator collects, uses, discloses and protects information on this website, provided on the RecruitmentCV platform by Qamr International.', $legalOperator)
                : __('locale.How Qamr International collects, uses, discloses and protects information on the Worker Portal.')) }}</p>
        </div>
    </section>

    @if (!empty($privacyContent['body']))
        {{-- Partner Website Config override - the entire body is a single
        Partner-authored block replacing every section below, never mixed
        field-by-field with the default legal text. --}}
        <section class="w-section">
            <div class="w-container">
                <div class="w-legal w-fade" style="max-width:900px;margin-inline:auto;">
                    {!! $privacyContent['body'] !!}
                </div>
            </div>
        </section>
    @else
    <section class="w-section">
        <div class="w-container">
            <div class="w-legal w-fade" style="max-width:900px;margin-inline:auto;">
                @include('worker.partials.privacy-policy-legal-content', ['legalOperator' => $legalOperator])
            </div>
        </div>
    </section>
    @endif

@endsection
