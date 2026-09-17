<div class="w-modal-backdrop" data-add-employer-backdrop></div>
<div class="w-modal" data-add-employer-modal role="dialog" aria-modal="true" aria-labelledby="addEmployerTitle"
    @if (session('add_employer_open')) data-reopen-on-load @endif>
    <div class="w-modal-dialog" style="max-width:760px;">
        <button type="button" class="w-modal-close" data-add-employer-close aria-label="Close">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        <div class="w-modal-body">
            <h3 id="addEmployerTitle" class="w-modal-title">{{ __('locale.Add Employer') }}</h3>

            {{-- Same fields/sections as the admin's own Add Employer offcanvas
            (EmployerController@empVisaStore, resources/views/admin/employer/
            index.blade.php) except the Partner and Careoff selects, which are
            internal/admin-only assignment fields there - here the Employer is
            always created under the logged-in partner, so there is nothing
            for the partner to pick and no way to target another partner's
            account. --}}
            <form id="employerAddForm" method="POST" action="{{ route('worker.partner.employer.store') }}">
                @csrf
                <div class="w-form-grid">
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Employer Name') }}</label>
                        <input type="text" name="employer_name" class="w-input" placeholder="{{ __('locale.Enter Employer Name...') }}" value="{{ old('employer_name') }}">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Employer Arabic Name') }} <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="employer_ar_name" class="w-input" placeholder="{{ __('locale.Enter Employer Name (Arabic)...') }}" value="{{ old('employer_ar_name') }}" required>
                        @error('employer_ar_name')<div class="w-form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Visa No') }} <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="visa_no" class="w-input" placeholder="{{ __('locale.Enter Visa No...') }}" value="{{ old('visa_no') }}" required>
                        @error('visa_no')<div class="w-form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.ID No') }} <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="id_no" class="w-input" placeholder="{{ __('locale.Enter ID No...') }}" value="{{ old('id_no') }}" required>
                        @error('id_no')<div class="w-form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Visa Date') }}</label>
                        <input type="date" name="visa_date" class="w-input" autocomplete="off" placeholder="{{ __('locale.Enter Visa Date...') }}" value="{{ old('visa_date') }}">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Issuing Authority') }} <span style="color:#dc2626;">*</span></label>
                        <select name="issuing_authority" class="w-select w-select2" data-placeholder="{{ __('locale.Select Issuing Authority') }}">
                            <option value=""></option>
                            <option value="Mumbai" @selected(old('issuing_authority') === 'Mumbai')>{{ __('locale.Mumbai') }}</option>
                            <option value="New Delhi" @selected(old('issuing_authority') === 'New Delhi')>{{ __('locale.New Delhi') }}</option>
                        </select>
                        @error('issuing_authority')<div class="w-form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.City of Work') }} <span style="color:#dc2626;">*</span></label>
                        <select name="wpcity_id" class="w-select w-select2" data-placeholder="{{ __('locale.Select City of Work') }}">
                            <option value=""></option>
                            @foreach ($workCities as $workCity)
                                <option value="{{ $workCity->id }}" @selected(old('wpcity_id') == $workCity->id)>{{ $workCity->display_name }}</option>
                            @endforeach
                        </select>
                        @error('wpcity_id')<div class="w-form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="w-form-row w-form-row-full">
                        <label class="w-form-label">{{ __('locale.Profession') }} / {{ __('locale.Openings') }} / {{ __('locale.Monthly Salary') }} <span style="color:#dc2626;">*</span></label>
                        @php
                            $oldProffIds = old('proff_id', []);
                            $oldOpenings = old('openings', []);
                            $oldSalaries = old('salary', []);
                            $prefillRows = [];
                            foreach ($oldProffIds as $i => $proffId) {
                                $prefillRows[] = ['proff_id' => $proffId, 'openings' => $oldOpenings[$i] ?? '', 'salary' => $oldSalaries[$i] ?? ''];
                            }
                        @endphp
                        @include('worker.partner.employer-plus._visa-rows', ['professionOptions' => $professionOptions, 'prefillRows' => $prefillRows])
                        @error('proff_id')<div class="w-form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="w-form-row w-form-row-full">
                        <label class="w-form-label">{{ __('locale.Notes') }}</label>
                        <textarea name="notes" class="w-input" rows="3" placeholder="{{ __('locale.Enter Notes...') }}">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:18px;">
                    <button type="button" class="w-btn w-btn-outline w-btn-block" data-add-employer-close>
                        {{ __('locale.Close') }}
                    </button>
                    <button type="submit" class="w-btn w-btn-primary w-btn-block" data-add-employer-submit
                        data-default-text="{{ __('locale.Add Employer') }}"
                        data-loading-text="{{ __('locale.Saving…') }}">
                        {{ __('locale.Add Employer') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
