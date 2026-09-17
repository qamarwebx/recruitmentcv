<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Userprofile;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Whatsappapi;
use App\Models\Templatecampaign;
use Illuminate\Support\Facades\Validator;

class WhatsappOtpController extends Controller
{
    public function index(Request $request)
    { 
        $user = Auth::user();
        $umobile = $user['mobile_verify_at'];      
        $countries = Country::orderBy('id','DESC')->get();  
        return view('admin.api-packages.index')->with('user', $user)->with('countries', $countries);
    }
    public function generateOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        
        $otp = mt_rand(1000, 9999); 
        $getApi = Whatsappapi::where('status','=',1)->where('api_for','=','booking_not')->first();
        
        $url_text = $getApi->api_url;
        $instance_id = $getApi->instance_id;
        $access_token = $getApi->access_token;
        $otpMessage1 = $getApi->notes;
        $mobile = $request->mobile;
        $countryCodeId = $request->countryCode;
        list($countryId, $countryCode) = explode(",", $countryCodeId);
    
        // Store data in session
        $request->session()->put('OTP', $otp);
        $request->session()->put('Mobile', $mobile);
        $request->session()->put('CountryId', $countryId);
        $request->session()->put('CountryCode', $countryCode);
    
        $phone = $request->session()->get('CountryCode').''.$mobile;
        $template = Templatecampaign::where('template_name','=','OTP Verification')->first();
        $otpMessage = "$template->msg_whatsapp: $otp";   

        if(isset($getApi)){
            $sendURL = $url_text . "?number=" . $phone . "&type=text&message=" . urlencode($otpMessage) . "&instance_id=" . $instance_id . "&access_token=" . $access_token;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $sendURL);
            $result = curl_exec($ch);
            curl_close($ch);
        }
        return response()->json(['otp' => $otp], 200);
    } 

    public function validateOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|numeric', 
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $enteredOtp = $request->input('otp');
        $OTP = $request->session()->get('OTP');
        if ($OTP == $enteredOtp) {
            return response()->json(['message' => 'OTP is valid.'], 200);
        } else {    
            return response()->json(['error' => 'Invalid OTP. Please try again.'], 422);
        }
    } 
 /*    public function loginUser(Request $request)
    {
        if (Auth::check()) {
            return response()->json(['message' => 'User is already logged in.'], 200);
        }
            $mobile = $request->session()->get('Mobile');
            //$user = User::where('mobile', $mobile)->first();
            $userData = Userprofile::where('mobile_no', $mobile)->get('id,user_id');
           $user = $userData->user_id;
        if ($user) {
            Auth::login($user);
            $request->session()->forget('Mobile');
            return response()->json(['message' => 'User logged in successfully.'], 200);
        } else {
            $request->session()->put('new_user', true);
            return response()->json(['status' => 'new_user'], 200);
        }
    } */
/* public function loginUser(Request $request)
{
    if (Auth::check()) {
        return response()->json(['message' => 'User is already logged in.'], 200);
    }
    
    $mobile = $request->session()->get('Mobile');
    $userProfile = Userprofile::where('mobile_no', $mobile)->first();
    
    if ($userProfile) {
        $user = User::find($userProfile->user_id);
        Auth::login($user);
        $request->session()->forget('Mobile');
        return response()->json(['message' => 'User logged in successfully.'], 200);
    } else {
        $request->session()->put('new_user', true);
        return response()->json(['status' => 'new_user'], 200);
    }
} */
public function loginUser(Request $request)
{
    if (Auth::check()) {
        return response()->json(['message' => 'User is already logged in.'], 200);
    }
    
    $mobile = $request->session()->get('Mobile');
    
    // Retrieve the user profile based on mobile
    $userProfile = Userprofile::where('mobile_no', $mobile)->first();
    
    if ($userProfile) {
        // Retrieve the user using the user_id from the user profile
        $user = User::find($userProfile->user_id);
        
        if ($user) {
            Auth::login($user);
            $request->session()->forget('Mobile');
            return response()->json(['message' => 'User logged in successfully.'], 200);
        }
    }
    
    $request->session()->put('new_user', true);
    return response()->json(['status' => 'new_user'], 200);
}


public function registerUser(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
    ]);

    // Create a new user
    $user = new User();
    $user->name = $validatedData['name'];
    $user->status = '1';
    $user->save();

    // Retrieve the user's ID
    $userId = $user->id;

    // Create a new user profile
    $userProfile = new Userprofile();
    $userProfile->user_id = $userId;
    $userProfile->mobile_no = $request->session()->get('Mobile');
    $userProfile->country_id = $request->session()->get('CountryId');
    $userProfile->status = '1';
    $userProfile->save();

    // Log in the user
    Auth::login($user);

    return response()->json(['message' => 'User registered and logged in successfully.'], 200);
}
}