<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Pageloginurl;
use App\Models\Partner;
use App\Models\Socialmediaauth;
use App\Models\User;
use App\Models\Websiteconfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\FacebookProvider;
use Laravel\Socialite\Two\GoogleProvider;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;
use App\Support\CustomerSite;

class SocialLoginController extends Controller
{
    public function redirectToFacebook(Request $request)
    {
        // return Socialite::driver('facebook')->redirect();

        $faceb = $this->configfbDriver($request);
        return $faceb->redirect();
    }

    public function arredirectToFacebook(Request $request){
        $faceb = $this->configfbDriver($request);
        return $faceb->redirect();
    }

    private function configfbDriver(Request $request,$domain='',$driver = 'facebook'){
        $authData = Socialmediaauth::where('type','=','facebook')->first();
        $confg['client_id'] = $authData->client_id;
        $confg['client_secret'] = $authData->client_secret;
        // $confg['redirect'] = env('GOOGLE_CALLBACK_URL');
        // $confg['redirect'] = url('auth/facebook/callback');
        $confg['redirect'] = $authData->callback_url;
        return Socialite::buildProvider(FacebookProvider::class,$confg);
    }

    public function handleFacebookCallback(Request $request,$domain='',$driver = 'facebook')
    {   
        
        try {
            $user = $this->configDriver($request)->stateless()->user();
            // $user = Socialite::driver('facebook')->user();
            $findUser = User::where('facebook_id','=',$user->id)->first();
            if ($findUser) {
                // If Email not verified

                if ($findUser->email_verified_at == '') {
                    $findUser->email_verified_at = date("Y-m-d H:i:s");
                    $findUser->save();
                }

                Auth::login($findUser);
                return redirect()->route('dashboard');
            } else {
                $newUser = new User();
                $newUser->name = $user->name;
                $newUser->email = $user->email;
                $newUser->facebook_id = $user->id;
                $newUser->password = encrypt('123456dummy');
                $newUser->email_verified_at = date("Y-m-d H:i:s");
                $newUser->save();
                Auth::login($newUser);
                return redirect()->route('dashboard');
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            // return redirect()->route('login');
        }
    }

    public function arhandleFacebookCallback(Request $request,$domain='',$driver = 'facebook'){
        try {
            $user = $this->configDriver($request)->stateless()->user();
            // $user = Socialite::driver('facebook')->user();
            $findUser = User::where('facebook_id','=',$user->id)->first();
            if ($findUser) {
                if ($findUser->email_verified_at == '') {
                    $findUser->email_verified_at = date("Y-m-d H:i:s");
                    $findUser->save();
                }
                Auth::login($findUser);
                return redirect()->route('ar.myorder');
            } else {
                $newUser = new User();
                $newUser->name = $user->name;
                $newUser->email = $user->email;
                $newUser->facebook_id = $user->id;
                $newUser->password = encrypt('123456dummy');
                $newUser->email_verified_at = date("Y-m-d H:i:s");
                $newUser->save();
                Auth::login($newUser);
                return redirect()->route('ar.myorder');
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            // return redirect()->route('login');
        }
    }

    public function redirectToGoogle(Request $request)
    {
        $revUrl = url()->previous();

        // Remove old session record
        Pageloginurl::where('token_id', $request->session()->get('_token'))->delete();

        // Save previous page
        Pageloginurl::create([
            'token_id' => $request->session()->get('_token'),
            'prev_url' => $revUrl,
        ]);

        // Save login status in session
        $request->session()->put('loginStatus2525', $request->loginStatus2525);

        // Google's callback always lands on the main domain, so remember
        // which website (partner subdomain or main) the customer started on.
        // The session cookie is shared across *.recruitmentcv.com.
        $request->session()->put('customer_google_site', CustomerSite::partnerId() ?? 'main');

        // dd($this->configDriver()->stateless()->redirect());
        return $this->configDriver()->stateless()->redirect();
    }

    private function configDriver()
    {
        // RecruitmentCV's OWN Google OAuth Client (config/services.php ->
        // .env GOOGLE_CLIENT_ID/GOOGLE_CLIENT_SECRET/GOOGLE_CALLBACK_URL) -
        // deliberately NOT the Socialmediaauth DB row the source qamarhire.com
        // codebase's copy of this same method reads, since that table is
        // shared across both projects' database and its Google row is
        // Worker's own client. Using it here would mean every Google login
        // on this domain - partner or general - authenticates against
        // Worker's OAuth Client instead of this project's own, which is
        // exactly the coupling this project needs to NOT have.
        //
        // GOOGLE_CALLBACK_URL is built in .env from "${APP_URL}auth/google/
        // callback" (string substitution at parse time, not route()), so
        // it's already a single fixed value regardless of which host
        // (recruitmentcv.com vs www.recruitmentcv.com) started the flow.
        return Socialite::buildProvider(
            \Laravel\Socialite\Two\GoogleProvider::class,
            [
                'client_id'     => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect'      => config('services.google.redirect'),
            ]
        );
    }

    /**
     * Entry point for "Continue with Google" on the RecruitmentCV Partner
     * login modal. This project's own install (recruitmentcv.com) runs the
     * ENTIRE OAuth round trip on its own domain - start, Google's redirect
     * back, and the callback below are all recruitmentcv.com requests, so
     * configDriver()'s route('google.callback') naturally generates
     * https://recruitmentcv.com/auth/google/callback and there is no
     * cross-domain handoff to design around (contrast with the source
     * qamarhire.com codebase, where this same method has to bounce through
     * a different domain because Google's OAuth app there only whitelists
     * the qamarhire.com apex callback - not relevant here since this
     * project's callback IS the domain the modal lives on).
     */
    public function redirectToGooglePartner(Request $request)
    {
        $request->session()->put('partner_google_intent', true);

        return $this->configDriver()->stateless()->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            // Get Google User
            $googleUser = $this->configDriver()->stateless()->user();

            if ($request->session()->pull('partner_google_intent')) {
                return $this->handlePartnerGoogleCallback($googleUser, $request);
            }
            // Find existing user
            // Get stored redirect URL
            $storedUrl = Pageloginurl::where('token_id', $request->session()->get('_token'))->first();
            $redirectUrl = $storedUrl->prev_url ?? url('/');
    
            $agent = new Agent();
            $ip = $request->ip();
    
            $last_login_from = [
                'device_type' => $agent->device() ?: 'Unknown',
                'browser'     => $agent->browser() ?: 'Unknown',
                'os'          => $agent->platform() ?: 'Unknown',
                'ip_address'  => $ip,
                'last_logged_at'   => Carbon::now()->toDateTimeString(),
            ];

            // The website the customer started on (session value set in
            // redirectToGoogle(), never from the request) - only recorded as
            // "registered via" for a brand-new customer. One customer
            // account works on every partner website (see CustomerSite), and
            // only CUSTOMER accounts (users) are looked up here - a Partner
            // with the same email is a separate account and never matters.
            $site = $request->session()->pull('customer_google_site', 'main');
            $sitePartnerId = $site === 'main' ? null : (int) $site;

            $googleAttributes = [
                'name'              => $googleUser->name,
                'google_id'         => $googleUser->id,
                'avatar_url'        => $googleUser->avatar,
                'email_verified_at' => now(),
            ];

            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Existing customer: link Google and log in. Their password
                // (if any, e.g. set on qamarhire.com) is left untouched.
                $user->fill($googleAttributes);
            } else {
                // New customer: existing Google registration, with an
                // unusable random password (Google-only sign-in).
                $user = new User($googleAttributes + ['password' => \Hash::make(\Illuminate\Support\Str::random(40))]);
                $user->email = $googleUser->email;
                $user->partner_id = $sitePartnerId;
            }

            $user->save();

            $user->last_login_from = json_encode($last_login_from);
            $user->save();
    
            // Login user
            Auth::login($user);
    
            // Clean stored URL
            if ($storedUrl) {
                $storedUrl->delete();
            }
    
            return redirect($redirectUrl);
    
        } catch (\Exception $e) {
            // Back to where the customer started, with the error shown in the
            // Login modal (customer-auth-modal reads customer_auth_error).
            \Illuminate\Support\Facades\Log::warning('Google sign-in failed', ['error' => $e->getMessage()]);
            $message = __('locale.Google sign-in could not be completed. Please try again.');

            // Partner attempt: back to the Partner Login modal (its existing
            // ?login=1&auth_message= prompt).
            if ($request->session()->pull('partner_google_intent')) {
                return redirect()->route('worker.partner.login.page', ['login' => 1, 'auth_message' => $message]);
            }

            // Customer attempt: back to the page they started on (the
            // failure can happen before $storedUrl above was read).
            $startUrl = Pageloginurl::where('token_id', $request->session()->get('_token'))->value('prev_url');

            return redirect($startUrl ?: url('/'))->with('customer_auth_error', $message);
        }
    }

    /**
     * Applies the same Pending/Approved/Rejected rules as the phone+OTP
     * partner flow (Worker\PartnerAuthController). Unlike the source
     * qamarhire.com codebase (where this same method has to hand off to a
     * *different* domain via a short-lived token, because that install's
     * OAuth callback runs on qamarhire.com but the Partner Portal lives on
     * worker.qamarhire.com), this project's callback and Partner Portal are
     * the SAME domain (recruitmentcv.com) - so an approved partner is
     * logged in directly, right here, no cross-domain handoff token needed.
     *
     * A first-time email (no existing Partner row) does NOT create a
     * Partner here - it used to, which skipped Company Name/Mobile Number
     * entirely and left a bare, never-completable Pending row behind
     * (registration_status was already correctly 0/Pending, but nothing
     * ever collected a real mobile number or ran it through OTP). Instead
     * this mints a short-lived, server-verified token carrying the Google
     * identity and sends the browser back to the Register as Partner modal,
     * pre-filled+locked from that token. The Partner row itself is only
     * created once Company Name + Mobile Number are supplied and the
     * mobile OTP is verified, through the exact same Worker\
     * PartnerAuthController::register() the existing mobile+OTP
     * registration flow already uses (see the `google_token` handling
     * there) - not a second, parallel creation path.
     */
    private function handlePartnerGoogleCallback($googleUser, Request $request)
    {
        $partner = Partner::where('email', $googleUser->email)->first();
        $partnerLoginPage = route('worker.partner.login.page');

        // Not a partner: a team member with this Google account / email
        // (created by its partner) signs in to that partner's portal.
        $member = !$partner
            ? (\App\Models\PartnerTeamMember::where('google_id', $googleUser->id)->first() ?? \App\Models\PartnerTeamMember::findByAccountIdentifier($googleUser->email))
            : null;
        if ($member) {
            if ($member->google_id && $member->google_id !== $googleUser->id) {
                return redirect($partnerLoginPage . '?login=1&auth_message=' . urlencode('This Google account is not linked to your team member account.'));
            }
            if (!$member->google_id) {
                $member->google_id = $googleUser->id;
                $member->save();
            }
            $result = app(\App\Http\Controllers\Worker\PartnerAuthController::class)->completeTeamMemberLogin($request, $member)->getData(true);

            return ($result['status'] ?? '') === 'success'
                ? redirect($result['redirect'])
                : redirect($partnerLoginPage . '?login=1&auth_message=' . urlencode($result['message'] ?? ''));
        }

        // A row whose mobile was never OTP-verified is only an abandoned
        // registration attempt (PartnerAuthController::registerPending()) -
        // let Google resume registration instead of reporting it as
        // "pending approval". registerPending()/register() reuse that same
        // row by mobile number, so no duplicate is created.
        if (!$partner || !$partner->mobile_verified_at) {
            $token = \Illuminate\Support\Str::random(48);
            \Illuminate\Support\Facades\Cache::put('partner_google_register:' . $token, [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
            ], now()->addMinutes(15));

            return redirect($partnerLoginPage . '?register=1&google_token=' . $token);
        }

        if (!$partner->google_id) {
            $partner->google_id = $googleUser->id;
            $partner->save();
        }

        // Pending partners sign in (Hire Now / Download CV wait for approval -
        // Partner::isRegistrationApproved()); a Rejected one can't.
        if ($partner->isRegistrationRejected()) {
            return redirect($partnerLoginPage . '?login=1&auth_message=' . urlencode('Your registration has been rejected.'));
        }

        Auth::guard('partner')->login($partner);

        // Google's OAuth callback always lands on the apex (see
        // configDriver()'s note above on why), so without this a partner
        // who started "Continue with Google" from their own subdomain
        // would otherwise end up back on the apex after completing it.
        // Partner::portalBaseUrl() sends them to their own configured
        // subdomain instead, same as the OTP login path.
        return redirect($partner->portalBaseUrl() . route('worker.partner.candidates', [], false));
    }

}
