@extends('layout.admin.admin_layout')

@section('title','Permission')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.permission.store') }}" method="POST" id="permissionV">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 offset-md-9">
                            <div class="mb-3">
                                <label for="" class="form-label">Staff <span class="text-danger">*</span></label>
                                <select name="staff_id" id="staff_id" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-check-label" for="full-access"> Full Access</label>
                                <input class="form-check-input" type="checkbox" name="full_access" value="1" id="full-access" />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="6">
                                                <label class="form-check-label" for="bookings">Bookings</label>
                                                <input class="form-check-input" type="checkbox" name="bookings" value="1" id="bookings">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="confirm-booking">Confirm Booking</label>
                                                <input class="form-check-input groups1" type="checkbox" name="booking_confirm" id="confirm-booking" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-visa-details">Add Visa Details</label>
                                                <input class="form-check-input groups1" type="checkbox" name="add_visa_details" id="add-visa-details" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-booking">View</label>
                                                <input class="form-check-input groups1" type="checkbox" name="view_booking" id="view-booking" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-payment">Add Payment</label>
                                                <input class="form-check-input groups1" type="checkbox" name="add_payment" id="add-payment" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="cancel-booking">Cancel Booking</label>
                                                <input class="form-check-input groups1" type="checkbox" name="cancel_booking" id="cancel-booking" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="replace-booking">Replace Candidate</label>
                                                <input class="form-check-input groups1" type="checkbox" name="replace_candidate" id="replace-booking" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="2">
                                                <label class="form-check-label" for="employer">Employer</label>
                                                <input class="form-check-input" type="checkbox" name="employer" value="1" id="employer">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="view-employer">View Employer</label>
                                                <input class="form-check-input groups2" type="checkbox" name="view_employer" id="view-employer" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-employer">Delete Employer</label>
                                                <input class="form-check-input groups2" type="checkbox" name="delete_employer" id="delete-employer" value="1" disabled>
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="5">
                                                <label class="form-check-label" for="candidate">Candidate</label>
                                                <input class="form-check-input" type="checkbox" name="candidate" value="1" id="candidate">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="add-candidate">Add Candidate</label>
                                                <input class="form-check-input groups3" type="checkbox" name="add_candidate" id="add-candidate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-candidate">Edit Candidate</label>
                                                <input class="form-check-input groups3" type="checkbox" name="edit_candidate" id="edit-candidate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-candidate">View Candidate</label>
                                                <input class="form-check-input groups3" type="checkbox" name="view_candidate" id="view-candidate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-candidate">Delete Candidate</label>
                                                <input class="form-check-input groups3" type="checkbox" name="delete_candidate" id="delete-candidate" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="publish-candidate">Publish Stage</label>
                                                <input class="form-check-input groups3" type="checkbox" name="publish_candidate" id="publish-candidate" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="2">
                                                <label class="form-check-label" for="client">CLient</label>
                                                <input class="form-check-input" type="checkbox" name="client" value="1" id="client">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="view-client">View Client</label>
                                                <input class="form-check-input groups4" type="checkbox" name="view_client" id="view-client" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-client">Delete Client</label>
                                                <input class="form-check-input groups4" type="checkbox" name="delete_client" id="delete-client" value="1" disabled>
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="4">
                                                <label class="form-check-label" for="partner">Recruitment Partner</label>
                                                <input class="form-check-input" type="checkbox" name="partner" value="1" id="partner">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="add-partner">Add Partner</label>
                                                <input class="form-check-input groups5" type="checkbox" name="add_partner" id="add-partner" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-partner">Edit Partner</label>
                                                <input class="form-check-input groups5" type="checkbox" name="edit_partner" id="edit-partner" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-partner">View Partner</label>
                                                <input class="form-check-input groups5" type="checkbox" name="view_partner" id="view-partner" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-partner">Delete Partner</label>
                                                <input class="form-check-input groups5" type="checkbox" name="delete_partner" id="delete-partner" value="1" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="5">
                                                <label class="form-check-label" for="settings">Settings</label>
                                                <input class="form-check-input" type="checkbox" name="settings" value="1" id="settings">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="staff">Staff</label>
                                                <input class="form-check-input groups6" type="checkbox" name="staff" id="staff" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-staff">Add Staff</label>
                                                <input class="form-check-input groups6" type="checkbox" name="add_staff" id="add-staff" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-staff">Edit Staff</label>
                                                <input class="form-check-input groups6" type="checkbox" name="edit_staff" id="edit-staff" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-staff">View Staff</label>
                                                <input class="form-check-input groups6" type="checkbox" name="view_staff" id="view-staff" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-staff">Delete Staff</label>
                                                <input class="form-check-input groups6" type="checkbox" name="delete_staff" id="delete-staff" value="1" disabled> 
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="profession">Profession</label>
                                                <input class="form-check-input groups6" type="checkbox" name="profession" id="profession" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-profession">Add Profession</label>
                                                <input class="form-check-input groups6" type="checkbox" name="add_profession" id="add-profession" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-profession">Edit Profession</label>
                                                <input class="form-check-input groups6" type="checkbox" name="edit_profession" id="edit-profession" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-profession">View Profession</label>
                                                <input class="form-check-input groups6" type="checkbox" name="view_profession" id="view-profession" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-profession">Delete Profession</label>
                                                <input class="form-check-input groups6" type="checkbox" name="delete_profession" id="delete-profession" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="placeofissue">Place of Issue</label>
                                                <input class="form-check-input groups6" type="checkbox" name="placeofissue" id="placeofissue" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-placeofissue">Add Place of Issue</label>
                                                <input class="form-check-input groups6" type="checkbox" name="add_placeofissue" id="add-placeofissue" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-placeofissue">Edit Place of Issue</label>
                                                <input class="form-check-input groups6" type="checkbox" name="edit_placeofissue" id="edit-placeofissue" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-placeofissue">View Place of Issue</label>
                                                <input class="form-check-input groups6" type="checkbox" name="view_placeofissue" id="view-placeofissue" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-placeofissue">Delete Place of Issue</label>
                                                <input class="form-check-input groups6" type="checkbox" name="delete_placeofissue" id="delete-placeofissue" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="country">Country</label>
                                                <input class="form-check-input groups6" type="checkbox" name="country" id="country" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-country">Add Country</label>
                                                <input class="form-check-input groups6" type="checkbox" name="add_country" id="add-country" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-country">Edit Country</label>
                                                <input class="form-check-input groups6" type="checkbox" name="edit_country" id="edit-country" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-country">View Country</label>
                                                <input class="form-check-input groups6" type="checkbox" name="view_country" id="view-country" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-country">Delete Country</label>
                                                <input class="form-check-input groups6" type="checkbox" name="delete_country" id="delete-country" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="region">Region</label>
                                                <input class="form-check-input groups6" type="checkbox" name="region" id="region" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-region">Add Region</label>
                                                <input class="form-check-input groups6" type="checkbox" name="add_region" id="add-region" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-region">Edit Region</label>
                                                <input class="form-check-input groups6" type="checkbox" name="edit_region" id="edit-region" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-region">View Region</label>
                                                <input class="form-check-input groups6" type="checkbox" name="view_region" id="view-region" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-region">Delete Region</label>
                                                <input class="form-check-input groups6" type="checkbox" name="delete_region" id="delete-region" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="city">City</label>
                                                <input class="form-check-input groups6" type="checkbox" name="city" id="city" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-city">Add City</label>
                                                <input class="form-check-input groups6" type="checkbox" name="add_city" id="add-city" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-city">Edit City</label>
                                                <input class="form-check-input groups6" type="checkbox" name="edit_city" id="edit-city" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-city">View City</label>
                                                <input class="form-check-input groups6" type="checkbox" name="view_city" id="view-city" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-city">Delete City</label>
                                                <input class="form-check-input groups6" type="checkbox" name="delete_city" id="delete-city" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="5">
                                                <label class="form-check-label" for="websiteconfig">Website Configuration</label>
                                                <input class="form-check-input groups6" type="checkbox" name="websiteconfig" id="websiteconfig" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="mailsetup">Mail Setup</label>
                                                <input class="form-check-input groups6" type="checkbox" name="mailsetup" id="mailsetup" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-mailsetup">Add Mail Setup</label>
                                                <input class="form-check-input groups6" type="checkbox" name="add_mailsetup" id="add-mailsetup" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-mailsetup">Edit Mail Setup</label>
                                                <input class="form-check-input groups6" type="checkbox" name="edit_mailsetup" id="edit-mailsetup" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-mailsetup">View Mail Setup</label>
                                                <input class="form-check-input groups6" type="checkbox" name="view_mailsetup" id="view-mailsetup" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-mailsetup">Delete Mail Setup</label>
                                                <input class="form-check-input groups6" type="checkbox" name="delete_mailsetup" id="delete-mailsetup" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="carknown">Car Known</label>
                                                <input class="form-check-input groups6" type="checkbox" name="carknown" id="carknown" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-carknown">Add Car Known</label>
                                                <input class="form-check-input groups6" type="checkbox" name="add_car_known" id="add-carknown" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-carknown">Edit Car Known</label>
                                                <input class="form-check-input groups6" type="checkbox" name="edit_car_known" id="edit-carknown" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-carknown">View Car Known</label>
                                                <input class="form-check-input groups6" type="checkbox" name="view_car_known" id="view-carknown" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-carknown">Delete Car Known</label>
                                                <input class="form-check-input groups6" type="checkbox" name="delete_car_known" id="delete-carknown" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="personalise-class">Personalise Class</label>
                                                <input class="form-check-input groups6" type="checkbox" name="personalise_class" id="personalise-class" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-personalise-class">Add Personalise Class</label>
                                                <input class="form-check-input groups6" type="checkbox" name="add_personalise_class" id="add-personalise-class" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-personalise-class">Edit Personalise Class</label>
                                                <input class="form-check-input groups6" type="checkbox" name="edit_personalise_class" id="edit-personalise-class" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-personalise-class">View Personalise Class</label>
                                                <input class="form-check-input groups6" type="checkbox" name="view_personalise_class" id="view-personalise-class" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-personalise-class">Delete Personalise Class</label>
                                                <input class="form-check-input groups6" type="checkbox" name="delete_personalise_class" id="delete-personalise-class" value="1" disabled>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <label class="form-check-label" for="template">Template</label>
                                                <input class="form-check-input groups6" type="checkbox" name="template" id="template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="add-template">Add Template</label>
                                                <input class="form-check-input groups6" type="checkbox" name="add_template" id="add-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="edit-template">Edit Template</label>
                                                <input class="form-check-input groups6" type="checkbox" name="edit_template" id="edit-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="view-template">View Template</label>
                                                <input class="form-check-input groups6" type="checkbox" name="view_template" id="view-template" value="1" disabled>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="delete-template">Delete Template</label>
                                                <input class="form-check-input groups6" type="checkbox" name="delete_template" id="delete-template" value="1" disabled>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm float-end data-submit">Submit</button>
                </form>
            </div>
        </div>
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
    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/permission-validation.js') }}"></script>



    <script>
        $(document).ready(function(){
            $('#staff_id').on('change',function(){
                var staff_id = $(this).val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url: "{{ url('admin/permission/get') }}",
                    method: 'post',
                    type: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        staff_id: staff_id

                    },
                    success: function(data){
                        if (data.full_access == 1) {
                            $('#full-access').prop('checked',true);
                        }else{
                            $('#full-access').prop('checked',false);
                        }
                        
                        if (data.bookings == 1) {
                            $('#bookings').prop('checked',true).change();
                        } else {
                            $('#bookings').prop('checked',false).change();
                        }

                        if (data.employer == 1) {
                            $('#employer').prop('checked',true).change();
                        } else {
                            $('#employer').prop('checked',false).change();
                        }

                        if (data.candidate == 1) {
                            $('#candidate').prop('checked',true).change();
                        } else {
                            $('#candidate').prop('checked',false).change();
                        }

                        if (data.client == 1) {
                            $('#client').prop('checked',true).change();
                        } else {
                            $('#client').prop('checked',false).change();
                        }

                        if (data.partner == 1) {
                            $('#partner').prop('checked',true).change();
                        } else {
                            $('#partner').prop('checked',false).change();
                        }

                        if (data.settings == 1) {
                            $('#settings').prop('checked',true).change();
                        } else {
                            $('#settings').prop('checked',false).change();
                        }

                        
                        if (data.booking_confirm == 1) {
                            $('#confirm-booking').prop('checked',true);
                        }else{
                            $('#confirm-booking').prop('checked',false);
                        }

                        if (data.add_visa_details == 1) {
                            $('#add-visa-details').prop('checked',true);
                        }else{
                            $('#add-visa-details').prop('checked',false);
                        }

                        if (data.view_booking == 1) {
                            $('#view-booking').prop('checked',true);
                        }else{
                            $('#view-booking').prop('checked',false);
                        }

                        if (data.add_payment == 1) {
                            $('#add-payment').prop('checked',true);
                        }else{
                            $('#add-payment').prop('checked',false);
                        }

                        if (data.cancel_booking == 1) {
                            $('#cancel-booking').prop('checked',true);
                        }else{
                            $('#cancel-booking').prop('checked',false);
                        }

                        if (data.replace_candidate == 1) {
                            $('#replace-booking').prop('checked',true);
                        }else{
                            $('#replace-booking').prop('checked',false);
                        }

                        if (data.view_employer == 1) {
                            $('#view-employer').prop('checked',true);
                        }else{
                            $('#view-employer').prop('checked',false);
                        }

                        if (data.delete_employer == 1) {
                            $('#delete-employer').prop('checked',true);
                        }else{
                            $('#delete-employer').prop('checked',false);
                        }

                        if (data.add_candidate == 1) {
                            $('#add-candidate').prop('checked',true);
                        }else{
                            $('#add-candidate').prop('checked',false);
                        }

                        if (data.edit_candidate == 1) {
                            $('#edit-candidate').prop('checked',true);
                        }else{
                            $('#edit-candidate').prop('checked',false);
                        }

                        if (data.view_candidate == 1) {
                            $('#view-candidate').prop('checked',true);
                        }else{
                            $('#view-candidate').prop('checked',false);
                        }

                        if (data.delete_candidate == 1) {
                            $('#delete-candidate').prop('checked',true);
                        }else{
                            $('#delete-candidate').prop('checked',false);
                        }

                        if (data.publish_candidate == 1) {
                            $('#publish-candidate').prop('checked',true);
                        }else{
                            $('#publish-candidate').prop('checked',false);
                        }

                        if (data.view_client == 1) {
                            $('#view-client').prop('checked',true);
                        }else{
                            $('#view-client').prop('checked',false);
                        }

                        if (data.delete_client == 1) {
                            $('#delete-client').prop('checked',true);
                        }else{
                            $('#delete-client').prop('checked',false);
                        }

                        if (data.add_partner == 1) {
                            $('#add-partner').prop('checked',true);
                        }else{
                            $('#add-partner').prop('checked',false);
                        }

                        if (data.edit_partner == 1) {
                            $('#edit-partner').prop('checked',true);
                        }else{
                            $('#edit-partner').prop('checked',false);
                        }

                        if (data.view_partner == 1) {
                            $('#view-partner').prop('checked',true);
                        }else{
                            $('#view-partner').prop('checked',false);
                        }

                        if (data.delete_partner == 1) {
                            $('#delete-partner').prop('checked',true);
                        }else{
                            $('#delete-partner').prop('checked',false);
                        }

                        if (data.staff == 1) {
                            $('#staff').prop('checked',true);
                        }else{
                            $('#staff').prop('checked',false);
                        }

                        if (data.add_staff == 1) {
                            $('#add-staff').prop('checked',true);
                        }else{
                            $('#add-staff').prop('checked',false);
                        }

                        if (data.edit_staff == 1) {
                            $('#edit-staff').prop('checked',true);
                        }else{
                            $('#edit-staff').prop('checked',false);
                        }

                        if (data.delete_staff == 1) {
                            $('#delete-staff').prop('checked',true);
                        }else{
                            $('#delete-staff').prop('checked',false);
                        }

                        if (data.view_staff == 1) {
                            $('#view-staff').prop('checked',true);
                        }else{
                            $('#view-staff').prop('checked',false);
                        }

                        if (data.profession == 1) {
                            $('#profession').prop('checked',true);
                        }else{
                            $('#profession').prop('checked',false);
                        }

                        if (data.add_profession == 1) {
                            $('#add-profession').prop('checked',true);
                        }else{
                            $('#add-profession').prop('checked',false);
                        }

                        if (data.edit_profession == 1) {
                            $('#edit-profession').prop('checked',true);
                        }else{
                            $('#edit-profession').prop('checked',false);
                        }

                        if (data.delete_profession == 1) {
                            $('#delete-profession').prop('checked',true);
                        }else{
                            $('#delete-profession').prop('checked',false);
                        }

                        if (data.view_profession == 1) {
                            $('#view-profession').prop('checked',true);
                        }else{
                            $('#view-profession').prop('checked',false);
                        }

                        if (data.placeofissue == 1) {
                            $('#placeofissue').prop('checked',true);
                        }else{
                            $('#placeofissue').prop('checked',false);
                        }

                        if (data.add_placeofissue == 1) {
                            $('#add-placeofissue').prop('checked',true);
                        }else{
                            $('#add-placeofissue').prop('checked',false);
                        }

                        if (data.edit_placeofissue == 1) {
                            $('#edit-placeofissue').prop('checked',true);
                        }else{
                            $('#edit-placeofissue').prop('checked',false);
                        }

                        if (data.delete_placeofissue == 1) {
                            $('#delete-placeofissue').prop('checked',true);
                        }else{
                            $('#delete-placeofissue').prop('checked',false);
                        }

                        if (data.view_placeofissue == 1) {
                            $('#view-placeofissue').prop('checked',true);
                        }else{
                            $('#view-placeofissue').prop('checked',false);
                        }

                        if (data.country == 1) {
                            $('#country').prop('checked',true);
                        }else{
                            $('#country').prop('checked',false);
                        }

                        if (data.add_country == 1) {
                            $('#add-country').prop('checked',true);
                        }else{
                            $('#add-country').prop('checked',false);
                        }

                        if (data.edit_country == 1) {
                            $('#edit-country').prop('checked',true);
                        }else{
                            $('#edit-country').prop('checked',false);
                        }

                        if (data.view_country == 1) {
                            $('#view-country').prop('checked',true);
                        }else{
                            $('#view-country').prop('checked',false);
                        }

                        if (data.delete_country == 1) {
                            $('#delete-country').prop('checked',true);
                        }else{
                            $('#delete-country').prop('checked',false);
                        }

                        if (data.region == 1) {
                            $('#region').prop('checked',true);
                        }else{
                            $('#region').prop('checked',false);
                        }

                        if (data.add_region == 1) {
                            $('#add-region').prop('checked',true);
                        }else{
                            $('#add-region').prop('checked',false);
                        }

                        if (data.edit_region == 1) {
                            $('#edit-region').prop('checked',true);
                        }else{
                            $('#edit-region').prop('checked',false);
                        }

                        if (data.view_region == 1) {
                            $('#view-region').prop('checked',true);
                        }else{
                            $('#view-region').prop('checked',false);
                        }

                        if (data.delete_region == 1) {
                            $('#delete-region').prop('checked',true);
                        }else{
                            $('#delete-region').prop('checked',false);
                        }

                        if (data.city == 1) {
                            $('#city').prop('checked',true);
                        }else{
                            $('#city').prop('checked',false);
                        }

                        if (data.add_city == 1) {
                            $('#add-city').prop('checked',true);
                        }else{
                            $('#add-city').prop('checked',false);
                        }

                        if (data.edit_city == 1) {
                            $('#edit-city').prop('checked',true);
                        }else{
                            $('#edit-city').prop('checked',false);
                        }

                        if (data.view_city == 1) {
                            $('#view-city').prop('checked',true);
                        }else{
                            $('#view-city').prop('checked',false);
                        }

                        if (data.delete_city == 1) {
                            $('#delete-city').prop('checked',true);
                        }else{
                            $('#delete-city').prop('checked',false);
                        }

                        if (data.websiteconfig == 1) {
                            $('#websiteconfig').prop('checked',true);
                        }else{
                            $('#websiteconfig').prop('checked',false);
                        }

                        if (data.mailsetup == 1) {
                            $('#mailsetup').prop('checked',true);
                        }else{
                            $('#mailsetup').prop('checked',false);
                        }

                        if (data.add_mailsetup == 1) {
                            $('#add-mailsetup').prop('checked',true);
                        }else{
                            $('#add-mailsetup').prop('checked',false);
                        }

                        if (data.edit_mailsetup == 1) {
                            $('#edit-mailsetup').prop('checked',true);
                        }else{
                            $('#edit-mailsetup').prop('checked',false);
                        }

                        if (data.view_mailsetup == 1) {
                            $('#view-mailsetup').prop('checked',true);
                        }else{
                            $('#view-mailsetup').prop('checked',false);
                        }

                        if (data.delete_mailsetup == 1) {
                            $('#delete-mailsetup').prop('checked',true);
                        }else{
                            $('#delete-mailsetup').prop('checked',false);
                        }
                        if (data.carknown == 1) {
                            $('#carknown').prop('checked',true);
                        }else{
                            $('#carknown').prop('checked',false);
                        }
                        if (data.add_car_known == 1) {
                            $('#add-carknown').prop('checked',true);
                        }else{
                            $('#add-carknown').prop('checked',false);
                        }
                        if (data.edit_car_known == 1) {
                            $('#edit-carknown').prop('checked',true);
                        }else{
                            $('#edit-carknown').prop('checked',false);
                        }
                        if (data.view_car_known == 1) {
                            $('#view-carknown').prop('checked',true);
                        }else{
                            $('#view-carknown').prop('checked',false);
                        }
                        if (data.delete_car_known == 1) {
                            $('#delete-carknown').prop('checked',true);
                        }else{
                            $('#delete-carknown').prop('checked',false);
                        }
                        if (data.personalise_class == 1) {
                            $('#personalise-class').prop('checked',true);
                        }else{
                            $('#personalise-class').prop('checked',false);
                        }
                        if (data.add_personalise_class == 1) {
                            $('#add-personalise-class').prop('checked',true);
                        }else{
                            $('#add-personalise-class').prop('checked',false);
                        }
                        if (data.edit_personalise_class == 1) {
                            $('#edit-personalise-class').prop('checked',true);
                        }else{
                            $('#edit-personalise-class').prop('checked',false);
                        }
                        if (data.view_personalise_class == 1) {
                            $('#view-personalise-class').prop('checked',true);
                        }else{
                            $('#view-personalise-class').prop('checked',false);
                        }
                        if (data.delete_personalise_class == 1) {
                            $('#delete-personalise-class').prop('checked',true);
                        }else{
                            $('#delete-personalise-class').prop('checked',false);
                        }
                        if (data.template == 1) {
                            $('#template').prop('checked',true);
                        }else{
                            $('#template').prop('checked',false);
                        }
                        if (data.add_template == 1) {
                            $('#add-template').prop('checked',true);
                        }else{
                            $('#add-template').prop('checked',false);
                        }
                        if (data.edit_template == 1) {
                            $('#edit-template').prop('checked',true);
                        }else{
                            $('#edit-template').prop('checked',false);
                        }
                        if (data.view_template == 1) {
                            $('#view-template').prop('checked',true);
                        }else{
                            $('#view-template').prop('checked',false);
                        }
                        if (data.delete_template == 1) {
                            $('#delete-template').prop('checked',true);
                        }else{
                            $('#delete-template').prop('checked',false);
                        }
                    }
                });
            });
        });
    </script>

    <script>
        // $(document).ready(function(){
        //     $('#full-access').on('change',function(){
        //         if (this.checked) {
        //             $('#bookings').prop("checked",true).change();
        //             $('#employer').prop("checked",true).change();
        //             $('#candidate').prop("checked",true).change();
        //             $('#client').prop("checked",true).change();
        //             $('#partner').prop("checked",true).change();
        //             $('#settings').prop("checked",true).change();
        //         } else {
        //             $('#bookings').prop("checked",false).change();
        //             $('#employer').prop("checked",false).change();
        //             $('#candidate').prop("checked",false).change();
        //             $('#client').prop("checked",false).change();
        //             $('#partner').prop("checked",false).change();
        //             $('#settings').prop("checked",false).change();
        //         }
        //     });
        // });

        $(document).ready(function(){
            $('#bookings').on('change',function(){

                if (this.checked) {
                    $("input.groups1").removeAttr("disabled");
                } else {
                    $("input.groups1").attr("disabled", true);
                    $('input.groups1').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#employer').on('change',function(){

                if (this.checked) {
                    $("input.groups2").removeAttr("disabled");
                } else {
                    $("input.groups2").attr("disabled", true);
                    $('input.groups2').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#candidate').on('change',function(){

                if (this.checked) {
                    $("input.groups3").removeAttr("disabled");
                } else {
                    $("input.groups3").attr("disabled", true);
                    $('input.groups3').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#client').on('change',function(){

                if (this.checked) {
                    $("input.groups4").removeAttr("disabled");
                } else {
                    $("input.groups4").attr("disabled", true);
                    $('input.groups4').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#partner').on('change',function(){

                if (this.checked) {
                    $("input.groups5").removeAttr("disabled");
                } else {
                    $("input.groups5").attr("disabled", true);
                    $('input.groups5').prop("checked",false);
                }
            });
        });

        $(document).ready(function(){
            $('#settings').on('change',function(){

                if (this.checked) {
                    $("input.groups6").removeAttr("disabled");
                } else {
                    $("input.groups6").attr("disabled", true);
                    $('input.groups6').prop("checked",false);
                }
            });
        });


    </script>

@endsection