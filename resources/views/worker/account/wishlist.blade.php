@extends('worker.account.layout')

@section('title', __('locale.Wishlist'))

@section('account-content')
    <div class="w-account-card">
        <h2>{{ __('locale.Wishlist') }}</h2>

        @if ($items->count() > 0)
            <div class="w-account-list">
                @foreach ($items as $item)
                    @php
                        $isAr = app()->getLocale() === 'ar';
                        $candName = ($isAr && !empty($item->arcand_name)) ? $item->arcand_name : ($item->cand_name ?? '---');
                        $profession = ($isAr && !empty($item->profession_ar)) ? $item->profession_ar : ($item->profession_eng ?? '---');
                    @endphp
                    <div class="w-account-item" data-wishlist-row>
                        <img class="w-account-item-photo" src="{{ \App\Support\CandidatePhoto::url($item->photo_file) }}" alt="{{ $candName }}" onerror="this.onerror=null;this.src='{{ \App\Support\CandidatePhoto::defaultUrl() }}';">
                        <div class="w-account-item-main">
                            <div class="w-account-item-title">
                                <a href="{{ route('worker.resume.details', $item->slug_text) }}">{{ $candName }}@include('worker.partials.candidate-verified-icon')</a>
                            </div>
                            <div class="w-account-item-sub">
                                {{ $profession }}@if ($item->age) · {{ $item->age }} {{ __('locale.yrs old') }}@endif
                            </div>
                        </div>
                        <div class="w-account-item-actions">
                            <a href="{{ route('worker.resume.details', $item->slug_text) }}" class="w-btn w-btn-primary w-btn-sm">{{ __('locale.View Details') }}</a>
                            <button type="button" class="w-btn w-btn-outline w-btn-sm w-account-danger"
                                data-wishlist-toggle data-remove-row
                                data-url="{{ route('worker.account.wishlist.toggle') }}"
                                data-candidate="{{ $item->slug_text }}">
                                {{ __('locale.Remove') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{ $items->links('worker.partials.pagination') }}
        @else
            <div class="w-account-empty">
                <h3>{{ __('locale.Your wishlist is empty.') }}</h3>
                <p>{{ __('locale.Save candidates you like to find them again here.') }}</p>
                <a href="{{ route('worker.resumes') }}" class="w-btn w-btn-primary w-btn-sm">{{ __('locale.Browse Resumes') }}</a>
            </div>
        @endif
    </div>
@endsection
