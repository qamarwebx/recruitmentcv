  <!-- Add Employer Canvas Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Employer</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addEmployerVisa" action="{{ route('admin.employer.storeVisaDet') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-visa-from">Business <span class="text-danger">*</span></label>
                                <select name="businesstype" id="add-visa-from" class="form-select select2" data-placeholder="Select Visa From...." data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="B2B Online">B2B Online</option>
                                    <option value="B2B Offline">B2B Offline</option>
                                    <option value="B2C Online">B2C Online</option>
                                    <option value="B2C Offline">B2C Offline</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-partner">Partner</label>
                                <select name="partner_office_id" id="add-partner" class="form-select select2" data-placeholder="Select Partner...." data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($partners as $partner)
                                        <option value="{{ $partner->id }}">{{ $partner->rec_off_name.' ('.$partner->rec_office_arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-employer-name">Employer Name</label>
                                <input type="text" name="employer_name" id="add-employer-name" class="form-control" placeholder="Enter Employer Name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-employer-ar-name">Employer Name (Arabic) <span class="text-danger">*</span></label>
                                <input type="text" name="employer_ar_name" id="add-employer-ar-name" class="form-control" placeholder="Enter Employer Name (Arabic)...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-visa-no">Visa No <span class="text-danger">*</span></label>
                                <input type="text" name="visa_no" id="add-visa-no" class="form-control" placeholder="Enter Visa No...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-id-no">ID No <span class="text-danger">*</span></label>
                                <input type="text" name="id_no" id="add-id-no" class="form-control" placeholder="Enter ID No...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-visa-date">Visa Date</label>
                                <input type="text" name="visa_date" id="add-visa-date" class="form-control" placeholder="Enter Visa Date...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-proff-id">Profession <span class="text-danger">*</span></label>
                                <select name="proff_id" id="add-proff-id" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession...">
                                    <option value=""></option>
                                    @foreach ($professions as $profession)
                                        <option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-issueing-authority">Issuing Authority <span class="text-danger">*</span></label>
                                <select name="issuing_authority" id="add-issueing-authority" class="form-select select2" data-placeholder="Select Visa Issuing Authority..." data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="Mumbai">Mumbai</option>
                                    <option value="New Delhi">New Delhi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-wpcity-id">City of Work <span class="text-danger">*</span></label>
                                <select name="wpcity_id" id="add-wpcity-id" class="form-select select2" data-allow-clear="true" data-placeholder="Select City of Work...">
                                    <option value=""></option>
                                    @foreach ($expworkcities as $expworkcity)
                                        <option value="{{ $expworkcity->id }}">{{ $expworkcity->name.' ('.$expworkcity->arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-salary">Monthly Salary</label>
                                <input type="text" name="salary" id="add-salary" class="form-control" placeholder="Enter Salary...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-notes">Notes</label>
                                <input type="text" name="notes" id="add-notes" class="form-control" placeholder="Enter Notes...">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Add Employer Canvas End -->

        <!-- Edit Employer Canvas Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editEmployerVisa" aria-labelledby="editEmployerVisaLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editEmployerVisaLabel" class="offcanvas-title">Edit Employer</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editEmployerVisaValidation" action="{{ route('admin.employer.updateVisaDet') }}" method="POST">
                    @csrf
                    <input type="hidden" name="edit_id" id="editID">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-visa-from">Business <span class="text-danger">*</span></label>
                                <select name="businesstype" id="edit-visa-from" class="form-select select2" data-placeholder="Select Visa From...." data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="B2B Online">B2B Online</option>
                                    <option value="B2B Offline">B2B Offline</option>
                                    <option value="B2C Online">B2C Online</option>
                                    <option value="B2C Offline">B2C Offline</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-partner">Partner</label>
                                <select name="partner_office_id" id="edit-partner" class="form-select select2" data-placeholder="Select Partner...." data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($partners as $partner)
                                        <option value="{{ $partner->id }}">{{ $partner->rec_off_name.' ('.$partner->rec_office_arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-employer-name">Employer Name</label>
                                <input type="text" name="employer_name" id="edit-employer-name" class="form-control" placeholder="Enter Employer Name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-employer-ar-name">Employer Name (Arabic) <span class="text-danger">*</span></label>
                                <input type="text" name="employer_ar_name" id="edit-employer-ar-name" class="form-control" placeholder="Enter Employer Name (Arabic)...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-visa-no">Visa No <span class="text-danger">*</span></label>
                                <input type="text" name="visa_no" id="edit-visa-no" class="form-control" placeholder="Enter Visa No...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-id-no">ID No <span class="text-danger">*</span></label>
                                <input type="text" name="id_no" id="edit-id-no" class="form-control" placeholder="Enter ID No...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-visa-date">Visa Date</label>
                                <input type="text" name="visa_date" id="edit-visa-date" class="form-control" placeholder="Enter Visa Date...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-proff-id">Profession <span class="text-danger">*</span></label>
                                <select name="proff_id" id="edit-proff-id" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession...">
                                    <option value=""></option>
                                    @foreach ($professions as $profession)
                                        <option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-issueing-authority">Issuing Authority <span class="text-danger">*</span></label>
                                <select name="issuing_authority" id="edit-issueing-authority" class="form-select select2" data-placeholder="Select Visa Issuing Authority..." data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="Mumbai">Mumbai</option>
                                    <option value="New Delhi">New Delhi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-wpcity-id">City of Work <span class="text-danger">*</span></label>
                                <select name="wpcity_id" id="edit-wpcity-id" class="form-select select2" data-allow-clear="true" data-placeholder="Select City of Work...">
                                    <option value=""></option>
                                    @foreach ($expworkcities as $expworkcity)
                                        <option value="{{ $expworkcity->id }}">{{ $expworkcity->name.' ('.$expworkcity->arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-salary">Monthly Salary</label>
                                <input type="text" name="salary" id="edit-salary" class="form-control" placeholder="Enter Salary...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-notes">Notes</label>
                                <input type="text" name="notes" id="edit-notes" class="form-control" placeholder="Enter Notes...">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Edit Employer Canvas End -->