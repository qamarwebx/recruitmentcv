@php
    $navItems = [
        'dashboard' => ['label' => 'Dashboard', 'route' => 'admin.fund_advance.dashboard'],
        'transactions' => ['label' => 'Transactions', 'route' => 'admin.fund_advance.transactions.list'],
        'settlements' => ['label' => 'Settlements', 'route' => 'admin.fund_advance.settlements.pending'],
        'ledgers' => ['label' => 'Ledgers', 'route' => 'admin.fund_advance.ledgers.party'],
        'reports' => ['label' => 'Reports', 'route' => 'admin.fund_advance.reports.transactions'],
    ];
@endphp
<ul class="nav nav-pills mb-3 flex-nowrap overflow-auto">
    @foreach ($navItems as $key => $item)
        <li class="nav-item">
            <a class="nav-link text-nowrap {{ $active === $key ? 'active' : '' }}" href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
        </li>
    @endforeach
</ul>
