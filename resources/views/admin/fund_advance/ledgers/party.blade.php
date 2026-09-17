@extends('layout.admin.admin_layout')

@section('title','Fund & Advance - Party Ledger')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    @include('admin.fund_advance.partials.subnav', ['active' => 'ledgers'])

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.fund_advance.ledgers.party') }}">Party Ledger</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.fund_advance.ledgers.employee') }}">Employee Ledger</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.fund_advance.ledgers.fund') }}">Fund Ledger</a></li>
    </ul>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Party Type</label>
                    <select name="party_type" id="party-type" class="form-select select2s">
                        <option value="partner" {{ $partyType === 'partner' ? 'selected' : '' }}>Partner</option>
                        <option value="contact" {{ $partyType === 'contact' ? 'selected' : '' }}>Contact</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Party</label>
                    <select name="party_id" id="party-id" class="form-select" required>
                        @if ($party)
                            <option value="{{ $party->id }}" selected>{{ $party->rec_off_name ?? $party->owner_name ?? $party->full_name }}</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date Range</label>
                    <input type="text" name="date_range" id="date-range" class="form-control" value="{{ request('date_range') }}" autocomplete="off">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">View Ledger</button>
                </div>
            </form>
        </div>
    </div>

    @if ($party)
        <div class="row g-3 mb-3">
            <div class="col-md-3"><div class="card card-body"><small class="text-muted">Opening Balance</small><h5>{{ number_format($openingBalance, 2) }}</h5></div></div>
            <div class="col-md-3"><div class="card card-body"><small class="text-muted">Total Debit</small><h5>{{ number_format($totalDebit, 2) }}</h5></div></div>
            <div class="col-md-3"><div class="card card-body"><small class="text-muted">Total Credit</small><h5>{{ number_format($totalCredit, 2) }}</h5></div></div>
            <div class="col-md-3"><div class="card card-body"><small class="text-muted">Closing Balance</small><h5>{{ number_format($closingBalance, 2) }}</h5></div></div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Ledger - {{ $party->rec_off_name ?? $party->owner_name ?? $party->full_name ?? $party->name }}</span>
                <a class="btn btn-sm btn-label-secondary" href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}"><i class="ti ti-download"></i> Export CSV</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Date</th><th>Reference</th><th>Type</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr></thead>
                    <tbody>
                        <tr class="table-light"><td colspan="5"><strong>Opening Balance</strong></td><td class="text-end"><strong>{{ number_format($openingBalance, 2) }}</strong></td></tr>
                        @forelse ($rows as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                                <td>{{ $row['reference'] }}</td>
                                <td>{{ $row['type'] }}</td>
                                <td class="text-end">{{ $row['debit'] ? number_format($row['debit'], 2) : '-' }}</td>
                                <td class="text-end">{{ $row['credit'] ? number_format($row['credit'], 2) : '-' }}</td>
                                <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4">No transactions in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info">Select a party to view their ledger.</div>
    @endif
</div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script>
    $(document).ready(function () {
        $('#date-range').daterangepicker({ autoUpdateInput: false, locale: { cancelLabel: 'Clear' } });
        $('#date-range').on('apply.daterangepicker', function (e, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }).on('cancel.daterangepicker', function () { $(this).val(''); });

        $('.select2s').each(function () {
            $(this).wrap('<div class="position-relative"></div>').select2({ dropdownParent: $(this).parent(), width: '100%' });
        });

        $('#party-type').on('change', function () { $(this).closest('form').submit(); });

        // Paginated, server-searched Party dropdown (20/page) reusing the same
        // fund_advance.parties.search endpoint the Transaction form's party picker
        // uses. Select2's own AJAX pagination (results appended on scroll) does the
        // heavy lifting; the "Load More" button just nudges that same scroll-driven
        // pipeline programmatically so it can be triggered by a click as well.
        var partyMoreAvailable = false;
        var partyLoadingMore = false;

        var $partySelect = $('#party-id').wrap('<div class="position-relative"></div>');
        $partySelect.select2({
            dropdownParent: $partySelect.parent(),
            placeholder: 'Search party...',
            minimumInputLength: 0,
            allowClear: false,
            ajax: {
                delay: 300,
                url: "{{ route('admin.fund_advance.parties.search') }}",
                dataType: 'json',
                cache: false,
                data: function (params) {
                    return {
                        party_type: $('#party-type').val(),
                        q: params.term || '',
                        page: params.page || 1,
                    };
                },
                processResults: function (data) {
                    partyMoreAvailable = !!(data.pagination && data.pagination.more);
                    partyLoadingMore = false;
                    updateLoadMoreButton();
                    return { results: data.results, pagination: { more: partyMoreAvailable } };
                },
            },
            language: {
                searching: function () { return 'Searching...'; },
                loadingMore: function () { return 'Loading more results...'; },
                noResults: function () { return 'No parties found.'; },
            },
        });

        $partySelect.on('select2:open', function () {
            partyMoreAvailable = false;
            partyLoadingMore = false;
            setTimeout(injectLoadMoreButton, 0);
        });

        function injectLoadMoreButton() {
            var $dropdown = $('.select2-container--open .select2-dropdown');
            if (!$dropdown.length) return;
            $dropdown.find('.fa-party-loadmore').remove();
            var $btn = $('<button type="button" class="fa-party-loadmore btn btn-sm btn-link w-100"></button>').text('Load More');
            $btn.on('mousedown', function (e) {
                e.preventDefault();
                e.stopPropagation();
                loadMoreParties();
            });
            $dropdown.append($btn);
            updateLoadMoreButton();
        }

        function updateLoadMoreButton() {
            var $btn = $('.select2-container--open .select2-dropdown .fa-party-loadmore');
            if (!$btn.length) return;
            $btn.toggle(partyMoreAvailable)
                .prop('disabled', partyLoadingMore)
                .text(partyLoadingMore ? 'Loading...' : 'Load More');
        }

        function loadMoreParties() {
            if (partyLoadingMore || !partyMoreAvailable) return;
            partyLoadingMore = true;
            updateLoadMoreButton();
            var $ul = $('.select2-container--open .select2-results__options');
            if ($ul.length) {
                $ul.scrollTop($ul[0].scrollHeight).trigger('scroll');
            }
        }
    });
    </script>
@endsection
