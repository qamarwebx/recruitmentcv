@extends('layout.admin.admin_layout')

@section('title','Device Management')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <style>
        .pagestyle{
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out
        }
        .pagestyle:focus{
            color: #6e6b7b;
            background-color: #fff;
            border-color: #7367f0;
            outline: 0;
            box-shadow: 0 3px 10px 0 rgba(34, 41, 47, 0.1);
        }
    </style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header border-bottom">
            <div class="mb-1 float-start">
                <select id="pagination_list" class="pagestyle">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="150">150</option>
                    <option value="200">200</option>
                    <option value="250">250</option>
                    <option value="500">500</option>
                    <option value="1000">1000</option>
                </select>
            </div>
            <div class="mb-1 float-end">
                <input type="text" id="search_text" class="form-control" placeholder="Search...">
            </div>
        </div>

        <div class="card-datatable table-responsive contactpaginate">
            <table class="datatables-users table border-top">
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Device</th>
                        <th>Browser</th>
                        <th>OS</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="listitem">
                    @if($devices->count() > 0)
                        @foreach($devices as $device)
                            <tr>
                                <td>{{ $device->admin->name ?? 'N/A' }}</td>
                                <td>{{ $device->device_type }}</td>
                                <td>{{ $device->browser }}</td>
                                <td>{{ $device->os }}</td>
                                <td>{{ $device->ip_address }}</td>
                                <td>
                                    @if($device->is_approved)
                                        <a href="javascript:void(0);" class="badge bg-label-success">Approved</a>
                                    @else
                                        <a href="javascript:void(0);" class="badge bg-label-warning text-dark">Pending</a>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript::void(0);" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>
                                        <div class="dropdown-menu dropdown-menu-end m-0">
                                            @if(!$device->is_approved)
                                                <form action="{{ route('admin.device.approve') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $device->id }}">
                                                    <button class="dropdown-item"><i class="ti ti-check ti-sm"></i> Approve</button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.device.revoke') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $device->id }}">
                                                    <button class="dropdown-item"><i class="ti ti-x ti-sm"></i> Revoke</button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.device.delete') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $device->id }}">
                                                <button class="dropdown-item text-danger"><i class="ti ti-trash ti-sm"></i> Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">No Devices Found</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
<script>
    // Optional: add search or pagination scripts here
</script>
@endsection
