
<div class="w-modal-backdrop" data-employer-filter-backdrop></div>
<div class="w-modal" data-employer-filter-modal role="dialog" aria-modal="true" aria-labelledby="employerFilterTitle">
    <div class="w-modal-dialog has-fixed-sections" style="max-width:640px;">
        <button type="button" class="w-modal-close" data-employer-filter-close aria-label="Close">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        <div class="w-modal-fixed-top">
            <h3 id="employerFilterTitle" class="w-modal-title">{{ __('locale.Employer Filter') }}</h3>
        </div>

        <div class="w-modal-scroll-area">
            <div class="w-form-alert" data-employer-filter-alert hidden></div>


            <input type="hidden" name="filtered" value="1" form="employerFilterForm">

            <div class="w-form-grid wp-filter-modal-grid" data-employer-filter-fields>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Status') }}</label>
                    <select name="status" form="employerFilterForm" class="w-select w-select2" data-placeholder="{{ __('locale.Select Status') }}">
                        <option value=""></option>
                        <option value="1" @selected($filters['status'] === '1' || $filters['status'] === 1)>{{ __('locale.Active') }}</option>
                        <option value="0" @selected($filters['status'] === '0' || $filters['status'] === 0)>{{ __('locale.Inactive') }}</option>
                    </select>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Created Date') }}</label>
                    <input type="date" name="created_date" form="employerFilterForm" class="w-input" autocomplete="off" value="{{ $filters['created_date'] }}">
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Profession') }}</label>
                    <select name="profession[]" form="employerFilterForm" class="w-select w-select2" multiple data-placeholder="{{ __('locale.Select Profession') }}">
                        @foreach ($professionOptions as $profession)
                            <option value="{{ $profession->id }}" @selected(in_array($profession->id, (array) $filters['profession']))>{{ $profession->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.City of Work') }}</label>
                    <select name="wpcity_id[]" form="employerFilterForm" class="w-select w-select2" multiple data-placeholder="{{ __('locale.Select City of Work') }}">
                        @foreach ($workCities as $workCity)
                            <option value="{{ $workCity->id }}" @selected(in_array($workCity->id, (array) $filters['wpcity_id']))>{{ $workCity->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Business') }}</label>
                    <select name="businesstype[]" form="employerFilterForm" class="w-select w-select2" multiple data-placeholder="{{ __('locale.Select Business Type') }}">
                        @foreach (['B2B Online', 'B2B Offline', 'B2C Online', 'B2C Offline'] as $option)
                            <option value="{{ $option }}" @selected(in_array($option, (array) $filters['businesstype']))>{{ __('locale.' . $option) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Wakala Status') }}</label>
                    <select name="wakala_status[]" form="employerFilterForm" class="w-select w-select2" multiple data-placeholder="{{ __('locale.Select Wakala Status') }}">
                        @foreach (['Wakala Completed', 'Wakala Not Completed'] as $option)
                            <option value="{{ $option }}" @selected(in_array($option, (array) $filters['wakala_status']))>{{ __('locale.' . $option) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Payment Status') }}</label>
                    <select name="payment_status[]" form="employerFilterForm" class="w-select w-select2" multiple data-placeholder="{{ __('locale.Select Payment Status') }}">
                        @foreach (['Paid' => __('locale.Paid'), 'To Collect' => __('locale.To Collect'), 'Unpaid' => __('locale.Unpaid')] as $value => $label)
                            <option value="{{ $value }}" @selected(in_array($value, (array) $filters['payment_status']))>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="w-modal-fixed-bottom wp-filter-modal-actions">
            <button type="button" class="w-btn w-btn-outline" data-employer-filter-reset
                data-reset-url="{{ route('worker.partner.employer.filter.reset') }}">
                {{ __('locale.Reset Filter') }}
            </button>
            <button type="button" class="w-btn w-btn-outline" data-employer-filter-save
                data-save-url="{{ route('worker.partner.employer.filter.save') }}"
                data-default-text="{{ __('locale.Save Filter') }}"
                data-loading-text="{{ __('locale.Saving…') }}">
                {{ __('locale.Save Filter') }}
            </button>
            <button type="submit" form="employerFilterForm" class="w-btn w-btn-primary">{{ __('locale.Apply Filter') }}</button>
        </div>
    </div>
</div>
