<div class="w-modal-backdrop" data-assign-candidate-backdrop></div>
<div class="w-modal" data-assign-candidate-modal role="dialog" aria-modal="true" aria-labelledby="assignCandidateTitle">
    <div class="w-modal-dialog" style="max-width:760px;">
        <button type="button" class="w-modal-close" data-assign-candidate-close aria-label="Close">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        <div class="w-modal-body">
            <h3 id="assignCandidateTitle" class="w-modal-title">{{ __('locale.Assign Candidate') }}</h3>

            <div class="w-form-alert" data-assign-candidate-alert hidden></div>

            <div class="w-form-row">
                <input type="text" class="w-input" placeholder="{{ __('locale.Search candidates...') }}" data-assign-candidate-search>
            </div>

            <div data-assign-candidate-loading style="text-align:center;padding:24px 0;color:var(--w-ink-500);">
                {{ __('locale.Loading…') }}
            </div>

            <div class="wp-table-wrap" data-assign-candidate-table-wrap style="max-height:360px;overflow-y:auto;" hidden>
                <div class="wp-table-scroll">
                    <table class="wp-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>{{ __('locale.Candidate') }}</th>
                                <th>{{ __('locale.Passport No') }}</th>
                                <th>{{ __('locale.Profession') }}</th>
                            </tr>
                        </thead>
                        <tbody data-assign-candidate-rows></tbody>
                    </table>
                </div>
            </div>

            <div class="wp-empty" data-assign-candidate-empty style="padding:24px 0;" hidden>
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
                <p>{{ __('locale.No eligible candidates found for this employer right now.') }}</p>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
                <button type="button" class="w-btn w-btn-outline" data-assign-candidate-close>
                    {{ __('locale.Close') }}
                </button>
                <button type="button" class="w-btn w-btn-primary" data-assign-candidate-submit disabled
                    data-default-text="{{ __('locale.Assign Candidate') }}"
                    data-loading-text="{{ __('locale.Assigning…') }}">
                    {{ __('locale.Assign Candidate') }}
                </button>
            </div>
        </div>
    </div>
</div>
