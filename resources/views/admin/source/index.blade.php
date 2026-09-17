@extends('layout.admin.admin_layout')

@section('title', 'Source List')

@section('page-style')
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

  @if (Auth::guard('admin')->user()->user_type == 2)
    @if (isset($perm) && $perm->add_staff == 0)
      <style>.addcand { display: none !important; }</style>
    @endif
    @if (isset($perm) && $perm->edit_staff == 0)
      <style>.edcand { display: none !important; }</style>
    @endif
    @if (isset($perm) && $perm->delete_staff == 0)
      <style>.delcand { display: none !important; }</style>
    @endif
  @endif
@endsection

@section('content')

<div class="container-fluid flex-grow-1 container-p-y">
  <!-- Source Management Table -->
  <div class="card">
    
    <div class="card-datatable table-responsive">
      <table class="datatables-users table border-top">
        <thead>
          <tr>
            <th></th>
            <th>Name</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Actions</th>
          </tr>
        </thead>
      </table>
    </div>

    <!-- Offcanvas: Add Source -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddSource" aria-labelledby="offcanvasAddSourceLabel">
      <div class="offcanvas-header">
        <h5 id="offcanvasAddSourceLabel" class="offcanvas-title">Add Source</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
      </div>

      <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
        <form id="addSourceForm" class="add-new-user pt-0" action="{{ route('admin.source-management.store') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label" for="add-name">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="add-name" name="name" placeholder="Enter source name..." required />
          </div>

          <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="add-active" name="is_active" value="1" checked>
            <label class="form-check-label" for="add-active">Active</label>
          </div>

          <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
          <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </form>
      </div>
    </div>
    <!-- /Offcanvas: Add Source -->

    <!-- Offcanvas: Edit Source -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="editSource" aria-labelledby="editSourceLabel">
      <div class="offcanvas-header">
        <h5 id="editSourceLabel" class="offcanvas-title">Edit Source</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
      </div>

      <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
        <form id="editSourceForm" class="add-new-user pt-0" action="{{ route('admin.source-management.update') }}" method="POST">
          @csrf
          <input type="hidden" name="edit_id" id="editid">

          <div class="mb-3">
            <label class="form-label" for="edit-name">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="edit-name" name="name" placeholder="Enter source name..." required />
          </div>

          <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="edit-active" name="is_active" value="1">
            <label class="form-check-label" for="edit-active">Active</label>
          </div>

          <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Update</button>
          <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </form>
      </div>
    </div>
    <!-- /Offcanvas: Edit Source -->

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteSource" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Delete Source</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <form action="{{ route('admin.source-management.delete') }}" method="POST">
            @csrf
            <input type="hidden" name="source_id" id="delSourceID">
            <div class="modal-body">
              <p>Are you sure you want to delete this source?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- /Delete Modal -->
  </div>
</div>

@endsection

@section('page-script')
  <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
  <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>

  <!-- Page JS -->
  <script src="{{ asset('admin/assets/pages/app-source-list.js') }}"></script>
  <script src="{{ asset('admin/assets/pages/validation/source-validation.js') }}"></script>

  <script>
    $(document).ready(function () {
      // Edit Offcanvas
      $('#editSource').on('show.bs.offcanvas', function (e) {
        const editID = $(e.relatedTarget).data('id');
        $.ajax({
          url: '{{ url('admin/source-management/edit') }}',
          method: 'POST',
          data: { id: editID, _token: '{{ csrf_token() }}' },
          success: function (data) {
            $('#editid').val(data.id);
            $('#edit-name').val(data.name);
            $('#edit-active').prop('checked', data.is_active == 1);
          }
        });
      });

      // Delete Modal
      $('#deleteSource').on('show.bs.modal', function (e) {
        const sourceID = $(e.relatedTarget).data('id');
        $('#delSourceID').val(sourceID);
      });
    });
  </script>
@endsection
