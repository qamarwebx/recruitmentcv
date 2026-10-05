<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Booking;
use App\Models\Candidate;
use App\Models\City;
use App\Models\Country;
use App\Models\User;
use App\Models\Userprofile;
use App\Models\Websiteconfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Basepathstatus;
use App\Models\Whatsappapi;
use App\Models\Templatecampaign;
use App\Jobs\CancelBookingOrder;
use App\Mail\SendOTPVerification;
use App\Models\OrderStatus;
use App\Models\Profession;
use Illuminate\Support\Facades\Mail;
use App\Models\Metanotification;
use App\Models\Metawhatsapplog;
use App\Models\Normalwhatsapplogs;
use App\Models\Metawhatsappapi;
use App\Models\Partner;
use App\Models\Staticmetanotification;
use App\Models\Otpvalidationmetalist;

class DashboardController extends Controller
{

    public function index()
    {
        return view('dashboard2');
    }

    public function myorder()
    {
    //   $posts = Booking::where('user_id','=',Auth::user()->id)->get();
      $ordstatuses = OrderStatus::where('admin_id','=','2')->get();
      $professions= Profession::all();
      // $ordstatuses = OrderStatus::where('admin_id','=',Auth::guard('admin')->user()->id)->get();
      $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
      $posts = DB::table('bookings as booking')
            ->leftJoin('candidates as cand','booking.cand_id','=','cand.id')
            ->leftJoin('bookingorderstatuses as orderstatus','booking.id','=','orderstatus.booking_id')
            ->select('booking.*','cand.cand_name','cand.experience','cand.exp_sal','cand.address','cand.jobtype_id','cand.photo_file','cand.slug_text','cand.cv_execute_file','cand.cv_execute','cand.pass_no')
            ->where('booking.user_id','=',Auth::user()->id)
            ->where('booking.booking_status','!=',2)
            // Only the orders this site may show (CustomerSite::whereOrderVisible()).
            ->where(fn ($visible) => \App\Support\CustomerSite::whereOrderVisible($visible, 'booking.partner_id'))
            ->get();



        return view('user.myorder',compact('posts','ordstatuses', 'professions','frontwebsite'));
    }

    public function armyorder(){
        $ordstatuses = OrderStatus::where('admin_id','=','2')->get();
        $countries = Country::orderBy('id','DESC')->get();
        $professions= Profession::all();
        $posts = DB::table('bookings as booking')
            ->leftJoin('candidates as cand','booking.cand_id','=','cand.id')
            ->leftJoin('professions as proff','proff.id','=','cand.jobtype_id')
            ->leftJoin('bookingorderstatuses as orderstatus','booking.id','=','orderstatus.booking_id')
            ->select('booking.*','cand.cand_name','cand.experience','cand.exp_sal','cand.address','cand.jobtype_id','cand.photo_file','proff.ar_name as arname','cand.slug_text','cand.arcand_name','cand.cv_execute_file','cand.cv_execute','cand.pass_no')
            ->where('booking.user_id','=',Auth::user()->id)
            ->where('booking.booking_status','!=',2)
            // Only the orders this site may show (CustomerSite::whereOrderVisible()).
            ->where(fn ($visible) => \App\Support\CustomerSite::whereOrderVisible($visible, 'booking.partner_id'))
            ->get();

        return view('user.arabic.myorder',compact('posts','ordstatuses','countries', 'professions'));
    }

    public function getDetails($id)
    {
        // Only the logged-in customer's own booking, and only one this site
        // may show (CustomerSite::whereOrderVisible()).
        $booking = Booking::where('id', $id)->where('user_id', Auth::id())
            ->where(fn ($visible) => \App\Support\CustomerSite::whereOrderVisible($visible, 'bookings.partner_id'))
            ->first();

        if (!$booking) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $booking->formatted_created_at = $booking->created_at
            ? $booking->created_at->format('d M Y, h:i A')
            : null;

        return response()->json($booking);
    }

    public function armyprofile(){
        $post = DB::table('users as user')
            // ->leftJoin('userprofiles as profile','user.id','=','profile.user_id')
            ->leftJoin('countries as country','user.country_id','=','country.id')
            ->leftJoin('cities as city','user.city_id','=','city.id')
            ->select('user.*','country.name as conname','country.country_code as contcode','country.iso_code as conisocode','city.name as citname')
            ->where('user.id','=',Auth::user()->id)
            ->first();

        // dd($post);

        $countries = Country::orderBy('id','DESC')->get();
        $cities = City::orderBy('id','DESC')->get();

        return view('user.arabic.myprofile',compact('post','countries','cities'));
    }

    public function myprofile()
    {
        $post = DB::table('users as user')
            // ->leftJoin('userprofiles as profile','user.id','=','profile.user_id')
            ->leftJoin('countries as country','user.country_id','=','country.id')
            ->leftJoin('cities as city','user.city_id','=','city.id')
            // ->select('user.name as uname','user.email as uemail','user.status as ustatus','user.country_id','user.city_id','user.mobile_no','user.photo','country.name as conname','city.name as citname')
            ->select('user.*','country.name as conname','country.country_code as contcode','country.iso_code as conisocode','city.name as citname')
            ->where('user.id','=',Auth::user()->id)
            ->first();

        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();

        // dd($post);

        $countries = Country::orderBy('id','DESC')->get();
        $cities = City::orderBy('id','DESC')->get();

        return view('user.myprofile',compact('post','countries','cities','frontwebsite'));
    }


    public function updateMyprofile(Request $request)
    {
        // Always the logged-in customer - never an id from the request
        // (previously User::find($request->user_id) let any customer edit
        // any account).
        $user = Auth::user();
        $basepathstatus = Basepathstatus::first();
        $country = Country::find($request->country_id);

        // Images only, and the stored extension comes from the detected
        // file type - never the client's filename (a .php "photo" would have
        // been saved into a public folder).
        $request->validate([
            'photo' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        // Upload Files
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $new_file1 = time().'_'.\Illuminate\Support\Str::random(10).'.'.$file->extension();
            if($basepathstatus->base_path_status == 1){
                $file->move(base_path().'/public/user/img/avatars',$new_file1);
            }else{
                $file->move(base_path().'/public_html/user/img/avatars',$new_file1);
            }
            $photo = $new_file1;
        }else{
            // Keep the current photo when none is uploaded.
            $photo = $user->photo;
        }

        // Update data and change status of user in User table
        $user->name = $request->name;
        // mobile_no is NOT changed here - only through the OTP flow
        // (getMobileOTPandUpdate + getMobileOTPandUpdateValidate), so an
        // unverified number can never be attached to an account.
        $user->company_name = $request->company_name;
        $user->address = $request->address;
        $user->country_id = $request->country_id;
        $user->country_code = $country->country_code ?? null;
        $user->city_id = $request->city_id;
        $user->photo = $photo;
        // if ($request->mobile_no != '') {
        //     $user->status = true;
        // } else {
        //     $user->status = false;
        // }

        $user->save();


        // Check profile details exists or not
        // $chkProfile = Userprofile::where('user_id','=',$id)->first();


        // if (isset($chkProfile)) {

        //     // Upload Files
        //     if ($request->hasFile('photo')) {
        //         $file = $request->file('photo');
        //         $name = time().'_'.$file->getClientOriginalName();
        //         // remove space from image
        //         $filename_ren = pathinfo($name,PATHINFO_FILENAME);
        //         $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
        //         $repspfilename = str_replace(" ","_",$filename_ren);
        //         $new_file1 = $repspfilename.'.'.$fileext_ren;
        //         if($basepathstatus->base_path_status == 1){
	    //             $file->move(base_path().'/public/user/img/avatars',$new_file1);
        //         }else{
        //             $file->move(base_path().'/public_html/user/img/avatars',$new_file1);
        //         }
        //         $photo = $new_file1;
        //     }else{
        //         $photo = $chkProfile->photo;
        //     }

        //     // Update Profile
        //     $chkProfile->mobile_no = $request->mobile_no;
        //     $chkProfile->company_name = $request->company_name;
        //     $chkProfile->address = $request->address;
        //     $chkProfile->country_id = $request->country_id;
        //     $chkProfile->city_id = $request->city_id;
        //     $chkProfile->photo = $photo;
        //     // change profile status
        //     if ($request->mobile_no != '') {
        //         $chkProfile->status = true;
        //     }else{
        //         $chkProfile->status = false;
        //     }
        //     $chkProfile->save();

        // }else{
        //     // Upload Files
        //     if ($request->hasFile('photo')) {
        //         $file = $request->file('photo');
        //         $name = time().'_'.$file->getClientOriginalName();
        //         // remove space from image
        //         $filename_ren = pathinfo($name,PATHINFO_FILENAME);
        //         $fileext_ren = pathinfo($name,PATHINFO_EXTENSION);
        //         $repspfilename = str_replace(" ","_",$filename_ren);
        //         $new_file1 = $repspfilename.'.'.$fileext_ren;
        //         if($basepathstatus->base_path_status == 1){
	    //             $file->move(base_path().'/public/user/img/avatars',$new_file1);
        //         }else{
        //             $file->move(base_path().'/public_html/user/img/avatars',$new_file1);
        //         }
        //         $photo = $new_file1;
        //     }else{
        //         $photo = '';
        //     }

        //     // Create Profile
        //     $postProfile = new Userprofile();
        //     $postProfile->user_id = $id;
        //     $postProfile->mobile_no = $request->mobile_no;
        //     $postProfile->company_name = $request->company_name;
        //     $postProfile->address = $request->address;
        //     $postProfile->country_id = $request->country_id;
        //     $postProfile->city_id = $request->city_id;
        //     $postProfile->photo = $photo;
        //     if($request->mobile_no != ''){
        //         $postProfile->status = true;
        //     }else{
        //         $postProfile->status = false;
        //     }
        //     $postProfile->save();
        // }

        return redirect()->back()->with('success','Profile updated!');

    }


    // GET Mobile OTP
    public function getOTPFORVER(Request $request){
        $oldPhone = $request->oldPhone;
        $newPhone = $request->mobile_no;
        $page = $request->page;
        if($oldPhone != $newPhone){

            // Send OTP to the given number
            // Get Meta Template Details
            if($page == 'english'){
                $metanotification = Metanotification::where('meta_template_name','=','otpone')->first();
                $template_language = "en";
            }else{
                $metanotification = Metanotification::where('meta_template_name','=','otp')->first();
                // $template_language = "ar";
                $template_language = "en";
            }

            // GET Meta API
            // $getMetaAPI = Metawhatsappapi::where('status','=',1)->first();

            $getMetaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['otp'])->first();

            if(isset($otpTemplate) && isset($metaAPI)){
                // Generate OTP
                $otp = mt_rand(1000, 9999);


                $request->session()->put('OTP', $otp);
                $request->session()->put('Mobile', $newPhone);

                // API Details
                $base_url = $metaAPI->api_base_url;
                $vendor_id = $metaAPI->vendor_uid;
                $access_token = $metaAPI->api_access_token;
                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';

                $token = "Authorization: Bearer ".$access_token;


                // Send OTP to Mobile Number
                $data = [];
                $data['phone_number'] = $newPhone;
                $data['template_name'] = $metanotification['meta_template_name'];
                $data['template_language'] = $template_language;
                $data['field_1'] = $otp;
                $data['button_0'] = $otp;
                $data['contact'] =  [
                    'first_name' => "Customer",
                    'last_name' => "--",
                    "email" => "customer@gmail.com",
                    "country" => "SAUDI",
                    "language_code" => $template_language
                ];

                // Send Campaign Message
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                    CURLOPT_URL => $endpoint_api,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($data)
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                $responseGet = json_decode($response);

                // Store meta notification logs into database
                $metaWhatsappLog = new Metawhatsapplog();
                $metaWhatsappLog->message_for = "OTP";
                $metaWhatsappLog->message_type = "OTP Template";
                $metaWhatsappLog->template_name = $metanotification['meta_template_name'];

                if (isset($responseGet->errors) || $responseGet->result == 'failed') {

                    $metaWhatsappLog->message_text = $responseGet->message;
                    if(isset($responseGet->errors)){
                        $metaWhatsappLog->message_status = "failed";
                    }else{
                        $metaWhatsappLog->message_status = $responseGet->result;
                    }

                    $respData = [
                        'respStatus' => 0
                    ];
                }else{

                    $metaWhatsappLog->message_status = $responseGet->result;
                    $metaWhatsappLog->message_text = $responseGet->message;

                    $respData = [
                        'resmessage' => 'Otp send to your mobile no..',
                        'respStatus' => 1
                    ];
                }



                $metaWhatsappLog->save();

            }else{
                $respData = [
                    'respStatus' => 0
                ];
            }


        }else{
            $respData = [
                'respStatus' => 0
            ];
        }


        return response()->json($respData);
    }

    public function getOTPFORVER2(Request $request){
        $mobile = $request->mobile;
        $country_code = $request->country_code;
        $page = $request->page;

        $phone_no = $country_code.''.$mobile;

        // GET Country Code
        $getCountry = Country::where('country_code','=',$country_code)->first();

        // Get Meta Whatsapp API and Meta Template
        // $metaAPI = Metawhatsappapi::where('status','=',1)->first();
        $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['recruitmentcv_otp'])->first();
        if($page == 'en'){
            // $otpTemplate = Metanotification::where('meta_template_name','=','otpone')->first();
            $otpTemplate = Metanotification::where('meta_template_name','=','otp')->first();
            $templateLang = "en";
        }else{
            $otpTemplate = Metanotification::where('meta_template_name','=','otp')->first();
            $templateLang = "ar";
        }

        // Check Mobile No is already exist exist
        $checkMob = User::where('mobile_no','=',$mobile)->where('id','!=',Auth::user()->id)->count();

        // Checked Given is already Verified
        $checkVerMob = User::where('mobile_no','=',$mobile)->where('id','=',Auth::user()->id)->where('mobile_verified_at','!=','')->where('country_id','=',$getCountry->id)->count();

        if(empty($country_code)){
            $respData = [
                'status' => 'error',
                'message' => "Select your country!"
            ];
        }elseif($checkMob > 0){
            $respData = [
                'status' => 'error',
                'message' => "Mobile No is already Exists!"
            ];
        }elseif($checkVerMob != 0){
            $respData = [
                'status' => 'error',
                'message' => "Mobile No is already Verified!"
            ];
        }else{

            // dump($page);
            // dump($metaAPI);
            // dd($otpTemplate);

            if(isset($metaAPI) && isset($otpTemplate)){
                // Generate OTP
                $otp = mt_rand(1000, 9999);

                // Store data in Session
                $request->session()->put('OTP', $otp);
                $request->session()->put('Mobile', $mobile);
                $request->session()->put('CountryId', $getCountry->id);
                $request->session()->put('CountryCode', $country_code);


                // API Details
                $base_url = $metaAPI->api_base_url;
                $vendor_id = $metaAPI->vendor_uid;
                $access_token = $metaAPI->api_access_token;
                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                $token = "Authorization: Bearer ".$access_token;

                // Send OTP to Mobile Number
                $data = [];
                $data['phone_number'] = $phone_no;
                $data['template_name'] = $otpTemplate['meta_template_name'];
                $data['template_language'] = $templateLang;
                $data['field_1'] = $otp;
                $data['button_0'] = $otp;
                $data['contact'] =  [
                    'first_name' => "Customer",
                    'last_name' => "--",
                    "email" => "customer@gmail.com",
                    "country" => $getCountry->name,
                    "language_code" => $templateLang
                ];

                // Send Campaign Message
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                    CURLOPT_URL => $endpoint_api,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($data)
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                $responseGet = json_decode($response);

                // Store meta notification logs into database
                $metaWhatsappLog = new Metawhatsapplog();
                $metaWhatsappLog->message_for = "OTP";
                $metaWhatsappLog->message_type = "OTP Template";
                $metaWhatsappLog->template_name = $otpTemplate['meta_template_name'];

                if (isset($responseGet->errors) || $responseGet->result == 'failed') {
                    $metaWhatsappLog->message_text = $responseGet->message;
                    if(isset($responseGet->errors)){
                        $metaWhatsappLog->message_status = "failed";
                    }else{
                        $metaWhatsappLog->message_status = $responseGet->result;
                    }

                    $respData = [
                        'status' => 'error',
                        'message' => "OTP not send!,please try after sometime.",
                        'errormsg' => $responseGet
                    ];

                }else{
                    $metaWhatsappLog->message_status = $responseGet->result;
                    $metaWhatsappLog->message_text = $responseGet->message;

                    $respData = [
                        'status' => 'success',
                        'message' => "OTP has been sent!",
                        'mobile' => $mobile,
                        'countryCode' => $country_code,
                        'iso_code' => $getCountry->iso_code
                    ];
                }

                $metaWhatsappLog->save();

            }else{
                $respData = [
                    'status' => 'error',
                    'message' => "OTP not send!,please try after sometime.",
                    'errormsg' => $responseGet
                ];
            }


        }

        return response()->json($respData);

    }

    public function getOTPValidation(Request $request){
        if (\App\Support\OtpVerification::check($request, $request->otp)) {
            \App\Support\OtpVerification::consume($request);
            $mobile = $request->session()->get('Mobile');
            $country_id = $request->session()->get('CountryId');

            $country = Country::find($country_id);

            // Get USER Details
            $user = User::where('id','=',Auth::user()->id)->first();

            $user->mobile_no = $mobile;
            $user->country_id = $country_id;
            $user->country_code = $country->country_code;
            $user->mobile_verified_at = date('Y-m-d H:i:s');
            $user->save();

            $respData = [
                'status' => 'success',
                'message' => 'Mobile No has been verified'
            ];
        }else{
            $respData = [
                'status' => 'error',
                'message' => 'OTP not match!'
            ];
        }

        Auth::user()->refresh();

        return response()->json($respData);

    }


    public function getEmailOTPandUpdate(Request $request){
        $email = trim((string) $request->email);
        // users.email is unique - reject a taken/invalid address before
        // sending anything (saving it later would just fail).
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['message' => __('locale.Please enter a valid email address.')], 422);
        }
        if (User::where('email', $email)->where('id', '!=', Auth::id())->exists()) {
            return response()->json(['message' => __('locale.An account with this email already exists.')], 422);
        }
        $userData = User::find(Auth::user()->id);
        // Generate OTP For Email
        $otp = mt_rand(1000,9999);
        $data = [];

        // Store Request and OTP in Session
        $request->session()->put('EmailOtp',$otp);
        $request->session()->put('emaiAddr',$email);

        $data['name'] = $userData->name;
        // The code is only sent by email and checked server-side - it is no
        // longer returned in this response.
        $data['email'] = $email;

        // Send OTP to Email

        // The code goes in the email only (never the JSON response below).
        Mail::to($email)->send(new SendOTPVerification($data + ['EmailOtp' => $otp]));





        return response()->json($data);

    }

    /**
     * "Email changed" confirmation to the NEW address - only called after
     * the OTP matched and the new email was saved, and only when the
     * address actually changed (re-verifying the same one isn't a change).
     * The OTP is single use, so a repeated request never gets here twice.
     * Through the current site partner's SMTP (App\Support\CustomerMail).
     */
    private function sendEmailChangedMail(User $user, ?string $oldEmail): void
    {
        if (strcasecmp(trim((string) $oldEmail), trim((string) $user->email)) === 0) {
            return;
        }

        $partnerId = \App\Support\CustomerSite::partnerId();

        \App\Support\CustomerMail::send($partnerId, $user->email, new \App\Mail\CustomerEmailChanged([
            'brand' => \App\Support\CustomerMail::brand($partnerId),
            'customer_name' => $user->name,
            'new_email' => $user->email,
            'changed_at' => now()->format('d M Y, h:i A') . ' (GMT' . now()->format('P') . ')',
            'account_url' => route('worker.account.profile'),
        ]));
    }

    /** The code sent by getEmailOTPandUpdate(), for that same email. Single use. */
    private function emailOtpMatches(Request $request): bool
    {
        $expected = (string) $request->session()->get('EmailOtp');
        $expectedEmail = (string) $request->session()->get('emaiAddr');
        $ok = $expected !== ''
            && hash_equals($expected, trim((string) $request->otp))
            && $expectedEmail !== ''
            && strcasecmp($expectedEmail, trim((string) $request->email)) === 0;

        if ($ok) {
            $request->session()->forget(['EmailOtp', 'emaiAddr']);
        }

        return $ok;
    }

    public function getEmailOTPandUpdateValidate(Request $request){
        if (!$this->emailOtpMatches($request)) {
            return response()->json(['message' => 'Invalid OTP. Please try again.'], 422);
        }
        $email = $request->email;
        $userData = User::find(Auth::user()->id);
        $oldEmail = $userData->email;
        $userData->email = $email;
        $userData->email_verified_at = date('Y-m-d H:i:s');
        $userData->status = true;
        $userData->save();
        $this->sendEmailChangedMail($userData, $oldEmail);
        return response()->json(['message' => 'Email Verified Successfully!']);
    }

    public function getEmailOTPandUpdateValidateAr(Request $request){
        if (!$this->emailOtpMatches($request)) {
            return response()->json(['message' => 'رمز التحقق غير صحيح. يرجى المحاولة مرة أخرى.'], 422);
        }
        $email = $request->email;
        $userData = User::find(Auth::user()->id);
        $oldEmail = $userData->email;
        $userData->email = $email;
        $userData->email_verified_at = date('Y-m-d H:i:s');
        $userData->status = true;
        $userData->save();
        $this->sendEmailChangedMail($userData, $oldEmail);
        return response()->json(['message' => 'تم التحقق من البريد الإلكتروني بنجاح!']);
    }

    public function getMobileOTPandUpdateBackup(Request $request){
        $mobile = $request->mobile;
        $countryCode = $request->countryCode;
        $phone = $countryCode.''.$mobile;
        $whatsapp_notification = $request->whatsapp_notification;
        // Get Country From Country code
        $country = DB::table('countries')->where('country_code','=',$countryCode)->first();
        if(isset($country)){
            $country_id = $country->id;
        }else{
            $country_id = Null;
        }
        $userData = User::find(Auth::user()->id);
        $userData->mobile_no = $mobile;
        $userData->whatsapp_notification = "1";
        $userData->country_id = $country_id;

        $userData->save();

        $data = [];


        // Generate OTP
        $otp = mt_rand(1000,9999);

        // GET API and Send OTP to USER Mobile
        $getApi = Whatsappapi::where('status','=',1)->where('api_for','=','booking_not')->first();
        $url_text = $getApi->api_url;
        $instance_id = $getApi->instance_id;
        $access_token = $getApi->access_token;

        // Storing OTP and mobile number in session
        $request->session()->put('OTP', $otp);
        $request->session()->put('Mobile', $mobile);
        $request->session()->put('CountryCode', $countryCode);

        // Sending OTP via WhatsApp
        $template = Templatecampaign::where('template_name', '=', 'OTP Verification')->first();
        $otpMessage = "$template->msg_whatsapp: $otp";
        if(isset($getApi)){
            $sendURL = $url_text . "?number=" . $phone . "&type=text&message=" . urlencode($otpMessage) . "&instance_id=" . $instance_id . "&access_token=" . $access_token;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $sendURL);
            $result = curl_exec($ch);
            curl_close($ch);

            $data['success'] = "Message Sent";

        }else{
            $data['error'] = "Whatsapp API Error";
        }

        $data['mobile_no'] = $mobile;
        $data['countryCode'] = $countryCode;
        $data['country_iso_code'] = $country->iso_code;
        $data['otp'] = $otp;


        return response()->json($data);

    }

    public function getMobileOTPandUpdate(Request $request){
        // A real number and a known country code (the country is read
        // below) - shared by Profile > Change Mobile and the Hire Now modal.
        $request->merge(['countryCode' => ltrim(trim((string) $request->countryCode), '+'), 'mobile' => trim((string) $request->mobile)]);
        $validator = \Illuminate\Support\Facades\Validator::make($request->only('mobile', 'countryCode'), [
            'mobile' => ['required', 'regex:/^[0-9]{6,15}$/'],
            'countryCode' => ['required', 'exists:countries,country_code'],
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->has('countryCode')
                ? __('locale.Please choose the country code.')
                : __('locale.Please enter a valid mobile number.')], 422);
        }

        $mobile = $request->mobile;
        $countryCode = $request->countryCode;
        $phone = $countryCode.''.$mobile;

        // Get Country From Country code
        $country = DB::table('countries')->where('country_code','=',$countryCode)->first();
        if(isset($country)){
            $country_id = $country->id;
        }else{
            $country_id = Null;
        }
        // One account per mobile number per website (CustomerSite) -
        // otherwise OTP login for that number would be ambiguous.
        if (\App\Support\CustomerSite::accounts()->where('mobile_no', $mobile)->where('id', '!=', Auth::id())->exists()) {
            return response()->json(['message' => __('locale.An account with this mobile number already exists.')], 422);
        }

        // The new number is only written to the account after its OTP is
        // verified (getMobileOTPandUpdateValidate) - previously it was saved
        // here, before any code was even sent.
        \App\Support\OtpVerification::reset($request);
        $request->session()->put('PendingMobileCountryId', $country_id);
        $request->session()->put('PendingMobileCountryCode', $countryCode);

        // Send through the SAME sender as Customer Login OTP
        // (WhatsappOtpController::generateOtp2: the provider-approved "otp"
        // authentication template with a fresh random code, its own fallback
        // API, and the OTP / Mobile / CountryCode session keys that
        // applyVerifiedMobile() checks). This method used to rotate the old
        // fixed-code static templates (staticmetanotifications otp1-otp7),
        // which the WhatsApp provider rejects ("Template for the selected
        // language not found") - so no code was ever delivered.
        $request->merge(['country_code' => $countryCode]);
        $sent = app(\App\Http\Controllers\Auth\WhatsappOtpController::class)->generateOtp2($request)->getData(true);
        // generateOtp2() resets the session's OTP state; keep the pending
        // new number's country for applyVerifiedMobile().
        $request->session()->put('PendingMobileCountryId', $country_id);
        $request->session()->put('PendingMobileCountryCode', $countryCode);

        $responseData = [];
        if (($sent['message'] ?? null) === 'otpsend') {
            $responseData['success'] = "Message Sent";
        } else {
            // The provider's reason (no credentials) for the server log only.
            \Illuminate\Support\Facades\Log::warning('Customer mobile OTP not sent', [
                'user_id' => Auth::id(),
                'country_code' => $countryCode,
                'provider' => optional(Metawhatsapplog::where('message_for', 'OTP')->latest('id')->first(['template_name', 'message_status', 'message_text']))->toArray(),
            ]);
            $responseData['error'] = "Whatsapp API Error";
            $responseData['message'] = __('locale.We could not send the OTP. Please try again in a moment.');
        }
        $responseData['mobile_no'] = $mobile;
        $responseData['countryCode'] = $countryCode;
        $responseData['country_iso_code'] = $country->iso_code;
        // The code is only sent by WhatsApp and checked server-side
        // (applyVerifiedMobile) - never returned here.
        return response()->json($responseData);
    }

    /**
     * Applies the number sent by getMobileOTPandUpdate() to the logged-in
     * account - only once its OTP has actually been verified.
     */
    private function applyVerifiedMobile(Request $request): bool
    {
        if (!\App\Support\OtpVerification::check($request, $request->otp)) {
            return false;
        }

        $mobile = $request->session()->get('Mobile');
        if (\App\Support\CustomerSite::accounts()->where('mobile_no', $mobile)->where('id', '!=', Auth::id())->exists()) {
            return false;
        }

        $post = User::find(Auth::user()->id);
        $post->mobile_no = $request->session()->get('Mobile');
        $post->whatsapp_notification = "1";
        $post->country_id = $request->session()->get('PendingMobileCountryId');
        $post->country_code = $request->session()->get('PendingMobileCountryCode');
        $post->mobile_verified_at = date('Y-m-d H:i:s');
        $post->status = true;
        $post->save();

        \App\Support\OtpVerification::consume($request);
        $request->session()->forget(['PendingMobileCountryId', 'PendingMobileCountryCode']);

        return true;
    }

    public function getMobileOTPandUpdateValidate(Request $request){
        if (!$this->applyVerifiedMobile($request)) {
            return response()->json(['message' => 'Invalid OTP. Please try again.'], 422);
        }

        return response()->json(['message' => 'Mobile No Verified Successfully!']);
    }

    public function getMobileOTPandUpdateValidateAr(Request $request){
        if (!$this->applyVerifiedMobile($request)) {
            return response()->json(['message' => 'رمز التحقق غير صحيح. يرجى المحاولة مرة أخرى.'], 422);
        }

        return response()->json(['message' => 'تم التحقق من الجوال بنجاح!']);
    }

    public function checkMobileExists(Request $request){
        $post = User::where('mobile_no','=',$request->mobile_no)->where('id','!=',Auth::user()->id)->count();

        if($post == 0){
            echo "true";
        }else{
            echo "false";
        }
    }

    public function checkMobileExists2(Request $request){
        $post = User::where('mobile_no','=',$request->mobile_no)->where('id','!=',Auth::user()->id)->count();

        if($post == 0){
            $data['dataMsg'] = 'isnotavailable';
        }else{
            $data['dataMsg'] = 'isavailable';
        }

        return response()->json($data);
    }

    public function updprofileBKC(Request $request)
    {
        $id = Auth::user()->id;
        $user = User::find($id);

        // get Country Details
        if ($request->phone_code != '') {
            $country = Country::where('country_code','=',$request->phone_code)->first();
            if(isset($country)){
                $countryID = $country->id;
            }else{
                $countryID = null;
            }
        } else {
            $country = Country::where('country_code','=','91')->first();
            if(isset($country)){
                $countryID = $country->id;
            }else{
                $countryID = null;
            }
        }


        // if ($request->mobile_no != '' || $request->country_id != '' || $request->city_id != '') {
        //     $user->status = true;
        // } else {
        //     $user->status = false;
        // }

        if ($request->mobile_no != '') {
            $user->status = true;
            if($request->whatsapp_notification == 1 ){
                $user->whatsapp_notification = 1;
            }else{
                $user->whatsapp_notification = 0;
            }
        } else {
            $user->status = false;
        }


        $user->country_id = $countryID;
        $user->mobile_no = $request->mobile_no;
        $user->country_code = $request->phone_code;
        $user->save();

        // Check profile details exists or not
        // $chkProfile = Userprofile::where('user_id','=',$id)->first();

        // if (isset($chkProfile)) {
        //     // Update Profile
        //     $chkProfile->mobile_no = $request->mobile_no;
        //     // $chkProfile->country_id = $request->country_id;
        //     // $chkProfile->city_id = $request->city_id;
        //     $chkProfile->country_id = $countryID;
        //     // change profile status
        //     // if ($request->mobile_no != '' || $request->country_id != '' || $request->city_id != '') {
        //     //     $chkProfile->status = true;
        //     // }else{
        //     //     $chkProfile->status = false;
        //     // }
        //     if ($request->mobile_no != '') {
        //         $chkProfile->status = true;
        //     }else{
        //         $chkProfile->status = false;
        //     }
        //     $chkProfile->save();
        // } else {
        //     // Create Profile
        //     $postProfile = new Userprofile();
        //     $postProfile->user_id = $id;
        //     $postProfile->mobile_no = $request->mobile_no;
        //     // $postProfile->country_id = $request->country_id;
        //     // $postProfile->city_id = $request->city_id;
        //     $postProfile->country_id = $countryID;
        //     $postProfile->save();
        // }

        $data = "Profile updated!";
        return response()->json($data);
    }

    public function updprofileEmailBKC(Request $request){
        $user = User::find(Auth::user()->id);

        $user->email = $request->email;
        $user->save();
        if($request->bklang == "ar"){
            $data = "تحديث الملف الشخصي";
        }else{
            $data = "Profile Updated!";
        }

        return response()->json($data);
    }

    public function getPartnerDetail(Request $request)
    {
        $post = DB::table('partners as partner')
            ->leftJoin('countries as country','partner.country_id','=','country.id')
            ->leftJoin('cities as city','partner.city_id','=','city.id')
            ->select('partner.*','country.name as country','city.name as city')
            ->where('partner.id','=',$request->id)
            ->first();

            // Never expose credentials/internal fields to a customer.
            if ($post) {
                foreach (['password', 'remember_token', 'google_id', 'email_verified_at', 'admin_id', 'registration_status', 'portal_status', 'user_type'] as $secret) {
                    unset($post->{$secret});
                }
            }

            return response()->json($post);
    }

    public function getCandidateDetail(Request $request)
    {

        $post = DB::table('candidates as cand')
            ->leftJoin('countries as nation','cand.nation_id','=','nation.id')
            ->leftJoin('regions as region','cand.region_id','=','region.id')
            ->leftJoin('cities as city','cand.candcity_id','=','city.id')
            ->leftJoin('cities as plb','cand.plb_id','=','plb.id')
            ->select('cand.*','nation.name as nation','region.name as region','city.name as candcity','plb.name as placeofbirth')
            ->where('cand.id','=',$request->cand_id)
            ->first();

            $res = '';
            $res .= '<table class="table table-bordered"><tr><th>Candidate Name</th><td>'.$post->cand_name.'</td>';


            $data['res'] = $res;

        return response()->json($data);
    }

    public function cancelBooking(Request $request)
    {
        $id = $request->id;

        // Only the logged-in customer's own booking (previously any booking
        // id, including other customers' and partners', could be cancelled),
        // and only one this site may show (CustomerSite::whereOrderVisible())
        // - another website partner's order can't be cancelled from here.
        $booking = Booking::where('id', $id)->where('user_id', Auth::id())
            ->where(fn ($visible) => \App\Support\CustomerSite::whereOrderVisible($visible, 'bookings.partner_id'))
            ->first();
        if (!$booking) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $booking->status = true;
        $booking->booking_status = 2;
        $booking->booking_cancelled_by = 'User';
        $booking->booking_cancelled_at = now();
        $booking->save();

        // Candidate Limit
        $cand_limit = Websiteconfig::first();

        // Count booking as per candidate
        $cbkc = Booking::where('cand_id','=',$booking->cand_id)->where('status','=',0)->count();

        // Release candidate 21/06/2023
        // $cand = Candidate::find($booking->cand_id);
        // $cand->status = true;
        // $cand->publish = true;
        // $cand->save();

        if($cbkc < $cand_limit->cand_booking_limit){
            $cand = Candidate::find($booking->cand_id);
            $cand->status = true;
            $cand->publish = true;
            $cand->save();
        }

        // Create Timeline
        $timeline = new Activity();
        $timeline->cand_id = $booking->cand_id;
        $timeline->headline = "Candidate booking canceled!";
        $timeline->bodyMessage = "Candidate booking canceled by ".Auth::user()->name.' on '.date('d-m-Y h:i A');
        $timeline->icons = "ti ti-square-x";
        $timeline->user_id = Auth::user()->id;
        $timeline->save();

        // Cancel Booking Notification
        // $userphone = Userprofile::where('user_id','=',Auth::user()->id)->first();
        $userphone = User::where('id','=',Auth::user()->id)->first();
        $whatsappAPI = Whatsappapi::where('status','=',1)->where('api_for','=','booking_not')->first();
        // Template Name
        $template = Templatecampaign::where('template_name','=','Cancel Order')->first();

        // Meta Template
        $metaTemplate = Metanotification::where('meta_template_name','=','order_cancelled_arabic')->where('status','=',1)->first();

        if(isset($metaTemplate) && isset($userphone)){
            // Phone number with Country Code
            $getCont = Country::where('id','=',$userphone->country_id)->first();
            $phone = $getCont->country_code.''.$userphone->mobile_no;

            $bookingDetails = Booking::find($id);

            $field_var = explode(",",$metaTemplate->meta_field_name);
            $assign_var = explode(",",$metaTemplate->assign_var_name);

            $data = [];
            $data['phone_number'] = $phone;
            $data['template_name'] = $metaTemplate->meta_template_name;
            $data['template_language'] = "ar";

            for($i=0;count($field_var) > $i;$i++){
                if ($assign_var[$i] == "customer_name") {
                    $data[$field_var[$i]] = $userphone->name;
                }
                if ($assign_var[$i] == "order_reference_number") {
                    $data[$field_var[$i]] = $bookingDetails->reference_no;
                }

                if ($assign_var[$i] == "booking_date") {
                    $data[$field_var[$i]] = $bookingDetails->booking_date;
                }
            }

            $data['contact'] =  [
                'first_name' => $userphone->name,
                'last_name' => $userphone->name,
                "email" => $userphone->email,
                "country" => $getCont->name,
                "language_code" => "en"
            ];

            // $metaAPI = Metawhatsappapi::where('status','=',1)->first();

            $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['recruitmentcv_otp'])->first();

            if(isset($metaAPI)){
                $base_url = $metaAPI->api_base_url;
                $vendor_id = $metaAPI->vendor_uid;
                $access_token = $metaAPI->api_access_token;
            }else{
                $base_url = "https://wa.qamr.in/api";
                $vendor_id = "d97cec67-b154-4f5c-9b93-61649b2e650e";
                $access_token = "7GiC9fzJDJ5Df5HF4bEjKw1aZ6ouxIwlkmpIWHjNVqpijP1BumIMCpq1ua0McaNT";
            }


            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
            $token = "Authorization: Bearer ".$access_token;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                CURLOPT_URL => $endpoint_api,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data)
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $responseGet = json_decode($response);

            if(isset($responseGet)){
                if($responseGet->result == 'success'){
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Cancel Order";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $template->template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();

                }elseif($responseGet->result == 'failed'){
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Cancel Order";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $template->template_name;
                    $postLog->message_status = $responseGet->result;
                    $postLog->message_text = $responseGet->message;
                    $postLog->save();

                    // Send Normal Whatsapp
                    if(isset($whatsappAPI)){
                        // API Details
                        $api_id = $whatsappAPI->instance_id;
                        $acc_token = $whatsappAPI->access_token;;
                        $url_text = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[username]' => $userphone->name,
                            '[order_reference_number]' => $bookingDetails->reference_no,
                        ];

                        $userDataMap2 = [
                            '[booking date]' => $bookingDetails->booking_date,
                        ];
                        $userDataMap = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage = $template->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage = str_replace(array_keys($userDataMap), array_values($userDataMap), $otpMessage);

                        $sendURL = $url_text."?number=".$phone."&type=text&message=".urlencode($finalMessage)."&instance_id=".$api_id."&access_token=".$acc_token;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL);
                        $result =  curl_exec($ch);
                        curl_close($ch);

                        // Store Normal Whatsapp Logs
                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }

                }else{
                    $postLog = new Metawhatsapplog();
                    $postLog->message_for = "Cancel Order";
                    $postLog->message_type = "Template";
                    $postLog->template_name = $template->template_name;
                    $postLog->message_status = "Failed";
                    $postLog->message_text = $responseGet->message;
                    $postLog->message_data_error = json_encode($responseGet->errors);
                    $postLog->save();

                    // Send Normal Whatsapp
                    if(isset($whatsappAPI)){
                        // API Details
                        $api_id = $whatsappAPI->instance_id;
                        $acc_token = $whatsappAPI->access_token;;
                        $url_text = $whatsappAPI->api_url;

                        $userDataMap1 = [
                            '[username]' => $userphone->name,
                            '[order_reference_number]' => $bookingDetails->reference_no,
                        ];

                        $userDataMap2 = [
                            '[booking date]' => $bookingDetails->booking_date,
                        ];
                        $userDataMap = array_merge($userDataMap1, $userDataMap2);
                        $otpMessage = $template->msg_whatsapp;

                        // Replace placeholders in the message with user data
                        $finalMessage = str_replace(array_keys($userDataMap), array_values($userDataMap), $otpMessage);

                        $sendURL = $url_text."?number=".$phone."&type=text&message=".urlencode($finalMessage)."&instance_id=".$api_id."&access_token=".$acc_token;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$sendURL);
                        $result =  curl_exec($ch);
                        curl_close($ch);

                        // Store Normal Whatsapp Logs
                        $normalResult = json_decode($result);
                        // Store Logs into normal whatsapp
                        if(isset($normalResult)){
                            if(isset($normalResult->status) && $normalResult->status == "error"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Cancel Order";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = $normalResult->message;
                                $normalPostLog->save();
                            }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Cancel Order";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template->template_name;
                                $normalPostLog->message_status = $normalResult->status;
                                $normalPostLog->message_text = json_encode($normalResult->message);
                                $normalPostLog->save();
                            }else{
                                $normalPostLog = new Normalwhatsapplogs();
                                $normalPostLog->message_for = "Order Booking";
                                $normalPostLog->message_type = "Template";
                                $normalPostLog->template_name = $template->template_name;
                                $normalPostLog->message_status = "error";
                                $normalPostLog->message_text = "Message Not Send";
                                $normalPostLog->save();
                            }
                        }

                    }
                }
            }else{
                // Send Normal Whatsapp
                if(isset($whatsappAPI)){
                    // API Details
                    $api_id = $whatsappAPI->instance_id;
                    $acc_token = $whatsappAPI->access_token;;
                    $url_text = $whatsappAPI->api_url;

                    $userDataMap1 = [
                        '[username]' => $userphone->name,
                        '[order_reference_number]' => $bookingDetails->reference_no,
                    ];

                    $userDataMap2 = [
                        '[booking date]' => $bookingDetails->booking_date,
                    ];
                    $userDataMap = array_merge($userDataMap1, $userDataMap2);
                    $otpMessage = $template->msg_whatsapp;

                    // Replace placeholders in the message with user data
                    $finalMessage = str_replace(array_keys($userDataMap), array_values($userDataMap), $otpMessage);

                    $sendURL = $url_text."?number=".$phone."&type=text&message=".urlencode($finalMessage)."&instance_id=".$api_id."&access_token=".$acc_token;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$sendURL);
                    $result =  curl_exec($ch);
                    curl_close($ch);

                    // Store Normal Whatsapp Logs
                    $normalResult = json_decode($result);
                    // Store Logs into normal whatsapp
                    if(isset($normalResult)){
                        if(isset($normalResult->status) && $normalResult->status == "error"){
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Cancel Order";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template->template_name;
                            $normalPostLog->message_status = $normalResult->status;
                            $normalPostLog->message_text = $normalResult->message;
                            $normalPostLog->save();
                        }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Cancel Order";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template->template_name;
                            $normalPostLog->message_status = $normalResult->status;
                            $normalPostLog->message_text = json_encode($normalResult->message);
                            $normalPostLog->save();
                        }else{
                            $normalPostLog = new Normalwhatsapplogs();
                            $normalPostLog->message_for = "Cancel Order";
                            $normalPostLog->message_type = "Template";
                            $normalPostLog->template_name = $template->template_name;
                            $normalPostLog->message_status = "error";
                            $normalPostLog->message_text = "Message Not Send";
                            $normalPostLog->save();
                        }
                    }

                }
            }

        }else{
            if(isset($userphone) && isset($whatsappAPI)){

                // API Details
                $api_id = $whatsappAPI->instance_id;
                $acc_token = $whatsappAPI->access_token;;
                $url_text = $whatsappAPI->api_url;
                // Phone number with Country Code
                $getCont = Country::where('id','=',$userphone->country_id)->first();
                $phone = $getCont->country_code.''.$userphone->mobile_no;

                // $userDetail = Userprofile::where('mobile_no','=',$userphone->mobile_no)->first();
                // $userid = $userDetail->user_id;
                // $user = User::find($userid);

                $bookingDetails = Booking::find($id);


                $userDataMap1 = [
                    '[username]' => $userphone->name,
                    '[order_reference_number]' => $bookingDetails->reference_no,
                ];

                $userDataMap2 = [
                    '[booking date]' => $bookingDetails->booking_date,
                ];

                $userDataMap = array_merge($userDataMap1, $userDataMap2);
                $otpMessage = $template->msg_whatsapp;

                // Replace placeholders in the message with user data
                $finalMessage = str_replace(array_keys($userDataMap), array_values($userDataMap), $otpMessage);

                $sendURL = $url_text."?number=".$phone."&type=text&message=".urlencode($finalMessage)."&instance_id=".$api_id."&access_token=".$acc_token;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$sendURL);
                $result =  curl_exec($ch);
                curl_close($ch);

                // Store Normal Whatsapp Logs
                $normalResult = json_decode($result);
                // Store Logs into normal whatsapp
                if(isset($normalResult)){
                    if(isset($normalResult->status) && $normalResult->status == "error"){
                        $normalPostLog = new Normalwhatsapplogs();
                        $normalPostLog->message_for = "Cancel Order";
                        $normalPostLog->message_type = "Template";
                        $normalPostLog->template_name = $template->template_name;
                        $normalPostLog->message_status = $normalResult->status;
                        $normalPostLog->message_text = $normalResult->message;
                        $normalPostLog->save();
                    }elseif(isset($normalResult->status) && $normalResult->status == "success"){
                        $normalPostLog = new Normalwhatsapplogs();
                        $normalPostLog->message_for = "Cancel Order";
                        $normalPostLog->message_type = "Template";
                        $normalPostLog->template_name = $template->template_name;
                        $normalPostLog->message_status = $normalResult->status;
                        $normalPostLog->message_text = json_encode($normalResult->message);
                        $normalPostLog->save();
                    }else{
                        $normalPostLog = new Normalwhatsapplogs();
                        $normalPostLog->message_for = "Cancel Order";
                        $normalPostLog->message_type = "Template";
                        $normalPostLog->template_name = $template->template_name;
                        $normalPostLog->message_status = "error";
                        $normalPostLog->message_text = "Message Not Send";
                        $normalPostLog->save();
                    }
                }

                // CancelBookingOrder::dispatch($userphone,$whatsappAPI,$candDet)->onQueue('orderCancel');

            }
        }


    }

    public function checkloginorderstatus(Request $request){
        $candBkc = DB::table('bookings')->where('cand_id','=',$request->cand_id)->where('booking_status','!=',2)->where('user_id','=',Auth::user()->id)->count();
        $userbkc = Booking::where('user_id','=',Auth::user()->id)->where('booking_status','!=',2)->count();
        $max_limit = Websiteconfig::first();
        $partner_count = Partner::where('portal_status','=', '1')->get();
        $checkCand = DB::table('bookings as booking')
        ->leftJoin('partners as partner','booking.partner_id','=','partner.id')
        ->where('booking.partner_id','!=','')
        ->where('partner.portal_status','=','1')
        ->where('booking.booking_status','!=',2)
        ->where('booking.cand_id','=',$request->cand_id)
        ->select('booking.*','partner.portal_add_disp_only')
        ->first();

        if($candBkc == 0){
            if(Auth::user()->status == 1 && Auth::user()->mobile_verified_at != ''){
                if($userbkc < $max_limit->max_booking_limit){
                    if($partner_count->count() == 1){
                        // modalLarge3
                        $responseData['data_status'] = "modalLarge3";
                    }else{
                        if(isset($checkCand)){
                            // modalLarge2
                            $responseData['data_status'] = "modalLarge2";

                        }else{
                            // modalLarge
                            $responseData['data_status'] = "modalLarge";

                        }
                    }

                }else{
                    // bkerror
                    $responseData['data_status'] = "bkerror";

                }
            }else{
                if(Auth::user()->mobile_verified_at == ''){
                    // errorProfile2
                    $responseData['data_status'] = "errorProfile2";

                }
            }
        }else{
            // Already Hire
            $responseData['data_status'] = "hire";

        }

        return response()->json($responseData);
    }
}
