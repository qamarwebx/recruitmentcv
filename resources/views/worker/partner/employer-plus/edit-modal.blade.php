<div class="w-modal-backdrop" data-edit-employer-backdrop></div>
<div class="w-modal" data-edit-employer-modal role="dialog" aria-modal="true" aria-labelledby="editEmployerTitle">
    <div class="w-modal-dialog" style="max-width:800px;">
        <button type="button" class="w-modal-close" data-edit-employer-close aria-label="Close">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        <div class="w-modal-body">
            <h3 id="editEmployerTitle" class="w-modal-title">{{ __('locale.Edit Employer') }}</h3>

            <div class="w-form-alert" data-edit-employer-alert hidden></div>

            {{-- Same fields/structure as the Add Employer modal (see
            add-modal.blade.php and the admin's EmployerController@
            empVisaUpdateP, whose Edit offcanvas mirrors its own Add
            offcanvas field-for-field) plus one Edit-only field, Status -
            new Employers don't need a status toggle at creation time, but
            an existing one does, matching this portal's own established
            convention rather than the admin's separate empStatusUpdatep
            quick-action. One shared modal/form, reused for every row,
            populated from the clicked row's own data-* attributes and the
            employer's existing proff_id/openings pairs (see
            employer-edit.js + employer-widgets.js's rebuildVisaRows). --}}
            <form id="employerEditForm" method="POST" action="">
                @csrf
                <div class="w-form-grid">
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Employer Name') }}</label>
                        <input type="text" name="employer_name" id="edit-employer-name" class="w-input" placeholder="{{ __('locale.Enter Employer Name...') }}" required>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Employer Arabic Name') }}</label>
                        <input type="text" name="employer_ar_name" id="edit-employer-ar-name" class="w-input" placeholder="{{ __('locale.Enter Employer Name (Arabic)...') }}">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Visa No') }}</label>
                        <input type="text" name="visa_no" id="edit-visa-no" class="w-input" placeholder="{{ __('locale.Enter Visa No...') }}">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.ID No') }}</label>
                        <input type="text" name="id_no" id="edit-id-no" class="w-input" placeholder="{{ __('locale.Enter ID No...') }}">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Visa Date') }}</label>
                        <input type="date" name="visa_date" id="edit-visa-date" class="w-input" autocomplete="off" placeholder="{{ __('locale.Enter Visa Date...') }}">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Issuing Authority') }}</label>
                        <select name="issuing_authority" id="edit-issuing-authority" class="w-select w-select2" data-placeholder="{{ __('locale.Select Issuing Authority') }}">
                            <option value=""></option>
                            <option value="Mumbai">{{ __('locale.Mumbai') }}</option>
                            <option value="New Delhi">{{ __('locale.New Delhi') }}</option>
                        </select>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.City of Work') }}</label>
                        <select name="wpcity_id" id="edit-wpcity-id" class="w-select w-select2" data-placeholder="{{ __('locale.Select City of Work') }}" data-other-city-select>
                            <option value=""></option>
                            @foreach ($workCities as $workCity)
                                <option value="{{ $workCity->id }}">{{ $workCity->display_name }}</option>
                            @endforeach
                            <option value="other">{{ __('locale.Other') }}</option>
                        </select>
                    </div>
                    <div class="w-form-row" data-other-city-wrap hidden>
                        <label class="w-form-label">{{ __('locale.City Name') }} <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="custom_city_name" id="edit-custom-city-name" class="w-input" data-other-city-input
                            placeholder="{{ __('locale.Enter city name...') }}">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Status') }}</label>
                        <select name="status" id="edit-status" class="w-select w-select2">
                            <option value="1">{{ __('locale.Active') }}</option>
                            <option value="0">{{ __('locale.Inactive') }}</option>
                        </select>
                    </div>

                    <div class="w-form-row w-form-row-full">
                        <label class="w-form-label">{{ __('locale.Profession') }} / {{ __('locale.Openings') }} / {{ __('locale.Monthly Salary') }}</label>
                        @include('worker.partner.employer-plus._visa-rows', ['professionOptions' => $professionOptions])
                    </div>

                    <div class="w-form-row w-form-row-full">
                        <label class="w-form-label">{{ __('locale.Notes') }}</label>
                        <textarea name="notes" id="edit-notes" class="w-input" rows="3" placeholder="{{ __('locale.Enter Notes...') }}"></textarea>
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:18px;">
                    <button type="button" class="w-btn w-btn-outline w-btn-block" data-edit-employer-close>
                        {{ __('locale.Close') }}
                    </button>
                    <button type="submit" class="w-btn w-btn-primary w-btn-block" data-edit-employer-submit
                        data-default-text="{{ __('locale.Save Changes') }}"
                        data-loading-text="{{ __('locale.Saving…') }}">
                        {{ __('locale.Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
