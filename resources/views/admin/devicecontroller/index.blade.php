@extends('layout.admin.admin_layout')

@section('title', 'Device Management')

@section('page-style')
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
<style>
    .pagestyle {
        height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
        font-size: 1rem;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        transition: all 0.2s ease-in-out;
    }
    .pagestyle:focus {
        border-color: #7367f0;
        box-shadow: 0 3px 10px rgba(34, 41, 47, 0.1);
    }

   /* Dropdown inside Modal fix */
    .modal .dropdown-menu {
        position: absolute !important;
        inset: auto auto auto 0 !important; /* reset positioning */
        transform: translate3d(0, 0, 0) !important;
        z-index: 9999 !important; /* modal ke upar dikhne ke liye */
    }

    /* Ensure modal content doesn't hide dropdown */
    .modal-dialog,
    .modal-content {
        overflow: visible !important;
    }

</style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <select id="pagination_list" class="pagestyle w-auto">
                @foreach([10,25,50,100,150,200,250,500,1000] as $num)
                    <option value="{{ $num }}">{{ $num }}</option>
                @endforeach
            </select>
            <div class="d-flex align-items-center">
                <label class="switch switch-primary me-3">
                    <input type="checkbox" class="switch-input" id="approveAllLogin" name="approve_all_login" {{ $admin->approve_all_login ? 'checked' : '' }}>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                    <span class="switch-label">Approve All Login</span>
                </label>
            </div>
            <input type="text" id="search_text" class="form-control w-25" placeholder="Search...">
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-users table border-top">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="deviceTableBody">
                        @include('admin.devicecontroller.load', ['devices' => $devices])
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Admin Activity Modal -->
<div class="modal fade" id="adminActivityModal" aria-hidden="true" aria-labelledby="adminActivityModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Admin Device Activity</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <p>Loading...</p>
      </div>
    </div>
  </div>
</div>

<!-- Approved By Modal -->
<div class="modal fade" id="approvedByModal" aria-hidden="true" aria-labelledby="approvedByModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Approved By Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group">
          <li class="list-group-item"><strong>Name:</strong> <span id="approvedByName"></span></li>
          <li class="list-group-item"><strong>Device:</strong> <span id="approvedByDevice"></span></li>
          <li class="list-group-item"><strong>Browser:</strong> <span id="approvedByBrowser"></span></li>
          <li class="list-group-item"><strong>OS:</strong> <span id="approvedByOS"></span></li>
          <li class="list-group-item"><strong>IP:</strong> <span id="approvedByIP"></span></li>
          <li class="list-group-item"><strong>Location:</strong> <span id="approvedByLocation"></span></li>
          <li class="list-group-item"><strong>Live:</strong> <span id="approvedByLive"></span></li>
          <li class="list-group-item"><strong>Comments:</strong> <span id="approvedByComments"></span></li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function() {
    /** 🔹 View Admin Activity */
    $(document).on('click', '.viewAdminBtn', function() {
        const adminId = $(this).data('admin-id');
        const modalBody = $('#adminActivityModal .modal-body');

        $('#adminActivityModal').modal('show');
        modalBody.html('<p class="text-center text-muted my-3">Loading...</p>');

        $.ajax({
            url: `/admin/device/activity/${adminId}`,
            type: 'GET',
            success: res => {
                modalBody.html(res);

                // Paginate the activity table (modal is reused across admins,
                // so destroy any previous instance before re-initializing).
                const $activityTable = modalBody.find('#adminActivityTable');
                if ($activityTable.length) {
                    if ($.fn.DataTable.isDataTable($activityTable[0])) {
                        $activityTable.DataTable().destroy();
                    }
                    $activityTable.DataTable({
                        pageLength: 10,
                        lengthMenu: [10, 25, 50, 100],
                        order: []
                    });
                }

                // Initialize dropdowns inside modal dynamically
                modalBody.find('.dropdown-toggle').each(function() {
                    new bootstrap.Dropdown(this, {
                        popperConfig(defaultConfig) {
                            return {
                                ...defaultConfig,
                                modifiers: [
                                    {
                                        name: 'preventOverflow',
                                        options: { boundary: modalBody[0] }
                                    },
                                    {
                                        name: 'flip',
                                        options: {
                                            fallbackPlacements: ['top', 'bottom']
                                        }
                                    }
                                ]
                            };
                        }
                    });
                });

            },
            error: () => modalBody.html('<p class="text-danger text-center">Error loading activity.</p>')
        });
    });

    /** 🔹 Approved By Modal */
    $(document).on('click', '.approved-by-btn', function() {
        $('#approvedByName').text($(this).data('approved-by'));
        $('#approvedByDevice').text($(this).data('device-type'));
        $('#approvedByBrowser').text($(this).data('browser'));
        $('#approvedByOS').text($(this).data('os'));
        $('#approvedByIP').text($(this).data('ip'));
        $('#approvedByLocation').text($(this).data('location'));

        // ✅ New line for Live Status
        $('#approvedByLive').text($(this).data('live'));

        $('#approvedByComments').text($(this).data('comments'));
        $('#approvedByModal').modal('show');
    });

   /** 🔹 Approve Device */
    $(document).on('click', '.btn-approve', function() {
        const id = $(this).data('id');
        const $row = $(`tr[data-id="${id}"]`);
        const $statusTd = $row.find('td').eq(7);
        const $actionTd = $row.find('td').eq(8);
        const $btn = $(this);

        // 🕓 Show loading state before request
        $statusTd.html('<span class="badge bg-label-secondary text-dark">Loading...</span>');
        $btn.prop('disabled', true).text('Approving...');

        $.post('{{ route("admin.device.approve") }}', { id, _token: '{{ csrf_token() }}' }, function(res) {
            toastr.success(res.message);

            // ✅ Update status
            $statusTd.html('<span class="badge bg-label-success bg-label-success">Approved</span>');

            // 🔁 Replace Approve → Revoke button only
            $btn
                .removeClass('btn-approve')
                .addClass('btn-revoke')
                .html('<i class="ti ti-x ti-sm"></i> Revoke')
                .prop('disabled', false);

        }).fail(function() {
            toastr.error('Something went wrong. Please try again.');
            $statusTd.html('<span class="badge bg-label-warning text-dark">Pending</span>');
            $btn.prop('disabled', false).html('<i class="ti ti-check ti-sm"></i> Approve');
        });
    });

    /** 🔹 Revoke Device */
    $(document).on('click', '.btn-revoke', function() {
        const id = $(this).data('id');
        const $row = $(`tr[data-id="${id}"]`);
        const $statusTd = $row.find('td').eq(7);
        const $btn = $(this);

        // 🕓 Show loading state before request
        $statusTd.html('<span class="badge bg-label-secondary text-dark">Loading...</span>');
        $btn.prop('disabled', true).text('Revoking...');

        $.post('{{ route("admin.device.revoke") }}', { id, _token: '{{ csrf_token() }}' }, function(res) {
            toastr.info(res.message);

            // 🔄 Update status
            $statusTd.html('<span class="badge bg-label-warning text-dark">Pending</span>');

            // 🔁 Replace Revoke → Approve button only
            $btn
                .removeClass('btn-revoke')
                .addClass('btn-approve')
                .html('<i class="ti ti-check ti-sm"></i> Approve')
                .prop('disabled', false);

        }).fail(function() {
            toastr.error('Something went wrong. Please try again.');
            $statusTd.html('<span class="badge bg-label-success bg-label-success">Approved</span>');
            $btn.prop('disabled', false).html('<i class="ti ti-x ti-sm"></i> Revoke');
        });
    });

    /** 🔹 Delete Device */
    $(document).on('click', '.btn-delete', function() {
        if (!confirm('Are you sure you want to delete this device?')) return;
        const id = $(this).data('id');
        $.post('{{ route("admin.device.delete") }}', { id, _token: '{{ csrf_token() }}' }, function(res) {
            toastr.error(res.message);
            $(`tr[data-id="${id}"]`).fadeOut(300, function() { $(this).remove(); });
        });
    });

    // Copy IP to clipboard
    $(document).on('click', '.copy-ip', function() {
        const ip = $(this).data('ip');
        navigator.clipboard.writeText(ip).then(() => {
            toastr.success('IP Address copied: ' + ip);
        }).catch(() => {
            toastr.error('Failed to copy IP address.');
        });
    });

    $(document).on('change', '#approveAllLogin', function() {
        const isChecked = $(this).is(':checked');

        $.ajax({
            url: "{{ route('admin.device.toggleApproveAllLogin') }}",
            type: "POST",
            data: {
                approve_all_login: isChecked,
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                if (res.status) {
                    toastr.success(res.message);
                } else {
                    toastr.error('Failed to update setting.');
                }
            },
            error: function() {
                toastr.error('Something went wrong.');
            }
        });
    });

    /** 🔍 AJAX Search using SAME route */
    let searchTimeout;
    $(document).on('keyup', '#search_text', function () {
        let search = $(this).val();

        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(function () {

            $.ajax({
                url: "{{ route('admin.device.index') }}", // 👈 SAME route
                type: "GET",
                data: { search: search },

                beforeSend: function () {
                    $('#deviceTableBody').html(
                        '<tr><td colspan="3" class="text-center">Searching...</td></tr>'
                    );
                },

                success: function (res) {
                    $('#deviceTableBody').html(res);
                },

                error: function () {
                    $('#deviceTableBody').html(
                        '<tr><td colspan="3" class="text-danger text-center">Error loading data</td></tr>'
                    );
                }
            });

        }, 400);
    });

});

    document.addEventListener("shown.bs.modal", function () {
        document.querySelectorAll('.modal .dropdown-toggle').forEach(function (el) {
            new bootstrap.Dropdown(el, {
                popperConfig: function (defaultBsPopperConfig) {
                    return {
                        ...defaultBsPopperConfig,
                        modifiers: [{
                            name: 'preventOverflow',
                            options: {
                                boundary: document.querySelector('.modal-dialog') // modal ke andar hi rakho
                            }
                        }]
                    }
                }
            });
        });
    });


</script>
@endsection
