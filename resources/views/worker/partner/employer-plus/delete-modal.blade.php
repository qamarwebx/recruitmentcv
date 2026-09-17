<div class="w-modal-backdrop" data-delete-employer-backdrop></div>
<div class="w-modal" data-delete-employer-modal role="dialog" aria-modal="true" aria-labelledby="deleteEmployerTitle">
    <div class="w-modal-dialog">
        <button type="button" class="w-modal-close" data-delete-employer-close aria-label="Close">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        <div class="w-modal-body">
            <h3 id="deleteEmployerTitle" class="w-modal-title">{{ __('locale.Delete Employer') }}</h3>
            <p class="w-modal-subtitle">{{ __('locale.Are you sure you want to delete this employer? This action cannot be undone.') }}</p>

            <div class="w-form-alert" data-delete-employer-alert hidden></div>

            <div style="display:flex;gap:10px;margin-top:18px;">
                <button type="button" class="w-btn w-btn-outline w-btn-block" data-delete-employer-close>
                    {{ __('locale.No, Keep') }}
                </button>
                <button type="button" class="w-btn w-btn-danger w-btn-block" data-delete-employer-confirm
                    data-default-text="{{ __('locale.Yes, Delete') }}"
                    data-loading-text="{{ __('locale.Deleting…') }}">
                    {{ __('locale.Yes, Delete') }}
                </button>
            </div>
        </div>
    </div>
</div>
