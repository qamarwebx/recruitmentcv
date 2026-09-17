@extends('layout.admin.admin_layout')

@section('title','Fund & Advance - Loan Report')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    @include('admin.fund_advance.partials.subnav', ['active' => 'reports'])
    @include('admin.fund_advance.reports._tabs', ['active' => 'loans'])
    @include('admin.fund_advance.reports._report_table', ['reportKey' => 'loans', 'reportRoute' => 'admin.fund_advance.reports.loans', 'showTypeFilter' => false])
</div>
@endsection
