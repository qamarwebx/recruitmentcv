@extends('layout.admin.admin_layout')

@section('title','Fund & Advance - Outstanding Report')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    @include('admin.fund_advance.partials.subnav', ['active' => 'reports'])
    @include('admin.fund_advance.reports._tabs', ['active' => 'outstanding'])
    @include('admin.fund_advance.reports._report_table', ['reportKey' => 'outstanding', 'reportRoute' => 'admin.fund_advance.reports.outstanding', 'showTypeFilter' => true])
</div>
@endsection
