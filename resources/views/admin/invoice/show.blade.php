@extends('layout.admin.admin_layout')

@section('title','Invoice View')

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
        <div class="row invoice-preview">
            <!-- Invoice -->
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column m-sm-3 m-0">
                            <div class="mb-xl-0 mb-4">
                                <div class="d-flex svg-illustration mb-4 gap-2 align-items-center">

                                    @if ($invoice->partneroffice->website_logo != '')
                                        <img src="{{ asset('admin/assets/images/partner/'.$invoice->partneroffice->website_logo) }}" alt="user-avatar" class="d-block w-px-200 rounded" id="uploadedAvatar"/>
                                    @else

                                    @endif

                                    {{-- <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="#7367F0"/>
                                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616"/>
                                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="#7367F0"/>
                                    </svg>
                                    <span class="app-brand-text fw-bold fs-4"> Vuexy </span> --}}
                                </div>
                            </div>
                            @php
                                $badge = match ($invoice->payment_status) {
                                    'Paid' => 'success',
                                    'Partially Paid' => 'warning',
                                    default => 'danger'
                                };

                                $disable_badge = match ($invoice->payment_status) {
                                    'Paid' => 'secondary',
                                    'Partially Paid' => 'secondary',
                                    default => 'primary'
                                };

                                $balance_payment = $invoice->invoice_amount - $invoice->paid_amount;
                            @endphp
                            <div>
                                <h4 class="fw-semibold mb-2">Invoice No: {{ $invoice->invoice_no }}</h4>
                                <div class="mb-2 pt-1">
                                    <span>Date Issues:</span>
                                    <span class="fw-semibold">{{ date('d-m-Y',strtotime($invoice->invoice_date)) }}</span>
                                </div>
                                <div class="mb-2 pt-1">
                                    <span>Payment Status: </span>
                                    <span class="badge bg-label-{{ $badge }}">{{ $invoice->payment_status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-0" />
                    <div class="card-body">
                        <div class="row p-sm-3 p-0">
                            <div class="col-xl-6 col-md-12 col-sm-5 col-12 mb-xl-0 mb-md-4 mb-sm-0 mb-4">
                                <h6 class="mb-3">Invoice To:</h6>
                                <p class="mb-1">{{ $invoice->partneroffice->owner_name }}</p>
                                <p class="mb-1">{{ $invoice->partneroffice->rec_off_name }}</p>
                                <p class="mb-1">{{ $invoice->partneroffice->info_eng_address }}</p>
                                <p class="mb-1">{{ $invoice->partneroffice->office_no }}</p>
                                <p class="mb-0">{{ $invoice->partneroffice->primary_email }}</p>
                            </div>
                            <div class="col-xl-6 col-md-12 col-sm-7 col-12">
                                <h6 class="mb-3">Invoice From:</h6>
                                <p class="mb-1">Qamr International</p>
                                <p class="mb-1">Naseem Mansion, Charnull, Dongri, Mumbai, India 400009</p>
                                <p class="mb-1">91 9004266888 / +91 9004882666</p>
                                <p class="mb-0">jobs@qamrintl.com / hr@qamrintl.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive border-top">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th>Services</th>
                                    <th colspan="2">Service Charge</th>

                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $service_charge = explode(",",$invoice->service_charge);
                                @endphp
                                @foreach ($empcands as $key => $empcand)
                                    <tr>
                                        <td>
                                            Employer: {{ $empcand->emp->employer_name }}<br>
                                            (Visa No: {{ $empcand->emp->visa_no }} | ID No: {{ $empcand->emp->id_no }})<br>
                                            Candidate: {{ $empcand->cand->cand_name }} (Pass No: {{ $empcand->cand->pass_no }})<br>
                                            Profession: {{ $empcand->proff->eng_name }}
                                        </td>
                                        <td colspan="2">{{ $service_charge[$key] }}</td>
                                    </tr>
                                @endforeach


                                <tr>
                                    <td class="align-top px-4 py-4">
                                        <p class="mb-2 mt-3">
                                            <span class="ms-3 fw-semibold">Responsible Person:</span>
                                            <span>{{ $invoice->admin->name }}</span>
                                        </p>
                                        <span class="ms-3">Thanks for your business</span>
                                    </td>
                                    <td class="text-end pe-3 py-4">
                                        <p class="mb-2 pt-3 fw-semibold">Subtotal:</p>
                                        <p class="mb-2 fw-semibold">Paid:</p>
                                        <p class="mb-0 pb-3 fw-semibold">Total:</p>
                                    </td>
                                    <td class="ps-2 py-4">
                                        <p class="fw-semibold mb-2 pt-3">{{ $invoice->invoice_amount }}</p>
                                        <p class="fw-semibold mb-2">@if($invoice->paid_amount != '' || $invoice->paid_amount != 0) {{ $invoice->paid_amount }} @else 0 @endif</p>
                                        <p class="fw-semibold mb-0 pb-3">{{ $balance_payment }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body mx-3">
                        <div class="row">
                            <div class="col-12">
                                <span class="fw-semibold">Note:</span>
                                <span>It was a pleasure working with you and your team. We hope you will keep us in mind for future recruitment. Thank You!</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Invoice -->
            <!-- Invoice Actions -->
            <div class="col-xl-3 col-md-4 col-12 invoice-actions">
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-primary d-grid w-100 mb-2" data-bs-toggle="offcanvas" data-bs-target="#sendInvoiceOffcanvas">
                            <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-send ti-xs me-1"></i>Send Invoice</span>
                        </button>

                        <a href="{{ route('admin.invoice.generatedpdf',$invoice->id) }}" target="_blank" class="btn btn-label-secondary d-grid w-100 mb-2"><span><i class="ti ti-pdf ti-xs me-1"></i> Download PDF</span></a>
                        <a href="javascript:;" @if($invoice->payment_status == 'Unpaid') data-bs-toggle="offcanvas" data-bs-target="#editinvoice" data-id="{{ $invoice->id }}"  @endif  class="btn btn-label-{{ $disable_badge }} d-grid w-100 mb-2">Edit Invoice</a>
                        <button class="btn btn-primary d-grid w-100" data-bs-toggle="offcanvas" data-bs-target="#addPaymentOffcanvas">
                            <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-currency-dollar ti-xs me-1"></i>Add Payment</span>
                        </button>
                    </div>
                </div>
              </div>
            <!-- /Invoice Actions -->
        </div>


        <!-- Edit Invoice Start Here -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editinvoice" aria-labelledby="editinvoiceLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editinvoiceLabel" class="offcanvas-title">Edit Invoice</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editInvoiceValidation" action="{{ route('admin.invoice.update') }}" method="POST">
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

        <!-- Send Invoice to Customer or Partner Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="sendInvoiceOffcanvas" aria-labelledby="sendInvoiceOffcanvasLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="sendInvoiceOffcanvasLabel" class="offcanvas-title">Send Invoice</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="sendInvoiceOffcanvasValidation" action="" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-send-invoice-through" class="form-label">Send Through <span class="text-danger">*</span></label>
                                <select name="send_through" id="add-send-invoice-through" class="form-select select2" data-allow-clear="true" data-placeholder="Select Send through...">
                                    <option value="">Select</option>
                                    <option value="both">Both</option>
                                    <option value="whatsapp">Whatsapp</option>
                                    <option value="mail">Mail</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-send-invoice-to" class="form-label">Send To <span class="text-danger">*</span></label>
                                <select name="send_to" id="add-send-invoice-to" data-allow-clear="true" data-placeholder="Select Send To" class="form-select select2">
                                    <option value="">Select</option>
                                    <option value="both">Both</option>
                                    <option value="to_staff">To Staff</option>
                                    <option value="to_partner">To Partner</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-send-to-staff-list" class="form-label">Staff <span class="text-danger">*</span></label>
                                <select name="send_staff_to" id="add-send-to-staff-list" class="form-select select2" data-allow-clear="true" data-placeholder="Select Staff">
                                    <option value="">Select</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit disableinvoicebtne">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Send Invoice to Customer or Partner End -->
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

            if (select2.length) {
                select2.each(function () {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>');
                    $this.select2({
                        dropdownParent: $this.parent()
                    });
                });
            }

            if (invoce_date_picker.length) {
                $(invoce_date_picker).flatpickr();
            }

            // Edit Invoice System
            $('#editinvoice').on('show.bs.offcanvas',function(e){
                var editID = $(e.relatedTarget).data('id');

                // alert(editID);
                $('#edit_invoice_id').val(editID);
                $.ajax({
                    url: "{{ route('admin.invoice.edit') }}",
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
                        console.log(response);

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
                            url: '{{ route("admin.invoice.checkinvoicenumber") }}',
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
