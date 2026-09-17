@extends('layout.partner.partner_layout')

@section('title','Invoice Lists')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}">
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Total Sales</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2"><i class="ti ti-currency-rupee ti-sm"></i> 21,459</h4>
                                    {{-- <span class="text-success">(+29%)</span> --}}
                                </div>
                            </div>
                            <span class="badge bg-label-primary rounded p-2">
                                <i class="ti ti-currency-rupee ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Paid</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2"><i class="ti ti-currency-rupee ti-sm"></i> 21,459</h4>
                                    {{-- <span class="text-success">(+29%)</span> --}}
                                </div>
                                {{-- <span>Total Users</span> --}}
                            </div>
                            <span class="badge bg-label-success rounded p-2">
                                <i class="ti ti-currency-rupee ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Unpaid</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2"><i class="ti ti-currency-rupee ti-sm"></i> 21,459</h4>
                                    {{-- <span class="text-success">(+29%)</span> --}}
                                </div>
                                {{-- <span>Total Users</span> --}}
                            </div>
                            <span class="badge bg-label-danger rounded p-2">
                                <i class="ti ti-currency-rupee ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Invoice Card List Start -->
        <div class="card">
            <div class="card-header py-3 px-4">
                <div class="float-start">
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
                    <button class="btn btn-xs btn-primary filterpanel" data-bs-toggle="modal" data-bs-target="#filterpanel"><i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter</button>
                </div>
                <div class="float-end">
                    <button class="add-new btn btn-sm btn-primary mx-1" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Create Sales Invoice</span></button>
                </div>
                <div class="float-end">
                    <input type="text" id="search_text" class="form-control form-control-sm" placeholder="Search...">
                </div>
            </div>
            <!-- Invoice Card List End -->
            <div class="card-datatable table-responsive invoicepaginate">
                @include('admin.invoice.partner.loadinvoice')
            </div>
        </div>


        <!-- Add Invoice Start Here -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Invoice</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addInvoiceValidation" action="{{ route('partner.invoice.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="invoice_amount" id="invoice-amount" class="invoice-amount">
                        <input type="hidden" name="empcand_id" id="add-empcand-id" class="add-empcand-id">
                        {{-- <input type="hidden" name="emp_id" id="add-emp-id" class="add-emp-id"> --}}
                        {{-- <input type="hidden" name="cand_id" id="add-cand-id" class="add-cand-id"> --}}
                        <input type="hidden" name="service_charge" id="add-service-charge" class="add-service-charge">
                        {{-- <input type="hidden" name="candidate_name" id="add-str-cand-name" class="add-str-cand-name"> --}}
                        {{-- <input type="hidden" name="candidate_pass_no" id="add-str-cand-pass" class="add-str-cand-pass"> --}}
                        {{-- <input type="hidden" name="employer_name" id="add-str-employer-name" class="add-str-employer-name"> --}}
                        {{-- <input type="hidden" name="employer_ar_name" id="add-str-employer-arname" class="add-str-employer-arname"> --}}
                        {{-- <input type="hidden" name="employer_visa_no" id="add-str-employer-visano" class="add-str-employer-visano"> --}}
                        {{-- <input type="hidden" name="employer_id_no" id="add-str-employer-idno" class="add-str-employer-idno"> --}}
                        {{-- <input type="hidden" name="profession_id" id="add-str-profession" class="add-str-profession"> --}}
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label" for="add-partneroffice-id">Partner <span class="text-danger">*</span></label>
                                <select name="partneroffice_id" id="add-partneroffice-id" class="form-select select2" data-placeholder="Select Partner...." data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($partners as $partner)
                                        <option value="{{ $partner->id }}">{{ $partner->rec_off_name.' ('.$partner->rec_office_arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-invoice-date" class="form-label">Invoice Date <span class="text-danger">*</span></label>
                                <input type="text" name="invoice_date" id="add-invoice-date" class="form-control invoce-date-picker" placeholder="Enter Invoice Date...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="add-invoice-number" class="form-label">Invoice No <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_no" id="add-invoice-number" class="form-control" value="{{ $invoicedata['generate_invoice'] }}" placeholder="Enter Invoice No...">
                        </div>
                        {{-- <div class="col-md-12">
                            <button type="button" class="mb-3 btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addcandidateforinvoice">Add Candidate</button>
                        </div> --}}


                        <div class="col-md-12">
                            <div class="mb-3">
                                <table class="table table-bordered" id="servicechargeTable">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class='form-check form-check-inline'>
                                                    <input class='form-check-input checkboxSelectAll' type='checkbox' id='checkboxSelectAll' />
                                                    <label class='form-check-label' for='checkboxSelectAll'></label>
                                                </div>
                                            </th>
                                            <th>Services</th>
                                            <th>Service Charge</th>
                                        </tr>
                                    </thead>
                                    <tbody class="dynamic-cand listitem">

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2"><strong class="float-end">Total Amount</strong></td>
                                            <td><span id="total_amount"></span></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-bankdetails" class="form-label">Bank Detail</label>
                                <select name="invoiceaccountdet_id" class="form-select select2" data-allow-clear="true" data-placeholder="Select Bank Detail" id="add-bankdetails">
                                    <option value=""></option>
                                    @foreach ($bankdetails as $bankdetail)
                                        <option value="{{ $bankdetail->id }}">{{ $bankdetail->name.' ('.$bankdetail->account_number.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-terms-condition" class="form-label">Terms and Condition</label>
                                <textarea name="terms_condtion" id="add-terms-condition" class="form-control" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit disableinvoicebtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Add Invoice End Here -->
        <!-- Add Candidate For Invoice Start -->
        <div class="modal fade" id="addcandidateforinvoice" aria-hidden="true" aria-labelledby="addcandidateforinvoiceLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <form action="" method="GET">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header pb-2">
                            <h5 class="offcanvas-title" id="updateLstageLabel">Add Candidate</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="dispallcandlist"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-sm btn-success" id="confirmcandidate">Confirm</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Add Candidate For Invoice End -->

        <!-- Edit Invoice Start Here -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editinvoice" aria-labelledby="editinvoiceLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editinvoiceLabel" class="offcanvas-title">Edit Invoice</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editInvoiceValidation" action="{{ route('partner.invoice.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="invoice_id" id="edit_invoice_id">
                        <input type="hidden" name="invoice_amount" id="edit-invoice-amount" class="edit-invoice-amount">
                        <input type="hidden" name="empcand_id" id="edit-empcand-id" class="edit-empcand-id">
                        <input type="hidden" name="service_charge" id="edit-service-charge" class="edit-service-charge">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label" for="edit-partneroffice-id">Partner <span class="text-danger">*</span></label>
                                <select name="partneroffice_id" id="edit-partneroffice-id" class="form-select select2" disabled data-placeholder="Select Partner...." data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($partners as $partner)
                                        <option value="{{ $partner->id }}">{{ $partner->rec_off_name.' ('.$partner->rec_office_arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-invoice-date" class="form-label">Invoice Date <span class="text-danger">*</span></label>
                                <input type="text" name="invoice_date" id="edit-invoice-date" class="form-control invoce-date-picker" placeholder="Enter Invoice Date...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="edit-invoice-number" class="form-label">Invoice No <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_no" id="edit-invoice-number" class="form-control" placeholder="Enter Invoice No...">
                        </div>
                        {{-- <div class="col-md-12">
                            <button type="button" class="mb-3 btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addcandidateforinvoice">Add Candidate</button>
                        </div> --}}


                        <div class="col-md-12">
                            <div class="mb-3">
                                <table class="table table-bordered" id="servicechargeTablee">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class='form-check form-check-inline'>
                                                    <input class='form-check-input checkboxSelectAlle' type='checkbox' id='checkboxSelectAlle' />
                                                    <label class='form-check-label' for='checkboxSelectAlle'></label>
                                                </div>
                                            </th>
                                            <th>Services</th>
                                            <th>Service Charge</th>
                                        </tr>
                                    </thead>
                                    <tbody class="dynamic-cande listiteme">

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2"><strong class="float-end">Total Amount</strong></td>
                                            <td><span id="total_amount_e"></span></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-bankdetails" class="form-label">Bank Detail</label>
                                <select name="invoiceaccountdet_id" class="form-select select2" data-allow-clear="true" data-placeholder="Select Bank Detail" id="edit-bankdetails">
                                    <option value=""></option>
                                    @foreach ($bankdetails as $bankdetail)
                                        <option value="{{ $bankdetail->id }}">{{ $bankdetail->name.' ('.$bankdetail->account_number.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-terms-condition" class="form-label">Terms and Condition</label>
                                <textarea name="terms_condtion" id="edit-terms-condition" class="form-control" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit disableinvoicebtne">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Edit Invoice End Here -->

    </div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/employer-visa-validation.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script>

    <script>
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            var select2 = $('.select2');
            var invoce_date_picker = $('.invoce-date-picker');

            if (invoce_date_picker.length) {
                $(invoce_date_picker).flatpickr();
            }

            if (select2.length) {
                select2.each(function () {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>');
                    $this.select2({
                        dropdownParent: $this.parent()
                    });
                });
            }

            // Get Show and Select Candidate
            $('#addcandidateforinvoice').on('show.bs.modal',function(e){
                var partneroffice_id = $('#add-partneroffice-id').val();
                if (partneroffice_id != '') {


                    $('.dispallcandlist').html("<p class='text-success'>Good</p>");
                } else {
                    $('.dispallcandlist').html("<p class='text-danger'>Please Select Partner Name</p>");
                }
            });

            // Add Invoice Validation
            $('#addInvoiceValidation').validate({
                rules:{
                    partneroffice_id: {
                        required: true
                    },
                    invoice_date:{
                        required: true
                    },
                    invoice_no:{
                        required: true,
                        // digits: true,
                        remote:{
                            type: 'POST',
                            url: '{{ route("partner.invoice.checkinvoicenumber") }}',
                            data:{
                                invoice_no: function(){
                                    return $('#add-invoice-number').val()
                                },
                                _token: "{{ csrf_token() }}"
                            }
                        }
                    }
                },
                messages:{
                    partneroffice_id: {
                        required: "Please Select Partner Office"
                    },
                    invoice_date:{
                        required: "Please enter invoice date"
                    },
                    invoice_no:{
                        required: "Please enter valid invoice number",
                        // digits: "Please enter only digits",
                        remote: "Invoice Number already exists!"
                    }
                }
            });

            // Get Candidate, Employer and Services Charges
            $(document).on('change','#add-partneroffice-id',function(e){
                var partner_id = $(this).val();
                if (partner_id != '') {

                    $.ajax({
                        url: "{{ route('partner.invoice.getcandidate') }}",
                        method: "GET",
                        type: "html",
                        data:{
                            id: partner_id
                        },
                        success: function(response){

                            console.log(response);


                            var html = "";
                            var empcandID = [];
                            var candID = [];
                            var empID = [];
                            var serviceCharg = [];
                            var employername = [];
                            var employerarname = [];
                            var employervisano = [];
                            var employeridno = [];
                            var candname = [];
                            var candpass = [];
                            var profession = [];


                            response.invoice_data.forEach(data => {

                                html += "<tr><td><div class='form-check form-check-inline'><input class='form-check-input dt-checkboxes sub-chk' data-id='"+data.empcand_id+"' type='checkbox' value='"+data.empcand_id+"' id='checkbox"+data.empcand_id+"' checked /><label class='form-check-label' for='checkbox"+data.empcand_id+"'></label></div></td>";
                                html += "<td>Employer: "+data.employer_name+" <br>(Visa No: "+data.emp_visa_no+" | ID No: "+data.emp_id_no+")<br>Candidate: "+data.cand_name+" (PP: "+data.cand_pass_no+")<br>Profession: "+data.profession_eng+" ("+data.profession_ar+")</td>";
                                html += "<td><input type='text' name='service_charge[]' disabled class='form-control add-service-charge-input' id='service_charge"+data.emp_id_no+"' value="+data.service_charge+"></td>";
                                html += "</tr>";

                                empcandID.push(data.empcand_id);
                                candID.push(data.cand_id);
                                empID.push(data.employer_id);
                                serviceCharg.push(data.service_charge);
                                employername.push(data.employer_name);
                                employerarname.push(data.employer_arabic_name);
                                employervisano.push(data.emp_visa_no);
                                employeridno.push(data.emp_id_no);
                                candname.push(data.cand_name);
                                candpass.push(data.cand_pass_no);
                                profession.push(data.profession_id);
                            });
                            var strempcandID = empcandID.join(",");
                            var strcandID = candID.join(",");
                            var strempID = empID.join(",");
                            var serCharg = serviceCharg.join(",");
                            var stremployername = employername.join(",");
                            var stremployerarname = employerarname.join(",");
                            var stremployervisano = employervisano.join(",");
                            var stremployeridno = employeridno.join(",");
                            var strcandname = candname.join(",");
                            var strcandpass = candpass.join(",");
                            var strprofession = profession.join(",");

                            $('.dynamic-cand').html(html);

                            // If no invoice found disabled the submit button
                            if (response.total_amount != 0) {
                                $('.disableinvoicebtn').prop('disabled',false);
                                $('#checkboxSelectAll').prop('checked',true);
                            } else {
                                $('.disableinvoicebtn').prop('disabled',true);
                                $('#checkboxSelectAll').prop('checked',false);
                            }

                            $('.invoice-amount').val(response.total_amount);
                            $('#total_amount').text(response.total_amount);
                            $('#add-empcand-id').val(strempcandID);
                            $('#add-cand-id').val(strcandID);
                            $('#add-emp-id').val(strempID);
                            $('#add-service-charge').val(serCharg);

                            $('#add-str-employer-name').val(stremployername);
                            $('#add-str-employer-arname').val(stremployerarname);
                            $('#add-str-employer-visano').val(stremployervisano);
                            $('#add-str-employer-idno').val(stremployeridno);
                            $('#add-str-cand-name').val(strcandname);
                            $('#add-str-cand-pass').val(strcandpass);
                            $('#add-str-profession').val(strprofession);



                        }
                    });

                } else {
                    $('.dynamic-cand').html('');
                    $('.invoice-amount').val('');
                    $('#total_amount').text('');
                    $('#add-empcand-id').val('');
                    $('#add-cand-id').val('');
                    $('#add-emp-id').val('');
                    $('.disableinvoicebtn').prop('disabled',true);
                    $('#checkboxSelectAll').prop('checked',false);
                }

            });

            // Set Price and Candidate based on checkboxes
            $(document).on('click','.checkboxSelectAll',function(){
                var listcheckitem = $('.listitem :checkbox');
                var isCheckmarked = $(this).is(':checked');
                var checkedList = listcheckitem.length;



                listcheckitem.prop("checked", isCheckmarked);
                if (isCheckmarked == false) {
                    $('.disableinvoicebtn').prop('disabled',true);
                }
                calculateAmount();
            });

            $(document).on('change','.listitem :checkbox',function(){
                var listcheckitem = $('.listitem :checkbox');
                var masterCheck = $('.checkboxSelectAll');
                // Total Checkboxes in list
                var totalItems = listcheckitem.length;
                // Total Checked Checkboxes in list
                var checkedItems = listcheckitem.filter(":checked").length;
                //If all are checked
                if (totalItems == checkedItems) {
                    masterCheck.prop("indeterminate", false);
                    masterCheck.prop("checked", true);
                    $('.disableinvoicebtn').prop("disabled",false);
                }
                // Not all but only some are checked
                else if (checkedItems > 0 && checkedItems < totalItems) {
                    masterCheck.prop("indeterminate", true);
                    $('.disableinvoicebtn').prop("disabled",false);
                }
                //If none is checked
                else {
                    masterCheck.prop("indeterminate", false);
                    masterCheck.prop("checked", false);
                    $('.disableinvoicebtn').prop("disabled",true);
                }

                calculateAmount();


            });


            function calculateAmount(){
                let total = 0;
                let empcand_id = [];
                let service_charges = [];

                $('#servicechargeTable .listitem .dt-checkboxes:checked').each(function(){
                    let rowClo = $(this).closest('tr');
                    let amount = parseFloat(rowClo.find('.add-service-charge-input').val());

                    var empcandtext_id = rowClo.find('.sub-chk').val();
                    var service_charges_text = rowClo.find('.add-service-charge-input').val();

                    empcand_id.push(empcandtext_id.trim());
                    service_charges.push(service_charges_text.trim());

                    total += amount;
                });
                if (total != 0) {
                    $('.disableinvoicebtn').prop('disabled',false);
                }
                // alert(empcand_id);
                $('#total_amount').text(total);
                $('.invoice-amount').val(total);
                $('#add-empcand-id').val(empcand_id);
                $('#add-service-charge').val(service_charges);
            }

            // Edit Invoice System
            $('#editinvoice').on('show.bs.offcanvas',function(e){
                var editID = $(e.relatedTarget).data('id');

                // alert(editID);
                $('#edit_invoice_id').val(editID);
                $.ajax({
                    url: "{{ route('partner.invoice.edit') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        id: editID
                    },
                    success: function(response){
                        // fetch edit date
                        $('#edit-partneroffice-id').val(response.post.partneroffice_id).change();
                        $('#edit-invoice-date').val(response.post.invoice_date);
                        $('#edit-invoice-number').val(response.post.invoice_no);
                        $('#edit-terms-condition').val(response.post.terms_condtion);
                        $('#total_amount_e').text(response.post.invoice_amount);
                        $('#edit-empcand-id').val(response.post.empcand_id);
                        
                        $('#edit-bankdetails').val(response.post.invoiceaccountdet_id).change();

                        
                        // console.log(response);

                        // Fetch Listed Candidtae and tabel
                        var html = "";
                        var empcandID = [];
                        var candID = [];
                        var empID = [];
                        var serviceCharg = [];
                        var employername = [];
                        var employerarname = [];
                        var employervisano = [];
                        var employeridno = [];
                        var candname = [];
                        var candpass = [];
                        var profession = [];

                        var selectedEmpcandIDs = response.post.empcand_id ? response.post.empcand_id.split(',') : [];

                        response.invoice_data.forEach(data => {


                            // Check if the current candidate ID is in the selectedEmpcandIDs array
                            let checkboxedCheck = selectedEmpcandIDs.includes(String(data.empcand_id)) ? "checked" : "";

                            // Start building the row HTML
                            html += "<tr>";

                            // Checkbox column with checked attribute dynamically set
                            html += "<td><div class='form-check form-check-inline'>";
                            html += "<input class='form-check-input dt-checkboxes2 sub-chk2' " +
                                    "data-id='" + data.empcand_id + "' type='checkbox' " +
                                    "value='" + data.empcand_id + "' id='checkbox2" + data.empcand_id + "' "+checkboxedCheck+" />";
                            html += "<label class='form-check-label' for='checkbox2" + data.empcand_id + "'></label>";
                            html += "</div></td>";

                            // Employer and candidate details column
                            html += "<td>Employer: " + (data.employer_name || "N/A") +
                                    " <br>(Visa No: " + (data.emp_visa_no || "N/A") +
                                    " | ID No: " + (data.emp_id_no || "N/A") + ")" +
                                    "<br>Candidate: " + (data.cand_name || "N/A") +
                                    " (PP: " + (data.cand_pass_no || "N/A") + ")" +
                                    "<br>Profession: " + (data.profession_eng || "N/A") +
                                    " (" + (data.profession_ar || "N/A") + ")</td>";

                            // Service charge input column
                            html += "<td><input type='text' name='service_charge[]' disabled " +
                                    "class='form-control add-service-charge-input2' " +
                                    "id='service_charge2" + data.emp_id_no + "' " +
                                    "value='" + (data.service_charge || 0) + "'></td>";

                            // Close the row
                            html += "</tr>";

                            empcandID.push(data.empcand_id);
                            candID.push(data.cand_id);
                            empID.push(data.employer_id);
                            serviceCharg.push(data.service_charge);
                            employername.push(data.employer_name);
                            employerarname.push(data.employer_arabic_name);
                            employervisano.push(data.emp_visa_no);
                            employeridno.push(data.emp_id_no);
                            candname.push(data.cand_name);
                            candpass.push(data.cand_pass_no);
                            profession.push(data.profession_id);

                        });

                        var strempcandID = empcandID.join(",");
                        var strcandID = candID.join(",");
                        var strempID = empID.join(",");
                        var serCharg = serviceCharg.join(",");
                        var stremployername = employername.join(",");
                        var stremployerarname = employerarname.join(",");
                        var stremployervisano = employervisano.join(",");
                        var stremployeridno = employeridno.join(",");
                        var strcandname = candname.join(",");
                        var strcandpass = candpass.join(",");
                        var strprofession = profession.join(",");

                        $('.dynamic-cande').html(html);

                        // If All Candidate Employer Cheched then All Checkbox selected
                        var listcheckitem = $('.listiteme :checkbox');
                        var masterCheck = $('.checkboxSelectAlle');
                        // Total Checkboxes in list
                        var totalItems = listcheckitem.length;
                        // Total Checked Checkboxes in list
                        var checkedItems = listcheckitem.filter(":checked").length;
                        //If all are checked
                        if (totalItems == checkedItems) {
                            masterCheck.prop("indeterminate", false);
                            masterCheck.prop("checked", true);
                            $('.disableinvoicebtne').prop("disabled",false);
                        }
                        // Not all but only some are checked
                        else if (checkedItems > 0 && checkedItems < totalItems) {
                            masterCheck.prop("indeterminate", true);
                            $('.disableinvoicebtne').prop("disabled",false);
                        }
                        //If none is checked
                        else {
                            masterCheck.prop("indeterminate", false);
                            masterCheck.prop("checked", false);
                            $('.disableinvoicebtne').prop("disabled",true);
                        }

                        // Assign this value to that
                        $('.edit-invoice-amount').val(response.post.invoice_amount);
                        // $('#edit-empcand-id').val(strempcandID);
                        $('#edit-cand-id').val(strcandID);
                        $('#edit-emp-id').val(strempID);
                        $('#edit-service-charge').val(serCharg);

                        $('#edit-str-employer-name').val(stremployername);
                        $('#edit-str-employer-arname').val(stremployerarname);
                        $('#edit-str-employer-visano').val(stremployervisano);
                        $('#edit-str-employer-idno').val(stremployeridno);
                        $('#edit-str-cand-name').val(strcandname);
                        $('#edit-str-cand-pass').val(strcandpass);
                        $('#edit-str-profession').val(strprofession);

                    }
                });

            });


            // Set Price and Candidate based on checkboxes
            $(document).on('click','.checkboxSelectAlle',function(){
                var listcheckitem = $('.listiteme :checkbox');
                var isCheckmarked = $(this).is(':checked');
                var checkedList = listcheckitem.length;



                listcheckitem.prop("checked", isCheckmarked);
                if (isCheckmarked == false) {
                    $('.disableinvoicebtne').prop('disabled',true);
                }
                calculateAmount2();
            });

            $(document).on('change','.listiteme :checkbox',function(){
                var listcheckitem = $('.listiteme :checkbox');
                var masterCheck = $('.checkboxSelectAlle');
                // Total Checkboxes in list
                var totalItems = listcheckitem.length;
                // Total Checked Checkboxes in list
                var checkedItems = listcheckitem.filter(":checked").length;
                //If all are checked
                if (totalItems == checkedItems) {
                    masterCheck.prop("indeterminate", false);
                    masterCheck.prop("checked", true);
                    $('.disableinvoicebtne').prop("disabled",false);
                }
                // Not all but only some are checked
                else if (checkedItems > 0 && checkedItems < totalItems) {
                    masterCheck.prop("indeterminate", true);
                    $('.disableinvoicebtne').prop("disabled",false);
                }
                //If none is checked
                else {
                    masterCheck.prop("indeterminate", false);
                    masterCheck.prop("checked", false);
                    $('.disableinvoicebtne').prop("disabled",true);
                }

                calculateAmount2();


            });


            function calculateAmount2(){
                let total = 0;
                let empcand_id = [];
                let service_charges = [];

                $('#servicechargeTablee .listiteme .dt-checkboxes2:checked').each(function(){
                    let rowClo = $(this).closest('tr');
                    let amount = parseFloat(rowClo.find('.add-service-charge-input2').val());

                    var empcandtext_id = rowClo.find('.sub-chk2').val();
                    var service_charges_text = rowClo.find('.add-service-charge-input2').val();

                    empcand_id.push(empcandtext_id.trim());
                    service_charges.push(service_charges_text.trim());

                    total += amount;
                });
                if (total != 0) {
                    $('.disableinvoicebtne').prop('disabled',false);
                }
                // alert(empcand_id);
                $('#total_amount_e').text(total);
                $('.edit-invoice-amount').val(total);
                $('#edit-empcand-id').val(empcand_id);
                $('#edit-service-charge').val(service_charges);
            }

            // Edit Invoice Validation
            $('#editInvoiceValidation').validate({
                rules:{
                    partneroffice_id: {
                        required: true
                    },
                    invoice_date:{
                        required: true
                    },
                    invoice_no:{
                        required: true,
                        // digits: true,
                        remote:{
                            type: 'POST',
                            url: '{{ route("partner.invoice.checkinvoicenumber") }}',
                            data:{
                                invoice_no: function(){
                                    return $('#edit-invoice-number').val()
                                },
                                invoice_id: function(){
                                    return $('#edit_invoice_id').val()
                                },
                                _token: "{{ csrf_token() }}"
                            }
                        }
                    }
                },
                messages:{
                    partneroffice_id: {
                        required: "Please Select Partner Office"
                    },
                    invoice_date:{
                        required: "Please enter invoice date"
                    },
                    invoice_no:{
                        required: "Please enter valid invoice number",
                        // digits: "Please enter only digits",
                        remote: "Invoice Number already exists!"
                    }
                }
            });

        });
    </script>



@endsection
