<?php

namespace App\Http\Controllers\Auth;

use App\AdminModel\Userwhatsappapi;
use App\Http\Controllers\Controller;
use App\Mail\SendUserDetailsAdmin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

    }

    // Login
    public function showLoginForm(){
      $pageConfigs = [
          'bodyClass' => "bg-full-screen-image",
          'blankPage' => true
      ];

      return view('/auth/login', [
          'pageConfigs' => $pageConfigs
      ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    

    public function username()
    {

        $login = request()->input('username');
        $field = filter_var($login,FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        request()->merge([$field => $login]);

        return $field;

        // return 'username';
    }

    protected function authenticated(Request $request, $user)
    {
        // check user is active
        if($user->status != '1'){
            Auth::logout();
            return redirect('/')->withErrors(['activeU' => 'Sorry your not longer here!']);
        }

        // if ($user->user_type == 1) {

        
        //     // Send details on Whatsapp
        //     $getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=','1')->first();
        //     $whmsg = "Dear Khursheed Khan \nYou are login in the below IP Address, if you are not please change the password,because someone try to login from your credentials.\nUsername: ".Auth::user()->username."\nIP Address: ".$request->ip()."\nKindly check";

        //     $url = $getAPI->text_message_url."?number=919004446665&type=text&message=".urlencode($whmsg)."&instance_id=".$getAPI->instance_key."&access_token=".$getAPI->api_key;
        //     $ch = curl_init();
        //     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //     curl_setopt($ch,CURLOPT_URL,$url);
        //     $result = curl_exec($ch);
        //     echo $result;
        //     curl_close($ch);

        //     // Send details on mail
        //     $data = [
        //         'username' => Auth::user()->username,
        //         'ip_address' => $request->ip()
        //     ];

        //     Mail::to('khan786info@gmail.com')->send(new SendUserDetailsAdmin($data));
        // }

    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/login');
    }
}
