@extends('layout.admin.admin_layout')

@section('title','Expense List')

@section('page-style')

    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/dropzone/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/pickr/pickr-themes.css') }}" />

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

        .filter-indicator {
            position: absolute;
            top: 1px;
            right: 2px;
            width: 8px;
            height: 8px;
            background: #ffc107;
            border-radius: 50%;
        }

        /* Expense summary status cards - same design as All Contacts / Leads / Testimonials */
        .candidate-status-wrapper {
            width: 100%;
        }

        .candidate-status-card {
            background: var(--bs-card-bg);
            padding: 10px;
            border-radius: 8px;
        }

        .candidate-status-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .status-item {
            flex: 0 0 auto;
        }

        .status-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            min-height: 28px;
            font-size: 11px;
            white-space: nowrap;
            border: 1px solid;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
        }

        .status-btn strong {
            font-size: 16px;
            line-height: 1;
        }

        .status-btn:hover {
            background: rgba(115, 103, 240, 0.05);
            text-decoration: none;
        }

        .status-btn.expense-status-card.active {
            background: #7367F0;
            color: #fff !important;
        }

        .status-btn.expense-status-card.active span,
        .status-btn.expense-status-card.active strong {
            color: #fff !important;
        }

        @media (max-width: 991px) {
            .status-item {
                flex: 0 0 calc(25% - 10px);
            }
        }

        @media (max-width: 767px) {
            .status-item {
                flex: 0 0 calc(50% - 10px);
            }
        }
    </style>

@endsection

@section('content')

    <div class="container-fluid flex-grow-1 container-p-y">

        <!-- Expense Status Cards Start -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="candidate-status-wrapper">
                    <div class="candidate-status-card">
                        <div class="candidate-status-grid">

                            @php
                                $expenseStatusCards = [
                                    'total'          => ['title' => 'Total Expenses',  'color' => '#6C757D'],
                                    'this_month'     => ['title' => 'This Month',      'color' => '#7367F0'],
                                    'recently_added' => ['title' => 'Recently Added',  'color' => '#0D6EFD'],
                                ];

                                $paymentModeColors = [
                                    'Cash'        => '#28C76F',
                                    'UPI'         => '#00CFE8',
                                    'Netbanking'  => '#FF9F43',
                                    'Cheque'      => '#EA5455',
                                ];
                            @endphp

                            @foreach($expenseStatusCards as $key => $card)
                                <div class="status-item">
                                    <a href="javascript:void(0)"
                                       class="status-btn expense-status-card"
                                       data-type="metric"
                                       data-card="{{ $key }}"
                                       style="border-color:{{ $card['color'] }};color:{{ $card['color'] }};">
                                        <span>{{ $card['title'] }}</span>
                                        <strong id="expense_count_{{ $key }}">{{ number_format($summaryCounts[$key] ?? 0) }}</strong>
                                    </a>
                                </div>
                            @endforeach

                            @foreach($paymentModeColors as $mode => $color)
                                <div class="status-item">
                                    <a href="javascript:void(0)"
                                       class="status-btn expense-status-card"
                                       data-type="payment_mode"
                                       data-value="{{ $mode }}"
                                       style="border-color:{{ $color }};color:{{ $color }};">
                                        <span>{{ $mode }}</span>
                                        <strong id="expense_count_pm_{{ \Illuminate\Support\Str::slug($mode) }}">{{ number_format($summaryCounts['payment_mode'][$mode] ?? 0) }}</strong>
                                    </a>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Expense Status Cards End -->

        <!-- Users List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <div class="mb-1 float-start">
                    {{-- <label for="">Show</label> --}}
                    <select id="pagination_list" class="form-select form-select-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                    </select>
                </div>
                <div class="px-3 float-start">
                    <button class="btn btn-xs btn-primary filterpanel position-relative" data-bs-toggle="modal" data-bs-target="#filterpanel"><i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter
                        <span class="filter-indicator d-none"></span>
                    </button>
                </div>
                <div class="mb-1 float-end">

                    {{-- <div class="btn-group mx-2">
                        <button class="btn btn-primary btn-sm dropdown-toggle bulkactions" type="button" disabled id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Bulk Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfergroup"><i class="ti ti-users me-2"></i> Transfer To Group</a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfercareoff"><i class="ti ti-user me-2"></i> Transfer To Careoff</a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransferleadowner"><i class="ti ti-user me-2"></i> Transfer To Lead Owner</a>
                            <a class="dropdown-item bulcontactwhpsend" href="#" data-bs-toggle="offcanvas" data-bs-target="#bulkwhatsappsend"><i class="ti ti-brand-whatsapp me-2"></i> Send Whatsapp</a>
                            <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkdelete"><i class="ti ti-trash me-2"></i> Delete</a>
                        </div>
                    </div> --}}

                    @if ((Auth::guard('admin')->user()->user_type == 1) || (isset($adminpermission) && $adminpermission->add_expense == 1))
                        <button class="add-new btn btn-sm btn-primary addcontact addcandidate mx-2" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Expense</span></button>
                    @endif


                </div>

                <div class="mb-1 float-end">
                    <input type="text" id="search_text" class="form-control" placeholder="Search...">
                </div>
            </div>
            <div class="card-datatable table-responsive contactpaginate">
                @include('admin.expense.indexload')
            </div>
        </div>


        <!-- Add Expense Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Expense</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form action="{{ route('admin.expense.store') }}" id="addUserForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-expense-for" class="form-label">Expense For <span class="text-danger">*</span></label>
                                <select name="expensefor_id" id="add-expense-for" class="form-select select2e" data-placeholder="Select Expense For" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($expensefors as $expensefor)
                                        <option value="{{ $expensefor->id }}">{{ $expensefor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="add-name" class="form-control" placeholder="Enter Expense Name">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-expence-category-id" class="form-label">Expense Category <span class="text-danger">*</span></label>
                                <select name="expensecat_id" id="add-expence-category-id" class="form-select select2e" data-allow-clear="true" data-placeholder="Select Category">
                                    <option value="">Select</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="text" name="amount" id="add-amount" class="form-control" placeholder="Enter amount...">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-paid-to" class="form-label">Payment To</label>
                                <input type="text" name="paid_to" id="add-paid-to" class="form-control" placeholder="Enter Shop or Person Name...">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-expense-date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                                <input type="text" name="expense_date" id="add-expense-date" class="form-control expdate" placeholder="Enter date...">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-payment-mode" class="form-label">Payment Mode</label>
                                <select name="payment_mode" class="form-select select2e" id="add-payment-mode" data-allow-clear="true" data-placeholder="Select Payment Mode">
                                    <option value="">Select</option>
                                    <option value="Cash">Cash</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Netbanking">Netbanking</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-payment-from" class="form-label">Payment From</label>
                                <input type="text" name="payment_from" id="add-payment-from" placeholder="Please enter payment from" class="form-control" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-utr-reference-no" class="form-label">Transaction No</label>
                                <input type="text" name="reference_no" id="add-utr-reference-no" class="form-control" placeholder="Enter Transaction No...">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-notes" class="form-label">Notes</label>
                                <textarea name="notes" id="add-notes" class="form-control" cols="30" rows="5"></textarea>
                            </div>

                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-receipt-file" class="form-label">Receipt</label>
                                <input type="file" name="receipt_file[]" class="form-control" id="add-receipt-file" multiple>
                            </div>
                        </div>

                    </div>
                    {{-- <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editnewcontact">Submit</button> --}}
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>

                </form>
            </div>
        </div>
        <!-- Add Expense End -->

        <!-- Edit Expenses Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="updateReg" aria-labelledby="updateRegLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="updateRegLabel" class="offcanvas-title">Edit Contact</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form action="{{ route('admin.expense.update') }}" id="editUserForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="editID" id="edit_ID">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-expense-for" class="form-label">Expense For <span class="text-danger">*</span></label>
                                <select name="expensefor_id" id="edit-expense-for" class="form-select select2e" data-placeholder="Select Expense For" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($expensefors as $expensefor)
                                        <option value="{{ $expensefor->id }}">{{ $expensefor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit-name" class="form-control" placeholder="Enter Expense Name">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-expence-category-id" class="form-label">Expense Category <span class="text-danger">*</span></label>
                                <select name="expensecat_id" id="edit-expence-category-id" class="form-select select2e" data-allow-clear="true" data-placeholder="Select Category">
                                    <option value="">Select</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="text" name="amount" id="edit-amount" class="form-control" placeholder="Enter amount...">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-paid-to" class="form-label">Payment To</label>
                                <input type="text" name="paid_to" id="edit-paid-to" class="form-control" placeholder="Enter Shop or Person Name...">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-expense-date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                                <input type="text" name="expense_date" id="edit-expense-date" class="form-control expdate" placeholder="Enter date...">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-payment-mode" class="form-label">Payment Mode</label>
                                <select name="payment_mode" class="form-select select2e" id="edit-payment-mode" data-allow-clear="true" data-placeholder="Select Payment Mode">
                                    <option value="">Select</option>
                                    <option value="Cash">Cash</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Netbanking">Netbanking</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-payment-from" class="form-label">Payment From</label>
                                <input type="text" name="payment_from" id="edit-payment-from" placeholder="Please enter payment from" class="form-control" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-utr-reference-no" class="form-label">Transaction No</label>
                                <input type="text" name="reference_no" id="edit-utr-reference-no" class="form-control" placeholder="Enter Transaction No...">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-notes" class="form-label">Notes</label>
                                <textarea name="notes" id="edit-notes" class="form-control" cols="30" rows="5"></textarea>
                            </div>

                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-receipt-file" class="form-label">Receipt</label>
                                <input type="file" name="receipt_file[]" class="form-control" id="edit-receipt-file" multiple>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Uploaded Receipts</label>
                                <div id="edit-receipt-preview" class="d-flex flex-wrap gap-3"></div>
                            </div>
                        </div>


                    </div>


                    {{-- <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editnewcontact">Submit</button> --}}
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>

                </form>
            </div>
        </div>
        <!-- Edit Expenses End -->

        <!-- View Expense Modal Start -->
        <div class="modal fade modal_type" id="viewExpenseModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" >
            <div class="modal-dialog modal-xl">
                <div class="modal-content pt-0">

                    <div class="modal-header mb-1">
                        <h5 id="e" class="modal-title">Expense Details</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div id="expenseViewContent">
                            <p class="text-center text-muted">Loading...</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- View Expense Modal End -->


        <!-- Filter Panel Start -->
        <div class="modal fade" id="filterpanel" aria-hidden="true" aria-labelledby="filterpanelLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="filterpanelLabel">Expense Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>


                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <select name="" id="by-expense-for" class="selectpicker w-100" data-actions-box="true" data-live-search="true" data-style="default-btn" title="Expense For" multiple>
                                    @foreach ($expenseforFields as $expenseforField)
                                        <option value="{{ $expenseforField->expensefor->id }}" @if(isset($expensesaveadminfilter) && in_array($expenseforField->expensefor->id,explode(",",$expensesaveadminfilter->expensefor))) selected @endif>{{ $expenseforField->expensefor->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select name="" id="by-expense-cat" class="selectpicker w-100" data-actions-box="true" data-live-search="true" data-style="default-btn" title="Expense Type" multiple>
                                    @foreach ($expensetypeFields as $expensetypeField)
                                        <option value="{{ $expensetypeField->expensecat->id }}" @if(isset($expensesaveadminfilter) && in_array($expensetypeField->expensecat->id,explode(",",$expensesaveadminfilter->expensetype))) selected @endif>{{ $expensetypeField->expensecat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select name="" id="by-payment-mode" class="selectpicker w-100" data-actions-box="true" data-live-search="true" data-style="default-btn" title="Payment Mode" multiple>
                                    @foreach ($paymentmodeFields as $paymentmodeField)
                                        <option value="{{ $paymentmodeField->payment_mode }}" @if(isset($expensesaveadminfilter) && in_array($paymentmodeField->payment_mode,explode(",",$expensesaveadminfilter->payment_mode))) selected @endif>{{ $paymentmodeField->payment_mode }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select name="" id="by-create-by" class="selectpicker w-100" data-actions-box="true" data-live-search="true" data-style="default-btn" title="Create By" multiple>
                                    @foreach ($createbyFields as $createbyField)
                                        <option value="{{ $createbyField->admin->id }}" @if(isset($expensesaveadminfilter) && in_array($createbyField->admin->id,explode(",",$expensesaveadminfilter->createby))) selected @endif>{{ $createbyField->admin->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <input type="text" name="payment_date" id="by-payment-date" @if(isset($expensesaveadminfilter)) value="{{ $expensesaveadminfilter->payment_date }}" @endif class="form-control createdate-picker bsdatpicket" placeholder="Payment date with range...">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="button" class="btn btn-sm btn-primary applyfilter">Apply Filter</button> --}}
                        <button type="button" class="btn btn-warning btn-sm resetfilter">Reset Filter</button>
                        <button type="button" class="btn btn-success btn-sm savetodoFilter">Save Filter</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filter Panel End -->

        <!-- Delete Expense Start -->
        <div class="modal fade" id="deleteStaff" aria-hidden="true" aria-labelledby="deleteStaffLabel" tabindex="-1">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="deleteStaffLabel">Expense Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.expense.delete') }}" method="POST">
                        @csrf
                        <input type="hidden" name="delete_id" id="deleteID">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-danger">Are you sure to delete the expense?</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">No</button>
                            <button type="submit" class="btn btn-success btn-sm">Yes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Expense End -->

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
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>

    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/pickr/pickr.js') }}"></script>

    <script src="{{ asset('admin/assets/js/forms-selects.js') }}"></script>

    <script src="{{ asset('admin/assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/quill/quill.js') }}"></script>

    <!-- Page JS -->
    {{-- <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script> --}}
    <script src="{{ asset('admin/assets/pages/validation/expense-validation.js') }}"></script>

    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
    {{-- <script src="{{ asset('admin/assets/js/forms-file-upload.js') }}"></script> --}}

    <script>
        $(document).ready(function(){
            const select2e = $('.select2e');
            const expdate = $('.expdate');

            if (select2e.length) {
                select2e.each(function(){
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        dropdownParent: $this.parent()
                    });
                });
            }

            if (expdate.length) {
                expdate.flatpickr();
            }

            var bsRangePickerBasic = $('.bsdatpicket');
            if (bsRangePickerBasic.length) {
                bsRangePickerBasic.daterangepicker({
                    // todayHighlight: true,
                    opens: isRtl ? 'left' : 'right',
                    autoUpdateInput: false,

                    locale: {
                        cancelLabel: 'Clear'
                    }
                });
            }

            // View Expense
            $(document).on('click', '.viewExpenseBtn', function () {

                const expenseId = $(this).data('id');

                // Build Laravel route URL dynamically
                const url = "{{ route('admin.expense.view', ':id') }}".replace(':id', expenseId);

                $('#viewExpenseModal').modal('show');
                $('#expenseViewContent').html('<p class="text-muted my-3 text-center text-center">Loading...</p>');

                $.ajax({
                    url: url,
                    type: "GET",
                    success: function (response) {
                        $('#expenseViewContent').html(response);
                    },
                    error: function () {
                        $('#expenseViewContent').html('<p class="text-danger text-center">Error loading data</p>');
                    }
                });

            });

            // Edit Expenses
            $('#updateReg').on("show.bs.offcanvas", function (e) {

                let editID = $(e.relatedTarget).data('id');
                $('#edit_ID').val(editID);

                $.ajax({
                    url: '{{ route("admin.expense.edit") }}',
                    method: "GET",
                    data: { id: editID },
                    success: function (res) {

                        let data = res.expense;
                        let files = res.files;

                        // Fill form fields
                        $('#edit-name').val(data.name);
                        $('#edit-expence-category-id').val(data.expensecat_id).trigger('change');
                        $('#edit-amount').val(data.amount);
                        $('#edit-paid-to').val(data.paid_to);
                        $('#edit-expense-date').val(data.expense_date);
                        $('#edit-payment-mode').val(data.payment_mode).trigger('change');
                        $('#edit-payment-from').val(data.payment_from);
                        $('#edit-utr-reference-no').val(data.reference_no);
                        $('#edit-notes').val(data.notes);
                        $('#edit-expense-for').val(data.expensefor_id).trigger('change');

                        // Render receipt preview
                        let previewHtml = '';

                        files.forEach(function (file) {

                            let ext = file.split('.').pop().toLowerCase();
                            let fileUrl = "{{ asset('admin/assets/images/payment/expense') }}/" + file;

                            previewHtml += `
                                <div class="position-relative border p-2 rounded" style="width:120px">
                                    <button type="button"
                                            class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-receipt"
                                            data-file="${file}"
                                            data-id="${editID}">
                                        &times;
                                    </button>`;

                            if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
                                previewHtml += `<img src="${fileUrl}" class="img-fluid">`;
                            } else {
                                previewHtml += `
                                    <a href="${fileUrl}" target="_blank">
                                        <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                    </a>`;
                            }

                            previewHtml += `</div>`;
                        });

                        $('#edit-receipt-preview').html(previewHtml);
                    }
                });
            });

            // Remove Receipt
            $(document).on('click', '.remove-receipt', function () {

                let btn = $(this);
                let file = btn.data('file');
                let expenseId = btn.data('id');

                if (!confirm('Remove this receipt?')) return;

                $.ajax({
                    url: "{{ route('admin.expense.removeReceipt') }}",
                    method: "POST",
                    data: {
                        expense_id: expenseId,
                        file: file,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (res) {
                        if (res.status === 1) {
                            btn.closest('div').remove();
                        }
                    }
                });
            });

            // Filter Start
            $('body').on('shown.bs.modal', '#filterpanel', function() {
                $(this).find('.select22f').each(function() {

                    $(this).select2({
                        dropdownParent: $(this).parent()

                    });
                });
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            // Function to retrieve all filter values
            function getFilterData() {
                return {
                    page_list: $('#pagination_list').val(),
                    search_text: $('#search_text').val(),
                    expensefor: $('#by-expense-for').val(),
                    expensecat: $('#by-expense-cat').val(),
                    paymentmode: $('#by-payment-mode').val(),
                    createby_id: $('#by-create-by').val(),
                    payment_date: $('#by-payment-date').val(),
                };
            }

            // Function to reload todo list based on filter data
            function reloadTodoList() {
                $.ajax({
                    url: "{{ route('admin.expense.list') }}",
                    method: "GET",
                    dataType: "html",
                    data: getFilterData(),
                    success: function (data) {
                        $('.contactpaginate').html(data);
                    }
                });
            }

            // Filter Indicator (show / hide dot on the Filter button)
            function updateExpenseFilterIndicator() {
                let isFiltered =
                    ($('#by-expense-for').val() && $('#by-expense-for').val().length > 0) ||
                    ($('#by-expense-cat').val() && $('#by-expense-cat').val().length > 0) ||
                    ($('#by-payment-mode').val() && $('#by-payment-mode').val().length > 0) ||
                    ($('#by-create-by').val() && $('#by-create-by').val().length > 0) ||
                    ($('#by-payment-date').val() && $('#by-payment-date').val() !== '');

                if (isFiltered) {
                    $('.filterpanel .filter-indicator')
                        .removeClass('d-none')
                        .addClass('d-block');
                } else {
                    $('.filterpanel .filter-indicator')
                        .removeClass('d-block')
                        .addClass('d-none');
                }
            }

            // Generic event handler for change and input events on filter elements
            function bindFilterChange(selector) {
                $(selector).on('change input', function () {
                    reloadTodoList();
                    updateExpenseFilterIndicator();
                });
            }
            // Bind change/input events to all filter elements
            bindFilterChange('#pagination_list');
            bindFilterChange('#search_text');
            bindFilterChange('#by-expense-for');
            bindFilterChange('#by-expense-cat');
            bindFilterChange('#by-payment-mode');
            bindFilterChange('#by-create-by');

            function bindDateRangePicker(selector) {
                $(selector).on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                    reloadTodoList();
                    updateExpenseFilterIndicator();
                }).on('cancel.daterangepicker', function () {
                    $(this).val('');
                    reloadTodoList();
                    updateExpenseFilterIndicator();
                });
            }

            // Bind date range picker to the relevant fields
            bindDateRangePicker('input[name="payment_date"]');

            // Initial check (on page load / saved filter)
            updateExpenseFilterIndicator();

            // Status Cards (Total / This Month / Recently Added / Payment Mode) -
            // mutually exclusive: clicking one clears every other card's filter and
            // selection, so only one can be active at a time. The cards themselves
            // always show fixed/global counts (set server-side from $summaryCounts);
            // this handler only ever changes which rows the table shows.
            $(document).on('click', '.expense-status-card', function (e) {
                e.preventDefault();

                var $this = $(this);
                var type = $this.attr('data-type');
                var wasActive = $this.hasClass('active');

                $('.expense-status-card').removeClass('active');

                $('#by-payment-mode').selectpicker('val', []);
                if ($('#by-payment-date').val() != '') {
                    $('#by-payment-date').val('');
                }

                if (!wasActive) {
                    $this.addClass('active');

                    if (type === 'metric') {
                        switch ($this.attr('data-card')) {
                            case 'this_month':
                                $('#by-payment-date').val(
                                    moment().startOf('month').format('MM/DD/YYYY') +
                                    ' - ' +
                                    moment().endOf('month').format('MM/DD/YYYY')
                                );
                                break;
                            case 'recently_added':
                                $('#by-payment-date').val(
                                    moment().subtract(6, 'days').format('MM/DD/YYYY') +
                                    ' - ' +
                                    moment().format('MM/DD/YYYY')
                                );
                                break;
                            case 'total':
                            default:
                                // Total is a shortcut to clear the filters above; no filter to set.
                                break;
                        }
                    } else if (type === 'payment_mode') {
                        $('#by-payment-mode').selectpicker('val', [$this.attr('data-value')]);
                    }
                }

                // A single explicit call is enough: #by-payment-date isn't wired to
                // bindFilterChange (it only reloads on the daterangepicker's own
                // apply/cancel events), so we reload directly here once rather than
                // firing multiple field triggers.
                reloadTodoList();
                updateExpenseFilterIndicator();
            });

            // Save Filter
            $(document).on('click','.savetodoFilter',function(){
                var expense_for = $('#by-expense-for ').val();
                var expense_type = $('#by-expense-cat').val();
                var payment_mode = $('#by-payment-mode').val();
                var createby_id = $('#by-create-by').val();
                var payment_date = $('#by-payment-date').val();


                jQuery.ajax({
                    url: "{{ route('admin.expense.saveadminfilter') }}",
                    method: "POST",
                    type: "html",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        expense_for:expense_for,
                        expense_type: expense_type,
                        payment_mode: payment_mode,
                        createby_id: createby_id,
                        payment_date: payment_date,
                    },
                    success: function(data){
                        if(data){
                            toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                        }
                    }
                });

            });

            // Reset Filter
            $(document).on('click','.resetfilter',function(){

                // $('#by-cand-status').selectpicker('deselectAll');
                $('.selectpicker').selectpicker('deselectAll');

                $('.expense-status-card').removeClass('active');

                if ($('#by-payment-date').val() != '') {
                    $('#by-payment-date').trigger('cancel.daterangepicker');
                }

                updateExpenseFilterIndicator();

                jQuery.ajax({
                    url: "{{ route('admin.expense.resetadminfilter') }}",
                    method: "POST",
                    type: "html",
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if(data){
                            toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                        }
                    }
                });

            });

            // Pagination Request
            $('body').on('click','.pagination a',function(e){
                e.preventDefault();
                var url = $(this).attr('href');
                var url_data = getFilterData();
                var finalURL = url + "&" + $.param(url_data);;
                // alert(finalURL);

                getPaginations(finalURL);
                window.history.pushState("", url);

            });

            function getPaginations(finalURL){
                $.ajax({
                    url : finalURL
                }).done(function(data){
                    $('.contactpaginate').html(data);

                }).fail(function(){

                    alert("Something gone wrong!")
                });
            }

            // .delcandidate
            $('#deleteStaff').on('show.bs.modal',function(e){
                var delete_id = $(e.relatedTarget).data('id');
                $('#deleteID').val(delete_id);
            });

        });
    </script>

@endsection
