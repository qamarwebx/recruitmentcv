@php
    $reportTabs = [
        'outstanding' => ['label' => 'Outstanding', 'route' => 'admin.fund_advance.reports.outstanding'],
        'advances' => ['label' => 'Advances', 'route' => 'admin.fund_advance.reports.advances'],
        'loans' => ['label' => 'Loans', 'route' => 'admin.fund_advance.reports.loans'],
        'funds' => ['label' => 'Funds', 'route' => 'admin.fund_advance.reports.funds'],
        'settlements' => ['label' => 'Settlements', 'route' => 'admin.fund_advance.reports.settlements'],
        'transactions' => ['label' => 'Transactions', 'route' => 'admin.fund_advance.reports.transactions'],
    ];
@endphp
<ul class="nav nav-tabs mb-3">
    @foreach ($reportTabs as $key => $tab)
        <li class="nav-item">
            <a class="nav-link {{ $active === $key ? 'active' : '' }}" href="{{ route($tab['route']) }}">{{ $tab['label'] }}</a>
        </li>
    @endforeach
</ul>
