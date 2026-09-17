{{-- Add Transaction --}}
<div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="offcanvasAddTransaction" data-bs-backdrop="static">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Add Transaction</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
        <form id="addTransactionForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="text" name="transaction_date" class="form-control txn-date" required>
                    <div class="invalid-feedback d-block text-danger small add-transaction-date-error"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="transaction_type" id="add-type" class="form-select select2s" required>
                        <option value="Fund">Fund</option>
                        <option value="Advance">Advance</option>
                        <option value="Loan">Loan</option>
                        <option value="Receivable">Receivable</option>
                        <option value="Payable">Payable</option>
                    </select>
                    <div class="invalid-feedback d-block text-danger small add-transaction-type-error"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nature <span class="text-danger">*</span></label>
                    <select name="transaction_nature" id="add-nature" class="form-select select2s" required></select>
                    <div class="invalid-feedback d-block text-danger small add-transaction-nature-error"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Party Type <span class="text-danger">*</span></label>
                    <select name="party_type" id="add-party-type" class="form-select select2s" required>
                        <option value="partner">Partner</option>
                        <option value="contact">Contact</option>
                        <option value="employee">Employee</option>
                        <option value="other">Other</option>
                    </select>
                    <div class="invalid-feedback d-block text-danger small add-party-type-error"></div>
                </div>
                <div class="col-md-8 mb-3" id="add-party-select-wrap">
                    <label class="form-label">Party / Employee</label>
                    <select name="party_id" id="add-party-id" class="form-select select2t"></select>
                </div>
                <div class="col-md-8 mb-3 d-none" id="add-party-name-wrap">
                    <label class="form-label">Party Name <span class="text-danger">*</span></label>
                    <input type="text" name="party_name" id="add-party-name" class="form-control" placeholder="Enter party/vendor name">
                    <div class="invalid-feedback d-block text-danger small add-party-name-error"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Amount <span class="text-danger">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0.01" class="form-control" required>
                    <div class="invalid-feedback d-block text-danger small add-amount-error"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                    <select name="payment_mode" class="form-select select2s" required>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="UPI">UPI</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Card">Card</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Reference No.</label>
                    <input type="text" name="reference_no" class="form-control">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Attachment</label>
                    <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    <div class="form-text">JPG, PNG or PDF, max 5MB.</div>
                    <div class="invalid-feedback d-block text-danger small add-attachment-error"></div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </form>
    </div>
</div>

{{-- Edit Transaction --}}
<div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="offcanvasEditTransaction" data-bs-backdrop="static">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Edit Transaction</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
        <form id="editTransactionForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="edit-id">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="text" name="transaction_date" id="edit-transaction-date" class="form-control txn-date" required>
                    <div class="invalid-feedback d-block text-danger small edit-transaction-date-error"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="transaction_type" id="edit-type" class="form-select select2s" required>
                        <option value="Fund">Fund</option>
                        <option value="Advance">Advance</option>
                        <option value="Loan">Loan</option>
                        <option value="Receivable">Receivable</option>
                        <option value="Payable">Payable</option>
                    </select>
                    <div class="invalid-feedback d-block text-danger small edit-transaction-type-error"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nature <span class="text-danger">*</span></label>
                    <select name="transaction_nature" id="edit-nature" class="form-select select2s" required></select>
                    <div class="invalid-feedback d-block text-danger small edit-transaction-nature-error"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Party Type <span class="text-danger">*</span></label>
                    <select name="party_type" id="edit-party-type" class="form-select select2s" required>
                        <option value="partner">Partner</option>
                        <option value="contact">Contact</option>
                        <option value="employee">Employee</option>
                        <option value="other">Other</option>
                    </select>
                    <div class="invalid-feedback d-block text-danger small edit-party-type-error"></div>
                </div>
                <div class="col-md-8 mb-3" id="edit-party-select-wrap">
                    <label class="form-label">Party / Employee</label>
                    <select name="party_id" id="edit-party-id" class="form-select select2t"></select>
                </div>
                <div class="col-md-8 mb-3 d-none" id="edit-party-name-wrap">
                    <label class="form-label">Party Name <span class="text-danger">*</span></label>
                    <input type="text" name="party_name" id="edit-party-name" class="form-control">
                    <div class="invalid-feedback d-block text-danger small edit-party-name-error"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Amount <span class="text-danger">*</span></label>
                    <input type="number" name="amount" id="edit-amount" step="0.01" min="0.01" class="form-control" required>
                    <div class="invalid-feedback d-block text-danger small edit-amount-error"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                    <select name="payment_mode" id="edit-payment-mode" class="form-select select2s" required>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="UPI">UPI</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Card">Card</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Reference No.</label>
                    <input type="text" name="reference_no" id="edit-reference-no" class="form-control">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="edit-description" class="form-control" rows="3"></textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Replace Attachment</label>
                    <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    <div class="form-text">Leave blank to keep the existing attachment.</div>
                    <div class="invalid-feedback d-block text-danger small edit-attachment-error"></div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </form>
    </div>
</div>
