@extends('worker.account.layout')

@section('title', __('locale.Notifications'))

@section('account-content')
    <div class="w-account-card">
        <h2>{{ __('locale.Notifications') }}</h2>

        @if ($items->count() > 0)
            <ul class="w-account-feed">
                @foreach ($items as $item)
                    <li>
                        <span class="w-account-feed-dot {{ $item['kind'] === 'order' ? 'is-order' : '' }}"></span>
                        <div>
                            <strong>
                                @if ($item['url'])
                                    <a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
                                @else
                                    {{ $item['title'] }}
                                @endif
                            </strong>
                            @if (!empty($item['body']))
                                <p>{{ $item['body'] }}</p>
                            @endif
                            @if ($item['date'])
                                <time datetime="{{ $item['date']->toIso8601String() }}">{{ $item['date']->format('d M Y, h:i A') }}</time>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="w-account-empty">
                <h3>{{ __('locale.No notifications yet.') }}</h3>
                <p>{{ __('locale.Updates about your orders will appear here.') }}</p>
            </div>
        @endif
    </div>
@endsection
