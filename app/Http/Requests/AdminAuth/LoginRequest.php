<?php

namespace App\Http\Requests\AdminAuth;

use App\Models\AdminDevice;
use App\Models\Admin;
use App\Notifications\NewAdminDeviceLoginNotification;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
 use Illuminate\Support\Facades\Cookie;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;


class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email_username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required' => 'Location access is required to sign in. Please allow location access and try again.',
            'longitude.required' => 'Location access is required to sign in. Please allow location access and try again.',
            'accuracy.required' => 'Location access is required to sign in. Please allow location access and try again.',
            'latitude.numeric' => 'Invalid location data received. Please try again.',
            'longitude.numeric' => 'Invalid location data received. Please try again.',
            'accuracy.numeric' => 'Invalid location data received. Please try again.',
            'latitude.between' => 'Invalid location data received. Please try again.',
            'longitude.between' => 'Invalid location data received. Please try again.',
        ];
    }

    /**
     * TEMP DEBUG: log why an admin login request failed validation
     * (never logs the password). Safe to remove once geolocation capture
     * is confirmed stable across environments.
     */
    protected function failedValidation(Validator $validator): void
    {
        Log::warning('Admin login validation failed', [
            'failed_fields' => array_keys($validator->errors()->toArray()),
            'has_latitude'  => $this->filled('latitude'),
            'has_longitude' => $this->filled('longitude'),
            'has_accuracy'  => $this->filled('accuracy'),
            'ip'            => $this->ip(),
            'environment'   => app()->environment(),
        ]);

        parent::failedValidation($validator);
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginInput = $this->input('email_username');
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!Auth::guard('admin')->attempt([$fieldType => $loginInput, 'password' => $this->password], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email_username' => trans('Invalid credentials provided'),
            ]);
        }

        $user = Auth::guard('admin')->user();

        if ($user->login_status !== 1) {
            Auth::guard('admin')->logout();
            throw ValidationException::withMessages([
                'email_username' => 'Your account is deactivated. Please contact the administrator.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        /** -----------------------------
         * DEVICE-WISE LOGIN APPROVAL
         * ----------------------------- */
        $agent = new Agent();
        $ip = $this->ip();

        // Real browser GPS location, validated by rules() above (never trust it beyond that).
        $latitude = $this->latitude;
        $longitude = $this->longitude;
        $accuracy = $this->accuracy;

        // TEMP DEBUG: confirm geolocation coordinates reached the backend.
        Log::info('Admin login geolocation received', [
            'admin_id' => $user->id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy' => $accuracy,
            'ip' => $ip,
        ]);

        /** -----------------------------
         * 🔑 UNIQUE PERSISTENT DEVICE TOKEN (cookie based)
         * ----------------------------- */
        $deviceToken = $this->cookie('device_token');
        if (!$deviceToken) {
            $deviceToken = (string) Str::uuid();
            Cookie::queue('device_token', $deviceToken, 60 * 24 * 365); // 1 साल तक valid
        }

        // Ab device ID actual unique token + user id se banega,
        // generic browser/platform/city se nahi (jo collide ho sakte the)
        $deviceId = hash('sha256', $deviceToken . $user->id);

        // Check if device already exists
        $existingDevice = AdminDevice::where('admin_id', $user->id)
            ->where('device_id', $deviceId)
            ->first();

        /** -----------------------------
         * 🔐 APPROVE ALL LOGIN HANDLING
         * ----------------------------- */
        $superAdmin = Admin::where('user_type', 1)->first();
        $approveAll = $superAdmin->approve_all_login;

        $isSuperAdmin = $user->user_type == 1;

        // 🆕 New Device Detected
        if (empty($existingDevice)) {

            // ✅ Super admin, or "Approve All Login" enabled → create pre-approved & allow login
            if ($isSuperAdmin || $approveAll) {
                AdminDevice::create([
                    'admin_id'    => $user->id,
                    'device_id'   => $deviceId,
                    'device_type' => $agent->device() ?: 'Unknown',
                    'browser'     => $agent->browser() ?: 'Unknown',
                    'os'          => $agent->platform() ?: 'Unknown',
                    'ip_address'  => $ip,
                    'latitude'    => $latitude,
                    'longitude'   => $longitude,
                    'accuracy'    => $accuracy,
                    'is_approved' => true,
                    'login_status'=> 'Active',
                ]);

                Admin::whereId($user->id)->update(['last_login_at' => now()]);
                session(['device_id' => $deviceId]);
                return;
            }

            // 🔒 Otherwise (staff) → Create record & wait for approval
            $device = AdminDevice::create([
                'admin_id'    => $user->id,
                'device_id'   => $deviceId,
                'device_type' => $agent->device() ?: 'Unknown',
                'browser'     => $agent->browser() ?: 'Unknown',
                'os'          => $agent->platform() ?: 'Unknown',
                'ip_address'  => $ip,
                'latitude'    => $latitude,
                'longitude'   => $longitude,
                'accuracy'    => $accuracy,
                'is_approved' => false,
                'login_status'=> 'Inactive',
            ]);

            Auth::guard('admin')->logout();

            // Notify super admin
            if ($superAdmin && $superAdmin->login_access_email != null) {
                try {
                    Notification::route('mail', $superAdmin->login_access_email)
                        ->notify(new NewAdminDeviceLoginNotification(
                            $user,
                            $device,
                            $agent,
                            $ip
                        ));
                } catch (\Exception $e) {
                    \Log::error('New device login notification failed: ' . $e->getMessage());
                }
            }

            throw ValidationException::withMessages([
                'email_username' => 'You are trying to log in from a new device. Please wait for approval.'
            ])->redirectTo(route('auth.login-message', [
                'device_id' => $deviceId,
                'message'   => 'You are trying to log in from a new device. Please wait for approval.'
            ]));
        }

        // 🚫 Device exists but not approved
        if ($existingDevice && !$existingDevice->is_approved) {

            // Super admin can never stay locked out even if their device was revoked.
            if ($isSuperAdmin || $approveAll) {
                $existingDevice->update([
                    'is_approved'  => true,
                    'login_status' => 'Active',
                    'latitude'     => $latitude,
                    'longitude'    => $longitude,
                    'accuracy'     => $accuracy,
                ]);

                Admin::whereId($user->id)->update(['last_login_at' => now()]);
                session(['device_id' => $existingDevice->device_id]);
                return;
            }

            Auth::guard('admin')->logout();

            throw ValidationException::withMessages([
                'email_username' => 'Your device is pending approval. Please wait for verification.'
            ])->redirectTo(route('auth.login-message', [
                'device_id' => $deviceId,
                'message'   => 'Your device is pending approval. Please wait for verification.'
            ]));
        }

        // ✅ Approved device → allow login, mark as Active, refresh location
        if ($existingDevice && $existingDevice->is_approved) {

            Admin::whereId($user->id)->update([
                'last_login_at' => now()
            ]);

            $existingDevice->update([
                'login_status' => 'Active',
                'latitude'     => $latitude,
                'longitude'    => $longitude,
                'accuracy'     => $accuracy,
                'updated_at'   => now(),
            ]);

            session(['device_id' => $existingDevice->device_id]);
        }
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email_username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email_username')) . '|' . $this->ip());
    }
}
