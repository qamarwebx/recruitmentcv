<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use App\Support\OtpVerification;
use App\Support\PartnerMobile;

/**
 * Completes partner login/registration after the shared OTP endpoints
 * (generate-otp2 / validate-otp2, in Auth\WhatsappOtpController) have
 * confirmed the mobile number. Mirrors WhatsappOtpController's
 * loginUser2/registerUser2 pattern, but against the `partner` guard and
 * gated by Partner::registration_status (Pending/Approved/Rejected).
 */
class PartnerAuthController extends Controller
{
    /**
     * Dedicated Partner login/register page (GET /partner/login) - works on
     * the apex and on every partner subdomain (routes/worker.php has no
     * domain restriction). Renders the existing auth partial inline; the
     * OTP/login/register/Google flow behind it is unchanged.
     */
    public function loginPage(Request $request)
    {
        $partner = Auth::guard('partner')->user();

        // Already signed in (Pending or Approved - only Rejected can't hold a session).
        if ($partner && !$partner->isRegistrationRejected()) {
            return redirect($partner->portalBaseUrl() . route('worker.partner.candidates', [], false));
        }

        // Only same-host relative paths - never "//evil.com" or an absolute
        // URL - so this can't be used as an open redirect.
        $redirect = (string) $request->query('redirect', '');
        if (!str_starts_with($redirect, '/') || str_starts_with($redirect, '//') || str_contains($redirect, '\\')) {
            $redirect = '';
        }

        return view('worker.partner.login', [
            'partnerAuthInline' => true,
            'redirect' => $redirect,
            'mode' => $request->query('mode') === 'register' ? 'register' : 'login',
        ]);
    }

    public function login(Request $request)
    {
        if (Auth::guard('partner')->check()) {
            return response()->json(['status' => 'success']);
        }

        $mobile = $request->session()->get('Mobile');

        // The session number alone is set before the OTP is entered - only
        // a number that actually passed validate-otp2 may log in.
        if (!$mobile || !OtpVerification::isVerified($request)) {
            return response()->json(['status' => 'error', 'message' => __('locale.Please verify your mobile number first.')], 422);
        }

        // Recognizes the number in either stored format (CRM: code+number,
        // this site: local number) - see App\Support\PartnerMobile.
        $partner = PartnerMobile::find($request->session()->get('CountryCode'), $mobile);

        // Not a partner's number: a team member's? (verified by the same OTP)
        if (!$partner && ($member = \App\Models\PartnerTeamMember::findByMobile($request->session()->get('CountryCode'), $mobile))) {
            OtpVerification::consume($request);
            $request->session()->forget('Mobile');

            return $this->completeTeamMemberLogin($request, $member);
        }

        if (!$partner) {
            $request->session()->put('new_partner', true);
            return response()->json(['status' => 'new_partner']);
        }

        // Recorded regardless of the registration_status gate below - this
        // OTP verification is a real event against this Partner record even
        // if a still-pending registerPending() row (see register() /
        // registerPending() above) is the one being verified here, e.g. the
        // original Register attempt was abandoned before OTP and the same
        // number is now being verified through Login instead.
        if (!$partner->mobile_verified_at) {
            // A self-registration row that was never verified (still
            // pending approval): anything identity-related on it was saved
            // before anyone proved they own this number (older
            // registerPending() stored password / Google identity / email
            // before OTP). Drop it now, so none of it can survive into an
            // approved account - the verified owner sets their own through
            // register(). Admin-created partners (already approved) keep
            // their admin-set values.
            if ((int) $partner->registration_status === 0) {
                $partner->password = null;
                $partner->google_id = null;
                $partner->email = null;
            }
            $partner->mobile_verified_at = now();
            $partner->save();
        }

        // Pending partners sign in (Hire Now / Download CV wait for approval -
        // Partner::isRegistrationApproved()); a Rejected one can't.
        if ($partner->isRegistrationRejected()) {
            return response()->json(['status' => 'rejected', 'message' => __('locale.Your registration has been rejected.')], 403);
        }

        Auth::guard('partner')->login($partner);
        OtpVerification::consume($request);
        $request->session()->forget('Mobile');
        $request->session()->regenerate();

        // Partner::portalBaseUrl() sends them to their OWN configured
        // subdomain if they have one (regardless of which host - base
        // domain or another partner's subdomain - they logged in from),
        // otherwise the app's default host. route(..., [], false) keeps
        // the actual URI in sync with the route definition instead of
        // hardcoding '/partner/candidates' a second time here.
        $redirect = $partner->portalBaseUrl() . route('worker.partner.candidates', [], false);

        return response()->json(['status' => 'success', 'redirect' => $redirect]);
    }

    /**
     * Login with Password. The identifier is a mobile number (country code
     * + number, existing PartnerMobile lookup) or a username / email address
     * (findByAccountIdentifier()) - identifier_type only picks which lookup
     * runs; the partner is always resolved here, server-side. Checked
     * against partners.password (bcrypt, the column the CRM also writes);
     * same rate limits and verified/approved rules for both, and a password
     * never replaces the one-time mobile OTP verification.
     */
    public function passwordLogin(Request $request)
    {
        $byAccount = $request->input('identifier_type') === 'account';

        $validator = Validator::make($request->all(), $byAccount ? [
            'identifier' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ] : [
            'country_code' => 'required|string|max:5',
            'mobile' => 'required|string|max:20',
            'password' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $byAccount
                ? __('locale.Please enter your username or email address and password.')
                : __('locale.Please enter your mobile number and password.')], 422);
        }

        // Own limiter keys (not the route throttle middleware, whose
        // guest key is shared by every throttled route on the domain):
        // 5 failures per identifier + IP, and 30 attempts per IP overall.
        $identifierKey = $byAccount
            ? 'account:' . strtolower(trim((string) $request->identifier))
            : preg_replace('/\D/', '', $request->country_code . $request->mobile);
        $throttleKey = 'partner-password-login:' . sha1($identifierKey . '|' . $request->ip());
        $ipKey = 'partner-password-login-ip:' . sha1($request->ip());

        foreach ([[$throttleKey, 5], [$ipKey, 30]] as [$key, $max]) {
            if (RateLimiter::tooManyAttempts($key, $max)) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('locale.Too many login attempts. Please try again in :seconds seconds.', ['seconds' => RateLimiter::availableIn($key)]),
                ], 429);
            }
        }
        RateLimiter::hit($ipKey);

        $partner = $byAccount
            ? $this->findByAccountIdentifier($request->identifier)
            : PartnerMobile::find($request->country_code, $request->mobile);

        // Not a partner: a team member with these identifiers? (Partners
        // always win - identifiers are kept unique across both.)
        if (!$partner) {
            $member = $byAccount
                ? \App\Models\PartnerTeamMember::findByAccountIdentifier($request->identifier)
                : \App\Models\PartnerTeamMember::findByMobile($request->country_code, $request->mobile);
            if ($member && !empty($member->password) && Hash::check($request->password, $member->password)) {
                RateLimiter::clear($throttleKey);

                return $this->completeTeamMemberLogin($request, $member);
            }
        }

        if (!$partner || empty($partner->password) || !Hash::check($request->password, $partner->password)) {
            RateLimiter::hit($throttleKey);

            return response()->json(['status' => 'error', 'message' => $byAccount
                ? __('locale.The username, email or password is incorrect.')
                : __('locale.The mobile number or password is incorrect.')], 422);
        }

        RateLimiter::clear($throttleKey);

        return $this->completePartnerLogin($request, $partner);
    }

    /**
     * The Partner a username or email address identifies: partners.email
     * when it contains "@", otherwise partners.username. Neither column is
     * unique in the database, so only an EXACT single match counts - an
     * ambiguous identifier never logs in to (or sends a code for) any of
     * the matching accounts.
     */
    private function findByAccountIdentifier(?string $identifier): ?Partner
    {
        $identifier = trim((string) $identifier);
        if ($identifier === '') {
            return null;
        }

        $matches = Partner::where(str_contains($identifier, '@') ? 'email' : 'username', $identifier)->limit(2)->get();

        return $matches->count() === 1 ? $matches->first() : null;
    }

    /**
     * Team member login (password / mobile OTP / email OTP / Google): the
     * member must be active and its partner approved; the partner guard then
     * holds that partner (every portal query stays scoped to it) and the
     * session records the member (App\Support\PartnerTeam). Lands on the
     * partner's own site, on the member's first permitted page.
     */
    public function completeTeamMemberLogin(Request $request, \App\Models\PartnerTeamMember $member)
    {
        $partner = $member->partner;

        if (!$member->status) {
            return response()->json(['status' => 'error', 'message' => __('locale.This team member account is disabled. Please contact your partner.')], 403);
        }
        // Same rule as the partner: a Rejected partner's team can't sign in;
        // Pending is fine (Hire Now / Download CV wait for approval).
        if (!$partner || $partner->isRegistrationRejected()) {
            return response()->json(['status' => 'error', 'message' => __('locale.Your partner account is not active.')], 403);
        }

        Auth::guard('partner')->login($partner);   // Login event clears any earlier member marker
        $request->session()->regenerate();
        \App\Support\PartnerTeam::start($member);

        $path = \App\Support\PartnerTeam::homeUrl($member, false) ?? route('worker.partner.account', [], false);

        return response()->json(['status' => 'success', 'redirect' => $partner->portalBaseUrl() . $path]);
    }

    /**
     * Shared final step for password and email-OTP login, once the partner
     * is identified and authenticated: the existing rules (OTP-verified
     * mobile, approved registration), then the partner guard login with a
     * regenerated session, and the partner's own site
     * (Partner::portalBaseUrl()) as the redirect.
     */
    private function completePartnerLogin(Request $request, Partner $partner)
    {
        if (!$partner->mobile_verified_at) {
            return response()->json(['status' => 'error', 'message' => __('locale.Please log in with OTP once to verify your mobile number.')], 403);
        }

        // Pending signs in; Rejected can't (Partner::isRegistrationRejected()).
        if ($partner->isRegistrationRejected()) {
            return response()->json(['status' => 'rejected', 'message' => __('locale.Your registration has been rejected.')], 403);
        }

        Auth::guard('partner')->login($partner);
        $request->session()->regenerate();

        $redirect = $partner->portalBaseUrl() . route('worker.partner.candidates', [], false);

        return response()->json(['status' => 'success', 'redirect' => $redirect]);
    }

    /**
     * Login with OTP by username / email: the partner is resolved here and
     * a 6-digit code is emailed ONLY to that partner's own, already-verified
     * email (email_verified_at) - never to an address typed by the visitor.
     * The response is identical whether or not an account matched or could
     * receive a code, so it reveals nothing; a non-match still gets a
     * session entry that can never verify. Rate-limited per identifier +
     * IP by ThrottlePartnerOtpSend (route middleware).
     */
    public function emailOtpSend(Request $request)
    {
        $validator = Validator::make($request->all(), ['identifier' => 'required|string|max:255']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => __('locale.Please enter your username or email address.')], 422);
        }

        $partner = $this->findByAccountIdentifier($request->identifier);
        $canReceive = $partner && filled($partner->email) && $partner->email_verified_at;
        // Not a partner: a team member's own email (set by its partner).
        $member = !$partner ? \App\Models\PartnerTeamMember::findByAccountIdentifier($request->identifier) : null;

        $this->issueEmailCode($request, 'partner_email_login', $canReceive ? $partner : ($member && filled($member->email) ? $member : null), 'login');

        return response()->json(['message' => 'otpsend']);
    }

    /** Verifies the emailed login code against the partner resolved at send time. */
    public function emailOtpVerify(Request $request)
    {
        $partner = $this->consumeEmailCode($request, 'partner_email_login', true);
        if ($partner instanceof \App\Models\PartnerTeamMember) {
            return $this->completeTeamMemberLogin($request, $partner);
        }
        if (!$partner instanceof Partner) {
            return $partner;
        }

        return $this->completePartnerLogin($request, $partner);
    }

    /**
     * Forgot Password, step 1: a 6-digit reset code to the Partner whose
     * registered email (partners.email - never username, mobile or a
     * customer account) is exactly the one submitted. Same email-code
     * scheme, mailer and per-address/IP limits as the email login code;
     * the reply is the same generic notice whether or not a Partner matched.
     */
    public function passwordResetSend(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => 'required|string|email|max:255']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => __('locale.Please enter a valid email address.')], 422);
        }

        $email = trim((string) $request->email);
        $matches = Partner::where('email', $email)->limit(2)->get();
        $partner = $matches->count() === 1 ? $matches->first() : null;

        // Signed in (e.g. Partner Account -> Forgot Password): only the
        // signed-in partner's own account - same generic reply otherwise.
        $signedInId = Auth::guard('partner')->id();
        if ($partner && $signedInId && $partner->id !== $signedInId) {
            $partner = null;
        }

        // A new request always starts over (drops any earlier verified state).
        $request->session()->forget('partner_password_reset_verified');
        $this->issueEmailCode($request, 'partner_password_reset', $partner, 'reset');

        return response()->json([
            'message' => 'otpsend',
            'notice' => __('locale.If this email is registered, an OTP has been sent.'),
        ]);
    }

    /**
     * Forgot Password, step 2: checks the code. Success only marks this
     * session as allowed to set a new password for the Partner resolved at
     * send time (for a limited time) - it never logs anyone in.
     */
    public function passwordResetVerify(Request $request)
    {
        $limits = config('partner.password_reset');
        $ipKey = 'partner-password-reset-verify:' . sha1((string) $request->ip());
        if (RateLimiter::tooManyAttempts($ipKey, (int) $limits['verify_per_ip'])) {
            return response()->json(['status' => 'error', 'message' => __('locale.Too many attempts. Please try again later.')], 429);
        }
        RateLimiter::hit($ipKey, (int) $limits['verify_minutes'] * 60);

        $partner = $this->consumeEmailCode($request, 'partner_password_reset', false);
        if (!$partner instanceof Partner) {
            return $partner;
        }

        $request->session()->put('partner_password_reset_verified', [
            'partner_id' => $partner->id,
            'email' => $partner->email,
            'expires_at' => now()->addMinutes((int) $limits['update_minutes'])->timestamp,
        ]);

        return response()->json(['status' => 'verified', 'message' => __('locale.OTP verified successfully.')]);
    }

    /**
     * Forgot Password, step 3: the new password (existing policy +
     * confirmation, same rule as Partner Account -> Change Password) for
     * the Partner this session verified - never a partner id from the
     * browser. Only partners.password changes; the verified state is
     * dropped, so the code / verification cannot be used again. No login:
     * the Partner signs in normally afterwards.
     */
    public function passwordResetUpdate(Request $request)
    {
        $verified = $request->session()->get('partner_password_reset_verified');

        if (!$verified || now()->timestamp > $verified['expires_at']) {
            $request->session()->forget('partner_password_reset_verified');

            return response()->json(['status' => 'error', 'message' => __('locale.Your password reset session has expired. Please start again.')], 422);
        }

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [], ['password' => __('locale.New Password')]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
        }

        $partner = Partner::find($verified['partner_id']);
        $signedInId = Auth::guard('partner')->id();
        if (!$partner || strcasecmp((string) $partner->email, (string) $verified['email']) !== 0
            || ($signedInId && $partner->id !== $signedInId)) {
            $request->session()->forget('partner_password_reset_verified');

            return response()->json(['status' => 'error', 'message' => __('locale.Your password reset session has expired. Please start again.')], 422);
        }

        $partner->password = Hash::make($request->password);
        $partner->save();
        $request->session()->forget(['partner_password_reset_verified', 'partner_password_reset']);

        return response()->json(['status' => 'success', 'message' => __('locale.Password updated successfully.')]);
    }

    /**
     * Shared email-code issuer (email login + Forgot Password): a 6-digit
     * code emailed to $partner's stored email through this website's SMTPs
     * (App\Support\SmtpMailer: the partner site's own, then Global, else
     * .env) and the branded
     * partner-email-verification template; only its hash goes into the
     * session under $key, with the partner/email it was sent to, an expiry
     * and an attempt counter. $partner null (no match) or a failed send
     * still stores an entry - one that can never verify.
     */
    private function issueEmailCode(Request $request, string $key, Partner|\App\Models\PartnerTeamMember|null $partner, string $purpose): void
    {
        $minutes = (int) config('partner.email_code.minutes');
        $code = (string) random_int(100000, 999999);
        $sent = false;
        // A team member (Login with OTP by username/email only): its own
        // name and email, its partner's company.
        $member = $partner instanceof \App\Models\PartnerTeamMember ? $partner : null;

        if ($partner && filled($partner->email)) {
            try {
                \App\Support\SmtpMailer::forSite()->to($partner->email)->send(new \App\Mail\SendOTPVerification([
                    'name' => $member ? $member->full_name : ($partner->owner_name ?: $partner->rec_off_name),
                    'company' => $member ? optional($member->partner)->rec_off_name : $partner->rec_off_name,
                    'EmailOtp' => $code,
                    'email' => $partner->email,
                    'purpose' => $purpose,
                    'expires_minutes' => $minutes,
                    'view' => 'emails.partner-email-verification',
                    'subject' => $purpose === 'reset' ? 'Your partner password reset code' : 'Your partner login code: ' . $code,
                ]));
                $sent = true;
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Partner email code not sent', ['partner_id' => $partner->id, 'purpose' => $purpose, 'exception' => get_class($e)]);
            }
        }

        $request->session()->put($key, [
            'partner_id' => $sent && !$member ? $partner->id : null,
            'team_member_id' => $sent && $member ? $member->id : null,
            'email' => $sent ? $partner->email : null,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes($minutes)->timestamp,
            'attempts' => 0,
        ]);
    }

    /**
     * Checks the submitted code against the session entry $key and returns
     * the Partner it was sent to, or the JSON error to send back. One-time:
     * the entry is removed on success, on expiry and after too many wrong
     * codes. The Partner must still have the same email the code went to
     * (and, for login, still verified).
     */
    private function consumeEmailCode(Request $request, string $key, bool $requireVerifiedEmail): Partner|\App\Models\PartnerTeamMember|\Illuminate\Http\JsonResponse
    {
        $pending = $request->session()->get($key);
        $otp = trim((string) $request->otp);
        $expired = response()->json(['status' => 'error', 'message' => __('locale.The verification code has expired. Please request a new one.')], 422);

        if (!$pending || now()->timestamp > $pending['expires_at']) {
            $request->session()->forget($key);

            return $expired;
        }

        if ($otp === '' || !Hash::check($otp, $pending['code']) || (!$pending['partner_id'] && empty($pending['team_member_id']))) {
            $pending['attempts']++;
            if ($pending['attempts'] >= (int) config('partner.email_code.attempts')) {
                $request->session()->forget($key);

                return response()->json(['status' => 'error', 'message' => __('locale.Too many incorrect codes. Please request a new one.')], 422);
            }
            $request->session()->put($key, $pending);

            return response()->json(['status' => 'error', 'message' => __('locale.The verification code is incorrect.')], 422);
        }

        $request->session()->forget($key);

        if (!empty($pending['team_member_id'])) {
            $member = \App\Models\PartnerTeamMember::find($pending['team_member_id']);

            return $member && strcasecmp((string) $member->email, (string) $pending['email']) === 0 ? $member : $expired;
        }

        $partner = Partner::find($pending['partner_id']);
        if (!$partner || ($requireVerifiedEmail && !$partner->email_verified_at) || strcasecmp((string) $partner->email, (string) $pending['email']) !== 0) {
            return $expired;
        }

        return $partner;
    }

    /**
     * Password for Partner registration (single field, no confirmation) - required for a
     * normal (mobile) registration, optional for the Google flow.
     */
    private function passwordRules(Request $request): array
    {
        return ['password' => [$request->filled('google_token') ? 'nullable' : 'required', Password::defaults()]];
    }

    /**
     * Real-time "is this number already a partner?" hint fired while typing
     * in the Register step's mobile field (see partner-auth.js) - lets the
     * UI block the Send OTP click before it happens, instead of only
     * catching it after a full OTP round-trip in register() below. Reuses
     * that same owner_mobile_no lookup and message.
     */
    public function checkMobile(Request $request)
    {
        $mobile = trim((string) $request->mobile);

        if ($mobile === '') {
            return response()->json(['exists' => false]);
        }

        $exists = PartnerMobile::query($request->country_code, $mobile)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? __('locale.An account with this mobile number already exists.') : null,
        ]);
    }

    /**
     * Is this number already a real Partner account - OTP-verified, or
     * approved/rejected by an admin (admin-created partners can be approved
     * without a verified mobile)? Such a row is never overwritten by a
     * registration request; the owner logs in instead.
     */
    private function mobileHasAccount(?string $countryCode, string $mobile): bool
    {
        return PartnerMobile::query($countryCode, $mobile)
            ->where(fn ($q) => $q->whereNotNull('mobile_verified_at')->orWhere('registration_status', '!=', 0))
            ->exists();
    }

    /** The unverified, still-pending self-registration row for this number (or null). */
    private function pendingRegistrationRow(?string $countryCode, string $mobile): ?Partner
    {
        return PartnerMobile::query($countryCode, $mobile)
            ->whereNull('mobile_verified_at')
            ->where('registration_status', 0)
            ->orderBy('id')
            ->first();
    }

    /**
     * Saves the submitted registration details immediately when "Send OTP"
     * is clicked in the Register as Partner modal - before OTP is ever
     * verified - so admin can see registration attempts even if the OTP
     * step is abandoned. mobile_verified_at is deliberately left null here;
     * that's what marks this row as "pending OTP verification" (as opposed
     * to registration_status, which separately gates admin approval and is
     * unaffected by this step). register() below finalizes this same row -
     * by owner_mobile_no, which is unique at the DB level - once OTP
     * verification actually succeeds; it never creates a second row.
     */
    public function registerPending(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'google_token' => 'nullable|string|max:255',
        ] + $this->passwordRules($request));

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $mobile = $request->session()->get('Mobile');

        if (!$mobile) {
            return response()->json(['status' => 'error', 'message' => __('locale.Please verify your mobile number first.')], 422);
        }

        // Only a real account (verified, or approved/rejected by an admin)
        // is a conflict - a still-pending row for this same number (e.g.
        // Resend OTP, or a retry after abandoning an earlier attempt) is
        // reused below instead.
        if ($this->mobileHasAccount($request->session()->get('CountryCode'), $mobile)) {
            return response()->json(['status' => 'error', 'message' => __('locale.An account with this mobile number already exists.')], 422);
        }

        $googleData = null;
        if ($request->filled('google_token')) {
            $googleData = Cache::get('partner_google_register:' . $request->google_token);

            if (!$googleData) {
                return response()->json(['status' => 'error', 'message' => __('locale.Your Google sign-in has expired. Please try again.')], 422);
            }

            if (Partner::where('email', $googleData['email'])->whereNotNull('mobile_verified_at')->exists()) {
                return response()->json(['status' => 'error', 'message' => __('locale.An account with this email already exists.')], 422);
            }
        }

        $countryId = $request->session()->get('CountryId');
        $country = $countryId ? Country::find($countryId) : null;

        // Requesting an OTP is NOT proof of owning this number, so only the
        // non-sensitive "registration attempt" details are kept here. No
        // password, Google identity or email (Google login matches partners
        // by email) - those are saved only by register(), after the OTP is
        // verified; the password/Google fields above are only validated
        // now so the form reports errors before the OTP step.
        $partner = $this->pendingRegistrationRow($request->session()->get('CountryCode'), $mobile) ?? new Partner();
        $partner->rec_off_name = $request->company_name;
        $partner->owner_name = $googleData['name'] ?? $request->full_name;
        if (!$partner->exists) {
            $partner->owner_mobile_no = $mobile;
        }
        // The code chosen in the selector the OTP was sent with (Primary Mobile country code).
        $partner->owner_mobile_country_code = preg_replace('/\D+/', '', (string) $request->session()->get('CountryCode')) ?: null;
        $partner->user_type = '1';
        $partner->country_id = $country->id ?? null;
        // A new partner starts fully inactive/pending: Partner Status
        // Inactive, Portal Status Deactive, Registration Request Pending -
        // the admin approves/activates it from CRM -> Partner.
        $partner->status = 0;
        $partner->portal_status = 0;
        $partner->registration_status = 0;
        // mobile_verified_at intentionally NOT set - still pending OTP
        // verification. register() sets it once that actually succeeds.
        $partner->save();

        // Match the existing admin-created-partner pattern (PartnerController::store)
        // which also self-references partner_id = id for uniqueness.
        if (!$partner->partner_id) {
            $partner->partner_id = $partner->id;
            $partner->save();
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Finalizes the pending Partner row registerPending() above already
     * created for this session's Mobile, once OTP verification has
     * actually succeeded (see verifyOtp() in partner-auth.js, which calls
     * this instead of the login flow for a register-mode OTP success).
     * Updates that same row rather than creating a new one - owner_mobile_no
     * is unique at the DB level, so a second insert here would fail anyway.
     * Still reachable as a plain create+verify in one step for the
     * "tried to Login, number wasn't registered" fallback form (step 3 of
     * the modal), which never goes through registerPending() since no
     * register-mode Send OTP ever happened for that path - firstOrNew below
     * covers both.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'google_token' => 'nullable|string|max:255',
        ] + $this->passwordRules($request));

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $mobile = $request->session()->get('Mobile');

        // Finalizing sets mobile_verified_at, so it requires a number that
        // actually passed validate-otp2 (unlike registerPending(), which
        // only ever saves an unverified row before the OTP step).
        if (!$mobile || !OtpVerification::isVerified($request)) {
            return response()->json(['status' => 'error', 'message' => __('locale.Please verify your mobile number first.')], 422);
        }

        // An existing real account (verified, or approved/rejected) is never
        // re-registered over - its owner logs in instead.
        if ($this->mobileHasAccount($request->session()->get('CountryCode'), $mobile)) {
            return response()->json(['status' => 'error', 'message' => __('locale.An account with this mobile number already exists.')], 422);
        }

        // Google-originated registration (see SocialLoginController::
        // handlePartnerGoogleCallback()): the Register as Partner modal
        // only ever shows Full Name/Email as pre-filled, disabled fields
        // for this flow - re-resolved here from the same server-side cache
        // entry rather than trusted from whatever the client actually
        // submitted for those two fields, so a tampered request body can
        // never register under a different name/email than the one Google
        // itself authenticated.
        $googleData = null;
        if ($request->filled('google_token')) {
            $googleData = Cache::get('partner_google_register:' . $request->google_token);

            if (!$googleData) {
                return response()->json(['status' => 'error', 'message' => __('locale.Your Google sign-in has expired. Please try again.')], 422);
            }

            if (Partner::where('email', $googleData['email'])->whereNotNull('mobile_verified_at')->exists()) {
                Cache::forget('partner_google_register:' . $request->google_token);
                return response()->json(['status' => 'error', 'message' => __('locale.An account with this email already exists.')], 422);
            }
        }

        $countryId = $request->session()->get('CountryId');
        $country = $countryId ? Country::find($countryId) : null;

        $partner = $this->pendingRegistrationRow($request->session()->get('CountryCode'), $mobile) ?? new Partner();
        $partner->rec_off_name = $request->company_name;
        $partner->owner_name = $googleData['name'] ?? $request->full_name;
        // Credentials / login identity come ONLY from this OTP-verified
        // submission - nothing saved on the row before verification
        // survives (an absent value is cleared, not kept).
        $partner->email = $googleData['email'] ?? ($request->email ?: null);
        $partner->google_id = $googleData ? $googleData['google_id'] : null;
        if (!$partner->exists) {
            $partner->owner_mobile_no = $mobile;
        }
        // The code chosen in the selector the OTP was sent with (Primary Mobile country code).
        $partner->owner_mobile_country_code = preg_replace('/\D+/', '', (string) $request->session()->get('CountryCode')) ?: null;
        $partner->user_type = '1';
        $partner->country_id = $country->id ?? null;
        // New partner = inactive/pending until the admin approves/activates
        // it (same initial statuses as registerPending()).
        $partner->status = 0;
        $partner->portal_status = 0;
        $partner->registration_status = 0;
        // Only the password submitted by whoever actually passed OTP (none
        // for a Google-only registration) - never one from before.
        $partner->password = $request->filled('password') ? Hash::make($request->password) : null;
        $partner->mobile_verified_at = now();

        // Username = the local part of the final (OTP-verified) email, e.g.
        // qamarwebx@gmail.com -> qamarwebx - only for a row that has none
        // yet. Same uniqueness rule as the CRM's username check
        // (PartnerController::edcheckusername: not used by another partner);
        // if it is taken it is left empty for an admin to set, never
        // overwritten or reshaped.
        if (blank($partner->username) && filled($partner->email)) {
            $username = Str::before(strtolower(trim($partner->email)), '@');
            $taken = Partner::where('username', $username)
                ->when($partner->exists, fn ($q) => $q->whereKeyNot($partner->getKey()))
                ->exists();
            if ($username !== '' && !$taken) {
                $partner->username = $username;
            }
        }

        $partner->save();

        // Match the existing admin-created-partner pattern (PartnerController::store)
        // which also self-references partner_id = id for uniqueness.
        if (!$partner->partner_id) {
            $partner->partner_id = $partner->id;
            $partner->save();
        }

        if ($googleData) {
            Cache::forget('partner_google_register:' . $request->google_token);
        }

        OtpVerification::consume($request);
        $request->session()->forget(['Mobile', 'new_partner']);

        // Signed in right away (the mobile was just OTP-verified): a Pending
        // partner uses the Portal; Hire Now / Download CV wait for the CRM
        // approval. The "Registration Submitted" screen offers the dashboard.
        Auth::guard('partner')->login($partner);
        $request->session()->regenerate();

        return response()->json([
            'status' => 'pending',
            'message' => __('locale.Thanks for registering! Your account is pending admin approval. We will notify you once approved.'),
            'redirect' => $partner->portalBaseUrl() . route('worker.partner.dashboard', [], false),
        ]);
    }

    public function logout(Request $request)
    {
        // Partner logout only. The session is shared by every
        // *.recruitmentcv.com site and may also hold a Customer login (`web`
        // guard), so it is not invalidated/flushed: only the partner login
        // (the partner guard's own session key and remember cookie) and the
        // partner-only flow data are removed, then the session id is
        // regenerated (fixation-safe). The CSRF token is kept so other open
        // tabs keep working.
        Auth::guard('partner')->logout();
        $request->session()->forget(['partner_email_change', 'partner_google_intent', 'new_partner', \App\Support\PartnerTeam::SESSION_KEY]);
        // New session id, old one destroyed; data (incl. the CSRF token) kept
        // - regenerate() would also rotate the token.
        $request->session()->migrate(true);

        // Partner Login on the SAME host the partner logged out from (route()
        // uses the current request's host - e.g. raha.recruitmentcv.com).
        return redirect()->route('worker.partner.login.page');
    }

    /**
     * Exchanges the short-lived handoff token minted by
     * SocialLoginController::handlePartnerGoogleCallback() (which ran on
     * qamarhire.com, where Google's OAuth app requires the callback) for a
     * real `partner` guard session here on worker.qamarhire.com. The token
     * is single-use (Cache::pull deletes it on read) and expires quickly, so
     * it's only ever valid for this one redirect immediately after Google
     * login succeeds - registration_status was already checked before the
     * token was issued, but it's re-checked here too as defense in depth.
     */
    public function completeGoogleLogin(Request $request)
    {
        $token = $request->query('token');
        $partnerId = $token ? Cache::pull('partner_google_handoff:' . $token) : null;

        if (!$partnerId) {
            return redirect()->route('worker.home', [
                'login' => 1,
                'auth_message' => 'That Google sign-in link has expired. Please try again.',
            ]);
        }

        $partner = Partner::find($partnerId);

        if (!$partner || $partner->isRegistrationRejected()) {
            $message = $partner ? 'Your registration has been rejected.' : 'That Google sign-in link has expired. Please try again.';

            return redirect()->route('worker.home', ['login' => 1, 'auth_message' => $message]);
        }

        Auth::guard('partner')->login($partner);
        $request->session()->regenerate();

        return redirect()->route('worker.partner.candidates');
    }

    /**
     * Attaches a verified mobile number to the ALREADY-authenticated partner
     * - used by the Hire Now gate (candidates.show) and the Profile page's
     * "Add/Change Number" action. Unlike login()/register(), this never
     * creates or logs in a Partner; it only updates the current session's
     * own record, reusing the exact same generate-otp2/validate-otp2 +
     * mobile_verified_at convention as login()/register() above.
     */
    public function verifyMobile(Request $request)
    {
        $partner = Auth::guard('partner')->user();
        $mobile = $request->session()->get('Mobile');

        if (!$mobile || !OtpVerification::isVerified($request)) {
            return response()->json(['status' => 'error', 'message' => __('locale.Please verify your mobile number first.')], 422);
        }

        $existing = PartnerMobile::query($request->session()->get('CountryCode'), $mobile)->where('id', '!=', $partner->id)->first();

        if ($existing) {
            return response()->json(['status' => 'error', 'message' => __('locale.An account with this mobile number already exists.')], 422);
        }

        $partner->owner_mobile_no = $mobile;
        // The code chosen in the selector the OTP was sent with (Primary Mobile country code).
        $partner->owner_mobile_country_code = preg_replace('/\D+/', '', (string) $request->session()->get('CountryCode')) ?: null;
        $partner->mobile_verified_at = now();
        $partner->save();

        OtpVerification::consume($request);
        $request->session()->forget('Mobile');

        return response()->json(['status' => 'success']);
    }
}
