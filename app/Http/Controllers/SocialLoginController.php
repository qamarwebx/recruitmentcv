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

        // dd($this->configDriver()->stateless()->redirect());
        return $this->configDriver()->stateless()->redirect();
    }

    private function configDriver()
    {
        $authData = Socialmediaauth::where('type', 'google')->first();

        return Socialite::buildProvider(
            \Laravel\Socialite\Two\GoogleProvider::class,
            [
                'client_id'     => $authData->client_id,
                'client_secret' => $authData->client_secret,
                'redirect'      => route('google.callback'), // Dynamic & Safe
            ]
        );
    }

    /**
     * Entry point for "Continue with Google" on the Worker Partner login modal.
     * Reuses the exact same Socialite/Google provider config as redirectToGoogle()
     * above - only the redirect target differs, via a session flag the shared
     * callback (handleGoogleCallback) checks. This lives on qamarhire.com (not
     * worker.qamarhire.com) because Google's OAuth app only whitelists
     * https://qamarhire.com/auth/google/callback as a redirect URI; the
     * handoff back to a real session on worker.qamarhire.com happens via a
     * short-lived signed token (see handlePartnerGoogleCallback() and
     * Worker\PartnerAuthController::completeGoogleLogin()).
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
                return $this->handlePartnerGoogleCallback($googleUser);
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

            $user = User::updateOrCreate(
                ['email' => $googleUser->email], // check condition
                [
                    'name'              => $googleUser->name,
                    'google_id'         => $googleUser->id,
                    'password'          => \Hash::make('dummy-password'),
                    'avatar_url'        => $googleUser->avatar,
                    'email_verified_at' => now(),
                ]
            );  

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

            return redirect('/')
                ->with('errorMsg', 'Google login failed. Please try again.');
        }
    }

    /**
     * Applies the same Pending/Approved/Rejected rules as the phone+OTP
     * partner flow (Worker\PartnerAuthController). Runs on qamarhire.com
     * (where the OAuth callback lands), so it can't call
     * Auth::guard('partner')->login() directly - that session wouldn't be
     * visible on worker.qamarhire.com. Instead it issues a short-lived,
     * single-use handoff token that Worker\PartnerAuthController::
     * completeGoogleLogin() exchanges for a real session on that domain.
     *
     * A first-time email (no existing Partner row) does NOT create a
     * Partner here - it used to, which skipped Company Name/Mobile Number
     * entirely and left a bare, never-completable Pending row behind
     * (registration_status was already correctly 0/Pending, but nothing
     * ever collected a real mobile number or ran it through OTP). Instead
     * this mints a short-lived, server-verified handoff token carrying the
     * Google identity and sends the browser back to the Register as
     * Partner modal, pre-filled+locked from that token. The Partner row
     * itself is only created once Company Name + Mobile Number are
     * supplied and the mobile OTP is verified, through the exact same
     * Worker\PartnerAuthController::register() the existing mobile+OTP
     * registration flow already uses (see the `google_token` handling
     * there) - not a second, parallel creation path.
     */
    private function handlePartnerGoogleCallback($googleUser)
    {
        $partner = Partner::where('email', $googleUser->email)->first();

        if (!$partner) {
            $token = \Illuminate\Support\Str::random(48);
            \Illuminate\Support\Facades\Cache::put('partner_google_register:' . $token, [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
            ], now()->addMinutes(15));

            return redirect('https://worker.qamarhire.com?register=1&google_token=' . $token);
        }

        if (!$partner->google_id) {
            $partner->google_id = $googleUser->id;
            $partner->save();
        }

        if ((int) $partner->registration_status === 0) {
            return redirect('https://worker.qamarhire.com?login=1&auth_message=' . urlencode('Your registration is pending approval.'));
        }

        if ((int) $partner->registration_status === 2) {
            return redirect('https://worker.qamarhire.com?login=1&auth_message=' . urlencode('Your registration has been rejected.'));
        }

        $token = \Illuminate\Support\Str::random(48);
        \Illuminate\Support\Facades\Cache::put('partner_google_handoff:' . $token, $partner->id, now()->addSeconds(60));

        return redirect('https://worker.qamarhire.com/partner/google/complete?token=' . $token);
    }

}
