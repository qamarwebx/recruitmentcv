<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Userprofile;
use App\Models\Booking;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Candidate;
use App\Models\Country;
use App\Models\Metanotification;
use App\Models\Metawhatsappapi;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Whatsappapi;
use App\Models\Templatecampaign;
use Illuminate\Support\Facades\Validator;
use App\Models\Websiteconfig;
use App\Models\Metawhatsapplog;
use App\Models\Staticmetanotification;
use App\Models\Otpvalidationmetalist;
use Jenssegers\Agent\Agent;


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
        // Your logic to fetch API details
        $getApi = Whatsappapi::where('status','=',1)->where('api_for','=','booking_not')->first();
        $url_text = $getApi->api_url;
        $instance_id = $getApi->instance_id;
        $access_token = $getApi->access_token;
        $mobile = $request->mobile;
        $countryCodeId = $request->countryCode;
        list($countryId, $countryCode) = explode(",", $countryCodeId);

        // Storing OTP and mobile number in session
        $request->session()->put('OTP', $otp);
        $request->session()->put('Mobile', $mobile);
        $request->session()->put('CountryId', $countryId);
        $request->session()->put('CountryCode', $countryCode);
        $phone = $countryCode . $mobile;

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
        }

        return response()->json(['otp' => $otp], 200);
    }

    public function generateOtp2(Request $request){

        // Get Mobile No, Country Code from ajax request
        $mobile = $request->mobile;
        $countryCode = $request->country_code;
        $countryCodeId = $request->country_code;
        // list($countryId,$countryCode) = explode(",",$countryCodeId);
        $phone_no = $countryCode.''.$mobile;

        // Get Contry Detail
        $getCountry = Country::where('country_code','=',$countryCode)->first();
 
        // Get Meta Whatsapp API and Meta Template
        // $metaAPI = Metawhatsappapi::where('status','=',1)->first();
        $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['otp'])->first();

        $staticTemplates = Staticmetanotification::where('status','=',1)->get();
        $otpTemplate = Metanotification::where('meta_template_name','=','otp')->first();

        // Get Normal Whatsapp API and Template
        $getAPI = Whatsappapi::where('status','=',1)->where('api_for','=','booking_not')->first();
        $template = Templatecampaign::where('template_name', '=', 'OTP Verification')->where('status','=',1)->first();

        $responseData = [];

        if(isset($otpTemplate) && isset($metaAPI)){
            // Generate OTP
            $otp = mt_rand(1000, 9999);

            $request->session()->put('OTP', $otp);
            $request->session()->put('Mobile', $mobile);
            $request->session()->put('CountryId', $getCountry->id);
            $request->session()->put('CountryCode', $countryCode);

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
            $data['template_language'] = "en";
            $data['field_1'] = $otp;
            $data['button_0'] = $otp;
            $data['contact'] =  [
                'first_name' => "Customer",
                'last_name' => "--",
                "email" => "customer@gmail.com",
                "country" => $getCountry->name,
                "language_code" => "en"
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

                // Send OTP To Normal Number
                if(isset($getAPI) && isset($template)){
                    // Generate OTP
                    $otp = mt_rand(1000, 9999);

                    $request->session()->put('OTP', $otp);
                    $request->session()->put('Mobile', $mobile);
                    $request->session()->put('CountryId', $getCountry->id);
                    $request->session()->put('CountryCode', $countryCode);

                    // API Details
                    $url_text = $getAPI->api_url;
                    $instance_id = $getAPI->instance_id;
                    $access_token = $getAPI->access_token;
                    $otpMessage = "$template->msg_whatsapp: $otp";

                    $sendURL = $url_text . "?number=" . $phone_no . "&type=text&message=" . urlencode($otpMessage) . "&instance_id=" . $instance_id . "&access_token=" . $access_token;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_URL, $sendURL);
                    $result = curl_exec($ch);
                    curl_close($ch);

                    $finalResult = json_decode($result);

                    if(isset($finalResult) && $finalResult->status == 'success'){
                        $responseData['message'] = "otpsend";
                    }else{
                        $responseData['message'] = "otpnotsend";
                    }
                }else{
                    $responseData['message'] = "otpnotsend";
                }



            } else {
                $responseData['message'] = "otpsend";
                $metaWhatsappLog->message_status = $responseGet->result;
                $metaWhatsappLog->message_text = $responseGet->message;

            }


            $metaWhatsappLog->save();

        }elseif (isset($staticTemplates) && isset($metaAPI)) {
            // API Details
            $base_url = $metaAPI->api_base_url;
            $vendor_id = $metaAPI->vendor_uid;
            $access_token = $metaAPI->api_access_token;
            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';

            $token = "Authorization: Bearer ".$access_token;

            $initState = 0;
            $staticNotData = [];

            //Check OTP Already Sent or Store in Record
            $otpCount = Otpvalidationmetalist::where('mobile_no','=',$mobile)->get();
            $latestOTPVID = Otpvalidationmetalist::where('mobile_no','=',$mobile)->latest()->first();

            $allOTPVIDs = [];
            $allTempIDs = [];

            if(count($otpCount) > 0){
                // STORE ID on allOTPVIDs
                foreach($otpCount as $otpCount2){
                    $allOTPVIDs [] = $otpCount2->metanotification_id;
                }
                // Store ID on AllTempIDs
                foreach ($staticTemplates as $staticTemp2) {
                    $allTempIDs [] = $staticTemp2->id;
                }

                $newTempID = array_diff($allTempIDs,$allOTPVIDs);
                if (count($newTempID) > 0) {
                    $metaStaticTemp = Staticmetanotification::wherein('id',$newTempID)->get();
                    $initState = 0;
                    $staticNotData['id'] = $metaStaticTemp[$initState]->id;
                    $staticNotData['otp_number'] = $metaStaticTemp[$initState]->otp_number;
                    $staticNotData['meta_template_name'] = $metaStaticTemp[$initState]->meta_template_name;
                }else{
                    $allstaticTemp = Staticmetanotification::where('id','!=',$latestOTPVID->metanotification_id)->get();
                    $initState = 0;
                    $totalTemp = count($allstaticTemp) - 1;
                    $randomID = mt_rand($initState,$totalTemp);
                    $staticNotData['id'] = $allstaticTemp[$randomID]->id;
                    $staticNotData['otp_number'] = $allstaticTemp[$randomID]->otp_number;
                    $staticNotData['meta_template_name'] = $allstaticTemp[$randomID]->meta_template_name;
                }

            }else{
                $initState = 0;
                $totalTemp = count($staticTemplates) - 1;
                $randomID = mt_rand($initState,$totalTemp);
                $staticNotData['id'] = $staticTemplates[$randomID]->id;
                $staticNotData['otp_number'] = $staticTemplates[$randomID]->otp_number;
                $staticNotData['meta_template_name'] = $staticTemplates[$randomID]->meta_template_name;
            }

            // Store Data in Session
            $request->session()->put('OTP', $staticNotData['otp_number']);
            $request->session()->put('Mobile', $mobile);
            $request->session()->put('CountryId', $getCountry->id);
            $request->session()->put('CountryCode', $countryCode);

            // Send OTP to Mobile Number
            $data = [];
            $data['phone_number'] = $phone_no;
            $data['template_name'] = $staticNotData['meta_template_name'];
            $data['template_language'] = "en";
            // $data['field_1'] = $otp;
            $data['contact'] =  [
                'first_name' => "Customer",
                'last_name' => "--",
                "email" => "customer@gmail.com",
                "country" => $getCountry->name,
                "language_code" => "en"
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
            $metaWhatsappLog->template_name = $staticNotData['meta_template_name'];

            if (isset($responseGet->errors) || $responseGet->result == 'failed') {
                $metaWhatsappLog->message_text = $responseGet->message;
                if(isset($responseGet->errors)){
                    $metaWhatsappLog->message_status = "failed";
                }else{
                    $metaWhatsappLog->message_status = $responseGet->result;
                }

                // Send OTP To Normal Number
                if(isset($getAPI) && isset($template)){
                    // Generate OTP
                    $otp = mt_rand(1000, 9999);

                    $request->session()->put('OTP', $otp);
                    $request->session()->put('Mobile', $mobile);
                    $request->session()->put('CountryId', $getCountry->id);
                    $request->session()->put('CountryCode', $countryCode);

                    // API Details
                    $url_text = $getAPI->api_url;
                    $instance_id = $getAPI->instance_id;
                    $access_token = $getAPI->access_token;
                    $otpMessage = "$template->msg_whatsapp: $otp";

                    $sendURL = $url_text . "?number=" . $phone_no . "&type=text&message=" . urlencode($otpMessage) . "&instance_id=" . $instance_id . "&access_token=" . $access_token;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_URL, $sendURL);
                    $result = curl_exec($ch);
                    curl_close($ch);

                    $finalResult = json_decode($result);

                    if(isset($finalResult) && $finalResult->status == 'success'){
                        $responseData['message'] = "otpsend";
                    }else{
                        $responseData['message'] = "otpnotsend";
                    }
                }else{
                    $responseData['message'] = "otpnotsend";
                }



            } else {
                $responseData['message'] = "otpsend";
                $metaWhatsappLog->message_status = $responseGet->result;
                $metaWhatsappLog->message_text = $responseGet->message;

                // Store On OTPValidation
                $newOTP = new Otpvalidationmetalist();
                $newOTP->metanotification_id = $staticNotData['id'];
                $newOTP->otp_no = $staticNotData['otp_number'];
                $newOTP->mobile_no = $mobile;
                $newOTP->otp_date = date('Y-m-d');
                $newOTP->save();
            }


            $metaWhatsappLog->save();


        } elseif (isset($template) && isset($getAPI)) {
            // Generate OTP
            $otp = mt_rand(1000, 9999);

            $request->session()->put('OTP', $otp);
            $request->session()->put('Mobile', $mobile);
            $request->session()->put('CountryId', $getCountry->id);
            $request->session()->put('CountryCode', $countryCode);

           // API Details
           $url_text = $getAPI->api_url;
           $instance_id = $getAPI->instance_id;
           $access_token = $getAPI->access_token;
           $otpMessage = "$template->msg_whatsapp: $otp";

           $sendURL = $url_text . "?number=" . $phone_no . "&type=text&message=" . urlencode($otpMessage) . "&instance_id=" . $instance_id . "&access_token=" . $access_token;
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_URL, $sendURL);
           $result = curl_exec($ch);
           curl_close($ch);

           $finalResult = json_decode($result);
           if(isset($finalResult) && $finalResult->status == 'success'){
                $responseData['message'] = "otpsend";
           }else{
                $responseData['message'] = "otpnotsend";
           }
        } else{
            $responseData['message'] = "systemerror";
        }



        $responseData['mobile'] = $mobile;
        $responseData['iso_code'] = $getCountry->iso_code;
        $responseData['countryId'] = $getCountry->id;
        $responseData['countryCode'] = $countryCode;

        return response()->json($responseData);

    }




    public function generateOtp2Backup23072024(Request $request){
        // Get Mobile No, Country Code from ajax request
        $mobile = $request->mobile;
        $countryCode = $request->country_code;
        $countryCodeId = $request->country_code;
        // list($countryId,$countryCode) = explode(",",$countryCodeId);
        $phone_no = $countryCode.''.$mobile;

        // Get Contry Detail
        $getCountry = Country::where('country_code','=',$countryCode)->first();

        // Get Meta Whatsapp API and Meta Template
        // $metaAPI = Metawhatsappapi::where('status','=',1)->first();
        $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['otp'])->first();
        $staticTemplates = Staticmetanotification::where('status','=',1)->get();

        // Get Normal Whatsapp API and Template
        $getAPI = Whatsappapi::where('status','=',1)->where('api_for','=','booking_not')->first();
        $template = Templatecampaign::where('template_name', '=', 'OTP Verification')->where('status','=',1)->first();

        $responseData = [];

        if (isset($staticTemplates) && isset($metaAPI)) {
            // API Details
            $base_url = $metaAPI->api_base_url;
            $vendor_id = $metaAPI->vendor_uid;
            $access_token = $metaAPI->api_access_token;
            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';

            $token = "Authorization: Bearer ".$access_token;

            $initState = 0;
            $staticNotData = [];

            //Check OTP Already Sent or Store in Record
            $otpCount = Otpvalidationmetalist::where('mobile_no','=',$mobile)->get();
            $latestOTPVID = Otpvalidationmetalist::where('mobile_no','=',$mobile)->latest()->first();

            $allOTPVIDs = [];
            $allTempIDs = [];

            if(count($otpCount) > 0){
                // STORE ID on allOTPVIDs
                foreach($otpCount as $otpCount2){
                    $allOTPVIDs [] = $otpCount2->metanotification_id;
                }
                // Store ID on AllTempIDs
                foreach ($staticTemplates as $staticTemp2) {
                    $allTempIDs [] = $staticTemp2->id;
                }

                $newTempID = array_diff($allTempIDs,$allOTPVIDs);
                if (count($newTempID) > 0) {
                    $metaStaticTemp = Staticmetanotification::wherein('id',$newTempID)->get();
                    $initState = 0;
                    $staticNotData['id'] = $metaStaticTemp[$initState]->id;
                    $staticNotData['otp_number'] = $metaStaticTemp[$initState]->otp_number;
                    $staticNotData['meta_template_name'] = $metaStaticTemp[$initState]->meta_template_name;
                }else{
                    $allstaticTemp = Staticmetanotification::where('id','!=',$latestOTPVID->metanotification_id)->get();
                    $initState = 0;
                    $totalTemp = count($allstaticTemp) - 1;
                    $randomID = mt_rand($initState,$totalTemp);
                    $staticNotData['id'] = $allstaticTemp[$randomID]->id;
                    $staticNotData['otp_number'] = $allstaticTemp[$randomID]->otp_number;
                    $staticNotData['meta_template_name'] = $allstaticTemp[$randomID]->meta_template_name;
                }

            }else{
                $initState = 0;
                $totalTemp = count($staticTemplates) - 1;
                $randomID = mt_rand($initState,$totalTemp);
                $staticNotData['id'] = $staticTemplates[$randomID]->id;
                $staticNotData['otp_number'] = $staticTemplates[$randomID]->otp_number;
                $staticNotData['meta_template_name'] = $staticTemplates[$randomID]->meta_template_name;
            }

            // Store Data in Session
            $request->session()->put('OTP', $staticNotData['otp_number']);
            $request->session()->put('Mobile', $mobile);
            $request->session()->put('CountryId', $getCountry->id);
            $request->session()->put('CountryCode', $countryCode);

            // Send OTP to Mobile Number
            $data = [];
            $data['phone_number'] = $phone_no;
            $data['template_name'] = $staticNotData['meta_template_name'];
            $data['template_language'] = "en";
            // $data['field_1'] = $otp;
            $data['contact'] =  [
                'first_name' => "Customer",
                'last_name' => "--",
                "email" => "customer@gmail.com",
                "country" => $getCountry->name,
                "language_code" => "en"
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
            $metaWhatsappLog->template_name = $staticNotData['meta_template_name'];

            if (isset($responseGet->errors) || $responseGet->result == 'failed') {
                $metaWhatsappLog->message_text = $responseGet->message;
                if(isset($responseGet->errors)){
                    $metaWhatsappLog->message_status = "failed";
                }else{
                    $metaWhatsappLog->message_status = $responseGet->result;
                }

                // Send OTP To Normal Number
                if(isset($getAPI) && isset($template)){
                    // Generate OTP
                    $otp = mt_rand(1000, 9999);

                    $request->session()->put('OTP', $otp);
                    $request->session()->put('Mobile', $mobile);
                    $request->session()->put('CountryId', $getCountry->id);
                    $request->session()->put('CountryCode', $countryCode);

                    // API Details
                    $url_text = $getAPI->api_url;
                    $instance_id = $getAPI->instance_id;
                    $access_token = $getAPI->access_token;
                    $otpMessage = "$template->msg_whatsapp: $otp";

                    $sendURL = $url_text . "?number=" . $phone_no . "&type=text&message=" . urlencode($otpMessage) . "&instance_id=" . $instance_id . "&access_token=" . $access_token;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_URL, $sendURL);
                    $result = curl_exec($ch);
                    curl_close($ch);

                    $finalResult = json_decode($result);

                    if(isset($finalResult) && $finalResult->status == 'success'){
                        $responseData['message'] = "otpsend";
                    }else{
                        $responseData['message'] = "otpnotsend";
                    }
                }else{
                    $responseData['message'] = "otpnotsend";
                }



            } else {
                $responseData['message'] = "otpsend";
                $metaWhatsappLog->message_status = $responseGet->result;
                $metaWhatsappLog->message_text = $responseGet->message;

                // Store On OTPValidation
                $newOTP = new Otpvalidationmetalist();
                $newOTP->metanotification_id = $staticNotData['id'];
                $newOTP->otp_no = $staticNotData['otp_number'];
                $newOTP->mobile_no = $mobile;
                $newOTP->otp_date = date('Y-m-d');
                $newOTP->save();
            }


            $metaWhatsappLog->save();


        } elseif (isset($template) && isset($getAPI)) {
            // Generate OTP
            $otp = mt_rand(1000, 9999);

            $request->session()->put('OTP', $otp);
            $request->session()->put('Mobile', $mobile);
            $request->session()->put('CountryId', $getCountry->id);
            $request->session()->put('CountryCode', $countryCode);

           // API Details
           $url_text = $getAPI->api_url;
           $instance_id = $getAPI->instance_id;
           $access_token = $getAPI->access_token;
           $otpMessage = "$template->msg_whatsapp: $otp";

           $sendURL = $url_text . "?number=" . $phone_no . "&type=text&message=" . urlencode($otpMessage) . "&instance_id=" . $instance_id . "&access_token=" . $access_token;
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_URL, $sendURL);
           $result = curl_exec($ch);
           curl_close($ch);

           $finalResult = json_decode($result);
           if(isset($finalResult) && $finalResult->status == 'success'){
                $responseData['message'] = "otpsend";
           }else{
                $responseData['message'] = "otpnotsend";
           }
        } else{
            $responseData['message'] = "systemerror";
        }



        $responseData['mobile'] = $mobile;
        $responseData['iso_code'] = $getCountry->iso_code;
        $responseData['countryId'] = $getCountry->id;
        $responseData['countryCode'] = $countryCode;

        return response()->json($responseData);

    }

    public function generateOtp3bakcup(Request $request){
        // OTP Verification and Meta Response
        $getwebsiteconfig = Websiteconfig::first();

        // Get Meta Whatsapp API and Meta Template
        // $metaAPI = Metawhatsappapi::where('status','=',1)->first();
        $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['otp'])->first();
        $metaTemplate = Metanotification::where('meta_template_name','=','otp_ar')->where('status','=',1)->first();
        $staticTemplates = Staticmetanotification::orderBy('id','DESC')->get();
        $date = date('Y-m-d');
        $otpValidationLists = Otpvalidationmetalist::where('mobile_no','=',$request->mobile)->where('otp_date','=',$date)->get();

        $statictNotificationData = [];
        $loopinit = 0;
        $statNotC = count($staticTemplates) - 1;

        // Get Template Data and Generate OTP
        if(count($staticTemplates) > 0){
            $randOTPData = mt_rand($loopinit,$statNotC);
            $statictNotificationData['id'] = $staticTemplates[$randOTPData]->id;
            $statictNotificationData['meta_template_name'] = $staticTemplates[$randOTPData]->meta_template_name;
            $statictNotificationData['otp_number'] = $staticTemplates[$randOTPData]->otp_number;
            $otp = $statictNotificationData['otp_number'];
        }else{
            $otp = mt_rand(1000, 9999);
        }




        // Get Normal Whatsapp API and Template
        $getAPI = Whatsappapi::where('status','=',1)->where('api_for','=','booking_not')->first();
        $template = Templatecampaign::where('template_name', '=', 'OTP Verification')->where('status','=',1)->first();

        // Get Mobile No, Country Code from ajax request
        $mobile = $request->mobile;
        $countryCodeId = $request->country_code;
        list($countryId,$countryCode) = explode(",",$countryCodeId);
        $phone_no = $countryCode.''.$mobile;

        // Get Contry Detail
        $getCountry = Country::find($countryId);

        // Generate OTP
        // $otp = mt_rand(1000, 9999);
        // $otp = $statictNotificationData['otp_number'];
        // Store OTP and Mobile in Session

        $request->session()->put('OTP', $otp);
        $request->session()->put('Mobile', $mobile);
        $request->session()->put('CountryId', $countryId);
        $request->session()->put('CountryCode', $countryCode);

        $responseData = [];

        // Send OTP through Meta Whatsapp
        // if(isset($getAPI) && isset($template)){
        //     // API Details
        //     $url_text = $getAPI->api_url;
        //     $instance_id = $getAPI->instance_id;
        //     $access_token = $getAPI->access_token;
        //     $otpMessage = "$template->msg_whatsapp: $otp";

        //     $sendURL = $url_text . "?number=" . $phone_no . "&type=text&message=" . urlencode($otpMessage) . "&instance_id=" . $instance_id . "&access_token=" . $access_token;
        //     $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_URL, $sendURL);
        //     $result = curl_exec($ch);
        //     curl_close($ch);

        //     $finalResult = json_decode($result);

        //     if(isset($finalResult) && $finalResult->result == 'success'){
        //         $responseData['message'] = "otpsend";
        //     }else{
        //         // $responseData['message'] = "otpnotsend";
        //         if (isset($metaAPI) && isset($metaTemplate)) {
        //             // API Details
        //             $base_url = $metaAPI->api_base_url;
        //             $vendor_id = $metaAPI->vendor_uid;
        //             $access_token = $metaAPI->api_access_token;
        //             $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
        //             $token = "Authorization: Bearer ".$access_token;

        //             $data = [];
        //             $data['phone_number'] = $phone_no;
        //             $data['template_name'] = $metaTemplate->meta_template_name;
        //             $data['template_language'] = "en_US";
        //             $data['field_1'] = $otp;
        //             $data['contact'] =  [
        //                 'first_name' => "Customer",
        //                 'last_name' => "--",
        //                 "email" => "customer@gmail.com",
        //                 "country" => $getCountry->name,
        //                 "language_code" => "en"
        //             ];

        //             // Send Campaign Message
        //             $curl = curl_init();
        //             curl_setopt_array($curl, array(
        //                 CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
        //                 CURLOPT_URL => $endpoint_api,
        //                 CURLOPT_RETURNTRANSFER => true,
        //                 CURLOPT_ENCODING => '',
        //                 CURLOPT_MAXREDIRS => 10,
        //                 CURLOPT_TIMEOUT => 0,
        //                 CURLOPT_FOLLOWLOCATION => true,
        //                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //                 CURLOPT_CUSTOMREQUEST => 'POST',
        //                 CURLOPT_POSTFIELDS => json_encode($data)
        //             ));

        //             $response = curl_exec($curl);
        //             curl_close($curl);
        //             $responseGet = json_decode($response);

        //             if ((isset($responseGet) && isset($responseGet->errors)) || (isset($responseGet) && $responseGet->result == 'failed')) {
        //                 $responseData['message'] = "otpnotsend";
        //             }else{
        //                 $responseData['message'] = "otpsend";
        //             }


        //         } else {
        //             $responseData['message'] = "otpnotsend";
        //         }

        //     }
        // }elseif(isset($metaAPI) && isset($metaTemplate)){
        //     // API Details
        //     $base_url = $metaAPI->api_base_url;
        //     $vendor_id = $metaAPI->vendor_uid;
        //     $access_token = $metaAPI->api_access_token;
        //     $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';

        //     $token = "Authorization: Bearer ".$access_token;

        //     $data = [];
        //     $data['phone_number'] = $phone_no;
        //     $data['template_name'] = $metaTemplate->meta_template_name;
        //     $data['template_language'] = "en_US";
        //     $data['field_1'] = $otp;
        //     $data['contact'] =  [
        //         'first_name' => "Customer",
        //         'last_name' => "--",
        //         "email" => "customer@gmail.com",
        //         "country" => $getCountry->name,
        //         "language_code" => "en"
        //     ];

        //     // Send Campaign Message
        //     $curl = curl_init();
        //     curl_setopt_array($curl, array(
        //        CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
        //        CURLOPT_URL => $endpoint_api,
        //        CURLOPT_RETURNTRANSFER => true,
        //        CURLOPT_ENCODING => '',
        //        CURLOPT_MAXREDIRS => 10,
        //        CURLOPT_TIMEOUT => 0,
        //        CURLOPT_FOLLOWLOCATION => true,
        //        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //        CURLOPT_CUSTOMREQUEST => 'POST',
        //        CURLOPT_POSTFIELDS => json_encode($data)
        //    ));

        //     $response = curl_exec($curl);
        //     curl_close($curl);
        //     $responseGet = json_decode($response);

        //     if ((isset($responseGet) && isset($responseGet->errors)) || (isset($responseGet) && $responseGet->result == 'failed')) {
        //         $responseData['message'] = "otpnotsend";
        //     }else{
        //         $responseData['message'] = "otpsend";
        //     }

        // }else{
        //     $responseData['message'] = "otpnotsend";
        // }

        // Send OTP based on Website Configuration

        if($getwebsiteconfig->otp_verification == 1 && $getwebsiteconfig->meta_response == 0){
            if(isset($getAPI) && isset($template)){
                // API Details
                $url_text = $getAPI->api_url;
                $instance_id = $getAPI->instance_id;
                $access_token = $getAPI->access_token;
                $otpMessage = "$template->msg_whatsapp: $otp";

                $sendURL = $url_text . "?number=" . $phone_no . "&type=text&message=" . urlencode($otpMessage) . "&instance_id=" . $instance_id . "&access_token=" . $access_token;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $sendURL);
                $result = curl_exec($ch);
                curl_close($ch);

                $finalResult = json_decode($result);


                if(isset($finalResult) && $finalResult->status == 'success'){
                    $responseData['message'] = "otpsend";
                }else{
                    // $responseData['message'] = "otpnotsend";
                    if (isset($metaAPI) && isset($staticTemplates)) {
                        // Store OTP Validation
                        $postOPTValid = new Otpvalidationmetalist();
                        $postOPTValid->metanotification_id = $statictNotificationData['id'];
                        $postOPTValid->otp_no = $statictNotificationData['otp_number'];
                        $postOPTValid->mobile_no = $request->mobile;
                        $postOPTValid->otp_date = date('Y-m-d');
                        $postOPTValid->save();
                        // API Details
                        $base_url = $metaAPI->api_base_url;
                        $vendor_id = $metaAPI->vendor_uid;
                        $access_token = $metaAPI->api_access_token;
                        $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                        $token = "Authorization: Bearer ".$access_token;

                        $data = [];
                        $data['phone_number'] = $phone_no;
                        $data['template_name'] = $statictNotificationData['meta_template_name'];
                        $data['template_language'] = "en_US";
                        // $data['field_1'] = $otp;
                        $data['contact'] =  [
                            'first_name' => "Customer",
                            'last_name' => "--",
                            "email" => "customer@gmail.com",
                            "country" => $getCountry->name,
                            "language_code" => "en"
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
                        $metaWhatsappLog->template_name = $statictNotificationData['meta_template_name'];

                        if ((isset($responseGet) && isset($responseGet->errors)) || (isset($responseGet) && $responseGet->result == 'failed')) {
                            $responseData['message'] = "otpnotsend";
                            if(isset($responseGet->errors)){
                                $metaWhatsappLog->message_status = "failed";
                                $metaWhatsappLog->message_text = $responseGet->message;
                            }else{
                                $metaWhatsappLog->message_status = $responseGet->result;
                                $metaWhatsappLog->message_text = $responseGet->message;
                            }
                        }else{
                            $metaWhatsappLog->message_status = $responseGet->result;
                            $metaWhatsappLog->message_text = $responseGet->message;
                            $responseData['message'] = "otpsend";
                        }
                        $metaWhatsappLog->save();

                    } else {
                        $responseData['message'] = "otpnotsend";
                    }

                }
            }elseif(isset($metaAPI) && isset($staticTemplates)){
                // Store OTP Validation
                $postOPTValid = new Otpvalidationmetalist();
                $postOPTValid->metanotification_id = $statictNotificationData['id'];
                $postOPTValid->otp_no = $statictNotificationData['otp_number'];
                $postOPTValid->mobile_no = $request->mobile;
                $postOPTValid->otp_date = date('Y-m-d');
                $postOPTValid->save();

                // API Details
                $base_url = $metaAPI->api_base_url;
                $vendor_id = $metaAPI->vendor_uid;
                $access_token = $metaAPI->api_access_token;
                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';

                $token = "Authorization: Bearer ".$access_token;

                $data = [];
                $data['phone_number'] = $phone_no;
                $data['template_name'] = $statictNotificationData['meta_template_name'];
                $data['template_language'] = "en_US";
                // $data['field_1'] = $otp;
                $data['contact'] =  [
                    'first_name' => "Customer",
                    'last_name' => "--",
                    "email" => "customer@gmail.com",
                    "country" => $getCountry->name,
                    "language_code" => "en"
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
                $metaWhatsappLog->template_name = $statictNotificationData['meta_template_name'];

                if ((isset($responseGet) && isset($responseGet->errors)) || (isset($responseGet) && $responseGet->result == 'failed')) {
                    $responseData['message'] = "otpnotsend";
                    if(isset($responseGet->errors)){
                        $metaWhatsappLog->message_status = "failed";
                        $metaWhatsappLog->message_text = $responseGet->message;
                    }else{
                        $metaWhatsappLog->message_status = $responseGet->result;
                        $metaWhatsappLog->message_text = $responseGet->message;
                    }
                }else{
                    $responseData['message'] = "otpsend";
                    $metaWhatsappLog->message_status = $responseGet->result;
                    $metaWhatsappLog->message_text = $responseGet->message;
                }
                $metaWhatsappLog->save();
            }else{
                $responseData['message'] = "systemerror";
            }
        }else{
            $responseData['message'] = "systemerror";
        }

        //25052024 if ($getwebsiteconfig->meta_response == 1 && $getwebsiteconfig->otp_verification == 0) {
        //     if(isset($metaAPI) && isset($metaTemplate)){
        //         // API Details
        //         $base_url = $metaAPI->api_base_url;
        //         $vendor_id = $metaAPI->vendor_uid;
        //         $access_token = $metaAPI->api_access_token;
        //         $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';

        //         $token = "Authorization: Bearer ".$access_token;

        //         $data = [];
        //         $data['phone_number'] = $phone_no;
        //         $data['template_name'] = $metaTemplate->meta_template_name;
        //         $data['template_language'] = "en_US";
        //         $data['field_1'] = $otp;
        //         $data['contact'] =  [
        //             'first_name' => "Customer",
        //             'last_name' => "--",
        //             "email" => "customer@gmail.com",
        //             "country" => $getCountry->name,
        //             "language_code" => "en"
        //         ];

        //         // Send Campaign Message
        //         $curl = curl_init();
        //         curl_setopt_array($curl, array(
        //            CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
        //            CURLOPT_URL => $endpoint_api,
        //            CURLOPT_RETURNTRANSFER => true,
        //            CURLOPT_ENCODING => '',
        //            CURLOPT_MAXREDIRS => 10,
        //            CURLOPT_TIMEOUT => 0,
        //            CURLOPT_FOLLOWLOCATION => true,
        //            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //            CURLOPT_CUSTOMREQUEST => 'POST',
        //            CURLOPT_POSTFIELDS => json_encode($data)
        //         ));

        //         $response = curl_exec($curl);
        //         curl_close($curl);
        //         $responseGet = json_decode($response);

        //         // Store meta notification logs into database
        //         $metaWhatsappLog = new Metawhatsapplog();
        //         $metaWhatsappLog->message_for = "OTP";
        //         $metaWhatsappLog->message_type = "OTP Template";
        //         $metaWhatsappLog->template_name = $metaTemplate->meta_template_name;
        //         if ((isset($responseGet) && isset($responseGet->errors)) || (isset($responseGet) && $responseGet->result == 'failed')) {
        //             $responseData['message'] = "responsefailed";

        //             if(isset($responseGet->errors)){
        //                 $metaWhatsappLog->message_status = "failed";
        //                 $metaWhatsappLog->message_text = $responseGet->message;
        //             }else{
        //                 $metaWhatsappLog->message_status = $responseGet->result;
        //                 $metaWhatsappLog->message_text = $responseGet->message;
        //             }
        //         }else{
        //             $metaWhatsappLog->message_status = $responseGet->result;
        //             $metaWhatsappLog->message_text = $responseGet->message;
        //             $responseData['message'] = "responsesuccess";
        //         }
        //         $metaWhatsappLog->save();

        //     }else{
        //         $responseData['message'] = "systemerror";
        //     }
        // }elseif($getwebsiteconfig->otp_verification == 1 && $getwebsiteconfig->meta_response == 0){
        //     if(isset($getAPI) && isset($template)){
        //         // API Details
        //         $url_text = $getAPI->api_url;
        //         $instance_id = $getAPI->instance_id;
        //         $access_token = $getAPI->access_token;
        //         $otpMessage = "$template->msg_whatsapp: $otp";

        //         $sendURL = $url_text . "?number=" . $phone_no . "&type=text&message=" . urlencode($otpMessage) . "&instance_id=" . $instance_id . "&access_token=" . $access_token;
        //         $ch = curl_init();
        //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //         curl_setopt($ch, CURLOPT_URL, $sendURL);
        //         $result = curl_exec($ch);
        //         curl_close($ch);

        //         $finalResult = json_decode($result);


        //         if(isset($finalResult) && $finalResult->status == 'success'){
        //             $responseData['message'] = "otpsend";
        //         }else{
        //             // $responseData['message'] = "otpnotsend";
        //             if (isset($metaAPI) && isset($metaTemplate)) {
        //                 // API Details
        //                 $base_url = $metaAPI->api_base_url;
        //                 $vendor_id = $metaAPI->vendor_uid;
        //                 $access_token = $metaAPI->api_access_token;
        //                 $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
        //                 $token = "Authorization: Bearer ".$access_token;

        //                 $data = [];
        //                 $data['phone_number'] = $phone_no;
        //                 $data['template_name'] = $metaTemplate->meta_template_name;
        //                 $data['template_language'] = "en_US";
        //                 $data['field_1'] = $otp;
        //                 $data['contact'] =  [
        //                     'first_name' => "Customer",
        //                     'last_name' => "--",
        //                     "email" => "customer@gmail.com",
        //                     "country" => $getCountry->name,
        //                     "language_code" => "en"
        //                 ];

        //                 // Send Campaign Message
        //                 $curl = curl_init();
        //                 curl_setopt_array($curl, array(
        //                     CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
        //                     CURLOPT_URL => $endpoint_api,
        //                     CURLOPT_RETURNTRANSFER => true,
        //                     CURLOPT_ENCODING => '',
        //                     CURLOPT_MAXREDIRS => 10,
        //                     CURLOPT_TIMEOUT => 0,
        //                     CURLOPT_FOLLOWLOCATION => true,
        //                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //                     CURLOPT_CUSTOMREQUEST => 'POST',
        //                     CURLOPT_POSTFIELDS => json_encode($data)
        //                 ));

        //                 $response = curl_exec($curl);
        //                 curl_close($curl);
        //                 $responseGet = json_decode($response);

        //                 // Store meta notification logs into database
        //                 $metaWhatsappLog = new Metawhatsapplog();
        //                 $metaWhatsappLog->message_for = "OTP";
        //                 $metaWhatsappLog->message_type = "OTP Template";
        //                 $metaWhatsappLog->template_name = $metaTemplate->meta_template_name;

        //                 if ((isset($responseGet) && isset($responseGet->errors)) || (isset($responseGet) && $responseGet->result == 'failed')) {
        //                     $responseData['message'] = "otpnotsend";
        //                     if(isset($responseGet->errors)){
        //                         $metaWhatsappLog->message_status = "failed";
        //                         $metaWhatsappLog->message_text = $responseGet->message;
        //                     }else{
        //                         $metaWhatsappLog->message_status = $responseGet->result;
        //                         $metaWhatsappLog->message_text = $responseGet->message;
        //                     }
        //                 }else{
        //                     $metaWhatsappLog->message_status = $responseGet->result;
        //                     $metaWhatsappLog->message_text = $responseGet->message;
        //                     $responseData['message'] = "otpsend";
        //                 }
        //                 $metaWhatsappLog->save();

        //             } else {
        //                 $responseData['message'] = "otpnotsend";
        //             }

        //         }
        //     }elseif(isset($metaAPI) && isset($metaTemplate)){
        //         // API Details
        //         $base_url = $metaAPI->api_base_url;
        //         $vendor_id = $metaAPI->vendor_uid;
        //         $access_token = $metaAPI->api_access_token;
        //         $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';

        //         $token = "Authorization: Bearer ".$access_token;

        //         $data = [];
        //         $data['phone_number'] = $phone_no;
        //         $data['template_name'] = $metaTemplate->meta_template_name;
        //         $data['template_language'] = "en_US";
        //         $data['field_1'] = $otp;
        //         $data['contact'] =  [
        //             'first_name' => "Customer",
        //             'last_name' => "--",
        //             "email" => "customer@gmail.com",
        //             "country" => $getCountry->name,
        //             "language_code" => "en"
        //         ];

        //         // Send Campaign Message
        //         $curl = curl_init();
        //         curl_setopt_array($curl, array(
        //             CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
        //             CURLOPT_URL => $endpoint_api,
        //             CURLOPT_RETURNTRANSFER => true,
        //             CURLOPT_ENCODING => '',
        //             CURLOPT_MAXREDIRS => 10,
        //             CURLOPT_TIMEOUT => 0,
        //             CURLOPT_FOLLOWLOCATION => true,
        //             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //             CURLOPT_CUSTOMREQUEST => 'POST',
        //             CURLOPT_POSTFIELDS => json_encode($data)
        //         ));

        //         $response = curl_exec($curl);
        //         curl_close($curl);
        //         $responseGet = json_decode($response);

        //         // Store meta notification logs into database
        //         $metaWhatsappLog = new Metawhatsapplog();
        //         $metaWhatsappLog->message_for = "OTP";
        //         $metaWhatsappLog->message_type = "OTP Template";
        //         $metaWhatsappLog->template_name = $metaTemplate->meta_template_name;

        //         if ((isset($responseGet) && isset($responseGet->errors)) || (isset($responseGet) && $responseGet->result == 'failed')) {
        //             $responseData['message'] = "otpnotsend";
        //             if(isset($responseGet->errors)){
        //                 $metaWhatsappLog->message_status = "failed";
        //                 $metaWhatsappLog->message_text = $responseGet->message;
        //             }else{
        //                 $metaWhatsappLog->message_status = $responseGet->result;
        //                 $metaWhatsappLog->message_text = $responseGet->message;
        //             }
        //         }else{
        //             $responseData['message'] = "otpsend";
        //             $metaWhatsappLog->message_status = $responseGet->result;
        //             $metaWhatsappLog->message_text = $responseGet->message;
        //         }
        //         $metaWhatsappLog->save();
        //     }else{
        //         $responseData['message'] = "systemerror";
        //     }
        // }else{
        //     $responseData['message'] = "systemerror";
        // }

        // 25052024 3:49PM
        // if (isset($metaAPI) && isset($metaTemplate)) {
        //     // API Details
        //     $base_url = $metaAPI->api_base_url;
        //     $vendor_id = $metaAPI->vendor_uid;
        //     $access_token = $metaAPI->api_access_token;
        //     $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';

        //     $token = "Authorization: Bearer ".$access_token;



        //     $data = [];
        //     $data['phone_number'] = $phone_no;
        //     $data['template_name'] = $metaTemplate->meta_template_name;
        //     $data['template_language'] = "en_US";
        //     $data['field_1'] = $otp;
        //     $data['contact'] =  [
        //         'first_name' => "Customer",
        //         'last_name' => "--",
        //         "email" => "customer@gmail.com",
        //         "country" => $getCountry->name,
        //         "language_code" => "en"
        //     ];

        //      // Send Campaign Message
        //      $curl = curl_init();
        //      curl_setopt_array($curl, array(
        //         CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
        //         CURLOPT_URL => $endpoint_api,
        //         CURLOPT_RETURNTRANSFER => true,
        //         CURLOPT_ENCODING => '',
        //         CURLOPT_MAXREDIRS => 10,
        //         CURLOPT_TIMEOUT => 0,
        //         CURLOPT_FOLLOWLOCATION => true,
        //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //         CURLOPT_CUSTOMREQUEST => 'POST',
        //         CURLOPT_POSTFIELDS => json_encode($data)
        //     ));

        //     $response = curl_exec($curl);
        //     curl_close($curl);
        //     $responseGet = json_decode($response);

        //     // Message not send then send to normal whatsapp
        //     if ((isset($responseGet) && isset($responseGet->errors)) || (isset($responseGet) && $responseGet->result == 'failed')) {
        //         if(isset($getAPI)){
        //             // API Details
        //             $url_text = $getAPI->api_url;
        //             $instance_id = $getAPI->instance_id;
        //             $access_token = $getAPI->access_token;
        //             $otpMessage = "$template->msg_whatsapp: $otp";

        //             $sendURL = $url_text . "?number=" . $phone_no . "&type=text&message=" . urlencode($otpMessage) . "&instance_id=" . $instance_id . "&access_token=" . $access_token;
        //             $ch = curl_init();
        //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //             curl_setopt($ch, CURLOPT_URL, $sendURL);
        //             $result = curl_exec($ch);
        //             curl_close($ch);

        //             $finalResult = json_decode($result);

        //             if(isset($finalResult) && $finalResult->result == 'success'){
        //                 $responseData['message'] = "otpsend";
        //             }else{
        //                 $responseData['message'] = "otpnotsend";
        //             }

        //         }else{
        //             $responseData['message'] = "otpnotsend";
        //         }


        //     }else{
        //         $responseData['message'] = "otpsend";
        //     }



        // }elseif(isset($getAPI) && isset($template)){

        //     // API Details
        //     $url_text = $getAPI->api_url;
        //     $instance_id = $getAPI->instance_id;
        //     $access_token = $getAPI->access_token;
        //     $otpMessage = "$template->msg_whatsapp: $otp";

        //     $sendURL = $url_text . "?number=" . $phone_no . "&type=text&message=" . urlencode($otpMessage) . "&instance_id=" . $instance_id . "&access_token=" . $access_token;
        //     $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_URL, $sendURL);
        //     $result = curl_exec($ch);
        //     curl_close($ch);

        //     $finalResult = json_decode($result);

        //     if(isset($finalResult) && $finalResult->result == 'success'){
        //         $responseData['message'] = "otpsend";
        //     }else{
        //         $responseData['message'] = "otpnotsend";
        //     }

        // }else{
        //     $responseData['message'] = "otpnotsend";
        // }

        $responseData['mobile'] = $mobile;
        $responseData['countryId'] = $countryId;
        $responseData['countryCode'] = $countryCode;

        return response()->json($responseData);
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

    public function validateOtp2(Request $request){
        $requestOtp = $request->otp;
        $sessionOTP =  $request->session()->get('OTP');

        $responsedata = [];

        if($requestOtp == $sessionOTP){
            $responsedata['message'] = "success";
        }else{
            $responsedata['message'] = "error";
        }

        return response()->json($responsedata);

    }

    public function loginUser(Request $request)
    {
        if (Auth::check()) {
            return response()->json(['message' => 'User is already logged in.'], 200);
        }

        $mobile = $request->session()->get('Mobile');
        // $userProfile = Userprofile::where('mobile_no', $mobile)->first();
        $userProfile = User::where('mobile_no', $mobile)->first();

        // If Mobile Verified Not update then update



        if ($userProfile) {
            // $user = User::find($userProfile->user_id);

            // if ($user) {
            //     Auth::login($user);
            //     $request->session()->forget('Mobile');
            //     return response()->json(['message' => 'User logged in successfully.'], 200);
            // }

            if ($userProfile->mobile_verified_at == '') {
                $userProfile->mobile_verified_at = date('Y-m-d H:i:s');
                $userProfile->verification_status = true;
                $userProfile->save();
            }

            Auth::login($userProfile);
            $request->session()->forget('Mobile');
            return response()->json(['message' => 'User logged in successfully.'], 200);

        }

        // If user not found, set a flag to indicate new user
        $request->session()->put('new_user', true);
        return response()->json(['status' => 'new_user'], 200);
    }

    public function loginUser2(Request $request){
        if (Auth::check()) {
            return response()->json(['message' => 'User is already logged in.'], 200);
        }

        $mobile = $request->session()->get('Mobile');
        $userProfile = User::where('mobile_no', $mobile)->first();

        $agent = new Agent();
        $ip = $request->ip();

        $last_login_from = [
            'device_type' => $agent->device() ?: 'Unknown',
            'browser'     => $agent->browser() ?: 'Unknown',
            'os'          => $agent->platform() ?: 'Unknown',
            'ip_address'  => $ip,
            'last_logged_at'   => Carbon::now()->toDateTimeString(),
        ];

        if ($userProfile) {

            $country = Country::find($userProfile->country_id);


            if ($userProfile->mobile_verified_at == '') {
                $userProfile->mobile_verified_at = date('Y-m-d H:i:s');
            }
            $userProfile->last_login_from = json_encode($last_login_from);
            $userProfile->country_code = $country->country_code ?? null;
            $userProfile->save();
            Auth::login($userProfile);
            $request->session()->forget('Mobile');
            return response()->json(['message' => 'User logged in successfully.'], 200);
        }

         // If user not found, set a flag to indicate new user
         $request->session()->put('new_user', true);
         return response()->json(['status' => 'new_user'], 200);
    }

    public function registerUser(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $country = Country::find($request->session()->get('CountryId'));

        // Create new user
        $user = new User();
        $user->name = $validatedData['name'];
        $user->verification_status = '1';
        $user->mobile_no = $request->session()->get('Mobile');
        $user->country_id = $request->session()->get('CountryId');
        $user->country_code = $country->country_code ?? null;
        $user->mobile_verified_at = date('Y-m-d H:i:s');
        $user->save();

        $userId = $user->id;

        // Create user profile
        // $userProfile = new Userprofile();
        // $userProfile->user_id = $userId;
        // $userProfile->mobile_no = $request->session()->get('Mobile');
        // $userProfile->country_id = $request->session()->get('CountryId');
        // $userProfile->status = '1';
        // $userProfile->save();

        // Log in the newly registered user
        Auth::login($user);

        return response()->json(['message' => 'User registered and logged in successfully.'], 200);
    }

    public function registerUser2(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $country = Country::find($request->session()->get('CountryId'));

        // Create new user
        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $request->email;
        $user->verification_status = '1';
        $user->mobile_no = $request->session()->get('Mobile');
        $user->country_id = $request->session()->get('CountryId');
        $user->country_code = $country->country_code ?? null;
        $user->mobile_verified_at = date('Y-m-d H:i:s');
        $user->save();

        $userId = $user->id;

        // Create user profile
        // $userProfile = new Userprofile();
        // $userProfile->user_id = $userId;
        // $userProfile->mobile_no = $request->session()->get('Mobile');
        // $userProfile->country_id = $request->session()->get('CountryId');
        // $userProfile->status = '1';
        // $userProfile->save();

        // Log in the newly registered user
        Auth::login($user);

        return response()->json(['message' => 'User registered and logged in successfully.'], 200);
    }

}
