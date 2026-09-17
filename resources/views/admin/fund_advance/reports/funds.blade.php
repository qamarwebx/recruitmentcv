@extends('layout.admin.admin_layout')

@section('title','Fund & Advance - Fund Report')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    @include('admin.fund_advance.partials.subnav', ['active' => 'reports'])
    @include('admin.fund_advance.reports._tabs', ['active' => 'funds'])
    @include('admin.fund_advance.reports._report_table', ['reportKey' => 'funds', 'reportRoute' => 'admin.fund_advance.reports.funds', 'showTypeFilter' => false])
</div>
@endsection
