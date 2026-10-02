{{-- Show/hide toggle for the password input it follows inside a
.w-input-password wrapper (registration, Forgot Password, Partner Account
Change Password) - public/worker/js/password-toggle.js.
Inline SVG eye / eye-off, the same icon approach the rest of the site uses. --}}
<button type="button" class="w-input-password-toggle" data-password-toggle
    aria-label="{{ __('locale.Show password') }}" aria-pressed="false"
    data-label-show="{{ __('locale.Show password') }}"
    data-label-hide="{{ __('locale.Hide password') }}">
    <svg class="w-icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
    <svg class="w-icon-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.6 5.1A10.4 10.4 0 0 1 12 5c6.4 0 10 7 10 7a17.6 17.6 0 0 1-2.2 3.1M6.6 6.6C3.9 8.4 2 12 2 12s3.6 7 10 7a9.7 9.7 0 0 0 5.4-1.6"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/><path d="m3 3 18 18"/></svg>
</button>
