<!--- Add Candidate Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add New Contact</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.contact.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-business-type" class="form-label">Business Type <span class="text-danger">*</span></label>
                                <select name="businesstype_id" id="add-business-type" class="form-select select22" data-placeholder="Select Business Type" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($businesstypes as $businesstype)
                                        <option value="{{ $businesstype->id }}" @if($businesstype->name == 'B2C') selected @endif>{{ $businesstype->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label for="add-office-name-eng" class="form-label">Office / Company Name (Eng) <span class="text-danger">*</span></label>
                                    <input type="text" name="office_eng_name" id="add-office-name-eng" class="form-control" placeholder="Enter Office Name (eng)...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-office-name-ar" class="form-label">Office / Company Name (Arabic)</label>
                                <input type="text" name="office_ar_name" id="add-office-name-ar" class="form-control" placeholder="Enter Office Name (arabic)...">
                            </div>
                        </div>

                        <div class="col-md-6 showDIvInd" style="display: none">
                            <div class="mb-3">
                                <label for="add-industry" class="form-label">Industry</label>
                                <select name="industry_id" id="add-industry" class="form-select select22" data-placeholder="Select Industry" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($industries as $industry)
                                        <option value="{{ $industry->id }}">{{ $industry->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-office-no" class="form-label">Office Number</label>
                                <input type="text" name="office_no" id="add-office-no" class="form-control" placeholder="Enter Office Contact No...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-office-email" class="form-label">Office Email</label>
                                <input type="text" name="office_email" id="add-office-email" class="form-control" placeholder="Enter Office Email...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-owner-name" class="form-label">Owner Name</label>
                                <input type="text" name="owner_name" id="add-owner-name" class="form-control" placeholder="Enter Owner Name...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-owner-contact" class="form-label">Owner Contact</label>
                                <input type="text" name="owner_contact" id="add-owner-contact" class="form-control" placeholder="Enter Owner Phone...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv" style="display: none">
                            <div class="mb-3">
                                <label for="add-owner-email" class="form-label">Owner Email</label>
                                <input type="text" name="owenr_email" id="add-owner-email" class="form-control" placeholder="Enter Owner Email...">
                            </div>
                        </div> --}}

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-country-id">Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="add-country-id" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-city-id">City</label>
                                <select name="city_id" id="add-city-id" class="form-select select22" data-placeholder="Select City" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-primary-person" class="form-label">Primary Concern Person Name</label>
                                <input type="text" name="prim_concern_name" id="add-primary-person" class="form-control" placeholder="Enter Primary Concern Person...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-primary-contact" class="form-label">Primary Contact No</label>
                                <input type="text" name="prim_contact" id="add-primary-contact" class="form-control checkNoExistance" placeholder="Enter Primary Contact No...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-primary-email" class="form-label">Primary Email</label>
                                <input type="text" name="prim_email" id="add-primary-email" class="form-control" placeholder="Enter Primary Email...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-secondary-person" class="form-label">Seconday Concern Person Name</label>
                                <input type="text" name="sec_concern_name" id="add-secondary-person" class="form-control" placeholder="Enter Secondary Concern Person...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-secondary-contact" class="form-label">Seconday Contact No</label>
                                <input type="text" name="sec_contact" id="add-secondary-contact" class="form-control checkNoExistance" placeholder="Enter Secondary Contact No...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-secondary-email" class="form-label">Seconday Email</label>
                                <input type="text" name="sec_email" id="add-secondary-email" class="form-control" placeholder="Enter Seconday Email...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-person3" class="form-label">Concern Person Name 3</label>
                                <input type="text" name="concern_name3" id="add-person3" class="form-control" placeholder="Enter Concern Person 3...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact3" class="form-label">Contact No3</label>
                                <input type="text" name="contact3" id="add-contact3" class="form-control checkNoExistance" placeholder="Enter Contact No3...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-person4" class="form-label">Concern Person Name 4</label>
                                <input type="text" name="concern_name4" id="add-person4" class="form-control" placeholder="Enter Concern Person4...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact4" class="form-label">Contact No4</label>
                                <input type="text" name="contact4" id="add-contact4" class="form-control checkNoExistance" placeholder="Enter Contact No4...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-person5" class="form-label">Concern Person Name 5</label>
                                <input type="text" name="concern_name5" id="add-person5" class="form-control" placeholder="Enter Concern Person5...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact5" class="form-label">Contact No5</label>
                                <input type="text" name="contact5" id="add-contact5" class="form-control checkNoExistance" placeholder="Enter Contact No5...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-person6" class="form-label">Concern Person Name6</label>
                                <input type="text" name="concern_name6" id="add-person6" class="form-control" placeholder="Enter Concern Person6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact6" class="form-label">Contact No6</label>
                                <input type="text" name="contact6" id="add-contact6" class="form-control checkNoExistance" placeholder="Enter Contact No6...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact7" class="form-label">Contact No7</label>
                                <input type="text" name="contact7" id="add-contact7" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact8" class="form-label">Contact No8</label>
                                <input type="text" name="contact8" id="add-contact8" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact9" class="form-label">Contact No9</label>
                                <input type="text" name="contact9" id="add-contact9" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact10" class="form-label">Contact No10</label>
                                <input type="text" name="contact10" id="add-contact10" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact11" class="form-label">Contact No11</label>
                                <input type="text" name="contact11" id="add-contact11" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact12" class="form-label">Contact No12</label>
                                <input type="text" name="contact12" id="add-contact12" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-careoff" class="form-label">Careoff <span class="text-danger">*</span></label>
                                <select name="careoff_id" id="add-careoff" class="form-select select22" data-placeholder="Select Careoff" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($adminusers as $adminuser)
                                        <option value="{{ $adminuser->id }}" @if($adminuser->id == Auth::guard('admin')->user()->id) selected @endif>{{ $adminuser->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-leadowner" class="form-label">Lead Owner <span class="text-danger">*</span></label>
                                <select name="leadowner_id" id="add-leadowner" class="form-select select22" data-placeholder="Select Lead Owner" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($adminusers as $adminuser2)
                                        <option value="{{ $adminuser2->id }}" @if($adminuser2->id == Auth::guard('admin')->user()->id) selected @endif>{{ $adminuser2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="addnewcandidateBtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Add Candidate End --->

        <!--- Edit Candidate Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="edituserLabel" class="offcanvas-title">Edit Contact</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.contact.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="editID" id="edit_ID">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-business-type" class="form-label">Business Type</label>
                                <select name="businesstype_id" id="edit-business-type" class="form-select select22" data-placeholder="Select Business Type" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($businesstypes as $businesstype2)
                                        <option value="{{ $businesstype2->id }}">{{ $businesstype2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-name-eng" class="form-label">Office Name (Eng) <span class="text-danger">*</span></label>
                                <input type="text" name="office_eng_name" id="edit-office-name-eng" class="form-control" placeholder="Enter Office Name (eng)...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-name-ar" class="form-label">Office Name (Arabic)</label>
                                <input type="text" name="office_ar_name" id="edit-office-name-ar" class="form-control" placeholder="Enter Office Name (arabic)...">
                            </div>
                        </div>

                        <div class="col-md-6 showDIvInd2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-industry-id" class="form-label">Industry</label>
                                <select name="industry_id" id="edit-industry-id" class="form-select select22" data-placeholder="Select Industry" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($industries as $industry)
                                        <option value="{{ $industry->id }}">{{ $industry->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-no" class="form-label">Office Number</label>
                                <input type="text" name="office_no" id="edit-office-no" class="form-control" placeholder="Enter Office Contact No...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-email" class="form-label">Office Email</label>
                                <input type="text" name="office_email" id="edit-office-email" class="form-control" placeholder="Enter Office Email...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-owner-name" class="form-label">Owner Name</label>
                                <input type="text" name="owner_name" id="edit-owner-name" class="form-control" placeholder="Enter Owner Name...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-owner-contact" class="form-label">Owner Contact</label>
                                <input type="text" name="owner_contact" id="edit-owner-contact" class="form-control" placeholder="Enter Owner Phone...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-owner-email" class="form-label">Owner Email</label>
                                <input type="text" name="owenr_email" id="edit-owner-email" class="form-control" placeholder="Enter Owner Email...">
                            </div>
                        </div> --}}

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-country-id">Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="edit-country-id" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-city-id">City</label>
                                <select name="city_id" id="edit-city-id" class="form-select select22" data-placeholder="Select City" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-primary-person" class="form-label">Primary Concern Person Name</label>
                                <input type="text" name="prim_concern_name" id="edit-primary-person" class="form-control" placeholder="Enter Primary Concern Person...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-primary-contact" class="form-label">Primary Contact No</label>
                                <input type="text" name="prim_contact" id="edit-primary-contact" class="form-control checkNoExistance" placeholder="Enter Primary Contact No...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-primary-email" class="form-label">Primary Email</label>
                                <input type="text" name="prim_email" id="edit-primary-email" class="form-control" placeholder="Enter Primary Email...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-secondary-person" class="form-label">Seconday Concern Person Name</label>
                                <input type="text" name="sec_concern_name" id="edit-secondary-person" class="form-control" placeholder="Enter Secondary Concern Person...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-secondary-contact" class="form-label">Seconday Contact No</label>
                                <input type="text" name="sec_contact" id="edit-secondary-contact" class="form-control checkNoExistance" placeholder="Enter Secondary Contact No...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-secondary-email" class="form-label">Seconday Email</label>
                                <input type="text" name="sec_email" id="edit-secondary-email" class="form-control " placeholder="Enter Seconday Email...">

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person3" class="form-label">Concern Person Name 3</label>
                                <input type="text" name="concern_name3" id="edit-person3" class="form-control" placeholder="Enter Concern Person 3...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact3" class="form-label">Contact No3</label>
                                <input type="text" name="contact3" id="edit-contact3" class="form-control checkNoExistance" placeholder="Enter Contact No3...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person4" class="form-label">Concern Person Name 4</label>
                                <input type="text" name="concern_name4" id="edit-person4" class="form-control" placeholder="Enter Concern Person4...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact4" class="form-label">Contact No4</label>
                                <input type="text" name="contact4" id="edit-contact4" class="form-control checkNoExistance" placeholder="Enter Contact No4...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person5" class="form-label">Concern Person Name 5</label>
                                <input type="text" name="concern_name5" id="edit-person5" class="form-control" placeholder="Enter Concern Person5...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact5" class="form-label">Contact No5</label>
                                <input type="text" name="contact5" id="edit-contact5" class="form-control checkNoExistance" placeholder="Enter Contact No5...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person6" class="form-label">Concern Person Name6</label>
                                <input type="text" name="concern_name6" id="edit-person6" class="form-control" placeholder="Enter Concern Person6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact6" class="form-label">Contact No6</label>
                                <input type="text" name="contact6" id="edit-contact6" class="form-control checkNoExistance" placeholder="Enter Contact No6...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact7" class="form-label">Contact No7</label>
                                <input type="text" name="contact7" id="edit-contact7" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact8" class="form-label">Contact No8</label>
                                <input type="text" name="contact8" id="edit-contact8" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact9" class="form-label">Contact No9</label>
                                <input type="text" name="contact9" id="edit-contact9" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact10" class="form-label">Contact No10</label>
                                <input type="text" name="contact10" id="edit-contact10" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact11" class="form-label">Contact No11</label>
                                <input type="text" name="contact11" id="edit-contact11" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact12" class="form-label">Contact No12</label>
                                <input type="text" name="contact12" id="edit-contact12" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-careoff" class="form-label">Careoff <span class="text-danger">*</span></label>
                                <select name="careoff_id" id="edit-careoff" class="form-select select22" data-placeholder="Select Careoff" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($adminusers as $adminuser3)
                                        <option value="{{ $adminuser3->id }}">{{ $adminuser3->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-leadowner" class="form-label">Lead Owner <span class="text-danger">*</span></label>
                                <select name="leadowner_id" id="edit-leadowner" class="form-select select22" data-placeholder="Select Lead Owner" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($adminusers as $adminuser4)
                                        <option value="{{ $adminuser4->id }}">{{ $adminuser4->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editnewcontact">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Edit Candidate End --->
        <!-- Delete Contactplus Start -->

        <div class="modal fade" id="deleteStaff" aria-hidden="true" aria-labelledby="deleteStaffLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Delete Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.contact.delete') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="contactID" id="contactID2">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                <div class="mb-3">
                                    <p class="text-danger">Are you sure to delete contact?</p>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Contactplus Start -->
        <!-- Lead Stage Update Start -->
        <div class="modal fade" id="updateLstage" aria-hidden="true" aria-labelledby="updateLstageLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
            <div class="modal-content">
                <div class="modal-header pb-2">
                <h5 class="offcanvas-title" id="updateLstageLabel">Lead Stage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.stageUpdate') }}" method="POST" id="updateLeadStageValidation">
                    @csrf
                    <input type="hidden" name="contactID" id="contactID">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="update-lead-cycle-status" class="form-label">Life Cycle Status <span class="text-danger">*</span></label>
                                        <select name="lcs_id" id="update-lead-cycle-status" class="form-control select22" data-allow-clear="true" data-placeholder="Select Lead Cycle Status">
                                            <option value=""></option>
                                            @foreach ($lcss as $lcs)
                                                <option value="{{ $lcs->id }}">{{ $lcs->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="update-lead-stage" class="form-label">Lead Stage <span class="text-danger">*</span></label>
                                        <select name="ls_id" id="update-lead-stage" class="form-control select22" data-allow-clear="true" data-placeholder="Select Lead...">

                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer">
                            {{-- <button class="btn btn-primary" type="submit">Update Lead Stage</button> --}}
                            <button class="btn btn-primary" type="button" id="leadStageUpdateBtn">Update Lead Stage</button>
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </div>
        <!-- Lead Stage Update End -->

        <!-- Filter List Start-->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="filter" aria-labelledby="filterLabel">
            <div class="offcanvas-header">
                <h5 id="filterLabel" class="offcanvas-title">Add Filter</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <div class="row">
                    <div class="col-md-12">
                        <div class="all-check">
                            <div class="form-check mt-2" id="shortform-f">
                                <input class="form-check-input" type="checkbox" name="shortformf" id="shortformf" value="1" @if(isset($contactpfilter) && $contactpfilter->short_form_filter == 1) checked @endif>
                                <label class="form-check-label" for="shortformf">Short Form</label>
                            </div>
                            <div class="form-check mt-2" id="country-f">
                                <input class="form-check-input" type="checkbox" name="countryf" value="1" id="countryf" @if(isset($contactpfilter) && $contactpfilter->country_filter == 1) checked @endif />
                                <label class="form-check-label" for="countryf"> Country</label>
                            </div>
                            <div class="form-check mt-2" id="city-f">
                                <input class="form-check-input" type="checkbox" name="cityf" value="1" id="cityf" @if(isset($contactpfilter) && $contactpfilter->city_filter == 1) checked @endif />
                                <label class="form-check-label" for="cityf"> City</label>
                            </div>
                            <div class="form-check mt-2" id="groupname-f">
                                <input class="form-check-input" type="checkbox" name="groupnamef" value="1" id="groupnamef" @if(isset($contactpfilter) && $contactpfilter->group_name_filter == 1) checked @endif />
                                <label class="form-check-label" for="groupnamef"> Group Name</label>
                            </div>
                            <div class="form-check mt-2" id="lifecyclestatus-f">
                                <input class="form-check-input" type="checkbox" name="lifecyclestatusf" value="1" id="lifecyclestatusf" @if(isset($contactpfilter) && $contactpfilter->life_cycle_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="lifecyclestatusf"> Life Cycle Status</label>
                            </div>
                            <div class="form-check mt-2" id="leadstage-f">
                                <input class="form-check-input" type="checkbox" name="leadstagef" value="1" id="leadstagef" @if(isset($contactpfilter) && $contactpfilter->lead_stage_filter == 1) checked @endif />
                                <label class="form-check-label" for="leadstagef"> Lead Stage</label>
                            </div>
                            <div class="form-check mt-2" id="businesstype-f">
                                <input class="form-check-input" type="checkbox" name="businesstypef" value="1" id="businesstypef" @if(isset($contactpfilter) && $contactpfilter->business_type_filter == 1) checked @endif />
                                <label class="form-check-label" for="businesstypef"> Business Type</label>
                            </div>
                            <div class="form-check mt-2" id="createddate-f">
                                <input class="form-check-input" type="checkbox" name="createddatef" value="1" id="createddatef" @if(isset($contactpfilter) && $contactpfilter->created_date_filter == 1) checked @endif />
                                <label class="form-check-label" for="createddatef"> Created Date</label>
                            </div>

                            <div class="form-check mt-2" id="createby-f">
                                <input class="form-check-input" type="checkbox" name="createbyf" value="1" id="createbyf" @if(isset($contactpfilter) && $contactpfilter->created_by == 1) checked @endif />
                                <label class="form-check-label" for="createbyf"> Created By</label>
                            </div>

                            <div class="mt-3">
                                <a href="#" id="all-chk"><span class="badge bg-label-primary">Select all</span></a>
                                <a href="#" id="all-unchk"><span class="badge bg-label-primary">Unselect all</span></a>
                                <a href="#" id="default-chk"><span class="badge bg-label-primary">Basic</span></a>
                                <a href="#" id="update-chk"><span class="badge bg-label-primary">Update</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filter List End -->

        <!-- Bulk Send Whatsapp Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="bulkwhatsappsend" aria-labelledby="bulkwhatsappsendLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="bulkwhatsappsendLabel" class="offcanvas-title">Send Bulk Whatsapp</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="bulksendwhatsapp" action="{{ route('admin.contact.bulksendwhatsapp') }}" method="POST" >
                    @csrf
                    <div class="row">
                        <input type="hidden" id="contactpID2" name="contactpID">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="send-whatsapp-type" class="form-label">For Whatsapp <span class="text-danger">*</span></label>
                                <select name="send_whatsapp_type" id="send-whatsapp-type" class="form-select select22" data-placeholder="Select For Whatsapp" data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="meta_whatsapp">Meta Whatsapp</option>
                                    <option value="normal_whatsapp" selected>Normal Whatsapp</option>
                                </select>
                            </div>
                        </div>
                        <!-- Meta Whatsapp Display Start -->
                        <div class="col-md-4 dismetawhatsapp" style="display: none">
                            <div class="mb-3">
                                <label for="send-meta-template-name" class="form-label">Meta Template <span class="text-danger">*</span></label>
                                <select name="metatemplate_id" id="send-meta-template-name" class="form-select select22" data-placeholder="Select Meta Template" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($metatemplates as $metatemplate)
                                        <option value="{{ $metatemplate->id }}">{{ $metatemplate->template_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Meta Whatsapp Display End -->
                        <!-- Normal Whatsapp Display Start -->
                        <div class="col-md-4 disnormalwhatsapp">
                            <div class="mb-3">
                                <label for="send-template-name" class="form-label">Template Name <span class="text-danger">*</span></label>
                                <select name="template_id" id="send-template-name" class="form-select select22" data-placeholder="Select Template Name" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($normaltemplates as $normaltemplate)
                                        <option value="{{ $normaltemplate->id }}">{{ $normaltemplate->template_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 disnormalwhatsapp">
                            <div class="mb-3">
                                <label class="form-label" for="send-template-whatsapp-api">Select Whatsapp API <span class="text-danger">*</span></label>
                                <select name="wapi_id_text[]" id="send-template-whatsapp-api" class="form-select select22"  multiple data-placeholder="Select Whatsapp API...">
                                    <option value=""></option>
                                    @foreach ($wapis as $wapi)
                                        <option value="{{ $wapi->id }}">{{ $wapi->mobile_no }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Common Element Start -->
                        <div class="col-md-4 disallforwhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-contact-type" class="form-label">Contact Type <span class="text-danger">*</span></label>
                                <select name="contact_type[]" id="send-meta-template-contact-type" class="form-select select22" data-placeholder="Select Contact Type" multiple>
                                    <option value="all" selected>All</option>
                                    <option value="owner">Owner</option>
                                    <option value="Primary">Primary</option>
                                    <option value="Secondary">Secondary</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 disallforwhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-contact-status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="contact_status[]" id="send-meta-template-contact-status" class="form-select select22" multiple data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 disallforwhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-subscribe" class="form-label">Subscribe <span class="text-danger">*</span></label>
                                <select name="subscribe[]" id="send-meta-template-subscribe" class="form-select select22" multiple data-placeholder="Select Subscribe">
                                    <option value=""></option>
                                    <option value="1">Subscribe</option>
                                    <option value="0">Unsubscribe</option>
                                </select>
                            </div>
                        </div>
                        <!-- Common Element End -->


                        <div class="col-md-4 disallforwhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-contact-group" class="form-label">Group</label>
                                <select name="contact_group_id[]" id="send-meta-template-contact-group" class="form-select select22" multiple data-placeholder="Select Group...">
                                    <option value="">Select Group</option>
                                    @foreach ($groupms as $groupm)
                                        <option value="{{ $groupm->id }}">{{ $groupm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 disnormalwhatsapp">
                            <div class="mb-3">
                                <label for="send-template-personalise-class" class="form-label">Personalise Name</label>
                                <select name="personalise_class" id="send-template-personalise-class" class="form-select select22" data-allow-clear="true" data-placeholder="Select Personalise Class">
                                    <option value=""></option>
                                    <option value="[Company]">Company</option>
                                    <option value="[Office Name (English)]">Office Name (English)</option>
                                    <option value="[Office Name (Arabic)]">Office Name (Arabic)</option>
                                    <option value="[Country]">Country</option>
                                    <option value="[City]">City</option>
                                    <option value="[Primary Concern Person]">Primary Concern Person</option>
                                    <option value="[Primary Contact No]">Primary Contact No</option>
                                    <option value="[Primary Email]">Primary Email</option>
                                    <option value="[Secondary Concern Person]">Secondary Concern Person</option>
                                    <option value="[Secondary Contact No]">Secondary Contact No</option>
                                    <option value="[Secondary Email]">Secondary Email</option>
                                    <option value="[Concern Person 3]">Concern Person 3</option>
                                    <option value="[Contact No 3]">Contact No 3</option>
                                    <option value="[Concern Person 4]">Concern Person 4</option>
                                    <option value="[Contact No 4]">Contact No 4</option>
                                    <option value="[Concern Person 5]">Concern Person 5</option>
                                    <option value="[Contact No 5]">Contact No 5</option>
                                    <option value="[Concer Person 6]">Concer Person 6</option>
                                    <option value="[Contact No 6]">Contact No 6</option>
                                    <option value="[Status]">Status</option>
                                    <option value="[Unsubscribe]">Unsubscribe</option>
                                </select>
                            </div>
                        </div>
                        <!-- Normal Whatsapp Display End -->
                    </div>
                    <!-- Meta Whatsapp Display Start -->

                    <div class="row dismetawhatsapp" style="display: none">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="send-meta-template-whatsapp-message" class="form-label">Whatsapp Message</label>
                                <textarea name="msg_whatsapp" id="send-meta-template-whatsapp-message" class="form-control" cols="30" rows="14"></textarea>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="mb-3">
                                <label for="send-meta-template-whatsapp-message-ar" class="form-label">Whatsapp Message Arabic</label>
                                <textarea name="msg_whatsapp_ar" id="send-meta-template-whatsapp-message-ar" class="form-control" cols="30" rows="5"></textarea>
                            </div>
                        </div> --}}

                        <div class="col-md-6">
                            <div class="mb-3">
                                <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar" id="uploadedAvatar"/>
                            </div>
                        </div>
                    </div>
                    <div class="row dismetawhatsapp" style="display: none">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-campaign-type" class="form-label">Campaign Type <span class="text-danger">*</span></label>
                                <select name="campaign_type" id="add-campaign-type" class="form-select select22" data-allow-clear="true" data-placeholder="Campaign Type...">
                                    <option value=""></option>
                                    <option value="1">Now</option>
                                    <option value="2">Scheduled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="disdateandtime" style="display: none">
                            <div class="mb-3">
                                <label for="add-date-and-time" class="form-label">Date and Time <span class="text-danger">*</span></label>
                                <input type="text" name="date_and_time" id="add-date-and-time" class="form-control flatpickr-datetime" placeholder="Enter date and time...">
                            </div>
                        </div>
                    </div>
                    <!-- Meta Whatsapp Display End -->
                    <!-- Normal Whatsapp Display Start -->
                    <div class="row disnormalwhatsapp">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="send-template-message" class="form-label">Whatsapp Message</label>
                                <textarea name="whatsapp_message" class="form-control" id="send-template-message" cols="30" rows="10"></textarea>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="mb-3">
                                <label for="send-template-message-ar" class="form-label">Whatsapp Message Arabic</label>
                                <textarea name="msg_whatsapp_ar" class="form-control" id="send-template-message-ar" cols="30" rows="6"></textarea>
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2"/>
                            </div>
                        </div>
                    </div>
                    <div class="row disnormalwhatsapp">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-campaign-type2" class="form-label">Campaign Type <span class="text-danger">*</span></label>
                                <select name="campaign_type2" id="add-campaign-type2" class="form-select select22" data-allow-clear="true" data-placeholder="Campaign Type...">
                                    <option value=""></option>
                                    <option value="1">Now</option>
                                    <option value="2">Scheduled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="disdateandtime2" style="display: none">
                            <div class="mb-3">
                                <label for="add-date-and-time2" class="form-label">Date and Time <span class="text-danger">*</span></label>
                                <input type="text" name="date_and_time2" id="add-date-and-time2" class="form-control flatpickr-datetime2" placeholder="Enter date and time...">
                            </div>
                        </div>
                    </div>
                    <!-- Normal Whatsapp Display End -->
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Bulk Send Whatsapp End -->

        <!-- Bulk Transfer Lead Owner Start -->
        <div class="modal fade" id="bulktransferleadowner" aria-hidden="true" aria-labelledby="bulktransferleadownerLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulktransferleadownerLabel">Transfer Lead Owner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.bulkleadownertransfer') }}" method="POST" id="bulktransferleadownevalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDLTR" id="contactIDLTRB">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="transfer-lead-owner" class="form-label">Transfer Lead Owner <span class="text-danger">*</span></label>
                                        <select name="leadowner_id" id="transfer-lead-owner" class="form-select select22" data-placeholder="Select Lead Owner" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($adminusers as $adminuserl)
                                                <option value="{{ $adminuserl->id }}">{{ $adminuserl->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Transfer Lead Owner</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Transfer Lead Owner End -->

        <!-- Bulk Transfer Careoff Start -->
        <div class="modal fade" id="bulktransfercareoff" aria-hidden="true" aria-labelledby="bulktransfercareoffLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulktransfercareoffLabel">Transfer Careoff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.bulkcareofftransfer') }}" method="POST" id="bulktransfercareoffvalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDCTR" id="contactIDCTR">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="transfer-careoff" class="form-label">Transfer Careoff <span class="text-danger">*</span></label>
                                        <select name="careoff_id" id="transfer-careoff" class="form-select select22" data-placeholder="Select Careoff" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($adminusers as $adminuser2)
                                                <option value="{{ $adminuser2->id }}">{{ $adminuser2->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Transfer Careoff</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Transfer Careoff End -->

        <!-- Bulk Group Transfer Start -->
        <div class="modal fade" id="bulktransfergroup" aria-hidden="true" aria-labelledby="bulktransfergroupLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulktransfergroupLabel">Transfer Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.bulkgrouptransfer') }}" method="POST" id="bulktransfergroupvalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDGRPTR" id="contactIDGRPTR">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <p id="totlaTransferGrp" class="text-success"></p>
                                        <label for="transfer-groupm" class="form-label">Transfer Group <span class="text-danger">*</span></label>
                                        <select name="group_id" id="transfer-groupm" class="form-select select22" data-placeholder="Select Group" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($groupms as $groupm)
                                                <option value="{{ $groupm->id }}">{{ $groupm->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <p id="respMessageGroupTransfer"></p>
                                    <p id="respGroupLimit"></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary dibtngrp" type="submit">Transfer Group</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Group Transfer End -->

        <!-- Filter Panel Start -->
        <div class="modal fade" id="filterpanel" aria-hidden="true" aria-labelledby="filterpanelLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="filterpanelLabel">Contactplus Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <select id="by-business-type" class="form-select select22f" multiple data-placeholder="Select Business Type">
                                    <option value="">Select Business Type</option>
                                    @foreach ($businessTypes as $businessType)
                                        <option value="{{ $businessType->businesstype_id }}" @if(isset($contactsaveadminfilter) && in_array($businessType->businesstype_id,explode(",",$contactsaveadminfilter->businesstype_id))) selected @endif>{{ $businessType->businame }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select name="" id="by-industries" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Industry">
                                    {{-- <select name="" id="by-industries" class="form-select select22f" multiple data-placeholder="Select Industry"> --}}
                                    {{-- <option value="">Select Industry</option> --}}
                                    @foreach ($industruesFs as $industruesF)
                                        <option value="{{ $industruesF->industry_id }}" @if(isset($contactsaveadminfilter) && in_array($industruesF->industry_id,explode(",",$contactsaveadminfilter->industry_id))) selected @endif>{{ $industruesF->industname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-country" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" data-placeholder="Select Country">
                                    {{-- <select id="by-country" class="form-select select22f" multiple data-placeholder="Select Country"> --}}
                                    {{-- <option value="">Select Country</option> --}}
                                    @foreach ($countryfs as $country)
                                        <option value="{{ $country->country_id }}" @if(isset($contactsaveadminfilter) && in_array($country->country_id,explode(",",$contactsaveadminfilter->country_id))) selected @endif>{{ $country->cname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-city" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select City">
                                    {{-- <select id="by-city" class="form-select select22f" multiple data-placeholder="Select City"> --}}
                                    {{-- <option value="">Select City</option> --}}
                                    @foreach ($cityfs as $cityf)
                                        <option value="{{ $cityf->city_id }}" @if(isset($contactsaveadminfilter) && in_array($cityf->city_id,explode(",",$contactsaveadminfilter->city_id))) selected @endif>{{ $cityf->citname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-group" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Group Name">
                                    {{-- <select id="by-group" class="form-select select22f" multiple data-placeholder="Select Group Name"> --}}
                                    {{-- <option value="">Select Group</option> --}}
                                    <option value="gb0" @if(isset($contactsaveadminfilter) && in_array("gb0",explode(",",$contactsaveadminfilter->group_id))) selected @endif>Null</option>
                                    @foreach ($groupfs as $groupf)
                                        <option value="{{ $groupf->group_id }}" @if(isset($contactsaveadminfilter) && in_array($groupf->group_id,explode(",",$contactsaveadminfilter->group_id))) selected @endif>{{ $groupf->grpname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-lifecycle-status" class="selectpicker w-100" multiple data-actions-box="true" data-live-search="true" data-style="default-btn" title="Select Life Cycle Status">
                                    <option value="">Select Life Cycle Status</option>
                                    @foreach ($lifcsts as $lifcst)
                                        <option value="{{ $lifcst->lcs_id }}" @if(isset($contactsaveadminfilter) && in_array($lifcst->lcs_id,explode(",",$contactsaveadminfilter->lcs_id))) selected @endif>{{ $lifcst->lfsname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-lead-stage" class="selectpicker w-100" multiple data-actions-box="true" data-live-search="true" data-style="default-btn" title="Select Lead Stage">
                                    <option value="">Select Lead Stage</option>
                                    @foreach ($leadstg as $leadstg)
                                        <option value="{{ $leadstg->ls_id }}" @if(isset($contactsaveadminfilter) && in_array($leadstg->ls_id,explode(",",$contactsaveadminfilter->ls_id))) selected @endif>{{ $leadstg->leadstage }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-4 mb-3">
                                <select name="" id="by-send-tag" class="selectpicker w-100" data-live-search="true" data-actions-box="true" data-style="default-btn" multiple title="Select Whatsapp Send Tag...">
                                    <option value="">Select Whatsapp Send Tag</option>
                                    @foreach ($sendTags as $sendTag)
                                        <option value="{{ $sendTag->send_tag }}" @if(isset($contactsaveadminfilter) && in_array($sendTag->send_tag,explode(",",$contactsaveadminfilter->sned_tag))) selected @endif>{{ $sendTag->send_tag }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <input name="send_date_filter" type="text" id="by-send-date" @if(isset($contactsaveadminfilter)) value="{{ $contactsaveadminfilter->send_date }}" @endif class="form-control singledatepicker" placeholder="Send Date...">
                            </div>

                            <div class="col-md-4 mb-3">
                                <input type="text" name="created_date_filter" id="by-created-date" @if(isset($contactsaveadminfilter)) value="{{ $contactsaveadminfilter->created_date }}" @endif class="form-control singledatepicker" placeholder="Created date...">
                            </div>

                            <div class="col-md-4 mb-3">
                                <input type="text" name="update_date_filter" id="by-update-date" @if(isset($contactsaveadminfilter)) value="{{ $contactsaveadminfilter->updated_date }}" @endif class="form-control singledatepicker" placeholder="Updated date...">
                            </div>

                            <div class="col-md-4 mb-3">
                                <input name="by_update_status_date" type="text" id="by-update-status-date" @if(isset($contactsaveadminfilter)) value="{{ $contactsaveadminfilter->update_status_date }}" @endif class="form-control bsdatpicket" placeholder="Update Status Date...">
                            </div>
                            <div class="col-md-4 mb-3">
                                <select name="" id="by-createdby" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select created by...">
                                    {{-- <select name="" id="by-createdby" class="form-select select22f" multiple data-placeholder="Select created by..."> --}}
                                    {{-- <option value="">Select Created By</option> --}}
                                    @foreach ($createdBys as $createdBy)
                                        <option value="{{ $createdBy->staff_id }}" @if(isset($contactsaveadminfilter) && in_array($createdBy->staff_id,explode(",",$contactsaveadminfilter->created_by))) selected @endif>{{ $createdBy->adminname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select name="subscribe" id="by-subscribe" class="form-select select22f" multiple data-placeholder="Select Subscribe">
                                    <option value="">Select Subscribe</option>
                                    <option value="1" @if(isset($contactsaveadminfilter) && in_array("1",explode(",",$contactsaveadminfilter->subscribe))) selected @endif>Subscribe</option>
                                    <option value="0" @if(isset($contactsaveadminfilter) && in_array("0",explode(",",$contactsaveadminfilter->subscribe))) selected @endif>Unsubscribe</option>
                                </select>
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

        <!-- Bulk Export Start -->
        <div class="modal fade" id="bulkexport" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel125">Export Contact Plus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.contactPlus.bulkexport') }}" method="POST" id="bulkassigntoValidation">
                    @csrf
                    <div class="modal-body">
                                <input type="hidden" name="bulkcontactp_id" id="bulkcontactp_id">
                                
                                <div id="Contactpcolumns" class="row g-2">
                                </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="bulk-export" class="btn btn-sm btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
        <!-- Bulk Export End -->

        <!-- Bulk Delete Start -->
        <div class="modal fade" id="bulkdelete" aria-hidden="true" aria-labelledby="bulkdeleteLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Delete Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.contact.bulk.delete') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="contactids" id="delcontactIDS">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="mb-3">
                                      <p class="text-danger">Are you sure to delete contact?</p>
                                   </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Delete</button>
                         </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Bulk Delete End -->

        <!--- Start Bulk Import Contact Start -->
        <div class="modal fade" id="bulkimport" aria-hidden="true" aria-labelledby="bulkimportLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Import Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form id="importValidationAllcontact" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="modal-body">
                            <div class="row">
                                
                                <!-- CSV Upload -->
                                <div class="col-md-10 mb-3">
                                    <label for="add-allcontact-csv-data" class="form-label">Upload CSV File</label>
                                    <input type="file" name="allcontact_csv_data" id="add-allcontact-csv-data" class="form-control" accept=".csv">
                                </div>
                                <div class="col-md-2 mt-4">
                                    <button type="button" class="btn btn-primary btn-sm" id="uploadCSVBtn">Upload CSV</button>                                
                                </div>

                                <div class="col-md-4 6_dropdowns" style="display:none;">
                                    <div class="mb-3">
                                        <label for="bulk_business_type_id" class="form-label">Business Type<span class="text-danger">*</span></label>
                                        <select name="business_type_id" id="bulk_business_type_id" class="form-select select22" data-placeholder="Select Business Type" data-allow-clear="true">
                                            <option value="">Select</option>
                                            @foreach ($businesstypes as $businesstype)
                                                <option value="{{ $businesstype->id }}" @if($businesstype->name == 'B2C') selected @endif>{{ $businesstype->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-display-business-type" style="display:none;"></span>
                                    </div>
                                </div>

                                <div class="col-md-4 6_dropdowns" style="display:none;">
                                    <div class="mb-3">
                                        <label for="bulk_country_id" class="form-label">Country<span class="text-danger">*</span></label>
                                        <select name="country_id" id="bulk_country_id" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                            <option value="">Select</option>
                                            @foreach ($countryfs as $country)
                                                <option value="{{ $country->country_id }}">{{ $country->cname }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-display-country" style="display:none;"></span>
                                    </div>
                                </div>

                                

                                <div class="col-md-4 6_dropdowns" style="display:none;">
                                    <div class="mb-3">
                                        <label for="bulk_careoff_id" class="form-label">Careoff</label>
                                        <select name="careoff_id" id="bulk_careoff_id" class="form-select select22" data-placeholder="Select Careoff" data-allow-clear="true">
                                            <option value="not_required">Not Required</option>
                                            @foreach ($adminusers as $adminuser)
                                                <option value="{{ $adminuser->id }}" @if($adminuser->id == Auth::guard('admin')->user()->id) selected @endif>{{ $adminuser->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 6_dropdowns" style="display:none;">
                                    <div class="mb-3">
                                        <label for="bulk_source" class="form-label">Source</label>
                                            <select name="source" id="bulk_source" class="form-select select22" data-allow-clear="true" data-placeholder="Select Source">
                                                <option value="not_required">Not Required</option>
                                                <option value="Direct">Direct</option>
                                                <option value="Facebook">Facebook</option>
                                                <option value="Instagram">Instagram</option>
                                                <option value="Google">Google</option>
                                                <option value="LinkedIn">LinkedIn</option>
                                                <option value="Quora">Quora</option>
                                                <option value="Twitter">Twitter</option>
                                                <option value="Bing">Bing</option>
                                                <option value="Reddit">Reddit</option>
                                            </select>      
                                    </div>
                                </div>

                               
                                <div class="col-md-4 6_dropdowns" style="display:none;">
                                    <div class="mb-3">
                                        <label for="bulk_group_id" class="form-label">Group</label>
                                        <select name="group_id" id="bulk_group_id" class="form-select select22" data-allow-clear="true" data-placeholder="Select Group...">
                                            <option value="not_required">Not Required</option>
                                            @foreach ($groupms as $groupm)
                                                <option value="{{ $groupm->id }}">{{ $groupm->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- Error Display -->
                                <div class="col-md-12">
                                    <span class="text-danger error-display" style="display:none;"></span>
                                </div>
                                

                                <!-- Preview Table -->
                                <div class="col-md-12 mt-3">
                                    <p class="error-message text-danger" style="display:none;"></p>
                                    <table id="previewTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 50%;">CSV Header</th>
                                                <th style="width: 50%;">DB List Field</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamically filled via JS -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Progress Bar -->
                                <div class="col-md-12 mt-2">
                                    <div class="progress" style="height: 20px; display:none;" id="importProgressBarContainer">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                            role="progressbar" style="width: 0%;" id="importProgressBar">
                                            0%
                                        </div>
                                    </div>
                                </div>


                                <!-- Duplicate / Result Display -->
                                <div class="col-md-12 mt-2">
                                    <div class="duplicate-display" style="display:none;"></div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                Close
                            </button>
                           
                            <button type="button" class="btn btn-primary btn-sm" id="importCSVData" style="display:none;">
                                Import Data
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!-- Start Bulk Import Contact End -->