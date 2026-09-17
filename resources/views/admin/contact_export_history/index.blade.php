@extends('layout.admin.admin_layout')

@section('title','All Contact Export History')

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

    <style>

        .pagestyle{
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            /* color: #495057; */
            /* background-color: #fff; */
            background-clip: padding-box;
            /* border: 1px solid #ced4da; */
            border-radius: .25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out
        }

        .pagestyle:focus{
            /* color: #6e6b7b; */
            /* background-color: #fff; */
            /* border-color: #7367f0; */
            outline: 0;
            /* box-shadow: 0 3px 10px 0 rgba(34, 41, 47, 0.1); */
        }
        .shortformmargin{
            /* margin-right: 195px; */
        }

        .filter-indicator {
            position: absolute;
            top: 1px;
            right: 2px;
            width: 8px;
            height: 8px;
            background: #ffc107;
            border-radius: 50%;
        }
    </style>

    <style>
        .report-scroll{
            max-height:70vh;      /* Only table data scrolls */
            overflow-y:auto;
            overflow-x:auto;
        }

        .report-scroll table{
            white-space:nowrap;
            margin-bottom:0;
        }

        .report-scroll thead th{
            position:sticky;
            top:0;
            z-index:3;
            background:#343a40;
            color:#fff;
        }

        /* Keep Group column visible while horizontal scrolling */
        .report-scroll .sticky-col{
            position:sticky;
            left:0;
            z-index:2;
            background:#fff;
        }

        .report-scroll thead .sticky-col{
            z-index:4;
            background:#343a40;
            color:#fff;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="card">
            <div class="card-datatable table-responsive contactpaginate">
                @include('admin.contact_export_history.load')
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
    <script>
        $(function () {

            $('.datatables-users').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                destroy: true,
                pageLength: 10,

                ajax: {
                    url: "{{ route('admin.contact_export_history.json') }}",
                    type: "GET"
                },

               columns: [
                    {
                        data: 'file_name',
                        name: 'file_name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'progress',
                        name: 'progress',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

        }); 


        $(document).on('click', '.delete-export', function () {

            if (!confirm('Are you sure you want to delete this export?')) {
                return;
            }

            let id = $(this).data('id');

            $.ajax({
                url: '/admin/contact-export-history/' + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('.datatables-users').DataTable().ajax.reload(null, false);
                    toastr.success(response.message);
                },
                error: function () {
                    toastr.error('Something went wrong.');
                }
            });

        });
    </script>
 
@endsection
