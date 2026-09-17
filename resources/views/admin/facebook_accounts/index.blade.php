@extends('layout.admin.admin_layout')

@section('title','Facebook Accounts')

@section('page-style')
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endsection

@section('content')

<div class="container-fluid flex-grow-1 container-p-y">

  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Facebook Accounts</h5>
    </div>

    <div class="card-datatable table-responsive">
      <table class="datatables-facebook-accounts table border-top">
        <thead>
          <tr>
            <th></th>
            <th>Account Name</th>
            <th>Pixel ID</th>
            <th>BM ID</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>

</div>

{{-- ADD --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="addAccount">
  <div class="offcanvas-header">
    <h5>Add Facebook Account</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form action="{{ route('admin.facebook.accounts.store') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label>Account Name</label>
        <input type="text" name="account_name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Dataset ID</label>
        <input type="text" name="pixel_id" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Business Manager ID</label>
        <input type="text" name="business_manager_id" class="form-control">
      </div>
      <div class="mb-3">
        <label>Test Event Code</label>
        <input type="text" name="test_event_code" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>CAPI Token</label>
        <textarea name="test_event_code" class="form-control" required></textarea>
      </div>
      <!-- <div class="mb-3">
        <label>Meta Pixel Based Code</label>
        <textarea name="meta_pixel_base_code" class="form-control" required></textarea>
      </div> -->
      <button class="btn btn-primary">Save</button>
    </form>
  </div>
</div>

{{-- EDIT --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="editAccount">
  <div class="offcanvas-header">
    <h5>Edit Facebook Account</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form action="{{ route('admin.facebook.accounts.update') }}" method="POST">
      @csrf
      <input type="hidden" name="edit_id" id="edit_id">
      <div class="mb-3">
        <label>Account Name</label>
        <input type="text" name="account_name" id="edit_account_name" class="form-control">
      </div>
      <div class="mb-3">
        <label>Dataset ID</label>
        <input type="text" name="pixel_id" id="edit_pixel_id" class="form-control">
      </div>
      <div class="mb-3">
        <label>Business Manager ID</label>
        <input type="text" name="business_manager_id" id="edit_bm_id" class="form-control">
      </div>
      <div class="mb-3">
        <label>Test Event Code</label>
        <input type="text" name="test_event_code" id="edit_test_event_code" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>CAPI Token</label>
        <textarea name="capi_access_token" id="edit_token" class="form-control"></textarea>
      </div>
      <!-- <div class="mb-3">
        <label>Meta Pixel Based Code</label>
        <textarea name="meta_pixel_base_code" id="edit_meta_pixel_base_code" class="form-control" required></textarea>
      </div> -->
      <button class="btn btn-primary">Update</button>
    </form>
  </div>
</div>

{{-- DELETE --}}
<div class="modal fade" id="deleteAccount">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admin.facebook.accounts.delete') }}">
      @csrf
      <input type="hidden" name="id" id="delete_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Delete Account</h5>
        </div>
        <div class="modal-body">Are you sure?</div>
        <div class="modal-footer">
          <button class="btn btn-danger">Delete</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- STATUS ACTIVE --}}
<div class="modal fade" id="actModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admin.facebook.accounts.active') }}" >
      @csrf
      <input type="hidden" name="id" id="actID">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Activate Facebook Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to activate this account?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success btn-sm">Activate</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- STATUS DEACTIVE --}}
<div class="modal fade" id="deactModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admin.facebook.accounts.deactive') }}">
      @csrf
      <input type="hidden" name="id" id="deactID">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Deactivate Facebook Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to deactivate this account?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger btn-sm">Deactivate</button>
        </div>
      </div>
    </form>
  </div>
</div>


@endsection

@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

<script>
    'use strict';

    $(function () {

      var table = $('.datatables-facebook-accounts');

      if (table.length) {
        table.DataTable({
          ajax: {
            url: "facebook-accounts/list/json",
            type: "POST",
            data: { "_token": $('meta[name="csrf-token"]').attr('content') }
          },

          columns: [
            { data: 'id' },
            { data: 'account_name' },
            { data: 'pixel_id' },
            { data: 'business_manager_id' },
            { data: 'status' },
            { data: 'id' }
          ],

          columnDefs: [
            {
              targets: 0,
              className: 'control',
              orderable: false,
              render: () => ''
            },
            {
              targets: 4,
              render: function (data, type, full) {
                return data
                  ? `<span class="badge bg-label-success" data-bs-toggle="modal" data-bs-target="#deactModal" data-id="${full.id}">Active</span>`
                  : `<span class="badge bg-label-warning" data-bs-toggle="modal" data-bs-target="#actModal" data-id="${full.id}">Inactive</span>`;
              }
            },
            {
              targets: -1,
              orderable: false,
              render: function (data) {
                return `
                  <a href="javascript:;" data-bs-toggle="offcanvas" data-bs-target="#editAccount" data-id="${data}">
                    <i class="ti ti-edit me-2"></i>
                  </a>
                  <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#deleteAccount" data-id="${data}">
                    <i class="ti ti-trash"></i>
                  </a>`;
              }
            }
          ],

          order: [[1,'desc']],
          responsive: true,
          dom:
            '<"row me-2"' +
            '<"col-md-2"l>' +
            '<"col-md-10 text-end"B>>' +
            't' +
            '<"row mx-2"' +
            '<"col-sm-12 col-md-6"i>' +
            '<"col-sm-12 col-md-6"p>>',

          buttons: [
            {
              text: '<i class="ti ti-plus"></i> Add Account',
              className: 'btn btn-primary btn-sm',
              attr: {
                'data-bs-toggle': 'offcanvas',
                'data-bs-target': '#addAccount'
              }
            }
          ]
        });
      }

    });

</script>


<script>
$(document).on('show.bs.offcanvas','#editAccount',function(e){
  let id = $(e.relatedTarget).data('id');
  $.post("{{ url('admin/facebook-accounts/edit') }}",{
    _token:"{{ csrf_token() }}",
    id:id
  },function(res){
    $('#edit_id').val(res.id);
    $('#edit_account_name').val(res.account_name);
    $('#edit_pixel_id').val(res.pixel_id);
    $('#edit_bm_id').val(res.business_manager_id);
    $('#edit_test_event_code').val(res.test_event_code);
    $('#edit_meta_pixel_base_code').val(res.meta_pixel_base_code);
    $('#edit_token').val(res.capi_access_token);
  });
});

$(document).on('show.bs.modal','#deleteAccount',function(e){
  $('#delete_id').val($(e.relatedTarget).data('id'));
});
</script>

<script>
$(document).on('show.bs.modal', '#actModal', function (e) {
    let id = $(e.relatedTarget).data('id');
    $('#actID').val(id);
});

$(document).on('show.bs.modal', '#deactModal', function (e) {
    let id = $(e.relatedTarget).data('id');
    $('#deactID').val(id);
});
</script>




@endsection
