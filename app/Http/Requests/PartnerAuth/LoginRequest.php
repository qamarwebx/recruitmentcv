<?php

namespace App\Http\Requests\PartnerAuth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::guard('partner')->attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        // Same conditions as the Partner password / Google login: a
        // Partner session only for an OTP-verified mobile AND an approved
        // registration - an email+password alone (e.g. saved before any
        // OTP verification) is never enough.
        $partner = Auth::guard('partner')->user();
        $message = !$partner->mobile_verified_at
            ? __('locale.Please verify your mobile number first.')
            : ((int) $partner->registration_status === 2
                ? __('locale.Your registration has been rejected.')
                : ((int) $partner->registration_status !== 1 ? __('locale.Your registration is pending approval.') : null));
        // Subdomain removed in CRM: RecruitmentCV portal access revoked.
        if ($message === null && $partner->isPortalRevoked()) {
            $message = __('locale.Your RecruitmentCV portal is currently unavailable. Please contact support.');
        }

        if ($message !== null) {
            Auth::guard('partner')->logout();

            throw ValidationException::withMessages(['email' => $message]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}
