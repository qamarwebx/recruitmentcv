@extends('layout.admin.admin_layout')

@section('title','Fund & Advance - Transaction Report')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    @include('admin.fund_advance.partials.subnav', ['active' => 'reports'])
    @include('admin.fund_advance.reports._tabs', ['active' => 'transactions'])
    @include('admin.fund_advance.reports._report_table', ['reportKey' => 'transactions', 'reportRoute' => 'admin.fund_advance.reports.transactions', 'showTypeFilter' => true])
</div>
@endsection
