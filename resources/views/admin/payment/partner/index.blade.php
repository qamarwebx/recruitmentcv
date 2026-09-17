@extends('layout.partner.partner_layout')

@section('title','Payment Lists')

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
        <!-- Payment Card List Start -->
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
                    <button class="add-new btn btn-sm btn-primary mx-1" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Payment</span></button>
                </div>
                <div class="float-end">
                    <input type="text" id="search_text" class="form-control form-control-sm" placeholder="Search...">
                </div>
            </div>
            <!-- Payment Card List End -->
            <div class="card-datatable table-responsive paymentpaginate">
                @include('admin.payment.partner.loadpayment')
            </div>
        </div>

        <div class="modal fade" id="paymentSlipModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Slip</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body text-center">
                    <!-- container for image or PDF iframe -->
                    <div id="slipContainer">
                    <img id="slipImage" src="" alt="Payment Slip" class="img-fluid d-none"/>
                    <iframe id="slipPdf" src="" class="w-100" style="height:600px; display:none;" frameborder="0"></iframe>
                    <p id="slipError" class="text-danger d-none">Payment slip not found or cannot be displayed. <a id="slipDirectLink" href="#" target="_blank">Open in new tab</a></p>
                    </div>
                </div>
                </div>
            </div>
        </div>




        <!-- Add Invoice Start Here -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Payment</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addPaymentValidation" action="{{ route('partner.payment.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="invoice_id" id="add-invoice-id">
                        <div class="col-md-4">
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
                                <label for="add-payment-amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="text" name="amount" id="add-payment-amount" class="form-control add-payment-amount-change" placeholder="Enter Amount">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-payment-date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="text" name="payment_date" id="add-payment-date" class="form-control invoce-date-picker" placeholder="Enter Payment Date...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-payment-mode" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                <select name="payment_mode" id="add-payment-mode" class="form-select select2" data-allow-clear="true" data-placeholder="Select Payment Mode">
                                    <option value="">Select</option>
                                    <option value="Cash">Cash</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Card">Card</option>
                                    <option value="Netbanking">Netbanking</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-recieved-in" class="form-label">Recived In</label>
                                <select name="received_in" id="add-recieved-in" class="form-select select2" data-allow-clear="true" data-placeholder="Select Received In">
                                    <option value="">Select</option>
                                    <option value="Khursheed Khan">Khursheed Khan</option>
                                    <option value="Junaid Khan">Junaid Khan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-transaction-id" class="form-label">Transaction ID</label>
                                <input type="text" name="transaction_id" id="add-transaction-id" class="form-control" placeholder="Enter Transaction ID">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-terms-condition" class="form-label">Notes</label>
                                <textarea name="terms_condtion" id="add-terms-condition" class="form-control" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <table class="table table-bordered" id="getinvoiceTable">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class='form-check form-check-inline'>
                                                    <input class='form-check-input checkboxSelectAll' type='checkbox' id='checkboxSelectAll' />
                                                    <label class='form-check-label' for='checkboxSelectAll'></label>
                                                </div>
                                            </th>
                                            <th>Date</th>
                                            <th>Invoice No</th>
                                            <th>Invoice Amount</th>
                                            <th>Amount Settled</th>
                                        </tr>
                                    </thead>
                                    <tbody class="dynamic-invoice listitem">

                                    </tbody>
                                    <tfoot>
                                        <td colspan="3"><strong>Total</strong></td>
                                        <td><span id="total_inv_amount"></span></td>
                                        <td><span id="total_settled_amount"></span></td>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-payment-slip" class="form-label">Payment Slip</label>
                                <input type="file" name="payment_slip" class="form-control" id="add-payment-slip">
                            </div>
                        </div>
                    </div>

                    

                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit disableinvoicebtn" disabled>Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Add Invoice End Here -->

        <!-- Edit Invoice Payment Start Here -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editpayment" aria-labelledby="editpaymentLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editpaymentLabel" class="offcanvas-title">Edit Payment</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editPaymentValidation" action="{{ route('partner.payment.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="invoice_id" id="edit-invoice-id">
                        <input type="hidden" name="payment_id" id="edit-payment-id">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="edit-partneroffice-id">Partner <span class="text-danger">*</span></label>
                                <select name="partneroffice_id" id="edit-partneroffice-id" disabled class="form-select select2" data-placeholder="Select Partner...." data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($partners as $partner)
                                        <option value="{{ $partner->id }}">{{ $partner->rec_off_name.' ('.$partner->rec_office_arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-payment-amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="text" name="amount" id="edit-payment-amount" class="form-control edit-payment-amount-change" placeholder="Enter Amount">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-payment-date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="text" name="payment_date" id="edit-payment-date" class="form-control invoce-date-picker" placeholder="Enter Payment Date...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-payment-mode" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                <select name="payment_mode" id="edit-payment-mode" class="form-select select2" data-allow-clear="true" data-placeholder="Select Payment Mode">
                                    <option value="">Select</option>
                                    <option value="Cash">Cash</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Card">Card</option>
                                    <option value="Netbanking">Netbanking</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-recieved-in" class="form-label">Recived In</label>
                                <select name="received_in" id="edit-recieved-in" class="form-select select2" data-allow-clear="true" data-placeholder="Select Received In">
                                    <option value="">Select</option>
                                    <option value="Khursheed Khan">Khursheed Khan</option>
                                    <option value="Junaid Khan">Junaid Khan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-transaction-id" class="form-label">Transaction ID</label>
                                <input type="text" name="transaction_id" id="edit-transaction-id" class="form-control" placeholder="Enter Transaction ID">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-terms-condition" class="form-label">Notes</label>
                                <textarea name="terms_condtion" id="edit-terms-condition" class="form-control" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <table class="table table-bordered" id="getinvoiceTable2">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class='form-check form-check-inline'>
                                                    <input class='form-check-input checkboxSelectAll2' type='checkbox' id='checkboxSelectAll2' />
                                                    <label class='form-check-label' for='checkboxSelectAll2'></label>
                                                </div>
                                            </th>
                                            <th>Date</th>
                                            <th>Invoice No</th>
                                            <th>Invoice Amount</th>
                                            <th>Amount Settled</th>
                                        </tr>
                                    </thead>
                                    <tbody class="dynamic-invoice2 listitem2">

                                    </tbody>
                                    <tfoot>
                                        <td colspan="3"><strong>Total</strong></td>
                                        <td><span id="total_inv_amount2"></span></td>
                                        <td><span id="total_settled_amount2"></span></td>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-payment-slip" class="form-label">Payment Slip</label>
                                <input type="file" name="payment_slip" class="form-control" id="edit-payment-slip">

                                <div id="current-slip-box" class="mt-2"></div>
                            </div>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit disableinvoicebtn2" disabled>Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Edit Invoice Payment End Here -->

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

        document.addEventListener('click', function(e){
        if(!e.target.closest('.viewPaymentSlip')) return;
        e.preventDefault();

        const el = e.target.closest('.viewPaymentSlip');
        const slipUrl = el.getAttribute('data-slip') || '';

        console.log('slipUrl =', slipUrl); // <= check console if URL is correct

        // elements
        const img = document.getElementById('slipImage');
        const pdf = document.getElementById('slipPdf');
        const err = document.getElementById('slipError');
        const link = document.getElementById('slipDirectLink');

        // reset
        img.classList.add('d-none'); img.src = '';
        pdf.style.display = 'none'; pdf.src = '';
        err.classList.add('d-none'); link.href = '#';

        if (!slipUrl) {
            err.classList.remove('d-none');
            link.href = '#';
        } else {
            // Determine file extension
            const ext = slipUrl.split('.').pop().toLowerCase().split(/\#|\?/)[0];

            if (['jpg','jpeg','png','gif','bmp','webp'].includes(ext)) {
            img.src = slipUrl;
            img.classList.remove('d-none');

            // show modal after image load (or on error)
            img.onload = () => {
                showBootstrapModal('paymentSlipModal');
            };
            img.onerror = () => {
                err.classList.remove('d-none');
                link.href = slipUrl;
                showBootstrapModal('paymentSlipModal');
            };
            } else if (['pdf'].includes(ext)) {
            pdf.src = slipUrl;
            pdf.style.display = 'block';
            showBootstrapModal('paymentSlipModal');
            } else {
            // unknown type — try to show in iframe, otherwise show direct link
            pdf.src = slipUrl;
            pdf.style.display = 'block';
            showBootstrapModal('paymentSlipModal');
            }
        }
        });

        // helper to show bootstrap modal (compatible with BS5)
        function showBootstrapModal(id) {
        const modalEl = document.getElementById(id);
        if (!modalEl) return;
        if (window.bootstrap && bootstrap.Modal) {
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        } else if (jQuery && jQuery.fn.modal) {
            $('#' + id).modal('show');
        }
        }

        $(document).ready(function(){
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

            // Get Invoices related to Partner Selected
            $(document).on('change','#add-partneroffice-id',function(e){
                var partner_id = $(this).val();

                if (partner_id != '') {
                    $.ajax({
                        url: "{{ route('partner.payment.getinvoice') }}",
                        method: "GET",
                        type: "html",
                        data:{
                            id: partner_id
                        },
                        success: function(response){
                            if(response.post.length > 0){

                                var html = "";

                                response.post.forEach(data => {

                                    // Get Due Amount
                                    if (data.paid_amount != null) {
                                        var due_amount = data.invoice_amount - data.paid_amount;
                                    } else {
                                        var due_amount = data.invoice_amount;
                                    }

                                    html += "<tr><td><div class='form-check form-check-inline'><input class='form-check-input dt-checkboxes sub-chk' data-id='"+data.id+"' type='checkbox' value='"+data.id+"' id='checkbox"+data.id+"' /><label class='form-check-label' for='checkbox"+data.id+"'></label></div></td>";
                                    html += "<td><span>"+data.invoice_date+"</span></td><td>"+data.invoice_no+"</td><td><input type='hidden' class='due-amount-text' value='"+due_amount+"'><span class='invoice-amount-update-updt'>"+data.invoice_amount+"</span> <span class='text-danger due-amount-hide-text' style='font-size:12px;'> ("+due_amount+" pending)</span></td><td><span class='settled-amount-update'></span></td>";
                                    html += '</tr>';

                                });

                                $('.dynamic-invoice').html(html);
                                // $('.disableinvoicebtn').prop("disabled",false);
                            }else{
                                $('.disableinvoicebtn').prop("disabled",true);
                                $('.dynamic-invoice').html('');
                                $('#add-payment-amount').val('');
                            }



                        }
                    });
                }else{
                    $('.disableinvoicebtn').prop("disabled",true);
                    $('.dynamic-invoice').html('');
                    $('#add-payment-amount').val('');
                }
            });

            // Set Price and Candidate based on checkboxes
            $(document).on('click','.checkboxSelectAll',function(){
                var listcheckitem = $('.listitem :checkbox');
                var isCheckmarked = $(this).is(':checked');
                var checkedList = listcheckitem.length;



                listcheckitem.prop("checked", isCheckmarked);
                $('.disableinvoicebtn').prop('disabled',isCheckmarked);
                if (isCheckmarked == false) {
                    $('.disableinvoicebtn').prop('disabled',true);
                }

                updatecalculateamount();
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


                updatecalculateamount();

            });


            function updatecalculateamount(){
                let total_due_amount = 0;
                let total_invoice_amount = 0;
                let invoice_id = [];

                $('#getinvoiceTable tbody tr').each(function(){
                    let checkbox = $(this).find('.dt-checkboxes');
                    let due_amount = parseFloat($(this).find('.due-amount-text').val());
                    let settleAmountCell = $(this).find('.settled-amount-update');
                    let due_amount_hide_text = $(this).find('.due-amount-hide-text');
                    let invoice_amount = parseFloat($(this).find('.invoice-amount-update-updt').text());
                    let invoice_id_text = $(this).find('.sub-chk').val();

                    if (checkbox.is(':checked')) {
                        settleAmountCell.text(due_amount);
                        total_due_amount += due_amount;
                        total_invoice_amount += invoice_amount;
                        due_amount_hide_text.text('');

                        invoice_id.push(invoice_id_text.trim());

                    }else{
                        settleAmountCell.text(0);
                        due_amount_hide_text.text("("+due_amount+" pending)");
                    }

                });

                // alert(invoice_id);

                if (total_due_amount != 0) {
                    $('.disableinvoicebtn').prop('disabled',false);
                }

                // Update into form field
                $('#add-payment-amount').val(total_due_amount);
                $('#add-invoice-id').val(invoice_id);
                $('#total_settled_amount').text(total_due_amount);
                $('#total_inv_amount').text(total_invoice_amount);
            }

            // Get invoice from input amount
            $('.add-payment-amount-change').on('input',function(){
                let inputAmount = parseFloat($(this).val()) || 0;
                let inputFinal = 0;
                let total_due_amount = 0;
                let total_invoice_amount = 0;
                let allchecked = true;
                let invoice_id = [];

                $('#getinvoiceTable tbody tr').each(function(){
                    let checkbox = $(this).find('.dt-checkboxes');
                    let due_amount = parseFloat($(this).find('.due-amount-text').val());
                    let invoice_id_text = $(this).find('.sub-chk').val();
                    let settleAmountCell = $(this).find('.settled-amount-update');
                    let due_amount_hide_text = $(this).find('.due-amount-hide-text');
                    let invoice_amount = parseFloat($(this).find('.invoice-amount-update-updt').text());

                    if (inputAmount > 0) {
                        inputFinal += inputAmount;
                        // Checked If Condtion true
                        checkbox.prop('checked',true);

                        //Update Table Row as per calculation
                        total_invoice_amount += invoice_amount;

                        let settleamount = due_amount - inputAmount;
                        if (settleamount > 0) {
                            settleAmountCell.text(inputAmount);
                            due_amount_hide_text.text("("+settleamount+" pending)");
                        }else{
                            settleAmountCell.text(due_amount);
                            due_amount_hide_text.text('');

                        }

                        inputAmount -= due_amount;
                        total_due_amount += due_amount;


                        // Get invoice ID
                        invoice_id.push(invoice_id_text.trim());

                    } else {
                        checkbox.prop('checked',false);
                        allchecked = false;
                        settleAmountCell.text(0);
                        due_amount_hide_text.text("("+due_amount+" pending)");
                    }

                });




                if (total_due_amount != 0) {
                    $('.disableinvoicebtn').prop('disabled',false);
                }else{
                    $('.disableinvoicebtn').prop('disabled',true);

                }
                $('#checkboxSelectAll').prop('checked',allchecked);


                // update into form field
                // $('#add-payment-amount').val(total_invoice_amount);
                $('#add-invoice-id').val(invoice_id);
                $('#total_settled_amount').text(inputFinal);
                $('#total_inv_amount').text(total_invoice_amount);

            });

            // Form Validation before submit
            $('#addPaymentValidation').validate({
                rules:{
                    partneroffice_id:{
                        required: true
                    },
                    amount:{
                        required: true,
                        digits: true
                    },
                    payment_date:{
                        required: true
                    },
                    payment_mode:{
                        required: true
                    }
                },
                messages:{
                    partneroffice_id:{
                        required: "Please Select Partner office",
                    },
                    amount:{
                        required: "Please enter amount",
                        digits: "Please enter only digits"
                    },
                    payment_date:{
                        required: "Please select Payment Received date"
                    },
                    payment_mode:{
                        required: "Please select payment mode"
                    }
                }
            });

        });
    </script>

    <script>
        $(document).ready(function(){
            $('#editpayment').on('show.bs.offcanvas',function(e){
                var editID = $(e.relatedTarget).data('id');
                $('#edit-payment-id').val(editID);

                $.ajax({
                    url: "{{ route('partner.payment.edit') }}",
                    method: "GET",
                    type: "html",
                    data:{
                        id: editID
                    },
                    success: function(response){

                        // console.log(response);

                        $('#edit-partneroffice-id').val(response.post.partneroffice_id).change();
                        $('#edit-payment-amount').val(response.post.amount);
                        $('#edit-payment-date').val(response.post.payment_date);
                        $('#edit-payment-mode').val(response.post.payment_mode).change();
                        $('#edit-recieved-in').val(response.post.received_in).change();
                        $('#edit-transaction-id').val(response.post.transaction_id).change();
                        $('#edit-terms-condition').val(response.post.notes).change();

                        if (response.invoices.length > 0) {
                            var html = "";
                            var total_inv_amt = 0;

                            response.invoices.forEach(data => {
                                // Get Due Amount
                                if (data.paid_amount != null) {
                                    var due_amount = data.invoice_amount - data.paid_amount;
                                } else {
                                    var due_amount = data.invoice_amount;
                                }

                                if (data.checked == 1) {
                                    var checked = 'checked';
                                } else {
                                    var checked = '';
                                }


                                total_inv_amt = parseFloat(data.invoice_amount) + parseFloat(total_inv_amt);

                                html += "<tr><td><div class='form-check form-check-inline'><input class='form-check-input dt-checkboxes2 sub-chk2' data-id='"+data.invoice_id+"' type='checkbox' value='"+data.invoice_id+"' id='checkbox2"+data.invoice_id+"' "+checked+" /><label class='form-check-label' for='checkbox"+data.invoice_id+"'></label></div></td>";
                                html += "<td><span>"+data.invoice_date+"</span></td><td>"+data.invoice_no+"</td><td><input type='hidden' class='due-amount-text2' value='"+due_amount+"'><span class='invoice-amount-update-updt2'>"+data.invoice_amount+"</span> <span class='text-danger due-amount-hide-text2' style='font-size:12px;'> ("+due_amount+" pending)</span></td><td><span class='settled-amount-update2'>"+data.amount_settled+"</span></td>";
                                html += '</tr>';



                            });


                            $('.dynamic-invoice2').html(html);
                            $('.disableinvoicebtn2').prop("disabled",false);

                            $('#total_inv_amount2').text(total_inv_amt);
                            $('#total_settled_amount2').text(response.post.amount);

                        } else {
                            $('.dynamic-invoice2').html('');
                            $('.disableinvoicebtn2').prop("disabled",true);
                            $('#total_inv_amount2').text('');
                        }

                        // Check and Tick according to checked box
                        var listcheckitem2 = $('.listitem2 :checkbox');
                        var masterCheck = $('.checkboxSelectAll2');

                        // Total Checkboxes in list
                        var totalItems = listcheckitem2.length;
                        // Total Checked Checkboxes in list
                        var checkedItems = listcheckitem2.filter(":checked").length;
                        //If all are checked
                        if (totalItems == checkedItems) {
                            masterCheck.prop("indeterminate", false);
                            masterCheck.prop("checked", true);
                            $('.disableinvoicebtn2').prop("disabled",false);
                        }
                        // Not all but only some are checked
                        else if (checkedItems > 0 && checkedItems < totalItems) {
                            masterCheck.prop("indeterminate", true);
                            $('.disableinvoicebtn2').prop("disabled",false);
                        }
                        //If none is checked
                        else {
                            masterCheck.prop("indeterminate", false);
                            masterCheck.prop("checked", false);
                            $('.disableinvoicebtn2').prop("disabled",true);
                        }

                        if(response.post.payment_slip != "" && response.post.payment_slip != null){
                            $('#current-slip-box').html(`
                                <a href="${response.slip_full_path}" target="_blank" class="btn btn-xs btn-success">
                                    Download Payment Slip
                                </a>
                            `);
                        } else {
                            $('#current-slip-box').html(`<small class="text-muted">No slip uploaded</small>`);
                        }




                    }
                });

            });

            // Set Price and Candidate based on checkboxes
            $(document).on('click','.checkboxSelectAll2',function(){
                var listcheckitem = $('.listitem2 :checkbox');
                var isCheckmarked = $(this).is(':checked');
                var checkedList = listcheckitem.length;

                listcheckitem.prop("checked", isCheckmarked);
                $('.disableinvoicebtn2').prop('disabled',isCheckmarked);
                if (isCheckmarked == false) {
                    $('.disableinvoicebtn2').prop('disabled',true);
                }

                updatecalculateamount2();
            });

            $(document).on('change','.listitem2 :checkbox',function(){
                var listcheckitem = $('.listitem2 :checkbox');
                var masterCheck = $('.checkboxSelectAll2');
                // Total Checkboxes in list
                var totalItems = listcheckitem.length;
                // Total Checked Checkboxes in list
                var checkedItems = listcheckitem.filter(":checked").length;
                //If all are checked
                if (totalItems == checkedItems) {
                    masterCheck.prop("indeterminate", false);
                    masterCheck.prop("checked", true);
                    $('.disableinvoicebtn2').prop("disabled",false);
                }
                // Not all but only some are checked
                else if (checkedItems > 0 && checkedItems < totalItems) {
                    masterCheck.prop("indeterminate", true);
                    $('.disableinvoicebtn2').prop("disabled",false);
                }
                //If none is checked
                else {
                    masterCheck.prop("indeterminate", false);
                    masterCheck.prop("checked", false);
                    $('.disableinvoicebtn2').prop("disabled",true);
                }


                updatecalculateamount2();

            });

            function updatecalculateamount2(){
                let total_due_amount = 0;
                let total_invoice_amount = 0;
                let invoice_id = [];

                $('#getinvoiceTable2 tbody tr').each(function(){
                    let checkbox = $(this).find('.dt-checkboxes2');
                    let due_amount = parseFloat($(this).find('.due-amount-text2').val());
                    let due_amount_text = $(this).find('.due-amount-text2');
                    let settleAmountCell = $(this).find('.settled-amount-update2');
                    let settledAmount = parseFloat($(this).find('.settled-amount-update2').text());
                    let due_amount_hide_text = $(this).find('.due-amount-hide-text2');
                    let invoice_amount = parseFloat($(this).find('.invoice-amount-update-updt2').text());
                    let invoice_id_text = $(this).find('.sub-chk2').val();

                    if (checkbox.is(':checked')) {
                        var new_due_amount = due_amount + settledAmount; // 700 + 300 = 1000

                        total_due_amount += new_due_amount;
                        total_invoice_amount += invoice_amount;
                        due_amount_hide_text.text('');
                        settleAmountCell.text(new_due_amount);
                        due_amount_text.val(0);
                        invoice_id.push(invoice_id_text.trim());

                    } else {
                        var new_due_amount = due_amount + settledAmount; // 0 + 3000 = 3000

                        // Update Due Amount
                        due_amount_text.val(new_due_amount);
                        due_amount_hide_text.text("("+new_due_amount+" Pending)");
                        settleAmountCell.text(0);
                    }
                });

                // alert(total_due_amount);

                if (total_due_amount != 0) {
                    $('.disableinvoicebtn2').prop('disabled',false);
                }

                // Update into form field
                $('#edit-payment-amount').val(total_due_amount);
                $('#edit-invoice-id').val(invoice_id);
                $('#total_settled_amount2').text(total_due_amount);
                $('#total_inv_amount2').text(total_invoice_amount);
            }

            // Get invoice from input
            $('.edit-payment-amount-change').on('input',function(){
                let inputAmount = parseFloat($(this).val()) || 0;

                let inputFinal = 0;
                let textSettledamount = 0;
                let total_due_amount = 0;
                let total_invoice_amount = 0;
                let allchecked = true;
                let invoice_id = [];

                $('#getinvoiceTable2 tbody tr').each(function(){
                    let checkbox = $(this).find('.dt-checkboxes2');
                    let due_amount = parseFloat($(this).find('.due-amount-text2').val());
                    let invoice_id_text = $(this).find('.sub-chk2').val();
                    let settleAmountCell = $(this).find('.settled-amount-update2');
                    let due_amount_hide_text = $(this).find('.due-amount-hide-text2');
                    let invoice_amount = parseFloat($(this).find('.invoice-amount-update-updt2').text());
                    let settledAmount = parseFloat($(this).find('.settled-amount-update2').text());
                    let due_amount_text = $(this).find('.due-amount-text2');

                    let new_due_amount = settledAmount + due_amount; // 3000 + 0 = 3000 / 300 + 700 = 1000
                    if (inputAmount > 0) {

                        inputFinal += inputAmount;
                        // Checked If Condtion true
                        checkbox.prop('checked',true);

                        //Update Table Row as per calculation
                        total_invoice_amount += invoice_amount;

                        let in_settle_amount = new_due_amount - inputAmount;
                        if (in_settle_amount > 0) {
                            settleAmountCell.text(inputAmount);
                            due_amount_hide_text.text("("+in_settle_amount+" Pending)");
                            due_amount_text.val(in_settle_amount);

                            textSettledamount += inputAmount;
                        } else {
                            settleAmountCell.text(new_due_amount);
                            due_amount_hide_text.text('');
                            due_amount_text.val(0);

                            textSettledamount += new_due_amount;
                        }

                        inputAmount -= new_due_amount;
                        total_due_amount += new_due_amount;

                        // Get invoice ID
                        invoice_id.push(invoice_id_text.trim());

                    } else {
                        checkbox.prop('checked',false);
                        allchecked = false;
                        settleAmountCell.text(0);
                        due_amount_hide_text.text("("+new_due_amount+" pending)");
                        due_amount_text.val(new_due_amount);
                    }




                });
                if (total_due_amount != 0) {
                    $('.disableinvoicebtn2').prop('disabled',false);
                }else{
                    $('.disableinvoicebtn2').prop('disabled',true);

                }
                $('#checkboxSelectAll2').prop('checked',allchecked);


                // update into form field
                // $('#add-payment-amount').val(total_invoice_amount);
                $('#edit-invoice-id').val(invoice_id);
                $('#total_settled_amount2').text(textSettledamount);
                // $('#total_settled_amount2').text(inputFinal);
                $('#total_inv_amount2').text(total_invoice_amount);
            });

            // Form Validation before submit
            $('#editPaymentValidation').validate({
                rules:{
                    partneroffice_id:{
                        required: true
                    },
                    amount:{
                        required: true,
                        digits: true
                    },
                    payment_date:{
                        required: true
                    },
                    payment_mode:{
                        required: true
                    }
                },
                messages:{
                    partneroffice_id:{
                        required: "Please Select Partner office",
                    },
                    amount:{
                        required: "Please enter amount",
                        digits: "Please enter only digits"
                    },
                    payment_date:{
                        required: "Please select Payment Received date"
                    },
                    payment_mode:{
                        required: "Please select payment mode"
                    }
                }
            }); 
        });
    </script>

@endsection
