@extends('layout.admin.admin_layout')

@section('title','Fund & Advance - Advance Report')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    @include('admin.fund_advance.partials.subnav', ['active' => 'reports'])
    @include('admin.fund_advance.reports._tabs', ['active' => 'advances'])
    @include('admin.fund_advance.reports._report_table', ['reportKey' => 'advances', 'reportRoute' => 'admin.fund_advance.reports.advances', 'showTypeFilter' => false])
</div>
@endsection
