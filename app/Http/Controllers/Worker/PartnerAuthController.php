<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

/**
 * Completes partner login/registration after the shared OTP endpoints
 * (generate-otp2 / validate-otp2, in Auth\WhatsappOtpController) have
 * confirmed the mobile number. Mirrors WhatsappOtpController's
 * loginUser2/registerUser2 pattern, but against the `partner` guard and
 * gated by Partner::registration_status (Pending/Approved/Rejected).
 */
class PartnerAuthController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::guard('partner')->check()) {
            return response()->json(['status' => 'success']);
        }

        $mobile = $request->session()->get('Mobile');

        if (!$mobile) {
            return response()->json(['status' => 'error', 'message' => __('locale.Please verify your mobile number first.')], 422);
        }

        $partner = Partner::where('owner_mobile_no', $mobile)->first();

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
            $partner->mobile_verified_at = now();
            $partner->save();
        }

        if ((int) $partner->registration_status === 0) {
            return response()->json(['status' => 'pending', 'message' => __('locale.Your registration is pending approval.')], 403);
        }

        if ((int) $partner->registration_status === 2) {
            return response()->json(['status' => 'rejected', 'message' => __('locale.Your registration has been rejected.')], 403);
        }

        Auth::guard('partner')->login($partner);
        $request->session()->forget('Mobile');

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

        $exists = Partner::where('owner_mobile_no', $mobile)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? __('locale.An account with this mobile number already exists.') : null,
        ]);
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
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $mobile = $request->session()->get('Mobile');

        if (!$mobile) {
            return response()->json(['status' => 'error', 'message' => __('locale.Please verify your mobile number first.')], 422);
        }

        // Only an already-OTP-verified partner counts as a real duplicate -
        // a still-pending row for this same number (e.g. Resend OTP, or a
        // retry after abandoning an earlier attempt) is reused below
        // instead of being treated as a conflict.
        if (Partner::where('owner_mobile_no', $mobile)->whereNotNull('mobile_verified_at')->exists()) {
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

        $partner = Partner::where('owner_mobile_no', $mobile)->whereNull('mobile_verified_at')->first() ?? new Partner();
        $partner->rec_off_name = $request->company_name;
        $partner->owner_name = $googleData['name'] ?? $request->full_name;
        $partner->email = $googleData['email'] ?? $request->email;
        if ($googleData) {
            $partner->google_id = $googleData['google_id'];
        }
        $partner->owner_mobile_no = $mobile;
        $partner->user_type = '1';
        $partner->country_id = $country->id ?? null;
        $partner->status = 1;
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
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $mobile = $request->session()->get('Mobile');

        if (!$mobile) {
            return response()->json(['status' => 'error', 'message' => __('locale.Please verify your mobile number first.')], 422);
        }

        if (Partner::where('owner_mobile_no', $mobile)->whereNotNull('mobile_verified_at')->exists()) {
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

        $partner = Partner::where('owner_mobile_no', $mobile)->whereNull('mobile_verified_at')->first() ?? new Partner();
        $partner->rec_off_name = $request->company_name;
        $partner->owner_name = $googleData['name'] ?? $request->full_name;
        $partner->email = $googleData['email'] ?? $request->email;
        if ($googleData) {
            $partner->google_id = $googleData['google_id'];
        }
        $partner->owner_mobile_no = $mobile;
        $partner->user_type = '1';
        $partner->country_id = $country->id ?? null;
        $partner->status = 1;
        $partner->portal_status = 0;
        $partner->registration_status = 0;
        $partner->mobile_verified_at = now();
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

        $request->session()->forget(['Mobile', 'new_partner']);

        return response()->json([
            'status' => 'pending',
            'message' => __('locale.Thanks for registering! Your account is pending admin approval. We will notify you once approved.'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('partner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('worker.home');
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

        if (!$partner || (int) $partner->registration_status !== 1) {
            $message = $partner && (int) $partner->registration_status === 2
                ? 'Your registration has been rejected.'
                : 'Your registration is pending approval.';

            return redirect()->route('worker.home', ['login' => 1, 'auth_message' => $message]);
        }

        Auth::guard('partner')->login($partner);

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

        if (!$mobile) {
            return response()->json(['status' => 'error', 'message' => __('locale.Please verify your mobile number first.')], 422);
        }

        $existing = Partner::where('owner_mobile_no', $mobile)->where('id', '!=', $partner->id)->first();

        if ($existing) {
            return response()->json(['status' => 'error', 'message' => __('locale.An account with this mobile number already exists.')], 422);
        }

        $partner->owner_mobile_no = $mobile;
        $partner->mobile_verified_at = now();
        $partner->save();

        $request->session()->forget('Mobile');

        return response()->json(['status' => 'success']);
    }
}
