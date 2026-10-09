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
        // Started on a partner's Own Domain: its session isn't recruitmentcv.com's
        // (where Google returns), so the round trip runs there, carrying the
        // validated domain + the page to return to, signed and short-lived;
        // the sign-in is then handed back (completeCustomerGoogleLogin()).
        $site = app()->bound('currentPartner') ? app('currentPartner') : null;
        $root = \App\Support\PartnerDomains::root();
        $host = preg_replace('/^www\./', '', strtolower($request->getHost()));
        if ($site && !str_ends_with($host, '.' . $root) && $site->domain && \App\Support\PartnerDomains::customServes($site->domain)) {
            $previous = url()->previous();
            $path = strcasecmp((string) parse_url($previous, PHP_URL_HOST), $request->getHost()) === 0 ? (parse_url($previous, PHP_URL_PATH) ?: '/') : '/';
            $payload = encrypt(['domain_id' => $site->domain->id, 'host' => $site->domain->custom_domain, 'path' => $path, 'exp' => now()->addMinutes(10)->timestamp]);

            return redirect()->away('https://' . $root . route('googleLogin', ['site' => $payload, 'loginStatus2525' => $request->loginStatus2525], false));
        }
        // A customer sign-in: no leftover marker of an abandoned partner attempt applies.
        $request->session()->forget(['customer_google_handoff', 'partner_google_intent', 'partner_google_site', 'partner_google_return']);
        if (!$site && $request->filled('site')) {
            $handoff = self::ownDomainSite((string) $request->query('site'));
            if (!$handoff) {
                return redirect()->route('worker.home');
            }
            $request->session()->put('customer_google_handoff', $handoff);
            $request->session()->put('loginStatus2525', $request->loginStatus2525);
            $request->session()->put('customer_google_site', $handoff['partner_id']);

            return $this->configDriver()->stateless()->redirect();
        }

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
        $root = \App\Support\PartnerDomains::root();
        $host = strtolower($request->getHost());
        $site = app()->bound('currentPartner') ? app('currentPartner') : null;
        $onPartnerSubdomain = $site && str_ends_with(preg_replace('/^www\./', '', $host), '.' . $root);

        // Started on a partner's Own Domain: Google only returns to
        // recruitmentcv.com, whose session that domain doesn't share - so the
        // whole round trip runs there, carrying which (validated) domain to
        // hand the sign-in back to, signed and short-lived.
        if ($site && !$onPartnerSubdomain) {
            $domain = $site->domain;
            if ($domain && \App\Support\PartnerDomains::customServes($domain)) {
                $payload = encrypt(['domain_id' => $domain->id, 'host' => $domain->custom_domain, 'exp' => now()->addMinutes(10)->timestamp]);

                return redirect()->away('https://' . $root . route('partner.google.start', ['site' => $payload], false));
            }
        }

        $request->session()->forget(['partner_google_site', 'partner_google_return', 'customer_google_handoff']);
        if ($request->filled('site') && !$site) {
            // Back on recruitmentcv.com from an Own Domain (above): accepted only
            // signed, unexpired, and for a domain that still serves its partner.
            try {
                $data = decrypt((string) $request->query('site'));
                $domain = is_array($data) && ($data['exp'] ?? 0) >= now()->timestamp ? \App\Models\Domain::find($data['domain_id'] ?? 0) : null;
                if ($domain && \App\Support\PartnerDomains::customServes($domain) && $domain->custom_domain === ($data['host'] ?? null)) {
                    $request->session()->put('partner_google_site', $domain->id);
                }
            } catch (\Throwable $e) {
                // Tampered / old value: an ordinary sign-in.
            }
        } elseif ($onPartnerSubdomain) {
            // Partner subdomain (same session as recruitmentcv.com): come back here.
            $request->session()->put('partner_google_return', $host);
        }

        $request->session()->put('partner_google_intent', true);

        return $this->configDriver()->stateless()->redirect();
    }

    /**
     * The signed "site" value of a Google sign-in started on an Own Domain
     * (redirectToGoogle()): ['domain_id', 'host', 'path', 'partner_id'] when
     * valid, unexpired and the domain still serves its partner; else null.
     */
    private static function ownDomainSite(string $value): ?array
    {
        try {
            $data = decrypt($value);
        } catch (\Throwable $e) {
            return null;
        }
        if (!is_array($data) || ($data['exp'] ?? 0) < now()->timestamp) {
            return null;
        }
        $domain = \App\Models\Domain::find($data['domain_id'] ?? 0);
        if (!$domain || !\App\Support\PartnerDomains::customServes($domain) || $domain->custom_domain !== ($data['host'] ?? null)) {
            return null;
        }
        $path = (string) ($data['path'] ?? '/');
        if (!str_starts_with($path, '/') || str_starts_with($path, '//') || str_contains($path, '\\')) {
            $path = '/';
        }

        return ['domain_id' => $domain->id, 'host' => $domain->custom_domain, 'path' => $path, 'partner_id' => (int) $domain->partner_id];
    }

    /**
     * GET /auth/google/complete on an Own Domain: exchanges the single-use
     * token minted on recruitmentcv.com for a customer session here. Only
     * on the domain it was issued for, which must still serve its partner.
     */
    public function completeCustomerGoogleLogin(Request $request)
    {
        $token = (string) $request->query('token');
        $data = $token !== '' ? \Illuminate\Support\Facades\Cache::pull('customer_google_handoff:' . $token) : null;
        $site = app()->bound('currentPartner') ? app('currentPartner') : null;
        $host = preg_replace('/^www\./', '', strtolower($request->getHost()));
        $user = is_array($data) ? User::find($data['user_id'] ?? 0) : null;

        if (!$user || !$site || $host !== ($data['host'] ?? null)) {
            return redirect('/')->with('customer_auth_error', __('locale.Google sign-in could not be completed. Please try again.'));
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect($data['path'] ?? '/');
    }

    /**
     * Where a Google sign-in of $partnerId (team member: its partner) ends:
     * the website it was started on when that is this partner's own live
     * address, else the partner's primary address, else the main site.
     * ['host' => ?string, 'custom' => bool] - custom = an Own Domain, which
     * needs the single-use handoff (its session is not recruitmentcv.com's).
     */
    private function googleTarget(Request $request, int $partnerId, ?int $originDomainId, ?string $originHost): array
    {
        $domain = \App\Models\Domain::where('partner_id', $partnerId)->first();
        $hosts = \App\Support\PartnerDomains::hosts($domain);

        if ($originDomainId && $domain && (int) $domain->id === $originDomainId && \App\Support\PartnerDomains::customServes($domain)) {
            return ['host' => $domain->custom_domain, 'custom' => true];
        }
        if ($originHost && in_array(preg_replace('/^www\./', '', $originHost), $hosts, true)) {
            return ['host' => $originHost, 'custom' => false];
        }
        $primary = \App\Support\PartnerDomains::primaryHost($domain);

        return ['host' => $primary, 'custom' => $primary !== null && \App\Support\PartnerDomains::customServes($domain) && $primary === $domain->custom_domain];
    }

    /** Single-use handoff of a verified Google sign-in to an Own Domain (PartnerAuthController::completeGoogleLogin). */
    private function handoffToOwnDomain(string $host, int $partnerId, ?int $teamMemberId = null)
    {
        $token = \Illuminate\Support\Str::random(64);
        \Illuminate\Support\Facades\Cache::put('partner_google_handoff:' . $token, ['partner_id' => $partnerId, 'team_member_id' => $teamMemberId, 'host' => $host], now()->addMinutes(2));

        return redirect()->away('https://' . $host . route('worker.partner.google.complete', ['token' => $token], false));
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

            // Started on an Own Domain: signed in there, through a single-use token.
            $handoff = $request->session()->pull('customer_google_handoff');
            if (is_array($handoff) && !empty($handoff['host'])) {
                $token = \Illuminate\Support\Str::random(64);
                \Illuminate\Support\Facades\Cache::put('customer_google_handoff:' . $token, ['user_id' => $user->id, 'host' => $handoff['host'], 'path' => $handoff['path']], now()->addMinutes(2));

                return redirect()->away('https://' . $handoff['host'] . route('customer.google.complete', ['token' => $token], false));
            }
    
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
        // The website the sign-in started on (set in redirectToGooglePartner()).
        $originDomainId = (int) $request->session()->pull('partner_google_site') ?: null;
        $originHost = $request->session()->pull('partner_google_return');
        $originDomain = $originDomainId ? \App\Models\Domain::find($originDomainId) : null;
        $partnerLoginPage = $originDomain && \App\Support\PartnerDomains::customServes($originDomain)
            ? 'https://' . $originDomain->custom_domain . route('worker.partner.login.page', [], false)
            : route('worker.partner.login.page');

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
            $target = $this->googleTarget($request, (int) $member->partner_id, $originDomainId, $originHost);
            if ($target['custom']) {
                // Same checks as a sign-in here, then the handoff signs in on the Own Domain.
                $refusal = app(\App\Http\Controllers\Worker\PartnerAuthController::class)->teamMemberLoginRefusal($member);
                if ($refusal) {
                    return redirect($partnerLoginPage . '?login=1&auth_message=' . urlencode($refusal));
                }

                return $this->handoffToOwnDomain($target['host'], (int) $member->partner_id, (int) $member->id);
            }
            $result = app(\App\Http\Controllers\Worker\PartnerAuthController::class)->completeTeamMemberLogin($request, $member)->getData(true);
            if (($result['status'] ?? '') === 'success' && $target['host']) {
                $result['redirect'] = 'https://' . $target['host'] . (parse_url($result['redirect'], PHP_URL_PATH) ?: '/');
            }

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
                // Google's own "email verified" - register() then marks the
                // email verified (App\Support\GoogleEmailVerification).
                'email_verified' => \App\Support\GoogleEmailVerification::claimed($googleUser),
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
        // Subdomain removed in CRM: RecruitmentCV portal access revoked.
        if ($partner->isPortalRevoked()) {
            return redirect($partnerLoginPage . '?login=1&auth_message=' . urlencode(__('locale.Your RecruitmentCV portal is currently unavailable. Please contact support.')));
        }

        // Signed in with Google using this partner's own email (verified by
        // Google): that proves the address - mark it verified if it isn't
        // yet (never un-verified). App\Support\GoogleEmailVerification.
        if (\App\Support\GoogleEmailVerification::apply($partner, $googleUser->email, \App\Support\GoogleEmailVerification::claimed($googleUser))) {
            $partner->save();
        }

        // Google's OAuth callback always lands on the apex (see
        // configDriver()'s note above on why): back to the website the
        // sign-in started on when it is this partner's own address, else the
        // partner's primary one. An Own Domain gets the single-use handoff
        // (signs in there); recruitmentcv.com and its subdomains share this
        // session, so the sign-in happens here.
        $target = $this->googleTarget($request, (int) $partner->id, $originDomainId, $originHost);
        if ($target['custom']) {
            return $this->handoffToOwnDomain($target['host'], (int) $partner->id);
        }

        Auth::guard('partner')->login($partner);

        return redirect(($target['host'] ? 'https://' . $target['host'] : rtrim(config('app.url'), '/')) . route('worker.partner.candidates', [], false));
    }

}
