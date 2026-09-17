@extends('layout.admin.admin_layout')

@section('title','All Contact')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/pickr/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('user/intl-tel-input-master/build/css/intlTelInput.css') }}">
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Add Google Account Start -->
        <div class="modal fade" id="gmailAccountModal" tabindex="-1">
            <div class="modal-dialog">

               <form id="syncGoogleContactForm"
                    action="{{ route('admin.allcontact.google.redirect') }}"
                    method="GET"
                    style="display: none;">

                    <input type="hidden" name="sync_contact_by_gmail_id" value="true">
                    <input type="hidden" name="email" id="syncGoogleEmail">
                </form>

                <form action="{{ route('admin.allcontact.google.redirect') }}" method="GET">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">Select Careoff User to Sync Google Contacts</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                         <div class="modal-body">
                        

                            <div class="mb-3">

                                <label class="form-label">Careoff</label>
                                    <select
                                    class="selectpicker w-100 dynamic-filter load-filter"
                                    name="sync_contact_by_careof_id"
                                    data-filter="careoff"
                                    data-live-search="true"
                                    data-actions-box="true"
                                    data-style="default-btn"
                                    title="Select Careoff" required>
                                    </select>
                            </div>


                        </div>


                        <div class="modal-footer">
                            <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                                Close
                            </button>

                            <button type="submit"
                                class="btn btn-danger">
                                Continue with Google
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        <!-- Add Google Account Start -->

        <!-- Delete Google Account Start -->
        <div class="modal fade" id="deleteGoogleAccount" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Delete Google Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('admin.google-account.delete') }}" method="POST">
                        @csrf

                        <input type="hidden" name="google_account_id" id="deleteGoogleAccountId">

                        <div class="modal-body">
                            <p>Are you sure you want to delete this Google Account?</p>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">
                                Close
                            </button>

                            <button type="submit" class="btn btn-danger btn-sm">
                                Delete
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
        <!-- Delete Google Account End -->

        <div class="card">

            <div class="card-header border-bottom">

                <div class="mb-1 float-end">
                    <button type="button"
                        class="btn btn-sm btn-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#gmailAccountModal">
                        <i class="fab fa-google me-1"></i>
                        <span class="d-none d-sm-inline-block">
                            Add Gmail Account
                        </span>
                    </button>
                </div> 

            </div>

            <div class="card-datatable table-responsive contactpaginate">
                <table id="googleAccountsTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Careoff</th>
                            <th>Total Synced Contacts</th>
                            <th>Last Synced</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>
@endsection


@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ asset('user/intl-tel-input-master/build/js/intlTelInput-jquery.min.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/all-contact-validation.js') }}"></script>
    <script>
        let table;

        $(function () {

            $('#googleAccountsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.allcontact.sync-with-google') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'careoff', name: 'careoff' },
                    { data: 'total_contacts', name: 'total_contacts' },
                    { data: 'last_synced_at', name: 'last_synced_at' },
                    { data: 'action', name: 'action' }
                ]
            });


            $(document).on('click', '.syncGoogleContact', function () {

                $('#syncGoogleEmail').val($(this).data('email'));

                $('#syncGoogleContactForm').submit();

            });


            $(document).on('click', '.deleteGoogleAccount', function () {

                let id = $(this).data('id');

                $('#deleteGoogleAccountId').val(id);

            });


             $(document).on('click','.bootstrap-select .dropdown-toggle',function(){

                let dropdown = $(this).closest('.bootstrap-select').find('select.load-filter');

                if(
                    !dropdown.length
                ){
                    return;
                }

                if(
                    dropdown.attr(
                        'data-loaded'
                    )
                ){
                    return;
                }

                dropdown.attr('data-loaded','1');

                loadDropdown(
                    dropdown
                );

            }
        );

        function loadDropdown(
        dropdown
        ){

            console.log(
                'loadDropdown called'
            );

            $.ajax({

                url:
                "{{ route('admin.allcontact.filter.load') }}",

                type:"GET",

                data:{
                    filter:
                    dropdown.data(
                        'filter'
                    )
                },

                success:function(res){

                    dropdown
                    .html(res);

                    dropdown
                    .selectpicker(
                        'refresh'
                    );

                },

                error:function(){

                    dropdown
                    .removeAttr(
                        'data-loaded'
                    );

                }

            });

        }


        });
    </script>
@endsection
