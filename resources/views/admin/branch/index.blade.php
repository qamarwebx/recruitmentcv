@extends('layout.admin.admin_layout')

@section('title','Branch')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

    @if (Auth::guard('admin')->user()->user_type == 2)
        @if (isset($perm) && $perm->add_branch == 0)
        <style>
            .addbranch{
            display: none !important;
            }
        </style>
        @endif
        @if (isset($perm) && $perm->edit_branch == 0)
        <style>
            .edbranch{
            display: none !important;
            }
        </style>
        @endif
        @if (isset($perm) && $perm->delete_branch == 0)
        <style>
            .delbranch{
            display: none !important;
            }
        </style>
        @endif
    @endif

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Branch List Table -->
        <div class="card">
            <h5 class="card-header">Branch</h5>
            <div class="card-datatable table-responsive">
                <table class="datatables-branch table border-top">
                <thead>
                    <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                    </tr>
                </thead>
                </table>
            </div>
            <!-- Offcanvas to add new branch -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddBranch" aria-labelledby="offcanvasAddBranchLabel">
                <div class="offcanvas-header">
                <h5 id="offcanvasAddBranchLabel" class="offcanvas-title">Add Branch</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-branch pt-0" id="addNewBranchForm" action="{{ route('admin.branch.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                    <label class="form-label" for="add-branch-name">Branch Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="add-branch-name" placeholder="Enter branch name..." name="name"/>
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
                </div>
            </div>
            <!-- Offcanvas to edit branch -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="editbranch" aria-labelledby="editbranchLabel">
                <div class="offcanvas-header">
                <h5 id="editbranchLabel" class="offcanvas-title">Edit Branch</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-branch pt-0" id="editBranchForm" action="{{ route('admin.branch.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                    <input type="hidden" name="edit_id" id="editid">
                    <label class="form-label" for="edit-branch-name">Branch Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="edit-branch-name" placeholder="Enter branch name..." name="name"/>
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
                </div>
            </div>
            <!-- Delete Branch start -->
            <div class="modal fade" id="deleteBranch" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Delete Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.branch.delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="branch_ids" id="delBranchID">
                    <div class="modal-body">
                        <div class="row">
                        <div class="col mb-12">
                            <p>Are you sure, to delete this branch?</p>
                        </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Delete</button>
                    </div>
                    </form>
                </div>
                </div>
            </div>
            <!-- Delete Branch end -->
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
    <script src="{{ asset('admin/assets/pages/app-branch-list.js') }}"></script>

    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/branch-validation.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('#editbranch').on('show.bs.offcanvas',function(e){
                var editID = $(e.relatedTarget).data('id');
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url('admin/branch/edit') }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#editid').val(data.id);
                        $('#edit-branch-name').val(data.name);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function(){
      $('#deleteBranch').on('show.bs.modal',function(e){
        var branch_id =  $(e.relatedTarget).data('id');
        $('#delBranchID').val(branch_id);
        // check already in use by a Google Review before allowing delete
        $.ajaxSetup({
          headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        jQuery.ajax({
          url : '{{ url('admin/branch/check/exist') }}',
          method: "POST",
          type: "html",
          data: {
            "id": branch_id,
            "_token": "{{ csrf_token() }}",
          },
          success: function(data){
            if (data) {
              $('#disbtn').prop('disabled',true);
            } else {
              $('#disbtn').prop('disabled',false);
            }
          }
        });
      });
        });
    </script>

@endsection
