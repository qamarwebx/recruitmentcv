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
                @include('admin.sync_contact_to_lead.load')
            </div>
        </div>


    </div>
@endsection

@php

$businessType = !empty($allcontactsaveadminfilter?->lead_type)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->lead_type))
    : [];

$careoffId = !empty($allcontactsaveadminfilter?->careoff_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->careoff_id))
    : [];

$createdBy = !empty($allcontactsaveadminfilter?->user_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->user_id))
    : [];

$groupId = !empty($allcontactsaveadminfilter?->group_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->group_id))
    : [];

$lcsId = !empty($allcontactsaveadminfilter?->lcs_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->lcs_id))
    : [];

$lsId = !empty($allcontactsaveadminfilter?->ls_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->ls_id))
    : [];

$leadPriority = !empty($allcontactsaveadminfilter?->lead_prority)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->lead_prority))
    : [];

$industId = !empty($allcontactsaveadminfilter?->indust_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->indust_id))
    : [];

$stateId = !empty($allcontactsaveadminfilter?->state_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->state_id))
    : [];

$countryId = !empty($allcontactsaveadminfilter?->country_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->country_id))
    : [];

$conversation_type = !empty($allcontactsaveadminfilter?->conversation_type)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->conversation_type))
    : [];

$cityId = !empty($allcontactsaveadminfilter?->city_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->city_id))
    : [];

$ownerId = !empty($allcontactsaveadminfilter?->owner_id)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->owner_id))
    : [];

$country_dial_code = !empty($allcontactsaveadminfilter?->country_dial_code)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->country_dial_code))
    : [];

$country_dial_code_number = !empty($allcontactsaveadminfilter?->country_dial_code_number)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->country_dial_code_number))
    : [];

$exlude_country_mobile_code = !empty($allcontactsaveadminfilter?->exlude_country_mobile_code)
    ? array_map('trim', explode(',', $allcontactsaveadminfilter->exlude_country_mobile_code))
    : [];

@endphp

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
                    url: "{{ route('admin.sync_contact_to_lead.json') }}",
                    type: "GET"
                },

                columns: [
                    {
                        data: 'allcontact_id',
                        name: 'allcontact_id'
                    },
                    {
                        data: 'lead_id',
                        name: 'lead_id'
                    },
                    {
                        data: 'primary_no_wsp',
                        name: 'primary_no_wsp'
                    }
                ],
            });

        }); 
    </script>
 
@endsection
