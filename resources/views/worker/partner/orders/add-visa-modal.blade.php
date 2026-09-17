{{-- Shared Add Visa Details modal - one instance per page (Order Details
and the Orders Card view both @include this), populated per-order via
order-visa.js from whichever trigger button was clicked (same shared-modal
+ data-* attribute convention already used for the Employer Edit modal).
Field set/order mirrors the admin's own Booking "Add Visa Details"
offcanvas (BookingController@visaStr,
resources/views/admin/booking/index.blade.php's #addvisadetail) and the
legacy partner portal's already-partner-scoped equivalent
(PartnerBookingController@visaStr) - both update-or-create ONE Visadetails
row per booking, so this modal doubles as "Edit" once a booking already
has visa details (pre-filled by order-visa.js, matching admin's own
show.bs.offcanvas AJAX populate). Salary is a real, editable, submitted
field here (defaulted from the candidate's own expected salary as a
convenience) rather than the admin form's `disabled` - and therefore never
actually submitted - input. --}}
<div class="w-modal-backdrop" data-order-visa-backdrop></div>
<div class="w-modal" data-order-visa-modal role="dialog" aria-modal="true" aria-labelledby="orderVisaTitle">
    <div class="w-modal-dialog" style="max-width:680px;">
        <button type="button" class="w-modal-close" data-order-visa-close aria-label="Close">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        <div class="w-modal-body">
            <h3 id="orderVisaTitle" class="w-modal-title">{{ __('locale.Add Visa Details') }}</h3>

            <div class="w-form-alert" data-order-visa-alert hidden></div>

            <form id="orderVisaForm" method="POST" action="">
                @csrf
                <div class="w-form-grid">
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Employer Name') }} <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="employer_name" id="order-visa-employer-name" class="w-input" placeholder="{{ __('locale.Enter Employer Name...') }}" required>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Employer Arabic Name') }}</label>
                        <input type="text" name="employer_ar_name" id="order-visa-employer-ar-name" class="w-input" placeholder="{{ __('locale.Enter Employer Name (Arabic)...') }}">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Visa Number') }} <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="visa_no" id="order-visa-visa-no" class="w-input" placeholder="{{ __('locale.Enter Visa No...') }}" required>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.ID Number') }}</label>
                        <input type="text" name="id_no" id="order-visa-id-no" class="w-input" placeholder="{{ __('locale.Enter ID No...') }}">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Profession') }} <span style="color:#dc2626;">*</span></label>
                        <select name="proff_id" id="order-visa-proff-id" class="w-select select2" data-placeholder="{{ __('locale.Select Profession') }}">
                            <option value=""></option>
                            @foreach ($professionOptions as $profession)
                                <option value="{{ $profession->id }}">{{ $profession->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Issuing Authority') }} <span style="color:#dc2626;">*</span></label>
                        <select name="issuing_authority" id="order-visa-issuing-authority" class="w-select select2" data-placeholder="{{ __('locale.Select Issuing Authority') }}">
                            <option value=""></option>
                            <option value="Mumbai">{{ __('locale.Mumbai') }}</option>
                            <option value="New Delhi">{{ __('locale.New Delhi') }}</option>
                        </select>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.City of Work') }} <span style="color:#dc2626;">*</span></label>
                        <select name="wpcity_id" id="order-visa-wpcity-id" class="w-select select2" data-placeholder="{{ __('locale.Select City of Work') }}">
                            <option value=""></option>
                            @foreach ($allWorkCities as $city)
                                <option value="{{ $city->id }}">{{ $city->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Monthly Salary') }}</label>
                        <input type="text" name="salary" id="order-visa-salary" class="w-input" placeholder="{{ __('locale.Enter salary...') }}">
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:18px;">
                    <button type="button" class="w-btn w-btn-outline w-btn-block" data-order-visa-close>
                        {{ __('locale.Close') }}
                    </button>
                    <button type="submit" class="w-btn w-btn-primary w-btn-block" data-order-visa-submit
                        data-default-text="{{ __('locale.Save Changes') }}"
                        data-loading-text="{{ __('locale.Saving…') }}">
                        {{ __('locale.Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
